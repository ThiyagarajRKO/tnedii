<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AttendanceCreateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('attendance')) {
            Schema::create('attendance', function (Blueprint $table) {
                $table->id();
			$table->integer("financial_year_id")->nullable();
			$table->integer("annual_action_plan_id")->nullable();
			$table->integer("training_title_id")->nullable();
			$table->integer("entrepreneur_id")->nullable();
			$table->date("attendance_date")->nullable();
			$table->integer("present")->nullable();
			$table->integer("absent")->nullable();
			
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
        Schema::dropIfExists('attendance');
    }
}
