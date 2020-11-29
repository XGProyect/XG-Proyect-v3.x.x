<?php
/**
 * Debug Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\libraries;

use App\core\enumerators\ErrorTypesEnumerator as ErrorTypes;
use App\libraries\Functions;

/**
 * DebugLib Class
 */
class DebugLib
{
    /**
     * Contains an array of executed queries
     *
     * @var array
     */
    private $queries = [];

    /**
     * Contains an array of errors
     *
     * @var array
     */
    private $logs = [];

    /**
     * dump
     *
     * @param array $var Var
     *
     * @return string
     */
    private function dump($var)
    {
        $result = var_export($var, true);
        $loc = whereCalled();

        return "\n<pre>Dump: $loc\n$result</pre>";
    }

    /**
     * Return error stack trace until provided level
     *
     * @param integer $level
     * @return string
     */
    private function whereCalled(int $level = 5): string
    {
        $error_stack_trace = [];

        for ($error_level = $level; $error_level >= 0; $error_level--) {
            $trace = debug_backtrace();
            $file = isset($trace[$error_level]['file']) ? $trace[$error_level]['file'] : '';
            $line = isset($trace[$error_level]['line']) ? $trace[$error_level]['line'] : '';
            $object = isset($trace[$error_level]['object']) ? $trace[$error_level]['object'] : '';

            if (is_object($object)) {
                $object = get_class($object);
            }

            $break = explode('/', $file);
            $pfile = $break[count($break) - 1];

            $error_stack_trace[] = "Where called: line $line of $object (in $pfile)";
        }

        return join('<br>', $error_stack_trace);
    }

    /**
     * Add a query to the query list
     *
     * @param int $query Query
     *
     * @return void
     */
    public function add(string $query): void
    {
        if (isset($this->queries[$query])) {
            $this->queries[$query]++;
        } else {
            array_push($this->queries, [$query => 1]);
        }
    }

    /**
     * Returns the database log information
     *
     * @return string
     */
    public function echoLog(): void
    {
        if ($this->queries && DEBUG_MODE) {
            print_r($this->queries);
        }
    }

    /**
     * Save errors
     *
     * @param integer $code
     * @param string $description
     * @param string $file
     * @param integer $line
     * @return void
     */
    public function log(int $code, string $description, string $file = '', int $line = 0, string $type = 'db'): void
    {
        $this->logs[] = [
            'code' => $code,
            'description' => $description,
            'file' => $file,
            'line' => $line,
            'type' => $type,
        ];
    }

    /**
     * Take different actions like displaying the error, logging the error and sending an email
     *
     * @param integer $code
     * @param string $description
     * @param string $file
     * @param integer $line
     * @param string $type
     * @return void
     */
    public function error(int $code = 0, string $description = '', string $file = '', int $line = 0, string $type = 'db'): void
    {
        if (count($this->logs) > 0) {
            extract($this->logs[0]);
        }

        $string_code = 'DB Exception';

        if ($type != 'db') {
            $string_code = ErrorTypes::PHP_ERRORS[$code];
        }

        if (DEBUG_MODE or (in_array($_SERVER['HTTP_HOST'], ['127.0.0.1', 'localhost']) !== false)) {
            echo '<div style="background-color:blue;color:white;position:absolute;width:100%;z-index:999999;text-align:center;bottom:0">
                    <h2 style="color:white; font-weight:normal">' . $description . '</h2>';

            if ($type != 'db') {
                echo '<h3>in ' . $file . ' on line ' . $line . '</h3>
                        <span>Error code: ' . $string_code . '</span>';
            }

            echo '<br><br>
                </div>';
        } else {
            $user_ip = $_SERVER['REMOTE_ADDR'];

            // format log
            $log = '|' . $user_ip . '|' . $type . '|' . $string_code . '|' . $description . '|' . $this->whereCalled() . '|' . SYSTEM_VERSION . '|';

            if (defined('LOG_ERRORS') && LOG_ERRORS != '') {
                // log the error
                $this->writeErrors($log, $type);
            }

            // notify administrator
            if (defined('ERROR_LOGS_MAIL') && ERROR_LOGS_MAIL != '') {
                Functions::sendEmail(
                    ERROR_LOGS_MAIL,
                    '[DEBUG][' . $string_code . ']',
                    $log,
                    [
                        'mail' => ERROR_LOGS_MAIL,
                        'name' => 'XG Proyect',
                    ]
                );
            }

            // show page to the user
            if (E_NOTICE != $code) {
                echo '<!DOCTYPE html>
                <html lang=en>
                    <meta charset=utf-8>
                    <meta name=viewport content="initial-scale=1, minimum-scale=1, width=device-width">
                    <title>Error 500 (Internal Server Error)</title>
                    <style>
                    *{margin:0;padding:0}html,code{font:15px/22px arial,sans-serif}
                    html{background:#fff;color:#222;padding:15px}
                    body{margin:7% auto 0;max-width:390px;min-height:180px;padding:30px 0 15px}
                    * > p{margin:11px 0 22px;overflow:hidden}
                    ins{color:#777;text-decoration:none}a img{border:0}
                    @media screen and (max-width:772px)
                    {body{background:none;margin-top:0;max-width:none;padding-right:0}}
                    </style>
                    <a href=//www.xgproyect.org/>
                    <img src="https://xgproyect.org/wp-content/uploads/2019/10/xgp-new-logo-black.png" alt="XG Proyect Logo" width="250px">
                    </a>
                    <p><b>500.</b> <ins>That’s an error.</ins>
                    <p>The requested URL throw an error. Contact the game Administrator.
                    <ins>That’s all we know.</ins>
                ';

                die();
            }
        }
    }

    /**
     * Log the errors into a file
     *
     * @param string $text
     * @param string $type
     * @return void
     */
    private function writeErrors(string $text, string $type): void
    {
        $file_name = $type . '-error-' . date('Ymd') . '-' . time();
        $file_code = sha1($file_name . $text);
        $file = XGP_ROOT . LOGS_PATH . $file_name . '-' . $file_code . '.txt';

        if (!file_exists($file)) {
            @fopen($file, "w+");
            @fclose(fopen($file, "w+"));
        }

        $fp = @fopen($file, "a");
        $date = $text;
        $date .= date('Y/m/d H:i:s', time()) . "|\n";

        @fwrite($fp, $date);
        @fclose($fp);
    }
}
