$(document).ready(function(){
	
	$.fn.dataTable.ext.errMode = 'throw';
 /// Service BOQ datatable load
	function ServiceBoqDatatable(){
		
		if ($(document).find('#nv_serviceBoq_datatables').length > 0) {
			var nv_id = $('#nv_id').val();
			var service_id = $('#service_id').val();
			// alert(nv_id);
			var dataTable3 = $('#nv_serviceBoq_datatables').DataTable({
				responsive: true,
				processing: true,
				serverSide: true,
				destroy: true,
				"searching": true,
				ajax: {
					data: {
						nv_id: nv_id,
						service_id: service_id,
					},
					url: '/admin/nv_serviceBoq',
		
		
				},
				type: 'get',
				url: '/admin/nv_material/approvedByStatus',
				columns: [{
						data: 'DT_RowIndex',
						name: 'DT_RowIndex',
						orderable: false,
						searchable: false,
						className: "text-center"
					},
					// {
					// 	data: 'id',
					// 	name: 'id',
					// 	className: "text-center"
					// },
					{
						data: 'nv_id',
						name: 'nv_id',
						className: "text-center"
					},
					{
						data: 'service_code',
						name: 'service_code',
						className: "text-center"
					},
					{
						data: 'description',
						name: 'description',
						className: "text-center"
					},
					{
						data: 'uom',
						name: 'uom',
						className: "text-center"
					},
					{
						data: 'rate',
						name: 'rate',
						className: "text-center"
					},
		
					{
						data: 'qty',
						name: 'qty',
						className: "text-center"
					},
					{
						data: 'amount',
						name: 'amount',
						className: "text-center"
					},
		
					{
						data: 'action',
						name: 'action',
						className: "text-center",
						orderable: false,
						// visible: false
					},
		
		
		
		
				],
				"bLengthChange" : false,
				"bInfo":false,
				'columnDefs': [{ 'orderable': false, 'targets': 0 }, { 'visible': false, 'targets': [1], 'orderable': true }],
				'aaSorting': [[1, 'desc']]
			});
			$('#RefreshBtn3').on('click', function(e) {
				e.preventDefault(); // Prevent default link behavior (page reload)
		
				// Reload DataTable
				dataTable3.ajax.reload();
				updateTotalServiceAmount();
		
			});
			function updateTotalServiceAmount() {
				var nv_id = $('#nv_id').val();
				var service_id = $('#service_id').val();
				$.ajax({
					type: 'GET',
					url: '/admin/nv_service/data',
					data: {
						nv_id: nv_id,
						service_id: service_id,
					},
					success: function(response) {
						if(response.success==true){
							$('#total_ser_amo').val(response.total);
						}
						
						
					},
					error: function(error) {
					 
					}
				});
			   
			}
		
		}
	}

	function getPlainTextLength(htmlContent) {
		// Strip HTML tags and get plain text length
		const plainText = htmlContent.replace(/<[^>]*>?/gm, '');
		return plainText.length;
	}
	
	 // Set a timer to auto-save every X seconds
	 var autoSaveInterval = 10; // 60 seconds (adjust as needed)
	 var timerId;
	 var previousFiles = {}; // Store previous uploaded files
 
	 // Function to perform auto-save
	 function autoSave() {
		 // Get the CKEditor field values
		 var just_Prop = editor1.getData();
		 var background = editor2.getData();
		 var broad_just = editor3.getData();

		 var length_just_prop = getPlainTextLength(just_Prop);
		var length_broad_just = getPlainTextLength(broad_just);
		var length_background = getPlainTextLength(background);

		 var benefit = $('#benefit').val();
        var past_practice_text = $('#past_practice_text').val();
	    var proposal_name = $('#proposal_name').val();
		var special_remarks = $('#special_remarks').val();
		var cause_analysis = $('#cause_analysis').val();

		 var derc_approval = $('#derc_approval').val();
		 var derc_ref_no = $('input[name="derc_ref_no"]').val();
		 var derc_app_date = $('input[name="derc_app_date"]').val();

		
		 if (length_just_prop > 2000) {
			$('.just_Prop_error').html('Detailed Justification should not be more than 2000 characters');
			$('.just_Prop_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			
		}else{
			$('.just_Prop_error').html('');
		}
		 if (length_background > 2500) {
			$('.background_error').html('Background  should not be more than 2500 characters');
			$('.background_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			
		}else{
			$('.background_error').html('');
		}
		 if (length_broad_just > 500) {
			$('.broad_just_error').html('Broad Justification should not be more than 500 characters');
			$('.broad_just_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			
		}else{
			$('.broad_just_error').html('');
		}
		 if (past_practice_text.length > 1000) {
			$('.past_practice_text_error').html('Past Practice Followed should not be more than 1000 characters');
			$('.past_practice_text_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			
		}else{
			$('.past_practice_text_error').html('');
		}
	if (proposal_name.length > 200) {
			$('.proposal_name_error').html('Proposal Name should not be more than 200 characters');
			$('.proposal_name_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			
		}else{
			$('.proposal_name_error').html('');
		}
	if (special_remarks.length > 500) {
				$('.special_remarks_error').html('Special Remarks / Any Specific Recommendation Out Plan should not be more than 500 characters');
				$('.special_remarks_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
				
			}else{
				$('.special_remarks_error').html('');
			}
			if (cause_analysis.length > 1000) {
				$('.cause_analysis_error').html('Cost Reduction Plan/Future Phasing Out Plan should not be more than 1000 characters');
				$('.cause_analysis_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
				
			}else{
				$('.cause_analysis_error').html('');
			}
					if (benefit.length > 2500) {
				$('.benefit_error').html('Benefit should not be more than 2500 characters');
				$('.benefit_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
				
			}else{
				$('.benefit_error').html('');
			}
	
		 

		 if (derc_approval == 'Approved') {
			if (derc_ref_no == '' && derc_app_date == '') {
			$('.derc_ref_no_error').html('Please Enter DERC Reference Number');
			$('.app_date_error').html('Please Enter DERC Approval Date');
			$('.derc_details')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			
			}else if(derc_ref_no != '' && derc_app_date == ''){
				$('.derc_ref_no_error').html('');
				$('.app_date_error').html('Please Enter DERC Approval Date');
				$('.derc_details')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
				
			}else if(derc_ref_no == '' && derc_app_date != ''){
				$('.derc_ref_no_error').html('Please Enter DERC Reference Number');
				$('.app_date_error').html('');
				$('.derc_details')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
				
			}
		}else{
			$('.derc_ref_no_error').html('');
				$('.app_date_error').html('');
		}
 
		 // Serialize form data, including CKEditor fields
		 var formData = new FormData($('#create_nv_service')[0]);
		 formData.append('just_Prop', just_Prop); // Add CKEditor content
		 formData.append('background', background); // Add CKEditor content 
		 formData.append('broad_just', broad_just);
 
		 // Check if any file input has changed
		 var currentFiles = {};
		 $('input[type="file"]').each(function() {
			 var fieldName = $(this).attr('name');
			 if (fieldName && $(this).val()) {
				 // File input has a value (file selected)
				 currentFiles[fieldName] = $(this).val();
			 }
		 });
 
		 // Compare current files with previous files
		 for (var fieldName in currentFiles) {
			 if (previousFiles[fieldName] !== currentFiles[fieldName]) {
				 // New file has been uploaded for this field
				 formData.append(fieldName, $('input[name="' + fieldName + '"]')[0].files[0]);
			 }
		 }
 
		 // Send an AJAX request to the Laravel backend to save the data
		 $.ajax({
			 type: 'POST',
			 url:  '/admin/store_service_form', // Replace with your Laravel route
			 data: formData,
			 contentType: false, // Required when sending FormData
			 processData: false, // Required when sending FormData
			 success: function(response) {
				 if(response.service_id != null){
					 $('#service_id').val(response.service_id);
					 // Call the function of Datatable of ServiceBOQ
					 ServiceBoqDatatable();
					 }
				
			 },
			 error: function(error) {
				 console.error('Auto-save failed:', error);
			 }
		 });
 	
		 // Update previousFiles with currentFiles for the next auto-save
		 previousFiles = currentFiles;
	 }

	  // Call the function of Datatable of ServiceBOQ
	  ServiceBoqDatatable();
 
	 // Start auto-save timer
	 timerId = setInterval(autoSave, autoSaveInterval);
 
	 setTimeout(function() {
		 clearInterval(timerId); // Clear initial timer
		 autoSaveInterval = 10000; // 1 minutes in milliseconds
		 timerId = setInterval(autoSave, autoSaveInterval); // Start new timer
	 }, autoSaveInterval); // Wait for the initial interval to pass

	 $('#create_nv_service').change('input, select, textarea',function() {
		autoSave(); 
	  });
	 // Stop auto-save when the user submits the form
	 $('#create_nv_service').submit(function(event) {
		 clearInterval(timerId);
	 });
	
	// Datatable
	if($(document).find('#employee_datatable').length > 0){
		$('#employee_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				// url: '/admin/employees'
			},
			columns: [
				{ 
					data: 'DT_RowIndex', 
					name: 'DT_RowIndex', 
					orderable: false, 
					searchable: false,
					className: "text-center" 
				},
				{
					data: 'id',
					name: 'id',
					className: "text-center"
				},
				{
					data: 'name',
					name: 'name',
					className: "text-center"
				},
				{
					data: 'email',
					name: 'email',
					className: "text-center"
				},
				{
					data: 'phone',
					name: 'phone',
					className: "text-center"
				},
				{
					data: 'division',
					name: 'division',
					className: "text-center"
				},
				{
					data: 'location',
					name: 'location',
					className: "text-center"
				},
				
				{
					data: 'employee_id',
					name: 'emp_id',
					
					className: "text-center"
				},
					{
					data: 'role',
					name: 'role',
					className: "text-center"
				},
			
				{
					data: 'status',
					name: 'status',
					className: "text-center"
				},
				{
					data: 'action',
					name: 'action',
					className: "text-center",
					orderable: false
				}
			],
			'columnDefs': [{ 'orderable': false, 'targets': 0 },{'visible': false, 'targets': [1], 'orderable': true}],
			'aaSorting': [[1, 'desc']]
		});
	}
	jQuery.validator.addMethod(
        "validEmail",
        function (value, element) {
            return this.optional(element) || /\S+@\S+\.\S+/.test(value);
        },
        "Please enter valid email"
    );
	jQuery.validator.addMethod("alpha", function (value, element) {
		return this.optional(element) || /^[a-zA-Z\s']*$/.test(value);
	}, "Invalid input");
	jQuery.validator.addMethod("alphanumeric", function (value, element) {
		return this.optional(element) || /^[a-zA-Z0-9\s']*$/.test(value);
	}, "Invalid input");
	jQuery.validator.addMethod("alphadash", function (value, element) {
		return this.optional(element) || /^[a-zA-Z \s']*$/.test(value);
	}, "Invalid input");

	// var response;
    // $.validator.addMethod(
    //     "unique", 
    //     function(value, element) {
    //         $.ajax({
    //             type: "POST",
    //             url: "http://"+location.host+"/checkUser.php",
    //             data: "dop_ref_no="+value,
    //             dataType:"html",
    //             success: function(msg)
    //             {
    //                 //If username exists, set response to true
    //                 response = ( msg == 'true' ) ? true : false;
    //             }
    //          });
    //         return response;
    //     },
    //     "dop ref no is already taken"
    // );

	// Create role
	$('#create_nv_service, #preview_nvservice ,#serviceboqform', ).validate({
		rules: {
			// dept_id: {
			// 	required: true,
			// 	// maxlength: 50,
			// 	// 
			// },
            // dop_ref_no: {
			// 	required: true,
			// 	maxlength: 50,
			// 	number:true,
			// },
			remark: {
				required: true,
				maxlength: 200,
				
			},
			check_technology: {
				required: true,
				
			},
			
          		
		},
		messages: {
			// dept_id: {
			// 	required: "Please select department",
			// },
            dop_ref_no: {
				required: "Please enter DOP reference number",
				maxlength: "DOP reference number not be more than 50 characters",
				number: "DOP ref no should be numbers only",
				// unique: "dop ref no is already taken"

			},
			remark: {
				required: "Please enter your remark",
				maxlength: "Remark should not be more than 200 characters",
				
			},
			check_technology: {
				required: "Please select technology",
				
			},
			service_code_0: {
				required: "Please enter service code",

			},
            
		},
		errorPlacement: function (error, element) {
			if (element.attr("name") == "logo") {
				// custom error placement
				$(element).closest('.form-group').find('.common-error').html(error.text());
			}
			else {
				// default error placement
				element.after(error);
			}
		}
	});

	$('#create_nv_service').on('submit', function(e){

		var status = $('.submit_btn').val();
		var draft = $('#draft2').val();
		var just_of_proposal = editor1.getData();
		var background =  editor2.getData();
		var broad_just =  editor3.getData();
		var length_just_prop = getPlainTextLength(just_of_proposal);
		var length_broad_just = getPlainTextLength(broad_just);
		var length_background = getPlainTextLength(background);
		var benefit = $('#benefit').val();
        var past_practice_text = $('#past_practice_text').val();
	    var proposal_name = $('#proposal_name').val();
		var special_remarks = $('#special_remarks').val();
		var cause_analysis = $('#cause_analysis').val();
		var bgt_prov = $('#bgt_prov').val();
		var derc_approval = $('#derc_approval').val();
		var derc_ref_no = $('input[name="derc_ref_no"]').val();
		var derc_app_date = $('input[name="derc_app_date"]').val();
		// var just_of_proposal = ClassicEditor.instances.just_of_proposal.getData();
		// var background = ClassicEditor.instances.background.getData();
		e.preventDefault();

		var totalAmount = parseFloat($('#total_buget').val()) || 0;
		var add_budget = parseFloat($('#add_budget').val()) || 0;
		var approved_budget = parseFloat($('#approved_budget').val());
        var budgetAvailable = parseFloat($('#budget_available').val()) || 0;
        let service_amount = 0;
        document.querySelectorAll('input[name="service_amount[]"]').forEach(function(input) {
            service_amount += parseFloat(input.value) || 0;
        });

		var selectedYear = parseInt($("#exampleFormControlSelect").val()) || 0;
		var total_ser_amo = parseFloat($('#total_ser_amo').val()) || 0;
	   		
			var tax_amount1 = parseFloat($('#tax_amount1').val()) || 0;
            var tax_amount2 = parseFloat($('#tax_amount2').val()) || 0;
            var tax_amount3 = parseFloat($('#tax_amount3').val()) || 0;

            // Calculate Year 1 (Calculated but NOT added to final sum as per request)
            var mat1 = parseFloat($('#total_matyear1').val()) || 0;
            total_matyear1 = mat1 + ((mat1 * tax_amount1) / 100);
            // Calculate Year 2 (Calculated but NOT added to final sum)
            if (selectedYear >= 2) {
                var mat2 = parseFloat($('#total_matyear2').val()) || 0;
                total_matyear2 = mat2 + ((mat2 * tax_amount2) / 100);
            }

            // Calculate Year 3 (Calculated but NOT added to final sum)
            if (selectedYear >= 3) {
                var mat3 = parseFloat($('#total_matyear3').val()) || 0;
                total_matyear3 = mat3 + ((mat3 * tax_amount3) / 100);
            }
		// if (!isNaN(total_matyear1) && !isNaN(tax_amount1)) {
		// 	var total_matyear1 = total_matyear1 + ((total_matyear1 * tax_amount1) / 100) ||0;
		// }else{

        //    var total_matyear1 = total_matyear1 || 0;
		// }

		// if (!isNaN(total_matyear2) && !isNaN(tax_amount2)) {
		// 	var total_matyear2 = total_matyear2 + ((total_matyear2 * tax_amount2) / 100) ||0;
		// }else{
        //    var total_matyear2 = total_matyear2 || 0;
		// }

		// if (!isNaN(total_matyear3) && !isNaN(tax_amount3)) {
		// 	var total_matyear3 = total_matyear3 +  ((total_matyear3 * tax_amount3) / 100) ||0;
		// }else{
        //    var total_matyear3 = total_matyear3 || 0;
		// }

		var budget_type = $('#budget_type').val();
		var fiscal_year = $('#fiscal_year').val();
		var service_id = $('#services_id').val();
		var department_id = $('#dept_id').val();
		var nvid = $('#nvid').val();
		var successMessage = "NV/" + budget_type + "/" + fiscal_year + "/" + department_id + "/";
	
		if (service_id == 1) {
			successMessage += "Material/" + nvid;
		} else if (service_id == 2) {
			successMessage += "Service/" + nvid;
		}
       
   $('.error-message').remove();
	var selectedYear = parseInt($("#exampleFormControlSelect").val()) || 0;

    if (selectedYear === 0) {
        $('.select_year_error').html('Please Select Year to Proceed');
        $('#past_3_year_actual_cost_service3')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
        return;
    } else {
        $('.select_year_error').html('');
    }
   if (length_just_prop > 2000) {
	$('.just_Prop_error').html('Detailed Justification should not be more than 2000 characters');
	$('.just_Prop_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	return;
}else{
	$('.just_Prop_error').html('');
}
 if (length_background > 2500) {
	$('.background_error').html('Background  should not be more than 2500 characters');
	$('.background_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	return;
}else{
	$('.background_error').html('');
}
 if (length_broad_just > 500) {
	$('.broad_just_error').html('Broad Justification should not be more than 500 characters');
	$('.broad_just_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	return;
}else{
	$('.broad_just_error').html('');
}
 if (past_practice_text.length > 1000) {
	$('.past_practice_text_error').html('Past Practice Followed should not be more than 1000 characters');
	$('.past_practice_text_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	return;
}else{
	$('.past_practice_text_error').html('');
}
if (proposal_name.length > 200) {
	$('.proposal_name_error').html('Proposal Name should not be more than 200 characters');
	$('.proposal_name_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	return;
}else{
	$('.proposal_name_error').html('');
}
if (special_remarks.length > 500) {
		$('.special_remarks_error').html('Special Remarks / Any Specific Recommendation Out Plan should not be more than 500 characters');
		$('.special_remarks_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.special_remarks_error').html('');
	}
	if (cause_analysis.length > 1000) {
		$('.cause_analysis_error').html('Cost Reduction Plan/Future Phasing Out Plan should not be more than 1000 characters');
		$('.cause_analysis_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.cause_analysis_error').html('');
	}
			if (benefit.length > 2500) {
		$('.benefit_error').html('Benefit should not be more than 2500 characters');
		$('.benefit_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.benefit_error').html('');
	}

   if (derc_approval == 'Approved') {
	if (derc_ref_no == '' && derc_app_date == '') {
	$('.derc_ref_no_error').html('Please Enter DERC Reference Number');
	$('.app_date_error').html('Please Enter DERC Approval Date');
	$('.derc_details')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	return;
	}else if(derc_ref_no != '' && derc_app_date == ''){
		$('.derc_ref_no_error').html('');
		$('.app_date_error').html('Please Enter DERC Approval Date');
		$('.derc_details')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else if(derc_ref_no == '' && derc_app_date != ''){
		$('.derc_ref_no_error').html('Please Enter DERC Reference Number');
		$('.app_date_error').html('');
		$('.derc_details')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}
}else{
	$('.derc_ref_no_error').html('');
	$('.app_date_error').html('');
}

if (totalAmount == 0) {

	Swal.fire({
		text: "Total Amount should not be Zero",
		type: 'error',
		buttonsStyling: false,
		confirmButtonText: "OK",
		confirmButtonClass: "btn font-weight-bold btn-light"
	}).then(function() {
		window.location.reload();
	});

	return; // Stop form submi
}
tot = $('#total_buget').val();

    if(selectedYear == 1){
       console.log(
			Number((total_matyear1).toFixed(2))+'==='+ parseFloat(tot).toFixed(2)
			);
    } else if(selectedYear == 2){
        yearSum = total_matyear1 + total_matyear2;
		console.log(
					Number((yearSum).toFixed(2))+'==='+ parseFloat(tot).toFixed(2)
					);
    } else if(selectedYear == 3){
        yearSum = total_matyear1 + total_matyear2 + total_matyear3;
			console.log(
					Number((yearSum).toFixed(2))+'==='+ parseFloat(tot).toFixed(2)
					);
    }

if(bgt_prov == "Approved"){

	if(selectedYear != 0){
    // 1. Calculate the sum based on the selected year
    var yearSum = 0;
    
    if(selectedYear == 1){
        yearSum = total_matyear1 + service_amount;
    } else if(selectedYear == 2){
        yearSum = total_matyear1 + total_matyear2 + service_amount;
    } else if(selectedYear == 3){
        yearSum = total_matyear1 + total_matyear2 + total_matyear3 + service_amount;
    }

    // 2. Compare values handling decimal precision
    // parseFloat(...).toFixed(2) ensures we compare "10.55" to "10.55" 
    // instead of "10.55" to "10.5499999999"
    if (parseFloat(tot).toFixed(2) != parseFloat(yearSum).toFixed(2)) {
        
        Swal.fire({
            text: "Year Wise Amount should be equal to the Total Service Amount.",
            type: 'error',
            buttonsStyling: false,
            confirmButtonText: "OK",
            confirmButtonClass: "btn font-weight-bold btn-light"
        }).then(function() {
            window.location.reload();
        });

        return; 
    }
}
	if(totalAmount != 0 && isNaN(approved_budget)){
		$('.approved_budget_error').html('Please Enter Approved Budget');
		$('#cost_calculation_for_service')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.approved_budget_error').html('');		
	}
	if (approved_budget > budgetAvailable) {
		Swal.fire({
			text: "Approved Budget should not be greater than Budget Available.",
			type: 'error',
			buttonsStyling: false,
			confirmButtonText: "OK",
			confirmButtonClass: "btn font-weight-bold btn-light"
		}).then(function() {
			window.location.reload();
		});

		return; 
	}
	
	if(approved_budget > (totalAmount)){
		$('.approved_budget_error').html('Approved Budget should not be greater than Total Amount');
		$('#cost_calculation_for_service')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
		}else{
			$('.approved_budget_error').html('');	
		}

		const tolerance = 0.01;
		const calculatedValue = totalAmount - add_budget;

		if (Math.abs(approved_budget - calculatedValue) > tolerance) {
			$('.approved_budget_error').html('Please Check the Calculations and click on <i class="fas fa-sync-alt text-info"></i>');
			$('#cost_calculation_for_service')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			return;
		} else {
			$('.approved_budget_error').html('');
		}
	
	// if(approved_budget !== (totalAmount-add_budget)){
	// 	$('.approved_budget_error').html('Please Check the Calculations and click on <i class="fas fa-sync-alt text-info"></i>');
	// 	$('#cost_calculation_for_service')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	// 	return;
	// 	}else{
	// 		$('.approved_budget_error').html('');	
	// 	}
	 }

		$('.common-error').empty();
		if($('#create_nv_service').valid()){
         $('.pre-loader').show();

		var formData = new FormData($('#create_nv_service')[0]);
		formData.append("status", status);
		formData.append("draft", draft);
		formData.append("just_of_proposal", just_of_proposal);
		formData.append("broad_just", broad_just);
		formData.append("background", background);

			$.ajax({
				data: formData,
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/nv_service/store",
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						
						Swal.fire({
							html: successMessage + " <br>has been submitted successfully",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/needvalidation/list';
						});
						
						 $('#create_nv_service')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_nv_service').find('.'+key+'_error').html(error_msgs[key][0]);
							}
						}

					}
					else if(res.result == 'failure'){
						
						Swal.fire({
			                text: "Something went wrong. Please try again.",
			                type: 'error',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-light"
			            }).then(function() {
							window.location.reload();
						});
					}

					$('.pre-loader').hide();
				},

				error: function(error){

				}
			});
		}
	});

	$('.save_btn').click(function (e) {
		var status = $('.save_btn').val();
		// alert(status);
		var draft = $('#draft1').val();
		var just_of_proposal = editor1.getData();
		var broad_just =  editor3.getData();
		var background =  editor2.getData();
		var length_just_prop = getPlainTextLength(just_of_proposal);
		var length_broad_just = getPlainTextLength(broad_just);
		var length_background = getPlainTextLength(background);
		var benefit = $('#benefit').val();
        var past_practice_text = $('#past_practice_text').val();
	    var proposal_name = $('#proposal_name').val();
		var special_remarks = $('#special_remarks').val();
		var cause_analysis = $('#cause_analysis').val();
		var bgt_prov = $('#bgt_prov').val();
	
		e.preventDefault();
		var totalAmount = parseFloat($('#total_buget').val()) || 0;
		var add_budget = parseFloat($('#add_budget').val()) || 0;
		var approved_budget = parseFloat($('#approved_budget').val());
		var budgetAvailable = parseFloat($('#budget_available').val()) || 0;

		var selectedYear = parseInt($("#exampleFormControlSelect").val()) || 0;
		var total_ser_amo = parseFloat($('#total_ser_amo').val()) || 0;
		var total_matyear1 = parseFloat($('#total_matyear1').val()) || 0;
	    var total_matyear2 = parseFloat($('#total_matyear2').val()) || 0;
		var total_matyear3 = parseFloat($('#total_matyear3').val()) || 0;

		var tax_amount1 = parseFloat($('#tax_amount1').val()) || 0;
	    var tax_amount2 = parseFloat($('#tax_amount2').val()) || 0;
		var tax_amount3 = parseFloat($('#tax_amount3').val()) || 0;

		// if (!isNaN(total_matyear1) && !isNaN(tax_amount1)) {
		// 	var total_matyear1 = total_matyear1 + ((total_matyear1 * tax_amount1) / 100) ||0;
		// }else{
        //    var total_matyear1 = total_matyear1 || 0;
		// }

		// if (!isNaN(total_matyear2) && !isNaN(tax_amount2)) {
		// 	var total_matyear2 = total_matyear2 + ((total_matyear2 * tax_amount2) / 100) ||0;
		// }else{
        //    var total_matyear2 = total_matyear2 || 0;
		// }

		// if (!isNaN(total_matyear3) && !isNaN(tax_amount3)) {
		// 	var total_matyear3 = total_matyear3 +  ((total_matyear3 * tax_amount3) / 100) ||0;
		// }else{
        //    var total_matyear3 = total_matyear3 || 0;
		// }

		var budget_type = $('#budget_type').val();
		var fiscal_year = $('#fiscal_year').val();
		var service_id = $('#services_id').val();
		var department_id = $('#dept_id').val();
		var nvid = $('#nvid').val();
		var successMessage = "NV/" + budget_type + "/" + fiscal_year + "/" + department_id + "/";
	
		if (service_id == 1) {
			successMessage += "Material/" + nvid;
		} else if (service_id == 2) {
			successMessage += "Service/" + nvid;
		}
    //  alert(totalAmount);
   $('.error-message').remove();

   if (length_just_prop > 2000) {
	$('.just_Prop_error').html('Detailed Justification should not be more than 2000 characters');
	$('.just_Prop_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	return;
}else{
	$('.just_Prop_error').html('');
}
 if (length_background > 2500) {
	$('.background_error').html('Background  should not be more than 2500 characters');
	$('.background_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	return;
}else{
	$('.background_error').html('');
}
 if (length_broad_just > 500) {
	$('.broad_just_error').html('Broad Justification should not be more than 500 characters');
	$('.broad_just_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	return;
}else{
	$('.broad_just_error').html('');
}
 if (past_practice_text.length > 1000) {
	$('.past_practice_text_error').html('Past Practice Followed should not be more than 1000 characters');
	$('.past_practice_text_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	return;
}else{
	$('.past_practice_text_error').html('');
}
if (proposal_name.length > 200) {
	$('.proposal_name_error').html('Proposal Name should not be more than 200 characters');
	$('.proposal_name_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	return;
}else{
	$('.proposal_name_error').html('');
}
if (special_remarks.length > 500) {
		$('.special_remarks_error').html('Special Remarks / Any Specific Recommendation Out Plan should not be more than 500 characters');
		$('.special_remarks_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.special_remarks_error').html('');
	}
	if (cause_analysis.length > 1000) {
		$('.cause_analysis_error').html('Cost Reduction Plan/Future Phasing Out Plan should not be more than 1000 characters');
		$('.cause_analysis_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.cause_analysis_error').html('');
	}
			if (benefit.length > 2500) {
		$('.benefit_error').html('Benefit should not be more than 2500 characters');
		$('.benefit_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.benefit_error').html('');
	}

   var derc_approval = $('#derc_approval').val();
		var derc_ref_no = $('input[name="derc_ref_no"]').val();
		var derc_app_date = $('input[name="derc_app_date"]').val();

		if (derc_approval == 'Approved') {
			if (derc_ref_no == '' && derc_app_date == '') {
			$('.derc_ref_no_error').html('Please Enter DERC Reference Number');
			$('.app_date_error').html('Please Enter DERC Approval Date');
			$('.derc_details')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			return;
			}else if(derc_ref_no != '' && derc_app_date == ''){
				$('.derc_ref_no_error').html('');
				$('.app_date_error').html('Please Enter DERC Approval Date');
				$('.derc_details')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
				return;
			}else if(derc_ref_no == '' && derc_app_date != ''){
				$('.derc_ref_no_error').html('Please Enter DERC Reference Number');
				$('.app_date_error').html('');
				$('.derc_details')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
				return;
			}
		}else{
			$('.derc_ref_no_error').html('');
			$('.app_date_error').html('');
		}

	

		if(bgt_prov == "Approved"){
			if(selectedYear != 0){
			// if(selectedYear == 1){
            //  if(total_ser_amo != total_matyear1){
			// 	Swal.fire({
			// 		text: "Year Wise Amount should be equal to the Total Service Amount.",
			// 		type: 'error',
			// 		buttonsStyling: false,
			// 		confirmButtonText: "OK",
			// 		confirmButtonClass: "btn font-weight-bold btn-light"
			// 	}).then(function() {
			// 		window.location.reload();
			// 	});
		
			// 	return; 
			//  }
			// }else if(selectedYear == 2){
			// 	if(total_ser_amo != (total_matyear1 + total_matyear2)){
			// 		Swal.fire({
			// 			text: "Year Wise Amount should be equal to the Total Service Amount.",
			// 			type: 'error',
			// 			buttonsStyling: false,
			// 			confirmButtonText: "OK",
			// 			confirmButtonClass: "btn font-weight-bold btn-light"
			// 		}).then(function() {
			// 			window.location.reload();
			// 		});
			
			// 		return; 
			// 	 }
			// }else if(selectedYear == 3){
			// 	if(total_ser_amo != (total_matyear1 + total_matyear2 + total_matyear3)){
			// 		Swal.fire({
			// 			text: "Year Wise Amount should be equal to the Total Service Amount.",
			// 			type: 'error',
			// 			buttonsStyling: false,
			// 			confirmButtonText: "OK",
			// 			confirmButtonClass: "btn font-weight-bold btn-light"
			// 		}).then(function() {
			// 			window.location.reload();
			// 		});
			
			// 		return; 
			// 	 }
			// }

			// Commented on 25 September by Pooja
			// var year3 = ($("#year3").val());
			// if(approved_budget > (total_matyear1)){
			// $('.approved_budget_error').html('Approved Budget should not be greater than FY '+year3+' Amount');
			// $('#cost_calculation_for_service')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			// return;
			// }else{
			// 	$('.approved_budget_error').html('');
			// }
		}
			// Commented on 25 September by Pooja
			// if(totalAmount != 0 && isNaN(approved_budget)){
			// 	$('.approved_budget_error').html('Please Enter Approved Budget');
			// 	$('#cost_calculation_for_service')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			// 	return;
			// }else{
			// 	$('.approved_budget_error').html('');		
			// }
			// if (approved_budget > budgetAvailable) {
			// 	Swal.fire({
			// 		text: "Approved Budget should not be greater than Budget Available.",
			// 		type: 'error',
			// 		buttonsStyling: false,
			// 		confirmButtonText: "OK",
			// 		confirmButtonClass: "btn font-weight-bold btn-light"
			// 	}).then(function() {
			// 		window.location.reload();
			// 	});
		
			// 	return; 
			// }
			// if(approved_budget > (totalAmount)){
			// 	$('.approved_budget_error').html('Approved Budget should not be greater than Total Amount');
			// 	$('#cost_calculation_for_service')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			// 	return;
			// 	}else{
			// 		$('.approved_budget_error').html('');	
			// 	}
			
			// if(approved_budget !== (totalAmount-add_budget)){
			// 	$('.approved_budget_error').html('Please Check the Calculations and click on <i class="fas fa-sync-alt text-info"></i>');
			// 	$('#cost_calculation_for_service')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			// 	return;
			// 	}else{
			// 		$('.approved_budget_error').html('');	
			// 	}
			 }
		$('.common-error').empty();
		if($('#create_nv_service').valid()){

			$('.pre-loader').show();

			var formData = new FormData($('#create_nv_service')[0]);
			formData.append("status", status);
			formData.append("draft", draft);
			formData.append("just_of_proposal", just_of_proposal);
			formData.append("broad_just", broad_just);
			formData.append("background", background);

			$.ajax({
				data:formData,
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/nv_service/store",
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						
						Swal.fire({
							html: successMessage + " <br>has been saved successfully",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/needvalidation/list';
						});
						
						 $('#create_nv_service')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_nv_service').find('.'+key+'_error').html(error_msgs[key][0]);
							}
						}

					}
					else if(res.result == 'failure'){
						
						Swal.fire({
			                text: "Something went wrong. Please try again.",
			                type: 'error',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-light"
			            }).then(function() {
							window.location.reload();
						});
					}

					$('.pre-loader').hide();
				},

				error: function(error){

				}
			});
		}
	});
	// Edit role
	$('#edit_employee_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_employee_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_employee_form')[0]),
				type: 'post',
				url: "/admin/employees/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Employee Updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/employees';
						});
						
						$('#edit_employee_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_employee_form').find('.'+key+'_error').html(error_msgs[key][0]);
							}
						}

					}
					else if(res.result == 'failure'){
						
						Swal.fire({
			                text: "Something went wrong. Please try again.",
			                type: 'error',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-light"
			            }).then(function() {
							window.location.reload();
						});
					}

					$('.pre-loader').hide();
				},

				error: function(error){

				}
			});
		}
	});


	$('.delete_btn').click(function (e) {
		e.preventDefault();
		var just_Prop = editor1.getData();
		var broad_just = editor3.getData();
		var background =  editor2.getData();
		var bgt_prov = $('#bgt_prov').val();
         var totalAmount = $('#total_mat_mat').val();
		
         var budgetAvailable = $('#budget_avl').val();
		
		 budgetAvailable = parseFloat(budgetAvailable);

		 var totalserviceAmount = $('#total_ser_amo').val();
	
         var serbudgetAvailable = $('#ser_budget_avl').val();
		 
		 serbudgetAvailable = parseFloat(serbudgetAvailable);
		 var status = $('.save_btn').val();
		 var draft = $('#draft1').val();
		 var nv_id = $("#nv_id").val();
		 var formData = new FormData($('#create_nv_material')[0]);
		 formData.append("status", status);
		 formData.append("draft", draft);
		 formData.append("just_Prop", just_Prop);
		 formData.append('broad_just', broad_just);
		
		 var budget_type = $('#budget_type').val();
			var fiscal_year = $('#fiscal_year').val();
			var service_id = $('#services_id').val();
			var department_id = $('#dept_id').val();
		 var successMessage = "NV/" + budget_type + "/" + fiscal_year + "/" + department_id + "/";
		 if (service_id == 1) {
			successMessage += "Material/" + nv_id;
		} else if (service_id == 2) {
			successMessage +=  "Service/" + nv_id;
		}
		
		
		Swal.fire({
			title: "Do You Want To Delete The NV <br> "+ " " + "(" + successMessage+ ")" + "?",
			type: "question",
			showCancelButton: true,
			confirmButtonColor: "#d33",
			cancelButtonColor: "#3085d6",
			confirmButtonText: "Yes"
		}).then((result) => {
			if (result.value) {
				// Proceed with AJAX request if user confirms
				// if ($('#preview_nvmaterial').valid()) {
					$('.pre-loader').show();
	
					$.ajax({
						data: formData,
						cache: false,
							processData: false,
							contentType: false,
							type: 'get',
							url: "/admin/nv_service/delete/" + nv_id,
							success: function (response) {
								var res = response;
								
								
									Swal.fire({
										html: successMessage +  " <br>Has Been Deleted Successfully",
										type: 'success',
										buttonsStyling: false,
										confirmButtonText: "Ok",
										confirmButtonClass: "btn font-weight-bold btn-primary"
									}).then(function () {
										window.location = '/admin/needvalidation/list';
									});
	
							$('.pre-loader').hide();
						},
						error: function (error) {
							// Handle error
						}
					});
				// }
			}
		});
	});	
	
	$(document).on('click', '.delete_employee', function () {

		Swal.fire({
			title: 'Are you sure?',
			// text: "You won't be able to revert this!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes'
		}).then((result) => {
			if (result.value) {
				$('.pre-loader').show();

				let employee_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'employee_id': employee_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/employees/delete/'+employee_id,

					success: function (response) {
						let res = response;

						if (res.result == 'success') {

							Swal.fire({
								text: res.msg,
								type: "success",
								buttonsStyling: false,
								confirmButtonText: "OK",
								confirmButtonClass: "btn font-weight-bold btn-primary"
							}).then(function () {
								window.location.reload();
							});
						}
						else if (res.result == 'failure') {

							Swal.fire({
								text: "Something went wrong. Please try again.",
								type: "error",
								buttonsStyling: false,
								confirmButtonText: "OK",
								confirmButtonClass: "btn font-weight-bold btn-light"
							}).then(function () {
								window.location.reload();
							});
						}

						$('.pre-loader').hide();
					},

					error: function (error) {

					}
				});
			}
		});
	});


	$('#division').on('change', function () {

		division = $(this).val();
		$('.pre-loader').show();
		$.ajax({
			data: {
				division: division,
				_token: $('input[name="_token"]').val()
			},
			type: 'post',
			url: '/admin/employees/location',
			async: false,
			success: function (response) {
				if (response.result == 'success') {
					let locations = response.data;
                    $("#location").empty();
                    $("#location").append(
                        '<option value="">Select location</option>'
                    );

                    $.each(locations, function (i, location) {
                        $("#location").append(
                            $("<option></option>")
                                .attr("value", location.id)
                                .text(location.name)
                        );
                    });
				}
				else if (response.result == 'failure') {
					Swal.fire({
						text: "Something went wrong. Please try again.",
						type: 'error',
						buttonsStyling: false,
						confirmButtonText: "OK",
						confirmButtonClass: "btn font-weight-bold btn-light"
					}).then(function () {
						window.location.reload();
					});
				}

				$('.pre-loader').hide();
			},
    
			error: function (error) {

			}
		});
	});

