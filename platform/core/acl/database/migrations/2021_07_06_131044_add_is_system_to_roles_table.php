<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSystemToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'is_system')){
                $table->tinyinteger('is_system')->after('is_admin')->nullable()->default(0);
            }
            if (!Schema::hasColumn('roles', 'is_enabled')){
                $table->tinyinteger('is_enabled')->after('is_system')->nullable()->default(1);
            }
            if (!Schema::hasColumn('roles', 'deleted_at')){
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('is_system');
            $table->dropColumn('is_enabled');
        });
    }
}
