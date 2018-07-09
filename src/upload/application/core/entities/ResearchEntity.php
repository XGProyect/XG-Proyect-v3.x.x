<?php
/**
 * Research entity
 *
 * PHP Version 5.5+
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
 * Research Entity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class ResearchEntity extends Entity
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
     * Return the research id
     * 
     * @return string
     */
    public function getResearchId()
    {
        return $this->_data['research_id'];
    }
    /*


	2	research_user_idIndex	int(11)		UNSIGNED	No	None			 Change Change	 Drop Drop	
More More
	3	research_current_research	int(11)			No	0			 Change Change	 Drop Drop	
More More
	4	research_espionage_technology	int(11)			No	0			 Change Change	 Drop Drop	
More More
	5	research_computer_technology	int(11)			No	0			 Change Change	 Drop Drop	
More More
	6	research_weapons_technology	int(11)			No	0			 Change Change	 Drop Drop	
More More
	7	research_shielding_technology	int(11)			No	0			 Change Change	 Drop Drop	
More More
	8	research_armour_technology	int(11)			No	0			 Change Change	 Drop Drop	
More More
	9	research_energy_technology	int(11)			No	0			 Change Change	 Drop Drop	
More More
	10	research_hyperspace_technology	int(11)			No	0			 Change Change	 Drop Drop	
More More
	11	research_combustion_drive	int(11)			No	0			 Change Change	 Drop Drop	
More More
	12	research_impulse_drive	int(11)			No	0			 Change Change	 Drop Drop	
More More
	13	research_hyperspace_drive	int(11)			No	0			 Change Change	 Drop Drop	
More More
	14	research_laser_technology	int(11)			No	0			 Change Change	 Drop Drop	
More More
	15	research_ionic_technology	int(11)			No	0			 Change Change	 Drop Drop	
More More
	16	research_plasma_technology	int(11)			No	0			 Change Change	 Drop Drop	
More More
	17	research_intergalactic_research_network	int(11)			No	0			 Change Change	 Drop Drop	
More More
	18	research_astrophysics	int(11)			No	0			 Change Change	 Drop Drop	
More More
	19	research_graviton_technology*/
}

/* end of ResearchEntity.php */
