<?php

namespace App\Core;

class Objects
{
    private array $objects;
    private array $relations;
    private array $price;
    private array $combat_specs;
    private array $production;
    private array $objects_list;

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
