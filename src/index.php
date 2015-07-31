<?php
/**
 * Index file. When compiling, this function does not compile.
 * It functions as an index and autoloader for the src directory so the code
 * can be tested by opening this index file in the browser.
 *
 * @author Jordi Jolink
 * @date 31-7-2015
 */


/**
 * Class Template
 * Returns the filename of a template file.
 */
class Template
{
    /**
     * @param string $template
     * @return string
     */
    public function getTemplateFilename($template)
    {
        return __DIR__ . '/template/' . $template . '.php';
    }
}

/**
 * Register the autoloader
 */
spl_autoload_register(
    function ($class)
    {
        require_once __DIR__ . '/code/' . $class . '.php';
    }
);

/**
 * Include the Script object file.
 */
require 'Script.php';
