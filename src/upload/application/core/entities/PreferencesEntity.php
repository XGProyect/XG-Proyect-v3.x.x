<?php
/**
 * Preferences entity
 *
 * PHP Version 7+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\core\entities;

use application\core\Entity;

/**
 * Notes Entity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class PreferencesEntity extends Entity
{

    /**
     * Constructor
     * 
     * @param array $data Data
     * 
     * @return void
     */
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Return the preference id
     * 
     * @return string
     */
    public function gePreferenceId()
    {
        return $this->_data['preference_id'];
    }

    /**
     * Return the preference user id
     * 
     * @return string
     */
    public function getPreferenceUsedId()
    {
        return $this->_data['preference_user_id'];
    }

    /**
     * Return the preference spy probes
     * 
     * @return string
     */
    public function getPreferenceSpyProbes()
    {
        return $this->_data['preference_spy_probes'];
    }

    /**
     * Return the preference planet sort
     * 
     * @return string
     */
    public function getPreferencePlanetSort()
    {
        return $this->_data['preference_planet_sort'];
    }

    /**
     * Return the prefernce planet sort sequence
     * 
     * @return string
     */
    public function getPreferencePlanetSortSequence()
    {
        return $this->_data['preference_planet_sort_sequence'];
    }
    
    /**
     * Return the preference vacation mode
     * 
     * @return string
     */
    public function getPreferenceVacationMode()
    {
        return $this->_data['preference_vacation_mode'];
    }

    /**
     * Return the preference delete mode
     * 
     * @return string
     */
    public function getPreferenceDeleteMode()
    {
        return $this->_data['preference_delete_mode'];
    }
}

/* end of PreferencesEntity.php */
