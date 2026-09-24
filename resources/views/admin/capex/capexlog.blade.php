@extends('admin.layout.master', ['page_title' => 'CapexLog'])
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
            <h1 class="m-0">Manage CAPEX Log</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">CAPEX Log</li>
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
                    <h3 class="card-label">CAPEX Log</h3>
                </div>
               
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive">
              <div style="text-align-last: end;">
              <a href="/admin/capexmaster/logdownload-excel" class="btn btn-primary font-weight-bolder" target="_blank" style="float:left;margin-right: 6px;">
                <i class="fas fa-file-download mr-1"></i> Excel</a>
              <!-- <a href="?prev" class="btn btn-primary" style="float:left; margin-right: 6px;"><b>Previous</b></a> &nbsp;
              <a href="?next" class="btn btn-primary" style="float:left;"><b>Next</b></a> -->
                Search: <input id="myInput" type="text" style="text-align-last: start;">
            </div>
              <table id="" class="table table-bordered mt-3">
                  <thead class="text-center">
                      <tr>
                            <th class="text-center" width="5%">S.No</th>
                            <th class="text-center" width="5%">Event</th>
                            <th class="text-center" width="6%">Audited By</th>
                            <th class="text-center" width="7%">Audited At</th>
                            <th width="7%"></th>
                            <th class="text-center" width="13%">Department</th>
                            <th class="text-center" width="10%">Sub-Department</th>
                            <th class="text-center" width="5%">Head</th>
                            <th class="text-center" width="5%">Sub Head</th>
                            <th class="text-center" width="5%">BPR Head</th>
                            <th class="text-center" width="8%">Regular CAPEX/Project CAPEX</th>

                            <?php
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

                          $startYear = date('Y');
                          $currentYear = date('Y');
                          $endYear = $currentYear + 2;
                          $numFields = $endYear - $startYear + 1; 
                          $currentDisplayYear = $currentYear;
                          $startDisplayYear = $currentYear;
              
                          if (isset($_GET['prev'])) {
                              $currentDisplayYear--;
                              $startDisplayYear--;
                          } elseif (isset($_GET['next'])) {
                              $currentDisplayYear++;
                              $startDisplayYear++;
                          }
    
                          $endDisplayYear = $startDisplayYear + $numFields - 1;
    
                            if ($currentDisplayYear < $startYear) {
                                $currentDisplayYear = $startYear;
                                $startDisplayYear = $currentYear - 1;
                            }
    
                            if ($endDisplayYear > $endYear) {
                                $endDisplayYear = $endYear;
                            }
                          ?>
                    @for ($i = 0; $i <  $numFields; $i++)
                    <th class="text-center">CAPEX FY {{ $startDisplayYear + $i }}-{{ $startDisplayYear + $i + 1 }} (W/O OH & INT)</th>
                    <th class="text-center">CAPEX FY {{ $startDisplayYear + $i }}-{{ $startDisplayYear + $i + 1 }} (With OH & INT)</th>
                    @endfor
                      </tr>
                  </thead>

                  <tbody id="myTable">
                        @foreach($audits as $key => $audit)
                            @php
                            $newvalue =  $audit->new_values ?? [];
                            $oldvalue =  $audit->old_values ?? [];
                            $service = App\Models\Service::find($audit->auditable_id);
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
                                <td>{{getsuperdepname(data_get($oldvalue, 'super_department')) ?? '' }}</td>
                                <td>{{getDepartmentName(data_get($oldvalue, 'department_id')) ?? '' }}</td>
                                <td>@if (isset($oldvalue['head']))
                                   {{ $headMapping[$oldvalue['head']] ?? ' ' }}   
                                    @endif
                                </td>
                                <td>{{(data_get($oldvalue, 'sub_head')) ?? '' }}</td>
                                <td>@if (isset($oldvalue['brp_head']))
                                    {{ $brpHeadMapping[$oldvalue['brp_head']] ?? ' ' }}  
                                    @endif
                                </td>
                                <td>@if (isset($oldvalue['status']))
                                    {{ $oldvalue['status'] == 1 ? 'Project' : 'Regular' }}
                                    @endif
                                </td> 
                                    @php
                                    $capx_fy_one =  data_get($oldvalue, 'revised_budget');
                                    $year = explode(',',$capx_fy_one);
                                    
                                    if (is_array($year)) {
                                        $year_count = count($year);
                                    } else {
                                        $year_count = 0; 
                                    }

                                    $capx_fy_two =  data_get($oldvalue, 'capx_fy_two');
                                    $year_two = json_decode($capx_fy_two, true);
                            
                                    if (is_array($year_two)) {
                                        $year_count_two = count($year_two);
                                    } else {
                                        $year_count_two = 0; 
                                    }
                                    @endphp
                                @for ($i = 0; $i < $numFields; $i++)
                                <td>
                                    @if (isset($year[$i + ($currentDisplayYear - $startYear)]))
                                        <script>
                                            var number = {{ $year[$i + ($currentDisplayYear - $startYear)] }};
                                            var formattedNumber = new Intl.NumberFormat('en-IN').format(number);
                                            document.write(formattedNumber);
                                        </script>
                                    @else
                                        0
                                    @endif
                                </td>

                                <td>
                                @if (isset($year_two[$i + ($currentDisplayYear - $startYear)]))
                                        <script>
                                            var number = {{ $year_two[$i + ($currentDisplayYear - $startYear)] }};
                                            var formattedNumber = new Intl.NumberFormat('en-IN').format(number);
                                            document.write(formattedNumber);
                                        </script>
                                    @else
                                        0
                                    @endif
                                </td>
                                @endfor
                                    @else
                                    <td colspan="12"></td>
                                    @endif
                            </tr>
                            <tr>
                              <td><b>New Value</b></td>
                              <td>{{getsuperdepname(data_get($newvalue, 'super_department')) ?? '' }}</td>
                              <td>{{getDepartmentName(data_get($newvalue, 'department_id')) ?? '' }}</td>
                              <td>@if (isset($newvalue['head']))
                                 {{ $headMapping[$newvalue['head']] ?? ' ' }}  
                                    @endif
                                </td>
                              <td>{{(data_get($newvalue, 'sub_head')) ?? '' }}</td>
                              <td>@if (isset($newvalue['brp_head']))
                                  {{ $brpHeadMapping[$newvalue['brp_head']] ?? ' ' }}  
                                   @endif
                                </td>
                              <td>@if (isset($newvalue['status']))
                                    {{ $newvalue['status'] == 1 ? 'Project' : 'Regular' }}
                                    @endif
                                </td> 
                                @php
                                $capx_fy_one =  data_get($newvalue, 'revised_budget');
                                $year = explode(',',$capx_fy_one);

                                if (is_array($year)) {
                                    $year_count = count($year);
                                } else {
                                    $year_count = 0; 
                                }

                                $capx_fy_two =  data_get($newvalue, 'capx_fy_two');
                                $year_two = json_decode($capx_fy_two, true);
                        
                                if (is_array($year_two)) {
                                    $year_count_two = count($year_two);
                                } else {
                                    $year_count_two = 0; 
                                }
                                @endphp
                          @for ($i = 0; $i < $numFields; $i++)
                          <td>
                            @if (isset($year[$i + ($currentDisplayYear - $startYear)]))
                                <script>
                                    var number = {{ $year[$i + ($currentDisplayYear - $startYear)] }};
                                    var formattedNumber = new Intl.NumberFormat('en-IN').format(number);
                                    document.write(formattedNumber);
                                </script>
                            @else
                                0
                            @endif
                        </td>

                        <td>
                          @if (isset($year_two[$i + ($currentDisplayYear - $startYear)]))
                                 <script>
                                    var number = {{ $year_two[$i + ($currentDisplayYear - $startYear)] }};
                                     var formattedNumber = new Intl.NumberFormat('en-IN').format(number);
                                     document.write(formattedNumber);
                                 </script>
                             @else
                                 0
                             @endif
                         </td>
                        @endfor
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