<?php

namespace Database\Factories;

use App\Enums\CoatingJobOwnerEnum;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use App\Models\Powder;
use App\Models\CoatingJob;
use App\Enums\CoatingJobProfileTypesEnum;
use App\Enums\CoatingJobStatusEnum;
use App\Models\CashSale;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoatingJobFactory extends Factory
{
    public function definition()
    {
        return [
            'customer_id' => Customer::factory(),
            'lpo' => $this->faker->regexify('[1-9]{5}'),
            'date' => $this->faker->dateTimeBetween('-2 days'),
            'in_date' => $this->faker->dateTimeBetween('-2 days'),
            'ready_date' => $this->faker->dateTimeBetween('now', '+3 days'),
            'out_date' => $this->faker->dateTimeBetween('+3 days', '+5 days'),
            'goods_weight' => $this->faker->numberBetween(1, 50),
            'profile_type' => CoatingJobProfileTypesEnum::MEDIUM->value,
            'powder_estimate' => $this->faker->numberBetween(1, 10),
            'powder_id' => Powder::factory(),
            'belongs_to' => CoatingJobOwnerEnum::MARUTI->value,
            'status' => CoatingJobStatusEnum::OPEN->value,
            'prepared_by' => User::factory(),
            'supervisor' => User::factory(),
            'quality_by' => User::factory(),
            'sale_by' => User::factory(),
            'created_by' => User::factory(),
            'company_id' => Company::factory()
        ];
    }

    public function cancelledcoatingjob()
    {
        return $this->state(function (array $attributes) {
            $coatingJob = new CoatingJob();
            return [
                'coating_prefix' => $coatingJob->next_coating_job_prefix,
                'coating_suffix' => $coatingJob->next_coating_job_suffix,
                'status' => CoatingJobStatusEnum::CANCELLED->value,
                'cancelled_at' => Carbon::now()
            ];
        });
    }

    public function closedcoatingjobinvoice()
    {
        return $this->state(function (array $attributes) {
            $coatingJob = new CoatingJob();
            return [
                'invoice_id' => Invoice::factory(),
                'coating_prefix' => $coatingJob->next_coating_job_prefix,
                'coating_suffix' => $coatingJob->next_coating_job_suffix,
                'status' => CoatingJobStatusEnum::CLOSED->value,
            ];
        });
    }

    public function closedcoatingjobcashsale()
    {
        return $this->state(function (array $attributes) {
            $coatingJob = new CoatingJob();
            return [
                'cash_sale_id' => CashSale::factory(),
                'coating_prefix' => $coatingJob->next_coating_job_prefix,
                'coating_suffix' => $coatingJob->next_coating_job_suffix,
                'status' => CoatingJobStatusEnum::CLOSED->value,
            ];
        });
    }

    public function coatingjobquotation()
    {
        return $this->state(function (array $attributes) {
            $coatingJob = new CoatingJob();
            return [
                'quotation_prefix' => $coatingJob->next_quotation_prefix,
                'quotation_suffix' => $coatingJob->next_quotation_suffix,
            ];
        });
    }

    public function jobcard()
    {
        return $this->state(function (array $attributes) {
            $coatingJob = new CoatingJob();
            return [
                'coating_prefix' => $coatingJob->next_coating_job_prefix,
                'coating_suffix' => $coatingJob->next_coating_job_suffix,
            ];
        });
    }
}
