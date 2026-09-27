<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Division;
use App\Models\Department;
use App\Models\User;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Designation;
use Config;
use DB;
use PDF;
use Exception;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Silber\Bouncer\Database\Role;
use App\Models\SupDept;
use Validator;
use Illuminate\Support\Facades\Mail;
use OwenIt\Auditing\Models\Audit;
use App\Services\ActivityLogService;

class EmployeeController extends Controller
{
    private $logger;
    public function __construct(ActivityLogService $Logger)
    {
        $this->logger = $Logger;
        $this->middleware(function ($request, $next) {
            Session::put('active', 'employees');

            return $next($request);
        });
    }
    public function employee_list(Request $request)
    {
        $user = \Auth()->user();
        if ($request->ajax()) {

            $employees = datatables()
                ->of(
                    Employee::with('division', 'location', 'role', 'report','department','design')
                    ->select('name','status','email','phone','department_id','designation','employee_id','role_id','last_login_at','id')
                    ->orderBy('id', 'desc')->get()
                )
                ->addColumn('role', function ($data) {

                    return $data->role->name;
                })
            
                ->addColumn('status', function ($data) {

                    return $data->status == '1' ? 'Active' : 'Inactive';
                })
                ->addColumn('department_id', function ($data) {

                    $departmentIds = explode(',', $data->department_id);
                    $departmentNames = [];
                    foreach ($departmentIds as $id) {
                        $departmentNames[] = getDepartmentName($id);
                    }
                    return implode(', ', $departmentNames);
                })
                ->addColumn('design', function ($data) {
                   if(!empty($data->designation)){
                    return $data->design->name;
                   }
                   
                })

                ->addColumn('last_login_at', function ($data) {
                    if ($data->last_login_at === null) {
                        return "Not logged in";
                    }

                    return date("d-M-y h:i A", strtotime($data->last_login_at));
                })

                ->addColumn('action', function ($data) use ($user) {
                    $button = '';
                    if ($user->can('edit_division')) {
                        $button = '<a href="/admin/employees/edit/' . $data->id . '" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>';
                    }
                  
                    return $button;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status', 'division', 'location', 'role','department','design'])
                ->make(true);

            return $employees;
        }
        return view('admin.employee.list');
    }
    public function create_employee(Request $request)
    {
        $divisions = Division::select('id', 'name')->where('status', 1)->get();
        $designation = Designation::select('id', 'name')->where('status', 1)->get();
        $departments = Department::select('id', 'name')->where('status', 1)->get();
        $supdept = SupDept::select('id', 'name')->where('status', 1)->get();
        $locations = Location::select('id', 'name')->where('status', 1)->get();
        $roles = Role::select('id', 'title',)->whereIn('id', [9, 11,12])->get();
        $employees = User::where('status', 1)->where('id', '!=', 1)->get();
        return view('admin.employee.create', compact('divisions', 'locations', 'roles', 'departments', 'employees','supdept','designation'));
    }
    public function store_employee(Request $request)
    {
       
        $user_id = \Auth::user()->id;
        try {
            $request_input = $request->except('_token');
           
            $rules = [
                'name' => 'required|string|max:50',
                'email' => 'required|email|max:50|unique:employee,email',
                'phone' => 'required|numeric|digits_between:10,15|unique:employee,phone',
                'division' => 'required',
                'password' => 'required',
                'department_id'=> 'required',
                // 'location' => 'required',
                'employee_id' => 'required|unique:employee',
                'role_id' => 'required',
                // 'rights' => 'required',
                'status' => 'required|in:0,1',
            ];
    
            $messages = [
                'name.required' => 'Please enter employee name',
                'name.max' => 'Employee name should not be more than 50 characters',
                'email.required' => 'Please enter employee email',
                'email.email' => 'Please enter a valid email',
                'email.max' => 'Employee email should not be more than 50 characters',
                'email.unique' => 'This email is already taken. Please choose a different one.',
                'phone.required' => 'Please enter employee mobile',
                'phone.numeric' => 'Please enter a valid mobile number',
                'phone.digits_between' => 'Please enter a valid contact number',
                'phone.unique' => 'This Mobile number is already exists.',
                'division.required' => 'Please select a division',
                'password.required' => 'Please insert password',

                'department_id.required' => 'Please insert department',
                // 'location.required' => 'Please select a location',
                'employee_id.required' => 'Please enter employee id',
                // 'employee_id.unique' => 'Employee ID already exists',
                'role_id.required' => 'Please select role',
                // 'rights.required' => 'Please select Access Right',
                'status.required' => 'Please select status',
            ];
    
            $validator = Validator::make($request_input, $rules, $messages);
            if(isset($request->super_department))
            {
             $superDepIds = implode(',', $request->super_department);
            }
              else
              {
                 $superDepIds = $request->super_department;
              }
       if(isset($request->department_id))
       {
        $departmentIds = implode(',', $request->department_id);
       }
         else
         {
            $departmentIds = $request->department_id;
         }
            if ($validator->fails()) {
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            } else {
                $existingDesignation = Designation::where('id', $request_input['designation'])->where('status', 1)->first();
                if ($existingDesignation) {
                    // If the designation exists, use the existing one
                    $designationId = $existingDesignation->id;
                } else {
                    // If the designation doesn't exist, create a new one
                    $newDesignation = Designation::create([
                        'name' => $request_input['customDesignation'],
                        'status' => 1, // Assuming 'status' is a required field
                    ]);

                    $designationId = $newDesignation->id;
                }
                // dd($request_input['mobile']);
                $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                $Emp = User::create([
                    'role_id' => $request_input['role_id'],
                    'name' => $request_input['name'],
                    'email' => $request_input['email'],
                    'password' => \Hash::make($request_input['password']),
                    'mobile_number' => $request_input['phone'], // Store mobile number here
                    'last_password_change_date' => now(),
                    'email_verified_at' => now(),
                    'designation' => $designationId,
                    'emp_status' => 0,
                    'department_id' => $departmentIds,
                    'super_department' => $superDepIds,
                    'username' => $request_input['employee_id'],
                    'status' => $request_input['status'],
                    // 'rights' => $request_input['rights'],

                ]);
               
                $Emp->assign('HOD');
                $Emp->assign('CEO');
                $Emp->assign('User');
                $Emp->assign('Subadmin');
                $Employee = Employee::create([
                    'name' => $request_input['name'],
                    'email' => $request_input['email'],
                    'phone' => $request_input['phone'], // Store mobile number here
                    'division_id' => $request_input['division'],
                    'password' => $request_input['password'],
                    'role_name' => $request_input['role_id'],
                    'designation' => $designationId,
                    // 'location_id' => $request_input['location'],
                    'role_id' => $request_input['role_id'],
                    'department_id' => $departmentIds,
                    'super_department' => $superDepIds,
                    // 'report_to' => $request_input['report_to'],
                    'employee_id' => $request_input['employee_id'],
                    // 'rights' => $request_input['rights'],
                    'status' => $request_input['status'],
                    'user_id' => $Emp['id'],
                ]);
                $data = DB::table('assigned_roles as asrole')
                    ->leftJoin('users as usr', 'usr.id', '=', 'asrole.entity_id')
                    ->where('usr.id', $user_id)
                    ->select('asrole.role_id', 'asrole.entity_id', 'usr.disabled')
                    ->first();
                    if (!$data) {
                        DB::table('assigned_roles')->insert([
                            'role_id' => $request->role_id,
                            'entity_id' => $user_id,
                            'entity_type' => 'App\Models\User'
                        ]);
                        DB::table('users')->update([
                            'disabled' => 0,
                            
                        ]);
                    }
                $response['result'] = 'success';
                $response['msg'] = 'Employee created';
                $this->logger->log($request, 'Store Employee');
            }
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }
    
        return response()->json($response);
    }
    
    public function edit_employee(Request $request)
    {
        $employee = Employee::findOrFail($request->id);
        $divisions = Division::select('id', 'name')->where('status', 1)->get();
        $designation = Designation::select('id', 'name')->where('status', 1)->get();
        $departments = Department::select('id', 'name')->where('status', 1)->get();
        $locations = Location::select('id', 'name')->where('status', 1)->get();
        $role = Role::select('id', 'title')->whereIn('id', [9, 11,12])->get();
        // $reportTo = User::where('status', 1)->where('id', '!=', 1)->get();
        // $emp_dept = Employee::join('superdepartment','superdepartment.id','=','employee.super_department')->where('employee.id',$request->id)->first();
        // dd($employee);
        $selectedDepartments = explode(',', $employee->department_id);
        $selectedSuperDepartments = explode(',', $employee->super_department);
        // $supdepts122 = SupDept::select('id', 'name','departments')->whereIn('id',explode(',', $emp_dept->super_department))->where('status', 1)->get();
        $supdepts = SupDept::select('id', 'name','departments')->where('status', 1)->get();
        // foreach($supdepts122 as $dept)
        // {
        //     $map_dept = explode(',',$dept->departments,);
            
        // }
        // $s_id = explode(',', $emp_dept->super_department);
        $subDepartments = SupDept::whereIn('id', $selectedSuperDepartments)->pluck('departments')->toArray(); 
           
        $itemsArray = [];
        foreach ($subDepartments as $departments) {
            $itemsArray = array_merge($itemsArray, explode(',', $departments));
        }
        $itemsArray = array_filter(array_unique($itemsArray));
        
        $map_dept = Department::whereIn('id', $itemsArray)->select('name', 'id')->get();
        // $dept_name = Department::select('id', 'name')->where('status', 1)->whereIn('id',$map_dept->id)->get();
        // $users = Department::select('id', 'name')->where('status', 1)->whereIn('id',$map_dept->id)->get();
            //    dd($map_dept);

        // dd($dept_name);
       // Using Laravel Query Builder (recommended)
            $data = DB::table('assigned_roles as asrole')
            ->leftJoin('users as usr', 'usr.id', '=', 'asrole.entity_id')
            ->where('usr.id', $employee->user_id)
            ->select('asrole.role_id', 'asrole.entity_id', 'usr.disabled')
            ->first();
            if (!$data) {
                DB::table('assigned_roles')->insert([
                    'role_id' => $employee->role_id,
                    'entity_id' => $employee->user_id,
                    'entity_type' => 'App\Models\User'
                ]);
                DB::table('users')->update([
                    'disabled' => 0,
                    
                ]);
            }


        return view('admin.employee.edit', compact('selectedSuperDepartments','employee', 'divisions', 'departments','map_dept', 'locations', 'role', 'supdepts', 'selectedDepartments','designation'));
    }   
    // public function update_employee(Request $request)
    // {
    //     // dd($request->department_id);
    //     $user_id = \Auth::user()->id;
    //     try {
    //         $request_input = $request->except('_token');
    //         $employee_id = $request_input['emp_id'];
    //         $db_employee = Employee::find($employee_id);
    //         $id = $db_employee['user_id'];

    //         $rules = [
    //             'name' => 'required|string|max:50',
    //             'email' => 'required|email|max:50|unique:employee,email,' . $employee_id,
    //             'phone' => 'required|numeric|digits_between:10,15|unique:employee,phone,' . $employee_id,
    //             'division' => 'required',
    //             // 'location' => 'required',
    //             'employee_id' => 'required|unique:employee,employee_id,' . $employee_id,
    //             // 'rights' => 'required',
    //             'status' => 'required|in:0,1',
    //         ];

    //         $messages = [
    //             'name.required' => 'Please enter employee name',
    //             'name.max' => 'Employee name should not be more than 50 characters',
    //             'email.required' => 'Please enter employee email',
    //             'email.email' => 'Please enter a valid email',
    //             'email.max' => 'Employee email should not be more than 50 characters',
    //             'email.unique' => 'This email is already taken. Please choose a different one.',
    //             'phone.required' => 'Please enter employee mobile',
    //             'phone.numeric' => 'Please enter a valid mobile number',
    //             'phone.digits_between' => 'Please enter a valid contact number',
    //             'phone.unique' => 'This Mobile number is already exists.',
    //             'division.required' => 'Please select a division',
    //             // 'location.required' => 'Please select a location',
    //             'employee_id.required' => 'Please enter an employee ID',
    //             'employee_id.unique' => 'This employee ID is already taken. Please choose a different one.',
    //             // 'rights.required' => 'Please select Access Right',
    //             'status.required' => 'Please select status',
    //         ];

    //         $validator = Validator::make($request_input, $rules, $messages);
    //         $departmentIds = implode(',', $request->department_id);
    //         $supDepIds = implode(',', $request->super_department);
    //         if ($validator->fails()) {
    //             $response['msg'] = $validator->errors()->toArray();
    //             $response['result'] = 'error';
    //         } else {
    //             $existingDesignation = Designation::where('id', $request_input['designation'])->where('status', 1)->first();
    //             if ($existingDesignation) {
    //                 // If the designation exists, use the existing one
    //                 $designationId = $existingDesignation->id;
    //             } else {
    //                 // If the designation doesn't exist, create a new one
    //                 $newDesignation = Designation::create([
    //                     'name' => $request_input['customDesignation'],
    //                     'status' => 1, // Assuming 'status' is a required field
    //                 ]);

    //                 $designationId = $newDesignation->id;
    //             }
                
    //             $employee_id = $request_input['emp_id'];
    //             unset($request_input['emp_id']);

    //             $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;

    //             $user = User::find($id);

    //             $updateData = [
    //                 'name' => $request_input['name'],
    //                 'email' => $request_input['email'],
    //                 'mobile_number' => $request_input['phone'],
    //                 'status' => $request_input['status'],
    //                 'role_id' => $request_input['role_id'],
    //                 'username' => $request_input['employee_id'],
    //                 'designation' => $designationId,
    //                 'department_id' => $departmentIds,
    //                 'super_department' => $supDepIds,
    //                 // 'rights' => $request_input['rights'],
    //             ];
                
    //             // Check if password is present in the request
    //             if (!empty($request_input['password'])) {
    //                 $updateData['password'] = \Hash::make($request_input['password']);
    //                 $updateData['last_password_change_date'] = now();

    //                 $userEmail = $request_input['email'];
    //                 $username = $request_input['name'];
    //                 $data = [
    //                     'name' => $username,
                        
    //                 ];
                 

    //                 Mail::send('emailtemp.changePass_mail', $data, function ($message) use ($userEmail ) {
    //                     $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
    //                     $message->to($userEmail);
                       
    //                     $message->subject('Password Change Notification');
    //                 });
    //             }
                
    //             $user->update($updateData);
                

    //             Employee::find($employee_id)->update([
    //                 'name' => $request_input['name'],
    //                 'email' => $request_input['email'],
    //                 'phone' => $request_input['phone'],
    //                 'division_id' => $request_input['division'],
    //                 // 'location_id' => $request_input['location'],
    //                 'department_id' => $departmentIds,
    //                 'employee_id' => $request_input['employee_id'],
    //                 'designation' => $designationId,
    //                 'role_name' => $request_input['role_id'],
    //                 'super_department' => $supDepIds,
    //                 'role_id' => $request_input['role_id'],
    //                 // 'rights' => $request_input['rights'],
    //                 'status' => $request_input['status'],
    //                 'password' => !empty($request_input['password']) ? $request_input['password'] : \DB::raw('`password`'),
    //             ]);

    //             $response['result'] = 'success';
    //             $response['msg'] = 'Employee Updated';
    //         }
    //     } catch (\Exception $e) {
    //         app(\App\Exceptions\Handler::class)->report($e);
    //         $response['result'] = 'failure';
    //         $response['msg'] = $e->getMessage();
    //     }
    //     $this->logger->log($request, 'Update Employee');
    //     return response()->json($response);
    // }
    
    public function update_employee(Request $request)
    {
        $user_id = \Auth::user()->id;

        try {
            $request_input = $request->except('_token');
            $empId = $request_input['emp_id'];

            $employee = Employee::findOrFail($empId);
            $user = User::findOrFail($employee->user_id);

            /* ---------------- VALIDATION ---------------- */
            $rules = [
                'name'          => 'required|string|max:50',
                'email'         => 'required|email|max:50|unique:employee,email,' . $empId,
                'phone'         => 'required|numeric|digits_between:10,15|unique:employee,phone,' . $empId,
                'division'      => 'required',
                'employee_id'   => 'required|unique:employee,employee_id,' . $empId,
                'role_id'       => 'required',
                'department_id' => 'required|array',
                'status'        => 'required|in:0,1',
            ];

            $messages = [
                'name.required'        => 'Please enter employee name',
                'email.required'       => 'Please enter employee email',
                'email.unique'         => 'Email already exists',
                'phone.required'       => 'Please enter mobile number',
                'phone.unique'         => 'Mobile already exists',
                'division.required'    => 'Please select division',
                'employee_id.required' => 'Please enter employee id',
                'employee_id.unique'   => 'Employee ID already exists',
                'role_id.required'     => 'Please select role',
                'department_id.required' => 'Please select department',
                'status.required'      => 'Please select status',
            ];

            $validator = Validator::make($request_input, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'result' => 'error',
                    'msg'    => $validator->errors()->toArray()
                ]);
            }

            /* ---------------- DEPARTMENT HANDLING ---------------- */
            $departmentIds = isset($request->department_id)
                ? implode(',', $request->department_id)
                : null;

            $superDepIds = isset($request->super_department)
                ? implode(',', $request->super_department)
                : null;

            /* ---------------- DESIGNATION HANDLING ---------------- */
            $designation = Designation::where('id', $request_input['designation'])
                ->where('status', 1)
                ->first();

            if ($designation) {
                $designationId = $designation->id;
            } else {
                $designationId = Designation::create([
                    'name'   => $request_input['customDesignation'],
                    'status' => 1
                ])->id;
            }

            /* ---------------- USER UPDATE ---------------- */
            $userUpdate = [
                'name'              => $request_input['name'],
                'email'             => $request_input['email'],
                'mobile_number'     => $request_input['phone'],
                'role_id'           => $request_input['role_id'],
                'username'          => $request_input['employee_id'],
                'designation'       => $designationId,
                'department_id'     => $departmentIds,
                'super_department'  => $superDepIds,
                'status'            => $request_input['status'],
            ];

                $data = DB::table('assigned_roles as asrole')
                ->leftJoin('users as usr', 'usr.id', '=', 'asrole.entity_id')
                ->where('usr.id', $employee->user_id)
                ->select('asrole.role_id', 'asrole.entity_id', 'usr.disabled')
                ->first();
                if (!$data) {
                    DB::table('assigned_roles')->insert([
                        'role_id' => $employee->role_id,
                        'entity_id' => $employee->user_id,
                        'entity_type' => 'App\Models\User'
                    ]);
                    DB::table('users')->update([
                        'disabled' => 0,
                        
                    ]);
                }
            if (!empty($request_input['password'])) {
                $userUpdate['password'] = \Hash::make($request_input['password']);
                $userUpdate['last_password_change_date'] = now();

                Mail::send('emailtemp.changePass_mail', [
                    'name' => $request_input['name']
                ], function ($message) use ($request_input) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($request_input['email']);
                    $message->subject('Password Change Notification');
                });
            }

            $user->update($userUpdate);

            /* ---------------- EMPLOYEE UPDATE ---------------- */
            $employee->update([
                'name'            => $request_input['name'],
                'email'           => $request_input['email'],
                'phone'           => $request_input['phone'],
                'division_id'     => $request_input['division'],
                'employee_id'     => $request_input['employee_id'],
                'role_id'         => $request_input['role_id'],
                'role_name'       => $request_input['role_id'],
                'designation'     => $designationId,
                'department_id'   => $departmentIds,
                'super_department'=> $superDepIds,
                'status'          => $request_input['status'],
                'password'        => !empty($request_input['password'])
                                        ? $request_input['password']
                                        : $employee->password,
            ]);

            $this->logger->log($request, 'Update Employee');

            return response()->json([
                'result' => 'success',
                'msg'    => 'Employee Updated Successfully'
            ]);

        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);

