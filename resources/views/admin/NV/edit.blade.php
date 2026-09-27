
@extends('admin.layout.master', ['page_title' => 'Create NV'])

@push('styles')
    <link rel="stylesheet" href="{{asset('theme/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
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
              <li class="breadcrumb-item active">Update NV</li>
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
            
          <form class="form" id="update_nv_form" method="POST" action="{{ route('nvs.update', ['id' => $data->id]) }}">
                @csrf
             
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">New NV Details</h3>
                  </div>
                 
                
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  <input type="hidden" id="nv_id" value="{{ $data->id }}">
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
                            <option value="CAPEX" @if(old('budget_type', $data->budget_type) == 'CAPEX') selected @endif>CAPEX</option>
                            <option value="OPEX" @if(old('budget_type', $data->budget_type) == 'OPEX') selected @endif>OPEX</option>
                        </select>
                        <div class="common-error form-text budget_type_error"></div>
                        </div>

                        <div class="col-lg-4">
                        <label>Budgetary Provision<span class="mandatory_input">*</span></label>
                        <select class="form-control" id="budget_prov" name="budget_prov">
                            <option value="">-- Select Budgetary Provision --</option>
                            <option value="Approved" @if(old('budget_prov', $data->budgetary_provision) == 'Approved') selected @endif>Approved</option>
                            <option value="Additional" @if(old('budget_prov', $data->budgetary_provision) == 'Additional') selected @endif>Additional</option>
                        </select>
                        <div class="common-error form-text budget_prov_error"></div>
                        </div>
                        </div>

                        <div class="form-group row">
                        <div class="col-lg-4">
                        <label>Proposal Type<span class="mandatory_input">*</span></label>
                        <select class="form-control" id="prop_type" name="prop_type">
                            <option value="">-- Select Proposal Type --</option>
                            <option value="One Time" @if(old('prop_type', $data->proposal_type) == 'One Time') selected @endif>One Time</option>
                            <option value="Regular" @if(old('prop_type', $data->proposal_type) == 'Regular') selected @endif>Regular</option>
                        </select>
                        <div class="common-error form-text prop_type_error"></div>
                        </div>
                        <div class="col-lg-4">
                            <label>NV Type<span class="mandatory_input">*</span></label>
                            <select class="form-control" id="nv_type" name="nv_type">
                                <option value="">-- Select NV Type --</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" @if(old('nv_type', $nvType->id ?? '') == $service->id) selected @endif>{{ $service->name }}</option>
                                @endforeach
                            </select>
                            <div class="common-error form-text nv_type_error"></div>
                        </div>

                        <div class="col-lg-4">
                            <label>Fiscal Year<span class="mandatory_input">*</span></label>
                            <select class="form-control" id="fiscal_year" name="fiscal_year">
                                <option value="">-- Select Fiscal Year --</option>
                                <option value="2023-24" @if(old('fiscal_year', $data->fiscal_year) == '2023-24') selected @endif>2023-24</option>
                                <option value="2024-25" @if(old('fiscal_year', $data->fiscal_year) == '2024-25') selected @endif>2024-25</option>
                                <option value="2025-26" @if(old('fiscal_year', $data->fiscal_year) == '2025-26') selected @endif>2025-26</option>
                            </select>
                         </div>

               
                  </div>
               </div>
                </div>
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
          </div>
          <!-- /.col -->
        </div>
        </div>
        <!-- /.container-fluid -->
    </section>
@endsection

@push('script')
<script>
    var nvId = "{{ $data->id }}";
</script>
<script>
   $('#update_nv_form').on('submit', function(event) {
    event.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                // Show SweetAlert success message
                swal({
                    title: 'Success',
                    text: response.message,
                    icon: 'success',
                    type: 'success',
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    confirmButtonClass: "btn font-weight-bold btn-primary"
                }).then(function() {
                  
                  window.location.href = '/admin/needvalidation/list';
                    
                    
                });
             
            },
            error: function (xhr, status, error) {
                // Show SweetAlert error message
                swal({
                    title: 'Error',
                    text: xhr.responseJSON.message,
                    icon: 'error',
                });
            }
        });
    });
</script>
<script src="{{asset('theme/plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('admin/js/nv.js')}}"></script>

@endpush
