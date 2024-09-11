<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccreditedStatusFieldsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('impiger_users', function (Blueprint $table) {
                if(!Schema::hasColumn('impiger_users','accredited_status')){
                    $table->integer("accredited_status")->nullable()->after('wf_status');
                }	
                if(!Schema::hasColumn('impiger_users','registration_number')){
                    $table->string("registration_number")->nullable()->after('accredited_status');
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
        Schema::dropIfExists('impiger_users');
    }
}
