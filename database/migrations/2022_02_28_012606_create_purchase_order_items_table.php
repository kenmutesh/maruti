<?php

use App\Models\InventoryItem;
use App\Models\NonInventoryItem;
use App\Models\Powder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PurchaseOrder;

class CreatePurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('purchase_order_id')->unsigned();
            $table->string('item_type');
            $table->string('new_item_name')->nullable();

            $table->bigInteger('powder_id')->unsigned()->nullable();
            $table->bigInteger('inventory_item_id')->unsigned()->nullable();
            $table->bigInteger('non_inventory_item_id')->unsigned()->nullable();

            $table->unsignedDecimal('quantity');
            $table->unsignedDecimal('cost', 14, 2, 10);
            $table->unsignedDecimal('vat');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('purchase_order_id')->references('id')->on(PurchaseOrder::getTableName());
            $table->foreign('powder_id')->references('id')->on(Powder::getTableName());
            $table->foreign('inventory_item_id')->references('id')->on(InventoryItem::getTableName());
            $table->foreign('non_inventory_item_id')->references('id')->on(NonInventoryItem::getTableName());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
