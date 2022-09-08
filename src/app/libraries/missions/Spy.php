<?php

/**
 * Spy Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace App\libraries\missions;

use App\core\enumerators\MissionsEnumerator;
use App\helpers\StringsHelper;
use App\libraries\FleetsLib;
use App\libraries\FormatLib;
use App\libraries\Functions;
use App\libraries\missions\Missions;
use App\libraries\OfficiersLib;

/**
 * Spy Class
 */
class Spy extends Missions
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['game/global', 'game/missions', 'game/spy', 'game/constructions', 'game/defenses', 'game/ships', 'game/technologies']);
    }

    /**
     * spyMission
     *
     * @param string $string String
     *
     * @return void
     */
    public function spyMission($fleet_row)
    {
        // do mission
        if (parent::canStartMission($fleet_row)) {
            parent::makeUpdate($fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet'], $fleet_row['fleet_end_type']);

            $current_data = $this->missionsModel->getSpyUserDataByCords([
                'coords' => [
                    'galaxy' => $fleet_row['fleet_start_galaxy'],
                    'system' => $fleet_row['fleet_start_system'],
                    'planet' => $fleet_row['fleet_start_planet'],
                    'type' => $fleet_row['fleet_start_type'],
                ],
            ]);

            $target_data = $this->missionsModel->getInquiredUserDataByCords([
                'coords' => [
                    'galaxy' => $fleet_row['fleet_end_galaxy'],
                    'system' => $fleet_row['fleet_end_system'],
                    'planet' => $fleet_row['fleet_end_planet'],
                    'type' => $fleet_row['fleet_end_type'],
                ],
            ]);

            $CurrentSpyLvl = OfficiersLib::getMaxEspionage($current_data['research_espionage_technology'], $current_data['premium_officier_technocrat']);
            $TargetSpyLvl = OfficiersLib::getMaxEspionage($target_data['research_espionage_technology'], $target_data['premium_officier_technocrat']);
            $fleet = FleetsLib::getFleetShipsArray($fleet_row['fleet_array']);

            foreach ($fleet as $id => $amount) {
                if ($id == "210") {
                    $LS = $amount;
                    $SpyToolDebris = $LS * 300;

                    $MaterialsInfo = $this->generateSpyReport($target_data, 0, $this->langs->line('spy_report_resources'));
                    $Materials = $MaterialsInfo['String'];

                    $PlanetFleetInfo = $this->generateSpyReport($target_data, 1, $this->langs->line('spy_report_fleet'));
                    $PlanetFleet = $Materials;
                    $PlanetFleet .= $PlanetFleetInfo['String'];

                    $PlanetDefenInfo = $this->generateSpyReport($target_data, 2, $this->langs->line('spy_report_defenses'));
                    $PlanetDefense = $PlanetFleet;
                    $PlanetDefense .= $PlanetDefenInfo['String'];

                    $PlanetBuildInfo = $this->generateSpyReport($target_data, 3, $this->langs->line('spy_report_buildings'));
                    $PlanetBuildings = $PlanetDefense;
                    $PlanetBuildings .= $PlanetBuildInfo['String'];

                    $TargetTechnInfo = $this->generateSpyReport($target_data, 4, $this->langs->line('spy_report_research'));
                    $TargetTechnos = $PlanetBuildings;
                    $TargetTechnos .= $TargetTechnInfo['String'];

                    $TargetForce = ($PlanetFleetInfo['Count'] * $LS) / 4;

                    if ($TargetForce > 100) {
                        $TargetForce = 100;
                    }

                    $TargetChances = mt_rand(0, intval($TargetForce));
                    $SpyerChances = mt_rand(0, 100);

                    if ($TargetChances >= $SpyerChances) {
                        Functions::sendMessage(
                            $fleet_row['fleet_owner'],
                            '',
                            $fleet_row['fleet_start_time'],
                            0,
                            $this->langs->line('mi_fleet_command'),
                            sprintf($this->langs->line('spy_result_destroyed_title'), FormatLib::prettyCoords($fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet'])),
                            $this->langs->line('spy_result_destroyed'),
                            true
                        );
                    }

                    $AttackLink = "<center>";
                    $AttackLink .= "<a href=\"game.php?page=fleet1&galaxy=" . $fleet_row['fleet_end_galaxy'] . "&system=" . $fleet_row['fleet_end_system'] . "";
                    $AttackLink .= "&planet=" . $fleet_row['fleet_end_planet'] . "&planettype=" . $fleet_row['fleet_end_type'] . "";
                    $AttackLink .= "&target_mission=1";
                    $AttackLink .= " \">" . $this->langs->language['type_mission'][MissionsEnumerator::ATTACK] . "";
                    $AttackLink .= "</a></center>";
                    $MessageEnd = "<center>" . sprintf($this->langs->line('spy_report_detection'), $TargetChances) . "</center>";

                    $spionage_difference = abs($CurrentSpyLvl - $TargetSpyLvl);

                    $CurrentSpyLvl = 100;
                    $TargetSpyLvl = 0;
                    if ($TargetSpyLvl >= $CurrentSpyLvl) {
                        $ST = pow($spionage_difference, 2);
                        $resources = 1;
                        $fleet = $ST + 2;
                        $defense = $ST + 3;
                        $buildings = $ST + 5;
                        $tech = $ST + 7;
                    }

                    if ($CurrentSpyLvl > $TargetSpyLvl) {
                        $ST = pow($spionage_difference, 2) * -1;
                        $resources = 1;
                        $fleet = $ST + 2;
                        $defense = $ST + 3;
                        $buildings = $ST + 5;
                        $tech = $ST + 7;
                    }

                    if ($resources <= $LS) {
                        $SpyMessage = $Materials . "<br />" . $AttackLink . $MessageEnd;
                    }

                    if ($fleet <= $LS) {
                        $SpyMessage = $PlanetFleet . "<br />" . $AttackLink . $MessageEnd;
                    }

                    if ($defense <= $LS) {
                        $SpyMessage = $PlanetDefense . "<br />" . $AttackLink . $MessageEnd;
                    }

                    if ($buildings <= $LS) {
                        $SpyMessage = $PlanetBuildings . "<br />" . $AttackLink . $MessageEnd;
                    }

                    if ($tech <= $LS) {
                        $SpyMessage = $TargetTechnos . "<br />" . $AttackLink . $MessageEnd;
                    }

                    Functions::sendMessage(
                        $fleet_row['fleet_owner'],
                        '',
                        $fleet_row['fleet_start_time'],
                        0,
                        $this->langs->line('mi_fleet_command'),
                        sprintf($this->langs->line('spy_report_title'), $target_data['planet_name'], FormatLib::prettyCoords($target_data['planet_galaxy'], $target_data['planet_system'], $target_data['planet_planet'])),
                        $SpyMessage,
                        true
                    );

                    $this->sendReportToTarget($fleet_row, $current_data, $target_data, $TargetChances);

                    if ($TargetChances >= $SpyerChances) {
                        $this->missionsModel->updateCrystalDebrisByPlanetId([
                            'time' => time(),
                            'crystal' => (0 + $SpyToolDebris),
                            'planet_id' => $target_data['planet_id'],
                        ]);

                        parent::removeFleet($fleet_row['fleet_id']);
                    } else {
                        parent::returnFleet($fleet_row['fleet_id']);
                    }
                }
            }
        } elseif ($fleet_row['fleet_mess'] == 1 && $fleet_row['fleet_end_time'] <= time()) {
            parent::restoreFleet($fleet_row, true);
            parent::removeFleet($fleet_row['fleet_id']);
        }
    }

    /**
     * Spy the target
     *
     * @param array $target_data
     * @param int $mode
     * @param string $report_title
     * @return void
     */
    private function generateSpyReport($target_data, $mode, $report_title)
    {
        $LookAtLoop = true;
        $Count = 0;

        switch ($mode) {
            case 0:
                $String = "<table width=\"440\"><tr><td class=\"c\" colspan=\"5\">";
                $String .= $report_title;
                $String .= "</td>";
                $String .= "</tr><tr>";
                $String .= "<td width=220>" . $this->langs->line('metal') . "</td><td width=220 align=right>" . FormatLib::prettyNumber($target_data['planet_metal']) . "</td><td>&nbsp;</td>";
                $String .= "<td width=220>" . $this->langs->line('crystal') . "</td></td><td width=220 align=right>" . FormatLib::prettyNumber($target_data['planet_crystal']) . "</td>";
                $String .= "</tr><tr>";
                $String .= "<td width=220>" . $this->langs->line('deuterium') . "</td><td width=220 align=right>" . FormatLib::prettyNumber($target_data['planet_deuterium']) . "</td><td>&nbsp;</td>";
                $String .= "<td width=220>" . $this->langs->line('energy') . "</td><td width=220 align=right>" . FormatLib::prettyNumber($target_data['planet_energy_max']) . "</td>";
                $String .= "</tr>";

                $LookAtLoop = false;

                break;

            case 1:
                $ResFrom[0] = 200;
                $ResTo[0] = 299;
                $Loops = 1;

                break;

            case 2:
                $ResFrom[0] = 400;
                $ResTo[0] = 499;
                $ResFrom[1] = 500;
                $ResTo[1] = 599;
                $Loops = 2;

                break;

            case 3:
                $ResFrom[0] = 1;
                $ResTo[0] = 99;
                $Loops = 1;

                break;

            case 4:
                $ResFrom[0] = 100;
                $ResTo[0] = 199;
                $Loops = 1;

                break;
        }

        if ($LookAtLoop == true) {
            $String = "<table width=\"440\" cellspacing=\"1\"><tr><td class=\"c\" colspan=\"" . ((2 * 2) + (2 - 1)) . "\">" . $report_title . "</td></tr>";
            $Count = 0;
            $CurrentLook = 0;

            while ($CurrentLook < $Loops) {
                $row = 0;
                for ($Item = $ResFrom[$CurrentLook]; $Item <= $ResTo[$CurrentLook]; $Item++) {
                    if (isset($this->resource[$Item]) && $target_data[$this->resource[$Item]] > 0) {
                        if ($row == 0) {
                            $String .= "<tr>";
                        }

                        $String .= "<td align=left>" . $this->langs->language[$this->resource[$Item]] . "</td><td align=right>" . FormatLib::prettyNumber($target_data[$this->resource[$Item]]) . "</td>";

                        if ($row < 2 - 1) {
                            $String .= "<td>&nbsp;</td>";
                        }

                        $Count += $target_data[$this->resource[$Item]];
                        $row++;

                        if ($row == 2) {
                            $String .= "</tr>";
                            $row = 0;
                        }
                    }
                }

                while ($row != 0) {
                    $String .= "<td>&nbsp;</td><td>&nbsp;</td>";
                    $row++;

                    if ($row == 2) {
                        $String .= "</tr>";
                        $row = 0;
                    }
                }
                $CurrentLook++;
            }
        }

        $String .= "</table>";

        $return['String'] = $String;
        $return['Count'] = $Count;

        return $return;
    }

    /**
     * Send a report to the target informing that their planet is being spy
     *
     * @param array $fleet
     * @param array $user
     * @param array $target
     * @param integer $chances
     * @return void
     */
    private function sendReportToTarget(array $fleet, array $user, array $target, int $chances): void
    {
        Functions::sendMessage(
            $target['planet_user_id'],
            '',
            $fleet['fleet_start_time'],
            0,
            $this->langs->line('spy_activity_from'),
            StringsHelper::parseReplacements(
                $this->langs->line('spy_activity_title'),
                [
                    $target['planet_name'],
                    FormatLib::prettyCoords($target['planet_galaxy'], $target['planet_system'], $target['planet_planet']),
                ]
            ),
            StringsHelper::parseReplacements(
                $this->langs->line('spy_activity_enemy_seen'),
                [
                    $user['planet_name'],
                    FormatLib::prettyCoords($user['planet_galaxy'], $user['planet_system'], $user['planet_planet']),
                    $user['user_name'],
                    $target['planet_name'],
                    FormatLib::prettyCoords($target['planet_galaxy'], $target['planet_system'], $target['planet_planet']),
                    $chances,
                ]
            ),
            true
        );
    }
}
