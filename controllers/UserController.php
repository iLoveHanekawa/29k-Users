<?php 
namespace _29kUsers;

use WP_Error;
use WP_REST_Request;
use WP_User;

class UserController {
    /**
        * @var array
    */
    function __construct() {}

    private function sendFailureResponse(ValidationResult $result) {
        wp_send_json([
            'success' => $result->getSuccessStatus(),
            'errors' => $result->getProcessedErrors()
        ], $result->getStatusCode());
    }

    private function createUser(string $email, string $fname, string $lname, string $password): WP_Error|int {
        $user_details = array(
            'user_login' => str_replace(array('@', '.'), '_', $email),
            'first_name' => $fname,
            'last_name' => $lname,
            'user_email' => $email,
            'user_pass' => $password,
            'role' => 'subscriber'
        );
        return wp_insert_user($user_details);
    }

    private function signUserIn(string $email, string $pass, bool $remember = false): WP_User|WP_Error {
        wp_clear_auth_cookie();
        return wp_signon([
            'user_login' => $email,
            'user_password' => $pass,
            'remember' => $remember
        ]);
    }
    public function register(WP_REST_Request $request) {
        $utils = new Utils();
        $validationRes = $utils->CSRFFailed($request, '29k_register_csrf_field', '29k_register_action');
        if(!$validationRes->getSuccessStatus()){
            return $this->sendFailureResponse($validationRes);
        }
        else {
            $validationRes = $utils->requiredFields($request, [
                'reg-email' => ['email' => 'Email is a required field.'], 
                'reg-fname' => ['fname' => 'First Name is a required field.'], 
                'reg-lname' => ['lname' => 'Last Name is a required field.'], 
                'reg-pass' => ['pass' => 'Password is a required field.'], 
                'reg-cpass' => ['cpass' => 'Confirm Password is a required field.']
            ]);
            if(!$validationRes->getSuccessStatus()) {
                return $this->sendFailureResponse($validationRes);
            }
            $req_body = $request->get_body_params();
            $email = $utils->makeSafe($req_body['reg-email'], true);
            $fname = $utils->makeSafe($req_body['reg-fname']);
            $lname = $utils->makeSafe($req_body['reg-lname']);
            $password = $req_body['reg-pass'];
            $cpass = $req_body['reg-cpass'];
            $validationRes->addErrors($utils->validatePassword('pass', $password, 'cpass', $cpass)->addErrors($utils->validateEmail('email', $email, true)));
            if(!$validationRes->getSuccessStatus()) {
                $this->sendFailureResponse($validationRes);
            }
            $new_user = $this->createUser($email, $fname, $lname, $password);
            if(!is_wp_error($new_user)) {
                $user = $this->signUserIn($email, $password);
                if(is_wp_error($user)) { 
                    $this->sendFailureResponse($utils->wordpressError($new_user->get_error_message())); 
                }
                wp_send_json(array(
                    'success' => true,
                    'created_user' => $new_user,
                ), 200);
            }
            else {
                $this->sendFailureResponse($utils->wordpressError($new_user->get_error_message()));
            }
            return;
        }
    }

    public function login(WP_REST_Request $request) {
        $utils = new Utils();
        $validationRes = $utils->CSRFFailed($request, '29k_login_csrf_field', '29k_login_action');
        if(!$validationRes->getSuccessStatus()) {
            return $this->sendFailureResponse($validationRes);
        }
        else {
            $required_fields = [
                'login-email' => ['email' => 'Email is a required field.'], 
                'login-pass' => ['pass' => 'Password is a required field.'], 
            ];
            $validationRes = $utils->requiredFields($request, $required_fields);
            if(!$validationRes->getSuccessStatus()){
                return $this->sendFailureResponse($validationRes);
            }
            $req_body = $request->get_body_params();
            $email = $utils->makeSafe($req_body['login-email'], true);
            $remember = false;
            if(isset($req_body['login-remember']) && $req_body['login-remember'] === 'on') {
                $remember = true;
            }
            $validationRes = $utils->validateEmail('email', $email, false);
            if(!$validationRes->getSuccessStatus()) {
                return $this->sendFailureResponse($validationRes);
            }
            $pass = $req_body['login-pass'];
            $user = $this->signUserIn($email, $pass, $remember);
            if(is_wp_error($user)) {
                $validationRes = $utils->failedValidationFactory('pass', 'Incorrect username or password.');
                $this->sendFailureResponse($validationRes);
            }
            wp_send_json([
                'success' => true,
                'user_logged_in' => $user,
            ]);
        }
    }

