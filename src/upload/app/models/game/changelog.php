<?php
/**
 * Changelog Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
namespace App\models\game;

use App\core\Model;

/**
 * Changelog Class
 */
class Changelog extends Model
{
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
