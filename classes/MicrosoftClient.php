<?php 
    namespace _29kUsers;
    require_once __DIR__ . '/OAuthClient.php';
    require_once __DIR__ . '/OAuthUser.php';
    class MicrosoftClient extends OAuthClient {
        public function __construct() {
            parent::__construct(
                get_option('29k_um_microsoft_api_key', ''),
                get_option('29k_um_microsoft_secret_api_key', ''),
                'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
                'https://login.microsoftonline.com/common/oauth2/v2.0/token',
                'https://graph.microsoft.com/oidc/userinfo',
                'user.read openid profile email',
                site_url() . '/wp-json/_29kreativ/v1/oauth/microsoft/login'
            );
        }
        public function getCodeUrl(): string {
            return $this->codeUrl . '?' . http_build_query([
                'client_id' => $this->clientId,
                'response_type' => 'code',
                'redirect_uri' => $this->redirectUrl,
                'response_mode' => 'query',
                'scope' => $this->scopes,
                'state' => '12345'
            ]);
        }
        protected function exchangeCode($code): string {
            $options = [
                'body' => http_build_query([
                    'client_id' => $this->clientId,
                    'scope' => $this->scopes,
                    'code' => $code,
                    'state' => '12345',
                    'redirect_uri'=> $this->redirectUrl,
                    'grant_type'=>'authorization_code',
                    'client_secret'=> $this->clientSecret
                ]),
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            ];
            $api_response = wp_remote_post( $this->tokenUrl, $options );
            $api_body = wp_remote_retrieve_body($api_response);
            $access_token = json_decode($api_body)->access_token;
            return $access_token;
        }
        public function getUser($code): OAuthUser|Error {
            $access_token = $this->exchangeCode($code);
            $api_response = wp_remote_get($this->apiEndpoint, array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $access_token,
                ),
            ));
            if (is_wp_error($api_response)) {
                return new Error('OAuth', ['Request to authorization endpoint failed.']);
            } else {
                $api_body = wp_remote_retrieve_body($api_response);
                $user_info = json_decode($api_body, true);
                $first_name = $user_info['givenname'];
                if(isset($user_info['familyname'])) {
                    $last_name = $user_info['familyname'];
                } else {
                    $last_name = '';
                }
                $email = $user_info['email'];
                return new OAuthUser($first_name, $last_name, $email);
            }
        }
    }
?>