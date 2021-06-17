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
        \EasySwoole\HyperfOrm\Db::table('platform')->insert([
            [
                'platform_id '  => SnowFlake::make(1, 1),
                'account'       => 'root',
                'password'      => '$2y$12$3Ulm.8owrYUHoW8gFh2DIeHWucFivOJMarlBp28iXFtTO.a.2EDwO',
                'platform_name' => '超级管理员',
                'mobile'        => '18888888888',
                'email'         => 'admin@admin.com',
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
