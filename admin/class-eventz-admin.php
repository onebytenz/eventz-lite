<?php
/**
 * The admin-specific functionality of the plugin.
 * @link       http://onebyte.nz
 * @since      1.0.0
 * @package    Eventz Lite
 * @subpackage Eventz/admin
 * @author     Craig Sugden - onebyte.nz <info@onebyte.nz>
 */
class Eventz_Lite_Admin {
    private $plugin_name;
    private $version;
    private $options;
    private $eventfinda_link;
    private $plugin_extended = false;    
    private $option_name = 'plugin_eventz_lite_options';

    public function __construct($plugin_name, $version) {
        global $eventz_lite_options;
        $this->options = $eventz_lite_options;
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        if ($this->options !== false) {
            if ($this->options['_version'] !== $version) {
                $this->options['_version'] = $version;
                update_option($this->option_name, $this->options);
            }
            if (!empty($this->options['_extensions'])) {
                $this->plugin_extended = true;
            }
        }
        add_action('wp_ajax_eventz_lite_check_user', array($this, $this->plugin_name . '_check_user'));
    }
    public function enqueue_styles() {
        /* An instance of this class should be passed to the run() function
         * defined in Eventz_Loader as all of the hooks are defined
         * in that particular class.
         * The Eventz_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/eventz-admin.min.css', array(), $this->version, 'all');
    }
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'js/eventz-admin.min.js', '', $this->version, false);
        wp_enqueue_script('jquery-form');
        if(!wp_script_is('jquery-ui-dialog')) {wp_enqueue_script('jquery-ui-dialog');} 
    }
    /*
        * Add settings action link to the plugins page.
        * @since 1.0.0
    */
    public function add_settings_links($links) {
        $settings_link = array(
            '<a href="' . admin_url('options-general.php?page=eventz-lite') . '">' . __('Settings', 'eventz-lite') . '</a>',
            '<a href="https://support.onebyte.nz/" target="_blank">' . __('Support', 'eventz-lite') . '</a>',
            '<a href="https://plugin.onebyte.nz/eventz-pro/" target="_blank">' . __('Go Pro', 'eventz-lite') . '</a>'
        );
        return array_merge($settings_link, $links);
    }
    public function add_options_page() {
        $this->plugin_screen_hook_suffix = add_options_page(
            __('Eventz Lite', 'eventz-lite'),
            __('Eventz Lite', 'eventz-lite'),
            'manage_options',
            'eventz-lite',
            array($this, 'display_options_page')
        );
    }
    public function display_options_page() {
        if (!current_user_can('manage_options'))  {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        include_once 'partials/eventz-admin-display.php';
    }
    public function register_setting() {
        register_setting(
            'eventz-lite',
            $this->option_name,
            array($this, $this->plugin_name . '_validate_options') 
	);
        add_settings_section(
            '_general',
            __('Eventfinda API Setup', 'eventz-lite'),
            array($this, $this->plugin_name . '_general_cb'),
            $this->plugin_name . '_general'
        );
        add_settings_field(
            '_endpoint',
            __('Eventfinda API Endpoint', 'eventz-lite') . ': <span id="eventz-icon-endpoint" class="dashicons dashicons-editor-help icenter" ' . 
            'title="' . __('The Eventfinda server you would like to get the event listings from.', 'eventz-lite') . '"></span>',
            array($this, $this->plugin_name . '_endpoint_cb'),
            $this->plugin_name . '_general',
            '_general',
            array('label_for' => '_endpoint')
        );
        add_settings_field(
            '_username',
            __('Eventfinda API User Name', 'eventz-lite') . ': <span id="eventz-icon-username" class="dashicons dashicons-editor-help icenter" ' .
            'title="' . __('Your Eventfinda API username. You can request this from the Eventfinda API page.', 'eventz-lite') .'"></span>',
            array($this, $this->plugin_name . '_username_cb'),
            $this->plugin_name . '_general',
            '_general',
            array('label_for' => '_username')
        );
        add_settings_field(
            '_password',
            __('Eventfinda API Password', 'eventz-lite') . ': <span id="eventz-icon-password" class="dashicons dashicons-editor-help icenter" ' .
            'title="' . __('Your Eventfinda API password.', 'eventz-lite') . '"></span>',
            array($this, $this->plugin_name . '_password_cb'),
            $this->plugin_name . '_general',
            '_general',
            array('label_for' => '_password')
        );
        add_settings_section(
            '_display',
            __('Event Display Options', 'eventz-lite'),
            array($this, $this->plugin_name . '_general_cb'),
            $this->plugin_name . '_display'
        );
        add_settings_field(
            '_display_options_header',
            __('Listing Display Options', 'eventz-lite') . ': <span id="eventz-icon-display" class="dashicons dashicons-editor-help icenter" ' .
            'title="' . __('Select which items to show for each event listing.', 'eventz-lite') . '"></span>',
            array($this, $this->plugin_name . '_display_options_header_cb'),
            $this->plugin_name . '_display',
            '_display',
            array('label_for' => '_display_options_header')
        );
        add_settings_field(
            '_display_options',
            __('', 'eventz-lite'),
            array($this, $this->plugin_name . '_display_options_cb'),
            $this->plugin_name . '_display',
            '_display',
            array('label_for' => '_display_options')
        );
        add_settings_field(
            '_excerpt',
            __('Excerpt Length', 'eventz-lite') . ': <span id="eventz-icon-excerpt" class="dashicons dashicons-editor-help icenter" ' .
            'title="' . __('Select how many characters to show for each event description.', 'eventz-lite') . '"></span>',
            array($this, $this->plugin_name . '_excerpt_cb'),
            $this->plugin_name . '_display',
            '_display',
            array('label_for' => '_excerpt')
        );
        add_settings_field(
            '_results_pp',
            __('Results Per Page', 'eventz-lite') . ': <span id="eventz-icon-results" class="dashicons dashicons-editor-help icenter" title="' . 
            '' . __('Select how many listings to show per page.', 'eventz-lite') . '"></span>',
            array($this, $this->plugin_name . '_results_pp_cb'),
            $this->plugin_name . '_display',
            '_display',
            array('label_for' => '_results_pp')
        );
        add_settings_section(
            '_extensions',
            __('Installed Extensions', 'eventz-lite'),
            array($this, $this->plugin_name . '_extensions_cb'),
            $this->plugin_name . '_extensions'
        );
        add_settings_section(
            '_licenses',
            __('Extension Licenses', 'eventz-lite'),
            array($this, $this->plugin_name . '_licenses_cb'),
            $this->plugin_name . '_licenses'
        );
        add_settings_section(
            '_debugging',
            __('Logging Options', 'eventz-lite'),
            array($this, $this->plugin_name . '_general_cb'),
            $this->plugin_name . '_debugging'
        );
        add_settings_field(
            '_debug',
            __('File Logging', 'eventz') . ': <span id="eventz-icon-debug" class="dashicons dashicons-editor-help icenter" ' .
            'title="' . __('Check this box to write errors and general debug information to the Wordpress Debug Log', 'eventz-lite') .
                '. ' . __('Set WP_DEBUG and WP_DEBUG_LOG to true in wp-config.php', 'eventz-lite') .
                    '. ' . __('WP_DEBUG_DISPLAY can be set to false to hide PHP errors on the page', 'eventz-lite') .
                        '."></span>',
            array($this, $this->plugin_name . '_debug_cb'),
            $this->plugin_name . '_debugging',
            '_debugging',
            array('label_for' => '_debug')
        );
        add_settings_field(
            '_debug_screen',
            __('On Screen Logging', 'eventz-lite') . ': <span id="eventz-icon-debug-screen" class="dashicons dashicons-editor-help icenter" ' .
            'title="' . __('Check this box to add extended error messages to the public facing pages on the site', 'eventz-lite') .
            '."></span>',
            array($this, $this->plugin_name . '_debug_screen_cb'),
            $this->plugin_name . '_debugging',
            '_debugging',
            array('label_for' => '_debug_screen')
        );
        add_settings_section(
            '_misc',
            __('Miscellaneous Settings', 'eventz-lite'),
            array($this, $this->plugin_name . '_general_cb'),
            $this->plugin_name . '_misc'
        );
        add_settings_field(
            '_delete_options',
            __('Delete Settings On Uninstall', 'eventz-lite') . ': <span id="eventz-icon-delete-options" class="dashicons dashicons-editor-help icenter" ' .
            'title="' . __('Check this box to delete the plugin settings on uninstall', 'eventz-lite') . '."></span>',
            array($this, $this->plugin_name . '_delete_options_cb'),
           $this->plugin_name . '_misc',
            '_misc',
            array('label_for' => '_delete_options')
        );
        add_settings_section(
            '_branding',
            __('Branding Options', 'eventz-lite'),
            array($this, $this->plugin_name . '_general_cb'),
            $this->plugin_name . '_branding'
        );
        add_settings_field(
            '_eventfinda_branding',
            __('Eventfinda Branding', 'eventz-lite') . ': <span id="eventz-icon-eventfinda" class="dashicons dashicons-editor-help icenter" ' .
            'title="' . __('Eventfinda API terms require that you display a link to their site', 'eventz-lite') . 
                    '. ' . __('You can choose to display the Eventfinda logo or a plain text link', 'eventz-lite') . '."></span>',
            array($this, $this->plugin_name . '_eventfinda_branding_cb'),
            $this->plugin_name . '_branding',
            '_branding',
            array('label_for' => '_eventfinda_branding')
        );
        add_settings_field(
            '_plugin_branding',
            __('Plugin Branding', 'eventz-lite') . ' <span id="eventz-icon-plugin" class="dashicons dashicons-editor-help icenter" ' .
                'title="' . __('If you would like to display a link to our web site that would be very much appreciated', 'eventz-lite') . 
                    '. ' . __('You can choose to display the eventz lite logo or a plain text link', 'eventz-lite') .
                        '."></span>',
            array($this, $this->plugin_name . '_plugin_branding_cb'),
            $this->plugin_name . '_branding',
            '_branding',
            array('label_for' => '_plugin_branding')
        );
        add_settings_section(
            '_shortcode_guide',
            __('Shortcode Guide', 'eventz-lite'),
            array($this, $this->plugin_name . '_general_cb'),
            $this->plugin_name . '_shortcode_guide'
        );
    }
    public function eventz_lite_validate_options ($input) {
        $options = $this->options;
        if (!is_array($input)) {return $options;}
        $values = shortcode_atts($options, $input);
        $out = array ();
        foreach ($options as $key => $value) {
            /*switch ($key) {
                case '_extensions':
                    error_log('_extensions: ' . var_export($values[$key], 1));
                    break;
            }*/
            switch ($values[$key]) {
                case empty($values[$key]):
                    error_log('empty($values[$key]): ' . var_export($values[$key], 1));
                    $out[$key] = $value;                   
                    break;
                default:
                    switch ($key) {
                        case '_username':
                            $out[$key] = sanitize_text_field($values[$key]);
                            break;
                        case '_password':
                            $out[$key] = sanitize_text_field($values[$key]);
                            break;
                        case '_endpoint':
                            $out[$key] = sanitize_text_field($values[$key]);
                            break;
                        case '_extensions':
                            $out[$key] = $values[$key];
                            break;
                        case '_licenses':
                            switch ($value) {
                                case '[]':
                                    $out[$key] = array();
                                    break;
                                case is_array($value) && !empty($value):
                                    $out[$key] = $values[$key];
                                default:
                                    $out[$key] = array();
                            }
                            break;
                        default:
                            if (is_array($values[$key])) {
                                $out[$key] = $values[$key];
                            } else {
                                $out[$key] = filter_var($values[$key], FILTER_SANITIZE_NUMBER_INT);
                            }
                            break;
                    }
                    break;
            }
        }
        return $out;
    }
    public function eventz_lite_general_cb() {
        echo '<p><small>' . __('onebyte Eventz Lite Version' . ' ' . $this->options['_version'] . '</small>', 'eventz-lite') . '</p>';
        //print_r($this->options);
    }
    public function eventz_lite_extensions_cb() {
        $htmlstr = '';
        $ext_opts = '';
        $ext_header = '';
        $ext_content = '';
        $extensions = $this->options['_extensions'];
        $html = '<form name=""><div class="wrap el-license-wrap">' .
            '<p><small>' . __('onebyte Eventz Lite Version' . ' ' . 
                $this->options['_version'] . '</small>', 'eventz-lite') . '</p>';
        $ext_table = '<div class="el-ext"><table class="form-table">' .
            '<tbody><tr><th scope="row">[EXT_HEAD]</th>' .
                '<td>[EXT_CONTENT]</td>' .
                    '</tr></tbody></table></div>';
        if (empty($extensions)) {
            $html .= '<p>' . __('No extensions currently installed or activated.', 'eventz-lite') . '</p>';
        } else {
            if (is_array($extensions)) {
                foreach ($extensions as $key => $extension) {
                    //$option = $this->option_name . '[_extensions][' . $key . ']';
                    $ext_id = $this->option_name . '[_extensions][' . $key . ']';
                    //$ext_lic_id = $option . '[license_key]';
                    $ext_name = (!empty($extension['name'])) ? $extension['name'] : '';
                    $ext_version = (!empty($extension['version'])) ? $extension['version'] : '';
                    $ext_author = (!empty($extension['author'])) ? $extension['author'] : '';
                    $ext_url = (!empty($extension['author_url'])) ? $extension['author_url'] : '';
                    //$ext_key = (!empty($extension['license_key'])) ? $extension['license_key'] : '';
                    $ext_desc = (!empty($extension['description'])) ? $extension['description'] : '';
                    $ext_opt_id = 'extension-' . $key . '-options';
                    $ext_has_opts = array_key_exists($ext_opt_id, $this->options);
                    if ($ext_has_opts) {
                        $ext_opts = (!empty($this->options[$ext_opt_id])) ? $this->options[$ext_opt_id] : '';
                    }
                    switch ($ext_url) {
                        case '':
                            $ext_author_url = $ext_author;
                            break;
                        default: 
                            $ext_author_url = '<a style="text-decoration:none;float:right;" href="' . $ext_url .'" target="_blank">' . $ext_author . '</a>';
                            break;
                    }
                    if (is_array($ext_opts)) {
                        if (empty($ext_opts)) {
                            $ext_opts = __('No options to configure for this extension.', 'eventz-lite');
                        } else {
                            foreach ($ext_opts as $option => $value){
                                $checked = '';
                                if ($value === '1') {$checked = 'checked';}
                                $option_name = $this->option_name . '[' . $ext_opt_id . '][' . $option . ']';
                                $ext_opts = '<input type="hidden" name="' . $option_name . '" id="' . $option_name . '[off]" value="0">' . "\r\n" .
                                    '<input type="checkbox" name="' . $option_name . '" id="' . $option_name . '" value="1" ' . 
                                        $checked . '> Delete Options on Deactivation.' . "\r\n";
                            }
                        }
                    }
                    $ext_heaader_str = $ext_name . ' Version ' . $ext_version . ' ' . $ext_author_url;
                    $ext_header = str_replace('[EXT_HEAD]', $ext_heaader_str, $ext_table);
                    $htmlstr = '<p>' . $ext_desc . '</p><p>' . $ext_opts .'</p>';
                    $ext_content = str_replace('[EXT_CONTENT]', $htmlstr, $ext_header);
                    $html .= str_replace('[EXT_ID]', $ext_id, $ext_content);
                }
                
            } else {
                $html .= '<p>' . __('An error occurred while getting installed extension information.', 'eventz-lite') . '</p>';
            }
        }
        $html .= '</div>';
        echo $html;
    }
    public function eventz_lite_licenses_cb () {
        $htmlstr = '';
        $ext_header = '';
        $ext_content = '';
        $extensions = $this->options['_extensions'];
        $html = '<div class="wrap el-license-wrap">' .
            '<p><small>' . __('onebyte Eventz Lite Version' . ' ' . 
                $this->options['_version'] . '</small>', 'eventz-lite') . '</p>';
        $ext_table = '<div class="el-ext"><table class="form-table">' .
            '<tbody><tr><th scope="row">[EXT_NAME]</th>' .
                '<td>[EXT_CONTENT]</td>' .
                    '</tr></tbody></table></div>';
        $msg_div_e = '<div class="el-license-msg el-license-empty">' .
            '<p>Please enter your [EXT_NAME] license key to receive updates and support.</p>' .
                '</div>';
        if (empty($extensions)) {
            $html .= '<p>' . __('No extensions currently installed or activated.', 'eventz-lite') . '</p>';
        } else {
            if (is_array($extensions)) {
                add_action('wp_ajax_eventz_lite_check_license', array($this, $this->plugin_name . '_check_license'));
                foreach ($extensions as $key => $extension) {
                    $option = $this->option_name . '[_extensions][' . $key . ']';
                    $ext_lic_id = $option . '[license_key]';
                    $ext_name = (!empty($extension['name'])) ? $extension['name'] : '';
                    $ext_version = (!empty($extension['version'])) ? $extension['version'] : '';
                    $ext_author = (!empty($extension['author'])) ? $extension['author'] : '';
                    $ext_url = (!empty($extension['author_url'])) ? $extension['author_url'] : '';
                    $ext_key = (!empty($extension['license_key'])) ? $extension['license_key'] : '';
                    $ext_desc = (!empty($extension['description'])) ? $extension['description'] : '';
                    $ext_opts = (!empty($extension['options'])) ? json_encode($extension['options']) : '';                    
                    $input = '<input type="hidden" name="' . $option;
                    $input_n = $input . '[name]" value="' . $ext_name . '">';
                    $input_v = $input . '[version]" value="' . $ext_version . '">';
                    $input_a = $input . '[author]" value="' . $ext_author . '">';
                    $input_u = $input . '[author_url]" value="' . $ext_url . '">';
                    $input_d = $input . '[description]" value="' . $ext_desc . '">';
                    $input_o = $input . '[options]" value="' . $ext_opts . '">';
                    $ext_header = str_replace('[EXT_NAME]', $ext_name . ' Version ' . $ext_version, $ext_table);
                    $htmlstr = '<input type="text" maxlength="40" data-rule-required="true" data-msg-required=" ' .
                        __('Please enter your license key', 'eventz-lite') . '." class="regular-text" name="' . 
                            $ext_lic_id . '" id="_extensions_' . 
                                $key . '_license_key" value="' . $ext_key . '" required>' .
                                    $input_n . $input_v . $input_a . $input_u . $input_d . $input_o;
                    switch ($ext_key) {
                        case '':
                            $htmlstr .= str_replace('[EXT_NAME]', $ext_name, $msg_div_e);
                            break;
                    }
                    $ext_content = str_replace('[EXT_CONTENT]', $htmlstr, $ext_header);
                    $html .= str_replace('[EXT_ID]', $ext_lic_id, $ext_content);
                }
            } else {
                $html .= '<p>' . __('An error occurred while getting installed extension information.', 'eventz-lite') . '</p>';
            }
        }
        $html .= '</div>';
        echo $html;
    }
    public function eventz_lite_endpoint_cb() {
        $request_link = '';
        $endpoint = $this->options['_endpoint'];
        switch ($endpoint) {
            case 'api.eventfinda.com.au':
                $this->eventfinda_link = 'www.eventfinda.com.au';
                break;
            case 'api.eventfinda.sg':
                $this->eventfinda_link = 'www.eventfinda.sg';
                break;
            case 'api.wohintipp.at':
                $this->eventfinda_link = 'www.wohintipp.at';
                break;
            case 'api.eventfinda.co.nz':
                $this->eventfinda_link = 'www.eventfinda.co.nz';
                break;
            default:
                $this->eventfinda_link = 'www.eventfinda.co.nz';
                break;
        }
        $array = array(
            1=>"api.eventfinda.co.nz",
            2=>"api.eventfinda.sg",
            3=>"api.eventfinda.com.au",
            4=> "api.wohintipp.at"
        );                
        echo '<select name="' . $this->option_name . '[_endpoint]" id="_endpoint">' . "\r\n";
        foreach($array as $key => $value) {
            if ($value === $endpoint) {
                echo "<option selected value='$value'>$value</option>" . "\r\n";
            } else {
                echo "<option value='$value'>$value</option>" . "\r\n";
            }
        }
        $username = $this->options['_username'];
        If (!$username) {
            $request_link = '<a name="_apilink" id="_apilink" href="http://www.eventfinda.co.nz/api/v2/index" target="_blank">' . __('Request Eventfinda API Key', 'eventz-lite') . '</a>';
        }
        echo '</select> ' . $request_link . "\r\n";
    }
    public function eventz_lite_username_cb() {
        $username = $this->options['_username'];
        echo '<input type="text" maxlength="40" data-rule-required="true" data-msg-required=" ' .
            __('Please enter your Eventfinda API user name', 'eventz-lite') . '." style="width:300px;" name="' . 
                $this->option_name . '[_username]" id="_username" value="' . $username . '" required>' . 
                    "\r\n";
        if (!$this->options['_endpoint']) {}
    }
    public function eventz_lite_password_cb() {
        $password = $this->options['_password'];
        echo '<input type="password" maxlength="30" data-rule-required="true" data-msg-required=" ' .
            __('Please enter your Eventfinda API password', 'eventz-lite') . '." style="width:300px;" name="' . $this->option_name . 
                '[_password]" id="_password" value="' . $password . '" required>' .
                        "\r\n";
    }
    public function eventz_lite_display_options_header_cb() { 
        echo __('Check the options below to enable or disable', 'eventz-lite') . ': ';
    }
    public function eventz_lite_display_options_cb() {
        $loc_checked = '';
        $date_checked = '';
        $cat_checked = '';
        $sep_checked = '';        
        $show_event_location = intval($this->options['_event_location']);
        $show_event_date = intval($this->options['_event_date']);
        $show_event_category = intval($this->options['_event_category']);
        $show_event_separator = intval($this->options['_event_separator']);
        if ($show_event_location === 1) {$loc_checked = 'checked';}
        if ($show_event_date === 1) {$date_checked = 'checked';}
        if ($show_event_category === 1) {$cat_checked = 'checked';}
        if ($show_event_separator === 1) {$sep_checked = 'checked';}
        
        $str =  '    <fieldset>' . 
                '        <legend class="screen-reader-text"><span>' . __('Listing Display Options', 'eventz-lite') . '</span></legend>' . 
                '        <label for="_event_location">' . 
                '        <input type="hidden" name="' . $this->option_name . '[_event_location]" id="_event_location_off" value="0">' . "\r\n" .
                '        <input type="checkbox" name="' . $this->option_name . '[_event_location]" id="_event_location" value="1" ' . $loc_checked . '>' . 
                '        ' . __('Event Location / Venue', 'eventz-lite') . '</label>' . 
                '        <br>' . 
                '        <label for="_event_date">' . 
                '        <input type="hidden" name="' . $this->option_name . '[_event_date]" id="_event_date_off" value="0">' . "\r\n" .
                '        <input type="checkbox" name="' . $this->option_name . '[_event_date]" id="_event_date" value="1" ' . $date_checked . '>' . 
                '        ' . __('Event Start Date', 'eventz-lite') . '</label>' . 
                '        <br>' . 
                '        <label for="_event_category">' . 
                '        <input type="hidden" name="' . $this->option_name . '[_event_category]" id="_event_category_off" value="0">' . "\r\n" .
                '        <input type="checkbox" name="' . $this->option_name . '[_event_category]" id="_event_category" value="1" ' . $cat_checked . '>' . 
                '        ' . __('Event Category', 'eventz-lite') . '</label>' . 
                '        <br>' . 
                '        <label for="_event_separator">' . 
                '        <input type="hidden" name="' . $this->option_name . '[_event_separator]" id="_event_separator_off" value="0">' . "\r\n" .
                '        <input type="checkbox" name="' . $this->option_name . '[_event_separator]" id="_event_separator" value="1" ' . $sep_checked . '>' . 
                '        ' . __('Event Separator', 'eventz-lite') . '</label>' . 
                '        <br>' . 
                '    </fieldset>';
        echo $str;
    }
    public function eventz_lite_excerpt_cb() {
        $str_options = '';
        $array = array(
            1=>"10",2=>"20",3=>"30",4=>"40",5=>"50",6=>"60",7=>"70",8=>"80",9=>"90",10=>"100",
            11=>"110",12=>"120",13=>"130",14=>"140",15=>"150",16=>"160",17=>"170",18=>"180",
            19=>"190",20=>"200",21=>"210",22=>"220"
        );
        $excerpt_length = intval($this->options['_event_excerpt']);
        foreach($array as $key => $value) {
            if (intval($value) === $excerpt_length) {
                $str_options .= "<option selected value='$value'>$value</option>" . "\r\n";
            } else {
                $str_options .=  "<option value='$value'>$value</option>" . "\r\n";
            }
        }
        $str =  '    <fieldset>' . 
                '        <select name="' . $this->option_name . '[_event_excerpt]" id="_event_excerpt">' . 
                '       ' . $str_options . 
                '        </select>' .
                '    </fieldset>';
        echo $str;
    }
    public function eventz_lite_results_pp_cb() {
        $results = intval($this->options['_results_pp']);
        $array = array(
            1=>"5",
            2=>"10",
            3=>"15",
            4=>"20"
        );  
        echo '<select name="' . $this->option_name . '[_results_pp]" id="_results_pp">' . "\r\n";
        foreach($array as $key => $value) {
            if (intval($value) === $results) {
                echo "<option selected value='$value'>$value</option>" . "\r\n";
            } else {
                echo "<option value='$value'>$value</option>" . "\r\n";
            }
        }
        echo '</select>' . "\r\n";
    }
    public function eventz_lite_debug_cb() {
        $checked = '';
        $debug = $this->options['_debug'];
        if (intval($debug) === 1) {$checked = 'checked="checked"';}
        echo '<input type="hidden" name="' . $this->option_name . '[_debug]" id="_debug_off" value="0">' . "\r\n" .
            '<input type="checkbox" name="' . $this->option_name . '[_debug]" id="_debug" value="1" ' . 
                $checked . '>' . "\r\n";
    }
    public function eventz_lite_debug_screen_cb() {
        $checked = '';
        $debug_screen = $this->options['_debug_screen'];
        if (intval($debug_screen) === 1) {$checked = 'checked';}
        echo '<input type="hidden" name="' . $this->option_name . '[_debug_screen]" id="_debug_screen_off" value="0">' . "\r\n" .
            '<input type="checkbox" name="' . $this->option_name . '[_debug_screen]" id="_debug_screen" value="1" ' . 
                $checked . '>' . "\r\n";
    }
    public function eventz_lite_delete_options_cb() {
        $checked = '';
        $delete_options = $this->options['_delete_options'];
        if (intval($delete_options) === 1) {$checked = 'checked';}
        echo '<input type="hidden" name="' . $this->option_name . '[_delete_options]" id="_delete_options_off" value="0">' . "\r\n" .
            '<input type="checkbox" name="' . $this->option_name . '[_delete_options]" id="_delete_options" value="1" ' . 
                $checked . '>' . "\r\n";
    }
    public function eventz_lite_eventfinda_branding_cb() {
        $logo_checked = '';
        $text_checked = '';
        $eventfinda_show_logo = intval($this->options['_eventfinda_logo']);
        $eventfinda_show_text = intval($this->options['_eventfinda_text']);
        $eventfinda_logo = '<a href="http://' . $this->eventfinda_link . '" title="' . __('Powered by Eventfinda', 'eventz-lite') . '" target="_blank">' . "\r\n" .
            '<img width="180" height="50" alt="Powered by Eventfinda" src="' . $this->eventz_lite_plugin_dir() . 'img/eventfinda.gif"></a>';
        $eventfinda_text = '<a href="http://' . $this->eventfinda_link . '" title="' . __('Powered by Eventfinda', 'eventz-lite') . '" target="_blank">' .
            __('Powered by Eventfinda', 'eventz-lite') . '</a>';
        if (intval($eventfinda_show_logo) === 1) {$logo_checked = 'checked';}
        if (intval($eventfinda_show_text) === 1) {$text_checked = 'checked';}
        echo '<input type="hidden" name="' . $this->option_name . '[_eventfinda_logo]" id="_eventfinda_logo_off" value="0">' . "\r\n" .
            '<input type="checkbox" name="' . $this->option_name . '[_eventfinda_logo]" id="_eventfinda_logo" value="1" ' . $logo_checked . '>' . "\r\n" . 
            $eventfinda_logo . 
            '<input type="hidden" name="' . $this->option_name . '[_eventfinda_text]" id="_eventfinda_text_off" value="0">' . "\r\n" .
            '<input type="checkbox" name="' . $this->option_name . '[_eventfinda_text]" id="_eventfinda_text" value="1" ' . $text_checked . '>' . "\r\n" . 
                $eventfinda_text . "\r\n";
    }
    public function eventz_lite_plugin_branding_cb() {
        $logo_checked = '';
        $text_checked = '';
        $show_plugin_logo = intval($this->options['_show_plugin_logo']);
        $show_plugin_link = intval($this->options['_show_plugin_link']);
        $plugin_logo = '<a href="http://plugin.onebyte.nz" title="' . __('Get the Plugin', 'eventz-lite') . '" target="_blank">' . "\r\n" .
            '<img width="180" height="50" alt="Eventfinda" src="' . $this->eventz_lite_plugin_dir() . 'img/eventz-lite.png"></a>';
        $plugin_link = '<small><a href="http://plugin.onebyte.nz" title="' . 
            __('Get the Plugin', 'eventz-lite') . '" target="_blank">' . 
                __('Get the Plugin', 'eventz-lite') . '</a></small>';
        if (intval($show_plugin_logo) === 1) {$logo_checked = 'checked';}
        if (intval($show_plugin_link) === 1) {$text_checked = 'checked';}
        echo '<input type="hidden" name="' . $this->option_name . '[_show_plugin_logo]" id="_show_plugin_logo_off" value="0">' . "\r\n" .
            '<input type="checkbox" name="' . $this->option_name . '[_show_plugin_logo]" id="_show_plugin_logo" value="1" ' . $logo_checked . '>' . "\r\n" . 
            $plugin_logo .
            '<input type="hidden" name="' . $this->option_name . '[_show_plugin_link]" id="_show_plugin_link_off" value="0">' . "\r\n" .
            '<input type="checkbox" name="' . $this->option_name . '[_show_plugin_link]" id="_show_plugin_link" value="1" ' . $text_checked . '>' . "\r\n" . 
                $plugin_link . "\r\n";
    }
    public function eventz_lite_show_plugin_link_cb() {
        $checked = '';
        $show_plugin_link = intval($this->options['_show_plugin_link']);
        $plugin_link = '<small><a href="http://plugin.onebyte.nz" title="' . __('Get the Plugin', 'eventz-lite') . '" target="_blank">Get Plugin</a></small>';
        if (intval($show_plugin_link) === 1) {$checked = 'checked';}
        echo '<input type="hidden" name="' . $this->option_name . '[_show_plugin_link]" id="_show_plugin_link_off" value="0">' . "\r\n" .
            '<input type="checkbox" name="' . $this->option_name . '[_show_plugin_link]" id="_show_plugin_link" value="1" ' . $checked . '>' . "\r\n" . 
                $plugin_link;
    }
    public function eventz_lite_plugin_dir () {
        return plugin_dir_url(__FILE__);
    }
    /* Ajax Functions */
    public function eventz_lite_check_user () {
        $user =  sanitize_text_field($_POST['username']);
        $pass =  sanitize_text_field($_POST['password']);
        $endpoint =  sanitize_text_field($_POST['endpoint']);
        if ($user == '') {return;}
        if ($pass == '') {return;}
        $url = 'http://' . $endpoint . '/v2/events.json?rows=1';
        $args = array(
            'timeout'     => 10,
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($user . ':' . $pass)
            )
        );
        $return = wp_safe_remote_get($url, $args);
        if (is_wp_error($return)){
            echo $return->get_error_message();
        } elseif (strpos(json_encode($return), 'error code') !== false) {
            echo __('Eventfinda API Login Failed', 'eventz-lite') . ': ' . 
                __('Please check your details and try again', 'eventz-lite') . '.<br/><br/>' . 
                    __('Eventfinda API says', 'eventz-lite') . ': ' . $return['response']['code'] . 
                        ': ' . $return['response']['message'];
        } else {
            echo 'true';
        }
        wp_die();
    }
}
