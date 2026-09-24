@extends('admin.layout.master', ['page_title' => 'New Workflow'])
@push('styles')
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
@endpush
@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Notes Workflow</h1>
          </div><!-- /.col -->
          @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active"> Notes Workflow</li>
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
                    <h4 class="card-label">Notes Workflow</h4>
                </div>
                @if (\Auth::user()->isA('Admin'))
                <div class="card-toolbar mt-2">
                <a href="/admin/notesworkflows/download-excel" class="btn btn-primary font-weight-bolder" target="_blank">
                      <i class="fas fa-file-download mr-1"></i> Excel</a>
                    <a href="/admin/notesworkflows/create" class="btn btn-primary font-weight-bolder">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add Notes Workflow</a>
                </div>
                @endif
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive">
                <table id="userDataFilter" class="table table-bordered workflow-notes">
                  <thead class="text-center">
                  <tr>
                    <th class="text-center" width="5%">S.No</th>
                    <th>Workflow Name</th>
                    <th>Status</th>
                    <th>Add Stage</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                    @php
                    $sequenceNumber = 1; // Initialize the sequence number
                    @endphp
                    @foreach ($workflows as $workflow)
                    <tr >
                      <td >{{$sequenceNumber}}</td>
                      <td> {{ $workflow->name }}</td>
                      <td>@if($workflow->status ==0)
                        Inactive
                        @else
                        Active
                      @endif
                      </td>
                      <td><a href="{{ route('admin.edit_notesworkflows', ['workflow' => $workflow->id]) }}"><i class="fas fa fa-plus"></i></a></td>
                      <td>
                        <a href="{{ route('admin.edit_notesworkflows', ['workflow' => $workflow->id]) }}"><i class="fas fa-edit text-info"></i></a>
                      </td>
                    </tr>
                    @php
                    $sequenceNumber++; // Increment the sequence number
                @endphp
                    @endforeach
                  </tbody>
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
<script>
   $(function() {
                    $("#userDataFilter").DataTable({
                      "responsive": true,
                      "lengthChange": false,
                      "autoWidth": false,
                      // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
                      "buttons": ["csv", "excel"]
                    }).buttons().container().appendTo('#userDataFilter_wrapper .col-md-6:eq(0)');
                    $('#example2').DataTable({
                      "paging": true,
                      "lengthChange": false,
                      "searching": false,
                      "ordering": true,
                      "info": true,
                      "autoWidth": false,
                      "responsive": true,
                    });
                  });
</script>
<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>

@endpush