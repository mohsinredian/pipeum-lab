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
</style>
@push('styles')
<link rel="stylesheet" href="{{ asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush

@extends('admin.layout.master', ['page_title' => 'Preview NV Service'])



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
                            <h3 class="card-label">NV-Service Details</h3>
                        </div>

                        <div class="card-toolbar">
                            <button type="reset" class="btn btn-primary " onclick="history.back();">Back »</button>

                        </div>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form id="preview_nvservice">
                        @csrf

                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Department Name</label>
                                        @if (!empty($service_details->dept_id))
                                        <input readonly type="text" class="form-control" name="dept_id" id="dept_id"
                                            value="{{ $service_details->department->name }}">
                                        @else
                                        <input readonly type="text" class="form-control" name="dept_id" id="dept_id"
                                            value="N/A">
                                        @endif

                                    </div>
                                </div>


                                <div class="col-xl-6 col-lg-6 col-md-6">
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

                                <div class="col-12 col-xs-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Proposal Name (Max 1000
                                            Characters)</label>
                                        @if (!empty($service_details->proposal_name))
                                        <textarea readonly name="proposal_name" id="proposal_name" cols="2" rows="2"
                                            class="form-control"
                                            value="{{ $service_details->proposal_name }}">{{ $service_details->proposal_name }}</textarea>
                                        @else
                                        <textarea readonly name="proposal_name" id="proposal_name" cols="2" rows="2"
                                            class="form-control" value="N/A">N/A</textarea>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Background (Max 2500 Characters)</label>
                                        @if (!empty($service_details->background))
                                        <textarea readonly name="background" id="background" cols="2" rows="2"
                                            class="form-control" value="">{{ $service_details->background }}</textarea>
                                        @else
                                        <textarea readonly name="background" id="background" cols="2" rows="2"
                                            class="form-control" value="">N/A</textarea>
                                        @endif
                                    </div>
                                </div>


                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Justification of Proposal (Max 2500
                                            Characters)</label>
                                        @if (!empty($service_details->just_of_proposal))
                                        <textarea readonly name="just_of_proposal" id="just_of_proposal" cols="2"
                                            rows="2" class="form-control"
                                            value="">{{ $service_details->just_of_proposal }}</textarea>
                                        @else
                                        <textarea readonly name="just_of_proposal" id="just_of_proposal" cols="2"
                                            rows="2" class="form-control" value="">N/A</textarea>
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
                                                            $fy = explode(',',
                                                            $service_details['past_3_year_actual_cost_fy']);
                                                            $cost = explode(',',
                                                            $service_details['past_3_year_actual_cost']);
                                                            $service = explode(',',
                                                            $service_details['past_3_year_actual_cost_service']);

                                                            @endphp


                                                            <div class="col-xl-3 col-lg-6 col-md-6 mt-2">


                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost_fy[]"
                                                                    id="past_3_year_actual_cost_fy" value="N/A">

                                                            </div>


                                                            <div class="col-xl-3 col-lg-6 col-md-6 mt-2">


                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost[]"
                                                                    id="past_3_year_actual_cost" value="N/A">
                                                            </div>

                                                            <div class="col-xl-3 col-lg-6 col-md-6 mt-2">
                                                                <input readonly type="text" class="form-control"
                                                                    name="past_3_year_actual_cost_service[]"
                                                                    id="past_3_year_actual_cost_service" value="N/A">

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
                                        <textarea readonly name="benefit" id="benefit" cols="2" rows="2"
                                            class="form-control" value="">{{ $service_details->benefit }}</textarea>
                                        @else
                                        <textarea readonly name="benefit" id="benefit" cols="2" rows="2"
                                            class="form-control" value="">N/A</textarea>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Implementation Period From</label>
                                        @if (!empty($service_details->implementation_period_from))
                                        <input readonly type="text" class="form-control"
                                            name="implementation_period_from" id="implementation_period_from"
                                            value="{{ $service_details->implementation_period_from }}">
                                        @else
                                        <input readonly type="text" class="form-control"
                                            name="implementation_period_from" id="implementation_period_from"
                                            value="N/A">
                                        @endif
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Implementation Period To</label>
                                        @if (!empty($service_details->implementation_period_to))
                                        <input readonly type="text" class="form-control" name="implementation_period_to"
                                            id="implementation_period_to"
                                            value="{{ $service_details->implementation_period_to }}">
                                        @else
                                        <input readonly type="text" class="form-control" name="implementation_period_to"
                                            id="implementation_period_to" value="N/A">
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12  mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Implementation Plan Year Wise (Max 2500
                                            Characters)
                                        </label>
                                        @if (!empty($service_details->implementation_plan_year_wise))
                                        <textarea readonly name="implementation_plan_year_wise"
                                            id="implementation_plan_year_wise" cols="2" rows="2" class="form-control"
                                            value="">{{ $service_details->implementation_plan_year_wise }}</textarea>
                                        @else
                                        <textarea readonly name="implementation_plan_year_wise"
                                            id="implementation_plan_year_wise" cols="2" rows="2" class="form-control"
                                            value="N/A">N/A</textarea>
                                        @endif
                                    </div>
                                </div>

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


                                <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                    <div class="form-group">
                                        <label>Mode of Award of Services</label>
                                        @if (!empty($service_details->mode_of_award_of_service))
                                        <input readonly type="text" class="form-control"
                                            value="{{ $service_details->mode_of_award_of_service }}">
                                        @else
                                        <input readonly type="text" class="form-control" value="N/A">
                                        @endif

                                    </div>
                                </div>


                                <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                    <label>AMC/WO/RC Proposed Start Date</label>
                                    @if (!empty($service_details->amc_proposal_sdate))
                                    <input readonly type="text" id="amc_proposal_sdate"
                                        value="{{ $service_details->amc_proposal_sdate }}" name="amc_proposal_sdate"
                                        class="form-control">
                                    @else
                                    <input readonly type="text" id="amc_proposal_sdate" value="N/A"
                                        name="amc_proposal_sdate" class="form-control">
                                    @endif

                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                    <label>AMC/WO/RC Proposed End Date</label>
                                    @if (!empty($service_details->amc_proposal_edate))
                                    <input readonly type="text" id="amc_proposal_edate"
                                        value="{{ $service_details->amc_proposal_edate }}" name="amc_proposal_edate"
                                        class="form-control">
                                    @else
                                    <input readonly type="text" id="amc_proposal_edate" value="N/A"
                                        name="amc_proposal_edate" class="form-control">
                                    @endif

                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                    <label>Budget Available</label>
                                    <br>
                                    @if (!empty($service_details->budget_available))
                                    <input readonly class="form-control" name="budget_available" type="text"
                                        id="budget_available" placeholder="Budget Available"
                                        value="{{ $service_details->budget_available }}">
                                    @else
                                    <input readonly class="form-control" name="budget_available" type="text"
                                        id="budget_available" placeholder="Budget Available" value="N/A">
                                    @endif
                                </div>

                                <div class="form-group row mt-2 p-2">
                                    <div class="col-lg-6 mt-3">
                                        <label>Estimated amount of services </label>
                                        @if (!empty($service_details->estimate_amount_of_service))
                                        <input readonly type="text" class="form-control" id="estimate_amount_of_service"
                                            name="estimate_amount_of_service"
                                            value="{{ $service_details->estimate_amount_of_service }}">
                                        @else
                                        <input readonly type="text" class="form-control" id="estimate_amount_of_service"
                                            name="estimate_amount_of_service" value="N/A">
                                        @endif
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                        <label>Estimated amount of services Civil </label>
                                        @if (!empty($service_details->estimate_amount_of_service_civil))
                                        <input readonly type="text" class="form-control"
                                            id="estimate_amount_of_service_civil" placeholder="Enter In Lacs"
                                            name="estimate_amount_of_service_civil"
                                            value="{{ $service_details->estimate_amount_of_service_civil }}">
                                        @else
                                        <input readonly type="text" class="form-control"
                                            id="estimate_amount_of_service_civil" placeholder="Enter In Lacs"
                                            name="estimate_amount_of_service_civil" value="N/A">
                                        @endif

                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                        <label>Estimated amount of RR Charges</label>
                                        @if (!empty($service_details->estimate_amount_of_rr_chnage))
                                        <input readonly type="text" class="form-control"
                                            id="estimate_amount_of_rr_chnage" placeholder="Enter In Lacs"
                                            name="estimate_amount_of_rr_chnage"
                                            value="{{ $service_details->estimate_amount_of_rr_chnage }}">
                                        @else
                                        <input readonly type="text" class="form-control"
                                            id="estimate_amount_of_rr_chnage" placeholder="Enter In Lacs"
                                            name="estimate_amount_of_rr_chnage" value="N/A">
                                        @endif

                                    </div>

                                    <div class="col-lg-6 mt-3">
                                        <label>Estimated amount - Other </label>
                                        @if (!empty($service_details->estimate_amount_other))
                                        <input readonly type="text" class="form-control" id="estimate_amount_other"
                                            name="estimate_amount_other"
                                            value="{{ $service_details->estimate_amount_other }}">
                                        @else
                                        <input readonly type="text" class="form-control" id="estimate_amount_other"
                                            name="estimate_amount_other" value="N/A">
                                        @endif

                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                        <label> Cost Caluculation for services
                                            <br>
                                            @if (!empty($service_doc->cost_calculation_for_service))
                                            <a class="ml-2" download="{{ $service_doc->cost_calculation_for_service }}"
                                                href="{{ url(asset('services-doc/' . $service_doc->cost_calculation_for_service)) }}"><i
                                                    class="fa fa-download" title="Download"></i>{{
                                                $service_doc->cost_calculation_for_service }}</a>
                                            @else
                                            <spam> N/A </spam>
                                            @endif
                                        </label>
                                        <input type="file" readonly class="form-control"
                                            id="cost_calculation_for_service" name="cost_calculation_for_service"
                                            accept="" multiple>

                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                        <label>Total Budget</label>
                                        @if (!empty($service_details->total_buget))
                                        <input readonly type="text" class="form-control" id="total_buget"
                                            name="total_buget" value="{{ $service_details->total_buget }}">
                                        @else
                                        <input readonly type="text" class="form-control" id="total_buget"
                                            name="total_buget" value="N/A">
                                        @endif

                                    </div>

                                </div>

                                <div class="col-12 mb-2">


                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h6 class="accordion-header" type="button" data-toggle="collapse"
                                                data-target="#collapseTwo" aria-expanded="true"
                                                aria-controls="collapseTwo">
                                                Atachments </h6>

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
                                                                        class="fa fa-download" title="Download"></i>{{
                                                                    $service_doc->copy_of_previous_work }}</a>
                                                                @else
                                                                <spam> N/A </spam>
                                                                @endif
                                                            </label>
                                                            <input type="file" readonly class="form-control"
                                                                name="copy_of_previous_work" id="copy_of_previous_work">
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                            <label for="atachment">2. Copy of DERC/Other stakeholder
                                                                approvals : <br>

                                                                @if (!empty($service_doc->copy_of_derc_other))
                                                                <a class="ml-2"
                                                                    download="{{ $service_doc->copy_of_derc_other }}"
                                                                    href="{{ url(asset('services-doc/' . $service_doc->copy_of_derc_other)) }}"><i
                                                                        class="fa fa-download" title="Download"></i>{{
                                                                    $service_doc->copy_of_derc_other }}</a>
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
                                                                        class="fa fa-download" title="Download"></i>{{
                                                                    $service_doc->consuption_details }}</a>
                                                                @else
                                                                <spam> N/A </spam>
                                                                @endif
                                                            </label>
                                                            <input type="file" readonly name="consuption_details"
                                                                class="form-control" id="consuption_details"
                                                                placeholder="Serviced">
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                            <label for="atachment">4. Budget statement for both <br>
                                                                OPEX/CAPEX
                                                                activities : <br>


                                                                @if (!empty($service_doc->buget_stmt_for_both))
                                                                <a class="ml-2"
                                                                    download="{{ $service_doc->buget_stmt_for_both }}"
                                                                    href="{{ url(asset('services-doc/' . $service_doc->buget_stmt_for_both)) }}"><i
                                                                        class="fa fa-download" title="Download"></i>{{
                                                                    $service_doc->buget_stmt_for_both }}</a>
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
                                                                        class="fa fa-download" title="Download"></i>{{
                                                                    $service_doc->photographs_of_product }}</a>
                                                                @else
                                                                <spam> N/A </spam>
                                                                @endif
                                                            </label>
                                                            <input type="file" readonly name="photographs_of_product"
                                                                class="form-control" id="photographs_of_product"
                                                                placeholder="">
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                            <label for="atachment">6. Material procurement -
                                                                Details of Material,
                                                                Code, Qty, Rate, Amount etc : <br>

                                                                @if (!empty($service_doc->material_procurement))
                                                                <a class="ml-2"
                                                                    download="{{ $service_doc->material_procurement }}"
                                                                    href="{{ url(asset('services-doc/' . $service_doc->material_procurement)) }}"><i
                                                                        class="fa fa-download" title="Download"></i>{{
                                                                    $service_doc->material_procurement }}</a>
                                                                @else
                                                                <spam> N/A </spam>
                                                                @endif
                                                            </label>
                                                            <input type="file" readonly class="form-control"
                                                                name="material_procurement" id="material_procurement">
                                                        </div>



                                                        <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                            <label for="atachment">7. Vendor Quotation : <br>



                                                                @if (!empty($service_doc->vendor_quatation))
                                                                <a class="ml-2"
                                                                    download="{{ $service_doc->vendor_quatation }}"
                                                                    href="{{ url(asset('services-doc/' . $service_doc->vendor_quatation)) }}"><i
                                                                        class="fa fa-download" title="Download"></i>{{
                                                                    $service_doc->vendor_quatation }}</a>
                                                                @else
                                                                <spam> N/A </spam>
                                                                @endif
                                                            </label>
                                                            <input type="file" readonly name="vendor_quatation"
                                                                class="form-control" id="vendor_quatation">
                                                        </div>

                                                        <div class="col-xl-6 col-lg-6 col-md-6 mt-3">
                                                            <label for="atachment">8. Others : <br>


                                                                @if (!empty($service_doc->others))
                                                                <a class="ml-2" download="{{ $service_doc->others }}"
                                                                    href="{{ url(asset('services-doc/' . $service_doc->others)) }}"><i
                                                                        class="fa fa-download"
                                                                        title="Download"></i><br>{{ $service_doc->others
                                                                    }}</a>
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

                                </div>
                                <!-- @if ($user->role_id != 9)
    <div class="col-6 mb-2">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Remarks <span
                                                                    class="mandatory_input">*</span></label>

                                                            <textarea name="remark" id="remark" cols="2" rows="1" class="form-control"
                                                                placeholder=" Enter Remark Description"></textarea>
                                                        </div>
                                                    </div>
    @endif -->
                                @if (Auth::user()->role_id != 9)
                                <div class="col-6 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Enter Approval & Rejection Remarks
                                            <span class="mandatory_input">*</span></label>

                                        <textarea name="remark" id="remark" cols="2" rows="1" class="form-control"
                                            placeholder=" Enter Remark Description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-info">
                                        <div class="card-body">
                                            <label for="exampleFormControlInput1">Clarification Remark
                                             </label>
                                            <textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>
                                        </div>

                                    </div>
                                </div>
                                <!-- /.col-->
                                <div class="col-6 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Clarification
                                            To <span class="mandatory_input">*</span></label>
                                        <select id="clar_fy" class="form-control" name="users_name[]" multiple>
                                            @foreach ($employees as $employee)
                                            <option value="{{ $employee->user_id }}">{{ $employee->name }}</option>
                                            @endforeach
                                        </select>

                                    </div>

                                </div>

                                <table class=" m-3">
                                    @foreach($chats as $chat)
                                        <tr> 
                                           <td>
                                            @php
                                            $string= strip_tags(preg_replace('/\s+/' , ' ', $chat->message));
                                          
                                            @endphp
                                        
                                           <a href="#"> DOP  #{{$chat->dop_ref_no}} : {{$string}}</a><br>
                                           {{ date('d/m/y', strtotime($chat->created_at)) }}
                                             {{ date('H:i', strtotime($chat->created_at)) }}  Updated by : {{$chat->users->name}} <br><br>
                                          
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                            </div>

                            @endif

                        </div>

                        <input type="hidden" name="nv_id" id='nv_id' value="{{ $service_details->nv_id }}">
                        <input type="hidden" name="service_id" id='service_id' value="{{ $service_details->id }}">
                        <input type="hidden" name="dop_ref_no" id='dop_ref_no' value="{{$service_details->dop_ref_no}}">
                </div>
                <div class="card">
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                                @if ($user->role_id != 9)

                                @php
                                $id = Request::segment(4);
                                $data = App\Models\NVService::where('id', $id)->first();

                                $Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $data->id)->exists();

                                if ($Nvsericestatus) {
                                $Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $data->id)->first();
                                } else {
                                $Nvsericestatus = App\Models\Nvsericestatus::where('material_id', $data->id)->first();
                                }
                                $rv1 = $ap_rj_status->rv1_status;
                                $rv2 = $ap_rj_status->rv2_status;
                                $rv3 = $ap_rj_status->rv3_status;
                                $cpmg = $ap_rj_status->cpmg_status;

                                @endphp
                                @if ($Nvsericestatus->bt_status == null)
                                @if ($user->role_id == 6 && $Nvsericestatus->cpmg_status == 1)
                                <button type="button" class="btn btn-info approve-button"
                                    value="Approve">Approve</button>
                                <button type="button" class="btn btn-danger reject-button"
                                    value="Reject">Reject</button>
                                @endif
                                @endif
                                @if ($Nvsericestatus->hod_status == null)
                                @if ($user->role_id == 2)
                                <button type="button" class="btn btn-info approve-button"
                                    value="Approve">Approve</button>
                                <button type="button" class="btn btn-danger reject-button"
                                    value="Reject">Reject</button>
                                @endif
                                @endif
                                @if ($Nvsericestatus->cpmg_status == null)
                                @if ($user->role_id == 5 && $Nvsericestatus->hod_status == 1)
                                <button type="button" class="btn btn-info approve-button"
                                    value="Approve">Approve</button>
                                <button type="button" class="btn btn-danger reject-button"
                                    value="Reject">Reject</button>
                                @endif
                                @endif
                                @if ($Nvsericestatus->ces_status == null)
                                @if ($user->role_id == 10)
                                <button type="button" class="btn btn-info approve-button"
                                    value="Approve">Approve</button>
                                <button type="button" class="btn btn-danger reject-button"
                                    value="Reject">Reject</button>
                                @endif
                                @endif

                                @if ($Nvsericestatus->ceo_nominee_status == null)
                                @if ($user->role_id == 7 && $Nvsericestatus->bt_status == 1)
                                <button type="button" class="btn btn-info approve-button"
                                    value="Approve">Approve</button>
                                <button type="button" class="btn btn-danger reject-button"
                                    value="Reject">Reject</button>
                                @endif
                                @endif
                                @if ($Nvsericestatus->ceo_status == null)
                                @if ($user->role_id == 8 && $Nvsericestatus->ceo_nominee_status == 1)
                                <button type="button" class="btn btn-info approve-button"
                                    value="Approve">Approve</button>
                                <button type="button" class="btn btn-danger reject-button"
                                    value="Reject">Reject</button>
                                @endif
                                @endif
                                @endif


                                @if (Auth::user()->role_id != 9)
                                <button type="button" class="btn btn-info approve-button"
                                    value="Approve">Approve</button>
                                <button type="button" class="btn btn-danger reject-button"
                                    value="Reject">Reject</button>
                                <button type="button" class="btn btn-danger save-button" value="Save">Save</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
        <!-- /.card -->

    </div>
    </div>
    </div>

</section>
@endsection
@push('script')
<!-- Summernote -->
<script src="{{ asset('theme/plugins/ckeditor/ckeditor.js') }}"></script>
<!-- include jquery, bootstrap, summernote here -->
{{--
<script src="{{ asset('theme/plugins/summernote/summernote.js') }}"></script> --}}
<script src="{{ asset('theme/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>

<script src="{{ asset('admin/js/nvservices.js') }}"></script>
<script>
    // Replace the <textarea id="editor1"> with a CKEditor 4
    // instance, using default configuration.
    CKEDITOR.replace('editor1');
</script>
@endpush