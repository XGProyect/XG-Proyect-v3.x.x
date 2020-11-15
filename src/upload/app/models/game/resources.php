<?php declare (strict_types = 1);

/**
 * Resources Model
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
 * Resources Class
 */
class Resources extends Model
{
    /**
     * Update current planet
     *
     * @param array $planet
     * @param string $sub_query
     * @return void
     */
    public function updateCurrentPlanet(array $planet, string $sub_query): void
    {
        $this->db->query(
            "UPDATE `" . PLANETS . "` SET
                `planet_id` = '" . $planet['planet_id'] . "'
                $sub_query
                WHERE `planet_id` = '" . $planet['planet_id'] . "';"
        );
    }
}
