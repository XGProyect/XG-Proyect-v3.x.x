<?php

namespace App\Models\Adm;

use App\Core\Model;
use App\Libraries\Functions;

class Users extends Model
{
    public function checkUser(string $user): array
    {
        return $this->db->queryFetch(
            'SELECT
                `user_id`, `user_authlevel`
            FROM `' . USERS . "`
            WHERE
                `user_name` = '" . $user . "'
            OR
                `user_email` = '" . $user . "';"
        ) ?? [];
    }

    public function getUserDataById(int $user_id): array
    {
        return $this->db->queryFetch(
            'SELECT u.*,
                    p.*,
                    pr.*,
                    r.*
            FROM `' . USERS . '` AS u
                INNER JOIN `' . PREFERENCES . '` AS pr ON pr.`preference_user_id` = u.`user_id`
                INNER JOIN `' . PREMIUM . '` AS p ON p.`premium_user_id` = u.`user_id`
                INNER JOIN `' . RESEARCH . "` AS r ON r.`research_user_id` = u.`user_id`
            WHERE (u.user_id = '" . $user_id . "')
            LIMIT 1;"
        ) ?? [];
    }

    public function getAllPlanetsByUserId(int $user_id): array
    {
        return $this->db->queryFetchAll(
            'SELECT
                    `planet_id`,
                    `planet_name`,
                    `planet_galaxy`,
                    `planet_system`,
                    `planet_planet`
            FROM `' . PLANETS . "`
            WHERE `planet_user_id` = '" . $user_id . "';"
        ) ?? [];
    }

    public function getAllAlliances(): array
    {
        return $this->db->queryFetchAll(
            'SELECT
                `alliance_id`,
                `alliance_name`,
                `alliance_tag`
            FROM `' . ALLIANCE . '`;'
        ) ?? [];
    }

    public function checkUsername(string $username, int $user_id): array
    {
        return $this->db->queryFetch(
            'SELECT `user_id`
            FROM `' . USERS . "`
            WHERE `user_name` = '" . $username . "' AND
                    `user_id` <> '" . $user_id . "';"
        ) ?? [];
    }

    public function checkEmail(string $email, int $user_id): array
    {
        return $this->db->queryFetch(
            'SELECT `user_id`
            FROM `' . USERS . "`
            WHERE `user_email` = '" . $email . "' AND
                `user_id` <> '" . $user_id . "';"
        ) ?? [];
    }

    public function deleteSessionByUserId(int $user_id): void
    {
        $this->db->query(
            'DELETE FROM `' . SESSIONS . "`
            WHERE `session_data`
            LIKE '%user_id|s:1:\"" . $user_id . "\"%'"
        );
    }

    public function getAllPlanetsData(int $user_id, int $planet_id = 0, string $edit = ''): array
    {
        $sub_query = '';

        switch ($edit) {
            case 'planet':
                $get_query = 'p.* ';
                break;

            case 'buildings':
                $get_query = 'b.* ';
                break;

            case 'ships':
                $get_query = 's.* ';
                break;

            case 'defenses':
                $get_query = 'd.* ';
                break;

            case '':
            default:
                $get_query = 'p.*, b.*, d.*, s.*,
                    m.planet_id AS moon_id,
                    m.planet_name AS moon_name,
                    m.planet_image AS moon_image,
                    m.planet_destroyed AS moon_destroyed ';

                break;
        }

        if ($planet_id > 0) {
            $sub_query = ' AND p.`planet_id` = ' . $planet_id;
        }

        return $this->db->queryFetchAll(
            "SELECT {$get_query}
            FROM `" . PLANETS . '` AS p
            INNER JOIN `' . BUILDINGS . '` AS b ON b.`building_planet_id` = p.`planet_id`
            INNER JOIN `' . DEFENSES . '` AS d ON d.`defense_planet_id` = p.`planet_id`
            INNER JOIN `' . SHIPS . '` AS s ON s.`ship_planet_id` = p.`planet_id`
            LEFT JOIN `' . PLANETS . '` AS m ON m.`planet_id` = (SELECT mp.`planet_id`
            FROM `' . PLANETS . "` AS mp
            WHERE (mp.`planet_galaxy` = p.`planet_galaxy` AND
                mp.`planet_system` = p.`planet_system` AND
                mp.`planet_planet` = p.`planet_planet` AND
                mp.`planet_type` = 3))
            WHERE p.`planet_user_id` = '" . $user_id . "'
                AND p.`planet_type` = 1{$sub_query};"
        ) ?? [];
    }

