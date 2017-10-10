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
use application\core\Database;
use application\libraries\FunctionsLib;

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
    const MODULE_ID = 19;

    private $_lang;
    private $_current_user;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        $this->_db = new Database();
        $this->_lang = parent::$lang;
        $this->_current_user = parent::$users->getUserData();

        $this->build_page();
    }

    /**
     * method __destruct
     * param
     * return close db connection
     */
    public function __destruct()
    {
        $this->_db->closeConnection();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $this->_lang['js_path'] = JS_PATH;
        $parse = $this->_lang;

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

                FunctionsLib::redirect('game.php?page=notes');
            } elseif ($s == 2) {
                $id = intval($_POST['n']);
                $note_query = $this->_db->query(
                    "SELECT *
                    FROM " . NOTES . "
                    WHERE note_id=" . intval($id) . " AND
                                    note_owner=" . intval($this->_current_user['user_id']) . ""
                );

                if (!$note_query)
                    FunctionsLib::redirect('game.php?page=notes');

                $this->_db->query("UPDATE `" . NOTES . "` SET
                    note_time=$time,
                    note_priority=$priority,
                    note_title='$title',
                    note_text='$text'
                    WHERE note_id=" . intval($id) . ""
                );

                FunctionsLib::redirect('game.php?page=notes');
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

            FunctionsLib::redirect('game.php?page=notes');
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
                    FunctionsLib::redirect('game.php?page=notes');
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
            } else {
                $notes_query = $this->_db->query("SELECT *
														FROM `" . NOTES . "`
														WHERE `note_owner` = " . $this->_current_user['user_id'] . "
														ORDER BY `note_time` DESC");

                $count = 0;
                $NotesBodyEntryTPL = parent::$page->getTemplate('notes/notes_body_entry');
                $list = '';

                while ($note = $this->_db->fetchArray($notes_query)) {
                    $count++;

                    $parse['NOTE_COLOR'] = $this->return_priority($note['note_priority']);
                    $parse['NOTE_ID'] = $note['note_id'];
                    $parse['NOTE_TIME'] = date(FunctionsLib::readConfig('date_format_extended'), $note['note_time']);
                    $parse['NOTE_TITLE'] = $note['note_title'];
                    $parse['NOTE_TEXT'] = strlen($note['note_text']);

                    $list .= parent::$page->parseTemplate($NotesBodyEntryTPL, $parse);
                }

                if ($count == 0) {
                    $list .= "<tr><th colspan=4>" . $this->_lang['nt_you_dont_have_notes'] . "</th>\n";
                }

                $parse['BODY_LIST'] = $list;

                parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('notes/notes_body'), $parse), false, '', false);
            }
        }
    }

    /**
     * method return_priority
     * param $priority
     * return the color for each priority
     */
    private function return_priority($priority)
    {
        switch ($priority) {
            case 0:
            default:
                return 'lime';
                break;
            case 1:
                return 'yellow';
                break;
            case 2:
                return 'red';
                break;
        }
    }
}

/* end of notes.php */
