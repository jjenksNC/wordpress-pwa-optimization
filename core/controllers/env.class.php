<?php
namespace O10n;

/**
 * Environment Controller
 *
 * @package    optimization
 * @subpackage optimization/controllers
 * @author     Optimization.Team <info@optimization.team>
 */
if (!defined('ABSPATH')) {
    exit;
}

class Env extends Controller implements Controller_Interface
{
    private $debug_enabled = null; // optimization debug mode

    private $template_redirect_completed = false;

    private $is_ssl = null;

    private $enabled = null;
    private $disabled = false;

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
            'options'
        ));
    }

    /**
     * Setup controller
     */
    protected function setup()
    {

        // check environment on template_redirect hook
        add_action('template_redirect', array($this, 'template_redirect'), -10);
    }

    /**
     * Detect SSL
     */
    final public function is_ssl()
    {
        if (is_null($this->is_ssl)) {

            // detect SSL
            if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === '1' || strtolower($_SERVER['HTTPS']) === 'on')) {
                $this->is_ssl = true;
            } elseif (isset($_SERVER['SERVER_PORT']) && '443' === $_SERVER['SERVER_PORT']) {
                $this->is_ssl = true;
            } else {
                $this->is_ssl = false;
            }
        }
        
        return $this->is_ssl;
    }

    /**
     * Template redirect hook (required for is_feed() enabled check)
     */
    final public function template_redirect()
    {
        $this->template_redirect_completed = true;

        // set debug flag
        $this->is_debug(true);

        // update enabled flag
        $this->enabled(array_keys($this->core->modules()), false, true);
    }

    /**
     * Return enabled state
     *
     * @param  string|array $modules   Modules to verify
     * @param  bool         $use_cache Use cached enabled state
     * @return bool         Enabled state
     */
    final public function enabled($modules = false, $use_cache = true)
    {

        // disabled by filters
        if ($use_cache && $this->enabled === false) {
            return false;
        }

        if (!$use_cache || is_null($this->enabled)) {

            // global disabled
            if (defined('O10N_DISABLED') && O10N_DISABLED) {
                return $this->enabled = false;
            }

            // disabled by method
            if ($this->disabled) {
                return $this->enabled = false;
            }
            
            // disabled by filter
            $disabled = apply_filters('o10n_disabled', false);
            if ($disabled === true) {
                return $this->enabled = false;
            }

            // verify constants
            if (
                // disable for ajax / post requests
                defined('DOING_AJAX')
                || defined('WP_ADMIN')
                || defined('XMLRPC_REQUEST')
                || defined('DOING_CRON')
                || defined('APP_REQUEST')
                || (defined('SHORTINIT') && SHORTINIT)
            ) {
                return $this->enabled = false;
            }

            // WordPress methods
            if (
                // admin area
                is_admin()

                // feed
                || ($this->template_redirect_completed && function_exists('is_feed') && is_feed())

                || ($GLOBALS['pagenow'] === 'wp-login.php')
            ) {
                return $this->enabled = false;
            }
        }

        $this->enabled = true;

        // convert single module check to array
        if ($modules && is_string($modules)) {
            $modules = array($modules);
        }

        // verify module state
        if ($modules && !empty($modules)) {
            foreach ($modules as $module) {

                // get module controller
                $module_ctrl = $this->core->modules($module);

                // module disabled
                if (!$module_ctrl || !$module_ctrl->enabled($use_cache)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Return optimization enabled state
     *
     * @todo remove (old method)
     *
     * @return bool true/false
     */
    final public function is_optimization($reset = false)
    {
        return $this->enabled($false, !$reset);
    }

    /**
     * Debug enabled
     *
     * @return bool True/false
     */
    final public function is_debug($reset = false)
    {
        // return cached result
        if (!$reset && !is_null($this->debug_enabled)) {
            return $this->debug_enabled;
        }

        // enabled by constant
        if (defined('O10N_DEBUG') && O10N_DEBUG) {
            return $this->debug_enabled = true;
        }

        // enabled by filter
        $debug = apply_filters('o10n_debug', null);
        if (!is_null($debug)) {
            return $this->debug_enabled = $debug;
        }

        // verify config
        if (function_exists('current_user_can') && current_user_can('administrator') && $this->options->get('debug', false) === true) {
            return $this->debug_enabled = true;
        }

        return $this->debug_enabled = false;
    }

    /**
     * Plugin development mode
     */
    final public function is_dev()
    {
        return (defined('O10N_DEV') && O10N_DEV);
    }

    /**
     * Admin bar enabled?
     *
     * @return bool True/false
     */
    final public function admin_bar()
    {

        // verify config
        if ($this->options->get('admin_bar', true) === false) {
            return false;
        }

        return true; // enabled
    }

    /**
     * Disable optimization
     */
    final public function disable($state = true, $modules = false)
    {
        if ($modules) {
            if (is_string($modules)) {
                $modules = array($modules);
            }
            foreach ($modules as $module) {

                    // get module controller
                $module_ctrl = $this->core->modules($module);
                if ($module_ctrl) {
                    $module_ctrl->disable($state);
                }
            }

            return;
        }

        $this->disabled = $state;
        $this->enabled = ($state) ? false : true;
    }
}
