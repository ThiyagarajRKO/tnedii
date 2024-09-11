<?php

namespace Impiger\Crud\Imports;

use Impiger\Department\Models\Department;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Excel;
use DB;
use Impiger\Language\Models\LanguageMeta;
use Language;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Impiger\Crud\Imports\ImportSheet;

/**
 * Description of BulkImport
 *
 * @author 
 */
class ImportMultiSheetNew extends BulkImport implements SkipsEmptyRows,WithHeadingRow, WithBatchInserts, WithMultipleSheets {

    /**
     * @var Model
     */
    public $model;
    public $rows = 0;
    protected $sheets = [];
    protected $user_img_arr = [];
    protected $workSheets = [];

    public function __construct($model, $sheets = NULL, $user_img_arr = []) {        
		ini_set('post_max_size', '100M');
        ini_set('upload_max_filesize', '100M');
        set_time_limit(0);
        $this->model = get_class($model);
        if ($sheets) {
            $this->sheets = $sheets;
        }
        if ($user_img_arr) {
            $this->user_img_arr = $user_img_arr;
        }
    }

    public function sheets(): array {
        if ($this->sheets) {
            foreach ($this->sheets as $key => $sheet) {
                if($key == 0)
                {
                    $importSheet = new ImportSheetNew($this->model, get_class($sheet), $this->user_img_arr);
                    //echo "<pre>";
                    //print_r($importSheet);
                    //echo "</pre>";
                    $this->workSheets[] = $importSheet;
                }
                
            }
        }
        //die();
        return $this->workSheets;
    }

   

    public function batchSize(): int {
        return 1000;
    }

    public function getRowCount(): int {
        if ($this->workSheets) {
            return $this->workSheets[0]->rows;
        }
        return $this->rows;
    }
    
    public function transformDate($value, $format = 'Y-m-d') {
        try {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format($format);
        } catch (\ErrorException $e) {
            return Carbon::parse($value)->format($format);
        }
    }

}
