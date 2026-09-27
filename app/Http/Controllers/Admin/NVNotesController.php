<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Validator;
use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use App\Models\Location;
use App\Models\Service;
use App\Models\NVNotes;
use App\Models\Division;
use Silber\Bouncer\Database\Role;
use Config;
use Illuminate\Support\Facades\DB;
use Exception;
use Hash;
use DateTime;
use DatePeriod;
use DateInterval;
use PDF;

class NVNotesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put("active", "create_Notes");

            return $next($request);
        });
    }
    public function create_Notes(Request $request)
    {
        $user = \Auth()->user();
       
        $emp = Employee::select("id", "name", 'department_id','division_id')
            ->where("status", 1)->where('user_id', $user->id)
            ->first();
        // dd($emp);
        $departments = Department::select("id", "name")
            ->where("status", 1)->where('id', $emp->department_id)
            ->get();
            $divisions = Division::select("id", "name")
            ->where("status", 1)->where('id', $emp->division_id)
            ->get();
           
        return view("admin.Notes.create", compact("departments",'divisions'));
    }
    public function store_Notes(Request $request)
    {
        try {
            $request_input = $request->except("_token");
            $dept_id = $request_input["dept_id"];
            $subject = $request->subject;
            $status = $request->status;
            $draft = $request->draft;
            // dd( $subject);
            if($status == "submit_nv"){
            if (
                NVNotes::where(["dept_id" => $dept_id])->exists()
            ) {
                $rules = [
                    "dept_id" => "required",
                    "prop_no" => "required||numeric",
                    "sub_line" => "required",
                    // "subject" => "required",
                    // "upload_docs" => "required",
                ];

                $messages = [
                    "dept_id.required" => "Please enter department name",
                    "prop_no.required" => "Please enter proposal number",
                    "sub_line.required" => "Please enter subject line",
                    // "subject.required" => "Please enter content",
                    // "upload_docs.required" => "Please choose the document",
                ];
                $validator = Validator::make($request_input, $rules, $messages);
                if ($validator->fails()) {
                    $response["msg"] = $validator->errors()->toArray();
                    $response["result"] = "error";
                } else {
                    if ($request->hasFile('upload_docs')) {
                        $file = $request->file('upload_docs');
                        $extension = $file->getClientOriginalName();
                        $file->move('upload-docs/', $extension);
                        NVNotes::create([
                            "dept_id" => $request_input["dept_id"],
                            "user_id" => \Auth::user()->id,
                            "prop_no" => $request_input["prop_no"],
                            "sub_line" => $request_input["sub_line"],
                            "division_id" => $request_input["division_id"],
                            "subject" => $subject??null,
                            'upload_docs' => $extension,
                      
                        ]);
                    }else{
                        $notes = NVNotes::create([
                            "dept_id" => $request_input["dept_id"],
                            "user_id" => \Auth::user()->id,
                            "prop_no" => $request_input["prop_no"],
                            "sub_line" => $request_input["sub_line"],
                            "division_id" => $request_input["division_id"],
                            "subject" => $subject??null,
                          
                        ]);
                    }

                    $response["result"] = "success";
                    $response["msg"] = "Note is created successfully";
                }
            } else {
                $rules = [
                    "dept_id" => "required",
                    "prop_no" => "required||numeric",
                    "sub_line" => "required",
                    // "subject" => "required",
                    // "upload_docs" => "required",
                ];

                $messages = [
                    "dept_id.required" => "Please enter department name",
                    "prop_no.required" => "Please enter proposal number",
                    "sub_line.required" => "Please enter subject line",
                    // "subject.required" => "Please enter content",
                    // "upload_docs.required" => "Please choose the document",
                ];

                $validator = Validator::make($request_input, $rules, $messages);
                if ($validator->fails()) {
                    $response["msg"] = $validator->errors()->toArray();
                    $response["result"] = "error";
                } else {
                    if ($request->hasFile('upload_docs')) {
                        $file = $request->file('upload_docs');
                        $extension = $file->getClientOriginalName();
                        $file->move('upload-docs/', $extension);
                        NVNotes::create([
                            "dept_id" => $request_input["dept_id"],
                            "user_id" => \Auth::user()->id,
                            "prop_no" => $request_input["prop_no"],
                            "sub_line" => $request_input["sub_line"],
                            "division_id" => $request_input["division_id"],
                            "subject" => $subject??null,
                            'upload_docs' => $extension,
                      
                        ]);
                    }else{
                        $notes = NVNotes::create([
                            "dept_id" => $request_input["dept_id"],
                            "user_id" => \Auth::user()->id,
                            "prop_no" => $request_input["prop_no"],
                            "sub_line" => $request_input["sub_line"],
                            "division_id" => $request_input["division_id"],
                            "subject" => $subject??null,
                        ]);
                    }

                    
                    // echo $location;
              
                    $response["result"] = "success";
                    $response["msg"] = "Note is created successfully";
                }
            }
             }elseif($status == "save_nv"){
                if (
                    NVNotes::where(["dept_id" => $dept_id])->exists()
                ) {
                    $rules = [
                        "dept_id" => "required",
                        "prop_no" => "required||numeric",
                        "sub_line" => "required",
                        // "subject" => "required",
                        // "upload_docs" => "required",
                    ];
    
                    $messages = [
                        "dept_id.required" => "Please enter department name",
                        "prop_no.required" => "Please enter proposal number",
                        "sub_line.required" => "Please enter subject line",
                        // "subject.required" => "Please enter content",
                        // "upload_docs.required" => "Please choose the document",
                    ];
                    $validator = Validator::make($request_input, $rules, $messages);
                    if ($validator->fails()) {
                        $response["msg"] = $validator->errors()->toArray();
                        $response["result"] = "error";
                    } else {
                        if ($request->hasFile('upload_docs')) {
                            $file = $request->file('upload_docs');
                            $extension = $file->getClientOriginalName();
                            $file->move('upload-docs/', $extension);
                            NVNotes::create([
                            "dept_id" => $request_input["dept_id"],
                            "user_id" => \Auth::user()->id,
                            "prop_no" => $request_input["prop_no"],
                            "sub_line" => $request_input["sub_line"],
                            "division_id" => $request_input["division_id"],
                            "subject" => $subject??null,
                            'upload_docs' => $extension,
                          
                            ]);
                        }else{
                            $notes = NVNotes::create([
                                "dept_id" => $request_input["dept_id"],
                                "user_id" => \Auth::user()->id,
                                "prop_no" => $request_input["prop_no"],
                                "sub_line" => $request_input["sub_line"],
                                "division_id" => $request_input["division_id"],
                                "subject" => $subject??null,
                              
                            ]);
                        }
    
                          
                   
                        $response["result"] = "success";
                        $response["msg"] = "Note is created successfully";
                    }
                } else {
                    $rules = [
                        "dept_id" => "required",
                        "prop_no" => "required||numeric",
                        "sub_line" => "required",
                        // "subject" => "required",
                        // "upload_docs" => "required",
                    ];
    
                    $messages = [
                        "dept_id.required" => "Please enter department name",
                        "prop_no.required" => "Please enter proposal number",
                        "sub_line.required" => "Please enter subject line",
                        // "subject.required" => "Please enter content",
                        // "upload_docs.required" => "Please choose the document",
                    ];
    
                    $validator = Validator::make($request_input, $rules, $messages);
                    if ($validator->fails()) {
                        $response["msg"] = $validator->errors()->toArray();
                        $response["result"] = "error";
                    } else {
                        if ($request->hasFile('upload_docs')) {
                            $file = $request->file('upload_docs');
                            $extension = $file->getClientOriginalName();
                            $file->move('upload-docs/', $extension);
                            NVNotes::create([
                                "dept_id" => $request_input["dept_id"],
                                "user_id" => \Auth::user()->id,
                                "prop_no" => $request_input["prop_no"],
                                "sub_line" => $request_input["sub_line"],
                                "division_id" => $request_input["division_id"],
                                "subject" => $subject??null,
                                'upload_docs' => $extension,
                          
                            ]);
                        }else{
                            $notes = NVNotes::create([
                                "dept_id" => $request_input["dept_id"],
                                "user_id" => \Auth::user()->id,
                                "prop_no" => $request_input["prop_no"],
                                "sub_line" => $request_input["sub_line"],
                                "division_id" => $request_input["division_id"],
                                "subject" => $subject??null,
                            ]);
                        }
    
                          
                        // echo $location;
                  
                        $response["result"] = "success";
                        $response["msg"] = "Note is created successfully";
                    }
                }
             }
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response["result"] = "failure";
            $response["msg"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function list_Notes(Request $request)
    {
        $user_id = \Auth::user()->id;
        $user_name =  \Auth::user()->name;
 
        $data = DB::table('notes')
                ->join('department','department.id','=','notes.dept_id')
                // ->join('users','users.id','=','notes.dept_id')
                ->where('notes.user_id',$user_id)
                ->select('notes.prop_no','notes.created_at','notes.sub_line','department.name as dept_name')
                ->get();
        // dd($data);
        
        return response()->json([
            "message"       => "Success",
            "data"          =>  $data ?? '',
            "user_name"     =>  $user_name ?? '',
            "code"          => 200
        ]);
    }


    public function edit_Notes(){

        $notes_id = request()->segment(4);
        $user = \Auth()->user();
        $note = NVNotes::where('id', $notes_id)->orderBy('id', 'desc')->first();

        if (empty($note)) {
            $notes = "";
        } else {

            if ($note->id != "") {

                $notes = NVNotes::select('*')->where('id', $note->id)->orderBy('id', 'desc')->first();
            } else {
                $notes = NVNotes::select('*')->where('id', $note->id)->orderBy('id', 'desc')->first();
            }
        }
       
        $emp = Employee::select("id", "name", 'department_id','division_id')
            ->where("status", 1)->where('user_id', $user->id)
            ->first();
        $departments = Department::select("id", "name")
            ->where("status", 1)->where('id', $emp->department_id)
            ->get();
            $divisions = Division::select("id", "name")
            ->where("status", 1)->where('id', $emp->division_id)
            ->get();
           
        return view("admin.Notes.edit", compact("departments",'divisions',"notes"));
    }
    public function update_Notes(){
        
    }
}
