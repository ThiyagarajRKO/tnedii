<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class TrainingTitleCreateTrainingTitleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('training_title')) {
            Schema::create('training_title', function (Blueprint $table) {
                $table->id();
			$table->integer("division_id");
			$table->integer("financial_year_id");
			$table->integer("annual_action_plan_id");
			$table->string("code","255");
			$table->string("venue","255");
			$table->string("email","55")->nullable();
			$table->string("phone","20");
			$table->integer("vendor_id");
			$table->integer("officer_incharge_designation_id");
			$table->string("fee_paid","20");
			$table->bigInteger("private_workshop")->default('0');
			$table->timestamp("training_start_date");
			$table->timestamp("training_end_date");
			$table->string("webinar_link","255")->nullable();
			$table->text("small_content")->nullable();
			$table->text("description")->nullable();
			$table->string("left_signature","55")->nullable();
			$table->string("left_signature_name","55")->nullable();
			$table->string("middle_signature","55")->nullable();
			$table->string("middle_signature_name","55")->nullable();
			$table->string("right_signature","55")->nullable();
			$table->string("right_signature_name","55")->nullable();
			$table->string("attendance_percentage","55")->nullable();
			$table->string("footer_note","255")->nullable();
			
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
        Schema::dropIfExists('training_title');
    }
}
