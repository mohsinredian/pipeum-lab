$(document).ready(function () {
 
  if ($(document).find("#opex_datatable").length > 0) {
    $("#opex_datatable").DataTable({
      responsive: true,
      processing: true,
      serverSide: true,
      searching: true,
      ajax: {
        url: "/admin/opex",
      },
      columns: [
        {
          data: "DT_RowIndex",
          name: "DT_RowIndex",
          orderable: false,
          searchable: false,
          className: "text-center",
        },
        {
          data: "id",
          name: "id",
          className: "text-center",
        },
        {
					data: 'super_department',
					name: 'super_department',
					className: "text-center"
				},
        {
          data: "department",
          name: "department",
          className: "text-center",
        },
        // {
        //   data: "subdepartment",
        //   name: "subdepartment",
        //   className: "text-center",
        // },
        {
          data: "expenses_head",
          name: "expenses_head",
          className: "text-center",
        },
        {
          data: "activity",
          name: "activity",
          className: "text-center",
        },
        {

          data: "initial_approved_budget",
         
          name: "initial_approved_budget",
        
          render: function (data, type, row) {
          
                 for (var index = 0;index < data.length; index++) {
                   var value = data[index];
                   console.log(value);
             
                   return value;
             
                 }
				
					}
        },
        {
          data: "initial_approved_budget2",
          name: "initial_approved_budget2",
          className: "text-center",
          render: function (data, type, row) {
					 
					  return data.toLocaleString('en-IN');
					}
        },
        {
          data: "initial_approved_budget3",
          name: "initial_approved_budget3",
          className: "text-center",
          render: function (data, type, row) {
					 
					  return data.toLocaleString('en-IN');
					}
        },
        {
          data: "action",
          name: "action",
          className: "text-center",
          orderable: false,
        },
      ],
      columnDefs: [
        { orderable: false, targets: 0 },
        { visible: false, targets: [1], orderable: true },
      ],
      aaSorting: [[1, "desc"]],
    });
  }
  jQuery.validator.addMethod(
    "validEmail",
    function (value, element) {
      return this.optional(element) || /\S+@\S+\.\S+/.test(value);
    },
    "Please enter valid email"
  );
  jQuery.validator.addMethod(
    "alpha",
    function (value, element) {
      return this.optional(element) || /^[a-zA-Z\s']*$/.test(value);
    },
    "Invalid input"
  );
  jQuery.validator.addMethod(
    "alphanumeric",
    function (value, element) {
      return this.optional(element) || /^[a-zA-Z0-9\s']*$/.test(value);
    },
    "Invalid input"
  );
  jQuery.validator.addMethod(
    "alphadash",
    function (value, element) {
      return this.optional(element) || /^[a-zA-Z \s']*$/.test(value);
    },
    "Invalid input"
  );
  // Create role
  $("#create_opex_form, #edit_location_form, #edit_opex_form").validate({
    rules: {
      department_id: {
        required: true,
      },
      super_department: {
        required: true,
      },

      sub_department: {
        required: true,
      },
      expenses_head: {
        required: true,
        maxlength: 100,
      },
      activity: {
        required: true,
      },

      initial_approved_budget: {
        // required: true,
        maxlength: 150,
      },
      initial_approved_budget2: {
        // required: true,
        maxlength: 150,
      },
      initial_approved_budget3: {
        // required: true,
        maxlength: 150,
      },
    },
    messages: {
      department_id: {
        required: "Please select department",
      },
      super_department: {
        required: "Please select Super Department",
      },
      sub_department: {
        required: "Please select sub department",
      },
      activity: {
        required: "Please select activity",
      },
      expenses_head: {
        required: "Please enter expense head",
        maxlength: "Expense Head should not be more than 1000 characters",
      },

      // initial_approved_budget: {
      //   required: "Please enter initial approved budget amount",
      //   maxlength: "Expense Head should not be more than 150 characters",
      // },
    },
    errorPlacement: function (error, element) {
      if (element.attr("name") == "logo") {
        // custom error placement
        $(element)
          .closest(".form-group")
          .find(".common-error")
          .html(error.text());
      } else {
        // default error placement
        element.after(error);
      }
    },
  });

  $("#create_opex_form").on("submit", function (e) {
    e.preventDefault();
    $(".common-error").empty();

    if ($("#create_opex_form").valid()) {
      $(".pre-loader").show();

      $.ajax({
        data: new FormData($("#create_opex_form")[0]),
        cache: false,
        processData: false,
        contentType: false,
        type: "post",
        url: "/admin/opex/store",
        success: function (response) {
          var res = response;
          if (res.result == "success") {
            Swal.fire({
              text: "Opex Created.",
              type: "success",
              buttonsStyling: false,
              confirmButtonText: "OK",
              confirmButtonClass: "btn font-weight-bold btn-primary",
            }).then(function () {
              window.location = "/admin/opex";
            });

            $("#create_opex_form")[0].reset();
          } else if (res.result == "error") {
            let error_msgs = res.msg;
            for (let key in error_msgs) {
              if (error_msgs.hasOwnProperty(key)) {
                $("#create_opex_form")
                  .find("." + key + "_error")
                  .html(error_msgs[key][0]);
              }
            }
          } else if (res.result == "failure") {
            Swal.fire({
              text: "Something went wrong. Please try again.",
              type: "error",
              buttonsStyling: false,
              confirmButtonText: "OK",
              confirmButtonClass: "btn font-weight-bold btn-light",
            }).then(function () {
              window.location.reload();
            });
          }

          $(".pre-loader").hide();
        },

        error: function (error) {},
      });
    }
  });

  // Edit role
  $("#edit_opex_form").on("submit", function (e) {
    e.preventDefault();
    $(".common-error").empty();

    if ($("#edit_opex_form").valid()) {
      $(".pre-loader").show();

      $.ajax({
        data: new FormData($("#edit_opex_form")[0]),
        type: "post",
        url: "/admin/opex/update",
        cache: false,
        contentType: false,
        processData: false,
        success: function (response) {
          let res = response;

          if (res.result == "success") {
            Swal.fire({
              text: "Opex Updated.",
              type: "success",
              buttonsStyling: false,
              confirmButtonText: "OK",
              confirmButtonClass: "btn font-weight-bold btn-primary",
            }).then(function () {
              window.location = "/admin/opex";
            });

            $("#edit_opex_form")[0].reset();
          } else if (res.result == "error") {
            let error_msgs = res.msg;
            for (let key in error_msgs) {
              if (error_msgs.hasOwnProperty(key)) {
                $("#edit_opex_form")
                  .find("." + key + "_error")
                  .html(error_msgs[key][0]);
              }
            }
          } else if (res.result == "failure") {
            Swal.fire({
              text: "Something went wrong. Please try again.",
              type: "error",
              buttonsStyling: false,
              confirmButtonText: "OK",
              confirmButtonClass: "btn font-weight-bold btn-light",
            }).then(function () {
              window.location.reload();
            });
          }

          $(".pre-loader").hide();
        },

        error: function (error) {},
      });
    }
  });

  $(document).on("click", ".delete_location", function () {
    Swal.fire({
      title: "Are you sure?",
      // text: "You won't be able to revert this!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes",
    }).then((result) => {
      if (result.value) {
        $(".pre-loader").show();

        let location_id = $(this).attr("data-id");

        $.ajax({
          data: {
            location_id: location_id,
            _token: $('input[name="_token"]').val(),
          },
          type: "DELETE",
          url: "/admin/locations/delete/" + location_id,

          success: function (response) {
            let res = response;

            if (res.result == "success") {
              Swal.fire({
                text: res.msg,
                type: "success",
                buttonsStyling: false,
                confirmButtonText: "OK",
                confirmButtonClass: "btn font-weight-bold btn-primary",
              }).then(function () {
                window.location.reload();
              });
            } else if (res.result == "failure") {
              Swal.fire({
                text: "Something went wrong. Please try again.",
                type: "error",
                buttonsStyling: false,
                confirmButtonText: "OK",
                confirmButtonClass: "btn font-weight-bold btn-light",
              }).then(function () {
                window.location.reload();
              });
            }

            $(".pre-loader").hide();
          },

          error: function (error) {},
        });
      }
    });
  });

  $("#create_floor_plan_form").on("submit", function (e) {
    // let obj ={basement:[],ground_floor:[],first_floor:[],second_floor:[],third_floor:[],fourth_floor:[],fifth_floor:[],sixth_floor:[]}
    // var basement = document.querySelectorAll("[id^='basement']");
    // //console.log(basement,"basement")

    // basement.forEach(x=>{
    // 	if(x && x.checked){
    // 		if(obj.basement)
    // 		obj.basement.push([x.value])
    // 	}
    //   });

    // var ground_floor = document.querySelectorAll("[id^='ground']");
    // ground_floor.forEach(x=>{
    // 	if(x && x.checked){
    // 		obj.ground_floor.push([x.value])
    // 	}
    //   });

    // var first_floor = document.querySelectorAll("[id^='first']");
    // first_floor.forEach(x=>{
    // 	if(x && x.checked){
    // 		obj.first_floor.push([x.value])
    // 	}
    //   });

    // var second_floor = document.querySelectorAll("[id^='second']");
    // second_floor.forEach(x=>{
    // 	if(x && x.checked){
    // 		obj.second_floor.push([x.value])
    // 	}
    //   });
    //   var third_floor = document.querySelectorAll("[id^='third']");
    //   third_floor.forEach(x=>{
    // 	  if(x && x.checked){
    // 		  obj.third_floor.push([x.value])
    // 	  }
    // 	});
    //   var fourth_floor = document.querySelectorAll("[id^='fourth']");
    //   fourth_floor.forEach(x=>{
    // 	  if(x && x.checked){
    // 		  obj.fourth_floor.push([x.value])
    // 	  }
    // 	});
    // 	var fifth_floor = document.querySelectorAll("[id^='fifth']");
    // 	fifth_floor.forEach(x=>{
    // 		if(x && x.checked){
    // 			obj.fifth_floor.push([x.value])
    // 		}
    // 	  });
    // 	  var sixth_floor = document.querySelectorAll("[id^='sixth']");
    // 	sixth_floor.forEach(x=>{
    // 		if(x && x.checked){
    // 			obj.sixth_floor.push([x.value])
    // 		}
    // 	  });
    //   console.log(obj)
    e.preventDefault();
    //$('.common-error').empty();

    if ($("#create_floor_plan_form").valid()) {
      let formData = new FormData($("#create_floor_plan_form")[0]);
      //   formData.append('data',JSON.stringify(obj));
      console.log(formData);
      $(".pre-loader").show();

      $.ajax({
        // data: formData,
        data: new FormData($("#create_floor_plan_form")[0]),
        cache: false,
        processData: false,
        contentType: false,
        type: "post",
        url: "/admin/locations/store_floor_plan",
        success: function (response) {
          var res = response;
          if (res.result == "success") {
            Swal.fire({
              text: "Location Created.",
              type: "success",
              buttonsStyling: false,
              confirmButtonText: "OK",
              confirmButtonClass: "btn font-weight-bold btn-primary",
            }).then(function () {
              window.location = "/admin/locations";
            });

            $("#create_floor_plan_form")[0].reset();
          } else if (res.result == "error") {
            let error_msgs = res.msg;
            for (let key in error_msgs) {
              if (error_msgs.hasOwnProperty(key)) {
                $("#create_floor_plan_form")
                  .find("." + key + "_error")
                  .html(error_msgs[key][0]);
              }
            }
          } else if (res.result == "failure") {
            Swal.fire({
              text: "Something went wrong. Please try again.",
              type: "error",
              buttonsStyling: false,
              confirmButtonText: "OK",
              confirmButtonClass: "btn font-weight-bold btn-light",
            }).then(function () {
              window.location.reload();
            });
          }

          $(".pre-loader").hide();
        },

        error: function (error) {},
      });
    }
  });
});

//import
$(document).ready(function () {
  $("#file-upload").on("change", () => {
    $.ajax({
      url: "/admin/opex/upload",
      type: "POST",
      data: new FormData($("#upload-form")[0]),
      dataType: "json",
      processData: false,
      contentType: false,
      success: function (data) {
        console.log(data);
        swal({
          title: "Success!",
          text: "Your file has been Imported.",
          icon: "success",
          button: "OK",
        });
      },
      error: function (xhr, textStatus, errorThrown) {
        console.error(
          "There was a problem with the ajax operation:",
          errorThrown
        );
        swal({
          title: "Error!",
          text: "Something went wrong. Please try again later.",
          icon: "error",
          button: "OK",
        });
      },
    });
  });
});


$(document).ready(function () {

    $('#super_department').change(function () {

        let selected = $('#deps').val() || "";
        let selectedArray = selected !== "" ? selected.split(',').map(v => v.trim()) : [];

        let selectedDepartment = $(this).val();

        let url = selectedDepartment !== ''
            ? '/admin/getSubDepartments_opex/' + selectedDepartment
            : '/admin/getSubDepartments1_opex';

        console.log("Calling URL =>", url);

        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',

            success: function (response) {

                console.log("Response =>", response.data);

                $('#department_id').empty();
                $('#department_id').append('<option value="">Select Sub-Department</option>');

                if (response.data && response.data.length > 0) {

                    $.each(response.data, function (key, value) {

                        // If no selected previous OR not in selected list → show item
                        if (selectedArray.length === 0 ||
                            !selectedArray.includes(value.id.toString())) {

                            $('#department_id').append(
                                `<option value="${value.id}">${value.name}</option>`
                            );
                        }
                    });
                } 
                
                // refresh if using Select2
                $('#department_id').trigger('change');
            },

            error: function (xhr) {
                console.log("AJAX ERROR:", xhr.responseText);
            }
        });

    });

});

