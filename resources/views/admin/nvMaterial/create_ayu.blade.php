@extends('admin.layout.master', ['page_title' => 'Create NV Material'])
@push('styles')

<style>
    .dataTables_filter>label {
        float: right;
    }


    .pagination {
        float: right;
    }

    .delete_all_materials {
        background-color: rgb(3, 142, 220) !important;
        width: 110px !important;
        height: 35px !important;
        color: white !important;
        margin-top: 2px;
    }
    .delete_all_service {
        background-color: rgb(3, 142, 220) !important;
        width: 110px !important;
        height: 35px !important;
        color: white !important;
        margin-top: 2px;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />
@endpush
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

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form class="form" id="create_nv_material" enctype="multipart/form-data">
                    @csrf

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Need Validation Material Details</h3>
                        </div>

                        <input type="hidden" class="form-control" name="nv_id" id="nv_id" value="{{ request()->segment(4) }}">
                        <input type="hidden" class="form-control" name="company_id" id="company_id" value="{{ request()->segment(5) }}">
                        <input type="hidden" class="form-control" name="service_id" id="service_id" value="{{ $material_details != '' ? $material_details->id : '' }}">

                        <div class="card-body">
                            <div class="form-group ">

                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Department Name</label>
                                            <select class="form-control" id="dept_id" name="dept_id" readonly>
                                                <!-- <option value="">Select Department</option> -->
                                                @foreach ($departments as $department)
                                                <option value="{{ $department->id }}" @if ($department->id == ($material_details != '' ? $material_details->dept_id : '')) selected="selected" @endif>
                                                    {{ $department->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">DOP Reference Number</label>
                                            <input type="text" class="form-control" id="dop" name="dop" value="{{ $material_details != '' ? $material_details->dop : '' }}" placeholder="Enter DOP Reference Number">
                                            <div class="common-error form-text dop_error"></div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-12 col-xs-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Proposal Name </label>

                                        <textarea name="proposal_name" id="proposal_name" class="form-control auto-resize-textarea" value="{{ $material_details != '' ? $material_details->proposal_name : '' }}" placeholder=" Enter Proposal Name">{{ $material_details != '' ? $material_details->proposal_name : '' }}</textarea>
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Background (Max 2500 Characters)</label>

                                        <textarea name="background" id="background" class="form-control auto-resize-textarea" value="{{ $material_details != '' ? $material_details->background : '' }}" placeholder=" Enter Background">{{ $material_details != '' ? $material_details->background : '' }}</textarea>
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Justification of Proposal (Max 2500
                                            Characters)</label>

                                        <textarea name="just_Prop" id="just_Prop" class="form-control auto-resize-textarea" value="{{ $material_details != '' ? $material_details->just_Prop : '' }}" placeholder=" Enter Justification of Proposal">{{ $material_details != '' ? $material_details->just_Prop : '' }}</textarea>
                                    </div>
                                </div>


                                <div class="col-12 mb-2">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                                                Past 3 Years Actual Cost Trend For Material </h6>

                                            <div id="collapseFive" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                <div class="card-body">
                                                    <!-- start here -->
                                                    @php
                                                    $year1 = explode(',', $material_details != '' ? $material_details->cost_trend_year1 : '');
                                                    $year2 = explode(',', $material_details != '' ? $material_details->cost_trend_year2 : '');
                                                    $year3 = explode(',', $material_details != '' ? $material_details->cost_trend_year3 : '');
                                                    $year = $nv_year->fiscal_year;
                                                    $yr_l = substr($year, 5);
                                                    $yr_f = substr($year, 0, 4);
                                                    @endphp

                                                    <div class="row">
                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                            <input type="text" class="form-control" id="cost_trend_year3" name="cost_trend_year3[]" placeholder="{{ $yr_f - 1 }}-{{ $yr_l - 1 }}" value="{{ $yr_f - 1 }}-{{ $yr_l - 1 }}" readonly>

                                                        </div>

                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                            <input type="number" class="form-control" id="cost_trend_year1" name="cost_trend_year1[]" value="{{ isset($year1[0]) ? $year1[0] : '' }}" placeholder="₹">
                                                        </div>

                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                            <input type="text" class="form-control" id="cost_trend_year2" name="cost_trend_year2[]" placeholder="Material" value="{{ isset($year2[0]) ? $year2[0] : '' }}">
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                            <input type="text" class="form-control" id="cost_trend_year3" name="cost_trend_year3[]" placeholder="{{ $yr_f - 2 }}-{{ $yr_l - 2 }}" value="{{ $yr_f - 2 }}-{{ $yr_l - 2 }}" readonly>
                                                        </div>

                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                            <input type="number" class="form-control" id="cost_trend_year1" name="cost_trend_year1[]" value="{{ isset($year1[1]) ? $year1[1] : '' }}" placeholder="₹">
                                                        </div>

                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                            <input type="text" class="form-control" id="cost_trend_year2" name="cost_trend_year2[]" placeholder="Material" value="{{ isset($year2[1]) ? $year2[1] : '' }}">
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <!-- Add more rows here -->
                                                    <div class="row after-add-more">
                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                            <input type="text" class="form-control" id="cost_trend_year3" name="cost_trend_year3[]" placeholder="{{ $yr_f - 3 }}-{{ $yr_l - 3 }}" value="{{ $yr_f - 3 }}-{{ $yr_l - 3 }}" readonly>
                                                        </div>

                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                            <input type="number" class="form-control" id="cost_trend_year1" name="cost_trend_year1[]" value="{{ isset($year1[2]) ? $year1[2] : '' }}" placeholder="₹">
                                                        </div>

                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                            <input type="text" class="form-control" id="cost_trend_year2" name="cost_trend_year2[]" placeholder="Material" value="{{ isset($year2[2]) ? $year2[2] : '' }}">
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
                                                    $selectedYear = $material_details != '' ? $material_details->implements_years : '';
                                                    $from = explode(',', $material_details != '' ? $material_details->imp_from : '');
                                                    $to = explode(',', $material_details != '' ? $material_details->imp_to : '');
                                                    $service = explode(',', $material_details != '' ? $material_details->imp_plan : '');

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
                                                                            @for ($i = 0; $i < $selectedYear; $i++) <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                                <div class="form-group">
                                                                                    <label for="exampleFormControlInput1">Implementation
                                                                                        Period From </label>
                                                                                    <input type="date" class="form-control" name="imp_from[]" id="imp_from" value="{{ isset($from[$i]) ? $from[$i] : '' }}" placeholder="Enter Benefit" max="9999-12-31">
                                                                                </div>
                                                                        </div>

                                                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                            <div class="form-group">
                                                                                <label for="exampleFormControlInput1">Implementation
                                                                                    Period To</label>
                                                                                <input type="date" class="form-control" name="imp_to[]" id="imp_to" value="{{ isset($to[$i]) ? $to[$i] : '' }}" placeholder="Enter Benefit" max="9999-12-31">
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12 mb-2">
                                                                            <div class="form-group">
                                                                                <label for="exampleFormControlInput1">Implementation
                                                                                    Plan Year Wise (Max 2500 Characters) </label>
                                                                                     
                                                                                <textarea name="imp_plan[]" id="imp_plan" cols="2" rows="10" class="form-control text_editor"  placeholder="Enter Implementation Plan Year Wise">{{ isset($service[$i]) ? $service[$i] : '' }}</textarea>
                                                                                <!-- <textarea name="imp_plan[]" id="imp_plan{{$i+1}}" cols="2" rows="10" class="form-control" placeholder="Enter Implementation Plan Year Wise">{{$material_details->imp_plan }}</textarea> -->
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
                                @else
                                <div class="col-12 mb-2">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseimple" aria-expanded="true" aria-controls="collapseimple">
                                                Implementation Period</h6>

                                            <div id="collapseimple" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                <div class="card-body">
                                                    @php
                                                    $from = explode(',', $material_details != '' ? $material_details->imp_from : '');
                                                    $to = explode(',', $material_details != '' ? $material_details->imp_to : '');
                                                    $service = explode(',', $material_details != '' ? $material_details->imp_plan : '');

                                                    @endphp
                                                    @foreach ($from as $key => $fromValue)
                                                    <div class="row after-add-more-third">

                                                        <div class="col-12">
                                                            <div class="row">
                                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                    <label for="exampleFormControlSelect">Select
                                                                        Year</label>
                                                                    <select class="form-control" id="exampleFormControlSelect" name="implements_years" onchange="duplicateColumns()" style="display: inline-block; width:200px;">
                                                                        <option value="">Select Year</option>
                                                                        <option value="1" {{ ($material_details != '' ? $material_details->implements_years : '') == '1' ? 'selected' : '' }}>
                                                                            1</option>
                                                                        <option value="2" {{ ($material_details != '' ? $material_details->implements_years : '') == '2' ? 'selected' : '' }}>
                                                                            2</option>
                                                                        <option value="3" {{ ($material_details != '' ? $material_details->implements_years : '') == '3' ? 'selected' : '' }}>
                                                                            3</option>
                                                                    </select>
                                                                </div>

                                                            </div>

                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div id="duplicateContainer" class="row" style="display: none;">
                                                                        <!-- Original columns -->


                                                                        <div class="original-columns row">
                                                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                                <div class="form-group">
                                                                                    <label for="exampleFormControlInput1">Implementation
                                                                                        Period From</label>
                                                                                    <input type="date" class="form-control" name="imp_from[]" id="imp_from" value="{{ $material_details != '' ? $fromValue : '' }}" placeholder="Enter Benefit" max="9999-12-31">
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                                <div class="form-group">
                                                                                    <label for="exampleFormControlInput1">Implementation
                                                                                        Period To</label>
                                                                                    @php
                                                                                    $toValue = isset($to[$key]) ? $to[$key] : '';
                                                                                    @endphp
                                                                                    <input type="date" class="form-control" name="imp_to[]" id="imp_to" value="{{ $material_details != '' ? $toValue : '' }}" placeholder="Enter Benefit" max="9999-12-31">
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12 mb-2">
                                                                                <div class="form-group">
                                                                                    <label for="exampleFormControlInput1">Implementation
                                                                                        Plan Year Wise (Max 2500
                                                                                        Characters)  </label>
                                                                                    @php
                                                                                    $serviceValue = isset($service[$key]) ? $service[$key] : '';
                                                                                    @endphp
                                                                                    {{-- @dd($key) --}}
                                                                                    <textarea name="imp_plan[]"  id="imp_plan" class=" form-control text_editor" placeholder="Enter Implementation Plan Year Wise">{{ $material_details != '' ? $serviceValue : '' }}
                                                                                    </textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>

                                                        </div>





                                                        {{-- @if ($key == 0)
                                                                <div class="col-xl-3 col-lg-3 col-md-6">
                                                                    <div class="form-group change">
                                                                        <a class="btn btn-alert add-more-third">+ Add More</a>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 my-class">
                                                                </div>
                                                            @else --}}
                                                        {{-- <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                    <div class="form-group ">
                                                                        <label for="">&nbsp;</label><a
                                                                            class="btn btn-success remove">-
                                                                            Remove</a>
                                                                    </div>
                                                                </div>
                                                            @endif --}}
                                                    </div>
                                                    @endforeach





                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @endif


                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Benefit (Max 2500
                                            Characters)</label>

                                        <textarea name="benefit" id="benefit" class="form-control auto-resize-textarea" value="{{ $material_details != '' ? $material_details->benefit : '' }}" placeholder=" Enter Benefit">{{ $material_details != '' ? $material_details->benefit : '' }}</textarea>
                                    </div>
                                </div>


                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Nature of Work</label> <br>
                                        <select class="form-control" id="worktype" name="worktype">
                                            <option value="">Select Type Nature of Work</option>
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
                                        <select class="form-control" id="mode_award" name="mode_award">
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
                            </div>
                            <br>

                            <div class="col-12 mb-2">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                                            Scheme Details </h6>

                                        <div id="collapseThree" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                            <div class="card-body">

                                                <div class="row after-add-more-scheme">
                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlSelect1">Scheme Type /
                                                                Category</label>
                                                            <select class="form-control" id="scheme_type" name="scheme_type">
                                                                <option value="">Select Category</option>
                                                                <option value="Load Growth" {{ ($material_details != '' ? $material_details->scheme_type : '') == 'Load Growth' ? 'selected' : '' }}>
                                                                    Load Growth</option>
                                                                <option value="System Improvement" {{ ($material_details != '' ? $material_details->scheme_type : '') == 'System Improvement' ? 'selected' : '' }}>
                                                                    System Improvement</option>
                                                                <option value="Technology" {{ ($material_details != '' ? $material_details->scheme_type : '') == 'Technology' ? 'selected' : '' }}>
                                                                    Technology</option>
                                                                <option value="Statutory" {{ ($material_details != '' ? $material_details->scheme_type : '') == 'Statutory' ? 'selected' : '' }}>
                                                                    Statutory</option>
                                                                <option value="Infrastructure" {{ ($material_details != '' ? $material_details->scheme_type : '') == 'Infrastructure' ? 'selected' : '' }}>
                                                                    Infrastructure</option>
                                                                <option value="Deposit" {{ ($material_details != '' ? $material_details->scheme_type : '') == 'Deposit' ? 'selected' : '' }}>
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
                                                        class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Scheme No</label>
                                                            <input type="text" class="form-control"
                                                                 id="scheme_no"
                                                                name="scheme_no[]" placeholder="Enter Scheme No"
                                                                value="{{ $material_details != '' ? $sch_noo : '' }} " maxlength="10"
                                                                >
                                                        </div>
                                                    </div>

                                            <div class="col-12 mb-2">
                                                <div class="form-group">
                                                    <label for="exampleFormControlInput1">Scheme
                                                        Description </label>

                                                        <textarea name="scheme_des[]" id="scheme_des" class="form-control auto-resize-textarea" placeholder="Enter Scheme Description">
                                                            @if(isset($sch_des[$key]))
                                                                {{ $sch_des[$key] }}
                                                            @else
                                                                {{ $material_details != '' ? '' : '' }}
                                                            @endif
                                                        </textarea>

                                                </div>
                                            </div>
                                            @if ($key == 0)
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                <div class="form-group change">
                                                    <a class="btn btn-alert add-more-scheme">+ Add
                                                        More</a>
                                                </div>
                                            </div>
                                            <div class="col-12 my-class-scheme">
                                            </div>
                                            @else
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                <div class="form-group ">
                                                    <label for="">&nbsp;</label><a class="btn btn-success remove">-
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
                            <!-- <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2"> -->
                                @if($nv->budget_type == 'CAPEX') 
                            <div class="col-12 mb-2">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                                            DERC Details  </h6>

                                        <div id="collapseFour" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-xl-6 col-lg-6 col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">DERC Proposal
                                                                Number</label>
                                                            <input type="text" class="form-control" id="prop_number" name="prop_number" value="{{ $material_details != '' ? $material_details->prop_number : '' }}" placeholder="Enter Proposal Number">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">DERC Reference
                                                                No</label>
                                                            <input type="text" class="form-control" id="derc_ref_no" name="derc_ref_no" value="{{ $material_details != '' ? $material_details->derc_ref_no : '' }}" placeholder="Enter DERC Reference No">
                                                        </div>
                                                    </div>

                                                    <!-- </div> -->

                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlSelect1">DERC Approval
                                                                Status</label>
                                                            <select class="form-control" id="derc_approval" name="derc_approval">
                                                                <option value="">Select DERC Approval Status
                                                                </option>
                                                                <option value="Not Required" {{ ($material_details != '' ? $material_details->derc_approval : '') == 'Not Required' ? 'selected' : '' }}>
                                                                    Not Required</option>
                                                                <option value="Approved" {{ ($material_details != '' ? $material_details->derc_approval : '') == 'Approved' ? 'selected' : '' }}>
                                                                    Approved</option>
                                                                <option value="Pre Approved Buckets" {{ ($material_details != '' ? $material_details->derc_approval : '') == 'Pre Approved Buckets'
                                                                                ? 'selected'
                                                                                : '' }}>
                                                                    Pre Approved Buckets</option>
                                                                <option value="DPR Submitted" {{ ($material_details != '' ? $material_details->derc_approval : '') == 'DPR Submitted' ? 'selected' : '' }}>
                                                                    DPR Submitted</option>
                                                                <option value="DPR Not Submitted" {{ ($material_details != '' ? $material_details->derc_approval : '') == 'DPR Not Submitted' ? 'selected' : '' }}>
                                                                    DPR Not Submitted</option>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">DERC Approval
                                                                Date</label>
                                                            <input type="date" class="form-control" name="derc_app_date" id="derc_app_date" value="{{ $material_details != '' ? $material_details->derc_app_date : '' }}" max="9999-12-31">
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
                                        <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                            Material Details</h6>

                                        <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <form enctype="multipart/form-data" method="post" id="materialboqform">
                                                    <input type="hidden" class="form-control" name="nv_id" id="nv_id" value="{{ request()->segment(4) }}">
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


                                                        <div class="col-6 mb-2">
                                                            <div class="form-group">
                                                                <label for="form-control exampleFormControlInput1">
                                                                    Search Material </label>
                                                                <select name="search_material" style="width: 100%;" id="search_material"></select>

                                                            </div>
                                                        </div>

                                                        <div class="col-6 mb-2">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Material
                                                                    Code</label>
                                                                <input type="number" class="form-control" placeholder="Enter material Code" name="material_code_0" id="material_code_0" value="" onkeyup="autofetchMaterialCode_0()">
                                                                <div class="common-error form-text material_code_error"></div>
                                                            </div>
                                                        </div>


                                                        <!-- <div class="col-6 mb-2"> -->
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">
                                                                <!-- Material
                                                                                                            Description -->
                                                            </label>

                                                            <input type="hidden" name="mat_des_0" id="mat_des_0" value="" class="form-control" placeholder=" Enter material Description">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">
                                                                <!-- UoM -->
                                                            </label>
                                                            <input type="hidden" class="form-control" name="uom_0" id="uom_0" value="" placeholder="Enter Uom">
                                                        </div>
                                                        <!-- </div> -->

                                                        <div class="col-6 mb-2">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Rate</label>
                                                                <input type="number" class="form-control" placeholder="Enter Rate" name="rate" value="" id="rate" onblur="sumSolarCap()">
                                                                <div class="common-error form-text rate_error"></div>
                                                            </div>
                                                        </div>

                                                        <div class="col-6 mb-2">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Quantity
                                                                    Required</label>
                                                                <input type="number" class="form-control" placeholder="Enter Quantity Required" name="quantity" id="quantity" value="" onblur="sumSolarCap()">
                                                                <div class="common-error form-text quantity_error"></div>
                                                            </div>
                                                        </div>

                                                        <!-- <div class="col-6 mb-2"> -->
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">
                                                                <!-- Total
                                                                                                            Amount -->
                                                            </label>
                                                            {{-- <input type="hidden" class="form-control" placeholder="Enter Total Amount" name="total_amount" id="total_amount" value="{{ $material_details != '' ? $m7[$key] : '' }}" onblur="sumSolarCap()"> --}}
                                                            <input type="hidden" class="form-control" placeholder="Enter Total Amount" name="total_amount" id="total_amount" value="{{ isset($m7[$key]) ? $m7[$key] : '' }}" onblur="sumSolarCap()">

                                                        </div>
                                                        <!-- </div> -->

                                                        <!-- <div class="col-6 mb-2"> -->
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">
                                                                <!-- Delivery Schedule -->
                                                            </label>
                                                            {{-- <input type="hidden" class="form-control" placeholder="Enter Delivery Schdule" name="delivery_schedule[]" id="delivery_schedule" value="{{ $material_details != '' ? $m8[$key] : '' }}"> --}}
                                                            <input type="hidden" class="form-control" placeholder="Enter Delivery Schedule" name="delivery_schedule[]" id="delivery_schedule" value="{{ isset($m8[$key]) ? $m8[$key] : '' }}">

                                                        </div>
                                                        <!-- </div> -->

                                                        <div class="col-6 mb-2">
                                                            <div class="form-group change">
                                                                <br>

                                                                <button type="button" class="btn btn-alert add-more-second" id="materialBoqsave">Save</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                        </div>

                                                        <form enctype="multipart/form-data" method="post" id="upload-form">
                                                            <div class="col-xl-6 col-lg-6">
                                                                <div class="form-group">
                                                                    <label for="materialboq">Upload Material
                                                                        BOQ
                                                                        <a class="ml-2" download="{{ asset('materials-doc/MaterialBOQ.xlsx') }}" href="{{ asset('materials-doc/MaterialBOQ.xlsx') }}"><i class="fa fa-download" title="Download"></i></a>
                                                                    </label>
                                                                    <input type="file" class="form-control" id="materialboq" name="materialboq">
                                                                </div>
                                                            </div>
                                                            <input type="hidden" class="form-control" name="nv_id" id="nv_id" value="{{ request()->segment(4) }}">
                                                            <div class="col-xl-6 col-lg-6">
                                                                <div class="form-group">
                                                                    <label style="visibility:hidden">Upload</label>
                                                                    <br>
                                                                    <button type="button" class="btn btn-success" id="materialBoqUpload">Upload</button>
                                                                </div>
                                                            </div>
                                                        </form>



                                                        <!-- /.container-fluid -->

                                                    </div>

                                                    <?php
                                                    $total = 0;
                                                    $m_importAmount = 0;
                                                    $totalvalue = $total;
                                                    ?>

                                                    @foreach ($material_import as $key => $m_import)
                                                    <?php
                                                    $m_importAmount = (int) $m_import->amount;
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

                                                                <!-- /.card-header -->
                                                                <div class="card-body table-responsive">
                                                                 

                                                                        <div class="btn-group-toggle" data-toggle="buttons">
                                                                            <label class="btn btn-sm btn-info toggle-btn" data-option="expand">
                                                                                <input type="radio" name="expand-collapse-option" autocomplete="off"> Expand
                                                                            </label>
                                                                            <label class="btn btn-sm btn-info toggle-btn" data-option="collapse">
                                                                                <input type="radio" name="expand-collapse-option" autocomplete="off">
                                                                                Collapse
                                                                            </label>

                                                                            <lable>
                                                                                <a href="javascript:;" data-id="{{ request()->segment(4) }}" class="btn btn-sm btn-clean btn-icon delete_all_materials" title="Delete">Delete All &nbsp;<i class="fas fa-trash text-danger"></i></a>
                                                                                </label>

                                                                                

                                                                        </div>
                                                                
                                                                    <table id="nv_datatable" class="table table-bordered">
                                                                        <input type="hidden" class="form-control" name="nv_id" id="nv_id" value="{{ request()->segment(4) }}">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="text-center" width="5%">S.No</th>
                                                                                <th class="text-center" width="5%">NV Id</th>
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
                                                                                <th width="15%" class="expandable">Action
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                    </table>
                                                                </div>

                                                            </div>
                                                            <!-- /.card -->
                                                        </div>
                                                        <!-- /.col -->
                                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Total Material
                                                                    Amount <input type="text" class="form-control" placeholder="Total
                                                                                Amount" name="total_mat_mat" id="total_mat_mat" value="{{ $total ?? null }}" readonly>


                                                            </div>
                                                        </div>
                                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Budget
                                                                    Available <span class="mandatory_input">*</span></label>
                                                                <input type="hidden" id="avlbgt" value="{{ $avlbgt }}">
                                                                <?php 
                                                                    $amount = $avlbgt;

                                                                    $amount = moneyFormatIndia( $amount );
                                                                    function moneyFormatIndia($num) {
                                                                    
                                                                        $explrestunits = "" ;
                                                                    
                                                                        if(strlen($num)>3) {
                                                                    
                                                                            $lastthree = substr($num, strlen($num)-3, strlen($num));
                                                                    
                                                                            $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
                                                                    
                                                                            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
                                                                    
                                                                            $expunit = str_split($restunits, 2);
                                                                    
                                                                            for($i=0; $i<sizeof($expunit); $i++) {
                                                                    
                                                                                // creates each of the 2's group and adds a comma to the end
                                                                    
                                                                                if($i==0) {
                                                                    
                                                                                    $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
                                                                    
                                                                                } else {
                                                                    
                                                                                    $explrestunits .= $expunit[$i].",";
                                                                    
                                                                                }
                                                                    
                                                                            }
                                                                    
                                                                            $thecash = $explrestunits.$lastthree;
                                                                    
                                                                        } else {
                                                                    
                                                                            $thecash = $num;
                                                                    
                                                                        }
                                                                    
                                                                        return $thecash; 
                                                                    
                                                                    }
                                                       
         
                                                                ?> 
                                                                
                                                                <input type="text" readonly class="form-control" placeholder="Budget Available" name="budget_avl" id="budget_avl" value="{{ $amount ?? '0' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                 
                                                               
                                                        

                                            </div>

                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card-body">
                                                        <div class="row after-add-more-scheme-material">
                                                            @php
                                                            // echo $material_details->material_amount; exit();
                                                            $material_amount = explode(',', $material_details != '' ? $material_details->material_amount : '0');
                                                            $material_descripition = explode(',', $material_details != '' ? $material_details->material_description : '');
                                                        //    print_r($material_descripition);
                                                        //    exit;
                                                            
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
                                                                        <input type="hidden" name="mat_amt" id="mat_amt" value="0">

                                                                        
                                                                        <input type="text" class="form-control" name="material_amount[]"  id="material_amount"
                                                                            placeholder="Amount" value="{{ $material_details != '' ? $material_amt : '' }}">
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                                    <div class="form-group">
                                                                    
                                                                        <input type="text" class="form-control"
                                                                            id="material_descripition" name="material_description[]"
                                                                            placeholder="Description" value="{{@$material_descripition[$key]}}">
                                                                    </div>
                                                                </div>

                                                                @if ($key == 0)
                                                                <div
                                                                    class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                                    <div class="form-group change">
                                                                        <a class="btn btn-alert add-more-button_material">+
                                                                            Add
                                                                            More</a>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 my-class-scheme_material">
                                                                </div>
                                                                @else
                                                                <div
                                                                    class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                    <div class="form-group ">
                                                                        <label for="">&nbsp;</label><a
                                                                            class="btn btn-success remove">-
                                                                            Remove</a>
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

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapsefour" aria-expanded="true" aria-controls="collapsefour">
                                            Rate Reference </h6>

                                        <div id="collapsefour" class="collapse show" aria-labelledby="collapsefour" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">C&M Rate
                                                                Reference @if (!empty($material_doc) && !empty($material_doc->cm_rate_ref))
                                                                <a class="ml-2" download="{{ $material_doc->cm_rate_ref }}" href="{{ url(asset('materials-doc/' . $material_doc->cm_rate_ref)) }}"><i class="fa fa-download" title="Download"></i></a>
                                                                @endif
                                                            </label>
                                                            <input type="file" class="form-control" name="cm_rate_ref" id="cm_rate_ref">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Vendor
                                                                Quotation @if (!empty($material_doc) && !empty($material_doc->vend_quatation))
                                                                <a class="ml-2" download="{{ $material_doc->vend_quatation }}" href="{{ url(asset('materials-doc/' . $material_doc->vend_quatation)) }}"><i class="fa fa-download" title="Download"></i></a>
                                                                @endif
                                                            </label>
                                                            <input type="file" class="form-control" name="vend_quatation" id="vend_quatation">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Last Purchase
                                                                Price @if (!empty($material_doc) && !empty($material_doc->last_purchase_price))
                                                                <a class="ml-2" download="{{ $material_doc->last_purchase_price }}" href="{{ url(asset('materials-doc/' . $material_doc->last_purchase_price)) }}"><i class="fa fa-download" title="Download"></i></a>
                                                                @endif
                                                            </label>
                                                            <input type="file" class="form-control" name="last_purchase_price" id="last_purchase_price">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">User
                                                                Estimation @if (!empty($material_doc) && !empty($material_doc->user_estimation))
                                                                <a class="ml-2" download="{{ $material_doc->user_estimation }}" href="{{ url(asset('materials-doc/' . $material_doc->user_estimation)) }}"><i class="fa fa-download" title="Download"></i></a>
                                                                @endif </label>
                                                            <input type="file" class="form-control" name="user_estimation" id="user_estimation">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($nv->budget_type === 'CAPEX')
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <div id="myRadioGroup">

                                        <label for="exampleFormControlInput1">Capacity Addition</label> <br>



                                        @if (!empty($material_details) && !empty($material_details->cap_add))
                                        Yes &nbsp; <input type="radio" id="chkYes" name="cars-first" value="4" {{ ($material_details != '' ? $material_details->cap_add : '') == '4' ? 'checked' : '' }} />&nbsp;
                                        &nbsp;
                                        |&nbsp; &nbsp;
                                        No &nbsp;<input type="radio" name="cars-first" value="5" {{ ($material_details != '' ? $material_details->cap_add : '') == '5' ? 'checked' : '' }} />
                                        @else
                                        Yes &nbsp; <input type="radio" name="cars-first" value="4" />&nbsp; &nbsp;
                                        |&nbsp; &nbsp;
                                        No &nbsp;<input type="radio" name="cars-first" value="5" checked="checked" />
                                        @endif


                                        <br>
                                        <br>

                                        <div id="First4" class="first-radio">
                                            <div class="row">
                                                <div class="col-xl-6 col-lg-6">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">PTR
                                                            MVA</label>
                                                        <input type="number" class="form-control" name="ptr_mva" id="ptr_mva" placeholder="Enter PTR MVA" value="{{ $material_details != '' ? $material_details->ptr_mva : '' }}">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">DT
                                                            MVA</label>
                                                        <input type="number" class="form-control" name="dt_mva" id="dt_mva" placeholder="Enter DT MVA" value="{{ $material_details != '' ? $material_details->dt_mva : '' }}">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 mt-2 ">
                                                    <div class="form-group">
                                                        <label style="display:block;" for="exampleFormControlInput1">EHV Line(Ckt.km)
                                                            <input type="number" class="form-control" name="ehv_line" id="ehv_line" placeholder="Enter EHV Line(Ckt.km)" value="{{ $material_details != '' ? $material_details->ehv_line : '' }}">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">HT
                                                            Line(Ckt.km)</label>
                                                        <input type="number" class="form-control" name="ht_line" id="ht_line" placeholder="Enter HT Line(Ckt.km)" value="{{ $material_details != '' ? $material_details->ht_line : '' }}">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">LT
                                                            Line(Ckt.km)</label>
                                                        <input type="number" class="form-control" name="lt_line" id="lt_line" placeholder="Enter LT Line(Ckt.km)" value="{{ $material_details != '' ? $material_details->lt_line : '' }}">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div id="First5" class="first-radio" style="display: none;">

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
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1" title="Material Trail Details and Schudule of Pilot feedback Submission">New
                                        Product @if (!empty($material_doc) && !empty($material_doc->new_product))
                                        <a class="ml-2" download="{{ $material_doc->new_product }}" href="{{ url(asset('materials-doc/' . $material_doc->new_product)) }}"><i class="fa fa-download" title="Download"></i></a>
                                        @endif
                                    </label>
                                    <input type="file" class="form-control" name="new_product" id="new_product" placeholder="New Product">
                                </div>
                            </div>

                            <!-- <div class="col-12 mb-2">
                                                                            <div class="accordion" id="accordionExample">
                                                                                <div class="accordion-item">
                                                                                    <h6 class="accordion-header" type="button" data-toggle="collapse"
                                                                                        data-target="#collapsefour" aria-expanded="true"
                                                                                        aria-controls="collapsefour">
                                                                                        Rate Reference </h6>

                                                                                    <div id="collapsefour" class="collapse show" aria-labelledby="collapsefour"
                                                                                        data-parent="#accordionExample">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="col-xl-6 col-lg-6">
                                                                                                    <div class="form-group">
                                                                                                        <label for="exampleFormControlInput1">C&M Rate
                                                                                                            Reference @if (!empty($material_doc) && !empty($material_doc->cm_rate_ref))
    <a class="ml-2"
                                                                                                                download="{{ $material_doc->cm_rate_ref }}"
                                                                                                                href="{{ url(asset('materials-doc/' . $material_doc->cm_rate_ref)) }}"><i
                                                                                                                    class="fa fa-download"
                                                                                                                    title="Download"></i></a>
    @endif
                                                                                                        </label>
                                                                                                        <input type="file" class="form-control"
                                                                                                            name="cm_rate_ref" id="cm_rate_ref">
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-xl-6 col-lg-6">
                                                                                                    <div class="form-group">
                                                                                                        <label for="exampleFormControlInput1">Vendor
                                                                                                            Quatation @if (!empty($material_doc) && !empty($material_doc->vend_quatation))
    <a class="ml-2"
                                                                                                                download="{{ $material_doc->vend_quatation }}"
                                                                                                                href="{{ url(asset('materials-doc/' . $material_doc->vend_quatation)) }}"><i
                                                                                                                    class="fa fa-download"
                                                                                                                    title="Download"></i></a>
    @endif
                                                                                                        </label>
                                                                                                        <input type="file" class="form-control"
                                                                                                            name="vend_quatation" id="vend_quatation">
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-xl-6 col-lg-6">
                                                                                                    <div class="form-group">
                                                                                                        <label for="exampleFormControlInput1">Last Purchase
                                                                                                            Price @if (!empty($material_doc) && !empty($material_doc->last_purchase_price))
    <a class="ml-2"
                                                                                                                download="{{ $material_doc->last_purchase_price }}"
                                                                                                                href="{{ url(asset('materials-doc/' . $material_doc->last_purchase_price)) }}"><i
                                                                                                                    class="fa fa-download"
                                                                                                                    title="Download"></i></a>
    @endif
                                                                                                        </label>
                                                                                                        <input type="file" class="form-control"
                                                                                                            name="last_purchase_price" id="last_purchase_price">
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-xl-6 col-lg-6">
                                                                                                    <div class="form-group">
                                                                                                        <label for="exampleFormControlInput1">User
                                                                                                            Estimation @if (!empty($material_doc) && !empty($material_doc->user_estimation))
    <a class="ml-2"
                                                                                                                download="{{ $material_doc->user_estimation }}"
                                                                                                                href="{{ url(asset('materials-doc/' . $material_doc->user_estimation)) }}"><i
                                                                                                                    class="fa fa-download"
                                                                                                                    title="Download"></i></a>
    @endif </label>
                                                                                                        <input type="file" class="form-control"
                                                                                                            name="user_estimation" id="user_estimation">
                                                                                                    </div>
                                                                                                </div>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div> -->

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Root Cause Analysis (Max 1000
                                        Characters)</label>

                                    <textarea name="root_cause_analysis" id="root_cause_analysis" value="{{ $material_details != '' ? $material_details->root_cause_analysis : '' }}" class="form-control auto-resize-textarea" placeholder="Root Cause Analysis">{{ $material_details != '' ? $material_details->root_cause_analysis : '' }}</textarea>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Cost Reduction plan/Future phasing
                                        out
                                        plan(if applicable) (Max 1000
                                        Characters)</label>

                                    <textarea name="cause_analysis" id="cause_analysis" value="{{ $material_details != '' ? $material_details->cause_analysis : '' }}" class="form-control auto-resize-textarea" placeholder="Cost Reduction plan/Future phasing out plan(if applicable) ">{{ $material_details != '' ? $material_details->cause_analysis : '' }}</textarea>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Special Remarks / 
                                        Any Specific Recommendation (Max 500 Characters)</label>


                                    <textarea name="special_remarks" id="special_remarks" value="{{ $material_details != '' ? $material_details->special_remarks : '' }}" class="form-control auto-resize-textarea" placeholder="Special Remarks / Any Specific Recommendation (Max 500 Characters)">{{ $material_details != '' ? $material_details->special_remarks : '' }}</textarea>
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item ">
                                        <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapsesix" aria-expanded="true" aria-controls="collapsesix">
                                            Attachments</h6>

                                        <div id="collapsesix" class="collapse show" aria-labelledby="collapsesix" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Copy of Previous work
                                                                order/purchase order @if (!empty($material_doc) && !empty($material_doc->previous_work_order))
                                                                <a class="ml-2" download="{{ $material_doc->previous_work_order }}" href="{{ url(asset('materials-doc/' . $material_doc->previous_work_order)) }}"><i class="fa fa-download" title="Download"></i></a>
                                                                @endif
                                                            </label>
                                                            <input type="file" class="form-control" id="previous_work_order" name="previous_work_order" accept="image/png, image/gif, image/jpeg, image/jpg, application/pdf">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Copy of
                                                                DERC/Other
                                                                Stakeholder Approvals @if (!empty($material_doc) && !empty($material_doc->derc_stakeholder_approvals))
                                                                <a class="ml-2" download="{{ $material_doc->derc_stakeholder_approvals }}" href="{{ url(asset('materials-doc/' . $material_doc->derc_stakeholder_approvals)) }}"><i class="fa fa-download" title="Download"></i></a>
                                                                @endif
                                                            </label>
                                                            <input type="file" class="form-control" id="derc_stakeholder_approvals" name="derc_stakeholder_approvals" accept="image/png, image/gif, image/jpeg, image/jpg, application/pdf,application/msword">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Consumption
                                                                details -
                                                                Last 3
                                                                Year @if (!empty($material_doc) && !empty($material_doc->consumption_details))
                                                                <a class="ml-2" download="{{ $material_doc->consumption_details }}" href="{{ url(asset('materials-doc/' . $material_doc->consumption_details)) }}"><i class="fa fa-download" title="Download"></i></a>
                                                                @endif
                                                            </label>
                                                            <input type="file" class="form-control" id="consumption_details" name="consumption_details">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Technical
                                                                Specifications @if (!empty($material_doc) && !empty($material_doc->vendor_quatation))
                                                                <a class="ml-2" download="{{ $material_doc->vendor_quatation }}" href="{{ url(asset('materials-doc/' . $material_doc->vendor_quatation)) }}"><i class="fa fa-download" title="Download"></i></a>
                                                                @endif
                                                            </label>
                                                            <input type="file" class="form-control" id="vendor_quatation" name="vendor_quatation">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Photographs
                                                                of
                                                                Product @if (!empty($material_doc) && !empty($material_doc->photo_product))
                                                                <a class="ml-2" download="{{ $material_doc->photo_product }}" href="{{ url(asset('materials-doc/' . $material_doc->photo_product)) }}"><i class="fa fa-download" title="Download"></i></a>
                                                                @endif
                                                            </label>
                                                            <input type="file" class="form-control" id="photo_product" name="photo_product">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Material
                                                                Procurement @if (!empty($material_doc) && !empty($material_doc->material_procurement))
                                                                <a class="ml-2" download="{{ $material_doc->material_procurement }}" href="{{ url(asset('materials-doc/' . $material_doc->material_procurement)) }}"><i class="fa fa-download" title="Download"></i></a>
                                                                @endif
                                                            </label>
                                                            <input type="file" class="form-control" id="material_procurement" name="material_procurement">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Budget
                                                                Statement
                                                                for
                                                                Both
                                                                OPEX/CAPEX Activities @if (!empty($material_doc) && !empty($material_doc->budget_for_both))
                                                                <a class="ml-2" download="{{ $material_doc->budget_for_both }}" href="{{ url(asset('materials-doc/' . $material_doc->budget_for_both)) }}"><i class="fa fa-download" title="Download"></i></a>
                                                                @endif
                                                            </label>
                                                            <input type="file" class="form-control" id="budget_for_both" name="budget_for_both">
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="exampleFormControlInput1">Others
                                                                @if (!empty($material_doc) && !empty($material_doc->others))
                                                                <a class="ml-2" download="{{ $material_doc->others }}" href="{{ url(asset('materials-doc/' . $material_doc->others)) }}"><i class="fa fa-download" title="Download"></i></a>
                                                                @endif
                                                            </label>
                                                            
                                                            @if ($key == $key)
                                                                <a
                                                                class="btn btn-materialBoqsave add-more-button_attached btn-sm" style="float: right">+
                                                                Add
                                                                More</a>
                                                                
                                                            @else
                                                                <label
                                                                for="">&nbsp;</label><a
                                                                class="btn btn-success remove">-
                                                                Remove</a>
                                                            @endif
                                                            <input type="file" class="form-control" id="others" name="others[]">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="row my-class-scheme_attached">
                                                    </div>


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
                                                                                <input type="text" class="form-control" id="total_budget_material"
                                                                                    name="total_budget_material"
                                                                                    value="{{ $material_details != '' ? $material_details->total_budget_material : '' }}"
                                                                                    placeholder=" (In Rs)"
                                                                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57'></input>
                                                                            </div>
                                                                        </div> -->

                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <div id="myRadioGroup">

                                        <label for="exampleFormControlInput1">Any Services Related
                                            To
                                            This
                                            NV</label> <br>
                                        @if (!empty($material_details) && !empty($material_details->ser_rel_nv))
                                        Yes &nbsp; <input type="radio" id="CheckYes" name="cars" value="2" {{ ($material_details != '' ? $material_details->ser_rel_nv : '') == '2' ? 'checked' : '' }} />&nbsp;
                                        &nbsp;
                                        |&nbsp; &nbsp;
                                        No &nbsp;<input type="radio" name="cars" value="3" {{ ($material_details != '' ? $material_details->ser_rel_nv : '') == '3' ? 'checked' : '' }} />
                                        @else
                                        Yes &nbsp; <input type="radio" name="cars" value="2" />&nbsp;
                                        &nbsp;
                                        |&nbsp; &nbsp;
                                        No &nbsp;<input type="radio" name="cars" value="3" checked="checked" />
                                        @endif

                                        <br>
                                        <br>

                                        <div id="Cars2" class="desc">
                                            <!-- <div class="row">
                                                                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label for="exampleFormControlInput1">Proposal
                                                                                                        Number</label>
                                                                                                    <input type="text" class="form-control" id="prop_number"
                                                                                                        name="prop_number"
                                                                                                        value="{{ $material_details != '' ? $material_details->prop_number : '' }}"
                                                                                                        placeholder="Enter Proposal Number">
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label for="exampleFormControlSelect1">Mode of
                                                                                                        Award(Services)</label>
                                                                                                    <select class="form-control" id="mode_award"
                                                                                                        name="mode_award">
                                                                                                        <option value="">Select Mode of Award</option>
                                                                                                        <option value="Tender" {{ ($material_details != '' ? $material_details->mode_award : '') == 'Tender' ? 'selected' : '' }}>
                                                                                                            Tender</option>
                                                                                                     
                                                                                                        <option value=" Single Vendor" {{ ($material_details != '' ? $material_details->mode_award : '') == 'Single Vendor' ? 'selected' : '' }}>
                                                                                                            Single Vendor
                                                                                                        </option>
                                                                                                        <option value="Turnkey" {{ ($material_details != '' ? $material_details->mode_award : '') == 'Turnkey' ? 'selected' : '' }}>
                                                                                                            Turnkey</option>
                                                                                                     
                                                                                                        <option value="Extension" {{ ($material_details != '' ? $material_details->mode_award : '') == 'Extension' ? 'selected' : '' }}>
                                                                                                            Extension</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div> -->
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
                                                        <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                            Past 3 Years Actual Cost Trend For Services
                                                        </h6>

                                                        <div id="collapseOne" class="collapse show" aria-labelledby="collapseOne" data-parent="#accordionExample">
                                                            <div class="card-body">
                                                                <div class="after-add-more">
                                                                    <div class="row">
                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="text" class="form-control" name="past_3_year_actual_cost_fy[]" id="past_3_year_actual_cost_fy" value="{{ $yr_f - 1 }}-{{ $yr_l - 1 }}" placeholder="{{ $yr_f - 1 }}-{{ $yr_l - 1 }}" readonly>
                                                                        </div>

                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="number" class="form-control" name="past_3_year_actual_cost[]" id="past_3_year_actual_cost" placeholder="₹" value="{{ isset($year2[0]) ? $year2[0] : '' }}">
                                                                        </div>

                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="text" class="form-control" name="past_3_year_actual_cost_service[]" id="past_3_year_actual_cost_service" value="{{ isset($year3[0]) ? $year3[0] : '' }}" placeholder="Services">
                                                                        </div>
                                                                    </div>
                                                                    <br>
                                                                    <div class="row">
                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="text" class="form-control" name="past_3_year_actual_cost_fy[]" id="past_3_year_actual_cost_fy" value="{{ $yr_f - 2 }}-{{ $yr_l - 2 }}" placeholder="{{ $yr_f - 2 }}-{{ $yr_l - 2 }}" readonly>
                                                                        </div>

                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="number" class="form-control" name="past_3_year_actual_cost[]" id="past_3_year_actual_cost" placeholder="₹" value="{{ isset($year2[1]) ? $year2[1] : '' }}">
                                                                        </div>

                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="text" class="form-control" name="past_3_year_actual_cost_service[]" id="past_3_year_actual_cost_service" value="{{ isset($year3[1]) ? $year3[1] : '' }}" placeholder="Services">
                                                                        </div>
                                                                    </div>
                                                                    <br>
                                                                    <div class="row">
                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="text" class="form-control" name="past_3_year_actual_cost_fy[]" id="past_3_year_actual_cost_fy" value="{{ $yr_f - 3 }}-{{ $yr_l - 3 }}" placeholder="{{ $yr_f - 3 }}-{{ $yr_l - 3 }}" readonly>
                                                                        </div>

                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="number" class="form-control" name="past_3_year_actual_cost[]" id="past_3_year_actual_cost" placeholder="₹" value="{{ isset($year2[2]) ? $year2[2] : '' }}">
                                                                        </div>

                                                                        <div class="col-xl-3 col-lg-3 col-md-6">
                                                                            <input type="text" class="form-control" name="past_3_year_actual_cost_service[]" id="past_3_year_actual_cost_service" value="{{ isset($year3[2]) ? $year3[2] : '' }}" placeholder="Services">
                                                                        </div>






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
                                                        <h6 class="accordion-header" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                                                            Service Details</h6>

                                                        <div id="collapseFour" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                                            <div class="card-body">
                                                                <form enctype="multipart/form-data" method="post" id="serviceboqform">
                                                                    <input type="hidden" class="form-control" name="nv_id" id="nv_id" value="{{ request()->segment(4) }}">
                                                                    <div class="row 
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

                                                                        <div class="col-6 mb-2">
                                                                            <div class="form-group">
                                                                                <label for="exampleFormControlInput1">Service
                                                                                    code</label>
                                                                                <input type="number" class="form-control" placeholder="Enter Service Code" name="service_code_0" id="service_code_0" value="" onkeyup="autofetchServiceCode()">
                                                                                <div class="common-error form-text service_code_0_error"></div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- <div class="col-6 mb-2"> -->
                                                                        <div class="form-group">
                                                                            <label for="exampleFormControlInput1">
                                                                                <!-- Service
                                                                                                                                Description -->
                                                                            </label>

                                                                            <input type="hidden" name="ser_des_0" id="ser_des_0" value="" class="form-control" placeholder=" Enter Service Description">
                                                                        </div>
                                                                        <!-- </div> -->

                                                                        <!-- <div class="col-6 mb-2"> -->
                                                                        <div class="form-group">
                                                                            <label for="exampleFormControlInput1"></label>
                                                                            <input type="hidden" class="form-control" placeholder="Enter Rate Reference" name="ser_rate_ref" id="ser_rate_ref" value="">
                                                                        </div>

                                                                        <!-- </div> -->

                                                                        <!-- <div class="col-6 mb-2"> -->
                                                                        <div class="form-group">
                                                                            <label for="exampleFormControlInput1">
                                                                                <!-- UoM -->
                                                                            </label>
                                                                            <input type="hidden" class="form-control" name="ser_uom_0" id="ser_uom_0" value="" placeholder="Enter Uom">
                                                                        </div>
                                                                        <!-- </div> -->

                                                                        <div class="col-6 mb-2">
                                                                            <div class="form-group">
                                                                                <label for="exampleFormControlInput1">Rate</label>
                                                                                <input type="number" class="form-control" placeholder="Enter Rate" name="ser_rate" value="" id="ser_rate" onblur="sumSolar()">
                                                                                <div class="common-error form-text ser_rate_error"></div>
                                                                            </div>

                                                                        </div>

                                                                        <div class="col-6 mb-2">
                                                                            <div class="form-group">
                                                                                <label for="exampleFormControlInput1">Quantity
                                                                                    Required</label>
                                                                                <input type="number" class="form-control" placeholder="Enter Quantity Required" name="ser_quantity" id="ser_quantity" value="" onblur="sumSolar()">
                                                                                <div class="common-error form-text ser_quantity_error"></div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- <div class="col-6 mb-2"> -->
                                                                        <div class="form-group">
                                                                            <label for="exampleFormControlInput1">
                                                                                <!-- Total
                                                                                                                                Amount -->
                                                                            </label>
                                                                            <input type="hidden" class="form-control" placeholder="Enter Total Amount" name="ser_total_amount" id="ser_total_amount" value="" onblur="sumSolar()">
                                                                        </div>
                                                                        <!-- </div> -->


                                                                        <div class="col-6 mb-2">

                                                                        </div>
                                                                        <br>

                                                                        <div class="col-6 mb-2">
                                                                            <div class="form-group change">
                                                                                <br>
                                                                                <button type="button" class="btn btn-success" id="serviceBoqsave" value="Save">Save</button>
                                                                                <!-- <a
                                                                                                                                class="btn btn-alert add-more-four">
                                                                                                                                + Add
                                                                                                                                More</a> -->
                                                                            </div>
                                                                        </div><br>

                                                                        <div class="col-6 mb-2">
                                                                            <div class="form-group ">
                                                                                <!-- <label for="">&nbsp;</label><a
                                                                                                                                class="btn btn-success remove">-
                                                                                                                                Remove</a> -->
                                                                            </div>
                                                                        </div>
                                                                </form>


                                                                <form enctype="multipart/form-data" method="post" id="uploads-form" style="display: flex; justify-content: space-between;">
                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <label for="serviceboq">Upload
                                                                                Service BOQ
                                                                                <a class="ml-2" download="{{ asset('materials-doc/Service BOQ.xlsx') }}" href="{{ asset('materials-doc/Service BOQ.xlsx') }}"><i class="fa fa-download" title="Download"></i></a>
                                                                            </label>
                                                                            <input type="file" class="form-control" id="serviceboq" name="serviceboq">
                                                                        </div>
                                                                        <input type="hidden" class="form-control" name="nv_id" id="nv_id" value="{{ request()->segment(4) }}">
                                                                    </div>



                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <label style="visibility:hidden">Upload</label>
                                                                            <br>
                                                                            <button type="button" class="btn btn-success" id="serviceBoqUpload">Upload</button>
                                                                        </div>
                                                                    </div>
                                                                </form>

                                                            </div>

                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="card">
                                                                        <div class="card-body table-responsive">
                                                                            <div>

                                                                                <div class="btn-group-toggle" data-toggle="buttons" style="margin-left:28px;">

                                                                                    <label for="delete_all_service">
                                                                                        <a href="javascript:;" data-id="{{ request()->segment(4) }}" class="btn btn-sm btn-clean btn-icon delete_all_service" title="Delete">Delete All<i class="fas fa-trash text-danger"></i></a>
                                                                                    </label>

                                                                                </div>
                                                                            </div>
                                                                            <div class="card-body table-responsive">
                                                                                <table id="nv_serviceBoq_datatable" class="table table-bordered">
                                                                                    <input type="hidden" class="form-control" name="nv_id" id="nv_id" value="{{ request()->segment(4) }}">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th class="text-center" width="5%">
                                                                                                S.No</th>

                                                                                            <th class="text-center" width="5%">
                                                                                                NV Id</th>
                                                                                            <th>Service Code
                                                                                            </th>

                                                                                            <th>Service
                                                                                                Description</th>
                                                                                            <th width="15%">
                                                                                                UOM</th>
                                                                                            <th width="10%">
                                                                                                Rate</th>
                                                                                            <th width="15%">
                                                                                                Quantity</th>
                                                                                            <th width="15%">
                                                                                                Amount (Rs.)
                                                                                            </th>
                                                                                            <th width="15%">
                                                                                                Action</th>

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
                                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                                        <div class="form-group">
                                                                            <label for="exampleFormControlInput1">Total Amount For Services <input type="text" class="form-control" placeholder="Budget Available" name="total_ser_amo" id="total_ser_amo" value="{{ $total ?? null }}" readonly></label>

                                                                        </div>
                                                                    </div>

                                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                                        <div class="form-group">
                                                                            <label for="exampleFormControlInput1">Budget
                                                                                Available <span class="mandatory_input">*</span></label>

                                                                            <input type="text" readonly class="form-control" placeholder="Budget Available" name="ser_budget_avl" id="ser_budget_avl" value="">
                                                                        </div>
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
                                                                        //   $service_amt = 0;
                                                                            
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
                                                                                                <input type="hidden" name="ser_amt" id="ser_amt" value="0">
                                                                                                
                                                                                                <input type="number"
                                                                                                    class="form-control" id="service_amount" name="service_amount[]"
                                                                                                    placeholder="Amount" value="{{ $material_details != '' ? $service_amt : '' }}">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div
                                                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                                                            <div class="form-group">

                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    id="service_description"
                                                                                                    name="service_description[]"
                                                                                                    placeholder="Descripition" value="{{@$service_description[$key]}}">
                                                                                            </div>
                                                                                        </div>
                                                                                        
                                                                                        @if ($key == 0)
                                                                                        <div
                                                                                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                                                                                            <div class="form-group change">
                                                                                                <a
                                                                                                    class="btn btn-alert add-more-button_service">+
                                                                                                    Add
                                                                                                    More</a>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-12 my-class-scheme-service">
                                                                                        </div>
                                                                                        @else
                                                                                        <div
                                                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                                            <div class="form-group ">
                                                                                                <label
                                                                                                    for="">&nbsp;</label><a
                                                                                                    class="btn btn-success remove">-
                                                                                                    Remove</a>
                                                                                            </div>
                                                                                        </div>
                                                                                        @endif
                                                                            @endforeach
                                                                               
                                                                                    <input type="hidden" name="" value=" {{$ser_tt}}" id="serv_amt">
                                                                        </div>

                                                                    </div>

                                                                           
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">Cost Calculation
                                                            for
                                                            services @if (!empty($material_doc) && !empty($material_doc->cost_calculation_for_service))
                                                            <a class="ml-2" download="{{ $material_doc->cost_calculation_for_service }}" href="{{ url(asset('materials-doc/' . $material_doc->cost_calculation_for_service)) }}"><i class="fa fa-download" title="Download"></i></a>
                                                            @endif
                                                        </label>
                                                        <input type="file" class="form-control" name="cost_calculation_for_service" id="cost_calculation_for_service" placeholder=" Attachement in Excel">
                                                    </div>

                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">Past Practice
                                                            followed
                                                            for
                                                            services (if any)</label>
                                                        <input type="text" class="form-control" name="past_practice_follow" id="past_practice_follow" placeholder="Past Practices followed for services (if any)" value="{{ $material_details != '' ? $material_details->past_practice_follow : '' }}">
                                                    </div>
                                                </div>



                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">AMC Proposed
                                                            Start
                                                            Date</label>
                                                        <input type="date" class="form-control" name="amc_prop_start_date" id="amc_prop_start_date" value="{{ $material_details != '' ? $material_details->amc_prop_start_date : '' }}" max="9999-12-31">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">AMC Proposed End Date</label>
                                                        <input type="date" class="form-control" name="amc_prop_end_date" id="amc_prop_end_date" value="{{ $material_details != '' ? $material_details->amc_prop_end_date : '' }}" max="9999-12-31">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                    <div class="form-group">
                                                        <input type="hidden" value="0" id="est_amt">
                                                        <label for="exampleFormControlInput1">Estimated Amount Of GST On Services (Labour+Transport)</label>
                                                        <input type="number" class="form-control" name="estimate_amount_of_service" id="estimate_amount_of_service" value="{{ $material_details != '' ? $material_details->estimate_amount_of_service : '' }}" placeholder="Estimated Amount Of GST On Services (Labour+Transport) (In Rs)" >
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">Estimated amount
                                                            of Services (Civil)</label>
                                                        <input type="hidden" value="0" id="est_amt_ser_civil">
                                                        <input type="number" class="form-control" name="estimate_amount_of_service_civil" id="estimate_amount_of_service_civil" value="{{ $material_details != '' ? $material_details->estimate_amount_of_service_civil : '' }}" placeholder=" (In Rs)">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                    <div class="form-group">
                                                        <input type="hidden" id="est_amt_rr" value="0">
                                                        <label for="exampleFormControlInput1">Estimated amount
                                                            of RR Charges</label>
                                                        <input type="number" class="form-control" name="estimate_amount_of_rr_charge" id="estimate_amount_of_rr_charge" value="{{ $material_details != '' ? $material_details->estimate_amount_of_rr_charge : '' }}" placeholder=" (In Rs)">
                                                    </div>
                                                </div>

                                                <!-- <div
                                                                                            class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                                                            <div class="form-group">
                                                                                                <label for="exampleFormControlInput1">Estimated amount
                                                                                                    -
                                                                                                    Other</label>
                                                                                                <input type="text" class="form-control"
                                                                                                    name="estimate_amount_other"
                                                                                                    id="estimate_amount_other"
                                                                                                    placeholder=" Estimated amount - Other"
                                                                                                    value="{{ $material_details != '' ? $material_details->estimate_amount_other : '' }}">
                                                                                            </div>
                                                                                        </div> -->

                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">Estimated
                                                            amount -
                                                            Other</label>
                                                            <input type="hidden" value="0" name="est_amt_oth" id="est_amt_oth">
                                                        <input type="number" class="form-control" name="estimate_amount_other" id="estimate_amount_other" placeholder=" Estimated amount - Other IN (RS)" value="{{ $material_details != '' ? $material_details->estimate_amount_other : '' }}">
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">Total Amount for
                                                            Services (Including Civil and RR)
                                                        </label>
                                                        <input type="text" class="form-control" name="total_budget_service" id="total_budget_service" placeholder=" (In Rs)" value="{{ $material_details != '' ? $material_details->total_budget_service : '' }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">Total NV Amount</label>
                                                        <input type="text" class="form-control" name="total_budget_both" id="total_budget_both" placeholder=" (In Rs)" value="{{ $material_details != '' ? $material_details->total_budget_both : '' }}" readonly>
                                                    </div>
                                                </div>



                                            </div>

                                        </div>
                                    </div>


                                    <div id="Cars3" class="desc" style="display: none;">

                                    </div>
                                    @php
                                    $Nvsericestatus = App\Models\Nvsericestatus::where('nv_id', $nv->id)->first();
                                    @endphp
                                    <input type="hidden" name="draft1" id="draft1" value="0">
                                    <input type="hidden" name="draft2" id="draft2" value="1">
                                    <div class="card">
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-lg-12 text-center">
                                                    @if ($material_details != '')
                                                    <a href="/admin/nv_material/preview/{{ $material_details != '' ? $material_details->nv_id : '' }}">
                                                        @endif
                                                        <button type="button" class="btn btn-success">Preview</button>
                                                    </a>

                                                    @if ($material_details == '')
                                                    <button type="button" class="btn btn-success save_btn" value="save_nv">Save</button>
                                                    <button type="submit" class="btn btn-success submit_btn" value="submit_nv">Submit</button>
                                                    @endif
                                                    <!-- @if($Nvsericestatus != '' && $Nvsericestatus->draft == 0)
                                                            <button type="button" class="btn btn-success save_btn" value="save_nv">Save</button>
                                                            <button type="submit" class="btn btn-success submit_btn" value="submit_nv">Submit</button>
                                                            @endif -->
                                                    @if($Nvsericestatus != '' )
                                                    @if($Nvsericestatus->draft == 0)
                                                    <button type="button" class="btn btn-success save_btn" value="save_nv">Save</button>
                                                    <button type="submit" class="btn btn-success submit_btn" value="submit_nv">Submit</button>
                                                    @elseif (($Nvsericestatus->draft == 1) &&($Nvsericestatus->rv1_status == 2||$Nvsericestatus->rv2_status == 2||$Nvsericestatus->rv3_status == 2||$Nvsericestatus->rv4_status == 2||$Nvsericestatus->hod_status == 2||$Nvsericestatus->ces_rew1_status == 2 || $Nvsericestatus-> ces_rew2_status == 2 || $Nvsericestatus->ces_rew3_status == 2 || $Nvsericestatus->ces_rew4_status == 2 || $Nvsericestatus->work_rew1_status == 2 || $Nvsericestatus->work_rew2_status == 2 ||$Nvsericestatus->work_rew3_status == 2 || $Nvsericestatus->work_rew4_status == 2 || $Nvsericestatus->approver_status == 2 || $Nvsericestatus->work_rew1dep2_status == 2 || $Nvsericestatus->work_rew2dep2_status == 2 || $Nvsericestatus->work_rew3dep2_status == 2 ||$Nvsericestatus->work_rew4dep2_status == 2 || $Nvsericestatus->approverdep2_status == 2 || $Nvsericestatus->work_rew1dep3_status == 2 || $Nvsericestatus->work_rew2dep3_status == 2 || $Nvsericestatus->work_rew3dep3_status == 2 || $Nvsericestatus->work_rew4dep3_status == 2 || $Nvsericestatus->approverdep3_status == 2 || $Nvsericestatus->work_rew1dep4_status == 2 || $Nvsericestatus->work_rew2dep4_status == 2 || $Nvsericestatus->work_rew3dep4_status == 2 || $Nvsericestatus->work_rew4dep4_status == 2 || $Nvsericestatus->approverdep4_status == 2 || $Nvsericestatus->work_rew1dep5_status == 2 || $Nvsericestatus->work_rew2dep5_status == 2 || $Nvsericestatus->work_rew3dep5_status == 2 || $Nvsericestatus->work_rew4dep5_status == 2 ||$Nvsericestatus->approverdep5_status == 2))
                                                    <button type="button" class="btn btn-success save_btn" value="save_nv">Save</button>
                                                    <button type="submit" class="btn btn-success submit_btn" value="submit_nv">Submit</button>

                                                    @endif
                                                    @endif

                                                    <button type="reset" class="btn btn-secondary" onclick="history.back();">Reset</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>




                            {{-- <div class="card">
                                            <div class="card-footer">
                                                <div class="row">
                                                    <div class="col-lg-12 text-center">
                                                        @if ($material_details != '')
                                                            <a
                                                                href="/admin/nv_material/preview/{{ $material_details != '' ? $material_details->nv_id : '' }}">
                            @endif
                            <button type="button" class="btn btn-success">Preview</button>
                            </a>
                            <button type="submit" class="btn btn-success ">Save</button>
                            <button type="reset" class="btn btn-secondary" onclick="history.back();">Reset</button>
                        </div>
                    </div>
            </div>
        </div> --}}
    </div>
    </div>
    </div>


    </form>
    </div>




    </form>
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
<input type="hidden" id="tokennm" value="{{ csrf_token() }}">
@endsection

@push('script')
<script>
    //auto fetch materialdata
    function autofetchMaterialCode_0() {
        var token = "{{ csrf_token() }}";
        var material_code = $("#material_code_0").val();
        console.log(material_code);
        if (material_code.length >= 6) {
            $.ajax({
                url: "{{ route('capex.auto.fetch.data') }}",
                type: 'post',
                dataType: "json",
                data: {
                    '_token': token,
                    'key': material_code,
                },
                success: function(data) {
                    //response(data);
                    $('#mat_des_0').val(data.material_short_text);
                    $('#uom_0').val(data.uom);
                    $('#rate').val(data.rate_add);
                }

            });


        }
        //   count++;
    };


    //auto fetch servicedata
    function autofetchServiceCode() {
        var token = "{{ csrf_token() }}";
        var service_code = $("#service_code_0").val();
        if (service_code.length >= 6) {
            $.ajax({
                url: "{{ route('capex.auto.fetch.servicedata') }}",
                type: 'post',
                dataType: "json",
                data: {
                    '_token': token,
                    'key': service_code,
                },
                success: function(data) {
                    //response(data);
                    $('#ser_des_0').val(data.service_short_text);
                    $('#ser_uom_0').val(data.bun);
                    $('#ser_rate').val(data.rate_ser);
                }
            });

        }
    };




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


    //Rate calculation
    function sumSolarCap() {
        var sce = $('#rate').val();
        var sca = $('#quantity').val();
        var sum = (1 * sce) * (1 * sca);
        $('#total_amount').val(sum);
    }

    //Rate calculation for ServiceBOQ
    function sumSolar() {
        var sce = $('#ser_rate').val();
        var sca = $('#ser_quantity').val();
        var sum = (1 * sce) * (1 * sca);
        $('#ser_total_amount').val(sum);
    }

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
            var table = $('#nv_datatable').DataTable();
            var columns = table.columns().indexes();

            // Expand or collapse columns from index 8 to 19 (April to March) based on the selected option
            if (option === 'expand') {
                for (var i = 10; i <= 21; i++) {
                    table.column(columns[i]).visible(true);
                }
            } else if (option === 'collapse') {
                for (var i = 10; i <= 21; i++) {
                    table.column(columns[i]).visible(false);
                }
            }
        });

    });


    //  $(document).ready(function() {

    //     $('#materialBoqsave').click(function() {

    //       $.ajax({
    //         url: 'your-server-side-url', 
    //         type: 'POST', 
    //         success: function(response) {

    //           $('#total_mat_mat').val(response.totalAmount);
    //         },
    //         error: function() {

    //           alert('An error occurred while reloading the field.');
    //         }
    //       });
    //     });
    //   });