    public function getAllMoonsData(int $user_id, int $moon_id = 0, string $edit = ''): array
    {
        $sub_query = '';

        switch ($edit) {
            case 'moon':
                $get_query = 'm.* ';
                break;

            case 'buildings':
                $get_query = 'b.* ';
                break;

            case 'ships':
                $get_query = 's.* ';
                break;

            case 'defenses':
                $get_query = 'd.* ';
                break;

            case '':
            default:
                $get_query = 'm.*, b.*, d.*, s.*';
                break;
        }

        if ($moon_id > 0) {
            $sub_query = ' AND m.`planet_id` = ' . $moon_id;
        }

        return $this->db->queryFetchAll(
            "SELECT {$get_query}
            FROM `" . PLANETS . '` AS m
            INNER JOIN `' . BUILDINGS . '` AS b ON b.`building_planet_id` = m.`planet_id`
            INNER JOIN `' . DEFENSES . '` AS d ON d.`defense_planet_id` = m.`planet_id`
            INNER JOIN `' . SHIPS . "` AS s ON s.`ship_planet_id` = m.`planet_id`
            WHERE m.`planet_user_id` = '" . $user_id . "'
                            AND m.`planet_type` = 3{$sub_query};"
        ) ?? [];
    }

    public function getAllUsers(): array
    {
        return $this->db->queryFetchAll(
            'SELECT
                `user_id`,
                `user_name`
            FROM `' . USERS . '`;'
        ) ?? [];
    }

    /**
     *
     * SAVE DATA METHODS
     *
     */
    public function saveUserData(array $data): void
    {
        $this->db->query(
            'UPDATE `' . USERS . "` SET
                `user_name` = '" . $data['username'] . "',
                `user_password` = " . $data['password'] . ",
                `user_email` = '" . $data['email'] . "',
                `user_authlevel` = '" . $data['authlevel'] . "',
                `user_home_planet_id` = '" . $data['id_planet'] . "',
                `user_current_planet` = '" . $data['cur_planet'] . "',
                `user_ally_id` = '" . $data['ally_id'] . "'
                WHERE `user_id` = '" . $data['id'] . "';"
        );
    }

    public function saveUserPreferences(array $post, int $user_id, array $current_user): void
    {
        $vacation_head = '';
        $vacation_condition = '';
        $vacation_query = '';
        $vacation_time = Functions::getDefaultVacationTime(); // DEFAULT VACATION TIME BEFORE A USER CAN REMOVE IT

        $preference_planet_sort = ((isset($post['preference_planet_sort'])) ? (int) $post['preference_planet_sort'] : 0);
        $preference_planet_sort_sequence = ((isset($post['preference_planet_sort_sequence'])) ? (int) $post['preference_planet_sort_sequence'] : 0);
        $preference_spy_probes = ((isset($post['preference_spy_probes'])) ? (int) $post['preference_spy_probes'] : 0);
        $preference_vacations_status = ((isset($post['preference_vacations_status']) && $post['preference_vacations_status'] == 'on') ? 1 : 0);
        $preference_vacation_mode = ((isset($post['preference_vacations_status']) && $post['preference_vacations_status'] == 'on') ? "'" . $vacation_time . "'" : 'NULL');
        $preference_delete_mode = ((isset($post['preference_delete_mode']) && $post['preference_delete_mode'] == 'on') ? "'" . time() . "'" : 'NULL');

        // BUILD THE SPECIFIC QUERY
        if (($current_user['preference_vacation_mode'] > 0) && $preference_vacations_status == 0) {
            // WE HAVE TO REMOVE HIM FROM VACATION AND SET PLANET PRODUCTION
            $vacation_head = ' , `' . PLANETS . '` AS p';
            $vacation_condition = " AND p.`planet_user_id` = '" . (int) $user_id . "'";
            $vacation_query = "
                pr.`preference_vacation_mode` = {$preference_vacation_mode},
                p.`planet_last_update` = '" . time() . "',
                p.`planet_building_metal_mine_percent` = '10',
                p.`planet_building_crystal_mine_percent` = '10',
                p.`planet_building_deuterium_sintetizer_percent` = '10',
                p.`planet_building_solar_plant_percent` = '10',
                p.`planet_building_fusion_reactor_percent` = '10',
                p.`planet_ship_solar_satellite_percent` = '10',";
        } elseif ($current_user['preference_vacation_mode'] == 0
            or is_null($current_user['preference_vacation_mode'])
            && $preference_vacations_status == 1) {
            // WE HAVE TO ADD HIM TO VACATION AND REMOVE PLANET PRODUCTION
            $vacation_head = ' , `' . PLANETS . '` AS p';
            $vacation_condition = " AND p.`planet_user_id` = '" . (int) $user_id . "'";
            $vacation_query = "
                pr.`preference_vacation_mode` = {$preference_vacation_mode},
                p.`planet_building_metal_mine_percent` = '0',
                p.`planet_building_crystal_mine_percent` = '0',
                p.`planet_building_deuterium_sintetizer_percent` = '0',
                p.`planet_building_solar_plant_percent` = '0',
                p.`planet_building_fusion_reactor_percent` = '0',
                p.`planet_ship_solar_satellite_percent` = '0',";
        }

        $this->db->query(
            'UPDATE `' . PREFERENCES . "` AS pr{$vacation_head} SET
                {$vacation_query}
                pr.`preference_spy_probes` = '{$preference_spy_probes}',
                pr.`preference_planet_sort` = '{$preference_planet_sort}',
                pr.`preference_planet_sort_sequence` = '{$preference_planet_sort_sequence}',
                pr.`preference_delete_mode` = {$preference_delete_mode}
                WHERE pr.`preference_user_id` = '{$user_id}'{$vacation_condition}"
        );
    }