// Approved and Reject

var service_id = window.location.pathname.split('/')[4];
var nv_id = $('#nv_id').val();
var service_id = $('#service_id').val();


$('.approve-button').click(function () {
    var budget_type = $('#budget_type').val();
    var fiscal_year = $('#fiscal_year').val();
    var service_id = $('#service_id').val();
	var services_id = $('#services_id').val();
    var department_id = $('#department_name').val();
    var nvid = $('#nvid').val();
    var successMessage = "NV/" + budget_type + "/" + fiscal_year + "/" + department_id + "/";

    if (services_id == 1) {
        successMessage += "Material/" + nvid;
    } else if (services_id == 2) {
        successMessage += "Service/" + nvid;
    }

	var status_id = $('.approve-button').val();
	var remark = $('textarea[name="remark"]').val();
	// var check_technology = $('input[name="check_technology"]:checked').val();
	var check_ceonm2 = $('input[name="check_ceonm2"]:checked').val();
	var derc_info = $('input[name="derc_info"]').val();

	// Validate the remark
	if (remark.trim() === '') {
		// $('.remark_error').html('Please enter your remark');
		Swal.fire({
			text: "Please Enter Approval & Rejection Remarks",
			type: 'error',
			buttonsStyling: false,
			confirmButtonText: "OK",
			confirmButtonClass: "btn font-weight-bold btn-light"
		}).then(function() {
			// window.location.reload();
		});
	
		return; 
	} else if (remark.length > 200) {
		// $('.remark_error').html('Remark should not be more than 200 characters');
		Swal.fire({
			text: "Approval & Rejection Remarks should not be more than 200 characters",
			type: 'error',
			buttonsStyling: false,
			confirmButtonText: "OK",
			confirmButtonClass: "btn font-weight-bold btn-light"
		}).then(function() {
			// window.location.reload();
		});
	
		return; 
	} else {
		$('.remark_error').html('');
	}

	var formData = new FormData();
	formData.append('approval_attachements', $('#approval_attachements')[0].files[0]);
	formData.append('status_id', status_id);
	formData.append('service_id', service_id);
	formData.append('nv_id', nvid);
	formData.append('remark', remark);
	// formData.append('check_technology', check_technology);
	formData.append('check_ceonm2', check_ceonm2);
	formData.append('derc_info', derc_info);
	formData.append('_token', $('input[name="_token"]').val());
	formData.append('transfer_to_nominee1', $('input[name="transfer_to_nominee1"]:checked').val());

    // Use SweetAlert for confirmation
    Swal.fire({
        title: "Do You Want To Approve The NV <br>"+ " " + "(" + successMessage + ")" + "?",
        type: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Proceed"
    }).then((result) => {
        if (result.value) {
        if ($('#preview_nvservice').valid()) {
                $('.pre-loader').show();

                $.ajax({
					data: formData, 
                    type: 'POST',
                    url: '/admin/nv_service/approvedByStatus',
					processData: false, 
					contentType: false, 
                    async: false,
                    success: function (response) {
                        // Handle response
                        if (response.result == 'success') {
                            Swal.fire({
                                html: successMessage + " <br>Has Been Approved",
                                type: 'success',
                                buttonsStyling: false,
                                confirmButtonText: "OK",
                                confirmButtonClass: "btn font-weight-bold btn-primary"
                            }).then(function () {
								if(response.sign_url != ''){
									window.location.href = response.sign_url;
								}else{
									window.location.reload();
								}
                            });

                            $('#preview_nvservice')[0].reset();
                        } else if (response.result == 'error') {
                            let error_msgs = response.msg;
                            for (let key in error_msgs) {
                                if (error_msgs.hasOwnProperty(key)) {
                                    $('#preview_nvservice').find('.' + key + '_error').html(error_msgs[key][0]);
                                }
                            }
                        } else if (response.result == 'failure') {
                            Swal.fire({
                                text: "Something went wrong. Please try again.",
                                type: 'error',
                                buttonsStyling: false,
                                confirmButtonText: "OK",
                                confirmButtonClass: "btn font-weight-bold btn-light"
                            }).then(function () {
                                window.location.reload();
                            });
                        }

                        $('.pre-loader').hide();
                    },
                    error: function (error) {
                        // Handle error
                    }
                });
            }
        }
    });
});



