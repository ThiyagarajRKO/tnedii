<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WorkflowsModifyWorkflowTransitionTable9OeUIlzqys90Copy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workflow_transitions', function (Blueprint $table) {
            if(!Schema::hasColumn('workflow_transitions','action')){
                $table->string('action', 120)->nullable()->after('action_complete_label');
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
        Schema::dropIfExists('workflow_transitions');
    }
}
