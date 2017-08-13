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
|	Show topic
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

class action_show_post extends action{
		
	/**
	 * this forum id
	 *
	 */
	
	var $post_to_show;
	
	/**
	 * topic result
	 *
	 */
	
	var $post_result = array();
	
	/**
	 * forum result
	 *
	 */
	
	var $forum_result = array();
	
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check, if post exists
		 */
		
		$post_to_show = $_GET['post'];
		
		settype( $post_to_show, 'integer');
		
		if ( $post_to_show < 0){
			
			$post_to_show = 0;
			
		}
		
		$this -> post_to_show = $post_to_show;
		
		$posts_query = $mysql -> query( "SELECT p.*, t.*, u.*, f.*, g.users_group_name, g.users_group_title, g.users_group_prefix, g.users_group_suffix, mu.user_id AS editor_user_id, mu.user_login AS editor_user_login, mu.user_main_group AS editor_user_main_group, mu.user_other_groups AS editor_user_other_groups, mg.users_group_prefix AS editor_users_group_prefix, mg.users_group_suffix AS editor_users_group_suffix
		FROM posts p
		LEFT JOIN topics t ON p.post_topic = t.topic_id
		LEFT JOIN users u ON p.post_author = u.user_id
		LEFT JOIN profile_fields_data f ON u.user_id = f.profile_fields_user
		LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
		LEFT JOIN users mu ON p.post_last_editor = mu.user_id
		LEFT JOIN users_groups mg ON mu.user_main_group = mg.users_group_id
		WHERE p.post_id = '$post_to_show'");
		
		if ( $post_result = mysql_fetch_array( $posts_query, MYSQL_ASSOC)){
			
			//clear result
			$post_result = $mysql -> clear( $post_result);
			
			$this -> post_result = $post_result;
			
			/**
			 * check, if we can read topic
			 */
			
			if ( $session -> canSeeTopics( $post_result['topic_forum_id'])){
			
				/**
				 * select forum
				 */
				
				$forum_query = $mysql -> query( "SELECT * FROM forums WHERE `forum_id` = '".$post_result['topic_forum_id']."'");
				
				if ( $forum_result = mysql_fetch_array( $forum_query, MYSQL_ASSOC)){
					
					//clear result
					$forum_result = $mysql -> clear( $forum_result);
					
					$this -> forum_result = $forum_result;
					
					/**
					 * select topic read
					 */

					$topic_read_query = $mysql -> query( "SELECT * FROM topics_reads WHERE `topic_read_forum` = '".$forum_result['forum_id']."' AND `topic_read_topic` = '".$topic_to_show."' AND `topic_read_user` = '".$session -> user['user_id']."'");
					
					if ( $topic_read_result = mysql_fetch_array( $topic_read_query, MYSQL_ASSOC)){
						
						$topic_read = $topic_read_result['topic_read_time'];
						
					}else{
						
						$topic_read = 0;
						
					}
					
					/**
					 * draw path to our topic
					 */
					
					$curren_position = $forum_result['forum_id'];

					if ( $curren_position != 0){
							
						while ( $curren_position != 0){
							
							/**
							 * add tree element
							 */
							
							$path_elements[$forums -> forums_list[$curren_position]['forum_name']] = parent::systemLink( 'forum', array( 'forum' => $curren_position));
							
							/**
							 * jump to next element
							 */
							
							$curren_position = $forums -> forums_list[$curren_position]['forum_parent'];
							
						}
						
						$path_elements = array_reverse( $path_elements);
						
						foreach ( $path_elements as $path_element_name => $path_element_link)
							$path -> addBreadcrumb( $path_element_name, $path_element_link);
						
					}
					
					$topic_prefix = $forums -> getPrefixHTML( $post_result['topic_prefix'], $post_result['topic_forum_id']);
					
					if ( strlen( $topic_prefix) == 0){
												
						if ( $post_result['topic_type'] == 1){
							
							$topic_prefix = '<b>'.$settings['forum_stick_prefix'].':</b> ';
							$title_prefix = $settings['forum_stick_prefix'].': ';
						
						}else if ($post_result['topic_survey']){
							
							$topic_prefix = '<b>'.$settings['forum_survey_prefix'].':</b> ';
							$title_prefix = $settings['forum_survey_prefix'].': ';
							
						}else{
							
							$topic_prefix = '';
							$title_prefix = '';
							
						}
						
					}else{
						
						$topic_prefix .= ' ';
						$title_prefix = $forums -> getPrefixName( $post_result['topic_prefix'], $post_result['topic_forum_id']).': ';
											
					}
					
					$path -> addBreadcrumb( $topic_prefix.$post_result['topic_name'], parent::systemLink( 'topic', array( 'topic' => $post_result['topic_id'])));
										
					$path -> addBreadcrumb( $language -> getString( 'single_post_title'), parent::systemLink( 'post', array( 'post' => $post_result['post_id'])));
					
					/**
					 * set page title
					 */
					
					$output -> setTitle( $language -> getString( 'single_post_title').' '.$title_prefix.$post_result['topic_name']);
					
					if ( $session -> user['user_id'] == -1 && $settings['guest_limit_reads'] > 0){
						
						$reads_actual = getUniCookie( 'topics_reads');
						settype( $reads_actual, 'integer');
						$reads_actual ++;
						
						if ( $reads_actual < 1)
							$reads_actual = 1;
						
					}
					
					if ( $session -> user['user_id'] != -1 || $session -> user['user_is_bot'] || $settings['guest_limit_reads'] <= 0 || $settings['guest_limit_reads'] >= $reads_actual){
					
						/**
						 * actions
						 */
						
						$topic_actions = array();
											
						/**
						 * check classic reply
						 */
											
						if ( $session -> canReplyTopics( $forum_result['forum_id'])){
							
							if ( !$forum_result['forum_locked'] && !$post_result['topic_closed']){
								
								$topic_actions[] = '<a href="'.parent::systemLink( 'new_post', array( 'topic' => $post_result['topic_id'])).'">'.$style -> drawImage( 'button_reply_topic', $language -> getString( 'write_new_post')).'</a>';
							
							}else if ( $session -> user['user_avoid_closed_topics'] || $session -> isMod( $forum_result['forum_id']) ){
								
								$topic_actions[] = '<a href="'.parent::systemLink( 'new_post', array( 'topic' => $post_result['topic_id'])).'">'.$style -> drawImage( 'button_closed_topic', $language -> getString( 'write_new_post')).'</a>';
							
							}else{
								
								$topic_actions[] = $style -> drawImage( 'button_closed_topic', $language -> getString( 'write_new_post'));
							
							}
							
						}
						
						if ( $forum_result['forum_locked']){
								
							/**
							 * forum is locked, check if we have perms
							 */
							
							if ( $session -> user['user_avoid_closed_topics']){
								
								$topic_actions[] = '<a href="'.parent::systemLink( 'new_topic', array( 'forum' => $post_result['topic_forum_id'])).'">'.$style -> drawImage( 'button_closed_topic', $language -> getString( 'topics_new_button')).'</a>';
								
							}else{
							
								$topic_actions[] = $style -> drawImage( 'button_closed_topic', $language -> getString( 'topics_new_button_locked'));
							
							}
							
						}else{
	
							$topic_actions[] = '<a href="'.parent::systemLink( 'new_topic', array( 'forum' => $forum_result['forum_id'])).'">'.$style -> drawImage( 'button_new_topic', $language -> getString( 'topics_new_button')).'</a>';
						
						}
						
						/**
						 * post number and return
						 */
							
						$post_number = 1 + $mysql -> countRows( 'posts', "`post_topic` = '".$post_result['topic_id']."' && `post_time` < '".$post_result['post_time']."'");
						
						$return_link = '<a href="'.parent::systemLink( 'topic', array( 'topic' => $post_result['topic_id'], 'p' => ceil( $post_number / $settings['forum_posts_per_page']))).'#post'.$post_result['post_id'].'">'.$language -> getString( 'single_post_return').'</a>';
						
						/**
						 * draw options
						 */
						
						parent::draw('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 6px;">
						  <tr>
						    <td style="text-align: left">'.$return_link.'</td>
						    <td style="text-align: right">'.join( " ", $topic_actions).'</td>
						  </tr>
						</table>');
											
						/**
						 * done, lets start drawing
						 */
							
						$topic_posts = new form();
						$topic_posts -> openOpTable();	
						
						/**
						 * draw post
						 */
						
						//var_dump( $post_result);
						/**
						 * set clears
						 */
						
						$post_draw_parts = array();
						$post_draw_strings = array();
						
						/**
						 * post groups
						 */
						
						$sender_groups = array();
						$sender_groups = split( ",", $post_result['user_other_groups']);
						
						$sender_groups[] = $post_result['user_main_group'];
						
						if ( !defined( 'SIMPLE_MODE')){
											
							/**
							 * user is guest?
							 */
							
							if ( $post_result['post_author'] == -1){
								
								$post_draw_parts['member'] = false;
								
							}else{
								
								$post_draw_parts['member'] = true;
								
							}
							
							/**
							 * message info
							 */
							
							if ( $post_result['post_time'] > $topic_read){
								$msg_image = $style -> drawImage( 'small_forum_new', $language -> getString( 'user_cp_section_messenger_msgs_list_unread'));
							}else{
								$msg_image = $style -> drawImage( 'small_forum', $language -> getString( 'user_cp_section_messenger_msgs_list_read'));
							}
							
							if ( $session -> user['user_id'] == -1)
								$msg_image = $style -> drawImage( 'small_forum', $language -> getString( 'user_cp_section_messenger_msgs_list_read'));
							
							$message_info_title = $language -> getString('user_cp_section_messenger_msgs_received_date').': '.$time -> drawDate( $post_result['post_time']);				
							
							/**
							 * post info
							 */
							
							$post_info_params = array();
							
							/**
							 * user ip
							 */
							
							if ( $session -> isMod( $forum_result['forum_id']) || ($session -> user['user_id'] == $post_result['post_author'] && $session -> user['user_id'] != -1)){
								
								$post_info_params[] = 'ip: '.long2ip( $post_result['post_ip']);
								$post_info_params[] = 'agent: '.$post_result['post_user_agent'];
								
							}
							
							/**
							 * rate post
							 */
									
							if ( $settings['reputation_turn'] && $session -> user['user_id'] != -1 && $session -> user['user_id'] != $post_result['post_author'] && $post_result['post_author'] != -1){
							
								$post_info_params[] = '<a href="'.parent::systemLink( 'rate_post', array( 'post' => $post_result['post_id'], 'd' => 0)).'">'.$style -> drawImage( 'minus', $language -> getString( 'rate_post_m')).'</a> <a href="'.parent::systemLink( 'rate_post', array( 'post' => $post_result['post_id'], 'd' => 1)).'">'.$style -> drawImage( 'plus', $language -> getString( 'rate_post_p')).'</a>';
														
							}
							
							/**
							 * postnum
							 */
							
							$post_info_params[] = '#'.$post_number;
													
							$post_draw_strings['INFO'] = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
														  <tr>
														    <td style="text-align: left">'.$msg_image.' '.$message_info_title.'</td>
														    <td style="text-align: right">'.join( " | ", $post_info_params).'</td>
														  </tr>
														</table>';
							
							/**
							 * begin from author
							 */
							
							if ( $post_result['post_author'] == -1){
							
								/**
								 * author is deleted
								 */
								
								$post_draw_parts['author_status'] = false;
								
								$post_draw_strings['AUTHOR_NAME'] = '<a name="post'.$post_result['post_id'].'" id="post'.$post_result['post_id'].'">'.$post_result['users_group_prefix'].$post_result['post_author_name'].$post_result['users_group_suffix'].'</a>';
								
							}else{
								
								/**
								 * author exists
								 */
								
								if ( $settings['users_count_online']){
								
									$post_draw_parts['author_status'] = true;
								
									$post_draw_strings['AUTHOR_STATUS_IMG'] = $style -> drawStatus( $users -> checkOnLine( $post_result['post_author']));
									
								}else{
									
									$post_draw_parts['author_status'] = false;
									
								}
								
								/**
								 * and user login
								 */
								
								$post_draw_strings['AUTHOR_NAME'] = '<a name="post'.$post_result['post_id'].'" id="post'.$post_result['post_id'].'">'.'<a href="'.parent::systemLink( 'user', array( 'user' => $post_result['post_author'])).'">'.$post_result['users_group_prefix'].$post_result['user_login'].$post_result['users_group_suffix'].'</a></a>';				
								
							}
							
							/**
							 * author profile
							 */
							
							if ( $post_result['user_avatar_type'] != 0 && $settings['users_can_avatars'] && $session -> user['user_show_avatars']){
								
								$post_draw_parts['avatar'] = true;
								
								/**
								 * draw avatar
								 */
								
								$post_draw_strings['AUTHOR_AVATAR'] = $users -> drawAvatar( $post_result['user_avatar_type'], $post_result['user_avatar_image'], $post_result['user_avatar_width'], $post_result['user_avatar_height']);				
								
							}else{
								
								$post_draw_parts['avatar'] = false;
								
							}
							
							/**
							 * title
							 */
							
							if ( strlen( $post_result['user_custom_title']) > 0  && $settings['users_posts_to_title'] > 0 && $post_result['user_posts_num'] >= $settings['users_posts_to_title']){
									
								$post_draw_strings['AUTHOR_TITLE'] = $post_result['user_custom_title'];
								
							}else if ( strlen( $post_result['users_group_title']) > 0){
								
								$post_draw_strings['AUTHOR_TITLE'] = $post_result['users_group_title'];
								
							}else{
								
								$post_draw_strings['AUTHOR_TITLE'] = $users -> drawRankName( $post_result['user_posts_num']);
													
							}
							
							if ( strlen( $users -> users_groups[ $post_result['user_main_group']]['users_group_image']) > 0){
								$post_draw_strings['AUTHOR_RANK'] = '<img src="'.$users -> users_groups[$post_result['user_main_group']]['users_group_image'].'" alt="" title""/>';						
							}else{
								$post_draw_strings['AUTHOR_RANK'] = $users -> drawRankImage( $post_result['user_posts_num']);
							}
							
							$post_draw_strings['PROFILE_GROUP'] = $language -> getString( 'user_group');
							$post_draw_strings['USER_GROUP'] = $post_result['users_group_prefix'].$post_result['users_group_name'].$post_result['users_group_suffix'];
							
							$post_draw_strings['PROFILE_POSTS'] = $language -> getString( 'user_posts');
							$post_draw_strings['USER_POSTS'] = $post_result['user_posts_num'];
							
							/**
							 * check, if we can draw counter
							 */
							
							switch ( $settings['posts_counters_draw']){
								
								case '0':
									
									$post_draw_parts['posts'] = true;
									
								break;
								
								case '1':
									
									if ( $forum_result['forum_increase_counter']){
									
										$post_draw_parts['posts'] = true;
									
									}else{
	
										$post_draw_parts['posts'] = false;
										
									}
									
								break;
								
								case '2':
									
									$post_draw_parts['posts'] = false;
																
								break;
							}
							
							if ( strlen( $post_result['user_localisation']) != 0){
								
								$post_draw_parts['author_location'] = true;
								$post_draw_strings['PROFILE_LOCATION'] = $language -> getString( 'user_localisation');
								$post_draw_strings['USER_LOCATION'] = $post_result['user_localisation'];
							
							}else{
								
								$post_draw_parts['author_location'] = false;
								
							}
													
							$post_draw_strings['PROFILE_JOIN_DATE'] = $language -> getString( 'user_registration');
							$post_draw_strings['USER_JOIN_DATE'] = $time -> drawDate( $post_result['user_regdate']);
											
							/**
							 * reputation
							 */
							
							if ( $settings['reputation_turn']){
								
								$post_draw_parts['reps'] = true;
								
								$post_draw_strings['PROFILE_REPUTATION'] = $language -> getString( 'user_reputation');
								
								$post_draw_strings['USER_REPUTATION'] = $users -> drawReputation( $users -> countReputation( $post_result['user_rep'], $post_result['user_posts_num'], $post_result['user_regdate']));
								
							}else{
								
								$post_draw_parts['reps'] = false;
								
							}
							
							/**
							 * and thanks
							 */
							
							if ( $settings['reputation_turn'] && $post_result['post_thanked']){
								
								$post_draw_parts['thanks'] = true;
								$post_draw_strings['SHOW_THANKS'] = '<a href="'.parent::systemLink( 'show_reps', array( 'post' => $post_result['post_id'])).'">'.$language -> getString( 'post_thanked').'</a>';
																
							}else{
								
								$post_draw_parts['thanks'] = false;
								
							}
							
							/**
							 * warns
							 */
									
							if ( $settings['warns_turn'] && $post_result['post_author'] != -1){
								
								/**
								 * check if we can see warns
								 */
								
								if ( $settings['warns_show'] == 0 || ( $settings['warns_show'] == 1 && $session -> user['user_id'] != -1) || ( $settings['warns_show'] == 2 && ($session -> user['user_can_be_mod'] || ($session -> user['user_id'] != -1 && $session -> user['user_id'] == $post_result['post_author'])))){
								
									/**
									 * draw warns
									 */
									
									$post_draw_parts['warns'] = true;
									
									$post_draw_strings['PROFILE_WARNS'] = $language -> getString( 'user_warns');							
									
									if ( $post_result['user_warns'] > 0){
										
										$warns_link_open = '<a href="'.parent::systemLink( 'user_warns', array( 'user' => $post_result['user_id'])).'">';
										$warns_link_close = '</a>';
										
									}else{
										
										$warns_link_open = '';
										$warns_link_close = '';
										
									}
									
									/**
									 * draw warns, or wanrs + mod?
									 */
									
									if ( $session -> user['user_can_be_mod']){
										
										$post_draw_strings['USER_WARNS'] = '<a href="'.parent::systemLink( 'mod', array( 'user' => $post_result['post_author'], 'd' => '1')).'" title="'.$language -> getString( 'user_warn_decrease').'">'.$style -> drawImage( 'minus').'</a> '.$warns_link_open.$users -> drawWarnLevel( $post_result['user_warns']).$warns_link_close.' <a href="'.parent::systemLink( 'mod', array( 'user' => $post_result['post_author'], 'd' => '0')).'" title="'.$language -> getString( 'user_warn_add').'">'.$style -> drawImage( 'plus').'</a>';
										
									}else{
										
										$post_draw_strings['USER_WARNS'] = $warns_link_open.$users -> drawWarnLevel( $post_result['user_warns']).$warns_link_close;
																	
									}
									
								}else{
									
									/**
									 * cant see warns
									 */
									
									$post_draw_parts['warns'] = false;
								
								}
								
							}else{
								
								$post_draw_parts['warns'] = false;
								
							}
								
							/**
							 * custom fields
							 */
							
							$drawed_custom_fields = '';
							
							if ( $post_result['user_id'] != -1){
								
								if ( count( $users -> custom_fields) > 0){
									
									foreach ( $users -> custom_fields as $field_id => $field_ops) {
										
										if ( strlen( $post_result['field_'.$field_id]) > 0 && $field_ops['profile_field_inposts'] && (!$field_ops['profile_field_private'] || ( $field_ops['profile_field_private'] && ( $post_result['user_id'] == $session -> user['user_id'] || $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate'])))){
											
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
												$field_template = str_ireplace( '{VALUE}', $made_options[$post_result['field_'.$field_id]], $field_template);
												
											}else{
												
												$field_template = str_ireplace( '{VALUE}', $post_result['field_'.$field_id], $field_template);
																		
											}
																	
											$drawed_custom_fields .= '<br />'.$field_template;
											
										}
										
									}
									
								}
								
							}
							
							$post_draw_strings['FIELDS'] = $drawed_custom_fields;
							
							/**
							 * signature
							 */
							
							if ( strlen( $post_result['user_signature']) > 0 && $session -> user['user_show_sigs'] && $settings['users_can_sigs']){
								
								$post_draw_parts['signature'] = true;
								$post_draw_strings['SIGNATURE'] = $strings -> parseBB( nl2br( $post_result['user_signature']), $settings['users_allow_bbcodes_in_sigs'], $settings['users_allow_emoticones_in_sigs']);
							
							}else{
								
								$post_draw_parts['signature'] = false;
									
							}
							
							/**
							 * any edits?
							 */
							
							if ( $post_result['post_last_edit'] > 0){
								
								/**
								 * check legend
								 */
								
								$editor_groups = array();
								$editor_groups = split( ",", $post_result['editor_user_other_groups']);
								$editor_groups[] = $post_result['editor_user_main_group'];
								
								$drawLegend = $users -> checkEditLegend($editor_groups);
								
								/**
								 * we will draw legend always if:
								 * there is edit reason
								 * show legend
								 */
								
								if ( $drawLegend || strlen( $post_result['post_edit_message']) > 0){
								
									/**
									 * draw message
									 */
										
									$post_draw_parts['edit'] = true;
									
									$post_draw_parts['edit_info'] = true;
									
									$language -> setKey( 'post_edit_time', $time -> drawDate( $post_result['post_last_edit']));
									$language -> setKey( 'post_edit_num', $post_result['post_edits']);
									
									/**
									 * post editor
									 */
	
									if ( $post_result['post_last_editor'] == -1){
										
										$language -> setKey( 'post_edit_author', $post_result['editor_users_group_prefix'].$post_result[$post_result['post_last_editor_name']].$post_result['editor_users_group_suffix']);
									
									}else{
										
										$language -> setKey( 'post_edit_author', '<a href="'.parent::systemLink( 'user', array( 'user' => $post_result['post_last_editor'])).'">'.$post_result['editor_users_group_prefix'].$post_result['editor_user_login'].$post_result['editor_users_group_suffix'].'</a>');
									
									}
									
									$post_draw_strings['EDIT_INFO'] = $language -> getString( 'post_edit_info');
									
									if ( strlen( $post_result['post_edit_message']) > 0){
										
										$post_draw_parts['edit_reason'] = true;
									
										$post_draw_strings['EDIT_REASON'] = $language -> getString( 'post_edit_reason').': '.$post_result['post_edit_message'];
																		
									}else{
										
										$post_draw_parts['edit_reason'] = false;
									
									}
							
								}else{
									
									$post_draw_parts['edit'] = false;
									$post_draw_parts['edit_info'] = false;
									$post_draw_parts['edit_reason'] = false;
																
								}
								
							}else{
								
								$post_draw_parts['edit'] = false;
								$post_draw_parts['edit_info'] = false;
								$post_draw_parts['edit_reason'] = false;
								
							}
							
							/**
							 * any attachments?
							 */
							
							if ( $post_result['post_has_attachments']){
								
								$post_draw_parts['attachment'] = true;
								
								/**
								 * begin drawing list
								 */
								
								$attachments_list = '<b>'.$language -> getString( 'post_attachments').':</b><br />';
								
								/**
								 * check perms?
								 */
								
								if ( $session -> canDownload( $forum_result['forum_id'])){
									
									/**
									 * can download, select attachments
									 */
									
									$attachments_from_cache = $cache -> loadCache( 'attachments_'.$post_to_show);
									
									if ( gettype( $attachments_from_cache) != 'array'){
									
										$attachments_query = $mysql -> query( "SELECT a.*, t.* FROM attachments a LEFT JOIN attachments_types t ON a.attachment_type = t.attachments_type_id WHERE a.attachment_post = '".$post_to_show."' ORDER BY a.attachment_name");
									
										while ( $attachments_result = mysql_fetch_array( $attachments_query, MYSQL_ASSOC)){
											
											//clear result
											$attachments_result = $mysql -> clear( $attachments_result);
											
											$post_attachments[] = $attachments_result;
											
										}
									
										$cache -> saveCache( 'attachments_'.$post_to_show, $post_attachments);
										
									}else{
										
										$post_attachments = $attachments_from_cache;
										
									}
									
									settype( $post_attachments, 'array');
									
									foreach ( $post_attachments as $attachments_result){
									
										$post_attachments[] = $attachments_result;
										
										if ( file_exists( ROOT_PATH.'images/attachments_types/'.$attachments_result['attachments_type_image'])){
											
											$attachment_image = '<img src="'.ROOT_PATH.'images/attachments_types/'.$attachments_result['attachments_type_image'].'" title="'.$attachments_result['attachments_type_extension'].'">';
											
										}else{
										
											$attachment_image = '';
												
										}
										
										$language -> setKey( 'attachment_size', $strings -> fileSize( $attachments_result['attachment_size']));
										$language -> setKey( 'attachment_downloads', $attachments_result['attachment_downloads']);
										$language -> setKey( 'attachment_time', $time -> drawDate( $attachments_result['attachment_time']));
										
										$proper_mimes = array( 'image/jpeg', 'image/png', 'image/gif');
										
										if ( in_array( $attachments_result['attachments_type_mime'], $proper_mimes)){
											
											$attachment_thumbnail = '<br /><a href="'.parent::systemLink('download', array('attachment' => $attachments_result['attachment_id'])).'"><img src="'.parent::systemLink( 'download', array('attachment' => $attachments_result['attachment_id'], 'thumb' => true)).'"></a>';
										
										}else{
										
											$attachment_thumbnail = '';
										
										}
										
										/**
										 * add position
										 */
										
										$attachments_list .= '<div>'.$attachment_image.' <a href="'.parent::systemLink('download', array('attachment' => $attachments_result['attachment_id'])).'">'.$attachments_result['attachment_name'].'</a><br />'.$language -> getString( 'post_attachments_info').$attachment_thumbnail.'</div>';
																						
									}
									
								}else{
									
									/**
									 * cant download, sorry
									 */
									
									$attachments_list .= '<i>'.$language -> getString( 'post_attachments_noaccess').'</i>';
									
								}
								
								/**
								 * send to parse
								 */
								
								$post_draw_strings['ATTACHMENTS'] = $attachments_list;
								
							}else{
								
								$post_draw_parts['attachment'] = false;
								
							}
							
							/**
							 * shortcuts now
							 */
							
							$shortcuts_list = array();
							
							/**
							 * report post
							 */
							
							if ( $session -> user['user_id'] != -1 && $settings['users_allow_post_report'])
								$shortcuts_list[] = '<a href="'.parent::systemLink( 'report_post', array( 'post' => $post_result['post_id'])).'">'.$style -> drawImage( 'button_report', $language -> getString( 'post_report_button')).'</a>';
							
								
							/**
							 * mail
							 */
							
							if ( $post_result['user_want_mail'] && $post_result['user_id'] != -1 && $session -> user['user_can_send_mails']){
								
								if ( $post_result['user_show_mail']){
								
									/**
									 * user show his mail, draw it
									 */
									
									$send_mail_link = 'mailto:'.$post_result['user_mail'];
									
								}else{
									
									/**
									 * user not shows his mail, we have to send it "round the way"
									 */
									
									$mail_user_target = array( 'user' => $post_result['user_id']);
									
									$send_mail_link = parent::systemLink( 'mail_user', $mail_user_target);
									
								}
								
								$shortcuts_list[] = '<a href="'.$send_mail_link.'">'.$style -> drawImage( 'button_email', $language -> getString( 'user_mail_send')).'</a>';
								
							}
							
							/**
							 * pm
							 */
							
							if ( $post_result['user_id'] != -1 && $session -> user['user_id'] != -1){
								
								$send_pw_link = array( 'do' => 'new_pm', 'user' => $post_result['user_id']);
									
								$shortcuts_list[] = '<a href="'.parent::systemLink( 'profile', $send_pw_link).'">'.$style -> drawImage( 'button_pm', $language -> getString( 'user_pm_send')).'</a>';
								
							}
							
							/**
							 * www
							 */
								
							if ( !empty( $post_result['user_web'])){
											
								if ( substr( $post_result['user_web'], 0, 7) != "http://")
									$post_result['user_web'] = "http://".$post_result['user_web'];
									
								$shortcuts_list[] = '<a href="'.$post_result['user_web'].'">'.$style -> drawImage( 'button_www', $language -> getString( 'user_www')).'</a>';
							
							}
													
							/**
							 * display
							 */
							
							$post_draw_strings['SHORTCUTS'] = join( " ", $shortcuts_list);
							
							/**
							 * and actions now
							 */
							
							$pm_actions = array();
							
							/**
							 * if we can reply, add button
							 */
							
							if ( $session -> canReplyTopics( $forum_result['forum_id']) && ( (!$forum_result['forum_locked'] && !$post_result['topic_closed']) || $session -> user['user_avoid_closed_topics'] || $session -> isMod( $forum_result['forum_id']))){
								
								$reply_link = array( 'topic' => $post_result['topic_id'], 'post' => $post_result['post_id']);
								
								$pm_actions[] = '<a href="'.parent::systemLink( 'new_post', $reply_link).'">'.$style -> drawImage( 'button_reply', $language -> getString( 'user_cp_section_messenger_msgs_received_reply')).'</a>';
							
								
							}
							
							/**
							 * edit button
							 */
							
							if ( (!$forum_result['forum_locked'] && !$post_result['topic_closed']) || $session -> user['user_avoid_closed_topics'] || $session -> isMod( $forum_result['forum_id']) ){
								
								/**
								 * we can operate in topic
								 */
								
								$edit_topic_button = '<a href="'.parent::systemLink( 'edit_topic', array( 'topic' => $post_result['topic_id'])).'">'.$style -> drawImage( 'button_edit', $language -> getString( 'edit_post_button')).'</a>';
								$edit_post_button = '<a href="'.parent::systemLink( 'edit_post', array( 'post' => $post_result['post_id'])).'">'.$style -> drawImage( 'button_edit', $language -> getString( 'edit_post_button')).'</a>';
								
								$delete_post_button = '<a href="'.parent::systemLink( 'mod', array( 'post' => $post_result['post_id'])).'">'.$style -> drawImage( 'button_delete', $language -> getString( 'user_cp_section_messenger_msgs_received_delete')).'</a>';
									
								/**
								 * check, if we are mods
								 */
								
								if ( $session -> isMod( $forum_result['forum_id'])){
									
									/**
									 * we are mod
									 * limit doesnt matters for us
									 */
									
									if ( $post_result['topic_first_post_id'] != $post_result['post_id'] && $session -> user['user_id'] != -1){
	
										$pm_actions[] = $edit_post_button;
										$pm_actions[] = $delete_post_button;
									
									}
									
									if ( $post_result['topic_first_post_id'] == $post_result['post_id'] && $session -> user['user_id'] != -1){
										
										$pm_actions[] = $edit_topic_button;
									
									}
									
								}else{
									
									/**
									 * we arent mod, so there is an timelimit for us
									 */
									
									if ( $session -> user['user_edit_time_limit'] == 0 || (time() - $post_result['post_time']) < ($session -> user['user_edit_time_limit'] * 60)){
									
										if ( $session -> canReplyTopics( $forum_result['forum_id']) && $post_result['topic_first_post_id'] != $post_result['post_id'] && $session -> user['user_edit_own_posts'] && $post_result['post_author'] == $session -> user['user_id'] && $session -> user['user_id'] != -1)
											$pm_actions[] = $edit_post_button;
										
										if ( $session -> canReplyTopics( $forum_result['forum_id']) && $post_result['topic_first_post_id'] != $post_result['post_id'] && $post_result['topic_last_post_id'] == $post_result['post_id'] && $session -> user['user_delete_own_posts'] && $post_result['post_author'] == $session -> user['user_id'] && $session -> user['user_id'] != -1)
											$pm_actions[] = $delete_post_button;
											
										if ( $post_result['post_author'] == $session -> user['user_id'] && $session -> user['user_id'] != -1){
											
											if ( $session -> canStartTopics( $forum_result['forum_id']) && $post_result['topic_first_post_id'] == $post_result['post_id'] && $session -> user['user_change_own_topics']){
											
												$pm_actions[] = $edit_topic_button;
											
											}else if( $session -> canReplyTopics( $forum_result['forum_id']) && !in_array( $edit_post_button, $pm_actions) && $session -> user['user_edit_own_posts']){
												
												$pm_actions[] = $edit_post_button;
											
											}
										}
																			
									}
										
								
								}
								
															
							}
					
							/**
							 * display actions
							 */
							
							$post_draw_strings['ACTIONS'] = join( " ", $pm_actions);
					
							/**
							 * is post reported?
							 */
							
							if ( $settings['users_allow_post_report'] && $post_result['post_reported'] && $session -> isMod( $forum_result['forum_id'])){
								
								$post_draw_strings['REPORT_TEXT'] = '<a href="'.parent::systemLink( 'profile', array( 'do' => 'post_reports', 'post' => $post_result['post_id'])).'">'.$language -> getString( 'post_reported').'</a>';
								$post_draw_parts['reported'] = true;
								
							}else{
								
								$post_draw_parts['reported'] = false;
								
							}
							
							/**
							 * post text
							 */
							
							$message_text = $post_result['post_text'];
							
							/**
							 * check badwords
							 */
											
							if ( !$users -> cantCensore( $sender_groups)){
								$message_text = $strings -> censore( $message_text);
							}
							
							/**
							 * add it
							 */
												
							$post_draw_strings['POST_TEXT'] = $strings -> parseBB( nl2br( $message_text), $forum_result['forum_allow_bbcode'], true);
							
							if( $settings['message_big_cut'] > 0 && !defined( 'SIMPLE_MODE')){
								
								$post_draw_strings['POST_TEXT'] = '<div style="max-height: '.$settings['message_big_cut'].'px;overflow: auto">'.$post_draw_strings['POST_TEXT'].'<div>';
								
							}
									
							/**
							 * draw it
							 */
							
							$topic_posts -> addToContent( $style -> drawPost( (($post_number - 1) % 2), $post_draw_parts, $post_draw_strings));
					
						}else{
							
							/**
							 * author
							 */
							
							if ( $post_result['post_author'] == -1){
							
								/**
								 * author is deleted
								 */
							
								$post_author = $post_result['post_author_name'];
								
							}else{
								
								/**
								 * author exists
								 */
								
								$post_author = $post_result[16];				
								
							}
							
							/**
							 * post text
							 */
							
							$message_text = $post_result['post_text'];
							
							/**
							 * check badwords
							 */
											
							if ( !$users -> cantCensore( $sender_groups)){
								$message_text = $strings -> censore( $message_text);
							}
							
							/**
							 * post tile
							 */
							
							$post_title = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
							  <tr>
							    <td>'.$posts_number.'. '.$post_author.'</td>
							    <td style="text-align: right">'.$time -> drawDate( $post_result['post_time']).'</td>
							  </tr>
							</table>';
							
							/**
							 * add it
							 */
							
							parent::draw( $style -> drawBlock( $post_title, $strings -> parseBB( nl2br( $message_text), $forum_result['forum_allow_bbcode'], true)));
							
						}
							
						/**
						 * increase post number
						 */
						
						$post_number ++;
						
						/**
						 * close list
						 */
						
						$topic_posts -> closeTable();
									
						if ( $session -> isMod( $forum_result['forum_id']))
							$topic_posts -> closeForm();
						
						$language -> setKey( 'topic_posts_num', $post_result['topic_posts_num']);
						
						$topic_posts -> drawSpacer( '<div style="text-align: right">'.$language -> getString( 'topic_info').'</div>');
							
						/**
						 * topic mod ops
						 */
														
						if ( $session -> isMod( $forum_result['forum_id']) || ($session -> user['user_close_own_topics'] && $topic_result['topic_start_user'] == $session -> user['user_id'] && $session -> user['user_id'] != -1 && !$forum_result['forum_locked'])){
																
							if ( $topic_result['topic_closed']){
								
								$topic_mod_ops['open'] = $language -> getString( 'topic_moderation_open');
						
							}else{
								
								$topic_mod_ops['close'] = $language -> getString( 'topic_moderation_close');
						
							}
								
						}
						
						if ( $session -> isMod( $forum_result['forum_id']))
							$topic_mod_ops['move'] = $language -> getString( 'topic_moderation_move');
								
						if ( $session -> isMod( $forum_result['forum_id']) || ($session -> user['user_delete_own_topics'] && $topic_result['topic_start_user'] == $session -> user['user_id'] && $session -> user['user_id'] != -1  && !$forum_result['forum_locked']))
							$topic_mod_ops['delete'] = $language -> getString( 'topic_moderation_delete');
															
						if ( $session -> isMod( $forum_result['forum_id']) && $topic_result['topic_type'] != 0)
							$topic_mod_ops['normalize'] = $language -> getString( 'topic_moderation_normal');
						
						if ( $session -> isMod( $forum_result['forum_id']) && $topic_result['topic_type'] != 1)
							$topic_mod_ops['pin'] = $language -> getString( 'topic_moderation_pin');
						
						if ( $session -> isMod( $forum_result['forum_id']) && $topic_result['topic_type'] != 2)
							$topic_mod_ops['important'] = $language -> getString( 'topic_moderation_important');
						
						/**
						 * draw topic head
						 */
						
						if ( count( $topic_mod_ops) > 0){
							
							$topic_ops = new tools();
							
							$topic_ops -> drawSpacer( $language -> getString( 'topic_moderation'));
										
							foreach ( $topic_mod_ops as $mod_do => $mod_label)
								$topic_ops -> drawButton( '<a href="'.parent::systemLink( 'mod', array('topic' => $topic_to_show, 'do' => $mod_do)).'">'.$mod_label.'</a>');
									
							/**
							 * draw tools
							 */
								
							$topic_head = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
								  <tr>
								    <td style="width: 100%;">'.$title_prefix.$post_result['topic_name'].'</td>
								    <td nowrap="nowrap">'.$topic_ops -> display( $language -> getString( 'topic_options'), 'post').'</td>
								  </tr>
								</table>';
							
						}else{
							
							$topic_head = $title_prefix.$post_result['topic_name'];
							
						}
						
						/**
						 * display generated list
						 */
						
						parent::draw( $style -> drawFormBlock( $topic_head, $topic_posts -> display()));
							
						/**
						 * draw options
						 */
											
						parent::draw('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 6px;">
						  <tr>
						    <td style="text-align: left">'.$return_link.'</td>
						    <td style="text-align: right">'.join( " ", $topic_actions).'</td>
						  </tr>
						</table>');
											
						/**
						 * and jump list
						 */
						
						if ( $settings['forum_draw_forums_jumplist']){
							
							parent::draw( $style -> drawBlankBlock( '<b>'.$language -> getString( 'forums_jump').':</b><br />'.$forums -> drawForumsJumpList( $post_result['topic_forum_id'])));
							
						}
											
						/**
						 * update reads num
						 */
						
						$mysql -> update( array( 'topic_views_num' => ($post_result['topic_views_num'] + 1)), 'topics', "`topic_id` = '$topic_to_show'");
						
						/**
						 * and reads limit
						 */
						
						if ( $session -> user['user_id'] == -1 && $settings['guest_limit_reads'] > 0){
							
							setUniCookie( 'topics_reads', $reads_actual, $settings['guest_limit_length'] * 60);						
							
						}
						
					}else{
						
						/**
						 * reads limit excedeed
						 */
						
						$language -> setKey( 'max_topics_views', $settings['guest_limit_reads']);
						
						$main_error = new main_error();
						$main_error -> type = 'information';
						$main_error -> message = $language -> getString( 'show_topic_limit');
						parent::draw( $main_error -> display());
						
					}
					
				}else{
				
					/**
					 * no access to topic forum
					 */
							
					$main_error = new main_error();
					$main_error -> type = 'error';
					parent::draw( $main_error -> display());		
							
				}
				
			}else{
				
				/**
				 * no access to topic forum
				 */
				
				$main_error = new main_error();
				$main_error -> type = 'error';
				parent::draw( $main_error -> display());
								
			}
			
		}else{
			
			/**
			 * topic not found
			 */
			
			$main_error = new main_error();
			$main_error -> type = 'error';
			parent::draw( $main_error -> display());
			
		}
		
	}
	
}

?>