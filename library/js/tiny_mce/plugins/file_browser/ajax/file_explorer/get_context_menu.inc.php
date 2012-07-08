var foldercmenu = new Array();
var filecmenu = new Array();
var imagecmenu = new Array();
var cmenu = new Array();



//Context menus
foldercmenu = [
{'<?php echo translate("Open");?>':{
onclick: function(menuItem,menu) { $.MediaBrowser.loadFolder($(this).attr('href')); },
icon:'img/contextmenu/open.png'
}
}
<?php if($allowedActions['copy_paste'] === TRUE || $allowedActions['cut_paste'] === TRUE): ?>
,$.contextMenu.separator
<?php endif; ?>

<?php if($allowedActions['copy_paste'] === TRUE): ?>
,{'<?php echo translate("Copy");?>':{
onclick:function(menuItem,menu) { $.MediaBrowser.copy(); },
icon:'img/contextmenu/copy.gif'
}
}
<?php endif; ?>
<?php if($allowedActions['cut_paste'] === TRUE): ?>
,{'<?php echo translate("Cut");?>':{
onclick:function(menuItem,menu) { $.MediaBrowser.cut(); },
icon:'img/contextmenu/cut.gif'
}
}
<?php endif; ?>
<?php if($allowedActions['rename'] === TRUE): ?>
,$.contextMenu.separator,
{'<?php echo translate("Rename");?>':{
onclick:function(menuItem,menu) { $.MediaBrowser.rename($(this).attr('href'), 'folder'); },
icon:'img/contextmenu/rename.png'
}
}
<?php endif; ?>
<?php if($allowedActions['delete'] === TRUE): ?>
,$.contextMenu.separator,
{'<?php echo translate("Delete");?>':{
onclick:function(menuItem,menu) {
if(confirm('<?php echo translate("Do you really want to delete this folder and its contents ?");?>')){
$.MediaBrowser.delete_all();
}
},

icon:'img/contextmenu/delete.gif',
disabled:false
}
}
<?php endif; ?>




];




filecmenu = [

{'<?php echo translate("Insert");?>':{
    onclick:function(menuItem,menu) { $.MediaBrowser.insertFile(); },
    icon:'img/contextmenu/insert.png'
}
}

, {'<?php echo translate("Open");?>':{
    onclick:function(menuItem,menu) { $.MediaBrowser.viewFile(); },
    icon:'img/contextmenu/open.png'
}
}

<?php if($allowedActions['copy_paste'] === TRUE || $allowedActions['cut_paste'] === TRUE): ?>
    ,$.contextMenu.separator
<?php endif; ?>

<?php if($allowedActions['copy_paste'] === TRUE): ?>
,{'<?php echo translate("Copy");?>':{
    onclick:function(menuItem,menu) { $.MediaBrowser.copy(); },
    icon:'img/contextmenu/copy.gif'
}
}
<?php endif; ?>
<?php if($allowedActions['cut_paste'] === TRUE): ?>
,{'<?php echo translate("Cut");?>':{
    onclick:function(menuItem,menu) { $.MediaBrowser.cut(); },
    icon:'img/contextmenu/cut.gif'
}
}
<?php endif; ?>
<?php if($allowedActions['copy_paste'] === TRUE || $allowedActions['cut_paste'] === TRUE): ?>
,{'<?php echo translate("Paste");?>':{
    onclick:function(menuItem,menu) { $.MediaBrowser.paste(); },
    icon:'img/contextmenu/paste.gif',
    disabled:true
}
}
<?php endif; ?>
<?php if($allowedActions['rename'] === TRUE): ?>
,$.contextMenu.separator,
{'<?php echo translate("Rename");?>':{
    onclick:function(menuItem,menu) { $.MediaBrowser.rename($(this).attr('href'), 'file'); },
    icon:'img/contextmenu/rename.png'
}
}
<?php endif; ?>

<?php if($allowedActions['delete'] === TRUE): ?>
,$.contextMenu.separator,
{'<?php echo translate("Delete");?>':{
    onclick:function(menuItem,menu) {
    if(confirm('<?php echo translate("Do you really want to delete this file?");?>')){
    $.MediaBrowser.delete_all();
}
},
    icon:'img/contextmenu/delete.gif',
    disabled:false
}
}
<?php endif; ?>

];

