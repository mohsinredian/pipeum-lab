@extends('admin.layout.pre_login_master', ['page_title' => 'Authenticate'])

@section('content')
<form class="form" id="login_form">
    @csrf
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    @if(Session::get('success'))
    <div class="alert alert-info">

        {{Session::get('success')}}
    </div>
    @endif

    @if(Session::get('fail'))
    <div class="alert alert-danger">

        {{Session::get('fail')}}
    </div>
    @endif

    <div class="form-group">
        <label class="">Employee ID</label> <br>
        <input type="text" class="form-control" placeholder="Employee ID" name="login_field" id="login_field"
            autocomplete="off">
        <div class="common-error form-text login_field_error"></div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label class="">Password</label> <br>
                <input type="password" class="form-control" placeholder="Password" name="password" id="password">

                <div class="input-group-append" style="position: absolute;right: 7px;top: 31.5px;">
                    <span class="input-group-text toggle-password" id="toggle-password"
                        onclick="togglePasswordVisibility()" style="padding: 10px;cursor: pointer;">
                        <i class="fa fa-eye-slash" aria-hidden="true"></i>
                    </span>
                </div>
                <div class="common-error form-text password_error"></div>

            </div>
        </div>
    </div>



    <div class="input-group{{ $errors->has('captcha') ? ' has-error' : '' }}">
        <div class="col-md-12">
            <div class="captcha">
                <span>{!! captcha_img('math') !!}</span>
                <button type="button" class="btn btn-success btn-refresh"><i>&#x21bb;</i></button>
            </div>
            <input id="captcha" type="text" class="form-control" placeholder="Enter Captcha" name="captcha"
                style="border:1px solid #ced4da;">
            <span class="captcha_error text-danger">
            </span>
        </div>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-danger btn-block login_btn btn-refresh">Login</button>

    </div>

    <p class="text-center">
        <a href="{{route('forgot.password')}}"><b>Forgot Password/Reset Password</b></a>
    </p>

</form>


@endsection

@push('script')
<script type="text/javascript">
    $(".btn-refresh").click(function () {
        $.ajax({
            type: 'GET'
            , url: '/refresh_captcha'
            , success: function (data) {
                $(".captcha span").html(data.captcha);
            }
        });
    });

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById("password");
        const togglePassword = document.getElementById("toggle-password");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            togglePassword.innerHTML = '<i class="fa fa-eye" aria-hidden="true"></i>';
        } else {
            passwordInput.type = "password";
            togglePassword.innerHTML = '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
        }
    }

</script>
<script src="{{ asset('admin/js/auth.js') }}"></script>
@endpush