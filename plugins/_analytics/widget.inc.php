<?php
/**
 * Plugin analytics - widget
 *
 * @version 1.0
 * @date 21/04/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(Plugin::getIncludeUserLanguagePath('_analytics'));
include(NUTS_PLUGINS_PATH.'/_analytics/config.inc.php');

if(empty($google_analytics_ooID) || empty($google_analytics_profil_id))
{
    Plugin::dashboardAddNotification('info', $lang_msg[1]);
}
else
{

    $date_start = strtotime('-31 days');
    $date_end = strtotime('yesterday');

    $nuts->open(NUTS_PLUGINS_PATH.'/_analytics/widget.html');
    $nuts->parse('google_analytics_ooID', $google_analytics_ooID);
    $nuts->parse('google_analytics_profil_id', $google_analytics_profil_id);
    $nuts->parse('date_start', date('m/d/Y', $date_start));
    $nuts->parse('date_end', date('m/d/Y', $date_end));

    $nuts->parse('visits', $lang_msg[4]);
    $nuts->parse('visits_uniq', $lang_msg[5]);
    $nuts->parse('page_views', $lang_msg[6]);

    $ga_url = Query::factory()->select('ExternalUrl')->from('NutsMenu')->whereEqualTo('Name', '_analytics')->executeAndGetOne();
    $nuts->parse('ga_url', $ga_url);

    $date_format = ($_SESSION['Language'] == 'fr') ? "d/m/Y" : "Y-m-d";
    $msg = $lang_msg[3]." (".date($date_format, $date_start).' - '.date($date_format, $date_end).")";
    $nuts->parse('title', $msg);

    $contents = $nuts->output();

    Plugin::dashboardAddWidget($lang_msg[0], 'final', 'analytics', 'full', '', $contents);
}





?>


