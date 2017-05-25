<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsEDMGroup'));

$plugin->formAddFieldText('Name', $lang_msg[1], true, 'ucfirst');
$plugin->formAddFieldTextArea('Description', "", false);

// users ***************************************************************************************************************
Query::factory()->select('ID, LastName, FirstName')
                ->from('NutsUser')
                ->where("NutsGroupID IN(SELECT ID FROM NutsGroup WHERE Deleted = 'NO' AND BackOfficeAccess = 'YES')")
                ->where("NutsGroupID IN(SELECT NutsGroupID FROM NutsMenuRight WHERE NutsGroupID = NutsUser.NutsGroupID AND Name = 'exec' AND NutsMenuID = (SELECT ID FROM NutsMenu WHERE Deleted = 'NO' AND Name = '_edm'))")
                ->order_by('LastName, FirstName')
                ->execute();

$select_users = '<select style="width: 99%" name="users[]">';
$select_users .= '<option></option>';
while($r = $nuts->dbFetch())
{
    $name = "{$r['LastName']} {$r['FirstName']} (#{$r['ID']})";
    $select_users .= '<option value="'.$r['ID'].'">'.$name.'</option>'."\n";
}
$select_users .= '</select>';



$table = <<<EOF

<table id="users" class="datagrid" cellspacing="1">
<thead>
<tr>
    <th style="min-width:350px;">{$lang_msg[1]}</th>
    <th><a class="datagrid_btn_add" href="javascript:userAdd();">&nbsp;</a></th>
</tr>
</thead>
<tbody>

    <tr id="tr_0">
        <td>$select_users</td>
        <td><a class="datagrid_btn_delete" href="javascript:userDelete(0);">&nbsp;</a></td>
    </tr>


</tbody>
</table>
EOF;


$plugin->formAddFieldsetStart("Users", $lang_msg[2], array('html' => $table));
$plugin->formAddFieldsetEnd();

$plugin->formAddException('users*');


if($_POST)
{
    if(!@is_array($_POST['users'])) $_POST['users'] = array();

    $tmp = array();
    foreach($_POST['users'] as $user)
    {
        $user = (int)$user;
        if($user && !in_array($user, $tmp))
            $tmp[] = $user;
    }


    if(count($tmp) == 0)
    {
        $nuts->addError('users[]', $lang_msg[3]);
    }
}

