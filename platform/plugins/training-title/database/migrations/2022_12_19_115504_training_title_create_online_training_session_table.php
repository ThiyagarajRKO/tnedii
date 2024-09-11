<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class TrainingTitleCreateOnlineTrainingSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('online_training_sessions')) {
            Schema::create('online_training_sessions', function (Blueprint $table) {
                $table->id();
			$table->integer("header");
			$table->string("title","255")->nullable();
			$table->string("sub_title","150")->nullable();
			$table->string("url","255");
			$table->integer("type")->nullable();
			
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
        Schema::dropIfExists('online_training_sessions');
    }
}
