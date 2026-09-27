<!doctype html>
<html lang="en">

<head>


  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href='https://fonts.googleapis.com/css?family=Cedarville Cursive' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Shadows Into Light Two' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Courier New' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Satisfy' rel='stylesheet'>

  <title>NV Service, Details!</title>

  <style>
    .container-fluid {
      width: 98%;
      margin-left: 1%;
      margin-right: 1%;
      font-family: Arial, sans-serif;
    }
     td{
           word-break: break-word !important;
        }

    .container-fluid .fileds p {
      color: gray;

    }

    .container-fluid .fileds {
      margin-top: -5px;
    }

    .implementation-period .to-date {
      background-color: #d3d3d3;
      display: inline-block;
      color: black;
      font-size: 15px;
      padding: 5px 10px;
      margin-right: 10px;
    }


    .container-fluid .fileds span {
      color: black;
      font-size: 20px;
    }

    .container-fluid .implementation-period .to-date {
      background-color: #d3d3d3;
      display: inline-block;
      color: black;
      font-size: 16px;
      padding: 5px 10px;
      margin-right: 10px;
    }

    .implementation-period .impl {
      color: black !important;
    }

    .implementation-period .to-date-heading {
      color: black !important;
    }

    .fileds table {
      border-collapse: collapse;
      width: 100%;
    }

    .fileds table td,
    th {
      border: 1px solid #dddddd;
      text-align: left;
      padding: 8px;
      text-align: center;
    }

    .fileds table th {
      background-color: rgb(3, 142, 220);
      color: white;
    }

    .main-heaing table {
      border-collapse: collapse;
      width: 100%;
    }

    .main-heaing table th {
      background-color: rgb(3, 142, 220);
      color: white;
      border: 1px solid rgb(3, 142, 220);
    }

    .main-heaing table .th1 {
      text-align: center;
    }

    .main-heaing table .th2 {
      text-align: right;
    }

    table td,
    th {
      font-size: 9px !important;
    }

    th,td{
      padding: 1px !important;
    }

    .padding-text1 p{
          padding: 0px;
          margin: 0px;
          font-size: 9px !important;
        }
        .bottom-heading
        {
                position: absolute;
                bottom: 20px;
                font-size: 12px;
                font-weight: 700;
        }
  </style>

</head>



<body data-responsejs='{"create":[{"prop":"width", "prefix":"min-device-width-", "breakpoints":[0, 320, 481, 641, 767, 961, 1025, 1281] }]}'>

  @php
  $user = \Auth()->user();
  $nv_data = App\Models\NeedValidation::where('id', $id)->first();
  $data = App\Models\NVService::where('nv_id', $id)->orderBy('id', 'desc')->first();
  $initiated_date = App\Models\NVService::select('created_at')->where('nv_id', $id)->orderBy('id', 'desc')->first();
  $initiated_by = App\Models\User::select('name','id','role_id','signature_id','image','department_id')->where('id', $data->user_id)->first();
  $ini_role = App\Models\Role::select('name','id')->where('id', $initiated_by->role_id)->first();

  @endphp



  @php
  $user = \Auth()->user();
  $data = App\Models\NVService::where('nv_id', $id)->orderBy('id', 'desc')->first();

  $Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $data->id)->exists();

  if ($Nvsericestatus) {
  $Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $data->id)->first();
  } else {
  $Nvsericestatus = App\Models\Nvsericestatus::where('material_id', $data->id)->first();
  }
  $iniciator_id = App\Models\NeedValidation::where('id', $Nvsericestatus->nv_id)->first();

  $employees_data = App\Models\Employee::where('user_id', $iniciator_id->user_id)->first();
  $dept_name = App\Models\SupDept::where('id',$employees_data->super_department)->first('name');
  
  $employee = App\Models\Employee::with('department')->where("user_id", $data->user_id)->first();

    if ($employee && $employee->department) {
        $department = $employee->department;
        
        $hod = $department->dep_hod;
        $rv1 = $department->dep_rew1;
        $rv2 = $department->dep_rew2;
        $rv3 = $department->dep_rew3;
        $rv4 = $department->dep_rew4;
        $group_cio = $department->group_cio;
    } else {
        $hod = $rv1 = $rv2 = $rv3 = $rv4 = $group_cio = null;
    }
  @endphp

<header>
  <table>
      <tr>
          <td style="width: 33.3%;"><img style="margin-left:50px" src="{{ public_path('theme/dist/img/pdf-logo.png') }}" alt="logo"><br></td>
          
          <td> <p style="font-size: 17px !important; font-weight: bold;color:black; margin-top:17px; margin-left:230px;">BSES Rajdhani Power Limited </p></td>
      </tr>
  </table>
</header>




  <!-- <div id="watermark">
    <img src="{{ public_path('theme/dist/img/watermark.png') }}" alt="Ashu">
  </div> -->



  <div class="container-fluid">
  <div class="main-heaing">

<table>
  <tr>
    <th class="th1">
     <strong></strong> Need Validation Approval Note
    </th>
  </tr>
</table>

