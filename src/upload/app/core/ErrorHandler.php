<?php declare (strict_types = 1);

/**
 * Error Handler
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @since    3.1.0
 */
namespace App\core;

use \App\libraries\DebugLib as Debug;

/**
 * Error Handler Class
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
        ini_set('display_errors', '0');

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
    final public function errorHandler(int $code, string $description, string $file, int $line): bool
    {
        $displayErrors = strtolower(ini_get("display_errors"));

        if (error_reporting() === 0 || $displayErrors === "on") {
            return false;
        }

        if ($code === E_ERROR) {
            $this->debug->error($code, $description, $file, $line, 'php');
        } else {
            $this->debug->log($code, $description, $file, $line, 'php');
            $this->debug->error();
        }

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