$('.reject-button').click(function () {
    var budget_type = $('#budget_type').val();
	var check_ceonm2 = $('input[name="check_ceonm2"]:checked').val();
    var fiscal_year = $('#fiscal_year').val();
    var service_id = $('#service_id').val();
	var services_id = $('#services_id').val();
    var department_id = $('#department_name').val();
    var nvid = $('#nvid').val();
    var successMessage = "NV/" + budget_type + "/" + fiscal_year + "/" + department_id + "/";

    if (services_id == 1) {
        successMessage += "Material/" + nvid;
    } else if (services_id == 2) {
        successMessage += "Service/" + nvid;
    }

	var status_id = $('.reject-button').val();
	var remark = $('textarea[name="remark"]').val();

	// Validate the remark
	if (remark.trim() === '') {
		// $('.remark_error').html('Please enter your remark');
		Swal.fire({
			text: "Please Enter Approval & Rejection Remarks",
			type: 'error',
			buttonsStyling: false,
			confirmButtonText: "OK",
			confirmButtonClass: "btn font-weight-bold btn-light"
		}).then(function() {
			// window.location.reload();
		});
	
		return; 
	} else if (remark.length > 200) {
		// $('.remark_error').html('Remark should not be more than 200 characters');
		Swal.fire({
			text: "Approval & Rejection Remarks should not be more than 200 characters",
			type: 'error',
			buttonsStyling: false,
			confirmButtonText: "OK",
			confirmButtonClass: "btn font-weight-bold btn-light"
		}).then(function() {
			// window.location.reload();
		});
	
		return; 
	} else {
		$('.remark_error').html('');
	}

	var formData = new FormData();
	formData.append('approval_attachements', $('#approval_attachements')[0].files[0]);
	formData.append('status_id', status_id);
	formData.append('service_id', service_id);
	formData.append('nv_id', nvid);
	formData.append('remark', remark);
	formData.append('check_ceonm2', check_ceonm2);
	// formData.append('check_technology', check_technology);
	// formData.append('derc_info', derc_info);
	formData.append('_token', $('input[name="_token"]').val());

    // Use SweetAlert for confirmation
    Swal.fire({
        title: "Do You Want To Reject The NV <br>" + " " + "(" + successMessage  + ")" + "?",
        type: "question",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Proceed"
    }).then((result) => {
        if (result.value) {
         if ($('#preview_nvservice').valid()) {
                $('.pre-loader').show();

                $.ajax({
					data: formData, 
                    type: 'POST',
                    url: '/admin/nv_service/approvedByStatus',
					processData: false, 
					contentType: false, 
                    async: false,
                    success: function (response) {
                        if (response.result == 'success') {
                            Swal.fire({
                                html: successMessage + " <br>Has Been Rejected",
                                type: 'success',
                                buttonsStyling: false,
                                confirmButtonText: "OK",
                                confirmButtonClass: "btn font-weight-bold btn-primary"
                            }).then(function () {
								if(response.sign_url != ''){
									window.location.href = response.sign_url;
								}else{
									window.location.reload();
								}
                            });
                        } else if (response.result == 'failure') {
							Swal.fire({
								text: "Something went wrong. Please try again.",
								type: 'error',
								buttonsStyling: false,
								confirmButtonText: "OK",
								confirmButtonClass: "btn font-weight-bold btn-light"
							}).then(function () {
								window.location.reload();
							});
						}

                        $('.pre-loader').hide();
                    },
                    error: function (error) {
                        // Handle error
                    }
                });
            }
        }
    });
});


