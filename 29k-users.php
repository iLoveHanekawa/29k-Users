<?php 
    /*
    * Plugin Name: 29K Users Plugin
    * Description: User Management
    * Version: 1.0
    * Author: 29K team
    */
    require_once __DIR__ . '/controllers/UserController.php';
    require_once __DIR__ . '/classes/GoogleClient.php';
    require_once __DIR__ . '/classes/MetaClient.php';
    require_once __DIR__ . '/classes/LinkedInClient.php';
    require_once __DIR__ . '/classes/MicrosoftClient.php';
    require_once __DIR__ . '/controllers/OAuthController.php';
    require_once __DIR__ . '/classes/UsersPlugin.php';
    require_once __DIR__ . '/classes/Utils.php';

    $plugin = _29kUsers\UsersPlugin::getPluginInstance();

    $plugin->addRestRoute('POST', '_29kreativ/v1', 'register', function (WP_REST_Request $request) {
        $register_form = new _29kUsers\UserController();
        return $register_form->register($request);
    });
    $plugin->addRestRoute('POST', '_29kreativ/v1', 'login', function (WP_REST_Request $request) {
        $register_form = new _29kUsers\UserController();
        return $register_form->login($request);
    });
    $plugin->addRestRoute('POST', '_29kreativ/v1', 'lostpass', function (WP_REST_Request $request) {
        $register_form = new _29kUsers\UserController();
        return $register_form->lostPass($request);
    });
    $plugin->addRestRoute('GET', '_29kreativ/v1', 'resetpass', function (WP_REST_Request $request) {
        $register_form = new _29kUsers\UserController();
        return $register_form->resetPass($request);
    });
    $plugin->addStyle(plugin_dir_url(__FILE__).'ui/assets/css/styles.css');
    $plugin->addShortCode('29k_register_form', dirname(__FILE__) . '\ui\register.php');
    $plugin->addShortCode('29k_login_form', dirname(__FILE__) . '\ui\login.php');
    $plugin->addShortCode('29k_lostpass_form', dirname(__FILE__) . '\ui\lostpass.php');
    $plugin->addOAuthRestRoutes();
    $plugin->addScriptWithJavaScriptObject(plugin_dir_url(__FILE__) . 'ui/assets/js/shortcode_req.js', 'siteData', [
        'siteUrl' => site_url()
    ], ['jquery']); 

    $plugin->addPluginAdminSettings(
        plugin_basename(__FILE__),
        '29kreativ Users Management',
        '29K Users',
        '29k-users-plugin-settings',
        [
            '29k_um_google_api_key',
            '29k_um_facebook_api_key',
            '29k_um_google_secret_api_key',
            '29k_um_facebook_secret_api_key',
            '29k_um_linkedin_secret_api_key',
            '29k_um_linkedin_api_key',
            '29k_um_microsoft_api_key',
            '29k_um_microsoft_secret_api_key'
        ],
        '29k_settings_submit',
        dirname(__FILE__) . '\ui\settings.php',
        'dashicons-id'
    );

    $plugin->setResetPasswordEmail(function (string $message, string $key, string $user_login, WP_User $user_data) {
        date_default_timezone_set('GMT');
        $reset_link = network_site_url("wp-json/_29kreativ/v1/resetpass?key=$key&login=" . rawurlencode($user_login));
        // Create new message
        $message = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Password Reset</title>
            <style>
                p, div, a, h1 { font-size: 14px; font-weight: 400; }
                .email-body { font-family: sans-serif; }
                .email-fn { font-size: 24.88px; font-weight: 700; }
                .logo-img { width: 60px; height: 60px; }
                .email-heading { font-size: 18.66px; font-weight: 500; }
                .email-content { color: #777777; font-size: 15px; font-weight: 300; }
                .email-link { display: block; font-weight: 700; }
                .ai-main { font-weight: 700; font-size: 12px; }
                .site-name { color: rgb(255, 21, 64); font-size: 15px; font-weight: 700; }
                .ai-sub { color: #666666; font-weight: 300; }
            </style>
        </head>
        <body>
            <div class="email-body">
                <img class="logo-img" src=" ' . 'https://www.29kreativ.com/images/sprite1.png' . '">
                <p class="site-name">29KREATIV</p>
                <h2 class="email-fn">Password Recovery:</h2>
                <h1 class="email-heading">Hi ' . esc_html($user_data->first_name) . ',</h1>
                <p class="email-content">We have received a request to reset the password of your 29kreativ account.</p>
                <p class="email-content">Follow this link to complete the reset.</p>
                <a href="' . esc_url($reset_link) . '" class="email-link">Reset Password</a>
                <p class="email-content">Thanks for helping us keep your account secure.</p>
                <p class="email-content">29K team.</p>
                <h2 class="email-fn">Additional Information:</h2>
                <h3 class="ai-main">Date and Time: <span class="ai-sub">' . esc_html(date("Y-m-d H:i:s", time())) . ' (GMT)</span></h3>
                <p class="ai-main">Didn\'t request a new password? <span class="ai-sub">If this was a mistake, just ignore this email and nothing will happen.</span></p>
            </div>
        </body>
        </html>';
        return $message;
    });
    $plugin->addShortCode('29k_resetpass_form', dirname(__FILE__) . '\ui\resetpass.php');

    // TODO why capture doesn't work here
    add_action('template_redirect', function () {
        if (is_page('resetpass') ) {
            if($_SERVER['REQUEST_METHOD'] === 'GET') {
                $userController = new _29kUsers\UserController();
                $result = $userController->processPRGET();
                if($result['success'] === false) {
                    // TODO no idea what this line does but it was in core
                    if($result['payload'] && $result['payload']->get_error_code() === 'expired_key' ) {
                        wp_redirect( site_url( 'lostpass?error=expiredkey' ) );
                    } else {
                        wp_redirect( site_url( 'lostpass?error=invalidkey' ) );
                    }
                    add_shortcode('29k_resetpass_form', function ($atts) {
                        ob_start();
                        include(dirname(__FILE__) . '\ui\resetpass.php');
                        $content = ob_get_clean();
                        return $content;
                    });
                    exit();
                }
                else {
                    $rp_key = $result['payload'];
                    add_shortcode('29k_resetpass_form', function ($atts) use($rp_key) {
                        $shortcode_atts = shortcode_atts(['rp-key' => $rp_key], $atts);
                        ob_start();
                        include(dirname(__FILE__) . '\ui\resetpass.php');
                        $content = ob_get_clean();
                        return $content;
                    });
                }
            }
            else if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $request = new WP_REST_Request('POST', site_url() . '/resetpass');
                $request->set_body_params($_POST);
                $userController = new _29kUsers\UserController();
                $userController->processPRPOST($request);
            }
        }
    });
?>