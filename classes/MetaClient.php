<?php 
    namespace _29kUsers;
    require_once __DIR__ . '/OAuthClient.php';
    require_once __DIR__ . '/OAuthUser.php';
    class MetaClient extends OAuthClient {
        public function __construct() {
            parent::__construct(
                get_option('29k_um_facebook_api_key', ''),
                get_option('29k_um_facebook_secret_api_key', ''),
                'https://www.facebook.com/v17.0/dialog/oauth',
                'https://graph.facebook.com/v17.0/oauth/access_token',
                'https://graph.facebook.com/me?fields=first_name,last_name,email',
                '',
                site_url() . '/wp-json/_29kreativ/v1/oauth/facebook/login'
            );
        }
        public function getCodeUrl(): string {
            return $this->codeUrl . '?' . http_build_query([
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUrl, 
                'state' => '{state-param}',
                'response_type' => 'code'
            ]);
        }

        protected function exchangeCode(string $code): string {
            $api_response = wp_remote_get($this->tokenUrl . '?' . http_build_query([
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUrl, 
                'client_secret' => $this->clientSecret,
                'code' => $code
            ]));
            $api_body = wp_remote_retrieve_body($api_response);
            $access_token = json_decode($api_body)->access_token;
            return $access_token;
        }

        public function getUser(string $code): OAuthUser|Error {
            $access_token = $this->exchangeCode($code);
            $api_response = wp_remote_get($this->apiEndpoint, array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $access_token,
                ),
            ));
            if (is_wp_error($api_response)) {
                return new Error('OAuth', ['Request to authorization endpoint failed.']);
            } else {
                $graph_api_body = wp_remote_retrieve_body($api_response);
                $user_info = json_decode($graph_api_body, true);
                $first_name = $user_info['first_name'];
                $last_name = $user_info['last_name'];
                $email = $user_info['email'];
                return new OAuthUser($first_name, $last_name, $email);
            }
        }
    }
?>