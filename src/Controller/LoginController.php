<?php

namespace App\HttpController\Platform\V1;

use App\Constants\Errors\Platform\PlatformError;
use App\Service\Platform\PlatformService;
use EasySwoole\Component\Context\ContextManager;
use EasySwoole\HttpAnnotation\AnnotationTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\Di;
use EasySwoole\HttpAnnotation\AnnotationTag\InjectParamsContext;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\HttpAnnotation\Swagger\Annotation\ApiSuccessTemplate;
use EasySwoole\Skeleton\Framework\BaseController;
use EasySwoole\Skeleton\Framework\BizException;
use EasySwoole\Skeleton\Utility\VerifyCodeHash;
use EasySwoole\Utility\Random;
use EasySwoole\VerifyCode\VerifyCode;
use Throwable;

/**
 * Class LoginController
 * @package App\HttpController\Platform\V1
 * @ApiGroup(groupName="登录")
 * @ApiGroupDescription("管理端")
 */
class LoginController extends BaseController
{
    protected static $verifyCodeHash = 'platformVerifyCodeHash';
    protected static $verifyCodeTime = 'platformVerifyCodeTime';

    /**
     * @Di(key="PlatformService")
     * @var PlatformService
     */
    protected $loginService;

    /**
     * @Api(name="登录",path="/platform/v1/login")
     * @ApiDescription("管理员登录")
     * @Method(allow={POST})
     * @Param(name="account",alias="账号",description="账号",lengthMax="32",required="")
     * @Param(name="password",alias="密码",description="密码",lengthMax="32",required="")
     * @Param(name="verifyCode",alias="验证码",description="验证码",lengthMax="32",required="")
     * @Param(name="platformVerifyCodeHash", alias="验证码hash", description="验证码hash",required="")
     * @Param(name="platformVerifyCodeTime", alias="验证码时间", description="验证码时间",required="")
     * @ApiSuccessTemplate(template="success", result={
     *      "token|token" : "xxxxx",
     *      "refreshToken|刷新token" : "xxxxx",
     *      "platform_name|管理员名称" : "xxxxx",
     *     })
     * @InjectParamsContext(key="data")
     * @return bool|void
     * @throws BizException|Throwable
     */
    public function index()
    {
        $params = ContextManager::getInstance()->get('data');
        $check = VerifyCodeHash::checkVerifyCode($params['verifyCode'], $params['platformVerifyCodeTime'], $params['platformVerifyCodeHash']);
        if (!$check) {
            throw new BizException(PlatformError::LOGIN_VERIFY_CODE_ERROR);
        }
        $ip = $this->clientRealIP();
        $data = $this->loginService->login($params, $ip);
        return $this->success($data);
    }

    /**
     * @Api(name="验证码",path="/platform/v1/login/verifyCode")
     * @ApiDescription("验证码")
     * @ApiSuccessTemplate(template="success", result={
     *      "verifyCode|验证码": "xxxx"
     *     })
     * @Method(allow={POST})
     */
    public function verifyCode()
    {
        $code = new VerifyCode();
        //获取随机数
        $random = Random::character(4, '1234567890abcdefghijklmnopqrstuvwxyz');
        $code = $code->DrawCode($random);
        $time = time();
        $date = $time + VerifyCodeHash::DURATION;
        $result = [
            'verifyCode' => $code->getImageBase64(),
            'cookies'    => [
                [
                    'key'    => self::$verifyCodeHash,
                    'value'  => VerifyCodeHash::getVerifyCodeHash($random, $time),
                    'expire' => $date,
                ],
                [
                    'key'    => self::$verifyCodeTime,
                    'value'  => $time,
                    'expire' => $date,
                ],
            ],
        ];
        return $this->success($result);
    }
}
