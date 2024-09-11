<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class InnovationVoucherProgramCreateIvpCompanyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('ivp_company_details')) {
            Schema::create('ivp_company_details', function (Blueprint $table) {
                $table->id();
			$table->integer("innovation_voucher_program_id")->nullable();
			$table->string("company_name","255")->nullable();
			$table->string("designation","255")->nullable();
			$table->text("company_address")->nullable();
			$table->string("company_classification","100")->nullable();
			$table->string("website","191")->nullable();
			$table->text("certificate")->nullable();
			$table->string("registration_number","255")->nullable();
			$table->date("registration_date")->nullable();
			$table->string("annual_turnover","100")->nullable();
			$table->string("no_of_employees","100")->nullable();
			
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
        Schema::dropIfExists('ivp_company_details');
    }
}
