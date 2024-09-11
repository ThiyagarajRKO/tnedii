<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class FinancialYearCreateFinancialYearTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('financial_year')) {
            Schema::create('financial_year', function (Blueprint $table) {
                $table->id();
			$table->string("session_year","50");
			$table->string("session_start","25");
			$table->string("session_end","25");
			$table->text("description")->nullable();
			$table->bigint("is_running");
			$table->bigint("is_enabled")->default('1');
			
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
        Schema::dropIfExists('financial_year');
    }
}
