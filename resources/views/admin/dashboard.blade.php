@extends('admin.layout.master', ['page_title' => 'Dashboard'])
@push('styles')
    <style>
        #approve_list_filter>label {
            float: right;
            position: relative;
            top: -10px;
        }

        .dataTables_info {
            float: left;
            margin-left: -20px;
            position: relative;
            right: -15px;

        }

        .paging_simple_numbers {
            float: right;
            margin-left: -30px;
        }
        

        .buttons {
            position: relative;
            bottom: -50px;
        }

        /* Hide modal backdrop */
        .modal-backdrop {
            display: none;
        }

        /* Close modal when clicking outside */
        .modal {
            pointer-events: none;
        }

        .card {
           box-shadow: 2px 2px 33px 5px rgb(0 0 0 / 25%) !important;
           }  

           div#approve_list_wrapper {
    margin-top: -50px;
}

.pagination {
    float: right;
}
/* button.btn.btn-danger.ml-1.mb-2.export-dashboard {
  position: absolute;
  left: 429px;
}

@media only screen and (min-width:670px) and (max-width:880px) {
  button.btn.btn-danger.ml-1.mb-2.export-dashboard {
    left: 327px;
  }
}

@media only screen and (min-width:461px) and (max-width:669px) {
  button.btn.btn-danger.ml-1.mb-2.export-dashboard {
    left: 224px;
  }
}

@media only screen and (max-width:460px) {
  button.btn.btn-danger.ml-1.mb-2.export-dashboard {
    left: 128px;
  }
} */
             
    </style>
@endpush

