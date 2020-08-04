<?php

declare (strict_types = 1);

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
 * @version  3.1.0
 */
namespace application\models\adm;

use application\core\Model;

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
class Changelog extends Model
{
    /**
     * Get all changelog items
     *
     * @return array|null
     */
    public function getAllItems(): ?array
    {
        return $this->db->queryFetchAll(
            "SELECT
                c.*,
                l.`language_name` AS `changelog_language`
            FROM `" . CHANGELOG . "` AS c
            INNER JOIN `" . LANGUAGES . "` AS l
                ON l.`language_id` = c.`changelog_lang_id`
            ORDER BY c.`changelog_date` DESC, c.`changelog_version` ASC"
        );
    }

    public function getSingleItem(int $changelog_id)
    {

    }

    public function addItem()
    {

    }

    public function updateItem(int $changelog_id)
    {

    }

    public function deleteItem(int $changelog_id)
    {

    }

    /**
     * Get the full list of languages
     *
     * @return array
     */
    public function getAllLanguages(): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                l.*
            FROM `" . LANGUAGES . "` AS l
            ORDER BY l.`language_name`"
        );
    }
}

/* end of changelog.php */
