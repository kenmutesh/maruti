<?php

use App\Enums\CoatingJobOwnerEnum;
use App\Enums\CoatingJobProfileTypesEnum;
use App\Enums\CoatingJobStatusEnum;
use App\Models\CashSale;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Powder;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoatingJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coating_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('coating_prefix')->nullable();
            $table->unsignedInteger('coating_suffix')->nullable();
            $table->string('quotation_prefix')->nullable();
            $table->unsignedInteger('quotation_suffix')->nullable();

            $table->bigInteger('customer_id')->unsigned();
            $table->string('lpo')->nullable();
            $table->string('cash_sale_name')->nullable();

            $table->date('date')->nullable();
            $table->date('in_date', 250)->nullable();
            $table->date('ready_date', 250)->nullable();
            $table->date('out_date', 250)->nullable();

            $table->unsignedDecimal('goods_weight')->nullable();
            $table->unsignedInteger('profile_type')->default(CoatingJobProfileTypesEnum::NOTAPPLICABLE->value);

            $table->unsignedDecimal('powder_estimate')->nullable();
            $table->bigInteger('powder_id')->unsigned()->nullable();

            $table->unsignedInteger('belongs_to')->default(CoatingJobOwnerEnum::MARUTI->value);
            $table->unsignedInteger('status')->default(CoatingJobStatusEnum::OPEN->value);

            $table->unsignedDecimal('sum_subtotal', 14, 2)->default(0);
            $table->unsignedDecimal('sum_vataddition', 14, 2)->default(0);
            $table->unsignedDecimal('sum_grandtotal', 14, 2)->default(0);

            $table->bigInteger('cash_sale_id')->unsigned()->nullable();
            $table->bigInteger('invoice_id')->unsigned()->nullable();

            $table->bigInteger('prepared_by')->unsigned()->nullable();
            $table->bigInteger('supervisor')->unsigned()->nullable();
            $table->bigInteger('quality_by')->unsigned()->nullable();
            $table->bigInteger('sale_by')->unsigned()->nullable();
            $table->bigInteger('created_by')->unsigned();

            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('cancelled_at')->nullable();

            $table->bigInteger('company_id')->unsigned();

            $table->foreign('powder_id')->references('id')->on(Powder::getTableName());
            $table->foreign('cash_sale_id')->references('id')->on(CashSale::getTableName());
            $table->foreign('invoice_id')->references('id')->on(Invoice::getTableName());
            $table->foreign('company_id')->references('id')->on(Company::getTableName());
            $table->foreign('customer_id')->references('id')->on(Customer::getTableName());
            $table->foreign('prepared_by')->references('id')->on(User::getTableName());
            $table->foreign('supervisor')->references('id')->on(User::getTableName());
            $table->foreign('quality_by')->references('id')->on(User::getTableName());
            $table->foreign('sale_by')->references('id')->on(User::getTableName());
            $table->foreign('created_by')->references('id')->on(User::getTableName());

            $table->index('created_at');
            $table->index('coating_suffix');
            $table->index('quotation_suffix');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coating_jobs');
    }
}
