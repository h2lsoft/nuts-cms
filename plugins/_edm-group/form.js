var user_tr_html = '';
var last_timer = '';

function userInit(){

    user_tr_html = $('#users tbody tr:eq(0)')[0].outerHTML;

    CUR_ID = formGetCurrentID();
    $.getJSON('?mod=_edm-group&do=list&ajax=1&_action=user_init&ID='+CUR_ID, {}, function(data){

        if(data.length > 0)
        {
            for(i=0; i < data.length; i++)
            {
                if(i == 0)
                {
                    $('#users tbody tr:eq(0) select').val(data[i]);
                }
                else
                {
                    userAdd();
                    $('#users tbody tr:last-child select').val(data[i]);
                }
            }
        }
    });
}

function userAdd(){

    tr = user_tr_html;

    t = time();
    if(t == last_timer)t =  t + 1;
    last_timer =  t;

    tr = str_replace('(0)', '('+t+')', tr);
    tr = str_replace('tr_0', 'tr_'+t, tr);
    $('#users tbody').append(tr);

}


function userDelete(curID){

    if($('#users tbody tr').length == 1)
    {
        $('#users tbody tr:eq(0) select').val('');
    }
    else
    {
        $('#users tbody #tr_'+curID).remove();
    }
}


userInit();


