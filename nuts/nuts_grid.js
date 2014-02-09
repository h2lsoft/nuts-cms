function nutsDatagrid(id)
{
    this.id = id;
    this.width = 'auto';
    this.deleteMessage = (nutsUserLang  == 'fr') ? "Voulez-vous supprimer cette ligne ?" : "Would you like to delete this record ?";
    this.dragNdrop = true;
    this.style = '';
    this.caption = '';

    this.columns = [];
    this.values = [];
    this.footer = "";

    this.callback = "";
}

function nutsDatagridUpdate(datagrid_id)
{
    vals = '';
    cur_line = 0;
    $('#datagrid_'+datagrid_id+' tbody tr.row').each(function(){


        tmp_arr = '';
        $(this).find('input, select, textarea').each(function(){

            obj_id = $(this).attr('id');
            obj_idX = str_replace('datagrid_'+datagrid_id+'_obj_', '', obj_id);
            if(!empty(obj_idX))
            {
                tmp = explode('_', obj_idX);
                obj_idX = tmp[0];

                if($('#'+obj_id).attr('type') ==  'checkbox')
                {
                    val = ($('#'+obj_id).is(':checked')) ? 1 : 0;
                }
                else
                {
                    val = $('#'+obj_id).val();
                    val = str_replace('\n', '\\\\n', val);
                    val = str_replace('\t', '\\\\t', val);
                    val = str_replace('"', '\\"', val);
                }

                // tmp_arr[obj_idX] = val;
                if(!empty(tmp_arr))tmp_arr += ', ';
                tmp_arr += '"'+obj_idX+'": "'+val+'"';
            }
        });

        if(!empty(vals))vals += ',\n';
        vals += "\t\{"+tmp_arr+"}";

        cur_line++;

    });

    vals = "[\n"+vals+"\n]";
    $('#'+datagrid_id).val(vals);


    callback = $('#datagrid_'+datagrid_id).attr('callback');
    if(!empty(callback))
    {
        eval(callback+'()');
    }

}



nutsDatagrid.prototype.setFooter = function(footer) {
    this.footer = footer;
}

nutsDatagrid.prototype.setCaption = function(caption) {
    this.caption = caption;
}

nutsDatagrid.prototype.setId = function(Id) {
    this.Id = Id;
}

nutsDatagrid.prototype.setWidth = function(width) {
    this.width = width;
}

nutsDatagrid.prototype.setStyle = function(style) {
    this.style = style;
}

nutsDatagrid.prototype.setDeleteMessage = function(msg) {
    this.msg = msg;
}

nutsDatagrid.prototype.setDragAndDrop = function(dragNdrop) {
    this.dragNdrop = dragNdrop;
}

nutsDatagrid.prototype.addColumn = function(classname, label, input_type, col_style,  input_style, input_placeholder, input_options) {
    this.columns[this.columns.length] = {label: label, input_type: input_type, input_style: input_style, input_placeholder:input_placeholder, input_options:input_options, class: classname, style: col_style};
}

nutsDatagrid.prototype.setValues = function(values) {
    this.values = values;
}

