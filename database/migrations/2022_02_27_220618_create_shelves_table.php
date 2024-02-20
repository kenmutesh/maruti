<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;
use App\Models\Floor;

class CreateShelvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->string('shelf_name', 250);
            
            $table->timestamps();
            $table->softDeletes();

            $table->bigInteger('company_id')->unsigned();
            $table->bigInteger('floor_id')->unsigned();

            $table->foreign('company_id')->references('id')->on(Company::getTableName());
            $table->foreign('floor_id')->references('id')->on(Floor::getTableName());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shelves');
    }
}
