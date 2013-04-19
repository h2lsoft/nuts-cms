<?php
/**
 * Plugin page-manager - widget
 *
 * @version 1.0
 * @date 19/04/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(Plugin::getIncludeUserLanguagePath('_page-manager'));

$nuts->open(NUTS_PLUGINS_PATH.'/_page-manager/widget.html');


// last edited *********************************************************************************************************
$pages = Query::factory()->select("ID, MenuName, DateUpdate")
                         ->from('NutsPage')
                         ->whereEqualTo('State', 'PUBLISHED')
                         ->whereEqualTo('NutsUserID', $_SESSION['NutsUserID'])
                         ->order_by('DateUpdate DESC')
                         ->limit(30)
                         ->executeAndGetAll();

$nuts->parse('last_edited_nb', count($pages));

if(count($pages) == 0)
{
    $nuts->eraseBloc('page_le', "<div class=\"no-record\">{$lang_msg[115]}</div>");
}

foreach($pages as $page)
{
    if($_SESSION['Language'] == 'fr')
        $page['DateUpdate'] = $nuts->db2Date($page['DateUpdate']);

    $nuts->parse('page_le.ID', $page['ID']);
    $nuts->parse('page_le.Date', $page['DateUpdate']);
    $nuts->parse('page_le.Name', $page['MenuName']);
    $nuts->loop('page_le');
}


// draft ***************************************************************************************************************
$pages = Query::factory()->select("ID, MenuName, DateUpdate")
                        ->from('NutsPage')
                        ->whereEqualTo('State', 'DRAFT')
                        ->whereEqualTo('NutsUserID', $_SESSION['NutsUserID'])
                        ->order_by('DateUpdate DESC')
                        ->limit(30)
                        ->executeAndGetAll();

$nuts->parse('draft_nb', count($pages));

if(count($pages) == 0)
    $nuts->eraseBloc('page_dr', "<div class=\"no-record\">{$lang_msg[115]}</div>");

foreach($pages as $page)
{
    if($_SESSION['Language'] == 'fr')
        $page['DateUpdate'] = $nuts->db2Date($page['DateUpdate']);

    $nuts->parse('page_dr.ID', $page['ID']);
    $nuts->parse('page_dr.Date', $page['DateUpdate']);
    $nuts->parse('page_dr.Name', $page['MenuName']);
    $nuts->loop('page_dr');
}

// locked ***************************************************************************************************************
$pages = Query::factory()->select("ID, MenuName, DateUpdate")
    ->from('NutsPage')
    ->whereEqualTo('Locked', 'YES')
    ->whereEqualTo('LockedNutsUserID', $_SESSION['NutsUserID'])
    ->order_by('DateUpdate DESC')
    ->limit(30)
    ->executeAndGetAll();

$nuts->parse('locked_nb', count($pages));

if(count($pages) == 0)
    $nuts->eraseBloc('page_lk', "<div class=\"no-record\">{$lang_msg[115]}</div>");

foreach($pages as $page)
{
    if($_SESSION['Language'] == 'fr')
        $page['DateUpdate'] = $nuts->db2Date($page['DateUpdate']);

    $nuts->parse('page_lk.ID', $page['ID']);
    $nuts->parse('page_lk.Date', $page['DateUpdate']);
    $nuts->parse('page_lk.Name', $page['MenuName']);
    $nuts->loop('page_lk');
}

// waiting moderation **************************************************************************************************
$pages = Query::factory()->select("ID, MenuName, DateUpdate")
    ->from('NutsPage')
    ->whereEqualTo('State', 'WAITING MODERATION')
    ->order_by('DateUpdate DESC')
    ->limit(30)
    ->executeAndGetAll();

$nuts->parse('waiting_moderation_nb', count($pages));

if(count($pages) == 0)
    $nuts->eraseBloc('page_wm', "<div class=\"no-record\">{$lang_msg[115]}</div>");

foreach($pages as $page)
{
    if($_SESSION['Language'] == 'fr')
        $page['DateUpdate'] = $nuts->db2Date($page['DateUpdate']);

    $nuts->parse('page_wm.ID', $page['ID']);
    $nuts->parse('page_wm.Date', $page['DateUpdate']);
    $nuts->parse('page_wm.Name', $page['MenuName']);
    $nuts->loop('page_wm');
}


Plugin::dashboardAddWidget($lang_msg[0], 'medium', 'shortcut', 'full', '', $nuts->output());






?>


