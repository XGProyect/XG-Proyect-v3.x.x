<?php

namespace App\Models\Libraries\Missions;

use App\Core\Model;
use App\Libraries\FleetsLib;
use App\Libraries\StatisticsLibrary;

class Missions extends Model
{
    public function deleteFleetById(int $fleedId): void
    {
        if ((int) $fleedId > 0) {
            $this->db->query(
                'DELETE FROM `' . FLEETS . "` WHERE `fleet_id` = '" . $fleedId . "'"
            );
        }
    }

    public function updateFleetStatusToReturnById(int $fleedId): void
    {
        if ((int) $fleedId > 0) {
            $this->db->query(
                'UPDATE ' . FLEETS . " SET
                    `fleet_mess` = '1'
                WHERE `fleet_id` = '" . $fleedId . "'"
            );
        }
    }

    public function updateFleetStatusToStayById($fleedId): void
    {
        if ((int) $fleedId > 0) {
            $this->db->query(
                'UPDATE ' . FLEETS . " SET
                    `fleet_mess` = '2'
                WHERE `fleet_id` = '" . $fleedId . "'"
            );
        }
    }

    public function updatePlanetsShipsByCoords(array $data = []): void
    {
        if (is_array($data)) {
            $this->db->query(
                'UPDATE ' . PLANETS . ' AS p
                INNER JOIN ' . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id` SET
                    {$data['ships']}
                    `planet_metal` = `planet_metal` + '" . $data['resources']['metal'] . "',
                    `planet_crystal` = `planet_crystal` + '" . $data['resources']['crystal'] . "',
                    `planet_deuterium` = `planet_deuterium` + '" . $data['resources']['deuterium'] . "'
                WHERE `planet_galaxy` = '" . $data['coords']['galaxy'] . "' AND
                    `planet_system` = '" . $data['coords']['system'] . "' AND
                    `planet_planet` = '" . $data['coords']['planet'] . "' AND
                    `planet_type` = '" . $data['coords']['type'] . "'"
            );
        }
    }

    public function updatePlanetResourcesByCoords(array $data = []): void
    {
        if (is_array($data)) {
            $this->db->query(
                'UPDATE ' . PLANETS . " SET
                    `planet_metal` = `planet_metal` + '" . $data['resources']['metal'] . "',
                    `planet_crystal` = `planet_crystal` + '" . $data['resources']['crystal'] . "',
                    `planet_deuterium` = `planet_deuterium` + '" . $data['resources']['deuterium'] . "'
                WHERE `planet_galaxy` = '" . $data['coords']['galaxy'] . "' AND
                    `planet_system` = '" . $data['coords']['system'] . "' AND
                    `planet_planet` = '" . $data['coords']['planet'] . "' AND
                    `planet_type` = '" . $data['coords']['type'] . "'
                LIMIT 1;"
            );
        }
    }

    public function getAllPlanetDataByCoords(array $data = []): array
    {
        if (is_array($data)) {
            return $this->db->queryFetch(
                'SELECT *
                FROM `' . PLANETS . '` AS p
                LEFT JOIN `' . BUILDINGS . '` AS b ON b.building_planet_id = p.`planet_id`
                LEFT JOIN `' . DEFENSES . '` AS d ON d.defense_planet_id = p.`planet_id`
                LEFT JOIN `' . SHIPS . "` AS s ON s.ship_planet_id = p.`planet_id`
                WHERE `planet_galaxy` = '" . (int) $data['coords']['galaxy'] . "' AND
                    `planet_system` = '" . (int) $data['coords']['system'] . "' AND
                    `planet_planet` = '" . (int) $data['coords']['planet'] . "' AND
                    `planet_type` = '" . (int) $data['coords']['type'] . "'
                LIMIT 1;"
            );
        }

        return [];
    }

    /**
     * Get all user data by user ID
     *
     * @param int $userId User ID
     *
     * @return array
     */
    public function getAllUserDataByUserId(int $userId)
    {
        if ((int) $userId > 0) {
            return $this->db->queryFetch(
                'SELECT u.*,
                    r.*,
                    pr.*,
                    pref.preference_vacation_mode
                FROM `' . USERS . '` AS u
                INNER JOIN `' . RESEARCH . '` AS r ON r.research_user_id = u.user_id
                INNER JOIN `' . PREMIUM . '` AS pr ON pr.premium_user_id = u.user_id
                INNER JOIN `' . PREFERENCES . "` AS pref ON pref.preference_user_id = u.user_id
                WHERE u.`user_id` = '" . $userId . "'
                LIMIT 1;"
            );
        }

        return [];
    }

