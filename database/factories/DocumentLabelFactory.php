<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentLabelFactory extends Factory
{
    public function definition()
    {
        return [
            'document_prefix' => 'ABC',
            'document_suffix' => 1000,
            'company_id' => Company::factory()
        ];
    }
}
