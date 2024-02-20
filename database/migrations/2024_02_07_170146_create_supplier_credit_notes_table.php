<?php

use App\Models\Company;
use App\Models\PurchaseOrder;
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
        Schema::create('supplier_credit_notes', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_credit_note_prefix')->nullable();
            $table->unsignedInteger('supplier_credit_note_suffix');

            $table->bigInteger('supplier_id')->unsigned();
            $table->bigInteger('purchase_order_id')->unsigned()->nullable();

            $table->date('record_date');
            $table->longText('memo')->nullable();

            $table->unsignedDecimal('sum_subtotal', 14, 2)->default(0);
            $table->unsignedDecimal('sum_vataddition', 14, 2)->default(0);
            $table->unsignedDecimal('sum_grandtotal', 14, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('cancelled_at')->nullable();

            $table->bigInteger('company_id')->unsigned();

            $table->foreign('supplier_id')->references('id')->on(Supplier::getTableName());
            $table->foreign('purchase_order_id')->references('id')->on(PurchaseOrder::getTableName());
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
        Schema::dropIfExists('supplier_credit_notes');
    }
};
