<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Division;
use App\Models\Location;
use Helper;
use Validator;
use OwenIt\Auditing\Models\Audit;


class DivisionController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('active', 'divisions');

            return $next($request);
        });
    }
    
    public function division_list(Request $request)
    {
        $user = \Auth()->user();
        if($request->ajax()){
           
            $divisions = datatables()
                ->of(
                    Division::orderBy('id','desc')->get()
                )
                ->addColumn('status', function ($data) {

                    return $data->status == '1' ? 'Active' : 'Inactive';
                })
                ->addColumn('action', function($data) use ($user){
                    $button = '';
                    if ($user->can('edit_division')) {
                        $button = '<a href="/admin/company/edit/'.$data->id.'" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>&nbsp; &nbsp;'; 
                        // $button .= '&nbsp;&nbsp;';
                    }
                    // if ($user->can('delete_division')) {
                    //     $button .= '<a href="javascript:;" data-id="'.$data->id.'" class="btn btn-sm btn-clean btn-icon delete_division" title="Delete"><i class="fas fa-trash text-danger"></i></a>';
                    // }
                  
                    
                    return $button;           
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);

            return $divisions;
        }
        return view('admin.divisions');
    }
    public function create_division(Request $request){

        return view('admin.create_division');
    }
    public function store_division(Request $request){
        $user_id = \Auth::user()->id;
        try{
            $request_input = $request->except('_token');

            $rules = [
                'name' => 'required|string|max:50|unique:divisions,name,except,id',
                'short_code' => 'required|string|max:10|unique:divisions,short_code,except,id',
            ];

            $messages = [
                'name.required' => 'Please enter division name',
                'name.max' => 'Division name should not be more than 50 characters',
                'short_code.required' => 'Please enter division short code',
                'short_code.max' => 'short code should not be more than 10 characters',
                
            ];
            $validator = Validator::make($request_input, $rules, $messages);
            if($validator->fails()){
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }
            else{
                    
                $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
            

                $division = Division::create([
                    'name'=>$request_input['name'],
                    'short_code'=>$request_input['short_code'],
                    'status'=>$request_input['status'],
                ]);
             
                $response['result'] = 'success';
                $response['msg'] = 'Company created';
            }
        }
        catch(\Exception $e){
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }
 
        return response()->json($response);
    }
   
    public function edit_division(Request $request)
    {

        $division = Division::findOrFail($request->id);

        
        return view('admin.edit_division', compact('division'));
    } 
    public function manage_division(Request $request)
    {
       
        $division = Division::findOrFail($request->id); 
        return view('admin.location.create',['division'=>$division]);
    } 
    public function update_division(Request $request){
        $user_id = \Auth::user()->id;
        try{
            $request_input = $request->except('_token');
            $division_id = $request_input['division_id'];
            $name=$request_input['name'];
            $short_code=$request_input['short_code'];
           $db_division= Division::find($division_id);
 
           if($db_division->name!=$name && $db_division->name!=$short_code){
            $rules = [
                'name' => 'required|string|max:50|unique:divisions,name,except,id',
                'short_code' => 'required|string|max:10|unique:divisions,short_code,except,id',
            ];

            $messages = [
                'name.required' => 'Please enter division name',
                'name.max' => 'Division name should not be more than 50 characters',
                'short_code.required' => 'Please enter division short code',
                'short_code.max' => 'short code should not be more than 10 characters',
                
            ];

            $validator = Validator::make($request_input, $rules, $messages);
            if($validator->fails()){
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }
            else{
                $division_id = $request_input['division_id'];
                unset($request_input['division_id']);

                $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                
            
                Division::find($division_id)->update(
                    [
                        'name'=>$request_input['name'],
                        'short_code'=>$request_input['short_code'],
                        'status'=>$request_input['status']
                    ]
                );
             
                
                $response['result'] = 'success';
                $response['msg'] = 'Company Updated';
            }
           }else{
            $rules = [
                'name' => 'required|string|max:50',
                'short_code' => 'required|string|max:10',
            ];

            $messages = [
                'name.required' => 'Please enter division name',
                'name.max' => 'Division name should not be more than 50 characters',
                'short_code.required' => 'Please enter division short code',
                'short_code.max' => 'short code should not be more than 10 characters',
                
            ];

            $validator = Validator::make($request_input, $rules, $messages);
            if($validator->fails()){
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }
            else{
                $division_id = $request_input['division_id'];
                unset($request_input['division_id']);

                $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                
            
                Division::find($division_id)->update(
                    [
                        'name'=>$request_input['name'],
                        'short_code'=>$request_input['short_code'],
                        'status'=>$request_input['status']
                    ]
                );
             
                $response['result'] = 'success';
                $response['msg'] = 'Division Updated';
            }
           }
            
        }
        catch(\Exception $e){
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }
    
    public function delete_division(Request $request)
    {
        try {
            $id = $request['id'];
            if (!empty($id)) {
                $division = Division::findOrFail($id);
                
                $division->delete();

                $response['result'] = 'success';
                $response['msg'] = 'Division Deleted';
            } else {
                $response['result'] = 'failure';
                $response['msg'] = 'Select Restaurant';
            }
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }
    public function send_mail($data){


        $details['subject'] = 'New Restaurant Created';
        
        $details['name'] = $data['name'];
        $details['address'] = $data->address;
        $details['contact'] = $data->contact;
        $details['email'] = $data->email;
        $details['temp_contact1'] = $data->temp_contact1;
        $details['temp_contact2'] = $data->temp_contact2;
        $details['temp_email1'] = $data->temp_email1;
        $details['temp_email2'] = $data->temp_email2;
        $details['country'] = $data->country;
        $details['state'] = $data->state;
        $details['city'] = $data->city;
        $details['pincode'] = $data->pin_code;
        $admin_mail = config('constants.ADMIN_MAIL');

        $mail = Mail::to($data->email)->cc($admin_mail)->send(new RestaurentCreateMail($details));
    }
    public function addnewlocation(Request $req){
      //  dd('aa');
        $data = new Location();
        $data->id = $req->id;
        $data->divisions_id = $req->company_id;
        $data->status = $req->status;
        $data->name = $req->name;
        $data->save();
        return redirect()->back()->with('success','Location added successfully');

    }
    public function export_excel(Request $request)
    {
        $divisions = Division::orderBy('id', 'desc')->get();
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr><th>S.no</th><th>Name</th><th>Short code</th><th>Status</th></tr>';
        
        foreach ($divisions as $key => $division) {
            $i = $key + 1;
            $output .= '<tr>';
            $output .= '<td>' . $i . '</td>';
            $output .= '<td>' . $division->name . '</td>';
            $output .= '<td>' . $division->short_code . '</td>';
            $output .= '<td>' . ($division->status == 1 ? 'Active' : 'Inactive') . '</td>';
            $output .= '</tr>';
        }
        
        $output .= '</table>';
        $output .= '</body></html>';
    
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=Company_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    }
    public function Logs(Request $request){
    $audits = Audit::where('auditable_type','App\Models\Division')->paginate(10);
    return view('admin.companylog', compact('audits'));
    } 


    public function export_logexcel(Request $request)
  {
    $audits = Audit::where('auditable_type','App\Models\Division')->get();
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
    <th>Name</th>
    <th>Short Code</th>
    <th>Status</th></tr>';

    foreach($audits as $key => $audit){
        $newvalue =  $audit->new_values ?? [];
        $oldvalue =  $audit->old_values ?? [];
        $division = Division::find($audit->auditable_id);
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
            $output .= '<td>' . data_get($oldvalue, 'name') ?? '' . '</td>'; 
            $output .= '<td>' . data_get($oldvalue, 'short_code') ?? '' . '</td>'; 

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
            $output .= '<td colspan="3"></td>';  
        }

        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<td><b>'. "New Value". "</b></td>";

        $output .= '<td>' . data_get($newvalue, 'name') ?? '' . '</td>'; 
        $output .= '<td>' . data_get($newvalue, 'short_code') ?? '' . '</td>'; 

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
    header("Content-Disposition: attachment; filename=Companylog_List.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo $output;

}

}