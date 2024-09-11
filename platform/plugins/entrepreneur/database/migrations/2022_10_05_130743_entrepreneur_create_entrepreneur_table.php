<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class EntrepreneurCreateEntrepreneurTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('entrepreneurs')) {
            Schema::create('entrepreneurs', function (Blueprint $table) {
                $table->id();
			$table->integer("prefix_id")->nullable();
			$table->string("care_of","20")->nullable();
			$table->string("community","255")->nullable();
			$table->string("name","250")->nullable();
			$table->string("dob","20")->nullable();
			$table->integer("gender_id")->nullable();
			$table->string("mobile","15")->nullable();
			$table->string("email","200")->nullable();
			$table->string("password","150")->nullable();
			$table->string("father_name","255")->nullable();
			$table->string("aadhaar_no","255")->nullable();
			$table->integer("category_id")->nullable();
			$table->string("company_name","150")->nullable();
			$table->integer("state_id")->nullable();
			$table->integer("district_id")->nullable();
			$table->integer("pincode")->nullable();
			$table->text("address")->nullable();
			// $table->string("business_address")->nullable();
			// $table->integer("industry_id")->nullable();
			// $table->integer("specialization_id")->nullable();
			// $table->integer("entrepreneur_business_sector_id")->nullable();
			// $table->integer("entrepreneur_specific_sector_id")->nullable();
			// $table->text("entrepreneur_business_description")->nullable();
			// $table->integer("bank_category_id")->nullable();
			// $table->text("support_required")->nullable();
			// $table->text("support_offered")->nullable();
			// $table->text("service_offer_id")->nullable();
			$table->integer("university_type_id")->nullable();
			$table->integer("type_of_college_id")->nullable();
			$table->string("college_name","255")->nullable();
			// $table->string("product","255")->nullable();
			// $table->string("cluster","255")->nullable();
			// $table->string("other_organization","255")->nullable();
			// $table->integer("govt_type")->nullable();
			// $table->string("department_name","255")->nullable();
			$table->string("aadhar_no","100")->nullable();
			// $table->integer("business_status")->default('0');
			$table->string("website_link","255")->nullable();
			$table->string("photo_path","150")->nullable();
			$table->integer("candidate_type_id")->nullable();
			$table->integer("entrepreneurial_category_id")->nullable();
			$table->integer("college_hub_id")->nullable();
			$table->string("student_college_name","255")->nullable();
			$table->string("student_course_name","255")->nullable();
			$table->string("student_year","100")->nullable();
			$table->string("student_school_name","255")->nullable();
			$table->string("student_standard_name","255")->nullable();
			$table->integer("physically_challenged")->nullable()->default('0');
			$table->string("activity_name","255")->nullable();
			$table->integer("qualification_id")->nullable();
			$table->integer("religion_id")->nullable();
			$table->integer("student_type_id")->nullable();
			$table->integer("is_active")->nullable()->default('1');
			$table->text("note")->nullable();
			
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
        Schema::dropIfExists('entrepreneurs');
    }
}
