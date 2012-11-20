var forbidden = array('ID', 'Language', 'NutsPageID','NutsUserID', 'CustomVars', 'FirstName', 'LastName', 'Email',
					  'NutsGroupID', 'Timezone', 'GroupName', 'CommentsNb',
					  'Position', '_HasChildren', 'Deleted', 'DateCreation', 'DateUpdate', 'Event', 'CommentName', 'CommentText', 'PageAccess',
					  'NavigationBar');
var lastPageData;

function treeView()
{
	simpleTreeCollection = $('.simpleTree').simpleTree({
		autoclose: false,
		drag: false,

		afterClick:function(node){
			hideContext();
			//cancelPage();
			//alert("text-"+$('span:first',node).text());
		},

		afterDblClick:function(node){
			//alert("text-"+$('span:first',node).text());
			editPage(node.attr('id'));
		},

		afterMove:function(destination, source, pos){
			hideContext();
			cancelPage();

			// get all position of tree
			parent_id = $('.simpleTree li#'+source.attr('id')).parents('li').attr('id');
			var arrIDs = '';
			$('.simpleTree li#'+parent_id+' ul> li[id!=""]').each(function(){
				if(arrIDs != '')arrIDs += ';';
				arrIDs += $(this).attr('id');
			});

            //alert("destination-"+destination.attr('id')+" source-"+source.attr('id')+" pos-"+pos);
			url = tree_static_url+'&_action=move_page&language='+$('#Language').val()+'&zoneID='+$('#ZoneID').val();
			url += '&ID='+source.attr('id');
			url += '&nutsPageID='+destination.attr('id');
			url += '&position='+pos;
			url += '&positions='+arrIDs;

			$.get(url, {},
							function(data){
									//alert(data);
  							}
			);

		},

		afterAjax:function(){
			hideContext();
			cancelPage();
			//alert('Loaded');
		},

		afterContextMenu:function(node){
			//cancelPage();

			//alert('R-click: '+$('span:first',node).text());
			var offs = $(node).offset({scroll:false});


            // hide folder menu options
            nodeID = node.attr('id');
            if(!$('.simpleTree #'+nodeID+'[class~=folder]').length)
                $('#page_tree_context .folder').hide();
            else
                $('#page_tree_context .folder').show();


			$('#page_tree_context_pID').html('Page ID #'+node.attr('id'));
			$('#page_tree_context').click( function() {$(this).hide();} );

			$('#page_tree_context').css( {'position':'absolute',
								'z-index':'999999',
								'left': (offs.left + 60),
								'top': (offs.top + 10)} ).show();
		},

		//animate:true
		docToFolderConvert:true
	});
}

function hideContext()
{
	$('#page_tree_context').hide();
}

function reloadResizer()
{
	$("#page_tree").resizable({
		handles: 'e',
		minWidth: 215,
		maxWidth: $(document).width()-700-100,
		resize: function(event, ui) {

			w = $('div#page_tree').width()
			$('div#page_form').width($(document).width()-w-50);
			$('div#page_options_bottom').width($('div#page_form').width()-15);
			$('#Content').width($('#page_form').width()-180);
			$('#iframe_Content').width($('#Content').width());
		},

		stop: function(event, ui){
			refreshWYSIWYG('Content');
			saveCookie('PageManagerResize', $('div#page_tree').width());
		}
	});

}

function reloadPage(pageID)
{
    hideContext();
	cancelPage();

	if($('#dID').val() == '')
	{
		$('#page_options #Language').attr('disabled', '');
		$('#page_options #ZoneID').attr('disabled', '');
		$('#page_options #Status').attr('disabled', '');
		$('#a_dID_delete').hide();
	}
	else
	{
		$('#page_options #Language').attr('disabled', 'disabled');
		$('#page_options #ZoneID').attr('disabled', 'disabled');
		$('#page_options #Status').attr('disabled', 'disabled');
		$('#a_dID_delete').show();
	}



	url = tree_static_url+'&_action=reload&language='+$('#Language').val()+'&zoneID='+$('#ZoneID').val()+'&state='+$('#Status').val()+'&dID='+$('#dID').val();
	$('#page_tree').fadeTo(0, 0.33);

	var offs = $('#page_tree').offset({scroll:false});
	$('#page_tree_loader').css( {'position':'absolute',
						'z-index':'1000',
						'left': (offs.left),
						'top': (offs.top)} );
	$('#page_tree_loader').show();


	$.get(url, {},
		function(data){

			if(data.indexOf('@NO_TREE@') == 0)
			{
				data = str_replace('@NO_TREE@', '', data);
				data = str_replace('<script>', '', data);
				data = str_replace('</script>', '', data);

				eval(data);
				$('#page_tree_loader').hide();
				$('#page_tree').fadeTo(0.33, 1);

			}
			else
			{

				$('#page_tree').html(data);
				treeView();

				// update drag icon
				simpleTreeCollection.get(0).option.drag = true;
				dragPage();

				if(!isNaN(pageID))
				{
					// special option tabs
					if(from_tab_selected == 'options')
					{
						setTimeout("$('#"+pageID+" span').addClass('active');editPage(0, 5);", 250);
					}
					else
					{
						setTimeout("$('#"+pageID+" span').addClass('active');editPage(0);", 250);
					}
				}

				$('#page_tree_loader').hide();
				$('#page_tree').fadeTo(0.33, 1);
				counterPage();
			}

			selectSetOptionStyle('Language');
			reloadResizer();

  		}
	);
}

