<?php
/**
 * Fleet Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\game;

use App\core\entities\FleetEntity;
use App\core\enumerators\MissionsEnumerator as Missions;
use App\core\Model;

/**
 * Fleet Class
 */
class Fleet extends Model
{
    /**
     * Get ships by planet id
     *
     * @param int $planet_id Planet ID
     *
     * @return array
     */
    public function getShipsByPlanetId($planet_id)
    {
        if ((int) $planet_id > 0) {
            return $this->db->queryFetch(
                "SELECT
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
                    FROM `" . SHIPS . "` AS s
                    WHERE s.`ship_planet_id` = '" . $planet_id . "';"
            );
        }

        return [];
    }

    /**
     * Get all fleets by user id or owner
     *
     * @param int $user_id User ID
     *
     * @return array
     */
    public function getAllFleetsByUserId($user_id)
    {
        if ((int) $user_id > 0) {
            return $this->db->queryFetchAll(
                "SELECT
                        f.*
                    FROM `" . FLEETS . "` f
                    WHERE f.`fleet_owner` = '" . $user_id . "';"
            );
        }

        return [];
    }

    /**
     * Get ACS Data by group ID
     *
     * @param int $group_id
     *
     * @return array
     */
    public function getAcsDataByGroupId(string $group_id)
    {
        if (!empty($group_id)) {
            return $this->db->queryFetch(
                "SELECT
                        acs.*,
                        (
                            SELECT
                                COUNT(*)
                            FROM `" . ACS_MEMBERS . "` am
                            WHERE am.`acs_group_id` = acs.`acs_id`
                        ) AS `acs_members`
                    FROM `" . ACS . "` acs
                    WHERE acs.`acs_id` = '" . $group_id . "';"
            );
        }

        return [];
    }

    /**
     * Get all user planets
     *
     * @param int $user_id User ID
     *
     * @return array
     */
    public function getAllPlanetsByUserId($user_id)
    {
        if ((int) $user_id > 0) {
            return $this->db->queryFetchAll(
                "SELECT
                    p.`planet_id`,
                    p.`planet_name`,
                    p.`planet_galaxy`,
                    p.`planet_system`,
                    p.`planet_planet`,
                    p.`planet_type`
                FROM `" . PLANETS . "` AS p
                WHERE p.`planet_user_id` = '" . $user_id . "';"
            );
        }

        return [];
    }

    /**
     * Get ongoing ACS attacks
     *
     * @param
     *
     * @return mixed
     */
    public function getOngoingAcs($user_id)
    {
        return $this->db->queryFetchAll(
            "SELECT
                    acs.*
                FROM `" . ACS_MEMBERS . "` am
                INNER JOIN `" . ACS . "` acs ON acs.`acs_id` = am.`acs_group_id`
                INNER JOIN `" . FLEETS . "` f ON f.`fleet_group` = acs.`acs_id`
                WHERE am.`acs_user_id` = '" . $user_id . "';"
        );
    }

    /**
     * Get planet owner by coords
     *
     * @param int $g    Galaxy
     * @param int $s    System
     * @param int $p    Planet
     * @param int $pt   Planet Type
     *
     * @return bool
     */
    public function getPlanetOwnerByCoords(int $g, int $s, int $p, int $pt): array
    {
        return $this->db->queryFetch(
            "SELECT
                p.`planet_user_id`,
                u.`user_ally_id`
            FROM `" . PLANETS . "` AS p
            INNER JOIN `" . USERS . "` AS u
                ON u.`user_id` = p.`planet_user_id`
            WHERE p.`planet_galaxy` = '" . $g . "'
                AND p.`planet_system` = '" . $s . "'
                AND p.`planet_planet` = '" . $p . "'
                AND p.`planet_type` = '" . $pt . "';"
        ) ?? [];
    }

    /**
     * Get target data by coords
     *
     * @param int $g    Galaxy
     * @param int $s    System
     * @param int $p    Planet
     * @param int $pt   Planet Type
     *
     * @return array
     */
    public function getTargetDataByCoords(int $g, int $s, int $p, int $pt): array
    {
        return $this->db->queryFetch(
            "SELECT
                p.`planet_user_id`,
                p.`planet_debris_metal`,
                p.`planet_debris_crystal`,
                p.`planet_invisible_start_time`,
                p.`planet_destroyed`,
                u.`user_id`,
                u.`user_authlevel`,
                u.`user_onlinetime`,
                u.`user_ally_id`,
                pr.`preference_vacation_mode`
            FROM `" . PLANETS . "` p
            INNER JOIN `" . USERS . "` u ON u.`user_id` = p.`planet_user_id`
            INNER JOIN `" . PREFERENCES . "` pr ON pr.`preference_user_id` = u.`user_id`
            WHERE p.`planet_galaxy` = '" . $g . "'
                AND p.`planet_system` = '" . $s . "'
                AND p.`planet_planet` = '" . $p . "'
                AND p.`planet_type` = '" . $pt . "'"
        ) ?? [];
    }

