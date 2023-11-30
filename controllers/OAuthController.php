<?php 
    namespace _29kUsers;
    use WP_REST_Request;

    class OAuthController {
        /** 
            *@var OAuthClient
        */
        private $client;
        public function __construct(OAuthClient $client) {
            $this->client = $client;
        }
        public function redirectToConsentScreen() {
            wp_redirect($this->client->getCodeUrl());
            exit();
        }
        private function registerAndReturnID(OAuthUser $user) {
            $register_url = site_url() . '/wp-json/_29kreativ/v1/register';
            $csrf_token = wp_create_nonce('29k_register_action');
            // this random '@7x' will prevent password validation failure
            $random_pass = wp_generate_password() . '@7x';
            $body = [
                '29k_register_csrf_field' => $csrf_token,
                'reg-fname' => $user->getFirstName(),
                'reg-lname' => $user->getLastName(),
                'reg-email' => $user->getEmail(),
                'reg-pass' => $random_pass,
                'reg-cpass' => $random_pass
            ];
            $options = [
                'body' => $body,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            ];
            $response = wp_remote_post($register_url, $options);
            $wpUser = json_decode(wp_remote_retrieve_body($response), true);
            return email_exists($wpUser['created_user']['user_email']);
        }
        public function login(WP_REST_Request $request) {
            $params = $request->get_query_params();
            $code = $params['code'];
            if(!isset($params['code'])) {
                $error = new Error('OAuth', ['Code was not received.']);
                wp_send_json([$error->getKey() => $error->getMessages()]);
            }
            $user = $this->client->getUser($code);
            if($user instanceof Error) {
                wp_send_json([$user->getKey() => $user->getMessages()]);
            }
            $user_id = email_exists($user->getEmail());
            if(!$user_id) {
                $user_id = $this->registerAndReturnID($user);
            }
            $wpUser = get_user_by( 'id', $user_id ); 
            wp_set_current_user( $user_id, $wpUser->user_login );
            wp_set_auth_cookie( $user_id );
            do_action( 'wp_login', $wpUser->user_login, $user );
            wp_redirect(site_url(). '/wp-admin');
            exit();
        }
    }
?>