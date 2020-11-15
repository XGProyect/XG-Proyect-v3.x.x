<?php
/**
 * Notes Model
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\models\game;

use App\core\Model;

/**
 * Notes Class
 */
class Notes extends Model
{
    /**
     * Get all notes by a certain user
     *
     * @param int $user_id
     *
     * @return array
     */
    public function getAllNotesByUserId(int $user_id): array
    {
        return $this->db->queryFetchAll(
            "SELECT
                n.*
            FROM `" . NOTES . "` n
            WHERE n.`note_owner` = '" . $user_id . "'
            ORDER BY n.`note_time` DESC;"
        ) ?? [];
    }

    /**
     * Get a note by a certain user
     *
     * @param int $user_id
     * @param int $note_id
     *
     * @return array
     */
    public function getNoteById(int $user_id, int $note_id): array
    {
        return $this->db->queryFetch(
            "SELECT
                n.*
            FROM `" . NOTES . "` n
            WHERE n.`note_id` = '" . $note_id . "'
                AND n.`note_owner` = '" . $user_id . "';"
        ) ?? [];
    }

    /**
     * Create a note by a certain user
     *
     * @param int $user_id
     * @param int $note_id
     *
     * @return void
     */
    public function createNewNote(array $note_data): void
    {
        foreach ($note_data as $field => $value) {
            $sql[] = "`" . $field . "` = '" . $value . "'";
        }

        $this->db->query(
            "INSERT INTO `" . NOTES . "` SET "
            . join(', ', $sql)
        );
    }

    /**
     * Update a note by a certain user
     *
     * @param int $user_id
     * @param int $note_id
     * @param array $note_data
     *
     * @return void
     */
    public function updateNoteById(int $user_id, int $note_id, array $note_data): void
    {
        foreach ($note_data as $field => $value) {
            $sql[] = "n.`" . $field . "` = '" . $value . "'";
        }

        $this->db->query(
            "UPDATE `" . NOTES . "` n SET "
            . join(', ', $sql) .
            "WHERE n.`note_owner` = '" . $user_id . "'
                AND n.`note_id` = '" . $note_id . "';"
        );
    }

    /**
     * Delete a note by a certain user
     *
     * @param int    $user_id
     * @param string $notes_ids
     *
     * @return void
     */
    public function deleteNoteById(int $user_id, string $notes_ids): void
    {
        $this->db->query(
            "DELETE FROM `" . NOTES . "`
            WHERE `note_owner` = '" . $user_id . "'
                AND `note_id` IN (" . $notes_ids . ");"
        );
    }
}
