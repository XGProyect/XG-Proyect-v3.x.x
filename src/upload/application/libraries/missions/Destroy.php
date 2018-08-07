<?php
/**
 * Destroy Library
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
namespace application\libraries\missions;

use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Destroy Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Destroy extends Missions
{

    const SHIP_MIN_ID = 202;
    const SHIP_MAX_ID = 215;
    const DEFENSE_MIN_ID = 401;
    const DEFENSE_MAX_ID = 408;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * method destroy_mission
     * param $fleet_row
     * return the transport result
     */
    public function destroyMission($fleet_row)
    {
        if ($fleet_row['fleet_mess'] == 0 && $fleet_row['fleet_start_time'] <= time()) {

            $current_data = $this->Missions_Model->getDestroyerData([
                'coords' => [
                    'galaxy' => $fleet_row['fleet_start_galaxy'],
                    'system' => $fleet_row['fleet_start_system'],
                    'planet' => $fleet_row['fleet_start_planet'],
                    'type' => $fleet_row['fleet_start_type']
                ]
            ]);

            $target_data = $this->Missions_Model->getTargetToDestroyData([
                'coords' => [
                    'galaxy' => $fleet_row['fleet_end_galaxy'],
                    'system' => $fleet_row['fleet_end_system'],
                    'planet' => $fleet_row['fleet_end_planet'],
                    'type' => $fleet_row['fleet_end_type']
                ]
            ]);

            $target_ships = [];
            $current_ships = [];

            for ($i = self::DEFENSE_MIN_ID; $i <= self::DEFENSE_MAX_ID; $i++) {

                if (isset($this->resource[$i]) && isset($target_planet[$this->resource[$i]])) {

                    if ($target_planet[$this->resource[$i]] != 0) {

                        $target_ships[$SetItem]['count'] = $target_data[$this->resource[$SetItem]];
                    }
                }
            }

            for ($i = self::SHIP_MIN_ID; $i <= self::SHIP_MAX_ID; $i++) {

                if (isset($this->resource[$i]) && isset($target_planet[$this->resource[$i]])) {

                    if ($target_planet[$this->resource[$i]] != 0) {

                        $target_ships[$SetItem]['count'] = $target_data[$this->resource[$SetItem]];
                    }
                }
            }

            $TheFleet = FleetsLib::getFleetShipsArray($fleet_row['fleet_array']);

            foreach ($TheFleet as $id => $count) {

                $current_ships[$id]['count'] = $count;
            }

            $attack = $this->attack($current_ships, $target_ships, $current_data, $target_data);
            $current_ships = $attack['attacker'];
            $target_ships = $attack['enemy'];
            $FleetResult = $attack['win'];
            $dane_do_rw = $attack['data_for_rw'];
            $zlom = $attack['debris'];
            $FleetArray = '';
            $FleetAmount = 0;
            $FleetStorage = 0;

            foreach ($current_ships as $Ship => $Count) {
                $FleetStorage += $this->pricelist[$Ship]['capacity'] * $Count['count'];
                $FleetArray .= $Ship . "," . $Count['count'] . ";";
                $FleetAmount += $Count['count'];
            }

            $TargetPlanetUpd = "";

            if (!is_null($target_ships)) {
                foreach ($target_ships as $Ship => $Count) {
                    $TargetPlanetUpd .= "`" . $this->resource[$Ship] . "` = '" . $Count['count'] . "', ";
                }
            }

            $probarip = '';

            if ($FleetResult == "a") {

                $destructionl1 = 100 - sqrt($target_data['planet_diameter']);
                $destructionl21 = $destructionl1 * sqrt($current_ships['214']['count']);
                $destructionl2 = $destructionl21 / 1;

                if ($destructionl2 > 100) {
                    $chance = '100';
                } else {
                    $chance = round($destructionl2);
                }

                $tirage = mt_rand(0, 100);
                $probalune = sprintf($this->langs['sys_destruc_lune'], $chance);

                if ($tirage <= $chance) {

                    $resultat = '1';
                    $finmess = $this->langs['sys_destruc_reussi'];

                    $this->Missions_Model->updateFleetsStatusToMakeThemReturn([
                        'coords' => [
                            'galaxy' => $fleet_row['fleet_end_galaxy'],
                            'system' => $fleet_row['fleet_end_system'],
                            'planet' => $fleet_row['fleet_end_planet']
                        ],
                        'time' => time(),
                        'planet_id' => $target_data['planet_id']
                    ]);

                    if ($target_data['user_current_planet'] == $target_data['planet_id']) {

                        $this->Missions_Model->updateUserCurrentPlanetByCoordsAndUserId([
                            'coords' => [
                                'galaxy' => $fleet_row['fleet_end_galaxy'],
                                'system' => $fleet_row['fleet_end_system'],
                                'planet' => $fleet_row['fleet_end_planet']
                            ],
                            'planet_user_id' => $target_data['planet_user_id']
                        ]);
                    }
                } else {
                    $resultat = '0';
                }

                $destructionrip = sqrt($target_data['planet_diameter']) / 2;
                $chance2 = round($destructionrip);

                if ($resultat == 0) {
                    $tirage2 = mt_rand(0, 100);
                    $probarip = sprintf($this->langs['sys_destruc_rip'], $chance2);

                    if ($tirage2 <= $chance2) {
                        $resultat2 = ' detruite 1';
                        $finmess = $this->langs['sys_destruc_echec'];

                        parent::removeFleet($fleet_row['fleet_id']);
                    } else {
                        $resultat2 = 'sauvees 0';
                        $finmess = $this->langs['sys_destruc_null'];
                    }
                }
            }

            $introdestruc = sprintf($this->langs['sys_destruc_mess'], $current_data['planet_name'], $fleet_row['fleet_start_galaxy'], $fleet_row['fleet_start_system'], $fleet_row['fleet_start_planet'], $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']);

            $this->Missions_Model->updatePlanetDataAfterDestruction([
                'data_to_update' => $TargetPlanetUpd,
                'time' => time(),
                'debris' => [
                    'metal' => $zlom['metal'],
                    'crystal' => $zlom['crystal'],
                ],
                'coords' => [
                    'galaxy' => $fleet_row['fleet_end_galaxy'],
                    'system' => $fleet_row['fleet_end_system'],
                    'planet' => $fleet_row['fleet_end_planet'],
                    'type' => $fleet_row['fleet_end_type'],
                ]
            ]);

            $StrAttackerUnits = sprintf($this->langs['sys_attacker_lostunits'], $zlom['attacker']);
            $StrDefenderUnits = sprintf($this->langs['sys_defender_lostunits'], $zlom['enemy']);
            $StrRuins = sprintf($this->langs['sys_gcdrunits'], $zlom['metal'], $this->langs['Metal'], $zlom['crystal'], $this->langs['Crystal']);
            $DebrisField = $StrAttackerUnits . "<br />" . $StrDefenderUnits . "<br />" . $StrRuins;

            $AttackDate = date("r", $fleet_row['fleet_start_time']);
            $title = sprintf($this->langs['sys_destruc_title'], $AttackDate);
            $raport = "<center><table><tr><td>" . $title . "<br />";
            $zniszczony = FALSE;
            $AttackTechon['A'] = $current_data['research_weapons_technology'] * 10;
            $AttackTechon['B'] = $current_data['research_shielding_technology'] * 10;
            $AttackTechon['C'] = $current_data['research_armour_technology'] * 10;
            $AttackerData = sprintf($this->langs['sys_attack_attacker_pos'], $current_data['user_name'], $fleet_row['fleet_start_galaxy'], $fleet_row['fleet_start_system'], $fleet_row['fleet_start_planet']);
            $AttackerTech = sprintf($this->langs['sys_attack_techologies'], $AttackTechon['A'], $AttackTechon['B'], $AttackTechon['C']);
            $DefendTechon['A'] = $target_data['research_weapons_technology'] * 10;
            $DefendTechon['B'] = $target_data['research_shielding_technology'] * 10;
            $DefendTechon['C'] = $target_data['research_armour_technology'] * 10;
            $DefenderData = sprintf($this->langs['sys_attack_defender_pos'], $target_data['user_name'], $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']);
            $DefenderTech = sprintf($this->langs['sys_attack_techologies'], $DefendTechon['A'], $DefendTechon['B'], $DefendTechon['C']);

            foreach ($dane_do_rw as $a => $b) {
                $raport .= "<table border=1 width=100%><tr><th><br /><center>" . $AttackerData . "<br />" . $AttackerTech . "<table border=1>";

                if ($b['attacker']['count'] > 0) {
                    $raport1 = "<tr><th>" . $this->langs['sys_ship_type'] . "</th>";
                    $raport2 = "<tr><th>" . $this->langs['sys_ship_count'] . "</th>";
                    $raport3 = "<tr><th>" . $this->langs['sys_ship_weapon'] . "</th>";
                    $raport4 = "<tr><th>" . $this->langs['sys_ship_shield'] . "</th>";
                    $raport5 = "<tr><th>" . $this->langs['sys_ship_armour'] . "</th>";

                    foreach ($b['attacker'] as $Ship => $Data) {
                        if (is_numeric($Ship)) {
                            if ($Data['count'] > 0) {
                                $raport1 .= "<th>" . $this->langs['tech_rc'][$Ship] . "</th>";
                                $raport2 .= "<th>" . $Data['count'] . "</th>";
                                $raport3 .= "<th>" . round($Data['attack'] / $Data['count']) . "</th>";
                                $raport4 .= "<th>" . round($Data['shield'] / $Data['count']) . "</th>";
                                $raport5 .= "<th>" . round($Data['defense'] / $Data['count']) . "</th>";
                            }
                        }
                    }

                    $raport1 .= "</tr>";
                    $raport2 .= "</tr>";
                    $raport3 .= "</tr>";
                    $raport4 .= "</tr>";
                    $raport5 .= "</tr>";
                    $raport .= $raport1 . $raport2 . $raport3 . $raport4 . $raport5;
                } else {
                    $zniszczony = TRUE;
                    $raport .= "<br />" . $this->langs['sys_destroyed'];
                }

                $raport .= "</table></center></th></tr></table>";
                $raport .= "<table border=1 width=100%><tr><th><br /><center>" . $DefenderData . "<br />" . $DefenderTech . "<table border=1>";

                if ($b['enemy']['count'] > 0) {
                    $raport1 = "<tr><th>" . $this->langs['sys_ship_type'] . "</th>";
                    $raport2 = "<tr><th>" . $this->langs['sys_ship_count'] . "</th>";
                    $raport3 = "<tr><th>" . $this->langs['sys_ship_weapon'] . "</th>";
                    $raport4 = "<tr><th>" . $this->langs['sys_ship_shield'] . "</th>";
                    $raport5 = "<tr><th>" . $this->langs['sys_ship_armour'] . "</th>";

                    foreach ($b['enemy'] as $Ship => $Data) {
                        if (is_numeric($Ship)) {
                            if ($Data['count'] > 0) {
                                $raport1 .= "<th>" . $this->langs['tech_rc'][$Ship] . "</th>";
                                $raport2 .= "<th>" . $Data['count'] . "</th>";
                                $raport3 .= "<th>" . round($Data['attack'] / $Data['count']) . "</th>";
                                $raport4 .= "<th>" . round($Data['shield'] / $Data['count']) . "</th>";
                                $raport5 .= "<th>" . round($Data['defense'] / $Data['count']) . "</th>";
                            }
                        }
                    }

                    $raport1 .= "</tr>";
                    $raport2 .= "</tr>";
                    $raport3 .= "</tr>";
                    $raport4 .= "</tr>";
                    $raport5 .= "</tr>";
                    $raport .= $raport1 . $raport2 . $raport3 . $raport4 . $raport5;
                } else {
                    $zniszczony = TRUE;
                    $raport .= "<br />" . $this->langs['sys_destroyed'];
                }

                $raport .= "</table></center></th></tr></table>";

                if (( $zniszczony == FALSE ) && !( $a == 8 )) {
                    $AttackWaveStat = sprintf($this->langs['sys_attack_attack_wave'], floor($b['attacker']['attack']), floor($b['enemy']['shield']));
                    $DefendWavaStat = sprintf($this->langs['sys_attack_defend_wave'], floor($b['enemy']['attack']), floor($b['attacker']['shield']));
                    $raport .= "<br /><center>" . $AttackWaveStat . "<br />" . $DefendWavaStat . "</center>";
                }
            }

            switch ($FleetResult) {
                case "a":

                    $raport .= $this->langs['sys_attacker_won'] . "<br />";
                    $raport .= $DebrisField . "<br />";
                    $raport .= $introdestruc . "<br />";
                    $raport .= $this->langs['sys_destruc_mess1'];
                    $raport .= $finmess . "<br />";
                    $raport .= $probalune . "<br />";
                    $raport .= $probarip . "<br />";

                    break;

                case "r":

                    $raport .= $this->langs['sys_both_won'] . "<br />";
                    $raport .= $DebrisField . "<br />";
                    $raport .= $introdestruc . "<br />";
                    $raport .= $this->langs['sys_destruc_stop'] . "<br />";

                    break;

                case "w":

                    $raport .= $this->langs['sys_defender_won'] . "<br />";
                    $raport .= $DebrisField . "<br />";
                    $raport .= $introdestruc . "<br />";
                    $raport .= $this->langs['sys_destruc_stop'] . "<br />";

                    parent::removeFleet($fleet_row['fleet_id']);

                    break;
            }

            $raport .= "</table>";
            $rid = md5($raport);

            $owners = $fleet_row['fleet_owner'] . "," . $target_data['planet_user_id'];

            $this->Missions_Model->insertReport([
                'owners' => $owners,
                'rid' => $rid,
                'content' => addslashes($raport),
                'time' => time()
            ]);

            $raport = $this->buildReportLink(
                $this->set_report_color($FleetResult), $rid, $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']
            );

            $this->Missions_Model->updateFleetDataToReturn([
                'amount' => $FleetAmount,
                'ships' => $FleetArray,
                'fleet_id' => $fleet_row['fleet_id']
            ]);

            $this->destroy_message($current_data['user_id'], $raport, $fleet_row['fleet_start_time']);

            $raport2 = $this->buildReportLink(
                $this->set_report_color($FleetResult, false), $rid, $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']
            );

            $this->destroy_message($target_data['planet_user_id'], $raport2, $fleet_row['fleet_start_time']);
        } elseif ($fleet_row['fleet_mess'] == 1 && $fleet_row['fleet_end_time'] <= time()) {

            parent::restoreFleet($fleet_row, true);
            parent::removeFleet($fleet_row['fleet_id']);
        }
    }

    /**
     * method attack
     * param $current_ships
     * param $target_ships
     * param $current_tech
     * param $target_tech
     * return process the attack
     */
    private function attack($current_ships, $target_ships, $current_tech, $target_tech)
    {
        $round = array();
        $attacker_n = array();
        $enemy_n = array();

        if (!is_null($current_ships)) {
            $current_debris_start['metal'] = 0;
            $current_debris_start['crystal'] = 0;

            foreach ($current_ships as $a => $b) {
                $current_debris_start['metal'] = $current_debris_start['metal'] + $current_ships[$a]['count'] * $this->pricelist[$a]['metal'];
                $current_debris_start['crystal'] = $current_debris_start['crystal'] + $current_ships[$a]['count'] * $this->pricelist[$a]['crystal'];
            }
        }

        $target_debris_start['metal'] = 0;
        $target_debris_start['crystal'] = 0;
        $target_start = $target_ships;

        if (!is_null($target_ships)) {
            foreach ($target_ships as $a => $b) {
                if ($a < 300) {
                    $target_debris_start['metal'] = $target_debris_start['metal'] + $target_ships[$a]['count'] * $this->pricelist[$a]['metal'];
                    $target_debris_start['crystal'] = $target_debris_start['crystal'] + $target_ships[$a]['count'] * $this->pricelist[$a]['crystal'];
                } else {
                    $target_debris_start_defense['metal'] = $target_debris_start_defense['metal'] + $target_ships[$a]['count'] * $this->pricelist[$a]['metal'];
                    $target_debris_start_defense['crystal'] = $target_debris_start_defense['crystal'] + $target_ships[$a]['count'] * $this->pricelist[$a]['crystal'];
                }
            }
        }

        for ($i = 1; $i <= 7; $i++) {
            $attacker_attack = 0;
            $enemy_attack = 0;
            $attacker_defense = 0;
            $enemy_defense = 0;
            $attacker_amount = 0;
            $enemy_amount = 0;
            $attacker_shield = 0;
            $enemy_shield = 0;

            if (!is_null($current_ships)) {
                foreach ($current_ships as $a => $b) {
                    $current_ships[$a]['defense'] = $current_ships[$a]['count'] * ( $this->pricelist[$a]['metal'] + $this->pricelist[$a]['crystal'] ) / 10 * ( 1 + ( 0.1 * ( $current_tech['research_shielding_technology'] ) ) );
                    $rand = mt_rand(80, 120) / 100;
                    $current_ships[$a]['shield'] = $current_ships[$a]['count'] * $this->combat_caps[$a]['shield'] * ( 1 + ( 0.1 * $current_tech['research_armour_technology'] ) ) * $rand;
                    $atak_statku = $this->combat_caps[$a]['attack'];
                    $technologie = ( 1 + ( 0.1 * $current_tech['research_weapons_technology'] ) );
                    $rand = mt_rand(80, 120) / 100;
                    $number = $current_ships[$a]['count'];
                    $current_ships[$a]['attack'] = $number * $atak_statku * $technologie * $rand;
                    $attacker_attack = $attacker_attack + $current_ships[$a]['attack'];
                    $attacker_defense = $attacker_defense + $current_ships[$a]['defense'];
                    $attacker_amount = $attacker_amount + $current_ships[$a]['count'];
                }
            } else {
                $attacker_amount = 0;
                break;
            }

            if (!is_null($target_ships)) {
                foreach ($target_ships as $a => $b) {
                    $target_ships[$a]['defense'] = $target_ships[$a]['count'] * ( $this->pricelist[$a]['metal'] + $this->pricelist[$a]['crystal'] ) / 10 * ( 1 + ( 0.1 * ( $target_tech['research_shielding_technology'] ) ) );
                    $rand = mt_rand(80, 120) / 100;
                    $target_ships[$a]['shield'] = $target_ships[$a]['count'] * $this->combat_caps[$a]['shield'] * ( 1 + ( 0.1 * $target_tech['research_armour_technology'] ) ) * $rand;
                    $atak_statku = $this->combat_caps[$a]['attack'];
                    $technologie = ( 1 + ( 0.1 * $target_tech['research_weapons_technology'] ) );
                    $rand = mt_rand(80, 120) / 100;
                    $number = $target_ships[$a]['count'];
                    $target_ships[$a]['attack'] = $number * $atak_statku * $technologie * $rand;
                    $enemy_attack = $enemy_attack + $target_ships[$a]['attack'];
                    $enemy_defense = $enemy_defense + $target_ships[$a]['defense'];
                    $enemy_amount = $enemy_amount + $target_ships[$a]['count'];
                }
            } else {
                $enemy_amount = 0;
                $round[$i]['attacker'] = $current_ships;
                $round[$i]['enemy'] = $target_ships;
                $round[$i]['attacker']['attack'] = $attacker_attack;
                $round[$i]['enemy']['attack'] = $enemy_attack;
                $round[$i]['attacker']['count'] = $attacker_amount;
                $round[$i]['enemy']['count'] = $enemy_amount;
                break;
            }

            $round[$i]['attacker'] = $current_ships;
            $round[$i]['enemy'] = $target_ships;
            $round[$i]['attacker']['attack'] = $attacker_attack;
            $round[$i]['enemy']['attack'] = $enemy_attack;
            $round[$i]['attacker']['count'] = $attacker_amount;
            $round[$i]['enemy']['count'] = $enemy_amount;

            if (( $attacker_amount == 0 ) OR ( $enemy_amount == 0 )) {
                break;
            }

            foreach ($current_ships as $a => $b) {
                if ($attacker_amount > 0) {
                    $wrog_moc = $current_ships[$a]['count'] * $enemy_attack / $attacker_amount;

                    if ($current_ships[$a]['shield'] < $wrog_moc) {
                        $max_zdjac = floor($current_ships[$a]['count'] * $enemy_amount / $attacker_amount);
                        $wrog_moc = $wrog_moc - $current_ships[$a]['shield'];
                        $attacker_shield = $attacker_shield + $current_ships[$a]['shield'];
                        $ile_zdjac = floor(( $wrog_moc / ( ( $this->pricelist[$a]['metal'] + $this->pricelist[$a]['crystal'] ) / 10 )));

                        if ($ile_zdjac > $max_zdjac) {
                            $ile_zdjac = $max_zdjac;
                        }

                        $attacker_n[$a]['count'] = ceil($current_ships[$a]['count'] - $ile_zdjac);

                        if ($attacker_n[$a]['count'] <= 0) {
                            $attacker_n[$a]['count'] = 0;
                        }
                    } else {
                        $attacker_n[$a]['count'] = $current_ships[$a]['count'];
                        $attacker_shield = $attacker_shield + $wrog_moc;
                    }
                } else {
                    $attacker_n[$a]['count'] = $current_ships[$a]['count'];
                    $attacker_shield = $attacker_shield + $wrog_moc;
                }
            }

            foreach ($target_ships as $a => $b) {
                if ($enemy_amount > 0) {
                    $atakujacy_moc = $target_ships[$a]['count'] * $attacker_attack / $enemy_amount;

                    if ($target_ships[$a]['shield'] < $atakujacy_moc) {
                        $max_zdjac = floor($target_ships[$a]['count'] * $attacker_amount / $enemy_amount);
                        $atakujacy_moc = $atakujacy_moc - $target_ships[$a]['shield'];
                        $enemy_shield = $enemy_shield + $target_ships[$a]['shield'];
                        $ile_zdjac = floor(( $atakujacy_moc / ( ( $this->pricelist[$a]['metal'] + $this->pricelist[$a]['crystal'] ) / 10 )));

                        if ($ile_zdjac > $max_zdjac) {
                            $ile_zdjac = $max_zdjac;
                        }

                        $enemy_n[$a]['count'] = ceil($target_ships[$a]['count'] - $ile_zdjac);

                        if ($enemy_n[$a]['count'] <= 0) {
                            $enemy_n[$a]['count'] = 0;
                        }
                    } else {
                        $enemy_n[$a]['count'] = $target_ships[$a]['count'];
                        $enemy_shield = $enemy_shield + $atakujacy_moc;
                    }
                } else {
                    $enemy_n[$a]['count'] = $target_ships[$a]['count'];
                    $enemy_shield = $enemy_shield + $atakujacy_moc;
                }
            }

            foreach ($current_ships as $a => $b) {
                foreach ($this->combat_caps[$a]['sd'] as $c => $d) {
                    if (isset($target_ships[$c])) {
                        $enemy_n[$c]['count'] = $enemy_n[$c]['count'] - floor($d * mt_rand(50, 100) / 100);

                        if ($enemy_n[$c]['count'] <= 0) {
                            $enemy_n[$c]['count'] = 0;
                        }
                    }
                }
            }

            foreach ($target_ships as $a => $b) {
                foreach ($this->combat_caps[$a]['sd'] as $c => $d) {
                    if (isset($current_ships[$c])) {
                        $attacker_n[$c]['count'] = $attacker_n[$c]['count'] - floor($d * mt_rand(50, 100) / 100);

                        if ($attacker_n[$c]['count'] <= 0) {
                            $attacker_n[$c]['count'] = 0;
                        }
                    }
                }
            }

            $round[$i]['attacker']['shield'] = $attacker_shield;
            $round[$i]['enemy']['shield'] = $enemy_shield;
            $target_ships = $enemy_n;
            $current_ships = $attacker_n;
        }

        if (( $attacker_amount == 0 ) OR ( $enemy_amount == 0 )) {
            if (( $attacker_amount == 0 ) && ( $enemy_amount == 0 )) {
                $wygrana = "r";
            } else {
                if ($attacker_amount == 0) {
                    $wygrana = "w";
                } else {
                    $wygrana = "a";
                }
            }
        } else {
            $i = sizeof($round);
            $round[$i]['attacker'] = $current_ships;
            $round[$i]['enemy'] = $target_ships;
            $round[$i]['attacker']['attack'] = $attacker_attack;
            $round[$i]['enemy']['attack'] = $enemy_attack;
            $round[$i]['attacker']['count'] = $attacker_amount;
            $round[$i]['enemy']['count'] = $enemy_amount;
            $wygrana = "r";
        }

        $current_debris_end['metal'] = 0;
        $current_debris_end['crystal'] = 0;

        if (!is_null($current_ships)) {
            foreach ($current_ships as $a => $b) {
                $current_debris_end['metal'] = $current_debris_end['metal'] + $current_ships[$a]['count'] * $this->pricelist[$a]['metal'];
                $current_debris_end['crystal'] = $current_debris_end['crystal'] + $current_ships[$a]['count'] * $this->pricelist[$a]['crystal'];
            }
        }

        $target_debris_end['metal'] = 0;
        $target_debris_end['crystal'] = 0;

        if (!is_null($target_ships)) {
            foreach ($target_ships as $a => $b) {
                if ($a < 300) {
                    $target_debris_end['metal'] = $target_debris_end['metal'] + $target_ships[$a]['count'] * $this->pricelist[$a]['metal'];
                    $target_debris_end['crystal'] = $target_debris_end['crystal'] + $target_ships[$a]['count'] * $this->pricelist[$a]['crystal'];
                } else {
                    $target_debris_end_obrona['metal'] = $target_debris_end_obrona['metal'] + $target_ships[$a]['count'] * $this->pricelist[$a]['metal'];
                    $target_debris_end_obrona['crystal'] = $target_debris_end_obrona['crystal'] + $target_ships[$a]['count'] * $this->pricelist[$a]['crystal'];
                }
            }
        }

        $ilosc_wrog = 0;
        $straty_obrona_wrog = 0;

        if (!is_null($target_ships)) {
            foreach ($target_ships as $a => $b) {
                if ($a > 300) {
                    $straty_obrona_wrog = $straty_obrona_wrog + ( ( $target_start[$a]['count'] - $target_ships[$a]['count'] ) * ( $this->pricelist[$a]['metal'] + $this->pricelist[$a]['crystal'] ) );
                    $target_ships[$a]['count'] = $target_ships[$a]['count'] + ( ( $target_start[$a]['count'] - $target_ships[$a]['count'] ) * mt_rand(60, 80) / 100 );
                    $ilosc_wrog = $ilosc_wrog + $target_ships[$a]['count'];
                }
            }
        }

        if (( $ilosc_wrog > 0 ) && ( $attacker_amount == 0 )) {
            $wygrana = "w";
        }

        $game_fleet_cdr = FunctionsLib::readConfig('fleet_cdr');
        $game_def_cdr = FunctionsLib::readConfig('defs_cdr');

        $debris['metal'] = ( ( ( $current_debris_start['metal'] - $current_debris_end['metal'] ) + ($target_debris_start['metal'] - $target_debris_end['metal'] ) ) * ( $game_fleet_cdr / 100 ) );
        $debris['crystal'] = ( ( ( $current_debris_start['crystal'] - $current_debris_end['crystal'] ) + ($target_debris_start['crystal'] - $target_debris_end['crystal'] ) ) * ( $game_fleet_cdr / 100 ) );

        $debris['metal'] += ( ( ( $current_debris_start['metal'] - $current_debris_end['metal']) + ($target_debris_start['metal'] - $target_debris_end['metal'])) * ($game_def_cdr / 100));
        $debris['crystal'] += ( ( ( $current_debris_start['crystal'] - $current_debris_end['crystal']) + ($target_debris_start['crystal'] - $target_debris_end['crystal'])) * ($game_def_cdr / 100));

        $debris['attacker'] = ( ( $current_debris_start['metal'] - $current_debris_end['metal'] ) + ($current_debris_start['crystal'] - $current_debris_end['crystal'] ) );
        $debris['enemy'] = ( ( $target_debris_start['metal'] - $target_debris_end['metal'] ) + ($target_debris_start['crystal'] - $target_debris_end['crystal'] ) + $straty_obrona_wrog );

        return array("attacker" => $current_ships, "enemy" => $target_ships, "win" => $wygrana, "data_for_rw" => $round, "debris" => $debris);
    }

    /**
     * method set_report_color
     * param $result
     * parem $current
     * return the color for the current attack result
     */
    private function set_report_color($result, $current = TRUE)
    {
        if ($current) {
            switch ($result) {
                case 'a':
                    return "green";
                    break;

                case 'r':
                    return "orage";
                    break;

                case 'w':
                    return "red";
                    break;
            }
        } else {
            switch ($result) {
                case 'a':
                    return "red";
                    break;

                case 'r':
                    return "orange";
                    break;

                case 'w':
                    return "green";
                    break;
            }
        }
    }

    /**
     * method destroy_message
     * param $owner
     * param $message
     * param $time
     * return send a message with the destroy details
     */
    private function destroy_message($owner, $message, $time)
    {
        FunctionsLib::sendMessage(
            $owner, '', $time, 1, $this->langs['sys_mess_tower'], $message, ''
        );
    }

    /**
     * buildReportLink
     *
     * @param string $color Color
     * @param string $rid   Report ID
     * @param int    $g     Galaxy
     * @param int    $s     System
     * @param int    $p     Planet
     *
     * @return string
     */
    private function buildReportLink($color, $rid, $g, $s, $p)
    {
        $style = 'style="color:' . $color . ';"';
        $js = "OnClick=\'f(\"game.php?page=combatreport&report=" . $rid . "\", \"\");\'";
        $content = $this->langs['sys_mess_destruc_report'] . ' ' . FormatLib::prettyCoords($g, $s, $p);

        return FunctionsLib::setUrl(
                '', '', $content, $style . ' ' . $js
        );
    }
}

/* end of destroy.php */
