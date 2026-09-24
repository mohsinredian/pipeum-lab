<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting"> <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->

    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700" rel="stylesheet">

    <!-- CSS Reset : BEGIN -->
    <style>
        /* What it does: Remove spaces around the email design added by some email clients. */
        /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
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
    </style>

    <!-- CSS Reset : END -->

    <!-- Progressive Enhancements : BEGIN -->
    <style>
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
    </style>


</head>

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


</html>

<table cellpadding="0" cellspacing="0" align="center">
	<tbody>
	   <tr>
		  <td align="center">
			 <table align="center" cellpadding="0" cellspacing="0" width="840">
				<tbody>
				   <tr>
					  <td align="left" style="background-position: center top">
						 <table cellpadding="0" cellspacing="0" width="100%">
							<tbody>
							   <tr>
								  <td>
									 <center><img alt="Logo" src="{{ asset('images/logo-login.png') }}"
										style="width:192px; height:auto;"></center>
								  </td>
							   </tr>
							   <tr>
								  <td width="560" align="center" valign="top">
									 <table cellpadding="0" cellspacing="0" width="100%"
										style="background-color: #f9f9f9f0">
										<tbody>
										   <tr>
											  <td align="center">
												 <h2
													style="
													color: #c29d59;
													margin-top: 20px;
													">
													<span
													   style="
													   background-image: linear-gradient(
													   to bottom,
													   #69c 40%,
													   #316598
													   );
													   padding: 10px;
													   border-top-right-radius: 20px;
													   border-bottom-right-radius: 20px;
													   color: rgb(0, 0, 0);
													   ">BSES
													Online Need Validation</span>
												 </h2>
											  </td>
										   </tr>
										   <tr>
											  <td align="left"
												 style="
												 padding-bottom: 20px;
												 padding-left: 23px;
												 ">
												 <h3 
													style="
													margin-bottom: 10px;
													line-height: 20px;
													">
													Dear Team,
												 </h3 >
												 <h3 style="line-height: 20px">
													Please find update on the status of the NV
													activity for today:
												 </h3>
												 <h3>{{ $p1 ?? '' }}</h3>
												 <h3>{{ $p2 ?? '' }}</h3>
											  </td>
										   </tr>
										</tbody>
									 </table>
								  </td>
							   </tr>
							</tbody>
						 </table>
					  </td>
				   </tr>
				   <tr>
					  <td
						 style="
						 background-color: #f9f9f9f0;
						 padding-bottom: 20px;
						 ">
						 <table cellspacing="0" cellpadding="0" align="center">
							<tbody>
							   <tr>
								  <td align="left"></td>
							   </tr>
							</tbody>
							<tbody>
							   <tr>
								  <td align="center">
									 <table
										style="
										background-color: #c29d5996;
										width: 800px;
										">
										<tbody>
										   <tr
											  style="
											  background: linear-gradient(
											  to bottom,
											  #ec5f67 40%,
											  #b82d35
											  );
											  ">
											  <td colspan="4"
												 style="
												 font-size: 16px;
												 line-height: 150%;
												 padding: 6px 10px 6px;
												 color: #fff;
												 ">
												  <p> NV Status  :  </p>
											  </td>
											
										   </tr>
										   <tr
											  style="
											  border: 1px solid #ccc;
											  background-color: #fff;
											  ">
											  <td colspan="4"
												 style="
												 border-right: 1px solid #ccc;
												 font-size: 14px;
												 line-height: 150%;
												 padding: 6px 10px 6px;
												 ">
												<p> NV Initiated BY  :  @if(!empty($initiated_by)){{ $initiated_by->name ?? '' }}@endif</p>
											  </td>
											  <td colspan="4"
												 style="
												 border-right: 1px solid #ccc;
												 font-size: 14px;
												 line-height: 150%;
												 padding: 6px 10px 6px;
												 ">
												<p> NV Initiated Date  :  @if(empty($initiated_date->created_at))
													{{' '}}
													@else
													{{ date('d-M-y', strtotime($initiated_date->created_at ?? '' )) }}
													<spam>
                                       ({{ date('H:i', strtotime($initiated_date->created_at ?? '' )) }})
									               </spam>
												   @endif
												</p>
											  </td>
										   </tr>
										   @if(!empty($status))
										   <tr
											  style="
											  border: 1px solid #ccc;
											  background-color: #fff;
											  ">
											  <td colspan="4"
												 style="
												 border-right: 1px solid #ccc;
												 font-size: 14px;
												 line-height: 150%;
												 padding: 6px 10px 6px;
												 ">
											
												<p>	NV {{ $status ?? '' }} By  :  @if(!empty($name)){{ $name ?? '' }}@endif</p>
													
													
											  </td>
											  <td colspan="4"
												 style="
												 border-right: 1px solid #ccc;
												 font-size: 14px;
												 line-height: 150%;
												 padding: 6px 10px 6px;
												 ">
												<p>NV {{ $status ?? '' }} Date  :  	 @if(empty($date))
													{{' '}}
													@else
													{{ date('d-M-y', strtotime($date ?? '' )) }}
													<spam>
                                       ({{ date('H:i', strtotime($date ?? '' )) }})
									               </spam>
												   @endif
												   </p>
											  </td>
										   </tr>
										   @endif
										   <!-- <tr
											  style="
											  border: 1px solid #ccc;
											  background-color: #fff;
											  ">
											  <td colspan="4"
												 style="
												 border-right: 1px solid #ccc;
												 font-size: 14px;
												 line-height: 150%;
												 padding: 6px 10px 6px;
												 color: red;
												 font-weight: 600;
												 ">
												 Pending Tickets
											  </td>
											  <td
												 style="
												 font-size: 14px;
												 line-height: 150%;
												 padding: 6px 0px 6px;
												 border-right: 1px solid #ccc;
												 text-align: right;
												 padding-right: 45px;
												 color: red;
												 font-weight: 600;
												 ">
												 59
											  </td>
										   </tr> -->
										</tbody>
									 </table>
								  </td>
							   </tr>
							</tbody>
						 </table>
					  </td>
				   </tr>
				   <tr>
					  <td align="left" style="background-position: center top">
						 <table cellpadding="0" cellspacing="0" width="100%">
							<tbody>
							   <tr>
								  <td width="560" align="center" valign="top">
									 <table cellpadding="0" cellspacing="0" width="100%"
										style="background-color: #f9f9f9f0">
										<tbody>
										   <tr>
											  <td align="left"
												 style="padding-bottom: 20px;padding-left: 23px;">
												 <h4
													style="color:#316598 ;margin-bottom: -20px;
													">
													Please login to access the application:
												 </h4>
												 <h4 style="line-height: 20px; color:blue">
													<a href="https://bses-need-validation.redianglobal.com/admin/auth" target="_blank">https://bses-need-validation.redianglobal.com/admin/auth</a
												 </h4>
											  </td>
										   </tr>
										   <tr>
											  <td align="left"
												 style="padding-bottom: 20px;padding-left: 23px;">
												 <h4
													style="color:#091520 ;margin-bottom: -10px;
													">
													Thanks & Regards,
												 </h4>
												 <h4 style="line-height: 20px; color:rgb(2, 2, 7)">
													BSES IT Team
												 </h4>
											  </td>
										   </tr>
										</tbody>
									 </table>
								  </td>
							   </tr>
							</tbody>
						 </table>
					  </td>
				   </tr>
				</tbody>
			 </table>
		  </td>
	   </tr>
	</tbody>
 </table>
