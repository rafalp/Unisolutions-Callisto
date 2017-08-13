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
	
class action_calendar_event extends action{
		
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
				 * get event to draw
				 */
				
				$event_to_draw = $_GET['event'];
				settype( $event_to_draw, 'integer');
				
				$event_query = $mysql -> query( "SELECT e.*, u.*, f.*, g.* FROM calendar_events e
				LEFT JOIN users u ON e.calendar_event_user = u.user_id
				LEFT JOIN profile_fields_data f ON u.user_id = f.profile_fields_user
				LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
				WHERE e.calendar_event_id = '$event_to_draw'");
				
				if ( $event_result = mysql_fetch_array( $event_query, MYSQL_ASSOC)){
					
					//clear result
					$event_result = $mysql -> clear( $event_result);
					
					/**
					 * drawing
					 */
					
					$draw_strings = array();
					$draw_parts = array();
					
					$cal_event = new form();
					$cal_event -> openOpTable();
				
					/**
					 * event info
					 */
					
					$event_time = split( "-", $event_result['calendar_event_date']);
					
					$event_timestamp = mktime( 0, 0, 0, $event_time[1], $event_time[0], $event_time[2]);
					
					$path -> addBreadcrumb( $time -> translateDate( date( 'F Y', $event_timestamp)), parent::systemLink( 'calendar', array( 'm' => $event_time[1], 'y' => $event_time[2])));
					$path -> addBreadcrumb( $language -> getString( 'calendar_event_show'), parent::systemLink( 'cal_event', array( 'event' => $event_to_draw)));
					$path -> addBreadcrumb( $event_result['calendar_event_name'], parent::systemLink( 'cal_event', array( 'event' => $event_to_draw)));
					
					switch ( $event_result['calendar_event_repeat']){
						
						case 0:
							
							$language -> setKey( 'event_time', $time -> translateDate( date( 'j F Y', $event_timestamp)));
							
							if ( time() > $event_timestamp){
								
								$event_time_text = $language -> getString( 'calendar_event_date_exact_past');
								
							}else{
								
								$event_time_text = $language -> getString( 'calendar_event_date_exact');
								
							}
							
						break;
						
						case 1:
							
							$language -> setKey( 'event_time', $event_time[0].$event_time[1]);
												
							$event_time_text = $language -> getString( 'calendar_event_date_year');
														
						break;
						
						case 2:
							
							$language -> setKey( 'event_time', $event_time[0]);
												
							$event_time_text = $language -> getString( 'calendar_event_date_month');
							
						break;
						
					}
					
					$language -> setKey( 'event_add_time', $time -> drawDate( $event_result['calendar_event_add_time']));
					
					$draw_strings['INFO'] = '<table border="0" cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td>'.$event_time_text.'</td>
							<td style="text-align: right">'.$language -> getString( 'calendar_event_add_date').'</td>
						</tr>
					</table>';
					
					/**
					 * begin from author
					 */
					
					if ( $event_result['calendar_event_user'] == -1){
					
						/**
						 * author is deleted
						 */
						
						$draw_parts['author_status'] = false;
						
						$draw_strings['AUTHOR_NAME'] = '<a name="post'.$event_result['calendar_event_id'].'" id="post'.$event_result['calendar_event_id'].'">'.$event_result['users_group_prefix'].$event_result['calendar_event_username'].$event_result['users_group_suffix'].'</a>';
						
					}else{
						
						/**
						 * author exists
						 */
						
						if ( $settings['users_count_online']){
						
							$draw_parts['author_status'] = true;
						
							$draw_strings['AUTHOR_STATUS_IMG'] = $style -> drawStatus( $users -> checkOnLine( $event_result['calendar_event_user']));
							
						}else{
							
							$draw_parts['author_status'] = false;
							
						}
						
						/**
						 * and user login
						 */
						
						$draw_strings['AUTHOR_NAME'] = '<a name="post'.$event_result['calendar_event_id'].'" id="post'.$event_result['calendar_event_id'].'">'.'<a href="'.parent::systemLink( 'user', array( 'user' => $event_result['user_id'])).'">'.$event_result['users_group_prefix'].$event_result['user_login'].$event_result['users_group_suffix'].'</a></a>';				
						
					}
							
					/**
					 * author profile
					 */
					
					if ( $event_result['user_avatar_type'] != 0 && $settings['users_can_avatars'] && $session -> user['user_show_avatars']){
						
						$draw_parts['avatar'] = true;
						
						/**
						 * draw avatar
						 */
						
						$draw_strings['AUTHOR_AVATAR'] = $users -> drawAvatar( $event_result['user_avatar_type'], $event_result['user_avatar_image'], $event_result['user_avatar_width'], $event_result['user_avatar_height']);				
						
					}else{
						
						$draw_parts['avatar'] = false;
						
					}
					
					/**
					 * title
					 */
					
					if ( strlen( $event_result['user_custom_title']) > 0  && $settings['users_posts_to_title'] > 0 && $event_result['user_posts_num'] >= $settings['users_posts_to_title']){
							
						$draw_strings['AUTHOR_TITLE'] = $event_result['user_custom_title'];
						
					}else if ( strlen( $event_result['users_group_title']) > 0){
						
						$draw_strings['AUTHOR_TITLE'] = $event_result['users_group_title'];
						
					}else{
						
						$draw_strings['AUTHOR_TITLE'] = $users -> drawRankName( $event_result['user_posts_num']);
											
					}
					
					if ( strlen( $users -> users_groups[ $event_result['user_main_group']]['users_group_image']) > 0){
						$draw_strings['AUTHOR_RANK'] = '<img src="'.$users -> users_groups[$event_result['user_main_group']]['users_group_image'].'" alt="" title""/>';						
					}else{
						$draw_strings['AUTHOR_RANK'] = $users -> drawRankImage( $event_result['user_posts_num']);
					}
					
					$sender_groups = array();
					$sender_groups = split( ",", $event_result['user_other_groups']);
					$sender_groups[] = $event_result['user_main_group'];
					
					$draw_strings['PROFILE_GROUP'] = $language -> getString( 'user_group');
					$draw_strings['USER_GROUP'] = $event_result['users_group_prefix'].$event_result['users_group_name'].$event_result['users_group_suffix'];
					
					$draw_strings['PROFILE_POSTS'] = $language -> getString( 'user_posts');
					$draw_strings['USER_POSTS'] = $event_result['user_posts_num'];
										
					$draw_parts['posts'] = true;
														
					if ( strlen( $event_result['user_localisation']) != 0){
								
						$draw_parts['author_location'] = true;
						$draw_strings['PROFILE_LOCATION'] = $language -> getString( 'user_localisation');
						$draw_strings['USER_LOCATION'] = $event_result['user_localisation'];
					
					}else{
						
						$draw_parts['author_location'] = false;
						
					}
					
					$draw_strings['PROFILE_JOIN_DATE'] = $language -> getString( 'user_registration');
					$draw_strings['USER_JOIN_DATE'] = $time -> drawDate( $event_result['user_regdate']);
								
					/**
					 * reputation
					 */
					
					if ( $settings['reputation_turn']){
						
						$draw_parts['reps'] = true;
						
						$draw_strings['PROFILE_REPUTATION'] = $language -> getString( 'user_reputation');
					
						$draw_strings['USER_REPUTATION'] = $users -> drawReputation( $users -> countReputation( $post_result['user_rep'], $post_result['user_posts_num'], $post_result['user_regdate']));
						
					}else{
						
						$draw_parts['reps'] = false;
						
					}
						
					/**
					 * warns
					 */
							
					if ( $settings['warns_turn'] && $event_result['post_author'] != -1){
						
						/**
						 * check if we can see warns
						 */
						
						if ( $settings['warns_show'] == 0 || ( $settings['warns_show'] == 1 && $session -> user['user_id'] != -1) || ( $settings['warns_show'] == 2 && ($session -> user['user_can_be_mod'] || ($session -> user['user_id'] != -1 && $session -> user['user_id'] == $event_result['post_author'])))){
						
							/**
							 * draw warns
							 */
							
							$draw_parts['warns'] = true;
							
							$draw_strings['PROFILE_WARNS'] = $language -> getString( 'user_warns');							
							
							if ( $event_result['user_warns'] > 0){
							
								$warns_link_open = '<a href="'.parent::systemLink( 'user_warns', array( 'user' => $event_result['calendar_event_user'])).'">';
								$warns_link_close = '</a>';
								
							}else{
								
								$warns_link_open = '';
								$warns_link_close = '';
								
							}
							
							/**
							 * draw warns, or wanrs + mod?
							 */
							
							if ( $session -> user['user_can_be_mod']){
								
								$draw_strings['USER_WARNS'] = '<a href="'.parent::systemLink( 'mod', array( 'user' => $event_result['calendar_event_user'], 'd' => '1')).'" title="'.$language -> getString( 'user_warn_decrease').'">'.$style -> drawImage( 'minus').'</a> '.$warns_link_open.$users -> drawWarnLevel( $event_result['user_warns']).$warns_link_close.' <a href="'.parent::systemLink( 'mod', array( 'user' => $event_result['calendar_event_user'], 'd' => '0')).'" title="'.$language -> getString( 'user_warn_add').'">'.$style -> drawImage( 'plus').'</a>';
								
							}else{
								
								$draw_strings['USER_WARNS'] = $warns_link_open.$users -> drawWarnLevel( $event_result['user_warns']).$warns_link_close;
															
							}
							
						}else{
							
							/**
							 * cant see warns
							 */
							
							$draw_parts['warns'] = false;
						
						}
						
					}else{
						
						$draw_parts['warns'] = false;
						
					}
							
					/**
					 * custom fields
					 */
					
					$drawed_custom_fields = '';
					
					if ( $event_result['user_id'] != -1){
						
						if ( count( $users -> custom_fields) > 0){
							
							foreach ( $users -> custom_fields as $field_id => $field_ops) {
								
								if ( strlen( $event_result['field_'.$field_id]) > 0 && $field_ops['profile_field_inposts'] && (!$field_ops['profile_field_private'] || ( $field_ops['profile_field_private'] && ( $event_result['user_id'] == $session -> user['user_id'] || $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate'])))){
									
									$field_template = $field_ops['profile_field_display'];
					
									$field_template = str_ireplace( '{NAME}', $field_ops['profile_field_name'], $field_template);
									
									if ( $field_ops['profile_field_type'] == 2){
										
										/**
										 * profile options list
										 */
										
										$preparsed_options = split( "\n", $field_ops['profile_field_options']);
										
										$made_options = array();
										
										foreach ( $preparsed_options as $preparsed_option) {
											
											$option_id = substr( $preparsed_option, 0, strpos( $preparsed_option, "="));
											$option_value = substr( $preparsed_option, strpos( $preparsed_option, "=") + 1);
											
											$made_options[$option_id] = $option_value;
											
										}
										
										$field_template = str_ireplace( '{KEY}', $post_result['field_'.$field_id], $field_template);
										$field_template = str_ireplace( '{VALUE}', $made_options[$event_result['field_'.$field_id]], $field_template);
										
									}else{
										
										$field_template = str_ireplace( '{VALUE}', $event_result['field_'.$field_id], $field_template);
																
									}
															
									$drawed_custom_fields .= '<br />'.$field_template;
									
								}
								
							}
							
						}
						
					}
					
					$draw_strings['FIELDS'] = $drawed_custom_fields;
							
					/**
					 * signature
					 */
					
					if ( strlen( $event_result['user_signature']) > 0 && $session -> user['user_show_sigs'] && $settings['users_can_sigs']){
						
						$draw_parts['signature'] = true;
						$draw_strings['SIGNATURE'] = $strings -> parseBB( nl2br( $event_result['user_signature']), $settings['users_allow_bbcodes_in_sigs'], $settings['users_allow_emoticones_in_sigs']);
					
					}else{
						
						$draw_parts['signature'] = false;
							
					}
					
					/**
					 * shortcuts now
					 */
					
					$shortcuts_list = array();
						
					/**
					 * mail
					 */
					
					if ( $event_result['user_want_mail'] && $event_result['user_id'] != -1 && $session -> user['user_can_send_mails']){
						
						if ( $event_result['user_show_mail']){
						
							/**
							 * user show his mail, draw it
							 */
							
							$send_mail_link = 'mailto:'.$event_result['user_mail'];
							
						}else{
							
							/**
							 * user not shows his mail, we have to send it "round the way"
							 */
							
							$mail_user_target = array( 'user' => $event_result['user_id']);
							
							$send_mail_link = parent::systemLink( 'mail_user', $mail_user_target);
							
						}
						
						$shortcuts_list[] = '<a href="'.$send_mail_link.'">'.$style -> drawImage( 'button_email', $language -> getString( 'user_mail_send')).'</a>';
						
					}
					
					/**
					 * pm
					 */
					
					if ( $event_result['user_id'] != -1 && $session -> user['user_id'] != -1){
						
						$send_pw_link = array( 'do' => 'new_pm', 'user' => $event_result['user_id']);
							
						$shortcuts_list[] = '<a href="'.parent::systemLink( 'profile', $send_pw_link).'">'.$style -> drawImage( 'button_pm', $language -> getString( 'user_pm_send')).'</a>';
						
					}
					
					/**
					 * www
					 */
						
					if ( !empty( $event_result['user_web'])){
							
						if ( substr( $event_result['user_web'], 0, 7) != "http://")
							$event_result['user_web'] = "http://".$event_result['user_web'];
							
						$shortcuts_list[] = '<a href="'.$event_result['user_web'].'">'.$style -> drawImage( 'button_www', $language -> getString( 'user_www')).'</a>';
					
					}
											
					/**
					 * display
					 */
					
					$draw_strings['SHORTCUTS'] = join( " ", $shortcuts_list);
					
					/**
					 * and actions now
					 */
					
					$pm_actions = array();
					
					/**
					 * edit button
					 */
					
					if ( $session -> user['user_can_edit_calendar'] ){
						
						/**
						 * we can operate in topic
						 */
						
						$pm_actions[] = '<a href="'.parent::systemLink( 'cal_event_edit', array( 'event' => $event_result['calendar_event_id'])).'">'.$style -> drawImage( 'button_edit', $language -> getString( 'calendar_event_edit_act')).'</a>';
						
						$pm_actions[] = '<a href="'.parent::systemLink( 'cal_event_del', array( 'event' => $event_result['calendar_event_id'])).'">'.$style -> drawImage( 'button_delete', $language -> getString( 'calendar_event_del_act')).'</a>';
																								
					}
						
					/**
					 * display actions
					 */
					
					$draw_strings['ACTIONS'] = join( " ", $pm_actions);
									
					/**
					 * event text
					 */
							
					$message_text = $event_result['calendar_event_info'];
					
					/**
					 * check badwords
					 */
									
					if ( !$users -> cantCensore( $sender_groups)){
						$message_text = $strings -> censore( $message_text);
					}
					
					/**
					 * add it
					 */
										
					$draw_strings['POST_TEXT'] = $strings -> parseBB( nl2br( $message_text), true, true);
					
					//limit it
					if( $settings['message_big_cut'] > 0 && !defined( 'SIMPLE_MODE')){
						
						$draw_strings['POST_TEXT'] = '<div style="max-height: '.$settings['message_big_cut'].'px;overflow: auto">'.$draw_strings['POST_TEXT'].'<div>';
						
					}
						
					/**
					 * hide few parts
					 */
									
					$draw_parts['reported'] = false;
					$draw_parts['attachment'] = false;
					$draw_parts['edit'] = false;
					$draw_parts['thanks'] = false;
					
					/**
					 * display
					 */
						
					$cal_event -> addToContent( $style -> drawPost( 0, $draw_parts, $draw_strings));
					$cal_event -> closeTable();
					
					/**
					 * display
					 */
					
					parent::draw( $style -> drawFormBlock( $event_result['calendar_event_name'], $cal_event -> display()));
					
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