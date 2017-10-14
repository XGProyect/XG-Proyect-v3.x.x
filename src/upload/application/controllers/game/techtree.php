<?php
/**
 * Techtree Controller
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
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;

/**
 * Techtree Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Techtree extends Controller
{

    const MODULE_ID = 10;

    private $_lang;
    private $_resource;
    private $_requeriments;
    private $_current_user;
    private $_current_planet;

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

        $this->_lang = parent::$lang;
        $this->_resource = parent::$objects->getObjects();
        $this->_requeriments = parent::$objects->getRelations();
        $this->_current_user = parent::$users->getUserData();
        $this->_current_planet = parent::$users->getPlanetData();

        $this->build_page();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $parse = $this->_lang;
        $page = '';
        $header_template = parent::$page->getTemplate('techtree/techtree_head');
        $row_template = parent::$page->getTemplate('techtree/techtree_row');

        foreach ($this->_lang['tech'] as $element => $element_name) {
            if ($element < 600) {
                $parse['tt_name'] = $element_name;

                if (!isset($this->_resource[$element])) {
                    $parse['Requirements'] = $this->_lang['tt_requirements'];
                    $page .= parent::$page->parseTemplate($header_template, $parse);
                } else {
                    if (isset($this->_requeriments[$element])) {
                        $list = '';

                        foreach ($this->_requeriments[$element] as $requirement => $level) {
                            if (isset($this->_current_user[$this->_resource[$requirement]]) && $this->_current_user[$this->_resource[$requirement]] >= $level) {
                                $list .= FormatLib::colorGreen($this->set_level_format($level, $this->_lang['tech'][$requirement]));
                            } elseif (isset($this->_current_planet[$this->_resource[$requirement]]) && $this->_current_planet[$this->_resource[$requirement]] >= $level) {
                                $list .= FormatLib::colorGreen($this->set_level_format($level, $this->_lang['tech'][$requirement]));
                            } else {
                                $list .= FormatLib::colorRed($this->set_level_format($level, $this->_lang['tech'][$requirement]));
                            }

                            $list .= '<br/>';
                        }

                        $parse['required_list'] = $list;
                    } else {
                        $parse['required_list'] = '';
                        $parse['tt_detail'] = '';
                    }

                    $parse['tt_info'] = $element;
                    $page .= parent::$page->parseTemplate($row_template, $parse);
                }
            }
        }

        $parse['techtree_list'] = $page;

        return parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('techtree/techtree_body'), $parse));
    }

    /**
     * method set_level_format
     * param $level
     * param $tech_name
     * return (string) format tech with level
     */
    private function set_level_format($level, $tech_name)
    {
        return $tech_name . ' (' . $this->_lang['tt_lvl'] . $level . ')';
    }
}

/* end of techtree.php */