    /**
     * Delete ACS fleet by ID
     *
     * @param int $fleet_group_id Fleet group ID
     *
     * @return void
     */
    public function deleteAcsFleetById($fleet_group_id)
    {
        if ((int) $fleet_group_id > 0) {
            $this->db->query(
                'DELETE FROM `' . ACS . "`
                WHERE `acs_id` = '" . $fleet_group_id . "'"
            );
        }
    }

    /**
     * Update ACS fleet status by ID
     *
     * @param string $fleet_group_id Fleet group
     *
     * @return void
     */
    public function updateAcsFleetStatusByGroupId($fleet_group_id)
    {
        if ((int) $fleet_group_id > 0) {
            $this->db->query(
                'UPDATE `' . FLEETS . "` SET
                    `fleet_mess` = '1'
                WHERE `fleet_group` = '" . $fleet_group_id . "'"
            );
        }
    }

    /**
     * Get all fleets by ACS fleet ID
     *
     * @param int $fleet_group_id
     *
     * @return array
     */
    public function getAllAcsFleetsByGroupId(int $fleet_group_id)
    {
        if ((int) $fleet_group_id > 0) {
            return $this->db->queryFetchAll(
                'SELECT
                    f.*,
                    r.`research_hyperspace_technology`
                FROM `' . FLEETS . '` f
                LEFT JOIN `' . RESEARCH . "` r
                    ON r.`research_user_id` = f.`fleet_owner`
                WHERE f.`fleet_group` = '" . $fleet_group_id . "';"
            );
        }

        return null;
    }

    /**
     * Get all fleets by end coordinates, start time and stay time
     *
     * @param array $data Data to get the fleets
     *
     * @return array
     */
    public function getAllFleetsByEndCoordsAndTimes(array $data = [])
    {
        if (is_array($data)) {
            return $this->db->queryFetchAll(
                'SELECT *
                FROM `' . FLEETS . "`
                WHERE `fleet_end_galaxy` = '" . (int) $data['coords']['galaxy'] . "' AND
                    `fleet_end_system` = '" . (int) $data['coords']['system'] . "' AND
                    `fleet_end_planet` = '" . (int) $data['coords']['planet'] . "' AND
                    `fleet_end_type` = '" . (int) $data['coords']['type'] . "' AND
                    `fleet_start_time` < '" . $data['time'] . "' AND
                    `fleet_end_stay` >= '" . $data['time'] . "';"
            );
        }

        return [];
    }

    /**
     * Update planet debris by coordinates
     *
     * @param array $data Data to run the query
     *
     * @return void
     */
    public function updatePlanetDebrisByCoords(array $data = [])
    {
        if (is_array($data)) {
            $this->db->query(
                'UPDATE ' . PLANETS . " SET
                    `planet_invisible_start_time` = '" . $data['time'] . "',
                    `planet_debris_metal` = `planet_debris_metal` + '" . $data['debris']['metal'] . "',
                    `planet_debris_crystal` = `planet_debris_crystal` + '" . $data['debris']['crystal'] . "'
                WHERE `planet_galaxy` = '" . (int) $data['coords']['galaxy'] . "' AND
                    `planet_system` = '" . (int) $data['coords']['system'] . "' AND
                    `planet_planet` = '" . (int) $data['coords']['planet'] . "' AND
                    `planet_type` = 1
                LIMIT 1;"
            );
        }
    }

    /**
     * Get user technologies by the provided user ID
     *
     * @param int $userId User ID
     *
     * @return array
     */
    public function getTechnologiesByUserId($userId)
    {
        if ((int) $userId > 0) {
            return $this->db->queryFetch(
                'SELECT u.user_name,
                    r.research_weapons_technology,
                    r.research_shielding_technology,
                    r.research_armour_technology,
                    r.research_hyperspace_technology
                FROM ' . USERS . ' AS u
                    INNER JOIN `' . RESEARCH . "` AS r
                        ON r.research_user_id = u.user_id
                WHERE u.user_id = '" . $userId . "';"
            );
        }
    }

