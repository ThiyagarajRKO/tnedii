<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UserCreateEducationInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('education_info')) {
            Schema::create('education_info', function (Blueprint $table) {
                $table->id();
			$table->integer("imp_user_id");
			$table->integer("level_of_education")->nullable();
			$table->string("year_of_graduation","20")->nullable();
			$table->integer("specialization")->nullable();
			$table->string("location","255")->nullable();
			$table->integer("university")->nullable();
			
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
        Schema::dropIfExists('education_info');
    }
}