@section('content')
    {{-- Login modal start --}}
    <!-- Button trigger modal -->


    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#welcomepopup" style="display: none;">
        Launch demo modal
    </button>

    <!-- Modal -->

    @if (session()->has('welcome_message'))
        <div class="modal login-modal" id="welcomepopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-body text-center">
                        <img src="{{ asset('theme/dist/img/logo.png') }}" alt="BSES" class="brand-image img-circle2"
                            style="opacity: .8">

                        <h5>Welcome </h5>
                        <p>Welcome to the Need Validation application. We're delighted to have you join us!
                            Our application is designed to help you validate your needs effectively and efficiently. Before
                            we
                            get started, we want to assure you that your information security is our topmost priority. Our
                            system implements robust security measures to safeguard your information.

                            As you embark on the journey of validating your needs, we invite you to choose a signature that
                            reflects your unique style and preferences. This signature will be associated with your
                            validated
                            needs, making them easily recognizable. Take your time to select a signature that resonates with
                            you.

                            We are here to support you every step of the way. Get ready to validate your needs with
                            confidence
                            and clarity. </p>
                        <p class="text-center">Welcome aboard!</p>

                        <div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                {{-- <span aria-hidden="true">Close</span> --}}
                                Close
                            </button>
                        </div>




                    </div>

                </div>
            </div>
        </div>
    @endif
   
    {{-- Login modal end --}}
 
    <div class="content-header ">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                <form action="{{route('dashboard.home')}}" method="get">
                        <div class="row">
                      
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-1">
                                <label style="color: #495057;" style="display:inline-block;">Fiscal Year</span></label>
                                <select  class="form-control"  name="fiscal_year" style="display:inline-block !important; width:65%;">
                                    <!-- <option value="{{$currentFinancialYear}},{{$nextFinancialYear}},{{$nextToNextFinancialYear}}" {{ request('fiscal_year') == $currentFinancialYear,$nextFinancialYear,$nextToNextFinancialYear ? 'selected' : '' }}>All</option> -->
                                    <option value="{{$currentFinancialYear}}" {{ request('fiscal_year') == $currentFinancialYear ? 'selected' : '' }}>{{$currentFinancialYear}}</option>
                                    <option value="{{$nextFinancialYear}}" {{ request('fiscal_year') == $nextFinancialYear ? 'selected' : '' }}>{{$nextFinancialYear}}</option>
                                    <option value="{{$nextToNextFinancialYear}}" {{ request('fiscal_year') == $nextToNextFinancialYear ? 'selected' : '' }}>{{$nextToNextFinancialYear}}</option>
                                </select>
                                <button type="submit" class="btn btn-success mt-2">Search</button>
                            </div>
                          
                         </div>
                        </form>
                        <div class="row">
                            @if (Auth::user()->role_id == 1)

                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-1 ">
                                <label style="color: #495057;" style="display:inline-block;">Company</span></label>
                                <select class="form-control" id="company" name="company" style="display:inline-block !important; width:65%;">
                                    <option value="">Select Company</option>
                                    <option value="0">All</option>
                                    @foreach ($company as $value)
                                        <option value="{{ url('admin/dashboard') }}/{{ $value->id }}"
                                            {{ $value->id == $company_id ? 'selected' : '' }}>
                                            {{ $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                       
                        @endif
                        </div>

                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-1">
                       
                                <p>@if (session()->has('notification'))
                                <div class=" alert-{{ session('notification')['type'] }}" style="background-color: transparent; font-size:14px;">
                                    {!! session('notification')['message'] !!}
                                </div>
                                @endif</p>
                            </div>


                </div><!-- /.col -->
                <div class="col-sm-6">
           
                </div><!-- /.col -->
            </div><!-- /.row -->
         
           

        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


    <?php
    $TodDate = date('Y-m-d');
    $tommDate = date('Y-m-d', strtotime('+1 day', strtotime($TodDate)));
    ?>


    @if (\Auth::user()->isA('Admin'))
       
    @endif






    <section class="content">
        <div class="container-fluid">
     
            <div class="row">
                <div class="col-12">
                    <div class="card retAjax rounded-0 shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title"><b>Count of Need Validation Details </b></h3>
                        </div>
                        <div class="card-body">
                            <div class="row col-xsm-12">

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <a href="/admin/dashboard?total_processed=1">
                                        <div class="card text-center pt-3 pb-3 dashboad-icons">
                                            <span><i class="fas fa-copy"></i></span><br>
                                            <h3><b id="totalNV"> {{ $totalNV ?? '' }}</b></h3>
                                            <p>Total Processed NV</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <a href="/admin/dashboard?approved=1">
                                        <div class="card text-center pt-3 pb-3 dashboad-icons1 dashboad-icons">
                                            <span><i class="fas fa-copy"></i></span><br>
                                            <h3><b id="approvedNV">{{ $approvedNV ?? '' }}</b></h3>
                                            <p>Approved NV</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="clearfix hidden-md-up"></div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <a href="{{route('list_reject')}}">
                                        <div class="card text-center pt-3 pb-3 dashboad-icons2 dashboad-icons" id="reject_list">
                                            <span><i class="fas fa-copy"></i></span><br>
                                            <h3><b id="rejectedNV">{{ $rejectedNV ?? '' }}</b></h3>
                                            <p>Rejected NV</p>
                                        </div>
                                    </a>
                                    <!-- /.info-box -->
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <a href="/admin/dashboard?pending=0">
                                        <div class="card text-center pt-3 pb-3 dashboad-icons3 dashboad-icons">
                                            <span><i class="fas fa-copy"></i></span><br>
                                            <h3><b id="pendingNV"> {{ $pendingNV ?? '' }}</b></h3>
                                            <p>Pending NV</p>
                                        </div>
                                    </a>
                                    <!-- /.info-box -->
                                </div>

                                <div class="clearfix hidden-md-up"></div>

                            </div>
                        </div>

                        <link rel="stylesheet"
                            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

                    </div>

                    <div class="card retAjax rounded-0 shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title"><b>Amount of Need Validation </b></h3>
                        </div>

                        <div class="card-body">
                        <div class="row col-xsm-12">
                                <!-- Add class "amount-value" to each h3 element -->
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <div class="card text-center pt-3 pb-3 dashboad-icons">
                                        <span><i class="fas fa-copy"></i></span><br>
                                        <h3 class="amount-value" id="totalNVValue"><b id="totalAmount">{{ $totalAmount ?? '' }}</b></h3>
                                        <p>Total Processed NV Amount</p>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <div class="card text-center pt-3 pb-3 dashboad-icons1 dashboad-icons">
                                        <span><i class="fas fa-copy"></i></span><br>
                                        <h3 class="amount-value" id="approvedNVValue"><b>{{ $approvedAmount ?? '' }}</b></h3>
                                        <p>Approved NV Amount</p>
                                    </div>
                                </div>

                                <div class="clearfix hidden-md-up"></div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <div class="card text-center pt-3 pb-3 dashboad-icons2 dashboad-icons">
                                        <span><i class="fas fa-copy"></i></span><br>
                                        <h3 class="amount-value" id="rejectedNVValue"><b>{{ $rejectedAmount ?? '' }}</b></h3>
                                        <p>Rejected NV Amount</p>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <div class="card text-center pt-3 pb-3 dashboad-icons3 dashboad-icons">
                                        <span><i class="fas fa-copy"></i></span><br>
                                        <h3 class="amount-value" id="pendingNVValue"><b>{{ $pendingAmount ?? '' }}</b></h3>
                                        <p>Pending NV Amount</p>
                                    </div>
                                </div>

                                <div class="clearfix hidden-md-up"></div>
                            </div>

                        </div>
                        <link rel="stylesheet"
                            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

                    </div>


                    <div>
                        @php
                            $user = \Auth::user();
                            $employees = App\Models\Employee::where('user_id', $user->id)->first();
                        @endphp
                        <div class="card retAjax rounded-0 shadow-lg">
                            <div class="card-body">
                                <div class="row">
                                    @if ($company_id == 6)
                                        <div class="col-xl-6 col-lg-6 col-md-6 mb-2">
                                            <div class="card card-success">
                                                <div class="card-header">
                                                    <h3 class="card-title"><b>Monthly BRPL NV</b></h3>
                                                   
                                                </div>
                                                <div class="card-body">
                                                    <div class="chart">
                                                        <canvas id="barChartOne"
                                                            style="width:100%;max-width:600px"></canvas>
                                                    </div>
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 mb-2">
                    <div class="card">

                        <div class="card-header">
                        <h3 class="card-title"><b>BRPL NV Amount</b></h3>
                        </div>
   
                       <div class="card-body">
                    <div class="chart">
                    <canvas id="myChartOne" style="width:100%;max-width:600px"></canvas>
                    </div>
                    </div>
                    </div>
                    </div>
                                    @elseif ($company_id == 5)
                                        <div class="col-xl-6 col-lg-6 col-md-6 mb-2">

                                            <div class="card">

                                                <div class="card-header">

                                                    <h3 class="card-title"><b>Monthly BYPL NV</b></h3>

                                                </div>

                                                <div class="card-body">

                                                    <div class="chart">

                                                        <canvas id="barCharttwo"
                                                            style="width:100%;max-width:600px"></canvas>

                                                    </div>

                                                </div>

                                                <!-- /.card-body -->

                                            </div>

                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 mb-2">
                    <div class="card">

                        <div class="card-header">
                        <h3 class="card-title"><b>BYPL NV Amount</b></h3>
                        </div>
   
                       <div class="card-body">
                    <div class="chart">
                    <canvas id="myChartTwo" style="width:100%;max-width:600px"></canvas>
                    </div>
                    </div>
                                    @elseif(Auth::user()->role_id == 1)
                                        <div class="col-xl-6 col-lg-6 col-md-6 mb-2">
                                            <div class="card card-success">
                                                <div class="card-header">
                                                    <h3 class="card-title"><b>Monthly BRPL NV</b></h3>
                                                 
                                                </div>
                                                <div class="card-body">
                                                    <div class="chart">
                                                        <canvas id="barChartOne"
                                                            style="width:100%;max-width:600px"></canvas>
                                                    </div>
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 mb-2">
                    <div class="card">

                        <div class="card-header">
                        <h3 class="card-title"><b>BRPL NV Amount</b></h3>
                        </div>
   
                       <div class="card-body">
                    <div class="chart">
                    <canvas id="myChartOne" style="width:100%;max-width:600px"></canvas>
                    </div>
                    </div>
                    </div>
                    </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 mb-2">


                                            <div class="card">

                                                <div class="card-header">

                                                    <h3 class="card-title"><b>Monthly BYPL NV</b></h3>

                                                </div>

                                                <div class="card-body">

                                                    <div class="chart">

                                                        <canvas id="barCharttwo"
                                                            style="width:100%;max-width:600px"></canvas>

                                                    </div>

                                                </div>

                                                <!-- /.card-body -->

                                            </div>

                                        </div>
                     
             
                    <div class="col-xl-6 col-lg-6 col-md-6 mb-2">
                    <div class="card">

                        <div class="card-header">
                        <h3 class="card-title"><b>BYPL NV Amount</b></h3>
                        </div>
   
                       <div class="card-body">
                    <div class="chart">
                    <canvas id="myChartTwo" style="width:100%;max-width:600px"></canvas>
                    </div>
                    </div>
                                    @else
                                        @if ($employees->division_id == 6)
                                            <div class="col-xl-6 col-lg-6 col-md-6 mb-2">
                                                <div class="card card-success">
                                                    <div class="card-header">
                                                        <h3 class="card-title"><b>Monthly BRPL NV</b></h3>
                                                     
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="chart">
                                                            <canvas id="barChartOne"
                                                                style="width:100%;max-width:600px"></canvas>
                                                        </div>
                                                    </div>
                                                    <!-- /.card-body -->
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 mb-2">
                    <div class="card">

                        <div class="card-header">
                        <h3 class="card-title"><b>BRPL NV Amount</b></h3>
                        </div>
   
                       <div class="card-body">
                    <div class="chart">
                    <canvas id="myChartOne" style="width:100%;max-width:600px"></canvas>
                    </div>
                    </div>
                    </div>
                    </div>
                                        @elseif($employees->division_id == 5)
                                            <div class="col-xl-6 col-lg-6 col-md-6 mb-2">


                                                <div class="card">

                                                    <div class="card-header">

                                                        <h3 class="card-title"><b>Monthly BYPL NV</b></h3>




                                                    </div>

                                                    <div class="card-body">

                                                        <div class="chart">

                                                            <canvas id="barCharttwo"
                                                                style="width:100%;max-width:600px"></canvas>

                                                        </div>

                                                    </div>

                                                    <!-- /.card-body -->

                                                </div>

                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 mb-2">
                    <div class="card">

                        <div class="card-header">
                        <h3 class="card-title"><b>BYPL NV Amount</b></h3>
                        </div>
   
                       <div class="card-body">
                    <div class="chart">
                    <canvas id="myChartTwo" style="width:100%;max-width:600px"></canvas>
                    </div>
                    </div>
                                        @endif
                                    @endif

                                </div>
                            </div>

                        </div>
                    </div>

                    <div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @php
                 
                    $user = \Auth::user()->id;

                    $workflows = DB::table('capex_workflows_status')->get();
                    $showDiv = false;

                    if ($workflows->isNotEmpty()) {
                        foreach ($workflows as $workflow) {
                            $capexWorkflowUsers = $workflow->workflow_user_id;

                            if (!empty($capexWorkflowUsers) && $capexWorkflowUsers == Auth::user()->id) {
                                $showDiv = true;
                                break;
                            }
                        }
                    }
                @endphp
                <div class="col-12">
                    @if (Auth::user()->role_id == 1 || $showDiv)
                    
                        @if ($company_id == 6)
                       
                            <div class="card retAjax rounded-0 shadow-lg">
                                <div class="card-header">
                                    <h3 class="card-title "> <b>Need Validation Request</b> </h3>
                                </div>
                                <div class="card-body">
                                    @if(!empty($workflowBRPLStages))
                                        <div class="row col-xsm-12">
                                            @foreach ($workflowBRPLStages as $workflowStage)
                                                <div class="col-12 col-sm-6 col-md-3">
                                                    <div class="card text-center pt-3 pb-3 dashboad-icons{{ $loop->index + 1 }} dashboad-icons">
                                                        <span><i class="fas fa-list-alt"></i></span><br>
                                                            <h3><b id="ceonominee2Approval">{{ $capexBRPLApprovalCounts[$workflowStage->work_dep] ?? 0 }}</b></h3>
                                                            <p>{{ getPrefixDepartmentName($workflowStage->work_dep) }} <br> Approval Stage (CAPEX)</p>
                                                    </div>
                                                </div>
                                                <!-- /.col -->
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(!empty($opexWorkflowBRPLStages))
                                        <div class="row col-xsm-12">
                                            @foreach ($opexWorkflowBRPLStages as $opexWorkflowStage)
                                                <div class="col-12 col-sm-6 col-md-3">
                                                    <div class="card text-center pt-3 pb-3 dashboad-icons{{ $loop->index + 1 }} dashboad-icons">
                                                        <span><i class="fas fa-folder"></i></span><br>
                                                        <h3><b id="ceonominee2Approval">{{ $opexBRPLApprovalCounts[$opexWorkflowStage->work_dep] ?? 0 }}</b></h3>
                                                        <p>{{ getPrefixDepartmentName($opexWorkflowStage->work_dep) }} <br> Approval Stage (OPEX)</p>
                                                    </div>
                                                </div>
                                                <!-- /.col -->
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                </div>

                            </div>
                        @elseif ($company_id == 5)
                            <div class="card retAjax rounded-0 shadow-lg">
                                <div class="card-header">
                                    <h3 class="card-title "> <b>Need Validation Request</b> </h3>
                                </div>
                                <div class="card-body">

                                    @if(!empty($workflowBYPLStages))
                                        <div class="row col-xsm-12">
                                            @foreach ($workflowBYPLStages as $workflowStage)
                                                <div class="col-12 col-sm-6 col-md-3">
                                                    <div class="card text-center pt-3 pb-3 dashboad-icons{{ $loop->index + 1 }} dashboad-icons">
                                                        <span><i class="fas fa-list-alt"></i></span><br>
                                                            <h3><b id="ceonominee2Approval">{{ $capexBYPLApprovalCounts[$workflowStage->work_dep] ?? 0 }}</b></h3>
                                                            <p>{{ getPrefixDepartmentName($workflowStage->work_dep) }} <br> Approval Stage (CAPEX)</p>
                                                    </div>
                                                </div>
                                                <!-- /.col -->
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(!empty($opexWorkflowBYPLStages))
                                        <div class="row col-xsm-12">
                                            @foreach ($opexWorkflowBYPLStages as $opexWorkflowStage)
                                                <div class="col-12 col-sm-6 col-md-3">
                                                    <div class="card text-center pt-3 pb-3 dashboad-icons{{ $loop->index + 1 }} dashboad-icons">
                                                        <span><i class="fas fa-folder"></i></span><br>
                                                        <h3><b id="ceonominee2Approval">{{ $opexBYPLApprovalCounts[$opexWorkflowStage->work_dep] ?? 0 }}</b></h3>
                                                        <p>{{ getPrefixDepartmentName($opexWorkflowStage->work_dep) }} <br> Approval Stage (OPEX)</p>
                                                    </div>
                                                </div>
                                                <!-- /.col -->
                                            @endforeach
                                        </div>
                                    @endif


                                </div>

                            </div>
                        @else
                            <div class="card retAjax rounded-0 shadow-lg">
                                <div class="card-header">
                                    <h3 class="card-title "> <b>Need Validation Request</b> </h3>
                                </div>
                                <div class="card-body">

                                @if(!empty($workflowStages))
                                    <div class="row col-xsm-12">
                                        @foreach ($workflowStages as $workflowStage)
                                            <div class="col-12 col-sm-6 col-md-3">
                                                <div class="card text-center pt-3 pb-3 dashboad-icons{{ $loop->index + 1 }} dashboad-icons">
                                                    <span><i class="fas fa-list-alt"></i></span><br>
                                                        <h3><b id="ceonominee2Approval">{{ $capexApprovalCounts[$workflowStage->work_dep] ?? 0 }}</b></h3>
                                                        <p>{{ getPrefixDepartmentName($workflowStage->work_dep) }} <br> Approval Stage (CAPEX)</p>
                                                </div>
                                            </div>
                                            <!-- /.col -->
                                        @endforeach
                                    </div>
                                @endif

                                @if(!empty($opexWorkflowStages))
                                    <div class="row col-xsm-12">
                                        @foreach ($opexWorkflowStages as $opexWorkflowStage)
                                            <div class="col-12 col-sm-6 col-md-3">
                                                <div class="card text-center pt-3 pb-3 dashboad-icons{{ $loop->index + 1 }} dashboad-icons">
                                                    <span><i class="fas fa-folder"></i></span><br>
                                                    <h3><b id="ceonominee2Approval">{{ $opexApprovalCounts[$opexWorkflowStage->work_dep] ?? 0 }}</b></h3>
                                                    <p>{{ getPrefixDepartmentName($opexWorkflowStage->work_dep) }} <br> Approval Stage (OPEX)</p>
                                                </div>
                                            </div>
                                            <!-- /.col -->
                                        @endforeach
                                    </div>
                                @endif
                                    

                                </div>

                            </div>
                        @endif
                    @endif

                </div>

            </div>

            <div class="row">
                @if (Auth::user()->role_id == 1)
                    @if ($company_id == 6)
                        <div class="col-xl-6 col-lg-6 col-md-12 mb-2">
                            <div class="card retAjax rounded-0 shadow-lg">
                                <div class="card-header">
                                    <h5 class="card-title "><b>BRPL NV Approval Stages</b></h5>
                                </div>
                                <div class="card-body">
                                    <ul class="p-0 m-0">

                                        @if(!empty($workflowBRPLStages))
                                                    @foreach ($workflowBRPLStages as $workflowStage)
                                                    <li class="d-flex mb-4 pb-2">
                                                    <div class="avatar avatar-sm flex-shrink-0 me-3 mr-2">
                                                        <span class="avatar-initial rounded-circle bg-warning p-1"><i
                                                                class="fas fa-chart-line"></i></span>
                                                    </div>
                                                    <div class="d-flex flex-column w-100">

                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span>{{ getPrefixDepartmentName($workflowStage->work_dep) }} (CAPEX)</span>
                                                            <span class="text-muted">{{ $capexBRPLApprovalCounts[$workflowStage->work_dep] ?? 0 }}</span>
                                                        </div>

                                                        <div class="progress" style="height:6px;">
                                                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="80" aria-valuemin="0"
                                                                aria-valuemax="100"></div>
                                                        </div>

                                                    </div>
                                                </li>
                                                    @endforeach
                                        @endif

                                        @if(!empty($opexWorkflowBRPLStages))
                                                @foreach ($opexWorkflowBRPLStages as $opexWorkflowStage)

                                            <li class="d-flex mb-4 pb-2">
                                                <div class="avatar avatar-sm flex-shrink-0 me-3 mr-2">
                                                    <span class="avatar-initial rounded-circle bg-warning p-1"><i
                                                            class="fas fa-chart-line"></i></span>
                                                </div>
                                                <div class="d-flex flex-column w-100">

                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>{{ getPrefixDepartmentName($opexWorkflowStage->work_dep) }} (OPEX)</span>
                                                        <span class="text-muted">{{ $opexBRPLApprovalCounts[$opexWorkflowStage->work_dep] ?? 0 }}</span>
                                                    </div>

                                                    <div class="progress" style="height:6px;">
                                                        <div class="progress-bar bg-warning"
                                                            role="progressbar" aria-valuenow="80" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>

                                                </div>
                                            </li>
                                                    <!-- /.col -->
                                                @endforeach
                                        @endif
                                      
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @elseif ($company_id == 5)
                        <div class="col-xl-6 col-lg-6 col-md-12 mb-2">
                            <div class="card retAjax rounded-0 shadow-lg">
                                <div class="card-header">
                                    <h5 class="card-title "><b>BYPL NV Approval Stages</b></h5>
                                </div>
                                <div class="card-body">
                                    <ul class="p-0 m-0">
                                        @if(!empty($workflowBYPLStages))
                                                    @foreach ($workflowBYPLStages as $workflowStage)
                                                    <li class="d-flex mb-4 pb-2">
                                                    <div class="avatar avatar-sm flex-shrink-0 me-3 mr-2">
                                                        <span class="avatar-initial rounded-circle bg-warning p-1"><i
                                                                class="fas fa-chart-line"></i></span>
                                                    </div>
                                                    <div class="d-flex flex-column w-100">

                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span>{{ getPrefixDepartmentName($workflowStage->work_dep) }} (CAPEX)</span>
                                                            <span class="text-muted">{{ $capexBYPLApprovalCounts[$workflowStage->work_dep] ?? 0 }}</span>
                                                        </div>

                                                        <div class="progress" style="height:6px;">
                                                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="80" aria-valuemin="0"
                                                                aria-valuemax="100"></div>
                                                        </div>

                                                    </div>
                                                </li>
                                                    @endforeach
                                        @endif

                                        @if(!empty($opexWorkflowBYPLStages))
                                                @foreach ($opexWorkflowBYPLStages as $opexWorkflowStage)

                                            <li class="d-flex mb-4 pb-2">
                                                <div class="avatar avatar-sm flex-shrink-0 me-3 mr-2">
                                                    <span class="avatar-initial rounded-circle bg-warning p-1"><i
                                                            class="fas fa-chart-line"></i></span>
                                                </div>
                                                <div class="d-flex flex-column w-100">

                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>{{ getPrefixDepartmentName($opexWorkflowStage->work_dep) }} (OPEX)</span>
                                                        <span class="text-muted">{{ $opexBYPLApprovalCounts[$opexWorkflowStage->work_dep] ?? 0 }}</span>
                                                    </div>

                                                    <div class="progress" style="height:6px;">
                                                        <div class="progress-bar bg-warning"
                                                            role="progressbar" aria-valuenow="80" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>

                                                </div>
                                            </li>
                                                    <!-- /.col -->
                                                @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-xl-6 col-lg-6 col-md-12 mb-2">
                            <div class="card retAjax rounded-0 shadow-lg">
                                <div class="card-header">
                                    <h5 class="card-title "><b>BRPL NV Approval Stages</b></h5>
                                </div>
                                <div class="card-body">
                                    <ul class="p-0 m-0">
                                        
                                        @if(!empty($workflowBRPLStages))
                                                    @foreach ($workflowBRPLStages as $workflowStage)
                                                    <li class="d-flex mb-4 pb-2">
                                                    <div class="avatar avatar-sm flex-shrink-0 me-3 mr-2">
                                                        <span class="avatar-initial rounded-circle bg-warning p-1"><i
                                                                class="fas fa-chart-line"></i></span>
                                                    </div>
                                                    <div class="d-flex flex-column w-100">

                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span>{{ getPrefixDepartmentName($workflowStage->work_dep) }} (CAPEX)</span>
                                                            <span class="text-muted">{{ $capexBRPLApprovalCounts[$workflowStage->work_dep] ?? 0 }}</span>
                                                        </div>

                                                        <div class="progress" style="height:6px;">
                                                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="80" aria-valuemin="0"
                                                                aria-valuemax="100"></div>
                                                        </div>

                                                    </div>
                                                </li>
                                                    @endforeach
                                        @endif

                                        @if(!empty($opexWorkflowBRPLStages))
                                                @foreach ($opexWorkflowBRPLStages as $opexWorkflowStage)

                                            <li class="d-flex mb-4 pb-2">
                                                <div class="avatar avatar-sm flex-shrink-0 me-3 mr-2">
                                                    <span class="avatar-initial rounded-circle bg-warning p-1"><i
                                                            class="fas fa-chart-line"></i></span>
                                                </div>
                                                <div class="d-flex flex-column w-100">

                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>{{ getPrefixDepartmentName($opexWorkflowStage->work_dep) }} (OPEX)</span>
                                                        <span class="text-muted">{{ $opexBRPLApprovalCounts[$opexWorkflowStage->work_dep] ?? 0 }}</span>
                                                    </div>

                                                    <div class="progress" style="height:6px;">
                                                        <div class="progress-bar bg-warning"
                                                            role="progressbar" aria-valuenow="80" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>

                                                </div>
                                            </li>
                                                    <!-- /.col -->
                                                @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-12 mb-2">
                            <div class="card retAjax rounded-0 shadow-lg">
                                <div class="card-header">
                                    <h5 class="card-title "><b>BYPL NV Approval Stages</b></h5>
                                </div>
                                <div class="card-body">
                                    <ul class="p-0 m-0">
                                        @if(!empty($workflowBYPLStages))
                                                    @foreach ($workflowBYPLStages as $workflowStage)
                                                    <li class="d-flex mb-4 pb-2">
                                                    <div class="avatar avatar-sm flex-shrink-0 me-3 mr-2">
                                                        <span class="avatar-initial rounded-circle bg-warning p-1"><i
                                                                class="fas fa-chart-line"></i></span>
                                                    </div>
                                                    <div class="d-flex flex-column w-100">

                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span>{{ getPrefixDepartmentName($workflowStage->work_dep) }} (CAPEX)</span>
                                                            <span class="text-muted">{{ $capexBYPLApprovalCounts[$workflowStage->work_dep] ?? 0 }}</span>
                                                        </div>

                                                        <div class="progress" style="height:6px;">
                                                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="80" aria-valuemin="0"
                                                                aria-valuemax="100"></div>
                                                        </div>

                                                    </div>
                                                </li>
                                                    @endforeach
                                        @endif

                                        @if(!empty($opexWorkflowBYPLStages))
                                                @foreach ($opexWorkflowBYPLStages as $opexWorkflowStage)

                                            <li class="d-flex mb-4 pb-2">
                                                <div class="avatar avatar-sm flex-shrink-0 me-3 mr-2">
                                                    <span class="avatar-initial rounded-circle bg-warning p-1"><i
                                                            class="fas fa-chart-line"></i></span>
                                                </div>
                                                <div class="d-flex flex-column w-100">

                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>{{ getPrefixDepartmentName($opexWorkflowStage->work_dep) }} (OPEX)</span>
                                                        <span class="text-muted">{{ $opexBYPLApprovalCounts[$opexWorkflowStage->work_dep] ?? 0 }}</span>
                                                    </div>

                                                    <div class="progress" style="height:6px;">
                                                        <div class="progress-bar bg-warning"
                                                            role="progressbar" aria-valuenow="80" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>

                                                </div>
                                            </li>
                                                    <!-- /.col -->
                                                @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>


    </section>

    @if (Auth::user()->role_id == 1)
        <div class="card retAjax rounded-0 shadow-lg">
            <div class="card-header">
                <h3 class="card-title"> <b>Need Validation Information By Department</b> </h3>
            </div>
            <div class="card-body">

                <div class="row col-xsm-12">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="card text-center pt-3 pb-3 dashboad-icons4 dashboad-icons">
                            <span><i class="fas fa-folder"></i></span><br>
                            <h3><b> {{ $dpnv ?? '' }}</b></h3>
                            <p>Department <br> Wise NV </p>
                        </div>
                    </div>
                   
                    <div class="col-12 col-sm-6 col-md-3" style="visibility: hidden;">
                        <div class="card text-center pt-3 pb-3 dashboad-icons4 dashboad-icons">
                            <span><i class="fas fa-folder"></i></span><br>
                            <h3><b> {{ $dpnv ?? '' }}</b></h3>
                            <p>Department <br> Wise NV </p>
                        </div>
                    </div>
                   
                </div>

                <div class="row col-xsm-12 load_div">
                    @foreach($dept_data_p as $department)
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="card text-center pt-3 pb-3 dashboad-icons{{ $loop->index + 1 }} dashboad-icons">
                                <span><i class="fas fa-folder"></i></span><br>
                                <h3 id="data"><b>{{ $department->name }}</b></h3>
                                <p id="data_count">{{ $departmentNVCounts[$department->name] ?? 0 }}</p>
                            </div>
                        </div>
                        <!-- /.col -->
                    @endforeach
                </div>
                
                {{ $dept_data_p->links() }}
                {{-- code here --}}

            </div>


        </div>
    @endif
    </div>
    {{-- Table --}}
<!-- Include jQuery -->
      <br>
    <div class="container-fluid dashboard-btn">
       
        <div class="row">
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-1">
              {{-- Pending --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'pending' => 0,
                    'total_processed' => null,
                    'approved' => null,
                    'rejected' => null,
                    'total' => null
                ]) }}">
                    <button class="btn btn-danger mb-2 btn_dis" id="pending">Pending</button>
                </a>

                {{-- Processed --}}
                @if (Auth::user()->role_id == 11)
                <a href="{{ request()->fullUrlWithQuery([
                    'total_processed' => 1,
                    'pending' => null,
                    'approved' => null,
                    'rejected' => null,
                    'total' => null
                ]) }}">
                    <button class="btn total mb-2 btn_dis" id="current_approve">Processed</button>
                </a>
                @endif

                {{-- Approved --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'approved' => 1,
                    'pending' => null,
                    'total_processed' => null,
                    'rejected' => null,
                    'total' => null
                ]) }}">
                    <button class="btn btn-primary mb-2 btn_dis" id="approve">Approved</button>
                </a>

                {{-- Rejected --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'rejected' => 2,
                    'pending' => null,
                    'total_processed' => null,
                    'approved' => null,
                    'total' => null
                ]) }}">
                    <button class="btn mb-2 reject btn_dis" id="reject">Rejected</button>
                </a>

                {{-- Total --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'total' => 3,
                    'pending' => null,
                    'total_processed' => null,
                    'approved' => null,
                    'rejected' => null
                ]) }}">
                    <button class="btn total mb-2 btn_dis">Total</button>
                </a>

                <a href="javascript:void" id="export_btn"><button class="btn btn-danger mb-2 btn_dis" style="background-color: #1BC5BD">Export</button>
                </a>

            </div>
            @php
            $fiscal_year = request('fiscal_year');
            @endphp
            <form action={{ url('admin/dashboard/download-pdf') }} method="Post" target="_blank" >

                @csrf

                <div>
                    <input type="hidden" name="fiscal_year" value="{{ $fiscal_year }}">
                    <input type="hidden" name="company_id" value="{{ $company_id }}">
                    <a href="javascript:void" id="export_pdf"><button class="btn btn-danger mb-2 btn_dis" style="background-color: #1BC5BD"> Pdf </button>
                   </a>

                </div>

            </form>

            <form action={{ url('admin/dashboard/download-excel') }} method="Post" target="_blank">

                @csrf

                <div>
                    <input type="hidden" name="fiscal_year" value="{{ $fiscal_year }}">
                    <input type="hidden" name="company_id" value="{{ $company_id }}">
                    <a href="javascript:void" id="export_excel"><button class="btn  btn-danger ml-1 mb-2 btn_dis" style="background-color: #1BC5BD"> Excel </button>
                   </a>
                </div>

            </form>
         
           


        </div>
    </div>
    <table id="approve_list" class="table shadow-lg table-responsive dashboard-table">
        <thead >
            <tr>
                <th class="text-center" >Sr. No</th>
                <th class="text-center" >Proposal Number</th>
                <th class="text-center" >NV Budget Type</th>
                <th class="text-center" >Sub-Department</th>
                <th class="text-center" >NV Type</th>
                <th class="text-center" >Initiated By & Date </th>
                <th class="text-center" >HOD </th>
                <th class="text-center" >Group Head</th>
               @php
                  $workflowStages = App\Models\Workflow::where('status',1)->get();
                  $opexWorkflowStages = App\Models\OpexWorkflow::where('status',1)->get();
                @endphp
                @if(!empty($workflowStages))
                @foreach ($workflowStages as $workflowStage)
                    <th>{{ getPrefixDepartmentName($workflowStage->work_dep) }}<br>(CAPEX)</th>
                @endforeach
                @endif
                @if(!empty($opexWorkflowStages))
                @foreach ($opexWorkflowStages as $opexWorkflowStage)
                    <th>{{ getPrefixDepartmentName($opexWorkflowStage->work_dep) }}<br>(OPEX)</th>
                @endforeach
                @endif
            </tr>
        </thead>
        <tbody id="dash_table">
       
            <?php $i = 1;
            $no = 101;
           
            ?>
           {{-- @dd(!empty($nv_sm_data) && count($nv_sm_data) >= 1) --}}
            @if (!empty($nv_sm_data) )
           
            @php
            $sno = count($nv_sm_data);
            $sequenceNumber = $sno;
            
           
            @endphp
                   
                    @foreach ($nv_sm_data as $nv_list)

                        @php
                            
                            $proposal_no = getProposalNumber($nv_list->nv_id);
                            $status_obj = getAllStatus($nv_list->nv_id);
                            $inc_date = new DateTime($nv_list->created_at);

                            if($nv_list->hod_timestamp != null){
                                $hod_app = new DateTime($nv_list->hod_timestamp);
                                $hodinterval = $hod_app->diff($inc_date);
                                $days = $hodinterval->days;
                            }else{
                                $days = 0;
                            }
                            if($nv_list->nv_stage_timestamp != null){
                            $ces_app = new DateTime($nv_list->nv_stage_timestamp);
                            $cesinterval = $ces_app->diff($inc_date);
                            $cesdays = $cesinterval->days;
                            }else{
                                $cesdays = 0;
                            }
                            if($nv_list->groupcio_timestamp != null){
                            $groupcio_app = new DateTime($nv_list->groupcio_timestamp);
                            $groupciointerval = $groupcio_app->diff($inc_date);
                            $groupheaddays = $groupciointerval->days;
                            }else{
                            $groupheaddays = 0;
                            }

                            $nv_id[] = $nv_list->nv_id;
                           
                            foreach ($nv_id as $key => $value) {
                                $count = count(array_keys($nv_id, $value)); // Count the occurrences of the value
                           
                                if ($count > 1) {
                                    for ($i = 0; $i <= $count; $i++) {
                                        $result = $value . '-v' . $i;
                                    }
                                } else {
                                    $result = $value;
                                }
                            }
                           
                             @endphp
                             
                             @php
                                $nvid = $nv_list->nv_id;
                                $count = \App\Models\Nvsericestatus::where('nv_id', $nvid)->count();
                                $currentOccurrence = \App\Models\Nvsericestatus::where('nv_id', $nvid)->where('created_at', '<=', $nv_list->created_at)->count();
                            @endphp

                            @if ($currentOccurrence > 1)
                                @for ($i = 1; $i <= $count; $i++) 
                                    @php
                                        $result = $nvid . '-v' . $currentOccurrence; 
                                    @endphp
                                @endfor
                            @else
                                @php
                                    $result = $nvid;
                                @endphp
                            @endif
                            
                            <tr style="margin-bottom:30px; !important">
                                @php 
                                $service= getServiceName($nv_list->nv_id);
                                $NeedValidation = App\Models\NeedValidation::select("id", "service_id")->find($nv_list->nv_id);
                                $name_service= $NeedValidation->service_id == 1 ? 'material' : 'service';
                                @endphp
                                <?php
                                if ($nv_list->service_id == null){
                                    $service_id = $nv_list->material_id;
                                }else{
                                    $service_id = $nv_list->service_id;
                                }

                                $url = '/admin/nv_' . lcfirst($name_service) . '/create/' . $nv_list->nv_id . '/' . $nv_list->company_id . '/' .$service_id;
                                $i = $key + 1;
                                $url_app = '/admin/nv_' . lcfirst($name_service) . '/preview/' . $nv_list->nv_id. '/' .$service_id;
                                ?>
                                <td>{{$sequenceNumber}}</td>
                              <td>
                                    @php
                                    // ---------------------------------------------------
                                    // 1. Logic: Determine Background Color
                                    // ---------------------------------------------------
                                    $bgColor = "rgb(255, 191, 0)"; // default yellow

                                    if($nv_list->is_reject == 1){
                                        $bgColor = "rgb(245, 24, 8)"; // red
                                    }
                                    elseif($nv_list->ceo_status == 1){
                                        $bgColor = "rgb(17, 82, 15)"; // green
                                    }

                                    // ---------------------------------------------------
                                    // 2. Logic: Prepare Department Name (Safe Check)
                                    // ---------------------------------------------------
                                    $deptName = 'N/A';
                                    if ($nv_list->service_id == null && isset($nv_list->material)) {
                                        $deptName = getDepartmentName($nv_list->material->dept_id);
                                    } elseif (isset($nv_list->service)) {
                                        $deptName = getDepartmentName($nv_list->service->dept_id);
                                    }

                                    // ---------------------------------------------------
                                    // 3. Logic: Generate Button Text (Identifier)
                                    // ---------------------------------------------------
                                    $buttonText = "NV/{$proposal_no->budget_type}/FY {$proposal_no->fiscal_year}/{$deptName}/" . getServiceName($nv_list->nv_id) . " / {$result}";
                                    
                                    // ---------------------------------------------------
                                    // 4. Logic: Fetch Proposal Name & Total Amount
                                    // ---------------------------------------------------
                                    $proposal_name = 'N/A';
                                    $total_amount_val = 0; // Default value

                                    if ($nv_list->service_id != null && isset($nv_list->service)) {
                                        // Fetch from Service Table
                                        $proposal_name = $nv_list->service->proposal_name ?? 'N/A';
                                        
                                        // Get Amount from Service
                                        $total_amount_val = $nv_list->service->total_buget ?? 0;
                                    } 
                                    elseif ($nv_list->service_id == null && isset($nv_list->material)) {
                                        // Fetch from Material Table
                                        $proposal_name = $nv_list->material->proposal_name ?? 'N/A';
                                        
                                        // Get Amount from Material
                                        $total_amount_val = $nv_list->material->total_budget_both ?? 0;
                                    }

                                    // ---------------------------------------------------
                                    // 5. Logic: Build Hover Content
                                    // ---------------------------------------------------
                                    $hoverContent  = "<b>Proposal Name:</b><br>" . $proposal_name . "<br><br>";
                                    $hoverContent .= "<b>Identifier:</b><br>" . $buttonText . "<br><br>";
                                    
                                    // --- TOTAL AMOUNT FIELD ADDED HERE ---
                                    $hoverContent .= "<b>Total Amount:</b><br>" . indian_number_format($total_amount_val, 2) . "<br><br>";
                                    
                                    $hoverContent .= "<b>Broad Justification:</b><br>";
                                    
                                    if ($nv_list->service_id != null && isset($nv_list->service)) {
                                        $hoverContent .= $nv_list->service->broad_just ?? 'No justification available';
                                    } elseif ($nv_list->service_id == null && isset($nv_list->material)) {
                                        $hoverContent .= $nv_list->material->broad_just ?? $nv_list->material->justification ?? 'No justification available';
                                    } else {
                                        $hoverContent .= 'No justification available';
                                    }

                                    // ---------------------------------------------------
                                    // 6. FIX: Escape Double Quotes
                                    // ---------------------------------------------------
                                    $hoverContent = str_replace('"', '&quot;', $hoverContent);
                                    @endphp

                                    {{-- ---------------------------------------------------
                                    BUTTON FOR ROLE 1 (Hover Enabled)
                                    --------------------------------------------------- --}}
                                    @if(Auth::user()->role_id == 1)
                                        <button 
                                            type="button"
                                            class="btn_dis"
                                            data-toggle="popover"
                                            data-placement="right"
                                            data-trigger="hover"
                                            data-html="true"
                                            title="Proposal Details"
                                            data-content="{!! $hoverContent !!}"
                                            style="background-color:{{$bgColor}}; color:white; border:none; border-radius:5px; font-size:0.75rem; cursor:pointer;">
                                            {{ $buttonText }}
                                        </button>
                                    @endif

                                    {{-- ---------------------------------------------------
                                    BUTTON FOR ROLE 9 & 11 (Link + Hover Enabled)
                                    --------------------------------------------------- --}}
                                    @if(Auth::user()->role_id == 9 || Auth::user()->role_id == 11)
                                        @php 
                                            $targetUrl = ($nv_list->draft == 0) ? $url : $url_app; 
                                        @endphp

                                        <a href="{{ $targetUrl }}" 
                                        class="btn_dis"
                                        data-toggle="popover"
                                        data-placement="right"
                                        data-trigger="hover"
                                        data-html="true"
                                        title="Proposal Details"
                                        data-content="{!! $hoverContent !!}"
                                        style="background-color:{{$bgColor}}; color:white; border:none; border-radius:5px; font-size:0.75rem; text-decoration:none; display:inline-block;">
                                            {{ $buttonText }}
                                        </a>
                                    @endif
                                </td>

                                <td>{{$proposal_no->budget_type}}</td>
                                <td>
                                    @if ($nv_list->service_id == null)
                                        {{ getDepartmentName($nv_list->material->dept_id) }}
                                    @else
                                        {{ getDepartmentName($nv_list->service->dept_id) }}
                                    @endif
                                </td>
                                <td>
                                    @if ($nv_list->service_id == null && $nv_list->material->ser_rel_nv == 2)
                                        {{ getServiceName($nv_list->nv_id) }}+Service
                                    @else
                                        {{ getServiceName($nv_list->nv_id) }}
                                    @endif
                                </td>

                                @if ($nv_list->service_id == null)
                                    <td> {{ getUserName($nv_list->material->user_id) }}<br>
                                        <span>{{ date('d-M-y', strtotime($nv_list->created_at)) }} <br>
                                            {{ date('h:i A', strtotime($nv_list->created_at)) }}
                                        </span>
                                    </td>
                                @else
                                    <td> {{ getUserName($nv_list->service->user_id) }}<br>
                                        <span>{{ date('d-M-y', strtotime($nv_list->created_at)) }} <br>
                                            {{ date('h:i A', strtotime($nv_list->created_at)) }}
                                        </span>
                                    </td>
                                @endif


                                @if ($nv_list->service_id == null)
                                    <td>
                                
                                        @if ($nv_list->hod_status == 0)
                                            <b> </b><br>
                                            @if (!empty($nv_list->hod_timestamp))
                                                {{ date('d-M-y', strtotime($nv_list->hod_timestamp)) }}
                                                <br> {{ date('h:i A', strtotime($nv_list->hod_timestamp)) }}
                                            @endif
                                        @elseif($nv_list->hod_status == 1)
                                        {{ getHodName($nv_list->hod_id ?? '') }}
                                            <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i> </b><br>
                                                <a class="hod-approve-modal"
                                                    style="border:none; background-color:none; color:black;" rel="dialog"
                                                    data-toggle="modal"
                                                    data-hod-approve-date="<?= date('d-M-y', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-approve-time="<?= date('h:i A', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-approve-ip="<?= $nv_list->hod_action_ip ?>"
                                                    data-hod-approve-days="<?= $days ?>"href="#HODapproveModal"
                                                    data-target="#HODapproveModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="HODapproveModal" tabindex="-1" role="dialog"
                                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style="color:green;">Approval Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Approval Date </b></span> : <span
                                                                    id='hod-approve-date'></span><br>
                                                                <span><b>Approval Time </b></span> : <span
                                                                    id='hod-approve-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='hod-approve-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span id='hod-approve-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </span>
                                        @elseif($nv_list->hod_status == 2)
                                        {{ getHodName($nv_list->hod_id ?? '') }}
                                            <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>

                                                <a class="hod-reject-modal "
                                                    style="border:none; background-color:none; color:black;" rel="dialog"
                                                    data-toggle="modal"
                                                    data-hod-reject-date="<?= date('d-M-y', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-reject-time="<?= date('h:i A', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-reject-ip="<?= $nv_list->hod_action_ip ?>"
                                                    data-hod-reject-days="<?= $days ?>"href="#HODrejectModal"
                                                    data-target="#HODrejectModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="HODrejectModal" tabindex="-1" role="dialog"
                                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style="color:red">
                                                                    Rejection Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Rejection Date </b></span> : <span
                                                                    id='hod-reject-date'></span><br>
                                                                <span><b>Rejection Time </b></span> : <span
                                                                    id='hod-reject-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='hod-reject-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span id='hod-reject-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </span>
                                        @endif
                                    </td>
                                @else
                                    <td>

                                        @if ($nv_list->hod_status == 0)
                                    
                                            <b> </b><br>
                                            @if (!empty($nv_list->hod_timestamp))
                                                {{ date('d-M-y', strtotime($nv_list->hod_timestamp)) }}
                                                <br> {{ date('h:i A', strtotime($nv_list->hod_timestamp)) }}
                                            @endif
                                        @elseif($nv_list->hod_status == 1)
                                        {{ getHodName($nv_list->hod_id ?? '') }}
                                            <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i> </b><br>
                                                <a class="hod-approve-modal"
                                                    style="border:none; background-color:none; color:black;" rel="dialog"
                                                    data-toggle="modal"
                                                    data-hod-approve-date="<?= date('d-M-y', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-approve-time="<?= date('h:i A', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-approve-ip="<?= $nv_list->hod_action_ip ?>"
                                                    data-hod-approve-days="<?= $days ?>"href="#HODapproveModal"
                                                    data-target="#HODapproveModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="HODapproveModal" tabindex="-1"
                                                    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style="color:green;">Approval Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Approval Date </b></span> : <span
                                                                    id='hod-approve-date'></span><br>
                                                                <span><b>Approval Time </b></span> : <span
                                                                    id='hod-approve-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='hod-approve-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span
                                                                    id='hod-approve-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </span>
                                        @elseif($nv_list->hod_status == 2)
                                        {{ getHodName($nv_list->hod_id ?? '') }}
                                            <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>

                                                <a class="hod-reject-modal "
                                                    style="border:none; background-color:none; color:black;" rel="dialog"
                                                    data-toggle="modal"
                                                    data-hod-reject-date="<?= date('d-M-y', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-reject-time="<?= date('h:i A', strtotime($nv_list->hod_timestamp)) ?>"
                                                    data-hod-reject-ip="<?= $nv_list->hod_action_ip ?>"
                                                    data-hod-reject-days="<?= $days ?>"href="#HODrejectModal"
                                                    data-target="#HODrejectModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="HODrejectModal" tabindex="-1"
                                                    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"
                                                                    style="color:red">
                                                                    Rejection Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Rejection Date </b></span> : <span
                                                                    id='hod-reject-date'></span><br>
                                                                <span><b>Rejection Time </b></span> : <span
                                                                    id='hod-reject-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='hod-reject-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span id='hod-reject-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </span>
                                        @endif
                                    </td>
                                @endif

                                @if ($nv_list->service_id == null)
                                    <td>
                                        @if ($nv_list->groupcio_status == 0)
                                       {{--  @if($proposal_no->budget_type == 'CAPEX' && ($nv_list->ces_status == 1 || $nv_list->ces_status == 2))
                                        <span>N/A</span>
                                        @elseif($proposal_no->budget_type == 'OPEX' && ($nv_list->ceo_nominee_status == 1 || $nv_list->ceo_nominee_status == 2))
                                        <span>N/A</span>
                                        @endif --}}
                                        <span><br>
                                            @if (!empty($nv_list->groupcio_timestamp))
                                                    {{ date('d-M-y', strtotime($nv_list->groupcio_timestamp)) }}
                                                    <br> {{ date('h:i A', strtotime($nv_list->groupcio_timestamp)) }}
                                                    </span>
                                            @endif
                                        @elseif($nv_list->groupcio_status == 1)
                                            {{ getGroupHeadName($nv_list->groupcio_id ?? '') }}
                                            <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i></b><br>
                                                <a class="grouphead-approve-modal " style="border:none; background-color:none; color:black;"
                                                    rel="dialog" data-toggle="modal"
                                                    data-grouphead-approve-date="<?= date('d-M-y', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-approve-time="<?= date('h:i A', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-approve-ip="<?= $nv_list->groupcio_action_ip ?>"
                                                    data-grouphead-approve-days="<?= $groupheaddays ?>"href="#GroupHeadapproveModal"
                                                    data-target="#GroupHeadapproveModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="GroupHeadapproveModal" tabindex="-1" role="dialog"
                                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel" style="color:green;">Approval
                                                                    Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Approval Date </b></span> : <span id='grouphead-approve-date'></span><br>
                                                                <span><b>Approval Time </b></span> : <span id='grouphead-approve-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='grouphead-approve-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span id='grouphead-approve-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </span>
                                        @elseif($nv_list->groupcio_status == 2)
                                            {{ getGroupHeadName($nv_list->groupcio_id ?? '') }}
                                            <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>
                                                <a class="grouphead-reject-modal " style="border:none; background-color:none; color:black;"
                                                    rel="dialog" data-toggle="modal"
                                                    data-grouphead-reject-date="<?= date('d-M-y', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-reject-time="<?= date('h:i A', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-reject-ip="<?= $nv_list->groupcio_action_ip ?>"
                                                    data-grouphead-reject-days="<?= $groupheaddays ?>"href="#GroupHeadrejectModal"
                                                    data-target="#GroupHeadrejectModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="GroupHeadrejectModal" tabindex="-1" role="dialog"
                                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel" style="color:red;">Rejection
                                                                    Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Rejection Date </b></span> : <span id='grouphead-reject-date'></span><br>
                                                                <span><b>Rejection Time </b></span> : <span id='grouphead-reject-time'></span><br>
                                                                <span><b>System IP </b></span> : <span
                                                                    id='grouphead-reject-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span id='grouphead-reject-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </span>
                                        @endif
                                    </td>
                                @else
                                    <td>
                                        @if ($nv_list->groupcio_status == 0)
                                       {{-- @if($proposal_no->budget_type == 'CAPEX' && ($nv_list->ces_status == 1 || $nv_list->ces_status == 2))
                                        <span>N/A</span>
                                        @elseif($proposal_no->budget_type == 'OPEX' && ($nv_list->ceo_nominee_status == 1 || $nv_list->ceo_nominee_status == 2))
                                        <span>N/A</span>
                                        @endif --}}
                                        <span><br>
                                            @if (!empty($nv_list->groupcio_timestamp))
                                                    {{ date('d-M-y', strtotime($nv_list->groupcio_timestamp)) }}
                                                    <br> {{ date('h:i A', strtotime($nv_list->groupcio_timestamp)) }}
                                                    </span>
                                            @endif
                                        @elseif($nv_list->groupcio_status == 1)
                                            {{ getGroupHeadName($nv_list->groupcio_id ?? '') }}
                                            <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i></b><br>
                                                <a class="grouphead-approve-modal " style="border:none; background-color:none; color:black;"
                                                    rel="dialog" data-toggle="modal"
                                                    data-grouphead-approve-date="<?= date('d-M-y', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-approve-time="<?= date('h:i A', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-approve-ip="<?= $nv_list->groupcio_action_ip ?>"
                                                    data-grouphead-approve-days="<?= $groupheaddays ?>"href="#GroupHeadapproveModal"
                                                    data-target="#GroupHeadapproveModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="GroupHeadapproveModal" tabindex="-1" role="dialog"
                                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel" style="color:green;">Approval
                                                                    Status</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Approval Date </b></span> : <span id='grouphead-approve-date'></span><br>
                                                                <span><b>Approval Time </b></span> : <span id='grouphead-approve-time'></span><br>
                                                                <span><b>System IP </b></span> : <span id='grouphead-approve-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span id='grouphead-approve-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </span>
                                        @elseif($nv_list->groupcio_status == 2)
                                            {{ getGroupHeadName($nv_list->groupcio_id ?? '') }}
                                            <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>
                                                <a class="grouphead-reject-modal " style="border:none; background-color:none; color:black;"
                                                    rel="dialog" data-toggle="modal"
                                                    data-grouphead-reject-date="<?= date('d-M-y', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-reject-time="<?= date('h:i A', strtotime($nv_list->groupcio_timestamp)) ?>"
                                                    data-grouphead-reject-ip="<?= $nv_list->groupcio_action_ip ?>"
                                                    data-grouphead-reject-days="<?= $groupheaddays ?>"href="#GroupHeadrejectModal"
                                                    data-target="#GroupHeadrejectModal">
                                                    View
                                                </a>
                                                <div class="modal fade modal" id="GroupHeadrejectModal" tabindex="-1" role="dialog"
                                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel" style="color:red;">Rejection Status
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <span><b>Rejection Date </b></span> : <span id='grouphead-reject-date'></span><br>
                                                                <span><b>Rejection Time </b></span> : <span id='grouphead-reject-time'></span><br>
                                                                <span><b>System IP </b></span> : <span id='grouphead-reject-ip'></span><br>
                                                                <!-- <span><b>Pendency </b></span> : <span id='grouphead-reject-days'></span> -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </span>
                                        @endif
                                    </td>
                                @endif

                                @if(!empty($workflowStages))
                                    @foreach ($workflowStages as $workflowStage)

                                            @php 
                                                $signatures = null;

                                                if ($nv_list->service_id == null){
                                                            $signatures = DB::table('capex_workflows_status')
                                                            ->where('nv_id', $nv_list->nv_id)
                                                            ->where('material_id', $nv_list->material_id)
                                                            ->whereIn('nv_stage_status',[1,2])
                                                            ->where('nv_budget_type', 'CAPEX')
                                                            ->where('nv_stage_remark', '!=', null)
                                                            ->where('reviewer_name', 'approver')
                                                            ->where('department_id', $workflowStage->work_dep)
                                                            ->get();
                                                }else{
                                                            $signatures = DB::table('capex_workflows_status')
                                                            ->where('nv_id', $nv_list->nv_id)
                                                            ->where('service_id', $nv_list->service_id)
                                                            ->whereIn('nv_stage_status',[1,2])
                                                            ->where('nv_budget_type', 'CAPEX')
                                                            ->where('nv_stage_remark', '!=', null)
                                                            ->where('reviewer_name', 'approver')
                                                            ->where('department_id', $workflowStage->work_dep)
                                                            ->get();
                                                }
                                            @endphp
                                        <td>
                                            @if(!empty($signatures))
                                                @foreach($signatures as $signature)
                                                    @if ($nv_list->service_id == null)
                                                        @if($signature->nv_stage_status == 1)
                                                            {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                        
                                                                <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i> </b><br>
                                                                    <a class="ces-approve-modal"
                                                                        style="border:none; background-color:none; color:black;" rel="dialog"
                                                                        data-toggle="modal"
                                                                        data-ces-approve-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-approve-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-approve-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                        data-ces-approve-days="<?= $cesdays ?>"href="#CESapproveModal"
                                                                        data-target="#CESapproveModal">
                                                                        View
                                                                    </a>
                                                                    
                                                                    <div class="modal fade modal" id="CESapproveModal" tabindex="-1"
                                                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="exampleModalLabel"
                                                                                        style="color:green;">Approval Status</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                                        aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <span><b>Approval Date </b></span> : <span
                                                                                        id='ces-approve-date'></span><br>
                                                                                    <span><b>Approval Time </b></span> : <span
                                                                                        id='ces-approve-time'></span><br>
                                                                                    <span><b>System IP </b></span> : <span
                                                                                        id='ces-approve-ip'></span><br>
                                                                                    <!-- <span><b>Pendency </b></span> : <span
                                                                                        id='ces-approve-days'></span> -->
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-default"
                                                                                        data-dismiss="modal">
                                                                                        Close
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </span>
                                                        @elseif($signature->nv_stage_status == 2)
                                                            {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                                <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>

                                                                    <a class="ces-reject-modal "
                                                                        style="border:none; background-color:none; color:black;" rel="dialog"
                                                                        data-toggle="modal"
                                                                        data-ces-reject-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-reject-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-reject-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                        data-ces-reject-days="<?= $cesdays ?>"href="#CESrejectModal"
                                                                        data-target="#CESrejectModal">
                                                                        View
                                                                    </a>
                                                                    <div class="modal fade modal" id="CESrejectModal" tabindex="-1"
                                                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="exampleModalLabel"
                                                                                        style="color:red">
                                                                                        Rejection Status</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                                        aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <span><b>Rejection Date </b></span> : <span
                                                                                        id='ces-reject-date'></span><br>
                                                                                    <span><b>Rejection Time </b></span> : <span
                                                                                        id='ces-reject-time'></span><br>
                                                                                    <span><b>System IP </b></span> : <span
                                                                                        id='ces-reject-ip'></span><br>
                                                                                    <!-- <span><b>Pendency </b></span> : <span id='ces-reject-days'></span> -->
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-default"
                                                                                        data-dismiss="modal">
                                                                                        Close
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </span>
                                                        @endif
                                                    
                                                    @else
                                                        @if($signature->nv_stage_status == 1)
                                                            {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                                <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i> </b><br>
                                                                    <a class="ces-approve-modal"
                                                                        style="border:none; background-color:none; color:black;" rel="dialog"
                                                                        data-toggle="modal"
                                                                        data-ces-approve-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-approve-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-approve-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                        data-ces-approve-days="<?= $cesdays ?>"href="#CESapproveModal"
                                                                        data-target="#CESapproveModal">
                                                                        View
                                                                    </a>
                                                                    <div class="modal fade modal" id="CESapproveModal" tabindex="-1"
                                                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="exampleModalLabel"
                                                                                        style="color:green;">Approval Status</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                                        aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <span><b>Approval Date </b></span> : <span
                                                                                        id='ces-approve-date'></span><br>
                                                                                    <span><b>Approval Time </b></span> : <span
                                                                                        id='ces-approve-time'></span><br>
                                                                                    <span><b>System IP </b></span> : <span
                                                                                        id='ces-approve-ip'></span><br>
                                                                                    <!-- <span><b>Pendency </b></span> : <span
                                                                                        id='ces-approve-days'></span> -->
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-default"
                                                                                        data-dismiss="modal">
                                                                                        Close
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </span>
                                                        @elseif($signature->nv_stage_status == 2)
                                                            {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                                <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>

                                                                    <a class="ces-reject-modal "
                                                                        style="border:none; background-color:none; color:black;" rel="dialog"
                                                                        data-toggle="modal"
                                                                        data-ces-reject-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-reject-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-reject-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                        data-ces-reject-days="<?= $cesdays ?>"href="#CESrejectModal"
                                                                        data-target="#CESrejectModal">
                                                                        View
                                                                    </a>
                                                                    <div class="modal fade modal" id="CESrejectModal" tabindex="-1"
                                                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="exampleModalLabel"
                                                                                        style="color:red">
                                                                                        Rejection Status</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                                        aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <span><b>Rejection Date </b></span> : <span
                                                                                        id='ces-reject-date'></span><br>
                                                                                    <span><b>Rejection Time </b></span> : <span
                                                                                        id='ces-reject-time'></span><br>
                                                                                    <span><b>System IP </b></span> : <span
                                                                                        id='ces-reject-ip'></span><br>
                                                                                    <!-- <span><b>Pendency </b></span> : <span id='ces-reject-days'></span> -->
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-default"
                                                                                        data-dismiss="modal">
                                                                                        Close
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </span>
                                                        @endif
                                                        
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                    @endforeach
                                @endif

                                @if(!empty($opexWorkflowStages))
                                    @foreach ($opexWorkflowStages as $opexWorkflowStage)

                                        @php 
                                            $signatures = null;
                                            if ($nv_list->service_id == null){
                                                $signatures = DB::table('capex_workflows_status')
                                                    ->where('nv_id', $nv_list->nv_id)
                                                    ->where('material_id', $nv_list->material_id)
                                                    ->whereIn('nv_stage_status',[1,2])
                                                    ->where('nv_budget_type', 'OPEX')
                                                    ->where('nv_stage_remark', '!=', null)
                                                    ->where('reviewer_name', 'approver')
                                                    ->where('department_id', $opexWorkflowStage->work_dep)
                                                    ->get();
                                            }else{
                                                $signatures = DB::table('capex_workflows_status')
                                                    ->where('nv_id', $nv_list->nv_id)
                                                    ->where('service_id', $nv_list->service_id)
                                                    ->whereIn('nv_stage_status',[1,2])
                                                    ->where('nv_budget_type', 'OPEX')
                                                    ->where('nv_stage_remark', '!=', null)
                                                    ->where('reviewer_name', 'approver')
                                                    ->where('department_id', $opexWorkflowStage->work_dep)
                                                    ->get();
                                            }
                                    
                                        @endphp
                                        <td>
                                            @if(!empty($signatures))
                                                @foreach($signatures as $signature)
                                                    @if ($nv_list->service_id == null) 
                                                        @if($signature->nv_stage_status == 1)
                                                            {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                        
                                                                <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i> </b><br>
                                                                    <a class="ces-approve-modal"
                                                                        style="border:none; background-color:none; color:black;" rel="dialog"
                                                                        data-toggle="modal"
                                                                        data-ces-approve-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-approve-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-approve-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                        data-ces-approve-days="<?= $cesdays ?>"href="#CESapproveModal"
                                                                        data-target="#CESapproveModal">
                                                                        View
                                                                    </a>
                                                                    
                                                                    <div class="modal fade modal" id="CESapproveModal" tabindex="-1"
                                                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="exampleModalLabel"
                                                                                        style="color:green;">Approval Status</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                                        aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <span><b>Approval Date </b></span> : <span
                                                                                        id='ces-approve-date'></span><br>
                                                                                    <span><b>Approval Time </b></span> : <span
                                                                                        id='ces-approve-time'></span><br>
                                                                                    <span><b>System IP </b></span> : <span
                                                                                        id='ces-approve-ip'></span><br>
                                                                                    <!-- <span><b>Pendency </b></span> : <span
                                                                                        id='ces-approve-days'></span> -->
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-default"
                                                                                        data-dismiss="modal">
                                                                                        Close
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </span>
                                                        @elseif($signature->nv_stage_status == 2)
                                                            {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                                <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>

                                                                    <a class="ces-reject-modal "
                                                                        style="border:none; background-color:none; color:black;" rel="dialog"
                                                                        data-toggle="modal"
                                                                        data-ces-reject-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-reject-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-reject-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                        data-ces-reject-days="<?= $cesdays ?>"href="#CESrejectModal"
                                                                        data-target="#CESrejectModal">
                                                                        View
                                                                    </a>
                                                                    <div class="modal fade modal" id="CESrejectModal" tabindex="-1"
                                                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="exampleModalLabel"
                                                                                        style="color:red">
                                                                                        Rejection Status</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                                        aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <span><b>Rejection Date </b></span> : <span
                                                                                        id='ces-reject-date'></span><br>
                                                                                    <span><b>Rejection Time </b></span> : <span
                                                                                        id='ces-reject-time'></span><br>
                                                                                    <span><b>System IP </b></span> : <span
                                                                                        id='ces-reject-ip'></span><br>
                                                                                    <!-- <span><b>Pendency </b></span> : <span id='ces-reject-days'></span> -->
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-default"
                                                                                        data-dismiss="modal">
                                                                                        Close
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </span>
                                                        @endif
                                                    
                                                    @else
                                                        @if($signature->nv_stage_status == 1)
                                                            {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                                <span style="color: green;"><b><br> <i class='fas fa-check-circle'></i> </b><br>
                                                                    <a class="ces-approve-modal"
                                                                        style="border:none; background-color:none; color:black;" rel="dialog"
                                                                        data-toggle="modal"
                                                                        data-ces-approve-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-approve-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-approve-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                        data-ces-approve-days="<?= $cesdays ?>"href="#CESapproveModal"
                                                                        data-target="#CESapproveModal">
                                                                        View
                                                                    </a>
                                                                    <div class="modal fade modal" id="CESapproveModal" tabindex="-1"
                                                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="exampleModalLabel"
                                                                                        style="color:green;">Approval Status</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                                        aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <span><b>Approval Date </b></span> : <span
                                                                                        id='ces-approve-date'></span><br>
                                                                                    <span><b>Approval Time </b></span> : <span
                                                                                        id='ces-approve-time'></span><br>
                                                                                    <span><b>System IP </b></span> : <span
                                                                                        id='ces-approve-ip'></span><br>
                                                                                    <!-- <span><b>Pendency </b></span> : <span
                                                                                        id='ces-approve-days'></span> -->
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-default"
                                                                                        data-dismiss="modal">
                                                                                        Close
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </span>
                                                        @elseif($signature->nv_stage_status == 2)
                                                            {{ getStatusUserName($signature->workflow_user_id ?? '') }}
                                                                <span style="color: red;"><b><br><i class='fas fa-times-circle'></i></b><br>

                                                                    <a class="ces-reject-modal "
                                                                        style="border:none; background-color:none; color:black;" rel="dialog"
                                                                        data-toggle="modal"
                                                                        data-ces-reject-date="<?= date('d-M-y', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-reject-time="<?= date('h:i A', strtotime($signature->nv_stage_timestamp)) ?>"
                                                                        data-ces-reject-ip="<?= $signature->nv_stage_action_ip ?>"
                                                                        data-ces-reject-days="<?= $cesdays ?>"href="#CESrejectModal"
                                                                        data-target="#CESrejectModal">
                                                                        View
                                                                    </a>
                                                                    <div class="modal fade modal" id="CESrejectModal" tabindex="-1"
                                                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="exampleModalLabel"
                                                                                        style="color:red">
                                                                                        Rejection Status</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                                        aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <span><b>Rejection Date </b></span> : <span
                                                                                        id='ces-reject-date'></span><br>
                                                                                    <span><b>Rejection Time </b></span> : <span
                                                                                        id='ces-reject-time'></span><br>
                                                                                    <span><b>System IP </b></span> : <span
                                                                                        id='ces-reject-ip'></span><br>
                                                                                    <!-- <span><b>Pendency </b></span> : <span id='ces-reject-days'></span> -->
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-default"
                                                                                        data-dismiss="modal">
                                                                                        Close
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </span>
                                                        @endif
                                                    
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                              

                            </tr>
                        @php
                    $sequenceNumber-- 
                @endphp
                    @endforeach  
               
               
               
            @endif
        </tbody>
    </table>
    </div>

    <br><br><br>

    </section>




    </div>

   
    <div class="set-signature-popup"style="position: absolute; top:12%; left:70%;">

        <a class="nav-link" data-toggle="dropdown" style="margin-top:-4px; position:">
            @if(\Auth::user()->signature_status == 1)
                {{-- <b style="color: #7b8190;font-size:20px; " class="btn" data-toggle="modal" data-target="#myModal">  &nbsp; Welcome, {{ \Auth::user()->name ?? Admin }}</b> --}}
            @else
           
                {{-- <div  id="alert_msg" class="alert alert-danger alert-block">  
                    <button type="button" id="click_btn" class="close" data-dismiss="alert">x</button>  
                    <strong>Please Set your Signature</strong>  
                </div>   --}}
               
            @endif
        </a>
    </div>
   

@endsection
@push('script')
<script>
    $(document).ready(function(){
        // Ye code hover wale popovers ko enable karega
        $('body').popover({
            selector: '[data-toggle="popover"]',
            trigger: 'hover',
            html: true,
            container: 'body' // Ye important hai: table ke bahar dikhane ke liye
        });
    });
</script>
    <script>
        $(document).ready(function() {
            if ($(document).find('#approve_list').length > 0) {
                $('#approve_list').DataTable({
                    responsive: false,
                    searching: true,
                    lengthChange: false,
                    dom: 'Bfrtip',
                    order: [[0, 'asc']]


                });
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.hod-approve-modal').click(function() {
                $('#hod-approve-date').html($(this).data('hod-approve-date'));
                $('#hod-approve-time').html($(this).data('hod-approve-time'));
                $('#hod-approve-ip').html($(this).data('hod-approve-ip'));
                $('#hod-approve-days').html($(this).data('hod-approve-days'));

                $('#HODapproveModal').modal('show');
            });

            $('.hod-reject-modal').click(function() {
                $('#hod-reject-date').html($(this).data('hod-reject-date'));
                $('#hod-reject-time').html($(this).data('hod-reject-time'));
                $('#hod-reject-ip').html($(this).data('hod-reject-ip'));
                $('#hod-reject-days').html($(this).data('hod-reject-days'));

                $('#HODrejectModal').modal('show');
            });

            $('.ces-approve-modal').click(function() {
                $('#ces-approve-date').html($(this).data('ces-approve-date'));
                $('#ces-approve-time').html($(this).data('ces-approve-time'));
                $('#ces-approve-ip').html($(this).data('ces-approve-ip'));
                $('#ces-approve-days').html($(this).data('ces-approve-days'));

                $('#CESapproveModal').modal('show');
            });

            $('.ces-reject-modal').click(function() {
                $('#ces-reject-date').html($(this).data('ces-reject-date'));
                $('#ces-reject-time').html($(this).data('ces-reject-time'));
                $('#ces-reject-ip').html($(this).data('ces-reject-ip'));
                $('#ces-reject-days').html($(this).data('ces-reject-days'));

                $('#CESrejectModal').modal('show');
            });

            $('.cpmg-approve-modal').click(function() {
                $('#cpmg-approve-date').html($(this).data('cpmg-approve-date'));
                $('#cpmg-approve-time').html($(this).data('cpmg-approve-time'));
                $('#cpmg-approve-ip').html($(this).data('cpmg-approve-ip'));
                $('#cpmg-approve-days').html($(this).data('cpmg-approve-days'));

                $('#CPMGapproveModal').modal('show');
            });

            $('.cpmg-reject-modal').click(function() {
                $('#cpmg-reject-date').html($(this).data('cpmg-reject-date'));
                $('#cpmg-reject-time').html($(this).data('cpmg-reject-time'));
                $('#cpmg-reject-ip').html($(this).data('cpmg-reject-ip'));
                $('#cpmg-reject-days').html($(this).data('cpmg-reject-days'));

                $('#CPMGrejectModal').modal('show');
            });

            $('.cto-approve-modal').click(function() {
                $('#cto-approve-date').html($(this).data('cto-approve-date'));
                $('#cto-approve-time').html($(this).data('cto-approve-time'));
                $('#cto-approve-ip').html($(this).data('cto-approve-ip'));
                $('#cto-approve-days').html($(this).data('cto-approve-days'));

                $('#CTOapproveModal').modal('show');
            });

            $('.cto-reject-modal').click(function() {
                $('#cto-reject-date').html($(this).data('cto-reject-date'));
                $('#cto-reject-time').html($(this).data('cto-reject-time'));
                $('#cto-reject-ip').html($(this).data('cto-reject-ip'));
                $('#cto-reject-days').html($(this).data('cto-reject-days'));

                $('#CTOrejectModal').modal('show');
            });

            $('.ceon1-approve-modal').click(function() {
                $('#ceon1-approve-date').html($(this).data('ceon1-approve-date'));
                $('#ceon1-approve-time').html($(this).data('ceon1-approve-time'));
                $('#ceon1-approve-ip').html($(this).data('ceon1-approve-ip'));
                $('#ceon1-approve-days').html($(this).data('ceon1-approve-days'));

                $('#CEON1approveModal').modal('show');
            });

            $('.ceon1-reject-modal').click(function() {
                $('#ceon1-reject-date').html($(this).data('ceon1-reject-date'));
                $('#ceon1-reject-time').html($(this).data('ceon1-reject-time'));
                $('#ceon1-reject-ip').html($(this).data('ceon1-reject-ip'));
                $('#ceon1-reject-days').html($(this).data('ceon1-reject-days'));

                $('#CEON1rejectModal').modal('show');
            });

            $('.ceon2-approve-modal').click(function() {
                $('#ceon2-approve-date').html($(this).data('ceon2-approve-date'));
                $('#ceon2-approve-time').html($(this).data('ceon2-approve-time'));
                $('#ceon2-approve-ip').html($(this).data('ceon2-approve-ip'));
                $('#ceon2-approve-days').html($(this).data('ceon2-approve-days'));

                $('#CEON2approveModal').modal('show');
            });

            $('.ceon2-reject-modal').click(function() {
                $('#ceon2-reject-date').html($(this).data('ceon2-reject-date'));
                $('#ceon2-reject-time').html($(this).data('ceon2-reject-time'));
                $('#ceon2-reject-ip').html($(this).data('ceon2-reject-ip'));
                $('#ceon2-reject-days').html($(this).data('ceon2-reject-days'));

                $('#CEON2rejectModal').modal('show');
            });

            $('.grouphead-approve-modal').click(function() {
                $('#grouphead-approve-date').html($(this).data('grouphead-approve-date'));
                $('#grouphead-approve-time').html($(this).data('grouphead-approve-time'));
                $('#grouphead-approve-ip').html($(this).data('grouphead-approve-ip'));
                $('#grouphead-approve-days').html($(this).data('grouphead-approve-days'));

                $('#GroupHeadapproveModal').modal('show');
            });

            $('.grouphead-reject-modal').click(function() {
                $('#grouphead-reject-date').html($(this).data('grouphead-reject-date'));
                $('#grouphead-reject-time').html($(this).data('grouphead-reject-time'));
                $('#grouphead-reject-ip').html($(this).data('grouphead-reject-ip'));
                $('#grouphead-reject-days').html($(this).data('grouphead-reject-days'));

                $('#GroupHeadrejectModal').modal('show');
            });

            $('.ceo-approve-modal').click(function() {
                $('#ceo-approve-date').html($(this).data('ceo-approve-date'));
                $('#ceo-approve-time').html($(this).data('ceo-approve-time'));
                $('#ceo-approve-ip').html($(this).data('ceo-approve-ip'));
                $('#ceo-approve-days').html($(this).data('ceo-approve-days'));

                $('#CEOapproveModal').modal('show');
            });

            $('.ceo-reject-modal').click(function() {
                $('#ceo-reject-date').html($(this).data('ceo-reject-date'));
                $('#ceo-reject-time').html($(this).data('ceo-reject-time'));
                $('#ceo-reject-ip').html($(this).data('ceo-reject-ip'));
                $('#ceo-reject-days').html($(this).data('ceo-reject-days'));

                $('#CEOrejectModal').modal('show');
            });
        });
    </script>
<script>
    $(document).ready(function() {

        $("#export_pdf").hide();

        $("#export_excel").hide();

        $("#export_btn").click(function() {

            $("#export_pdf").toggle();

            $("#export_excel").toggle();

        });
    });
</script>
    <script src="http://thecodeplayer.com/uploads/js/prefixfree-1.0.7.js" type="text/javascript" type="text/javascript">
    </script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="{{ asset('theme/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('admin/js/brand.js') }}"></script>
    <!-- <script src="{{ asset('admin/js/employee.js') }}"></script> -->
    <script src="{{ asset('theme/plugins/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('admin/js/export.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

  <!-- Include jQuery -->

    <!-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get the modal element
            const modal = document.getElementById("welcomepopup");

            // Show the modal
            modal.style.display = "block";

            // Get the close button element inside the modal
            const closeButton = modal.querySelector(".close");

            // Close the modal when the close button is clicked
            closeButton.addEventListener("click", function() {
                modal.style.display = "none";
            });

            // Close the modal when clicking outside the modal content
            window.addEventListener("click", function(event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });
        });
    </script> -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get the modal element
        const modal = document.getElementById("welcomepopup");

        @auth
            // Get the user's login count from the authenticated user
            const loginCount = {{ auth()->user()->login_count }};

            if (loginCount < 4) {
                // Show the modal
                modal.style.display = "block";

                // Get the close button element inside the modal
                const closeButton = modal.querySelector(".close");

                // Close the modal when the close button is clicked
                closeButton.addEventListener("click", function() {
                    modal.style.display = "none";
                });

                // Close the modal when clicking outside the modal content
                window.addEventListener("click", function(event) {
                    if (event.target === modal) {
                        modal.style.display = "none";
                    }
                });
            } else {
                // User has seen the popup three times, so hide it
                modal.style.display = "none";
            }
        @else
            // User is not logged in, so hide the popup
            modal.style.display = "none";
        @endauth
    });
</script>



    <script>

$(document).ready(function() {

// Your BRPL graph code...
var BRPLlabels = {!! json_encode($BRPLlabels) !!};
var BRPLapprovedData = {!! json_encode($BRPLapprovedData) !!};
var BRPLrejectedData = {!! json_encode($BRPLrejectedData) !!};
var BRPLpendingData = {!! json_encode($BRPLpendingData) !!};

// BRPL GRAPH
$(function() {
    var areaChartData = {
        labels: BRPLlabels,
        datasets: [
            {
                label: 'Approved',
                backgroundColor: '#00873E',
                borderColor: 'rgba(210, 214, 222, 1)',
                pointRadius: false,
                pointColor: 'rgba(210, 214, 222, 1)',
                pointStrokeColor: '#c1c7d1',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(220,220,220,1)',
                data: BRPLapprovedData
            },
            {
                label: 'Pending',
                backgroundColor: '#FFBF00',
                borderColor: 'rgba(60,141,188,0.8)',
                pointRadius: false,
                pointColor: '#3b8bba',
                pointStrokeColor: 'rgba(60,141,188,1)',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data: BRPLpendingData
            },
            {
                label: 'Rejected',
                backgroundColor: '#D6001C',
                borderColor: 'rgba(210, 214, 222, 1)',
                pointRadius: false,
                pointColor: 'rgba(210, 214, 222, 1)',
                pointStrokeColor: '#c1c7d1',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(220,220,220,1)',
                data: BRPLrejectedData
            },
        ]
    };

    var areaChartOptions = {
        maintainAspectRatio: false,
        responsive: true,
        legend: {
            display: false
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        display: false,
                    }
                }
            ],
            yAxes: [
                {
                    gridLines: {
                        display: false,
                    }
                }
            ]
        }
    };

    var barChartCanvas = $('#barChartOne').get(0).getContext('2d');
    var barChartData = $.extend(true, {}, areaChartData);
    var temp0 = areaChartData.datasets[0];
    var temp1 = areaChartData.datasets[1];
    barChartData.datasets[0] = temp1;
    barChartData.datasets[1] = temp0;

    var barChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false
    };

    new Chart(barChartCanvas, {
        type: 'bar',
        data: barChartData,
        options: barChartOptions
    });
});

// Your BYPL graph code...
var BYPLlabels = {!! json_encode($BYPLlabels) !!};
var BYPLapprovedData = {!! json_encode($BYPLapprovedData) !!};
var BYPLrejectedData = {!! json_encode($BYPLrejectedData) !!};
var BYPLpendingData = {!! json_encode($BYPLpendingData) !!};

// BYPL GRAPH
$(function() {
    var areaChartData = {
        labels: BYPLlabels,
        datasets: [
            {
                label: 'Approved',
                backgroundColor: '#00873E',
                borderColor: 'rgba(210, 214, 222, 1)',
                pointRadius: false,
                pointColor: 'rgba(210, 214, 222, 1)',
                pointStrokeColor: '#c1c7d1',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(220,220,220,1)',
                data: BYPLapprovedData
            },
            {
                label: 'Pending',
                backgroundColor: '#FFBF00',
                borderColor: 'rgba(60,141,188,0.8)',
                pointRadius: false,
                pointColor: '#3b8bba',
                pointStrokeColor: 'rgba(60,141,188,1)',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data: BYPLpendingData
            },
            {
                label: 'Rejected',
                backgroundColor: '#D6001C',
                borderColor: 'rgba(210, 214, 222, 1)',
                pointRadius: false,
                pointColor: 'rgba(210, 214, 222, 1)',
                pointStrokeColor: '#c1c7d1',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(220,220,220,1)',
                data: BYPLrejectedData
            },
        ]
    };

    var areaChartOptions = {
        maintainAspectRatio: false,
        responsive: true,
        legend: {
            display: false
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        display: false,
                    }
                }
            ],
            yAxes: [
                {
                    ticks: {
                        stepSize: 1
                    },
                    gridLines: {
                        beginAtZero: true,
                        precision: 0,
                        display: false,
                    }
                }
            ]
        }
    };

    var barChartCanvas = $('#barCharttwo').get(0).getContext('2d');
    var barChartData = $.extend(true, {}, areaChartData);
    var temp0 = areaChartData.datasets[0];
    var temp1 = areaChartData.datasets[1];
    barChartData.datasets[0] = temp1;
    barChartData.datasets[1] = temp0;

    var barChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false
    };

    new Chart(barChartCanvas, {
        type: 'bar',
        data: barChartData,
        options: barChartOptions
    });
});
$('#company').change(function() {
    var item = $(this);
    if (item.val() == 0) {
        window.location.href = "{{ url('admin/dashboard') }}"
    } else {
        window.location.href = item.val();
    }
});
// $('#fiscal_year').change(function() {
//     var item = $(this);
//     if (item.val() == 0) {
//         window.location.href = "{{ url('admin/dashboard') }}"
//     } else {
//         window.location.href = item.val();
//     }
// });
});

