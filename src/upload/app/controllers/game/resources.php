<?php
/**
 * Resources Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\controllers\game;

use App\core\BaseController;
use App\core\enumerators\PlanetTypesEnumerator;
use App\libraries\FormatLib;
use App\libraries\Functions;
use App\libraries\OfficiersLib;
use App\libraries\ProductionLib;

/**
 * Resources Class
 */
class Resources extends BaseController
{
    const MODULE_ID = 4;

    /**
     * @var mixed
     */
    private $_resource;
    /**
     * @var mixed
     */
    private $_prod_grid;
    /**
     * @var mixed
     */
    private $_reslist;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // load Model
        parent::loadModel('game/resources');

        // load Language
        parent::loadLang(['game/global', 'game/constructions', 'game/ships', 'game/resources']);

        // check if session is active
        parent::$users->checkSession();

        $this->_resource = parent::$objects->getObjects();
        $this->_prod_grid = parent::$objects->getProduction();
        $this->_reslist = parent::$objects->getObjectsList();
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

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
        $parse = $this->langs->language;

        $game_metal_basic_income = Functions::readConfig('metal_basic_income');
        $game_crystal_basic_income = Functions::readConfig('crystal_basic_income');
        $game_deuterium_basic_income = Functions::readConfig('deuterium_basic_income');
        $game_energy_basic_income = Functions::readConfig('energy_basic_income');
        $game_resource_multiplier = Functions::readConfig('resource_multiplier');

        if ($this->user['preference_vacation_mode'] > 0 or $this->planet['planet_type'] == PlanetTypesEnumerator::MOON) {
            $game_metal_basic_income = 0;
            $game_crystal_basic_income = 0;
            $game_deuterium_basic_income = 0;
        }

        $this->planet['planet_metal_max'] = ProductionLib::maxStorable($this->planet[$this->_resource[22]]);
        $this->planet['planet_crystal_max'] = ProductionLib::maxStorable($this->planet[$this->_resource[23]]);
        $this->planet['planet_deuterium_max'] = ProductionLib::maxStorable($this->planet[$this->_resource[24]]);

        $parse['production_level'] = 100;
        $post_percent = ProductionLib::maxProduction($this->planet['planet_energy_max'], $this->planet['planet_energy_used']);

        $parse['resource_row'] = '';
        $this->planet['planet_metal_perhour'] = 0;
        $this->planet['planet_crystal_perhour'] = 0;
        $this->planet['planet_deuterium_perhour'] = 0;
        $this->planet['planet_energy_max'] = 0;
        $this->planet['planet_energy_used'] = 0;

        $BuildTemp = $this->planet['planet_temp_max'];

