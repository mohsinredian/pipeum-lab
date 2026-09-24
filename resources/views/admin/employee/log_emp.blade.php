@extends('admin.layout.master', ['page_title' => 'OpexLog'])
@push('styles')
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <style>
        .pagination {

            justify-content: end;
        }
        .table td, th{
                    vertical-align:inherit !important;
        }
    </style>
@endpush
@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Employee Log</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Employee Log</li>
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
                    <h3 class="card-label">Employee Log</h3>
                </div>
               
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive">
              <div style="text-align-last: end;">
              <a href="/admin/logemployees/download-excel" class="btn btn-primary font-weight-bolder" target="_blank" style="float:left;">
              <i class="fas fa-file-download mr-1"></i> Excel</a>
                Search: <input id="myInput" type="text" style="text-align-last: start;">
            </div>
              <table id="" class="table table-bordered mt-3">
                  <thead class="text-center">
                      <tr>
                          <th class="text-center" width="5%">S.No</th>
                          <th class="text-center" width="6%">Event</th>
                          <th class="text-center" width="7%">Audited By</th>
                          <th class="text-center" width="10%">Audited At</th>
                          <th width="8%"></th>
                          <th class="text-center" width="8%">Name</th>
                          <th class="text-center" width="7%">Email ID</th>
                          <th class="text-center" width="7%">Mobile No</th>
                          <!-- <th class="text-center" width="15%">Department</th> -->
                          <th class="text-center" width="25%">Sub-Department</th>
                          <th class="text-center" width="7%">Emp ID</th>
                          <th class="text-center" width="5%">Role</th>
                          <th class="text-center" width="5%">Status</th>
                      </tr>
                  </thead>
                
                  <tbody id="myTable">
                       @foreach($audits as $key => $audit)
                            @php
                            $newvalue =  $audit->new_values ?? [];
                            $oldvalue =  $audit->old_values ?? [];
                            $department = App\Models\Department::find($audit->auditable_id);
                            $userName = optional($audit->user)->name ?? ' ';
                            $date = $audit->created_at->format('d-M-Y h:i:s A');
                            $event = ucfirst($audit->event);
                            @endphp
                          <tbody class="audit-group">
                          <tr>
                          <td rowspan="2">{{ $key + 1 }}</td>
                            <td rowspan="2">{{$event}}</td>
                            <td rowspan="2">{{$userName }}</td>
                            <td rowspan="2">{{  $date }}</td>
                              <td><b>Old Value</b></td>
                                @if( $event == 'Updated')
                                <td>{{ (data_get($oldvalue, 'name')) ?? '' }}</td>
                                <td>{{ (data_get($oldvalue, 'email')) ?? '' }}</td>
                                <td>{{ (data_get($oldvalue, 'phone')) ?? '' }}</td>
                                <!-- <td>{{getsuperdepname(data_get($oldvalue, 'super_department'))}}</td> -->
                                <td>   
                                    @if(is_array($oldvalue) && array_key_exists('department_id', $oldvalue))
                                    @php 
                                    $newDepartmentIds = is_array($oldvalue['department_id']) ? $oldvalue['department_id'] : explode(',', $oldvalue['department_id']);
                                    $newDepartmentNames = [];
                                    foreach ($newDepartmentIds as $id) {
                                        $newDepartmentNames[] = getDepartmentName($id);
                                    }
                                    @endphp
                                    {{ implode(', ', $newDepartmentNames) }}
                                    @endif
                                </td> 
                                <td>{{ (data_get($oldvalue, 'employee_id')) ?? '' }}</td>
                                <td>{{ getRoleName(data_get($oldvalue, 'role_id')) ?? '' }}</td>
                                <td>@if (isset($oldvalue['status']))
                                    {{ $oldvalue['status'] == 0 ? 'Inactive' : 'Active' }}
                                    @endif
                                </td>
                                @else
                                <td colspan="8"></td>
                                @endif
                            
                          </tr>
                          <tr>
                                <td><b>New Value</b></td>
                                <td>{{ (data_get($newvalue, 'name')) ?? '' }}</td>
                                <td>{{ (data_get($newvalue, 'email')) ?? '' }}</td>
                                <td>{{ (data_get($newvalue, 'phone')) ?? '' }}</td>
                                <!-- <td>{{getsuperdepname(data_get($newvalue, 'super_department'))}}</td> -->
                                <td>   
                                    @if(is_array($newvalue) && array_key_exists('department_id', $newvalue))
                                    @php 
                                    $newDepartmentIds = is_array($newvalue['department_id']) ? $newvalue['department_id'] : explode(',', $newvalue['department_id']);
                                    $newDepartmentNames = [];
                                    foreach ($newDepartmentIds as $id) {
                                        $newDepartmentNames[] = getDepartmentName($id);
                                    }
                                    @endphp
                                    {{ implode(', ', $newDepartmentNames) }}
                                    @endif
                                </td> 
                                <td>{{ (data_get($newvalue, 'employee_id')) ?? '' }}</td>
                                <td>{{ getRoleName(data_get($newvalue, 'role_id')) ?? '' }}</td>
                                <td>@if (isset($newvalue['status']))
                                    {{ $newvalue['status'] == 0 ? 'Inactive' : 'Active' }}
                                    @endif
                                </td>                          
                            </tr>
                          </tbody>
                      @endforeach
                  </tbody>
              </table>
              <br>
              <div id="noMatchesMessage" style="display: none; text-align:center;">No matches found.</div>
              {{ $audits->links() }} 
            
          </div>
            
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
// $(document).ready(function() {
//     $("#myInput").on("keyup", function() {
//         var value = $(this).val();
//         var $tableRows = $("#myTable tr");
//         var $visibleRows = $tableRows.filter(function() {
//             return $(this).text().indexOf(value) > -1;
//         });
//         $tableRows.hide();
//         $visibleRows.show();
//         var noMatches = $visibleRows.length === 0;
//         if (noMatches) {
//             $("#noMatchesMessage").show();
//         } else {
//             $("#noMatchesMessage").hide();
//         }
//     });
// })

$(document).ready(function() {
    $('#myInput').on('keyup', function() {
        let searchValue = $(this).val().toLowerCase();
        let hasMatch = false;

        $('.audit-group').each(function() {
            let groupText = $(this).text().toLowerCase();
            if (groupText.indexOf(searchValue) > -1) {
                $(this).show();
                hasMatch = true;
            } else {
                $(this).hide();
            }
        });
        if (hasMatch) {
            $('#noMatchesMessage').hide();
        } else {
            $('#noMatchesMessage').show();
        }
    });
});
</script>
<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('admin/js/services.js')}}"></script>


@endpush