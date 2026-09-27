<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\Boqmaterial;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Services\ExcelImportService;
use App\Services\ActivityLogService;
use OwenIt\Auditing\Models\Audit;

class ServiceBoq extends Controller
{
    private $logger;
    public function __construct(ActivityLogService $logger)
    {
        $this->logger = $logger;
        $this->middleware(function ($request, $next) {
            Session::put('active', 'serviceboq_list');

            return $next($request);
        });
    }
    public function serviceboq_list()
    {
        $data = Boqmaterial::select('activity','bun','service_short_text','rate_ser','id')->orderBy('id', 'desc')->paginate(10);
        return view('admin.serviceboq.list')->with(['data' => $data]);
    }

    public function create_serviceboq()
    {
        return view('admin.serviceboq.create');
    }

    public function store_serviceboq(Request $request)
    {
        $user_id = \Auth::user()->id;
        $this->validate($request, [
            'activity'  =>  'required',
            'bun'  =>  'required',
            'rate_ser'  =>  'required',
            'service_short_text'  =>  'required',
        ]);
        $data = new Boqmaterial();
        $data->activity = $request->activity;
        $data->bun = $request->bun;
        $data->rate_ser = $request->rate_ser;
        $data->service_short_text = $request->service_short_text;
        $data->save();
        
        $request_input = $request->except('_token');
       
        Session::flash('message', 'Added Successfully!');
        return redirect('/admin/serviceboq');

    }

    public function edit_serviceboq($id)
    {
        $data = Boqmaterial::where(['id' => $id])->first();
        return view('admin.serviceboq.edit')->with(['data' => $data]);
    }

    public function update_serviceboq(Request $request)
    {
        $db_capex = Boqmaterial::find($request->capexlist_id);

        $user_id = \Auth::user()->id;
        $data = Boqmaterial::find($request->capexlist_id);
        $data->activity = $request->input('activity');
        $data->bun = $request->input('bun');
        $data->rate_ser = $request->rate_ser;
        $data->service_short_text = $request->input('service_short_text');
        $data->save();
        
        Session::flash('message', 'Form updated successfully!');
        return redirect('/admin/serviceboq');
    }
    public function upload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $path = $request->file('file')->store('mboq');
            $import = new ExcelImportService(new Boqmaterial);
            $data = $import->import($path);
            $columns = array_diff(Schema::getColumnListing((new Boqmaterial)->getTable()), ['id', 'created_at', 'updated_at']);
            // $company_id = [
            //     'BRPL' => 5,
            //     'BYPL' => 6
            // ];
            // $data['rows'] = array_map(function ($row) use ($columns, $company_id) {
                $data['rows'] = array_map(function ($row) use ($columns) {
                if (count($columns) !== count($row)) {
                    return response()->json(['error' => 'Invalid file: Number of columns do not match in all rows'], 422);
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
            return response()->json(['error' => $message], 422);
        }
    }
    public function export_excel(Request $request)
    {
        $serviceboq =  Boqmaterial::orderBy('id', 'desc')->get();
        $output = '<html><head></head><body>';
        $output .= '<table>';
        $output .= '<tr><th>S.no</th><th>Activity</th><th>UOM</th><th>Service Short Text</th><th>Rate</th></tr>';
        
        foreach ($serviceboq as $key => $serviceboq) {
            $i = $key + 1;
            $output .= '<tr>';
            $output .= '<td>' . $i . '</td>';
            $output .= '<td>' . $serviceboq->activity . '</td>';
            $output .= '<td>' . $serviceboq->bun . '</td>';
            
            $output .= '<td>' . $serviceboq->service_short_text . '</td>';
            $output .= '<td>' . $serviceboq->rate_ser . '</td>';
            $output .= '</tr>';
        }
        
        $output .= '</table>';
        $output .= '</body></html>';
    
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=ServiceBOQ_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    }
    public function Logs(Request $request){
        $audits = Audit::where('auditable_type','App\Models\Boqmaterial')->paginate(10);
        return view('admin.serviceboq.logserviceboq', compact('audits'));
     }

     public function export_logexcel(Request $request)
     {
        $audits = Audit::where('auditable_type','App\Models\Boqmaterial')->get();
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
        <th>Activity</th>
        <th>UOM</th>
        <th>Rate</th>
        <th>Service Short Text</th></tr>';
         
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
                $output .= '<td>' . data_get($oldvalue, 'bun') ?? '' . '</td>'; 
                $output .= '<td>' . data_get($oldvalue, 'rate_ser') ?? '' . '</td>'; 
                $output .= '<td>' . data_get($oldvalue, 'service_short_text') ?? '' . '</td>';     
            }else{
                $output .= '<td colspan="4"></td>';  
            }

            $output .= '</tr>';
            $output .= '<tr>';
            $output .= '<td><b>'. "New Value". "</b></td>";
            $output .= '<td>' . data_get($newvalue, 'activity') ?? '' . '</td>'; 
            $output .= '<td>' . data_get($newvalue, 'bun') ?? '' . '</td>'; 
            $output .= '<td>' . data_get($newvalue, 'rate_ser') ?? '' . '</td>'; 
            $output .= '<td>' . data_get($newvalue, 'service_short_text') ?? '' . '</td>';  
            $output .= '</tr>';
        }
         
         $output .= '</table>';
         $output .= '</body></html>';
     
         header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
         header("Content-Disposition: attachment; filename=LogServiceBOQ_List.xls");
         header("Pragma: no-cache");
         header("Expires: 0");
     
         echo $output;
     }
}
