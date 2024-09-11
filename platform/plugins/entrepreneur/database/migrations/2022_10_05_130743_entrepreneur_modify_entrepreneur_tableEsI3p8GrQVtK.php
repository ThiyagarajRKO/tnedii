<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EntrepreneurModifyEntrepreneurTableEsI3p8GrQVtK extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('entrepreneurs', function (Blueprint $table) {
            	if (!Schema::hasColumn('entrepreneurs', 'community')){
                        $table->string("community","255")->nullable()->after('care_of');
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
        Schema::dropIfExists('entrepreneurs');
    }
}
