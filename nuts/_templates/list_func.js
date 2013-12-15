// rewrite uri with system
$("#list th a").each(
	function(){
		$(this).attr('href', "javascript:system_goto('"+$(this).attr('href')+"', 'list');");
        $(this).parents('th').addClass('ordered').click(function(){
            document.location.href = $(this).children('a').attr('href');
        });
	}
);

// add selected
$("#list th.ordered").find("img[src$='actived.gif']").parents('th').addClass('selected');


// table
$('a.tt').tooltip({
    track: false,
    delay: 0,
    showURL: false,
    showBody: " - ",
    opacity: 0.85
});


function listTrColor()
{
	// click event
	$('#list:not(.listDnd) tbody tr td[noclick!="1"]').click(function () {

		if($("#list tbody .listDnd").length)return;

		if($(this).parents('tr').hasClass('tr_selected'))
		{
			$(this).parents('tr').removeClass('tr_selected')
		}
		else
		{
			$(this).parents('tr').addClass('tr_selected');
		}
	});

}

listTrColor();





// drag and drop table
if($("#list tbody .listDnd").length)
{
	$("#list tbody .listDnd < td").css('cursor', 'move');

	$('#list').sortable({

		axis:'y',
		opacity: 0.8,
		cursor: 'move',

		// forceHelperSize: true,
		// forcePlaceholderSize: true,
		helper: function(e, tr) {
			var $originals = tr.children();
			var $helper = tr.clone();
			$helper.children().each(function(index)
			{
				// Set helper cell sizes to match the original sizes
				$(this).width($originals.eq(index).width())
			});

			return $helper;
		},

		items: 'tr.row',
		update: function(event, ui) {

			list = $('#list').sortable("toArray");
			uri = $("#list .listDnd").attr('uri');
			system_position(uri, list);

		}

	});
}


// batch actions
function listBatchActionExecute()
{
    // no action
    if(empty($('#select_batch_actions').val()))
    {
        msg = "You must select your action";
        if(nutsUserLang == 'fr')
            msg = "Vous devez choisir votre action";

        alert(msg);
        $("#select_batch_actions").focus();
        return;
    }

    // no ID
    if($('#list_container .list_batch:checked').length == 0)
    {
        msg = "You must select at least one record";
        if(nutsUserLang == 'fr')
            msg = "Vous devez sélectionner au moins un enregistrement";
        alert(msg);
        return;
    }

    IDS = '';
    $('#list_container .list_batch:checked').each(function(){

        if(!empty(IDS))IDS += ';';
        IDS += $(this).val();
    });


    uri = $('#select_batch_actions').val();
    uri += '&IDS='+IDS;

    $.get(uri, function(resp){

        if(resp.indexOf('ko;') == 0)
        {
            msg = str_replace('ko;', '', resp);
            notify('error', msg);
            return;
        }
        else if(resp != 'ok')
        {
            msg = "Unknow error please retry";
            if(nutsUserLang == 'fr')
                msg = "Erreur inconnue, merci de recommencer";
            notify('error', msg);
            return;
        }
        else if(resp = 'ok')
        {
            r = (nutsUserLang == 'fr') ? 'enregistrement(s)' : 'records(s)';
            msg_ok = $('#select_batch_actions option:selected').text()+" : "+$('#list_container .list_batch:checked').length+" "+r+"(s)";

            p = parseUri(uri);
            if(!empty($('#select_batch_actions option:selected').attr('data-msg-ok')))
            {
                msg_ok = $('#select_batch_actions option:selected').attr('data-msg-ok');
                msg_ok = str_replace('{X}', $('#list_container .list_batch:checked').length, msg_ok);
            }

            notify('ok', msg_ok);
            system_refresh();
        }

    });

}

function listBatchActionCheck()
{
    if($('#list_batch_all').is(':checked'))
    {
        $('#list_container .list_batch').attr('checked', true);
    }
    else
    {
        $('#list_container .list_batch').attr('checked', false);
    }
}


// init toggle column
$('list_column_toggler_menu').hide();
if($('#list_column_toggler_menu').length == 1)
{

    tmp_list_hidden_columns = array();

    // init array
    list_key_exists = false;
    for(i=0; i < list_toggle_columns.length; i++)
    {
        if(list_toggle_columns[i][0] == current_plugin_name)
        {
            list_key_exists = true;
            tmp_list_hidden_columns = explode('µ', list_toggle_columns[i][1]);
            break;
        }
    }
    if(!list_key_exists)
    {
        list_toggle_columns[list_toggle_columns.length] = [current_plugin_name, ''];
    }



    str = '';
    $('#list th').each(function(index){

        label = $(this).html();
        label = str_replace('&nbsp;', ' ', label);
        label = strip_tags(label);
        label = trim(label);

        column = $(this).attr('data-column');

        if(empty(label) && !empty(column))
        {
            label = '('+column+')';
        }

        if(!empty(label))
        {
            label = str_replace("'", "\'", label);


            checked = ' checked';
            if(in_array(column, tmp_list_hidden_columns))
            {
                checked = '';
                $("#list th[data-column="+column+"]").toggle();
                $("#list td[data-column="+column+"]").toggle();
            }

            tpl = '<label><input type="checkbox" data-column="'+column+'" onclick="listColumnToggle(\''+column+'\', this);" '+checked+' /> '+label+'</label>';
            str += tpl;
        }
    });

    $('#list_column_toggler_menu').html(str);
}




function listColumnToggle(colID, obj)
{
    visible = obj.checked;

    // check at leat one column visible
    if(!$('#list_column_toggler_menu input:checked').length)
    {
        msg = "One column must be visible at least";
        if(nutsUserLang == 'fr')
            msg = "Au moins une colonne doit être visible";
        obj.checked = true;
        alert(msg);
        return;
    }

    $("#list th[data-column="+colID+"]").toggle();
    $("#list td[data-column="+colID+"]").toggle();


    // save all hidden
    for(i=0; i <  list_toggle_columns.length; i++)
    {
        if(list_toggle_columns[i][0] == current_plugin_name)
        {
            not_checked = array();

            $('#list_column_toggler_menu input:not(:checked)').each(function(){
                not_checked[not_checked.length] = $(this).attr('data-column');
            });

            list_toggle_columns[i][1] = join('µ', not_checked);
            break;
        }
    }
}

// pre copy
$('#list pre.copy').click(function(){

    copy2Clipboard($(this).text());

});
