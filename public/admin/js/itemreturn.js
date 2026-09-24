$(document).ready(function(){
	
	// Datatable
	if($(document).find('#itemreturn_datatable').length > 0){
		$('#itemreturn_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/item-returns'
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
					data: 'serial_number',
					name: 'serial_number',
					className: "text-center"
				},
				{
					data: 'item_type',
					name: 'item_type',
					className: "text-center"
				},
				{
					data: 'circle',
					name: 'circle',
					className: "text-center"
				},
				{
					data: 'location',
					name: 'location',
					className: "text-center"
				},
				{
					data: 'return_location',
					name: 'return_location',
					className: "text-center"
				},
				{
					data: 'returned_by',
					name: 'returned_by',
					className: "text-center"
				},
				{
					data: 'remarks',
					name: 'remarks',
					className: "text-center"
				},
				{
					data: 'action',
					name: 'action',
					className: "text-center"
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
	$('#create_itemreturn_form, #edit_location_form').validate({
		rules: {
			serial_number: {
				required: true,
			},
			item_type: {
                   required: true,
			},
			circle: {
				required:true,
			},
			location: {
					required:true,
			},
			return_to_location: {
					required:true,
			},
			returned_by: {
					required:true,
			},
			remarks: {
					//required:true,
			},
			status: {
				required: true,
			},
			
		},
		messages: {
			serial_number: {
				required: "Please enter item serial",
			},
			item_type: {
				  required: "Please enter item type",
			},
			circle: {
				required: "Please enter circle",
		    },
			location: {
				required: "Please enter location",
            },
            return_to_location: {
				required: "Please enter return to location",
            },
            returned_by: {
				required: "Please enter returned by",
            },
            remarks: {
				required: "Please enter remarks",
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

	$('#create_itemreturn_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#create_itemreturn_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#create_itemreturn_form')[0]),
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/item-returns/store",
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Item Return Created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/item-returns';
						});
						
						 $('#create_itemreturn_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_itemreturn_form').find('.'+key+'_error').html(error_msgs[key][0]);
							}
						}

					}
					else if(res.result == 'fail'){
						
						Swal.fire({
			                text: res.msg,
			                type: 'error',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-light"
			            }).then(function() {
							window.location.reload();
						});
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
	$('#serial_number').on('change', function () {

		let serial_number = $(this).val();
		$('.pre-loader').show();
		$.ajax({
			data: {
				serial_number: serial_number,
				_token: $('input[name="_token"]').val()
			},
			type: 'post',
			url: '/admin/get_item_issues',
			async: false,
			success: function (response) {
				if (response.result == 'success') {
					let issues = response.data;
					if(issues != null){
						$('#item_type').val(issues.item_type);
						$('#circle').val(issues.circle_id);
						$('#location').val(issues.location_id);
					}else{
						$('#item_type').val('');
						$('#circle').val('');
						$('#location').val('');
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
	});
	// Edit role
	$('#edit_vendor_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_vendor_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_vendor_form')[0]),
				type: 'post',
				url: "/admin/vendors/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Vendor updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/vendors';
						});
						
						$('#edit_vendor_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_vendor_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	
	$(document).on('click', '.delete_vendor', function () {

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

				let vendor_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'vendor_id': vendor_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/vendors/delete/'+vendor_id,

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
});