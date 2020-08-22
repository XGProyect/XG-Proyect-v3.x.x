<?php
/**
 * Officier Controller
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
use application\core\Enumerators\OfficiersEnumerator as OE;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\OfficiersLib;
use application\libraries\TimingLibrary as Timing;
use DPATH;

/**
 * Officier Class
 */
class Officier extends Controller
{
    /**
     * The module ID
     *
     * @var int
     */
    const MODULE_ID = 15;

    /**
     * Current user data
     *
     * @var array
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // load Model
        parent::loadModel('game/officier');

        // load Language
        parent::loadLang(['game/global', 'game/officier']);

        // set data
        $this->user = $this->getUserData();

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Run an action
     *
     * @return void
     */
    private function runAction()
    {
        $data = filter_input_array(INPUT_GET, [
            'offi' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => [
                    'min_range' => OE::PREMIUM_OFFICIER_COMMANDER,
                    'max_range' => OE::PREMIUM_OFFICIER_TECHNOCRAT,
                ],
            ],
            'time' => [
                'filter' => FILTER_SANITIZE_STRING,
            ],
        ]);

        if (in_array($data['offi'], $this->getObjects()->getObjectsList('officier')) && in_array($data['time'], ['week', 'month'])) {
            $time = 'darkmatter_' . $data['time'];
            $set_time = (($time == 'darkmatter_month') ? (ONE_MONTH * 3) : ONE_WEEK);

            if ($this->isOfficierAccesible($data['offi'], $time)) {
                $price = $this->getOfficierPrice($data['offi'], $time);
                $officier = $this->getObjects()->getObjects($data['offi']);

                if (OfficiersLib::isOfficierActive($this->user[$officier])) {
                    $time_to_add = $this->user[$officier] + $set_time;
                } else {
                    $time_to_add = time() + $set_time;
                }

                $this->Officier_Model->setPremium($this->user['user_id'], $price, $officier, $time_to_add);

                FunctionsLib::redirect('game.php?page=officier');
            }
        }
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        /**
         * Parse the items
         */
        $page = [];
        $page['dpath'] = DPATH;
        $page['premium_pay_url'] = FunctionsLib::readConfig('premium_url') != '' ? FunctionsLib::readConfig('premium_url') : 'game.php?page=officier';
        $page['officier_list'] = $this->buildOfficiersList();

        // display the page
        parent::$page->display(
            $this->getTemplate()->set('game/officier_view', array_merge($page, $this->langs->language))
        );
    }

    /**
     * Return an array with a list of officiers
     *
     * @return array
     */
    private function buildOfficiersList(): array
    {
        $allowed_items = [
            OE::PREMIUM_OFFICIER_COMMANDER,
            OE::PREMIUM_OFFICIER_ADMIRAL,
            OE::PREMIUM_OFFICIER_ENGINEER,
            OE::PREMIUM_OFFICIER_GEOLOGIST,
            OE::PREMIUM_OFFICIER_TECHNOCRAT,
        ];

        $officiers_list = [];

        foreach ($allowed_items as $item_id) {
            $officiers_list[] = $this->setOfficier($item_id);
        }

        return $officiers_list;
    }

    /**
     * Build each officier block
     *
     * @param integer $item_id
     * @return array
     */
    private function setOfficier(int $item_id): array
    {
        $item_to_parse = [];
        $item_to_parse = $this->langs->language;
        $item_to_parse['dpath'] = DPATH;
        $item_to_parse['status'] = $this->setOfficierStatusWithFormat($item_id);
        $item_to_parse['name'] = $this->langs->language['officiers'][$item_id]['name'];
        $item_to_parse['description'] = $this->langs->language['officiers'][$item_id]['description'];
        $item_to_parse['benefits'] = $this->langs->language['officiers'][$item_id]['benefits'];
        $item_to_parse['month_price'] = FormatLib::prettyNumber($this->getOfficierPrice($item_id, 'darkmatter_month'));
        $item_to_parse['week_price'] = FormatLib::prettyNumber($this->getOfficierPrice($item_id, 'darkmatter_week'));
        $item_to_parse['img_big'] = $this->getOfficierImage($item_id, 'img_big');
        $item_to_parse['img_small'] = $this->getOfficierImage($item_id, 'img_small');
        $item_to_parse['link_month'] = "game.php?page=officier&offi=" . $item_id . "&time=month";
        $item_to_parse['link_week'] = "game.php?page=officier&offi=" . $item_id . "&time=week";

        return $item_to_parse;
    }

    /**
     * Return the officier status with format
     *
     * @param integer $item_id
     * @return string
     */
    private function setOfficierStatusWithFormat(int $item_id): string
    {
        if (OfficiersLib::isOfficierActive($this->user[$this->getObjects()->getObjects($item_id)])) {
            return FormatLib::customColor($this->langs->line('of_active') . ' ' . Timing::formatShortDate($this->user[$this->getObjects()->getObjects($item_id)]), 'lime');
        }

        return FormatLib::colorRed($this->langs->line('of_inactive'));
    }

    /**
     * Check if the officier is accesible or not
     *
     * @param integer $officier
     * @param string $time
     * @return bool
     */
    private function isOfficierAccesible(int $officier, string $time): bool
    {
        return ($this->getObjects()->getPrice($officier, $time) <= $this->user['premium_dark_matter']);
    }

    /**
     * Get the officier darkmatter price
     *
     * @param integer $officier
     * @param string $time
     * @return integer
     */
    private function getOfficierPrice(int $officier, string $time): int
    {
        return floor($this->getObjects()->getPrice($officier, $time));
    }

    /**
     * Get the officier image
     *
     * @param integer $officier
     * @param string $type
     * @return string
     */
    private function getOfficierImage(int $officier, string $type): string
    {
        return $this->getObjects()->getPrice($officier, $type);
    }
}

/* end of officier.php */
