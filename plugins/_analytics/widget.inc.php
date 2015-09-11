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

			try
			{
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


				// ga TOP 10
				$reporting_top10 = array();

				// referers
				$reporting_top10['REFERERS'] = array();
				$ga->requestReportData($google_analytics_profil_id, array('fullReferrer'), array('pageviews', 'visits'), '-visits', '', '', '', 1, 10);
				foreach($ga->getResults() as $result)
				{
					$name = (string)$result;
					$v = (int)$result->getVisits();

					$reporting_top10['REFERERS'][] = array($name, $v);
				}

				// keywords
				$reporting_top10['KEYWORDS'] = array();
				$ga->requestReportData($google_analytics_profil_id, array('keyword'), array('pageviews', 'visits'), '-visits', '', '', '', 1, 10);
				foreach($ga->getResults() as $result)
				{
					$name = (string)$result;
					$v = (int)$result->getVisits();
					$reporting_top10['KEYWORDS'][] = array($name, $v);
				}

				// country
				$reporting_top10['COUNTRIES'] = array();
				$ga->requestReportData($google_analytics_profil_id, array('country'), array('pageviews', 'visits'), '-visits', '', '', '', 1, 10);
				foreach($ga->getResults() as $result)
				{
					$name = (string)$result;
					$v = (int)$result->getVisits();
					$reporting_top10['COUNTRIES'][] = array($name, $v);
				}

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

				// top 10 referers
				$nuts->parse('top_referers_label', $lang_msg[7]);
				$tmp = $reporting_top10['REFERERS'];
				if(!count($tmp))
				{
					$nuts->eraseBloc('data_ref');
				}
				else
				{
					$init = false;
					foreach($tmp as $col)
					{
						$comma = (!$init) ? '' : ',';

						$nuts->parse('data_ref.ref_col1', $col[0]);
						$nuts->parse('data_ref.ref_col2', $col[1]);
						$nuts->parse('data_ref.ref_comma', $comma);
						$nuts->loop('data_ref');

						$init = true;
					}
				}

				// top 10 searches
				$nuts->parse('top_searchs_label', $lang_msg[8]);
				$tmp = $reporting_top10['KEYWORDS'];
				if(!count($tmp))
				{
					$nuts->eraseBloc('data_se');
				}
				else
				{
					$init = false;
					foreach($tmp as $col)
					{
						$comma = (!$init) ? '' : ',';
						$nuts->parse('data_se.se_col1', $col[0]);
						$nuts->parse('data_se.se_col2', $col[1]);
						$nuts->parse('data_se.se_comma', $comma);
						$nuts->loop('data_se');
						$init = true;
					}
				}

				// top 10 countries
				$nuts->parse('top_countries_label', $lang_msg[9]);
				$tmp = $reporting_top10['COUNTRIES'];
				if(!count($tmp))
				{
					$nuts->eraseBloc('data_c');
				}
				else
				{
					$init = false;
					foreach($tmp as $col)
					{
						$comma = (!$init) ? '' : ',';
						$nuts->parse('data_c.c_col1', $col[0]);
						$nuts->parse('data_c.c_col2', $col[1]);
						$nuts->parse('data_c.c_comma', $comma);
						$nuts->loop('data_c');
						$init = true;
					}
				}



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

				nutsSetCache('widget-ga', $contents, '+1 day');
			} catch (Exception $e){
                // echo 'Exception reçue : ',  $e->getMessage(), "\n";
				$contents = $e->getMessage();
			}
		}

		Plugin::dashboardAddWidget($lang_msg[0], 'final', 'analytics', 'full', '', $contents);

	}
}

