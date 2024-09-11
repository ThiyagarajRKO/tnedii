<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UserCreateUserPermissionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function (Blueprint $table) {
                $table->id();
                $table->integer("user_id");
                $table->integer("reference_id");
                $table->integer("reference_key");
                $table->string("reference_type", "255");
                $table->text("role_id");
                $table->text("role_permissions");
                $table->integer("is_retired")->default(0);
                $table->date("retire_after_restore")->nullable();
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
        Schema::dropIfExists('user_permissions');
    }

}
