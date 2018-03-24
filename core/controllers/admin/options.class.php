<?php
namespace O10n;

/**
 * Admin Options Controller
 *
 * @package    optimization
 * @subpackage optimization/controllers/admin
 * @author     Optimization.Team <info@optimization.team>
 */
if (!defined('ABSPATH')) {
    exit;
}

class AdminOptions extends Controller implements Controller_Interface
{
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
    }

    /**
     * Delete settings from options
     */
    final public function delete($keys)
    {
        if (is_string($keys)) {
            $keys = array($keys);
        }

        // get options
        $options = $this->options->get();

        // remove settings
        foreach ($keys as $key) {
            if (isset($options[$key])) {
                unset($options[$key]);
            }
        }

        // save
        $this->save($options, true);
    }

    /**
     * Save settings to options
     */
    final public function save($settings, $replace = false)
    {
        if (!is_array($settings)) {
            throw new Exception('Options to save not array.', 'settings');
        }

        if (!$replace) {

            // get options
            $options = $this->options->get();

            // add options to save
            $options = array_merge($options, $settings);
        } else {
            $options = $settings;
        }

        // store update count
        if (!isset($options['update_count'])) {
            $options['update_count'] = 0;
        }
        $options['update_count']++;

        // save
        update_option('o10n', $options, true);

        // update cache
        $this->options->update($options);
    }
}
