<?php
/**
 * Fleet3 Controller
 *
 * PHP Version 5.5+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace application\controllers\game;

use application\core\XGPCore;
use application\libraries\FleetsLib;
use application\libraries\FunctionsLib;

/**
 * Fleet3 Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Fleet3 extends XGPCore
{
    const MODULE_ID = 8;

    private $langs;
    private $current_user;
    private $current_planet;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        $this->langs            = parent::$lang;
        $this->current_user     = parent::$users->getUserData();
        $this->current_planet   = parent::$users->getPlanetData();

        $this->buildPage();
    }

    /**
     * __destructor
     *
     * @return void
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * buildPage
     *
     * @return void
     */
    private function buildPage()
    {
        if (!isset($_POST) or empty($_POST)) {
            FunctionsLib::redirect('game.php?page=fleet1');
        }

        $pricelist  = parent::$objects->getPrice();
        $reslist    = parent::$objects->getObjectsList();
        $lang       = $this->langs;

        #####################################################################################################
        // SOME DEFAULT VALUES
        #####################################################################################################
        // ARRAYS
        $exp_values     = array(1, 2, 3, 4, 5);
        $hold_values    = array(0, 1, 2, 4, 8, 16, 32);

        // LANG
        $this->langs['js_path'] = XGP_ROOT . JS_PATH;
        $parse                  = $this->langs;

        // LOAD TEMPLATES REQUIRED
        $mission_row_template   = parent::$page->getTemplate('fleet/fleet3_mission_row');
        $input_template         = parent::$page->getTemplate('fleet/fleet3_inputs');
        $stay_template          = parent::$page->getTemplate('fleet/fleet3_stay_row');
        $options_template       = parent::$page->getTemplate('fleet/fleet_options');

        // OTHER VALUES
        $galaxy             = (int)$_POST['galaxy'];
        $system             = (int)$_POST['system'];
        $planet             = (int)$_POST['planet'];
        $planettype         = (int)$_POST['planettype'];
        $fleet_acs          = (int)$_POST['fleet_group'];
        $YourPlanet         = false;
        $UsedPlanet         = false;
        $MissionSelector    = '';
        $available_ships    = $this->getAvailableShips($_POST);
        
        // QUERYS
        $select = parent::$db->queryFetch(
            "SELECT `planet_user_id`
            FROM `" . PLANETS . "`
            WHERE `planet_galaxy` = '" . $galaxy . "'
            AND `planet_system` = '" . $system . "'
            AND `planet_planet` = '" . $planet . "'
            AND `planet_type` = '" . $planettype . "';"
        );

        if ($select) {
                
            if ($select['planet_user_id'] == $this->current_user['user_id']) {
                
                $YourPlanet = true;
                $UsedPlanet = true;
            } else {
                
                $UsedPlanet = true;
            }
        }
        
        if ($planettype == 2) {

            if ($available_ships['ship209'] >= 1) {
                
                $missiontype[8] = $this->langs['type_mission'][8];
            } else {
                
                $missiontype = array();
            }
        } elseif ($planettype == 1 or $planettype == 3) {
            
            if ($available_ships['ship208'] >= 1 && !$UsedPlanet) {

                $missiontype[7] = $this->langs['type_mission'][7];
            } elseif ($available_ships['ship210'] >= 1 && !$YourPlanet) {

                $missiontype[6] = $this->langs['type_mission'][6];
            }

            if ($available_ships['ship202'] >= 1 or
                $available_ships['ship203'] >= 1 or
                $available_ships['ship204'] >= 1 or
                $available_ships['ship205'] >= 1 or
                $available_ships['ship206'] >= 1 or
                $available_ships['ship207'] >= 1 or
                $available_ships['ship210'] >= 1 or
                $available_ships['ship211'] >= 1 or
                $available_ships['ship213'] >= 1 or
                $available_ships['ship214'] >= 1 or
                $available_ships['ship215'] >= 1) {

                if (!$YourPlanet) {

                    $missiontype[1] = $this->langs['type_mission'][1];
                }

                $missiontype[3] = $this->langs['type_mission'][3];
                $missiontype[5] = $this->langs['type_mission'][5];
            }
        } elseif ($available_ships['ship209'] >= 1 or $available_ships['ship208']) {
            $missiontype[3] = $this->langs['type_mission'][3];
        }

        if ($YourPlanet) {

            $missiontype[4] = $this->langs['type_mission'][4];
        }

        if ($planettype == 3 || $planettype == 1 && ($fleet_acs > 0) && $UsedPlanet) {
            
            if ($this->acsExists($fleet_acs, $galaxy, $system, $planet, $planettype)) {

                $missiontype[2] = $this->langs['type_mission'][2];
            }
        }

        if ($planettype == 3 && $available_ships['ship214'] >= 1 && !$YourPlanet && $UsedPlanet) {

            $missiontype[9] = $this->langs['type_mission'][9];
        }

        $fleetarray     = unserialize(base64_decode(str_rot13($_POST['usedfleet'])));
        $mission        = $_POST['target_mission'];
        $SpeedFactor    = $_POST['speedfactor'];
        $AllFleetSpeed  = FleetsLib::fleetMaxSpeed($fleetarray, 0, $this->current_user);
        $GenFleetSpeed  = $_POST['speed'];
        $MaxFleetSpeed  = min($AllFleetSpeed);
        $distance       = FleetsLib::targetDistance(
            $_POST['thisgalaxy'],
            $galaxy,
            $_POST['thissystem'],
            $system,
            $_POST['thisplanet'],
            $planet
        );
        
        $duration       = FleetsLib::missionDuration($GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor);
        
        $consumption    = FleetsLib::fleetConsumption(
            $fleetarray,
            $SpeedFactor,
            $duration,
            $distance,
            $this->current_user
        );

        #####################################################################################################
        // INPUTS DATA
        #####################################################################################################
        $parse['metal']         = floor($this->current_planet['planet_metal']);
        $parse['crystal']       = floor($this->current_planet['planet_crystal']);
        $parse['deuterium']     = floor($this->current_planet['planet_deuterium']);
        $parse['consumption']   = $consumption;
        $parse['distance']      = $distance;
        $parse['speedfactor']   = $_POST['speedfactor'];
        $parse['thisgalaxy']    = $_POST['thisgalaxy'];
        $parse['thissystem']    = $_POST['thissystem'];
        $parse['thisplanet']    = $_POST['thisplanet'];
        $parse['galaxy']        = $galaxy;
        $parse['system']        = $system;
        $parse['planet']        = $planet;
        $parse['thisplanettype']= $_POST['thisplanettype'];
        $parse['planettype']    = $planettype;
        $parse['speedallsmin']  = $_POST['speedallsmin'];
        $parse['speed']         = $_POST['speed'];
        $parse['speedfactor']   = $_POST['speedfactor'];
        $parse['usedfleet']     = $_POST['usedfleet'];
        $parse['maxepedition']  = $_POST['maxepedition'];
        $parse['curepedition']  = $_POST['curepedition'];
        $parse['fleet_group']   = $_POST['fleet_group'];
        $parse['acs_target_mr'] = $_POST['acs_target_mr'];

        #####################################################################################################
        // EXTRA INPUTS
        #####################################################################################################
        $input_extra    = '';

        foreach ($fleetarray as $Ship => $Count) {

            $input_parse['ship']        = $Ship;
            $input_parse['amount']      = $Count;
            $input_parse['capacity']    = $pricelist[$Ship]['capacity'];
            $input_parse['consumption'] = FleetsLib::shipConsumption($Ship, $this->current_user);
            $input_parse['speed']       = FleetsLib::fleetMaxSpeed('', $Ship, $this->current_user);

            $input_extra .= parent::$page->parseTemplate($input_template, $input_parse);
        }

        #####################################################################################################
        // TOP TABLE TITLE
        #####################################################################################################
        
        $parse['title'] = $_POST['thisgalaxy'] . ':' . $_POST['thissystem'] . ':' . $_POST['thisplanet'] . ' - ';
        
        if ($_POST['thisplanettype'] == 1) {

            $parse['title'] .= $this->langs['fl_planet'];

        } elseif ($_POST['thisplanettype'] == 3) {

            $parse['title'] .= $this->langs['fl_moon'];
        }

        #####################################################################################################
        // MISSION TYPES
        #####################################################################################################
        if (count($missiontype) > 0) {
            
            if ($planet == 16) {
                
                $parse_mission['value']                 = 15;
                $parse_mission['mission']               = $this->langs['type_mission'][15];
                $parse_mission['expedition_message']    = $this->langs['fl_expedition_alert_message'];
                $parse_mission['id']                    = ' ';
                $parse_mission['checked']               = ' checked="checked"';

                $MissionSelector    .= parent::$page->parseTemplate($mission_row_template, $parse_mission);
            } else {

                $i  = 0;

                foreach ($missiontype as $a => $b) {

                    $parse_mission['value']                 = $a;
                    $parse_mission['mission']               = $b;
                    $parse_mission['expedition_message']    = '';
                    $parse_mission['id']                    = ' id="inpuT_' . $i . '" ';
                    $parse_mission['checked']               = (($mission == $a) ? ' checked="checked"' : '');

                    $i++;

                    $MissionSelector    .= parent::$page->parseTemplate($mission_row_template, $parse_mission);
                }
            }
        } else {
            FunctionsLib::redirect('game.php?page=fleet1');
        }

        #####################################################################################################
        // STAY / EXPEDITION BLOCKS
        #####################################################################################################
        $stay_row['options']    = '';
        $StayBlock              = '';

        if ($planet == 16) {

            $stay_row['stay_type']  = 'expeditiontime';

            foreach ($exp_values as $value) {
                $stay['value']      = $value;
                $stay['selected']   = '';
                $stay['title']      = $value;

                $stay_row['options']    .= parent::$page->parseTemplate($options_template, $stay);
            }

            $StayBlock  = parent::$page->parseTemplate($stay_template, array_merge($stay_row, $this->langs));
        } elseif (isset($missiontype[5])) {

            $stay_row['stay_type']  = 'holdingtime';

            foreach ($hold_values as $value) {

                $stay['value']      = $value;
                $stay['selected']   = (($value == 1) ? ' selected' : '');
                $stay['title']      = $value;

                $stay_row['options']  .= parent::$page->parseTemplate($options_template, $stay);
            }

            $StayBlock  = parent::$page->parseTemplate($stay_template, array_merge($stay_row, $this->langs));
        }

        $parse['input_extra']       = $input_extra;
        $parse['missionselector']   = $MissionSelector;
        $parse['stayblock']         = $StayBlock;

        parent::$page->display(
            parent::$page->parseTemplate(parent::$page->getTemplate('fleet/fleet3_table'), $parse)
        );
    }
    
    /**
     * acsExists
     *
     * @param int $fleet_acs    Fleet ACS ID
     * @param int $galaxy       Galaxy
     * @param int $system       System
     * @param int $planet       Planet
     * @param int $planettype   Planet Type
     *
     * @return boolean
     */
    private function acsExists($fleet_acs, $galaxy, $system, $planet, $planettype)
    {
        $acs = parent::$db->queryFetch(
            "SELECT 
                COUNT(`acs_fleet_id`) AS `amount`
            FROM `" . ACS_FLEETS . "`
            WHERE `acs_fleet_id` = '" . $fleet_acs . "' AND 
                `acs_fleet_galaxy` = '" . $galaxy . "' AND 
                `acs_fleet_system` = '" . $system . "' AND 
                `acs_fleet_planet` = '" . $planet . "' AND 
                `acs_fleet_planet_type` = '" . $planettype . "';"
        );

        if ($acs['amount'] > 0) {

            return true;
        }
        
        return false;
    }
    
    /**
     * getAvailableShips
     *
     * @param array $post_data Post Data
     *
     * @return array
     */
    private function getAvailableShips($post_data)
    {
        if (is_array($post_data)) {
            
            $ships      = array();
            $resource   = parent::$objects->getObjects();
            
            foreach ($resource as $ship => $amount) {

                if (strpos($amount, 'ship') !== false) {

                    if (isset($post_data['ship' . $ship])) {
                        $ships['ship' . $ship]  = $post_data['ship' . $ship];
                    } else {
                        $ships['ship' . $ship] = 0;
                    }
                }
            }

            return $ships;
        }
        
        return array();
    }
}

/* end of fleet3.php */
