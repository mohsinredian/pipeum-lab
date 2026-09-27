@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{asset('admin/css/styles.css')}}">

<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        cursor: default;
        padding-left: 25px !important;
        padding-right: 3px !important;
    }

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
@extends('admin.layout.master', ['page_title' => 'Create NV Material'])



@php


$id = request()->segment(4);
$user = \Auth()->user();

$segment_id =request()->segment(5);

if(!empty($segment_id)){
$data = App\Models\NVMaterial::where('id', $segment_id)->orderBy('id', 'desc')->first();

$Nvsericestatus = App\Models\Nvsericestatus::where('material_id', $segment_id)->exists();

if ($Nvsericestatus) {
$Nvsericestatus = App\Models\Nvsericestatus::where('material_id', $segment_id)->first();
} else {
$Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $segment_id)->first();

}
}else{
$data = App\Models\NVMaterial::where('nv_id', $id)->orderBy('id', 'desc')->first();

$Nvsericestatus = App\Models\Nvsericestatus::where('material_id', $data->id)->exists();

if ($Nvsericestatus) {
$Nvsericestatus = App\Models\Nvsericestatus::where('material_id', $data->id)->first();
} else {
$Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $data->id)->first();
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
                <h1 class="m-0">Manage NV Material</h1>

            </div>
            <div class="col-sm-6">

                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ URL::to('/admin/dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">NV Material</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@php
$user = \Auth::user();
@endphp

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form class="form" id="preview_nvmaterial">
                    @csrf
                    <div class="card card-info  shadow-lg printout show-table-data-nv-material">
                        <div class="card-header bg-transparent">
                            <div class="card-title ">
                                <h3 class="card-title">Need Validation Material Details<br>
                                    NV Number: {{'NV' . '/' . $nv->budget_type . '/' . $nv->fiscal_year . '/' . getDepartmentNameByPro($nv->user_id) . '/' . $nv->service->name . '/' . $nv->id}}</h3>
                            </div>

                            <div class="card-toolbar">
                                <button type="reset" class="btn btn-primary mt-2" onclick="history.back();">
                                    << Back</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="form-group row" style="margin-top:-12px">
                                <div class="col-12">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item ">
                                            <h6 class="accordion-header" type="button" data-toggle="collapse"
                                                data-target="#collapsefive" aria-expanded="true"
                                                aria-controls="collapsefive">
                                                All Attachments ({{$countFiles ?? 0}})</h6>

                                            <div id="collapsefive" class="collapse hide" aria-labelledby="collapsefive"
                                                data-parent="#accordionExample">
                                                <div class="card-body">

                                                    @if (!empty($material_doc->previous_work_order)||!empty($material_doc->derc_stakeholder_approvals)||!empty($material_doc->consumption_details)||!empty($material_doc->vend_quatation)||!empty($material_doc->photo_product)||!empty($material_doc->material_procurement)||!empty($material_doc->budget_for_both)||!empty($material_doc->new_product)||!empty($material_doc->cm_rate_ref)||!empty($material_doc->vendor_quatation)||!empty($material_doc->last_purchase_price)||!empty($material_doc->user_estimation)||!empty($material_doc->previous_wo_rc)||!empty($material_doc->cost_calculation_for_service)||!empty($material_doc->others)||!empty($material_doc->special_attch)||!empty($material_doc->quant_just))
                                                    <div class="form-group">
                                                        <input type="checkbox" id="select_all">
                                                        <label for="select_all">Select All</label>

                                                    </div>
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">

                                                                <label for="exampleFormControlInput1">
                                                                    C&M Rate
                                                                    Reference : <br>
                                                                    @if (!empty($material_doc->cm_rate_ref))
                                                                    @php
                                                                    $images = explode(',', $material_doc->cm_rate_ref);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>

                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"> Vendor
                                                                    Quatation : <br>

                                                                    @if (!empty($material_doc->vend_quatation))
                                                                    @php
                                                                    $images = explode(',', $material_doc->vend_quatation);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif
                                                                </label>

                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"> Last Purchase
                                                                    Price : <br>
                                                                    @if (!empty($material_doc->last_purchase_price))
                                                                    @php
                                                                    $images = explode(',', $material_doc->last_purchase_price);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>

                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"> User
                                                                    Estimation : <br>
                                                                    @if (!empty($material_doc->user_estimation))
                                                                    @php
                                                                    $images = explode(',', $material_doc->user_estimation);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif
                                                                </label>

                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Previous WO/RC (if any) : <br>
                                                                    @if (!empty($material_doc->previous_wo_rc))
                                                                    @php
                                                                    $images = explode(',', $material_doc->previous_wo_rc);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif
                                                                </label>

                                                            </div>
                                                        </div>

                                                        <div
                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"
                                                                    title="Material Trail Details and Schudule of Pilot feedback Submission"> New
                                                                    Product : <br>
                                                                    @if (!empty($material_doc->new_product))
                                                                    @php
                                                                    $images = explode(',', $material_doc->new_product);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"
                                                                    title="Material Trail Details and Schudule of Pilot feedback Submission"> Quantity Justification: <br>
                                                                    @if (!empty($material_doc->quant_just))
                                                                    @php
                                                                    $images = explode(',', $material_doc->quant_just);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"
                                                                    title="Material Trail Details and Schudule of Pilot feedback Submission"> Special Remarks: <br>
                                                                    @if (!empty($material_doc->special_attch))
                                                                    @php
                                                                    $images = explode(',', $material_doc->special_attch);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Copy of Previous
                                                                    work
                                                                    order/purchase order : <br>
                                                                    @if (!empty($material_doc->previous_work_order))
                                                                    @php
                                                                    $images = explode(',', $material_doc->previous_work_order);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"> Copy of
                                                                    DERC/Other
                                                                    Stakeholder Approvals : <br>

                                                                    @if (!empty($material_doc->derc_stakeholder_approvals))
                                                                    @php
                                                                    $images = explode(',', $material_doc->derc_stakeholder_approvals);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"> Consumption
                                                                    details -
                                                                    Last 3
                                                                    Year : <br>
                                                                    @if (!empty($material_doc->consumption_details))
                                                                    @php
                                                                    $images = explode(',', $material_doc->consumption_details);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"> Technical
                                                                    Specifications : <br>
                                                                    @if (!empty($material_doc->vendor_quatation))
                                                                    @php
                                                                    $images = explode(',', $material_doc->vendor_quatation);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif


                                                                </label>

                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"> Photographs of
                                                                    Product : <br>
                                                                    @if (!empty($material_doc->photo_product))
                                                                    @php
                                                                    $images = explode(',', $material_doc->photo_product);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>

                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"> Material
                                                                    Procurement : <br>
                                                                    @if (!empty($material_doc->material_procurement))
                                                                    @php
                                                                    $images = explode(',', $material_doc->material_procurement);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1"> Budget Statement
                                                                    for
                                                                    Both
                                                                    OPEX/CAPEX Activities : <br>
                                                                    @if (!empty($material_doc->budget_for_both))
                                                                    @php
                                                                    $images = explode(',', $material_doc->budget_for_both);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Cost Caluculation
                                                                    for
                                                                    services : <br>
                                                                    @if (!empty($material_doc->cost_calculation_for_service))
                                                                    @php
                                                                    $images = explode(',', $material_doc->cost_calculation_for_service);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
                                                                        {{ $image }}
                                                                    </a><br>
                                                                    @endforeach
                                                                    @else
                                                                    <span>N/A</span>
                                                                    @endif

                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Others : <br>
                                                                    @if (!empty($material_doc->others))
                                                                    @php
                                                                    $images = explode(',', $material_doc->others);
                                                                    @endphp
                                                                    @foreach($images as $image)
                                                                    <input type="checkbox" class="selectedImage" data-file="{{ asset('materials-doc/' . $image) }}">
                                                                    <a href="{{ asset('materials-doc/' . $image) }}" target="_blank" class="ml-2 download-link" data-file="{{ asset('materials-doc/' . $image) }}" data-field="others" title="Download">
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

                                                    @if (!empty($material_doc->previous_work_order)||!empty($material_doc->derc_stakeholder_approvals)||!empty($material_doc->consumption_details)||!empty($material_doc->vend_quatation)||!empty($material_doc->photo_product)||!empty($material_doc->material_procurement)||!empty($material_doc->budget_for_both)||!empty($material_doc->new_product)||!empty($material_doc->cm_rate_ref)||!empty($material_doc->vendor_quatation)||!empty($material_doc->last_purchase_price)||!empty($material_doc->user_estimation)||!empty($material_doc->previous_wo_rc)||!empty($material_doc->cost_calculation_for_service)||!empty($material_doc->others)||!empty($material_doc->special_attch)||!empty($material_doc->quant_just))
                                                    <!-- <a href="/admin/download-attachments/{{$material_details->nv_id}}/{{$material_details->company_id}}"><button type="button" class="btn btn-success" >Download All</button></a>&nbsp;&nbsp; -->
                                                    <button type="button" class="btn btn-success" id="selectedDownloadButton">Selected Download</button>
                                                    <!-- <a target="_blank" href=" {{url('/admin/nv_material/previewAttachedFiles', ['id' => $material_details->nv_id]) }}"><button type="button" class="btn btn-success" >Preview All Attached Files</button></a> -->

                                                    @endif


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-xl-4 col-lg-4 col-md-4" style="margin-top:-12px">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Sub-Department Name</label>
                                        @if (!empty($material_details->dept_id))
                                        <input readonly type="text" class="form-control" name="dept_id"
                                            id="dept_id" value="{{ $material_details->department->name }}">
                                        @else
                                        <input readonly type="text" class="form-control" name="dept_id"
                                            id="dept_id" value="N/A">
                                        @endif
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-4" style="margin-top:-12px">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">DOP Reference Number </label>
                                        @if (!empty($material_details->dop))
                                        <input readonly type="text" class="form-control" id="dop"
                                            name="dop"
                                            value="{{ $material_details != '' ? $material_details['dop'] : '' }}">
                                        @else
                                        <input readonly type="text" class="form-control" id="dop"
                                            name="dop" value="N/A">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4" style="margin-top:-12px">
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

                                <div class="col-12 col-xs-12" style="margin-top:-12px">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Proposal Name</label>
                                        @if (!empty($material_details->proposal_name))
                                        <textarea readonly name="proposal_name" id="proposal_name" class="form-control auto-resize-textarea"
                                            value="" placeholder=" Enter Proposal Name">{{ $material_details != '' ? $material_details['proposal_name'] : '' }}</textarea>
                                        @else
                                        <textarea readonly name="proposal_name" id="proposal_name" class="form-control auto-resize-textarea"
                                            value="" placeholder=" Enter Proposal Name">N/A</textarea>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Background (Max 2500 Characters)</label>
                                        @if (!empty($material_details->background))
                                        <textarea readonly name="background" id="background" class="form-control auto-resize-textarea"
                                            value="{{ $material_details != '' ? $material_details['background'] : '' }}" placeholder=" Enter Background">{{ $material_details != '' ? $material_details['background'] : '' }}</textarea>
                                        @else
                                        <textarea readonly name="background" id="background" class="form-control auto-resize-textarea"
                                            value="{{ $material_details != '' ? $material_details['background'] : '' }}" placeholder=" Enter Background">N/A</textarea>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Broad Justification (Max 500
                                            Characters)</label>
                                        @if (!empty($material_details->broad_just))
                                        <textarea readonly name="broad_just" id="broad_just" class="form-control auto-resize-textarea"
                                            value="{{ $material_details != '' ? $material_details['broad_just'] : '' }}"
                                            placeholder=" Enter ustification of Proposal">{{ $material_details != '' ? $material_details['broad_just'] : '' }}</textarea>
                                        @else
                                        <textarea readonly name="broad_just" id="broad_just" class="form-control auto-resize-textarea"
                                            value="{{ $material_details != '' ? $material_details['broad_just'] : '' }}"
                                            placeholder=" Enter ustification of Proposal">N/A</textarea>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Detailed Justification (Max 2500
                                            Characters)</label>
                                        @if (!empty($material_details->just_Prop))
                                        <textarea readonly name="just_Prop" id="just_Prop" class="form-control auto-resize-textarea"
                                            value="{{ $material_details != '' ? $material_details['just_Prop'] : '' }}"
                                            placeholder=" Enter ustification of Proposal">{{ $material_details != '' ? $material_details['just_Prop'] : '' }}</textarea>
                                        <div class="col-md-6 mt-2 d-flex align-items-center gap-2">
                                            <input type="file" class="form-control" name="just_prop_upload[]" multiple>

                                            @if (!empty($material_doc->just_prop_upload))
                                            @php
                                            $images = explode(',', $material_doc->just_prop_upload);
                                            @endphp
                                           @foreach($images as $image)
                                                    <input type="checkbox" class="selectedImage ml-2" data-file="{{ asset('materials-doc/' . urlencode($image)) }}">

                                                    <a href="{{ asset('materials-doc/' . urlencode($image)) }}" 
                                                    target="_blank" 
                                                    class="ml-2 download-link"
                                                    data-file="{{ asset('materials-doc/' . urlencode($image)) }}"
                                                    data-field="others" 
                                                    title="Download">
                                                        {{ $image }}
                                                    </a><br>
                                                @endforeach
                                            @else
                                            <span>N/A</span>
                                            @endif
                                        </div>
                                        @else
                                        <textarea readonly name="just_Prop" id="just_Prop" class="form-control auto-resize-textarea"
                                            value="{{ $material_details != '' ? $material_details['just_Prop'] : '' }}"
                                            placeholder=" Enter ustification of Proposal">N/A</textarea>
                                        <input type="file" class="form-control" name="just_prop_upload[]" multiple>

                                        @endif
                                    </div>
                                </div>


                                <div class="col-12">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h6 class="accordion-header" type="button" data-toggle="collapse"
                                                data-target="#collapseFive" aria-expanded="true"
                                                aria-controls="collapseFive">
                                                Past 3 Years Actual Cost Trend For Material (in Rs / Lakh / Crore, etc.)</h6>

                                            <div id="collapseFive" class="collapse show" aria-labelledby="headingOne"
                                                data-parent="#accordionExample">
                                                <div class="card-body">
                                                    <div class="row after-add-more">
                                                        @php
                                                        $year1 = explode(',', $material_details != '' ? $material_details->cost_trend_year1 : '');
                                                        $year2 = explode(',', $material_details != '' ? $material_details->cost_trend_year2 : '');
                                                        $year3 = explode(',', $material_details != '' ? $material_details->cost_trend_year3 : '');
                                                        $year = $nv_year->fiscal_year;
                                                        $yr_l = substr($year, 5);
                                                        $yr_f = substr($year, 0, 4);
                                                        @endphp



                                                        <div class="col-xl-3 col-lg-6 col-md-6 mt-1">

                                                            @if (!empty($material_details->cost_trend_year3))
                                                            <input type="text" class="form-control"
                                                                name="cost_trend_year3[]" id="cost_trend_year3"
                                                                value="{{ $yr_f - 1 }}-{{ $yr_l - 1 }}"
                                                                readonly>
                                                            @else
                                                            <input readonly type="text" class="form-control"
                                                                id="cost_trend_year3" name="cost_trend_year3[]"
                                                                placeholder="FY" value="N/A">
                                                            @endif

                                                        </div>
                                                        <div class="col-xl-3 col-lg-6 col-md-6 mt-1">
                                                            @if (!empty($material_details->cost_trend_year1))
                                                            <input readonly type="text" class="form-control"
                                                                id="cost_trend_year1" name="cost_trend_year1[]"
                                                                value="{{ isset($year1[0]) ? $year1[0] : '' }}"
                                                                placeholder="₹">
                                                            @else
                                                            <input readonly type="text" class="form-control"
                                                                name="cost_trend_year1[]" id="cost_trend_year1"
                                                                value="N/A">
                                                            @endif

                                                        </div>

                                                        <div class="col-xl-3 col-lg-6 col-md-6 mt-1">
                                                            @if (!empty($material_details->cost_trend_year2))
                                                            <input readonly type="text" class="form-control"
                                                                id="cost_trend_year2" name="cost_trend_year2[]"
                                                                placeholder="Material"
                                                                value="{{ isset($year2[0]) ? $year2[0] : '' }}">
                                                            @else
                                                            <input readonly type="text" class="form-control"
                                                                name="cost_trend_year2[]" id="cost_trend_year2"
                                                                value="N/A">
                                                            @endif

                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-xl-3 col-lg-6 col-md-6 mt-1">

                                                            @if (!empty($material_details->cost_trend_year3))
                                                            <input readonly type="text" class="form-control"
                                                                name="cost_trend_year3[]" id="cost_trend_year3"
                                                                value="{{ $yr_f - 2 }}-{{ $yr_l - 2 }}"
                                                                readonly>
                                                            @else
                                                            <input readonly type="text" class="form-control"
                                                                id="cost_trend_year3" name="cost_trend_year3[]"
                                                                placeholder="FY" value="N/A">
                                                            @endif

                                                        </div>
                                                        <div class="col-xl-3 col-lg-6 col-md-6 mt-1">
                                                            @if (!empty($material_details->cost_trend_year1))
                                                            <input readonly type="text" class="form-control"
                                                                id="cost_trend_year1" name="cost_trend_year1[]"
                                                                value="{{ isset($year1[1]) ? $year1[1] : '' }}"
                                                                placeholder="₹">
                                                            @else
                                                            <input readonly type="text" class="form-control"
                                                                name="cost_trend_year1[]" id="cost_trend_year1"
                                                                value="N/A">
                                                            @endif
                                                        </div>

                                                        <div class="col-xl-3 col-lg-6 col-md-6 mt-1">
                                                            @if (!empty($material_details->cost_trend_year2))
                                                            <input readonly type="text" class="form-control"
                                                                id="cost_trend_year2" name="cost_trend_year2[]"
                                                                placeholder="Material"
                                                                value="{{ isset($year2[1]) ? $year2[1] : '' }}">
                                                            @else
                                                            <input readonly type="text" class="form-control"
                                                                name="cost_trend_year2[]" id="cost_trend_year2"
                                                                value="N/A">
                                                            @endif

                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-xl-3 col-lg-6 col-md-6 mt-1">

                                                            @if (!empty($material_details->cost_trend_year3))
                                                            <input readonly type="text" class="form-control"
                                                                name="cost_trend_year3[]" id="cost_trend_year3"
                                                                value="{{ $yr_f - 3 }}-{{ $yr_l - 3 }}"
                                                                readonly>
                                                            @else
                                                            <input readonly type="text" class="form-control"
                                                                id="cost_trend_year3" name="cost_trend_year3[]"
                                                                placeholder="FY" value="N/A">
                                                            @endif

                                                        </div>
                                                        <div class="col-xl-3 col-lg-6 col-md-6 mt-1">
                                                            @if (!empty($material_details->cost_trend_year1))
                                                            <input readonly type="text" class="form-control"
                                                                id="cost_trend_year1" name="cost_trend_year1[]"
                                                                value="{{ isset($year1[2]) ? $year1[2] : '' }}"
                                                                placeholder="₹">
                                                            @else
                                                            <input readonly type="text" class="form-control"
                                                                name="cost_trend_year1[]" id="cost_trend_year1"
                                                                value="N/A">
                                                            @endif
                                                        </div>

                                                        <div class="col-xl-3 col-lg-6 col-md-6 mt-1">
                                                            @if (!empty($material_details->cost_trend_year2))
                                                            <input readonly type="text" class="form-control"
                                                                id="cost_trend_year2" name="cost_trend_year2[]"
                                                                placeholder="Material"
                                                                value="{{ isset($year2[2]) ? $year2[2] : '' }}">
                                                            @else
                                                            <input readonly type="text" class="form-control"
                                                                name="cost_trend_year2[]" id="cost_trend_year2"
                                                                value="N/A">
                                                            @endif

                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                @php
                                $selectedYear = $material_details != '' ? $material_details->implements_years : '';
                                @endphp
                                @if(!empty($selectedYear))

                                <div class="col-12">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseimple" aria-expanded="true" aria-controls="collapseimple">
                                                Implementation Period
                                            </h6>

                                            <div id="collapseimple" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                <div class="card-body">

                                                    @php
                                                    $selectedYear = $material_details != '' ? $material_details->implements_years : '';
                                                    $from = explode(',', $material_details != '' ? $material_details->imp_from : '');
                                                    $to = explode(',', $material_details != '' ? $material_details->imp_to : '');
                                                    $service = explode('.,', $material_details != '' ? $material_details->imp_plan : '');
                                                    @endphp
                                                    <input type="hidden" id="selectedYear" value="{{ $selectedYear }}">
                                                    <div class="row after-add-more-third">
                                                        <div class="col-12">
                                                            <div class="row" style="margin-top:-15px;">
                                                                <div class="col-12">
                                                                    <label for="exampleFormControlSelect">Select Year</label>
                                                                    {{-- ()" --}}
                                                                    <select class="form-control" id="exampleFormControlSelect" name="implements_years" onchange="editDuplicateColumns()" style="display: inline-block; width:200px;" disabled>
                                                                        <option value="">Select Year</option>
                                                                        <option value="1" {{ $selectedYear == '1' ? 'selected' : '' }}>1</option>
                                                                        <option value="2" {{ $selectedYear == '2' ? 'selected' : '' }}>2</option>
                                                                        <option value="3" {{ $selectedYear == '3' ? 'selected' : '' }}>3</option>
                                                                    </select>
                                                                </div>

                                                                @for ($i = 0; $i < $selectedYear; $i++)
                                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                    <div class="form-group">
                                                                        <label for="exampleFormControlInput1">Implementation
                                                                            Period From</label>
                                                                        <input type="date" class="form-control" name="imp_from[]" id="imp_from" value="{{ isset($from[$i]) ? $from[$i] : '' }}" placeholder="Enter Benefit" disabled>
                                                                    </div>
                                                            </div>

                                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                <div class="form-group">
                                                                    <label for="exampleFormControlInput1">Implementation
                                                                        Period To</label>
                                                                    <input type="date" class="form-control" name="imp_to[]" id="imp_to" value="{{ isset($to[$i]) ? $to[$i] : '' }}" placeholder="Enter Benefit" disabled>
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label for="exampleFormControlInput1">Implementation
                                                                        Plan Year Wise (Max 2500 Characters)</label>
                                                                    <textarea name="imp_plan[]" id="imp_plan" cols="2" rows="10" class="form-control" placeholder="Enter Implementation Plan Year Wise" readonly>{{ isset($service[$i]) ? $service[$i] : '' }}</textarea>
                                                                </div>
                                                            </div>
                                                            @endfor

                                                        </div>

                                                        <div class="row">
                                                            <div class="col-12">
                                                                {{-- Here Ashu --}}
                                                                <div id="" class="row">
                                                                    <!-- Original columns -->
                                                                    <div class="original-columns row ">
                                                                        <!-- @for ($i = 0; $i < $selectedYear; $i++) 
                                                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                                <div class="form-group">
                                                                                    <label for="exampleFormControlInput1">Implementation
                                                                                        Period From {{$selectedYear}}</label>
                                                                                    <input type="date" class="form-control" name="imp_from[]" id="imp_from" value="{{ isset($from[$i]) ? $from[$i] : '' }}" placeholder="Enter Benefit" disabled>
                                                                                </div>
                                                                            </div>

                                                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                            <div class="form-group">
                                                                                <label for="exampleFormControlInput1">Implementation
                                                                                    Period To</label>
                                                                                <input type="date" class="form-control" name="imp_to[]" id="imp_to" value="{{ isset($to[$i]) ? $to[$i] : '' }}" placeholder="Enter Benefit"disabled>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12 mb-2">
                                                                            <div class="form-group">
                                                                                <label for="exampleFormControlInput1">Implementation
                                                                                    Plan Year Wise (Max 2500 Characters)</label>
                                                                                <textarea name="imp_plan[]" id="imp_plan" cols="2" rows="10" class="form-control" placeholder="Enter Implementation Plan Year Wise" readonly>{{ isset($service[$i]) ? $service[$i] : '' }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                        @endfor -->
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



                                <div class="col-12">

                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Benefit (Max 2500
                                            Characters)</label>
                                        @if (!empty($material_details->benefit))
                                        <textarea readonly name="benefit" id="benefit" class="form-control auto-resize-textarea"
                                            value="{{ $material_details != '' ? $material_details['benefit'] : '' }}" placeholder=" Enter Benefit">{{ $material_details != '' ? $material_details['benefit'] : '' }}</textarea>
                                        @else
                                        <textarea readonly name="benefit" id="benefit" class="form-control auto-resize-textarea"
                                            value="{{ $material_details != '' ? $material_details['benefit'] : '' }}" placeholder=" Enter Benefit">N/A</textarea>
                                        @endif
                                    </div>
                                </div>



                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Nature of Work</label> <br>
                                        <select class="form-control" id="worktype" name="worktype" disabled>
                                            <option value="" disabled>Select Type Nature of Work</option>
                                            <option value="General" {{ ($material_details != '' ? $material_details->worktype : '') == 'General' ? 'selected' : '' }}>
                                                General</option>
                                            <option value="Specific" {{ ($material_details != '' ? $material_details->worktype : '') == 'Specific' ? 'selected' : '' }}>
                                                Specific</option>
                                            <option value="Emergency" {{ ($material_details != '' ? $material_details->worktype : '') == 'Emergency' ? 'selected' : '' }}>
                                                Emergency</option>
                                        </select>
                                    </div>
                                    <sup style="color:rgb(3, 142, 220) ">(Please
                                        choose "specific" if the entire NV is being raised for one particular work
                                        or Scheme.)</sup>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Mode of
                                            Award</label>
                                        <select class="form-control" id="mode_award" name="mode_award" disabled>
                                            <option value="">Select Mode of Award</option>
                                            <option value="Tender" {{ ($material_details != '' ? $material_details->mode_award : '') == 'Tender' ? 'selected' : '' }}>
                                                Tender</option>
                                            <!-- <option value="FO" {{ ($material_details != '' ? $material_details->mode_award : '') == 'FO' ? 'selected' : '' }}>
                                                                                                            FO
                                                                                                        </option> -->
                                            <option value=" Single Vendor" {{ ($material_details != '' ? $material_details->mode_award : '') == 'Single Vendor' ? 'selected' : '' }}>
                                                Single Vendor
                                            </option>
                                            <option value="Turnkey" {{ ($material_details != '' ? $material_details->mode_award : '') == 'Turnkey' ? 'selected' : '' }}>
                                                Turnkey</option>
                                            <!-- <option value="In House" {{ ($material_details != '' ? $material_details->mode_award : '') == 'In House' ? 'selected' : '' }}>
                                                                                                            In House</option> -->
                                            <option value="Extension" {{ ($material_details != '' ? $material_details->mode_award : '') == 'Extension' ? 'selected' : '' }}>
                                                Extension</option>
                                            <option value="Others" {{ ($material_details != '' ? $material_details->mode_award : '') == 'Others' ? 'selected' : '' }}>
                                                Others</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                                                Scheme Details </h6>

                                            <div id="collapseThree" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                <div class="card-body">

                                                    <div class="row after-add-more-scheme">
                                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12" style="margin-top:-20px">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlSelect1">Scheme Type /
                                                                    Category</label>
                                                                <select class="form-control" id="scheme_type"
                                                                    name="scheme_type" disabled>
                                                                    <option value="">Select Category</option>
                                                                    <option value="Load Growth"
                                                                        {{ ($material_details != '' ? $material_details->scheme_type : '') == 'Load Growth' ? 'selected' : '' }}>
                                                                        Load Growth</option>
                                                                    <option value="System Improvement"
                                                                        {{ ($material_details != '' ? $material_details->scheme_type : '') == 'System Improvement' ? 'selected' : '' }}>
                                                                        System Improvement</option>
                                                                    <option value="Technology"
                                                                        {{ ($material_details != '' ? $material_details->scheme_type : '') == 'Technology' ? 'selected' : '' }}>
                                                                        Technology</option>
                                                                    <option value="Statutory"
                                                                        {{ ($material_details != '' ? $material_details->scheme_type : '') == 'Statutory' ? 'selected' : '' }}>
                                                                        Statutory</option>
                                                                    <option value="Infrastructure"
                                                                        {{ ($material_details != '' ? $material_details->scheme_type : '') == 'Infrastructure' ? 'selected' : '' }}>
                                                                        Infrastructure</option>
                                                                    <option value="Deposit"
                                                                        {{ ($material_details != '' ? $material_details->scheme_type : '') == 'Deposit' ? 'selected' : '' }}>
                                                                        Deposit</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        @php

                                                        $sch_no = explode(',', $material_details != '' ? $material_details->scheme_no : '');
                                                        $sch_des = explode(',', $material_details != '' ? $material_details->scheme_des : '');
                                                        @endphp
                                                        @foreach ($sch_no as $key => $sch_noo)
                                                        <div
                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12" style="margin-top:-20px">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Scheme No</label>
                                                                <input type="text" class="form-control"
                                                                    id="scheme_no"
                                                                    name="scheme_no[]" placeholder="Enter Scheme No"
                                                                    value="{{ $material_details != '' ? $sch_noo : '' }}"
                                                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-12" style="margin-top:-12px">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Scheme
                                                                    Description </label>

                                                                <textarea name="scheme_des[]" id="scheme_des" cols="2" rows="10" class="form-control" value="{{ $material_details != '' ? $sch_des[$key] : '' }}" placeholder=" Enter Scheme Description" disabled>{{ $material_details != '' ? $sch_des[$key] : '' }}</textarea>

                                                            </div>
                                                        </div>
                                                        @if ($key == 0)
                                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group change">
                                                                <a class="btn btn-alert add-more-scheme" style="pointer-events: none">+ Add
                                                                    More</a>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 my-class-scheme">
                                                        </div>
                                                        @else
                                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group ">
                                                                <label for="">&nbsp;</label><a class="btn btn-success remove" style="pointer-events: none">-
                                                                    Remove</a>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    @endforeach




                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    @if($nv->budget_type == 'CAPEX')
                                    <div class="col-12">
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
                                                                    @if (!empty($material_details->prop_number))
                                                                    <input readonly type="text"
                                                                        class="form-control" id="prop_number"
                                                                        name="prop_number"
                                                                        value="{{ $material_details != '' ? $material_details->prop_number : '' }}"
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
                                                                    @if (!empty($material_details->derc_ref_no))
                                                                    <input readonly type="text"
                                                                        class="form-control" id="derc_ref_no"
                                                                        name="derc_ref_no"
                                                                        value="{{ $material_details != '' ? $material_details->derc_ref_no : '' }}"
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
                                                                    @if (!empty($material_details->derc_approval))
                                                                    <input readonly type="text"
                                                                        class="form-control" id="derc_approval"
                                                                        name="derc_approval"
                                                                        value="{{ $material_details->derc_approval }}"
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
                                                                    @if (!empty($material_details->derc_app_date))
                                                                    <input readonly type="date"
                                                                        class="form-control" name="derc_app_date"
                                                                        id="derc_app_date"
                                                                        value="{{ $material_details != '' ? $material_details->derc_app_date : '' }}">
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
                                    <div class="col-12">

                                        <div class="accordion" id="accordionExample">
                                            <div class="accordion-item">
                                                <h6 class="accordion-header" type="button" data-toggle="collapse"
                                                    data-target="#collapseTwo" aria-expanded="true"
                                                    aria-controls="collapseTwo">
                                                    Material BOQ</h6>

                                                <div id="collapseTwo" class="collapse show"
                                                    aria-labelledby="headingTwo" data-parent="#accordionExample">
                                                    <div class="card-body">
                                                        <form enctype="multipart/form-data" method="post"
                                                            id="materialboqform">
                                                            <input type="hidden" class="form-control" name="nv_id"
                                                                id="nv_id" value="{{ request()->segment(4) }}">
                                                            <div class="row after-add-more-second">
                                                                @php
                                                                $m1 = explode(',', $material_details != '' ? $material_details->material_code : '');
                                                                $m2 = explode(',', $material_details != '' ? $material_details->mat_des : '');
                                                                $m3 = explode(',', $material_details != '' ? $material_details->mat_group : '');
                                                                $m4 = explode(',', $material_details != '' ? $material_details->uom : '');
                                                                $m5 = explode(',', $material_details != '' ? $material_details->rate : '');
                                                                $m6 = explode(',', $material_details != '' ? $material_details->quantity : '');
                                                                $m7 = explode(',', $material_details != '' ? $material_details->total_amount : '');
                                                                $m8 = explode(',', $material_details != '' ? $material_details->delivery_schedule : '');

                                                                @endphp

                                                            </div>

                                                            <?php
                                                            $total = 0;
                                                            $m_importAmount = 0;
                                                            $totalvalue = $total;
                                                            ?>

                                                            @foreach ($material_import as $key => $m_import)
                                                            <?php
                                                            $m_importAmount =  $m_import->amount;
                                                            ?>

                                                            @if (!empty($m_importAmount))
                                                            <?php

                                                            $total = $total + $m_importAmount;

                                                            ?>
                                                            @else
                                                            <?php
                                                            $total = $total;
                                                            ?>
                                                            @endif
                                                            @endforeach


                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="card">

                                                                        <div class="card-body table-responsive">
                                                                            <div style="margin-top:-20px;">

                                                                                <div class="btn-group-toggle"
                                                                                    data-toggle="buttons">
                                                                                    <label
                                                                                        class="btn btn-sm btn-info toggle-btn"
                                                                                        data-option="expand">
                                                                                        <input type="radio"
                                                                                            name="expand-collapse-option"
                                                                                            autocomplete="off"> Expand
                                                                                    </label>
                                                                                    <label
                                                                                        class="btn btn-sm btn-info toggle-btn"
                                                                                        data-option="collapse">
                                                                                        <input type="radio"
                                                                                            name="expand-collapse-option"
                                                                                            autocomplete="off">
                                                                                        Collapse
                                                                                    </label>
                                                                                </div>

                                                                            </div>

                                                                            <table id="nv_datatable1"
                                                                                class="table table-bordered">
                                                                                <input type="hidden"
                                                                                    class="form-control"
                                                                                    name="nv_id" id="nv_id"
                                                                                    value="{{ request()->segment(4) }}">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th class="text-center"
                                                                                            width="5%">S.No</th>
                                                                                        <th class="text-center"
                                                                                            width="5%">NV Id</th>
                                                                                        <th>Material Code</th>
                                                                                        <th>Description</th>
                                                                                        <th width="15%">UOM</th>
                                                                                        <th width="10%">Rate</th>
                                                                                        <th width="15%">Qty</th>
                                                                                        <th width="15%">Amount (Rs.)
                                                                                        </th>
                                                                                        <th>Rate reference
                                                                                        </th>
                                                                                        <th>Fiscal Year
                                                                                        </th>
                                                                                        <th width="15%">April</th>
                                                                                        <th width="15%">May</th>
                                                                                        <th width="15%">June</th>
                                                                                        <th width="15%">July</th>
                                                                                        <th width="15%">Aug</th>
                                                                                        <th width="15%">Sept
                                                                                        </th>
                                                                                        <th width="15%">Oct</th>
                                                                                        <th width="15%">Nov</th>
                                                                                        <th width="15%">Dec</th>
                                                                                        <th width="15%">Jan</th>
                                                                                        <th width="15%">Feb</th>
                                                                                        <th width="15%">Mar</th>
                                                                                        <th width="15%"
                                                                                            class="expandable">Action
                                                                                        </th>
                                                                                    </tr>
                                                                                </thead>
                                                                            </table>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($nv->budgetary_provision === 'Approved')
                                        <div class="row">
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="exampleFormControlInput1">Total Material Amount</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Budget Available" name=""
                                                        id="total_mat" value="{{ indian_number_format($total) ?? null }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            @else
                                            <div class="row">
                                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">Total Material Amount</label>
                                                        <input type="text" class="form-control"
                                                            placeholder="Budget Available" name=""
                                                            id="total_mat_mat" value="{{ indian_number_format($total) ?? null }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-2 col-md-2">
                                                    <label>Tax (In %)</label>
                                                    @if (!empty($material_details->tax1))
                                                    <input readonly type="text" class="form-control"
                                                        id="tax1" name="tax1"
                                                        value="{{ $material_details->tax1 }}">
                                                    @else
                                                    <input readonly type="text" class="form-control"
                                                        id="tax1" name="tax1"
                                                        value="N/A">
                                                    @endif
                                                </div>
                                                @endif
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        @if ($nv->budgetary_provision === 'Approved')
                                                        <label for="exampleFormControlInput1">Budget Available <span
                                                                class="mandatory_input">*</span></label>
                                                        @if (!empty($material_details->budget_avl))
                                                        <input readonly type="text" class="form-control"
                                                            placeholder="Budget Available" name="budget_avl"
                                                            id="budget_avl"
                                                            value="{{ $material_details != '' ? indian_number_format($material_details->budget_avl) : '' }}">
                                                        @else
                                                        <input readonly type="text" class="form-control"
                                                            placeholder="Budget Available" name="budget_avl"
                                                            id="budget_avl" value="N/A">
                                                        @endif
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                            @php

                                            if(!empty($selectedYear)){

                                            $mat_imp = App\Models\MateriBOQBulk::where('nv_id', $nv->id)->where('material_id', $material_details->id)->orderBy('id', 'desc')->get();

                                            $totalAmt1 = 0;
                                            $totalAmt2 = 0;
                                            $totalAmt3 = 0;
                                            $m_impAmount = 0;
                                            $totalvalue = $totalAmt1;
                                            foreach ($mat_imp as $key => $m_imp){
                                            $rate = $m_imp->rate;
                                            $months = [
                                            $m_imp->april1, $m_imp->may1, $m_imp->june1, $m_imp->july1, $m_imp->august1, $m_imp->september1,
                                            $m_imp->oct1, $m_imp->nov1, $m_imp->dec1, $m_imp->jan1, $m_imp->feb1, $m_imp->march1
                                            ];
                                            if($selectedYear == 2 || $selectedYear == 3){
                                            $months2 = [
                                            $m_imp->april2, $m_imp->may2, $m_imp->june2, $m_imp->july2, $m_imp->august2, $m_imp->september2,
                                            $m_imp->oct2, $m_imp->nov2, $m_imp->dec2, $m_imp->jan2, $m_imp->feb2, $m_imp->march2
                                            ];
                                            foreach ($months2 as $value2) {

                                            $totalAmt2 += $value2*$rate;
                                            }
                                            }
                                            if($selectedYear == 3){
                                            $months3 = [
                                            $m_imp->april3, $m_imp->may3, $m_imp->june3, $m_imp->july3, $m_imp->august3, $m_imp->september3,
                                            $m_imp->oct3, $m_imp->nov3, $m_imp->dec3, $m_imp->jan3, $m_imp->feb3, $m_imp->march3
                                            ];

                                            foreach ($months3 as $value3) {

                                            $totalAmt3 += $value3*$rate;
                                            }
                                            }
                                            if(!empty($months)){
                                            foreach ($months as $value) {

                                            $totalAmt1 += $value*$rate;
                                            }
                                            }else{
                                            $totalAmt1 = 0;
                                            }
                                            }
                                            $date = new DateTime($from[0]);
                                            $Impyear = $date->format('Y');
                                            $Imp1 = $Impyear.'-'.($Impyear+1);
                                            $Imp2 = ($Impyear+1).'-'.($Impyear+2);
                                            $Imp3 = ($Impyear+2).'-'.($Impyear+3);

                                            }else{
                                            $totalAmt1 = $total;
                                            $Imp1 = $nv->fiscal_year;
                                            }




                                            @endphp
                                            @if ($nv->budgetary_provision === 'Approved')

                                            <div class="col-12 mb-0">
                                                <div class="accordion" id="accordionExample">
                                                    <div class="accordion-item">
                                                        <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                            Provisional Years Amount For Material BOQ
                                                        </h6>

                                                        <div id="collapseOne" class="collapse show" aria-labelledby="collapseOne" data-parent="#accordionExample">
                                                            <div class="card-body">
                                                                <div class="after-add-more">
                                                                    <div class="row">
                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="text" class="form-control" value="{{$Imp1}}" readonly>
                                                                        </div>

                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="text" class="form-control" placeholder="Total Amount" name="total_mat_mat" id="total_mat_mat" value="{{ indian_number_format($totalAmt1) ?? null }}" readonly>
                                                                        </div>

                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            @if (!empty($material_details->tax1))
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax1" name="tax1"
                                                                                value="{{ $material_details->tax1 }}">
                                                                            @else
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax1" name="tax1"
                                                                                value="N/A">
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    @if($selectedYear == 2 || $selectedYear == 3)
                                                                    <br>

                                                                    <div class="row">
                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="text" class="form-control" value="{{$Imp2}}" readonly>
                                                                        </div>

                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="text" class="form-control" placeholder="Total Amount" name="total_mat_mat2" id="total_mat_mat2" value="{{ indian_number_format($totalAmt2) ?? null }}" readonly>
                                                                        </div>

                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            @if (!empty($material_details->tax_amount2))
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax_amount2" name="tax_amount2"
                                                                                value="{{ $material_details->tax_amount2 }}">
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
                                                                            <input type="text" class="form-control" value="{{$Imp3}}" readonly>
                                                                        </div>

                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="text" class="form-control" placeholder="Total Amount" name="total_mat_mat3" id="total_mat_mat3" value="{{ indian_number_format($totalAmt3) ?? null }}" readonly>
                                                                        </div>

                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            @if (!empty($material_details->tax_amount3))
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax_amount3" name="tax_amount3"
                                                                                value="{{ $material_details->tax_amount3 }}">
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
                                                <!-- <div class="row" >
                                                 <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-0">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">
                                                                      FY {{$Imp1}} Amount</label> 
                                                           <input type="text" class="form-control" placeholder="Total Amount" name="total_mat_mat" id="total_mat_mat" value="{{ $totalAmt1 ?? null }}" readonly>


                                                            </div>
                                                        </div>

                                                        <div class="col-xl-2 col-lg-2 col-md-2">
                                                      <label>Tax (In %)</label>
                                                      @if (!empty($material_details->tax1))
                                                          <input readonly type="text" class="form-control"
                                                              id="tax1" name="tax1"
                                                              value="{{ $material_details->tax1 }}">
                                                      @else
                                                          <input readonly type="text" class="form-control"
                                                              id="tax1" name="tax1"
                                                              value="N/A">
                                                      @endif
                                                  </div>
                                                        <div class="col-xl-6 col-lg-6 col-md-6">
                                                        </div>
                                                   
                                                        @if($selectedYear == 2 || $selectedYear == 3)
                                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-0">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">
                                                                      FY {{$Imp2}} Amount</label> 
                                                           <input type="text" class="form-control" placeholder="Total Amount" name="total_mat_mat2" id="total_mat_mat2" value="{{ $totalAmt2 ?? null }}" readonly>


                                                            </div>
                                                        </div>

                                                        <div class="col-xl-2 col-lg-2 col-md-2">
                                                      <label>Tax (In %)</label>
                                                      @if (!empty($material_details->tax_amount2))
                                                          <input readonly type="text" class="form-control"
                                                              id="tax_amount2" name="tax_amount2"
                                                              value="{{ $material_details->tax_amount2 }}">
                                                      @else
                                                          <input readonly type="text" class="form-control"
                                                              id="tax_amount2" name="tax_amount2"
                                                              value="N/A">
                                                      @endif
                                                  </div>
                                                        <div class="col-xl-6 col-lg-6 col-md-6">
                                                        </div>
                                                        @endif
                                                        @if($selectedYear == 3)
                                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-0">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">
                                                                      FY {{$Imp3}} Amount</label> 
                                                           <input type="text" class="form-control" placeholder="Total Amount" name="total_mat_mat3" id="total_mat_mat3" value="{{ $totalAmt3 ?? null }}" readonly>


                                                            </div>
                                                        </div>

                                                        <div class="col-xl-2 col-lg-2 col-md-2">
                                                      <label>Tax (In %)</label>
                                                      @if (!empty($material_details->tax_amount3))
                                                          <input readonly type="text" class="form-control"
                                                              id="tax_amount3" name="tax_amount3"
                                                              value="{{ $material_details->tax_amount3 }}">
                                                      @else
                                                          <input readonly type="text" class="form-control"
                                                              id="tax_amount3" name="tax_amount3"
                                                              value="N/A">
                                                      @endif
                                                  </div>
                                                        <div class="col-xl-6 col-lg-6 col-md-6">
                                                        </div>
                                                        @endif
                                                        </div> -->
                                                @endif


                                            </div>
                                        </div>


                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <div class="card-body">
                                                    <div class="row after-add-more-scheme-material">
                                                        @php
                                                        // echo $material_details->material_amount; exit();
                                                        $material_amount = explode(',', $material_details != '' ? $material_details->material_amount : '0');
                                                        $material_descripition = explode(',', $material_details != '' ? $material_details->material_description : '');
                                                        // print_r($material_descripition);
                                                        // exit;
                                                        $ttal = 0;
                                                        @endphp
                                                        {{-- @dd($material_descripition) --}}
                                                        <input type="hidden" name="" id="matamount" value="">
                                                        @foreach ($material_amount as $key => $material_amt)
                                                        @php
                                                        if(!empty($material_amt)){
                                                        $ttal += $material_amt;
                                                        } else{
                                                        $material_amt=0;
                                                        $ttal+=$material_amt;
                                                        }
                                                        @endphp
                                                        <input type="hidden" name="" value="">
                                                        <div
                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group">
                                                                <input type="hidden" name="sum" id="sum2" value="0">
                                                                <input type="text" class="form-control" name="material_amount[]" id="material_amount"
                                                                    placeholder="Amount" value="{{ $material_details != '' ? indian_number_format($material_amt) : '' }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control"
                                                                    id="material_descripition" name="material_description[]"
                                                                    placeholder="Description" value="{{@$material_descripition[$key]}}" readonly>
                                                            </div>
                                                        </div>
                                                        @if ($key == 0)
                                                        <div
                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group change">

                                                            </div>
                                                        </div>
                                                        <div class="col-12 my-class-scheme_material">
                                                        </div>
                                                        @else
                                                        <div
                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group ">
                                                                <label for="">&nbsp;</label>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @endforeach
                                                        {{-- @dd($ttal); --}}
                                                        <input type="hidden" name="" id="mtamt" value="{{$ttal}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Rate Reference -->
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
                                                                            @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->cm_rate_ref))
                                                                            <a class="ml-2 download-all"
                                                                                id="cm_rate_ref-{{ $material_doc->id }}"
                                                                                href="#"
                                                                                data-file-type="cm_rate_ref"
                                                                                data-files="{{ $material_doc->cm_rate_ref }}">
                                                                                <i class="fa fa-download" title="Download All"></i>
                                                                            </a>
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
                                                                            @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->vend_quatation))
                                                                            <a class="ml-2 download-all"
                                                                                id="vend_quatation-{{ $material_doc->id }}"
                                                                                href="#"
                                                                                data-file-type="vend_quatation"
                                                                                data-files="{{ $material_doc->vend_quatation }}">
                                                                                <i class="fa fa-download" title="Download All"></i>
                                                                            </a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input readonly type="file"
                                                                            class="form-control"
                                                                            name="vend_quatation"
                                                                            id="vend_quatation">
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleFormControlInput1">Last
                                                                            Purchase
                                                                            Price :
                                                                            @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->last_purchase_price))
                                                                            <a class="ml-2 download-all"
                                                                                id="last_purchase_price-{{ $material_doc->id }}"
                                                                                href="#"
                                                                                data-file-type="last_purchase_price"
                                                                                data-files="{{ $material_doc->last_purchase_price }}">
                                                                                <i class="fa fa-download" title="Download All"></i>
                                                                            </a>
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
                                                                            @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->user_estimation))
                                                                            <a class="ml-2 download-all"
                                                                                id="user_estimation-{{ $material_doc->id }}"
                                                                                href="#"
                                                                                data-file-type="user_estimation"
                                                                                data-files="{{ $material_doc->user_estimation }}">
                                                                                <i class="fa fa-download" title="Download All"></i>
                                                                            </a>
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
                                                                            @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->previous_wo_rc))
                                                                            <a class="ml-2 download-all"
                                                                                id="previous_wo_rc-{{ $material_doc->id }}"
                                                                                href="#"
                                                                                data-file-type="previous_wo_rc"
                                                                                data-files="{{ $material_doc->previous_wo_rc }}">
                                                                                <i class="fa fa-download" title="Download All"></i>
                                                                            </a>
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
                                        @if ($nv->budget_type === 'CAPEX')
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div id="myRadioGroup">

                                                    <label for="exampleFormControlInput1">Is there any Capacity
                                                        addition</label> <br>
                                                    @if (!empty($material_details->cap_add))
                                                    @if ($material_details->cap_add == 4)
                                                    Yes &nbsp; <input readonly type="radio"
                                                        id="chkYes" name="cars-first"
                                                        value="4"
                                                        {{ ($material_details != '' ? $material_details['cap_add'] : '') == '4' ? 'checked' : '' }} />&nbsp;
                                                    @else
                                                    No &nbsp;<input readonly type="radio"
                                                        name="cars-first" value="5"
                                                        {{ ($material_details != '' ? $material_details['cap_add'] : '') == '5' ? 'checked' : '' }} />
                                                    @endif
                                                    @endif

                                                    <br>
                                                    <br>

                                                    <div id="First4" class="first-radio">
                                                        <div class="row">
                                                            <div class="col-xl-6 col-lg-6">
                                                                <div class="form-group">
                                                                    <label for="exampleFormControlInput1">PTR
                                                                        MVA</label>
                                                                    @if (!empty($material_details->ptr_mva))
                                                                    <input readonly type="text"
                                                                        class="form-control"
                                                                        name="ptr_mva" id="ptr_mva"
                                                                        placeholder="Enter PTR MVA"
                                                                        value="{{ $material_details != '' ? $material_details['ptr_mva'] : '' }}">
                                                                    @else
                                                                    <input readonly type="text"
                                                                        class="form-control"
                                                                        name="ptr_mva" id="ptr_mva"
                                                                        placeholder="Enter PTR MVA"
                                                                        value="N/A">
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-xl-6 col-lg-6">
                                                                <div class="form-group">
                                                                    <label for="exampleFormControlInput1">DT
                                                                        MVA</label>
                                                                    @if (!empty($material_details->dt_mva))
                                                                    <input readonly type="text"
                                                                        class="form-control"
                                                                        name="dt_mva" id="dt_mva"
                                                                        placeholder="Enter DT MVA"
                                                                        value="{{ $material_details != '' ? $material_details['dt_mva'] : '' }}">
                                                                    @else
                                                                    <input readonly type="text"
                                                                        class="form-control"
                                                                        name="dt_mva" id="dt_mva"
                                                                        placeholder="Enter DT MVA"
                                                                        value="N/A">
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-xl-6 col-lg-6 mt-2 ">
                                                                <div class="form-group">
                                                                    <label
                                                                        style="display:block;" for="exampleFormControlInput1">EHV
                                                                        Line(Ckt.km)</label>
                                                                    @if (!empty($material_details->ehv_line))
                                                                    <input readonly type="text"
                                                                        class="form-control"
                                                                        name="ehv_line" id="ehv_line"
                                                                        placeholder="Enter EHV Line(Ckt.km)"
                                                                        value="{{ $material_details != '' ? $material_details['ehv_line'] : '' }}">
                                                                    @else
                                                                    <input readonly type="text"
                                                                        class="form-control"
                                                                        name="ehv_line" id="ehv_line"
                                                                        placeholder="Enter EHV Line(Ckt.km)"
                                                                        value="N/A">
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-xl-6 col-lg-6">
                                                                <div class="form-group">
                                                                    <label for="exampleFormControlInput1">HT
                                                                        Line(Ckt.km)</label>
                                                                    @if (!empty($material_details->ht_line))
                                                                    <input readonly type="text"
                                                                        class="form-control"
                                                                        name="ht_line" id="ht_line"
                                                                        placeholder="Enter HT Line(Ckt.km)"
                                                                        value="{{ $material_details != '' ? $material_details['ht_line'] : '' }}">
                                                                    @else
                                                                    <input readonly type="text"
                                                                        class="form-control"
                                                                        name="ht_line" id="ht_line"
                                                                        placeholder="Enter HT Line(Ckt.km)"
                                                                        value="N/A">
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-xl-6 col-lg-6">
                                                                <div class="form-group">
                                                                    <label for="exampleFormControlInput1">LT
                                                                        Line(Ckt.km)</label>
                                                                    @if (!empty($material_details->lt_line))
                                                                    <input readonly type="text"
                                                                        class="form-control"
                                                                        name="lt_line" id="lt_line"
                                                                        placeholder="Enter LT Line(Ckt.km)"
                                                                        value="{{ $material_details != '' ? $material_details['lt_line'] : '' }}">
                                                                    @else
                                                                    <input readonly type="text"
                                                                        class="form-control"
                                                                        name="lt_line" id="lt_line"
                                                                        placeholder="Enter LT Line(Ckt.km)"
                                                                        value="N/A">
                                                                    @endif
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>


                                                    <div id="First5" class="first-radio"
                                                        style="display: none;">

                                                    </div>

                                                </div>
                                                {{-- <label for="exampleFormControlInput1">Is there any Capacity
                                            addition</label>
                                        <br>
                                        <input type="radio" id="yes" name="fav_language" value="HTML">
                                        <label for="yes">Yes</label>
                                        <input type="radio" id="no" name="fav_language" value="CSS">
                                        <label for="no">No</label><br> --}}


                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12" style="margin-top:-20px;">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1"
                                                    title="Material Trail Details and Schudule of Pilot feedback Submission">New
                                                    Product (Max 200 Characters):
                                                    @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->new_product))
                                                    <a class="ml-2 download-all"
                                                        id="new_product-{{ $material_doc->id }}"
                                                        href="#"
                                                        data-file-type="new_product"
                                                        data-files="{{ $material_doc->new_product }}">
                                                        <i class="fa fa-download" title="Download All"></i>
                                                    </a>
                                                    @else
                                                    <spam> N/A </spam>
                                                    @endif
                                                </label>
                                                <input readonly type="file" class="form-control"
                                                    name="new_product" id="new_product"
                                                    placeholder="New Product">
                                            </div>
                                        </div>
                                        <div class="col-12 ">
                                            @if (!empty($material_details->new_product_text))
                                            <textarea readonly name="new_product_text" id="new_product_text"
                                                value="{{ $material_details != '' ? $material_details['new_product_text'] : '' }}" class="form-control auto-resize-textarea"
                                                placeholder="New Product">{{ $material_details['new_product_text'] }}</textarea>
                                            @else
                                            <textarea readonly name="new_product_text" id="new_product_text"
                                                value="{{ $material_details != '' ? $material_details['new_product_text'] : '' }}" class="form-control auto-resize-textarea"
                                                placeholder="New Product">N/A</textarea>
                                            @endif
                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mt-3" style="margin-top:-20px;">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1"
                                                    title="Material Trail Details and Schudule of Pilot feedback Submission">Quantity Justification (Max 200 Characters):
                                                    @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->quant_just))
                                                    <a class="ml-2 download-all"
                                                        id="quant_just-{{ $material_doc->id }}"
                                                        href="#"
                                                        data-file-type="quant_just"
                                                        data-files="{{ $material_doc->quant_just }}">
                                                        <i class="fa fa-download" title="Download All"></i>
                                                    </a>
                                                    @else
                                                    <spam> N/A </spam>
                                                    @endif
                                                </label>
                                                <input readonly type="file" class="form-control"
                                                    name="quant_just" id="quant_just"
                                                    placeholder="New Product">
                                            </div>
                                        </div>
                                        <div class="col-12 ">
                                            @if (!empty($material_details->quant_just_text))
                                            <textarea readonly name="quant_just_text" id="quant_just_text"
                                                value="{{ $material_details != '' ? $material_details['quant_just_text'] : '' }}" class="form-control auto-resize-textarea"
                                                placeholder="New Product">{{ $material_details['quant_just_text'] }}</textarea>
                                            @else
                                            <textarea readonly name="quant_just_text" id="quant_just_text"
                                                value="{{ $material_details != '' ? $material_details['quant_just_text'] : '' }}" class="form-control auto-resize-textarea"
                                                placeholder="New Product">N/A</textarea>
                                            @endif
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Root Cause Analysis (Max
                                                    1000
                                                    Characters)</label>
                                                @if (!empty($material_details->root_cause_analysis))
                                                <textarea readonly name="root_cause_analysis" id="root_cause_analysis"
                                                    value="{{ $material_details != '' ? $material_details['root_cause_analysis'] : '' }}" class="form-control auto-resize-textarea"
                                                    placeholder="Root Cause Analysis">{{ $material_details['root_cause_analysis'] }}</textarea>
                                                @else
                                                <textarea readonly name="root_cause_analysis" id="root_cause_analysis"
                                                    value="{{ $material_details != '' ? $material_details['root_cause_analysis'] : '' }}" class="form-control auto-resize-textarea"
                                                    placeholder="Root Cause Analysis">N/A</textarea>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Cost Reduction plan/Future
                                                    phasing out
                                                    plan(if applicable) (Max 1000
                                                    Characters)</label>
                                                @if (!empty($material_details->cause_analysis))
                                                <textarea readonly name="cause_analysis" id="cause_analysis" value=""
                                                    class="form-control auto-resize-textarea" placeholder=" ">{{ $material_details['cause_analysis'] }}</textarea>
                                                @else
                                                <textarea readonly name="cause_analysis" id="cause_analysis" value=""
                                                    class="form-control auto-resize-textarea" placeholder=" ">N/A</textarea>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mt-3" style="margin-top:-20px;">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1"
                                                    title="Material Trail Details and Schudule of Pilot feedback Submission">Special Remarks / Any Specific Recommendation (Max 500 Characters):
                                                    @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->special_attch))
                                                    <a class="ml-2 download-all"
                                                        id="special_attch-{{ $material_doc->id }}"
                                                        href="#"
                                                        data-file-type="special_attch"
                                                        data-files="{{ $material_doc->special_attch }}">
                                                        <i class="fa fa-download" title="Download All"></i>
                                                    </a>
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
                                                @if (!empty($material_details->special_remarks))
                                                <textarea readonly name="special_remarks" id="special_remarks" value=""
                                                    class="form-control auto-resize-textarea" placeholder="Special Remarks such as any specific recommendation">{{ $material_details['special_remarks'] }}</textarea>
                                                @else
                                                <textarea readonly name="special_remarks" id="special_remarks" value=""
                                                    class="form-control auto-resize-textarea" placeholder="Special Remarks such as any specific recommendation">N/A</textarea>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="accordion" id="accordionExample">
                                                <div class="accordion-item ">
                                                    <h6 class="accordion-header" type="button"
                                                        data-toggle="collapse" data-target="#collapsesix"
                                                        aria-expanded="true" aria-controls="collapsesix">
                                                        Attachments</h6>

                                                    <div id="collapsesix" class="collapse show"
                                                        aria-labelledby="collapsesix"
                                                        data-parent="#accordionExample">
                                                        <div class="card-body">
                                                            <div class="row" style="margin-top:-20px;">
                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleFormControlInput1">Copy
                                                                            of Previous
                                                                            work
                                                                            order/purchase order :
                                                                            @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->previous_work_order))
                                                                            <a class="ml-2 download-all"
                                                                                id="previous_work_order-{{ $material_doc->id }}"
                                                                                href="#"
                                                                                data-file-type="previous_work_order"
                                                                                data-files="{{ $material_doc->previous_work_order }}">
                                                                                <i class="fa fa-download" title="Download All"></i>
                                                                            </a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input readonly type="file"
                                                                            class="form-control"
                                                                            id="previous_work_order"
                                                                            name="previous_work_order">
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleFormControlInput1">Copy
                                                                            of
                                                                            DERC/Other
                                                                            Stakeholder Approvals :
                                                                            @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->derc_stakeholder_approvals))
                                                                            <a class="ml-2 download-all"
                                                                                id="derc_stakeholder_approvals-{{ $material_doc->id }}"
                                                                                href="#"
                                                                                data-file-type="derc_stakeholder_approvals"
                                                                                data-files="{{ $material_doc->derc_stakeholder_approvals }}">
                                                                                <i class="fa fa-download" title="Download All"></i>
                                                                            </a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input readonly type="file"
                                                                            class="form-control"
                                                                            id="derc_stakeholder_approvals"
                                                                            name="derc_stakeholder_approvals">
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="exampleFormControlInput1">Consumption
                                                                            details -
                                                                            Last 3
                                                                            Year :
                                                                            @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->consumption_details))
                                                                            <a class="ml-2 download-all"
                                                                                id="consumption_details-{{ $material_doc->id }}"
                                                                                href="#"
                                                                                data-file-type="consumption_details"
                                                                                data-files="{{ $material_doc->consumption_details }}">
                                                                                <i class="fa fa-download" title="Download All"></i>
                                                                            </a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input readonly type="file"
                                                                            class="form-control"
                                                                            id="consumption_details"
                                                                            name="consumption_details">
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="exampleFormControlInput1">Technical
                                                                            Specifications :
                                                                            @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->vendor_quatation))
                                                                            <a class="ml-2 download-all"
                                                                                id="vendor_quatation-{{ $material_doc->id }}"
                                                                                href="#"
                                                                                data-file-type="vendor_quatation"
                                                                                data-files="{{ $material_doc->vendor_quatation }}">
                                                                                <i class="fa fa-download" title="Download All"></i>
                                                                            </a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input readonly type="file"
                                                                            class="form-control"
                                                                            id="vendor_quatation"
                                                                            name="vendor_quatation">
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="exampleFormControlInput1">Photographs
                                                                            of
                                                                            Product :
                                                                            @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->photo_product))
                                                                            <a class="ml-2 download-all"
                                                                                id="photo_product-{{ $material_doc->id }}"
                                                                                href="#"
                                                                                data-file-type="photo_product"
                                                                                data-files="{{ $material_doc->photo_product }}">
                                                                                <i class="fa fa-download" title="Download All"></i>
                                                                            </a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input readonly type="file"
                                                                            class="form-control"
                                                                            id="photo_product"
                                                                            name="photo_product">
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="exampleFormControlInput1">Material
                                                                            Procurement :
                                                                            @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->material_procurement))
                                                                            <a class="ml-2 download-all"
                                                                                id="material_procurement-{{ $material_doc->id }}"
                                                                                href="#"
                                                                                data-file-type="material_procurement"
                                                                                data-files="{{ $material_doc->material_procurement }}">
                                                                                <i class="fa fa-download" title="Download All"></i>
                                                                            </a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input readonly type="file"
                                                                            class="form-control"
                                                                            id="material_procurement"
                                                                            name="material_procurement">
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="exampleFormControlInput1">Budget
                                                                            Statement
                                                                            for
                                                                            Both
                                                                            OPEX/CAPEX Activities :
                                                                            @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->budget_for_both))
                                                                            <a class="ml-2 download-all"
                                                                                id="budget_for_both-{{ $material_doc->id }}"
                                                                                href="#"
                                                                                data-file-type="budget_for_both"
                                                                                data-files="{{ $material_doc->budget_for_both }}">
                                                                                <i class="fa fa-download" title="Download All"></i>
                                                                            </a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input readonly type="file"
                                                                            class="form-control"
                                                                            id="budget_for_both"
                                                                            name="budget_for_both">

                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="exampleFormControlInput1">Others
                                                                            :
                                                                            @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->others))
                                                                            <a class="ml-2 download-all"
                                                                                id="others-{{ $material_doc->id }}"
                                                                                href="#"
                                                                                data-file-type="others"
                                                                                data-files="{{ $material_doc->others }}">
                                                                                <i class="fa fa-download" title="Download All"></i>
                                                                            </a>
                                                                            @else
                                                                            <spam> N/A </spam>
                                                                            @endif
                                                                        </label>
                                                                        <input type="file" readonly
                                                                            class="form-control" id="others"
                                                                            name="others">

                                                                    </div>
                                                                </div>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>






                                                <!-- <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Total Budget Materials
                                                (Rs)</label>
                                                @if (!empty($material_details->total_budget_material))
    <input type="text" readonly class="form-control" id="total_budget_material"
                                                name="total_budget_material"
                                                value="{{ $material_details != '' ? $material_details['total_budget_material'] : '' }}"
                                                placeholder=" (In Rs)"
                                                onkeypress='return event.charCode >= 48 && event.charCode <= 57'></input>
