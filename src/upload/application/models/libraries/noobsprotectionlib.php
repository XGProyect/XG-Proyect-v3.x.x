<?php

declare (strict_types = 1);

/**
 * NoobsProtectionLib Model
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

use application\core\Database;
use application\libraries\FunctionsLib as Functions;

/**
 * NoobsProtectionLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class NoobsProtectionLib
{
    private $db = null;

    /**
     * Constructor
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        // use this to make queries
        $this->db = $db;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->db->closeConnection();
    }

    /**
     * Read all server configurations
     *
     * @return array
     */
    public function readAllConfigs(): array
    {
        return Functions::readConfig('', true);
    }

    /**
     * Return points for current user and the other user
     *
     * @param integer $current_user_id
     * @param integer $other_user_id
     * @return array
     */
    public function returnBothPartiesPoints(int $current_user_id, int $other_user_id): array
    {
        return $this->db->queryFetch(
            "SELECT
                (
                    SELECT
                        `user_statistic_total_points`
                    FROM `" . USERS_STATISTICS . "`
                    WHERE `user_statistic_user_id` = " . $current_user_id . "
                ) AS user_points,
                (
                    SELECT
                        `user_statistic_total_points`
                    FROM `" . USERS_STATISTICS . "`
                    WHERE `user_statistic_user_id` = " . $other_user_id . "
                ) AS target_points"
        );
    }
}

/* end of noobsprotectionlib.php */
