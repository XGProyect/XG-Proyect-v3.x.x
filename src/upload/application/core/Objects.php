<?php
/**
 * Objects
 *
 * PHP Version 5.5+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace application\core;

/**
 * Objects Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Objects
{
    private $objects;
    private $relations;
    private $price;
    private $combat_specs;
    private $production;
    private $objects_list;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // REQUIRE THIS DAMN FILE
        require XGP_ROOT . CORE_PATH . 'objects_collection.php';

        // SET THE ARRAY ELEMENTS TO A PARTICULAR PROPERTY
        $this->objects      = $resource;
        $this->relations    = $requeriments;
        $this->price        = $pricelist;
        $this->combat_specs = $CombatCaps;
        $this->production   = $ProdGrid;
        $this->objects_list = $reslist;
    }

    /**
     * getObjects
     *
     * @param int $object_id Object ID
     *
     * @return array
     */
    public function getObjects($object_id = null)
    {
        if (!empty($object_id)) {
            
            return $this->objects[$object_id];
        } else {

            return $this->objects;
        }
    }

    /**
     * getRelations
     *
     * @param int $object_id Object ID
     *
     * @return array
     */
    public function getRelations($object_id = null)
    {
        if (!empty($object_id)) {

            return $this->relations[$object_id];
        } else {

            return $this->relations;
        }
    }

    /**
     * getPrice
     *
     * @param int    $object_id Object ID
     * @param string $resource  Resource
     *
     * @return array
     */
    public function getPrice($object_id = null, $resource = '')
    {
        if (!empty($object_id)) {

            if (empty($resource)) {

                return $this->price[$object_id];
            } else {

                return $this->price[$object_id][$resource];
            }
        } else {

            return $this->price;
        }
    }

    /**
     * getCombatSpecs
     *
     * @param int    $object_id Object ID
     * @param string $type      Type
     *
     * @return array
     */
    public function getCombatSpecs($object_id = null, $type = '')
    {
        if (!empty($object_id)) {

            if (empty($type)) {

                return $this->combat_specs[$object_id];
            } else {

                return $this->combat_specs[$object_id][$type];
            }
        } else {

            return $this->combat_specs;
        }
    }

    /**
     * getProduction
     *
     * @param int $object_id Object ID
     *
     * @return array
     */
    public function getProduction($object_id = null)
    {
        if (!empty($object_id)) {

            return $this->production[$object_id];
        } else {

            return $this->production;
        }
    }

    /**
     * getObjectsList
     *
     * @param int $object_id Object ID
     *
     * @return array
     */
    public function getObjectsList($object_id = null)
    {
        if (!empty($object_id)) {

            return $this->objects_list[$object_id];
        } else {

            return $this->objects_list;
        }
    }
}

/* end of Objects.php */
