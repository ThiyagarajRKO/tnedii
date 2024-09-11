<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MenteeCreateMenteeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('mentees')) {
            Schema::create('mentees', function (Blueprint $table) {
                $table->id();
			$table->integer("entrepreneur_id");
			$table->integer("mentor_id");
			$table->integer("industry_id");
			$table->integer("specialization_id");
			$table->integer("experience_id")->nullable();
			$table->integer("last_use_id")->nullable();
			$table->integer("proficiency_level_id")->nullable();
			$table->integer("qualification_id")->nullable();
			$table->text("description")->nullable();
			$table->string("resume","255")->nullable();
			$table->integer("status_id");
			$table->timestamp("updated")->nullable();
			
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
        Schema::dropIfExists('mentees');
    }
}
