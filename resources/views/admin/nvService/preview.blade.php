@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{asset('admin/css/styles.css')}}">
<style>
    .mandatory_input {
        color: red;
    }

    .popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fff;
        border: 1px solid #000;
        padding: 20px;
        z-index: 999;
    }

    input[readonly] {
        background: #eee;
        pointer-events: none;
        touch-action: none;
    }

    .select2-container {
        width: 300px !important;
    }

    .signature .card {
        box-shadow: none !important;
    }

    .signature .card img {
        position: absolute;
        top: 20px;
    }
</style>


@endpush

@extends('admin.layout.master', ['page_title' => 'Preview NV Service'])
@php
$id = request()->segment(4);
$user = \Auth()->user();

$segment_id =request()->segment(5);


if(!empty($segment_id)){

$data = App\Models\NVService::where('id', $segment_id)->orderBy('id', 'desc')->first();

$Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $segment_id)->exists();

if ($Nvsericestatus) {
$Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $segment_id)->first();
} else {
$Nvsericestatus = App\Models\Nvsericestatus::where('material_id', $segment_id)->first();
}
}else{
$data = App\Models\NVService::where('nv_id', $id)->orderBy('id', 'desc')->first();

$Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $data->id)->exists();

if ($Nvsericestatus) {
$Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $data->id)->first();
} else {
$Nvsericestatus = App\Models\Nvsericestatus::where('material_id', $data->id)->first();
}

}

$employee = App\Models\Employee::with('department')->where("user_id", $data->user_id)->first();

if ($employee && $employee->department) {
$department = $employee->department;

$hod = $department->dep_hod;
$rv1 = $department->dep_rew1;
$rv2 = $department->dep_rew2;
$rv3 = $department->dep_rew3;
$rv4 = $department->dep_rew4;
$group_cio = $department->group_cio;
} else {
$hod = $rv1 = $rv2 = $rv3 = $rv4 = $group_cio = null;
}


@endphp