        foreach ($this->_reslist['prod'] as $ProdID) {
            if ($this->planet[$this->_resource[$ProdID]] > 0 && isset($this->_prod_grid[$ProdID])) {
                $BuildLevelFactor = $this->planet['planet_' . $this->_resource[$ProdID] . '_percent'];
                $BuildLevel = $this->planet[$this->_resource[$ProdID]];
                $BuildEnergy = $this->user['research_energy_technology'];

                // BOOST
                $geologe_boost = 1 + (1 * (OfficiersLib::isOfficierActive($this->user['premium_officier_geologist']) ? GEOLOGUE : 0));
                $engineer_boost = 1 + (1 * (OfficiersLib::isOfficierActive($this->user['premium_officier_engineer']) ? ENGINEER_ENERGY : 0));

                // PRODUCTION FORMULAS
                $metal_prod = eval($this->_prod_grid[$ProdID]['formule']['metal']);
                $crystal_prod = eval($this->_prod_grid[$ProdID]['formule']['crystal']);
                $deuterium_prod = eval($this->_prod_grid[$ProdID]['formule']['deuterium']);
                $energy_prod = eval($this->_prod_grid[$ProdID]['formule']['energy']);

                // PRODUCTION
                $metal = ProductionLib::productionAmount($metal_prod, $geologe_boost, $game_resource_multiplier);
                $crystal = ProductionLib::productionAmount($crystal_prod, $geologe_boost, $game_resource_multiplier);
                $deuterium = ProductionLib::productionAmount($deuterium_prod, $geologe_boost, $game_resource_multiplier);

                if ($ProdID >= 4) {
                    $energy = ProductionLib::productionAmount($energy_prod, $engineer_boost, 0, true);
                } else {
                    $energy = ProductionLib::productionAmount($energy_prod, 1, 0, true);
                }

                if ($energy > 0) {
                    $this->planet['planet_energy_max'] += $energy;
                } else {
                    $this->planet['planet_energy_used'] += $energy;
                }

                $this->planet['planet_metal_perhour'] += $metal;
                $this->planet['planet_crystal_perhour'] += $crystal;
                $this->planet['planet_deuterium_perhour'] += $deuterium;

                $metal = ProductionLib::currentProduction($metal, $post_percent);
                $crystal = ProductionLib::currentProduction($crystal, $post_percent);
                $deuterium = ProductionLib::currentProduction($deuterium, $post_percent);
                $energy = ProductionLib::currentProduction($energy, $post_percent);
                $Field = 'planet_' . $this->_resource[$ProdID] . '_percent';
                $CurrRow = [];
                $CurrRow['name'] = $this->_resource[$ProdID];
                $CurrRow['percent'] = $this->planet[$Field];
                $CurrRow['option'] = $this->build_options($CurrRow['percent']);
                $CurrRow['type'] = $this->langs->language[$this->_resource[$ProdID]];
                $CurrRow['level'] = ($ProdID > 200) ? $this->langs->line('rs_amount') : $this->langs->line('level');
                $CurrRow['level_type'] = $this->planet[$this->_resource[$ProdID]];
                $CurrRow['metal_type'] = FormatLib::prettyNumber($metal);
                $CurrRow['crystal_type'] = FormatLib::prettyNumber($crystal);
                $CurrRow['deuterium_type'] = FormatLib::prettyNumber($deuterium);
                $CurrRow['energy_type'] = FormatLib::prettyNumber($energy);
                $CurrRow['metal_type'] = FormatLib::colorNumber($CurrRow['metal_type']);
                $CurrRow['crystal_type'] = FormatLib::colorNumber($CurrRow['crystal_type']);
                $CurrRow['deuterium_type'] = FormatLib::colorNumber($CurrRow['deuterium_type']);
                $CurrRow['energy_type'] = FormatLib::colorNumber($CurrRow['energy_type']);
                $parse['resource_row'] .= $this->getTemplate()->set(
                    'resources/resources_row',
                    $CurrRow
                );
            }
        }

        $parse['Production_of_resources_in_the_planet'] = str_replace('%s', $this->planet['planet_name'], $this->langs->line('rs_production_on_planet'));

        $parse['production_level'] = $this->prod_level($this->planet['planet_energy_used'], $this->planet['planet_energy_max']);
        $parse['metal_basic_income'] = $game_metal_basic_income;
        $parse['crystal_basic_income'] = $game_crystal_basic_income;
        $parse['deuterium_basic_income'] = $game_deuterium_basic_income;
        $parse['energy_basic_income'] = $game_energy_basic_income;
        $parse['planet_metal_max'] = $this->resource_color($this->planet['planet_metal'], $this->planet['planet_metal_max']);
        $parse['planet_crystal_max'] = $this->resource_color($this->planet['planet_crystal'], $this->planet['planet_crystal_max']);
        $parse['planet_deuterium_max'] = $this->resource_color($this->planet['planet_deuterium'], $this->planet['planet_deuterium_max']);

        $parse['metal_total'] = FormatLib::colorNumber(FormatLib::prettyNumber(floor((($this->planet['planet_metal_perhour'] * 0.01 * $parse['production_level']) + $parse['metal_basic_income']))));
        $parse['crystal_total'] = FormatLib::colorNumber(FormatLib::prettyNumber(floor((($this->planet['planet_crystal_perhour'] * 0.01 * $parse['production_level']) + $parse['crystal_basic_income']))));
        $parse['deuterium_total'] = FormatLib::colorNumber(FormatLib::prettyNumber(floor((($this->planet['planet_deuterium_perhour'] * 0.01 * $parse['production_level']) + $parse['deuterium_basic_income']))));
        $parse['energy_total'] = FormatLib::colorNumber(FormatLib::prettyNumber(floor(($this->planet['planet_energy_max'] + $parse['energy_basic_income']) + $this->planet['planet_energy_used'])));

        $parse['daily_metal'] = $this->calculate_daily($this->planet['planet_metal_perhour'], $parse['production_level'], $parse['metal_basic_income']);
        $parse['weekly_metal'] = $this->calculate_weekly($this->planet['planet_metal_perhour'], $parse['production_level'], $parse['metal_basic_income']);

        $parse['daily_crystal'] = $this->calculate_daily($this->planet['planet_crystal_perhour'], $parse['production_level'], $parse['crystal_basic_income']);
        $parse['weekly_crystal'] = $this->calculate_weekly($this->planet['planet_crystal_perhour'], $parse['production_level'], $parse['crystal_basic_income']);

