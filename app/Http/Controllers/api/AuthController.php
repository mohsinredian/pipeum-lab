<?php
namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Ticket;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use App\Models\ApiLog;
use App\Models\Subsidy;
use App\Models\ConfirmationSubsidyOptin;
use App\Helpers\CustomHelper;
use App\Models\IssueAsset;
use App\Models\InventorySerialNumberMapping;
use App\Models\ItemIssue;


class AuthController extends Controller
{
   public function register(Request $request)
   {
      $validator = Validator::make($request->all(),[
         'name' => 'required|string|max:255',
         'email' => 'required|string|email|max:255|unique:users',
         'password' => 'required|string|min:8'
      ]);

      if($validator->fails()){
         return response()->json($validator->errors());       
      }

      $user = User::create([
         'name' => $request->name,
         'email' => $request->email,
         'password' => Hash::make($request->password)
      ]);

      $token = $user->createToken('auth_token')->plainTextToken;      
      
      return response()
         ->json(['data' => $user,'access_token' => $token, 'token_type' => 'Bearer', ]);         
   }

   public function login(Request $request)
   {
      $validator = Validator::make($request->all(), [
         'email' => 'required|string|email',
         'password' => 'required|string|min:8'
      ]);

      if ($validator->fails()) {
         return response()->json([
               'success' => false,
               'message' => $validator->errors()
         ], 422);
      }

      if (Auth::attempt([
         'email' => $request->email,
         'password' => $request->password,
         'status' => '1'
      ])) {

         $user = Auth::user();
         $role = Role::find($user->role_id);

         // Delete old tokens (optional)
         $user->tokens()->delete();

         // Create new token
         $token = $user->createToken('apiToken')->plainTextToken;

         $data = [
               'status'    => 200,
               'name'      => $user->name,
               'role_id'   => $user->role_id,
               'role_name' => $role->name ?? '',
               'token'     => $token,
         ];

         return response()->json([
               'success' => true,
               'data'    => $data,
               'message' => 'Login successfully'
         ], 200);

      } else {

         return response()->json([
               'success' => false,
               'message' => 'Please check your email or password'
         ], 401);
      }
   }


   public function userdetails(Request $request)
   {
      $user_id=$request->user()->id;
      //$data=User::find($user_id);
      //$success['name']=$data->name;
      $success=User::find($user_id);
      $success['status'] = 200;
      //$success= DB::table('roles')->orderBy('id','desc')->get();
      
      if($success){
      $response=[
         'success'=>true,
         
         'data'=>$success,
         'message'=>'Record Found'

         ];

         return response()->json($response,200);
      }
      else 
      {
         $response=[
            'success'=>false,
            'status'=>404,
            'message'=>'Record Not Found'

         ];
         return response()->json($response,404);

      }
   }

   public function assigntasklist(Request $request)
   {
      
      $user_id=$request->user()->id;

      //dd($user_id);
      //$data=User::find($user_id);
      //$success['name']=$data->name;
      
      $success=DB::table('taks')
         ->join('service', 'taks.task_name', '=', 'service.id')
         ->join('divisions', 'taks.company_id', '=', 'divisions.id')
         ->join('locations', 'taks.location_id', '=', 'locations.id')
         ->join('users', 'taks.assigne_id', '=', 'users.id')
         ->join('tickets', 'tickets.task_id', '=', 'taks.id')
         ->select('service.name as service_name', 'divisions.name as division_name', 'locations.name as location_name', 'users.name as user_name', 'tickets.task_description', 'taks.role_id', 'taks.status', 'tickets.day', 'tickets.assigne_id','tickets.floor','tickets.start_date')
         ->where('taks.assigne_id', $user_id)->get();
      
      //$success= DB::table('roles')->orderBy('id','desc')->get();
      // dd($success);       
   
      if(count($success) != 0){
         $success['status'] = 200;
         $response=[
         'success'=>true,
         
         'data'=>$success,
         'message'=>'Record Found'

         ];
         return response()->json($response,200);
      }else {
      $response=[
         'success'=>false,
         'status'=>404,
         'message'=>'Record Not Found'

      ];
      return response()->json($response,404);

      }
   }

