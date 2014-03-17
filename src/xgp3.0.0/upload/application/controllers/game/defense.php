<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Defense extends XGPCore
{
	const MODULE_ID	= 12;

	private $_lang;
	private $_resource;
	private $_price;
	private $_current_user;
	private $_current_planet;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();

		// check if session is active
		parent::$users->check_session();

		// Check module access
		Functions_Lib::module_message ( Functions_Lib::is_module_accesible ( self::MODULE_ID ) );

		$this->_lang			= parent::$lang;
		$this->_resource		= parent::$objects->get_objects();
		$this->_price			= parent::$objects->get_price();
		$this->_current_user	= parent::$users->get_user_data();
		$this->_current_planet	= parent::$users->get_planet_data();

		$this->build_page();
	}

	/**
	 * method __destruct
	 * param
	 * return close db connection
	 */
	public function __destruct()
	{
		parent::$db->close_connection();
	}

	public function build_page()
	{
		$parse = $this->_lang;

		if ( isset ( $_POST['fmenge'] ) )
		{
			$Missiles[502] 	= $this->_current_planet[ $this->_resource[502] ];
			$Missiles[503] 	= $this->_current_planet[ $this->_resource[503] ];
			$SiloSize      	= $this->_current_planet[ $this->_resource[44] ];
			$MaxMissiles   	= $SiloSize * 10;
			$BuildQueue    	= $this->_current_planet['planet_b_hangar_id'];
			$BuildArray    	= explode (";", $BuildQueue);
			$totalCount		= 0;

			for ($QElement = 0; $QElement < count($BuildArray); $QElement++)
			{
				$ElmentArray = explode (",", $BuildArray[$QElement] );
				if($ElmentArray[0] == 502)
				{
					$Missiles[502] += $ElmentArray[1];
				}
				elseif($ElmentArray[0] == 503)
				{
					$Missiles[503] += $ElmentArray[1];
				}
			}

			foreach($_POST['fmenge'] as $Element => $Count)
			{
				if($Element < 300 OR $Element > 550)
				{
					continue;
				}

				$Element 	 = (int)$Element;
				$Count   	 = (int)$Count;
				$totalCount += $Count;

				if ($Count > MAX_FLEET_OR_DEFS_PER_ROW)
				{
					$Count = MAX_FLEET_OR_DEFS_PER_ROW;
				}

				if ($Count != 0)
				{
					if ( $Element == 407 or $Element == 408 )
					{
						if ( ! $this->is_shield_in_queue ( $BuildQueue , $Element ) )
						{
							$Count = 1;
						}
						else
						{
							$Count = 0;
						}
					}

					if (Developments_Lib::is_development_allowed($this->_current_user, $this->_current_planet, $Element))
					{
						$MaxElements = $this->GetMaxConstructibleElements ( $Element, $this->_current_planet );

						if ($Element == 502 || $Element == 503)
						{
							$ActuMissiles  = $Missiles[502] + ( 2 * $Missiles[503] );
							$MissilesSpace = $MaxMissiles - $ActuMissiles;
							if ($Element == 502)
							{
								if ( $Count > $MissilesSpace )
								{
									$Count = $MissilesSpace;
								}

							}
							else
							{
								if ( $Count > floor( $MissilesSpace / 2 ) )
								{
									$Count = floor( $MissilesSpace / 2 );
								}
							}

							if ($Count > $MaxElements)
							{
								$Count = $MaxElements;
							}


							$Missiles[$Element] += $Count;
						}
						else
						{
							if ($Count > $MaxElements)
							{
								$Count = $MaxElements;
							}

						}

						$Ressource = $this->GetElementRessources ( $Element, $Count );

						if ($Count >= 1)
						{
							$this->_current_planet['planet_metal']           -= $Ressource['metal'];
							$this->_current_planet['planet_crystal']         -= $Ressource['crystal'];
							$this->_current_planet['planet_deuterium']       -= $Ressource['deuterium'];
							$this->_current_planet['planet_b_hangar_id']     .= '' . $Element . ',' . $Count . ';';
						}
					}
				}
			}

			if ( $totalCount > 0 )
			{
				parent::$db->query ( "UPDATE " . PLANETS . " AS p SET
										p.`planet_b_hangar_id` = '".$this->_current_planet['planet_b_hangar_id']."',
										p.`planet_metal` = '".$this->_current_planet['planet_metal']."',
										p.`planet_crystal` = '".$this->_current_planet['planet_crystal']."',
										p.`planet_deuterium` = '".$this->_current_planet['planet_deuterium']."'
										WHERE p.`planet_id` = '".$this->_current_planet['planet_id']."';" );
			}

			Functions_Lib::redirect ( 'game.php?page=defense' );
		}

		if ($this->_current_planet[$this->_resource[21]] == 0)
			Functions_Lib::message($this->_lang['bd_shipyard_required'], '', '', TRUE);

		$NotBuilding = TRUE;

		if ($this->_current_planet['planet_b_building_id'] != 0)
		{
			$CurrentQueue = $this->_current_planet['planet_b_building_id'];
			if (strpos ($CurrentQueue, ";"))
			{
				// FIX BY LUCKY - IF THE SHIPYARD IS IN QUEUE THE USER CANT RESEARCH ANYTHING...
				$QueueArray		= explode (";", $CurrentQueue);

				for($i = 0; $i < MAX_BUILDING_QUEUE_SIZE; $i++)
				{
					$ListIDArray	= explode (",", $QueueArray[$i]);
					$Element		= $ListIDArray[0];

					if ( ($Element == 21 ) or ( $Element == 14 ) or ( $Element == 15 ) )
					{
						break;
					}
				}
				// END - FIX
			}
			else
			{
				$CurrentBuilding = $CurrentQueue;
			}

			if ( ( ( $CurrentBuilding == 21 ) or ( $CurrentBuilding == 14 ) or ( $CurrentBuilding == 15 ) ) or  (($Element == 21 ) or ( $Element == 14 ) or ( $Element == 15 )) ) // ADDED (or $Element == 21) BY LUCKY
			{
				$parse['message'] = "<font color=\"red\">".$this->_lang['bd_building_shipyard']."</font>";
				$NotBuilding = FALSE;
			}


		}

		$TabIndex  	= 0;
		$PageTable 	= "";
		$BuildQueue	= '';

		foreach($this->_lang['tech'] as $Element => $ElementName)
		{
			if ($Element > 400 && $Element <= 599)
			{
				if (Developments_Lib::is_development_allowed($this->_current_user, $this->_current_planet, $Element))
				{
					$CanBuildOne         			= Developments_Lib::is_development_payable($this->_current_user, $this->_current_planet, $Element, FALSE);
					$BuildOneElementTime 			= Developments_Lib::development_time($this->_current_user, $this->_current_planet, $Element);
					$ElementCount        			= $this->_current_planet[$this->_resource[$Element]];
					$ElementNbre         			= ($ElementCount == 0) ? "" : " (". $this->_lang['bd_available'] . Format_Lib::pretty_number($ElementCount) . ")";

					$parse['add_element']			= '';
					$parse['dpath']					= DPATH;
					$parse['element']				= $Element;
					$parse['element_name']			= $ElementName;
					$parse['element_description']	= $this->_lang['res']['descriptions'][$Element];
					$parse['element_price']			= Developments_Lib::formated_development_price($this->_current_user, $this->_current_planet, $Element, FALSE);
					$parse['building_time']			= Developments_Lib::formated_development_time($BuildOneElementTime);
					$parse['element_nbre']			= $ElementNbre;

					if ( $CanBuildOne && ! parent::$users->is_on_vacations ( $this->_current_user ) )
					{
						$InQueue = strpos ( $this->_current_planet['planet_b_hangar_id'], $Element.",");
						$IsBuildp = ($this->_current_planet[$this->_resource[407]] >= 1) ? TRUE : FALSE;
						$IsBuildg = ($this->_current_planet[$this->_resource[408]] >= 1) ? TRUE : FALSE;
						$BuildIt = TRUE;

						if ($Element == 407 || $Element == 408 )
						{
							$BuildIt = FALSE;

							if ( $Element == 407 && !$IsBuildp && $InQueue === FALSE )
								$BuildIt = TRUE;

							if ( $Element == 408 && !$IsBuildg && $InQueue === FALSE )
								$BuildIt = TRUE;

						}

						if (!$BuildIt)
						{
							$parse['add_element'] 		= '<font color="red">'.$this->_lang['bd_protection_shield_only_one'].'</font>';
						}
						elseif($NotBuilding)
						{
							$TabIndex++;
							$parse['add_element'] 		= '<input type=text name=fmenge['.$Element.'] alt="'.$this->_lang['tech'][$Element].'" size=6 maxlength=6 value=0 tabindex='.$TabIndex.'>';
						}

						if($NotBuilding)
						{
							$parse['build_defenses']	= '<tr><td class="c" colspan="2" align="center"><input type="submit" value="'.$this->_lang['bd_build_defenses'].'"></td></tr>';
						}
					}

					$PageTable .= parent::$page->parse_template (parent::$page->get_template ('buildings/buildings_fleet_row'), $parse);
				}
			}
		}

		if ($this->_current_planet['planet_b_hangar_id'] != '')
			$BuildQueue .= $this->ElementBuildListBox( 'defenses' );

		$parse['buildlist']    	= $PageTable;
		$parse['buildinglist'] 	= $BuildQueue;

		parent::$page->display (parent::$page->parse_template (parent::$page->get_template ('buildings/buildings_defense'), $parse));
	}

	//optimized by alivan & jstar
	private function GetMaxConstructibleElements ( $Element , $Ressources )
    {
        $Buildable=array();
        if ($this->_price[$Element]['metal'] != 0)
            $Buildable['metal']     = floor($Ressources['metal'] / $this->_price[$Element]['metal']);

        if ($this->_price[$Element]['crystal'] != 0)
            $Buildable['crystal']   = floor($Ressources['crystal'] / $this->_price[$Element]['crystal']);

        if ($this->_price[$Element]['deuterium'] != 0)
            $Buildable['deuterium'] = floor($Ressources['deuterium'] / $this->_price[$Element]['deuterium']);

        if ($this->_price[$Element]['energy'] != 0)
            $Buildable['energy']    = floor($Ressources['planet_energy_max'] / $this->_price[$Element]['energy']);

        return max(min($Buildable),0);
    }

	private function GetElementRessources($Element, $Count)
	{
		$ResType['metal']     = ($this->_price[$Element]['metal']     * $Count);
		$ResType['crystal']   = ($this->_price[$Element]['crystal']   * $Count);
		$ResType['deuterium'] = ($this->_price[$Element]['deuterium'] * $Count);

		return $ResType;
	}

	private function ElementBuildListBox ( $current_page )
	{
		$ElementQueue 	= explode ( ';' , $this->_current_planet['planet_b_hangar_id'] );
		$NbrePerType  	= '';
		$NamePerType  	= '';
		$TimePerType	= '';
		$QueueTime		= 0;

		foreach ( $ElementQueue as $ElementLine => $Element )
		{
			if ( $Element != '' )
			{
				$Element		= explode(',', $Element);
				$ElementTime  	= Developments_Lib::development_time( $this->_current_user, $this->_current_planet, $Element[0] );
				$QueueTime   	+= $ElementTime * $Element[1];
				$TimePerType 	.= "".$ElementTime.",";
				$NamePerType 	.= "'". html_entity_decode ( $this->_lang['tech'][$Element[0]], ENT_COMPAT , "utf-8" ) ."',";
				$NbrePerType 	.= "".$Element[1].",";
			}
		}

		$parse 							= $this->_lang;
		$parse['a'] 					= $NbrePerType;
		$parse['b'] 					= $NamePerType;
		$parse['c'] 					= $TimePerType;
		$parse['b_hangar_id_plus'] 		= $this->_current_planet['planet_b_hangar'];
		$parse['current_page']			= $current_page;
		$parse['pretty_time_b_hangar'] 	= Format_Lib::pretty_time($QueueTime - $this->_current_planet['planet_b_hangar']);

		return parent::$page->parse_template ( parent::$page->get_template ( 'buildings/buildings_script' ) , $parse );
	}

	/**
	 * method is_shield_in_queue
	 * param $queue
	 * param $element
	 * return true if the shield was found in the shipyard queue
	 */
	private function is_shield_in_queue ( $queue , $element )
	{
		return ( strpos ( $queue , $element . ',' ) );
	}

}
/* end of shipyard.php */