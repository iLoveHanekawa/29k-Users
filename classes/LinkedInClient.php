<?php 
    namespace _29kUsers;
    require_once __DIR__ . '/OAuthClient.php';
    require_once __DIR__ . '/OAuthUser.php';
    class LinkedInClient extends OAuthClient {
        /** 
            *@var string 
        */
        private $emailEndpoint = 'https://api.linkedin.com/v2/clientAwareMemberHandles?q=members&projection=(elements*(primary,type,handle~))';
        public function __construct() {
            parent::__construct(
                get_option('29k_um_linkedin_api_key', ''),
                get_option('29k_um_linkedin_secret_api_key', ''),
                'https://www.linkedin.com/oauth/v2/authorization',
                'https://www.linkedin.com/oauth/v2/accessToken',
                'https://api.linkedin.com/v2/me?projection=(firstName,lastName)',
                'r_liteprofile r_emailaddress',
                site_url() . '/wp-json/_29kreativ/v1/oauth/linkedin/login'
            ); 
        }
        public function getCodeUrl(): string {
            return $this->codeUrl . '?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $this->clientId, 
                'redirect_uri' => $this->redirectUrl, 
                'state' => 'foobar',
                'scope'=> $this->scopes
            ]);
        }
        protected function exchangeCode(string $code): string {
            $api_response = wp_remote_get($this->tokenUrl . '?' . http_build_query([
                'code' => $code, 
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUrl
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
            $email_api_response = wp_remote_get($this->emailEndpoint, array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $access_token,
                ),
            ));
            if (is_wp_error($api_response)) {
                return new Error('OAuth', ['Request to authorization endpoint failed.']);
            } else {
                $api_body = wp_remote_retrieve_body($api_response);
                $email_api_body = wp_remote_retrieve_body($email_api_response);
                $user_info = json_decode($api_body, true);
                $user_email = json_decode($email_api_body, true);
                return new OAuthUser(
                    $user_info['firstName']['localized']['en_US'],
                    $user_info['lastName']['localized']['en_US'],
                    $user_email['elements'][0]['handle~']['emailAddress']
                );
            }
        }
    }
?>