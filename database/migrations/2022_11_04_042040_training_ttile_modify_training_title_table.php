<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TrainingTtileModifyTrainingTitleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_title', function (Blueprint $table) {
            
            if (!Schema::hasColumn('training_title', 'name')){
                $table->string("name","255")->nullable()->after('id');
            }
            if (!Schema::hasColumn('training_title', 'left_signature_file')){
                $table->string("left_signature_file","255")->nullable()->after('left_signature');
            }
            if (!Schema::hasColumn('training_title', 'middle_signature_file')){
                $table->string("middle_signature_file","255")->nullable()->after('middle_signature');
            }
            if (!Schema::hasColumn('training_title', 'right_signature_file')){
                $table->string("right_signature_file","255")->nullable()->after('right_signature');
            }
            if (!Schema::hasColumn('training_title', 'special_flag')){
                $table->tinyInteger("special_flag")->nullable()->after('webinar_link');
            }
            if (!Schema::hasColumn('training_title', 'status')){
                $table->tinyInteger("status")->nullable()->after('footer_note');
            }
            if (!Schema::hasColumn('training_title', 'day_count')){
                $table->integer("day_count")->nullable()->after('special_flag');
            }
            if (!Schema::hasColumn('training_title', 'created_by')){
                $table->integer("created_by")->nullable()->after('footer_note');
            }
            if (!Schema::hasColumn('training_title', 'updated_by')){
                $table->integer("updated_by")->nullable()->after('created_by');
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
