<?php

use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_credit_notes', function (Blueprint $table) {
            $table->id();
            $table->string('credit_note_prefix')->nullable();
            $table->unsignedInteger('credit_note_suffix');

            $table->bigInteger('customer_id')->unsigned();
            $table->bigInteger('invoice_id')->unsigned()->nullable();

            $table->date('record_date');
            $table->longText('memo')->nullable();

            $table->unsignedDecimal('sum_subtotal', 14, 2)->default(0);
            $table->unsignedDecimal('sum_vataddition', 14, 2)->default(0);
            $table->unsignedDecimal('sum_grandtotal', 14, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('cancelled_at')->nullable();

            $table->bigInteger('company_id')->unsigned();

            $table->foreign('customer_id')->references('id')->on(Customer::getTableName());
            $table->foreign('invoice_id')->references('id')->on(Invoice::getTableName());
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('customer_credit_notes');
    }
};
