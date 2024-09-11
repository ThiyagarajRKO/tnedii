<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class VendorCreateVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('vendors')) {
            Schema::create('vendors', function (Blueprint $table) {
                $table->id();
                $table->integer("prefix_id");
                $table->string("name","255");
                $table->integer("pia_constitution_id");
                $table->integer("pia_mainactivity_id");
                $table->string("email","100");
                $table->string("password","255");
                $table->string("phone","50");
                $table->string("address");
                $table->integer("district_id");
                $table->integer("state_id");
                $table->string("pincode","15");
                $table->date("date_of_establishment");
                $table->string("name_principal","255");
                $table->string("contact_number","255");
                $table->string("seating_capacity","255");
                $table->string("audio_video","255");
                $table->string("rest_dining","255");
                $table->string("access_distance","255");
                $table->string("accommodation_facility","255");
                $table->string("refreshment_provision","255");
                $table->integer("experience_year");
                $table->text("achivements");
                $table->string("profile","150");
                $table->integer("created_by");
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
        Schema::dropIfExists('vendors');
    }
}
