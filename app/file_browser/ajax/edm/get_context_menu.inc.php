var cmenu = new Array();
var foldercmenu = new Array();
var filecmenu = new Array();
var imagecmenu = new Array();
<?php

// init ****************************************************************************************************************
$folder = @urldecode($_GET['folder']);
if(empty($folder))$folder = $upload_pathX;


// actions ****************************************************************************************************************
$allowed_actions = array();
$allowed_actions['paste'] = (edmUserHasRight('WRITE', $folder));


$context = array();
function edmAddContext($dest, $title, $js, $icon, $enabled, $add_separator_before)
{
    global $context;

    if(!isset($context[$dest]))$context[$dest] = '';

    if($add_separator_before)
        $context[$dest] .= ",$.contextMenu.separator";

    if(!empty($context[$dest]))
        $context[$dest] .= ',';

    $title_opt = strtolower($title);
    $title2 = translate($title);
    $disabled = ($enabled) ? 'false' : 'true';
    $context[$dest] .= "
        {\"$title2\":
            {
                onclick: function(menuItem,menu) {
                    $js
                },
                icon:'img/contextmenu/$icon',
                disabled: $disabled,
                title: \"$title_opt\"
            }
        }";
}

// cmenu ***************************************************************************************************************

// New Folder
$js = "$.MediaBrowser.createFolder();";
$enabled = edmUserHasRight('WRITE', $folder);
edmAddContext('cmenu', "New folder", $js, 'open.png', $enabled, false);

// Upload
$js = "showUpload();";
$enabled = edmUserHasRight('UPLOAD', $folder);
edmAddContext('cmenu', "Upload files...", $js, 'insert.png', $enabled, true);

// Paste
$js = "$.MediaBrowser.paste();";
$enabled = edmUserHasRight('WRITE', $folder);
edmAddContext('cmenu', "Paste", $js, 'paste.gif', $enabled, true);

// Administrator
if(EDM_ADMINISTRATOR == true)
{
    $js = "showRights($.MediaBrowser.currentFolder);";
    edmAddContext('cmenu', "Rights", $js, 'view_list.png', true, true);
}

// foldercmenu *********************************************************************************************************

// Open
$js = "$.MediaBrowser.loadFolder($(this).attr('href'));";
$enabled = edmUserHasRight('LIST', $folder);
edmAddContext('foldercmenu', "Open", $js, 'open.png', $enabled, false);

// copy
$js = "$.MediaBrowser.copy();";
$enabled = edmUserHasRight('READ', $folder);
edmAddContext('foldercmenu', "Copy", $js, 'copy.gif', $enabled, true);

// cut
$js = "$.MediaBrowser.cut();";
$enabled = edmUserHasRight('DELETE', $folder);
edmAddContext('foldercmenu', "Cut", $js, 'cut.gif', $enabled, false);

// rename
$js = "$.MediaBrowser.rename($(this).attr('href'),'folder');";
$enabled = edmUserHasRight('MODIFY', $folder);
edmAddContext('foldercmenu', "Rename", $js, 'rename.png', $enabled, true);

// delete
$del_msg = translate("Do you really want to delete this folder and its contents ?");
$js = <<<EOF
    if(confirm('$del_msg'))$.MediaBrowser.delete_all();
EOF;
$enabled = edmUserHasRight('DELETE', $folder);
edmAddContext('foldercmenu', "Delete", $js, 'delete.gif', $enabled, true);

// share
if(edmUserHasRight('SHARE', $folder))
{
    $js = "$.MediaBrowser.share();";
    edmAddContext('foldercmenu', "Share", $js, 'share.gif', $enabled, true);
}



// Administrator
if(EDM_ADMINISTRATOR == true)
{
$js = <<<EOF
    folder = $.MediaBrowser.currentFolder;
    folder += $.MediaBrowser.trim($('#file-specs .filename').text());
    showRights(folder);
EOF;

    edmAddContext('foldercmenu', "Rights", $js, 'view_list.png', true, true);
}

// filecmenu ***********************************************************************************************************

if(is_dir("service/editlive_office"))
{
    // Visualize
    $js = "$.MediaBrowser.viewFile();";
    $enabled = edmUserHasRight('READ', $folder);
    edmAddContext('filecmenu', "Visualize", $js, 'open.png', $enabled, false);
}