</script>

<script>
    // function myFunction() {
    //     var x = document.getElementById("scheme_no").value;
    //     fetch(x);
    // }

    // function fetch(x) {
    //     $.ajax({
    //         url: "{{ url('admin/fetchDataApi') }}",
    //         type: "POST",
    //         headers: {
    //             'X-CSRF-Token': '{{ csrf_token() }}',
    //         },
    //         //dataType: "json",   
    //         data: {
    //             scheme_number: '00' + x
    //         },
    //         success: function(data) {
    //             //alert("success");
    //             var json = JSON.parse(data);
    //             $("#scheme_des").val(json.TEXT_DESC);

    //         },
    //         error: function(data) {
    //             //     alert("Scheme number. not found");
    //             $("#scheme_des").val("");
    //         }
    //     })
    // }



    // function myFunction2() {
    //     var x = document.getElementById("scheme_no2").value;
    //     fetch2(x);
    // }

    // function fetch2(x) {
    //     $.ajax({
    //         url: "{{ url('admin/fetchDataApi') }}",
    //         type: "POST",
    //         headers: {
    //             'X-CSRF-Token': '{{ csrf_token() }}',
    //         },
    //         //dataType: "json",   
    //         data: {
    //             scheme_number: '00' + x
    //         },
    //         success: function(data) {
    //             //alert("success");
    //             var json = JSON.parse(data);
    //             $("#scheme_des2").val(json.TEXT_DESC);

    //         },
    //         error: function(data) {
    //             //     alert("Scheme number. not found");
    //             $("#scheme_des2").val("");
    //         }
    //     })
    // }


    // function myFunction3() {
    //     var x = document.getElementById("scheme_no3").value;
    //     fetch3(x);
    // }

    // function fetch3(x) {
    //     $.ajax({
    //         url: "{{ url('admin/fetchDataApi') }}",
    //         type: "POST",
    //         headers: {
    //             'X-CSRF-Token': '{{ csrf_token() }}',
    //         },
    //         //dataType: "json",   
    //         data: {
    //             scheme_number: '00' + x
    //         },
    //         success: function(data) {
    //             //alert("success");
    //             var json = JSON.parse(data);
    //             $("#scheme_des3").val(json.TEXT_DESC);

    //         },
    //         error: function(data) {
    //             //     alert("Scheme number. not found");
    //             $("#scheme_des3").val("");
    //         }
    //     })
    // }


    // function myFunction4() {
    //     var x = document.getElementById("scheme_no4").value;
    //     fetch4(x);
    // }

    // function fetch4(x) {
    //     $.ajax({
    //         url: "{{ url('admin/fetchDataApi') }}",
    //         type: "POST",
    //         headers: {
    //             'X-CSRF-Token': '{{ csrf_token() }}',
    //         },
    //         //dataType: "json",   
    //         data: {
    //             scheme_number: '00' + x
    //         },
    //         success: function(data) {
    //             //alert("success");
    //             var json = JSON.parse(data);
    //             $("#scheme_des4").val(json.TEXT_DESC);

    //         },
    //         error: function(data) {
    //             //     alert("Scheme number. not found");
    //             $("#scheme_des4").val("");
    //         }
    //     })
    // }


    // function myFunction5() {
    //     var x = document.getElementById("scheme_no5").value;
    //     fetch5(x);
    // }

    // function fetch5(x) {
    //     $.ajax({
    //         url: "{{ url('admin/fetchDataApi') }}",
    //         type: "POST",
    //         headers: {
    //             'X-CSRF-Token': '{{ csrf_token() }}',
    //         },
    //         //dataType: "json",   
    //         data: {
    //             scheme_number: '00' + x
    //         },
    //         success: function(data) {
    //             //alert("success");
    //             var json = JSON.parse(data);
    //             $("#scheme_des5").val(json.TEXT_DESC);

    //         },
    //         error: function(data) {
    //             //     alert("Scheme number. not found");
    //             $("#scheme_des5").val("");
    //         }
    //     })
    // }


    // function myFunction6() {
    //     var x = document.getElementById("scheme_no6").value;
    //     fetch6(x);
    // }

    // function fetch6(x) {
    //     $.ajax({
    //         url: "{{ url('admin/fetchDataApi') }}",
    //         type: "POST",
    //         headers: {
    //             'X-CSRF-Token': '{{ csrf_token() }}',
    //         },
    //         //dataType: "json",   
    //         data: {
    //             scheme_number: '00' + x
    //         },
    //         success: function(data) {
    //             //alert("success");
    //             var json = JSON.parse(data);
    //             $("#scheme_des6").val(json.TEXT_DESC);

    //         },
    //         error: function(data) {
    //             //     alert("Scheme number. not found");
    //             $("#scheme_des6").val("");
    //         }
    //     })
    // }


    // function myFunction7() {
    //     var x = document.getElementById("scheme_no7").value;
    //     fetch7(x);
    // }

    // function fetch7(x) {
    //     $.ajax({
    //         url: "{{ url('admin/fetchDataApi') }}",
    //         type: "POST",
    //         headers: {
    //             'X-CSRF-Token': '{{ csrf_token() }}',
    //         },
    //         //dataType: "json",   
    //         data: {
    //             scheme_number: '00' + x
    //         },
    //         success: function(data) {
    //             //alert("success");
    //             var json = JSON.parse(data);
    //             $("#scheme_des7").val(json.TEXT_DESC);

    //         },
    //         error: function(data) {
    //             //     alert("Scheme number. not found");
    //             $("#scheme_des7").val("");
    //         }
    //     })
    // }


    // function myFunction8() {
    //     var x = document.getElementById("scheme_no8").value;
    //     fetch8(x);
    // }

    // function fetch8(x) {
    //     $.ajax({
    //         url: "{{ url('admin/fetchDataApi') }}",
    //         type: "POST",
    //         headers: {
    //             'X-CSRF-Token': '{{ csrf_token() }}',
    //         },
    //         //dataType: "json",   
    //         data: {
    //             scheme_number: '00' + x
    //         },
    //         success: function(data) {
    //             //alert("success");
    //             var json = JSON.parse(data);
    //             $("#scheme_des8").val(json.TEXT_DESC);

    //         },
    //         error: function(data) {
    //             //     alert("Scheme number. not found");
    //             $("#scheme_des8").val("");
    //         }
    //     })
    // }


    // function myFunction9() {
    //     var x = document.getElementById("scheme_no9").value;
    //     fetch9(x);
    // }

    // function fetch9(x) {
    //     $.ajax({
    //         url: "{{ url('admin/fetchDataApi') }}",
    //         type: "POST",
    //         headers: {
    //             'X-CSRF-Token': '{{ csrf_token() }}',
    //         },
    //         //dataType: "json",   
    //         data: {
    //             scheme_number: '00' + x
    //         },
    //         success: function(data) {
    //             //alert("success");
    //             var json = JSON.parse(data);
    //             $("#scheme_des9").val(json.TEXT_DESC);

    //         },
    //         error: function(data) {
    //             //     alert("Scheme number. not found");
    //             $("#scheme_des9").val("");
    //         }
    //     })
    // }




    //  var totalMatMat = document.getElementById('total_mat_mat');
    // var totalSerAmo = document.getElementById('total_ser_amo');
    // var estimateSerAmoCivil = document.getElementById('estimate_amount_of_service_civil');
    // var estimateAmountOfService = document.getElementById('estimate_amount_of_service');
    // var estimateAmountOfRRCharge = document.getElementById('estimate_amount_of_rr_charge');
    // var estimateAmountOther = document.getElementById('estimate_amount_other');
    // var totalCivilandRR = document.getElementById('total_budget_service');
    // var totalBudgetNV = document.getElementById('total_budget_both');


    // var sum = Number(totalMatMat.value) + Number(totalSerAmo.value) + Number(estimateAmountOfService.value)
    //  + Number(estimateAmountOfRRCharge.value)+ Number(estimateSerAmoCivil.value)+ Number(totalCivilandRR.value)+ Number(estimateAmountOther.value);


    // totalBudgetNV.value = sum;


    // Your JavaScript code
    // Your JavaScript code
    // Your JavaScript code

    // function service_amount() {
    //     var totalMat = Number(document.getElementById('total_mat_mat').value) || 0;
    //     var totalBudget = Number(document.getElementById('budget_avl').value) || 0;

    //     var sum = totalBudget - totalMat;
    //     const totalSerbgt = document.getElementById('ser_budget_avl');
    //     if (totalSerbgt.value !== sum.toString()) {
    //         totalSerbgt.value = sum;
    //     }
    // }
    // service_amount();



    let previousValues = {};

    // function sumSolarCap_2() {
    //     var totalMatMat = Number(document.getElementById('total_mat_mat').value) || 0;
    //     var totalSerAmo = Number(document.getElementById('total_ser_amo').value) || 0;
    //     var estimateSerAmoCivil = Number(document.getElementById('estimate_amount_of_service_civil').value) || 0;
    //     var estimateAmountOfService = Number(document.getElementById('estimate_amount_of_service').value) || 0;
    //     var estimateAmountOfRRCharge = Number(document.getElementById('estimate_amount_of_rr_charge').value) || 0;
    //     var estimateAmountOther = Number(document.getElementById('estimate_amount_other').value) || 0;
    //     var totalCivilandRR = Number(document.getElementById('total_budget_service').value) || 0;

    //     var sum = totalMatMat + totalSerAmo + estimateAmountOfService +
    //         estimateAmountOfRRCharge + estimateSerAmoCivil + totalCivilandRR +
    //         estimateAmountOther;
    //     const totalBudgetNV = document.getElementById('total_budget_both');
    //     if (totalBudgetNV.value !== sum.toString()) {
    //         totalBudgetNV.value = sum;
    //     }

    //     var sumofservices = estimateAmountOfService + estimateAmountOfRRCharge + estimateSerAmoCivil + estimateAmountOther + totalSerAmo;
    //     const totalService = document.getElementById('total_budget_service');
    //     if (totalService.value !== sumofservices.toString()) {
    //         totalService.value = sumofservices;
    //     }
    // }


    sumSolarCap_2();


    setInterval(function() {
        var currentValues = {};
        document.querySelectorAll('input[type="text"]').forEach(function(input) {
            currentValues[input.id] = input.value;
        });

        if (JSON.stringify(previousValues) !== JSON.stringify(currentValues)) {
            previousValues = currentValues;
            sumSolarCap_2();
        }
    }, 100);


    function setMinimumDates() {
        $(".after-add-more-third input[name='imp_from[]']").each(function() {
            $(this).attr("min", new Date().toISOString().split("T")[0]);
            const impToInput = $(this).parent().parent().next().find("input[name='imp_to[]']");
            const fromValue = $(this).val();
            if (fromValue) {
                impToInput.attr("min", fromValue);
            } else {
                impToInput.attr("min", new Date().toISOString().split("T")[0]);
            }
        });
    }
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
            CKEDITOR.replace('imp_plan');
            
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
                CKEDITOR.replace('imp_plan');
                // $('#imp_plan'+i+'').append('<body tabindex="0" aria-label="Rich Text Editor, imp_plan1" role="textbox" aria-multiline="true" contenteditable="true" aria-readonly="false" class="cke_editable cke_editable_themed cke_contents_ltr cke_show_borders" spellcheck="false"><p><br></p></body>')
                // Append the duplicated columns to the container
                 $("#duplicateContainer").append(clonedColumns);
               // var txted= '<textarea name="imp_plan[]"  id="imp_plan66" class="customtxteditor form-control" placeholder="Enter Implementation Plan Year Wise"> </textarea>';
               // $("#duplicateContainer").append("<span>welcome</span>");
               
              //  $("#duplicatContainer").append('');
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
                CKEDITOR.replace('imp_plan');
                // $('#imp_plan'+i+'').append('<body tabindex="0" aria-label="Rich Text Editor, imp_plan1" role="textbox" aria-multiline="true" contenteditable="true" aria-readonly="false" class="cke_editable cke_editable_themed cke_contents_ltr cke_show_borders" spellcheck="false"><p><br></p></body>')

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

