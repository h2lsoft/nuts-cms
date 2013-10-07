<?php

// controller ***************************************************
if(!isset($_GET['syntax']))$_GET['syntax'] = 'php';
if(!isset($_GET['parentID']))$_GET['parentID'] = '';

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Code Editor</title>

        <script type="text/javascript" src="/library/js/jquery.js"></script>
        <script language="text/javascript" src="/library/js/php.js"></script>

        <link rel="stylesheet" href="/nuts/css/style.css" />
	    <link rel="stylesheet" href="lib/codemirror.css" />
		<script src="lib/codemirror.js" type="text/javascript"></script>


		<?php if($_GET['syntax'] == 'php'): ?>

		<script src="mode/xml/xml.js" type="text/javascript"></script>
		<script src="mode/javascript/javascript.js" type="text/javascript"></script>
		<script src="mode/css/css.js" type="text/javascript"></script>
		<script src="mode/clike/clike.js" type="text/javascript"></script>
		<script src="mode/php/php.js" type="text/javascript"></script>

		<?php elseif($_GET['syntax'] == 'html'): ?>

		<script src="mode/xml/xml.js" type="text/javascript"></script>
		<script src="mode/javascript/javascript.js" type="text/javascript"></script>
		<script src="mode/css/css.js" type="text/javascript"></script>

		<?php elseif($_GET['syntax'] == 'sql'): ?>

		<script src="mode/sql/sql.js" type="text/javascript"></script>

		<?php elseif($_GET['syntax'] == 'js'): ?>

		<script src="mode/javascript/javascript.js" type="text/javascript"></script>

		<?php elseif($_GET['syntax'] == 'css'): ?>

		<script src="mode/css/css.js" type="text/javascript"></script>

		<?php endif ?>

		<style type="text/css">
		body {padding:0px; margin:0;}
		#save_bar {text-align: right; position: absolute; z-index: 5; top: 0; right: 0; padding-right: 10px; padding-top: 5px;}
		.CodeMirror {border-top: 1px solid black; border-bottom: 1px solid black;}
		.CodeMirror-scroll {height:99%;}
		span.cm-comment {color: black!important; background-color: #FFCC99;}
		.activeline {background: #E9EFF8 !important;}
		</style>

        <script>
		var syntax = '<?php echo $_GET['syntax']; ?>';
		var editor = '';

        // call back
        function my_save()
        {
			val = editor.getValue();
			window.opener.document.getElementById('<?php echo $_GET['parentID']; ?>').value = val;
			$("#saver").attr('disabled', true);
			editor.focus();
		}
        </script>


    </head>

   <body>

	   <div id="save_bar">
		   <input type="button" id="saver" value="Save (Alt+S)" onclick="my_save();" accesskey="s" disabled />
	   </div>

	   <textarea name="CodeEditor" id="CodeEditor"></textarea>


        <script>
		new_value = window.opener.document.getElementById('<?php echo $_GET['parentID']; ?>').value;
		$('#CodeEditor').val(new_value);


		// init codemirror
		if(syntax == 'php')
		{
			curMode = "application/x-httpd-php-open";
		}
		else if(syntax == 'html')
		{
			curMode = "text/html";
		}
		else if(syntax == 'sql')
		{
			curMode = "text/x-mysql";
		}
		else if(syntax == 'js')
		{
			curMode = "text/javascript";
		}
		else if(syntax == 'css')
		{
			curMode = "text/css";
		}

		var editor = CodeMirror.fromTextArea(document.getElementById("CodeEditor"), {

							lineNumbers: true,
							matchBrackets: true,
							mode: curMode,
							indentUnit: 4,
							indentWithTabs: true,
							smartIndent:false,
							enterMode: "keep",
							tabMode: "shift"

							/*onCursorActivity: function() {
								editor.setLineClass(hlLine, null);
								hlLine = editor.setLineClass(editor.getCursor().line, "activeline");
							},
							change: function (cm, change){
								alert("fire change");

								$("#saver").attr('disabled', "");

							}*/


				});

		// var hlLine = editor.setLineClass(0, "activeline");

		editor.on("change", function(cm) {
			$("#saver").attr('disabled', "");
		});

        </script>


    </body>
</html>
