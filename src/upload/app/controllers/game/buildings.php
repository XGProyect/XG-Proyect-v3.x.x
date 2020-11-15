<?php
/**
 * Buildings Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\controllers\game;

use App\core\BaseController;
use App\core\enumerators\BuildingsEnumerator;
use App\helpers\UrlHelper;
use App\libraries\buildings\Building;
use App\libraries\DevelopmentsLib as Developments;
use App\libraries\FormatLib;
use App\libraries\Functions;
use App\libraries\OfficiersLib;
use App\libraries\TimingLibrary as Timing;
use App\libraries\UpdatesLibrary;
use Exception;

/**
 * Buildings Class
 */
class Buildings extends BaseController
{
    const MODULE_ID = 3;

    /**
     *
     * @var \Buildings
     */
    private $_building = null;

    /**
     * List of currently available buildings
     *
     * @var array
     */
    private $_allowed_buildings = [];

    /**
     * Status of the commander officer
     *
     * @var boolean
     */
    private $_commander_active = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/buildings');

        // load Language
        parent::loadLang(['game/global', 'game/buildings', 'game/constructions']);

        // init a new building object with the current building queue
        $this->setUpBuildings();
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

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Creates a new building object that will handle all the building
     * creation methods and actions
     *
     * @return void
     */
    private function setUpBuildings()
    {
        $this->_building = new Building(
            $this->planet,
            $this->user,
            $this->getObjects()
        );

        $this->_allowed_buildings = $this->getAllowedBuildings();
        $this->_commander_active = OfficiersLib::isOfficierActive($this->user['premium_officier_commander']);
    }

    /**
     * Run an action
     *
     * @return void
     */
    private function runAction()
    {
        $action = filter_input(INPUT_GET, 'cmd');
        $reload = filter_input(INPUT_GET, 'r');
        $building = filter_input(INPUT_GET, 'building', FILTER_VALIDATE_INT);
        $list_id = filter_input(INPUT_GET, 'listid', FILTER_VALIDATE_INT);
        $allowed_actions = ['cancel', 'destroy', 'insert', 'remove'];

        if (!is_null($action)) {
            if (in_array($action, $allowed_actions)) {
                if ($this->canInitBuildAction($building, $list_id)) {
                    switch ($action) {
                        case 'cancel':
                            $this->cancelCurrent();
                            break;

                        case 'destroy':
                            $this->addToQueue($building, false);
                            break;

                        case 'insert':
                            $this->addToQueue($building, true);
                            break;

                        case 'remove':
                            $this->removeFromQueue($list_id);
                            break;
                    }

                    // start building
                    UpdatesLibrary::setFirstElement($this->planet, $this->user);

                    // start building
                    $this->Buildings_Model->updatePlanetBuildingQueue(
                        $this->planet
                    );
                }

                if ($reload == 'overview') {
                    header('location:game.php?page=overview');
                } else {
                    header('location:game.php?page=' . $this->getCurrentPage());
                }
            }
        }
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
        $page = [];
        $page['list_of_buildings'] = $this->buildListOfBuildings();

        // display the page
        parent::$page->display(
            $this->getTemplate()->set(
                'buildings/buildings_builds',
                array_merge($page, $this->buildQueueBlock())
            )
        );
    }

    /**
     * Build the list of buildings
     *
     * @return string
     */
    private function buildListOfBuildings()
    {
        $buildings_list = [];

        if (!is_null($this->_allowed_buildings)) {
            foreach ($this->_allowed_buildings as $building_id) {
                $buildings_list[] = $this->setListOfBuildingsItem($building_id);
            }
        }

        return $buildings_list;
    }

    /**
     * Build the list of queued elements
     *
     * @return array
     */
    private function buildQueueBlock()
    {
        $return['BuildListScript'] = '';
        $return['BuildList'] = '';

        $queue = $this->showQueue();

        if ($this->_commander_active && $queue['lenght'] > 0) {
            $return['BuildListScript'] = Developments::currentBuilding($this->getCurrentPage(), $this->langs->language);
            $return['BuildList'] = $queue['buildlist'];
        }

        return $return;
    }

