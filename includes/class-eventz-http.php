<?php
/**
 * HTTP Class.
 * @link       http://onebyte.nz
 * This class defines all code necessary to run during the plugin's deactivation.
 * @since      1.0.0
 * @package    Eventz Lite
 * @subpackage Eventz/includes
 * @author     Craig Sugden - onebyte.nz <info@onebyte.nz>
 */
class Eventz_Lite_HTTP {
    private $username;
    private $password;
    private $caller;
    private $logging = false;

    public function __construct($user, $pass, $caller) {
        global $eventz_lite_options;
        if ($eventz_lite_options['_debug']) {
            $this->logging = true;
        }
        unset($eventz_lite_options);
        $this->username = $user;
        $this->password = $pass;
        $this->caller = $caller;
    }
    public function get_api_data($url) {
        $data = $this->get_http_data($url);
        return $data;
    }
    private function get_http_data ($url) {
        $user = $this->username;
        $pass = $this->password;
        $args = array(
            'timeout' => 10,
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($user . ':' . $pass)
            )
        );
        if ($this->logging) {
            $d = new Eventz_Lite_Debug (true, false, $this->caller . 'API Request: ' . $url);
            unset($d);
        }
        $response = wp_safe_remote_get($url, $args);
        if (is_wp_error($response)){
            /* Remote Get Errors will be logged by calling class method */
            $data = '<!--eventz-error-->' . htmlentities($response->get_error_message());
        } else {
            $data = wp_remote_retrieve_body($response);
        }
        return $data; 
    }
}