$('.save-button').click(function(){
	var dop_ref_no = $('#dop_ref_no').val();
   var status_id = $('.save-button').val();
	var editor1 = CKEDITOR.instances.editor1.getData();
	var users_name = $('#clar_fy').val();
	
	// alert(editor1);
	// return false;
	// if($('#preview_nvservice').valid()){
	$('.pre-loader').show();
	$.ajax({
		data: {
			status_id: status_id,
			service_id : service_id,
			nv_id : nv_id,
			dop_ref_no : dop_ref_no,
			users_name : users_name,
			editor1:editor1,
			_token: $('input[name="_token"]').val()
		},
		type: 'get',
		url: '/admin/nv_service/approvedByStatus',
		async: false,
		success: function (response) {
			// alert(response.result);
			if (response.result == 'success') {
					Swal.fire({
			text: "Your NV has been saved successfully ",
			type: 'success',
			buttonsStyling: false,
			confirmButtonText: "OK",
			confirmButtonClass: "btn font-weight-bold btn-primary"
		}).then(function () {
			window.location.reload();
		});
	}

	$('.pre-loader').hide();
},
error: function (error) {

}
});
	// }
});


});


// $(document).ready(function() {
// 	var max_fields = 3;
// 	var x = 1;
//     $("body").on("click",".add-more",function(){ 
// 		if(x < max_fields){
// 			x++;
//         var html = $(".after-add-more").first().clone();
//           $(html).find(".change").html("<label for=''>&nbsp;</label><a class='btn btn-danger remove'>- Remove</a>");
//         $(".after-add-more").last().after(html);
// 		}
//     });

