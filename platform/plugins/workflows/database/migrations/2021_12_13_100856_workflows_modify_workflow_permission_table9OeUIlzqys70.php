<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WorkflowsModifyWorkflowPermissionTable9OeUIlzqys70 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workflow_permissions', function (Blueprint $table) {
            $table->increments('id')->change();
            $table->text("user_permissions")->nullable();
            $table->dropColumn("role_permissions");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workflow_permissions');
    }
}
