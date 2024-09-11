<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TrainingTitleTableAlterPayAttribute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_title', function (Blueprint $table) {
            if (!Schema::hasColumn('training_title', 'fee_amount')){
                $table->string("fee_amount","25")->nullable()->after('fee_paid');
            }
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_title');
    }
}
