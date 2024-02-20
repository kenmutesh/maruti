<?php

namespace Database\Factories;

use App\Enums\PurchaseOrderDocumentsEnum;
use App\Models\PurchaseOrder;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderDocumentFactory extends Factory
{
    public function definition()
    {
        $file = UploadedFile::fake()->image('avatar.pdf');
        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'type' => PurchaseOrderDocumentsEnum::QUOTATION,
            'document_path' => $file->hashName(),
            'document_name' => $file->getClientOriginalName()
        ];
    }

    public function memo()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => PurchaseOrderDocumentsEnum::MEMO,
            ];
        });
    }

    public function invoice()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => PurchaseOrderDocumentsEnum::INVOICE,
            ];
        });
    }

    public function delivery()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => PurchaseOrderDocumentsEnum::DELIVERY,
            ];
        });
    }
}
