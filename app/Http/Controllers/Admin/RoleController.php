<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;
use Validator;
use DB;
use Session;
class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('active', 'roles');

            return $next($request);
        });
    }
    // Roles listing
    public function roles(Request $request){
        $roles = Role::paginate(10);

        if($request->ajax()){

            $roles = datatables()
                ->of(
                    Role::select('id', 'name', 'status')
                            ->get()
                )
                ->addIndexColumn()
                ->addColumn('action', function($data){

                    $button = '<a href="/admin/edit_role/'.$data->id.'" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>';
                    // $button .= '&nbsp;&nbsp;';
                    $button .= '<button class="btn btn-sm btn-light-warning change_role_status" data-role-id="'.$data->id.'">'. ($data->status == 1 ? 'Inactivate' : 'Activate') .'</button>';

                    return $button;           
                })
                // ->rawColumns(['action'])
                ->make(true);

            return $roles;
        }

        return view('admin.roles');
    }

    // Display create role form
    public function create_role(Request $request){
        $abilities = Ability::all();
        return view('admin.create_role', compact('abilities'));
    }

    // Save new role
    public function store_role(Request $request){
        $user_id = \Auth::user()->id;
        try{
            $request_input = $request->except('_token');
            $rules = [
                'name' => 'required|string|max:250',
                'abilities' => 'required'
            ];

            $messages = [
                'name.required' => 'Please enter role name',
                'name.max' => 'Role name should not be more than 250 characters',
                'abilities.required' => 'Please select abilities'
            ];

            $validator = Validator::make($request_input, $rules, $messages);
            if($validator->fails()){
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }
            else{
                // Create role
                $role = Role::create([
                    'name' => $request_input['name']
                ]);

                // Assign abilities
                $role->allow($request_input['abilities']);

              
                $response['result'] = 'success';
                $response['msg'] = 'Role created';
            }
        }
        catch(Exception $e){
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return json_encode($response);
    }

    // Display edit role form
    public function edit_role(Request $request){
        $role = Role::find($request->id);

        if(is_null($role)){
            abort(404);
        }

        $assigned_abilities = $role->getAbilities()->toArray();
        $assigned_abilities_id_array = array_column($assigned_abilities, 'id');

        $all_abilities = Ability::all();

        return view('admin.edit_role', compact('role', 'all_abilities', 'assigned_abilities_id_array'));
    }

    // Update role
    public function update_role(Request $request){
        $user_id = \Auth::user()->id;
        try{
            $roles = Role::find($request['role_id']);
            $request_input = $request->except('_token');

            $rules = [
                'name' => 'required|string|max:250',
                'abilities' => 'required'
            ];

            $messages = [
                'name.required' => 'Please enter role name',
                'name.max' => 'Role name should not be more than 250 characters',
                'abilities.required' => 'Please select abilities'
            ];

            $validator = Validator::make($request_input, $rules, $messages);
            if($validator->fails()){
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }
            else{
                DB::beginTransaction();
                
                // Find role
                $role = Role::find($request_input['role_id']);
                $role->update(['name' => $request_input['name']]);

                // Revoke previous abilities
                $role->disallow($role->getAbilities());
                
                // Re-Assign abilities
                $role->allow($request_input['abilities']);
                
                DB::commit();

               
                $response['result'] = 'success';
                $response['msg'] = 'Role created';
            }
        }
        catch(Exception $e){
            DB::rollback();
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return json_encode($response);
    }

    // Function to active - inactive role
    public function change_role_status(Request $request){

        try{
            $role = Role::find($request['role_id']);

            if($role['status'] == 1){
                $status = 0;
            }
            else{
                $status = 1;
            }

            $role->status = $status;
            $role->save();

            $response['result'] = 'success';
            $response['msg'] = 'Role status changed';
            $response['role_status'] = $status;
        }
        catch(Exception $e){
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return json_encode($response);
    }

    public function export_excel(Request $request)
    {
        $roles =  Role::select('id', 'name', 'status')->get();
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr><th>S.no</th><th>Role</th><th>Status</th></tr>';
        
        foreach ($roles as $key => $role) {
            $i = $key + 1;
            $output .= '<tr>';
            $output .= '<td>' . $i . '</td>';
            $output .= '<td>' .$role->name . '</td>';
            $output .= '<td>' . ($role->status == 0 ? 'Active' : 'Inactive') . '</td>';
            $output .= '</tr>';
        }
        
        $output .= '</table>';
        $output .= '</body></html>';
    
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=Role_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    }
   
}
