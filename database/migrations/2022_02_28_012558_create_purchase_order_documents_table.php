<?php

use App\Models\PurchaseOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('purchase_order_id')->unsigned();
            $table->unsignedInteger('type');
            $table->string('document_path');
            $table->string('document_name');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('purchase_order_id')->references('id')->on(PurchaseOrder::getTableName());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_documents');
    }
};
