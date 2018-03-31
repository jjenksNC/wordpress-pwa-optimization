<?php
namespace O10n;

/**
 * Progressive Web App (PWA) Optimization Admin View Controller
 *
 * @package    optimization
 * @subpackage optimization/controllers/admin
 * @author     PageSpeed.pro <info@pagespeed.pro>
 */
if (!defined('ABSPATH')) {
    exit;
}

class AdminViewPwa extends AdminViewBase
{
    protected static $view_key = 'pwa'; // reference key for view
    protected $module_key = 'pwa';

    // default tab view
    private $default_tab_view = 'intro';

    /**
     * Load controller
     *
     * @param  Core       $Core Core controller instance.
     * @param  string     $View View key.
     * @return Controller Controller instance.
     */
    public static function &load(Core $Core)
    {
        // instantiate controller
        return parent::construct($Core, array(
            'json',
            'file',
            'pwa',
            'AdminClient'
        ));
    }
    
    /**
     * Setup controller
     */
    protected function setup()
    {
        // set view etc
        parent::setup();
    }

    /**
     * Return help tab data
     */
    final public function help_tab()
    {
        $data = array(
            'name' => __('PWA Optimization', 'o10n'),
            'github' => 'https://github.com/o10n-x/wordpress-pwa-optimization',
            'wordpress' => 'https://wordpress.org/support/plugin/pwa-optimization',
            'docs' => 'https://github.com/o10n-x/wordpress-pwa-optimization/tree/master/docs'
        );

        return $data;
    }