    /**
     * Build each building block
     *
     * @param int $building_id Building ID
     *
     * @return array
     */
    private function setListOfBuildingsItem($building_id)
    {
        $item_to_parse = [];

        $item_to_parse['dpath'] = DPATH;
        $item_to_parse['i'] = $building_id;
        $item_to_parse['nivel'] = $this->getBuildingLevelWithFormat($building_id);
        $item_to_parse['n'] = $this->langs->language[$this->getObjects()->getObjects()[$building_id]];
        $item_to_parse['descriptions'] = $this->langs->language['descriptions'][$this->getObjects()->getObjects()[$building_id]];
        $item_to_parse['price'] = $this->getBuildingPriceWithFormat($building_id);
        $item_to_parse['time'] = $this->getBuildingTimeWithFormat($building_id);
        $item_to_parse['click'] = $this->getActionButton($building_id);

        return $item_to_parse;
    }

    /**
     * Expects a building ID to calculate and format the level
     *
     * @param int $building_id Building ID
     *
     * @return string
     */
    private function getBuildingLevelWithFormat($building_id)
    {
        return Developments::setLevelFormat(
            $this->getBuildingLevel($building_id),
            $this->langs
        );
    }

    /**
     * Expects a building ID to calculate and format the price
     *
     * @param int $building_id Building ID
     *
     * @return string
     */
    private function getBuildingPriceWithFormat($building_id)
    {
        return Developments::formatedDevelopmentPrice(
            $this->user,
            $this->planet,
            $building_id,
            $this->langs,
            true,
            $this->getBuildingLevel($building_id)
        );
    }

    /**
     * Expects a building ID to calculate and format the time
     *
     * @param int $building_id Building ID
     *
     * @return string
     */
    private function getBuildingTimeWithFormat($building_id)
    {
        return Developments::formatedDevelopmentTime(
            $this->getBuildingTime($building_id),
            $this->langs->line('bd_time')
        );
    }

    /**
     * Expects a building ID to calculate the building level
     *
     * @param int $building_id Building ID
     *
     * @return int
     */
    private function getBuildingLevel($building_id)
    {
        return $this->planet[$this->getObjects()->getObjects()[$building_id]];
    }

    /**
     * Expects a building ID to calculate the building time
     *
     * @param int $building_id Building ID
     *
     * @return int
     */
    private function getBuildingTime($building_id)
    {
        return Developments::developmentTime(
            $this->user,
            $this->planet,
            $building_id,
            $this->getBuildingLevel($building_id)
        );
    }

    /**
     * Expects a building ID, runs several validations and then returns a button,
     * based on the validations
     *
     * @param int $building_id Building ID
     *
     * @return string
     */
    private function getActionButton($building_id)
    {
        $build_url = 'game.php?page=' . $this->getCurrentPage() . '&cmd=insert&building=' . $building_id;

        // validations
        $is_development_payable = Developments::isDevelopmentPayable($this->user, $this->planet, $building_id, true, false);
        $is_on_vacations = parent::$users->isOnVacations($this->user);
        $have_fields = Developments::areFieldsAvailable($this->planet);
        $is_queue_full = $this->_building->isQueueFull();
        $queue_element = $this->_building->getCountElementsOnQueue();

        // check fields
        if (!$have_fields) {
            // block all if we don't have any
            return $this->buildButton('all_occupied');
        }

        // check if there's any work in progress
        if ($this->isWorkInProgress($building_id)) {
            // block some
            return $this->buildButton('work_in_progress');
        }

        // check vacations
        if ($is_on_vacations) {
            // block all or some
            return $this->buildButton('not_allowed');
        }

        // if a queue was already set
        if ($this->_commander_active) {
            if ($is_queue_full) {
                return $this->buildButton('not_allowed');
            }

            if ($queue_element > 0) {
                return UrlHelper::setUrl($build_url, $this->buildButton('allowed_for_queue'));
            }
        }

        // if something is being build
        if (!$this->_commander_active) {
            if ($queue_element > 0) {
                return $this->buildCountDownClock($building_id);
            }
        }

        if (!$is_development_payable) {
            return $this->buildButton('not_allowed');
        }

        return UrlHelper::setUrl($build_url, $this->buildButton('allowed'));
    }