    /**
     * Get moon id by coords
     *
     * @param array $data Moon coords
     *
     * @return array
     */
    public function getMoonIdByCoords(array $data = [])
    {
        if (is_array($data)) {
            return $this->db->queryFetch(
                'SELECT `planet_id`
                FROM `' . PLANETS . "`
                WHERE `planet_galaxy` = '" . $data['coords']['galaxy'] . "'
                    AND `planet_system` = '" . $data['coords']['system'] . "'
                    AND `planet_planet` = '" . $data['coords']['planet'] . "'
                    AND `planet_type` = '3';"
            );
        }

        return [];
    }

    /**
     * Insert a new record in the reports table
     *
     * @param array $data Report data
     *
     * @return void
     */
    public function insertReport(array $data = [])
    {
        if (is_array($data)) {
            $this->db->query(
                'INSERT INTO `' . REPORTS . "` SET
                `report_owners` = '" . $data['owners'] . "',
                `report_rid` = '" . $data['rid'] . "',
                `report_content` = '" . $data['content'] . "',
                `report_destroyed` = '" . $data['destroyed'] . "',
                `report_time` = '" . $data['time'] . "'"
            );
        }
    }

    /**
     * Update returning fleet steal resources
     *
     * @param array $data
     *
     * @return void
     */
    public function updateReturningFleetData(array $data = [])
    {
        if (is_array($data)) {
            $this->db->query(
                'UPDATE `' . FLEETS . "` SET
                `fleet_array` = '" . $data['ships'] . "',
                `fleet_amount` = '" . $data['amount'] . "',
                `fleet_mess` = '1',
                `fleet_resource_metal` = `fleet_resource_metal` + '" . $data['stolen']['metal'] . "' ,
                `fleet_resource_crystal` = `fleet_resource_crystal` + '" . $data['stolen']['crystal'] . "' ,
                `fleet_resource_deuterium` = `fleet_resource_deuterium` + '" . $data['stolen']['deuterium'] . "'
                WHERE `fleet_id` = '" . $data['fleet_id'] . "';"
            );
        }
    }

    /**
     * Delete multiple fleets by a set of provided ids
     *
     * @param string $id_string String of IDS
     *
     * @return void
     */
    public function deleteMultipleFleetsByIds($id_string)
    {
        $this->db->query(
            'DELETE FROM `' . FLEETS . '`
            WHERE `fleet_id` IN (' . $id_string . ')'
        );
    }

    /**
     * Update planet losses by Id
     *
     * @param array $data Data to update
     *
     * @return void
     */
    public function updatePlanetLossesById(array $data = [])
    {
        if (is_array($data)) {
            // Updating defenses and ships on planet
            $this->db->query(
                'UPDATE `' . PLANETS . '`, `' . SHIPS . '`, `' . DEFENSES . '`  SET
                ' . $data['ships'] . '
                `planet_metal` = `planet_metal` -  ' . $data['stolen']['metal'] . ',
                `planet_crystal` = `planet_crystal` -  ' . $data['stolen']['crystal'] . ',
                `planet_deuterium` = `planet_deuterium` -  ' . $data['stolen']['deuterium'] . "
                WHERE `planet_id` = '" . $data['planet_id'] . "' AND
                    `ship_planet_id` = '" . $data['planet_id'] . "' AND
                    `defense_planet_id` = '" . $data['planet_id'] . "'"
            );
        }
    }

    /**
     * Get planet and user data before colonization can take place
     *
     * @param array $data User and Planet data
     *
     * @return array
     */
    public function getPlanetAndUserCountsCounts(array $data = [])
    {
        if (is_array($data)) {
            return $this->db->queryFetch(
                'SELECT
                    (SELECT COUNT(*)
                            FROM ' . PLANETS . " AS pc1
                            WHERE pc1.`planet_user_id` = '" . $data['user_id'] . "' AND
                                            pc1.`planet_type` = '1' AND
                                            pc1.`planet_destroyed` = '0') AS planet_count,
                    (SELECT COUNT(*)
                            FROM " . PLANETS . " AS pc2
                            WHERE pc2.`planet_galaxy` = '" . $data['coords']['galaxy'] . "' AND
                                            pc2.`planet_system` = '" . $data['coords']['system'] . "' AND
                                            pc2.`planet_planet` = '" . $data['coords']['planet'] . "' AND
                                            pc2.`planet_type` = '1') AS galaxy_count,
                    (SELECT `research_astrophysics`
                            FROM " . RESEARCH . "
                            WHERE `research_user_id` = '" . $data['user_id'] . "') AS astro_level"
            );
        }

        return [];
    }

