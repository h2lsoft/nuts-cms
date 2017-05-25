<?php
/**
 * Plugin news_calendar - Front office
 *
 * @version 1.0
 * @date 11/12/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Page */
/* @var $nuts Page */
include(NUTS_PLUGINS_PATH.'/_news/config.inc.php');
include(NUTS_PLUGINS_PATH.'/_news-agenda-widget/config.inc.php');

$plugin->openPluginTemplate();

// current url
$nuts->parse('nuts_calendar_widget_page_url', $calendar_widget_uri);

// parse days label
$days = array();
$days['en'] = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
$days['fr'] = array('Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim');
$cur_days_label = ($page->language == 'fr') ? $days['fr'] : $days['en'];
for($i=0; $i < count($cur_days_label); $i++)
{
    $nuts->parse('day'.($i+1), $cur_days_label[$i]);
}
// parse month label
$cur_month = (!@empty($_POST['widget_calendar_cur_month'])) ? $_POST['widget_calendar_cur_month'] : date('m');
$months_label = array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
if($page->language == 'fr')
    $months_label = array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

$cur_year = (!@empty($_POST['widget_calendar_cur_year'])) ? $_POST['widget_calendar_cur_year'] : date('Y');
$cur_filter = (!@empty($_POST['widget_calendar_cur_filter'])) ? $_POST['widget_calendar_cur_filter'] : '';

if(!@empty($_POST['widget_calendar_cur_month']) && !@empty($_POST['widget_calendar_cur_year']))
{
    $cur_mkt =  mktime(0, 0, 0, date($cur_month), 1, date($cur_year));
    if(@$_POST['widget_calendar_event'] == 'prev')
    {
        $cur_month = date('m', strtotime('-1 month', $cur_mkt));
        $cur_year = date('Y', strtotime('-1 month', $cur_mkt));
    }
    elseif(@$_POST['widget_calendar_event'] == 'next')
    {
        $cur_month = date('m', strtotime('+1 month', $cur_mkt));
        $cur_year = date('Y', strtotime('+1 month', $cur_mkt));
    }
}

$cur_month = (int)$cur_month;
$cur_month_label = ($cur_month < 10) ? "0".$cur_month : $cur_month;

// select * news from current month and year
$calendar_widget_sql_news = str_replace('[cur_month]', $cur_month_label, $calendar_widget_sql_news);
$calendar_widget_sql_news = str_replace('[cur_year]', $cur_year, $calendar_widget_sql_news);

$sql_added = '';
if(!empty($cur_filter))
    $sql_added = " AND `$calendar_widget_sql_news_filter_column` = '".sqlX($cur_filter)."'";
$calendar_widget_sql_news = str_replace('[sql_added]', $sql_added, $calendar_widget_sql_news);

$nuts->doQuery($calendar_widget_sql_news);

$dates = array();
$all_dates = array();
while($row = $nuts->dbFetch())
{
    $all_dates[] = $row['Date'];
    $dates[$row['Date']][] = $row;
}
$plugin->parse('Month', $months_label[(int)$cur_month]);
$plugin->parse('Year', $cur_year);
$plugin->parse('cur_month', $cur_month);
$plugin->parse('cur_year', $cur_year);


$tds_generated = '';
$init = false;
$first_day = date('w', mktime(0, 0, 0, date($cur_month), 1, date($cur_year))); // de 0 (sunday) à 6 (saturday)
if($first_day == 0)$first_day = 7;
$month_max_days = date('t', mktime(0, 0, 0, date($cur_month), 1, date($cur_year)));

$cur_month_prefixed = ($cur_month < 10) ? '0'.$cur_month : $cur_month;
$cur_day = 1;
$cur_day_loop = 0;
$init = false;
for($i=1; $i <= 6; $i++)
{
    $tds_generated .= '<tr>';
    for($j=1; $j <= 7; $j++)
    {
        $day_label = "&nbsp;";
        if(!$init)
        {
            if($j >= $first_day)
            {
                $day_label = $cur_day;
                $day_label_prefixed = ($cur_day < 10) ? '0'.$cur_day : $cur_day;
                $cur_date = "$cur_year-$cur_month_prefixed-$day_label_prefixed";
                if(in_array($cur_date, $all_dates))
                    $day_label = '<a data-date="'.$cur_date.'" href="javascript:nutsCalendarWidgetView(\''.$cur_date.'\');">'.$cur_day.'</a>';

                $cur_day++;
            }
        }
        else
        {
            if($cur_day <= $month_max_days)
            {
                $day_label = $cur_day;
                $day_label_prefixed = ($cur_day < 10) ? '0'.$cur_day : $cur_day;
                $cur_date = "$cur_year-$cur_month_prefixed-$day_label_prefixed";
                if(in_array($cur_date, $all_dates))
                    $day_label = '<a data-date="'.$cur_date.'" href="javascript:;" onclick="nutsCalendarWidgetView(\''.$cur_date.'\');">'.$cur_day.'</a>';
            }

            $cur_day++;
        }

        $tds_generated .= '<td>'.$day_label.'</td>'.CR;
    }
    $tds_generated .= '</tr>';

    $init = true;
}

$plugin->parse('tds_generated', $tds_generated);


// filters
if(!$calendar_widget_has_filters)
{
    $plugin->eraseBloc('widget_filters');
}
else
{
    $nuts->doQuery($calendar_widget_filters_sql);
    $filters = $nuts->dbGetData();
    if(count($filters) == 0)
    {
        $plugin->eraseBloc('widget_filters');
    }
    else
    {
        foreach($filters as $filter)
        {
            $label = ucfirst(strtolower($filter['Type']));
            $plugin->parse('filters.label', $label);
            $plugin->parse('filters.value', $filter['Type']);

            $selected = ($cur_filter == $filter['Type']) ? 'selected' : '';
            $plugin->parse('filters.selected', $selected);
            $plugin->loop('filters');
        }
    }
}



// parsing tooltips
if(count($dates) == 0)
{
    $plugin->eraseBloc('tooltip');
}
else
{
    foreach($all_dates as $key)
    {
        $plugin->parse('tooltip.Date', $key);

        foreach($dates[$key] as $n)
        {
            $plugin->parse('tooltip.tooltip_title.Type', $n['Type']);
            $plugin->parse('tooltip.tooltip_title.Title', $n['Title']);
            $plugin->parse('tooltip.tooltip_title.VirtualPageName', $n['VirtualPageName']);
            $plugin->loop('tooltip.tooltip_title');
        }

        $plugin->loop('tooltip');
    }
}

if($include_plugin_css)$plugin->addHeaderFile('css', '/'.$plugin->plugin_front_path.'/style.css');
if($include_plugin_js)$plugin->addHeaderFile('js', '/'.$plugin->plugin_front_path.'/func.js');

if($_POST && @$_GET['ajaxer'] == 1 && @$_GET['action'] == 'news-calendar-widget' && !@empty($_POST['widget_calendar_cur_month']) && !@empty($_POST['widget_calendar_cur_year']))
{
    $out = $plugin->getAjaxBloc('nuts_calendar_widget');
    die($out);
}

$plugin->setNutsContent();

