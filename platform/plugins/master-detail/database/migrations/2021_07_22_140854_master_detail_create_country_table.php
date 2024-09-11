<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MasterDetailCreateCountryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
			$table->string("nationality","191")->nullable();
			$table->string("country_code","10")->nullable();
			$table->string("country_name","255");
			$table->string("phone_code","6")->nullable();
			$table->bigInteger("status")->default('1');
			$table->integer("created_by");
			$table->string("languages","255")->nullable();
			$table->integer("is_enabled")->default('1');
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
        Schema::dropIfExists('countries');
    }
}
