<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AttendanceCreateAttendanceRemarkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('attendance_remarks')) {
            Schema::create('attendance_remarks', function (Blueprint $table) {
                $table->id();
			$table->integer("training_title_id");
			$table->integer("entrepreneur_id");
			$table->text("remark");
			$table->integer("created_by");
			
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
        Schema::dropIfExists('attendance_remarks');
    }
}
