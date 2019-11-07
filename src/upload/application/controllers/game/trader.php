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
use application\libraries\game\ResourceMarket;
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
     * ResourceMarket object
     *
     * @var \ResourceMarket
     */
    private $trader;

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

        // loda library
        $this->formula = Functions::loadLibrary('FormulaLib');

        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

        // set user data
        $this->user = $this->getUserData();

        // set planet data
        $this->planet = $this->getPlanetData();

        // init a new trader object
        $this->setUpTrader();

        // time to do something
        //$this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Creates a new trader object that will handle all the trader
     * creation methods and actions
     *
     * @return void
     */
    private function setUpTrader(): void
    {
        $this->trader = new ResourceMarket(
            $this->user,
            $this->planet
        );
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

        foreach (['metal', 'crystal', 'deuterium'] as $resource) {
            $list_of_resources[] = array_merge(
                $this->langs->language,
                [
                    'dpath' => DPATH,
                    'resource' => $resource,
                    'resource_name' => $this->langs->line($resource),
                    'current_resource' => Format::shortlyNumber($this->planet['planet_' . $resource]),
                    'max_resource' => Format::shortlyNumber($this->planet['planet_' . $resource . '_max']),
                ],
                $this->returnDarkMatterPricePoints($resource)
            );
        }

        return $list_of_resources;
    }

    /**
     * Return the dark matter price points
     *
     * @param string $resource
     * @return array
     */
    private function returnDarkMatterPricePoints(string $resource): array
    {
        $pricePoints = [];

        foreach ([10, 50, 100] as $percentage) {
            $dm_price = $this->trader->{'getPriceToFill' . $percentage . 'Percent'}($resource);
            $formated_dm_price = Format::customColor(Format::prettyNumber($dm_price), '#2cbef2') . ' ' . $this->langs->line('dark_matter_short');
            $pricePoints['dark_matter_price_' . $percentage] = ($dm_price > 0) ? $formated_dm_price : Format::colorRed('-');
        }

        return $pricePoints;
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
