<?php
/**
 * Planet Library
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
 * PlanetLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class PlanetLib extends XGPCore
{
    
    /**
     * createPlanetWithOptions
     *
     * @param array $data Data
     *
     * @return void
     */
    public static function createPlanetWithOptions($data)
    {
        if (is_array($data)) {
            
            $insert_query   = 'INSERT INTO ' . PLANETS . ' SET ';
            
            foreach ($data as $column => $value) {
                $insert_query .= "`" . $column . "` = '" . $value . "', ";
            }
                
            // Remove last comma
            $insert_query   = substr_replace($insert_query, '', -2) . ';';
            
            parent::$db->query($insert_query);
        }
    }
    
    /**
     * deletePlanetById
     *
     * @param int $planet_id The planed ID
     *
     * @return void
     */
    public static function deletePlanetById($planet_id)
    {
    }
    
    /**
     * deletePlanetByCoords
     *
     * @param int $galaxy The galaxy
     * @param int $system The system
     * @param int $planet The planet
     * @param int $type   The planet type (planet|moon)
     *
     * @return void
     */
    public static function deletePlanetByCoords($galaxy, $system, $planet, $type)
    {
    }
}

/* end of PlanetLib.php */
