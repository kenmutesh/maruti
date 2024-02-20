<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoicePaymentFactory extends Factory
{
    public function definition()
    {
        return [
            'payment_id' => Payment::factory(),
            'invoice_id' => Invoice::factory(),
            'amount_applied' => rand(1, 500)
        ];
    }

    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'cancelled_at' => Carbon::now(),
            ];
        });
    }

    public function openingbalancepayment()
    {
        return $this->state(function (array $attributes) {
            return [
                'invoice_id' => null,
                'amount_applied' => $this->faker->numberBetween(100, 999)
            ];
        });
    }
}
