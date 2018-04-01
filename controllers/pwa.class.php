<?php
namespace O10n;

/**
 * Progressive Web App Optimization Controller
 *
 * @package    optimization
 * @subpackage optimization/controllers
 * @author     PageSpeed.pro <info@pagespeed.pro>
 */
if (!defined('ABSPATH')) {
    exit;
}

class Pwa extends Controller implements Controller_Interface
{

    // module key refereces
    private $client_modules = array(
        'pwa'
    );

    // automatically load dependencies
    private $client_module_dependencies = array();

    private $preload = array();

    private $manifest_file;

    private $sw_filename = 'o10n-sw.js';
    private $sw_filename_debug = 'o10n-sw.debug.js';
    private $sw_worker_filename = 'o10n-sw-worker.js';
    private $sw_worker_filename_debug = 'o10n-sw-worker.debug.js';
    private $sw_hash_filename = 'o10n-sw-hash.txt';
    private $sw_hash_filename_debug = 'o10n-sw-hash.debug.txt';

    private $sw_path; // root path of service worker
    private $sw_url_path; // root URL path of service worker
    private $sw_scope; // service worker scope
    private $sw_hash; // service worker source file hash

    private $preload_attachments = array(); // attached assets for smart preloading in service worker

    /**
     * Load controller
     *
     * @param  Core       $Core Core controller instance.
     * @return Controller Controller instance.
     */
    public static function &load(Core $Core)
    {
        // instantiate controller
        return parent::construct($Core, array(
            'env',
            'file',
            'cache',
            'options',
            'client'
        ));
    }

    /**
     * Setup controller
     */
    protected function setup()
    {

        // add module definitions
        $this->client->add_module_definitions($this->client_modules, $this->client_module_dependencies);

        // path to manifest.json
        $this->manifest_file = apply_filters('o10n_manifest_file', trailingslashit(ABSPATH) . 'manifest.json');

        $debug = defined('O10N_DEBUG') && O10N_DEBUG;

        // service worker files
        if ($debug) {
            $this->sw_filename = $this->sw_filename_debug;
            $this->sw_worker_filename = $this->sw_worker_filename_debug;
        }
        $this->sw_filename = apply_filters('o10n_sw_filename', $this->sw_filename);
        $this->sw_worker_filename = apply_filters('o10n_sw_worker_filename', $this->sw_worker_filename);

        // file path
        $path = $this->file->trailingslashit(ABSPATH);
        $this->sw_path = apply_filters('o10n_sw_path', $path);
        if ($this->sw_path !== $path) {
            $this->sw_path = $this->file->trailingslashit($this->sw_path);
        }

        // URL path of service worker files
        $url_path = $this->file->trailingslashit(parse_url(site_url(), PHP_URL_PATH));
        $this->sw_url_path = apply_filters('o10n_sw_url_path', $url_path);
        if ($this->sw_url_path !== $url_path) {
            $this->sw_url_path = $this->file->trailingslashit($this->sw_url_path);
        }

        // service worker scope
        $scope = $this->options->get('pwa.scope');
        $this->sw_scope = apply_filters('o10n_sw_scope', (($scope) ? $scope : $url_path));

        if ($this->options->bool('pwa.enabled') && $this->env->is_optimization()) {
            $this->client->load_module('pwa', O10N_CORE_VERSION, $this->core->modules('pwa')->dir_path());

            // client config
            $this->client->set_config('pwa', 'path', $this->sw_url_path . $this->sw_filename);
            $this->client->set_config('pwa', 'scope', $this->sw_scope);

            $this->client->set_config('pwa', 'register', $this->options->bool('pwa.register'));

            // offline class on body
            if (!$this->options->bool('pwa.offline_class')) {
                $this->client->set_config('pwa', 'offline_class', 1);
            }

            // preload mouse down
            if (!$this->options->bool('pwa.preload_mousedown')) {
                $this->client->set_config('pwa', 'preload_mousedown', 1);
            }
        } else {
            if ($this->options->bool('pwa.unregister')) {
                $this->client->set_config('pwa', 'unregister', 1);
            }
        }

        $this->sw_hash = get_option('o10n_pwa_sw_hash' . (($debug) ? '_debug' : ''));

        // install service worker
        if (!$this->sw_hash) {
            $this->update_sw();
            $this->sw_hash = get_option('o10n_pwa_sw_hash' . (($debug) ? '_debug' : ''));
        }

        // service worker updated in past day, verify hash to force updates to visitors after configuration changes
        if ($this->options->bool('pwa.verify.enabled')) {
            $update_interval = $this->options->get('pwa.verify.update_interval');
            if (!is_numeric($update_interval)) {
                $update_interval = -1;
            }

            $this->client->set_config('pwa', 'file_hash', $update_interval);
        }


        if (!$this->env->is_optimization()) {
            return;
        }

        // web app meta
        add_action('wp_head', array($this, 'header'), $this->first_priority);

        // output headers
        add_action('o10n_headers', array($this, 'send_headers'), PHP_INT_MAX);
    }

