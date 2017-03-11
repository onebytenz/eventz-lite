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
    public static $option_name = 'plugin_eventz_lite_options';
    public static function activate() {
        if (!current_user_can('activate_plugins')) {
            wp_die( __('You do not have sufficient permissions to perform this action.'));
        }
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "activate-plugin_{$plugin}" );
        $this_plugin = new Eventz_Lite;
        $version = $this_plugin->get_version();
        unset($this_plugin);
        $default_options = array('_version'=>$version,'_username'=>'','_password'=>'',
            '_endpoint'=>'api.eventfinda.co.nz','_event_location'=>'1','_event_date'=>'1',
                '_event_category'=>'1','_event_excerpt'=>'300','_event_separator'=>'1',
                    '_results_pp'=>'10','_delete_options'=>'0','_eventfinda_logo'=>'1',
                        '_eventfinda_text'=>'0','_show_plugin_logo'=>'0','_show_plugin_link'=>'0');
        $options = get_option(self::$option_name,array());
        if(!$options) {
            add_option(self::$option_name, $default_options);
        } else {
            /* For Future Updates */
            /* Update Version */
            $options['_version'] = $version;
            update_option(self::$option_name, $options);
            /* Add New Options */
            $new_key = '_event_location';
            $new_options = array('_event_location'=>'1',
                                    '_event_date'=>'1',
                                    '_event_category'=>'1',
                                    '_event_excerpt'=>'300',
                                    '_event_separator'=>'1'
                                );
            if (!array_key_exists($new_key, $options)) {
                /* Probaly safe to assume other keys do not exist so update options */
                update_option(self::$option_name, array_merge($new_options, $options));
            }
        }
    }
}