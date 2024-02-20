<?php

namespace App\Http\Controllers;

use App\Enums\InventoryItemsEnum;
use App\Enums\PowderAndInventoryLogsEnum;
use App\Models\Bin;
use App\Models\InventoryItem;
use App\Models\PowderAndInventoryLog;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ExcelReader;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class InventoryItemController extends Controller
{
  public function index(Request $request)
  {
    $this->authorize('viewAny', InventoryItem::class);
    $inventoryItems = Cache::remember('inventory_items', (60 * 2), function () {
      return InventoryItem::orderBy('id', 'desc')->with(['supplier'])->get();
    });
    if ($request->is('api/*')) {
      return $inventoryItems;
    } else {
      $suppliers =  Cache::remember('supplier_list_inventory_item', (60 * 5), function () {
        return Supplier::all();
      });
      $inventoryItemTypes = InventoryItemsEnum::cases();
      $bins = Cache::remember('bin_list_inventory_item', (60 * 5), function () {
        return Bin::with(['shelf.floor.warehouse.location'])->get();
      });
      return view('system.inventory.index', [
        'inventoryItems' => $inventoryItems,
        'suppliers' => $suppliers,
        'inventoryItemTypes' => $inventoryItemTypes,
        'bins' => $bins
      ]);
    }
  }

  public function create()
  {
    //
  }

  public function store(Request $request)
  {
    $request->validate([
      'item_name' => ['required'],
      'item_code' => ['required'],
      'item_description' => ['required'],
      'serial_no' => ['required'],
      'quantity_tag' => ['required'],
      'type' => ['required'],
      'goods_weight' => ['required'],
      'standard_cost' => ['required'],
      'standard_cost_vat' => ['required'],
      'standard_price' => ['required'],
      'standard_price_vat' => ['required'],
      'min_threshold' => ['required'],
      'max_threshold' => ['required'],
      'opening_quantity' => ['required'],
    ]);

    $inventoryItem = new InventoryItem();

    $inventoryItem->fill([
      'item_name' => strtoupper($request->item_name),
      'item_code' => strtoupper($request->item_code),
      'item_description' => strtoupper($request->item_description),
      'serial_no' => strtoupper($request->serial_no),
      'quantity_tag' => strtoupper($request->quantity_tag),
      'type' => $request->type,
      'goods_weight' => $request->goods_weight,
      'standard_cost' => $request->standard_cost,
      'standard_cost_vat' => $request->standard_cost_vat,
      'standard_price' => $request->standard_price,
      'standard_price_vat' => $request->standard_price_vat,
      'min_threshold' => $request->min_threshold,
      'max_threshold' => $request->max_threshold,
      'opening_quantity' => $request->opening_quantity,
      'current_quantity' => $request->opening_quantity,
      'supplier_id' => $request->supplier_id,
      'company_id' => auth()->user()->company_id
    ]);

    if ($inventoryItem->save()) {
      $bin = Bin::find($request->bin_id);

      $inventoryLog = new PowderAndInventoryLog();

      $inventoryLog->fill([
        'reason' => PowderAndInventoryLogsEnum::CREATING,
        'reason_id' => $inventoryItem->id,
        'sum_added' => $inventoryItem->current_quantity,
        'inventory_item_id' => $inventoryItem->id,
        'warehouse_id' => $bin->shelf->floor->warehouse_id,
        'floor_id' => $bin->shelf->floor_id,
        'shelf_id' => $bin->shelf_id,
        'bin_id' => $bin->id,
        'company_id' => auth()->user()->company_id
      ]);

      $inventoryLog->saveQuietly();
      if ($request->is('api/*')) {
        return $inventoryItem;
      } else {
        return back()->with('Success', 'Created successfully');
      }
    } else {
      return back()->with('Error', 'Failed to create. Please retry');
    }
  }

  public function show(Request $request, InventoryItem $inventoryitem)
  {
    $date = Carbon::now()->format('Y-m-d');
    if (isset($request->date)) {
      $date = Carbon::parse($request->date)->format('Y-m-d');
    }

    $logSumQuantity = PowderAndInventoryLog::where([
      ['inventory_item_id', '=', $inventoryitem->id],
      ['reason', '!=', PowderAndInventoryLogsEnum::CREATING->value]
    ])->whereDate('created_at', '<', $date)->sum('sum_added');

    $currentTransactions = PowderAndInventoryLog::where([
      ['inventory_item_id', '=', $inventoryitem->id],
      ['reason', '!=', PowderAndInventoryLogsEnum::CREATING->value]
    ])->whereDate('created_at', '=', $date)->get();

    $openingQuantity = $inventoryitem->opening_quantity + $logSumQuantity;

    return view('system.inventory.breakdown', [
      'inventoryitem' => $inventoryitem,
      'logSumQuantity' => $logSumQuantity,
      'date' => $date,
      'currentTransactions' => $currentTransactions,
      'openingQuantity' => $openingQuantity
    ]);
  }

  public function showCustomExcel(Request $request)
  {
    $this->authorize('viewAny', InventoryItem::class);

    $request->validate([
      'inventory_date' => ['required'],
    ]);

    $date = Carbon::parse($request->inventory_date)->format('Y-m-d');

    $inventoryItemsCollection = collect(InventoryItem::select('id', 'item_name', 'type', 'goods_weight', 'opening_quantity', 'supplier_id')->with(['supplier'])->get()->toArray());

    $inventoryItems = $inventoryItemsCollection->groupBy('type')->all();

    $spreadsheet = new Spreadsheet();

    $sheet = 0;

    foreach ($inventoryItems as $groupType => $items) {
      $workSheet = new Worksheet($spreadsheet, InventoryItemsEnum::from($groupType)->humanreadablestring());
      $workSheet->setCellValue('A1', strtoupper(InventoryItemsEnum::from($groupType)->humanreadablestring()) . ' REPORT');
      $workSheet->setCellValue('A2', 'CLOSE OF ' . $date);
      $cellRowNumber = 3;
      foreach ($items as $item) {
        $inventoryItem = json_decode(json_encode($item));

        $logSumQuantity = PowderAndInventoryLog::where([
          ['inventory_item_id', '=', $inventoryItem->id],
          ['reason', '!=', PowderAndInventoryLogsEnum::CREATING->value]
        ])->whereDate('created_at', '<=', $date)->sum('sum_added');

        $closingQuantity = $inventoryItem->opening_quantity + $logSumQuantity;

        $workSheet->setCellValue('A' . $cellRowNumber, $inventoryItem->item_name);
        $workSheet->setCellValue('B' . $cellRowNumber, $inventoryItem->goods_weight);
        $workSheet->setCellValue('C' . $cellRowNumber, $closingQuantity);
        $workSheet->setCellValue('D' . $cellRowNumber, $inventoryItem->supplier->supplier_name ?? 'N/A');
        $cellRowNumber += 1;
      }
      
      $workSheet->getColumnDimension('A')->setAutoSize(true);
      $workSheet->getColumnDimension('B')->setAutoSize(true);
      $workSheet->getColumnDimension('C')->setAutoSize(true);
      $workSheet->getColumnDimension('D')->setAutoSize(true);

      $spreadsheet->addSheet($workSheet, $sheet);
      $sheet += 1;
    }



    $writer = new Xlsx($spreadsheet);

    $extension = 'xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"inventory-{$date}.{$extension}\"");
    $writer->save('php://output');
    exit();
  }

  public function edit(InventoryItem $inventoryItem)
  {
    //
  }

  public function update(Request $request, InventoryItem $inventoryitem)
  {
    $request->validate([
      'item_name' => ['required'],
      'item_code' => ['required'],
      'item_description' => ['required'],
      'serial_no' => ['required'],
      'quantity_tag' => ['required'],
      'type' => ['required'],
      'goods_weight' => ['required'],
      'standard_cost' => ['required'],
      'standard_cost_vat' => ['required'],
      'standard_price' => ['required'],
      'standard_price_vat' => ['required'],
      'min_threshold' => ['required'],
      'max_threshold' => ['required'],
    ]);

    $inventoryitem->fill([
      'item_name' => strtoupper($request->item_name),
      'item_code' => strtoupper($request->item_code),
      'item_description' => strtoupper($request->item_description),
      'serial_no' => strtoupper($request->serial_no),
      'quantity_tag' => strtoupper($request->quantity_tag),
      'type' => $request->type,
      'goods_weight' => $request->goods_weight,
      'standard_cost' => $request->standard_cost,
      'standard_cost_vat' => $request->standard_cost_vat,
      'standard_price' => $request->standard_price,
      'standard_price_vat' => $request->standard_price_vat,
      'min_threshold' => $request->min_threshold,
      'max_threshold' => $request->max_threshold,
      'supplier_id' => $request->supplier_id,
      'company_id' => auth()->user()->company_id
    ]);

    if ($inventoryitem->update()) {
      if ($request->is('api/*')) {
        return $inventoryitem;
      } else {
        return back()->with('Success', 'Edited successfully');
      }
    } else {
      return back()->with('Error', 'Failed to edit. Please retry');
    }
  }

  public function updateQuantity(Request $request, InventoryItem $inventoryitem)
  {
    $request->validate([
      'quantity' => ['required']
    ]);

    $this->authorize('update', InventoryItem::class);

    $lastInventoryLog = PowderAndInventoryLog::where([
      ['reason', '=', PowderAndInventoryLogsEnum::CREATING->value],
      ['inventory_item_id', '=', $inventoryitem->id]
    ])->first();

    $logAddition = $request->quantity - $inventoryitem->current_quantity;

    $inventoryLog = new PowderAndInventoryLog();

    if ($lastInventoryLog) {
      $inventoryLog->fill([
        'reason' => PowderAndInventoryLogsEnum::MANUALADJUSMENT,
        'sum_added' => $logAddition,
        'inventory_item_id' => $inventoryitem->id,
        'warehouse_id' => $lastInventoryLog->warehouse_id,
        'floor_id' => $lastInventoryLog->floor_id,
        'shelf_id' => $lastInventoryLog->shelf_id,
        'bin_id' => $lastInventoryLog->bin_id,
        'company_id' => auth()->user()->company_id
      ]);
    } else {
      $bin = Bin::orderBy('id', 'desc')->first();
      $inventoryLog->fill([
        'reason' => PowderAndInventoryLogsEnum::MANUALADJUSMENT,
        'sum_added' => $logAddition,
        'inventory_item_id' => $inventoryitem->id,
        'warehouse_id' => $bin->shelf->floor->warehouse_id,
        'floor_id' => $bin->shelf->floor_id,
        'shelf_id' => $bin->shelf_id,
        'bin_id' => $bin->id,
        'company_id' => auth()->user()->company_id
      ]);
    }

    if ($inventoryLog->save()) {
      Cache::forget('inventory_items');
      return back()->with('Success', 'Updated successfully');
    } else {
      return back()->with('Error', 'Failed to update. Please retry');
    }
  }

  public function destroy(InventoryItem $inventoryitem)
  {
    if ($inventoryitem->delete()) {
      return back()->with('Success', 'Deleted successfully');
    } else {
      return back()->with('Error', 'Failed to delete. Please retry');
    }
  }

  public function excelReport()
  {
    $inventoryItemsCollection = collect(InventoryItem::with(['supplier'])->get()->toArray());

    $inventoryItems = $inventoryItemsCollection->groupBy('type')->all();

    $spreadsheet = new Spreadsheet();

    $sheet = 0;

    foreach ($inventoryItems as $groupType => $items) {
      $workSheet = new Worksheet($spreadsheet, InventoryItemsEnum::from($groupType)->humanreadablestring());
      $workSheet->setCellValue('A1', strtoupper(InventoryItemsEnum::from($groupType)->humanreadablestring()) . ' REPORT');
      $workSheet->setCellValue('A2', date('y/m/d', time()));
      $cellRowNumber = 3;
      foreach ($items as $item) {
        $inventoryItem = json_decode(json_encode($item));
        $workSheet->setCellValue('A' . $cellRowNumber, $inventoryItem->item_name);
        $workSheet->setCellValue('B' . $cellRowNumber, $inventoryItem->goods_weight);
        $workSheet->setCellValue('C' . $cellRowNumber, $inventoryItem->current_quantity);
        $workSheet->setCellValue('D' . $cellRowNumber, $inventoryItem->supplier->supplier_name ?? 'N/A');
        $cellRowNumber += 1;
      }
      $spreadsheet->addSheet($workSheet, $sheet);
      $sheet += 1;
    }

    $writer = new Xlsx($spreadsheet);

    $extension = 'xlsx';
    $date = date('d/m/Y', time());
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"inventory-{$date}.{$extension}\"");
    $writer->save('php://output');
    exit();
  }

  public function excelTemplate()
  {
    $spreadsheet = new Spreadsheet();

    $sheet = $spreadsheet->getActiveSheet();

    // the titles
    $sheet->setCellValue('A1', 'TYPE');
    $sheet->setCellValue('B1', 'ITEM NAME');
    $sheet->setCellValue('C1', 'ITEM CODE');
    $sheet->setCellValue('D1', 'SERIAL NO');
    $sheet->setCellValue('E1', 'QUANTITY TAG');
    $sheet->setCellValue('F1', 'GOODS WEIGHT');
    $sheet->setCellValue('G1', 'OPENING QUANTITY');
    $sheet->setCellValue('H1', 'MIN THRESHOLD');
    $sheet->setCellValue('I1', 'MAX THRESHOLD');
    $sheet->setCellValue('J1', 'UNIT COST');
    $sheet->setCellValue('K1', 'UNIT COST VAT');
    $sheet->setCellValue('L1', 'PRICE');
    $sheet->setCellValue('M1', 'PRICE VAT');
    $sheet->setCellValue('N1', 'STORAGE SECTION');
    $sheet->setCellValue('O1', 'SUPPLIER');

    $systemDataWorkSheet = new Worksheet($spreadsheet, 'Data');

    $suppliers = Supplier::all();

    $supplierCount = count($suppliers);
    for ($i = 0; $i < $supplierCount; $i++) {
      $systemDataWorkSheet->setCellValue('A' . $i + 1, $suppliers[$i]->id . '-' . $suppliers[$i]->supplier_name);
    }

    $bins = Bin::with(['shelf.floor.warehouse.location'])->get();

    $binsCount = count($bins);
    for ($i = 0; $i < $binsCount; $i++) {
      $binString =  $bins[$i]->id . '-' . $bins[$i]->bin_name . ':(' . $bins[$i]->shelf->shelf_name . '->' . $bins[$i]->shelf->floor->floor_name . '->' . $bins[$i]->shelf->floor->warehouse->warehouse_name . ')';
      $systemDataWorkSheet->setCellValue('B' . $i + 1, $binString);
    }

    $inventoryItemsEnum = InventoryItemsEnum::cases();
    $inventoryItemsEnumCount = count($inventoryItemsEnum);
    for ($i = 0; $i < $inventoryItemsEnumCount; $i++) {
      $systemDataWorkSheet->setCellValue('C' . $i + 1, $inventoryItemsEnum[$i]->value . "-" . $inventoryItemsEnum[$i]->humanreadablestring());
    }

    for ($i = 2; $i < 400; $i++) {

      $inventoryItemValidation = $sheet->getCell('A' . $i)->getDataValidation();
      $inventoryItemValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
        ->setAllowBlank(false)
        ->setShowDropDown(true)
        ->setShowErrorMessage(true)
        ->setShowInputMessage(true)
        ->setErrorTitle('Input error')
        ->setError('Value is not in list.')
        ->setPromptTitle('Pick from list')
        ->setPrompt('Choose the type')
        ->setFormula1('=Data!$C$1:$C$' . $inventoryItemsEnumCount . '');

      $binValidation = $sheet->getCell('N' . $i)->getDataValidation();
      $binValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
        ->setAllowBlank(false)
        ->setShowDropDown(true)
        ->setShowErrorMessage(true)
        ->setShowInputMessage(true)
        ->setErrorTitle('Input error')
        ->setError('Value is not in list.')
        ->setPromptTitle('Pick from list')
        ->setPrompt('Choose the ection')
        ->setFormula1('=Data!$B$1:$B$' . $binsCount . '');

      $supplierValidation = $sheet->getCell('O' . $i)->getDataValidation();
      $supplierValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
        ->setAllowBlank(false)
        ->setShowDropDown(true)
        ->setShowErrorMessage(true)
        ->setShowInputMessage(true)
        ->setErrorTitle('Input error')
        ->setError('Value is not in list.')
        ->setPromptTitle('Pick from list')
        ->setPrompt('Choose the supplier')
        ->setFormula1('=Data!$A$1:$A$' . $supplierCount . '');
    }

    $spreadsheet->getSheet(0);

    $spreadsheet->addSheet($systemDataWorkSheet, 1);

    $writer = new Xlsx($spreadsheet);

    $extension = 'xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"inventory-template.{$extension}\"");
    $writer->save('php://output');
    exit();
  }

  public function excelTemplateUpload(Request $request)
  {
    if ($request->file('inventory_excel_file') != NULL) {
      $file = $request->file('inventory_excel_file');

      if ($file->extension() != 'xlsx') {
        return back()->with('Error', 'You need to have placed an excel file');
      } else {
        $reader = new ExcelReader();

        $spreadsheet = $reader->load($file->getPathName());

        $data = $spreadsheet->getSheet(0)->toArray();

        $titleRow = $data[0];

        $valid = true;

        if ($titleRow[0] != 'TYPE') {
          $valid = false;
        } elseif ($titleRow[1] != 'ITEM NAME') {
          $valid = false;
        } elseif ($titleRow[2] != 'ITEM CODE') {
          $valid = false;
        } elseif ($titleRow[3] != 'SERIAL NO') {
          $valid = false;
        } elseif ($titleRow[4] != 'QUANTITY TAG') {
          $valid = false;
        } elseif ($titleRow[5] != 'GOODS WEIGHT') {
          $valid = false;
        } elseif ($titleRow[6] != 'OPENING QUANTITY') {
          $valid = false;
        } elseif ($titleRow[7] != 'MIN THRESHOLD') {
          $valid = false;
        } elseif ($titleRow[8] != 'MAX THRESHOLD') {
          $valid = false;
        } elseif ($titleRow[9] != 'UNIT COST') {
          $valid = false;
        } elseif ($titleRow[10] != 'UNIT COST VAT') {
          $valid = false;
        } elseif ($titleRow[11] != 'PRICE') {
          $valid = false;
        } elseif ($titleRow[12] != 'PRICE VAT') {
          $valid = false;
        } elseif ($titleRow[13] != 'STORAGE SECTION') {
          $valid = false;
        } elseif ($titleRow[14] != 'SUPPLIER') {
          $valid = false;
        }

        if (!$valid) {
          return back()->with('Error', 'Wrong excel template file');
        }

        for ($i = 1; $i < count($data); $i++) {
          if ($data[$i][0] == NULL) {
            continue;
          }
          $inventoryItem = new InventoryItem();

          $inventoryItem->fill([
            'type' => explode("-", $data[$i][0])[0],
            'item_name' => strtoupper($data[$i][1]),
            'item_code' => strtoupper($data[$i][2]),
            'item_description' => strtoupper($data[$i][1]),
            'serial_no' => strtoupper($data[$i][3]),
            'quantity_tag' => strtoupper($data[$i][4]),
            'goods_weight' => $data[$i][5],
            'standard_cost' => $data[$i][9],
            'standard_cost_vat' => $data[$i][10],
            'standard_price' => $data[$i][11],
            'standard_price_vat' => $data[$i][12],
            'min_threshold' => $data[$i][7],
            'max_threshold' => $data[$i][8],
            'opening_quantity' => $data[$i][6],
            'current_quantity' => $data[$i][6],
            'supplier_id' => explode("-", $data[$i][14])[0],
            'company_id' => auth()->user()->company_id
          ]);

          if ($inventoryItem->save()) {
            $bin = Bin::find(explode("-", $data[$i][13])[0]);

            $inventoryLog = new PowderAndInventoryLog();

            $inventoryLog->fill([
              'reason' => PowderAndInventoryLogsEnum::CREATING,
              'reason_id' => $inventoryItem->id,
              'sum_added' => $inventoryItem->current_quantity,
              'inventory_item_id' => $inventoryItem->id,
              'warehouse_id' => $bin->shelf->floor->warehouse_id,
              'floor_id' => $bin->shelf->floor_id,
              'shelf_id' => $bin->shelf_id,
              'bin_id' => $bin->id,
              'company_id' => auth()->user()->company_id
            ]);

            $inventoryLog->saveQuietly();
          }
        }

        return back()->with('Success', 'Inventory template items added!');
      }
    } else {
      return back()->with('Error', 'You need to have placed an excel file');
    }
  }

  public function editInventoryItem(Request $request, InventoryItem $inventoryitem)
  {
    $suppliers =  Cache::remember('supplier_list_inventory_item', (60 * 5), function () {
      return Supplier::all();
    });
    $inventoryItemTypes = InventoryItemsEnum::cases();
    $bins = Cache::remember('bin_list_inventory_item', (60 * 5), function () {
      return Bin::with(['shelf.floor.warehouse.location'])->get();
    });
    return view('system.inventory.misc.edit-form', [
      'inventoryItem' => $inventoryitem,
      'suppliers' => $suppliers,
      'inventoryItemTypes' => $inventoryItemTypes,
      'bins' => $bins
    ]);
  }

  public function editQuantityInventoryItem(Request $request, InventoryItem $inventoryitem)
  {
    return view('system.inventory.misc.edit-qty-form', [
      'inventoryItem' => $inventoryitem,
    ]);
  }

  public function excelEditTemplate()
  {
    $spreadsheet = new Spreadsheet();

    $sheet = $spreadsheet->getActiveSheet();

    // the titles
    $sheet->setCellValue('A1', 'SUPPLIER');
    $sheet->setCellValue('B1', 'TYPE');
    $sheet->setCellValue('C1', 'INVENTORY ITEM');
    $sheet->setCellValue('D1', 'CURRENT QUANTITY');
    $sheet->setCellValue('E1', 'NEW QUANTITY');

    $inventoryItems = InventoryItem::select('id', 'type', 'item_name', 'current_quantity', 'supplier_id')->with(['supplier:id,supplier_name'])->orderBy('type', 'desc')->orderBy('supplier_id', 'asc')->get();

    $rowNumber = 2;
    foreach ($inventoryItems as $inventoryItem) {
      if ($inventoryItem->supplier_id === null) {
        $sheet->setCellValue('A' . $rowNumber, null);
      } else {
        $sheet->setCellValue('A' . $rowNumber, $inventoryItem->supplier_id . '-' . $inventoryItem->supplier->supplier_name);
      }
      $sheet->setCellValue('B' . $rowNumber, $inventoryItem->type->humanreadablestring());
      $sheet->setCellValue('C' . $rowNumber, $inventoryItem->id . '-' . $inventoryItem->item_name);
      $sheet->setCellValue('D' . $rowNumber, $inventoryItem->current_quantity);
      $rowNumber += 1;
    }
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

    $sheet->getProtection()->setPassword('xlvisben');
    $sheet->getProtection()->setSheet(true);
    $sheet->getStyle('E1:E' . $rowNumber)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);


    $writer = new Xlsx($spreadsheet);

    $extension = 'xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"inventory-items-edit-template.{$extension}\"");
    $writer->save('php://output');
    exit();
  }

  public function excelEditTemplateUpload(Request $request)
  {
    $this->authorize('update', InventoryItem::class);

    if ($request->file('inventory_excel_file') != NULL) {
      $file = $request->file('inventory_excel_file');

      if ($file->extension() != 'xlsx') {
        return back()->with('Error', 'You need to have placed an excel file');
      } else {
        $reader = new ExcelReader();

        $spreadsheet = $reader->load($file->getPathName());

        $data = $spreadsheet->getSheet(0)->toArray();

        $titleRow = $data[0];

        $valid = true;

        if ($titleRow[0] != 'SUPPLIER') {
          $valid = false;
        } elseif ($titleRow[1] != 'TYPE') {
          $valid = false;
        } elseif ($titleRow[2] != 'INVENTORY ITEM') {
          $valid = false;
        } elseif ($titleRow[3] != 'CURRENT QUANTITY') {
          $valid = false;
        } elseif ($titleRow[4] != 'NEW QUANTITY') {
          $valid = false;
        }

        if (!$valid) {
          return back()->with('Error', 'Wrong excel template file');
        }

        for ($i = 1; $i < count($data); $i++) {
          if ($data[$i][4] === NULL || $data[$i][4] === "") {
            continue;
          }
          $inventoryItemParts = explode('-', $data[$i][2]);

          $inventoryitem = InventoryItem::find($inventoryItemParts[0]);

          $lastInventoryLog = PowderAndInventoryLog::where([
            ['reason', '=', PowderAndInventoryLogsEnum::CREATING->value],
            ['inventory_item_id', '=', $inventoryitem->id]
          ])->first();

          $logAddition = $data[$i][4] - $inventoryitem->current_quantity;

          $inventoryLog = new PowderAndInventoryLog();

          if ($lastInventoryLog) {
            $inventoryLog->fill([
              'reason' => PowderAndInventoryLogsEnum::MANUALADJUSMENT,
              'sum_added' => $logAddition,
              'inventory_item_id' => $inventoryitem->id,
              'warehouse_id' => $lastInventoryLog->warehouse_id,
              'floor_id' => $lastInventoryLog->floor_id,
              'shelf_id' => $lastInventoryLog->shelf_id,
              'bin_id' => $lastInventoryLog->bin_id,
              'company_id' => auth()->user()->company_id
            ]);
          } else {
            $bin = Bin::orderBy('id', 'desc')->first();
            $inventoryLog->fill([
              'reason' => PowderAndInventoryLogsEnum::MANUALADJUSMENT,
              'sum_added' => $logAddition,
              'inventory_item_id' => $inventoryitem->id,
              'warehouse_id' => $bin->shelf->floor->warehouse_id,
              'floor_id' => $bin->shelf->floor_id,
              'shelf_id' => $bin->shelf_id,
              'bin_id' => $bin->id,
              'company_id' => auth()->user()->company_id
            ]);
          }

          $inventoryLog->save();
        }

        Cache::forget('inventory_items');

        return back()->with('Success', 'Inventory Items template items added!');
      }
    } else {
      return back()->with('Error', 'You need to have placed an excel file');
    }
  }
}
