<?php

use App\Models\Supplier;
use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePowdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('powders', function (Blueprint $table) {
            $table->id();
            $table->string('powder_color');
            $table->string('powder_code');
            $table->string('powder_description');
            $table->string('serial_no');

            $table->date('manufacture_date');
            $table->date('expiry_date');
            $table->unsignedFloat('goods_weight', 8, 4);
            $table->string('batch_no');

            $table->unsignedDecimal('standard_cost', 14, 2)->default(0);
            $table->unsignedDecimal('standard_cost_vat')->default(0);
            $table->unsignedDecimal('standard_price', 14, 2)->default(0);
            $table->unsignedDecimal('standard_price_vat')->default(0);

            $table->unsignedDecimal('min_threshold');
            $table->unsignedDecimal('max_threshold');
            $table->decimal('current_weight')->default(0);
            $table->unsignedDecimal('opening_weight')->default(0);

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
        Schema::dropIfExists('powders');
    }
}
