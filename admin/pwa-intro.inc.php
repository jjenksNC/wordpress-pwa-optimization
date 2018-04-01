<?php
/**
 * Intro admin template
 *
 * @package    optimization
 * @subpackage optimization/admin
 * @author     Optimization.Team <info@optimization.team>
 */

if (!defined('ABSPATH')) {
    exit;
}

$module_name = $view->module->name();
$module_version = $view->module->version();

?>
<div class="wrap">

	<div class="metabox-prefs">
		<div class="wrap about-wrap" style="position:relative;">
			<div style="float:right;">
				<a href="https://developers.google.com/web/progressive-web-apps/" target="_blank" rel="noopener"><img src="<?php print $view->module->dir_url(); ?>admin/images/pwa.png" alt="Progressive Web App" width="180" height="180" border="0" style="float:right;"></a>
			</div>
			<h1><?php print $module_name; ?> <?php print $module_version; ?></h1>

			<p class="about-text" style="min-height:inherit;">Thank you for using the <?php print $module_name; ?> plugin by <a href="https://github.com/o10n-x/" target="_blank" rel="noopener" style="color:black;text-decoration:none;">Optimization.Team</a></p>
			
			<p class="about-text" style="min-height:inherit;">This plugin is a toolkit for advanced Progressive Web App and Service Worker optimization for WordPress.</p>

			<p class="about-text info_yellow" style="min-height:inherit;"><strong>Warning:</strong> This plugin is intended for optimization professionals and advanced WordPress users.</p>

			<p class="about-text" style="min-height:inherit;">Getting started? Read <a href="https://developers.google.com/web/progressive-web-apps/" target="_blank">this article</a> about Progressive Web Apps and read <a href="https://developers.google.com/web/fundamentals/primers/service-workers/" target="_blank">this article</a> and <a href="https://developers.google.com/web/fundamentals/primers/service-workers/high-performance-loading" target="_blank">this article</a> about Service Worker Optimization by Google. Test your for website for PWA validation and performance using <a href="https://developers.google.com/web/tools/lighthouse/" target="_blank" rel="noopener">Google Lighthouse</a> (Chrome plugin).</p>

			<p class="about-text" style="min-height:inherit;">If you are happy with the plugin, please consider to <a href="https://wordpress.org/support/plugin/pwa-optimization/reviews/#new-post" target="_blank" rel="noopener">write a review</a> and <span class="star" style="display:inline-block;vertical-align:middle;"><a class="github-button" data-manual="1" data-size="large" href="https://github.com/o10n-x/wordpress-pwa-optimization" data-icon="octicon-star" data-show-count="true" aria-label="Star o10n-x/wordpress-pwa-optimization on GitHub">Star</a></span> on Github.</p>
			

			<hr />

			<h3>WordPress WPO Collection</h3>
			<p class="about-text">This plugin is part of a <a href="<?php print esc_url(add_query_arg(array('s' => 'o10n', 'tab' => 'search', 'type' => 'author'), admin_url('plugin-install.php'))); ?>">collection of optimization plugins</a> that can be used stand alone or as a selection of plugins with single plugin performance. The goal of the optimization plugins is to achieve the absolute best website performance possible.</p>
			<p class="about-text">If you are interested in HTML, CSS or Javascript optimization, check out the <a href="<?php print esc_url(add_query_arg(array('s' => 'o10n', 'tab' => 'search', 'type' => 'author'), admin_url('plugin-install.php'))); ?>">other plugins</a> that are designed to cooperate with this plugin.</p>

		</div>
	</div>

</div>