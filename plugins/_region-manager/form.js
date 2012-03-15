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
		$('#PagerPreviousText').parents('p').show();
		$('#PagerNextText').parents('p').show();
	}
	else
	{
		$('#PagerPreviousText').parents('p').hide();
		$('#PagerNextText').parents('p').hide();
	}
}


updatePager();
$('#Pager').change(function(){
	updatePager();
})


