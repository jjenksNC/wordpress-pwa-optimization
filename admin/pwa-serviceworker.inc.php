<?php
namespace O10n;

/**
 * Service Worker / Progressive Web App optimization admin template
 *
 * @package    optimization
 * @subpackage optimization/admin
 * @author     Optimization.Team <info@optimization.team>
 */
if (!defined('ABSPATH') || !defined('O10N_ADMIN')) {
    exit;
}

// print form header
$this->form_start(__('Progressive Web App Optimization', 'optimization'), 'pwa');
$global_scope = '';
$sw = array(
'filename' => '',
'scope' => ''
    );

$asset_policy = '';
?>
<table class="form-table">
    <tr valign="top">
        <th scope="row">Service Worker</th>
        <td>
            <label><input type="checkbox" name="o10n[pwa.enabled]" data-json-ns="1" value="1"<?php $checked('pwa.enabled'); ?> /> Enabled</label>
            <p class="description">Enable Service Worker optimization in browsers that support <a href="https://jakearchibald.github.io/isserviceworkerready/" target="_blank">Service Worker</a>. These optimizations enable progressive loading and to validate as a <a href="https://developers.google.com/web/progressive-web-apps/" target="_blank">Progressive Web App</a> (PWA).</p>

            <div class="suboption" data-ns="pwa"<?php $visible('pwa');  ?>>
                <label><input type="checkbox" name="o10n[pwa.register]" data-json-ns="1" value="1"<?php $checked('pwa.register'); ?> /> Register Service Worker</label>
                <p class="description">Unchecking this option enables to manually register the service worker.</p>
            </div>
            <div class="suboption" data-ns-hide="pwa"<?php $invisible('pwa');  ?>>
                <label><input type="checkbox" name="o10n[pwa.unregister]" value="1"<?php $checked('pwa.unregister'); ?> /> Unregister Service Worker</label>
                <p class="description">Unregister the PWA Service Worker for visitors.</p>
            </div>

            <div class="suboption" data-ns="pwa.register"<?php $visible('pwa.register');  ?>>
                <h5 class="h">&nbsp;Service Worker Scope</h5>
                <input type="text" size="40" name="o10n[pwa.scope]" min="1" placeholder="Leave blank for global scope<?php if ($global_scope) {
    echo esc_attr(' ('.$global_scope.')');
} ?>" value="<?php $value('pwa.scope'); ?>">
                <p class="description">Enter an optional <a href="https://developers.google.com/web/fundamentals/getting-started/primers/service-workers#register_a_service_worker" target="_blank">scope</a> for the service worker, e.g. <code>/blog/</code>. The scope restricts the Service Worker to a path.</p>
            </div>

            <div class="suboption" data-ns="pwa.register"<?php $visible('pwa.register');  ?>>

                <label><input type="checkbox" name="o10n[pwa.importScripts.enabled]" data-json-ns="1" value="1"<?php $checked('pwa.importScripts.enabled'); ?> /> Import scripts using <code>importScripts</code></label>
                <p class="description">This option enables to combine the PWA Service Worker with other service workers using <code>importScripts</code>, for example for <a href="<?php echo admin_url('plugin-install.php?s=push+notifications&tab=search&type=term'); ?>">Push Notifications</a>.</p>

                <div class="suboption" data-ns="pwa.importScripts"<?php $visible('pwa.importScripts');  ?>>
                    <h5 class="h">&nbsp;Scripts to import</h5>
                    <div id="pwa-importScripts-scripts"><div class="loading-json-editor"><?php print __('Loading JSON editor...', 'optimization'); ?></div></div>
                    <input type="hidden" class="json" name="o10n[pwa.importScripts.scripts]" data-json-type="json-array" data-json-editor-height="auto" data-json-editor-init="1" value="<?php print esc_attr($json('pwa.importScripts.scripts')); ?>" />
                    <p class="description">Enter a JSON array with script URL's.</p>
                </div>
            </div>
            
            <div class="suboption" data-ns="pwa"<?php $visible('pwa');  ?>>

                <div data-ns="pwa"<?php $visible('pwa');  ?>>
                    <label><input type="checkbox" name="o10n[pwa.bypass.enabled]" data-json-ns="1" value="1"<?php $checked('pwa.bypass.enabled'); ?> /> Custom bypass policy</label>
                    <p class="description">By default, the Service Worker is bypassed for requests from the WordPress admin area. You can provide custom rules for URLs that should not be processed by the Service Worker.</p>
                </div>
                <div class="suboption" data-ns="pwa.bypass"<?php $visible('pwa.bypass');  ?>>
                    <h5 class="h">&nbsp;Service Worker Bypass Policy</h5>
                    <div id="pwa-bypass-policy"><div class="loading-json-editor"><?php print __('Loading JSON editor...', 'optimization'); ?></div></div>
                    <input type="hidden" class="json" name="o10n[pwa.bypass.policy]" data-json-type="json-array" data-json-editor-height="auto" data-json-editor-init="1" value="<?php print esc_attr($json('pwa.bypass.policy', $view->pwa->bypass_policy(true))); ?>" />
                </div>

                <p class="info_yellow" style="margin-top:1em;font-size:14px;" id="edit"><strong><span class="dashicons dashicons-lightbulb"></span></strong> For debugging see <strong><a href="chrome://serviceworker-internals" target="_blank" style="color:#444;font-weight:normal;">chrome://serviceworker-internals</a></strong> in Chrome or <strong><a href="about:debugging#workers" target="_blank" style="color:#444;font-weight:normal;">about:debugging#workers</a></strong> in Firefox (copy in the address bar)</p>
            </div>

            <div class="suboption" data-ns="pwa"<?php $visible('pwa');  ?>>
                <label><input type="checkbox" name="o10n[pwa.verify.enabled]" data-json-ns="1" value="1"<?php $checked('pwa.verify.enabled'); ?> /> Verify Service Worker hash in background worker</label>
                <p class="description">The PWA configuration is stored in the service worker. It is therefor required that the service worker is updated when configuration changes. This option will verify the service worker hash in the background worker on each request and trigger an update when the hash changes.</p>

                <div class="suboption" data-ns="pwa.verify"<?php $visible('pwa.verify');  ?>>
                    <h5 class="h">&nbsp;Verify Interval</h5>
                    <input type="number" name="o10n[pwa.verify.update_interval]" min="1" placeholder="Always" value="<?php $value('pwa.verify.update_interval'); ?>" style="width:120px;">
                    <p class="description">Enter a time in seconds to verify the service worker hash file in the background worker. Leave blank to verify the hash on each request.</p>
                </div>
            </div>
        </td>
    </tr>
    <!--tr valign="top" data-ns="pwa"<?php $visible('pwa');  ?>>
        <th scope="row">Stream HTML</th>
        <td>
            <label><input type="checkbox" name="o10n[pwa.html.stream.enabled]" value="1"<?php $checked('pwa.html.stream.enabled'); ?> /> Enabled</label>
            <p class="description">Progressively stream HTML by cutting it in fragments with the option to load HTML asynchronously or when scrolled into view. (<a href="https://developers.google.com/web/updates/2016/06/sw-readablestreams" target="_blank">documentation by Google</a>)</p>

        </td>
    </tr-->
    <tr valign="top" data-ns="pwa"<?php $visible('pwa');  ?>>
        <th scope="row">Cache Pages</th>
        <td>
            <label><input type="checkbox" name="o10n[pwa.cache.pages.enabled]" data-json-ns="1" value="1"<?php $checked('pwa.cache.pages.enabled'); ?> /> Enabled</label>
            <p class="description">Cache HTML pages in the service worker. This option enables to make a website available offline.</p>

            <div class="info_yellow" data-ns="pwa.cache.pages"<?php $visible('pwa.cache.pages'); ?>><strong>Note:</strong> The page cache policy form simply adds a rule to the <strong>Cache Policy</strong>. You can disable this option and manage the page cache policy manually using the Cache Policy editor.</div>

            <p data-ns="pwa.cache.pages"<?php $visible('pwa.cache.pages'); ?>>
                <label><input type="checkbox" value="1" name="o10n[pwa.cache.pages.match.enabled]" data-json-ns="1"<?php $checked('pwa.cache.pages.match.enabled'); ?> /> Custom match policy</label>
                <p class="description">By default HTML pages are matched based on the HTTP accept request header.</p>
            </p>
            <div class="suboption" data-ns="pwa.cache.pages.match"<?php $visible('pwa.cache.pages.match'); ?>>

                <h5 class="h">&nbsp;Custom Page Match Policy</h5>
                <div id="pwa-cache-pages-match-policy"><div class="loading-json-editor"><?php print __('Loading JSON editor...', 'optimization'); ?></div></div>
                <input type="hidden" class="json" name="o10n[pwa.cache.pages.match.policy]" data-json-type="json-array" data-json-editor-height="auto" data-json-editor-init="1" value="<?php print esc_attr($json('pwa.cache.pages.match.policy', array(
                    array( 'type' => 'header', 'name' => 'Accept', 'pattern' => 'text/html')
                ))); ?>" />

            </div>
        </td>
    </tr>
    <tr valign="top" data-ns="pwa.cache.pages"<?php $visible('pwa.cache.pages'); ?>>
        <th scope="row">&nbsp;</th>
        <td style="padding-top:0px;">
            <div>
                <h5 class="h">&nbsp;Page Cache Strategy</h5>
                <select name="o10n[pwa.cache.pages.strategy]" data-ns-change="pwa.cache.pages" data-json-default="<?php print esc_attr(json_encode('network')); ?>">
                    <option value="network"<?php $selected('pwa.cache.pages.strategy', 'network'); ?>>Network &rarr; Cache</option>
                    <option value="cache"<?php $selected('pwa.cache.pages.strategy', 'cache'); ?>>Cache &rarr; Network</option>
                    <option value="event"<?php $selected('pwa.cache.pages.strategy', 'event'); ?>>On demand (event based)</option>
                </select>
            </div>
            <div class="clearfix" style="clear:both;"></div>
            <p class="description">By default HTML pages are fetched from the network with the cache as a fallback for when the network fails. Select the Cache First strategy to serve pages from cache. The On demand strategy enables to use a Cache First strategy with a manual (event based) cache population (e.g. "click to read this page offline"). The API is <code>o10n.cache(urls);</code>. (<a href="javascript:void(0);" onclick="jQuery('#precache_example').fadeToggle();">show example</a>)</p>

            <pre style="display:none;padding:10px;border:solid 1px #efefef;" id="precache_example">o10n.cache(['/shop/','/shop/product1.html','/wp-content/uploads/.../product-image.jpg'])