<script src="{{ asset('admin/js/searchmaterial.js') }}"></script>
<script src="{{ asset('admin/js/nvmaterials.js') }}"></script>
<script src="{{ asset('theme/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>


<script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>

<script>
    $('#scheme_no').keyup(function() {
        var schemeno = $('#scheme_no').val();
        $.ajax({

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            url: '/admin/store_data',
            method: 'post',
            data: {
                'schemeno': schemeno
            },
            dataType: 'json',
            success: function(response) {
                var data = $('#scheme_des').val();
                $('#scheme_des').val(response.value);

                // document.getElementById("scheme_des").innerHTML + = response.value;
            }

        });
    });
</script>
<script>
    // var s_id = 1;
    function storeapi(a, b) {

        var id = a.id;
        var id2 = b.id;

        // console.log(id2);
        // console .log(id);

        var schemeno = $("#" + id).val();
        // console.log(schemeno);    

        $.ajax({

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            url: '/admin/store_data',
            method: 'post',
            data: {
                'schemeno': schemeno
            },
            dataType: 'json',
            success: function(response) {
                //  console.log(response.value);
                // s_id = s_id+1;
                console.log(id2);

                $("#" + id2).val(response.value);
                //  


            }

        });


    }
</script>
<script>
    $(document).ready(function(){

        var budgetavl = $('#budget_avl').val();
        budgetavl = budgetavl.replace(/[^a-zA-Z0-9_ ]/g, "")
        budgetavl = parseInt(budgetavl);
        var totalMatMat = parseInt($('#total_mat_mat').val()) || 0;
        var totalSerAmo = parseInt($('#total_ser_amo').val()) || 0;
        var estimateSerAmoCivil = $('#estimate_amount_of_service_civil').val() || 0;
        var estimateAmountOfService = $('#estimate_amount_of_service').val() || 0;
        var estimateAmountOfRRCharge = $('#estimate_amount_of_rr_charge').val() || 0;
        var estimateAmountOther = $('#estimate_amount_other').val() || 0;
        var material_amount = parseInt($('#mtamt').val());
        var service_amount = parseInt($('#serv_amt').val())
      
        var sum2 = totalSerAmo + service_amount  + parseInt(estimateSerAmoCivil) + parseInt(estimateAmountOfService) + parseInt(estimateAmountOfRRCharge) + parseInt(estimateAmountOther);
        
        // alert(sum2);
        var sum = totalMatMat + totalSerAmo + material_amount + service_amount + parseInt(estimateSerAmoCivil) + parseInt(estimateAmountOfService) + parseInt(estimateAmountOfRRCharge) + parseInt(estimateAmountOther);
          sum = sum.toLocaleString('en-IN');
        //  alert(estimateAmountOther);
         sum2 = sum2.toLocaleString('en-IN');
    
        // sum2 = sum2.toLocaleString('en-IN');
        var diff = budgetavl - totalMatMat;
        diff = diff.toLocaleString('en-IN');
        const budget =  document.getElementById('ser_budget_avl');
        if (budget.value !== diff.toString()) {
             var total13 = diff.toLocaleString('en-IN');
            $("#ser_budget_avl").val(total13);
        }
        totalMatMat = totalMatMat.toLocaleString('en-IN');
        totalSerAmo =totalSerAmo.toLocaleString('en-IN');
        const total_Amt  =  document.getElementById('total_mat_mat');
        if (total_Amt.value !== totalMatMat.toString()) {
             var total1 = totalMatMat.toLocaleString('en-IN');
            $("#total_mat_mat").val(total1);
        }
        const total_Amt1  =  document.getElementById('total_mat_mat');
        if (total_Amt1.value !== totalSerAmo.toString()) {
             var total12 = totalSerAmo.toLocaleString('en-IN');
            $("#total_ser_amo").val(total12);
        }
        const totalBudgetNV = document.getElementById('total_budget_both');
        const totalservice =  document.getElementById('total_budget_service');
        if (totalBudgetNV.value !== sum.toString()) {
             var total = sum.toLocaleString('en-IN');
            //  total = total.toLocaleString('en-IN');
            //  alert(total);
            $("#total_budget_both").val(total);
        }
        if (totalservice.value !== sum2.toString()) {
             var total44 = sum2.toLocaleString('en-IN');
            //   alert(total44);
           $('#total_budget_service').val(total44);
        }
        
    });
