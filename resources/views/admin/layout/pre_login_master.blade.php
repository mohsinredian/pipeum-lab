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

    <style>
      body{
        overflow:hidden;
      }
    </style>
</head>

<body class="login-body">
    <!-- <h1 style="color: #059AC9; font-weight:900; text-align:center;">Need Validation</h1>
    <p style="color: black; font-weight:900; text-align:center;">Management System</p> -->
    <div class="row">

        <div class="col-12 d-flex flex-column justify-content-center align-items-center" style="width:100%; height:100vh;">

            <div class="login-box" style="width:300px; border-radius:10px;">
                <div class="card bordered-0" style="border-radius: 10px; border:none; box-shadow:none;">
                <h1 style="color: #059AC9; font-weight:900; text-align:center;">Need Validation</h1>
                    <p style="color: black; font-weight:900; text-align:center;">Management System</p>
                    <div class="card-body">
                        <div class="login-logo ">
                            <a href="javascript:;"><img alt="Logo" src="{{ asset('/images/logo-login.png') }}"
                                    class="max-h-100px img-fluid">
                            </a>

                        </div>

                        @yield('content')

                    </div>

                </div>
            </div>

            
    <div >
    <p style="color: gray; font-weight:900; text-align:center; margin-top:20px;">The
            Online Need Validation Process Aims To Digitize The Process of Need Validation Submission & Approvals </p>
    </div>

    </div>
        </div>



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