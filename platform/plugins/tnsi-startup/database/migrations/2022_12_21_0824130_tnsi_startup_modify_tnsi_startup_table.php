<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TnsiStartupModifyTnsiStartupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('tnsi_startup', function (Blueprint $table) {
                if(!Schema::hasColumn('tnsi_startup','region_id')){
                $table->integer("region_id")->nullable()->after('id');
            }
                if(!Schema::hasColumn('tnsi_startup','hub_institution_id')){
                $table->integer("hub_institution_id")->nullable()->after('region_id');
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
        Schema::dropIfExists('hub_institutions');
    }
}
