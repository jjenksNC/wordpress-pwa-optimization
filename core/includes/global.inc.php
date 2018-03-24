<?php
namespace O10n;

/**
 * Global functions
 *
 * @package    optimization
 * @subpackage optimization/controllers
 * @author     Optimization.Team <info@optimization.team>
 */

// Add search replace filter
function search_replace($search, $replace, $regex = false)
{
    Core::get('output')->add_search_replace($search, $replace, $regex);
}


// load cron related methods
require O10N_CORE_PATH . 'includes/cron.inc.php';