    public function lostPass(WP_REST_Request $request) {
        $utils = new Utils();
        $validationRes = $utils->CSRFFailed($request, '29k_lp_csrf_field', '29k_lp_action');
        if(!$validationRes->getSuccessStatus()) {
            $this->sendFailureResponse($validationRes);
        }
        else {
            $required_fields = [
                'lp-email' => ['email' => 'Email is a required field.'], 
            ];
            $validationRes = $utils->requiredFields($request, $required_fields);
            if(!$validationRes->getSuccessStatus()) {
                $this->sendFailureResponse($validationRes);
            }
            $req_body = $request->get_body_params();
            $email = $utils->makeSafe($req_body['lp-email'], true);
            $validationRes = $utils->validateEmail('email', $email, false);
            if(!$validationRes->getSuccessStatus()) {
                $this->sendFailureResponse($validationRes);
            }
            $user_data = get_user_by('email', $email);
            if ($user_data) {
                retrieve_password($user_data->user_login);
                wp_send_json([
                    'success' => true,
                    'message' => 'We have sent you an email containing the link for creating a new password. Visit the login page after changing your password.'
                ]);
            } else {
                $validationRes = $utils->failedValidationFactory('email', 'Email doesn\'t exist.');
                $this->sendFailureResponse($validationRes);
            }
        }
    }

    public function resetPass(WP_REST_Request $request) {
        $rp_path = '/29k-redevelopment/resetpass';
		$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
        $query_params = $request->get_query_params();
		if(isset($query_params['key']) && isset($query_params['login'])) {
			$value = sprintf( '%s:%s', wp_unslash($query_params['login']), wp_unslash($query_params['key']) );
			setcookie($rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true);
			wp_safe_redirect($rp_path);
			exit;
		}
    }

    public function invalidPRRequest(string $rp_cookie, string $rp_path, Utils $utils) {
        setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
        $validationRes = $utils->failedValidationFactory('rp-key', 'Invalid Request. Please request for a new password again.', 403);
        return $this->sendFailureResponse($validationRes);
    }

    public function processPRGET() {
        $rp_cookie = 'wp-resetpass-' . COOKIEHASH;
        $rp_path = site_url() . '/resetpass';
        $rp_key = null;
        $user = false;
        if(isset($_COOKIE[$rp_cookie]) && 0 < strpos($_COOKIE[$rp_cookie ],':')) {
            list($rp_login, $rp_key) = explode(':', wp_unslash($_COOKIE[ $rp_cookie ]), 2);
            $user = check_password_reset_key( $rp_key, $rp_login );
        } else {
            $user = false;
        }

        if (!$user || is_wp_error($user)) {
            setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
            return [
                'success' => false,
                'payload' => $user
            ];
        } else {
            $rp_cookie = 'wp-resetpass-' . COOKIEHASH;
            list($rp_login, $rp_key) = explode(':', wp_unslash($_COOKIE[ $rp_cookie ]), 2);
            return [
                'success' => true,
                'payload' => $rp_key
            ];
            
        }
    }

    public function processPRPOST(WP_REST_Request $request) {
        $utils = new Utils();
        $req_body = $request->get_body_params();
        $rp_path = '/29k-redevelopment/resetpass';
        $validationRes = $utils->CSRFFailed($request, '29k_rp_csrf_field', '29k_rp_action');
        if(!$validationRes->getSuccessStatus()) {
            $this->sendFailureResponse($validationRes);
        }
        $rp_cookie = 'wp-resetpass-' . COOKIEHASH;
        if(!isset($req_body['rp-key'])) {
            return $this->invalidPRRequest($rp_cookie, $rp_path, $utils);
        }
        if(isset($_COOKIE[$rp_cookie]) && 0 < strpos($_COOKIE[$rp_cookie ],':')) {
            list($rp_login, $rp_key) = explode(':', wp_unslash($_COOKIE[ $rp_cookie ]), 2);
        } else {
            $validationRes = $utils->failedValidationFactory('rp-key', 'Password change request has is either invalid or has expired. Please request for a new password again.', 500);
            return $this->sendFailureResponse($validationRes);
        }
        if(!hash_equals($rp_key, $req_body['rp-key'])) {
            return $this->invalidPRRequest($rp_cookie, $rp_path, $utils);
        };
        $user = check_password_reset_key( $rp_key, $rp_login );
        if(is_wp_error($user)) {
            return $this->invalidPRRequest($rp_cookie, $rp_path, $utils);
        }
        // TODO fix rp path

        $validationRes = $utils->requiredFields($request, ['rp-newpass' => ['rp-newpass' => 'New Password is a required field.'], 'rp-cnewpass' => ['rp-cnewpass' => 'Confirm New Password is a required field.']]);
        $pass = $req_body['rp-newpass'];
        $cpass = $req_body['rp-cnewpass'];
        if(!$validationRes->getSuccessStatus()) {
            return $this->sendFailureResponse($validationRes);
        }
        $validationRes = $utils->validatePassword('rp-newpass', $pass, 'rp-cnewpass', $cpass);
        if(!$validationRes->getSuccessStatus()) {
            $this->sendFailureResponse($validationRes);
        }
        reset_password( $user, $req_body['rp-newpass'] );
        setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
        wp_send_json([
            'success' => true,
            'message' => 'Password changed successfully. Return to the login page and use your new password to log in.'
        ]);
    }
}
?>