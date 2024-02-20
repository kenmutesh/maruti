<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/***
-------------------------------------
      APROTEC SIDE
-------------------------------------
 */
Route::prefix('aprotec')->group(function () {
      Route::get('/', 'AprotecUserController@index')->middleware('aprotecuserauthcheck');

      Route::post('/login', 'AprotecUserController@login')->name('aprotec_login');

      Route::get('/super-user', function () {
            return view('aprotec.super-user');
      });

      Route::middleware(['aprotecuserauth'])->group(function () {
            Route::get('/dashboard', 'AprotecUserController@dashboard');
            Route::post('/companies/create', 'CompanyController@store')->name('add_company');

            Route::put('/companies/edit/{company}', 'CompanyController@update')->name('edit_company');
            Route::delete('/companies/delete/{company}', 'CompanyController@destroy')->name('delete_company');

            Route::get('/profile', 'AprotecUserController@profile');
            Route::get('/logout', 'AprotecUserController@logout');
      });
});

Route::get('/flush-cache', 'Controller@flushCache');

Route::get('/activate', 'CompanyController@activate');
Route::post('/activate', 'CompanyController@activateCompany')->name('activate_company');

Route::get('/', 'UserController@login')->middleware('customuserauthcheck');
Route::get('/set-password/{reset_token}', 'UserController@setPassword')->middleware('customuserauthcheck');
Route::post('/reset-password', 'UserController@resetPassword')->middleware('customuserauthcheck')->name('set_user_password');
Route::post('/forgot-password', 'UserController@forgotPassword')->middleware('customuserauthcheck')->name('forgot_password');
Route::post('/login', 'UserController@loginUser')->name('login_user');

