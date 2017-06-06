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



function blockSelectInit(objID)
{
	$(".block_wrapper_"+objID).remove();

	str = '';
	str += '<div class="block_wrapper_'+objID+'">';
	str += '<select data-parent="'+objID+'" class="options" onchange="blockSelectAdd(this)">';
	str += '    <option value="">'+$('#'+objID).attr('title')+'</option>';
	
	$('#'+objID).children('option').each(function(){
		
		option_label = $(this).text();
		option_value = $(this).val();
		option_img = $(this).attr('image_preview');
		option_style = (!$(this).is(':selected')) ? '' : 'display:none';
		
		str += '<option style="'+option_style+'" data-image_preview="'+option_img+'" value="'+option_value+'">'+option_label+'</option>';
		
	});
	
	str += '</select>';
	
	str += '<ul id="list_'+objID+'" data-parent="'+objID+'" class="asmList ui-sortable asmListSortable">';
	
	$('#'+objID).children('option').each(function(){
		
		option_label = $(this).text();
		option_value = $(this).val();
		option_image = $(this).attr('image_preview');
		
		if($(this).is(':selected'))
		{
			str += blockSelectListItemAdd(option_label, option_value, option_image, false);
		}
	});
	
	str += '</ul>';
	str += '</div>';
	
	$('#'+objID).after(str).hide();
	blockSortableRefresh(objID, false);

	list = document.getElementById("list_"+objID);
	Sortable.create(list, {
	
			animation: 150,
			scroll: true,
			draggable: ".asmListItem",
			onUpdate: function (/**Event*/evt) {
			
				// update select parent order
				listID = $(evt.from).attr('id');
				parentID = listID.replace('list_', '');
				
				// order all item hidden
				new_orders = [];
				$('#'+listID+' li').each(function(){
					new_orders[new_orders.length] = $(this).attr('data-value');
				});
				
				
				new_orders2 = '';
				for(i=0; i < new_orders.length; i++)
				{
					new_orders2 += $('#'+parentID+' option:selected[value="'+new_orders[i]+'"]')[0].outerHTML;
				}
				
				
				// new_orders2 = str_replace('<option ', '<option selected ', new_orders2);
				$('#'+parentID+' option:selected').remove();
				$('#'+parentID).append(new_orders2);
				
				
				
			},
	
	});
	
	
	
}

function blockSortableRefresh(objID, forced)
{
	return;
	
	if(forced)
	{
		// $('.block_wrapper_'+objID+' ol').sortable("destroy");
		// $('.block_wrapper_'+objID+' ol').unbind();
	}
	
	/*
	$('.block_wrapper_'+objID+' ol').sortable({
										items: ' li.asmListItem',
           
											appendTo: 'body',
		
										forceHelperSize : true,
										helper: 'clone',
										axis: 'y'
									});*/

	
}


function blockSelectRemove(link)
{
	val = $(link).parent('li').attr('data-value');
	parentID = $(link).parent('li').parent('ul').attr('data-parent');
	
	$('#'+parentID+' option[value="'+val+'"]').removeAttr('selected');
	$(link).parent('li').parent('ul').prev('select').find('option[value="'+val+'"]').show();
	
	$(link).parent('li').remove();
	blockSortableRefresh(parentID, true);
}

function blockSelectAdd(select_option)
{
	val = $(select_option).val();
	
	if(val == '')return;

	parentID = $(select_option).attr('data-parent');
	
	$(select_option).find('option[value="'+val+'"]').hide();
	
	$(select_option).val('');
	
	// select parent
	$('#'+parentID+' option[value="'+val+'"]').attr('selected', true);
	
	
	
	// add item
	option_label = $('#'+parentID+' option[value="'+val+'"]').text();
	option_value = $('#'+parentID+' option[value="'+val+'"]').val();
	option_image = $('#'+parentID+' option[value="'+val+'"]').attr('image_preview');
	
	str = blockSelectListItemAdd(option_label, option_value, option_image);
	
	$(select_option).next().append(str);
	blockSortableRefresh(parentID, true);
	
	
}

function blockSelectListItemAdd(option_label, option_value, option_image)
{
	str = '';
	str += '<li class="asmListItem" data-value="'+option_value+'">';
	str += '    <span class="asmListItemLabel" style="font-size: 12px;">';
	str += '        <img src="'+option_image+'" style="max-height:100px; margin-right:10px; vertical-align:middle;"> ';
	str += option_label;
	str += '    </span>';
	str += '    <a href="#" onclick="blockSelectRemove(this)" class="asmListItemRemove"><img src="/nuts/img/list_delete.png"></a>';
	str += '</li>';
	
	return str;
}

