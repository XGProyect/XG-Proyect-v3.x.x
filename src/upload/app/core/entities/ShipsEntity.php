<?php
/**
 * Ships entity
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\core\entities;

use Exception;

/**
 * Ships Entity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class ShipsEntity
{
    /**
     *
     * @var array
     */
    private $_ships = [];

    /**
     * Init with the ships data
     *
     * @param array $ships Ships
     *
     * @return void
     */
    public function __construct($ships)
    {
        $this->setShips($ships);
    }

    /**
     * Set the current ships
     *
     * @param array $ships Ships
     *
     * @throws Exception
     *
     * @return void
     */
    private function setShips($ships)
    {
        try {
            if (!is_array($ships)) {
                return null;
            }

            $this->_ships = $ships;
        } catch (Exception $e) {
            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Return the ship id
     *
     * @return string
     */
    public function getShipId()
    {
        return $this->_ships['ship_id'];
    }

    /**
     * Return the ship planet id
     *
     * @return string
     */
    public function getShipPlanetId()
    {
        return $this->_ships['ship_planet_id'];
    }

    /**
     * Return the ship small cargo ship
     *
     * @return string
     */
    public function getShipSmallCargoShip()
    {
        return $this->_ships['ship_small_cargo_ship'];
    }

    /**
     * Return the ship big cargo ship
     *
     * @return string
     */
    public function getShipBigCargoShip()
    {
        return $this->_ships['ship_big_cargo_ship'];
    }

    /**
     * Return the ship light fighter
     *
     * @return string
     */
    public function getShipLightFighter()
    {
        return $this->_ships['ship_light_fighter'];
    }

    /**
     * Return the ship heavy fighter
     *
     * @return string
     */
    public function getShipHeavyFighter()
    {
        return $this->_ships['ship_heavy_fighter'];
    }

    /**
     * Return the ship cruiser
     *
     * @return string
     */
    public function getShipCruiser()
    {
        return $this->_ships['ship_cruiser'];
    }

    /**
     * Return the ship battleship
     *
     * @return string
     */
    public function getShipBattleship()
    {
        return $this->_ships['ship_battleship'];
    }

    /**
     * Return the ship_colony_ship
     *
     * @return string
     */
    public function getShipColonyShip()
    {
        return $this->_ships['ship_colony_ship'];
    }

    /**
     * Return the ship_recycler
     *
     * @return string
     */
    public function getShipRecycler()
    {
        return $this->_ships['ship_recycler'];
    }

    /**
     * Return the ship espionage probe
     *
     * @return string
     */
    public function getShipEspionageProbe()
    {
        return $this->_ships['ship_espionage_probe'];
    }

    /**
     * Return the ship bomber
     *
     * @return string
     */
    public function getShipBomber()
    {
        return $this->_ships['ship_bomber'];
    }

    /**
     * Return the ship solar satellite
     *
     * @return string
     */
    public function getShipSolarSatellite()
    {
        return $this->_ships['ship_solar_satellite'];
    }

    /**
     * Return the ship destroyer
     *
     * @return string
     */
    public function getShipDestroyer()
    {
        return $this->_ships['ship_destroyer'];
    }

    /**
     * Return the ship deathstar
     *
     * @return string
     */
    public function getShipDeathstar()
    {
        return $this->_ships['ship_deathstar'];
    }

    /**
     * Return the ship battlecruiser
     *
     * @return string
     */
    public function getShipBattlecruiser()
    {
        return $this->_ships['ship_battlecruiser'];
    }
}
