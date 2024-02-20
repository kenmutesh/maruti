<?php

namespace Database\Factories;

use App\Models\CashSale;
use App\Models\Company;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CashSaleFactory extends Factory
{
    public function definition()
    {
        $cashSale = new CashSale();
        return [
            'customer_id' => Customer::factory(),
            'discount' => 0,
            'cu_number_prefix' => $cashSale->next_cu_prefix,
            'cu_number_suffix' => $cashSale->next_cu_suffix,
            'company_id' => Company::factory()
        ];
    }

    public function normalcashsale()
    {
        return $this->state(function (array $attributes) {
            $cashSale = new CashSale();
            return [
                'cash_sale_prefix' => $cashSale->next_cash_sale_prefix,
                'cash_sale_suffix' => $cashSale->next_cash_sale_suffix,
            ];
        });
    }

    public function extcashsale()
    {
        return $this->state(function (array $attributes) {
            $cashSale = new CashSale();
            return [
                'ext_cash_sale_prefix' => $cashSale->next_ext_cash_sale_prefix,
                'ext_cash_sale_suffix' => $cashSale->next_ext_cash_sale_suffix,
                'external' => true
            ];
        });
    }

    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'cancelled_at' => Carbon::now(),
            ];
        });
    }
}
