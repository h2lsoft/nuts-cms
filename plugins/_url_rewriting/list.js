function urlRewritingGenerate()
{
    uri = "index.php?mod=_url_rewriting&do=list&ajaxer=1";
    $.get(uri, function(resp){

        if(resp != 'ok')
        {
            alert(resp);
        }
        else
        {
            // alert("SuccessUrl correclty rewrited in file `/nuts/url_rewriting_rules.inc.php`");
            notify('ok', "Urls have been correclty rewrited");
        }

    });


}