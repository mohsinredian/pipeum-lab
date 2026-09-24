$(document).ready(function () {

	// Datatable
	if ($(document).find('#employee_datatable').length > 0) {
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
	$('#create_nv_material, #preview_nvmaterial').validate({
		rules: {
			dept_id: {
				required: true,
				// maxlength: 50,
				// 
			},
			dop: {
				required: true,
				maxlength: 50,
				number:true,
			
				
		
			},
			budget_avl: {
				required: true,
				maxlength: 100,

			},
			remark: {
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
			dop: {
				required: "Please enter dop reference number",
				maxlength: "DOP reference number not be more than 50 characters",
				number: "DOP ref no should be numbers only",
				 
			},
			budget_avl: {
				required: "Please enter budget available",
			},
			remark: {
				required: "Please enter your remark",
				
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
		$('.common-error').empty();
		if ($('#create_nv_material').valid()) {

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#create_nv_material')[0]),
				cache: false,
				processData: false,
				contentType: false,
				type: 'post',
				url: "/admin/nv_material/store",
				success: function (response) {
					var res = response;
					if (res.result == 'success') {

						Swal.fire({
							text: "NV Material created.",
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
					url: '/admin/employees/delete/' + employee_id,

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


$(document).ready(function() {
    var max_fields      = 3; //maximum input boxes allowed
    var wrapper         = $(".my-class"); //Fields wrapper
    var add_button      = $(".add-more-third"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class=""> <div class="row "><div class="col-xl-3 col-lg-3 col-md-6 mt-2"><input type="text" class="form-control"name="past_3_year_actual_cost_fy[]"id="past_3_year_actual_cost_fy"value=""placeholder="FY"></div><div class="col-xl-3 col-lg-3 col-md-6 mt-2"><input type="text" class="form-control"name="past_3_year_actual_cost[]"id="past_3_year_actual_cost"placeholder="Cost"value=""></div><div class="col-xl-3 col-lg-3 col-md-6 mt-2"><input type="text" class="form-control"name="past_3_year_actual_cost_service[]"id="past_3_year_actual_cost_service"value=""placeholder="Services"></div><label for="">&nbsp;</label><a class=" btn btn-success mt-2 remove">- Remove</a></div></div>'); //add input box
        }
    });
    
    $(wrapper).on("click",".remove", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
}); 

$(document).ready(function() {
    var max_fields      = 3; //maximum input boxes allowed
    var wrapper         = $(".my-fields"); //Fields wrapper
    var add_button      = $(".add-more"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class=""> <div class="row "><div class="col-xl-3 col-lg-3 col-md-6 mt-2"><input type="text" class="form-control"name="cost_trend_year3[]"id="cost_trend_year3"value=""placeholder="FY"></div><div class="col-xl-3 col-lg-3 col-md-6 mt-2"><input type="text" class="form-control"name="cost_trend_year1[]"id="cost_trend_year1"placeholder="Cost"value=""></div><div class="col-xl-3 col-lg-3 col-md-6 mt-2"><input type="text" class="form-control"name="cost_trend_year2[]"id="cost_trend_year2"value=""placeholder="Material"></div><label for="">&nbsp;</label><a class=" btn btn-success mt-2 remove">- Remove</a></div></div>'); //add input box
        }
    });
    
    $(wrapper).on("click",".remove", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
}); 



$(document).ready(function() {
    // var max_fields      = 3; //maximum input boxes allowed
    var wrapper         = $(".my-fieldssss"); //Fields wrapper
    var add_button      = $(".add-more-second"); //Add button ID
    
    // var x = 1; //initlal text box count
	var count=1;
	var token= $('#tokennm').val();

	
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        // if(x < max_fields){ //max input box allowed
            // x++; //text box increment
            $(wrapper).append("<div class='container-fluid'> <div class='row'><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> Material Code </label><input type='text' class='form-control'name='material_code[]_"+count+"' id='material_code_"+count+"'value='' placeholder='Enter Material Code' onkeyup='autofetchMaterialCode_"+count+"()'></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> Material Description </label><input type='text' class='form-control'name='mat_des[]_"+count+"'id='mat_des_"+count+"'placeholder='Enter Material Description'value=''></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> Material Group </label><select class='form-control' id='mat_group' name='mat_group[]'><option value=''>Select Material Group</option><option value='DT'>DT</option><option value='HT Cable'>HT Cable</option> <option value='LT Cable'>LT Cable</option><option value='RMU'>RMU</option><option value='LT ACB'>LT ACB</option><option value='Pole'> Pole</option><option value='Joint Kit'> Joint Kit</option><option value='Feeder Pillar'> Feeder Pillar</option><option value='Miscellaneous' >Miscellaneous</option></select></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> UoM </label><input type='text' class='form-control'name='uom[]_"+count+"'id='uom_"+count+"'value=''placeholder='Enter Uom''></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> Rate </label><input type='text' class='form-control'name='rate[]'id='rate'value=''placeholder='Enter Rate''></div><div class='col-6 mb-2 mt-3'><label style='font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;'> Quantity Required </label><input type='text' class='form-control'name='quantity[]'id='quantity'value=''placeholder='Enter Quantity Required''></div><div class='col-6 mb-2 mt-3'><label  >Total Amount</label><input type='text' class='form-control'name='total_amount[]'id='total_amount'value=''placeholder='Enter Total Amount'></div><div class='col-6 mb-2 mt-3'><label  >Delivery Schedule</label><input type='text' class='form-control'name='delivery_schedule[]'id='delivery_schedule'value=''placeholder='Enter Delivery Schedule'></div><br><label for=''>&nbsp;</label><a class=' btn btn-success mt-3 remove'>- Remove</a></div></div> <script>     function autofetchMaterialCode_"+count+"() { var token = token;   var material_code = $('#material_code_"+count+"' ).val();  if (material_code.length >= 6) {$.ajax({url: '/admin/capexmaster/autofetch',type: 'post',dataType: 'json',data: {'token': token, 'key': material_code},success: function (data) { $('#mat_des_"+count+"').val(data.material_short_text);$('#uom_"+count+"').val(data.uom);}});}}; </script><br>"); 
        // }
		count++;
    });
    
    $(wrapper).on("click",".remove", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })

var material_id = window.location.pathname.split('/')[4];
var nv_id = $('#nv_id').val();
var material_id = $('#material_id').val();
// var remarkValue = $('textarea[name="remark"]').val();

$('.approve-button').click(function(){
	var status_id = $('.approve-button').val();
	var remark = $('textarea[name="remark"]').val();
	if($('#preview_nvmaterial').valid()){
	$('.pre-loader').show();
	
	$.ajax({
		data: {
			status_id: status_id,
			material_id : material_id,
			nv_id : nv_id,
			remark :remark,
			_token: $('input[name="_token"]').val()
		},
		type: 'get',
		url: '/admin/nv_material/approvedByStatus',
		async: false,
		success: function (response) {
			// alert(response.result);
			if (response.result == 'success') {
					Swal.fire({
			text: "Approved NV ",
			type: 'success',
			buttonsStyling: false,
			confirmButtonText: "Ok",
			confirmButtonClass: "btn font-weight-bold btn-primary"
		}).then(function() {
			window.location = '/admin/nv_material/preview/' + response.material_id;
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

$('.reject-button').click(function(){
	var status_id = $('.reject-button').val();
	var remark = $('textarea[name="remark"]').val();
	// alert(status_id);
	// return false;
	if($('#preview_nvmaterial').valid()){
	$('.pre-loader').show();
	$.ajax({
		data: {
			status_id: status_id,
			material_id : material_id,
			nv_id : nv_id,
			remark:remark,
			_token: $('input[name="_token"]').val()
		},
		type: 'get',
		url: '/admin/nv_material/approvedByStatus',
		async: false,
		success: function (response) {
			// alert(response.result);
			if (response.result == 'success') {
					Swal.fire({
			text: "Reject NV ",
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
	
}); 


$(document).ready(function() {
    // var max_fields      = 3; //maximum input boxes allowed
    var wrapper         = $(".my-field-four"); //Fields wrapper
    var add_button      = $(".add-more-four"); //Add button ID
    
    // var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        // if(x < max_fields){ //max input box allowed
            // x++; //text box increment
            $(wrapper).append(' <div class=""> <div class="row "><div class="col-6 mb-2 mt-3"><label style="font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;"> Service Code </label><input type="text" class="form-control"name="service_code[]"id="service_code"value=""placeholder="Enter Service Code"></div><div class="col-6 mb-2 mt-3"><label style="font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;"> Service Description </label><input type="text" class="form-control"name="ser_des[]"id="ser_des"placeholder="Enter Service Description"value=""></div><div class="col-6 mb-2 mt-3"><label style="font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;"> Rate Reference </label><input type="text" class="form-control"name="ser_rate_ref[]"id="ser_rate_ref"value=""placeholder="Enter Rate Reference""></div><div class="col-6 mb-2 mt-3"><label style="font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;">UoM</label><input type="text" class="form-control"name="ser_uom[]"id="ser_uom"value=""placeholder="Enter UoM""></div><div class="col-6 mb-2 mt-3"><label style="font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;">Rate</label><input type="text" class="form-control"name="ser_rate[]"id="ser_rate"value=""placeholder="Enter Rate""></div><div class="col-6 mb-2 mt-3"><label style="font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;">Quantity Required</label><input type="text" class="form-control"name="ser_quantity[]"id="ser_quantity"value=""placeholder="Enter Quantity Required""></div><div class="col-6 mb-2 mt-3"><label style="font-size:16px; font-weight:700; display:inline-block; margin-bottom:6px;">Total Amount</label><input type="text" class="form-control"name="ser_total_amount[]"id="ser_total_amount"value=""placeholder="Enter Total Amount"></div><div class="col-6 mb-2 mt-3"></div><br><label for="">&nbsp;</label><a class=" btn btn-success mt-3 remove">- Remove</a></div></div>'); //add input box
        // }
    });
    
    $(wrapper).on("click",".remove", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
	
}); 