            return response()->json([
                'result' => 'failure',
                'msg'    => $e->getMessage()
            ]);
        }
    }

    public function delete_employee(Request $request)
    {
        try {
            $id = $request['id'];
            if (!empty($id)) {
                $vendor = Employee::findOrFail($id);

                $vendor->delete();

                $response['result'] = 'success';
                $response['msg'] = 'Employee Deleted';
            } else {
                $response['result'] = 'failure';
                $response['msg'] = 'Select Employees';
            }
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }
        $this->logger->log($request, 'Delete Employee');
        return response()->json($response);
    }

    public function getLocationByDivision(Request $request)
    {
        try {
            $division_id = $request->division ?? null;

            $assets = Location::select('id', 'name')->where('divisions_id', $division_id)->orderBy('id', 'desc')->get()->toArray();

            return response()->json(['result' => 'success', 'data' => $assets]);
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);

            return response()->json(['result' => 'failure', 'msg' => $e->getMessage()]);
        }
    }

    public function export_excel(Request $request)
    {
        $employees = Employee::with('division', 'location', 'role', 'report','department')->orderBy('id', 'desc')->get();
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr><th>S.no</th><th>Name</th><th>Email Id</th><th>Mobile No</th><th>Department</th><th>Employee Id</th><th>Role</th><th>Status</th><th>LastLogin</th></tr>';
        
        foreach ($employees as $key => $employees) {
            $i = $key + 1;
            $output .= '<tr>';
            $output .= '<td>' . $i . '</td>';
            $output .= '<td>' .$employees->name . '</td>';
            $output .= '<td>' .$employees->email . '</td>';
            $output .= '<td>' .$employees->phone . '</td>';
            $departmentIds = explode(',', $employees->department_id);
            $departmentNames = [];
            foreach ($departmentIds as $id) {
                $departmentNames[] = getDepartmentName($id);
            }
            $output .= '<td>' .implode(', ', $departmentNames) . '</td>';
            $output .= '<td>' .$employees->employee_id . '</td>';
            $output .= '<td>' .$employees->role->name . '</td>';
            $output .= '<td>' . ($employees->status == 1 ? 'Active' : 'Inactive') . '</td>';
            if ($employees->last_login_at === null) {
                $output .= '<td>' ."Not logged in" . '</td>';
            }else{
                $output .= '<td>' .date("d-M-y h:i A", strtotime($employees->last_login_at)) . '</td>';
            }
            $output .= '</tr>';
        }
        
        $output .= '</table>';
        $output .= '</body></html>';
    
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=Employee_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    }

    public function export_pdf(Request $request)
    {
        $employees = Employee::with('division', 'location', 'role', 'report','department')->orderBy('id', 'desc')->get();
        $pdf = PDF::loadView('admin.employee.emp_pdf', ["employees" => $employees])->setPaper('a4', 'landscape');
        return $pdf->download('Employee_list.pdf');
    }

    public function Logs(Request $request){
        $audits = Audit::where('auditable_type','App\Models\Employee')->orderBy('id', 'desc')->paginate(10);
        return view('admin.employee.log_emp', compact('audits'));
     }
     public function export_logexcel(Request $request)
{
    $audits = Audit::where('auditable_type','App\Models\Employee')->orderBy('id', 'desc')->get();
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
    <th >Audited At</th>
    <th></th>
    <th>Name</th>
    <th>Email ID</th>
    <th>Mobile No</th>
    <th>Sub-Department</th>
    <th>Employee ID</th>
    <th>Role</th>
    <th>Status</th></tr>';
     
    foreach($audits as $key => $audit){
     $newvalue =  $audit->new_values ?? [];
     $oldvalue =  $audit->old_values ?? [];
     $department = Department::find($audit->auditable_id);
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
         $output .= '<td>' . (data_get($oldvalue, 'email')) ?? '' . '</td>'; 
         $output .= '<td>' . (data_get($oldvalue, 'phone')) ?? '' . '</td>';
        //  $output .= '<td>' . getsuperdepname(data_get($oldvalue, 'super_department')) ?? '' . '</td>';
         if(is_array($oldvalue) && array_key_exists('department_id', $oldvalue)){
            $departmentIds = explode(',', $oldvalue['department_id'] );
            $departmentNames = [];
            foreach ($departmentIds as $id) {
            $departmentNames[] = getDepartmentName($id);
                }
            $output .= '<td>' . implode(', ', $departmentNames) . '</td>'; 
         }         
         $output .= '<td>' . (data_get($oldvalue, 'employee_id')) ?? '' . '</td>'; 
         $output .= '<td>' . getRoleName(data_get($oldvalue, 'role_id')) ?? '' . '</td>';
        
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
         $output .= '<td colspan="8"></td>';  
     }

     $output .= '</tr>';
     $output .= '<tr>';
     $output .= '<td><b>'. "New Value". "</b></td>";
     $output .= '<td>' . data_get($newvalue, 'name') ?? '' . '</td>';
     $output .= '<td>' . (data_get($newvalue, 'email')) ?? '' . '</td>'; 
     $output .= '<td>' . (data_get($newvalue, 'phone')) ?? '' . '</td>'; 
    //  $output .= '<td>' . getsuperdepname(data_get($newvalue, 'super_department')) ?? '' . '</td>';
     if(is_array($newvalue) && array_key_exists('department_id', $newvalue)){
        $newDepartmentIds = is_array($newvalue['department_id']) ? $newvalue['department_id'] : explode(',', $newvalue['department_id']);
        $newDepartmentNames = [];
        foreach ($newDepartmentIds as $id) {
            $newDepartmentNames[] = getDepartmentName($id);
        }
        $output .= '<td>' . implode(', ', $newDepartmentNames). '</td>';
     }     
     $output .= '<td>' . (data_get($newvalue, 'employee_id')) ?? '' . '</td>'; 
     $output .= '<td>' . getRoleName(data_get($newvalue, 'role_id')) ?? '' . '</td>';

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
            header("Content-Disposition: attachment; filename=LogEmployee.xls");
            header("Pragma: no-cache");
            header("Expires: 0");

            echo $output;
        }

        public function getSubDepartments($id) {
            $s_id = explode(',', $id);
            $subDepartments = SupDept::whereIn('id', $s_id)->pluck('departments')->toArray(); 
           
            $itemsArray = [];
            foreach ($subDepartments as $departments) {
                $itemsArray = array_merge($itemsArray, explode(',', $departments));
            }
            $itemsArray = array_filter(array_unique($itemsArray));
            
            $data = Department::whereIn('id', $itemsArray)->select('name', 'id')->get();
            
            return response()->json([
                'data' => $data ?? '',
            ]);
        }
        public function getSubDepartments1() {
            $departments = department::pluck('name');
            return response()->json([
                'department' => $departments ?? '',
            ]);
        }
}
