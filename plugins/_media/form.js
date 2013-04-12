current_type_search = $('#Type').val();
if(current_type_search != '')
    $('#former #Type').val(current_type_search);


$('#former #Type').change(function(){

    $('#former #Url').parents('p').hide();
    $('#former #EmbedCode').parents('p').hide();
    $('#former #EmbedCodePreviewUrl').parents('p').hide();
    $('#former #fieldset_YoutubeVideoParams').hide();
    $('#former #fieldset_DailymotionParams').hide();
    $('#former #fieldset_VideoParams').hide();
    $('#former #fieldset_AudioParams').hide();
    $('#former #fieldset_IframeParams').hide();

    if($(this).val() == 'YOUTUBE VIDEO')
    {
        $('#former #fieldset_YoutubeVideoParams').show();
    }
    if($(this).val() == 'DAILYMOTION')
    {
        $('#former #fieldset_DailymotionParams').show();
    }
	else if($(this).val() == 'AUDIO')
	{
        $('#former #Url').parents('p').show();
        $('#former #fieldset_AudioParams').show();
	}
	else if($(this).val() == 'VIDEO')
	{
		$('#former #Url').parents('p').show();
		$('#former #fieldset_VideoParams').show();
	}
    else if($(this).val() == 'IFRAME')
    {
        $('#former #fieldset_IframeParams').show();
    }
	else if($(this).val() == 'EMBED CODE')
	{
		$('#former #EmbedCode').parents('p').show();
		$('#former #EmbedCodePreviewUrl').parents('p').show();
	}

});

$('#former #Type').change();

// update from parameters
params = $('#former #Parameters').val();
if(!empty(params))
{
	tabs = explode('@@', params);
	for(i=0; i < tabs.length; i++)
	{
		if(!empty(tabs[i]))
		{
			v2 = explode('=>', tabs[i]);

			prefix = 'PA_';
			if($('#former #Type').val() == 'VIDEO')prefix = 'PV_';
            if($('#former #Type').val() == 'YOUTUBE VIDEO')prefix = 'PVYT_';
            if($('#former #Type').val() == 'DAILYMOTION')prefix = 'PVD_';
            if($('#former #Type').val() == 'IFRAME')prefix = 'PIF_';

			if(!empty(v2[0]))
				$('#former #'+prefix+v2[0]).val(v2[1]);

		}
	}
}


