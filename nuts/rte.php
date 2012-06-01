<?php

if(!in_array($_GET['view'], array('paste')))
    die("Error: view not found");

@session_start();
$view = $_GET['view'];

$insert = ($_SESSION['Language'] == 'fr') ? "Insérer" : "Insert";
$cancel = ($_SESSION['Language'] == 'fr') ? "Annuler" : "Cancel";

if($view == 'paste'){
    $title = 'Paste Editor';
    $info = ($_SESSION['Language'] == 'fr') ? "Utilisez CTRL+V sur votre clavier pour coller le texte dans la fenêtre" : "Use CTRL+V to paste text";
}


?>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="chrome=1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $title; ?></title>

    <script type="text/javascript" src="/library/js/jquery.js"></script>
    <script type="text/javascript" src="/library/js/php.js"></script>

    <link rel="stylesheet" type="text/css" href="/library/js/tiny_mce/themes/advanced/skins/o2k7/dialog.css" media="all" />
    <link rel="stylesheet" type="text/css" href="/library/js/tiny_mce/plugins/paste/blank.css" media="all" />
</head>
<body>

<?php if($_GET['view'] == 'paste'): ?>
<form name="source" onsubmit="window.opener.WYSIWYGAddText('<?php echo $_GET['id']; ?>', str_replace('\n', '\n<br />', document.getElementById('content').value)); window.close(); return false;" action="#">

    <div><b><?php echo $info; ?></b></div>

    <textarea id="content" name="content" rows="13" cols="100" style="width: 100%; height: 85%; font-family: 'Courier New',Courier,mono; font-size: 12px;" dir="ltr" wrap="soft" class="mceFocus"></textarea>

    <div class="mceActionPanel">
        <input type="submit" name="insert" value="<?php echo $insert; ?>" id="insert" />
        <input type="button" name="cancel" value="<?php echo $cancel; ?>" onclick="window.close();" id="cancel" />
    </div>

</form>
<script>
$(function(){
    $('#content').focus();
});
</script>


<?php endif; ?>

<script>
$(document).keydown(function(e){
    code = e.keyCode ? e.keyCode : e.which;
    if(code == 27)
       $('#cancel').click();
});
</script>


</body>
</html>