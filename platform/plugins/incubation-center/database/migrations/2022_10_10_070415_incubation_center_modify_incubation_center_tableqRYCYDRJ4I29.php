<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncubationCenterModifyIncubationCenterTableqRYCYDRJ4I29 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('incubation_centers', function (Blueprint $table) {
                if (Schema::hasColumn('incubation_centers', 'created_date')){

                        Schema::table('incubation_centers', function (Blueprint $table) {
                            $table->dropColumn('created_date');
                        });
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
        Schema::dropIfExists('incubation_centers');
    }
}
