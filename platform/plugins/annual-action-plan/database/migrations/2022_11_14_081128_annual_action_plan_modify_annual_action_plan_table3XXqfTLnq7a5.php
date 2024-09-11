<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AnnualActionPlanModifyAnnualActionPlanTable3XXqfTLnq7a5 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('annual_action_plan', function (Blueprint $table) {
                

                if (!Schema::hasColumn('annual_action_plan', 'officer_incharge_designation_id')){
                    $table->integer("officer_incharge_designation_id")->after('division_id');
                }
                if (!Schema::hasColumn('annual_action_plan', 'training_module')){
                    $table->string("training_module","255")->nullable()->after('total_budget');
                }
			
                if (!Schema::hasColumn('annual_action_plan', 'submit_date')){
                    $table->date("submit_date")->after('remarks');
                }
                if (!Schema::hasColumn('annual_action_plan', 'created_by')){
                    $table->integer("created_by")->after('submit_date');
                }
                
                if (!Schema::hasColumn('annual_action_plan', 'modified_by')){
                    $table->integer("modified_by")->nullable()->after('created_by');
                }
			
			
			if (Schema::hasColumn('annual_action_plan', 'officer_incharge_id')){

                        Schema::table('annual_action_plan', function (Blueprint $table) {
                            $table->dropColumn('officer_incharge_id');
                        });
                    }if (Schema::hasColumn('annual_action_plan', 'online_training')){

                        Schema::table('annual_action_plan', function (Blueprint $table) {
                            $table->dropColumn('online_training');
                        });
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
        Schema::dropIfExists('annual_action_plan');
    }
}
