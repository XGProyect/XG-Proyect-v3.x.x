<?php
/**
 * Notes entity
 *
 * PHP Version 7.1+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\core\entities;

use application\core\Entity;

/**
 * Notes Entity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class NotesEntity extends Entity
{

    /**
     * Constructor
     * 
     * @param array $data Data
     * 
     * @return void
     */
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Return the note id
     * 
     * @return string
     */
    public function getNoteId()
    {
        return $this->_data['note_id'];
    }

    /**
     * Return the note owner
     * 
     * @return string
     */
    public function getNoteOwner()
    {
        return $this->_data['note_owner'];
    }

    /**
     * Return the note time
     * 
     * @return string
     */
    public function getNoteTime()
    {
        return $this->_data['note_time'];
    }

    /**
     * Return the note priority
     * 
     * @return string
     */
    public function getNotePriority()
    {
        return $this->_data['note_priority'];
    }

    /**
     * Return the note title
     * 
     * @return string
     */
    public function getNoteTitle()
    {
        return $this->_data['note_title'];
    }
    
    /**
     * Return the note text
     * 
     * @return string
     */
    public function getNoteText()
    {
        return $this->_data['note_text'];
    }
}

/* end of NotesEntity.php */
