<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class IncubationCenterCreateIncubationCenterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('incubation_centers')) {
            Schema::create('incubation_centers', function (Blueprint $table) {
                $table->id();
			$table->integer("district_id")->nullable();
			$table->string("center_name","255")->nullable();
			$table->string("manager_name","255")->nullable();
			$table->date("establishment_date")->nullable();
			$table->integer("no_of_incubatees")->nullable();
			$table->integer("is_active")->nullable();
			$table->date("submit_date")->nullable();
			$table->integer("created_by")->nullable();
			$table->timestamp("created_date")->nullable();
			$table->integer("modified_by")->nullable();
			
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
        Schema::dropIfExists('incubation_centers');
    }
}