</script>
<script src="{{ asset('theme/plugins/ckeditor/ckeditor.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize CKEditor
        CKEDITOR.replace('just_Prop');
        CKEDITOR.replace('background');
    });
</script>
<script>
   
        function storedata(a, b) {
        
            var id = a.id;
            // alert(id);
        var id2 = b.id;
        var amount = parseInt($("#material_amount").val());
        // alert(amount);
        var total_amt = parseInt($("#total_mat_mat").val());
        // alert(total_amt);
        var amount1 = $("#" + id).val();
        var sum2 = parseInt($("#sum2").val());
      
        var description1 = $("#" + id2).val();
        nv_id = $('#nv_id').val();

            $.ajax({

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                url: '/admin/store_material',
                method: 'post',
                data: {
                   'nv_id':nv_id,
                    'amount': amount,
                    'amount1': amount1,
                    'sum2' : sum2,
                    'description1' : description1
                },
                dataType: 'json',
                success: function(response) {
                    var total_amount= response.total_amt;
                    total_amount = parseInt(total_amount);
                    var sum = response.sum;
                    $(".total_budget_both").val(total_amt);
                    var total_budget = $("#total_budget_both").val();
                    total_budget = total_budget.replace(/[^a-zA-Z0-9_ ]/g, "")
                    total_budget = parseInt(total_budget);
                    // alert(total_budget);
                // console.log(sum);
                if(amount != 0 && amount1 != 0)
                {
                    
                    var total = total_budget + total_amount;
                    total = total.toLocaleString('en-IN');
                   $("#total_budget_both").val(total);
                   $('#sum2').val(sum)
                    // $(".budget").ready(function(){
                    //         alert( document.getElementById("total_budget_both").innerHTML(total));
                    // });
                }
                  
             
                    
                 


                }

            });
        

            
      

        }
    
        
    </script>
    <script>
        function store_data(a, b) {

            var id = a.id;
            var id2 = b.id;
            var amount = parseInt($("#service_amount").val());
            var amount1 = $("#" + id).val();
            // alert(sum);
            var description1 = $("#" + id2).val();
            nv_id = $('#nv_id').val();
            var sum = parseInt($("#sum").val()); 
            // alert(sum);
            $.ajax({

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            url: '/admin/store_service',
            method: 'post',
            data: {
                'nv_id':nv_id,
                'amount': amount,
                'amount1': amount1,
                'sum' : sum,
                'description1' : description1
            },
                dataType: 'json',
                success: function(response) {
                console.log(response);
                var total_amt = response.total_amt;
                total_amt = parseInt(total_amt);
                var total1 = parseInt($('#service_amount').val());
                var total2 = $('#total_budget_service').val();
                total2 = total2.replace(/[^a-zA-Z0-9_ ]/g, "")
                total2 = parseInt(total2);
                var sum = response.sum;
                // $(".total_budget_both").val(total_amt);
                    var total_budget = $("#total_budget_both").val();
                    total_budget = total_budget.replace(/[^a-zA-Z0-9_ ]/g, "")
                    total_budget = parseInt(total_budget);
                    // alert(total_budget);
                // console.log(sum);
                if(amount != 0 && amount1 != 0)
                {
                    
                    var total = total_budget + total_amt;
                    var tot_service = total_amt +  total2;
                    // alert(tot_service);
                    total = total.toLocaleString('en-IN');
                    $("#total_budget_both").empty();
                   $("#total_budget_both").val(total);
                   $("#total_budget_service").val(tot_service)
                   $('#sum').val(sum);
                    // $(".budget").ready(function(){
                    //         alert( document.getElementById("total_budget_both").innerHTML(total));
                    // });
                }
                // console.log(sum);
                // $("#sum").val(sum);
                // $("#total_budget_both").val(total_amt); 


                }

            });

            }
    </script>
   <script>
    $(document).ready(function() {
        // Initialize CKEditor
        // CKEDITOR.replace('imp_plan');
        CKEDITOR.replace('background');
    });
