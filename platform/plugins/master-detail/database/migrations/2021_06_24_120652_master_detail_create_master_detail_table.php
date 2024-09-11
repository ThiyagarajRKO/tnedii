<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MasterDetailCreateMasterDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('attribute_options')) {
            Schema::create('attribute_options', function (Blueprint $table) {
                $table->id();
			$table->string("attribute","64");
			$table->string("name","64");
			$table->bigInteger("is_default")->nullable()->default('0');
			$table->integer("is_enabled")->default('1');
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
        Schema::dropIfExists('attribute_options');
    }
}
