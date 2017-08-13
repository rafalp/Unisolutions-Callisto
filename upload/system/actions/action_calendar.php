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
|	Calendar
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class action_calendar extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * begin error checking
		 */
				
		if ( $settings['calendar_turn']){
			
			/**
			 * can we browse it?
			 */
			
			if ( !$settings['calendar_to_guests'] || ( $settings['calendar_to_guests'] && $session -> user['user_id'] != -1)){
				
				/**
				 * we can see calendar and all ;)
				 */
				
				$path -> addBreadcrumb( $language -> getString( 'main_menu_calendar'), parent::systemLink( 'calendar'));
				
				/**
				 * get actual month and year
				 */
				
				$ac_month = date( 'n', $time -> getTime());
				$ac_year = date( 'Y', $time -> getTime());
				
				/**
				 * get date to draw
				 */
				
				$draw_month = $_GET['m'];
				$draw_year = $_GET['y'];
				
				settype( $draw_month, 'integer');
				settype( $draw_year, 'integer');
				
				/**
				 * check if its proper
				 */
				
				if ( $draw_year < $settings['calendar_start']){
					
					$draw_month = $ac_month;
					$draw_year = $ac_year;
				
				}else if ( $draw_year > ($ac_year + $settings['calendar_max_jump'])){
					
					$draw_month = $ac_month;
					$draw_year = $ac_year;
					
				}
				
				/**
				 * get time
				 */
				
				$actual_date = mktime( 0, 0, 0, $draw_month, 1, $draw_year);
				
				$month_days_num = date( "t", $actual_date);
				$month_first_day = date( "w", $actual_date);
				
				/**
				 * start drawing table
				 */
				
				$column_num = 0;
				$day_num = 1;
				
				$events = array();
				
				/**
				 * get events
				 */
				
				$events_query = $mysql -> query( 'SELECT calendar_event_id, calendar_event_date, calendar_event_name
				FROM calendar_events
				WHERE (calendar_event_date LIKE "%-'.$draw_month.'-'.$draw_year.'" AND calendar_event_repeat = \'0\')
				OR (calendar_event_date LIKE "%-'.$draw_month.'-%" AND calendar_event_repeat = \'1\')
				OR (calendar_event_date LIKE "%-%-%" AND calendar_event_repeat = \'2\')');
				
				while( $events_result = mysql_fetch_array( $events_query, MYSQL_ASSOC)){
					
					//clear result
					$events_result = $mysql -> clear($events_result);
					
					//get event date
					$event_time = split( '-', $events_result['calendar_event_date']);
					
					/**
					 * add to list
					 */
					
					$events[$event_time[0]][] = '<a href="'.parent::systemLink( 'cal_event', array( 'event' => $events_result['calendar_event_id'])).'">'.$events_result['calendar_event_name'].'</a>';
					
				}
				
				/**
				 * get birthdays
				 */
				
				$birthdays_query = $mysql -> query( 'SELECT u.user_id, u.user_login, u.user_birth_date, g.users_group_prefix, g.users_group_suffix
				FROM users u
				LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
				WHERE u.user_birth_date LIKE "%-'.$draw_month.'-%" AND u.user_birth_date NOT LIKE "%-'.$draw_month.'-'.$draw_year.'"');
				
				while( $birthdays_result = mysql_fetch_array( $birthdays_query, MYSQL_ASSOC)){
					
					//clear result
					$birthdays_result = $mysql -> clear($birthdays_result);
					
					//get birth date
					$user_bith = split( '-', $birthdays_result['user_birth_date']);
					
					$user_age = $draw_year - $user_bith[2];
					
					/**
					 * set keys
					 */
					
					$language -> setKey( 'user_name', $birthdays_result['users_group_prefix'].$birthdays_result['user_login'].$birthdays_result['users_group_suffix']);
					$language -> setKey( 'birth_number', $user_age);
					
					/**
					 * add to list
					 */
					
					$events[$user_bith[0]][] = '<a href="'.parent::systemLink( 'user', array( 'user' => $birthdays_result['user_id'])).'">'.$language -> getString( 'calendar_user_birthday').'</a>';
					
				}
				
				/**
				 * navigation
				 */
				
				if ( $draw_year > $settings['calendar_start']){
					
					$back_year = '<a href="'.parent::systemLink( parent::getId(), array( 'm' => $draw_month, 'y' => ($draw_year - 1))).'">'.$language -> getString( 'calendar_navigation_back_year').'</a>';
					
				}else{
					
					$back_year = '&nbsp;';
					
				}
				
				if ( $draw_year < (date( 'Y') + $settings['calendar_max_jump'])){
					
					$next_year = '<a href="'.parent::systemLink( parent::getId(), array( 'm' => $draw_month, 'y' => ($draw_year + 1))).'">'.$language -> getString( 'calendar_navigation_next_year').'</a>';
					
				}else{
					
					$next_year = '&nbsp;';
					
				}
				
				if ( $draw_year > $settings['calendar_start'] || ($draw_year == $settings['calendar_start'] && $draw_month > 1)){
										
					if ( $draw_month == 1 && $draw_year > $settings['calendar_start']){
					
						$back_month = '<a href="'.parent::systemLink( parent::getId(), array( 'm' => 12, 'y' => ($draw_year - 1))).'">'.$language -> getString( 'calendar_navigation_back_month').'</a>';
						
					}else{
						
						$back_month = '<a href="'.parent::systemLink( parent::getId(), array( 'm' => ($draw_month - 1), 'y' => $draw_year)).'">'.$language -> getString( 'calendar_navigation_back_month').'</a>';
											
					}
					
				}else{
					
					$back_month = '&nbsp;';
					
				}
				
				if ( $draw_year < (date( 'Y') + $settings['calendar_max_jump']) ||( $draw_year ==(date( 'Y') + $settings['calendar_max_jump']) && $draw_month < 12)){
										
					if ( $draw_month == 12 && $draw_year < (date( 'Y') + $settings['calendar_max_jump'])){
					
						$next_month = '<a href="'.parent::systemLink( parent::getId(), array( 'm' => 1, 'y' => ($draw_year + 1))).'">'.$language -> getString( 'calendar_navigation_next_month').'</a>';
						
					}else{
						
						$next_month = '<a href="'.parent::systemLink( parent::getId(), array( 'm' => ($draw_month + 1), 'y' => $draw_year)).'">'.$language -> getString( 'calendar_navigation_next_month').'</a>';
											
					}
					
				}else{
					
					$next_month = '&nbsp;';
					
				}
				
				$navigation = '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed;">
				  <tr>
				    <td style="text-align: center">'.$back_year.'</td>
				    <td style="text-align: center">'.$back_month.'</td>
				    <td style="text-align: center">'.$next_month.'</td>
				    <td style="text-align: center">'.$next_year.'</td>
				  </tr>
				</table>';
				
				/**
				 * open table
				 */
				
				$month_days = new form();
				$month_days -> drawSpacer( $navigation);
				$month_days -> openOpTable( true);

				/**
				 * days of month
				 */
				
				if ( $settings['calendar_monday']){
					
					$month_days -> addToContent( '<tr>
						<th>'.$language -> getString( 'time_day_monday').'</th>
						<th>'.$language -> getString( 'time_day_tuesday').'</th>
						<th>'.$language -> getString( 'time_day_wednesday').'</th>
						<th>'.$language -> getString( 'time_day_thursday').'</th>
						<th>'.$language -> getString( 'time_day_friday').'</th>
						<th>'.$language -> getString( 'time_day_saturday').'</th>
						<th>'.$language -> getString( 'time_day_sunday').'</th>
					</tr>');
					
				}else{
						
					$month_days -> addToContent( '<tr>
						<th>'.$language -> getString( 'time_day_sunday').'</th>
						<th>'.$language -> getString( 'time_day_monday').'</th>
						<th>'.$language -> getString( 'time_day_tuesday').'</th>
						<th>'.$language -> getString( 'time_day_wednesday').'</th>
						<th>'.$language -> getString( 'time_day_thursday').'</th>
						<th>'.$language -> getString( 'time_day_friday').'</th>
						<th>'.$language -> getString( 'time_day_saturday').'</th>
					</tr>');
									
				}
				
				/**
				 * blank cells to first day
				 */
				
				$month_days -> addToContent( '<tr>');
				
				$blank_cells = $month_first_day;
				
				if ( $settings['calendar_monday']){
					$blank_cells --;
						
					if ( $month_first_day == 0)
						$blank_cells += 7;
						
				}	
					
				while ( $blank_cells > 0){
					
					$month_days -> addToContent( '<td class="opt_row3">&nbsp;</td>');
									
					$blank_cells --;
					$column_num ++;
					
				}
					
				/**
				 * now draw days
				 */
				
				while ( $day_num <= $month_days_num){
					
					/**
					 * break columnt after 7 cells
					 */
					
					if ( $column_num == 7){
						
						$column_num = 0;
						$month_days -> addToContent( '</tr><tr>');
				
					}
					
					/**
					 * is it today?
					 */
					
					if ( $draw_month == $ac_month && $draw_year == $ac_year && $day_num == date( 'j', $time -> getTime())){
						
						$month_days -> addToContent( '<td class="calendar_today">');
					
					}else{
						
						$month_days -> addToContent( '<td class="calendar_otherday">');
					
					}
					
					/**
					 * draw day title
					 */
					
					$month_days -> addToContent( '<div class="calendar_title">'.$day_num.'.</div>');
					
					/**
					 * and content
					 */
					
					if ( key_exists( $day_num, $events)){
						
						$today_content = join( "<br />", $events[$day_num]);
						
					}else{
						
						$today_content = '&nbsp;';
						
					}
					
					$month_days -> addToContent( '<div class="calendar_content">'.$today_content.'</div>');
					
					$month_days -> addToContent( '</td>');
					
					/**
					 * increase counters
					 */
					
					$day_num ++;
					$column_num ++;
					
				}
				
				/**
				 * finish table
				 */
				
				while ( $column_num < 7){
					
					$month_days -> addToContent( '<td class="opt_row3">&nbsp;</td>');
					$column_num ++;
					
				}
				
				$month_days -> addToContent( '</tr>');
				
				/**
				 * draw it
				 */
				
				$month_days -> closeTable();			
				
				parent::draw( $style -> drawFormBlock( $time -> translateDate( date( 'F Y', $actual_date)), $month_days -> display()));
				
				/**
				 * set all
				 */
				
				$path -> addBreadcrumb( $time -> translateDate( date( 'F Y', $actual_date)), parent::systemLink( 'calendar', array( 'm' => $draw_month, 'y' => $draw_year)));
				$output -> setTitle( $language -> getString( 'main_menu_calendar').' - '.$time -> translateDate( date( 'F Y', $actual_date)));
				
				/**
				 * what about new event?
				 */
				
				if ( $session -> user['user_can_edit_calendar']){
					
					$event_link = '<a href="'.parent::systemLink( 'cal_event_new').'">'.$language -> getString( 'calendar_add_event').'</a>';
					
				}else{
					$event_link = '';
				}
				
				/**
				 * and jumping?
				 */
				
				$months_list = '<option value="1">'.$language -> getString( 'time_month_january').'</option>';
				$months_list .= '<option value="2">'.$language -> getString( 'time_month_february').'</option>';
				$months_list .= '<option value="3">'.$language -> getString( 'time_month_march').'</option>';
				$months_list .= '<option value="4">'.$language -> getString( 'time_month_april').'</option>';
				$months_list .= '<option value="5">'.$language -> getString( 'time_month_may').'</option>';
				$months_list .= '<option value="6">'.$language -> getString( 'time_month_june').'</option>';
				$months_list .= '<option value="7">'.$language -> getString( 'time_month_july').'</option>';
				$months_list .= '<option value="8">'.$language -> getString( 'time_month_august').'</option>';
				$months_list .= '<option value="9">'.$language -> getString( 'time_month_september').'</option>';
				$months_list .= '<option value="10">'.$language -> getString( 'time_month_october').'</option>';
				$months_list .= '<option value="11">'.$language -> getString( 'time_month_november').'</option>';
				$months_list .= '<option value="12">'.$language -> getString( 'time_month_december').'</option>';
				
				$years_list = '';
				
				$year_to_list = $settings['calendar_start'];
				
				while ( $year_to_list <= (date( 'Y', $time -> getTime()) + $settings['calendar_max_jump'])){
					
					if ( $year_to_list == $draw_year){
						
						$years_list .= '<option value="'.$year_to_list.'" selected>'.$year_to_list.'</option>';
				
					}else{
						
						$years_list .= '<option value="'.$year_to_list.'">'.$year_to_list.'</option>';
				
					}
					
					$year_to_list++;
					
				}
				
				$jump_form = new form();
				$jump_form -> openForm( '', 'GET');
				$jump_form -> hiddenValue( 'act', 'calendar');
				$jump_form -> addToContent( '<b>'.$language -> getString( 'calendar_jump').':</b><br /><select name="m">'.str_replace( 'value="'.$draw_month.'"', 'value="'.$draw_month.'" selected', $months_list).'</select> <select name="y">'.$years_list.'</select> <input name="" value="'.$language -> getString( 'calendar_jump_button').'" type="submit">');
				$jump_form -> closeForm();
				/**
				 * and draw tab
				 */
				
				parent::draw( $style -> drawBlankBlock( '<table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <tr>
				    <td nowrap="nowrap">'.$event_link.'</td>
				    <td style="width: 100%">&nbsp;</td>
				    <td nowrap="nowrap">'.($jump_form -> display()).'</td>
				  </tr>
				</table>'));
				
			}else{
			
				/**
				 * we cant see calendar
				 */

				$main_error = new main_error();
				$main_error -> type = 'information';
				parent::draw( $main_error -> display());
				
			}
			
		}else{
			
			$main_error = new main_error();
			$main_error -> type = 'information';
			$main_error -> message = $language -> getString( 'calendar_turned_off');
			parent::draw( $main_error -> display());
			
		}
		
	}
	
}

?>