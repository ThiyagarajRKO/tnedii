<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateKnowledgePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('knowledge_partners');

        Schema::create('knowledge_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('email', 60);
            $table->string('phone', 60)->nullable();
            $table->string('address', 120)->nullable();
            $table->string('subject', 120)->nullable();
            $table->longText('content');
            $table->string('status', 60)->default('unread');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('knowledge_partners');
    }
}
