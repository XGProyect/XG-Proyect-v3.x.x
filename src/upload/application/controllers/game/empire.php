<?php

declare(strict_types=1);

/**
 * Empire Controller
 *
 * PHP Version 7+
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
use application\core\Database;
use application\libraries\DevelopmentsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

use DPATH;
use MODULE_ID;

/**
 * Empire Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Empire extends Controller
{

    const MODULE_ID = 2;
    
    /**
     *
     * @var type \Users_library
     */
    private $_user;

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
        parent::loadModel('game/empire');

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();

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
        parent::$page->display(
            $this->getTemplate()->set(
                'empire/empire_view',
                array_merge(
                    $this->getLang(),
                    $this->buildBlocks()
                )
            ) 
        );
    }

    /**
     * Build all the different blocks
     *
     * @return array
     */
    private function buildBlocks(): array
    {
        $empire_data            = $this->Empire_Model->getAllPlayerData((int)$this->_user['user_id']);
        $elements['overview']   = ['image', 'name', 'coords', 'fields'];
        $elements['resources']  = ['metal', 'crystal', 'deuterium', 'energy'];

        foreach ($empire_data as $planet) {

            foreach ($elements['overview'] as $element) {

                $empire[$element][] = $this->{'set' . ucfirst($element)}($planet);
            }

            foreach ($elements['resources'] as $element) {

                $empire[$element][] = $this->setResources($planet, $element);
            }
        }





        return array_merge(
            [
                'amount_of_planets' => count($empire_data) + 1,
            ],
            $empire
        );

        /*
        $resource = parent::$objects->getObjects();
        $reslist = parent::$objects->getObjectsList();


        while ($p = $this->_db->fetchArray($planetsrow)) {
            $planet[] = $p;
        }

        $parse['mount'] = count($planet) + 1;

        foreach ($planet as $p) {
            
            for ($k = 0; $k < 8; $k++) {
                $parse[$f[$k]] = isset($parse[$f[$k]]) ? $parse[$f[$k]] : '';
                $data['text'] = $datat[$k];
                $parse[$f[$k]] .= parent::$page->parseTemplate($EmpireRowTPL, $data);
            }

            foreach ($resource as $i => $res) {
                $r[$i] = isset($r[$i]) ? $r[$i] : '';
                $data['text'] = (!isset($p[$resource[$i]]) && !isset($this->_current_user[$resource[$i]]) ) ? '0' : ( ( in_array($i, $reslist['build']) ) ? "<a href=\"game.php?page=" . DevelopmentsLib::setBuildingPage($i) . "&cp={$p['planet_id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$p[$resource[$i]]}</a>" : ( ( in_array($i, $reslist['tech']) ) ? "<a href=\"game.php?page=research&cp={$p['planet_id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$this->_current_user[$resource[$i]]}</a>" : ( ( in_array($i, $reslist['fleet']) ) ? "<a href=\"game.php?page=shipyard&cp={$p['planet_id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$p[$resource[$i]]}</a>" : ( ( in_array($i, $reslist['defense']) ) ? "<a href=\"game.php?page=defense&cp={$p['planet_id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$p[$resource[$i]]}</a>" : '0' ) ) ) );
                $r[$i] .= parent::$page->parseTemplate($EmpireRowTPL, $data);
            }
        }

        for ($j = 0; $j < 4; $j++) {
            foreach ($reslist[$m[$j]] as $a => $i) {
                $parse[$n[$j]] = isset($parse[$n[$j]]) ? $parse[$n[$j]] : '';
                $data['text'] = $this->_lang['tech'][$i];
                $parse[$n[$j]] .= "<tr>" . parent::$page->parseTemplate($EmpireRowTPL, $data) . $r[$i] . "</tr>";
            }
        }*/
    }

    /**
     * Set the planet image
     *
     * @param array $planet
     * @return array
     */
    private function setImage(array $planet): array
    {
        return [
            'planet_id' => $planet['planet_id'],
            'planet_image' => $planet['planet_image'],
            'dpath' => DPATH
        ];
    }

    /**
     * Set the planet image
     *
     * @param array $planet
     * @return string
     */
    private function setName(array $planet): array
    {
        return [
            'planet_name' => $planet['planet_name']
        ];
    }

    /**
     * Set the planet coordinates
     *
     * @param array $planet
     * @return array
     */
    private function setCoords(array $planet): array 
    {
        return [
            'planet_coords' => FormatLib::prettyCoords($planet['planet_galaxy'], $planet['planet_system'], $planet['planet_planet']),
            'planet_galaxy' => $planet['planet_galaxy'],
            'planet_system' => $planet['planet_system']
        ];
    }

    /**
     * Set the planet fields
     *
     * @param array $planet
     * @return array
     */
    private function setFields(array $planet): array 
    {
        return [
            'planet_field_current' => $planet['planet_field_current'],
            'planet_field_max' => $planet['planet_field_max']
        ];
    }

    /**
     * Set the planet resources
     *
     * @param array $planet
     * @param string $resource
     * @return array
     */
    private function setResources(array $planet, string $resource): array
    {
        if ($resource == 'energy') {

            return [
                'used_energy' => (FormatLib::prettyNumber($planet['planet_energy_max'] - $planet['planet_energy_used'])),
                'max_energy' => FormatLib::prettyNumber($planet['planet_energy_max'])
            ];
        }

        return [
            'planet_id' => $planet['planet_id'],
            'planet_type' => $planet['planet_type'],
            'planet_current_amount' => FormatLib::prettyNumber($planet['planet_' . $resource]),
            'planet_production' => (
                FormatLib::prettyNumber($planet['planet_' . $resource .  '_perhour'] + FunctionsLib::readConfig($resource . '_basic_income'))
            )
        ];
    }
}

/* end of empire.php */
