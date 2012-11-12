$('#former #Type').change(function(){

    v = $(this).val();

    if(v == 'gone')
        $('#former #UrlNew').attr('disabled', true);
    else
        $('#former #UrlNew').attr('disabled', false);


});


$('#former #Type').change();