<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EntrepreneurModifyEntrepreneurTableU9Jxbi93PECo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('entrepreneurs', function (Blueprint $table) {
                // $table->integer("prefix_id")->nullable()->after('id');
			// $table->string("community","255")->nullable()->after('care_of');
			// $table->string("name","250")->nullable()->after('community');
			// $table->integer("gender_id")->nullable()->after('dob');
			// $table->string("mobile","15")->nullable()->after('gender_id');
			// $table->string("aadhaar_no","255")->nullable()->after('father_name');
			// $table->integer("district_id")->nullable()->after('state_id');
			// $table->text("address")->nullable()->after('pincode');
			// $table->text("business_address")->nullable()->after('address');
			// $table->integer("physically_challenged")->nullable()->after('student_standard_name');
			// $table->integer("business_status")->after('department_name')->change();
			$table->integer("is_active")->nullable()->after('student_type_id')->change();
			if (Schema::hasColumn('entrepreneurs', 'aadhar_no')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('aadhar_no');
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
        Schema::dropIfExists('entrepreneurs');
    }
}