@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Preview NV Service</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ URL::to('/admin/dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Preview NV Service</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
@php
$user = \Auth::user();
@endphp
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card card-info  shadow-lg printout show-table-data-nv-material">
                    <div class="card-header bg-transparent">


                        <div class="card-title ">
                            <h3 class="card-title">Need Validation Service Details<br>
                                NV Number: {{'NV' . '/' . $nv->budget_type . '/' . $nv->fiscal_year . '/' . getDepartmentNameByPro($nv->user_id) . '/' . $nv->service->name . '/' . $nv->id}}</h3>
                        </div>

                        <div class="card-toolbar">
                            <button type="reset" class="btn btn-primary " onclick="history.back();">
                                << Back</button>

                        </div>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form id="preview_nvservice">
                        @csrf

                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-12 mb-2">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item ">
                                            <h6 class="accordion-header" type="button" data-toggle="collapse"
                                                data-target="#collapsefive" aria-expanded="true"
                                                aria-controls="collapsefive">
                                                All Attachments ({{$countFiles}})</h6>

                                            <div id="collapsefive" class="collapse hide" aria-labelledby="collapsefive"
                                                data-parent="#accordionExample">
                                                <div class="card-body">

                                                    @if (!empty($service_doc->cost_calculation_for_service)||!empty($service_doc->vend_quatation)||
                                                    !empty($service_doc->copy_of_previous_work)||!empty($service_doc->copy_of_derc_other)||
                                                    !empty($service_doc->consuption_details)||!empty($service_doc->buget_stmt_for_both)||
                                                    !empty($service_doc->photographs_of_product)||!empty($service_doc->material_procurement)||
                                                    !empty($service_doc->vendor_quatation)||!empty($service_doc->others)
                                                    ||!empty($service_doc->cm_rate_ref)||!empty($service_doc->vendor_quat)||
                                                    !empty($service_doc->last_purchase_price)||!empty($service_doc->user_estimation)||
                                                    !empty($service_doc->special_attch)||!empty($service_doc->past_practice)||!empty($service_doc->previous_wo_rc))
                                                    <div class="form-group">
                                                        <input type="checkbox" id="select_all">
                                                        <label for="select_all">Select All</label>

                                                    </div>
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">

                                                                <label for="exampleFormControlInput1">Cost Caluculation For Services: <br>
                                                                    @if (!empty($service_doc->cost_calculation_for_service))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/'. $service_doc->cost_calculation_for_service) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/'. $service_doc->cost_calculation_for_service) }}" data-field="cost_calculation_for_service" title="Download">
                                                                        {{ $service_doc->cost_calculation_for_service }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>

                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">

                                                                <label for="exampleFormControlInput1">C&M Rate Reference: <br>
                                                                    @if (!empty($service_doc->cm_rate_ref))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/'. $service_doc->cm_rate_ref) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/'. $service_doc->cm_rate_ref) }}" data-field="cm_rate_ref" title="Download">
                                                                        {{ $service_doc->cm_rate_ref }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>

                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">

                                                                <label for="exampleFormControlInput1">Vendor
                                                                    Quotation: <br>
                                                                    @if (!empty($service_doc->vendor_quat))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/'. $service_doc->vendor_quat) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/'. $service_doc->vendor_quat) }}" data-field="vendor_quat" title="Download">
                                                                        {{ $service_doc->vendor_quat }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>

                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">

                                                                <label for="exampleFormControlInput1">Last Purchase
                                                                    Price: <br>
                                                                    @if (!empty($service_doc->last_purchase_price))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/'. $service_doc->last_purchase_price) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/'. $service_doc->last_purchase_price) }}" data-field="last_purchase_price" title="Download">
                                                                        {{ $service_doc->last_purchase_price }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>

                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">

                                                                <label for="exampleFormControlInput1">User
                                                                    Estimation: <br>
                                                                    @if (!empty($service_doc->user_estimation))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/'. $service_doc->user_estimation) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/'. $service_doc->user_estimation) }}" data-field="user_estimation" title="Download">
                                                                        {{ $service_doc->user_estimation }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>

                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">

                                                                <label for="exampleFormControlInput1">Previous WO/RC (if any): <br>
                                                                    @if (!empty($service_doc->previous_wo_rc))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/'. $service_doc->previous_wo_rc) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/'. $service_doc->previous_wo_rc) }}" data-field="previous_wo_rc" title="Download">
                                                                        {{ $service_doc->previous_wo_rc }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>

                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">

                                                                <label for="exampleFormControlInput1">Special Remarks: <br>
                                                                    @if (!empty($service_doc->special_attch))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/'. $service_doc->special_attch) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/'. $service_doc->special_attch) }}" data-field="special_attch" title="Download">
                                                                        {{ $service_doc->special_attch }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>

                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">

                                                                <label for="exampleFormControlInput1">Past Practice Followed (If Any): <br>
                                                                    @if (!empty($service_doc->past_practice))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/'. $service_doc->past_practice) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/'. $service_doc->past_practice) }}" data-field="past_practice" title="Download">
                                                                        {{ $service_doc->past_practice }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>

                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Copy Of Previous Work Order/Purchase Order : <br>

                                                                    @if (!empty($service_doc->copy_of_previous_work))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/' . $service_doc->copy_of_previous_work) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/' . $service_doc->copy_of_previous_work) }}" data-field="copy_of_previous_work" title="Download">
                                                                        {{ $service_doc->copy_of_previous_work }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif
                                                                </label>

                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"> Copy Of DERC/Other Stakeholder Approvals : <br>
                                                                    @if (!empty($service_doc->copy_of_derc_other))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/' . $service_doc->copy_of_derc_other) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/' . $service_doc->copy_of_derc_other) }}" data-field="copy_of_derc_other" title="Download">
                                                                        {{ $service_doc->copy_of_derc_other }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>

                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"> Consumption Details Last 3 Years : <br>
                                                                    @if (!empty($service_doc->consuption_details))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/' . $service_doc->consuption_details) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/' . $service_doc->consuption_details) }}" data-field="consuption_details" title="Download">
                                                                        {{ $service_doc->consuption_details }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif
                                                                </label>

                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"
                                                                    title="Material Trail Details and Schudule of Pilot feedback Submission">Budget Statement For Both OPEX/CAPEX Activities : <br>
                                                                    @if (!empty($service_doc->buget_stmt_for_both))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/' . $service_doc->buget_stmt_for_both) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/' . $service_doc->buget_stmt_for_both) }}" data-field="buget_stmt_for_both" title="Download">
                                                                        {{ $service_doc->buget_stmt_for_both }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"> Photographs Of Product/PoC In Case Of New Product Trial : <br>
                                                                    @if (!empty($service_doc->photographs_of_product))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/' . $service_doc->photographs_of_product) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/' . $service_doc->photographs_of_product) }}" data-field="photographs_of_product" title="Download">
                                                                        {{ $service_doc->photographs_of_product }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Material Procurement - Details Of Material, Code, Qty, Rate, Amount Etc : <br>

                                                                    @if (!empty($service_doc->material_procurement))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/' . $service_doc->material_procurement) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/' . $service_doc->material_procurement) }}" data-field="material_procurement" title="Download">
                                                                        {{ $service_doc->material_procurement }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Technical Specifications : <br>
                                                                    @if (!empty($service_doc->vendor_quatation))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/' . $service_doc->vendor_quatation) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/' . $service_doc->vendor_quatation) }}" data-field="vendor_quatation" title="Download">
                                                                        {{ $service_doc->vendor_quatation }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Vendor Quatation : <br>
                                                                    @if (!empty($service_doc->vend_quatation))
                                                                    <input type="checkbox" class="selectedImage">
                                                                    <a href="{{ asset('services-doc/' . $service_doc->vend_quatation) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/' . $service_doc->vend_quatation) }}" data-field="vend_quatation" title="Download">
                                                                        {{ $service_doc->vend_quatation }}
                                                                    </a>
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>


                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"> Others : <br>
                                                                    @if (!empty($service_doc->others))
                                                                    @php
                                                                    $images = explode(',', $service_doc->others);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('services-doc/' . $image) }}">
                                                                    <a href="{{ asset('services-doc/' . $image) }}" class="ml-2 download-link" target="_blank" data-file="{{ asset('services-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif



                                                                </label>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if (!empty($service_doc->cost_calculation_for_service)||!empty($service_doc->vend_quatation)||!empty($service_doc->copy_of_previous_work)||!empty($service_doc->copy_of_derc_other)||!empty($service_doc->consuption_details)||!empty($service_doc->buget_stmt_for_both)||!empty($service_doc->photographs_of_product)||!empty($service_doc->material_procurement)||!empty($service_doc->vendor_quatation)||!empty($service_doc->others)
                                                    ||!empty($service_doc->cm_rate_ref)||!empty($service_doc->vendor_quat)||!empty($service_doc->last_purchase_price)||!empty($service_doc->user_estimation)||!empty($service_doc->previous_wo_rc)||!empty($service_doc->special_attch)||!empty($service_doc->past_practice))
                                                    <!-- <a href="/admin/download-service-attachments/{{$service_details->nv_id}}/{{$service_details->company_id}}"><button type="button" class="btn btn-success" >Download All</button></a>&nbsp;&nbsp; -->
                                                    <button type="button" class="btn btn-success" id="selectedDownloadButton">Selected Download</button>
                                                    <a target="_blank" href=" {{url('/admin/nv_service/previewAttachedFiles', ['id' => $service_details->nv_id]) }}"><button type="button" class="btn btn-success">Preview All Attached Files</button></a>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-xl-4 col-lg-4 col-md-4">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Sub-Department Name</label>
                                        @if (!empty($service_details->dept_id))
                                        <input readonly type="text" class="form-control" name="dept_id"
                                            id="dept_id" value="{{ $service_details->department->name }}">
                                        @else
                                        <input readonly type="text" class="form-control" name="dept_id"
                                            id="dept_id" value="N/A">
                                        @endif

                                    </div>
                                </div>


                                <div class="col-xl-4 col-lg-4 col-md-4">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">DOP Reference Number </label>
                                        @if (!empty($service_details->dop_ref_no))
                                        <input type="text" readonly class="form-control" name="dop_ref_no"
                                            id="dop_ref_no" value="{{ $service_details->dop_ref_no }}">
                                        @else
                                        <input type="text" readonly class="form-control" name="dop_ref_no"
                                            id="dop_ref_no" value="N/A">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Proposal Type</label>
                                        @if (!empty($nv_year->proposal_type))
                                        <input readonly type="text" class="form-control" name="proposal_type"
                                            id="proposal_type" value="{{ $nv_year->proposal_type }}">
                                        @else
                                        <input readonly type="text" class="form-control" name="proposal_type"
                                            id="proposal_type" value="N/A">
                                        @endif

                                    </div>
                                </div>

                                <div class="col-12 col-xs-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Proposal Name (Max 1000
                                            Characters)</label>
                                        @if (!empty($service_details->proposal_name))
                                        <textarea readonly name="proposal_name" id="proposal_name" class="form-control auto-resize-textarea"
                                            value="{{ $service_details->proposal_name }}">{{ $service_details->proposal_name }}</textarea>
                                        @else
                                        <textarea readonly name="proposal_name" id="proposal_name" class="form-control auto-resize-textarea"
                                            value="N/A">N/A</textarea>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Background (Max 2500 Characters)</label>
                                        @if (!empty($service_details->background))
                                        <textarea readonly name="background" id="background" class="form-control auto-resize-textarea" value="">{{ $service_details->background }}</textarea>
                                        @else
                                        <textarea readonly name="background" id="background" class="form-control auto-resize-textarea" value="">N/A</textarea>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Broad Justification (Max 500
                                            Characters)</label>
                                        @if (!empty($service_details->broad_just))
                                        <textarea readonly name="broad_just" id="broad_just" class="form-control auto-resize-textarea"
                                            value="">{{ $service_details->broad_just }}</textarea>
                                        @else
                                        <textarea readonly name="broad_just" id="broad_just" class="form-control auto-resize-textarea"
                                            value="">N/A</textarea>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Detailed Justification (Max 2500
                                            Characters)</label>
                                        @if (!empty($service_details->just_of_proposal))
                                        <textarea readonly name="just_of_proposal" id="just_of_proposal" class="form-control auto-resize-textarea"
                                            value="">{{ $service_details->just_of_proposal }}</textarea>
                                        @if (!empty($service_doc->just_prop_upload))
                                        @php
                                        $images = explode(',', $service_doc->just_prop_upload);
                                        @endphp
                                        @foreach($images as $image)
                                        <input type="checkbox" class="selectedImage ml-2" data-file="{{ asset('services-doc/' . $image) }}">
                                        <a href="{{ asset('services-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('services-doc/' . $image) }}" data-field="others" title="Download">
                                            {{ $image }}
                                        </a><br>
                                        @endforeach
                                        @else
                                        <span>N/A</span>
                                        @endif
                                        @else
                                        <textarea readonly name="just_of_proposal" id="just_of_proposal" class="form-control auto-resize-textarea"
                                            value="">N/A</textarea>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h6 class="accordion-header" type="button" data-toggle="collapse"
                                                data-target="#collapseOne" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                Past 3 Years actual cost trend Services </h6>

                                            <div id="collapseOne" class="collapse show" aria-labelledby="collapseOne"
                                                data-parent="#accordionExample">
                                                <div class="card-body">

                                                    <div class="after-add-more">
                                                        <div class="row ">

                                                            @php
                                                            $year1 = explode(',', $service_details != '' ? $service_details->past_3_year_actual_cost_fy : '');
                                                            $year2 = explode(',', $service_details != '' ? $service_details->past_3_year_actual_cost : '');
                                                            $year3 = explode(',', $service_details != '' ? $service_details->past_3_year_actual_cost_service: '');
                                                            $year = $nv_year->fiscal_year;
                                                            $yr_l= substr($year, 5);
                                                            $yr_f= substr($year, 0,4);

                                                            @endphp


                                                            <div class="col-xl-3 col-lg-6 col-md-6 mt-2">

                                                                @if (!empty($service_details->past_3_year_actual_cost_fy))
                                                                <input type="text" class="form-control" name="past_3_year_actual_cost_fy[]"
                                                                    id="past_3_year_actual_cost_fy" value="{{$yr_f-1}}-{{$yr_l-1}}"
                                                                    readonly>

                                                                @else
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost_fy[]"
                                                                    id="past_3_year_actual_cost_fy" value="N/A">
                                                                @endif

                                                            </div>
                                                            <div class="col-xl-3 col-lg-6 col-md-6 mt-2">
                                                                @if (!empty($service_details->past_3_year_actual_cost))
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost[]"
                                                                    id="past_3_year_actual_cost" value="{{ isset($year2[0]) ? $year2[0] : '' }}" placeholder="₹">
                                                                @else
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost[]"
                                                                    id="past_3_year_actual_cost" value="N/A">
                                                                @endif

                                                            </div>

                                                            <div class="col-xl-3 col-lg-6 col-md-6 mt-2">
                                                                @if (!empty($service_details->past_3_year_actual_cost_service))
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost_service[]"
                                                                    id="past_3_year_actual_cost_service"
                                                                    value="{{ isset($year3[0]) ? $year3[0] : '' }}" placeholder="Services">
                                                                @else
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost_service[]"
                                                                    id="past_3_year_actual_cost_service" value="N/A">
                                                                @endif

                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-xl-3 col-lg-6 col-md-6 mt-2">

                                                                @if (!empty($service_details->past_3_year_actual_cost_fy))
                                                                <input type="text" class="form-control" name="past_3_year_actual_cost_fy[]"
                                                                    id="past_3_year_actual_cost_fy" value="{{$yr_f-2}}-{{$yr_l-2}}"
                                                                    readonly>

                                                                @else
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost_fy[]"
                                                                    id="past_3_year_actual_cost_fy" value="N/A">
                                                                @endif

                                                            </div>
                                                            <div class="col-xl-3 col-lg-6 col-md-6 mt-2">
                                                                @if (!empty($service_details->past_3_year_actual_cost))
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost[]"
                                                                    id="past_3_year_actual_cost" value="{{ isset($year2[1]) ? $year2[1] : '' }}" placeholder="₹">
                                                                @else
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost[]"
                                                                    id="past_3_year_actual_cost" value="N/A">
                                                                @endif
                                                            </div>

                                                            <div class="col-xl-3 col-lg-6 col-md-6 mt-2">
                                                                @if (!empty($service_details->past_3_year_actual_cost_service))
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost_service[]"
                                                                    id="past_3_year_actual_cost_service"
                                                                    value="{{ isset($year3[1]) ? $year3[1] : '' }}" placeholder="Services">
                                                                @else
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost_service[]"
                                                                    id="past_3_year_actual_cost_service" value="N/A" placeholder="Services">
                                                                @endif

                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-xl-3 col-lg-6 col-md-6 mt-2">

                                                                @if (!empty($service_details->past_3_year_actual_cost_fy))
                                                                <input type="text" class="form-control" name="past_3_year_actual_cost_fy[]"
                                                                    id="past_3_year_actual_cost_fy" value="{{$yr_f-3}}-{{$yr_l-3}}"
                                                                    readonly>

                                                                @else
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost_fy[]"
                                                                    id="past_3_year_actual_cost_fy" value="N/A">
                                                                @endif

                                                            </div>
                                                            <div class="col-xl-3 col-lg-6 col-md-6 mt-2">
                                                                @if (!empty($service_details->past_3_year_actual_cost))
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost[]"
                                                                    id="past_3_year_actual_cost" value="{{ isset($year2[2]) ? $year2[2] : '' }}" placeholder="₹">
                                                                @else
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost[]"
                                                                    id="past_3_year_actual_cost" value="N/A">
                                                                @endif
                                                            </div>

                                                            <div class="col-xl-3 col-lg-6 col-md-6 mt-2">
                                                                @if (!empty($service_details->past_3_year_actual_cost_service))
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost_service[]"
                                                                    id="past_3_year_actual_cost_service"
                                                                    value="{{ isset($year3[2]) ? $year3[2] : '' }}" placeholder="Services">
                                                                @else
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost_service[]"
                                                                    id="past_3_year_actual_cost_service" value="N/A">
                                                                @endif

                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>



                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Benefit (Max 2500 Characters)</label>
                                    @if (!empty($service_details->benefit))
                                    <textarea readonly name="benefit" id="benefit" class="form-control auto-resize-textarea"
                                        value="">{{ $service_details->benefit }}</textarea>
                                    @else
                                    <textarea readonly name="benefit" id="benefit" class="form-control auto-resize-textarea"
                                        value="">N/A</textarea>
                                    @endif
                                </div>
                            </div>

                            @php
                            $selectedYear = $service_details != '' ? $service_details->implements_years : '';

                            @endphp
                            @if(!empty($selectedYear))
                            <input type="hidden" name="implements_years" value="{{$selectedYear ?? ''}}">
                            <div class="col-12 mb-2">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseimple" aria-expanded="true" aria-controls="collapseimple">
                                            Implementation Period
                                        </h6>
                                        <div id="collapseimple" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                            <div class="card-body">

                                                @php
                                                $selectedYear = $service_details != '' ? $service_details->implements_years : '';
                                                $from = explode(',', $service_details != '' ? $service_details->implementation_period_from : '');
                                                $to = explode(',', $service_details != '' ? $service_details->implementation_period_to : '');
                                                $service = explode('.,', $service_details != '' ? $service_details->implementation_plan_year_wise : '');


                                                @endphp
                                                <input type="hidden" id="selectedYear" value="{{ $selectedYear }}">
                                                <div class="row after-add-more-third">
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                <label for="exampleFormControlSelect">Select Year</label>
                                                                {{-- ()" --}}
                                                                <select class="form-control" id="exampleFormControlSelect" name="implements_years" onchange="editDuplicateColumns()" style="display: inline-block; width:200px;" disabled>
                                                                    <option value="">Select Year</option>
                                                                    <option value="1" {{ $selectedYear == '1' ? 'selected' : '' }}>1</option>
                                                                    <option value="2" {{ $selectedYear == '2' ? 'selected' : '' }}>2</option>
                                                                    <option value="3" {{ $selectedYear == '3' ? 'selected' : '' }}>3</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                {{-- Here Ashu --}}
                                                                <div id="" class="row">
                                                                    <!-- Original columns -->
                                                                    <div class="original-columns row ">
                                                                        @for ($i = 0; $i < $selectedYear; $i++)
                                                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                            <div class="form-group">
                                                                                <label for="exampleFormControlInput1">Implementation
                                                                                    Period From </label>
                                                                                <input type="date" class="form-control" name="imp_from[]" id="imp_from" value="{{ isset($from[$i]) ? $from[$i] : '' }}" placeholder="Enter Benefit" max="9999-12-31" disabled>
                                                                            </div>
                                                                    </div>

                                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                        <div class="form-group">
                                                                            <label for="exampleFormControlInput1">Implementation
                                                                                Period To</label>
                                                                            <input type="date" class="form-control" name="imp_to[]" id="imp_to" value="{{ isset($to[$i]) ? $to[$i] : '' }}" placeholder="Enter Benefit" max="9999-12-31" disabled>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12 mb-2">
                                                                        <div class="form-group">
                                                                            <label for="exampleFormControlInput1">Implementation
                                                                                Plan Year Wise (Max 2500 Characters)</label>
                                                                            <textarea name="imp_plan[]" id="imp_plan" cols="2" rows="10" class="form-control" placeholder="Enter Implementation Plan Year Wise" disabled>{{ isset($service[$i]) ? $service[$i] : '' }}</textarea>
                                                                            <!-- <textarea name="imp_plan[]" id="imp_plan{{$i+1}}" cols="2" rows="10" class="form-control" placeholder="Enter Implementation Plan Year Wise">{{$service_details->imp_plan }}</textarea> -->
                                                                        </div>
                                                                    </div>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                    <div class="form-group">
                                                        <label for="">Type of Proposal</label>
                                                        @if (!empty($service_details->type_of_proposal))
    <input readonly type="text" class="form-control"
                                                                name="type_of_proposal" id="type_of_proposal"
                                                                value="{{ $service_details->type_of_proposal }}">
@else
    <input readonly type="text" class="form-control"
                                                                name="type_of_proposal" id="type_of_proposal" value="N/A">
    @endif

                                                    </div>
                                                </div> -->

                            <div class="row">



                                <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                    <label>AMC/WO/RC Proposed Start Date</label>
                                    @if (!empty($service_details->amc_proposal_sdate))
                                    <input readonly type="text" id="amc_proposal_sdate"
                                        value="{{ date('d-m-Y', strtotime($service_details->amc_proposal_sdate)) }}"
                                        name="amc_proposal_sdate" class="form-control">
                                    @else
                                    <input readonly type="text" id="amc_proposal_sdate" value="N/A"
                                        name="amc_proposal_sdate" class="form-control">
                                    @endif

                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                    <label>AMC/WO/RC Proposed End Date</label>
                                    @if (!empty($service_details->amc_proposal_edate))
                                    <input readonly type="text" id="amc_proposal_edate"
                                        value="{{ date('d-m-Y', strtotime($service_details->amc_proposal_edate)) }}"
                                        name="amc_proposal_edate" class="form-control">
                                    @else
                                    <input readonly type="text" id="amc_proposal_edate" value="N/A"
                                        name="amc_proposal_edate" class="form-control">
                                    @endif

                                </div>
                                @if($nv->budget_type == 'CAPEX')
                                <div class="col-12 mt-4">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">

                                            <h6 class="accordion-header" type="button" data-toggle="collapse"
                                                data-target="#collapseFour" aria-expanded="true"
                                                aria-controls="collapseFour">
                                                DERC Details </h6>

                                            <div id="collapseFour" class="collapse show" aria-labelledby="headingOne"
                                                data-parent="#accordionExample">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-xl-6 col-lg-6 col-md-6" style="margin-top:-20px;">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">DERC Proposal
                                                                    Number</label>
                                                                @if (!empty($service_details->prop_number))
                                                                <input readonly type="text"
                                                                    class="form-control" id="prop_number"
                                                                    name="prop_number"
                                                                    value="{{ $service_details != '' ? $service_details->prop_number : '' }}"
                                                                    placeholder="Enter Proposal Number" readonly>
                                                                @else
                                                                <input readonly type="text"
                                                                    class="form-control" id="prop_number"
                                                                    name="prop_number" value="N/A"
                                                                    placeholder="Enter Proposal Number" readonly>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12" style="margin-top:-20px;">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">DERC Reference
                                                                    No</label>
                                                                @if (!empty($service_details->derc_ref_no))
                                                                <input readonly type="text"
                                                                    class="form-control" id="derc_ref_no"
                                                                    name="derc_ref_no"
                                                                    value="{{ $service_details != '' ? $service_details->derc_ref_no : '' }}"
                                                                    placeholder="Enter DERC Reference No" readonly>
                                                                @else
                                                                <input readonly type="text"
                                                                    class="form-control" id="derc_ref_no"
                                                                    name="derc_ref_no" value="N/A"
                                                                    placeholder="Enter DERC Reference No" readonly>
                                                                @endif
                                                            </div>
                                                        </div>



                                                        <div
                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlSelect1">DERC Approval
                                                                    Status</label>
                                                                @if (!empty($service_details->derc_approval))
                                                                <input readonly type="text"
                                                                    class="form-control" id="derc_approval"
                                                                    name="derc_approval"
                                                                    value="{{ $service_details->derc_approval }}"
                                                                    placeholder="Enter DERC Reference No">
                                                                @else
                                                                <input readonly type="text"
                                                                    class="form-control" id="derc_ref_no"
                                                                    name="derc_ref_no" value="N/A"
                                                                    placeholder="Enter DERC Reference No">
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">DERC Approval
                                                                    Date</label>
                                                                @if (!empty($service_details->derc_app_date))
                                                                <input readonly type="date"
                                                                    class="form-control" name="derc_app_date"
                                                                    id="derc_app_date"
                                                                    value="{{ $service_details != '' ? $service_details->derc_app_date : '' }}">
                                                                @else
                                                                <input readonly type="text"
                                                                    class="form-control" name="derc_app_date"
                                                                    id="derc_app_date" value="N/A">
                                                                @endif
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @endif

                            </div>
                            <div class="col-12">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseF" aria-expanded="true" aria-controls="collapseF">
                                            Service BOQ</h6>
                                        <div id="collapseF" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <form enctype="multipart/form-data" method="post" id="serviceboqform">
                                                    <input type="hidden" class="form-control" name="nv_id" id="nv_id" value="{{ request()->segment(4) }}">
                                                    <div class="row  after-add-more-four">
                                                        @php
                                                        $s1 = explode(',', $service_details != '' ? $service_details->service_code : '');
                                                        $s2 = explode(',', $service_details != '' ? $service_details->ser_des : '');
                                                        $s3 = explode(',', $service_details != '' ? $service_details->ser_rate_ref : '');
                                                        $s4 = explode(',', $service_details != '' ? $service_details->ser_uom : '');
                                                        $s5 = explode(',', $service_details != '' ? $service_details->ser_rate : '');
                                                        $s6 = explode(',', $service_details != '' ? $service_details->ser_quantity : '');
                                                        $s7 = explode(',', $service_details != '' ? $service_details->ser_total_amount : '');

                                                        @endphp
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="card">
                                                                <div class="card-body table-responsive">
                                                                    <table id="nv_serviceBoq_datatable2" class="table table-bordered">
                                                                        <input type="hidden" class="form-control" name="nv_id" id="nv_id" value="{{ request()->segment(4) }}">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="text-center"
                                                                                    width="5%">
                                                                                    S.No
                                                                                </th>

                                                                                <th class="text-center"
                                                                                    width="5%">
                                                                                    NV
                                                                                    Id
                                                                                </th>
                                                                                <th>Service
                                                                                    Code
                                                                                </th>

                                                                                <th>Service
                                                                                    Description
                                                                                </th>
                                                                                <th
                                                                                    width="15%">
                                                                                    UOM
                                                                                </th>
                                                                                <th
                                                                                    width="10%">
                                                                                    Rate
                                                                                </th>
                                                                                <th
                                                                                    width="15%">
                                                                                    Quantity
                                                                                </th>
                                                                                <th
                                                                                    width="15%">
                                                                                    Amount
                                                                                    (Rs.)
                                                                                </th>


                                                                            </tr>
                                                                        </thead>





                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Total Amount For Services </label>
                                                                <input type="text"
                                                                    class="form-control"
                                                                    placeholder="Budget Available"
                                                                    name="" id="total_amt_service"
                                                                    value="{{ $service_details != '' ? indian_number_format($service_details->total_ser_amo) : ''}}"
                                                                    readonly>

                                                            </div>
                                                        </div>
                                                        @if($nv->budgetary_provision=="Additional" || $selectedYear == null)
                                                        <div class="col-xl-2 col-lg-2 col-md-2">
                                                            <label>Tax (In %)</label>
                                                            @if (!empty($service_details->tax5))
                                                            <input readonly type="text" class="form-control"
                                                                id="tax5" name="tax5"
                                                                value="{{ $service_details->tax5 }}">
                                                            @else
                                                            <input readonly type="text" class="form-control"
                                                                id="tax5" name="tax5"
                                                                value="N/A">
                                                            @endif
                                                        </div>
                                                        @endif
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    @if($nv->budgetary_provision=="Approved")
                                    @php
                                    $Imp_year = explode('-',$nv->fiscal_year);
                                    $Imp = $nv->fiscal_year;
                                    @endphp
                                    @if($selectedYear != null)
                                    <div class="col-12 mb-0">
                                        <div class="accordion" id="accordionExample">
                                            <div class="accordion-item">
                                                <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    Provisional Years Amount For Service BOQ
                                                </h6>

                                                <div id="collapseOne" class="collapse show" aria-labelledby="collapseOne" data-parent="#accordionExample">
                                                    <div class="card-body">
                                                        <div class="after-add-more">
                                                            @if($selectedYear == 1 || $selectedYear == 2 || $selectedYear == 3)

                                                            <div class="row">
                                                                <input type="hidden" id="year3" value="{{$Imp}}">
                                                                <div class="col-xl-3 col-lg-3 col-md-6">
                                                                    <input type="text" class="form-control" value="{{$Imp}}" readonly>
                                                                </div>

                                                                <div class="col-xl-3 col-lg-3 col-md-6">
                                                                    <input type="text" readonly class="form-control" placeholder="Enter Amount" name="total_matyear1" id="total_matyear1" value="{{indian_number_format($service_details->total_matyear1) ?? 0}}">
                                                                </div>

                                                                <div class="col-xl-3 col-lg-3 col-md-6">
                                                                    @if (!empty($service_details->tax_amount1))
                                                                    <input readonly type="text" class="form-control"
                                                                        id="tax_amount1" name="tax_amount1"
                                                                        value="{{ $service_details->tax_amount1 }}">
                                                                    @else
                                                                    <input readonly type="text" class="form-control"
                                                                        id="tax_amount1" name="tax_amount1"
                                                                        value="N/A">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            @endif
                                                            @if($selectedYear == 2 || $selectedYear == 3)
                                                            <br>

                                                            <div class="row">
                                                                <div class="col-xl-3 col-lg-3 col-md-6">
                                                                    <input type="text" class="form-control" value="{{$Imp_year[0]+1}}-{{$Imp_year[1]+1}}" readonly>
                                                                </div>

                                                                <div class="col-xl-3 col-lg-3 col-md-6">
                                                                    <input type="text" readonly class="form-control" placeholder="Enter Amount" name="total_matyear2" id="total_matyear2" value="{{indian_number_format($service_details->total_matyear2) ?? 0}}">
                                                                </div>

                                                                <div class="col-xl-3 col-lg-3 col-md-6">
                                                                    @if (!empty($service_details->tax_amount2))
                                                                    <input readonly type="text" class="form-control"
                                                                        id="tax_amount2" name="tax_amount2"
                                                                        value="{{ $service_details->tax_amount2 }}">
                                                                    @else
                                                                    <input readonly type="text" class="form-control"
                                                                        id="tax_amount2" name="tax_amount2"
                                                                        value="N/A">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            @endif
                                                            @if($selectedYear == 3)
                                                            <br>
                                                            <div class="row">
                                                                <div class="col-xl-3 col-lg-3 col-md-6">
                                                                    <input type="text" class="form-control" value="{{$Imp_year[0]+2}}-{{$Imp_year[1]+2}}" readonly>
                                                                </div>

                                                                <div class="col-xl-3 col-lg-3 col-md-6">
                                                                    <input type="text" readonly class="form-control" placeholder="Enter Amount" name="total_matyear3" id="total_matyear3" value="{{indian_number_format($service_details->total_matyear3) ?? 0}}">
                                                                </div>

                                                                <div class="col-xl-3 col-lg-3 col-md-6">
                                                                    @if (!empty($service_details->tax_amount3))
                                                                    <input readonly type="text" class="form-control"
                                                                        id="tax_amount3" name="tax_amount3"
                                                                        value="{{ $service_details->tax_amount3 }}">
                                                                    @else
                                                                    <input readonly type="text" class="form-control"
                                                                        id="tax_amount3" name="tax_amount3"
                                                                        value="N/A">
                                                                    @endif
                                                                </div>

                                                                @endif




                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @endif

                                        @endif
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <div class="card-body" style="padding: 0px">
                                                    <div class="row after-add-more-scheme-service">
                                                        @php
                                                        $service_amount = explode(',', $service_details != '' ? $service_details->service_amount : '0');
                                                        $service_description = explode(',', $service_details != '' ? $service_details->service_description : '');
                                                        $ser_tt = 0;
                                                        // $service_amt = 0;
                                                        @endphp
                                                        @foreach ($service_amount as $key => $service_amt)
                                                        @php
                                                        if(!empty($service_amt)){
                                                        $ser_tt += $service_amt;
                                                        } else{
                                                        $service_amt=0;
                                                        $ser_tt+= $service_amt;
                                                        }
                                                        @endphp
                                                        <input type="hidden" name="" value=" {{$ser_tt}}">
                                                        <div
                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group">

                                                                <input type="hidden" name="sum" id="sum" value="0">
                                                                <input type="number"
                                                                    class="form-control" id="service_amount" name="service_amount[]"
                                                                    placeholder="Amount" value="{{ $service_details != '' ? indian_number_format($service_amt) : '' }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group">
                                                                <input type="text"
                                                                    class="form-control"
                                                                    id="service_description"
                                                                    name="service_description[]"
                                                                    placeholder="Descripition" value="{{@$service_description[$key]}}" readonly>
                                                            </div>
                                                        </div>
                                                        @if ($key == 0)
                                                        <div
                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group change">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 my-class-scheme-service">
                                                        </div>
                                                        @else
                                                        <div
                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group ">
                                                                <label
                                                                    for="">&nbsp;</label>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @endforeach
                                                        {{-- @dd($ser_tt) --}}
                                                        <input type="hidden" name="" value=" {{$ser_tt}}" id="serv_amt">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="accordion" id="accordionExample">
                                                <div class="accordion-item">
                                                    <h6 class="accordion-header" type="button"
                                                        data-toggle="collapse" data-target="#collapsefour"
                                                        aria-expanded="true" aria-controls="collapsefour">
                                                        Rate Reference </h6>

                                                    <div id="collapsefour" class="collapse show"
                                                        aria-labelledby="collapsefour"
                                                        data-parent="#accordionExample">
                                                        <div class="card-body">
                                                            <div class="row" style="margin-top:-20px;">
                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleFormControlInput1">C&M
                                                                            Rate
                                                                            Reference :
                                                                            @if (!empty($service_doc->cm_rate_ref))
                                                                            <a class="ml-2"
                                                                                download="{{ $service_doc['cm_rate_ref'] }}"
                                                                                href="{{ url(asset('services-doc/' . $service_doc['cm_rate_ref'])) }}"><i
                                                                                    class="fa fa-download"
                                                                                    title="Download"></i>{{ $service_doc->cm_rate_ref }}</a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input readonly type="file"
                                                                            class="form-control"
                                                                            name="cm_rate_ref" id="cm_rate_ref">
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="exampleFormControlInput1">Vendor Quatation :
                                                                            @if (!empty($service_doc->vendor_quat))
                                                                            <a class="ml-2"
                                                                                download="{{ $service_doc['vendor_quat'] }}"
                                                                                href="{{ url(asset('services-doc/' . $service_doc['vendor_quat'])) }}"><i
                                                                                    class="fa fa-download"
                                                                                    title="Download"></i>{{ $service_doc->vendor_quat }}</a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input readonly type="file"
                                                                            class="form-control"
                                                                            name="vendor_quat"
                                                                            id="vendor_quat">
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleFormControlInput1">Last
                                                                            Purchase
                                                                            Price :
                                                                            @if (!empty($service_doc->last_purchase_price))
                                                                            <a class="ml-2"
                                                                                download="{{ $service_doc['last_purchase_price'] }}"
                                                                                href="{{ url(asset('services-doc/' . $service_doc['last_purchase_price'])) }}"><i
                                                                                    class="fa fa-download"
                                                                                    title="Download"></i>{{ $service_doc->last_purchase_price }}</a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input readonly type="file"
                                                                            class="form-control"
                                                                            name="last_purchase_price"
                                                                            id="last_purchase_price">
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleFormControlInput1">User
                                                                            Estimation :
                                                                            @if (!empty($service_doc->user_estimation))
                                                                            <a class="ml-2"
                                                                                download="{{ $service_doc['user_estimation'] }}"
                                                                                href="{{ url(asset('services-doc/' . $service_doc['user_estimation'])) }}"><i
                                                                                    class="fa fa-download"
                                                                                    title="Download"></i>{{ $service_doc->user_estimation }}</a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input readonly type="file"
                                                                            class="form-control"
                                                                            name="user_estimation"
                                                                            id="user_estimation">
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleFormControlInput1">Previous WO/RC (if any) :
                                                                            @if (!empty($service_doc->previous_wo_rc))
                                                                            <a class="ml-2"
                                                                                download="{{ $service_doc['previous_wo_rc'] }}"
                                                                                href="{{ url(asset('services-doc/' . $service_doc['previous_wo_rc'])) }}"><i
                                                                                    class="fa fa-download"
                                                                                    title="Download"></i>{{ $service_doc->previous_wo_rc }}</a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input readonly type="file"
                                                                            class="form-control"
                                                                            name="previous_wo_rc"
                                                                            id="previous_wo_rc">
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                <div class="form-group">
                                                    <label>Mode of Award(Services)</label>
                                                    @if (!empty($service_details->mode_of_award_of_service))
                                                    <input readonly type="text" class="form-control"
                                                        value="{{ $service_details->mode_of_award_of_service }}">
                                                    @else
                                                    <input readonly type="text" class="form-control" value="N/A">
                                                    @endif

                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                @if ($nv->budgetary_provision === 'Approved')
                                                <label>Budget Available</label>
                                                <br>
                                                @if (!empty($service_details->budget_available))
                                                <input readonly class="form-control" name="budget_available" type="text"
                                                    id="budget_available" placeholder="Budget Available"
                                                    value="{{ indian_number_format($service_details->budget_available) }}">
                                                @else
                                                <input readonly class="form-control" name="budget_available" type="text"
                                                    id="budget_available" placeholder="Budget Available" value="N/A">
                                                @endif
                                                @endif
                                            </div>

                                            <div class="col-xl-4 col-lg-4 col-md-4 mt-3">
                                                <label>Estimated amount of services </label>
                                                @if (!empty($service_details->estimate_amount_of_service))
                                                <input readonly type="text" class="form-control"
                                                    id="estimate_amount_of_service" name="estimate_amount_of_service"
                                                    value="{{ indian_number_format($service_details->estimate_amount_of_service) }}">
                                                @else
                                                <input readonly type="text" class="form-control"
                                                    id="estimate_amount_of_service" name="estimate_amount_of_service"
                                                    value="N/A">
                                                @endif
                                            </div>

                                            <div class="col-xl-2 col-lg-2 col-md-2 mt-3">
                                                <label>Tax (In %)</label>
                                                @if (!empty($service_details->tax1))
                                                <input readonly type="text" class="form-control"
                                                    id="tax1" name="tax1"
                                                    value="{{ $service_details->tax1 }}">
                                                @else
                                                <input readonly type="text" class="form-control"
                                                    id="tax1" name="tax1"
                                                    value="N/A">
                                                @endif
                                            </div>

                                            <div class="col-xl-4 col-lg-4 col-md-4 mt-3">
                                                <label>Estimated amount of services Civil </label>
                                                @if (!empty($service_details->estimate_amount_of_service_civil))
                                                <input readonly type="text" class="form-control"
                                                    id="estimate_amount_of_service_civil" placeholder="Enter In Lacs"
                                                    name="estimate_amount_of_service_civil"
                                                    value="{{ indian_number_format($service_details->estimate_amount_of_service_civil) }}">
                                                @else
                                                <input readonly type="text" class="form-control"
                                                    id="estimate_amount_of_service_civil" placeholder="Enter In Lacs"
                                                    name="estimate_amount_of_service_civil" value="N/A">
                                                @endif

                                            </div>

                                            <div class="col-xl-2 col-lg-2 col-md-2 mt-3">
                                                <label>Tax (In %)</label>
                                                @if (!empty($service_details->tax2))
                                                <input readonly type="text" class="form-control"
                                                    id="tax2" name="tax2"
                                                    value="{{ $service_details->tax2 }}">
                                                @else
                                                <input readonly type="text" class="form-control"
                                                    id="tax2" name="tax2"
                                                    value="N/A">
                                                @endif
                                            </div>

                                            <div class="col-xl-4 col-lg-4 col-md-4 mt-3">
                                                <label>Estimated amount of RR Charges</label>
                                                @if (!empty($service_details->estimate_amount_of_rr_chnage))
                                                <input readonly type="text" class="form-control"
                                                    id="estimate_amount_of_rr_chnage" placeholder="Enter In Lacs"
                                                    name="estimate_amount_of_rr_chnage"
                                                    value="{{ indian_number_format($service_details->estimate_amount_of_rr_chnage) }}">
                                                @else
                                                <input readonly type="text" class="form-control"
                                                    id="estimate_amount_of_rr_chnage" placeholder="Enter In Lacs"
                                                    name="estimate_amount_of_rr_chnage" value="N/A">
                                                @endif

                                            </div>

                                            <div class="col-xl-2 col-lg-2 col-md-2 mt-3">
                                                <label>Tax (In %)</label>
                                                @if (!empty($service_details->tax3))
                                                <input readonly type="text" class="form-control"
                                                    id="tax3" name="tax3"
                                                    value="{{ $service_details->tax3 }}">
                                                @else
                                                <input readonly type="text" class="form-control"
                                                    id="tax3" name="tax3"
                                                    value="N/A">
                                                @endif
                                            </div>

                                            <div class="col-xl-4 col-lg-4 col-md-4 mt-3">
                                                <label>Estimated amount - Other </label>
                                                @if (!empty($service_details->estimate_amount_other))
                                                <input readonly type="text" class="form-control"
                                                    id="estimate_amount_other" name="estimate_amount_other"
                                                    value="{{ indian_number_format($service_details->estimate_amount_other) }}">
                                                @else
                                                <input readonly type="text" class="form-control"
                                                    id="estimate_amount_other" name="estimate_amount_other"
                                                    value="N/A">
                                                @endif

                                            </div>

                                            <div class="col-xl-2 col-lg-2 col-md-2 mt-3">
                                                <label>Tax (In %)</label>
                                                @if (!empty($service_details->tax4))
                                                <input readonly type="text" class="form-control"
                                                    id="tax4" name="tax4"
                                                    value="{{ $service_details->tax4 }}">
                                                @else
                                                <input readonly type="text" class="form-control"
                                                    id="tax4" name="tax4"
                                                    value="N/A">
                                                @endif
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                <label> Cost Calculation for services:
                                                    @if (!empty($service_doc->cost_calculation_for_service))
                                                    <a class="ml-2"
                                                        download="{{ $service_doc->cost_calculation_for_service }}"
                                                        href="{{ url(asset('services-doc/' . $service_doc->cost_calculation_for_service)) }}"><i
                                                            class="fa fa-download"
                                                            title="Download"></i>{{ $service_doc->cost_calculation_for_service }}</a>
                                                    @else
                                                    <spam> N/A </spam>
                                                    @endif
                                                </label>
                                                <input type="file" readonly class="form-control"
                                                    id="cost_calculation_for_service" name="cost_calculation_for_service"
                                                    accept="" multiple>

                                            </div>

                                            {{-- <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                            <label>Total</label> 
                                            @if (!empty($service_details->total_buget))
                                                <input readonly type="text" class="form-control" id="total_buget"
                                                    name="total_buget" value="{{ $service_details->total_buget }}">
                                            @else
                                            <input readonly type="text" class="form-control" id="total_buget"
                                                name="total_buget" value="N/A">
                                            @endif

                                        </div>
                                        @if ($nv->budgetary_provision === 'Approved')
                                        <div class="col-xl-6 col-lg-6 col-md-6 mt-3">

                                            <label>Approved Budget</label>
                                            <br>
                                            @if (!empty($service_details->approved_budget))
                                            <input readonly class="form-control" name="approved_budget" type="text"
                                                id="approved_budget" value="{{ $service_details->approved_budget }}">
                                            @else
                                            <input readonly class="form-control" name="approved_budget" type="text"
                                                id="approved_budget" value="N/A">
                                            @endif

                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 mt-3">

                                            <label>Additional Budget</label>
                                            <br>
                                            @if (!empty($service_details->add_budget))
                                            <input readonly class="form-control" name="add_budget" type="text"
                                                id="add_budget" value="{{ $service_details->add_budget }}">
                                            @else
                                            <input readonly class="form-control" name="add_budget" type="text"
                                                id="add_budget" value="N/A">
                                            @endif

                                        </div>
                                        @endif --}}
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Cost Reduction plan/Future
                                                phasing out
                                                plan(if applicable) (Max 1000
                                                Characters)</label>
                                            @if (!empty($service_details->cause_analysis))
                                            <textarea readonly name="cause_analysis" id="cause_analysis" value=""
                                                class="form-control auto-resize-textarea" placeholder=" ">{{ $service_details['cause_analysis'] }}</textarea>
                                            @else
                                            <textarea readonly name="cause_analysis" id="cause_analysis" value=""
                                                class="form-control auto-resize-textarea" placeholder=" ">N/A</textarea>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mt-3" style="margin-top:-20px;">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1"
                                                title="Material Trail Details and Schudule of Pilot feedback Submission">Past Practice Followed (If Any)(Max 1000 Characters):
                                                @if (!empty($service_doc->past_practice))
                                                <a class="ml-2"
                                                    download="{{ $service_doc['past_practice'] }}"
                                                    href="{{ url(asset('services-doc/' . $service_doc['past_practice'])) }}"><i
                                                        class="fa fa-download"
                                                        title="Download"></i>{{ $service_doc->past_practice }}</a>
                                                @else
                                                <spam> N/A </spam>
                                                @endif
                                            </label>
                                            <input readonly type="file" class="form-control"
                                                name="past_practice" id="past_practice"
                                                placeholder="New Product">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <!-- <label for="exampleFormControlInput1">Special Remarks / Any Specific Recommendation (Max 500 Characters)</label> -->
                                            @if (!empty($service_details->past_practice_text))
                                            <textarea readonly name="past_practice_text" id="past_practice_text" value=""
                                                class="form-control auto-resize-textarea" placeholder="Special Remarks such as any specific recommendation">{{ $service_details['past_practice_text'] }}</textarea>
                                            @else
                                            <textarea readonly name="past_practice_text" id="past_practice_text" value=""
                                                class="form-control auto-resize-textarea" placeholder="Special Remarks such as any specific recommendation">N/A</textarea>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mt-3" style="margin-top:-20px;">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1"
                                                title="Material Trail Details and Schudule of Pilot feedback Submission">Special Remarks / Any Specific Recommendation (Max 500 Characters):
                                                @if (!empty($service_doc->special_attch))
                                                <a class="ml-2"
                                                    download="{{ $service_doc['special_attch'] }}"
                                                    href="{{ url(asset('services-doc/' . $service_doc['special_attch'])) }}"><i
                                                        class="fa fa-download"
                                                        title="Download"></i>{{ $service_doc->special_attch }}</a>
                                                @else
                                                <spam> N/A </spam>
                                                @endif
                                            </label>
                                            <input readonly type="file" class="form-control"
                                                name="special_attch" id="special_attch"
                                                placeholder="New Product">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <!-- <label for="exampleFormControlInput1">Special Remarks / Any Specific Recommendation (Max 500 Characters)</label> -->
                                            @if (!empty($service_details->special_remarks))
                                            <textarea readonly name="special_remarks" id="special_remarks" value=""
                                                class="form-control auto-resize-textarea" placeholder="Special Remarks such as any specific recommendation">{{ $service_details['special_remarks'] }}</textarea>
                                            @else
                                            <textarea readonly name="special_remarks" id="special_remarks" value=""
                                                class="form-control auto-resize-textarea" placeholder="Special Remarks such as any specific recommendation">N/A</textarea>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12 mt-4">


                                        <div class="accordion" id="accordionExample">
                                            <div class="accordion-item">
                                                <h6 class="accordion-header" type="button" data-toggle="collapse"
                                                    data-target="#collapseTwo" aria-expanded="true"
                                                    aria-controls="collapseTwo">
                                                    Attachments </h6>

                                                <div id="collapseTwo" class="collapse show" aria-labelledby="collapseTwo"
                                                    data-parent="#accordionExample">
                                                    <div class="card-body">
                                                        <div class="row">

                                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                                <label for="atachment">1. Copy of previous work
                                                                    order/purchase
                                                                    order : <br>


                                                                    @if (!empty($service_doc->copy_of_previous_work))
                                                                    <a class="ml-2"
                                                                        download="{{ $service_doc->copy_of_previous_work }}"
                                                                        href="{{ url(asset('services-doc/' . $service_doc->copy_of_previous_work)) }}"><i
                                                                            class="fa fa-download"
                                                                            title="Download"></i>{{ $service_doc->copy_of_previous_work }}</a>
                                                                    @else
                                                                    <spam> N/A </spam>
                                                                    @endif
                                                                </label>
                                                                <input type="file" readonly class="form-control"
                                                                    name="copy_of_previous_work"
                                                                    id="copy_of_previous_work">
                                                            </div>

                                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                                <label for="atachment">2. Copy of DERC/Other stakeholder
                                                                    approvals : <br>

                                                                    @if (!empty($service_doc->copy_of_derc_other))
                                                                    <a class="ml-2"
                                                                        download="{{ $service_doc->copy_of_derc_other }}"
                                                                        href="{{ url(asset('services-doc/' . $service_doc->copy_of_derc_other)) }}"><i
                                                                            class="fa fa-download"
                                                                            title="Download"></i>{{ $service_doc->copy_of_derc_other }}</a>
                                                                    @else
                                                                    <spam> N/A </spam>
                                                                    @endif
                                                                </label>
                                                                <input type="file" readonly class="form-control"
                                                                    id="copy_of_derc_other" name="copy_of_derc_other"
                                                                    placeholder="">
                                                            </div>

                                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                                <label for="atachment">3. Consumption Details Last 3 years
                                                                    : <br>


                                                                    @if (!empty($service_doc->consuption_details))
                                                                    <a class="ml-2"
                                                                        download="{{ $service_doc->consuption_details }}"
                                                                        href="{{ url(asset('services-doc/' . $service_doc->consuption_details)) }}"><i
                                                                            class="fa fa-download"
                                                                            title="Download"></i>{{ $service_doc->consuption_details }}</a>
                                                                    @else
                                                                    <spam> N/A </spam>
                                                                    @endif
                                                                </label>
                                                                <input type="file" readonly name="consuption_details"
                                                                    class="form-control" id="consuption_details"
                                                                    placeholder="Serviced">
                                                            </div>

                                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                                <label for="atachment">4. Budget statement for both OPEX/CAPEX activities : <br>


                                                                    @if (!empty($service_doc->buget_stmt_for_both))
                                                                    <a class="ml-2"
                                                                        download="{{ $service_doc->buget_stmt_for_both }}"
                                                                        href="{{ url(asset('services-doc/' . $service_doc->buget_stmt_for_both)) }}"><i
                                                                            class="fa fa-download"
                                                                            title="Download"></i>{{ $service_doc->buget_stmt_for_both }}</a>
                                                                    @else
                                                                    <spam> N/A </spam>
                                                                    @endif
                                                                </label>
                                                                <input type="file" readonly name="buget_stmt_for_both"
                                                                    class="form-control" id="buget_stmt_for_both">

                                                            </div>

                                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                                <label for="atachment">5. Photographs of product/PoC in
                                                                    case of new
                                                                    product trial : <br>



                                                                    @if (!empty($service_doc->photographs_of_product))
                                                                    <a class="ml-2"
                                                                        download="{{ $service_doc->photographs_of_product }}"
                                                                        href="{{ url(asset('services-doc/' . $service_doc->photographs_of_product)) }}"><i
                                                                            class="fa fa-download"
                                                                            title="Download"></i>{{ $service_doc->photographs_of_product }}</a>
                                                                    @else
                                                                    <spam> N/A </spam>
                                                                    @endif
                                                                </label>
                                                                <input type="file" readonly
                                                                    name="photographs_of_product" class="form-control"
                                                                    id="photographs_of_product" placeholder="">
                                                            </div>

                                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                                <label for="atachment">6. Material procurement -
                                                                    Details of Material,
                                                                    Code, Qty, Rate, Amount etc : <br>

                                                                    @if (!empty($service_doc->material_procurement))
                                                                    <a class="ml-2"
                                                                        download="{{ $service_doc->material_procurement }}"
                                                                        href="{{ url(asset('services-doc/' . $service_doc->material_procurement)) }}"><i
                                                                            class="fa fa-download"
                                                                            title="Download"></i>{{ $service_doc->material_procurement }}</a>
                                                                    @else
                                                                    <spam> N/A </spam>
                                                                    @endif
                                                                </label>
                                                                <input type="file" readonly class="form-control"
                                                                    name="material_procurement" id="material_procurement">
                                                            </div>



                                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                                <label for="atachment">7. Technical Specifications : <br>



                                                                    @if (!empty($service_doc->vendor_quatation))
                                                                    <a class="ml-2"
                                                                        download="{{ $service_doc->vendor_quatation }}"
                                                                        href="{{ url(asset('services-doc/' . $service_doc->vendor_quatation)) }}"><i
                                                                            class="fa fa-download"
                                                                            title="Download"></i>{{ $service_doc->vendor_quatation }}</a>
                                                                    @else
                                                                    <spam> N/A </spam>
                                                                    @endif
                                                                </label>
                                                                <input type="file" readonly name="vendor_quatation"
                                                                    class="form-control" id="vendor_quatation">
                                                            </div>
                                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                                <label for="atachment">8. Vendor Quatation : <br>


                                                                    @if (!empty($service_doc->vend_quatation))
                                                                    <a class="ml-2"
                                                                        download="{{ $service_doc->vend_quatation }}"
                                                                        href="{{ url(asset('services-doc/' . $service_doc->vend_quatation)) }}"><i
                                                                            class="fa fa-download"
                                                                            title="Download"></i>{{ $service_doc->vend_quatation }}</a>
                                                                    @else
                                                                    <spam> N/A </spam>
                                                                    @endif
                                                                </label>
                                                                <input type="file" readonly name="vend_quatation"
                                                                    class="form-control" id="vend_quatation" placeholder="">
                                                            </div>


                                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                                <label for="atachment">9. Others : <br>


                                                                    @if (!empty($service_doc) && is_object($service_doc) && !empty($service_doc->others))
                                                                    <a class="ml-2 download-all" href="#">
                                                                        <i class="fa fa-download" title="Download All"></i> Download All
                                                                    </a>
                                                                </label>
                                                                <input type="hidden" id="others_data" value="{{ $service_doc->others }}">
                                                                @else
                                                                <spam> N/A </spam>
                                                                @endif
                                                                </label>
                                                                <input type="file" readonly name="others"
                                                                    class="form-control" id="others" placeholder="">
                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                <label>Total</label>
                                                @if (!empty($service_details->total_buget))
                                                <input readonly type="text" class="form-control" id="total_buget"
                                                    name="total_buget" value="{{ indian_number_format($service_details->total_buget) }}">
                                                @else
                                                <input readonly type="text" class="form-control" id="total_buget"
                                                    name="total_buget" value="N/A">
                                                @endif

                                            </div>
                                            @if ($nv->budgetary_provision === 'Approved')
                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">

                                                <label>Approved Budget</label>
                                                <br>
                                                @if (!empty($service_details->approved_budget))
                                                <input readonly class="form-control" name="approved_budget" type="text"
                                                    id="approved_budget" value="{{ indian_number_format($service_details->approved_budget) }}">
                                                @else
                                                <input readonly class="form-control" name="approved_budget" type="text"
                                                    id="approved_budget" value="N/A">
                                                @endif

                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">

                                                <label>Additional Budget</label>
                                                <br>
                                                @if (!empty($service_details->add_budget))
                                                <input readonly class="form-control" name="add_budget" type="text"
                                                    id="add_budget" value="{{ indian_number_format($service_details->add_budget) }}">
                                                @else
                                                <input readonly class="form-control" name="add_budget" type="text"
                                                    id="add_budget" value="N/A">
                                                @endif

                                            </div>
                                            @endif
                                        </div> <br>
                                        @php
                                        $userId = Auth::id();
                                        $userDeptId = Auth::user()->department_id;
                                        $departmentName = getdepname(Auth::user()->department_id);
// dd($departmentName);
                                        $isCeoNomineeReviewer = false;
                                        if (str_contains($departmentName, 'CEO Nominee-2')) { 
                                        $isCeoNomineeReviewer = DB::table('capex_workflows_status')
                                        ->where('workflow_user_id', $userId)
                                        ->where('department_id', $userDeptId)
                                        ->where('service_id', $service_details->id)
                                        ->where('nv_budget_type', $nv->budget_type)
                                        ->where('reviewer_name', '!=', 'approver')
                                        ->exists();
                                        }
                                        // dd($isCeoNomineeReviewer);
                                        $checkValue = $service_details['check_ceonm2'] ?? null;
                                        @endphp

                                        <label for="exampleFormControlInput1" class="mt-3">Has approval from Budget Team been obtained on mail?</label>
                                        <br>

                                        <input type="checkbox" id="yes" name="check_ceonm2" value="1"
                                            {{ $checkValue == '1' ? 'checked' : '' }}
                                            {{ !$isCeoNomineeReviewer ? 'disabled' : '' }}>
                                        <label for="yes">Yes</label> &nbsp;&nbsp;&nbsp;&nbsp;

                                        <input type="checkbox" id="no" name="check_ceonm2" value="0"
                                            {{ $checkValue == '0' ? 'checked' : '' }}
                                            {{ !$isCeoNomineeReviewer ? 'disabled' : '' }}>
                                        <label for="no">No</label>
                                    </div>
                                </div>
                            </div>
                            @php
                            $checkValue = $transfer_to_nominee1 ?? null;

                            $userId = Auth::id();

                            // Fetch both OPEX and CAPEX workflows

                            $opex = DB::table('opex_workflows')->where('work_dep', 17)->first();
                            $capex = DB::table('workflows')->where('work_dep', 15)->first();

                            $isReviewer = false;

                            // Collect reviewers from both if they exist
                            $reviewers = [];

                            if ($opex) {
                            $reviewers = array_merge($reviewers, [
                            $opex->work_rew1 ?? null,
                            $opex->work_rew2 ?? null,
                            $opex->work_rew3 ?? null,
                            $opex->work_rew4 ?? null,
                            ]);
                            }


                            if ($capex) {
                            $reviewers = array_merge($reviewers, [
                            $capex->work_rew1 ?? null,
                            $capex->work_rew2 ?? null,
                            $capex->work_rew3 ?? null,
                            $capex->work_rew4 ?? null,
                            ]);
                            }

                            // Clean and normalize reviewer IDs
                            $reviewers = array_map('intval', array_filter($reviewers, fn($v) => $v !== null && trim((string)$v) !== ''));

                            // Check if logged-in user is in reviewer list
                            if (in_array((int)$userId, $reviewers, true)) {
                            $isReviewer = true;
                            }

                            @endphp

                            @if($isReviewer)
                            <div class="form-group mt-3">
                                <label class="form-label d-block">Is the NV for EHV Schemes/System Improvement (LT & 11 KV related)/Trial Order-New Product/New Vendor?</label>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input yes-no-checkbox"
                                        type="checkbox"
                                        name="transfer_to_nominee1"
                                        id="transfer_yes"
                                        value="0"
                                        {{ old('transfer_to_nominee1', $checkValue) == 0 ? 'checked' : '' }}
                                        data-group="yes-no-group">
                                    <label class="form-check-label" for="transfer_yes">Yes</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input yes-no-checkbox"
                                        type="checkbox"
                                        name="transfer_to_nominee1"
                                        id="transfer_no"
                                        value="1"
                                        {{ old('transfer_to_nominee1', $checkValue) == 1 ? 'checked' : '' }}
                                        data-group="yes-no-group">
                                    <label class="form-check-label" for="transfer_no">No</label>
                                </div>
                            </div>
                            @else

                            <div class="form-group mt-3">
                                <label class="form-label d-block">Is the NV for EHV Schemes/System Improvement (LT & 11 KV related)/Trial Order-New Product/New Vendor?</label>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input yes-no-checkbox"
                                        type="checkbox"
                                        id="transfer_yes_disabled"
                                        value="0"
                                        {{ $checkValue == 0 ? 'checked' : '' }}
                                        disabled
                                        data-group="yes-no-group">
                                    <label class="form-check-label" for="transfer_yes_disabled">Yes</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input yes-no-checkbox"
                                        type="checkbox"
                                        id="transfer_no_disabled"
                                        value="1"
                                        {{ $checkValue == 1 ? 'checked' : '' }}
                                        disabled
                                        data-group="yes-no-group">
                                    <label class="form-check-label" for="transfer_no_disabled">No</label>
                                </div>

                                {{-- ✅ Hidden input to preserve value for DB update 
                                <input type="hidden" name="transfer_to_nominee1" value="{{ $checkValue }}"> --}}
                            </div>
                            @endif

                            {{-- Add this script to make checkboxes behave like radio buttons --}}

                            @if (in_array(Auth::user()->role_id, [11, 9]))
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    @if (Auth::user()->role_id != 9)

                                    <label for="exampleFormControlInput1">Enter Approval & Rejection Remarks (Max 200 Characters)
                                        <span class="mandatory_input">*</span></label>
                                    @endif

                                    @if (!empty($rv1) && $rv1 == $user->id)
                                    <textarea name="remark" id="remark" cols="2" rows="5" class="form-control"
                                        placeholder=" Enter Remark Description">{{ $Nvsericestatus->rv1_remark }}</textarea>

                                    <div class="col-6 mt-2 p-2">
                                        <label for="exampleFormControlInput1">Upload Attachment for Approval & Rejection Remarks (if Any) </label>
                                        <input type="file" class="form-control" id="approval_attachements" name="approval_attachements">
                                    </div>

                                    <div class="row mt-2 p-2 signature">
                                        @if($Nvsericestatus->rv1_status == 1||$Nvsericestatus->rv1_status == 2)
                                        @if($user->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer1 <h6 id="signature">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer1 <h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer1 <h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer1 <h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==5)
                                        <div class="col-4 mb-2 ">
                                            <div class="card status-center" id="sign">
                                                Reviewer1<img src="{{asset('/images/'.$user->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2 status-center">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer1)
                                            @if (!empty($Nvsericestatus->rv1_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv1_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv1_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                    </div>

                                    @endif
                                    @if(!empty($rv2) && $rv2 == $user->id)
                                    <textarea name="remark" id="remark" cols="2" rows="5" class="form-control"
                                        placeholder=" Enter Remark Description">{{$Nvsericestatus->rv2_remark}}</textarea>

                                    <div class="col-6 mt-2 p-2">
                                        <label for="exampleFormControlInput1">Upload Attachment for Approval & Rejection Remarks (if Any) </label>
                                        <input type="file" class="form-control" id="approval_attachements" name="approval_attachements">
                                    </div>
                                    @php
                                    $user_rv1= App\Models\User::where("id",$Nvsericestatus->rv1_id)->first();
                                    @endphp

                                    <div class="row mt-2 p-2 signature">
                                        @if($Nvsericestatus->rv1_status == 1||$Nvsericestatus->rv1_status == 2)
                                        @if($user_rv1->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer1 <h6 id="signature">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer1 <h6 id="signature1">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer1 <h6 id="signature1">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer1 <h6 id="signature1">{{$user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer1<img src="{{asset('/images/'.$user_rv1->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer1)
                                            @if (!empty($Nvsericestatus->rv1_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv1_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv1_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv2_status == 1||$Nvsericestatus->rv2_status == 2)
                                        @if($user->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer2 <h6 id="signature">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer2 <h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer2 <h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer2 <h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer2<img src="{{asset('/images/'.$user->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer2)
                                            @if (!empty($Nvsericestatus->rv2_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv2_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv2_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif

                                    </div>

                                    @endif

                                    @if(!empty($rv3) && $rv3 == $user->id)
                                    <textarea name="remark" id="remark" cols="2" rows="5" class="form-control"
                                        placeholder=" Enter Remark Description">{{$Nvsericestatus->rv3_remark}}</textarea>

                                    <div class="col-6 mt-2 p-2">
                                        <label for="exampleFormControlInput1">Upload Attachment for Approval & Rejection Remarks (if Any) </label>
                                        <input type="file" class="form-control" id="approval_attachements" name="approval_attachements">
                                    </div>

                                    @php
                                    $user_rv1= App\Models\User::where("id",$Nvsericestatus->rv1_id)->first();
                                    $user_rv2= App\Models\User::where("id",$Nvsericestatus->rv2_id)->first();
                                    @endphp
                                    <div class="row mt-2 p-2 signature">
                                        @if($Nvsericestatus->rv1_status == 1||$Nvsericestatus->rv1_status == 2)
                                        @if($user_rv1->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer1 <h6 id="signature">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer1 <h6 id="signature1">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer1 <h6 id="signature1">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer1 <h6 id="signature1">{{$user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer1<img src="{{asset('/images/'.$user_rv1->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer1)
                                            @if (!empty($Nvsericestatus->rv1_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv1_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv1_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv2_status == 1||$Nvsericestatus->rv2_status == 2)
                                        @if($user_rv2->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer2 <h6 id="signature">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer2 <h6 id="signature1">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer2 <h6 id="signature1">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer2 <h6 id="signature1">{{$user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer2<img src="{{asset('/images/'.$user_rv2->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer2)
                                            @if (!empty($Nvsericestatus->rv2_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv2_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv2_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv3_status == 1||$Nvsericestatus->rv3_status == 2)

                                        @if($user->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer3 <h6 id="signature">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer3 <h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer3 <h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer3 <h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==5)
                                        <div class="col mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer3<img src="{{asset('/images/'.$user->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                            </div>
                                        </div>

                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer3)
                                            @if (!empty($Nvsericestatus->rv3_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv3_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv3_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif

                                    </div>
                                    @endif
                                    @if(!empty($rv4) && $rv4 == $user->id)
                                    <textarea name="remark" id="remark" cols="2" rows="5" class="form-control"
                                        placeholder=" Enter Remark Description">{{$Nvsericestatus->rv4_remark}}</textarea>

                                    <div class="col-6 mt-2 p-2">
                                        <label for="exampleFormControlInput1">Upload Attachment for Approval & Rejection Remarks (if Any) </label>
                                        <input type="file" class="form-control" id="approval_attachements" name="approval_attachements">
                                    </div>

                                    @php
                                    $user_rv1= App\Models\User::where("id",$Nvsericestatus->rv1_id)->first();
                                    $user_rv2= App\Models\User::where("id",$Nvsericestatus->rv2_id)->first();
                                    $user_rv3= App\Models\User::where("id",$Nvsericestatus->rv3_id)->first();
                                    @endphp

                                    <div class="row mt-2 p-2 signature">
                                        @if($Nvsericestatus->rv1_status == 1||$Nvsericestatus->rv1_status == 2)
                                        @if($user_rv1->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer1 <h6 id="signature">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer1 <h6 id="signature1">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer1 <h6 id="signature1">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer1 <h6 id="signature1">{{$user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer1<img src="{{asset('/images/'.$user_rv1->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer1)
                                            @if (!empty($Nvsericestatus->rv1_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv1_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv1_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv2_status == 1||$Nvsericestatus->rv2_status == 2)
                                        @if($user_rv2->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer2 <h6 id="signature">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer2 <h6 id="signature1">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer2 <h6 id="signature1">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer2 <h6 id="signature1">{{$user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer2<img src="{{asset('/images/'.$user_rv2->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                            </div>
                                        </div>

                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer2)
                                            @if (!empty($Nvsericestatus->rv2_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv2_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv2_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv3_status == 1||$Nvsericestatus->rv3_status == 2)

                                        @if($user_rv3->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer3 <h6 id="signature">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>

                                        @endif
                                        @if($user_rv3->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer3 <h6 id="signature1">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>

                                        @endif
                                        @if($user_rv3->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer3 <h6 id="signature1">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv3->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer3 <h6 id="signature1">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv3->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer3<img src="{{asset('/images/'.$user_rv3->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer3)
                                            @if (!empty($Nvsericestatus->rv3_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv3_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv3_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv4_status == 1||$Nvsericestatus->rv4_status == 2)

                                        @if($user->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer4 <h6 id="signature">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer4 <h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer4 <h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer4 <h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==5)
                                        <div class="col mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer4<img src="{{asset('/images/'.$user->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer4)
                                            @if (!empty($Nvsericestatus->rv4_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv4_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv4_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif

                                    </div>
                                    @endif
                                    @if(!empty($hod) && $hod == $user->id)
                                    <textarea name="remark" id="remark" cols="2" rows="5" class="form-control"
                                        placeholder=" Enter Remark Description">{{$Nvsericestatus->hod_remark}}</textarea>

                                    <div class="col-6 mt-2 p-2">

                                        <label for="exampleFormControlInput1">Upload Attachment for Approval & Rejection Remarks (if Any) </label>
                                        <input type="file" class="form-control" id="approval_attachements" name="approval_attachements">
                                    </div>

                                    @php
                                    $user_rv1= App\Models\User::where("id",$Nvsericestatus->rv1_id)->first();
                                    $user_rv2= App\Models\User::where("id",$Nvsericestatus->rv2_id)->first();
                                    $user_rv3= App\Models\User::where("id",$Nvsericestatus->rv3_id)->first();
                                    $user_rv4= App\Models\User::where("id",$Nvsericestatus->rv4_id)->first();
                                    @endphp
                                    <div class="row mt-2 p-2 signature">
                                        @if($Nvsericestatus->rv1_status == 1||$Nvsericestatus->rv1_status == 2)

                                        @if($user_rv1->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer1 <h6 id="signature">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer1 <h6 id="signature1">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer1 <h6 id="signature1">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer1 <h6 id="signature1">{{$user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer1<img src="{{asset('/images/'.$user_rv1->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer1)
                                            @if (!empty($Nvsericestatus->rv1_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv1_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv1_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv2_status == 1||$Nvsericestatus->rv2_status == 2)
                                        @if($user_rv2->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer2 <h6 id="signature">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer2 <h6 id="signature1">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer2 <h6 id="signature1">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer2 <h6 id="signature1">{{$user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer2<img src="{{asset('/images/'.$user_rv2->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                            </div>
                                        </div>

                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer2)
                                            @if (!empty($Nvsericestatus->rv2_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv2_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv2_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv3_status == 1||$Nvsericestatus->rv3_status == 2)

                                        @if($user_rv3->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer3 <h6 id="signature">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>

                                        @endif
                                        @if($user_rv3->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer3 <h6 id="signature1">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>

                                        @endif
                                        @if($user_rv3->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer3 <h6 id="signature1">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv3->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer3 <h6 id="signature1">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv3->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer3<img src="{{asset('/images/'.$user_rv3->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer3)
                                            @if (!empty($Nvsericestatus->rv3_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv3_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv3_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv4_status == 1||$Nvsericestatus->rv4_status == 2)

                                        @if($user_rv4->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer4 <h6 id="signature">{{ $user_rv4->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv4->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer4 <h6 id="signature1">{{ $user_rv4->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv4->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer4 <h6 id="signature1">{{ $user_rv4->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv4->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer4 <h6 id="signature1">{{ $user_rv4->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv4->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer4<img src="{{asset('/images/'.$user_rv4->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer4)
                                            @if (!empty($Nvsericestatus->rv4_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv4_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv4_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->hod_status == 1||$Nvsericestatus->hod_status == 2)

                                        @if($user->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            HOD <h6 id="signature">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            HOD<h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            HOD <h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            HOD<h6 id="signature1">{{ \Auth::user()->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                        </div>
                                        @endif
                                        @if($user->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                HOD<img src="{{asset('/images/'.$user->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(HOD)
                                            @if (!empty($Nvsericestatus->hod_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->hod_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->hod_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif

                                    </div>
                                    @endif


                                    @php
                                    $signatures = null;
                                    $signatures = DB::table('capex_workflows_status')
                                    ->where('service_id', $service_details->id)->whereIn('nv_stage_status',[1,2])
                                    ->where('nv_budget_type', $nv->budget_type)
                                    ->where('nv_stage_remark', '!=', null)
                                    ->get();
                                    $currentUserRemark = DB::table('capex_workflows_status')
                                    ->where('service_id', $service_details->id)->where('workflow_user_id',$user->id)
                                    ->first();

                                    $stages_workflows = DB::table('capex_workflows_status')->where('service_id', $service_details->id)->get();
                                    @endphp

                                    @if($stages_workflows->isNotEmpty())
                                    @foreach ($stages_workflows as $stageworkflow)
                                    @if (!empty($stageworkflow->workflow_user_id) && $stageworkflow->workflow_user_id == $user->id)
                                    <textarea name="remark" id="remark" cols="2" rows="5" class="form-control"
                                        placeholder=" Enter Remark Description">{{$stageworkflow->nv_stage_remark ?? ' '}}</textarea>

                                    <div class="col-6 mt-2 p-2">
                                        <label for="exampleFormControlInput1">Upload Attachment for Approval & Rejection Remarks (if Any) </label>
                                        <input type="file" class="form-control" id="approval_attachements" name="approval_attachements">
                                    </div>
                                    @php
                                    $user_rv1= App\Models\User::where("id",$Nvsericestatus->rv1_id)->first();
                                    $user_rv2= App\Models\User::where("id",$Nvsericestatus->rv2_id)->first();
                                    $user_rv3= App\Models\User::where("id",$Nvsericestatus->rv3_id)->first();
                                    $user_rv4= App\Models\User::where("id",$Nvsericestatus->rv4_id)->first();
                                    $user_hod= App\Models\User::where("id",$Nvsericestatus->hod_id)->first();
                                    $user_groupcio= App\Models\User::where("id",$Nvsericestatus->groupcio_id)->first();
                                    @endphp
                                    <div class="row mt-2 p-2 signature">
                                        @if($Nvsericestatus->rv1_status == 1||$Nvsericestatus->rv1_status == 2)

                                        @if($user_rv1->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer1 <h6 id="signature">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer1 <h6 id="signature1">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer1 <h6 id="signature1">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer1 <h6 id="signature1">{{$user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer1<img src="{{asset('/images/'.$user_rv1->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer1)
                                            @if (!empty($Nvsericestatus->rv1_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv1_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv1_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv2_status == 1||$Nvsericestatus->rv2_status == 2)
                                        @if($user_rv2->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer2 <h6 id="signature">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer2 <h6 id="signature1">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer2 <h6 id="signature1">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer2 <h6 id="signature1">{{$user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer2<img src="{{asset('/images/'.$user_rv2->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                            </div>
                                        </div>

                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer2)
                                            @if (!empty($Nvsericestatus->rv2_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv2_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv2_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv3_status == 1||$Nvsericestatus->rv3_status == 2)

                                        @if($user_rv3->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer3 <h6 id="signature">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>

                                        @endif
                                        @if($user_rv3->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer3 <h6 id="signature1">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>

                                        @endif
                                        @if($user_rv3->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer3 <h6 id="signature1">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv3->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer3 <h6 id="signature1">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv3->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer3<img src="{{asset('/images/'.$user_rv3->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer3)
                                            @if (!empty($Nvsericestatus->rv3_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv3_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv3_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv4_status == 1||$Nvsericestatus->rv4_status == 2)

                                        @if($user_rv4->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer4 <h6 id="signature">{{ $user_rv4->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv4->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer4 <h6 id="signature1">{{ $user_rv4->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv4->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer4 <h6 id="signature1">{{ $user_rv4->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv4->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer4 <h6 id="signature1">{{ $user_rv4->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv4->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer4<img src="{{asset('/images/'.$user_rv4->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer4)
                                            @if (!empty($Nvsericestatus->rv4_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv4_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv4_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->hod_status == 1||$Nvsericestatus->hod_status == 2 )

                                        @if($user_hod->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            HOD <h6 id="signature">{{ $user_hod->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                        </div>
                                        @endif
                                        @if($user_hod->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            HOD<h6 id="signature1">{{ $user_hod->name?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                        </div>
                                        @endif
                                        @if($user_hod->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            HOD <h6 id="signature1">{{$user_hod->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                        </div>
                                        @endif
                                        @if($user_hod->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            HOD<h6 id="signature1">{{ $user_hod->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                        </div>
                                        @endif
                                        @if($user_hod->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                HOD<img src="{{asset('/images/'.$user_hod->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(HOD)
                                            @if (!empty($Nvsericestatus->hod_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->hod_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->hod_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif


                                        @if( $Nvsericestatus->groupcio_status == 1||$Nvsericestatus->groupcio_status == 2 )
                                        @if($user_groupcio->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Group Head<h6 id="signature">{{ $user_groupcio->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Group Head) <p>{{ $Nvsericestatus->groupcio_remark }}</p>
                                        </div>
                                        @endif
                                        @if($user_groupcio->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Group Head<h6 id="signature1">{{$user_groupcio->name?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Group Head) <p>{{ $Nvsericestatus->groupcio_remark }}</p>
                                        </div>
                                        @endif
                                        @if($user_groupcio->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Group Head<h6 id="signature1">{{$user_groupcio->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Group Head) <p>{{ $Nvsericestatus->groupcio_remark }}</p>
                                        </div>
                                        @endif
                                        @if($user_groupcio->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Group Head<h6 id="signature1">{{$user_groupcio->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Group Head) <p>{{ $Nvsericestatus->groupcio_remark }}</p>
                                        </div>
                                        @endif
                                        @if($user_groupcio->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Group Head<img src="{{asset('/images/'.$user_groupcio->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Group Head) <p>{{ $Nvsericestatus->groupcio_remark }}</p>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Group Head)
                                            @if (!empty($Nvsericestatus->groupcio_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->groupcio_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->groupcio_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif

                                        @if(!empty($signatures))
                                        @foreach($signatures as $signature)
                                        @php
                                        $stage_user_name = App\Models\User::where("id",$signature->workflow_user_id)->first();
                                        $reviewerLabels = [
                                        'work_rew1' => 'Reviewer1',
                                        'work_rew2' => 'Reviewer2',
                                        'work_rew3' => 'Reviewer3',
                                        'work_rew4' => 'Reviewer4',
                                        'approver' => 'Approver'
                                        ];
                                        $role = $reviewerLabels[$signature->reviewer_name] ?? '';

                                        $departmentName = \App\Models\Department::where('id', $signature->department_id)->value('name');
                                        $roleWithDepartment = $role . ' (' . $departmentName . ')';
                                        @endphp
                                        @if(in_array($signature->nv_stage_signature, [1,2,3,4]))
                                        <div class="col-4 mb-2 {{ ['first', 'second', 'third', 'fourth'][$signature->nv_stage_signature - 1] }}">
                                            {{ $roleWithDepartment }}
                                            <h6 id="signature{{ $signature->nv_stage_signature > 1 ? $signature->nv_stage_signature : '' }}">
                                                {{ $stage_user_name->name ?? '' }}
                                            </h6>
                                        </div>
                                        @elseif($signature->nv_stage_signature == 5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                {{ $roleWithDepartment }}
                                                <img src="{{ asset('/images/'.$stage_user_name->image) }}" id="output" />
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            <div class="status-center">
                                                Remark ({{ $roleWithDepartment }})
                                                <p>{{ $signature->nv_stage_remark }}</p>
                                            </div>
                                        </div>

                                        <div class="col-4 mb-2">
                                            Attachment ({{ $roleWithDepartment }})
                                            @if (!empty($signature->nv_stage_attachement))
                                            <a class="ml-2" download="{{ $signature->nv_stage_attachement }}"
                                                href="{{ url(asset('approval-remark/' . $signature->nv_stage_attachement)) }}">
                                                <br>Click Here</a>
                                            @endif
                                        </div>


                                        @endforeach
                                        @endif
                                    </div>
                                    @endif
                                    @endforeach
                                    @endif


                                    @if(!empty($group_cio) && $group_cio == $user->id)
                                    <textarea name="remark" id="remark" cols="2" rows="5" class="form-control"
                                        placeholder=" Enter Remark Description">{{$Nvsericestatus->groupcio_remark}}</textarea>

                                    <div class="col-6 mt-2 p-2">
                                        <label for="exampleFormControlInput1">Upload Attachment for Approval & Rejection Remarks (if Any) </label>
                                        <input type="file" class="form-control" id="approval_attachements" name="approval_attachements">
                                    </div>

                                    @php
                                    $user_rv1= App\Models\User::where("id",$Nvsericestatus->rv1_id)->first();
                                    $user_rv2= App\Models\User::where("id",$Nvsericestatus->rv2_id)->first();
                                    $user_rv3= App\Models\User::where("id",$Nvsericestatus->rv3_id)->first();
                                    $user_rv4= App\Models\User::where("id",$Nvsericestatus->rv4_id)->first();
                                    $user_hod= App\Models\User::where("id",$Nvsericestatus->hod_id)->first();

                                    @endphp
                                    <div class="row mt-2 p-2 signature">
                                        @if($Nvsericestatus->rv1_status == 1||$Nvsericestatus->rv1_status == 2)

                                        @if($user_rv1->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer1 <h6 id="signature">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer1 <h6 id="signature1">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer1 <h6 id="signature1">{{ $user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer1 <h6 id="signature1">{{$user_rv1->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv1->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer1<img src="{{asset('/images/'.$user_rv1->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer1)<p>{{ $Nvsericestatus->rv1_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer1)
                                            @if (!empty($Nvsericestatus->rv1_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv1_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv1_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv2_status == 1||$Nvsericestatus->rv2_status == 2)
                                        @if($user_rv2->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer2 <h6 id="signature">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer2 <h6 id="signature1">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer2 <h6 id="signature1">{{ $user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer2 <h6 id="signature1">{{$user_rv2->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv2->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer2<img src="{{asset('/images/'.$user_rv2->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer2)<p>{{ $Nvsericestatus->rv2_remark }}
                                            </div>
                                        </div>

                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer2)
                                            @if (!empty($Nvsericestatus->rv2_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv2_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv2_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv3_status == 1||$Nvsericestatus->rv3_status == 2)

                                        @if($user_rv3->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer3 <h6 id="signature">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>

                                        @endif
                                        @if($user_rv3->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer3 <h6 id="signature1">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>

                                        @endif
                                        @if($user_rv3->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer3 <h6 id="signature1">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv3->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer3 <h6 id="signature1">{{ $user_rv3->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv3->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer3<img src="{{asset('/images/'.$user_rv3->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer3)<p>{{ $Nvsericestatus->rv3_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer3)
                                            @if (!empty($Nvsericestatus->rv3_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv3_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv3_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->rv4_status == 1||$Nvsericestatus->rv4_status == 2)

                                        @if($user_rv4->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Reviewer4 <h6 id="signature">{{ $user_rv4->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv4->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Reviewer4 <h6 id="signature1">{{ $user_rv4->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv4->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Reviewer4 <h6 id="signature1">{{ $user_rv4->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv4->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Reviewer4 <h6 id="signature1">{{ $user_rv4->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                        </div>
                                        @endif
                                        @if($user_rv4->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Reviewer4<img src="{{asset('/images/'.$user_rv4->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Reviewer4)<p>{{ $Nvsericestatus->rv4_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Reviewer4)
                                            @if (!empty($Nvsericestatus->rv4_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->rv4_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->rv4_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif
                                        @if($Nvsericestatus->hod_status == 1||$Nvsericestatus->hod_status == 2 )

                                        @if($user_hod->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            HOD <h6 id="signature">{{ $user_hod->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                        </div>
                                        @endif
                                        @if($user_hod->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            HOD<h6 id="signature1">{{ $user_hod->name?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                        </div>
                                        @endif
                                        @if($user_hod->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            HOD <h6 id="signature1">{{$user_hod->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                        </div>
                                        @endif
                                        @if($user_hod->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            HOD<h6 id="signature1">{{ $user_hod->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                        </div>
                                        @endif
                                        @if($user_hod->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                HOD<img src="{{asset('/images/'.$user_hod->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(HOD)<p>{{ $Nvsericestatus->hod_remark }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(HOD)
                                            @if (!empty($Nvsericestatus->hod_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->hod_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->hod_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif


                                        @if( $Nvsericestatus->groupcio_status == 1||$Nvsericestatus->groupcio_status == 2 )
                                        @if($user->signature_id==1)
                                        <div class="col-4 mb-2 first">
                                            Group Head<h6 id="signature">{{ $user->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 first">
                                            Remark(Group Head) <p>{{ $Nvsericestatus->groupcio_remark }}</p>
                                        </div>
                                        @endif
                                        @if($user->signature_id==2)
                                        <div class="col-4 mb-2 second">
                                            Group Head<h6 id="signature1">{{$user->name?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 second">
                                            Remark(Group Head) <p>{{ $Nvsericestatus->groupcio_remark }}</p>
                                        </div>
                                        @endif
                                        @if($user->signature_id==3)
                                        <div class="col-4 mb-2 third">
                                            Group Head<h6 id="signature1">{{$user->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 third">
                                            Remark(Group Head) <p>{{ $Nvsericestatus->groupcio_remark }}</p>
                                        </div>
                                        @endif
                                        @if($user->signature_id==4)
                                        <div class="col-4 mb-2 fourth">
                                            Group Head<h6 id="signature1">{{$user->name ?? '' }}</h6>
                                        </div>
                                        <div class="col-4 mb-2 fourth">
                                            Remark(Group Head) <p>{{ $Nvsericestatus->groupcio_remark }}</p>
                                        </div>
                                        @endif
                                        @if($user->signature_id==5)
                                        <div class="col-4 mb-2">
                                            <div class="card status-center" id="sign">
                                                Group Head<img src="{{asset('/images/'.$user->image)}}" id="output" />
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class=" status-center" id="sign">
                                                Remark(Group Head) <p>{{ $Nvsericestatus->groupcio_remark }}</p>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-4 mb-2">
                                            Attachment(Group Head)
                                            @if (!empty($Nvsericestatus->groupcio_attachement))
                                            <a class="ml-2" download="{{ $Nvsericestatus->groupcio_attachement}}" href="{{ url(asset('approval-remark/' . $Nvsericestatus->groupcio_attachement)) }}">
                                                <br>Click Here</a>

                                            @endif
                                        </div>
                                        @endif

                                    </div>
                                    @endif

                                    @php
                                    foreach($clarificationsrecevier as $recevier){
                                    $recevier=$recevier->receiver_user_id;
                                    }
                                    @endphp
                                    @if(!empty($recevier))


                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-outline card-info">
                                                <div class="card-body" style="height:200px;overflow:scroll">
                                                    <table class="table table-bordered">
                                                        <thead class="text-center">
                                                            <tr role="row">
                                                                <th class="text-center sorting_disabled" aria-label="S.No" style="width:20px !important">S.No</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" aria-label="Proposal Number: activate to sort column ascending" style="width:60px !important">Sender</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" aria-label="Budget Type: activate to sort column ascending" style="width:60px !important">Receiver</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" aria-label="Initiated By: activate to sort column ascending">Clarifiation Remarks</th>
                                                                <!-- <th  class="text-center sorting" tabindex="0" aria-controls="nv_datatable"  aria-label="Initiated Date: activate to sort column ascending">Date and Time</th> -->
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" aria-label="Initiated Date: activate to sort column ascending">Action/ Reply Message</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody class="clarification-log">
                                                            @php $count = 1; @endphp
                                                            @foreach ($clarificationsrecevier as $recevier)
                                                            <tr>
                                                                <td style="width:20px !important">{{ $count++ }}</td>
                                                                <td class="text-center" style="width:60px !important">{{ $recevier->users->name }}</td>
                                                                <td class="text-center" style="width:60px !important"> {{ $recevier->user_name }}</td>

                                                                <td class="text-center">
                                                                    @if(!empty($recevier->attachment))
                                                                    <a class="ml-2" download="{{ $recevier->attachment}}" href="{{ url(asset('clarification-file/' . $recevier->attachment)) }}">{{$recevier->attachment}}</a>
                                                                    <br> @endif
                                                                    @if(!empty($recevier->clarification_remark))
                                                                    {{date('d-M-Y h:i:s A', strtotime($recevier->created_at))}}<br>
                                                                    <span class="clarification-cell text-center" data-full-text="{!! $recevier->clarification_remark !!}">

                                                                        @if(strlen($recevier->clarification_remark) <= 50)
                                                                            {!! $recevier->clarification_remark !!}
                                                                            @else
                                                                            {!! substr($recevier->clarification_remark, 0, 50) !!}
                                                                            <span class="read-more" style="cursor: pointer; color: blue;">...Show More</span>

                                                                            @endif
                                                                    </span>

                                                                    @endif
                                                                </td>


                                                                <td class="text-center">
                                                                    @if($recevier->is_replied==0)
                                                                    <button type="button"
                                                                        class="btn btn-info btn-sm clarification-reply"
                                                                        data-toggle="modal"
                                                                        data-target="#clarificationReplyModal"
                                                                        data-name="{{ $recevier->users->name }}"
                                                                        data-email="{{ $clarificationssenderemailid }}"
                                                                        data-userid="{{ $recevier->user_id }}"
                                                                        data-clarificationid="{{ $recevier->id }}">Reply</button>
                                                                    @else
                                                                    @if(!empty($recevier->reply_attachment))
                                                                    <a class="ml-2" download="{{ $recevier->reply_attachment}}" href="{{ url(asset('clarification-file/' . $recevier->reply_attachment)) }}">{{$recevier->reply_attachment}}</a>
                                                                    <br> @endif
                                                                    {{date('d-M-Y h:i:s A', strtotime($recevier->reply_timestamp))}}<br>
                                                                    <span class="clarification-cell text-center" data-full-text="{!! $recevier->clarification_remark_reply !!}">

                                                                        @if(strlen($recevier->clarification_remark_reply) <= 50)
                                                                            {!! $recevier->clarification_remark_reply !!}
                                                                            @else
                                                                            {!! substr($recevier->clarification_remark_reply, 0, 50) !!}
                                                                            <span class="read-more" style="cursor: pointer; color: blue;">...Show More</span>

                                                                            @endif
                                                                    </span>
                                                                    @endif
                                                                </td>

                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="modal fade" id="clarificationReplyModal" tabindex="-1" role="dialog" aria-labelledby="clarificationReplyModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="clarificationReplyModalLabel">Reply to Email</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="replyForm">
                                                        <div class="form-group">
                                                            <label for="email">Name</label>
                                                            <input type="text" id="replyclarification_name" name="replyclarification_name" class="form-control" value="" readonly>
                                                        </div>
                                                        <input type="hidden" id="clarification_id" name="clarification_id" class="form-control">
                                                        <div class="form-group">
                                                            <label for="replyattachment">Attachment</label>
                                                            <input type="file" id="replyattachment" name="replyattachment" class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="replyMessage">Reply Message</label>
                                                            <textarea class="form-control" id="replyMessage" rows="4"></textarea>
                                                        </div>
                                                        <!-- Add other necessary fields for the reply -->
                                                        <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                                                        <button class="replyEmailBtn btn btn-primary"
                                                            type="button">Send</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    @endif

                                    @if (Auth::user()->role_id != 9)

                                    @if ($Nvsericestatus->rv1_status == 0 && $Nvsericestatus->draft == 1)
                                    @if (!empty($rv1) && $rv1 == $user->id)
                                    <div class="row m-3">
                                        <div class="col-md-12">
                                            <div class="card card-outline card-info">
                                                <div class="card-body">
                                                    <table class="table table-bordered">
                                                        <thead class="text-center">
                                                            <tr role="row">
                                                                <th class="text-center sorting_disabled" rowspan="1" colspan="1" aria-label="S.No">S.No</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" aria-label="Proposal Number: activate to sort column ascending">Name</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" aria-label="Budget Type: activate to sort column ascending">Email</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" aria-label="Initiated By: activate to sort column ascending">Department</th>
                                                                <th width="15%" class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" aria-label="Initiated Date: activate to sort column ascending">Mobile No</th>
                                                                <th width="15%" class="text-center" rowspan="1" colspan="1">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $count = 1; @endphp
                                                            @foreach ($employees as $employee)
                                                            <tr>
                                                                <td>{{ $count++ }}</td>
                                                                <td>{{ $employee->name }}</td>
                                                                <td>{{ $employee->email }}</td>
                                                                <td>{{ $employee->department->name }}</td>
                                                                <td>{{ $employee->phone }}</td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-info btn-sm clarification-btn"
                                                                        data-toggle="modal" data-target="#clarificationModal" data-name="{{ $employee->name }}"
                                                                        data-email="{{ $employee->email }}" data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                    <!-- <button type="button" class="btn btn-primary btn-sm email-btn" data-toggle="modal" data-target="#emailModal10">Email</button> -->
                                                                </td>
                                                            </tr>
                                                            <input type="hidden" id="receiver_user_id" value="">
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endif

                                    @php
                                    $isEligibleForRW2 = false;

                                    if ($Nvsericestatus->rv2_status == 0 && $Nvsericestatus->draft == 1 && $Nvsericestatus->rv1_status != 2) {
                                    if (!empty($rv1) && $Nvsericestatus->rv1_status == 1) {
                                    $isEligibleForRW2 = true;
                                    }
                                    $isEligibleForRW2 = true;
                                    }

                                    @endphp

                                    @if ($isEligibleForRW2)
                                    @if (!empty($rv2) && $rv2 == $user->id)
                                    <div class="row m-3">
                                        <div class="col-md-12">
                                            <div class="card card-outline card-info">
                                                <div class="card-body">
                                                    <table class="table table-bordered">
                                                        <thead class="text-center">
                                                            <tr role="row">
                                                                <th class="text-center sorting_disabled" width="5%" rowspan="1" colspan="1" style="width: 79px;" aria-label="S.No">S.No</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 114px;" aria-label="Proposal Number: activate to sort column ascending">Name</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 87px;" aria-label="Budget Type: activate to sort column ascending">Email</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 98px;" aria-label="Initiated By: activate to sort column ascending">Department</th>
                                                                <th width="15%" class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 90px;" aria-label="Initiated Date: activate to sort column ascending">Mobile No</th>
                                                                <th width="15%" class="text-center" rowspan="1" colspan="1">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $count = 1; @endphp
                                                            @foreach ($employees as $employee)
                                                            <tr>
                                                                <td>{{ $count++ }}</td>
                                                                <td>{{ $employee->name }}</td>
                                                                <td>{{ $employee->email }}</td>
                                                                <td>{{ $employee->department->name }}</td>
                                                                <td>{{ $employee->phone }}</td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-info btn-sm clarification-btn"
                                                                        data-toggle="modal" data-target="#clarificationModal" data-name="{{ $employee->name }}"
                                                                        data-email="{{ $employee->email }}" data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                    <!-- <button type="button" class="btn btn-primary btn-sm email-btn" data-toggle="modal" data-target="#emailModal10">Email</button> -->
                                                                </td>
                                                            </tr>
                                                            <input type="hidden" id="receiver_user_id" value="">
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endif

                                    @php
                                    $isEligibleForRW3 = false;

                                    if ($Nvsericestatus->rv3_status == 0 && $Nvsericestatus->draft == 1 && $Nvsericestatus->rv2_status != 2 && $Nvsericestatus->rv1_status != 2) {
                                    if (!empty($rv2) && $Nvsericestatus->rv2_status == 1) {
                                    $isEligibleForRW3 = true;
                                    } elseif (!empty($rv1) && $Nvsericestatus->rv1_status == 1) {
                                    $isEligibleForRW3 = true;
                                    }
                                    $isEligibleForRW3 = true;
                                    }

                                    @endphp

                                    @if ($isEligibleForRW3)
                                    @if (!empty($rv3) && $rv3 == $user->id)
                                    <div class="row m-3">
                                        <div class="col-md-12">
                                            <div class="card card-outline card-info">
                                                <div class="card-body">
                                                    <table class="table table-bordered">
                                                        <thead class="text-center">
                                                            <tr role="row">
                                                                <th class="text-center sorting_disabled" width="5%" rowspan="1" colspan="1" style="width: 79px;" aria-label="S.No">S.No</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 114px;" aria-label="Proposal Number: activate to sort column ascending">Name</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 87px;" aria-label="Budget Type: activate to sort column ascending">Email</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 98px;" aria-label="Initiated By: activate to sort column ascending">Department</th>
                                                                <th width="15%" class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 90px;" aria-label="Initiated Date: activate to sort column ascending">Mobile No</th>
                                                                <th width="15%" class="text-center" rowspan="1" colspan="1">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $count = 1; @endphp
                                                            @foreach ($employees as $employee)
                                                            <tr>
                                                                <td>{{ $count++ }}</td>
                                                                <td>{{ $employee->name }}</td>
                                                                <td>{{ $employee->email }}</td>
                                                                <td>{{ $employee->department->name }}</td>
                                                                <td>{{ $employee->phone }}</td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-info btn-sm clarification-btn"
                                                                        data-toggle="modal" data-target="#clarificationModal" data-name="{{ $employee->name }}"
                                                                        data-email="{{ $employee->email }}" data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                    <!-- <button type="button" class="btn btn-primary btn-sm email-btn" data-toggle="modal" data-target="#emailModal10">Email</button> -->
                                                                </td>
                                                            </tr>
                                                            <input type="hidden" id="receiver_user_id" value="">
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endif

                                    @php
                                    $isEligibleForRW4 = false;

                                    if ($Nvsericestatus->rv4_status == 0 && $Nvsericestatus->draft == 1 &&
                                    $Nvsericestatus->rv3_status != 2 && $Nvsericestatus->rv2_status != 2 && $Nvsericestatus->rv1_status != 2) {
                                    if (!empty($rv3) && $Nvsericestatus->rv3_status == 1) {
                                    $isEligibleForRW4 = true;
                                    } elseif (!empty($rv2) && $Nvsericestatus->rv2_status == 1) {
                                    $isEligibleForRW4 = true;
                                    } elseif (!empty($rv1) && $Nvsericestatus->rv1_status == 1) {
                                    $isEligibleForRW4 = true;
                                    }
                                    $isEligibleForRW4 = true;
                                    }

                                    @endphp

                                    @if ($isEligibleForRW4)
                                    @if (!empty($rv4) && $rv4 == $user->id)
                                    <div class="row m-3">
                                        <div class="col-md-12">
                                            <div class="card card-outline card-info">
                                                <div class="card-body">
                                                    <table class="table table-bordered">
                                                        <thead class="text-center">
                                                            <tr role="row">
                                                                <th class="text-center sorting_disabled" width="5%" rowspan="1" colspan="1" style="width: 79px;" aria-label="S.No">S.No</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 114px;" aria-label="Proposal Number: activate to sort column ascending">Name</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 87px;" aria-label="Budget Type: activate to sort column ascending">Email</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 98px;" aria-label="Initiated By: activate to sort column ascending">Department</th>
                                                                <th width="15%" class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 90px;" aria-label="Initiated Date: activate to sort column ascending">Mobile No</th>
                                                                <th width="15%" class="text-center" rowspan="1" colspan="1">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $count = 1; @endphp
                                                            @foreach ($employees as $employee)
                                                            <tr>
                                                                <td>{{ $count++ }}</td>
                                                                <td>{{ $employee->name }}</td>
                                                                <td>{{ $employee->email }}</td>
                                                                <td>{{ $employee->department->name }}</td>
                                                                <td>{{ $employee->phone }}</td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-info btn-sm clarification-btn"
                                                                        data-toggle="modal" data-target="#clarificationModal" data-name="{{ $employee->name }}"
                                                                        data-email="{{ $employee->email }}" data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                    <!-- <button type="button" class="btn btn-primary btn-sm email-btn" data-toggle="modal" data-target="#emailModal10">Email</button> -->
                                                                </td>
                                                            </tr>
                                                            <input type="hidden" id="receiver_user_id" value="">
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endif

                                    @php
                                    $isEligibleForHOD = false;

                                    if ($Nvsericestatus->hod_status == 0 && $Nvsericestatus->draft == 1 && $Nvsericestatus->rv4_status != 2 &&
                                    $Nvsericestatus->rv3_status != 2 && $Nvsericestatus->rv2_status != 2 && $Nvsericestatus->rv1_status != 2) {
                                    if (!empty($rv4) && $Nvsericestatus->rv4_status == 1) {
                                    $isEligibleForHOD = true;
                                    } elseif (!empty($rv3) && $Nvsericestatus->rv3_status == 1) {
                                    $isEligibleForHOD = true;
                                    } elseif (!empty($rv2) && $Nvsericestatus->rv2_status == 1) {
                                    $isEligibleForHOD = true;
                                    } elseif (!empty($rv1) && $Nvsericestatus->rv1_status == 1) {
                                    $isEligibleForHOD = true;
                                    }
                                    $isEligibleForHOD = true;
                                    }

                                    @endphp

                                    @if ($isEligibleForHOD)
                                    @if (!empty($hod) && $hod == $user->id)
                                    <div class="row m-3">
                                        <div class="col-md-12">
                                            <div class="card card-outline card-info">
                                                <div class="card-body">
                                                    <table class="table table-bordered">
                                                        <thead class="text-center">
                                                            <tr role="row">
                                                                <th class="text-center sorting_disabled" width="5%" rowspan="1" colspan="1" style="width: 79px;" aria-label="S.No">S.No</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 114px;" aria-label="Proposal Number: activate to sort column ascending">Name</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 87px;" aria-label="Budget Type: activate to sort column ascending">Email</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 98px;" aria-label="Initiated By: activate to sort column ascending">Department</th>
                                                                <th width="15%" class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 90px;" aria-label="Initiated Date: activate to sort column ascending">Mobile No</th>
                                                                <th width="15%" class="text-center" rowspan="1" colspan="1">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $count = 1; @endphp
                                                            @foreach ($employees as $employee)
                                                            <tr>
                                                                <td>{{ $count++ }}</td>
                                                                <td>{{ $employee->name }}</td>
                                                                <td>{{ $employee->email }}</td>
                                                                <td>{{ $employee->department->name }}</td>
                                                                <td>{{ $employee->phone }}</td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-info btn-sm clarification-btn"
                                                                        data-toggle="modal" data-target="#clarificationModal" data-name="{{ $employee->name }}"
                                                                        data-email="{{ $employee->email }}" data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                    <!-- <button type="button" class="btn btn-primary btn-sm email-btn" data-toggle="modal" data-target="#emailModal10">Email</button> -->
                                                                </td>
                                                            </tr>
                                                            <input type="hidden" id="receiver_user_id" value="">
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endif

                                    @if (!empty($group_cio) && $group_cio == $user->id)
                                    @if ($Nvsericestatus->hod_status == 1 && $Nvsericestatus->groupcio_status == 0)
                                    <div class="row m-3">
                                        <div class="col-md-12">
                                            <div class="card card-outline card-info">
                                                <div class="card-body">
                                                    <table class="table table-bordered">
                                                        <thead class="text-center">
                                                            <tr role="row">
                                                                <th class="text-center sorting_disabled" width="5%" rowspan="1" colspan="1" style="width: 79px;" aria-label="S.No">S.No</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 114px;" aria-label="Proposal Number: activate to sort column ascending">Name</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 87px;" aria-label="Budget Type: activate to sort column ascending">Email</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 98px;" aria-label="Initiated By: activate to sort column ascending">Department</th>
                                                                <th width="15%" class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 90px;" aria-label="Initiated Date: activate to sort column ascending">Mobile No</th>
                                                                <th width="15%" class="text-center" rowspan="1" colspan="1">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $count = 1; @endphp
                                                            @foreach ($employees as $employee)
                                                            <tr>
                                                                <td>{{ $count++ }}</td>
                                                                <td>{{ $employee->name }}</td>
                                                                <td>{{ $employee->email }}</td>
                                                                <td>{{ $employee->department->name }}</td>
                                                                <td>{{ $employee->phone }}</td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-info btn-sm clarification-btn"
                                                                        data-toggle="modal" data-target="#clarificationModal" data-name="{{ $employee->name }}"
                                                                        data-email="{{ $employee->email }}" data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                    <!-- <button type="button" class="btn btn-primary btn-sm email-btn" data-toggle="modal" data-target="#emailModal10">Email</button> -->
                                                                </td>
                                                            </tr>
                                                            <input type="hidden" id="receiver_user_id" value="">
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endif
                                    @php
                                    $workflow = null;
                                    $workflow = DB::table('capex_workflows_status')
                                    ->where('service_id', $service_details->id)
                                    ->where('workflow_user_id', $user->id)
                                    ->where('department_id', $user->department_id)
                                    ->where('nv_budget_type', $nv->budget_type)
                                    ->first();
                                    @endphp
                                    @if(!empty($workflow) && ($workflow->nv_stage_status == 0) && ($Nvsericestatus->is_reject == 0))
                                    <div class="row m-3">
                                        <div class="col-md-12">
                                            <div class="card card-outline card-info">
                                                <div class="card-body">
                                                    <table class="table table-bordered">
                                                        <thead class="text-center">
                                                            <tr role="row">
                                                                <th class="text-center sorting_disabled" width="5%" rowspan="1" colspan="1" style="width: 79px;" aria-label="S.No">S.No</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 114px;" aria-label="Proposal Number: activate to sort column ascending">Name</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 87px;" aria-label="Budget Type: activate to sort column ascending">Email</th>
                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 98px;" aria-label="Initiated By: activate to sort column ascending">Department</th>
                                                                <th width="15%" class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 90px;" aria-label="Initiated Date: activate to sort column ascending">Mobile No</th>
                                                                <th width="15%" class="text-center" rowspan="1" colspan="1">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $count = 1; @endphp
                                                            @foreach ($employees as $employee)
                                                            <tr>
                                                                <td>{{ $count++ }}</td>
                                                                <td>{{ $employee->name }}</td>
                                                                <td>{{ $employee->email }}</td>
                                                                <td>{{ $employee->department->name }}</td>
                                                                <td>{{ $employee->phone }}</td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-info btn-sm clarification-btn"
                                                                        data-toggle="modal" data-target="#clarificationModal" data-name="{{ $employee->name }}"
                                                                        data-email="{{ $employee->email }}" data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                    <!-- <button type="button" class="btn btn-primary btn-sm email-btn" data-toggle="modal" data-target="#emailModal10">Email</button> -->
                                                                </td>
                                                            </tr>
                                                            <input type="hidden" id="receiver_user_id" value="">
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif


                                    <br>
                                    <br>
                                    @php
                                    foreach($clarifications as $clarification){
                                    $serviceID=$clarification->service_id;
                                    }
                                    @endphp
                                    @if(!empty($serviceID))
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-info btn-md ml-4" data-toggle="modal" data-target="#clarificationLogModal" style="margin-top:-20px;">Clarification Log</button>
                                        </div>
                                    </div>

                                    @endif


                                    <div class="modal fade" id="clarificationLogModal" tabindex="-1"
                                        role="dialog" aria-labelledby="clarificationModalLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-xl" role="document">

                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="clarificationModalLabel">
                                                        <b>Clarification Log</b>
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="row m-3">
                                                        <div class="col-md-12">
                                                            <div class="card card-outline card-info">
                                                                <div class="card-body" style="height:200px;overflow:scroll">
                                                                    <table class="table table-bordered">
                                                                        <thead class="text-center">
                                                                            <tr role="row">
                                                                                <th class="text-center sorting_disabled" aria-label="S.No" style="width: 20px !important;">S.No</th>
                                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" aria-label="Proposal Number: activate to sort column ascending" style="width: 60px !important;">Sender</th>
                                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" aria-label="Budget Type: activate to sort column ascending" style="width: 60px !important;">Receiver</th>
                                                                                <!-- <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable"  aria-label="Initiated By: activate to sort column ascending">Clarifiation Attachment</th> -->
                                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" aria-label="Initiated By: activate to sort column ascending">Clarifiation Remark</th>
                                                                                <!-- <th width="15%" class="text-center sorting" tabindex="0" aria-controls="nv_datatable" rowspan="1" colspan="1" style="width: 90px;" aria-label="Initiated Date: activate to sort column ascending">Date and Time</th> -->
                                                                                <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" aria-label="Initiated By: activate to sort column ascending">Reply</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @php $count = 1; @endphp
                                                                            @foreach ($clarifications as $clarification)
                                                                            <tr>
                                                                                <td style="width: 20px !important;">{{ $count++ }}</td>
                                                                                <td style="width: 60px !important;">{{ $clarification->users->name }}</td>
                                                                                <td style="width: 60px !important;"> {{ $clarification->user_name }}</td>
                                                                                <td>
                                                                                    @if(!empty($clarification->attachment))
                                                                                    <a class="ml-2" download="{{ $clarification->attachment}}" href="{{ url(asset('clarification-file/' . $clarification->attachment)) }}">{{$clarification->attachment}}</a>
                                                                                    <br> @endif
                                                                                    @if(!empty($clarification->clarification_remark))

                                                                                    {{date('d-M-Y h:i:s A', strtotime($clarification->created_at))}}<br>
                                                                                    <span class="clarification-cell" data-full-text="{!! $clarification->clarification_remark !!}">

                                                                                        @if(strlen($clarification->clarification_remark) <= 50)
                                                                                            {!! $clarification->clarification_remark !!}
                                                                                            @else
                                                                                            {!! substr($clarification->clarification_remark, 0, 50) !!}
                                                                                            <span class="read-more" style="cursor: pointer; color: blue;">...Show More</span>

                                                                                            @endif
                                                                                    </span>

                                                                                    @endif

                                                                                </td>
                                                                                <td>
                                                                                    @if(!empty($clarification->clarification_remark_reply))
                                                                                    @if(!empty($clarification->reply_attachment))
                                                                                    <a class="ml-2" download="{{ $clarification->reply_attachment}}" href="{{ url(asset('clarification-file/' . $clarification->reply_attachment)) }}">{{$clarification->reply_attachment}}</a>
                                                                                    <br> @endif
                                                                                    {{ date('d-M-Y h:i:s A', strtotime($clarification->reply_timestamp)) }}
                                                                                    <br>
                                                                                    <span class="clarification-cell text-center" data-full-text="{!! $clarification->clarification_remark_reply !!}">

                                                                                        @if(strlen($clarification->clarification_remark_reply) <= 50)
                                                                                            {!! $clarification->clarification_remark_reply !!}
                                                                                            @else
                                                                                            {!! substr($clarification->clarification_remark_reply, 0, 50) !!}
                                                                                            <span class="read-more" style="cursor: pointer; color: blue;">...Show More</span>

                                                                                            @endif
                                                                                    </span>
                                                                                    @else
                                                                                    <!-- Show blank when data is not available -->
                                                                                    @endif
                                                                                </td>

                                                                            </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <!-- Clarification Modal -->
                                    <div class="modal fade" id="clarificationModal" tabindex="-1"
                                        role="dialog" aria-labelledby="clarificationModalLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-xl" role="document">

                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="clarificationModalLabel">
                                                        <b>Clarification</b>
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form id="clarification_form" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="row m-3">
                                                            <div class="col-md-12">
                                                                <div class="card card-outline card-info" style="margin-top:-20px;">
                                                                    <div class="card-body">
                                                                        <label
                                                                            for="exampleFormControlSelect1">Name</label>
                                                                        <select id="clarification_name"
                                                                            class="form-control"
                                                                            name="clarification_name"
                                                                            multiple="multiple">
                                                                            @foreach ($employees as $employee)
                                                                            <option value="{{ $employee->email }}" data-username="{{ $employee->name }}">
                                                                                {{ $employee->name }}
                                                                            </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <input type="hidden" id="user_name" name="user_name" value="">

                                                                        <input type="hidden" id="clarification_email"
                                                                            name="clarification_email">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row m-3">
                                                            <div class="col-md-12">
                                                                <div class="card card-outline card-info" style="margin-top:-20px;">
                                                                    <div class="card-body">
                                                                        <label for="exampleFormControlInput1">Clarification Attachment </label>
                                                                        <input type="file" class="form-control" id="attachment" name="attachment" style="width:50%;"></label><br>
                                                                        <label
                                                                            for="exampleFormControlInput1">Clarification
                                                                            Remarks</label>
                                                                        <textarea name="clarification_remark" id="clarification_remark" rows="10" cols="80"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row text-center" style="margin-top:-20px;">
                                                            <div class="col-12">
                                                                <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                                                                <button class="sendEmailBtn3 btn btn-primary"
                                                                    type="button">Send</button>

                                                            </div>
                                                        </div>

                                                    </div>
                                                </form>
                                                {{-- <div class="modal-footer">

                                                        <button class="sendEmailBtn btn btn-primary"
                                                            type="button">Send</button>

                                                    </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Email Modal -->
                                    <div class="modal fade" id="emailModal10" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="emailModalLabel">Email</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row m-3">
                                                        <div class="col-md-12">
                                                            <div class="card card-outline card-info">
                                                                <div class="card-body">
                                                                    <div class="form-group">
                                                                        <label for="toField">To:</label>
                                                                        <input type="text" class="form-control" id="toField" name="toField">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="ccField">CC:</label>
                                                                        <input type="text" class="form-control" id="ccField" name="ccField">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="subjectField">Subject:</label>
                                                                        <input type="text" class="form-control" id="subjectField" name="subjectField">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="exampleFormControlInput1">Email Remark</label>
                                                                        <textarea name="email_remark" id="email_remark" rows="10" cols="80"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary sendEmailBtn5">Send Email</button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                </div>
                <input type="hidden" name="nv_id" id='nv_id' value="{{ $service_details->nv_id }}">
                <input type="hidden" name="serviceID" id='serviceID' value="{{ $service_details->id }}">
                <input type="hidden" name="service_id" id='service_id' value="{{ $service_details->id }}">
                <input type="hidden" name="dop_ref_no" id='dop_ref_no'
                    value="{{ $service_details->dop_ref_no }}">
                <input type="hidden" name="prop_name" id='prop_name'
                    value="{{ $service_details->proposal_name ?? '' }}">
                <input type="hidden" name="derc_info" id='derc_info' value="{{$Nvsericestatus->derc_info}}">
            </div>
        </div>
        <div class="card col-md-12">
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        @if ($user->role_id != 9)

                        @if ($Nvsericestatus->rv1_status == 0 && $Nvsericestatus->draft == 1)
                        @if (!empty($rv1) && $rv1 == $user->id)
                        <button type="button" class="btn btn-info approve-button"
                            value="Approve">Approve</button>
                        <button type="button" class="btn btn-danger reject-button"
                            value="Reject">Reject</button>
                        <!-- <button type="button" class="btn btn-danger save-button" value="Save">Save</button> -->
                        @endif
                        @endif

                        @php
                        $isEligibleForRW2 = false;

                        if ($Nvsericestatus->rv2_status == 0 && $Nvsericestatus->draft == 1 && $Nvsericestatus->rv1_status != 2) {
                        if (!empty($rv1) && $Nvsericestatus->rv1_status == 1) {
                        $isEligibleForRW2 = true;
                        }
                        $isEligibleForRW2 = true;
                        }

                        @endphp

                        @if ($isEligibleForRW2)
                        @if (!empty($rv2) && $rv2 == $user->id)
                        <button type="button" class="btn btn-info approve-button"
                            value="Approve">Approve</button>
                        <button type="button" class="btn btn-danger reject-button"
                            value="Reject">Reject</button>
                        <!-- <button type="button" class="btn btn-danger save-button" value="Save">Save</button> -->
                        @endif
                        @endif
                        @php
                        $isEligibleForRW3 = false;

                        if ($Nvsericestatus->rv3_status == 0 && $Nvsericestatus->draft == 1 && $Nvsericestatus->rv2_status != 2 && $Nvsericestatus->rv1_status != 2) {
                        if (!empty($rv2) && $Nvsericestatus->rv2_status == 1) {
                        $isEligibleForRW3 = true;
                        } elseif (!empty($rv1) && $Nvsericestatus->rv1_status == 1) {
                        $isEligibleForRW3 = true;
                        }
                        $isEligibleForRW3 = true;
                        }

                        @endphp

                        @if ($isEligibleForRW3)
                        @if (!empty($rv3) && $rv3 == $user->id)
                        <button type="button" class="btn btn-info approve-button"
                            value="Approve">Approve</button>
                        <button type="button" class="btn btn-danger reject-button"
                            value="Reject">Reject</button>
                        <!-- <button type="button" class="btn btn-danger save-button" value="Save">Save</button> -->
                        @endif
                        @endif

                        @php
                        $isEligibleForRW4 = false;

                        if ($Nvsericestatus->rv4_status == 0 && $Nvsericestatus->draft == 1 &&
                        $Nvsericestatus->rv3_status != 2 && $Nvsericestatus->rv2_status != 2 && $Nvsericestatus->rv1_status != 2) {
                        if (!empty($rv3) && $Nvsericestatus->rv3_status == 1) {
                        $isEligibleForRW4 = true;
                        } elseif (!empty($rv2) && $Nvsericestatus->rv2_status == 1) {
                        $isEligibleForRW4 = true;
                        } elseif (!empty($rv1) && $Nvsericestatus->rv1_status == 1) {
                        $isEligibleForRW4 = true;
                        }
                        $isEligibleForRW4 = true;
                        }

                        @endphp

                        @if ($isEligibleForRW4)
                        @if (!empty($rv4) && $rv4 == $user->id)
                        <button type="button" class="btn btn-info approve-button"
                            value="Approve">Approve</button>
                        <button type="button" class="btn btn-danger reject-button"
                            value="Reject">Reject</button>
                        <!-- <button type="button" class="btn btn-danger save-button" value="Save">Save</button> -->
                        @endif
                        @endif

                        @php
                        $isEligibleForHOD = false;

                        if ($Nvsericestatus->hod_status == 0 && $Nvsericestatus->draft == 1 && $Nvsericestatus->rv4_status != 2 &&
                        $Nvsericestatus->rv3_status != 2 && $Nvsericestatus->rv2_status != 2 && $Nvsericestatus->rv1_status != 2) {
                        if (!empty($rv4) && $Nvsericestatus->rv4_status == 1) {
                        $isEligibleForHOD = true;
                        } elseif (!empty($rv3) && $Nvsericestatus->rv3_status == 1) {
                        $isEligibleForHOD = true;
                        } elseif (!empty($rv2) && $Nvsericestatus->rv2_status == 1) {
                        $isEligibleForHOD = true;
                        } elseif (!empty($rv1) && $Nvsericestatus->rv1_status == 1) {
                        $isEligibleForHOD = true;
                        }
                        $isEligibleForHOD = true;
                        }

                        @endphp

                        @if ($isEligibleForHOD)
                        @if (!empty($hod) && $hod == $user->id)
                        <button type="button" class="btn btn-info approve-button"
                            value="Approve">Approve</button>
                        <button type="button" class="btn btn-danger reject-button"
                            value="Reject">Reject</button>
                        <!-- <button type="button" class="btn btn-danger save-button" value="Save">Save</button> -->
                        @endif
                        @endif


                        @if (!empty($group_cio) && $group_cio == $user->id)
                        @if ($Nvsericestatus->hod_status == 1 && $Nvsericestatus->groupcio_status == 0)
                        <button type="button" class="btn btn-info approve-button"
                            value="Approve">Forward</button>
                        <button type="button" class="btn btn-danger reject-button"
                            value="Reject">Reject</button>
                        <!-- <button type="button" class="btn btn-danger save-button" value="Save">Save</button> -->
                        @endif
                        @endif

                        @if(!empty($workflow) && ($workflow->nv_stage_status == 0) && ($Nvsericestatus->is_reject == 0))

                        <button type="button" class="btn btn-info approve-button"
                            value="Approve">Approve</button>
                        <button type="button" class="btn btn-danger reject-button"
                            value="Reject">Reject</button>
                        @endif

                        @endif
                        <!-- <a target="_blank" href="https://accounts.zoho.in/signin?servicename=ZohoSign&signupurl=https://www.zoho.com/sign/signup.html"><button type="button" class="btn btn-info">Zoho Sign</button></a> -->
                    </div>
                </div>
            </div>

            <input type="hidden" id="budget_type" value="{{$nv->budget_type}}">
            <input type="hidden" id="fiscal_year" value="{{$nv->fiscal_year}}">
            <input type="hidden" id="services_id" value="{{$nv->service_id}}">
            <input type="hidden" id="department_name" value="{{getDepartmentName($nv->department_id)}}">
            <input type="hidden" id="nvid" value="{{$nv->id}}">
            </form>

            <!-- /.card -->

        </div>
    </div>
    </div>

</section>


@endsection
@push('script')
<script src="{{ asset('theme/plugins/ckeditor/ckeditor.js') }}"></script>
{{--
<script src="{{ asset('theme/plugins/summernote/summernote.js') }}"></script> --}}
<script src="{{ asset('theme/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('theme/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('admin/js/brand.js') }}"></script>
<script src="{{ asset('admin/js/nvservices.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Replace the <textarea id="editor1"> with a CKEditor 4
    // instance, using default configuration.
    // Replace the <textarea id="editor1"> with a CKEditor 4
    CKEDITOR.replace('clarification_remark');
    CKEDITOR.replace('email_remark');
</script>
<!-- <script src="{{ asset('theme/plugins/ckeditor/ckeditor.js') }}"></script>
                                <script>
                                $(document).ready(function () {
                                    // Initialize CKEditor
                                    CKEDITOR.replace('just_of_proposal');
                                    CKEDITOR.replace('background');
                                });

                                </script> -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector("#background")).then(editor => {
        editor.enableReadOnlyMode("editor");
        console.log(editor);
    }).catch(error => {
        console.error(error);
    });
    ClassicEditor.create(document.querySelector("#just_of_proposal")).then(editor => {
        editor.enableReadOnlyMode("editor");
        console.log(editor);
    }).catch(error => {
        console.error(error);
    });
    ClassicEditor.create(document.querySelector("#broad_just")).then(editor => {
        editor.enableReadOnlyMode("editor");
        console.log(editor);
    }).catch(error => {
        console.error(error);
    });
</script>
<script>
    $(document).ready(function() {
        $('#clarification_name').change(function() {
            var selectedOptions = $('#clarification_name option:selected');
            var selectedUsernames = selectedOptions.map(function() {
                return $(this).data('username');
            }).get();
            $('#user_name').val(selectedUsernames.join(', '));
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Update the clarification modal with selected employee's name and email
        $('.clarification-btn').click(function() {
            var name = $(this).data('name');
            var email = $(this).data('email');
            var userId = $(this).data('userid');
            var clarificationId = $(this).data('data-clarificationid');
            $('#receiver_user_id').val(userId);
            $('#clarificationModal').find('#clarification_name').val(email).trigger('change');
            $('#clarificationModal').find('#clarification_email').val(email);
        });

        // Clear the modal data when the modal is closed
        $('#clarificationModal').on('hidden.bs.modal', function() {
            $('#clarification_name').val('').trigger('change');
            $('#clarification_email').val('');
            $('#clarification_remark').val('');
        });
        $('#emailModal10').on('hidden.bs.modal', function() {
            $('#toField').val('');
            $('#ccField').val('');
            $('#subjectField').val('');
            CKEDITOR.instances['email_remark'].setData('');
        });

        $('#clarificationModal').on('hidden.bs.modal', function() {
            CKEDITOR.instances['clarification_remark'].setData('');
        });

    });

    $(document).ready(function() {
        $('#clarification_name').select2({
            maximumSelectionLength: 1,
        });
    });
</script>
<!-- clarification  Model-->
<script>
    $(document).ready(function() {
        $('.sendEmailBtn3').click(function() {

            var selectedEmails = $('#clarification_name').val();
            var clarificationRemark = CKEDITOR.instances['clarification_remark'].getData();
            var nv_id = $('#nv_id').val();
            var service_id = $('#service_id').val();
            var user_name = $('#user_name').val();
            var userId = $('#receiver_user_id').val();
            var proposal_name = $('#prop_name').val();
            var formData = new FormData();
            formData.append('emails', selectedEmails);
            formData.append('remark', clarificationRemark);
            formData.append('nv_id', nv_id);
            formData.append('service_id', service_id);
            formData.append('user_name', user_name);
            formData.append('user_id', userId);
            formData.append('proposal_name', proposal_name);
            formData.append('is_replied', 0);
            formData.append('file', $('#attachment')[0].files[0]);
            // alert(clarificationRemark);
            // Perform an AJAX request to send the email
            $.ajax({
                url: '/admin/send-Clarification',
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting contentType

                success: function(response) {
                    console.log(response);
                    // Handle the response from the server
                    if (response.success) {
                        // Email sent successfully

                        swal({
                            title: "Success!",
                            text: "Email Sent Successfully.",
                            icon: "success",
                            button: "OK",
                        }).then(function() {
                            window.location.reload();
                        });
                        $('#clarificationModal').modal('hide');
                        // Clear the modal data
                        $('#clarification_name').val('').trigger('change');
                        $('#clarification_remark').val('');
                    } else {
                        // Email sending failed
                        alert('Failed to send the email. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    // Handle the AJAX request error
                    alert('An error occurred while sending the email. Please try again.');
                }
            });
        });
    });
</script>
<!-- Email Model-->
<script>
    $(document).ready(function() {
        $('#emailModal10').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget);
            var email = button.data('email');
            // Set the email value in the "To" field
            $('#toField').val(email);
        });

        $('.sendEmailBtn5').click(function() {

            var toField = $('#toField').val();
            var ccField = $('#ccField').val();
            var subjectField = $('#subjectField').val();
            var emailRemark = CKEDITOR.instances['email_remark'].getData();
            // Perform an AJAX request to send the email
            $.ajax({
                url: '/admin/send-email4',
                type: 'GET',
                data: {
                    to: toField,
                    cc: ccField,
                    subject: subjectField,
                    remark: emailRemark
                },
                success: function(response) {
                    // Handle the response from the server
                    if (response.success) {
                        // Email sent successfully
                        swal({
                            title: "Success!",
                            text: "Email Sent Successfully.",
                            icon: "success",
                            button: "OK",
                        });
                        $('#emailModal10').modal('hide');

                        // Clear the modal data
                        $('#toField').val('');
                        $('#ccField').val('');
                        $('#subjectField').val('');
                        $('#email_remark').val('');
                    } else {
                        // Email sending failed
                        alert('Failed to send the email. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    // Handle the AJAX request error
                    alert('An error occurred while sending the email. Please try again.');
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const downloadLinks = document.querySelectorAll('.download-link');
        const checkboxes = document.querySelectorAll('.selectedImage');
        const selectedDownloadButton = document.getElementById('selectedDownloadButton');
        const selectAllImagesCheckbox = document.getElementById('select_all');
        let selectedFiles = [];

        checkboxes.forEach((checkbox, index) => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    downloadLinks[index].style.display = 'inline';
                    downloadLinks[index].setAttribute('data-selected', 'true');
                    selectedFiles.push(downloadLinks[index].getAttribute('data-file'));
                } else {
                    downloadLinks[index].setAttribute('data-selected', 'false');
                    const fileUrl = downloadLinks[index].getAttribute('data-file');
                    selectedFiles = selectedFiles.filter(file => file !== fileUrl);
                }
                updateSelectAllImagesCheckbox();
            });
        });

        selectedDownloadButton.addEventListener('click', function() {
            if (selectedFiles.length === 0) {
                alert('Please select at least one file to download.');
            } else {
                selectedFiles.forEach(file => {
                    downloadFile(file);
                });
            }
        });

        selectAllImagesCheckbox.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedFiles();
        });

        function updateSelectedFiles() {
            selectedFiles = [];
            checkboxes.forEach((checkbox, index) => {
                if (checkbox.checked) {
                    selectedFiles.push(downloadLinks[index].getAttribute('data-file'));
                }
            });
        }

        function updateSelectAllImagesCheckbox() {
            const checkedCount = document.querySelectorAll('input[type="checkbox"]:checked').length;
            selectAllImagesCheckbox.checked = checkedCount === checkboxes.length;
        }

        function downloadFile(fileUrl) {
            // Create a temporary link element
            const link = document.createElement('a');
            link.href = fileUrl;
            link.download = getFileNameFromUrl(fileUrl);
            link.click();
        }

        function getFileNameFromUrl(url) {
            return url.substring(url.lastIndexOf('/') + 1);
        }
    });

    function autoResizeTextarea(event) {
        const textarea = event.target;
        textarea.style.height = "auto";
        textarea.style.height = `${textarea.scrollHeight}px`;
    }

    const textareas = document.querySelectorAll(".auto-resize-textarea");


    textareas.forEach((textarea) => {
        autoResizeTextarea({
            target: textarea
        });
    });

    textareas.forEach((textarea) => {
        textarea.addEventListener("input", autoResizeTextarea);
    });
</script>
<script>
    $(document).ready(function() {
        $('.clarification-cell').each(function() {
            var fullText = $(this).data('full-text');
            if (fullText.length > 50) {
                $(this).html(fullText.substring(0, 50) +
                    '<span class="read-more" style="cursor: pointer; color: blue;">...Show More</span>' +
                    '<span class="read-less" style="cursor: pointer; color: red; display: none;">...Show Less</span>');
            }

            $(this).on('click', '.read-more', function() {
                var fullText = $(this).closest('.clarification-cell').data('full-text');
                $(this).siblings('.read-less').show();
                $(this).hide();
                $(this).closest('.clarification-cell').html(fullText +
                    '<span class="read-less" style="cursor: pointer; color: red;">...Show Less</span>');
            });

            $(this).on('click', '.read-less', function() {
                $(this).siblings('.read-more').show();
                $(this).hide();
                $(this).parent('.clarification-cell').html(fullText.substring(0, 50) +
                    '<span class="read-more" style="cursor: pointer; color: blue;">...Show More</span>' +
                    '<span class="read-less" style="cursor: pointer; color: red; display: none;">...Show Less</span>');
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('.download-all').on('click', function(e) {
            e.preventDefault();

            var files = $('#others_data').val().split(',');

            downloadFiles(files);
        });

        function downloadFiles(files) {
            var link = document.createElement('a');
            link.style.display = 'none';

            document.body.appendChild(link);

            for (var i = 0; i < files.length; i++) {
                link.setAttribute('href', '{{ url('
                    services - doc / ') }}/' + files[i]);
                link.setAttribute('download', files[i]);
                link.click();
            }

            document.body.removeChild(link);
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('.clarification-reply').click(function() {
            var name = $(this).data('name');
            $('#clarificationReplyModal').find('#replyclarification_name').val(name);
            // Retrieve data from the table row where the button was clicked
            var emailSender = $(this).closest('tr').find('td:eq(1)').text().trim();
            var emailReceiver = $(this).closest('tr').find('td:eq(2)').text().trim();
            var clfn_id = $(this).attr('data-clarificationid');
            console.log(clfn_id);
            $('#clarification_id').val(clfn_id);
            // Populate modal fields with the retrieved data
            // var emailsValue = emailReceiver; // Replace this with your actual value
            // var trimmedEmails = emailsValue.replace(/[\[\]"]/g, '');
            // $('#email').val(emailsValue); // Fill the email field with the receiver's email
            // You can fill other fields as needed

            // Show the modal
            $('#clarificationReplyModal').modal('show');
        });

        $(document).ready(function() {
            $('.replyEmailBtn').click(function() {
                var selectedEmails = $('#replyclarification_name').val();
                // var clarificationRemark = CKEDITOR.instances['clarification_remark'].getData();
                var nv_id = $('#nv_id').val();
                var service_id = $('#service_id').val();
                var user_name = $('#user_name').val();
                var userId = $('#receiver_user_id').val();
                var clarificationId = $('#clarification_id').val();
                var replyMessage = $('#replyMessage').val()
                var formData = new FormData();
                formData.append('emails', selectedEmails);
                // formData.append('material_id', material_id);
                formData.append('nv_id', nv_id);
                formData.append('service_id', service_id);
                formData.append('user_name', user_name);
                formData.append('user_id', userId);
                formData.append('clarificationId', clarificationId);
                formData.append('remark', replyMessage);
                formData.append('is_replied', 1);
                formData.append('file', $('#replyattachment')[0].files[0]);

                // alert(clarificationRemark);
                // Perform an AJAX request to send the email
                $.ajax({
                    /*url: '/admin/send-email',
                    type: 'POST',
                    data: {
                        emails: selectedEmails,
                        nv_id:nv_id,
                         material_id:material_id,
                        user_name:user_name,
                        user_id: userId,
                        clarificationId: clarificationId,
                        remark: replyMessage,
                        is_replied: 1
                    },*/

                    url: '/admin/send-email3',
                    type: 'POST',
                    data: formData,
                    processData: false, // Prevent jQuery from processing the data
                    contentType: false, // Prevent jQuery from setting contentType


                    success: function(response) {
                        // Handle the response from the server
                        if (response.success) {
                            // Email sent successfully

                            swal({
                                title: "Success!",
                                text: "Email Sent Successfully.",
                                icon: "success",
                                button: "OK",
                            }).then(function() {
                                window.location.reload();
                            });
                            $('#clarificationModal').modal('hide');
                            // Clear the modal data
                            $('#clarification_name').val('').trigger('change');
                            $('#clarification_remark').val('');
                        } else {
                            // Email sending failed
                            alert('Failed to send the email. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle the AJAX request error
                        alert('An error occurred while sending the email. Please try again.');
                    }
                });
            });
        });
    });
</script>
<script>
    if ($(document).find('#nv_serviceBoq_datatable2').length > 0) {
        var nv_id = $('#nv_id').val();
        var service_id = $('#serviceID').val();
        // alert(nv_id);
        $('#nv_serviceBoq_datatable2').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            destroy: true,
            "searching": false,
            "ordering": false,
            ajax: {
                data: {
                    nv_id: nv_id,
                    service_id: service_id,
                },
                url: '/admin/nv_serviceBoq',


            },
            type: 'get',
            url: '/admin/nv_material/approvedByStatus',
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                },
                // {
                // 	data: 'id',
                // 	name: 'id',
                // 	className: "text-center"
                // },
                {
                    data: 'nv_id',
                    name: 'nv_id',
                    className: "text-center"
                },
                {
                    data: 'service_code',
                    name: 'service_code',
                    className: "text-center"
                },
                {
                    data: 'description',
                    name: 'description',
                    className: "text-center"
                },
                {
                    data: 'uom',
                    name: 'uom',
                    className: "text-center"
                },
                {
                    data: 'rate',
                    name: 'rate',
                    className: "text-center"
                },

                {
                    data: 'qty',
                    name: 'qty',
                    className: "text-center"
                },
                {
                    data: 'amount',
                    name: 'amount',
                    className: "text-center"
                },

                {
                    data: 'action',
                    name: 'action',
                    className: "text-center",
                    orderable: false,
                    visible: false
                },




            ],
            'columnDefs': [{
                'orderable': false,
                'targets': 0
            }, {
                'visible': false,
                'targets': [1],
                'orderable': true
            }],
            'aaSorting': [
                [1, 'desc']
            ]
        });
    }
</script>
<script>
    function scrollToElement(element) {
        document.querySelector(element).scrollIntoView({
            behavior: 'smooth'
        });
    }

    window.onload = function() {
        scrollToElement('.approve-button');
    };
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.yes-no-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    // Uncheck other checkboxes in the same group
                    document.querySelectorAll(`.yes-no-checkbox[data-group="${this.dataset.group}"]`).forEach(otherCheckbox => {
                        if (otherCheckbox !== this) {
                            otherCheckbox.checked = false;
                        }
                    });
                }
            });
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const yesCheckbox = document.getElementById('yes');
    const noCheckbox = document.getElementById('no');
    
    // Function to ensure only one checkbox is checked
    function handleCheckboxChange(checkedBox, otherBox) {
        if (checkedBox.checked) {
            otherBox.checked = false;
        }
    }
    
    // Add event listeners
    yesCheckbox.addEventListener('change', function() {
        handleCheckboxChange(this, noCheckbox);
    });
    
    noCheckbox.addEventListener('change', function() {
        handleCheckboxChange(this, yesCheckbox);
    });
});
</script>
@endpush
