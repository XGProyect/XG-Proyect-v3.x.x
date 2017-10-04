<?php
/**
 * Building
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

namespace application\libraries\buildings;

use application\libraries\buildings\Queue;
use application\libraries\buildings\QueueElements;
use application\libraries\OfficiersLib;

/**
 * Buildings Class
 *
 * @category Classes
 * @package  building
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Building
{
    /**
     *
     * @var string $_queue Queue
     */
    private $_queue = '';

    /**
     *
     * @var array $_user User
     */
    private $_user  = '';
    
    /**
     *
     * @var int $_building building ID
     */
    private $_building          = 0;

    /**
     *
     * @var int $_build_level current building level
     */
    private $_build_level       = 0;

    /**
     *
     * @var int $_build_time building time
     */
    private $_build_time        = 0;

    /**
     * Init the class with some values
     *
     * @param type $current_queue  Current Queue
     *
     * @return void
     */
    public function __construct($current_queue, $user)
    {
        $this->_queue   = new Queue($current_queue);
        $this->_user    = $user;
    }

    /**
     *
     */
    public function addBuilding()
    {
        // set building
        // set build level
        // calculate build time, recalculate based on next building
        // calculate build end time, recalculate based on final build time
        // set build mode

        $this->queueElementToBuild();
    }

    /**
     *
     */
    public function removeBuilding()
    {
        $this->removeElementFromBuildingQueue($element_id);
    }

    /**
     *
     */
    public function cancelBuilding()
    {
        $this->removeFirstElementFromBuildingQueue();
    }

    /**
     *
     */
    public function tearDownBuilding()
    {
        $this->queueElementToTearDown();
    }

    /**
     * Count current elements in the queue
     * 
     * @return int
     */
    public function getElementsOnQueue()
    {
        return $this->_queue->countQueueElements();
    }
    
    /**
     * Check if the queue is full
     * 
     * @return boolean
     */
    public function isQueueFull()
    {
        $queue_size = 1;
        
        if (OfficiersLib::isOfficierActive($this->_user['premium_officier_commander'])) {

            $queue_size = MAX_BUILDING_QUEUE_SIZE;
        }
        
        if ($this->getElementsOnQueue() < $queue_size) {
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Create a new QueueElements block
     *
     * @param string $build_mode Build mode
     *
     * @return QueueElements
     */
    private function buildQueueElementsBlock($build_mode)
    {
        $queue_elements = new QueueElements;
        $queue_elements->building       = $this->building;
        $queue_elements->build_level    = $this->build_level;
        $queue_elements->build_time     = $this->calculateBuildTime();
        $queue_elements->build_end_time = $this->calculateBuildEndTime();
        $queue_elements->build_mode     = $build_mode;

        return $queue_elements;
    }

    /**
     * Queue an element to be build
     *
     * @return void
     */
    private function queueElementToBuild()
    {
        $this->_queue->addElementToQueue(
            $this->buildQueueElementsBlock('build')
        );
    }

    /**
     * Queue an element to tear down
     *
     * @return void
     */
    private function queueElementToTearDown()
    {
        $this->_queue->addElementToQueue(
            $this->buildQueueElementsBlock('teardown')
        );
    }

    /**
     * Remove an element from the queue
     *
     * @return void
     */
    private function removeElementFromBuildingQueue($element_id)
    {
        $this->_queue->removeElementFromQueue($element_id);
    }

    /**
     * Remove the first element from the queue, cancel action
     *
     * @return void
     */
    private function removeFirstElementFromBuildingQueue()
    {
        $this->removeElementFromBuildingQueue(0);
    }
    
    /**
     *
     * @return type
     */
    private function calculateBuildTime()
    {
        return $this->build_time;
    }

    /**
     * Calculate the building time for each element
     * depending if it's the first element in the queue
     * or there's something before.
     * 
     * @return int
     */
    private function calculateBuildEndTime()
    {
        if (thereAreQueueElements) {

            return time() + $this->build_time;
        } else {

            return prevElementBuildTime + $this->build_time;
        }
    }
}

/* end of Buildings.php */
