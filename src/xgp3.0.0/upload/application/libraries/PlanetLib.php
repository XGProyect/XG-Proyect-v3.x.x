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
     *
     * @var FormulaLib
     */
    private $formula;
    
    /**
     *
     * @var array
     */
    private $langs;
    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->langs    = parent::$lang;
        $this->formula  = FunctionsLib::loadLibrary('FormulaLib');
    }
    
    /**
     * createPlanetWithOptions
     *
     * @param array   $data        The data as an array
     * @param boolean $full_insert Insert all the required tables
     *
     * @return void
     */
    public static function createPlanetWithOptions($data, $full_insert = true)
    {
        if (is_array($data)) {
            
            $insert_query   = 'INSERT INTO ' . PLANETS . ' SET ';
            
            foreach ($data as $column => $value) {
                $insert_query .= "`" . $column . "` = '" . $value . "', ";
            }
                
            // Remove last comma
            $insert_query   = substr_replace($insert_query, '', -2) . ';';
            
            parent::$db->query($insert_query);
            
            // insert extra required tables
            if ($full_insert) {

                // get the last inserted planet id
                $planet_id  = parent::$db->insertId();
                
                // create the buildings, defenses and ships tables
                self::createBuildings($planet_id);
                self::createDefenses($planet_id);
                self::createShips($planet_id);
            }
        }
    }
    
    /**
     * setNewPlanet
     *
     * @param int     $galaxy   Galaxy
     * @param int     $system   System
     * @param int     $position Position
     * @param int     $owner    Planet owner Id
     * @param string  $name     Planet name
     * @param boolean $main     Main planet
     *
     * @return boolean
     */
    public function setNewPlanet($galaxy, $system, $position, $owner, $name = '', $main = false)
    {
        $planet_exist   = parent::$db->queryFetch(
            "SELECT `planet_id`
            FROM " . PLANETS . "
            WHERE `planet_galaxy` = '" . $galaxy . "' AND
                `planet_system` = '" . $system . "' AND
                `planet_planet` = '" . $position . "';"
        );

        if (!$planet_exist) {

            $planet = $this->formula->getPlanetSize($position, $main);
            $temp   = $this->formula->setPlanetTemp($position);
            $name   = ($name == '') ? $this->langs['ge_colony'] : $name;
            
            if ($main == true) {
                $name   = $this->langs['ge_home_planet'];
            }
            
            $this->createPlanetWithOptions(
                [
                    'planet_name' => $name,
                    'planet_user_id' => $owner,
                    'planet_galaxy' => $galaxy,
                    'planet_system' => $system,
                    'planet_planet' => $position,
                    'planet_last_update' => time(),
                    'planet_type' => '1',
                    'planet_image' => $this->formula->setPlanetImage($system, $position),
                    'planet_diameter' => $planet['planet_diameter'],
                    'planet_field_max' => $planet['planet_field_max'],
                    'planet_temp_min' => $temp['min'],
                    'planet_temp_max' => $temp['max'],
                    'planet_metal' => BUILD_METAL,
                    'planet_metal_perhour' => FunctionsLib::readConfig('metal_basic_income'),
                    'planet_crystal' => BUILD_CRISTAL,
                    'planet_crystal_perhour' => FunctionsLib::readConfig('crystal_basic_income'),
                    'planet_deuterium' => BUILD_DEUTERIUM,
                    'planet_deuterium_perhour' => FunctionsLib::readConfig('deuterium_basic_income')
                ]
            );

            return true;
        }
        
        return false;
    }
    
    
    /**
     * setNewMoon
     *
     * @param int    $galaxy   Galaxy
     * @param int    $system   System
     * @param int    $position Position
     * @param int    $owner    Owner
     * @param string $name     Moon name
     * @param int    $chance   Chance
     * @param int    $size     Size
     *
     * @return string
     */
    public function setNewMoon($galaxy, $system, $position, $owner, $name = '', $chance = 0, $size = 0)
    {
        $MoonPlanet = parent::$db->queryFetch(
            "SELECT pm2.`planet_id`,
            pm2.`planet_name`,
            pm2.`planet_temp_max`,
            pm2.`planet_temp_min`,
            (SELECT pm.`planet_id` AS `id_moon`
                    FROM " . PLANETS . " AS pm
                    WHERE pm.`planet_galaxy` = '". $galaxy ."' AND
                                    pm.`planet_system` = '". $system ."' AND
                                    pm.`planet_planet` = '". $position ."' AND
                                    pm.`planet_type` = 3) AS `id_moon`
            FROM " . PLANETS . " AS pm2
            WHERE pm2.`planet_galaxy` = '". $galaxy ."' AND
                    pm2.`planet_system` = '". $system ."' AND
                    pm2.`planet_planet` = '". $position ."';"
        );

        if ($MoonPlanet['id_moon'] == '' && $MoonPlanet['planet_id'] != 0) {

            $SizeMin    = 2000 + ($chance * 100);
            $SizeMax    = 6000 + ($chance * 200);
            $temp       = $this->formula->setPlanetTemp($position);
            $size       = $chance == 0 ? $size : mt_rand($SizeMin, $SizeMax);

            
            $this->createPlanetWithOptions(
                [
                    'planet_name' => $name == '' ? $this->langs['fcm_moon'] : $name,
                    'planet_user_id' => $owner,
                    'planet_galaxy' => $galaxy,
                    'planet_system' => $system,
                    'planet_planet' => $position,
                    'planet_last_update' => time(),
                    'planet_type' => '3',
                    'planet_image' => 'mond',
                    'planet_diameter' => $size,
                    'planet_field_max' => '1',
                    'planet_temp_min' => $temp['min'],
                    'planet_temp_max' => $temp['max']
                ]
            );
        
            return true;
        }
        
        return false;
    }
    
    /**
     * createBuildings
     *
     * @param int $planet_id The planet id
     *
     * @return void
     */
    public static function createBuildings($planet_id)
    {
        parent::$db->query(
            "INSERT INTO " . BUILDINGS . " SET `building_planet_id` = '".$planet_id."';"
        );
    }
    
    /**
     * createDefenses
     *
     * @param int $planet_id The planet id
     *
     * @return void
     */
    public static function createDefenses($planet_id)
    {
        parent::$db->query(
            "INSERT INTO " . DEFENSES . " SET `defense_planet_id` = '".$planet_id."';"
        );
    }
    
    /**
     * createShips
     *
     * @param int $planet_id The planet id
     *
     * @return void
     */
    public static function createShips($planet_id)
    {
        parent::$db->query(
            "INSERT INTO " . SHIPS . " SET `ship_planet_id` = '".$planet_id."';"
        );
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