   public function assigntasktoday(Request $request)
   {      
      $user_id=$request->user()->id;
      //dd($user_id);
      //$data=User::find($user_id);
      //$success['name']=$data->name;
     // dd()
      $success=DB::table('taks')
         ->join('service', 'taks.task_name', '=', 'service.id')
         ->join('divisions', 'taks.company_id', '=', 'divisions.id')
         ->join('locations', 'taks.location_id', '=', 'locations.id')
         ->join('users', 'taks.assigne_id', '=', 'users.id')
         ->join('tickets', 'tickets.task_id', '=', 'taks.id')
         ->select('service.name as service_name', 'divisions.name as division_name', 'locations.name as location_name', 'users.name as user_name', 'tickets.task_description', 'taks.role_id', 'taks.status', 'tickets.day', 'tickets.start_date', 'tickets.id as ticketId', 'tickets.assigne_id','tickets.floor')
         ->where('taks.assigne_id', $user_id)
         ->where('tickets.start_date', Carbon::now()->format('y-m-d'))
         ->where('tickets.status', 1)
         ->get();
      
      //$success= DB::table('roles')->orderBy('id','desc')->get();
      //  dd($success);         
      
      if(count($success) != 0)
      {
         $success['status'] = 200;
         $response=[
         'success'=>true,
         
         'data'=>$success,
         'message'=>'Record Found'

         ];
         return response()->json($response,200);
      }
      else 
      {
         $response=[
            'success'=>false,
            'status'=>404,
            'message'=>'Record Not Found'

         ];
         return response()->json($response,404);
      }
   }

   public function assigntasktomorrow(Request $request)
   {      
      $user_id=$request->user()->id;
      //dd($user_id);
      //$data=User::find($user_id);
      //$success['name']=$data->name;
      $tomorrow = Carbon::tomorrow()->format('y-m-d');
      $success=DB::table('taks')
         ->join('service', 'taks.task_name', '=', 'service.id')
         ->join('divisions', 'taks.company_id', '=', 'divisions.id')
         ->join('locations', 'taks.location_id', '=', 'locations.id')
         ->join('users', 'taks.assigne_id', '=', 'users.id')
         ->join('tickets', 'tickets.task_id', '=', 'taks.id')
         ->select('service.name as service_name', 'divisions.name as division_name', 'locations.name as location_name', 'users.name as user_name', 'tickets.task_description', 'taks.role_id', 'taks.status', 'tickets.day', 'tickets.start_date', 'tickets.assigne_id')
         ->where('taks.assigne_id', $user_id)
         ->where('tickets.start_date', $tomorrow)
         ->where('tickets.status', 1)
         ->get();
         //$tomorrow = Carbon::tomorrow()->format('y-m-d');
      //$success= DB::table('roles')->orderBy('id','desc')->get();
      //dd($tomorrow);
      
   
      if(count($success) != 0){
         $success['status'] = 200;
         $response=[
         'success'=>true,
         
         'data'=>$success,
         'message'=>'Record Found'

      ];
      return response()->json($response,200);
      }else {
         $response=[
            'success'=>false,
            'status'=>404,
            'message'=>'Record Not Found'

         ];
         return response()->json($response,404);

      }
   }