    /**
     * Get friendly planet data
     *
     * @param array $data Data to update
     *
     * @return void
     */
    public function getFriendlyPlanetData(array $data = [])
    {
        if (is_array($data)) {
            return $this->db->queryFetch(
                'SELECT
                    pc1.`planet_user_id` AS `start_id`,
                    pc1.`planet_name` AS `start_name`,
                    pc2.`planet_user_id` AS `target_id`,
                    pc2.`planet_name` AS `target_name`,
                    pc2.`planet_metal` AS `target_metal`,
                    pc2.`planet_crystal` AS `target_crystal`,
                    pc2.`planet_deuterium` AS `target_deuterium`,
                    u.`user_name` AS `start_user_name`
                FROM `' . PLANETS . '` AS pc1 JOIN `' . PLANETS . '` AS pc2
                LEFT JOIN `' . USERS . "` AS u
                    ON u.`user_id` = pc1.`planet_user_id`
                WHERE pc1.planet_galaxy = '" . $data['coords']['start']['galaxy'] . "' AND
                    pc1.`planet_system` = '" . $data['coords']['start']['system'] . "' AND
                    pc1.`planet_planet` = '" . $data['coords']['start']['planet'] . "' AND
                    pc1.`planet_type` = '" . $data['coords']['start']['type'] . "' AND
                    pc2.`planet_galaxy` = '" . $data['coords']['end']['galaxy'] . "' AND
                    pc2.`planet_system` = '" . $data['coords']['end']['system'] . "' AND
                    pc2.`planet_planet` = '" . $data['coords']['end']['planet'] . "' AND
                    pc2.`planet_type` = '" . $data['coords']['end']['type'] . "'"
            );
        }
    }

