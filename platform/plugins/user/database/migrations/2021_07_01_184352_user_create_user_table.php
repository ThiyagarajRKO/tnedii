<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UserCreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('impiger_users')) {
            Schema::create('impiger_users', function (Blueprint $table) {
                $table->id();
			$table->integer("user_id")->nullable();
			$table->string("email","191");
			$table->string("first_name","191");
			$table->string("last_name","191")->nullable();
			$table->string("username","60");
			$table->string("password","191")->nullable();
			$table->text("dob")->nullable();
			$table->string("nationality")->nullable();
			$table->string("identity_number","60")->nullable();
			$table->string("if_refugee")->default(0);
			$table->string("card_number")->nullable();
			$table->string("passport_number")->nullable();
			$table->text("blood_group")->nullable();
			$table->text("gender")->nullable();
			$table->text("marital_status")->nullable();
			$table->text("no_of_child")->nullable();
			$table->text("religion")->nullable();
			$table->text("next_of_kin")->nullable();
			$table->text("designation")->nullable();
			$table->text("staff_category")->nullable();
            $table->integer("payroll")->nullable();
			$table->integer("is_login_needed")->default(0)->nullable();
            $table->integer("is_enabled")->default(1);
			$table->text("photo")->nullable();
			
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
        Schema::dropIfExists('impiger_users');
    }
}