// Download
$js = "$.MediaBrowser.downloadFile();";
$enabled = edmUserHasRight('READ', $folder);
edmAddContext('filecmenu', "Download", $js, 'download.png', $enabled, false);

// service EditLiveOffice
if(is_dir("service/editlive_office"))
{
    // Edit
    $js = "$.MediaBrowser.editFile();";
    $enabled = edmUserHasRight('WRITE', $folder);
    edmAddContext('filecmenu', "Edit", $js, 'rename.png', $enabled, true);
}

// copy
$js = "$.MediaBrowser.copy();";
$enabled = edmUserHasRight('READ', $folder);
edmAddContext('filecmenu', "Copy", $js, 'copy.gif', $enabled, true);

// cut
$js = "$.MediaBrowser.cut();";
$enabled = edmUserHasRight('DELETE', $folder);
edmAddContext('filecmenu', "Cut", $js, 'cut.gif', $enabled, false);

// Paste
$js = "$.MediaBrowser.paste();";
$enabled = edmUserHasRight('WRITE', $folder);
edmAddContext('filecmenu', "Paste", $js, 'paste.gif', $enabled, true);

// rename
$js = "$.MediaBrowser.rename($(this).attr('href'), 'file'); ";
$enabled = edmUserHasRight('MODIFY', $folder);
edmAddContext('filecmenu', "Rename", $js, 'rename.png', $enabled, true);

// delete
$del_msg = translate("Do you really want to delete this file ?");
$js = <<<EOF
    if(confirm('$del_msg'))$.MediaBrowser.delete_all();
EOF;
$enabled = edmUserHasRight('DELETE', $folder);
edmAddContext('filecmenu', "Delete", $js, 'delete.gif', $enabled, true);


// share
if(edmUserHasRight('SHARE', $folder))
{
    $js = "$.MediaBrowser.share();";
    edmAddContext('filecmenu', "Share", $js, 'share.gif', $enabled, true);
}



// imagecmenu **********************************************************************************************************

// Visualize
$js = "$.MediaBrowser.viewImage();";
$enabled = edmUserHasRight('READ', $folder);
edmAddContext('imagecmenu', "Visualize", $js, 'edit_image.gif', $enabled, false);

// Download
$js = "$.MediaBrowser.downloadFile();";
$enabled = edmUserHasRight('READ', $folder);
edmAddContext('imagecmenu', "Download", $js, 'download.png', $enabled, false);


// copy
$js = "$.MediaBrowser.copy();";
$enabled = edmUserHasRight('READ', $folder);
edmAddContext('imagecmenu', "Copy", $js, 'copy.gif', $enabled, true);

// cut
$js = "$.MediaBrowser.cut();";
$enabled = edmUserHasRight('DELETE', $folder);
edmAddContext('imagecmenu', "Cut", $js, 'cut.gif', $enabled, false);

// Paste
$js = "$.MediaBrowser.paste();";
$enabled = edmUserHasRight('WRITE', $folder);
edmAddContext('imagecmenu', "Paste", $js, 'paste.gif', $enabled, true);

// rename
$js = "$.MediaBrowser.rename($(this).attr('href'), 'file'); ";
$enabled = edmUserHasRight('MODIFY', $folder);
edmAddContext('imagecmenu', "Rename", $js, 'rename.png', $enabled, true);

// delete
$del_msg = translate("Do you really want to delete this file ?");
$js = <<<EOF
    if(confirm('$del_msg'))$.MediaBrowser.delete_all();
EOF;
$enabled = edmUserHasRight('DELETE', $folder);
edmAddContext('imagecmenu', "Delete", $js, 'delete.gif', $enabled, true);

// share
if(edmUserHasRight('SHARE', $folder))
{
    $js = "$.MediaBrowser.share();";
    edmAddContext('imagecmenu', "Share", $js, 'share.gif', $enabled, true);
}


$output = '';
$output .= "cmenu = [{$context['cmenu']}];\n";
$output .= "foldercmenu = [{$context['foldercmenu']}];\n";
$output .= "filecmenu = [{$context['filecmenu']}];\n";
$output .= "imagecmenu = [{$context['imagecmenu']}];\n\n";

$output .= "allowed_actions = ".json_encode($allowed_actions);



    die($output);

?>







