<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UserCreateExperienceInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('experience_info')) {
            Schema::create('experience_info', function (Blueprint $table) {
                $table->id();
			$table->integer("imp_user_id");
			$table->date("prev_org_date_of_appointment")->nullable();
			$table->string("cur_employer","255")->nullable();
			$table->string("registration_no","60")->nullable();
			$table->date("cur_org_date_of_appointment")->nullable();
			$table->integer("employment_status")->nullable();
			$table->string("designation","255")->nullable();
			$table->string("prev_employer","255")->nullable();
			$table->string("salary_scale","20")->nullable();
			
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
        Schema::dropIfExists('experience_info');
    }
}
