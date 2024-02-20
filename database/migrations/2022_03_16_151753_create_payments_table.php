<?php

use App\Enums\PaymentModesEnum;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->unsigned();
            $table->bigInteger('created_by')->unsigned();

            $table->unsignedInteger('payment_mode')->default(PaymentModesEnum::CASH->value);
            $table->string('transaction_ref');

            $table->date('payment_date')->nullable();
            
            $table->decimal('sum_invoice_payments', 14)->default(0);

            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('nullified_at')->nullable();

            $table->bigInteger('company_id')->unsigned();

            $table->foreign('customer_id')->references('id')->on(Customer::getTableName());
            $table->foreign('company_id')->references('id')->on(Company::getTableName());

            $table->index('payment_date');
            $table->index('created_at');
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
        Schema::dropIfExists('payments');
    }
}
