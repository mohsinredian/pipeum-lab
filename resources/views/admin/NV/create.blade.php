
@extends('admin.layout.master', ['page_title' => 'Create NV'])

@push('styles')
    <link rel="stylesheet" href="{{asset('theme/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
      select[readonly] {
              background: #eee;
              pointer-events: none;
              touch-action: none;
          }
          </style>
@endpush
@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Need Validation</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Create NV</li>
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
           
            <form class="form" id="create_nv_form">
                @csrf
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">New NV Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                        <div class="form-group row">
                        <div class="col-lg-4">
                      <label>Company Name<span class="mandatory_input">*</span></label>
                       <select readonly class="form-control" id="company_name" name="company_name">
                          <!-- <option value="">-- Select Company --</option> -->
                          @foreach ($divisions as $division)
                          <option value="{{ $division->id }}" >{{ $division->name }}</option>
                          @endforeach
                       </select>
                      <div class="common-error form-text company_name_error"></div>
                    </div>

                    <div class="col-lg-4">
                    <label>Budget Type<span class="mandatory_input">*</span></label>
                    <select class="form-control" id="budget_type" name="budget_type">
                        <option value="">-- Select Budget --</option>
                        <option value="CAPEX">CAPEX</option>
                        <option value="OPEX">OPEX</option>
                    </select>
                    <div class="common-error form-text budget_type_error"></div>
                  </div>
                  <input type="hidden" name="budget_prov" id="budget_prov" value="Approved">
                  <div class="col-lg-4">
                  <label >Fiscal Year<span class="mandatory_input">*</span></label>
                      <select class="form-control" id="fiscal_year" name="fiscal_year">
                      <option value="">-- Select Fiscal Year --</option>
                      <option value="{{$currentFinancialYear}}">{{$currentFinancialYear}}</option>
                      <option value="{{$nextFinancialYear}}" >{{$nextFinancialYear}}</option>
                      <option value="{{$nextToNextFinancialYear}}" >{{$nextToNextFinancialYear}}</option>
                      
                  </select>
                    <!-- <label>Budgetary Provision<span class="mandatory_input">*</span></label>
                    <select class="form-control" id="budget_prov" name="budget_prov">
                        <option value="">-- Select Budgetary Provision --</option>
                        <option value="Approved">Approved</option>
                        <option value="Additional">Additional</option>
                    </select>
                    <div class="common-error form-text budget_prov_error"></div> -->
                  </div>
                  </div>

                  <div class="form-group row  ">
                  <div class="col-lg-4">
                    <label>Proposal Type<span class="mandatory_input">*</span></label>
                     <select class="form-control" id="prop_type" name="prop_type">
                        <option value="">-- Select Proposal Type --</option>
                        <option value="One Time">One Time</option>
                        <option value="Regular">Regular</option>
                    </select>
                    <div class="common-error form-text prop_type_error"></div>
                    </div>
                
                  <div class="col-lg-4">
                    <label>NV Type<span class="mandatory_input" >*</span></label>
                    <select class="form-control" id="nv_type" name="nv_type">
                        <option value="">-- Select NV Type --</option>
                        @foreach ($services as $service)
                          <option value="{{ $service->id }}">{{ $service->name }}</option>
                          @endforeach
                    </select>
                    <div class="common-error form-text nv_type_error"></div>
                    </div>
                    <!-- <div class="col-lg-4">
                      <label >Fiscal Year<span class="mandatory_input">*</span></label>
                      <select class="form-control" id="fiscal_year" name="fiscal_year">
                      <option value="">-- Select Fiscal Year --</option>
                      <option value="{{$currentFinancialYear}}">{{$currentFinancialYear}}</option>
                      <option value="{{$nextFinancialYear}}" >{{$nextFinancialYear}}</option>
                      <option value="{{$nextToNextFinancialYear}}" >{{$nextToNextFinancialYear}}</option>
                      
                  </select>
                  
                    </div> -->
                    <select class="form-control" id="department_id" name="department_id" hidden>
                        @foreach ($departments as $department)
                          <option value="{{ $department->id }}">{{ $department->name }}</option>
                          @endforeach
                    </select>
                  </div>
               </div>
                </div>
               @if(!empty($capex->department_id))
                <input type="hidden" id="capex_depart" value="{{$capex->department_id}}">
                @endif
                @if(!empty($opex->department_id))
                <input type="hidden" id="opex_depart" value="{{$opex->department_id}}">
                @endif
                <div class="card">
                   <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                              <button type="submit" class="btn btn-success mr-2">Submit</button>
                              <button type="reset" class="btn btn-secondary" onclick="history.back();">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </form>

            <!-- <label>Upload .dwg (autocad file)</label>
              <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                        <input type="file" name="file" accept=".dwg" id="fileInput" class="form-control">
                        </div>
                        <div class="col-lg-3">
                        <button type="submit" class="btn btn-primary font-weight-bolder">Upload</button>
                        </div>
                    </div>
              </form>

              <div id="uploadedFileContainer" style="display: none; margin-top: 20px;">
                <p>Uploaded File: <a id="uploadedFileLink" href="#" target="_blank"></a></p>
                <button id="viewDwgButton" class="btn btn-info" style="display: none;">View DWG File</button>
              </div> -->

          </div>
          <!-- /.col -->
        </div>
        </div>
        <!-- /.container-fluid -->
    </section>
@endsection

@push('script')
<script src="{{asset('theme/plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('admin/js/nv.js')}}"></script>
<script src="{{ asset('theme/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('admin/js/brand.js') }}"></script>
<script>
  document.getElementById("uploadForm").addEventListener("submit", function(e) {
      e.preventDefault(); 

      let formData = new FormData(this);
      
      fetch("/admin/upload/dwg_file", {
          method: "POST",
          body: formData,
          headers: {
              "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
          }
      })
      .then(response => response.json())
      .then(data => {
          if (data.message) {
              Swal.fire({
                  title: "Success!",
                  text: data.message,
                  icon: "success",
                  confirmButtonText: "OK"
              });
              document.getElementById("fileInput").value = "";
              let fileContainer = document.getElementById("uploadedFileContainer");
              let fileLink = document.getElementById("uploadedFileLink");

              fileLink.href = "/uploads/" + data.file; 
              fileLink.innerText = data.file; 
              fileContainer.style.display = "block";
               
               // Set up "View DWG File" button
                viewDwgButton.style.display = "inline-block";
                viewDwgButton.onclick = function() {
                    window.open("https://www.dwgsee.com/online_viewer.html", "_blank");
                };
                //
          } else {
              Swal.fire({
                  title: "Error!",
                  text: data.error || "File upload failed",
                  icon: "error",
                  confirmButtonText: "Try Again"
              });
          }
      })
      .catch(error => {
          Swal.fire({
              title: "Error!",
              text: "Please select a valid .dwg File",
              icon: "error",
              confirmButtonText: "OK"
          });
      });
  });
</script>

@endpush
