<?php
/**
 * @link       http://onebyte.nz
 * @since      1.0.0
 * @package    Eventfinder Lite
 * @subpackage eventfinder/admin/partials
 * @author     Craig Sugden - onebyte.nz <info@onebyte.nz>
 */
?>
<div class="wrap">
    <h2><?php echo esc_html(get_admin_page_title());?></h2>
    <h2 id="tabs" class="nav-tab-wrapper">
      <a class="nav-tab nav-tab-active" href="#">General Setup</a>
      <a class="nav-tab" href="#">Display Options</a>
      <a class="nav-tab" href="#">Miscellaneous</a>
      <a class="nav-tab" href="#">Shortcode Guide</a>
    </h2>
    <div id='sections'>
        <form action="options.php" id="eventfindaOptions" method="post">
        <section>
                <?php
                    settings_fields('eventz-lite');
                    do_settings_sections($this->plugin_name . '_general');
                ?>
                <div id="info"></div>
        </section>
        <section>
            <?php
               settings_fields('eventz-lite');
               do_settings_sections($this->plugin_name . '_display');
               echo '<br/>';
               submit_button('Update', 'primary',  'eventfindaOptions', false);
           ?>
        </section>
        <section>
            <?php
               settings_fields('eventz-lite');
               do_settings_sections($this->plugin_name . '_misc');
            ?>
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row"><label>Review &amp; Rate?</label></th>
                    <td>If you like this plugin please <a target="_blank" href="https://wordpress.org/support/plugin/eventz-lite/reviews/">review &amp; rate it.</a></td>
                </tr>
                </tbody>
            </table>
            <?php
               submit_button('Update', 'primary',  'eventfindaOptions', false);
            ?>
        </section>
        </form>
        <section>
            <p><em><strong>How do I use the shortcode?</strong></em><br>
            Place the shortcode <strong>[eventz-lite]</strong> in any page or post.</p>
            <p><em><strong>How do I refine the result sets?</strong></em><br>
            Visit <a href="http://www.eventfinda.co.nz/" target="_blank">Eventfinda</a> and navigate to the “Parameters” section to see the available query parameters that can be used to retrieve the result sets.</p>
            <p><em><strong>I have events listed with Eventfinda, how do I display just those listings?</strong></em><br>
            <strong>[eventz-lite params=”username=myeventfindausername”]</strong></p>
            <p><strong>The Location Slug:</strong><br>
            To find your location slug just visit <a href="http://www.eventfinda.co.nz/" target="_blank">Eventfinda </a>and navigate to the location you would like to display listings for.<br>
            The location slug is the last string in the url:</p>
            <p>http://www.eventfinda.co.nz/whatson/events/<a href="http://www.eventfinda.co.nz/whatson/events/auckland-central" target="_blank">auckland-central</a></p>
            <p>The location slug for the url above is “auckland-central”.<br>
            <strong>[eventz-lite params=”location_slug=auckland-central”]</strong><br><br>
            The location slug can also be used for venues.<br>
            Visit Eventfinda and search for your venue, example “Henderson RSA”.<br>
            Events for this venue will be displayed with a link to the venue, for the above example the link is:<br>
            https://www.eventfinda.co.nz/venue/henderson-rsa-auckland-west<br>
            The location slug to enter for the above example is “henderson-rsa-auckland-west”.<br>
            <strong>[eventz-lite params=”location_slug=henderson-rsa-auckland-west”]</strong></p>
            <p><strong>The Category Slug:</strong><br>
            To find the category slugs for your country visit your local Eventfinda site and click “Find Events” at the top of the page.<br>
            When the “Upcoming Events” page has loaded you will see the event categories listed underneath the locations.<br>
            Click the category you would like to display and get the category slug from the url:</p>
            <p>http://www.eventfinda.co.nz/<a href="http://www.eventfinda.co.nz/concerts-gig-guide/events/new-zealand" target="_blank">concerts-gig-guide</a>/events/new-zealand</p>
            <p>The category slug for the url above is “concerts-gig-guide”.<br>
            <strong>[eventz-lite params=”category_slug=concerts-gig-guide”]</strong></p>
            <p><strong>Example Shortcodes:</strong></p>
            <p>Auckland Events:<strong><br>
            [eventz-lite params=”location_slug=auckland”]</strong><br>
            Auckland Gig Guide:<strong><br>
            [eventz-lite params=”location_slug=auckland&amp;category_slug=concerts-gig-guide”]</strong></p>
            <p>The paramaters for “rows” &amp; “offset” are taken care of by the plugin (results per page in the admin setup).</p>
            <p>More information on querying the <a href="http://www.eventfinda.co.nz/api/v2/events" target="_blank">Eventfinda API</a><br>
            <a href="https://plugin.onebyte.nz/eventz-lite/docs/" target="_blank">https://plugin.onebyte.nz/eventz-lite/docs/</a></p>
        </section>
    </div>
