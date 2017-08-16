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

use application\core\XGPCore;
use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Fleet4 Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Fleet4 extends XGPCore
{

    const MODULE_ID = 8;

    private $_lang;
    private $_noob;
    private $_current_user;
    private $_current_planet;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        $this->_lang = parent::$lang;
        $this->_current_user = parent::$users->getUserData();
        $this->_current_planet = parent::$users->getPlanetData();
        $this->_noob = FunctionsLib::loadLibrary('NoobsProtectionLib');

        $this->build_page();
    }

    /**
     * method __destruct
     * param
     * return close db connection
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $resource = parent::$objects->getObjects();
        $pricelist = parent::$objects->getPrice();
        $reslist = parent::$objects->getObjectsList();
        $parse = $this->_lang;

        if (parent::$users->isOnVacations($this->_current_user)) {
            exit(FunctionsLib::message($this->_lang['fl_vacation_mode_active'], "game.php?page=overview", 2));
        }

        $fleet_group_mr = 0;

        if ($_POST['fleet_group'] > 0) {
            if ($_POST['mission'] == 2) {
                $target = 'g' . (int) $_POST['galaxy'] .
                        's' . (int) $_POST['system'] .
                        'p' . (int) $_POST['planet'] .
                        't' . (int) $_POST['planettype'];

                if ($_POST['acs_target_mr'] == $target) {
                    $aks_count_mr = parent::$db->query("SELECT COUNT(`acs_fleet_id`)
															FROM `" . ACS_FLEETS . "`
															WHERE `acs_fleet_id` = '" . (int) $_POST['fleet_group'] . "'");

                    if ($aks_count_mr > 0) {
                        $fleet_group_mr = $_POST['fleet_group'];
                    }
                }
            }
        }

        if (($_POST['fleet_group'] == 0) && ($_POST['mission'] == 2)) {
            $_POST['mission'] = 1;
        }

        $TargetPlanet = parent::$db->queryFetch(
            "SELECT `planet_user_id`,`planet_destroyed`
            FROM `" . PLANETS . "`
             WHERE `planet_galaxy` = '" . (int) $_POST['galaxy'] . "' AND
                            `planet_system` = '" . (int) $_POST['system'] . "' AND
                            `planet_planet` = '" . (int) $_POST['planet'] . "' AND
                            `planet_type` = '" . (int) $_POST['planettype'] . "';"
        );

        $MyDBRec = parent::$db->queryFetch(
            "SELECT u.`user_id`, u.`user_onlinetime`, u.`user_ally_id`, s.`setting_vacations_status`
            FROM " . USERS . " AS u, " . SETTINGS . " AS s
            WHERE u.`user_id` = '" . $this->_current_user['user_id'] . "'
                    AND s.`setting_user_id` = '" . $this->_current_user['user_id'] . "';"
        );

        $fleetarray = unserialize(base64_decode(str_rot13($_POST['usedfleet'])));

        if ($TargetPlanet['planet_destroyed'] != 0) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        if (!is_array($fleetarray)) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        foreach ($fleetarray as $Ship => $Count) {
            $Count = intval($Count);

            if ($Count > $this->_current_planet[$resource[$Ship]]) {
                FunctionsLib::redirect('game.php?page=movement');
            }
        }

        $error = 0;
        $galaxy = (int) $_POST['galaxy'];
        $system = (int) $_POST['system'];
        $planet = (int) $_POST['planet'];
        $planettype = (int) $_POST['planettype'];
        $fleetmission = (int) $_POST['mission'];

        //fix by jstar
        if ($fleetmission == 7 && !isset($fleetarray[208])) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        if ($planettype != 1 && $planettype != 2 && $planettype != 3) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        //fix invisible debris like ogame by jstar
        if ($fleetmission == 8) {
            $YourPlanet = false;
            $UsedPlanet = false;
            $select = parent::$db->queryFetch("SELECT COUNT(*) AS count, p.*
														FROM `" . PLANETS . "` AS p
														WHERE `planet_galaxy` = '" . $galaxy . "' AND
																`planet_system` = '" . $system . "' AND
																`planet_planet` = '" . $planet . "' AND
																`planet_type` = 1;");

            if ($select['planet_debris_metal'] == 0 && $select['planet_debris_crystal'] == 0 && time() > ($select['planet_invisible_start_time'] + DEBRIS_LIFE_TIME)) {
                FunctionsLib::redirect('game.php?page=movement');
            }
        } else {
            $YourPlanet = false;
            $UsedPlanet = false;
            $select = parent::$db->queryFetch("SELECT COUNT(*) AS count, p.`planet_user_id`
														FROM `" . PLANETS . "` AS p
														WHERE `planet_galaxy` = '" . $galaxy . "' AND
																`planet_system` = '" . $system . "' AND
																`planet_planet` = '" . $planet . "' AND
																`planet_type` = '" . $planettype . "'");
        }

        if ($this->_current_planet['planet_galaxy'] == $galaxy && $this->_current_planet['planet_system'] == $system &&
                $this->_current_planet['planet_planet'] == $planet && $this->_current_planet['planet_type'] == $planettype) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        if ($_POST['mission'] != 15) {
            if ($select['count'] < 1 && $fleetmission != 7) {
                FunctionsLib::redirect('game.php?page=movement');
            } elseif ($fleetmission == 9 && $select['count'] < 1) {
                FunctionsLib::redirect('game.php?page=movement');
            }
        } else {
            $MaxExpedition = $this->_current_user[$resource[124]];

            if ($MaxExpedition >= 1) {
                $maxexpde = parent::$db->queryFetch("SELECT COUNT(fleet_owner) AS `expedi`
																	FROM " . FLEETS . "
																	WHERE `fleet_owner` = '" . $this->_current_user['user_id'] . "'
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

        if ($select['planet_user_id'] == $this->_current_user['user_id']) {
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
                FunctionsLib::redirect('game.php?page=movement');
            } elseif ($countfleettype == 1 && !( isset($fleetarray[214]) )) {
                FunctionsLib::redirect('game.php?page=movement');
            } elseif ($countfleettype == 2 && !( isset($fleetarray[214]) )) {
                FunctionsLib::redirect('game.php?page=movement');
            } elseif ($countfleettype > 2) {
                FunctionsLib::redirect('game.php?page=movement');
            }
        }

        if (empty($fleetmission)) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        if ($TargetPlanet['planet_user_id'] == '') {
            $HeDBRec = $MyDBRec;
        } elseif ($TargetPlanet['planet_user_id'] != '') {
            $HeDBRec = parent::$db->queryFetch(
                "SELECT u.`user_id`, u.`user_authlevel`, u.`user_onlinetime`, u.`user_ally_id`, s.`setting_vacations_status`
                FROM " . USERS . " AS u, " . SETTINGS . " AS s
                WHERE u.`user_id` = '" . $TargetPlanet['planet_user_id'] . "'
                        AND s.`setting_user_id` ='" . $TargetPlanet['planet_user_id'] . "';"
            );
        }

        $user_points = $this->_noob->returnPoints($MyDBRec['user_id'], $HeDBRec['user_id']);
        $MyGameLevel = $user_points['user_points'];
        $HeGameLevel = $user_points['target_points'];

        if ($HeDBRec['user_onlinetime'] >= (time() - 60 * 60 * 24 * 7)) {
            if ($this->_noob->isWeak($MyGameLevel, $HeGameLevel) &&
                    $TargetPlanet['planet_user_id'] != '' &&
                    ($_POST['mission'] == 1 or $_POST['mission'] == 6 or $_POST['mission'] == 9)) {
                FunctionsLib::message("<font color=\"lime\"><b>" . $this->_lang['fl_week_player'] . "</b></font>", "game.php?page=movement", 2);
            }

            if ($this->_noob->isStrong($MyGameLevel, $HeGameLevel) &&
                    $TargetPlanet['planet_user_id'] != '' &&
                    ($_POST['mission'] == 1 or $_POST['mission'] == 5 or $_POST['mission'] == 6 or $_POST['mission'] == 9)) {
                FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_strong_player'] . "</b></font>", "game.php?page=movement", 2);
            }
        }

        if ($HeDBRec['setting_vacations_status'] && $_POST['mission'] != 8) {
            FunctionsLib::message("<font color=\"lime\"><b>" . $this->_lang['fl_in_vacation_player'] . "</b></font>", "game.php?page=movement", 2);
        }

        $FlyingFleets = parent::$db->queryFetch("SELECT COUNT(fleet_id) as Number
													FROM " . FLEETS . "
													WHERE `fleet_owner`='" . $this->_current_user['user_id'] . "'");
        $ActualFleets = $FlyingFleets['Number'];

        if ((FleetsLib::getMaxFleets($this->_current_user[$resource[108]], $this->_current_user['premium_officier_admiral']) ) <= $ActualFleets) {
            FunctionsLib::message($this->_lang['fl_no_slots'], "game.php?page=movement", 1);
        }

        if ($_POST['resource1'] + $_POST['resource2'] + $_POST['resource3'] < 1 && $_POST['mission'] == 3) {
            FunctionsLib::message("<font color=\"lime\"><b>" . $this->_lang['fl_empty_transport'] . "</b></font>", "game.php?page=movement", 1);
        }

        if ($_POST['mission'] != 15) {
            if ($TargetPlanet['planet_user_id'] == '' && $_POST['mission'] < 7) {
                FunctionsLib::redirect('game.php?page=movement');
            }

            if ($TargetPlanet['planet_user_id'] != '' && $_POST['mission'] == 7) {
                FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_planet_populed'] . "</b></font>", "game.php?page=movement", 2);
            }

            if ($HeDBRec['user_ally_id'] != $MyDBRec['user_ally_id'] && $_POST['mission'] == 4) {
                FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_stay_not_on_enemy'] . "</b></font>", "game.php?page=movement", 2);
            }

            if (($TargetPlanet['planet_user_id'] == $this->_current_planet['planet_user_id']) && (($_POST['mission'] == 1) or ( $_POST['mission'] == 6))) {
                FunctionsLib::redirect('game.php?page=movement');
            }

            if (($TargetPlanet['planet_user_id'] != $this->_current_planet['planet_user_id']) && ($_POST['mission'] == 4)) {
                FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_deploy_only_your_planets'] . "</b></font>", "game.php?page=movement", 2);
            }

            if ($_POST['mission'] == 5) {
                $buddy = parent::$db->queryFetch("SELECT COUNT( * ) AS buddys
														FROM  `" . BUDDY . "`
															WHERE (
																(
																	buddy_sender ='" . intval($this->_current_planet['planet_user_id']) . "'
																	AND buddy_receiver ='" . intval($TargetPlanet['planet_user_id']) . "'
																)
																OR (
																	buddy_sender ='" . intval($TargetPlanet['planet_user_id']) . "'
																	AND buddy_receiver ='" . intval($this->_current_planet['planet_user_id']) . "'
																)
															)
															AND buddy_status =1");

                if ($HeDBRec['user_ally_id'] != $MyDBRec['user_ally_id'] && $buddy['buddys'] < 1) {
                    FunctionsLib::message("<font color=\"red\"><b>" . $this->_lang['fl_stay_not_on_enemy'] . "</b></font>", "game.php?page=movement", 2);
                }
            }
        }

        $missiontype = FleetsLib::getMissions();
        $speed_possible = array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1);
        $AllFleetSpeed = FleetsLib::fleetMaxSpeed($fleetarray, 0, $this->_current_user);
        $GenFleetSpeed = $_POST['speed'];
        $SpeedFactor = FunctionsLib::fleetSpeedFactor();
        $MaxFleetSpeed = min($AllFleetSpeed);

        if (!in_array($GenFleetSpeed, $speed_possible)) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        if ($MaxFleetSpeed != $_POST['speedallsmin']) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        if (!$_POST['planettype']) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        if (!$_POST['galaxy'] || !is_numeric($_POST['galaxy']) || $_POST['galaxy'] > MAX_GALAXY_IN_WORLD || $_POST['galaxy'] < 1) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        if (!$_POST['system'] || !is_numeric($_POST['system']) || $_POST['system'] > MAX_SYSTEM_IN_GALAXY || $_POST['system'] < 1) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        if (!$_POST['planet'] || !is_numeric($_POST['planet']) || $_POST['planet'] > (MAX_PLANET_IN_SYSTEM + 1) || $_POST['planet'] < 1) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        if ($_POST['thisgalaxy'] != $this->_current_planet['planet_galaxy'] |
                $_POST['thissystem'] != $this->_current_planet['planet_system'] |
                $_POST['thisplanet'] != $this->_current_planet['planet_planet'] |
                $_POST['thisplanettype'] != $this->_current_planet['planet_type']) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        if (!isset($fleetarray)) {
            FunctionsLib::redirect('game.php?page=movement');
        }

        $distance = FleetsLib::targetDistance($_POST['thisgalaxy'], $_POST['galaxy'], $_POST['thissystem'], $_POST['system'], $_POST['thisplanet'], $_POST['planet']);
        $duration = FleetsLib::missionDuration($GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor);
        $consumption = FleetsLib::fleetConsumption($fleetarray, $SpeedFactor, $duration, $distance, $this->_current_user);

        $fleet['start_time'] = $duration + time();

        // START CODE BY JSTAR
        if ($_POST['mission'] == 15) {
            $StayDuration = floor($_POST['expeditiontime']);

            if ($StayDuration > 0) {
                $StayDuration = $StayDuration * 3600;
                $StayTime = $fleet['start_time'] + $StayDuration;
            } else {
                FunctionsLib::redirect('game.php?page=movement');
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
            FunctionsLib::redirect('game.php?page=movement');
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

        $StockMetal = $this->_current_planet['planet_metal'];
        $StockCrystal = $this->_current_planet['planet_crystal'];
        $StockDeuterium = $this->_current_planet['planet_deuterium'];
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

        if (FunctionsLib::readConfig('adm_attack') != 0 && $HeDBRec['user_authlevel'] >= 1 && $this->_current_user['user_authlevel'] == 0) {
            FunctionsLib::message($this->_lang['fl_admins_cannot_be_attacked'], "game.php?page=movement", 2);
        }

        if ($fleet_group_mr != 0) {
            $AksStartTime = parent::$db->queryFetch("SELECT MAX(`fleet_start_time`) AS Start
														FROM " . FLEETS . "
														WHERE `fleet_group` = '" . $fleet_group_mr . "';");

            if ($AksStartTime['Start'] >= $fleet['start_time']) {
                $fleet['end_time'] += $AksStartTime['Start'] - $fleet['start_time'];
                $fleet['start_time'] = $AksStartTime['Start'];
            } else {
                parent::$db->query("UPDATE " . FLEETS . " SET
										`fleet_start_time` = '" . $fleet['start_time'] . "',
										`fleet_end_time` = fleet_end_time + '" . ($fleet['start_time'] - $AksStartTime['Start']) . "'
										WHERE `fleet_group` = '" . $fleet_group_mr . "';");

                $fleet['end_time'] += $fleet['start_time'] - $AksStartTime['Start'];
            }
        }

        parent::$db->query("INSERT INTO " . FLEETS . " SET
							`fleet_owner` = '" . $this->_current_user['user_id'] . "',
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
							`fleet_target_owner` = '" . (int) $TargetPlanet['planet_user_id'] . "',
							`fleet_group` = '" . (int) $fleet_group_mr . "',
							`fleet_creation` = '" . time() . "';");

        parent::$db->query("UPDATE `" . PLANETS . "` AS p
								INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id` SET
								$FleetSubQRY
								`planet_metal` = `planet_metal` - " . $TransMetal . ",
								`planet_crystal` = `planet_crystal` - " . $TransCrystal . ",
								`planet_deuterium` = `planet_deuterium` - " . ($TransDeuterium + $consumption) . "
								WHERE `planet_id` = " . $this->_current_planet['planet_id'] . ";");

        FunctionsLib::redirect('game.php?page=movement');
    }
}

/* end of fleet4.php */
