<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CrudCreateCrudTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cruds', function (Blueprint $table) {
            $table->id();
			$table->string("module_name","100")->nullable();
			$table->string("module_title","100")->nullable();
			$table->string("module_note","255")->nullable();
			$table->string("module_author","100")->nullable();
			$table->string("module_created")->nullable();
			$table->text("module_desc")->nullable();
			$table->string("module_db","255")->nullable();
			$table->string("module_db_key","100")->nullable();
			$table->string("module_type")->nullable()->default('native');
			$table->longText("module_config")->nullable();
			$table->longText("module_queries")->nullable();
			$table->text("repeater_data")->nullable();
			$table->text("module_lang")->nullable();
			$table->integer("parent_id")->nullable()->default('0');
			$table->integer("is_entity")->nullable()->default('0');
			$table->integer("is_bulkupload")->nullable()->default('0');
			$table->integer("is_multi_lingual")->nullable()->default('0');
			$table->text("module_actions")->nullable();
			$table->string("module_before_insert","255")->nullable();
			$table->string("name","255")->nullable();
			$table->string("status","60")->default('published');
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
        Schema::dropIfExists('cruds');
    }
}
