<?php

declare (strict_types = 1);

/**
 * Resource Market
 *
 * PHP Version 7.1+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\libraries\game;

use application\core\entities\PlanetEntity;
use application\core\entities\UserEntity;

/**
 * ResourceMarket Class
 *
 * @category Classes
 * @package  alliance
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class ResourceMarket
{
    /**
     *
     * @var \UserEntity
     */
    private $resource_market_user;

    /**
     *
     * @var \PlanetEntity
     */
    private $resource_market_planet;

    /**
     * Constructor
     *
     * @param array $user
     * @param array $planet
     */
    public function __construct(array $user, array $planet)
    {
        $this->setUpUser($user);
        $this->setUpPlanet($planet);
    }

    /**
     * Calculate the base price
     *
     * @param integer $max_storage
     * @param integer $base_dm
     * @param integer $max_storage_initial_level
     * @return float
     */
    public function calculateBasePriceToRefill(int $max_storage, int $base_dm, int $max_storage_initial_level): float
    {
        return ($max_storage * 0.10) * $base_dm / ($max_storage_initial_level * 0.10);
    }

    /**
     * Get the price to refill the storage a 10%
     *
     * @param string $resource
     * @return float
     */
    public function getPriceToFill10Percent(string $resource): float
    {
        return $this->calculateRefillStoragePrice(
            $this->resource_market_planet->{'getPlanetStorageCapacity' . ucfirst($resource)}(),
            BASIC_RESOURCE_MARKET_DM[$resource],
            10,
            10000
        );
    }

    /**
     * Get the price to refill the storage a 50%
     *
     * @param string $resource
     * @return float
     */
    public function getPriceToFill50Percent(string $resource): float
    {
        return $this->calculateRefillStoragePrice(
            $this->resource_market_planet->{'getPlanetStorageCapacity' . ucfirst($resource)}(),
            BASIC_RESOURCE_MARKET_DM[$resource],
            50,
            10000
        );
    }

    /**
     * Get the price to completely refill the storage
     *
     * @param string $resource
     * @return float
     */
    public function getPriceToFill100Percent(string $resource): float
    {
        return $this->calculateRefillStoragePrice(
            $this->resource_market_planet->{'getPlanetStorageCapacity' . ucfirst($resource)}(),
            BASIC_RESOURCE_MARKET_DM[$resource],
            100,
            10000,
            $this->resource_market_planet->{'getPlanetAmountOf' . ucfirst($resource)}()
        );
    }

    /**
     * Get the price to refill the storage
     *
     * @param integer $max_storage
     * @param integer $base_dm
     * @param integer $percentage
     * @param float $current_resources
     * @return integer
     */
    public function calculateRefillStoragePrice(int $max_storage, int $base_dm, int $percentage, int $max_storage_initial_level, float $current_resources = 0): float
    {
        return (($max_storage - $current_resources) * $percentage / $max_storage) * $this->calculateBasePriceToRefill($max_storage, $base_dm, $max_storage_initial_level) / 10;
    }

    /**
     * Get all the preferences
     *
     * @return array
     */
    public function getResourceMarketUser(): array
    {
        $list_of_preferences = [];

        foreach ($this->preferences as $preference) {
            if (($preference instanceof PreferenceEntity)) {
                $list_of_preferences[] = $preference;
            }
        }

        return $list_of_preferences;
    }

    /**
     * Return current preference data
     *
     * @return \PreferencesEntity
     */
    public function getCurrentPreference(): PreferencesEntity
    {
        return $this->preferences[0];
    }

    /**
     * Set up the list of preferences
     *
     * @param array $preferences Preferences
     *
     * @return void
     */
    private function setUpUser($user): void
    {
        $this->resource_market_user = $this->createNewUserEntity($user);
    }

    /**
     * Set up the list of preferences
     *
     * @param array $preferences Preferences
     *
     * @return void
     */
    private function setUpPlanet($planet): void
    {
        $this->resource_market_planet = $this->createNewPlanetEntity($planet);
    }

    /**
     * Create a new instance of UserEntity
     *
     * @param array $user
     *
     * @return \UserEntity
     */
    private function createNewUserEntity($user)
    {
        return new UserEntity($user);
    }

    /**
     * Create a new instance of PlanetEntity
     *
     * @param array $planet
     *
     * @return \PlanetEntity
     */
    private function createNewPlanetEntity($planet)
    {
        return new PlanetEntity($planet);
    }
}

/* end of ResourceMarket.php */
