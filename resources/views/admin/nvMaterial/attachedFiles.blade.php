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
     $cm_rate_ref = $material_doc->cm_rate_ref;
     $last_purchase_price = $material_doc->last_purchase_price; 
     $user_estimation = $material_doc->user_estimation; 
     $previous_wo_rc = $material_doc->previous_wo_rc;
     $others = explode(',',$material_doc->others); 
     $cost_calculation_for_service = $material_doc->cost_calculation_for_service;
     $new_product = $material_doc->new_product;
     $previous_work_order = $material_doc->previous_work_order;
     $vend_quatation = $material_doc->vend_quatation; 
     $special_attch = $material_doc->special_attch; 
     $quant_just = $material_doc->quant_just; 
     $derc_stakeholder_approvals = $material_doc->derc_stakeholder_approvals; 
     $consumption_details = $material_doc->consumption_details; 
     $budget_for_both = $material_doc->budget_for_both; 
     $photo_product = $material_doc->photo_product; 
     $material_procurement = $material_doc->material_procurement; 
     $vendor_quatation = $material_doc->vendor_quatation; 
    @endphp
    <!-- First PDF iframe -->
     @if(!empty($cm_rate_ref))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $cm_rate_ref) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($vendor_quatation))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $vendor_quatation) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($last_purchase_price))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $last_purchase_price) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($user_estimation))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $user_estimation) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($previous_wo_rc))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $previous_wo_rc) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($cost_calculation_for_service))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $cost_calculation_for_service) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($new_product))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $new_product) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($previous_work_order))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $previous_work_order) }}" frameborder="0"></iframe>
    @endif

    @if(!empty($vend_quatation))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $vend_quatation) }}" frameborder="0"></iframe>
    @endif

    @if(!empty($special_attch))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $special_attch) }}" frameborder="0"></iframe>
    @endif

    @if(!empty($quant_just))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $quant_just) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($derc_stakeholder_approvals))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $derc_stakeholder_approvals) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($consumption_details))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $consumption_details) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($budget_for_both))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $budget_for_both) }}" frameborder="0"></iframe>
    @endif
    @if(!empty($photo_product))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $photo_product) }}" frameborder="0"></iframe>
    @endif
    @if(count($others)>0)
    @foreach($others as $other)
    @if(!empty($other))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $other) }}" frameborder="0"></iframe>
    @endif
    @endforeach
    @endif
    @if(!empty($material_procurement))
    <iframe class="pdf-iframe" src="{{ asset('materials-doc/' . $material_procurement) }}" frameborder="0"></iframe>
    @endif




</body>
</html>
