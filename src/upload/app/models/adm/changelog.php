<?php declare (strict_types = 1);

/**
 * Changelog Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\adm;

use App\core\entities\ChangelogEntity;
use App\core\Model;

/**
 * Changelog Class
 */
class Changelog extends Model
{
    /**
     * Get all changelog items
     *
     * @return array|null
     */
    public function getAllEntries(): ?array
    {
        return $this->db->queryFetchAll(
            "SELECT
                c.*,
                l.`language_name` AS `changelog_language`
            FROM `" . CHANGELOG . "` AS c
            INNER JOIN `" . LANGUAGES . "` AS l
                ON l.`language_id` = c.`changelog_lang_id`
            ORDER BY c.`changelog_date` DESC, c.`changelog_version` DESC"
        );
    }

    /**
     * Get a single changelog entry
     *
     * @param integer $changelog_id
     * @return ChangelogEntity
     */
    public function getSingleEntry(int $changelog_id): ChangelogEntity
    {
        return new ChangelogEntity($this->db->queryFetch(
            "SELECT
                c.*
            FROM `" . CHANGELOG . "` AS c
            WHERE c.`changelog_id` = '" . $changelog_id . "';"
        ));
    }

    /**
     * Add a new entry validating the language id
     *
     * @param array $data
     * @return void
     */
    public function addEntry(array $data): void
    {
        try {
            $this->db->beginTransaction();

            $this->db->query(
                "INSERT INTO `" . CHANGELOG . "` SET
                    `changelog_lang_id` = (
                        SELECT
                            l.`language_id`
                        FROM `" . LANGUAGES . "` AS l
                        WHERE l.`language_id` = '" . $data['changelog_language'] . "'
                        LIMIT 1
                    ),
                    `changelog_version` = '" . $data['changelog_version'] . "',
                    `changelog_date` = '" . $data['changelog_date'] . "',
                    `changelog_description` = '" . $data['text'] . "';"
            );

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }

    /**
     * Edit an existing entry validating the language id
     *
     * @param array $data
     * @return void
     */
    public function updateEntry(array $data): void
    {
        try {
            $this->db->beginTransaction();

            $this->db->query(
                "UPDATE `" . CHANGELOG . "` AS c SET
                    c.`changelog_lang_id` = (
                        SELECT
                            l.`language_id`
                        FROM `" . LANGUAGES . "` AS l
                        WHERE l.`language_id` = '" . $data['changelog_language'] . "'
                        LIMIT 1
                    ),
                    c.`changelog_version` = '" . $data['changelog_version'] . "',
                    c.`changelog_date` = '" . $data['changelog_date'] . "',
                    c.`changelog_description` = '" . $data['text'] . "'
                WHERE c.`changelog_id` = '" . $data['changelog_id'] . "';"
            );

            $this->db->commitTransaction();
        } catch (Exception $e) {
            $this->db->rollbackTransaction();
        }
    }

    /**
     * Delete a changelog entry
     *
     * @param integer $changelog_id
     * @return void
     */
    public function deleteEntry(int $changelog_id): void
    {
        $this->db->query(
            "DELETE FROM `" . CHANGELOG . "` WHERE `changelog_id` = '" . $changelog_id . "';"
        );
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
