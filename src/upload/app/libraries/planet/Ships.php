<?php
/**
 * Ships
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\libraries\planet;

use App\core\entities\ShipsEntity;

/**
 * Ships Class
 *
 * @category Classes
 * @package  alliance
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Ships
{
    /**
     *
     * @var array
     */
    private $_ships = [];

    /**
     * Constructor
     *
     * @param array $planet_data Planet Data
     *
     * @return void
     */
    public function __construct($planet_data)
    {
        if (is_array($planet_data)) {
            $this->setUp($planet_data);
        }
    }

    /**
     * Get all the ships
     *
     * @return array
     */
    public function getShips()
    {
        $list_of_ships = [];

        foreach ($this->_ships as $ship) {
            if (($ship instanceof ShipsEntity)) {
                $list_of_ships[] = $ship;
            }
        }

        return $list_of_ships;
    }

    /**
     * Return current alliance data
     *
     * @return array
     */
    public function getCurrentShips()
    {
        return $this->getShips()[0];
    }

    /**
     * Set up the list of alliances
     *
     * @param array $ships Ships
     *
     * @return void
     */
    private function setUp($ships)
    {
        foreach ($ships as $ship) {
            $this->_ships[] = $this->createNewShipsEntity($ship);
        }
    }

    /**
     * Create a new instance of ShipsEntity
     *
     * @param array $ships Ships
     *
     * @return \ShipsEntity
     */
    private function createNewShipsEntity($ships)
    {
        return new ShipsEntity($ships);
    }
}
