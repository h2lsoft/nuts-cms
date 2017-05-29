<?php

// controller ***************************************************
if(!isset($_GET['syntax']))$_GET['syntax'] = 'php';
if(!isset($_GET['parent_target']))$_GET['parent_target'] = '';

?>
<!DOCTYPE html>
<html>
    <head>

        <title>Code Editor</title>

        <script type="text/javascript" src="/library/js/jquery.js"></script>
	    <script language="text/javascript" src="/library/js/php.js"></script>
	    <script type="text/javascript" src="/library/js/jquery.htmlClean.js"></script>


        <link rel="stylesheet" href="/nuts/css/style.css" />
	    <link rel="stylesheet" href="/library/js/codemirror/lib/codemirror.css" />
		<script src="/library/js/codemirror/lib/codemirror.js" type="text/javascript"></script>


		<?php if($_GET['syntax'] == 'php'): ?>

		<script src="/library/js/codemirror/mode/xml/xml.js" type="text/javascript"></script>
		<script src="/library/js/codemirror/mode/javascript/javascript.js" type="text/javascript"></script>
		<script src="/library/js/codemirror/mode/css/css.js" type="text/javascript"></script>
		<script src="/library/js/codemirror/mode/clike/clike.js" type="text/javascript"></script>
		<script src="/library/js/codemirror/mode/php/php.js" type="text/javascript"></script>

		<?php elseif($_GET['syntax'] == 'html'): ?>

		<script src="/library/js/codemirror/mode/xml/xml.js" type="text/javascript"></script>
		<script src="/library/js/codemirror/mode/javascript/javascript.js" type="text/javascript"></script>
		<script src="/library/js/codemirror/mode/css/css.js" type="text/javascript"></script>

		<?php elseif($_GET['syntax'] == 'sql'): ?>

		<script src="/library/js/codemirror/mode/sql/sql.js" type="text/javascript"></script>

		<?php elseif($_GET['syntax'] == 'js'): ?>

		<script src="/library/js/codemirror/mode/javascript/javascript.js" type="text/javascript"></script>

		<?php elseif($_GET['syntax'] == 'css'): ?>

		<script src="/library/js/codemirror/mode/css/css.js" type="text/javascript"></script>

		<?php endif ?>

	    <script type="text/javascript" src="/library/js/codemirror/addon/selection/active-line.js"></script>


	    <?php if($_GET['syntax'] == 'html'): ?>

	    <?php endif ?>



		<style type="text/css">
		body {padding:0px; margin:0;}
		#save_bar {text-align: right; position: fixed; z-index: 5; top: 0; right: 0; padding-right: 10px; padding-top: 5px;}
		.CodeMirror {border-top: 1px solid black; border-bottom: 1px solid black;}
		.CodeMirror-scroll {height:99%;}
		span.cm-comment {color: black!important; background-color: #FFCC99;}
		.activeline {background: #E9EFF8 !important;}
		</style>

        <script>
		var parent_target = '<?php echo $_GET['parent_target']; ?>';
		var syntax = '<?php echo $_GET['syntax']; ?>';
		var editor = '';

        // call back
        function my_save()
        {
			val = editor.getValue();

	        if(parent_target == 'tinymce')
	        {
		        tiny = window.opener.tinyMCE.activeEditor;
		        tiny.focus();
		        tiny.undoManager.transact(function() {
			        tiny.setContent(val, {format : 'raw'});
		        });

		        tiny.selection.setCursorLocation();
		        tiny.nodeChanged();
	        }
	        else
	        {
		        window.opener.document.getElementById('<?php echo $_GET['parent_target']; ?>').value = val;
	        }

			$("#saver").attr('disabled', true);
			editor.focus();
	        window.close();
		}
        </script>


    </head>

   <body>

	   <div id="save_bar">
		   <input type="button" id="saver" value="Save (Alt+S)" onclick="my_save();" accesskey="s" disabled />
	   </div>

	   <textarea name="CodeEditor" id="CodeEditor"></textarea>


        <script>
	    if(parent_target == 'tinymce')
	    {
		    new_value = window.opener.tinyMCE.activeEditor.getContent({raw: true});
		    new_value = $.htmlClean(new_value, { format: true})

	    }
	    else
	    {
		    new_value = window.opener.document.getElementById('<?php echo $_GET['parent_target']; ?>').value;
	    }

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

							lineWrapping: true,
							lineNumbers: true,
							matchBrackets: true,
							mode: curMode,
							indentUnit: 4,
							indentWithTabs: true,
							smartIndent:false,
							enterMode: "keep",
							tabMode: "shift",
							autofocus: true,
							styleActiveLine: true
		});



		editor.on("change", function(cm) {
			$("#saver").attr('disabled', false);
		});


		$(window).resize(function(){
			$('.CodeMirror').height($(window).height()-5);
		});
		$(window).resize();


        </script>


    </body>
</html>