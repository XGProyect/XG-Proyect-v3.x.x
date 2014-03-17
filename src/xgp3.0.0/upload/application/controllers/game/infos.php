<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Infos extends XGPCore
{
	const MODULE_ID	= 24;

	private $_lang;
	private $_current_user;
	private $_current_planet;
	private $_element_id;
	private $_resource;
	private $_pricelist;
	private $_combat_caps;
	private $_prod_grid;

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
		$this->_pricelist		= parent::$objects->get_price();
		$this->_combat_caps		= parent::$objects->get_combat_specs();
		$this->_prod_grid		= parent::$objects->get_production();
		$this->_current_user	= parent::$users->get_user_data();
		$this->_current_planet	= parent::$users->get_planet_data();
		$this->_element_id		= isset ( $_GET['gid'] ) ? (int)$_GET['gid'] : NULL;

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

	/**
	 * method build_page
	 * param
	 * return main method, loads everything
	 */
    private function build_page()
    {
		if ( !array_key_exists ( $this->_element_id , $this->_resource ) )
		{
			Functions_Lib::redirect ( 'game.php?page=techtree' );
		}

        $GateTPL              	= '';
        $DestroyTPL           	= '';
        $TableHeadTPL         	= '';
        $TableFooterTPL			= '';

        $parse                	= $this->_lang;
        $parse['dpath']       	= DPATH;
        $parse['name']        	= $this->_lang['info'][$this->_element_id]['name'];
        $parse['image']       	= $this->_element_id;
        $parse['description'] 	= $this->_lang['info'][$this->_element_id]['description'];

        if ( $this->_element_id < 13 or ( $this->_element_id == 43 && $this->_current_planet[$this->_resource[43]] > 0 ) )
        {
	    	$PageTPL 	= parent::$page->get_template ( 'infos/info_buildings_table' );
        }
        elseif ( $this->_element_id < 200 )
        {
            $PageTPL 	= parent::$page->get_template ( 'infos/info_buildings_general' );
        }
        elseif ( $this->_element_id < 400 )
        {
            $PageTPL 	= parent::$page->get_template ('infos/info_buildings_fleet');
        }
        elseif ( $this->_element_id < 600 )
        {
            $PageTPL	= parent::$page->get_template ('infos/info_buildings_defense');
        }
        else
        {
            $PageTPL	= parent::$page->get_template ('infos/info_officiers_general');
        }

        //Sólo hay destroy en <200
        if($this->_element_id < 200 && $this->_element_id != 33 && $this->_element_id != 41)
        {
	        $DestroyTPL           = parent::$page->get_template ('infos/info_buildings_destroy');
        }

        if ($this->_element_id >= 1 && $this->_element_id <= 3)
        {
            $PageTPL              = parent::$page->get_template ('infos/info_buildings_table');
            $TableHeadTPL         = parent::$page->get_template ('infos/info_production_header');
            $TableTPL             = parent::$page->get_template ('infos/info_production_body');
        }
        elseif ($this->_element_id == 4)
        {
            $PageTPL              = parent::$page->get_template ('infos/info_buildings_table');
            $TableHeadTPL         = parent::$page->get_template ('infos/info_production_simple_header');
            $TableTPL             = parent::$page->get_template ('infos/info_production_simple_body');
        }
        elseif ( $this->_element_id >= 22 && $this->_element_id <= 24 )
        {
            $PageTPL              = parent::$page->get_template ( 'infos/info_buildings_table' );
            $DestroyTPL           = parent::$page->get_template ( 'infos/info_buildings_destroy' );
            $TableHeadTPL         = parent::$page->get_template ( 'infos/info_storage_header' );
            $TableTPL             = parent::$page->get_template ( 'infos/info_storage_table' );
        }
        elseif ($this->_element_id == 12)
        {
            $TableHeadTPL         = parent::$page->get_template ('infos/info_energy_header');
            $TableTPL             = parent::$page->get_template ('infos/info_energy_body');
        }
        elseif ($this->_element_id ==  42)
        {
        	$TableHeadTPL         = parent::$page->get_template ('infos/info_range_header');
        	$TableTPL             = parent::$page->get_template ('infos/info_range_body');
        }
        elseif ($this->_element_id ==  43)
        {
            $GateTPL              = parent::$page->get_template ('infos/info_gate_table');

            if ( $_POST )
            {
	            Functions_Lib::message ( $this->DoFleetJump() , "game.php?page=infos&gid=43" , 2 );
            }
        }
        elseif ( $this->_element_id ==  124 )
        {
            $PageTPL              = parent::$page->get_template ( 'infos/info_buildings_table' );
            $DestroyTPL           = parent::$page->get_template ( 'infos/info_buildings_destroy' );
            $TableHeadTPL         = parent::$page->get_template ( 'infos/info_astrophysics_header' );
            $TableTPL             = parent::$page->get_template ( 'infos/info_astrophysics_table' );
            $TableFooterTPL       = parent::$page->get_template ( 'infos/info_astrophysics_footer' );
        }
        elseif ($this->_element_id >= 202 && $this->_element_id <= 250)
        {
            $PageTPL              = parent::$page->get_template ('infos/info_buildings_fleet');
            $parse['element_typ'] = $this->_lang['tech'][200];
            $parse['rf_info_to']  = $this->ShowRapidFireTo();
            $parse['rf_info_fr']  = $this->ShowRapidFireFrom();
            $parse['hull_pt']     = Format_Lib::pretty_number ( $this->_pricelist[$this->_element_id]['metal'] + $this->_pricelist[$this->_element_id]['crystal'] );
            $parse['shield_pt']   = Format_Lib::pretty_number ( $this->_combat_caps[$this->_element_id]['shield'] );
            $parse['attack_pt']   = Format_Lib::pretty_number ( $this->_combat_caps[$this->_element_id]['attack'] );
            $parse['capacity_pt'] = Format_Lib::pretty_number ( $this->_pricelist[$this->_element_id]['capacity'] );
            $parse['base_speed']  = Format_Lib::pretty_number ( $this->_pricelist[$this->_element_id]['speed'] );
            $parse['base_conso']  = Format_Lib::pretty_number ( $this->_pricelist[$this->_element_id]['consumption'] );

            if ($this->_element_id == 202)
            {
                $parse['upd_speed']   = "<font color=\"yellow\">(". Format_Lib::pretty_number ($this->_pricelist[$this->_element_id]['speed2']) .")</font>";
                $parse['upd_conso']   = "<font color=\"yellow\">(". Format_Lib::pretty_number ($this->_pricelist[$this->_element_id]['consumption2']) .")</font>";
            }
            elseif ($this->_element_id == 211)
            {
	            $parse['upd_speed']   = "<font color=\"yellow\">(". Format_Lib::pretty_number ($this->_pricelist[$this->_element_id]['speed2']) .")</font>";
            }
        }
        elseif ( $this->_element_id >= 401 && $this->_element_id <= 550 )
        {
            $PageTPL              = parent::$page->get_template ( 'infos/info_buildings_defense' );
            $parse['element_typ'] = $this->_lang['tech'][400];

            if ( $this->_element_id < 500 )
            {
                $parse['rf_info_to']  = $this->ShowRapidFireTo();
                $parse['rf_info_fr']  = $this->ShowRapidFireFrom();
            }

            $parse['hull_pt']     = Format_Lib::pretty_number ( $this->_pricelist[$this->_element_id]['metal'] + $this->_pricelist[$this->_element_id]['crystal'] );
            $parse['shield_pt']   = Format_Lib::pretty_number ( $this->_combat_caps[$this->_element_id]['shield'] );
            $parse['attack_pt']   = Format_Lib::pretty_number ( $this->_combat_caps[$this->_element_id]['attack'] );
        }

		if ( $TableHeadTPL != '' )
		{
			$parse['table_head']  		= parent::$page->parse_template  ( $TableHeadTPL , $this->_lang );

			if ( $this->_element_id >= 22 && $this->_element_id <= 24 )
			{
				$parse['table_data']  	= $this->storage_table ( $TableTPL );
			}
			elseif ( $this->_element_id == 124 )
			{
				$parse['table_data']	= $this->astrophysics_table ( $TableTPL );
			}
			else
			{
				$parse['table_data']  	= $this->ShowProductionTable ( $TableTPL );
			}
		}

		if ( $TableFooterTPL != '' )
		{
			$parse['table_footer']  	= parent::$page->parse_template  ( $TableFooterTPL , $this->_lang );
		}

        $page  = parent::$page->parse_template ( $PageTPL , $parse );

        if ( $GateTPL != '' )
        {
            if ( $this->_current_planet[$this->_resource[$this->_element_id]] > 0 )
            {
                $RestString               = $this->GetNextJumpWaitTime ( $this->_current_planet );
                $parse['gate_start_link'] = $this->planet_link ( $this->_current_planet );
                if ($RestString['value'] != 0)
                {
                    $parse['gate_time_script'] = Functions_Lib::chrono_applet ( "Gate", "1", $RestString['value'], TRUE );
                    $parse['gate_wait_time']   = "<div id=\"bxx". "Gate" . "1" ."\"></div>";
                    $parse['gate_script_go']   = Functions_Lib::chrono_applet ( "Gate", "1", $RestString['value'], FALSE );
                }
                else
                {
                    $parse['gate_time_script'] = "";
                    $parse['gate_wait_time']   = "";
                    $parse['gate_script_go']   = "";
                }
                $parse['gate_dest_moons'] = $this->BuildJumpableMoonCombo ($this->_current_user, $this->_current_planet);
                $parse['gate_fleet_rows'] = $this->BuildFleetListRows ($this->_current_planet);
                $page .= parent::$page->parse_template ($GateTPL, $parse);
            }
        }

        if ( $DestroyTPL != '' )
        {
            if ( isset ( $this->_current_planet[$this->_resource[$this->_element_id]] ) && $this->_current_planet[$this->_resource[$this->_element_id]] > 0 )
            {
                $NeededRessources		= Developments_Lib::development_price ( $this->_current_user , $this->_current_planet , $this->_element_id , TRUE , TRUE );
                $DestroyTime          	= Developments_Lib::development_time  ( $this->_current_user , $this->_current_planet , $this->_element_id ) / 2;
                $parse['destroyurl']  	= "game.php?page=" . Developments_Lib::set_building_page ( $this->_element_id ) . "&cmd=destroy&building=" . $this->_element_id;
                $parse['levelvalue']  	= $this->_current_planet[$this->_resource[$this->_element_id]];
                $parse['nfo_metal']   	= $this->_lang['Metal'];
                $parse['nfo_crysta']  	= $this->_lang['Crystal'];
                $parse['nfo_deuter']  	= $this->_lang['Deuterium'];
                $parse['metal']       	= Format_Lib::pretty_number ( $NeededRessources['metal'] );
                $parse['crystal']     	= Format_Lib::pretty_number ( $NeededRessources['crystal'] );
                $parse['deuterium']   	= Format_Lib::pretty_number ( $NeededRessources['deuterium'] );
                $parse['destroytime'] 	= Format_Lib::pretty_time   ( $DestroyTime );

                $page .= parent::$page->parse_template ( $DestroyTPL , $parse );
            }
        }
        parent::$page->display ( $page );
    }

	/**
	 * method storage_table
	 * param
	 * return builds the storage table
	 */
    private function storage_table ( $template )
    {
		$CurrentBuildtLvl	= $this->_current_planet[$this->_resource[$this->_element_id]];
		$BuildStartLvl    	= max ( 1 , $CurrentBuildtLvl - 2 );
		$Table     			= "";
		$ProdFirst 			= 0;
		$ActualProd 		= Production_Lib::max_storable ( $CurrentBuildtLvl );

		for ( $BuildLevel = $BuildStartLvl ; $BuildLevel < $BuildStartLvl + 15 ; ++$BuildLevel )
		{
			$Prod	= Production_Lib::max_storable ( $BuildLevel );

			$bloc['build_lvl']			= ( $CurrentBuildtLvl == $BuildLevel ) ? "<font color=\"#ff0000\">" . $BuildLevel . "</font>" : $BuildLevel;
			$bloc['build_prod']      	= Format_Lib::pretty_number ( $Prod );
			$bloc['build_prod_diff'] 	= Format_Lib::color_number ( Format_Lib::pretty_number ( ( $Prod - $ActualProd ) ) );

			if ( $ProdFirst == 0 )
			{
				$ProdFirst = floor ( $Prod );
			}

			$Table .= parent::$page->parse_template ( $template , $bloc );
		}

		return $Table;
    }

	/**
	 * method astrophysics_table
	 * param
	 * return builds the astrophysics table
	 */
    private function astrophysics_table ( $template )
    {
		$CurrentBuildtLvl	= $this->_current_user[$this->_resource[$this->_element_id]];
		$BuildStartLvl    	= max ( 1 , $CurrentBuildtLvl - 2 );
		$Table     			= "";

		for ( $BuildLevel = $BuildStartLvl ; $BuildLevel < $BuildStartLvl + 15 ; ++$BuildLevel )
		{
			$bloc['tech_lvl']			= ( $CurrentBuildtLvl == $BuildLevel ) ? "<font color=\"#ff0000\">" . $BuildLevel . "</font>" : $BuildLevel;
			$bloc['tech_colonies']      = Format_Lib::pretty_number ( Fleets_Lib::get_max_colonies ( $BuildLevel ) );
			$bloc['tech_expeditions']	= Format_Lib::pretty_number ( Fleets_Lib::get_max_expeditions ( $BuildLevel ) );

			$Table .= parent::$page->parse_template ( $template , $bloc );
		}

		return $Table;
    }

    private function GetNextJumpWaitTime ( $CurMoon )
    {
        $JumpGateLevel  = $CurMoon[$this->_resource[43]];
        $LastJumpTime   = $CurMoon['planet_last_jump_time'];
        if ($JumpGateLevel > 0)
        {
            $WaitBetweenJmp = (60 * 60) * (1 / $JumpGateLevel);
            $NextJumpTime   = $LastJumpTime + $WaitBetweenJmp;
            if ($NextJumpTime >= time())
            {
                $RestWait   = $NextJumpTime - time();
                $RestString = " ". Format_Lib::pretty_time($RestWait);
            }
            else
            {
                $RestWait   = 0;
                $RestString = "";
            }
        }
        else
        {
            $RestWait   = 0;
            $RestString = "";
        }
        $RetValue['string'] = $RestString;
        $RetValue['value']  = $RestWait;

        return $RetValue;
    }

    private function DoFleetJump()
    {
        if ($_POST)
        {
            $RestString   = $this->GetNextJumpWaitTime();
            $NextJumpTime = $RestString['value'];
            $JumpTime     = time();

            if ( $NextJumpTime == 0 )
            {
                $TargetPlanet = $_POST['jmpto'];
                $TargetGate   = parent::$db->query_fetch ( "SELECT p.`planet_id`, b.`building_jump_gate`, p.`planet_last_jump_time`
                												FROM `" . PLANETS . "` AS p
                												INNER JOIN `" . BUILDINGS . "` AS b ON b.`building_planet_id` = p.`planet_id`
                												WHERE p.`planet_id` = '". $TargetPlanet ."';");

                if ($TargetGate['sprungtor'] > 0)
                {
                    $RestString   = $this->GetNextJumpWaitTime ( $TargetGate );
                    $NextDestTime = $RestString['value'];

                    if ( $NextDestTime == 0 )
                    {
                        $ShipArray   = array();
                        $SubQueryOri = "";
                        $SubQueryDes = "";

                        for ( $Ship = 200; $Ship < 300; $Ship++ )
                        {
                            $ShipLabel = "c". $Ship;
                            $gemi_kontrol    =    $_POST[ $ShipLabel ];

                            if (is_numeric($gemi_kontrol))
                            {
                                if ( $gemi_kontrol > $this->_current_planet[ $this->_resource[ $Ship ] ])
                                {
                                    $ShipArray[ $Ship ] = $this->_current_planet[ $this->_resource[ $Ship ] ];
                                }
                                else
                                {
                                    $ShipArray[ $Ship ] = $gemi_kontrol;
                                }


                                if ($ShipArray[ $Ship ] > 0)
                                {
                                    $SubQueryOri .= "`". $this->_resource[ $Ship ] ."` = `". $this->_resource[ $Ship ] ."` - '". $ShipArray[ $Ship ] ."', ";
                                    $SubQueryDes .= "`". $this->_resource[ $Ship ] ."` = `". $this->_resource[ $Ship ] ."` + '". $ShipArray[ $Ship ] ."', ";
                                }
                            }
                        }
                        if ($SubQueryOri != "")
                        {
                            parent::$db->query ( "UPDATE " . PLANETS . " SET
                            						$SubQueryOri
                            						`planet_last_jump_time` = '". $JumpTime ."'
                            						WHERE `planet_id` = '". $this->_current_planet['planet_id'] ."';" );

                            parent::$db->query ( "UPDATE " . PLANETS . " SET
                            						$SubQueryDes
                            						`planet_last_jump_time` = '". $JumpTime ."
                            						WHERE `planet_id` = '". $TargetGate['id'] ."';" );

                            parent::$db->query ( "UPDATE " . USERS . " SET
                            						`user_current_planet` = '". $TargetGate['id'] ."'
                            						WHERE `user_id` = '". $this->_current_user['user_id'] ."'" );

                            $this->_current_planet['planet_last_jump_time'] = $JumpTime;
                            $RestString    = $this->GetNextJumpWaitTime ( $this->_current_planet );
                            $RetMessage    = $this->_lang['in_jump_gate_done'] . $RestString['string'];
                        }
                        else
                        {
                            $RetMessage = $this->_lang['in_jump_gate_error_data'];
                        }
                    }
                    else
                    {
                        $RetMessage = $this->_lang['in_jump_gate_not_ready_target'] . $RestString['string'];
                    }
                }
                else
                {
                    $RetMessage = $this->_lang['in_jump_gate_doesnt_have_one'];
                }
            }
            else
            {
                $RetMessage = $this->_lang['in_jump_gate_already_used'] . $RestString['string'];
            }
        }
        else
        {
            $RetMessage = $this->_lang['in_jump_gate_error_data'];
        }

        return $RetMessage;
    }

    private function BuildFleetListRows()
    {
        $RowsTPL  = parent::$page->get_template ('infos/info_gate_rows');
        $CurrIdx  = 1;
        $Result   = "";
        for ($Ship = 200; $Ship < 250; $Ship++ )
        {
            if ($this->_resource[$Ship] != "")
            {
                if ($this->_current_planet[$this->_resource[$Ship]] > 0)
                {
                    $bloc['idx']             = $CurrIdx;
                    $bloc['fleet_id']        = $Ship;
                    $bloc['fleet_name']      = $this->_lang['tech'][$Ship];
                    $bloc['fleet_max']       = Format_Lib::pretty_number ( $this->_current_planet[$this->_resource[$Ship]] );
                    $bloc['gate_ship_dispo'] = $this->_lang['in_jump_gate_available'];
                    $Result                 .= parent::$page->parse_template ( $RowsTPL, $bloc );
                    $CurrIdx++;
                }
            }
        }
        return $Result;
    }

    private function BuildJumpableMoonCombo()
    {
        $MoonList        = parent::$db->query ( "SELECT *
        											FROM " . PLANETS . "
        											WHERE `planet_type` = '3' AND
        													`planet_user_id` = '" . $this->_current_user['user_id'] . "';" );
        $Combo           = "";

        while ( $CurMoon = parent::$db->fetch_assoc ( $MoonList ) )
        {
            if ( $CurMoon['planet_id'] != $this->_current_planet['planet_id'] )
            {
                $RestString = $this->GetNextJumpWaitTime ( $CurMoon );
                if ($CurMoon[$this->_resource[43]] >= 1)
                    $Combo .= "<option value=\"". $CurMoon['planet_id'] ."\">[". $CurMoon['planet_galaxy'] .":". $CurMoon['planet_system'] .":". $CurMoon['planet_planet'] ."] ". $CurMoon['planet_name'] . $RestString['string'] ."</option>\n";
            }
        }
        return $Combo;
    }

    private function ShowProductionTable ( $Template )
    {
        $BuildLevelFactor 	= $this->_current_planet[ 'planet_' . $this->_resource[$this->_element_id] . '_porcent' ];
        $BuildTemp        	= $this->_current_planet[ 'planet_temp_max' ];
        $CurrentBuildtLvl 	= $this->_current_planet[ $this->_resource[$this->_element_id] ];
        $BuildLevel       	= ($CurrentBuildtLvl > 0) ? $CurrentBuildtLvl : 1;
        $BuildEnergy        = $this->_current_user['research_energy_technology'];

		// BOOST
		$geologe_boost		= 1 + ( 1 * ( Officiers_Lib::is_officier_active ( $this->_current_user['premium_officier_geologist'] ) ? GEOLOGUE : 0 ) );
		$engineer_boost		= 1 + ( 1 * ( Officiers_Lib::is_officier_active ( $this->_current_user['premium_officier_engineer'] ) ? ENGINEER_ENERGY : 0 ) );

		// PRODUCTION FORMULAS
		$metal_prod			= eval ( $this->_prod_grid[$this->_element_id]['formule']['metal'] );
		$crystal_prod		= eval ( $this->_prod_grid[$this->_element_id]['formule']['crystal'] );
		$deuterium_prod		= eval ( $this->_prod_grid[$this->_element_id]['formule']['deuterium'] );
		$energy_prod		= eval ( $this->_prod_grid[$this->_element_id]['formule']['energy'] );

		// PRODUCTION
		$Prod[1]			= Production_Lib::production_amount ( $metal_prod , $geologe_boost );
		$Prod[2]			= Production_Lib::production_amount ( $crystal_prod , $geologe_boost );
		$Prod[3]			= Production_Lib::production_amount ( $deuterium_prod , $geologe_boost );

		if( $this->_element_id >= 4 )
		{
			$Prod[4]		= Production_Lib::production_amount ( $energy_prod , $engineer_boost , TRUE );
			$ActualProd    	= floor ( $Prod[4] );
		}
		else
		{
			$Prod[4]		= Production_Lib::production_amount ( $energy_prod , 1 , TRUE );
			$ActualProd    	= floor ( $Prod[$this->_element_id] );
		}

        if ( $this->_element_id != 12 )
        {
        	$ActualNeed     = floor ( $Prod[4] );
        }
        else
        {
        	$ActualNeed		= floor ( $Prod[3] );
        }

        $BuildStartLvl    = $CurrentBuildtLvl - 2;
        if ($BuildStartLvl < 1)
            $BuildStartLvl = 1;

        $Table     = "";
        $ProdFirst = 0;

        for ( $BuildLevel = $BuildStartLvl; $BuildLevel < $BuildStartLvl + 15; $BuildLevel++ )
        {
            if ( $this->_element_id != 42 )
            {
				// PRODUCTION FORMULAS
				$metal_prod			= eval ( $this->_prod_grid[$this->_element_id]['formule']['metal'] );
				$crystal_prod		= eval ( $this->_prod_grid[$this->_element_id]['formule']['crystal'] );
				$deuterium_prod		= eval ( $this->_prod_grid[$this->_element_id]['formule']['deuterium'] );
				$energy_prod		= eval ( $this->_prod_grid[$this->_element_id]['formule']['energy'] );

				// PRODUCTION
				$Prod[1]			= Production_Lib::production_amount ( $metal_prod , $geologe_boost );
				$Prod[2]			= Production_Lib::production_amount ( $crystal_prod , $geologe_boost );
				$Prod[3]			= Production_Lib::production_amount ( $deuterium_prod , $geologe_boost );

				if( $this->_element_id >= 4 )
				{
					$Prod[4]		= Production_Lib::production_amount ( $energy_prod , $engineer_boost , TRUE );
				}
				else
				{
					$Prod[4]		= Production_Lib::production_amount ( $energy_prod , 1 , TRUE );
				}

                $bloc['build_lvl']       = ($CurrentBuildtLvl == $BuildLevel) ? "<font color=\"#ff0000\">".$BuildLevel."</font>" : $BuildLevel;

                if ($ProdFirst > 0)
                    if ($this->_element_id != 12)
                        $bloc['build_gain']      = "<font color=\"lime\">(". Format_Lib::pretty_number(floor($Prod[$this->_element_id] - $ProdFirst)) .")</font>";
                    else
                        $bloc['build_gain']      = "<font color=\"lime\">(". Format_Lib::pretty_number(floor($Prod[4] - $ProdFirst)) .")</font>";
                else
                    $bloc['build_gain']      = "";

                if ($this->_element_id != 12)
                {
                    $bloc['build_prod']      = Format_Lib::pretty_number(floor($Prod[$this->_element_id]));
                    $bloc['build_prod_diff'] = Format_Lib::color_number( Format_Lib::pretty_number(floor($Prod[$this->_element_id] - $ActualProd)) );
                    $bloc['build_need']      = Format_Lib::color_number( Format_Lib::pretty_number(floor($Prod[4])) );
                    $bloc['build_need_diff'] = Format_Lib::color_number( Format_Lib::pretty_number(floor($Prod[4] - $ActualNeed)) );
                }
                else
                {
                    $bloc['build_prod']      = Format_Lib::pretty_number(floor($Prod[4]));
                    $bloc['build_prod_diff'] = Format_Lib::color_number( Format_Lib::pretty_number(floor($Prod[4] - $ActualProd)) );
                    $bloc['build_need']      = Format_Lib::color_number( Format_Lib::pretty_number(floor($Prod[3])) );
                    $bloc['build_need_diff'] = Format_Lib::color_number( Format_Lib::pretty_number(floor($Prod[3] - $ActualNeed)) );
                }
                if ($ProdFirst == 0)
                {
                    if ($this->_element_id != 12)
                        $ProdFirst = floor($Prod[$this->_element_id]);
                    else
                        $ProdFirst = floor($Prod[4]);
                }
            }
            else
            {
                $bloc['build_lvl']       = ($CurrentBuildtLvl == $BuildLevel) ? "<font color=\"#ff0000\">".$BuildLevel."</font>" : $BuildLevel;
                $bloc['build_range']     = ($BuildLevel * $BuildLevel) - 1;
            }
            $Table    .= parent::$page->parse_template ($Template, $bloc);
        }

        return $Table;
    }

    private function ShowRapidFireTo()
    {
        $ResultString = "";
        for ($Type = 200; $Type < 500; $Type++)
        {
            if ($this->_combat_caps[$this->_element_id]['sd'][$Type] > 1)
                $ResultString .= $this->_lang['in_rf_again']. " ". $this->_lang['tech'][$Type] ." <font color=\"#00ff00\">".$this->_combat_caps[$this->_element_id]['sd'][$Type]."</font><br>";
        }
        return $ResultString;
    }

    private function ShowRapidFireFrom()
    {
        $ResultString = "";
        for ($Type = 200; $Type < 500; $Type++)
        {
            if ($this->_combat_caps[$Type]['sd'][$this->_element_id] > 1)
                $ResultString .= $this->_lang['in_rf_from']. " ". $this->_lang['tech'][$Type] ." <font color=\"#ff0000\">".$this->_combat_caps[$Type]['sd'][$this->_element_id]."</font><br>";
        }
        return $ResultString;
    }

	private function planet_link ( $current_planet )
	{
		return "<a href=\"game.php?page=galaxy&mode=3&galaxy=".$current_planet['planet_galaxy']."&system=".$current_planet['planet_system']."\">[".$current_planet['planet_galaxy'].":".$current_planet['planet_system'].":".$current_planet['planet_planet']."]</a>";
	}
}
/* end of infos.php */