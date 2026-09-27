@extends('admin.layout.master', ['page_title' => 'List NV'])
@push('styles')
    <link rel="stylesheet" href="{{ asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <style>
        .list-btn .btn-danger {
            background-color: #ffc000;
            padding: 6px 20px;
            border-top-right-radius: 10px;
            border-top-right-radius: 10px;
            color: rgb(255, 255, 255);
            font-size: 14px;
            border: none;
            font-weight: 500;
            margin-bottom: -70px;
        }

            .list-btn .btn-primary {
            background-color: green;
            padding: 6px 20px;
            border-top-right-radius: 10px;
            border-top-right-radius: 10px;
            color: white;
            font-size: 14px;
            border: none;
            margin-bottom: -70px;
        }

        .card {
           box-shadow: 2px 2px 33px 5px rgb(0 0 0 / 25%) !important;
           }    
           
           button.dt-button, div.dt-button, a.dt-button, input.dt-button {
            background-color:rgb(3, 142, 220) !important;
            color: white !important;
           }
    </style>
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content">
        <div class="container-fluid">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Manage Need Validation</h1>
                        </div><!-- /.col -->

                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ URL::to('/admin/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">List NV</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <div class="row col-xsm-12">
            
            @php
                
                $user = \Auth::user();
                $role_id = $user->role_id;
            @endphp

            @if ($role_id != '11' && $role_id != '1')
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">

                    <div class="card text-center pt-3 pb-3 dashboad-icons">
                        <a href="/admin/needvalidations/create" style="color:rgb(44, 42, 42);";>
                            <span><i class="fa fa-plus-circle"></i></span><br><br>
                            <h3><b>Create NV</b></h3>
                            <p>
                                Add Need Validation
                            </p>

                        </a>
                    </div>

                </div>
                @if($role_id == 9 )
                
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 ">
                    <a href="{{route('list_delete')}}">
                        <div class="card text-center pt-3 pb-3 dashboad-icons">
                            <span><i class="fa fa-trash" aria-hidden="true"></i></span><br>
                            <h3><b> {{ $deleteNV ?? '' }} </b></h3>
                            <p>Deleted Need Validation</p>
                        </div>
                    </a>

                </div>
                @endif
               
                <div class="col-xl-4 col-lg-3 col-md-6 col-sm-6" style="visibility:hidden;">

                <div class="card text-center pt-3 pb-3 dashboad-icons">
                    <a href="/admin/nv-notes/create" style="color:rgb(44, 42, 42);";>
                        <span><i class="fa fa-plus-circle"></i></span><br><br>
                        <h3><b>Create Notes</b></h3>
                        <p>
                            Add Notes
                        </p>

                    </a>
                </div>

                </div>
            @endif

        </div>
            <div class="row col-xsm-12">
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 ">
                    <div class="card text-center pt-3 pb-3 dashboad-icons">
                        <span><i class="fas fa-copy"></i></span><br>
                        <h3><b> {{ $totalNV ?? 0 }} </b></h3>
                        <p>Total Processed NV</p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                    <div class="card text-center pt-3 pb-3 dashboad-icons">
                        <span><i class="fas fa-thumbs-up"></i></span><br>
                        <h3><b> {{ $approvedNV ?? 0 }} </b></h3>
                        <p>Approved NV</p>
                    </div>

                </div>
                <div class="clearfix hidden-md-up"></div>

                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                    <div class="card text-center pt-3 pb-3 dashboad-icons">
                        <span><i class="far fa-file-excel"></i></span><br>
                        <h3><b> {{ $rejectedNV ?? 0 }}</b></h3>
                        <p>Rejected NV</p>
                    </div>

                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                    <div class="card text-center pt-3 pb-3 dashboad-icons">
                        <span><i class="far fa-clock"></i></span><br>
                        <h3><b> {{ $pendingNV ?? 0 }}</b></h3>
                        <p>Pending NV</p>
                    </div>
                </div>
              

            </div>
            
          
        </div>

    </section><br>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h5 class="card-label mt-2" style="color:rgb(3, 142, 220)  !important;">List of Need
                                    Validation</h5>
                            </div>
                            @if (\Auth::user()->isA('Admin'))
                                <div class="card-toolbar">
                                    <!-- <a href="/admin/needvalidations/create" class="btn btn-primary font-weight-bolder">
                                                            <i class="fa fa-plus-circle mr-1"></i>
                                                            Add Need Validation</a> -->
                                </div>
                            @endif
                        </div>

                     
                   
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="nv_datatable" class="table">
                                <thead class="text-center">
                                    <tr>
                                        <th class="text-center list-nv">S.No</th>
                                        <th class="list-nv">id</th>
                                        <!-- <th>Edit NV</th> -->
                                        <th class="list-nv">Proposal Number</th>
                                        <th class="list-nv">Fiscal Year</th>
                                        <th class="list-nv">Budget Type</th>
                                        <th class="list-nv">Initiated By</th>
                                        <th class="list-nv">Initiated Date</th>
                                        <th class="list-nv">DOP Ref No</th>
                                        <th class="list-nv">Budgetary Provision</th>
                                        <th class="list-nv">Proposal Type</th>
                                        <th class="list-nv">NV Type</th>
                                        
                                        <th class="list-nv">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->

            </div>
        </div><!-- /.container-fluid -->


    </section>
@endsection

@push('script')
    <script src="{{ asset('theme/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('admin/js/brand.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>

{{-- For Datatable buttons cdn --}}

    <script src="{{ asset('theme/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/pdfmake/vfs_fonts.js') }}"></script>

    <script src="{{ asset('admin/js/nv.js') }}"></script>
    
{{-- <script>

    $(document).ready(function () {

      $("#export_pdf").hide();

        $("#export_excel").hide();

        $("#export_btn").click(function(){

            $("#export_pdf").toggle();

            $("#export_excel").toggle();

        });

    

    });

</script> --}}
 
@endpush