nutsDatagrid.prototype.render = function(){

    str = '';
    str += '<table callback="'+this.callback+'" class="datagrid" id="datagrid_'+this.id+'" style="width:'+this.width+'; '+this.style+'">\n';

    if(this.caption != '')
    {
        str += '<caption>'+this.caption+'</caption>\n';
    }

    // columns
    str += '<thead>\n';
    str += '<tr>\n';

    // drag and drop
    str += '\t<th style="width:18px;">&nbsp;</th>';


    for(i=0; i <  this.columns.length; i++)
    {

        str += '\t<th';
        str += ' class="'+this.columns[i].class+'" ';
        str += ' style="'+this.columns[i].style+'" ';
        str += '>\n';

        str += this.columns[i].label;

        str += '</th>\n';
    }

    str += '\t<th style="width:18px;"><a href="javascript:;" class="datagrid_btn_add"></a></th>\n';


    str += '\t</tr>\n';
    str += '</thead>\n';


    str += '<tbody>\n';


    // first init
    str += '<tr>\n';
    if(this.dragNdrop)
        str += '\t<td style="width:18px;"><a href="javascript:;" class="datagrid_btn_drag"></a></td>';
    else
        str += '\t<td style="width:18px;">1.</td>';

    for(i=0; i <  this.columns.length; i++)
    {
        str += '\t<td';
        str += ' class="'+this.columns[i].class+'" ';
        str += ' style="'+this.columns[i].style+'" ';
        str += '>\n';

        // type : text, textaera, date, number, select ?
        if(this.columns[i].input_type == 'text' || this.columns[i].input_type == 'date' || this.columns[i].input_type == 'number' || this.columns[i].input_type == 'money'  || this.columns[i].input_type == 'checkbox')
        {
            o_class = this.columns[i].class;
            if(this.columns[i].input_type == 'date') o_class = this.columns[i].class+' date';
            if(this.columns[i].input_type == 'number') o_class = this.columns[i].class+' number';
            if(this.columns[i].input_type == 'money') o_class = this.columns[i].class+' money';

            input_type = 'text';
            if(this.columns[i].input_type == 'checkbox')input_type = 'checkbox';

            str += '\t\t<input type="'+input_type+'" ';
            str += ' id="datagrid_'+this.id+'_obj_'+this.columns[i].class+'_[UID]" ';
            str += ' class="'+o_class+'" ';
            str += ' style="'+this.columns[i].input_style+'" ';

            if(this.columns[i].input_type == 'checkbox')
                str += ' value="'+this.columns[i].input_placeholder+'" ';
            else
                str += ' placeholder="'+this.columns[i].input_placeholder+'" ';
            str += '>\n';
        }
        else if(this.columns[i].input_type == 'select')
        {
            str += '\t\t<select ';
            str += ' type="select" ';
            str += ' id="datagrid_'+this.id+'_obj_'+this.columns[i].class+'_[UID]" ';
            str += ' class="'+o_class+'" ';
            str += ' style="'+this.columns[i].input_style+'" ';
            str += '>\n';

            str += '<option value=""></option>\n';

            for(j=0; j <  this.columns[i].input_options.length; j++)
            {
                if(is_array(this.columns[i].input_options[j]))
                {
                    val = this.columns[i].input_options[j][0];
                    label = this.columns[i].input_options[j][1];
                }
                else
                {
                    val = this.columns[i].input_options[j];
                    label = this.columns[i].input_options[j];
                }

                str += '<option value="'+val+'">'+label+'</option>\n';

            }

        }
        else if(this.columns[i].input_type == 'textarea')
        {
            str += '\t\t<textarea ';
            str += ' type="textarea" ';
            str += ' id="datagrid_'+this.id+'_obj_'+this.columns[i].class+'_[UID]" ';
            str += ' class="'+o_class+'" ';
            str += ' style="'+this.columns[i].input_style+'" ';
            str += '></textarea>\n';
        }

        str += '</td>\n';
    }

    str += '\t<td style="width:18px;"><a href="javascript:;" class="datagrid_btn_delete"></a></td>\n';

    str += '</tr>\n';

    str += '</tbody>\n';


    // footer
    if(this.footer != '')
    {
        str += '<tfoot>\n';
        str += '<tr>\n';

        cols = this.columns.length;
        cols += 1; // drag and drop
        cols += 1; // delete button

        str += '<td colspan="'+cols+'">'+this.footer+'</td>\n';
        str += '</tr>\n';
        str += '</tfoot>\n';
    }
    str += '</table>\n';

    // input receiver
    str += '<input type="hidden" name="'+this.id+'" id="'+this.id+'" value="">\n';
    return str;
}

nutsDatagrid.prototype.renderInFieldset = function(fieldset_id)
{
    legend = $('#'+fieldset_id+' legend').html();
    legend = $.trim(legend);

    html = '';
    if(!empty(legend))
        html += '<legend>'+legend+'</legend>';

    html += this.render();

    $('#'+fieldset_id).html(html);

    datagrid_id = this.id;

    // drag and drop
    if(this.dragNdrop)
    {
        $('#datagrid_'+this.id).sortable({

            axis:'y',
            opacity: 0.8,
            cursor: 'move',

            helper: function(e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function(index)
                {
                    // Set helper cell sizes to match the original sizes
                    $(this).width($originals.eq(index).width())
                });

                return $helper;
            },


            items: 'tr.row',

            update: function(event, ui) {
                nutsDatagridUpdate(datagrid_id);
            }
        })
    }


    // attach event add && delete
    delete_message = this.deleteMessage;
    $('#datagrid_'+datagrid_id+' .datagrid_btn_add').click(function(){

        l = $('#datagrid_'+datagrid_id+' tbody tr:eq(0)').html();
        l = str_replace('[UID]', time(), l);
        $('#datagrid_'+datagrid_id+' tbody tr:last').after('<tr class="row">'+l+'</tr>');

        // init calendar
        $('#datagrid_'+datagrid_id+' tbody tr:last input.date').each(function(){
            obj_id = $(this).attr('id');
            inputDate(obj_id, 'date');
        });
        nutsDatagridUpdate(datagrid_id);

        // atach on change on input
        $('#datagrid_'+datagrid_id+' tr.row input, #datagrid_'+datagrid_id+' tr.row select, #datagrid_'+datagrid_id+' tr.row textarea').bind('click keyup blur', function(){
            nutsDatagridUpdate(datagrid_id);
        });

        // attach delete event
        $('#datagrid_'+datagrid_id+' tbody tr:last .datagrid_btn_delete').click(function(event){
            // lines = $('#datagrid_'+datagrid_id+' tbody tr').length;
            if(confirm(delete_message))
            {
                $(this).parents('tr').remove();
                nutsDatagridUpdate(datagrid_id);
            }
        });
    });

}






