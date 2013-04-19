<?php
/**
 * Plugin user-shortcuts - widget
 *
 * @version 1.0
 * @date 19/04/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

$shortcuts = Query::factory()->select('Plugin')->from('NutsUserShortcut')->whereEqualTo('NutsUserID', $_SESSION['NutsUserID'])->order_by('Position')->executeAndGetAllOne();

if(count($shortcuts) > 0)
{
    $pref_language = ($_SESSION['Language'] == 'fr') ? 'fr' : 'en';
    // get plugin name translated
    if(file_exists(NUTS_PLUGINS_PATH.'/_user-shortcuts/lang/'.$pref_language.'.inc.php'))
        include(NUTS_PLUGINS_PATH.'/_user-shortcuts/lang/'.$pref_language.'.inc.php');
    else
        include(NUTS_PLUGINS_PATH.'/'.$shortcut.'/lang/en.inc.php');
    $title = $lang_msg[0];

    $content = '';
    foreach($shortcuts as $shortcut)
    {
        // get plugin name translated
        if(file_exists(NUTS_PLUGINS_PATH.'/'.$shortcut.'/lang/'.$pref_language.'.inc.php'))
            include(NUTS_PLUGINS_PATH.'/'.$shortcut.'/lang/'.$pref_language.'.inc.php');
        else
            include(NUTS_PLUGINS_PATH.'/'.$shortcut.'/lang/en.inc.php');

        $content .= <<<EOF

            <a href="#" class="list-image">
                <img src="/plugins/{$shortcut}/icon.png" /><br />
                {$lang_msg[0]}
            </a>
EOF;

    }

    Plugin::dashboardAddWidget($title, 'high', 'shortcut', 'full', '', $content);

}





?>


