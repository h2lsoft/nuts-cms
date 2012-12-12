var from_init = false;
function tableAdd(table_name, alias_name)
{
    if(!from_init)
    {
        pos = $('#canvas').position();
        $('#ajax_loader').css('top', pos.top+10+'px');
        $('#ajax_loader').css('left', pos.left+10+'px');
        $('#ajax_loader').show();
    }


    uri = '?mod=_visual-query-builder&do=exec&ajaxer=1&action=get_fields'
    $.post(uri, {table:table_name}, function(resp){

        c = $('#canvas').html();
        guid = uniqid(md5());

        tpl = '<div class="table" id="table_'+guid+'">';
        tpl += '    <div class="table_title">'+table_name;
        tpl += '        <a href="javascript:;" onclick="tableDelete(\'table_'+guid+'\')" class="table_delete"></a>';
        tpl += '    </div>';
        tpl += '    <div class="table_fields">';
        tpl += '        <ul>';

        for(i=0; i < resp.length; i++)
        {
            tpl += '<li class="'+strtolower(resp[i]['Key'])+'" tableID="table_'+guid+'">';
            tpl += resp[i]['Field'];
            tpl += '</li>';
        }

        tpl += '        </ul>';
        tpl += '    </div>';
        tpl += '    <div class="table_alias">';

        if(empty(alias_name))
            tpl += '    Alias <input type="text" class="table_alias_input" />';
        else
            tpl += '    Alias <input type="text" class="table_alias_input" value="'+alias_name+'" />';

        tpl += '    </div>';
        tpl += '</div>';

        // c += tpl;

        $(tpl).appendTo("#canvas").draggable({
            handle: ".table_title",
            cursor:"move",
            /* containment:'#canvas', */

            start: function(event, ui){
                 $('#canvas').css('background','');
                // repaintJoins();

            },

            stop: function(event, ui){
                repaintJoins();
            }


        });

        // $('#canvas').html(c);

        // field click
        $('#table_'+guid).each(function(){

            $(this).find('li').click(function(){

                if($('#sql_btns .selected').length == 0)
                {
                    alert(lang_msg_12);
                    return;
                }

                field_name = trim($(this).text());
                table_name_ID = $(this).attr('tableID');
                table_name = $('#'+table_name_ID+' .table_title').text();
                table_name = trim(table_name);
                alias_name = $('#'+table_name_ID+' .table_alias_input').val();
                alias_name = trim(alias_name);

                arr = query2Array();

                // SELECT
                if($('#sql_btns .selected').text() == 'SELECT')
                {
                    if($('#canvas .table').length == 1)
                        str = field_name;
                    if($('#canvas .table').length > 1)
                    {
                        if(!empty(alias_name))
                            str = alias_name+'.'+field_name;
                        else
                            str = table_name+'.'+field_name;
                    }

                    // already selected ?
                    if(!$(this).hasClass('in_select'))
                    {
                        arr['SELECT'] = trim(arr['SELECT']);
                        if(empty(arr['SELECT']))
                        {
                            arr['SELECT'] = "\t"+str;
                        }
                        else
                        {
                            arr['SELECT'] += ",\n\t"+str;
                        }
                    }
                    else
                    {
                        tmp = arr['SELECT'];
                        lines = explode('\n', tmp);
                        str2 = "";
                        for(i=0; i < count(lines); i++)
                        {
                            line = lines[i];
                            line = str_replace('`', '', line);
                            line = str_replace(',', '', line);
                            line = trim(line);
                            if($('#canvas .table').length == 1)
                            {
                                if(line != field_name)
                                    str2 += lines[i]+"\n";
                            }
                            else
                            {
                                if(!empty(alias_name))
                                    pattern = alias_name+'.'+field_name;
                                else
                                    pattern = table_name+'.'+field_name;

                                if(line != pattern)
                                    str2 += lines[i]+"\n";
                            }
                        }

                        str2 = rtrim(str2);
                        if(!empty(str2))
                        {
                            if(str2[strlen(str2)-1] == ',')
                                str2 = str2.substr(0, strlen(str2)-1);
                        }

                        arr['SELECT'] = str2;

                    }
                }

                // WHERE
                if($('#sql_btns .selected').text() == 'WHERE')
                {
                    add = prompt(lang_msg_13, "=");
                    add = strtoupper(trim(add));
                    str_op = add+" '[TEXT]'";

                    if(add == 'BETWEEN')str_op = add+" '[TEXT]' AND '[TEXT]'";
                    if(add == 'LIKE')str_op = add+" '[TEXT]%'";
                    if(add == 'IN')str_op = add+"('[TEXT]')";

                    if($('#canvas .table').length == 1)
                        str = field_name;
                    if($('#canvas .table').length > 1)
                    {
                        if(!empty(alias_name))
                            str = alias_name+'.'+field_name;
                        else
                            str = table_name+'.'+field_name;
                    }

                    arr['WHERE'] = trim(arr['WHERE']);
                    if(empty(arr['WHERE']))
                    {
                        arr['WHERE'] = "\t"+str+" "+str_op;
                    }
                    else
                    {
                        arr['WHERE'] += " AND\n\t"+str+" "+str_op;
                    }
                }

                // GROUP BY
                if($('#sql_btns .selected').text() == 'GROUP BY')
                {
                    if($('#canvas .table').length == 1)
                        str = field_name;
                    if($('#canvas .table').length > 1)
                    {
                        if(!empty(alias_name))
                            str = alias_name+'.'+field_name;
                        else
                            str = table_name+'.'+field_name;
                    }

                    arr['GROUP BY'] = trim(arr['GROUP BY']);
                    if(empty(arr['GROUP BY']))
                    {
                        arr['GROUP BY'] = "\t"+str;
                    }
                    else
                    {
                        arr['GROUP BY'] += ",\n\t"+str;
                    }
                }

                // ORDER BY
                if($('#sql_btns .selected').text() == 'ORDER BY')
                {
                    if($('#canvas .table').length == 1)
                        str = field_name;
                    if($('#canvas .table').length > 1)
                    {
                        if(!empty(alias_name))
                            str = alias_name+'.'+field_name;
                        else
                            str = table_name+'.'+field_name;
                    }

                    // add sort
                    str += ' '+$('#sql_btns input:checked').val();


                    arr['ORDER BY'] = trim(arr['ORDER BY']);
                    if(empty(arr['ORDER BY']))
                    {
                        arr['ORDER BY'] = "\t"+str;
                    }
                    else
                    {
                        arr['ORDER BY'] += ",\n\t"+str;
                    }
                }

                // JOIN
                if($('#sql_btns .selected').text() == 'JOIN')
                {
                    if($('#canvas .table').length == 1)
                        str = field_name;
                    if($('#canvas .table').length > 1)
                    {
                        if(!empty(alias_name))
                            str = alias_name+'.'+field_name;
                        else
                            str = table_name+'.'+field_name;
                    }

                    if(empty(join_last_selected_fields))
                    {
                        join_last_selected_fields = str;
                        return;
                    }
                    else
                    {
                        arr['WHERE'] = trim(arr['WHERE']);
                        if(empty(arr['WHERE']))
                        {
                            arr['WHERE'] = "\t"+str+" = "+join_last_selected_fields;
                        }
                        else
                        {
                            arr['WHERE'] += " AND \n\t"+str+" = "+join_last_selected_fields;
                        }

                        join_last_selected_fields = "";
                        $('#sql_btns button').removeClass('selected');
                        $('#canvas').removeClass();
                    }

                }




                v = queryArray2String(arr);
                $('#SqlCode').val(v);
                tableRepaint();
                repaintJoins();

                // hightlight last string '[TEXT]'
                if(v.indexOf("'[TEXT]'") != -1)
                {
                    $('#SqlCode')[0].selectionStart = v.indexOf("'[TEXT]'")+1;
                    $('#SqlCode')[0].selectionEnd = v.indexOf("'[TEXT]'")+7;
                    $('#SqlCode').focus();
                }

                updateSqlCode();

            });


            // alias ***********************************************************
            $(this).find('input').change(function(){queryGenerate();});
            $(this).find('input').keydown(function(event){
                if(event.which == 13){
                    queryGenerate();
                    event.preventDefault();
                }
            });




        });

        if(!from_init)
        {
            queryGenerate();
            reorderTable();
            tableRepaint();
            $('#ajax_loader').hide();
        }

    }, 'json');
}



