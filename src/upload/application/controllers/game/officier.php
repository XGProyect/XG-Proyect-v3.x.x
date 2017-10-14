<?php
/**
 * Officier Controller
 *
 * PHP Version 5.5+
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
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\OfficiersLib;

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

    private $_lang;
    private $_resource;
    private $_pricelist;
    private $_reslist;
    private $_current_user;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        $this->_db = new Database();
        $this->_lang = parent::$lang;
        $this->_current_user = parent::$users->getUserData();
        $this->_resource = parent::$objects->getObjects();
        $this->_pricelist = parent::$objects->getPrice();
        $this->_reslist = parent::$objects->getObjectsList();

        $this->build_page();
    }

    /**
     * method __destruct
     * param
     * return close db connection
     */
    public function __destruct()
    {
        $this->_db->closeConnection();
    }

    /**
     * method build_page
     * param
     * return the statistics page
     */
    private function build_page()
    {
        $parse = $this->_lang;
        $parse['dpath'] = DPATH;
        $bloc = $this->_lang;
        $mode = isset($_GET['mode']) ? $_GET['mode'] : '';
        $time = isset($_GET['time']) ? $_GET['time'] : '';

        if ($mode == 2 && ( $time == 'month' or $time == 'week' )) {
            $Selected = $_GET['offi'];
            $time = 'darkmatter_' . $time;
            $set_time = $time == 'darkmatter_month' ? ( 3600 * 24 * 30 * 3 ) : ( 3600 * 24 * 7 );

            if (in_array($Selected, $this->_reslist['officier'])) {
                $Result = $this->is_officier_accesible($Selected, $time);
                $Price = $this->get_officier_price($Selected, $time);

                if ($Result !== false) {
                    $this->_current_user['premium_dark_matter'] -= $Price;

                    // IF THE OFFICIER IS ACTIVE
                    if (OfficiersLib::isOfficierActive($this->_current_user[$this->_resource[$Selected]])) {
                        $this->_current_user[$this->_resource[$Selected]] += $set_time; // ADD TIME
                    } else { // ELSE
                        $this->_current_user[$this->_resource[$Selected]] = time() + $set_time; // SET TIME
                    }

                    $this->_db->query("UPDATE " . PREMIUM . " SET
											`premium_dark_matter` = '" . $this->_current_user['premium_dark_matter'] . "',
											`" . $this->_resource[$Selected] . "` = '" . $this->_current_user[$this->_resource[$Selected]] . "'
											WHERE `premium_user_id` = '" . $this->_current_user['user_id'] . "';");
                }
            }
            FunctionsLib::redirect('game.php?page=officier');
        } else {
            $OfficierRowTPL = parent::$page->getTemplate('officier/officier_row');
            $parse['disp_off_tbl'] = '';
            $parse['premium_pay_url'] = FunctionsLib::readConfig('premium_url') != '' ? FunctionsLib::readConfig('premium_url') : 'game.php?page=officier';

            foreach ($this->_lang['tech'] as $Element => $ElementName) {
                if ($Element >= 601 && $Element <= 605) {
                    $bloc['dpath'] = DPATH;
                    $bloc['off_id'] = $Element;
                    $bloc['off_status'] = ( ( OfficiersLib::isOfficierActive($this->_current_user[$this->_resource[$Element]]) ) ? '<font color=lime>' . $this->_lang['of_active'] . ' ' . date(FunctionsLib::readConfig('date_format'), $this->_current_user[$this->_resource[$Element]]) . '</font>' : '<font color=red>' . $this->_lang['of_inactive'] . '</font>' );
                    $bloc['off_name'] = $ElementName;
                    $bloc['off_desc'] = $this->_lang['res']['descriptions'][$Element];
                    $bloc['off_desc_short'] = $this->_lang['info'][$Element]['description'];
                    $bloc['month_price'] = FormatLib::prettyNumber($this->get_officier_price($Element, 'darkmatter_month'));
                    $bloc['week_price'] = FormatLib::prettyNumber($this->get_officier_price($Element, 'darkmatter_week'));
                    $bloc['img_big'] = $this->get_officier_image($Element, 'img_big');
                    $bloc['img_small'] = $this->get_officier_image($Element, 'img_small');
                    $bloc['off_link_month'] = "game.php?page=officier&mode=2&offi=" . $Element . "&time=month";
                    $bloc['off_link_week'] = "game.php?page=officier&mode=2&offi=" . $Element . "&time=week";

                    $parse['disp_off_tbl'] .= parent::$page->parseTemplate($OfficierRowTPL, $bloc);
                }
            }
        }

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('officier/officier_table'), $parse));
    }

    /**
     * method is_officier_accesible
     * param $Officier
     * param $time
     * return if the officier is accesible or not
     */
    private function is_officier_accesible($officier, $time)
    {
        if ($this->_pricelist[$officier][$time] <= $this->_current_user['premium_dark_matter']) {
            return true;
        }

        return false;
    }

    /**
     * method get_officier_price
     * param $officier
     * param $time
     * return the officier darkmatter price
     */
    private function get_officier_price($officier, $time)
    {
        return floor($this->_pricelist[$officier][$time]);
    }

    /**
     * method get_officier_image
     * param $officier
     * param $type
     * return the officier darkmatter price
     */
    private function get_officier_image($officier, $type)
    {
        return $this->_pricelist[$officier][$type];
    }
}

/* end of officier.php */
