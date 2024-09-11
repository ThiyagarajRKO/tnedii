<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MasterDetailCreateCountyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('county')) {
            Schema::create('county', function (Blueprint $table) {
                $table->id();
			$table->string("name","100");
			$table->bigInteger("status")->default('1');
			$table->integer("district_id");
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
        Schema::dropIfExists('county');
    }
}
