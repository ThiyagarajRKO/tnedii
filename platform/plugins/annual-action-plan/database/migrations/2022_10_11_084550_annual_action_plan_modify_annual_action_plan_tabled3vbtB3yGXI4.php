<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AnnualActionPlanModifyAnnualActionPlanTabled3vbtB3yGXI4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('annual_action_plan', function (Blueprint $table) {
                if (!Schema::hasColumn('annual_action_plan', 'financial_year_id')){
                    $table->integer("financial_year_id")->after('name');
                }
                if (!Schema::hasColumn('annual_action_plan', 'officer_incharge_id')){
                    $table->integer("officer_incharge_id")->after('division_id');
                }
                if (!Schema::hasColumn('annual_action_plan', 'batch_size')){
                    $table->integer("batch_size")->after('no_of_batches');
                }
                if (!Schema::hasColumn('annual_action_plan', 'online_training')){
                    $table->string("online_training","20")->nullable()->after('total_budget');
                }
                if (!Schema::hasColumn('annual_action_plan', 'remarks')){
                    $table->text("remarks")->nullable()->after('online_training');
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
