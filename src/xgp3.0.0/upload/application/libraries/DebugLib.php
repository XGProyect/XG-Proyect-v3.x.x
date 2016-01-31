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

use application\core\XGPCore;

/**
 * DebugLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class DebugLib extends XGPCore
{
    private $log;
    private $numqueries;
    private $langs;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->vars         = $this->log    = '';
        $this->numqueries   = 0;
        $this->langs        = parent::$lang;
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
        $loc    = whereCalled();

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
        $trace  = debug_backtrace();
        $file   = $trace[$level]['file'];
        $line   = $trace[$level]['line'];
        $object = $trace[$level]['object'];

        if (is_object($object)) {

            $object = get_class($object);
        }

        $break  = Explode('/', $file);
        $pfile  = $break[count($break) - 1];

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
        return  '<br>
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
    public function error($message, $title)
    {
        if (FunctionsLib::read_config('debug') == 1) {

            echo '<h2>'.$title.'</h2><br><font color="red">' . $message . '</font><br><hr>';
            echo $this->echoLog();
            echo $this->whereCalled(3);
        } else {

            if (isset(parent::$users->get_user_data)) {

                $user_id    = parent::$users->get_user_data()['user_id'];
            } else {
                $user_id    = 0;
            }

            // format log
            $log    = '|' . $user_id . '|'. $title .'|' . $message . '|' . $this->whereCalled(3) . '|';

            // log the error
            $this->writeErrors($log, "ErrorLog");

            $headers    =  'MIME-Version: 1.0' . "\r\n";
            $headers    .= 'From: XG Proyect ' . FunctionsLib::read_config('admin_email') . "\r\n";
            $headers    .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            
            // notify administrator
            @mail(
                FunctionsLib::read_config('admin_email'),
                '[DEBUG][' . $title . ']',
                $this->whereCalled(3),
                $headers
            );

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
    private function writeErrors($text, $log_file)
    {
        $file   = XGP_ROOT . LOGS_PATH . $log_file . ".php";

        if (!file_exists($file) && is_writable($file)) {

            @fopen($file, "w+");
            @fclose(fopen($file, "w+"));
        }

        $fp     =   @fopen($file, "a");
        $date   =   $text;
        $date   .=  date(FunctionsLib::read_config('date_format_extended'), time()) . "||\n";

        @fwrite($fp, $date);
        @fclose($fp);
    }
}

/* end of DebugLib.php */
