<?php

use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_prefix')->nullable();
            $table->unsignedInteger('invoice_suffix');

            $table->string('ext_invoice_prefix')->nullable();
            $table->unsignedInteger('ext_invoice_suffix')->nullable();

            $table->bigInteger('customer_id')->unsigned();

            $table->unsignedDecimal('discount', 14, 2)->default(0.00);
            $table->unsignedDecimal('amount_due', 14, 2)->default(0.00);

            $table->string('cu_number_prefix')->nullable();
            $table->unsignedInteger('cu_number_suffix')->nullable();

            $table->boolean('external')->default(false);

            $table->bigInteger('created_by')->unsigned();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->bigInteger('company_id')->unsigned();

            $table->foreign('company_id')->references('id')->on(Company::getTableName());
            $table->foreign('customer_id')->references('id')->on(Customer::getTableName());
            $table->foreign('created_by')->references('id')->on(User::getTableName());

            $table->index('created_at');
            $table->index('invoice_suffix');
            $table->index('ext_invoice_suffix');
            $table->index('cu_number_suffix');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
