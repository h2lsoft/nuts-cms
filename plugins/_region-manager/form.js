initCodeEditor('PhpCode', 'php', true);
initCodeEditor('Query', 'sql', true);
initCodeEditor('HtmlBefore', 'html', true);
initCodeEditor('Html', 'html', true);
initCodeEditor('HtmlAfter', 'html', true);
initCodeEditor('HtmlNoRecord', 'html', true);
initCodeEditor('HookData', 'php', true);



function updatePager()
{
	v = $('#Pager').val();
	if(v == 'YES')
	{
        $('#SetUrl').parents('p').show();

        $('#PreviousStartEndVisible').parents('p').show();
        $('#PagerStartText').parents('p').show();
        $('#PagerEndText').parents('p').show();

        $('#PagerPreviousText').parents('p').show();
        $('#PagerNextText').parents('p').show();

        updatePagerStartEnd()
	}
	else
	{
		$('#SetUrl').parents('p').hide();

		$('#PreviousStartEndVisible').parents('p').hide();
        $('#PagerStartText').parents('p').hide();
		$('#PagerEndText').parents('p').hide();

		$('#PagerPreviousText').parents('p').hide();
		$('#PagerNextText').parents('p').hide();
	}
}

function updatePagerStartEnd()
{
    v = $('#PreviousStartEndVisible').val();
    if(v == 'YES')
    {
        $('#PagerStartText').parents('p').show();
        $('#PagerEndText').parents('p').show();
    }
    else
    {
        $('#PagerStartText').parents('p').hide();
        $('#PagerEndText').parents('p').hide();
    }
}


updatePager();
$('#Pager').change(function(){
	updatePager();
});

$('#PreviousStartEndVisible').change(function(){
    updatePagerStartEnd();
});


