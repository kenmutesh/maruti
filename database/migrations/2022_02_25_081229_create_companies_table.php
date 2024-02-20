<?php

use App\Enums\SubscriptionStatusEnum;
use App\Models\AprotecUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->unsignedInteger('subscription_status')->default(SubscriptionStatusEnum::INCOMPLETE->value);
            $table->dateTime('subscription_start_date')->nullable();
            $table->integer('subscription_duration')->nullable(); // measured in days
            $table->string('activation_key')->unique();
            $table->bigInteger('created_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on(AprotecUser::getTableName());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
