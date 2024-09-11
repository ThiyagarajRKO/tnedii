<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleEntitiestoRolesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'entity_type')) {
                $table->integer('entity_type')->nullable();
            }
            if (!Schema::hasColumn('roles', 'entity_id')) {
                $table->integer('entity_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }

}
