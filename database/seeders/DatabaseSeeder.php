<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AprotecUserSeeder::class,
            CompanySeeder::class,
            DocumentLabelSeeder::class,
            TaxSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            SupplierSeeder::class,
            LocationSeeder::class,
            WarehouseSeeder::class,
            FloorSeeder::class,
            ShelfSeeder::class,
            BinSeeder::class,
            PowderSeeder::class,
            InventoryItemSeeder::class,
            NonInventoryItemSeeder::class,
            PurchaseOrderSeeder::class,
            PurchaseOrderDocumentSeeder::class,
            PurchaseOrderItemSeeder::class,
            PowderAndInventoryLogSeeder::class,
            CustomerSeeder::class,
            CustomerCCEmailSeeder::class,
            CashSaleSeeder::class,
            InvoiceSeeder::class,
            CoatingJobSeeder::class,
            CoatingJobMarutiItemSeeder::class,
            CoatingJobAluminiumItemSeeder::class,
            CoatingJobSteelItemSeeder::class,
            PaymentSeeder::class,
            InvoicePaymentSeeder::class,
            CustomerCreditNoteSeeder::class,
            CustomerCreditNoteItemSeeder::class
        ]);
    }
}
