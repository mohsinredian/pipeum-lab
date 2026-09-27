<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MyPageController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AbilityController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ServiceBoq;
use App\Http\Controllers\Admin\Materialboq;
use App\Http\Controllers\Admin\ProfileImageController;
use App\Http\Controllers\Admin\CircleController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ItemReturnController;
use App\Http\Controllers\Admin\ItemIssueController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\CapexController;
use App\Http\Controllers\Admin\WorkFlowController;
use App\Http\Controllers\Admin\OpexWorkFlowController;
use App\Http\Controllers\Admin\QrcodeController;
use App\Http\Controllers\Admin\reportController;
use App\Http\Controllers\Admin\OpexController;
use App\Http\Controllers\Admin\CreateNeedValidationControlle;
use App\Http\Controllers\Admin\NvMaterialController;
use App\Http\Controllers\Admin\NvServiceController;
use App\Http\Controllers\Admin\DepartmentNVController;
use App\Http\Controllers\Admin\NVController;
use App\Http\Controllers\Admin\NVNotesController;
use App\Http\Controllers\Admin\OTPController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AdminWorkflowController;
use App\Http\Controllers\Admin\SuperDepartmentController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\UserManualController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return 'Cache is cleared';
})->middleware('auth');

Route::get('/', function () {
    return redirect('/admin/auth');
});
Route::get('admin/auth', function () {
    return view('admin.auth');
});

Route::get('/admin/user-manual/{id}', [UserManualController::class,'index'])->name('user.manual');

Route::get('/user/auth', 'Admin\UserAuthController@login_view')->name('login');
Route::get('refresh_captcha', 'Admin\UserAuthController@refreshCaptcha')->name('refresh_captcha');
Route::get('/forgot/password', [UserAuthController::class, 'showForgotForm'])->name('forgot.password');
Route::post('/forgot/password', [UserAuthController::class, 'sendResetLink'])->name('reset.password.link');
Route::get('/reset/password/{token}', [UserAuthController::class, 'showResetForm'])->name('reset.password.form');
Route::post('/reset/password', [UserAuthController::class, 'resetPassword'])->name('reset.password');

Route::get('qr-code-for-return/{id}', [QrcodeController::class, 'qrCodeReturn'])->name('admin.qr-code.return');

