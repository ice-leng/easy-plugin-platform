<?php

namespace App\Service\Platform;

use App\Constants\Errors\Platform\PlatformError;
use App\Constants\Type\System\LoginLogChannel;
use App\Constants\Type\System\LoginLogType;
use App\Model\Platform;
use App\Service\System\LoginLogService;
use EasySwoole\HyperfOrm\Db;
use EasySwoole\Skeleton\Constant\SoftDeleted;
use EasySwoole\Skeleton\Constant\ActiveStatus;
use EasySwoole\Skeleton\Entities\PageEntity;
use EasySwoole\Skeleton\Framework\BaseService;
use EasySwoole\Skeleton\Framework\BizException;
use EasySwoole\Skeleton\Helpers\Arrays\ArrayHelper;
use EasySwoole\Skeleton\Helpers\PasswordHelper;
use EasySwoole\Skeleton\Utility\Auth;
use EasySwoole\Utility\SnowFlake;
use Throwable;

class PlatformService extends BaseService
{
    /**
     * @var Auth
     */
    protected $auth;

    public function __construct()
    {
        $this->auth = new Auth();
    }

    /**
     * @param array $params
     * @param array $field
     *
     * @return Platform
     * @throws BizException
     */
    public function findOne(array $params, array $field = ['*']): Platform
    {
        $model = Platform::findOneCondition([
            'platform_id' => $params['platform_id'] ?? -1,
        ], $field);
        if (empty($model)) {
            throw new BizException(PlatformError::NOT_FOUND);
        }
        return $model;
    }

    /**
     * @param array $params
     * @param array $field
     *
     * @return Platform
     */
    public function findOneCondition(array $params, array $field = ['*']): Platform
    {
        return Platform::findOneCondition([
            'mobile' => $params['mobile'] ?? null,
            'email'  => $params['email'] ?? null,
        ], $field);
    }

    /**
     * @param array  $params
     * @param string $ip
     *
     * @return array
     * @throws BizException|Throwable
     */
    public function login(array $params, string $ip): array
    {
        $account = Platform::query()->select([
            'platform_id',
            'platform_name',
            'password',
            'active',
            'last_time',
            'account',
        ])->where([
            'account' => $params['account'],
            'enable'  => SoftDeleted::ENABLE,
        ])->first();

        if (!$account) {
            throw new BizException(PlatformError::LOGIN_ACCOUNT_OR_PASSWORD_ERROR);
        }

        $check = PasswordHelper::verifyPassword($params['password'], $account->password);
        if (!$check) {
            throw new BizException(PlatformError::LOGIN_ACCOUNT_OR_PASSWORD_ERROR);
        }

        if ((int)$account->active !== ActiveStatus::ENABLE) {
            throw new BizException(PlatformError::LOGIN_ACCOUNT_NOT_FOUND);
        }

        try {
            Db::beginTransaction();
            $status = $account->update([
                'last_time' => time(),
            ]);
            if (!$status) {
                throw new BizException(PlatformError::UPDATE_FAIL);
            }

            make(LoginLogService::class)->create([
                'login_log_id' => SnowFlake::make(1, 1),
                'ip'           => $ip,
                'channel'      => LoginLogChannel::PLATFORM,
                'type'         => LoginLogType::LOGIN,
                'duration'     => 0,
                'day'          => date('Y-m-d'),
                'relation_id'  => $account->platform_id,
            ]);

            $token = $this->auth->generate([
                'platform_id' => $account->platform_id,
            ]);
            Db::commit();
            return [
                'token'         => $token,
                'refreshToken'  => $this->auth->generateRefreshToken($token),
                'platform_name' => $account->platform_name,
            ];
        } catch (\Throwable $exception) {
            Db::rollBack();
            throw $exception;
        }
    }

    /**
     * 刷新token
     *
     * @param string $refreshToken
     * @param string $ip
     *
     * @return array
     * @throws BizException
     * @throws Throwable
     */
    public function refreshToken(string $refreshToken, string $ip): array
    {
        $token = $this->auth->refreshToken($refreshToken);
        return [
            'token'        => $token,
            'refreshToken' => $refreshToken,
        ];
    }

    /**
     * 退出
     *
     * @param string $token
     * @param string $ip
     *
     * @return bool
     * @throws BizException
     */
    public function logout(string $token, string $ip): bool
    {
        $data = $this->auth->verifyToken($token);
        make(LoginLogService::class)->create([
            'login_log_id' => SnowFlake::make(1, 1),
            'ip'           => $ip,
            'channel'      => LoginLogChannel::PLATFORM,
            'type'         => LoginLogType::LOGOUT,
            'duration'     => 0,
            'day'          => date('Y-m-d'),
            'relation_id'  => $data['platform_id'],
        ]);
        return $this->auth->logout($token);
    }

