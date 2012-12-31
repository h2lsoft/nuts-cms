function commentPostToggle(){
	
	if(!$('#comment_post').is(':visible'))
	{
		$('#comment_post').show();
		$('#CommentText').focus();
	}
	else
	{
		$('#comment_post').hide();
	}
}


function commentPostSubmit()
{
	if(empty($('#CommentName').val()))
	{
		//alert("You must enter your name for your comment");
		alert(lang_msg_74);
		$('#CommentName').focus();
		return;
	}
	if(empty($('#CommentText').val()))
	{
		//alert("You must enter the text for your comment");
		alert(lang_msg_75);
		$('#CommentName').focus();
		return;
	}

	nodeID = simpleTreeCollection.get(0).getSelected().attr('id');
	uri = "index.php?mod=_page-manager&do=exec&_action=comment_new&ID="+nodeID;

	// $('#CommentSubmit').val('...');
	$('#CommentSubmit').attr('disabled', true);
	$.post(uri, {action: 'comment_new', ID:nodeID, Language: $('#Language').val(), CommentName:$('#CommentName').val(), CommentText:$('#CommentText').val()}, function (data){

		//$('#CommentSubmit').val('...');
		$('#CommentSubmit').attr('disabled', false);
		$('#CommentText').val('');
		$('#comment_post').hide();

		commentList();
		
	});



}


function commentList(){

	updateCommentTab();

	$('#comments').html(nuts_lang_msg_23);
	$('#comment_post').hide();
	$('#CommentText').val('');

	nodeID = simpleTreeCollection.get(0).getSelected().attr('id');
	uri = "index.php?mod=_page-manager&do=exec&_action=comment_list&ID="+nodeID;
	$.getJSON(uri, {}, function(json_data){

		tpl = '<div class="comment" id="comment[ID]">';
		tpl += '<img src="[Avatar]" class="c_avatar" />';
		tpl += '<b>Date :</b> [Date]<br />';
		tpl += '<b>Author :</b> <span id="commentAuthor[ID]">[Name]</span> (<a href="mailto:[Email]">[Email]</a>)<br />';
		tpl += '<b>Website :</b> <a href="[Website]" target="_blank">[Website]</a><br />';
		tpl += '<b>IP :</b> <a href="http://www.geoiptool.com/en/?IP=[IP]" target="_blank">[IP]</a><br />';
		tpl += '<br />';
		tpl += '<span id="commentText[ID]">[Text]</span>';
		tpl += '<div class="comment_options">';

		// cite
		tpl += '<img src="/nuts/img/icon-user.gif" /> ';
		tpl += '<a href="javascript:commentCite([ID]);">'+lang_msg_79+'</a>';

		tpl += ' | ';

		// visible
		tpl += '<img src="/nuts/img/[VisibilityImage]" class="ImgVisible" /> ';
		tpl += '<a href="javascript:commentVisible([ID]);">'+lang_msg_76+'</a>';

		tpl += ' | ';
		
		// delete
		tpl += '<img src="/nuts/img/list_delete.png" /> ';
		tpl += '<a href="javascript:commentDelete([ID]);">'+lang_msg_77+'</a>';

		tpl += '</div>';
		tpl += '</div>';

		str = '';
		str_tmp = '';
		for(k=0; k < json_data.length; k++)
		{
			str_tmp = tpl;
			str_tmp = str_replace('[ID]', json_data[k]['ID'], str_tmp);
			str_tmp = str_replace('[Avatar]', json_data[k]['Avatar'], str_tmp);
			str_tmp = str_replace('[IP]', json_data[k]['IP'], str_tmp);
			str_tmp = str_replace('[Date]', json_data[k]['Date'], str_tmp);
			str_tmp = str_replace('[Name]', json_data[k]['Name'], str_tmp);
			str_tmp = str_replace('[Website]', json_data[k]['Website'], str_tmp);
			str_tmp = str_replace('[Email]', json_data[k]['Email'], str_tmp);
			str_tmp = str_replace('[Text]', json_data[k]['Message'], str_tmp);
			str_tmp = str_replace('[VisibilityImage]', json_data[k]['VisibilityImage'], str_tmp);

			str += str_tmp;
		}

		$('#comments').html(str);

		// update comment nb
		str = $(".ui-tabs-nav-item a").eq(6).text();
		strt = explode(' (', str);
		str = strt[0]+' ('+json_data.length+')';
		$(".ui-tabs-nav-item a").eq(6).text(str);
	});
	


}


function commentVisible(cID){

	$("#comment"+cID).fadeTo(0, 0.2);
	nodeID = simpleTreeCollection.get(0).getSelected().attr('id');
	uri = "index.php?mod=_page-manager&do=exec&_action=comment_visible&ID="+nodeID;

	$.get(uri, {CommentID:cID}, function(data){
		
		$("#comment"+cID).fadeTo(0, 1);

		src = 'img/YES.gif';
		if(data == 'NO')
			src = 'img/icon-error.gif';

		$("#comment"+cID+" .ImgVisible").attr('src', src);
		
	});

}


function commentDelete(cID){

	if((c=confirm(lang_msg_78)))
	{
		$("#comment"+cID).fadeTo(0, 0.2);

		nodeID = simpleTreeCollection.get(0).getSelected().attr('id');
		uri = "index.php?mod=_page-manager&do=exec&_action=comment_delete&ID="+nodeID;

		$.get(uri, {CommentID:cID}, function(){

			$("#comment"+cID).remove();

			// update comment nb
			str = $(".ui-tabs-nav-item a").eq(5).text();
			strt = explode(' (', str);
			str = strt[0]+' ('+$(".comment").length+')';
			$(".ui-tabs-nav-item a").eq(5).text(str);
		});
	}

}

function commentCite(cID){	

	v = '<blockquote>';
	v += '<span>'+$("#commentAuthor"+cID).text()+' : </span>\n\n';
	v += $("#commentText"+cID).text()+'\n';
	v += '</blockquote>\n\n';


	$("#CommentText").val(v);
	commentPostToggle();

}

function updateCommentTab()
{
	if(!$('#Comments').parent('p').is(':visible'))
		$('#Comments').val('NO');

	if($('#Comments').val() == 'YES' && $('#Sitemap').val() == 'YES')
	{
		$('.ui-tabs-nav-item').eq(6).show();
	}
	else
	{
		$('.ui-tabs-nav-item').eq(6).hide();
	}





}

