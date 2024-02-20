<?php

use App\Enums\InventoryItemsEnum;
use App\Models\Supplier;
use App\Models\Company;
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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('type')->default(InventoryItemsEnum::ALUMINIUM->value);
            $table->string('item_name');
            $table->string('item_code');
            $table->string('item_description');
            $table->string('serial_no');
            $table->string('quantity_tag');
            $table->unsignedFloat('goods_weight', 8, 4);
            
            $table->unsignedDecimal('standard_cost', 14, 2)->default(0);
            $table->unsignedDecimal('standard_cost_vat')->default(0);
            $table->unsignedDecimal('standard_price', 14, 2)->default(0);
            $table->unsignedDecimal('standard_price_vat')->default(0);

            $table->unsignedDecimal('min_threshold');
            $table->unsignedDecimal('max_threshold');
            $table->decimal('current_quantity')->default(0);
            $table->unsignedDecimal('opening_quantity')->default(0);
            
            $table->timestamps();
            $table->softDeletes();

            $table->bigInteger('supplier_id')->unsigned()->nullable();
            $table->bigInteger('company_id')->unsigned();


            $table->foreign('supplier_id')->references('id')->on(Supplier::getTableName());
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
        Schema::dropIfExists('inventory_items');
    }
};
