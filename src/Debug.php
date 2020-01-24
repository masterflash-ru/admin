<?php
/**
 */

namespace Admin;

use Laminas\Escaper\Escaper;

/**
 * Concrete class for generating debug dumps related to the output source.
 */
class Debug
{
    /**
     * @var Escaper
     */
    protected static $escaper;

    /**
     * @var string
     */
    protected static $sapi;

    /**
     * Get the current value of the debug output environment.
     * This defaults to the value of PHP_SAPI.
     *
     * @return string;
     */
    public static function getSapi()
    {
        if (static::$sapi === null) {
            static::$sapi = PHP_SAPI;
        }
        return static::$sapi;
    }

    /**
     * Set the debug output environment.
     * Setting a value of null causes Zend\Debug\Debug to use PHP_SAPI.
     *
     * @param string $sapi
     * @return void;
     */
    public static function setSapi($sapi)
    {
        static::$sapi = $sapi;
    }

    /**
     * Set Escaper instance
     *
     * @param  Escaper $escaper
     */
    public static function setEscaper(Escaper $escaper)
    {
        static::$escaper = $escaper;
    }

    /**
     * Get Escaper instance
     *
     * Lazy loads an instance if none provided.
     *
     * @return Escaper
     */
    public static function getEscaper()
    {
        if (null === static::$escaper) {
            static::setEscaper(new Escaper());
        }
        return static::$escaper;
    }

    /**
     * Debug helper function.  This is a wrapper for var_dump() that adds
     * the <pre /> tags, cleans up newlines and indents, and runs
     * htmlentities() before output.
     *
     * @param  mixed  $var   The variable to dump.
     * @param  string $label OPTIONAL Label to prepend to output.
     * @param  bool   $echo  OPTIONAL Echo output if true.
     * @return string
     */
    public static function dump($var, $label = null, $echo = true)
    {
        // format the label
        $label = ($label === null) ? '' : rtrim($label) . ' ';

        // var_dump the variable into a buffer and keep the output
        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        // neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        if (static::getSapi() == 'cli') {
            $output = PHP_EOL . $label
                    . PHP_EOL . $output
                    . PHP_EOL;
        } else {
            if (null !== static::$escaper) {
                $output = static::$escaper->escapeHtml($output);
            } elseif (! extension_loaded('xdebug')) {
                $output = static::getEscaper()->escapeHtml($output);
            }

            $output = '<pre>'
                    . $label
                    . $output
                    . '</pre>';
        }

        if ($echo) {
            echo $output;
        }
        return $output;
    }
}
