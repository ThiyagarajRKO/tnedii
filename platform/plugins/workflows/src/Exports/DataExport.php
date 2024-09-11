<?php

namespace Impiger\Workflows\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
/**
 * Description of Data Export
 *
 * @author sabarishankar.parthi
 */
class DataExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('plugins/workflows::export.export-pdf', $this->data);
    }
    
//    public function collection()
//    {
//        return $this->data;
//    }
 

}
