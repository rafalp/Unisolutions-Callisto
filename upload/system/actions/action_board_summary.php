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
|	Board summary
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class action_board_summary extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * begin from starting new form
		 */
		
		$board_summary_form = new form();	
		
		#if ( ALLOW_PLUGINS){				
		#	//run plugins
		#	$plugins_manager -> runPlugins( 'summary_beginning');
		#	
		#	$board_summary_form -> addToContent( $plugins_manager -> getPluginsHTML());
		#}
		
		/**
		 * last posts
		 */
		
		if ( $settings['forum_last_topics_show'] > 0){
			
			$found_topics = array();
			
			/**
			 * select rows
			 */
			
			$pre_proper_forums = $forums -> getForumsList();
			
			$proper_forums = array();
			
			foreach ( $pre_proper_forums as $forum_id => $forum_name){
				
				if ( $session -> canSeeTopics( $forum_id))
					$proper_forums[] = $forum_id;
				
			}
			
			if ( count( $proper_forums) > 0){
				
				$topics_query = $mysql -> query( "SELECT t.*, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix, su.user_main_group AS starter_main_group, su.user_other_groups AS starter_other_groups
				FROM topics t
				LEFT JOIN users su ON t.topic_start_user = su.user_id
				LEFT JOIN users u ON t.topic_last_user = u.user_id
				LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
				WHERE t.topic_forum_id IN (".join( ",", $proper_forums).") ORDER BY t.topic_last_time DESC LIMIT ".$settings['forum_last_topics_show']);
			
				while ( $topic_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
					
					//clear result
					$topic_result = $mysql -> clear( $topic_result);
					
					if ( $topic_result['topic_last_user'] == -1){
						
						$anwswer_author = $topic_result['users_group_prefix'].$topic_result['topic_last_user_name'].$topic_result['users_group_suffix'];
						$anwswer_author_closing = '';
	
					}else{
						
						$anwswer_author = '<a href="'.parent::systemLink( 'user', array( 'user' => $topic_result['user_id'])).'">'.$topic_result['users_group_prefix'].$topic_result['user_login'].$topic_result['users_group_suffix'].'</a>';

					}
					
					/**
					 * prefix
					 */
					
					$topic_prefix = $forums -> getPrefixHTML( $topic_result['topic_prefix'], $topic_result['topic_forum_id']);
					
					if ( strlen( $topic_prefix) == 0){
												
						if ( $topic_result['topic_type'] == 1){
							
							$topic_prefix = '<b>'.$settings['forum_stick_prefix'].':</b> ';
						
						}else if ($topic_result['topic_survey']){
							
							$topic_prefix = '<b>'.$settings['forum_survey_prefix'].':</b> ';
							
						}else{
							
							$topic_prefix = '';
							
						}
						
					}else{
						
						$topic_prefix .= ' ';
						
					}
						
					/**
					 * censore topic name
					 */
					
					$topic_author_groups = array();
					$topic_author_groups = split( ",", $topics_result['starter_other_groups']);
					$topic_author_groups[] = $topics_result['starter_main_group'];
					
					if ( !$users -> cantCensore( $topic_author_groups)){
						
						$topics_result['topic_name'] = $strings -> censore( $topics_result['topic_name']);
						$topic_info = $strings -> censore( $topic_info);
						
					}
								
					/**
					 * add topic to list
					 */
					
					$found_topics[] = '<a href="'.parent::systemLink('topic', array('topic' => $topic_result['topic_id'], 'p' => ceil(($topic_result['topic_posts_num'] + 1) / $settings[ 'forum_posts_per_page'] ))).'#post'.$topic_result['topic_last_post_id'].'" title="'.$topic_result['topic_name'].'">'.$style -> drawImage( 'goto', $language -> getString( 'topic_goto_last')).' '.$topic_prefix.$forums -> cutTopicName( $topic_result['topic_name']).'</a> - '.$anwswer_author.' ( '.$time -> drawDate( $topic_result['topic_last_time']).')';
					
				}
				
			}
			
			/**
			 * draw row
			 */
						
			$section_title = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
			    <td>'.$language -> getString( 'board_summary_newest_posts').'</td>
			    <td style="text-align: right"><a href="javascript:switchBlockDisplay(\'last_topics_div\', \'last_topics_img\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')"><div id="last_topics_img">'.$style -> drawImage( 'forum_collapse').'</div></a></td>
			  </tr>
			</table>';
			
			$board_summary_form -> drawSpacer( $section_title);
			$board_summary_form -> addToContent( '<div id="last_topics_div">');
			$board_summary_form -> openOpTable();
			
			if ( count( $found_topics) > 0){
				
				$board_summary_form -> addToContent( '<tr>
					<td class="opt_row3">'.$style -> drawImage( 'icon_stats', $language -> getString( 'board_summary_newest_posts')).'</td>
					<td class="opt_row2" style="width: 100%">'.join( "<br />", $found_topics).'</td>
				</tr>');
				
			}else{
				
				$board_summary_form -> addToContent( '<tr>
					<td class="opt_row3">'.$style -> drawImage( 'icon_stats', $language -> getString( 'board_summary_newest_posts')).'</td>
					<td class="opt_row2" style="width: 100%"><i>'.$language -> getString( 'board_summary_newest_posts_none').'</i></td>
				</tr>');
							
			}
			
			$board_summary_form -> closeTable();
			$board_summary_form -> addToContent( '</div>
			<script type="text/javascript">
							
				getBlockDisplayR(\'last_topics_div\', \'last_topics_img\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')
				
			</script>');
			
		}
				
		#if ( ALLOW_PLUGINS){				
		#	//run plugins
		#	$plugins_manager -> runPlugins( 'summary_after_posts');
		#	
		#	$board_summary_form -> addToContent( $plugins_manager -> getPluginsHTML());
		#}
		
		/**
		 * on-line section
		 */
		
		if ( $settings['users_count_online']){
			
		
			$users_to_query = $users -> users_on_line;
			
			/**
			 * set keys
			 */
						
			$language -> setKey( 'users_online_total', $users -> members_online_num + $users -> hidden_online_num + $users -> guests_online_num);
			$language -> setKey( 'users_online_members', $users -> members_online_num);
			$language -> setKey( 'users_online_hidden', $users -> hidden_online_num);
			$language -> setKey( 'users_online_guests', $users -> guests_online_num);

			$block_title = $language -> getString( 'board_summary_onlines_hidden');
					
			$board_summary_form -> drawSpacer( $block_title);
			
			$board_summary_form -> openOpTable();
			
			/**
			 * draw sessions list
			 */
			
			if ( $settings['draw_online_board']){
					
				/**
				 * define if we have anything to display
				 */
				
				if ( $users -> members_online_num > 0 || $users_to_query[$session -> user['user_id']] || ( $session -> user['user_see_hidden'] && ($users -> members_online_num + $users -> hidden_online_num) > 0)){
					
					$users_online = array();
					
					$users_ids_to_query = array();
					
					foreach ( $users_to_query as $user_online_id => $users_online_hidden){
						
						if ( ($session -> user['user_see_hidden'] && $users_online_hidden) || !$users_online_hidden || $user_online_id == $session -> user['user_id']){
							
							$users_ids_to_query[] = $user_online_id;
							
						}
						
					}
					
					$groups_stats = array();
					
					$users_query = $mysql -> query( "SELECT u.*, g.* FROM users u LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE u.user_id IN (".join( ', ', $users_ids_to_query).")");
										
					while ( $users_online_result = mysql_fetch_array( $users_query, MYSQL_ASSOC)){
						
						//clear result
						$users_online_result = $mysql -> clear( $users_online_result);
						
						$hidden = '';
						
						$groups_stats[ $users_online_result['user_main_group']] ++;
						
						if ( $users_to_query[ $users_online_result['user_id']])
							$hidden = "*";
						
						/**
						 * add to list
						 */
						
						$user_profile_link = array( 'user' => $users_online_result['user_id']);
						$users_online[] = '<a href="'.parent::systemLink( 'user', $user_profile_link).'">'.$users_online_result['users_group_prefix'].$users_online_result['user_login'].$users_online_result['users_group_suffix'].'</a>'.$hidden;
						
					}
					
					/**
					 * add bots, if we have any
					 */
					
					if ( $settings['spiders_draw_online']){
						
						$drawed_bots = array();
						
						foreach ( $users -> bots_on_line as $bot_name){
															
							$drawed_bots[$bot_name] ++;
													
						}
						
						foreach ( $drawed_bots as $bot_name => $bot_nums){
							
							if ( $bot_nums > 1){
								$users_online[] = '<span class="online_bot">'.$bot_name.' ('.$bot_nums.')</span>';
							}else{
								$users_online[] = '<span class="online_bot">'.$bot_name.'</span>';
							}
						}		
					}
					
					/**
					 * draw it all
					 */
					
					$section_content = join( ', ', $users_online);
					
					if ( $session -> user['user_see_hidden'] || $users_to_query[$session -> user['user_id']])
						$section_content .= '<div class="funct_help">'.$language -> getString( 'board_summary_onlines_legend').'</div>';
										
				}else{
					
					$section_content = '<i>'.$language -> getString( 'board_summary_onlines_empty').'</i>';
					
				}
				
				/**
				 * groups now
				 */
										
				$groups_online = array();
				
				foreach ( $users -> users_groups as $group_id => $group_ops){
					
					if ( !$group_ops['users_group_hidden']){
						
						if ( empty( $groups_stats[$group_id]))
							$groups_stats[$group_id] = 0;
						
						$groups_online[] = $group_ops['users_group_prefix'].$group_ops['users_group_name'].$group_ops['users_group_suffix'].' (<b>'.$groups_stats[$group_id].'</b>)';
						
					}
					
				}
				
				if ( count( $groups_online) > 0){
					
					$section_content .= '<hr />'.join( ", ", $groups_online);
					
				}
				
				/**
				 * and draw
				 */
				
				$board_summary_form -> addToContent( '<tr>
					<td class="opt_row3">'.$style -> drawImage( 'icon_users', $language -> getString( $section_title)).'</td>
					<td class="opt_row2" style="width: 100%">'.$section_content.'</td>
				</tr>');
				
			}
			
			$sublinks[] = '<a href="'.parent::systemLink( 'online').'">'.$language -> getString( 'board_summary_sessions').'</a>';
			
		}else{
			
			$board_summary_form -> openOpTable();
			
		}
		
		/**
		 * clear all
		 */
		
		$sublinks[] = '<a href="'.parent::systemLink( 'team').'">'.$language -> getString( 'board_summary_the_team').'</a>';
			
		$board_summary_form -> addToContent( '<tr>
			<td colspan="2" class="opt_row1" colspan="2" style="text-align: right">'.join( ' - ', $sublinks).'</td>
		</tr>');
		
		$board_summary_form -> closeTable();
		
		#if ( ALLOW_PLUGINS){				
		#	//run plugins
		#	$plugins_manager -> runPlugins( 'summary_after_onlines');
		#	
		#	$board_summary_form -> addToContent( $plugins_manager -> getPluginsHTML());
		#}
				
		/**
		 * birthdays
		 */
		
		if ( $settings['birthdays_show'] && $session -> user['user_can_see_users_profiles']){
			
			/**
			 * load birthdays from cache
			 */
			
			$today_birthdays = $cache -> loadCache( 'today_birthdays');
			
			if ( gettype( $today_birthdays) != 'array'){
				
				/**
				 * select birthdays from cache
				 */
				
				$birthdays_query = $mysql -> query( 'SELECT u.user_id, u.user_login, u.user_birth_date, g.users_group_prefix, g.users_group_suffix
				FROM users u
				LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
				WHERE u.user_birth_date LIKE "'.date('j', $time -> getTime()).'-'.date('n', $time -> getTime()).'-%" AND u.user_birth_date NOT LIKE "'.date('j', $time -> getTime()).'-'.date('n', $time -> getTime()).'-'.date('Y', $time -> getTime()).'"');
				
				$today_birthdays = array();
				
				while ( $birthdays_result = mysql_fetch_array( $birthdays_query, MYSQL_ASSOC)) {
					
					//clear result
					$birthdays_result = $mysql -> clear( $birthdays_result);
										
					$today_birthdays[$birthdays_result['user_id']] = array(
						'user_login' => $birthdays_result['user_login'],
						'user_birth_date' => $birthdays_result['user_birth_date'],
						'users_group_prefix' => $birthdays_result['users_group_prefix'],
						'users_group_suffix' => $birthdays_result['users_group_suffix']
					);
					
				}
				
				$cache -> saveCache( 'today_birthdays', $today_birthdays, ( 8 * 3600));
				
			}
			
			/**
			 * what to do
			 */
						
			$section_title = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
			    <td>'.$language -> getString( 'board_summary_birthdays').'</td>
			    <td style="text-align: right"><a href="javascript:switchBlockDisplay(\'birthdays_div\', \'birthdays_img\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')"><div id="birthdays_img">'.$style -> drawImage( 'forum_collapse').'</div></a></td>
			  </tr>
			</table>';	
			
			if ( !$settings['birthdays_hide_empty'] || ($settings['birthdays_hide_empty'] && count( $today_birthdays) > 0)){
				
				$board_summary_form -> drawSpacer( $section_title);
				$board_summary_form -> addToContent( '<div id="birthdays_div">');
				$board_summary_form -> openOpTable();
				
				if ( count( $today_birthdays) > 0){
					
					$births_to_draw = array();
					
					/**
					 * cycle trought birthdays
					 */
					
					foreach ( $today_birthdays as $user_id => $user_data){
						
						$user_birt = split( "-", $user_data['user_birth_date']);
						
						$user_age = date( 'Y') - $user_birt[2];
						
						$births_to_draw[] = '<a href="'.parent::systemLink( 'user', array( 'user' => $user_id)).'">'.$user_data['users_group_prefix'].$user_data['user_login'].$user_data['users_group_suffix'].'</a> (<b>'.$user_age.'</b>)';
						
					}
					
					/**
					 * draw births
					 */
					
					$board_summary_form -> addToContent( '<tr>
						<td class="opt_row3">'.$style -> drawImage( 'icon_date', $language -> getString( 'board_summary_birthdays')).'</td>
						<td class="opt_row2" style="width: 100%">'.join( ", ", $births_to_draw).'</td>
					</tr>');
					
				}else{
					
					$board_summary_form -> addToContent( '<tr>
						<td class="opt_row3">'.$style -> drawImage( 'icon_date', $language -> getString( 'board_summary_birthdays')).'</td>
						<td class="opt_row2" style="width: 100%"><i>'.$language -> getString( 'board_summary_birthdays_empty').'</i></td>
					</tr>');
					
				}
				
				$board_summary_form -> closeTable();
				$board_summary_form -> addToContent( '</div>
				<script type="text/javascript">
								
					getBlockDisplayR(\'birthdays_div\', \'birthdays_img\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')
					
				</script>');
			}
		
		}
		
		#if ( ALLOW_PLUGINS){				
		#	//run plugins
		#	$plugins_manager -> runPlugins( 'summary_after_birthdays');
		#	
		#	$board_summary_form -> addToContent( $plugins_manager -> getPluginsHTML());
		#}
				
		/**
		 * board events
		 */
		
		if ( $settings['calendar_turn'] && $settings['events_show']){
			
			/**
			 * load events from cache
			 */
			
			$today_events = $cache -> loadCache( 'today_events');
			
			if ( gettype( $today_events) != 'array'){
				
				/**
				 * select birthdays from cache
				 */
				
				$events_query = $mysql -> query( 'SELECT calendar_event_id, calendar_event_name
				FROM calendar_events
				WHERE (calendar_event_date LIKE "'.date('j', $time -> getTime()).'-'.date('n', $time -> getTime()).'-'.date('Y', $time -> getTime()).'" AND `calendar_event_repeat` = \'0\') OR 
				(calendar_event_date LIKE "%-'.date('n', $time -> getTime()).'-'.date('Y', $time -> getTime()).'" AND `calendar_event_repeat` = \'1\') OR
				(calendar_event_date LIKE "%-%-'.date('Y', $time -> getTime()).'" AND `calendar_event_repeat` = \'2\')');
				
				$today_events = array();
				
				while ( $events_result = mysql_fetch_array( $events_query, MYSQL_ASSOC)) {
					
					//clear result
					$events_result = $mysql -> clear( $events_result);
										
					$today_events[$events_result['calendar_event_id']] = $events_result['calendar_event_name'];
					
				}
				
				$cache -> saveCache( 'today_events', $today_events, ( 8 * 3600));
				
			}
			
			/**
			 * check empty
			 */
						
			$section_title = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
			    <td>'.$language -> getString( 'board_summary_events').'</td>
			    <td style="text-align: right"><a href="javascript:switchBlockDisplay(\'calendar_events_today_div\', \'calendar_events_today_img\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')"><div id="calendar_events_today_img">'.$style -> drawImage( 'forum_collapse').'</div></a></td>
			  </tr>
			</table>';	
			
			if ( count( $today_events) > 0){
				
				$events_today_to_draw = array();
				
				foreach ( $today_events as $event_id => $event_name){
					
					$events_today_to_draw[] = '<a href="'.parent::systemLink( 'cal_event', array( 'event' => $event_id)).'">'.$event_name.'</a>';
					
				}
				
				$board_summary_form -> drawSpacer( $section_title);
				$board_summary_form -> addToContent( '<div id="calendar_events_today_div">');
				$board_summary_form -> openOpTable();
				
				$board_summary_form -> addToContent( '<tr>
					<td class="opt_row3">'.$style -> drawImage( 'icon_date', $language -> getString( 'board_summary_events')).'</td>
					<td class="opt_row2" style="width: 100%">'.join( ", ", $events_today_to_draw).'</td>
				</tr>');
				
				$board_summary_form -> closeTable();
				$board_summary_form -> addToContent( '</div>
				<script type="text/javascript">
								
					getBlockDisplayR(\'calendar_events_today_div\', \'calendar_events_today_img\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')
					
				</script>');
				
			}
			
			if ( count( $today_events) == 0 && !$settings['events_hide_empty']){
				
				$board_summary_form -> drawSpacer( $section_title);
				$board_summary_form -> addToContent( '<div id="calendar_events_today_div">');
				$board_summary_form -> openOpTable();
				
				$board_summary_form -> addToContent( '<tr>
					<td class="opt_row3">'.$style -> drawImage( 'icon_date', $language -> getString( 'board_summary_events')).'</td>
					<td class="opt_row2" style="width: 100%"><i>'.$language -> getString( 'board_summary_events_empty').'</i></td>
				</tr>');
				
				$board_summary_form -> closeTable();
				$board_summary_form -> addToContent( '</div>
				<script type="text/javascript">
								
					getBlockDisplayR(\'calendar_events_today_div\', \'calendar_events_today_img\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')
					
				</script>');
				
			}
			
		}
			
		#if ( ALLOW_PLUGINS){				
		#	//run plugins
		#	$plugins_manager -> runPlugins( 'summary_after_actual_events');
		#	
		#	$board_summary_form -> addToContent( $plugins_manager -> getPluginsHTML());
		#}
		
		/**
		 * coming_events
		 */
			
		if ( $settings['calendar_turn'] && $settings['events_show'] && $settings['events_time_limit'] > 0){
			
			$coming_events = $cache -> loadCache( 'coming_events');

			if ( gettype( $coming_events) != 'array'){
				
				/**
				 * create events conditions
				 */
				
				$events_conditions = array();
				
				/**
				 * check years
				 */
				
				$timing = $settings['events_time_limit'];

				if ( $timing > 366)
					$timing = 366;

				$actual_time = 0;
								
				while ( $actual_time <= $timing) {
											
					$actual_time ++;
					
					$events_conditions[] = '( calendar_event_date LIKE "'.(date( 'j', $time -> getTime() + ( $actual_time * 24 * 3600))).'-'.(date( 'n', $time -> getTime() + ( $actual_time * 24 * 3600))).'-'.(date( 'Y', $time -> getTime() + ( $actual_time * 24 * 3600))).'" AND calendar_event_repeat = \'0\')
					OR ( calendar_event_date LIKE "'.(date( 'j', $time -> getTime() + ( $actual_time * 24 * 3600))).'-'.(date( 'n', $time -> getTime() + ( $actual_time * 24 * 3600))).'-%" AND calendar_event_repeat = \'1\')
					OR ( calendar_event_date LIKE "'.(date( 'j', $time -> getTime() + ( $actual_time * 24 * 3600))).'-%-%" AND calendar_event_repeat = \'2\')';
					
				}
				
				
				/**
				 * select events from cache
				 */
				
				$events_query = $mysql -> query( 'SELECT calendar_event_id, calendar_event_name
				FROM calendar_events
				WHERE '.join(" OR ", $events_conditions));
				
				$coming_events = array();
				
				while ( $events_result = mysql_fetch_array( $events_query, MYSQL_ASSOC)) {
					
					//clear result
					$events_result = $mysql -> clear( $events_result);
										
					$coming_events[$events_result['calendar_event_id']] = $events_result['calendar_event_name'];
					
				}
				
				$cache -> saveCache( 'coming_events', $coming_events, ( 8 * 3600));
				
			}
			
			/**
			 * check empty
			 */
			
			$language -> setKey( 'coming_time', $settings['events_time_limit']);
					
			$section_title = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
			    <td>'.$language -> getString( 'board_summary_events_coming').'</td>
			    <td style="text-align: right"><a href="javascript:switchBlockDisplay(\'calendar_events_coming_div\', \'calendar_events_coming_img\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')"><div id="calendar_events_coming_img">'.$style -> drawImage( 'forum_collapse').'</div></a></td>
			  </tr>
			</table>';		
			
			if ( count( $coming_events) > 0){
				
				$events_coming_to_draw = array();
				
				foreach ( $coming_events as $event_id => $event_name){
					
					$events_coming_to_draw[] = '<a href="'.parent::systemLink( 'cal_event', array( 'event' => $event_id)).'">'.$event_name.'</a>';
					
				}
				
				$board_summary_form -> drawSpacer( $section_title);
				$board_summary_form -> addToContent( '<div id="calendar_events_coming_div">');
				$board_summary_form -> openOpTable();
				
				$board_summary_form -> addToContent( '<tr>
					<td class="opt_row3">'.$style -> drawImage( 'icon_date', $language -> getString( 'board_summary_events_coming')).'</td>
					<td class="opt_row2" style="width: 100%">'.join( ", ", $events_coming_to_draw).'</td>
				</tr>');
				
				$board_summary_form -> closeTable();
				$board_summary_form -> addToContent( '</div>
				<script type="text/javascript">
								
					getBlockDisplayR(\'calendar_events_coming_div\', \'calendar_events_coming_img\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')
					
				</script>');
				
			}
			
			if ( count( $coming_events) == 0 && !$settings['events_hide_empty']){
				
				$board_summary_form -> drawSpacer( $section_title);
				$board_summary_form -> addToContent( '<div id="calendar_events_coming_div">');
				$board_summary_form -> openOpTable();
				
				$board_summary_form -> addToContent( '<tr>
					<td class="opt_row3">'.$style -> drawImage( 'icon_date', $language -> getString( 'board_summary_events_coming')).'</td>
					<td class="opt_row2" style="width: 100%"><i>'.$language -> getString( 'board_summary_events_empty_coming').'</i></td>
				</tr>');
				
				$board_summary_form -> closeTable();
				$board_summary_form -> addToContent( '</div>
				<script type="text/javascript">
								
					getBlockDisplayR(\'calendar_events_coming_div\', \'calendar_events_coming_img\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')
					
				</script>');
			
			}
						
		}
		
		#if ( ALLOW_PLUGINS){				
		#	//run plugins
		#	$plugins_manager -> runPlugins( 'summary_after_coming_events');
		#	
		#	$board_summary_form -> addToContent( $plugins_manager -> getPluginsHTML());
		#}
		
		/**
		 * board statistics
		 */
		
		$board_summary_form -> drawSpacer( $language -> getString( 'board_summary_title'));
			
		$board_summary_form -> openOpTable();
		
		/**
		 * main counting line
		 */
		
		$language -> setKey( 'board_threads_total', $settings['board_threads_total']);
		$language -> setKey( 'board_posts_total', $settings['board_posts_total']);
		
		$stats_content[] = $language -> getString( 'board_summary_posts_total');
		
		$language -> setKey( 'board_members_num', $settings['users_num']);
		
		$stats_content[] = $language -> getString( 'board_summary_users_total');
			
		/**
		 * last member
		 */
		
		$last_member_query = $mysql -> query( "SELECT u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM users u LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE u.user_id > '0' ORDER BY u.user_regdate DESC LIMIT 1");

		if ( $last_member_result = mysql_fetch_array( $last_member_query, MYSQL_ASSOC)){
			
			$last_member_result = $mysql -> clear( $last_member_result);
			
			$last_member_id = $last_member_result['user_id'];
			$last_member_name = $last_member_result['users_group_prefix'].$last_member_result['user_login'].$last_member_result['users_group_suffix'];
			
		}
		
		$user_profile_link = array( 'user' => $last_member_id);
		
		$stats_content[] = $language -> getString( 'board_summary_last_member').' <a href="'.parent::systemLink( 'user', $user_profile_link).'">'.$last_member_name.'</a>';
			
		/**
		 * record
		 */

		if ( isset( $settings['users_count_online'])){
		
			$language -> setKey( 'record_num', $settings['record_number']);
			$language -> setKey( 'record_time', $time -> drawDate( $settings['record_time']));
			
			$stats_content[] = $language -> getString( 'board_summary_record');
			
		}
		
		/**
		 * display section
		 */
		
		$board_summary_form -> addToContent( '<tr>
			<td class="opt_row3">'.$style -> drawImage( 'icon_stats', $language -> getString( 'board_summary_title')).'</td>
			<td class="opt_row2" style="width: 100%">'.join( '<br />', $stats_content).'</td>
		</tr>');
		
		if ( $session -> user['user_id'] != -1)
			$subactions[] = '<a href="'.parent::systemLink( 'mark_read').'">'.$language -> getString( 'board_summary_mark_all_read').'</a>';
			
		$subactions[] = '<a href="'.parent::systemLink( 'flush_cookies').'">'.$language -> getString( 'board_summary_delete_cookies').'</a>';
			
			
		$board_summary_form -> addToContent( '<tr>
			<td colspan="2" class="opt_row1" colspan="2" style="text-align: right">'.join( ' - ', $subactions).'</td>
		</tr>');
		
		
		$board_summary_form -> closeTable();
		
		#if ( ALLOW_PLUGINS){				
		#	//run plugins
		#	$plugins_manager -> runPlugins( 'summary_after_statistics');
		#	
		#	$board_summary_form -> addToContent( $plugins_manager -> getPluginsHTML());
		#}
		
		/**
		 * legend form
		 */
		
		if ( $settings['forums_draw_legend']){
			
			$section_title = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
			    <td>'.$language -> getString( 'board_summary_legend').'</td>
			    <td style="text-align: right"><a href="javascript:switchBlockDisplay(\'forums_legend_div\', \'forums_legend_img\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')"><div id="forums_legend_img">'.$style -> drawImage( 'forum_collapse').'</div></a></td>
			  </tr>
			</table>';
			
			$board_summary_form -> drawSpacer( $section_title);
			$board_summary_form -> addToContent( '<div id="forums_legend_div">');
			$board_summary_form -> openOpTable();
			
			$board_summary_form -> drawRow( '<table style="width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 5px;">
				<tr>
					<td>'.$style -> drawImage( 'forum').' '.$language -> getString( 'forum_no_new_posts').'</td>
					<td>'.$style -> drawImage( 'forum_closed').' '.$language -> getString( 'forum_no_new_posts_closed').'</td>
					<td>'.$style -> drawImage( 'forum_redirect').' '.$language -> getString( 'forum_url').'</td>
				</tr>
				<tr>
					<td>'.$style -> drawImage( 'forum_new').' '.$language -> getString( 'forum_new_posts').'</td>
					<td>'.$style -> drawImage( 'forum_closed_new').' '.$language -> getString( 'forum_new_posts_closed').'</td>
					<td>'.$style -> drawImage( 'forum_category').' '.$language -> getString( 'forum_category').'</td>
				</tr>
			</table>');
			
			$board_summary_form -> closeTable();
			$board_summary_form -> addToContent( '</div>
			<script type="text/javascript">
							
				getBlockDisplayR(\'forums_legend_div\', \'forums_legend_img\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')
				
			</script>');
			
			
		}
		
		#if ( ALLOW_PLUGINS){				
		#	//run plugins
		#	$plugins_manager -> runPlugins( 'summary_after_legend');
		#	
		#	$board_summary_form -> addToContent( $plugins_manager -> getPluginsHTML());
		#}
		
		/**
		 * display
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'board_summary_title'), $board_summary_form -> display()));
				
	}
	
}

?>