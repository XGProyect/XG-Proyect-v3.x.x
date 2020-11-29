<?php declare (strict_types = 1);

/**
 * Research Model
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
 * Research Class
 */
class Research extends Model
{
    /**
     * Start a new research
     *
     * @param array $working_planet
     * @param array $current_user
     * @return void
     */
    public function startNewResearch(array $working_planet, array $current_user): void
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` AS p, `" . RESEARCH . "` AS r SET
                p.`planet_b_tech_id` = '" . $working_planet['planet_b_tech_id'] . "',
                p.`planet_b_tech` = '" . $working_planet['planet_b_tech'] . "',
                p.`planet_metal` = '" . $working_planet['planet_metal'] . "',
                p.`planet_crystal` = '" . $working_planet['planet_crystal'] . "',
                p.`planet_deuterium` = '" . $working_planet['planet_deuterium'] . "',
                r.`research_current_research` = '" . $current_user['research_current_research'] . "'
            WHERE p.`planet_id` = '" . $working_planet['planet_id'] . "'
                AND r.`research_user_id` = '" . $current_user['user_id'] . "';"
        );
    }

    /**
     * Get planet that's currently researching
     *
     * @param integer $current_research
     * @return array
     */
    public function getPlanetResearching(int $current_research): array
    {
        return $this->db->queryFetch(
            "SELECT
                `planet_id`,
                `planet_name`,
                `planet_b_tech`,
                `planet_b_tech_id`,
                `planet_galaxy`,
                `planet_system`,
                `planet_planet`
            FROM `" . PLANETS . "`
            WHERE `planet_id` = '" . $current_research . "';"
        );
    }

    /**
     * Get the total amount of laboratory levels
     *
     * @param integer $user_id
     * @param integer $labs_limit
     * @return integer
     */
    public function getAllLabsLevel(int $user_id, int $labs_limit): int
    {
        return (int) $this->db->queryFetch(
            "SELECT
                SUM(`building_laboratory`) AS `total_level`
            FROM `" . BUILDINGS . "` AS b
            INNER JOIN `" . PLANETS . "` AS p ON p.`planet_id` = b.building_planet_id
            WHERE planet_user_id = '" . $user_id . "'
            ORDER BY building_laboratory DESC
            LIMIT " . $labs_limit . ""
        )['total_level'];
    }
}
