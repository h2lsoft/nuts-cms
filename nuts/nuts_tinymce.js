function loadRichEditor(theme)
{
    if(empty(current_theme))theme = current_theme;
    theme_url = "/themes/editor_css.php?t="+theme+"&tstmp="+time();


    // remove all tinymce instance to prevent ajax bug
    tinyMCE.editors=[];
    tinymce.remove();

    $('textarea.mceEditor').each(function(){

        id = $(this).attr('id');
        $(this).css('float', 'left');
        
        width = $(this).width();
        
        tmp_lang = strtolower(nutsUserLang)+'_'+strtoupper(nutsUserLang);
        if(tmp_lang == 'en_EN')tmp_lang = '';
        
        tinyMCE.init({
	            branding: false,
	            browser_spellcheck : true,

                language : tmp_lang,
                content_css: theme_url,
                document_base_url : WEBSITE_URL+"/",
                relative_urls : false,
                remove_script_host : true,
                convert_urls : false,
                custom_undo_redo_levels: 40,
                selector: "#"+id,
                theme: "modern",
                inline: false,
                schema: "html5",
                image_advtab: true,
                relative_urls : true,
                width: width,
                importcss_append: true,
                style_formats_merge: true,

                plugins: [
                    "advlist autolink lists link image charmap anchor preview",
                    "visualblocks code fullscreen wordcount template hr",
                    "media table contextmenu directionality paste searchreplace textcolor importcss"
                ],

                contextmenu: "link | formats | cell row column deletetable",

                file_browser_callback :  function(field_name, url, type, win) {
                    fileBrowserURL = WEBSITE_URL+"/app/file_browser/index.php?editor=tinymce&filter="+type;
                    folder = '';
                    if(type == 'image')imgBrowser(field_name, folder);
                    else if(type == 'media')mediaBrowser(field_name, folder);
                    else allBrowser(field_name, folder);

                    return false;

                },
	        
                external_plugins: {},
	        
                plugin_preview_width : "1150",
                plugin_preview_height : "750",

                code_dialog_width : "1150",
                code_dialog_height : "750",

                templates: "index.php?_action=rte_get-templates",
                link_list: "index.php?_action=rte_get-link_list",


                menubar : true,
                menu : {
                    file   : {title : 'File'  , items : 'preview xcode2 | fullscreen'},
                    edit   : {title : 'Edit'  , items : 'undo redo | cut copy paste pastetext | selectall | searchreplace'},
                    format : {title : 'Format', items : 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
                    insert : {title : 'Insert', items : 'image widget | youtube dailymotion video audio | iframe embed | map | hr template'},
                    table  : {title : 'Table' , items : 'inserttable tableprops deletetable | cell row column'},
                    tools  : {title : 'Tools' , items : 'visualaid visualblocks | charmap | fullscreen '}
                },

                toolbar1: "bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent blockquote | link unlink | xcode2",

                setup: function(editor) {

                    editor.on('focus activate', function(e) {
                        id = tinymce.activeEditor.id;
                        $('.mce-tinymce').removeClass('mce-panel-focus');
                        $('#'+id).prev('.mce-tinymce').addClass('mce-panel-focus');

                    });

                    // xcode2 => add button
                    editor.addButton('xcode2', {
                        icon: 'code',
                        tooltip: 'Source code',
                        onclick: function(){
                            popupModalV2('/nuts/code_editor.php?syntax=html&parent_target=tinymce', 'Source Code', 1200, 750);
                        }
                    });

                    // xcode2 => add menu item
                    editor.addMenuItem('xcode2', {
                        icon: 'code',
                        text: 'Source code',
                        context: 'tools',
                        onclick: function(){
                            popupModalV2('/nuts/code_editor.php?syntax=html&parent_target=tinymce', 'Source Code', 1200, 750);
                        }
                    });

                    // widget => add menu item => http://www.nuts-cms.com/nuts/index.php?mod=_gallery&do=list&popup=1&parent_refresh=no&parentID=Text
                    editor.addMenuItem('widget', {
                        image: '/nuts/img/widget.png',
                        text: (nutsUserLang == 'fr') ? "Insérer un widget" : "Insert a widget",
                        onclick: function(){
                            popupModalV2('/nuts/widgets.php?parentID=tinymce&popup=1', 'Widget', 1200, 750);
                        }
                    });

                    // youtube
                    editor.addMenuItem('youtube', {
                        image: '/plugins/_media/img/youtube.png',
                        text: ((nutsUserLang == 'fr') ? "Insérer une vidéo Youtube" : "Insert a video Youtube"),
                        onclick: function(){
                            popupModalV2('/nuts/index.php?mod=_media&do=list&user_se=1&Type=YOUTUBE%20VIDEO&Type_operator=_equal_&popup=1&parent_refresh=no&parentID=tinymce', 'Youtube', 1200, 750);
                        }
                    });

                    // dailymotion
                    editor.addMenuItem('dailymotion', {
                        image: '/plugins/_media/img/dailymotion.png',
                        text: (nutsUserLang == 'fr') ? "Insérer une vidéo Dailymotion" : "Insert a video Dailymotion",
                        onclick: function(){
                            popupModalV2('/nuts/index.php?mod=_media&do=list&user_se=1&Type=DAILYMOTION&Type_operator=_equal_&popup=1&parent_refresh=no&parentID=tinymce', 'Dailymotion', 1200, 750);
                        }
                    });

                    // video
                    editor.addMenuItem('video', {
                        image: '/plugins/_media/img/video.png',
                        text: (nutsUserLang == 'fr') ? "Insérer une vidéo" : "Insert a video",
                        onclick: function(){
                            popupModalV2('/nuts/index.php?mod=_media&do=list&user_se=1&Type=VIDEO&Type_operator=_equal_&popup=1&parent_refresh=no&parentID=tinymce', 'Video', 1200, 750);
                        }
                    });

                    // audio
                    editor.addMenuItem('audio', {
                        image: '/plugins/_media/img/audio.png',
                        text: (nutsUserLang == 'fr') ? "Insérer une fichier audio" : "Insert an audio file",
                        onclick: function(){
                            popupModalV2('/nuts/index.php?mod=_media&do=list&user_se=1&Type=AUDIO&Type_operator=_equal_&popup=1&parent_refresh=no&parentID=tinymce', 'Audio', 1200, 750);
                        }
                    });

                    // map
                    editor.addMenuItem('map', {
                        image: '/plugins/_gmaps/icon.png',
                        text: (nutsUserLang == 'fr') ? "Insérer une map" : "Insert a map",
                        onclick: function(){
                            popupModalV2('/nuts/index.php?mod=_gmaps&do=list&popup=1&parent_refresh=no&parentID=tinymce', 'Map', 1200, 750);
                        }
                    });

                    // iframe
                    editor.addMenuItem('iframe', {
                        image: '/plugins/_media/img/iframe.png',
                        text: ((nutsUserLang == 'fr') ? "Insérer une iframe" : "Insert an iframe"),
                        onclick: function(){
                            popupModalV2('/nuts/index.php?mod=_media&do=list&user_se=1&Type=IFRAME&Type_operator=_equal_&popup=1&parent_refresh=no&parentID=tinymce', 'Iframe', 1200, 750);
                        }
                    });

                    // embed
                    editor.addMenuItem('embed', {
                        image: '/plugins/_media/img/embed.png',
                        text: ((nutsUserLang == 'fr') ? "Insérer code intégré" : "Insert an embed code"),
                        onclick: function(){
                            popupModalV2('/nuts/index.php?mod=_media&do=list&user_se=1&Type=EMBED%20CODE&Type_operator=_equal_&popup=1&parent_refresh=no&parentID=tinymce', 'Embed', 1200, 750);
                        }
                    });
	                
                }

            });

    });
    
    // $('.mce-container, .mce-container-body').css('float', 'left!important');

}

function forceWYSIWYGUpdate(){}
function WYSIWYGhackSubmit() {}


function WYSIWYGAddText(id, txt)
{
    txt = str_replace('``', '"', txt);

    if(id == 'tinymce')
    {
        tinymce.activeEditor.insertContent(txt);
    }
    else
    {
        v = $('textarea#'+id).val();
        v += txt;
        $('textarea#'+id).val(v);
    }

}