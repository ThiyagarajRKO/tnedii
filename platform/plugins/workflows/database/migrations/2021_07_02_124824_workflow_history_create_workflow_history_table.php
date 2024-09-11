<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class WorkflowHistoryCreateWorkflowHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        if(!Schema::hasTable('workflow_history')) {
        Schema::create('workflow_history', function (Blueprint $table) {
            $table->id();
            $table->text('comments');
            $table->integer('reference_id');
            $table->string('reference_type', 255);
            $table->string('transition_name', 255);
            $table->integer('created_by');
            $table->timestamps();
        });
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workflow_history');
    }
}