</div>
    <table style="width:100%; border:1px solid black; border-collapse: separate !important;">

      <tr style="border:1px solid black;">
        <td class=" padding-text" style="border:1px solid black;width:90px;" ><strong>NV Number</strong></td>
        <td class="padding-text" style="border:1px solid black;">
          @if(!empty($service_details))
          NV/{{ ($nv_data->service->name)}}/{{ $nv_data->budget_type }}/{{ $service_details->department->name }}/FY{{ $nv_data->fiscal_year }}/00{{
          $service_details->nv_id }}
          @endif
        </td>

        <td class=" padding-text" style="border:1px solid black;"><strong>Date</strong></td>
        <td class="padding-text" style="border:1px solid black;"> @if (!empty($service_details->created_at))
          {{date('d-M-y h:i A', strtotime($service_details->created_at)) }}
          @else
          @endif
        </td>
        <td class=" padding-text" style="border:1px solid black;"><strong>Department</strong></td>
        <td class="padding-text" colspan="2" style="border:1px solid black;">
          @if (!empty($dept_name->name))
          {{ $dept_name->name }}
          @else
          @endif
        </td>
        
      </tr>
      <tr style="border:1px solid black;">
        
        <td class=" padding-text" style="border:1px solid black;"><strong>Sub Department</strong></td>
        <td class="padding-text" style="border:1px solid black;">
          @if (!empty($service_details->dept_id))
          {{ $service_details->department->name }}
          @else
          @endif
        </td>
        <td class="" style="border:1px solid black;"><strong>Company</strong> </td>
        <td  class="padding-text" style="border:1px solid black;">
          @if (!empty($service_details->company_id))
          {{ $service_details->division->name }}
          @else
          @endif
        </td>
        <td class=" padding-text" style="border:1px solid black;"><strong>Capex / Opex</strong></td>
        <td class="padding-text" colspan="2" style="border:1px solid black;">
          @if (!empty($nv_data->budget_type))
          {{ $nv_data->budget_type}}
          @else
          @endif
        </td>
       
      </tr>
      

      <tr>
        <td class="padding-text" style="border:1px solid black;"><strong>Proposal Name</strong></td>
        <td colspan="6" class="padding-text" style="border:1px solid black;">
       

          @if (!empty($service_details->proposal_name))
          @php
              $proposalName = $service_details['proposal_name'];
          @endphp
        
              {{ $proposalName }}
          
      @else
          <span></span>
      @endif
        </td>
      </tr>

      <tr>

        <td class=" padding-text" style="border:1px solid black;"><strong>Broad Justification</strong></td>
        <td colspan="6" class="padding-text padding-text1" style="border:1px solid black;">
         

          @if (!empty($service_details->broad_just))
          @php
              $just_PropName = $service_details['broad_just'];
          @endphp
       
              {!! $just_PropName !!}
       
      @else
          <span></span>
      @endif
        </td>
        
      </tr>
      <tr>
      <td  style="border:1px solid black;  "><strong>Budgetory Provision</strong> 
      <!-- <strong style="font-size:10px">(Approved / Additional)</strong> -->
    </td>
        <td  class="padding-text" style="border:1px solid black;">
	{{--
        @if (!empty($service_details->approved_budget) && !empty($service_details->add_budget))
        Approved + Additional
        @elseif(!empty($service_details->approved_budget) && empty($service_details->add_budget))
        Approved
        @elseif(empty($service_details->approved_budget) && !empty($service_details->add_budget))
        Additional
        @else
        Approved
        @endif
	--}}
{{--
	@if (!empty($material_details->approved_budget) && (float)$material_details->add_budget > 0)
    			Approved + Additional
		@elseif (!empty($material_details->approved_budget))
    			Approved
		@elseif ((float)$material_details->add_budget > 0)
    			Additional
		@else
	@endif

--}}
			@if (!empty($service_details->approved_budget) && (float)$service_details->add_budget > 0)
    Approved + Additional
@elseif (!empty($service_details->approved_budget))
    Approved
@elseif ((float)$service_details->add_budget > 0)
    Additional
