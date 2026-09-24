@extends('admin.layout.master', ['page_title' => 'Edit Employee'])

@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Employees</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Employees</li>
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
            
            <form class="form" id="edit_otp_form">
                @csrf
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Manage OTP</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                        <div class="form-group row">
                          {{-- <input type="hidden" name="user_id" id="user_id" value="{{$data->user_id}}"> --}}
                           
                             <div class="col-lg-4">
                              <Label >OTP Status</Label><br>
                                
                                    <input type="radio"  name="otp_status" id="otp_status1" value="1" {{$data->otp_status == 1? "checked" : "" }}>
                                    <label for="Enable">Enable</label><br>
                                    <input type="radio"  name="otp_status" id="otp_status2" value="2" {{$data->otp_status == 2? "checked" : "" }}>
                                    <label for="Disable">Disable</label>
                              
                                <div class="common-error form-text email_error"></div>
                            </div>
                            
                        </div>
                    </div>
                
                <div class="card">
                        
                   <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                              <button type="submit" class="btn btn-success ">Update</button>
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
    $('#edit_otp_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_otp_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_otp_form')[0]),
				type: 'post',
				url: "/admin/otp/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "otp updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/dashboard';
						});
						
						$('#edit_otp_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_otp_form').find('.'+key+'_error').html(error_msgs[key][0]);
							}
						}

					}
					else if(res.result == 'failure'){
						
						Swal.fire({
			                text: "Something went wrong. Please try again.",
			                type: 'error',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-light"
			            }).then(function() {
							window.location.reload();
						});
					}

					$('.pre-loader').hide();
				},

				error: function(error){

				}
			});
		}
	});
</script>
@endpush