function addPage(parentID)
{
	hideContext();
	cancelPage();

	if(parentID == -1)
		parentID = simpleTreeCollection.get(0).getSelected().attr('id');

	//simpleTreeCollection.get(0).addNode(12, 'untitled');
	url = tree_static_url+'&_action=add_page&language='+$('#Language').val()+'&zoneID='+$('#ZoneID').val()+'&parentID='+parentID;
	$.getJSON(url, {},
		function(data){

			if(parentID == 0)
			{
				reloadPage(data['ID']);
                updateStateIcon();
			}
			else
			{
				simpleTreeCollection.get(0).addNode(data['ID'], data['Title']);
                updateStateIcon();
                editPage(0, 0);
			}

            counterPage();

  		}
	);
}

function duplicatePage()
{
	hideContext();
	cancelPage();

	parentID = simpleTreeCollection.get(0).getSelected().attr('id');

    // check sub pages ?
    duplicate_sub = 0;
    if($('.simpleTree #'+parentID+' ul').length > 0)
    {
        if((c=confirm(lang_msg_101)))
        {
            duplicate_sub = 1;
        }
    }

	url = tree_static_url+'&_action=duplicate_page&language='+$('#Language').val()+'&zoneID='+$('#ZoneID').val()+'&parentID='+parentID;
    url += '&duplicate_sub='+duplicate_sub;

	$.getJSON(url, {},
		function(data){

			// add to simple tree node
			curID = simpleTreeCollection.get(0).getSelected().attr('id');
			pID = $('.simpleTree #'+curID).parents('li').attr('id');
			$('.simpleTree #'+curID+'>span').removeClass('active').addClass('text');

			if(pID == 0)
			{
				reloadPage(data['ID']);
			}
			else
			{
				$('.simpleTree #'+pID+'>span').removeClass('text').addClass('active');

                if(duplicate_sub == 0)
                {
                    simpleTreeCollection.get(0).addNode(data['ID'], data['Title']);
                }
                else
                {
                    reloadPage();
                    return;
                }
			}

			updateStateIcon();
			editPage(0, 0);
			counterPage();

  		}
	);

}

function renamePage(){

	nodeText = getTreeNodeText();

	c = '';
	while(empty(c))
	{
		c = prompt(lang_msg_97+' ?', nodeText);
		if(empty(c))alert(lang_msg_98);
	}

	nodeID = simpleTreeCollection.get(0).getSelected().attr('id');
	url = tree_static_url+'&_action=rename_page&language='+$('#Language').val()+'&zoneID='+$('#ZoneID').val()+'&ID='+nodeID+'&XName='+c;

	$.get(url, {}, function(data){
		nodeID = simpleTreeCollection.get(0).getSelected().attr('id');
		$('li#'+nodeID+' span:first').text(c);
	});



}

function deletePage()
{
	nodeText = getTreeNodeText();
	if(c=confirm(lang_msg_50+" `"+nodeText+"` "+lang_msg_51+" ?"))
	{
		cancelPage();

		nodeID = simpleTreeCollection.get(0).getSelected().attr('id');
		url = tree_static_url+'&_action=delete_page&language='+$('#Language').val()+'&zoneID='+$('#ZoneID').val()+'&ID='+nodeID;
		$.get(url, {},
			function(data){
				simpleTreeCollection.get(0).delNode();

				// parent has no child ?
				d = explode('|', data);
				parentID = d[0];
				childCount = d[1];

				if(childCount == 0)
				{
					setClassName = 'doc';
					ul = $('li#'+parentID).parent().children();

					p = -1;
					for(k=0; k <  ul.length; k++)
					{
						if(ul[k].id == parentID)
						{
							p = k;
							break;
						}
					}

					if(p == ul.length-2)
					{
						setClassName += '-last';
					}

					$('#'+parentID).removeClass();
					$('li#'+parentID).addClass(setClassName);
				}

				counterPage();
  			}
		);
	}
}

