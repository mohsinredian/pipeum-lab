<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\MasterMaterialboq;
use App\Models\Division;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Services\ExcelImportService;
use App\Services\ActivityLogService;
use OwenIt\Auditing\Models\Audit;

class Materialboq extends Controller
{
    private $logger;
    public function __construct(ActivityLogService $logger)
    {
        $this->logger = $logger;
        $this->middleware(function ($request, $next) {
            Session::put('active', 'materialboq_list');

            return $next($request);
        });
    }
    public function materialboq_list()
    {
        $data = MasterMaterialboq::select('activity','uom','material_short_text','rate_add','id')->orderBy('id', 'desc')->paginate(10);

        return view('admin.materialboq.list')->with(['data' => $data]);
    }

    public function create_materialboq()
    {
        $company = Division::select('id', 'name')->get();
        return view('admin.materialboq.create')->with(['company' => $company]);
    }

    public function store_materialboq(Request $request)
    {
        $user_id = \Auth::user()->id;

        $this->validate($request, [
            'activity'  =>  'required',
            // 'company_id'  =>  'required',
            'uom'  =>  'required',
            'rate_add'  =>  'required',
            'material_short_text'  =>  'required',
        ]);
        $data = new MasterMaterialboq();
        $data->activity = $request->activity;
        // $data->company_id = $request->company_id;
        $data->uom = $request->uom;
        $data->rate_add = $request->input('rate_add');
        $data->material_short_text = $request->material_short_text;
        $data->save();

        $request_input = $request->except('_token');

        Session::flash('message', 'MaterialBOQ Added Successfully!');
        // return redirect()->back()->with('success', 'MaterialBOQ Added successfully!');
        return redirect('/admin/materialboq');
    }

    public function edit_materialboq($id)
    {
        $company = Division::select('id', 'name')->get();
        $data = MasterMaterialboq::where(['id' => $id])->first();
        return view('admin.materialboq.edit')->with(['data' => $data, 'company' => $company]);
    }

    public function update_materialboq(Request $request)
    {
        $user_id = \Auth::user()->id;

        $db_capex = MasterMaterialboq::find($request->capexlist_id);
       
        $data = MasterMaterialboq::find($request->capexlist_id);
        $data->activity = $request->input('activity');
        $data->uom = $request->input('uom');
        $data->rate_add = $request->input('rate_add');
        $data->material_short_text = $request->input('material_short_text');
        $data->save();

        Session::flash('message', 'MaterialBOQ updated successfully!');
        // return redirect()->back()->with('success', 'MaterialBOQ updated successfully!');
        return redirect('/admin/materialboq');
    }
    public function upload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json(['ValidatationError' => $validator->errors()], 422);
            }

            $path = $request->file('file')->store('mboq');
            $import = new ExcelImportService(new MasterMaterialboq);
            $data = $import->import($path);
            $columns = array_diff(Schema::getColumnListing((new MasterMaterialboq)->getTable()), ['id', 'created_at', 'updated_at','company_id']);
            // $company_id = [
            //     'BRPL' => 5,
            //     'BYPL' => 6
            // ];
            // $data['rows'] = array_map(function ($row) use ($columns, $company_id) {
                $data['rows'] = array_map(function ($row) use ($columns) {
                if (count($columns) !== count($row)) {
                    return response()->json(['SheetError' => 'Invalid file: Number of columns do not match in all rows'], 422);
                }
                $row = array_combine($columns, $row);
                // $row['company_id'] = isset($company_id[$row['company_id']]) ? $company_id[$row['company_id']] : null;   
                return $row;
            }, $data['rows']);
            $import->seedDB($data['rows']);
            $this->logger->log($request, 'File imported successfully.');
            return response()->json(['message' => 'File imported successfully']);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return response()->json(['UploadError' => $e], 422);
        }
    }
    public function export_excel(Request $request)
    {
        $materialboq = MasterMaterialboq::with('division')->orderBy('id', 'desc')->get();
        $output = '<html><head></head><body>';
        $output .= '<table>';
        $output .= '<tr><th>S.no</th><th>Material Code</th><th>UOM</th><th>Material Description</th><th>Rate</th></tr>';
        
        foreach ($materialboq as $key => $materialboq) {
            $i = $key + 1;
            $output .= '<tr>';
            $output .= '<td>' . $i . '</td>';
            $output .= '<td>' . $materialboq->activity . '</td>';
            $output .= '<td>' . $materialboq->uom . '</td>';
            $output .= '<td>' . $materialboq->material_short_text . '</td>';
            $output .= '<td>' . $materialboq->rate_add . '</td>';
            $output .= '</tr>';
        }
        
        $output .= '</table>';
        $output .= '</body></html>';
    
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=MaterialBOQ_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    }
    public function Logs(Request $request){
        $audits = Audit::where('auditable_type','App\Models\MasterMaterialboq')->paginate(10);
        return view('admin.materialboq.logmaterialboq', compact('audits'));
     }

     public function export_logexcel(Request $request)
     {
        $audits = Audit::where('auditable_type','App\Models\MasterMaterialboq')->get();
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr>
        <th>S.No</th>
        <th>Event</th>
        <th>Audited By</th>
        <th>Audited At</th>
        <th></th>
        <th>Material Code</th>
        <th>UOM</th>
        <th>Material Description</th>
        <th>Rate</th></tr>';
         
        foreach($audits as $key => $audit){
            $newvalue =  $audit->new_values ?? [];
            $oldvalue =  $audit->old_values ?? [];
            $userName = optional($audit->user)->name ?? ' ';
            $date = $audit->created_at->format('d-M-Y h:i:s A');
            $event = ucfirst($audit->event);
            $i = $key + 1;

            $output .= '<tr>';
            $output .= '<td rowspan="2">' . $i . '</td>';
            $output .= '<td rowspan="2">' . $event . '</td>'; 
            $output .= '<td rowspan="2">' . $userName . '</td>'; 
            $output .= '<td rowspan="2">' . $date . '</td>';
            $output .= '<td><b>'. "Old Value". "</b></td>";
           
            if( $event == 'Updated'){
                $output .= '<td>' . data_get($oldvalue, 'activity') ?? '' . '</td>'; 
                $output .= '<td>' . data_get($oldvalue, 'uom') ?? '' . '</td>'; 
                $output .= '<td>' . data_get($oldvalue, 'material_short_text') ?? '' . '</td>'; 
                $output .= '<td>' . data_get($oldvalue, 'rate_add') ?? '' . '</td>';     
            }else{
                $output .= '<td colspan="4"></td>';  
            }

            $output .= '</tr>';
            $output .= '<tr>';
            $output .= '<td><b>'. "New Value". "</b></td>";
            $output .= '<td>' . data_get($newvalue, 'activity') ?? '' . '</td>'; 
            $output .= '<td>' . data_get($newvalue, 'uom') ?? '' . '</td>'; 
            $output .= '<td>' . data_get($newvalue, 'material_short_text') ?? '' . '</td>'; 
            $output .= '<td>' . data_get($newvalue, 'rate_add') ?? '' . '</td>';  
            $output .= '</tr>';
        }
         
         $output .= '</table>';
         $output .= '</body></html>';
     
         header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
         header("Content-Disposition: attachment; filename=LogMaterialBOQ_List.xls");
         header("Pragma: no-cache");
         header("Expires: 0");
     
         echo $output;
     }
}
