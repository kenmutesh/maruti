<?php

use App\Models\CustomerCreditNote;
use App\Models\InventoryItem;
use App\Models\Powder;
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
        Schema::create('customer_credit_note_items', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('customer_credit_note_id')->unsigned()->nullable();
            $table->bigInteger('powder_id')->unsigned()->nullable();
            $table->bigInteger('inventory_item_id')->unsigned()->nullable();

            $table->string('custom_item_name')->nullable();

            $table->unsignedDecimal('unit_price', 14, 2);
            $table->unsignedDecimal('quantity');
            $table->unsignedDecimal('vat');
            $table->boolean('vat_inclusive')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_credit_note_id')->references('id')->on(CustomerCreditNote::getTableName());
            $table->foreign('powder_id')->references('id')->on(Powder::getTableName());
            $table->foreign('inventory_item_id')->references('id')->on(InventoryItem::getTableName());
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
        Schema::dropIfExists('customer_credit_note_items');
    }
};
