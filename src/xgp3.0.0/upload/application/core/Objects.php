<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Objects
{
	private $_objects;
	private $_relations;
	private $_price;
	private $_combat_specs;
	private $_production;
	private $_objects_list;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		// REQUIRE THIS DAMN FILE
		require ( XGP_ROOT . 'application/core/objects_collection.php' );

		// SET THE ARRAY ELEMENTS TO A PARTICULAR PROPERTY
		$this->_objects			= $resource;
		$this->_relations		= $requeriments;
		$this->_price			= $pricelist;
		$this->_combat_specs	= $CombatCaps;
		$this->_production		= $ProdGrid;
		$this->_objects_list	= $reslist;
	}

	/**
	 * method get_objects
	 * param $object_id
	 * return one particular object or everything
	 */
	public function get_objects ( $object_id = NULL )
	{
		if ( ! empty ( $object_id ) )
		{
			return $this->_objects[$object_id];
		}
		else
		{

			return $this->_objects;
		}
	}

	/**
	 * method get_relations
	 * param $object_id
	 * return one particular object relations or everything
	 */
	public function get_relations ( $object_id = NULL )
	{
		if ( ! empty ( $object_id ) )
		{
			return $this->_relations[$object_id];
		}
		else
		{
			return $this->_relations;
		}
	}

	/**
	 * method get_price
	 * param $object_id
	 * param $resource
	 * return one particular object relations or everything
	 */
	public function get_price ( $object_id = NULL , $resource = '' )
	{
		if ( ! empty ( $object_id ) )
		{
			if ( empty ( $resource ) )
			{
				return $this->_price[$object_id];
			}
			else
			{
				return $this->_price[$object_id][$resource];
			}
		}
		else
		{
			return $this->_price;
		}
	}

	/**
	 * method get_combat_specs
	 * param $object_id
	 * param $type
	 * return one particular object combat specs or everything
	 */
	public function get_combat_specs ( $object_id = NULL , $type = '' )
	{
		if ( ! empty ( $object_id ) )
		{
			if ( empty ( $type ) )
			{
				return $this->_combat_specs[$object_id];
			}
			else
			{
				return $this->_combat_specs[$object_id][$type];
			}
		}
		else
		{
			return $this->_combat_specs;
		}
	}

	/**
	 * method get_production
	 * param $object_id
	 * return one particular object relations or everything
	 */
	public function get_production ( $object_id = NULL )
	{
		if ( ! empty ( $object_id ) )
		{
			return $this->_production[$object_id];
		}
		else
		{
			return $this->_production;
		}
	}

	/**
	 * method get_objects_list
	 * param $object_id
	 * return one particular object list or everything
	 */
	public function get_objects_list ( $object_id = NULL )
	{
		if ( ! empty ( $object_id ) )
		{
			return $this->_objects_list[$object_id];
		}
		else
		{
			return $this->_objects_list;
		}
	}
}

/* end of Objects.php */