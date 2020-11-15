<?php
/**
 * Backup Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\adm;

use App\core\Model;

/**
 * Backup Class
 */
class Backup extends Model
{
    /**
     * Execute a DB Backup
     *
     * @return string
     */
    public function performBackup(): string
    {
        return $this->db->backupDb();
    }
}