// function initializeChart() {
//     var barChartCanvas = $('#barChartOne').get(0).getContext('2d');
//     var barChartData = $.extend(true, {}, areaChartData);
//     var temp0 = areaChartData.datasets[0];
//     var temp1 = areaChartData.datasets[1];
//     barChartData.datasets[0] = temp1;
//     barChartData.datasets[1] = temp0;

//     var barChartOptions = {
//         responsive: true,
//         maintainAspectRatio: false,
//         datasetFill: false
//     };

//     new Chart(barChartCanvas, {
//         type: 'bar',
//         data: barChartData,
//         options: barChartOptions
//     });
// }
// initializeChart();



    </script>

<script>
        var BRPLlabels = {!! json_encode($BRPLlabels) !!};

        var approvedAmountBRPL = {!! json_encode($approvedAmountBRPL) !!};

        if ({!! json_encode($approvedAmountBRPL) !!} >= 100000) {
            var approvedAmountBRPL = ({!! json_encode($approvedAmountBRPL) !!})/100000;
        } else {
            var approvedAmountBRPL = {!! json_encode($approvedAmountBRPL) !!};
        }

        var pendingAmountBRPL = {!! json_encode($pendingAmountBRPL) !!};

        if ({!! json_encode($pendingAmountBRPL) !!} >= 100000) {
            var pendingAmountBRPL = ({!! json_encode($pendingAmountBRPL) !!})/100000;
        } else {  
            var pendingAmountBRPL = {!! json_encode($pendingAmountBRPL) !!};
        }

        var rejectedAmountBRPL = {!! json_encode($rejectedAmountBRPL) !!};

        if ({!! json_encode($rejectedAmountBRPL) !!} >= 100000) {
            var rejectedAmountBRPL = ({!! json_encode($rejectedAmountBRPL) !!})/100000;
        } else {
            var rejectedAmountBRPL = {!! json_encode($rejectedAmountBRPL) !!};
        }


        var BYPLlabels = {!! json_encode($BYPLlabels) !!};

        var approvedAmountBYPL = {!! json_encode($approvedAmountBYPL) !!};

        if ({!! json_encode($approvedAmountBYPL) !!} >= 100000) {
            var approvedAmountBYPL = ({!! json_encode($approvedAmountBYPL) !!})/100000;
        } else {
            var approvedAmountBYPL = {!! json_encode($approvedAmountBYPL) !!};
        }

        var pendingAmountBYPL = {!! json_encode($pendingAmountBYPL) !!};

        if ({!! json_encode($pendingAmountBYPL) !!} >= 100000) {
            var pendingAmountBYPL = ({!! json_encode($pendingAmountBYPL) !!})/100000;
            // alert(pendingAmountBYPL);
        } else {
            var pendingAmountBYPL = {!! json_encode($pendingAmountBYPL) !!};
        }

        var rejectedAmountBYPL = {!! json_encode($rejectedAmountBYPL) !!};

        if ({!! json_encode($rejectedAmountBYPL) !!} >= 100000) {
            var rejectedAmountBYPL = ({!! json_encode($rejectedAmountBYPL) !!})/100000;
        } else {
            var rejectedAmountBYPL = {!! json_encode($rejectedAmountBYPL) !!};
        }

 
