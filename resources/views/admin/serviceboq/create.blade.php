@extends('admin.layout.master', ['page_title' => 'Create Service'])


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage Service BOQ</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/admin/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Service BOQ</li>
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
                  {{-- @if (Session::has('message'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ Session::get('message') }}
                        </div>
                        @endif 
                  @if ($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    {{ $error }}
                                </div>
                            @endforeach
                        @endif
                        @if (Session::has('err'))
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                {{ Session::get('err') }}
                            </div>
                        @endif --}}
                        <form class="form" id="yourFormID" action="{{ url('admin/serviceboq/store') }}" method="POST">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">New Service BOQ Details</h3>
                                </div>
                                <!-- /.card-header -->
                                <!--begin::Form-->

                                <div class="card-body">
                                    <div class="form-group row">

                                        <div class="col-lg-4">
                                            <label>Activity <span class="mandatory_input">*</span></label>
                                            <input type="number" class="form-control" id="activity" name="activity" value="{{ old('activity') }}"
                                                placeholder="Enter Activity" onkeypress="return isNumberKey(event)" oninput="validateInput(this)">
                                                <div class="common-error form-text" id="activity_error"></div>
                                        </div>

                                        <div class="col-lg-4">
                                            <label>UoM<span class="mandatory_input">*</span></label>
                                            <input type="text" class="form-control" id="bun" name="bun" value="{{ old('bun') }}"
                                                placeholder="Enter UoM">
                                            <div class="common-error form-text" id="bun_error"></div>
                                        </div>

                                        <div class="col-lg-4">
                                            <label>Service Short Text<span class="mandatory_input">*</span></label>
                                            <input type="text" class="form-control" id="service_short_text" value="{{ old('service_short_text') }}"
                                                name="service_short_text" placeholder="Enter Service Short Text">
                                            <div class="common-error form-text" id="service_short_text_error"></div>
                                        </div>
                                        <div class="col-lg-4">
                                            <label>Rate<span class="mandatory_input">*</span></label>
                                            <input type="number" class="form-control decimal" id="rate_ser" value="{{ old('rate_ser') }}"
                                                name="rate_ser" placeholder="Enter Rate">
                                            <div class="common-error form-text" id="rate_ser_error"></div>
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
                                            <button type="submit" id="submitBtn" class="btn btn-success">Submit</button>
                                            <button type="reset" class="btn btn-secondary"
                                                onclick="history.back();">Cancel</button>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   
  
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        $(document).ready(function() {
            $("#submitBtn").click(function(e) {
                e.preventDefault(); 
        
                var valid = true;
                $(".common-error").text(""); 
        
                // Activity field
                if ($("#activity").val() === "") {
                    $("#activity_error").text("Activity is required");
                    valid = false;
                } else {
                    $("#activity_error").text(""); 
                }
        
                // UoM field
                if ($("#bun").val() === "") {
                    $("#bun_error").text("UoM is required");
                    valid = false;
                } else {
                    $("#bun_error").text(""); 
                }
        
                // Service Short Text field
                if ($("#service_short_text").val() === "") {
                    $("#service_short_text_error").text("Service Short Text is required");
                    valid = false;
                } else {
                    $("#service_short_text_error").text(""); 
                }
        
                // Rate field
                if ($("#rate_ser").val() === "") {
                    $("#rate_ser_error").text("Rate is required");
                    valid = false;
                } else {
                    $("#rate_ser_error").text(""); 
                }
        
                if (valid) {
                    
                Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Form submitted successfully!',
                buttonsStyling: false,
                confirmButtonText: "Ok",
                confirmButtonClass: "btn font-weight-bold btn-primary"
            }).then(function() {
                $("#yourFormID").submit();
                // window.location = '/admin/serviceboq';
            });
           
        }
    });
        
           
            $("#activity").on("input", function() {
                $("#activity_error").text("");
            });
        
            $("#bun").on("input", function() {
                $("#bun_error").text("");
            });
        
            $("#service_short_text").on("input", function() {
                $("#service_short_text_error").text("");
            });
        
            $("#rate_ser").on("input", function() {
                $("#rate_ser_error").text("");
            });
        });

        $(document).on('keydown', '.decimal', function(e) {
        var type = $(this).hasClass('decimal') ? 'decimal' : '';
        var key = e.key;
        var isCtrlV = (e.ctrlKey && (key === 'v' || key === 'V'));
        var isSelectAll = (key === "a" || key === 'A') && e.ctrlKey;
        var isTab = key === "Tab" || (key === "Tab" && e.shiftKey);
        var isReload = (key === "R" || key === "r" || key === "F5") && e.ctrlKey;
        var keys = ["Del", "Delete", "Backspace", "Home", "End", "Up", "Down", "Left", "Right", "ArrowUp", "ArrowDown", "ArrowLeft", "ArrowRight", ",", ".", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
 
        if (isTab || isReload || isSelectAll || isCtrlV) {
            return true;
        }
 
        switch(type) {
            case 'decimal':
                var invalidKey = $.inArray(key, keys) === -1;
                break;
           
                invalidKey = $.inArray(key, keys) === -1;
        }
 
        if (invalidKey) {
            e.preventDefault();
        }
    });
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
    function validateInput(input) {
        input.value = input.value.replace(/[^\d]/g, '');
    }
    </script>
@endpush
