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

Query::factory()->select('Plugin, (SELECT ExternalUrl FROM NutsMenu WHERE Name = NutsUserShortcut.Plugin) AS ExternalUrl')->from('NutsUserShortcut')->whereEqualTo('NutsUserID', $_SESSION['NutsUserID'])->order_by('Position')->execute();

$shortcuts = array();
$shortcuts_db = array();
while($r = $nuts->dbFetch())
{
    $shortcuts[] = $r['Plugin'];
    $shortcuts_db[$r['Plugin']] = $r['ExternalUrl'];
}



if(count($shortcuts) > 0)
{

    // get plugin name translated
    include(Plugin::getIncludeUserLanguagePath('_user-shortcuts'));
    $title = $lang_msg[0];

    $content = '';
    foreach($shortcuts as $shortcut)
    {
        // get plugin name translated
        include(Plugin::getIncludeUserLanguagePath($shortcut));

        // external url
        if(!empty($shortcuts_db[$shortcut]))
        {
            $blank = (preg_match('#^(http|ftp|mailto)#i', $shortcuts_db[$shortcut])) ? '_blank' : '';
            if($shortcuts_db[$shortcut][0] == '/')$blank = '';
            $uri = $shortcuts_db[$shortcut].'" target="'.$blank;
        }
        else
        {
            $yaml = Spyc::YAMLLoad(WEBSITE_PATH.'/plugins/'.$shortcut.'/info.yml');
            $default_action = $yaml['default_action'];
            $uri = 'javascript:;" onclick="system_goto(\'index.php?mod='.$shortcut.'&do='.$default_action.'\', \'content\'); return false;';
        }


        $content .= <<<EOF

            <a href="{$uri}" class="list-image">
                <img src="/plugins/{$shortcut}/icon.png" /><br />
                {$lang_msg[0]}
            </a>
EOF;

    }

    Plugin::dashboardAddWidget($title, 'high', 'shortcut', 'full', '', $content);

}





?>


