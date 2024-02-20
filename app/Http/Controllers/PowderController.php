<?php

namespace App\Http\Controllers;

use App\Enums\PowderAndInventoryLogsEnum;
use App\Models\Bin;
use App\Models\Powder;
use App\Models\PowderAndInventoryLog;
use Illuminate\Http\Request;

use App\Models\Supplier;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ExcelReader;
use PhpOffice\PhpSpreadsheet\Style\Protection;

use Illuminate\Support\Facades\Cache;

use Carbon\Carbon;

class PowderController extends Controller
{

  public function index(Request $request)
  {
    $this->authorize('viewAny', Powder::class);
    $powders = Cache::remember('powder_items', (60 * 2), function () {
      return Powder::orderBy('id', 'desc')->with(['supplier'])->get();
    });
    if ($request->is('api/*')) {
      return $powders;
    } else {
      $suppliers =  Cache::remember('supplier_list_powder', (60 * 5), function () {
        return Supplier::all();
      });
      $bins = Cache::remember('bin_list_inventory_item', (60 * 5), function () {
        return Bin::with(['shelf.floor.warehouse.location'])->get();
      });
      return view('system.powder.index', [
        'powders' => $powders,
        'suppliers' => $suppliers,
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
      'powder_color' => ['required'],
      'powder_code' => ['required'],
      'powder_description' => ['required'],
      'serial_no' => ['required'],
      'manufacture_date' => ['required'],
      'expiry_date' => ['required'],
      'goods_weight' => ['required'],
      'batch_no' => ['required'],
      'standard_cost' => ['required'],
      'standard_cost_vat' => ['required'],
      'standard_price' => ['required'],
      'standard_price_vat' => ['required'],
      'min_threshold' => ['required'],
      'max_threshold' => ['required'],
      'opening_weight' => ['required'],
      'supplier_id' => ['required'],
    ]);

    $powder = new Powder();

    $powder->fill([
      'powder_color' => strtoupper($request->powder_color),
      'powder_code' => strtoupper($request->powder_code),
      'powder_description' => strtoupper($request->powder_description),
      'serial_no' => strtoupper($request->serial_no),
      'manufacture_date' => $request->manufacture_date,
      'expiry_date' => $request->expiry_date,
      'goods_weight' => $request->goods_weight,
      'batch_no' => strtoupper($request->batch_no),
      'standard_cost' => $request->standard_cost,
      'standard_cost_vat' => $request->standard_cost_vat,
      'standard_price' => $request->standard_price,
      'standard_price_vat' => $request->standard_price_vat,
      'min_threshold' => $request->min_threshold,
      'max_threshold' => $request->max_threshold,
      'current_weight' => $request->opening_weight,
      'opening_weight' => $request->opening_weight,
      'supplier_id' => $request->supplier_id,
      'company_id' => auth()->user()->company_id
    ]);

    if ($powder->save()) {
      $bin = Bin::find($request->bin_id);

      $powderLog = new PowderAndInventoryLog();

      $powderLog->fill([
        'reason' => PowderAndInventoryLogsEnum::CREATING,
        'reason_id' => $powder->id,
        'sum_added' => $powder->current_weight,
        'powder_id' => $powder->id,
        'warehouse_id' => $bin->shelf->floor->warehouse_id,
        'floor_id' => $bin->shelf->floor_id,
        'shelf_id' => $bin->shelf_id,
        'bin_id' => $bin->id,
        'company_id' => auth()->user()->company_id
      ]);

      $powderLog->saveQuietly();

      if ($request->is('api/*')) {
        return $powder;
      } else {
        return back()->with('Success', 'Created successfully');
      }
    } else {
      return back()->with('Error', 'Failed to create. Please retry');
    }
  }

  public function show(Request $request, Powder $powder)
  {
    $date = Carbon::now()->format('Y-m-d');
    if (isset($request->date)) {
      $date = Carbon::parse($request->date)->format('Y-m-d');
    }

    $logSumWeight = PowderAndInventoryLog::where([
      ['powder_id', '=', $powder->id],
      ['reason', '!=', PowderAndInventoryLogsEnum::CREATING->value]
    ])->whereDate('created_at', '<', $date)->sum('sum_added');

    $currentTransactions = PowderAndInventoryLog::where([
      ['powder_id', '=', $powder->id],
      ['reason', '!=', PowderAndInventoryLogsEnum::CREATING->value]
    ])->whereDate('created_at', '=', $date)->get();

    $openingWeight = $powder->opening_weight + $logSumWeight;

    return view('system.powder.breakdown', [
      'powder' => $powder,
      'logSumWeight' => $logSumWeight,
      'date' => $date,
      'currentTransactions' => $currentTransactions,
      'openingWeight' => $openingWeight
    ]);
  }

  public function showCustomExcel(Request $request)
  {
    $this->authorize('viewAny', Powder::class);

    $request->validate([
      'inventory_date' => ['required'],
    ]);

    $date = Carbon::parse($request->inventory_date)->format('Y-m-d');

    $suppliers = Supplier::select('id', 'supplier_name')->with(['powders:id,powder_color,supplier_id'])->get();

    $spreadsheet = new Spreadsheet();

    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'POWDER REPORT');
    $sheet->setCellValue('A2', 'Close of ' . $date);

    $cellRowNumber = 3;
    foreach ($suppliers as $supplier) {
      if (count($supplier->powders) > 0) {
        $cellRowNumber += 1;
        $sheet->setCellValue('A' . $cellRowNumber, $supplier->supplier_name);
        $totalPowderWeight = 0;
        $cellRowNumber += 1;
        foreach ($supplier->powders as $powder) {
          $logSumWeight = PowderAndInventoryLog::where([
            ['powder_id', '=', $powder->id],
            ['reason', '!=', PowderAndInventoryLogsEnum::CREATING->value]
          ])->whereDate('created_at', '<', $date)->sum('sum_added');

          $closingWeight = $powder->opening_quantity + $logSumWeight;
          $sheet->setCellValue('A' . $cellRowNumber, $powder->powder_color);
          $sheet->setCellValue('B' . $cellRowNumber, number_format($closingWeight, 2));
          $totalPowderWeight += floatval($closingWeight);
          $cellRowNumber += 1;
        }
        $sheet->setCellValue('A' . $cellRowNumber, "TOTAL WEIGHT");
        $sheet->setCellValue('B' . $cellRowNumber, $totalPowderWeight);
        $cellRowNumber += 1;
      }
    }
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $spreadsheet->getSheet(0);

    $writer = new Xlsx($spreadsheet);

    $extension = 'xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"powder-inventory-{$date}.{$extension}\"");
    $writer->save('php://output');
    exit();
  }

