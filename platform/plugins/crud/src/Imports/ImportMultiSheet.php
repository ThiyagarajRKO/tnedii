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
 * @author sabarishankar.parthi
 */
class ImportMultiSheet extends BulkImport implements SkipsEmptyRows,WithHeadingRow, WithBatchInserts, WithMultipleSheets {

    /**
     * @var Model
     */
    public $model;
    public $rows = 0;
    protected $sheets = [];
    protected $workSheets = [];

    public function __construct($model, $sheets = NULL) {        
		ini_set('post_max_size', '100M');
        ini_set('upload_max_filesize', '100M');
        set_time_limit(0);
        $this->model = get_class($model);
        if ($sheets) {
            $this->sheets = $sheets;
        }
    }

    public function sheets(): array {
        if ($this->sheets) {
            foreach ($this->sheets as $key => $sheet) {
                $importSheet = new ImportSheet($this->model, get_class($sheet));
                $this->workSheets[] = $importSheet;
                
            }
        }
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

    
    protected function addLanguageMeta($reference) {
        $modelObj = new $this->model;
        $table = $modelObj->getTable();
        $supportedModule = DB::table("cruds")->where("module_db", $table)->where("is_multi_lingual", 1)->get();
        if (count($supportedModule) > 0) {
            $currentLanguage = Language::getCurrentAdminLocaleCode();
            $originValue = null;

            if ($currentLanguage !== 'en_US') {
                $originValue = LanguageMeta::where([
                            'reference_id' => $reference->id,
                            'reference_type' => $this->model,
                        ])->value('lang_meta_origin');
            }

            LanguageMeta::saveMetaData($reference, $currentLanguage, $originValue);
        }
        return false;
    }

    /**
     * Transform a date value into a Carbon object.
     *
     * @return \Carbon\Carbon|null
     */
    public function transformDate($value, $format = 'Y-m-d') {
        try {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format($format);
        } catch (\ErrorException $e) {
            return Carbon::parse($value)->format($format);
        }
    }

    public function transformTime($value, $format = 'h:i:s') {
        try {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format($format);
        } catch (\ErrorException $e) {
            return Carbon::parse($value)->format($format);
        }
    }
    
    public function getIntakeId($value, $trainingProgramId) {
        $intakeId = NULL;
        if (!$value && !$trainingProgramId) {
            return $intakeId;
        }
        $month = date("m", strtotime($value));
        $intake = \DB::table('training_program_intakes')->where(['intake_start_month' => $month, 'training_program_id' => $trainingProgramId])->first();
        $intakeId = ($intake) ? $intake->id : NULL;
        return $intakeId;
    }

    public function getSemesterId($value, $intakeId) {
        $semesterId = NULL;
        if (!$value && !$intakeId) {
            return $semesterId;
        }
        $semester = \DB::table('training_program_intake_semester_mapping')->where(['session_name' => $value, 'intake_id' => $intakeId])->first();
        $semesterId = ($semester) ? $semester->id : NULL;
        return $semesterId;
    }

}
