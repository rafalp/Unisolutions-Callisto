<?

/*
#===========================================================================
|
|	Unisolutions Callisto
|
|	by Rafał Pitoń
|	Copyright 2007 by Unisolutions
|	http://www.unisolutions.pl
|
#===========================================================================
|
|	This software is released under GNU General Public License v3
|	http://www.gnu.org/licenses/gpl.txt
|
#===========================================================================
|
|	Time Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class site_time{

	/**
	 * actual server timezone
	 *
	 * @var int
	 */
	
	public $system_timezone;
	
	/**
	 * server time correction
	 *
	 * @var int	
	 */
	
	public $time_correction;
	
	/**
	 * check, if we has DST
	 *
	 * @var bool
	 */
	
	public $time_dst;
	
	/**
	 * full date format
	 *
	 * @var string
	 */
	
	public $format_full;
	
	/**
	 * hour format
	 *
	 * @var string
	 */
	
	public $format_hour;
	
	/**
	 * hour format
	 *
	 * @var relative format
	 */
	
	public $format_relative;	
	
	function __construct(){
		
		global $settings;
		global $session;
		
		/**
		 * set default time zone
		 */
		
		date_default_timezone_set( 'UTC');
		
		if( $session -> user['user_id'] != -1){		
			$this -> system_timezone = $session -> user['user_time_zone'];
		}else{
			$this -> system_timezone = $settings['time_timezone'];
		}
		
		$this -> time_correction = $settings['system_time_adjustment'];
		
		/**
		 * check if we has DST
		 */
		
		if( $session -> user['user_id'] == -1){
			
			$this -> time_dst = $settings['time_dst'];
			
		}else{
			
			$this -> time_dst = $session -> user['user_dst'];
			
		}
		
		/**
		 * set time formats
		 */
		
		$this -> format_full = $settings['time_fulldate_format'];
		$this -> format_hour = $settings['time_hour_format'];
		$this -> format_relative = $settings['relative_hour_format'];
		
	}
	
	/**
	 * correscts time
	 *
	 * @param int $timestamp
	 * @return int
	 */
	
	function getTime( $timestamp = null){
		
		global $settings;
		
		if ( $timestamp == null)
			$timestamp = time();
		
		/**
		 * check if we have dst
		 */
		
		if( $this -> time_dst){
			
			$time_dst = date( "I", $timestamp);
			
			if ( !$time_dst)			
				$timestamp += 3600;
		}
		
		$timestamp += ( 3600 * $this -> system_timezone) + $this -> time_correction;
		
		return	$timestamp;
		
	}
	
	/**
	 * draws hour
	 *
	 * @param int $timestamp
	 * @return string
	 */
	
	function drawDate( $timestamp, $relative = true){
		
		global $settings;
		global $language;
		
		/**
		 * correct time first
		 */
		
		$timestamp += ( 3600 * $this -> system_timezone) + $this -> time_correction;
		
		/**
		 * check if we have dst
		 */
		
		if( $this -> time_dst){
			
			$time_dst = date( "I", $timestamp);
			
			if ( !$time_dst)			
				$timestamp += 3600;
		}
				
		/**
		 * do rest
		 */
		
		if( $settings['time_relative'] && $relative){
			
			$actual_time = time();
			
			$actual_time += ( 3600 * $this -> system_timezone) + $this -> time_correction;
			
			if( $this -> time_dst){
				
				$time_dst = date( "I", $actual_time);
				
				if ( !$time_dst)			
					$actual_time += 3600;
			}
			
						
			/**
			 * get days
			 */
			
			$day_today = date( 'z', $actual_time);
			$year_today = date( 'Y', $actual_time);
			
			$day = date( 'z', $timestamp);
			$year = date( 'Y', $timestamp);
						
			if( $year == $year_today){
				
				if( $day == $day_today){
					
					/**
					 * date is today
					 */
					
					$result = date( $this -> format_relative, $timestamp);
					
					$result = str_ireplace( '(---)', $language -> getString( 'time_today'), $result);
					
				}else if( $day_today - 1 == $day){
					
					/**
					 * date is yesterday
					 */
					
					$result = date( $this -> format_relative, $timestamp);
					
					$result = str_ireplace( '(---)', $language -> getString( 'time_yesterday'), $result);
					
				}else if( $day_today + 1 == $day){
					
					/**
					 * date is tomorrow
					 */
					
					$result = date( $this -> format_relative, $timestamp);
					
					$result = str_ireplace( '(---)', $language -> getString( 'time_tomorrow'), $result);
					
				}else{
					
					$result = date( $this -> format_full, $timestamp);
					
				}
				
				
			}else{
				
				if( $year < $year_today && $day_today == 0){
					
					if( ($year % 4 == 0 && $day == 365) || ($year % 4 != 0 &&  $day == 364)){
						
						/**
						 * date is yesterday
						 */
						
						$result = date( $this -> format_relative, $timestamp);
					
						$result = str_ireplace( '(---)', $language -> getString( 'time_today'), $result);
						
					}else{
						
						$result = date( $this -> format_full, $timestamp);
						
					}
					
				}else{
					
					if( ($year_today % 4 == 0 && $day_today == 365) || ($year_today % 4 != 0 &&  $day_today == 364) && $day == 0){
						
						/**
						 * date is tomorrow
						 */
						
						$result = date( $this -> format_relative, $timestamp);
						
						$result = str_ireplace( '(---)', $language -> getString( 'time_tomorrow'), $result);
						
					}else{
						
						$result = date( $this -> format_full, $timestamp);
						
					}
					
				}
			
			}
		
		}else{
			
			$result = date( $this -> format_full, $timestamp);
		
		}
		
		$result = $this -> translateDate($result);
		return $result;
	}
	
	/**
	 * draws date
	 *
	 * @param int $timestamp
	 * @return string
	 */
	
	function drawHour( $timestamp){
		
		/**
		 * correct time first
		 */
		
		$timestamp += ( 3600 * $this -> system_timezone) + $this -> time_correction;
		
		/**
		 * check if we have dst
		 */
		
		if( $this -> time_dst){
			
			$time_dst = date( "I", $timestamp);
			
			if ( !$time_dst)			
				$timestamp += 3600;
		}
				
		/**
		 * do rest
		 */
		
		$result = date( $this -> format_hour, $timestamp);
		$result = $this -> translateDate($result);
		
		return $result;
	}
	
	/**
	 * reurns an array cointaining timezones
	 *
	 */
	
	function getTimeZones(){
		
		global $language;
		
		$timezone['-12'] = $language -> getString( 'time_zone_m12');
		$timezone['-11'] = $language -> getString( 'time_zone_m11');
		$timezone['-10'] = $language -> getString( 'time_zone_m10');
		$timezone['-9.5'] = $language -> getString( 'time_zone_m9:30');
		$timezone['-9'] = $language -> getString( 'time_zone_m9');
		$timezone['-8'] = $language -> getString( 'time_zone_m8');
		$timezone['-7'] = $language -> getString( 'time_zone_m7');
		$timezone['-6'] = $language -> getString( 'time_zone_m6');
		$timezone['-5'] = $language -> getString( 'time_zone_m5');
		$timezone['-4'] = $language -> getString( 'time_zone_m4');
		$timezone['-3.5'] = $language -> getString( 'time_zone_m3:30');
		$timezone['-3'] = $language -> getString( 'time_zone_m3');
		$timezone['-2'] = $language -> getString( 'time_zone_m2');
		$timezone['-1'] = $language -> getString( 'time_zone_m1');
		$timezone['0'] = $language -> getString( 'time_zone_0');
		$timezone['1'] = $language -> getString( 'time_zone_1');
		$timezone['2'] = $language -> getString( 'time_zone_2');
		$timezone['3'] = $language -> getString( 'time_zone_3');
		$timezone['3.5'] = $language -> getString( 'time_zone_3:30');
		$timezone['4'] = $language -> getString( 'time_zone_4');
		$timezone['4.5'] = $language -> getString( 'time_zone_4:30');
		$timezone['5'] = $language -> getString( 'time_zone_5');
		$timezone['5.5'] = $language -> getString( 'time_zone_5:30');
		$timezone['5.75'] = $language -> getString( 'time_zone_5:45');
		$timezone['6'] = $language -> getString( 'time_zone_6');
		$timezone['6.5'] = $language -> getString( 'time_zone_6:30');
		$timezone['7'] = $language -> getString( 'time_zone_7');
		$timezone['8'] = $language -> getString( 'time_zone_8');
		$timezone['8.75'] = $language -> getString( 'time_zone_8:45');
		$timezone['9'] = $language -> getString( 'time_zone_9');
		$timezone['9.5'] = $language -> getString( 'time_zone_9:30');
		$timezone['10'] = $language -> getString( 'time_zone_10');
		$timezone['10.5'] = $language -> getString( 'time_zone_10:30');
		$timezone['11'] = $language -> getString( 'time_zone_11');
		$timezone['11.5'] = $language -> getString( 'time_zone_11:30');
		$timezone['12'] = $language -> getString( 'time_zone_12');
		$timezone['12.75'] = $language -> getString( 'time_zone_12:45');
		$timezone['13'] = $language -> getString( 'time_zone_13');
		$timezone['14'] = $language -> getString( 'time_zone_14');
		
		return $timezone;
	}
	
	function timeAgo( $time){
			
		global $language;
		
		$time = time() - $time;
	
	 	if( $time > 60){
	 		
	 		$time = floor($time/60);	
	 		return $time.' '.$language -> getString( 'time_minutes_ago');
	 		
	 	}else if($time > 3600){
	 		
	 		$time = round($time/3600);	
	 		return $time.' '.$language -> getString( 'time_hours_ago');
	 		
	 	}else{
	 		
	 		return $time.' '.$language -> getString( 'time_seconds_ago');
	 		
	 	}
		
	}
	
	/**
	 * gets full date from form, and converts it into timestamp
	 *
	 * @param string $form_name
	 * @return int
	 */
	
	function getFullDate( $form_name){
		
		$time_hour = $_POST[$form_name.'_hour'];
	 	$time_minute = $_POST[$form_name.'_minute'];
	 	$time_secound = $_POST[$form_name.'_secound'];
	 	
	 	$time_year = $_POST[$form_name.'_year'];
	 	$time_month = $_POST[$form_name.'_month'];
	 	$time_day = $_POST[$form_name.'_day'];
		
	 	$time = mktime( $time_hour, $time_minute, $time_secound, $time_month, $time_day, $time_year);
	 	
		return $time;
		
	}
	
	function getDate( $form_name){
			 	
	 	$time_year = $_POST[$form_name.'_year'];
	 	$time_month = $_POST[$form_name.'_month'];
	 	$time_day = $_POST[$form_name.'_day'];
		
	 	$time = mktime( 0, 0, 0, $time_month, $time_day, $time_year);
	 	
		return $time;
		
	}
		
	function getHour( $form_name){
			 	
	 	$time_hour = $_POST[$form_name.'_hour'];
	 	$time_minute = $_POST[$form_name.'_minute'];
	 	$time_secound = $_POST[$form_name.'_secound'];
		
	 	$time = mktime( $time_hour, $time_minute, $time_secound);
	 	
		return $time;
		
	}
	
	function translateDate( $date){
		
		global $language;
		
		$replacements['January'] 	= $language -> getString( 'time_month_january');
		$replacements['February'] 	= $language -> getString( 'time_month_february');
		$replacements['March'] 		= $language -> getString( 'time_month_march');
		$replacements['April'] 		= $language -> getString( 'time_month_april');
		$replacements['May'] 		= $language -> getString( 'time_month_may');
		$replacements['June'] 		= $language -> getString( 'time_month_june');
		$replacements['July'] 		= $language -> getString( 'time_month_july');
		$replacements['August'] 	= $language -> getString( 'time_month_august');
		$replacements['September'] 	= $language -> getString( 'time_month_september');
		$replacements['October'] 	= $language -> getString( 'time_month_october');
		$replacements['November'] 	= $language -> getString( 'time_month_november');
		$replacements['December'] 	= $language -> getString( 'time_month_december');
		
		$replacements['Jan'] 	= $language -> getString( 'time_month_january_short');
		$replacements['Feb'] 	= $language -> getString( 'time_month_february_short');
		$replacements['Mar'] 	= $language -> getString( 'time_month_march_short');
		$replacements['Apr'] 	= $language -> getString( 'time_month_april_short');
		$replacements['May'] 	= $language -> getString( 'time_month_may_short');
		$replacements['Jun'] 	= $language -> getString( 'time_month_june_short');
		$replacements['Jul'] 	= $language -> getString( 'time_month_july_short');
		$replacements['Aug'] 	= $language -> getString( 'time_month_august_short');
		$replacements['Sep'] 	= $language -> getString( 'time_month_september_short');
		$replacements['Oct'] 	= $language -> getString( 'time_month_october_short');
		$replacements['Nov'] 	= $language -> getString( 'time_month_november_short');
		$replacements['Dec'] 	= $language -> getString( 'time_month_december_short');
		
		$replacements['Monday'] 	= $language -> getString( 'time_day_monday');
		$replacements['Tuesday'] 	= $language -> getString( 'time_day_tuesday');
		$replacements['Wednesday'] 	= $language -> getString( 'time_day_wednesday');
		$replacements['Thursday'] 	= $language -> getString( 'time_day_thursday');
		$replacements['Friday'] 	= $language -> getString( 'time_day_friday');
		$replacements['Saturday'] 	= $language -> getString( 'time_day_saturday');
		$replacements['Sunday'] 	= $language -> getString( 'time_day_sunday');
		
		$replacements['Mon'] 	= $language -> getString( 'time_day_monday_short');
		$replacements['Tue'] 	= $language -> getString( 'time_day_tuesday_short');
		$replacements['Wed'] 	= $language -> getString( 'time_day_wednesday_short');
		$replacements['Thu'] 	= $language -> getString( 'time_day_thursday_short');
		$replacements['Fri'] 	= $language -> getString( 'time_day_friday_short');
		$replacements['Sat'] 	= $language -> getString( 'time_day_saturday_short');
		$replacements['Sun'] 	= $language -> getString( 'time_day_sunday_short');
		
		foreach ( $replacements as $eng => $user)
			$date = str_ireplace( $eng, $user, $date);
		
		return $date;
		
	}
}

?>