<?php
@session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<title><?php echo strip_tags($_GET['t']); ?></title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/print.css?t=<?php echo time(); ?>" />
	<link rel="stylesheet" type="text/css" href="css/dialog_print.css?t=<?php echo time(); ?>" />

	<script>
	var nutsUserLang  = "<?php echo $_SESSION['Language']; ?>";
	</script>


	<script type="text/javascript" src="/library/js/php.js"></script>
	<script type="text/javascript" src="nuts.js"></script>
	<script src="../library/js/jscalendar/calendar.js" type="text/javascript"></script>
	<script src="../library/js/jscalendar/lang/calendar-<?php echo $_SESSION['Language']; ?>.js" type="text/javascript"></script>
	<script src="../library/js/jscalendar/calendar-setup.js" type="text/javascript"></script>



	<script type="text/javascript" src="/library/js/jquery.js"></script>
	<script type="text/javascript" src="/library/js/jquery.form.js"></script>

	<!-- <script src="../library/js/jquery-ui/jquery-ui-personalized-1.5.1.js" type="text/javascript"></script> -->
	<!-- <link rel="stylesheet" href="../library/js/jquery-ui/themes/custom/jquery-ui-themeroller.css" /> -->
	<script src="/library/js/jquery-ui/jquery-ui-1.10.4.custom.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="/library/js/jquery-ui/themes/ui-lightness/jquery-ui-1.10.4.custom.min.css" />

</head>
<body>

	<script>
	function getIFrameDocument(aID){
		// if contentDocument exists, W3C compliant (Mozilla)
		if(document.getElementById(aID).contentWindow){
			return document.getElementById(aID).contentWindow.document;
		} else {
			// IE
			return document.frames[aID].document;
		}
	}

    opener_content = window.opener.$("#form_content").html();
    opener_content = str_replace('<iframe', "<div", opener_content);
    opener_content = str_replace('</iframe>', "</div>", opener_content);
    $('body').append(opener_content);

    $('textarea.mceEditor').each(function(){
        id = $(this).attr('id');
        $('#iframe_'+id).html($(this).val());
    });


	$(document).keyup(function(event){
		if(event.keyCode == 27)
			window.close();
    });

	count_time  = $('textarea.mceEditor').length * 300;
	setTimeout( function() {window.print();}, count_time);


	</script>

</body>
</html>