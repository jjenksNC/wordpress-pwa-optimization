<?php
namespace O10n;

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://github.com/o10n-x/
 * @package    optimization
 */

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$plugin_path = dirname(__FILE__);

// load uninstall controller
if (!class_exists('\O10n\Uninstall')) {
    require $plugin_path . '/core/controllers/uninstall.class.php';
}

// start uninstaller
$uninstaller = new Uninstall('pwa');

// delete options
delete_option('o10n_pwa_sw_hash');

// delete settings
$uninstaller->delete_settings('pwa');

// delete cache tables
$uninstaller->delete_tables();
