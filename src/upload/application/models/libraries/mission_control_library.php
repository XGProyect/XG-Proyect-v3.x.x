<?php
/**
 * Users_library Model
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

/**
 * Users_library Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Mission_control_library
{

    private $db = null;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct($db)
    {
        // use this to make queries
        $this->db = $db;
    }

    /**
     * __destruct
     * 
     * @return void
     */
    public function __destruct()
    {
        $this->db->closeConnection();
    }

    /**
     * Return a list of all the arriving fleets that are going 
     * to be processed at this time.
     * 
     * @return array
     */
    public function getArrivingFleets()
    {
        return $this->db->queryFetchAll(
                "SELECT f.*
            FROM `" . FLEETS . "` f, `" . FLEETS . "` sf
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
            GROUP BY f.`fleet_id`
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
                "SELECT f.*
            FROM `" . FLEETS . "` f, `" . FLEETS . "` ef
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
            GROUP BY f.`fleet_id`
            ORDER BY f.`fleet_id` ASC"
        );
    }
}

/* end of mission_control_library.php */
