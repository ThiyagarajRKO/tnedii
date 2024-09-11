<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VendorModifyVendorTableKJWGJZUelfGI extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('vendors', function (Blueprint $table) {
			    
                if (!Schema::hasColumn('vendors', 'prefix_id')){
                    $table->integer("prefix_id")->after('id');
                }
                if (!Schema::hasColumn('vendors', 'user_id')){
                    $table->string("user_id", "255")->after('id');
                }
                if (!Schema::hasColumn('vendors', 'address')){
                    $table->text("address")->after('phone')->change();
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
        Schema::dropIfExists('vendors');
    }
}