@else
@endif
        </td>
        <td class=" padding-text" style="border:1px solid black;"><strong>DERC Status</strong></td>
        <td class="padding-text" style="border:1px solid black;" >
          @if (!empty($service_details->derc_approval))
          {{ $service_details != '' ? $service_details['derc_approval'] : '' }}
          @else
          @endif
        </td>
        <td class=" padding-text" style="border:1px solid black;"><strong>Cost Estimate </strong></td>
        <td class="padding-text" style="border:1px solid black;" colspan="2"> &nbsp;
        @if (!empty($service_details->approved_budget) && !empty($service_details->add_budget))
                    @php
                        $approved_budget = $service_details->approved_budget;
                        $formatted_budget_appr = 0;
                        if (strlen(round($approved_budget)) >= 8) {
                            $crore = $approved_budget / 10000000;
                            $crores = floor($crore * 100) / 100;
                            $formatted_budget_appr = number_format($crores, 2) . '  Cr';
                        }else{
                            $formatted_budget_appr = number_format($approved_budget, 2) . '  Rs.';
                        }

                        $add_budget = $service_details->add_budget;
                        $formatted_budget_addl = 0;
                        if (strlen(round($add_budget)) >= 8) {
                            $crore = $add_budget / 10000000;
                            $crores = floor($crore * 100) / 100;
                            $formatted_budget_addl = number_format($crores, 2) . '  Cr';
                        }else{
                            $formatted_budget_addl = number_format($add_budget, 2) . '  Rs.';
                        }
                      
                    @endphp
                        
			{{--
                   // <span style="color:black;">
                   // Appr : {{$formatted_budget_appr}} , Addl : {{$formatted_budget_addl}}</span>
			--}}
			<span style="color:black;">
    Appr : {{ $formatted_budget_appr }}
    @if($formatted_budget_addl != '0.00 Rs.' && $formatted_budget_addl != '0.00  Rs.' && $formatted_budget_addl != '0')
        , Addl : {{ $formatted_budget_addl }}
    @endif
