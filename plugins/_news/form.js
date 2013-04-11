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



