<?php
/**
 * research.php
 *
 * @author   XG Proyect Team
 * @license  https://www.xgproyect.org XG Proyect
 * @link     https://www.xgproyect.org
 * @version  3.2.0
 */
namespace App\controllers\game;

use App\core\BaseController;
use App\helpers\UrlHelper;
use App\libraries\DevelopmentsLib;
use App\libraries\FormatLib;
use App\libraries\Functions;

/**
 * Research Class
 */
class Research extends BaseController
{
    const MODULE_ID = 6;

    /**
     * @var mixed
     */
    private $_current_user;
    /**
     * @var mixed
     */
    private $_current_planet;
    /**
     * @var mixed
     */
    private $_resource;
    /**
     * @var mixed
     */
    private $_reslist;
    /**
     * @var mixed
     */
    private $_is_working;
    /**
     * @var mixed
     */
    private $_lab_level;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

        // load Model
        parent::loadModel('game/research');

        // load Language
        parent::loadLang(['game/global', 'game/research', 'game/technologies']);

        $this->_resource = parent::$objects->getObjects();
        $this->_reslist = parent::$objects->getObjectsList();

        $this->setLabsAmount();
        $this->handleTechnologieBuild();
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

        if ($this->planet[$this->_resource[31]] == 0) {
            Functions::message($this->langs->line('re_lab_required'), '', '', true);
        }

        // build the page
        $this->buildPage();
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage()
    {
        $parse = $this->langs->language;
        $technology_list = '';

        // time to do something
        $this->doCommand();

        // build the page
        foreach ($this->_reslist['tech'] as $tech) {
            if (DevelopmentsLib::isDevelopmentAllowed($this->user, $this->planet, $tech)) {
                $RowParse['dpath'] = DPATH;
                $RowParse['tech_id'] = $tech;
                $building_level = $this->user[$this->_resource[$tech]];
                $RowParse['tech_level'] = DevelopmentsLib::setLevelFormat($building_level, $this->langs, $tech, $this->user);
                $RowParse['tech_name'] = $this->langs->line($this->_resource[$tech]);
                $RowParse['tech_descr'] = $this->langs->language['descriptions'][$this->_resource[$tech]];
                $RowParse['tech_price'] = DevelopmentsLib::formatedDevelopmentPrice($this->user, $this->planet, $tech, $this->langs);
                $SearchTime = DevelopmentsLib::developmentTime($this->user, $this->planet, $tech, false, $this->_lab_level);
                $RowParse['search_time'] = DevelopmentsLib::formatedDevelopmentTime($SearchTime, $this->langs->line('re_time'));

                if (!$this->_is_working['is_working']) {
                    if (DevelopmentsLib::isDevelopmentPayable($this->user, $this->planet, $tech) && !parent::$users->isOnVacations($this->user)) {
                        if (!$this->isLaboratoryInQueue()) {
                            $action_link = FormatLib::colorRed($this->langs->line('re_research'));
                        } else {
                            $action_link = UrlHelper::setUrl('game.php?page=research&cmd=search&tech=' . $tech, FormatLib::colorGreen($this->langs->line('re_research')));
                        }
                    } else {
                        $action_link = FormatLib::colorRed($this->langs->line('re_research'));
                    }
                } else {
                    if ($this->_is_working['working_on']['planet_b_tech_id'] == $tech) {
                        $bloc = $this->langs->language;

                        if ($this->_is_working['working_on']['planet_id'] != $this->planet['planet_id']) {
                            $bloc['tech_time'] = $this->_is_working['working_on']['planet_b_tech'] - time();
                            $bloc['tech_name'] = $this->langs->line('re_from') . $this->_is_working['working_on']['planet_name'] . '<br /> ' . FormatLib::prettyCoords($this->_is_working['working_on']['planet_galaxy'], $this->_is_working['working_on']['planet_system'], $this->_is_working['working_on']['planet_planet']);
                            $bloc['tech_home'] = $this->_is_working['working_on']['planet_id'];
                            $bloc['tech_id'] = $this->_is_working['working_on']['planet_b_tech_id'];
                        } else {
                            $bloc['tech_time'] = $this->planet['planet_b_tech'] - time();
                            $bloc['tech_name'] = '';
                            $bloc['tech_home'] = $this->planet['planet_id'];
                            $bloc['tech_id'] = $this->planet['planet_b_tech_id'];
                        }
                        $action_link = $this->getTemplate()->set(
                            'buildings/buildings_research_script',
                            $bloc
                        );
                    } else {
                        $action_link = "<center>-</center>";
                    }
                }
                $RowParse['tech_link'] = $action_link;
                $technology_list .= $this->getTemplate()->set(
                    'buildings/buildings_research_row',
                    $RowParse
                );
            }
        }

        $parse['noresearch'] = (!$this->isLaboratoryInQueue() ? $this->langs->line('re_building_lab') : '');
        $parse['technolist'] = $technology_list;

        parent::$page->display(
            $this->getTemplate()->set(
                'buildings/buildings_research',
                $parse
            )
        );
    }

