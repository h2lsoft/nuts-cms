var swfu;
var foldercmenu;
var filecmenu;
var imagecmenu;
var cmenu;


$(function(){

    $('#filelist').css('opacity', 0);

    loading_msg = (nutsUserLang == 'fr') ? 'Chargement' : 'Loading';

    $.blockUI.defaults.css.cursor = 'default';
    $.blockUI({

        onUnblock: function(){

            $('#filelist').css('opacity', 1);

        },
        message: "<img src='img/ajaxLoader.gif' align='absmiddle' style='margin-right:5px; width:24px;' /> "+loading_msg, css: {'border-radius':'0px', 'border-width': '2px', 'border-color':'#999', height:'50px', 'line-height': '50px', 'font-weight': 'normal', 'font-size': '16px'}});

    // *** Context Menu ***//
    $.contextMenu.theme = 'mb';
    $.contextMenu.shadowOpacity = .3;

    //Check if a url should be returned absolute
    $.MediaBrowser.absoluteURL = $('#absolute_url').is(':checked') ? true : false;

    // activate folder/file selection before show
    $.contextMenu.beforeShow = function(){
        $('table.contextmenu, div.context-menu-shadow').css({'display': 'none'});

        if(!allowed_actions['paste'])
        {
            $('table.contextmenu div.context-menu-item[title=paste]').addClass('context-menu-item-disabled');
        }
        else
        {
            // Enable paste button if clipboard has items
            if($.MediaBrowser.clipboard.length > 0){
                $('table.contextmenu div.context-menu-item').removeClass('context-menu-item-disabled');
            } else {
                $('table.contextmenu div.context-menu-item[title=paste]').addClass('context-menu-item-disabled');
            }
        }

        // Show selection of file, folder or image
        if($(this.target).hasClass('folder'))
            $.MediaBrowser.selectFileOrFolder(this.target, $(this.target).attr('href'), 'folder', 'cmenu');

        if($(this.target).hasClass('file'))
            $.MediaBrowser.selectFileOrFolder(this.target, $(this.target).attr('href'), 'file', 'cmenu');

        if($(this.target).hasClass('image'))
            $.MediaBrowser.selectFileOrFolder(this.target, $(this.target).attr('href'), 'image', 'cmenu');

        return true;
    }

    $.MediaBrowser.contextmenu();


    // assign values
    $.MediaBrowser.resizeWindow();
    $.MediaBrowser.setCurrentFolder($('input#currentfolder').val());
    $.MediaBrowser.currentView = $('input#currentview').val();

    // Add event handlers to links in treeview
    $('ul.treeview a[href]').live('click', function(event){
        $.MediaBrowser.loadFolder($(this).attr('href'));
        event.preventDefault();
    });

    // tree width
    cookie_name = 'nuts_file_explorer_tree_width';
    if(editor == 'edm')cookie_name = 'nuts_edm_tree_width';

    tree_width = $.MediaBrowser.getCookie(cookie_name);
    tree_width = parseInt(tree_width);
    if(tree_width)$('#tree').width(tree_width);

    $("#tree").bind("resize", function(event, ui) {
        w = $('#tree').width();
        nw = $(document).width() - w;
        $('#main').width(nw);

        cookie_name = 'nuts_file_explorer_tree_width';
        if(editor == 'edm')cookie_name = 'nuts_edm_tree_width';

        $.MediaBrowser.createCookie(cookie_name, w, 365);
    });

    // tree init
    $.MediaBrowser.reloadTree();

    // *** Navbar ***//

    // Style navbar children
    $('div#navbar ul li:has(ul) > a')
        .addClass('children')
        .append('<span class="options"><img src="img/spacer.gif" width="20" /></span>')
        .click(function(){
            $(this).next().toggle().end().toggleClass('selected');
            return false;
        })
        .end();

    // select all <li> with children
    $('div#navbar ul li:has(ul)').hover(function(){},function(event){
        $('a.selected', this).removeClass('selected').next().hide();
        event.preventDefault();
    });


    // Add event handlers to links in addressbar
    $('div#addressbar a[href]').live('click', function(event){
        $.MediaBrowser.loadFolder($(this).attr('href'));
        event.preventDefault(); //Don't follow link
    });

    // Add event handlers to links in addressbar
    $('input#fn').live('keyup', function(event){
        if(this.value != this.defaultValue){
            $('a.save_rename').css({'display':'inline'});
        } else {
            $('a.save_rename').css({'display':'none'});
        }
    });

    // *** Events *** //

    // Check if ctrl key or shift key is pressed
    $(document).keydown(function(event) {
        if(event.ctrlKey || event.metaKey){
            $.MediaBrowser.ctrlKeyPressed = true;
        }
        if(event.shiftKey){
            $.MediaBrowser.shiftKeyPressed = true;
        }

        // delete pressed 46
        if(editor != 'edm' && event.which == 46 && $.MediaBrowser.isFilesSelection())
        {
            total = $.MediaBrowser.isFilesSelection();
            msg = "Do you really want to delete these "+total+" items ?";
            if(nutsUserLang == 'fr')
                msg = "Voulez-vous supprimer ces "+total+" éléments ?";

            if(confirm(msg))
                $.MediaBrowser.delete_all();
        }
    });

    $(document).keyup(function(event) {
        $.MediaBrowser.ctrlKeyPressed = false;
        $.MediaBrowser.shiftKeyPressed = false;
    });

    // If filter has changed then apply new filtering
    $('select#filters').change(function(){
        $.MediaBrowser.filter();
    });

    // Details TH click event
    $('#details th').live('click', function(e){
        direction = 'up';
        if(!$(this).hasClass('selected'))
        {
            $('#details th').removeClass('selected');
            $('#details th').removeClass('selected_up');
            $('#details th').removeClass('selected_down');
            $(this).addClass('selected');
            $(this).addClass('selected_up');
        }
        else
        {
            if(!$(this).hasClass('selected_up'))
            {
                $('#details th').removeClass('selected_down');
                $(this).addClass('selected_up');
            }
            else
            {
                $('#details th').removeClass('selected_up');
                $(this).addClass('selected_down');
                direction = 'down';
            }
        }

        tableDetailsSort($(this).attr('type'), direction);
        $.MediaBrowser.contextmenu();
        event.preventDefault(); //Don't follow link
    });


    // Folder events
    $('div#files ul li a.folder, div#files table tr.folder').live('dblclick', function(event){
        $.MediaBrowser.loadFolder($(this).attr('href'));
        event.preventDefault(); //Don't follow link
    });

    $('div#files ul li a.folder, div#files table tr.folder').live('click', function(event){
        if (event.button != 0) return true; //If right click then return true
        $.MediaBrowser.selectFileOrFolder(this,$(this).attr('href'),'folder'); //Select clicked folder
        event.preventDefault(); //Don't follow link
    });


    // File events
    $('div#files ul li a.file, div#files table tr.file').live('click', function(event){
        if (event.button != 0) return true; //If right click then return true
        $.MediaBrowser.selectFileOrFolder(this,$(this).attr('href'),'file'); //Select clicked file
        event.preventDefault(); //Don't follow link
    });

    $('div#files ul li a.file, div#files table tr.file').live('dblclick', function(event){
        $("form#fileform input#file").val($(this).attr('href'));
        $.MediaBrowser.insertFile();
        event.preventDefault(); //Don't follow link
    });

    // Image events
    $('div#files ul li a.image, div#files table tr.image').live('click', function(event){
        if (event.button != 0) return true; //If right click then return true
        $.MediaBrowser.selectFileOrFolder(this,$(this).attr('href'),'image'); //Select clicked image
        event.preventDefault(); //Don't follow link
    });

    $('div#files ul li a.image, div#files table tr.image').live('dblclick', function(event){
        var host = '';
        var path = $(this).attr('href');

        if($.MediaBrowser.absoluteURL){
            host = $.MediaBrowser.hostname;
        }

        $("form#fileform input#file").val(host + path);
        $.MediaBrowser.insertFile();
        event.preventDefault(); //Don't follow link
    });

    $('div#files').click(function(event){
        if (event.button != 0) return true; //If right click then return true
        if ($(event.target).closest('.files').length == 1) return true;

        $('div#files li.selected, div#files tr.selected').removeClass("selected"); //Deselect all selected items
        $.MediaBrowser.updateFileSpecs($.MediaBrowser.currentFolder, 'folder');
        $.MediaBrowser.currentFile = '';

        $('table.contextmenu, div.context-menu-shadow').css({'display': 'none'}); //Hide all contextmenus
    });


    // Start searching while typing
    $('#searchbar input#search').keyup(function(event){

        var keycode = event.keyCode;
        if(!(keycode == 9 || keycode == 13 || keycode == 16 || keycode == 17 || keycode == 18 || keycode == 38 || keycode == 40 || keycode == 224))
            $.MediaBrowser.searchFor($(this).val());

        event.preventDefault();
    });



    // If a filter is specified then hide files with a wrong file type
    $.MediaBrowser.filter();

    // Set currently selected folder and view
    $.MediaBrowser.setCurrentFolder($('input#currentfolder').val());
    $.MediaBrowser.currentView = $('input#currentview').val();





    //Absolute URl active/inactive
    $('#absolute_url').click(function(){
        if ($('#absolute_url').is(':checked')) {
            $.MediaBrowser.absoluteURL = true;
            if ($.MediaBrowser.currentFile !== '') {
                $("form#fileform input#file").val($.MediaBrowser.hostname + $.MediaBrowser.currentFile);
            }
            $.MediaBrowser.createCookie('absoluteURL', 1, 365);
        }
        else
        {
            $.MediaBrowser.absoluteURL = false;
            if ($.MediaBrowser.currentFile !== '') {
                $("form#fileform input#file").val($.MediaBrowser.currentFile);
            }
            $.MediaBrowser.createCookie('absoluteURL', 0, 365);
        }
    });



    // Reset layout if window is being resized
    window.onresize = window.onload = function(){
        $.MediaBrowser.resizeWindow();
    };


    // fancybox
    $("a#fancybox").fancybox({hideOnContentClick: true});


    // uploader
    $("#html5_uploader").pluploadQueue({
        runtimes : 'html5',
        url : 'index.php?ajax=1&action=upload&editor='+editor,
        max_file_size : max_file_size,

        // PreInit events, bound before any internal events
        preinit : {
            UploadFile: function(up, file) {
                up.settings.multipart_params = {path: $("#addressbar ol li:last-child a").attr('href'), lang: nutsUserLang};
            }
        },

        // Post init events, bound after the internal events
        init : {
            FilesAdded: function(up, files){
                up.start();
            },

            FileUploaded: function(up, file, response) {
                if(response.response != 'ok'){
                    msg = response.response+" ("+file.name+")";
                    $.MediaBrowser.showMessage(msg, 'error');
                }

                // reset
                if(up.total.queued == 0) {
                    $(".plupload_upload_status").fadeOut('normal', function(){
                        $(".plupload_buttons").show();
                        up.splice();
                        $('#upload_window a.close').click();
                        $.MediaBrowser.loadFolder($.MediaBrowser.currentFolder);
                    });
                }
            }
        }
    });


    // add drag and drop utility
    /*if(allowed_actions['upload'])
    {

        aDivElement = document.getElementById('files');
        aDivElement.ondragenter = function(e) {
            nWindowOpen('upload_window');
            e.dataTransfer.dropEffect = 'move'
            e.preventDefault();
            return false;
        };
    }
     */


    // draggable window
    /* $('.n_window').draggable({handle: ".n_titlebar", cursor:"move", cancel:".n_content" }); */
    $('.n_window').draggable({
                                handle: ".n_titlebar",
                                cursor:"move",
                                stop: function(event, ui ) {

                                    console.log(ui);

                                }
                            });
    // .disableSelection().css('webkit-user-select','none')

    // detect ESC on window
    shortcut.add('Esc', function(){

        if($('#upload_window').is(':visible')){
            nWindowClose('upload_window');
            return;
        }

        if($('#groups_window').is(':visible')){
            nWindowClose('groups_window');
            return;
        }

        if($('#users_window').is(':visible')){
            nWindowClose('users_window');
            return;
        }

        if($('#rights_window').is(':visible')){
            nWindowClose('rights_window');
            return;
        }

        if($('#share_window').is(':visible')){
            nWindowClose('share_window');
            return;
        }

    });

    // detect java
    if(editor == 'edm')
    {
        if(!navigator.javaEnabled())
        {
            // $.MediaBrowser.showMessage("Java plugin must be installed,<br>please download it at <a href='http://www.java.com' target='_blank'>Java website</a>", "special");
        }
    }


    // no fullscreen
    if(top === self)
        $('#option_fullscreen').remove();


    // unblock
    timer_unblock = 1000;
    if(editor == 'edm')timer_unblock = 2000;
    setTimeout(function(){$.unblockUI();}, timer_unblock);


    // load default folder
    if(!empty(load_folder))
    {
        setTimeout(function(){
            $.MediaBrowser.loadFolder(load_folder);
        }, 1000);

    }


    // Chrome && FF bug zoom must be 100%
    zoom_level = parseInt(document.defaultView.getComputedStyle(document.documentElement, null).width,10)/document.documentElement.clientWidth;
    if(zoom_level != 1)
    {
        msg = "Warning !\n==========\n\nYour zoom level must be 100% to view contents of the explorer, please fix it by pressing keys `Ctrl+0`";
        if(nutsUserLang == 'fr')
            msg = "Attention !\n==========\n\nVotre niveau de zoom doit être à 100% pour voir les contenus de l'explorateur, merci de corriger ceci en appuyant sur les touches `Ctrl+0`";
        alert(msg);
    }

    // add scroll bind
    $('#tree').bind('scroll', function(){
        $('.ui-resizable-handle').css('top', $('#tree').scrollTop()+'px');
    });


    // add lazy loading
    $('#files').scroll(function(){
        $.MediaBrowser.lazyLoading();
    });


});