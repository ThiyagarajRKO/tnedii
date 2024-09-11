<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AnnualActionPlanCreateAnnualActionPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('annual_action_plan')) {
            Schema::create('annual_action_plan', function (Blueprint $table) {
                $table->id();
			$table->string("name","255");
			$table->integer("financial_year_id");
			$table->integer("division_id");
			$table->integer("officer_incharge_id");
			$table->string("duration","20");
			$table->integer("no_of_batches");
			$table->integer("batch_size");
			$table->integer("budget_per_program");
			$table->integer("total_budget")->nullable();
			$table->string("online_training","20")->nullable();
			$table->text("remarks")->nullable();
			
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
        Schema::dropIfExists('annual_action_plan');
    }
}
