<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MasterDetailCreateParishTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('parish')) {
            Schema::create('parish', function (Blueprint $table) {
                $table->id();
			$table->string("name","100");
			$table->bigInteger("status");
			$table->integer("created_by");
			$table->integer("sub_county_id");
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
        Schema::dropIfExists('parish');
    }
}
