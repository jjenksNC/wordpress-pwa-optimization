<?php
namespace O10n;

/**
 * Global functions
 *
 * @package    optimization
 * @subpackage optimization/controllers
 * @author     Optimization.Team <info@optimization.team>
 */

// attach assets for bundled preload in Service Worker
function attach_preload($urls)
{
    Core::get('pwa')->attach_preload($urls);
}