</span>
          
                    @elseif(!empty($service_details->approved_budget) && empty($service_details->add_budget))
                    @php
                    $approved_budget = $service_details->approved_budget;
                        $formatted_budget_appr = 0;
                        if (strlen(round($approved_budget)) >= 8) {
                            $crore = $approved_budget / 10000000;
                            $crores = floor($crore * 100) / 100;
                            $formatted_budget_appr = number_format($crores, 2) . '  Cr';
                        }else{
                            $formatted_budget_appr = number_format($approved_budget, 2) . '  Rs.';
                        }
                    @endphp
                    Appr : {{$formatted_budget_appr}}
                    @elseif(empty($service_details->approved_budget) && !empty($service_details->add_budget))
                    @php
                    $add_budget = $service_details->add_budget;
                        $formatted_budget_addl = 0;
                        if (strlen(round($add_budget)) >= 8) {
                            $crore = $add_budget / 10000000;
                            $crores = floor($crore * 100) / 100;
                            $formatted_budget_addl = number_format($crores, 2) . '  Cr';
                        }else{
                            $formatted_budget_addl = number_format($add_budget, 2) . '  Rs.';
                        }
                    @endphp
                    Addl : {{$formatted_budget_addl}}
                    @else
                        <span></span>
                    @endif
        </td>
        
        
      </tr>
      <tr>
        
        <td class=" padding-text" style="border:1px solid black;"><strong>Proposal Type </strong> 
          <!-- <strong style="font-size:10px">(One-Time / Regular)</strong> -->
        </td>
        <td  class="padding-text" style="border:1px solid black;">
          @if (!empty($nv_data->proposal_type))
          {{ $nv_data->proposal_type }}
          @else
          @endif
        </td>
        <td class=" padding-text" style="border:1px solid black;"><strong>DERC Ref no</strong></td>
        <td class="padding-text" style="border:1px solid black;" >
          @if (!empty($service_details->derc_ref_no))
          {{ $service_details != '' ? $service_details['derc_ref_no'] : '' }}
          @else
          @endif
        </td>
        
        @php
        $from = explode(',', $service_details != '' ? $service_details->implementation_period_from : '');
        $to = explode(',', $service_details != '' ? $service_details->implementation_period_to : '');
        $selectedYear = $service_details != '' ? $service_details->implements_years : '';
        @endphp
        <td class=" padding-text" style="border:1px solid black;width:155px;" colspan="1"><strong>Implementation From:-</strong> &nbsp; @if (!empty($service_details->implementation_period_from))
          <span class="to-date" >{{date('d-m-Y', strtotime(isset($from[0]) ? $from[0] : ''))}}</span>
          {{-- <span class="to-date">{{(isset($from[1]) ? $from[1] : '') }}</span>
          <span class="to-date">{{(isset($from[2]) ? $from[2] : '') }}</span> --}}
          @else
          <span></span>
          @endif</td>
        
        <td class=" padding-text" style="border:1px solid black;width:155px;" colspan="2"> <strong>Implementation To:-</strong> &nbsp; @if (!empty($service_details->implementation_period_to))
          @if($selectedYear == 1 )
          <span  class="to-date" >{{date('d-m-Y', strtotime(isset($to[0]) ? $to[0] : '')) }}</span>
      @elseif($selectedYear == 2) 
          <span  class="to-date" >{{date('d-m-Y', strtotime(isset($to[1]) ? $to[1] : '' )) }}</span>
      @else
          <span  class="to-date" >{{date('d-m-Y', strtotime(isset($to[2]) ? $to[2] : '' )) }}</span>
      @endif
          {{-- <span class="to-date">{{(isset($to[0]) ? $to[0] : '') }}</span>
          <span class="to-date">{{(isset($to[1]) ? $to[1] : '' ) }}</span> --}}
          
          @else
          <span> </span>
          @endif</td>
        
     
       
      </tr>

    
      <tr>
        <td class=" padding-text" style="border:1px solid black;"><strong>Special Remarks</strong></td>
        <td class="padding-text" style="border:1px solid black;" colspan="6">
         

              @if (!empty($service_details->special_remarks))
              @php
                  $special_remarksName = $service_details['special_remarks'];
              @endphp
            
                  {{ $special_remarksName }}
            
          @else
              <span></span>
          @endif
             
      </td>
      
      </tr>
      
      
    </table>

        <div class="fileds" style="padding-top:10px">

          <table style="width:100%;">

            <tr>
              <th style="width:13.3%;"> </th>
              <th style="width:13.3%;">Initiated By</th>
              <th style="width:13.3%;">Reviewed By</th>
              <th style="width:13.3%;">Reviewed By</th>
              <th style="width:13.3%;">Reviewed By</th>
              <th style="width:13.3%;">Reviewed By</th>
              <th style="width:13.3%;">HOD</th>
            </tr>

            <tr>
              <th style="width:13.3%;">Remarks</th>

              <td style="border: 1px solid #dddddd !important;">

              </td>
            
              <td style="border: 1px solid #dddddd !important;">
              
                @if($Nvsericestatus->rv1_status == 1 || $Nvsericestatus->rv1_status == 2)
                <span>{{ $Nvsericestatus->rv1_remark }}</span>
                @endif
              </td>
            
              <td style="border: 1px solid #dddddd !important;">
                @if($Nvsericestatus->rv2_status == 1 || $Nvsericestatus->rv2_status == 2)
                <span>{{ $Nvsericestatus->rv2_remark }}</span>
                @endif
              </td>
              
              <td style="border: 1px solid #dddddd !important;">
                @if($Nvsericestatus->rv3_status == 1 || $Nvsericestatus->rv3_status == 2)
                <span>{{ $Nvsericestatus->rv3_remark }}</span>
                @endif
              </td>
          
              <td style="border: 1px solid #dddddd !important;">
                @if($Nvsericestatus->rv4_status == 1 || $Nvsericestatus->rv4_status == 2)
                <span>{{ $Nvsericestatus->rv4_remark }}</span>
                @endif
              
              </td>
            
              <td style="border: 1px solid #dddddd !important;">
                @if($Nvsericestatus->hod_status == 1 || $Nvsericestatus->hod_status == 2)
                <span>{{ $Nvsericestatus->hod_remark }}</span>
                @endif
              </td>

            </tr>

            <tr>
              <th tyle="width:13.3%;">Signature <br> Date & Time </th>

              <td> @if (!empty($service_details->user_id))

                @if( $initiated_by->signature_id == 1)
                <span style="font-family: 'Cedarville Cursive', cursive;">{{ $initiated_by->name ?? '' }}</span>
                @elseif($initiated_by->signature_id==2)
                <span style="font-family: 'Courier New', Courier, monospace;">{{ $initiated_by->name ?? '' }}</span>
                @elseif($initiated_by->signature_id==3)
                <span style=" font-family: 'Satisfy', cursive;">{{ $initiated_by->name ?? '' }}</span>
                @elseif($initiated_by->signature_id==4)
                <span style=" font-family: 'Shadows Into Light Two', cursive;">{{ $initiated_by->name ?? '' }}</span>
                @elseif($initiated_by->signature_id==5)<img src="{{public_path('/images/'.$initiated_by->image)}}"
                  style="height:auto;width:80px; margin-top:10px;" />
                @else
                <span> </span>
                @endif
                @endif
                <br>
                @if (!empty($service_details->created_at))

                <span>{{ date('d-M-y h:i A', strtotime($service_details->created_at)) }}</span>

                @else

                <span></span>

                @endif

              </td>


              @php
              $user_rv1= App\Models\User::where("id",$Nvsericestatus->rv1_id)->first();
              $user_rv2= App\Models\User::where("id",$Nvsericestatus->rv2_id)->first();
              $user_rv3= App\Models\User::where("id",$Nvsericestatus->rv3_id)->first();
              $user_rv4= App\Models\User::where("id",$Nvsericestatus->rv4_id)->first();
              $user_hod= App\Models\User::where("id",$Nvsericestatus->hod_id)->first();

              $user_ces_rew1= App\Models\User::where("id",$Nvsericestatus->ces_rew1_id)->first();
              $user_ces_rew2= App\Models\User::where("id",$Nvsericestatus->ces_rew2_id)->first();
              $user_ces_rew3= App\Models\User::where("id",$Nvsericestatus->ces_rew3_id)->first();
              $user_ces_rew4= App\Models\User::where("id",$Nvsericestatus->ces_rew4_id)->first();
              $user_ces= App\Models\User::where("id",$Nvsericestatus->ces_id)->first();

              $user_cpmg_rew1= App\Models\User::where("id",$Nvsericestatus->work_rew1_id)->first();
              $user_cpmg_rew2= App\Models\User::where("id",$Nvsericestatus->work_rew2_id)->first();
              $user_cpmg_rew3= App\Models\User::where("id",$Nvsericestatus->work_rew3_id)->first();
              $user_cpmg_rew4= App\Models\User::where("id",$Nvsericestatus->work_rew4_id)->first();
              $user_cpmg= App\Models\User::where("id",$Nvsericestatus->cpmg_id)->first();

              $user_cto_rew1= App\Models\User::where("id",$Nvsericestatus->work_rew1dep2_id)->first();
              $user_cto_rew2= App\Models\User::where("id",$Nvsericestatus->work_rew2dep2_id)->first();
              $user_cto_rew3= App\Models\User::where("id",$Nvsericestatus->work_rew3dep2_id)->first();
              $user_cto_rew4= App\Models\User::where("id",$Nvsericestatus->work_rew4dep2_id)->first();
              $user_cto= App\Models\User::where("id",$Nvsericestatus->cto_id)->first();

              $user_ceon1r1= App\Models\User::where("id",$Nvsericestatus->work_rew1dep3_id)->first();
              $user_ceon1r2= App\Models\User::where("id",$Nvsericestatus->work_rew2dep3_id)->first();
              $user_ceon1r3= App\Models\User::where("id",$Nvsericestatus->work_rew3dep3_id)->first();
              $user_ceon1r4= App\Models\User::where("id",$Nvsericestatus->work_rew4dep3_id)->first();
              $user_ceon1= App\Models\User::where("id",$Nvsericestatus->ceo_nominee_id)->first();

              $user_ceon2r1= App\Models\User::where("id",$Nvsericestatus->work_rew1dep4_id)->first();
              $user_ceon2r2= App\Models\User::where("id",$Nvsericestatus->work_rew2dep4_id)->first();
              $user_ceon2r3= App\Models\User::where("id",$Nvsericestatus->work_rew3dep4_id)->first();
              $user_ceon2r4= App\Models\User::where("id",$Nvsericestatus->work_rew4dep4_id)->first();
              $user_ceon2= App\Models\User::where("id",$Nvsericestatus->ceo_nominee2_id)->first();

              $user_ceor1= App\Models\User::where("id",$Nvsericestatus->work_rew1dep5_id)->first();
              $user_ceor2= App\Models\User::where("id",$Nvsericestatus->work_rew2dep5_id)->first();
              $user_ceor3= App\Models\User::where("id",$Nvsericestatus->work_rew3dep5_id)->first();
              $user_ceor4= App\Models\User::where("id",$Nvsericestatus->work_rew4dep5_id)->first();
              $user_ceo= App\Models\User::where("id",$Nvsericestatus->ceo_id)->first();

              $user_groupcio= App\Models\User::where("id",$Nvsericestatus->groupcio_id)->first();

              @endphp
              <td> 
                @if($Nvsericestatus->rv1_status == 1 || $Nvsericestatus->rv1_status == 2)
                @if( $user_rv1->signature_id == 1)
                <span style="font-family: 'Cedarville Cursive', cursive;">{{ $user_rv1->name ?? '' }}</span>
                @elseif($user_rv1->signature_id==2)
                <span style="font-family: 'Courier New', Courier, monospace;">{{ $user_rv1->name ?? '' }}</span>
                @elseif($user_rv1->signature_id==3)
                <span style=" font-family: 'Satisfy', cursive;">{{ $user_rv1->name ?? '' }}</span>
                @elseif($user_rv1->signature_id==4)
                <span style=" font-family: 'Shadows Into Light Two', cursive;">{{ $user_rv1->name ?? '' }}</span>
                @elseif($user_rv1->signature_id==5)
                <img src="{{public_path('/images/'.$user_rv1->image)}}" style="height:auto;width:80px; margin-top:10px;" />
                @else
                <span> </span>
                @endif
                @endif
                <br>
                @if($Nvsericestatus->rv1_status == 1 || $Nvsericestatus->rv1_status == 2)
                <span>{{ date('d-M-y h:i A', strtotime($Nvsericestatus->rv1_timestamp)) }} </span>
                @endif
              </td>

              <td> 
                @if($Nvsericestatus->rv2_status == 1 || $Nvsericestatus->rv2_status == 2)
                @if( $user_rv2->signature_id == 1)
                <span style="font-family: 'Cedarville Cursive', cursive;">{{ $user_rv2->name ?? '' }}</span>
                @elseif($user_rv2->signature_id==2)
                <span style="font-family: 'Courier New', Courier, monospace;">{{ $user_rv2->name ?? '' }}</span>
                @elseif($user_rv2->signature_id==3)
                <span style=" font-family: 'Satisfy', cursive;">{{ $user_rv2->name ?? '' }}</span>
                @elseif($user_rv2->signature_id==4)
                <span style=" font-family: 'Shadows Into Light Two', cursive;">{{ $user_rv2->name ?? '' }}</span>
                @elseif($user_rv2->signature_id==5)
                <img src="{{public_path('/images/'.$user_rv2->image)}}" style="height:auto;width:80px; margin-top:10px;" />
                @else
                <span> </span>
                @endif
                @endif
                <br>
                @if($Nvsericestatus->rv2_status == 1 || $Nvsericestatus->rv2_status == 2)
                <span>{{ date('d-M-y h:i A', strtotime($Nvsericestatus->rv2_timestamp)) }} </span>
                @endif
              </td>
              <td> 
                @if($Nvsericestatus->rv3_status == 1 || $Nvsericestatus->rv3_status == 2)
                @if( $user_rv3->signature_id == 1)
                <span style="font-family: 'Cedarville Cursive', cursive;">{{ $user_rv3->name ?? '' }}</span>
                @elseif($user_rv3->signature_id==2)
                <span style="font-family: 'Courier New', Courier, monospace;">{{ $user_rv3->name ?? '' }}</span>
                @elseif($user_rv3->signature_id==3)
                <span style=" font-family: 'Satisfy', cursive;">{{ $user_rv3->name ?? '' }}</span>
                @elseif($user_rv3->signature_id==4)
                <span style=" font-family: 'Shadows Into Light Two', cursive;">{{ $user_rv3->name ?? '' }}</span>
                @elseif($user_rv3->signature_id==5)
                <img src="{{public_path('/images/'.$user_rv3->image)}}" style="height:auto;width:80px; margin-top:10px;" />
                @else
                <span> </span>
                @endif
                @endif
                <br>
                @if($Nvsericestatus->rv3_status == 1 || $Nvsericestatus->rv3_status == 2)
                <span>{{ date('d-M-y h:i A', strtotime($Nvsericestatus->rv3_timestamp)) }} </span>
                @endif
              </td>
              <td> 
                @if($Nvsericestatus->rv4_status == 1 || $Nvsericestatus->rv4_status == 2)
                @if( $user_rv4->signature_id == 1)
                <span style="font-family: 'Cedarville Cursive', cursive;">{{ $user_rv4->name ?? '' }}</span>
                @elseif($user_rv4->signature_id==2)
                <span style="font-family: 'Courier New', Courier, monospace;">{{ $user_rv4->name ?? '' }}</span>
                @elseif($user_rv4->signature_id==3)
                <span style=" font-family: 'Satisfy', cursive;">{{ $user_rv4->name ?? '' }}</span>
                @elseif($user_rv4->signature_id==4)
                <span style=" font-family: 'Shadows Into Light Two', cursive;">{{ $user_rv4->name ?? '' }}</span>
                @elseif($user_rv4->signature_id==5)
                <img src="{{public_path('/images/'.$user_rv4->image)}}" style="height:auto;width:80px; margin-top:10px;" />
                @else
                <span> </span>
                @endif
                @endif
                <br>
                @if($Nvsericestatus->rv4_status == 1 || $Nvsericestatus->rv4_status == 2)
                <span>{{ date('d-M-y h:i A', strtotime($Nvsericestatus->rv4_timestamp)) }} </span>
                @endif
              </td>
              <td> 
                
                @if($Nvsericestatus->hod_status == 1 || $Nvsericestatus->hod_status == 2)
                @if( $user_hod->signature_id == 1)
                <span style="font-family: 'Cedarville Cursive', cursive;">{{ $user_hod->name ?? '' }}</span>
                @elseif($user_hod->signature_id==2)
                <span style="font-family: 'Courier New', Courier, monospace;">{{ $user_hod->name ?? '' }}</span>
                @elseif($user_hod->signature_id==3)
                <span style=" font-family: 'Satisfy', cursive;">{{ $user_hod->name ?? '' }}</span>
                @elseif($user_hod->signature_id==4)
                <span style=" font-family: 'Shadows Into Light Two', cursive;">{{ $user_hod->name ?? '' }}</span>
                @elseif($user_hod->signature_id==5)
                <img src="{{public_path('/images/'.$user_hod->image)}}" style="height:auto;width:80px; margin-top:10px;" />
                @else
                <span> </span>
                @endif
                @endif
                <br>
                @if($Nvsericestatus->hod_status == 1 || $Nvsericestatus->hod_status == 2)
                <span>{{ date('d-M-y h:i A', strtotime($Nvsericestatus->hod_timestamp)) }} </span>
                @endif
              </td>

            </tr>

            <tr>

              <th tyle="width:13.3%;">Name <br> Designation </th>

              <td> @if (!empty($service_details->user_id))

                <span>{{ $initiated_by->name }}</span>

                @else

                <span> </span>

                @endif
                <br>
                @if (!empty($service_details->user_id))



                <span>{{($ini_role->name )}}</span>

                @else

                <span> </span>

                @endif

              </td>
            
              <td>
                @if($Nvsericestatus->rv1_status == 1 || $Nvsericestatus->rv1_status == 2)
                <span>{{ $user_rv1->name ?? ''}} </span>
                @endif 
                <br>
                @if($Nvsericestatus->rv1_status == 1 || $Nvsericestatus->rv1_status == 2)
                <span>Reviewer1</span>
                @endif
              </td>
              <td>
                @if($Nvsericestatus->rv2_status == 1 || $Nvsericestatus->rv2_status == 2)
                <span>{{ $user_rv2->name ?? ''}} </span>
                @endif 
                <br>
                @if($Nvsericestatus->rv2_status == 1 || $Nvsericestatus->rv2_status == 2)
                <span>Reviewer2</span>
                @endif 
              </td>
              <td>
                @if($Nvsericestatus->rv3_status == 1 || $Nvsericestatus->rv3_status == 2)
                <span>{{ $user_rv3->name ?? ''}} </span>
                @endif 
                <br>
                @if($Nvsericestatus->rv3_status == 1 || $Nvsericestatus->rv3_status == 2)
                <span>Reviewer3 </span>
                @endif 
              </td>
              <td>
                @if($Nvsericestatus->rv4_status == 1 || $Nvsericestatus->rv4_status == 2)
                <span>{{ $user_rv4->name ?? ''}} </span>
                @endif 
                <br>
                @if($Nvsericestatus->rv4_status == 1 || $Nvsericestatus->rv4_status == 2)
                <span>Reviewer4</span>
                @endif 

              </td>
              <td>
                @if (!empty($service_details->user_id))

                

                @if($Nvsericestatus->hod_status == 1 || $Nvsericestatus->hod_status == 2)
                <span>{{ $user_hod->name ?? ''}} </span>
                @endif
                @if ($Nvsericestatus->hod_status == 1 || $Nvsericestatus->hod_status == 2)
                    <span>HOD</span>
                @endif
                @endif
              
              
              </td>

            </tr>
            
          

          </table>

        </div>

        <div class="fileds" style="padding-top:10px;">

          <table>

              <tr>

                <th style="width:13%;"></th>
                @if(!empty($group_cio))
                <th style="width:12%;">Group Head</th>
                @endif
                @php
                if($nv_data->budget_type == 'CAPEX'){
                    $worflowStages = App\Models\Workflow::where('status',1)->get();
                }else{
                    $worflowStages = App\Models\OpexWorkflow::where('status',1)->get();
                }
                @endphp
                @foreach($worflowStages as $workflowStage)
                <th>{{getDepartmentName($workflowStage->work_dep)}}</th>
                @endforeach


              </tr>

              <tr>
                <th>Remarks</th> 

                    @if(!empty($group_cio))
                    <td>
                      @if($Nvsericestatus->groupcio_status == 1 || $Nvsericestatus->groupcio_status == 2)
                      <span>{{ $Nvsericestatus->groupcio_remark }}</span>
                      @endif 
                    </td>
                    @endif

                    @foreach($worflowStages as $workflowStage)
                        @php 
                        $workflow_data = DB::table('capex_workflows_status')
                                        ->where('service_id', $service_details->id)->whereIn('nv_stage_status',[1,2])
                                        ->where('nv_budget_type', $nv_data->budget_type)
                                        ->where('transfer_to_nominee1', '0')
                                        ->where('department_id', $workflowStage->work_dep)
                                        ->where('reviewer_name', 'approver')
                                        ->first();
                        @endphp
                    
                        
                        <td>
                            @if(!empty($workflow_data))
                            <span>{{$workflow_data->nv_stage_remark ?? ''}}</span>
                            @endif
                        </td>
                    @endforeach
              
            

              </tr>

              <tr>
                <th> Signature <br> Date & Time </th>

                    @if(!empty($group_cio))
                    <td>
                        @if (!empty($service_details->user_id))

                      

                        @if($Nvsericestatus->groupcio_status == 1 || $Nvsericestatus->groupcio_status == 2)
                        @if( $user_groupcio->signature_id == 1)
                        <span style="font-family: 'Cedarville Cursive', cursive;">{{ $user_groupcio->name ?? '' }}</span>
                        @elseif($user_groupcio->signature_id==2)
                        <span style="font-family: 'Courier New', Courier, monospace;">{{ $user_groupcio->name ?? '' }}</span>
                        @elseif($user_groupcio->signature_id==3)
                        <span style=" font-family: 'Satisfy', cursive;">{{ $user_groupcio->name ?? '' }}</span>
                        @elseif($user_groupcio->signature_id==4)
                        <span style=" font-family: 'Shadows Into Light Two', cursive;">{{ $user_groupcio->name ?? '' }}</span>
                        @elseif($user_groupcio->signature_id==5)
                        <img src="{{public_path('/images/'.$user_groupcio->image)}}"
                          style="height:auto;width:80px; margin-top:10px;" />
                        @else
                        <span> </span>
                        @endif

                      
                        @endif
                        @endif

                        <br>


                        @if (!empty($service_details->user_id))


                        @if($Nvsericestatus->groupcio_status == 1 || $Nvsericestatus->groupcio_status == 2)
                        <span>{{ date('d-M-y h:i A', strtotime($Nvsericestatus->groupcio_timestamp)) }} </span>
                        @endif
                        @endif
                    


                    </td>
                    @endif
                    @foreach($worflowStages as $workflowStage)
                        @php 
                        $workflow_data = DB::table('capex_workflows_status')
                                        ->where('service_id', $service_details->id)->whereIn('nv_stage_status',[1,2])
                                        ->where('nv_budget_type', $nv_data->budget_type)
                                        ->where('department_id', $workflowStage->work_dep)
                                        ->where('reviewer_name', 'approver')
                                        ->where('transfer_to_nominee1', '0')
                                        ->first();
                            if(!empty($workflow_data)){
                                $stage_user_name = App\Models\User::where("id",$workflow_data->workflow_user_id)->first();
                            }
                        
                        @endphp
                            <td>
                                @if(!empty($workflow_data))
                                @if(in_array($workflow_data->nv_stage_signature, [1,2,3,4]))
                                <span style="font-family: 
                                    @if($workflow_data->nv_stage_signature == 1) 'Cedarville Cursive', cursive;
                                    @elseif($workflow_data->nv_stage_signature == 2) 'Courier New', Courier, monospace;
                                    @elseif($workflow_data->nv_stage_signature == 3) 'Satisfy', cursive;
                                    @elseif($workflow_data->nv_stage_signature == 4) 'Shadows Into Light Two', cursive;
                                    @endif">
                                    {{ $stage_user_name->name ?? '' }}</span>
                                @elseif($workflow_data->nv_stage_signature == 5)
                                <img src="{{ public_path('/images/' . $stage_user_name->image) }}"
                                    style="height:auto;width:80px; margin-top:10px;" />
                                @endif  <br>
                                <span>{{ date('d-M-y h:i A', strtotime($workflow_data->nv_stage_timestamp)) }} </span>
                                @endif
                            
                        </td>
                    @endforeach
            
              </tr>

              <tr>

                <th>Name</th>

                    @if(!empty($group_cio))
                    <td>
                      @if (!empty($service_details->user_id))


                      @if($Nvsericestatus->groupcio_status == 1 || $Nvsericestatus->groupcio_status == 2)
                      <span>{{ $user_groupcio->name ?? '' }} </span>
                      @endif
                      @endif
                  
                    </td>
                    @endif

                    @foreach($worflowStages as $workflowStage)
                        @php 
                        $workflow_data = DB::table('capex_workflows_status')
                                        ->where('service_id', $service_details->id)->whereIn('nv_stage_status',[1,2])
                                        ->where('nv_budget_type', $nv_data->budget_type)
                                        ->where('department_id', $workflowStage->work_dep)
                                        ->where('reviewer_name', 'approver')
                                        ->where('transfer_to_nominee1', '0')
                                        ->first();
                            if(!empty($workflow_data)){
                                $stage_user_name = App\Models\User::where("id",$workflow_data->workflow_user_id)->first();
                            }
                        @endphp
                        <td>
                            @if(!empty($workflow_data))
                            <span>{{ $stage_user_name->name ?? '' }} </span>
                            @endif
                    
                        </td>
                    @endforeach 

              </tr>

          </table>

        </div>
   

        <table>
          <tr>
              <td>
                  <footer class="main-footer text-center" style="color:rgb(3, 142, 220); font-size: 12px;">
                      <div class="container">
                          <div class="row">
                              <div class="col">
                                  <strong>&copy;{{ date('Y') }} <a href="#" target="_blank"
                                          style="color:rgb(3, 142, 220);">BSES</a>. All Rights Reserved.</strong>
                              </div>
                          </div>
                      </div>
                  </footer>

              </td>
          </tr>


        </table>
        
        <br>
            @php 
              $ceoStage = DB::table('capex_workflows_status as cws')
                  ->join(DB::raw('(SELECT nv_id, MAX(workflow_serial) as max_serial
                                  FROM capex_workflows_status
                                  GROUP BY nv_id) as latest'),
                      function ($join) {
                          $join->on('cws.nv_id', '=', 'latest.nv_id')
                                  ->on('cws.workflow_serial', '=', 'latest.max_serial');
                      })
                  ->where('cws.service_id', $service_details->id)
                  ->where('cws.nv_budget_type', $nv_data->budget_type)
                  ->first();
                  if(!empty($ceoStage)){
                      $ceo_name = App\Models\User::where("id",$ceoStage->workflow_user_id)->first();
                  }
            @endphp
            <div class="fileds">
              <p style="font-size: 16px;"><span><b>Submitted for your approval please.</b></span></p><br><br>
              @if (!empty($ceoStage))
                  @if ($ceoStage->nv_stage_status == 1 || $ceoStage->nv_stage_status == 2)
                          @if(in_array($ceoStage->nv_stage_signature, [1,2,3,4]))
                              <span id="signature{{ $ceoStage->nv_stage_signature > 1 ? $ceoStage->nv_stage_signature : '' }}">
                                  {{ $ceo_name->name ?? '' }}</span>
                          @elseif($ceoStage->nv_stage_signature == 5)
                          <img src="{{ public_path('/images/' . $ceo_name->image) }}"
                              style="height:auto;width:80px; margin-top:10px;" />
                          @endif

                  @endif

              @endif
              <p style="font-size: 16px;"><span><b>CEO, BRPL</b></span></p>

            </div>


</div>

<!-- <p class="bottom-heading">
  ... indicates that the data could not fit in the column due to single page constraint. Please refer to Annexure 2a/2b for complete details.
  </p> -->

</body>



</html>
