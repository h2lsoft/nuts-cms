// ucfirst
$('#search_form .ucfirst').blur(function(e) {$(this).val(ucfirst($(this).val()));});

// upper
$('#search_form .upper').blur(function(e) {$(this).val(strtoupper($(this).val()));});

// lower
$('#search_form .lower').blur(function(e) {$(this).val(strtolower($(this).val()));});


// list engine
$('#search_form').bind('submit', function(){

	uri = search_uri;
    $('#search_form input[type=checkbox]:checked').each(function(){

        id = str_replace('_checkbox', '', $(this).attr('id'));

        // add operator
        uri += id + '_operator=' + $('#search_form #'+id+'_operator').val()+'&';

        // add select|text
        if(!$('#search_form #'+id).length)
            uri += id + '=' + $('#search_form #se_'+id).val()+'&';
        else
            uri += id + '=' + $('#search_form #'+id).val()+'&';
    });

	system_goto(uri, 'list');
	return false;

});


// detect enter key
$('#search_form input, #search_form select').keyup(function(e) {
	if(e.keyCode == 13){
		$('#search_form').submit();
	}
});



// ajax autocompletion
$('#search_form input.ajax_autocomplete').each(function(){

	field = str_replace('se_', '', this.id);
	$("#se_"+field).autocomplete(
									ajax_ac_uri+field,
									{
										delay:300,
										minChars:3,
										matchSubset:false,
										matchContains:false,
										cacheLength:0,
										autoFill:true
									}
								);
});


// transform select into combo
$('#search_form select.ac').each(function(){

	str =  this.id;
	if(str.indexOf('_operator') == -1 && str.indexOf('ID') == -1)
	{
		// get array form select
		tmp_arr = array();
		$('#search_form select#'+str+" option").each(function(){
			if(!empty($(this).attr('value')))
				tmp_arr[tmp_arr.length] = $(this).attr('value');
		});

	// replace select by input
	$('#search_form select#'+str).after('<input type="text" name="'+str+'" id="se_'+str+'" value="" />').remove();

		// autocomplete
		$('#search_form input#se_'+str).autocomplete(tmp_arr, {
			width: 400,
			highlight: false,
			multiple: false,
			scroll: true,
			scrollHeight: 300
		});
	}
});

// list
// $("#search_form .tt[title!='']").css('text-decoration', 'underline');
$("#search_form .tt[title!='']").append(' <img src="img/icon_help_mini.gif" align="absmiddle" />');
$("#search_form .tt[title!='']").tooltip({track: true, delay: 0, showURL: false, showBody: " - ",opacity: 0.85});


// image_preview
$('.image_preview').click(function(){
	src = $(this).attr('src');
	src = str_replace('/thumb_', '/', src);
	imageBox(src);
});


shortcut.remove('Escape');
shortcut.add('Escape', function(){

    if($('#list_search_content').is(':visible'))
        $('#list_search_content').slideToggle('fast');

    if($('#nuts_canvas').is(':visible'))
        imageBoxClose();

});


