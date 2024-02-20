<?php

use App\Models\Invoice;
use App\Models\Payment;
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
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('payment_id')->unsigned();
            $table->bigInteger('invoice_id')->unsigned()->nullable();

            $table->decimal('amount_applied', 14)->default(0.00);
            
            $table->timestamps();
            $table->timestamp('nullified_at')->nullable();
            $table->softDeletes();
            
            $table->foreign('payment_id')->references('id')->on(Payment::getTableName());
            $table->foreign('invoice_id')->references('id')->on(Invoice::getTableName());

            $table->index('created_at');
            $table->index('nullified_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_payments');
    }
};
