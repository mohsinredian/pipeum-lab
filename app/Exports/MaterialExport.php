<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Department;
use App\Models\MasterMaterialboq;
use App\Models\NVMaterial;
use App\Models\MateriBOQBulk;
use DB;

class MaterialExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $departments;
    protected $materialCounts;
    protected $searchTerm;

    public function __construct($searchTerm = null)
    {
        $this->searchTerm = $searchTerm;

        $this->departments = Department::select('name', 'id')->where('status', 1)->get();
        $materialCodes = MasterMaterialboq::pluck('activity')->toArray();

        $this->materialCounts = [];
        foreach ($this->departments as $department) {
            $nvIds = NVMaterial::where('dept_id', $department->id)->pluck('nv_id');
            $counts = MateriBOQBulk::whereIn('nv_id', $nvIds)
                ->whereIn('material_code', $materialCodes)
                ->select('material_code', DB::raw('count(*) as count'))
                ->groupBy('material_code')
                ->pluck('count', 'material_code')
                ->toArray();
            
            $this->materialCounts[$department->id] = $counts;
        }
    }

    public function collection()
    {
        $query = MasterMaterialboq::select('activity', 'uom', 'material_short_text', 'rate_add')
            ->orderBy('id', 'desc');


        // Apply search filter
        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('activity', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('material_short_text', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('rate_add', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('uom', 'like', '%' . $this->searchTerm . '%');
            });
        }

        return $query->get()->map(function ($item, $key) {
            $item->sr_no = $key + 1; 
            return $item;
        });
    }

    public function headings(): array
    {
        $headings = [
            'S.No',
            'Material Code',
            'Material Description',
            'Rate',
            'Category',
            'UOM',
        ];

        foreach ($this->departments as $department) {
            $headings[] = $department->name;
        }

        $headings[] = 'Total';
        return $headings;
    }

    public function map($data): array
    {
        $mapped = [
            $data->sr_no,
            $data->activity,
            $data->material_short_text,
            $data->rate_add,
            '',
            $data->uom,
        ];

        $totalCount = 0;
        foreach ($this->departments as $department) {
            $count = $this->materialCounts[$department->id][$data->activity] ?? 0;
            $mapped[] = $count;
            $totalCount += $count;
        }

        $mapped[] = $totalCount;
        return $mapped;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:Q1')->getFont()->setBold(true);
    }
}

