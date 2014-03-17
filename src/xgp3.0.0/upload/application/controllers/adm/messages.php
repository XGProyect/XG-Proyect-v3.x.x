<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Messages extends XGPCore
{
	private $_lang;
	private $_alert;
	private $_current_user;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();

		// check if session is active
		parent::$users->check_session();

		$this->_lang			= parent::$lang;
		$this->_current_user	= parent::$users->get_user_data();

		// Check if the user is allowed to access
		if ( Administration_Lib::have_access ( $this->_current_user['user_authlevel'] ) && Administration_Lib::authorization ( $this->_current_user['user_authlevel'] , 'observation' ) == 1 )
		{
			$this->build_page();
		}
		else
		{
			die ( Functions_Lib::message ( $this->_lang['ge_no_permissions'] ) );
		}
	}

	/**
	 * method __destruct
	 * param
	 * return close db connection
	 */
	public function __destruct ()
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
		$parse					= $this->_lang;
		$parse['days_options']	= $this->days_combo(); // days combo with pre selected current day
		$parse['months_options']= $this->months_combo(); // months combo with pre selected current month
		$parse['years_options']	= $this->years_combo(); // years combo with pre selected current year

		if ( $_POST && isset ( $_POST['search'] ) )
		{
			$parse['results']	= $this->do_search();
		}

		if ( $_POST && isset ( $_POST['delete'] ) )
		{
			$this->delete_messages();

			$parse['results']	= '';
		}

		$parse['alert']			= $this->_alert != '' ? $this->_alert : '';

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'adm/messages_view' ) , $parse ) );
	}

	/**
	 * method do_search
	 * param
	 * return do the search
	 */
	private function do_search()
	{
		// build the query, run the query and return the result
		$search_result	= parent::$db->query ( $this->build_search_query() );
		$template		= parent::$page->get_template ( 'adm/messages_row_view' );
		$results		= '';

		if ( $search_result !== FALSE )
		{
			// loop thru the results
			while ( $search_data = parent::$db->fetch_array ( $search_result ) )
			{
				$search_data['mg_show_hide']	= $this->_lang['mg_show_hide'];
				$search_data['message_time']	= date ( Functions_Lib::read_config ( 'date_format_extended' ) , $search_data['message_time'] );
				$search_data['message_text']	= stripslashes( nl2br( $search_data['message_text'] ) ) ;

				$results	.= parent::$page->parse_template ( $template , $search_data );
			}

			// return search results with table format of course
			return $results;
		}

		$this->_alert	= Administration_Lib::save_message ( 'warning' , $this->_lang['mg_no_results'] );
	}

	/**
	 * method delete_messages
	 * param
	 * return delete messages
	 */
	private function delete_messages()
	{
		$ids_array	= '';

		// build the ID's list to delete, we're going to delete them all in one single query
		foreach ( $_POST['delete_message'] as $message_id => $delete_status )
		{
			if ( $delete_status == 'on' && $message_id > 0 && is_numeric ( $message_id ) )
			{
				$ids_array	.= $message_id . ',';
			}
		}

		// delete messages
		parent::$db->query ( "DELETE FROM `" . MESSAGES . "`
								WHERE `message_id` IN (" . rtrim ( $ids_array , ',') . ")" );

		// show alert
		$this->_alert	= Administration_Lib::save_message ( 'ok' , $this->_lang['mg_delete_ok'] );
	}

	/**
	 * method build_search_query
	 * param
	 * return build the search query
	 */
	private function build_search_query ()
	{
		// search by message id
		if ( isset ( $_POST['message_id'] ) && ! empty ( $_POST['message_id'] ) )
		{
			$message_id	= (int)$_POST['message_id'];

			if ( $message_id > 0 )
			{
				$query_search['message_id']			= "(`message_id` = '" . $message_id . "')";
			}
		}

		// search by username or user id
		if ( isset ( $_POST['message_user'] ) && ! empty ( $_POST['message_user'] ) )
		{
			$message_user	= $_POST['message_user'];

			if ( is_numeric ( $message_user ) )
			{
				$message_user	= (int)$message_user;

				if ( $message_user > 0 )
				{
					$query_search['message_user']	= "(`message_sender` = '" . $message_user . "' OR `message_receiver` = '" . $message_user . "')";
				}
			}
			elseif ( is_string ( $message_user ) )
			{
				$query_search['message_user']		= "(`message_sender` = (SELECT `user_id` FROM `" . USERS. "` WHERE `user_name` = '" . $message_user . "' LIMIT 1) OR `message_receiver` = (SELECT `user_id` FROM `" . USERS. "` WHERE `user_name` = '" . $message_user . "' LIMIT 1))";
			}
		}

		// search by message subject/planets coords/planet name
		if ( isset ( $_POST['message_subject'] ) && ! empty ( $_POST['message_subject'] ) )
		{
			$query_search['message_subject']		= "(`message_subject` = '" . $_POST['message_subject'] . "')";
		}

		// search by date, also we validate here
		if ( isset ( $_POST['message_day'] ) && isset ( $_POST['message_month'] ) && isset ( $_POST['message_year'] ) )
		{
			if ( checkdate ( $_POST['message_month'] , $_POST['message_day'] , $_POST['message_year'] ) )
			{
				$current_time	= $_POST['message_day'] . '-' . $_POST['message_month'] . '-' . $_POST['message_year'];
				$current_time 	= strtotime ( $current_time );

				$query_search['message_time']		= "(`message_time` >= '" . $current_time . "' AND `message_time` <= '" . $current_time . "')";
			}
		}

		// search by message type
		if ( isset ( $_POST['message_type'] ) && ! empty ( $_POST['message_type'] ) )
		{
			$message_type	= (int)$_POST['message_type'];

			if ( $message_type > 0 )
			{
				$query_search['message_type']		= "(`message_type` = '" . $message_type . "')";
			}
		}

		// search by message text
		if ( isset ( $_POST['message_text'] ) && ! empty ( $_POST['message_text'] ) )
		{
			$message_text	= (string)$_POST['message_text'];

			$query_search['message_text']			= "(`message_text` LIKE '%" . $message_text . "%')";
		}

		if ( isset ( $query_search ) )
		{
			$search_query_string		= "SELECT m.*, u1.`user_name` AS sender, u2.`user_name` AS receiver
											FROM `" . MESSAGES . "` AS m
											LEFT JOIN `" . USERS . "` as u1 ON u1.`user_id` = m.`message_sender`
											LEFT JOIN `" . USERS . "` as u2 ON u2.`user_id` = m.`message_receiver`
											WHERE ";

			foreach ( $query_search as $what => $content )
			{
				$search_query_string	.= $content . ' AND ';
			}

			$search_query_string	= rtrim ( $search_query_string , ' AND ') . ';';

			return $search_query_string;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * method days_combo
	 * param
	 * return the set of options for the days combo box
	 */
	private function days_combo()
	{
		$days_combo	= '';

		for ( $day = 1 ; $day <= 31 ; $day++ )
		{
			$days_combo	.= '<option value="' . $day . '">' . $day . '</option>';
		}

		return $days_combo;
	}

	/**
	 * method months_combo
	 * param
	 * return the set of options for the month combo box
	 */
	private function months_combo()
	{
		$month_combo	= '';

		for ( $month = 1 ; $month <= 12 ; $month++ )
		{
			$month_combo	.= '<option value="' . $month . '">' . $month . '</option>';
		}

		return $month_combo;
	}

	/**
	 * method years_combo
	 * param
	 * return the set of options for the years combo box
	 */
	private function years_combo()
	{
		$year_combo	= '';

		for ( $year = date ( 'Y' ) ; $year >= 2008 ; $year-- ) // 2008 the year XG Proyect started :)
		{
			$year_combo	.= '<option value="' . $year . '">' . $year . '</option>';
		}

		return $year_combo;
	}
}
/* end of messages.php */