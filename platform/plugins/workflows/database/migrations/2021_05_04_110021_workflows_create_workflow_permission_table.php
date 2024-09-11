<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class WorkflowsCreateWorkflowPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('workflows_id')->unsigned()->references('id')->on('workflows')->index();
            $table->integer('reference_id');
            $table->string('reference_type', 150);
            $table->text('transition');
            $table->text('role_permissions')->nullable();
            $table->timestamps();
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
