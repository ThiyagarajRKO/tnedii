<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class InnovationVoucherProgramCreateInnovationVoucherProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('innovation_voucher_programs')) {
            Schema::create('innovation_voucher_programs', function (Blueprint $table) {
                $table->id();
			$table->string("application_number","191")->nullable();
			$table->string("voucher_type","191")->nullable();
			$table->text("project_title")->nullable();
			$table->text("problem_of_sector")->nullable();
			$table->text("scope_objective")->nullable();
			$table->text("outcomes_deliverables")->nullable();
			$table->text("role_of_knowledge_partner")->nullable();
			$table->text("budjet")->nullable();
			$table->text("team_capability")->nullable();
			$table->text("nature_of_innovation")->nullable();
			$table->text("impact")->nullable();
			$table->text("project_need")->nullable();
			$table->text("competetive")->nullable();
			$table->text("level_of_impact")->nullable();
			$table->text("capability_capacity")->nullable();
			$table->text("collabaration_with_knowledge_partner")->nullable();
			$table->text("pitch_for_your_project")->nullable();
			$table->string("project_based","50")->nullable();
			$table->string("main_sector","50")->nullable();
			$table->string("additional_sector","50")->nullable();
			$table->string("duration","191")->nullable();
			$table->text("envisaged_timeline")->nullable();
			$table->string("project_cost","100")->nullable();
			$table->text("estimated_cost")->nullable();
			$table->text("presentation")->nullable();
			$table->text("attachments")->nullable();
			$table->string("reference_link")->nullable();
			$table->integer("is_agree")->nullable();
			$table->string("state","100")->nullable();
			$table->integer("district_id")->nullable();
			$table->string("identified_knowledge_partner","100")->nullable();
			$table->string("wf_status","191")->nullable()->default('registered');
			$table->integer("created_by")->nullable();
			$table->integer("updated_by")->nullable();
			
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
        Schema::dropIfExists('innovation_voucher_programs');
    }
}
