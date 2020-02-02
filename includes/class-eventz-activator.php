<?php
/**
 * Fired during plugin activation.
 * This class defines all code necessary to run during the plugin's activation.
 * @link       http://onebyte.nz
 * @since      1.0.0
 * @package    Eventz Lite
 * @subpackage Eventz/includes
 * @author     Craig Sugden - onebyte.nz <info@onebyte.nz>
 */
class Eventz_Lite_Activator {
    public static function activate() {
        if (!current_user_can('activate_plugins')) {
            wp_die( __('You do not have sufficient permissions to perform this action.'));
        }
        if (version_compare(get_bloginfo('version'), '4.0', '<' )) {
            wp_die('WordPress 4.0 and above is required to use this plugin.');
        }
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "activate-plugin_{$plugin}" );
    }
}