Route::group(['middleware' => 'customuserauth'], function () {
      Route::get('/dashboard','UserController@dashboard');

      Route::get('/logout','UserController@logout');
      
      Route::resource('documentlabels', DocumentLabelController::class);
      
      Route::resource('taxes', TaxController::class);
      
      Route::resource('roles', RoleController::class);
      
      Route::resource('users', UserController::class);
      
      Route::get('/suppliers/agingreport/{date?}', 'SupplierController@agingReport');
      Route::get('/suppliers/creditnotes/purchaseorders/{supplier}', 'SupplierController@getSupplierPurchasesOptions');
      Route::get('/suppliers/purchases/{supplier}', 'SupplierController@getSupplierPurchases');
      Route::get('/suppliers/statements', 'SupplierController@indexStatements');
      Route::get('/suppliers/statements/{supplier}', 'SupplierController@showStatements');
      Route::resource('suppliers', SupplierController::class);
      
      Route::resource('noninventoryitems', NonInventoryItemController::class);
      
      Route::get('/powders/excel', 'PowderController@excelReport')->name('powders.excelreport');
      Route::get('/powders/excel/template', 'PowderController@excelTemplate')->name('powders.exceltemplate');
      Route::post('/powders/excel/template', 'PowderController@excelTemplateUpload')->name('powders.exceltemplate.upload');
      Route::get('/powders/excel/template/edit', 'PowderController@excelEditTemplate')->name('powders.exceltemplate.edit');
      Route::post('/powders/excel/template/edit', 'PowderController@excelEditTemplateUpload')->name('powders.exceltemplate.edit.upload');
      Route::post('/powders/edit/quantity/{powder}', 'PowderController@updateQuantity')->name('powders.update.quantity');
      Route::get('/powders/edit-form/{powder}', 'PowderController@editPowder');
      Route::get('/powders/edit-qty-form/{powder}', 'PowderController@editQuantityPowder');
      Route::post('/powders/show-custom-excel', 'PowderController@showCustomExcel')->name('powders.custom.excel');
      Route::resource('powders', PowderController::class);
      
      Route::get('/inventoryitems/excel', 'InventoryItemController@excelReport')->name('inventoryitems.excelreport');
      Route::get('/inventoryitems/excel/template', 'InventoryItemController@excelTemplate')->name('inventoryitems.exceltemplate');
      Route::post('/inventoryitems/excel/template', 'InventoryItemController@excelTemplateUpload')->name('inventoryitems.exceltemplate.upload');
      Route::get('/inventoryitems/excel/template/edit', 'InventoryItemController@excelEditTemplate')->name('inventoryitems.exceltemplate.edit');
      Route::post('/inventoryitems/excel/template/edit', 'InventoryItemController@excelEditTemplateUpload')->name('inventoryitems.exceltemplate.edit.upload');
      Route::post('/inventoryitems/edit/quantity/{inventoryitem}', 'InventoryItemController@updateQuantity')->name('inventoryitems.update.quantity');
      Route::get('/inventoryitems/edit-form/{inventoryitem}', 'InventoryItemController@editInventoryItem');
      Route::get('/inventoryitems/edit-qty-form/{inventoryitem}', 'InventoryItemController@editQuantityInventoryItem');
      Route::post('/inventoryitems/show-custom-excel', 'InventoryItemController@showCustomExcel')->name('inventoryitems.custom.excel');
      Route::resource('inventoryitems', InventoryItemController::class);
      
      Route::resource('locations', LocationController::class);
      
      Route::resource('warehouses', WarehouseController::class);
      
      Route::resource('floors', FloorController::class);
      
      Route::resource('shelves', ShelfController::class);
      
      Route::resource('bins', BinController::class);
      
      Route::post('/purchaseorders/{purchaseorder}/cancel', 'PurchaseOrderController@cancel')->name('purchaseorders.cancel');
      Route::get('/purchaseorders/{purchaseorder}/complete', 'PurchaseOrderController@completeCreate')->name('purchaseorders.completecreate');
      Route::put('/purchaseorders/{purchaseorder}/complete', 'PurchaseOrderController@complete')->name('purchaseorders.complete');
      Route::get('/purchaseorders/{purchaseorder}/complete/show', 'PurchaseOrderController@completeShow')->name('purchaseorders.completeshow');
      Route::get('/purchaseorders/update-amount-due', 'PurchaseOrderController@updateAmountDue');
      Route::resource('purchaseorders', PurchaseOrderController::class);
      
      Route::get('/customers/agingreport/{date?}', 'CustomerController@agingReport');
      Route::get('/customers/statements', 'CustomerController@indexStatements');
      Route::get('/customers/statements/{customer}', 'CustomerController@showStatements');
      Route::get('/customers/invoices/{customer}', 'CustomerController@getCustomerInvoices');
      Route::get('/customers/creditnotes/invoices/{customer}', 'CustomerController@getCustomerInvoicesOptions');
      Route::resource('customers', CustomerController::class);

      Route::get('/coatingjobs/updateAmounts', 'CoatingJobController@updateAmountsCustom');
      Route::get('/coatingjobs/aged/{number}', 'CoatingJobController@agedOpenCoatingJobs');
      Route::get('/coatingjobs/open/{customer_id}/{coatingjob_id?}', 'CoatingJobController@openCustomerCoatingJobs');
      Route::get('/coatingjobs/quotations', 'CoatingJobController@quotations');
      Route::get('/coatingjobs/quotations/convert/{coatingjob}', 'CoatingJobController@convert');
      Route::get('/coatingjobs/quotations/aged/{number}', 'CoatingJobController@agedOpenQuotations');
      Route::get('/coatingjobs/cancelled', 'CoatingJobController@cancelledJobs')->name('coatingjobs.cancelled');
      Route::get('/coatingjobs/closed', 'CoatingJobController@closedJobs')->name('coatingjobs.closed');
      Route::get('/coatingjobs/unbilled', 'CoatingJobController@unbilledJobs')->name('coatingjobs.unbilled');
      Route::get('/coatingjobs/unbilled/excel', 'CoatingJobController@unbilledJobCardsExcel');
      Route::get('/coatingjobs/sections/{minimum}/{maximum}', 'CoatingJobController@coatingJobSections');
      Route::resource('coatingjobs', CoatingJobController::class);

      Route::get('/invoices/aged/{number}', 'InvoiceController@agedInvoices');
      Route::get('/invoices/direct', 'InvoiceController@createDirectInvoice')->name('invoices.direct');
      Route::post('/invoices/direct', 'InvoiceController@storeDirectInvoice')->name('invoices.direct.store');
      Route::get('/invoices/undo/{invoice}', 'InvoiceController@undoInvoice');
      Route::get('/invoices/external', 'InvoiceController@indexExternal');
      Route::get('/invoices/sections/{minimum}/{maximum}', 'InvoiceController@invoiceSections');
      Route::get('/invoices/update-amount-due', 'InvoiceController@updateAmountDue');
      Route::resource('invoices', InvoiceController::class);
      
      Route::get('/cashsales/aged/{number}', 'CashSaleController@agedCashSales');
      Route::get('/cashsales/direct', 'CashSaleController@createDirectCashSale')->name('cashsales.direct');
      Route::post('/cashsales/direct', 'CashSaleController@storeDirectCashSale')->name('cashsales.direct.store');
      Route::get('/cashsales/undo/{cashsale}', 'CashSaleController@undoCashSale');
      Route::get('/cashsales/external', 'CashSaleController@indexExternal');
      Route::get('/cashsales/sections/{minimum}/{maximum}', 'CashSaleController@cashSaleSections');
      Route::resource('cashsales', CashSaleController::class);

      Route::resource('customercreditnotes', CustomerCreditNoteController::class);

      Route::put('/payments/{payment}/nullify', 'PaymentController@nullify')->name('payments.nullify');
      Route::get('/payments/nullify-form/{payment}', 'PaymentController@nullifyForm');
      Route::get('/payments/delete-form/{payment}', 'PaymentController@deleteForm');
      Route::resource('payments', PaymentController::class);

      Route::resource('suppliercreditnotes', SupplierCreditNoteController::class);

      Route::put('/supplier-payments/{supplierPayment}/nullify', 'SupplierPaymentController@nullify')->name('supplier-payments.nullify');
      Route::get('/supplier-payments/nullify-form/{supplierPayment}', 'SupplierPaymentController@nullifyForm');
      Route::get('/supplier-payments/delete-form/{supplierPayment}', 'SupplierPaymentController@deleteForm');
      Route::resource('supplier-payments', SupplierPaymentController::class);

      Route::get('/email-secrets', 'Controller@emailSecrets');
      Route::post('/email-secrets', 'Controller@updateSecrets');
});
