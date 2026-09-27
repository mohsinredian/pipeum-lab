<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Exception;

class ExcelImportService implements ToCollection, WithHeadingRow
{
    protected $data = [
        'columns' => [],
        'rows' => []
    ];
    protected $model;

    public function __construct(Model $model = null)
    {
        $this->model = $model;
        $this->data = ['columns' => [], 'rows' => []];
    }

    /**
     * Called by the package with a Collection of rows (associative because of WithHeadingRow)
     * Each $row is a Laravel Collection (heading => value)
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // convert row (Collection) to plain array
            $rowArr = $row->toArray();

            // skip fully empty rows
            if (count(array_filter($rowArr, function ($v) {
                return !(is_null($v) || $v === '');
            })) === 0) {
                continue;
            }

            // normalize header keys: "Material code" -> "material_code"
            $normalized = [];
            foreach ($rowArr as $k => $v) {
                $kNorm = Str::snake(preg_replace('/[^A-Za-z0-9]+/', '_', trim($k)));
                $kNorm = preg_replace('/_+/', '_', $kNorm);
                $kNorm = trim($kNorm, '_');
                $normalized[$kNorm] = $v;
            }

            // set columns on first non-empty row (only once)
            if (empty($this->data['columns'])) {
                $this->data['columns'] = array_keys($normalized);
            }

            $this->data['rows'][] = $normalized;
        }
    }

    /**
     * Import driver - uses Excel::import so WithHeadingRow works
     * Returns array with keys 'columns' and 'rows'
     */
    public function import($filePath)
    {
        try {
            // reset
            $this->data = ['columns' => [], 'rows' => []];

            // Use Excel::import; this will call collection()
            Excel::import($this, $filePath);

            return $this->data;
        } catch (ValidationException $e) {
            // bubble up or return consistent structure
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function toArray()
    {
        return $this->data;
    }

    public function seedDB(array $data)
    {
        if ($this->model && !empty($data)) {
            // Insert expects each array to be associative with DB column names.
            $this->model::insert($data);
        }
    }
}
