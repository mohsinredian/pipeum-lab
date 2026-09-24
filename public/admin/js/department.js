$(document).ready(function(){
	
	// Datatable
	if($(document).find('#department_datatable').length > 0){
		$('#department_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/department'
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
				// {
				// 	data: 'prefix',
				// 	name: 'prefix',
				// 	className: "text-left"
				// },
				{
					data: 'name',
					name: 'name',
					className: "text-left"
				},
				
				
				{
					data: 'dep_rew1',
					name: 'dep_rew1',
					className: "text-left"
				},
				{
					data: 'dep_rew2',
					name: 'dep_rew2',
					className: "text-left"
				},
				{
					data: 'dep_rew3',
					name: 'dep_rew3',
					className: "text-left"
				},
				{
					data: 'dep_rew4',
					name: 'dep_rew4',
					className: "text-left"
				},
				{
					data: 'dep_hod',
					name: 'dep_hod',
					className: "text-left"
				},
				{
					data: 'group_cio',
					name: 'group_cio',
					className: "text-left"
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
	// Create role
	$('#create_department_form, #edit_department_form').validate({
		rules: {
			name: {
				required: true,
			},
			dep_hod: {
				required: true,
			},
			status: {
				required: true,
			},
			prefix: {
				required: true,
			},
			
		},
		messages: {
			name: {
				required: "Please enter Department name",
				
			},
			dep_hod: {
				required: "Please Select Department Hod",
				
			},
			status: {
				required: 'Please select status',
			},
			prefix: {
				required: 'Please enter prefix',
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

	$('#create_department_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#create_department_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#create_department_form')[0]),
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/department/store",
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Department Created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/department';
						});
						
						 $('#create_department_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_department_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	$('#edit_department_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_department_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_department_form')[0]),
				type: 'post',
				url: "/admin/department/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Department Updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "OK",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/department';
						});
						
						$('#edit_department_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_department_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
});

$(document).ready(function() {
    var allDropdowns = $('#dep_rew1, #dep_rew2, #dep_rew3, #dep_rew4, #dep_hod, #group_cio');
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

// $(document).ready(function() {
//     var allDropdowns = $('#dep_rew1, #dep_rew2, #dep_rew3, #dep_rew4, #dep_hod, #group_cio');
//     var previousValues = {};

//     allDropdowns.change(function() {
//         var selectedValue = $(this).val();
//         var dropdownId = $(this).attr('id');

//         // Enable the previously selected value in other dropdowns
//         if (previousValues[dropdownId] !== undefined) {
//             allDropdowns.not(this).find('option[value="' + previousValues[dropdownId] + '"]').prop('disabled', false);
//         }

//         // Disable the current selected value in other dropdowns
//         allDropdowns.not(this).find('option[value="' + selectedValue + '"]').not('[value=""]').prop('disabled', true);

//         // Update the previous value for this dropdown
//         previousValues[dropdownId] = selectedValue;
//     });
// });

  