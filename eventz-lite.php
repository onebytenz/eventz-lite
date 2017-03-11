<?php
/*
 * @link              http://onebyte.nz
 * @since             1.0.0
 * @package           Eventz Lite
 * @wordpress-plugin
 * Plugin Name:       Eventz Lite
 * Plugin URI:        http://plugin.onebyte.nz
 * Description:       Easily display Eventfinda listings on your web site with a simple shortcode.
 * Version:           1.1.0
 * Author:            onebyte.nz
 * Author URI:        http://onebyte.nz
 * Text Domain:       eventz-lite
 * Domain Path:       /languages
 */
if (!defined('WPINC' )) {
    die();
}
function activate_eventz_lite() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-eventz-activator.php';
    Eventz_Lite_Activator::activate();
}
function deactivate_eventz_lite() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-eventz-deactivator.php';
    Eventz_Lite_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_eventz_lite' );
register_deactivation_hook( __FILE__, 'deactivate_eventz_lite' );
require plugin_dir_path( __FILE__ ) . 'includes/class-eventz.php';
function run_eventz_lite() {
    $plugin = new Eventz_Lite();
    $plugin->run();  
}
run_eventz_lite();