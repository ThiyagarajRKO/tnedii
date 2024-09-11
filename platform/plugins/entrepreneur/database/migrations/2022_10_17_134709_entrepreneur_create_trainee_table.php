<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class EntrepreneurCreateTraineeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('trainees')) {
            Schema::create('trainees', function (Blueprint $table) {
                $table->id();
			$table->integer("entrepreneur_id");
			$table->integer("division_id")->nullable();
			$table->integer("financial_year_id")->nullable();
			$table->integer("annual_action_plan_id")->nullable();
			$table->integer("training_title_id");
			$table->integer("certificate_status")->default('0');
			
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
        Schema::dropIfExists('trainees');
    }
}
