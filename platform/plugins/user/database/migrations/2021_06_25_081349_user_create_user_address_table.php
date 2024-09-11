<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UserCreateUserAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('user_address')) {
            Schema::create('user_address', function (Blueprint $table) {
            $table->id();
			$table->integer("imp_user_id");
			$table->string("present_add_1","255")->nullable();
			$table->string("present_add_2","255")->nullable();
			$table->integer("present_country")->nullable();
			$table->string("present_district");
			$table->integer("present_county");
			$table->integer("present_sub_county");
			$table->integer("present_parish");
			$table->integer("present_village");
			$table->string("present_phonecode")->nullable();;
			$table->string("present_phone");
			$table->string("present_zipcode","15")->nullable();
            $table->string("present_coordinates","64")->nullable();
			$table->integer("same_as_present")->default('0');
			$table->string("permanent_add_1","255")->nullable();
			$table->string("permanent_add_2","255")->nullable();
			$table->integer("permanent_country")->nullable();
			$table->string("permanent_district")->nullable();
			$table->integer("permanent_county")->nullable();
			$table->integer("permanent_sub_county")->nullable();
			$table->integer("permanent_parish")->nullable();
			$table->integer("permanent_village")->nullable();
			$table->string("permanent_phonecode")->nullable();
			$table->string("permanent_phone")->nullable();
			$table->string("permanent_zipcode","15")->nullable();
            $table->string("permanent_coordinates","64")->nullable();
			
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
        Schema::dropIfExists('user_address');
    }
}
