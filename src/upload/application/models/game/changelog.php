<?php
/**
 * Changelog Model
 *
 * PHP Version 7.1+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
namespace application\models\game;

use application\core\Database;

/**
 * Changelog Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Changelog
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
     * Get changelog entries
     *
     * @return array
     */
    public function getAllChangelogEntries()
    {
        return $this->db->queryFetchAll(
            "SELECT
                c.`changelog_version`,
                c.`changelog_date`,
                c.`changelog_description`
            FROM `" . CHANGELOG . "` c
            LEFT JOIN `" . LANGUAGES . "` l
                ON l.`language_id` = c.`changelog_lang_id`
            WHERE l.`language_name` = (
                SELECT o.`option_value` FROM `" . OPTIONS . "` o WHERE `option_name` = 'lang'
            )
            ORDER BY c.`changelog_date` DESC"
        );
    }
}

/* end of changelog.php */
