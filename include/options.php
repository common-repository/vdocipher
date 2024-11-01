<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    exit("<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\r\n<html lang='en'><head>\r\n<title>404 Not Found</title>\r\n".
        "</head><body>\r\n<h1>Not Found</h1>\r\n<p>The requested URL " . $_SERVER['SCRIPT_NAME'] . " was not found on ".
        "this server.</p>\r\n</body></html>");
}
?>

<style>
    details.vdo_docs {
        border: 1px solid #aaa;
        border-radius: 4px;
        padding: 0.5em 0.5em 0;
        max-width: 600px;
        margin-top: 15px;
    }

    details.vdo_docs ul {
        list-style: revert;
        padding: revert;
    }

    details.vdo_docs > summary {
        font-weight: bold;
        margin: -0.5em -0.5em 0;
        padding: 0.5em;
        cursor: pointer;
        user-select: none;
    }

    details.vdo_docs[open] {
        padding: 0.5em;
    }

    details.vdo_docs[open] summary {
        border-bottom: 1px solid #aaa;
        margin-bottom: 0.5em;
    }

    details.vdo_docs table {
        border: 1px solid #aaaaaa;
        border-collapse: collapse;
    }
    details.vdo_docs td, details.vdo_docs th {
        border: 1px solid #aaaaaa;
        padding: 5px 10px;
    }

</style>

<div class="wrap">
<h2>VdoCipher Options</h2>

<details style="width: min(100%, 500px);background: #ffffff;padding: 5px 20px;" open>
    <summary style="cursor: pointer">Any questions or feedback for us?</summary>
    <p>
        Send us your questions and feedback from <a target="_blank" href="https://www.vdocipher.com/dashboard/zen-desk">the "Support" section in your vdocipher dashboard</a> or send email to <a href="mailto:support@vdocipher.com">support@vdocipher.com</a>
    </p>
</details>
<?php include('shortcode-doc.html') ?>

