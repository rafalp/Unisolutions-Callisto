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
|	Calendar event edition
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class action_edit_calendar_event extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * begin error checking
		 */
				
		if ( $settings['calendar_turn']){
			
			/**
			 * can we browse it?
			 */
			
			if ( $session -> user['user_id'] != -1){
				
				/**
				 * we can see calendar and all ;)
				 */
				
				if ( $session -> user['user_can_edit_calendar']){
				
					/**
					 * select event
					 */
					
					$event_to_draw = $_GET['event'];
					settype( $event_to_draw, 'integer');
					
					$event_query = $mysql -> query( "SELECT * FROM calendar_events WHERE `calendar_event_id` = '$event_to_draw'");
					
					if ( $event_result = mysql_fetch_array( $event_query, MYSQL_ASSOC)){
					
						//clear and keep result
						$event_result = $mysql -> clear( $event_result);
						$this -> event_result = $event_result;
						
						$path -> addBreadcrumb( $language -> getString( 'main_menu_calendar'), parent::systemLink( 'calendar'));
						
						$path -> addBreadcrumb( $language -> getString( 'calendar_edit_event'), parent::systemLink( 'cal_event_edit', array( 'event' => $event_to_draw)));
						
						$output -> setTitle( $language -> getString( 'calendar_edit_event'));
						
						/**
						 * specify what to do
						 */
		
						if ( $session -> checkForm()){
						
							if ( $_GET['preview']){
								
								$event_info = stripslashes( $strings -> inputClear( $_POST['event_info'], false));
								
								if ( strlen( $event_info) > 0){
									
									parent::draw( $style -> drawBlock( $language -> getString( 'calendar_add_event_preview'), $strings -> parseBB(nl2br( $event_info), true, true)));
									
									
								}else{
									
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'calendar_add_event_preview'), $language -> getString( 'calendar_add_event_empty_info')));
												
								}
								
								/**
								 * draw again form
								 */
								
								$this -> drawForm( true);
						
							}else{
								
								/**
								 * new event
								 */
								
								$event_day = $_POST['event_day'];
								$event_month = $_POST['event_month'];
								$event_year = $_POST['event_year'];
								$event_repeat = $_POST['event_repeat'];
								
								settype( $event_day, 'integer');
								settype( $event_month, 'integer');
								settype( $event_year, 'integer');
								settype( $event_repeat, 'integer');
											
								$event_name = $strings -> inputClear( $_POST['event_name'], false);
								
								$event_name_clear = trim( $_POST['event_name']);
								$event_name_clear = str_replace( '&quot;', '"', $event_name_clear);
												
									if ( get_magic_quotes_gpc())
										$event_name_clear = stripslashes( $event_name_clear);
								
								$event_info = $strings -> inputClear( $_POST['event_info'], false);
								
								if ( $event_repeat < 0)
									$event_repeat = 0;
								
								if ( $event_repeat > 2)
									$event_repeat = 2;
									
								/**
								 * check date
								 */
								
								if ( strlen($event_name) == 0){
	
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'calendar_edit_event'), $language -> getString( 'calendar_add_event_empty_name')));
									$this -> drawForm( true);
									
								}else if ( strlen( $event_name_clear) > $settings['msg_title_max_length'] || strlen( $event_name_clear) > 90){
						
									$language -> setKey( 'event_title_limit', $settings['msg_title_max_length']);
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'calendar_edit_event'), $language -> getString( 'calendar_add_event_long_name')));
									
									$this -> drawForm( true);
							
								}else if ( strlen($event_info) == 0){
	
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'calendar_edit_event'), $language -> getString( 'calendar_add_event_empty_info')));
									$this -> drawForm( true);
									
								}else if ( $event_day < 1){
	
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'calendar_edit_event'), $language -> getString( 'calendar_add_event_wrong_date')));
									$this -> drawForm( true);
									
								}else if ( $event_day > 31){
	
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'calendar_edit_event'), $language -> getString( 'calendar_add_event_wrong_date')));
									$this -> drawForm( true);
									
								}else if ( $event_month < 1){
	
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'calendar_edit_event'), $language -> getString( 'calendar_add_event_wrong_date')));
									$this -> drawForm( true);
									
								}else if ( $event_month > 12){
	
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'calendar_edit_event'), $language -> getString( 'calendar_add_event_wrong_date')));
									$this -> drawForm( true);
									
								}else if ( $event_year < $settings['calendar_start']){
	
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'calendar_edit_event'), $language -> getString( 'calendar_add_event_wrong_date')));
									$this -> drawForm( true);
									
								}else if ( $event_year > date( 'Y', $time -> getTime()) + $settings['calendar_max_jump']){
	
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'calendar_edit_event'), $language -> getString( 'calendar_add_event_wrong_date')));
									$this -> drawForm( true);
									
								}else{
									
									/**
									 * date checked from one side, another one is problem of the adder :P
									 */
									
									$new_event_sql['calendar_event_date'] = $event_day.'-'.$event_month.'-'.$event_year;
									$new_event_sql['calendar_event_repeat'] = $event_repeat;
									$new_event_sql['calendar_event_name'] = $event_name;
									$new_event_sql['calendar_event_info'] = $event_info;
									
									$mysql -> update( $new_event_sql, 'calendar_events', "`calendar_event_id` = '$event_to_draw'");
									
									/**
									 * clear cache
									 */
									
									$cache -> flushCache( 'today_events');
									$cache -> flushCache( 'coming_events');
									
									/**
									 * draw message
									 */
									
									parent::draw( $style -> drawBlock( $language -> getString( 'calendar_edit_event'), $language -> getString( 'calendar_edit_event_done').'<br /><br /><a href="'.parent::systemLink( 'cal_event', array( 'event' => $event_to_draw)).'">'.$language -> getString( 'calendar_event_return').'</a><br /><a href="'.parent::systemLink( 'calendar', array( 'm' => $event_month, 'y' => $event_year)).'">'.$language -> getString( 'calendar_return').'</a>'));
									
								}
								
							}
								
						}else{
						
							$this -> drawForm();
						
						}
						
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
					 * we cant edit calendar
					 */
					
					$main_error = new main_error();
					$main_error -> type = 'error';
					$main_error -> message = $language -> getString( 'calendar_cant_edit');
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
	
	function drawForm( $retake = false){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * set values
		 */
		
		$event_date = split( "-", $this -> event_result['calendar_event_date']);
		
		$event_day = $event_date[0];
		$event_month = $event_date[1];
		$event_year = $event_date[2];

		$event_repeat = $this -> event_result['calendar_event_repeat'];
		$event_name = $this -> event_result['calendar_event_name'];
		$event_info = $this -> event_result['calendar_event_info'];
			
		/**
		 * retake
		 */
		
		if ( $retake){
			
			$event_day = $_POST['event_day'];
			$event_month = $_POST['event_month'];
			$event_year = $_POST['event_year'];
			$event_repeat = $_POST['event_repeat'];
			
			settype( $event_day, 'integer');
			settype( $event_month, 'integer');
			settype( $event_year, 'integer');
			settype( $event_repeat, 'integer');
						
			$event_name = stripslashes( $strings -> inputClear( $_POST['event_name'], false));
			$event_info = stripslashes( $strings -> inputClear( $_POST['event_info'], false));
			
		}
		
		/**
		 * begin drawing form
		 */
		
		$new_event = new form();
		$new_event -> openForm( parent::systemLink( 'cal_event_edit', array( 'event' => $this -> event_result['calendar_event_id'])), 'POST', false, 'new_event');
		$new_event -> openOpTable();
		
		$new_event -> drawTextInput( $language -> getString( 'calendar_add_event_name'), 'event_name', $event_name);
		$new_event -> drawInfoRow( $language -> getString( 'calendar_add_event_date'), '<input name="event_day" type="text" size="2" maxlength="2" value="'.$event_day.'"/> - <input name="event_month" type="text" size="2" maxlength="2" value="'.$event_month.'"/> - <input name="event_year" type="text" size="4" maxlength="4" value="'.$event_year.'"/>', $language -> getString( 'calendar_add_event_date_help'));
		
		$freq_list[0] = $language -> getString( 'calendar_add_event_repeat_0');
		$freq_list[1] = $language -> getString( 'calendar_add_event_repeat_1');
		$freq_list[2] = $language -> getString( 'calendar_add_event_repeat_2');
		
		$new_event -> drawList( $language -> getString( 'calendar_add_event_repeat'), 'event_repeat', $freq_list, $event_repeat);
		$new_event -> drawEditor( $language -> getString( 'calendar_add_event_info'), 'event_info', $event_info, null, true, true);
		
		$new_event -> closeTable();
		$new_event -> drawButton( $language -> getString( 'calendar_edit_event_send'), false, '<input name="show_preview" type="button" value="'.$language -> getString( 'calendar_add_event_preview').'" onClick="previewEvent()" />');
		$new_event -> closeForm();
		$new_event -> addToContent( '<script type="text/JavaScript">
		
			function previewEvent(){
				
				post_form = document.forms["new_event"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'preview' => true, 'event' => $this -> event_result['calendar_event_id'])).'";
			
				post_form.submit();
				
			}
			
		</script>');
		
		/**
		 * draw form
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'calendar_edit_event'), $new_event -> display()));
		
	}
	
}

?>