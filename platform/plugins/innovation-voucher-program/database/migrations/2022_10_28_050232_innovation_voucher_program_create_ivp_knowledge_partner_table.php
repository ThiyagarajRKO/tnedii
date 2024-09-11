<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class InnovationVoucherProgramCreateIvpKnowledgePartnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('ivp_knowledge_partner_details')) {
            Schema::create('ivp_knowledge_partner_details', function (Blueprint $table) {
                $table->id();
			$table->integer("innovation_voucher_program_id")->nullable();
                        $table->string("organization_type","255")->nullable();
			$table->string("organization_name","255")->nullable();
			$table->string("contact_person","255")->nullable();
			$table->string("designation","255")->nullable();
			$table->string("mobile_number","255")->nullable();
			$table->string("email_id","255")->nullable();
			$table->string("responsibilities","255")->nullable();
			$table->text("attachment")->nullable();
			
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
        Schema::dropIfExists('ivp_knowledge_partner_details');
    }
}
