<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Format_Lib
{
	/**
	 * method pretty_time
	 * param $seconds
	 * return the time with format
	 */
	public static function pretty_time ( $seconds )
	{
		$day	= floor ( $seconds / (24 * 3600 ) );
		$hs 	= floor ( $seconds / 3600 % 24 );
		$ms 	= floor ( $seconds / 60 % 60 );
		$sr 	= floor ( $seconds / 1 % 60 );

		if ( $hs < 10 ) { $hh = "0" . $hs; } else { $hh = $hs; }
		if ( $ms < 10 ) { $mm = "0" . $ms; } else { $mm = $ms; }
		if ( $sr < 10 ) { $ss = "0" . $sr; } else { $ss = $sr; }

		$time = '';
		if ( $day != 0 ) { $time .= $day . 'd '; }
		if ( $hs  != 0 ) { $time .= $hh . 'h ';  } else { $time .= '00h '; }
		if ( $ms  != 0 ) { $time .= $mm . 'm ';  } else { $time .= '00m '; }
		$time .= $ss . 's';

		return $time;
	}

	/**
	 * method pretty_time_hour
	 * param $seconds
	 * return the hour in minutes
	 */
	public static function pretty_time_hour ( $seconds )
	{
		$min 	= floor ( $seconds / 60 % 60 );
		$time 	= '';

		if ( $min != 0 )
		{
			$time .= $min . 'min ';
		}

		return $time;
	}

	/**
	 * method color_number
	 * param1 $n
	 * param2 $s
	 * return the color determined by: if the number is positive -> green else negative -> red
	 */
	public static function color_number ( $n , $s = '' )
	{
		if ( $n > 0 )
		{
			if ( $s != '' )
			{
				$s = self::color_green ( $s );
			}
			else
			{
				$s = self::color_green ( $n );
			}

		}
		elseif ( $n < 0 )
		{
			if ($s != '')
			{
				$s = self::color_red ( $s );
			}
			else
			{
				$s = self::color_red ( $n );
			}
		}
		else
		{
			if ( $s != '' )
			{
				$s = $s;
			}
			else
			{
				$s = $n;
			}
		}

		return $s;
	}

	/**
	 * method color_red
	 * param $n
	 * return a value in red color
	 */
	public static function color_red ( $n )
	{
		return '<font color="#ff0000">' . $n . '</font>';
	}

	/**
	 * method color_green
	 * param $n
	 * return a value in green color
	 */
	public static function color_green ( $n )
	{
		return '<font color="#00ff00">' . $n . '</font>';
	}

	/**
	 * method pretty_number
	 * param1 $n
	 * param2 $floor
	 * return a number with format
	 */
	public static function pretty_number ( $n , $floor = TRUE )
	{
		if ( $floor )
		{
			$n = floor ( $n );
		}

		return number_format ( $n , 0 , "," , "." );
	}

	/**
	 * method shortly_number
	 * param $number
	 * return a shortly number
	 */
	public static function shortly_number ( $number )
	{
		// MAS DEL TRILLON
		if ( $number >= 1000000000000000000000000 )
		{
			return self::pretty_number ( ( $number / 1000000000000000000 ) ) . "&nbsp;<font color=lime>T+</font>";
		}
		// TRILLON
		elseif ( $number >= 1000000000000000000 && $number < 1000000000000000000000000 )
		{
			return self::pretty_number ( ( $number / 1000000000000000000 ) ) . "&nbsp;<font color=lime>T</font>";
		}
		// BILLON
		elseif ( $number >= 1000000000000 && $number < 1000000000000000000 )
		{
			return self::pretty_number ( ( $number / 1000000000000 ) ) . "&nbsp;<font color=lime>B</font>";
		}
		// MILLON
		elseif ( $number >= 1000000 && $number < 1000000000000 )
		{
			return self::pretty_number ( ( $number / 1000000 ) ) . "&nbsp;<font color=lime>M</font>";
		}
		// MIL
		elseif ( $number >= 1000 && $number < 1000000 )
		{
			return self::pretty_number ( ( $number / 1000 ) ) . "&nbsp;<font color=lime>K</font>";
		}
		// NUMERO SIN DEFINIR
		else
		{
			return self::pretty_number ( $number );
		}
	}

	/**
	 * method float_to_string
	 * param1 $numeric
	 * param2 $pro
	 * param3 $output
	 * return a string
	 */
	public static function float_to_string ( $numeric , $pro = 0 , $output = FALSE )
	{
		return ( $output ) ? str_replace ( "," , "." , sprintf ( "%." . $pro . "f" , $numeric ) ) : sprintf ( "%." . $pro . "f" , $numeric );
	}

	/**
	 * method round_up
	 * param1 $value
	 * param2 $precision
	 * return a rounded up number
	 */
	public static function round_up ( $value , $precision = 0 )
	{
		if ( $precision == 0 )
		{
			$precisionFactor = 1;
		}
		else
		{
			$precisionFactor = pow ( 10, $precision );
		}

		return ceil ( $value * $precisionFactor ) / $precisionFactor;
	}

	/**
	 * method pretty_coords
	 * param $galaxy
	 * param $system
	 * param $planet
	 * return a string
	 */
	public static function pretty_coords ( $galaxy , $system , $planet )
	{
		return "[$galaxy:$system:$planet]";
	}

	/**
	 * method strong_text
	 * param $value
	 * return a bold string
	 */
	public static function strong_text ( $value )
	{
		return '<strong>' . $value . '</strong>';
	}
}
/* end of Format_Lib.php */