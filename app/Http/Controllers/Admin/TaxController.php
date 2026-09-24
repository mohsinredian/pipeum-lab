<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Validator;
use App\Models\Tax;
use App\Models\Division;
use App\Models\Department;
use OwenIt\Auditing\Models\Audit;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('active', 'tax');

            return $next($request);
        });
    }
    public function tax_list(Request $request){
        $user = \Auth()->user();
        if($request->ajax()){
           
            $taxes = datatables()
                ->of(
                    Tax::orderBy('id','desc')->get()
                )
                ->addColumn('status', function ($data) {

                    return $data->status == '1' ? 'Active' : 'Inactive';
                })
                ->addColumn('action', function($data) use ($user){
                    $button = '';
                    if ($user->can('edit_division')) {
                        $button = '<a href="/admin/tax/edit/'.$data->id.'" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>'; 
                       
                    }
                  
                    
                    return $button;           
                })
                ->addIndexColumn()
                ->rawColumns(['action','status'])
                ->make(true);

            return $taxes;
        }
        return view('admin.tax.list');
    }
    public function create_tax(Request $request){
        return view('admin.tax.create');
    }

    public function store_tax(Request $request)
    {
        try{
            $request_input = $request->except('_token');

            $rules = [
            ];

            $messages = [
                
            ];
            $validator = Validator::make($request_input, $rules, $messages);
            if($validator->fails()){
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }
            else{
            

                $tax = Tax::create([
                    'tax'=>$request_input['tax'],
                    'status'=>$request_input['status'],
                ]);
                $response['result'] = 'success';
                $response['msg'] = 'Tax created';
            }
        }
        catch(\Exception $e){
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }
 
        return response()->json($response);
    }

    public function edit_tax(Request $request){
        $tax = Tax::findOrFail($request->id); 
        $data = [
            'tax'=> $tax,
            ];

        return view('admin.tax.edit',['data'=>$data]);
    }

    public function update_tax(Request $request){
        try{
            $request_input = $request->except('_token');
            $tax_id = $request_input['tax_id'];

            $rules = [
            ];

            $messages = [
                
            ];

            $validator = Validator::make($request_input, $rules, $messages);
            if($validator->fails()){
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }
            else{
                
                Tax::find($tax_id)->update(
                    [
                        'tax'=>$request_input['tax'],
                        'status'=>$request_input['status'],
                    ]
                );
                $response['result'] = 'success';
                $response['msg'] = 'Tax Updated';
            }
           }
            
       
        catch(\Exception $e){
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function Logs(Request $request){
        $audits = Audit::where('auditable_type','App\Models\Tax')->paginate(10);
        return view('admin.tax.log_tax', compact('audits'));
    }

    public function export_logexcel(Request $request)
    {
        $audits = Audit::where('auditable_type','App\Models\Tax')->get();
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
        <th>Tax (In %)</th>
        <th>Status</th></tr>';

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
                $output .= '<td>' . data_get($oldvalue, 'tax') ?? '' . '</td>'; 
               
                if (isset($oldvalue['status'])){
                    if($oldvalue['status']==0){
                        $output .= '<td>'."Inactive" .'</td>';
                    }else{
                        $output .= '<td>'."Active" .'</td>';
                    }
                }else{
                    $output .= '<td></td>';
                }
              
                
            }else{
                $output .= '<td colspan="2"></td>';  
            }

            $output .= '</tr>';
            $output .= '<tr>';
            $output .= '<td><b>'. "New Value". "</b></td>";
            $output .= '<td>' . data_get($newvalue, 'tax') ?? '' . '</td>'; 

                if (isset($newvalue['status'])){
                    if($newvalue['status']==0){
                        $output .= '<td>'."Inactive" .'</td>';
                    }else{
                        $output .= '<td>'."Active" .'</td>';
                    }
                }else{
                    $output .= '<td></td>';
                }
           
            $output .= '</tr>';
        }
                                             
        $output .= '</table>';
        $output .= '</body></html>';
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=Taxlog_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    
    }
}
