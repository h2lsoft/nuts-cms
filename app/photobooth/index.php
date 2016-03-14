<?php
/**
 * Photobooth app
 *
 * @version 1.0
 * @author H2Lsoft
 *
 *  GET target to save data uri
 *  GET captureEndTrigger to lanche automatically opner.webcamFinish()
 *
 */

@session_start();
$close = (@$_SESSION['Language'] == 'fr') ? 'Fermer' : 'Close';
$save = (@$_SESSION['Language'] == 'fr') ? 'Enregister' : 'Save';
$error = (@$_SESSION['Language'] == 'fr') ? "Merci de cliquer sur le bouton photo pour sauvegarder votre image" : "Please, click on camera image to save your picture";

?>
<!doctype html>
<html lang="en">
<head>
	<title>Webcam capture</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="chrome=1" />
	<meta name="viewport" content="initial-scale=1, maximum-scale=1" />

	<script  type="text/javascript" src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script  type="text/javascript"src="photobooth_min.js"></script>

	<link rel="stylesheet" type="text/css" href="/nuts/css/style.css" media="all" />
	<link rel="stylesheet" type="text/css" href="style.css" media="all" />
</head>

<body>

<div class="container">

	<div class="layout">
		<div id="webcam"></div>
		<div id="preview"></div>
	</div>

	<div class="buttons">
		<input type="button" id="btn_submit" value="<?php echo $save; ?>" />
		<input type="button" id="btn_cancel" value="<?php echo $close; ?>" onclick="window.close();" />
	</div>

</div>


<script>
$('#btn_submit').on('click', function(){

	if(webcamDataUrl == '')
	{
		msg = "<?php echo $error; ?>";
		alert(msg);
		return;
	}


	target = "<?php echo $_GET['target']; ?>";
	opener.$('#'+target).val(webcamDataUrl);

	finish = "<?php echo @$_GET['captureEndTrigger']; ?>";
	if(finish != '')
	{
		opener[finish]();
	}


	window.close();
});


var webcamDataUrl = '';
$('#webcam').photobooth().on("image",function( event, dataUrl ){

	webcamDataUrl = dataUrl;
	$("#preview" ).html( '<img src="'+dataUrl+'" >');

});

</script>


</body>
</html>