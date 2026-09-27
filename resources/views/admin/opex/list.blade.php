@extends('admin.layout.master', ['page_title' => 'Location'])
@push('styles')
<style>

  #opex_table_length {

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
        <h1 class="m-0">Manage OPEX</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
          <li class="breadcrumb-item active">OPEX</li>
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
              <h3 class="card-label">OPEX</h3>
            </div>
            @if (\Auth::user()->isA('Admin'))
            <div class="card-toolbar">
           
             

              <!-- Upload File Button -->
              <!-- <form id="upload-form" enctype="multipart/form-data" style="display: inline-block;">
                @csrf
                <label for="file-upload" class="btn btn-secondary font-weight-bolder m-0">
                  <i class="fas fa-file-upload mr-1"></i>
                  Upload File
                </label>
                <input id="file-upload" type="file" name="file" style="display:none;">
              </form> -->
             

            {{-- <a href="#" class="btn btn-primary font-weight-bolder" id="prevYear">
                <i class="fa fa-chevron-left mr-1"></i> Previous
            </a>
            <a href="#" class="btn btn-primary font-weight-bolder" id="nextYear">
                <i class="fa fa-chevron-right mr-1"></i> Next
            </a> --}}

            <!-- <a href="?prev" class="btn btn-primary">Previous</a>
            <a href="?next" class="btn btn-primary">Next</a> -->
           
              <a href="/admin/opex/download-excel" class="btn btn-primary font-weight-bolder" target="_blank">
                <i class="fas fa-file-download mr-1"></i> Excel</a>
              <a href="/admin/opex/create" class="btn btn-primary font-weight-bolder">
                <i class="fa fa-plus-circle mr-1"></i>
                Add OPEX
              </a>


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
            <table id="opex_table" class="table table-bordered mt-3" >
              <thead>
                  <tr>
                      <th class="text-center" width="5%">S.No</th>
                      <th>Department</th>
                      <th>Sub-Department</th>
                      <th>Expense Head</th>
                      <th>Activity</th>
                      <?php
                        $activityMapping = [
                          '0' => 'Activity 1',
                          '1' => 'Activity 2',
                          '2' => 'Activity 3'
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
                 
                     
                      @for ($i = 0; $i < $numFields; $i++)
                          <th>Initial Approved Budget FY {{ $startDisplayYear + $i }}-{{ $startDisplayYear + $i + 1 }}</th>
                      @endfor

                     
                      <th>Action</th>
                  </tr>
              </thead>
              <tbody>
                  @foreach($opex as $key => $value)
                  <tr>
                      <td>{{$key+1}}</td>
                      <td>{{getsuperdepname($value->super_department)}}</td> 
                      <td>{{getDepartmentName($value->department_id)}}</td>
                      <td>{{$value->expenses_head}}</td>
                      <td>{{ $activityMapping[$value->activity] ?? ' ' }}</td>
         
                      @php
                      $year = json_decode($value->initial_approved_budget, true);
                 
                      if (is_array($year)) {
                          $year_count = count($year);
                      } else {
                          $year_count = 0;
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
                  @endfor
                 
                      <td>
                          <a href="{{ route('opex.edit', $value->id) }}" class="btn btn-sm btn-clean btn-icon" title="Edit">
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
<script src="{{asset('admin/js/opex.js')}}"></script>

<script>

  $(document).ready(function() {

  $('#opex_table').DataTable({

  "paging": true,

  "pageLength": 10, // number of rows per page

  "lengthMenu": [

  [10, 25, 50, -1],

  [10, 25, 50, "All"]

  ]

  });

  });

</script>
<script>
  var start_year = 2023;

 function showPreviousYearData() {
 
            currentYear--;
            updateTable(currentYear);
        }

        // Function to show the next year's data
        function showNextYearData() {
            currentYear++;
            updateTable(currentYear);
        }

       
        function updateTable(year) {
           
            var startYear = year - 1;
            var endYear = year;
       
            var tableHeaders = document.querySelectorAll("#opex_table th");
            tableHeaders.forEach(function (header, index) {
                if (index >= 4 && index < tableHeaders.length - 1) {
                    header.textContent = "Initial Approved Budget FY " + (startYear + (index - 4)) + "-" + (startYear + (index - 3));
                }
            });

           
        }
</script>

@endpush