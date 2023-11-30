<div class="wrap">
    <?php 
        $current_google_api_key = get_option('29k_um_google_api_key', '');
        $current_facebook_api_key = get_option('29k_um_facebook_api_key', '');
        $current_google_secret_api_key = get_option('29k_um_google_secret_api_key', '');
        $current_facebook_secret_api_key = get_option('29k_um_facebook_secret_api_key', '');
        $current_linkedin_api_key = get_option('29k_um_linkedin_api_key');
        $current_linkedin_secret_api_key = get_option('29k_um_linkedin_secret_api_key');
        $current_microsoft_api_key = get_option('29k_um_microsoft_api_key');
        $current_microsoft_secret_api_key = get_option('29k_um_microsoft_secret_api_key');
    ?>
    <h1>29kreativ User Management Settings</h1>
    <h3>OAuth API keys</h3>
    <form class="um-settings-form" method="post" action="">
        <table class='form-table'>
            
            <tr valign="top">
                <th scope="row">
                    <label for="29k_um_google_api_key">Google ClientID Key:</label>
                </th>
                <td>
                    <input type="text" class="regular-text" id="29k_um_google_api_key" name="29k_um_google_api_key" value="<?php echo esc_attr($current_google_api_key); ?>">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="29k_um_google_secret_api_key">Google Client Secret Key:</label>
                </th>
                <td>
                    <input type="text" class="regular-text" id="29k_um_google_secret_api_key" name="29k_um_google_secret_api_key" value="<?php echo esc_attr($current_google_secret_api_key); ?>">
                </td>
            </tr>
    
            <tr valign="top">
                <th scope="row">
                    <label for="29k_um_facebook_api_key">Facebook API Key:</label>
                </th>
                <td>
                    <input type="text" class="regular-text" id="29k_um_facebook_api_key" name="29k_um_facebook_api_key" value="<?php echo esc_attr($current_facebook_api_key); ?>">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="29k_um_facebook_secret_api_key">Facebook API Secret Key:</label>
                </th>
                <td>
                    <input type="text" class="regular-text" id="29k_um_facebook_secret_api_key" name="29k_um_facebook_secret_api_key" value="<?php echo esc_attr($current_facebook_secret_api_key); ?>">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="29k_um_linkedin_api_key">Linkedin ClientId Key:</label>
                </th>
                <td>
                    <input type="text" class="regular-text" id="29k_um_linkedin_api_key" name="29k_um_linkedin_api_key" value="<?php echo esc_attr($current_linkedin_api_key); ?>">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="29k_um_linkedin_secret_api_key">LinkedIn Client Secret Key:</label>
                </th>
                <td>
                    <input type="text" class="regular-text" id="29k_um_linkedin_secret_api_key" name="29k_um_linkedin_secret_api_key" value="<?php echo esc_attr($current_linkedin_secret_api_key); ?>">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="29k_um_microsoft_api_key">Microsoft ClientId Key:</label>
                </th>
                <td>
                    <input type="text" class="regular-text" id="29k_um_microsoft_api_key" name="29k_um_microsoft_api_key" value="<?php echo esc_attr($current_microsoft_api_key); ?>">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="29k_um_microsoft_secret_api_key">Microsoft Tenant ID:</label>
                </th>
                <td>
                    <input type="text" class="regular-text" id="29k_um_microsoft_secret_api_key" name="29k_um_microsoft_secret_api_key" value="<?php echo esc_attr($current_microsoft_secret_api_key); ?>">
                </td>
            </tr>

        </table>

        <button type="submit" name="29k_settings_submit" class="button-primary">Save Settings</button>
    </form>
</div>