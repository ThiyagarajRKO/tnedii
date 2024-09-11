<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class WorkflowsCreateWorkflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   

        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->string('module_controller', 255);
            $table->text('email_subject')->nullable();
            $table->text('email_content')->nullable();
            $table->integer('is_enabled')->nullable()->default(1);
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });

        Schema::create('workflow_meta_data', function (Blueprint $table) {
            $table->id();
            $table->integer('workflow_id')->unsigned()->references('id')->on('workflows')->onDelete('cascade');
            $table->string('transition_name', 255);
            $table->text('users')->nullable();
            $table->text('meta_data')->nullable();
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workflows');
        Schema::dropIfExists('workflow_meta_data');
    }
}