    public function updateLostShipsAndDefensePoints(int $playerId, array $lost): void
    {
        $shipPoints = 0;
        $defensePoints = 0;

        foreach ($lost as $unit => $lostCount) {
            if ($unit >= 401) {
                $defensePoints += StatisticsLibrary::calculatePoints($unit, 1) * $lostCount;
            } else {
                $shipPoints += StatisticsLibrary::calculatePoints($unit, 1) * $lostCount;
            }
        }

        $this->db->query('
            UPDATE `' . USERS_STATISTICS . "` AS us SET
                us.`user_statistic_ships_points` = us.`user_statistic_ships_points` - '" . $shipPoints . "' ,
                us.`user_statistic_defenses_points` = us.`user_statistic_defenses_points` - '" . $defensePoints . "'
            WHERE us.`user_statistic_user_id` = '" . $playerId . "'
        ");
    }

    /**
     *
     * COLONIZATION
     *
     */

    /**
     * Updates the points after the colonization took place
     *
     * @param array $data Data to update
     *
     * @return void
     */
    public function updateColonizationStatistics(array $data = [])
    {
        if (is_array($data)) {
            $this->db->query(
                'UPDATE ' . USERS_STATISTICS . ' AS us SET
                us.`user_statistic_ships_points` = us.`user_statistic_ships_points` - ' . $data['points'] . '
                WHERE us.`user_statistic_user_id` = (
                    SELECT p.planet_user_id FROM ' . PLANETS . " AS p
                    WHERE p.planet_galaxy = '" . $data['coords']['galaxy'] . "' AND
                        p.planet_system = '" . $data['coords']['system'] . "' AND
                        p.planet_planet = '" . $data['coords']['planet'] . "' AND
                        p.planet_type = '" . $data['coords']['type'] . "'
                );"
            );
        }
    }

    /**
     * Updates the fleet array and points by fleet id and coords
     *
     * @param array $data Data to update
     *
     * @return void
     */
    public function updateColonizatonReturningFleet(array $data = [])
    {
        if (is_array($data)) {
            $this->db->query(
                'UPDATE ' . FLEETS . ', ' . USERS_STATISTICS . " SET
                `fleet_array` = '" . $data['ships'] . "',
                `fleet_amount` = `fleet_amount` - 1,
                `fleet_resource_metal` = '0',
                `fleet_resource_crystal` = '0',
                `fleet_resource_deuterium` = '0',
                `fleet_mess` = '1',
                `user_statistic_ships_points` = `user_statistic_ships_points` - " . $data['points'] . "
                WHERE `fleet_id` = '" . $data['fleet_id'] . "' AND
                    `user_statistic_user_id` = (
                    SELECT planet_user_id FROM " . PLANETS . "
                    WHERE planet_galaxy = '" . $data['coords']['galaxy'] . "' AND
                        planet_system = '" . $data['coords']['system'] . "' AND
                        planet_planet = '" . $data['coords']['planet'] . "' AND
                        planet_type = '" . $data['coords']['type'] . "'
                );"
            );
        }
    }
    /**
     *
     * DESTROY
     *
     */

    /**
     * Get destroyer data
     *
     * @param array $data Data to update
     *
     * @return array
     */
    public function getDestroyerData(array $data = [])
    {
        if (is_array($data)) {
            return $this->db->queryFetch(
                'SELECT
                    p.planet_name,
                    r.research_weapons_technology,
                    r.research_shielding_technology,
                    r.research_armour_technology,
                    u.user_name,
                    u.user_id
                FROM ' . PLANETS . ' AS p
                INNER JOIN ' . USERS . ' AS u ON u.user_id = p.planet_user_id
                INNER JOIN ' . PREMIUM . ' AS pr ON pr.premium_user_id = p.planet_user_id
                INNER JOIN ' . RESEARCH . ' AS r ON r.research_user_id = p.planet_user_id
                WHERE p.`planet_galaxy` = ' . $data['coords']['galaxy'] . ' AND
                                p.`planet_system` = ' . $data['coords']['system'] . ' AND
                                p.`planet_planet` = ' . $data['coords']['planet'] . ' AND
                                p.`planet_type` = ' . $data['coords']['type'] . ';'
            );
        }

        return [];
    }

    /**
     * Get target to destroy data
     *
     * @param array $data Data to update
     *
     * @return array
     */
    public function getTargetToDestroyData(array $data = [])
    {
        if (is_array($data)) {
            return $this->db->queryFetch(
                'SELECT
                    s.*,
                    d.*,
                    p.`planet_id`,
                    p.planet_diameter,
                    p.planet_user_id,
                    u.user_name,
                    u.user_current_planet,
                    r.research_weapons_technology,
                    r.research_shielding_technology,
                    r.research_armour_technology
                FROM ' . PLANETS . ' AS p
                INNER JOIN ' . SHIPS . ' AS s ON s.ship_planet_id = p.`planet_id`
                INNER JOIN ' . DEFENSES . ' AS d ON d.defense_planet_id = p.`planet_id`
                INNER JOIN ' . USERS . ' AS u ON u.user_id = p.planet_user_id
                INNER JOIN ' . PREMIUM . ' AS pr ON pr.premium_user_id = p.planet_user_id
                INNER JOIN ' . RESEARCH . " AS r ON r.research_user_id = p.planet_user_id
                WHERE p.`planet_galaxy` = '" . $data['coords']['galaxy'] . "' AND
                                p.`planet_system` = '" . $data['coords']['system'] . "' AND
                                p.`planet_planet` = '" . $data['coords']['planet'] . "' AND
                                p.`planet_type` = '" . $data['coords']['type'] . "';"
            );
        }

        return [];
    }

    /**
     *
     *
     * @param array $data Data to update
     *
     * @return void
     */
    public function updateFleetsStatusToMakeThemReturn(array $data = [])
    {
        if (is_array($data)) {
            $this->db->queryMulty(
                'UPDATE `' . FLEETS . "` AS f SET
                    f.`fleet_start_type` = '1'
                WHERE f.`fleet_start_galaxy` = '" . $data['coords']['galaxy'] . "'
                    AND f.`fleet_start_system` = '" . $data['coords']['system'] . "'
                    AND f.`fleet_start_planet` = '" . $data['coords']['planet'] . "';
                UPDATE `" . FLEETS . "` AS f SET
                    f.`fleet_end_type` = '1'
                WHERE f.`fleet_end_galaxy` = '" . $data['coords']['galaxy'] . "'
                    AND f.`fleet_end_system` = '" . $data['coords']['system'] . "'
                    AND f.`fleet_end_planet` = '" . $data['coords']['planet'] . "';
                UPDATE `" . PLANETS . "` AS p SET
                    `planet_destroyed` = '" . $data['time'] . "'
                WHERE p.`planet_id` = '" . $data['planet_id'] . "';"
            );
        }
    }

    /**
     * Update user current planet, to avoid that they get stuck on a deleted moon
     *
     * @param array $data Data to update
     *
     * @return void
     */
    public function updateUserCurrentPlanetByCoordsAndUserId(array $data = [])
    {
        if (is_array($data)) {
            $this->db->query(
                'UPDATE ' . USERS . ' SET
                    `user_current_planet` = (
                        SELECT `planet_id`
                        FROM ' . PLANETS . "
                        WHERE `planet_galaxy` = '" . $data['coords']['fleet_end_galaxy'] . "' AND
                            `planet_system` = '" . $data['coords']['fleet_end_system'] . "' AND
                            `planet_planet` = '" . $data['coords']['fleet_end_planet'] . "' AND
                            `planet_type` = '1')
                WHERE `user_id` = '" . $data['planet_user_id'] . "';"
            );
        }
    }

    /**
     * Update planet data after its destruction
     *
     * @param array $data Data to update
     *
     * @return void
     */
    public function updatePlanetDataAfterDestruction(array $data = [])
    {
        if (is_array($data)) {
            $this->db->query(
                'UPDATE ' . PLANETS . ' AS p
                INNER JOIN ' . SHIPS . ' AS s ON s.ship_planet_id = p.`planet_id`
                INNER JOIN ' . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id` SET
                {$data['data_to_update']}
                `planet_invisible_start_time` = '" . $data['time'] . "',
                `planet_debris_metal` = `planet_debris_metal` + '" . $data['debris']['metal'] . "',
                `planet_debris_crystal` = `planet_debris_crystal` + '" . $data['debris']['crystal'] . "'
                WHERE `planet_galaxy` = '" . $data['coords']['galaxy'] . "' AND
                    `planet_system` = '" . $data['coords']['system'] . "' AND
                    `planet_planet` = '" . $data['coords']['planet'] . "' AND
                    `planet_type` = '" . $data['coords']['type'] . "';"
            );
        }
    }

    /**
     * Update destroy fleet data and make it return
     *
     * @param array $data Data to update
     *
     * @return void
     */
    public function updateFleetDataToReturn(array $data): void
    {
        if (is_array($data)) {
            $this->db->query(
                'UPDATE ' . FLEETS . " SET
                `fleet_amount` = '" . $data['amount'] . "',
                `fleet_array` = '" . FleetsLib::setFleetShipsArray($data['ships']) . "',
                `fleet_mess` = '1'
                WHERE fleet_id = '" . (int) $data['fleet_id'] . "';"
            );
        }
    }

    /**
     *
     * EXPEDITION
     *
     */
    public function updateFleetArrayById(array $data = []): void
    {
        if (is_array($data)) {
            $this->db->query(
                'UPDATE ' . FLEETS . " SET
                `fleet_array` = '" . $data['ships'] . "',
                `fleet_mess` = '1'
                WHERE `fleet_id` = '" . (int) $data['fleet_id'] . "';"
            );
        }
    }

    public function updateFleetResourcesById(int $fleetId, string $resource, int $amount): void
    {
        $this->db->query(
            'UPDATE ' . FLEETS . " AS f SET
            `fleet_resource_' . $resource . '` = `fleet_resource_' . $resource . '` + '" . $amount . "',
            `fleet_mess` = '1'
            WHERE `fleet_id` = '" . $fleetId . "';"
        );
    }

    public function updateDarkMatter(int $userId, int $darkMatter): void
    {
        $this->db->query(
            'UPDATE ' . PREMIUM . " AS p SET
            `premium_dark_matter` = `premium_dark_matter` + '" . $darkMatter . "'
            WHERE `premium_user_id` = '" . $userId . "';"
        );
    }

    public function getTopPlayerPoints(): int
    {
        return $this->db->queryFetch(
            'SELECT MAX(us.user_statistic_total_points) AS total FROM `xgp_users_statistics` us;'
        )['total'];
    }

    public function updateFleetEndTime(int $fleetId, int $fleetEndTime): void
    {
        $this->db->query(
            'UPDATE ' . FLEETS . " AS f SET
            `fleet_end_time` = '" . $fleetEndTime . "',
            `fleet_mess` = '1'
            WHERE `fleet_id` = '" . $fleetId . "';"
        );
    }

    /**
     *
     * MISSILE
     *
     */

    /**
     * Get all missiles attacker data by coords
     *
     * @param array $data Coords
     *
     * @return void
     */
    public function getMissileAttackerDataByCoords(array $data = [])
    {
        if (is_array($data)) {
            return $this->db->queryFetch(
                'SELECT p.`planet_name`, r.`research_weapons_technology`
                FROM ' . PLANETS . ' AS p
                INNER JOIN ' . RESEARCH . ' AS r ON r.research_user_id = p.planet_user_id
                WHERE `planet_galaxy` = ' . $data['coords']['galaxy'] . ' AND
                    `planet_system` = ' . $data['coords']['system'] . ' AND
                    `planet_planet` = ' . $data['coords']['planet'] . ' AND
                    `planet_type` = ' . $data['coords']['type'] . ';'
            );
        }
    }

    /**
     * Get all missiles target data by coords
     *
     * @param array $data Coords
     *
     * @return void
     */
    public function getMissileTargetDataByCoords(array $data = [])
    {
        if (is_array($data)) {
            return $this->db->queryFetch(
                'SELECT p.`planet_id`, p.`planet_name`, p.`planet_user_id`, d.*, r.`research_shielding_technology`
                FROM ' . PLANETS . ' AS p
                INNER JOIN ' . DEFENSES . ' AS d ON d.defense_planet_id = p.`planet_id`
                INNER JOIN ' . RESEARCH . ' AS r ON r.research_user_id = p.planet_user_id
                WHERE `planet_galaxy` = ' . $data['coords']['galaxy'] . ' AND
                    `planet_system` = ' . $data['coords']['system'] . ' AND
                    `planet_planet` = ' . $data['coords']['planet'] . ' AND
                    `planet_type` = ' . $data['coords']['type'] . ';'
            );
        }
    }

    /**
     * Update planet target defenses based on the attack result
     *
     * @param array $data Data to update
     *
     * @return void
     */
    public function updatePlanetDefenses(array $data = [])
    {
        if (is_array($data)) {
            $this->db->query(
                'UPDATE ' . DEFENSES . " SET
                {$data['destroyed_query']}
                `defense_anti-ballistic_missile` = '" . $data['amount'] . "'
                WHERE defense_planet_id = '" . (int) $data['planet_id'] . "';"
            );
        }
    }
    /**
     *
     * RECYCLE
     *
     */

    /**
     * Update planet debris field and make the fleet return
     *
     * @param array $data Data to update
     *
     * @return void
     */
    public function updatePlanetDebrisFieldAndFleet(array $data = [])
    {
        if (is_array($data)) {
            $this->db->query(
                'UPDATE ' . PLANETS . ', ' . FLEETS . " SET
                `planet_debris_metal` = `planet_debris_metal` - '" . $data['recycled']['metal'] . "',
                `planet_debris_crystal` = `planet_debris_crystal` - '" . $data['recycled']['crystal'] . "',
                `fleet_resource_metal` = `fleet_resource_metal` + '" . $data['recycled']['metal'] . "',
                `fleet_resource_crystal` = `fleet_resource_crystal` + '" . $data['recycled']['crystal'] . "',
                `fleet_mess` = '1'
                WHERE `planet_galaxy` = '" . $data['coords']['galaxy'] . "' AND
                    `planet_system` = '" . $data['coords']['system'] . "' AND
                    `planet_planet` = '" . $data['coords']['planet'] . "' AND
                    `planet_type` = 1 AND
                    `fleet_id` = '" . (int) $data['fleet_id'] . "'"
            );
        }
    }

    /**
     * Update planet debris field and make the fleet return
     *
     * @param array $data Data to update
     *
     * @return void
     */
    public function getPlanetDebris(array $data = [])
    {
        if (is_array($data)) {
            return $this->db->queryFetch(
                'SELECT
                    `planet_name` AS target_name,
                    `planet_debris_metal`,
                    `planet_debris_crystal`
                FROM `' . PLANETS . "`
                WHERE `planet_galaxy` = '" . $data['coords']['galaxy'] . "' AND
                    `planet_system` = '" . $data['coords']['system'] . "' AND
                    `planet_planet` = '" . $data['coords']['planet'] . "' AND
                    `planet_type` = 1
                LIMIT 1;"
            );
        }

        return [];
    }
    /**
     *
     * SPY
     *
     */

    /**
     * Get user data that's going to start the spy process
     *
     * @param array $data Data to update
     *
     * @return array
     */
    public function getSpyUserDataByCords(array $data = [])
    {
        if (is_array($data)) {
            return $this->db->queryFetch(
                'SELECT
                    p.`planet_name`,
                    p.`planet_galaxy`,
                    p.`planet_system`,
                    p.`planet_planet`,
                    u.`user_name`,
                    r.`research_espionage_technology`,
                    pr.`premium_officier_technocrat`
                    FROM `' . PLANETS . '` AS p
                    INNER JOIN `' . USERS . '` AS u ON u.`user_id` = p.`planet_user_id`
                    INNER JOIN `' . PREMIUM . '` AS pr ON pr.`premium_user_id` = p.`planet_user_id`
                    INNER JOIN `' . RESEARCH . '` AS r ON r.`research_user_id` = p.`planet_user_id`
                    WHERE p.`planet_galaxy` = ' . $data['coords']['galaxy'] . ' AND
                        p.`planet_system` = ' . $data['coords']['system'] . ' AND
                        p.`planet_planet` = ' . $data['coords']['planet'] . ' AND
                        p.`planet_type` = ' . $data['coords']['type'] . ';'
            );
        }

        return [];
    }

    /**
     * Get user data that's going to be inquired (spied)
     *
     * @param array $data Data to update
     *
     * @return array
     */
    public function getInquiredUserDataByCords(array $data = [])
    {
        if (is_array($data)) {
            return $this->db->queryFetch(
                'SELECT
                    p.`planet_id`,
                    p.`planet_user_id`,
                    p.`planet_name`,
                    p.`planet_galaxy`,
                    p.`planet_system`,
                    p.`planet_planet`,
                    p.planet_metal,
                    p.`planet_crystal`,
                    p.`planet_deuterium`,
                    p.`planet_energy_max`,
                    s.*, d.*, b.*, r.*,
                    pr.`premium_officier_technocrat`
                    FROM `' . PLANETS . '` AS p
                    INNER JOIN `' . SHIPS . '` AS s ON s.`ship_planet_id` = p.`planet_id`
                    INNER JOIN `' . DEFENSES . '` AS d ON d.`defense_planet_id` = p.`planet_id`
                    INNER JOIN `' . BUILDINGS . '` AS b ON b.`building_planet_id` = p.`planet_id`
                    INNER JOIN `' . USERS . '` AS u ON u.`user_id` = p.`planet_user_id`
                    INNER JOIN `' . PREMIUM . '` AS pr ON pr.`premium_user_id` = p.`planet_user_id`
                    INNER JOIN `' . RESEARCH . "` AS r ON r.`research_user_id` = p.`planet_user_id`
                    WHERE p.`planet_galaxy` = '" . $data['coords']['galaxy'] . "' AND
                        p.`planet_system` = '" . $data['coords']['system'] . "' AND
                        p.`planet_planet` = '" . $data['coords']['planet'] . "' AND
                        p.`planet_type` = '" . $data['coords']['type'] . "';"
            );
        }

        return [];
    }

    /**
     * Update planet target defenses based on the attack result
     *
     * @param array $data Data to update
     *
     * @return void
     */
    public function updateCrystalDebrisByPlanetId(array $data = [])
    {
        if (is_array($data)) {
            $this->db->query(
                'UPDATE ' . PLANETS . " SET
                `planet_invisible_start_time` = '" . $data['time'] . "',
                `planet_debris_crystal` = `planet_debris_crystal` + '" . $data['crystal'] . "'
                WHERE `planet_id` = '" . $data['planet_id'] . "';"
            );
        }
    }

    /**
     *
     * TRANSPORT
     *
     */
    public function updateReturningFleetResources(int $fleedId = 0): void
    {
        if ($fleedId > 0) {
            $this->db->query(
                'UPDATE ' . FLEETS . " SET
                    `fleet_resource_metal` = '0' ,
                    `fleet_resource_crystal` = '0' ,
                    `fleet_resource_deuterium` = '0' ,
                    `fleet_mess` = '1'
                WHERE `fleet_id` = '" . $fleedId . "'
                LIMIT 1 ;"
            );
        }
    }
}
