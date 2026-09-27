$(document).ready(function(){
	
	// Datatable
	if($(document).find('#floor_datatable').length > 0){
		$('#floor_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/floor'
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
					data: 'location_id',
					name: 'location_id',
					className: "text-center"
				},
				{
					data: 'floor_name',
					name: 'floor_name',
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
				},
				
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
	$('#create_floor_form, #edit_floor_form').validate({
		rules: {
			name: {
				required: true,
				maxlength: 50,
				alpha: true,
			},
			location_id: {
				required: true,
				maxlength: 10,
				alphanumeric:true,
			},
			status: {
				required: true,
			},
			
		},
		messages: {
			name: {
				required: "Please enter Floor name",
				//maxlength: "Division name should not be more than 50 characters",
				alpha: "Please enter valid input",
			},
			location_id: {
				required: "Please enter Location",
				//maxlength: "Short code should not be more than 10 characters",
				alphanumeric: "Please enter valid input",
			},
			status: {
				required: 'Please select status',
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

	$('#create_floor_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#create_floor_form').valid()){

			$('.pre-loader').show();
			
			$.ajax({
				data: new FormData($('#create_floor_form')[0]),
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/floor/store",  
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Floor created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/floor';
						});
						
						 $('#create_floor_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_floor_form').find('.'+key+'_error').html(error_msgs[key][0]);
							}
						}

					}
					else if(res.result == 'failure'){
						
						Swal.fire({
			                text: "Something went wrong. Please try again.",
			                type: 'error',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
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
	$('#edit_floor_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_floor_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_floor_form')[0]),
				type: 'post',
				url: "/admin/floor/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Floor updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/floor';
						});
						
						$('#edit_floor_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_floor_form').find('.'+key+'_error').html(error_msgs[key][0]);
							}
						}

					}
					else if(res.result == 'failure'){
						
						Swal.fire({
			                text: "Something went wrong. Please try again.",
			                type: 'error',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
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
	
	// $(document).on('click', '.delete_floor', function () {

	// 	Swal.fire({
	// 		title: 'Are you sure?',
	// 		// text: "You won't be able to revert this!",
	// 		type: 'warning',
	// 		showCancelButton: true,
	// 		confirmButtonColor: '#3085d6',
	// 		cancelButtonColor: '#d33',
	// 		confirmButtonText: 'Yes'
	// 	}).then((result) => {
	// 		if (result.value) {
	// 			$('.pre-loader').show();

	// 			let division_id = $(this).attr('data-id');

	// 			$.ajax({
	// 				data: {
	// 					'division_id': division_id,
	// 					'_token': $('input[name="_token"]').val()
	// 				},
	// 				type: 'DELETE',
	// 				url: '/admin/foor/delete/'+division_id,

	// 				success: function (response) {
	// 					let res = response;

	// 					if (res.result == 'success') {

	// 						Swal.fire({
	// 							text: res.msg,
	// 							type: "success",
	// 							buttonsStyling: false,
	// 							confirmButtonText: "Ok",
	// 							confirmButtonClass: "btn font-weight-bold btn-primary"
	// 						}).then(function () {
	// 							window.location.reload();
	// 						});
	// 					}
	// 					else if (res.result == 'failure') {

	// 						Swal.fire({
	// 							text: "Something went wrong. Please try again.",
	// 							type: "error",
	// 							buttonsStyling: false,
	// 							confirmButtonText: "Ok",
	// 							confirmButtonClass: "btn font-weight-bold btn-light"
	// 						}).then(function () {
	// 							window.location.reload();
	// 						});
	// 					}

	// 					$('.pre-loader').hide();
	// 				},

	// 				error: function (error) {

	// 				}
	// 			});
	// 		}
	// 	});
	// });
});