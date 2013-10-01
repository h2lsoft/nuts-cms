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

if(empty($google_analytics_account_email) || empty($google_analytics_account_password) || empty($google_analytics_profil_id))
{
    Plugin::dashboardAddNotification('info', $lang_msg[1]);
}
else
{
	// curl extension found ?
	if(!function_exists('curl_version'))
	{
		Plugin::dashboardAddNotification('error', "Curl extension not installed");
	}
	else
	{
		if(!($contents = nutsGetCache('widget-ga')))
		{

			$date_start_mkt = strtotime('-31 days');
			$date_start = date('Y-m-d', $date_start_mkt);

			$date_end_mkt = strtotime('yesterday');
			$date_end = date('Y-m-d', $date_end_mkt);

			// ga dash
			include(NUTS_LIBRARY_PATH.'/php/gapi/gapi.class.php');
			$ga = new gapi($google_analytics_account_email, $google_analytics_account_password);
			$ga->requestReportData($google_analytics_profil_id, array('date'), array('visitors', 'visits', 'pageviews'), '', '', $date_start, $date_end);

			$reporting = array();
			$total_pageviews = $total_visitors = $total_visits = 0;
			foreach($ga->getResults() as $result)
			{
				$date = $result->getDimesions();
				$date = substr($date['date'], 0, 4).'-'.substr($date['date'], 4, 2).'-'.substr($date['date'], 6, 2);
				$reporting[$date] = $result->getMetrics();

				$total_pageviews += $reporting[$date]['pageviews'];
				$total_visitors += $reporting[$date]['visitors'];
				$total_visits += $reporting[$date]['visits'];
			}

			@ksort($reporting);
			$tmp = array();
			$tmp['data'] = $reporting;
			$tmp['total_pageviews'] = $total_pageviews;
			$tmp['total_visitors'] = $total_visitors;
			$tmp['total_visits'] = $total_visits;
			$reporting = $tmp;


			// parsing
			$nuts->open(NUTS_PLUGINS_PATH.'/_analytics/widget.html');
			$nuts->parse('visits', $lang_msg[4]);
			$nuts->parse('total_visits', $reporting['total_visits'], '|int_formatX');

			$nuts->parse('visits_uniq', $lang_msg[5]);
			$nuts->parse('total_visits_uniq', $reporting['total_visitors'], '|int_formatX');

			$nuts->parse('page_views', $lang_msg[6]);
			$nuts->parse('total_page_views', $reporting['total_pageviews'], '|int_formatX');

			$ga_url = Query::factory()->select('ExternalUrl')->from('NutsMenu')->whereEqualTo('Name', '_analytics')->executeAndGetOne();
			$nuts->parse('ga_url', $ga_url);

			$date_format = ($_SESSION['Language'] == 'fr') ? "d/m/Y" : "Y-m-d";
			$msg = $lang_msg[3]." (".date($date_format, $date_start_mkt).' - '.date($date_format, $date_end_mkt).")";
			$nuts->parse('title', $msg);

			// parsing json array
			$init = false;
			foreach($reporting['data'] as $day => $vals)
			{
				list($YYYY, $MM, $DD) = explode('-', $day);
				$nuts->parse('datas.c_YYYY', $YYYY);
				$nuts->parse('datas.c_MM', $MM-1);
				$nuts->parse('datas.c_DD', $DD);

				$nuts->parse('datas.c_visits', $vals['visits']);
				$nuts->parse('datas.c_visitors', $vals['visitors']);

				$comma = ($init) ? ',': '';
				$nuts->parse('datas.c_comma', $comma);
				$nuts->loop('datas');
				$init = true;
			}

			$contents = $nuts->output();

			nutsSetCache('widget-ga', $contents, date('Y-m-d 00:00:00', strtotime('tomorrow')));
		}

		Plugin::dashboardAddWidget($lang_msg[0], 'final', 'analytics', 'full', '', $contents);

	}
}

?>
