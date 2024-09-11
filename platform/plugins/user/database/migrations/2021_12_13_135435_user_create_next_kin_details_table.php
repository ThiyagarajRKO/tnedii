<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UserCreateNextKinDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('next_kin_details')) {
            Schema::create('next_kin_details', function (Blueprint $table) {
                $table->id();
			$table->integer("imp_user_id")->nullable();
			$table->string("first_name","100")->nullable();
			$table->string("last_name","100")->nullable();
			$table->date("dob")->nullable();
            $table->integer("country_id")->nullable();
			$table->integer("phonecode")->nullable();
			$table->string("contact_number","100")->nullable();
			$table->string("email","191")->nullable();
			$table->string("address_1","255")->nullable();
			$table->string("address_2","255")->nullable();
			$table->integer("country")->nullable();
			$table->integer("district")->nullable();
			$table->integer("county")->nullable();
			$table->integer("sub_county")->nullable();
			$table->integer("parish")->nullable();
			$table->integer("village")->nullable();
			$table->string("postal_code","20")->nullable();
                        $table->string("coordinates","64")->nullable();
			
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
        Schema::dropIfExists('next_kin_details');
    }
}
