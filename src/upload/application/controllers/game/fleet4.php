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
use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\research\Researches;
use const BUDDY;
use const DEBRIS_LIFE_TIME;
use const FLEETS;
use const MAX_GALAXY_IN_WORLD;
use const MAX_PLANET_IN_SYSTEM;
use const MAX_SYSTEM_IN_GALAXY;
use const PLANETS;

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
    private $_research = null;
    
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
        $this->_research = new Researches(
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
        
        // validate if any player is on vacations
        $this->validateVacations();
        
        die();
        
        $this->validateAcs();
        
        $this->validateTarget();
        
        $this->validateShips();
        
        $this->validateMission();
        
        $this->validateNoobProtection();
        
        
        $resource = parent::$objects->getObjects();
        $pricelist = parent::$objects->getPrice();
        
        $fleet_group = 0;

        if ($_POST['fleet_group'] > 0) {
            if ($_POST['mission'] == 2) {
                $target = 'g' . (int) $_POST['galaxy'] .
                    's' . (int) $_POST['system'] .
                    'p' . (int) $_POST['planet'] .
                    't' . (int) $_POST['planettype'];

                if ($_POST['acs_target'] == $target) {
                    $aks_count = $this->Fleet_Model->getAcsCount($_POST['fleet_group']);

                    if ($aks_count > 0) {
                        $fleet_group = $_POST['fleet_group'];
                    }
                }
            }
        }

        if (($_POST['fleet_group'] == 0) && ($_POST['mission'] == 2)) {
            $_POST['mission'] = 1;
        }

        $fleetarray = $this->getSessionShips();

        if (!is_array($fleetarray)) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        foreach ($fleetarray as $Ship => $Count) {
            $Count = intval($Count);

            if ($Count > $this->_planet[$resource[$Ship]]) {
                FunctionsLib::redirect(self::REDIRECT_TARGET);
            }
        }

        $galaxy = (int) $_POST['galaxy'];
        $system = (int) $_POST['system'];
        $planet = (int) $_POST['planet'];
        $planettype = (int) $_POST['planettype'];
        $fleetmission = (int) $_POST['mission'];

        //fix by jstar
        if ($fleetmission == 7 && !isset($fleetarray[208])) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        if ($planettype != 1 && $planettype != 2 && $planettype != 3) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        //fix invisible debris like ogame by jstar
        if ($fleetmission == 8) {
            $YourPlanet = false;
            $UsedPlanet = false;
            $select = $this->_db->queryFetch("SELECT COUNT(*) AS count, p.*
														FROM `" . PLANETS . "` AS p
														WHERE `planet_galaxy` = '" . $galaxy . "' AND
																`planet_system` = '" . $system . "' AND
																`planet_planet` = '" . $planet . "' AND
																`planet_type` = 1;");

            if ($select['planet_debris_metal'] == 0 && $select['planet_debris_crystal'] == 0 && time() > ($select['planet_invisible_start_time'] + DEBRIS_LIFE_TIME)) {
                FunctionsLib::redirect(self::REDIRECT_TARGET);
            }
        } else {
            $YourPlanet = false;
            $UsedPlanet = false;
            $select = $this->_db->queryFetch("SELECT COUNT(*) AS count, p.`planet_user_id`
														FROM `" . PLANETS . "` AS p
														WHERE `planet_galaxy` = '" . $galaxy . "' AND
																`planet_system` = '" . $system . "' AND
																`planet_planet` = '" . $planet . "' AND
																`planet_type` = '" . $planettype . "'");
        }

        if ($this->_planet['planet_galaxy'] == $galaxy && $this->_planet['planet_system'] == $system &&
            $this->_planet['planet_planet'] == $planet && $this->_planet['planet_type'] == $planettype) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        if ($_POST['mission'] != 15) {
            if ($select['count'] < 1 && $fleetmission != 7) {
                FunctionsLib::redirect(self::REDIRECT_TARGET);
            } elseif ($fleetmission == 9 && $select['count'] < 1) {
                FunctionsLib::redirect(self::REDIRECT_TARGET);
            }
        } else {
            $MaxExpedition = $this->_user[$resource[124]];

            if ($MaxExpedition >= 1) {
                $maxexpde = $this->_db->queryFetch("SELECT COUNT(fleet_owner) AS `expedi`
																	FROM " . FLEETS . "
																	WHERE `fleet_owner` = '" . $this->_user['user_id'] . "'
																		AND `fleet_mission` = '15';");
                $ExpeditionEnCours = $maxexpde['expedi'];
                $EnvoiMaxExpedition = FleetsLib::getMaxExpeditions($MaxExpedition);
            } else {
                $ExpeditionEnCours = 0;
                $EnvoiMaxExpedition = 0;
            }

            if ($EnvoiMaxExpedition == 0) {
                FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_expedition_tech_required'] . "</b></font>", "game.php?page=movement", 2);
            } elseif ($ExpeditionEnCours >= $EnvoiMaxExpedition) {
                FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_expedition_fleets_limit'] . "</b></font>", "game.php?page=movement", 2);
            }
        }

        if ($select['planet_user_id'] == $this->_user['user_id']) {
            $YourPlanet = true;
            $UsedPlanet = true;
        } elseif (!empty($select['planet_user_id'])) {
            $YourPlanet = false;
            $UsedPlanet = true;
        } else {
            $YourPlanet = false;
            $UsedPlanet = false;
        }

        //fix by jstar
        if ($fleetmission == 9) {
            $countfleettype = count($fleetarray);

            if ($YourPlanet or ! $UsedPlanet or $planettype != 3) {
                FunctionsLib::redirect(self::REDIRECT_TARGET);
            } elseif ($countfleettype == 1 && !( isset($fleetarray[214]) )) {
                FunctionsLib::redirect(self::REDIRECT_TARGET);
            } elseif ($countfleettype == 2 && !( isset($fleetarray[214]) )) {
                FunctionsLib::redirect(self::REDIRECT_TARGET);
            } elseif ($countfleettype > 2) {
                FunctionsLib::redirect(self::REDIRECT_TARGET);
            }
        }

        if (empty($fleetmission)) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        $user_points = $this->_noob->returnPoints($this->_user['user_id'], $this->_target_data['user_id']);
        $MyGameLevel = $user_points['user_points'];
        $HeGameLevel = $user_points['target_points'];

        if (parent::$users->isInactive($this->_target_data)) {
            if ($this->_noob->isWeak($MyGameLevel, $HeGameLevel) &&
                $this->_target_data['planet_user_id'] != '' &&
                ($_POST['mission'] == 1 or $_POST['mission'] == 6 or $_POST['mission'] == 9)) {
                FunctionsLib::message("<font color=\"lime\"><b>" . $this->_lang['fl_week_player'] . "</b></font>", "game.php?page=movement", 2);
            }

            if ($this->_noob->isStrong($MyGameLevel, $HeGameLevel) &&
                $this->_target_data['planet_user_id'] != '' &&
                ($_POST['mission'] == 1 or $_POST['mission'] == 5 or $_POST['mission'] == 6 or $_POST['mission'] == 9)) {
                FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_strong_player'] . "</b></font>", "game.php?page=movement", 2);
            }
        }

        $FlyingFleets = $this->_db->queryFetch("SELECT COUNT(fleet_id) as Number
													FROM " . FLEETS . "
													WHERE `fleet_owner`='" . $this->_user['user_id'] . "'");
        $ActualFleets = $FlyingFleets['Number'];

        if ((FleetsLib::getMaxFleets($this->_user[$resource[108]], $this->_user['premium_officier_admiral']) ) <= $ActualFleets) {
            FunctionsLib::message($this->_lang['fl_no_slots'], "game.php?page=movement", 1);
        }

        if ($_POST['resource1'] + $_POST['resource2'] + $_POST['resource3'] < 1 && $_POST['mission'] == 3) {
            FunctionsLib::message("<font color=\"lime\"><b>" . $this->_lang['fl_empty_transport'] . "</b></font>", "game.php?page=movement", 1);
        }

        if ($_POST['mission'] != 15) {
            if ($this->_target_data['planet_user_id'] == '' && $_POST['mission'] < 7) {
                FunctionsLib::redirect(self::REDIRECT_TARGET);
            }

            if ($this->_target_data['planet_user_id'] != '' && $_POST['mission'] == 7) {
                FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_planet_populed'] . "</b></font>", "game.php?page=movement", 2);
            }

            if ($this->_target_data['user_ally_id'] != $this->_user['user_ally_id'] && $_POST['mission'] == 4) {
                FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_stay_not_on_enemy'] . "</b></font>", "game.php?page=movement", 2);
            }

            if (($this->_target_data['planet_user_id'] == $this->_planet['planet_user_id']) && (($_POST['mission'] == 1) or ( $_POST['mission'] == 6))) {
                FunctionsLib::redirect(self::REDIRECT_TARGET);
            }

            if (($this->_target_data['planet_user_id'] != $this->_planet['planet_user_id']) && ($_POST['mission'] == 4)) {
                FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_deploy_only_your_planets'] . "</b></font>", "game.php?page=movement", 2);
            }

            if ($_POST['mission'] == 5) {
                $buddy = $this->_db->queryFetch("SELECT COUNT( * ) AS buddys
														FROM  `" . BUDDY . "`
															WHERE (
																(
																	buddy_sender ='" . intval($this->_planet['planet_user_id']) . "'
																	AND buddy_receiver ='" . intval($this->_target_data['planet_user_id']) . "'
																)
																OR (
																	buddy_sender ='" . intval($this->_target_data['planet_user_id']) . "'
																	AND buddy_receiver ='" . intval($this->_planet['planet_user_id']) . "'
																)
															)
															AND buddy_status =1");

                if ($this->_target_data['user_ally_id'] != $this->_user['user_ally_id'] && $buddy['buddys'] < 1) {
                    FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_stay_not_on_enemy'] . "</b></font>", "game.php?page=movement", 2);
                }
            }
        }

        $AllFleetSpeed = FleetsLib::fleetMaxSpeed($fleetarray, 0, $this->_user);
        $GenFleetSpeed = $this->getFleetData()['speed'];
        $SpeedFactor = FunctionsLib::fleetSpeedFactor();
        $MaxFleetSpeed = min($AllFleetSpeed);

        if (!$_POST['planettype']) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        if (!$_POST['galaxy'] || !is_numeric($_POST['galaxy']) || $_POST['galaxy'] > MAX_GALAXY_IN_WORLD || $_POST['galaxy'] < 1) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        if (!$_POST['system'] || !is_numeric($_POST['system']) || $_POST['system'] > MAX_SYSTEM_IN_GALAXY || $_POST['system'] < 1) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        if (!$_POST['planet'] || !is_numeric($_POST['planet']) || $_POST['planet'] > (MAX_PLANET_IN_SYSTEM + 1) || $_POST['planet'] < 1) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        if ($_POST['thisgalaxy'] != $this->_planet['planet_galaxy'] |
            $_POST['thissystem'] != $this->_planet['planet_system'] |
            $_POST['thisplanet'] != $this->_planet['planet_planet'] |
            $_POST['thisplanettype'] != $this->_planet['planet_type']) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        if (!isset($fleetarray)) {
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }

        $distance = FleetsLib::targetDistance($_POST['thisgalaxy'], $_POST['galaxy'], $_POST['thissystem'], $_POST['system'], $_POST['thisplanet'], $_POST['planet']);
        $duration = FleetsLib::missionDuration($GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor);
        $consumption = FleetsLib::fleetConsumption($fleetarray, $SpeedFactor, $duration, $distance, $this->_user);

        $fleet['start_time'] = $duration + time();

        // START CODE BY JSTAR
        if ($_POST['mission'] == 15) {
            $StayDuration = floor($_POST['expeditiontime']);

            if ($StayDuration > 0) {
                $StayDuration = $StayDuration * 3600;
                $StayTime = $fleet['start_time'] + $StayDuration;
            } else {
                FunctionsLib::redirect(self::REDIRECT_TARGET);
            }
        } // END CODE BY JSTAR
        elseif ($_POST['mission'] == 5) {
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

            if ($Ship == 210) {
                $haveSpyProbos = true;
            }

            $FleetStorage += $pricelist[$Ship]['capacity'] * $Count;
            $FleetShipCount += $Count;
            $fleet_array .= $Ship . "," . $Count . ";";
            $FleetSubQRY .= "`" . $resource[$Ship] . "` = `" . $resource[$Ship] . "` - " . $Count . ", ";
        }

        if (!$haveSpyProbos && $_POST['mission'] == 6) {
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
            FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_no_enought_deuterium'] . FormatLib::prettyNumber($consumption) . "</b></font>", "game.php?page=movement", 2);
        }

        if ($StorageNeeded > $FleetStorage) {
            FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_no_enought_cargo_capacity'] . FormatLib::prettyNumber($StorageNeeded - $FleetStorage) . "</b></font>", "game.php?page=movement", 2);
        }

        if (FunctionsLib::readConfig('adm_attack') != 0 && $this->_target_data['user_authlevel'] >= 1 && $this->_user['user_authlevel'] == 0) {
            FunctionsLib::message($this->_lang['fl_admins_cannot_be_attacked'], "game.php?page=movement", 2);
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

        // final step, send and redirect
        $this->sendFleet();
        
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
            'fleet_mission' => [
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

            // prepare coords
            $this->_fleet_data['fleet_start_galaxy'] = $this->_planet['planet_galaxy'];
            $this->_fleet_data['fleet_start_system'] = $this->_planet['planet_system'];
            $this->_fleet_data['fleet_start_planet'] = $this->_planet['planet_planet'];
            $this->_fleet_data['fleet_start_type'] = $this->_planet['planet_type'];
            $this->_fleet_data['fleet_end_galaxy'] = $target_data['galaxy'];
            $this->_fleet_data['fleet_end_system'] = $target_data['system'];
            $this->_fleet_data['fleet_end_planet'] = $target_data['planet'];
            $this->_fleet_data['fleet_end_type'] = $target_data['type'];

            // set target data
            $this->_target_data = $target;
        } else {
            
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }
    }
    
    /**
     * Validate vacations for both players
     * 
     * @return void
     */
    private function validateVacations()
    {
        if (parent::$users->isOnVacations($this->_user)) {

            $this->showMessage($this->getLang()['fl_vacation_mode_active']);
        }

        if (parent::$users->isOnVacations($this->_target_data)
            && $this->_clean_input_data['fleet_mission'] != Missions::recycle) {
            
            $this->showMessage($this->getLang()['fl_in_vacation_player']);
        }
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
        return unserialize(base64_decode(str_rot13($_SESSION['fleet_data']['fleetarray'])));
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