Route::group(['prefix' => 'admin'], function () {

    Route::get('/view-otp', [OTPController::class, 'viewOTP'])->name('view-otp')->middleware('authenticate.user');
    // Send the OTP
    Route::post('/send-otp', [OTPController::class, 'sendOTP'])->name('send-otp')->middleware('authenticate.user');
    // Verify the OTP
    Route::post('/verify-otp', [OTPController::class, 'verifyOTP'])->name('verify-otp')->middleware('authenticate.user');
// notifications
    Route::post('/regenerate-otp', [OTPController::class, 'regenerateOTP'])->name('regenerateOTP')->middleware('authenticate.user');
    Route::get('/notification', [NotificationController::class, 'NotificationList'])->name('admin.notification');
    Route::delete('/notification/delete/{id}', [NotificationController::class, 'delete_notification'])->name('admin.delete_notification');

    Route::post('/login', [UserAuthController::class, 'login']);
    Route::get('/logout', [UserAuthController::class, 'logout']);
    Route::get('/password-reset', [UserAuthController::class, 'changePassword'])->name('expiry.password')->Middleware('auth');
    Route::post('/password-reset', [UserAuthController::class, 'savechangePassword'])->name('save.password.change')->Middleware('auth');
    Route::group(['middleware' => ['authenticate.user', 'authorise.user', 'expiry_password', 'otp']], function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard.home');
        Route::get('/dashboard/{company_id}', [DashboardController::class, 'dashboard'])->name('company.dashboard');
        Route::post('/get_employee_total', [DashboardController::class, 'get_employeedata']);

        // Route::get('/mypage', [MyPageController::class, 'myPage']);
        //Route::post('/get_employee_total', [MyPageController::class, 'get_employeedata']);

        Route::get('/abilities', [AbilityController::class, 'abilities']);
        Route::get('/create_ability', [AbilityController::class, 'create_ability']);
        Route::post('/store_ability', [AbilityController::class, 'store_ability']);
        Route::get('/edit_ability/{id}', [AbilityController::class, 'edit_ability']);
        Route::post('/update_ability', [AbilityController::class, 'update_ability']);

        Route::get('/roles', [RoleController::class, 'roles']);
        Route::get('/rlog', [RoleController::class, 'Logs']);
        Route::get('/roles/download-excel',[RoleController::class,'export_excel']);
        Route::get('/logroles/download-excel',[RoleController::class,'export_logexcel']);
        Route::get('/create_role', [RoleController::class, 'create_role']);
        Route::post('/store_role', [RoleController::class, 'store_role']);
        Route::get('/edit_role/{id}', [RoleController::class, 'edit_role']);
        Route::post('/update_role', [RoleController::class, 'update_role']);
        Route::post('/change_role_status', [RoleController::class, 'change_role_status']);

        // Company Routes
        Route::get('/logcompany/download-excel',[DivisionController::class,'export_logexcel']);
        Route::get('/clog-company', [DivisionController::class, 'Logs']);
        Route::get('/company/download-excel',[DivisionController::class,'export_excel']);
        Route::get('/company', [DivisionController::class, 'division_list']);
        Route::get('/company/create', [DivisionController::class, 'create_division']);
        Route::post('/company/store', [DivisionController::class, 'store_division']);
        Route::get('/company/edit/{id}', [DivisionController::class, 'edit_division']);
        Route::get('/company/manage/{id}', [DivisionController::class, 'manage_division']);
        // Route::post('/company/addlocation', [DivisionController::class, 'addnewlocation'])->name('newlocation');
        Route::post('/company/update', [DivisionController::class, 'update_division']);
        Route::delete('/company/delete/{id}', [DivisionController::class, 'delete_division']);


        //Entity Controller
        Route::get('/entity/{id}', [EntityController::class, 'entity_view']);


        // Location Routes
        Route::get('/locations', [LocationController::class, 'location_list']);
        Route::get('/locations/create', [LocationController::class, 'create_location']);
        Route::post('/locations/store', [LocationController::class, 'store_location']);
        Route::post('/locations/store_floor_plan', [LocationController::class, 'store_floor_plan']);
        Route::get('/locations/edit/{id}', [LocationController::class, 'edit_location']);
        Route::get('/locations/manage_floor/{id}', [LocationController::class, 'manage_floor']);
        Route::post('/locations/update', [LocationController::class, 'update_location']);
        Route::delete('/locations/delete/{id}', [LocationController::class, 'delete_location']);



        // Department Routes 
        Route::get('/dlog-department', [DepartmentNVController::class, 'Logs']);
        Route::get('/logdepartment/download-excel',[DepartmentNVController::class,'export_logexcel']);
        Route::get('/department/download-excel',[DepartmentNVController::class,'export_excel']);
        Route::get('/department', [DepartmentNVController::class, 'department_nv_list']);
        Route::get('/department/create', [DepartmentNVController::class, 'create_nv_department']);
        Route::post('/department/store', [DepartmentNVController::class, 'store_nv_department']);
        Route::get('/department/edit/{id}', [DepartmentNVController::class, 'edit_nv_department']);
        Route::post('/department/update', [DepartmentNVController::class, 'update_nv_department']);
        Route::delete('/department/delete/{id}', [DepartmentNVController::class, 'delete_nv_department']);

         // SuperDepartment Routes 
         Route::get('/logsupdepartment/download-excel',[SuperDepartmentController::class,'export_logexcel']);
         Route::get('/log-supdepartment', [SuperDepartmentController::class, 'Logs']);
         Route::get('/supdepartment/download-excel',[SuperDepartmentController::class,'export_excel']);
         Route::get('/supdepartment', [SuperDepartmentController::class, 'supdepartment_list']);
         Route::get('/supdepartment/create', [SuperDepartmentController::class, 'create_supdepartment']);
         Route::post('/supdepartment/store', [SuperDepartmentController::class, 'store_supdept']);
         Route::get('/supdepartment/edit/{id}', [SuperDepartmentController::class, 'edit_supdepartment']);
         Route::post('/supdepartment/update', [SuperDepartmentController::class, 'update_supdepartment']);
         Route::delete('/supdepartment/delete/{id}', [SuperDepartmentController::class, 'delete_supdepartment']);

        // Service Routes 
        Route::get('/shlog-service', [DepartmentController::class, 'Logs']);
        Route::get('/logservice/download-excel',[DepartmentController::class,'export_logexcel']);
        Route::get('/service/download-excel',[DepartmentController::class,'export_excel']);
        Route::get('/listservice', [DepartmentController::class, 'department_list']);
        Route::get('/service/create', [DepartmentController::class, 'create_department']);
        Route::post('/service/store', [DepartmentController::class, 'store_department']);
        Route::get('/service/edit/{id}', [DepartmentController::class, 'edit_department']);
        Route::post('/service/update', [DepartmentController::class, 'update_department']);
        Route::delete('/service/delete/{id}', [DepartmentController::class, 'delete_department']);

        // Workflow Routes
        Route::get('/nvlog', [WorkFlowController::class, 'Logs']);
        Route::get('/logWorkflow/download-excel',[WorkFlowController::class,'export_logexcel']);
        Route::get('/Workflow/download-excel',[WorkFlowController::class,'export_excel']);
        Route::get('/Workflow', [WorkFlowController::class, 'workFlow_list']);
        Route::get('/Workflow/create', [WorkFlowController::class, 'create_workFlow']);
        Route::post('/Workflow/store', [WorkFlowController::class, 'store_workflow']);
        Route::get('/Workflow/edit/{id}', [WorkFlowController::class, 'edit_workFlow']);
        Route::post('/Workflow/update', [WorkFlowController::class, 'update_workflow']);
        Route::post('/Workflow/changeUser', [WorkFlowController::class, 'changeUser'])->name('admin.changeUser');

        // Workflow Routes
        Route::get('/opex_nvlog', [OpexWorkFlowController::class, 'Logs']);
        Route::get('/opex_logWorkflow/download-excel',[OpexWorkFlowController::class,'export_logexcel']);
        Route::get('/opex_Workflow/download-excel',[OpexWorkFlowController::class,'export_excel']);
        Route::get('/opex_Workflow', [OpexWorkFlowController::class, 'workFlow_list']);
        Route::get('/opex_Workflow/create', [OpexWorkFlowController::class, 'create_workFlow']);
        Route::post('/opex_Workflow/store', [OpexWorkFlowController::class, 'store_workflow']);
        Route::get('/opex_Workflow/edit/{id}', [OpexWorkFlowController::class, 'edit_workFlow']);
        Route::post('/opex_Workflow/update', [OpexWorkFlowController::class, 'update_workflow']);
        Route::post('/opex_Workflow/changeUser', [OpexWorkFlowController::class, 'changeUser'])->name('admin.changeUser');

         // Multiple workflow routes
         Route::get('/noteslog', [AdminWorkflowController::class, 'Logs']);
         Route::get('/lognotesworkflows/download-excel',[AdminWorkflowController::class,'export_logexcel']);
         Route::get('/notesworkflows/download-excel',[AdminWorkflowController::class,'export_excel']);
         Route::get('/notesworkflows', [AdminWorkflowController::class, 'notesworkflows_list'])->name('admin.notesworkflows_list');
         Route::get('/notesworkflows/create', [AdminWorkflowController::class, 'create_notesworkflows'])->name('admin.create_notesworkflows');
         Route::post('/notesworkflows/store', [AdminWorkflowController::class, 'store_notesworkflows'])->name('admin.store_notesworkflows');
         Route::get('/notesworkflows/{workflow}/edit', [AdminWorkflowController::class, 'edit_notesworkflows'])->name('admin.edit_notesworkflows');
         Route::post('/notesworkflows/{workflow}', [AdminWorkflowController::class, 'update_notesworkflows'])->name('admin.update_notesworkflows');

         //Multiple stages on Multiple routes

        // Employee Routes changeEmployeeEmail
        Route::get('/elog', [EmployeeController::class, 'Logs']);
        Route::get('/logemployees/download-excel',[EmployeeController::class,'export_logexcel']);
        Route::get('/employees/download-excel',[EmployeeController::class,'export_excel']);
        Route::get('/employees/download-pdf',[EmployeeController::class,'export_pdf']);
        Route::get('/employees', [EmployeeController::class, 'employee_list']);
        Route::get('/employees/create', [EmployeeController::class, 'create_employee']);
        
        Route::post('/employees/store', [EmployeeController::class, 'store_employee']);
        Route::get('/employees/edit/{id}', [EmployeeController::class, 'edit_employee']);
        Route::post('/employees/update', [EmployeeController::class, 'update_employee']);
        Route::post('/employees/location', [EmployeeController::class, 'getLocationByDivision']);

        Route::delete('/employees/delete/{id}', [EmployeeController::class, 'delete_employee']);

        // Itemissue Routes employee
        Route::get('/changeemployeeemail', [ItemIssueController::class, 'changeEmployeeEmail'])->name('change.employeeEmail');
        // mucode 

        Route::group(['middleware' => ['otp']], function () {
            //Opex Master Routes 
            Route::prefix('opex')->group(function () {
                Route::post('/update-dummy-department',[OpexController::class,'updateDummyDepartment']);
                Route::post('/cancel-dummy-department', [OpexController::class, 'cancelDummyDepartment']);
                Route::get('/logdownload-excel',[OpexController::class,'export_logexcel']);
               
                Route::get('/download-excel',[OpexController::class,'export_excel']);
                Route::get('/opex_otp', [OpexController::class, 'opex_otp'])->name('opex_otp');
                Route::get('/', [OpexController::class, 'opex_list']);
                Route::get('/create', [OpexController::class, 'create_opex']);
                Route::post('/store', [OpexController::class, 'store_opex']);
                Route::get('/edit/{id}', [OpexController::class, 'edit_opex'])->name('opex.edit');
                Route::post('/update', [OpexController::class, 'update_opex']);
                Route::delete('/delete/{id}', [OpexController::class, 'delete_opex']);
                Route::post('/upload', [OpexController::class, 'upload'])->name('opex.upload');
            });
            Route::get('/list-dummy-department',[CapexController::class,'DummyList']);
            Route::get('/list-provisional',[CapexController::class,'ProvisionalList']);
            Route::post('/save-budget',[CapexController::class,'saveBudget']);
         
            // Capex Master Routes 
            Route::prefix('capexmaster')->group(function () {
                Route::post('/cancel-dummy-department', [CapexController::class, 'cancelDummyDepartment']);

                Route::post('/update-dummy-department',[CapexController::class,'updateDummyDepartment']);
                Route::get('/logdownload-excel',[CapexController::class,'export_logexcel']);
                Route::get('/capex_otp', [CapexController::class, 'capex_otp'])->name('capex_otp');
                Route::get('/download-excel',[CapexController::class,'export_excel']);
                Route::get('/', [CapexController::class, 'capex_list']);
                Route::get('/create', [CapexController::class, 'create_capex_master']);
                Route::post('/store', [CapexController::class, 'store_capex_master']);
                Route::get('/edit/{id}', [CapexController::class, 'edit_capex_master'])->name('capex.edit');
                Route::post('/update', [CapexController::class, 'update_capex_master']);
                Route::delete('/delete/{id}', [CapexController::class, 'delete_capex_master']);
                Route::post('/upload', [CapexController::class, 'upload'])->name('capex.upload');
                Route::post('/autofetch', [CapexController::class, 'autofetchdata'])->name('capex.auto.fetch.data');
                Route::post('/autofetchservice', [CapexController::class, 'autofetchdata_service'])->name('capex.auto.fetch.servicedata');
            });
        });
        Route::get('/capex_list', [CapexController::class, 'capex_list']);
        // Tax Master Routes 
        Route::get('/tlogs', [TaxController::class, 'Logs']);
        Route::get('/logtax/download-excel',[TaxController::class,'export_logexcel']);
        Route::get('/tax', [TaxController::class, 'tax_list'])->name('admin.tax.list');
        Route::get('/tax/create', [TaxController::class, 'create_tax'])->name('admin.tax.create');
        Route::post('/tax/store', [TaxController::class, 'store_tax'])->name('admin.tax.store');
        Route::get('/tax/edit/{id}', [TaxController::class, 'edit_tax'])->name('admin.tax.edit');
        Route::post('/tax/update', [TaxController::class, 'update_tax'])->name('admin.tax.update');


        //Create Serviceboq Routes
        Route::get('/slog', [ServiceBoq::class, 'Logs']);
        Route::get('/serviceboq/download-excel',[ServiceBoq::class,'export_excel']);
        Route::get('/logserviceboq/download-excel',[ServiceBoq::class,'export_logexcel']);
        Route::get('/serviceboq', [ServiceBoq::class, 'serviceboq_list']);
        Route::get('/serviceboq/create', [ServiceBoq::class, 'create_serviceboq']);
        Route::post('/serviceboq/store', [ServiceBoq::class, 'store_serviceboq']);
        Route::get('/serviceboq/edit/{id}', [ServiceBoq::class, 'edit_serviceboq']);
        Route::post('/serviceboq/update', [ServiceBoq::class, 'update_serviceboq']);
        Route::delete('/serviceboq/delete/{id}', [ServiceBoq::class, 'delete_serviceboq']);
        Route::post('/serviceboq/upload', [ServiceBoq::class, 'upload']);
        //Create Materialboq Routes
        Route::get('/mlog', [MaterialBoq::class, 'Logs']);
        Route::get('/materialboq/download-excel',[MaterialBoq::class,'export_excel']);
        Route::get('/logmaterialboq/download-excel',[MaterialBoq::class,'export_logexcel']);
        Route::get('/materialboq', [MaterialBoq::class, 'materialboq_list']);
        Route::get('/materialboq/create', [MaterialBoq::class, 'create_materialboq']);
        Route::post('/materialboq/store', [MaterialBoq::class, 'store_materialboq']);
        Route::get('/materialboq/edit/{id}', [MaterialBoq::class, 'edit_materialboq']);
        Route::post('/materialboq/update', [MaterialBoq::class, 'update_materialboq']);
        Route::delete('/materialboq/delete/{id}', [MaterialBoq::class, 'delete_materialboq']);
        Route::post('/materialboq/upload', [MaterialBoq::class, 'upload']);

        Route::get('/consolidated_report/download-excel',[reportController::class,'export_excel']);

        //nv service preview

        // NV Material Routes
        Route::get('/nv_service/previewAttachedFiles/{id}',[NvServiceController::class,'AttachedFiles']);

        Route::get('/nv_material/previewAttachedFiles/{id}',[NvMaterialController::class,'AttachedFiles']);
        Route::get('/nv_material', [NvMaterialController::class, 'nv_material_list']);
        Route::get('/nv_material/create/{id}/{company_id}/{material_id}', [NvMaterialController::class, 'create_nv_material']);
        Route::get('/nv_material/create/{id}/{company_id}', [NvMaterialController::class, 'create_nv_material']);
        Route::get('/nv_material/delete/{id}', [NvMaterialController::class, 'delete_nv_material']);
        Route::get('/nv_service/delete/{id}', [NvServiceController::class, 'delete_nv_service']);
        Route::post('/send-email', [NvMaterialController::class,'sendEmail1']);
        Route::post('/send-clarification', [NvMaterialController::class,'sendclarification']);
        Route::post('/send-email2', [NvMaterialController::class,'sendEmail2']);
        Route::get('/download-nv-pdf/{id}/{userId}', [NvMaterialController::class,'downloadNvPdf'])->name('download.nv.pdf');
         Route::get('/download-nvmaterialboq-pdf/{id}/{userId}', [NvMaterialController::class,'downloadNvMaterialboqPdf'])->name('download.nvmaterialboq.pdf');
        Route::get('/download-nvservice-pdf/{id}/{userId}', [NvServiceController::class,'downloadNvservicePdf'])->name('download.nvservice.pdf');
        Route::get('/download-attachments/{id}/{userId}', [NvMaterialController::class,'downloadMaterialFiles'])->name('download.attachments');
        Route::get('/download-service-attachments/{id}/{userId}', [NvServiceController::class,'downloadServiceFiles'])->name('download.serviceattachments');
        Route::post('/nv_material/store', [NvMaterialController::class, 'store_nv_material']);
        Route::get('/nv_material/edit/{id}', [NvMaterialController::class, 'edit_nv_material']);
        Route::post('/nv_material/update', [NvMaterialController::class, 'update_nv_material']);
        Route::delete('/nv_material/delete/{id}', [NvMaterialController::class, 'delete_material']);
        Route::delete('/nv_material/delete/all/{id}', [NvMaterialController::class, 'delete_all_materials']);
        Route::get('nv_material/preview/{nv_id}/{material_id}', [NvMaterialController::class, 'preview']);
        Route::get('nv_material/preview/{nv_id}', [NvMaterialController::class, 'preview']);
        Route::post('nv_material/approvedByStatus', [NvMaterialController::class, 'approvedByStatus']);
        Route::post('nv_material/bulkProviderStore', [NvMaterialController::class, 'bulkProviderStore']);
        Route::get('nv_material/edit_material/{id}/{year}', [NvMaterialController::class, 'edit_material']);
        // Route::get('/nv_material/delete/{id}', [NvMaterialController::class, 'delete_material'])->name('delete_material');
        Route::post('nv_material/update_material', [NvMaterialController::class, 'update_material']);
        Route::post('nv_material/removeMaterialAmount', [NvMaterialController::class, 'removeMaterialAmount']);
        Route::post('nv_material/removeServiceAmount', [NvMaterialController::class, 'removeServiceAmount']);
        Route::post('nv_material/SchemeButtonRemove', [NvMaterialController::class, 'SchemeButtonRemove']);

        Route::post('nv_material/bulkServiceProviderStore', [NvMaterialController::class, 'bulkServiceProviderStore']);
        Route::get('nv_material/edit_service/{id}', [NvMaterialController::class, 'edit_service']);
        Route::post('nv_material/update_service', [NvMaterialController::class, 'update_service']);
        Route::delete('/nv_material/service_delete/{id}', [NvMaterialController::class, 'delete_service']);
        Route::delete('/nv_material/service_delete/all/{id}', [NvMaterialController::class, 'delete_all_service']);
        Route::post('nv_material/serviceBoqStore', [NvMaterialController::class, 'serviceBoqStore']);
        Route::post('/nv_material/store_material_boq', [NvMaterialController::class, 'store_material_boq']);
        Route::get('/nv_materialBoq', [NvMaterialController::class, 'list_materialBoq']);
        Route::get('/nv_material/data', [NvMaterialController::class, 'datafetch']);
	    // Route::get('/material/nv_serviceBoq', [NvMaterialController::class, 'list_serviceBoq']);
        Route::get('/nv_material/search_material_by_name', [NvMaterialController::class, 'search_material_by_name'])->name('material.search');
        // Route::post('/nv_material/store_service_boq', [NvMaterialController::class, 'store_service_boq']);
        Route::post('/fetch-material-data', [NvMaterialController::class, 'fetchMaterialData']);
        Route::post('/fetch-service-data', [NvMaterialController::class, 'fetchServiceData']);

        // NV Service Routes
        Route::delete('/deleteMaterial-file', [NvMaterialController::class, 'deleteFile'])->name('delete.file');
        Route::delete('/delete-file', [NvServiceController::class, 'deleteFile'])->name('delete.file');
        Route::get('/nv_service/data', [NvServiceController::class, 'datafetch']);
        Route::get('/nv_service', [NvServiceController::class, 'nv_service_list']);
        Route::get('/nv_service/create/{id}/{company_id}/{service_id}', [NvServiceController::class, 'create_nv_service']);
        Route::get('/nv_service/create/{id}/{company_id}', [NvServiceController::class, 'create_nv_service']);
        Route::post('/send-email3', [NvServiceController::class,'sendEmail3']);
        Route::post('/send-Clarification', [NvServiceController::class,'sendclarification']);
        Route::get('/send-email4', [NvServiceController::class,'sendEmail4']);
        Route::post('/nv_service/store', [NvServiceController::class, 'store_nv_service']);
        Route::get('/nv_service/edit/{id}', [NvServiceController::class, 'edit_nv_service']);
        Route::post('/nv_service/update', [NvServiceController::class, 'update_nv_service']);
        Route::delete('/nv_service/delete/{id}', [NvServiceController::class, 'delete_nv_service']);
        Route::get('nv_service/preview/{nv_id}/{service_id}', [NvServiceController::class, 'preview']);
        Route::get('nv_service/preview/{nv_id}', [NvServiceController::class, 'preview']);
        Route::post('nv_service/approvedByStatus', [NvServiceController::class, 'approvedByStatus']);
        Route::post('nv_service/bulkServiceProviderStore', [NvServiceController::class, 'bulkServiceProviderStore']);
        Route::get('/nv_serviceBoq',[NvMaterialController::class, 'list_serviceBoq'])->name('list_serviceBoq');
        // Route::get('/nv_serviceBoqs', [NvServiceController::class, 'list_serviceBoq']);
        Route::get('nv_service/edit_service/{id}', [NvServiceController::class, 'edit_service']);
        // Route::get('nv_service/approvedByStatus', [NvServiceController::class, 'approvedByStatus']);
        Route::post('nv_service/serviceBoqStore', [NvServiceController::class, 'serviceBoqStore']);
        Route::delete('/nv_service/service_delete/all/{id}', [NvServiceController::class, 'delete_all_service']);
        Route::post('nv_service/removeServiceAmount', [NvServiceController::class, 'removeServiceAmount']);


        // reports Routes
        Route::get('/reports', [reportController::class, 'reports_list']);
        Route::get('/nv_tracker_list', [reportController::class, 'nv_tracker_list']);
        //upload .dwg file
         Route::post('/upload/dwg_file',[NVController::class,'uploadDWGFile']);
        //Scheme Api Routes
        Route::post('/fetchDataApi', [NVController::class, 'fetchDataApi']);
        //Brand Routes
        Route::get('/needvalidation/list', [NVController::class, 'NV_list']);
        // Route::get('/needvalidations/ticket_list', [NVController::class, 'ticket_list']);
        Route::get('/needvalidations/create', [NVController::class, 'create_NV']);
        Route::post('/needvalidations/store', [NVController::class, 'store_NV']);
        Route::get('/needvalidations/edit/{id}', [NVController::class, 'edit_NV']);
        Route::post('/needvalidations/update/{id}', [NVController::class, 'update_NV'])->name('nvs.update');
        Route::get('/needvalidations/edit_need/{id}', [NVController::class, 'edit_need']);
        Route::delete('/needvalidations/delete/{id}', [NVController::class, 'delete_NV']);

        //NV Notes Routes
        Route::get('/nv-notes/create', [NVNotesController::class, 'create_Notes']);
        Route::post('/nv-notes/store', [NVNotesController::class, 'store_Notes']);
        Route::post('/nv-notes/list',[NVNotesController::class,'list_Notes']);
        Route::get('/nv-notes/edit/{id}', [NVNotesController::class, 'edit_Notes']);
        Route::post('/nv-notes/update/{id}', [NVNotesController::class, 'update_Notes']);


        Route::get('/view/nv_material/{id}', [NVController::class, 'createPrintMaterial']);
        Route::get('/view/nv_service/{id}', [NVController::class, 'createPrintService']);
        Route::get('/create-view/{id}', [NVController::class, 'createView'])->name('admin.view');
        Route::post('/store-signature', [DashboardController::class, 'addSignature'])->name('signature.store');
        Route::post('/log-signature', [DashboardController::class, 'logSignature']);

        Route::post('/profile-image',[ProfileImageController::class,'uploadImage'])->name('upload.image');
         Route::post('/password/send-otp', [ProfileImageController::class, 'sendOtp'])->name('password.sendOtp');
        Route::post('/password/verify-otp', [ProfileImageController::class, 'verifyOtp'])->name('password.verifyOtp');
        Route::post('/password/change', [ProfileImageController::class, 'changePassword'])->name('password.change');
        Route::get('/fetch-sender-email', [NvMaterialController::class,'fetchSenderEmail']);

    });
  
  
    Route::post('/dashboard/download-pdf',[DashboardController::class,'export_NV_pdf']);

    Route::post('/dashboard/download-excel',[DashboardController::class,'export_NV_excel']);
    Route::post('/needvalidation/list/download-pdf',[NVController::class,'export_pdf'])->name('pdfExport');
    Route::post('/needvalidation/list/download-excel',[NVController::class,'export_excel']);
    Route::post('/dashboard/fiscal_year',[DashboardController::class,'fiscal_year']);
    Route::post('/store_data',[NvMaterialController::class,'store_data']);
    Route::post('/needvalidation/list/list_nv',[NVController::class,'list_nv']);
    Route::post('/store_material',[NvMaterialController::class,'store_material']);
    Route::post('/store_service',[NvMaterialController::class,'store_service']);
    Route::post('/store_form',[NvMaterialController::class,'store_form']);
    Route::post('/store_service_form',[NvServiceController::class,'store_service_form']);
    Route::get('/user_otp',[OTPController::class,'otp_list']);
    Route::get('/otp_edit/{id}',[OTPController::class,'otp_edit']);
    Route::post('/otp/update',[OTPController::class,'otp_update']);
    Route::get('/ulog',[OTPController::class,'Logs']);
    Route::get('/logotp/download-excel',[OTPController::class,'export_logexcel']);
    Route::get('/rejectedlist',[DashboardController::class,'show_list'])->name('list_reject');
    Route::get('/deletelist',[NVController::class,'show_list'])->name('list_delete');
    Route::post('/fetch_data/{nv_id}/{id}',[NvMaterialController::class,'fetch_data']);
    Route::post('/fetch_data_service/{nv_id}/{id}',[NvServiceController::class,'fetch_data_service']);
    Route::get('/ologs', [OpexController::class, 'Logs']);
    Route::get('/blog', [CapexController::class, 'Logs']);
    Route::get('/getSubDepartments_employee/{id}', [EmployeeController::class,'getSubDepartments']);
    Route::get('/getSubDepartments1', [EmployeeController::class,'getSubDepartments1']);

    Route::get('/getSubDepartments_capex/{id}', [CapexController::class,'getSubDepartments']);
    Route::get('/getSubDepartments1_capex', [CapexController::class,'getSubDepartments1']);

    Route::get('/getSubDepartments_opex/{id}', [OpexController::class,'getSubDepartments']);
    Route::get('/getSubDepartments1_opex', [OpexController::class,'getSubDepartments1']);
});
  

