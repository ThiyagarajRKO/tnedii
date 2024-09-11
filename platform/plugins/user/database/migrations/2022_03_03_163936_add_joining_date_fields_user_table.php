<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJoiningDateFieldsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('impiger_users', function (Blueprint $table) {
                if(!Schema::hasColumn('impiger_users','joining_date')){
                    $table->date("joining_date")->nullable()->after('is_login_needed');
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
