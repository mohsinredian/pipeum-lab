@extends('admin.layout.pre_login_master', ['page_title' => 'Authenticate'])

@section('content')
<form class="form" method="post" action="{{route('reset.password')}}" onsubmit="return validatePassword()">
    @csrf

      <input type="hidden" name="token" value="{{$token}}">

    <div class="form-group">
        <label class="">Email ID</label> <br>
        <input type="email" value="{{$email ?? old('email')}}" id ="email" class="form-control"  placeholder="Enter Email" name="email">
         <span class="text-danger"><b>@error('email'){{$message}}@enderror</b></span>
    </div>
    <div class="form-group">
        <label class="">New Password</label> <br>
        <div class="input-group">
            <input type="password" id="password" class="form-control password-input" placeholder="Enter New Password" name="password" oninput="clearError('password-error')">
            <span class="input-group-text toggle-password" id="toggle-password" onclick="togglePasswordVisibility(event)" style="padding: 9px;cursor: pointer;">
                <i class="fa fa-eye-slash" aria-hidden="true"></i>
            </span>
        </div>
        <span class="text-danger" id="password-error"></span>
    </div>
    


    <div class="form-group">
        <label class="">Confirm Password</label> <br>
        <div class="input-group">
            <input type="password" id ="password_confirmation" class="form-control password-input"  placeholder="Enter Confirm Password" name="password_confirmation" oninput="clearError('password-confirmation-error')">
            <span class="input-group-text toggle-password" id="confirmation_toggle-password" onclick="togglePasswordVisibility(event)" style="padding: 9px;cursor: pointer;">
                <i class="fa fa-eye-slash" aria-hidden="true"></i>
            </span>
        </div>
        <span class="text-danger" id="password-confirmation-error"></span>
    </div>

   

    <div class="text-center mt-4">
    <button type="submit" class="btn btn-info" style="width:100%;background-color: #059AC9 !important;
    border: 2px solid #059AC9 !important;"><b>Reset Password</b></button>

 

    </div>

   

</form>
<div class="text-center mt-3">
<a href="/admin/auth" style="color:white;"><button class="btn btn-info" style="width:100%;background-color: #059AC9 !important;
    border: 2px solid #059AC9 !important;"><b>Log in</b></button> </a>
</div>


@endsection

@push('script')

<script>
   function validatePassword() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        const lengthPattern = /^.{8,}$/;
        const uppercasePattern = /[A-Z]/;
        const lowercasePattern = /[a-z]/;
        const specialCharacterPattern = /[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]/;

        clearError('password-error');
        clearError('password-confirmation-error');

        if (!lengthPattern.test(password)) {
            setError('password-error', 'Password must be at least 8 characters long.');
            return false;
        }

        if (!uppercasePattern.test(password)) {
            setError('password-error', 'Password must contain at least one uppercase letter[A-Z].');
            return false;
        }

        if (!lowercasePattern.test(password)) {
            setError('password-error', 'Password must contain at least one lowercase letter[a-z].');
            return false;
        }

        if (!specialCharacterPattern.test(password)) {
            setError('password-error', 'Password must contain at least one special character(!@#$%^&*()_+{}).');
            return false;
        }

        if (password !== confirmPassword) {
            setError('password-confirmation-error', 'Passwords do not match.');
            return false;
        }

        return true;
    }

    function setError(elementId, message) {
        document.getElementById(elementId).innerText = message;
    }

    function clearError(elementId) {
        document.getElementById(elementId).innerText = '';
    }

    function togglePasswordVisibility(event) {
    const toggleButton = event.currentTarget;
    const passwordInput = toggleButton.parentElement.querySelector(".password-input");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleButton.innerHTML = '<i class="fa fa-eye" aria-hidden="true"></i>';
    } else {
        passwordInput.type = "password";
        toggleButton.innerHTML = '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
    }
}

</script>

@endpush
