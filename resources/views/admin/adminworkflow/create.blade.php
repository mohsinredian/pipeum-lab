@extends('admin.layout.master', ['page_title' => 'Create Festival'])


@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Notes Workflow</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Notes Workflow</li>
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
            
            <form class="form" id="create_newworkflow_form" method="POST" action="{{route('admin.store_notesworkflows')}}">
                @csrf
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">New Note Workflow</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                        <div class="form-group row">
                            
                            <div class="col-lg-6">
                                <label>Workflow Name <span class="mandatory_input">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Workflow Name" value="{{old('name')}}">
                                @error('name')
                                <div class="common-error form-text status_error">{{ $message }}</div>
                              @enderror
                            </div>
                            
                            <div class="col-lg-4">
                                <label>Status <span class="mandatory_input">*</span></label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                   <option value="1">Active</option>
                                   <option value="0">Inactive</option>
                                </select>
                                @error('status')
                                <div class="common-error form-text status_error">{{ $message }}</div>
                                @enderror
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
  $(document).ready(function() {
      $('#create_newworkflow_form').submit(function(event) {
          event.preventDefault(); // Prevent default form submission
          $.ajax({
              url: $(this).attr('action'),
              type: 'POST',
              data: new FormData(this),
              processData: false,
              contentType: false,
              success: function(response) {
                  var res = response;
                  if (res.result == 'success') {
                      Swal.fire({
                          text: "Workflow created.",
                          type: 'success',
                          buttonsStyling: false,
                          confirmButtonText: "Ok",
                          confirmButtonClass: "btn font-weight-bold btn-primary"
                      }).then(function() {
                          window.location = '/admin/notesworkflows';
                      });

                      $('#create_newworkflow_form')[0].reset();
                  }
              }
          });
      });
  });
</script>
@endpush