$(document).ready(function(){

    nTabInit('ntabs0');

    // autocomplete
    $('#table_search_input').keyup(function(){

        if($(this).val() == '')
        {
            $("#tables li").show();
            return;
        }

        $("#tables li").each(function(){

            txt = strtolower($(this).text());
            v = strtolower($('#table_search_input').val());
            if(txt.indexOf(v) == -1)
                $(this).hide();
            else
                $(this).show();

        });

    });


    // buttons
    $('#sql_btns button').each(function(){

        $(this).click(function(){

            if($('#canvas .table').length == 0)
            {
                alert(lang_msg_4);
                return;
            }

            tmp = explode(' ', $(this).text());
            attr = strtolower(tmp[0]);
            join_last_selected_fields = "";

            // JOIN ?
            if($(this).text() == "JOIN" && $('#canvas .table').length < 2)
            {
                alert(lang_msg_5);
                return;
            }

            if($(this).hasClass('selected'))
            {
                $(this).removeClass('selected');
                $('#canvas').removeClass();
            }
            else
            {
                $('#sql_btns button').removeClass('selected');
                $(this).addClass('selected');
                $('#canvas').removeClass();
                $('#canvas').addClass(attr);
            }

        });

    });


    // init from parent
    if(get_parent != '' && window.opener)
    {
        parent_v = window.opener.$('#'+get_parent).val();

        if(parent_v == '')
        {
            $('#table_search_input').focus();
        }
        else
        {
            parent_v = queryFormatCode(parent_v);
            $('#SqlCode').val(parent_v);
        }
    }


    $('#SqlCode').tabby();


    // multiple caracters
    $('#SqlCode').scroll(function() {

            t = $(this).scrollTop();
            l = $(this).scrollLeft();

            $('#SqlCodeHighlighter').scrollTop(t);
            $('#SqlCodeHighlighter').scrollLeft(l);

    });


    $('#SqlCode').keyup(function(e){updateSqlCode();});
    $('#SqlCode').keydown(function(e){

        // console.log(e.which);
        if(e.shiftKey)return;

        // "'
        /*if(e.which == 51 || e.which == 52 || e.which == 53 || e.which == 187)
        {
            if(e.which == 51 || e.which == 52)rep = "'[TEXT]'";
            else if(e.which == 53)rep = '()';
            else if(e.which == 187)rep = "= '[TEXT]'";

            sel_start = $(this)[0].selectionStart;
            sel_end = $(this)[0].selectionEnd;
            cur_val = $(this).val();
            text_before = cur_val.substring(0, sel_start);
            text_after = cur_val.substring(sel_end);
            cur_val = text_before+rep+text_after;
            $(this).val(cur_val);

            // exception for =
            if(e.which == 187)
                this.selectionStart = sel_start+3;
            else
                this.selectionStart = sel_start+1;
            this.selectionEnd = sel_start+strlen(rep)-1;

            e.preventDefault();
        }*/

        // ENTER && hightlight last string '[TEXT]'
        if(e.which == 13)
        {
            v = $(this).val();
            if(v.indexOf("'[TEXT]'") != -1)
            {
                $(this)[0].selectionStart = v.indexOf("'[TEXT]'")+1;
                $(this)[0].selectionEnd = v.indexOf("'[TEXT]'")+7;
                e.preventDefault();
            }
        }

        updateSqlCode();

    });


    $('#resizer').click(function(){

        if($(this).attr('opened') == '1')
        {
            $('#table_search').hide();
            $('#tables ul').hide();
            // $(this).css('margin-right', '275px');
            $(this).attr('opened', '0');
            $(this).attr('original_width', $('#tables').width());
            $('#tables').width(8);

        }
        else
        {
            // $(this).css('margin-right', '0px');
            $('#table_search').show();
            $('#tables ul').show();
            $(this).attr('opened', '1');

            ow = $(this).attr('original_width');
            $('#tables').width(ow+'px');
        }

        $(window).resize();
        reorderTable();
    });


    $(window).bind('resize', function(){
        nw = $('#tables_wrapper').width() - $('#tables').width() - 25;
        if(nw < 755)nw = 755;

        $('#right_view').width(nw);
        // $('#canvas').width($('#canvas').width()+5);
        $('#SqlCode').width(nw-10);
        $('#SqlCodeHighlighter').width(nw-10);
        $('#canvas').width(nw-20);
    });

    $(window).resize();

    $('#tables li').click(function(){
        tableAdd($(this).text());
    });

    // canvas scroll
    $('#canvas').scroll(function() {
        $('#canvas').css('background-position', '0 -'+$('#canvas').scrollTop()+'px');

    });

    // query limit
    $('#Querylimit').keydown(function(event){
        if(event.which == 13)
            $('#QueryPreviewButton').click();
    });

    // query button
    $('#QueryPreviewButton').click(function(){

        v = $('#SqlCode').val();
        v = trim(v);
        if(empty(v))
        {
            alert(lang_msg_6);
            return;
        }

        // verify limit
        limit = $('#Querylimit').val();
        if(isNaN(limit))
        {
            alert(lang_msg_7);
            return;
        }

        if(limit > 500)
        {
            alert(lang_msg_8);
            return;
        }


        pos = $('#previewer').position();
        $('#ajax_loader').css('top', pos.top+10+'px');
        $('#ajax_loader').css('left', pos.left+10+'px');
        $('#ajax_loader').show();

        uri = '?mod=_visual-query-builder&do=exec&ajaxer=1&action=query_preview';
        $.post(uri, {query:v, limit:limit}, function(resp){
            $('#previewer').html(resp);
            $('#ajax_loader').hide();
        });
    });


    // Save
    $('#VQBSave').click(function(){

        v = $('#SqlCode').val();

        if(get_parent != '' && window.opener)
        {
            if(!window.opener.$('#'+get_parent))
            {
                alert(lang_msg_9);
            }
            else
            {
                window.opener.$('#'+get_parent).val(v);
                window.close();
                return;
            }
        }

        msg = lang_msg_10+" :\n\n"+v;
        alert(msg);
        window.close();

    });


});