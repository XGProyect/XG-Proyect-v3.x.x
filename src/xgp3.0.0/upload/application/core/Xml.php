<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

/**
 *
 * @author Jstar
 * @version v2
 * @tutorial
 *   $c=xml::getInstance('config.xml');
 *   echo $c->get_config('version');
 *   $c->write_config('version','blabla');
 *   echo $c->get_config('version');
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Xml
{
    //an istance of this class: see singleton pattern
    private static $instance = null;
    //the complete path to xml config: used to load and save it
    private $path;
    //SimpleXMLElement object that rappresent xml config
    private $config;

    /**
     * xml::__construct()
     * Constructor: access is private to enable class istancing only by getInstance() method, to ensure better performace
     *
     * @param String $sheet
     * @return null
     */
    private function __construct($sheet)
    {
        $this->path = XGP_ROOT . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $sheet;
        $this->config = simplexml_load_file($this->path);
    }

    /**
     * xml::doXpathQuery()
     * This function execute a Xpath query
     *
     * @param String $query
     * @return Array
     */
    public function doXpathQuery($query)
    {
        return $this->config->xpath($query);
    }

    /**
     * xml::get_xml_entity()
     * Search in the xml for a entity rappresented by $config_name
     *
     * @param String $config_name: the key
     * @return SimpleXMLElement object
     */
    private function get_xml_entity($config_name)
    {
        //searching inside <configurations> and where config name=$config_name
        $result = $this->doXpathQuery('/configurations/config[name="' . $config_name . '"]');
        //if multiple result are returned so key is not unique
        if (!$result || count($result) !== 1)
        {
            throw new Exception(sprintf('Item with id "%s" does not exists or is not unique.', $config_name));
        }
        list($result) = $result;
        return $result;
    }

    /**
     * xml::get_config()
     * This function search in loaded xml for a value according to specific configuration name passed
     *
     * @param String $config_name
     * @return String: the configuration value of given key
     */
    public function get_config($config_name)
    {
        // (string) is a cast to String type from SimpleXMLElement object: we need this to extract value
        return (string) $this->get_xml_entity($config_name)->value;
    }

    /**
     * xml::get_configs()
     * This function return all configurations loaded from xml file
     *
     * @return Array: an associative array of key-value
     */
    public function get_configs()
    {
        $config = array();
        $x = $this->config->children();
        foreach ($x as $xmlObject)
        {
            $config[(string) $xmlObject->name] = (string) $xmlObject->value;
        }
        return $config;
    }

    /**
     * xml::write_config()
     * This function write the xml configuration file updating one or multiple key-value at time
     *
     * @param mixed $config_name : String for single update or an associative array of key=>value
     * @param String $config_value : The value that will be setted in corrispective key $config_name
     * @return null
     */
    public function write_config($config_name, $config_value)
    {
        //if $config_name is an array, then we wont update all values and do single save task at the end
        if (is_array($config_name))
        {
            foreach ($config_name as $key => $value)
            {
                $this->get_xml_entity($key)->value = $value;
            }
        }
        else
        {
            $this->get_xml_entity($config_name)->value = $config_value;
        }
        $this->config->asXML($this->path);
    }

    /**
     * xml::getInstance()
     * Static function used to istance this class: implements singleton pattern to avoid multiple xml parsing.
     *
     * @param String $sheet : the complete name of xml configuration file.
     * @return xml object
     */
    public static function getInstance($sheet)
    {
        if (self::$instance == null)
        {
            //make new istance of this class and save it to field for next usage
            $c = __class__;
            self::$instance = new $c($sheet);
        }

        return self::$instance;
    }
}

/* end of Xml.php */