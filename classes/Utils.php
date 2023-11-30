<?php 
    namespace _29kUsers;
    use WP_REST_Request;
    require_once __DIR__ . '/Error.php';
    require_once __DIR__ . '/ValidationResult.php';
    class Utils {
        private $filtersArr = [];
        function __construct() {
            $this->filtersArr = [
                'trim',
                'esc_html',
                'esc_sql',
                function ($text, $tags = array(
                    "html", "head", "title", "meta", "link", "script", "style", "body", "div", "span",
                    "p", "h1", "h2", "h3", "h4", "h5", "h6", "strong", "em", "u", "br", "hr", "blockquote", "abbr", "sup", "sub",
                    "ul", "ol", "li",
                    "a",
                    "img",
                    "form", "input", "textarea", "button", "select", "option", "label", "fieldset", "legend",
                    "table", "tr", "td", "th", "caption", "thead", "tbody", "tfoot",
                    "audio", "video", "source",
                    "header", "nav", "main", "article", "section", "aside", "footer", "figure", "figcaption",
                    "meta", "link", "base", "iframe", "canvas", "details", "summary", "mark", "time", "progress"
                )){
                    foreach($tags as $tag){ 
                        if(preg_match_all('/<'.$tag.'[^>]*>(.*)<\/'.$tag.'>/iU', $text, $found)){
                            $text = str_replace($found[0],$found[1],$text);
                        } 
                    }
                    return $text;
                }
            ];    
        }
        public function makeSafe(string $value, bool $isEmail = false): string {
            $res = $value;
            foreach($this->filtersArr as $filter) {
                $res = $filter($res);
            }
            if($isEmail) {
                return sanitize_email($res);
            }
            return sanitize_text_field($res);
        }
        public function requiredFields(WP_REST_Request $request, array $reqFields): ValidationResult {
            $req_body = $request->get_body_params();
            $missing_fields = [];
            $errors = [];
            foreach($reqFields as $error_key => $error_details) {
                $error_msg = key($error_details);
                $error_description = current($error_details);
                if(!isset($req_body[$error_key]) || empty($req_body[$error_key])) {
                    $errors[] = new Error($error_msg, [$error_description]);
                    $missing_fields[] = $error_key;
                }
            }
            if(count($errors) > 0) {
                $errors[] = new Error('missing_fields', $missing_fields);
                return new ValidationResult(false, 400, $errors);
            }
            return new ValidationResult(true);
        }
        public function CSRFFailed(WP_REST_Request $request, string $nonceKey, string $action): ValidationResult {
            $req_body = $request->get_body_params();
            if(!isset($req_body[$nonceKey]) || !wp_verify_nonce($req_body[$nonceKey], $action)) {
                return new ValidationResult(false, 403, [new Error('csrf', ['Request failed. CSRF token mismatch.'])]);
            }
            return new ValidationResult(true);
        }

        public function validatePassword(string $errorKey, string $pass, string $cpassKey, string $cpass): ValidationResult {
            $errors = [];
            $pass_errors = [];
            $passPatterns = [
                '/[a-zA-Z]/' => 'atleast one alphabet',
                '/(?=.*?[0-9])/' => 'atleast one number',
                '/(?=.*?[@$!%*?&])/' => 'atleast one special character (@$!%*?&)',
                '/.{8,}/' => 'a minimum of 8 characters'
            ];
            foreach($passPatterns as $filterKey => $filterMsg) {
                if(!preg_match($filterKey, $pass)) $pass_errors[] = $filterMsg;
            }
            $pass_error_length = count($pass_errors);
            if($pass_error_length === 1) {
                $pass_string = 'Password must contain ' . $pass_errors[0] . '.';
                $errors[] = new Error($errorKey, [$pass_string]);
            }
            else if($pass_error_length > 1) {
                $pass_string = 'Password must contain ';
                for($i = 0; $i < $pass_error_length; $i++) {
                    if($i === $pass_error_length - 1) $pass_string = $pass_string . 'and ';
                    $pass_string = $pass_string . $pass_errors[$i];
                    if($i !== $pass_error_length - 1) $pass_string = $pass_string . ', ';
                }
                $errors[] = new Error($errorKey, [$pass_string . '.']);
            }
            if(count($errors) > 0) {
                return new ValidationResult(false, 400, $errors);
            }
            else if($pass !== $cpass) {
                return new ValidationResult(false, 400, [new Error($cpassKey, ['Entered passwords don\'t match.'])]);
            }
            else return new ValidationResult(true);
        }

        public function validateEmail(string $errorKey, string $email, bool $forRegistering): ValidationResult {
            if(!is_email($email)) {
                return new ValidationResult(false, 400, [new Error($errorKey, ['Please enter a valid email address.'])]);
            }
            else if($forRegistering && email_exists($email)) {
                $validation = new ValidationResult(false, 400, [new Error($errorKey, ['Sorry, this email is already taken.'])]);
                return $validation;
            }
            return new ValidationResult(true);
        }

        public function wordpressError(string $errorMsg): ValidationResult {
            return new ValidationResult(false, 500, [new Error('wp_error', [$errorMsg])]);
        }

        public function failedValidationFactory(string $errorKey, string $errorMsg, int $statusCode = 400) {
            return new ValidationResult(false, $statusCode, [
                new Error($errorKey, [$errorMsg])
            ]);
        }
    }
?>