</script>
<script>
    $(document).ready(function() {
        $("#material_amount").keyup(function(e){
        
            var amount = parseInt($("#material_amount").val());
            var total_budget = $("#total_budget_both").val();
            // alert(amount);
            total_budget = total_budget.replace(/[^a-zA-Z0-9_ ]/g, "");
            total_budget = parseInt(total_budget);
            // alert(total_budget);
            if(amount != 0)
            {
                var total = total_budget + amount;
                // alert(total);
                total = total.toLocaleString('en-IN');
                $("#total_budget_both").val(total);
                $("#mat_amt").val(amount);
                
            }
            else if(amount == 0)
            {
                var ser_amt = parseInt($('#mat_amt').val()||0);
                var tot_ser = parseInt(total_budget) - parseInt(ser_amt);
                tot_ser = tot_ser.toLocaleString('en-IN');
                $("#total_budget_service").val(total_service)
                $("#total_budget_both").val(tot_ser);
            }
                
        });
    });
</script>
<script>
    $(document).ready(function() {
        $("#service_amount").keyup(function(e){
        
            var amount = parseInt($("#service_amount").val() || 0);
            var total_budget = $("#total_budget_both").val();
            total_budget = total_budget.replace(/[^a-zA-Z0-9_ ]/g, "");
            total_budget = parseInt(total_budget);
            var total_service = $("#total_budget_service").val();
            total_service = total_service.replace(/[^a-zA-Z0-9_ ]/g, "");
            total_service = parseInt(total_service);
            if(amount != 0)
            {
                var total = total_budget + amount;
                total = total.toLocaleString('en-IN');
                var service = total_service + amount;
                service = service.toLocaleString('en-IN');
                // console.log('ttttt',total);
                // $("#total_budget_both").empty();
                $("#total_budget_service").val(service)
                $("#total_budget_both").val(total);
                $('#ser_amt').val(amount);
                
            }
        
            else if(amount == 0)
            {
                var ser_amt = parseInt($('#ser_amt').val()||0);
                var tot_ser = parseInt(total_budget) - parseInt(ser_amt);
                tot_ser = tot_ser.toLocaleString('en-IN');
                var tot1 = parseInt(total_service)- parseInt(ser_amt);
                tot1 = tot1.toLocaleString('en-IN');
                $("#total_budget_service").val(tot1)
                $("#total_budget_both").val(tot_ser);
            }
        
                
        });
    });
