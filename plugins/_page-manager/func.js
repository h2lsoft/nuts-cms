function updateHeaderImg()
{
	v = $('#HeaderImage').val();

	if(!$('#HeaderImage').is(":visible"))
		v = "";

	str = '';
	if(v != '')
	{
		str = '<img src="{CONST::NUTS_HEADER_IMAGES_URL}/'+v+'" style="max-width:750px; max-height:100px;" />';
	}

	$('#HeaderImagePreview').html(str);
}


function openUrlRewriting(){

    popupModal('index.php?mod=_url_rewriting&do=list&parent_refresh=0', "UriRewriting", 1024, 768);
}


function generateFromH1(){

	if($("#H1").val() != '')
		v = $("#H1").val();
	else if($("#MetaTitle").val() != '')
		v = $("#MetaTitle").val();
	else if($("#MenuName").val() != '')
		v = $("#MenuName").val();

	v = strtolower(v);
	v = trim(v);
    v = str_replace("/", '', v);
	v = str_replace("'", '-', v);
	v = str_replace('"', '-', v);
	v = str_replace(' ', '-', v);
	v = str_replace(',', '-', v);
	v = str_replace('.', '-', v);
	v = str_replace(';', '-', v);
	v = str_replace('!', '-', v);
	v = str_replace('?', '-', v);
	v = str_replace('#', '-', v);
	v = str_replace('&', '-', v);
	v = str_replace('(', '-', v);
	v = str_replace(')', '-', v);
	v = str_replace('{', '-', v);
	v = str_replace('}', '-', v);
	v = str_replace('%', '-', v);
	v = str_replace('ç', 'c', v);
	v = str_replace('à', 'a', v);
	v = str_replace('â', 'a', v);
	v = str_replace('ä', 'a', v);
	v = str_replace('é', 'e', v);
	v = str_replace('è', 'e', v);
	v = str_replace('ê', 'e', v);
	v = str_replace('ê', 'e', v);
	v = str_replace('ï', 'i', v);
	v = str_replace('î', 'i', v);
	v = str_replace('ô', 'o', v);
	v = str_replace('ö', 'o', v);
	v = str_replace('ù', 'u', v);
	v = str_replace('û', 'u', v);
	v = str_replace('ü', 'u', v);
	v = str_replace('---', '-', v);
	v = str_replace('--', '-', v);
	v = str_replace('-', ' ', v);
	v = trim(v);
	v = str_replace(' ', '-', v);
	
	$("#VirtualPagename").val(v);
}


function blockPreview(selectID, imageUrl)
{
	pos = $('#'+selectID).position();

	$('#image_preview').css('overflow', "hidden");
    $('#image_preview').css('background-color', "#e5e5e5");
    $('#image_preview').css('border', "1px solid #ccc");
	$('#image_preview').css('top', pos.top);
	$('#image_preview').css('left', pos.left+10+$('#'+selectID).width());

	img = '<img src="'+imageUrl+'" style="width:150px;" />'
    $('#image_preview').html(img);
	$('#image_preview').show();
}

function trtUpdateSitemap(){

	v = $("#former #Sitemap").val();
	if(v == 'NO')
	{
		$('#SitemapChangefreq').parent('p').hide();
		$('#SitemapPriority').parent('p').hide();
		$('#fieldset_SitemapOptions').hide();
	}
	else
	{
		$('#SitemapChangefreq').parent('p').show();
		$('#SitemapPriority').parent('p').show();
		$('#fieldset_SitemapOptions').show();
	}

	trtUpdateSitemapOptions();
    updateCommentTab();
}

function trtUpdateSitemapOptions(){

	v = $("#former #SitemapPageType").val();

	if(v == 'NORMAL')
	{
		$('#SitemapUrlRegex1').parent('p').hide();
		$('#SitemapUrlRegex2').parent('p').hide();
	}
	else
	{
		$('#SitemapUrlRegex1').parent('p').show();
		$('#SitemapUrlRegex2').parent('p').show();
	}

}


function trtUpdateAccessRestrict(){

	v = $("#former #AccessRestricted").val();
	if(v == 'NO')
	{
		$('.checkbox_list').parent('p').hide();
	}
	else
	{
		$('.checkbox_list').parent('p').show();
		$('#Sitemap').val('NO');
		trtUpdateSitemap();
	}

}