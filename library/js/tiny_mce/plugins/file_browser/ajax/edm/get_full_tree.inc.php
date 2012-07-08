<?php
/**
 * Tree treatment
 */

$html = "";

// full view for administrator
if(EDM_ADMINISTRATOR == true)
{
    $dirs = getDirTree($upload_path, false);
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
    Query::factory()->select('Folder')
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
    $dirs = array();
    while($r = $nuts->dbFetch())
    {
        $dir = str_replace($upload_pathX, '/', $r['Folder']);
        if($dir != '/')
        {
            $tmp = explode('/', $dir);
            $tmp2 = array();
            foreach($tmp as $current)
            {
                if(!empty($current))
                    $tmp2[] = $current;
            }

            $formatted_dirs[] = $tmp2;
        }
    }

    for($i=0; $i < count($formatted_dirs); $i++)
    {
        $formatted_dir = $formatted_dirs[$i];
        if(!isset($dirs[$formatted_dir[0]]))
            $dirs[$formatted_dir[0]] = array();

        for($j=1; $j < count($formatted_dir); $j++)
        {
            $ref = &$dirs[$formatted_dir[$j-1]];
            $ref[$formatted_dir[$j]] = array();
        }
    }
}






$tree = renderTree($dirs, $upload_path);

$upload_pathX = str_replace(WEBSITE_PATH, '', $upload_path);

$html = <<<EOF
<ul class="treeview">
    <li class="selected"><a class="root" href="$upload_pathX">$root_name</a>
    $tree
</ul>
EOF;

$resp['html'] = $html;



?>