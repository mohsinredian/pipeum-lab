@extends('admin.layout.master', ['page_title' => 'Preview NV Service'])
@push('styles')
<link rel="stylesheet" href="{{asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Preview NV-Service</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Preview NV-Service</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
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
                    <div class="card-body rounded-0">
                        <div class="container-fluid text-left">
                            <div class="row p-2">

                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Department Name</p>
                                    @if(!empty($data->dept_id))
                                    <spam>{{$data->department->name}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">DOP Reference Number</p>
                                    @if(!empty($data->dop_ref_no))
                                    <spam>{{$data->dop_ref_no}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                    
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Proposal Name</p>
                                    @if(!empty($data->proposal_name))
                                    <spam>{{$data->proposal_name}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Background</p>
                                    @if(!empty($data->background))
                                    <spam>{{$data->background}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Justification Of Proposal</p>
                                    @if(!empty($data->just_of_proposal))
                                    <spam>{{$data->just_of_proposal}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                </div>
                            

                            </div>
                            <!-- <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h6 class="accordion-header" type="button" data-toggle="collapse"
                                        data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Past 3 Years Actual Cost Trend Services </h6> -->
                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Past 3 Years Actual Cost Trend Services FY</p>
                                    @if(!empty($data->past_3_year_actual_cost_fy))
                                    <spam>{{$data->past_3_year_actual_cost_fy}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Past 3 Years Actual Cost </p>
                                    @if(!empty($data->past_3_year_actual_cost))
                                    <spam>{{$data->past_3_year_actual_cost}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Past 3 Years Actual Cost Trend Services </p>
                                    @if(!empty($data->past_3_year_actual_cost_service))
                                    <spam>{{$data->past_3_year_actual_cost_service}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                </div>
                            </div>
                        </div>
                        <!-- </div>
                        </div> -->

                        <div class="row mt-2 p-2">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">Benefit</p>
                                @if(!empty($data->benefit))
                                <spam>{{$data->benefit}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">Implementation Period From</p>
                                @if(!empty($data->implementation_period_from))
                                <spam>{{$data->implementation_period_from}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">Implementation Period To</p>
                                @if(!empty($data->implementation_period_to))
                                <spam>{{$data->implementation_period_to}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>

                        </div>

                        <div class="row mt-2 p-2">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">Implementation Plan Year Wise</p>
                                @if(!empty($data->implementation_plan_year_wise))
                                <spam>{{$data->implementation_plan_year_wise}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">Type Of Proposal</p>
                                @if(!empty($data->type_of_proposal))
                                <spam>{{$data->type_of_proposal}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">Mode Of Award Of Services</p>
                                @if(!empty($data->mode_of_award_of_service))
                                <spam>{{$data->mode_of_award_of_service}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>

                        </div>

                        <div class="row mt-2 p-2">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">AMC/WO/RC Proposed Start Date</p>
                                @if(!empty($data->amc_proposal_sdate))
                                <spam>{{$data->amc_proposal_sdate}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">AMC/WO/RC Proposed End Date</p>
                                @if(!empty($data->amc_proposal_edate))
                                <spam>{{$data->amc_proposal_edate}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">Budget Available</p>
                                @if(!empty($data->budget_available))
                                <spam>{{$data->budget_available}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>

                        </div>

                        <div class="row mt-2 p-2">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">Estimated Amount Of Services
                                    (Lobour+Transport) (In Lacs)</p>
                                @if(!empty($data->estimate_amount_of_service))
                                <spam>{{$data->estimate_amount_of_service}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">Estimated Amount Of Services Civil </p>
                                @if(!empty($data->estimate_amount_of_service_civil))
                                <spam>{{$data->estimate_amount_of_service_civil}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">Estimated Amount Of RR Charges</p>
                                @if(!empty($data->estimate_amount_of_rr_chnage))
                                <spam>{{$data->estimate_amount_of_rr_chnage}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>

                        </div>

                        <div class="row mt-2 p-2">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">Estimated Amount - Other</p>
                                @if(!empty($data->estimate_amount_other))
                                <spam>{{$data->estimate_amount_other}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">Total Budget </p>
                                @if(!empty($data->total_buget))
                                <spam>{{$data->total_buget}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>

                        </div>
                        <hr>
                        <!-- <div class="accordion" id="">
                            <div class="accordion-item">
                                <h4 class="" type="button" data-toggle="collapse">
                                    Atachments </h4>
                                    <hr> -->
                        <div class="card-header bg-transparent">

                            <div class="card-title ">
                                <h3 class="card-label">Atachments </h3>
                            </div>


                        </div>
                        <div class="row mt-2 p-2">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">Cost Caluculation for services</p>
                                @if (!empty($data->cost_calculation_for_service))
                                <a class="ml-2" download="{{ $data->cost_calculation_for_service }}"
                                    href="{{ url(asset('services-doc/' . $data->cost_calculation_for_service)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $service_doc->cost_calculation_for_service }}</a>
                                @else
                                <spam> N/A </spam>
                                 @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">1. Copy of previous work
                                    order/purchase
                                    order </p>
                                @if (!empty($service_doc->copy_of_previous_work))
                                <a class="ml-2" download="{{ $service_doc->copy_of_previous_work }}"
                                    href="{{ url(asset('services-doc/' . $service_doc->copy_of_previous_work)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $service_doc->copy_of_previous_work }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">2. Copy of DERC/Other stakeholder
                                    approvals </p>
                                @if (!empty($service_doc->copy_of_derc_other))
                                <a class="ml-2" download="{{ $service_doc->copy_of_derc_other }}"
                                    href="{{ url(asset('services-doc/' . $service_doc->copy_of_derc_other)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $service_doc->copy_of_derc_other }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                            </div>
                        </div>

                        <div class="row mt-2 p-2">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">3. Consumption Details Last 3 years</p>
                                @if (!empty($service_doc->consuption_details))
                                <a class="ml-2" download="{{ $service_doc->consuption_details }}"
                                    href="{{ url(asset('services-doc/' . $service_doc->consuption_details)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $service_doc->consuption_details }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">4. Budget statement for both
                                    OPEX/CAPEX
                                    activities </p>
                                @if (!empty($service_doc->buget_stmt_for_both))
                                <a class="ml-2" download="{{ $service_doc->buget_stmt_for_both }}"
                                    href="{{ url(asset('services-doc/' . $service_doc->buget_stmt_for_both)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $service_doc->buget_stmt_for_both }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">5. Photographs of product/PoC in
                                    case of new
                                    product trial </p>
                                @if (!empty($service_doc->photographs_of_product))
                                <a class="ml-2" download="{{ $service_doc->photographs_of_product }}"
                                    href="{{ url(asset('services-doc/' . $service_doc->photographs_of_product)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $service_doc->photographs_of_product }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                            </div>
                        </div>

                        <div class="row mt-2 p-2">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">6. Material procurement - Details of
                                    Material,
                                    Code, Qty, Rate, Amount etc</p>
                                @if (!empty($service_doc->material_procurement))
                                <a class="ml-2" download="{{ $service_doc->material_procurement }}"
                                    href="{{ url(asset('services-doc/' . $service_doc->material_procurement)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $service_doc->material_procurement }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">7. Vendor Quotation </p>
                                @if (!empty($service_doc->vendor_quatation))
                                <a class="ml-2" download="{{ $service_doc->vendor_quatation }}"
                                    href="{{ url(asset('services-doc/' . $service_doc->vendor_quatation )) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $service_doc->vendor_quatation }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <p class="font-weight-bolder">8. Others </p>
                                @if (!empty($service_doc->others))
                                <a class="ml-2" download="{{ $service_doc->others }}"
                                    href="{{ url(asset('services-doc/' . $service_doc->others)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $service_doc->others }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                            </div>
                        </div>
                        <!-- </div>

                        </div> -->

                    </div>
                    <div class="card">
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-12 text-center">

                                    <button type="submit" class="btn btn-success">Save</button>
                                    <!-- <button type="reset" class="btn btn-secondary"
                                        onclick="history.back();">Reset</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->

        <!-- <button  onclick="myFunction()">click</button>
        <div class="box" style="display:block;border:2px solid red "  >
       </div> -->
    </div>
    </div><!-- /.container-fluid -->



    @endsection
    @push('script')
    <script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>

    <script src="{{asset('admin/js/nv.js')}}"></script>

    @endpush