<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AttendanceModifyAttendanceTable2UtWPyrcAbEx extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('attendance', function (Blueprint $table) {
                $table->integer("entrepreneur_id")->after('training_title_id');
			    // $table->bigInteger("id")->nullable()->change();
			if (Schema::hasColumn('attendance', 'entrepreneur_id')){
                        Schema::table('attendance', function (Blueprint $table) {
                            $table->dropColumn('entrepreneur_id');
                        });
                    }if (Schema::hasColumn('attendance', 'entrepreneur_id')){

                        Schema::table('attendance', function (Blueprint $table) {
                            $table->dropColumn('entrepreneur_id');
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
        Schema::dropIfExists('attendance');
    }
}
