<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/6/4
 * Time:  11:45 下午
 */

namespace App\Constants\Type\System;

use EasySwoole\Skeleton\Framework\BaseEnum;

class LoginLogChannel extends BaseEnum
{
    /**
     * @Message("管理台")
     */
    const PLATFORM = 1;

    /**
     * @Message("客户端")
     */
    const CLIENT = 2;
}
