<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAprotecUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aprotec_users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 250)->unique();
            $table->string('username', 250)->unique();
            $table->string('password', 250);
            $table->string('reset_token', 250)->nullable();
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
        Schema::dropIfExists('aprotec_users');
    }
}
