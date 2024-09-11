<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class InnovationVoucherProgramAddEmailMobileIvpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('innovation_voucher_programs', function (Blueprint $table) {
            if(!Schema::hasColumn('innovation_voucher_programs','mobile_number')){
                $table->string("mobile_number","15")->nullable()->after('project_title');
            }
            if(!Schema::hasColumn('innovation_voucher_programs','email_id')){
                $table->string("email_id","255")->nullable()->after('mobile_number');
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
        Schema::dropIfExists('innovation_voucher_programs');
    }
}