  public function edit(Request $request)
  {
    $Powder = Powder::find($request->powder_id);

    $Powder->powder_color = strtoupper($request->powder_color);

    $Powder->powder_code = strtoupper($request->powder_code);

    $Powder->powder_description = strtoupper($request->powder_description) ?? '';

    $Powder->serial_no = $request->serial_no;

    $Powder->manufacture_date = $request->manufacture_date;

    $Powder->expiry_date = $request->expiry_date;

    $Powder->goods_weight = $request->goods_weight;

    $Powder->batch_no = $request->batch_no;

    $Powder->standard_cost = $request->standard_cost;

    $Powder->item_price = $request->item_price;

    $Powder->tax = $request->tax;

    $Powder->taxed_price = $request->taxed_price;

    $Powder->min_threshold = $request->min_threshold;

    $Powder->max_threshold = $request->max_threshold;

    $Powder->update_by = session()->get('auth_user_uid');

    $Powder->last_update = date('Y-m-d H:i:s', time());

    if ($Powder->save()) {
      return back()->with('Error', 'Powder item details updated successfully');
    } else {
      return back()->with('Error', 'Failed to update powder item please retry');
    }
  }

  public function update(Request $request, Powder $powder)
  {
    $request->validate([
      'powder_color' => ['required'],
      'powder_code' => ['required'],
      'powder_description' => ['required'],
      'serial_no' => ['required'],
      'manufacture_date' => ['required'],
      'expiry_date' => ['required'],
      'goods_weight' => ['required'],
      'batch_no' => ['required'],
      'standard_cost' => ['required'],
      'standard_cost_vat' => ['required'],
      'standard_price' => ['required'],
      'standard_price_vat' => ['required'],
      'min_threshold' => ['required'],
      'max_threshold' => ['required'],
      'supplier_id' => ['required'],
    ]);

    $powder->fill([
      'powder_color' => strtoupper($request->powder_color),
      'powder_code' => strtoupper($request->powder_code),
      'powder_description' => strtoupper($request->powder_description),
      'serial_no' => strtoupper($request->serial_no),
      'manufacture_date' => $request->manufacture_date,
      'expiry_date' => $request->expiry_date,
      'goods_weight' => $request->goods_weight,
      'batch_no' => strtoupper($request->batch_no),
      'standard_cost' => $request->standard_cost,
      'standard_cost_vat' => $request->standard_cost_vat,
      'standard_price' => $request->standard_price,
      'standard_price_vat' => $request->standard_price_vat,
      'min_threshold' => $request->min_threshold,
      'max_threshold' => $request->max_threshold,
      'supplier_id' => $request->supplier_id,
      'company_id' => auth()->user()->company_id
    ]);

    if ($powder->save()) {
      if ($request->is('api/*')) {
        return $powder;
      } else {
        return back()->with('Success', 'Edited successfully');
      }
    } else {
      return back()->with('Error', 'Failed to edit. Please retry');
    }
  }

