<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class SpokeRegistrationCreateSpokeRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('spoke_registration')) {
            Schema::create('spoke_registration', function (Blueprint $table) {
                $table->id();
			$table->string("name_of_institution","255");
			$table->integer("stream_of_institution");
			$table->integer("category");
			$table->integer("affiliation");
			$table->integer("hub_id")->nullable();
			$table->string("year_of_establishment","50")->nullable();
			$table->integer("locality_type")->nullable();
			$table->integer("institute_state")->nullable();
			$table->integer("program_level")->nullable();
			$table->integer("has_incubator")->nullable();
			$table->text("address")->nullable();
			$table->string("pin_code","255")->nullable();
			$table->string("city","100")->nullable();
			$table->integer("district_id")->nullable();
			$table->string("phone_no","50")->nullable();
			$table->string("email","255")->nullable();
			$table->string("location_of_e_cell","255")->nullable();
			$table->string("availability_space","100")->nullable();
			$table->string("internet","25")->nullable();
			$table->string("telephone","25")->nullable();
			$table->string("budget","100")->nullable();
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
        Schema::dropIfExists('spoke_registration');
    }
}