        $parse['daily_deuterium'] = $this->calculate_daily($this->planet['planet_deuterium_perhour'], $parse['production_level'], $parse['deuterium_basic_income']);
        $parse['weekly_deuterium'] = $this->calculate_weekly($this->planet['planet_deuterium_perhour'], $parse['production_level'], $parse['deuterium_basic_income']);

        $parse['daily_metal'] = FormatLib::colorNumber(FormatLib::prettyNumber($parse['daily_metal']));
        $parse['weekly_metal'] = FormatLib::colorNumber(FormatLib::prettyNumber($parse['weekly_metal']));

        $parse['daily_crystal'] = FormatLib::colorNumber(FormatLib::prettyNumber($parse['daily_crystal']));
        $parse['weekly_crystal'] = FormatLib::colorNumber(FormatLib::prettyNumber($parse['weekly_crystal']));

        $parse['daily_deuterium'] = FormatLib::colorNumber(FormatLib::prettyNumber($parse['daily_deuterium']));
        $parse['weekly_deuterium'] = FormatLib::colorNumber(FormatLib::prettyNumber($parse['weekly_deuterium']));

        $ValidList['percent'] = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100];
        $SubQry = '';

        if ($_POST && !parent::$users->isOnVacations($this->user)) {
            foreach ($_POST as $Field => $Value) {
                $FieldName = 'planet_' . $Field . '_percent';
                if (isset($this->planet[$FieldName])) {
                    if (!in_array($Value, $ValidList['percent'])) {
                        Functions::redirect('game.php?page=resourceSettings');
                    }

                    $Value = $Value / 10;
                    $this->planet[$FieldName] = $Value;
                    $SubQry .= ", `" . $FieldName . "` = '" . $Value . "'";
                }
            }

            $this->Resources_Model->updateCurrentPlanet($this->planet, $SubQry);

            Functions::redirect('game.php?page=resourceSettings');
        }

        parent::$page->display(
            $this->getTemplate()->set(
                'resources/resources',
                $parse
            )
        );
    }

    /**
     * method build_options
     * param $current_percentage
     * return percentage options for the select element
     */
    private function build_options($current_porcentage)
    {
        $option_row = '';

        for ($option = 10; $option >= 0; $option--) {
            $opt_value = $option * 10;

            if ($option == $current_porcentage) {
                $opt_selected = " selected=selected";
            } else {
                $opt_selected = "";
            }

            $option_row .= "<option value=\"" . $opt_value . "\"" . $opt_selected . ">" . $opt_value . "%</option>";
        }

        return $option_row;
    }

    /**
     * method calculate_daily
     * param1 $prod_per_hour
     * param2 $prod_level
     * param3 $basic_income
     * return production per day
     */
    private function calculate_daily($prod_per_hour, $prod_level, $basic_income)
    {
        return floor(($basic_income + ($prod_per_hour * 0.01 * $prod_level)) * 24);
    }

    /**
     * method calculate_weekly
     * param1 $prod_per_hour
     * param2 $prod_level
     * param3 $basic_income
     * return production per week
     */
    private function calculate_weekly($prod_per_hour, $prod_level, $basic_income)
    {
        return floor(($basic_income + ($prod_per_hour * 0.01 * $prod_level)) * 24 * 7);
    }

    /**
     * method resource_color
     * param1 $current_amount
     * param2 $max_amount
     * return color depending on the current storage capacity
     */
    private function resource_color($current_amount, $max_amount)
    {
        if ($max_amount < $current_amount) {
            return (FormatLib::colorRed(FormatLib::prettyNumber($max_amount / 1000) . 'k'));
        } else {
            return (FormatLib::colorGreen(FormatLib::prettyNumber($max_amount / 1000) . 'k'));
        }
    }

    /**
     * method prod_level
     * param1 $energy_used
     * param2 $energy_max
     * return the production level based on the energy consumption
     */
    private function prod_level($energy_used, $energy_max)
    {
        if ($energy_max == 0 && $energy_used > 0) {
            $prod_level = 0;
        } elseif ($energy_max > 0 && abs($energy_used) > $energy_max) {
            $prod_level = floor(($energy_max) / ($energy_used * -1) * 100);
        } elseif ($energy_max == 0 && abs($energy_used) > $energy_max) {
            $prod_level = 0;
        } else {
            $prod_level = 100;
        }

        if ($prod_level > 100) {
            $prod_level = 100;
        }

        return $prod_level;
    }
}