  public function updateQuantity(Request $request, Powder $powder)
  {
    $request->validate([
      'quantity' => ['required']
    ]);

    $this->authorize('update', Powder::class);

    $lastPowderLog = PowderAndInventoryLog::where([
      ['reason', '=', PowderAndInventoryLogsEnum::CREATING->value],
      ['powder_id', '=', $powder->id],
    ])->first();

    $logAddition = $request->quantity - $powder->current_weight;

    $powderLog = new PowderAndInventoryLog();
    if ($lastPowderLog) {
      $powderLog->fill([
        'reason' => PowderAndInventoryLogsEnum::MANUALADJUSMENT,
        'sum_added' => $logAddition,
        'powder_id' => $powder->id,
        'warehouse_id' => $lastPowderLog->warehouse_id,
        'floor_id' => $lastPowderLog->floor_id,
        'shelf_id' => $lastPowderLog->shelf_id,
        'bin_id' => $lastPowderLog->bin_id,
        'company_id' => auth()->user()->company_id
      ]);
    } else {
      $bin = Bin::orderBy('id', 'desc')->first();
      $powderLog->fill([
        'reason' => PowderAndInventoryLogsEnum::MANUALADJUSMENT,
        'sum_added' => $logAddition,
        'powder_id' => $powder->id,
        'warehouse_id' => $bin->shelf->floor->warehouse_id,
        'floor_id' => $bin->shelf->floor_id,
        'shelf_id' => $bin->shelf_id,
        'bin_id' => $bin->id,
        'company_id' => auth()->user()->company_id
      ]);
    }

    if ($powderLog->save()) {
      Cache::forget('powder_items');
      return back()->with('Success', 'Updated successfully');
    } else {
      return back()->with('Error', 'Failed to update. Please retry');
    }
  }

  public function destroy(Powder $powder)
  {
    $this->authorize('delete', Powder::class);
    if ($powder->delete()) {
      return back()->with('Success', 'Deleted successfully');
    } else {
      return back()->with('Error', 'Failed to delete. Please retry');
    }
  }

  public function excelReport(Request $request)
  {
    $suppliers = Supplier::with(['powders'])->get();

    $spreadsheet = new Spreadsheet();

    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'POWDER REPORT');
    $sheet->setCellValue('A2', Carbon::now()->format('d/M/Y h:i:sa'));

    $cellRowNumber = 3;
    foreach ($suppliers as $supplier) {
      if (count($supplier->powders) > 0) {
        $cellRowNumber += 1;
        $sheet->setCellValue('A' . $cellRowNumber, $supplier->supplier_name);
        $totalPowderWeight = 0;
        $cellRowNumber += 1;
        foreach ($supplier->powders as $powder) {
          $sheet->setCellValue('A' . $cellRowNumber, $powder->powder_color);
          $sheet->setCellValue('B' . $cellRowNumber, number_format($powder->current_weight, 2));
          $totalPowderWeight += floatval($powder->current_weight);
          $cellRowNumber += 1;
        }
        $sheet->setCellValue('A' . $cellRowNumber, "TOTAL WEIGHT");
        $sheet->setCellValue('B' . $cellRowNumber, $totalPowderWeight);
        $cellRowNumber += 1;
      }
    }
    $spreadsheet->getSheet(0);

