<?php

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
        Schema::create('non_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->unsignedDecimal('standard_cost', 14, 2);
            $table->unsignedDecimal('standard_cost_vat')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->bigInteger('supplier_id')->unsigned();
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
        Schema::dropIfExists('non_inventory_items');
    }
};