</script>
<script>
    $(document).ready(function() {
        $("#estimate_amount_of_service").keyup(function(e){
        
            var amount = parseInt($("#estimate_amount_of_service").val() || 0);
            var total_budget = $("#total_budget_both").val();
            total_budget = total_budget.replace(/[^a-zA-Z0-9_ ]/g, "");
            total_budget = parseInt(total_budget);
            var total_service = $("#total_budget_service").val();
            total_service = total_service.replace(/[^a-zA-Z0-9_ ]/g, "");
            total_service = parseInt(total_service);
            if(amount != 0)
            {
                var total = total_budget + amount;
                total = total.toLocaleString('en-IN');
                var service = total_service + amount;
                service = service.toLocaleString('en-IN');
                // console.log('ttttt',total);
                // $("#total_budget_both").empty();
                $("#total_budget_service").val(service)
                $("#total_budget_both").val(total);
                $("#est_amt").val(amount);
                
            }
            else if(amount == 0)
            {
                var ser_amt = parseInt($('#est_amt').val()||0);
                var tot_ser = parseInt(total_budget) - parseInt(ser_amt);
                tot_ser = tot_ser.toLocaleString('en-IN');
                // alert(tot_ser);
                var tot1 = parseInt(total_service)- parseInt(ser_amt);
                tot1 = tot1.toLocaleString('en-IN');
                $("#total_budget_service").val(tot1)
                $("#total_budget_both").val(tot_ser);
            }
                
        });
    });
