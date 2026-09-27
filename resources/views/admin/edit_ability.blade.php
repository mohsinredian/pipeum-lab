@extends('admin.layout.master', ['page_title' => 'Edit Ability'])

@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage Abilities</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">Abilities</li>
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
                <h3 class="card-title">Edit Ability</h3>
              </div>
              <!-- /.card-header -->
              <!--begin::Form-->
              <form class="form" id="edit_ability_form">

                <div class="row">
                    <div class="col-xl-3"></div>
                    <div class="col-xl-6">
                        <div class="my-5">
                          @csrf
                          <input type="hidden" name="ability_id" value="{{$ability['id']}}">
                          <div class="form-group row">
                                <label class="col-3">Ability Name <span class="mandatory_input">*</span></label>
                                <div class="col-9">
                                    <input type="text" id="name" name="name" class="form-control" value="{{$ability['name']}}" />
                                    <div class="common-error form-text name_error"></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3">Title <span class="mandatory_input">*</span></label>
                                <div class="col-9">
                                    <input type="text" id="title" name="title" class="form-control" value="{{$ability['title']}}" />
                                    <div class="common-error form-text title_error"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 text-center">
                                  <button type="submit" class="btn btn-success mr-2">Submit</button>
                                  <button type="reset" class="btn btn-secondary" onclick="history.back();">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                  <div class="col-xl-3"></div>
                </div>
              </form>
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
<script src="{{asset('admin/js/ability.js')}}"></script>

@endpush