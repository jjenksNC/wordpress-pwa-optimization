=== PWA Optimization ===
Contributors: o10n
Donate link: https://github.com/o10n-x/
Tags: pwa, progressive web app, service worker, lighthouse, manifest.json, web app, manifest, mobile app
Requires at least: 4.0
Requires PHP: 5.4
Tested up to: 4.9.4
Stable tag: 0.0.11
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Advanced Progressive Web App and Service Worker optimization toolkit. Cache strategy, offline browsing, preloading, Google Lighthouse PWA validation and more.

== Description ==

This plugin is a toolkit for Progressive Web App and Service Worker optimization.

The plugin provides in a complete solution for service worker based performance optimization and enables to validate a website as a Progressive Web App (PWA) in [Google Lighthouse](https://developers.google.com/web/tools/lighthouse/).

The service worker can be configured with multiple cache strategies (cache first, network with fallback and event based), offline content, smart preloading, preload on mousedown to improve navigation speed and many more options to enable offline browsing and improved navigation performance.

The plugin provides a Web App `manifest.json` editor to easily optimize the web app configuration.

Additional features can be requested on the [Github forum](https://github.com/o10n-x/wordpress-pwa-optimization/issues).

**This plugin is a beta release.**

Documentation is available on [Github](https://github.com/o10n-x/wordpress-pwa-optimization/tree/master/docs).

== Installation ==

### WordPress plugin installation

1. Upload the `pwa-optimization/` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to the plugin settings page.
4. Configure PWA Optimization settings. Documentation is available on [Github](https://github.com/o10n-x/wordpress-pwa-optimization/tree/master/docs).

== Screenshots ==



== Changelog ==

= 0.0.11 =
* Bugfix: preload list not working.
* Bugfix: preload before install performed in background worker (should be service worker)
* Bugfix: Web App Manifest start url not preloaded automatically.

= 0.0.10 =
* Added: plugin update protection (plugin index).

= 0.0.9 =
* Bugfix: Background Fetch related client communication bug.

= 0.0.8 =
* Core update (see changelog.txt)

= 0.0.7 =
* Bugfix: Service Worker file not saved after previous update.
* Added: Smart Preload option: attach additional assets to Service Worker Fetch requests via `X-O10N-SW-PRELOAD` header or `O10n\attach_preload()` method. Sz`imilar advantage of HTTP/2 Server Push via Service Worker.
* Added: Background Fetch option: significantly improve Service Worker performance during fast navigation or on devices with a slow internet connection.

= 0.0.6 =
* Core update (see changelog.txt)

= 0.0.3 =
* Bugfix: client module not loaded when installed stand alone.
* Bugfix: localStorage not available when installed stand alone.

= 0.0.2 =
* Core update (see changelog.txt)

= 0.0.1 =

Beta release. Please provide feedback on [Github forum](https://github.com/o10n-x/wordpress-pwa-optimization/issues).

== Upgrade Notice ==

None.