<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserModifyExperienceInfoTableRHwX3bYR95ZR extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('experience_info', function (Blueprint $table) {
            $table->renameColumn('cur_employer', 'name_of_the_employer');
            $table->integer("type_of_employment_sector")->nullable()->after('cur_employer');
            $table->renameColumn('designation', 'position_held');
            $table->integer("is_current_job")->nullable()->after('designation');
            $table->integer("country_id")->nullable()->after('salary_scale');
            $table->renameColumn('prev_org_date_of_appointment', 'start_date');
            $table->renameColumn('cur_org_date_of_appointment', 'end_date');
            $table->text("achievements")->nullable()->after('cur_org_date_of_appointment');
            if (Schema::hasColumn('experience_info', 'registration_no')) {

                Schema::table('experience_info', function (Blueprint $table) {
                    $table->dropColumn('registration_no');
                });
            }
            if (Schema::hasColumn('experience_info', 'prev_employer')) {

                Schema::table('experience_info', function (Blueprint $table) {
                    $table->dropColumn('prev_employer');
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
        Schema::dropIfExists('experience_info');
    }
}
