<?php

/**
 *  OPBE
 *  Copyright (C) 2013  Jstar
 *
 * This file is part of OPBE.
 * 
 * OPBE is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPBE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with OPBE.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OPBE
 * @author Jstar <frascafresca@gmail.com>
 * @copyright 2013 Jstar <frascafresca@gmail.com>
 * @license http://www.gnu.org/licenses/ GNU AGPLv3 License
 * @version beta(26-10-2013)
 * @link https://github.com/jstar88/opbe
 */

require (dirname(__DIR__) . DIRECTORY_SEPARATOR ."utils".DIRECTORY_SEPARATOR."includer.php");
require (OPBEPATH . "tests".DIRECTORY_SEPARATOR."runnable".DIRECTORY_SEPARATOR."langs".DIRECTORY_SEPARATOR."MoonsLangImplementation.php");
require (OPBEPATH . "tests".DIRECTORY_SEPARATOR."runnable".DIRECTORY_SEPARATOR."langs".DIRECTORY_SEPARATOR."XGLangImplementation.php");

class RunnableTest
{
    private $time;
    private $memory;
    private $report;

    public static $reslist, $pricelist, $requeriments, $resource, $CombatCaps;
    public function __construct($debug = false)
    {
        if(empty(self::$reslist))
        {
            self::includeVars('XG');
        }
        if(!LangManager::getInstance()->implementationExist())
        {
            LangManager::getInstance()->setImplementation(new XGLangImplementation());
        }
        $attackers = $this->getAttachers();
        $defenders = $this->getDefenders();
        $memory1 = memory_get_usage();
        $micro1 = microtime();

        $engine = new Battle($attackers, $defenders);
        $startBattle = DebugManager::runDebugged(array($engine,'startBattle'),array('RunnableTest', 'myErrorHandler'), array('RunnableTest', 'save'));
        $startBattle($debug);

        $micro1 = microtime() - $micro1;
        $memory1 = memory_get_usage() - $memory1;

        $this->report = $engine->getReport();

        $this->time = round(1000 * $micro1, 2);
        $this->memory = round($memory1 / 1000);
        echo $this;


    }
    public function getShipType($id, $count)
    {
        $rf = self::$CombatCaps[$id]['sd'];
        $shield = self::$CombatCaps[$id]['shield'];
        $cost = array(self::$pricelist[$id]['metal'], self::$pricelist[$id]['crystal']);
        $power = self::$CombatCaps[$id]['attack'];
        if (in_array($id, self::$reslist['fleet']))
        {
            return new Ship($id, $count, $rf, $shield, $cost, $power);
        }
        return new Defense($id, $count, $rf, $shield, $cost, $power);
    }
    public function getAttachers()
    {

    }
    public function getDefenders()
    {

    }
    public static function myErrorHandler($errno, $errstr, $errfile, $errline)
    {
        $error = '';
        switch ($errno)
        {
            case E_USER_ERROR:
                $error .= "ERROR [$errno] $errstr<br />";
                break;

            case E_USER_WARNING:
                $error .= "WARNING [$errno] $errstr<br />";
                break;

            case E_USER_NOTICE:
                $error .= "NOTICE [$errno] $errstr<br />";
                break;

            default:
                $error .= "Unknown error type: [$errno] $errstr<br />";
                break;
        }
        $error .= "Error on line $errline in file $errfile";
        $error .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />";
        self::save($error);
        /* Don't execute PHP internal error handler */
        return true;

    }
    public static function save($other)
    {
        date_default_timezone_set(TIMEZONE);
        $time = date('l jS \of F Y h:i:s A');
        $post = '$_POST =' . var_export($_POST);
        $get = '$_GET =' . var_export($_GET);
        $output = ob_get_clean();
        $path = OPBEPATH.'tests'.DIRECTORY_SEPARATOR.'runnable'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.'internals';
        if (!file_exists($path))
        {
            mkdir($path, 0777, true);
        }
        file_put_contents($path.DIRECTORY_SEPARATOR . date('d-m-y__H-i-s') . '.html', $time . PHP_EOL . self::br2nl($other) . PHP_EOL . $post . PHP_EOL . $get . PHP_EOL . self::br2nl($output));
        die('An error occurred, we will resolve it soon as possible');
    }
    private static function br2nl($text)
    {
        $x = preg_replace('/<br\\\\s*?\\/??>/i', PHP_EOL, $text);
        return str_ireplace('<br />', '', $x);
    }
    public function __toString()
    {
        $micro = $this->time;
        $memory = $this->memory;
        if(get_class($this) != 'WebTest')
        {
            $this->report->css = '../../../';
        }
        return $this->report . <<< EOT
<br>______________________________________________<br>
Battle calculated in <font color=blue>$micro ms</font>.<br>
Memory used: <font color=blue>$memory KB</font><br>
_______________________________________________<br>
EOT;

    }


    public static function includeVars($name)
    {
        require (OPBEPATH."tests".DIRECTORY_SEPARATOR."runnable".DIRECTORY_SEPARATOR."vars".DIRECTORY_SEPARATOR."$name.php");
        RunnableTest::$reslist = $reslist;
        RunnableTest::$pricelist = $pricelist;
        RunnableTest::$requeriments = $requeriments;
        RunnableTest::$resource = $resource;
        RunnableTest::$CombatCaps = $CombatCaps;
    }
    public static function getVarsList()
    {
        $list = array();
        if ($handle = opendir(OPBEPATH."tests".DIRECTORY_SEPARATOR."runnable".DIRECTORY_SEPARATOR."vars"))
        {
            while (false !== ($entry = readdir($handle)))
                if ($entry != "." && $entry != "..")
                    $list[] = basename($entry, ".php");
            closedir($handle);
        }
        return $list;
    }
}

?>
