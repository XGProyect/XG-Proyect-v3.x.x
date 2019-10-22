<?php

declare(strict_types=1);

/**
 * Preferences Controller
 *
 * PHP Version 7.1+
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
use application\core\entities\PreferencesEntity;
use application\core\enumerators\SwitchIntEnumerator as SwitchInt;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\Timing_library;
use application\libraries\game\Preferences as Pref;

use const MODULE_ID;

/**
 * Preferences Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Preferences extends Controller
{

    const MODULE_ID = 21;
    
    /**
     *
     * @var type \Users_library
     */
    private $_user;

    /**
     *
     * @var \Preferences
     */
    private $_preferences = null;

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
        parent::loadModel('game/preferences');
        
        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();

        // init a new buddy object
        $this->setUpPreferences();

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Creates a new preferences object that will handle all the preferences
     * creation methods and actions
     * 
     * @return void
     */
    private function setUpPreferences()
    {
        $this->_preferences = new Pref(
            $this->Preferences_Model->getAllPreferencesByUserId((int)$this->_user['user_id']),
            (int)$this->_user['user_id']
        );
    }

    /**
     * Run an action
     * 
     * @return void
     */
    private function runAction()
    {
        var_dump($this->_preferences->getCurrentPreference());
    }

    /**
     * Build the page
     * 
     * @return void
     */
    private function buildPage()
    {
        parent::$page->display(
            $this->getTemplate()->set(
                'game/preferences_view',
                array_merge(
                    $this->getLang(),
                    [
    
                    ]
                )
            )
        );
    }
}

/* end of preferences.php */
