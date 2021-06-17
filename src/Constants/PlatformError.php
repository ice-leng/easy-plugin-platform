<?php

namespace App\Constants\Errors\Platform;

use EasySwoole\Skeleton\Framework\BaseEnum;

class PlatformError extends BaseEnum
{
    /**
     * @Message("管理员不存在")
     */
    const NOT_FOUND = 'B-002-001-001';

    /**
     * @Message("管理员创建失败")
     */
    const CREATE_FAIL = 'B-002-001-002';

    /**
     * @Message("管理员更新失败")
     */
    const UPDATE_FAIL = 'B-002-001-003';

    /**
     * @Message("管理员删除失败")
     */
    const REMOVE_FAIL = 'B-002-001-004';

    /**
     * @Message("管理员未启用, 请联系管理员")
     */
    const DISABLE = 'B-002-001-005';

    /**
     * @Message("验证码错误")
     */
    const LOGIN_VERIFY_CODE_ERROR = 'B-002-001-006';

    /**
     * @Message("用户名或密码错误")
     */
    const LOGIN_ACCOUNT_OR_PASSWORD_ERROR = 'B-002-001-007';

    /**
     * @Message("账号不存在，请联系管理员")
     */
    const LOGIN_ACCOUNT_NOT_FOUND = 'B-002-001-008';

    /**
     * @Message("登录日志创建失败")
     */
    const LOG_CREATE_FAIL = 'B-002-001-009';

    /**
     * @Message("原密码错误")
     */
    const PASSWORD_ERROR = 'B-002-001-010';

    /**
     * @Message("赋值权限错误")
     */
    const ASSIGN_ERROR = 'B-002-001-011';

    /**
     * @Message("管理员账号已存在")
     */
    const ACCOUNT_EXIST = 'B-002-001-012';
}