//     $("body").on("click",".remove",function(){ 
//         $(this).parents(".after-add-more").remove();x--;
//     });
// });
$(document).ready(function() {
    var max_fields      = 3; //maximum input boxes allowed
    var wrapper         = $(".my-class"); //Fields wrapper
    var add_button      = $(".add-more"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class=""> <div class="row "><div class="col-xl-3 col-lg-3 col-md-6 mt-2"><input type="text" class="form-control"name="past_3_year_actual_cost_fy[]"id="past_3_year_actual_cost_fy"value=""placeholder="FY"></div><div class="col-xl-3 col-lg-3 col-md-6 mt-2"><input type="text" class="form-control"name="past_3_year_actual_cost[]"id="past_3_year_actual_cost"placeholder="Cost"value=""></div><div class="col-xl-3 col-lg-3 col-md-6 mt-2"><input type="text" class="form-control"name="past_3_year_actual_cost_service[]"id="past_3_year_actual_cost_service"value=""placeholder="Services"></div><label for="">&nbsp;</label><a class=" btn btn-success mt-2 remove">- Remove</a></div></div>'); //add input box
       
			// $(".my-class").attr('disabled');
		
		}
    });
    
    $(wrapper).on("click",".remove", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});  
$(document).ready(function() {
    var max_fields      = 3; //maximum input boxes allowed
    var wrapper         = $(".my-class"); //Fields wrapper
    var add_button      = $(".add-more-third"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
			$(wrapper).append('<div class=""> <div class="row "><div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2"><div class="form-group"> <label for="exampleFormControlInput1">Implementation Period From</label> <input type="date" class="form-control" id="implementation_period_from" name="imp_from[]" value="" placeholder="Enter Benefit"></div></div><div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2"><div class="form-group"><label for="exampleFormControlInput1">Implementation Period To</label><input type="date" class="form-control" id="implementation_period_to" name="imp_to[]" value="" placeholder="Enter Benefit"></div></div><div class="col-12  mb-2"><div class="form-group"><label for="exampleFormControlInput1">Implementation Plan Year Wise(Max 2500 Characters)</label><textarea name="imp_plan[]" id="implementation_plan_year_wise" cols="2" rows="2" class="form-control" value="" placeholder=" Enter Implementation Plan Year Wise"></textarea></div></div><label for="">&nbsp;</label><a class=" btn btn-success mt-2 remove">- Remove</a></div></div>'); //add input box
		 }});
    
    $(wrapper).on("click",".remove", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
}); 





