$(document).ready(function(){
	
	// Datatable
	if($(document).find('#complaint_datatable').length > 0){
		$('#complaint_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/complaints'
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
					data: 'department',
					name: 'department',
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
					data: 'asset',
					name: 'asset',
					className: "text-center"
				},
				{
					data: 'model',
					name: 'model',
					className: "text-center"
				},
				{
					data: 'serial_number',
					name: 'serial_number',
					className: "text-center"
				},
				{
					data: 'vendor',
					name: 'vendor',
					className: "text-center"
				},
				{
					data: 'sla',
					name: 'sla',
					className: "text-center"
				},
				{
					data: 'complaint_type',
					name: 'complaint_type',
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
	// Create role
	$('#create_complaint_form, #edit_complaint_form').validate({
		rules: {
			department: {
				required: true,
			},
			circle: {
				required:true,
			},
			location: {
					required:true,
			},
			asset: {
					required:true,
			},
			model: {
					required:true,
			},
			serial_number: {
					required:true,
			},
			vendor: {
					required:true,
			},
			sla: {
					required:true,
			},
			complaint_type: {
					required:true,
			},
			status: {
				required: true,
			},
			
		},
		messages: {
			department: {
				required: "Please select department ",
			},
			circle: {
				required: "Please select circle",
		    },
			location: {
				required: "Please select location",
            },
            asset: {
				required: "Please select asset",
            },
            model: {
				required: "Please enter model ",
            },
            serial_number: {
				required: "Please enter serial number",
            },
            vendor: {
				required: "Please enter vendor ",
            },
            sla: {
				required: "Please enter sla",
            },
            complaint_type: {
				required: "Please select complaint type",
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
 
//  	$("#serial number").change(function(){
//  		$.get("serial number", function('model','asset','sla'){
 			

//  		 });

//   alert("The text has been changed.");
// });



	$('#create_complaint_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#create_complaint_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#create_complaint_form')[0]),
				cache: false,
				processData: false,
    			contentType: false,
				type: 'post',
				url: "/admin/complaints/store",
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Complaint created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/complaints';
						});
						
						 $('#create_complaint_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_complaint_form').find('.'+key+'_error').html(error_msgs[key][0]);
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

	// Edit role
	$('#edit_complaint_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_complaint_form').valid()){

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_complaint_form')[0]),
				type: 'post',
				url: "/admin/complaints/update",
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Complaint updated.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/complaints';
						});
						
						$('#edit_complaint_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_complaint_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	
	$(document).on('click', '.delete_complaint', function () {

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

				let complaint_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'complaint_id':complaint_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/complaints/delete/'+complaint_id,

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