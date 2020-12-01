<?php


namespace App\Exports;



use App\Models\Ad;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class AdExport implements FromCollection
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // TODO: Implement collection() method.
        return new Collection($this->data);
    }
}
