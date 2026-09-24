$(document).ready(function() {
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
	var ttlamt_ser1 = $('#total_budget_service').val();
	// alert(ttlamt_ser1);
	
	//    alert(nv_id);total_ser_amo

	var formData = new FormData();
	formData.append("nv_id", nv_id);
	formData.append("service_code_0", service_code_0);
	formData.append("ser_des_0", ser_des_0);
	formData.append("ser_uom_0", ser_uom_0);
	formData.append("ser_rate", ser_rate);
	formData.append("ser_quantity", ser_quantity);
	formData.append("ser_total_amount", ser_total_amount);
	if ($('#serviceboqform')) {

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
                $('.pre-loader').hide(); // Hide the loader after success
                // Reset the form
            $('#serviceboqform')[0].reset();
            });
			// ttlamt_ser = ttlamt_ser.replace(/[^a-zA-Z0-9_ ]/g, "");
	ttlamt_ser= parseInt(ttlamt_ser)+parseInt(ser_total_amount);
	// ttlamt_ser = ttlamt_ser.toLocaleString('en-IN');
	 var tot_amt = $('#total_budget_both').val();
	// tot_amt = tot_amt.replace(/[^a-zA-Z0-9_ ]/g, "");
	var tot1 = parseInt(tot_amt) + parseInt(ser_total_amount);
	// tot1 = tot1.toLocaleString('en-IN');
	//  alert(ser_total_amount);
	$('#total_budget_both').val(tot1);
	$('#total_ser_amo').val(ttlamt_ser);
	$('#total_budget_service').val(ttlamt_ser);
	// $('#serviceboqform')[0].reset();
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
                            $('#nv_serviceBoq_datatables').DataTable().row($(this).closest('tr')).remove().draw(false);
                            
							var totalbud = $('#total_buget').val() || 0 ;
							var totservice = $('#total_ser_amo').val() || 0;
                            var tot_ser = parseFloat(totservice) - parseFloat(res.response);
							var totalservice = parseFloat(totalbud) - parseFloat(res.response);
							$('#total_buget').val(totalservice);
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
                            // Remove all rows from the DataTable
                            $('#nv_serviceBoq_datatable').DataTable().clear().draw(false);

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
    // var ttlamt_ser= $('#total_ser_amo').val();
    // ttlamt_ser = ttlamt_ser.toLocaleString('en-IN');
    var formData = new FormData();
    formData.append("serviceboq", fileInput);
    formData.append("nv_id", nv_id);

    var totalAmountService = $('#total_ser_amo').val();
    // totalAmountService = totalAmountService.replace(/[^a-zA-Z0-9_ ]/g, "");
    // totalAmountService = parseFloat(totalAmountService);

    var total_budget = $('#total_budget_both').val();
    // total_budget = total_budget.replace(/[^a-zA-Z0-9_ ]/g, "");
    // total_budget = parseFloat(total_budget);

    var totalbudgetservice = $('#total_budget_service').val();
    // totalbudgetservice = totalbudgetservice.replace(/[^a-zA-Z0-9_ ]/g, "");
    // totalbudgetservice = parseFloat(totalbudgetservice);

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

            var totalbdgtser =parseInt(totalbudgetservice)+parseInt(totalAmountboq);
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