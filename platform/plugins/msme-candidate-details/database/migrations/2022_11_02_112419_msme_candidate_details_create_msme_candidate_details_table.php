<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MsmeCandidateDetailsCreateMsmeCandidateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('msme_candidate_details')) {
            Schema::create('msme_candidate_details', function (Blueprint $table) {
                $table->id();
			$table->string("msme_type","100")->nullable();
			$table->string("candidate_msme_ref_id","100")->nullable();
                        $table->string("scheme","100")->nullable();
			$table->string("candidate_name","255")->nullable();
			$table->string("care_of","10")->nullable();
			$table->string("father_husband_name","255")->nullable();
                        $table->string("spouse_name","255")->nullable();
			$table->string("gender","100")->nullable();
			$table->string("mobile_no","20")->nullable();
			$table->string("email","150")->nullable();
			$table->date("dob")->nullable();
			$table->string("qualification","100")->nullable();
			$table->string("district","100")->nullable();
			$table->text("address")->nullable();
			$table->text("photo")->nullable();
			$table->integer("is_enrolled")->nullable()->default('0');
			
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
        Schema::dropIfExists('msme_candidate_details');
    }
}
