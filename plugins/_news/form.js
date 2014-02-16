for(i=0; i < hfs.length; i++)
{
	n = trim(hfs[i]);
	if(!empty(n))
	{
		if(n.indexOf('fieldset') == 0)
        {
            if(news_new_system && n != 'VirtualPageName')
                $('#former #'+n).hide();
        }
		else
			$('#former #'+n).parent('p').hide();
	}
}


// hide filters
if($('#former #fieldset_CustomFilter').children('p').length == 0)
	$('#former #fieldset_CustomFilter').hide();


// tags autocomplete
// 'index.php?mod=_internal-messaging&do=list&action=get_user';
uri = ajaxerUrlConstruct('get_tag', '_news');
$("#Tags").autocomplete(uri, {
    width: 300,
    multiple: true,
    multipleSeparator: ", ",
    matchContains: false,
    autoFill: true,
    delay:300,
    minChars:1,
    cache : 0,

    formatItem: function(data, i, n, value){
        return value;
    },

    formatResult: function(data, value){
        return value;
    }


});





