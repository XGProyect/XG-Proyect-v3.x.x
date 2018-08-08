<?php
/**
 * Notes Controller
 *
 * PHP Version 5.5+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\controllers\game;

use application\core\Controller;
use application\core\entities\NotesEntity;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\Timing_library;
use application\libraries\users\Notes as Note;
use const JS_PATH;
use const NOTES;

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
class Notes extends Controller
{

    /**
     * 
     * @var int
     */
    const MODULE_ID = 19;

    /**
     * 
     * @var string
     */
    const REDIRECT_TARGET = 'game.php?page=notes';
    
    /**
     *
     * @var array
     */
    private $_user;

    /**
     *
     * @var \Notes
     */
    private $_notes = null;
    
    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/notes');
        
        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();

        // time to do something
        $this->runAction();
        
        // init a new notes object
        $this->setUpNotes();

        // build the page
        $this->buildPage();
    }

    /**
     * Run an action
     * 
     * @return void
     */
    private function runAction()
    {
        
    }
    
    /**
     * Creates a new ships object that will handle all the ships
     * creation methods and actions
     * 
     * @return void
     */
    private function setUpNotes()
    {
        $this->_notes = new Note(
            $this->Notes_Model->getAllNotesByUserId($this->_user['user_id']),
            $this->_user['user_id']
        );
    }

    /**
     * Build the page
     * 
     * @return void
     */
    private function buildPage()
    {
        /**
         * Parse the items
         */        
        $page = $this->getCurrentPage();

        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                $page['template'],
                array_merge(
                    $this->getLang(), $page['data']
                )
            ), false, '', false
        );

        $a = isset($_GET['a']) ? intval($_GET['a']) : NULL;
        $n = isset($_GET['n']) ? intval($_GET['n']) : NULL;
        $s = isset($_POST['s']) ? intval($_POST['s']) : NULL;

        if ($s == 1 or $s == 2) {
            $time = time();
            $priority = intval($_POST['u']);
            $title = ( $_POST['title'] ) ? $this->_db->escapeValue(strip_tags($_POST['title'])) : "Sin t&iacute;tulo";
            $text = $_POST['text'] ? FunctionsLib::formatText($_POST['text']) : $this->_lang['nt_no_text'];

            if ($s == 1) {
                $this->_db->query("INSERT INTO " . NOTES . " SET
                    note_owner=" . intval($this->_current_user['user_id']) . ",
                    note_time=$time,
                    note_priority=$priority,
                    note_title='$title',
                    note_text='$text'"
                );

                FunctionsLib::redirect(self::REDIRECT_TARGET);
            } elseif ($s == 2) {
                $id = intval($_POST['n']);
                $note_query = $this->_db->query(
                    "SELECT *
                    FROM " . NOTES . "
                    WHERE note_id=" . intval($id) . " AND
                                    note_owner=" . intval($this->_current_user['user_id']) . ""
                );

                if (!$note_query)
                    FunctionsLib::redirect(self::REDIRECT_TARGET);

                $this->_db->query("UPDATE `" . NOTES . "` SET
                    note_time=$time,
                    note_priority=$priority,
                    note_title='$title',
                    note_text='$text'
                    WHERE note_id=" . intval($id) . ""
                );

                FunctionsLib::redirect(self::REDIRECT_TARGET);
            }
        }
        elseif ($_POST) {
            foreach ($_POST as $a => $b) {
                if (preg_match("/delmes/i", $a) && $b == "y") {
                    $id = str_replace("delmes", "", $a);
                    $note_query = $this->_db->query("SELECT *
															FROM `" . NOTES . "`
															WHERE `note_id` = " . (int) $id . "
																AND `note_owner` = " . $this->_current_user['user_id'] . "");

                    if ($note_query) {
                        $this->_db->query("DELETE FROM `" . NOTES . "`
												WHERE `note_id` = " . (int) $id . ";");
                    }
                }
            }

            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }
    }

    /**
     * Build list of notes block
     * 
     * @return array
     */
    private function buildNotesListBlock(): array
    {
        $list_of_notes = [];
        
        $notes = $this->_notes->getNotes();
        
        if ($this->_notes->hasNotes()) {

            foreach ($notes as $note) {

                if ($note instanceof NotesEntity) {

                    $list_of_notes[] = [
                        'note_id' => $note->getNoteId(),
                        'note_time' => Timing_library::formatDefaultTime($note->getNoteTime()),
                        'note_color' => FormatLib::getImportanceColor($note->getNotePriority()),
                        'note_title' => $note->getNoteTitle(),
                        'note_text' => strlen($note->getNoteText())
                    ];   
                }
            }   
        }
        
        return $list_of_notes;
    }
    
    /**
     * Get current page
     * 
     * @return array
     */
    private function getCurrentPage(): array
    {
        $edit_view = filter_input(INPUT_GET, 'a', FILTER_VALIDATE_INT, [
            'options'   => ['min_range' => 1, 'max_range' => 2]
        ]);

        if ($edit_view !== false && !is_null($edit_view)) {
            
            return [
                'template' => 'notes/notes_form_view',
                'data' => array_merge(
                    ['js_path' => JS_PATH],
                    $this->buildEditBlock($edit_view)
                )
            ];
        }

        return [
            'template' => 'notes/notes_view',
            'data' => [
                'list_of_notes' => $this->buildNotesListBlock(),
                'no_notes' => $this->_notes->hasNotes() ? '' : '<tr><th colspan="4">' . $this->getLang()['nt_you_dont_have_notes'] . '</th>'
            ]
        ];
    }
    
    /**
     * Build the edit view
     * 
     * @param int $edit_view
     * 
     * @return array
     */
    private function buildEditBlock(int $edit_view): array
    {
        $note_id = filter_input(INPUT_GET, 'n', FILTER_VALIDATE_INT);
        $selected = [
            'selected_2' => '',
            'selected_1' => '',
            'selected_0' => ''
        ];
        
        // edit
        if ($edit_view == 2 && !is_null($note_id)) {
            
            $note = $this->Notes_Model->getNoteById($this->_user['user_id'], $note_id);
            
            if ($note) {

                $note_data = new Note(
                    [$note]
                );
                
                $selected[$note_data->getNoteById($note_id)]->getNotePriority();
                
                return array_merge([
                    's' => 2,
                    'note_id' => '<input type="hidden" name="n" value=' . $note_data->getNoteById($note_id)->getNoteId() . '>',
                    'title' => $this->getLang()['nt_edit_note'],
                    'subject' => $note_data->getNoteById($note_id)->getNoteTitle(),
                    'text' => $note_data->getNoteById($note_id)->getNoteText()
                ], $selected);
            }            
        }

        // add or default
        return array_merge([
            's' => 1,
            'title' => $this->getLang()['nt_create_note'],
            'subject' => $this->getLang()['nt_subject_note'],
            'text' => ''
        ], $selected);
    }
}

/* end of notes.php */
