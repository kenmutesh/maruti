<?php

use App\Enums\PurchaseOrderStatusEnum;
use App\Models\Bin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;
use App\Models\Floor;
use App\Models\Shelf;
use App\Models\Supplier;
use App\Models\Warehouse;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('lpo_prefix')->nullable();
            $table->unsignedInteger('lpo_suffix')->nullable();

            $table->date('record_date');
            $table->date('due_date')->nullable();

            $table->string('quotation_ref')->nullable();
            $table->string('memo_ref')->nullable();
            $table->string('invoice_ref')->nullable();
            $table->string('delivery_ref')->nullable();

            $table->unsignedDecimal('sum_subtotal', 14, 2)->default(0);
            $table->unsignedDecimal('sum_vataddition', 14, 2)->default(0);
            $table->unsignedDecimal('sum_grandtotal', 14, 2)->default(0);

            $table->unsignedDecimal('discount', 14, 2)->default(0.00);
            $table->unsignedDecimal('amount_due', 14, 2)->default(0.00);

            $table->string('currency');
            $table->mediumText('terms');

            $table->unsignedInteger('status')->default(PurchaseOrderStatusEnum::OPEN->value);

            $table->timestamps();
            $table->softDeletes();

            $table->bigInteger('warehouse_id')->unsigned()->nullable();
            $table->bigInteger('floor_id')->unsigned()->nullable();
            $table->bigInteger('shelf_id')->unsigned()->nullable();
            $table->bigInteger('bin_id')->unsigned()->nullable();
            
            $table->bigInteger('supplier_id')->unsigned();
            $table->bigInteger('company_id')->unsigned();

            $table->foreign('warehouse_id')->references('id')->on(Warehouse::getTableName());
            $table->foreign('floor_id')->references('id')->on(Floor::getTableName());
            $table->foreign('shelf_id')->references('id')->on(Shelf::getTableName());
            $table->foreign('bin_id')->references('id')->on(Bin::getTableName());
            $table->foreign('company_id')->references('id')->on(Company::getTableName());
            $table->foreign('supplier_id')->references('id')->on(Supplier::getTableName());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}
