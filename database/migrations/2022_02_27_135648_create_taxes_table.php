<?php

use App\Enums\TaxTypesEnum;
use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedDecimal('percentage');
            $table->unsignedInteger('type')->default(TaxTypesEnum::VAT->value);
            $table->timestamps();
            $table->softDeletes();

            $table->bigInteger('company_id')->unsigned();

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
        Schema::dropIfExists('taxes');
    }
}
