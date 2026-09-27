@extends('admin.layout.master', ['page_title' => 'report'])
@push('styles')
<link rel="stylesheet" href="{{asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('theme/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<style>
  * {
    font-family: 'Poppins', sans-serif;
  }

  .table td {
    text-align: center;
  }

  .table th {
    text-align: center;
  }

  .pagination {
    float: right;
  }
</style>

@endpush
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Manage Consolidated Requirement of FY 24-25</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
          <li class="breadcrumb-item active">Consolidated Requirement of FY 24-25</li>
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
              <h3 class="card-label">Consolidated Requirement of FY 24-25</h3>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive">
            <div class="row">
              <div class=" col-xl-1 col-lg-2 col-md-2 col-sm-12 mb-1">
              <a href="/admin/consolidated_report/download-excel?search={{ request('search') }}" 
                class="btn btn-primary font-weight-bolder" target="_blank">
                  <i class="fas fa-file-download mr-1"></i> Excel
              </a>
              </div>

              <div class="col-xl-4 col-lg-5 col-md-5 col-sm-12 mb-1">
                
                <div class="row">
                  <div class="col-6">
                  <select class="form-control" id="selectDepartment">
                    <option value="">Select Department</option>
                    @foreach($department as $dep)
                        <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                    @endforeach
                </select>
                  </div>
                </div>
            
              </div>

              <div class="col-xl-7 col-lg-5 col-md-5 col-sm-12 mb-1">
                <form action="/admin/reports" method="get">
                  <div class="row" style="display:flex; justify-content: end;">
                    <div class="col-8">
                      <div class="row ">
                        <div class="col-10">
                          <input type="text" name="search" class="form-control search"
                            placeholder="Search by Material Code/Description/Rate/UOM ..."
                            value="{{ request()->input('search') }}">
                        </div>

                        <div class="col-2 d-flex ">
                          <button type="submit" class="btn-sm btn-primary">Search</button>
                        </div>
                      </div>
                    </div>

                  </div>
                </form>
              </div>
            </div>


            <table id="reports_datatable" class="table table-bordered mt-3">
              <thead class="text-center">
                <tr>
                  <th class="text-center" width="5%">S.No</th>
                  <th>Material Code</th>
                  <th>Material Description</th>
                  <th>Rate</th>
                  <th>Category</th>
                  <th>UOM</th>
                  @foreach($department as $dep)
                  <th class="department-header" data-department-id="{{ $dep->id }}">{{ $dep->name }}</th>
                  @endforeach
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                @foreach($datas as $key => $item)
                <tr>
                  <td>{{ $key + 1 }}</td>
                  <td>{{ $item->activity }}</td>
                  <td>{{ $item->material_short_text }}</td>
                  <td>{{ $item->rate_add }}</td>
                  <td></td>
                  <td>{{ $item->uom }}</td>
                  @php
                  $totalCount = 0;
                  @endphp
                  @foreach($department as $dep)
                  @php
                  $count = $materialCounts[$dep->id][$item->activity] ?? 0;
                  $totalCount += $count;
                  @endphp
                  <td class="department-column" data-department-id="{{ $dep->id }}">
                      @if($count != 0) {{ $count }} @endif
                  </td>
                  @endforeach
                  <td>@if($totalCount != 0) {{ $totalCount }} @endif</td>
                </tr>
                @endforeach
              </tbody>
            </table><br>
            {{$datas->links()}}
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
<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
<!-- <script src="{{asset('admin/js/reports.js')}}"></script> -->
<!-- <script>
  $(function() {
    $("#reports_datatable").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": true,
      "info": false,
      "ordering": false, // Disable sorting
      "paging": false, // Disable pagination
      // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      // "buttons": ["csv","excel"]
    }).buttons().container().appendTo('#reports_datatable_wrapper .col-md-6:eq(0)');

    $('#example2').DataTable({
      "paging": false, // Disable pagination
      "lengthChange": false,
      "searching": false,
      "ordering": false, // Disable sorting
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script> -->

<script>
$(document).ready(function () {
    $('#selectDepartment').on('change', function () {
        var selectedDepartmentId = $(this).val();

        if (selectedDepartmentId) {
            $('.department-header, .department-column').hide();
            $('.department-header[data-department-id="' + selectedDepartmentId + '"]').show();
            $('.department-column[data-department-id="' + selectedDepartmentId + '"]').show();
        } else {
            $('.department-header, .department-column').show();
        }
    });
});

</script>


@endpush