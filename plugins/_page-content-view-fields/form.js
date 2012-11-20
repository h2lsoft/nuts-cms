$('#former #Name').blur(function(){

    if(empty($('#former #Label').val()))
    {
        $('#former #Label').val($(this).val());
    }


});