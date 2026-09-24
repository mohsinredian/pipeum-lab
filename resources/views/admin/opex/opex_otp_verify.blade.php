@extends('admin.layout.master', ['page_title' => 'Services'])

@push('styles')

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


                    <div class="card-header" >

                        <div class="card-title">


                            <h5 class="card-label" style="color:white;">OPEX OTP</h5>


                        </div>


                    </div>





                    <div class="card-body body-form">

                        <div>

                            <form id="otpVery" class="shadow-lg" method="POST" action="{{ route('verify-otp') }}">
                                @csrf
                                <h5>Verification code was sent <span style="color: rgb(3, 142, 220);"> successfully ! </span></h5>

                                {{-- <input type="number" class="form-control" placeholder="Enter OTP"> --}}

                                <div class="input-group mb-4">

                                    <input type="text" name="otp" id="otp" class="form-control" placeholder="Enter OTP"  maxlength="6" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>

                                    <div class="input-group-append">

                                        <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>


                                    </div>
                                    <div class="common-error form-text" id="otp_error"></div>

                                </div>




                                @php
                                $id = optional(Auth::user())->id;
                                $otp = $id ? \App\Models\Otps::where('user_id', $id)->first() : null;
                                @endphp
                                <p> <b>OTP : {{$otp->otp}}</b></p>


                                <br>


                                <!-- <button class="btn btn-success">Verify OTP</button> -->
                                <button id="verifyButton" class="btn btn-success mt-2" type="submit">
                                    {{ __('Verify OTP') }}
                                </button>

                            </form>
                        </div>

                    </div>

                    <!-- /.card-header -->
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
//     $(document).ready(function() {
//         $("#verifyButton").click(function(e) {
//             e.preventDefault(); 
    
//             var valid = true;
//             $(".common-error").text(""); 
    
//             // Activity field
//             if ($("#otp").val() === "") {
//                 $("#otp_error").text("Please Enter OTP");
//                 valid = false;
//             } else {
//                 $("#otp_error").text(""); 
//             }
    
           
    
//             if (valid) {
//                 $("#otpVery").submit();
           
       
//     }
// });
    
       
//         $("#otp").on("input", function() {
//             $("#otp_error").text("");
//         });
    
       
//     });
</script>

@endpush