<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UserCreateDeployedUserTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('deployed_users')) {
            Schema::create('deployed_users', function (Blueprint $table) {
                $table->id();
                $table->integer("imp_user_id")->nullable();
                $table->integer("reference_id")->nullable();
                $table->integer("reference_key")->nullable();
                $table->string("reference_type", "255")->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('deployed_users');
    }

}