</div>
<script type="text/javascript">
    "use strict";
    jQuery.validator.setDefaults({
        debug: false
    });
    jQuery(document).ready(function() {
        "use strict";
        var checkrun = false;
        var verified = false;
        var ajaxSubmit = false;
        var info_loading = '<span id="info_loading" class="spinner" style="visibility:visible;float:none;"></span>';
        var validator = jQuery("#eventfindaOptions").validate({
            errorElement: "span",
            submitHandler: function() {
                ajaxSubmit = false;
                if (!ajaxSubmit) {
                    jQuery("#submit").attr("disabled", true);
                    ajaxSubmit = false;
                    HTMLFormElement.prototype.submit.call(jQuery('#eventfindaOptions')[0]);
                } else {
                    jQuery("#eventfindaOptions").ajaxSubmit({
                        success: function(){
                            jQuery('#info').html('');
                        }, 
                        timeout: 5000
                    });
                    ajaxSubmit = false;
                    return false;
                }
            }
        });
        jQuery(function() {
            var offsetX = 25;
            var offsetY = -35;
            var TooltipOpacity = 0.9;
            jQuery('[title]').mouseenter(function(e) {
            var Tooltip = jQuery(this).attr('title');
            if(Tooltip !== '') {
                if (Tooltip.indexOf('Delete') === -1 || Tooltip.indexOf('Login') === -1) {
                    jQuery(this).attr('customTooltip',Tooltip);
                    jQuery(this).attr('title','');
                }
            }
            var customTooltip = jQuery(this).attr('customTooltip');
            if(customTooltip !== '') {
                jQuery("body").append('<div id="tooltip">' + customTooltip + '</div>');
                jQuery('#tooltip').css('left', e.pageX + offsetX );
                jQuery('#tooltip').css('top', e.pageY + offsetY );
                jQuery('#tooltip').fadeIn('500');
                jQuery('#tooltip').fadeTo('10',TooltipOpacity);
            }
            }).mousemove(function(e) {
                var X = e.pageX;
                var Y = e.pageY;
                jQuery('#tooltip').css('left', X + offsetX );
                jQuery('#tooltip').css('top', Y + offsetY );
            }).mouseleave(function() {
                jQuery("body").children('div#tooltip').remove();
            });
        });
        jQuery(document).on( 'click', '.nav-tab-wrapper a', function() {
		jQuery('section').hide();
		jQuery('section').eq(jQuery(this).index()).show();
                jQuery('.nav-tab-wrapper a').removeClass('nav-tab-active');
                jQuery('.nav-tab-wrapper a').eq(jQuery(this).index()).addClass('nav-tab-active');
		return false;
        });
        jQuery('#_username').on('change', function () {
            verified = false;
            jQuery(this.form).valid();
            if (this.value && jQuery('#_password').val()) {
                jQuery(this).chkuser();
            }
        });
        jQuery('#_password').on('change', function () {
            verified = false;
            jQuery(this.form).valid();
            if (this.value && jQuery('#_username').val()) {
                jQuery(this).chkuser();
            }           
        });
        jQuery('#_endpoint').on('change', function () {
            verified = false;
            jQuery(this.form).valid();
            if (jQuery('#_username').val() && jQuery('#_password').val()) {
                jQuery(this).chkuser();
            } else {
                jQuery('#_apilink').attr('href', 'http://' + jQuery('#_endpoint').val() + '/api/v2/index');
            }
        });
        jQuery('body').on('change','#_delete_options',function(){
            if (jQuery(this).is(':checked')) {
                var str = '<p style="text-align:center;">' +
                    'Checking this box will delete all of the plugins settings from the database ' +
                    'if the plugin is uninstalled. This action cannot be undone.' +
                    '</p>';
                jQuery("#delete-confirm").html(str);
                jQuery("#delete-confirm").dialog({
                    resizable: false,
                    height:225,
                    modal: true,
                    buttons: {
                        "Continue": function() {
                            jQuery(this).dialog('close');
                        },
                        Cancel: function() {
                            jQuery('form #_delete_options').attr('checked',false);
                            jQuery(this).dialog('close');
                        }
                    }
                });
            }
        });
        jQuery('body').on('change','#_eventfinda_logo',function(){
            if (jQuery('form #_eventfinda_text').is(':checked')) {
                jQuery('form #_eventfinda_text').attr('checked',false);
            } else {
                jQuery('form #_eventfinda_logo').attr('checked',true);
            }
        });
        jQuery('body').on('change','#_eventfinda_text',function(){
            if (jQuery('form #_eventfinda_logo').is(':checked')) {
                jQuery('form #_eventfinda_logo').attr('checked',false);
            } else {
                jQuery('form #_eventfinda_text').attr('checked',true);
            }
        });
        jQuery('body').on('change','#_show_plugin_logo',function(){
            if (jQuery('form #_show_plugin_link').is(':checked')) {
                jQuery('form #_show_plugin_link').attr('checked',false);
            }
        });
        jQuery('body').on('change','#_show_plugin_link',function(){
            if (jQuery('form #_show_plugin_logo').is(':checked')) {
                jQuery('form #_show_plugin_logo').attr('checked',false);
            }
        });
        /* Validate User Credentials */
        jQuery.fn.chkuser = function () {
            var _username = jQuery('#_username').val();
            var _password = jQuery('#_password').val();
            var _endpoint = jQuery('#_endpoint').val();
            var data = {
                'action': 'check_user',
                'username': _username,
                'password': _password,
                'endpoint': _endpoint
            };
            jQuery('#info').attr('title', 'API Login');
            jQuery('#info').html('<p style="text-align:center;">Logging in to Eventfinda<br/>Please Wait...<br/>' + info_loading + '</p>');
            jQuery("#info").dialog({
                resizable: false,
                height:150,
                modal: true
            });
            /*since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php*/
            jQuery.post(ajaxurl, data, function(response) {
                if (response !== 'true') {
                    checkrun = false;
                    verified = false;
                    jQuery('#info').hide();
                    jQuery('#info').attr('title', 'An Error Occurred');
                    jQuery('#info').html('<p class="error" style="text-align:center;">' + response + '</p>');
                    jQuery("#info").dialog({
                        resizable: false,
                        height:195,
                        modal: true
                    });
                } else {
                    verified = true;
                    if (verified && !checkrun) {
                        checkrun = true;
                        ajaxSubmit = true;
                        jQuery('#info').attr('title', 'Success');
                        jQuery('#info').html('<p class="success">Eventfinda API Login Successful.<br/>Saving...<br/>' + info_loading + '</p>');
                        jQuery('#eventfindaOptions').submit();
                    }
                }
            });
        };
        jQuery(this.form).keypress(function(e) {
            /*Enter key*/
            if (e.which === 13) {
                return false;
            }
        });
    });
</script>
