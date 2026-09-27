@extends('admin.layout.master', ['page_title' => 'Create Role'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('theme/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Roles</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Roles</li>
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
                        <div class="card-header">
                            <h3 class="card-title">Create Role</h3>
                        </div>
                        <!-- /.card-header -->
                        <!--begin::Form-->

                        <div class="card-body">
                        <form class="form" id="create_role_form">

<div class="row">
    <div class="col-1"></div>
    <div class="col-10">
        <div class="my-5">
            @csrf

            <div class="row">

                  <div class="col-1">

                  </div>  

                  <div class="col-3">
                      <label>Role Name <span
                        class="mandatory_input">*</span>
                     </label>
                  </div>  

                <div class="col-7">
                    <input type="text" id="name" name="name" class="form-control" />
                    <div class="common-error form-text name_error"></div>
                </div>

                <div class="col-1">

                </div> 
            </div>

            <div class="row">
               <div class="col-1">

               </div>  

               <div class="col-3">
                  <label>Abilities <span
                        class="mandatory_input">*</span>
                  </label>
              </div>  
                  
                <div class="col-7">
                    <select class="form-control select2_element abilities_select2_dropdown"
                        name="abilities[]" multiple="multiple" style="width:100%">
                        <option disabled="disabled">Choose abilities</option>
                           @foreach ($abilities as $ability)
                             <option value="{{ $ability['id'] }}">{{ $ability['title'] }}
                             </option>
                          @endforeach
                    </select>
                    <div class="common-error form-text abilities_error"></div>
                </div>

                <div class="col-1">
                    
                </div>  
            </div>

            <div class="row">
                <div class="col-lg-12 text-center">
                    <button type="submit" class="btn btn-success ">Submit</button>
                    <button type="reset" class="btn btn-secondary"
                        onclick="history.back();">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-1"></div>
</div>
</form>
                        </div>
                        
                        <!--end::Form-->
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection

@push('script')
    <script src="{{ asset('theme/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('admin/js/role.js') }}"></script>
@endpush
