@extends('admin.layout.master', ['page_title' => 'report'])
@push('styles')
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
    <style>
        *{
            font-family: 'Poppins', sans-serif;
        }
  .table td {
text-align: center;
} 
.table th {
text-align: center;
} 
</style>
   
@endpush
@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Report</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Reports</li>
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
                    <h3 class="card-label">Asset Register</h3>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive">
                <table id="reports_datatable" class="table table-bordered table-striped  table-responsive text-nowrap">
                  <thead>
                  <tr>
                    <th class="text-center" width="5%">S.No</th>
                    <th>Location</th>
                    <th>Make </th>
                    <th>Model</th>
                    <th>Device Type</th>
                    <th>Device Serial no.</th>
                    <th>Confidentiality Rating</th>
                    <th>Integrity Rating</th>
                    <th>Availablility Rating</th>
                    <th>Asset criticality Value</th>
                    <th>Overall Asset Value</th>
                  </tr>
                  </thead>
                  <tbody>
                     <?php
                    $i=1;
                   
                    ?>
                    
                    @foreach($inventories3 as $inventories)
                    @php 
                    $itemtype="";
                    $itype= $inventories->item_type;
                  if($itype=='1'||$itype=='2'||$itype=='3'){
                    $itemtype='Link Circuit ID';
                  }
                  if($itype=='4'||$itype=='7'){
                    $itemtype='ROUTER';
                  }
                  if($itype=='5'||$itype=='8'){
                    $itemtype='FIREWALL';
                  }
                  if($itype=='6'){
                    $itemtype='SWITCH';
                  }
                  
                    @endphp
                   <tr>
                      <td>{{$i}}</td>
                      <td>{{$inventories->locationname}}</td>
                      <td>{{$inventories->brandname}}</td>
                      <td>{{$inventories->model_number}}</td>
                      <td>{{$itemtype}}</td>
                      <td>{{$inventories->serial_number}}</td>
                      <td>{{$inventories->confidentiality_rating}}</td>
                      <td>{{$inventories->integrity_rating}}</td>
                      <td>{{$inventories->availability_rating}}</td>
                      <td>{{$inventories->asset_criticality}}</td>
                      <td>{{$inventories->overall_asset}}</td>
                    </tr>
                    <?php $i++;?>
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
    <section class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Work Order Status (Device)</h3>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive">
                <table id="reports_datatable1" class="table table-bordered table-striped  table-responsive text-nowrap">
                  <thead>
                  <tr>
                    <th class="text-center" width="5%">S.No</th>
                    <th>Vendor</th>
                    <th>WO No.</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Item Type</th>

                    <th>Item serial No</th>
                    <th>Alert (3 months before expiry)</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                    $i=1;
                   
                    ?>
                    @foreach($inventories4 as $inventories)
                    @php 
                    $itype= $inventories->item_type;
                  if($itype=='1'||$itype=='2'||$itype=='3'){
                    $itemtype='Link Circuit ID';
                  }
                  if($itype=='4'||$itype=='7'){
                    $itemtype='ROUTER';
                  }
                  if($itype=='5'||$itype=='8'){
                    $itemtype='FIREWALL';
                  }
                  if($itype=='6'){
                    $itemtype='SWITCH';
                  }
                  
                    @endphp
                   <tr>
                      <td>{{$i}}</td>
                      <td>{{$inventories->vendorsname}}</td>
                      <td>{{$inventories->po_number}}</td>
                      <td>{{$inventories->po_start}}</td>
                      <td>{{$inventories->po_expiry}}</td>
                      <td>{{$inventories->brandname}}</td>
                      <td>{{$inventories->model_number}}</td>
                      <td>{{$itemtype}}</td>
                      <td>{{$inventories->serial_number}}</td>
                      <td>{{null}}</td>
                    </tr>
                    <?php $i++;?>
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
    <section class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Work Order Status (Links)</h3>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive">
                <table id="reports_datatable2" class="table table-bordered table-striped  table-responsive text-nowrap">
                  <thead>
                  <tr>
                    <th class="text-center" width="5%">S.No</th>
                    <th>Vendor</th>
                    <th>WO No.</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                   
                    <th>Bandwidth</th>
                    <th>Circuit ID</th>
                    <th>Alert (3 months before expiry)</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                    $i=1;
                   
                    ?>
                    @foreach($inventories5 as $inventories)
                   <tr>
                      <td>{{$i}}</td>
                      <td>{{$inventories->vendorsname}}</td>
                      <td>{{$inventories->po_number}}</td>
                      <td>{{$inventories->po_start}}</td>
                      <td>{{$inventories->po_expiry}}</td>                      
                      <td>{{$inventories->model_number}}</td>
                      <td>{{$inventories->serial_number}}</td>
                      <td>{{"0"}}</td>
                    </tr>
                    <?php $i++;?>
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
    <section class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Location Wise Deployment (Issue / Return)</h3>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive">
                <table id="reports_datatable3" class="table table-bordered table-striped  table-responsive text-nowrap">
                  <thead>
                  <tr>
                    <th class="text-center" width="5%">S.No</th>
                    <th>Date</th>
                    <th>Issue / Return</th>
                    <th>Location</th>
                    <th>Device Make</th>
                    <th>Device Model</th>
                    <th>Device Type</th>
                    <th>Device Serial No</th>
                    <th>Link 1 ISP</th>
                    <th>Link 1 Circuit ID</th>
                    <th>Link 1 Bandwidth</th>
                    <th>Link 2 ISP</th>
                    <th>Link 2 Circuit ID</th>
                    <th>Link 2 Bandwidth</th>
                    
                  </tr>
                  </thead>
                  <tbody>
                    <?php                    
                     $i=1;
                   // echo $inventories6;
                     ?>
                     @foreach($inventories6 as $inventories)

                     @php
                      $itype= $inventories->item_type;
                  if($itype=='1'||$itype=='2'||$itype=='3'){
                    $itemtype='Link Circuit ID';
                  }
                  if($itype=='4'||$itype=='7'){
                    $itemtype='ROUTER';
                  }
                  if($itype=='5'||$itype=='8'){
                    $itemtype='FIREWALL';
                  }
                  if($itype=='6'){
                    $itemtype='SWITCH';
                  }
                  $link2isp="";
                  $link2circuit="";
                  $link2bandwidth="";
                    if($inventories->return_status=='2'){
                      $retunstatus="Issued";                     
                    }
                    if($inventories->return_status=='1'){
                      $retunstatus="Return";
                      $link2isp=$inventories->vendorsname;
                  $link2circuit=$inventories->serial_number;
                  $link2bandwidth=$inventories->model_number;
                    }
                    $issuedate=$inventories->created_at;
                    $doissue= explode(" ",$issuedate);
                     @endphp
                    <tr>
                      <td>{{$i}}</td>
                      <td>{{$doissue[0]}}</td>
                      <td>{{$retunstatus}}</td>
                      <td>{{getLocationName($inventories->location_id)}}</td>
                      <td>{{$inventories->brandname}}</td>
                      <td>{{$inventories->model_number}}</td>
                      <td>{{$itemtype}}</td>                      
                      <td>{{$inventories->serial_number}}</td>
                      <td>{{$inventories->vendorsname}}</td>
                      <td>{{$inventories->serial_number}}</td>
                      <td>{{$inventories->model_number}}</td>
                      <td>{{$link2isp}}</td>
                      <td>{{$link2circuit}}</td>
                      <td>{{$link2bandwidth}}</td>
                      
                    </tr> 
                    <?php $i++;?>
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
<script src="{{asset('theme/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('theme/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
  <script src="{{asset('theme/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
  <script src="{{asset('theme/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
  <script src="{{asset('theme/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
<!-- <script src="{{asset('admin/js/reports.js')}}"></script> -->
<script>
    $(function() {
      $("#reports_datatable").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": true,
        "info": false,
        "responsive": false,
        // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        "buttons": ["csv","excel"]
      }).buttons().container().appendTo('#reports_datatable_wrapper .col-md-6:eq(0)');
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
  <script>
    $(function() {
      $("#reports_datatable1").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": true,
        "info": false,
        "responsive": false,
        // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        "buttons": ["csv", "excel"]
      }).buttons().container().appendTo('#reports_datatable1_wrapper .col-md-6:eq(0)');
      $('#example3').DataTable({
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
  <script>
    $(function() {
      $("#reports_datatable2").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": true,
        "info": false,
        "responsive": false,
        // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        "buttons": ["csv", "excel"]
      }).buttons().container().appendTo('#reports_datatable2_wrapper .col-md-6:eq(0)');
      $('#example4').DataTable({
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

<script>
    $(function() {
      $("#reports_datatable3").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": true,
        "info": false,
        "responsive": false,
        // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        "buttons": ["csv","excel"]
      }).buttons().container().appendTo('#reports_datatable3_wrapper .col-md-6:eq(0)');
      $('#example5').DataTable({
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

@endpush