<?php
/**
 * Fired during plugin deactivation.
 * @link       http://onebyte.nz
 * This class defines all code necessary to run during the plugin's deactivation.
 * @since      1.0.0
 * @package    Eventz Lite
 * @subpackage Eventz/includes
 * @author     Craig Sugden - onebyte.nz <info@onebyte.nz>
 */
class Eventz_Lite_Deactivator {
    /**
     * @since    1.0.0
    */
    public static function deactivate() {
        if (!current_user_can('activate_plugins')) {
            wp_die( __('You do not have sufficient permissions to perform this action.'));
        }
        global $eventz_lite_options;
        $url = '';
        $options = $eventz_lite_options;
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer("deactivate-plugin_{$plugin}");
        if (!empty($options['_extensions'])) {
            $referer = wp_get_referer();
            if (!$referer) {
                $url = admin_url('plugins.php');
            } else {
                $url = $referer;
            }
            wp_die( __('Please deactivate all Eventz Lite extensions before deactivating Eventz Lite.', 'eventz-lite') . 
                '<br><br><a href="' . $url . '">' . __('Back to Plugins', 'eventz-lite') . '</a>');
        }
    }
}