@else
    <input type="text" readonly class="form-control" id="total_budget_material"
                                                name="total_budget_material"
                                                value="N/A"
                                               
                                                onkeypress='return event.charCode >= 48 && event.charCode <= 57'></input>
    @endif
                                        </div>
                                    </div> -->

                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                </div>

                                                <div class="col-12 mb-2">
                                                    <div class="form-group">
                                                        <div id="myRadioGroup">

                                                            <label for="exampleFormControlInput1">Are There Any Services
                                                                Related To
                                                                This
                                                                NV</label> <br>
                                                            @if (!empty($material_details->ser_rel_nv))
                                                            @if ($material_details->ser_rel_nv == 2)
                                                            Yes &nbsp; <input readonly type="radio"
                                                                id="CheckYes" name="cars" value="2"
                                                                {{ ($material_details != '' ? $material_details['ser_rel_nv'] : '') == '2' ? 'checked' : '' }} />
                                                            @else
                                                            No &nbsp;<input readonly type="radio"
                                                                name="cars" value="3"
                                                                {{ ($material_details != '' ? $material_details['ser_rel_nv'] : '') == '3' ? 'checked' : '' }} />
                                                            <div class="col-6 mt-3 form-group">
                                                                <label for="exampleFormControlInput1">Total NV Amount</label>
                                                                <input type="text" class="form-control" name="total_budget_both" id="total_budget_both" placeholder=" (In Rs)" value="{{ $material_details != '' ? indian_number_format($material_details->total_budget_both) : '' }}" readonly>
                                                            </div>
                                                            @if ($nv->budgetary_provision === 'Approved')
                                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">

                                                                <label>Approved Budget</label>
                                                                @if (!empty($material_details->approved_budget))
                                                                <input readonly class="form-control" name="approved_budget" type="text"
                                                                    id="approved_budget" value="{{ indian_number_format($material_details->approved_budget) }}">
                                                                @else
                                                                <input readonly class="form-control" name="approved_budget" type="text"
                                                                    id="approved_budget" value="N/A">
                                                                @endif

                                                            </div>
                                                            <div class="col-xl-6 col-lg-6 col-md-6 mt-3">

                                                                <label>Additional Budget</label>
                                                                @if (!empty($material_details->add_budget))
                                                                <input readonly class="form-control" name="add_budget" type="text"
                                                                    id="add_budget" value="{{ indian_number_format($material_details->add_budget) }}">
                                                                @else
                                                                <input readonly class="form-control" name="add_budget" type="text"
                                                                    id="add_budget" value="N/A">
                                                                @endif

                                                            </div>
                                                            @endif

                                                            @endif


                                                            @endif


                                                            <div id="Cars2" class="desc">

                                                                @php
                                                                $year1 = explode(',', $material_details != '' ? $material_details->past_3_year_actual_cost_fy : '');
                                                                $year2 = explode(',', $material_details != '' ? $material_details->past_3_year_actual_cost : '');
                                                                $year3 = explode(',', $material_details != '' ? $material_details->past_3_year_actual_cost_service : '');
                                                                $year = $nv_year->fiscal_year;
                                                                $yr_l = substr($year, 5);
                                                                $yr_f = substr($year, 0, 4);
                                                                @endphp

                                                                <div class="col-12 mb-2">
                                                                    <div class="accordion" id="accordionExample">
                                                                        <div class="accordion-item">
                                                                            <h6 class="accordion-header" type="button"
                                                                                data-toggle="collapse"
                                                                                data-target="#collapseOne"
                                                                                aria-expanded="true"
                                                                                aria-controls="collapseOne">
                                                                                Past 3 Years actual cost trend Services (in Rs / Lakh / Crore, etc.)
                                                                            </h6>

                                                                            <div id="collapseOne" class="collapse show"
                                                                                aria-labelledby="collapseOne"
                                                                                data-parent="#accordionExample">
                                                                                <div class="card-body">
                                                                                    <div class="after-add-more">
                                                                                        <div class="row">
                                                                                            <div
                                                                                                class="col-xl-3 col-lg-3 col-md-6">
                                                                                                @if (!empty($material_details->past_3_year_actual_cost_fy))
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost_fy[]"
                                                                                                    id="past_3_year_actual_cost_fy"
                                                                                                    value="{{ $yr_f - 1 }}-{{ $yr_l - 1 }}"
                                                                                                    placeholder="{{ $yr_f - 1 }}-{{ $yr_l - 1 }}"
                                                                                                    readonly>
                                                                                                @else
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost_fy[]"
                                                                                                    id="past_3_year_actual_cost_fy"
                                                                                                    value="N/A"
                                                                                                    readonly>
                                                                                                @endif
                                                                                            </div>

                                                                                            <div
                                                                                                class="col-xl-3 col-lg-3 col-md-6">
                                                                                                @if (!empty($material_details->past_3_year_actual_cost))
                                                                                                <input readonly
                                                                                                    type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost[]"
                                                                                                    id="past_3_year_actual_cost"
                                                                                                    placeholder="₹"
                                                                                                    value="{{ isset($year2[0]) ? $year2[0] : '' }}">
                                                                                                @else
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost[]"
                                                                                                    id="past_3_year_actual_cost"
                                                                                                    value="N/A"
                                                                                                    readonly>
                                                                                                @endif
                                                                                            </div>

                                                                                            <div
                                                                                                class="col-xl-3 col-lg-3 col-md-6">
                                                                                                @if (!empty($material_details->past_3_year_actual_cost_service))
                                                                                                <input readonly
                                                                                                    type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost_service[]"
                                                                                                    id="past_3_year_actual_cost_service"
                                                                                                    value="{{ isset($year3[0]) ? $year3[0] : '' }}"
                                                                                                    placeholder="Services">
                                                                                                @else
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost_service[]"
                                                                                                    id="past_3_year_actual_cost_service"
                                                                                                    value="N/A"
                                                                                                    readonly>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <br>
                                                                                        <div class="row">
                                                                                            <div
                                                                                                class="col-xl-3 col-lg-3 col-md-6">
                                                                                                @if (!empty($material_details->past_3_year_actual_cost_fy))
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost_fy[]"
                                                                                                    id="past_3_year_actual_cost_fy"
                                                                                                    value="{{ $yr_f - 2 }}-{{ $yr_l - 2 }}"
                                                                                                    placeholder="{{ $yr_f - 2 }}-{{ $yr_l - 2 }}"
                                                                                                    readonly>
                                                                                                @else
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost_fy[]"
                                                                                                    id="past_3_year_actual_cost_fy"
                                                                                                    value="N/A"
                                                                                                    readonly>
                                                                                                @endif
                                                                                            </div>

                                                                                            <div
                                                                                                class="col-xl-3 col-lg-3 col-md-6">
                                                                                                @if (!empty($material_details->past_3_year_actual_cost))
                                                                                                <input readonly
                                                                                                    type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost[]"
                                                                                                    id="past_3_year_actual_cost"
                                                                                                    placeholder="₹"
                                                                                                    value="{{ isset($year2[1]) ? $year2[1] : '' }}">
                                                                                                @else
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost[]"
                                                                                                    id="past_3_year_actual_cost"
                                                                                                    value="N/A"
                                                                                                    readonly>
                                                                                                @endif
                                                                                            </div>

                                                                                            <div
                                                                                                class="col-xl-3 col-lg-3 col-md-6">
                                                                                                @if (!empty($material_details->past_3_year_actual_cost_service))
                                                                                                <input readonly
                                                                                                    type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost_service[]"
                                                                                                    id="past_3_year_actual_cost_service"
                                                                                                    value="{{ isset($year3[1]) ? $year3[1] : '' }}"
                                                                                                    placeholder="Services">
                                                                                                @else
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost_service[]"
                                                                                                    id="past_3_year_actual_cost_service"
                                                                                                    value="N/A"
                                                                                                    readonly>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <br>
                                                                                        <div class="row">
                                                                                            <div
                                                                                                class="col-xl-3 col-lg-3 col-md-6">
                                                                                                @if (!empty($material_details->past_3_year_actual_cost_fy))
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost_fy[]"
                                                                                                    id="past_3_year_actual_cost_fy"
                                                                                                    value="{{ $yr_f - 3 }}-{{ $yr_l - 3 }}"
                                                                                                    placeholder="{{ $yr_f - 3 }}-{{ $yr_l - 3 }}"
                                                                                                    readonly>
                                                                                                @else
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost_fy[]"
                                                                                                    id="past_3_year_actual_cost_fy"
                                                                                                    value="N/A"
                                                                                                    readonly>
                                                                                                @endif
                                                                                            </div>

                                                                                            <div
                                                                                                class="col-xl-3 col-lg-3 col-md-6">
                                                                                                @if (!empty($material_details->past_3_year_actual_cost))
                                                                                                <input readonly
                                                                                                    type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost[]"
                                                                                                    id="past_3_year_actual_cost"
                                                                                                    placeholder="₹"
                                                                                                    value="{{ isset($year2[2]) ? $year2[2] : '' }}">
                                                                                                @else
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost[]"
                                                                                                    id="past_3_year_actual_cost"
                                                                                                    value="N/A"
                                                                                                    readonly>
                                                                                                @endif
                                                                                            </div>

                                                                                            <div
                                                                                                class="col-xl-3 col-lg-3 col-md-6">
                                                                                                @if (!empty($material_details->past_3_year_actual_cost_service))
                                                                                                <input readonly
                                                                                                    type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost_service[]"
                                                                                                    id="past_3_year_actual_cost_service"
                                                                                                    value="{{ isset($year3[2]) ? $year3[2] : '' }}"
                                                                                                    placeholder="Services">
                                                                                                @else
                                                                                                <input readonly
                                                                                                    type="text"
                                                                                                    class="form-control"
                                                                                                    name="past_3_year_actual_cost_service[]"
                                                                                                    id="past_3_year_actual_cost_service"
                                                                                                    value="N/A">
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <div class="accordion" id="accordionExample">
                                                                            <div class="accordion-item">
                                                                                <h6 class="accordion-header"
                                                                                    type="button"
                                                                                    data-toggle="collapse"
                                                                                    data-target="#collapseF"
                                                                                    aria-expanded="true"
                                                                                    aria-controls="collapseF">
                                                                                    Service BOQ</h6>

                                                                                <div id="collapseF"
                                                                                    class="collapse show"
                                                                                    aria-labelledby="headingTwo"
                                                                                    data-parent="#accordionExample">
                                                                                    <div class="card-body">
                                                                                        <form
                                                                                            enctype="multipart/form-data"
                                                                                            method="post"
                                                                                            id="serviceboqform">
                                                                                            <input type="hidden"
                                                                                                class="form-control"
                                                                                                name="nv_id"
                                                                                                id="nv_id"
                                                                                                value="{{ request()->segment(4) }}">
                                                                                            <div
                                                                                                class="row 
                                                                                             after-add-more-four">
                                                                                                @php
                                                                                                $s1 = explode(',', $material_details != '' ? $material_details->service_code : '');
                                                                                                $s2 = explode(',', $material_details != '' ? $material_details->ser_des : '');
                                                                                                $s3 = explode(',', $material_details != '' ? $material_details->ser_rate_ref : '');
                                                                                                $s4 = explode(',', $material_details != '' ? $material_details->ser_uom : '');
                                                                                                $s5 = explode(',', $material_details != '' ? $material_details->ser_rate : '');
                                                                                                $s6 = explode(',', $material_details != '' ? $material_details->ser_quantity : '');
                                                                                                $s7 = explode(',', $material_details != '' ? $material_details->ser_total_amount : '');

                                                                                                @endphp

                                                                                                <!-- <div class="col-6 mb-2">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="exampleFormControlInput1">Service
                                                                                            code</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Service Code"
                                                                                            name="service_code_0"
                                                                                            id="service_code_0"
                                                                                            value=""
                                                                                            onkeyup="autofetchServiceCode()">

                                                                                    </div>
                                                                                </div>

                                                                         
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="exampleFormControlInput1">
                                                                                      
                                                                                        </label>

                                                                                        <input type="hidden" name="ser_des_0"
                                                                                            id="ser_des_0" cols="2" rows="1"
                                                                                            value=""
                                                                                            class="form-control"
                                                                                            placeholder=" Enter Service Description">
                                                                                    </div>
                                                                         
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="exampleFormControlInput1"></label>
                                                                                        <input type="hidden"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Rate Reference"
                                                                                            name="ser_rate_ref"
                                                                                            id="ser_rate_ref"
                                                                                            value="">
                                                                                    </div>

                                                                        
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="exampleFormControlInput1">
                                                                                      
                                                                                        </label>
                                                                                            <input type="hidden"
                                                                                            class="form-control"
                                                                                            name="ser_uom_0" id="ser_uom_0"
                                                                                            value=""
                                                                                            placeholder="Enter Uom">
                                                                                    </div>
                                                                         

                                                                                <div class="col-6 mb-2">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="exampleFormControlInput1">Rate</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Rate"
                                                                                            name="ser_rate"
                                                                                            value=""
                                                                                            id="ser_rate"
                                                                                            onblur="sumSolar()">
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-6 mb-2">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="exampleFormControlInput1">Quantity
                                                                                            Required</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Quantity Required"
                                                                                            name="ser_quantity"
                                                                                            id="ser_quantity"
                                                                                            value=""
                                                                                            onblur="sumSolar()">
                                                                                    </div>
                                                                                </div>

                                                                    
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="exampleFormControlInput1">
                                                                                     
                                                                                        </label>
                                                                                            <input type="hidden"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Total Amount"
                                                                                            name="ser_total_amount"
                                                                                            id="ser_total_amount"
                                                                                            value=""
                                                                                            onblur="sumSolar()">
                                                                                    </div>
                                                                         


                                                                                <div class="col-6 mb-2">
                                                                                    
                                                                                </div>
                                                                                <br>
                                                                               
                                                                                <div class="col-6 mb-2">
                                                                                    <div class="form-group change">
                                                                                        <br>
                                                                                        <button type="button" class="btn btn-success" id="serviceBoqsave" value="Save">Save</button>
                                                                         
                                                                                    </div>
                                                                                </div><br>
                                                                            
                                                                              <div class="col-6 mb-2">
                                                                                    <div class="form-group ">
                                                                                 
                                                                                    </div>
                                                                                </div>
                                                                            </form> -->


                                                                                                <!-- <form enctype="multipart/form-data" method="post" id="uploads-form" style="display: flex; justify-content: space-between;">
                                                                                    <div class="col-12">
                                                                                        <div class="form-group">
                                                                                            <label for="serviceboq">Upload Service BOQ
                                                                                                <a class="ml-2" download="{{ asset('materials-doc/Service BOQ.xls') }}" href="{{ asset('materials-doc/Service BOQ.xls') }}"><i class="fa fa-download" title="Download"></i></a>
                                                                                            </label>
                                                                                            <input type="file" class="form-control" id="serviceboq" name="serviceboq">
                                                                                        </div>
                                                                                        <input type="hidden" class="form-control" name="nv_id" id="nv_id"
                                                                                        value="{{ request()->segment(4) }}">
                                                                                    </div>

                                   

                                                                                    <div class="col-12">
                                                                                        <div class="form-group">
                                                                                            <label style="visibility:hidden">Upload</label>
                                                                                            <br>
                                                                                            <button type="button" class="btn btn-success" id="serviceBoqUpload">Upload</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </form> -->

                                                                                            </div>

                                                                                            <div class="row">
                                                                                                <div class="col-12">
                                                                                                    <div class="card">

                                                                                                        <div
                                                                                                            class="card-body table-responsive">
                                                                                                            <table
                                                                                                                id="nv_serviceBoq_datatable2"
                                                                                                                class="table table-bordered">
                                                                                                                <input
                                                                                                                    type="hidden"
                                                                                                                    class="form-control"
                                                                                                                    name="nv_id"
                                                                                                                    id="nv_id"
                                                                                                                    value="{{ request()->segment(4) }}">
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
                                                                                                                        <th
                                                                                                                            width="15%">
                                                                                                                            Action
                                                                                                                        </th>

                                                                                                                    </tr>
                                                                                                                </thead>
                                                                                                            </table>
                                                                                                        </div>


                                                                                                        <!-- <div class="card-body table-responsive">
                                                                                            <table id="nv_datatable" class="table table-bordered">
                                                                                                <thead class="text-center">
                                                                                                    <tr>
                                                                                                        <th class="text-center" width="5%">S.No</th>
                                                                                                        <th>NV Id</th>
                                                                                                        <th>Material Code</th>
                                                                                                        <th>Description</th>
                                                                                                        <th width="15%">UOM</th>
                                                                                                        <th width="10%">Rate</th>
                                                                                                        <th width="15%">Qty</th>
                                                                                                        <th width="15%">Amount (Rs.)</th>
                                                                                                            <th width="15%">Action</th>
                                                                                                    </tr>
                                                                                                 </thead>
                                                                                                 <tbody>
                                                                                                    <?php $total = 0; ?>
                                                                                                    @foreach ($service_import as $key => $s_import)
    @php
        
        $total = $total + $s_import->amount;
    @endphp
                                                                                                    <tr>
                                                                                                        <td>{{ ++$key }}</td>
                                                                                                        <td>{{ $s_import->nv_id }}</td>
                                                                                                        <td>{{ $s_import->service_code }}</td>
                                                                                                        <td>{{ $s_import->description }}</td>
                                                                                                        <td>{{ $s_import->uom }}</td>
                                                                                                        <td>{{ $s_import->rate }}</td>
                                                                                                        <td>{{ $s_import->qty }}</td>
                                                                                                        <td>{{ $s_import->amount }}</td>
                                                                                                               <td><a href="/admin/nv_material/edit_service/{{ $s_import->id }}">Edit</a></td>
                                                                                                    </tr>
    @endforeach
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div> -->

                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>

                                                                                    </div>


                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                <label
                                                                                    for="exampleFormControlInput1">Estimated Amount Of Services (Civil)</label>
                                                                                @if (!empty($material_details->estimate_amount_of_service_civil))
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="estimate_amount_of_service_civil"
                                                                                    id="estimate_amount_of_service_civil"
                                                                                    value="{{indian_number_format($material_details['estimate_amount_of_service_civil']) }}">
                                                                                @else
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="estimate_amount_of_service_civil"
                                                                                    id="estimate_amount_of_service_civil"
                                                                                    value="N/A">
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-xl-2 col-lg-2 col-md-2">
                                                                            <label>Tax (In %)</label>
                                                                            @if (!empty($material_details->tax6))
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax6" name="tax6"
                                                                                value="{{ $material_details->tax6 }}">
                                                                            @else
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax6" name="tax6"
                                                                                value="N/A">
                                                                            @endif
                                                                        </div>

                                                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                @if ($nv->budgetary_provision === 'Approved')
                                                                                <label for="exampleFormControlInput1">Budget Available <span
                                                                                        class="mandatory_input">*</span></label>
                                                                                @if (!empty($material_details->budget_avl))
                                                                                <input readonly type="text" class="form-control"
                                                                                    placeholder="Budget Available" name="budget_avl"
                                                                                    id="budget_avl"
                                                                                    value="{{ $material_details != '' ? indian_number_format($material_details->budget_avl) : '' }}">
                                                                                @else
                                                                                <input readonly type="text" class="form-control"
                                                                                    placeholder="Budget Available" name="budget_avl"
                                                                                    id="budget_avl" value="N/A">
                                                                                @endif
                                                                                @endif
                                                                            </div>
                                                                        </div>


                                                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                @if ($nv->budgetary_provision === 'Approved')
                                                                                <!-- <label for="exampleFormControlInput1">Budget
                                                                                Available <span class="mandatory_input">*</span></label> -->

                                                                                <input type="hidden" readonly class="form-control" placeholder="Budget Available" name="ser_budget_avl" id="ser_budget_avl" value="">
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                    </div>



                                                                    <div class="row mt-2">
                                                                        <div class="col-12">
                                                                            <div class="card-body" style="padding: 0px">
                                                                                <div class="row after-add-more-scheme-service">
                                                                                    @php
                                                                                    $service_amount = explode(',', $material_details != '' ? $material_details->service_amount : '0');
                                                                                    $service_description = explode(',', $material_details != '' ? $material_details->service_description : '');
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
                                                                                                placeholder="Amount" value="{{ $material_details != '' ? indian_number_format($service_amt) : '' }}" readonly>
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

                                                                    <div class="row">

                                                                        <div
                                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                <label for="exampleFormControlInput1">Cost
                                                                                    Calculation for
                                                                                    services : <br>
                                                                                    @if (!empty($material_doc) && is_object($material_doc) && !empty($material_doc->cost_calculation_for_service))
                                                                                    <a class="ml-2 download-all"
                                                                                        id="cost_calculation_for_service-{{ $material_doc->id }}"
                                                                                        href="#"
                                                                                        data-file-type="cost_calculation_for_service"
                                                                                        data-files="{{ $material_doc->cost_calculation_for_service }}">
                                                                                        <i class="fa fa-download" title="Download All"></i>
                                                                                    </a>
                                                                                    @else
                                                                                    <spam> </spam>
                                                                                    @endif
                                                                                </label>
                                                                                <input type="file" readonly
                                                                                    class="form-control"
                                                                                    name="cost_calculation_for_service"
                                                                                    id="cost_calculation_for_service"
                                                                                    placeholder=" Attachement in Excel">
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                <label for="exampleFormControlInput1">Past Practice Followed For Services (If Any)</label>
                                                                                @if (!empty($material_details->past_practice_follow))
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="past_practice_follow"
                                                                                    id="past_practice_follow"
                                                                                    placeholder="Past Practices followed for services (if any)"
                                                                                    value="{{ $material_details['past_practice_follow'] }}">
                                                                                @else
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="past_practice_follow"
                                                                                    id="past_practice_follow"
                                                                                    placeholder="Past Practices followed for services (if any)"
                                                                                    value="N/A">
                                                                                @endif
                                                                            </div>
                                                                        </div>



                                                                        <div
                                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                <label for="exampleFormControlInput1">AMC
                                                                                    Proposed Start
                                                                                    Date</label>
                                                                                @if (!empty($material_details->amc_prop_start_date))
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="amc_prop_start_date"
                                                                                    id="amc_prop_start_date"
                                                                                    value="{{ date('d-m-Y', strtotime($material_details['amc_prop_start_date'])) }}">
                                                                                @else
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="amc_prop_start_date"
                                                                                    id="amc_prop_start_date"
                                                                                    value="N/A">
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                <label for="exampleFormControlInput1">AMC
                                                                                    Proposed End Date
                                                                                </label>
                                                                                @if (!empty($material_details->amc_prop_end_date))
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="amc_prop_end_date"
                                                                                    id="amc_prop_end_date"
                                                                                    value="{{ date('d-m-Y', strtotime($material_details['amc_prop_end_date'])) }}">
                                                                                @else
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="amc_prop_end_date"
                                                                                    id="amc_prop_end_date"
                                                                                    value="N/A">
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                <label
                                                                                    for="exampleFormControlInput1">Estimated Amount Of Services (Labour+Transport)</label>
                                                                                @if (!empty($material_details->estimate_amount_of_service))
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="estimate_amount_of_service"
                                                                                    id="estimate_amount_of_service"
                                                                                    value="{{ indian_number_format($material_details['estimate_amount_of_service']) }}">
                                                                                @else
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="estimate_amount_of_service"
                                                                                    id="estimate_amount_of_service"
                                                                                    value="N/A">
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-xl-2 col-lg-2 col-md-2">
                                                                            <label>Tax (In %)</label>
                                                                            @if (!empty($material_details->tax2))
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax2" name="tax2"
                                                                                value="{{ $material_details->tax2 }}">
                                                                            @else
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax2" name="tax2"
                                                                                value="N/A">
                                                                            @endif
                                                                        </div>

                                                                        <div
                                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                <label
                                                                                    for="exampleFormControlInput1">Estimated Amount Of Services (Civil)</label>
                                                                                @if (!empty($material_details->estimate_amount_of_service_civil))
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="estimate_amount_of_service_civil"
                                                                                    id="estimate_amount_of_service_civil"
                                                                                    value="{{ indian_number_format($material_details['estimate_amount_of_service_civil']) }}">
                                                                                @else
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="estimate_amount_of_service_civil"
                                                                                    id="estimate_amount_of_service_civil"
                                                                                    value="N/A">
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-xl-2 col-lg-2 col-md-2">
                                                                            <label>Tax (In %)</label>
                                                                            @if (!empty($material_details->tax3))
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax3" name="tax3"
                                                                                value="{{ $material_details->tax3 }}">
                                                                            @else
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax3" name="tax3"
                                                                                value="N/A">
                                                                            @endif
                                                                        </div>

                                                                        <div
                                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                <label
                                                                                    for="exampleFormControlInput1">Estimated
                                                                                    amount of
                                                                                    RR
                                                                                    Charges</label>
                                                                                @if (!empty($material_details->estimate_amount_of_rr_charge))
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="estimate_amount_of_rr_charge"
                                                                                    id="estimate_amount_of_rr_charge"
                                                                                    value="{{ indian_number_format($material_details['estimate_amount_of_rr_charge']) }}"
                                                                                    placeholder=" (In Rs)">
                                                                                @else
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="estimate_amount_of_rr_charge"
                                                                                    id="estimate_amount_of_rr_charge"
                                                                                    value="N/A">
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-xl-2 col-lg-2 col-md-2">
                                                                            <label>Tax (In %)</label>
                                                                            @if (!empty($material_details->tax4))
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax4" name="tax4"
                                                                                value="{{ $material_details->tax4 }}">
                                                                            @else
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax4" name="tax4"
                                                                                value="N/A">
                                                                            @endif
                                                                        </div>

                                                                        <div
                                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                <label
                                                                                    for="exampleFormControlInput1">Estimated
                                                                                    amount -
                                                                                    Other</label>
                                                                                @if (!empty($material_details->estimate_amount_other))
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="estimate_amount_other"
                                                                                    id="estimate_amount_other"
                                                                                    placeholder=" Estimated amount - Other"
                                                                                    value="{{ indian_number_format($material_details['estimate_amount_other']) }}">
                                                                                @else
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="estimate_amount_other"
                                                                                    id="estimate_amount_other"
                                                                                    placeholder=" Estimated amount - Other"
                                                                                    value="N/A">
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-xl-2 col-lg-2 col-md-2">
                                                                            <label>Tax (In %)</label>
                                                                            @if (!empty($material_details->tax5))
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax5" name="tax5"
                                                                                value="{{ $material_details->tax5 }}">
                                                                            @else
                                                                            <input readonly type="text" class="form-control"
                                                                                id="tax5" name="tax5"
                                                                                value="N/A">
                                                                            @endif
                                                                        </div>

                                                                        <div
                                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                <label
                                                                                    for="exampleFormControlInput1"> Estimated Amount of Servicess (Including Labor,Civil & RR)
                                                                                </label>
                                                                                @if (!empty($material_details->total_budget_service))
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="total_budget_service"
                                                                                    id="total_budget_service"
                                                                                    placeholder=" (In Rs)"
                                                                                    value="{{ indian_number_format($material_details['total_budget_service']) }}">
                                                                                @else
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="total_budget_service"
                                                                                    id="total_budget_service"
                                                                                    placeholder=" (In Rs)"
                                                                                    value="N/A">
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                {{-- @dd( $material_details['total_budget_both']) --}}
                                                                                <label
                                                                                    for="exampleFormControlInput1">Total
                                                                                    Budget NV</label>
                                                                                @if (!empty($material_details->total_budget_both))
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="total_budget_both"
                                                                                    id="total_budget_both"
                                                                                    placeholder=" (In Rs)"
                                                                                    value="{{ indian_number_format($material_details['total_budget_both']) }}">
                                                                                @else
                                                                                <input readonly type="text"
                                                                                    class="form-control"
                                                                                    name="total_budget_both"
                                                                                    id="total_budget_both"
                                                                                    placeholder=" (In Rs)"
                                                                                    value="N/A">
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        @if ($nv->budgetary_provision === 'Approved')
                                                                        <div class="col-xl-6 col-lg-6 col-md-6 mt-3">

                                                                            <label>Approved Budget</label>
                                                                            @if (!empty($material_details->approved_budget))
                                                                            <input readonly class="form-control" name="approved_budget" type="text"
                                                                                id="approved_budget" value="{{ indian_number_format($material_details->approved_budget) }}">
                                                                            @else
                                                                            <input readonly class="form-control" name="approved_budget" type="text"
                                                                                id="approved_budget" value="N/A">
                                                                            @endif

                                                                        </div>
                                                                        <div class="col-xl-6 col-lg-6 col-md-6 mt-3">

                                                                            <label>Additional Budget</label>
                                                                            @if (!empty($material_details->add_budget))
                                                                            <input readonly class="form-control" name="add_budget" type="text"
                                                                                id="add_budget" value="{{ indian_number_format($material_details->add_budget) }}">
                                                                            @else
                                                                            <input readonly class="form-control" name="add_budget" type="text"
                                                                                id="add_budget" value="N/A">
                                                                            @endif

                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div id="Cars3" class="desc" style="display: none;">

                                                            </div>

                                                        </div>
                                                        @php

                                                        $userId = Auth::id();
                                                        $userDeptId = Auth::user()->department_id;
                                                        $departmentName = getdepname(Auth::user()->department_id);

                                                        $isCeoNomineeReviewer = false;
                                                        if (str_contains($departmentName, 'CEO Nominee-2')) {
                                                        $isCeoNomineeReviewer = DB::table('capex_workflows_status')
                                                        ->where('workflow_user_id', $userId)
                                                        ->where('department_id', $userDeptId)
                                                        ->where('material_id', $material_details->id)
                                                        ->where('nv_budget_type', $nv->budget_type)
                                                        ->where('reviewer_name', '!=', 'approver')
                                                        ->exists();
                                                        }

                                                        $checkValue = $material_details['check_ceonm2'] ?? null;

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
                                            $checkValue = $transfer_to_nominee1 ?? 0;

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
                                                        required>
                                                    <label class="form-check-label" for="transfer_yes">Yes</label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input yes-no-checkbox"
                                                        type="checkbox"
                                                        name="transfer_to_nominee1"
                                                        id="transfer_no"
                                                        value="1"
                                                        {{ old('transfer_to_nominee1', $checkValue) == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="transfer_no">No</label>
                                                </div>
                                            </div>
                                            @else
                                            <div class="form-group mt-3">
                                                <label class="form-label d-block">Is the NV for EHV Schemes/System Improvement (LT & 11 KV related)/Trial Order-New Product/New Vendor?</label>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        id="transfer_yes_disabled"
                                                        value="0"
                                                        {{ $checkValue == 0 ? 'checked' : '' }}
                                                        disabled>
                                                    <label class="form-check-label" for="transfer_yes_disabled">Yes</label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        id="transfer_no_disabled"
                                                        value="1"
                                                        {{ $checkValue == 1 ? 'checked' : '' }}
                                                        disabled>
                                                    <label class="form-check-label" for="transfer_no_disabled">No</label>
                                                </div>

                                                {{-- ✅ Hidden input to preserve value for DB update 
                                                <input type="hidden" name="transfer_to_nominee1" value="{{ $checkValue }}"> --}}
                                            </div>
                                            @endif

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
                                                    <span class="remark_error"></span><br>
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
                                                    <span class="remark_error"></span><br>
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
                                                    <span class="remark_error"></span><br>
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
                                                    <span class="remark_error"></span><br>
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
                                                    <span class="remark_error"></span><br>
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
                                                    ->where('material_id', $material_details->id)->whereIn('nv_stage_status',[1,2])
                                                    ->where('nv_budget_type', $nv->budget_type)
                                                    ->where('nv_stage_remark', '!=', null)
                                                    ->get();
                                                    $stages_workflows = DB::table('capex_workflows_status')->where('material_id', $material_details->id)->get();
                                                    @endphp

                                                    @if($stages_workflows->isNotEmpty())
                                                    @foreach ($stages_workflows as $stageworkflow)
                                                    @if (!empty($stageworkflow->workflow_user_id) && $stageworkflow->workflow_user_id == $user->id)
                                                    <textarea name="remark" id="remark" cols="2" rows="5" class="form-control"
                                                        placeholder=" Enter Remark Description">{{$stageworkflow->nv_stage_remark ?? ''}}</textarea>
                                                    <span class="remark_error"></span><br>
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
                                                    <span class="remark_error"></span><br>
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
                                                </div>

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
                                                                            <td>
                                                                                @if(!empty($recevier->clarification_remark))
                                                                                {{-- @if(!empty($recevier->attachment))
                                                                                    <a class="ml-2" download="{{ $recevier->attachment}}" href="{{ url(asset('clarification-file/' . $recevier->attachment)) }}">{{$recevier->attachment}}</a>
                                                                                <br> @endif --}}
                                                                                @if(!empty($recevier->attachment))
                                                                                @php
                                                                                $attachments = json_decode($recevier->attachment, true);
                                                                                @endphp

                                                                                @if(is_array($attachments))
                                                                                @foreach($attachments as $file)
                                                                                <a class="ml-2" download="{{ $file }}" href="{{ asset('clarification-file/' . $file) }}">{{ $file }}</a><br>
                                                                                @endforeach
                                                                                @else
                                                                                <a class="ml-2" download="{{ $recevier->attachment }}" href="{{ asset('clarification-file/' . $recevier->attachment) }}">{{ $recevier->attachment }}</a><br>
                                                                                @endif
                                                                                @endif
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
                                                                                {{-- @if(!empty($recevier->reply_attachment))
                                                              <a class="ml-2" download="{{ $recevier->reply_attachment}}" href="{{ url(asset('clarification-file/' . $recevier->reply_attachment)) }}">{{$recevier->reply_attachment}}</a>
                                                                                <br> @endif --}}
                                                                                @if(!empty($recevier->reply_attachment))
                                                                                @php
                                                                                $attachments = json_decode($recevier->reply_attachment, true);
                                                                                @endphp

                                                                                @if(is_array($attachments))
                                                                                @foreach($attachments as $file)
                                                                                <a class="ml-2" download="{{ $file }}" href="{{ asset('clarification-file/' . $file) }}">{{ $file }}</a><br>
                                                                                @endforeach
                                                                                @else
                                                                                <a class="ml-2" download="{{ $recevier->reply_attachment }}" href="{{ asset('clarification-file/' . $recevier->reply_attachment) }}">{{ $recevier->reply_attachment }}</a><br>
                                                                                @endif
                                                                                @endif
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
                                                                        <input type="file" id="replyattachment" name="replyattachment[]" multiple class="form-control">
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


                                                {{-----------------Start----}}

                                                {{-- --------------------clarification --}}
                                                {{-- @include('clarification.materialreply') --}}


                                                @if (Auth::user()->role_id != 9)

                                                @if ($Nvsericestatus->rv1_status == 0 && $Nvsericestatus->draft == 1)
                                                @if (!empty($rv1) && $rv1 == $user->id)
                                                <div class="row m-3">
                                                    <div class="col-md-12">
                                                        <div class="card card-outline card-info" id="clarify">
                                                            <div class="card-body">
                                                                <table class="table table-bordered">
                                                                    <thead class="text-center">
                                                                        <tr role="row t-body">
                                                                            <th class="text-center sorting_disabled th"
                                                                                aria-label="S.No">S.No</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Proposal Number: activate to sort column ascending">
                                                                                Name</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Budget Type: activate to sort column ascending">
                                                                                Email</th>
                                                                            <th class="text-center sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Initiated By: activate to sort column ascending">
                                                                                Department</th>
                                                                            <th class="text-center sorting"
                                                                                tabindex="0" aria-controls="nv_datatable"

                                                                                aria-label="Initiated Date: activate to sort column ascending">
                                                                                Mobile No</th>
                                                                            <th class="text-center">Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php $count = 1; @endphp
                                                                        @foreach ($employees as $employee)
                                                                        <tr>
                                                                            <td>{{ $count++ }}</td>
                                                                            <td class="text-left">{{ $employee->name }}</td>
                                                                            <td class="text-left">{{ $employee->email }}</td>
                                                                            <td>{{ $employee->department->name }}</td>
                                                                            <td>{{ $employee->phone }}</td>
                                                                            <td class="text-center">
                                                                                <button type="button"
                                                                                    class="btn btn-info btn-sm clarification-btn"
                                                                                    data-toggle="modal"
                                                                                    data-target="#clarificationModal"
                                                                                    data-name="{{ $employee->name }}"
                                                                                    data-email="{{ $employee->email }}"
                                                                                    data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                                <!-- <button type="button"
                                              class="btn btn-primary btn-sm email-btn"
                                              data-toggle="modal"
                                              data-target="#emailModal">Email</button> -->
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
                                                        <div class="card card-outline card-info" id="clarify">
                                                            <div class="card-body">
                                                                <table class="table table-bordered">
                                                                    <thead class="text-center">
                                                                        <tr role="row t-body">
                                                                            <th class="text-center sorting_disabled th"
                                                                                aria-label="S.No">S.No</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Proposal Number: activate to sort column ascending">
                                                                                Name</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Budget Type: activate to sort column ascending">
                                                                                Email</th>
                                                                            <th class="text-center sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Initiated By: activate to sort column ascending">
                                                                                Department</th>
                                                                            <th class="text-center sorting"
                                                                                tabindex="0" aria-controls="nv_datatable"

                                                                                aria-label="Initiated Date: activate to sort column ascending">
                                                                                Mobile No</th>
                                                                            <th class="text-center">Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php $count = 1; @endphp
                                                                        @foreach ($employees as $employee)
                                                                        <tr>
                                                                            <td>{{ $count++ }}</td>
                                                                            <td class="text-left">{{ $employee->name }}</td>
                                                                            <td class="text-left">{{ $employee->email }}</td>
                                                                            <td>{{ $employee->department->name }}</td>
                                                                            <td>{{ $employee->phone }}</td>
                                                                            <td class="text-center">
                                                                                <button type="button"
                                                                                    class="btn btn-info btn-sm clarification-btn"
                                                                                    data-toggle="modal"
                                                                                    data-target="#clarificationModal"
                                                                                    data-name="{{ $employee->name }}"
                                                                                    data-email="{{ $employee->email }}"
                                                                                    data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                                <!-- <button type="button"
                                              class="btn btn-primary btn-sm email-btn"
                                              data-toggle="modal"
                                              data-target="#emailModal">Email</button> -->
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
                                                        <div class="card card-outline card-info" id="clarify">
                                                            <div class="card-body">
                                                                <table class="table table-bordered">
                                                                    <thead class="text-center">
                                                                        <tr role="row t-body">
                                                                            <th class="text-center sorting_disabled th"
                                                                                aria-label="S.No">S.No</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Proposal Number: activate to sort column ascending">
                                                                                Name</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Budget Type: activate to sort column ascending">
                                                                                Email</th>
                                                                            <th class="text-center sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Initiated By: activate to sort column ascending">
                                                                                Department</th>
                                                                            <th class="text-center sorting"
                                                                                tabindex="0" aria-controls="nv_datatable"

                                                                                aria-label="Initiated Date: activate to sort column ascending">
                                                                                Mobile No</th>
                                                                            <th class="text-center">Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php $count = 1; @endphp
                                                                        @foreach ($employees as $employee)
                                                                        <tr>
                                                                            <td>{{ $count++ }}</td>
                                                                            <td class="text-left">{{ $employee->name }}</td>
                                                                            <td class="text-left">{{ $employee->email }}</td>
                                                                            <td>{{ $employee->department->name }}</td>
                                                                            <td>{{ $employee->phone }}</td>
                                                                            <td class="text-center">
                                                                                <button type="button"
                                                                                    class="btn btn-info btn-sm clarification-btn"
                                                                                    data-toggle="modal"
                                                                                    data-target="#clarificationModal"
                                                                                    data-name="{{ $employee->name }}"
                                                                                    data-email="{{ $employee->email }}"
                                                                                    data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                                <!-- <button type="button"
                                              class="btn btn-primary btn-sm email-btn"
                                              data-toggle="modal"
                                              data-target="#emailModal">Email</button> -->
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
                                                        <div class="card card-outline card-info" id="clarify">
                                                            <div class="card-body">
                                                                <table class="table table-bordered">
                                                                    <thead class="text-center">
                                                                        <tr role="row t-body">
                                                                            <th class="text-center sorting_disabled th"
                                                                                aria-label="S.No">S.No</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Proposal Number: activate to sort column ascending">
                                                                                Name</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Budget Type: activate to sort column ascending">
                                                                                Email</th>
                                                                            <th class="text-center sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Initiated By: activate to sort column ascending">
                                                                                Department</th>
                                                                            <th class="text-center sorting"
                                                                                tabindex="0" aria-controls="nv_datatable"

                                                                                aria-label="Initiated Date: activate to sort column ascending">
                                                                                Mobile No</th>
                                                                            <th class="text-center">Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php $count = 1; @endphp
                                                                        @foreach ($employees as $employee)
                                                                        <tr>
                                                                            <td>{{ $count++ }}</td>
                                                                            <td class="text-left">{{ $employee->name }}</td>
                                                                            <td class="text-left">{{ $employee->email }}</td>
                                                                            <td>{{ $employee->department->name }}</td>
                                                                            <td>{{ $employee->phone }}</td>
                                                                            <td class="text-center">
                                                                                <button type="button"
                                                                                    class="btn btn-info btn-sm clarification-btn"
                                                                                    data-toggle="modal"
                                                                                    data-target="#clarificationModal"
                                                                                    data-name="{{ $employee->name }}"
                                                                                    data-email="{{ $employee->email }}"
                                                                                    data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                                <!-- <button type="button"
                                              class="btn btn-primary btn-sm email-btn"
                                              data-toggle="modal"
                                              data-target="#emailModal">Email</button> -->
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
                                                        <div class="card card-outline card-info" id="clarify">
                                                            <div class="card-body">
                                                                <table class="table table-bordered">
                                                                    <thead class="text-center">
                                                                        <tr role="row t-body">
                                                                            <th class="text-center sorting_disabled th"
                                                                                aria-label="S.No">S.No</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Proposal Number: activate to sort column ascending">
                                                                                Name</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Budget Type: activate to sort column ascending">
                                                                                Email</th>
                                                                            <th class="text-center sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Initiated By: activate to sort column ascending">
                                                                                Department</th>
                                                                            <th class="text-center sorting"
                                                                                tabindex="0" aria-controls="nv_datatable"

                                                                                aria-label="Initiated Date: activate to sort column ascending">
                                                                                Mobile No</th>
                                                                            <th class="text-center">Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php $count = 1; @endphp
                                                                        @foreach ($employees as $employee)
                                                                        <tr>
                                                                            <td>{{ $count++ }}</td>
                                                                            <td class="text-left">{{ $employee->name }}</td>
                                                                            <td class="text-left">{{ $employee->email }}</td>
                                                                            <td>{{ $employee->department->name }}</td>
                                                                            <td>{{ $employee->phone }}</td>
                                                                            <td class="text-center">
                                                                                <button type="button"
                                                                                    class="btn btn-info btn-sm clarification-btn"
                                                                                    data-toggle="modal"
                                                                                    data-target="#clarificationModal"
                                                                                    data-name="{{ $employee->name }}"
                                                                                    data-email="{{ $employee->email }}"
                                                                                    data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                                <!-- <button type="button"
                                              class="btn btn-primary btn-sm email-btn"
                                              data-toggle="modal"
                                              data-target="#emailModal">Email</button> -->
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
                                                        <div class="card card-outline card-info" id="clarify">
                                                            <div class="card-body">
                                                                <table class="table table-bordered">
                                                                    <thead class="text-center">
                                                                        <tr role="row t-body">
                                                                            <th class="text-center sorting_disabled th"
                                                                                aria-label="S.No">S.No</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Proposal Number: activate to sort column ascending">
                                                                                Name</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Budget Type: activate to sort column ascending">
                                                                                Email</th>
                                                                            <th class="text-center sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Initiated By: activate to sort column ascending">
                                                                                Department</th>
                                                                            <th class="text-center sorting"
                                                                                tabindex="0" aria-controls="nv_datatable"

                                                                                aria-label="Initiated Date: activate to sort column ascending">
                                                                                Mobile No</th>
                                                                            <th class="text-center">Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php $count = 1; @endphp
                                                                        @foreach ($employees as $employee)
                                                                        <tr>
                                                                            <td>{{ $count++ }}</td>
                                                                            <td class="text-left">{{ $employee->name }}</td>
                                                                            <td class="text-left">{{ $employee->email }}</td>
                                                                            <td>{{ $employee->department->name }}</td>
                                                                            <td>{{ $employee->phone }}</td>
                                                                            <td class="text-center">
                                                                                <button type="button"
                                                                                    class="btn btn-info btn-sm clarification-btn"
                                                                                    data-toggle="modal"
                                                                                    data-target="#clarificationModal"
                                                                                    data-name="{{ $employee->name }}"
                                                                                    data-email="{{ $employee->email }}"
                                                                                    data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                                <!-- <button type="button"
                                              class="btn btn-primary btn-sm email-btn"
                                              data-toggle="modal"
                                              data-target="#emailModal">Email</button> -->
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
                                                ->where('material_id', $material_details->id)
                                                ->where('workflow_user_id', $user->id)
                                                ->where('department_id', $user->department_id)
                                                ->where('nv_budget_type', $nv->budget_type)
                                                ->first();
                                                @endphp

                                                @if(!empty($workflow) && $workflow->nv_stage_status == 0)
                                                <div class="row m-3">
                                                    <div class="col-md-12">
                                                        <div class="card card-outline card-info" id="clarify">
                                                            <div class="card-body">
                                                                <table class="table table-bordered">
                                                                    <thead class="text-center">
                                                                        <tr role="row t-body">
                                                                            <th class="text-center sorting_disabled th"
                                                                                aria-label="S.No">S.No</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Proposal Number: activate to sort column ascending">
                                                                                Name</th>
                                                                            <th class="text-left sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Budget Type: activate to sort column ascending">
                                                                                Email</th>
                                                                            <th class="text-center sorting" tabindex="0"
                                                                                aria-controls="nv_datatable"
                                                                                aria-label="Initiated By: activate to sort column ascending">
                                                                                Department</th>
                                                                            <th class="text-center sorting"
                                                                                tabindex="0" aria-controls="nv_datatable"

                                                                                aria-label="Initiated Date: activate to sort column ascending">
                                                                                Mobile No</th>
                                                                            <th class="text-center">Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php $count = 1; @endphp
                                                                        @foreach ($employees as $employee)
                                                                        <tr>
                                                                            <td>{{ $count++ }}</td>
                                                                            <td class="text-left">{{ $employee->name }}</td>
                                                                            <td class="text-left">{{ $employee->email }}</td>
                                                                            <td>{{ $employee->department->name }}</td>
                                                                            <td>{{ $employee->phone }}</td>
                                                                            <td class="text-center">
                                                                                <button type="button"
                                                                                    class="btn btn-info btn-sm clarification-btn"
                                                                                    data-toggle="modal"
                                                                                    data-target="#clarificationModal"
                                                                                    data-name="{{ $employee->name }}"
                                                                                    data-email="{{ $employee->email }}"
                                                                                    data-userid="{{ $employee->user_id }}">Clarification</button>

                                                                                <!-- <button type="button"
                                                    class="btn btn-primary btn-sm email-btn"
                                                    data-toggle="modal"
                                                    data-target="#emailModal">Email</button> -->
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
                                                $materialID=$clarification->material_id;
                                                }

                                                @endphp
                                                @if(!empty($materialID))

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
                                                                                            <th class="text-center sorting_disabled" aria-label="S.No" style="width:20px !important;">S.No</th>
                                                                                            <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" aria-label="Proposal Number: activate to sort column ascending" style="width:60px !important;">Email Sender</th>
                                                                                            <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" aria-label="Budget Type: activate to sort column ascending" style="width:60px !important;">Email Receiver</th>
                                                                                            <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" aria-label="Initiated By: activate to sort column ascending">Clarifiation Remarks</th>
                                                                                            <!-- <th  class="text-center sorting" tabindex="0" aria-controls="nv_datatable"  aria-label="Initiated Date: activate to sort column ascending">Date and Time</th> -->
                                                                                            <th class="text-center sorting" tabindex="0" aria-controls="nv_datatable" aria-label="Initiated By: activate to sort column ascending">Reply</th>
                                                                                            {{-- @if($clarification->receiver_user_id == Auth::user()->user_id) --}}
                                                                                            {{-- <th  class="text-center sorting" tabindex="0" aria-controls="nv_datatable"  aria-label="Initiated Date: activate to sort column ascending">Action</th> --}}
                                                                                            {{-- @endif --}}
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody class="clarification-log">
                                                                                        @php $count = 1; @endphp
                                                                                        @foreach ($clarifications as $clarification)
                                                                                        <tr>
                                                                                            <td style="width:20px !important">{{ $count++ }}</td>
                                                                                            <td class="text-center" style="width:60px !important;">{{ $clarification->users->name }}</td>
                                                                                            <td class="text-center" style="width:60px !important;"> {{ $clarification->user_name }}</td>
                                                                                            <td>
                                                                                                {{-- @if(!empty($clarification->attachment))
                                                                            <a class="ml-2" download="{{ $clarification->attachment}}" href="{{ url(asset('clarification-file/' . $clarification->attachment)) }}">{{$clarification->attachment}}</a>
                                                                                                <br> @endif --}}

                                                                                                @if(!empty($clarification->attachment))
                                                                                                @php
                                                                                                $attachments = json_decode($clarification->attachment, true);
                                                                                                @endphp

                                                                                                @if(is_array($attachments))
                                                                                                @foreach($attachments as $file)
                                                                                                <a class="ml-2" download="{{ $file }}" href="{{ asset('clarification-file/' . $file) }}">{{ $file }}</a><br>
                                                                                                @endforeach
                                                                                                @else
                                                                                                <a class="ml-2" download="{{ $clarification->attachment }}" href="{{ asset('clarification-file/' . $clarification->attachment) }}">{{ $clarification->attachment }}</a><br>
                                                                                                @endif
                                                                                                @endif
                                                                                                @if(!empty($clarification->clarification_remark))
                                                                                                {{date('d-M-Y h:i:s A', strtotime($clarification->created_at))}}<br>
                                                                                                <span class="clarification-cell text-center" data-full-text="{!! $clarification->clarification_remark !!}">

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
                                                                                                {{-- @if(!empty($clarification->reply_attachment))
                                                                                <a class="ml-2" download="{{ $clarification->reply_attachment}}" href="{{ url(asset('clarification-file/' . $clarification->reply_attachment)) }}">{{$clarification->reply_attachment}}</a>
                                                                                                <br> @endif --}}
                                                                                                @if(!empty($clarification->reply_attachment))
                                                                                                @php
                                                                                                $attachments = json_decode($clarification->reply_attachment, true);
                                                                                                @endphp

                                                                                                @if(is_array($attachments))
                                                                                                @foreach($attachments as $file)
                                                                                                <a class="ml-2" download="{{ $file }}" href="{{ asset('clarification-file/' . $file) }}">{{ $file }}</a><br>
                                                                                                @endforeach
                                                                                                @else
                                                                                                <a class="ml-2" download="{{ $clarification->reply_attachment }}" href="{{ asset('clarification-file/' . $clarification->reply_attachment) }}">{{ $clarification->reply_attachment }}</a><br>
                                                                                                @endif
                                                                                                @endif
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
                                                                                            {{-- <td > 
                                                                            {{date('d-M-Y h:i:s A', strtotime($clarification->reply_timestamp))}}
                                                                                            <br>{!! $clarification->clarification_remark_reply !!}</td> --}}
                                                                                            {{-- @if($clarification->receiver_user_id == Auth::user()->user_id) --}}
                                                                                            {{-- <td class="text-center">
                                                                                <button type="button"
                                                                                    class="btn btn-info btn-sm clarification-reply"
                                                                                    data-toggle="modal"
                                                                                    data-target="#clarificationReplyModal"
                                                                                    >Reply</button>
                                                                            </td> --}}
                                                                                            {{-- @endif --}}
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
                                                                                    <input type="file" class="form-control" id="attachment" name="attachment[]" multiple style="width:50%;"></label><br>
                                                                                    <label
                                                                                        for="exampleFormControlInput1">Clarification
                                                                                        Remark</label>
                                                                                    <textarea name="clarification_remark" id="clarification_remark" rows="10" cols="80"></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row text-center" style="margin-top:-20px;">
                                                                        <div class="col-12">
                                                                            <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                                                                            <button class="sendEmailBtn btn btn-primary"
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
                                                <div class="modal fade" id="emailModal" tabindex="-1" role="dialog"
                                                    aria-labelledby="emailModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="emailModalLabel"><b>Email</b></h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row m-3">
                                                                    <div class="col-md-12">
                                                                        <div class="card card-outline card-info" style="margin-top:-20px;">
                                                                            <div class="card-body">
                                                                                <div class="form-group">
                                                                                    <label for="toField">To:</label>
                                                                                    <input type="text" class="form-control"
                                                                                        id="toField" name="toField">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="ccField">CC:</label>
                                                                                    <input type="text" class="form-control"
                                                                                        id="ccField" name="ccField">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="subjectField">Subject:</label>
                                                                                    <input type="text" class="form-control"
                                                                                        id="subjectField" name="subjectField">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="exampleFormControlInput1">Email
                                                                                        Remark</label>
                                                                                    <textarea name="email_remark" id="email_remark" rows="10" cols="80"></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row text-center" style="margin-top:-20px;">
                                                                    <div class="col-12">
                                                                        <button type="button"
                                                                            class="btn btn-primary sendEmailBtn1">Send Email</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {{-- <div class="modal-footer">
                                                        <button type="button"
                                                            class="btn btn-primary sendEmailBtn1">Send Email</button>

                                                    </div> --}}
                                                        </div>
                                                    </div>
                                                </div>



                                                {{-- <div class="row m-3">
                                            <table class=" m-3">
                                                @foreach ($chats as $chat)
                                                    <tr>
                                                        <td>
                                                            @php
                                                                $string = strip_tags(preg_replace('/\s+/', ' ', $chat->message));
                                                                
                                                            @endphp

                                                            <a href="#"> DOP #{{ $chat->dop_ref_no }} :
                                                {{ $string }}</a><br>
                                                {{ date('d/m/y', strtotime($chat->created_at)) }}
                                                {{ date('H:i', strtotime($chat->created_at)) }} Updated by :
                                                {{ $chat->users->name }} <br><br>

                                                </td>
                                                </tr>
                                                @endforeach
                                                </table>
                                            </div> --}}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="nv_id" id='nv_id'
                                    value="{{ $material_details->nv_id }}">
                                <input type="hidden" name="serviceID" id='serviceID'
                                    value="{{ $material_details->id }}">
                                <input type="hidden" name="material_id" id='material_id'
                                    value="{{ $material_details->id }}">
                                <input type="hidden" name="dop_ref_no" id='dop_ref_no'
                                    value="{{ $material_details->dop }}">
                                <input type="hidden" name="prop_name" id='prop_name'
                                    value="{{ $material_details->proposal_name ?? '' }}">

                                <input type="hidden" name="derc_infosfsdsfds" id='derc_info'
                                    value="{{ $Nvsericestatus->derc_info }}">
                            </div>


                            <input type="hidden" id="budget_type" value="{{$nv->budget_type}}">
                            <input type="hidden" id="fiscal_year" value="{{$nv->fiscal_year}}">
                            <input type="hidden" id="services_id" value="{{$nv->service_id}}">
                            <input type="hidden" id="department_name" value="{{getDepartmentName($nv->department_id)}}">
                            <input type="hidden" id="nvid" value="{{$nv->id}}">
                </form>


            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        @if ($user->role_id != 9)


                        @if ($Nvsericestatus->rv1_status == 0 && $Nvsericestatus->draft == 1)
                        @if (!empty($rv1) && $rv1 == $user->id)
                        <button type="button" class="btn btn-info save-button" value="Save">Save</button>
                        <button type="button" class="btn btn-info approve-button"
                            value="Approve">Approve</button>
                        <button type="button" class="btn btn-danger reject-button"
                            value="Reject">Reject</button>
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
                        <button type="button" class="btn btn-info save-button" value="Save">Save</button>
                        <button type="button" class="btn btn-info approve-button"
                            value="Approve">Approve</button>
                        <button type="button" class="btn btn-danger reject-button"
                            value="Reject">Reject</button>
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
                        <button type="button" class="btn btn-info save-button" value="Save">Save</button>
                        <button type="button" class="btn btn-info approve-button"
                            value="Approve">Approve</button>
                        <button type="button" class="btn btn-danger reject-button"
                            value="Reject">Reject</button>
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
                        <button type="button" class="btn btn-info save-button" value="Save">Save</button>
                        <button type="button" class="btn btn-info approve-button"
                            value="Approve">Approve</button>
                        <button type="button" class="btn btn-danger reject-button"
                            value="Reject">Reject</button>
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
                        <button type="button" class="btn btn-info save-button" value="Save">Save</button>
                        <button type="button" class="btn btn-info approve-button"
                            value="Approve">Approve</button>
                        <button type="button" class="btn btn-danger reject-button"
                            value="Reject">Reject</button>
                        @endif
                        @endif

                        @if (!empty($group_cio) && $group_cio == $user->id)
                        @if ($Nvsericestatus->hod_status == 1 && $Nvsericestatus->groupcio_status == 0)
                        <button type="button" class="btn btn-info save-button" value="Save">Save</button>
                        <button type="button" class="btn btn-info approve-button"
                            value="Approve">Forward</button>
                        <button type="button" class="btn btn-danger reject-button"
                            value="Reject">Reject</button>
                        @endif
                        @endif

                        @if(!empty($workflow) && ($workflow->nv_stage_status == 0) && ($Nvsericestatus->is_reject == 0))
                        <button type="button" class="btn btn-info save-button" value="Save">Save</button>
                        <button type="button" class="btn btn-info approve-button" value="Approve">Approve</button>
                        <button type="button" class="btn btn-danger reject-button" value="Reject">Reject</button>
                        @endif
                        @endif
                        <!-- <a target="_blank" href="https://accounts.zoho.in/signin?servicename=ZohoSign&signupurl=https://www.zoho.com/sign/signup.html"><button type="button" class="btn btn-info">Zoho Sign</button></a> -->
                    </div>
                </div>
            </div>

        </div>


    </div>
    <!-- /.container-fluid -->
</section>




{{--
<div id="myRadioGroup">
    <label for="exampleFormControlInput1">Is there any Capacity addition</label> <br>
    Yes &nbsp; <input type="radio" name="cars-first" value="4" />&nbsp; &nbsp; |&nbsp; &nbsp;
    No &nbsp;<input type="radio" name="cars-first" value="5" checked="checked" />
    <br>
    <br>
    <div id="First4" class="first-radio">first</div>
    <div id="First5" class="first-radio" style="display: none;">Second</div>
</div>
--}}
@endsection

@push('script')



<script>
    function sumSolarCap_2() {

        var totalMatMat = document.getElementById('total_mat_mat');
        var totalSerAmo = document.getElementById('total_ser_amo');
        var estimateSerAmoCivil = document.getElementById('estimate_amount_of_service_civil');
        var estimateAmountOfService = document.getElementById('estimate_amount_of_service');
        var estimateAmountOfRRCharge = document.getElementById('estimate_amount_of_rr_charge');
        var estimateAmountOther = document.getElementById('estimate_amount_other');
        var totalCivilandRR = document.getElementById('total_budget_service');
        var totalBudgetNV = document.getElementById('total_budget_both');


        var sum = Number(totalMatMat.value) + Number(totalSerAmo.value) + Number(estimateAmountOfService.value) +
            Number(estimateAmountOfRRCharge.value) + Number(estimateSerAmoCivil.value) + Number(totalCivilandRR.value) +
            Number(estimateAmountOther.value);
        totalBudgetNV.value = sum;
    }


    window.addEventListener('keyup', sumSolarCap_2);

    sumSolarCap_2();
</script>
<script>
    $(document).ready(function() {
        $('#add , #add1, #add3').click(function() {
            $('#details , #details1 , #details3').toggle();
        });
    });
    $(document).ready(function() {
        $("div.desc").hide();
        $("input[name$='cars']").click(function() {
            var test = $(this).val();

            $("div.desc").hide();
            $("#Cars" + test).show();
        });
        if ($("#CheckYes").is(":checked")) {
            $("#Cars2").show();
        } else {
            $("#Cars2").hide();
        }
    });




    // first radio Button
    $(document).ready(function() {
        $("div.first-radio").hide();
        $("input[name$='cars-first']").click(function() {
            var test = $(this).val();

            $("div.first-radio").hide();
            $("#First" + test).show();
        });
        if ($("#chkYes").is(":checked")) {
            $("#First4").show();
        } else {
            $("#First4").hide();
        }
    });

    $(document).ready(function() {
        $('#nv_datatable_length').remove();
        $('#nv_serviceBoq_datatable_length').remove();
        $('.toggle-btn[data-option="collapse"]').addClass(
            'active'); // Set the "Collapse" button as active by default

        $('.toggle-btn').click(function() {
            var option = $(this).data('option');
            var table = $('#nv_datatable1').DataTable();
            var columns = table.columns().indexes();


            // Expand or collapse columns from index 8 to 19 (April to March) based on the selected option
            if (option === 'expand') {
                for (var i = 8; i <= 19; i++) {
                    table.column(columns[i]).visible(true);
                }
                table.column(20).visible(false);
            } else if (option === 'collapse') {
                for (var i = 8; i <= 19; i++) {
                    table.column(columns[i]).visible(false);
                }
                table.column(20).visible(false);
            }
        });

    });

    $(document).ready(function() {

        // Datatable
        if ($(document).find('#nv_datatable1').length > 0) {
            var nv_id = $('#nv_id').val();
            var service_id = $('#serviceID').val();
            $('#nv_datatable1').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                "searching": true,
                dom: 'Bfrtip',
                "ordering": false,
                buttons: [{
                        extend: 'excelHtml5',
                        text: 'Export Excel',
                        exportOptions: {
                            columns: ':visible:not(.exclude-export)'
                        },
                        customize: function(xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            $('row c[r^="C"]', sheet).attr('s', '2'); // Style column C
                        },
                        action: function(e, dt, button, config) {
                            var self = this;
                            var oldStart = dt.settings()[0]._iDisplayStart;

                            dt.one('preXhr', function(e, s, data) {
                                data.start = 0;
                                data.length = -1; // fetch all rows

                                dt.one('preDraw', function(e, settings) {
                                    // Do the export
                                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);

                                    // Reset paging
                                    settings._iDisplayStart = oldStart;
                                    data.start = oldStart;
                                    dt.one('preDraw', function() {
                                        dt.ajax.reload(null, false); // reload old page
                                        return false;
                                    });
                                    return false;
                                });
                            });

                            dt.ajax.reload();
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'Export CSV',
                        exportOptions: {
                            columns: ':visible:not(.exclude-export)'
                        },
                        action: function(e, dt, button, config) {
                            var self = this;
                            var oldStart = dt.settings()[0]._iDisplayStart;

                            dt.one('preXhr', function(e, s, data) {
                                data.start = 0;
                                data.length = -1;

                                dt.one('preDraw', function(e, settings) {
                                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config);

                                    settings._iDisplayStart = oldStart;
                                    data.start = oldStart;
                                    dt.one('preDraw', function() {
                                        dt.ajax.reload(null, false);
                                        return false;
                                    });
                                    return false;
                                });
                            });

                            dt.ajax.reload();
                        }
                    }
                ],
                ajax: {
                    data: {
                        nv_id: nv_id,
                        service_id: service_id,
                    },
                    url: '/admin/nv_materialBoq'
                },
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
                        className: "text-center exclude-export"
                    },
                    {
                        data: 'material_code',
                        name: 'material_code',
                        className: "text-center"
                    },
                    {
                        data: 'material_short_text',
                        name: 'material_short_text',
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
                        className: "text-center",
                        render: function(data, type, full, meta) {
                            if (data) {
                                return new Intl.NumberFormat('en-IN', {
                                    style: 'currency',
                                    currency: 'INR',
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }).format(data);
                            }
                            return "₹0.00";
                        }
                    },

                    {
                        data: 'quantity',
                        name: 'quantity',
                        className: "text-center",
                        render: function(data, type, full, meta) {
                            var formattedquantity = parseFloat(data).toFixed(3); // Convert to float and format to 2 decimal places
                            return formattedquantity;
                        }
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        className: "text-center",
                        render: function(data, type, full, meta) {
                            if (data) {
                                return new Intl.NumberFormat('en-IN', {
                                    style: 'currency',
                                    currency: 'INR',
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }).format(data);
                            }
                            return "₹0.00";
                        }
                    },

                    {
                        data: 'rate_reference',
                        name: 'rate_reference',
                        className: "text-left",
                        render: function(data, type, full, meta) {
                            var rateReferenceValue = data;
                            if (rateReferenceValue === 0) {
                                return 'Vendor Quotation';
                            } else if (rateReferenceValue === 1) {
                                return 'Last Work Order';
                            } else if (rateReferenceValue === 2) {
                                return 'C&M Rate Reference';
                            } else if (rateReferenceValue === 4) {
                                return 'Rate Reference';
                            } else {
                                return 'User Estimation';
                            }
                        }
                    },
                    {
                        data: null,
                        name: 'year_combined',
                        className: "text-left",
                        render: function(data, type, full, meta) {
                            // Get the selected year from the dropdown
                            var selectedYear = parseInt($("#exampleFormControlSelect").val());

                            if (isNaN(selectedYear) || selectedYear <= 0) {
                                return ''; // Return an empty string if no year is selected
                            }

                            // Get the selected date from the input field
                            var selectedDate = $("#imp_from").val();
                            var selectedDateYear = selectedDate ? new Date(selectedDate).getFullYear() : null;

                            if (selectedDateYear === null) {
                                return selectedYear.toString(); // Display the selected year if no date is selected
                            }

                            var yearArray = [];
                            for (let i = 0; i < selectedYear; i++) {
                                var startYear = selectedDateYear + i;
                                var endYear = startYear + 1;
                                yearArray.push(startYear.toString() + '-' + endYear.toString());
                            }

                            return yearArray.join('<hr>');
                        }
                    },
                    {
                        data: null,

                        name: 'april_combined',

                        className: "text-center",

                        render: function(data, type, full, meta) {

                            var selectedYear = parseInt($("#exampleFormControlSelect").val());

                            const aprilDataArray = [];

                            for (let i = 1; i <= selectedYear; i++) {

                                // Replace null with 0

                                var aprilValue = data['april' + i] !== null ? parseFloat(data['april' + i]).toFixed(2) : '0.00'; // Fix to 2 decimal places

                                aprilDataArray.push(aprilValue);

                            }

                            // Replace black with 0 in the joined string

                            const joinedData = aprilDataArray.join('<hr>').replace(/black/g, '0');

                            // Split the joinedData by '<hr>' to create an array of values

                            const dataArray = joinedData.split('<hr>');

                            // Join all values to get the final result

                            const result = dataArray.join('<hr>');

                            return result;

                        }

                    },
                    {
                        data: null,

                        name: 'may_combined',

                        className: "text-center",

                        render: function(data, type, full, meta) {

                            var selectedYear = parseInt($("#exampleFormControlSelect").val());

                            const mayDataArray = [];

                            for (let i = 1; i <= selectedYear; i++) {

                                // Replace null with 0

                                var mayValue = data['may' + i] !== null ? parseFloat(data['may' + i]).toFixed(2) : '0.00'; // Fix to 2 decimal places

                                mayDataArray.push(mayValue);

                            }

                            // Replace black with 0 in the joined string

                            const joinedData = mayDataArray.join('<hr>').replace(/black/g, '0');

                            // Split the joinedData by '<hr>' to create an array of values

                            const dataArray = joinedData.split('<hr>');

                            // Join all values to get the final result

                            const result = dataArray.join('<hr>');

                            return result;

                        }
                    },
                    {
                        data: null,

                        name: 'june_combined',

                        className: "text-center",

                        render: function(data, type, full, meta) {

                            var selectedYear = parseInt($("#exampleFormControlSelect").val());



                            const juneDataArray = [];

                            for (let i = 1; i <= selectedYear; i++) {

                                // Replace null with 0

                                var juneValue = data['june' + i] !== null ? parseFloat(data['june' + i]).toFixed(2) : '0.00';

                                juneDataArray.push(juneValue);

                            }



                            // Replace black with 0 in the joined string

                            const joinedData = juneDataArray.join('<hr>').replace(/black/g, '0');



                            // Split the joinedData by '<hr>' to create an array of values

                            const dataArray = joinedData.split('<hr>');



                            // Join all values to get the final result

                            const result = dataArray.join('<hr>');



                            return result;

                        }

                    },
                    {
                        data: null,

                        name: 'july_combined',

                        className: "text-center",

                        render: function(data, type, full, meta) {

                            var selectedYear = parseInt($("#exampleFormControlSelect").val());



                            const julyDataArray = [];

                            for (let i = 1; i <= selectedYear; i++) {

                                // Replace null with 0

                                var julyValue = data['july' + i] !== null ? parseFloat(data['july' + i]).toFixed(2) : '0.00';

                                julyDataArray.push(julyValue);

                            }



                            // Replace black with 0 in the joined string

                            const joinedData = julyDataArray.join('<hr>').replace(/black/g, '0');



                            // Split the joinedData by '<hr>' to create an array of values

                            const dataArray = joinedData.split('<hr>');



                            // Join all values to get the final result

                            const result = dataArray.join('<hr>');



                            return result;

                        }
                    },
                    {
                        data: null,

                        name: 'august_combined',

                        className: "text-center",

                        render: function(data, type, full, meta) {

                            var selectedYear = parseInt($("#exampleFormControlSelect").val());



                            const augustDataArray = [];

                            for (let i = 1; i <= selectedYear; i++) {

                                // Replace null with 0

                                var augustValue = data['august' + i] !== null ? parseFloat(data['august' + i]).toFixed(2) : '0.00';

                                augustDataArray.push(augustValue);

                            }



                            // Replace black with 0 in the joined string

                            const joinedData = augustDataArray.join('<hr>').replace(/black/g, '0');



                            // Split the joinedData by '<hr>' to create an array of values

                            const dataArray = joinedData.split('<hr>');



                            // Join all values to get the final result

                            const result = dataArray.join('<hr>');



                            return result;

                        }

                    },
                    {
                        data: null,

                        name: 'september_combined',

                        className: "text-center",

                        render: function(data, type, full, meta) {

                            var selectedYear = parseInt($("#exampleFormControlSelect").val());



                            const septemberDataArray = [];

                            for (let i = 1; i <= selectedYear; i++) {

                                // Replace null with 0

                                var septemberValue = data['september' + i] !== null ? parseFloat(data['september' + i]).toFixed(2) : '0.00';

                                septemberDataArray.push(septemberValue);

                            }



                            // Replace black with 0 in the joined string

                            const joinedData = septemberDataArray.join('<hr>').replace(/black/g, '0');



                            // Split the joinedData by '<hr>' to create an array of values

                            const dataArray = joinedData.split('<hr>');



                            // Join all values to get the final result

                            const result = dataArray.join('<hr>');



                            return result;

                        }

                    },

                    {

                        data: null,

                        name: 'oct_combined',

                        className: "text-center",

                        render: function(data, type, full, meta) {

                            var selectedYear = parseInt($("#exampleFormControlSelect").val());



                            const octDataArray = [];

                            for (let i = 1; i <= selectedYear; i++) {

                                // Replace null with 0

                                var octValue = data['oct' + i] !== null ? parseFloat(data['oct' + i]).toFixed(2) : '0.00';

                                octDataArray.push(octValue);

                            }



                            // Replace black with 0 in the joined string

                            const joinedData = octDataArray.join('<hr>').replace(/black/g, '0');



                            // Split the joinedData by '<hr>' to create an array of values

                            const dataArray = joinedData.split('<hr>');



                            // Join all values to get the final result

                            const result = dataArray.join('<hr>');



                            return result;

                        }

                    },

                    {

                        data: null,

                        name: 'nov_combined',

                        className: "text-center",

                        render: function(data, type, full, meta) {

                            var selectedYear = parseInt($("#exampleFormControlSelect").val());



                            const novDataArray = [];

                            for (let i = 1; i <= selectedYear; i++) {

                                // Replace null with 0

                                var novValue = data['nov' + i] !== null ? parseFloat(data['nov' + i]).toFixed(2) : '0.00';

                                novDataArray.push(novValue);

                            }



                            // Replace black with 0 in the joined string

                            const joinedData = novDataArray.join('<hr>').replace(/black/g, '0');



                            // Split the joinedData by '<hr>' to create an array of values

                            const dataArray = joinedData.split('<hr>');



                            // Join all values to get the final result

                            const result = dataArray.join('<hr>');



                            return result;

                        }

                    },

                    {

                        data: null,

                        name: 'dec_combined',

                        className: "text-center",

                        render: function(data, type, full, meta) {

                            var selectedYear = parseInt($("#exampleFormControlSelect").val());



                            const decDataArray = [];

                            for (let i = 1; i <= selectedYear; i++) {

                                // Replace null with 0

                                var decValue = data['dec' + i] !== null ? parseFloat(data['dec' + i]).toFixed(2) : '0.00';

                                decDataArray.push(decValue);

                            }



                            // Replace black with 0 in the joined string

                            const joinedData = decDataArray.join('<hr>').replace(/black/g, '0');



                            // Split the joinedData by '<hr>' to create an array of values

                            const dataArray = joinedData.split('<hr>');



                            // Join all values to get the final result

                            const result = dataArray.join('<hr>');



                            return result;

                        }

                    },

                    {

                        data: null,

                        name: 'jan_combined',

                        className: "text-center",

                        render: function(data, type, full, meta) {

                            var selectedYear = parseInt($("#exampleFormControlSelect").val());



                            const janDataArray = [];

                            for (let i = 1; i <= selectedYear; i++) {

                                // Replace null with 0

                                var janValue = data['jan' + i] !== null ? parseFloat(data['jan' + i]).toFixed(2) : '0.00';

                                janDataArray.push(janValue);

                            }



                            // Replace black with 0 in the joined string

                            const joinedData = janDataArray.join('<hr>').replace(/black/g, '0');



                            // Split the joinedData by '<hr>' to create an array of values

                            const dataArray = joinedData.split('<hr>');



                            // Join all values to get the final result

                            const result = dataArray.join('<hr>');



                            return result;

                        }

                    },

                    {

                        data: null,

                        name: 'feb_combined',

                        className: "text-center",

                        render: function(data, type, full, meta) {

                            var selectedYear = parseInt($("#exampleFormControlSelect").val());



                            const febDataArray = [];

                            for (let i = 1; i <= selectedYear; i++) {

                                // Replace null with 0

                                var febValue = data['feb' + i] !== null ? parseFloat(data['feb' + i]).toFixed(2) : '0.00';

                                febDataArray.push(febValue);

                            }



                            // Replace black with 0 in the joined string

                            const joinedData = febDataArray.join('<hr>').replace(/black/g, '0');



                            // Split the joinedData by '<hr>' to create an array of values

                            const dataArray = joinedData.split('<hr>');



                            // Join all values to get the final result

                            const result = dataArray.join('<hr>');



                            return result;

                        }

                    },

                    {

                        data: null,

                        name: 'march_combined',

                        className: "text-center",

                        render: function(data, type, full, meta) {

                            var selectedYear = parseInt($("#exampleFormControlSelect").val());



                            const marchDataArray = [];

                            for (let i = 1; i <= selectedYear; i++) {

                                // Replace null with 0

                                var marchValue = data['march' + i] !== null ? parseFloat(data['march' + i]).toFixed(2) : '0.00';

                                marchDataArray.push(marchValue);

                            }



                            // Replace black with 0 in the joined string

                            const joinedData = marchDataArray.join('<hr>').replace(/black/g, '0');



                            // Split the joinedData by '<hr>' to create an array of values

                            const dataArray = joinedData.split('<hr>');



                            // Join all values to get the final result

                            const result = dataArray.join('<hr>');



                            return result;

                        }

                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: "text-center",
                        orderable: false,
                        visible: false
                    },




                ],
                "bLengthChange": false,
                "bInfo": false,
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
                        className: "text-center",
                        render: function(data, type, row) {
                            if (data) {
                                return new Intl.NumberFormat('en-IN', {
                                    style: 'currency',
                                    currency: 'INR',
                                    maximumFractionDigits: 2
                                }).format(data);
                            }
                            return data;
                        }
                    },

                    {
                        data: 'qty',
                        name: 'qty',
                        className: "text-center"
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        className: "text-center",
                        render: function(data, type, row) {
                            if (data) {
                                return new Intl.NumberFormat('en-IN', {
                                    style: 'currency',
                                    currency: 'INR',
                                    maximumFractionDigits: 2
                                }).format(data);
                            }
                            return data;
                        }
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
    });
