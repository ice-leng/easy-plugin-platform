<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/6/3
 * Time:  11:20 下午
 */

namespace App\Constants\Type\System;

use EasySwoole\Skeleton\Framework\BaseEnum;

class LoginLogType extends BaseEnum
{
    //类型，1登录，2退出, 3在线时长，

    /**
     * @Message("登录")
     */
    const LOGIN = 1;

    /**
     * @Message("退出")
     */
    const LOGOUT = 2;

    /**
     * @Message("在线时长")
     */
    const DURATION = 3;
}
