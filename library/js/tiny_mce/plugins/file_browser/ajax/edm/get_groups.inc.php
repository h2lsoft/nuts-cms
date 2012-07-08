<?php
/**
 * Groups
 */

$folder = @urldecode($_POST['folder']);
if(!empty($folder) && $folder[strlen($folder)-1] != '/')$folder .= '/';

// right access verification
if(EDM_ADMINISTRATOR == false)
{
    edmLog('RIGHTS', 'ERROR', $folder);
    systemError(translate("Action not allowed !"));
}

// check path
if(!is_dir(WEBSITE_PATH.$folder) || !preg_match("#^$upload_pathX#", $folder))
{
    edmLog('RIGHTS', 'ERROR', $folder, "Folder not exists");
    systemError(translate("The folder path was tampered with !"));
}

// list all groups
Query::factory()->select('NutsEDMGroupID')
                ->from('NutsEDMFolderRights')
                ->where("Folder = '$folder'")
                ->where("Type = 'GROUP'")
                ->where('NutsEDMGroupID != 0')
                ->execute();

$groups_exluded = array();
if($nuts->dbNumRows())
{
    while($row = $nuts->dbFetch())
    {
        if(!in_array($row['NutsEDMGroupID'], $groups_exluded))
            $groups_exluded[] = $row['NutsEDMGroupID'];
    }
}
$groups_exluded[] = 0; # to avoid query bugs

// get correct group
$ex = join(',', $groups_exluded);
Query::factory()->select("* , (SELECT COUNT(*) FROM NutsEDMGroupUser WHERE Deleted = 'NO' AND NutsEDMGroupID = NutsEDMGroup.ID) AS NbUsers")
                ->from('NutsEDMGroup')
                ->where("ID NOT IN($ex)")
                ->execute();

$html = "";
while($row = $nuts->dbFetch())
{
    $name_l = strtolower($row['Name']);

    $html .= <<<EOF

        <tr groupname="$name_l">
            <td class="center"><input type="checkbox" value="{$row['ID']}" /></td>
            <td>{$row['Name']}</td>
            <td>{$row['Description']}</td>
            <td class="center">{$row['NbUsers']}</td>
        </tr>

EOF;


}

$resp['result'] = 'ok';
$resp['html'] = $html;

?>