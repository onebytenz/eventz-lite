<?php
/**
 * The public-facing functionality of the plugin.
 * @link       http://onebyte.nz
 * @since      1.0.0
 * @package    Eventz Lite
 * @subpackage Eventz/public
 * @author     Craig Sugden - onebyte.nz <info@onebyte.nz>
 */
class Eventz_Lite_Public {
    private $dir;
    private $dir_url;
    private $pagenum;
    private $plugin_name;
    private $version;
    private $options;
    private $option_name = 'plugin_eventz_lite_options';
    private $username;
    private $password;
    private $eventfinda_logo;
    private $eventfinda_link;
    private $show_plugin_logo;
    private $show_plugin_link;
    private $default_location;
    private $plugin_endpoint;
    private $plugin_params;
    private $plugin_results_pp;
    
    public function __construct($plugin_name, $version) {
        $this->dir = dirname(__FILE__);
        $this->dir_url = plugin_dir_url( __FILE__ );
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->options = get_option($this->option_name);
        $this->username = $this->options['_username'];
        $this->password = $this->options['_password'];
        $this->plugin_endpoint = $this->options['_endpoint'];
        $this->eventfinda_logo = intval($this->options['_eventfinda_logo']);
        $this->show_plugin_logo = intval($this->options['_show_plugin_logo']);
        $this->show_plugin_link = intval($this->options['_show_plugin_link']);
        $this->plugin_results_pp = intval($this->options['_results_pp']);
        unset($this->options);
        $this->plugin_params = '';
        $this->pagenum = isset( $_GET['pageId'] ) ? absint( $_GET['pageId'] ) : 1;
        $this->plugin_search_query = isset($_GET['q']) ? $_GET['q'] : '';
    }
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name . '-min', plugin_dir_url( __FILE__ ) . 'css/eventz.min.css', array(), $this->version, 'all' );
    }
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name . '-min', plugin_dir_url( __FILE__ ) . 'js/eventz.min.js', array( 'jquery' ), $this->version, false );
    }
    public function eventz_lite_register_shortcodes(){
        add_shortcode('eventz-lite', array($this, 'eventz_lite_shortcode'));
    }
    public function eventz_lite_shortcode($atts){
        $dateformat = 'Y-m-d';
        $datenow = date($dateformat);
        extract(shortcode_atts(array(
            'params' => ''
        ), $atts ) );
        $sc_params = $params;
        $this->set_default_location();
        if ($sc_params !== '') {$this->plugin_params = '&' . htmlspecialchars_decode($sc_params);}
        if ($this->pagenum == 1) {
            $offset = '&offset=0';
        } else {
            $f = $this->pagenum * $this->plugin_results_pp - intval($this->plugin_results_pp);
            $offset = '&offset=' . $f;
        }
        $url = 'http://' . $this->plugin_endpoint . '/v2/events.json?rows=' . $this->plugin_results_pp . $offset . 
            '&start_date=' . $datenow . $this->plugin_params .
                '&fields=event:(id,name,category,location_summary,datetime_summary,description,url,images)';
        /*echo '<!--URL:' . $url . '-->';*/
        $eventz_http = new Eventz_Lite_HTTP($this->username,$this->password);
        $response = $eventz_http->get_api_data($url);
        $result = $this->get_page_body($response);
        unset($eventz_http);
        return $result;
    }
    private function get_page_body($response) {
        $htmlstr = '';
        $result_str = '';
        $plugin_dir = $this->dir_url;
        $script = '<script>var notfound="' . $plugin_dir . 'img/notfound.png";</script>';
        $event_footer = $this->get_page_footer();
        $collection = json_decode($response);
        $count = intval($collection->{'@attributes'}->count);
        if ($count === 0) {
            $result_str = 'No events found.';
            $page_links_html = '';
            return '<div class="events">' . $result_str . '</div>' . $event_footer;
        } else {
            $page_links_html = $this->get_page_links($count);
            foreach ($collection->events as $event) {
                $htmlstr .= $this->get_event_html($event);
            }
        }
        $result = $script . '<div id="events" class="events">' . $result_str . 
            $htmlstr . $page_links_html . '</div>' . $event_footer;
        return $result;
    }
    private function get_page_links ($count) {
        $result = '';
        $page_links = '';
        $limit = intval($this->plugin_results_pp);
        if ($count > $limit) {
            $num_of_pages = ceil($count / $limit);
            $page_links   = paginate_links( array(
                'base'      => add_query_arg( 'pageId', '%#%' ),
                'format'    => '',
                'prev_next' => true,
                'prev_text' => __( '&laquo; Prev', 'Previous' ),
                'next_text' => __( 'Next &raquo;', 'Next' ),
                'total'     => $num_of_pages,
                'current'   => $this->pagenum,
                'add_fragment' => '#events'
            ));
            $template_file = '/partials/page-links.tpl';
            $template = new Eventz_Lite_Template($this->dir . $template_file);
            $template->set('PAGE_LINKS', $page_links);
            $result = $template->output();
            unset($template);
        }
        return $result;
    }
    private function get_page_footer () {
        $eventfinda_branding = $this->eventfinda_branding();
        $plugin_branding = $this->plugin_branding();     
        $template_file = '/partials/footer-branding.tpl';
        $template = new Eventz_Lite_Template($this->dir . $template_file);
        $template->set('EVENTFINDA', $eventfinda_branding);
        $template->set('PLUGIN', $plugin_branding);
        return $template->output();
    }
    private function get_event_html ($event) {
        $id = $event->id;
        $name = $event->name;
        $location_summary = $event->location_summary;
        $location = str_replace('Great Barrier Island, Great Barrier Island,','Great Barrier Island,',$location_summary);
        $category = $event->category->name;
        //$timezone = $event->timezone;
        $datetime_summary = $event->datetime_summary;
        $description = $event->description;
        $url = $event->url;
        $template_file = '/partials/events-list.tpl';
        $template = new Eventz_Lite_Template($this->dir . $template_file);          
        $template->set("IMGID", $id);
        $template->set("LINK", $url);
        $template->set("NAME", $name);
        $template->set("LOCATION", $location);
        $template->set("TIME", $datetime_summary);
        $template->set("CATEGORY", $category);
        $template->set("DESCRIPTION", $description);
        foreach ($event->images->images as $image) {
            if ($image->is_primary){
                $template->set("IMG", $this->get_event_image($image));
            }			
        }
        $result = $template->output();
        unset ($template);
        return $result;
    }
    private function get_event_image ($image) {
        foreach ($image->transforms->transforms as $transform) {
            $img = $transform->url;
            if (strpos($img, '-8.jpg') > 0 || strpos($img, '-8.png') > 0 || strpos($img, '-8.gif') > 0) {
                if (strpos($img, 'http:') === false) {
                    $img = str_replace('//cdn','http://cdn',$img);
                }
                return $img;
            }
        }
    }
    private function eventfinda_branding () {
         switch ($this->eventfinda_logo) {
            case 0:
                $template_file = '/partials/eventfinda-link.tpl';
                break;
            case 1:
                $template_file = '/partials/eventfinda-logo.tpl';
                break;
            default:
                $template_file = '/partials/eventfinda-logo.tpl';
                break;
        }
        $ef_template = new Eventz_Lite_Template($this->dir . $template_file);
        $ef_template->set('LINK', $this->eventfinda_link);
        $ef_template->set('PLUGIN_IMG_URL', plugin_dir_url( __FILE__ ));
        $result = $ef_template->output();
        unset($ef_template);
        return $result;
    }
    private function plugin_branding () {
        $plugin_logo = $this->show_plugin_logo;
        $plugin_link = $this->show_plugin_link;
        switch ($plugin_logo) {
            case 0;
                break;
            case 1;
                $template_file = '/partials/plugin-logo.tpl';
                break;
            default;
                break;
        }
        switch ($plugin_link) {
            case 0;
                break;
            case 1;
                $template_file = '/partials/plugin-link.tpl';
                break;
            default;
                break;
        }
        if ($this->show_plugin_logo || $this->show_plugin_link) {
            $template = new Eventz_Lite_Template($this->dir . $template_file);
            $template->set('PLUGIN_IMG_URL', plugin_dir_url( __FILE__ ));
            $result = $template->output();
            unset($template);
        } else {
            $result = '';
        }
        return $result;
    }
    private function set_default_location () {
        switch ($this->plugin_endpoint) {
            case 'api.eventfinda.com.au':
                $this->eventfinda_link = 'www.eventfinda.com.au';
                $this->default_location = 'Australia';
                break;
            case 'api.eventfinda.sg':
                $this->eventfinda_link = 'www.eventfinda.sg';
                $this->default_location = 'Singapore';
                break;
            case 'api.wohintipp.at':
                $this->eventfinda_link = 'www.wohintipp.at';
                $this->default_location = 'Österreich';
                break;
            default:
                $this->eventfinda_link = 'www.eventfinda.co.nz';
                $this->default_location = 'New Zealand';
                break;
        }
    }
}
