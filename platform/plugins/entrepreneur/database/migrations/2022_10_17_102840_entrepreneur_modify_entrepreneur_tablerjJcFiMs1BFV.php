<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EntrepreneurModifyEntrepreneurTablerjJcFiMs1BFV extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('entrepreneurs', function (Blueprint $table) {
                if (!Schema::hasColumn('entrepreneurs', 'hub_institution_id')){
                    $table->integer("hub_institution_id")->nullable()->after('entrepreneurial_category_id');
                }
                if (!Schema::hasColumn('entrepreneurs', 'spoke_registration_id')){
			        $table->integer("spoke_registration_id")->nullable()->after('hub_institution_id');
                }   
                if (!Schema::hasColumn('entrepreneurs', 'is_active')){
                        $table->integer("is_active")->nullable()->after('student_type_id')->change();
                }
			    if (Schema::hasColumn('entrepreneurs', 'college_hub_id')){
                    Schema::table('entrepreneurs', function (Blueprint $table) {
                        $table->dropColumn('college_hub_id');
                    });
                }
                if (Schema::hasColumn('entrepreneurs', 'spoke_id')){
                    Schema::table('entrepreneurs', function (Blueprint $table) {
                        $table->dropColumn('spoke_id');
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