    /**
     * Print web app meta
     */
    final public function header()
    {
        if (!$this->env->is_optimization()) {
            return;
        }
        
        $meta = $this->options->get('pwa.meta');
        if ($meta) {
            print $meta;
        }
    }

    /**
     * Output HTTP headers
     */
    final public function send_headers()
    {
        if (headers_sent()) {
            return;
        }

        // preload attachments for smart preloading in service worker
        $preload_attachments = apply_filters('o10n_sw_preload_attachments', $this->preload_attachments);
        if (is_array($preload_attachments) && !empty($preload_attachments)) {
            $links = array();
            foreach ($preload_attachments as $url) {

                // sanitize URL
                if (strpos($url, '<') !== false || strpos($url, '>') !== false) {
                    $url = str_replace(array('<','>'), array('%3C','%3E'), $url);
                }
                
                $links[] = sprintf(
                    '<%s>',
                    $url
                );
            }
            header(sprintf('%s: %s', 'X-O10N-SW-Preload', implode(', ', $links)));
        }
    }

    /**
     * Return Service Worker URL
     */
    final public function sw_url_path()
    {
        return $this->sw_url_path . $this->sw_filename;
    }

    /**
     * Return Service Worker file path
     */
    final public function sw_path()
    {
        return $this->sw_path . $this->sw_filename;
    }

    /**
     * Return Service Worker config
     */
    final public function sw_config()
    {

        // get manifest
        $manifest = $this->manifest_json(true);

        // construct cache policy
        $cache_policy = $this->options->get('pwa.cache.policy', array());

        // page cache
        if ($this->options->bool('pwa.cache.pages')) {

            // create page cache policy
            $page_cache_policy = array(
                'title' => 'Match pages',
                'match' => array(
                    array( 'type' => 'header', 'name' => 'Accept', 'pattern' => 'text/html')
                ),
                'strategy' => $this->options->get('pwa.cache.pages.strategy', 'network'),
                'cache' => array(
                    'conditions' => array(
                        array(
                            'type' => 'header',
                            'name' => 'content-type',
                            'pattern' => 'text/html'
                        )
                    )
                )
            );

            // apply page cache policy filter
            if ($this->options->bool('pwa.cache.pages.filter')) {
                $custom_match_filter = $this->options->json('pwa.cache.pages.filter.match');
                if ($custom_match_filter && !empty($custom_match_filter)) {
                    $page_cache_policy['match'] = $custom_match_filter;
                }
            }

            // offline page
            $offline = $this->options->get('pwa.cache.pages.offline');
            if ($offline) {
                $page_cache_policy['offline'] = $offline;
            }

            // cache strategy
            $strategy = $this->options->get('pwa.cache.pages.strategy');
            if (in_array($strategy, array('cache', 'event'))) {
                $update_interval = $this->options->get('pwa.cache.pages.cache.update_interval');
                if (isset($update_interval) && is_numeric($update_interval) && $update_interval >= 0) {
                    $page_cache_policy['cache']['update_interval'] = intval($update_interval);
                }
                $max_age = $this->options->get('pwa.cache.pages.cache.max_age');
                if (isset($max_age) && is_numeric($max_age) && $max_age > 0) {
                    $page_cache_policy['cache']['max_age'] = intval($max_age);
                }
                $page_cache_policy['cache']['head_update'] = $this->options->get('pwa.cache.pages.cache.head_update');
                $page_cache_policy['cache']['notify'] = $this->options->get('pwa.cache.pages.cache.notify');
            }

            // custom cache conditions
            $conditions = $this->options->get('pwa.cache.pages.cache.conditions');
            if (!is_array($conditions)) {
                $conditions = json_decode('[{
                    "type": "header",
                    "name": "content-type",
                    "pattern": "text/html"
                }]', true);
            }
            $page_cache_policy['cache']['conditions'] = $conditions;

            $cache_policy[] = $page_cache_policy;

            // Lighthouse audit bug results in false negative, require URL based match of start URL
            // @link https://github.com/GoogleChrome/lighthouse/issues/4312
            if ($manifest && isset($manifest['start_url']) && $manifest['start_url'] && $manifest['start_url'] !== '/') {

                // create start url cache policy
                $page_cache_policy['match'] = array(
                        array( 'type' => 'url', 'pattern' => $manifest['start_url'] )
                );
                $cache_policy[] = $page_cache_policy;
            }
        }

