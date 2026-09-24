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

<body class="hold-transition login-page" >
    <div class="pre-loader" style="display: block;"></div>
    
    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
        <div class="login-box" style="width: 432px;"  >
       
            <!-- /.login-logo -->
            <div class="card ">
                <div class="card-body login-card-body " >
                    <div class="login-logo " >
                        <a href="javascript:;"><img alt="Logo" src="{{ asset('/images/logo-login.png') }}"
                                class="max-h-100px img-fluid">
                        </a>
                        {{-- <p><b>SCADA MANAGEMENT SYSTEM</b></p> --}}
                        <div class="login-icons">
                            <img alt="Logo" src="{{ asset('/images/login-img.png') }}"
                           ><i class="fas fa-user-tie"></i>
    
                           <button class="my-account-login">My Account</button>
                        </div>
                    </div>
              
                    @yield('content')
                    
                </div>
                <!-- /.login-card-body -->
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">

    </div>

    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">

    </div>
    
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="{{ asset('theme/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('theme/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7"></script>
    <script src="{{ asset('theme/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('theme/dist/js/adminlte.min.js') }}"></script>

    @stack('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.pre-loader').hide();
        });
    </script>
</body>

</html>
