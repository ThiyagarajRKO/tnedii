<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TrainingTitleModifyTrainingTitleTable666JfrzsyPpF extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('training_title', function (Blueprint $table) {
                $table->bigInteger("private_workshop")->after('fee_paid');
			$table->timestamp("training_start_date")->after('private_workshop');
			$table->timestamp("training_end_date")->after('training_start_date');
			$table->string("webinar_link","255")->nullable()->after('training_end_date');
			$table->text("small_content")->nullable()->after('webinar_link');
			$table->text("description")->nullable()->after('small_content');
			$table->string("left_signature","55")->nullable()->after('description');
			$table->string("left_signature_name","55")->nullable()->after('left_signature');
			$table->string("middle_signature","55")->nullable()->after('left_signature_name');
			$table->string("middle_signature_name","55")->nullable()->after('middle_signature');
			$table->string("right_signature","55")->nullable()->after('middle_signature_name');
			$table->string("right_signature_name","55")->nullable()->after('right_signature');
			$table->string("attendance_percentage","55")->nullable()->after('right_signature_name');
			$table->string("footer_note","255")->nullable()->after('attendance_percentage');
			if (Schema::hasColumn('training_title', 'training_start_date_time')){

                        Schema::table('training_title', function (Blueprint $table) {
                            $table->dropColumn('training_start_date_time');
                        });
                    }if (Schema::hasColumn('training_title', 'training_end_date_time')){

                        Schema::table('training_title', function (Blueprint $table) {
                            $table->dropColumn('training_end_date_time');
                        });
                    }
            });
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
