<?php

define('ga_email','youremail@email.com');
define('ga_password','your password');
define('ga_profile_id','xxxxxxxxxxx');

require 'gapi.class.php';
$ga = new gapi(ga_email, ga_password);

$ga->requestReportData(ga_profile_id, array('date'), array('visitors', 'visits', 'pageviews'), '', '', date('Y-m-d', strtotime('-31 days')), date('Y-m-d', strtotime('-1 days')));


$total_pageviews = 0;
$total_visitors = 0;
$total_visits = 0;
$reporting = array();
foreach($ga->getResults() as $result)
{
	$date = $result->getDimesions();
	$date = substr($date['date'], 0, 4).'-'.substr($date['date'], 4, 2).'-'.substr($date['date'], 6, 2);
	$reporting[$date] = $result->getMetrics();

	$total_pageviews += $reporting[$date]['pageviews'];
	$total_visitors += $reporting[$date]['visitors'];
	$total_visits += $reporting[$date]['visits'];
}

ksort($reporting);
$tmp = $reporting;
$tmp['total_pageviews'] = $total_pageviews;
$tmp['total_visitors'] = $total_visitors;
$tmp['total_visits'] = $total_visits;
$reporting = $tmp;
print_r($reporting);
die();


?>
<table>
<tr>
  <th>Pageviews</th>
  <th>Visits</th>
</tr>
<?php
foreach($ga->getResults() as $result):
?>
<tr>
  <td><?php echo $result->getPageviews() ?></td>
  <td><?php echo $result->getVisits() ?></td>
</tr>
<?php
endforeach
?>
</table>

<table>
<tr>
  <th>Total Results</th>
  <td><?php echo $ga->getTotalResults() ?></td>
</tr>
<tr>
  <th>Total Pageviews</th>
  <td><?php echo $ga->getPageviews() ?>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getVisits() ?></td>
</tr>
<tr>
  <th>Results Updated</th>
  <td><?php echo $ga->getUpdated() ?></td>
</tr>
</table>