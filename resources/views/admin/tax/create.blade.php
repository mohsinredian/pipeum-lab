@extends('admin.layout.master', ['page_title' => 'Create Tax'])
@push('styles')
<style>
  #tax_error-error{
  color: red !important;
}
</style>
@endpush
@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Tax</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Tax</li>
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
            
            <form class="form" id="create_tax_form">
                @csrf
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">New Tax Details</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                        <div class="form-group row">
                            

                            <div class="col-lg-4 mt-2">
                                <label>Tax (in %) <span class="mandatory_input">*</span></label>
                              <input class="form-control" type="text" Placeholder="Enter Tax (in %)" name="tax" id="tax">
                                <div class="common-error form-text tax_error" ></div>
                            </div>
                            <div class="col-lg-4 mt-2">
                              <label>Status <span class="mandatory_input">*</span></label>
                              <select class="form-control" id="status" name="status">
                                  <option value="">Select Status</option>
                                 <option value="1">Active</option>
                                 <option value="0">Inactive</option>
                              </select>
                              <div class="common-error form-text status_error"></div>
                         </div>
                        </div>
                    </div>
                  <!--end::Form-->
                  <!-- /.card-body -->
                </div>
                
                <div class="card">
                   <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                              <button type="submit" class="btn btn-success">Submit</button>
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

<script src="{{asset('admin/js/tax.js')}}"></script>

<script>
    function restrictInputToOneDecimal(event) {
        var inputValue = event.target.value;
        if ((inputValue.match(/\./g) || []).length > 1) {
            var lastDotIndex = inputValue.lastIndexOf('.');
            inputValue = inputValue.substring(0, lastDotIndex) + inputValue.substring(lastDotIndex + 1);
        }
        var sanitizedValue = inputValue.replace(/[^\d.]/g, '');
        event.target.value = sanitizedValue;
    }

    // var cgstInput = document.getElementById('cgst');
    var taxInput = document.getElementById('tax');

    // cgstInput.addEventListener('input', restrictInputToOneDecimal);
    taxInput.addEventListener('input', restrictInputToOneDecimal);
</script>

@endpush