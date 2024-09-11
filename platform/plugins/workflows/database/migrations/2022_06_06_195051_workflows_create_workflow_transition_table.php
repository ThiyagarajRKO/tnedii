<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class WorkflowsCreateWorkflowTransitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();
            $table->integer('workflow_id')->unsigned()->references('id')->on('workflows')->index();
            $table->string('slug', 60)->nullable();
            $table->string('from_state', 64)->nullable();
            $table->string('to_state', 64)->nullable();
            $table->string('action_complete_label', 120)->nullable();
            $table->string('icon', 120)->nullable();
            $table->string('color', 120)->nullable();
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
        Schema::dropIfExists('workflow_transitions');
    }
}