    $writer = new Xlsx($spreadsheet);

    $extension = 'xlsx';
    $date = date('d/m/Y', time());
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"powder-inventory-{$date}.{$extension}\"");
    $writer->save('php://output');
    exit();
  }

  public function excelTemplate()
  {
    $spreadsheet = new Spreadsheet();

    $sheet = $spreadsheet->getActiveSheet();

    // the titles
    $sheet->setCellValue('A1', 'COLOR');
    $sheet->setCellValue('B1', 'SERIAL');
    $sheet->setCellValue('C1', 'MANUFACTURE DATE');
    $sheet->setCellValue('D1', 'EXPIRY DATE');
    $sheet->setCellValue('E1', 'BATCH NO');
    $sheet->setCellValue('F1', 'GOODS WEIGHT');
    $sheet->setCellValue('G1', 'OPENING WEIGHT');
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

    for ($i = 2; $i < 400; $i++) {

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
    header("Content-Disposition: attachment; filename=\"powder-template.{$extension}\"");
    $writer->save('php://output');
    exit();
  }

  public function excelTemplateUpload(Request $request)
  {
    if ($request->file('powder_excel_file') != NULL) {
      $file = $request->file('powder_excel_file');

      if ($file->extension() != 'xlsx') {
        return back()->with('Error', 'You need to have placed an excel file');
      } else {
        $reader = new ExcelReader();

        $spreadsheet = $reader->load($file->getPathName());

        $data = $spreadsheet->getSheet(0)->toArray();

        $titleRow = $data[0];

        $valid = true;

        if ($titleRow[0] != 'COLOR') {
          $valid = false;
        } elseif ($titleRow[1] != 'SERIAL') {
          $valid = false;
        } elseif ($titleRow[2] != 'MANUFACTURE DATE') {
          $valid = false;
        } elseif ($titleRow[3] != 'EXPIRY DATE') {
          $valid = false;
        } elseif ($titleRow[4] != 'BATCH NO') {
          $valid = false;
        } elseif ($titleRow[5] != 'GOODS WEIGHT') {
          $valid = false;
        } elseif ($titleRow[6] != 'OPENING WEIGHT') {
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
          $powder = new Powder();

          $powder->fill([
            'powder_color' => strtoupper($data[$i][0]),
            'powder_code' => strtoupper($data[$i][0]),
            'powder_description' => strtoupper($data[$i][0]),
            'serial_no' => strtoupper($data[$i][1]),
            'manufacture_date' => $data[$i][2],
            'expiry_date' => $data[$i][3],
            'batch_no' => strtoupper($data[$i][4]),
            'goods_weight' => $data[$i][5],
            'standard_cost' => $data[$i][9],
            'standard_cost_vat' => $data[$i][10],
            'standard_price' => $data[$i][11],
            'standard_price_vat' => $data[$i][12],
            'min_threshold' => $data[$i][7],
            'max_threshold' => $data[$i][8],
            'current_weight' => $data[$i][6],
            'opening_weight' => $data[$i][6],
            'supplier_id' => explode("-", $data[$i][14])[0],
            'company_id' => auth()->user()->company_id
          ]);

          if ($powder->save()) {
            $bin = Bin::find(explode("-", $data[$i][13])[0]);

            $powderLog = new PowderAndInventoryLog();

            $powderLog->fill([
              'reason' => PowderAndInventoryLogsEnum::CREATING,
              'reason_id' => $powder->id,
              'sum_added' => $powder->current_weight,
              'powder_id' => $powder->id,
              'warehouse_id' => $bin->shelf->floor->warehouse_id,
              'floor_id' => $bin->shelf->floor_id,
              'shelf_id' => $bin->shelf_id,
              'bin_id' => $bin->id,
              'company_id' => auth()->user()->company_id
            ]);

            $powderLog->saveQuietly();
          }
        }

        return back()->with('Success', 'Powder template items added!');
      }
    } else {
      return back()->with('Error', 'You need to have placed an excel file');
    }
  }

  public function excelEditTemplate()
  {
    $spreadsheet = new Spreadsheet();

    $sheet = $spreadsheet->getActiveSheet();

    // the titles
    $sheet->setCellValue('A1', 'SUPPLIER');
    $sheet->setCellValue('B1', 'POWDER');
    $sheet->setCellValue('C1', 'CURRENT WEIGHT');
    $sheet->setCellValue('D1', 'NEW WEIGHT');

    $powders = Powder::select('id', 'powder_color', 'current_weight', 'supplier_id')->with(['supplier:id,supplier_name'])->orderBy('supplier_id')->get();

    $rowNumber = 2;
    foreach ($powders as $powder) {
      $sheet->setCellValue('A' . $rowNumber, $powder->supplier_id . '-' . $powder->supplier->supplier_name);
      $sheet->setCellValue('B' . $rowNumber, $powder->id . '-' . $powder->powder_color);
      $sheet->setCellValue('C' . $rowNumber, $powder->current_weight);
      $rowNumber += 1;
    }
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

    $sheet->getProtection()->setPassword('xlvisben');
    $sheet->getProtection()->setSheet(true);
    $sheet->getStyle('D1:D' . $rowNumber)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);


    $writer = new Xlsx($spreadsheet);

    $extension = 'xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"powder-edit-template.{$extension}\"");
    $writer->save('php://output');
    exit();
  }

  public function excelEditTemplateUpload(Request $request)
  {
    $this->authorize('update', Powder::class);

    if ($request->file('powder_excel_file') != NULL) {
      $file = $request->file('powder_excel_file');

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
        } elseif ($titleRow[1] != 'POWDER') {
          $valid = false;
        } elseif ($titleRow[2] != 'CURRENT WEIGHT') {
          $valid = false;
        } elseif ($titleRow[3] != 'NEW WEIGHT') {
          $valid = false;
        }

        if (!$valid) {
          return back()->with('Error', 'Wrong excel template file');
        }

        for ($i = 1; $i < count($data); $i++) {
          if ($data[$i][0] === NULL || $data[$i][3] === NULL || $data[$i][3] === "") {
            continue;
          }
          $powderParts = explode('-', $data[$i][1]);

          $powder = Powder::find($powderParts[0]);

          $lastPowderLog = PowderAndInventoryLog::where([
            ['reason', '=', PowderAndInventoryLogsEnum::CREATING->value],
            ['powder_id', '=', $powder->id],
          ])->first();

          $logAddition = $data[$i][3] - $powder->current_weight;

          $powderLog = new PowderAndInventoryLog();
          if ($lastPowderLog) {
            $powderLog->fill([
              'reason' => PowderAndInventoryLogsEnum::MANUALADJUSMENT,
              'sum_added' => $logAddition,
              'powder_id' => $powder->id,
              'warehouse_id' => $lastPowderLog->warehouse_id,
              'floor_id' => $lastPowderLog->floor_id,
              'shelf_id' => $lastPowderLog->shelf_id,
              'bin_id' => $lastPowderLog->bin_id,
              'company_id' => auth()->user()->company_id
            ]);
          } else {
            $bin = Bin::orderBy('id', 'desc')->first();
            $powderLog->fill([
              'reason' => PowderAndInventoryLogsEnum::MANUALADJUSMENT,
              'sum_added' => $logAddition,
              'powder_id' => $powder->id,
              'warehouse_id' => $bin->shelf->floor->warehouse_id,
              'floor_id' => $bin->shelf->floor_id,
              'shelf_id' => $bin->shelf_id,
              'bin_id' => $bin->id,
              'company_id' => auth()->user()->company_id
            ]);
          }

          $powderLog->save();
        }
        Cache::forget('powder_items');
        return back()->with('Success', 'Powder template items added!');
      }
    } else {
      return back()->with('Error', 'You need to have placed an excel file');
    }
  }

  public function editPowder(Request $request, Powder $powder)
  {
    $suppliers =  Cache::remember('supplier_list_powder', (60 * 5), function () {
      return Supplier::all();
    });
    return view('system.powder.misc.edit-form', [
      'powder' => $powder,
      'suppliers' => $suppliers
    ]);
  }

  public function editQuantityPowder(Request $request, Powder $powder)
  {
    $suppliers =  Cache::remember('supplier_list_powder', (60 * 5), function () {
      return Supplier::all();
    });
    return view('system.powder.misc.edit-qty-form', [
      'powder' => $powder,
      'suppliers' => $suppliers
    ]);
  }
}
