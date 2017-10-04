<?php
/**
 * Buildings Controller
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
use Exception;

/**
 * Buildings Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Buildings extends Controller
{
    const MODULE_ID = 3;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // build the page
        $this->buildPage();
    }

    /**
     * Close DB Connection
     * 
     * @return void
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }
    
    /**
     * Build the page
     * 
     * @return void
     */
    private function buildPage()
    {
        //$buildings  = $this->getObjects()->getObjectsList();
        
        /**
         * Parse the items
         */
        $page                   = [];
        $page['BuildingsList']  = $this->buildListOfBuildings();
        
        parent::$page->display(
            parent::$page->get('buildings/buildings_builds')->parse($page)
        );
    }
    
    /**
     * Build the list of buildings
     * 
     * @return string
     */
    private function buildListOfBuildings()
    {
        $buildings      = $this->getAllowedBuildings();
        $buildings_list = '';
        
        if (!is_null($buildings)) {
           
            $item_to_parse          = [];
            $item_to_parse['dpath'] = DPATH;
            
            foreach ($buildings as $building_id) {

                $item_to_parse['i']             = $building_id;
                $item_to_parse['descriptions']  = $this->getLang()['res']['descriptions'][$building_id];                
                
                $buildings_list .= parent::$page->get('buildings/buildings_builds_row')->parse($item_to_parse);
            }
        }
        
        return $buildings_list;
    }
    
    /**
     * Determine the current page and validate it
     * 
     * @return array
     * 
     * @throws Exception
     */
    private function getCurrentPage()
    {
        try {
            $get_value      = filter_input(INPUT_GET, 'page');
            $allowed_pages  = ['resources', 'station'];

            if (in_array($get_value, $allowed_pages)) {

                return $get_value;
            }
            
            throw new Exception();

        } catch (Exception $e) {

            FunctionsLib::redirect('game.php?page=overview');
        }
    }
    
    /**
     * Get an array with an allowed set of items for the current page
     * 
     * @return array
     */
    private function getAllowedBuildings()
    {
        $allowed_buildings = [
            'resources' => [1, 2, 3, 4, 12, 22, 23, 24],
            'station'   => [14, 15, 21, 31, 33, 34, 44]
        ];
        
        return $allowed_buildings[$this->getCurrentPage()];
    }
}

/* end of buildings.php */
