<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class TnsiStartupCreateTnsiStartupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('tnsi_startup')) {
            Schema::create('tnsi_startup', function (Blueprint $table) {
                $table->id();
			$table->string("college_name","191")->nullable();
			$table->text("team_members")->nullable();
			$table->string("name","191")->nullable();
			$table->string("idea_about","191")->nullable();
			$table->string("is_your_idea","191")->nullable();
			$table->string("about_startup","191")->nullable();
			$table->string("problem_of_address","191")->nullable();
			$table->string("solution_of_problem","191")->nullable();
			$table->string("unique_selling_proposition","191")->nullable();
			$table->string("revenue_stream","191")->nullable();
			$table->text("description")->nullable();
			$table->string("duration","191")->nullable();
			$table->string("is_won","191")->nullable();
			$table->string("pitch_training","191")->nullable();
			$table->string("is_incubated","191")->nullable();
                        $table->string("demo_link","191")->nullable();
			$table->string("about_tnsi","191")->nullable();
			$table->string("wf_status","191")->nullable()->default('registered');
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
        Schema::dropIfExists('tnsi_startup');
    }
}
