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
            FROM `xgp_changelog` c
            LEFT JOIN `xgp_languages` l
                ON l.`language_id` = c.`changelog_lang_id`
            WHERE l.`language_name` = (
                SELECT o.`option_value` FROM `xgp_options` o WHERE `option_name` = 'lang'
            )
            ORDER BY c.`changelog_date` DESC"
        );
    }
}

/* end of changelog.php */
