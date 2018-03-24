<?php
namespace O10n;

/**
 * Web Font optimization admin template
 *
 * @package    optimization
 * @subpackage optimization/admin
 * @author     Optimization.Team <info@optimization.team>
 */
if (!defined('ABSPATH') || !defined('O10N_ADMIN')) {
    exit;
}

?>

<div class="wrap">

	<div class="metabox-prefs">
		<div class="wrap about-wrap" style="position:relative;">
			<h1><strong><?php print $view->plugin_name;?></strong> is not installed</h1>

<p class="about-text">The Performance Optimization plugin is a modular toolbox that enables to achieve the highest efficiency by installing only the optimization components that are needed.</p>

<p class="about-text" style="min-height:inherit;"><?php print $view->dummy_description; ?> requires the <strong><?php print $view->plugin_name;?></strong> plugin v<?php print $view->minimum_version;?> or higher.</p>

<p><a href="<?php print esc_url($view->dummy_install_url); ?>" class="button button-large button-primary" style="font-size:22px;line-height:40px;height:40px;">Install <?php print $view->plugin_name;?></a></p>
<p><a href="<?php print $view->dummy_info_url; ?>">View plugin details</a></p>

		</div>
	</div>

</div>

<?php

// print form header
