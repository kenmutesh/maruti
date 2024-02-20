<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\InventoryItem;
use App\Models\NonInventoryItem;
use App\Models\Powder;
use App\Models\Floor;
use App\Models\Shelf;
use App\Models\Warehouse;
use App\Models\Bin;
use App\Models\Company;

class CreatePowderAndInventoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('powder_and_inventory_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('reason')->nullable();
            $table->bigInteger('reason_id')->unsigned()->nullable();

            $table->decimal('sum_added');

            $table->bigInteger('powder_id')->unsigned()->nullable();
            $table->bigInteger('inventory_item_id')->unsigned()->nullable();
            $table->bigInteger('non_inventory_item_id')->unsigned()->nullable();

            $table->bigInteger('warehouse_id')->unsigned()->nullable();
            $table->bigInteger('floor_id')->unsigned()->nullable();
            $table->bigInteger('shelf_id')->unsigned()->nullable();
            $table->bigInteger('bin_id')->unsigned()->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->bigInteger('company_id')->unsigned()->nullable();

            $table->foreign('warehouse_id')->references('id')->on(Warehouse::getTableName());
            $table->foreign('floor_id')->references('id')->on(Floor::getTableName());
            $table->foreign('shelf_id')->references('id')->on(Shelf::getTableName());
            $table->foreign('bin_id')->references('id')->on(Bin::getTableName());

            $table->foreign('powder_id')->references('id')->on(Powder::getTableName());
            $table->foreign('inventory_item_id')->references('id')->on(InventoryItem::getTableName());
            $table->foreign('non_inventory_item_id')->references('id')->on(NonInventoryItem::getTableName());

            $table->foreign('company_id')->references('id')->on(Company::getTableName());

            $table->index('reason_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('powder_and_invetory_logs');
    }
};
