<?php
/**
 * Planet Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\libraries;

use App\core\enumerators\PlanetTypesEnumerator;
use App\core\Language;
use App\core\XGPCore;
use App\libraries\Formulas;
use App\libraries\Functions;

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

        // load model
        parent::loadModel('libraries/planetlib');

        // load Language
        $this->loadLanguage();
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
        $planet_exist = $this->Planetlib_Model->checkPlanetExists($galaxy, $system, $position);

        if (!$planet_exist) {
            $planet = Formulas::getPlanetSize($position, $main);
            $temp = Formulas::setPlanetTemp($position);
            $name = ($name == '') ? $this->langs->line('colony') : $name;

            if ($main == true) {
                $name = $this->langs->line('homeworld');
            }

            $this->Planetlib_Model->createNewPlanet(
                [
                    'planet_name' => $name,
                    'planet_user_id' => $owner,
                    'planet_galaxy' => $galaxy,
                    'planet_system' => $system,
                    'planet_planet' => $position,
                    'planet_last_update' => time(),
                    'planet_type' => PlanetTypesEnumerator::PLANET,
                    'planet_image' => Formulas::setPlanetImage($system, $position),
                    'planet_diameter' => $planet['planet_diameter'],
                    'planet_field_max' => $planet['planet_field_max'],
                    'planet_temp_min' => $temp['min'],
                    'planet_temp_max' => $temp['max'],
                    'planet_metal' => BUILD_METAL,
                    'planet_metal_perhour' => Functions::readConfig('metal_basic_income'),
                    'planet_crystal' => BUILD_CRISTAL,
                    'planet_crystal_perhour' => Functions::readConfig('crystal_basic_income'),
                    'planet_deuterium' => BUILD_DEUTERIUM,
                    'planet_deuterium_perhour' => Functions::readConfig('deuterium_basic_income'),
                    'planet_b_building_id' => '0',
                    'planet_b_hangar_id' => '',
                ]
            );

            return true;
        }

        return false;
    }

    /**
     * setNewMoon
     *
     * @param int    $galaxy     Galaxy
     * @param int    $system     System
     * @param int    $position   Position
     * @param int    $owner      Owner
     * @param string $name       Moon name
     * @param int    $chance     Chance
     * @param int    $size       Size
     * @param int    $max_fields Max Fields
     * @param int    $min_temp   Min Temp
     * @param int    $max_temp   Max Temp
     *
     * @return string
     */
    public function setNewMoon($galaxy, $system, $position, $owner, $name = '', $chance = 0, $size = 0, $max_fields = 1, $min_temp = 0, $max_temp = 0)
    {
        $MoonPlanet = $this->Planetlib_Model->checkMoonExists($galaxy, $system, $position);

        if ($MoonPlanet['id_moon'] == '' && $MoonPlanet['planet_id'] != 0) {
            $SizeMin = 2000 + ($chance * 100);
            $SizeMax = 6000 + ($chance * 200);
            $temp = Formulas::setPlanetTemp($position);
            $size = $chance == 0 ? $size : mt_rand($SizeMin, $SizeMax);
            $size = $size == 0 ? mt_rand(2000, 6000) : $size;
            $max_fields = $max_fields == 0 ? 1 : $max_fields;

            $this->Planetlib_Model->createNewPlanet(
                [
                    'planet_name' => $name == '' ? $this->langs->line('moon') : $name,
                    'planet_user_id' => $owner,
                    'planet_galaxy' => $galaxy,
                    'planet_system' => $system,
                    'planet_planet' => $position,
                    'planet_last_update' => time(),
                    'planet_type' => PlanetTypesEnumerator::MOON,
                    'planet_image' => 'mond',
                    'planet_diameter' => $size,
                    'planet_field_max' => $max_fields,
                    'planet_temp_min' => $min_temp == 0 ? $temp['min'] : $min_temp,
                    'planet_temp_max' => $max_temp == 0 ? $temp['max'] : $max_temp,
                    'planet_b_building_id' => '0',
                    'planet_b_hangar_id' => '',
                ]
            );

            return true;
        }

        return false;
    }

    /**
     * Load CI language
     *
     * @return void
     */
    private function loadLanguage()
    {
        $lang = new Language();
        $lang = $lang->loadLang('game/global', true);

        $this->langs = $lang;
    }
}