    public function saveTechnologies(array $technologies, int $user_id): void
    {
        // start
        $query_string = 'UPDATE `' . RESEARCH . '` SET ';

        // build middle
        foreach ($technologies as $tech => $level) {
            if (strpos($tech, 'research_') !== false) {
                $level = (isset($level) ? (int) $level : 0);
                $query_string .= "`{$this->db->escapeValue($tech)}` = '" . (int) $level . "',";
            }
        }

        // remove last comma
        $query_string = substr_replace($query_string, '', -1);

        // end
        $query_string .= " WHERE `research_user_id` = '" . $user_id . "';";

        // run
        $this->db->query($query_string);
    }

    public function savePremium(array $premium_data, int $user_id, array $user_query): void
    {
        // start
        $query_string = 'UPDATE `' . PREMIUM . '` SET ';

        // build middle
        foreach ($premium_data as $premium => $data) {
            // is a value from premium table
            if (strpos($premium, 'premium_') !== false) {
                // dark matter has a different behaviour
                if ($premium == 'premium_dark_matter') {
                    if (!is_numeric($data) or empty($data) or !isset($data)) {
                        $data = 0;
                    }
                } else {
                    switch ($data) {
                        default:
                        case 0:
                            $data = $user_query[$premium];
                            break;
                        case 1:
                            $data = 0;
                            break;
                        case 2:
                        case 3:
                            // set the time (3 = 3 months, 2 = one week, 1 = not active / deactivate)
                            $data = time() + ($data == 3 ? (3600 * 24 * 30 * 3) : (3600 * 24 * 7));
                            break;
                    }
                }

                $query_string .= "`{$this->db->escapeValue($premium)}` = '" . $this->db->escapeValue($data) . "',";
            }
        }

        // remove last comma
        $query_string = substr_replace($query_string, '', -1);

        // end
        $query_string .= " WHERE `premium_user_id` = '" . $this->db->escapeValue($user_id) . "';";

        // run
        $this->db->query($query_string);
    }

    public function savePlanet(array $planet_data, int $planet_id): void
    {
        // start
        $query_string = 'UPDATE `' . PLANETS . '` SET ';

        // remove unneeded field
        unset($planet_data['send_data'], $planet_data['planet_b_building_id'], $planet_data['planet_b_tech_id'], $planet_data['planet_b_hangar_id']);
        $string_type = ['planet_name', 'planet_image'];

        // build middle
        foreach ($planet_data as $field => $value) {
            switch ($field) {
                case 'planet_destroyed':
                    if ($value == 1) {
                        $query_string .= "`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',";
                    } else {
                        $query_string .= "`planet_destroyed` = '0',";
                    }
                    break;

                case 'planet_last_jump_time':
                    $query_string .= "`planet_last_jump_time` = '0',";
                    break;

                case '':
                default:
                    if (in_array($field, $string_type)) {
                        $query_string .= "`{$this->db->escapeValue($field)}` = '" . $this->db->escapeValue($value) . "',";
                    } else {
                        $query_string .= "`{$this->db->escapeValue($field)}` = '" . (int) $value . "',";
                    }
                    break;
            }
        }

        // remove last comma
        $query_string = substr_replace($query_string, '', -1);

        // end
        $query_string .= " WHERE `planet_id` = '" . $planet_id . "';";

        // run
        $this->db->query($query_string);
    }