        // convert cache policy keys to config index
        try {
            foreach ($cache_policy as $index => $policy) {
                $index_policy = array();
                foreach ($policy as $key => $value) {
                    switch ($key) {
                    case "cache":
                        $index_cache = array();
                        foreach ($value as $c_key => $c_settings) {
                            switch ($c_key) {
                                case "conditions":
                                    foreach ($c_settings as $c_index => $condition) {
                                        $index_condition = array();
                                        foreach ($condition as $condition_key => $c_value) {
                                            if ($condition_key === 'type') {
                                                $c_value = $this->client->config_index('pwa', 'policy_' . $c_value);
                                            }
                                            if ($condition_key === 'pattern' && is_array($c_value)) {
                                                $pattern = array();
                                                foreach ($c_value as $pattern_key => $pattern_value) {
                                                    $pattern[$this->client->config_index('pwa', 'policy_'. $pattern_key)] = $pattern_value;
                                                }
                                                $c_value = $pattern;
                                            }
                                            $index_condition[$this->client->config_index('pwa', 'policy_'. $condition_key)] = $c_value;
                                        }
                                        $index_cache[$this->client->config_index('pwa', 'policy_'. $c_key)][$c_index] = $index_condition;
                                    }
                                break;
                                default:
                                    $index_cache[$this->client->config_index('pwa', 'policy_'. $c_key)][$c_index] = $c_settings;
                                break;
                            }
                        }

                        $index_policy[$this->client->config_index('pwa', 'policy_'. $key)] = $index_cache;
                    break;
                    case "match":
                        foreach ($value as $c_index => $condition) {
                            $index_match = array();
                            foreach ($condition as $condition_key => $c_value) {
                                if ($condition_key === 'type') {
                                    $c_value = $this->client->config_index('pwa', 'policy_' . $c_value);
                                }
                                $index_match[$this->client->config_index('pwa', 'policy_'. $condition_key)] = $c_value;
                            }
                            $value[$c_index] = $index_match;
                        }

                        $index_policy[$this->client->config_index('pwa', 'policy_'. $key)] = $value;
                    break;
                    case "strategy":
                        
                        $index_policy[$this->client->config_index('pwa', 'policy_'. $key)] = $this->client->config_index('pwa', 'policy_'. $value);
                    break;
                    default:
                        $index_policy[$this->client->config_index('pwa', 'policy_'. $key)] = $value;
                    break;
                }
                }
                $cache_policy[$index] = $index_policy;
            }
        } catch (Exception $err) {
            wp_die($err->getMessage());
        }

        $config = array();

        // cache policy
        $config[$this->client->config_index('pwa', 'policy')] = $cache_policy;

        // bypass policy
        if ($this->options->bool('pwa.bypass.enabled')) {
            $bypass_policy = $this->bypass_policy();
        } else {
            $bypass_policy = $this->bypass_policy(true);
        }

        $policy_index = array();
        foreach ($bypass_policy as $c_index => $condition) {
            $index_match = array();
            foreach ($condition as $condition_key => $c_value) {
                if ($condition_key === 'type') {
                    $c_value = $this->client->config_index('pwa', 'policy_' . $c_value);
                }
                $index_match[$this->client->config_index('pwa', 'policy_'. $condition_key)] = $c_value;
            }
            $policy_index[$c_index] = $index_match;
        }
        $config[$this->client->config_index('pwa', 'bypass')] = $policy_index;