.then(function(status) {
    console.log('Assets stored in cache and available offline', status);
});</pre>
        </td>
    </tr>
    <tr valign="top" data-ns="pwa.cache.pages"<?php $visible('pwa.cache.pages', (in_array($get('pwa.cache.pages.strategy'), array('cache','event')))); ?> data-ns-condition="<?php print esc_attr('pwa.cache.pages.strategy==["cache","event"]'); ?>">
        <th scope="row">&nbsp;</th>
        <td style="padding-top:0px;">
            <div>
                <h5 class="h">&nbsp;Cache Update Interval</h5>
                <input type="number" name="o10n[pwa.cache.pages.cache.update_interval]" min="1" placeholder="Always" value="<?php $value('pwa.cache.pages.cache.update_interval'); ?>" style="width:120px;">
                <p class="description">Enter a time in seconds to update cached pages using the network. Leave blank to update the cache on each request.</p>
            </div>
            <div class="suboption">
                <h5 class="h">&nbsp;Cache Max Age</h5>
                <input type="number" name="o10n[pwa.cache.pages.cache.max_age]" min="1" value="<?php $value('pwa.cache.pages.cache.max_age'); ?>" style="width:120px;">
                <p class="description">Enter a expire time in seconds. The maximum age does not override HTTP expire headers.</p>
            </div>
            <div class="suboption">
                <label><input type="checkbox" name="o10n[pwa.cache.pages.cache.head_update]" value="1"<?php $checked('pwa.cache.pages.cache.head_update'); ?> /> HEAD based network update</label>
                <p class="description">Use a HTTP HEAD request and <code>etag</code> and/or <code>last-modified</code> header verification to update the cache. This option saves bandwidth while enabling quick updates of changed content, however, it adds an extra request for content that always changes.</p>
            </div>
            <div class="suboption">
                <label><input type="checkbox" name="o10n[pwa.cache.pages.mousedown]" data-json-ns="1" value="1"<?php $checked('pwa.cache.pages.mousedown'); ?> /> Preload on Mouse Down</label>
                <p class="description">Start preloading navigation requests in the Service Worker on mouse down. Older mobile devices such as iOS8 have a <a href="https://encrypted.google.com/search?q=300ms+tap+delay+mobile" target="_blank" rel="noopener">300ms click delay</a> which is a lot of time wasted for navigation clicks. An average mouse click also has a 200-500ms delay before navigation starts. This feature enables to make use of the otherwise wasted delay.</p>
            </div>
        </td>
    </tr>
    <tr valign="top" data-ns="pwa.cache.pages"<?php $visible('pwa.cache.pages');  ?>>
        <th scope="row">&nbsp;</th>
        <td style="padding-top:0px;">

            <h5 class="h">&nbsp;Page Cache Conditions</h5>
            <div id="pwa-cache-pages-cache-conditions"><div class="loading-json-editor"><?php print __('Loading JSON editor...', 'optimization'); ?></div></div>
            <input type="hidden" class="json" name="o10n[pwa.cache.pages.cache.conditions]" data-json-type="json-array" data-json-editor-height="auto" data-json-editor-init="1" value="<?php print esc_attr($json('pwa.cache.pages.cache.conditions', json_decode('[{
    "type": "header",
    "name": "content-type",
    "pattern": "text/html"
}]', true))); ?>" />
            <p class="description">Optionally, enter cache conditions to restrict caching to specific pages. (<a href="javascript:void(0);" onclick="jQuery('#page_cache_conditions_example').fadeToggle();">show example</a>)</p>
            <div class="info_yellow" id="page_cache_conditions_example" style="display:none;"><strong>Example:</strong> <pre class="clickselect" title="<?php print esc_attr('Click to select', 'optimization'); ?>" style="cursor:copy;padding: 10px;margin: 0 1px;margin-top:5px;font-size: 13px;">[{
    "type": "url",
    "pattern": ["page1.html","page2.html"],
    "exclude": true
}, {
    "type": "url",
    "pattern": "page([0-9]+)\\.html",
    "regex": true
}, {
    "type": "header",
    "name": "content-type",
    "pattern": "text/html"
}, {
    "type": "header",
    "name": "content-length",
    "pattern": {
        "operator": "<",
        "value": 14336
    },
    "required": true
}]</pre></div>
        </td>
    </tr>
    <tr valign="top" data-ns="pwa.cache.pages"<?php $visible('pwa.cache.pages');  ?>>
        <th scope="row">&nbsp;</th>
        <td style="padding-top:0px;">
            <h5 class="h">&nbsp;Offline Page</h5>
            <select id="offline_page" class="wp-pageselect" name="o10n[pwa.cache.pages.offline]" size="80" placeholder="/path/to/offline.html"><option>Loading...</option></select>
            <p class="description">Enter an URL or absolute path to a HTML page or asset to display when the network is offline and when the requested page is not available in cache.</p>
        </td>
    </tr>
    <tr valign="top" data-ns="pwa.cache.pages"<?php $visible('pwa.cache.pages');  ?>>
        <td style="padding:0px;" colspan="2">

            
