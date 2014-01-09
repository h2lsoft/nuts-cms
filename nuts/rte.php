<?php

// ajax ****************************************************************************************************************
if(@$_GET['ajax'] == 1 && $_POST)
{
    $source = $_POST['html'];
    $source = utf8_decode($source);

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    @$doc->loadHTML($source);
    $htmlX = $doc->saveHTML();
    $htmlX = strip_tags($htmlX, '<b><u><i><s><em><strong><strike><p><table><th><td><tr><br><h1><h2><h3><h4><h5><h6><ul><ol><li>');

    $regEx = '/([^<]*<\s*[a-z](?:[0-9]|[a-z]{0,9}))(?:(?:\s*[a-z\-]{2,14}\s*=\s*(?:"[^"]*"|\'[^\']*\'))*)(\s*\/?>[^<]*)/i'; // match any start tag
    $chunks = preg_split($regEx, $htmlX, -1,  PREG_SPLIT_DELIM_CAPTURE);
    $chunkCount = count($chunks);
    $strippedString = '';
    for($n=1; $n < $chunkCount; $n++) {
        $strippedString .= $chunks[$n];
    }

    $htmlX = $strippedString;

    // reformat
    $reps = array('table', 'tr', 'th', 'td', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li');
    foreach($reps as $rep)
    {
        $htmlX = str_replace("<$rep>", "\n<$rep>\n", $htmlX);
        $htmlX = str_replace("</$rep>", "\n</$rep>\n", $htmlX);
    }

    $htmlX = str_replace('<p></p>', '', $htmlX);
    $htmlX = str_replace('<p>&nbsp;</p>', '', $htmlX);

    do
    {
        $htmlX = str_replace("\n\n", "\n", $htmlX);
    }
    while(strpos($htmlX, "\n\n") !== false);

    $htmlX = trim($htmlX);

    // $htmlX = mb_convert_encoding($htmlX, 'utf-8');
    die($htmlX);

}




if(!in_array(@$_GET['view'], array('paste')))
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
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="chrome=1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $title; ?></title>

    <script type="text/javascript" src="/library/js/jquery.js"></script>
    <script type="text/javascript" src="/library/js/php.js"></script>

    <link rel="stylesheet" type="text/css" href="/library/js/tiny_mce/themes/advanced/skins/o2k7/dialog.css" media="all" />

    <style type="text/css">
    body {overflow: hidden;}
    fieldset {background-color: #ffffe0; border-radius: 5px; margin-bottom: 10px;}
    fieldset legend {font-size: 12px;}
    fieldset label {cursor: pointer;}
    #content {display:none; width: 99%; height: 75%; font-family: 'Courier New',Courier,mono; font-size: 12px;}
    #iframecontainer {display:none; width: 99%; height: 75%; border:1px solid black; background-color: white; padding: 1px; overflow: scroll}
    </style>

</head>
<body>

<?php if($_GET['view'] == 'paste'): ?>
<form name="source" onsubmit="return false;" action="#">

    <fieldset>
            <legend>Format</legend>
                    <label><input type="radio" name="format" value="html"  checked="checked"> Word/Html</label>
                    <label><input type="radio" name="format" value="text"> Text</label>
    </fieldset>

    <div><b><?php echo $info; ?></b></div>

    <div id="iframecontainer" contentEditable="true"></div>
    <textarea id="content" name="content" dir="ltr" wrap="soft" class="mceFocus" autofocus="1"></textarea>


    <div class="mceActionPanel">
        <input type="button" name="insert" value="<?php echo $insert; ?>" id="insert" onclick="textSubmit()" />
        <input type="button" name="cancel" value="<?php echo $cancel; ?>" onclick="window.close();" id="cancel" />
    </div>

</form>
<script>

function textSubmit()
{
    parentID = '<?php echo $_GET['id']; ?>';

    if($('input[name="format"]:eq(0)').is(':checked'))
    {
        text = $('#iframecontainer').html();
        $.post('rte.php?ajax=1', {html:text}, function(resp){
            window.opener.WYSIWYGAddText(parentID, resp);
            window.close();
        });

    }
    else
    {
        text = str_replace('\n', '\n<br />', document.getElementById('content').value);
        window.opener.WYSIWYGAddText(parentID, text);
        window.close();
    }
}


function trtUpdateFormat()
{
    $('#iframecontainer').hide();
    $('#content').hide();

    if($('input[name="format"]:eq(0)').is(':checked'))
    {
        $('#iframecontainer').show().focus();
    }
    else
    {
        $('#content').show().focus();
    }
}

$(function(){
    $('#content').focus();

    $('input[name="format"]').click(function(){
        trtUpdateFormat();
    });

    trtUpdateFormat();

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