        // background fetch policy
        if ($this->options->bool('pwa.background-fetch.enabled')) {
            $policies = $this->options->get('pwa.background-fetch.policy', array());

            $default_force = $this->options->bool('pwa.background-fetch.force');
            $default_timeout = $this->options->get('pwa.background-fetch.timeout');
            $default_startup_timeout = $this->options->get('pwa.background-fetch.startup_timeout');

            $policy_index = array();
            foreach ($policies as $index => $policy) {
                $policy_index[$index] = array();

                if (!isset($policy['force'])) {
                    $policy['force'] = $default_force;
                }
                if (!isset($policy['timeout'])) {
                    $policy['timeout'] = $default_timeout;
                }
                if (!isset($policy['startup_timeout'])) {
                    $policy['startup_timeout'] = $default_startup_timeout;
                }

                foreach ($policy as $key => $value) {
                    switch ($key) {
                        case "match":
                            $policy_index[$index][$this->client->config_index('pwa', 'policy_'. $key)] = array();
                            foreach ($value as $c_index => $condition) {
                                $index_match = array();
                                foreach ($condition as $condition_key => $c_value) {
                                    if ($condition_key === 'type') {
                                        $c_value = $this->client->config_index('pwa', 'policy_' . $c_value);
                                    }
                                    $index_match[$this->client->config_index('pwa', 'policy_'. $condition_key)] = $c_value;
                                }
                                $policy_index[$index][$this->client->config_index('pwa', 'policy_'. $key)][$c_index] = $index_match;
                            }
                        break;
                        default:
                            $policy_index[$index][$this->client->config_index('pwa', 'policy_'. $key)] = $value;
                        break;
                    }
                }
            }
            
            $config[$this->client->config_index('pwa', 'backgroundfetch')] = $policy_index;
        }

        // preload assets
        
        // apply filters
        $this->preload = apply_filters('o10n_sw_preload', $this->options->get('pwa.cache.preload', array()));

        if (!empty($this->preload)) {
            $config[$this->client->config_index('pwa', 'preload')] = $this->preload;

            $config[$this->client->config_index('pwa', 'preload_before_install')] = $this->options->bool('pwa.cache.preload_on_install');
        }

        if (isset($manifest['start_url']) && $manifest['start_url']) {
            $config[$this->client->config_index('pwa', 'start_url')] = $manifest['start_url'];
        }

        $cache_version = $this->options->get('pwa.cache.version');
        if ($cache_version) {
            $config[$this->client->config_index('pwa', 'cache_version')] = $cache_version;
        }

        $max_size = $this->options->get('pwa.cache.max_size');
        if ($max_size) {
            $config[$this->client->config_index('pwa', 'max_size')] = $max_size;
        }

