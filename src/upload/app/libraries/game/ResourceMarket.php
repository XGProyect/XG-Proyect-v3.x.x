<?php declare (strict_types = 1);

/**
 * Resource Market
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\libraries\game;

use App\core\entities\BuildingsEntity;
use App\core\entities\PlanetEntity;
use App\core\entities\PremiumEntity;
use App\core\entities\UserEntity;
use App\libraries\ProductionLib as Production;

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
     * Contains the current user data
     *
     * @var \UserEntity
     */
    private $user;

    /**
     * Contains the current user premium data
     *
     * @var \PremiumEntity
     */
    private $premium;

    /**
     * Contains the current planet data
     *
     * @var \PlanetEntity
     */
    private $planet;

    /**
     * Contains the current planet buildings
     *
     * @var \BuildingEntity
     */
    private $buildings;

    /**
     * Constructor
     *
     * @param array $user
     * @param array $planet
     */
    public function __construct(array $user, array $planet)
    {
        $this->setUpUser($user);
        $this->setUpPremium($user);
        $this->setUpPlanet($planet);
        $this->setUpBuildings($planet);
    }

    /**
     * Calculate the base price
     *
     * @param integer $max_storage
     * @param integer $base_dm
     * @return float
     */
    public function calculateBasePriceToRefill(int $max_storage, int $base_dm): float
    {
        // (max_storage_capacity * 0.10) * base_dark_maatter / (max_initial_storage * 0.10)
        return ($max_storage * 0.10) * $base_dm / (Production::maxStorable(0) * 0.10);
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
            $resource,
            10
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
            $resource,
            50
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
            $resource,
            100,
            $this->planet->{'getPlanetAmountOf' . ucfirst($resource)}()
        );
    }

    /**
     * Get the price to refill the storage
     *
     * @param integer $max_storage
     * @param integer $base_dm
     * @param integer $percentage
     * @param float $current_resources
     * @return float
     */
    public function calculateRefillStoragePrice(string $resource, int $percentage, float $current_resources = 0): float
    {
        $max_storage = Production::maxStorable($this->buildings->{'getBuilding' . ucfirst($resource) . 'Store'}());
        $base_price = $this->calculateBasePriceToRefill($max_storage, BASIC_RESOURCE_MARKET_DM[$resource]);

        return floor((($max_storage - $current_resources) * $percentage / $max_storage) * $base_price / 10);
    }

    /**
     * Check if the metal storage is full, returns true if full
     *
     * @return boolean
     */
    public function isMetalStorageFull(): bool
    {
        return (Production::maxStorable($this->buildings->getBuildingMetalStore()) <= $this->planet->getPlanetAmountOfMetal());
    }

    /**
     * Check if the crystal storage is full, returns true if full
     *
     * @return boolean
     */
    public function isCrystalStorageFull(): bool
    {
        return (Production::maxStorable($this->buildings->getBuildingCrystalStore()) <= $this->planet->getPlanetAmountOfCrystal());
    }

    /**
     * Check if the deuterium storage is full, returns true if full
     *
     * @return boolean
     */
    public function isDeuteriumStorageFull(): bool
    {
        return (Production::maxStorable($this->buildings->getBuildingDeuteriumStore()) <= $this->planet->getPlanetAmountOfDeuterium());
    }

    /**
     * Get the amount of resources that we will refill based on the provided percentage
     *
     * @param int $percentage
     * @return float
     */
    public function getProjectedResouces(string $resource, int $percentage): float
    {
        $amount_to_fill = Production::maxStorable(
            $this->buildings->{'getBuilding' . ucfirst($resource) . 'Store'}()
        ) * $percentage / 100;

        if ($percentage != 100) {
            return $this->planet->{'getPlanetAmountOf' . ucfirst($resource)}() + $amount_to_fill;
        }

        return $amount_to_fill;
    }

    /**
     * Check if the metal storage is fillable up to the provided percentage, returns true if it is
     *
     * @param integer $percentage
     * @return boolean
     */
    public function isMetalStorageFillable(int $percentage): bool
    {
        return $this->isStorageFillable('metal', $percentage);
    }

    /**
     * Check if the crystal storage is fillable up to the provided percentage, returns true if it is
     *
     * @param integer $percentage
     * @return boolean
     */
    public function isCrystalStorageFillable(int $percentage): bool
    {
        return $this->isStorageFillable('crystal', $percentage);
    }

    /**
     * Check if the deuterium storage is fillable up to the provided percentage, returns true if it is
     *
     * @param integer $percentage
     * @return boolean
     */
    public function isDeuteriumStorageFillable(int $percentage): bool
    {
        return $this->isStorageFillable('deuterium', $percentage);
    }

    /**
     * Check if the user can pay for the storage refill
     *
     * @param string $resource
     * @param integer $percentage
     * @return boolean
     */
    public function isRefillPayable(string $resource, int $percentage): bool
    {
        return ($this->{'getPriceToFill' . $percentage . 'Percent'}($resource) <= $this->premium->getPremiumDarkMatter());
    }

    /**
     * Check if the storage is fillable up to the provided percentage, returns true if it is
     *
     * @param string $resource
     * @param integer $percentage
     * @return boolean
     */
    private function isStorageFillable(string $resource, int $percentage): bool
    {
        if ($this->{'is' . ucfirst($resource) . 'StorageFull'}()) {
            return false;
        }

        return (Production::maxStorable($this->buildings->{'getBuilding' . ucfirst($resource) . 'Store'}()) >= $this->getProjectedResouces($resource, $percentage));
    }

    /**
     * Set up the user
     *
     * @param array $user
     *
     * @return void
     */
    private function setUpUser($user): void
    {
        $this->user = $this->createNewUserEntity($user);
    }

    /**
     * Set up the user premium data
     *
     * @param array $user
     *
     * @return void
     */
    private function setUpPremium($user): void
    {
        $this->premium = $this->createNewPremiumEntity($user);
    }

    /**
     * Set up the planet
     *
     * @param array $planet
     *
     * @return void
     */
    private function setUpPlanet($planet): void
    {
        $this->planet = $this->createNewPlanetEntity($planet);
    }

    /**
     * Set up the plaanet buildings
     *
     * @param array $planet
     *
     * @return void
     */
    private function setUpBuildings($planet): void
    {
        $this->buildings = $this->createNewBuildingsEntity($planet);
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
     * Create a new instance of PremiumEntity
     *
     * @param array $user
     *
     * @return \PremiumEntity
     */
    private function createNewPremiumEntity($user)
    {
        return new PremiumEntity($user);
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

    /**
     * Create a new instance of BuildingsEntity
     *
     * @param array $planet
     *
     * @return \BuildingsEntity
     */
    private function createNewBuildingsEntity($planet)
    {
        return new BuildingsEntity($planet);
    }
}
