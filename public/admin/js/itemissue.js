$(document).ready(function(){
	
	// Datatable
	if($(document).find('#itemissue_datatable').length > 0){
		$('#itemissue_datatable').DataTable({
			dom: 'Bfrtip',	
			buttons: [
				'csv', 'excel', 'pdf', 'print'
			],
			buttons:true,
			responsive: true,
			processing: true,
			serverSide: true,
			"searching": true,
			ajax: {
				url: '/admin/item-issues'
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
					data: 'brands',
					name: 'brands',
					className: "text-center"
				},
				
				{
					data: 'serial_number',
					name: 'serial_number',
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
					data: 'issued_to',
					name: 'issued_to',
					className: "text-center"
				},
				
				{
					data: 'remarks',
					name: 'remarks',
					className: "text-center"
				},
				{
					data: 'issue_reason',
					name: 'issue_reason',
					className: "text-center"
				},
				// {
				// 	data: 'brand',
				// 	name: 'brand',
				// 	className: "text-center"
				// },
				/*{
					data: 'status',
					name: 'status',
				},*/
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
	$('#create_itemissue_form').validate({
		rules: {
			item_type: {
				required: true,
			},
			item_qty: {
				required: true,
			},
			brand: {
				required: true,
			},
			asset_type: {
				required: true,
			},
			serial_number: {
				required: true,
			//	separated_serial_number: true,
			},
			circle: {
				required: true,
			},
			location: {
				required: true,
			},
			issued_to: {
				required: true,
			},
			issued_to_email: {
				required: true,
				email:true,
			},
			confidentiality_rating: {
				required: true,
			},
			integrity_rating: {
				required: true,
			},
			availability_rating: {
				required: true,
			},
			overall_asset: {
				required: true,
			},
			asset_criticality: {
				required: true,
			},
			remarks: {
				maxlength:225,
			},
			status: {
				required: true,
			},
			
		},
		messages: {
			item_type: {
				required: "Please select item type",
			},
			item_qty: {
				required: "Please enter item quantity",
			},
			brand: {
				required: "Please select brand",
			},
			asset_type: {
				required: "Please select asset type",
			},
			serial_number: {
				required: "Please enter serial number",
				separated_serial_number:
                    "Please specify a serial number or a comma separated list of serial number",
			},
			circle: {
				required: "Please select circle",
			},
			location: {
				required: "Please select location",
			},
			issued_to: {
				required: "Please enter issued to",
			},
			issued_to_email: {
				required: "Please enter issued to email",
				email: "Please enter valid issued to email",
			},
			confidentiality_rating: {
				required: "Please select confidentiality rating",
			},
			integrity_rating: {
				required: "Please select integrity rating",
			},
			availability_rating: {
				required: "Please select availability rating",
			},
			overall_asset: {
				required: "Please select overall asset",
			},
			asset_criticality: {
				required: "Please select asset criticality",
			},
			remarks: {
				maxlength: "Remarks should not be more than 225 characters",
			},
			issue_reason: {
				required: "Please select issue reason",
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

	$('#create_itemissue_form').on('submit', function(e){
		//alert('hi');
		e.preventDefault();
		$('.common-error').empty();

		if($('#create_itemissue_form').valid()){
			var quantity=$('#item_qty').val();
			//alert("h1");
		//	var serial_number=$('#serial_number').val();
			// var serial_number=serial_number.split(',');
			// alert(serial_number);
			
			// if (quantity!=serial_number.length) { 
			// 	alert ("Quantity and Serial number count does not match");
			// 	return false;
			// }
		//	$('.pre-loader').show();

			$.ajax({
				data: new FormData($('#create_itemissue_form')[0]),
				cache: false,
				processData: false,
    			contentType: false,
				
				type: 'post',
				url: "/admin/item-issues/store",
				
				success: function(response){
					//alert('hii');
					var res = response;
					if(res.result == 'success'){
						
						Swal.fire({
			                text: "Item Issue created.",
			                type: 'success',
			                buttonsStyling: false,
			                confirmButtonText: "Ok",
			                confirmButtonClass: "btn font-weight-bold btn-primary"
			            }).then(function() {
							window.location = '/admin/item-issues';
						});
						
						 $('#create_itemissue_form')[0].reset();
					}
					else if(res.result == 'error'){
						let error_msgs = res.msg;
						for(let key in error_msgs){
							if(error_msgs.hasOwnProperty(key)){
								$('#create_itemissue_form').find('.'+key+'_error').html(error_msgs[key][0]);
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

	// $('#item_type').on('change', function () {

	// 	asset_id = $(this).val();
	// 	$('.pre-loader').show();
	// 	$.ajax({
	// 		data: {
	// 			asset_id: asset_id,
	// 			_token: $('input[name="_token"]').val()
	// 		},
	// 		type: 'post',
	// 		url: '/admin/get_inventory',
	// 		async: false,
	// 		success: function (response) {
	// 			if (response.result == 'success') {
	// 				let inventory = response.data;
	// 				if(inventory.qty > 0){
	// 					$('#item_qty').val(inventory.qty);
	// 				}
	// 				$('#serial_number').val(inventory.serial_number);
	// 			}
	// 			else if (response.result == 'failure') {
	// 				Swal.fire({
	// 					text: "Something went wrong. Please try again.",
	// 					type: 'error',
	// 					buttonsStyling: false,
	// 					confirmButtonText: "Ok",
	// 					confirmButtonClass: "btn font-weight-bold btn-light"
	// 				}).then(function () {
	// 					window.location.reload();
	// 				});
	// 			}

	// 			$('.pre-loader').hide();
	// 		},

	// 		error: function (error) {

	// 		}
	// 	});
	// });

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
	$('#item_type').on('change', function () {
        
		var item_type = $('#item_type').val();
	    var	asset_id =$('#brand').val();
		// var qty = $('#item_qty').val();
		
		$('.pre-loader').show();
		$.ajax({
			data: {
				asset_id: asset_id,
				_token: $('input[name="_token"]').val(),
				// qty : qty,
				item_type : item_type
			},
			type: 'post',
			url: '/admin/get_inventory',
			async: false,
			success: function (response) {
				console.log(response);
				if (response.result == 'success') {
					let inventory = response.data;
					// if(inventory.qty > 0){
					// 	$('#item_qty').val(inventory.qty);
					// }
					var aserial=inventory.serial_number;
					  $('#serial_number').empty();
					
					  $.each(aserial, function (i, item) {
						  
						  $('#serial_number').append($("<option></option>")
							  .attr("value", item)
							  .text(item));
					  });

				}
				else if (response.result == 'failure') {
					Swal.fire({
						text: response.msg+" Please try again.",
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

	$(document).on('click', '.delete_itemissue', function () {

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

				let itemissue_id = $(this).attr('data-id');

				$.ajax({
					data: {
						'itemissue_id': itemissue_id,
						'_token': $('input[name="_token"]').val()
					},
					type: 'DELETE',
					url: '/admin/itemissues/delete/'+itemissue_id,

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