        return $config;
    }

    /**
     * Update service worker file
     */
    final public function update_sw()
    {
        // PWA disabled, remove service worker
        if (!$this->options->bool('pwa.enabled')) {
            $sw_files = array(
                $this->sw_path . $this->sw_filename,
                $this->sw_path . $this->sw_worker_filename,
                $this->sw_path . $this->sw_filename_debug,
                $this->sw_path . $this->sw_worker_filename_debug,
                $this->sw_path . $this->sw_hash_filename
            );
            foreach ($sw_files as $file) {
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        } else {

            // config
            $config = json_encode($this->sw_config());

            $modes = array(false,true);
            foreach ($modes as $debug) {
                $sw_modules = array($this->core->modules('pwa')->dir_path() . 'public/js/sw' . (($debug) ? '.debug.js' : '.js'));

                // add cache digest module
                if ($this->options->bool('pwa.cache_digest')) {
                    $sw_modules[] = $this->core->modules('pwa')->dir_path() . 'public/js/sw-cache-digest' . (($debug) ? '.debug.js' : '.js');
                }

                // service worker source
                $header = "/**\n * Progressive Web App Optimization\n * @date ".date('Y-m-d H:i:s')."\n */\n";
                $source = '';

                foreach ($sw_modules as $file) {

                    // verify if source file exists
                    if (!file_exists($file)) {
                        throw new Exception('Service Worker source not found: ' . $this->file->safe_path($file), 'core', true);
                    }

                    $source .= str_replace('O10N_CONFIG', $config, file_get_contents($file)) . "\n";
                }
                
                // calculate file hash
                $file_hash = md5($source);
                $source = str_replace('O10N_SW_FILE_HASH', json_encode($file_hash), $header . $source);

                if ($this->options->bool('pwa.importScripts.enabled')) {
                    $scripts = $this->options->get('pwa.importScripts.scripts');
                    if (is_array($scripts) && !empty($scripts)) {
                        $source .= "\n\n/* Import scripts */\nimportScripts(".json_encode(implode(',', $scripts)).");";
                    }
                }
                
                // write service worker file
                $filename = ($debug) ? $this->sw_filename_debug : $this->sw_filename;
                try {
                    $this->file->put_contents($this->sw_path . $filename, $source);
                } catch (\Exception $e) {
                    throw new Exception('Failed to store service worker ' . $this->file->safe_path($this->sw_path . $filename) . ' <pre>'.$e->getMessage().'</pre>', 'config');
                }
                

                $worker_file = $this->core->modules('pwa')->dir_path() . 'public/js/sw-background-worker' . (($debug) ? '.debug.js' : '.js');
                if (!file_exists($worker_file)) {
                    throw new Exception('Service Worker worker source not found: ' . $this->file->safe_path($worker_file), 'core', true);
                }

                $source = "/**\n * Progressive Web App Optimization - Background Worker\n * @date ".date('Y-m-d H:i:s')."\n */\n";
                $source .= str_replace('O10N_CONFIG', $config, file_get_contents($worker_file)) . "\n";

                // write service worker background worker file
                $filename = ($debug) ? $this->sw_worker_filename_debug : $this->sw_worker_filename;
                try {
                    $this->file->put_contents($this->sw_path . $filename, $source);
                } catch (\Exception $e) {
                    throw new Exception('Failed to store service worker background worker ' . $this->file->safe_path($this->sw_path . $filename) . ' <pre>'.$e->getMessage().'</pre>', 'config');
                }

                // write hash file
                $filename = ($debug) ? $this->sw_hash_filename_debug : $this->sw_hash_filename;
                try {
                    $this->file->put_contents($this->sw_path . $filename, $file_hash);
                } catch (\Exception $e) {
                    throw new Exception('Failed to store service worker hash file ' . $this->file->safe_path($this->sw_path . $filename) . ' <pre>'.$e->getMessage().'</pre>', 'config');
                }

                // save hash to database
                update_option('o10n_pwa_sw_hash' . (($debug) ? '_debug' : ''), array($file_hash, time()), true);
            }
        }
    }

    /**
     * Return manifest.json JSON
     */
    final public function manifest_json($return_default = false)
    {
        if (file_exists($this->manifest_file)) {
            try {
                $manifest_json = $this->file->get_json($this->manifest_file, true);
            } catch (\Exception $err) {
                return array('error' => array( 'manifest.json contains invalid JSON', $err->getMessage()));
            }
        }

        // return default manifest.json
        if (empty($manifest_json) && $return_default) {
            $current_user = wp_get_current_user();
            $manifest_json = array(
                "short_name" => get_bloginfo('name', 'display'),
                "name" => get_bloginfo('name', 'display'),
                "orientation" => "landscape",
                'theme_color' => 'black',
                'background_color' => 'white',
                "developer" => array(
                    "name" => $current_user->display_name,
                    "url" => $current_user->user_url
                ),
                "start_url" => "/",
                "display" => "fullscreen",
                "icons" => array()
            );
        }

        return $manifest_json;
    }

    /**
     * Return bypass policy
     *
     * @param  bool  $return_default Return default policy
     * @return array Policy rules
     */
    final public function bypass_policy($return_default = false)
    {
        if (!$return_default && $this->options->bool('pwa.bypass.enabled')) {
            $bypass_policy = $this->options->get('pwa.bypass.policy', array());
        } else {
            $bypass_policy = array(

                // match request URL
                array(
                    'type' => 'url',
                    'pattern' => array(
                        
                        // WordPress admin area
                        '/wp-admin', '/wp-login.php',

                        // preview pages
                        '&preview=true', '&preview_nonce='
                    )
                ),

                // match referrer
                array(
                    'type' => 'header',
                    'name' => 'referrer',
                    'pattern' => array(
                        
                        // WordPress admin area
                        '/wp-admin', '/wp-login.php',

                        // preview pages
                        '&preview=true', '&preview_nonce='
                    )
                )
            );
        }

        return $bypass_policy;
    }

    /**
     * Attach assets to preload bundled with the page in the Service Worker
     */
    final public function attach_preload($urls)
    {
        if (!is_array($urls)) {
            $urls = array($urls);
        }

        foreach ($urls as $url) {
            if (!is_string($url)) {
                continue 1;
            }
            $url = trim($url);
            if ($url !== '' && !in_array($url, $this->preload_attachments)) {
                $this->preload_attachments[] = $url;
            }
        }
    }
}
