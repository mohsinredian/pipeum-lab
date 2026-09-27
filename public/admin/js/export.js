$(document).ready(function(){
    $("#btnpdf").hide();
    $("#btnexcel").hide();
    $("#btncsv").hide();
    $("#btnExport ").click(function () {
        $("#btnpdf").show();
        $("#btnexcel").show();
        $("#btncsv").show();
        
    });
    $("#btnpdf").click(function () {
        $.ajax({ 
            url: "{{route('dasboard.pdf')}}",
            type: 'get',
            data: {},
            success: function(result){
                console.log(result)
            }
        });
    });  

    $("#btnexcel").click(function () {
        $.ajax({ 
            url: "{{ route('dasboard.excel') }}",
            data: {},
            type: 'get',
            success: function(result){
                console.log(result)
            }
        });
    }); 

    $("#btncsv").click(function () {
        $.ajax({ 
            url: "{{ route('dasboard.csv') }}",
            data: {},
            type: 'get',
            success: function(result){
                console.log(result)
            }
        });
    }); 
});
