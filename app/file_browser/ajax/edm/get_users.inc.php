<?php
/**
 * Users list
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
Query::factory()->select('NutsUserID')
                ->from('NutsEDMFolderRights')
                ->where("Folder = '".addslashes($folder)."'")
                ->where("Type = 'USER'")
                ->where('NutsUserID != 0')
                ->execute();

$users_exluded = array();
if($nuts->dbNumRows())
{
    while($row = $nuts->dbFetch())
    {
        if(!in_array($row['NutsUserID'], $users_exluded))
            $users_exluded[] = $row['NutsUserID'];
    }
}
$users_exluded[] = 0; # to avoid query bugs

// get correct users that group access is allowed
$ex = join(',', $users_exluded);

// Nuts user that group allwed and _edm plugin allowed with exec right and not selected
$sql = "SELECT
              NutsUser.ID,
              NutsUser.LastName,
              NutsUser.FirstName
        FROM
              NutsGroup,
              NutsUser
        WHERE
              NutsUser.Deleted = 'NO' AND
              NutsGroup.Deleted = 'NO' AND
              NutsGroup.BackofficeAccess = 'YES' AND
              NutsGroup.ID = NutsGroupID AND
              NutsGroupID IN(SELECT NutsGroupID FROM NutsMenuRight WHERE NutsMenuID = (SELECT ID FROM NutsMenu WHERE Deleted = 'NO' AND Name = '_edm') AND Name = 'exec') AND
              NutsUser.ID NOT IN($ex)
        ORDER BY
              NutsUser.LastName,
              NutsUser.FirstName";

$nuts->doQuery($sql);

$html = "";
$qID = $nuts->dbGetQueryID();
while($row = $nuts->dbFetch())
{
    // $name = $row['LastName'].' '.$row['FirstName'];
    $name = strtoupper($row['LastName']);
    $name .= " ".$row['FirstName'][0].".";

    $name_l = strtolower($name);

    // get edm groups
    Query::factory()->select('Name')
                    ->from('NutsEDMGroup')
                    ->where("ID IN(SELECT NutsEDMGroupID FROM NutsEDMGroupUser WHERE Deleted = 'NO' AND NutsUserID = {$row['ID']})")
                    ->execute();
    $groups = "";
    while($row2 = $nuts->dbFetch())
    {
        if(!empty($groups)) $groups .= ', ';
        $groups .= "{$row2['Name']}";
    }

    $html .= <<<EOF

        <tr username="$name_l">
            <td class="center"><input type="checkbox" value="{$row['ID']}" /></td>
            <td class="min30"><strong>$name (#{$row['ID']})</strong></td>
            <td>{$groups}</td>
        </tr>
EOF;


    $nuts->dbSetQueryID($qID);
}


$resp['result'] = 'ok';
$resp['html'] = $html;


?>