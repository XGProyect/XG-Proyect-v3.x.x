<?php
/**
 * Notes Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\controllers\game;

use App\core\BaseController;
use App\core\entities\NotesEntity;
use App\core\enumerators\ImportanceEnumerator as Importance;
use App\libraries\FormatLib;
use App\libraries\Functions;
use App\libraries\TimingLibrary as Timing;
use App\libraries\users\Notes as Note;

/**
 * Notes Class
 */
class Notes extends BaseController
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
     * @var \Notes
     */
    private $notes = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/notes');

        // load Language
        parent::loadLang(['game/notes']);

        // init a new notes object
        $this->setUpNotes();
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

        // time to do something
        $this->runAction();

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
        $data = filter_input_array(INPUT_POST, [
            's' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 1, 'max_range' => 2],
            ],
            'u' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => Importance::unimportant, 'max_range' => Importance::important],
            ],
            'title' => [
                'filter' => FILTER_SANITIZE_STRING,
                'options' => ['min_range' => 1, 'max_range' => 32],
            ],
            'text' => [
                'filter' => FILTER_SANITIZE_STRING,
                'options' => ['min_range' => 1, 'max_range' => 5000],
            ],
            'n' => FILTER_SANITIZE_NUMBER_INT,
        ]);

        $delete = filter_input(INPUT_POST, 'delnote', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        // add a note
        if (isset($data['s']) && $data['s'] == 1) {
            $this->createNewNote($data);
        }

        // edit a note
        if (isset($data['s']) && $data['s'] == 2) {
            $this->editNote($data);
        }

        // delete notes
        if (isset($delete) && count($delete) > 0) {
            $this->deleteNote($delete);
        }
    }

    /**
     * Creates a new notes object that will handle all the notes
     * creation methods and actions
     *
     * @return void
     */
    private function setUpNotes()
    {
        $this->notes = new Note(
            $this->Notes_Model->getAllNotesByUserId($this->user['user_id']),
            $this->user['user_id']
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
                    $this->langs->language,
                    $page['data']
                )
            ),
            false,
            '',
            false
        );
    }

    /**
     * Build list of notes block
     *
     * @return array
     */
    private function buildNotesListBlock(): array
    {
        $list_of_notes = [];

        $notes = $this->notes->getNotes();

        if ($this->notes->hasNotes()) {
            foreach ($notes as $note) {
                if ($note instanceof NotesEntity) {
                    $list_of_notes[] = [
                        'note_id' => $note->getNoteId(),
                        'note_time' => Timing::formatExtendedDate($note->getNoteTime()),
                        'note_color' => FormatLib::getImportanceColor($note->getNotePriority()),
                        'note_title' => $note->getNoteTitle(),
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
            'options' => ['min_range' => 1, 'max_range' => 2],
        ]);

        if ($edit_view !== false && !is_null($edit_view)) {
            return [
                'template' => 'game/notes_form_view',
                'data' => array_merge(
                    ['js_path' => JS_PATH],
                    $this->buildEditBlock($edit_view)
                ),
            ];
        }

        return [
            'template' => 'game/notes_view',
            'data' => [
                'list_of_notes' => $this->buildNotesListBlock(),
                'no_notes' => $this->notes->hasNotes() ? '' : '<tr><th colspan="4">' . $this->langs->line('nt_no_notes_found') . '</th>',
            ],
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
            'selected_1' => 'selected="selected"',
            'selected_0' => '',
        ];

        // edit
        if ($edit_view == 2 && !is_null($note_id)) {
            $note = $this->Notes_Model->getNoteById($this->user['user_id'], $note_id);
            $selected = array_fill_keys(array_keys($selected), null); // clear values keeping the keys

            if ($note) {
                $note_data = new Note(
                    [$note]
                );

                $selected['selected_' . $note_data->getNoteById($note_id)->getNotePriority()] = 'selected="selected"';

                return array_merge([
                    's' => 2,
                    'note_id' => '<input type="hidden" name="n" value=' . $note_data->getNoteById($note_id)->getNoteId() . '>',
                    'title' => $this->langs->line('nt_edit_note'),
                    'subject' => $note_data->getNoteById($note_id)->getNoteTitle(),
                    'text' => $note_data->getNoteById($note_id)->getNoteText(),
                ], $selected);
            }
        }

        // add or default
        return array_merge([
            's' => 1,
            'note_id' => '',
            'title' => $this->langs->line('nt_add_note'),
            'subject' => $this->langs->line('nt_your_subject'),
            'text' => '',
        ], $selected);
    }

    /**
     * Create a new note
     *
     * @param array $data
     *
     * @return void
     */
    private function createNewNote(array $data): void
    {
        $this->Notes_Model->createNewNote(
            [
                'note_owner' => $this->user['user_id'],
                'note_time' => time(),
                'note_priority' => is_int($data['u']) ? $data['u'] : Importance::important,
                'note_title' => !empty($data['title']) ? $data['title'] : $this->langs->line('nt_your_subject'),
                'note_text' => !empty($data['text']) ? $data['text'] : '',
            ]
        );
    }

    /**
     * Edit a note
     *
     * @param array $data
     *
     * @return void
     */
    private function editNote(array $data): void
    {
        $this->Notes_Model->updateNoteById(
            $this->user['user_id'],
            $data['n'],
            [
                'note_time' => time(),
                'note_priority' => is_int($data['u']) ? $data['u'] : Importance::important,
                'note_title' => !empty($data['title']) ? $data['title'] : $this->langs->line('nt_your_subject'),
                'note_text' => !empty($data['text']) ? $data['text'] : '',
            ]
        );
    }

    /**
     * Delete a note or multiple
     *
     * @param array $data
     *
     * @return void
     */
    private function deleteNote(array $data): void
    {
        $delete_string = [];

        foreach ($data as $note_id => $set) {
            if ($set == 'y') {
                $delete_string[] = $note_id;
            }
        }

        $this->Notes_Model->deleteNoteById(
            $this->user['user_id'],
            join(',', $delete_string)
        );
    }
}
