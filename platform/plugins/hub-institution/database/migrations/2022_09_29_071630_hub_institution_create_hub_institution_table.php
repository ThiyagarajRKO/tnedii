<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class HubInstitutionCreateHubInstitutionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('hub_institutions')) {
            Schema::create('hub_institutions', function (Blueprint $table) {
                $table->id();
			$table->integer("hub_type_id");
			$table->string("hub_code","100");
			$table->string("name","255")->nullable();
			$table->string("address","255")->nullable();
			$table->string("phone_no","100")->nullable();
			$table->string("year_of_establishment","100")->nullable();
			$table->string("pincode","100")->nullable();
			$table->string("email","100")->nullable();
			$table->string("accreditations","100")->nullable();
			$table->string("city","100")->nullable();
			$table->string("website","255")->nullable();
			$table->integer("district")->nullable();
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
        Schema::dropIfExists('hub_institutions');
    }
}
