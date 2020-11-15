<?php
/**
 * Objects.php
 *
 * @author   XG Proyect Team
 * @license  https://www.xgproyect.org XG Proyect
 * @link     https://www.xgproyect.org
 * @version  3.2.0
 */
namespace App\core;

/**
 * Objects Class
 */
class Objects
{
    /**
     * Contains an array with resources
     *
     * @var array
     */
    private $objects;

    /**
     * Contains an array with requirements
     *
     * @var array
     */
    private $relations;

    /**
     * Contains an array with the price list
     *
     * @var array
     */
    private $price;

    /**
     * Contains an array with the combat specs
     *
     * @var array
     */
    private $combat_specs;

    /**
     * Contains an array with the production formulas
     *
     * @var array
     */
    private $production;

    /**
     * Contains an array with the set of objects by type and related IDs
     *
     * @var array
     */
    private $objects_list;

    /**
     * Constructor
     */
    public function __construct()
    {
        // REQUIRE THIS DAMN FILE
        require XGP_ROOT . CORE_PATH . 'objects_collection.php';

        // SET THE ARRAY ELEMENTS TO A PARTICULAR PROPERTY
        $this->objects = $resource;
        $this->relations = $requeriments;
        $this->price = $pricelist;
        $this->combat_specs = $CombatCaps;
        $this->production = $ProdGrid;
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
