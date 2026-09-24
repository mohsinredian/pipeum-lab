@extends('admin.layout.master', ['page_title' => 'Services'])
@push('styles')
<style>

  #capex_table_length {

  display: none;

  }

</style>
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
@endpush
@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage CAPEX</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">CAPEX</li>
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
                    <h3 class="card-label">CAPEX</h3>
                </div>
               
                @if (\Auth::user()->isA('Admin'))
                    <div class="card-toolbar">
                     
                        <!-- Upload File Button -->
                        <!-- <form id="upload-form" action="{{ route('capex.upload') }}" method="POST" enctype="multipart/form-data" style="display:inline-block;">
                            @csrf
                            <label for="file-upload" class="btn btn-secondary font-weight-bolder m-0">
                                <i class="fas fa-file-upload mr-1"></i> Upload File
                            </label>
                            <input id="file-upload" type="file" name="file" style="display:none;">
                        </form> -->

                        <!-- <a href="?prev" class="btn btn-primary">Previous</a>
                        <a href="?next" class="btn btn-primary">Next</a> -->

                        <a href="/admin/capexmaster/download-excel" class="btn btn-primary font-weight-bolder" target="_blank">
                        <i class="fas fa-file-download mr-1"></i> Excel</a>
                        <a href="/admin/capexmaster/create" class="btn btn-primary font-weight-bolder">
                          <i class="fa fa-plus-circle mr-1"></i>
                          Add CAPEX</a>
                    </div>
                @endif
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive">
              <div style="text-align-last: end;">
             
              <a href="?prev" class="btn btn-primary" style="float:left; margin-right: 6px;"><b>Previous-FY</b></a> &nbsp;
              <a href="?next" class="btn btn-primary" style="float:left;"><b>Next-FY</b></a>
                  <!-- Search: <input id="myInput" type="text" style="text-align-last: start;"> -->
              </div>
                <table id="capex_table" class="table table-bordered mt-3">
                  <thead class="text-center">
                  <tr>
                    <th class="text-center" width="5%">S.No</th>
                   
                    <th class="text-left">Department</th>
                    <th class="text-left">Sub-Department</th>
                    <th class="text-left">Head</th>
                    <th class="text-left">Sub Head</th>
                    <th class="text-left">BPR Head</th>
                    <th class="text-left">Regular CAPEX/Project CAPEX</th>


                      <?php
                        // Map for head, brp_head, and status values to avoid if-else checks
                          $headMapping = [
                            '0' => 'Load Growth',
                            '1' => 'System Improvement',
                            '2' => 'Statutory Requirement',
                            '3' => 'Infrastructure',
                            '4' => 'Technology',
                            '5' => 'Deposit',
                            '6' => 'Overheads & Interest'
                        ];

                        $brpHeadMapping = [
                            '0' => 'Performance Obligation',
                            '1' => 'Power Reliability',
                            '2' => 'Infrastructure Development'
                        ];

                        $statusMapping = [
                            '0' => 'Regular',
                            '1' => 'Project'
                        ];
                      session_start();

                      $currentDate = \Carbon\Carbon::now();

                      if ($currentDate->month >= 4) {
                          $financialYearStart = \Carbon\Carbon::create($currentDate->year, 4, 1);
                      } else {
                          $financialYearStart = \Carbon\Carbon::create($currentDate->year - 1, 4, 1);
                      }
                      $financialYearEnd = $financialYearStart->copy()->addYear()->subDay();
                      $currentFinancialYear = $financialYearStart->format('Y');
                     

                      $startYear = $currentFinancialYear;
                      $currentYear = $currentFinancialYear;
                      $endYear = $currentYear + 2;
                      $numFields = $endYear - $startYear + 1;
                 
                      if (!isset($_SESSION['startDisplayYear']) || isset($_GET['reset'])) {
                          $_SESSION['startDisplayYear'] = $currentYear - 1;
                      }
                 
                      $startDisplayYear = $_SESSION['startDisplayYear'];
                 
                      if (isset($_GET['prev'])) {
                          $startDisplayYear--;
                      } elseif (isset($_GET['next'])) {
                          $startDisplayYear++;
                      }
                 
                      $endDisplayYear = $startDisplayYear + $numFields - 1;
                 
                      if ($startDisplayYear < $startYear) {
                          $startDisplayYear = $startYear;
                      }
                 
                      if ($endDisplayYear > $endYear) {
                          $endDisplayYear = $endYear;
                      }
                 
                      $_SESSION['startDisplayYear'] = $startDisplayYear;


                          ?>
                    @for ($i = 0; $i <  $numFields; $i++)
                    <th class="text-left">CAPEX FY {{ $startDisplayYear + $i }}-{{ $startDisplayYear + $i + 1 }} (W/O OH & INT)</th>
                    <th class="text-left">CAPEX FY {{ $startDisplayYear + $i }}-{{ $startDisplayYear + $i + 1 }} (With OH & INT)</th>
                    @endfor
                    <th>Action</th>
                   
                  </tr>
                  </thead>

                  <tbody>
                    @foreach($capexlist as $key => $value)
               
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{getsuperdepname($value->super_department)}}</td>
                        <td>{{getDepartmentName($value->department_id)}}</td>
                        <td>{{ $headMapping[$value->head] ?? ' ' }}</td>
                        <td>{{ $value->sub_head }}</td>
                        <td>{{ $brpHeadMapping[$value->brp_head] ?? ' ' }}</td>
                        <td>{{ $statusMapping[$value->status] ?? ' ' }}</td>
                        @php
                            $year = json_decode($value->capx_fy_one, true);
               
                            if (is_array($year)) {
                                $year_count = count($year);
                            } else {
                                $year_count = 0;
                            }
                     
                       $year_two = json_decode($value->capx_fy_two, true);
           
                       if (is_array($year_two)) {
                           $year_count_two = count($year_two);
                       } else {
                           $year_count_two = 0;
                       }
                   @endphp
                     

                     @for ($i = 0; $i < $numFields; $i++)
                        <td>
                            @if (isset($year[$i + ($startDisplayYear - $startYear)]))
                                <script>
                                    var number = {{ $year[$i + ($startDisplayYear - $startYear)] }};
                                    var formattedNumber = new Intl.NumberFormat('en-IN').format(number);
                                    document.write(formattedNumber);
                                </script>
                            @else
                                0
                            @endif
                        </td>
                     
                 

                   
                         <td>
                          @if (isset($year_two[$i + ($startDisplayYear - $startYear)]))
                                 <script>
                                    var number = {{ $year_two[$i + ($startDisplayYear - $startYear)] }};
                                     var formattedNumber = new Intl.NumberFormat('en-IN').format(number);
                                     document.write(formattedNumber);
                                 </script>
                             @else
                                 0
                             @endif
                         </td>
                     @endfor

                        <td>
                          <a href="{{ route('capex.edit', $value->id) }}" class="btn btn-sm btn-clean btn-icon" title="Edit">
                            <i class="fas fa-edit text-info"></i>
                        </a>
                      </td>
                     
                    </tr>
                   
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
<script src="{{asset('theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('theme/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('admin/js/capex.js')}}"></script>

<script>

  $(document).ready(function() {

  $('#capex_table').DataTable({

  "paging": true,

  "pageLength": 10, // number of rows per page

  "lengthMenu": [

  [10, 25, 50, -1],

  [10, 25, 50, "All"]

  ]

  });

  });

</script>

@endpush
