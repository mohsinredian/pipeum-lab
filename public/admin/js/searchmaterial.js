$(document).ready(function() {
    $('#search_material').select2({
        
        ajax: {
            url: '/admin/nv_material/search_material_by_name',
            data: function (params) {
                var query = {
                    search_term: params.term
                }
    
                return query;
            },
            processResults: function (data) {
                console.log(data);
                return {
                    results: data
                };
            }
        },
        cache: true,
        placeholder: 'Search for a material code...',
        minimumInputLength: 1,
    }).on('select2:select', function (e) {
        var selectedData = e.params.data;
        $("#material_code_0").val(selectedData.id);
        $("#uom_0").val(selectedData.mat_des_0);
        $("#mat_des_0").val(selectedData.uom_0);
        $("#rate").val(selectedData.rate);
    
     
    });

    $('#materialBoqsave').on('click', function() {
        // Clear the search field value
        $('#search_material').val('').trigger('change'); // This line clears the search field and triggers a change event.

    });
    
});
