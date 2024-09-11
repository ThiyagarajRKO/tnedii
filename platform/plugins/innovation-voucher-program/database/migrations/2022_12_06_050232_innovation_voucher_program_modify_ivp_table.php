<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class InnovationVoucherProgramModifyIvpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        Schema::table('innovation_voucher_programs', function (Blueprint $table) {
            if(!Schema::hasColumn('innovation_voucher_programs','is_enabled')){
                $table->tinyinteger("is_enabled")->default('1')->after('wf_status');
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
