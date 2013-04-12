function nutsCalendarWidgetView(date)
{
    $('#nuts_calendar_widget .nuts_calendar_widget_tooltip').hide();

    pos = $('#nuts_calendar_widget a[data-date='+date+']').position();
    $('#nuts_calendar_widget #nuts_calendar_widget_tooltip_'+date).css('top', pos.top+15);
    $('#nuts_calendar_widget #nuts_calendar_widget_tooltip_'+date).css('left', pos.left-260);
    $('#nuts_calendar_widget #nuts_calendar_widget_tooltip_'+date).show();
}

function nutsCalendarWidgetEvent(event, cur_month, cur_year)
{
    if(event == 'close')
    {
        $('#nuts_calendar_widget .nuts_calendar_widget_tooltip').hide();
        return;
    }

    widget_calendar_cur_filter =  $('#nuts_calendar_widget_filters').val();

    // next, previous
    $('#nuts_calendar_widget').fadeTo(1, 0, 0);

    uri = nuts_calendar_widget_page_url;
    if(uri.indexOf('?') == -1)uri += '?';uri += '&ajaxer=1&action=news-calendar-widget';
    $.post(uri, {widget_calendar_cur_month:cur_month, widget_calendar_cur_year:cur_year, widget_calendar_event:event, widget_calendar_cur_filter:widget_calendar_cur_filter}, function(data){
        $('#nuts_calendar_widget').html(data);
        $('#nuts_calendar_widget').fadeTo(0, 1, 0);
    });



}