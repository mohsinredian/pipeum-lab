@extends('admin.layout.master', ['page_title' => 'Servicesboq'])
@push('styles')
<link rel="stylesheet" href="{{ asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<style>
    .pagination{
        float:right;
        margin-top: 15px;
    }
</style>
@endpush
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Manage Material BOQ</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ URL::to('/admin/dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Material BOQ</li>
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
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">Material BOQ</h3>
                        </div>
                        @if (\Auth::user()->isA('Admin'))
                        <div class="card-toolbar">
                            
                            <!-- Upload File Button -->
                            <form id="upload-form" action="{{ route('capex.upload') }}" method="POST" enctype="multipart/form-data" style="display: inline-block;">
                                @csrf
                                <label for="file-upload" class="btn btn-secondary font-weight-bolder m-0">
                                    <i class="fas fa-file-upload mr-1"></i> Upload File
                                </label>
                                <input id="file-upload" type="file" name="file" style="display:none;">
                            </form>
                            <a href="/admin/materialboq/download-excel" class="btn btn-primary font-weight-bolder" target="_blank">
                                        <i class="fas fa-file-download mr-1"></i> Excel</a>
                                    
                            <a href="/admin/materialboq/create" class="btn btn-primary font-weight-bolder">
                                <i class="fa fa-plus-circle mr-1"></i>
                                Add Material BOQ</a>
                        </div>
                        @endif
                    </div>
                    <!-- /.card-header -->

                    <div class="card-body table-responsive">
                        <table id="materialboq_datatable" class="table table-bordered">
                            <thead class="text-center">
                                <tr>
                                    <th class="text-center" width="5%">S.No</th>
                                    <th class="text-center">Material Code</th>
                                    <th class="text-center">UoM</th>
                                    <th class="text-center">Material Description</th>
                                    <th class="text-center">Rate</th>

                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $dataRow)
                                <tr>

                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $dataRow->activity }}</td>
                                    <td>{{ $dataRow->uom }}</td>
                                    <td>{{ $dataRow->material_short_text }}</td>
                                    <td>{{ $dataRow->rate_add }}</td>
                                    <td>
                                        <a href="{{ url('admin/materialboq/edit', $dataRow->id) }}" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $data->links() }}
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
<script>
    $(document).ready(function() {
        $('#materialboq_datatable').DataTable({
            "paging": false,
            "pageLength": 10, // number of rows per page
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ] // dropdown to change number of rows per page
        });
    });
</script>
<script>
    //import
    $(document).ready(function() {
        $('#file-upload').on('change', () => {
            $.ajax({
                url: '/admin/materialboq/upload',
                type: 'POST',
                data: new FormData($('#upload-form')[0]),
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    swal({
                        title: "Success!",
                        text: "Your file has been Imported.",
                        icon: "success",
                        button: "OK"
                    }).then(function() {
							window.location = '/admin/materialboq';
						});
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.error('There was a problem with the ajax operation:', errorThrown);
                    swal({
                        title: "Error!",
                        text: "Something went wrong. Please try again later.",
                        icon: "error",
                        button: "OK"
                    });
                    $('#file-upload').val('');
                }
            });
        });
    });
</script>
<script src="{{ asset('theme/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
@endpush