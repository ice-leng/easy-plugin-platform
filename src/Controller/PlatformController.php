<?php

namespace App\HttpController\Platform\V1\Platform;

use App\Constants\Type\System\LoginLogChannel;
use App\HttpController\Platform\V1\Controller;
use App\Service\Platform\PlatformService;
use App\Service\System\LoginLogService;
use EasySwoole\Component\Context\ContextManager;
use EasySwoole\HttpAnnotation\AnnotationTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup;
use EasySwoole\HttpAnnotation\AnnotationTag\Di;
use EasySwoole\HttpAnnotation\AnnotationTag\InjectParamsContext;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\HttpAnnotation\Swagger\Annotation\ApiSuccessTemplate;
use EasySwoole\Skeleton\Entities\PageEntity;
use EasySwoole\Skeleton\Framework\BizException;

/**
 * Class LoginController
 * @package App\HttpController\Platform\V1
 * @ApiGroup(groupName="系统设置/权限设置")
 */
class PlatformController extends Controller
{
    /**
     * @Di(key="PlatformService")
     * @var PlatformService
     */
    protected $platformService;

    /**
     * @Di(key="LoginLogService")
     * @var LoginLogService
     */
    protected $loginLogService;

    /**
     * @Api(name="修改密码", path="/platform/v1/platform/changePassword")
     * @Method(allow={POST})
     * @ApiSuccessTemplate(template="success")
     * @Param(name="password", alias="原密码", description="原密码",lengthMax="32",required="", notEmpty="")
     * @Param(name="new_password", differentWithColumn="password", alias="新密码", description="新密码",lengthMax="32",required="", notEmpty="")
     * @Param(name="confirm_password", alias="重复确认密码", equalWithColumn="new_password", description="重复确认密码",lengthMax="32",required="", notEmpty="")
     *
     * @InjectParamsContext(key="data")
     *
     * @return bool
     * @throws BizException
     */
    public function changePassword()
    {
        $params = ContextManager::getInstance()->get('data');
        $this->platformService->changePassword($this->getAccount(), $params['new_password'], $params['password']);
        return $this->success();
    }

    /**
     * @Api(name="管理员列表", path="/platform/v1/platform/list")
     * @Method(allow={POST})
     * @ApiSuccessTemplate(template="page", result={
     *       "platform_id|管理员id": "1",
     *       "account|管理员名称": "超级管理员",
     *       "active|启用状态": 1,
     *       "active_message|启用状态信息": "启用",
     *       "last_time|上次登录时间": "2021-03-10 16:17:59",
     *       "mobile|手机": "xxxxx",
     *       "email|邮箱": "xxxx",
     *     })
     * @Param(name="page", type="int", alias="页", description="页", defaultValue="1" ,notEmpty="")
     * @Param(name="pageSize", type="int", alias="每页", description="每页", defaultValue="10" ,notEmpty="")
     *
     * @InjectParamsContext(key="data")
     *
     * @return bool
     */
    public function list()
    {
        $params = ContextManager::getInstance()->get('data');
        $page = new PageEntity($params);
        $data = $this->platformService->getList($params, [
            'platform_id',
            'account',
            'platform_name',
            'mobile',
            'email',
            'active',
            'last_time',
        ], $page);
        return $this->success($data);
    }

    /**
     * @Api(name="添加管理员", path="/platform/v1/platform/create")
     * @Method(allow={POST})
     *
     * @Param(name="account", type="string", alias="管理员名称", description="管理员名称", lengthMax="32", required="", notEmpty="")
     * @Param(name="password", alias="设置密码", description="设置密码",lengthMax="32",required="", notEmpty="")
     * @Param(name="confirm_password", alias="密码确认", equalWithColumn="password", description="密码确认",lengthMax="32",required="", notEmpty="")
     * @Param(name="platform_name", alias="姓名", description="姓名", required="", notEmpty="")
     * @Param(name="mobile", alias="手机号", description="手机号", optional="", notEmpty="")
     * @Param(name="email", alias="邮箱", description="邮箱", optional="", notEmpty="")
     * @Param(name="active", type="int", alias="启用状态", description="启用状态, 1启用，2未启用", inArray={1, 2}, required="", notEmpty="", defaultValue=1)
     *
     * @ApiSuccessTemplate(template="success")
     * @InjectParamsContext(key="data")
     *
     * @return bool
     * @throws
     */
    public function create()
    {
        $params = ContextManager::getInstance()->get('data');
        $this->platformService->create($params);
        return $this->success();
    }

