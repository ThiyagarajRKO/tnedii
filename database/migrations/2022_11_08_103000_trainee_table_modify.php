<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TraineeTableModify extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainees', function (Blueprint $table) {
            if (!Schema::hasColumn('trainees', 'file_path')){
                $table->string("file_path","255")->nullable()->after('certificate_status');
            }
            if (!Schema::hasColumn('trainees', 'file_name')){
                $table->string("file_name","255")->nullable()->after('certificate_status');
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
        Schema::dropIfExists('trainees');
    }
}
