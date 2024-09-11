<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EntrepreneurTableModificationAddedScheme extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entrepreneurs', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'scheme')){
                $table->text("scheme")->nullable()->after('student_type_id');
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
        //entrepreneurs
        Schema::dropIfExists('entrepreneurs');
    }
}
