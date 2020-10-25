<?php
/**
 * Missile Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\libraries\missions;

use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Missile Class
 */
class Missile extends Missions
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Language
        parent::loadLang(['game/missions', 'game/missile', 'game/defenses']);
    }

    /**
     * missileMission
     *
     * @param array $fleet_row Fleet row
     *
     * @return void
     */
    public function missileMission($fleet_row)
    {
        // do mission
        if (parent::canStartMission($fleet_row)) {
            $attacker_data = $this->Missions_Model->getMissileAttackerDataByCoords([
                'coords' => [
                    'galaxy' => $fleet_row['fleet_start_galaxy'],
                    'system' => $fleet_row['fleet_start_system'],
                    'planet' => $fleet_row['fleet_start_planet'],
                    'type' => $fleet_row['fleet_start_type'],
                ],
            ]);

            $target_data = $this->Missions_Model->getMissileTargetDataByCoords([
                'coords' => [
                    'galaxy' => $fleet_row['fleet_end_galaxy'],
                    'system' => $fleet_row['fleet_end_system'],
                    'planet' => $fleet_row['fleet_end_planet'],
                    'type' => $fleet_row['fleet_end_type'],
                ],
            ]);

            if ($target_data['defense_anti-ballistic_missile'] >= $fleet_row['fleet_amount']) {
                $message = $this->langs->line('mis_all_destroyed') . '<br>';
                $amount = $fleet_row['fleet_amount'];
            } else {
                $amount = 0;

                if ($target_data['defense_anti-ballistic_missile'] > 0) {
                    $message = $target_data['defense_anti-ballistic_missile'] .
                    $this->langs->line('mis_some_destroyed') . " <br>";
                }

                $attack = floor(
                    ($fleet_row['fleet_amount'] - $target_data['defense_anti-ballistic_missile']) * ($this->combat_caps[503]['attack'] * (1 + ($attacker_data['research_weapons_technology'] / 10)))
                );
                $attack_order = $this->setAttackOrder($fleet_row['fleet_target_obj']);
                $destroyed_query = '';
                $message = '';

                // PROCESS THE MISSILE ATTACK
                for ($t = 0; $t < count($attack_order); $t++) {
                    $n = $attack_order[$t];

                    if ($target_data[$this->resource[$n]]) {
                        $defense = (($this->pricelist[$n]['metal'] + $this->pricelist[$n]['crystal']) / 10) * (1 + ($target_data['research_shielding_technology'] / 10));

                        if ($attack >= ($defense * $target_data[$this->resource[$n]])) {
                            $destroyed = $target_data[$this->resource[$n]];
                        } else {
                            $destroyed = floor($attack / $defense);
                        }

                        $attack -= $destroyed * $defense;

                        if ($destroyed != 0) {
                            $message .= $target_data[$this->resource[$n]] . ' ' . $this->langs->line($this->resource[$n]) . " (-" . $destroyed . ")<br>";
                            $destroyed_query .= "`" . $this->resource[$n] . "` = `" .
                            $this->resource[$n] . "` - " . $destroyed . ",";
                        }
                    }
                }

                if ($destroyed_query != '') {
                    $this->Missions_Model->updatePlanetDefenses([
                        'destroyed_query' => $destroyed_query,
                        'amount' => $amount,
                        'planet_id' => $target_data['planet_id'],
                    ]);
                }
            }

            $search = ['%1%', '%2%', '%3%'];
            $replace = [
                $fleet_row['fleet_amount'],
                $attacker_data['planet_name'] . ' ' . FormatLib::prettyCoords(
                    $fleet_row['fleet_start_galaxy'],
                    $fleet_row['fleet_start_system'],
                    $fleet_row['fleet_start_planet']
                ),
                $target_data['planet_name'] . ' ' . FormatLib::prettyCoords(
                    $fleet_row['fleet_end_galaxy'],
                    $fleet_row['fleet_end_system'],
                    $fleet_row['fleet_end_planet']
                ),
            ];

            if (empty($message) or $message == '') {
                $message = $this->langs->line('mis_planet_without_defenses');
            }

            // send messages
            // attacker
            FunctionsLib::sendMessage(
                $fleet_row['fleet_owner'],
                '',
                $fleet_row['fleet_end_time'],
                5,
                $this->langs->line('mi_fleet_command'),
                $this->langs->line('mis_attack'),
                str_replace($search, $replace, $this->langs->line('mis_result_own')) . $message
            );

            // enemy
            FunctionsLib::sendMessage(
                $target_data['planet_user_id'],
                '',
                $fleet_row['fleet_end_time'],
                5,
                $this->langs->line('mi_fleet_command'),
                $this->langs->line('mis_attack'),
                str_replace($search, $replace, $this->langs->line('mis_result')) . $message
            );

            parent::removeFleet($fleet_row['fleet_id']);
        }
    }

    /**
     * setAttackOrder
     *
     * @param int $primary_objective Primary objective
     *
     * @return void
     */
    private function setAttackOrder($primary_objective)
    {
        $objectives = [
            0 => [401, 402, 403, 404, 405, 406, 407, 408, 503],
            1 => [401, 402, 403, 404, 405, 406, 407, 408, 503],
            2 => [402, 401, 403, 404, 405, 406, 407, 408, 503],
            3 => [403, 401, 402, 404, 405, 406, 407, 408, 503],
            4 => [404, 401, 402, 403, 405, 406, 407, 408, 503],
            5 => [405, 401, 402, 403, 404, 406, 407, 408, 503],
            6 => [406, 401, 402, 403, 404, 405, 407, 408, 503],
            7 => [407, 401, 402, 403, 404, 405, 406, 408, 503],
            8 => [408, 401, 402, 403, 404, 405, 406, 407, 503],
        ];

        return $objectives[$primary_objective];
    }
}

/* end of missile.php */
