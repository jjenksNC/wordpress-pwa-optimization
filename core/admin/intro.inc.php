<?php
namespace O10n;

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

if (defined('O10N_WPO_VERSION')) {
    // redirect
}

$module_name = 'Performance Optimization';

?>
<div class="wrap">

	<div class="metabox-prefs">
		<div class="wrap about-wrap" style="position:relative;">
			<div style="float:right;">
			</div>
			<h1>Website Performance Optimization</h1>

			<p class="about-text" style="min-height:inherit;">Thank you for using performance optimization plugins by <a href="https://optimization.team/" target="_blank" rel="noopener" style="color:black;text-decoration:none;">Optimization.Team</a>.</p>

			<p class="about-text" style="min-height:inherit;">You are using a selection of standalone optimization modules. To achieve optimal performance, the plugins automatically function as a single plugin when used in combination. The goal of the optimization plugins is to achieve the absolute best website performance possible.</p>

			<p class="about-text info_yellow" style="min-height:inherit;"><strong>Warning:</strong> This plugin is intended for optimization professionals and advanced WordPress users.</p>

			<p class="about-text" style="min-height:inherit;">Search WordPress.org for <a href="<?php print esc_url(add_query_arg(array('s' => 'o10n', 'tab' => 'search', 'type' => 'author'), admin_url('plugin-install.php'))); ?>">author o10n</a> or visit our <A href="https://github.com/o10n-x/" target="_blank" rel="noopener">Github</a> for more optimization plugins.</p>

			<p class="about-text" style="min-height:inherit;">We are very interested to receive feedback and feature requests. The preferred way to send us feedback is using the <a href="https://github.com/o10n-x/" target="_blank" rel="noopener">Github community forums</a>.</p>
			</div>

		</div>
	</div>

</div>