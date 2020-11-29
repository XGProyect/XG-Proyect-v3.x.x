<?php declare (strict_types = 1);

/**
 * Reset Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\adm;

use App\core\Model;
use App\libraries\Functions;
use App\libraries\PlanetLib;

/**
 * Reset Class
 */
class Reset extends Model
{
    /**
     * Set to 0 all planet's defenses
     *
     * @return void
     */
    public function resetDefenses(): void
    {
        $this->db->query(
            "UPDATE `" . DEFENSES . "` SET
            `defense_rocket_launcher` = '0',
            `defense_light_laser` = '0',
            `defense_heavy_laser` = '0',
            `defense_gauss_cannon` = '0',
            `defense_ion_cannon` = '0',
            `defense_plasma_turret` = '0',
            `defense_small_shield_dome` = '0',
            `defense_large_shield_dome` = '0',
            `defense_anti-ballistic_missile` = '0',
            `defense_interplanetary_missile` = '0'"
        );
    }

    /**
     * Set to 0 all planet's ships
     *
     * @return void
     */
    public function resetShips(): void
    {
        $this->db->query(
            "UPDATE `" . SHIPS . "` SET
                `ship_small_cargo_ship` = '0',
                `ship_big_cargo_ship` = '0',
                `ship_light_fighter` = '0',
                `ship_heavy_fighter` = '0',
                `ship_cruiser` = '0',
                `ship_battleship` = '0',
                `ship_colony_ship` = '0',
                `ship_recycler` = '0',
                `ship_espionage_probe` = '0',
                `ship_bomber` = '0',
                `ship_solar_satellite` = '0',
                `ship_destroyer` = '0',
                `ship_deathstar` = '0',
                `ship_battlecruiser` = '0'"
        );
    }