// $('#create_nv_service').on('keyup keypress', function(e) {
// 	var keyCode = e.keyCode || e.which;
// 	if (keyCode === 13) { 
// 	  e.preventDefault();
// 	  return false;
// 	}
//   });
  $(document).ready(function () {
	var max_fields = 10; //maximum input boxes allowed
	var wrapper = $(".my-class-scheme-service"); //Fields wrapper
	var add_button = $(".add-more-button_service"); 
	

	var x = 1; //initlal text box count
	$(add_button).click(function (e) {
		// alert(1);
		e.preventDefault();
		if (x < max_fields) { //max input box allowed
			x++; //text box increment
			$(wrapper).append('<div class=""> \
								<div class="row">\
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">\
										<div class="form-group">\
											<input type="text" class="form-control decimal" id="service_amount'+x+'" name="service_amount[]" placeholder="Amount" onchange="store_service_data(service_amount'+x+',service_description'+x+')">\
										</div>\
									</div>\
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">\
										<div class="form-group">\
											<input type="text" class="form-control"id="service_description'+x+'" name="service_description[]"placeholder="Descripition">\
										</div>\
									</div>\
									<label for="">&nbsp;</label><a class=" btn btn-success remove" style="height:37px;width:130px;border: 1px solid #316598;" id="service_remove" onclick="remove_ser_data(service_amount'+x+')">- Remove</a>\
								</div>\
							</div>');
			
		}
	});

	$(wrapper).on("click", ".remove", function (e) { //user click on remove text
		e.preventDefault(); $(this).parent('div').remove(); x--;
	})
	$(wrapper).on("keydown", "input[type='text']", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            return false;
        }
    });
});
$(document).on("click", "#serviceBoqUploaded", function () {
	var selectedYear = parseInt($("#exampleFormControlSelect").val()) || 0;

    if (selectedYear === 0) {
        $('.select_year_error').html('Please Select Year to Proceed');
        $('#past_3_year_actual_cost_service3')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
        return;
    } else {
        $('.select_year_error').html('');
    }
	var fileInput = $("#serviceboq1")[0].files[0];
	var nv_id = $('#nv_id').val();
	var service_id = $('#service_id').val();

	if (!fileInput) {
		swal({
			title: "Error!",
			text: "Please select a file to upload.",
			icon: "error",
			button: "OK",
		});
		return;
	}
	var formData = new FormData();
	formData.append("serviceboq", fileInput);
	formData.append("nv_id", nv_id);
	formData.append("service_id", service_id);

	var totalAmountService = $('#total_ser_amo').val() || 0;
	

	var total_budget = $('#total_buget').val() || 0;
	total_budget = parseFloat(total_budget);


	$.ajax({
		url: "/admin/nv_service/bulkServiceProviderStore",
		type: "POST",
		data: formData,
		dataType: "json",
		processData: false,
		contentType: false,
		success: function (response) {
			console.log(response.data);
			var data=  response.data;
			// var amount = data['rows'][0]['amount'];
				var rows = data.rows; 
				var totalAmountboq = 0; 
				for (var i = 0; i < rows.length; i++) {
					var amount = rows[i].amount; 
					totalAmountboq += parseFloat(amount); 
					
				
				}
			
			//  alert(total);
			swal({
				title: "Success!",
				text: "Your file has been imported.",
				icon: "success",
				button: "OK",
			}).then(function () {
				fetchDataservice();
				window.location.reload();
				function fetchDataservice() {
					$.ajax({
						url: "/admin/nv_serviceBoq",
						type: "GET",
						success: function (fetchedData) {
							var table = $('#nv_serviceBoq_datatables').DataTable();
							table.clear().draw();
							table.rows.add(fetchedData).draw();
						},
						error: function (xhr, textStatus, errorThrown) {
							console.error("There was a problem fetching the data:", errorThrown);
						}
					});
				}

				
			});
			
			$('#serviceboq1').val('');
			if (totalAmountService !== '' && totalAmountboq !== '' && !isNaN(totalAmountboq) && !isNaN(totalAmountService)) {
				// Use parseFloat to handle decimal values
				var total22_ser = parseFloat(totalAmountService) + parseFloat(totalAmountboq);
				
				// Check if the result is a valid number
				if (!isNaN(total22_ser)) {
					$('#total_ser_amo').val(total22_ser);
				}
			}
			
			
			
			var total23_ser = parseFloat(total_budget) + parseFloat(total22_ser);
			// alert(total23_ser);
			$('#total_service').val(total23_ser);
			$('#total_buget').val(total23_ser);

			
		},
		error: function (xhr, textStatus, errorThrown) {
			console.error("There was a problem with the AJAX operation:", errorThrown);

			if (xhr.responseJSON && xhr.responseJSON.DuplicateError) {
				var errorMessage = xhr.responseJSON.DuplicateError;
				swal({
					title: "Finding Duplicates Numbers Errors!",
					text: errorMessage,
					icon: "error",
					button: "OK",
				}).then(function () {
					location.reload();
				});
			} else {
				swal({
					title: "Error!",
					text: "Something went wrong. Please try again later.",
					icon: "error",
					button: "OK",
				});
			}
		},
	});
});
$(document).ready(function () {
	$.ajaxSetup({
		headers: {
			"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
		},
	});

	fetchDataservice();
	function fetchDataservice() {
		$.ajax({
			url: "/admin/nv_serviceBoq",
			type: "GET",
			success: function (fetchedData) {
				var table = $('#nv_serviceBoq_datatables').DataTable();
				table.clear().draw();
				table.rows.add(fetchedData).draw();
			},
			error: function (xhr, textStatus, errorThrown) {
				console.error("There was a problem fetching the data:", errorThrown);
			}
		});
	}

	
});

