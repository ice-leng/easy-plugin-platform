<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/6/3
 * Time:  10:58 下午
 */

namespace App\Service\System;

use App\Constants\Errors\System\LoginLogError;
use App\Constants\Type\System\LoginLogType;
use App\Model\LoginLog;
use EasySwoole\Skeleton\Constant\SoftDeleted;
use EasySwoole\Skeleton\Entities\PageEntity;
use EasySwoole\Skeleton\Framework\BaseService;
use EasySwoole\Skeleton\Framework\BizException;
use EasySwoole\Skeleton\Helpers\Arrays\ArrayHelper;

class LoginLogService extends BaseService
{
    /**
     * @param array           $params
     * @param array|string[]  $field
     * @param PageEntity|null $pageEntity
     *
     * @return array
     */
    public function getList(array $params = [], array $field = ['*'], ?PageEntity $pageEntity = null): array
    {
        $query = LoginLog::query()->select($field)->where([
            'enable' => SoftDeleted::ENABLE,
        ]);

        if (ArrayHelper::isValidValue($params, 'relation_id')) {
            $query->where(['relation_id' => $params['relation_id']]);
        }

        if (ArrayHelper::isValidValue($params, 'channel')) {
            $query->where(['channel' => $params['channel']]);
        }

        if (ArrayHelper::isValidValue($params, 'type')) {
            $query->where(['type' => $params['type']]);
        }

        if (ArrayHelper::isValidValue($params, 'groupBy')) {
            $query->groupBy($params['groupBy']);
            foreach ($params['groupBy'] as $groupBy) {
                $query->orderByDesc($groupBy);
            }
        } else {
            $this->orderBy($query);
        }

        $results = $pageEntity ? $this->page($query, $pageEntity) : $query->get()->toArray();
        return $this->toArray($results, function ($result) {
            if (ArrayHelper::isValidValue($result, 'type')) {
                $result['type_message'] = LoginLogType::byValue($result['type'])->getMessage();
            }
            return $result;
        });
    }

    /**
     * @param array $params
     *
     * @return array
     * @throws BizException
     */
    public function create(array $params): array
    {
        $model = new LoginLog();
        $status = $model->insert($params);
        if (!$status) {
            throw new BizException(LoginLogError::CREATE_FAIL);
        }
        return $model->toArray();
    }

}
