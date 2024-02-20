<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->unsignedDecimal('credit_limit', 14, 2)->default(0);
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->string('contact_number');
            $table->string('location');
            $table->string('company');
            $table->string('contact_person_name');
            $table->string('contact_person_email');
            $table->string('kra_pin');

            $table->timestamps();
            $table->softDeletes();

            $table->bigInteger('company_id')->unsigned();

            $table->foreign('company_id')->references('id')->on(Company::getTableName());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
