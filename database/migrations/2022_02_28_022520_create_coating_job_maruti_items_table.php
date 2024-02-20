<?php

use App\Models\CoatingJob;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Floor;
use App\Models\Shelf;
use App\Models\Warehouse;
use App\Models\Bin;
use App\Models\InventoryItem;
use App\Models\Powder;

class CreateCoatingJobMarutiItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coating_job_maruti_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('coating_job_id')->unsigned();
            $table->bigInteger('inventory_item_id')->unsigned()->nullable();
            $table->bigInteger('powder_id')->unsigned()->nullable();
            $table->string('custom_item_name')->nullable();
            $table->string('uom')->default('UNITS');
            $table->string('boxes')->default(1);

            $table->bigInteger('warehouse_id')->unsigned()->nullable();
            $table->bigInteger('floor_id')->unsigned()->nullable();
            $table->bigInteger('shelf_id')->unsigned()->nullable();
            $table->bigInteger('bin_id')->unsigned()->nullable();

            $table->unsignedDecimal('unit_price', 14, 2);
            $table->unsignedDecimal('quantity');
            $table->unsignedDecimal('vat');
            $table->boolean('vat_inclusive')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('coating_job_id')->references('id')->on(CoatingJob::getTableName());
            $table->foreign('warehouse_id')->references('id')->on(Warehouse::getTableName());
            $table->foreign('floor_id')->references('id')->on(Floor::getTableName());
            $table->foreign('shelf_id')->references('id')->on(Shelf::getTableName());
            $table->foreign('bin_id')->references('id')->on(Bin::getTableName());
            $table->foreign('inventory_item_id')->references('id')->on(InventoryItem::getTableName());
            $table->foreign('powder_id')->references('id')->on(Powder::getTableName());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coating_job_maruti_items');
    }
};
