<?php
/**
 * Tree treatment
 */

$html = "";

// full view for administrator
if(EDM_ADMINISTRATOR == true)
{
    // $dirs = getDirTree($upload_path, false);
    $formatted_dirs = getDirTreeX($upload_path);
}
else
{
    // get group allowed by user
    $sql = "SELECT
                    NutsEDMGroupID
            FROM
                    NutsEDMGroup,
                    NutsEDMGroupUser
            WHERE
                    NutsEDMGroup.ID = NutsEDMGroupUser.NutsEDMGroupID AND
                    NutsEDMGroupUser.NutsUserID = {$_SESSION['NutsUserID']} AND
                    NutsEDMGroup.Deleted = 'NO' AND
                    NutsEDMGroupUser.Deleted = 'NO'";
    $nuts->doQuery($sql);

    $group_user = '';
    while($r = $nuts->dbFetch())
    {
        if(!empty($group_user))$group_user .= ', ';
        $group_user .= $r['NutsEDMGroupID'];
    }

    $sql_added = '';
    if(!empty($group_user))
        $sql_added = " OR (Type = 'GROUP' AND NutsEDMGroupID IN($group_user)) ";

    // right for everybody, user or group
    Query::factory()->select('DISTINCT Folder')
                    ->from('NutsEDMFolderRights')
                    ->where("`LIST` = 'YES'")
                    ->where("(
                                (Type = 'GROUP' AND NutsEDMGroupID = 0 AND NutsUserID = 0) OR
                                (Type = 'USER' AND NutsUserID = {$_SESSION['NutsUserID']})
                                $sql_added
                              )")
                    ->order_by('Folder')
                    ->execute();

    $formatted_dirs = array();
    while($r = $nuts->dbFetch())
    {
        if(is_dir(WEBSITE_PATH.$upload_pathX))
        {
            $dir = str_replace($upload_pathX, '/', $r['Folder']);

            if($dir != '/')
            {
                $dir[0] = '';
                $dir[strlen($dir)-1] = '';
                $dir = trim($dir);

                $tmp = explode('/', $dir);
                $formatted_dirs[] = $tmp;
            }
        }
    }

}

// recreate array from directory as key
$dirs = array();
for($i=0; $i < count($formatted_dirs) ; $i++)
{
    $current_dirs = $formatted_dirs[$i];

    $str = '@$dirs';
    for($j=0; $j < count($current_dirs); $j++)
    {
        $str .= '["'.$current_dirs[$j].'"]';
    }

    $str .= ' = array();';
    eval($str);
}




$tree = renderTree($dirs, $upload_path);
$upload_pathX = str_replace(WEBSITE_PATH, '', $upload_path);

$html = <<<EOF
<ul class="treeview">
    <li class="selected"><a class="root" href="$upload_pathX">$root_name</a>
    $tree
</ul>
EOF;

die($html);




?>