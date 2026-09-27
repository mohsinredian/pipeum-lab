@extends('admin.layout.master', ['page_title' => 'Create Floor'])


@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Floors</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Location</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Floors</h3>
                </div>
                @if (\Auth::user()->isA('Admin'))
                 <div class="card-toolbar">
                    <a href="/admin/floor/create" class="btn btn-primary font-weight-bolder">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add Floor</a>
                </div> 
                @endif
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive">
                <table id="floor_datatable" class="table table-bordered">
                  <thead>
                  <tr>
                    <th class="text-center" width="5%">S.No</th>
                    <th>id</th>
                    <th>Location Name</th>
                    <th class="text-center">Floor Name</th>
                    <th class="text-center">Status</th>
                    <th>Action</th>
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
<!-- Content Header (Page header) -->
    <!-- /.content-header -->

    <!-- Main content -->
   
@endsection
   
@push('script')
<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('admin/js/floor.js')}}"></script>
@endpush