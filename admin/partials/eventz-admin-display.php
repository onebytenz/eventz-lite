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
    </h2>
    <div id='sections'>    
        <section>
            <form action="options.php" id="eventfindaOptions" method="post">
                <?php
                    /*settings_fields($this->plugin_name);*/
                    settings_fields('eventz-lite');
                    do_settings_sections($this->plugin_name . '_general');
                    submit_button();
                ?>
                <div id="login-success" title="Login Successful"></div>
                <div id="login-fail" title="Login Failed"></div>
                <div id="delete-confirm" title="Delete All Settings?"></div>
            </form>
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
                            jQuery('#login-success').html('');
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
            /*since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php*/
            jQuery.post(ajaxurl, data, function(response) {
                if (response !== 'true') {
                    checkrun = false;
                    verified = false;
                    jQuery('#login-fail').html('<p class="error" style="text-align:center;">' + response + '</p>');
                    jQuery("#login-fail").dialog({
                        resizable: false,
                        height:195,
                        modal: true
                    });
                } else {
                    verified = true;
                    if (verified && !checkrun) {
                        checkrun = true;
                        ajaxSubmit = true;
                        jQuery('#login-success').html('<p class="success">Eventfinda API Login Successful.<br/>Saving...</p>');
                        jQuery("#login-success").dialog({
                            resizable: false,
                            height:125,
                            modal: true
                        });
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
