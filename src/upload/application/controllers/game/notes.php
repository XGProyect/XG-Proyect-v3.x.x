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

        // init a new notes object
        $this->setUpNotes();

        // build the page
        $this->buildPage();
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
        $page = [
            'js_path' => JS_PATH,
            'list_of_notes' => $this->buildNotesListBlock(),
            'no_notes' => $this->_notes->hasNotes() ? '' : '<tr><th colspan="4">' . $this->getLang()['nt_you_dont_have_notes'] . '</th>'
        ];

        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                'notes/notes_view',
                array_merge(
                    $this->getLang(), $page
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
        } else {
            if ($a == 1) {
                $parse['c_Options'] = "<option value=2 selected=selected>" . $this->_lang['nt_important'] . "</option>
				<option value=1>" . $this->_lang['nt_normal'] . "</option>
				<option value=0>" . $this->_lang['nt_unimportant'] . "</option>";
                $parse['TITLE'] = $this->_lang['nt_create_note'];
                $parse['inputs'] = "<input type=hidden name=s value=1>";

                parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('notes/notes_form'), $parse), false, '', false);
            } elseif ($a == 2) {
                $SELECTED['0'] = '';
                $SELECTED['1'] = '';
                $SELECTED['2'] = '';

                $note = $this->_db->queryFetch("SELECT *
														FROM `" . NOTES . "`
														WHERE `note_owner` = " . $this->_current_user['user_id'] . "
															AND `note_id` = " . (int) $n . ";");

                if (!$note) {
                    FunctionsLib::redirect(self::REDIRECT_TARGET);
                }


                $SELECTED[$note['note_priority']] = ' selected="selected"';

                $parse['c_Options'] = "<option value=2{$SELECTED['2']}>" . $this->_lang['nt_important'] . "</option>
				<option value=1{$SELECTED['1']}>" . $this->_lang['nt_normal'] . "</option>
				<option value=0{$SELECTED['0']}>" . $this->_lang['nt_unimportant'] . "</option>";

                $parse['TITLE'] = $this->_lang['nt_edit_note'];
                $parse['inputs'] = '<input type="hidden" name="s" value="2"><input type="hidden" name="n" value=' . $note['note_id'] . '>';
                $parse['asunto'] = $note['note_title'];
                $parse['texto'] = $note['note_text'];

                parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('notes/notes_form'), $parse), false, '', false);
            }
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
}

/* end of notes.php */
