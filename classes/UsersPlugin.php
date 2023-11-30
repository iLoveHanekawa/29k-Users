<?php 
    namespace _29kUsers;
    require_once __DIR__ . '/BasePlugin.php';
    require_once __DIR__ . '/MetaClient.php';
    require_once __DIR__ . '/GoogleClient.php';
    require_once __DIR__ . '/LinkedInClient.php';
    require_once __DIR__ . '/MicrosoftClient.php';
    require_once __DIR__ . '/../controllers/OAuthController.php';
    use WP_REST_Request;

    class UsersPlugin extends BasePlugin {
        private static $instance = null;
        private function __construct() {}
        public static function getPluginInstance(): UsersPlugin {
            if(self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function clientFactory(string $provider): OAuthClient {
            $client = null;
            switch($provider) {
                case "facebook":
                    $client = new MetaClient();
                    break;
                case "google":
                    $client = new GoogleClient();
                    break;
                case "linkedin":
                    $client = new LinkedInClient();
                    break;
                case "microsoft":
                    $client = new MicrosoftClient();
                    break;
                default:
                    break;
            }
            return $client;
        }
        public function addShortCodeWithAtts(string $key, string $path, array $attsArr) {
            add_shortcode($key, function ($atts) use($attsArr, $path) {
                $shortcode_atts = shortcode_atts($attsArr, $atts);
                ob_start();
                include($path);
                $content = ob_get_clean();
                return $content;
            });
        }
        public function addOAuthRestRoutes(): void {
            $this->addRestRoute('GET', '_29kreativ/v1/oauth', 'init', function (WP_REST_Request $request) {
                $params = $request->get_query_params();
                $provider = $params['provider'];
                $client = $this->clientFactory($provider);
                $oauthController = new OAuthController($client);
                $oauthController->redirectToConsentScreen();
            });

            $providers = ['google', 'facebook', 'linkedin', 'microsoft'];
            foreach($providers as $provider){ 
                $client = $this->clientFactory($provider);
                $this->addRestRoute('GET', '_29kreativ/v1/oauth/' . $provider, 'login', function (WP_REST_Request $request) use($client) {
                    $oauth = new OAuthController($client);
                    return $oauth->login($request);
                });
            }
        }
        public function addScriptWithJavaScriptObject(string $scriptPath, string $objectNameForFrontend, array $assocArrayToConvert, array $scriptDependency = array(), string|bool $scriptVersion = false, array $scriptArgs = array()) {
            add_action( 'wp_enqueue_scripts', function () use($scriptPath, $objectNameForFrontend, $assocArrayToConvert, $scriptDependency, $scriptVersion, $scriptArgs) {
                $key = md5($scriptPath);
                wp_enqueue_script($key, $scriptPath, $scriptDependency, $scriptVersion, $scriptArgs);
                wp_localize_script($key, $objectNameForFrontend, $assocArrayToConvert);
            });
        }
    }
?>