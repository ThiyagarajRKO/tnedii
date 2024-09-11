<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class TrainingTitleFinancialDetailCreateTrainingTitleFinancialDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('training_title_financial_details')) {
            Schema::create('training_title_financial_details', function (Blueprint $table) {
                $table->id();
			$table->integer("division_id")->nullable();
			$table->integer("annual_action_plan_id")->nullable();
			$table->integer("training_title_id")->nullable();
			$table->integer("financial_year_id")->nullable();
			$table->double("budget_approved","13","2")->nullable();
			$table->double("actual_expenditure","13","2")->nullable();
			$table->double("edi_admin_cost","13","2")->nullable();
			$table->double("revenue_generated","13","2")->nullable();
			$table->string("neft_cheque_no","100")->nullable();
			$table->date("neft_cheque_date")->nullable();
			$table->text("remarks")->nullable();
			$table->date("submit_date")->nullable();
			$table->integer("created_by")->nullable();
			$table->integer("updated_by")->nullable();
			
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
        Schema::dropIfExists('training_title_financial_details');
    }
}
