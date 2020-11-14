<?php
/**
 * XGPCore
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\core;

use Exception;
use SimpleXMLElement;

/**
 * Xml Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team (Jstar)
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0 (v2)
 * @tutorial
 *   $c=xml::getInstance('config.xml');
 *   echo $c->getConfig('version');
 *   $c->writeConfig('version','blabla');
 *   echo $c->getConfig('version');
 */
class Xml
{
    /**
     *
     * @var Xml
     */
    private static $instance = null;

    /**
     *
     * @var string
     */
    private $path;

    /**
     *
     * @var SimpleXMLElement
     */
    private $config;

    /**
     * __construct
     *
     * @param string $sheet Sheet
     *
     * @return void
     */
    private function __construct($sheet)
    {
        $this->path = XGP_ROOT . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $sheet;
        $this->config = simplexml_load_file($this->path);
    }

    /**
     * This function execute a Xpath query
     *
     * @param string $query Query
     *
     * @return array
     */
    public function doXpathQuery($query)
    {
        return $this->config->xpath($query);
    }

    /**
     * Search in the xml for a entity rappresented by $config_name
     *
     * @param string $config_name Configuration name
     *
     * @return SimpleXMLElement
     */
    private function getXmlEntity($config_name)
    {
        // searching inside <configurations> and where config name=$config_name
        $result = $this->doXpathQuery('/configurations/config[name="' . $config_name . '"]');

        // if multiple result are returned so key is not unique
        if (!$result || count($result) !== 1) {
            throw new Exception(sprintf('Item with id "%s" does not exists or is not unique.', $config_name));
        }

        list($result) = $result;

        return $result;
    }

    /**
     * This function search in loaded xml for a value according to specific configuration name passed
     *
     * @param string $config_name Configuration name
     *
     * @return string
     */
    public function getConfig($config_name)
    {
        // (string) is a cast to String type from SimpleXMLElement object: we need this to extract value
        return (string) $this->getXmlEntity($config_name)->value;
    }

    /**
     * This function return all configurations loaded from xml file
     *
     * @return string
     */
    public function getConfigs()
    {
        $config = [];
        $x = $this->config->children();

        foreach ($x as $xmlObject) {
            $config[(string) $xmlObject->name] = (string) $xmlObject->value;
        }

        return $config;
    }

    /**
     * This function write the xml configuration file updating one or multiple key-value at time
     *
     * @param mixed  $config_name  String for single update or an associative array of key=>value
     * @param string $config_value The value that will be setted in corrispective key $config_name
     *
     * @return void
     */
    public function writeConfig($config_name, $config_value)
    {
        //if $config_name is an array, then we wont update all values and do single save task at the end
        if (is_array($config_name)) {
            foreach ($config_name as $key => $value) {
                $this->getXmlEntity($key)->value = $value;
            }
        } else {
            $this->getXmlEntity($config_name)->value = $config_value;
        }

        $this->config->asXML($this->path);
    }

    /**
     * Static function used to istance this class: implements singleton pattern to avoid multiple xml parsing.
     *
     * @param string $sheet The complete name of xml configuration file.
     *
     * @return Xml
     */
    public static function getInstance($sheet)
    {
        if (self::$instance == null) {
            //make new istance of this class and save it to field for next usage
            $c = __class__;
            self::$instance = new $c($sheet);
        }

        return self::$instance;
    }
}
