 {{-- @extends('admin.layout.pre_login_master', ['page_title' => 'Authenticate'])

 <style>
  .text-danger{
  font-weight:bold;
}
  </style>

@section('content')
<div class="card-header">
  <h3 class="card-title text-center">Your Password has expired, please change it</h3>
</div>

<form class="form-horizontal" action="{{ route('save.password.change') }}" method="post">
  @csrf
  <div class="">


@if(Session::get('success'))
<div class="alert alert-success">

{{Session::get('success')}}
</div>
@endif

@if(Session::get('fail'))
<div class="alert alert-danger">

{{Session::get('fail')}}
</div>
@endif
@if ($errors->any())
@foreach ($errors->all() as $error)

</div>
@endforeach
@endif

  
    <div class="form-group row">
      <div class="col-12">
        <label for="password">New Password : <span class="text-danger">*</span></label>
      </div>

      <div class="col-12">
        <input type="password" id ="password" class="form-control"  placeholder="Enter New Password" name="password">
        <span class="text-danger">@error('password'){{$message}}@enderror</span>
      </div>

      <div class="col-12">
        <label for="password" class="col-form-label">Confirm Password : <span class="text-danger">*</span></label>
      </div>


      <div class="col-12">
        <input type="password" id ="password_confirmation" class="form-control"  placeholder="Enter Confirm Password" name="password_confirmation">
        <span class="text-danger">@error('password_confirmation'){{$message}}@enderror</span>
      </div>

      <div class="col-12 mt-2">
        <button type="submit" class="btn btn-info" style="width: 100%; color:white;background-color:#059AC9;border:2px solid #059AC9;">Reset Password</button>
      </div>
    </div>
  </div>
</form>
@endsection --}}

@extends('admin.layout.pre_login_master', ['page_title' => 'Authenticate'])

@section('content')
<style>
  .text-danger{
  font-weight:bold;
}
  </style>
  <div class="card-header">
    <h3 class="card-title text-center">Your Password has expired, please change it</h3>
  </div>
<form class="form-horizontal" action="{{ route('save.password.change') }}" method="post"  onsubmit="return validatePassword()">
    @csrf

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

    

    <div class="text-center">
      <button type="submit" class="btn btn-info" style="width: 100%; color:white;background-color:#059AC9;border:2px solid #059AC9;">Reset Password</button>
    </div>

   

</form>


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