<form name="vdoOptionForm" method="post" action="options.php">
<?php
settings_fields('vdo_option-group');
do_settings_sections('vdo_option-group');
?>
    <?php
    $existingKey = get_option('vdo_client_key');
    $keyIsValid = $existingKey && strlen($existingKey) == 64;
    $keyDescriptionDisplay = $keyIsValid ? 'none' : 'block';
    $restElementDisplay = $keyIsValid ? 'table-row-group' : 'none';
    ?>
    <table class="form-table">
        <tbody>
        <tr valign="top">
        <th scope="row"><label for="vdo_client_key">API Secret Key</label></th>
        <td>
            <div style="display: inline-flex;">
                <input id="vdo_client_key" name="vdo_client_key" style="width: 640px"
                       type="password" required minlength="64" maxlength="64"
                       value="<?php echo esc_attr(get_option('vdo_client_key')); ?>"/>
                <button id="toggle_API_visibility" data-protected="On" class="button" type="button">Show API Secret Key</button>
            </div>
            <p class="description" style="display: <?= $keyDescriptionDisplay ?>">
                API Key is a shared secret between your website servers and vdocipher dashboard. To generate this,
                login to vdocipher dashboard, go to "Config" > "API Keys". Generate a new key and copy-paste it here.
            </p>
        </td>
        </tr>
        </tbody>

        <tbody style="display: <?= $restElementDisplay ?>">
        <tr valign="top">
        <th scope="row"><label for="vdo_default_width">Default Width</label></th>
        <td>
            <input id="vdo_default_width" name="vdo_default_width"
                   type="number" required
                   value="<?php echo esc_attr(get_option('vdo_default_width')); ?>"
        /></td>
        </tr>

        <tr valign="top">
        <th scope="row"><label for="vdo_default_height">Default Height</label></th>
        <td>
            <input type="text" id="vdo_default_height" name="vdo_default_height"
                   required pattern="^auto|\d+$"
                   value="<?php echo esc_attr(get_option('vdo_default_height')); ?>"/>
            <p class="description">Can be either "auto" or a number. Set to "auto" height and max width for responsive layout.</p>
        </td>
        </tr>

        <tr>
        <th scope="row"><label for="vdo_player_speed">Playback speed</label></th>
        <td>
            <input type="text" id="vdo_player_speed" name="vdo_player_speed"
                   pattern="^\d.\d{1,2}(,\d.\d{1,2})+$"
              value="<?php echo esc_attr(get_option('vdo_player_speed')); ?>"
            />
            <p class="description">Speed can be defined as comma separated decimal values e.g. 0.75,1.0,1.25,1.5,1.75,2.0</p>
            <p class="description">With player v2, there is an option to set this as part of custom player configuration. This setting will override any options set as part of custom player.</p>
        </td>
        </tr>

        <!-- Version Number -->
        <?php
        $existingVersion = get_option('vdo_embed_version');
        $embedVersionDisplay = 'none';
        if ($existingVersion !== false && $existingVersion !== VDOCIPHER_PLAYER_VERSION) {
            $embedVersionDisplay = 'table-row';
        } ?>
        <tr valign="top">
            <?php
            function vdo_selected($match) {
                $existingEmbedVersion = get_option('vdo_embed_version');
                // global $existingEmbedVersion;
                // return $GLOBALS['existingEmbedVersion'] ;
                return $existingEmbedVersion === $match ? 'selected="selected"' : '';
            }
            ?>
            <th scope="row"><label for="vdo_embed_version">Player Version</label></th>
            <td>
                <div style="display: inline-flex">
                    <select name="vdo_embed_version" id="vdo_embed_version" onchange="" autocomplete="off">
                        <option value="1.6.10" <?= vdo_selected('1.6.10') ?>>v1</option>
                        <option value="v2" <?= vdo_selected('v2') ?>>v2 (Recommended)</option>
                    </select>
                </div>
                <?php include( 'player-versions-doc.php' ) ?>
            </td>
        </tr>

        <!-- Player Theme Options -->
        <?php
        /**
         * if the player is v1 and non-standard theme, then only allow disabling non-standard themes
         * if the player is v1 and standard theme, do not show anything
         * if the player is v2, allow adding player IDs. But validate the length of id
         *
         * This can be implemented in both PHP and JS. But if the JS is essential, then we can skip
         * writing in PHP and run the JS function after page load.
         */
        $existingTheme  = get_option('vdo_player_theme');
        ?>
        <tr valign="top" id="vdo_theme_editing_row" >
        <th scope="row">
            <label for="vdo_player_theme">Player ID</label>
            <br />
            <small style="font-weight: normal"><em>Leave this blank for using default theme. Player id can also be specified in the shortcode if needed to be different for different videos.</em></small>
        </th>
        <td>
            <div style="display:inline-flex; margin-bottom:10px;">
                <input
                        type="text"
                        name="vdo_player_theme"
                        id="vdo_player_theme"
                        value="<?php echo $existingTheme; ?>"
                        maxlength="32"
                        style="width: 320px"
                />
                <p class="vdo_saveChangeMessage" style="display: none; font-style: italic">Click on "save changes" below to confirm</p>
                <button id="vdo_setDefaultThemeBtn" style="display: none" class="button" type="button">Revert to default theme</button><br/>
                <script>
                    (function () {
                        const themeInputRow = document.querySelector('#vdo_theme_editing_row');
                        const themeInputField = themeInputRow.querySelector('input');
                        const playerVersionField = document.querySelector('[name=vdo_embed_version]');
                        const setDefaultButton = document.querySelector('#vdo_setDefaultThemeBtn');
                        playerVersionField.addEventListener('change', updateThemeInput);
                        const defaultV1Theme = '9ae8bbe8dd964ddc9bdb932cca1cb59a';
                        const originalTheme = "<?= get_option('vdo_player_theme') ?>";
                        let resetV2Theme, resetV1Theme;
                        function updateThemeInput() {
                            if (playerVersionField.value === 'v2') {
                                themeInputRow.style.display = '';
                                if (themeInputField.value.match(/^\w{32}$/)) resetV1Theme = themeInputField.value;
                                if (resetV2Theme) themeInputField.value = resetV2Theme;
                                // if the input is not valid for v2, clear the field
                                if (!themeInputField.value.match(/^\w{16}$/)) themeInputField.value = '';
                                // set the max and min length for v2 players
                                themeInputField.setAttribute('maxlength', '16');
                                themeInputField.setAttribute('minlength', '16');
                                setDefaultButton.style.display = 'none';
                            } else {
                                // if input valid for v2, save in reset variable
                                if (themeInputField.value.match(/^\w{16}$/)) resetV2Theme = themeInputField.value;
                                if (resetV1Theme) themeInputField.value = resetV1Theme;
                                // if input is not valid for v1, reset to default theme
                                if (!themeInputField.value.match(/^\w{32}$/)) themeInputField.value = defaultV1Theme;
                                themeInputField.setAttribute('maxlength', '32');
                                themeInputField.setAttribute('minlength', '32');
                                // if original theme was some custom v1 theme, hide the entire row
                                // else, show reset button
                                if (originalTheme.length !== 32 || originalTheme === defaultV1Theme) {
                                    themeInputRow.style.display = 'none';
                                } else {
                                    setDefaultButton.style.display = '';
                                    setDefaultButton.addEventListener('click', (e) => {
                                        e.preventDefault();
                                        themeInputField.value = '9ae8bbe8dd964ddc9bdb932cca1cb59a'
                                        e.target.style.display = 'none';
                                        e.target.parentElement.querySelector('.vdo_saveChangeMessage').style.display = '';
                                    });
                                }
                            }
                        }
                        setTimeout(() => {
                            updateThemeInput();
                        }, 0);
                    })()
                </script>
            </div>
            <?php include('player-id-doc.html'); ?>
        </td>
        </tr>
        <!-- Player Theme Options end-->

        <tr valign="top">
        <th scope="row"><label for="vdo_watermarkjson">Annotation Statement</label></th>
        <td>
          <div style="display: inline-flex;">
              <textarea name="vdo_annotate_code" id="vdo_watermarkjson" rows="6" cols="55" style="font-family: monospace"
          ><?php
            if (get_option('vdo_annotate_code') != "") {
                echo get_option('vdo_annotate_code');
                $vdo_annotation_code = get_option('vdo_annotate_code');
            }
            ?></textarea>
          <p class="description" style="margin-left:20px; position: relative">
          <span style="color:purple"><b>Sample Code for Dynamic Watermark</b></span><br/>
          [{'type':'rtext', 'text':' {name}', 'alpha':'0.60', 'color':'0xFF0000','size':'15','interval':'5000'}] <br/>
          <span style="color:purple"><b>Sample Code for Static Watermark</b></span><br/>
          [{'type':'text', 'text':'{ip}', 'alpha':'0.5' , 'x':'10', 'y':'100', 'color':'0xFF0000', 'size':'12'}] <br/>
          </p>
          </div>
          <p class="description" id="vdojsonvalidator"></p>
          <p class="description">
                Leave this text blank in case you do not need watermark over all
                videos. For details on writing the annotation code
                <a href="https://www.vdocipher.com/blog/2014/12/add-text-to-videos-with-watermark/" target="_blank">
                    check this out
                </a>
          </p>
        </td>
        </tr>

        <!-- Advanced Options -->

        <tr valign="top">
          <th scope="row"></th>
          <td>
            <details>
              <summary style="cursor: pointer">Advanced Options</summary>
              <div style="margin: 20px 0">
                <div style="display: inline-flex;">
                  <div style="width: 300px;">
                    <label for="vdo_show_plugin_in_sidebar"><strong>Show VdoCipher in sidebar</strong></label>
                  </div>
                  <div>
                    <input type="checkbox" name="vdo_show_plugin_in_sidebar" id="vdo_show_plugin_in_sidebar"
                           value="true" <?php echo esc_attr(get_option('vdo_show_plugin_in_sidebar')) == 'true' ? 'checked' : ''; ?>/>
                  </div>
                </div>
                <p class="description">
                  Not recommended to be changed. Mark this option unchecked if you like to hide the VdoCipher settings page from the sidebar.<br/>
                  Once changed, you will need to access this page from inside the Wordpress Settings / Plugins page.
                </p>
              </div>
            </details>

          </td>
        </tr>

        <!-- Footer -->
        <tr style="display:none;">
          <td>Plugin version no.: </td>
          <td><input
            id="vdo_plugin_version" name="vdo_plugin_version" type="hidden"
            value="<?php echo esc_attr(get_option('vdo_plugin_version')); ?>" readonly>
          </td>
        </tr>

        </tbody>
    </table>
    <?php
        wp_enqueue_script('vdo_validate_watermark', plugin_dir_url(__FILE__).'js/validatewatermark.js');
        wp_enqueue_script('vdo_hide_key', plugin_dir_url(__FILE__).'js/showkey.js');
        ?>
<?php submit_button(); ?>
</form>
</div>