function editPage(nodeID, selectTabs)
{
    if(typeof selectTabs === "undefined")selectTabs = 2;

	cancelPage();
	hideContext();

	if(nodeID == 0)
		nodeID = simpleTreeCollection.get(0).getSelected().attr('id');

	$('#page_form > ul').tabs('select', 0);

	var offs = $('#page_tree').offset({scroll:false});
	$('#page_tree_loader').css( {'position':'absolute', 'z-index':'1000', 'left': $('#page_tree').width()+20,'top': (offs.top)} );
	$('#page_tree_loader').show();

	// load data in the form
	url = tree_static_url+'&_action=data_form&ID='+nodeID;
	$.getJSON(url, {},
		function(data){

			lastPageData  = data;

			// load information
			n = '';
			for(key in data)
			{
				if(!in_array(key, forbidden))
				{
					if(key == 'Content' || key == 'ContentResume')
					{
						data[key] = parse_nuts_tags(data[key]);
					}

					//{
						// log('load key => '+key+' value => '+data[key]);
						$('#former #'+key).val(data[key]);
					//}
					//else
					//{
						//ed = tinyMCE.getInstanceById(key)
						//ed.setContent(data[key]);
					//}
				}
			}

			// assign navigation
			$('#page_navigation_urls').html(data['NavigationBar']);


			// change form paramater
			var options = {
							url: tree_static_url+'&_action=save_page&ID='+nodeID+'&language='+$('#Language').val(),
							beforeSubmit:  showRequest,  // pre-submit callback
							success:       showResponse  // post-submit callback
			};
			$('#former').ajaxForm(options);

			// change author info and date
			str = '<img src="../library/media/images/flag/%s.gif" align="absmiddle" /> <b><a href="mailto:%s">%s %s</a></b> (%s)';
			str = sprintf(str, strtolower(data['Language']), data['Email'], data['FirstName'], data['LastName'], data['GroupName']);

			$('#lAuthor').html(str);
			$('#lDateCreation').html(data["DateCreation"]);
			$('#lDateUpdate').html(data["DateUpdate"]);

			// load in the inputs
			$('#page_tree_loader').hide();
			$('#page_form').show();
			$('#page_options_bottom').show();

			// initWYSIWYGIFrame('Content');

			// update html editor
			// document.getElementById('_WYSIWYG_Content').click();

            // no block no view
            if(data["BlocksNb"] == 0)
                $('#tab_blocks').html("Blocks");
		    else
				 $('#tab_blocks').html("Blocks ("+data["BlocksNb"]+")");

		    // reset all blocks
			block_names = array();
			$("#tab3 select[multiple]").each(function(obj){
				$("#"+this.id+" option").attr("selected", false);
				$("#"+this.id).change();
				array_push(block_names, this.id);
			});

			for(k=0; k < data["BlocksNames"].length; k++)
            {
				bloc_name = "cf2Block"+data["BlocksNames"][k];
				arr = data[bloc_name];
                for(j=0; j < arr.length; j++)
                {
					// get parent name index
					for(z=0; z < block_names.length; z++)
					{
						// log(block_names[z], bloc_name);
						if(block_names[z] == bloc_name)
						{
							$("#asmSelect"+z).val(arr[j]);
							$("#asmSelect"+z).change();
						}
					}
				}
            }

			// init access group
			$('.checkbox_list input').attr('checked', false);
			for(k=0; k < data["PageAccess"].length; k++)
			{
				$('.checkbox_list input[value='+data["PageAccess"][k]+']').attr('checked', true);
			}

			// init Header Image
			updateHeaderImg();

			// access restrict
			trtUpdateAccessRestrict();
			trtUpdateSitemap();

			updatePublishingDate();

			// select template
			tpl = $('#former #Template').val();
			$('#tpls_preview img').removeClass();
			$('#tpls_preview img[val='+tpl+']').addClass('tpl_selected');

			// active content tab
			commentList();
			$('.ui-tabs-nav').tabs('select', selectTabs);

			// reload state image icon
			selectSetOptionClass('State');

			// assign default spellchecker language
			nutsCurrentPageLang = $('#former #Language').val();

            // refresh view
            $('#NutsPageContentViewID').change();


  		}
	);
}

