<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class BackendMenuCreateBackendMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('backend_menus')) {
            Schema::create('backend_menus', function (Blueprint $table) {
                $table->id();
			$table->string("menu_id","255");
			$table->string("parent_id","255")->nullable();
			$table->string("name","500")->nullable();
			$table->string("url","120")->nullable();
			$table->string("icon","50")->nullable();
			$table->string("priority")->default('0');
			$table->text("permissions")->nullable();
			$table->string("target","20")->default('_self');
			$table->integer("active")->default('0');
			
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
        Schema::dropIfExists('backend_menus');
    }
}
