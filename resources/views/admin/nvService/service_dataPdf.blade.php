<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
        <link href='https://fonts.googleapis.com/css?family=Cedarville Cursive' rel='stylesheet'>
        <link href='https://fonts.googleapis.com/css?family=Shadows Into Light Two' rel='stylesheet'>
        <link href='https://fonts.googleapis.com/css?family=Courier New' rel='stylesheet'>
        <link href='https://fonts.googleapis.com/css?family=Satisfy' rel='stylesheet'>
    <title>NV Material, Details!</title>
    <style>
        @page {
            /* size: portrait; */
            size: landscape;

        }

        header {
            position: fixed;
            top: -40px;
            left: -40px;
            right: -40px;
            color: white;
            text-align: center;
        }
        td{
           word-break: break-word !important;
        }

        header table td{
            border: none !important;
        }

        header img{
            width: 200px !important;
            height: auto;
        }


        footer {
            position: fixed;
            bottom: -15px;
            left: -40px;
            right: -40px;
            height: 25px;
        }

        footer table td{
            border: none !important;
        }

        #watermark {
            position: fixed;
            bottom: 10cm;
            left: 5.5cm;
            width: 8cm;
            height: 8cm;
            z-index: 1;
        }

        @media screen {
            body {
                font-family: Arial, sans-serif;
                padding: 10px;
            }

            h1 {
                font-size: 15px;
            }

            p {
                font-size: 12px !important;
            }
        }

        @media print {
            body {
                /* font-family: Arial, sans-serif; */
            font-family: 'DejaVu Sans' !important;

                padding: 0;
            }

            h1 {
                font-size: 15px;
            }

            p {
                font-size: 12px !important;
            }
        }

        .table {
            width: 100%;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .table th,
        .table td {
            padding: 5px;
            word-wrap: break-word;
        }

        .table th,
        .table td {
            font-size: 12px !important;
            white-space: normal;
        }

        .table td {
            padding: 1px 3px 1px 3px !important;
        }

        #watermark img {
            margin: 0;
            position: absolute;
            top: 50%;
            left: -30%;
            width: 400px;
            height: auto;
            filter: grayscale(100%) !important; 
        }

        .card{
            margin-top: 30px;
        }
        body {
        margin-top: 40px; 
            font-family: 'DejaVu Sans' !important;

    }

    
    .just-prop-content{
                     color:#666666; 
                     font-family: Arial, sans-serif!important;
                    }
    .card-body p span{color:#666666; font-family: Arial, sans-serif;}
    h6{background-color: rgb(3, 142, 220);color:white; padding:3px 5px; margin-top:20px; display:block;}
    .sign-box{display:block;  clear:both; margin-bottom:5px;}
  
    .sign-box span { margin-right:5px; margin-left:5px; display:inline-block;}

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

        .h4{
            background-color: rgb(3, 142, 220);
            color: white;
        }

        .table4{
            border-collapse: separate ;
        }

        .table4 td{
           font-size:10px !important;
        }

        .fileds span{
            font-size:12px !important;
        }

        .signature-t th{
            background-color: rgb(3, 142, 220);
            color: white;
        }

        .signature-t{
            margin-top:6px;
        }

    </style>

</head>

<body>
    @php
            // ===== SAFE HELPERS - prevents TypeError on string values in PHP 8+ =====
            if (!function_exists('safeRound')) {
                function safeRound($num, $precision = 0) {
                    return round((float) str_replace(',', '', (string) $num), $precision);
                }
            }
            if (!function_exists('safeNumberFormat')) {
                function safeNumberFormat($num, $decimals = 2) {
                    return number_format((float) str_replace(',', '', (string) $num), $decimals);
                }
            }

            // $id = Request::segment(3);
            $user = \Auth()->user();
            $nv_data = App\Models\NeedValidation::where('id', $id)->first();
            $data = App\Models\NVService::where('nv_id', $id)->orderBy('id', 'desc')->first();
            $initiated_date = App\Models\NVService::select('created_at')->where('nv_id', $id)->orderBy('id', 'desc')->first();
            $initiated_by = App\Models\User::select('name','id')->where('id', $data->user_id)->first();
          
    @endphp
    <header>
        <table >
            <tr>
                <td style="width: 33.3%;"><img style="margin-left:50px" src="{{ public_path('theme/dist/img/pdf-logo.png') }}" alt="logo"></td>
                
                <td> <p style="font-size: 17px !important; font-weight: bold;color:black; margin-top:17px; margin-left:230px;">BSES Rajdhani Power Limited </p></td>
            </tr>
        </table>
    </header>
    

    <footer>
        <table style="border:none;">
            <tr >
                <!-- <td>
                <span style="color:black; font-size:10px; margin-left:50px;">{{'NV' . '/' . $nv_data->budget_type . '/' . $nv_data->fiscal_year . '/' . getDepartmentNameByPro($nv_data->user_id) . '/' . $nv_data->service->name}}</span>
                </td> -->
                {{-- <td >
                    <p style="margin-left:450px;" > 1/20 </p>
                 </td> --}}
            </tr>
        </table>
    </footer>

    <!-- <div id="watermark">
        <img src="{{ public_path('theme/dist/img/watermark.png') }}" alt="Ashu">
    </div> -->

    <main>
        @php
            // $id = Request::segment(3);
            $user = \Auth()->user();
            $data = App\Models\NVService::where('nv_id', $id)->orderBy('id', 'desc')->first();
            $Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $data->id)->exists();
            if ($Nvsericestatus) {
                 $Nvsericestatus = App\Models\Nvsericestatus::where('service_id', $data->id)->first();
            } else {
                $Nvsericestatus = App\Models\Nvsericestatus::where('material_id', $data->id)->first();
            }
            
            $employees_data = App\Models\Employee::where('user_id', $user->id)->first();
        @endphp

        <div>
      

        {{-- <div class="card-body" style="page-break-after: always;"> --}}
            <h5 class="text-center h4"> NV-Service Details <span style="float: right">Annexure 2a</span></h5>

            <table style="width:100%; paddding:0px; margin:0px; border:1px solid black;" class="table4">
                <tr style="  border:1px solid black;">
                    <td style="width:50%;  border:1px solid black;" colspan="2">  
                        
                            <span><b>Department Name :- </b>
                                @if (!empty($service_details->dept_id))
                                <span> {{ $service_details->department->name }} </span>
                                @else
                                <span>  </span>
                                @endif
                            </span>
                    
                    </td>

                    <td style="width:50%;  border:1px solid black;">
                                <span><b>DOP Reference Number :- </b>
                                    @if (!empty($service_details->dop_ref_no))
                                        <span> {{ $service_details != '' ? $service_details['dop_ref_no'] : '' }} </span>
                                    @else
                                    <span> </span>
                                    @endif
                                </span>
                    </td>
                </tr>

                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;">
                            <span><b>Proposal Name :- </b>
                                @if (!empty($service_details->proposal_name))
                                    <span>{{ $service_details != '' ? $service_details['proposal_name'] : '' }}</span>
                                @else
                                    <span>  </span>
                                @endif
                            </span>
                    </td>
                </tr>

                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;">
                            <span style="font-size:12px;"><b>Background:-</b>
                                @if (!empty($service_details->background))
                                {!! ($service_details->background) !!}
                                {{-- <span class="read-more" style="cursor: pointer; color: blue; float:right;">...for detailed view please see Annexure 2a</span> --}}
                            {{-- <span style="font-size:10px;">{!! $service_details['background'] !!} </span> --}}
                                @else
                            <span style="font-size:10px;"> </span>
                            @endif
                            </span>
                    
                    </td>
                </tr>

                
                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;">
                        
                            <span style="font-size:12px;"> <b>Broad Justification:-</b>
                                @if (!empty($service_details->broad_just))
                                {!! ($service_details->broad_just) !!}
                                {{-- <span class="read-more" style="cursor: pointer; color: blue; float:right;">...for detailed view please see Annexure 2a</span> --}}
                                {{-- <span style="font-size:10px;"> {!! $service_details['broad_just'] !!} </span> --}}
                                @else
                            <span style="font-size:10px;"> </span>
                            @endif
                    
                    </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;">
                        
                            <span style="font-size:12px;"> <b>Detailed Justification:-</b>
                                @if (!empty($service_details->just_of_proposal))
                                {!! ($service_details->just_of_proposal) !!}
                                {{-- <span class="read-more" style="cursor: pointer; color: blue; float:right;">...for detailed view please see Annexure 2a</span> --}}
                                {{-- <span style="font-size:10px;"> {!! $service_details['just_of_proposal'] !!} </span> --}}
                                @else
                            <span style="font-size:10px;"> </span>
                            @endif
                    
                    </td>
                </tr>

                <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                    <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold;text-align:center;">
                        Past 3 Years actual cost trend Service
                    </td>
                </tr>
                    
       

                @php
                $year1 = explode(',', $service_details != '' ? $service_details->past_3_year_actual_cost_fy : '');
                $year2 = explode(',', $service_details != '' ? $service_details->past_3_year_actual_cost : '');
                $year3 = explode(',', $service_details != '' ? $service_details->past_3_year_actual_cost_service : '');
                $year = $nv_year->fiscal_year;
                $yr_l = substr($year, 5);
                $yr_f = substr($year, 0, 4);
                @endphp

                <tr style="border:1px solid black;">
                    <td style="border:1px solid black; width:33.3%;">
                        <span><strong>Year :- </strong>{{ $yr_f - 1 }}-{{ $yr_l - 1 }}</span>
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                                <span style="color:black;"><b>Cost :- </b></span>
                                        @if (!empty($service_details->past_3_year_actual_cost))
                                        <span>{{ isset($year2[0]) ? $year2[0] : '' }}</span>
                                        {{-- <span>{{ isset($year2[1]) ? $year2[1] : '' }}</span>
                                        <span>{{ isset($year2[2]) ? $year2[2] : '' }}</span> --}}
                                        @else
                                        <span>  </span>
                                    @endif
                                    </span>
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                    <span style="color:black;"><b>Material :- </b></span>
                                    @if (!empty($service_details->past_3_year_actual_cost_service))
                                    <span>{{ isset($year3[0]) ? $year3[0] : '' }}</span>
                                    @else
                                    <span>  </span>
                                    @endif
                    </td> 

                </tr>
                <tr style="border:1px solid black;">
                    <td style="border:1px solid black; width:33.3%;">
                        <span><strong>Year :- </strong>{{ $yr_f - 2 }}-{{ $yr_l - 2 }}</span>
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                                <span style="color:black;"><b>Cost :- </b></span>
                                        @if (!empty($service_details->past_3_year_actual_cost))
                                        <span>{{ isset($year2[1]) ? $year2[1] : '' }}</span>
                                        @else
                                        <span>  </span>
                                    @endif
                                    </span>
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                    <span style="color:black;"><b>Material :- </b></span>
                                    @if (!empty($service_details->past_3_year_actual_cost_service))
                                    <span>{{ isset($year3[1]) ? $year3[1] : '' }}</span>
                                    @else
                                    <span>  </span>
                                    @endif
                    </td> 

                </tr>
                <tr style="border:1px solid black;">
                    <td style="border:1px solid black; width:33.3%;">
                        <span><strong>Year :- </strong>{{ $yr_f - 3 }}-{{ $yr_l - 3 }}</span>
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                                <span style="color:black;"><b>Cost :- </b></span>
                                        @if (!empty($service_details->past_3_year_actual_cost))
                                        <span>{{ isset($year2[2]) ? $year2[2] : '' }}</span>
                                        @else
                                        <span>  </span>
                                    @endif
                                    </span>
                    </td> 

                    <td style="border:1px solid black;width:33.3%;">
                    <span style="color:black;"><b>Material :- </b></span>
                                    @if (!empty($service_details->past_3_year_actual_cost_service))
                                    <span>{{ isset($year3[2]) ? $year3[2] : '' }}</span>
                                    @else
                                    <span>  </span>
                                    @endif
                    </td> 

                </tr>

                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="2">
                        <span class="table-box">
                            <span style="color:black;"><b>AMC/WO/RC Proposed Start Date :- </b></span>
                            @if (!empty($service_details->amc_proposal_sdate))
                                <span>{{\Carbon\Carbon::parse($service_details != '' ? $service_details['amc_proposal_sdate'] : '' )->format('d-m-Y') }}</span>
                            @else
                                <span>  </span>
                            @endif
                        </span>
                </td>
                    <td style="width:50%;border:1px solid black;">
                        <span class="table-box">
                        <span style="color:black;"><b>AMC/WO/RC Proposed End Date :- </b></span>

                        @if (!empty($service_details->amc_proposal_edate))
                                <span>{{\Carbon\Carbon::parse($service_details != '' ? $service_details['amc_proposal_edate'] : '' )->format('d-m-Y') }}</span>
                            @else
                                <span>  </span>
                            @endif
                    </span>
                </td>
                   

                    
                </tr>


                @php
                $from = explode(',', $service_details != '' ? $service_details->implementation_period_from : '');
                $to = explode(',', $service_details != '' ? $service_details->implementation_period_to : '');
                $service = explode('.,', $service_details != '' ? $service_details->implementation_plan_year_wise : '');
                $selectedYear = $service_details != '' ? $service_details->implements_years : '';

                @endphp

                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="2">
                        <span class="table-box">
                            <span style="color:black;"><b>Implementation Period From :- </b></span>
                            @if (!empty($service_details->implementation_period_from))
                                <span>{{date('d-m-Y', strtotime(isset($from[0]) ? $from[0] : ''))}}</span>
                            @else
                                <span>  </span>
                            @endif
                        </span>
                </td>
                    <td style="width:50%;border:1px solid black;">
                        <span class="table-box">
                        <span style="color:black;"><b>Implementation Period To :- </b></span>

                        @if (!empty($service_details->implementation_period_to))
                            @if($selectedYear == 1 )
                                <span>{{date('d-m-Y', strtotime(isset($to[0]) ? $to[0] : ''))}}</span>
                            @elseif($selectedYear == 2) 
                                <span>{{date('d-m-Y', strtotime(isset($to[1]) ? $to[1] : '' ))}}</span>
                            @else
                                <span>{{date('d-m-Y', strtotime(isset($to[2]) ? $to[2] : '' ))}}</span>
                            @endif
                        @else
                            <span>  </span>
                        @endif
                    </span>
                </td>
                   

                    
                </tr>  
                
                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;"> 
                        <span class="table-box">
                       
                            @if (!empty($service_details->implementation_plan_year_wise))
                            @if(!empty($service[0]))<span><p><b>Implementation Plan Year Wise 1</b> :- {{ substr(isset($service[0]) ? $service[0] : '' , 0, 500) }}</span>@endif
                            @if(!empty($service[1]))<span><p><b>Implementation Plan Year Wise 2</b> :-{{ substr(isset($service[1]) ? $service[1] : '', 0, 500) }}</span>@endif
                            @if(!empty($service[2]))<span><p><b>Implementation Plan Year Wise 3</b> :-{{ substr(isset($service[2]) ? $service[2] : '' , 0, 500) }}</span>@endif
                            @else
                            <span style="color:black;"><b>Implementation Plan Year Wise :-  </b> </span>
                        @endif
                       
                        </span>
                    </td>
                </tr>


                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;"> 
                        <b>Benefit :- </b>
                            @if (!empty($service_details->benefit))
                            <span> {{ $service_details != '' ? $service_details['benefit'] : '' }}</span>
                            @else
                            <span>  </span>
                            @endif
                    </td>
                </tr>

                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;"> 
                        <b>Mode of Award :- </b>
                            @if (!empty($service_details->mode_of_award_of_service))
                            <span>{{ $service_details != '' ? $service_details['mode_of_award_of_service'] : '' }}</span>
                            @else
                            <span>  </span>
                            @endif
                    </td>
                </tr>

                @if ( $nv_data->budget_type === 'CAPEX')

                    
                    <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                      <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold; text-align:center;">
                          DERC
                      </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="2" style="border:1px solid black;"> 
                        <b>DERC Proposal Number :- </b>
                        @if (!empty($service_details->prop_number))
                            <span>{{ $service_details != '' ? $service_details['prop_number'] : '' }}</span>
                        @else
                            <span>  </span>
                        @endif
                        </td>
                        <td colspan="2" style="border:1px solid black;"> 
                            <b>DERC Reference No :- </b>
                            @if (!empty($service_details->derc_ref_no))
                                <span>{{ $service_details != '' ? $service_details['derc_ref_no'] : '' }}</span>
                            @else
                                <span>  </span>
                            @endif
                            </td>
                    </tr>

                    <tr style="border:1px solid black;">
                        <td colspan="2" style="border:1px solid black;"> 
                        <b>DERC Approval Status :- </b>
                        @if (!empty($service_details->derc_approval))
                            <span>
                                {{ $service_details != '' ? $service_details['derc_approval'] : '' }}</span>
                        @else
                            <span>  </span>
                        @endif
                        </td>
                        <td colspan="2" style="border:1px solid black;"> 
                            <b>DERC Approval Date :- </b>
                            @if (!empty($service_details->derc_app_date))
                                <span>{{\Carbon\Carbon::parse($service_details != '' ? $service_details['derc_app_date'] : '')->format('d-m-Y') }}</span>
                            @else
                                <span>  </span>
                            @endif
                            </td>
                    </tr>

                   
                @endif

                <?php
                    $total = 0;
                    $m_importAmount = 0;
                    $totalvalue = $total;
                    ?>

                    @foreach ($service_import as $key => $m_import)
                        <?php
                        $m_importAmount =  $m_import->amount;
                        ?>

                        @if (!empty($m_importAmount))
                            <?php
                            
                            $total = $total + $m_importAmount;
                            
                            ?>
                        @else
                            <?php
                            $total = $total;
                            ?>
                        @endif
                    @endforeach

                  

                    <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                      <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold; text-align:center;">
                            Service BOQ 
                      </td>
                    </tr>
                    @php
                        $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                        $wordss = $fmt->format(safeRound($total));

                        $Imp_year = explode('-',$nv_data->fiscal_year);
                        $Imp = $nv_data->fiscal_year; 

                        if (safeRound($total) >= 10000000) {
                            $crore = $total / 10000000;
                            $croress = floor($crore * 100) / 100;
                            $crores = floor($crore);
                            $remaining = ($total - ($crores * 10000000)) / 100000;
                            $lakhs = floor($remaining);
                            
                            if ($lakhs > 0) {
                                $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                            } else {
                                $words = convertNumberToWords($crores) . ' Crores';
                            }
                            $total = safeNumberFormat($croress , 2) . '  Cr';
                        }else{
                            $words = convertNumberToWords($total). '  Rupees';
                          $total = safeNumberFormat($total ,2) . '  Rs.';
                        }

                        if (safeRound($service_details->total_matyear1) >= 10000000) {
                            $totalAmt1_crore = $service_details->total_matyear1 / 10000000;
                            $totalAmt1_croress = floor($totalAmt1_crore * 100) / 100;
                            $totalAmt1_crores = floor($totalAmt1_crore);
                            $remaining = ($service_details->total_matyear1 - ($totalAmt1_crores * 10000000)) / 100000;
                            $lakhs = floor($remaining);
                            if ($lakhs > 0) {
                                $totalAmt1_words = convertNumberToWords($totalAmt1_crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                            } else {
                                $totalAmt1_words = convertNumberToWords($totalAmt1_crores) . ' Crores';
                            }
                            $totalAmt1_budget = safeNumberFormat($totalAmt1_croress , 2) . '  Cr';
                        }else{
                            $totalAmt1_words = convertNumberToWords($service_details->total_matyear1). '  Rupees';
                          $totalAmt1_budget = safeNumberFormat($service_details->total_matyear1 ,2) . '  Rs.';
                        }
                     
                        if (safeRound($service_details->total_matyear2) >= 10000000) {
                            $totalAmt2_crore = $service_details->total_matyear2 / 10000000;
                            $totalAmt2_croress = floor($totalAmt2_crore * 100) / 100;
                            $totalAmt2_crores = floor($totalAmt2_crore);
                            $remaining = ($service_details->total_matyear2 - ($totalAmt2_crores * 10000000)) / 100000;
                            $lakhs = floor($remaining);
                            if ($lakhs > 0) {
                                $totalAmt2_words = convertNumberToWords($totalAmt2_crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                            } else {
                                $totalAmt2_words = convertNumberToWords($totalAmt2_crores) . ' Crores';
                            }
                            $totalAmt2_budget = safeNumberFormat($totalAmt2_croress , 2) . '  Cr';
                        }else{
                            $totalAmt2_words = convertNumberToWords($service_details->total_matyear2). '  Rupees';
                          $totalAmt2_budget = safeNumberFormat($service_details->total_matyear2 ,2) . '  Rs.';
                        }
                      
                        if (safeRound($service_details->total_matyear3) >= 10000000) {
                            $totalAmt3_crore = $service_details->total_matyear3 / 10000000;
                            $totalAmt3_croress = floor($totalAmt3_crore * 100) / 100;
                            $totalAmt3_crores = floor($totalAmt3_crore);
                            $remaining = ($service_details->total_matyear3 - ($totalAmt3_crores * 10000000)) / 100000;
                            $lakhs = floor($remaining);
                            if ($lakhs > 0) {
                                $totalAmt3_words = convertNumberToWords($totalAmt3_crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                            } else {
                                $totalAmt3_words = convertNumberToWords($totalAmt3_crores) . ' Crores';
                            }
                            $totalAmt3_budget = safeNumberFormat($totalAmt3_croress , 2) . '  Cr';
                        }else{
                            $totalAmt3_words = convertNumberToWords($service_details->total_matyear3). '  Rupees';
                          $totalAmt3_budget = safeNumberFormat($service_details->total_matyear3 ,2) . '  Rs.';
                        }
                       
                    @endphp
                    <tr style="border:1px solid black;">
                        <td colspan="3" style="border:1px solid black;"> 
                           <b>Service BOQ Amount :- </b>
                           @if($service_details->implements_years == 3)
                        <br><b>FY {{$Imp}} :</b> {{$totalAmt1_budget ?? null}}<span>({{ucwords($totalAmt1_words)}})</span>
                        <br><b>FY {{$Imp_year[0]+1}}-{{$Imp_year[1]+1}} :</b> {{$totalAmt2_budget ?? null}}<span>({{ucwords($totalAmt2_words)}})</span>
                        <br><b>FY {{$Imp_year[0]+2}}-{{$Imp_year[1]+2}} :</b> {{$totalAmt3_budget ?? null}}<span>({{ucwords($totalAmt3_words)}})</span>
                        @elseif($service_details->implements_years == 2)
                        <br><b>FY {{$Imp}} :</b> {{$totalAmt1_budget ?? null}}<span>({{ucwords($totalAmt1_words)}})</span>
                        <br><b>FY {{$Imp_year[0]+1}}-{{$Imp_year[1]+1}} :</b> {{$totalAmt2_budget ?? null}}<span>({{ucwords($totalAmt2_words)}})</span>
                        @elseif($service_details->implements_years == 1)
                        <br><b>FY {{$Imp}} :</b> {{$totalAmt1_budget ?? null}}<span>({{ucwords($totalAmt1_words)}})</span>
                        @else
                            <span>{{$total ?? null}}</span>
                             <span>({{ucwords($words)}})</span>
                             @endif
                        </td>
                    </tr>

                    @php
                        $service_amount = explode(',', $service_details != '' ? $service_details->service_amount : '0');
                        $service_description = explode(',', $service_details != '' ? $service_details->service_description : '');
                    @endphp


                    <tr style="border:1px solid black;">
                        <td colspan="2" style="border:1px solid black;"> 
                            <b>Amount :- </b>
                    
                             @if (!empty($service_details->service_amount))
                             @foreach ($service_amount as $key => $service_amt)
                             @php
                                if (safeRound($service_amt) >= 10000000) {
                                    $crore = $service_amt / 10000000;
                                    $crores = floor($crore * 100) / 100;
                                    $service_amt = safeNumberFormat($crores , 2) . '  Cr';
                                }else{
                                $service_amt = safeNumberFormat($service_amt ,2) . '  Rs.';
                                }
                            @endphp
                              <span style="background-color:lightgray;margin-right:5px;padding:2px;">{{$service_amt ?? ''}}</span>
                            @endforeach
                             @else
                                 <span>  </span>
                               @endif
                        </td>

                        <td colspan="2" style="border:1px solid black;"> 
                        <b>Description :- </b>
                        @if (!empty($service_details->service_description))
                        @foreach ($service_description as $key => $service_des)
                            <span style="background-color:lightgray;margin-right:5px;padding:2px;">{{ $service_details != '' ? $service_des : '' }}</span>
                            @endforeach
                        @else
                            <span>  </span>
                        @endif
                        </td>
                    </tr>  

                <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                    <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold; text-align:center;">
                        Rate Reference
                    </td>
                </tr>

                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="2">
                        <b>C&M Rate Reference :- </b>
                            @if (!empty($service_doc->cm_rate_ref))
                            <a class="ml-2" target="_blank" href="{{ url(asset('services-doc/' . $service_doc['cm_rate_ref'])) }}">{{ $service_doc->cm_rate_ref }}</a>
                            @else
                                <span>  </span>
                        @endif
                    </td>

                    <td style="width:50%;border:1px solid black;">
                        <b>Vendor Quotation :- </b>
                            @if (!empty($service_doc->vendor_quat))
                            <a class="ml-2" target="_blank" href="{{ url(asset('services-doc/' . $service_doc['vendor_quat'])) }}">{{ $service_doc->vendor_quat }}</a>
                            @else
                                <span>  </span>
                            @endif
                    </td>

                </tr>  

                   
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="2">
                    <b>Last Purchase Price  :- </b>
                    @if (!empty($service_doc->last_purchase_price))
                    <a class="ml-2" target="_blank" href="{{ url(asset('services-doc/' . $service_doc['last_purchase_price'])) }}">{{ $service_doc->last_purchase_price }}</a>
                    @else
                        <span>  </span>
                    @endif
                    </td>

                    <td style="width:50%;border:1px solid black;">
                    <b>User Estimation :- </b>
                    @if (!empty($service_doc->user_estimation))
                    <a class="ml-2" target="_blank" href="{{ url(asset('services-doc/' . $service_doc['user_estimation'])) }}">{{ $service_doc->user_estimation }}</a>
                    @else
                        <span>  </span>
                    @endif
                    </td>

                   
                </tr>  
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Previous WO/RC (if any) :- </b>
                        @if (!empty($service_doc->previous_wo_rc))
                        <a class="ml-2" target="_blank" href="{{ url(asset('services-doc/' . $service_doc['previous_wo_rc'])) }}">{{ $service_doc->previous_wo_rc }}</a>
                        @else
                            <span>  </span>
                        @endif
                        </td>
                </tr>
                
                <tr style="border:1px solid transparent;background-color: rgb(3, 142, 220);">
                      <td colspan="3" style="border:1px solid transparent; color:white; font-weight:bold; text-align:center;">
                             Attachments
                      </td>
                </tr>

                <tr style="border:1px solid black;">
                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                        <b>Copy Of Previous Work Order/Purchase Order :- </b>
                        @if (!empty($service_doc->copy_of_previous_work))
                        <a class="ml-2" download="{{ $service_doc->copy_of_previous_work }}" href="{{ url(asset('services-doc/' . $service_doc->copy_of_previous_work)) }}"><i class="fa fa-download" title="Download"></i>{{ $service_doc->copy_of_previous_work }}</a>
                            @else
                                <span>  </span>
                            @endif
                        </td>
                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                            <b>Copy Of DERC:- </b>
                            @if (!empty($service_doc->copy_of_derc_other))
                            <a class="ml-2" target="_blank" href="{{ url(asset('services-doc/' . $service_doc['copy_of_derc_other'])) }}">{{ $service_doc->copy_of_derc_other }}</a>
                                @else
                                    <span>  </span>
                                @endif
                            </td>
                </tr>

                <tr style="border:1px solid black;">
                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                        <b>Consumption Details - Last 3 Year :- </b>
                        @if (!empty($service_doc->consuption_details))
                        <a class="ml-2" target="_blank" href="{{ url(asset('services-doc/' . $service_doc['consuption_details'])) }}">{{ $service_doc->consuption_details }}</a>
                            @else
                                <span>  </span>
                            @endif
                        </td>

                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                            <b>Budget statement :- </b>
                            @if (!empty($service_doc->buget_stmt_for_both))
                            <a class="ml-2" target="_blank" href="{{ url(asset('services-doc/' . $service_doc['buget_stmt_for_both'])) }}">{{ $service_doc->buget_stmt_for_both }}</a>
                                @else
                                    <span>  </span>
                                @endif
                            </td>
                </tr>

                

                <tr style="border:1px solid black;">
                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                        <b>Photographs of product :- </b>
                        @if (!empty($service_doc->photographs_of_product))
                        <a class="ml-2" target="_blank" href="{{ url(asset('services-doc/' . $service_doc->photographs_of_product)) }}">{{ $service_doc->photographs_of_product }}</a>
                            @else
                                <span>  </span>
                            @endif
                        </td>

                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                            <b>Material procurement :- </b>
                            @if (!empty($service_doc->material_procurement))
                            <a class="ml-2" target="_blank" href="{{ url(asset('services-doc/' . $service_doc['material_procurement'])) }}">{{ $service_doc->material_procurement }}</a>
                                @else
                                    <span>  </span>
                                @endif
                            </td>
                </tr>

                

                <tr style="border:1px solid black;">
                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                        <b>Technical Specifications :- </b>
                        @if (!empty($service_doc->vendor_quatation))
                        <a class="ml-2" target="_blank" href="{{ url(asset('services-doc/' . $service_doc->vendor_quatation)) }}">{{ $service_doc->vendor_quatation }}</a>
                            @else
                                <span>  </span>
                            @endif
                        </td>

                        <td style="width:50%;border:1px solid black;" colspan="2"> 
                            <b>Vendor Quatation :- </b>
                            @if (!empty($service_doc->vend_quatation))
                            <a class="ml-2" target="_blank" href="{{ url(asset('services-doc/' . $service_doc['vend_quatation'])) }}">{{ $service_doc->vend_quatation }}</a>
                                @else
                                    <span>  </span>
                                @endif
                            </td>
                </tr>
                <tr style="border:1px solid black;">
                <td style="width:50%;border:1px solid black;" colspan="2"> 
                            <b>Others :- </b><br>
                            @if (!empty($service_doc->others))
                            @php
                                $images = explode(',', $service_doc->others);
                            @endphp
                                @foreach($images as $image)
                                    <a href="{{ url(asset('services-doc/' . $image)) }}" class="ml-2" target="_blank">
                                        {{ $image }}
                                    </a><br>
                                @endforeach
                            @else
                                <span>N/A</span>
                            @endif
                            </td>
                            <td style="width:50%;border:1px solid black;" colspan="2"> 
                            <b>Cost Calculation for services :- </b>
                            @if (!empty($service_doc->cost_calculation_for_service))
                            <a class="ml-2" target="_blank" href="{{ url(asset('services-doc/' . $service_doc['cost_calculation_for_service'])) }}">{{ $service_doc->cost_calculation_for_service }}</a>
                                @else
                                    <span>  </span>
                                @endif
                            </td>
                </tr>

                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Cost Reduction Plan :- </b>
                            @if (!empty($service_details->cause_analysis))
                            <span>{{ $service_details != '' ? $service_details['cause_analysis'] : '' }}</span>
                        @endif
                        <br>
                        
                    </td>

                   

                </tr> 
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Past Practice Followed :- </b>
                            @if (!empty($service_doc->past_practice))
                            <a class="ml-2" href="{{ url(asset('services-doc/' . $service_doc->past_practice)) }}">{{ $service_doc->past_practice }}</a>
                            @else
                                <span>  </span>
                            @endif
                            <br>
                            <span>{{ $service_details != '' ? $service_details->past_practice_text : '' }}</span>
                    </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Budget Calculation :- </b>
                        @if (!empty($service_details->approved_budget) && !empty($service_details->add_budget))
                    @php
                        $approved_budget = $service_details->approved_budget;
                        $formatted_budget_appr = 0;
                        if (safeRound($approved_budget) >= 10000000) {
                            $crore = $approved_budget / 10000000;
                            $crores = floor($crore * 100) / 100;
                            $formatted_budget_appr = safeNumberFormat($crores, 2) . '  Cr';
                        }else{
                            $formatted_budget_appr = safeNumberFormat($approved_budget, 2) . '  Rs.';
                        }

                        $add_budget = $service_details->add_budget;
                        $formatted_budget_addl = 0;
                        if (safeRound($add_budget) >= 10000000) {
                            $crore = $add_budget / 10000000;
                            $crores = floor($crore * 100) / 100;
                            $formatted_budget_addl = safeNumberFormat($crores, 2) . '  Cr';
                        }else{
                            $formatted_budget_addl = safeNumberFormat($add_budget, 2) . '  Rs.';
                        }
                      
                    @endphp
                        

                    <span style="color:black;">
                    Appr : {{$formatted_budget_appr}} , Addl : {{$formatted_budget_addl}}</span>
                    @elseif(!empty($service_details->approved_budget) && empty($service_details->add_budget))
                    @php
                    $approved_budget = $service_details->approved_budget;
                        $formatted_budget_appr = 0;
                        if (safeRound($approved_budget) >= 10000000) {
                            $crore = $approved_budget / 10000000;
                            $crores = floor($crore * 100) / 100;
                            $formatted_budget_appr = safeNumberFormat($crores, 2) . '  Cr';
                        }else{
                            $formatted_budget_appr = safeNumberFormat($approved_budget, 2) . '  Rs.';
                        }
                    @endphp
                    Appr : {{$formatted_budget_appr}}
                    @elseif(empty($service_details->approved_budget) && !empty($service_details->add_budget))
                    @php
                    $add_budget = $service_details->add_budget;
                        $formatted_budget_addl = 0;
                        if (safeRound($add_budget) >= 10000000) {
                            $crore = $add_budget / 10000000;
                            $crores = floor($crore * 100) / 100;
                            $formatted_budget_addl = safeNumberFormat($crores, 2) . '  Cr';
                        }else{
                            $formatted_budget_addl = safeNumberFormat($add_budget, 2) . '  Rs.';
                        }
                    @endphp
                    Addl : {{$formatted_budget_addl}}
                    @else
                        <span></span>
                    @endif
                    </td>
                </tr>
   
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Special Remarks :- </b>
                        @if (!empty($service_doc->special_attch))
                            <a class="ml-2" download target="_blank" href="{{ url(asset('services-doc/' . $service_doc->special_attch)) }}">{{ $service_doc->special_attch }}</a>
                            @else
                                <span>  </span>
                            @endif
                            <br>
                            <span>{{ $service_details != '' ? $service_details->special_remarks : '' }}</span>
                          
                      
                    </td>

                   

                </tr>
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Budget Available :- </b>
                            @if (!empty($service_details->budget_available))
                            @php
                            $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(safeRound($service_details->budget_available));

                                $budget_avl = $service_details->budget_available;
                                if (safeRound($budget_avl) >= 10000000) {
                                    $crore = $budget_avl / 10000000;
                                    $croress = floor($crore * 100) / 100;
                                    $crores = floor($crore);
                                    $remaining = ($budget_avl - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                    $budget_avl = safeNumberFormat($croress , 2) . '  Cr';
                                }else{
                                $words = convertNumberToWords($budget_avl). '  Rupees';
                                $budget_avl = safeNumberFormat($budget_avl ,2) . '  Rs.';
                                }
                            @endphp
                            <span>{{ $budget_avl ?? '' }}</span>
                            <span>({{ucwords($words)}})</span>
                            @else
                                <span>  </span>
                            @endif
                            <br>
                    </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Estimated Amount Of Services :- </b>
                            @if (!empty($service_details->estimate_amount_of_service))
                            @php
                            $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(safeRound($service_details->estimate_amount_of_service));

                                $amounts = $service_details->estimate_amount_of_service;
                                if (safeRound($amounts) >= 10000000) {
                                    $crore = $amounts / 10000000;
                                    $croress = floor($crore * 100) / 100;
                                    $crores = floor($crore);
                                    $remaining = ($amounts - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                    $amount = safeNumberFormat($croress , 2) . '  Cr';
                                }else{
                                    $words = convertNumberToWords($amounts). '  Rupees';
                                $amount = safeNumberFormat($amounts ,2) . '  Rs.';
                                }
                            @endphp
                            <span>{{ $amount ?? '' }}</span>
                            <span>({{ucwords($words)}})</span>
                            @else
                                <span>  </span>
                            @endif
                            <br>
                    </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Estimated Amount Of Services Civil :- </b>
                            @if (!empty($service_details->estimate_amount_of_service_civil))
                            @php
                            $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(safeRound($service_details->estimate_amount_of_service_civil));

                                $amounts = $service_details->estimate_amount_of_service_civil;
                                if (safeRound($amounts) >= 10000000) {
                                    $crore = $amounts / 10000000;
                                    $croress = floor($crore * 100) / 100;
                                    $crores = floor($crore);
                                    $remaining = ($amounts - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                    $amount = safeNumberFormat($croress , 2) . '  Cr';
                                }else{
                                    $words = convertNumberToWords($amounts). '  Rupees';
                                $amount = safeNumberFormat($amounts ,2) . '  Rs.';
                                }
                            @endphp
                            <span>{{ $amount ?? '' }}</span> 
                            <span>({{ucwords($words)}})</span>                           
                            @else
                                <span>  </span>
                            @endif
                            <br>
                    </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Estimated Amount Of RR Charges :- </b>
                            @if (!empty($service_details->estimate_amount_of_rr_chnage))
                            @php
                            $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(safeRound($service_details->estimate_amount_of_rr_chnage));

                                $amounts = $service_details->estimate_amount_of_rr_chnage;
                                if (safeRound($amounts) >= 10000000) {
                                    $crore = $amounts / 10000000;
                                    $croress = floor($crore * 100) / 100;
                                    $crores = floor($crore);
                                    $remaining = ($amounts - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                    $amount = safeNumberFormat($croress , 2) . '  Cr';
                                }else{
                                    $words = convertNumberToWords($amounts). '  Rupees';
                                $amount = safeNumberFormat($amounts ,2) . '  Rs.';
                                }
                            @endphp
                            <span>{{ $amount ?? '' }}</span>     
                            <span>({{ucwords($words)}})</span>                         
                            @else
                                <span>  </span>
                            @endif
                            <br>
                    </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Estimated Amount - Other :- </b>
                            @if (!empty($service_details->estimate_amount_other))
                            @php
                            $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(safeRound($service_details->estimate_amount_other));

                                $amounts = $service_details->estimate_amount_other;
                                if (safeRound($amounts) >= 10000000) {
                                    $crore = $amounts / 10000000;
                                    $croress = floor($crore * 100) / 100;
                                    $crores = floor($crore);
                                    $remaining = ($amounts - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                    $amount = safeNumberFormat($croress , 2) . '  Cr';
                                }else{
                                    $words = convertNumberToWords($amounts). '  Rupees';
                                $amount = safeNumberFormat($amounts ,2) . '  Rs.';
                                }
                            @endphp
                            <span>{{ $amount ?? '' }}</span> 
                            <span>({{ucwords($words)}})</span>                              
                            @else
                                <span>  </span>
                            @endif
                            <br>
                    </td>
                </tr>
                <tr style="border:1px solid black;">
                    <td style="width:50%;border:1px solid black;" colspan="4">
                        <b>Total :- </b>
                            @if (!empty($service_details->total_buget))
                            @php
                            $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                            $wordss = $fmt->format(safeRound($service_details->total_buget));

                                $amounts = $service_details->total_buget;
                                if (safeRound($amounts) >= 10000000) {
                                    $crore = $amounts / 10000000;
                                    $croress = floor($crore * 100) / 100;
                                    $crores = floor($crore);
                                    $remaining = ($amounts - ($crores * 10000000)) / 100000;
                                    $lakhs = floor($remaining);
                                    
                                    if ($lakhs > 0) {
                                        $words = convertNumberToWords($crores) . ' Crores ' . convertNumberToWords($lakhs) . ' Lakhs';
                                    } else {
                                        $words = convertNumberToWords($crores) . ' Crores';
                                    }
                                    $amount = safeNumberFormat($croress , 2) . '  Cr';
                                }else{
                                    $words = convertNumberToWords($amounts). '  Rupees';
                                $amount = safeNumberFormat($amounts ,2) . '  Rs.';
                                }
                            @endphp
                            <span>{{ $amount ?? '' }}</span> 
                            <span>({{ucwords($words)}})</span>                             
                            @else
                                <span>  </span>
                            @endif
                            <br>
                    </td>
                </tr>
                
            </table>

            
            {{-- <div style="page-break-after:always;"></div> --}}
            <table style="width:100% ;  border-collapse: collapse;" class="signature-t" >
        
                <tr style="border:1px solid lightgray !important;">
                    <th style="width:25%;border:1px solid lightgray !important;">Name</th>
                    <th style="width:25%;border:1px solid lightgray !important;">Remarks</th>
                    <th style="width:25%;border:1px solid lightgray !important;">Signature</th>
                    <th style="width:25%;border:1px solid lightgray !important;">Date & Time</th>
                </tr>
                @if($Nvsericestatus->rv1_status == 1||$Nvsericestatus->rv1_status == 2)   
                <tr style="width:25%;border:1px solid lightgray !important;">
                  
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <b>Reviewer1</b> 
                        </td> 
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <span>{{ $Nvsericestatus->rv1_remark }}</span>
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @php
                                $user_rv1= App\Models\User::where("id",$Nvsericestatus->rv1_id)->first();
                            @endphp
                            @if($user_rv1->signature_id==1) 
                            <span style="font-family: 'Cedarville Cursive', cursive;">{{  $user_rv1->name ?? '' }}</span>
                            @endif
                            @if($user_rv1->signature_id==2)  
                            <span style="font-family: 'Courier New', Courier, monospace;">{{$user_rv1->name ?? '' }}</span>
                            @endif
                            @if($user_rv1->signature_id==3)
                            <span style=" font-family: 'Satisfy', cursive;">{{$user_rv1->name ?? '' }}</span>
                            @endif
                            @if($user_rv1->signature_id==4)
                            <span style=" font-family: 'Shadows Into Light Two', cursive;">{{$user_rv1->name ?? '' }}</span>
                            @endif
                            @if($user_rv1->signature_id==5) 
                            <img  src="{{public_path('/images/'.$user_rv1->image)}}" style="height:auto;width:80px; margin-top:30px;" />
                            @endif
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">{{ date('d-M-y h:i A', strtotime($Nvsericestatus->rv1_timestamp)) }}</td>

                      
                </tr> 
                @endif           
                @if($Nvsericestatus->rv2_status == 1||$Nvsericestatus->rv2_status == 2)            
                <tr style="width:25%;border:1px solid lightgray !important;">
                    
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <b>Reviewer2</b> 
                        </td> 
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <span>{{ $Nvsericestatus->rv2_remark }}</span>
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @php
                                $user_rv2= App\Models\User::where("id",$Nvsericestatus->rv2_id)->first();
                            @endphp
                            @if($user_rv2->signature_id==1) 
                            <span style="font-family: 'Cedarville Cursive', cursive;">{{ $user_rv2->name ?? '' }}</span>
                            @endif
                            @if($user_rv2->signature_id==2)  
                            <span style="font-family: 'Courier New', Courier, monospace;">{{ $user_rv2->name ?? '' }}</span>
                            @endif
                            @if($user_rv2->signature_id==3)
                            <span style=" font-family: 'Satisfy', cursive;">{{ $user_rv2->name ?? '' }}</span>
                            @endif
                            @if($user_rv2->signature_id==4)
                            <span style=" font-family: 'Shadows Into Light Two', cursive;">{{ $user_rv2->name ?? '' }}</span>
                            @endif
                            @if($user_rv2->signature_id==5) 
                            <img  src="{{public_path('/images/'.$user_rv2->image)}}" style="height:auto;width:80px; margin-top:30px;" />
                            @endif
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">{{ date('d-M-y h:i A', strtotime($Nvsericestatus->rv2_timestamp)) }}</td>

                    
                </tr> 
                @endif                   
                @if($Nvsericestatus->rv3_status == 1||$Nvsericestatus->rv3_status == 2)        
                <tr style="width:25%;border:1px solid lightgray !important;">
                    
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <b>Reviewer3</b> 
                        </td> 
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <span >{{ $Nvsericestatus->rv3_remark }}</span>
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @php
                            $user_rv3= App\Models\User::where("id",$Nvsericestatus->rv3_id)->first();
                            @endphp
                            @if($user_rv3->signature_id==1) 
                            <span style="font-family: 'Cedarville Cursive', cursive;">{{$user_rv3->name ?? '' }}</span>
                            @endif
                            @if($user_rv3->signature_id==2)  
                            <span style="font-family: 'Courier New', Courier, monospace;">{{$user_rv3->name ?? '' }}</span>
                            @endif
                            @if($user_rv3->signature_id==3)
                            <span style=" font-family: 'Satisfy', cursive;">{{$user_rv3->name ?? '' }}</span>
                            @endif
                            @if($user_rv3->signature_id==4)
                            <span style=" font-family: 'Shadows Into Light Two', cursive;">{{$user_rv3->name ?? '' }}</span>
                            @endif
                            @if($user_rv3->signature_id==5) 
                            <img  src="{{public_path('/images/'.$user_rv3->image)}}" style="height:auto;width:80px; margin-top:30px;" />
                            @endif
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">{{ date('d-M-y h:i A', strtotime($Nvsericestatus->rv3_timestamp)) }}</td>
                    
                </tr> 
                @endif           
                @if($Nvsericestatus->rv4_status == 1||$Nvsericestatus->rv4_status == 2)         
                <tr style="width:25%;border:1px solid lightgray !important;">
                    
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <b>Reviewer4</b> 
                        </td> 
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <span >{{ $Nvsericestatus->rv4_remark }}</span>
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @php
                            $user_rv4= App\Models\User::where("id",$Nvsericestatus->rv4_id)->first();
                            @endphp
                            @if($user_rv4->signature_id==1) 
                            <span style="font-family: 'Cedarville Cursive', cursive;">{{ $user_rv4->name ?? '' }}</span>
                            @endif
                            @if($user_rv4->signature_id==2)  
                            <span style="font-family: 'Courier New', Courier, monospace;">{{ $user_rv4->name ?? '' }}</span>
                            @endif
                            @if($user_rv4->signature_id==3)
                            <span style=" font-family: 'Satisfy', cursive;">{{ $user_rv4->name ?? '' }}</span>
                            @endif
                            @if($user_rv4->signature_id==4)
                            <span style=" font-family: 'Shadows Into Light Two', cursive;">{{ $user_rv4->name ?? '' }}</span>
                            @endif
                            @if($user_rv4->signature_id==5) 
                            <img  src="{{public_path('/images/'.$user_rv4->image)}}" style="height:auto;width:80px; margin-top:30px;" />
                            @endif
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">{{ date('d-M-y h:i A', strtotime($Nvsericestatus->rv4_timestamp)) }}</td>
                      
                </tr>
                @endif           
                @if($Nvsericestatus->hod_status == 1||$Nvsericestatus->hod_status == 2)      
                <tr style="width:25%;border:1px solid lightgray !important;">
                    
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <b>HOD</b> 
                        </td> 
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <span>{{ $Nvsericestatus->hod_remark }}</span>
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @php
                            $user_hod= App\Models\User::where("id",$Nvsericestatus->hod_id)->first();
                            @endphp
                            @if($user_hod->signature_id==1) 
                            <span style="font-family: 'Cedarville Cursive', cursive;">{{  $user_hod->name ?? '' }}</span>
                            @endif
                            @if($user_hod->signature_id==2)  
                            <span style="font-family: 'Courier New', Courier, monospace;">{{  $user_hod->name ?? '' }}</span>
                            @endif
                            @if($user_hod->signature_id==3)
                            <span style=" font-family: 'Satisfy', cursive;">{{  $user_hod->name ?? '' }}</span>
                            @endif
                            @if($user_hod->signature_id==4)
                            <span style=" font-family: 'Shadows Into Light Two', cursive;">{{  $user_hod->name ?? '' }}</span>
                            @endif
                            @if($user_hod->signature_id==5) 
                            <img  src="{{public_path('/images/'.$user_hod->image)}}" style="height:auto;width:80px; margin-top:30px;" />
                            @endif
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">{{ date('d-M-y h:i A', strtotime($Nvsericestatus->hod_timestamp)) }}</td>
                      
                </tr>
                @endif 

                @if( $Nvsericestatus->groupcio_status == 1||$Nvsericestatus->groupcio_status == 2 )
                <tr style="width:25%;border:1px solid lightgray !important;">
                   
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <b> Group Head </b> 
                        </td> 
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            <span>{{ $Nvsericestatus->groupcio_remark }}</span>
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @php
                           $user_groupcio = App\Models\User::where("id",$Nvsericestatus->groupcio_id)->first();
                            @endphp
                            @if( $user_groupcio->signature_id==1) 
                            <span style="font-family: 'Cedarville Cursive', cursive;">{{    $user_groupcio->name ?? '' }}</span>
                            @endif
                            @if( $user_groupcio->signature_id==2)  
                            <span style="font-family: 'Courier New', Courier, monospace;">{{    $user_groupcio->name ?? '' }}</span>
                            @endif
                            @if( $user_groupcio->signature_id==3)
                            <span style=" font-family: 'Satisfy', cursive;">{{    $user_groupcio->name ?? '' }}</span>
                            @endif
                            @if( $user_groupcio->signature_id==4)
                            <span style=" font-family: 'Shadows Into Light Two', cursive;">{{    $user_groupcio->name ?? '' }}</span>
                            @endif
                            @if( $user_groupcio->signature_id==5) 
                            <img  src="{{public_path('/images/'.   $user_groupcio->image)}}" style="height:auto;width:80px; margin-top:30px;" />
                            @endif
                        </td>
                        <td style="text-align: center;width:25%;border:1px solid lightgray !important;">{{ date('d-M-y h:i A', strtotime($Nvsericestatus->groupcio_timestamp)) }}</td>
                      
                </tr>
                @endif 

                @php 
                    $workflow_data = DB::table('capex_workflows_status')
                                    ->where('service_id', $service_details->id)
                                    ->where('nv_budget_type', $nv_data->budget_type)
                                    ->whereIn('nv_stage_status',[1,2])
                                    ->where('transfer_to_nominee1', '0')
                                    ->where('nv_stage_remark', '!=', null)
                                    ->get();

                    $reviewerLabels = [
                    'work_rew1' => 'Reviewer1',
                    'work_rew2' => 'Reviewer2',
                    'work_rew3' => 'Reviewer3',
                    'work_rew4' => 'Reviewer4',
                    'approver' => 'Approver'
                    ];
            
                @endphp
            
                @if($workflow_data->isNotEmpty())
                    @foreach($workflow_data as $workflowData)
                        @php 
                                $role = $reviewerLabels[$workflowData->reviewer_name] ?? '';
                                $roleWithDepartment = $role . ' (' . getDepartmentName($workflowData->department_id) . ')';
                                $stage_user_name = App\Models\User::where("id",$workflowData->workflow_user_id)->first();
                        @endphp
                        <tr style="width:25%;border:1px solid lightgray !important;">
                            <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                                <b> {{ $roleWithDepartment }}</b> 
                            </td> 
                            <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                                <span>{{$workflowData->nv_stage_remark ?? ''}}</span>
                            </td>

                            <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            @if(in_array($workflowData->nv_stage_signature, [1,2,3,4]))
                                <span style="font-family: 
                                    @if($workflowData->nv_stage_signature == 1) 'Cedarville Cursive', cursive;
                                    @elseif($workflowData->nv_stage_signature == 2) 'Courier New', Courier, monospace;
                                    @elseif($workflowData->nv_stage_signature == 3) 'Satisfy', cursive;
                                    @elseif($workflowData->nv_stage_signature == 4) 'Shadows Into Light Two', cursive;
                                    @endif">
                                    {{ $stage_user_name->name ?? '' }}</span>
                                @elseif($workflowData->nv_stage_signature == 5)
                                <img src="{{ public_path('/images/' . $stage_user_name->image) }}"
                                    style="height:auto;width:80px; margin-top:10px;" />
                                @endif 
                            </td>

                            <td style="text-align: center;width:25%;border:1px solid lightgray !important;">
                            {{ date('d-M-y h:i A', strtotime($workflowData->nv_stage_timestamp)) }}
                            </td>

                        </tr>
                    @endforeach
                @endif
            </table>  
                      
            
               
           
        </div>

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

    </main>


    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
    <script>
        
    </script>

</body>

</html>