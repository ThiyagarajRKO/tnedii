<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class WorkflowsCreateTransitionConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        if(!Schema::hasTable('workflow_transition_config')) {
            Schema::create('workflow_transition_config', function (Blueprint $table) {
                $table->id();
                $table->integer('workflow_permission_id');
                $table->string('attachment_type', 100)->nullable();
                $table->text('attachment_content')->nullable();
                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists('workflow_transition_config');
    }
}
