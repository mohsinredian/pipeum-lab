@extends('admin.layout.pre_login_master', ['page_title' => 'Authenticate'])
@push('styles')

@endpush
@section('content')

<br>
            <br>
<form class="form" method="post" action="{{route('reset.password.link')}}">
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
      <div class="form-group row">
           <div class="col-12"> 
              <label class=""><b>Email ID</b></label>
            </div>

            <div class="col-9">
                <input type="email" value="{{old('email')}}" id ="email" class="form-control"  placeholder="Enter Email" name="email">
            </div>

            <div class="col-3">
                 <button type="submit" class="btn btn-info" style="background-color: #059AC9 !important;
                  border: 2px solid #059AC9 !important;"><b>Send</b></button>
            </div>

            <div class="col-12">
                 <span class="text-danger"><b>@error('email'){{$message}}@enderror</b></span>
            </div> 
      </div>

              </form>

              <div class="row">
                   <div class="col-12">
                   <a href="/admin/auth" style="color:white;"><button class="btn btn-info" style="width:100%;background-color: #059AC9 !important;border: 2px solid #059AC9 !important;"><b>
                         Log in</b>
                    </button> </a>
                   </div>
              </div>

          

            <br>
            <br>
            <br>
@endsection

@push('script')

@endpush
