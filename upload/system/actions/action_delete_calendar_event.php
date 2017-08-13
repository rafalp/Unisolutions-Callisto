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
|	Show Calendar Event
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class action_delete_calendar_event extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * begin error checking
		 */
				
		if ( $settings['calendar_turn']){
			
			/**
			 * can we browse it?
			 */
			
			if ( $session -> user['user_id'] != -1 && $session -> user['user_can_edit_calendar']){
				
				/**
				 * we can see calendar and all ;)
				 */
				
				$path -> addBreadcrumb( $language -> getString( 'main_menu_calendar'), parent::systemLink( 'calendar'));
				
				/**
				 * get event to draw
				 */
				
				$event_to_draw = $_GET['event'];
				settype( $event_to_draw, 'integer');
				
				$event_query = $mysql -> query( "SELECT e.*, u.*, g.* FROM calendar_events e
				LEFT JOIN users u ON e.calendar_event_user = u.user_id
				LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
				WHERE e.calendar_event_id = '$event_to_draw'");
				
				if ( $event_result = mysql_fetch_array( $event_query, MYSQL_ASSOC)){
					
					$path -> addBreadcrumb( $language -> getString( 'calendar_delete_event'), parent::systemLink( 'calendar'));
					
					/**
					 * delete
					 */
					
					$mysql -> delete( "calendar_events", "`calendar_event_id` = '$event_to_draw'");
					
					/**
					 * clear cache
					 */
					
					$cache -> flushCache( 'today_events');
					$cache -> flushCache( 'coming_events');
					
					/**
					 * draw message
					 */
					
					parent::draw( $style -> drawBlock( $language -> getString( 'calendar_delete_event'), $language -> getString( 'calendar_delete_event_done').'<br /><br /><a href="'.parent::systemLink( 'calendar').'">'.$language -> getString( 'calendar_return').'</a>'));
					
					
				}else{
					
					/**
					 * event not found
					 */
				
					$main_error = new main_error();
					$main_error -> type = 'error';
					$main_error -> message = $language -> getString( 'calendar_notfound');
					parent::draw( $main_error -> display());	
								
				}
				
			}else{
			
				/**
				 * we cant see calendar
				 */
				
				$main_error = new main_error();
				$main_error -> type = 'error';
				$main_error -> message = $language -> getString( 'calendar_cant_edit');
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