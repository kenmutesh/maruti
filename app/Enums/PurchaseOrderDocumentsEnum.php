<?php
namespace App\Enums;

enum PurchaseOrderDocumentsEnum:Int {
    case QUOTATION = 1;
    
    case MEMO = 2;
    
    case INVOICE = 3;
    
    case DELIVERY = 4;

    public function humanreadablestrng(): string
    {
           return match($this) {
               PurchaseOrderDocumentsEnum::QUOTATION => 'Quotation', 
               PurchaseOrderDocumentsEnum::MEMO => 'Memo',
               PurchaseOrderDocumentsEnum::INVOICE => 'Invoice',
               PurchaseOrderDocumentsEnum::DELIVERY => 'Delivery'
           };    
    }
    
}
