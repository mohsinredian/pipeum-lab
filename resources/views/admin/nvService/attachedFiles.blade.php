<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Attached Pdf</title>
<style>
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow: auto; /* Allow the body to scroll */
    }
    .pdf-iframe {
        width: 100%;
        height: 100%; /* Fixed height for each iframe */
        border: none; /* Optional: Removes border around iframe */
    }
</style>
</head>
<body>
    @php
    $cm_rate_ref = $service_doc->cm_rate_ref;
    $vendor_quat = $service_doc->vendor_quat;
    $last_purchase_price = $service_doc->last_purchase_price;
    $user_estimation = $service_doc->user_estimation;
    $previous_wo_rc = $service_doc->previous_wo_rc;
    $others = explode(',',$service_doc->others);
    $cost_calculation_for_service = $service_doc->cost_calculation_for_service;
    $past_practice = $service_doc->past_practice;
    $copy_of_previous_work = $service_doc->copy_of_previous_work;
    $copy_of_derc_other = $service_doc->copy_of_derc_other;
    $consuption_details = $service_doc->consuption_details;
    $buget_stmt_for_both = $service_doc->buget_stmt_for_both;
    $photographs_of_product = $service_doc->photographs_of_product;
    $material_procurement = $service_doc->material_procurement;
    $vendor_quatation = $service_doc->vendor_quatation;
    $vend_quatation = $service_doc->vend_quatation;
    $special_attch = $service_doc->special_attch;
    @endphp
    <!-- First PDF iframe -->
     @if(!empty($cm_rate_ref))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $cm_rate_ref) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($vendor_quat))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $vendor_quat) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($last_purchase_price))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $last_purchase_price) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($user_estimation))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $user_estimation) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($previous_wo_rc))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $previous_wo_rc) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($cost_calculation_for_service))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $cost_calculation_for_service) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($past_practice))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $past_practice) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($copy_of_previous_work))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $copy_of_previous_work) }}" frameborder="0"></iframe>
    @endif

    @if(!empty($copy_of_derc_other))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $copy_of_derc_other) }}" frameborder="0"></iframe>
    @endif

    @if(!empty($consuption_details))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $consuption_details) }}" frameborder="0"></iframe>
    @endif

    @if(!empty($buget_stmt_for_both))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $buget_stmt_for_both) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($photographs_of_product))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $photographs_of_product) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($material_procurement))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $material_procurement) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($vendor_quatation))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $vendor_quatation) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($vend_quatation))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $vend_quatation) }}" frameborder="0"></iframe>
    @endif
    @if(count($others)>0)
    @foreach($others as $other)
    @if(!empty($other))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $other) }}" frameborder="0"></iframe>
    @endif
    @endforeach
    @endif
    @if(!empty($special_attch))
    <iframe class="pdf-iframe" src="{{ asset('services-doc/' . $special_attch) }}" frameborder="0"></iframe>
    @endif




</body>
</html>
