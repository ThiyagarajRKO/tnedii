<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class SpokeRegistrationCreateSpokeEcellsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('spoke_ecells')) {
            Schema::create('spoke_ecells', function (Blueprint $table) {
                $table->id();
			$table->integer("spoke_registration_id");
			$table->string("name","191")->nullable();
			$table->string("start_year","191")->nullable();
			$table->string("end_year","191")->nullable();
			$table->text("logo")->nullable();
			$table->text("description")->nullable();
			$table->string("wf_status","191")->nullable();
			$table->integer("is_enabled")->nullable()->default('1');
			
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
        Schema::dropIfExists('spoke_ecells');
    }
}
