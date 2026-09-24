// $(document).ready(function () {
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
$('#create_notes_form').validate({
    rules: {
        // dept_id: {
        //     required: true,
            
        // },
        // prop_no: {
        //     required: true,
        // },
        // sub_line: {
        //     required: true,
        // },
        // subject: {
        //     required: true,
        // },
        
    },
    messages: {
        // dept_id: {
        //     required: "Please enter department name",
            
        // },

        // prop_no: {
        //     required: "Please enter proposal number",
            
        // },
        // sub_line: {
        //     required: "Please enter subject line",
            
        // },
        // subject: {
        //     required: "Please enter content",
            
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
// });

$('#create_notes_form').on('submit', function(e){
    var status = $('.submit_btn').val();
    var draft = $('#draft2').val();
    e.preventDefault();
    $('.common-error').empty();
	
    // if($('#create_notes_form').valid()){
    
        $('.pre-loader').show();
        var subject = CKEDITOR.instances.subject.getData();
        // var subject = $(string).find('p').unwrap().end().html();
        var formData=new FormData($('#create_notes_form')[0]);
        formData.append("subject", subject);
        formData.append("status", status);
		formData.append("draft", draft);
        $.ajax({
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            type: 'post',
            url: "/admin/nv-notes/store",
            success: function(response){
                var res = response;
                if(res.result == 'success'){
                    
                    Swal.fire({
                        text: "Your Note is Created Successfully",
                        type: 'success',
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        confirmButtonClass: "btn font-weight-bold btn-primary"
                    }).then(function() {
                        window.location = '/admin/needvalidation/list';
                    });
                    
                     $('#create_notes_form')[0].reset();
                }
                else if(res.result == 'error'){
                    let error_msgs = res.msg;
                    for(let key in error_msgs){
                        if(error_msgs.hasOwnProperty(key)){
                            $('#create_notes_form').find('.'+key+'_error').html(error_msgs[key][0]);
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
    // }
});

$('.save_btn').click(function (e) {
    var status = $('.save_btn').val();
    // alert(status);
    var draft = $('#draft1').val();

    e.preventDefault();
    $('.common-error').empty();
    if($('#create_notes_form').valid()){

        $('.pre-loader').show();
        var subject = CKEDITOR.instances.subject.getData();
        // alert(subject);
        // var subject = $(string).find('p').unwrap().end().html();
        var formData = new FormData($('#create_notes_form')[0]);
        formData.append("status", status);
        formData.append("draft", draft);
        formData.append("subject", subject);

        $.ajax({
            data:formData,
            cache: false,
            processData: false,
            contentType: false,
            type: 'post',
            url: "/admin/nv-notes/store",
            success: function(response){
                var res = response;
                if(res.result == 'success'){
                    
                    Swal.fire({
                        text:  " Your Note is Created Successfully",
                        type: 'success',
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        confirmButtonClass: "btn font-weight-bold btn-primary"
                    }).then(function() {
                        window.location = '/admin/needvalidation/list';
                    });
                    
                     $('#create_notes_form')[0].reset();
                }
                else if(res.result == 'error'){
                    let error_msgs = res.msg;
                    for(let key in error_msgs){
                        if(error_msgs.hasOwnProperty(key)){
                            $('#create_notes_form').find('.'+key+'_error').html(error_msgs[key][0]);
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