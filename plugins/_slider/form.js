function trtUpdateJS()
{
    v = $('#GenerateJs').val();
    if(v == 'YES')
    {
        $('#former #fieldset_Options').show();
    }
    else
    {
        $('#former #fieldset_Options').hide();
    }
}




trtUpdateJS();
$('#former #GenerateJs').change(function(){trtUpdateJS()})


