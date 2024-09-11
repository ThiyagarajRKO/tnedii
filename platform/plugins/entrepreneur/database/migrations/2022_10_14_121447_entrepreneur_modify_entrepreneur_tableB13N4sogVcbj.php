<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EntrepreneurModifyEntrepreneurTableB13N4sogVcbj extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('entrepreneurs', function (Blueprint $table) {
                $table->integer("user_id")->after('id');
			$table->integer("spoke_id")->nullable()->after('hub_institution_id');
			
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
