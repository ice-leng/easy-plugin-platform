<?php

namespace App\HttpController\Platform\V1;

use App\Constants\Errors\Platform\PlatformError;
use App\Model\Platform;
use App\Service\Platform\PlatformService;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiDescription;
use EasySwoole\Skeleton\Constant\ActiveStatus;
use EasySwoole\Skeleton\Constant\SoftDeleted;
use EasySwoole\Skeleton\Framework\BaseController;
use EasySwoole\Skeleton\Framework\BizException;
use EasySwoole\Skeleton\Utility\Auth;
use Throwable;

class Controller extends BaseController
{
    /**
     * @ApiAuth(from={"HEADER"}, name="token", optional="", notEmpty="token不能为空", type="string")
     * @ApiDescription("令牌")
     *
     * @param string|null $action
     *
     * @return bool|null
     * @throws BizException
     * @throws Throwable
     */
    public function onRequest(?string $action): ?bool
    {
        $token = $this->request()->getHeaderLine('token');
        $jwt = new Auth();
        $data = $jwt->verifyToken($token);
        if (!empty($data['platform_id'])) {
            $this->request()->withAttribute('platform_id', $data['platform_id']);
        }
        $account = $this->getAccount();
        // 账号未启用
        if ((int)$account->active === ActiveStatus::DISABLE) {
            throw new BizException(PlatformError::DISABLE);
        }
        if ((int)$account->enable !== SoftDeleted::ENABLE) {
            throw new BizException(PlatformError::LOGIN_ACCOUNT_NOT_FOUND);
        }
        return parent::onRequest($action);
    }

    /**
     * @return Platform|null
     * @throws BizException
     */
    public function getAccount(): ?Platform
    {
        $platformId = $this->request()->getAttribute('platform_id');
        if (!$platformId) {
            return null;
        }
        $platform = $this->request()->getAttribute('platform');
        if (!$platform) {
            $platform = make(PlatformService::class)->findOne([
                'platform_id' => $platformId,
            ]);
            $this->request()->withAttribute('platform', $platform);
        }
        return $platform;
    }
}