var xValues = [ "Pending NV Amount","Approved NV Amount","Rejected NV Amount", ];
var yValues = [pendingAmountBRPL,approvedAmountBRPL, rejectedAmountBRPL ];
var barColors = [
    "#FFBF00",
    "#1e7145",
    "#b91d47",
];

new Chart("myChartOne", {
  type: "pie",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
  options: {
    title: {
      display: true,
      text: "BRPL NV Amount (In Lac)"
    }
  }
});

var xValues = [ "Pending NV Amount","Approved NV Amount","Rejected NV Amount" ];
var yValues = [pendingAmountBYPL,approvedAmountBYPL, rejectedAmountBYPL ];
var barColors = [
    "#FFBF00",
    "#1e7145",
    "#b91d47",
];

new Chart("myChartTwo", {
  type: "pie",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
  options: {
    title: {
      display: true,
      text: "BYPL NV Amount (In Lac)"
    }
  }
});
    </script>
 

<script>
    function formatValue(value) {
        // if (value >= 10000000) {
        //     return (value / 10000000).toFixed(2) + 'cr';
        // } else
        if (value >= 100000) {
            return (value / 100000).toFixed(2) + 'Lac';
        } else {
            return value;
        }
    }
    // Loop through each element with the class "amount-value" and format its value
    $('.amount-value').each(function() {
        var rawValue = $(this).text().trim();
        var numericValue = parseFloat(rawValue);
        var formattedValue = formatValue(numericValue);
        $(this).text(formattedValue);
    });
</script>



<script>
   
            window.addEventListener('DOMContentLoaded', function () {
                const storedScroll = localStorage.getItem('scrollPosition');
                if (storedScroll) {
                    window.scrollTo(0, storedScroll);
                }
            });

         
            document.getElementById('total').addEventListener('click', function () {
                storeAndScroll();
            });
            document.getElementById('pending').addEventListener('click', function () {
                storeAndScroll();
            });
            document.getElementById('approve').addEventListener('click', function () {
                storeAndScroll();
            });
            document.getElementById('reject').addEventListener('click', function () {
                storeAndScroll();
            });

           
            function storeAndScroll() {
           
                localStorage.setItem('scrollPosition', window.scrollY);
               
               
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth'
                });
            }
</script>



@endpush
