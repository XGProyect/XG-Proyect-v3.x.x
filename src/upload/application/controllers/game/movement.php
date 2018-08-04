<?php
/**
 * Movement Controller
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
use application\core\entities\FleetEntity;
use application\libraries\fleets\Fleets;
use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\premium\Premium;
use application\libraries\research\Researches;
use application\libraries\Timing_library;
use const ACS_FLEETS;
use const FLEETS;
use const JS_PATH;

/**
 * Movement Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Movement extends Controller
{

    /**
     * 
     * @var int
     */
    const MODULE_ID = 8;

    /**
     * 
     * @var string
     */
    const REDIRECT_TARGET = 'game.php?page=movement';
    
    /**
     *
     * @var array
     */
    private $_user;

    /**
     *
     * @var array
     */
    private $_planet;

    /**
     *
     * @var \Fleets
     */
    private $_fleets = null;
    
    /**
     *
     * @var \Research
     */
    private $_research = null;
    
    /**
     *
     * @var \Premium
     */
    private $_premium = null;
    
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
        parent::loadModel('game/fleet');
        
        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        // set data
        $this->_user = $this->getUserData();
        
        // set planet data
        $this->_planet = $this->getPlanetData();

        // init a new fleets object
        $this->setUpFleets();

        // build the page
        $this->buildPage();
    }

    /**
     * Creates a new ships object that will handle all the ships
     * creation methods and actions
     * 
     * @return void
     */
    private function setUpFleets()
    {
        $this->_fleets = new Fleets(
            $this->Fleet_Model->getAllFleetsByUserId($this->_user['user_id']),
            $this->_user['user_id']
        );
        
        $this->_research = new Researches(
            [$this->_user],
            $this->_user['user_id']
        );
        
        $this->_premium = new Premium(
            [$this->_user],
            $this->_user['user_id']
        );
    }

    /**
     * Build the page
     * 
     * @return void
     */
    private function buildPage()
    {
        /**
         * Parse the items
         */
        $page = [
            'js_path' => JS_PATH,
            'fleets' => $this->_fleets->getFleetsCount(),
            'max_fleets' => FleetsLib::getMaxFleets(
                $this->_research->getCurrentResearch()->getResearchComputerTechnology(),
                $this->_premium->getCurrentPremium()->getPremiumOfficierAdmiral()
            ),
            'expeditions' => $this->_fleets->getExpeditionsCount(),
            'max_expeditions' => FleetsLib::getMaxExpeditions(
                $this->_research->getCurrentResearch()->getResearchAstrophysics()
            ),
            'list_of_movements' => $this->buildMovements()
        ];

        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                'movement/movements_view',
                array_merge(
                    $this->getLang(), $page
                )
            )
        );
    }

    /**
     * Build the list of movements
     * 
     * @return array
     */
    private function buildMovements(): array
    {
        $list_of_movements[] = [
            'num' => '-',
            'fleet_mission' => '-',
            'title' => '',
            'fleet_amount' => '-',
            'fleet_start' => '-',
            'fleet_start_time' => '-',
            'fleet_end' => '-',
            'fleet_end_time' => '-',
            'fleet_arrival' => '-',
            'inputs' => '-'
        ];
        
        if ($this->_fleets->getFleetsCount() > 0) {
            
            $fleet_count = 0;
            
            foreach($this->_fleets->getFleets() as $fleet) {

                $list_of_movements[] = [
                    'num' => ++$fleet_count,
                    'fleet_mission' => $this->getLang()['type_mission'][$fleet->getFleetMission()],
                    'title' => $this->buildTitleBlock($fleet->getFleetMess()),
                    'tooltip' => $this->buildToolTipBlock($fleet->getFleetMess()),
                    'fleet_amount' => FormatLib::prettyNumber($fleet->getFleetAmount()),
                    'fleet' => '',//$this->buildShipsBlock($fleet->getFleetArray()),
                    'fleet_start' => FormatLib::prettyCoords(
                        $fleet->getFleetStartGalaxy(), $fleet->getFleetStartSystem(), $fleet->getFleetStartPlanet()
                    ),
                    'fleet_start_time' => Timing_library::formatDefaultTime($fleet->getFleetCreation()),
                    'fleet_end' => FormatLib::prettyCoords(
                        $fleet->getFleetEndGalaxy(), $fleet->getFleetEndGalaxy(), $fleet->getFleetEndPlanet()
                    ),
                    'fleet_end_time' => Timing_library::formatDefaultTime($fleet->getFleetStartTime()),
                    'fleet_arrival' => Timing_library::formatDefaultTime($fleet->getFleetEndTime()),
                    'inputs' => '-'
                ];
            }
       
            /*
            while ($f = $this->_db->fetchArray($fq)) {
                $i++;

                //now we can view the call back button for ships in maintaing position (2)
                if ($f['fleet_mess'] == 0 or $f['fleet_mess'] == 2) {
                    $parse['inputs'] = '<form action="game.php?page=movement&action=return" method="post">';
                    $parse['inputs'] .= '<input name="fleetid" value="' . $f['fleet_id'] . '" type="hidden">';
                    $parse['inputs'] .= '<input value="' . $this->getLang()['fl_send_back'] . '" type="submit" name="send">';
                    $parse['inputs'] .= '</form>';

                    if ($f['fleet_mission'] == 1) {
                        $parse['inputs'] .= '<a href="#" onClick="f(\'game.php?page=federationlayer&union=' . $f['fleet_group'] . '&fleet=' . $f['fleet_id'] . '\', \'\')">';
                        $parse['inputs'] .= '<input value="' . $this->getLang()['fl_acs'] . '" type="button">';
                        $parse['inputs'] .= '</a>';
                    }
                } else {
                    $parse['inputs'] = '&nbsp;-&nbsp;';
                }
            }*/
        }
        
        return $list_of_movements;
    }
    
    /**
     * Build the title block
     * 
     * @param int $fleet_mess Fleet Mess
     * 
     * @return array
     */
    private function buildTitleBlock(int $fleet_mess): string
    {
        if (FleetsLib::isFleetReturning($fleet_mess)) {

            return $this->getLang()['fl_r'];
        }
        
        return $this->getLang()['fl_a'];
    }
    
    /**
     * Build the topltip block
     * 
     * @param int $fleet_mess Fleet Mess
     * 
     * @return array
     */
    private function buildToolTipBlock(int $fleet_mess): string
    {
        if (FleetsLib::isFleetReturning($fleet_mess)) {

            return $this->getLang()['fl_returning'];
        }
        
        return $this->getLang()['fl_onway'];
    }
    
    /**
     * 
     */
    private function buildShipsBlock(string $fleet_array): string
    {
        $fleet = explode(";", $fleet_array);
        $e = 0;
        $parse['fleet'] = '';

        foreach ($fleet as $a => $b) {
            if ($b != '') {
                $e++;
                $a = explode(",", $b);
                $parse['fleet'] .= $this->getLang()['tech'][$a[0]] . ":" . $a[1] . "\n";

                if ($e > 1) {
                    $parse['fleet'] .= "\t";
                }
            }
        }
    }
    
    /**
     * method send_back_fleet
     * param
     * returns the fleet to the planet
     */
    private function send_back_fleet()
    {
        if (( isset($_POST['fleetid']) ) && ( is_numeric($_POST['fleetid']) ) && ( isset($_GET['action']) ) && ( $_GET['action'] == 'return' )) {


            $fleet_id = (int) $_POST['fleetid'];
            $i = 0;
            $fleet_row = $this->_db->queryFetch("SELECT *
														FROM " . FLEETS . "
														WHERE `fleet_id` = '" . $fleet_id . "';");

            if ($fleet_row['fleet_owner'] == $this->_current_user['user_id']) {
                if ($fleet_row['fleet_mess'] == 0 or $fleet_row['fleet_mess'] == 2) {
                    if ($fleet_row['fleet_group'] > 0) {
                        $acs = $this->_db->queryFetch("SELECT `acs_fleet_members`
																FROM `" . ACS_FLEETS . "`
																WHERE `acs_fleet_id` = '" . $fleet_row['fleet_group'] . "';");

                        if ($acs['acs_fleet_members'] == $fleet_row['fleet_owner'] && $fleet_row['fleet_mission'] == 1) {
                            $this->_db->query("DELETE FROM `" . ACS_FLEETS . "`
													WHERE `acs_fleet_id` ='" . $fleet_row['fleet_group'] . "';");

                            $this->_db->query("UPDATE " . FLEETS . " SET
													`fleet_group` = '0'
													WHERE `fleet_group` = '" . $fleet_row['fleet_group'] . "';");
                        }

                        if ($fleet_row['fleet_mission'] == 2) {
                            $this->_db->query("UPDATE " . FLEETS . " SET
												`fleet_group` = '0'
												WHERE `fleet_id` = '" . $fleet_id . "';");
                        }
                    }

                    $CurrentFlyingTime = time() - $fleet_row['fleet_creation'];
                    $fleetLeght = $fleet_row['fleet_start_time'] - $fleet_row['fleet_creation'];
                    $ReturnFlyingTime = ( $fleet_row['fleet_end_stay'] != 0 && $CurrentFlyingTime > $fleetLeght ) ? $fleetLeght + time() : $CurrentFlyingTime + time();


                    $this->_db->query("UPDATE " . FLEETS . " SET
											`fleet_start_time` = '" . (time() - 1) . "',
											`fleet_end_stay` = '0',
											`fleet_end_time` = '" . ($ReturnFlyingTime + 1) . "',
											`fleet_target_owner` = '" . $this->_current_user['user_id'] . "',
											`fleet_mess` = '1'
											WHERE `fleet_id` = '" . $fleet_id . "';");
                }
            }
        }
    }
}

/* end of movement.php */
