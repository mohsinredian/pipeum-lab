<!DOCTYPE html>
<html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting"> <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700" rel="stylesheet">
    <style>
        table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

 

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

 

tr:nth-child(odd) {
  background-color: #dddddd;
}


    
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background: #f1f1f1;
        }

        /* What it does: Stops email clients resizing small text. */
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            font-size:14px  !important;
        }

        /* What it does: Centers email on Android 4.4 */
        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        /* What it does: Stops Outlook from adding extra spacing to tables. */
        table,

        /* What it does: Fixes webkit padding issue. */
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }

        /* What it does: Uses a better rendering method when resizing images in IE. */
        img {
            -ms-interpolation-mode: bicubic;
        }

        /* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
        a {
            text-decoration: none;
        }

        /* What it does: A work-around for email clients meddling in triggered links. */
        *[x-apple-data-detectors],
        /* iOS */
        .unstyle-auto-detected-links *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
        .a6S {
            display: none !important;
            opacity: 0.01 !important;
        }

        /* What it does: Prevents Gmail from changing the text color in conversation threads. */
        .im {
            color: inherit !important;
        }

        /* If the above doesn't work, add a .g-img class to any image in question. */
        img.g-img+div {
            display: none !important;
        }

        @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
            u~div .email-container {
                min-width: 320px !important;
            }
        }

        /* iPhone 6, 6S, 7, 8, and X */
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
            u~div .email-container {
                min-width: 375px !important;
            }
        }

        /* iPhone 6+, 7+, and 8+ */
        @media only screen and (min-device-width: 414px) {
            u~div .email-container {
                min-width: 414px !important;
            }
        }
 
        .primary {
            background: #248ad3;
        }

        .email-section {
            padding: 2.5em;
        }

        /*BUTTON*/
        .btn {
            padding: 10px 15px;
            display: inline-block;
        }

        .btn.btn-primary {
            border-radius: 5px;
            background: #248ad3;
            color: #ffffff;
        }


        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Poppins', sans-serif;
            color: #000000;
            margin-top: 0;
            font-weight: 400;
        }

        body {
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            font-size: 15px;
            line-height: 1.8;
            color: rgba(0, 0, 0, .4);
        }

        a {
            color: #248ad3;
        }

        p {
            color: #000000;
        }

        .light-blue-bg {
    background-color: lightblue;
}
    </style>


</head>
<body>
    
<!-- <body width="60%" style="background-color: #ffffff;margin:0 auto;">
    <p>Dear {{ $username ?? '' }},</p>
    <p>{{ $p1 ?? '' }}</p>
	 <p>{{ $p2 ?? '' }}</p>
    {{-- <p><strong>Request: </strong> [insert request details here]</p> --}}
  
	<p>{{ $remark ?? '' }}</p>
	<p>NV Initiated BY  : {{ $initiated_by->name ?? '' }}</p>
	<p>NV Initiated Date  :  {{ date('d-M-y', strtotime($initiated_date->created_at ?? '' )) }}
                                        <br> {{ date('H:i', strtotime($initiated_date->created_at ?? '' )) }}</p>
	<p>	NV {{ $status ?? '' }} By  :  {{ $approved_by->name ?? '' }}</p>
	<p>NV {{ $status ?? '' }} Date  :  {{ date('d-M-y', strtotime($approved_date->hod_timestamp ?? '' )) }}
                                        <br> {{ date('H:i', strtotime($approved_date->hod_timestamp ?? '' )) }}</p>


    <p>Thank you,</p>
    <p><b>Team BSES</b></p>
    {{-- <p><em>This is an automated message, please do not reply.</em></p> --}}
</body> -->



     <div class="row">
        <p>Dear Sir/Madam,</p>
        <p>The following NV is submitted for your review/approval.</p>
     </div>

    

     <table class="table">
        {{-- <tr >
            <th>NV Status</th>
            <td>{{$status ?? ''}}</td>
           
        </tr> --}}

        <tr class="light-blue-bg">
            <th>Type</th>
            <td>@if($nv_type->service_id==1)Material @elseif($nv_type->service_id==2)Service @endif</td>
           
        </tr>

        <tr>
            <th>Department</th>
            <td>{{ $department->name ?? ''}}</td>
        </tr>

        <tr class="light-blue-bg">
            <th>Creation Date</th>
            <td>@if(empty($ini_date->created_at))
                {{' '}}
                @else
                {{ date('d-M-y', strtotime($ini_date->created_at ?? '' )) }}
                <spam>
   ({{ date('H:i', strtotime($ini_date->created_at ?? '' )) }})
               </spam>
               @endif</td>
            
        </tr>
        {{-- @php
            use App\Models\NeedValidation;
            $NVNumer = PoNeedValidationst::where();
        @endphp --}}
        <tr>
            <th>NV Number</th>
            <td>NV/{{ $nv_type->budget_type }}/{{ $nv_type->fiscal_year }}/{{$department->name}}/@if($nv_type->service_id==1)Material/{{$nv_type->id}} @elseif($nv_type->service_id==2)Service/{{$nv_type->id}}@endif </td>
        </tr>
        <tr class="light-blue-bg">
            <th>Description</th>
            <td >{{$ini_date->proposal_name ?? ''}}</td>
        </tr>
        <tr>
            <th>Amount (Rs Lakhs)</th>
            <td>
                @if ($nv_type->service_id == 1)
                    {{ $ini_date->total_budget_both ? (number_format($ini_date->total_budget_both / 100000 , 2, '.', '') . ' Lakhs') : 'N/A' }}
                @elseif ($nv_type->service_id == 2)
                    {{ $ini_date->total_buget ? (number_format($ini_date->total_buget / 100000 , 2, '.', '') . ' Lakhs') : 'N/A' }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        {{-- <tr class="light-blue-bg">
            <th>Available Budget (Rs Lakhs)</th>
                    
            <td>
                @if ($nv_type->service_id == 1)
                    {{ $ini_date->budget_avl ? (number_format($ini_date->budget_avl / 100000 , 2, '.', '') . ' Lakhs') : 'N/A' }}
                @elseif ($nv_type->service_id == 2)
                    {{ $ini_date->budget_available ? (number_format($ini_date->budget_available / 100000 , 2, '.', '') . ' Lakhs') : 'N/A' }}
                @else
                    N/A
                @endif
            </td>
        </tr> --}}
        <tr>
            <th>Initiator Name</th>
            <td>@if(!empty($ini_by)){{ $ini_by->name ?? '' }}@endif</td>
           
        </tr>
        <tr class="light-blue-bg">
            <th>Initiator Contact Number</th>
            <td>{{$ini_by->phone}}</td>
        </tr>
    </table>
    <br>
    <div class="row">
        <p>Please click on the link to open the Need Validation application bses. <br> <a href="https://nv.bsesdelhi.com">https://nv.bsesdelhi.com</a> </p>
     </div>
    <div class="row">
            <!-- <p>Thanks and Regards <br> @if($nv_type->company_id==5) BYPL @else BRPL @endif Need Validation</p> -->
            <p>Thanks and Regards <br> BRPL Need Validation</p>
    </div>

    <div>
        <p style="color: blue !important;">**This email is generated by an automated system. Please do not reply to this message.</p>
    </div>
											 
 
 <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.slim.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
 <body>
</html>