   public function assigntaskcurrentmonth(Request $request)
   {      
      $user_id=$request->user()->id;
      //dd($user_id);
      //$data=User::find($user_id);
      //$success['name']=$data->name;
      $current_month = Carbon::now()->format('m');
      $current_year = Carbon::now()->year;
      
      $success=DB::table('taks')
         ->join('service', 'taks.task_name', '=', 'service.id')
         ->join('divisions', 'taks.company_id', '=', 'divisions.id')
         ->join('locations', 'taks.location_id', '=', 'locations.id')
         ->join('users', 'taks.assigne_id', '=', 'users.id')
         ->join('tickets', 'tickets.task_id', '=', 'taks.id')
         ->select('service.name as service_name', 'divisions.name as division_name', 'locations.name as location_name', 'users.name as user_name', 'tickets.task_description', 'taks.role_id', 'taks.status', 'tickets.day', 'tickets.start_date', 'tickets.assigne_id')
         ->where('taks.assigne_id', $user_id)
         ->whereMonth('tickets.start_date', '=', $current_month)
      //  ->whereRaw('MONTH(tickets.start_date) = '.$current_month)
         ->whereRaw('YEAR(tickets.start_date) = ?', [$current_year])
         ->where('tickets.status', 1)
         ->get();
         //$tomorrow = Carbon::tomorrow()->format('y-m-d');
      //$success= DB::table('roles')->orderBy('id','desc')->get();
      //dd($tomorrow);      
   
      if(count($success) != 0)
      {
         $success['status'] = 200;
         $response=[
         'success'=>true,
         
         'data'=>$success,
         'message'=>'Record Found'

         ];
         return response()->json($response,200);
      }
      else 
      {
         $response=[
         'success'=>false,
         'status'=>404,
         'message'=>'Record Not Found'

         ]  ;
         return response()->json($response,404);
      }
   }
   
 

   public function updatetodaystask(Request $request, $ticketId)
   {
     // dd($request);
      $validator = Validator::make($request->all(),[
         'img1' => 'required|mimes:png,jpeg,jpg',
         'img2' => 'mimes:png,jpeg,jpg',
         'img3' => 'mimes:png,jpeg,jpg',
         'img4' => 'mimes:png,jpeg,jpg',
         'img5' => 'mimes:png,jpeg,jpg',
         'img6' => 'mimes:png,jpeg,jpg',
         'img7' => 'mimes:png,jpeg,jpg',
         'img8' => 'mimes:png,jpeg,jpg'
      ]);

      if($validator->fails()){
         return response()->json(['success'=>false,'message'=>'Img1 is required and all files must be of type png, jpeg or jpg only.']);  
      }
      
      $user_id=$request->user()->id;
      
      $ticket = Ticket::find($ticketId);

      // dd($request->hasfile('img1'));

      if($request->hasfile('img1'))
      {
         $fname = 'Ticket'.$ticketId.'_photo1.'.$request->file('img1')->extension();  
         $fname = trim(str_replace(' ', '_', $fname));
         $request->file('img1')->move(public_path().'/TicketPhotos/Ticket'.$ticketId, $fname);
         $ticket->img1 = $fname;
      }   

      if($request->hasfile('img2'))
      {
         $fname = 'Ticket'.$ticketId.'_photo2.'.$request->file('img2')->extension();  
         $fname = trim(str_replace(' ', '_', $fname));
         $request->file('img2')->move(public_path().'/TicketPhotos/Ticket'.$ticketId, $fname);
         $ticket->img2 = $fname;
      }   

      if($request->hasfile('img3'))
      {
         $fname = 'Ticket'.$ticketId.'_photo3.'.$request->file('img3')->extension();  
         $fname = trim(str_replace(' ', '_', $fname));
         $request->file('img3')->move(public_path().'/TicketPhotos/Ticket'.$ticketId, $fname);
         $ticket->img3 = $fname;
      }   

      if($request->hasfile('img4'))
      {
         $fname = 'Ticket'.$ticketId.'_photo4.'.$request->file('img4')->extension();  
         $fname = trim(str_replace(' ', '_', $fname));
         $request->file('img4')->move(public_path().'/TicketPhotos/Ticket'.$ticketId, $fname);
         $ticket->img4 = $fname;
      }   

      if($request->hasfile('img5'))
      {
         $fname = 'Ticket'.$ticketId.'_photo5.'.$request->file('img5')->extension();  
         $fname = trim(str_replace(' ', '_', $fname));
         $request->file('img5')->move(public_path().'/TicketPhotos/Ticket'.$ticketId, $fname);
         $ticket->img5 = $fname;
      }   

      if($request->hasfile('img6'))
      {
         $fname = 'Ticket'.$ticketId.'_photo6.'.$request->file('img6')->extension();  
         $fname = trim(str_replace(' ', '_', $fname));
         $request->file('img6')->move(public_path().'/TicketPhotos/Ticket'.$ticketId, $fname);
         $ticket->img6 = $fname;
      }   

      if($request->hasfile('img7'))
      {
         $fname = 'Ticket'.$ticketId.'_photo7.'.$request->file('img7')->extension();  
         $fname = trim(str_replace(' ', '_', $fname));
         $request->file('img7')->move(public_path().'/TicketPhotos/Ticket'.$ticketId, $fname);
         $ticket->img7 = $fname;
      } 
      if($request->hasfile('img8'))
      {
         $fname = 'Ticket'.$ticketId.'_photo8.'.$request->file('img8')->extension();  
         $fname = trim(str_replace(' ', '_', $fname));
         $request->file('img8')->move(public_path().'/TicketPhotos/Ticket'.$ticketId, $fname);
         $ticket->img8 = $fname;
      } 

      $ticket->status = 0;     

      // dd(count($openTickets));          
      
      if($ticket->save())
      {         
         // $openTickets = Ticket::where('task_id', $ticket->task_id)->where('status', 1)->get();

         // if(count($openTickets)==0)
         // {
         //    $task = Task::find($ticket->task_id);
         //    $task->status = 0;
         //    $task->save();
         // }

         $response=[
         'success'=>true,
         
         'data'=>$ticket,
         'message'=>'Task Updated Successfully!'

         ];
         return response()->json($response,200);
      }
      else 
      {
         $response=[
            'success'=>false,
            'status'=>404,
            'message'=>'Record Not Found!'

         ];
         return response()->json($response,404);
      }
   }

