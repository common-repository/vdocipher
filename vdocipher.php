<?php
/**
 * Plugin Name: VdoCipher
 * Plugin URI: https://www.vdocipher.com
 * Description: Secured video hosting for WordPress
 * Version: 1.29
 * Author: VdoCipher
 * Author URI: https://www.vdocipher.com
 * License: GPL2
 */

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    exit("<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\r\n<html lang='en'><head>\r\n<title>404 Not Found</title>\r\n".
        "</head><body>\r\n<h1>Not Found</h1>\r\n<p>The requested URL " . $_SERVER['SCRIPT_NAME'] . " was not found on ".
        "this server.</p>\r\n</body></html>");
}

if (!defined('VDOCIPHER_PLUGIN_VERSION')) {
    define('VDOCIPHER_PLUGIN_VERSION', '1.29');
}

if (!defined('VDOCIPHER_PLAYER_VERSION')) {
    define('VDOCIPHER_PLAYER_VERSION', 'v2');
}

if (!defined('VDOCIPHER_DEFAULT_THEME')) {
    define('VDOCIPHER_DEFAULT_THEME', '');
}

function vdo_plugin_check_version()
{
    // This applies only for installs 1.24 and below
    if (!get_option('vdo_plugin_version')) {
        if (preg_match('/^1\.[0123456]\.[0-9]{1,2}$/', get_option('vdo_embed_version'))) {
            update_option('vdo_embed_version', VDOCIPHER_PLAYER_VERSION);
        }
        if (preg_match('/^1\.[01234]\.[0-9]{1,2}$/', get_option('vdo_embed_version'))) {
            update_option('vdo_default_height', 'auto');
        }
        update_option('vdo_plugin_version', VDOCIPHER_PLUGIN_VERSION);
        return ;
    }
    // This applies for all new installations after 1.25
    if (VDOCIPHER_PLUGIN_VERSION !== get_option('vdo_plugin_version')) {
        if (preg_match('/^1\.[0-9]{1,2}\.[0-9]{1,2}$/', get_option('vdo_embed_version'))) {
            update_option('vdo_embed_version', VDOCIPHER_PLAYER_VERSION);
        }
        update_option('vdo_plugin_version', VDOCIPHER_PLUGIN_VERSION);
    }
}

add_action('plugins_loaded', 'vdo_plugin_check_version');

// Function called to get OTP, starts
function vdo_otp($video, $otp_post_array = array())
{
    $client_key = get_option('vdo_client_key');
    if ($client_key == false || $client_key == "") {
        return (object)['message' =>  'Plugin not configured. API Key missing.'];
    } else if (strlen($client_key) !== 64) {
        return (object)["message" => "Invalid API Key."];
    }
    $url = "https://dev.vdocipher.com/api/videos/$video/otp";
    $headers = array(
        'Authorization'=>'Apisecret '.$client_key,
        'Content-Type'=>'application/json',
        'Accept'=>'application/json'
    );
    $otp_post_json = json_encode($otp_post_array);
    $response = wp_remote_post(
        $url,
        array(
            'method'    =>  'POST',
            'headers'   =>  $headers,
            'body'      =>  $otp_post_json
        )
    );
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        if (false !== stripos($error_message, 'Operation timed out')) {
          $error_message = 'Operation timed out. Check your firewall at web host or try after sometime.';
        } else if (false !== stripos($error_message, 'Could not resolve host')) {
          $error_message = 'Connectivity issue. Check firewall at web host.';
        }
        return ["message" => $error_message];
    }

    $responseCode = $response['response']['code'];
    if ($responseCode === 404) {
        return (object)["message" => "Video ID not found"];
    }
    else if ($responseCode === 403) {
        return (object)["message" => "API key does not have OTP creator permission"];
    }
    else if ($responseCode === 400 || $responseCode === 401) {
        return (object)["message" => "Incorrect API key"];
    } else {
        return json_decode($response['body']);
    }
}
// Function called to get OTP, ends

