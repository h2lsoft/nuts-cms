var join_last_selected_fields = "";
var join_tables = [];

function queryFormatCode(sql)
{
    // in ajax
    $('#SqlCodeHighlighter').html(lang_msg_11);

    uri = '?mod=_visual-query-builder&do=exec&ajaxer=1&action=format_sql';
    $.post(uri, {sql:sql}, function(resp){

        // ajax_loader
        pos = $('#canvas').position();
        $('#ajax_loader').css('top', pos.top+10+'px');
        $('#ajax_loader').css('left', pos.left+10+'px');
        $('#ajax_loader').show();

        $('#SqlCode').val(resp);
        updateSqlCode();

        query_original = resp;

        // retro factoring
        from_init = true;
        arr = query2Array();
        funcs = new Array();
        str_from = trim(arr['FROM']);
        if(!empty(str_from))
        {
            lines = explode('\n', str_from);
            for(i=0; i < lines.length; i++)
            {
                line = trim(lines[i]);
                line = str_replace(',', '', line);
                line = str_replace('`', '', line);

                tmp = explode(' AS ', line);
                if(count(tmp) == 2)
                {
                    table_name = trim(tmp[0]);
                    alias_name = trim(tmp[1]);
                }
                else
                {
                    table_name = line;
                    alias_name = "";
                }

                tableAdd(table_name, alias_name);
            }


            setTimeout(function(){
                reorderTable();
                tableRepaint();
                from_init = false;

                $('#ajax_loader').hide();


            }, 2500);


        }




    });



}


function query2Array()
{
    code = $('#SqlCode').val();
    lines = explode("\n", code);

    arr = {
        'SELECT' : '',
        'FROM' : '',
        'WHERE' : '',
        'GROUP BY' : '',
        'ORDER BY' : '',
        'LIMIT' : ''
    };


    init = false;
    cur_key = 'SELECT';
    last_key = cur_key;
    for(i=0; i < lines.length; i++)
    {
        if(lines[i] == 'FROM')cur_key = 'FROM';
        if(lines[i] == 'WHERE')cur_key = 'WHERE';
        if(lines[i] == 'GROUP BY')cur_key = 'GROUP BY';
        if(lines[i] == 'ORDER BY')cur_key = 'ORDER BY';
        if(lines[i] == 'LIMIT')cur_key = 'LIMIT';

        if(cur_key != last_key)
        {
            last_key = cur_key;
        }
        else
        {
            if(init)
                arr[cur_key] += "\n"+lines[i];
        }

        init = true;
    }


    return arr;
}

function queryArray2String(arr)
{

    str = "";

    // SELECT
    //if(!empty(arr['SELECT']))
    //{
        tmp = trim(arr['SELECT']);
        str += "SELECT\n\t"+tmp;
    //}

    // FROM
    if(empty(arr['FROM']))
    {
        str2 = "";

        $('#canvas .table').each(function(){

            table_name_ID = $(this).attr('id');
            table_name = $('#'+table_name_ID+' .table_title').text();
            table_name = trim(table_name);
            alias_name = $('#'+table_name_ID+' .table_alias_input').val();
            alias_name = trim(alias_name);

            pattern = table_name;
            if(!empty(alias_name))
                pattern = table_name+' AS '+alias_name;

            if(!empty(str2))str2 += ",";
            str2 += "\n\t"+pattern;

        });

        arr['FROM'] = trim(str2);
    }

    tmp = trim(arr['FROM']);
    str += "\nFROM\n\t"+tmp;


    // WHERE
    if(!empty(arr['WHERE']))
    {
        tmp = trim(arr['WHERE']);
        str += "\nWHERE\n\t"+tmp;
    }

    // GROUP BY
    if(!empty(arr['GROUP BY']))
    {
        tmp = trim(arr['GROUP BY']);
        str += "\nGROUP BY\n\t"+tmp;
    }

    // ORDER BY
    if(!empty(arr['ORDER BY']))
    {
        tmp = trim(arr['ORDER BY']);
        str += "\nORDER BY\n\t"+tmp;
    }

    return str;

}



