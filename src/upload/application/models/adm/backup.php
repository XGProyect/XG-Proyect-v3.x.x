<?php
/**
 * Backup Model
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
namespace application\models\adm;

use application\core\Model;

/**
 * Backup Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
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

/* end of backup.php */