    public function saveBuildings(array $buildings, int $planet_id): void
    {
        // start
        $query_string = 'UPDATE `' . BUILDINGS . '`, `' . PLANETS . '` SET ';
        $total_fields = 0;

        // build middle
        foreach ($buildings as $building => $level) {
            if (strpos($building, 'building_') !== false) {
                $level = (isset($level) ? (int) $level : 0);
                $query_string .= "`{$this->db->escapeValue($building)}` = '" . (int) $level . "',";
                $total_fields += $level;
            }
        }

        // end
        $query_string .= " `planet_field_current` = '" . $total_fields . "', ";
        $query_string .= ' `planet_field_max` = IF(`planet_type` = 3, 1 + `building_mondbasis` * ' . FIELDS_BY_MOONBASIS_LEVEL . ', `planet_field_max`) ';
        $query_string .= " WHERE `building_planet_id` = '" . $planet_id . "'
                            AND `planet_id` = '" . $planet_id . "';";

        // run
        $this->db->query($query_string);
    }

    public function saveShips(array $ships, int $planet_id): void
    {
        // start
        $query_string = 'UPDATE `' . SHIPS . '` SET ';

        // build middle
        foreach ($ships as $ship => $amount) {
            if (strpos($ship, 'ship_') !== false) {
                $amount = (isset($amount) ? (int) $amount : 0);
                $query_string .= "`{$this->db->escapeValue($ship)}` = '" . (int) $amount . "',";
            }
        }

        // remove last comma
        $query_string = substr_replace($query_string, '', -1);

        // end
        $query_string .= " WHERE `ship_planet_id` = '" . $planet_id . "';";

        // run
        $this->db->query($query_string);
    }

    public function saveDefenses(array $defenses, int $planet_id): void
    {
        // start
        $query_string = 'UPDATE `' . DEFENSES . '` SET ';

        // build middle
        foreach ($defenses as $defense => $amount) {
            if (strpos($defense, 'defense_') !== false) {
                $amount = (isset($amount) ? (int) $amount : 0);
                $query_string .= "`{$this->db->escapeValue($defense)}` = '" . (int) $amount . "',";
            }
        }

        // remove last comma
        $query_string = substr_replace($query_string, '', -1);

        // end
        $query_string .= " WHERE `defense_planet_id` = '" . $planet_id . "';";

        // run
        $this->db->query($query_string);
    }

    public function deletePlanetById(int $planet_id): void
    {
        $this->db->query(
            'DELETE p,b,d,s FROM `' . PLANETS . '` AS p
            INNER JOIN `' . BUILDINGS . '` AS b ON b.`building_planet_id` = p.`planet_id`
            INNER JOIN `' . DEFENSES . '` AS d ON d.`defense_planet_id` = p.`planet_id`
            INNER JOIN `' . SHIPS . "` AS s ON s.`ship_planet_id` = p.`planet_id`
            WHERE `planet_id` = '" . $planet_id . "'
                AND `planet_type`= '1';"
        );
    }

    public function softDeletePlanetById(int $planet_id): void
    {
        $this->db->query(
            'UPDATE `' . PLANETS . '` AS p, `' . PLANETS . '` AS m, `' . USERS . "` AS u SET
            p.`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',
            m.`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',
            u.`user_current_planet` = u.`user_home_planet_id`
            WHERE p.`planet_id` = '" . $planet_id . "' AND
                m.`planet_galaxy` = p.`planet_galaxy` AND
                m.`planet_system` = p.`planet_system` AND
                m.`planet_planet` = p.`planet_planet` AND
                m.`planet_type` = '3';"
        );
    }

    public function deleteMoonById(int $moon_id): void
    {
        $this->db->query(
            'DELETE m,b,d,s FROM `' . PLANETS . '` AS m
            INNER JOIN `' . BUILDINGS . '` AS b ON b.`building_planet_id` = m.`planet_id`
            INNER JOIN `' . DEFENSES . '` AS d ON d.`defense_planet_id` = m.`planet_id`
            INNER JOIN `' . SHIPS . "` AS s ON s.`ship_planet_id` = m.`planet_id`
            WHERE `planet_id` = '" . $moon_id . "'
                AND `planet_type` = '3';"
        );
    }

    public function softDeleteMoonById(int $moon_id): void
    {
        $this->db->query(
            'UPDATE `' . PLANETS . '` AS m, `' . USERS . "` AS u SET
                m.`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',
                u.`user_current_planet` = u.`user_home_planet_id`
                WHERE m.`planet_id` = '" . $moon_id . "'
                    AND m.`planet_type` = '3';"
        );
    }
}
