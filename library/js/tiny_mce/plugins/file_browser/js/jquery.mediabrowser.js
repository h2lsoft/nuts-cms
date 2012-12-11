(function($){

	$.MediaBrowser = {

		absoluteURL: false,
		clipboard: new Array(),
		copyMethod: '',
		ctrlKeyPressed: false,
		currentFile: '',
		currentFolder: '',
		currentView: '',
		dragMode: false,
		dragObj: null,
		dragID: '',
		hostname: "http://" + window.location.host,
		lastSelectedItem:null,
		searchDefaultValue: '',
		shiftKeyPressed: false,
		tableHeadersFixed: 0,
		timeout: null,


        searchLaunch: function(){
            $.MediaBrowser.searchFor($('input#search').val());
        },

        searchFor: function(strSearchFor){

            clearTimeout($.MediaBrowser.timeout);

            $.MediaBrowser.timeout = setTimeout(function () {

                if ($.MediaBrowser.currentView == "details"){
                    //Give table headers a fixed width so colums won't change widths when a row gets hidden
                    if (!$.MediaBrowser.tableHeadersFixed) $.MediaBrowser.fix_widths();
                    $('div#files table tbody tr:not(.filter)').css({display: ""}); //NO DISPLAY:BLOCK!!
                } else {
                    $('div#files ul li:not(.filter)').css({display: "block"});
                }

                // Normalise
                strSearchFor = $.trim(strSearchFor.toLowerCase().replace(/\n/, '').replace(/\s{2,}/, ' '));

                if (strSearchFor != ""){

                    var arrList = [];

                    var rgxpSearchFor = new RegExp(strSearchFor,'i');

                    // Fill array with the list items or table rows depending on view
                    if ($.MediaBrowser.currentView == "details"){
                        arrList = $('div#files table tbody tr:not(.filter) .filename').get();

                        for(var i = 0; i < arrList.length; i++){
                            if ( !rgxpSearchFor.test( $(arrList[i]).text() ) ) $(arrList[i]).parent().css({'display': "none"});
                        }
                    } else {
                        arrList = $('div#files ul li:not(.filter) .filename').get();

                        for(var i = 0; i < arrList.length; i++){
                            if ( !rgxpSearchFor.test( $(arrList[i]).text() ) ) $(arrList[i]).parent().parent().css({'display': "none"});
                        }
                    }

                }
            }, 250);
        },


        reloadTree: function(){

            uri = getAjaxUri();
            uri += '&action=get_full_tree';

            $.get(uri, function(html){

                $("#tree").html(html);

                $('#tree').resizable({
                    minWidth: 200,
                    maxWidth: $(document).width()-500,
                    handles : "e"
                });


                $("#tree").TreeView();
                $.MediaBrowser.loadFolder($.MediaBrowser.currentFolder);
                $('ul.treeview ul').eq(0).show();



            }, 'html');

        },

        viewImage: function (){

            var URL = $("form#fileform input#file").val();
            if(URL == '')
            {
                $.MediaBrowser.showMessage(select_one_file);
                return;
            }

            //Send new filename to server
            if(editor == 'edm')
            {
                uri = getAjaxUri();
                uri += '&action=open';
                uri += '&file='+urlencode(URL);
                URL = uri;
            }

            src = URL;
            $('#fancybox img').attr('src', src);
            $('#fancybox').attr('href', src);
            $('#fancybox').click();
        },


        editImage: function (){

            var URL = $("form#fileform input#file").val();

            if(URL == '')
            {
                $.MediaBrowser.showMessage(select_one_file);
                return;
            }

            src = URL;
            full_src = WEBSITE_URL+'/'+src;
            ctarget = WEBSITE_URL+'/library/js/tiny_mce/plugins/file_browser/service/pixlr/pixlr_save.php';
            relative_src = src;
            exit_url = WEBSITE_URL+'/library/js/tiny_mce/plugins/file_browser/service/pixlr/pixlr_exit.php';

            pixlr.overlay.show({
                service: 'express',
                referrer: 'Nuts CMS',
                locktitle: true,
                locktarget: true,
                method: 'GET',
                title: relative_src,
                exit: exit_url,
                image: full_src,
                target: ctarget
            });


        },


        delete_all: function(){
            var message;
            var files = new Array();

            // Get all selected files and folders
            $('div#files li.selected a, div#files tr.selected').each(function(){
                files.push( urlencode($(this).attr("href")) );
            });

            uri = getAjaxUri();
            uri += '&action=delete';
            $.post(uri, {folder: urlencode($.MediaBrowser.currentFolder), files:files}, function(resp){

                if(resp['result'] != 'ok')
                {
                    $.MediaBrowser.showMessage(resp['message'], "error");
                }
                else
                {
                    $.MediaBrowser.hideContextMenu();
                    $('form#fileform').hide();
                    $('input#file').val("");

                    // tree init
                    $.MediaBrowser.reloadTree();
                }

            }, 'json');

        },

        createFolder: function(type){

            message = window.create_folder;
            prompt_message = message.format(name, "\n", "^ \\ / ? * \" ' < > : | .");
            new_name = prompt(prompt_message, "");

            // Validate new name
            if(new_name === "" || new_name == name || new_name == null)
                return;

            // Check if any unwanted characters are used
            if(/\\|\/|\.|\?|\.|\^|\*|\"|'|\<|\>|\:|\|/.test(new_name))
            {
                $.MediaBrowser.showMessage(invalid_characters_used, "error");
                return;
            }

            //Send new filename to server and do rename
            uri = getAjaxUri();
            uri += '&action=create_folder';
            $.post(uri, {'folder_name': urlencode(new_name), folder: urlencode($.MediaBrowser.currentFolder)}, function(resp){

                if(resp.result != 'ok')
                {
                    $.MediaBrowser.showMessage(resp.message, "error");
                }
                else
                {
                    // tree init
                    $.MediaBrowser.reloadTree();
                }

            }, 'json');
        },



        rename: function(path, type){
            var path_segments, name, old_filename, message, file_segments, file_ext, new_name, prompt_message, new_filename;

            path_segments = ($.MediaBrowser.trim(path,"/")).split("/");
            name = path_segments[path_segments.length - 1];
            old_filename = name;
            message = window.rename_folder;

            if (type == 'file') {
                //Save extension for later use
                file_segments = name.split(".");
                name = file_segments[0];
                file_ext = file_segments[file_segments.length - 1];
                message = window.rename_file;
            }

            // prompt_message = printf(message, name, "\n", "^ \\ / ? * \" ' < > : | .");
            prompt_message = message.format(name, "\n", "^ \\ / ? * \" ' < > : | .");
            new_name = prompt(prompt_message, name);

            // Validate new name
            if(new_name === "" || new_name == name || new_name == null)
                return;

            // Check if any unwanted characters are used
            if(/\\|\/|\.|\?|\.|\^|\*|\"|'|\<|\>|\:|\|/.test(new_name)){
                $.MediaBrowser.showMessage(invalid_characters_used, "error");
                return;
            }

            if(type == 'file') {
                new_filename = new_name + '.' + file_ext;
            } else {
                new_filename = new_name;
            }

            //Send new filename to server and do rename
            uri = getAjaxUri();
            uri += '&action=rename';

            $.post(uri, {'new_filename': urlencode(new_filename), old_filename: urlencode(old_filename), folder: urlencode($.MediaBrowser.currentFolder), type: type}, function(resp){

                if(resp.result == 'ko')
                {
                    $.MediaBrowser.showMessage(resp.message, "error");
                }
                else
                {
                    // tree init
                    $.MediaBrowser.reloadTree();
                }
            }, 'json');

        },




        viewFile: function (){

            var URL = $("form#fileform input#file").val();
            if(URL == '')
            {
                $.MediaBrowser.showMessage(select_one_file);
                return;
            }

            // window.open(URL);
            if(editor == 'edm')
            {

                file_extension = "";
                tmp = explode('.', URL);
                if(count(tmp) >= 2)
                {
                    file_extension = tmp[tmp.length-1];
                    file_extension = strtolower(file_extension);

                    if(file_extension == 'pdf')
                    {
                        //Send new filename to server
                        uri = getAjaxUri();
                        uri += '&action=open';
                        uri += '&file='+urlencode(URL);
                        uri += '&download=false';
                        URL = uri;
                        window.open(URL);

                        return;
                    }
                }



                //Send new filename to server
                str = '';
                str += '<applet codebase="service/editlive_office" id="EditLive_Applet" name="EditLive_Applet" code="GWDAEditLive_Applet.class" archive="EditLive_Applet.jar" width="300" height="64" align="center">';
                str += '<param name="type" value="application/x-java-applet;version=1.4.2" />';
                str += '<param name="separate_jvm" value="true" />';
                str += '<param name="classloader_cache" value="false" />';
                str += '<param name="scriptable" value="true" />';
                str += '<param name="paramWEBSITE_URL" value="'+WEBSITE_URL+'" />';
                str += '<param name="paramPHPSESSID" value="'+PHPSESSID+'" />';
                str += '<param name="paramFolder" value="'+urlencode($.MediaBrowser.currentFolder)+'" />';
                str += '<param name="paramFile" value="'+urlencode($('#file-specs .filename').text())+'" />';
                str += '<param name="paramMode" value="READ" />';
                str += '</applet>';

                $('#EditLive_Applet').remove();
                $('#EditLive_AppletContainer').html(str);
            }
            else
            {
                document.location.href = URL;
            }

        },

        downloadFile: function(){

            var URL = $("form#fileform input#file").val();
            if(URL == '')
            {
                $.MediaBrowser.showMessage(select_one_file);
                return;
            }

            // window.open(URL);
            if(editor == 'edm')
            {
                //Send new filename to server
                uri = getAjaxUri();
                uri += '&action=open';
                uri += '&file='+urlencode(URL);
                URL = uri;
            }

            document.location.href = URL;
        },


        editFile: function(){

            var URL = $("form#fileform input#file").val();
            if(URL == '')
            {
                $.MediaBrowser.showMessage(select_one_file);
                return;
            }

            file_extension = "";
            tmp = explode('.', URL);
            if(count(tmp) >= 2)
            {
                file_extension = tmp[tmp.length-1];
                file_extension = strtolower(file_extension);

                if(file_extension == 'pdf')
                {
                    $.MediaBrowser.viewFile();
                    return;
                }
            }




            //Send new filename to server
            str = '';
            str += '<applet codebase="service/editlive_office" id="EditLive_Applet" name="EditLive_Applet" code="GWDAEditLive_Applet.class" archive="EditLive_Applet.jar" width="300" height="64" align="center">';
            str += '<param name="type" value="application/x-java-applet;version=1.4.2" />';
            str += '<param name="separate_jvm" value="true" />';
            str += '<param name="classloader_cache" value="false" />';
            str += '<param name="scriptable" value="true" />';
            str += '<param name="paramWEBSITE_URL" value="'+WEBSITE_URL+'" />';
            str += '<param name="paramPHPSESSID" value="'+PHPSESSID+'" />';
            str += '<param name="paramFolder" value="'+urlencode($.MediaBrowser.currentFolder)+'" />';
            str += '<param name="paramFile" value="'+urlencode($('#file-specs .filename').text())+'" />';
            str += '<param name="paramMode" value="WRITE" />';
            str += '</applet>';

            $('#EditLive_Applet').remove();
            $('#EditLive_AppletContainer').html(str);



        },

        copy: function(){
            // Clear clipboard
            $.MediaBrowser.clipboard = [];
            $.MediaBrowser.copyMethod = 'copy';

            $('div#files li.selected a, div#files tr.selected').each(function(){
                $.MediaBrowser.clipboard.push( urlencode($(this).attr("href")) );
            });

            //Update clipboard label
            $('div#cbItems').text( $.MediaBrowser.clipboard.length );

            // only ! edm
            if(editor != 'edm')$.MediaBrowser.contextmenu();

        },

        cut: function(){

            $.MediaBrowser.copy();
            $.MediaBrowser.copyMethod = 'cut';


            $('div#files li.selected, div#files tr.selected').addClass('cut');
        },

        paste: function(){
            var action, message;

            // Only paste if copyMethod is set
            if($.MediaBrowser.copyMethod != ''){
                action = $.MediaBrowser.copyMethod == 'cut' ? 'cut' : 'copy';

                // Show loading icon
                $('div#files').html('<div class="loading"></div>');

                uri = getAjaxUri();
                uri += '&action='+action+'_paste';
                $.post(uri, {files: $.MediaBrowser.clipboard, folder:urlencode($.MediaBrowser.currentFolder)}, function(resp){

                    if(resp.result == 'ko')
                    {
                        $.MediaBrowser.showMessage(resp.message, "error");
                        $.MediaBrowser.loadFolder($.MediaBrowser.currentFolder);
                    }
                    else
                    {
                        $.MediaBrowser.clipboard = [];
                        $.MediaBrowser.copyMethod = '';
                        $('div#cbItems').text('0');
                        $.MediaBrowser.loadFolder($.MediaBrowser.currentFolder);
                        $.MediaBrowser.reloadTree();
                    }

                }, 'json');
            }
        },

        printClipboard: function(){
            var cb, str;

            cb = $.MediaBrowser.clipboard;
            str = $('div#navbar li.label:eq(0) span').text() + "<br /><br />";

            for(i = 0; i < cb.length; i++){
                str	+= urldecode(cb[i]) + "<br />";
            }

            $.MediaBrowser.showMessage(str);
            return false;
        },

        clearClipboard: function(){

            $.MediaBrowser.clipboard = [];
            $.MediaBrowser.copyMethod = '';
            $('div#cbItems').text('0');

        },

        insertFile: function(){

            var URL = $("form#fileform input#file").val();

            if(URL == '')
            {
                $.MediaBrowser.showMessage(select_one_file);
            }

            if(editor == "tinymce")
            {
                try
                {
                    var win = tinyMCEPopup.getWindowArg("window");
                }
                catch(err)
                {
                    $.MediaBrowser.showMessage(insert_cancelled);
                    return;
                }

                // insert information now
                win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;

                // are we an image browser
                if (typeof(win.ImageDialog) != "undefined")
                {
                    // we are, so update image dimensions...
                    if (win.ImageDialog.getImageData)
                        win.ImageDialog.getImageData();

                    // ... and preview if necessary
                    if (win.ImageDialog.showPreviewImage)
                        win.ImageDialog.showPreviewImage(URL);
                }

                // close popup window
                tinyMCEPopup.close();
            }
            else if(editor == "standalone")
            {
                add_image_tag = false;
                oldReturnID = returnID;
                returnID = str_replace('imgTag_', '', returnID);
                if(returnID != oldReturnID)add_image_tag = true;

                cur_uri = URL;
                if(add_image_tag)
                    cur_uri = '<img src="'+URL+'" alt=" " />';

                // WYSIWYG editor exists ?
                if(!window.opener.document.getElementById('iframe_'+returnID))
                {
                    window.opener.document.getElementById(returnID).value = cur_uri;
                }
                else
                {
                    if(!add_image_tag)
                        window.opener.cmdWYSIWYG(returnID, 'createLink', cur_uri);
                    else
                    {
                        window.opener.WYSIWYGAddText(returnID, cur_uri);
                    }
                }

                window.close();
            }

        },

        isFilesSelection: function(){
            return $('div#files li.selected a, div#files tr.selected').length;
        },

        showMessage: function(str, type){
            $('div#message').removeClass();
            if (type == "success" || type == "error") $('div#message').addClass(type);
            $('div#message').html(str);
            $('div#message').slideDown();

            timeout = (type != "error") ? 5000 : 3000;
            if(type == 'special')
            {
                $('div#message').addClass('error');
                timeout = 20000;
            }

            setTimeout(function() {
                $("div#message").slideUp();
            }, timeout);
        },

        fix_widths: function(){
            if($.MediaBrowser.currentView == "details"){

                $('table#details th').each(function () {
                    $(this).attr('width', parseInt($(this).outerWidth()));
                });
                $.MediaBrowser.tableHeadersFixed = 1;
            }
        },

        selectFileOrFolder: function(el, path, type /* , contextmenu */){

            //See if function is called via a context menu
            var cm = (typeof arguments[3] == 'undefined') ? false : true;
            var host = '';

            // Hide all visible contextmenus
            $('table.contextmenu, div.context-menu-shadow').css({'display': 'none'});

            $.MediaBrowser.setSelection(el, cm);
            $.MediaBrowser.updateFileSpecs(path, type);

            if(type != "folder" && $('div#files li.selected, div#files tr.selected').length == 1){
                if($.MediaBrowser.absoluteURL){
                    host = $.MediaBrowser.hostname;
                }
                $("form#fileform input#file").val(host + path);
                $.MediaBrowser.currentFile = path;
            } else {
                $("form#fileform input#file").val("");
                $.MediaBrowser.currentFile = '';
            }
        },

        changeview: function(view){
            $.MediaBrowser.currentView = view;
            $.MediaBrowser.loadFolder($.MediaBrowser.currentFolder);
            $('input#search').val($.MediaBrowser.searchDefaultValue);
            $.MediaBrowser.createCookie("pdw-view", $.MediaBrowser.currentView, 30);
            return false;
        },

        contextmenu: function(){

            if(editor == 'edm')
            {
                folder = $.MediaBrowser.currentFolder;

                uri = getAjaxUri();
                uri += '&action=get_context_menu&folder='+urlencode(folder);
                $.getScript(uri, function(resp){
                    $('div#files li a.folder, div#files tr.folder').contextMenu(foldercmenu);
                    $('div#files li a.file, div#files tr.file').contextMenu(filecmenu);
                    $('div#files li a.image, div#files tr.image').contextMenu(imagecmenu);
                    $('div#files').contextMenu(cmenu);
                });
            }
            else
            {
                $('div#files li a.folder, div#files tr.folder').contextMenu(foldercmenu);
                $('div#files li a.file, div#files tr.file').contextMenu(filecmenu);
                $('div#files li a.image, div#files tr.image').contextMenu(imagecmenu);

                dis = ($.MediaBrowser.clipboard.length == 0) ? true : false;
                if(nutsUserLang == 'fr')
                    cmenu[4].Coller.disabled = dis;
                else
                    cmenu[4].Paste.disabled = dis;

                $('div#files').contextMenu(cmenu);
            }
        },

        hideContextMenu: function(){
            // Hide all other contextmenus
            $('table.contextmenu, div.context-menu-shadow').css({'display': 'none'});
        },

		resizeWindow: function(){

			// Set default screen layout
			var windowHeight = $(window).height();
			var addressbarHeight = $('div#addressbar').outerHeight();
			var navbarHeight = $('div#navbar').outerHeight();
			var detailsHeight = $('div#file-specs').outerHeight();
			var explorerHeight = windowHeight - navbarHeight - addressbarHeight - detailsHeight;

			var windowWidth = $(window).width();
			var treeWidth = $('div#tree').outerWidth();
			var separatorWidth = $('div#vertical-resize-handler').outerWidth();
			var mainWidth = windowWidth - treeWidth - separatorWidth;

			//Set Explorer Height
			$('div#explorer').height(explorerHeight);
			$('div#main').height(explorerHeight);
			$('div#files, div.window').height(explorerHeight - 41); // -41 because of the fixed heading and ruler (H2) above the files
			$('div#main').width(mainWidth);
		},

        // Breadcrumbs
        updateAddressBar: function(){
            var strLink = '';
            var html = '<li class=\'root\'><span>&nbsp;</span></li>';

            var uploadFolder = $.MediaBrowser.trim($('input#currentfolder').val(), '/');
            var uploadFolders = uploadFolder.split('/');
            var h = uploadFolders.length;

            var curFolder = $.MediaBrowser.trim($.MediaBrowser.currentFolder, '/');
            var folders = curFolder.split('/');

            folders.reverse();

            for(var i = 0; i < h; i++){
                strLink += '/' + folders.pop();
            }

            folders.reverse();

            html += '<li><a href="'+pathX+'"><span>'+root_name+'</span></a></li>';

            for(var j = 0; j < folders.length; j++){
                html += '<li><a href="';
                strLink += '/' + folders[j];
                html += strLink + '/' + '""><span>' + folders[j] + '</span></a></li>';
            }

            $('div#addressbar ol').html(html);

        },

        // Set name of the folder as header
        updateHeader: function(){

            var curFolder = $.MediaBrowser.trim($.MediaBrowser.currentFolder, '/');
            tmp_pathX =  $.MediaBrowser.trim(pathX, '/');
            var folders = curFolder.split('/');

            last_folder = folders[folders.length-1];
            if(curFolder == tmp_pathX)
                last_folder = root_name;

            $('div#main div#filelist h2').text(last_folder);

        },

        // Open folders and select currently active folder
        updateTreeView: function(folder){
            $('ul.treeview li').removeClass();

            $('ul.treeview a[href=' + folder + ']')
                .parents('ul')
                .css({'display':'block'})
                .prevAll('a.children')
                .addClass('open')
                .end()
                .end()

                .parent()
                .addClass('selected')
                .end();

        },

        // Show detailed information over the selected file or folder
        updateFileSpecs: function(path, type){

            uri = getAjaxUri();
            uri += '&action=get_file_specs&path='+urlencode(path)+'&type='+type;

            $.getJSON(uri, function(resp){
                $('div#file-specs #info').html(resp.html);

                v = path;
                if(type == 'folder')
                    v = '';

                $('input#file').val(v);

                // update file form
                if(editor == 'standalone' || editor == 'tinymce')
                {
                    if(type == 'folder')
                        $('form#fileform').hide();
                    else
                        $('form#fileform').show();
                }
            });
        },

        filter: function(){
            if($.MediaBrowser.filterString != ''){

                if ($.MediaBrowser.currentView == "details"){
                    if (!$.MediaBrowser.tableHeadersFixed) $.MediaBrowser.fix_widths();
                    $('div#files table tbody tr').css({display: ""});
                } else {
                    $('div#files ul li').css({display: "block"});
                }

                $('div#files table tbody tr, div#files ul li').removeClass('filter');

                // Normalise
                var filterString = $('select#filters').val();
                var strFilter = $.trim(filterString.toLowerCase().replace(/\n/, '').replace(/\s{2,}/, ' '));

                if(strFilter != ""){

                    var arrList = [];
                    var rgxpFilter = new RegExp(strFilter,'i');

                    // Fill array with the list items or table rows depending on view
                    if ($.MediaBrowser.currentView == "details"){
                        arrList = $('div#files table tbody tr:not(.folder) .filename').get();

                        for(var i = 0; i < arrList.length; i++){
                            if ( !rgxpFilter.test( $(arrList[i]).text() ) ) $(arrList[i]).parent().addClass('filter').css({'display': 'none'});
                        }
                    } else {
                        arrList = $('div#files ul li a:not(.folder) .filename').get();

                        for(var i = 0; i < arrList.length; i++){
                            if ( !rgxpFilter.test( $(arrList[i]).text() ) ) $(arrList[i]).parent().parent().addClass('filter').css({'display': 'none'});
                        }
                    }

                }
            }

            $.MediaBrowser.searchLaunch();
        },

        loadFolder: function(folder){


            // Show loading icon
            $('div#files').html('<div class="loading"></div>');

            uri = getAjaxUri();
            uri += '&action=get_files&view='+ $.MediaBrowser.currentView+'&path='+urlencode(folder);

            $.getJSON(uri, function(resp){

                if(resp.result == 'ko')
                {
                    $('div#files').html("");
                    $.MediaBrowser.showMessage(resp.message, "error");
                }
                else
                {
                    $('input#search').val('');

                    $.MediaBrowser.setCurrentFolder(folder);
                    $.MediaBrowser.updateAddressBar();
                    $.MediaBrowser.updateHeader();
                    $.MediaBrowser.updateTreeView(folder);
                    $.MediaBrowser.updateFileSpecs(folder, 'folder');

                    $('div#files').html(resp.html);

                    $.MediaBrowser.filter();

                    $.MediaBrowser.contextmenu();
                }
            });
        },

        setCurrentFolder: function(str){
            $.MediaBrowser.currentFolder = str;
            $('input#uploadpath, input#folderpath').val(str);
        },

        setSelection: function(el, cm){

            var lastItemNo = null;
            var currentItemNo = null;
            var currentSelectedItem = $(el).attr('href');

            el = ($.MediaBrowser.currentView == 'details') ? $(el) : $(el).parent();
            var container = ($.MediaBrowser.currentView == 'details') ? 'tbody' : 'ul';

            if($.MediaBrowser.shiftKeyPressed && $.MediaBrowser.lastSelectedItem != null){
                $('div#files li a, div#files tr').each(function(i){
                    if($.MediaBrowser.lastSelectedItem == $(this).attr('href')){
                        lastItemNo = i;
                    }

                    if(currentSelectedItem == $(this).attr('href')){
                        currentItemNo = i;
                    }
                });

                if(isNumber(lastItemNo) && isNumber(currentItemNo)){
                    if(lastItemNo > currentItemNo){
                        for(i = currentItemNo; i <= lastItemNo; i++){
                            $('div#files li, div#files tr').eq(i).addClass('selected');
                        }
                    } else {
                        for(i = lastItemNo; i <= currentItemNo; i++){
                            $('div#files li, div#files tr').eq(i).addClass('selected');
                        }
                    }
                }
            }

            //See if selections should be removed
            if(!$.MediaBrowser.ctrlKeyPressed && !$.MediaBrowser.shiftKeyPressed){
                if(!cm || !el.hasClass("selected")){ //If click is called via a context menu then don't remove selections
                    el.parents(container)
                        .find('.selected')
                        .removeClass('selected')
                        .end();
                }

                el.addClass('selected');

            } else if($.MediaBrowser.ctrlKeyPressed && el.hasClass("selected")) { //If ctrl-key is pressed and item is already selected then deselect item
                el.removeClass('selected');
            } else {
                el.addClass('selected');
            }

            $.MediaBrowser.lastSelectedItem = currentSelectedItem;

        },

        // Quirksmode.org --> http://www.quirksmode.org/js/cookies.html
		createCookie: function(name, value, days) {
			if (days) {
				var date = new Date();
				date.setTime(date.getTime()+(days*24*60*60*1000));
				var expires = "; expires="+date.toGMTString();
			}
			else var expires = "";
			document.cookie = name+"="+value+expires+"; path=/";
		},

        getCookie: function(name){

            if(document.cookie.length > 0)
            {
                start=document.cookie.indexOf(name+"=");
                pos = start+name.length+1;
                if(start!=0)
                {
                    start=document.cookie.indexOf("; "+name+"=");
                    pos = start+name.length+3;
                }
                if(start!=-1)
                {
                    start=pos;
                    end=document.cookie.indexOf(";",start);
                    if(end==-1)
                    {
                        end=document.cookie.length;
                    }
                    return unescape(document.cookie.substring(start,end));
                }
            }

            return "";

        },

		trim: function(str, chars) {
			return $.MediaBrowser.ltrim($.MediaBrowser.rtrim(str, chars), chars);
		},

		ltrim: function(str, chars) {
			chars = chars || '\\s';
			return str.replace(new RegExp('^[' + chars + ']+', 'g'), '');
		},

		rtrim: function(str, chars) {
			chars = chars || '\\s';
			return str.replace(new RegExp('[' + chars + ']+$', 'g'), '');
		}
	};

})(jQuery);




