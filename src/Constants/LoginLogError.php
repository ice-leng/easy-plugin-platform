<?php

namespace App\Constants\Errors\System;

use EasySwoole\Skeleton\Framework\BaseEnum;

class LoginLogError extends BaseEnum
{
    /**
     * @Message("登录日志不存在")
     */
    const NOT_FOUND = 'B-001-001-001';

    /**
     * @Message("登录日志创建失败")
     */
    const CREATE_FAIL = 'B-001-001-002';

}
