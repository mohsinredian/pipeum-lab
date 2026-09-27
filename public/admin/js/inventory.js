$(document).ready(function(){
	
	// Datatable
	if($(document).find('#inventory_datatable').length > 0){
		$('#inventory_datatable').DataTable({
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/inventory'
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
					data: 'po_number',
					name: 'po_number',
					className: "text-center"
				},
				{
					data: 'serial_number',
					name: 'serial_number',
					className: "text-center"
				},
				{
					data: 'po_start',
					name: 'po_start',
					className: "text-center"
				},
				{
					data: 'po_start',
					name: 'po_start',
					data: 'po_expiry',
					name: 'po_expiry',
					className: "text-center"
				},
				{
					data: 'model_number',
					name: 'model_number',
					className: "text-center"
				},
				{
					data: 'brand',
					name: 'brand',
					className: "text-center"
				},
				{
					data: 'item_type',
					name: 'item_type',
					className: "text-center"
				},
				{
					data: 'item_qty',
					name: 'item_qty',
					className: "text-center"
				},
				{
					data: 'vendor_name',
					name: 'vendor_name',
					className: "text-center"
				},
				{
					data: 'vendor_contact',
					name: 'vendor_contact',
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
	jQuery.validator.addMethod(
        "separated_serial_number",
        function (value, element) {
            if (this.optional(element)) {
                return true;
            }

            var numbers = value.split(/, |,|;/);
            for (var i = 0; i < numbers.length; i++) {
                // taken from the jquery validation internals
                if (
                    !/^[a-zA-Z0-9.!#$%&'+\/=?^_`{|}~-]+(?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)$/.test(
                        numbers[i]
                    )
                ) {
                    return false;
                }
            }

            return true;
        },
        "Please specify a serial number or a comma separated list of serial number"
    );
	// Create role
	$('#create_inventory_form, #edit_inventory_form').validate({
		rules: {
			po_number: {
				required: true,
			},
			// serial_number: {
			// 	required: true,
			// 	separated_serial_number: true,
			// },
			po_expiry: {
				required: true,
			},
			po_start: {
				required: true,
			},
			model_number: {
				required: true,
			},
			mrna_number: {
				required: true,
				alphanumeric: true,
			},
			item_type: {
				required: true,
			},
			// item_qty: {
			// 	required: true,
			// 	number:true,
			// },
			vendor: {
				required: true,
			},
			vendor_email: {
				required: true,
				validEmail:true,
			},
			vendor_contact: {
				required: true,
				number:true,
			},
			sla: {
				required: true,
				number:true,
			},
			status: {
				required: true,
			},
			item_cost: {
				required: true,
				number:true,
			},
			brand: {
				required: true,
			},
			asset_type: {
				required: true,
			},
			total_cost: {
				required: true,
				number:true,
			},
			
		},
		messages: {
			po_number: {
				required: "Please enter po number",
			},
			// serial_number: {
			// 	required: "Please enter serial number",
			// 	separated_serial_number:
            //         "Please specify a serial number or a comma separated list of serial number",
			// },
			po_expiry: {
				required: "Please select po expiry",
			},
			po_start: {
				required: "Please select po Start",
			},
			model_number: {
				required: "Please enter model number",
			},
			mrna_number: {
				required: "Please enter mrna number",
				alphanumeric: "Please enter valid input",
			},
			item_type: {
				required: "Please select item type",
			},
			// item_qty: {
			// 	required: "Please enter item quantity",
			// 	number: "Please enter valid input",
			// },
			vendor: {
				required: "Please select vendor",
			},
			vendor_email: {
				required: "Please enter vendor email",
				validEmail:"Please enter valid email",
			},
			vendor_contact: {
				required: "Please enter vendor contact",
				number: "Please enter valid input",
			},
			sla: {
				required: "Please enter sla",
				number: "Please enter valid input",
			},
			status: {
				required: 'Please select status',
			},
			item_cost: {
				required: 'Please enter item cost',
				number: "Please enter valid input",
			},
			total_cost: {
				required: 'Please enter total cost',
				number: "Please enter valid input",
			},
			brand: {
				required: 'Please select brand name',
			},
			asset_type: {
				required: 'Please select asset type',
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

	$('#create_inventory_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		

		if($('#create_inventory_form').valid()){

			var quantity=$('#item_qty').val();

			var serial_number=$('#serial_number').val();
			var serial_number=serial_number.split(',');
			
			if (quantity!=serial_number.length) { 
				alert ("Quantity and Serial number count does not match");
				return false;
			}


			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#create_inventory_form')[0]),
				enctype: 'multipart/form-data',
				cache: false,
				contentType: false,
				processData: false,
				type: 'post',
				url: "/admin/inventory/store",
				success: function(response){
					var res = response;
					if(res.result == 'success'){
						
						Swal.fire({
			                text: res.msg,
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/inventory';
						});
						
						 $('#create_inventory_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_inventory_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	$('#vendor').on('change', function () {

		vendor_id = $(this).val();
		$('.pre-loader').show();
		$.ajax({
			data: {
				vendor_id: vendor_id,
				_token: $('input[name="_token"]').val()
			},
			type: 'post',
			url: '/admin/get_vendor',
			async: false,
			success: function (response) {
				if (response.result == 'success') {
					let vendor = response.data;
					$('#vendor_contact').val(vendor.phone);
					$('#vendor_email').val(vendor.email);
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
	$('#edit_inventory_form').on('submit', function(e){
		e.preventDefault();
		$('.common-error').empty();

		if($('#edit_inventory_form').valid()){

			// var quantity=$('#item_qty').val();

			// var serial_number=$('#serial_number').val();
			// var serial_number=serial_number.split(',');
			
			// if (quantity!=serial_number.length) { 
			// 	alert ("Quantity and Serial number count does not match");
			// 	return false;
			// }

			$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#edit_inventory_form')[0]),
				type: 'post',
				url: "/admin/inventory/update",
				enctype: 'multipart/form-data',
				cache: false,
				contentType: false,
				processData: false,
				success: function(response){
					let res = response;

					if(res.result == 'success'){
						
						Swal.fire({
			                text: res.msg,
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/inventory';
						});
						
						$('#edit_inventory_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#edit_inventory_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
	
	$(document).on('click', '.delete_location', function () {

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

				let location_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'location_id': location_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/locations/delete/'+location_id,

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
	$(':file').on('change', function (event, numFiles, label) {
		let text = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
		if (text == '') {
			text = 'No file chosen...';
		}
		if(text.length > 20){
			text = text.substr(0, 20)+'...'; 
		}
		$(this).parent().parent().find('.file-select-name').html(text);
	});

	$('#brand').on('change', function () {

		brand_id = $(this).val();
		$('.pre-loader').show();
		$.ajax({
			data: {
				brand_id: brand_id,
				_token: $('input[name="_token"]').val()
			},
			type: 'post',
			url: '/admin/assets/get-asset',
			async: false,
			success: function (response) {
				if (response.result == 'success') {
					let assets = response.data;
                    $("#item_type").empty();
                    $("#item_type").append(
                        '<option value="">Select Item Type</option>'
                    );

                    $.each(assets, function (i, item) {
                        $("#item_type").append(
                            $("<option></option>")
                                .attr("value", item.id)
                                .text(item.name)
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