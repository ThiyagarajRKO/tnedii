<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpokeRegistrationModifySpokeRegistrationTablelyzzijJpSTIM extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('spoke_registration', function (Blueprint $table) {
                $table->string("website","255")->nullable()->after('email');
			$table->text("advisory_commitee")->nullable()->after('website');
			$table->text("department_faculty_coordinators")->nullable()->after('advisory_commitee');
			$table->integer("is_enabled")->nullable()->after('budget')->change();
			
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spoke_registration');
    }
}
