<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterDetailModifyDistrictTablehwD7pBBNp9GI extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('district', function (Blueprint $table) {
                if(!Schema::hasColumn('district',"code")){
                    $table->string("code","50")->nullable()->after('name');
                }
                if(!Schema::hasColumn('district',"region_id")){
			$table->integer("region_id")->nullable()->after('status');
                }
                if(!Schema::hasColumn('district',"is_enabled")){
			$table->integer("is_enabled")->after('deleted_at')->change();
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
        Schema::dropIfExists('district');
    }
}
