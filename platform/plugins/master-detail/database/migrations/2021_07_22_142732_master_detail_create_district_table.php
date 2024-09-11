<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MasterDetailCreateDistrictTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('district')) {
            Schema::create('district', function (Blueprint $table) {
                $table->id();
			$table->string("name","100");
			$table->bigInteger("status");
			$table->integer("country_id")->nullable();
			$table->integer("created_by");
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
        Schema::dropIfExists('district');
    }
}
