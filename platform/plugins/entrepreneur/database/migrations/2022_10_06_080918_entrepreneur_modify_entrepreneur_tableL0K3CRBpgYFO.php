<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EntrepreneurModifyEntrepreneurTableL0K3CRBpgYFO extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('entrepreneurs', function (Blueprint $table) {
                if (Schema::hasColumn('entrepreneurs', 'company_name')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('company_name');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'industry_id')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('industry_id');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'specialization_id')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('specialization_id');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'entrepreneur_business_sector_id')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('entrepreneur_business_sector_id');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'entrepreneur_specific_sector_id')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('entrepreneur_specific_sector_id');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'entrepreneur_business_description')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('entrepreneur_business_description');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'bank_category_id')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('bank_category_id');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'business_address')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('business_address');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'support_required')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('support_required');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'support_offered')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('support_offered');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'service_offer_id')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('service_offer_id');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'product')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('product');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'cluster')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('cluster');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'other_organization')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('other_organization');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'govt_type')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('govt_type');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'department_name')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('department_name');
                        });
                    }if (Schema::hasColumn('entrepreneurs', 'business_status')){

                        Schema::table('entrepreneurs', function (Blueprint $table) {
                            $table->dropColumn('business_status');
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
