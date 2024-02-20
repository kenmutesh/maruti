<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_labels', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('document');
            $table->string('document_prefix')->nullable();
            $table->unsignedInteger('document_suffix');
            $table->timestamps();
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
        Schema::dropIfExists('document_labels');
    }
}
