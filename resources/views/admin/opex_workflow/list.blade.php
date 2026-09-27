@extends('admin.layout.master', ['page_title' => 'Create workflow'])
@push('styles')
    <link rel="stylesheet" href="{{ asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush
@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage OPEX Workflow</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">OPEX Workflow</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">OPEX Workflow</h3>
                </div>
                @php 
                    $hasRecords = DB::table('capex_workflows_status')->exists();

                   $allApproved = ! $hasRecords;

                    if ($hasRecords) {
                        $lastStages = DB::table('capex_workflows_status as c1')
                            ->select('c1.nv_id', 'c1.sr_no', 'c1.reviewer_name', 'c1.nv_stage_status')
                            ->whereRaw('c1.sr_no = (
                                SELECT MAX(c2.sr_no) FROM capex_workflows_status c2 WHERE c2.nv_id = c1.nv_id
                            )')
                            ->where('nv_budget_type', 'OPEX')
                            ->get();

                        $allApproved = $lastStages->every(function ($stage) {
                            return $stage->reviewer_name === 'approver' && $stage->nv_stage_status == 1;
                        });
                    }
                @endphp
                @if (\Auth::user()->isA('Admin'))
                <div class="card-toolbar">
                <a href="/admin/opex_Workflow/download-excel" class="btn btn-primary font-weight-bolder" target="_blank">
                        <i class="fas fa-file-download mr-1"></i> Excel</a>
                    @if($allApproved)
                      <a href="/admin/opex_Workflow/create" class="btn btn-primary font-weight-bolder">
                          <i class="fa fa-plus-circle mr-1"></i>
                          Add Stage</a>
                    @endif
                </div>
                @endif
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive">
                <table id="workflow_datatable" class="table table-bordered">
                  <thead class="text-center">
                  <tr>
                    <th>S.No</th>
                    <th>Id</th>
                    <th>Sub-Department</th>
                    <th>Reviewer 1  </th>
                    <th>Reviewer 2  </i></span></th>
                    <th>Reviewer 3  </i></span></th>
                    <th>Reviewer 4  </i></span></th>
                    <th>Approver</th>
                    <th>Stage Serial No.</th>
                    <th>Status</th>
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
    @endsection
    @push('script')
    <script src="{{ asset('theme/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{asset('admin/js/opex_workflow.js')}}"></script>
    <script>
  
      $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
      </script>
    @endpush