<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition()
    {
        $invoice = new Invoice();
        return [
            'customer_id' => Customer::factory(),
            'discount' => 0,
            'cu_number_prefix' => $invoice->next_cu_prefix,
            'cu_number_suffix' => $invoice->next_cu_suffix,
            'invoice_prefix' => $invoice->next_invoice_prefix,
            'invoice_suffix' => $invoice->next_invoice_suffix,
            'created_by' => User::factory(),
            'company_id' => Company::factory()
        ];
    }

    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            $invoice = new Invoice();
            return [
                'cu_number_prefix' => $invoice->next_cu_prefix,
                'cu_number_suffix' => $invoice->next_cu_suffix,
                'invoice_prefix' => $invoice->next_invoice_prefix,
                'invoice_suffix' => $invoice->next_invoice_suffix,
                'cancelled_at' => Carbon::now(),
            ];
        });
    }

    public function thirtydayperiod()
    {
        return $this->state(function (array $attributes) {
            $invoice = new Invoice();
            return [
                'cu_number_prefix' => $invoice->next_cu_prefix,
                'cu_number_suffix' => $invoice->next_cu_suffix,
                'invoice_prefix' => $invoice->next_invoice_prefix,
                'invoice_suffix' => $invoice->next_invoice_suffix,
                'created_at' => $this->faker->dateTimeInInterval('-1 day', '-30 days'),
            ];
        });
    }

    public function sixtydayperiod()
    {
        return $this->state(function (array $attributes) {
            $invoice = new Invoice();
            return [
                'cu_number_prefix' => $invoice->next_cu_prefix,
                'cu_number_suffix' => $invoice->next_cu_suffix,
                'invoice_prefix' => $invoice->next_invoice_prefix,
                'invoice_suffix' => $invoice->next_invoice_suffix,
                'created_at' => $this->faker->dateTimeInInterval('-31 days', '-60 days'),
            ];
        });
    }

    public function ninetydayperiod()
    {
        return $this->state(function (array $attributes) {
            $invoice = new Invoice();
            return [
                'cu_number_prefix' => $invoice->next_cu_prefix,
                'cu_number_suffix' => $invoice->next_cu_suffix,
                'invoice_prefix' => $invoice->next_invoice_prefix,
                'invoice_suffix' => $invoice->next_invoice_suffix,
                'created_at' => $this->faker->dateTimeInInterval('-61 days', '-90 days'),
            ];
        });
    }

    public function overninetydayperiod()
    {
        return $this->state(function (array $attributes) {
            $invoice = new Invoice();
            return [
                'cu_number_prefix' => $invoice->next_cu_prefix,
                'cu_number_suffix' => $invoice->next_cu_suffix,
                'invoice_prefix' => $invoice->next_invoice_prefix,
                'invoice_suffix' => $invoice->next_invoice_suffix,
                'created_at' => $this->faker->dateTimeInInterval('-91 days', '-200 days'),
            ];
        });
    }
}