function queryGenerate()
{
    sql = "";

    if($('#canvas .table').length == 0)
    {

    }
    else if($('#canvas .table').length == 1)
    {
        sql = "SELECT\n\t\nFROM\n\t";

        table_name = $('#canvas .table .table_title').text();
        alias = $('#canvas .table .table_alias_input').val();
        sql += trim(table_name);
        if(!empty(alias))sql += ' AS '+trim(alias);


    }
    else if($('#canvas .table').length > 1)
    {
        sql = "SELECT\n\t\nFROM\n";

        i = 0;
        $("#canvas .table").each(function(eq){

            table_name = $('#canvas .table:eq('+eq+') .table_title').text();
            alias = $('#canvas .table:eq('+eq+') .table_alias_input').val();
            sql += "\t"+trim(table_name);
            if(!empty(alias))sql += ' AS '+alias;
            i++;

            if(i < $("#canvas .table").length)sql += ",";
            sql += "\n";

        });
    }

    $('#SqlCode').val(sql);
    updateSqlCode();
}




function updateSqlCode()
{
    c = $('#SqlCode').val();

    // replace keywords
    bloc_start = '<span class="keyword">';
    bloc_end = '</span>';

    c = str_replace('COUNT(', bloc_start+'COUNT('+bloc_end, c);
    c = str_replace('SELECT\n', bloc_start+'SELECT'+bloc_end+'\n', c);
    c = str_replace('\nFROM\n', '\n'+bloc_start+'FROM'+bloc_end+'\n', c);
    c = str_replace('\nWHERE\n', '\n'+bloc_start+'WHERE'+bloc_end+'\n', c);
    c = str_replace('\nGROUP BY\n', '\n'+bloc_start+'GROUP BY'+bloc_end+'\n', c);
    c = str_replace(' HAVING ', ' '+bloc_start+'HAVING'+bloc_end+' ', c);
    c = str_replace('\nORDER BY\n', '\n'+bloc_start+'ORDER BY'+bloc_end+'\n', c);
    c = str_replace('DATE_FORMAT(', bloc_start+'DATE_FORMAT'+bloc_end+'(', c);
    c = str_replace('MIN(', bloc_start+'MIN'+bloc_end+'(', c);
    c = str_replace('MAX(', bloc_start+'MAX'+bloc_end+'(', c);
    c = str_replace('SUM(', bloc_start+'SUM'+bloc_end+'(', c);
    c = str_replace('AVG(', bloc_start+'AVG'+bloc_end+'(', c);
    c = str_replace('NOW(', bloc_start+'NOW'+bloc_end+'(', c);
    c = str_replace('TO_DAYS(', bloc_start+'TO_DAYS'+bloc_end+'(', c);
    c = str_replace('DATE_ADD(', bloc_start+'DATE_ADD'+bloc_end+'(', c);
    c = str_replace('DATE_SUB(', bloc_start+'DATE_SUB'+bloc_end+'(', c);
    c = str_replace('CURDATE(', bloc_start+'CURDATE'+bloc_end+'(', c);
    c = str_replace('CURTIME(', bloc_start+'CURTIME'+bloc_end+'(', c);
    c = str_replace('CONCAT(', bloc_start+'CONCAT'+bloc_end+'(', c);
    c = str_replace('UPPER(', bloc_start+'UPPER'+bloc_end+'(', c);
    c = str_replace('LOWER(', bloc_start+'LOWER'+bloc_end+'(', c);

    c = str_replace('YEAR(', bloc_start+'YEAR'+bloc_end+'(', c);
    c = str_replace('MONTH(', bloc_start+'MONTH'+bloc_end+'(', c);
    c = str_replace('MINUTE(', bloc_start+'MINUTE'+bloc_end+'(', c);
    c = str_replace('REPLACE(', bloc_start+'REPLACE'+bloc_end+'(', c);
    c = str_replace('IF(', bloc_start+'IF'+bloc_end+'(', c);

    c = str_replace(' IN(', ' '+bloc_start+'IN'+bloc_end+'(', c);

    c = str_replace(' BETWEEN ', ' '+bloc_start+'BETWEEN'+bloc_end+' ', c);
    c = str_replace(' INTERVAL ', ' '+bloc_start+'INTERVAL'+bloc_end+' ', c);
    c = str_replace(' SECOND', ' '+bloc_start+'SECOND'+bloc_end, c);
    c = str_replace(' MINUTE', ' '+bloc_start+'MINUTE'+bloc_end, c);
    c = str_replace(' HOUR', ' '+bloc_start+'HOUR'+bloc_end, c);
    c = str_replace(' DAY', ' '+bloc_start+'DAY'+bloc_end, c);
    c = str_replace(' WEEK', ' '+bloc_start+'WEEK'+bloc_end, c);
    c = str_replace(' MONTH', ' '+bloc_start+'MONTH'+bloc_end, c);
    c = str_replace(' YEAR', ' '+bloc_start+'YEAR'+bloc_end, c);

    // imbricated query
    c = str_replace('(SELECT ', '('+bloc_start+'SELECT'+bloc_end+' ', c);
    c = str_replace(' FROM ', ' '+bloc_start+'FROM'+bloc_end+' ', c);
    c = str_replace(' WHERE ', ' '+bloc_start+'WHERE'+bloc_end+' ', c);
    c = str_replace(' AND ', ' '+bloc_start+'AND'+bloc_end+' ', c);
    c = str_replace(' OR ', ' '+bloc_start+'OR'+bloc_end+' ', c);
    c = str_replace(' LIMIT ', ' '+bloc_start+'LIMIT '+bloc_end+' ', c);

    c = str_replace(' ASC', ' '+bloc_start+'ASC'+bloc_end, c);
    c = str_replace(' DESC', ' '+bloc_start+'DESC'+bloc_end, c);
    c = str_replace(' LIKE ', ' '+bloc_start+'LIKE'+bloc_end+' ', c);
    c = str_replace(' BETWEEN ', ' '+bloc_start+'BETWEEN'+bloc_end+' ', c);
    c = str_replace(' AND\n', ' '+bloc_start+'AND'+bloc_end+'\n', c);
    c = str_replace(' AS ', ' '+bloc_start+'AS'+bloc_end+' ', c);
    c = str_replace(' NOT ', ' '+bloc_start+'NOT'+bloc_end+' ', c);
    c = str_replace(' IFNULL(', ' '+bloc_start+'IFNULL('+bloc_end, c);
    c = str_replace(' OR ', ' '+bloc_start+'OR'+bloc_end+' ', c);
    c = str_replace(' NULL ', ' '+bloc_start+'NULL'+bloc_end+' ', c);

    c = str_replace(',\n', bloc_start+','+bloc_end+'\n', c);
    c = str_replace('.', bloc_start+'.'+bloc_end, c);
    c = str_replace('(', bloc_start+'('+bloc_end, c);
    c = str_replace(')', bloc_start+')'+bloc_end, c);
    c = str_replace('`', bloc_start+'`'+bloc_end, c);


    // replace quotes by lines
    bloc_start = '<span class="quotes">';
    bloc_end = '</span>';
    text_in_quotes = [];
    cur_pos = 0;
    pos_start = 0;
    quote_opened = false;
    while((cur_pos = c.indexOf("'", cur_pos)) != -1)
    {
        if(!quote_opened)
        {
            pos_start = cur_pos;
            quote_opened = true;
            // console.log('quote_opened => pos start => '+pos_start);
        }
        else
        {
            // console.log('quote_closed => cur_pos => '+cur_pos);
            limit = cur_pos - pos_start + 1;
            if(c.substr(cur_pos-1, 1) != '\\')
            {
                str2 = c.substr(pos_start, limit);
                if(!empty(str2) && !in_array(str2, text_in_quotes))
                {
                    // console.log('str2 => '+str2);
                    text_in_quotes[text_in_quotes.length] = str2;
                }

                quote_opened = false;
                ++cur_pos;
            }
        }

        ++cur_pos;
    }

    // user type error
    if(quote_opened)
    {
        str2 = c.substr(pos_start);
        text_in_quotes[text_in_quotes.length] = str2;
    }

    text_in_quotes.sort(function(a,b) {return (a.length < b.length) ? 1 : 0; });
    for(i=0; i < text_in_quotes.length; i++)
    {
        rep = text_in_quotes[i];
        rep = htmlentities(rep);
        c = str_replace(text_in_quotes[i], bloc_start+rep+bloc_end, c);
    }

    // comments --
    bloc_start = '<span class="comments">';
    bloc_end = '</span>';
    lines = explode('\n', c);
    for(i=0; i < lines.length; i++)
    {
        line = ltrim(lines[i]);
        if(line.indexOf('-- ', line) != -1)
        {
            tmp = str_replace('-- ', bloc_start+'-- ', lines[i]);
            lines[i] = tmp+bloc_end;
        }
    }

    c = join('\n', lines);
    c = str_replace('&quot;', "'", c);




    $('#SqlCodeHighlighter').height($('#SqlCode').height());
    $('#SqlCodeHighlighter').html(c);
    $('#SqlCode').scroll();
}


function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds){
            break;
        }
    }
}



