<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class Platform extends Migration
{
    protected function pk(Blueprint $table, $pk)
    {
        $table->bigIncrements($pk);
        $table->unsignedInteger('create_at');
        $table->unsignedInteger('update_at');
        $table->unsignedTinyInteger('enable')->default(1);
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('platform', function (Blueprint $table) {
            $this->pk($table, 'platform_id');
            $table->string('platform_name', 32)->comment('姓名');
            $table->string('account', 32)->comment('账号');
            $table->char('password', 64)->comment('密码');
            $table->string('mobile', 16)->nullable()->comment('手机号');
            $table->string('email', 255)->nullable()->comment('邮箱');
            $table->integer('last_time')->nullable()->comment('上一次登录时间');
            $table->unsignedTinyInteger('active')->default(1)->comment('启用状态，1启用，2未启用');
        });

        // 登录日志
        Schema::create('login_log', function (Blueprint $table) {
            $this->pk($table, 'login_log_id');
            $table->string('ip', 16)->comment('ip');
            $table->unsignedTinyInteger('channel')->default(1)->comment("渠道，1前台，2后台");
            $table->unsignedTinyInteger('type')->default(1)->comment("类型，1登录，2退出，3在线时长");
            $table->unsignedSmallInteger('duration')->default(0)->comment('在线时长');
            $table->string('day', 16)->comment('日期');
            $table->unsignedBigInteger('relation_id')->default(0)->comment('关联id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform');
        Schema::dropIfExists('login_log');
    }
}
