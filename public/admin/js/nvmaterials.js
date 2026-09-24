$(document).ready(function () {

	$.fn.dataTable.ext.errMode = 'throw';
 // Datatable of MaterialBOQ load
	function materialBOQDataTable() {
    	
		if ($(document).find('#nv_datatable').length > 0) {
			var nv_id = $('#nv_id').val();
			var service_id = $('#service_id').val();
			var dataTable = $('#nv_datatable').DataTable({
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
						service_id:service_id,
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
						data: 'material_short_text',
						name: 'material_short_text',
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
						  var formattedquantity = parseFloat(data);
						//   .toFixed(3)
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
								return 'Vendor Quotation';
							} else if (rateReferenceValue === 1) {
								return 'Last Work Order';
							} else if (rateReferenceValue === 2) {
								return 'C&M Rate Reference';
							} else if (rateReferenceValue === 4) {
								return 'Rate Reference';
							} else {
								return 'User Estimation';
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
							// var selectedDate = $("#imp_from").val();
							// var selectedDateYear = selectedDate ? new Date(selectedDate).getFullYear() : null;
					
							// if (selectedDateYear === null) {
							// 	return selectedYear.toString(); // Display the selected year if no date is selected
							// }
					
							// var yearArray = [];
							// for (let i = 0; i < selectedYear; i++) {
							// 	var startYear = selectedDateYear + i;
							// 	var endYear = startYear + 1;
							// 	yearArray.push(startYear.toString() + '-' + endYear.toString());
							// }

							// This is new code. Date: 07-01-2026
							var selectedDate = $("#fiscal_year").val();
							var selectedDateYear = selectedDate ? new Date(selectedDate).getFullYear() : null;
					
							if (selectedDateYear === null) {
								return selectedYear.toString(); // Display the selected year if no date is selected
							}
					
							var yearArray = [];
							for (let i = 0; i < selectedYear; i++) {
								var startYear = selectedDateYear + i;
								// var endYear = startYear + 1;
								// yearArray.push(startYear.toString() + '-' + endYear.toString());
								yearArray.push(selectedDate.toString());
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
						
								var mayValue = data['may' + i] !== null ? parseFloat(data['may' + i]).toFixed(2) : '0.00'; // Fix to 2 decimal places
						
								mayDataArray.push(mayValue);
						
							}
						
							// Replace black with 0 in the joined string
						
							const joinedData = mayDataArray.join('<hr>').replace(/black/g, '0');
						
							// Split the joinedData by '<hr>' to create an array of values
						
							const dataArray = joinedData.split('<hr>');
						
							// Join all values to get the final result
						
							const result = dataArray.join('<hr>');
						
							return result;
						
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
						
								var juneValue = data['june' + i] !== null ? parseFloat(data['june' + i]).toFixed(2) : '0.00';
						
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
						
								var julyValue = data['july' + i] !== null ? parseFloat(data['july' + i]).toFixed(2) : '0.00';
						
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
						
								var augustValue = data['august' + i] !== null ? parseFloat(data['august' + i]).toFixed(2) : '0.00';
						
								augustDataArray.push(augustValue);
						
							}
						
							
						
							// Replace black with 0 in the joined stringNo data available in table
						
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
						
								var septemberValue = data['september' + i] !== null ? parseFloat(data['september' + i]).toFixed(2) : '0.00';
						
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
						
								var octValue = data['oct' + i] !== null ? parseFloat(data['oct' + i]).toFixed(2) : '0.00';
						
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
						
								var novValue = data['nov' + i] !== null ? parseFloat(data['nov' + i]).toFixed(2) : '0.00';
						
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
						
								var decValue = data['dec' + i] !== null ? parseFloat(data['dec' + i]).toFixed(2) : '0.00';
						
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
						
								var janValue = data['jan' + i] !== null ? parseFloat(data['jan' + i]).toFixed(2) : '0.00';
						
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
						
								var febValue = data['feb' + i] !== null ? parseFloat(data['feb' + i]).toFixed(2) : '0.00';
						
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
						
								var marchValue = data['march' + i] !== null ? parseFloat(data['march' + i]).toFixed(2) : '0.00';
						
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
				'aaSorting': [[1, 'desc']],
				// "drawCallback": function(settings) {
				// 	var api = this.api();
				// 	var total = api.column(7).data().reduce(function(a, b) {
				// 		return parseFloat(a) + parseFloat(b);
				// 	}, 0);
				// 	$('#total_mat_mat').val(total.toFixed(2));
				// }
			});
			$('#RefreshBtn').on('click', function(e) {
				e.preventDefault(); // Prevent default link behavior (page reload)
		
				// Reload DataTable
				dataTable.ajax.reload();
				updateTotalMaterialAmount();
			});
			function updateTotalMaterialAmount() {
				var nv_id = $('#nv_id').val();
				var bgt_prov = $('#bgt_prov').val();
				var service_id = $('#service_id').val();
				$.ajax({
					type: 'GET',
					url: '/admin/nv_material/data',
					data: {
						nv_id: nv_id,
						service_id:service_id,
					},
					success: function(response) {
						if(response.success==true){
							if(bgt_prov == "Approved"){
								$('#total_mat').val(response.total);
							}else{
								$('#total_mat_mat').val(response.total);
							}
							
						}
						
						
					},
					error: function(error) {
					 
					}
				});
			 }
			}
	}
// Datatable of ServiceBOQ load
	function serviceBOQDataTable(){
		if ($(document).find('#nvmaterial_serviceBoq_datatable').length > 0) {
			var nv_id = $('#nv_id').val();
			var service_id = $('#service_id').val();
			// alert(nv_id);
			var dataTable2 = $('#nvmaterial_serviceBoq_datatable').DataTable({
				responsive: true,
				processing: true,
				serverSide: true,
				destroy: true,
				"searching": true,
				ajax: {
					data: {
						nv_id: nv_id,
						service_id:service_id
					},
					//url: '/admin/material/nv_serviceBoq',
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
				'aaSorting': [[1, 'desc']],
				// "drawCallback": function(settings) {
				// 	var api = this.api();
				// 	var total = api.column(7).data().reduce(function(a, b) {
				// 		return parseFloat(a) + parseFloat(b);
				// 	}, 0);
				// 	$('#total_ser_amo').val(total.toFixed(2));
				// }
			});
			$('#RefreshBtn2').on('click', function(e) {
				e.preventDefault(); // Prevent default link behavior (page reload)
		
				// Reload DataTable
				dataTable2.ajax.reload();
				updateTotalServiceAmount();
			});
			function updateTotalServiceAmount() {
				var nv_id = $('#nv_id').val();
				var service_id = $('#service_id').val();
				$.ajax({
					type: 'GET',
					url: '/admin/nv_material/data',
					data: {
						nv_id: nv_id,
						service_id: service_id,
					},
					success: function(response) {
						if(response.success==true){
							$('#total_ser_amo').val(response.serviceTotal);
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
	var autoSaveInterval = 10;  // 60 seconds (adjust as needed)
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
		var proposal_name = $('#proposal_name').val();
		var benefit = $('#benefit').val();
		var new_product_text = $('#new_product_text').val();
		var quant_just_text = $('#quant_just_text').val();
		var cause_analysis = $('#cause_analysis').val();
		var special_remarks = $('#special_remarks').val();
		var root_cause_analysis = $('#root_cause_analysis').val();
		var derc_approval = $('#derc_approval').val();
		var derc_ref_no = $('input[name="derc_ref_no"]').val();
		var derc_app_date = $('input[name="derc_app_date"]').val();

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
		if (root_cause_analysis.length > 1000) {
			$('.root_cause_analysis_error').html('Root Cause Analysis should not be more than 1000 characters');
			$('.root_cause_analysis_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			
		}else{
			$('.root_cause_analysis_error').html('');
		}
		if (quant_just_text.length > 200) {
			$('.quant_just_text_error').html('Quantity Justification should not be more than 200 characters');
			$('.quant_just_text_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			
		}else{
			$('.quant_just_text_error').html('');
		}
		if (new_product_text.length > 200) {
			$('.new_product_text_error').html('New Product should not be more than 200 characters');
			$('.new_product_text_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			
		}else{
			$('.new_product_text_error').html('');
		}
		if (benefit.length > 2500) {
			$('.benefit_error').html('Benefit should not be more than 2500 characters');
			$('.benefit_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			
		}else{
			$('.benefit_error').html('');
		}
		

		if (proposal_name.length > 200) {
			$('.proposal_name_error').html('Proposal Name should not be more than 200 characters');
			$('.proposal_name_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			
		}else{
			$('.proposal_name_error').html('');
		}
	
		if (length_just_prop > 2500) {
			$('.just_Prop_error').html('Detailed Justification should not be more than 2500 characters');
			$('.just_Prop_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			
		}else{
			$('.just_Prop_error').html('');
		}
		if (length_background > 2500) {
			$('.background_error').html('Background should not be more than 2500 characters');
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
        var formData = new FormData($('#create_nv_material')[0]);
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
            url: '/admin/store_form', // Replace with your Laravel route
            data: formData,
            contentType: false, // Required when sending FormData
            processData: false, // Required when sending FormData
            success: function(response) {
				if(response.service_id != null){
				$('#service_id').val(response.service_id);
                // Call the function of Datatable of MaterialBOQ and ServiceBOQ
				materialBOQDataTable();
				serviceBOQDataTable();
				}
				
				
				
                // Swal.fire({
                //     title: "Success!",
                //     text: "Form Auto Successfully Saved.",
                //     icon: "success",
                //     button: "OK",
                // }).then(function () {
                //     // Reload the page after the user clicks "OK"
                //     location.reload();
                // });
            },
			
            error: function(error) {
                console.error('Auto-save failed:', error);
            }
        });

       
        // Update previousFiles with currentFiles for the next auto-save
        previousFiles = currentFiles;
    }

	// Call the function of Datatable of MaterialBOQ and ServiceBOQ
	materialBOQDataTable();
	serviceBOQDataTable();

    // Start auto-save timer
    timerId = setInterval(autoSave, autoSaveInterval);

    // Stop auto-save when the user submits the form
  
	setTimeout(function() {
        clearInterval(timerId); // Clear initial timer
        autoSaveInterval = 10000; 
        timerId = setInterval(autoSave, autoSaveInterval); // Start new timer
    }, autoSaveInterval); // Wait for the initial interval to pass

	$('#create_nv_material').change('input, select, textarea',function() {
		autoSave(); 
	  });

	$('#create_nv_material').submit(function(event) {
        clearInterval(timerId);
    });
	// Datatable
	
	
	
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
			
			},
			
			remark: {
				required: true,
				maxlength: 200,

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
			
		},
		messages: {
			dept_id: {
				required: "Please select department",
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
			material_code: {
				required: "Please enter service code",

			},
			
		},
	
	});
 	function parseInput(id) {
        var val = $(id).val() || "0";
        // Remove commas and spaces, handle empty or invalid input
        val = val.replace(/,/g, '').trim();
        // Parse as float and ensure 2 decimal precision
        return isNaN(val) ? 0 : parseFloat(parseFloat(val).toFixed(2));
    }
	$('#create_nv_material').on('submit', function (e) {
		e.preventDefault();
		var just_Prop = editor1.getData();
		var background =  editor2.getData();
		var broad_just =  editor3.getData();
		var length_just_prop = getPlainTextLength(just_Prop);
		var length_broad_just = getPlainTextLength(broad_just);
		var length_background = getPlainTextLength(background);
		var bgt_prov = $('#bgt_prov').val();
		var proposal_name = $('#proposal_name').val();
		var benefit = $('#benefit').val();
		var new_product_text = $('#new_product_text').val();
		var quant_just_text = $('#quant_just_text').val();
		var cause_analysis = $('#cause_analysis').val();
		var special_remarks = $('#special_remarks').val();
		var root_cause_analysis = $('#root_cause_analysis').val();
		var approved_budget = parseFloat($('#approved_budget').val());

		// var revised_budget1 = parseFloat($('#revised_budget1').val());
		// var revised_budget2 = parseFloat($('#revised_budget2').val());
		var total_mat_mat2 = parseFloat($('#total_mat_mat2').val());
		var total_mat_mat3 = parseFloat($('#total_mat_mat3').val());
		// var year1 = $('#year1').val();
		// var year2 = $('#year2').val();
		var tax1 = parseFloat($('#tax1').val());
		var tax_amount2 = parseFloat($('#tax_amount2').val());
		var tax_amount3 = parseFloat($('#tax_amount3').val());
            var total_budget_service = parseInput("#total_budget_service");
            var total_budgets = parseInput(".budgetBoth");

		// if (!isNaN(total_mat_mat2) && !isNaN(tax_amount2)) {
		// 	var total_mat_mat2 = total_mat_mat2 + ((total_mat_mat2 * tax_amount2) / 100) ||0;
		// }else{
        //    var total_mat_mat2 = total_mat_mat2 || 0;
		// }

		// if (!isNaN(total_mat_mat3) && !isNaN(tax_amount3)) {
		// 	var total_mat_mat3 = total_mat_mat3 +  ((total_mat_mat3 * tax_amount3) / 100) ||0;
		// }else{
        //    var total_mat_mat3 = total_mat_mat3 || 0;
		// }
		   var total_material = total_budgets - total_budget_service;
            var sum = total_material + total_budget_service;
			
		if(bgt_prov == "Approved"){
			
			// if(total_mat_mat2 > revised_budget1){
			// 	Swal.fire({
			// 		text: year1 + " Material Amount should not be greater than " + year1 + " Budget Available",
			// 		type: 'error',
			// 		buttonsStyling: false,
			// 		confirmButtonText: "OK",
			// 		confirmButtonClass: "btn font-weight-bold btn-light"
			// 	}).then(function() {
			// 		window.location.reload();
			// 	});
			// 	return;
			// }
			// if(total_mat_mat3 > revised_budget2){
			// 	Swal.fire({
			// 		text: year2 + " Material Amount should not be greater than " + year2 + " Budget Available",
			// 		type: 'error',
			// 		buttonsStyling: false,
			// 		confirmButtonText: "OK",
			// 		confirmButtonClass: "btn font-weight-bold btn-light"
			// 	}).then(function() {
			// 		window.location.reload();
			// 	});
			// 	return;
			// }
		// var totalAmount = parseFloat($('#total_mat').val()) || 0;
		// }else{
			var totalAmount = parseFloat($('#total_mat_mat').val()) || 0;
			var CheckYes = $('#CheckYes').val();
			var total_ser_amo = parseFloat($("#total_ser_amo").val()) || 0;
	
		}
		// if (!isNaN(totalAmount) && !isNaN(tax1)) {
		// 	var totalAmount = totalAmount + ((totalAmount * tax1) / 100) ||0;
		// }else{
        //    var totalAmount = totalAmount || 0;
		// }
			function parseNumber(val) {
			if (!val) return 0;
			val = val.toString().replace(/,/g, "").replace(/[^\d.-]/g, "");
			var num = parseFloat(val);
			return isNaN(num) ? 0 : num;
		}

		var budgetAvailable = parseNumber($('#budget_avl').val());
		var add_budget = parseNumber($('#add_budget').val());
		var budgetBoth = parseNumber($('#total_budget_both').val());

	

		var budget_type = $('#budget_type').val();
		var fiscal_year = $('#fiscal_year').val();
		var service_id = $('#material_id').val();
		var department_id = $('#dept_id').val();
		var nvid = $('#nvid').val();
		var successMessage = "NV/" + budget_type + "/" + fiscal_year + "/" + department_id + "/";
	
		if (service_id == 1) {
			successMessage += "Material/" + nvid;
		} else if (service_id == 2) {
			successMessage += "Service/" + nvid;
		}
    $('.error-message').remove();

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
	if (root_cause_analysis.length > 1000) {
		$('.root_cause_analysis_error').html('Root Cause Analysis should not be more than 1000 characters');
		$('.root_cause_analysis_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.root_cause_analysis_error').html('');
	}
	if (quant_just_text.length > 200) {
		$('.quant_just_text_error').html('Quantity Justification should not be more than 200 characters');
		$('.quant_just_text_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.quant_just_text_error').html('');
	}
	if (new_product_text.length > 200) {
		$('.new_product_text_error').html('New Product should not be more than 200 characters');
		$('.new_product_text_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.new_product_text_error').html('');
	}
	if (benefit.length > 2500) {
		$('.benefit_error').html('Benefit should not be more than 2500 characters');
		$('.benefit_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.benefit_error').html('');
	}
	if (proposal_name.length > 200) {
		$('.proposal_name_error').html('Proposal Name should not be more than 200 characters');
		$('.proposal_name_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.proposal_name_error').html('');
	}

	if (length_just_prop > 2000) {
		$('.just_Prop_error').html('Detailed Justification should not be more than 2000 characters');
		$('.just_Prop_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.just_Prop_error').html('');
	}
	if (length_background > 2500) {
		$('.background_error').html('Background should not be more than 2500 characters');
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
	    var derc_approval = $('#derc_approval').val();
		var derc_ref_no = $('input[name="derc_ref_no"]').val();
		var derc_app_date = $('input[name="derc_app_date"]').val();
		var implementsYears = parseInt($("#exampleFormControlSelect").val()) || 0;

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

		if(budgetBoth == 0){
			Swal.fire({
				text: "Total NV Amount should not be Zero",
				type: 'error',
				buttonsStyling: false,
				confirmButtonText: "OK",
				confirmButtonClass: "btn font-weight-bold btn-light"
			}).then(function() {
				window.location.reload();
			});
			return;
		}

if(bgt_prov == "Approved"){
	if(implementsYears != null){

		if(implementsYears == 1 ){
			if(totalAmount == 0){
			Swal.fire({
				text: "Please Update the Material Code before Save or Submit the NV",
				type: 'error',
				buttonsStyling: false,
				confirmButtonText: "OK",
				confirmButtonClass: "btn font-weight-bold btn-light"
			}).then(function() {
				window.location.reload();
			});
			return;
		}
		}else if(implementsYears == 2 ){
			if(totalAmount == 0 || total_mat_mat2 == 0){
			Swal.fire({
				text: "Please Update the Material Code before Save or Submit the NV",
				type: 'error',
				buttonsStyling: false,
				confirmButtonText: "OK",
				confirmButtonClass: "btn font-weight-bold btn-light"
			}).then(function() {
				window.location.reload();
			});
			return;
		}
		}else if(implementsYears == 3 ){


			if(totalAmount == 0 || total_mat_mat2 == 0 ||  total_mat_mat3 == 0){
			Swal.fire({
				text: "Please Update the Material Code before Save or Submit the NV",
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
	   }


	// var year3 = ($("#year3").val());
	// if (totalAmount > budgetAvailable) {
	// 	Swal.fire({
	// 		text: year3+" Amount should not be greater than Budget Available.",
	// 		type: 'error',
	// 		buttonsStyling: false,
	// 		confirmButtonText: "OK",
	// 		confirmButtonClass: "btn font-weight-bold btn-light"
	// 	}).then(function() {
	// 		window.location.reload();
	// 	});
       

    //     return; // Stop form submission
    // }
	
	if(budgetBoth != 0 && isNaN(approved_budget)){
		$('.approved_budget_error').html('Please Enter Approved Budget');
		$('.approved_budget_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
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
	var year3 = ($("#year3").val());
	if(CheckYes == 3 && approved_budget > (totalAmount)){
	 $('.approved_budget_error').html('Approved Budget should not be greater than FY '+year3+' Amount');
	 $('.approved_budget_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	 return;
	 }else if(CheckYes == 2 && approved_budget > (sum)){
		$('.approved_budget_error').html('Approved Budget should not be greater than Total Amount  ('+year3+ ') Material+Service Amount');
		$('.approved_budget_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	 }else{
		 $('.approved_budget_error').html('');
	 }
	if(approved_budget > (budgetBoth)){
		$('.approved_budget_error').html('Approved Budget should not be greater than Total Amount');
		$('.approved_budget_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
		}else{
			$('.approved_budget_error').html('');
		}

		const tolerance = 0.01;
		const calculatedValue = budgetBoth - add_budget;

		if (Math.abs(approved_budget - calculatedValue) > tolerance) {
			$('.approved_budget_error').html('Please Check the Calculations and click on <i class="fas fa-sync-alt text-info"></i>');
			$('.approved_budget_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
			return;
		} else {
			$('.approved_budget_error').html('');
		}
	
	// if(approved_budget !== (budgetBoth-add_budget)){
	// 	$('.approved_budget_error').html('Please Check the Calculations and click on <i class="fas fa-sync-alt text-info"></i>');
	// 	$('.approved_budget_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	// 	return;
	// 	}else{
	// 		$('.approved_budget_error').html('');
	// 	}
	
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
			formData.append("broad_just", broad_just);
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
					window.location.reload();
					if (res.result == 'success') {
						Swal.fire({
							html: successMessage + " <br>has been submitted successfully",
							type: 'success',
							buttonsStyling: false,
							confirmButtonText: "OK",
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

	$('.save_btn').on('click', function () {
    setTimeout(function () {
        location.reload();
    }, 500); // 0.5 second delay
	});
	$('.submit_btn').on('click', function () {
    setTimeout(function () {
        location.reload();
    }, 500); // 0.5 second delay
	});
	$('.save_btn').click(function (e) {

		e.preventDefault();
		var just_Prop = editor1.getData();
		var length_just_prop = getPlainTextLength(just_Prop);
		var broad_just =  editor3.getData();
		var length_broad_just = getPlainTextLength(broad_just);
		var background =  editor2.getData();
		var length_background = getPlainTextLength(background);
		var bgt_prov = $('#bgt_prov').val();
		var proposal_name = $('#proposal_name').val();
		var benefit = $('#benefit').val();
		var new_product_text = $('#new_product_text').val();
		var quant_just_text = $('#quant_just_text').val();
		var cause_analysis = $('#cause_analysis').val();
		var special_remarks = $('#special_remarks').val();
		var root_cause_analysis = $('#root_cause_analysis').val();
		var approved_budget = parseFloat($('#approved_budget').val());
		
		// var revised_budget1 = parseFloat($('#revised_budget1').val());
		// var revised_budget2 = parseFloat($('#revised_budget2').val());
		var total_mat_mat2 = parseFloat($('#total_mat_mat2').val());
		var total_mat_mat3 = parseFloat($('#total_mat_mat3').val());
		// var year1 = $('#year1').val();
		// var year2 = $('#year2').val();
		var tax1 = parseFloat($('#tax1').val());
		var tax_amount2 = parseFloat($('#tax_amount2').val());
		var tax_amount3 = parseFloat($('#tax_amount3').val());

		// if (!isNaN(total_mat_mat2) && !isNaN(tax_amount2)) {
		// 	var total_mat_mat2 = total_mat_mat2 + ((total_mat_mat2 * tax_amount2) / 100) ||0;
		// }else{
        //    var total_mat_mat2 = total_mat_mat2 || 0;
		// }

		// if (!isNaN(total_mat_mat3) && !isNaN(tax_amount3)) {
		// 	var total_mat_mat3 = total_mat_mat3 + ((total_mat_mat3 * tax_amount3) / 100) ||0;
		// }else{
        //    var total_mat_mat3 = total_mat_mat3 || 0;
		// }

		if(bgt_prov == "Approved"){
			// if(total_mat_mat2 > revised_budget1){
			// 	Swal.fire({
			// 		text: year1 + " Material Amount should not be greater than " + year1 + " Budget Available",
			// 		type: 'error',
			// 		buttonsStyling: false,
			// 		confirmButtonText: "OK",
			// 		confirmButtonClass: "btn font-weight-bold btn-light"
			// 	}).then(function() {
			// 		window.location.reload();
			// 	});
			// 	return;
			// }
			// if(total_mat_mat3 > revised_budget2){
			// 	Swal.fire({
			// 		text: year2 + " Material Amount should not be greater than " + year2 + " Budget Available",
			// 		type: 'error',
			// 		buttonsStyling: false,
			// 		confirmButtonText: "OK",
			// 		confirmButtonClass: "btn font-weight-bold btn-light"
			// 	}).then(function() {
			// 		window.location.reload();
			// 	});
			// 	return;
			// }
		// var totalAmount = parseFloat($('#total_mat').val()) || 0;
		// }else{
			var totalAmount = parseFloat($('#total_mat_mat').val()) || 0;	
		}
		// if (!isNaN(totalAmount) && !isNaN(tax1)) {
		// 	var totalAmount = totalAmount + ((totalAmount * tax1) / 100) ||0;
		// }else{
        //    var totalAmount = totalAmount || 0;
		// }
		var budgetAvailable = parseFloat($('#budget_avl').val()) || 0; 
		var totalserviceAmount = parseFloat($('#total_ser_amo').val()) || 0; 
		var serbudgetAvailable = $('#ser_budget_avl').val() || 0;
		var add_budget = parseFloat($('#add_budget').val()) || 0;
		serbudgetAvailable = parseFloat(serbudgetAvailable.replace(",", ""));
		var budgetBoth = parseFloat($('#total_budget_both').val()) || 0;

		var budget_type = $('#budget_type').val();
		var fiscal_year = $('#fiscal_year').val();
		var service_id = $('#material_id').val();
		var department_id = $('#dept_id').val();
		var nvid = $('#nvid').val();
		var successMessage = "NV/" + budget_type + "/" + fiscal_year + "/" + department_id + "/";
	
		if (service_id == 1) {
			successMessage += "Material/" + nvid;
		} else if (service_id == 2) {
			successMessage += "Service/" + nvid;
		}
    $('.error-message').remove();

	var derc_approval = $('#derc_approval').val();
	var derc_ref_no = $('input[name="derc_ref_no"]').val();
	var derc_app_date = $('input[name="derc_app_date"]').val();
	var implementsYears = parseInt($("#exampleFormControlSelect").val()) || 0;

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
	if (root_cause_analysis.length > 1000) {
		$('.root_cause_analysis_error').html('Root Cause Analysis should not be more than 1000 characters');
		$('.root_cause_analysis_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.root_cause_analysis_error').html('');
	}
	if (quant_just_text.length > 200) {
		$('.quant_just_text_error').html('Quantity Justification should not be more than 200 characters');
		$('.quant_just_text_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.quant_just_text_error').html('');
	}
	if (new_product_text.length > 200) {
		$('.new_product_text_error').html('New Product should not be more than 200 characters');
		$('.new_product_text_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.new_product_text_error').html('');
	}
	if (benefit.length > 2500) {
		$('.benefit_error').html('Benefit should not be more than 2500 characters');
		$('.benefit_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.benefit_error').html('');
	}
	if (proposal_name.length > 200) {
		$('.proposal_name_error').html('Proposal Name should not be more than 200 characters');
		$('.proposal_name_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.proposal_name_error').html('');
	}
	if (length_just_prop > 2000) {
		$('.just_Prop_error').html('Detailed Justification should not be more than 2000 characters');
		$('.just_Prop_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		return;
	}else{
		$('.just_Prop_error').html('');
	}
	if (length_background > 2500) {
		$('.background_error').html('Background should not be more than 2500 characters');
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
		// if(implementsYears != null){
		// 	if(implementsYears == 1 ){
		// 		if(totalAmount == 0){
		// 		Swal.fire({
		// 			text: "Please Update the Material Code before Save or Submit the NV",
		// 			type: 'error',
		// 			buttonsStyling: false,
		// 			confirmButtonText: "OK",
		// 			confirmButtonClass: "btn font-weight-bold btn-light"
		// 		}).then(function() {
		// 			window.location.reload();
		// 		});
		// 		return;
		// 	}
		// 	}else if(implementsYears == 2 ){
		// 		if(totalAmount == 0 || total_mat_mat2 == 0){
		// 		Swal.fire({
		// 			text: "Please Update the Material Code before Save or Submit the NV",
		// 			type: 'error',
		// 			buttonsStyling: false,
		// 			confirmButtonText: "OK",
		// 			confirmButtonClass: "btn font-weight-bold btn-light"
		// 		}).then(function() {
		// 			window.location.reload();
		// 		});
		// 		return;
		// 	}
		// 	}else if(implementsYears == 3 ){
		// 		if(totalAmount == 0 || total_mat_mat2 == 0 ||  total_mat_mat3 == 0){
		// 		Swal.fire({
		// 			text: "Please Update the Material Code before Save or Submit the NV",
		// 			type: 'error',
		// 			buttonsStyling: false,
		// 			confirmButtonText: "OK",
		// 			confirmButtonClass: "btn font-weight-bold btn-light"
		// 		}).then(function() {
		// 			window.location.reload();
		// 		});
		// 		return;
		// 	}
		// 	}
		//    }
	
		
		//    var year3 = ($("#year3").val());
		//    if (totalAmount > budgetAvailable) {
		// 	   Swal.fire({
		// 		   text: year3+" Amount should not be greater than Budget Available.",
		// 		   type: 'error',
		// 		   buttonsStyling: false,
		// 		   confirmButtonText: "OK",
		// 		   confirmButtonClass: "btn font-weight-bold btn-light"
		// 	   }).then(function() {
		// 		   window.location.reload();
		// 	   });
			  
	   
		// 	   return; // Stop form submission
		//    }
		   
		// Commented on 25 September by Pooja
		
		//    if(budgetBoth != 0 && isNaN(approved_budget)){
		// 	   $('.approved_budget_error').html('Please Enter Approved Budget');
		// 	   $('.approved_budget_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		// 	   return;
		//    }else{
		// 	   $('.approved_budget_error').html('');
		//    }
		//    if (approved_budget > budgetAvailable) {
		// 	   Swal.fire({
		// 		   text: "Approved Budget should not be greater than Budget Available.",
		// 		   type: 'error',
		// 		   buttonsStyling: false,
		// 		   confirmButtonText: "OK",
		// 		   confirmButtonClass: "btn font-weight-bold btn-light"
		// 	   }).then(function() {
		// 		   window.location.reload();
		// 	   });
	   
		// 	   return; 
		//    }
		//    var year3 = ($("#year3").val());
		//    if(approved_budget > (totalAmount)){
		// 	$('.approved_budget_error').html('Approved Budget should not be greater than FY '+year3+' Amount');
		// 	$('.approved_budget_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		// 	return;
		// 	}else{
		// 		$('.approved_budget_error').html('');
		// 	}
		//    if(approved_budget > (budgetBoth)){
		// 	   $('.approved_budget_error').html('Approved Budget should not be greater than Total Amount');
		// 	   $('.approved_budget_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		// 	   return;
		// 	   }else{
		// 		   $('.approved_budget_error').html('');
		// 	   }
		   
		//    if(approved_budget !== (budgetBoth-add_budget)){
		// 	   $('.approved_budget_error').html('Please Check the Calculations and click on <i class="fas fa-sync-alt text-info"></i>');
		// 	   $('.approved_budget_error')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
		// 	   return;
		// 	   }else{
		// 		   $('.approved_budget_error').html('');
		// 	   }
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
			formData.append("broad_just", broad_just);

			$.ajax({
				data: formData,
				cache: false,
				processData: false,
				contentType: false,
				type: 'post',
				url: "/admin/nv_material/store",
				success: function (response) {
					var res = response;
							window.location.reload();
					if (res.result == 'success') {
						var store =res.store;
						console.log(store);
						//  $('#derc_stakeholder_approvals').val(store);

						Swal.fire({
							html: successMessage + " <br>has been saved successfully",
							type: 'success',
							buttonsStyling: false,
							confirmButtonText: "OK",
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
							text: "Employee Updated.",
							type: 'success',
							buttonsStyling: false,
							confirmButtonText: "OK",
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

	
	$('.delete_btn').click(function (e) {
		e.preventDefault();
		var just_Prop = editor1.getData();
		var broad_just =  editor3.getData();
		var background =  editor2.getData();
		var bgt_prov = $('#bgt_prov').val();
         var totalAmount = $('#total_mat_mat').val();
		
         var budgetAvailable = $('#budget_avl').val();
		 budgetAvailable = budgetAvailable.replace(/[^a-zA-Z0-9_ ]/g, "");
		 budgetAvailable = parseFloat(budgetAvailable);

		 var totalserviceAmount = $('#total_ser_amo').val();
	
         var serbudgetAvailable = $('#ser_budget_avl').val();
		 serbudgetAvailable = serbudgetAvailable.replace(/[^a-zA-Z0-9_ ]/g, "");
		 serbudgetAvailable = parseFloat(serbudgetAvailable);
		 var status = $('.save_btn').val();
		 var draft = $('#draft1').val();
		 var nv_id = $("#nv_id").val();
		 var formData = new FormData($('#create_nv_material')[0]);
		 formData.append("status", status);
		 formData.append("draft", draft);
		 formData.append("just_Prop", just_Prop);
		 formData.append("broad_just", broad_just);
		 var budgetBoth = $('#total_budget_both').val();
		 budgetBoth = budgetBoth.replace(/[^a-zA-Z0-9_ ]/g, "");
		 budgetBoth = parseFloat(budgetBoth);
		 var budget_type = $('#budget_type').val();
			var fiscal_year = $('#fiscal_year').val();
			var service_id = $('#material_id').val();
			var department_id = $('#dept_id').val();
		 var successMessage = "NV/" + budget_type + "/" + fiscal_year + "/" + department_id + "/";
			if (service_id == 1) {
				successMessage += "Material/" + nv_id;
			} else if (service_id == 2) {
				successMessage +=  "Service/" + nv_id;
			}
		
		
		Swal.fire({
			title: "Do You Want To Delete The NV <br> "+ " " + "(" + successMessage+ ")" + " ?",
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
							url: "/admin/nv_material/delete/" + nv_id,
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
$(document).ready(function () {

    /* -----------------------------------------------------------
       SINGLE MATERIAL DELETE
    ------------------------------------------------------------*/
    $(document).on('click', '.delete_material', function (event) {
        event.preventDefault();

        const $deleteButton = $(this);
        var implementsYears = parseInt($("#exampleFormControlSelect").val()) || 0;
        var bgt_prov = $('#bgt_prov').val();

        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes'
        }).then((result) => {

            if (!result.value && !result.isConfirmed) return;

            $('.pre-loader').show();
            let floor_id = $deleteButton.attr('data-id');

            $.ajax({
                data: {
                    'floor_id': floor_id,
                    '_token': $('input[name="_token"]').val()
                },
                type: 'DELETE',
                url: '/admin/nv_material/delete/' + floor_id,

                success: function (response) {
                    let res = response;

                    if (res.result === 'success') {

                        const nvDataTable = $('#nv_datatable').DataTable();
                        nvDataTable.row($deleteButton.closest('tr')).remove().draw(false);

                        // Update totals
                        if (bgt_prov === "Approved") {

                            var totservice = $('#total_mat').val() || 0;
                            var totservice1 = $('#total_mat_mat').val() || 0;
                            var totservice2 = $('#total_mat_mat2').val() || 0;
                            var totservice3 = $('#total_mat_mat3').val() || 0;

                            $('#total_mat').val(parseInt(totservice) - parseInt(res.response));

                            if (implementsYears !== 0) {
                                $('#total_mat_mat').val(parseInt(totservice1) - parseInt(res.total_amount1));
                                $('#total_mat_mat2').val(parseInt(totservice2) - parseInt(res.total_amount2));
                                $('#total_mat_mat3').val(parseInt(totservice3) - parseInt(res.total_amount3));
                            } else {
                                $('#total_mat_mat').val(parseInt(totservice1) - parseInt(res.response));
                            }

                        } else {
                            var totservice = $('#total_mat_mat').val() || 0;
                            $('#total_mat_mat').val(parseInt(totservice) - parseInt(res.response));
                        }

                        $('.pre-loader').hide();

                        Swal.fire({
                            text: res.msg,
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.reload();   // 🔥 RELOAD PAGE
                        });

                    } else {
                        $('.pre-loader').hide();

                        Swal.fire({
                            text: "Something went wrong. Please try again.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                },

                error: function () {
                    $('.pre-loader').hide();
                }
            });
        });
    });


    /* -----------------------------------------------------------
       DELETE ALL MATERIALS
    ------------------------------------------------------------*/
    $(document).on('click', '.delete_all_materials', function (event) {
        event.preventDefault();

        const $btn = $(this);
        const bgt_prov = $('#bgt_prov').val();
        const service_id = $('#service_id').val();
        const nv_id = $btn.data('nv-id');
        const allmaterial = $btn.data('id');
        const token = $('input[name="_token"]').val();

        Swal.fire({
            title: 'Are you sure you want to delete all materials?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes'
        }).then((result) => {

            if (!result.value && !result.isConfirmed) return;

            $('.pre-loader').show();

            $.ajax({
                url: '/admin/nv_material/delete/all/' + allmaterial,
                type: 'DELETE',
                data: {
                    nv_id: nv_id,
                    service_id: service_id,
                    _token: token
                },

                success: function (response) {
                    $('.pre-loader').hide();

                    if (response.result === 'success') {

                        // Reset totals first
                        $("#total_mat").val(0);
                        $("#total_mat_mat").val(0);
                        $("#total_budget_both").val(0);
                        $("#ser_budget_avl").val(0);

                        if (bgt_prov === "Approved") {
                            $("#total_mat_mat2").val(0);
                            $("#total_mat_mat3").val(0);
                        }

                        Swal.fire({
                            text: response.msg || "All materials deleted successfully!",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.reload();    // 🔥 RELOAD PAGE
                        });

                    } else {
                        Swal.fire({
                            text: response.msg || "Something went wrong. Please try again.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                },

                error: function () {
                    $('.pre-loader').hide();
                    Swal.fire({
                        text: "Server error. Please try again later.",
                        icon: "error"
                    });
                }
            });
        });
    });

});

	
	$(document).ready(function () {
		
		// Delete single service
		$(document).on('click', '.delete_service', function () {
			Swal.fire({
				title: 'Are you sure?',
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
						url: '/admin/nv_material/service_delete/' + service_id,
	
						success: function (response) {
							let res = response;
	
							if (res.result == 'success') {
								// Remove the deleted row from the DataTable
								$('#nvmaterial_serviceBoq_datatable').DataTable().row($(this).closest('tr')).remove().draw(false);
								var totservice = $('#total_ser_amo').val() || 0;
								var tot_ser = parseFloat(totservice) - parseFloat(res.response);
								$('#total_ser_amo').val(tot_ser);
	
								Swal.fire({
									text: res.msg,
									type: "success",
									buttonsStyling: false,
									confirmButtonText: "OK",
									confirmButtonClass: "btn font-weight-bold btn-primary"
								}).then(function () {
									// You can optionally do something here after the success message
								});
							} else if (res.result == 'failure') {
								Swal.fire({
									text: "Something went wrong. Please try again.",
									type: "error",
									buttonsStyling: false,
									confirmButtonText: "OK",
									confirmButtonClass: "btn font-weight-bold btn-light"
								}).then(function () {
									// You can optionally do something here after the failure message
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
	
		// Delete all services
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
					var service_id = $("#service_id").val();
					let nv_id = $(this).data('nv-id'); // Retrieve nv_id from data attribute
					let $allmaterial = $(this).data('id'); // Retrieve data-id attribute
	
					$.ajax({
						data: {
							'nv_id': nv_id, // Include nv_id in the data
							'service_id' : service_id,
							'_token': $('input[name="_token"]').val()
						},
						type: 'DELETE',
						url: '/admin/nv_material/service_delete/all/' + $allmaterial,
	
						success: function (response) {
							let res = response;
	
							if (res.result == 'success') {
								// Remove all rows from the DataTable
								$('#nvmaterial_serviceBoq_datatable').DataTable().clear().draw(false);
	
								Swal.fire({
									text: res.msg,
									type: "success",
									buttonsStyling: false,
									confirmButtonText: "OK",
									confirmButtonClass: "btn font-weight-bold btn-primary"
								}).then(function () {
									$("#total_ser_amo").val(0);
								});
							} else if (res.result == 'failure') {
								Swal.fire({
									text: "Something went wrong. Please try again.",
									type: "error",
									buttonsStyling: false,
									confirmButtonText: "OK",
									confirmButtonClass: "btn font-weight-bold btn-light"
								}).then(function () {
									// You can optionally do something here after the failure message
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
			$(wrapper).append('<div class=""> <div class="row"><div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-2"><div class="form-group"> <label for="exampleFormControlInput1">Scheme No</label> <input type="text" class="form-control" id="scheme_no'+x+'" name="scheme_no[]" onkeyup="storeapi(scheme_no'+x+',scheme_des'+x+')" value="" placeholder="Enter Scheme No""></div></div><div class="col-12  mb-2"><div class="form-group"><label for="exampleFormControlInput1">Scheme Description</label><textarea name="scheme_des[]" id="scheme_des'+x+'"  cols="2" readonly rows="10" class="form-control" value="" placeholder="Enter Scheme Description"></textarea></div></div><label for="">&nbsp;</label><a class=" btn btn-success mt-2 remove">- Remove</a></div></div>'); //add input box
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
			var checkedceonom1 = $('input[name="transfer_to_nominee1"]:checked').val();

		// If no active radio is checked (disabled case), get hidden input value
		if (checkedceonom1 === undefined) {
			checkedceonom1 = $('input[name="transfer_to_nominee1"][type="hidden"]').val();
		}

		// If still empty, fallback to 0 by default
		if (checkedceonom1 === undefined || checkedceonom1 === null) {
			checkedceonom1 = 0;
		}

		var transfer_ceonom1 = checkedceonom1;
		var fullPath = $('#approval_attachements').val();
		var fileName = fullPath.split('\\').pop().split('/').pop();
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
	
		// Validate the remark
		if (remark.trim() === '') {
			$('.remark_error').html('Please enter your remark');
			// return;
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
			$('.remark_error').html('Remark should not be more than 200 characters');
			// return;
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
	
		// var check_technology = $('input[name="check_technology"]:checked').val();
		var derc_info = $('input[name="derc_info"]').val();
		var check_ceonm2 = $('input[name="check_ceonm2"]:checked').val();
		// Create a FormData object
		var formData = new FormData();
		formData.append('status_id', status_id);
		formData.append('material_id', material_id);
		formData.append('nv_id', nv_id);
		formData.append('remark', remark);
		formData.append('transfer_ceonom1', transfer_ceonom1);
		formData.append('check_ceonm2', check_ceonm2);
		formData.append('derc_info', derc_info);
		formData.append('file', $('#approval_attachements')[0].files[0]); // Append the file
	
		// Use SweetAlert for confirmation
		Swal.fire({
			title: "Do You Want To Approve The NV<br> " + " " + "(" + successMessage + ")" + "?",
			type: "question",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Proceed"
		}).then((result) => {
			if (result.value) {
				// Proceed with AJAX request if the user confirms
				if ($('#preview_nvmaterial').valid()) {
					$('.pre-loader').show();
	
					$.ajax({
						data: formData,
						type: 'POST', // Change to 'POST' for file uploads
						processData: false, // Prevent jQuery from processing the data
						contentType: false, // Prevent jQuery from setting contentType
						url: '/admin/nv_material/approvedByStatus',
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
								$('#preview_nvmaterial')[0].reset();
							} else if (response.result == 'error') {
								let error_msgs = response.msg;
								for (let key in error_msgs) {
									if (error_msgs.hasOwnProperty(key)) {
										$('#preview_nvmaterial').find('.' + key + '_error').html(error_msgs[key][0]);
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
	
	
	
//for the remark error show in the material priview page
	$('textarea[name="remark"]').on('input', function () {
		$('.remark_error').html('');
	});


	$('.reject-button').click(function () {
		var fullPath = $('#approval_attachements').val();
		var check_ceonm2 = $('input[name="check_ceonm2"]:checked').val();
		var fileName = fullPath.split('\\').pop().split('/').pop();
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
	
		var status_id = $('.reject-button').val();
		var remark = $('textarea[name="remark"]').val();
	
		// Validate the remark
		if (remark.trim() === '') {
			$('.remark_error').html('Please enter your remark');
			// return;
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
			$('.remark_error').html('Remark should not be more than 200 characters');
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
			// Clear the error message if the 'remark' field is valid
			$('.remark_error').html('');
		}
		
		// Create a FormData object
		var formData = new FormData();
		formData.append('status_id', status_id);
		formData.append('material_id', material_id);
		formData.append('nv_id', nv_id);
		formData.append('remark', remark);
		formData.append('check_ceonm2', check_ceonm2);
		formData.append('file', $('#approval_attachements')[0].files[0]); // Append the file
	
		// Use SweetAlert for confirmation
		Swal.fire({
			title: "Do You Want To Reject The NV <br> "+ " " + "(" + successMessage+ ")" + "?",
			type: "question",
			showCancelButton: true,
			confirmButtonColor: "#d33",
			cancelButtonColor: "#3085d6",
			confirmButtonText: "Yes"
		}).then((result) => {
			if (result.value) {
				// Proceed with AJAX request if user confirms
				if ($('#preview_nvmaterial').valid()) {
					$('.pre-loader').show();
	
					$.ajax({
						data: formData,
						type: 'POST', // Change to 'POST' for file uploads
						processData: false, // Prevent jQuery from processing the data
						contentType: false, // Prevent jQuery from setting contentType
						url: '/admin/nv_material/approvedByStatus',
						success: function (response) {
							// Handle response
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


	$('.save-button').click(function () {
		var checkedceonom1 = $('input[name="transfer_to_nominee1"]:checked').val();

		// If no active radio is checked (disabled case), get hidden input value
		if (checkedceonom1 === undefined) {
			checkedceonom1 = $('input[name="transfer_to_nominee1"][type="hidden"]').val();
		}

		// If still empty, fallback to 0 by default
		if (checkedceonom1 === undefined || checkedceonom1 === null) {
			checkedceonom1 = 0;
		}

		var transfer_ceonom1 = checkedceonom1;

		// ... your existing code ...
		var fullPath = $('#approval_attachements').val();
		var check_ceonm2 = $('input[name="check_ceonm2"]:checked').val();
		var fileName = fullPath.split('\\').pop().split('/').pop();
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

		var status_id = $('.save-button').val();
		var remark = $('textarea[name="remark"]').val();

		// validations...
		if (remark.trim() === '') {
			$('.remark_error').html('Please enter your remark');
			Swal.fire({
				text: "Please Enter Approval & Rejection Remarks",
				type: 'error',
				buttonsStyling: false,
				confirmButtonText: "OK",
				confirmButtonClass: "btn font-weight-bold btn-light"
			});
			return;  
		} else if (remark.length > 200) {
			$('.remark_error').html('Remark should not be more than 200 characters');
			Swal.fire({
				text: "Approval & Rejection Remarks should not be more than 200 characters",
				type: 'error',
				buttonsStyling: false,
				confirmButtonText: "OK",
				confirmButtonClass: "btn font-weight-bold btn-light"
			});
			return; 
		} else {
			$('.remark_error').html('');
		}

		// ✅ append the correct value now
		var formData = new FormData();
		formData.append('status_id', status_id);
		formData.append('material_id', material_id);
		formData.append('nv_id', nv_id);
		formData.append('remark', remark);
		formData.append('check_ceonm2', check_ceonm2);
		formData.append('transfer_ceonom1', transfer_ceonom1);
		formData.append('file', $('#approval_attachements')[0].files[0]);
		formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
		if ($('#preview_nvmaterial').valid()) {
			$('.pre-loader').show();

			$.ajax({
				data: formData,
				type: 'POST',
				processData: false,
				contentType: false,
				url: '/admin/nv_material/approvedByStatus',
				success: function (response) {
					if (response.result == 'success') {
						Swal.fire({
							html: successMessage + " <br>Has Been Saved Successfully!",
							type: 'success',
							buttonsStyling: false,
							confirmButtonText: "OK",
							confirmButtonClass: "btn font-weight-bold btn-primary"
						}).then(function () {
							if(response.sign_url != ''){
								window.location.href = response.sign_url;
							} else {
								window.location.reload();
							}
						});
					} else {
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
					$('.pre-loader').hide();
					console.error(error);
				}
			});
		}
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
    var selectedYear = parseInt($("#exampleFormControlSelect").val()) || 0;
    var bgt_prov = $('#bgt_prov').val();

    if (selectedYear === 0) {
        $('.select_year_error').html('Please Select Year to Proceed');
        $('#material_trend_year31')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
        return;
    } else {
        $('.select_year_error').html('');
    }

    // Handle totals based on approval status
    var totalAmount = 0, totalAmountyear1 = 0, totalAmountyear2 = 0, totalAmountyear3 = 0;
    if (bgt_prov === "Approved") {
        totalAmount = parseFloat($('#total_mat').val()) || 0;
        totalAmountyear1 = parseFloat($('#total_mat_mat').val()) || 0;
        totalAmountyear2 = parseFloat($('#total_mat_mat2').val()) || 0;
        totalAmountyear3 = parseFloat($('#total_mat_mat3').val()) || 0;
    } else {
        totalAmount = parseFloat($('#total_mat_mat').val()) || 0;
    }

    var budgetAvailable = parseFloat($('#budget_avl').val().replace(/[^\d.-]/g, "")) || 0;
    var totalbudget = parseFloat($('#total_budget_both').val().replace(/[^\d.-]/g, "")) || 0;
    var add_budget = parseFloat($('#add_budget').val()) || 0;

    $('.error-message').remove();

    var fileInput = $("#materialboq")[0].files[0];
    if (!fileInput) {
        swal({
            title: "Error!",
            text: "Please select a file to upload.",
            icon: "error",
            button: "OK",
        });
        return;
    }

    var nv_id = $('#nv_id').val();
    var service_id = $('#service_id').val();

    var formData = new FormData();
    formData.append("materialboq", fileInput);
    formData.append("nv_id", nv_id);
    formData.append("service_id", service_id);
    formData.append("selectedYear", selectedYear);

    $.ajax({
        url: "/admin/nv_material/bulkProviderStore",
        type: "POST",
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function (response) {
            var rows = response.data.rows || [];
            var totalAmountboq = 0, totalAmountboq1 = 0, totalAmountboq2 = 0, totalAmountboq3 = 0;

            rows.forEach(function (row, i) {
                var rate = parseFloat(row.rate) || 0;

                // Safely calculate amount(s)
                var amount = parseFloat(row.amount) || 0;
                totalAmountboq += amount;

                if (bgt_prov === "Approved") {
                    var amount1 = (
                        (parseFloat(row.april1) || 0) + (parseFloat(row.may1) || 0) + (parseFloat(row.june1) || 0) +
                        (parseFloat(row.july1) || 0) + (parseFloat(row.august1) || 0) + (parseFloat(row.september1) || 0) +
                        (parseFloat(row.oct1) || 0) + (parseFloat(row.nov1) || 0) + (parseFloat(row.dec1) || 0) +
                        (parseFloat(row.jan1) || 0) + (parseFloat(row.feb1) || 0) + (parseFloat(row.march1) || 0)
                    ) * rate;

                    var amount2 = (
                        (parseFloat(row.april2) || 0) + (parseFloat(row.may2) || 0) + (parseFloat(row.june2) || 0) +
                        (parseFloat(row.july2) || 0) + (parseFloat(row.august2) || 0) + (parseFloat(row.september2) || 0) +
                        (parseFloat(row.oct2) || 0) + (parseFloat(row.nov2) || 0) + (parseFloat(row.dec2) || 0) +
                        (parseFloat(row.jan2) || 0) + (parseFloat(row.feb2) || 0) + (parseFloat(row.march2) || 0)
                    ) * rate;

                    var amount3 = (
                        (parseFloat(row.april3) || 0) + (parseFloat(row.may3) || 0) + (parseFloat(row.june3) || 0) +
                        (parseFloat(row.july3) || 0) + (parseFloat(row.august3) || 0) + (parseFloat(row.september3) || 0) +
                        (parseFloat(row.oct3) || 0) + (parseFloat(row.nov3) || 0) + (parseFloat(row.dec3) || 0) +
                        (parseFloat(row.jan3) || 0) + (parseFloat(row.feb3) || 0) + (parseFloat(row.march3) || 0)
                    ) * rate;

                    totalAmountboq1 += amount1;
                    totalAmountboq2 += amount2;
                    totalAmountboq3 += amount3;
                }
            });

            swal({
                title: "Success!",
                text: "Your file has been imported.",
                icon: "success",
                button: "OK",
            }).then(function () {
                fetchData();
				window.location.reload();

            });

            $('#materialboq').val('');

            // Update totals
            var total22 = totalAmount + totalAmountboq;
            var total23 = totalbudget + totalAmountboq;

            if (bgt_prov === "Approved") {
                $('#total_mat').val(totalAmountyear1 + totalAmountboq1);
                $('#total_mat_mat').val(totalAmountyear1 + totalAmountboq1);
                $('#total_mat_mat2').val(totalAmountyear2 + totalAmountboq2);
                $('#total_mat_mat3').val(totalAmountyear3 + totalAmountboq3);
            } else {
                $('#total_mat_mat').val(total22);
            }

            $('#total_budget_both').val(total23);
            $('#ser_budget_avl').val(budgetAvailable - total22);
        },
        error: function (xhr) {
            var message = "Something went wrong. Please try again later.";

            if (xhr.responseJSON) {
                if (xhr.responseJSON.ValidationError) {
                    message = xhr.responseJSON.ValidationError;
                    if (xhr.responseJSON.invalid_rows && xhr.responseJSON.invalid_rows.length > 0) {
                        message += "\n\nInvalid rate_reference values found:\n";
                        xhr.responseJSON.invalid_rows.forEach(function (row, index) {
                            message += (index + 1) + ". Material Code: " + row.material_code +
                                ", Provided Value: " + row.invalid_value + "\n";
                        });
                    }
                } else if (xhr.responseJSON.UploadError) {
                    message = xhr.responseJSON.UploadError;
                }
            }

            swal({
                title: "Error!",
                text: message,
                icon: "error",
                button: "OK",
            });
        }
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
				var table = $('#nvmaterial_serviceBoq_datatable').DataTable();
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
		// var ttlamt_ser= $('#total_ser_amo').val();
		// ttlamt_ser = ttlamt_ser.toLocaleString('en-IN');
		var formData = new FormData();
		formData.append("serviceboq", fileInput);
		formData.append("nv_id", nv_id);
		formData.append("service_id", service_id);

		var totalAmountService = $('#total_ser_amo').val();
		// totalAmountService = totalAmountService.replace(/[^a-zA-Z0-9_ ]/g, "");
		// totalAmountService = parseFloat(totalAmountService);

		var total_budget = $('#total_budget_both').val();
		total_budget = total_budget.replace(/[^a-zA-Z0-9_ ]/g, "");
		total_budget = parseFloat(total_budget);

		var totalbudgetservice = $('#total_budget_service').val();
		totalbudgetservice = totalbudgetservice.replace(/[^a-zA-Z0-9_ ]/g, "");
		totalbudgetservice = parseFloat(totalbudgetservice);

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
				});
			    $('#serviceboq').val('');
				
				var total22_ser =parseFloat(totalAmountService)+parseFloat(totalAmountboq);
				// total22_ser =total22_ser.toLocaleString('en-IN');
				$('#total_ser_amo').val(total22_ser);
				var total23_ser =parseFloat(total_budget)+parseFloat(totalAmountboq);
				// total23_ser =total23_ser.toLocaleString('en-IN');
				$('#total_budget_both').val(total23_ser);

				var totalbdgtser =parseFloat(totalbudgetservice)+parseFloat(totalAmountboq);
				// totalbdgtser =totalbdgtser.toLocaleString('en-IN');
				$('#total_budget_service').val(totalbdgtser);
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
	var bgt_prov = $('#bgt_prov').val();
	var selectedYear = parseInt($("#exampleFormControlSelect").val()) || 0;

	if (selectedYear == 0) {
	$('.select_year_error').html('Please Select Year to Proceed');
	$('#material_trend_year31')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
	return;
	}else{
	$('.select_year_error').html('');
    }

	if(bgt_prov == "Approved"){ 
	var totalAmount = $('#total_mat').val() || 0;
	var totalAmountyear1 = $('#total_mat_mat').val() || 0;
	var totalAmountyear2 = $('#total_mat_mat2').val() || 0;
	var totalAmountyear3 = $('#total_mat_mat3').val() || 0;
	}else{
		var totalAmount = $('#total_mat_mat').val() || 0;
	}
	// totalAmount = totalAmount.replace(/[^a-zA-Z0-9_ ]/g, "");
	// totalAmount = parseInt(totalAmount);
    var budgetAvailable = $('#budget_avl').val() || 0;
	budgetAvailable = budgetAvailable.replace(/[^a-zA-Z0-9_ ]/g, "");
	budgetAvailable = parseInt(budgetAvailable);
	var add_budget = parseFloat($('#add_budget').val()) || 0;

   
    $('.error-message').remove();
	// if (bgt_prov=="Approved") {
	// 	if (totalAmount > (budgetAvailable + add_budget)) {
	// 		var errorMessage = "Total Amount is greater than Budget Available. Please adjust the values.";
	// 		var errorElement = $('<p class="error-message">' + errorMessage + '</p>');
	// 		$('#budget_avl').after(errorElement);
	
		   
	// 		var scrollTo = errorElement.offset().top - 100; 
	// 		$('html, body').animate({
	// 			scrollTop: scrollTo
	// 		}, 500);
	
	// 		return; 
	// 	}
	// }
   
	
	

	var nv_id = $('#nv_id').val();
	var service_id = $('#service_id').val();
	var material_code = $('#material_code_0').val();
	var mat_des = $('#mat_des_0').val();
	var uom_0 = $('#uom_0').val();
	var rate = $('#rate').val();
	var quantity = $('#quantity').val();
	var total_amount = $('#total_amount').val()||0;
	var budget_avl = $('#budget_avl').val() || 0;
	budget_avl = budget_avl.replace(/[^a-zA-Z0-9_ ]/g, "");
	var tot_bud_both = $('#total_budget_both').val() || 0;
	tot_bud_both = tot_bud_both.replace(/[^a-zA-Z0-9_ ]/g, "");

	if(bgt_prov == "Approved"){ 
    var ttlamt= $('#total_mat').val() || 0;
	}else{
		var ttlamt= $('#total_mat_mat').val() || 0;

	}
	// ttlamt = ttlamt.replace(/[^a-zA-Z0-9_ ]/g, "");
	
	
	// alert(ttlamt);
	
	// $('#total_mat_mat').val(ttlamt);

	var formData = new FormData();
	//  formData.append("material_boq_save", fileInput);
	formData.append("nv_id", nv_id);
	formData.append("service_id", service_id);
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
			// alert(total_amount);
			
			if (res.result == 'success') {
			swal({
				title: "Success!",
				text: "Your Material BOQ is Successfully Saved.",
				icon: "success",
				button: "OK",
			}).then(function () {
			
				fetchDatamaterialsave();
				window.location.reload();
			});
				//$('#materialboqform')[0].reset();
				$('#material_code_0').val('');
				$('#rate').val('');
				$('#quantity').val('');
				var total = parseInt(ttlamt) + parseInt(total_amount);
				// total = total.toLocaleString('en-IN');
				if(bgt_prov == "Approved"){ 
				$('#total_mat').val(total)
				
				$('#total_mat_mat').val(totalAmountyear1)
				$('#total_mat_mat2').val(totalAmountyear2)
				$('#total_mat_mat3').val(totalAmountyear3)
				}else{
					$('#total_mat_mat').val(total)
				}
				var budget = parseInt(budget_avl) - parseInt(total);
				// budget = budget.toLocaleString('en-IN');
				$('#ser_budget_avl').val(budget);
				var totalbudgt = parseInt(tot_bud_both)+parseInt(rate*quantity);
				// totalbudgt = totalbudgt.toLocaleString('en-IN');
				$('#total_budget_both').val(totalbudgt);

				// var tot1 = parseInt(tot_amt) + parseInt(total_amount);
				// tot1 = tot1.toLocaleString('en-IN')
			
			
				
		
				
				
			
				var tot_amt = $('#total_budget_both').val();
				tot_amt = tot_amt.replace(/[^a-zA-Z0-9_ ]/g, "");
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
			var table = $('#nvmaterial_serviceBoq_datatable').DataTable();
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
	var service_id = $('#service_id').val();
	var service_code_0 = $('#service_code_0').val();
	var ser_des_0 = $('#ser_des_0').val();
	var ser_uom_0 = $('#ser_uom_0').val();
	var ser_rate = $('#ser_rate').val();
	var ser_quantity = $('#ser_quantity').val();
	var ser_total_amount = $('#ser_total_amount').val();
	var ttlamt_ser= $('#total_ser_amo').val();
	var ttlamt_ser1 = $('#total_budget_service').val();
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
		// alert(ser_total_amount);
		
			if (res.result == 'success') {
				Swal({
					title: "Success!",
					text: "Service BOQ is Successfully Saved.",
					icon: "success",
					button: "OK",
			}).then(function () {
				fetchDataservicesave();
			});
			// ttlamt_ser = ttlamt_ser.replace(/[^a-zA-Z0-9_ ]/g, "");
	ttlamt_ser= parseFloat(ttlamt_ser)+parseFloat(ser_total_amount);
	// ttlamt_ser = ttlamt_ser.toLocaleString('en-IN');
	 var tot_amt = $('#total_budget_both').val();
	// tot_amt = tot_amt.replace(/[^a-zA-Z0-9_ ]/g, "");
	var tot1 = parseFloat(tot_amt) + parseFloat(ser_total_amount);
	// tot1 = tot1.toLocaleString('en-IN');
	//  alert(ser_total_amount);
	$('#total_budget_both').val(tot1);
	$('#total_ser_amo').val(ttlamt_ser);
	$('#total_budget_service').val(ttlamt_ser);
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
}

});
function edit_mat(id) {
    if (!id) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Request',
            text: 'Material ID is missing'
        });
        return;
    }

    var selectedYear = Number($("#exampleFormControlSelect").val());

    if (!selectedYear) {
        Swal.fire({
            icon: 'warning',
            title: 'Year Required',
            text: 'Please select number of years'
        });
        return;
    }

    var selectedDate = $("#imp_from").val();

    if (!selectedDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Date Required',
            text: 'Please select implementation date'
        });
        return;
    }


    // Handle date safely (YYYY-MM-DD expected)
    var parts = selectedDate.split('-');
    if (parts.length !== 3) {
        alert('Invalid date format');
        return;
    }

    var selectedDateYear = parseInt(parts[0]);
    console.log('Base Year:', selectedDateYear);

    let years = [];

    for (let i = 0; i < selectedYear; i++) {
        years.push(selectedDateYear + i);
    }

    var yearStr = years.join('-');
    console.log('Year String:', yearStr);

    window.location.href = `/admin/nv_material/edit_material/${id}/${yearStr}`;
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
											<input type="text" class="form-control decimal" id="service_amount'+x+'" name="service_amount[]" placeholder="Amount" onchange="store_data(service_amount'+x+',service_description'+x+')">\
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


$(document).ready(function () {

    var max_fields = 10;
    var wrapper = $(".my-class-scheme_material");
    var add_button = $(".add-more-button_material");
    var x = 1;

    // ADD NEW ROW
    $(add_button).click(function (e) {
        e.preventDefault();

        if (x < max_fields) {
            x++;

            $(wrapper).append(`
                <div class="material-row">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                            <div class="form-group">
                                <input type="text" 
                                       class="form-control decimal" 
                                       id="material_amount${x}" 
                                       name="material_amount[]" 
                                       placeholder="Amount"
                                       onchange="storedata('material_amount${x}','material_description${x}')">
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-2">
                            <div class="form-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="material_description${x}" 
                                       name="material_description[]" 
                                       placeholder="Description">
                            </div>
                        </div>

                        <label>&nbsp;</label>
                        <a class="btn btn-success remove" 
                           style="height:37px;width:130px;border:1px solid #316598;"
                           onclick="remove_mat_data('material_amount${x}')">
                           - Remove
                        </a>
                    </div>
                </div>
            `);
        }
    });

    // REMOVE ROW
    $(wrapper).on("click", ".remove", function (e) {
        e.preventDefault();
        $(this).closest('.material-row').remove();
        x--;
    });

    // Prevent Enter key
    $(wrapper).on("keydown", "input[type='text']", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
        }
    });
});



// DECIMAL VALIDATION (WORKS FOR DYNAMIC INPUTS)
$(document).on('input', '.decimal', function () {
    let v = $(this).val();

    v = v.replace(/[^0-9.]/g, '');

    let parts = v.split('.');
    if (parts.length > 2) {
        v = parts[0] + '.' + parts[1];
    }

    if (parts[1] !== undefined) {
        parts[1] = parts[1].substring(0, 2);
        v = parts[0] + '.' + parts[1];
    }

    $(this).val(v);
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
									<label for="">&nbsp;</label><a class=" btn btn-success remove" style="height:37px;width:130px;border: 1px solid #316598;">- Remove</a>\
								</div>\
							</div>');
			
		}
		
	

	});

	$(wrapper).on("click", ".remove", function (e) { //user click on remove text
		e.preventDefault(); $(this).parent('div').remove(); x--;
	})


	
});


// $('#create_nv_material').on('keyup keypress', function(e) {
// 	var keyCode = e.keyCode || e.which;
// 	if (keyCode === 13) { 
// 	  e.preventDefault();
// 	  return false;
// 	}
//   });

// document.getElementById('dop').addEventListener('keydown', function(event) {
// 	if (event.key === 'Enter') {
// 	  event.preventDefault();
// 	}
//   });
//   document.getElementById('quantity').addEventListener('keydown', function(event) {
// 	if (event.key === 'Enter') {
// 	  event.preventDefault();
// 	}
//   });
['dop', 'quantity','cost_trend_year1','material_trend_year1','past_3_year_actual_cost1',
'past_3_year_actual_cost_service1','scheme_no','prop_number','derc_ref_no','material_code_0',
'material_descripition','ptr_mva','dt_mva','ehv_line','ht_line','lt_line','past_practice_follow',
'service_description','cost_trend_year2','material_trend_year2','cost_trend_year31','material_trend_year31',
'past_3_year_actual_cost2','past_3_year_actual_cost_service2','past_3_year_actual_cost3','past_3_year_actual_cost_service3'].forEach(function(id) {
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
	
	})

	$("#derc_app_date").change(function(){
		var valu = $("#derc_app_date").val();
		if(valu != "")
		{
			$(".app_date_error").html('');
		}
	
	})

 })

 document.getElementById('approved_budget').addEventListener('input', function (e) {
    const value = e.target.value;
    
    const regex = /^\d+(\.\d{0,2})?$/;

    if (!regex.test(value)) {
        e.target.value = value.slice(0, -1); 
    }
});
 
$(document).ready(function() {
    sumSolarCap_2();
});


$(document).on('input', '.decimal', function () {
    let v = $(this).val();

    // remove invalid characters
    v = v.replace(/[^0-9.]/g, '');

    // allow only one dot
    let parts = v.split('.');
    if (parts.length > 2) {
        v = parts[0] + '.' + parts[1];
    }

    // limit to 2 decimal places
    if (parts[1] !== undefined) {
        parts[1] = parts[1].substring(0, 2);
        v = parts[0] + '.' + parts[1];
    }

    $(this).val(v);
});



