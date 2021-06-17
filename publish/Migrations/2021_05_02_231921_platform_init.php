<?php

use EasySwoole\Utility\SnowFlake;
use Hyperf\Database\Migrations\Migration;

class PlatformInit extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $time = time();
        \EasySwoole\HyperfOrm\Db::table('platform')->insert([
            [
                'platform_id'  => SnowFlake::make(1, 1),
                'platform_name' => '超级管理员',
                'account'       => 'root',
                'password'      => '$2y$12$3Ulm.8owrYUHoW8gFh2DIeHWucFivOJMarlBp28iXFtTO.a.2EDwO',
                'mobile'        => '18888888888',
                'email'         => 'admin@admin.com',
                'create_at'   => $time,
                'update_at'   => $time,
                'enable'      => \EasySwoole\Skeleton\Constant\SoftDeleted::ENABLE,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \EasySwoole\HyperfOrm\Db::table('platform')->truncate();
    }
}
