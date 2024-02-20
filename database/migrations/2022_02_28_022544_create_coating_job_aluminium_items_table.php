<?php

use App\Models\CoatingJob;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoatingJobAluminiumItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coating_job_aluminium_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('coating_job_id')->unsigned();
            $table->string('item_name');
            $table->string('uom')->default('LENGTHS');

            $table->unsignedDecimal('item_kg');
            
            $table->unsignedDecimal('unit_price', 14, 2);
            $table->unsignedDecimal('quantity');
            $table->unsignedDecimal('vat');
            $table->boolean('vat_inclusive')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('coating_job_id')->references('id')->on(CoatingJob::getTableName());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coating_job_aluminium_items');
    }
};
