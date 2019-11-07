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

    /**
     * The module ID
     *
     * @var int
     */
    const MODULE_ID = 5;

    /**
     * Contains the resources type
     *
     * @var array
     */
    const RESOURCES = ['metal', 'crystal', 'deuterium'];

    /**
     * Contains the refill percentages
     *
     * @var array
     */
    const PERCENTAGES = [10, 50, 100];

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

        foreach (self::RESOURCES as $resource) {
            $list_of_resources[] = array_merge(
                $this->langs->language,
                [
                    'dpath' => DPATH,
                    'resource' => $resource,
                    'resource_name' => $this->langs->line($resource),
                    'current_resource' => Format::shortlyNumber($this->planet['planet_' . $resource]),
                    'max_resource' => Format::shortlyNumber($this->planet['planet_' . $resource . '_max']),
                    'refill_options' => $this->setRefillOptions($resource),
                ]
            );
        }

        return $list_of_resources;
    }

    /**
     * Set the different refill options
     *
     * @param string $resource
     * @return array
     */
    private function setRefillOptions(string $resource): array
    {
        $refillOptions = [];

        foreach (self::PERCENTAGES as $percentage) {
            $dm_price = $this->trader->{'getPriceToFill' . $percentage . 'Percent'}($resource);

            if ($this->trader->{'is' . $resource . 'StorageFull'}() or $dm_price == 0) {
                $price = Format::colorRed('-');
                $button = '';
            } else {
                $price = Format::customColor(
                    Format::prettyNumber($dm_price),
                    '#2cbef2'
                ) . ' ' . $this->langs->line('dark_matter_short');
                $button = '<input type="button" name="' . $resource . '-' . $percentage . '" value="' . $this->langs->line('tr_refill_button') . '">';
            }

            $refillOptions[] = [
                'label' => (self::PERCENTAGES == 100) ? $this->langs->line('tr_refill_to') : $this->langs->line('tr_refill_by'),
                'percentage' => $percentage,
                'tr_requires' => $this->langs->line('tr_requires'),
                'price' => $price,
                'button' => $button,
            ];
        }

        return $refillOptions;
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
