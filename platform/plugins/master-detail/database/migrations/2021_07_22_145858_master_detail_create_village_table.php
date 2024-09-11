<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MasterDetailCreateVillageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('village')) {
            Schema::create('village', function (Blueprint $table) {
                $table->id();
			$table->string("name","100");
			$table->bigInteger("status");
			$table->string("parish_id","66");
			$table->integer("created_by");
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
        Schema::dropIfExists('village');
    }
}
