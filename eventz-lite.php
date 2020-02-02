<?php
/*
 * @link              http://onebyte.nz
 * @since             1.0.0
 * @package           Eventz Lite
 * @wordpress-plugin
 * Plugin Name:       Eventz Lite
 * Plugin URI:        http://plugin.onebyte.nz
 * Description:       Easily display Eventfinda listings on your web site with a simple shortcode.
 * Version:           1.3.7
 * Author:            onebyte.nz
 * Author URI:        http://onebyte.nz
 * Text Domain:       eventz-lite
 * Domain Path:       /languages
 */
if ( !defined( 'WPINC' ) ) {
	die();
}
if (!defined('EVENTZ_LITE_VERSION')) {
    define('EVENTZ_LITE_VERSION', '1.3.7');
}
if (!defined('EVENTZ_LITE_OPTION')) {
    define('EVENTZ_LITE_OPTION', 'plugin_eventz_lite_options');
}
function activate_eventz_lite()
{
    include_once plugin_dir_path(__FILE__) . 'includes/class-eventz-activator.php';
    Eventz_Lite_Activator::activate();
}
function deactivate_eventz_lite()
{
    include_once plugin_dir_path(__FILE__) . 'includes/class-eventz-deactivator.php';
    Eventz_Lite_Deactivator::deactivate();
}
function eventz_lite_set_options()
{
    global $eventz_lite_defaults;
    $option_name = EVENTZ_LITE_OPTION;
    $eventz_lite_defaults = eventz_lite_defaults();
    /* Get options if any */
    $old_options = get_option($option_name, array());
    if(!$old_options) {
        /* Not present  - create */
        $options = $eventz_lite_defaults;
        update_option($option_name, $options);
    } else {
        if ($old_options['_version'] !== EVENTZ_LITE_VERSION ) {
            /* Merge with defaults to ensure all required (and new) options present */
            $options = wp_parse_args($old_options, $eventz_lite_defaults);
            /* Update */
            update_option($option_name, $options);
        } else {
            $options = wp_parse_args($old_options, $eventz_lite_defaults);
        }
    }
    return apply_filters('eventz_lite_options', $options);
}
function eventz_lite_set_defaults()
{
    $defaults = array (
        '_version' => EVENTZ_LITE_VERSION,
        '_endpoint' => 'api.eventfinda.co.nz',
        '_username' => '',
        '_password' => '',
        '_event_location' => '1',
        '_event_date' => '1',
        '_event_category' => '1',
        '_event_separator' => '1',
        '_event_excerpt' => '220',
        '_results_pp' => '10',
        '_debug' => '0',
        '_debug_screen' => '0',
        '_delete_options' => '0',
        '_eventfinda_logo' => '1',
        '_eventfinda_text' => '0',
        '_show_plugin_logo' => '0',
        '_show_plugin_link' => '0',
        '_extensions' => array(),
        '_licenses' => array()
    );
    return $defaults;
}
function eventz_lite_defaults()
{
    return apply_filters( 'eventz_lite_defaults', eventz_lite_set_defaults() );
}
function run_eventz_lite()
{
    global $eventz_lite_options;
    $eventz_lite_options = eventz_lite_set_options();
    $plugin = new Eventz_Lite();
    $plugin->run();  
}
register_activation_hook(__FILE__, 'activate_eventz_lite');
register_deactivation_hook(__FILE__, 'deactivate_eventz_lite');
require plugin_dir_path(__FILE__) . 'includes/class-eventz.php';
run_eventz_lite();