$(document).on("click", "#serviceBoqsaves", function () {

	//  $('.save_service_button').click(function(){
    var selectedYear = parseInt($("#exampleFormControlSelect").val()) || 0;

    if (selectedYear === 0) {
        $('.select_year_error').html('Please Select Year to Proceed');
        $('#past_3_year_actual_cost_service3')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
        return;
    } else {
        $('.select_year_error').html('');
    }
	var nv_id = $('#nv_id').val();
	var service_id = $('#service_id').val();
	var service_code_0 = $('#service_code_0').val();
	var ser_des_0 = $('#ser_des_0').val();
	var ser_uom_0 = $('#ser_uom_0').val();
	var ser_rate = $('#ser_rate').val();
	var ser_quantity = $('#ser_quantity').val();
	var ser_total_amount = $('#ser_total_amount').val();
	var ttlamt_ser= $('#total_ser_amo').val();
	
	// alert(ttlamt_ser1);
	
	//    alert(nv_id);total_ser_amo

	var formData = new FormData();
	formData.append("nv_id", nv_id);
	formData.append("service_id", service_id);
	formData.append("service_code_0", service_code_0);
	formData.append("ser_des_0", ser_des_0);
	formData.append("ser_uom_0", ser_uom_0);
	formData.append("ser_rate", ser_rate);
	formData.append("ser_quantity", ser_quantity);
	formData.append("ser_total_amount",ser_total_amount)
	// if ($('#serviceboqform').valid()) {

	$('.pre-loader').show();
	$.ajax({
		data: formData,
		type: 'POST',
		url: '/admin/nv_service/serviceBoqStore',
		async: false,
		dataType: "json",
		processData: false,
		contentType: false,
		success: function (data) {
			var res = data;
		console.log(data);
		// alert(ser_total_amount);
		
			if (res.result == 'success') {
				Swal({
					title: "Success!",
					text: "Your Service BOQ is Successfully Saved.",
					icon: "success",
					button: "OK",
			}).then(function () {
				
				fetchDataservicesave();
				window.location.reload();
				function fetchDataservicesave() {
					$.ajax({
						url: "/admin/nv_serviceBoq",
						type: "GET",
						success: function (fetchedData) {
							var table = $('#nv_serviceBoq_datatables').DataTable();
							table.clear().draw();
							table.rows.add(fetchedData).draw();
						},
						error: function (xhr, textStatus, errorThrown) {
							console.error("There was a problem fetching the data:", errorThrown);
						}
					});
				}
				
			});
			$("#service_code_0").val('');
			$("#ser_rate").val('');
			$("#ser_quantity").val('');
			// ttlamt_ser = ttlamt_ser.replace(/[^a-zA-Z0-9_ ]/g, "");
			ttlamt_ser= parseFloat(ttlamt_ser)+parseFloat(ser_total_amount);
			var tot_amt = $('#total_buget').val() || 0;
		   var tot1 = parseFloat(tot_amt) + parseFloat(ser_total_amount);
	
	
	$('#total_buget').val(tot1);
	$('#total_ser_amo').val(ttlamt_ser);
	

		}
		else if (res.result == 'error') {
			let error_msgs = res.msg;
			for (let key in error_msgs) {
				if (error_msgs.hasOwnProperty(key)) {
					$('#serviceboqform').find('.' + key + '_error').html(error_msgs[key][0]);
				}
			}

		}
		else if (res.result == 'failure') {

			Swal.fire({
				text: "Something went wrong. Please try again.",
				type: 'error',
				buttonsStyling: false,
				confirmButtonText: "OK",
				confirmButtonClass: "btn font-weight-bold btn-light"
			}).then(function () {
				window.location.reload();
			});
		}

		},
	
	
		error: function (xhr, textStatus, errorThrown,error) {
			console.error("There was a problem with the AJAX operation:", errorThrown);
			if (xhr.responseJSON && xhr.responseJSON.DuplicateError) {
				var errorMessage = xhr.responseJSON.DuplicateError;
				swal({
					title: "Finding Duplicates Numbers Errors!",
					text: errorMessage,
					icon: "error",
					button: "OK",
				}).then(function () {
					location.reload();
				});
			} else {
				swal({
					title: "Error!",
					text: "Something went wrong. Please try again later.",
					icon: "error",
					button: "OK",
				});
			}
		},
		complete: function () {
			$('.pre-loader').hide();
		}

		
	
	});

	

});
fetchDataservicesave();
function fetchDataservicesave() {
	$.ajax({
		url: "/admin/nv_serviceBoq",
		type: "GET",
		success: function (fetchedData) {
			var table = $('#nv_serviceBoq_datatables').DataTable();
			table.clear().draw();
			table.rows.add(fetchedData).draw();
		},
		error: function (xhr, textStatus, errorThrown) {
			console.error("There was a problem fetching the data:", errorThrown);
		}
	});
}




