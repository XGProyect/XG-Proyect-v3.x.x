<?php
/**
 * Officier Controller
 *
 * PHP Version 7+
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
use application\core\Database;
use application\core\Enumerators\OfficiersEnumerator as OE;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\OfficiersLib;

use MDOULE_ID;

/**
 * Officier Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Officier extends Controller
{

    const MODULE_ID = 15;

    /**
     *
     * @var type \Users_library
     */
    private $_user;

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
        parent::loadModel('game/officier');

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();

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
                'filter'    => FILTER_VALIDATE_INT,
                'options'   => ['min_range' => OE::premium_officier_commander, 'max_range' => OE::premium_officier_technocrat]
            ],
            'time' => [
                'filter'    => FILTER_SANITIZE_STRING
            ]
        ]);

        if (in_array($data['offi'], $this->getObjects()->getObjectsList('officier')) && in_array($data['time'], ['week', 'month'])) {

            $time = 'darkmatter_' . $data['time'];
            $set_time = (($time == 'darkmatter_month') ? (ONE_MONTH * 3) : ONE_WEEK);

            if ($this->isOfficierAccesible($data['offi'], $time)) {
                
                $price      = $this->getOfficierPrice($data['offi'], $time);
                $officier   = $this->getObjects()->getObjects($data['offi']);

                if (OfficiersLib::isOfficierActive($this->_user[$officier])) {

                    $time_to_add = $this->_user[$officier] + $set_time;
                } else {

                    $time_to_add = time() + $set_time;
                }

                $this->Officier_Model->setPremium($this->_user['user_id'], $price, $officier, $time_to_add);

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
        $page['dpath']              = DPATH;
        $page['premium_pay_url']    = FunctionsLib::readConfig('premium_url') != '' ? FunctionsLib::readConfig('premium_url') : 'game.php?page=officier';
        $page['officier_list']      = $this->buildOfficiersList();

        // display the page
        parent::$page->display(
            $this->getTemplate()->set('game/officier_view', array_merge($page, $this->getLang()))
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
            OE::premium_officier_commander,
            OE::premium_officier_admiral,
            OE::premium_officier_engineer,
            OE::premium_officier_geologist,
            OE::premium_officier_technocrat
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

        $item_to_parse = $this->getLang();
        $item_to_parse['dpath'] = DPATH;
        $item_to_parse['off_id']= $item_id;
        $item_to_parse['off_status'] = $this->setOfficierStatusWithFormat($item_id);
        $item_to_parse['off_name'] = $this->getLang()['tech'][$item_id];
        $item_to_parse['off_desc'] = $this->getLang()['res']['descriptions'][$item_id];
        $item_to_parse['off_desc_short'] = $this->getLang()['info'][$item_id]['description'];
        $item_to_parse['month_price'] = FormatLib::prettyNumber($this->getOfficierPrice($item_id, 'darkmatter_month'));
        $item_to_parse['week_price'] = FormatLib::prettyNumber($this->getOfficierPrice($item_id, 'darkmatter_week'));
        $item_to_parse['img_big'] = $this->getOfficierImage($item_id, 'img_big');
        $item_to_parse['img_small'] = $this->getOfficierImage($item_id, 'img_small');
        $item_to_parse['off_link_month'] = "game.php?page=officier&offi=" . $item_id . "&time=month";
        $item_to_parse['off_link_week'] = "game.php?page=officier&offi=" . $item_id . "&time=week";

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
        if (OfficiersLib::isOfficierActive($this->_user[$this->getObjects()->getObjects($item_id)])) {

            return FormatLib::customColor($this->getLang()['of_active'] . ' ' . date(FunctionsLib::readConfig('date_format'), $this->_user[$this->getObjects()->getObjects($item_id)]), 'lime');
        }

        return FormatLib::colorRed($this->getLang()['of_inactive']);
    }

    /**
     * Check if the officier is accesible or not
     *
     * @param integer $officier
     * @param string $time
     * @return bool
     */
    private function isOfficierAccesible(int $officier,string $time): bool
    {
        if ($this->getObjects()->getPrice($officier, $time) <= $this->_user['premium_dark_matter']) {

            return true;
        }

        return false;
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
