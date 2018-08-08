<?php
/**
 * Notes Model
 *
 * PHP Version 7+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\models\game;

/**
 * Notes Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Notes
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
     * 
     * @return void
     */
    public function updateNoteById(int $user_id, int $note_id): void
    {
        $this->db->query(
            ""
        );
    }
    
    /**
     * Delete a note by a certain user
     * 
     * @param int $user_id
     * @param int $note_id
     * 
     * @return void
     */
    public function deleteNoteById(int $user_id, int $note_id): void
    {
        $this->db->query(
            ""
        );
    }
}

/* end of notes.php */