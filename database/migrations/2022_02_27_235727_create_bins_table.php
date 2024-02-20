<?php

use App\Models\Company;
use App\Models\Shelf;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bins', function (Blueprint $table) {
            $table->id();
            $table->string('bin_name');
            $table->string('bin_description');

            $table->timestamps();
            $table->softDeletes();

            $table->bigInteger('company_id')->unsigned();
            $table->bigInteger('shelf_id')->unsigned();

            $table->foreign('company_id')->references('id')->on(Company::getTableName());
            $table->foreign('shelf_id')->references('id')->on(Shelf::getTableName());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bins');
    }
}
