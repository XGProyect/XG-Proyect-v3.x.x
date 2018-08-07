<?php
/**
 * Shortcuts
 *
 * PHP Version 5.5+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\libraries\users;

use application\libraries\FunctionsLib;
use Exception;

/**
 * Shortcuts Class
 *
 * @category Classes
 * @package  alliance
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Shortcuts
{

    /**
     * Contains the shortcuts 
     * 
     * @var array
     */
    private $_shortcuts = [];

    /**
     * Constructor
     * 
     * @param string $shortcuts List of shortcuts as a JSON string
     * 
     * @return void
     * 
     * @throws Exception
     */
    public function __construct($shortcuts)
    {
        try {
            if (is_array($shortcuts)) {

                throw new Exception('JSON Expected!');
            }

            $this->setShortcuts($shortcuts);
        } catch (Exception $e) {

            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }
    
    /**
     * Set the shortcuts
     * 
     * @param string $shortcuts Shortcuts
     */
    private function setShortcuts($shortcuts)
    {
        $this->_shortcuts = json_decode($shortcuts, true);
    }
    
    /**
     * Get the shortcuts
     * 
     * @return string
     */
    private function getShortcuts()
    {
        return $this->_shortcuts;
    }

    /**
     * Create a new shortcut
     * 
     * @param string $name
     * @param int $g
     * @param int $s
     * @param int $p
     * @param int $pt
     * 
     * @return array
     * 
     * @throws Exception
     */
    public function addNew($name, $g, $s, $p, $pt)
    {
        try {
            if (empty($name) or empty($g) or empty($s) or empty($p) or empty($pt)) {
                
                throw new Exception('Name cannot be empty or null');
            }

            $filtered_name = FunctionsLib::escapeString(strip_tags($name));

            $this->_shortcuts[] = [
                'name' => $filtered_name,
                'g' => $g,
                's' => $s,
                'p' => $p,
                'pt' => $pt
            ];

            return $this->getShortcuts();
        } catch (Exception $e) {

            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }
    
    /**
     * Edit shortcuts by ID
     * 
     * @param int $shortcut_id
     * @param string $name
     * @param int $g
     * @param int $s
     * @param int $p
     * @param int $pt
     * 
     * @return array
     * 
     * @throws Exception
     */
    public function editById(int $shortcut_id, string $name, int $g, int $s, int $p, int $pt)
    {
        try {
            if (!isset($this->getShortcuts()[$this->validateShortcutId($shortcut_id)])) {
                
                throw new Exception('Shortcut ID doesn\'t exists');
            }

            $filtered_name = FunctionsLib::escapeString(strip_tags($name));
            
            $this->_shortcuts[$shortcut_id] = [
                'name' => $filtered_name,
                'g' => $g,
                's' => $s,
                'p' => $p,
                'pt' => $pt
            ];

            return $this->getShortcuts();
        } catch (Exception $e) {

            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Delete a shortcut by ID
     * 
     * @param int $shortcut_id
     * 
     * @return array
     */
    public function deleteById(int $shortcut_id): array
    {
        array_splice($this->_shortcuts, $this->validateShortcutId($shortcut_id), 1);
        
        return $this->getShortcuts();
    }

    /**
     * Get all the shortcuts as an Array
     * 
     * @return array
     */
    public function getAllAsArray()
    {
        return $this->_shortcuts;
    }
    
    /**
     * Get all the shortcuts as a JSON
     * 
     * @return string
     */
    public function getAllAsJsonString()
    {
        return json_encode($this->_shortcuts);
    }
    
    /**
     * Get the shortcut by ID
     * 
     * @param int $shortcut_id Shortcut ID
     * 
     * @return array
     */
    public function getById($shortcut_id)
    {
        return isset($this->_shortcuts[$shortcut_id]) ? $this->_shortcuts[$shortcut_id] : 0;
    }
    
    /**
     * Validate the shortcut ID
     * 
     * @param type $shortcut_id Shortcut ID
     * 
     * @return int
     */
    private function validateShortcutId($shortcut_id)
    {
        if ($shortcut_id < 0) {
            
            return 0;
        }

        if ($shortcut_id > count($this->_shortcuts)) {
            
            return count($this->_shortcuts) - 1;
        }
        
        return $shortcut_id;
    }
}

/* end of Shortcuts.php */