    /**
     * @param Platform    $platform
     * @param string      $changePassword
     * @param string|null $password
     *
     * @return bool
     * @throws BizException
     */
    public function changePassword(Platform $platform, string $changePassword, ?string $password = null): bool
    {
        if ($password) {
            $check = PasswordHelper::verifyPassword($password, $platform->password);
            if (!$check) {
                throw new BizException(PlatformError::PASSWORD_ERROR);
            }
        }

        $status = $platform->update([
            'password' => PasswordHelper::generatePassword($changePassword),
        ]);

        if (!$status) {
            throw new BizException(PlatformError::UPDATE_FAIL);
        }
        return $status;
    }

    /**
     * @param array           $params
     * @param array           $field
     * @param PageEntity|null $pageEntity
     *
     * @return array
     */
    public function getList(array $params = [], array $field = ['*'], ?PageEntity $pageEntity = null): array
    {
        $query = Platform::query();
        $query->select($field)->with('role')->where([
            'enable' => SoftDeleted::ENABLE,
        ]);
        if (ArrayHelper::isValidValue($params, 'active')) {
            $query->where([
                'active' => $params['active'],
            ]);
        }
        if (ArrayHelper::isValidValue($params, 'platform_name')) {
            $query->where('platform_name', 'like', "%{$params['platform_name']}%");
        }
        $this->orderBy($query);
        $results = $pageEntity ? $this->page($query, $pageEntity) : $query->get()->toArray();
        return $this->toArray($results, [$this, 'format']);
    }

    public function format(array $result): array
    {
        if (ArrayHelper::isValidValue($result, 'active')) {
            $result['active_message'] = ActiveStatus::byValue($result['active'])->getMessage();
        }

        if (ArrayHelper::isValidValue($result, 'last_time')) {
            $result['last_time'] = date('Y-m-d H:i:s', $result['last_time']);
        }

        if (ArrayHelper::keyExists($result, 'role')) {
            $result['role_name'] = $result['role']['name'] ?? '超级管理';
            unset($result['role']);
        }

        if (ArrayHelper::isValidValue($result, 'job_type')) {
            $result['job_type_message'] = PlatformJobType::byValue($result['job_type'])->getMessage();
        }

        if (ArrayHelper::keyExists($result, 'department')) {
            $result['department'] = $result['department'] ?? [];
        }

        if (ArrayHelper::keyExists($result, 'department2')) {
            $result['department2'] = $result['department2'] ?? [];
        }

        if (ArrayHelper::keyExists($result, 'department3')) {
            $result['department3'] = $result['department3'] ?? [];
        }

        return $result;
    }

    /**
     * @param array $params
     *
     * @throws BizException
     */
    protected function check(array $params): void
    {
        $check = Platform::existCondition([
            'account'     => $params['account'],
            'platform_id' => $params['platform_id'] ?? null,
        ]);
        if ($check) {
            throw new BizException(PlatformError::ACCOUNT_EXIST);
        }
    }

    /**
     * @param array $params
     *
     * @return array
     * @throws Throwable
     */
    public function create(array $params): array
    {
        $this->check($params);
        $model = new Platform();
        try {
            Db::beginTransaction();
            $status = $model->insert([
                'platform_id'   => SnowFlake::make(1, 1),
                'account'       => $params['account'],
                'password'      => PasswordHelper::generatePassword($params['password']),
                'active'        => $params['active'],
                'platform_name' => $params['platform_name'],
                'mobile'        => $params['mobile'] ?? null,
                'email'         => $params['email'] ?? null,
            ]);
            if (!$status) {
                throw new BizException(PlatformError::CREATE_FAIL);
            }
            Db::commit();
            return $model->toArray();
        } catch (Throwable $exception) {
            Db::rollBack();
            throw $exception;
        }
    }

    /**
     * @param array $params
     *
     * @return array
     * @throws BizException
     * @throws Throwable
     */
    public function update(array $params): array
    {
        $this->check($params);
        $model = $this->findOne($params);
        try {
            Db::beginTransaction();
            $status = $model->update([
                'account'       => $params['account'],
                'active'        => $params['active'],
                'platform_name' => $params['platform_name'],
                'mobile'        => $params['mobile'] ?? null,
                'email'         => $params['email'] ?? null,
            ]);
            if (!$status) {
                throw new BizException(PlatformError::UPDATE_FAIL);
            }
            Db::commit();
            return $model->toArray();
        } catch (Throwable $exception) {
            Db::rollBack();
            throw $exception;
        }
    }

    /**
     * @param array $params
     * @param array $field
     *
     * @return array
     * @throws BizException
     */
    public function detail(array $params, array $field = ['*']): array
    {
        $model = $this->findOne($params, $field);
        return $this->format($model->toArray());
    }

    /**
     * @param array $params
     *
     * @return int
     * @throws BizException
     */
    public function remove(array $params): int
    {
        $status = Platform::softDeleteCondition($params);
        if (!$status) {
            throw new BizException(PlatformError::REMOVE_FAIL);
        }
        return $status;
    }
}
