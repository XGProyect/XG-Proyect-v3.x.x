<?php
/**
 * Techtree Controller
 *
 * PHP Version 7.1+
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
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Techtree Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Techtree extends Controller
{

    const MODULE_ID = 10;

    /**
     *
     * @var array
     */
    private $_user;

    /**
     *
     * @var array
     */
    private $_planet;
    
    /**
     * 
     * @var \Objects
     */
    private $_resource;
    
    /**
     *
     * @var \Objects 
     */
    private $_requirements;
    
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
        
        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();
        
        // set planet data
        $this->_planet = $this->getPlanetData();

        // requirements
        $this->_resource = parent::$objects->getObjects();
        
        // requirements
        $this->_requirements = parent::$objects->getRelations();

        // build the page
        $this->buildPage();
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
            'list_of_constructions' => $this->buildBlock('build'),
            'list_of_research' => $this->buildBlock('tech'),
            'list_of_ships' => $this->buildBlock('fleet'),
            'list_of_missiles' => $this->buildBlock('missiles'),
            'list_of_defenses' => $this->buildBlock('defenses')
        ];

        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                'game/techtree_view',
                array_merge(
                    $this->getLang(), $page
                )
            )
        );
    }

    /**
     * Build the block
     * 
     * @param string $object_id
     * 
     * @return array
     */
    private function buildBlock(string $object_id): array
    {
        $objects = parent::$objects->getObjectsList($object_id);
        $list_of_objects = [];
        
        foreach ($objects as $object) {
 
            $list_of_objects[] = [
                'tt_info' => $object,
                'tt_name' => $this->getLang()['tech'][$object],
                'tt_detail' => '',
                'requirements' => join('<br/>', $this->getRequirements($object))
            ];
        }
        
        return $list_of_objects;
    }
    
    /**
     * Build the requirements list
     * 
     * @param int $object
     * 
     * @return array
     */
    private function getRequirements(int $object): array
    {
        $list_of_requirements = [];
        
        if (!isset($this->_requirements[$object])) {
        
            return $list_of_requirements;
        }
        
        foreach ($this->_requirements[$object] as $requirement => $level) {

            $color = 'Red';
            
            if ((isset($this->_user[$this->_resource[$requirement]]) 
                && $this->_user[$this->_resource[$requirement]] >= $level) 
                or (isset($this->_planet[$this->_resource[$requirement]])
                && $this->_planet[$this->_resource[$requirement]] >= $level)) {
                
                $color = 'Green';
            }

            $list_of_requirements[] = FormatLib::{'color' . $color}(
                FormatLib::formatLevel(
                    $this->getLang()['tech'][$requirement], $this->getLang()['tt_lvl'], $level
                )
            );
            
        }
        
        return $list_of_requirements;
    }
}

/* end of techtree.php */
