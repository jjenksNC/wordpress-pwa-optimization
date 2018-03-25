<?php
namespace O10n;

/**
 * Web App Manifest admin template
 *
 * @package    optimization
 * @subpackage optimization/admin
 * @author     Optimization.Team <info@optimization.team>
 */
if (!defined('ABSPATH') || !defined('O10N_ADMIN')) {
    exit;
}

// print form header
$this->form_start(__('Web App Manifest Optimization', 'optimization'), 'pwa');

?>
<p>The Web App Manifest is a JSON document that enables to control how a website app appears to the user in areas where they would expect to see apps. It is required to validate as PWA. (<a href="https://developers.google.com/web/fundamentals/engage-and-retain/web-app-manifest/" target="_blank">documentation</a>)</p>
<table class="form-table">
    <tr valign="top">
        <th scope="row">Web App Manifest<a name="manifest">&nbsp;</a></th>
        <td>
            <div id="pwa-manifest"><div class="loading-json-editor"><?php print __('Loading JSON editor...', 'optimization'); ?></div></div>
            <input type="hidden" class="json" name="o10n[pwa.manifest]" data-json-type="json" data-json-editor-height="auto" data-json-editor-mode="tree" data-json-editor-init="1" value="<?php print esc_attr(json_encode($view->pwa->manifest_json())); ?>" />
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">Web App Meta</th>
        <td>
            <textarea class="json-array-lines" id="pwa_meta_editor" name="o10n[pwa.meta]"><?php $value('pwa.meta', '<link rel="manifest" href="/manifest.json">'); ?></textarea>
            <p class="description">Enter Web App related meta tags to include in the <code>&lt;head&gt;</code> of the page. (<a href="https://developers.google.com/web/ilt/pwa/lab-auditing-with-lighthouse#43_add_tags_for_other_browsers" target="_blank">documentation</a>).</p>

            <p class="info_yellow" style="margin-top:1em;"><strong><span class="dashicons dashicons-lightbulb"></span></strong> Use the Google Chrome <strong>Application &gt; Manifest</strong> tab to debug the settings and to simulate <em>Add to homescreen</em>.</p> 

        </td>
    </tr>
</table>

<hr />
<?php
    submit_button(__('Save'), 'primary large', 'is_submit', false);

// print form header
$this->form_end();
