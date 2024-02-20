<?php

use App\Models\InventoryItem;
use App\Models\NonInventoryItem;
use App\Models\Powder;
use App\Models\SupplierCreditNote;
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
        Schema::create('supplier_credit_note_items', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('supplier_credit_note_id')->unsigned()->nullable();

            $table->bigInteger('powder_id')->unsigned()->nullable();
            $table->bigInteger('inventory_item_id')->unsigned()->nullable();
            $table->bigInteger('non_inventory_item_id')->unsigned()->nullable();

            $table->unsignedDecimal('unit_price', 14, 2);
            $table->unsignedDecimal('quantity');
            $table->unsignedDecimal('vat');
            $table->boolean('vat_inclusive')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('supplier_credit_note_id')->references('id')->on(SupplierCreditNote::getTableName());
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
        Schema::dropIfExists('supplier_credit_note_items');
    }
};
