<?php
/**
 * Missile Library
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

use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Missile Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Missile extends Missions
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
        if ($fleet_row['fleet_start_time'] <= time()) {

            if ($fleet_row['fleet_mess'] == 0) {

                $attacker_data  = parent::$db->queryFetch(
                    "SELECT p.`planet_name`, r.`research_weapons_technology`
                    FROM " . PLANETS . " AS p
                    INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = p.planet_user_id
                    WHERE `planet_galaxy` = " . $fleet_row['fleet_start_galaxy'] . " AND
                        `planet_system` = " . $fleet_row['fleet_start_system'] . " AND
                        `planet_planet` = " . $fleet_row['fleet_start_planet'] . " AND
                        `planet_type` = " . $fleet_row['fleet_start_type'] . ";"
                );

                $target_data = parent::$db->queryFetch(
                    "SELECT p.`planet_id`, p.`planet_name`, p.`planet_user_id`, d.*, r.`research_shielding_technology`
                    FROM " . PLANETS . " AS p
                    INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
                    INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = p.planet_user_id
                    WHERE `planet_galaxy` = " . $fleet_row['fleet_end_galaxy'] . " AND
                                    `planet_system` = " . $fleet_row['fleet_end_system'] . " AND
                                    `planet_planet` = " . $fleet_row['fleet_end_planet'] . " AND
                                    `planet_type` = " . $fleet_row['fleet_end_type'] . ";"
                );



                if ($target_data['defense_anti-ballistic_missile'] >= $fleet_row['fleet_amount']) {

                    $message    = $this->langs['ma_all_destroyed'] . '<br>';
                    $amount     = $fleet_row['fleet_amount'];
                } else {

                    $amount = 0;

                    if ($target_data['defense_anti-ballistic_missile'] > 0) {

                        $message    = $target_data['defense_anti-ballistic_missile'] .
                            $this->langs['ma_some_destroyed'] . " <br>";
                    }

                    $attack             = floor(
                        ($fleet_row['fleet_amount'] - $target_data['defense_anti-ballistic_missile'])
                        * ($this->combat_caps[503]['attack']
                        * (1 + ($attacker_data['research_weapons_technology'] / 10)))
                    );
                    $attack_order       = $this->setAttackOrder($fleet_row['fleet_target_obj']);
                    $destroyed_query    = '';

                    // PROCESS THE MISSILE ATTACK
                    for ($t = 0; $t < count($attack_order); $t++) {

                        $n  = $attack_order[$t];

                        if ($target_data[$this->resource[$n]]) {

                            $defense    = (($this->pricelist[$n]['metal'] + parent::$pricelist[$n]['crystal']) / 10)
                                * (1 + ( $target_data['research_shielding_technology'] / 10));

                            if ($attack >= ($defense * $target_data[$this->resource[$n]])) {

                                $destroyed  = $target_data[$this->resource[$n]];
                            } else {

                                $destroyed  = floor($attack / $defense);
                            }

                            $attack -= $destroyed * $defense;

                            if ($destroyed != 0) {

                                $message            .= $this->langs['tech'][$n] . " (-" . $destroyed . ")<br>";
                                $destroyed_query    .= "`" . $this->resource[$n] . "` = `" .
                                    $this->resource[$n] . "` - " . $destroyed . ",";
                            }
                        }
                    }

                    if ($destroyed_query != '') {

                        parent::$db->query(
                            "UPDATE " . DEFENSES . " SET
                            {$destroyed_query}
                            `defense_anti-ballistic_missile` = '" . $amount . "'
                            WHERE defense_planet_id = '" . $target_data['id'] . "';"
                        );
                    }
                }

                $search     = ['%1%', '%2%', '%3%'];
                $replace    = [
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
                $message_vorlage    = str_replace($search, $replace, $this->langs['ma_missile_string']);

                if (empty($message) or $message == '') {

                    $message    = $this->langs['ma_planet_without_defens'];
                }

                // send message to the enemy
                FunctionsLib::sendMessage(
                    $target_data['planet_user_id'],
                    '',
                    $fleet_row['fleet_end_time'],
                    5,
                    $this->langs['sys_mess_tower'],
                    $this->langs['gl_missile_attack'],
                    $message_vorlage . $message
                );

                parent::removeFleet($fleet_row['fleet_id']);
            }
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
        switch ($primary_objective) {
            case 0:
                return [401, 402, 403, 404, 405, 406, 407, 408, 503];

                break;

            case 1:
                return [402, 401, 403, 404, 405, 406, 407, 408, 503];

                break;

            case 2:
                return [403, 401, 402, 404, 405, 406, 407, 408, 503];

                break;

            case 3:
                return [404, 401, 402, 403, 405, 406, 407, 408, 503];

                break;

            case 4:
                return [405, 401, 402, 403, 404, 406, 407, 408, 503];

                break;

            case 5:
                return [406, 401, 402, 403, 404, 405, 407, 408, 503];

                break;

            case 6:
                return [407, 401, 402, 403, 404, 405, 406, 408, 503];

                break;

            case 7:
                return [408, 401, 402, 403, 404, 405, 406, 407, 503];

                break;

            case 8:
                return [401, 402, 403, 404, 405, 406, 407, 408, 503];

                break;
        }
    }
}

/* end of missile.php */