// VdoCipher Shortcode starts
function vdo_shortcode($atts)
{
    $vdo_args = shortcode_atts(
        array(
            'width' => get_option('vdo_default_width'),
            'height' => get_option('vdo_default_height'),
            'id'    => 'id',
            'no_annotate'=> false,
            'vdo_theme'=> false,
            'autoplay'=> false,
            'loop'=> false,
            'controls'=> 'on',
            'cc_language'=> false,
            'litemode'=> false,
        ),
        $atts
    );
    $width = $vdo_args['width'];
    $height = $vdo_args['height'];
    $id = $vdo_args['id'];
    $no_annotate = $vdo_args['no_annotate'];
    $vdo_theme = $vdo_args['vdo_theme'];

    if (!preg_match('/.*px$/', $width)) {
        $width = $width."px";
    }
    if (!preg_match('/.*px$/', $height)) {
        if ($height != 'auto') {
            $height = $height."px";
        }
    }
    if (!$atts['id']) {
        return "Required argument id for embedded video not found.";
    } else {
        $video = $id;
    }

    // Initialize $otp_post_array, to be sent as part of OTP request, as for time-to-live 300
    $otp_post_array = array("ttl" => 300);
    if (!function_exists("eval_date")) {
        function eval_date($matches)
        {
            return current_time($matches[1]);
        }
    }
    if (get_option('vdo_annotate_code') != "") {
        $current_user = wp_get_current_user();
        $vdo_annotate_code = get_option('vdo_annotate_code');
        $vdo_annotate_code = apply_filters('vdocipher_annotate_preprocess', $vdo_annotate_code);
        if (is_user_logged_in()) {
            $vdo_annotate_code = str_replace('{name}', $current_user->display_name, $vdo_annotate_code);
            $vdo_annotate_code = str_replace('{email}', $current_user->user_email, $vdo_annotate_code);
            $vdo_annotate_code = str_replace('{username}', $current_user->user_login, $vdo_annotate_code);
            $vdo_annotate_code = str_replace('{id}', $current_user->ID, $vdo_annotate_code);
        }
        $vdo_annotate_code = str_replace('{ip}', $_SERVER['REMOTE_ADDR'], $vdo_annotate_code);
        $vdo_annotate_code = preg_replace_callback('/{date\.([^}]+)}/', "eval_date", $vdo_annotate_code);
        $vdo_annotate_code = apply_filters('vdocipher_annotate_postprocess', $vdo_annotate_code);
        // Add annotate code to $otp_post_array, which will be
        // converted to Json and then sent as POST body to API endpoint
        if (!$no_annotate) {
            $otp_post_array["annotate"] = $vdo_annotate_code;
        }
    }
    // OTP is requested via vdo_otp function
    $OTP_Response = vdo_otp($video, $otp_post_array);

    // https://www.php.net/manual/en/function.isset.php#86313
    $message = (isset($OTP_Response->message)) ? $OTP_Response->message : null;
    $OTP = (isset($OTP_Response->otp)) ? $OTP_Response->otp : null;
    $playbackInfo = (isset($OTP_Response->playbackInfo)) ? $OTP_Response->playbackInfo : null;

    if (is_null($OTP)) {
        return "<div id='vdo$OTP'><strong>VdoCipher Error: $message</strong></div>";
    }

    // Version, legacy, for flash only
    $version = 0;
    if (isset($atts['version'])) {
        $version = $atts['version'];
    }

    // Video Embed version is retrieved from options table or from shortcode attribute
    $vdo_embed_version_str = get_option('vdo_embed_version');

    // Video Player theme, update and as shortcode attribute
    if (!$vdo_theme) {
        $vdo_player_theme = get_option('vdo_player_theme');
    } else {
        $vdo_player_theme = $vdo_theme;
    }
    if (strpos($vdo_player_theme, 'v1/') === 0 || strlen($vdo_player_theme) === 32) {
        $vdo_embed_version_str = '1.6.10';
    } else if (strlen($vdo_player_theme) === 16) {
        $vdo_embed_version_str = 'v2';
    }
    $speedOptions = esc_attr(get_option('vdo_player_speed'));
    $speedPattern = '/^\d.\d{1,2}(,\d.\d{1,2})+$/';

    // Old Embed Code
    if ($vdo_embed_version_str === '1.6.10') {
        //Old embed code
        $output = <<<END
        <div id='vdo$OTP' style='height:$height;width:$width;max-width:100%' ></div>
        <script>(function(v,i,d,e,o){v[o]=v[o]||{}; v[o].add = v[o].add || function V(a){
        (v[o].d=v[o].d||[]).push(a);};
        if(!v[o].l) { v[o].l=1*new Date(); a=i.createElement(d), m=i.getElementsByTagName(d)[0];
        a.async=1; a.src=e; m.parentNode.insertBefore(a,m);}
        })(window,document,'script','https://d1z78r8i505acl.cloudfront.net/playerAssets/1.6.10/vdo.js','vdo');
        vdo.add({
            otp: '$OTP',
            playbackInfo: '$playbackInfo',
            theme: '$vdo_player_theme',
            plugins: [{
                name: 'keyboard',
                options: {
                    preset: 'default',
                    bindings: {
                        'Left' : (player) => player.seek(player.currentTime - 15),
                        'Right' : (player) => player.seek(player.currentTime + 15),
                    },
                }
            }],
            container: document.querySelector('#vdo$OTP'),
        })
        </script>
END;

        if ($speedOptions !== false && preg_match($speedPattern, $speedOptions)) {
            $output .= <<<END
                         <script>
                         (function () {
                             var originalReadyFunction = window.onVdoCipherAPIReady;
                             // private API; do not use anywhere else; might change without notice
                             var index = vdo.d.length - 1;
                             window.onVdoCipherAPIReady = () => {
                                 if (originalReadyFunction) originalReadyFunction();
                                 var v_ = vdo.getObjects()[index];
                                 v_.addEventListener('load', () => {
                                     v_.availablePlaybackRates = [$speedOptions]
                                 });
                             }
                         })()
                         </script>
END;
        }
    } else if ($vdo_embed_version_str === 'v2') {
        $uniq = 'u' . rand();
        $url = "https://player.vdocipher.com/v2/?otp=$OTP&playbackInfo=$playbackInfo";
        if (strlen($vdo_player_theme) === 16) {
            $url .= "&player=$vdo_player_theme";
        }
        if ($vdo_args['autoplay']) {
            $url .= "&autoplay=true";
        }
        if ($vdo_args['loop']) {
            $url .= "&loop=true";
        }
        if (in_array($vdo_args['controls'], ['off', 'native'])) {
            $url .= "&controls=" . $vdo_args['controls'];
        }
        if ($vdo_args['cc_language']) {
            $url .= "&ccLanguage=" . $vdo_args['cc_language'];
        }
        if ($vdo_args['litemode'] === 'true') {
            $url .= "&litemode=true";
        }
        $output = <<<END
<script src="https://player.vdocipher.com/v2/api.js"></script>
<iframe
  src="$url"
  id="$uniq"
  style="height:$height;width:$width;max-width:100%;border:0;display: block;"
  allow="encrypted-media"
  allowfullscreen
></iframe>
<script>
(function() {
  const iframe = document.querySelector('#$uniq');
  const player = VdoPlayer.getInstance(iframe);
  const isAutoHeight = () => iframe.style.height === 'auto' && iframe.style.width.endsWith('px');
  const setAspectRatio = (ratio) => {
      iframe.style.maxHeight = '100vh';
      if (CSS.supports('aspect-ratio', 1)) {
          iframe.style.aspectRatio = ratio;
      } else {
          const offsetWidth = iframe.offsetWidth;
          iframe.style.height = Math.round(offsetWidth / ratio) + 'px';
      }
  }
  if (isAutoHeight()) {
    if (iframe.src.includes('litemode'))  {
       setAspectRatio(16/9);
    }
    player.video.addEventListener('loadstart', async () => {
      const aspectRatio = (await player.api.getMetaData()).aspectRatio;
      setAspectRatio(aspectRatio);
    });
  }
})();
</script>
END;
        if ($speedOptions !== false && preg_match($speedPattern, $speedOptions)) {
            $output .= <<<END
<script>
(function() {
    const iframe = document.querySelector('#$uniq')
    const player = VdoPlayer.getInstance(iframe);
    player.video.addEventListener('loadstart', async () => {
      player.api.updatePlayerConfig({playbackSpeedOptions: [$speedOptions]});
    });
})();
</script>
END;
        }

    } else {
        $output = 'Invalid player selection: ' . $vdo_embed_version_str;
    }
    return $output;
}
add_shortcode('vdo', 'vdo_shortcode');
// VdoCipher Shortcode ends