    /**
     * Build the countdown clock for that usually appears
     *
     * @param int $building_id Building ID
     *
     * @return string
     */
    private function buildCountDownClock($building_id)
    {
        $first_queued_element = (int) $this->_building->getNewQueueAsArray()[0][0];

        if ($first_queued_element == $building_id) {
            $block = [
                'build_time' => ($this->planet['planet_b_building'] - time()),
                'call_program' => $this->getCurrentPage(),
            ];

            return $this->getTemplate()->set(
                'buildings/buildings_build_script',
                array_merge($block, $this->langs->language)
            );
        }

        return '<center>-</center>';
    }

    /**
     *
     * @param int $building_id  Building ID
     * @param int $list_id      List ID
     *
     * @return boolean
     */
    private function canInitBuildAction($building_id, $list_id)
    {
        if (isset($list_id)) {
            return true;
        }

        if ($this->_building->isQueueFull()) {
            return false;
        }

        if ($this->isWorkInProgress($building_id)) {
            return false;
        }

        if (!in_array($building_id, $this->_allowed_buildings)) {
            return false;
        }

        return true;
    }

    /**
     * Get the properties for each button type
     *
     * @param string $button_code Button code
     *
     * @return string
     */
    private function buildButton($button_code)
    {
        $listOfButtons = [
            'all_occupied' => ['color' => 'red', 'lang' => 'bd_no_more_fields'],
            'allowed' => ['color' => 'green', 'lang' => 'bd_build'],
            'not_allowed' => ['color' => 'red', 'lang' => 'bd_build'],
            'allowed_for_queue' => ['color' => 'green', 'lang' => 'bd_add_to_list'],
            'work_in_progress' => ['color' => 'red', 'lang' => 'bd_working'],
        ];

        $color = ucfirst($listOfButtons[$button_code]['color']);
        $text = $this->langs->language[$listOfButtons[$button_code]['lang']];
        $methodName = 'color' . $color;

        return FormatLib::$methodName($text);
    }

    /**
     * Determine if there's any work in progress
     *
     * @param int $building_id Building ID
     *
     * @return boolean
     */
    private function isWorkInProgress($building_id)
    {
        $working_buildings = [14, 15, 21];

        if ($building_id == 31 && Developments::isLabWorking($this->user)) {
            return true;
        }

        if (in_array($building_id, $working_buildings) && Developments::isShipyardWorking($this->planet)) {
            return true;
        }

        return false;
    }

