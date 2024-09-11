<?php

namespace Impiger\Crud\Imports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
/**
 * Description of DepartmentImport
 *
 * @author sabarishankar.parthi
 */
class BulkErrorExport implements FromArray, WithHeadings
{
    protected $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }
    
    public function headings(): array
    {
        $headings = $this->getHeadings($this->errors);
        return $headings;
    }
    
    public function array(): array
    {
        return $this->errors;
    }
    
    public function getHeadings(array $errors) {
    $keys = array();

    foreach ($errors as $key => $value) {
        if (is_array($value)) {
            $keys = array_keys($value);
        }
    }
    
    return $keys;
}

}
