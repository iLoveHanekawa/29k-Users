<?php 
    namespace _29kUsers;
    require_once __DIR__ . '/OAuthClient.php';
    require_once __DIR__ . '/OAuthUser.php';
    class GoogleClient extends OAuthClient {
        public function __construct() {
            parent::__construct(
                get_option('29k_um_google_api_key', ''),
                get_option('29k_um_google_secret_api_key'),
                'https://accounts.google.com/o/oauth2/v2/auth',
                'https://oauth2.googleapis.com/token',
                'https://www.googleapis.com/oauth2/v3/userinfo',
                'https://www.googleapis.com/auth/drive.metadata.readonly',
                site_url() . '/wp-json/_29kreativ/v1/oauth/google/login'
            );
        }

        public function getCodeUrl(): string {
            return $this->codeUrl . '?' . http_build_query([
                'scope'=> $this->scopes,
                'access_type' => 'offline',
                'include_granted_scopes' => 'true',
                'response_type' => 'code',
                'state' => 'state_parameter_passthrough_value',
                'redirect_uri' => $this->redirectUrl,
                'client_id' => $this->clientId
            ]);
        }
        public function exchangeCode(string $code): string {
            $options = [
                'body' => http_build_query([
                    'code' => $code, 
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'redirect_uri' => $this->redirectUrl,
                    'grant_type' => 'authorization_code'
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
                $google_api_body = wp_remote_retrieve_body($api_response);
                $user_info = json_decode($google_api_body, true);
                $first_name = $user_info['given_name'];
                if(isset($user_info['family_name'])) {
                    $last_name = $user_info['family_name'];
                } else {
                    $last_name = '';
                }
                return new OAuthUser($first_name, $last_name, $user_info['email']);
            }
        }
    }
?>