<?php
submit_button(__('Save'), 'primary large', 'is_submit', false);
?>
        </td>
    </tr>
    <tr valign="top" data-ns="pwa"<?php $visible('pwa');  ?>>
        <th scope="row">Cache Policy</th>
        <td>
            <div id="pwa-cache-policy"><div class="loading-json-editor"><?php print __('Loading JSON editor...', 'optimization'); ?></div></div>
            <input type="hidden" class="json" name="o10n[pwa.cache.policy]" data-json-type="json-array" data-json-editor-height="auto" data-json-editor-height="auto" data-json-editor-init="1" value="<?php print esc_attr($json('pwa.cache.policy')); ?>" />

            <p class="description">Enter a JSON array with cache policy objects. (<a href="javascript:void(0);" onclick="jQuery('#cache_policy_example').fadeToggle();">show example</a>)</p>
            <div class="info_yellow" id="cache_policy_example" style="display:none;"><strong>Example:</strong> <pre class="clickselect" title="<?php print esc_attr('Click to select', 'optimization'); ?>" style="cursor:copy;padding: 10px;margin: 0 1px;margin-top:5px;font-size: 13px;">[
  {
    "title": "Match images",
    "match": [
      {
        "type": "header",
        "name": "Accept",
        "pattern": "image/"
      },
      {
        "exclude": true,
        "type": "header",
        "name": "Accept",
        "pattern": "text/html"
      },
      {
        "exclude": true,
        "type": "url",
        "pattern": "google-analytics.com/collect"
      }
    ],
    "strategy": "cache",
    "cache": {
      "update_interval": 3600,
      "head_update": true,
      "conditions": [
        {
          "type": "header",
          "name": "content-length",
          "pattern": {
            "operator": "<",
            "value": 35840
          }
        }
      ]
    },
    "offline": "/path/to/offline.png"
  },
  {
    "title": "Match assets",
    "match": [
      {
        "type": "url",
        "pattern": "/\\.(css|js|woff|woff2|ttf|otf|eot)(\\?.*)?$/i",
        "regex": true
      }
    ],
    "strategy": "cache",
    "cache": {
      "update_interval": 300,
      "head_update": true,
      "max_age": 86400
    }
  }
]</pre></div>
        </td>
    </tr>
    <tr valign="top" data-ns="pwa"<?php $visible('pwa');  ?>>
        <th scope="row">Cache Version</th>
        <td>
            <input type="text" name="o10n[pwa.cache.version]" size="20" value="<?php $value('pwa.cache.version'); ?>">
            <p class="description">Optionally enter a cache version. This feature enables to invalidate existing caches.</p>
        </td>
    </tr>
    <tr valign="top" data-ns="pwa"<?php $visible('pwa');  ?>>
        <th scope="row">Cache Max Size</th>
        <td>
            <input type="text" name="o10n[pwa.cache.max_size]" size="20" min="10" value="<?php $value('pwa.cache.max_size'); ?>" placeholder="1000">
            <p class="description">Maximum cache entries to maintain. The default is 1000.</p>
        </td>
    </tr>
    <tr valign="top" data-ns="pwa"<?php $visible('pwa');  ?>>
        <th scope="row">Cache Preload</th>
        <td>
            <div id="pwa-cache-preload"><div class="loading-json-editor"><?php print __('Loading JSON editor...', 'optimization'); ?></div></div>
            <input type="hidden" class="json" name="o10n[pwa.cache.preload]" data-json-type="json-array" data-json-editor-height="auto" data-json-editor-init="1" value="<?php $value('pwa.cache.preload'); ?>" />
            <p class="description">Enter URLs or absolute path's to preload for offline availability, e.g. <code>/path/to/page.html</code> or <code>/path/to/image.jpg</code>.</p>

            <p class="suboption"><label><input type="checkbox" name="o10n[pwa.cache.preload_on_install]" value="1"<?php $checked('pwa.cache.preload_on_install'); ?> /> Require preloading to complete in Service Worker installation. This option will activate the service worker after all assets have been preloaded.</label></p>
        </td>
    </tr>
    <tr valign="top" data-ns="pwa"<?php $visible('pwa');  ?>>
        <th scope="row">Smart Preloading</th>
        <td>
                <p class="description">When a page or asset is loaded by the Service Worker you can automatically preload additional assets via the <code>X-O10N-SW-PRELOAD: asset, asset [, ...]</code> header. You can control the header using the method <code>O10n\attach_preload()</code>.(<a href="javascript:void(0);" onclick="jQuery('#preload_header_example').fadeToggle();">show example</a>)</p>
            <div class="info_yellow" id="preload_header_example" style="display:none;"><strong>Example:</strong> <pre class="clickselect" title="<?php print esc_attr('Click to select', 'optimization'); ?>" style="cursor:copy;padding: 10px;margin: 0 1px;margin-top:5px;font-size: 13px;">
