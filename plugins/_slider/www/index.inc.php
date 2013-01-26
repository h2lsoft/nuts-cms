<?php
/**
 * Plugin slider - Front office
 * 
 * @version 1.0
 * @date 01/01/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Page */
/* @var $nuts Page */

$sliderID = (int)$plugin->getPluginParameter(0);
$slider = Query::factory()->select('*')->from('NutsSlider')->where('ID', '=', $sliderID)->executeAndFetch();

if(!$slider)
{
    setNutsContent("Error: slider not found");
}
else
{
    include_once($plugin->plugin_path.'/config.inc.php');

    if($include_plugin_css)$plugin->addHeaderFile('css', '/plugins/_slider/www/style.css');
    if($include_plugin_js)$plugin->addHeaderFile('js', '/plugins/_slider/www/jquery.carouFredSel.js', false);

    $plugin->openPluginTemplate();
    $plugin->parse('sliderID', $sliderID);
    $plugin->parse('Width', $slider['Width']);
    $plugin->parse('Height', $slider['Height']);
    $plugin->parse('Direction', strtolower($slider['Direction']));
    $plugin->parse('Align', strtolower($slider['Align']));
    $plugin->parse('Padding', $slider['Padding']);
    $plugin->parse('Circular', ($slider['Circular']=='YES') ? true : false);
    $plugin->parse('Infinite', ($slider['Infinite']=='YES') ? true : false);
    $plugin->parse('Items', $slider['Items']);
    $plugin->parse('Fx', strtolower($slider['Fx']));
    $plugin->parse('PauseDuration', $slider['PauseDuration']);
    $plugin->parse('ScrollDuration', $slider['ScrollDuration']);

    $slider_images = Query::factory()->select('*')->from('NutsSliderImage')->where('NutsSliderID', '=', $sliderID)->where('Visible', '=', 'YES')->order_by('Position')->executeAndGetAll();

    if(!count($slider_images))
    {
        $plugin->eraseBloc('loop');
    }
    else
    {
        foreach($slider_images as $slider_image)
        {
            $plugin->parse('loop.SliderImage', $slider_image['SliderImage']);
            $plugin->parse('loop.Title', $slider_image['Title']);
            $plugin->parse('loop.Url', $slider_image['Url']);
            $plugin->loop('loop');
        }
    }



    $plugin->setNutsContent();
}








?>