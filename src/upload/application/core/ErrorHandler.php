<?php
/**
 * Error Handler
 *
 * PHP Version 7.1+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\core;

use \application\libraries\DebugLib as Debug;

/**
 * Error Handler Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
final class ErrorHandler
{
    /**
     * Contains a DebugLib object
     *
     * @var \DebugLib
     */
    private $debug;

    /**
     * Constructor
     */
    public function __construct()
    {
        // report all errors
        error_reporting(E_ALL);
        ini_set('display_errors', 0);

        $this->createNewDebugObject();

        set_error_handler([$this, 'errorHandler']);
        register_shutdown_function([$this, 'fatalErrorShutdownFunction']);
    }

    /**
     * Create a new DebugLib object
     *
     * @return void
     */
    private function createNewDebugObject(): void
    {
        $this->debug = new Debug;
    }

    /**
     * Set the error handler
     *
     * @param integer $code
     * @param string $description
     * @param string $file
     * @param string $line
     * @return boolean
     */
    final public function errorHandler(int $code, string $description, string $file, string $line): bool
    {
        $displayErrors = strtolower(ini_get("display_errors"));

        if (error_reporting() === 0 || $displayErrors === "on") {
            return false;
        }

        $this->debug->error($code, $description, $file, $line, 'php');

        return true;
    }

    /**
     * Set a shutdown function on fatal error
     *
     * @return void
     */
    final public function fatalErrorShutdownFunction(): void
    {
        $last_error = error_get_last();
        if ($last_error['type'] === E_ERROR) {
            $this->errorHandler(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }
}

/* end of ErrorHandler.php */
