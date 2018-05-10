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

$module_info = $view->module_tab_info();

?>

		<?php
            $module_installer = true;
            if (isset($module_info['installer_key'])) {
                switch ($module_info['installer_key']) {
                    case "pagecache":
                        $module_installer = false;

                        $cache_plugins = array(
                            array(
                                'title' => 'File Cache with PHP Opcache boost option',
                                'desc' => 'Advanced file based page cache with PHP Opcache boost option (<a href="https://blog.graphiq.com/500x-faster-caching-than-redis-memcache-apc-in-php-hhvm-dcd26e8447ad" target="_blank">500x faster than Redis and Memcached</a>).',
                                'github' => 'https://github.com/o10n-x/wordpress-file-page-cache'
                            ),
                            array(
                                'title' => 'CloudFront Page Cache CDN',
                                'desc' => 'Page Cache based on <a href="https://aws.amazon.com/cloudfront/" target="_blank">Amazon CloudFront CDN</a>. Low cost and high performance international page cache.',
                                'github' => 'https://github.com/o10n-x/wordpress-cloudfront-page-cache'
                            ),
                            array(
                                'title' => 'Google Cloud Page Cache CDN',
                                'desc' => 'Page Cache based on <a href="https://cloud.google.com/cdn/" target="_blank">Google Cloud CDN</a>. Low cost and high performance international page cache.',
                                'github' => 'https://github.com/o10n-x/wordpress-google-cdn-page-cache'
                            )
                        );
?>
			<h1><strong>Page Cache</strong> options</h1>

			<div style="float:right; margin-right:10px;">
				<a href="https://github.com/afragen/github-updater" target="_blank" rel="noopener"><img src="<?php print O10N_CORE_URI; ?>admin/images/github-updater.png" alt="Github Updater" width="180" border="0" style="float:right;"></a>
			</div>

			<p class="about-text" style="min-height:inherit;">The following Page Cache plugins are available as part of the WPO plugin collection.</p>

			<?php
                foreach ($cache_plugins as $plugin) {
                    ?>
                            <form method="post" action="<?php print add_query_arg(array( 'page' => 'github-updater','tab' => 'github_updater_install_plugin' ), admin_url('options-general.php')); ?>">
<div class="wrap">

	<div class="metabox-prefs">
		<div class="wrap about-wrap" style="position:relative;">
<h3 style="margin-bottom:0px;"><?php print $plugin['title']; ?></h3>
<p class="about-text" style="min-height:inherit;margin-top:0px;margin-bottom:10px;"><?php print $plugin['desc']; ?></p>
<input type="hidden" name="option_page" value="github_updater_install">
<input type="hidden" name="action" value="update">
<input type="hidden" name="github_updater_branch" value="">
<input type="hidden" name="github_updater_api" value="github">
<input type="hidden" name="github_access_token" value="">
<input type="hidden" name="github_updater_repo" value="<?php print esc_attr($plugin['github']); ?>">
 <?php wp_nonce_field(); ?>
<p style="margin-top:5px;"><button type="submit" class="button button-large button-primary">Install</button> <a href="<?php print $plugin['github']; ?>" target="_blank" style="margin-left:5px;">View plugin details</a></p>
		</div>
	</div>

</div>
</form>
<?php
                }
            ?>
<?php
                    break;
                }
            }

            if ($module_installer) {
                ?>
        <form method="post" action="<?php print add_query_arg(array( 'page' => 'github-updater','tab' => 'github_updater_install_plugin' ), admin_url('options-general.php')); ?>">
<div class="wrap">

	<div class="metabox-prefs">
		<div class="wrap about-wrap" style="position:relative;">

			<h1><strong><?php print $module_info['name']; ?></strong> is not installed</h1>

			<div style="float:right; margin-right:10px;">
				<a href="https://github.com/afragen/github-updater" target="_blank" rel="noopener"><img src="<?php print O10N_CORE_URI; ?>admin/images/github-updater.png" alt="Github Updater" width="180" border="0" style="float:right;"></a>
			</div>
<p class="about-text" style="min-height:inherit;">The <?php print $module_info['name']; ?> plugin is not installed. You can install the plugin using <a href="https://github.com/afragen/github-updater" target="_blank">GitHub Updater</a>.</p>

<input type="hidden" name="option_page" value="github_updater_install">
<input type="hidden" name="action" value="update">
<input type="hidden" name="github_updater_branch" value="">
<input type="hidden" name="github_updater_api" value="github">
<input type="hidden" name="github_access_token" value="">
<input type="hidden" name="github_updater_repo" value="<?php print esc_attr($module_info['github']); ?>">
 <?php wp_nonce_field(); ?>
<p><button type="submit" class="button button-large button-primary" style="font-size:22px;line-height:40px;height:40px;">Install</button></p>
<p><a href="<?php print $module_info['github']; ?>" target="_blank">View plugin details</a></p>
		</div>
	</div>

</div>
</form>
<?php
            }
?>

<?php

// print form header
