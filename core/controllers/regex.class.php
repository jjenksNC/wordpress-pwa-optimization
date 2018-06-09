<?php
namespace O10n;

/**
 * Regex Controller
 *
 * @package    optimization
 * @subpackage optimization/controllers
 * @author     Optimization.Team <info@optimization.team>
 */
if (!defined('ABSPATH')) {
    exit;
}

class Regex extends Controller implements Controller_Interface
{
    /**
     * Load controller
     *
     * @param  Core       $Core Core controller instance.
     * @return Controller Controller instance.
     */
    public static function &load(Core $Core)
    {
        // instantiate controller
        return parent::construct($Core, array(
            'url'
        ));
    }

    /**
     * Setup controller
     */
    protected function setup()
    {
    }

    /**
     * Extract attribute from tag
     *
     * @param  string $param      Parameter to find
     * @param  string $attributes HTML tag attributes
     * @param  string $replace    src value to replace
     * @return string modified attributes
     */
    final public function attr($param, $attributes, $replace = false)
    {
        // detect if tag has src
        $paramlen = strlen($param);
        $srcpos = strpos($attributes, $param);
        if ($srcpos !== false) {
            $param_quote = preg_quote($param);
            $last_is_quote = true;

            $attributes_str = $attributes;

            // regex
            $char = substr($attributes, ($srcpos + ($paramlen + 1)), 1);
            if ($char === '"' || $char === '\'') {
                $char = preg_quote($char);
                $regex = '#('.$param_quote.'\s*=\s*'.$char.')([^'.$char.']+)('.$char.')#Usmi';
            } elseif ($char === ' ' || $char === "\n") {
                $regex = '#('.$param_quote.'\s*=\s*["|\'])([^"|\']+)(["|\'])#Usmi';
            } else {
                $regex = '#('.$param_quote.'\s*=)([^\s>]+)(\s|>|$)#Usmi';
            }

            // return param
            if (!$replace) {

                // match param
                if (!preg_match($regex, $attributes_str, $out)) {
                    return false;
                }

                return $out[2];
            }

            if ($replace === -1) {
                // replace param in tag
                $attributes = preg_replace($regex, '', $attributes_str);
            } else {
                // replace param in tag
                $attributes = preg_replace($regex, '$1' . $replace . '$3', $attributes_str);
            }
        }

        return ($replace) ? $attributes : false;
    }
}
