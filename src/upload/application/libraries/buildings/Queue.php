<?php
/**
 * Buildings
 *
 * PHP Version 7.1+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\libraries\buildings;

use application\libraries\buildings\QueueElements;

/**
 * Queue Class
 *
 * @category Classes
 * @package  buildings
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
final class Queue
{

    const QUEUE_SEPARATOR = ';';
    const ITEM_SEPARATOR = ',';

    /**
     * @var array $_queue Queue in array format
     */
    private $_queue = [];

    /**
     * Init with current queue
     * 
     * @param string $current_queue The current queue
     *
     * @return void
     */
    public function __construct($current_queue = [])
    {
        $this->_queue = $current_queue;
    }

    /**
     * Process the queue and put it into an array format
     *
     * @return void
     */
    private function breakDownCurrentQueue()
    {
        // extract elements and filter empty values
        $elements = array_filter(explode(self::QUEUE_SEPARATOR, $this->_queue));
        $queue = [];

        if (is_array($elements)) {

            foreach ($elements as $element_id => $content) {

                $queue[$element_id] = explode(self::ITEM_SEPARATOR, $content);
            }
        }

        $this->_queue = $queue;
    }

    /**
     * Process the queue and put into a string
     *
     * @return void
     */
    private function makeUpCurrentQueue()
    {
        if (isset($this->_queue)) {

            $queue = $this->_queue;

            foreach ($queue as $element_id => $content) {

                $queue[$element_id] = implode(self::ITEM_SEPARATOR, $content);
            }

            $this->_queue = implode(self::QUEUE_SEPARATOR, $queue);
        }
    }

    /**
     * Adds an element to the queue
     *
     * @param QueueElements $queue_elements Elements to queue
     *
     * @return void
     */
    public function addElementToQueue(QueueElements $queue_elements)
    {
        if (is_object($queue_elements)) {

            if (!is_array($this->_queue)) {

                $this->breakDownCurrentQueue();
            }

            // convert the object to an array and put it to the end
            array_push($this->_queue, (array) $queue_elements);
        }
    }

    /**
     * Removes an element from the queue
     *
     * @param int $element_id Element ID
     *
     * @return void
     */
    public function removeElementFromQueue($element_id)
    {
        if (is_int($element_id)) {

            if (!is_array($this->_queue)) {

                $this->breakDownCurrentQueue();
            }

            // unset that element from the array
            unset($this->_queue[$element_id]);
        }
    }

    /**
     * Returns an element from the queue
     *
     * @param int $element_id Element ID
     *
     * @return array
     */
    public function getElementFromQueueAsArray($element_id)
    {
        if (isset($this->_queue)) {

            if (!is_array($this->_queue)) {

                $this->breakDownCurrentQueue();
            }

            return $this->_queue[$element_id];
        }

        return [];
    }

    /**
     * Returns the queue as a string
     *
     * @return string
     */
    public function returnQueueAsString()
    {
        if (isset($this->_queue)) {

            if (is_array($this->_queue)) {

                $this->makeUpCurrentQueue();
            }

            return $this->_queue;
        }

        return '';
    }

    /**
     * Returns the queue as an associative array
     *
     * @return array
     */
    public function returnQueueAsArray()
    {
        if (isset($this->_queue)) {

            if (!is_array($this->_queue)) {

                $this->breakDownCurrentQueue();
            }

            return $this->_queue;
        }

        return [];
    }

    /**
     * Count the amount of elements of the current queue
     * 
     * @return int
     */
    public function countQueueElements()
    {
        if (isset($this->_queue)) {

            if (!is_array($this->_queue)) {

                $this->breakDownCurrentQueue();
            }

            return count($this->_queue);
        }

        return 0;
    }
}

/* end of Queue.php */
