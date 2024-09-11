<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MentorModifyMentorTablesgXhT73hyAHv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('mentors', function (Blueprint $table) {
                if (!Schema::hasColumn('mentors', 'user_id')){
                    $table->integer("user_id")->after('id');
                }
                if (!Schema::hasColumn('mentors', 'name')){
                    $table->string("name", "255")->after('entrepreneur_id');
                }
                if (!Schema::hasColumn('mentors', 'email')){
                    $table->string("email", "255")->after('name');
                }
                if (!Schema::hasColumn('mentors', 'password')){
                    $table->string("password", "255")->after('email');
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
        Schema::dropIfExists('mentors');
    }
}