</script>
<script>
    $(document).ready(function() {
        $("#estimate_amount_of_service_civil").keyup(function(e){
        
            var amount = parseInt($("#estimate_amount_of_service_civil").val() || 0);
            var total_budget = $("#total_budget_both").val();
            total_budget = total_budget.replace(/[^a-zA-Z0-9_ ]/g, "");
            total_budget = parseInt(total_budget);
            var total_service = $("#total_budget_service").val();
            total_service = total_service.replace(/[^a-zA-Z0-9_ ]/g, "");
            total_service = parseInt(total_service);
            if(amount != 0)
            {
                var total = total_budget + amount;
                total = total.toLocaleString('en-IN');
                var service = total_service + amount;
                service = service.toLocaleString('en-IN');
                // console.log('ttttt',total);
                // $("#total_budget_both").empty();
                $("#total_budget_service").val(service)
                $("#total_budget_both").val(total);
                $("#est_amt_ser_civil").val(amount);
                
            }
            else if(amount == 0)
            {
                var ser_amt = parseInt($('#est_amt_ser_civil').val()||0);
                var tot_ser = parseInt(total_budget) - parseInt(ser_amt);
                tot_ser = tot_ser.toLocaleString('en-IN');
                // alert(tot_ser);
                var tot1 = parseInt(total_service)- parseInt(ser_amt);
                tot1 = tot1.toLocaleString('en-IN');
                $("#total_budget_service").val(tot1)
                $("#total_budget_both").val(tot_ser);
            }
                
        });
    });
