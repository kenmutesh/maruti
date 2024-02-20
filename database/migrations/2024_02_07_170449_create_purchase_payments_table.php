<?php

use App\Models\PurchaseOrder;
use App\Models\SupplierPayment;
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
        Schema::create('purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('supplier_payment_id')->unsigned();
            $table->bigInteger('purchase_order_id')->unsigned()->nullable();

            $table->decimal('amount_applied', 14)->default(0.00);
            
            $table->timestamps();
            $table->timestamp('nullified_at')->nullable();
            $table->softDeletes();
            
            $table->foreign('supplier_payment_id')->references('id')->on(SupplierPayment::getTableName());
            $table->foreign('purchase_order_id')->references('id')->on(PurchaseOrder::getTableName());

            $table->index('created_at');
            $table->index('nullified_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_payments');
    }
};
