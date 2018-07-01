<?php
/**
 * Fleet Controller
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

/**
 * Fleet Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Fleet extends Controller
{
    /**
     * @var int Module ID
     */
    const MODULE_ID = 8;
    
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
        parent::loadModel('game/fleet');

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();
        $this->_planet = $this->getPlanetData();

        // init a new building object with the current building queue
        $this->setUpFleets();

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }
    
    /**
     * Creates a new building object that will handle all the building
     * creation methods and actions
     * 
     * @return void
     */
    private function setUpFleets()
    {

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
     * Build the page
     * 
     * @return void
     */
    private function buildPage()
    {
        
    }
}

/* end of fleet.php */