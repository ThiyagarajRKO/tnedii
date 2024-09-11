<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PasswordCriteriaCreatePasswordCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_criterias', function (Blueprint $table) {
            $table->id();
            $table->integer('min_length');
            $table->integer('max_length');
            $table->integer('has_alphabet');
            $table->integer('alphabet_count')->nullable(0);
            $table->string('alphabet_type', 10)->nullable();
            $table->integer('has_number')->nullable()->default(0);
            $table->integer('number_min_count')->nullable()->default(0);
            $table->integer('has_special_char')->nullable()->default(0);
            $table->integer('special_char_count')->nullable()->default(0);
            $table->string('allowed_spec_char', 64)->nullable();
            $table->integer('has_pwd_expiry')->nullable()->default(0);
            $table->integer('validity_period')->nullable()->default(0);
            $table->integer('reuse_pwd')->nullable()->default(0);
            $table->integer('reuse_after_x_times')->nullable()->default(0);
            $table->tinyInteger('auto_lock')->nullable()->default(0);
            $table->integer('invalid_attempt_allowed_time')->nullable()->default(0);
            $table->tinyInteger('auto_unlock')->nullable();
            $table->string('unlock_format', 50)->nullable();
            $table->string('unlock_time', 50)->nullable();
            $table->tinyInteger('auto_logout')->nullable();
            $table->string('logout_format', 50)->nullable();
            $table->string('logout_time', 50)->nullable();
            $table->timestamps();
        });
        Schema::create('password_history', function (Blueprint $table) {
            $table->id();
            $table->string('password', 255)->nullable();
            $table->integer('user_id')->unsigned()->references('id')->on('users')->index();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('password_criterias');
        Schema::dropIfExists('password_history');
    }
}
