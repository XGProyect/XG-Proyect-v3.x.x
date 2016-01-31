<?php

/**
 * Trader Controller.
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
use application\libraries\FunctionsLib;
use application\libraries\ProductionLib;
use Exception;

/**
 * Trader Class.
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
class Trader extends XGPCore
{
    const MODULE_ID = 5;

    private $_lang;
    private $_resource;
    private $_tr_dark_matter;
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

        $this->_lang           = parent::$lang;
        $this->_resource       = parent::$objects->getObjects();
        $this->_tr_dark_matter = FunctionsLib::read_config('trader_darkmatter');
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
        $parse = $this->_lang;

        if ($this->_current_user['premium_dark_matter'] < $this->_tr_dark_matter) {
            FunctionsLib::message(str_replace('%s', $this->_tr_dark_matter, $this->_lang['tr_darkmatter_needed']), '', '', true);
            die();
        }

        if (isset($_POST['ress']) && $_POST['ress'] != '') {
            switch ($_POST['ress']) {
                case 'metal':
                {
                    if ($_POST['cristal'] < 0 or $_POST['deut'] < 0) {
                        FunctionsLib::message($this->_lang['tr_only_positive_numbers'], 'game.php?page=trader', 1);
                    } else {
                        $necessaire = (($_POST['cristal'] * 2) + ($_POST['deut'] * 4));
                        $amout      = array(
                                                'metal'     => 0,
                                                'crystal'   => $_POST['cristal'],
                                                'deuterium' => $_POST['deut'],
                                            );

                        $storage = $this->check_storage($amout);

                        if (is_string($storage)) {
                            die(FunctionsLib::message($storage, 'game.php?page=trader', '2'));
                        }

                        if ($this->_current_planet['planet_metal'] > $necessaire) {
                            parent::$db->query('UPDATE ' . PLANETS . ' SET
													`planet_metal` = `planet_metal` - ' . round($necessaire) . ',
													`planet_crystal` = `planet_crystal` + ' . round($_POST['cristal']) . ',
													`planet_deuterium` = `planet_deuterium` + ' . round($_POST['deut']) . "
													WHERE `planet_id` = '" . $this->_current_planet['planet_id'] . "';");

                            $this->_current_planet['planet_metal']     -= $necessaire;
                            $this->_current_planet['planet_crystal']   += isset($_POST['cristal']) ? $_POST['cristal'] : 0;
                            $this->_current_planet['planet_deuterium'] += isset($_POST['deut']) ? $_POST['deut'] : 0;

                            $this->discount_dark_matter(); // REDUCE DARKMATTER
                        } else {
                            FunctionsLib::message($this->_lang['tr_not_enought_metal'], 'game.php?page=trader', 1);
                        }
                    }
                    break;
                }
                case 'cristal':
                {
                    if ($_POST['metal'] < 0 or $_POST['deut'] < 0) {
                        FunctionsLib::message($this->_lang['tr_only_positive_numbers'], 'game.php?page=trader', 1);
                    } else {
                        $necessaire = ((abs($_POST['metal']) * 0.5) + (abs($_POST['deut']) * 2));
                        $amout      = array(
                                                'metal'     => $_POST['metal'],
                                                'crystal'   => 0,
                                                'deuterium' => $_POST['deut'],
                                            );

                        $storage = $this->check_storage($amout);

                        if (is_string($storage)) {
                            die(FunctionsLib::message($storage, 'game.php?page=trader', '2'));
                        }

                        if ($this->_current_planet['planet_crystal'] > $necessaire) {
                            parent::$db->query('UPDATE ' . PLANETS . ' SET
													`planet_metal` = `planet_metal` + ' . round($_POST['metal']) . ',
													`planet_crystal` = `planet_crystal` - ' . round($necessaire) . ',
													`planet_deuterium` = `planet_deuterium` + ' . round($_POST['deut']) . "
													WHERE `planet_id` = '" . $this->_current_planet['planet_id'] . "';");

                            $this->_current_planet['planet_metal']     += isset($_POST['metal']) ? $_POST['metal'] : 0;
                            $this->_current_planet['planet_crystal']   -= $necessaire;
                            $this->_current_planet['planet_deuterium'] += isset($_POST['deut']) ? $_POST['deut'] : 0;

                            $this->discount_dark_matter($this->_current_user); // REDUCE DARKMATTER
                        } else {
                            FunctionsLib::message($this->_lang['tr_not_enought_crystal'], 'game.php?page=trader', 1);
                        }
                    }
                    break;
                }
                case 'deuterium':
                {
                    if ($_POST['cristal'] < 0 or $_POST['metal'] < 0) {
                        FunctionsLib::message($this->_lang['tr_only_positive_numbers'], 'game.php?page=trader', 1);
                    } else {
                        $necessaire = ((abs($_POST['metal']) * 0.25) + (abs($_POST['cristal']) * 0.5));
                        $amout      = array(
                                                'metal'     => $_POST['metal'],
                                                'crystal'   => $_POST['cristal'],
                                                'deuterium' => 0,
                                            );

                        $storage = $this->check_storage($amout);

                        if (is_string($storage)) {
                            die(message($storage, 'game.php?page=trader', '2'));
                        }
                        if ($this->_current_planet['planet_deuterium'] > $necessaire) {
                            parent::$db->query('UPDATE ' . PLANETS . ' SET
													`planet_metal` = `planet_metal` + ' . round($_POST['metal']) . ',
													`planet_crystal` = `planet_crystal` + ' . round($_POST['cristal']) . ',
													`planet_deuterium` = `planet_deuterium` - ' . round($necessaire) . "
													WHERE `planet_id` = '" . $this->_current_planet['planet_id'] . "';");

                            $this->_current_planet['planet_metal']     += isset($_POST['metal']) ? $_POST['metal'] : 0;
                            $this->_current_planet['planet_crystal']   += isset($_POST['cristal']) ? $_POST['cristal'] : 0;
                            $this->_current_planet['planet_deuterium'] -= $necessaire;

                            $this->discount_dark_matter($this->_current_user); // REDUCE DARKMATTER
                        } else {
                            FunctionsLib::message($this->_lang['tr_not_enought_deuterium'], 'game.php?page=trader', 1);
                        }
                    }
                    break;
                }
            }

            FunctionsLib::message($this->_lang['tr_exchange_done'], 'game.php?page=trader', 1);
        } else {
            $template = parent::$page->get_template('trader/trader_main');

            if (isset($_POST['action'])) {
                $parse['mod_ma_res'] = '1';

                switch ((isset($_POST['choix']) ? $_POST['choix'] : null)) {
                    case 'metal':

                        $template              = parent::$page->get_template('trader/trader_metal');
                        $parse['mod_ma_res_a'] = '2';
                        $parse['mod_ma_res_b'] = '4';

                    break;

                    case 'cristal':

                        $template              = parent::$page->get_template('trader/trader_cristal');
                        $parse['mod_ma_res_a'] = '0.5';
                        $parse['mod_ma_res_b'] = '2';

                    break;

                    case 'deut':

                        $template              = parent::$page->get_template('trader/trader_deuterium');
                        $parse['mod_ma_res_a'] = '0.25';
                        $parse['mod_ma_res_b'] = '0.5';

                    break;
                }
            }
        }

        parent::$page->display(parent::$page->parse_template($template, $parse));
    }

    /**
     * method check_storage
     * param $amount
     * param $force
     * return amount of resource production.
     */
    public function check_storage($amount, $force = null)
    {
        if (!is_array($amount)) {
            throw new Exception('Must be array', 1);
        }

        $hangar = array('metal' => 22, 'crystal' => 23, 'deuterium' => 24);
        $check  = array();

        foreach ($hangar as $k => $v) {
            if ($amount[$k] == 0) {
                unset($amount[$k]);
            }

            if (array_key_exists($k, $amount)) {
                if ($this->_current_planet[$k] + $amount[$k] >= ProductionLib::maxStorable($this->_current_planet[$this->_resource[$v]])) {
                    $check[$k] = false;
                } else {
                    $check[$k] = true;
                }
            } else {
                $check[$k] = true;
            }
        }

        if ($check['metal'] === true && $check['crystal'] === true && $check['deuterium'] === true) {
            return false;
        } else {
            if (is_null($force)) {
                foreach ($hangar as $k => $v) {
                    if ($check[$k] === false) {
                        return sprintf($this->_lang['tr_full_storage'], strtolower($this->_lang['info'][$v]['name']));
                    } else {
                        continue;
                    }
                }
            } else {
                return $check;
            }
        }
    }

    /**
     * method discount_dark_matter
     * param
     * return reduce dark matter from the current user.
     */
    private function discount_dark_matter()
    {
        parent::$db->query('UPDATE `' . PREMIUM . '` SET
								`premium_dark_matter` = `premium_dark_matter` - ' . $this->_tr_dark_matter . '
								WHERE `premium_user_id` = ' . $this->_current_user['user_id'] . '');
    }
}

/* end of trader.php */
