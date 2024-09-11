<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MenteeModifyMenteeTableb2qBdiMlRXLi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('mentees', function (Blueprint $table) {
                if (Schema::hasColumn('mentees', 'updated')){

                        Schema::table('mentees', function (Blueprint $table) {
                            $table->dropColumn('updated');
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
        Schema::dropIfExists('mentees');
    }
}