</script>
<script src="{{ asset('admin/js/nvmaterials.js') }}"></script>
<script src="{{ asset('theme/plugins/ckeditor/ckeditor.js') }}"></script>
{{--
<script src="{{ asset('theme/plugins/summernote/summernote.js') }}"></script> --}}


<script src="{{ asset('theme/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Replace the <textarea id="editor1"> with a CKEditor 4
    CKEDITOR.replace('clarification_remark');
    CKEDITOR.replace('email_remark');
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
            //$('#clarification_id').val(clarificationId);
            $('#clarificationModal').find('#clarification_name').val(email).trigger('change');
            $('#clarificationModal').find('#clarification_email').val(email);
        });

        // Clear the modal data when the modal is closed
        $('#clarificationModal').on('hidden.bs.modal', function() {
            $('#clarification_name').val('').trigger('change');
            $('#clarification_email').val('');
            $('#clarification_remark').val('');
        });
        $('#emailModal').on('hidden.bs.modal', function() {
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
<!-- clarification  Model-->
<script>
    $(document).ready(function() {
        $('.sendEmailBtn').click(function() {
            var selectedEmails = $('#clarification_name').val();
            var clarificationRemark = CKEDITOR.instances['clarification_remark'].getData();
            var nv_id = $('#nv_id').val();
            var material_id = $('#material_id').val();
            var user_name = $('#user_name').val();
            var userId = $('#receiver_user_id').val();
            var proposal_name = $('#prop_name').val();
            var formData = new FormData();
            formData.append('emails', selectedEmails);
            formData.append('remark', clarificationRemark);
            formData.append('nv_id', nv_id);
            formData.append('material_id', material_id);
            formData.append('user_name', user_name);
            formData.append('user_id', userId);
            formData.append('proposal_name', proposal_name);
            formData.append('is_replied', 0);
            // var files = $('#attachment')[0].files;

            // for (let i = 0; i < files.length; i++) {
            //     formData.append('attachments[]', files[i]);
            // }
            formData.append('file', $('#attachment')[0].files[0]); 

            // Perform an AJAX request to send the email
            $.ajax({
                url: '/admin/send-clarification',
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
</script>
<!-- Email Model-->
<script>
    $(document).ready(function() {

        $('#emailModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var email = button.data('email');
            // Set the email value in the "To" field
            $('#toField').val(email);
        });

        $('.sendEmailBtn1').click(function() {

            var toField = $('#toField').val();
            var ccField = $('#ccField').val();
            var subjectField = $('#subjectField').val();
            var emailRemark = CKEDITOR.instances['email_remark'].getData();
            // Perform an AJAX request to send the email
            $.ajax({
                url: '/admin/send-email2',
                type: 'POST',
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
                        $('#emailModal').modal('hide');

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



    $(document).on("change", ".after-add-more-third input[name='imp_from[]']", function() {
        const fromValue = $(this).val();
        const impToInput = $(this).parent().parent().next().find("input[name='imp_to[]']");
        if (fromValue) {
            impToInput.attr("min", fromValue);
        } else {
            impToInput.attr("min", new Date().toISOString().split("T")[0]);
        }
    });
    $(document).on("click", ".add-more-third", function() {
        setMinimumDates();
    });
    setMinimumDates();

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    today = yyyy + '-' + mm + '-' + dd;
    document.getElementById("derc_app_date").setAttribute("max", today);
</script>
<script>
    // Function to populate duplicated columns with data
    function populateDuplicatedColumns(data) {
        $(".duplicated-column").each(function(index) {

            // Populate each duplicated column with the appropriate data
            const columnData = data[index]; // Assuming data is an array of objects

            $(this).find(".form-control").each(function() {
                const columnName = $(this).attr("name");
                // Assuming the properties in the 'columnData' object have the same name as the input fields
                $(this).val(columnData[columnName]);
            });
        });
    }

    function duplicateColumns() {
        const selectedYear = parseInt($("#exampleFormControlSelect").val());

        // Retrieve data from the database for the selected year (assuming 'materialData' is the object containing all the data)
        const materialData = <?php echo json_encode($material_details); ?>;

        // Update the year values dynamically based on the selected year
        const yearArray = [];
        for (let i = 1; i <= selectedYear; i++) {
            yearArray.push(i.toString());
        }

        // Hide the container if no year is selected or value is not valid
        if (isNaN(selectedYear) || selectedYear <= 0) {
            $("#duplicateContainer").hide();
            return; // Stop further execution
        }

        // Remove any previously duplicated columns
        $(".duplicated-column").remove();

        // Check if we have data for the selected year (editing scenario)
        const selectedYearData = materialData[selectedYear];
        if (selectedYearData) {
            // Generate duplicated columns based on existing data
            const originalColumns = $(".original-columns").children();
            for (let i = 1; i < selectedYear; i++) {
                // Clone the original columns
                const clonedColumns = originalColumns.clone();

                // Add a class to identify duplicated columns
                clonedColumns.addClass("duplicated-column");

                // Append the duplicated columns to the container
                $("#duplicateContainer").append(clonedColumns);
            }

            // Populate the duplicated columns with data
            populateDuplicatedColumns(materialData);
        } else {
            // Generate empty duplicated columns for new creation
            const originalColumns = $(".original-columns").children();
            for (let i = 1; i < selectedYear; i++) {
                // Clone the original columns
                const clonedColumns = originalColumns.clone();

                // Add a class to identify duplicated columns
                clonedColumns.addClass("duplicated-column");

                // Clear the input values in the duplicated columns for new creation
                clonedColumns.find(".form-control").val('');

                // Append the duplicated columns to the container
                $("#duplicateContainer").append(clonedColumns);
            }
        }

        // Show the container after duplicating columns
        $("#duplicateContainer").show();
    }

    // Call the duplicateColumns function only when the page loads
    $(document).ready(function() {
        // This will ensure that the duplicateColumns function is called once on page load
        // and the duplicated columns are generated based on the initial selected year
        duplicateColumns();
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
</script>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector("#background")).then(editor => {
        editor.enableReadOnlyMode("editor");
        console.log(editor);
    }).catch(error => {
        console.error(error);
    });
    ClassicEditor.create(document.querySelector("#just_Prop")).then(editor => {
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
<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ asset('theme/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('admin/js/brand.js') }}"></script>

{{-- New Datatables --}}
<script src="{{asset('theme/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script>
    $(document).ready(function() {

        var budgetavl = $('#budget_avl').val();

        budgetavl = parseInt(budgetavl);

        var totalMatMat = parseInt($('#total_mat_mat').val() || 0);

        // alert(totalMatMat);

        var diff = budgetavl - totalMatMat;

        diff = diff.toLocaleString('en-IN');

        // alert(diff)

        const budget = document.getElementById('ser_budget_avl');

        if (budget.value !== diff.toString()) {

            var total13 = diff.toLocaleString('en-IN');

            $("#ser_budget_avl").val(total13);

        }
    });
</script>
<!-- <script>

     $(document).ready(function(){

         var budgetavl = $('#budget_avl').val();

         budgetavl = parseInt(budgetavl);

         var totalMatMat = parseInt($('#total_mat_mat').val() || 0);

        //   alert(totalMatMat);

         var diff = budgetavl - totalMatMat;

         diff = diff.toLocaleString('en-IN');

        //   alert(diff);

         const budget =  document.getElementById('ser_budget_avl');

         if (budget.value !== diff.toString()) {

              var total13 = diff.toLocaleString('en-IN');

             $("#ser_budget_avl").val(total13);

         }

         var mat_tot = parseInt($("#total_mat_mat").val() || 0).toLocaleString('en-IN');

         $("#total_mat_mat").val(mat_tot);

         var ser_tot = parseInt($("#total_amt_service").val() || 0).toLocaleString('en-IN');

         $("#total_amt_service").val(ser_tot);

         var budget_mat = parseInt($("#budget_avl").val() || 0).toLocaleString('en-IN');

         $("#budget_avl").val(budget_mat);

         var tot_bud_ser = parseInt($("#total_budget_service").val() || 0).toLocaleString('en-IN');

         $("#total_budget_service").val(tot_bud_ser);

         var tot_bud_both = parseInt($("#total_budget_both").val() || 0).toLocaleString('en-IN');

         $("#total_budget_both").val(tot_bud_both);

        

 

     });

 </script> -->

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

            var fileData = $(this).data('files');
            if (!fileData) return;

            var files = fileData.split(',');
            downloadFiles(files);
        });

        function downloadFiles(files) {
            var link = document.createElement('a');
            link.style.display = 'none';

            document.body.appendChild(link);

            for (var i = 0; i < files.length; i++) {
                link.setAttribute('href', '{{ url('
                    materials - doc / ') }}/' + files[i]);
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
                var material_id = $('#material_id').val();
                var user_name = $('#user_name').val();
                var userId = $('#receiver_user_id').val();
                var clarificationId = $('#clarification_id').val();
                var replyMessage = $('#replyMessage').val()
                var formData = new FormData();
                formData.append('emails', selectedEmails);
                // formData.append('material_id', material_id);
                formData.append('nv_id', nv_id);
                formData.append('material_id', material_id);
                formData.append('user_name', user_name);
                formData.append('user_id', userId);
                formData.append('clarificationId', clarificationId);
                formData.append('remark', replyMessage);
                formData.append('is_replied', 1);
                var files = $('#replyattachment')[0].files;

                for (let i = 0; i < files.length; i++) {
                    formData.append('attachments[]', files[i]);
                }
                // formData.append('file', $('#replyattachment')[0].files[0]); 
                // alert(userId);

                // alert(clarificationRemark);
                // Perform an AJAX request to send the email
                $.ajax({
                    url: '/admin/send-email',
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
    $('#cpmg1').change(function() {
        val = $(this).val();
        alert(val);
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.yes-no-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    // Uncheck other checkboxes with the same name
                    document.querySelectorAll(`input[name="${this.name}"]`).forEach(otherCheckbox => {
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

{{-- --}}
