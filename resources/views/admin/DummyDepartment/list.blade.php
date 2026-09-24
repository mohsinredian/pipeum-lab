@extends('admin.layout.master', ['page_title' => 'Dummy Department'])
@push('styles')
    <link rel="stylesheet" href="{{ asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <style>
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
                            <h1 class="m-0">Manage Dummy Department</h1>
                        </div><!-- /.col -->

                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ URL::to('/admin/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">Dummy Department</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <form class="form" id="create_nv_form">
                @csrf
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Dummy Department</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                  <div class="card-body">
                <div class="form-group row">
                    <div class="col-lg-4">
                        <label>Budget Amount</label>
                            <input type="text" readonly class="form-control decimal budget" id="budget"
                                value="{{ indian_number_format(sprintf('%.0f', $netCredit ?? 0)) }}"
                                placeholder="Budget Amount (In Rs.)">

                            <input type="hidden" 
                                name="budget" 
                                value="{{ sprintf('%.0f', $netCredit ?? 0) }}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-4">
                        <input type="number" class="form-control decimal mt-4" id="addAmount" name="addAmount"
                            placeholder="Add amount">
                    </div>
                    <div class="col-lg-4 mt-3">
                        <button type="button" class="btn btn-success mt-2" id="addButton">Add</button>
                    </div>
                </div>
            </div>

            </form>

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
                                <h5 class="card-label mt-2" style="color:rgb(3, 142, 220)  !important;">List of Dummy Department</h5>
                            </div>
                          
                        </div>
                       <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="dummy_datatable" class="table">
                                <thead class="text-center">
                                    <tr>
                                        <th class="text-center ">S.No</th>
                                        <th class="text-center ">Id</th>
                                        <th class="text-center">Department</th>
                                        <th class="text-center">Budget Type</th>
                                        <th class="text-center">Year</th>
                                        <th class="text-center">Transaction Type</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Updated At</th>
                                        
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

    <script src="{{ asset('admin/js/capex.js') }}"></script>

<script>
    // $(document).ready(function () {
        $('#addButton').click(function () {
            var addAmount = parseFloat($('#addAmount').val()) || 0; 
            var currentBudget = parseFloat($('#budget').val()) || 0; 
            if (!isNaN(addAmount)) { 
            var newBudget = currentBudget + addAmount; 
           }
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            $.ajax({
                url: '/admin/save-budget',
                method: 'POST',
                data: { budget: addAmount,
                    _token: csrfToken 
                 },
                success: function(response) {
                    Swal.fire({
							html: "Budget Amount Added Successfully",
							type: 'success',
							buttonsStyling: false,
							confirmButtonText: "OK",
							confirmButtonClass: "btn font-weight-bold btn-primary"
						}).then(function () {
							window.location = '/admin/list-dummy-department';
						});
                     
               
                $('#budget').val(newBudget); 
                $('#addAmount').val('')
         

                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error('Error updating budget:', error);
                }
            });

          
            
        });
    // });
</script>
@endpush