// adding the Settings link, starts
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'vdo_settings_link');

function vdo_settings_link($links)
{
    $settings_link = '<a href="options-general.php?page=vdocipher">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
// adding the Settings link, ends

// add the menu item and register settings (3 functions), starts
if (is_admin()) { // admin actions
    add_action('admin_init', 'register_vdo_settings');
    add_action('admin_menu', 'vdo_menu');
}
function vdo_menu()
{
  if (get_option('vdo_show_plugin_in_sidebar') === "") {
    add_options_page(
        'VdoCipher Options',
        'VdoCipher',
        'manage_options',
        'vdocipher',
        'vdo_options'
    );
  } else {
    add_menu_page(
        'VdoCipher Options',
        'VdoCipher',
        'manage_options',
        'vdocipher',
        'vdo_options',
        plugin_dir_url(__FILE__).'images/logo.png'
    );
  }
}

function vdo_options()
{
    if (!get_option('vdo_default_height')) {
        update_option('vdo_default_height', 'auto');
    }
    if (!get_option('vdo_default_width')) {
        update_option('vdo_default_width', '1280');
    }
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    include('include/options.php');
    return "";
}

function register_vdo_settings()
{
 // whitelist options
    register_setting('vdo_option-group', 'vdo_client_key');
    register_setting('vdo_option-group', 'vdo_default_height');
    register_setting('vdo_option-group', 'vdo_default_width');
    register_setting('vdo_option-group', 'vdo_annotate_code');
    register_setting('vdo_option-group', 'vdo_embed_version');
    register_setting('vdo_option-group', 'vdo_player_theme');
    register_setting('vdo_option-group', 'vdo_plugin_version');
    register_setting('vdo_option-group', 'vdo_player_speed');
    register_setting('vdo_option-group', 'vdo_show_plugin_in_sidebar');
}
// add the menu item and register settings (3 functions), ends

// Activation Hook starts
function vdo_activate()
{
    add_option('vdo_default_height', 'auto');
    add_option('vdo_default_width', 1280);
    add_option('vdo_embed_version', VDOCIPHER_PLAYER_VERSION);
    add_option('vdo_player_theme', VDOCIPHER_DEFAULT_THEME);
    add_option('vdo_show_plugin_in_sidebar', 'true');
}
register_activation_hook(__FILE__, 'vdo_activate');

// Registering and specifying Gutenberg block
function vdo_register_block()
{
    if (!function_exists('register_block_type')) {
        return ;
    }
    wp_register_script(
        'vdo-block-script',
        plugins_url('/include/block/dist/blocks.build.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-i18n')
    );
    wp_register_style(
        'vdo-block-base-style',
        plugins_url('/include/block/dist/blocks.style.build.css', __FILE__),
        array('wp-blocks')
    );
    wp_register_style(
        'vdo-block-editor-style',
        plugins_url('/include/block/dist/blocks.editor.build.css', __FILE__),
        array('wp-edit-blocks')
    );
    register_block_type(
        'vdo/block',
        array(
        'editor_script'=>'vdo-block-script',
        'editor_style'=>'vdo-block-editor-style',
        'style'=>'vdo-block-base-style',
        'attributes'=>array(
        'id'=>array(
            'type'=>'string',
        ),
        'width'=>array(
            'type'=>'string',
            'default'=>get_option('vdo_default_width')
        ),
        'height'=>array(
            'type'=>'string',
            'default'=>get_option('vdo_default_height')
        ),
        'vdo_theme'=>array(
            'type'=>'string',
            'default'=>get_option('vdo_player_theme')
        ),
        'vdo_version'=>array(
            'type'=>'string',
            'default'=>get_option('vdo_embed_version')
        ),
        ),
        'render_callback'=>'vdo_shortcode'
        )
    );
}

add_action('init', 'vdo_register_block');

// Deactivation Hook starts
function vdo_uninstall()
{
    delete_option('vdo_client_key');
    delete_option('vdo_default_width');
    delete_option('vdo_default_height');
    delete_option('vdo_annotate_code');
    delete_option('vdo_embed_version');
    delete_option('vdo_player_theme');
    delete_option('vdo_plugin_version');
    delete_option('vdo_player_speed');
    delete_option('vdo_show_plugin_in_sidebar');
}
register_uninstall_hook(__FILE__, 'vdo_uninstall');

// Admin notice to configure plugin for new installs, starts
function vdo_admin_notice()
{
    if ((!get_option('vdo_client_key') || strlen(get_option('vdo_client_key')) != 64)
        && basename($_SERVER['PHP_SELF']) == "plugins.php"
    ) {
        ?>
        <div class="error">
            <p>
            The VdoCipher plugin is not ready.
            <a href="options-general.php?page=vdocipher">Click here to configure</a>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'vdo_admin_notice');
