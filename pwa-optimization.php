<?php
namespace O10n;

/**
 * HTTP/2 Optimization
 *
 * Advanced HTTP/2 optimization toolkit. HTTP/2 Server Push, Service Worker based Cache-Digest and more.
 *
 * @link              https://github.com/o10n-x/
 * @package           o10n
 *
 * @wordpress-plugin
 * Plugin Name:       PWA Optimization
 * Description:       Advanced Progressive Web App optimization toolkit. Service Worker, HTML fragment streaming, HTTP/2 Cache-Digest calculation, Web App Manifest editor and more.
 * Version:           0.0.11
 * Author:            Optimization.Team
 * Author URI:        https://optimization.team/
 * Text Domain:       o10n
 * Domain Path:       /languages
 */

if (! defined('WPINC')) {
    die;
}

// abort loading during upgrades
if (defined('WP_INSTALLING') && WP_INSTALLING) {
    return;
}

// settings
$module_version = '0.0.11';
$minimum_core_version = '0.0.39';
$plugin_path = dirname(__FILE__);

// load the optimization module loader
if (!class_exists('\O10n\Module')) {
    require $plugin_path . '/core/controllers/module.php';
}

// load module
new Module(
    'pwa',
    'PWA Optimization',
    $module_version,
    $minimum_core_version,
    array(
        'core' => array(
            'tools',
            'client',
            'http',
            'pwa'
        ),
        'admin' => array(
            'AdminPwa'
        )
    ),
    false,
    array(),
    __FILE__
);

// load public functions in global scope
require $plugin_path . '/includes/global.inc.php';
