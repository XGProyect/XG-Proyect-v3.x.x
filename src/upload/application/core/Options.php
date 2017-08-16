<?php
/**
 * Options
 *
 * PHP Version 5.5+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace application\core;

/**
 * Options Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Options extends XGPCore
{
    /**
     *
     * @var Xml
     */
    private static $instance = null;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Static function used to istance this class: implements singleton pattern to avoid multiple parsing.
     *
     * @return Options
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            
            //make new istance of this class and save it to field for next usage
            $class  = __class__;
            self::$instance = new $class();
        }

        return self::$instance;
    }
    
    /**
     * Get the game options, leaving the param $option empty will return all of them
     *
     * @param string $option Option
     *
     * @return mixed
     */
    public function getOptions($option = '')
    {
        if ($option == '') {

            return parent::$db->query(
                "SELECT * FROM `" . OPTIONS . "`;"
            );
        } else {

            return parent::$db->queryFetch(
                "SELECT * 
                    FROM `" . OPTIONS . "` 
                    WHERE `option_name` = '" . $option . "';"
            )['option_value'];
        }
    }
    
    /**
     * Update the option in the database
     *
     * @param string $option Option
     * @param string $value  Value
     *
     * @return boolean
     */
    public function writeOptions($option, $value = '')
    {
        if ($option != '') {
            
            if (parent::$db->query(
                "UPDATE `" . OPTIONS . "` 
                    SET `option_value` = '" . $value . "' 
                    WHERE `option_name` = '" . $option . "';"
            )) {
                    return true;
            }
        }
        
        return false;
    }
    
    /**
     * Insert a new option into database
     * 
     * @param string $option Option
     * @param string $value  Value
     * 
     * @return boolean
     */
    public function insertOption($option, $value = '')
    {
        if ($option != '') {
            
            if (parent::$db->query(
                "INSERT INTO `" . OPTIONS . "` 
                    (`option_name`, `option_value`) VALUES('" . $option . "', '" . $value . "');"
            )) {
                    return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Delete an option permanently
     * 
     * @param string $option Option
     * 
     * @return boolean
     */
    public function deleteOption($option)
    {
        if ($option != '') {
            
            if (parent::$db->query(
                "DELETE `" . OPTIONS . "` 
                    WHERE `option_name` = '" . $option . "';"
            )) {
                    return true;
            }
        }
        
        return false;
    }
}

/* end of Options.php */
