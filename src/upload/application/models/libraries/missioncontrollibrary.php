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
                ps.`planet_name` AS `planet_start_name`,
                pe.`planet_name` AS `planet_end_name`
            FROM `" . FLEETS . "` f, `" . FLEETS . "` sf
            LEFT JOIN `" . PLANETS . "` ps
                ON (ps.`planet_galaxy` = sf.`fleet_start_galaxy` AND
                    ps.`planet_system` = sf.`fleet_start_system` AND
                    ps.`planet_planet` = sf.`fleet_start_planet` AND
                    ps.`planet_type` = sf.`fleet_start_type`
            )
            LEFT JOIN `" . PLANETS . "` pe
                ON (pe.`planet_galaxy` = sf.`fleet_end_galaxy` AND
                    pe.`planet_system` = sf.`fleet_end_system` AND
                    pe.`planet_planet` = sf.`fleet_end_planet` AND
                    pe.`planet_type` = sf.`fleet_end_type`
            )
            WHERE
            (
                (
                    f.`fleet_start_galaxy` = sf.`fleet_start_galaxy` AND
                    f.`fleet_start_system` = sf.`fleet_start_system` AND
                    f.`fleet_start_planet` = sf.`fleet_start_planet` AND
                    f.`fleet_start_type` = sf.`fleet_start_type`
                )
                OR
                (
                    f.`fleet_end_galaxy` = sf.`fleet_start_galaxy` AND
                    f.`fleet_end_system` = sf.`fleet_start_system` AND
                    f.`fleet_end_planet` = sf.`fleet_start_planet` AND
                    f.`fleet_end_type`= sf.`fleet_start_type`
                )
            )
            AND
            (
                f.`fleet_start_time` < '" . time() . "'
                    OR f.`fleet_end_time` < '" . time() . "'
            )
            AND
            (
                sf.`fleet_start_time` <= '" . time() . "'
                    AND sf.`fleet_mess` ='0'
            )
            GROUP BY f.`fleet_id`, ps.`planet_name`, pe.`planet_name`
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
                ps.`planet_name` AS `planet_start_name`,
                pe.`planet_name` AS `planet_end_name`
            FROM `" . FLEETS . "` f, `" . FLEETS . "` ef
            LEFT JOIN `" . PLANETS . "` ps
                ON (ps.`planet_galaxy` = ef.`fleet_start_galaxy` AND
                    ps.`planet_system` = ef.`fleet_start_system` AND
                    ps.`planet_planet` = ef.`fleet_start_planet` AND
                    ps.`planet_type` = ef.`fleet_start_type`
            )
            LEFT JOIN `" . PLANETS . "` pe
                ON (pe.`planet_galaxy` = ef.`fleet_end_galaxy` AND
                    pe.`planet_system` = ef.`fleet_end_system` AND
                    pe.`planet_planet` = ef.`fleet_end_planet` AND
                    pe.`planet_type` = ef.`fleet_end_type`
            )
            WHERE
            (
                (
                    f.`fleet_start_galaxy` = ef.`fleet_end_galaxy` AND
                    f.`fleet_start_system` = ef.`fleet_end_system` AND
                    f.`fleet_start_planet` = ef.`fleet_end_planet` AND
                    f.`fleet_start_type` = ef.`fleet_end_type`
                )
                OR
                (
                    f.`fleet_end_galaxy` = ef.`fleet_end_galaxy` AND
                    f.`fleet_end_system` = ef.`fleet_end_system` AND
                    f.`fleet_end_planet` = ef.`fleet_end_planet` AND
                    f.`fleet_end_type`= ef.`fleet_end_type`
                )
            )
            AND
            (
                f.`fleet_start_time` < '" . time() . "'
                    OR f.`fleet_end_time` < '" . time() . "'
            )
            AND
            (
                ef.`fleet_start_time` <= '" . time() . "'
            )
            GROUP BY f.`fleet_id`, ps.`planet_name`, pe.`planet_name`
            ORDER BY f.`fleet_id` ASC"
        );
    }
}

/* end of MissionControlLibrary.php */