function tableDelete(guid)
{
    $('#sql_btns button').removeClass('selected');
    $('#canvas').removeClass();
    $('#'+guid).remove();

    join_tables = [];

    queryGenerate();
    reorderTable();
    tableRepaint();
}




function tableRepaint()
{
    arr = query2Array();
    str = trim(arr['SELECT']);
    lines = explode('\n', str);

    all_fields = [];

    // apply special class to li fields in_select
    $('.table_fields li').removeClass('in_select');
    $('.table_fields li').each(function(){

        for(i=0; i < lines.length; i++)
        {
            line = lines[i];
            line = str_replace('`', '', line);
            line = str_replace(',', '', line);
            line = trim(line);

            line_tmp = explode(' AS ', line);
            if(count(line_tmp) == 2)
                line = trim(line_tmp[0]);
            field = trim($(this).text());

            if($('#canvas .table').length == 1)
            {
                if(line == field)
                {
                    $(this).addClass('in_select');
                }
            }
            else
            {
                table_name_ID = $(this).attr('tableID');
                table_name = $('#'+table_name_ID+' .table_title').text();
                table_name = trim(table_name);
                alias_name = $('#'+table_name_ID+' .table_alias_input').val();
                alias_name = trim(alias_name);

                pattern = table_name+'.'+field;
                if(!empty(alias_name))
                    pattern = alias_name+'.'+field;

                all_fields[all_fields.length] = pattern;
                if(line == pattern)
                {
                    $(this).addClass('in_select');
                }
            }
        }
    });

    // apply special class to li fields in_join
    join_tables_guid = [];
    $('.table_fields li').removeClass('in_join');
    if($('#canvas .table').length >= 2)
    {
        str = trim(arr['WHERE']);
        lines = explode('\n', str);
        $('.table_fields li').each(function(){

            field = trim($(this).text());
            table_name_ID = $(this).attr('tableID');
            table_name = $('#'+table_name_ID+' .table_title').text();
            table_name = trim(table_name);
            alias_name = $('#'+table_name_ID+' .table_alias_input').val();
            alias_name = trim(alias_name);

            pattern = table_name+'.'+field;
            if(!empty(alias_name))pattern = alias_name+'.'+field;

            for(i=0; i < lines.length; i++)
            {
                line = lines[i];
                line = str_replace('`', '', line);
                line = str_replace(',', '', line);
                line = str_replace(' AND', '', line);
                line = str_replace('  ', ' ', line);
                line = trim(line);

                if(line.indexOf(' = ') != -1)
                {
                    qparts = explode(' = ', line);
                    console.log(pattern);
                    console.log(qparts);
                    if((pattern == qparts[0] || pattern == qparts[1]) && in_array(qparts[0], all_fields) && in_array(qparts[1], all_fields))
                    {
                        $(this).addClass('in_join');

                        found = false;
                        for(k=0; k < join_tables.length;  k++)
                        {
                            cur_join = join_tables[k];
                            if(cur_join[0] == qparts[0] && cur_join[1] == qparts[1])
                            {
                                join_tables[k][3] = table_name_ID;
                                found = true;
                            }
                        }

                        // create first join
                        if(!found)
                        {
                            join_tables[join_tables.length] = [qparts[0], qparts[1], table_name_ID, ''];
                        }

                    }
                }
            }
        });
    }


    repaintJoins();

}