// pre-submit callback
var formData = null;
function showRequest(formData, jqForm, options)
{
	// verify group access
	if($('#AccessRestricted').val() == 'YES')
	{
		if($("#fieldset_Access input[type=checkbox]:checked").length == 0)
		{
			alert(lang_msg_85);
			return false;
		}
	}

	// verify dates
	if($('#DateStartOption').val() == 'YES' && empty($('#DateStart').val()))
	{
		alert(lang_msg_94);
		return false;
	}
	if($('#DateEndOption').val() == 'YES' && empty($('#DateEnd').val()))
	{
		alert(lang_msg_95);
		return false;
	}



	if(!empty(formerOnSubmit))
	{
		ret = eval(formerOnSubmit)();
		if(!ret)return false;
	}

	// WYSIWYG interception
	for(l=0; l < formData.length; l++)
	{
		if(formData[l].name == 'Content' || formData[l].name == 'ContentResume')
		{
			formData[l].value = remove_nuts_tags(formData[l].value);
		}
	}

	// add multiple group access
	if($('#former #AccessRestricted').val() == 'YES')
	{
		vals = '';
		$('.checkbox_list :checked').each(function(){

			if(!empty(vals))vals += ';';
			vals += $(this).val();
		});

		formData[formData.length] = {name:'PageAccessX', value:vals};
	}

	// add special MetaRobots because bug name=MetaRobots for jquery tabs
	formData[formData.length] = {name:'MetaRobots', value:$('#MetaRobots').val()};


    var queryString = $.param(formData);

    // jqForm is a jQuery object encapsulating the form element.  To access the
    // DOM element for the form do this:
    // var formElement = jqForm[0];
	$('#btn_submit').attr('value', nuts_lang_msg_23);
 	$('#btn_submit').attr("disabled", true);
	$('#page_form').fadeTo(0, 0.33);

    return true;
}

// post-submit callback
function showResponse(responseText, statusText)
{
	$('#page_form').fadeTo(0.33, 1);
	$('#btn_submit').attr('value', nuts_lang_msg_21);
 	$('#btn_submit').removeAttr("disabled");

	if(responseText != 'ok')
	{
		alert(responseText);
	}
	else
	{
		// change update date
		cur_gmt_date = gmdate('Y-m-d H:i:s');
		$('#lDateUpdate').html(cur_gmt_date);

		// update name in the menu
		nodeID = simpleTreeCollection.get(0).getSelected().attr('id');
		$('li#'+nodeID+' span:first').text($('#MenuName').val());
        updateStateIcon();

		if($('#chk_Close').attr('checked') == true)
		{
			// iframe mode
			if(from_mode == 'iframe')
			{
                parent.$.fancybox.close();

                // add page mode
                if(typeof(from_action) != 'undefined' && from_action != "")
				{
					uri = "/"+$("#Language").val()+"/"+nodeID+".html";
					if($('#page_form #State').val() != 'PUBLISHED')
						uri += '?nuts_preview=1&t='+time();

					document.location.href = uri;
				}
				else // edit page
				{
                    parent.history.go(0);
				}

				return;

			}
			else
			{
				// refresh whole tree if zone ID different
				if($('#former #PageZoneID').val() != $('#former #ZoneID').val())
				{
					reloadPage();
				}
			}

			cancelPage();
		}
		else
		{
			if(from_mode == 'iframe')
			{
				return;
			}



			// document.getElementById('_WYSIWYG_Content').click();
			if(newwindow != undefined  && newwindow != false && !newwindow.closed)
			{
				newwindow.focus();
				newwindow.history.go(0);
			}

            // reload block count
            c = $('#tab3 select[multiple] option[selected]').length;
            if(c == 0)
                $('#tab_blocks').html("Blocks");
            else
                $('#tab_blocks').html("Blocks ("+c+")");

			// reinit iFrame
			initWYSIWYGIFrame('Content');

		}
	}

    // reload page
    counterPage();
}

