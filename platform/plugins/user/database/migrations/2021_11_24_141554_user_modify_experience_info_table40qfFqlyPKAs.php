<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserModifyExperienceInfoTable40qfFqlyPKAs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('experience_info', function (Blueprint $table) {
                if(!Schema::hasColumn('experience_info','curriculum_vitae')){
                    $table->text("curriculum_vitae")->nullable()->after('achievements');
                }
			$table->integer("imp_user_id")->after('id')->change();
			$table->integer("type_of_employment_sector")->nullable()->after('name_of_the_employer')->change();
			$table->integer("employment_status")->nullable()->after('curriculum_vitae')->change();
			$table->integer("country_id")->nullable()->after('salary_scale')->change();
			
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