    /**
     * @Api(name="更新管理员", path="/platform/v1/platform/update")
     * @Method(allow={POST})
     *
     * @Param(name="platform_id", type="string", alias="管理员ID", description="管理员ID", lengthMax="32", required="", notEmpty="")
     * @Param(name="account", type="string", alias="管理员名称", description="管理员名称", lengthMax="32", required="", notEmpty="")
     * @Param(name="platform_name", alias="姓名", description="姓名", required="", notEmpty="")
     * @Param(name="mobile", alias="手机号", description="手机号", optional="", notEmpty="")
     * @Param(name="email", alias="邮箱", description="邮箱", optional="", notEmpty="")
     * @Param(name="active", type="int", alias="启用状态", description="启用状态, 1启用，2未启用", inArray={1, 2}, required="", notEmpty="", defaultValue=1)
     *
     * @ApiSuccessTemplate(template="success")
     * @InjectParamsContext(key="data")
     *
     * @return bool
     * @throws
     */
    public function update()
    {
        $params = ContextManager::getInstance()->get('data');
        $this->platformService->update($params);
        return $this->success();
    }

    /**
     * @Api(name="编辑管理员", path="/platform/v1/platform/edit")
     * @Method(allow={POST})
     *
     * @Param(name="platform_id", type="string", alias="管理员ID", description="管理员ID", lengthMax="32", required="", notEmpty="")
     * @ApiSuccessTemplate(template="success")
     * @InjectParamsContext(key="data")
     *
     * @return bool
     * @throws
     */
    public function edit()
    {
        $params = ContextManager::getInstance()->get('data');
        $data = $this->platformService->detail($params, [
            'platform_id',
            'account',
            'platform_name',
            'mobile',
            'email',
            'active',
            'last_time',
        ]);
        return $this->success($data);
    }

    /**
     * @Api(name="删除管理员", path="/platform/v1/platform/remove")
     * @Method(allow={POST})
     * @Param(name="platform_id", type="string", alias="管理员ID", description="管理员ID", lengthMax="32", required="", notEmpty="")
     * @ApiSuccessTemplate(template="success")
     *
     * @InjectParamsContext(key="data")
     *
     * @return bool
     * @throws
     */
    public function remove()
    {
        $params = ContextManager::getInstance()->get('data');
        $this->platformService->remove($params);
        return $this->success();
    }

    /**
     * @Api(name="重置密码", path="/platform/v1/platform/resetPassword")
     * @Method(allow={POST})
     *
     * @Param(name="platform_id", type="string", alias="管理员ID", description="管理员ID", lengthMax="32", required="", notEmpty="")
     * @Param(name="password", alias="密码", description="密码",lengthMax="32",required="", notEmpty="")
     * @ApiSuccessTemplate(template="success")
     * @InjectParamsContext(key="data")
     *
     * @return bool
     * @throws
     */
    public function resetPassword()
    {
        $params = ContextManager::getInstance()->get('data');
        $this->platformService->changePassword($this->getAccount(), $params['password']);
        return $this->success();
    }

    /**
     * @Api(name="登录日志", path="/platform/v1/platform/loginLog")
     * @Method(allow={POST})
     *
     * @Param(name="platform_id", type="string", alias="管理员ID", description="管理员ID", lengthMax="32", required="", notEmpty="")
     * @ApiSuccessTemplate(template="page", result={
     *          "ip": "xxx",
     *          "create_at|登录记录": "xxxxx"
     *     })
     * @InjectParamsContext(key="data")
     *
     * @return bool
     * @throws
     */
    public function loginLog()
    {
        $params = ContextManager::getInstance()->get('data');
        $page = new PageEntity($params);
        $params['channel'] = LoginLogChannel::PLATFORM;
        $params['relation_id'] = $this->getAccount()->platform_id;
        $data = $this->loginLogService->getList($params, [
            'ip',
            'create_at'
        ], $page);
        return $this->success($data);
    }
}
