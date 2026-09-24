<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Queue;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Session;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use App\Models\Chat;
use App\Models\Division;
use App\Models\Department;
use App\Models\Opex;
use App\Models\Capex;
use App\Models\User;
use App\Models\Location;
use App\Models\Workflow;
use App\Models\NVMaterial;
use App\Models\MateriBOQBulk;
use App\Models\ServiceBOQBulk;
use App\Models\Boqmaterial;
use App\Models\MasterMaterialboq;
use App\Models\MaterialDoc;
use App\Models\Nvsericestatus;
use App\Models\NeedValidation;
use App\Models\NVService;
use App\Models\ServiceDoc;
use App\Models\Otps;
use Illuminate\Support\Facades\Validator;
use Redirect;
// use App\imports\ProviderBulkImport;
use Config;
use DB;
use File;
use Exception;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Silber\Bouncer\Database\Role;
use Illuminate\Support\Facades\Mail;
use League\Csv\Reader;
use Illuminate\Support\Facades\Schema;
use App\Models\Notification;
// use Validator;
// use Maatwebsite\Excel\Facades\Excel;
// use League\Csv\Reader;
// use League\Csv\Statement;
// use Illuminate\Support\Facades\Validator;
use App\Services\ExcelImportService;
use App\Services\ActivityLogService;
// use Barryvdh\DomPDF\Facade\PDF;
// use App\PDFGenerate;
use PDF;


class NvMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('active', 'employees');

            return $next($request);
        });
    }
    // public function nv_material_list(Request $request)
    // {
    //     $user = \Auth()->user();
    //     if ($request->ajax()) {

    //         $employees = datatables()
    //             ->of(
    //                 Employee::with('division','location','role')->orderBy('id', 'desc')->get()
    //             )
    //             ->addColumn('role', function ($data) {

    //                 return $data->role->name;
    //             })
    //             // ->addColumn('role_id', function ($data) {

    //             //     return $data->role_id == '15'? 'Office Coordinator':'Supervisor';
    //             // })
    //             ->addColumn('status', function ($data) {

    //                 return $data->status == '1' ? 'Active' : 'Inactive';
    //             })
    //             ->addColumn('division', function ($data) {

    //                 return $data->division->name;
    //             })
    //             // ->addColumn('role', function ($data) {
    //             //       return $dat                    ["Asdfgh"
    //                 // if ($user->can('delete_division')) {
    //                 //     $button .= '<a href="javascript:;" data-id="'.$data->id.'" class="btn btn-sm btn-clean btn-icon delete_employee" title="Delete"><i class="fas fa-trash text-danger"></i></a>';
    //                 // }


    //                 return $button;
    //             })
    //             ->addIndexColumn()
    //             ->rawColumns(['action', 'status', 'division','location','role'])
    //             ->make(true);

    //         return $employees;
    //     }
    //     return view('admin.nvMaterial.list');
    // }


    public function downloadNvPdf($id, $userId,Request $request)
    {

        $user = \Auth()->user();
        $id = $request->id;
        $service_id = $id;
        $NeedValidation= NeedValidation::findOrFail($id);
     
            $material_import   =    MateriBOQBulk::where('nv_id',$id)->orderBy('id', 'desc')->get();
            $material_details =   NVMaterial::where('nv_id', $id)->first();
            $material_doc = MaterialDoc::where('service_id', $material_details->id)->first();
            $employees = Employee::where('department_id', $material_details->dept_id)->get();
            $chats = Chat::where('material_id',$id)->where('user_login_id',$user->id)->get();
            $service_import =     ServiceBOQBulk::where('nv_id',$id)->orderBy('id', 'desc')->get();

        $data = [
            'employees'=>$employees ?? '',
            'service_import'=>$service_import ?? '',
            'material_import'=>$material_import ?? '',
            'material_details'=> $material_details ?? '',
            'material_doc'=>$material_doc ?? '',
           
            'chats'=> $chats ?? ''
        ];
        // $coverPageView = view('admin.cover')->render();

        // Load the main content view
        $mainContentView = view('admin/nvMaterial/data', $data)->render();
    
        // Combine the cover page and main content
        $pdfContent = $mainContentView;
    
        // Generate PDF with different headers for the first page and other pages
        $pdf = PDF::loadHTML($pdfContent)->setPaper('A4', 'landscape');
    
        // Remove header and footer from subsequent pages
        $pdf->getDomPDF()->set_option('enable_html5_parser', true);
        $pdf->getDomPDF()->set_option('isPhpEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
    
        // Define the CSS rules to hide header and footer elements on subsequent pages
        $css = '@page :first { header { display: none; } footer { display: none; } }';
        $pdf->getDomPDF()->getOptions()->set(['isPhpEnabled' => true, 'isRemoteEnabled' => true, 'csslib' => $css]);
    
        return $pdf->download('NVMaterial.pdf');
    }

    public function downloadNvMaterialboqPdf($id, $userId,Request $request){
        $user = \Auth()->user();
        $id = $request->id;
        $service_id = $id;
        $NeedValidation= NeedValidation::findOrFail($id);
     
            $material_import   =    MateriBOQBulk::where('nv_id',$id)->orderBy('id', 'desc')->get();
            $material_details =   NVMaterial::where('nv_id', $id)->first();
            $material_doc = MaterialDoc::where('service_id', $material_details->id)->first();
            $employees = Employee::where('department_id', $material_details->dept_id)->get();
            $chats = Chat::where('material_id',$id)->where('user_login_id',$user->id)->get();
            $service_import =     ServiceBOQBulk::where('nv_id',$id)->orderBy('id', 'desc')->get();

        $data = [
            'employees'=>$employees ?? '',
            'service_import'=>$service_import ?? '',
            'material_import'=>$material_import ?? '',
            'material_details'=> $material_details ?? '',
            'material_doc'=>$material_doc ?? '',
           
            'chats'=> $chats ?? ''
        ];

        $mainContentView = view('admin/nvMaterial/MaterialBOQpdf', $data)->render();
    
        // Combine the cover page and main content
        $pdfContent = $mainContentView;
    
        // Generate PDF with different headers for the first page and other pages
        $pdf = PDF::loadHTML($pdfContent)->setPaper('A3', 'landscape');
    
        // Remove header and footer from subsequent pages
        $pdf->getDomPDF()->set_option('enable_html5_parser', true);
        $pdf->getDomPDF()->set_option('isPhpEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
    
        // Define the CSS rules to hide header and footer elements on subsequent pages
        $css = '@page :first { header { display: none; } footer { display: none; } }';
        $pdf->getDomPDF()->getOptions()->set(['isPhpEnabled' => true, 'isRemoteEnabled' => true, 'csslib' => $css]);
    
        return $pdf->download('NVMaterialBOQ.pdf');
    }
    
    public function downloadMaterialFiles($id, $userId,Request $request)
    {
                $user = \Auth()->user();
                $id = $request->id;

                // Find the material details and associated material documents
                $material_details = NVMaterial::where('nv_id', $id)->first();
                $material_doc = MaterialDoc::where('service_id', $material_details->id)->first();

                // Create a temporary zip file
                $zip = new ZipArchive();
                $zipFilename = 'MaterialAttachments.zip';
                $zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

                try {
                    // Add files to the zip archive if they exist
                    if (!empty($material_doc->previous_work_order)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->previous_work_order), $material_doc->previous_work_order);
                    }if (!empty($material_doc->derc_stakeholder_approvals)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->derc_stakeholder_approvals), $material_doc->derc_stakeholder_approvals);
                    }
                    if (!empty($material_doc->consumption_details)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->consumption_details), $material_doc->consumption_details);
                    }
                    if (!empty($material_doc->vend_quatation)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->vend_quatation), $material_doc->vend_quatation);
                    }
                    if (!empty($material_doc->photo_product)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->photo_product), $material_doc->photo_product);
                    }
                    if (!empty($material_doc->material_procurement)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->material_procurement), $material_doc->material_procurement);
                    }
                    if (!empty($material_doc->budget_for_both)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->budget_for_both), $material_doc->budget_for_both);
                    }
                    if (!empty($material_doc->others)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->others), $material_doc->others);
                    }
                    if (!empty($material_doc->new_product)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->new_product), $material_doc->new_product);
                    }
                    if (!empty($material_doc->cm_rate_ref)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->cm_rate_ref), $material_doc->cm_rate_ref);
                    }
                    if (!empty($material_doc->vendor_quatation)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->vendor_quatation), $material_doc->vendor_quatation);
                    }
                    if (!empty($material_doc->last_purchase_price)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->last_purchase_price), $material_doc->last_purchase_price);
                    }
                    if (!empty($material_doc->user_estimation)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->user_estimation), $material_doc->user_estimation);
                    }
                    if (!empty($material_doc->cost_calculation_for_service)) {
                        $zip->addFile(public_path('materials-doc/'.$material_doc->cost_calculation_for_service), $material_doc->cost_calculation_for_service);
                    }
                    $zip->close();

                    // Download the zip file
                    if (file_exists($zipFilename)) {
                        $headers = [
                            'Content-Type' => 'application/zip',
                            'Content-Disposition' => 'attachment; filename="' . $zipFilename . '"',
                        ];
                        $response = response()->download($zipFilename, 'MaterialAttachments.zip', $headers);

                        // Unset attachments data and delete the temporary zip file
                      //  unlink($zipFilename);

                        return $response;
                    } else {
                      
                        return redirect()->back()->with('Failed to create the zip file.');
                    }
                } catch (Exception $e) {
                    // Handle any exceptions that may occur during the zip file creation
                    // return response()->json(['error' => 'An error occurred while creating the zip file.']);
                    return redirect()->back()->with('Failed to create the zip file.');
                }
    }
           
   
    public function create_nv_material(Request $request)
    {
        
        $nv_id = request()->segment(4);
        $user = \Auth()->user();
        $nv = NeedValidation::where('id', $nv_id)->orderBy('id', 'desc')->first();

        
        $material_details = "";
        $material_doc = "";
        $material_import = MateriBOQBulk::where('nv_id', $nv_id)->orderBy('id', 'desc')->get();

        $service_import = ServiceBOQBulk::where('nv_id', $nv_id)->orderBy('id', 'desc')->get();
        $material_detail = NVMaterial::select('*')->where('nv_id', $nv_id)->orderBy('id', 'desc')->first();
    //  dd($material_detail);

        if (empty($material_detail)) {
            $material_details = "";
            $material_doc = "";
        } else {
            if ($material_detail->material_id != "") {

                $material_details = NVMaterial::select('*')->where('id', $material_detail->id)->orderBy('id', 'desc')->first();
               
                $material_doc = MaterialDoc::select('*')->where('service_id', $material_detail->id)->orderBy('id', 'desc')->first();
            } else {
                $material_details = NVMaterial::select('*')->where('id', $material_detail->id)->orderBy('id', 'desc')->first();
                $material_doc = MaterialDoc::select('*')->where('service_id', $material_detail->id)->orderBy('id', 'desc')->first();
                // dd($material_details);
            }
        }


        //  dd($material_details);
        $nv_year = NeedValidation::select('id', 'fiscal_year')->where('id', $nv_id)->first();
        $divisions = Division::select('id', 'name')->where('status', 1)->get();
        $employee = Employee::where('user_id', $user->id)->get();
        if (($user->role_id) != 1) {
            $departments = Department::select('id', 'name')->where('status', 1)->where('id', $employee[0]['department_id'])->get();
        } else {
            $departments = Department::select('id', 'name')->where('status', 1)->get();
        }

        $avlbgt = 0;
        $userid = \Auth()->user()->id;
        // dd($userdept);
        $lastNv = NeedValidation::select('id')
        ->where('id', '<', $nv_id)
        ->whereIn('service_id', [1, 2])
        ->where('budget_type', $nv->budget_type)
        ->where('user_id', $userid) // Use $userdept instead of $nv->userdept
        ->orderBy('id', 'desc')
        ->first();
//  dd($lastNv);    
    
    if (!isset($lastNv['id'])) {
            // If there are no existing nv entries, get avlbgt from capex or opex master table based on budget_type
            if ($nv->budget_type == 'OPEX') {
                $avlbgt = Opex::where("department_id", $employee[0]['department_id'])->pluck('initial_approved_budget');
            } elseif ($nv->budget_type == 'CAPEX') {
                $avlbgt = Capex::where("department_id", $employee[0]['department_id'])->pluck('capx_fy_two');
            }

            $avlbgt = (int)str_replace(['[', ']'], '', $avlbgt);
            //  dd($avlbgt,'ashu');
        }else{
        
            $checkmaterialid = NVMaterial::select('id','nv_id')->where('nv_id', $lastNv->getKey())->first();
            $checkserviceid = NVService::select('id')->where('nv_id', $lastNv->getKey())->first();
    
            $materialId =  $checkmaterialid ?  $checkmaterialid->id : null;
            $serviceId =  $checkserviceid ?  $checkserviceid->id : null;
    
            //  dd($materialId, $serviceId);
            $nv_status = Nvsericestatus::where('material_id', $materialId)
                                ->orWhere('service_id', $serviceId)
                                ->orderBy('id', 'desc')
                                ->first();
            // dd( $nv_status );
            // dd($nv_status->nv_id);
            
            if ($nv_status->nv_id ?? '') {
                // Check if hod_status is equal to 2
                if ($nv_status->hod_status == 2||$nv_status->ces_rew1_status == 2 || $nv_status-> ces_rew2_status == 2 || $nv_status->ces_rew3_status == 2 || $nv_status->ces_rew4_status == 2 || $nv_status->work_rew1_status == 2 || $nv_status->work_rew2_status == 2 ||$nv_status->work_rew3_status == 2 || $nv_status->work_rew4_status == 2 || $nv_status->approver_status == 2 || $nv_status->work_rew1dep2_status == 2 || $nv_status->work_rew2dep2_status == 2 || $nv_status->work_rew3dep2_status == 2 ||$nv_status->work_rew4dep2_status == 2 || $nv_status->approverdep2_status == 2 || $nv_status->work_rew1dep3_status == 2 || $nv_status->work_rew2dep3_status == 2 || $nv_status->work_rew3dep3_status == 2 || $nv_status->work_rew4dep3_status == 2 || $nv_status->approverdep3_status == 2 || $nv_status->work_rew1dep4_status == 2 || $nv_status->work_rew2dep4_status == 2 || $nv_status->work_rew3dep4_status == 2 || $nv_status->work_rew4dep4_status == 2 || $nv_status->approverdep4_status == 2 || $nv_status->work_rew1dep5_status == 2 || $nv_status->work_rew2dep5_status == 2 || $nv_status->work_rew3dep5_status == 2 || $nv_status->work_rew4dep5_status == 2 ||$nv_status->approverdep5_status == 2) {
                    // dd('helo1');
                    // If there are existing nv entries, calculate avlbgt based on the last total_budget_both
                    // dd($nv_status->material_id);
                    if(isset($nv_status['material_id'])){
                        $lastavlBudget = NVMaterial::where('nv_id', $lastNv->id)->sum('budget_avl');
                        $avlbgt = (int)$lastavlBudget;
                      
                    }
                    if(isset($nv_status['service_id'])){
                        $lastavlBudget = NVService::where('nv_id', $lastNv->id)->sum('budget_available');
                        $avlbgt = (int)$lastavlBudget;
                     
                    }
                }
                    else{
                    
                        if(isset($nv_status['service_id'])){
                            $lastTotalBudgetBoth = NVService::where('nv_id', $lastNv->id)->sum('total_buget');
                            $lastavlBudget = NVService::where('nv_id', $lastNv->id)->sum('budget_available');
                            $finalamount = (int)$lastavlBudget - (int)$lastTotalBudgetBoth;
                            $avlbgt = (int)$finalamount;
                            // dd('hello here');
                        // echo('ashu1');

                    }if(isset($nv_status['material_id'])){
                        
                        $lastTotalBudgetBoth = NVMaterial::where('nv_id', $lastNv->id)->sum('total_budget_both');
                        $lastavlBudget = NVMaterial::where('nv_id', $lastNv->id)->sum('budget_avl');
                        $finalamount = (int)$lastavlBudget - (int)$lastTotalBudgetBoth;
                        $avlbgt = (int)$finalamount;
                        // echo('ashu');
                }
            }
            
        }
    }

       

        $locations = Location::select('id', 'name')->where('status', 1)->get();
        $role = Role::select('id', 'title',)->where('id', '!=', 1)->get();
        return view('admin.nvMaterial.create', compact('divisions', 'locations', 'material_import', 'service_import', 'role', 'departments', 'material_details', 'material_doc', 'nv', 'nv_year','avlbgt'));
    }
    public function edit_material($id,$year)
    {
        $data = MateriBOQBulk::where(['id' => $id])->first();
        $materialCodes = MateriBOQBulk::pluck('material_code')->toArray();
        $nv_material = NeedValidation::where('id', $data->nv_id)->first();
        $fiscal = explode("-",$year);
        $yearCnt = count($fiscal);
        $fiscalArr = array();
        if(isset($fiscal[0])) {
            $fiscalArr['fiscal1'] = $fiscal[0].'-'.$fiscal[0]+1;

        }
        if(isset($fiscal[1])) {
            $fiscalArr['fiscal2'] = $fiscal[1].'-'.$fiscal[1]+1;
            
        }
        if(isset($fiscal[2])) {
            $fiscalArr['fiscal3'] = $fiscal[2].'-'.$fiscal[2]+1;
            
        }
        return view('admin.nvMaterial.edit', compact('data', 'materialCodes', 'nv_material','fiscalArr'));
    }

    public function delete_material($id)
    {
        try {
            $floor = MateriBOQBulk::findOrFail($id);
            $floor->delete();
            
            $response['result'] = 'success';
            $response['msg'] = 'Material Deleted';
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function delete_all_materials($id){
        try {
            $allmaterial = MateriBOQBulk::where('nv_id', $id);
            $count = $allmaterial->count(); // Count the number of records affected
    
            if ($count > 0) {
                $allmaterial->delete();
                $response['result'] = 'success';
                $response['msg'] = 'All materials related to this nv is deleted.';
            } else {
                $response['result'] = 'failure';
                $response['msg'] = 'No materials found for nv_id '.$id.'.';
            }
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }
    
        return response()->json($response);
    }

    public function delete_all_service($id)
    {
        try {
            $allservices = ServiceBOQBulk::where('nv_id', $id);
            $count = $allservices->count(); // Count the number of records affected
    
            if ($count > 0) {
                $allservices->delete();
                $response['result'] = 'success';
                $response['msg'] = 'All services related to this nv is deleted.';
            } else {
                $response['result'] = 'failure';
                $response['msg'] = 'No services found for nv_id '.$id.'.';
            }
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }
    
        return response()->json($response);
    }

    public function delete_service($id)
    {
        try {
            $service = ServiceBOQBulk::findOrFail($id);
            $service->delete();
            
            $response['result'] = 'success';
            $response['msg'] = 'Service Deleted';
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }

    //ayu
    public function fetchMaterialData(Request $request)
    {
        // dd('hi');
        $key = $request->key;
        $check_data = MateriBOQBulk::where('material_code', 'like', $key . '%')->select(
            'material_code',
            'material_short_text',
            'uom',
            'rate',
            'quantity',
            'amount',
            'april',
            'may',
            'june',
            'july',
            'august',
            'september',
            'oct',
            'nov',
            'dec',
            'jan',
            'feb',
            'march'
        )->first();
        // dd($check_data);
        $response = array();

        return response()->json($check_data);
    }
    public function edit_service($id)
    {
        //  $company = Division::select('id', 'name')->get();
        $data = ServiceBOQBulk::where(['id' => $id])->first();
        $nv_service = NeedValidation::where('id', $data->nv_id)->first();
        $serviceCodes = ServiceBOQBulk::pluck('service_code')->toArray();
        $escapedServiceCodes = [];

        foreach ($serviceCodes as $code) {
            if (is_string($code)) {
                $escapedServiceCodes[] = htmlspecialchars($code);
            } else {
                $escapedServiceCodes[] = $code;
            }
        }

        // return view('admin.nvMaterial.edit_service')->with(['data' => $data]);
        return view('admin.nvMaterial.edit_service', compact('data', 'serviceCodes','nv_service'));
    }
    public function fetchServiceData(Request $request)
    {
        // dd('hi');
        $key = $request->key;
        $check_data = ServiceBOQBulk::where('service_code', 'like', $key . '%')->select(
            'service_code',
            'description',
            'uom',
            'rate',
            'qty',
            'amount'
        )->first();

        $response = array();

        return response()->json($check_data);
    }

    public function update_material(Request $request)
    {
        $nv_id = $request->nv_id;
        $company_id = $request->company_id;
        //    dd( $company_id);
        //    dd( $nv_id);
        
        
        $data =   MateriBOQBulk::find($request->mt_id);
        $data['file'] = $this->uploadFile($request, 'file', $request->mt_id);
        $data->update([
            
            'material_code' => $request->input('material_code'),
            'rate_reference' => $request->input('rate_reference'),
            'uom' => $request->input('uom'),
            'material_short_text' => $request->input('material_short_text'),
            'quantity' => $request->input('quantity'),
            'rate' => $request->input('rate'),
            'amount' => $request->input('amount'),
            'april1' => $request->input('april'),
            'may1' => $request->input('may'),
            'june1' => $request->input('june'),
            'july1' => $request->input('july'),
            'august1' => $request->input('august'),
            'september1' => $request->input('september'),
            'oct1' => $request->input('oct'),
            'nov1' => $request->input('nov'),
            'dec1' => $request->input('dec'),
            'jan1' => $request->input('jan'),
            'feb1' => $request->input('feb'),
            'march1' => $request->input('march'),
            'april2' => $request->input('april2'),
            'may2' => $request->input('may2'),
            'june2' => $request->input('june2'),
            'july2' => $request->input('july2'),
            'august2' => $request->input('august2'),
            'september2' => $request->input('september2'),
            'oct2' => $request->input('oct2'),
            'nov2' => $request->input('nov2'),
            'dec2' => $request->input('dec2'),
            'jan2' => $request->input('jan2'),
            'feb2' => $request->input('feb2'),
            'march2' => $request->input('march2'),
            'april3' => $request->input('april3'),
            'may3' => $request->input('may3'),
            'june3' => $request->input('june3'),
            'july3' => $request->input('july3'),
            'august3' => $request->input('august3'),
            'september3' => $request->input('september3'),
            'oct3' => $request->input('oct3'),
            'nov3' => $request->input('nov3'),
            'dec3' => $request->input('dec3'),
            'jan3' => $request->input('jan3'),
            'feb3' => $request->input('feb3'),
            'march3' => $request->input('march3'),
            //  'file'=> $file,            
        ]);
     

        // $url = '/admin/nv_material/create/' . $nv_id .'/'.$company_id;
        // return Redirect::to($url);
        Session::flash('message', 'MaterialBOQ updated successfully!');
        return redirect()->back()->with('success', 'MaterialBOQ updated successfully!');
    }
    public function update_service(Request $request)
    {

        $data =   ServiceBOQBulk::find($request->s_id);
        $data['file'] = $this->uploadFile($request, 'file', $request->s_id);
        $data->update([
            'service_code' => $request->input('service_code'),
            'uom' => $request->input('uom'),
            'description' => $request->input('description'),
            'qty' => $request->input('qty'),
            'rate' => $request->input('rate'),
            'amount' => $request->input('amount'),

            //  'file'=> $file,

        ]);
        Session::flash('message', 'ServiceBOQ updated successfully!');
        return redirect()->back()->with('success', 'ServiceBOQ updated successfully!');
    }

    public function store_nv_material(Request $request)
    {
        // dd($request->material_amount, $request->service_amount);
         $total_budget_both = intval(preg_replace('/[^\d.]/', '', $request->total_budget_both));
         $total_budget_service = intval(preg_replace('/[^\d.]/', '', $request->total_budget_service));
         $ser_budget_avl = intval(preg_replace('/[^\d.]/', '', $request->ser_budget_avl));
         $total_ser_amo = intval(preg_replace('/[^\d.]/', '', $request->total_ser_amo));
         $total_mat_mat = intval(preg_replace('/[^\d.]/', '', $request->total_mat_mat));
         $budget_avl = intval(preg_replace('/[^\d.]/', '', $request->budget_avl));
        //  dd($total_budget_both);
        try {
            $request_input = $request->except('_token');
            $status = $request->status;
            $draft = $request->draft;
            $just_Prop = $request->just_Prop;
            // dd( $just_Prop);
            $background = $request->background;
            // dd($status);
            // $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
            if($status == "submit_nv"){
                if (empty($request['service_id'])) {

                    $rules = [
                        // 'dop' => 'required|numeric|unique:tbl_material,dop,except,id',
                        // 'short_code' => 'required|string|max:10|unique:divisions,short_code,except,id',
                    ];
    
                    $messages = [
                        // 'dop.unique' => 'This DOP ref no has already been taken',
                        // 'name.max' => 'Division name should not be more than 50 characters',
                        // 'short_code.required' => 'Please enter division short code',
                        // 'short_code.max' => 'short code should not be more than 10 characters',
    
                    ];
                    $validator = Validator::make($request_input, $rules, $messages);
                    if ($validator->fails()) {
                        $response['msg'] = $validator->errors()->toArray();
                        $response['result'] = 'error';
                    } else {
                        // dd(implode(',', $request_input['imp_plan']));
    
                            $nvmaterial = NVMaterial::create([
                            'dept_id' => $request_input['dept_id'],
                            'nv_id' => $request_input['nv_id'],
                            'company_id' => $request_input['company_id'],
                            'user_id' => \Auth::user()->id,
                            'dop' => $request_input['dop'],
                            'proposal_name' => $request_input['proposal_name'],
                            'background' =>  $background,
                            'just_Prop' =>  $just_Prop,
    
                            'cost_trend_year1' => implode(',', $request_input['cost_trend_year1']),
                            'cost_trend_year2' => implode(',', $request_input['cost_trend_year2']),
                            'cost_trend_year3' => implode(',', $request_input['cost_trend_year3']),
    
                            'benefit' => $request_input['benefit'],
                            'implements_years' =>$request_input['implements_years'],
                            'imp_to' => implode(',', $request_input['imp_to']),
                            'imp_from' => implode(',', $request_input['imp_from']),
                            'imp_plan' => implode('.,', $request_input['imp_plan']),
                            //'prop_type' => $request_input['prop_type'],
                            'worktype' => $request_input['worktype'],
                            'scheme_no' => implode(',', $request_input['scheme_no']),
                            'scheme_des' => implode(',', $request_input['scheme_des']),
                            'scheme_type' => $request_input['scheme_type'],
                            'derc_ref_no' => $request_input['derc_ref_no'],
                            'derc_approval' => $request_input['derc_approval'],
                            'derc_app_date' => $request_input['derc_app_date'],
                                
                            // 'material_code' => implode(',', $request_input['material_code']),
                            // 'mat_des' => implode(',', $request_input['mat_des']),
                            // //   'mat_just'=>implode(',',$request_input['mat_just']),
                            // 'mat_group' => implode(',', $request_input['mat_group']),
                            // 'uom' => implode(',', $request_input['uom']),
                            // 'rate' => implode(',', $request_input['rate']),
                            // 'quantity' => implode(',', $request_input['quantity']),
                            // 'total_amount' => implode(',', $request_input['total_amount']),
                            // 'delivery_schedule' => implode(',', $request_input['delivery_schedule']),
    
                            // 'service_code' => implode(',', $request_input['service_code']),
                            // 'ser_des' => implode(',', $request_input['ser_des']),
                            //   'mat_just'=>implode(',',$request_input['mat_just']),
                            // 'ser_rate_ref' => implode(',', $request_input['ser_rate_ref']),
                            // 'ser_uom' => implode(',', $request_input['ser_uom']),
                            // 'ser_rate' => implode(',', $request_input['ser_rate']),
                            // 'ser_quantity' => implode(',', $request_input['ser_quantity']),
                            // 'ser_total_amount' => implode(',', $request_input['ser_total_amount']),
    
                            'budget_avl' => $budget_avl,
                            'ptr_mva' => $request_input['ptr_mva'] ?? null,
                            'dt_mva' => $request_input['dt_mva'] ?? null,
                            'ehv_line' => $request_input['ehv_line'] ?? null,
                            'ht_line' => $request_input['ht_line'] ?? null,
                            'lt_line' => $request_input['lt_line'] ?? null,
                            'root_cause_analysis' => $request_input['root_cause_analysis'],
                            'cause_analysis' => $request_input['cause_analysis'],
                            'special_remarks' => $request_input['special_remarks'],
                            // 'total_budget_material' => $request_input['total_budget_material'],
                            'prop_number' => $request_input['prop_number'],
                            'mode_award' => $request_input['mode_award'],
                            'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
                            'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
                            'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
                            'past_practice_follow' => $request_input['past_practice_follow'],
                            'amc_prop_start_date' => $request_input['amc_prop_start_date'],
                            'amc_prop_end_date' => $request_input['amc_prop_end_date'],
                            'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
                            'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
                            'estimate_amount_of_rr_charge' => $request_input['estimate_amount_of_rr_charge'],
                            'estimate_amount_other' => $request_input['estimate_amount_other'],
                            'total_budget_service' => $total_budget_service,
                            'total_budget_both' => $total_budget_both,
                            'cap_add' => $request_input['cars-first'] ?? null,
                            'ser_rel_nv' => $request_input['cars'],
                            'material_amount' => implode(',',$request_input['material_amount']),
                            'material_description' => implode(',',$request_input['material_description']),
                            'service_amount' => implode(',',$request_input['service_amount']),
                            'service_description'=> implode(',',$request_input['service_description']),
    
    
                        ]);
                        //  dd($nvmaterial);
                        //   dd($request_input['cap_add']);
                        //   $nvmaterialstatus = new NVmaterialstatus();
                        //   $nvmaterialstatus->service_id = $nvmaterial->id;
                        //   $nvmaterialstatus->save();
    
                        // $nvmaterialstatus = new Nvsericestatus();
                        // $nvmaterialstatus->material_id = $nvmaterial->id;
                        // $nvmaterialstatus->nv_id = $request_input['nv_id'];
                        // $nvmaterialstatus->derc_info = $request_input['prop_number']='1';
                        // $nvmaterialstatus->derc_info = $request_input['derc_ref_no']='1';
                        // $nvmaterialstatus->derc_info = $request_input['derc_approval']='1';
                        // $nvmaterialstatus->derc_info = $request_input['derc_app_date']='1';
                        // $nvmaterialstatus->save();
    
                        if (!empty($request_input['prop_number'])) {
                            $nvmaterialstatus = new Nvsericestatus();
                            $nvmaterialstatus->material_id = $nvmaterial->id;
                            $nvmaterialstatus->nv_id = $request_input['nv_id'];
                            $nvmaterialstatus->company_id = $request_input['company_id'];
                            $nvmaterialstatus->derc_info = $request_input['prop_number'] = '1';
                            $nvmaterialstatus->draft =  $draft;
                            $nvmaterialstatus->save();
                        } else {
                            $nvmaterialstatus = new Nvsericestatus();
                            $nvmaterialstatus->material_id = $nvmaterial->id;
                            $nvmaterialstatus->nv_id = $request_input['nv_id'];
                            $nvmaterialstatus->company_id = $request_input['company_id'];
                            $nvmaterialstatus->derc_info = $request_input['prop_number'] = '0';
                            $nvmaterialstatus->draft =  $draft;
                            $nvmaterialstatus->save();
                        }
    
    
                        if (!empty($nvmaterial)) {
                            $serviceId = $nvmaterial->id;
                            $data = [];
    
                            $data['service_id'] = $serviceId;
                            $data['previous_work_order'] = $this->uploadFile($request, 'previous_work_order', $serviceId);
                            $data['derc_stakeholder_approvals'] = $this->uploadFile($request, 'derc_stakeholder_approvals', $serviceId);
                            $data['consumption_details'] = $this->uploadFile($request, 'consumption_details', $serviceId);
                            $data['vend_quatation'] = $this->uploadFile($request, 'vend_quatation', $serviceId);
                            $data['photo_product'] = $this->uploadFile($request, 'photo_product', $serviceId);
                            $data['material_procurement'] = $this->uploadFile($request, 'material_procurement', $serviceId);
                            $data['budget_for_both'] = $this->uploadFile($request, 'budget_for_both', $serviceId);
                            $data['others'] = $this->uploadFile($request, 'others', $serviceId);
                            $data['new_product'] = $this->uploadFile($request, 'new_product', $serviceId);
                            $data['cm_rate_ref'] = $this->uploadFile($request, 'cm_rate_ref', $serviceId);
                            $data['vendor_quatation'] = $this->uploadFile($request, 'vendor_quatation', $serviceId);
                            $data['last_purchase_price'] = $this->uploadFile($request, 'last_purchase_price', $serviceId);
                            $data['user_estimation'] = $this->uploadFile($request, 'user_estimation', $serviceId);
                            $data['cost_calculation_for_service'] = $this->uploadFile($request, 'cost_calculation_for_service', $serviceId);
    
                            $data['created_by'] = \Auth::user()->id;
                            // dd($data);
                            $document = MaterialDoc::create($data);
                        }
                        $response['result'] = 'success';
                        $response['msg'] = 'NV Material Created';
                        if (Auth::user()->role_id == 9) {
                            //   $uploaded = 'As uploaded on NV';
                            $nv_id = $request->nv_id;
                            $user_id = Auth::user()->id;
                            $employee = Employee::where('user_id', $user_id)->first();
                            $department = Department::where("id", $employee->department_id)->first();
                            $rv1 = $department->dep_rew1;
                            $emp_rv1 = Employee::where('user_id', $department->dep_rew1)->first();
                            $username = $emp_rv1->name;
                            $to_emails = $emp_rv1->email;
    
                            $initiated_date = NVMaterial::where('user_id', $user_id)->where('nv_id', $nv_id)->first();
                            $initiated_by =  Employee::where('user_id', $initiated_date->user_id)->first();
    
                            // $employee_hod = Employee::where('user_id', $employee->report_to)->first();
                            // $username = $employee_hod->name;
                            // $to_emails = $employee_hod->email;
                            //   dd($to_emails);
                            //   $to_emails="hareram.y@redianglobal.com";
                            $p1 = "You have a new request that requires your approval:";
                            $p2 = "Please review the request and take appropriate action.";
                            $remark = "";
                            //  send mail 
                            Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'remark' => $remark ?? ''], function ($message) use ($to_emails) {
                                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                                $message->to($to_emails);
                                $message->cc('raushan@rediansoftware.com');
                                $message->subject("NV  New Task Assign");
                            });
                        }
                    }
                } else {
                    $service_id = $request['service_id'];
                    $nvm = NVMaterial::find($service_id);
                    $nv_status = Nvsericestatus::where('material_id', $nvm->id)->first();
    
                    // $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                    if ($nv_status->hod_status == 0) {
    
                        NVMaterial::find($service_id)->update([
                            'dept_id' => $request_input['dept_id'],
                            'nv_id' => $request_input['nv_id'],
                            'company_id' => $request_input['company_id'],
                            'user_id' => \Auth::user()->id,
                            'dop' => $request_input['dop'],
                            'proposal_name' => $request_input['proposal_name'],
                            'background' =>  $background,
                            'just_Prop' =>  $just_Prop,
    
                            'cost_trend_year1' => implode(',', $request_input['cost_trend_year1']),
                            'cost_trend_year2' => implode(',', $request_input['cost_trend_year2']),
                            'cost_trend_year3' => implode(',', $request_input['cost_trend_year3']),
    
                            'benefit' => $request_input['benefit'],
                            'implements_years' =>$request_input['implements_years'],
                            'imp_to' => implode(',', $request_input['imp_to']),
                            'imp_from' => implode(',', $request_input['imp_from']),
                            'imp_plan' => implode(',', $request_input['imp_plan']),
                            //  'prop_type' => $request_input['prop_type'],
                            'worktype' => $request_input['worktype'],
                            'scheme_no' => implode(',', $request_input['scheme_no']),
                            'scheme_des' => implode(',', $request_input['scheme_des']),
                            'scheme_type' => $request_input['scheme_type'],
                            'derc_ref_no' => $request_input['derc_ref_no'],
                            'derc_approval' => $request_input['derc_approval'],
                            'derc_app_date' => $request_input['derc_app_date'],
                            // 'material_code' => implode(',', $request_input['material_code']),
                            // 'mat_des' => implode(',', $request_input['mat_des']),
                            // // 'mat_just'=>implode(',',$request_input['mat_just']),
                            // 'mat_group' => implode(',', $request_input['mat_group']),
                            // 'uom' => implode(',', $request_input['uom']),
                            // 'rate' => implode(',', $request_input['rate']),
                            // 'quantity' => implode(',', $request_input['quantity']),
                            // 'total_amount' => implode(',', $request_input['total_amount']),
                            // 'delivery_schedule' => implode(',', $request_input['delivery_schedule']),
    
                            // 'service_code' => implode(',', $request_input['service_code']),
                            // 'ser_des' => implode(',', $request_input['ser_des']),
                            // //   'mat_just'=>implode(',',$request_input['mat_just']),
                            // 'ser_rate_ref' => implode(',', $request_input['ser_rate_ref']),
                            // 'ser_uom' => implode(',', $request_input['ser_uom']),
                            // 'ser_rate' => implode(',', $request_input['ser_rate']),
                            // 'ser_quantity' => implode(',', $request_input['ser_quantity']),
                            // 'ser_total_amount' => implode(',', $request_input['ser_total_amount']),
    
                            'budget_avl' => $budget_avl,
                            'ptr_mva' => $request_input['ptr_mva'] ?? null,
                            'dt_mva' => $request_input['dt_mva'] ?? null,
                            'ehv_line' => $request_input['ehv_line'] ?? null,
                            'ht_line' => $request_input['ht_line'] ?? null,
                            'lt_line' => $request_input['lt_line'] ?? null,
                            'root_cause_analysis' => $request_input['root_cause_analysis'],
                            'cause_analysis' => $request_input['cause_analysis'],
                            'special_remarks' => $request_input['special_remarks'],
                            // 'total_budget_material' => $request_input['total_budget_material'],
                            'prop_number' => $request_input['prop_number'],
                            'mode_award' => $request_input['mode_award'],
                            'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
                            'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
                            'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
                            'past_practice_follow' => $request_input['past_practice_follow'],
                            'amc_prop_start_date' => $request_input['amc_prop_start_date'],
                            'amc_prop_end_date' => $request_input['amc_prop_end_date'],
                            'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
                            'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
                            'estimate_amount_of_rr_charge' => $request_input['estimate_amount_of_rr_charge'],
                            'estimate_amount_other' => $request_input['estimate_amount_other'],
                            'total_budget_service' => $total_budget_service,
                            'total_budget_both' => $total_budget_both,
                            'cap_add' => $request['cars-first'] ?? null,
                            'ser_rel_nv' => $request['cars'],
                            'material_amount' => implode(',',$request_input['material_amount']),
                            'material_description' => implode(',',$request_input['material_description']),
                            'service_amount' => implode(',',$request_input['service_amount']),
                            'service_description'=> implode(',',$request_input['service_description']),
    
                        ]);
                        // dd( $nv_status->material_id);
                        // Nvsericestatus::find($mat_id )->update([
                        //     'draft' =>  1,
                        //    ]);
                           Nvsericestatus::where('material_id', $nv_status->material_id)
                          ->update(['draft' => $draft]);
                        // $nvmaterialstatus = new Nvmaterialstatus();
                        // $nvmaterialstatus->service_id = $service_id;
                        // $nvmaterialstatus->save();
    
                        // if (!empty($nvservice)) {
                        // $serviceId = $nvservice->id;
                        $data = [];
    
                        // $data['service_id'] = $serviceId;
                        //   $data['service_id'] = $serviceId;
                        $data['previous_work_order'] = $this->uploadFile($request, 'previous_work_order', $service_id);
                        $data['derc_stakeholder_approvals'] = $this->uploadFile($request, 'derc_stakeholder_approvals', $service_id);
                        $data['consumption_details'] = $this->uploadFile($request, 'consumption_details', $service_id);
                        $data['vend_quatation'] = $this->uploadFile($request, 'vend_quatation', $service_id);
                        $data['photo_product'] = $this->uploadFile($request, 'photo_product', $service_id);
                        $data['material_procurement'] = $this->uploadFile($request, 'material_procurement', $service_id);
                        $data['budget_for_both'] = $this->uploadFile($request, 'budget_for_both', $service_id);
                        $data['others'] = $this->uploadFile($request, 'others', $service_id);
                        $data['new_product'] = $this->uploadFile($request, 'new_product', $service_id);
                        $data['cm_rate_ref'] = $this->uploadFile($request, 'cm_rate_ref', $service_id);
                        $data['vendor_quatation'] = $this->uploadFile($request, 'vendor_quatation', $service_id);
                        $data['last_purchase_price'] = $this->uploadFile($request, 'last_purchase_price', $service_id);
                        $data['user_estimation'] = $this->uploadFile($request, 'user_estimation', $service_id);
                        $data['cost_calculation_for_service'] = $this->uploadFile($request, 'cost_calculation_for_service', $service_id);
                        $data['created_by'] = \Auth::user()->id;
                        // print_r($data);die;
                        //    $document = ServiceDoc::create($data);
                        // dd($data);
                        MaterialDoc::where('service_id', $service_id)->update($data);
                    } elseif (
                        $nv_status->rv1_status == 2 || $nv_status->rv2_status == 2 || $nv_status->rv3_status == 2 || $nv_status->rv4_status == 2 || $nv_status->hod_status == 2 ||$nv_status->ces_rew1_status == 2 ||  $nv_status->ces_rew2_status == 2 || $nv_status->ces_rew3_status == 2 || $nv_status->ces_rew4_status == 2 || $nv_status->work_rew1_status == 2 || $nv_status->work_rew2_status == 2 ||$nv_status->work_rew3_status == 2 || $nv_status->work_rew4_status == 2 || $nv_status->approver_status == 2 || $nv_status->work_rew1dep2_status == 2 || $nv_status->work_rew2dep2_status == 2 || $nv_status->work_rew3dep2_status == 2 ||$nv_status->work_rew4dep2_status == 2 || $nv_status->approverdep2_status == 2 || $nv_status->work_rew1dep3_status == 2 || $nv_status->work_rew2dep3_status == 2 || $nv_status->work_rew3dep3_status == 2 || $nv_status->work_rew4dep3_status == 2 || $nv_status->approverdep3_status == 2 || $nv_status->work_rew1dep4_status == 2 || $nv_status->work_rew2dep4_status == 2 || $nv_status->work_rew3dep4_status == 2 || $nv_status->work_rew4dep4_status == 2 || $nv_status->approverdep4_status == 2 || $nv_status->work_rew1dep5_status == 2 || $nv_status->work_rew2dep5_status == 2 || $nv_status->work_rew3dep5_status == 2 || $nv_status->work_rew4dep5_status == 2 ||$nv_status->approverdep5_status == 2
    
                    ) {
    
                        $nvmaterial = NVMaterial::create([
                            'dept_id' => $request_input['dept_id'],
                            'nv_id' => $request_input['nv_id'],
                            'company_id' => $request_input['company_id'],
                            'user_id' => \Auth::user()->id,
                            'dop' => $request_input['dop'],
                            'proposal_name' => $request_input['proposal_name'],
                            'background' =>  $background,
                            'just_Prop' =>  $just_Prop,
    
                            'cost_trend_year1' => implode(',', $request_input['cost_trend_year1']),
                            'cost_trend_year2' => implode(',', $request_input['cost_trend_year2']),
                            'cost_trend_year3' => implode(',', $request_input['cost_trend_year3']),
    
                            'benefit' => $request_input['benefit'],
                            'implements_years' =>$request_input['implements_years'],
                            'imp_to' => implode(',', $request_input['imp_to']),
                            'imp_from' => implode(',', $request_input['imp_from']),
                            'imp_plan' => implode('.,', $request_input['imp_plan']),
                            //'prop_type' => $request_input['prop_type'],
                            'worktype' => $request_input['worktype'],
                            'scheme_no' => implode(',', $request_input['scheme_no']),
                            'scheme_des' => implode(',', $request_input['scheme_des']),
                            'scheme_type' => $request_input['scheme_type'],
                            'derc_ref_no' => $request_input['derc_ref_no'],
                            'derc_approval' => $request_input['derc_approval'],
                            'derc_app_date' => $request_input['derc_app_date'],
                            'budget_avl' => $budget_avl,
                            'ptr_mva' => $request_input['ptr_mva'] ?? null,
                            'dt_mva' => $request_input['dt_mva'] ?? null,
                            'ehv_line' => $request_input['ehv_line'] ?? null,
                            'ht_line' => $request_input['ht_line'] ?? null,
                            'lt_line' => $request_input['lt_line'] ?? null,
                            'root_cause_analysis' => $request_input['root_cause_analysis'],
                            'cause_analysis' => $request_input['cause_analysis'],
                            'special_remarks' => $request_input['special_remarks'],
                            // 'total_budget_material' => $request_input['total_budget_material'],
                            'prop_number' => $request_input['prop_number'],
                            'mode_award' => $request_input['mode_award'],
                            'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
                            'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
                            'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
                            'past_practice_follow' => $request_input['past_practice_follow'],
                            'amc_prop_start_date' => $request_input['amc_prop_start_date'],
                            'amc_prop_end_date' => $request_input['amc_prop_end_date'],
                            'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
                            'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
                            'estimate_amount_of_rr_charge' => $request_input['estimate_amount_of_rr_charge'],
                            'estimate_amount_other' => $request_input['estimate_amount_other'],
                            'total_budget_service' => $total_budget_service,
                            'total_budget_both' => $total_budget_both,
                            'cap_add' => $request_input['cars-first'] ?? null,
                            'ser_rel_nv' => $request_input['cars'],
                            'material_amount' => implode(',',$request_input['material_amount']),
                            'material_description' => implode(',',$request_input['material_description']),
                            'service_amount' => implode(',',$request_input['service_amount']),
                            'service_description'=> implode(',',$request_input['service_description']),
    
    
                        ]);
    
                        if (!empty($request_input['prop_number'])) {
                            $nvmaterialstatus = new Nvsericestatus();
                            $nvmaterialstatus->material_id = $nvmaterial->id;
                            $nvmaterialstatus->nv_id = $request_input['nv_id'];
                            $nvmaterialstatus->company_id = $request_input['company_id'];
                            $nvmaterialstatus->derc_info = $request_input['prop_number'] = '1';
                            $nvmaterialstatus->draft = $draft;
                            $nvmaterialstatus->save();
                        } else {
                            $nvmaterialstatus = new Nvsericestatus();
                            $nvmaterialstatus->material_id = $nvmaterial->id;
                            $nvmaterialstatus->nv_id = $request_input['nv_id'];
                            $nvmaterialstatus->company_id = $request_input['company_id'];
                            $nvmaterialstatus->derc_info = $request_input['prop_number'] = '0';
                            $nvmaterialstatus->draft = $draft;
                            $nvmaterialstatus->save();
                        }
    
    
                        if (!empty($nvmaterial)) {
                            $serviceId = $nvmaterial->id;
                            $data = [];
    
                            $data['service_id'] = $serviceId;
                            $data['previous_work_order'] = $this->uploadFile($request, 'previous_work_order', $serviceId);
                            $data['derc_stakeholder_approvals'] = $this->uploadFile($request, 'derc_stakeholder_approvals', $serviceId);
                            $data['consumption_details'] = $this->uploadFile($request, 'consumption_details', $serviceId);
                            $data['vend_quatation'] = $this->uploadFile($request, 'vend_quatation', $serviceId);
                            $data['photo_product'] = $this->uploadFile($request, 'photo_product', $serviceId);
                            $data['material_procurement'] = $this->uploadFile($request, 'material_procurement', $serviceId);
                            $data['budget_for_both'] = $this->uploadFile($request, 'budget_for_both', $serviceId);
                            $data['others'] = $this->uploadFile($request, 'others', $serviceId);
                            $data['new_product'] = $this->uploadFile($request, 'new_product', $serviceId);
                            $data['cm_rate_ref'] = $this->uploadFile($request, 'cm_rate_ref', $serviceId);
                            $data['vendor_quatation'] = $this->uploadFile($request, 'vendor_quatation', $serviceId);
                            $data['last_purchase_price'] = $this->uploadFile($request, 'last_purchase_price', $serviceId);
                            $data['user_estimation'] = $this->uploadFile($request, 'user_estimation', $serviceId);
                            $data['cost_calculation_for_service'] = $this->uploadFile($request, 'cost_calculation_for_service', $serviceId);
    
                            $data['created_by'] = \Auth::user()->id;
    
                            $document = MaterialDoc::create($data);
                        }
                    }
                    //  }
                    $response['result'] = 'success';
                    $response['msg'] = 'NV Material Updated';
                    // if (Auth::user()->role_id == 9) {
                    //     //   $uploaded = 'As uploaded on NV';
                    //     $user_id = Auth::user()->id;
                    //     $employee = Employee::where('user_id', $user_id)->first();
                    //     $department = Department::where("id",$employee->department_id)->first();
                    //     $rv1 = $department->dep_rew1;
                    //     $emp_rv1 = Employee::where('user_id', $department->dep_rew1)->first();
                    //     $username = $emp_rv1->name;
                    //     $to_emails = $emp_rv1->email;
                    //     //   dd($to_emails);
                    //     //   $to_emails="hareram.y@redianglobal.com";
                    //     $p1 = "You have a new request that requires your approval:";
                    //     $p2 = "Please review the request and take appropriate action.";
                    //     $remark = "";
                    //     //  send mail 
                    //     Mail::send('emailtemp.doc_mail', ['username' => 'user', 'p1' => $p1, 'p2' => $p2, 'remark' => $remark ?? ''], function ($message) use ($to_emails) {
                    //         $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    //         $message->to($to_emails);
                    //         $message->cc('raushan@rediansoftware.com');
                    //         $message->subject("NV  New Task Assign");
                    //     });
                    // }
    
                }
            }elseif($status == "save_nv"){
                if (empty($request['service_id'])) {

                    $rules = [
                        // 'dop' => 'required|numeric|unique:tbl_material,dop,except,id',
                        // 'short_code' => 'required|string|max:10|unique:divisions,short_code,except,id',
                    ];
    
                    $messages = [
                        // 'dop.unique' => 'This DOP ref no has already been taken',
                        // 'name.max' => 'Division name should not be more than 50 characters',
                        // 'short_code.required' => 'Please enter division short code',
                        // 'short_code.max' => 'short code should not be more than 10 characters',
    
                    ];
                    $validator = Validator::make($request_input, $rules, $messages);
                    if ($validator->fails()) {
                        $response['msg'] = $validator->errors()->toArray();
                        $response['result'] = 'error';
                    } else {
    
                      
    
                        $nvmaterial = NVMaterial::create([
                            'dept_id' => $request_input['dept_id'],
                            'nv_id' => $request_input['nv_id'],
                            'company_id' => $request_input['company_id'],
                            'user_id' => \Auth::user()->id,
                            'dop' => $request_input['dop'],
                            'proposal_name' => $request_input['proposal_name'],
                            'background' =>  $background,
                            'just_Prop' =>  $just_Prop,
    
                            'cost_trend_year1' => implode(',', $request_input['cost_trend_year1']),
                            'cost_trend_year2' => implode(',', $request_input['cost_trend_year2']),
                            'cost_trend_year3' => implode(',', $request_input['cost_trend_year3']),
    
                            'benefit' => $request_input['benefit'],
                            'implements_years' =>$request_input['implements_years'],
                            'imp_to' => implode(',', $request_input['imp_to']),
                            'imp_from' => implode(',', $request_input['imp_from']),
                            'imp_plan' => implode(',', $request_input['imp_plan']),
                            //'prop_type' => $request_input['prop_type'],
                            'worktype' => $request_input['worktype'],
                            'scheme_no' => implode(',', $request_input['scheme_no']),
                            'scheme_des' => implode(',', $request_input['scheme_des']),
                            'scheme_type' => $request_input['scheme_type'],
                            'derc_ref_no' => $request_input['derc_ref_no'],
                            'derc_approval' => $request_input['derc_approval'],
                            'derc_app_date' => $request_input['derc_app_date'],
    
                            // 'material_code' => implode(',', $request_input['material_code']),
                            // 'mat_des' => implode(',', $request_input['mat_des']),
                            // //   'mat_just'=>implode(',',$request_input['mat_just']),
                            // 'mat_group' => implode(',', $request_input['mat_group']),
                            // 'uom' => implode(',', $request_input['uom']),
                            // 'rate' => implode(',', $request_input['rate']),
                            // 'quantity' => implode(',', $request_input['quantity']),
                            // 'total_amount' => implode(',', $request_input['total_amount']),
                            // 'delivery_schedule' => implode(',', $request_input['delivery_schedule']),
    
                            // 'service_code' => implode(',', $request_input['service_code']),
                            // 'ser_des' => implode(',', $request_input['ser_des']),
                            //   'mat_just'=>implode(',',$request_input['mat_just']),
                            // 'ser_rate_ref' => implode(',', $request_input['ser_rate_ref']),
                            // 'ser_uom' => implode(',', $request_input['ser_uom']),
                            // 'ser_rate' => implode(',', $request_input['ser_rate']),
                            // 'ser_quantity' => implode(',', $request_input['ser_quantity']),
                            // 'ser_total_amount' => implode(',', $request_input['ser_total_amount']),
    
                            'budget_avl' => $budget_avl,
                            'ptr_mva' => $request_input['ptr_mva'] ?? null,
                            'dt_mva' => $request_input['dt_mva'] ?? null,
                            'ehv_line' => $request_input['ehv_line'] ?? null,
                            'ht_line' => $request_input['ht_line'] ?? null,
                            'lt_line' => $request_input['lt_line'] ?? null,
                            'root_cause_analysis' => $request_input['root_cause_analysis'],
                            'cause_analysis' => $request_input['cause_analysis'],
                            'special_remarks' => $request_input['special_remarks'],
                            // 'total_budget_material' => $request_input['total_budget_material'],
                            'prop_number' => $request_input['prop_number'],
                            'mode_award' => $request_input['mode_award'],
                            'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
                            'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
                            'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
                            'past_practice_follow' => $request_input['past_practice_follow'],
                            'amc_prop_start_date' => $request_input['amc_prop_start_date'],
                            'amc_prop_end_date' => $request_input['amc_prop_end_date'],
                            'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
                            'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
                            'estimate_amount_of_rr_charge' => $request_input['estimate_amount_of_rr_charge'],
                            'estimate_amount_other' => $request_input['estimate_amount_other'],
                            'total_budget_service' => $total_budget_service,
                            'total_budget_both' => $total_budget_both,
                            'cap_add' => $request_input['cars-first'] ?? null,
                            'ser_rel_nv' => $request_input['cars'],
                            'material_amount' => implode(',', $request_input['material_amount']),
                            'material_description' => implode(',', $request_input['material_description']),
                            'service_amount' => implode(',', $request_input['service_amount']),
                            'service_description'=> implode(',', $request_input['service_description']),
    
    
                        ]);
                        //  dd($nvmaterial);
                        //   dd($request_input['cap_add']);
                        //   $nvmaterialstatus = new NVmaterialstatus();
                        //   $nvmaterialstatus->service_id = $nvmaterial->id;
                        //   $nvmaterialstatus->save();
    
                        // $nvmaterialstatus = new Nvsericestatus();
                        // $nvmaterialstatus->material_id = $nvmaterial->id;
                        // $nvmaterialstatus->nv_id = $request_input['nv_id'];
                        // $nvmaterialstatus->derc_info = $request_input['prop_number']='1';
                        // $nvmaterialstatus->derc_info = $request_input['derc_ref_no']='1';
                        // $nvmaterialstatus->derc_info = $request_input['derc_approval']='1';
                        // $nvmaterialstatus->derc_info = $request_input['derc_app_date']='1';
                        // $nvmaterialstatus->save();
    
                        if (!empty($request_input['prop_number'])) {
                            $nvmaterialstatus = new Nvsericestatus();
                            $nvmaterialstatus->material_id = $nvmaterial->id;
                            $nvmaterialstatus->nv_id = $request_input['nv_id'];
                            $nvmaterialstatus->company_id = $request_input['company_id'];
                            $nvmaterialstatus->derc_info = $request_input['prop_number'] = '1';
                            $nvmaterialstatus->draft =  $draft;
                            $nvmaterialstatus->save();
                        } else {
                            $nvmaterialstatus = new Nvsericestatus();
                            $nvmaterialstatus->material_id = $nvmaterial->id;
                            $nvmaterialstatus->nv_id = $request_input['nv_id'];
                            $nvmaterialstatus->company_id = $request_input['company_id'];
                            $nvmaterialstatus->derc_info = $request_input['prop_number'] = '0';
                            $nvmaterialstatus->draft =  $draft;
                            $nvmaterialstatus->save();
                        }
    
    
                        if (!empty($nvmaterial)) {
                            $serviceId = $nvmaterial->id;
                            $data = [];
    
                            $data['service_id'] = $serviceId;
                            $data['previous_work_order'] = $this->uploadFile($request, 'previous_work_order', $serviceId);
                            $data['derc_stakeholder_approvals'] = $this->uploadFile($request, 'derc_stakeholder_approvals', $serviceId);
                            $data['consumption_details'] = $this->uploadFile($request, 'consumption_details', $serviceId);
                            $data['vend_quatation'] = $this->uploadFile($request, 'vend_quatation', $serviceId);
                            $data['photo_product'] = $this->uploadFile($request, 'photo_product', $serviceId);
                            $data['material_procurement'] = $this->uploadFile($request, 'material_procurement', $serviceId);
                            $data['budget_for_both'] = $this->uploadFile($request, 'budget_for_both', $serviceId);
                            $data['others'] = $this->uploadFile($request, 'others', $serviceId);
                            $data['new_product'] = $this->uploadFile($request, 'new_product', $serviceId);
                            $data['cm_rate_ref'] = $this->uploadFile($request, 'cm_rate_ref', $serviceId);
                            $data['vendor_quatation'] = $this->uploadFile($request, 'vendor_quatation', $serviceId);
                            $data['last_purchase_price'] = $this->uploadFile($request, 'last_purchase_price', $serviceId);
                            $data['user_estimation'] = $this->uploadFile($request, 'user_estimation', $serviceId);
                            $data['cost_calculation_for_service'] = $this->uploadFile($request, 'cost_calculation_for_service', $serviceId);
    
                            $data['created_by'] = \Auth::user()->id;
                            // dd($data);
                            $document = MaterialDoc::create($data);
                        }
                        $response['result'] = 'success';
                        $response['msg'] = 'NV Material Created';
                        if (Auth::user()->role_id == 9) {
                            //   $uploaded = 'As uploaded on NV';
                            $nv_id = $request->nv_id;
                            $user_id = Auth::user()->id;
                            $employee = Employee::where('user_id', $user_id)->first();
                            $department = Department::where("id", $employee->department_id)->first();
                            $rv1 = $department->dep_rew1;
                            $emp_rv1 = Employee::where('user_id', $department->dep_rew1)->first();
                            $username = $emp_rv1->name;
                            $to_emails = $emp_rv1->email;
    
                            $initiated_date = NVMaterial::where('user_id', $user_id)->where('nv_id', $nv_id)->first();
                            $initiated_by =  Employee::where('user_id', $initiated_date->user_id)->first();
    
                            // $employee_hod = Employee::where('user_id', $employee->report_to)->first();
                            // $username = $employee_hod->name;
                            // $to_emails = $employee_hod->email;
                            //   dd($to_emails);
                            //   $to_emails="hareram.y@redianglobal.com";
                            $p1 = "You have a new request that requires your approval:";
                            $p2 = "Please review the request and take appropriate action.";
                            $remark = "";
                            //  send mail 
                            Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'remark' => $remark ?? ''], function ($message) use ($to_emails) {
                                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                                $message->to($to_emails);
                                $message->cc('raushan@rediansoftware.com');
                                $message->subject("NV  New Task Assign");
                            });
                        }
                    }
                } else {
                    $service_id = $request['service_id'];
                    $nvm = NVMaterial::find($service_id);
                    $nv_status = Nvsericestatus::where('material_id', $nvm->id)->first();
                    // dd($nv_status);
                    // unset($request_input['employee_id']);
    
                    // $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                    if ($nv_status->hod_status == 0) {
                        // dd( implode(',', $request_input['material_amount']),implode(',', $request_input['service_amount']),);
                    //    dd(implode(',', $request_input['imp_plan']));
                        NVMaterial::find($service_id)->update([
                            'dept_id' => $request_input['dept_id'],
                            'nv_id' => $request_input['nv_id'],
                            'company_id' => $request_input['company_id'],
                            'user_id' => \Auth::user()->id,
                            'dop' => $request_input['dop'],
                            'proposal_name' => $request_input['proposal_name'],
                            'background' =>  $background,
                            'just_Prop' =>  $just_Prop,
    
                            'cost_trend_year1' => implode(',', $request_input['cost_trend_year1']),
                            'cost_trend_year2' => implode(',', $request_input['cost_trend_year2']),
                            'cost_trend_year3' => implode(',', $request_input['cost_trend_year3']),
    
                            'benefit' => $request_input['benefit'],
                            'implements_years' =>$request_input['implements_years'],
                            'imp_to' => implode(',', $request_input['imp_to']),
                            'imp_from' => implode(',', $request_input['imp_from']),
                            'imp_plan' => implode(',', $request_input['imp_plan']),
                            //  'prop_type' => $request_input['prop_type'],
                            'worktype' => $request_input['worktype'],
                            'scheme_no' => implode(',', $request_input['scheme_no']),
                            'scheme_des' => implode(',', $request_input['scheme_des']),
                            'scheme_type' => $request_input['scheme_type'],
                            'derc_ref_no' => $request_input['derc_ref_no'],
                            'derc_approval' => $request_input['derc_approval'],
                            'derc_app_date' => $request_input['derc_app_date'],
                            // 'material_code' => implode(',', $request_input['material_code']),
                            // 'mat_des' => implode(',', $request_input['mat_des']),
                            // // 'mat_just'=>implode(',',$request_input['mat_just']),
                            // 'mat_group' => implode(',', $request_input['mat_group']),
                            // 'uom' => implode(',', $request_input['uom']),
                            // 'rate' => implode(',', $request_input['rate']),
                            // 'quantity' => implode(',', $request_input['quantity']),
                            // 'total_amount' => implode(',', $request_input['total_amount']),
                            // 'delivery_schedule' => implode(',', $request_input['delivery_schedule']),
    
                            // 'service_code' => implode(',', $request_input['service_code']),
                            // 'ser_des' => implode(',', $request_input['ser_des']),
                            // //   'mat_just'=>implode(',',$request_input['mat_just']),
                            // 'ser_rate_ref' => implode(',', $request_input['ser_rate_ref']),
                            // 'ser_uom' => implode(',', $request_input['ser_uom']),
                            // 'ser_rate' => implode(',', $request_input['ser_rate']),
                            // 'ser_quantity' => implode(',', $request_input['ser_quantity']),
                            // 'ser_total_amount' => implode(',', $request_input['ser_total_amount']),
    
                            'budget_avl' => $budget_avl,
                            'ptr_mva' => $request_input['ptr_mva'] ?? null,
                            'dt_mva' => $request_input['dt_mva'] ?? null,
                            'ehv_line' => $request_input['ehv_line'] ?? null,
                            'ht_line' => $request_input['ht_line'] ?? null,
                            'lt_line' => $request_input['lt_line'] ?? null,
                            'root_cause_analysis' => $request_input['root_cause_analysis'],
                            'cause_analysis' => $request_input['cause_analysis'],
                            'special_remarks' => $request_input['special_remarks'],
                            // 'total_budget_material' => $request_input['total_budget_material'],
                            'prop_number' => $request_input['prop_number'],
                            'mode_award' => $request_input['mode_award'],
                            'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
                            'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
                            'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
                            'past_practice_follow' => $request_input['past_practice_follow'],
                            'amc_prop_start_date' => $request_input['amc_prop_start_date'],
                            'amc_prop_end_date' => $request_input['amc_prop_end_date'],
                            'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
                            'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
                            'estimate_amount_of_rr_charge' => $request_input['estimate_amount_of_rr_charge'],
                            'estimate_amount_other' => $request_input['estimate_amount_other'],
                            'total_budget_service' => $total_budget_service,
                            'total_budget_both' => $total_budget_both,
                            'cap_add' => $request['cars-first'] ?? null,
                            'ser_rel_nv' => $request['cars'],
                            'material_amount' => implode(',', $request_input['material_amount']),
                            'material_description' => implode(',', $request_input['material_description']),
                            'service_amount' => implode(',', $request_input['service_amount']),
                            'service_description'=> implode(',', $request_input['service_description']),
    
                        ]);
                    
                        // dd($nv_status->material_id);
                        // Nvsericestatus::find($nv_status->material_id)->update([
                        //     'draft' =>  $draft,
                        //    ]);
                        Nvsericestatus::where('material_id', $nv_status->material_id)
                        ->update(['draft' => $draft]);
                        // $nvmaterialstatus = new Nvmaterialstatus();
                        // $nvmaterialstatus->service_id = $service_id;
                        // $nvmaterialstatus->save();
    
                        // if (!empty($nvservice)) {
                        // $serviceId = $nvservice->id;
                        $data = [];
    
                        // $data['service_id'] = $serviceId;
                        //   $data['service_id'] = $serviceId;
                        
                        // dd(implode(',',$request->others));
                        $data['previous_work_order'] = $this->uploadFile($request, 'previous_work_order', $service_id);
                        $data['derc_stakeholder_approvals'] = $this->uploadFile($request, 'derc_stakeholder_approvals', $service_id);
                        $data['consumption_details'] = $this->uploadFile($request, 'consumption_details', $service_id);
                        $data['vend_quatation'] = $this->uploadFile($request, 'vend_quatation', $service_id);
                        $data['photo_product'] = $this->uploadFile($request, 'photo_product', $service_id);
                        $data['material_procurement'] = $this->uploadFile($request, 'material_procurement', $service_id);
                        $data['budget_for_both'] = $this->uploadFile($request, 'budget_for_both', $service_id);
                         $data['others'] = $this->uploadFile($request, 'others', $service_id);
                        //  $data['others'] = implode(',',$request->others);
                        $data['new_product'] = $this->uploadFile($request, 'new_product', $service_id);
                        $data['cm_rate_ref'] = $this->uploadFile($request, 'cm_rate_ref', $service_id);
                        $data['vendor_quatation'] = $this->uploadFile($request, 'vendor_quatation', $service_id);
                        $data['last_purchase_price'] = $this->uploadFile($request, 'last_purchase_price', $service_id);
                        $data['user_estimation'] = $this->uploadFile($request, 'user_estimation', $service_id);
                        $data['cost_calculation_for_service'] = $this->uploadFile($request, 'cost_calculation_for_service', $service_id);
                        $data['created_by'] = \Auth::user()->id;
                        // print_r($data);die;
                        //    $document = ServiceDoc::create($data);
                        // dd($data);
                        MaterialDoc::where('service_id', $service_id)->update($data);
                    } elseif (
                        $nv_status->rv1_status == 2 || $nv_status->rv2_status == 2 || $nv_status->rv3_status == 2 || $nv_status->rv4_status == 2 || $nv_status->hod_status == 2 ||$nv_status->ces_rew1_status == 2 ||  $nv_status->ces_rew2_status == 2 || $nv_status->ces_rew3_status == 2 || $nv_status->ces_rew4_status == 2 || $nv_status->work_rew1_status == 2 || $nv_status->work_rew2_status == 2 ||$nv_status->work_rew3_status == 2 || $nv_status->work_rew4_status == 2 || $nv_status->approver_status == 2 || $nv_status->work_rew1dep2_status == 2 || $nv_status->work_rew2dep2_status == 2 || $nv_status->work_rew3dep2_status == 2 ||$nv_status->work_rew4dep2_status == 2 || $nv_status->approverdep2_status == 2 || $nv_status->work_rew1dep3_status == 2 || $nv_status->work_rew2dep3_status == 2 || $nv_status->work_rew3dep3_status == 2 || $nv_status->work_rew4dep3_status == 2 || $nv_status->approverdep3_status == 2 || $nv_status->work_rew1dep4_status == 2 || $nv_status->work_rew2dep4_status == 2 || $nv_status->work_rew3dep4_status == 2 || $nv_status->work_rew4dep4_status == 2 || $nv_status->approverdep4_status == 2 || $nv_status->work_rew1dep5_status == 2 || $nv_status->work_rew2dep5_status == 2 || $nv_status->work_rew3dep5_status == 2 || $nv_status->work_rew4dep5_status == 2 ||$nv_status->approverdep5_status == 2
    
                    ) {
    
                        $nvmaterial = NVMaterial::create([
                            'dept_id' => $request_input['dept_id'],
                            'nv_id' => $request_input['nv_id'],
                            'company_id' => $request_input['company_id'],
                            'user_id' => \Auth::user()->id,
                            'dop' => $request_input['dop'],
                            'proposal_name' => $request_input['proposal_name'],
                            'background' =>  $background,
                            'just_Prop' =>  $just_Prop,
    
                            'cost_trend_year1' => implode(',', $request_input['cost_trend_year1']),
                            'cost_trend_year2' => implode(',', $request_input['cost_trend_year2']),
                            'cost_trend_year3' => implode(',', $request_input['cost_trend_year3']),
    
                            'benefit' => $request_input['benefit'],
                            'implements_years' =>$request_input['implements_years'],
                            'imp_to' => implode(',', $request_input['imp_to']),
                            'imp_from' => implode(',', $request_input['imp_from']),
                            'imp_plan' => implode('.,', $request_input['imp_plan']),
                            //'prop_type' => $request_input['prop_type'],
                            'worktype' => $request_input['worktype'],
                            'scheme_no' => implode(',', $request_input['scheme_no']),
                            'scheme_des' => implode(',', $request_input['scheme_des']),
                            'scheme_type' => $request_input['scheme_type'],
                            'derc_ref_no' => $request_input['derc_ref_no'],
                            'derc_approval' => $request_input['derc_approval'],
                            'derc_app_date' => $request_input['derc_app_date'],
                            'budget_avl' => $budget_avl,
                            'ptr_mva' => $request_input['ptr_mva'] ?? null,
                            'dt_mva' => $request_input['dt_mva'] ?? null,
                            'ehv_line' => $request_input['ehv_line'] ?? null,
                            'ht_line' => $request_input['ht_line'] ?? null,
                            'lt_line' => $request_input['lt_line'] ?? null,
                            'root_cause_analysis' => $request_input['root_cause_analysis'],
                            'cause_analysis' => $request_input['cause_analysis'],
                            'special_remarks' => $request_input['special_remarks'],
                            // 'total_budget_material' => $request_input['total_budget_material'],
                            'prop_number' => $request_input['prop_number'],
                            'mode_award' => $request_input['mode_award'],
                            'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
                            'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
                            'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
                            'past_practice_follow' => $request_input['past_practice_follow'],
                            'amc_prop_start_date' => $request_input['amc_prop_start_date'],
                            'amc_prop_end_date' => $request_input['amc_prop_end_date'],
                            'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
                            'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
                            'estimate_amount_of_rr_charge' => $request_input['estimate_amount_of_rr_charge'],
                            'estimate_amount_other' => $request_input['estimate_amount_other'],
                            'total_budget_service' => $total_budget_service,
                            'total_budget_both' => $total_budget_both,
                            'cap_add' => $request_input['cars-first'] ?? null,
                            'ser_rel_nv' => $request_input['cars'],
                            'material_amount' => implode(',', $request_input['material_amount']),
                            'material_description' => implode(',', $request_input['material_description']),
                            'service_amount' => implode(',', $request_input['service_amount']),
                            'service_description'=> implode(',', $request_input['service_description']),
    
    
                        ]);
                       
    
                        if (!empty($request_input['prop_number'])) {
                            $nvmaterialstatus = new Nvsericestatus();
                            $nvmaterialstatus->material_id = $nvmaterial->id;
                            $nvmaterialstatus->nv_id = $request_input['nv_id'];
                            $nvmaterialstatus->company_id = $request_input['company_id'];
                            $nvmaterialstatus->derc_info = $request_input['prop_number'] = '1';
                            $nvmaterialstatus->draft =  $draft;
                            $nvmaterialstatus->save();
                        } else {
                            $nvmaterialstatus = new Nvsericestatus();
                            $nvmaterialstatus->material_id = $nvmaterial->id;
                            $nvmaterialstatus->nv_id = $request_input['nv_id'];
                            $nvmaterialstatus->company_id = $request_input['company_id'];
                            $nvmaterialstatus->derc_info = $request_input['prop_number'] = '0';
                            $nvmaterialstatus->draft =  $draft;
                            $nvmaterialstatus->save();
                        }
    
    
                        if (!empty($nvmaterial)) {
                            $serviceId = $nvmaterial->id;
                            $data = [];
    
                            $data['service_id'] = $serviceId;
                            $data['previous_work_order'] = $this->uploadFile($request, 'previous_work_order', $serviceId);
                            $data['derc_stakeholder_approvals'] = $this->uploadFile($request, 'derc_stakeholder_approvals', $serviceId);
                            $data['consumption_details'] = $this->uploadFile($request, 'consumption_details', $serviceId);
                            $data['vend_quatation'] = $this->uploadFile($request, 'vend_quatation', $serviceId);
                            $data['photo_product'] = $this->uploadFile($request, 'photo_product', $serviceId);
                            $data['material_procurement'] = $this->uploadFile($request, 'material_procurement', $serviceId);
                            $data['budget_for_both'] = $this->uploadFile($request, 'budget_for_both', $serviceId);
                            $data['others'] = implode(',',$this->uploadFile($request, 'others', $serviceId));
                            $data['new_product'] = $this->uploadFile($request, 'new_product', $serviceId);
                            $data['cm_rate_ref'] = $this->uploadFile($request, 'cm_rate_ref', $serviceId);
                            $data['vendor_quatation'] = $this->uploadFile($request, 'vendor_quatation', $serviceId);
                            $data['last_purchase_price'] = $this->uploadFile($request, 'last_purchase_price', $serviceId);
                            $data['user_estimation'] = $this->uploadFile($request, 'user_estimation', $serviceId);
                            $data['cost_calculation_for_service'] = $this->uploadFile($request, 'cost_calculation_for_service', $serviceId);
    
                            $data['created_by'] = \Auth::user()->id;
                            $document = MaterialDoc::create($data);
                        }
                    }
                    //  }
                    $response['result'] = 'success';
                    $response['msg'] = 'NV Material Updated';
                    // if (Auth::user()->role_id == 9) {
                    //     //   $uploaded = 'As uploaded on NV';
                    //     $user_id = Auth::user()->id;
                    //     $employee = Employee::where('user_id', $user_id)->first();
                    //     $department = Department::where("id",$employee->department_id)->first();
                    //     $rv1 = $department->dep_rew1;
                    //     $emp_rv1 = Employee::where('user_id', $department->dep_rew1)->first();
                    //     $username = $emp_rv1->name;
                    //     $to_emails = $emp_rv1->email;
                    //     //   dd($to_emails);
                    //     //   $to_emails="hareram.y@redianglobal.com";
                    //     $p1 = "You have a new request that requires your approval:";
                    //     $p2 = "Please review the request and take appropriate action.";
                    //     $remark = "";
                    //     //  send mail 
                    //     Mail::send('emailtemp.doc_mail', ['username' => 'user', 'p1' => $p1, 'p2' => $p2, 'remark' => $remark ?? ''], function ($message) use ($to_emails) {
                    //         $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    //         $message->to($to_emails);
                    //         $message->cc('raushan@rediansoftware.com');
                    //         $message->subject("NV  New Task Assign");
                    //     });
                    // }
    
                }
            }
          
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function uploadFile($request, $field, $serviceId)
    {
        $fileName = null;
        if ($request->hasFile($field)) {
            $file = $request->file($field);
            $extension = $file->getClientOriginalName();
            $fileName = time() . '.' . $extension;;
            $file->move('materials-doc/', $fileName);
        }
        return $fileName;
    }

    public function approvedByStatus(Request $request)
    {

        $user = \Auth()->user();
        $status = $request->status_id;
        $material_id = $request->material_id;
        $nv_id = $request->nv_id;
        $remark = $request->remark;
        $dop_ref_no = $request->dop_ref_no;
        $check_technology = $request->check_technology;
        $derc_info = $request->derc_info;
        // dd($derc_info);
        $employees = Employee::where("user_id", $user->id)->first();
        // $employeesss = Employee::where("department_id", $employees->department_id)->get();
        //  foreach ($employeesss as $employee) {
        //     array_push($user_id, $employee["user_id"]);
        // }

        $department = Department::where("id", $employees->department_id)->first();
        $hod = $department->dep_hod;
        $rv1 = $department->dep_rew1;
        $rv2 = $department->dep_rew2;
        $rv3 = $department->dep_rew3;
        $rv4 = $department->dep_rew4;

        $id0 = Workflow::where("id", 1)->first();
        $id1 = Workflow::skip(1)->first();
        $id2 = Workflow::skip(2)->first();
        $id3 = Workflow::skip(3)->first();
        $id4 = Workflow::skip(4)->first();
        $id5 = Workflow::skip(5)->first();
        // $workflow = Workflow::select('*')->orderBy('id', 'desc')->first();
        // $capexflow = Workflow::where("id",1)->first();
        // $capexflow = Workflow::where("bt_capex_email",$employees->email)->first();
        // $opexflow = Workflow::where("bt_opex_eamil",$employees->email)->first();
        // $cpmgflow = Workflow::where("cpmg_email",$employees->email)->first();
        // $ceon11flow = Workflow::where("ceo_nominee1_email",$employees->email)->first();
        // $ceoA1flow = Workflow::where("ceo_nominee2_apr1_email",$employees->email)->first();
        // $ceoA2flow = Workflow::where("ceo_nominee2_apr2_email",$employees->email)->first();
        // $ceoflow = Workflow::where("ceo_email",$employees->email)->first();
        $tble_material = NVMaterial::where('id', $material_id)->first();

        if ($status == 'Save') {

            $user_ids = $request['users_name'];
            $dd = implode(',', $user_ids);
            //    dd($dd);
            $chats = Chat::create([
                'user_login_id' => $user->id,
                'nv_id' => $request->nv_id,
                'dop_ref_no' => $request->dop_ref_no,
                'material_id' => $request->material_id,
                'user_id' => $dd,
                'message' => $request['editor1'],
            ]);
            //  dd(  $chats );
             $user_ids=[];
             $user_ids = explode(',', $dd);
             $nv_status = Chat::where('user_id', $user_ids)->get();
             foreach ($nv_status as $nv_status) {
                 array_push($user_ids, $nv_status["user_id"]);
             }
             foreach ($user_ids as $user_id) {
                 
                 $users = Employee::whereIn('user_id',$user_ids)->get(); 
             
             }
     
             foreach ($users as $key => $user) {
                   $p1 = "Your nv request has been approved:";
                     $p2 = "";
                  $to_emails=$user->email;
                    
           Mail::send('emailtemp.doc_mail', [ 'p1' => $p1, 'p2' => $p2], function ($message) use ($to_emails) {
                     $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                     $message->to($to_emails);
                     $message->cc('raushan@rediansoftware.com');
                     $message->subject("NV New Task Assign");
                 });   
                    
           }
           
         }

        if ($status == 'Approve') {
            $status = 1;
        } elseif ($status == 'Reject') {
            $status = 2;
        } else {
            $status = 0;
        }

        if (!empty($rv1) && $rv1 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'rv1_id' => $user->id,
                'rv1_status' => $status,
                'rv1_timestamp' => date('Y-m-d H:i'),
                'rv1_action_ip' => request()->ip(),
                'rv1_remark' => $remark
            ]);

            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->rv1_id)->first();
            $approved_date = Nvsericestatus::select('rv1_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->rv1_timestamp;

            if ($department->dep_rew2 === null) {
                $emp_rv = Employee::where('user_id', $department->dep_rew3)->first();

                if ($department->dep_rew3 === null) {
                    $emp_rv = Employee::where('user_id', $department->dep_rew4)->first();
                    if ($department->dep_rew4 === null) {
                        $emp_rv = Employee::where('user_id', $department->dep_hod)->first();
                    } else {
                        $emp_rv = Employee::where('user_id', $department->dep_rew_4)->first();
                    }
                } else {
                    $emp_rv = Employee::where('user_id', $department->dep_rew3)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id', $department->dep_rew2)->first();
            }

            // if($rv2 === null){
            //     $emp_rv = Employee::where('user_id', $department->dep_rew3)->first();
            // }else if($rv3 === null){
            //     $emp_rv = Employee::where('user_id', $department->dep_rew4)->first();
            //     }else if($rv4 === null){
            //         $emp_rv = Employee::where('user_id', $department->dep_hod)->first();

            // }else{
            //     $emp_rv = Employee::where('user_id', $department->dep_rew2)->first();
            // }

            // $to_emails = $emp_rv->email;

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($rv2) && $rv2 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'rv2_id' => $user->id,
                'rv2_status' => $status,
                'rv2_timestamp' => date('Y-m-d H:i'),
                'rv2_action_ip' => request()->ip(),
                'rv2_remark' => $remark
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->rv2_id)->first();
            $approved_date = Nvsericestatus::select('rv2_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->rv2_timestamp;

            if ($department->dep_rew3 === null) {
                $emp_rv = Employee::where('user_id', $department->dep_rew4)->first();
                if ($department->dep_rew4 === null) {
                    $emp_rv = Employee::where('user_id', $department->dep_hod)->first();
                } else {
                    $emp_rv = Employee::where('user_id', $department->dep_rew_4)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id', $department->dep_rew3)->first();
            }
            // $to_emails = $emp_rv->email;

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($rv3) && $rv3 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'rv3_id' => $user->id,
                'rv3_status' => $status,
                'rv3_timestamp' => date('Y-m-d H:i'),
                'rv3_action_ip' => request()->ip(),
                'rv3_remark' => $remark
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->rv3_id)->first();
            $approved_date = Nvsericestatus::select('rv3_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->rv3_timestamp;

            if ($department->dep_rew4 === null) {
                $emp_rv = Employee::where('user_id', $department->dep_hod)->first();
            } else {
                $emp_rv = Employee::where('user_id', $department->dep_rew4)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($rv4) && $rv4 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'rv4_id' => $user->id,
                'rv4_status' => $status,
                'rv4_timestamp' => date('Y-m-d H:i'),
                'rv4_action_ip' => request()->ip(),
                'rv4_remark' => $remark
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->rv4_id)->first();
            $approved_date = Nvsericestatus::select('rv4_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->rv4_timestamp;

            $emp_rv = Employee::where('user_id', $department->dep_hod)->first();
            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($hod) && $hod == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'hod_id' => $user->id,
                'hod_status' => $status,
                'hod_timestamp' => date('Y-m-d H:i'),
                'hod_action_ip' => request()->ip(),
                'hod_remark' => $remark
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->hod_id)->first();
            $approved_date = Nvsericestatus::select('hod_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->hod_timestamp;
            // $emp_rv = Employee::where('user_id', $id1->work_rew1 )->first();

            if ($derc_info == 1) {
                $emp_rv = Employee::where('user_id', $id0->work_rew1)->first();
            } elseif ($derc_info == 0) {
                $emp_rv = Employee::where('user_id', $id1->work_rew1)->first();
            } else {
                $emp_rv = Employee::where('user_id', $id1->work_rew1)->first();
            }



            // $abc = NVMaterial::select('nv_id')->where('id', $material_id)->first();
            // $nv = NeedValidation::select('budget_type')->where('id',$abc->nv_id)->first();
            //   if($nv->budget_type === 'CAPEX'){
            //     $emp_rv = $workflow->bt_capex_email;
            //   }else if($nv->budget_type === 'OPEX'){
            //     $emp_rv = $workflow->bt_opex_eamil;
            //   }


            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id0->work_rew1) && $id0->work_rew1 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'ces_rew1_id' => $user->id,
                'ces_rew1_status' => $status,
                'ces_rew1_timestamp' => date('Y-m-d H:i'),
                'ces_rew1_action_ip' => request()->ip(),
                'ces_rew1_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->ces_rew1_id)->first();
            $approved_date = Nvsericestatus::select('ces_rew1_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->ces_rew1_timestamp;

            if ($id0->work_rew2 === null) {
                $emp_rv = Employee::where('user_id', $id0->work_rew3)->first();

                if ($id0->work_rew3  === null) {
                    $emp_rv = Employee::where('user_id', $id0->work_rew4)->first();
                    if ($id0->work_rew4 === null) {
                        $emp_rv = Employee::where('user_id', $id0->approver)->first();
                    } else {
                        $emp_rv = Employee::where('user_id', $id0->work_rew4)->first();
                    }
                } else {
                    $emp_rv = Employee::where('user_id', $id0->work_rew3)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id', $id0->work_rew2)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id0->work_rew2) && $id0->work_rew2 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'ces_rew2_id' => $user->id,
                'ces_rew2_status' => $status,
                'ces_rew2_timestamp' => date('Y-m-d H:i'),
                'ces_rew2_action_ip' => request()->ip(),
                'ces_rew2_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->ces_rew2_id)->first();
            $approved_date = Nvsericestatus::select('ces_rew2_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->ces_rew2_timestamp;

            if ($id0->work_rew3 === null) {
                $emp_rv = Employee::where('user_id', $id0->work_rew4)->first();
                if ($id0->work_rew4 === null) {
                    $emp_rv = Employee::where('user_id', $id0->approver)->first();
                } else {
                    $emp_rv = Employee::where('user_id',  $id0->work_rew4)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id',  $id0->work_rew3)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id0->work_rew3) && $id0->work_rew3 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'ces_rew3_id' => $user->id,
                'ces_rew3_status' => $status,
                'ces_rew3_timestamp' => date('Y-m-d H:i'),
                'ces_rew3_action_ip' => request()->ip(),
                'ces_rew3_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->ces_rew3_id)->first();
            $approved_date = Nvsericestatus::select('ces_rew3_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->ces_rew3_timestamp;

            if ($id0->work_rew4 === null) {
                $emp_rv = Employee::where('user_id', $id0->approver)->first();
            } else {
                $emp_rv = Employee::where('user_id', $id0->work_rew4)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id0->work_rew4) && $id0->work_rew4 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'ces_rew4_id' => $user->id,
                'ces_rew4_status' => $status,
                'ces_rew4_timestamp' => date('Y-m-d H:i'),
                'ces_rew4_action_ip' => request()->ip(),
                'ces_rew4_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->ces_rew4_id)->first();
            $approved_date = Nvsericestatus::select('ces_rew4_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->ces_rew4_timestamp;

            $emp_rv = Employee::where('user_id', $id0->approver)->first();

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id0->approver) && $id0->approver == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'ces_id' => $user->id,
                'ces_status' => $status,
                'ces_timestamp' => date('Y-m-d H:i'),
                'ces_action_ip' => request()->ip(),
                'ces_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->ces_id)->first();
            $approved_date = Nvsericestatus::select('ces_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->ces_timestamp;

            $emp_rv = Employee::where('user_id', $id1->work_rew1)->first();

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id1->work_rew1) && $id1->work_rew1 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew1_id' => $user->id,
                'work_rew1_status' => $status,
                'work_rew1_timestamp' => date('Y-m-d H:i'),
                'work_rew1_action_ip' => request()->ip(),
                'work_rew1_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name')->where('id', $approved->work_rew1_id)->first();
            $approved_date = Nvsericestatus::select('work_rew1_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew1_timestamp;

            if ($id1->work_rew2 === null) {
                $emp_rv = Employee::where('user_id', $id1->work_rew3)->first();

                if ($id1->work_rew3  === null) {
                    $emp_rv = Employee::where('user_id', $id1->work_rew4)->first();
                    if ($id1->work_rew4 === null) {
                        $emp_rv = Employee::where('user_id', $id1->approver)->first();
                    } else {
                        $emp_rv = Employee::where('user_id', $id1->work_rew4)->first();
                    }
                } else {
                    $emp_rv = Employee::where('user_id', $id1->work_rew3)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id', $id1->work_rew2)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id1->work_rew2) && $id1->work_rew2 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew2_id' => $user->id,
                'work_rew2_status' => $status,
                'work_rew2_timestamp' => date('Y-m-d H:i'),
                'work_rew2_action_ip' => request()->ip(),
                'work_rew2_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew2_id)->first();
            $approved_date = Nvsericestatus::select('work_rew2_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew2_timestamp;

            if ($id1->work_rew3 === null) {
                $emp_rv = Employee::where('user_id', $id1->work_rew4)->first();
                if ($id1->work_rew4 === null) {
                    $emp_rv = Employee::where('user_id', $id1->approver)->first();
                } else {
                    $emp_rv = Employee::where('user_id',  $id1->work_rew4)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id',  $id1->work_rew3)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id1->work_rew3) && $id1->work_rew3 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew3_id' => $user->id,
                'work_rew3_status' => $status,
                'work_rew3_timestamp' => date('Y-m-d H:i'),
                'work_rew3_action_ip' => request()->ip(),
                'work_rew3_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew3_id)->first();
            $approved_date = Nvsericestatus::select('work_rew3_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew3_timestamp;

            if ($id1->work_rew4 === null) {
                $emp_rv = Employee::where('user_id', $id1->approver)->first();
            } else {
                $emp_rv = Employee::where('user_id', $id1->work_rew4)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id1->work_rew4) && $id1->work_rew4 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew4_id' => $user->id,
                'work_rew4_status' => $status,
                'work_rew4_timestamp' => date('Y-m-d H:i'),
                'work_rew4_action_ip' => request()->ip(),
                'work_rew4_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew4_id)->first();
            $approved_date = Nvsericestatus::select('work_rew4_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew4_timestamp;

            $emp_rv = Employee::where('user_id', $id1->approver)->first();

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id1->approver) && $id1->approver == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'approver_id' => $user->id,
                'approver_status' => $status,
                'approver_timestamp' => date('Y-m-d H:i'),
                'approver_action_ip' => request()->ip(),
                'approver_remark' => $remark,

                'cpmg_id' => $user->id,
                'cpmg_status' => $status,
                'cpmg_timestamp' => date('Y-m-d H:i'),
                'cpmg_action_ip' => request()->ip(),
                'cpmg_remark' => $remark,
                'check_technology' =>  $check_technology,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->approver_id)->first();
            $approved_date = Nvsericestatus::select('approver_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->approver_timestamp;

            if ($check_technology == 1) {
                $emp_rv = Employee::where('user_id', $id2->work_rew1)->first();
            } elseif ($check_technology == 0) {
                $emp_rv = Employee::where('user_id', $id3->work_rew1)->first();
            } else {
                $emp_rv = Employee::where('user_id', $id3->work_rew1)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id2->work_rew1) && $id2->work_rew1 == $user->id) {

            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew1dep2_id' => $user->id,
                'work_rew1dep2_status' => $status,
                'work_rew1dep2_timestamp' => date('Y-m-d H:i'),
                'work_rew1dep2_action_ip' => request()->ip(),
                'work_rew1dep2_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew1dep2_id)->first();
            $approved_date = Nvsericestatus::select('work_rew1dep2_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew1dep2_timestamp;

            if ($id2->work_rew2 === null) {
                $emp_rv = Employee::where('user_id', $id2->work_rew3)->first();

                if ($id2->work_rew3  === null) {
                    $emp_rv = Employee::where('user_id', $id2->work_rew4)->first();
                    if ($id2->work_rew4 === null) {
                        $emp_rv = Employee::where('user_id', $id2->approver)->first();
                    } else {
                        $emp_rv = Employee::where('user_id', $id2->work_rew4)->first();
                    }
                } else {
                    $emp_rv = Employee::where('user_id', $id2->work_rew3)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id', $id2->work_rew2)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
            }
        } elseif (!empty($id2->work_rew2) && $id2->work_rew2 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew2dep2_id' => $user->id,
                'work_rew2dep2_status' => $status,
                'work_rew2dep2_timestamp' => date('Y-m-d H:i'),
                'work_rew2dep2_action_ip' => request()->ip(),
                'work_rew2dep2_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew2dep2_id)->first();
            $approved_date = Nvsericestatus::select('work_rew2dep2_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew2dep2_timestamp;

            if ($id2->work_rew3 === null) {
                $emp_rv = Employee::where('user_id', $id2->work_rew4)->first();
                if ($id2->work_rew4 === null) {
                    $emp_rv = Employee::where('user_id', $id2->approver)->first();
                } else {
                    $emp_rv = Employee::where('user_id',  $id2->work_rew4)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id',  $id2->work_rew3)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id2->work_rew3) && $id2->work_rew3 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew3dep2_id' => $user->id,
                'work_rew3dep2_status' => $status,
                'work_rew3dep2_timestamp' => date('Y-m-d H:i'),
                'work_rew3dep2_action_ip' => request()->ip(),
                'work_rew3dep2_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew3dep2_id)->first();
            $approved_date = Nvsericestatus::select('work_rew3dep2_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew3dep2_timestamp;

            if ($id2->work_rew4 === null) {
                $emp_rv = Employee::where('user_id', $id2->approver)->first();
            } else {
                $emp_rv = Employee::where('user_id', $id2->work_rew4)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id2->work_rew4) && $id2->work_rew4 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew4dep2_id' => $user->id,
                'work_rew4dep2_status' => $status,
                'work_rew4dep2_timestamp' => date('Y-m-d H:i'),
                'work_rew4dep2_action_ip' => request()->ip(),
                'work_rew4dep2_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew4dep2_id)->first();
            $approved_date = Nvsericestatus::select('work_rew4dep2_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew4dep2_timestamp;

            $emp_rv = Employee::where('user_id', $id2->approver)->first();

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id2->approver) && $id2->approver == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'approverdep2_id' => $user->id,
                'approverdep2_status' => $status,
                'approverdep2_timestamp' => date('Y-m-d H:i'),
                'approverdep2_action_ip' => request()->ip(),
                'approverdep2_remark' => $remark,

                'cto_id' => $user->id,
                'cto_status' => $status,
                'cto_timestamp' => date('Y-m-d H:i'),
                'cto_action_ip' => request()->ip(),
                'cto_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->approverdep2_id)->first();
            $approved_date = Nvsericestatus::select('approverdep2_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->approverdep2_timestamp;

            $emp_rv = Employee::where('user_id', $id3->work_rew1)->first();

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id3->work_rew1) && $id3->work_rew1 == $user->id) {

            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew1dep3_id' => $user->id,
                'work_rew1dep3_status' => $status,
                'work_rew1dep3_timestamp' => date('Y-m-d H:i'),
                'work_rew1dep3_action_ip' => request()->ip(),
                'work_rew1dep3_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew1dep3_id)->first();
            $approved_date = Nvsericestatus::select('work_rew1dep3_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew1dep3_timestamp;

            if ($id3->work_rew2 === null) {
                $emp_rv = Employee::where('user_id', $id3->work_rew3)->first();

                if ($id3->work_rew3  === null) {
                    $emp_rv = Employee::where('user_id', $id3->work_rew4)->first();
                    if ($id3->work_rew4 === null) {
                        $emp_rv = Employee::where('user_id', $id3->approver)->first();
                    } else {
                        $emp_rv = Employee::where('user_id', $id3->work_rew4)->first();
                    }
                } else {
                    $emp_rv = Employee::where('user_id', $id3->work_rew3)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id', $id3->work_rew2)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id3->work_rew2) && $id3->work_rew2 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew2dep3_id' => $user->id,
                'work_rew2dep3_status' => $status,
                'work_rew2dep3_timestamp' => date('Y-m-d H:i'),
                'work_rew2dep3_action_ip' => request()->ip(),
                'work_rew2dep3_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew2dep3_id)->first();
            $approved_date = Nvsericestatus::select('work_rew2dep3_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew2dep3_timestamp;

            if ($id3->work_rew3 === null) {
                $emp_rv = Employee::where('user_id', $id3->work_rew4)->first();
                if ($id3->work_rew4 === null) {
                    $emp_rv = Employee::where('user_id', $id3->approver)->first();
                } else {
                    $emp_rv = Employee::where('user_id',  $id3->work_rew4)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id',  $id3->work_rew3)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id3->work_rew3) && $id3->work_rew3 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew3dep3_id' => $user->id,
                'work_rew3dep3_status' => $status,
                'work_rew3dep3_timestamp' => date('Y-m-d H:i'),
                'work_rew3dep3_action_ip' => request()->ip(),
                'work_rew3dep3_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name')->where('id', $approved->work_rew3dep3_id)->first();
            $approved_date = Nvsericestatus::select('work_rew3dep3_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew3dep3_timestamp;

            if ($id3->work_rew4 === null) {
                $emp_rv = Employee::where('user_id', $id3->approver)->first();
            } else {
                $emp_rv = Employee::where('user_id', $id3->work_rew4)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id3->work_rew4) && $id3->work_rew4 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew4dep3_id' => $user->id,
                'work_rew4dep3_status' => $status,
                'work_rew4dep3_timestamp' => date('Y-m-d H:i'),
                'work_rew4dep3_action_ip' => request()->ip(),
                'work_rew4dep3_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew4dep3_id)->first();
            $approved_date = Nvsericestatus::select('work_rew4dep3_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew4dep3_timestamp;

            $emp_rv = Employee::where('user_id', $id3->approver)->first();

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id3->approver) && $id3->approver == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'approverdep3_id' => $user->id,
                'approverdep3_status' => $status,
                'approverdep3_timestamp' => date('Y-m-d H:i'),
                'approverdep3_action_ip' => request()->ip(),
                'approverdep3_remark' => $remark,

                'ceo_nominee_id' => $user->id,
                'ceo_nominee_status' => $status,
                'ceo_nominee_timestamp' => date('Y-m-d H:i'),
                'ceo_nomnee_action_ip' => request()->ip(),
                'ceo_nominee_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->approverdep3_id)->first();
            $approved_date = Nvsericestatus::select('approverdep3_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->approverdep3_timestamp;

            $emp_rv = Employee::where('user_id', $id4->work_rew1)->first();

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id4->work_rew1) && $id4->work_rew1 == $user->id) {

            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew1dep4_id' => $user->id,
                'work_rew1dep4_status' => $status,
                'work_rew1dep4_timestamp' => date('Y-m-d H:i'),
                'work_rew1dep4_action_ip' => request()->ip(),
                'work_rew1dep4_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew1dep4_id)->first();
            $approved_date = Nvsericestatus::select('work_rew1dep4_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew1dep4_timestamp;

            if ($id4->work_rew2 === null) {
                $emp_rv = Employee::where('user_id', $id4->work_rew3)->first();

                if ($id4->work_rew3  === null) {
                    $emp_rv = Employee::where('user_id', $id4->work_rew4)->first();
                    if ($id4->work_rew4 === null) {
                        $emp_rv = Employee::where('user_id', $id4->approver)->first();
                    } else {
                        $emp_rv = Employee::where('user_id', $id4->work_rew4)->first();
                    }
                } else {
                    $emp_rv = Employee::where('user_id', $id4->work_rew3)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id', $id4->work_rew2)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id4->work_rew2) && $id4->work_rew2 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew2dep4_id' => $user->id,
                'work_rew2dep4_status' => $status,
                'work_rew2dep4_timestamp' => date('Y-m-d H:i'),
                'work_rew2dep4_action_ip' => request()->ip(),
                'work_rew2dep4_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew2dep4_id)->first();
            $approved_date = Nvsericestatus::select('work_rew2dep4_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew2dep4_timestamp;

            if ($id4->work_rew3 === null) {
                $emp_rv = Employee::where('user_id', $id4->work_rew4)->first();
                if ($id4->work_rew4 === null) {
                    $emp_rv = Employee::where('user_id', $id4->approver)->first();
                } else {
                    $emp_rv = Employee::where('user_id',  $id4->work_rew4)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id',  $id4->work_rew3)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id4->work_rew3) && $id4->work_rew3 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew3dep4_id' => $user->id,
                'work_rew3dep4_status' => $status,
                'work_rew3dep4_timestamp' => date('Y-m-d H:i'),
                'work_rew3dep4_action_ip' => request()->ip(),
                'work_rew3dep4_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew3dep4_id)->first();
            $approved_date = Nvsericestatus::select('work_rew3dep4_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew3dep4_timestamp;

            if ($id4->work_rew4 === null) {
                $emp_rv = Employee::where('user_id', $id4->approver)->first();
            } else {
                $emp_rv = Employee::where('user_id', $id4->work_rew4)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id4->work_rew4) && $id4->work_rew4 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew4dep4_id' => $user->id,
                'work_rew4dep4_status' => $status,
                'work_rew4dep4_timestamp' => date('Y-m-d H:i'),
                'work_rew4dep4_action_ip' => request()->ip(),
                'work_rew4dep4_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew4dep4_id)->first();
            $approved_date = Nvsericestatus::select('work_rew4dep4_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew4dep4_timestamp;

            $emp_rv = Employee::where('user_id', $id4->approver)->first();

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id4->approver) && $id4->approver == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'approverdep4_id' => $user->id,
                'approverdep4_status' => $status,
                'approverdep4_timestamp' => date('Y-m-d H:i'),
                'approverdep4_action_ip' => request()->ip(),
                'approverdep4_remark' => $remark,

                'ceo_nominee2_id' => $user->id,
                'ceo_nominee2_status' => $status,
                'ceo_nominee2_timestamp' => date('Y-m-d H:i'),
                'ceo_nominee2_action_ip' => request()->ip(),
                'ceo_nominee2_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->approverdep4_id)->first();
            $approved_date = Nvsericestatus::select('approverdep4_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->approverdep4_timestamp;

            $emp_rv = Employee::where('user_id', $id5->work_rew1)->first();

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id5->work_rew1) && $id5->work_rew1 == $user->id) {

            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew1dep5_id' => $user->id,
                'work_rew1dep5_status' => $status,
                'work_rew1dep5_timestamp' => date('Y-m-d H:i'),
                'work_rew1dep5_action_ip' => request()->ip(),
                'work_rew1dep5_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew1dep5_id)->first();
            $approved_date = Nvsericestatus::select('work_rew1dep5_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew1dep5_timestamp;

            if ($id5->work_rew2 === null) {
                $emp_rv = Employee::where('user_id', $id5->work_rew3)->first();

                if ($id5->work_rew3  === null) {
                    $emp_rv = Employee::where('user_id', $id5->work_rew4)->first();
                    if ($id5->work_rew4 === null) {
                        $emp_rv = Employee::where('user_id', $id5->approver)->first();
                    } else {
                        $emp_rv = Employee::where('user_id', $id5->work_rew4)->first();
                    }
                } else {
                    $emp_rv = Employee::where('user_id', $id5->work_rew3)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id', $id5->work_rew2)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id5->work_rew2) && $id5->work_rew2 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew2dep5_id' => $user->id,
                'work_rew2dep5_status' => $status,
                'work_rew2dep5_timestamp' => date('Y-m-d H:i'),
                'work_rew2dep5_action_ip' => request()->ip(),
                'work_rew2dep5_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew2dep5_id)->first();
            $approved_date = Nvsericestatus::select('work_rew2dep5_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew2dep5_timestamp;

            if ($id5->work_rew3 === null) {
                $emp_rv = Employee::where('user_id', $id5->work_rew4)->first();
                if ($id5->work_rew4 === null) {
                    $emp_rv = Employee::where('user_id', $id5->approver)->first();
                } else {
                    $emp_rv = Employee::where('user_id',  $id5->work_rew4)->first();
                }
            } else {
                $emp_rv = Employee::where('user_id',  $id5->work_rew3)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id5->work_rew3) && $id5->work_rew3 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew3dep5_id' => $user->id,
                'work_rew3dep5_status' => $status,
                'work_rew3dep5_timestamp' => date('Y-m-d H:i'),
                'work_rew3dep5_action_ip' => request()->ip(),
                'work_rew3dep5_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew3dep5_id)->first();
            $approved_date = Nvsericestatus::select('work_rew3dep5_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew3dep5_timestamp;

            if ($id5->work_rew4 === null) {
                $emp_rv = Employee::where('user_id', $id5->approver)->first();
            } else {
                $emp_rv = Employee::where('user_id', $id5->work_rew4)->first();
            }

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();

                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id5->work_rew4) && $id5->work_rew4 == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'work_rew4dep5_id' => $user->id,
                'work_rew4dep5_status' => $status,
                'work_rew4dep5_timestamp' => date('Y-m-d H:i'),
                'work_rew4dep5_action_ip' => request()->ip(),
                'work_rew4dep5_remark' => $remark,
            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->work_rew4dep5_id)->first();
            $approved_date = Nvsericestatus::select('work_rew4dep5_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->work_rew4dep5_timestamp;

            $emp_rv = Employee::where('user_id', $id5->approver)->first();

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been approved:";
                $p2 = "";
                $status = "Approved";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                $p1 = "Your nv request has been Rejected:";
                $p2 = "";
                $status = "Rejected";
                $to_emails = $emp_rv->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });
                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
            }
        } elseif (!empty($id5->approver) && $id5->approver == $user->id) {
            $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            $update_status->update([
                'approverdep5_id' => $user->id,
                'approverdep5_status' => $status,
                'approverdep5_timestamp' => date('Y-m-d H:i'),
                'approverdep5_action_ip' => request()->ip(),
                'approverdep5_remark' => $remark,
                'ceo_id' => $user->id,
                'ceo_status' => $status,
                'ceo_timestamp' => date('Y-m-d H:i'),
                'ceo_action_ip' => request()->ip(),
                'ceo_remark' => $remark,

            ]);
            $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_material->user_id)->first();
            $user = User::select('email')->where('id', $tble_material->user_id)->first();
            $approved = Nvsericestatus::where('material_id', $material_id)->first();
            $approved_by = User::select('name','id')->where('id', $approved->approverdep5_id)->first();
            $approved_date = Nvsericestatus::select('approverdep5_timestamp')->where('material_id', $material_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->approverdep5_timestamp;

            // $emp_rv = $workflow->ceo_nominee2_apr2_email;

            if ($status == 1) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Approved";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name' => $name, 'date' => $date, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                // $p1 = "Your nv request has been approved:";
                // $p2 = "";
                // $status = "Approved";
                // $to_emails = $emp_rv->email;
                // Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2,'date'=> $date,'name'=> $name,'status'=>$status], function ($message) use ($to_emails) {
                //     $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                //     $message->to($to_emails);
                //     $message->cc('raushan@rediansoftware.com');
                //     $message->subject("NV  New Task Assign");
                // });

            } elseif ($status == 2) {
                $p1 = "You have a new request that requires your approval:";
                $p2 = "Please review the request and take appropriate action.";
                $status = "Rejected";
                $to_emails = $user->email;
                Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject("NV  New Task Assign");
                });

                $Notification=new Notification;
                $Notification->p1=$p1 ?? '';
                $Notification->p2=$p2 ?? '';
                $Notification->status=$status ?? '';
                $Notification->email=$to_emails ?? '';
                $Notification->actiontakenbyuser=$approved_by->id ?? '';
                $Notification->initiated_date=$initiated_date ?? '';
                $Notification->initiated_by=$initiated_by->id ?? '';
                $Notification->name=$name ?? '';
                $Notification->actiontakendate=$date ?? '';
                $Notification->save();
                // $p1 = "Your nv request has been Rejected:";
                // $p2 = "";
                // $status = "Rejected";
                // $to_emails = $emp_rv->email;
                // Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date'=> $date,'name'=> $name,'status'=>$status], function ($message) use ($to_emails) {
                //     $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                //     $message->to($to_emails);
                //     $message->cc('raushan@rediansoftware.com');
                //     $message->subject("NV  New Task Assign");
                // });

            }


            //   elseif(!empty($ceoA2flow->ceo_nominee2_apr2_username) && $ceoA2flow->ceo_nominee2_apr2_username == $user->name){
            //     $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            //     $update_status->update([
            //         'ceo_nominee2a2_id' => $user->id,
            //         'ceo_nominee2a2_status' => $status,
            //         'ceo_nominee2a2_timestamp' => date('Y-m-d H:i'),
            //         'ceo_nominee2a2_action_ip' => request()->ip(),
            //         'ceo_nominee2a2_remark' => $remark,
            //     ]);
            //     $initiated_date = NVMaterial::select('created_at')->where('id', $material_id)->first();
            //     $initiated_by = User::select('name')->where('id', $tble_material->user_id)->first();
            //     $user = User::select('email')->where('id', $tble_material->user_id)->first();
            //     $approved = Nvsericestatus::where('material_id', $material_id)->first();
            //     $approved_by = User::select('name')->where('id', $approved->ceo_nominee2a2_id)->first();
            //     $approved_date = Nvsericestatus::select('ceo_nominee2a2_timestamp')->where('material_id', $material_id)->first();

            //     $name = $approved_by->name;
            //     $date = $approved_date->ceo_nominee2a2_timestamp;

            //     $emp_rv = $workflow->ceo_email;

            //     if ($status == 1) {
            //         $p1 = "You have a new request that requires your approval:";
            //         $p2 = "Please review the request and take appropriate action.";
            //         $status = "Approved";
            //         $to_emails = $user->email;
            //         Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'name'=> $name,'date'=> $date,'status'=>$status], function ($message) use ($to_emails) {
            //             $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
            //             $message->to($to_emails);
            //             $message->cc('raushan@rediansoftware.com');
            //             $message->subject("NV  New Task Assign");
            //         });


            //         $p1 = "Your nv request has been approved:";
            //         $p2 = "";
            //         $status = "Approved";
            //         $to_emails = $emp_rv;
            //         Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2,'date'=> $date,'name'=> $name,'status'=>$status], function ($message) use ($to_emails) {
            //             $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
            //             $message->to($to_emails);
            //             $message->cc('raushan@rediansoftware.com');
            //             $message->subject("NV  New Task Assign");
            //         });

            //     } elseif ($status == 2) {
            //         $p1 = "You have a new request that requires your approval:";
            //         $p2 = "Please review the request and take appropriate action.";
            //         $status = "Rejected";
            //         $to_emails = $user->email;
            //         Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2,'date'=> $date,'name'=> $name,'status'=>$status], function ($message) use ($to_emails) {
            //             $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
            //             $message->to($to_emails);
            //             $message->cc('raushan@rediansoftware.com');
            //             $message->subject("NV  New Task Assign");
            //         });


            //         $p1 = "Your nv request has been Rejected:";
            //         $p2 = "";
            //         $status = "Rejected";
            //         $to_emails = $emp_rv;
            //         Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by, 'p1' => $p1, 'p2' => $p2, 'date'=> $date,'name'=> $name,'status'=>$status], function ($message) use ($to_emails) {
            //             $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
            //             $message->to($to_emails);
            //             $message->cc('raushan@rediansoftware.com');
            //             $message->subject("NV  New Task Assign");
            //         });
            //     }
            //  } elseif(!empty($ceoflow->ceo_username) && $ceoflow->ceo_username == $user->name){
            //     $update_status = Nvsericestatus::where('material_id', $material_id)->first();
            //     $update_status->update([
            //         'ceo_id' => $user->id,
            //         'ceo_status' => $status,
            //         'ceo_timestamp' => date('Y-m-d H:i'),
            //         'ceo_action_ip' => request()->ip(),
            //         'ceo_remark' => $remark,
            //     ]);
            // $initiated_date = NVService::select('created_at')->where('id', $material_id)->orderBy('id','desc')->first();
            // $initiated_by = User::select('name')->where('id', $tble_service->user_id)->orderBy('id','desc')->first();
            // $user = User::where('id', $tble_service->user_id)->orderBy('id','desc')->first();
            // $approved = Nvsericestatus::select('hod_id')->where('material_id', $material_id)->orderBy('id','desc')->first();
            // $approved_by = User::select('name')->where('id', $approved->hod_id)->orderBy('id','desc')->first();
            // $approved_date = Nvsericestatus::select('hod_timestamp')->where('material_id', $material_id)->orderBy('id','desc')->first();

            // if ($status == 1) {
            //     $status = "Approved";
            //     $this->sendApprovalEmails($user, $tble_service, $status,$initiated_date,$initiated_by,$approved_by, $approved_date);
            // } elseif ($status == 2) {
            //     $status = "Rejected";
            //     $this->sendRejectionEmails($user, $tble_service, $status,$initiated_date,$initiated_by,$approved_by, $approved_date);
            // }
        }
        $response['result'] = 'success';
        $response['material_id'] = $material_id;
        return response()->json($response);
    }

    // Common function to send approval emails
    function sendApprovalEmails($user, $emp_rv,  $status, $initiated_date, $initiated_by, $approved_by, $approved_date)
    {

        $p1 = "You have a new request that requires your approval:";
        $p2 = "Please review the request and take appropriate action.";
        $status = "Approved";
        $to_emails = $user->email;
        Queue::push(new SendEmailJob($to_emails, "NV  New Task Assign", ['username' => 'user', 'p1' => $p1, 'p2' => $p2, 'status' => $status, 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'approved_date' => $approved_date, 'approved_by' => $approved_by]));

        $p1 = "Your nv request has been approved:";
        $p2 = "";
        $status = "Approved";
        $to_emails = $emp_rv->email;
        Queue::push(new SendEmailJob($to_emails, "NV  New Task Assign", ['username' => 'user', 'p1' => $p1, 'p2' => $p2, 'status' => $status, 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'approved_date' => $approved_date, 'approved_by' => $approved_by]));

        // $this->sendEmail($to_emails, "NV  New Task Assign", ['username' => 'user', 'p1' => $p1, 'p2' => $p2]);
    }

    // Common function to send rejection emails
    function sendRejectionEmails($user, $emp_rv,  $status, $initiated_date, $initiated_by, $approved_by, $approved_date)
    {
        $p1 = "You have a new request that requires your approval:";
        $p2 = "Please review the request and take appropriate action.";
        $status = "Rejected";
        $to_emails = $user->email;
        // $this->sendEmail($to_emails, "NV  New Task Assign", ['username' => 'user', 'p1' => $p1, 'p2' => $p2]);
        Queue::push(new SendEmailJob($to_emails, "NV  New Task Assign", ['username' => 'user', 'p1' => $p1, 'p2' => $p2, 'status' => $status, 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'approved_date' => $approved_date, 'approved_by' => $approved_by]));


        $p1 = "Your nv request has been Rejected:";
        $p2 = "";
        $status = "Rejected";
        $to_emails = $emp_rv->email;
        Queue::push(new SendEmailJob($to_emails, "NV  New Task Assign", ['username' => 'user', 'p1' => $p1, 'p2' => $p2, 'status' => $status, 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'approved_date' => $approved_date, 'approved_by' => $approved_by]));

        // $this->sendEmail($to_emails, "NV  New Task Assign", ['username' => 'user', 'p1' => $p1, 'p2' => $p2]);
    }
    // Reusable function to send emails
    function sendEmail($to_emails, $subject, $data)
    {
        Mail::send('emailtemp.doc_mail', $data, function ($message) use ($to_emails, $subject) {
            $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
            $message->to($to_emails);
            $message->cc('raushan@rediansoftware.com');
            $message->subject($subject);
        });
    }

    public function sendEmail1(Request $request)
    {
   
        $selectedEmails = $request->emails;
      
        $clarificationRemark = $request->remark;
      
        $data = [
            'remark' => $clarificationRemark
        ];

        try {
            foreach($selectedEmails as $email) {
                Mail::send('emailtemp.doc_mail1', $data, function ($message) use ($email) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($email);
                    $message->cc('raushan@rediansoftware.com');
                    $message->subject('Your Email Subject');
                });
            }
           

            $response = [
                'success' => true
            ];
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        return response()->json($response);
    }
    public function sendEmail2(Request $request)
    {
    //    return $request->all();
        $to = $request->to;
        $cc = $request->cc;
        $subject = $request->subject;
        $remark = $request->remark;
       
        // Logic to send the email
        $data = [
            'remark' => $remark
        ];
        
        // Example using the Mail facade
        Mail::send('emailtemp.doc_mail1', $data, function($message) use ($to, $cc, $subject, $remark) {
            $message->to($to)
                ->cc($cc)
                ->subject($subject);
                
        });
        
        // Return a response indicating the email was sent successfully
        return response()->json(['success' => true]);
    }
    
    public function preview(Request $request, $nv_id)
    {
        $user = \Auth()->user();
        $nv_id = $request->nv_id;
        $nv = NeedValidation::where('id',$nv_id)->orderBy('id', 'desc')->first();
        $nv_year = NeedValidation::select('id', 'fiscal_year')->where('id', $nv_id)->first();
        $material_import = MateriBOQBulk::where('nv_id',$nv_id)->orderBy('id', 'desc')->get();
        $service_import = ServiceBOQBulk::where('nv_id',$nv_id)->orderBy('id', 'desc')->get();
        $material_details = NVMaterial::where('nv_id', $nv_id)->first();
        $material_doc = MaterialDoc::where('service_id', $material_details->id)->first();
        $employees = Employee::where('department_id', $material_details->dept_id)->get();
        $chats = Chat::where('material_id',$material_details->id)->where('user_login_id',$user->id)->get();
        

        return view('admin.nvMaterial.preview', compact('material_details', 'material_doc','employees','chats','nv_year','material_import','nv','service_import'));
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

    public function bulkProviderStore(Request $request)
    {
        //dd($request->all());
        try {
            $validator = Validator::make($request->all(), [
                'materialboq' => 'required|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json(['ValidationError' => $validator->errors()], 422);
            }

            $path = $request->file('materialboq')->store('matboq');

            $import = new ExcelImportService(new MateriBOQBulk);

            $data = $import->import($path);

            $columns = array_diff(Schema::getColumnListing((new MateriBOQBulk)->getTable()), ['id','file', 'created_at', 'updated_at']);

            $nv_id = $request->nv_id;

            $duplicateMaterialCodes = [];

            $data['rows'] = array_map(function ($row) use ($columns, $nv_id, &$duplicateMaterialCodes) {
             
                if (count($columns) !== count($row)) {
                    //  dd(count($columns), count($row));
                    response()->json(['SheetError' => 'Invalid file: Number of columns do not match in all rows'], 422);
                }
                $row = array_combine($columns, $row);   

                $validator = Validator::make($row, [
                    'material_code' => 'required|unique:tbl_materialboq_bulk',
                    'rate' => 'required',
                    'quantity' => 'required|numeric',
                ]);

                if ($validator->fails()) {
                    $duplicateMaterialCodes[] = $row['material_code'];
                }
                $material_codeID = '';
                if ($row['material_code'] ==  'N/A') {
                    $NineDigitRandomNumber = rand(100000000, 999999999);
                    $material_codeID = '7' . $NineDigitRandomNumber;
                } else {
                    $material_codeID = $row['material_code'];
                }
                $material_data = MasterMaterialboq::select('rate_add', 'uom', 'material_short_text')
                    ->where('activity', $row['material_code'])
                    ->first();

                 // If rate is not available in the database, use the rate from the Excel sheet
                //  if (!$material_data || empty($material_data->rate_add)) {
                //     $row['rate'] = floatval($row['rate']); // Cast to float
                // } else {
                //     $row['rate'] = floatval($material_data->rate_add); // Cast to float
                // }
            // Check if uom and material_short_text are not available in the database
            if (!$material_data || empty($material_data->uom)) {
                $row['uom'] = $row['uom']; // Assign uom from Excel sheet
            } else {
                $row['uom'] = $material_data->uom;
            }

             // Check if uom and material_short_text are not available in the database
             if (!$material_data || empty($material_data->material_short_text)) {
                $row['material_short_text'] = $row['material_short_text']; // Assign uom from Excel sheet
            } else {
                $row['material_short_text'] = $material_data->material_short_text;
            }

            $row['nv_id'] = $nv_id;

            if (!$material_data || empty($material_data->rate_add)) {
                // Rate is not available in the database or is empty, set rate_reference to 2 and cast $row['rate'] to a float
                $row['rate_reference'] = 2;
                $row['rate'] = floatval($row['rate']);
                // dd($row['rate']);
            } else {
                // Rate is available in the database or Excel sheet, set rate_reference to 0 and cast $material_data->rate_add to a float
                $row['rate_reference'] = 0;
                $row['rate'] = floatval($material_data->rate_add);
                // dd($row['rate']);
            }

                $row['quantity'] = $row['quantity'];
                $row['material_code'] = $material_codeID ?? '';

                $amount = '';
                if (!empty($row['rate']) && is_numeric($row['rate']) && is_numeric($row['quantity'])) {
                    $amount = $row['rate'] * $row['quantity'];
                } else {
                    $amount = 0; // You can set a default value here if needed.
                }
                $row['amount'] = $amount;

                return $row;


            }, $data['rows']);
            $import->seedDB($data['rows']);

            return response()->json(['message' => 'File imported successfully', 'data' => $data]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return response()->json(['UploadError' => $e], 422);
        }
    }

    public function bulkServiceProviderStore(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'serviceboq' => 'required|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['ValidationError' => $validator->errors()], 422);
        }
        
        $path = $request->file('serviceboq')->store('matboq');

        $import = new ExcelImportService(new ServiceBOQBulk);
        $data = $import->import($path);

        $columns = array_diff(Schema::getColumnListing((new ServiceBOQBulk)->getTable()), ['id', 'created_at', 'updated_at']);
        $nv_id = $request->nv_id;

        $sequenceNumber = 800000000; // Set the starting sequence number

        $duplicateMaterialCodes = [];

        $data['rows'] = array_map(function ($row) use ($columns, $nv_id, &$duplicateMaterialCodes, &$sequenceNumber) {
            if (count($columns) !== count($row)) {
                return response()->json(['SheetError' => 'Invalid file: Number of columns do not match in all rows'], 422);
            }

            $row = array_combine($columns, $row);

            if (empty($row['service_code'])) {
                return response()->json(['ServiceCodeError' => 'Service code is required'], 422);
            }

            if ($row['service_code'] == 'N/A') {
                $row['service_code'] = (string)$sequenceNumber++;
            }

            $validator = Validator::make($row, [
                'rate' => 'required',
            ]);

            if ($validator->fails()) {
                $duplicateMaterialCodes[] = $row['service_code'];
            }

            $service_data = Boqmaterial::select('rate_ser', 'bun', 'service_short_text')
                ->where('activity', $row['service_code'])
                ->first();

            if ($service_data) {
                $row['rate'] = $service_data->rate_ser;
                $row['uom'] = $service_data->bun;
                $row['description'] = $service_data->service_short_text;
            }

            $row['nv_id'] = $nv_id;

            $row['qty'] = $row['qty'];
            $amount = $row['rate'] * $row['qty'];
            $row['amount'] = $amount;

            return $row;
        }, $data['rows']);

        $import->seedDB($data['rows']);

        return response()->json(['message' => 'File imported successfully', 'data' => $data]);
    } catch (\Exception $e) {
        $message = $e->getMessage();
        return response()->json(['UploadError' => $e], 422);
    }
}

    
    public function list_materialBoq(Request $request)
    {


        $user = \Auth()->user();
        $nv_id = $request->nv_id;
        // dd( $nv_id);

        if ($request->ajax()) {

            $materialboq = datatables()
                ->of(
                    MateriBOQBulk::where('nv_id', $nv_id)->orderBy('id', 'desc')->get()
                )

                ->addColumn('action', function ($data) use ($user) {
                    // $button = '<a class="btn btn-sm btn-clean btn-icon" onclick="edit_mat('.$data->id.')" title="Edit"><i class="fas fa-edit text-info"></i></a>';

                    $editButton = '<a class="btn btn-sm btn-clean btn-icon" onclick="edit_mat('.$data->id.')" title="Edit"><i class="fas fa-edit text-info"></i></a>';

                    $deleteButton = '<a href="javascript:;" data-id="'.$data->id.'" class="btn btn-sm btn-clean btn-icon delete_material" title="Delete"><i class="fas fa-trash text-danger"></i></a>';

                    // $deleteallButton = '<a href="javascript:;" data-id="'.$data->id.'" class="btn btn-sm btn-clean btn-icon delete_all_materials" title="Delete"><i class="fas fa-trash text-danger"></i></a>';

                    return $editButton . $deleteButton;
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);

            return $materialboq;
        }
        return view('admin.nvMaterial.create');
    }

    public function list_serviceBoq(Request $request)
    {


        $user = \Auth()->user();
        $nv_id = $request->nv_id;
        // dd( $nv_id);
        if ($request->ajax()) {

            $serviceBoq = datatables()
                ->of(
                    ServiceBOQBulk::where('nv_id', $nv_id)->orderBy('id', 'desc')->get()
                )

                ->addColumn('action', function ($data) use ($user) {
                   
                    $editButton = '<a href="/admin/nv_material/edit_service/' . $data->id . '" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>';

                    $deleteButton = '<a href="javascript:;" data-id="'.$data->id.'" class="btn btn-sm btn-clean btn-icon delete_service" title="Delete"><i class="fas fa-trash text-danger"></i></a>';

                    return $editButton . $deleteButton;
               
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);

            return $serviceBoq;
        }
        return view('admin.nvMaterial.create');
    }


    public function store_material_boq(Request $request)
    {
        try {
            $request_input = $request->except("_token");


            // dd($_POST);
            $nv_id = $request_input["nv_id"];


            if (NeedValidation::where(["id" => $nv_id])->exists()) {
                $rules = [
                    "material_code" => "required",
                    "rate" => "required",
                    "quantity" => "required",
                    // "prop_type" => "required",
                    // "nv_type" => "required",
                    // "fiscal_year" => "required",
                ];

                $messages = [
                    "material_code.required" => "Please enter material code",
                    "rate.required" => "Please enter Rate",
                    "quantity.required" => "Please enter Quantity",
                    // "Please enter budgetary provision",
                    // "prop_type.required" => "Please enter proposal type",
                    // "nv_type.required" => "Please enter nv type",
                    // "fiscal_year.required" => "Please enter fiscal year",
                ];
                $validator = Validator::make($request_input, $rules, $messages);
                if ($validator->fails()) {
                    $response["msg"] = $validator->errors()->toArray();
                    $response["result"] = "error";
                } else {
                    $materialBoq = MateriBOQBulk::create([
                        "nv_id" => $request_input["nv_id"],
                        "material_code" => $request_input["material_code"],
                        "uom" => $request_input["uom_0"],
                        "material_short_text" => $request_input["mat_des"],
                        "rate" => $request_input["rate"],
                        "quantity" => $request_input["quantity"],
                        "amount" => $request_input["total_amount"],
                    ]);
                    $response["result"] = "success";
                    $response["msg"] = "Material BOQ created";
                }
            } else {
                $rules = ["material_code" => "required",];
                $messages = ["material_code.required" => "Please enter material code",];

                $validator = Validator::make($request_input, $rules, $messages);
                if ($validator->fails()) {
                    $response["msg"] = $validator->errors()->toArray();
                    $response["result"] = "error";
                } else {
                    $materialBoq = MateriBOQBulk::create([
                        "nv_id" => $request_input["nv_id"],
                        "material_code" => $request_input["material_code"],
                        "uom" => $request_input["uom_0"],
                        "material_short_text" => $request_input["mat_des"],
                        "rate" => $request_input["rate"],
                        "quantity" => $request_input["quantity"],
                        "amount" => $request_input["total_amount"],
                    ]);
                    // echo $location;

                    $response["result"] = "success";
                    $response["msg"] = "Material BOQ created";
                }
            }
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response["result"] = "failure";
            $response["msg"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function serviceBoqStore(Request $request)
    {

        try {
            $request_input = $request->except("_token");
            // dd($_POST);
            $nv_id = $request_input["nv_id"];

            if (NeedValidation::where(["id" => $nv_id])->exists()) {
                $rules = [
                    "service_code_0" => "required",
                    "ser_rate" => "required",
                    "ser_quantity" => "required",
                    // "prop_type" => "required",
                    // "nv_type" => "required",
                    // "fiscal_year" => "required",
                ];

                $messages = [
                    "service_code_0.required" => "Please enter Service Code",
                    "ser_rate.required" => "Please enter Rate",
                    "ser_quantity.required" => "Please enter Quantity",
                    // "Please enter budgetary provision",
                    // "prop_type.required" => "Please enter proposal type",
                    // "nv_type.required" => "Please enter nv type",
                    // "fiscal_year.required" => "Please enter fiscal year",
                ];
                $validator = Validator::make($request_input, $rules, $messages);
                if ($validator->fails()) {
                    $response["msg"] = $validator->errors()->toArray();
                    $response["result"] = "error";
                } else {
                    $serviceBoq = ServiceBOQBulk::create([

                        'nv_id' =>  $request_input["nv_id"],
                        'service_code' => $request_input["service_code_0"],
                        'description' =>  $request_input["ser_des_0"],
                        'uom' =>  $request_input["ser_uom_0"],
                        'rate' => $request_input["ser_rate"],
                        'qty' => $request_input["ser_quantity"],
                        'amount' =>  $request_input["ser_total_amount"],
                    ]);
                    $response["result"] = "success";
                    $response["msg"] = "Service BOQ created";
                }
            } else {
                $rules = ["service_code_0" => "required",];
                $messages = ["service_code_0.required" => "Please enter service code",];

                $validator = Validator::make($request_input, $rules, $messages);
                if ($validator->fails()) {
                    $response["msg"] = $validator->errors()->toArray();
                    $response["result"] = "error";
                } else {
                    $serviceBoq = ServiceBOQBulk::create([
                        'nv_id' =>  $request_input["nv_id"],
                        'service_code' => $request_input["service_code_0"],
                        'description' =>  $request_input["ser_des_0"],
                        'uom' =>  $request_input["ser_uom_0"],
                        'rate' => $request_input["ser_rate"],
                        'qty' => $request_input["ser_quantity"],
                        'amount' =>  $request_input["ser_total_amount"],
                    ]);
                    // echo $location;

                    $response["result"] = "success";
                    $response["msg"] = "Service BOQ created";
                }
            }
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response["result"] = "failure";
            $response["msg"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function search_material_by_name(Request $request)
    {
        $searchTerm = $request->search_term;

        $materials = MasterMaterialboq::select('activity', 'uom', 'rate_add','material_short_text')
            ->where('uom', 'like', "%$searchTerm%")
            ->orWhere('activity', 'like', "%$searchTerm%")
            ->get();
        $materialData = array();
        // dd($materialData );
        if ($materials->isNotEmpty()) {
            foreach($materials as $k => $value) {
               
                $data['id'] = $value['activity'];  
                $data['text'] = $value['activity'] ."-".$value['uom'];   
                $data['mat_des_0'] = $value['uom'];
                $data['uom_0'] = $value['material_short_text'];
                $data['rate'] = $value['rate_add'];
              
                array_push($materialData, $data); 
            }
           
            return response()->json($materialData);
        } else {
            return response()->json(array(""=> ""));
        }
    }



    public function store_data(Request $request)

    {  
        // dd($request);
        $schemeno = $request->schemeno;
        $api_url = 'https://bsesapps.bsesdelhi.com/delhiv2/ISUService.asmx?op=ZBAPI_MDI_LETTER?schemeno='.$schemeno."";

    //    $response =  Http::get($api_url);
        $xml = file_get_contents($api_url);
        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml);
        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        $responseArray = json_decode($json, true);
        $value = ($responseArray['diffgrdiffgram']['BAPI_RESULT']['ISBapiTable']['LONG_TEXT']);
   
        
        return response()->json([
            "message"            =>  "Success",
            "value"              =>   $value ?? '',
            "code"                  =>  200
        ]);

    }
    public function store_material(Request $request)
    {
        // dd($request->sum);
        $nv_id = $request->nv_id;
        // dd($nv_id);
        $amount = $request->amount;
        $sum = $request->sum2;
        
        $material_amount = $request->amount1;
        // dd($material_amount);
        $material_description = $request->description1;
        $total_amt = $sum;
        // dd($amount,$material_amount);
        // dd($material_amount);
        
        if($sum == 0)
        {
            $total_amt = $material_amount;
            
        }
        else
        {
            $total_amt = $material_amount;
            // dd($amount,$material_amount,$total_amt);
        }
        
        $sum = $total_amt;
        // dd($total_amt,$sum);
         $data = NVMaterial::where('nv_id',$nv_id);
        // dd($data);
        $data->update([
            'total_budget_both' => $total_amt ?? '',
        ]);
        // dd($total_amt);
        return response()->json([
            "message"            =>  "Success",
            "material_amount"              =>   $material_amount ?? '',
            "material_description"              =>   $material_description ?? '',
            "total_amt"                 =>      $total_amt ?? '',
            "sum" => $sum,
            "code"                  =>  200
        ]);
    }

    public function store_service(Request $request)
    {
        // dd($request->sum);
        $nv_id = $request->nv_id;
        // dd($nv_id);
        $amount = $request->amount;
        $sum = $request->sum;
        $service_amount = $request->amount1;
        // dd($service_amount);
        $service_description = $request->description1;
        $total_amt = $sum;
        // dd($amount,$service_amount);
        if($sum == 0)
        {
            $total_amt =  $service_amount;
        }
        else
        {
            $total_amt = $service_amount;
        }
        
        $sum = $total_amt;
        // dd($total_amt,$sum);
         $data = NVMaterial::where('nv_id',$nv_id);
        // dd($data);
        $data->update([
            'total_budget_both' => $total_amt ?? '',
            
        ]);
        // dd($data);
        return response()->json([
            "message"            =>  "Success",
            "service_amount"              =>   $service_amount ?? '',
            "service_description"              =>   $service_description ?? '',
            "total_amt"                 =>      $total_amt ?? '',
            "sum" => $sum,
            "code"                  =>  200
        ]);
    }

    // public function service_sms(Request $request)
    // {
    //     $user_id = \Auth()->user()->id;
    //     $data = Otps::with('user')->where('user_id',$user_id)->pluck('otp');
    //     $app_name = "NEEDVALIDATION";
    //     $encrypt_key = "!!B$"."E$@@*SMS";
    //     $companycode = "BRPL";
    //     $vendor_code = "REDIAN";
    //     $mobile = \Auth()->user()->mobile_number;
    //     $otp = $data[0];
    //     $sms_type = "OTP";
    //     $api_url='https://bsesapps.bsesdelhi.com/delhiv2/ISUService.asmx/SENDBSES_SMSAPI?_sAppName='.$app_name.'&_sEncryptionKey='.$encrypt_key.'&_sCompanyCode='.$companycode.'&_sVendorCode='.$vendor_code.'&_MobileNo='.$mobile.'&_sOTPMsg='.$otp.'&_sSMSType='.$sms_type;
    //     $xml = file_get_contents($api_url);
    //     $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml);
    //     // $xml = simplexml_load_string($xml);
    //     $json = json_encode($xml);
    //     $responseArray = json_decode($json, true);
    //     $value = ($responseArray['diffgr:diffgram']['NewDataSet']['Table1']['FLAG']['OUT_PUT']);
    //     dd($value);
    //     return response()->json([
    //         "message"            =>  "Success",
    //         "code"                  =>  200
    //     ]);
    // }
}
