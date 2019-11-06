<?php
/**
 * Trader Controller
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
use application\libraries\FormatLib as Format;
use application\libraries\FunctionsLib as Functions;
use application\libraries\ProductionLib;
use Exception;

/**
 * Trader Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Trader extends Controller
{

    const MODULE_ID = 5;

    /**
     * Current user data
     *
     * @var array
     */
    private $user;

    /**
     * Current planet data
     *
     * @var array
     */
    private $planet;

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
        parent::loadModel('game/trader');

        // load Language
        parent::loadLang(['global', 'trader']);

        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

        // set user data
        $this->user = $this->getUserData();

        // set planet data
        $this->planet = $this->getPlanetData();

        // init a new buddy object
        //$this->setUpTrader();

        // time to do something
        //$this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        parent::$page->display(
            $this->getTemplate()->set(
                'game/trader_overview_view',
                array_merge(
                    $this->langs->language,
                    $this->getMode()
                )
            )
        );
    }

    /**
     * Get the kind of trader that we are requesting
     *
     * @return array
     */
    private function getMode(): array
    {
        $mode = filter_input(INPUT_GET, 'mode', FILTER_SANITIZE_STRING);
        $template = '';

        if (in_array($mode, ['traderResources', 'traderAuctioneer', 'traderScrap', 'traderImportExport'])) {
            $view_to_get = strtolower(strtr($mode, ['trader' => '']));
            $template = $this->getTemplate()->set(
                'game/trader_' . $view_to_get . '_view',
                array_merge(
                    $this->langs->language,
                    [
                        'list_of_resources' => $this->{'build' . ucfirst($view_to_get) . 'Section'}(),
                    ]
                )
            );
        }

        return [
            'current_mode' => $template,
        ];
    }

    /**
     * Build resources section
     *
     * @return array
     */
    private function buildResourcesSection(): array
    {
        $list_of_resources = [];

        foreach (['metal' => 4500, 'crystal' => 9000, 'deuterium' => 13500] as $resource => $price) {
            $list_of_resources[] = array_merge(
                $this->langs->language,
                [
                    'dpath' => DPATH,
                    'resource' => $resource,
                    'resource_name' => $this->langs->line($resource),
                    'current_resource' => Format::shortlyNumber($this->planet['planet_' . $resource]),
                    'max_resource' => Format::shortlyNumber($this->planet['planet_' . $resource . '_max']),
                    'dark_matter_price_10' => Format::prettyNumber($price),
                    'dark_matter_price_50' => Format::prettyNumber($price * 5),
                    'dark_matter_price_100' => Format::prettyNumber($price * 10),
                ]
            );
        }

        return $list_of_resources;
    }

    private function buildAuctioneerSection(): array
    {
        return [];
    }

    private function buildScrapSection(): array
    {
        return [];
    }

    private function buildImportexportSection(): array
    {
        return [];
    }

    /**
     * checkStorage
     *
     * @param array   $amount Amount
     * @param boolean $force  Force, ignore storage size
     *
     * @return boolean
     */
    public function checkStorage($amount, $force = null)
    {
        if (!is_array($amount)) {

            throw new Exception("Must be array", 1);
        }

        $hangar = array('metal' => 22, 'crystal' => 23, 'deuterium' => 24);
        $check = array();

        foreach ($hangar as $k => $v) {

            if (!empty($amount[$k])) {

                if ($this->current_planet["planet_" . $k] + $amount[$k] >= ProductionLib::maxStorable($this->current_planet[$this->resource[$v]])) {

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

                        return sprintf(
                            $this->langs['tr_full_storage'], strtolower($this->langs['info'][$v]['name'])
                        );
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
     * Query to discount the amount of dark matter
     *
     * @return void
     */
    private function discountDarkMatter()
    {
        $this->_db->query(
            "UPDATE `" . PREMIUM . "` SET
            `premium_dark_matter` = `premium_dark_matter` - " . $this->tr_dark_matter . "
            WHERE `premium_user_id` = " . $this->current_user['user_id'] . ""
        );
    }
}

/* end of trader.php */