</script>
<script>
    $(document).ready(function() {
        $("#estimate_amount_of_rr_charge").keyup(function(e){
        
            var amount = parseInt($("#estimate_amount_of_rr_charge").val() || 0);
            var total_budget = $("#total_budget_both").val();
            total_budget = total_budget.replace(/[^a-zA-Z0-9_ ]/g, "");
            total_budget = parseInt(total_budget);
            var total_service = $("#total_budget_service").val();
            total_service = total_service.replace(/[^a-zA-Z0-9_ ]/g, "");
            total_service = parseInt(total_service);
            if(amount != 0)
            {
                var total = total_budget + amount;
                total = total.toLocaleString('en-IN');
                var service = total_service + amount;
                service = service.toLocaleString('en-IN');
                // console.log('ttttt',total);
                // $("#total_budget_both").empty();
                $("#total_budget_service").val(service)
                $("#total_budget_both").val(total);
                $('#est_amt_rr').val(amount);
                
            }
            else if(amount == 0)
            {
                var ser_amt = parseInt($('#est_amt_rr').val()||0);
                var tot_ser = parseInt(total_budget) - parseInt(ser_amt);
                tot_ser = tot_ser.toLocaleString('en-IN');
                // alert(tot_ser);
                var tot1 = parseInt(total_service)- parseInt(ser_amt);
                tot1 = tot1.toLocaleString('en-IN');
                $("#total_budget_service").val(tot1)
                $("#total_budget_both").val(tot_ser);
            }
                
        });
    });
</script>
<script>
    $(document).ready(function() {
        $("#estimate_amount_other").keyup(function(e){
        
            var amount = parseInt($("#estimate_amount_other").val() || 0);
            var total_budget = $("#total_budget_both").val();
            total_budget = total_budget.replace(/[^a-zA-Z0-9_ ]/g, "");
            total_budget = parseInt(total_budget);
            var total_service = $("#total_budget_service").val();
            total_service = total_service.replace(/[^a-zA-Z0-9_ ]/g, "");
            total_service = parseInt(total_service);
            if(amount != 0)
            {
                var total = total_budget + amount;
                total = total.toLocaleString('en-IN');
                var service = total_service + amount;
                service = service.toLocaleString('en-IN');
                // console.log('ttttt',total);
                // $("#total_budget_both").empty();
                $("#total_budget_service").val(service)
                $("#total_budget_both").val(total);
                $('#est_amt_oth').val(amount);
                
            }
            else if(amount == 0)
            {
                var ser_amt = parseInt($('#est_amt_oth').val()||0);
                var tot_ser = parseInt(total_budget) - parseInt(ser_amt);
                tot_ser = tot_ser.toLocaleString('en-IN');
                var tot1 = parseInt(total_service)- parseInt(ser_amt);
                tot1 = tot1.toLocaleString('en-IN');
                //  alert(tot1);
                $("#total_budget_service").val(tot1)
                $("#total_budget_both").val(tot_ser);
            }
                
        });
    });

</script>
<script>
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
@endpush
