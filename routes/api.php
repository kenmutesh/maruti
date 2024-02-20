<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'customuserauth'], function () {
    Route::resource('documentlabels', DocumentLabelController::class);

    Route::resource('taxes', TaxController::class);
    
    Route::resource('users', UserController::class);
    
    Route::resource('suppliers', SupplierController::class);
    
    Route::resource('noninventoryitems', SupplierController::class);
    
    Route::resource('powders', PowderController::class);
    
    Route::resource('inventoryitems', InventoryItemController::class);
    
    Route::resource('purchaseorders', PurchaseOrderController::class);
    
    Route::resource('locations', LocationController::class);
    
    Route::resource('warehouses', WarehouseController::class);
    
    Route::resource('floors', FloorController::class);
    
    Route::resource('shelves', ShelfController::class);
    
    Route::resource('bins', BinController::class);
    
    Route::resource('customers', CustomerController::class);
    
    Route::resource('coatingjobs', CoatingJobController::class);

    Route::resource('cashsales', CashSaleController::class);
    
    Route::resource('invoices', InvoiceController::class);
});
