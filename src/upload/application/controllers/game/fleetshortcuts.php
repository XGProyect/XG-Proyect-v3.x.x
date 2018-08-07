<?php
/**
 * Fleetshortcuts Controller
 *
 * PHP Version 5.5+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\controllers\game;

use application\core\Controller;
use application\libraries\FunctionsLib;
use application\libraries\users\Shortcuts;
use const MAX_GALAXY_IN_WORLD;
use const MAX_PLANET_IN_SYSTEM;
use const MAX_SYSTEM_IN_GALAXY;

/**
 * Fleetshortcuts Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Fleetshortcuts extends Controller
{

    /**
     * 
     * @var int
     */
    const MODULE_ID = 8;

    /**
     * 
     * @var string
     */
    const REDIRECT_TARGET = 'game.php?page=shortcuts';
    
    /**
     *
     * @var array
     */
    private $_user;

    /**
     *
     * @var \Shortcuts
     */
    private $_shortcuts = null;
    
    /**
     *
     * @var int
     */
    private $_shortcuts_count = 0;
    
    /**
     *
     * @var array
     */
    private $_clean_data = [];
    
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
        parent::loadModel('game/shortcuts');
        
        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();

        // init a new shortcut object
        $this->setUpShortcuts();

        // time to do something
        $this->runAction();
        
        // build the page
        $this->buildPage();
    }

    /**
     * Creates a new shortcut object that will handle all the shortcuts
     * creation methods and actions
     * 
     * @return void
     */
    private function setUpShortcuts()
    {
        $this->_shortcuts = new Shortcuts(
            $this->_user['user_fleet_shortcuts']
        );
    }

    /**
     * Run an action
     * 
     * @return void
     */
    private function runAction()
    {
        $mode = filter_input(
            INPUT_GET,
            'mode',
            FILTER_CALLBACK,
            [
                'options' => function($value) {
                    
                    if (in_array($value, ['add', 'edit', 'delete', 'a'])) {
                        
                        return $value;
                    }
                    
                    return false;
                }
            ]
        );
            
        $data = filter_input_array(INPUT_POST, [
            'name' => FILTER_SANITIZE_STRING,
            'galaxy' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => MAX_GALAXY_IN_WORLD]
            ],
            'system' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => MAX_SYSTEM_IN_GALAXY]
            ],
            'planet' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => (MAX_PLANET_IN_SYSTEM + 1)]
            ],
            'type' => [
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => 1, 'max_range' => 3]
            ]
        ]);

        $action = filter_input(INPUT_GET, 'a', FILTER_VALIDATE_INT);
            
        if ($mode) {
            
            $this->_clean_data['mode'] = $mode;
            $this->_clean_data['data'] = $data;
            $this->_clean_data['action'] = $action;
            
            $this->{$mode .'Shortcut'}();
        }
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
            'shortcuts' => $this->buildShortcuts(),
            'no_shortcuts' => $this->_shortcuts_count <= 0 ? '<th colspan="2">' . $this->getLang()['fl_no_shortcuts'] . '</th>' : ''
        ];
        
        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                'shortcuts/shortcuts_view',
                array_merge(
                    $this->getLang(), $page
                )
            )
        );
    }

    /**
     * Build the shortcuts list
     * 
     * @return array
     */
    private function buildShortcuts(): array
    {
        $shortcuts = $this->_shortcuts->getAllAsArray();
        $list_of_shortcuts = [];
        
        if (count($shortcuts) > 0) {

            $set_row = true;
            $shortcut_id = 0;
            
            foreach ($shortcuts as $shortcut) {

                $list_of_shortcuts[] = [
                    'row_start' => $set_row ? '<tr height="20">' : '',
                    'shortcut_id' => $shortcut_id++,
                    'shortcut_name' => $shortcut['name'],
                    'shortcut_galaxy' => $shortcut['g'],
                    'shortcut_system' => $shortcut['s'],
                    'shortcut_planet' => $shortcut['p'],
                    'shortcut_type' => $this->getLang()['planet_type_shortcuts'][$shortcut['pt']],
                    'row_end' => !$set_row ? '</tr>' : '',
                ];

                ++$this->_shortcuts_count;

                $set_row = !$set_row;
            }
        }

        return $list_of_shortcuts;
    }
    
    /**
     * Create a shortcut
     * 
     * @return void
     */
    private function addShortcut(): void
    {
        $this->setData();
        
        /**
         * Parse the items
         */
        $page = [
            'mode' => 'add',
            'visibility' => 'hidden',
            'shortcut_id' => '',
            'name' => '',
            'galaxy' => '',
            'system' => '',
            'planet' => '',
            'type' => ''
        ];
        
        $this->buildEdit($page);
    }
    
    /**
     * Edit a shortcut
     * 
     * @return void
     */
    private function editShortcut(): void
    {
        $this->setData();
        
        $shortcut_id = $this->_clean_data['action'];
        
        if ($shortcut_id === false) {
            
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }
        
        $shortcut = $this->_shortcuts->getById($shortcut_id);
        
        /**
         * Parse the items
         */
        $page = [
            'mode' => 'edit',
            'visibility' => 'button',
            'shortcut_id' => '&a=' . $shortcut_id,
            'name' => $shortcut['name'],
            'galaxy' => $shortcut['g'],
            'system' => $shortcut['s'],
            'planet' => $shortcut['p'],
            'type' . $shortcut['pt']  => 'selected="selected"',
        ];
        
        $this->buildEdit($page);
    }
    
    /**
     * Delete a shortcut
     * 
     * @return void
     */
    private function deleteShortcut(): void
    {
        $this->setData();
    }
    
    /**
     * Build the edit view
     * 
     * @param array $page Page Data
     * 
     * @return void
     */
    private function buildEdit(array $page): void
    {
        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                'shortcuts/shortcuts_edit_view',
                array_merge(
                    $this->getLang(), $page
                )
            )
        );
    }
    
    /**
     * Set and save the post data if valid
     * 
     * @return void
     */
    private function setData(): void
    {        
        $data = $this->_clean_data['data'];
        
        if (is_array($data)) {
         
            if (!empty($data['name']) && $data['galaxy'] && $data['system'] && $data['planet'] && $data['type']) {
            
                $mode = $this->_clean_data['mode'];
                $action = $this->_clean_data['action'];
                
                if (!is_null($action) && !is_null($mode)) {

                    if ($mode == 'edit') {

                        $this->_shortcuts->editById(
                            $action, $data['name'], $data['galaxy'], $data['system'], $data['planet'], $data['type']
                        );  
                    }
                    
                    if ($mode == 'delete') {
                        
                        $this->_shortcuts->deleteById($action);
                    } 
                } else {

                    $this->_shortcuts->addNew(
                        $data['name'], $data['galaxy'], $data['system'], $data['planet'], $data['type']
                    );
                }
                
                $this->Shortcuts_Model->updateShortcuts(
                    $this->_user['user_id'], $this->_shortcuts->getAllAsJsonString()
                );
            }
            
            FunctionsLib::redirect(self::REDIRECT_TARGET);
        }
    }
}

/* end of fleetshortcuts.php */
