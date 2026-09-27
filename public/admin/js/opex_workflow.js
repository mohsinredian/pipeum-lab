$(document).ready(function(){
	
	// Datatable
	if($(document).find('#workflow_datatable').length > 0){
		// alert('hii');
		$('#workflow_datatable').DataTable({
			
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/opex_Workflow'
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
					data: 'work_dep',
					name: 'work_dep',
					className: "text-center"
				},
				{
					data: 'work_rew1',
					name: 'work_rew1',
					className: "text-center"
				},
				{
					data: 'work_rew2',
					name: 'work_rew2',
					className: "text-center"
				},
				{
					data: 'work_rew3',
					name: 'work_rew3',
					className: "text-center"
				},
				{
					data: 'work_rew4',
					name: 'work_rew4',
					className: "text-center"
				},
				{
					data: 'approver',
					name: 'approver',
					className: "text-center"
				},
				{
					data: 'sr_no',
					name: 'sr_no',
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
			'columnDefs': [
				{ 'orderable': false, 'targets': '_all' }, 
				{'visible': false, 'targets': [1], 'orderable': true} 
			]
			
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
	$('#create_workflow_form,#edit_workflow_form').validate({
		rules: {
			work_dep: {
				required: true,
				
			},
			status: {
				required: true,
				
			},
			sr_no: {
				required: true,
				
			},
			
	// 		work_rew1: {
    //                required: true,
                
                  
	// 		},
    //         work_rew2: {
	// 			required: true,
				
	// 		},
			
	// 		work_rew3: {
    //                required: true,
                
                  
	// 		},
	// 		work_rew4: {
	// 			required: true,
				
			   
	// 	 },
	// 	 approver: {
	// 		required: true,
			
		   
	//  },
           
			
		},
		messages: {
            work_dep: {
				required: "Please select department",
			
			},
			status: {
				required: "Please select status",
			
			},
			sr_no: {
				required: "Please enter Sr No",
			
			},
			// work_rew1: {
			// 	  required: "Please enter reviewer1",
				
			
			// },

            // work_rew2: {
			// 	required: "Please enter reviewer2",
			
			// },
			
			// work_rew3: {
			// 	  required: "Please enter reviewer3",
				
			
			// },
            // work_rew4: {
			// 	required: "Please enter reviewer4",
			
			// },
			
			// approver: {
			// 	  required: "Please enter approver",
		
			
			// },
          
		
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

	$('#create_workflow_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		let status = $('#status').val();
		let approver = $('#approver').val();
		let isValid = true;

		if (status == 0) {
			// Clear all reviewer fields if status is Inactive
			$('#work_rew1, #work_rew2, #work_rew3, #work_rew4, #approver').val('');
		}

		if (status == 1) {
			// Make approver mandatory
			if (approver === '') {
				Swal.fire({
					text: 'Approver is required when status is active.',
					type: 'error',
					buttonsStyling: false,
					confirmButtonText: "OK",
					confirmButtonClass: "btn font-weight-bold btn-light"
				});
				isValid = false;
			}
		}

		if($('#create_workflow_form').valid() && isValid){

		// if($('#create_workflow_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#create_workflow_form')[0]),
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/opex_Workflow/store",
				success: function(response){
					var res = response;
					if (res.exists) {
						Swal.fire({
							text: res.message,
							type: 'error',
							buttonsStyling: false,
							confirmButtonText: "OK",
							confirmButtonClass: "btn font-weight-bold btn-light"
						});
					}
					else if(res.result == 'success'){
						
						Swal.fire({
			                text: "OPEX Workflow Saved Successfully",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/opex_Workflow';
						});
						
						 $('#create_workflow_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_workflow_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	$('#edit_workflow_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();
		
		let status = $('#status').val();
		let approver = $('#approver').val();
		let isValid = true;

		if (status == 0) {
			// Clear all reviewer fields if status is Inactive
			$('#work_rew1, #work_rew2, #work_rew3, #work_rew4, #approver').val('');
		}

		if (status == 1) {
			// Make approver mandatory
			if (approver === '') {
				Swal.fire({
					text: 'Approver is required when status is active.',
					type: 'error',
					buttonsStyling: false,
					confirmButtonText: "OK",
					confirmButtonClass: "btn font-weight-bold btn-light"
				});
				isValid = false;
			}
		}

		if($('#edit_workflow_form').valid() && isValid){

		// if($('#edit_workflow_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_workflow_form')[0]),
				type: 'post',
				url: "/admin/opex_Workflow/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if (res.exists) {
						Swal.fire({
							text: res.message,
							type: 'error',
							buttonsStyling: false,
							confirmButtonText: "OK",
							confirmButtonClass: "btn font-weight-bold btn-light"
						});
					}
					else if(res.result == 'success'){
						
						Swal.fire({
			                text: "OPEX Workflow Updated Successfully",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/opex_Workflow';
						});
						
						$('#edit_workflow_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_workflow_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	
	$(document).on('click', '.delete_department', function () {

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

				let department_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'department_id': department_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/department/delete/'+department_id,

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

	$('#work_dep').on('change', function () {
		var dep_id = $(this).val();
	   // console.log('dep_id')
		   $('.pre-loader').show();
		   
		   $.ajax({
			   data: {
				   dep_id: dep_id,
				   _token: $('input[name="_token"]').val()
			   },
			   type: 'post',
			   url: '/admin/opex_Workflow/changeUser',
			   async: false,
			   success: function (response) {
				   if (response.result == 'success') {
					   let locations = response.data;
					   
			     $("#work_rew1").empty();
				 $("#work_rew2").empty();
				 $("#work_rew3").empty();
				 $("#work_rew4").empty();
				 $("#approver").empty();
					 
				   $("#work_rew1").append(' <option value="">Select Reviewer 1 </option>');
				   $("#work_rew2").append(' <option value="">Select Reviewer 2 </option>');
				   $("#work_rew3").append(' <option value="">Select Reviewer 3 </option>');
				   $("#work_rew4").append(' <option value="">Select Reviewer 4 </option>');
				   $("#approver").append(' <option value="">Select Approver </option>');
   
					   $.each(locations, function (i, work_rew1) {
						   $("#work_rew1").append(
							   $("<option></option>")
								   .attr("value", work_rew1.user_id)
								   .text(work_rew1.name)
						   );
						 })
						 $.each(locations, function (i, work_rew2) {
						   $("#work_rew2").append(
							   $("<option></option>")
								   .attr("value", work_rew2.user_id)
								   .text(work_rew2.name)
						   );
						 })
						 $.each(locations, function (i, work_rew3) {
						   $("#work_rew3").append(
							   $("<option></option>")
								   .attr("value", work_rew3.user_id)
								   .text(work_rew3.name)
						   );
						 })
						 $.each(locations, function (i, work_rew4) {
						   $("#work_rew4").append(
							   $("<option></option>")
								   .attr("value", work_rew4.user_id)
								   .text(work_rew4.name)
						   );
						 })
						 $.each(locations, function (i, approver) {
						   $("#approver").append(
							   $("<option></option>")
								   .attr("value", approver.user_id)
								   .text(approver.name)
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

	   var rew1= $('#dep_work_rew1').val();
	   var rew2= $('#dep_work_rew2').val();
	   var rew3= $('#dep_work_rew3').val();
	   var rew4= $('#dep_work_rew4').val();
	   var app= $('#dep_work_app').val();
	divi();
	function divi() {
		var dep_id = $('#work_dep').val();
	// console.log('work_dep')
		$('.pre-loader').show();
		
		$.ajax({
			data: {
				dep_id: dep_id,
				_token: $('input[name="_token"]').val()
			},
			type: 'post',
			url: '/admin/opex_Workflow/changeUser',
			async: false,
			success: function (response) {
				if (response.result == 'success') {
					let locations = response.data;
					
					$("#work_rew1").empty();
					$("#work_rew2").empty();
					$("#work_rew3").empty();
					$("#work_rew4").empty();
					$("#approver").empty();
						
					  $("#work_rew1").append(' <option value="">Select Reviewer 1 </option>');
					  $("#work_rew2").append(' <option value="">Select Reviewer 2 </option>');
					  $("#work_rew3").append(' <option value="">Select Reviewer 3 </option>');
					  $("#work_rew4").append(' <option value="">Select Reviewer 4 </option>');
					  $("#approver").append(' <option value="">Select Approver </option>');
					
                    $.each(locations, function (i, work_rew1) {
						if(work_rew1.user_id==rew1){
							$("#work_rew1").append(
								$("<option selected></option>")
									.attr("value", rew1)
									.text(work_rew1.name)
							);
						}else {
                        $("#work_rew1").append(
                            $("<option></option>")
                                .attr("value", work_rew1.user_id)
                                .text(work_rew1.name)
                        );
						}
                    });

					$.each(locations, function (i, work_rew2) {
						if(work_rew2.user_id==rew2){
							$("#work_rew2").append(
								$("<option selected></option>")
									.attr("value", rew2)
									.text(work_rew2.name)
							);
						}else {
                        $("#work_rew2").append(
                            $("<option></option>")
                                .attr("value", work_rew2.user_id)
                                .text(work_rew2.name)
                        );
						}
                    });

					$.each(locations, function (i, work_rew3) {
						if(work_rew3.user_id==rew3){
							$("#work_rew3").append(
								$("<option selected></option>")
									.attr("value", rew3)
									.text(work_rew3.name)
							);
						}else {
                        $("#work_rew3").append(
                            $("<option></option>")
                                .attr("value", work_rew3.user_id)
                                .text(work_rew3.name)
                        );
						}
                    });

					$.each(locations, function (i, work_rew4) {
						if(work_rew4.user_id==rew4){
							$("#work_rew4").append(
								$("<option selected></option>")
									.attr("value", rew4)
									.text(work_rew4.name)
							);
						}else {
                        $("#work_rew4").append(
                            $("<option></option>")
                                .attr("value", work_rew4.user_id)
                                .text(work_rew4.name)
                        );
						}
                    });

					$.each(locations, function (i, approver) {
						if(approver.user_id==app){
							$("#approver").append(
								$("<option selected></option>")
									.attr("value", app)
									.text(approver.name)
							);
						}else {
                        $("#approver").append(
                            $("<option></option>")
                                .attr("value", approver.user_id)
                                .text(approver.name)
                        );
						}
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
	};
});

//disable drop down values
// $(document).ready(function() {
// 	var allDropdowns = $('#work_rew1, #work_rew2, #work_rew3, #work_rew4, #approver');
// 	var previousValues = {};
  
// 	allDropdowns.change(function() {
// 	  var selectedValue = $(this).val();
// 	  var dropdownId = $(this).attr('id');
  
// 	  // Enable the previously selected value in other dropdowns
// 	  if (previousValues[dropdownId] !== undefined) {
// 		allDropdowns.not(this).find('option[value="' + previousValues[dropdownId] + '"]').prop('disabled', false);
// 	  }
  
// 	  // Disable the current selected value in other dropdowns
// 	  allDropdowns.not(this).find('option[value="' + selectedValue + '"]').prop('disabled', true);
  
// 	  // Update the previous value for this dropdown
// 	  previousValues[dropdownId] = selectedValue;
// 	});
//   });
$(document).ready(function() {
    var allDropdowns = $('#work_rew1, #work_rew2, #work_rew3, #work_rew4, #approver');
    var previousValues = {};

    allDropdowns.change(function() {
        var selectedValue = $(this).val();
        var dropdownId = $(this).attr('id');

        // Enable the previously selected value in other dropdowns
        if (previousValues[dropdownId] !== undefined) {
            allDropdowns.not(this).find('option[value="' + previousValues[dropdownId] + '"]').prop('disabled', false);
        }

        // Disable the current selected value in other dropdowns
        allDropdowns.not(this).find('option[value="' + selectedValue + '"]').not('[value=""]').prop('disabled', true);

        // Update the previous value for this dropdown
        previousValues[dropdownId] = selectedValue;
    });

    // On page load, disable selected values in other dropdowns
    allDropdowns.each(function() {
        var selectedValue = $(this).val();
        var dropdownId = $(this).attr('id');

        allDropdowns.not(this).find('option[value="' + selectedValue + '"]').not('[value=""]').prop('disabled', true);
        previousValues[dropdownId] = selectedValue;
    });
});
