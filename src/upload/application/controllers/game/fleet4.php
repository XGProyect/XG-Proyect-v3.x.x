<?php
/**
 * Fleet4 Controller
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

use application\core\Controller;
use application\core\enumerators\MissionsEnumerator as Missions;
use application\core\enumerators\PlanetTypesEnumerator as PlanetTypes;
use application\core\enumerators\ShipsEnumerator as Ships;
use application\libraries\fleets\Fleets;
use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\premium\Premium;
use application\libraries\research\Researches;
use const BUDDY;
use const DEBRIS_LIFE_TIME;
use const FLEETS;

/**
 * Fleet4 Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Fleet4 extends Controller
{

    /**
     * 
     * @var int
     */
    const MODULE_ID = 8;

    /**
     * 
     * @var string
     */
    const REDIRECT_TARGET = 'game.php?page=movement';
    
    /**
     *
     * @var array
     */
    private $_user;

    /**
     *
     * @var array
     */
    private $_planet;

    /**
     *
     * @var \Fleets
     */
    private $_fleets = null;
    
    /**
     *
     * @var \Research
     */
    private $_research = null;
    
    /**
     *
     * @var \Premium
     */
    private $_premium = null;
    
    /**
     * Already filtered POST data
     * 
     * @var array
     */
    private $_clean_input_data = [];
    
    /**
     *
     * @var array
     */
    private $_fleet_data = [
        'fleet_owner' => 0,
        'fleet_mission' => 0,
        'fleet_amount' => 0,
        'fleet_array' => '',
        'fleet_start_time' => 0,
        'fleet_start_galaxy' => 0,
        'fleet_start_system' => 0,
        'fleet_start_planet' => 0,
        'fleet_start_type' => 0,
        'fleet_end_time' => 0,
        'fleet_end_stay' => 0,
        'fleet_end_galaxy' => 0,
        'fleet_end_system' => 0,
        'fleet_end_planet' => 0,
        'fleet_end_type' => 0,
        'fleet_resource_metal' => 0,
        'fleet_resource_crystal' => 0,
        'fleet_resource_deuterium' => 0,
        'fleet_fuel' => 0,
        'fleet_target_owner' => 0,
        'fleet_group' => 0,
        'fleet_creation' => 'NOW()'
    ];
    
    /**
     *
     * @var array
     */
    private $_target_data = [];
    
    /**
     *
     * @var boolean 
     */
    private $_own_planet = false;
    
    /**
     *
     * @var boolean 
     */
    private $_occupied_planet = false;
    
    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/fleet');
        
        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();
        
        // set planet data
        $this->_planet = $this->getPlanetData();

        // init a new fleets object
        $this->setUpFleets();

        // build the page
        $this->buildPage();
    }

    /**
     * Creates a new ships object that will handle all the ships
     * creation methods and actions
     * 
     * @return void
     */
    private function setUpFleets()
    {
        $this->_fleets = new Fleets(
            $this->Fleet_Model->getAllFleetsByUserId($this->_user['user_id']),
            $this->_user['user_id']
        );
        
        $this->_research = new Researches(
            [$this->_user],
            $this->_user['user_id']
        );
        
        $this->_premium = new Premium(
            [$this->_user],
            $this->_user['user_id']
        );
    }

    /**
     * Build the page
     * 
     * @return void
     */
    private function buildPage()
    {
        // filter stuff from fleet1, fleet2 and fleet3
        $this->setInputsData();
        
        // get the target
        $this->getTarget();
        
        // validate all the received data
        if (!$this->runValidations()) {
            
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }
        
        var_dump($this->_fleet_data);
        die('ok');
        
        // final step, send and redirect
        $this->sendFleet();
        
        $resource = parent::$objects->getObjects();
        $pricelist = parent::$objects->getPrice();

        if ($_POST['resource1'] + $_POST['resource2'] + $_POST['resource3'] < 1 && $_POST['mission'] == Missions::transport) {
            
            $this->showMessage(
                FormatLib::customColor($this->getLang()['fl_empty_transport'], 'lime')
            );
        }

        $AllFleetSpeed = FleetsLib::fleetMaxSpeed($fleetarray, 0, $this->_user);
        $GenFleetSpeed = $this->getFleetData()['speed'];
        $SpeedFactor = FunctionsLib::fleetSpeedFactor();
        $MaxFleetSpeed = min($AllFleetSpeed);

        if (!isset($fleetarray)) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        $distance = FleetsLib::targetDistance($_POST['thisgalaxy'], $_POST['galaxy'], $_POST['thissystem'], $_POST['system'], $_POST['thisplanet'], $_POST['planet']);
        $duration = FleetsLib::missionDuration($GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor);
        $consumption = FleetsLib::fleetConsumption($fleetarray, $SpeedFactor, $duration, $distance, $this->_user);

        $fleet['start_time'] = $duration + time();

        // START CODE BY JSTAR
        if ($_POST['mission'] == Missions::expedition) {
            $StayDuration = floor($_POST['expeditiontime']);

            if ($StayDuration > 0) {
                $StayDuration = $StayDuration * 3600;
                $StayTime = $fleet['start_time'] + $StayDuration;
            } else {
                FunctionsLib::redirect(self::REDIRECT_TARGET);
            }
        } // END CODE BY JSTAR
        elseif ($_POST['mission'] == Missions::stay) {
            $StayDuration = $_POST['holdingtime'] * 3600;
            $StayTime = $fleet['start_time'] + $_POST['holdingtime'] * 3600;
        } else {
            $StayDuration = 0;
            $StayTime = 0;
        }

        $fleet['end_time'] = $StayDuration + (2 * $duration) + time();
        $FleetStorage = 0;
        $FleetShipCount = 0;
        $fleet_array = "";
        $FleetSubQRY = "";

        //fix by jstar
        $haveSpyProbos = false;

        foreach ($fleetarray as $Ship => $Count) {
            $Count = intval($Count);

            if ($Ship == Ships::ship_espionage_probe) {
                $haveSpyProbos = true;
            }

            $FleetStorage += $pricelist[$Ship]['capacity'] * $Count;
            $FleetShipCount += $Count;
            $fleet_array .= $Ship . "," . $Count . ";";
            $FleetSubQRY .= "`" . $resource[$Ship] . "` = `" . $resource[$Ship] . "` - " . $Count . ", ";
        }

        if (!$haveSpyProbos && $_POST['mission'] == Missions::spy) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        $FleetStorage -= $consumption;
        $StorageNeeded = 0;

        $_POST['resource1'] = max(0, (int) trim($_POST['resource1']));
        $_POST['resource2'] = max(0, (int) trim($_POST['resource2']));
        $_POST['resource3'] = max(0, (int) trim($_POST['resource3']));

        if ($_POST['resource1'] < 1) {
            $TransMetal = 0;
        } else {
            $TransMetal = $_POST['resource1'];
            $StorageNeeded += $TransMetal;
        }

        if ($_POST['resource2'] < 1) {
            $TransCrystal = 0;
        } else {
            $TransCrystal = $_POST['resource2'];
            $StorageNeeded += $TransCrystal;
        }
        if ($_POST['resource3'] < 1) {
            $TransDeuterium = 0;
        } else {
            $TransDeuterium = $_POST['resource3'];
            $StorageNeeded += $TransDeuterium;
        }

        $StockMetal = $this->_planet['planet_metal'];
        $StockCrystal = $this->_planet['planet_crystal'];
        $StockDeuterium = $this->_planet['planet_deuterium'];
        $StockDeuterium -= $consumption;

        $StockOk = false;

        if ($StockMetal >= $TransMetal) {
            if ($StockCrystal >= $TransCrystal) {
                if ($StockDeuterium >= $TransDeuterium) {
                    $StockOk = true;
                }
            }
        }

        if (!$StockOk) {

            $this->showMessage(
                FormatLib::colorRed($this->getLang()['fl_no_enought_deuterium'] . FormatLib::prettyNumber($consumption))
            );
        }

        if ($StorageNeeded > $FleetStorage) {

            $this->showMessage(
                FormatLib::colorRed($this->getLang()['fl_no_enought_cargo_capacity'] . FormatLib::prettyNumber($StorageNeeded - $FleetStorage))
            );
        }

        if (FunctionsLib::readConfig('adm_attack') != 0 
            && $this->_target_data['user_authlevel'] >= 1 
            && $this->_user['user_authlevel'] == 0) {

            $this->showMessage(
                $this->getLang()['fl_admins_cannot_be_attacked']
            );
        }

        if ($fleet_group != 0) {
            $AksStartTime = $this->_db->queryFetch("SELECT MAX(`fleet_start_time`) AS Start
														FROM " . FLEETS . "
														WHERE `fleet_group` = '" . $fleet_group . "';");

            if ($AksStartTime['Start'] >= $fleet['start_time']) {
                $fleet['end_time'] += $AksStartTime['Start'] - $fleet['start_time'];
                $fleet['start_time'] = $AksStartTime['Start'];
            } else {
                $this->_db->query("UPDATE " . FLEETS . " SET
										`fleet_start_time` = '" . $fleet['start_time'] . "',
										`fleet_end_time` = fleet_end_time + '" . ($fleet['start_time'] - $AksStartTime['Start']) . "'
										WHERE `fleet_group` = '" . $fleet_group . "';");

                $fleet['end_time'] += $fleet['start_time'] - $AksStartTime['Start'];
            }
        }
        
        /*
        $this->_db->query("INSERT INTO " . FLEETS . " SET
							`fleet_owner` = '" . $this->_user['user_id'] . "',
							`fleet_mission` = '" . (int) $_POST['mission'] . "',
							`fleet_amount` = '" . (int) $FleetShipCount . "',
							`fleet_array` = '" . $fleet_array . "',
							`fleet_start_time` = '" . $fleet['start_time'] . "',
							`fleet_start_galaxy` = '" . (int) $_POST['thisgalaxy'] . "',
							`fleet_start_system` = '" . (int) $_POST['thissystem'] . "',
							`fleet_start_planet` = '" . (int) $_POST['thisplanet'] . "',
							`fleet_start_type` = '" . (int) $_POST['thisplanettype'] . "',
							`fleet_end_time` = '" . (int) $fleet['end_time'] . "',
							`fleet_end_stay` = '" . (int) $StayTime . "',
							`fleet_end_galaxy` = '" . (int) $_POST['galaxy'] . "',
							`fleet_end_system` = '" . (int) $_POST['system'] . "',
							`fleet_end_planet` = '" . (int) $_POST['planet'] . "',
							`fleet_end_type` = '" . (int) $_POST['planettype'] . "',
							`fleet_resource_metal` = '" . $TransMetal . "',
							`fleet_resource_crystal` = '" . $TransCrystal . "',
							`fleet_resource_deuterium` = '" . $TransDeuterium . "',
                                                        `fleet_fuel` = '" . $consumption . "',    
							`fleet_target_owner` = '" . (int) $this->_target_data['planet_user_id'] . "',
							`fleet_group` = '" . (int) $fleet_group . "',
							`fleet_creation` = '" . time() . "';");

        $this->_db->query("UPDATE `" . PLANETS . "` AS p
								INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id` SET
								$FleetSubQRY
								`planet_metal` = `planet_metal` - " . $TransMetal . ",
								`planet_crystal` = `planet_crystal` - " . $TransCrystal . ",
								`planet_deuterium` = `planet_deuterium` - " . ($TransDeuterium + $consumption) . "
								WHERE `planet_id` = " . $this->_planet['planet_id'] . ";");*/
    }
    
    /**
     * Set inputs data
     * 
     * @return array
     */
    private function setInputsData()
    {
        $exp_time = $this->_research->getCurrentResearch()->getResearchAstrophysics();
        
        $min_exp_time = $exp_time <= 0 ? 0 : 1;
        $max_exp_time = $exp_time;
        
        $data = filter_input_array(INPUT_POST, [
            'mission' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => 15]
            ],
            'resource1' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 0, 'max_range' => $this->_planet['planet_metal']]
            ],
            'resource2' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 0, 'max_range' => $this->_planet['planet_crystal']]
            ],
            'resource3' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 0, 'max_range' => $this->_planet['planet_deuterium']]
            ],
            'expeditiontime' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => $min_exp_time, 'max_range' => $max_exp_time]
            ],
            'holdingtime' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 0, 'max_range' => 32]
            ]
        ]);

        if (is_null($data)) {
            
            FunctionsLib::redirect('game.php?page=fleet1');
        }
        
        $this->_clean_input_data = $data;
    }
    
    /**
     * Get target user info and planet info
     * 
     * @return void
     */
    private function getTarget()
    {
        $target_data = $this->getTargetData();
        
        $target = $this->Fleet_Model->getTargetDataByCoords(
            $target_data['galaxy'],
            $target_data['system'],
            $target_data['planet'],
            $target_data['type']
        );

        if ($target) {

            $this->_occupied_planet = true;
            
            // set target data
            $this->_target_data = $target;
            
            // validate owner
            if ($target['planet_user_id'] == $this->_user['user_id']) {
                
                $this->_own_planet = true;
            }
            
            if ($target['planet_destroyed'] != 0) {
                
                FunctionsLib::redirect(self::REDIRECT_TARGET);
            }
            
            // set target owner
            $this->_fleet_data['fleet_target_owner'] = $target['planet_user_id'];
        }

        // set coords data
        $this->_fleet_data['fleet_start_galaxy'] = $this->_planet['planet_galaxy'];
        $this->_fleet_data['fleet_start_system'] = $this->_planet['planet_system'];
        $this->_fleet_data['fleet_start_planet'] = $this->_planet['planet_planet'];
        $this->_fleet_data['fleet_start_type'] = $this->_planet['planet_type'];
        $this->_fleet_data['fleet_end_galaxy'] = $target_data['galaxy'];
        $this->_fleet_data['fleet_end_system'] = $target_data['system'];
        $this->_fleet_data['fleet_end_planet'] = $target_data['planet'];
        $this->_fleet_data['fleet_end_type'] = $target_data['type'];
    }
    
    /**
     * Run multiple validations
     * 
     * @return boolean
     */
    private function runValidations()
    {
        $validations = [
            'vacations', 'acs', 'ships', 'mission', 'noobProtection', 'fleets'
        ];
        
        foreach ($validations as $validation) {
            
            if (!$this->{'validate' . ucfirst($validation)}()) {
                
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Validate vacations for both players
     * 
     * @return boolean
     */
    private function validateVacations()
    {
        if (parent::$users->isOnVacations($this->_user)) {

            $this->showMessage($this->getLang()['fl_vacation_mode_active']);
        }

        if (isset($this->_target_data) 
            && parent::$users->isOnVacations($this->_target_data)
            && $this->_clean_input_data['mission'] != Missions::recycle) {
            
            $this->showMessage($this->getLang()['fl_in_vacation_player']);
        }
        
        // set owner
        $this->_fleet_data['fleet_owner'] = $this->_user['user_id'];
        
        return true;
    }
    
    /**
     * Validate any current ACS
     * 
     * @return boolean
     */
    private function validateAcs()
    {
        $target_data = $this->getTargetData();
        
        if ($target_data['group'] > 0
            && $this->_clean_input_data['mission'] == Missions::acs) {

            $target_string =    'g' . (int)$target_data['galaxy'] . 
                                's' . (int)$target_data['system'] .
                                'p' . (int)$target_data['planet'] .
                                't' . (int)$target_data['type'];

            if ($target_data['acs_target'] == $target_string
                && $this->Fleet_Model->getAcsCount($target_data['group']) > 0) {

                // set acs group
                $this->_fleet_data['fleet_group'] = $target_data['group'];

                return true;
            }
            
            return false;
        }

        return true;
    }
    
    /**
     * Validate if the received amount of ships is valid
     * 
     * @return boolean
     */
    private function validateShips()
    {
        // post/session fleet
        $fleet = $this->getSessionShips();
        
        // planet ships
        $planet_ships = $this->Fleet_Model->getShipsByPlanetId($this->_planet['planet_id']);
        
        // objects
        $objects = parent::$objects->getObjects();
        
        if ($fleet) {

            foreach ($fleet as $ship_id => $amount) {

                if (!isset($planet_ships[$objects[$ship_id]]) 
                    or ((int)$amount > $planet_ships[$objects[$ship_id]])) {
                    
                    return false;
                }
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Validate the mission
     * 
     * @return boolean
     */
    private function validateMission()
    {
        // post/session fleet
        $fleet = $this->getSessionShips();
        
        // clean data from post
        $data = $this->_clean_input_data;
        
        // target data
        $target = $this->_target_data;

        if ($data['mission'] == Missions::attack
            or $data['mission'] == Missions::spy) {
            
            if ($this->_own_planet) {
                
                return false;
            }
        }

        if ($data['mission'] == Missions::deploy
            && !$this->_own_planet) {
            
            $this->showMessage(
                FormatLib::colorRed($this->getLang()['fl_deploy_only_your_planets'])
            );
        }
        
        if ($data['mission'] == Missions::stay) {
            
            $is_buddy = $this->Fleet_Model->getBuddies(
                $this->_planet['planet_user_id'], $this->_target_data['planet_user_id']
            ) >= 1;
            
            if ($this->_target_data['user_ally_id'] != $this->_user['user_ally_id'] && !$is_buddy) {
                
                $this->showMessage(
                    FormatLib::colorRed($this->getLang()['fl_stay_not_on_enemy'])
                );
            }
        }
        
        if ($data['mission'] == Missions::colonize) {
            
            if (!isset($fleet[Ships::ship_colony_ship])) {
                
                return false;
            }
            
            if ($this->_occupied_planet) {
                
                $this->showMessage(
                    FormatLib::colorRed($this->getLang()['fl_planet_populed'])
                );
            }
        }
        
        if ($data['mission'] == Missions::recycle) {
            
            if ($target['planet_debris_metal'] == 0 
                && $target['planet_debris_crystal'] == 0 
                && time() > ($target['planet_invisible_start_time'] + DEBRIS_LIFE_TIME)) {

                return false;
            }
        }

        if ($data['mission'] == Missions::destroy) {
            
            if ($this->_own_planet
                or $this->_occupied_planet
                or ($target['type'] != PlanetTypes::moon)
                or !isset($fleet[Ships::ship_deathstar])) {
                
                return false;
            }
        }
        
        if ($data['mission'] == Missions::expedition
            && !$this->_occupied_planet) {
            
            $expeditions = $this->_fleets->getExpeditionsCount();
            $max_expeditions = FleetsLib::getMaxExpeditions(
                $this->_research->getCurrentResearch()->getResearchAstrophysics()
            );
            
            if ($max_expeditions <= 0) {
                
                $this->showMessage(
                    FormatLib::colorRed($this->getLang()['fl_expedition_tech_required'])
                );
            }
            
            if ($max_expeditions <= $expeditions) {
                
                $this->showMessage(
                    FormatLib::colorRed($this->getLang()['fl_expedition_fleets_limit'])
                );    
            }
        } else {

            if ($data['mission'] != Missions::colonize
                && !$this->_occupied_planet) {
                
                return false;
            }
        }

        // add the fleet mission
        $this->_fleet_data['fleet_mission'] = $data['mission'];
        
        return true;
    }
    
    /**
     * Validate noob protection
     * 
     * @return boolean
     */
    private function validateNoobProtection()
    {
        // skip if it's our own planet or it's an empty planet
        if ($this->_own_planet
            or !$this->_occupied_planet) {

            return true;
        }

        if (!parent::$users->isInactive($this->_target_data)) {

            $noob = FunctionsLib::loadLibrary('NoobsProtectionLib');

            $points = $noob->returnPoints(
                $this->_user['user_id'],
                $this->_target_data['user_id']
            );

            $user_points = $points['user_points'];
            $target_points = $points['target_points'];
            
            $disallow_weak = [
                Missions::attack, Missions::acs, Missions::spy, Missions::destroy
            ];
            
            $disallow_strong = [
                Missions::attack, Missions::acs, Missions::stay, Missions::spy, Missions::destroy
            ];
            
            if ($noob->isWeak($user_points, $target_points)
                && in_array($this->_clean_input_data['mission'], $disallow_weak)) {
                
                $this->showMessage(
                    FormatLib::customColor($this->getLang()['fl_week_player'], 'lime')
                );
            }

            if ($noob->isStrong($user_points, $target_points)
                && in_array($this->_clean_input_data['mission'], $disallow_strong)) {
                
                $this->showMessage(
                    FormatLib::colorRed($this->getLang()['fl_strong_player'])
                );
            }
        }
        
        return true;
    }
    
    /**
     * Validate the amount of fleets
     * 
     * @return boolean
     */
    private function validateFleets()
    {
        $fleets = $this->_fleets->getFleetsCount();

        $max_fleets = FleetsLib::getMaxFleets(
            $this->_research->getCurrentResearch()->getResearchComputerTechnology(),
            $this->_premium->getCurrentPremium()->getPremiumOfficierAdmiral()
        );

        if ($max_fleets <= $fleets) {
            
            $this->showMessage(
                $this->getLang()['fl_no_slots']
            );
        }
        
        return true;
    }
    
    /**
     * Get fleet data
     * 
     * @return array
     */
    private function getFleetData()
    {
        return $_SESSION['fleet_data'];
    }
    
    /**
     * Get session set ships
     * 
     * @return string
     */
    private function getSessionShips()
    {
        return unserialize(base64_decode(str_rot13($this->getFleetData()['fleetarray'])));
    }
    
    /**
     * Get the target data
     * 
     * @return array
     */
    private function getTargetData()
    {
        return $_SESSION['fleet_data']['target'];
    }

    /**
     * Show message with some default fleet values
     * 
     * @param type $message
     * 
     * @return void
     */
    private function showMessage($message)
    {
        FunctionsLib::message(
            $message,
            self::REDIRECT_TARGET,
            3
        );
        exit();
    }
    
    /**
     * Send the fleet with the collected data
     */
    private function sendFleet()
    {
        // create the new fleet and
        // remove from the planet the ships and resources
        $this->Fleet_Model->insertNewFleet(
            $this->_fleet_data, $this->_planet
        );
        
        // go to movements view
        FunctionsLib::redirect(self::REDIRECT_TARGET);
    }
}

/* end of fleet4.php */