$(document).ready(function () {
	

	$(document).on('click', '.delete_all_services', function () {
		Swal.fire({
			title: 'Are you sure you want to delete all services?',
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes'
		}).then((result) => {
			if (result.value) {
				$('.pre-loader').show();
				var service_id = $('#service_id').val();
				let nv_id = $(this).data('nv-id'); // Retrieve nv_id from data attribute
				let $allmaterial = $(this).data('id'); // Retrieve data-id attribute

				$.ajax({
					data: {
						'nv_id': nv_id, // Include nv_id in the data
						'service_id' :service_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/nv_service/service_delete/all/' + $allmaterial,

					success: function (response) {
						let res = response;

						if (res.result == 'success') {
							// Remove all rows from the DataTable
							$('#nv_serviceBoq_datatables').DataTable().clear().draw(false);
							
							var totalbud = $('#total_buget').val() || 0 ;
							var totservice = $('#total_ser_amo').val() || 0;
							var totalservice = parseInt(totalbud) - parseInt(totservice);
							$('#total_buget').val(totalservice);
							


							Swal.fire({
								text: res.msg,
								type: "success",
								buttonsStyling: false,
								confirmButtonText: "OK",
								confirmButtonClass: "btn font-weight-bold btn-primary"
							}).then(function () {
								$('#total_ser_amo').val(0);
							});
						} else if (res.result == 'failure') {
							Swal.fire({
								text: "Something went wrong. Please try again.",
								type: "error",
								buttonsStyling: false,
								confirmButtonText: "OK",
								confirmButtonClass: "btn font-weight-bold btn-light"
							}).then(function () {
							
							});
						}

						$('.pre-loader').hide();
					},

					error: function (error) {
						// Handle error
					}
				});
			}
		});
	});
});



// document.getElementById('dop_ref_no').addEventListener('keydown', function(event) {
// 	if (event.key === 'Enter') {
// 	  event.preventDefault();
// 	}
//   });
['dop_ref_no','past_3_year_actual_cost1','past_3_year_actual_cost_service1','past_3_year_actual_cost_service2',
'past_3_year_actual_cost_service3','past_3_year_actual_cost2','past_3_year_actual_cost3','service_code_0','service_description'].forEach(function(id) {
	document.getElementById(id).addEventListener('keydown', function(event) {
	  // Check if the pressed key is 'Enter'
	  if (event.key === 'Enter') {
		// Prevent the default form submission behavior
		event.preventDefault();
	  }
	});
  });

  $(document).ready(function()
  {
	 $("#derc_ref_no").keyup(function(){
		 var valu = $("#derc_ref_no").val();
		 if(valu != "")
		 {
			 $(".derc_ref_no_error").html('');
		 }
	 
	 });
 
	 $("#derc_app_date").change(function(){
		 var valu = $("#derc_app_date").val();
		 if(valu != "")
		 {
			 $(".app_date_error").html('');
		 }
	 
	 });
 
  });

  document.getElementById('approved_budget').addEventListener('input', function (e) {
    const value = e.target.value;
    
    const regex = /^\d+(\.\d{0,2})?$/;

    if (!regex.test(value)) {
        e.target.value = value.slice(0, -1); 
    }
});