    /**
     * Setup view
     */
    public function setup_view()
    {
        // process form submissions
        add_action('o10n_save_settings_verify_input', array( $this, 'verify_input' ), 10, 1);

        // enqueue scripts
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), $this->first_priority);
    }

    /**
     * Enqueue scripts and styles
     */
    final public function enqueue_scripts()
    {
        // skip if user is not logged in
        if (!is_admin() || !is_user_logged_in()) {
            return;
        }

        // set module path
        $this->AdminClient->set_config('module_url', $this->module->dir_url());

        // global admin script
        wp_enqueue_script('o10n_view_pwa', $this->module->dir_url() . 'admin/js/view-pwa.js', array( 'jquery', 'o10n_cp' ), $this->module->version());
    }


    /**
     * Return view template
     */
    public function template($view_key = false)
    {
        // template view key
        $view_key = false;

        $tab = (isset($_REQUEST['tab'])) ? trim($_REQUEST['tab']) : $this->default_tab_view;
        if ($tab) {
            switch ($tab) {
                case "intro":
                case "serviceworker":
                case "manifest":
                    $view_key = 'pwa-' . $tab;
                break;
                default:
                    throw new Exception('Invalid view ' . esc_html($view_key), 'core');
                break;
            }
        }

        return parent::template($view_key);
    }
    
    /**
     * Verify settings input
     *
     * @param  object   Form input controller object
     */
    final public function verify_input($forminput)
    {
        // PWA Optimization

        $tab = (isset($_REQUEST['tab'])) ? trim($_REQUEST['tab']) : 'serviceworker';
        switch ($tab) {
            case "serviceworker":

                $forminput->type_verify(array(
                    'pwa.enabled' => 'bool',
                    'pwa.link_manifest' => 'bool'
                ));

                if ($forminput->bool('pwa.enabled')) {
                    $forminput->type_verify(array(
                        'pwa.register' => 'bool',
                        'pwa.unregister' => 'bool',
                        'pwa.scope' => 'string',

                        //'pwa.html.stream.enabled' => 'bool',

                        'pwa.importScripts.enabled' => 'bool',

                        'pwa.bypass.enabled' => 'bool',

                        'pwa.verify.enabled' => 'bool',

                        'pwa.cache.pages.enabled' => 'bool',

                        'pwa.cache.pages.match.enabled' => 'bool',

                        'pwa.cache.policy' => 'json-array',

                        'pwa.cache.version' => 'string',
                        'pwa.cache.max_size' => 'int',
                        'pwa.cache.preload' => 'json-array',
                        'pwa.cache.preload_on_install' => 'bool',

                        'pwa.background-fetch.enabled' => 'bool',

                        'pwa.bypass.enabled' => 'bool'
                    ));

                    if ($forminput->bool('pwa.background-fetch.enabled')) {
                        $forminput->type_verify(array(
                            'pwa.background-fetch.policy' => 'json-array',
                            'pwa.background-fetch.force' => 'bool',
                            'pwa.background-fetch.timeout' => 'int-empty',
                            'pwa.background-fetch.startup_timeout' => 'int-empty'
                        ));
                    }

                    if ($forminput->bool('pwa.importScripts.enabled')) {
                        $forminput->type_verify(array(
                            'pwa.importScripts.scripts' => 'json-array'
                        ));
                    }

                    if ($forminput->bool('pwa.bypass.enabled')) {
                        $forminput->type_verify(array(
                            'pwa.bypass.policy' => 'json-array'
                        ));
                    }

                    if ($forminput->bool('pwa.verify.enabled')) {
                        $forminput->type_verify(array(
                            'pwa.verify.update_interval' => 'int'
                        ));
                    }

                    if ($forminput->bool('pwa.cache.pages.enabled')) {
                        $forminput->type_verify(array(
                            'pwa.cache.pages.strategy' => 'string',
                            'pwa.cache.pages.offline' => 'string',
                            'pwa.cache.pages.mousedown' => 'bool'
                        ));
                    }

                    if ($forminput->bool('pwa.cache.pages.match.enabled')) {
                        $forminput->type_verify(array(
                            'pwa.cache.pages.match.policy' => 'json-array'
                        ));
                    }

                    if (in_array($forminput->get('pwa.cache.pages.strategy'), array('cache','event'))) {
                        $forminput->type_verify(array(
                            'pwa.cache.pages.cache.update_interval' => 'int',
                            'pwa.cache.pages.cache.max_age' => 'int',
                            'pwa.cache.pages.cache.head_update' => 'bool',
                            'pwa.cache.pages.cache.notify' => 'bool',
                            'pwa.cache.pages.cache.conditions' => 'json-array'
                        ));
                    }
                } else {

                    // remove service worker files
                    $path = trailingslashit(ABSPATH);
                    $sw_files = array(
                        'o10n-sw.js',
                        'o10n-sw.debug.js',
                        'o10n-sw-config.json'
                    );
                    foreach ($sw_files as $file) {
                        if (file_exists($path . $file)) {
                            @unlink($path . $file);
                        }
                    }
                }

                // update service worker with new config
                $this->pwa->update_sw();

            break;
            case "manifest":

                $forminput->type_verify(array(
                    'pwa.meta' => 'string'
                ));

                // update manifest.json
                $manifest_file = trailingslashit(ABSPATH) . 'manifest.json';
            
                $manifest_json = $forminput->get('pwa.manifest');

                // schema json
                try {
                    $manifest_json = $this->json->parse($manifest_json, true);
                } catch (\Exception $err) {
                    // invalid JSON
                    $manifest_json = false;
                    $forminput->error('manifest.json', $err);
                }

                if ($manifest_json) {

                    // verify JSON
                    if ($forminput->verify_json(array('pwa.manifest' => $manifest_json))) {
                        if (isset($manifest_json['start_url'])) {
                        }

                        // add service worker
                        $manifest_json['serviceworker'] = array(
                            'src' => $this->pwa->sw_url_path(),
                            'use_cache' => true
                        );
                        if (isset($input['pwa_scope']) && trim($input['pwa_scope']) !== '') {
                            $manifest_json['serviceworker']['scope'] = $input['pwa_scope'];
                        }

                        // beautify JSON
                        $manifest_json = json_encode($manifest_json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

                        try {
                            $this->file->put_contents($manifest_file, $manifest_json);
                        } catch (\Exception $err) {
                        }
                    
                        // verify contents
                        if (!file_exists($manifest_file) || file_get_contents($manifest_file) !== $manifest_json) {
                            $forminput->error('manifest.json', 'Failed to write to <strong>manifest.json</strong>. '. (($err) ? $err->getMessage() : ''));
                        }
                    }
                }

                // update service worker with new config
                $this->pwa->update_sw();
            break;
            default:
                throw new Exception('Invalid view ' . esc_html($tab), 'core');
            break;
        }
    }
}
