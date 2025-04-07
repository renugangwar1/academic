<?php

namespace App\Exports;

use App\Models\ExcelLog;
use App\Models\SubjectMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UploadExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    protected $fileupload,$heading;
    
    public function __construct($fileupload,$heading=null)
    {
        $this->fileupload = $fileupload;
        $this->heading = $heading;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->fileupload;
    }

    public function headings(): array
    {
        return $this->heading[0][0];
    }
}
