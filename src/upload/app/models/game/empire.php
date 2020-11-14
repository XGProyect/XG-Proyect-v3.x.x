<?php
/**
 * Empire Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\game;

use App\core\Model;

/**
 * Empire Class
 */
class Empire extends Model
{
    /**
     * Get all player data
     *
     * @param integer $user_id
     * @return array
     */
    public function getAllPlayerData(int $user_id): array
    {
        if ($user_id > 0) {
            return $this->db->queryFetchAll(
                "SELECT `planet_id`,
                    `planet_name`,
                    `planet_galaxy`,
                    `planet_system`,
                    `planet_planet`,
                    `planet_type`,
                    `planet_image`,
                    `planet_field_current`,
                    `planet_field_max`,
                    `planet_metal`,
                    `planet_metal_perhour`,
                    `planet_crystal`,
                    `planet_crystal_perhour`,
                    `planet_deuterium`,
                    `planet_deuterium_perhour`,
                    `planet_energy_used`,
                    `planet_energy_max`,
                    b.`building_metal_mine`,
                    b.`building_crystal_mine`,
                    b.`building_deuterium_sintetizer`,
                    b.`building_solar_plant`,
                    b.`building_fusion_reactor`,
                    b.`building_robot_factory`,
                    b.`building_nano_factory`,
                    b.`building_hangar`,
                    b.`building_metal_store`,
                    b.`building_crystal_store`,
                    b.`building_deuterium_tank`,
                    b.`building_laboratory`,
                    b.`building_terraformer`,
                    b.`building_ally_deposit`,
                    b.`building_missile_silo`,
                    b.`building_mondbasis`,
                    b.`building_phalanx`,
                    b.`building_jump_gate`,
                    d.`defense_rocket_launcher`,
                    d.`defense_light_laser`,
                    d.`defense_heavy_laser`,
                    d.`defense_gauss_cannon`,
                    d.`defense_ion_cannon`,
                    d.`defense_plasma_turret`,
                    d.`defense_small_shield_dome`,
                    d.`defense_large_shield_dome`,
                    d.`defense_anti-ballistic_missile`,
                    d.`defense_interplanetary_missile`,
                    s.`ship_small_cargo_ship`,
                    s.`ship_big_cargo_ship`,
                    s.`ship_light_fighter`,
                    s.`ship_heavy_fighter`,
                    s.`ship_cruiser`,
                    s.`ship_battleship`,
                    s.`ship_colony_ship`,
                    s.`ship_recycler`,
                    s.`ship_espionage_probe`,
                    s.`ship_bomber`,
                    s.`ship_solar_satellite`,
                    s.`ship_destroyer`,
                    s.`ship_deathstar`,
                    s.`ship_battlecruiser`
                FROM `" . PLANETS . "` AS p
                INNER JOIN `" . BUILDINGS . "` AS b ON b.building_planet_id = p.`planet_id`
                INNER JOIN `" . DEFENSES . "` AS d ON d.defense_planet_id = p.`planet_id`
                INNER JOIN `" . SHIPS . "` AS s ON s.ship_planet_id = p.`planet_id`
                WHERE `planet_user_id` = '" . $user_id . "'
                    AND `planet_destroyed` = 0;"
            );
        }

        return [];
    }
}
