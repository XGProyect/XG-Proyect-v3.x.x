<?php
/**
 * Combat Report Model
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
namespace application\models\game;

use application\core\Database;

/**
 * Combat Report Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Combatreport
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
     * Get report by its Id
     *
     * @param string $report_id Report ID
     *
     * @return array
     */
    public function getReportById($report_id)
    {
        $result[] = $this->db->queryFetch(
            "SELECT *
            FROM `" . REPORTS . "`
            WHERE `report_rid` = '" . $this->db->escapeValue($report_id) . "';"
        );

        return $result;
    }
}

/* end of combatreport.php */