    /**
     * method doCommand
     * param
     * return void
     */
    private function doCommand()
    {
        $cmd = isset($_GET['cmd']) ? $_GET['cmd'] : null;

        if (!is_null($cmd)) {
            $technology = (int) $_GET['tech'];

            if (in_array($technology, $this->_reslist['tech'])) {
                $update_data = false;

                if (is_array($this->_is_working['working_on'])) {
                    $working_planet = $this->_is_working['working_on'];
                } else {
                    $working_planet = $this->planet;
                }

                switch ($cmd) {
                    // cancel a research
                    case 'cancel':
                        if (!empty($this->_is_working['working_on'])) {
                            if ($this->_is_working['working_on']['planet_b_tech_id'] == $technology) {
                                $costs = DevelopmentsLib::developmentPrice($this->user, $working_planet, $technology);
                                $working_planet['planet_metal'] += $costs['metal'];
                                $working_planet['planet_crystal'] += $costs['crystal'];
                                $working_planet['planet_deuterium'] += $costs['deuterium'];
                                $working_planet['planet_b_tech_id'] = 0;
                                $working_planet['planet_b_tech'] = 0;
                                $this->user['research_current_research'] = 0;
                                $update_data = true;
                                $this->_is_working['is_working'] = false;
                            }
                        }

                        break;

                    // start a research
                    case 'search':
                        if (DevelopmentsLib::isDevelopmentAllowed($this->user, $working_planet, $technology) && DevelopmentsLib::isDevelopmentPayable($this->user, $working_planet, $technology) && !parent::$users->isOnVacations($this->user)) {
                            $costs = DevelopmentsLib::developmentPrice(
                                $this->user,
                                $working_planet,
                                $technology
                            );

                            $working_planet['planet_metal'] -= $costs['metal'];
                            $working_planet['planet_crystal'] -= $costs['crystal'];
                            $working_planet['planet_deuterium'] -= $costs['deuterium'];
                            $working_planet['planet_b_tech_id'] = $technology;
                            $working_planet['planet_b_tech'] = time() + DevelopmentsLib::developmentTime(
                                $this->user,
                                $working_planet,
                                $technology,
                                false,
                                $this->_lab_level
                            );

                            $this->user['research_current_research'] = $working_planet['planet_id'];
                            $update_data = true;
                            $this->_is_working['is_working'] = true;
                        }

                        break;
                }

                if ($update_data == true) {
                    $this->Research_Model->startNewResearch($working_planet, $this->user);
                }

                $this->planet = $working_planet;

                if (is_array($this->_is_working['working_on'])) {
                    $this->_is_working['working_on'] = $working_planet;
                } else {
                    $this->planet = $working_planet;

                    if ($cmd == 'search') {
                        $this->_is_working['working_on'] = $this->planet;
                    }
                }
            }

            Functions::redirect('game.php?page=research');
        }
    }

    /**
     * method isLaboratoryInQueue
     * param
     * return true if all clear, false if is anything in the queue
     */
    private function isLaboratoryInQueue()
    {
        $return = true;
        $current_building = '';
        $element_id = 0;

        if ($this->planet['planet_b_building_id'] != 0) {
            $current_queue = $this->planet['planet_b_building_id'];

            if (strpos($current_queue, ';')) {
                $queue = explode(';', $current_queue);

                for ($i = 0; $i < MAX_BUILDING_QUEUE_SIZE; $i++) {
                    if (isset($queue[$i])) {
                        $element_data = explode(",", $queue[$i]);
                        $element_id = $element_data[0];

                        if ($element_id == 31) {
                            break;
                        }
                    }
                }
            } else {
                $current_building = $current_queue;
            }

            if ($current_building == 31 or $element_id == 31) {
                $return = false;
            }
        }

        return $return;
    }

    /**
     * method handleTechnologieBuild
     * param
     * return return the planet where it's been working on and the status
     */
    private function handleTechnologieBuild()
    {
        $this->_is_working['working_on'] = '';
        $this->_is_working['is_working'] = false;

        if ($this->user['research_current_research'] != 0) {
            if ($this->user['research_current_research'] != $this->planet['planet_id']) {
                $working_planet = $this->Research_Model->getPlanetResearching($this->user['research_current_research']);
            }

            if (isset($working_planet)) {
                $the_planet = $working_planet;
            } else {
                $the_planet = $this->planet;
            }

            if ($the_planet['planet_b_tech'] <= time() && $the_planet['planet_b_tech_id'] != 0) {
                $the_planet['planet_b_tech_id'] = 0;

                if (isset($working_planet)) {
                    $working_planet = $the_planet;
                } else {
                    $this->planet = $the_planet;
                }
            } elseif ($the_planet['planet_b_tech_id'] == 0) {
                $this->_is_working['working_on'] = '';
                $this->_is_working['is_working'] = false;
            } else {
                $this->_is_working['working_on'] = $the_planet;
                $this->_is_working['is_working'] = true;
            }
        }
    }

    /**
     * method setLabsAmount
     * param
     * return (void)
     */
    private function setLabsAmount()
    {
        $labs_limit = $this->user[$this->_resource[123]] + 1;
        $this->_lab_level = $this->Research_Model->getAllLabsLevel($this->user['user_id'], $labs_limit);
    }
}
