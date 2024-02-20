<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->string('supplier_email');
            $table->string('supplier_mobile');
            $table->decimal('opening_balance', 10)->default(0);
            $table->string('company_location');
            $table->string('company_pin');
            $table->string('company_box');
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
        Schema::dropIfExists('suppliers');
    }
}
