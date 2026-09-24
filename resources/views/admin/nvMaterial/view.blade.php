@extends('admin.layout.master', ['page_title' => 'Preview NV Material'])
@push('styles')
<link rel="stylesheet" href="{{asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"> Preview NV-Material</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Preview NV-Material</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card  card-info shadow-lg printout show-table-data-nv-material">
                    <div class="card-header bg-transparent">

                        <div class="card-title">
                            <h3 class="card-label">NV-Material Details</h3>
                        </div>

                        <div class="card-toolbar">
                            <button type="reset" class="btn btn-primary " onclick="history.back();">Back »</button>

                        </div>

                    </div>
                    <!-- /.card-header -->

                    <div class="card-body rounded-0">
                        <div class="container-fluid bg-3 text-left">
                            <div class="row p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Department Name</p>
                                    @if(!empty($data->dept_id))
                                    <spam>{{$data->department->name}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">DOP reference Number</p>
                                    @if(!empty($data->dop))
                                    <spam>{{$data->dop}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                               
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Proposal Name</p>
                                    @if(!empty($data->proposal_name))
                                    <spam>{{$data->proposal_name}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                 
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Background</p>
                                    @if(!empty($data->background))
                                    <spam>{{$data->background}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                  
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Justification Of Proposal</p>
                                    @if(!empty($data->just_prop))
                                    <spam>{{$data->just_prop}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                
                                </div>

                            </div><br>
                            <div class="row ">
                                <div class="col-12">
                                    <!-- <div class="card  card-info shadow-lg printout show-table-data-nv-material"> -->
                                    <div class="card-header bg-transparent">

                                        <div class="card-title">
                                            <h1 class="m-0" style="font-size:18px">Past 3 Years actual cost trend Material</h1>
                                        </div>
                                    </div>
                                </div>
                                <!-- </div> -->
                            </div>
                            <div class="row mt-2 p-2">
                            <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Past 3 Years Actual Cost Trend Material FY </p>
                                    @if(!empty($data->cost_trend_year3))
                                <spam>{{$data->cost_trend_year3}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Past 3 Years Actual Cost</p>
                                    @if(!empty($data->cost_trend_year1))
                                <spam>{{$data->cost_trend_year1}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                                
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Past 3 Years Actual Cost Trend Materials</p>
                                    @if(!empty($data->cost_trend_year2))
                                <spam>{{$data->cost_trend_year2}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                               
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-12">
                                    <!-- <div class="card  card-info shadow-lg printout show-table-data-nv-material"> -->
                                    <div class="card-header bg-transparent">

                                        <div class="card-title">
                                          
                                        </div>
                                    </div>
                                </div>
                                <!-- </div> -->
                            </div><br>

                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Benefit</p>
                                    @if(!empty($data->benefit))
                                <spam>{{$data->benefit}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                                 
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Implementation Period To</p>
                                    @if(!empty($data->imp_to))
                                <spam>{{$data->imp_to}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                                   
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Implementation Period From</p>
                                    @if(!empty($data->imp_from))
                                <spam>{{$data->imp_from}}</spam>
                                @else
                                <spam> N/A </spam>
                                 @endif
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Implementation Plan Year Wise</p>
                                    @if(!empty($data->imp_plan))
                                    <spam>{{$data->imp_plan}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Type Of Proposal</p>
                                    @if(!empty($data->prop_type))
                                    <spam>{{$data->prop_type}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Nature Of Work</p>
                                    @if(!empty($data->worktype))
                                    <spam>{{$data->worktype}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                 
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Scheme No</p>
                                    @if(!empty($data->scheme_no))
                                    <spam>{{$data->scheme_no}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                             
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Scheme Description</p>
                                    @if(!empty($data->scheme_des))
                                    <spam>{{$data->scheme_des}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                   
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Scheme Type / Category</p>
                                    @if(!empty($data->scheme_type))
                                    <spam>{{$data->scheme_type}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                               
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">DERC Approval Status </p>
                                    @if(!empty($data->derc_approval))
                                    <spam>{{$data->derc_approval}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                              
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">DERC Approval Date</p>
                                    @if(!empty($data->derc_app_date))
                                    <spam>{{$data->derc_app_date}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">DERC reference No</p>
                                    @if(!empty($data->derc_ref_no))
                                    <spam>{{$data->derc_ref_no}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                 
                                </div>

                            </div><br>
                            <div class="row">
                                <div class="col-12">
                                    <!-- <div class="card  card-info shadow-lg printout show-table-data-nv-material"> -->
                                    <div class="card-header bg-transparent">

                                        <div class="card-title">
                                            <h1 class="m-0" style="font-size:18px"> Material BOQ</h1>
                                        </div>
                                    </div>
                                </div>
                                <!-- </div> -->
                            </div>

                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Material Code</p>
                                    @if(!empty($data->material_code))
                                    <spam>{{$data->material_code}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Material Description</p>
                                    @if(!empty($data->mat_des))
                                    <spam>{{$data->mat_des}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>
                                <!-- <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Material Justification</p>
                                    @if(!empty($data->mat_just))
                                    <spam>{{$data->mat_just}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                              
                                </div> -->
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Material Group</p>
                                    @if(!empty($data->mat_group))
                                    <spam>{{$data->mat_group}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                               
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                            
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">UoM</p>
                                    @if(!empty($data->uom))
                                    <spam>{{$data->uom}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                               
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Rate</p>
                                    @if(!empty($data->rate))
                                    <spam>{{$data->rate}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Quantity Required</p>
                                    @if(!empty($data->quantity))
                                    <spam>{{$data->quantity}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                   
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                               
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Total Amount</p>
                                    @if(!empty($data->total_amount))
                                    <spam>{{$data->total_amount}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Delivery Schedule</p>
                                    @if(!empty($data->delivery_schedule))
                                    <spam>{{$data->delivery_schedule}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                 
                                </div>

                            </div><br>
                            <div class="row">
                                <div class="col-12">
                                
                                    <div class="card-header bg-transparent">

                                    </div>
                                </div>
                             
                            </div>
                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Budget Available</p>
                                    @if(!empty($data->budget_avl))
                                    <spam>{{$data->budget_avl}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                               
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Is There Any Capacity Addition</p>
                                    @if(!empty($data->cap_add))
                                    <spam>@if($data->cap_add == '5' ) No @else Yes @endif</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                   
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">PTR MVA</p>
                                    @if(!empty($data->ptr_mva))
                                    <spam>{{$data->ptr_mva}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                 
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">DT MVA</p>
                                    @if(!empty($data->dt_mva))
                                    <spam>{{$data->dt_mva}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">EHV Line(Ckt.Km)</p>
                                    @if(!empty($data->ehv_line))
                                    <spam>{{$data->ehv_line}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">HT Line(Ckt.Km)</p>
                                    @if(!empty($data->ht_line))
                                    <spam>{{$data->ht_line}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">LT Line(Ckt.Km)</p>
                                    @if(!empty($data->lt_line))
                                    <spam>{{$data->lt_line}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">New Product</p>

                                    @if (!empty($data_doc->new_product))
                                <a class="ml-2" download="{{ $data_doc->new_product }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->new_product)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->new_product }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif

                                </div>
                              


                            </div><br>

                            <div class="row">
                                <div class="col-12">
                                    <!-- <div class="card  card-info shadow-lg printout show-table-data-nv-material"> -->
                                    <div class="card-header bg-transparent">

                                        <div class="card-title">
                                            <h1 class="m-0" style="font-size:18px"> Rate Reference</h1>
                                        </div>
                                    </div>
                                </div>
                                <!-- </div> -->
                            </div>

                            <div class="row mt-2 p-2">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">C&M Rate Reference</p>
                                    @if (!empty($data_doc->cm_rate_ref))
                                <a class="ml-2" download="{{ $data_doc->cm_rate_ref }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->cm_rate_ref)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->cm_rate_ref }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                               
                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Vendor Quatation</p>
                                    @if (!empty($data_doc->vend_quatation))
                                <a class="ml-2" download="{{ $data_doc->vend_quatation }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->vend_quatation)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->vend_quatation }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                                  
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Last Purchase Price</p>
                                    @if (!empty($data_doc->last_purchase_price))
                                <a class="ml-2" download="{{ $data_doc->last_purchase_price }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->last_purchase_price)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->last_purchase_price }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                                </div>
                              

                            </div>

                            <div class="row mt-2 p-2">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">User Estimation</p>
                                    @if (!empty($data_doc->user_estimation))
                                <a class="ml-2" download="{{ $data_doc->user_estimation }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->user_estimation)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->user_estimation }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                              
                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Root Cause Analysis</p>
                                    @if(!empty($data->root_cause_analysis))
                                    <spam>{{$data->root_cause_analysis}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Cost Reduction Plan/Future Phasing Out Plan(If Applicable)</p>
                                    @if(!empty($data->cause_analysis))
                                    <spam>{{$data->cause_analysis}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                
                                </div>
                               

                            </div>
                            <div class="row mt-2 p-2">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Special Remarks Such As Any Specific Recommendation
                                    </p>
                                    @if(!empty($data->special_remarks))
                                    <spam>{{$data->special_remarks}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                
                                </div>

                            </div><br>

                            <div class="row">
                                <div class="col-12">
                                    <!-- <div class="card  card-info shadow-lg printout show-table-data-nv-material"> -->
                                    <div class="card-header bg-transparent">

                                        <div class="card-title">
                                            <h1 class="m-0" style="font-size:18px"> Attachments</h1>
                                        </div>
                                    </div>
                                </div>
                                <!-- </div> -->
                            </div>
                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Copy Of Previous Work Order/Purchase Order</p>
                                    @if (!empty($data_doc->previous_work_order))
                                <a class="ml-2" download="{{ $data_doc->previous_work_order }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->previous_work_order)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->previous_work_order }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Copy Of DERC/Other Stakeholder Approvals</p>
                                    @if (!empty($data_doc->derc_stakeholder_approvals))
                                <a class="ml-2" download="{{ $data_doc->derc_stakeholder_approvals }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->derc_stakeholder_approvals)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->derc_stakeholder_approvals }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                                 
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Consumption Details - Last 3 Year</p>
                                    @if (!empty($data_doc->consumption_details))
                                <a class="ml-2" download="{{ $data_doc->consumption_details }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->consumption_details)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->consumption_details }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                                
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Vendor Quatation</p>
                                    @if (!empty($data_doc->vendor_quatation))
                                <a class="ml-2" download="{{ $data_doc->vendor_quatation }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->vendor_quatation)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->vendor_quatation }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                               
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Photographs Of Product</p>
                                    @if (!empty($data_doc->photo_product))
                                <a class="ml-2" download="{{ $data_doc->photo_product }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->photo_product)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->photo_product }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                                  
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Material Procurement</p>
                                    @if (!empty($data_doc->material_procurement))
                                <a class="ml-2" download="{{ $data_doc->material_procurement }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->material_procurement)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->material_procurement }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                                
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Budget Statement For Both OPEX/CAPEX Activities</p>
                                    @if (!empty($data_doc->budget_for_both))
                                <a class="ml-2" download="{{ $data_doc->budget_for_both }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->budget_for_both)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->budget_for_both }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                                
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Others</p>
                                    @if (!empty($data_doc->others))
                                <a class="ml-2" download="{{ $data_doc->others }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->others)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->others }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                               
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Total Budget Materials (Rs)
                                    </p>
                                    @if(!empty($data->total_budget_material))
                                    <spam>{{$data->total_budget_material}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                 
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Are There Any Services Related To This NV</p>
                                    @if(!empty($data->ser_rel_nv))
                                    <spam>@if($data->ser_rel_nv == 3) No @else Yes @endif</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                 
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Proposal Number</p>
                                    @if(!empty($data->prop_number))
                                    <spam>{{$data->prop_number}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Mode Of Award(Services)
                                    </p>
                                    @if(!empty($data->mode_award))
                                    <spam>{{$data->mode_award}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>

                            </div><br>
                            <div class="row ">
                                <div class="col-12">
                                    <!-- <div class="card  card-info shadow-lg printout show-table-data-nv-material"> -->
                                    <div class="card-header bg-transparent">

                                        <div class="card-title">
                                            <h1 class="m-0" style="font-size:18px">Past 3 Years actual cost trend Services</h1>
                                        </div>
                                    </div>
                                </div>
                                <!-- </div> -->
                            </div>
                            <div class="row mt-2 p-2">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Past 3 Years Actual Cost Trend Service FY</p>
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
                                </div><br>
                                <div class="row">
                                <div class="col-12">
                                    <!-- <div class="card  card-info shadow-lg printout show-table-data-nv-material"> -->
                                    <div class="card-header bg-transparent">

                                        <div class="card-title">
                                            <h1 class="m-0" style="font-size:18px"> Service BOQ</h1>
                                        </div>
                                    </div>
                                </div>
                                <!-- </div> -->
                            </div>
                                <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                <p class="font-weight-bolder">Service code</p>
                                    @if(!empty($data->service_code))
                                    <spam>{{$data->service_code}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                   </div>

                                   <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Service Description </p>
                                    @if(!empty($data->ser_des))
                                    <spam>{{$data->ser_des}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Rate Reference </p>
                                    @if(!empty($data->ser_rate_ref))
                                    <spam>{{$data->ser_rate_ref}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                </div>
                            </div>

                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">UoM </p>
                                    @if(!empty($data->ser_uom))
                                    <spam>{{$data->ser_uom}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                   </div>

                                   <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Rate</p>
                                    @if(!empty($data->ser_rate))
                                    <spam>{{$data->ser_rate}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Quantity Required </p>
                                    @if(!empty($data->ser_quantity))
                                    <spam>{{$data->ser_quantity}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                </div>
                            </div>


                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Total Amount </p>
                                    @if(!empty($data->ser_total_amount))
                                    <spam>{{$data->ser_total_amount}}</spam>
                                    @else
                                    <spam> N/A </spam>
                                     @endif
                                </div>
                           </div>
                           <div class="row">
                                <div class="col-12">
                                    <!-- <div class="card  card-info shadow-lg printout show-table-data-nv-material"> -->
                                    <div class="card-header bg-transparent">

                                        <div class="card-title">
                                         
                                        </div>
                                    </div>
                                </div>
                                <!-- </div> -->
                            </div>
                           <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Past Practice Follow For Services (If Any)</p>
                                    @if(!empty($data->past_practice_follow))
                                    <spam>{{$data->past_practice_follow}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                 
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">AMC Proposed Start Date
                                    </p>
                                    @if(!empty($data->amc_prop_start_date))
                                    <spam>{{$data->amc_prop_start_date}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>
                              
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">AMC Proposed End Date</p>
                                    @if(!empty($data->amc_prop_end_date))
                                    <spam>{{$data->amc_prop_end_date}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                 
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Estimated Amount Of Services</p>
                                    @if(!empty($data->estimate_amount_of_service))
                                    <spam>{{$data->estimate_amount_of_service}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                          
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Estimated Amount Of Services Civil
                                    </p>
                                    @if(!empty($data->estimate_amount_of_service_civil))
                                    <spam>{{$data->estimate_amount_of_service_civil}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Estimated Amount Of RR Charges</p>
                                    @if(!empty($data->estimate_amount_of_rr_charge))
                                    <spam>{{$data->estimate_amount_of_rr_charge}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>

                            </div>

                            <div class="row mt-2 p-2">
                               
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Estimated Amount - Other</p>
                                    @if(!empty($data->estimate_amount_other))
                                    <spam>{{$data->estimate_amount_other}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                  
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <p class="font-weight-bolder">Cost Calculation For Services
                                    </p>
                                    @if (!empty($data_doc->cost_calculation_for_service))
                                <a class="ml-2" download="{{ $data_doc->cost_calculation_for_service }}"
                                    href="{{ url(asset('materials-doc/' . $data_doc->cost_calculation_for_service)) }}"><i
                                        class="fa fa-download" title="Download"></i>{{ $data_doc->cost_calculation_for_service }}</a>
                                        @else
                                        <spam> N/A </spam>
                                         @endif
                                   
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Total Budget For Services</p>
                                    @if(!empty($data->total_budget_service))
                                    <spam>{{$data->total_budget_service}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                               
                                </div>

                            </div>
                            <div class="row mt-2 p-2">
                              
                                <div class="col-xl-4 col-lg-4 col-md-6 ">
                                    <p class="font-weight-bolder">Total Budget(Material & Services)</p>
                                    @if(!empty($data->total_budget_both))
                                    <spam>{{$data->total_budget_both}}</spam>
                                    @else
                                <spam> N/A </spam>
                                 @endif
                                 
                                </div>
                              

                            </div>


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

    <!-- <script src="{{asset('admin/js/nv.js')}}"></script> -->

    @endpush