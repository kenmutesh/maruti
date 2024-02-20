<?php

use App\Models\Company;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->bigInteger('role_id')->unsigned();
            $table->string('password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->string('reset_token')->nullable();
            $table->timestamps();
            $table->softDeletes();        
            $table->bigInteger('company_id')->unsigned();

            $table->foreign('company_id')->references('id')->on(Company::getTableName());
            $table->foreign('role_id')->references('id')->on(Role::getTableName());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