    /**
     * Get ACS count
     *
     * @param type $acs_id
     *
     * @return int
     */
    public function getAcsCount($acs_id): int
    {
        return $this->db->queryFetch(
            "SELECT COUNT(`acs_id`) AS `acs_amount`
            FROM `" . ACS . "`
            WHERE `acs_id` = '" . $acs_id . "'"
        )['acs_amount'] ?? 0;
    }

    /**
     * Insert a new fleet
     *
     * @param array $fleet_data
     * @param array $planet_data
     *
     * @return boolean
     */
    public function insertNewFleet(array $fleet_data, array $planet_data, array $fleet_ships): bool
    {
        try {
            $this->db->beginTransaction();

            // prepare the query
            foreach ($fleet_data as $field => $value) {
                $sql[] = "`" . $field . "` = '" . $value . "'";
            }

            $this->db->query(
                "INSERT INTO `" . FLEETS . "` SET "
                . join(', ', $sql) .
                ", `fleet_creation` = '" . time() . "';"
            );

            // remove ships and resources
            $this->updatePlanet($planet_data, $fleet_data, $fleet_ships);

            $this->db->commitTransaction();

            return true;
        } catch (Exception $e) {
            $this->db->rollbackTransaction();

            return false;
        }
    }

    /**
     * Insert a new missiles mission into the fleets table
     *
     * @param array $data
     * @return void
     */
    public function insertNewMissilesMission(array $data): void
    {
        try {
            $this->db->beginTransaction();

            $this->db->query(
                "INSERT INTO `" . FLEETS . "` SET
                `fleet_owner` = '" . $data['fleet_owner'] . "',
                `fleet_mission` = '10',
                `fleet_amount` = " . $data['fleet_amount'] . ",
                `fleet_array` = '" . $data['fleet_array'] . "',
                `fleet_start_time` = '" . $data['fleet_start_time'] . "',
                `fleet_start_galaxy` = '" . $data['fleet_start_galaxy'] . "',
                `fleet_start_system` = '" . $data['fleet_start_system'] . "',
                `fleet_start_planet` ='" . $data['fleet_start_planet'] . "',
                `fleet_start_type` = '1',
                `fleet_end_time` = '" . $data['fleet_end_time'] . "',
                `fleet_end_stay` = '0',
                `fleet_end_galaxy` = '" . $data['fleet_end_galaxy'] . "',
                `fleet_end_system` = '" . $data['fleet_end_system'] . "',
                `fleet_end_planet` = '" . $data['fleet_end_planet'] . "',
                `fleet_end_type` = '1',
                `fleet_target_obj` = '" . $data['fleet_target_obj'] . "',
                `fleet_resource_metal` = '0',
                `fleet_resource_crystal` = '0',
                `fleet_resource_deuterium` = '0',
                `fleet_target_owner` = '" . $data['fleet_target_owner'] . "',
                `fleet_group` = '0',
                `fleet_mess` = '0',
                `fleet_creation` = '" . time() . "';"
            );

            $this->db->query(
                "UPDATE `" . DEFENSES . "` SET
                    `defense_interplanetary_missile` = `defense_interplanetary_missile` - " . $data['fleet_amount'] . "
                WHERE `defense_planet_id` =  '" . $data['user_current_planet'] . "'"
            );

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }

    /**
     * Update planet based on the received values
     *
     * @param array $planet_data Planet Data
     * @param array $fleet_data  Fleet Data
     * @param array $fleet_ships Fleet Ships
     *
     * @return void
     */
    public function updatePlanet(array $planet_data, array $fleet_data, array $fleet_ships)
    {
        // prepare the query
        foreach ($fleet_ships as $field => $value) {
            $sql[] = "`" . $field . "` = `" . $field . "` - '" . $value . "'";
        }

        $this->db->query(
            "UPDATE `" . PLANETS . "` AS p
            INNER JOIN `" . SHIPS . "` AS s ON s.`ship_planet_id` = p.`planet_id` SET
            " . join(', ', $sql) . ",
            `planet_metal` = `planet_metal` - " . $fleet_data['fleet_resource_metal'] . ",
            `planet_crystal` = `planet_crystal` - " . $fleet_data['fleet_resource_crystal'] . ",
            `planet_deuterium` = `planet_deuterium` - " . ($fleet_data['fleet_resource_deuterium'] + $fleet_data['fleet_fuel']) . "
            WHERE `planet_id` = " . $planet_data['planet_id'] . ";"
        );
    }

    /**
     * Get buddies
     *
     * @param int $current_planet Current Planet ID
     * @param int $target_planet  Target Planet ID
     *
     * @return array
     */
    public function getBuddies(int $current_planet, int $target_planet): string
    {
        return $this->db->queryFetch(
            "SELECT COUNT(*) AS buddies
            FROM  `" . BUDDY . "`
            WHERE (
                (
                    buddy_sender = '" . $current_planet . "'
                    AND buddy_receiver = '" . $target_planet . "'
                )
                OR (
                    buddy_sender = '" . $target_planet . "'
                    AND buddy_receiver = '" . $current_planet . "'
                )
            )
            AND buddy_status = 1"
        )['buddies'];
    }

    /**
     * Get ACS Max Time
     *
     * @param int $group_id Group ID
     *
     * @return string
     */
    public function getAcsMaxTime(int $group_id): string
    {
        return $this->db->queryFetch(
            "SELECT MAX(`fleet_start_time`) AS start_time
                FROM `" . FLEETS . "`
                WHERE `fleet_group` = '" . $group_id . "';"
        )['start_time'];
    }

    /**
     * Update ACS Fleets Times
     *
     * @param int $group_id   Group ID
     * @param int $start_time Start Time
     * @param int $end_time   End Time
     *
     * @return void
     */
    public function updateAcsTimes(int $group_id, int $start_time, int $end_time)
    {
        $this->db->query(
            "UPDATE `" . FLEETS . "` SET
            `fleet_start_time` = '" . $start_time . "',
            `fleet_end_time` = fleet_end_time + '" . $end_time . "'
            WHERE `fleet_group` = '" . $group_id . "';"
        );
    }

    /**
     * Get the ACS Owner
     *
     * @param int $fleet_group
     *
     * @return string
     */
    public function getAcsOwner(int $fleet_group): int
    {
        return $this->db->queryFetch(
            "SELECT af.`acs_owner`
                FROM `" . ACS . "` af
                WHERE af.`acs_id` = '" . $fleet_group . "';"
        )['acs_owner'] ?? 0;
    }

    /**
     * Remove an ACS fleet
     *
     * @param int $fleet_group
     *
     * @return void
     */
    public function removeAcs(int $fleet_group): void
    {
        $this->db->query(
            "DELETE FROM `" . ACS . "`
            WHERE `acs_id` = '" . $fleet_group . "';"
        );

        $this->db->query(
            "UPDATE `" . FLEETS . "` f SET
                f.`fleet_group` = '0'
            WHERE f.`fleet_group` = '" . $fleet_group . "';"
        );
    }

    /**
     * Return the fleet to its start planet
     *
     * @param FleetEntity $fleet
     * @param int         $user_id Current user ID
     *
     * @return bool
     */
    public function returnFleet(FleetEntity $fleet, int $user_id): bool
    {
        try {
            $this->db->beginTransaction();

            if ($fleet->getFleetGroup() > 0) {
                $acs = $this->getAcsOwner($fleet->getFleetGroup());

                if (!empty($acs['acs_owner'])
                    && $acs['acs_owner'] == $fleet->getFleetOwner()
                    && $fleet->getFleetMission() == Missions::ATTACK) {
                    $this->removeAcs($fleet->getFleetGroup());
                }

                if ($fleet->getFleetMission() == Missions::ACS) {
                    $this->db->query(
                        "UPDATE `" . FLEETS . "` f SET
                            f.`fleet_group` = '0'
                        WHERE f.`fleet_id` = '" . $fleet->getFleetId() . "';"
                    );
                }
            }

            $base_time = time();
            $fleet_creation = $fleet->getFleetCreation();
            $current_time = $base_time - $fleet_creation;
            $flight_lenght = $fleet->getFleetStartTime() - $fleet_creation;
            $return_time = $base_time + $current_time;

            if ($fleet->getFleetEndStay() != 0
                && $current_time > $flight_lenght) {
                $return_time = $base_time + $flight_lenght;
            }

            $this->db->query(
                "UPDATE `" . FLEETS . "` f SET
                    f.`fleet_start_time` = '" . $base_time . "',
                    f.`fleet_end_stay` = '0',
                    f.`fleet_end_time` = '" . $return_time . "',
                    f.`fleet_target_owner` = '" . $user_id . "',
                    f.`fleet_mess` = '1'
                WHERE f.`fleet_id` = '" . $fleet->getFleetId() . "';"
            );

            $this->db->commitTransaction();

            return true;
        } catch (Exception $e) {
            $this->db->rollbackTransaction();

            return false;
        }
    }

    /**
     * Create a new ACS Record
     *
     * @param type $acs_code
     * @param FleetEntity $fleet
     *
     * @return boolean
     */
    public function createNewAcs($acs_code, FleetEntity $fleet)
    {
        try {
            $this->db->beginTransaction();

            $this->db->query(
                "INSERT INTO `" . ACS . "` SET
                    `acs_name` = '" . $acs_code . "',
                    `acs_owner` = '" . $fleet->getFleetOwner() . "',
                    `acs_galaxy` = '" . $fleet->getFleetEndGalaxy() . "',
                    `acs_system` = '" . $fleet->getFleetEndSystem() . "',
                    `acs_planet` = '" . $fleet->getFleetEndPlanet() . "',
                    `acs_planet_type` = '" . $fleet->getFleetEndType() . "'"
            );

            $group_id = $this->db->insertId();

            $this->db->query(
                "UPDATE `" . FLEETS . "` SET
                    `fleet_group` = '" . $group_id . "'
                WHERE `fleet_id` = '" . $fleet->getFleetId() . "'"
            );

            $this->insertNewAcsMember($fleet->getFleetOwner(), $group_id);

            $this->db->commitTransaction();

            return $group_id;
        } catch (Exception $e) {
            $this->db->rollbackTransaction();

            return false;
        }
    }

    /**
     * Get all the ACS Members
     *
     * @param int $group_id
     *
     * @return array
     */
    public function getListOfAcsMembers($group_id)
    {
        return $this->db->queryFetchAll(
            "SELECT
                u.`user_id`,
                u.`user_name`
            FROM `" . ACS_MEMBERS . "` am
                INNER JOIN `" . USERS . "` u ON u.`user_id` = am.`acs_user_id`
            WHERE am.`acs_group_id` = '" . $group_id . "'"
        );
    }

    /**
     * Update ACS Name
     *
     * @param string $acs_name
     * @param int $user_id
     *
     * @return void
     */
    public function updateAcsName(string $acs_name, int $acs_id, int $user_id): void
    {
        $this->db->query(
            "UPDATE `" . ACS . "` acs SET
                acs.`acs_name` = '" . $this->db->escapeValue($acs_name) . "'
            WHERE acs.`acs_id` = '" . $acs_id . "'
                AND acs.`acs_owner` = '" . $user_id . "';"
        );
    }

    /**
     * Create a new ACS Member
     *
     * @param int $member
     * @param int $group_id
     *
     * @return void
     */
    public function insertNewAcsMember(int $member, int $group_id): void
    {
        $this->db->query(
            "INSERT INTO `" . ACS_MEMBERS . "` SET
                `acs_group_id` = '" . $group_id . "',
                `acs_user_id` = '" . $member . "'"
        );
    }

    /**
     * Remove an ACS Member
     *
     * @param int $member
     * @param int $group_id
     *
     * @return void
     */
    public function removeAcsMember(int $member, int $group_id): void
    {
        $this->db->query(
            "DELETE FROM `" . ACS_MEMBERS . "`
             WHERE `acs_group_id` = '" . $group_id . "'
                AND `acs_user_id` = '" . $member . "'"
        );
    }

    /**
     * Get the user name by ID
     *
     * @param string $user_name User Name
     *
     * @return int
     */
    public function getUserIdByName(string $user_name, int $group_id): int
    {
        return $this->db->queryFetch(
            "SELECT
                u.`user_id`
            FROM `" . USERS . "` u
            WHERE u.`user_name` = '" . $user_name . "'
            AND u.`user_id` NOT IN (
                SELECT
                    acs.`acs_user_id`
                FROM `" . ACS_MEMBERS . "` acs
                WHERE acs.`acs_group_id` = '" . $group_id . "'
            )"
        )['user_id'] ?? 0;
    }
}
