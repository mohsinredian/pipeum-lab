<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ Config::get('app.name') }} @if (isset($page_title))
            {{ ' | ' . $page_title }}
        @endif
    </title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('theme/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('theme/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('theme/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <link href="{{ asset('admin/css/styles.css') }}" rel="stylesheet" type="text/css" />

</head>

<body>

    <body class="hold-transition otp-body">
        {{-- @if (session('otp_required')) --}}
        <div class="row" style="height: 100vh;">
            <div class="col-12 d-flex align-items-center justify-content-center">
                
                    <div class="login-box " style="width: 450px;">
                        <div class="card" style="box-shadow:none;">
                            <div class="card-body login-card-body ">
                                <div class="login-logo ">
                                    <a href="javascript:;"><img alt="Logo" src="{{ asset('/images/logo-login.png') }}"
                                            class="max-h-100px img-fluid">
                                    </a>
                                    {{-- <p><b>SCADA MANAGEMENT SYSTEM</b></p> --}}
                                    {{-- <div class="login-icons">
                                        <img alt="Logo" src="{{ asset('/images/login-img.png') }}"><i
                                            class="fas fa-user-tie"></i>
        
                                        <button class="my-account-login">My Account</button>
                                    </div> --}}
                                </div>
                             @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
        
                                @yield('content')
                                <div class="container mt-5 text-center">
                                    @if ($errors->any())
                                        @foreach ($errors->all() as $error)
                                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                                <strong>{{ $error }}</strong>
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
        
                                    <form id="otpVery" method="POST" action="{{ route('verify-otp') }}">
                                        @csrf
                                        <div class="container text-center mb-4">
                                            <span><b> Verification Code Was Sent <span style="color:rgb(3, 142, 220)"> Successfully ! </span></b></span>
                                        </div>
                                        <div class="container-fluid">
                                            <div class="conteiner-fluid mb-4">
                                                <input class="form-control" id="otp" type="text" name="otp"
                                                    required autofocus placeholder="Enter OTP" style="border:1px solid #ced4da;" maxlength="6" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                                                    <div class="common-error form-text" id="otp_error"></div>

                                                @error('otp')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                {{-- <div class="input-group-append">
                                                    <div class="input-group-text">
        
                                                        <span><i class="fas fa-mobile-alt"></i></span>
                                                    </div>
                                                </div> --}}
                                            </div>
                                        </div>
                                        @php
                                            $user = Auth::user();
                                            $role_id = $user->role_id;
                                        @endphp
                                         @if ($role_id == '1')
                                            <div class="container">
                                                @php
                                                    $id = optional(Auth::user())->id;
                                                    $otp = $id ? \App\Models\Otps::where('user_id', $id)->first() : null;
                                                @endphp
                                                <b>OTP: {{ $otp->otp }}</b> 
                                            
                                            </div>
                                            <div class="container-fluid text-center mb-4">
                                                <button  id="verifyButton" class="btn btn-success mt-2" type="submit" style="width:100%;">
                                                    {{ __('Verify OTP') }}
                                                </button>
                                                
                                            </div>
                                        @else
                                            <div class="container">
                                                @php
                                                    $id = optional(Auth::user())->id;
                                                    $otp = $id ? \App\Models\Otps::where('user_id', $id)->first() : null;
                                                @endphp
                                        {{-- <b>OTP: {{ $otp->otp }}</b>  --}}
                                            
                                            </div>
                                            <div class="container-fluid text-center mb-4">
                                                <button  id="verifyButton" class="btn btn-success mt-2" type="submit" style="width:100%;">
                                                    {{ __('Verify OTP') }}
                                                </button>
                                                <div> <b>Resend OTP in <span id="timer"></span></b></div>
                                            </div>
                                        @endif
                                    </form>  
                                </div>
                                <form class="form" action="{{ route('regenerateOTP') }}" method="POST">
                                    @csrf
                                    <div class="container">
                                        <div class="container-fluid text-center mb-4">
                                            <center>
                                                <button class="btn btn-success mt-2" id="resendButton" style="display: none" style="width:100%;">Resend OTP</button>
                                            </center>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
            
            </div>

          
        </div>




    </body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
    <script>
        $('input[name="otp"]').keyup(function(e)
                                {
         if (/\D/g.test(this.value))
        {
            // Filter non-digits from input value.
            this.value = this.value.replace(/\D/g, '');
        }
        });

        let timerOn = true;

function showResendButton() {
    document.getElementById('resendButton').style.display = 'block';
    document.getElementById('verifyButton').disabled = true; // Disable the "Verify OTP" button
}

function timer(remaining) {
    var m = Math.floor(remaining / 60);
    var s = remaining % 60;

    m = m < 10 ? '0' + m : m;
    s = s < 10 ? '0' + s : s;
    document.getElementById('timer').innerHTML = m + ':' + s;
    remaining -= 1;

    if (remaining >= 0 && timerOn) {
        setTimeout(function () {
            timer(remaining);
        }, 1000);
        return;
    }

    if (!timerOn) {
        // Do validate stuff here
        return;
    }

    // Show SweetAlert2 notification for timeout
    Swal.fire({
        text: "Timeout for OTP",
        type: 'error',
        buttonsStyling: false,
        confirmButtonText: "Ok",
        confirmButtonClass: "btn font-weight-bold btn-light"
    }).then(function () {
        showResendButton(); // Show the "Resend OTP" button when the timer stops
    });
}

    timer(180);


      
    </script>

<script>
    $(document).ready(function() {
        $("#verifyButton").click(function(e) {
            e.preventDefault(); 
    
            var valid = true;
            $(".common-error").text(""); 
    
            // Activity field
            if ($("#otp").val() === "") {
                $("#otp_error").text("Please Enter OTP");
                valid = false;
            } else {
                $("#otp_error").text(""); 
            }
    
           
    
            if (valid) {
                $("#otpVery").submit();
           
       
    }
});
    
       
        $("#otp").on("input", function() {
            $("#otp_error").text("");
        });
    
       
    });
</script>

</html>
