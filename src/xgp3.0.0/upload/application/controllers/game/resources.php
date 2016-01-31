<?php

/**
 * Resources Controller.
 *
 * PHP Version 5.5+
 *
 * @category Controller
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */

namespace application\controllers\game;

use application\core\XGPCore;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\OfficiersLib;
use application\libraries\ProductionLib;

/**
 * Resources Class.
 *
 * @category Classes
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */
class Resources extends XGPCore
{
    const MODULE_ID = 4;

    private $_lang;
    private $_resource;
    private $_prod_grid;
    private $_reslist;
    private $_current_user;
    private $_current_planet;

    /**
     * __construct().
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->check_session();

        // Check module access
        FunctionsLib::module_message(FunctionsLib::is_module_accesible(self::MODULE_ID));

        $this->_lang = parent::$lang;

        $this->_resource       = parent::$objects->getObjects();
        $this->_prod_grid      = parent::$objects->getProduction();
        $this->_reslist        = parent::$objects->getObjectsList();
        $this->_current_user   = parent::$users->get_user_data();
        $this->_current_planet = parent::$users->get_planet_data();

        $this->build_page();
    }

    /**
     * method __destruct
     * param
     * return close db connection.
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything.
     */
    private function build_page()
    {
        $parse                       = $this->_lang;
        $game_metal_basic_income     = FunctionsLib::read_config('metal_basic_income');
        $game_crystal_basic_income   = FunctionsLib::read_config('crystal_basic_income');
        $game_deuterium_basic_income = FunctionsLib::read_config('deuterium_basic_income');
        $game_energy_basic_income    = FunctionsLib::read_config('energy_basic_income');
        $game_resource_multiplier    = FunctionsLib::read_config('resource_multiplier');

        if ($this->_current_planet['planet_type'] == 3) {
            $game_metal_basic_income     = 0;
            $game_crystal_basic_income   = 0;
            $game_deuterium_basic_income = 0;
        }

        $this->_current_planet['planet_metal_max']     = ProductionLib::maxStorable($this->_current_planet[ $this->_resource[22] ]);
        $this->_current_planet['planet_crystal_max']   = ProductionLib::maxStorable($this->_current_planet[ $this->_resource[23] ]);
        $this->_current_planet['planet_deuterium_max'] = ProductionLib::maxStorable($this->_current_planet[ $this->_resource[24] ]);

        $parse['production_level'] = 100;
        $post_porcent              = ProductionLib::maxProduction($this->_current_planet['planet_energy_max'], $this->_current_planet['planet_energy_used']);

        $parse['resource_row']                             = '';
        $this->_current_planet['planet_metal_perhour']     = 0;
        $this->_current_planet['planet_crystal_perhour']   = 0;
        $this->_current_planet['planet_deuterium_perhour'] = 0;
        $this->_current_planet['planet_energy_max']        = 0;
        $this->_current_planet['planet_energy_used']       = 0;

        $BuildTemp       = $this->_current_planet[ 'planet_temp_max' ];
        $ResourcesRowTPL = parent::$page->get_template('resources/resources_row');

        foreach ($this->_reslist['prod'] as $ProdID) {
            if ($this->_current_planet[$this->_resource[$ProdID]] > 0 && isset($this->_prod_grid[$ProdID])) {
                $BuildLevelFactor = $this->_current_planet[ 'planet_' . $this->_resource[$ProdID] . '_porcent' ];
                $BuildLevel       = $this->_current_planet[ $this->_resource[$ProdID] ];
                $BuildEnergy      = $this->_current_user['research_energy_technology'];

                // BOOST
                $geologe_boost  = 1 + (1 * (OfficiersLib::isOfficierActive($this->_current_user['premium_officier_geologist']) ? GEOLOGUE : 0));
                $engineer_boost = 1 + (1 * (OfficiersLib::isOfficierActive($this->_current_user['premium_officier_engineer']) ? ENGINEER_ENERGY : 0));

                // PRODUCTION FORMULAS
                $metal_prod     = eval($this->_prod_grid[$ProdID]['formule']['metal']);
                $crystal_prod   = eval($this->_prod_grid[$ProdID]['formule']['crystal']);
                $deuterium_prod = eval($this->_prod_grid[$ProdID]['formule']['deuterium']);
                $energy_prod    = eval($this->_prod_grid[$ProdID]['formule']['energy']);

                // PRODUCTION
                $metal     = ProductionLib::productionAmount($metal_prod, $geologe_boost);
                $crystal   = ProductionLib::productionAmount($crystal_prod, $geologe_boost);
                $deuterium = ProductionLib::productionAmount($deuterium_prod, $geologe_boost);

                if ($ProdID >= 4) {
                    $energy = ProductionLib::productionAmount($energy_prod, $engineer_boost, true);
                } else {
                    $energy = ProductionLib::productionAmount($energy_prod, 1, true);
                }

                if ($energy > 0) {
                    $this->_current_planet['planet_energy_max']        += $energy;
                } else {
                    $this->_current_planet['planet_energy_used']    += $energy;
                }

                $this->_current_planet['planet_metal_perhour']         += $metal;
                $this->_current_planet['planet_crystal_perhour']    += $crystal;
                $this->_current_planet['planet_deuterium_perhour']    += $deuterium;

                $metal                     = ProductionLib::currentProduction($metal, $post_porcent);
                $crystal                   = ProductionLib::currentProduction($crystal, $post_porcent);
                $deuterium                 = ProductionLib::currentProduction($deuterium, $post_porcent);
                $energy                    = ProductionLib::currentProduction($energy, $post_porcent);
                $Field                     = 'planet_' . $this->_resource[$ProdID] . '_porcent';
                $CurrRow                   = array();
                $CurrRow['name']           = $this->_resource[$ProdID];
                $CurrRow['porcent']        = $this->_current_planet[$Field];
                $CurrRow['option']         = $this->build_options($CurrRow['porcent']);
                $CurrRow['type']           = $this->_lang['tech'][$ProdID];
                $CurrRow['level']          = ($ProdID > 200) ? $this->_lang['rs_amount'] : $this->_lang['rs_lvl'];
                $CurrRow['level_type']     = $this->_current_planet[ $this->_resource[$ProdID] ];
                $CurrRow['metal_type']     = FormatLib::pretty_number($metal);
                $CurrRow['crystal_type']   = FormatLib::pretty_number($crystal);
                $CurrRow['deuterium_type'] = FormatLib::pretty_number($deuterium);
                $CurrRow['energy_type']    = FormatLib::pretty_number($energy);
                $CurrRow['metal_type']     = FormatLib::color_number($CurrRow['metal_type']);
                $CurrRow['crystal_type']   = FormatLib::color_number($CurrRow['crystal_type']);
                $CurrRow['deuterium_type'] = FormatLib::color_number($CurrRow['deuterium_type']);
                $CurrRow['energy_type']    = FormatLib::color_number($CurrRow['energy_type']);
                $parse['resource_row']              .= parent::$page->parse_template($ResourcesRowTPL, $CurrRow);
            }
        }

        $parse['Production_of_resources_in_the_planet'] = str_replace('%s', $this->_current_planet['planet_name'], $this->_lang['rs_production_on_planet']);

        $parse['production_level']       = $this->prod_level($this->_current_planet['planet_energy_used'], $this->_current_planet['planet_energy_max']);
        $parse['metal_basic_income']     = $game_metal_basic_income;
        $parse['crystal_basic_income']   = $game_crystal_basic_income;
        $parse['deuterium_basic_income'] = $game_deuterium_basic_income;
        $parse['energy_basic_income']    = $game_energy_basic_income;
        $parse['planet_metal_max']       = $this->resource_color($this->_current_planet['planet_metal'], $this->_current_planet['planet_metal_max']);
        $parse['planet_crystal_max']     = $this->resource_color($this->_current_planet['planet_crystal'], $this->_current_planet['planet_crystal_max']);
        $parse['planet_deuterium_max']   = $this->resource_color($this->_current_planet['planet_deuterium'], $this->_current_planet['planet_deuterium_max']);

        $parse['metal_total']     = FormatLib::color_number(FormatLib::pretty_number(floor((($this->_current_planet['planet_metal_perhour']     * 0.01 * $parse['production_level']) + $parse['metal_basic_income']))));
        $parse['crystal_total']   = FormatLib::color_number(FormatLib::pretty_number(floor((($this->_current_planet['planet_crystal_perhour']   * 0.01 * $parse['production_level']) + $parse['crystal_basic_income']))));
        $parse['deuterium_total'] = FormatLib::color_number(FormatLib::pretty_number(floor((($this->_current_planet['planet_deuterium_perhour'] * 0.01 * $parse['production_level']) + $parse['deuterium_basic_income']))));
        $parse['energy_total']    = FormatLib::color_number(FormatLib::pretty_number(floor(($this->_current_planet['planet_energy_max'] + $parse['energy_basic_income']) + $this->_current_planet['planet_energy_used'])));

        $parse['daily_metal']  = $this->calculate_daily($this->_current_planet['planet_metal_perhour'], $parse['production_level'], $parse['metal_basic_income']);
        $parse['weekly_metal'] = $this->calculate_weekly($this->_current_planet['planet_metal_perhour'], $parse['production_level'], $parse['metal_basic_income']);

        $parse['daily_crystal']  = $this->calculate_daily($this->_current_planet['planet_crystal_perhour'], $parse['production_level'], $parse['crystal_basic_income']);
        $parse['weekly_crystal'] = $this->calculate_weekly($this->_current_planet['planet_crystal_perhour'], $parse['production_level'], $parse['crystal_basic_income']);

        $parse['daily_deuterium']  = $this->calculate_daily($this->_current_planet['planet_deuterium_perhour'], $parse['production_level'], $parse['deuterium_basic_income']);
        $parse['weekly_deuterium'] = $this->calculate_weekly($this->_current_planet['planet_deuterium_perhour'], $parse['production_level'], $parse['deuterium_basic_income']);

        $parse['daily_metal']  = FormatLib::color_number(FormatLib::pretty_number($parse['daily_metal']));
        $parse['weekly_metal'] = FormatLib::color_number(FormatLib::pretty_number($parse['weekly_metal']));

        $parse['daily_crystal']  = FormatLib::color_number(FormatLib::pretty_number($parse['daily_crystal']));
        $parse['weekly_crystal'] = FormatLib::color_number(FormatLib::pretty_number($parse['weekly_crystal']));

        $parse['daily_deuterium']  = FormatLib::color_number(FormatLib::pretty_number($parse['daily_deuterium']));
        $parse['weekly_deuterium'] = FormatLib::color_number(FormatLib::pretty_number($parse['weekly_deuterium']));

        $ValidList['percent'] = array(0,  10,  20,  30,  40,  50,  60,  70,  80,  90, 100);
        $SubQry               = '';

        if ($_POST && !parent::$users->is_on_vacations($this->_current_user)) {
            foreach ($_POST as $Field => $Value) {
                $FieldName = 'planet_' . $Field . '_porcent';
                if (isset($this->_current_planet[$FieldName])) {
                    if (!in_array($Value, $ValidList['percent'])) {
                        FunctionsLib::redirect('game.php?page=resourceSettings');
                    }

                    $Value                             = $Value / 10;
                    $this->_current_planet[$FieldName] = $Value;
                    $SubQry                      .= ', `' . $FieldName . "` = '" . $Value . "'";
                }
            }

            parent::$db->query('UPDATE ' . PLANETS . " SET
									`planet_id` = '" . $this->_current_planet['planet_id'] . "'
									$SubQry
									WHERE `planet_id` = '" . $this->_current_planet['planet_id'] . "';");

            FunctionsLib::redirect('game.php?page=resourceSettings');
        }

        parent::$page->display(parent::$page->parse_template(parent::$page->get_template('resources/resources'), $parse));
    }

    /**
     * method build_options
     * param $current_porcentage
     * return porcentage options for the select element.
     */
    private function build_options($current_porcentage)
    {
        $option_row = '';

        for ($option = 10; $option >= 0; --$option) {
            $opt_value = $option * 10;

            if ($option == $current_porcentage) {
                $opt_selected = ' selected=selected';
            } else {
                $opt_selected = '';
            }

            $option_row .= '<option value="' . $opt_value . '"' . $opt_selected . '>' . $opt_value . '%</option>';
        }

        return $option_row;
    }

    /**
     * method calculate_daily
     * param1 $prod_per_hour
     * param2 $prod_level
     * param3 $basic_income
     * return production per day.
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
     * return production per week.
     */
    private function calculate_weekly($prod_per_hour, $prod_level, $basic_income)
    {
        return floor(($basic_income + ($prod_per_hour * 0.01 * $prod_level)) * 24 * 7);
    }

    /**
     * method resource_color
     * param1 $current_amount
     * param2 $max_amount
     * return color depending on the current storage capacity.
     */
    private function resource_color($current_amount, $max_amount)
    {
        if ($max_amount < $current_amount) {
            return  FormatLib::color_red(FormatLib::pretty_number($max_amount / 1000) . 'k');
        } else {
            return  FormatLib::color_green(FormatLib::pretty_number($max_amount / 1000) . 'k');
        }
    }

    /**
     * method prod_level
     * param1 $energy_used
     * param2 $energy_max
     * return the production level based on the energy consumption.
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

/* end of resources.php */
