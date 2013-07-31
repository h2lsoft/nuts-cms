function zoneGetRights()
{
    ID = formGetCurrentID();
    if(!ID)return;


    uri = ajaxerUrlConstruct('get_rights', '_zone-manager', 'list', "&ID="+ID);
    $.getJSON(uri, function(groups){

         for(i=0; i < groups.length; i++)
         {
             $('#checkbox_list_Rights input[type=checkbox][value='+groups[i]+']').attr('checked', true);
         }

    });

}



zoneGetRights();