   public function closedtasks(Request $request)
   {
      $user_id=$request->user()->id;

      $success=DB::table('taks')
         ->join('service', 'taks.task_name', '=', 'service.id')
         ->join('divisions', 'taks.company_id', '=', 'divisions.id')
         ->join('locations', 'taks.location_id', '=', 'locations.id')
         ->join('users', 'taks.assigne_id', '=', 'users.id')
         ->join('tickets', 'tickets.task_id', '=', 'taks.id')
         ->select('service.name as service_name', 'divisions.name as division_name', 'locations.name as location_name', 'users.name as user_name', 'tickets.task_description', 'taks.role_id', 'taks.status as taskStatus', 'tickets.day', 'tickets.start_date', 'tickets.id as ticketId', 'tickets.status as ticketStatus', 'tickets.assigne_id')
         ->where('taks.assigne_id', $user_id)
         ->where('tickets.start_date', Carbon::now()->format('y-m-d'))
         ->where('tickets.status', 0)
         ->get();
            
      if(count($success) != 0)
      {
         $success['status'] = 200;
         $response=[
         'success'=>true,
         
         'data'=>$success,
         'message'=>'Record Found'

         ];
         return response()->json($response,200);
      }
      else 
      {
         $response=[
            'success'=>false,
            'status'=>404,
            'message'=>'Record Not Found'

         ];
         return response()->json($response,404);
      }
      
   }


