<?php
/**
 * Update Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\models\adm;

use application\core\Model;

/**
 * Update Class
 */
class Update extends Model
{
    /**
     * Execute a DB Backup
     *
     * @return string
     */
    public function runQuery(string $query): string
    {
        return $this->db->query($query);
    }
}
