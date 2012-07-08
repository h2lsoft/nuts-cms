<?php
/**
 * get rights folder
 */

$folder = @urldecode($_POST['folder']);
if(!empty($folder) && $folder[strlen($folder)-1] != '/')$folder .= '/';

// right access verification
if(EDM_ADMINISTRATOR == false)
{
    $msg = "Action not allowed !";
    edmLog('RIGHTS', 'ERROR', $folder, $msg);

    systemError(translate($msg));
}

// check path
if(!is_dir(WEBSITE_PATH.$folder) || !preg_match("#^$upload_pathX#", $folder))
{
    edmLog('RIGHTS', 'ERROR', $folder, "Folder not exists");
    systemError(translate("The folder path was tampered with !"));
}

// list all rights for folder

// Everybody rights
Query::factory()->select('*')
                ->from('NutsEDMFolderRights')
                ->where('NutsEDMGroupID = 0')
                ->where('NutsUserID = 0')
                ->where("Folder = '$folder'")
                ->limit(1)
                ->execute();

$everybody_rights = array();
if($nuts->dbNumRows())
{
    $everybody_rights = $nuts->dbFetch();
}

$everybody_lbl = translate('Everybody');
$list_checked = (@$everybody_rights['LIST'] == 'YES') ? 'checked' : '';
$read_checked = (@$everybody_rights['READ'] == 'YES') ? 'checked' : '';
$modify_checked = (@$everybody_rights['MODIFY'] == 'YES') ? 'checked' : '';
$delete_checked = (@$everybody_rights['DELETE'] == 'YES') ? 'checked' : '';
$write_checked = (@$everybody_rights['WRITE'] == 'YES') ? 'checked' : '';
$upload_checked = (@$everybody_rights['UPLOAD'] == 'YES') ? 'checked' : '';

$str = <<<EOF
<tr class="group_everybody">
    <td><span class="group"></span> <b>$everybody_lbl</b></td>
    <td class="center"><input type="checkbox" recId="0" name="rights[0]['list']" value="list" $list_checked /></td>
    <td class="center"><input type="checkbox" recId="0" name="rights[0]['read']" value="read" $read_checked /></td>
    <td class="center"><input type="checkbox" recId="0" name="rights[0]['modify']" value="modify" $modify_checked /></td>
    <td class="center"><input type="checkbox" recId="0" name="rights[0]['delete']" value="delete" $delete_checked /></td>
    <td class="center"><input type="checkbox" recId="0" name="rights[0]['write']" value="write" $write_checked /></td>
    <td class="center"><input type="checkbox" recId="0" name="rights[0]['upload']" value="upload" $upload_checked /></td>
    <td class="center">&nbsp;</td>
</tr>
EOF;

// other groups / users
Query::factory()->select("*, (IF(Type='GROUP', (SELECT Name FROM NutsEDMGroup WHERE Deleted = 'NO' AND ID = NutsEDMGroupID), (SELECT CONCAT(LastName,' ',FirstName) FROM NutsUser WHERE Deleted = 'NO' AND ID = NutsUserID))) AS DynName")
                ->from('NutsEDMFolderRights')
                ->where("((NutsEDMGroupID != 0 AND Type = 'GROUP') OR (NutsUserID != 0 AND Type = 'USER'))")
                ->where("Folder = '$folder'")
                ->order_by('DynName')
                ->execute();

while($row = $nuts->dbFetch())
{
    if(!empty($row['DynName']))
    {
        $list_checked = ($row['LIST'] == 'YES') ? 'checked' : '';
        $read_checked = ($row['READ'] == 'YES') ? 'checked' : '';
        $modify_checked = ($row['MODIFY'] == 'YES') ? 'checked' : '';
        $delete_checked = ($row['DELETE'] == 'YES') ? 'checked' : '';
        $write_checked = ($row['WRITE'] == 'YES') ? 'checked' : '';
        $upload_checked = ($row['UPLOAD'] == 'YES') ? 'checked' : '';

        $type = strtolower($row['Type']);
        $str .= <<<EOF
                        <tr recId="{$row['ID']}">
                            <td><span class="$type"></span> {$row['DynName']}</td>
                            <td class="center"><input type="checkbox" recId="{$row['ID']}" name="rights[{$row['ID']}]['list']" value="list" $list_checked /></td>
                            <td class="center"><input type="checkbox" recId="{$row['ID']}" name="rights[{$row['ID']}]['read']" value="read" $read_checked /></td>
                            <td class="center"><input type="checkbox" recId="{$row['ID']}" name="rights[{$row['ID']}]['modify']" value="modify" $modify_checked /></td>
                            <td class="center"><input type="checkbox" recId="{$row['ID']}" name="rights[{$row['ID']}]['delete']" value="delete" $delete_checked /></td>
                            <td class="center"><input type="checkbox" recId="{$row['ID']}" name="rights[{$row['ID']}]['write']" value="write" $write_checked /></td>
                            <td class="center"><input type="checkbox" recId="{$row['ID']}" name="rights[{$row['ID']}]['upload']" value="upload" $upload_checked /></td>
                            <td class="center"><a href="javascript:rightsDelete({$row['ID']})" class="delete"></a></td>
                        </tr>
EOF;
    }
}


$resp['result'] = 'ok';
$resp['html'] = $str;



?>