   public function managetasktoday(Request $request)
   {      
      $user_id=$request->user()->id;
      $role_id=$request->user()->role_id;
      if($role_id==15)
      {
      $success=DB::table('taks')
         ->join('service', 'taks.task_name', '=', 'service.id')
         ->join('locations', 'taks.location_id', '=', 'locations.id')         
         ->join('tickets', 'tickets.task_id', '=', 'taks.id')
         ->join('divisions', 'tickets.company_id', '=', 'divisions.id')
         ->join('roles', 'roles.id', '=', 'taks.role_id')
         ->join('users', 'users.id', '=', 'tickets.assigne_id')
         ->select('divisions.name as divisions_name', 'locations.name as location', 'users.name as Task assigned to','service.name as service_name',  'tickets.task_description', 'taks.role_id','roles.name as role name', 'taks.status as taskStatus', 'tickets.day', 'tickets.start_date', 'tickets.id as ticketId', 'tickets.status as ticketStatus', 'tickets.assigne_id as ticketsassigne')
         
         ->where('tickets.start_date', Carbon::now()->format('y-m-d'))
         ->where('tickets.status', 0)
         ->get();
      
      //$success= DB::table('roles')->orderBy('id','desc')->get();
       //dd($success);         
      
      if(count($success) != 0)
      {
         $success['status'] = 200;
         $response=[
         'success'=>true,
         
         'data'=>$success,
         'message'=>'Record Found'

         ];
         return response()->json($response,200);
      }
      else 
      {
         $response=[
            'success'=>false,
            'status'=>404,
            'message'=>'Record Not Found'

         ];
         return response()->json($response,404);
      }
   }else{

         $response=[
            'success'=>false,
            'status'=>404,
            'message'=>'Record Not Found!API work fpr only office coordinator'
   
         ];
         return response()->json($response,404);
   
      }
   }


   public function managetaskstatus(Request $request, $ticketId)
   {
     
      $user_id=$request->user()->id;
      $role_id=$request->user()->role_id;
      if($role_id==15)
      {
     
      $ticket = Ticket::where('id', $ticketId)->where('status', 0)->first();

      //dd($ticket);
     

      $ticket->oc_status = 0;     

      // dd(count($openTickets));          
      
      if($ticket->save())
      {         
         // $openTickets = Ticket::where('task_id', $ticket->task_id)->where('status', 1)->get();

         // if(count($openTickets)==0)
         // {
         //    $task = Task::find($ticket->task_id);
         //    $task->status = 0;
         //    $task->save();
         // }

         $response=[
         'success'=>true,
         
         'data'=>$ticket,
         'message'=>'Task Updated Successfully!'

         ];
         return response()->json($response,200);
      }
      else 
      {
         $response=[
            'success'=>false,
            'status'=>404,
            'message'=>'Record Not Found!'

         ];
         return response()->json($response,404);
      }
   }else{

      $response=[
         'success'=>false,
         'status'=>404,
         'message'=>'Record Not Found!API work for only  office coordinator'

      ];
      return response()->json($response,404);

   }
   }

   public function logout(Request $request)
   {
      if($request->user()->currentAccessToken()->delete()){

      
         return response()->json(['success'=>true,'message'=>'logout successfully','data'=>[]]);
      }else{

         
         return response()->json(['success'=>false,'message'=>'Try Again']);
      }
   }


   public function manageupdatetodaystask(Request $request, $ticketId)
   {
     
      $user_id=$request->user()->id;
      
      $ticket = Ticket::find($ticketId);

     

     // $ticket->status = 0;     

      // dd(count($openTickets));          
      
      if($ticket->status == 0)
      {         
         $openTickets = Ticket::where('id', $ticket->id)->where('status', 0)->get();

         if(count($openTickets)==0)
         {
            $task = Task::find($ticket->task_id);
            $task->oc_status = 0;
            $task->save();
         }

         $response=[
         'success'=>true,
         
         'data'=>$ticket,
         'message'=>'Task Updated Successfully!'

         ];
         return response()->json($response,200);
      }
      else 
      {
         $response=[
            'success'=>false,
            'status'=>404,
            'message'=>'Record Not Found!'

         ];
         return response()->json($response,404);
      }
   }

}