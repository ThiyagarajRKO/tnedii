<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UsergroupsCreateUsergroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usergroups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('description', 255)->nullable();
            $table->text('roles')->nullable();
            $table->timestamps();
        });
        Schema::create('usergroup_entity', function (Blueprint $table) {
            $table->id();
            $table->integer('crud_id')->unsigned()->nullable()->references('id')->on('cruds')->onDelete('cascade');
            $table->integer('usergroup_id')->unsigned()->nullable()->references('id')->on('usergroups')->onDelete('cascade');
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
        Schema::dropIfExists('usergroups');
    }
}