    /**
     * Determine the current page and validate it
     *
     * @return array
     *
     * @throws Exception
     */
    private function getCurrentPage()
    {
        try {
            $get_value = filter_input(INPUT_GET, 'page');
            $allowed_pages = ['resources', 'station'];

            if (in_array($get_value, $allowed_pages)) {
                return $get_value;
            }

            throw new Exception('"resources" and "station" are the valid options');
        } catch (Exception $e) {
            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Get an array with an allowed set of items for the current page,
     * filtering by page and available technologies
     *
     * @return array
     */
    private function getAllowedBuildings()
    {
        $allowed_buildings = [
            'resources' => [
                1 => [1, 2, 3, 4, 12, 22, 23, 24],
                3 => [12, 22, 23, 24],
            ],
            'station' => [
                1 => [14, 15, 21, 31, 33, 34, 44],
                3 => [14, 21, 41, 42, 43],
            ],
        ];

        return array_filter(
            $allowed_buildings[$this->getCurrentPage()][$this->planet['planet_type']],
            function ($value) {
                return Developments::isDevelopmentAllowed(
                    $this->user,
                    $this->planet,
                    $value
                );
            }
        );
    }
    /**
     * OLD METHODS BELOW
     * OLD METHODS BELOW
     * OLD METHODS BELOW
     * OLD METHODS BELOW
     * OLD METHODS BELOW
     */

    /**
     * method cancelCurrent
     * param
     * return (bool) confirmation
     */
    private function cancelCurrent()
    {
        $CurrentQueue = $this->planet['planet_b_building_id'];

        if ($CurrentQueue != 0) {
            $QueueArray = explode(";", $CurrentQueue);
            $ActualCount = count($QueueArray);
            $CanceledIDArray = explode(",", $QueueArray[0]);
            $building = $CanceledIDArray[0];
            $BuildMode = $CanceledIDArray[4];

            if ($ActualCount > 1) {
                array_shift($QueueArray);
                $NewCount = count($QueueArray);
                $BuildEndTime = time();

                for ($ID = 0; $ID < $NewCount; $ID++) {
                    $ListIDArray = explode(",", $QueueArray[$ID]);

                    if ($ListIDArray[0] == $building) {
                        $ListIDArray[1] -= 1;
                    }

                    $current_build_time = Developments::developmentTime($this->user, $this->planet, $ListIDArray[0]);
                    $BuildEndTime += $current_build_time;
                    $ListIDArray[2] = $current_build_time;
                    $ListIDArray[3] = $BuildEndTime;
                    $QueueArray[$ID] = join(",", $ListIDArray);
                }
                $NewQueue = join(";", $QueueArray);
                $ReturnValue = true;
                $BuildEndTime = '0';
            } else {
                $NewQueue = '0';
                $ReturnValue = false;
                $BuildEndTime = '0';
            }

            if ($BuildMode == 'destroy') {
                $ForDestroy = true;
            } else {
                $ForDestroy = false;
            }

            if ($building != false) {
                $Needed = Developments::developmentPrice($this->user, $this->planet, $building, true, $ForDestroy);
                $this->planet['planet_metal'] += $Needed['metal'];
                $this->planet['planet_crystal'] += $Needed['crystal'];
                $this->planet['planet_deuterium'] += $Needed['deuterium'];
            }
        } else {
            $NewQueue = '0';
            $BuildEndTime = '0';
            $ReturnValue = false;
        }

        $this->planet['planet_b_building_id'] = $NewQueue;
        $this->planet['planet_b_building'] = $BuildEndTime;

        return $ReturnValue;
    }

    /**
     * method removeFromQueue
     * param $QueueID
     * return (int) the queue ID
     */
    private function removeFromQueue($QueueID)
    {
        if ($QueueID > 1) {
            $CurrentQueue = $this->planet['planet_b_building_id'];

            if (!empty($CurrentQueue)) {
                $QueueArray = explode(";", $CurrentQueue);
                $ActualCount = count($QueueArray);
                if ($ActualCount < 2) {
                    Functions::redirect('game.php?page=' . $this->getCurrentPage());
                }

                //  finding the buildings time
                $ListIDArrayToDelete = explode(",", $QueueArray[$QueueID - 1]);
                $lastB = $ListIDArrayToDelete;
                $lastID = $QueueID - 1;

                //search for biggest element
                for ($ID = $QueueID; $ID < $ActualCount; $ID++) {
                    //next buildings
                    $nextListIDArray = explode(",", $QueueArray[$ID]);
                    //if same type of element
                    if ($nextListIDArray[0] == $ListIDArrayToDelete[0]) {
                        $lastB = $nextListIDArray;
                        $lastID = $ID;
                    }
                }

                // update the rest of buildings queue
                for ($ID = $lastID; $ID < $ActualCount - 1; $ID++) {
                    $nextListIDArray = explode(",", $QueueArray[$ID + 1]);
                    $nextBuildEndTime = $nextListIDArray[3] - $lastB[2];
                    $nextListIDArray[3] = $nextBuildEndTime;
                    $QueueArray[$ID] = join(",", $nextListIDArray);
                }

                unset($QueueArray[$ActualCount - 1]);
                $NewQueue = join(";", $QueueArray);
            }

            $this->planet['planet_b_building_id'] = $NewQueue;
        }

        return $QueueID;
    }

    /**
     * method addToQueue
     * param $building
     * param $AddMode
     * return (int) the queue ID
     */
    private function addToQueue($building, $AddMode = true)
    {
        $resource = $this->getObjects()->getObjects();
        $CurrentQueue = $this->planet['planet_b_building_id'];
        $queue = $this->showQueue();
        $max_fields = Developments::maxFields($this->planet);
        $QueueArray = [];

        if ($AddMode) {
            if (($this->planet['planet_field_current'] >= ($max_fields - $queue['lenght']))) {
                Functions::redirect('game.php?page=' . $this->getCurrentPage());
            }
        }

        if ($CurrentQueue != 0) {
            $QueueArray = explode(";", $CurrentQueue);
            $ActualCount = count($QueueArray);
        } else {
            $QueueArray = "";
            $ActualCount = 0;
        }

        if ($AddMode == true) {
            $BuildMode = 'build';
        } else {
            $BuildMode = 'destroy';
        }

        if ($ActualCount < MAX_BUILDING_QUEUE_SIZE) {
            $QueueID = $ActualCount + 1;
        } else {
            $QueueID = false;
        }

        $continue = false;

        if ($QueueID != false && Developments::isDevelopmentAllowed($this->user, $this->planet, $building)) {
            if ($QueueID <= 1) {
                if (Developments::isDevelopmentPayable($this->user, $this->planet, $building, true, !$AddMode) && !parent::$users->isOnVacations($this->user)) {
                    $continue = true;
                }
            } else {
                $continue = true;
            }

            if ($continue) {
                if ($QueueID > 1) {
                    $InArray = 0;
                    for ($QueueElement = 0; $QueueElement < $ActualCount; $QueueElement++) {
                        $QueueSubArray = explode(",", $QueueArray[$QueueElement]);
                        if ($QueueSubArray[0] == $building) {
                            $InArray++;
                        }
                    }
                } else {
                    $InArray = 0;
                }

                if ($InArray != 0) {
                    $ActualLevel = $this->planet[$resource[$building]];
                    if ($AddMode == true) {
                        $BuildLevel = $ActualLevel + 1 + $InArray;
                        $this->planet[$resource[$building]] += $InArray;
                        $BuildTime = Developments::developmentTime($this->user, $this->planet, $building);
                        $this->planet[$resource[$building]] -= $InArray;
                    } else {
                        $BuildLevel = $ActualLevel - 1 - $InArray;
                        $this->planet[$resource[$building]] -= $InArray;
                        $BuildTime = Developments::tearDownTime(
                            $building,
                            $this->planet[$resource[BuildingsEnumerator::BUILDING_ROBOT_FACTORY]],
                            $this->planet[$resource[BuildingsEnumerator::BUILDING_NANO_FACTORY]],
                            $this->planet[$resource[$building]]
                        );

                        $this->planet[$resource[$building]] += $InArray;
                    }
                } else {
                    $ActualLevel = $this->planet[$resource[$building]];
                    if ($AddMode == true) {
                        $BuildLevel = $ActualLevel + 1;
                        $BuildTime = Developments::developmentTime($this->user, $this->planet, $building);
                    } else {
                        $BuildLevel = $ActualLevel - 1;
                        $BuildTime = Developments::tearDownTime(
                            $building,
                            $this->planet[$resource[BuildingsEnumerator::BUILDING_ROBOT_FACTORY]],
                            $this->planet[$resource[BuildingsEnumerator::BUILDING_NANO_FACTORY]],
                            $this->planet[$resource[$building]]
                        );
                    }
                }

                if ($QueueID == 1) {
                    $QueueArray = [];
                    $BuildEndTime = time() + $BuildTime;
                } else {
                    $PrevBuild = explode(",", $QueueArray[$ActualCount - 1]);
                    $BuildEndTime = $PrevBuild[3] + $BuildTime;
                }

                $QueueArray[$ActualCount] = $building . "," . $BuildLevel . "," . $BuildTime . "," . $BuildEndTime . "," . $BuildMode;
                $NewQueue = join(";", $QueueArray);

                $this->planet['planet_b_building_id'] = $NewQueue;
            }
        }
        return $QueueID;
    }

    /**
     * method showQueue
     * param $Sprice
     * return (array) the queue to build data
     */
    private function showQueue(&$Sprice = false)
    {
        $lang = $this->langs->language;
        $CurrentQueue = $this->planet['planet_b_building_id'];
        $QueueID = 0;
        $to_destroy = 0;
        $BuildMode = '';

        if ($CurrentQueue != 0) {
            $QueueArray = explode(";", $CurrentQueue);
            $ActualCount = count($QueueArray);
        } else {
            $QueueArray = '0';
            $ActualCount = 0;
        }

        $ListIDRow = '';

        if ($ActualCount != 0) {
            $PlanetID = $this->planet['planet_id'];
            for ($QueueID = 0; $QueueID < $ActualCount; $QueueID++) {
                $BuildArray = explode(",", $QueueArray[$QueueID]);
                $BuildEndTime = floor($BuildArray[3]);
                $CurrentTime = floor(time());

                if ($BuildMode == 'destroy') {
                    $to_destroy++;
                }

                if ($BuildEndTime >= $CurrentTime) {
                    $ListID = $QueueID + 1;
                    $building = $BuildArray[0];
                    $BuildLevel = $BuildArray[1];
                    $BuildMode = $BuildArray[4];
                    $BuildTime = $BuildEndTime - time();
                    $ElementTitle = $this->langs->language[$this->getObjects()->getObjects()[$building]];

                    if (isset($Sprice[$building]) && $Sprice !== false && $BuildLevel > $Sprice[$building]) {
                        $Sprice[$building] = $BuildLevel;
                    }

                    if ($ListID > 0) {
                        $ListIDRow .= "<tr>";
                        if ($BuildMode == 'build') {
                            $ListIDRow .= "	<td class=\"l\" colspan=\"2\">" . $ListID . ".: " . $ElementTitle . " " . $BuildLevel . "</td>";
                        } else {
                            $ListIDRow .= "	<td class=\"l\" colspan=\"2\">" . $ListID . ".: " . $ElementTitle . " " . $BuildLevel . " " . $this->langs->line('bd_dismantle') . "</td>";
                        }
                        $ListIDRow .= "	<td class=\"k\">";

                        if ($ListID == 1) {
                            $ListIDRow .= "		<div id=\"blc\" class=\"z\">" . $BuildTime . "<br>";
                            $ListIDRow .= "		<a href=\"game.php?page=" . $this->getCurrentPage() . "&listid=" . $ListID . "&amp;cmd=cancel&amp;planet=" . $PlanetID . "\">" . $this->langs->line('bd_interrupt') . "</a></div>";
                            $ListIDRow .= "		<script language=\"JavaScript\">";
                            $ListIDRow .= "			pp = \"" . $BuildTime . "\";\n";
                            $ListIDRow .= "			pk = \"" . $ListID . "\";\n";
                            $ListIDRow .= "			pm = \"cancel\";\n";
                            $ListIDRow .= "			pl = \"" . $PlanetID . "\";\n";
                            $ListIDRow .= "			t();\n";
                            $ListIDRow .= "		</script>";
                            $ListIDRow .= "		<strong color=\"lime\"><br><font color=\"lime\">" . Timing::formatExtendedDate($BuildEndTime) . "</font></strong>";
                        } else {
                            $ListIDRow .= "		<font color=\"red\">";
                            $ListIDRow .= "		<a href=\"game.php?page=" . $this->getCurrentPage() . "&listid=" . $ListID . "&amp;cmd=remove&amp;planet=" . $PlanetID . "\">" . $this->langs->line('bd_cancel') . "</a></font>";
                        }

                        $ListIDRow .= "	</td>";
                        $ListIDRow .= "</tr>";
                    }
                }
            }
        }

        $RetValue['to_destoy'] = $to_destroy;
        $RetValue['lenght'] = $ActualCount;
        $RetValue['buildlist'] = $ListIDRow;

        return $RetValue;
    }
}