function updateStateIcon()
{
    nodeID = simpleTreeCollection.get(0).getSelected().attr('id');
    $('li#'+nodeID+' img:not(.trigger)').remove();
    if($('#State').val() != 'PUBLISHED')
    {
        if($('#State').val() == 'DRAFT')
            str = "<img src='img/icon-tag-edit.png' align='absbottom' />";
        if($('#State').val() == 'WAITING MODERATION')
            str = "<img src='img/icon-tag-moderator.png' align='absbottom' />";
        $('li#'+nodeID+' span:first').after(str);
    }

	// add lock icon
	str2 = "<img src='img/icon-tag-moderator.png' align='absbottom' />";
	if($('#AccessRestricted').val() == 'YES')
	{
		str2 = "<img src='img/icon-lock.png' align='absbottom' /> ";
		str2 += $('li#'+nodeID+' span:first').html();
		$('li#'+nodeID+' span:first').html(str2);
	}


}

function alertLink(text)
{
	text = str_replace('\t', '    ', text);

	m = prompt(lang_msg_60+"\n\n", text);

	if(m != null && m != '' && m != undefined)
	{
		// add to textarea
		m = parse_nuts_tags(m);
		WYSIWYGAddText('Content', m);
	}

}

function linkPage()
{
	hideContext();
	nodeID = simpleTreeCollection.get(0).getSelected().attr('id');

	// copy to clipboard
	nodeText = getTreeNodeText();

	s = '<a href="{@NUTS	TYPE=\'PAGE\'	CONTENT=\'URL\'	ID=\'�\'}">{@NUTS	TYPE=\'PAGE\'	CONTENT=\'MENU_NAME\'	FROM=\''+nodeText+'\'	ID=\'�\'}</a>';
    s = str_replace('�', nodeID, s);

	// copyToClipboard(s);
	alertLink(s);
}

function linkPages()
{
	hideContext();
	nodeID = simpleTreeCollection.get(0).getSelected().attr('id');

	// copy to clipboard
	nodeText = getTreeNodeText();

	s = '{@NUTS	TYPE=\'MENU\'	CONTENT=\'ALL CHILDRENS\'	FROM=\''+nodeText+'\'	ID=\'�\'	OUTPUT=\'LI>UL\'	CSS=\'menu_�\'	ATTRIBUTES=\'\'	INCLUDE_PARENT=\'0\'}';
	s = str_replace('�', nodeID, s);

	// copyToClipboard(s);
	alertLink(s);
}

function linkZone()
{
	hideContext();
	zoneID = $('#ZoneID').val();

	// copy to clipboard
	nodeText = $('#ZoneID option:selected').text();
	nodeText = str_replace("'", '`', nodeText);

	if(zoneID == 0)
		nodeText = 'Main menu';

	s = '{@NUTS	TYPE=\'MENU\'	CONTENT=\'ALL CHILDRENS\'	FROM=\''+nodeText+'\'	ZONE_ID=\'�\'	OUTPUT=\'LI>UL\'	CSS=\'zone_�\'	ATTRIBUTES=\'\'	INCLUDE_PARENT=\'0\'}';
	s = str_replace('�', zoneID, s);

    // non zoneID for main menu
    s = str_replace("	ZONE_ID='0'", "	ZONE_ID='0'", s);


	// copyToClipboard(s);
	alertLink(s);
}

function linkPageDirect()
{
	hideContext();
	nodeID = simpleTreeCollection.get(0).getSelected().attr('id');

	// copy to clipboard
	nodeText = getTreeNodeText();

	s = '{@NUTS	TYPE=\'PAGE\'	CONTENT=\'URL\'	FROM=\''+nodeText+'\'	ID=\'�\'}';
	s = str_replace('�', nodeID, s);

	// copyToClipboard(s);
	alertLink(s);
}

function cancelPageVerification()
{
	// iframe mode
	if(from_mode == 'iframe')
	{
		parent.$.fancybox.close();
		return;
	}

	// we compare if changes are made
	changes = false;
	for(key in lastPageData)
	{
		if(!in_array(key, forbidden))
		{
			if(lastPageData[key] == null)lastPageData[key] = '';
			if(!in_array(key, array('CustomBlock','BlocksNb', 'BlocksNames')) && $('#'+key).val() != lastPageData[key])
			{
				// log('change key => '+key+' value => '+lastPageData[key]+' input => '+$('#'+key).val());
				changes = true;
				break;
			}
		}
	}

	// changes ?
	if(changes == true)
	{
		if(!(c=confirm(lang_msg_56)))
			return;
	}

    cancelPage();

}

function cancelPage()
{
	forceWYSIWYGUpdate();

    $('#page_form').hide();
	$('#page_options_bottom').hide();
}