/* Attach assets to page for smart preloading in the Service Worker */
add_action('init', function() {

    if (function_exists('O10n\attach_preload')) {

        // attach single asset to page
        O10n\attach_preload('/path/to/image.jpg');

        // attach multiple assets to page
        O10n\attach_preload(array('/path/to/image.jpg', 'https://cdn.google.com/script.js'));

    }

});
</pre></div>

        <p clas="description">A use case would be to preload above the fold images for a page. It also makes it more easy to make a website fully available offline by simply preloading page URLs that automatically preload any relevant attached resources.</p>

        </td>
    </tr>
    <tr valign="top" data-ns="pwa"<?php $visible('pwa');  ?>>
        <th scope="row">Background Fetch</th>
        <td>
            <label><input type="checkbox" name="o10n[pwa.background-fetch.enabled]" data-json-ns="1" value="1"<?php $checked('pwa.background-fetch.enabled'); ?> /> Enabled</label>
            <p class="description">A performance flaw of Service Workers is that they initiate Fetch requests that cannot be cancelled when a user wants to navigate which can slow and even block navigation.</p>
            <p class="description">This plugin provides a unique innovation in which Fetch requests can be performed in a regular Web Worker that can be terminated when a user wants to navigate, instantly cancelling any Fetch requests or background tasks.</p>

            <div data-ns="pwa.background-fetch" <?php $visible('pwa.background-fetch');  ?>>
                <div class="suboption" id="pwa-background-fetch-policy"><div class="loading-json-editor"><?php print __('Loading JSON editor...', 'optimization'); ?></div></div>
                <input type="hidden" class="json" name="o10n[pwa.background-fetch.policy]" data-json-type="json-array" data-json-editor-height="auto" data-json-editor-init="1" value="<?php $value('pwa.background-fetch.policy', '[
  {
    "title": "Match all",
    "match": [
      {
        "type": "url",
        "pattern": ".*",
        "regex": true
      }
    ]
  }
]'); ?>" />
                <p class="description">Enter a policy configuration for resources that should be Fetched in the background worker. (<a href="javascript:void(0);" onclick="jQuery('#background_fetch_example').fadeToggle();">show example</a>)</p>
            <div class="info_yellow" id="background_fetch_example" style="display:none;"><strong>Example:</strong> <pre class="clickselect" title="<?php print esc_attr('Click to select', 'optimization'); ?>" style="cursor:copy;padding: 10px;margin: 0 1px;margin-top:5px;font-size: 13px;">[
  {
    "title": "Match images",
    "match": [
      {
        "type": "header",
        "name": "Accept",
        "pattern": "image/"
      },
      {
        "exclude": true,
        "type": "header",
        "name": "Accept",
        "pattern": "text/html"
      },
      {
        "exclude": true,
        "type": "url",
        "pattern": "important-above-the-fold-image.jpg"
      }
    ],
    "force": true
  }
]</pre></div>

                <p class="suboption"><label><input type="checkbox" name="o10n[pwa.background-fetch.force]" value="1"<?php $checked('pwa.background-fetch.force'); ?> /> Force background Fetch</label></p>
                <p class="description">Web Workers have a start-up latency of ~100ms. By default, Fetch requests are only handled by the background worker when the Web Worker is ready and before then Fetch requests are handled by the Service Worker. This option enables to wait for the Web Worker to be available before Fetch requests are processed. You can configure this option for individual assets in the policy configuration.</p>

                <div class="suboption">
                    <h5 class="h">&nbsp;Fetch Timeout</h5>
                    <input type="number" name="o10n[pwa.background-fetch.timeout]" min="1" placeholder="5000" value="<?php $value('pwa.background-fetch.timeout'); ?>" style="width:120px;">
                    <p class="description">Enter a time in milliseconds to wait for the background worker to resolve. The default is 5000ms.</p>
                </div>

                <div class="suboption">
                    <h5 class="h">&nbsp;Startup Timeout</h5>
                    <input type="number" name="o10n[pwa.background-fetch.startup_timeout]" min="1" placeholder="1000" value="<?php $value('pwa.background-fetch.startup_timeout'); ?>" style="width:120px;">
                    <p class="description">Enter a time in milliseconds to wait for the Web Worker to be ready. The default is 1000ms.</p>
                </div>
            </div>
        </td>
    </tr>
    <tr valign="top" data-ns="pwa"<?php $visible('pwa');  ?>>
        <th scope="row">CSS online/offline class</th>
        <td>
            <label><input type="checkbox" name="o10n[pwa.offline_class]" value="1"<?php $checked('pwa.offline_class'); ?> /> Enabled</label>
            <p class="description">Add the class <code>offline</code> to <code>&lt;body&gt;</code> based on <a href="https://developer.mozilla.org/en-US/docs/Online_and_offline_events" target="_blank">HTML5 online/offline events</a>. This feature enables to add a user friendly notice via CSS when the connection is offline. This option also enables the API <code>o10n.on('offline', fn);</code>.</p>
        </td>
    </tr>
</table>

<p class="suboption info_yellow"><strong><span class="dashicons dashicons-lightbulb"></span></strong> You can enable debug modus by adding <code>define('O10N_DEBUG', true);</code> to wp-config.php. The browser console will show details about the service worker.</p>

<hr />
<?php
    submit_button(__('Save'), 'primary large', 'is_submit', false);

// print form header
$this->form_end();
