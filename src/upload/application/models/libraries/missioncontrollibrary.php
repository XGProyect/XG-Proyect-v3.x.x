<?php
/**
 * MissionControlLibrary Model
 *
 * PHP Version 7.1+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\models\libraries;

use application\core\Model;

/**
 * MissionControlLibrary Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class MissionControlLibrary extends Model
{
    /**
     * Return a list of all the arriving fleets that are going
     * to be processed at this time.
     *
     * @return array
     */
    public function getArrivingFleets()
    {
        return $this->db->queryFetchAll(
            "SELECT
                f.*,
                sp.`planet_name` AS `planet_start_name`,
                ep.`planet_name` AS `planet_end_name`
            FROM `" . FLEETS . "` f
            LEFT JOIN `" . PLANETS . "` sp
                ON (sp.`planet_galaxy` = f.`fleet_start_galaxy` AND
                    sp.`planet_system` = f.`fleet_start_system` AND
                    sp.`planet_planet` = f.`fleet_start_planet` AND
                    sp.`planet_type` = f.`fleet_start_type`
            )
            LEFT JOIN `" . PLANETS . "` ep
                ON (ep.`planet_galaxy` = f.`fleet_end_galaxy` AND
                    ep.`planet_system` = f.`fleet_end_system` AND
                    ep.`planet_planet` = f.`fleet_end_planet` AND
                    ep.`planet_type` = f.`fleet_end_type`
            )
            WHERE f.`fleet_start_time` <= '" . time() . "'
                AND f.`fleet_mess` = '0'
            GROUP BY f.`fleet_id`, sp.`planet_name`, ep.`planet_name`
            ORDER BY f.`fleet_id` ASC"
        );
    }

    /**
     * Return a list of all the returning fleets that are going
     * to be processed at this time.
     *
     * @return array
     */
    public function getReturningFleets()
    {
        return $this->db->queryFetchAll(
            "SELECT
                f.*,
                sp.`planet_name` AS `planet_start_name`,
                ep.`planet_name` AS `planet_end_name`
            FROM `" . FLEETS . "` f
            LEFT JOIN `" . PLANETS . "` sp
                ON (sp.`planet_galaxy` = f.`fleet_start_galaxy` AND
                    sp.`planet_system` = f.`fleet_start_system` AND
                    sp.`planet_planet` = f.`fleet_start_planet` AND
                    sp.`planet_type` = f.`fleet_start_type`
            )
            LEFT JOIN `" . PLANETS . "` ep
                ON (ep.`planet_galaxy` = f.`fleet_end_galaxy` AND
                    ep.`planet_system` = f.`fleet_end_system` AND
                    ep.`planet_planet` = f.`fleet_end_planet` AND
                    ep.`planet_type` = f.`fleet_end_type`
            )
            WHERE f.`fleet_end_time` <= '" . time() . "'
                AND f.`fleet_mess` <> '0'
            GROUP BY f.`fleet_id`, sp.`planet_name`, ep.`planet_name`
            ORDER BY f.`fleet_id` ASC"
        );
    }
}

/* end of MissionControlLibrary.php */
