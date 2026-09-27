$(document).ready(function () {

	// Datatable
	if ($(document).find('#nv_datatable').length > 0) {
		var nv_id = $('#nv_id').val();
		$('#nv_datatable').DataTable({
			processing: true,
			serverSide: true,
			destroy: true,
			"searching": true,
			dom: 'Bfrtip',
			buttons: [
				{
					extend: 'excel',
					exportOptions: {
						modifier: { page: 'all', search: 'false' },
						columns: ':visible:not(.exclude-export)' // Exclude columns with class "exclude-export"
					},
					customize: function(xlsx) {
						var sheet = xlsx.xl.worksheets['sheet1.xml'];
						$('row c[r^="C"]', sheet).attr('s', '2'); // Style index 2 is hidden
					}
				},
				{ extend: 'csv',   exportOptions: { modifier: { page: 'all', search: 'false' } } },
				
			],
			ajax: {
				data: {
					nv_id: nv_id,
				},
				url: '/admin/nv_materialBoq'
			},
			columns: [
				{
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
					className: "text-center exclude-export",
					exportOptions: {
						visible: true  // This will exclude the column from export
					}
				},
				{
					data: 'material_code',
					name: 'material_code',
					className: "text-center"
				},
				{
					data: 'uom',
					name: 'uom',
					className: "text-center"
				},
				{
					data: 'material_short_text',
					name: 'material_short_text',
					className: "text-center"
				},
				{
					data: 'rate',
					name: 'rate',
					className: "text-center",
					render: function (data, type, full, meta) {
					  var formattedRate = parseFloat(data).toFixed(2); // Convert to float and format to 2 decimal places
					  return formattedRate;
					}
				  },

				  {
					data: 'quantity',
					name: 'quantity',
					className: "text-center",
					render: function (data, type, full, meta) {
					  var formattedquantity = parseFloat(data).toFixed(3); // Convert to float and format to 2 decimal places
					  return formattedquantity;
					}
				  },
				  {
					data: 'amount',
					name: 'amount',
					className: "text-center",
					render: function (data, type, full, meta) {
					  var formattedAmount = parseFloat(data).toFixed(2); // Convert to float and format to 2 decimal places
					  return formattedAmount;
					}
				  },
				{
					data: 'rate_reference',
					name: 'rate_reference',
					className: "text-left",
					render: function (data, type, full, meta) {
						var rateReferenceValue = data;
						if (rateReferenceValue === 0) {
							return 'C&M';
						} else if (rateReferenceValue === 1) {
							return 'Last Purchase Price';
						} else {
							return 'Vendor Quotation';
						}
					}
				},
				{
					data: null,
					name: 'year_combined',
					className: "text-left",
					render: function (data, type, full, meta) {
						// Get the selected year from the dropdown
						var selectedYear = parseInt($("#exampleFormControlSelect").val());
				
						if (isNaN(selectedYear) || selectedYear <= 0) {
							return ''; // Return an empty string if no year is selected
						}
				
						// Get the selected date from the input field
						var selectedDate = $("#imp_from").val();
						var selectedDateYear = selectedDate ? new Date(selectedDate).getFullYear() : null;
				
						if (selectedDateYear === null) {
							return selectedYear.toString(); // Display the selected year if no date is selected
						}
				
						var yearArray = [];
						for (let i = 0; i < selectedYear; i++) {
							var startYear = selectedDateYear + i;
							var endYear = startYear + 1;
							yearArray.push(startYear.toString() + '-' + endYear.toString());
						}
				
						return yearArray.join('<hr>');
					} 
				},
				{
					data: null,
					name: 'april_combined',
					className: "text-center",
					render: function (data, type, full, meta) {
						var selectedYear = parseInt($("#exampleFormControlSelect").val());
				
						const aprilDataArray = [];
						for (let i = 1; i <= selectedYear; i++) {
							// Replace null with 0
							var aprilValue = data['april' + i] !== null ? parseFloat(data['april' + i]).toFixed(2) : '0.00'; // Fix to 2 decimal places
							aprilDataArray.push(aprilValue);
						}
				
						// Replace black with 0 in the joined string
						const joinedData = aprilDataArray.join('<hr>').replace(/black/g, '0');
				
						// Split the joinedData by '<hr>' to create an array of values
						const dataArray = joinedData.split('<hr>');
				
						// Join all values to get the final result
						const result = dataArray.join('<hr>');
				
						return result;
					}
				},											
				{
					data: null,
					name: 'may_combined',
					className: "text-center",
					render: function (data, type, full, meta) {
						var selectedYear = parseInt($("#exampleFormControlSelect").val());
				
						const mayDataArray = [];
						for (let i = 1; i <= selectedYear; i++) {
							// Replace null with 0
							var mayValue = data['may' + i] !== null ? data['may' + i] : '0';
							mayDataArray.push(mayValue);
						}
				
						// Replace black with 0 in the joined string
						return mayDataArray.join('<hr>').replace(/black/g, '0');
					}
				},
				{
					data: null,
					name: 'june_combined',
					className: "text-center",
					render: function (data, type, full, meta) {
						var selectedYear = parseInt($("#exampleFormControlSelect").val());
						
						const juneDataArray = [];
						for (let i = 1; i <= selectedYear; i++) {
							// Replace null with 0
							var juneValue = data['june' + i] !== null ? data['june' + i] : '0';
							juneDataArray.push(juneValue);
						}
						
						// Replace black with 0 in the joined string
						const joinedData = juneDataArray.join('<hr>').replace(/black/g, '0');
						
						// Split the joinedData by '<hr>' to create an array of values
						const dataArray = joinedData.split('<hr>');
						
						// Join all values to get the final result
						const result = dataArray.join('<hr>');
						
						return result;
					}
				},
				{
					data: null,
					name: 'july_combined',
					className: "text-center",
					render: function (data, type, full, meta) {
						var selectedYear = parseInt($("#exampleFormControlSelect").val());
						
						const julyDataArray = [];
						for (let i = 1; i <= selectedYear; i++) {
							// Replace null with 0
							var julyValue = data['july' + i] !== null ? data['july' + i] : '0';
							julyDataArray.push(julyValue);
						}
						
						// Replace black with 0 in the joined string
						const joinedData = julyDataArray.join('<hr>').replace(/black/g, '0');
						
						// Split the joinedData by '<hr>' to create an array of values
						const dataArray = joinedData.split('<hr>');
						
						// Join all values to get the final result
						const result = dataArray.join('<hr>');
						
						return result;
					}
				},
				{
					data: null,
					name: 'august_combined',
					className: "text-center",
					render: function (data, type, full, meta) {
						var selectedYear = parseInt($("#exampleFormControlSelect").val());
						
						const augustDataArray = [];
						for (let i = 1; i <= selectedYear; i++) {
							// Replace null with 0
							var augustValue = data['august' + i] !== null ? data['august' + i] : '0';
							augustDataArray.push(augustValue);
						}
						
						// Replace black with 0 in the joined string
						const joinedData = augustDataArray.join('<hr>').replace(/black/g, '0');
						
						// Split the joinedData by '<hr>' to create an array of values
						const dataArray = joinedData.split('<hr>');
						
						// Join all values to get the final result
						const result = dataArray.join('<hr>');
						
						return result;
					}
				},
				{
					data: null,
					name: 'september_combined',
					className: "text-center",
					render: function (data, type, full, meta) {
						var selectedYear = parseInt($("#exampleFormControlSelect").val());
						
						const septemberDataArray = [];
						for (let i = 1; i <= selectedYear; i++) {
							// Replace null with 0
							var septemberValue = data['september' + i] !== null ? data['september' + i] : '0';
							septemberDataArray.push(septemberValue);
						}
						
						// Replace black with 0 in the joined string
						const joinedData = septemberDataArray.join('<hr>').replace(/black/g, '0');
						
						// Split the joinedData by '<hr>' to create an array of values
						const dataArray = joinedData.split('<hr>');
						
						// Join all values to get the final result
						const result = dataArray.join('<hr>');
						
						return result;
					}
				},
				{
					data: null,
					name: 'oct_combined',
					className: "text-center",
					render: function (data, type, full, meta) {
						var selectedYear = parseInt($("#exampleFormControlSelect").val());
						
						const octDataArray = [];
						for (let i = 1; i <= selectedYear; i++) {
							// Replace null with 0
							var octValue = data['oct' + i] !== null ? data['oct' + i] : '0';
							octDataArray.push(octValue);
						}
						
						// Replace black with 0 in the joined string
						const joinedData = octDataArray.join('<hr>').replace(/black/g, '0');
						
						// Split the joinedData by '<hr>' to create an array of values
						const dataArray = joinedData.split('<hr>');
						
						// Join all values to get the final result
						const result = dataArray.join('<hr>');
						
						return result;
					}
				},
				{
					data: null,
					name: 'nov_combined',
					className: "text-center",
					render: function (data, type, full, meta) {
						var selectedYear = parseInt($("#exampleFormControlSelect").val());
						
						const novDataArray = [];
						for (let i = 1; i <= selectedYear; i++) {
							// Replace null with 0
							var novValue = data['nov' + i] !== null ? data['nov' + i] : '0';
							novDataArray.push(novValue);
						}
						
						// Replace black with 0 in the joined string
						const joinedData = novDataArray.join('<hr>').replace(/black/g, '0');
						
						// Split the joinedData by '<hr>' to create an array of values
						const dataArray = joinedData.split('<hr>');
						
						// Join all values to get the final result
						const result = dataArray.join('<hr>');
						
						return result;
					}
				},
				{
					data: null,
					name: 'dec_combined',
					className: "text-center",
					render: function (data, type, full, meta) {
						var selectedYear = parseInt($("#exampleFormControlSelect").val());
						
						const decDataArray = [];
						for (let i = 1; i <= selectedYear; i++) {
							// Replace null with 0
							var decValue = data['dec' + i] !== null ? data['dec' + i] : '0';
							decDataArray.push(decValue);
						}
						
						// Replace black with 0 in the joined string
						const joinedData = decDataArray.join('<hr>').replace(/black/g, '0');
						
						// Split the joinedData by '<hr>' to create an array of values
						const dataArray = joinedData.split('<hr>');
						
						// Join all values to get the final result
						const result = dataArray.join('<hr>');
						
						return result;
					}
				},
				{
					data: null,
					name: 'jan_combined',
					className: "text-center",
					render: function (data, type, full, meta) {
						var selectedYear = parseInt($("#exampleFormControlSelect").val());
						
						const janDataArray = [];
						for (let i = 1; i <= selectedYear; i++) {
							// Replace null with 0
							var janValue = data['jan' + i] !== null ? data['jan' + i] : '0';
							janDataArray.push(janValue);
						}
						
						// Replace black with 0 in the joined string
						const joinedData = janDataArray.join('<hr>').replace(/black/g, '0');
						
						// Split the joinedData by '<hr>' to create an array of values
						const dataArray = joinedData.split('<hr>');
						
						// Join all values to get the final result
						const result = dataArray.join('<hr>');
						
						return result;
					}
				},
				{
					data: null,
					name: 'feb_combined',
					className: "text-center",
					render: function (data, type, full, meta) {
						var selectedYear = parseInt($("#exampleFormControlSelect").val());
						
						const febDataArray = [];
						for (let i = 1; i <= selectedYear; i++) {
							// Replace null with 0
							var febValue = data['feb' + i] !== null ? data['feb' + i] : '0';
							febDataArray.push(febValue);
						}
						
						// Replace black with 0 in the joined string
						const joinedData = febDataArray.join('<hr>').replace(/black/g, '0');
						
						// Split the joinedData by '<hr>' to create an array of values
						const dataArray = joinedData.split('<hr>');
						
						// Join all values to get the final result
						const result = dataArray.join('<hr>');
						
						return result;
					}
				},
				{
					data: null,
					name: 'march_combined',
					className: "text-center",
					render: function (data, type, full, meta) {
						var selectedYear = parseInt($("#exampleFormControlSelect").val());
						
						const marchDataArray = [];
						for (let i = 1; i <= selectedYear; i++) {
							// Replace null with 0
							var marchValue = data['march' + i] !== null ? data['march' + i] : '0';
							marchDataArray.push(marchValue);
						}
						
						// Replace black with 0 in the joined string
						const joinedData = marchDataArray.join('<hr>').replace(/black/g, '0');
						
						// Split the joinedData by '<hr>' to create an array of values
						const dataArray = joinedData.split('<hr>');
						
						// Join all values to get the final result
						const result = dataArray.join('<hr>');
						
						return result;
					}
				},
				{
					data: 'action',
					name: 'action',
					className: "text-center",
					orderable: false
				},




			],
			"bLengthChange" : false,
			"bInfo":false,
			'columnDefs': [{ 'orderable': false, 'targets': 0 }, { 'visible': false, 'targets': [1], 'orderable': true }],
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
	// Create role
	$('#create_nv_material, #preview_nvmaterial,#serviceboqform').validate({
		rules: {
			dept_id: {
				required: true,
				// maxlength: 50,
				// 
			},
			// dop: {
			// 	required: true,
			// 	maxlength: 50,
			// 	number:true,
			// },
			// budget_avl: {
			// 	required: true,
			// 	maxlength: 100,

			// },
			remark: {
				required: true,

			},
			check_technology: {
				required: true,

			},
			service_code_0: {
				required: true,

			},
			material_code: {
				required: true,

			}

			// proposal_name: {
			// 	required: true,
			// 	maxlength: 1000,
			// },
			// background: {
			// 	required: true,
			// 	maxlength: 2500,
			// },
			// just_of_proposal: {
			// 	required: true,
			// 	maxlength: 2500,
			// },
			// benefit: {
			// 	required: true,
			// 	maxlength: 2500,
			// },
			// implementation_preriod_from: {
			// 	required: true,
			// 	//date : true,
			//    // dateITA : true,
			// },
			// implementation_preriod_to: {
			// 	required: true,
			// 	//date : true,
			//     //dateITA : true,
			// },
			// implementation_plan_year_wise: {
			// 	required: true,
			// 	maxlength: 2500,
			// },
			// type_of_proposal: {
			// 	required: true,
			// },
			// mode_award: {
			// 	required: true,
			// },
			// amc_proposal_sdate: {
			// 	required: true,
			// 	// date : true,
			//     // dateITA : true,
			// },
			// amc_proposal_edate: {
			// 	required: true,
			// 	// date : true,
			//     // dateITA : true,
			// },
			// budget_available: {
			// 	required: true,
			// 	maxlength: 100,

			// },
			// estimate_amount_of_service: {
			// 	required: true,
			// 	maxlength: 100,
			//     number:true,
			// },
			// estimate_amount_of_service_civil: {
			// 	required: true,
			// 	maxlength: 100,
			//     number:true,
			// },
			// estimate_amount_of_rr_chnage: {
			// 	required: true,
			// 	maxlength: 100,
			//     number:true,
			// },
			// estimate_amount_other: {
			// 	required: true,
			// 	maxlength: 100,
			//     number:true,
			// },
			// cost_calculation_for_service: {
			// 	required: true,
			// },
			// total_buget: {
			// 	required: true,
			// 	maxlength: 100,
			//     number:true
			// }
			// copy_of_previous_work: {
			// 	required: true,
			//     uploadFile:true,
			// },
			// copy_of_derc_other: {
			// 	required: true,
			//     uploadFile:true,
			// },
			// consuption_details: {
			// 	required: true,
			//     uploadFile:true,
			// },
			// buget_stmt_for_both: {
			// 	required: true,
			//     uploadFile:true,
			// },
			// photographs_of_product: {
			// 	required: true,
			//     uploadFile:true,
			// },
			// material_procurement: {
			// 	required: true,
			//     uploadFile:true,
			// },
			// vendor_quatation: {
			// 	required: true,
			//     uploadFile:true,
			// },			
		},
		messages: {
			dept_id: {
				required: "Please select department",
			},
			// dop: {
			// 	required: "Please enter dop reference number",
			// 	maxlength: "DOP reference number not be more than 50 characters",
			// 	number: "DOP ref no should be numbers only",

			// },
			// budget_avl: {
			// 	required: "Please enter budget available",
			// },
			remark: {
				required: "Please enter your remark",

			},
			check_technology: {
				required: "Please select technology",

			},
			service_code_0: {
				required: "Please enter service code",

			},
			material_code: {
				required: "Please enter service code",

			},
			// proposal_name: {
			// 	required: "Please enter proposal name",
			// 	maxlength: "Proposal name not be more than 1000 characters",
			// 	alpha: "Please enter valid input",
			// },
			// background: {
			// 	required: "Please enter background",
			// 	maxlength: "Background not be more than 2500 characters",
			// 	alpha: "Please enter valid input",
			// },
			// just_of_proposal: {
			// 	required: "Please Enter Justification Of Proposal",
			// 	maxlength: "Justification of proposal not be more than 2500 characters",
			// 	alpha: "Please enter valid input",
			// },
			// benefit: {
			// 	required: "Please Enter Benefit",
			// 	maxlength: "Benefit not be more than 2500 characters",
			// },
			// implementation_preriod_from: {
			// 	required: "Please select implementation period from",
			// },
			// implementation_preriod_to: {
			// 	required: "Please select implementation period to",
			// },
			// implementation_plan_year_wise: {
			// 	required: "Please Enter Implementation Plan Year Wise",
			//     maxlength: "Benefit not be more than 2500 characters",				
			// },
			// type_of_proposal: {
			// 	required: "Please Select Type Of Proposal",				
			// },
		},
		// errorPlacement: function (error, element) {
		// 	if (element.attr("name") == "logo") {
		// 		// custom error placement
		// 		$(element).closest('.form-group').find('.common-error').html(error.text());
		// 	}
		// 	else {
		// 		// default error placement
		// 		element.after(error);
		// 	}
		// }
	});

	$('#create_nv_material').on('submit', function (e) {
		e.preventDefault();

		var totalAmount = $('#total_mat_mat').val();
		totalAmount = totalAmount.replace(/[^a-zA-Z0-9_ ]/g, "");
		 totalAmount = parseFloat(totalAmount);
         var budgetAvailable = $('#budget_avl').val();
		 budgetAvailable = budgetAvailable.replace(/[^a-zA-Z0-9_ ]/g, "");
		 budgetAvailable = parseFloat(budgetAvailable);
		 var totalserviceAmount = $('#total_ser_amo').val();
		 totalserviceAmount = totalserviceAmount.replace(/[^a-zA-Z0-9_ ]/g, "");
		 totalserviceAmount = parseFloat(totalserviceAmount);
         var serbudgetAvailable = $('#ser_budget_avl').val();
		  serbudgetAvailable = serbudgetAvailable.replace(/[^a-zA-Z0-9_ ]/g, "");
		 serbudgetAvailable = parseFloat(serbudgetAvailable);
		 var budgetBoth = $('#total_budget_both').val();
		 budgetBoth = budgetBoth.replace(/[^a-zA-Z0-9_ ]/g, "");
		 budgetBoth = parseFloat(budgetBoth);
		 var just_Prop = CKEDITOR.instances.just_Prop.getData();
		 var background = CKEDITOR.instances.background.getData();

    $('.error-message').remove();

    if (totalAmount > budgetAvailable) {
		Swal.fire({
			text: "Total Amount should not be greater than Budget Available.",
			type: 'error',
			buttonsStyling: false,
			confirmButtonText: "Ok",
			confirmButtonClass: "btn font-weight-bold btn-light"
		}).then(function() {
			window.location.reload();
		});
        // var errorMessage = "Total Amount should not be greater than Budget Available.";
        // var errorElement = $('<p class="error-message">' + errorMessage + '</p>');
        // $('#budget_avl').after(errorElement);

        // // Scroll to the error message location
        // var scrollTo = errorElement.offset().top - 100; // Adjust the offset as needed
        // $('html, body').animate({
        //     scrollTop: scrollTo
        // }, 500);

        return; // Stop form submission
    }
	if (totalserviceAmount > serbudgetAvailable) {
		Swal.fire({
			text: "Total Amount should not be greater than Budget Available.",
			type: 'error',
			buttonsStyling: false,
			confirmButtonText: "Ok",
			confirmButtonClass: "btn font-weight-bold btn-light"
		}).then(function() {
			window.location.reload();
		});
        // var errorMessage = "Total Amount should not be greater than Budget Available.";
        // var errorElement = $('<p class="error-message">' + errorMessage + '</p>');
        // $('#ser_budget_avl').after(errorElement);

        // // Scroll to the error message location
        // var scrollTo = errorElement.offset().top - 100; // Adjust the offset as needed
        // $('html, body').animate({
        //     scrollTop: scrollTo
        // }, 500);

        return; // Stop form submission
    }
	if (budgetBoth > budgetAvailable) {
        var errorMessage = "Total Budget NV should not be greater than Budget Available.";
        var errorElement = $('<p class="error-message">' + errorMessage + '</p>');
        $('#total_budget_both').after(errorElement);

        // Scroll to the error message location
        var scrollTo = errorElement.offset().top - 100; // Adjust the offset as needed
        $('html, body').animate({
            scrollTop: scrollTo
        }, 500);

        return; // Stop form submission
    }
		$('.common-error').empty();
		if ($('#create_nv_material').valid()) {

			$('.pre-loader').show();
			var status = $('.submit_btn').val();
			var draft = $('#draft2').val();
			var formData = new FormData($('#create_nv_material')[0]);
			formData.append("status", status);
			formData.append("draft", draft);
			formData.append("just_Prop", just_Prop);
			formData.append("background", background);

			$.ajax({
				data: formData,
				cache: false,
				processData: false,
				contentType: false,
				type: 'post',
				url: "/admin/nv_material/store",
				success: function (response) {
					var res = response;
					if (res.result == 'success') {

						Swal.fire({
							text: "NV submitted successfully",
							type: 'success',
							buttonsStyling: false,
							confirmButtonText: "Ok",
							confirmButtonClass: "btn font-weight-bold btn-primary"
						}).then(function () {
							window.location = '/admin/needvalidation/list';
						});

						$('#create_nv_material')[0].reset();
					}
					else if (res.result == 'error') {
						let error_msgs = res.msg;
						for (let key in error_msgs) {
							if (error_msgs.hasOwnProperty(key)) {
								$('#create_nv_material').find('.' + key + '_error').html(error_msgs[key][0]);
							}
						}

					}
					else if (res.result == 'failure') {

						Swal.fire({
							text: "Something went wrong. Please try again.",
							type: 'error',
							buttonsStyling: false,
							confirmButtonText: "Ok",
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

	$('.save_btn').click(function (e) {
		e.preventDefault();

         var totalAmount = parseFloat($('#total_mat_mat').val());
         var budgetAvailable = $('#budget_avl').val();
		 budgetAvailable = budgetAvailable.replace(/[^a-zA-Z0-9_ ]/g, "")
		 budgetAvailable = parseInt(budgetAvailable);
		 var totalserviceAmount = $('#total_ser_amo').val();
		 totalserviceAmount = totalserviceAmount.replace(/[^a-zA-Z0-9_ ]/g, "")
		 totalserviceAmount = parseFloat(totalserviceAmount);
         var serbudgetAvailable = $('#ser_budget_avl').val();
		 serbudgetAvailable = serbudgetAvailable.replace(/[^a-zA-Z0-9_ ]/g, "")
		 serbudgetAvailable = parseFloat(serbudgetAvailable);
		 var budgetBoth = parseFloat($('#total_budget_both').val());
		 var just_Prop = CKEDITOR.instances.just_Prop.getData();
		 var background = CKEDITOR.instances.background.getData();
    $('.error-message').remove();
    if (totalAmount > budgetAvailable) {
		
		Swal.fire({
			text: "Total Amount should not be greater than Budget Available.",
			type: 'error',
			buttonsStyling: false,
			confirmButtonText: "Ok",
			confirmButtonClass: "btn font-weight-bold btn-light"
		}).then(function() {
			window.location.reload();
		});
        // var errorMessage = "Total Amount should not be greater than Budget Available.";
        // var errorElement = $('<p class="error-message">' + errorMessage + '</p>');
        // $('#budget_avl').after(errorElement);

        // // Scroll to the error message location
        // var scrollTo = errorElement.offset().top - 100; // Adjust the offset as needed
        // $('html, body').animate({
        //     scrollTop: scrollTo
        // }, 500);

        return; // Stop form submission
    }
	// alert(totalserviceAmount);
	// alert(serbudgetAvailable);
	// alert(totalserviceAmount > serbudgetAvailable);
	if (totalserviceAmount > serbudgetAvailable) {
		Swal.fire({
			text: "Total Amount should not be greater than Budget Available.",
			type: 'error',
			buttonsStyling: false,
			confirmButtonText: "Ok",
			confirmButtonClass: "btn font-weight-bold btn-light"
		}).then(function() {
			window.location.reload();
		});
        // var errorMessage = "Total Amount should not be greater than Budget Available.";
        // var errorElement = $('<p class="error-message">' + errorMessage + '</p>');
        // $('#ser_budget_avl').after(errorElement);

        // // Scroll to the error message location
        // var scrollTo = errorElement.offset().top - 100; // Adjust the offset as needed
        // $('html, body').animate({
        //     scrollTop: scrollTo
        // }, 500);

        return; // Stop form submission
    }
	if (budgetBoth > budgetAvailable) {
        var errorMessage = "Total Budget NV should not be greater than Budget Available.";
        var errorElement = $('<p class="error-message">' + errorMessage + '</p>');
        $('#total_budget_both').after(errorElement);

        // Scroll to the error message location
        var scrollTo = errorElement.offset().top - 100; // Adjust the offset as needed
        $('html, body').animate({
            scrollTop: scrollTo
        }, 500);

        return; // Stop form submission
    }
		$('.common-error').empty();
		if ($('#create_nv_material').valid()) {

			$('.pre-loader').show();
			var status = $('.save_btn').val();
			var draft = $('#draft1').val();
			var formData = new FormData($('#create_nv_material')[0]);
			formData.append("status", status);
			formData.append("draft", draft);
			formData.append("just_Prop", just_Prop);
			formData.append("background", background);

			$.ajax({
				data: formData,
				cache: false,
				processData: false,
				contentType: false,
				type: 'post',
				url: "/admin/nv_material/store",
				success: function (response) {
					var res = response;
					if (res.result == 'success') {
						var store =res.store;
						console.log(store);
						//  $('#derc_stakeholder_approvals').val(store);

						Swal.fire({
							text: "NV saved successfully",
							type: 'success',
							buttonsStyling: false,
							confirmButtonText: "Ok",
							confirmButtonClass: "btn font-weight-bold btn-primary"
						}).then(function () {
							window.location = '/admin/needvalidation/list';
						});

						$('#create_nv_material')[0].reset();
					}
					else if (res.result == 'error') {
						let error_msgs = res.msg;
						for (let key in error_msgs) {
							if (error_msgs.hasOwnProperty(key)) {
								$('#create_nv_material').find('.' + key + '_error').html(error_msgs[key][0]);
							}
						}

					}
					else if (res.result == 'failure') {

						Swal.fire({
							text: "Something went wrong. Please try again.",
							type: 'error',
							buttonsStyling: false,
							confirmButtonText: "Ok",
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

	// Edit role
	$('#edit_employee_form').on('submit', function (e) {
		e.preventDefault();
		$('.common-error').empty();

		if ($('#edit_employee_form').valid()) {

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_employee_form')[0]),
				type: 'post',
				url: "/admin/employees/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function (response) {
					let res = response;

					if (res.result == 'success') {

						Swal.fire({
							text: "Employee updated.",
							type: 'success',
							buttonsStyling: false,
							confirmButtonText: "Ok",
							confirmButtonClass: "btn font-weight-bold btn-primary"
						}).then(function () {
							window.location = '/admin/employees';
						});

						$('#edit_employee_form')[0].reset();
					}
					else if (res.result == 'error') {
						let error_msgs = res.msg;
						for (let key in error_msgs) {
							if (error_msgs.hasOwnProperty(key)) {
								$('#edit_employee_form').find('.' + key + '_error').html(error_msgs[key][0]);
							}
						}

					}
					else if (res.result == 'failure') {

						Swal.fire({
							text: "Something went wrong. Please try again.",
							type: 'error',
							buttonsStyling: false,
							confirmButtonText: "Ok",
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

	$(document).ready(function () {
	$(document).on('click', '.delete_material', function () {

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

				let floor_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'floor_id': floor_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/nv_material/delete/'+floor_id,

					success: function (response) {
						let res = response;

						if (res.result == 'success') {

							Swal.fire({
								text: res.msg,
								type: "success",
								buttonsStyling: false,
								confirmButtonText: "Ok",
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
								confirmButtonText: "Ok",
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
// New code to handle the "Delete All" button
$(document).on('click', '.delete_all_materials', function () {
    Swal.fire({
        title: 'Are you sure you want to delete all materials?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.value) {
            $('.pre-loader').show();

            let nv_id = $(this).data('nv-id'); // Retrieve nv_id from data attribute
            let $allmaterial = $(this).data('id'); // Retrieve data-id attribute

            $.ajax({
                data: {
                    'nv_id': nv_id, // Include nv_id in the data
                    '_token': $('input[name="_token"]').val()
                },
                type: 'DELETE',
                url: '/admin/nv_material/delete/all/' + $allmaterial,

                success: function (response) {
                    let res = response;

                    if (res.result == 'success') {
                        Swal.fire({
                            text: res.msg,
                            type: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            confirmButtonClass: "btn font-weight-bold btn-primary"
                        }).then(function () {
                            window.location.reload();
                        });
                    } else if (res.result == 'failure') {
                        Swal.fire({
                            text: "Something went wrong. Please try again.",
                            type: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
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
    });
});
});

$(document).ready(function () {
	$(document).on('click', '.delete_service', function () {

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

				let service_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'service_id': service_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/nv_material/service_delete/'+service_id,

					success: function (response) {
						let res = response;

						if (res.result == 'success') {

							Swal.fire({
								text: res.msg,
								type: "success",
								buttonsStyling: false,
								confirmButtonText: "Ok",
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
								confirmButtonText: "Ok",
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
	// New code to handle the "Delete All" button
$(document).on('click', '.delete_all_service', function () {
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

            let nv_id = $(this).data('nv-id'); // Retrieve nv_id from data attribute
            let $allmaterial = $(this).data('id'); // Retrieve data-id attribute

            $.ajax({
                data: {
                    'nv_id': nv_id, // Include nv_id in the data
                    '_token': $('input[name="_token"]').val()
                },
                type: 'DELETE',
                url: '/admin/nv_material/service_delete/all/' + $allmaterial,

                success: function (response) {
                    let res = response;

                    if (res.result == 'success') {
                        Swal.fire({
                            text: res.msg,
                            type: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            confirmButtonClass: "btn font-weight-bold btn-primary"
                        }).then(function () {
                            window.location.reload();
                        });
                    } else if (res.result == 'failure') {
                        Swal.fire({
                            text: "Something went wrong. Please try again.",
                            type: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
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
    });
});
})



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
						confirmButtonText: "Ok",
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
	if ($(document).find('#nv_serviceBoq_datatable').length > 0) {
		var nv_id = $('#nv_id').val();
		// alert(nv_id);
		$('#nv_serviceBoq_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			destroy: true,
			"searching": true,
			ajax: {
				data: {
					nv_id: nv_id,
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
	}
});

// $(document).ready(function() {
//     $("body").on("click",".add-more",function(){ 
//         var html = $(".after-add-more").first().clone();
//           $(html).find(".change").html("<label for=''>&nbsp;</label><a class='btn btn-danger remove'>- Remove</a>");
//         $(".after-add-more").last().after(html);
//     });

//     $("body").on("click",".remove",function(){ 
//         $(this).parents(".after-add-more").remove();
//     });
// });

// Add More Third
//   $(document).ready(function () {
// 	$("body").on("click", ".add-more-third", function () {
// 	  var html = $(".after-add-more-third").first().clone();
// 	  $(html)
// 		.find(".change")
// 		.html(
// 		  "<label for=''></label> <a class='btn btn-success  remove'>-  Remove </a>"
// 		);
// 	  $(".after-add-more-third").last().after(html);
// 	});

// 	$("body").on("click", ".remove", function () {
// 	  $(this).parents(".after-add-more-third").remove();
// 	});
//   });

$(document).ready(function () {
	var max_fields = 10; //maximum input boxes allowed
	var wrapper = $(".my-class-scheme"); //Fields wrapper
	var add_button = $(".add-more-scheme"); //Add button ID

	var x = 1; //initlal text box count
	$(add_button).click(function (e) { //on add input button click
		e.preventDefault();
		if (x < max_fields) { //max input box allowed
			x++; //text box increment
			$(wrapper).append('<div class=""> <div class="row"><div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2"><div class="form-group"> <label for="exampleFormControlInput1">Scheme No</label> <input type="text" class="form-control" id="scheme_no'+x+'" name="scheme_no[]" onkeyup="storeapi(scheme_no'+x+',scheme_des'+x+')" value="" placeholder="Enter Scheme No""></div></div><div class="col-12  mb-2"><div class="form-group"><label for="exampleFormControlInput1">Scheme Description</label><textarea name="scheme_des[]" id="scheme_des'+x+'"  cols="2" rows="10" class="form-control" value="" placeholder="Enter Scheme Description"></textarea></div></div><label for="">&nbsp;</label><a class=" btn btn-success mt-2 remove">- Remove</a></div></div>'); //add input box
		}
	});

	$(wrapper).on("click", ".remove", function (e) { //user click on remove text
		e.preventDefault(); $(this).parent('div').remove(); x--;
	})
});


// $(document).ready(function () {
// 	var max_fields = 3; //maximum input boxes allowed
// 	var wrapper = $(".my-class"); //Fields wrapper
// 	var add_button = $(".add-more-third"); //Add button ID

// 	var x = 1; //initlal text box count
// 	$(add_button).click(function (e) { //on add input button click
// 		e.preventDefault();
// 		if (x < max_fields) { //max input box allowed
// 			x++; //text box increment
// 			$(wrapper).append('<div class=""> <div class="row"><div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2"><div class="form-group"> <label for="exampleFormControlInput1">Implementation Period From</label> <input type="date" class="form-control" id="imp_from" name="imp_from[]" value="" placeholder="Enter Benefit"></div></div><div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2"><div class="form-group"><label for="exampleFormControlInput1">Implementation Period To</label><input type="date" class="form-control" id="imp_to" name="imp_to[]" value="" placeholder="Enter Benefit"></div></div><div class="col-12  mb-2"><div class="form-group"><label for="exampleFormControlInput1">Implementation Plan Year Wise(Max 2500 Characters)</label><textarea name="imp_plan[]" id="imp_plan" cols="2" rows="10" class="form-control" value="" placeholder=" Enter Implementation Plan Year Wise"></textarea></div></div><label for="">&nbsp;</label><a class=" btn btn-success mt-2 remove">- Remove</a></div></div>'); //add input box
// 		}
// 	});

// 	$(wrapper).on("click", ".remove", function (e) { //user click on remove text
// 		e.preventDefault(); $(this).parent('div').remove(); x--;
// 	})
// });

$(document).ready(function () {
	var max_fields = 3; //maximum input boxes allowed
	var wrapper = $(".my-fields"); //Fields wrapper
	var add_button = $(".add-more"); //Add button ID

	var x = 1; //initlal text box count
	$(add_button).click(function (e) { //on add input button click
		e.preventDefault();
		if (x < max_fields) { //max input box allowed
			x++; //text box increment
			$(wrapper).append('<div class=""> <div class="row "><div class="col-xl-3 col-lg-3 col-md-6 mt-2"><input type="text" class="form-control"name="cost_trend_year3[]"id="cost_trend_year3"value=""placeholder="FY"></div><div class="col-xl-3 col-lg-3 col-md-6 mt-2"><input type="text" class="form-control"name="cost_trend_year1[]"id="cost_trend_year1"placeholder="Cost"value=""></div><div class="col-xl-3 col-lg-3 col-md-6 mt-2"><input type="text" class="form-control"name="cost_trend_year2[]"id="cost_trend_year2"value=""placeholder="Material"></div><label for="">&nbsp;</label><a class=" btn btn-success mt-2 remove">- Remove</a></div></div>'); //add input box
		}
	});

	$(wrapper).on("click", ".remove", function (e) { //user click on remove text
		e.preventDefault(); $(this).parent('div').remove(); x--;
	})
});



$(document).ready(function () {
	var max_fields = 10; //maximum input boxes allowed
	var wrapper = $(".my-fieldssss"); //Fields wrapper
	var add_button = $(".add-more-second"); //Add button ID

	var x = 1; //initlal text box count
	var count = 1;

	$(add_button).click(function (e) { //on add input button click
		e.preventDefault();
		if (x < max_fields) { //max input box allowed
			x++; //text box increment
			$(wrapper).append("<div class='container-fluid'> <div class='row'><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> Material Code </label><input type='text' class='form-control'name='material_code[]_" + count + "' id='material_code_" + count + "'value='' placeholder='Enter Material Code'></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> Material Description </label><input type='text' class='form-control'name='mat_des[]_" + count + "'id='mat_des_" + count + "'placeholder='Enter Material Description'value=''></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> Material Group </label><select class='form-control' id='mat_group' name='mat_group[]'><option value=''>Select Material Group</option><option value='DT'>DT</option><option value='HT Cable'>HT Cable</option> <option value='LT Cable'>LT Cable</option><option value='RMU'>RMU</option><option value='LT ACB'>LT ACB</option><option value='Pole'> Pole</option><option value='Joint Kit'> Joint Kit</option><option value='Feeder Pillar'> Feeder Pillar</option><option value='Miscellaneous' >Miscellaneous</option></select></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> UoM </label><input type='text' class='form-control'name='uom[]_" + count + "'id='uom_" + count + "'value=''placeholder='Enter Uom''></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> Rate </label><input type='text' class='form-control'name='rate[]'id='rate'value=''placeholder='Enter Rate''></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> Quantity Required </label><input type='text' class='form-control'name='quantity[]'id='quantity'value=''placeholder='Enter Quantity Required''></div><div class='col-6 mb-2 mt-3'><label  >Total Amount</label><input type='text' class='form-control'name='total_amount[]'id='total_amount'value=''placeholder='Enter Total Amount'></div><div class='col-6 mb-2 mt-3'><label  >Delivery Schedule</label><input type='text' class='form-control'name='delivery_schedule[]'id='delivery_schedule'value=''placeholder='Enter Delivery Schedule'></div><br><label for=''>&nbsp;</label><a class=' btn btn-success mt-3 remove'>- Remove</a></div></div><br>");

			// }
			$('#material_code_' + count).keyup(function () {
				var token = $('#tokennm').val();
				//var mtcode= "#material_code_"+count;

				var cnt = count;
				var cnt = cnt - 1;

				if (cnt == '1') {
					var material_code = $('#material_code_1').val();
					console.log(material_code);
				}

				if (cnt == '2') {
					var material_code = $('#material_code_2').val();
					console.log(cnt);
				}
				if (cnt == '3') {
					var material_code = $('#material_code_3').val();
					console.log(cnt);
				}
				if (cnt == '4') {
					var material_code = $('#material_code_4').val();
					console.log(cnt);
				}
				if (cnt == '5') {
					var material_code = $('#material_code_5').val();
					console.log(cnt);
				}
				if (cnt == '6') {
					var material_code = $('#material_code_6').val();
					console.log(cnt);
				}
				if (cnt == '7') {
					var material_code = $('#material_code_7').val();
					console.log(cnt);
				}
				if (cnt == '8') {
					var material_code = $('#material_code_8').val();
					console.log(cnt);
				}
				if (cnt == '9') {
					var material_code = $('#material_code_9').val();
					console.log(cnt);
				}
				if (cnt == '10') {
					var material_code = $('#material_code_10').val();
					console.log(cnt);
				}



				e.preventDefault();
				if (material_code.length >= 6) {

					$.ajax({
						url: "/admin/capexmaster/autofetch",
						type: 'post',
						dataType: "json",
						data: {
							'_token': token,
							'key': material_code,
						},
						success: function (data) {

							$('#mat_des_' + cnt).val(data.material_short_text);
							$('#uom_' + cnt).val(data.uom);
						}

					});


				}

			});
			count++;
		}

	});


	$(wrapper).on("click", ".remove", function (e) { //user click on remove text
		e.preventDefault(); $(this).parent('div').remove(); x--;
	})



	var material_id = window.location.pathname.split('/')[4];
	var nv_id = $('#nv_id').val();
	var material_id = $('#material_id').val();
	// alert(material_id);
	// var remarkValue = $('textarea[name="remark"]').val();

	$('.approve-button').click(function () {
		var status_id = $('.approve-button').val();
		var remark = $('textarea[name="remark"]').val();
		var check_technology = $('input[name="check_technology"]:checked').val();
		var derc_info = $('input[name="derc_info"]').val();
		// alert(derc_info);

		if ($('#preview_nvmaterial').valid()) {
			$('.pre-loader').show();

			$.ajax({
				data: {
					status_id: status_id,
					material_id: material_id,
					nv_id: nv_id,
					remark: remark,
					check_technology: check_technology,
					derc_info: derc_info,
					_token: $('input[name="_token"]').val()
				},
				type: 'get',
				url: '/admin/nv_material/approvedByStatus',
				async: false,
				success: function (response) {
					// alert(response.result);
					if (response.result == 'success') {
						Swal.fire({
							text: "Your NV has been approved successfully ",
							type: 'success',
							buttonsStyling: false,
							confirmButtonText: "Ok",
							confirmButtonClass: "btn font-weight-bold btn-primary"
						}).then(function () {
							window.location.reload();
							// window.location = '/admin/nv_material/preview/' + response.material_id;
						});
						$('#preview_nvmaterial')[0].reset();
					}

					else if (response.result == 'error') {
						let error_msgs = response.msg;
						for (let key in error_msgs) {
							if (error_msgs.hasOwnProperty(key)) {
								$('#preview_nvmaterial').find('.' + key + '_error').html(error_msgs[key][0]);
							}
						}


					}
					else if (response.result == 'failure') {
						Swal.fire({
							text: "Something went wrong. Please try again.",
							type: 'error',
							buttonsStyling: false,
							confirmButtonText: "Ok",
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

	$('.reject-button').click(function () {
		var status_id = $('.reject-button').val();
		var remark = $('textarea[name="remark"]').val();
		// alert(status_id);
		// return false;
		if ($('#preview_nvmaterial').valid()) {
			$('.pre-loader').show();
			$.ajax({
				data: {
					status_id: status_id,
					material_id: material_id,
					nv_id: nv_id,
					remark: remark,
					_token: $('input[name="_token"]').val()
				},
				type: 'get',
				url: '/admin/nv_material/approvedByStatus',
				async: false,
				success: function (response) {
					// alert(response.result);
					if (response.result == 'success') {
						Swal.fire({
							text: "Your NV has been rejected successfully ",
							type: 'success',
							buttonsStyling: false,
							confirmButtonText: "Ok",
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
		}
	});


	$('.save-button').click(function () {
		var dop_ref_no = $('#dop_ref_no').val();
		var status_id = $('.save-button').val();
		var editor1 = CKEDITOR.instances.editor1.getData();
		// var decodedData = decodeURIComponent(editor1);
		// alert(decodedData);
		var users_name = $('#clar_fy').val();

		// return false;
		// if($('#preview_nvservice').valid()){
		$('.pre-loader').show();
		$.ajax({
			data: {
				status_id: status_id,
				material_id: material_id,
				nv_id: nv_id,
				dop_ref_no: dop_ref_no,
				users_name: users_name,
				editor1: editor1,
				_token: $('input[name="_token"]').val()
			},
			type: 'get',
			url: '/admin/nv_material/approvedByStatus',
			async: false,
			success: function (response) {
				// alert(response.result);
				if (response.result == 'success') {
					Swal.fire({
						text: "Your NV has been saved successfully ",
						type: 'success',
						buttonsStyling: false,
						confirmButtonText: "Ok",
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


$(document).ready(function () {
	var max_fields = 10; //maximum input boxes allowed
	var wrapper = $(".my-field-four"); //Fields wrapper
	var add_button = $(".add-more-four"); //Add button ID

	var x = 1; //initlal text box count
	var count = 1;
	$(add_button).click(function (e) { //on add input button click
		e.preventDefault();
		if (x < max_fields) { //max input box allowed
			x++; //text box increment
			$(wrapper).append(" <div class=''> <div class='row '><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> Service Code </label><input type='text' class='form-control'name='service_code[]'id='service_code_" + count + "'value=''placeholder='Enter Service Code'></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> Service Description </label><input type='text' class='form-control'name='ser_des[]'id='ser_des_" + count + "'placeholder='Enter Service Description'value=''></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> Rate Reference </label><input type='text' class='form-control'name='ser_rate_ref[]'id='ser_rate_ref'value=''placeholder='Enter Rate Reference''></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'>UoM</label><input type='text' class='form-control'name='ser_uom[]'id='ser_uom_" + count + "'value=''placeholder='Enter UoM''></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'>Rate</label><input type='text' class='form-control'name='ser_rate[]'id='ser_rate'value=''placeholder='Enter Rate''></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'>Quantity Required</label><input type='text' class='form-control'name='ser_quantity[]'id='ser_quantity'value=''placeholder='Enter Quantity Required''></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'>Total Amount</label><input type='text' class='form-control'name='ser_total_amount[]'id='ser_total_amount'value=''placeholder='Enter Total Amount'></div><div class='col-6 mb-2 mt-3'></div><br><label for=''>&nbsp;</label><a class=' btn btn-success mt-3 remove'>- Remove</a></div></div>"); //add input box

			$('#service_code_' + count).keyup(function () {
				var token = $('#tokennm').val();
				//var mtcode= "#material_code_"+count;

				var cnt = count;
				var cnt = cnt - 1;

				if (cnt == '1') {
					var service_code = $('#service_code_1').val();
					console.log(service_code);
				}

				if (cnt == '2') {
					var service_code = $('#service_code_2').val();
					console.log(cnt);
				}
				if (cnt == '3') {
					var service_code = $('#service_code_3').val();
					console.log(cnt);
				}
				if (cnt == '4') {
					var service_code = $('#service_code_4').val();
					console.log(cnt);
				}
				if (cnt == '5') {
					var service_code = $('#service_code_5').val();
					console.log(cnt);
				}
				if (cnt == '6') {
					var service_code = $('#service_code_6').val();
					console.log(cnt);
				}
				if (cnt == '7') {
					var service_code = $('#service_code_7').val();
					console.log(cnt);
				}
				if (cnt == '8') {
					var service_code = $('#service_code_8').val();
					console.log(cnt);
				}
				if (cnt == '9') {
					var service_code = $('#service_code_9').val();
					console.log(cnt);
				}
				if (cnt == '10') {
					var service_code = $('#service_code_10').val();
					console.log(cnt);
				}



				e.preventDefault();
				if (service_code.length >= 6) {

					$.ajax({
						url: "/admin/capexmaster/autofetchservice",
						type: 'post',
						dataType: "json",
						data: {
							'_token': token,
							'key': service_code,
						},
						success: function (data) {

							$('#ser_des_' + cnt).val(data.service_short_text);
							$('#ser_uom_' + cnt).val(data.bun);
						}

					});


				}

			});
			count++;
		}

	});

	$(wrapper).on("click", ".remove", function (e) { //user click on remove text
		e.preventDefault(); $(this).parent('div').remove(); x--;
	})

});
$(document).ready(function () {
	$.ajaxSetup({
		headers: {
			"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
		},
	});


	fetchData();
	function fetchData() {
		$.ajax({
			url: "/admin/nv_materialBoq",
			type: "GET",
			success: function (fetchedData) {
				var table = $('#nv_datatable').DataTable();
				table.clear().draw();
				table.rows.add(fetchedData).draw();
			},
			error: function (xhr, textStatus, errorThrown) {
				console.error("There was a problem fetching the data:", errorThrown);
			}
		});
	}

	$(document).on("click", "#materialBoqUpload", function () {
		var totalAmount = $('#total_mat_mat').val();
		totalAmount = totalAmount.replace(/[^a-zA-Z0-9_ ]/g, "");
				totalAmount = parseFloat(totalAmount);
		var budgetAvailable = $('#budget_avl').val();
		budgetAvailable = budgetAvailable.replace(/[^a-zA-Z0-9_ ]/g, "");
				budgetAvailable = parseFloat(budgetAvailable);
	
		$('.error-message').remove();
		if (totalAmount > budgetAvailable) {
			var errorMessage = "Total Amount is greater than Budget Available. Please adjust the values.";
			var errorElement = $('<p class="error-message">' + errorMessage + '</p>');
			$('#budget_avl').after(errorElement);
	
		   
			var scrollTo = errorElement.offset().top - 100; 
			$('html, body').animate({
				scrollTop: scrollTo
			}, 500);
	
			return; 
		}

		var fileInput = $("#materialboq")[0].files[0];
		var nv_id = $('#nv_id').val();

		if (!fileInput) {
			swal({
				title: "Error!",
				text: "Please select a file to upload.",
				icon: "error",
				button: "OK",
			});
			return;
		}
		var ttlamt= $('#total_mat_mat').val();
		// ttlamt= parseInt(ttlamt)+parseInt(total_amount);
		
		$('#total_mat_mat').val(ttlamt);
		var formData = new FormData();
		formData.append("materialboq", fileInput);
		formData.append("nv_id", nv_id);

		$.ajax({
			url: "/admin/nv_material/bulkProviderStore",
			type: "POST",
			data: formData,
			dataType: "json",
			processData: false,
			contentType: false,
			success: function (response) {
				// console.log(response.data);
				var data=  response.data;
				// var amount = data['rows'][0]['amount'];
					var rows = data.rows; 
					var totalAmount = 0; 
					for (var i = 0; i < rows.length; i++) {
						var amount = rows[i].amount; 
						totalAmount += parseFloat(amount); 
				        }
				swal({
					title: "Success!",
					text: "Your file has been imported.",
					icon: "success",
					button: "OK",
				}).then(function () {

					fetchData();

				});
				$('#materialboq').val('');
			
				var total = parseInt(ttlamt)+parseInt(totalAmount);
				var total1 = $('#total_ser_amo').val();
				total1 = total1.replace(/[^a-zA-Z0-9_ ]/g, "");
				total1 = parseInt(total1);
				var totalBudget = $('#budget_avl').val();
				// var total_nv = $('#total_budget_both').val();
				
				totalBudget = totalBudget.replace(/[^a-zA-Z0-9_ ]/g, "");
				var available_budget = totalBudget - total;
				total = total.toLocaleString('en-IN');
				available_budget = available_budget.toLocaleString('en-IN');
			// alert(total_nv);
				if(total1 != 0)
				{
					total = total + total1;
					$('#total_budget_both').val(total);
				}
				else{
				$('#total_mat_mat').val(total);
				$('#ser_budget_avl').val(available_budget);
				$('#total_budget_both').val(total);
				// alert(totalBudget);
				}
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
					})
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
				var table = $('#nv_serviceBoq_datatable').DataTable();
				table.clear().draw();
				table.rows.add(fetchedData).draw();
			},
			error: function (xhr, textStatus, errorThrown) {
				console.error("There was a problem fetching the data:", errorThrown);
			}
		});
	}

	$(document).on("click", "#serviceBoqUpload", function () {
		var fileInput = $("#serviceboq")[0].files[0];
		var nv_id = $('#nv_id').val();

		if (!fileInput) {
			swal({
				title: "Error!",
				text: "Please select a file to upload.",
				icon: "error",
				button: "OK",
			});
			return;
		}
		var ttlamt_ser= $('#total_ser_amo').val();
		ttlamt_ser = ttlamt_ser.toLocaleString('en-IN');
		// ttlamt_ser= parseInt(ttlamt_ser);
		
		$('#total_ser_amo').val(ttlamt_ser);
		var formData = new FormData();
		formData.append("serviceboq", fileInput);
		formData.append("nv_id", nv_id);

		$.ajax({
			url: "/admin/nv_material/bulkServiceProviderStore",
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
					var totalAmount = 0; 
					for (var i = 0; i < rows.length; i++) {
						var amount = rows[i].amount; 
						totalAmount += parseFloat(amount); 
					
					}
				swal({
					title: "Success!",
					text: "Your file has been imported.",
					icon: "success",
					button: "OK",
				}).then(function () {
					fetchDataservice();
				});
			    $('#serviceboq').val('');
				var total = parseInt(ttlamt_ser)+parseInt(totalAmount);
				
				// alert(total);
				
				var total_nv = $('#total_mat_mat').val();
				// alert(total_nv);
				total_nv = total_nv.replace(/[^a-zA-Z0-9_ ]/g, "");
				var total_mat = parseInt(total_nv);
				var available_budget = total_mat + total;
				// alert(available_budget);
				total = total.toLocaleString('en-IN');
				available_budget = available_budget.toLocaleString('en-IN');
				$('#total_ser_amo').val(total);
				$('#total_budget_service').val(total);
				$('#total_budget_both').val(available_budget);
				
				// $('#total_ser_amo').val(parseInt(ttlamt_ser)+parseInt(totalAmount));
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
});

fetchDatamaterialsave();
function fetchDatamaterialsave() {
	$.ajax({
		url: "/admin/nv_materialBoq",
		type: "GET",
		success: function (fetchedData) {
			var table = $('#nv_datatable').DataTable();
			table.clear().draw();
			table.rows.add(fetchedData).draw();
		},
		error: function (xhr, textStatus, errorThrown) {
			console.error("There was a problem fetching the data:", errorThrown);
		}
	});
}
$(document).on("click", "#materialBoqsave", function () {
	// var totalAmount = parseFloat($('#total_mat_mat').val());
    var budgetAvailable = parseFloat($('#budget_avl').val());

   
    $('.error-message').remove();

    if (ttlamt > budgetAvailable) {
        var errorMessage = "Total Amount is greater than Budget Available. Please adjust the values.";
        var errorElement = $('<p class="error-message">' + errorMessage + '</p>');
        $('#budget_avl').after(errorElement);

       
        var scrollTo = errorElement.offset().top - 100; 
        $('html, body').animate({
            scrollTop: scrollTo
        }, 500);

        return; 
    }
	
	

	var nv_id = $('#nv_id').val();
	var material_code = $('#material_code_0').val();
	var mat_des = $('#mat_des_0').val();
	var uom_0 = $('#uom_0').val();
	var rate = $('#rate').val();
	var quantity = $('#quantity').val();
	var total_amount = $('#total_amount').val();
	var budget_avl = $('#budget_avl').val();
	


    var ttlamt= $('#total_mat_mat').val();
	ttlamt= parseInt(ttlamt)+parseInt(total_amount);
	ttlamt = ttlamt.toLocaleString('en-IN');
	// alert(ttlamt);
	
	$('#total_mat_mat').val(ttlamt);

	var formData = new FormData();
	//  formData.append("material_boq_save", fileInput);
	formData.append("nv_id", nv_id);
	formData.append("material_code", material_code);
	formData.append("mat_des", mat_des);
	formData.append("uom_0", uom_0);
	formData.append("rate", rate);
	formData.append("quantity", quantity);
	formData.append("total_amount", total_amount);


	$.ajax({
		url: "/admin/nv_material/store_material_boq",
		type: "POST",
		data: formData,
		dataType: "json",
		processData: false,
		contentType: false,
		success: function (data) {
			var res = data;
			if (res.result == 'success') {
			swal({
				title: "Success!",
				text: "Your Material BOQ is Successfully Saved.",
				icon: "success",
				button: "OK",
			}).then(function () {
			
				fetchDatamaterialsave();
			});
				//$('#materialboqform')[0].reset();
				$('#material_code_0').val('');
				$('#rate').val('');
				$('#quantity').val('');
				$('#total_mat_mat').val(ttlamt);
				var budget = parseInt(budget_avl)- parseInt(ttlamt);
				budget = budget.toLocaleString('en-IN');
				
				$('#ser_budget_avl').val(budget);
		}
			
		
			else if (res.result == 'error') {
				let error_msgs = res.msg;
				for (let key in error_msgs) {
					if (error_msgs.hasOwnProperty(key)) {
						$('#material_code').find('.' + key + '_error').html(error_msgs[key][0]);
					}
				}
	
			}
			else if (res.result == 'failure') {
	
				Swal.fire({
					text: "Something went wrong. Please try again.",
					type: 'error',
					buttonsStyling: false,
					confirmButtonText: "Ok",
					confirmButtonClass: "btn font-weight-bold btn-light"
				}).then(function () {
					window.location.reload();
				});
			}
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

fetchDataservicesave();
function fetchDataservicesave() {
	$.ajax({
		url: "/admin/nv_serviceBoq",
		type: "GET",
		success: function (fetchedData) {
			var table = $('#nv_serviceBoq_datatable').DataTable();
			table.clear().draw();
			table.rows.add(fetchedData).draw();
		},
		error: function (xhr, textStatus, errorThrown) {
			console.error("There was a problem fetching the data:", errorThrown);
		}
	});
}
$(document).on("click", "#serviceBoqsave", function () {

	//  $('.save_service_button').click(function(){

	var nv_id = $('#nv_id').val();
	var service_code_0 = $('#service_code_0').val();
	var ser_des_0 = $('#ser_des_0').val();
	var ser_uom_0 = $('#ser_uom_0').val();
	var ser_rate = $('#ser_rate').val();
	var ser_quantity = $('#ser_quantity').val();
	var ser_total_amount = $('#ser_total_amount').val();

	var ttlamt_ser= $('#total_ser_amo').val();
	ttlamt_ser= parseInt(ttlamt_ser)+parseInt(ser_total_amount);
	ttlamt_ser = ttlamt_ser.toLocaleString('en-IN');
	console.log(ttlamt_ser);
	$('#total_ser_amo').val(ttlamt_ser);

	//    alert(nv_id);total_ser_amo

	var formData = new FormData();
	formData.append("nv_id", nv_id);
	formData.append("service_code_0", service_code_0);
	formData.append("ser_des_0", ser_des_0);
	formData.append("ser_uom_0", ser_uom_0);
	formData.append("ser_rate", ser_rate);
	formData.append("ser_quantity", ser_quantity);
	formData.append("ser_total_amount", ser_total_amount);
	if ($('#serviceboqform').valid()) {

	$('.pre-loader').show();
	$.ajax({
		data: formData,
		type: 'POST',
		url: '/admin/nv_material/serviceBoqStore',
		async: false,
		dataType: "json",
		processData: false,
		contentType: false,
		success: function (data) {
			var res = data;
		console.log(data);
			if (res.result == 'success') {
				Swal({
					title: "Success!",
					text: "Your Service BOQ is Successfully Saved.",
					icon: "success",
					button: "OK",
			}).then(function () {
				fetchDataservicesave();
			});
			$('#serviceboqform')[0].reset();
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
				confirmButtonText: "Ok",
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
}

});
function edit_mat(id) {
	console.log(id);
	var selectedYear = parseInt($("#exampleFormControlSelect").val());
				
	if (isNaN(selectedYear) || selectedYear <= 0) {
		return ''; // Return an empty string if no year is selected
	}

	// Get the selected date from the input field
	var selectedDate = $("#imp_from").val();
	var selectedDateYear = selectedDate ? new Date(selectedDate).getFullYear() : null;

	if (selectedDateYear === null) {
		return selectedYear.toString(); // Display the selected year if no date is selected
	}

	var yearStr = '';
	var str1 = "";var str2 = ""; var str3 = "";
	
	if(selectedYear == 3) {
		str1 = selectedDateYear;
		str2 = selectedDateYear + 1;
		str3 = selectedDateYear + 2; 
		yearStr= str1+'-'+str2+'-'+str3;
	}
	if(selectedYear == 2) {
		str1 = selectedDateYear;
		str2 = selectedDateYear + 1;
		yearStr= str1+'-'+str2;
		
	}
	if(selectedYear == 1) {
		str1 = selectedDateYear;
		yearStr= str1;
	}
	
	// for (let i = 0; i < selectedYear; i++) {
	// 	var startYear = selectedDateYear + i;
	// 	var endYear = startYear + 1;
	// 	if(i == cnt) {
			
	// 		yearStr+= startYear;
	// 	}else {
	// 		yearStr+= startYear +'-';
	// 	}
		
	// }

	window.location = '/admin/nv_material/edit_material/'+id+'/'+yearStr;
}
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
											<input type="text" class="form-control" id="service_amount'+x+'" name="service_amount[]" placeholder="Amount" onchange="store_data(service_amount'+x+',service_description'+x+')">\
										</div>\
									</div>\
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">\
										<div class="form-group">\
											<input type="text" class="form-control"id="service_description'+x+'" name="service_description[]"placeholder="Descripition">\
										</div>\
									</div>\
									<label for="">&nbsp;</label><a class=" btn btn-success mt-2 remove">- Remove</a>\
								</div>\
							</div>');
			
		}
	});

	$(wrapper).on("click", ".remove", function (e) { //user click on remove text
		e.preventDefault(); $(this).parent('div').remove(); x--;
	})
});


$(document).ready(function () {
	var max_fields = 10; //maximum input boxes allowed
	var wrapper = $(".my-class-scheme_material"); //Fields wrapper
	var add_button = $(".add-more-button_material"); 
	

	var x = 1; //initlal text box count
	$(add_button).click(function (e) {
		//  alert(1);
		e.preventDefault();
		if (x < max_fields) { //max input box allowed
			x++; //text box increment
			$(wrapper).append('<div class=""> \
								<div class="row">\
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">\
										<div class="form-group">\
											<input type="text" class="form-control" id="material_amount'+x+'" name="material_amount[]" placeholder="Amount" onchange="storedata(material_amount'+x+',material_description'+x+')">\
										</div>\
									</div>\
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">\
										<div class="form-group">\
											<input type="text" class="form-control"id="material_description'+x+'" name="material_description[]"placeholder="Descripition">\
										</div>\
									</div>\
									<label for="">&nbsp;</label><a class=" btn btn-success mt-2 remove">- Remove</a>\
								</div>\
							</div>');
			
		}
		
	

	});

	$(wrapper).on("click", ".remove", function (e) { //user click on remove text
		e.preventDefault(); $(this).parent('div').remove(); x--;
	})


	
});

$(document).ready(function () {
	var max_fields = 10; //maximum input boxes allowed
	var wrapper = $(".my-class-scheme_attached"); //Fields wrapper
	var add_button = $(".add-more-button_attached"); 
	

	var x = 1; //initlal text box count
	$(add_button).click(function (e) {
		//  alert(1);
		e.preventDefault();
		if (x < max_fields) { //max input box allowed
			x++; //text box increment
			$(wrapper).append('<div class=""> \
								<div class="row">\
									<div class="col-xl-6 col-lg-6">\
										<div class="form-group">\
											<input type="file" class="form-control" id="others'+x+'" name="others[]" >\
										</div>\
									</div>\
									<label for="">&nbsp;</label><a class=" btn btn-success mt-2 remove">- Remove</a>\
								</div>\
							</div>');
			
		}
		
	

	});

	$(wrapper).on("click", ".remove", function (e) { //user click on remove text
		e.preventDefault(); $(this).parent('div').remove(); x--;
	})


	
});
