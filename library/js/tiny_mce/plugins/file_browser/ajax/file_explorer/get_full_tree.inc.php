<?php
/**
 * Tree treatment
 */

$html = "";

$dirs = getDirTree($upload_path, false);
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