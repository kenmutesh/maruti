<?php

use App\Enums\PaymentModesEnum;
use App\Models\Company;
use App\Models\Supplier;
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
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('supplier_id')->unsigned();
            $table->bigInteger('created_by')->unsigned();

            $table->unsignedInteger('payment_mode')->default(PaymentModesEnum::CASH->value);
            $table->string('transaction_ref');

            $table->date('payment_date');
            
            $table->decimal('sum_purchase_payments', 14)->default(0);

            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('nullified_at')->nullable();

            $table->bigInteger('company_id')->unsigned();

            $table->foreign('supplier_id')->references('id')->on(Supplier::getTableName());
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
        Schema::dropIfExists('supplier_payments');
    }
};
