function writeHtaccess()
{
    uri = "?mod=_url_redirect&do=list&_action=write_htaccess";
    $.get(uri, function(resp){

        if(resp != 'ok')
        {
            notify('error', resp);
        }
        else
        {
            notify('ok', "File .htaccess has been correctly rewrited");
        }


    });

}