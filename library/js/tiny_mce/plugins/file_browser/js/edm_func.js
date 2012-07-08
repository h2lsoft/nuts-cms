
var rights_folder = "";
function showRights(folder){

    rights_folder = folder;

    folder_name = str_replace(pathX , '', folder);
    if(folder_name == '')folder_name = root_name;
    folder_name = $.MediaBrowser.trim(folder_name, '/');

    $('#rights_window span.folder_name').text(folder_name);

    nWindowOpen('rights_window');

    tr_loader = '<tr><td class="n_loader" colspan="8"></td></tr>';
    $('#rights_window .n_table tbody').html(tr_loader);
    $('#rights_window input').attr('disabled', true);
    $('#right_subfolders').attr('checked', false);

    uri = getAjaxUri();
    uri += '&action=get_rights';
    $.post(uri, {folder:urlencode(rights_folder)}, function(resp){

        if(resp.result == 'ko')
        {
            $('#rights_window .n_table tbody').html("");
            $.MediaBrowser.showMessage(resp.message, "error");
        }
        else
        {
            $('#rights_window input').attr('disabled', false);
            $('#rights_window .n_table tbody').html(resp.html);
        }
    }, 'json');
}

function rightsDelete(recId){

    if(!confirm(right_delete_confirm))
        return;


    uri = getAjaxUri();
    uri += '&action=rights_delete';

    $.post(uri, {folder:urlencode(rights_folder), rightID:recId}, function(resp){
        if(resp.result == 'ko')
        {
            $.MediaBrowser.showMessage(resp.message, "error");
        }
        else
        {
            $('#rights_window .n_table tbody tr[recId='+recId+']').remove();
        }
    }, 'json');

}


function showGroups(){

    nWindowOpen('groups_window');
    tr_loader = '<tr><td class="n_loader" colspan="4"></td></tr>';

    $('#groups_window .n_table tbody').html(tr_loader);
    $('#groups_window input').attr('disabled', true);
    $('#groups_window .search').val('');

    uri = getAjaxUri();
    uri += '&action=get_groups';
    $.post(uri, {folder:urlencode(rights_folder)}, function(resp){

        if(resp.result == 'ko')
        {
            $('#groups_window .n_table tbody').html("");
            $.MediaBrowser.showMessage(resp.message, "error");
        }
        else
        {
            $('#groups_window input').attr('disabled', false);
            $('#groups_window .n_table tbody').html(resp.html);
        }

    }, 'json');
}


function showUsers(){

    nWindowOpen('users_window');
    tr_loader = '<tr><td class="n_loader" colspan="3"></td></tr>';

    $('#users_window .n_table tbody').html(tr_loader);
    $('#users_window input').attr('disabled', true);
    $('#users_window .search').val('');

    uri = getAjaxUri();
    uri += '&action=get_users';
    $.post(uri, {folder:urlencode(rights_folder)}, function(resp){

        if(resp.result == 'ko')
        {
            $('#users_window .n_table tbody').html("");
            $.MediaBrowser.showMessage(resp.message, "error");
        }
        else
        {
            $('#users_window input').attr('disabled', false);
            $('#users_window .n_table tbody').html(resp.html);
        }

    }, 'json');


}



function attachGroups(){

    groups = "";
    $('#groups_window .n_table tbody tr:visible input:checked').each(function(){
        groups += $(this).val()+";";
    });

    if(groups == '')
    {
        msg = group_select_error;
        alert(msg);
        return;
    }


    uri = getAjaxUri();
    uri += '&action=groups';
    $.post(uri, {folder:urlencode(rights_folder), groups:groups}, function(resp){

        if(resp.result == 'ko')
        {
            $.MediaBrowser.showMessage(resp.message, "error");
        }
        else
        {
            showRights(rights_folder);
            nWindowClose('groups_window');
        }

    }, 'json');

}

function attachUsers(){

    users = "";
    $('#users_window .n_table tbody tr:visible input:checked').each(function(){
        users += $(this).val()+";";
    });

    if(users == '')
    {
        msg = user_select_error;
        alert(msg);
        return;
    }

    uri = getAjaxUri();
    uri += '&action=users';
    $.post(uri, {folder:urlencode(rights_folder), users:users}, function(resp){

        if(resp.result == 'ko')
        {
            $.MediaBrowser.showMessage(resp.message, "error");
        }
        else
        {
            showRights(rights_folder);
            nWindowClose('users_window');
        }

    }, 'json');

}


function setRights(){

    rights = "";
    $('#rights_window input[name^=rights]').each(function(){

        recId = $(this).attr('recId');
        right = $(this).val();
        state = ($(this).is(':checked')) ? 1 : 0;

        rights += recId+';'+right+';'+state+';';
        rights += "\n";
    });


    recursive = ($('#right_subfolders').is(':checked')) ? 1 : 0;

    uri = getAjaxUri();
    uri += '&action=rights';
    $.post(uri, {folder:urlencode(rights_folder),rights:rights, recursive:recursive}, function(resp){

        if(resp.result == 'ko')
        {
            $.MediaBrowser.showMessage(resp.message, "error");
        }
        else
        {
            $.MediaBrowser.showMessage(resp.message, "ok");
        }

    }, 'json');

}