function previewPage(node)
{
	uri = WEBSITE_URL+"/";

	uri += strtolower($("#Language").val())+"/";

	if(node == 0)
		uri += lastPageData["ID"]+".html?nuts_preview=1&tmstp="+time();
	else
		uri += simpleTreeCollection.get(0).getSelected().attr('id')+".html?nuts_preview=1&tmstp="+time();

	popupModal(uri, "preview", 1024, 750, 'toolbar=yes, statusbar=yes, menubar=no, resizable=no');
}

function counterPage()
{
	url = tree_static_url+'&_action=counter_page&language='+$('#Language').val()+'&zoneID='+$('#ZoneID').val();
	$.getJSON(url, {},
		function(data){
			initStateCounter(data[0], data[1], data[2], data[3]);
  		}
	);
}

function initStateCounter(c_all, c_publish, c_draft, c_wm)
{
	for(i=0; i < 4 ; i++)
	{
		if(i == 0)counter = c_all;
		else if(i == 1)counter = c_publish;
		else if(i == 2)counter = c_draft;
		else if(i == 3)counter = c_wm;

		txt = document.getElementById('Status').options[i].text;
		txt_t = explode(' (', txt);
		txt = trim(txt_t[0]);

		document.getElementById('Status').options[i].text = txt+' ('+counter+')';
	}
}

function dragPage()
{
	drag_mode = simpleTreeCollection.get(0).option.drag;

	if(!drag_mode)
	{
		$('#img_tree_drag').attr('src', 'img/icon-drag_on.png');
		$('#a_tree_drag').text('drag on');

		simpleTreeCollection.get(0).option.drag = true;
	}
	else
	{
		$('#img_tree_drag').attr('src', 'img/icon-drag_off.png');
		$('#a_tree_drag').text('drag off');
		simpleTreeCollection.get(0).option.drag = false;
	}

}

function imageBrowser(f, folder)
{
	uri = WEBSITE_URL+'/library/js/tiny_mce/plugins/file_browser/index.php?editor=standalone&filter=image&returnID='+f;
	if(folder != '' && typeof folder !== 'undefined')
		uri += "&path="+folder;
	popupModal(uri, 'imgBrowser', 1024, 800, '');

}

function imagePreview(f)
{
	v = $('#'+f).val();

	if(empty(v))
	{
		alert(nuts_lang_msg_61);
		return;
	}

	// uri = WEBSITE_URL+'/library/media/images/user/'+v;
	popupModal(v, 'imageBrowser', 960, 750, '');
}

function directID()
{
	v = $('#dID').val();
	if(!empty(v) && isNaN(v))
	{
		alert(lang_msg_65);
		$('#dID').focus().select();
		return false;
	}

	reloadPage();
	return false;

}


function setTreeEdit(nodeID){

    // verify parent exists for directID mode
    if(!$('.simpleTree #'+nodeID).parents('li').length){

        $('#dID').val(nodeID);
        directID();
        return;
    }



    $('.simpleTree span.active').removeClass('active').addClass('text');
    $('.simpleTree #'+nodeID+' > span').eq(0).removeClass('text').addClass('active');
    editPage(0);
}



function getTreeNodeText()
{
	nodeText = simpleTreeCollection.get(0).getSelected().text();
	nodesText = explode('\t', nodeText);
	nodeText = trim(nodesText[0]);
	nodeText = str_replace("'", "`", nodeText);


	return nodeText;

}

function optionPage()
{
	hideContext();
	nodeID = simpleTreeCollection.get(0).getSelected().attr('id');
	editPage(nodeID, 5);
}

function updatePublishingDate()
{
    $('#DateStart').hide();
    $('#DateEnd').hide();

	if($('#DateStartOption').val() == 'YES')
		$('#DateStart').show();

	if($('#DateEndOption').val() == 'YES')
		$('#DateEnd').show();


}



function refreshFolderNode(){

    nodeID = simpleTreeCollection.get(0).getSelected().attr('id');
    $('.simpleTree #'+nodeID+' ul').remove();

    uri = "index.php?mod=_page-manager&do=exec&_action=reload_page&ID="+nodeID;
    uri += '&language='+$('#page_options #Language').val();
    uri += '&zoneID='+$('#page_options #ZoneID').val();
    uri += '&state=';

    str = '<ul class="ajax">';
    str += '    <li class="line">&nbsp;</li>';
    str += '    <li class="doc-last">{url:'+uri+'}</li>';
    str += '    <li class="line-last"></li>';
    str += '</ul>';


    $('.simpleTree #'+nodeID+' span').after(str);
    simpleTreeCollection.get(0).deleteAjaxCache(nodeID);

    $('.simpleTree #'+nodeID+' .trigger').click();

}







