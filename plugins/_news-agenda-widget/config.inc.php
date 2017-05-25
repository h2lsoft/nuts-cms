<?php

$include_plugin_css = true; // include bundle css dynamically
$include_plugin_js = true; // include bundle js dynamically

$calendar_widget_uri = "/en/1.html"; // uri to get ajax parameters (miust be different of news view)

$calendar_widget_has_filters = true; // add filter option

$calendar_widget_filters_sql = "SELECT DISTINCT Type FROM NutsNews WHERE Deleted = 'NO' AND Type != '' ORDER BY Type";

// cur_year, cur_month_label, sql_added
$calendar_widget_sql_news = "SELECT
                                    DateGMT,
                                    DATE_FORMAT(DateGMT, '%d/%m/%Y') AS Date,
                                    Title,
                                    VirtualPageName,
                                    Type
                            FROM
                                    NutsNews
                            WHERE
                                    Deleted = 'NO' AND
                                    Active = 'YES' AND
                                    DateGMT LIKE '[cur_year]-[cur_month]-%'
                                    [sql_added]
                            ORDER BY
                                    ID";

$calendar_widget_sql_news_filter_column = 'Type';