var number_table = 0;
var number_table_triple = 0;
function reorderTable()
{
    number_table = 0;
    number_table_triple = 0;
    $('#canvas .table').each(function(index){

        if(number_table == 0)
        {
            $(this).css('margin-left', '10px');
        }
        else if(number_table_triple == 3 && $('#resizer').attr('opened') == 1)
        {
            number_table_triple = 0;
            $(this).css('margin-left', '10px');
        }
        else
        {
            if($('#resizer').attr('opened') == 1)
                $(this).css('margin-left', '30px');
            else
                $(this).css('margin-left', '60px');

        }

        number_table_triple++;
        number_table++;

    });

}



join_colors = ['black', 'green', 'blue', 'red', 'purple', 'violet', 'salmon'];
function repaintJoins()
{
    join_colors_index = 0;

    // clear canvas
    $('#canvas_bkg').remove();
    $('<canvas id="canvas_bkg" width="'+$('#canvas').width()+'" height="'+$('#canvas').height()+'" style="display:none;"></canvas>').appendTo('body');
    ctx = $('#canvas_bkg')[0].getContext('2d');


    joined_tables = [];
    for(i=0; i < join_tables.length; i++)
    {
        table1_guid =  join_tables[i][2];
        table2_guid =  join_tables[i][3];

        if(!in_array(table1_guid+"."+table2_guid, joined_tables))
        {
            // pos table 1
            pos1 = $('#'+table1_guid).position();
            pos2 = $('#'+table2_guid).position();
            if(is_object(pos1) && is_object(pos2))
            {
                w1 = $('#'+table1_guid).width();
                h1 = $('#'+table1_guid).height();
                x1 = pos1.left-w1;
                y1 = pos1.top-80;

                // pos table 2
                w2 = $('#'+table2_guid).width();
                h2 = $('#'+table2_guid).height();
                x2 = pos2.left-w2;
                y2 = pos2.top;

                ctx.beginPath();
                ctx.moveTo(x1, y1);
                ctx.lineTo(x2, y2);
                // ctx.quadraticCurveTo(x1, y1 , x2, y2);
                ctx.lineWidth = 1;

                ctx.strokeStyle = join_colors[join_colors_index];
                join_colors_index++;
                if(join_colors_index == join_colors.length)
                    join_colors_index = 0;


                ctx.stroke();

                joined_tables[joined_tables.length] = table1_guid+"."+table2_guid;
                joined_tables[joined_tables.length] = table2_guid+"."+table1_guid;
            }
        }
    }

    // draw canvas as bkg
    png = $('#canvas_bkg')[0].toDataURL();
    $('#canvas').css('background', "url("+png+") no-repeat");
    $('#canvas').scroll();


}