    /**
     * Clears shipyard queues
     *
     * @return void
     */
    public function resetShipyardQueues(): void
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` SET
                `planet_b_hangar` = '0',
                `planet_b_hangar_id` = ''"
        );
    }

    /**
     * Set to 0 all planet's buildings
     *
     * @return void
     */
    public function resetPlanetBuildings(): void
    {
        $this->resetBuildingsByType(1);
    }

    /**
     * Set to 0 all moon's buildings
     *
     * @return void
     */
    public function resetMoonBuildings(): void
    {
        $this->resetBuildingsByType(3);
    }

    /**
     * Clears buildings queues
     *
     * @return void
     */
    public function resetBuildingsQueues(): void
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` SET
                `planet_b_building` = '0',
                `planet_b_building_id` = ''"
        );
    }

    /**
     * Set to 0 all research
     *
     * @return void
     */
    public function resetResearch(): void
    {
        $this->db->query(
            "UPDATE `" . RESEARCH . "` SET
                `research_espionage_technology` = '0',
                `research_computer_technology` = '0',
                `research_weapons_technology` = '0',
                `research_shielding_technology` = '0',
                `research_armour_technology` = '0',
                `research_energy_technology` = '0',
                `research_hyperspace_technology` = '0',
                `research_combustion_drive` = '0',
                `research_impulse_drive` = '0',
                `research_hyperspace_drive` = '0',
                `research_laser_technology` = '0',
                `research_ionic_technology` = '0',
                `research_plasma_technology` = '0',
                `research_intergalactic_research_network` = '0',
                `research_astrophysics` = '0',
                `research_graviton_technology` = '0'"
        );
    }

    /**
     * Clears research queues
     *
     * @return void
     */
    public function resetResearchQueues(): void
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` SET
                `planet_b_tech` = '0',
                `planet_b_tech_id` = '0'"
        );

        $this->db->query(
            "UPDATE `" . RESEARCH . "` SET
                `research_current_research` = '0'"
        );
    }

    /**
     * Set to 0 all user's officiers
     *
     * @return void
     */
    public function resetOfficiers(): void
    {
        $this->db->query(
            "UPDATE `" . PREMIUM . "` SET
                `premium_officier_commander` = '0',
                `premium_officier_admiral` = '0',
                `premium_officier_engineer` = '0',
                `premium_officier_geologist` = '0',
                `premium_officier_technocrat` = '0'"
        );
    }

    /**
     * Set to 0 all user's dark matter
     *
     * @return void
     */
    public function resetDarkMatter(): void
    {
        $this->db->query(
            "UPDATE `" . PREMIUM . "` SET
                `premium_dark_matter` = '0'"
        );
    }

    /**
     * Set to 0 all planets metal, crystal and deuterium
     *
     * @return void
     */
    public function resetResources(): void
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` SET
                `planet_metal` = '0',
                `planet_crystal` = '0',
                `planet_deuterium` = '0'"
        );
    }

    /**
     * Removes all the notes
     *
     * @return void
     */
    public function resetNotes(): void
    {
        $this->db->query("TRUNCATE TABLE `" . NOTES . "`");
    }

    /**
     * Removes all reports
     *
     * @return void
     */
    public function resetReports(): void
    {
        $this->db->query("TRUNCATE TABLE `" . REPORTS . "`");
    }

    /**
     * Removes all friends
     *
     * @return void
     */
    public function resetFriends(): void
    {
        $this->db->query("TRUNCATE TABLE `" . BUDDY . "`");
    }

    /**
     * Removes all alliances and their related data
     *
     * @return void
     */
    public function resetAlliances(): void
    {
        $this->db->query("TRUNCATE TABLE `" . ALLIANCE . "`");
        $this->db->query("TRUNCATE TABLE `" . ALLIANCE_STATISTICS . "`");
        $this->db->query(
            "UPDATE `" . USERS . "` SET
                `user_ally_id` = '0',
                `user_ally_request` = '0',
                `user_ally_request_text` = 'NULL',
                `user_ally_register_time` = '0',
                `user_ally_rank_id` = '0'"
        );
    }

    /**
     * Removes all fleets and their related data including ACS
     *
     * @return void
     */
    public function resetFleets(): void
    {
        $this->db->query("TRUNCATE TABLE `" . ACS . "`");
        $this->db->query("TRUNCATE TABLE `" . ACS_MEMBERS . "`");
        $this->db->query("TRUNCATE TABLE `" . FLEETS . "`");
    }

    /**
     * Removes all suspensions (bans)
     *
     * @return void
     */
    public function resetBanned(): void
    {
        $this->db->query("TRUNCATE TABLE `" . BANNED . "`");
        $this->db->query(
            "UPDATE `" . USERS . "` SET
                `user_banned` = '0'
                WHERE `user_id` > '1'"
        );
    }

    /**
     * Removes all messages
     *
     * @return void
     */
    public function resetMessages(): void
    {
        $this->db->query("TRUNCATE TABLE `" . MESSAGES . "`");
    }

    /**
     * Set to 0 all the statistics
     *
     * @return void
     */
    public function resetStatistics(): void
    {
        $this->db->query("TRUNCATE TABLE `" . USERS_STATISTICS . "`");
        $this->db->query("TRUNCATE TABLE `" . ALLIANCE_STATISTICS . "`");
    }

    /**
     * Deletes all moons
     *
     * @return void
     */
    public function resetMoons(): void
    {
        $this->db->query("DELETE FROM `" . PLANETS . "` WHERE `planet_type` = '3'");
    }

    /**
     * Reset the whole server
     *
     * @return void
     */
    public function resetAll(): void
    {
        try {
            $this->db->beginTransaction();

            // initial resets
            $this->resetFleets();
            $this->resetFriends();
            $this->resetMessages();
            $this->resetNotes();
            $this->resetReports();
            $this->resetStatistics();

            // other resets
            $this->db->query("TRUNCATE TABLE `" . ALLIANCE . "`");
            $this->db->query("TRUNCATE TABLE `" . BANNED . "`");
            $this->db->query("TRUNCATE TABLE `" . BUILDINGS . "`");
            $this->db->query("TRUNCATE TABLE `" . DEFENSES . "`");
            $this->db->query("TRUNCATE TABLE `" . PREFERENCES . "`");
            $this->db->query("TRUNCATE TABLE `" . PREMIUM . "`");
            $this->db->query("TRUNCATE TABLE `" . RESEARCH . "`");
            $this->db->query("TRUNCATE TABLE `" . SESSIONS . "`");
            $this->db->query("TRUNCATE TABLE `" . SHIPS . "`");

            // new creator
            $creator = $this->newCreator();

            // users and planets resets
            $this->db->query("RENAME TABLE `" . USERS . "` TO `" . USERS . "_s`");
            $this->db->query("RENAME TABLE `" . PLANETS . "` TO `" . PLANETS . "_s`");

            $this->db->query("CREATE TABLE IF NOT EXISTS `" . USERS . "` ( LIKE `" . USERS . "_s` );");
            $this->db->query("CREATE TABLE IF NOT EXISTS `" . PLANETS . "` ( LIKE `" . PLANETS . "_s` );");

            $all_users = $this->db->query(
                "SELECT
                    `user_name`,
                    `user_password`,
                    `user_email`,
                    `user_authlevel`,
                    `user_galaxy`,
                    `user_system`,
                    `user_planet`,
                    `user_onlinetime`,
                    `user_register_time`,
                    `user_home_planet_id`
                FROM `" . USERS . "_s`
                WHERE 1;"
            );

            $limit_time = time() - (ONE_WEEK * 2 + ONE_DAY); // 15 days

            while ($user = $this->db->fetchAssoc($all_users)) {
                if ($user['user_onlinetime'] > $limit_time) {
                    $time = time();

                    $this->db->query(
                        "INSERT INTO `" . USERS . "` SET
                            `user_name` = '" . $user['user_name'] . "',
                            `user_email` = '" . $user['user_email'] . "',
                            `user_home_planet_id` = '0',
                            `user_authlevel` = '" . $user['user_authlevel'] . "',
                            `user_galaxy` = '" . $user['user_galaxy'] . "',
                            `user_system` = '" . $user['user_system'] . "',
                            `user_planet` = '" . $user['user_planet'] . "',
                            `user_register_time` = '" . $user['user_register_time'] . "',
                            `user_onlinetime` = '" . $time . "',
                            `user_password` = '" . $user['user_password'] . "';"
                    );

                    $last_id = $this->db->insertId();
                    $new_user = $last_id;

                    $this->db->query(
                        "INSERT INTO `" . RESEARCH . "` SET
                            `research_user_id` = '" . $last_id . "';"
                    );

                    $this->db->query(
                        "INSERT INTO `" . USERS_STATISTICS . "` SET
                            `user_statistic_user_id` = '" . $last_id . "';"
                    );

                    $this->db->query(
                        "INSERT INTO `" . PREMIUM . "` (`premium_user_id`, `premium_dark_matter`)
                        VALUES('" . $last_id . "', '" . Functions::readConfig('registration_dark_matter') . "');"
                    );

                    $this->db->query(
                        "INSERT INTO `" . PREFERENCES . "` SET
                            `preference_user_id` = '" . $last_id . "';"
                    );

                    $creator->setNewPlanet(
                        $user['user_galaxy'],
                        $user['user_system'],
                        $user['user_planet'],
                        $new_user,
                        '',
                        true
                    );

                    $this->db->query(
                        "UPDATE `" . USERS . "` SET
                        `user_home_planet_id` = '" . $new_user . "',
                        `user_current_planet` = '" . $new_user . "'
                        WHERE `user_id` = '" . $new_user . "';"
                    );
                }
            }

            $this->db->query("DROP TABLE `" . PLANETS . "_s`");
            $this->db->query("DROP TABLE `" . USERS . "_s`");

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }

    /**
     * Reset buildings by type
     *
     * @param integer $planet_type
     * @return void
     */
    private function resetBuildingsByType(int $planet_type): void
    {
        $this->db->query(
            "UPDATE `" . BUILDINGS . "` AS b
                INNER JOIN `" . PLANETS . "` AS p ON b.`building_planet_id` = p.`planet_id` SET
                `building_metal_mine` = '0',
                `building_crystal_mine` = '0',
                `building_deuterium_sintetizer` = '0',
                `building_solar_plant` = '0',
                `building_fusion_reactor` = '0',
                `building_robot_factory` = '0',
                `building_nano_factory` = '0',
                `building_hangar` = '0',
                `building_metal_store` = '0',
                `building_crystal_store` = '0',
                `building_deuterium_tank` = '0',
                `building_laboratory` = '0',
                `building_terraformer` = '0',
                `building_ally_deposit` = '0',
                `building_missile_silo` = '0',
                `building_mondbasis` = '0',
                `building_phalanx` = '0',
                `building_jump_gate` = '0'
                WHERE p.`planet_type` = '" . $planet_type . "'"
        );
    }

    /**
     * Get a new creator
     *
     * @return PlanetLib
     */
    private function newCreator(): PlanetLib
    {
        return new PlanetLib();
    }
}
