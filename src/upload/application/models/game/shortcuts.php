<?php
/**
 * Shortcuts Model
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
 * Shortcuts Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Shortcuts
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
     * Update user shortcuts
     *
     * @param int    $user_id   User ID
     * @param string $shortcuts Shortcuts
     *
     * @return void
     */
    public function updateShortcuts(int $user_id, string $shortcuts): void
    {
        $this->db->query(
            "UPDATE `" . USERS . "` u SET
                u.`user_fleet_shortcuts` = '" . $shortcuts . "'
            WHERE u.`user_id` = '" . $user_id . "'"
        );
    }
}

/* end of shortcuts.php */