imagecmenu = [
{'<?php echo translate("Insert");?>':{
    onclick:function(menuItem,menu) { $.MediaBrowser.insertFile(); },
    icon:'img/contextmenu/insert.png'
}
}

, {'<?php echo translate("Open");?>':{
    onclick:function(menuItem,menu) { $.MediaBrowser.viewImage(); },
    icon:'img/contextmenu/edit_image.gif'
}
}


<?php if($allowedActions['upload'] === TRUE): ?>
,$.contextMenu.separator
,{"<?php echo translate("Edit");?>":{
    onclick:function(menuItem,menu) { $.MediaBrowser.editImage(); },
    icon:'img/contextmenu/view_images_large.png'
}
}
<?php endif; ?>



<?php if($allowedActions['copy_paste'] === TRUE || $allowedActions['cut_paste'] === TRUE): ?>
,$.contextMenu.separator
<?php endif; ?>
<?php if($allowedActions['copy_paste'] === TRUE): ?>
,{'<?php echo translate("Copy");?>':{
    onclick:function(menuItem,menu) { $.MediaBrowser.copy(); },
    icon:'img/contextmenu/copy.gif'
}
}
<?php endif; ?>
<?php if($allowedActions['cut_paste'] === TRUE): ?>
,{'<?php echo translate("Cut");?>':{
    onclick:function(menuItem,menu) { $.MediaBrowser.cut(); },
    icon:'img/contextmenu/cut.gif'
}
}
<?php endif; ?>
<?php if($allowedActions['copy_paste'] === TRUE || $allowedActions['cut_paste'] === TRUE): ?>
,{'<?php echo translate("Paste");?>':{
    onclick:function(menuItem,menu) { $.MediaBrowser.paste(); },
    icon:'img/contextmenu/paste.gif',
    disabled:true
}
}
<?php endif; ?>
<?php if($allowedActions['rename'] === TRUE): ?>
,$.contextMenu.separator,
{'<?php echo translate("Rename");?>':{
    onclick:function(menuItem,menu) { $.MediaBrowser.rename($(this).attr('href'), 'file'); },
    icon:'img/contextmenu/rename.png'
}
}
<?php endif; ?>
<?php if($allowedActions['delete'] === TRUE): ?>
,$.contextMenu.separator,
{'<?php echo translate("Delete");?>':{
    onclick:function(menuItem,menu) {
    if(confirm('<?php echo translate("Do you really want to delete this image?");?>')){
    $.MediaBrowser.delete_all();
}
},
    icon:'img/contextmenu/delete.gif',
    disabled:false
}
}
<?php endif; ?>
];


cmenu = [
<?php if($allowedActions['create_folder'] === TRUE): ?>

{'<?php echo translate("New folder");?>':{
onclick:function(menuItem,menu) { $.MediaBrowser.createFolder(); },
icon:'img/contextmenu/open.png'
}
}
<?php endif; ?>

<?php if($allowedActions['upload'] === TRUE): ?>
,$.contextMenu.separator
,{"<?php echo translate("Upload files...");?>":{
onclick:function(menuItem,menu) { showUpload(); },
icon:'img/contextmenu/insert.png'
}
}
<?php endif; ?>

<?php if($allowedActions['copy_paste'] === TRUE || $allowedActions['cut_paste'] === TRUE): ?>
,$.contextMenu.separator,
{'<?php echo translate("Paste");?>':{
onclick:function(menuItem,menu) { $.MediaBrowser.paste(); },
icon:'img/contextmenu/paste.gif',
disabled:true
}
}
<?php endif; ?>
];



<?php die(); ?>