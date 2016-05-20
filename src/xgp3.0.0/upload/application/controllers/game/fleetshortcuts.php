<?php
/**
 * Fleetshortcuts Controller
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

use application\core\XGPCore;
use application\libraries\FunctionsLib;
use application\libraries\OfficiersLib;

/**
 * Fleetshortcuts Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Fleetshortcuts extends XGPCore
{
    const MODULE_ID = 8;

    private $_current_user;
    private $_lang;

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

        $this->_current_user    = parent::$users->getUserData();
        $this->_lang            = parent::$lang;
        
        if (!OfficiersLib::isOfficierActive($this->_current_user['premium_officier_commander'])) {

            FunctionsLib::redirect('game.php?page=officier');
        } else {

            $this->build_page();
        }
    }

    /**
     * method __destruct
     * param
     * return close db connection
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        if (!empty($_GET['mode'])) {
            $mode = $_GET['mode'];

            if ($mode == "add" && !empty($_POST['galaxy']) && !empty($_POST['system']) && !empty($_POST['planet'])) {
                $this->addFleetShortcuts(parent::$db->escapeValue(strip_tags($_POST['name'])), (int) $_POST['galaxy'], (int) $_POST['system'], (int) $_POST['planet'], (int) $_POST['moon']);
            } elseif ($mode == "edit" && isset($_GET['a']) && !empty($_POST['galaxy']) && !empty($_POST['system']) && !empty($_POST['planet'])) {
                $this->saveFleetShortcuts((int) $_GET['a'], parent::$db->escapeValue(strip_tags($_POST['name'])), (int) $_POST['galaxy'], (int) $_POST['system'], (int) $_POST['planet'], (int) $_POST['moon']);
            } elseif ($mode == "delete" && isset($_GET['a'])) {
                $this->deleteFleetShortcuts((int) $_GET['a']);
            } elseif (isset($_GET['a'])) {
                $this->showEditPanelWithID((int) $_GET['a']);
            } else {
                $this->showEditPanel();
            }
        } else {
            $this->showAll();
        }
    }

    private function showEditPanel()
    {
        $parse = $this->_lang;
        $parse['mode'] = "add";
        $parse['visibility'] = "hidden";

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('shortcuts/shortcuts_editPanel'), $parse));
    }

    private function showEditPanelWithID($id)
    {
        $parse = $this->_lang;
        $parse['shortcut_id'] = "&a=" . $id;
        $parse['mode'] = "edit";

        $scarray = explode(";", $this->_current_user['user_fleet_shortcuts']);
        $c = explode(',', $scarray[$id]);

        $parse['name'] = $c[0];
        $parse['galaxy'] = $c[1];
        $parse['system'] = $c[2];
        $parse['planet'] = $c[3];
        $parse['moon' . $c[4]] = 'selected="selected"';
        $parse['visibility'] = "button";

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('shortcuts/shortcuts_editPanel'), $parse));
    }

    private function saveFleetShortcuts($id, $name, $galaxy, $system, $planet, $moon)
    {
        $scarray = explode(";", $this->_current_user['user_fleet_shortcuts']);
        $scarray[$id] = "{$name},{$galaxy},{$system},{$planet},{$moon};";

        $this->_current_user['user_fleet_shortcuts'] = implode(";", $scarray);

        parent::$db->query("UPDATE " . USERS . " SET
								user_fleet_shortcuts='" . ($this->_current_user['user_fleet_shortcuts']) . "'
								WHERE user_id=" . ($this->_current_user['user_id']));

        FunctionsLib::redirect('game.php?page=shortcuts');
    }

    private function addFleetShortcuts($name, $galaxy, $system, $planet, $moon)
    {
        $this->_current_user['user_fleet_shortcuts'] .= "{$name},{$galaxy},{$system},{$planet},{$moon};";

        parent::$db->query("UPDATE " . USERS . " SET
								user_fleet_shortcuts='" . ($this->_current_user['user_fleet_shortcuts']) . "'
								WHERE user_id=" . ($this->_current_user['user_id']));

        FunctionsLib::redirect('game.php?page=shortcuts');
    }

    private function deleteFleetShortcuts($id)
    {
        $scarray = explode(";", $this->_current_user['user_fleet_shortcuts']);

        unset($scarray[$id]);

        $this->_current_user['user_fleet_shortcuts'] = implode(";", $scarray);

        parent::$db->query("UPDATE " . USERS . " SET
							user_fleet_shortcuts='" . ($this->_current_user['user_fleet_shortcuts']) . "'
							WHERE user_id=" . ($this->_current_user['user_id']));

        FunctionsLib::redirect('game.php?page=shortcuts');
    }

    private function showAll()
    {
        $parse = $this->_lang;

        if ($this->_current_user['user_fleet_shortcuts']) {

            $scarray                = explode(";", $this->_current_user['user_fleet_shortcuts']);
            $sx                     = true;
            $e                      = 0;
            $ShortcutsRowTPL        = parent::$page->getTemplate("shortcuts/shortcuts_row");
            $parse['block_rows']    = '';
            
            foreach ($scarray as $a => $b) {
                if (!empty($b)) {
                    $c = explode(',', $b);

                    if ($sx) {
                        $parse['block_rows'] .= "<tr height=\"20\">";
                    }

                    $block['shortcut_id'] = $e++;
                    $block['shortcut_name'] = $c[0];
                    $block['shortcut_galaxy'] = $c[1];
                    $block['shortcut_system'] = $c[2];
                    $block['shortcut_planet'] = $c[3];

                    if ($c[4] == 2) {
                        $block['shortcut_moon'] = $this->_lang['fl_debris_shortcut'];
                    } elseif ($c[4] == 3) {
                        $block['shortcut_moon'] = $this->_lang['fl_moon_shortcut'];
                    } else {
                        $block['shortcut_moon'] = "";
                    }

                    $parse['block_rows'] .= parent::$page->parseTemplate($ShortcutsRowTPL, $block);

                    if (!$sx) {
                        $parse['block_rows'] .= "</tr>";
                    }

                    $sx = !$sx;
                }
            }
            if (!$sx) {
                $parse['block_rows'] .= "<td>&nbsp;</td></tr>";
            }
        } else {
            $parse['block_rows'] = "<th colspan=\"2\">" . $this->_lang['fl_no_shortcuts'] . "</th>";
        }

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('shortcuts/shortcuts_table'), $parse));
    }
}

/* end of fleetshortcuts.php */
