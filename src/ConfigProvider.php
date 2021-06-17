<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/6/17
 * Time:  5:23 下午
 */

namespace EasySwoole\Plugin\Platform;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [

            ],
            'publish' => [
                [
                    'id' => 'loginLogChannel',
                    'description' => 'The config for constant LoginLogChannel.',
                    'source' => __DIR__ . '/Constants/LoginLogChannel.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Constants/Type/System/LoginLogChannel.php',
                ],
                [
                    'id' => 'LoginLogType',
                    'description' => 'The config for constant LoginLogType.',
                    'source' => __DIR__ . '/Constants/LoginLogType.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Constants/Type/System/LoginLogType.php',
                ],
                [
                    'id' => 'LoginLogError',
                    'description' => 'The config for constant LoginLogError.',
                    'source' => __DIR__ . '/Constants/LoginLogError.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Constants/Errors/System/LoginLogError.php',
                ],
                [
                    'id' => 'PlatformError',
                    'description' => 'The config for constant PlatformError.',
                    'source' => __DIR__ . '/Constants/LoginLogError.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Constants/Errors/Platform/PlatformError.php',
                ],
                [
                    'id' => 'Controller',
                    'description' => 'The config for httpController controller.',
                    'source' => __DIR__ . '/Controller/Controller.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/HttpController/Platform/V1/Controller.php',
                ],
                [
                    'id' => 'LoginController',
                    'description' => 'The config for httpController LoginController.',
                    'source' => __DIR__ . '/Controller/LoginController.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/HttpController/Platform/V1/LoginController.php',
                ],
                [
                    'id' => 'PlatformController',
                    'description' => 'The config for httpController PlatformController.',
                    'source' => __DIR__ . '/Controller/PlatformController.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/HttpController/Platform/V1/Platform/PlatformController.php',
                ],
                [
                    'id' => 'migratePlatform',
                    'description' => 'The config for migrate Platform.',
                    'source' => __DIR__ . '/Migrations/2021_05_02_231918_platform.php',
                    'destination' => EASYSWOOLE_ROOT . '/migrations/platform.php',
                ],
                [
                    'id' => 'migratePlatformInit',
                    'description' => 'The config for migrate Platform Init.',
                    'source' => __DIR__ . '/Migrations/2021_05_02_231921_platform_init.php',
                    'destination' => EASYSWOOLE_ROOT . '/migrations/platform_init.php',
                ],
                [
                    'id' => 'LoginLogService',
                    'description' => 'The config for Service LoginLogService.',
                    'source' => __DIR__ . '/Service/LoginLogService.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Service/System/LoginLogService.php',
                ],
                [
                    'id' => 'PlatformService',
                    'description' => 'The config for Service PlatformService.',
                    'source' => __DIR__ . '/Service/PlatformService.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Service/Platform/PlatformService.php',
                ],
            ],
        ];
    }
}
