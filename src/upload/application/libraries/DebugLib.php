<?php
/**
 * Debug Library
 *
 * PHP Version 5.5+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\libraries;

/**
 * DebugLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class DebugLib
{

    private $log;
    private $numqueries;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->vars = $this->log = '';
        $this->numqueries = 0;
    }

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
     * whereCalled
     *
     * @param int $level Level
     *
     * @return string
     */
    private function whereCalled($level = 1)
    {
        
        $trace = debug_backtrace();
        $file = isset($trace[$level]['file']) ? $trace[$level]['file'] : '';
        $line = isset($trace[$level]['line']) ? $trace[$level]['line'] : '';
        $object = isset($trace[$level]['object']) ? $trace[$level]['object'] : '';

        if (is_object($object)) {

            $object = get_class($object);
        }

        $break = Explode('/', $file);
        $pfile = $break[count($break) - 1];

        return "Where called: line $line of $object <br/>(in $pfile)";
    }

    /**
     * add
     *
     * @param int $query Query
     *
     * @return void
     */
    public function add($query)
    {
        $this->numqueries++;
        $this->log .= '<tr><th rowspan="2">Query ' .
            $this->numqueries . ':</th><th>' . $query . '</th></tr><tr><th>' .
            $this->whereCalled(3) . '</th></tr>';
    }

    /**
     * echoLog
     *
     * @return string
     */
    public function echoLog()
    {
        return '<br>
                    <table>
                        <tr>
                            <td class="k" colspan="2">
                                <a href="' . XGP_ROOT . 'admin.php?page=settings">Debug Log</a>:
                            </td>
                        </tr>'
            . $this->log .
            '</table>';
    }

    /**
     * error
     *
     * @param string $message Message
     * @param string $title   Title
     *
     * @return void
     */
    public function error($message, $title, $type = 'db')
    {
        if (DEBUG_MODE == true) {

            echo '<h2>' . $title . '</h2><br><font color="red">' . $message . '</font><br><hr>';
            echo $this->echoLog();
            echo $this->whereCalled(3);
        } else {

            $user_ip = $_SERVER['REMOTE_ADDR'];

            // format log
            $log = '|' . $user_ip . '|' . $title . '|' . $message . '|' . $this->whereCalled(3) . '|';

            // log the error
            if ($type == 'db') {
                
                $this->writeDBErrors($log, "ErrorLog");
            }

            // log php error
            if ($type = 'php') {
                
                $this->writePHPErrors($log);   
            }
            
            // notify administrator
            if (defined('ERROR_LOGS_MAIL') && ERROR_LOGS_MAIL != '') {

                FunctionsLib::sendEmail(
                    ERROR_LOGS_MAIL, '[DEBUG][' . $title . ']', $this->whereCalled(3), [
                    'mail' => ERROR_LOGS_MAIL,
                    'name' => 'XG Proyect'
                    ]
                );
            }

            // show page to the user
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
                              <img src="http://www.xgproyect.org/images/misc/xg-logo.png" alt="XG Proyect">
                              </a>
                              <p><b>500.</b> <ins>That’s an error.</ins>
                              <p>The requested URL throw an error. Contact the game Administrator. 
                              <ins>That’s all we know.</ins>
                            ';
        }

        die();
    }

    /**
     * writeErrors
     *
     * @param string $text     Text
     * @param string $log_file Log title
     *
     * @return void
     */
    private function writeDBErrors($text, $log_file)
    {
        $file = XGP_ROOT . LOGS_PATH . $log_file . ".php";

        if (!file_exists($file) && is_writable($file)) {

            @fopen($file, "w+");
            @fclose(fopen($file, "w+"));
        }

        $fp = @fopen($file, "a");
        $date = $text;
        $date .= date('Y/m/d H:i:s', time()) . "||\n";

        @fwrite($fp, $date);
        @fclose($fp);
    }
    
    /**
     * Log php errors
     * 
     * @param type $message Error message
     * @param type $title   Error code
     * 
     * @return void
     */
    private function writePHPErrors($text)
    {
        $file_name = XGP_ROOT . LOGS_PATH . 'system-error-' . date('Ymd') . '-' . time() . '-' . (sha1($text)) . '.txt';
        
        if (!file_exists($file_name) && is_writable($file_name)) {

            @fopen($file_name, "w+");
            @fclose(fopen($file_name, "w+"));
        }

        $fp = @fopen($file_name, "a");
        $date = $text;
        $date .= date('Y/m/d H:i:s', time()) . "||\n";

        @fwrite($fp, $date);
        @fclose($fp);
    }
}

/* end of DebugLib.php */
