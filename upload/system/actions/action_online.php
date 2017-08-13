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
|	Show on-line list
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
/**
 * draw board guidelines
 *
 */

class action_online extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
						
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'online_list'));
		
		/**
		 * and add breadcrumb
		 */
		
		$path -> addBreadcrumb( $language -> getString( 'online_list'), parent::systemLink( parent::getId()));
	
		/**
		 * begin drawing tab
		 */
		
		$sessions_tab = new form();
		$sessions_tab -> openOpTable( true);
		$sessions_tab -> addToContent( '<tr>
			<th>'.$language -> getString( 'online_list_user').'</th>
			<th>'.$language -> getString( 'online_list_localisation').'</th>
			<th>'.$language -> getString( 'online_list_last_click').'</th>
		</tr>');
		
		/**
		 * do query
		 */
		
		$sessions_query = $mysql -> query( "SELECT s.*, u.user_login, g.users_group_prefix, g.users_group_suffix, f.forum_id, f.forum_name, t.topic_id, t.topic_name, t.topic_forum_id, p.post_topic, tu.user_login as w_user_login, tg.users_group_prefix as w_prefix, tg.users_group_suffix as w_suffix
		FROM users_sessions s
		LEFT JOIN users u ON s.users_session_user_id = u.user_id
		LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
		LEFT JOIN users tu ON s.users_session_location_user = tu.user_id
		LEFT JOIN users_groups tg ON tu.user_main_group = tg.users_group_id
		LEFT JOIN forums f ON s.users_session_location_forum = f.forum_id
		LEFT JOIN topics t ON s.users_session_location_topic = t.topic_id
		LEFT JOIN posts p ON s.users_session_location_post = p.post_id
		ORDER BY s.users_session_last_time DESC");
		
		while ( $session_result = mysql_fetch_array( $sessions_query, MYSQL_BOTH)){
			
			//clear result
			$session_result = $mysql -> clear( $session_result);
			
			/**
			 * user name
			 */
			
			if ( $session_result['users_session_bot']){
			
				/**
				 * its a bot session
				 */
				
				$user_name = '<span class="online_bot">'.$session_result['users_session_bot_name'].'</span>';
			
			}else{
				
				/**
				 * normal session
				 */
				
				if ( $session_result['users_session_user_id'] == -1){
					
					/**
					 * user is guest
					 */
					
					$user_name = $session_result['users_group_prefix'].$language -> getString( 'guest').$session_result['users_group_suffix'];
					
				}else{
					
					/**
					 * hiddens
					 */
					
					if ( !$session_result['users_session_hidden'] || $session -> user['user_see_hidden'] || $session -> user['user_id'] == $session_result['users_session_user_id']){
					
						if ( $session_result['users_session_hidden']){
							$hidden_prefix = '<b>'.$language -> getString( 'hidden_user').':</b> ';
						}else{
							$hidden_prefix = '';
						}
						
						$user_name = $hidden_prefix.'<a href="'.parent::systemLink( 'user', array( 'user' => $session_result['users_session_user_id'])).'">'.$session_result['users_group_prefix'].$session_result['user_login'].$session_result['users_group_suffix'].'</a>';
					
					}
				}
				
			}
			
			/**
			 * user localization
			 */
			
			switch ( $session_result['users_session_location_act']){
					
				case 'register':
					
					$user_localization_url = parent::systemLink( 'register');
					$user_localization = $language -> getString( 'online_list_localisation_register');
					
				break;
				
				case 'activate_acc':
					
					$user_localization_url = parent::systemLink( 'activate_acc');
					$user_localization = $language -> getString( 'online_list_localisation_activate_acc');
					
				break;
				
				case 'reset_pass':
					
					$user_localization_url = parent::systemLink( 'reset_pass');
					$user_localization = $language -> getString( 'reset_password');
					
				break;
				
				case 'users':
					
					if ( $session -> user['user_can_see_users_profiles']){
					
						$user_localization_url = parent::systemLink( 'users');
						$user_localization = $language -> getString( 'online_list_localisation_users');
						
					}else{
						
						$user_localization_url = ROOT_PATH;
						$user_localization = $language -> getString( 'online_list_localisation_main');
					
					}
					
				break;
				
				case 'user':
					
					if ( $session -> user['user_can_see_users_profiles']){
										
						$language -> setKey( 'session_user', $session_result['w_prefix'].$session_result['w_user_login'].$session_result['w_suffix']);
						
						$user_localization_url = parent::systemLink( 'user', array( 'user' => $session_result['users_session_location_user']));
						$user_localization = $language -> getString( 'online_list_localisation_user');
							
					}else{
						
						$user_localization_url = ROOT_PATH;
						$user_localization = $language -> getString( 'online_list_localisation_main');
					
					}
					
				break;
				
				case 'profile':
					
					$user_localization_url = parent::systemLink( 'profile');
					$user_localization = $language -> getString( 'online_list_localisation_profile');
					
				break;
					
				case 'mod_cp':
					
					$user_localization_url = parent::systemLink( 'mod_cp');
					$user_localization = $language -> getString( 'online_list_localisation_mod_cp');
					
				break;
				
				case 'mod':
					
					$user_localization_url = parent::systemLink( 'mod');
					$user_localization = $language -> getString( 'online_list_localisation_mod_cp');
					
				break;
				
				case 'mail_user':
					
					if ( $session -> user['user_can_see_users_profiles'] && $session -> user['user_can_send_mails']){
						
						$language -> setKey( 'session_user', $session_result[24].$session_result[23].$session_result[25]);
						
						$user_localization_url = parent::systemLink( 'mail_user', array( 'user' => $session_result['users_session_location_user']));
						$user_localization = $language -> getString( 'online_list_localisation_mail_user');
								
					}else{
						
						$user_localization_url = ROOT_PATH;
						$user_localization = $language -> getString( 'online_list_localisation_main');
					
					}
					
				break;
				
				case 'guidelines':
					
					$user_localization_url = parent::systemLink( 'guidelines');
					$user_localization = $language -> getString( 'online_list_localisation_guidelines');
					
				break;
				
				case 'calendar':
					
					$user_localization_url = parent::systemLink( 'calendar');
					$user_localization = $language -> getString( 'online_list_localisation_calendar');
					
				break;
				
				case 'cal_event':
					
					$user_localization_url = parent::systemLink( 'calendar');
					$user_localization = $language -> getString( 'online_list_localisation_calendar_event');
					
				break;
				
				case 'cal_event_new':
					
					$user_localization_url = parent::systemLink( 'calendar');
					$user_localization = $language -> getString( 'online_list_localisation_calendar_event_new');
					
				break;
				
				case 'cal_event_edit':
					
					$user_localization_url = parent::systemLink( 'calendar');
					$user_localization = $language -> getString( 'online_list_localisation_calendar_event_edit');
					
				break;
				
				case 'cal_event_del':
					
					$user_localization_url = parent::systemLink( 'calendar');
					$user_localization = $language -> getString( 'online_list_localisation_calendar_event_delete');
					
				break;
				
				case 'help':
					
					$user_localization_url = parent::systemLink( 'help');
					$user_localization = $language -> getString( 'online_list_localisation_help');
					
				break;
				
				case 'team':
					
					$user_localization_url = parent::systemLink( 'team');
					$user_localization = $language -> getString( 'online_list_localisation_team');
					
				break;
				
				case 'forum':
					
					if ( $session -> canSeeTopics( $session_result['forum_name'])){
						
						$language -> setKey( 'session_forum', $session_result['forum_name']);
						
						$user_localization_url = parent::systemLink( 'forum', array( 'forum' => $session_result['users_session_location_forum']));
						$user_localization = $language -> getString( 'online_list_localisation_forum');
									
					}else{
						
						$user_localization_url = ROOT_PATH;
						$user_localization = $language -> getString( 'online_list_localisation_main');
					
					}
					
				break;
				
				case 'topic':
					
					if ( $session -> canSeeTopics( $session_result['topic_forum_id'])){
						
						$language -> setKey( 'session_topic', $session_result['topic_name']);
						
						$user_localization_url = parent::systemLink( 'topic', array( 'topic' => $session_result['users_session_location_topic']));
						$user_localization = $language -> getString( 'online_list_localisation_topic');
										
					}else{
						
						$user_localization_url = ROOT_PATH;
						$user_localization = $language -> getString( 'online_list_localisation_main');
					
					}
					
				break;
				
				case 'new_topic':
					
					if ( $session -> canSeeTopics( $session_result['forum_id'])){
						
						$language -> setKey( 'session_forum', $session_result['forum_name']);
						
						$user_localization_url = parent::systemLink( 'new_topic', array( 'forum' => $session_result['users_session_location_forum']));
						$user_localization = $language -> getString( 'online_list_localisation_new_topic');
											
					}else{
						
						$user_localization_url = ROOT_PATH;
						$user_localization = $language -> getString( 'online_list_localisation_main');
					
					}
					
				break;
				
				case 'edit_topic':
					
					if ( $session -> canSeeTopics( $session_result['topic_forum_id'])){
						
						$language -> setKey( 'session_topic', $session_result['topic_name']);
						
						$user_localization_url = parent::systemLink( 'edit_topic', array( 'topic' => $session_result['users_session_location_topic']));
						$user_localization = $language -> getString( 'online_list_localisation_edit_topic');
												
					}else{
						
						$user_localization_url = ROOT_PATH;
						$user_localization = $language -> getString( 'online_list_localisation_main');
					
					}
					
				break;
				
				case 'new_post':
					
					if ( $session -> canSeeTopics( $session_result['topic_forum_id'])){
					
						$language -> setKey( 'session_topic', $session_result['topic_name']);
						
						$user_localization_url = parent::systemLink( 'new_post', array( 'topic' => $session_result['users_session_location_topic']));
						$user_localization = $language -> getString( 'online_list_localisation_new_post');
													
					}else{
						
						$user_localization_url = ROOT_PATH;
						$user_localization = $language -> getString( 'online_list_localisation_main');
					
					}
					
				break;
				
				case 'edit_post':
										
					$user_localization_url = parent::systemLink( ROOT_PATH);
					$user_localization = $language -> getString( 'online_list_localisation_edit_post');
					
				break;
				
				case 'report_post':
										
					$user_localization_url = parent::systemLink( ROOT_PATH);
					$user_localization = $language -> getString( 'online_list_localisation_report_post');
					
				break;
				
				case 'download':
										
					$user_localization_url = ROOT_PATH;
					$user_localization = $language -> getString( 'online_list_localisation_download');
					
				break;
					
				case 'search':
										
					$user_localization_url = parent::systemLink( 'search');
					$user_localization = $language -> getString( 'online_list_localisation_search');
					
				break;
				
				case 'tags_cloud':
										
					$user_localization_url = parent::systemLink( 'tags_cloud');
					$user_localization = $language -> getString( 'online_list_localisation_tags_cloud');
					
				break;
				
				case 'online':
										
					$user_localization_url = parent::systemLink( 'online');
					$user_localization = $language -> getString( 'online_list_localisation_online');
					
				break;
				
				case 'style':
										
					$user_localization_url = ROOT_PATH;
					$user_localization = $language -> getString( 'online_list_localisation_style');
					
				break;
				
				case 'lang':
										
					$user_localization_url = ROOT_PATH;
					$user_localization = $language -> getString( 'online_list_localisation_lang');
					
				break;
				
				default:
					
					$user_localization_url = ROOT_PATH;
					$user_localization = $language -> getString( 'online_list_localisation_main');
					
				break;
				
			}
			
			/**
			 * agent
			 */
			
			if ( $session -> user['user_can_be_mod']){
				
				$agent = '<div class="funct_help">'.$session_result['users_session_agent'].'<br />ip: '.long2ip( $session_result['users_session_ip']).'</div>';
				
			}else{
				
				$agent = '';
				
			}
			
			/**
			 * insert row
			 */
			
			$sessions_tab -> addToContent( '<tr>
				<td class="opt_row1">'.$user_name.$agent.'</td>
				<td class="opt_row2"><a href="'.$user_localization_url.'">'.$user_localization.'</a></td>
				<td class="opt_row3">'.$time -> drawDate( $session_result['users_session_last_time']).'<div class="funct_help">'.$language -> getString( 'online_list_user_open').': '.$time -> drawDate( $session_result['users_session_open_time']).'</div></td>
			</tr>');
			
		}
		
		/**
		 * close table
		 */
		
		$sessions_tab -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'online_list'), $sessions_tab -> display()));
		
	}
	
}

?>