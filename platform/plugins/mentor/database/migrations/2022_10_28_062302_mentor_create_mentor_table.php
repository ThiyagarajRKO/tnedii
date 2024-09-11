<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MentorCreateMentorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('mentors')) {
            Schema::create('mentors', function (Blueprint $table) {
                $table->id();
			$table->integer("entrepreneur_id");
			$table->integer("industry_id");
			$table->integer("specialization_id");
			$table->integer("experience_id");
			$table->integer("last_use_id");
			$table->integer("proficiency_level_id");
			$table->integer("qualification_id");
			$table->text("achivements");
			$table->string("resume","255");
			$table->integer("status_id");
			
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
        Schema::dropIfExists('mentors');
    }
}
