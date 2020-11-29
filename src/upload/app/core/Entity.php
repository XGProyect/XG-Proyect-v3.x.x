<?php
/**
 * Entity
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\core;

use Exception;

/**
 * Entity Class
 *
 * @category Entity
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Entity
{
    /**
     *
     * @var array
     */
    protected $data = [];

    /**
     * Init with the db data
     *
     * @param array $data data
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->setData($data);
    }

    /**
     * Set the current data
     *
     * @param array $data data
     *
     * @throws Exception
     *
     * @return void
     */
    private function setData($data)
    {
        try {
            if (!is_array($data)) {
                return null;
            }

            $this->data = $data;
        } catch (Exception $e) {
            die('Caught exception: ' . $e->getMessage() . "\n");
        }
    }
}
