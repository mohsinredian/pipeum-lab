@extends('admin.layout.master', ['page_title' => 'Create workflow'])


@section('content')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage WorkFlow</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('/admin/dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">WorkFlow</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">
            
            <form class="form" id="create_workflow_form">
                @csrf      
                <input type="hidden" class="form-control" name="workflow_id" id="workflow_id"
                value="{{ $workflows != '' ? $workflows->id: '' }}" >     
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">NV Workflow</h3>
                  </div>
                  <!-- /.card-header -->
                  <!--begin::Form-->
                  
                    <div class="card-body">
                    <div class="form-group row">

                    <table id="" class="table bg-light table-hover shadow-lg table-bordered table-responsive" style="height:100%;overflow-x: auto;">
                        <thead>
                        <tr>
                            <th  class="text-center">  <label>Department </label></th>
                            <th  class="text-center">  <label>Reviewer 1 </label></th>
                            <th  class="text-center">  <label>Reviewer 2 </label></th>
                            <th  class="text-center">  <label>Reviewer 3 </label></th>
                            <th  class="text-center">  <label>Reviewer 4 </label></th>
                            <th  class="text-center">  <label> Approver </label></th>
                        </tr>
                        </thead>

                        <tbody>
                            <tr>
                              <td class="text-center" >
                                <label style="width:200px">Department<span class="mandatory_input">*</span></label>
                                <select class="form-control " name="dep1" id="dep1">
                                <option value="">Select Department </option>
                            
                                    <option value="Budget Capex" {{ ($workflows !='' ? $workflows->
                                      dep1: '' )=='Budget Capex' ? 'selected'
                                                : '' }}>Budget Capex</option>
                                    <option value="Budget Opex"{{ ($workflows !='' ? $workflows->
                                      dep1: '' )=='Budget Opex' ? 'selected'
                                                : '' }}>Budget Opex</option>
                                    <option value="CPMG" {{ ($workflows !='' ? $workflows->
                                      dep1: '' )=='CPMG' ? 'selected'
                                                : '' }}>CPMG</option>
                                    <option value="CEO Nominee1" {{ ($workflows !='' ? $workflows->
                                      dep1: '' )=='CEO Nominee1' ? 'selected'
                                                : '' }}>CEO Nominnee1</option>
                                    <option value="CEO Nominee2 A1"{{ ($workflows !='' ? $workflows->
                                      dep1: '' )=='CEO Nominee2 A1' ? 'selected'
                                                : '' }}>CEO Nominee2 A1</option>
                                    <option value="CEO Nominee2 A2" {{ ($workflows !='' ? $workflows->
                                      dep1: '' )=='CEO Nominee2 A2' ? 'selected'
                                                : '' }}>CEO Nominee2 A2</option>
                                    <option value="CEO" {{ ($workflows !='' ? $workflows->
                                      dep1: '' )=='CEO' ? 'selected'
                                                : '' }}>CEO</option>
                               
                                  </select>
                                </td>
                              
                                
                                <td class="text-left">
                                    <div class="row">
                                         <div class="col-6">
                                            <label style="width:400px">User Name <span class="mandatory_input" >*</span></label>
                                            <input type="text" class="form-control" id="bt_cap_rv1_uname" name="bt_cap_rv1_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->bt_cap_rv1_uname: '' }}">
                                            <div class="common-error form-text email_error"></div><span> 
                                         </div>

                                         <div class="col-6">
                                        <label style="width:400px">Email Id <span class="mandatory_input">*</span></label>
                                               <input type="email" class="form-control" id="bt_cap_rv1_email" name="bt_cap_rv1_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->bt_cap_rv1_email: '' }}">
                                              <div class="common-error form-text mobile_error"></div></span>
                                        </div>
                                    </div>
                                </td>
                            

                                <td class="text-left" >
                                  <div class="row">
                                         <div class="col-6">
                                            <label style="width:400px">User Name <span class="mandatory_input">*</span></label>
                                            <input type="text" class="form-control" id="bt_cap_rv2_uname" name="bt_cap_rv2_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->bt_cap_rv2_uname: '' }}">
                                            <div class="common-error form-text email_error"></div><span> 
                                         </div>

                                         <div class="col-6">
                                          <label style="width:400px">Email Id <span class="mandatory_input">*</span></label>
                                               <input type="email" class="form-control" id="bt_cap_rv2_email" name="bt_cap_rv2_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->bt_cap_rv2_email: '' }}">
                                              <div class="common-error form-text mobile_error"></div></span>
                                        </div>
                                    </div>
                                </td>
                        
                                <td class="text-left" >
                                  <div class="row">
                                         <div class="col-6">
                                            <label style="width:400px">User Name <span class="mandatory_input">*</span></label>
                                            <input type="text" class="form-control" id="bt_cap_rv3_uname" name="bt_cap_rv3_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->bt_cap_rv3_uname: '' }}">
                                            <div class="common-error form-text email_error"></div><span> 
                                         </div>

                                         <div class="col-6">
                                        <label style="width:400px">Email Id <span class="mandatory_input">*</span></label>
                                               <input type="email" class="form-control" id="bt_cap_rv3_email" name="bt_cap_rv3_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->bt_cap_rv3_email: '' }}">
                                              <div class="common-error form-text mobile_error"></div></span>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-left" >
                                  <div class="row">
                                         <div class="col-6">
                                            <label style="width:400px">User Name <span class="mandatory_input">*</span></label>
                                            <input type="text" class="form-control" id="bt_cap_rv4_uname" name="bt_cap_rv4_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->bt_cap_rv4_uname: '' }}">
                                            <div class="common-error form-text email_error"></div><span> 
                                         </div>

                                         <div class="col-6">
                                               <div class="common-error form-text email_error"></div><span> <label style="width:400px">Email Id <span class="mandatory_input">*</span></label>
                                               <input type="email" class="form-control" id="bt_cap_rv4_email" name="bt_cap_rv4_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->bt_cap_rv4_email: '' }}">
                                              <div class="common-error form-text mobile_error"></div></span>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-left" >
                                  <div class="row">
                                         <div class="col-6">
                                            <label style="width:400px">User Name <span class="mandatory_input">*</span></label>
                                            <input type="text" class="form-control" id="bt_capex_username" name="bt_capex_username" placeholder="User Name " value="{{ $workflows != '' ? $workflows->bt_capex_username: '' }}">
                                            <div class="common-error form-text email_error"></div><span> 
                                         </div>

                                         <div class="col-6">
                                           <label style="width:400px">Email Id <span class="mandatory_input">*</span></label>
                                               <input type="email" class="form-control" id="bt_capex_email" name="bt_capex_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->bt_capex_email: '' }}">
                                              <div class="common-error form-text mobile_error"></div></span>
                                        </div>
                                    </div>
                                </td>
                               
                            </tr>

                            <tr>
                              <td class="text-center" >
                                <!-- <label>Department <span class="mandatory_input">*</span></label> -->
                                <select class="form-control " name="dep2" id="dep2">
                                <option value="">Select Department </option>

                                <option value="Budget Capex" {{ ($workflows !='' ? $workflows->
                                      dep2: '' )=='Budget Capex' ? 'selected'
                                                : '' }}>Budget Capex</option>
                                    <option value="Budget Opex"{{ ($workflows !='' ? $workflows->
                                      dep2: '' )=='Budget Opex' ? 'selected'
                                                : '' }}>Budget Opex</option>
                                    <option value="CPMG" {{ ($workflows !='' ? $workflows->
                                      dep2: '' )=='CPMG' ? 'selected'
                                                : '' }}>CPMG</option>
                                    <option value="CEO Nominee1" {{ ($workflows !='' ? $workflows->
                                      dep2: '' )=='CEO Nominee1' ? 'selected'
                                                : '' }}>CEO Nominnee1</option>
                                    <option value="CEO Nominee2 A1"{{ ($workflows !='' ? $workflows->
                                      dep2: '' )=='CEO Nominee2 A1' ? 'selected'
                                                : '' }}>CEO Nominee2 A1</option>
                                    <option value="CEO Nominee2 A2" {{ ($workflows !='' ? $workflows->
                                      dep2: '' )=='CEO Nominee2 A2' ? 'selected'
                                                : '' }}>CEO Nominee2 A2</option>
                                    <option value="CEO" {{ ($workflows !='' ? $workflows->
                                      dep2: '' )=='CEO' ? 'selected'
                                                : '' }}>CEO</option>
                                  </select>
                                </td>
                              
                                
                                <td class="text-center">
                                    <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                              <input type="text" class="form-control" id="bt_op_rv1_uname" name="bt_op_rv1_uname" placeholder="User Name" value="{{ $workflows != '' ? $workflows->bt_op_rv1_uname: '' }}">
                              <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                              <input type="email" class="form-control" id="bt_op_rv1_email" name="bt_op_rv1_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->bt_op_rv1_email: '' }}">
                              <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                            

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                              <input type="text" class="form-control" id="bt_op_rv2_uname" name="bt_op_rv2_uname" placeholder="User Name" value="{{ $workflows != '' ? $workflows->bt_op_rv2_uname: '' }}">
                              <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                              <input type="email" class="form-control" id="bt_op_rv2_email" name="bt_op_rv2_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->bt_op_rv2_email: '' }}">
                              <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                        
                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                              <input type="text" class="form-control" id="bt_op_rv3_uname" name="bt_op_rv3_uname" placeholder="User Name" value="{{ $workflows != '' ? $workflows->bt_op_rv3_uname: '' }}">
                              <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                              <input type="email" class="form-control" id="bt_op_rv3_email" name="bt_op_rv3_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->bt_op_rv3_email: '' }}">
                              <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                              <input type="text" class="form-control" id="bt_op_rv4_uname" name="bt_op_rv4_uname" placeholder="User Name" value="{{ $workflows != '' ? $workflows->bt_op_rv4_uname: '' }}">
                              <div class="common-error form-text email_error"></div>
                             
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                              <input type="email" class="form-control" id="bt_op_rv4_email" name="bt_op_rv4_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->bt_op_rv4_email: '' }}">
                              <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                              <input type="text" class="form-control" id="bt_opex_username" name="bt_opex_username" placeholder="User Name" value="{{ $workflows != '' ? $workflows->bt_opex_username: '' }}">
                              <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                              <input type="email" class="form-control" id="bt_opex_eamil" name="bt_opex_eamil" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->bt_opex_eamil: '' }}">
                              <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                               
                            </tr>
                            <tr> <td><i class="m-0 fa fa-plus-circle" style="position: relative;left: 50%; font-size:28px" aria-hidden="true"></i></td></tr>

                            <tr>
                              <td class="text-center" >
                                <!-- <label>CPMG <span class="mandatory_input">*</span></label> -->
                                <select class="form-control " name="dep3" id="dep3">
                                <option value="">Select Department </option>

                                  <option value="Budget Capex" {{ ($workflows !='' ? $workflows->
                                      dep3: '' )=='Budget Capex' ? 'selected'
                                                : '' }}>Budget Capex</option>
                                    <option value="Budget Opex"{{ ($workflows !='' ? $workflows->
                                      dep3: '' )=='Budget Opex' ? 'selected'
                                                : '' }}>Budget Opex</option>
                                    <option value="CPMG" {{ ($workflows !='' ? $workflows->
                                      dep3: '' )=='CPMG' ? 'selected'
                                                : '' }}>CPMG</option>
                                    <option value="CEO Nominee1" {{ ($workflows !='' ? $workflows->
                                      dep3: '' )=='CEO Nominee1' ? 'selected'
                                                : '' }}>CEO Nominnee1</option>
                                    <option value="CEO Nominee2 A1"{{ ($workflows !='' ? $workflows->
                                      dep3: '' )=='CEO Nominee2 A1' ? 'selected'
                                                : '' }}>CEO Nominee2 A1</option>
                                    <option value="CEO Nominee2 A2" {{ ($workflows !='' ? $workflows->
                                      dep3: '' )=='CEO Nominee2 A2' ? 'selected'
                                                : '' }}>CEO Nominee2 A2</option>
                                    <option value="CEO" {{ ($workflows !='' ? $workflows->
                                      dep3: '' )=='CEO' ? 'selected'
                                                : '' }}>CEO</option>
                                  </select>
                                </td>
                              
                                
                                <td class="text-center">
                                    <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                            <input type="text" class="form-control" id="cpmg_rv1_uname" name="cpmg_rv1_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->cpmg_rv1_uname: '' }}">
                            <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                            <input type="email" class="form-control" id="cpmg_rv1_email" name="cpmg_rv1_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cpmg_rv1_email: '' }}">
                            <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                            

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                            <input type="text" class="form-control" id="cpmg_rv2_uname" name="cpmg_rv2_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->cpmg_rv2_uname: '' }}">
                            <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                            <input type="email" class="form-control" id="cpmg_rv2_email" name="cpmg_rv2_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cpmg_rv2_email: '' }}">
                            <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                        
                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                            <input type="text" class="form-control" id="cpmg_rv3_uname" name="cpmg_rv3_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->cpmg_rv3_uname: '' }}">
                            <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                            <input type="email" class="form-control" id="cpmg_rv3_email" name="cpmg_rv3_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cpmg_rv3_email: '' }}">
                            <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                            <input type="text" class="form-control" id="cpmg_rv4_uname" name="cpmg_rv4_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->cpmg_rv4_uname: '' }}">
                            <div class="common-error form-text email_error"></div>
                             
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                            <input type="email" class="form-control" id="cpmg_rv4_email" name="cpmg_rv4_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cpmg_rv4_email: '' }}">
                            <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                            <input type="text" class="form-control" id="cpmg_username" name="cpmg_username" placeholder="User Name " value="{{ $workflows != '' ? $workflows->cpmg_username: '' }}">
                            <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                            <input type="email" class="form-control" id="cpmg_email" name="cpmg_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cpmg_email: '' }}">
                            <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                               
                            </tr>

                            <tr> <td><i class="m-0 fa fa-plus-circle" style="position: relative;left: 50%; font-size:28px" aria-hidden="true"></i></td></tr>
                            <tr>
                              <td class="text-center" >
                                <!-- <label>CEO Nominee 1 <span class="mandatory_input">*</span></label> -->
                                <select class="form-control " name="dep4" id="dep4">
                                <option value="">Select Department </option>

                                 <option value="Budget Capex" {{ ($workflows !='' ? $workflows->
                                      dep4: '' )=='Budget Capex' ? 'selected'
                                                : '' }}>Budget Capex</option>
                                    <option value="Budget Opex"{{ ($workflows !='' ? $workflows->
                                      dep4: '' )=='Budget Opex' ? 'selected'
                                                : '' }}>Budget Opex</option>
                                    <option value="CPMG" {{ ($workflows !='' ? $workflows->
                                      dep4: '' )=='CPMG' ? 'selected'
                                                : '' }}>CPMG</option>
                                    <option value="CEO Nominee1" {{ ($workflows !='' ? $workflows->
                                      dep4: '' )=='CEO Nominee1' ? 'selected'
                                                : '' }}>CEO Nominnee1</option>
                                    <option value="CEO Nominee2 A1"{{ ($workflows !='' ? $workflows->
                                      dep4: '' )=='CEO Nominee2 A1' ? 'selected'
                                                : '' }}>CEO Nominee2 A1</option>
                                    <option value="CEO Nominee2 A2" {{ ($workflows !='' ? $workflows->
                                      dep4: '' )=='CEO Nominee2 A2' ? 'selected'
                                                : '' }}>CEO Nominee2 A2</option>
                                    <option value="CEO" {{ ($workflows !='' ? $workflows->
                                      dep4: '' )=='CEO' ? 'selected'
                                                : '' }}>CEO</option>
                                  </select>
                                </td>
                              
                                
                                <td class="text-center">
                                    <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                          <input type="text" class="form-control" id="cn1_rv1_uname" name="cn1_rv1_uname" placeholder="User Name" value="{{ $workflows != '' ? $workflows->cn1_rv1_uname: '' }}">
                          <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                          <input type="email" class="form-control" id="cn1_rv1_email" name="cn1_rv1_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cn1_rv1_email: '' }}">
                          <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                            

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                          <input type="text" class="form-control" id="cn1_rv2_uname" name="cn1_rv2_uname" placeholder="User Name" value="{{ $workflows != '' ? $workflows->cn1_rv2_uname: '' }}">
                          <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                          <input type="email" class="form-control" id="cn1_rv2_email" name="cn1_rv2_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cn1_rv2_email: '' }}">
                          <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                        
                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                          <input type="text" class="form-control" id="cn1_rv3_uname" name="cn1_rv3_uname" placeholder="User Name" value="{{ $workflows != '' ? $workflows->cn1_rv3_uname: '' }}">
                          <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                          <input type="email" class="form-control" id="cn1_rv3_email" name="cn1_rv3_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cn1_rv3_email: '' }}">
                          <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                          <input type="text" class="form-control" id="cn1_rv4_uname" name="cn1_rv4_uname" placeholder="User Name" value="{{ $workflows != '' ? $workflows->cn1_rv4_uname: '' }}">
                          <div class="common-error form-text email_error"></div>
                             
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                          <input type="email" class="form-control" id="cn1_rv4_email" name="cn1_rv4_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cn1_rv4_email: '' }}">
                          <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                          <input type="text" class="form-control" id="ceo_nominee1_username" name="ceo_nominee1_username" placeholder="User Name" value="{{ $workflows != '' ? $workflows->ceo_nominee1_username: '' }}">
                          <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                          <input type="email" class="form-control" id="ceo_nominee1_email" name="ceo_nominee1_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->ceo_nominee1_email: '' }}">
                          <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                               
                            </tr>
                            <tr> <td><i class="m-0 fa fa-plus-circle" style="position: relative;left: 50%; font-size:28px" aria-hidden="true"></i></td></tr>

                            <tr>
                              <td class="text-center" >
                                <!-- <label>CEO Nominee 2 Approval 1 <span class="mandatory_input">*</span></label> -->
                                <select class="form-control " name="dep5" id="dep5">

                                <option value="Budget Capex" {{ ($workflows !='' ? $workflows->
                                      dep5: '' )=='Budget Capex' ? 'selected'
                                                : '' }}>Budget Capex</option>
                                    <option value="Budget Opex"{{ ($workflows !='' ? $workflows->
                                      dep5: '' )=='Budget Opex' ? 'selected'
                                                : '' }}>Budget Opex</option>
                                    <option value="CPMG" {{ ($workflows !='' ? $workflows->
                                      dep5: '' )=='CPMG' ? 'selected'
                                                : '' }}>CPMG</option>
                                    <option value="CEO Nominee1" {{ ($workflows !='' ? $workflows->
                                      dep5: '' )=='CEO Nominee1' ? 'selected'
                                                : '' }}>CEO Nominnee1</option>
                                    <option value="CEO Nominee2 A1"{{ ($workflows !='' ? $workflows->
                                      dep5: '' )=='CEO Nominee2 A1' ? 'selected'
                                                : '' }}>CEO Nominee2 A1</option>
                                    <option value="CEO Nominee2 A2" {{ ($workflows !='' ? $workflows->
                                      dep5: '' )=='CEO Nominee2 A2' ? 'selected'
                                                : '' }}>CEO Nominee2 A2</option>
                                    <option value="CEO" {{ ($workflows !='' ? $workflows->
                                      dep5: '' )=='CEO' ? 'selected'
                                                : '' }}>CEO</option>
                                  </select>
                                </td>
                              
                                
                                <td class="text-center">
                                    <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                        <input type="text" class="form-control" id="cn2a1_rv1_uname" name="cn2a1_rv1_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->cn2a1_rv1_uname: '' }}">
                        <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                        <input type="email" class="form-control" id="cn2a1_rv1_email" name="cn2a1_rv1_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cn2a1_rv1_email: '' }}">
                        <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                            

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                        <input type="text" class="form-control" id="cn2a1_rv2_uname" name="cn2a1_rv2_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->cn2a1_rv2_uname: '' }}">
                        <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                        <input type="email" class="form-control" id="cn2a1_rv2_email" name="cn2a1_rv2_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cn2a1_rv2_email: '' }}">
                        <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                        
                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                        <input type="text" class="form-control" id="cn2a1_rv3_uname" name="cn2a1_rv3_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->cn2a1_rv3_uname: '' }}">
                        <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                        <input type="email" class="form-control" id="cn2a1_rv3_email" name="cn2a1_rv3_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cn2a1_rv3_email: '' }}">
                        <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                        <input type="text" class="form-control" id="cn2a1_rv4_uname" name="cn2a1_rv4_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->cn2a1_rv4_uname: '' }}">
                        <div class="common-error form-text email_error"></div>
                             
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                        <input type="email" class="form-control" id="cn2a1_rv4_email" name="cn2a1_rv4_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cn2a1_rv4_email: '' }}">
                        <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                        <input type="text" class="form-control" id="ceo_nominee2_apr1_username" name="ceo_nominee2_apr1_username" placeholder="User Name " value="{{ $workflows != '' ? $workflows->ceo_nominee2_apr1_username: '' }}">
                        <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                        <input type="email" class="form-control" id="ceo_nominee2_apr1_email" name="ceo_nominee2_apr1_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->ceo_nominee2_apr1_email: '' }}">
                        <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                               
                            </tr>
                            <tr> <td><i class="m-0 fa fa-plus-circle" style="position: relative;left: 50%; font-size:28px" aria-hidden="true"></i></td></tr>

                            <tr>
                              <td class="text-center" >
                                <!-- <label>CEO Nominee 2 Approval 2 <span class="mandatory_input">*</span></label> -->
                                <select class="form-control " name="dep6" id="dep6">

                                <option value="">Select Department </option>

                               <option value="Budget Capex" {{ ($workflows !='' ? $workflows->
                                      dep6: '' )=='Budget Capex' ? 'selected'
                                                : '' }}>Budget Capex</option>
                                    <option value="Budget Opex"{{ ($workflows !='' ? $workflows->
                                      dep6: '' )=='Budget Opex' ? 'selected'
                                                : '' }}>Budget Opex</option>
                                    <option value="CPMG" {{ ($workflows !='' ? $workflows->
                                      dep6: '' )=='CPMG' ? 'selected'
                                                : '' }}>CPMG</option>
                                    <option value="CEO Nominee1" {{ ($workflows !='' ? $workflows->
                                      dep6: '' )=='CEO Nominee1' ? 'selected'
                                                : '' }}>CEO Nominnee1</option>
                                    <option value="CEO Nominee2 A1"{{ ($workflows !='' ? $workflows->
                                      dep6: '' )=='CEO Nominee2 A1' ? 'selected'
                                                : '' }}>CEO Nominee2 A1</option>
                                    <option value="CEO Nominee2 A2" {{ ($workflows !='' ? $workflows->
                                      dep6: '' )=='CEO Nominee2 A2' ? 'selected'
                                                : '' }}>CEO Nominee2 A2</option>
                                    <option value="CEO" {{ ($workflows !='' ? $workflows->
                                      dep6: '' )=='CEO' ? 'selected'
                                                : '' }}>CEO</option>
                                  </select>
                                </td>
                              
                                
                                <td class="text-center">
                                    <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                                <input type="text" class="form-control" id="cn2a2_rv1_uname" name="cn2a2_rv1_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->cn2a2_rv1_uname: '' }}">
                                <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                                <input type="email" class="form-control" id="cn2a2_rv1_email" name="cn2a2_rv1_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cn2a2_rv1_email: '' }}">
                                <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                            

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                                <input type="text" class="form-control" id="cn2a2_rv2_uname" name="cn2a2_rv2_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->cn2a2_rv2_uname: '' }}">
                                <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                                <input type="email" class="form-control" id="cn2a2_rv2_email" name="cn2a2_rv2_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cn2a2_rv2_email: '' }}">
                                <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                        
                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                                <input type="text" class="form-control" id="cn2a2_rv3_uname" name="cn2a2_rv3_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->cn2a2_rv3_uname: '' }}">
                                <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                                <input type="email" class="form-control" id="cn2a2_rv3_email" name="cn2a2_rv3_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cn2a2_rv3_email: '' }}">
                                <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                                <input type="text" class="form-control" id="cn2a2_rv4_uname" name="cn2a2_rv4_uname" placeholder="User Name " value="{{ $workflows != '' ? $workflows->cn2a2_rv4_uname: '' }}">
                                <div class="common-error form-text email_error"></div>
                             
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                                <input type="email" class="form-control" id="cn2a2_rv4_email" name="cn2a2_rv4_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->cn2a2_rv4_email: '' }}">
                                <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                                <input type="text" class="form-control" id="ceo_nominee2_apr2_username" name="ceo_nominee2_apr2_username" placeholder="User Name " value="{{ $workflows != '' ? $workflows->ceo_nominee2_apr2_username: '' }}">
                                <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                                <input type="email" class="form-control" id="ceo_nominee2_apr2_email" name="ceo_nominee2_apr2_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->ceo_nominee2_apr2_email: '' }}">
                                <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                               
                            </tr>
                            <tr> <td><i class="m-0 fa fa-plus-circle" style="position: relative;left: 50%; font-size:28px" aria-hidden="true"></i></td></tr>
                            <tr>
                              <td class="text-center" >
                                <!-- <label>CEO <span class="mandatory_input">*</span></label> -->
                                <select class="form-control " name="dep7" id="dep7">
                                <option value="">Select Department </option>
                                <option value="Budget Capex" {{ ($workflows !='' ? $workflows->
                                      dep7: '' )=='Budget Capex' ? 'selected'
                                                : '' }}>Budget Capex</option>
                                    <option value="Budget Opex"{{ ($workflows !='' ? $workflows->
                                      dep7: '' )=='Budget Opex' ? 'selected'
                                                : '' }}>Budget Opex</option>
                                    <option value="CPMG" {{ ($workflows !='' ? $workflows->
                                      dep7: '' )=='CPMG' ? 'selected'
                                                : '' }}>CPMG</option>
                                    <option value="CEO Nominee1" {{ ($workflows !='' ? $workflows->
                                      dep7: '' )=='CEO Nominee1' ? 'selected'
                                                : '' }}>CEO Nominnee1</option>
                                    <option value="CEO Nominee2 A1"{{ ($workflows !='' ? $workflows->
                                      dep7: '' )=='CEO Nominee2 A1' ? 'selected'
                                                : '' }}>CEO Nominee2 A1</option>
                                    <option value="CEO Nominee2 A2" {{ ($workflows !='' ? $workflows->
                                      dep7: '' )=='CEO Nominee2 A2' ? 'selected'
                                                : '' }}>CEO Nominee2 A2</option>
                                    <option value="CEO" {{ ($workflows !='' ? $workflows->
                                      dep7: '' )=='CEO' ? 'selected'
                                                : '' }}>CEO</option>
                                  </select>
                                </td>
                              
                                
                                <td class="text-center">
                                    <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                      <input type="text" class="form-control" id="ceo_rv1_uname" name="ceo_rv1_uname" placeholder="User Name" value="{{ $workflows != '' ? $workflows->ceo_rv1_uname: '' }}">
                      <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                      <input type="email" class="form-control" id="ceo_rv1_email" name="ceo_rv1_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->ceo_rv1_email: '' }}">
                      <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                            

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                      <input type="text" class="form-control" id="ceo_rv2_uname" name="ceo_rv2_uname" placeholder="User Name" value="{{ $workflows != '' ? $workflows->ceo_rv2_uname: '' }}">
                      <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                      <input type="email" class="form-control" id="ceo_rv2_email" name="ceo_rv2_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->ceo_rv2_email: '' }}">
                      <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                        
                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                      <input type="text" class="form-control" id="ceo_rv3_uname" name="ceo_rv3_uname" placeholder="User Name" value="{{ $workflows != '' ? $workflows->ceo_rv3_uname: '' }}">
                      <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                      <input type="email" class="form-control" id="ceo_rv3_email" name="ceo_rv3_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->ceo_rv3_email: '' }}">
                      <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                      <input type="text" class="form-control" id="ceo_rv4_uname" name="ceo_rv4_uname" placeholder="User Name" value="{{ $workflows != '' ? $workflows->ceo_rv4_uname: '' }}">
                      <div class="common-error form-text email_error"></div>
                             
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                      <input type="email" class="form-control" id="ceo_rv4_email" name="ceo_rv4_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->ceo_rv4_email: '' }}">
                      <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center" >
                                  <div class="row">
                                         <div class="col-6">
                                         <!-- <label>User Name <span class="mandatory_input">*</span></label> -->
                      <input type="text" class="form-control" id="ceo_username" name="ceo_username" placeholder="User Name" value="{{ $workflows != '' ? $workflows->ceo_username: '' }}">
                      <div class="common-error form-text email_error"></div>
                                         </div>

                                         <div class="col-6">
                                         <!-- <label>Email Id <span class="mandatory_input">*</span></label> -->
                      <input type="email" class="form-control" id="ceo_email" name="ceo_email" placeholder="Email Id" value="{{ $workflows != '' ? $workflows->ceo_email: '' }}">
                      <div class="common-error form-text mobile_error"></div>
                                        </div>
                                    </div>
                                </td>
                               
                            </tr>
                        </tbody>

                   </table>
                 
                  
                    </div>
 
              <div class="form-group row">
                </div>     
                
                <div class="card">
                        
                   <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                              <button type="submit" class="btn btn-success ">Submit</button>
                              <button type="reset" class="btn btn-secondary" onclick="history.back();">Cancel</button>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <!-- /.card -->
            </form>
          </div>
          <!-- /.col -->
        </div>
        </div>
        <!-- /.container-fluid -->
    </section>


   
@endsection

@push('script')

<script src="{{asset('admin/js/workflow.js')}}"></script>

@endpush