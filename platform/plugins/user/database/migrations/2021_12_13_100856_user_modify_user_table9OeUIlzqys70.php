<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserModifyUserTable9OeUIlzqys70 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('impiger_users', function (Blueprint $table) {
                if(!Schema::hasColumn('impiger_users','registered_user')){
                    $table->integer("registered_user")->default(0)->after('photo');
                }                
		$table->date("dob")->nullable()->after('passport_number')->change();
		$table->integer("user_id")->nullable()->after('id')->change();
		$table->string("password","191")->nullable()->after('username')->change();
		$table->integer("is_login_needed")->nullable()->after('payroll')->change();
                if(!Schema::hasColumn('impiger_users','wf_status')){
                    $table->string("wf_status","100")->nullable()->after('registered_user');
                }
                if(!Schema::hasColumn('impiger_users','registered_role')){
                    $table->integer("registered_role")->nullable()->after('registered_user');
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
