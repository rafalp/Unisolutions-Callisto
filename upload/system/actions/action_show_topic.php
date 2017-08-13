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

class action_show_topic extends action{
		
	/**
	 * this forum id
	 *
	 */
	
	var $topic_to_show;
	
	/**
	 * topic result
	 *
	 */
	
	var $topic_result = array();
	
	/**
	 * forum result
	 *
	 */
	
	var $forum_result = array();
	
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check, if topic exists
		 */
		
		$topic_to_show = $_GET['topic'];
		
		settype( $topic_to_show, 'integer');
		
		if ( $topic_to_show < 0){
			
			$topic_to_show = 0;
			
		}
		
		$this -> topic_to_show = $topic_to_show;
		
		$topic_query = $mysql -> query( "SELECT * FROM topics WHERE `topic_id` = '$topic_to_show'");
		
		if ( $topic_result = mysql_fetch_array( $topic_query, MYSQL_ASSOC)){
			
			//clear result
			$topic_result = $mysql -> clear( $topic_result);
			
			$this -> topic_result = $topic_result;
			
			/**
			 * check, if we can read topic
			 */
			
			if ( $session -> canSeeTopics( $topic_result['topic_forum_id'])){
			
				/**
				 * select forum
				 */
				
				$forum_query = $mysql -> query( "SELECT * FROM forums WHERE `forum_id` = '".$topic_result['topic_forum_id']."'");
				
				if ( $forum_result = mysql_fetch_array( $forum_query, MYSQL_ASSOC)){
					
					//clear result
					$forum_result = $mysql -> clear( $forum_result);
					
					$this -> forum_result = $forum_result;
					
					/**
					 * what mode do we have?
					 */
					
					if ( $smode != 2){
					
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
						
						$topic_prefix = $forums -> getPrefixHTML( $topic_result['topic_prefix'], $topic_result['topic_forum_id']);
						
						if ( strlen( $topic_prefix) == 0){
													
							if ( $topic_result['topic_type'] == 1){
								
								$topic_prefix = '<b>'.$settings['forum_stick_prefix'].':</b> ';
								$title_prefix = $settings['forum_stick_prefix'].': ';
							
							}else if ($topic_result['topic_survey']){
								
								$topic_prefix = '<b>'.$settings['forum_survey_prefix'].':</b> ';
								$title_prefix = $settings['forum_survey_prefix'].': ';
								
							}else{
								
								$topic_prefix = '';
								$title_prefix = '';
								
							}
							
						}else{
							
							$topic_prefix .= ' ';
							$title_prefix = $forums -> getPrefixName( $topic_result['topic_prefix'], $topic_result['topic_forum_id']).': ';
							
						}
						
						$path -> addBreadcrumb( $topic_prefix.$topic_result['topic_name'], parent::systemLink( parent::getId(), array( 'topic' => $topic_to_show)));
						
						/**
						 * set page title
						 */
						
						$output -> setTitle( $title_prefix.$topic_result['topic_name']);
						
						/**
						 * limit?
						 */
						
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
							
							if ( !defined('SIMPLE_MODE')){
													
								/**
								 * check quick reply
								 */
													
								if ( $forum_result['forum_allow_quick_reply'] && ($settings['quick_reply_avaibility'] == 1 || $settings['quick_reply_avaibility'] == 2) && $session -> canReplyTopics( $forum_result['forum_id']) && ((!$forum_result['forum_locked'] && !$topic_result['topic_closed']) || $session -> user['user_avoid_closed_topics'] || $session -> isMod( $forum_result['forum_id']))){
									
									$topic_actions[] = '<a href="javascript:openQuickReply()">'.$style -> drawImage( 'button_fast_reply_topic', $language -> getString( 'fast_reply_block')).'</a>';
									
								}
								
								/**
								 * clear quote cookie
								 */
								
								setcookie( 'topic_quote', null, time() - 1);
								
								/**
								 * check classic reply
								 */
													
								if ( $session -> canReplyTopics( $forum_result['forum_id'])){
									
									$multiquote_java = '<script type="text/JavaScript">
										
										mutliqoute_button_add = "'.addslashes( ROOT_PATH.'styles/'.$style -> style['path'].'/'.$style -> images['button_quote_add']).'";
										mutliqoute_button_remove = "'.addslashes( ROOT_PATH.'styles/'.$style -> style['path'].'/'.$style -> images['button_quote_remove']).'";
										
										topic_quote = new Array();
														
									</script>';
									
									if ( !$forum_result['forum_locked'] && !$topic_result['topic_closed']){
										
										$topic_actions[] = '<a href="'.parent::systemLink( 'new_post', array( 'topic' => $topic_to_show)).'">'.$style -> drawImage( 'button_reply_topic', $language -> getString( 'write_new_post')).'</a>';
																			
									}else if ( $session -> user['user_avoid_closed_topics'] || $session -> isMod( $forum_result['forum_id']) ){
										
										$topic_actions[] = '<a href="'.parent::systemLink( 'new_post', array( 'topic' => $topic_to_show)).'">'.$style -> drawImage( 'button_closed_topic', $language -> getString( 'write_new_post')).'</a>';
																			
									}else{
										
										$topic_actions[] = $style -> drawImage( 'button_closed_topic', $language -> getString( 'write_new_post'));
									
										$multiquote_java = '';
										
									}
									
									parent::draw($multiquote_java);
									
								}
								
								if ( $forum_result['forum_locked']){
										
									/**
									 * forum is locked, check if we have perms
									 */
									
									if ( $session -> user['user_avoid_closed_topics']){
										
										$topic_actions[] = '<a href="'.parent::systemLink( 'new_topic', array( 'forum' => $topic_result['topic_forum_id'])).'">'.$style -> drawImage( 'button_closed_topic', $language -> getString( 'topics_new_button')).'</a>';
										
									}else{
									
										$topic_actions[] = $style -> drawImage( 'button_closed_topic', $language -> getString( 'topics_new_button_locked'));
									
									}
									
								}else{
		
									$topic_actions[] = '<a href="'.parent::systemLink( 'new_topic', array( 'forum' => $forum_result['forum_id'])).'">'.$style -> drawImage( 'button_new_topic', $language -> getString( 'topics_new_button')).'</a>';
								
								}
														
								/**
								 * topic subscription
								 */
										
								if ( $session -> user['user_id'] != -1 && $settings['subscriptions_turn']){
									
									$subscription_sql = $mysql -> query( "SELECT * FROM subscriptions_topics WHERE `subscription_topic` = '$topic_to_show' AND `subscription_topic_user` = '".$session -> user['user_id']."'");
									
									if ( $subscription_result = mysql_fetch_array( $subscription_sql, MYSQL_ASSOC)){
										$subscribed = true;
										
										if ( $_GET['do'] == 'unsubscribe'){
											
											$mysql -> delete( 'subscriptions_topics', "`subscription_topic` = '$topic_to_show' AND `subscription_topic_user` = '".$session -> user['user_id']."'");
											
											$subscribed = false;
											
										}else{
											
											/**
											 * update
											 */
											
											$mysql -> update( array( 'subscription_topic_time' => time(), 'subscription_topic_posts' => $topic_result['topic_posts_num']), 'subscriptions_topics', "`subscription_topic_id` = '".$subscription_result['subscription_topic_id']."'");
																					
										}
									
									}else{
										
										$subscribed = false;
										
										if ($_GET['do'] == 'subscribe'){
									
											$mysql -> delete( 'subscriptions_topics', "`subscription_forum` = '$forum_to_show' AND `subscription_forum_user` = '".$session -> user['user_id']."'");
											$mysql -> insert( array( 'subscription_topic' => $topic_to_show, 'subscription_topic_user' => $session -> user['user_id'], 'subscription_topic_time' => 0, 'subscription_topic_posts' => $topic_result['topic_posts_num']), 'subscriptions_topics');
											
											$subscribed = true;
											
										}
									}
									
								}
		
							}
													
							/**
							 * paginating now
							 */
							
							$posts_number = $topic_result[ 'topic_posts_num'] + 1;
							
							if ( $settings['forum_posts_per_page'] < 1)
								$settings['forum_posts_per_page'] = 1;
							
							$pages_number = ceil( $posts_number / $settings['forum_posts_per_page']);
									
							if( isset( $_GET['p'])){
								
								$page_to_draw = $_GET['p'];
								settype( $page_to_draw, 'integer');
							
							}else{
								
								$page_to_draw = 1;
								
							}
							
							if ( $page_to_draw > $pages_number){
								
								$page_to_draw = 1;
								
							}
							
							if ( $page_to_draw < 1){
								
								$page_to_draw = 1;
								
							}
							
							$page_to_draw -= 1;
							
							/**
							 * paginator html
							 */
										
							$paginator_html = $style -> drawPaginator( parent::systemLink( parent::getId(), array( 'topic' => $topic_to_show)), 'p', $pages_number, ( $page_to_draw + 1));
							
							/**
							 * post number
							 */
							
							$post_number = 1 + ( $page_to_draw * $settings['forum_posts_per_page']);
							
							/**
							 * topic simple/normal link
							 */
							
							if ( defined( 'SIMPLE_MODE')){
								
								$style -> drawString( 'LINK_MODE_CHANGE', ROOT_PATH.'index.php?act=topic&topic='.$topic_to_show.'&p='.( $page_to_draw + 1));
						
							}else{
								
								$style -> drawString( 'LINK_MODE_CHANGE', ROOT_PATH.SIMPLE_PATH.'index.php?act=topic&topic='.$topic_to_show.'&p='.( $page_to_draw + 1));
								
							}
							
							/**
							 * draw options
							 */
												
							parent::draw('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 6px;">
							  <tr>
							    <td style="text-align: left">'.$paginator_html.'</td>
							    <td style="text-align: right">'.join( " ", $topic_actions).'</td>
							  </tr>
							</table>');
							
							if ( !defined('SIMPLE_MODE')){
							
								/**
								 * done, lets start drawing
								 */
								
								$topic_posts = new form();
								
								/**
								 * do we have a survey?
								 */
								
								if ( $topic_result['topic_survey']){
									
									$topic_posts -> drawSpacer( $settings['forum_survey_prefix']);
									$topic_posts -> openOpTable();
									
									$vote_made = false;
											
									/**
									 * select user vote
									 */
														
									if ( $session -> user['user_vote_surveys'] && $session -> user['user_id'] != -1){
									
										$survey_vote_sql = $mysql -> query( "SELECT * FROM `surveys_votes` WHERE `surveys_vote_topic` = '$topic_to_show' AND `surveys_vote_user` = '".$session -> user['user_id']."'");
																
										if ($survey_vote_result = mysql_fetch_array( $survey_vote_sql, MYSQL_ASSOC)){
											
											$survey_vote_result = $mysql -> clear( $survey_vote_result);
											
											$vote_made = true;
											
											$chas_vote = false;
										
										}else{
											
											$chas_vote = true;
										
										}
										
									}else{
									
										$chas_vote = false;
											
									}
									
									if ( $chas_vote && !$forum_result['forum_locked'] && !$topic_result['topic_closed'] && $session -> user['user_id'] != -1 && $session -> user['user_vote_surveys']){
										
										if ( $_GET['vote']){
											
											$vote_to_make = $_POST['survey_vote'];
											settype( $vote_to_make, 'integer');						
											
											/**
											 * check if that option exists
											 */
											
											$vote_option = $mysql -> query( "SELECT * FROM surveys_ops WHERE `survey_op_id` = '$vote_to_make' AND `survey_op_topic` = '".$topic_to_show."'");
											
											if ( $vote_result = mysql_fetch_array( $vote_option, MYSQL_ASSOC)){
												
												/**
												 * vote option exists
												 * update its votes num
												 */
												
												$vote_update['survey_op_votes'] = $vote_result['survey_op_votes'] + 1;
												$mysql -> update( $vote_update, 'surveys_ops', "`survey_op_id` = '$vote_to_make'");
												
												/**
												 * update survey votes
												 */
												
												$topic_result['topic_survey_votes'] ++;
												$mysql -> update( array( 'topic_survey_votes' => $topic_result['topic_survey_votes']), 'topics', "`topic_id` = '$topic_to_show'");
												
												/**
												 * insert new vote
												 */
												
												$mysql -> insert( array( 'surveys_vote_topic' => $topic_to_show, 'surveys_vote_option' => $vote_to_make, 'surveys_vote_user' => $session -> user['user_id']), 'surveys_votes');
												
												$vote_made = true;
												$chas_vote = false;
												
											}
											
										}
										
									}
									
									/**
									 * make sure we cant vote in all situations
									 */
									
									if ( $forum_result['forum_locked'] || $topic_result['topic_closed'])
										$chas_vote = false;
										
									/**
									 * and options
									 */
									
									$survey_form = new form();
									
									if ( $chas_vote)
										$survey_form -> openForm( parent::systemLink( parent::getId(), array('topic' => $topic_to_show, 'vote' => true, 'p' => ($page_to_draw +1))));
									
									$survey_form -> addToContent('<div style="text-align: center; font-weight: bold;">'.$topic_result['topic_survey_text'].'</div>');
									
									
									
									$survey_form -> addToContent('<table width="100%" border="0" cellspacing="0" cellpadding="4">');
									
									/**
									 * select them
									 */
									
									$survey_ops_sql = $mysql -> query( "SELECT * FROM `surveys_ops` WHERE `survey_op_topic` = '$topic_to_show'");
									
									while ($survey_ops_result = mysql_fetch_array( $survey_ops_sql, MYSQL_ASSOC)){
									
										//clear result
										$survey_ops_result = $mysql -> clear( $survey_ops_result);
										
										/**
										 * define what to do
										 */
											
										if ( $chas_vote){
											
											/**
											 * voting ops
											 */
											
											$survey_form -> addToContent('<tr>
										   		<td><input name="survey_vote" type="radio" value="'.$survey_ops_result['survey_op_id'].'" /></td><td style="width:100%">'.$survey_ops_result['survey_op_name'].'</td>
										   	</tr>');
											
										}else{
											
											/**
											 * voting stats
											 */
											
											if ( $topic_result['topic_survey_votes'] == 0 || $survey_ops_result['survey_op_votes'] == 0){
			
												$vote_bar = $style -> drawBar( 0);
												
											}else{
												
												$vote_bar = $style -> drawBar( ceil( $survey_ops_result['survey_op_votes'] * 100 / $topic_result['topic_survey_votes']));
											
											}
											
											$survey_form -> addToContent('<tr>
											    <td style="width: 140px">'.$survey_ops_result['survey_op_name'].' ('.$survey_ops_result['survey_op_votes'].')</td>
											    <td>'.$vote_bar.'</td>
										    </tr>');
											
										}
										
									}
															
									if ( !$chas_vote){
										
										
											$survey_form -> addToContent('<tr>
											    <td colspan="2"><b>'.$language -> getString( 'show_topic_survey_votes').':</b> '.$topic_result['topic_survey_votes'].'</td>
										    </tr>');
										
									}
									
									$survey_form -> addToContent( '</table>');
											
									$survey_form -> addToContent( '<div style="text-align: center; font-style:italic">');
									
									if ( $forum_result['forum_locked'] || $topic_result['topic_closed']){
										
										$survey_form -> addToContent( $language -> getString( 'show_topic_survey_closed'));
																		
									}else if ( $session -> user['user_id'] == -1){
										
										$survey_form -> addToContent( $language -> getString( 'show_topic_survey_guest'));
										
									}else if ( $vote_made){
										
										$survey_form -> addToContent( $language -> getString( 'show_topic_survey_alreadyvoted'));
										
									}else if ( !$session -> user['user_vote_surveys'] && $session -> user['user_id'] != -1){
										
										$survey_form -> addToContent( $language -> getString( 'show_topic_survey_cant_vote'));
										
									}else{
										
										$survey_form -> addToContent( '<input name="" type="submit" value="'.$language -> getString( 'show_topic_survey_select_vote').'"/>');
										
									}
																	
									$survey_form -> addToContent('</div>');
									
									if ( $chas_vote)
										$survey_form -> closeForm();
									
									/**
									 * and draw
									 */
									
									$topic_posts -> drawRow( $style -> drawBlankBlock( $survey_form -> display()));
									
									$topic_posts -> closeTable();
									
									/**
									 * cpacer maybe?
									 */
												
									$topic_posts -> drawSpacer( $language -> getString( 'show_topic'));
												
								}
							
								
								/**
								 * if we are mods, open form
								 */
								
								if ( $session -> isMod( $forum_result['forum_id']))
									$topic_posts -> openForm( parent::systemLink( 'mod'), 'POST', false, 'posts_list');
								
								$topic_posts -> openOpTable();
								
							}
								
							/**
							 * draw ad
							 */
							
							if ( !defined( 'SIMPLE_MODE') && $smode == 0){
							
								if ( $settings['ads_in_topics_display'] == 0 || $settings['ads_in_topics_display'] == 2){
									
									if ( !$settings['ads_in_topics_guests_only'] || ($settings['ads_in_topics_guests_only'] && $session -> user['user_id'] == -1)){
										
										if ( strlen( $settings['ads_in_topics_content']) > 0){
											
											$post_draw_parts = array();
											$post_draw_strings = array();
											
											/**
											 * hide parts
											 */
											
											$post_draw_parts['member'] = false;
											$post_draw_parts['author_status'] = false;
											$post_draw_parts['avatar'] = false;
											$post_draw_parts['reported'] = false;
											$post_draw_parts['attachment'] = false;
											$post_draw_parts['signature'] = false;
											$post_draw_parts['edit'] = false;
											$post_draw_parts['thanks'] = false;
											
											/**
											 * set strings
											 */
											
											$post_draw_strings['AUTHOR_NAME'] = $settings['ads_in_topics_author_name'];
											
											$post_draw_strings['PROFILE_GROUP'] = $language -> getString( 'user_group');
											$post_draw_strings['USER_GROUP'] = $users -> users_groups[2]['users_group_prefix'].$users -> users_groups[2]['users_group_name'].$users -> users_groups[2]['users_group_suffix'];
										
											$post_draw_strings['INFO'] = '<i>'.$language -> getString( 'this_is_advert').'</i>';
									
											$post_draw_strings['POST_TEXT'] = $settings['ads_in_topics_content'];
											$post_draw_strings['SHORTCUTS'] = '';		
											$post_draw_strings['ACTIONS'] = '';										
											
											$topic_posts -> addToContent( $style -> drawPost( (($post_number - 1) % 2), $post_draw_parts, $post_draw_strings));
							
											$post_number ++;
											
										}
										
									}
									
								}
								
							}
							
							/**
							 * select posts
							 */
							
							$posts_query = $mysql -> query( "SELECT p.*, u.*, f.*, g.users_group_name, g.users_group_title, g.users_group_prefix, g.users_group_suffix, mu.user_id AS editor_user_id, mu.user_login AS editor_user_login, mu.user_main_group AS editor_user_main_group, mu.user_other_groups AS editor_user_other_groups, mg.users_group_prefix AS editor_users_group_prefix, mg.users_group_suffix AS editor_users_group_suffix
							FROM posts p
							LEFT JOIN users u ON p.post_author = u.user_id
							LEFT JOIN profile_fields_data f ON u.user_id = f.profile_fields_user
							LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
							LEFT JOIN users mu ON p.post_last_editor = mu.user_id
							LEFT JOIN users_groups mg ON mu.user_main_group = mg.users_group_id
							WHERE p.post_topic = '$topic_to_show'
							ORDER BY post_time
							LIMIT ".( $page_to_draw * $settings['forum_posts_per_page']).", ".$settings['forum_posts_per_page']);
							
							while ( $posts_result = mysql_fetch_array( $posts_query, MYSQL_ASSOC)){
								
								//clear post result
								$posts_result = $mysql -> clear( $posts_result);
								//var_dump( $posts_result);
								/**
								 * set clears
								 */
								
								$post_draw_parts = array();
								$post_draw_strings = array();
								
								/**
								 * post groups
								 */
								
								$sender_groups = array();
								$sender_groups = split( ",", $posts_result['user_other_groups']);
								
								$sender_groups[] = $posts_result['user_main_group'];
								
								if ( !defined( 'SIMPLE_MODE')){
													
									/**
									 * user is guest?
									 */
									
									if ( $posts_result['post_author'] == -1){
										
										$post_draw_parts['member'] = false;
										
									}else{
										
										$post_draw_parts['member'] = true;
										
									}
									
									/**
									 * message info
									 */
									
									if ( $posts_result['post_time'] > $topic_read){
										$msg_image = $style -> drawImage( 'small_forum_new', $language -> getString( 'user_cp_section_messenger_msgs_list_unread'));
									}else{
										$msg_image = $style -> drawImage( 'small_forum', $language -> getString( 'user_cp_section_messenger_msgs_list_read'));
									}
									
									if ( $session -> user['user_id'] == -1)
										$msg_image = $style -> drawImage( 'small_forum', $language -> getString( 'user_cp_section_messenger_msgs_list_read'));
									
									$message_info_title = $language -> getString('user_cp_section_messenger_msgs_received_date').': '.$time -> drawDate( $posts_result['post_time']);				
									
									/**
									 * post info
									 */
									
									$post_info_params = array();
									
									/**
									 * user ip
									 */
									
									if ( $session -> isMod( $forum_result['forum_id']) || ($session -> user['user_id'] == $posts_result['post_author'] && $session -> user['user_id'] != -1)){
										
										$post_info_params[] = 'ip: '.long2ip( $posts_result['post_ip']);
										$post_info_params[] = 'agent: '.$posts_result['post_user_agent'];
										
									}
									
									/**
									 * rate post
									 */
											
									if ( $settings['reputation_turn'] && $session -> user['user_id'] != -1 && $session -> user['user_id'] != $posts_result['post_author'] && $posts_result['post_author'] != -1){
									
										$post_info_params[] = '<a href="'.parent::systemLink( 'rate_post', array( 'post' => $posts_result['post_id'], 'd' => 0)).'">'.$style -> drawImage( 'minus', $language -> getString( 'rate_post_m')).'</a> <a href="'.parent::systemLink( 'rate_post', array( 'post' => $posts_result['post_id'], 'd' => 1)).'">'.$style -> drawImage( 'plus', $language -> getString( 'rate_post_p')).'</a>';
																
									}
									
									/**
									 * postnum
									 */
									
									$post_info_params[] = '<a href="'.parent::systemLink( 'post', array( 'post' => $posts_result['post_id'])).'">#'.$post_number.'</a>';
									
									/**
									 * mod select
									 */
									
									if ( $session -> isMod( $forum_result['forum_id'])){
										
										$post_info_params[] = $topic_posts -> drawSelect( 'post_select['.$posts_result['post_id'].']');
										
									}
									
									$post_draw_strings['INFO'] = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
																  <tr>
																    <td style="text-align: left">'.$msg_image.' '.$message_info_title.'</td>
																    <td style="text-align: right">'.join( " | ", $post_info_params).'</td>
																  </tr>
																</table>';
									
									/**
									 * begin from author
									 */
									
									if ( $posts_result['post_author'] == -1){
									
										/**
										 * author is deleted
										 */
										
										$post_draw_parts['author_status'] = false;
										
										$post_draw_strings['AUTHOR_NAME'] = '<a name="post'.$posts_result['post_id'].'" id="post'.$posts_result['post_id'].'">'.$posts_result['users_group_prefix'].$posts_result['post_author_name'].$posts_result['users_group_suffix'].'</a>';
										
									}else{
										
										/**
										 * author exists
										 */
										
										if ( $settings['users_count_online']){
										
											$post_draw_parts['author_status'] = true;
										
											$post_draw_strings['AUTHOR_STATUS_IMG'] = $style -> drawStatus( $users -> checkOnLine( $posts_result['post_author']));
											
										}else{
											
											$post_draw_parts['author_status'] = false;
											
										}
										
										/**
										 * and user login
										 */
										
										$post_draw_strings['AUTHOR_NAME'] = '<a name="post'.$posts_result['post_id'].'" id="post'.$posts_result['post_id'].'">'.'<a href="'.parent::systemLink( 'user', array( 'user' => $posts_result['post_author'])).'">'.$posts_result['users_group_prefix'].$posts_result['user_login'].$posts_result['users_group_suffix'].'</a></a>';				
										
									}
									
									/**
									 * author profile
									 */
									
									if ( $posts_result['user_avatar_type'] != 0 && $settings['users_can_avatars'] && $session -> user['user_show_avatars']){
										
										$post_draw_parts['avatar'] = true;
										
										/**
										 * draw avatar
										 */
										
										$post_draw_strings['AUTHOR_AVATAR'] = $users -> drawAvatar( $posts_result['user_avatar_type'], $posts_result['user_avatar_image'], $posts_result['user_avatar_width'], $posts_result['user_avatar_height']);				
										
									}else{
										
										$post_draw_parts['avatar'] = false;
										
									}
									
									/**
									 * title
									 */
									
									if ( strlen( $posts_result['user_custom_title']) > 0  && $settings['users_posts_to_title'] > 0 && $posts_result['user_posts_num'] >= $settings['users_posts_to_title']){
											
										$post_draw_strings['AUTHOR_TITLE'] = $posts_result['user_custom_title'];
										
									}else if ( strlen( $posts_result['users_group_title']) > 0){
										
										$post_draw_strings['AUTHOR_TITLE'] = $posts_result['users_group_title'];
										
									}else{
										
										$post_draw_strings['AUTHOR_TITLE'] = $users -> drawRankName( $posts_result['user_posts_num']);
															
									}
									
									if ( strlen( $users -> users_groups[ $posts_result['user_main_group']]['users_group_image']) > 0){
										$post_draw_strings['AUTHOR_RANK'] = '<img src="'.$users -> users_groups[$posts_result['user_main_group']]['users_group_image'].'" alt="" title""/>';						
									}else{
										$post_draw_strings['AUTHOR_RANK'] = $users -> drawRankImage( $posts_result['user_posts_num']);
									}
									
									$post_draw_strings['PROFILE_GROUP'] = $language -> getString( 'user_group');
									$post_draw_strings['USER_GROUP'] = $posts_result['users_group_prefix'].$posts_result['users_group_name'].$posts_result['users_group_suffix'];
									
									$post_draw_strings['PROFILE_POSTS'] = $language -> getString( 'user_posts');
									$post_draw_strings['USER_POSTS'] = $posts_result['user_posts_num'];
									
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
									
									if ( strlen( $posts_result['user_localisation']) != 0){
										
										$post_draw_parts['author_location'] = true;
										$post_draw_strings['PROFILE_LOCATION'] = $language -> getString( 'user_localisation');
										$post_draw_strings['USER_LOCATION'] = $posts_result['user_localisation'];
									
									}else{
										
										$post_draw_parts['author_location'] = false;
										
									}
															
									$post_draw_strings['PROFILE_JOIN_DATE'] = $language -> getString( 'user_registration');
									$post_draw_strings['USER_JOIN_DATE'] = $time -> drawDate( $posts_result['user_regdate']);
													
									/**
									 * reputation
									 */
									
									if ( $settings['reputation_turn']){
										
										$post_draw_parts['reps'] = true;
										
										$post_draw_strings['PROFILE_REPUTATION'] = $language -> getString( 'user_reputation');
										$post_draw_strings['USER_REPUTATION'] = $users -> drawReputation( $users -> countReputation( $posts_result['user_rep'], $posts_result['user_posts_num'], $posts_result['user_regdate']));
										
									}else{
										
										$post_draw_parts['reps'] = false;
										
									}
									
									/**
									 * and thanks
									 */
									
									if ( $settings['reputation_turn'] && $posts_result['post_thanked']){
										
										$post_draw_parts['thanks'] = true;
										$post_draw_strings['SHOW_THANKS'] = '<a href="'.parent::systemLink( 'show_reps', array( 'post' => $posts_result['post_id'])).'">'.$language -> getString( 'post_thanked').'</a>';
																		
									}else{
										
										$post_draw_parts['thanks'] = false;
										
									}
									
									/**
									 * warns
									 */
											
									if ( $settings['warns_turn'] && $posts_result['post_author'] != -1){
										
										/**
										 * check if we can see warns
										 */
										
										if ( $settings['warns_show'] == 0 || ( $settings['warns_show'] == 1 && $session -> user['user_id'] != -1) || ( $settings['warns_show'] == 2 && ($session -> user['user_can_be_mod'] || ($session -> user['user_id'] != -1 && $session -> user['user_id'] == $posts_result['post_author'])))){
										
											/**
											 * draw warns
											 */
											
											$post_draw_parts['warns'] = true;
											
											$post_draw_strings['PROFILE_WARNS'] = $language -> getString( 'user_warns');							
											
											if ( $posts_result['user_warns'] > 0){
										
												$warns_link_open = '<a href="'.parent::systemLink( 'user_warns', array( 'user' => $posts_result['user_id'])).'">';
												$warns_link_close = '</a>';
												
											}else{
												
												$warns_link_open = '';
												$warns_link_close = '';
												
											}
											
											/**
											 * draw warns, or wanrs + mod?
											 */
											
											if ( $session -> user['user_can_be_mod']){
												
												$post_draw_strings['USER_WARNS'] = '<a href="'.parent::systemLink( 'mod', array( 'user' => $posts_result['post_author'], 'd' => '1')).'" title="'.$language -> getString( 'user_warn_decrease').'">'.$style -> drawImage( 'minus').'</a> '.$warns_link_open.$users -> drawWarnLevel( $posts_result['user_warns']).$warns_link_close.' <a href="'.parent::systemLink( 'mod', array( 'user' => $posts_result['post_author'], 'd' => '0')).'" title="'.$language -> getString( 'user_warn_add').'">'.$style -> drawImage( 'plus').'</a>';
												
											}else{
												
												$post_draw_strings['USER_WARNS'] = $warns_link_open.$users -> drawWarnLevel( $posts_result['user_warns']).$warns_link_close;
																			
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
									
									if ( $posts_result['user_id'] != -1){
										
										if ( count( $users -> custom_fields) > 0){
											
											foreach ( $users -> custom_fields as $field_id => $field_ops) {
												
												if ( strlen( $posts_result['field_'.$field_id]) > 0 && $field_ops['profile_field_inposts'] && (!$field_ops['profile_field_private'] || ( $field_ops['profile_field_private'] && ( $posts_result['user_id'] == $session -> user['user_id'] || $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate'])))){
													
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
														
														$field_template = str_ireplace( '{KEY}', $posts_result['field_'.$field_id], $field_template);
														$field_template = str_ireplace( '{VALUE}', $made_options[$posts_result['field_'.$field_id]], $field_template);
														
													}else{
														
														$field_template = str_ireplace( '{VALUE}', $posts_result['field_'.$field_id], $field_template);
																				
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
									
									if ( strlen( $posts_result['user_signature']) > 0 && $session -> user['user_show_sigs'] && $settings['users_can_sigs']){
										
										$post_draw_parts['signature'] = true;
										$post_draw_strings['SIGNATURE'] = $strings -> parseBB( nl2br( $posts_result['user_signature']), $settings['users_allow_bbcodes_in_sigs'], $settings['users_allow_emoticones_in_sigs']);
									
									}else{
										
										$post_draw_parts['signature'] = false;
											
									}
									
									/**
									 * any edits?
									 */
									
									if ( $posts_result['post_last_edit'] > 0){
										
										/**
										 * check legend
										 */
										
										$editor_groups = array();
										$editor_groups = split( ",", $posts_result['editor_user_other_groups']);
										$editor_groups[] = $posts_result['editor_user_main_group'];
										
										$drawLegend = $users -> checkEditLegend($editor_groups);
										
										/**
										 * we will draw legend always if:
										 * there is edit reason
										 * show legend
										 */
										
										if ( $drawLegend || strlen( $posts_result['post_edit_message']) > 0){
										
											/**
											 * draw message
											 */
												
											$post_draw_parts['edit'] = true;
											
											$post_draw_parts['edit_info'] = true;
											
											$language -> setKey( 'post_edit_time', $time -> drawDate( $posts_result['post_last_edit']));
											$language -> setKey( 'post_edit_num', $posts_result['post_edits']);
											
											/**
											 * post editor
											 */
			
											if ( $posts_result['post_last_editor'] == -1){
												
												$language -> setKey( 'post_edit_author', $posts_result['editor_users_group_prefix'].$posts_result[$posts_result['post_last_editor_name']].$posts_result['editor_users_group_suffix']);
											
											}else{
												
												$language -> setKey( 'post_edit_author', '<a href="'.parent::systemLink( 'user', array( 'user' => $posts_result['post_last_editor'])).'">'.$posts_result['editor_users_group_prefix'].$posts_result['editor_user_login'].$posts_result['editor_users_group_suffix'].'</a>');
											
											}
											
											$post_draw_strings['EDIT_INFO'] = $language -> getString( 'post_edit_info');
											
											if ( strlen( $posts_result['post_edit_message']) > 0){
												
												$post_draw_parts['edit_reason'] = true;
											
												$post_draw_strings['EDIT_REASON'] = $language -> getString( 'post_edit_reason').': '.$posts_result['post_edit_message'];
																				
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
									
									if ( $posts_result['post_has_attachments']){
										
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
											
											$attachments_from_cache = $cache -> loadCache( 'attachments_'.$posts_result['post_id']);
											
											if ( gettype( $attachments_from_cache) != 'array'){
											
												$attachments_query = $mysql -> query( "SELECT a.*, t.* FROM attachments a LEFT JOIN attachments_types t ON a.attachment_type = t.attachments_type_id WHERE a.attachment_post = '".$posts_result['post_id']."' ORDER BY a.attachment_name");
											
												while ( $attachments_result = mysql_fetch_array( $attachments_query, MYSQL_ASSOC)){
													
													//clear result
													$attachments_result = $mysql -> clear( $attachments_result);
													
													$post_attachments[] = $attachments_result;
													
												}
											
												$cache -> saveCache( 'attachments_'.$posts_result['post_id'], $post_attachments);
												
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
										$shortcuts_list[] = '<a href="'.parent::systemLink( 'report_post', array( 'post' => $posts_result['post_id'])).'">'.$style -> drawImage( 'button_report', $language -> getString( 'post_report_button')).'</a>';
									
										
									/**
									 * mail
									 */
									
									if ( $posts_result['user_want_mail'] && $posts_result['user_id'] != -1 && $session -> user['user_can_send_mails']){
										
										if ( $posts_result['user_show_mail']){
										
											/**
											 * user show his mail, draw it
											 */
											
											$send_mail_link = 'mailto:'.$posts_result['user_mail'];
											
										}else{
											
											/**
											 * user not shows his mail, we have to send it "round the way"
											 */
											
											$mail_user_target = array( 'user' => $posts_result['user_id']);
											
											$send_mail_link = parent::systemLink( 'mail_user', $mail_user_target);
											
										}
										
										$shortcuts_list[] = '<a href="'.$send_mail_link.'">'.$style -> drawImage( 'button_email', $language -> getString( 'user_mail_send')).'</a>';
										
									}
									
									/**
									 * pm
									 */
									
									if ( $posts_result['user_id'] != -1 && $session -> user['user_id'] != -1){
										
										$send_pw_link = array( 'do' => 'new_pm', 'user' => $posts_result['user_id']);
											
										$shortcuts_list[] = '<a href="'.parent::systemLink( 'profile', $send_pw_link).'">'.$style -> drawImage( 'button_pm', $language -> getString( 'user_pm_send')).'</a>';
										
									}
									
									/**
									 * www
									 */
										
									if ( !empty( $posts_result['user_web'])){
													
										if ( substr( $posts_result['user_web'], 0, 7) != "http://")
											$posts_result['user_web'] = "http://".$posts_result['user_web'];
											
										$shortcuts_list[] = '<a href="'.$posts_result['user_web'].'">'.$style -> drawImage( 'button_www', $language -> getString( 'user_www')).'</a>';
									
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
									
									if ( $session -> canReplyTopics( $forum_result['forum_id']) && (( !$forum_result['forum_locked'] && !$topic_result['topic_closed']) || $session -> user['user_avoid_closed_topics'] || $session -> isMod( $forum_result['forum_id']))){
										
										/**
										 * multiquote button
										 */
										
										$pm_actions[] = '<a href="javascript:addPostToMultiquote( '.$posts_result['post_id'].' )">'.$style -> drawImage( 'button_quote_add', $language -> getString( 'user_cp_section_messenger_msgs_quote_toggle'), 'mquote_button_'.$posts_result['post_id']).'</a>';
										
										/**
										 * reply button
										 */
										
										$reply_link = array( 'topic' => $topic_result['topic_id'], 'post' => $posts_result['post_id']);
										
										$pm_actions[] = '<a href="'.parent::systemLink( 'new_post', $reply_link).'">'.$style -> drawImage( 'button_reply', $language -> getString( 'user_cp_section_messenger_msgs_received_reply')).'</a>';
																			
									}
									
									/**
									 * edit button
									 */
									
									if ( (!$forum_result['forum_locked'] && !$topic_result['topic_closed']) || $session -> user['user_avoid_closed_topics'] || $session -> isMod( $forum_result['forum_id']) ){
										
										/**
										 * we can operate in topic
										 */
										
										$edit_topic_button = '<a href="'.parent::systemLink( 'edit_topic', array( 'topic' => $topic_to_show)).'">'.$style -> drawImage( 'button_edit', $language -> getString( 'edit_post_button')).'</a>';
										$edit_post_button = '<a href="'.parent::systemLink( 'edit_post', array( 'post' => $posts_result['post_id'])).'">'.$style -> drawImage( 'button_edit', $language -> getString( 'edit_post_button')).'</a>';
										
										$delete_post_button = '<a href="'.parent::systemLink( 'mod', array( 'post' => $posts_result['post_id'])).'">'.$style -> drawImage( 'button_delete', $language -> getString( 'user_cp_section_messenger_msgs_received_delete')).'</a>';
											
										/**
										 * check, if we are mods
										 */
										
										if ( $session -> isMod( $forum_result['forum_id'])){
											
											/**
											 * we are mod
											 * limit doesnt matters for us
											 */
											
											if ( $topic_result['topic_first_post_id'] != $posts_result['post_id'] && $session -> user['user_id'] != -1){
			
												$pm_actions[] = $edit_post_button;
												$pm_actions[] = $delete_post_button;
											
											}
											
											if ( $topic_result['topic_first_post_id'] == $posts_result['post_id'] && $session -> user['user_id'] != -1){
												
												$pm_actions[] = $edit_topic_button;
											
											}
											
										}else{
											
											/**
											 * we arent mod, so there is an timelimit for us
											 */
											
											if ( $session -> user['user_edit_time_limit'] == 0 || (time() - $posts_result['post_time']) < ($session -> user['user_edit_time_limit'] * 60)){
											
												if ( $session -> canReplyTopics( $forum_result['forum_id']) && $topic_result['topic_first_post_id'] != $posts_result['post_id'] && $session -> user['user_edit_own_posts'] && $posts_result['post_author'] == $session -> user['user_id'] && $session -> user['user_id'] != -1)
													$pm_actions[] = $edit_post_button;
												
												if ( $session -> canReplyTopics( $forum_result['forum_id']) && $topic_result['topic_first_post_id'] != $posts_result['post_id'] && $topic_result['topic_last_post_id'] == $posts_result['post_id'] && $session -> user['user_delete_own_posts'] && $posts_result['post_author'] == $session -> user['user_id'] && $session -> user['user_id'] != -1)
													$pm_actions[] = $delete_post_button;
													
												if ( $posts_result['post_author'] == $session -> user['user_id'] && $session -> user['user_id'] != -1){
													
													if ( $session -> canStartTopics( $forum_result['forum_id']) && $topic_result['topic_first_post_id'] == $posts_result['post_id'] && $session -> user['user_change_own_topics']){
													
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
									
									if ( $settings['users_allow_post_report'] && $posts_result['post_reported'] && $session -> isMod( $forum_result['forum_id'])){
										
										$post_draw_strings['REPORT_TEXT'] = '<a href="'.parent::systemLink( 'profile', array( 'do' => 'post_reports', 'post' => $posts_result['post_id'])).'">'.$language -> getString( 'post_reported').'</a>';
										$post_draw_parts['reported'] = true;
										
									}else{
										
										$post_draw_parts['reported'] = false;
										
									}
									
									/**
									 * post text
									 */
									
									$message_text = $posts_result['post_text'];
									
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
									
									//limit it
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
									
									if ( $posts_result['post_author'] == -1){
									
										/**
										 * author is deleted
										 */
									
										$post_author = $posts_result['post_author_name'];
										
									}else{
										
										/**
										 * author exists
										 */
										
										$post_author = $posts_result['user_login'];				
										
									}
									
									/**
									 * post text
									 */
									
									$message_text = $posts_result['post_text'];
									
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
									    <td>'.$post_number.'. '.$post_author.'</td>
									    <td style="text-align: right">'.$time -> drawDate( $posts_result['post_time']).'</td>
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
								
							}
							
							/**
							 * draw ad
							 */
							
							if ( !defined( 'SIMPLE_MODE') && $smode == 0){
							
								if ( $settings['ads_in_topics_display'] == 1 || $settings['ads_in_topics_display'] == 2){
									
									if ( !$settings['ads_in_topics_guests_only'] || ($settings['ads_in_topics_guests_only'] && $session -> user['user_id'] == -1)){
										
										if ( strlen( $settings['ads_in_topics_content']) > 0){
											
											$post_draw_parts = array();
											$post_draw_strings = array();
											
											/**
											 * hide parts
											 */
											
											$post_draw_parts['member'] = false;
											$post_draw_parts['author_status'] = false;
											$post_draw_parts['avatar'] = false;
											$post_draw_parts['reported'] = false;
											$post_draw_parts['attachment'] = false;
											$post_draw_parts['signature'] = false;
											$post_draw_parts['edit'] = false;
											$post_draw_parts['thanks'] = false;
											
											/**
											 * set strings
											 */
											
											$post_draw_strings['AUTHOR_NAME'] = $settings['ads_in_topics_author_name'];
											
											$post_draw_strings['PROFILE_GROUP'] = $language -> getString( 'user_group');
											$post_draw_strings['USER_GROUP'] = $users -> users_groups[2]['users_group_prefix'].$users -> users_groups[2]['users_group_name'].$users -> users_groups[2]['users_group_suffix'];
										
											$post_draw_strings['INFO'] = '<i>'.$language -> getString( 'this_is_advert').'</i>';
									
											$post_draw_strings['POST_TEXT'] = $settings['ads_in_topics_content'];
											$post_draw_strings['SHORTCUTS'] = '';		
											$post_draw_strings['ACTIONS'] = '';										
											
											$topic_posts -> addToContent( $style -> drawPost( (($post_number - 1) % 2), $post_draw_parts, $post_draw_strings));
							
											$post_number ++;
											
										}
										
									}
									
								}
								
							}
							
							/**
							 * close list
							 */
							
							if ( !defined( 'SIMPLE_MODE')){
							
								$topic_posts -> closeTable();
								
								/**
								 * topic subscription
								 */
								
								
									
								/**
								 * draw summary
								 */
								
								$language -> setKey( 'topic_posts_num', $topic_result['topic_posts_num']);
																
								$topic_posts -> drawSpacer( '<div style="text-align: right">'.$language -> getString( 'topic_info').'</div>');
															
								if ( $session -> isMod( $forum_result['forum_id']))
									$topic_posts -> closeForm();
								
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
								
								if ( $session -> user['user_id'] != -1 && ($settings['subscriptions_turn'] || $settings['topics_rantings_turn'] || count( $topic_mod_ops) > 0)){	
									
									$topic_ops = new tools();
									
									if ($settings['subscriptions_turn']){
																		
										/**
										 * select subscription
										 */
										
										if ( $subscribed){
											
											$subscribe_link_title = $language -> getString( 'topic_unsubscribe');
											$subscribe_link	= parent::systemLink( parent::getId(), array( 'topic' => $topic_to_show, 'p' => ($page_to_draw + 1), 'do' => 'unsubscribe'));
																
										}else{
											
											$subscribe_link_title = $language -> getString( 'topic_subscribe');
											$subscribe_link	= parent::systemLink( parent::getId(), array( 'topic' => $topic_to_show, 'p' => ($page_to_draw + 1), 'do' => 'subscribe'));
										
										}
									
										$topic_ops -> drawButton( '<a href="'.$subscribe_link.'">'.$subscribe_link_title.'</a>');
										
									}
									
									/**
									 * rate
									 */
									
									if ( $settings['topics_rantings_turn']){
										
										$topic_ops -> drawSpacer( $language -> getString( 'topic_options_ratings'));
																				
										$topic_rate_form = new form();
										$topic_rate_form -> openForm( parent::systemLink( parent::getId(), array( 'topic' => $topic_to_show, 'p' => ($page_to_draw + 1))), 'POST', false, 'topic_rate_form');
										$topic_rate_form -> hiddenValue( 'do_rate', true);
										
										$votes_ops[] = '<option value="-1">'.$language -> getString( 'topic_rating_none').'</option>';
										$votes_ops[] = '<option value="0">0</option>';
										$votes_ops[] = '<option value="1">1</option>';
										$votes_ops[] = '<option value="2">2</option>';
										$votes_ops[] = '<option value="3">3</option>';
										$votes_ops[] = '<option value="4">4</option>';
										$votes_ops[] = '<option value="5">5</option>';
										$votes_ops[] = '<option value="6">6</option>';
										$votes_ops[] = '<option value="7">7</option>';
										$votes_ops[] = '<option value="8">8</option>';
										$votes_ops[] = '<option value="9">9</option>';
										$votes_ops[] = '<option value="10">10</option>';
										
										$drawed_ops = join( '', $votes_ops);
										
										/**
										 * decide what to do
										 */
										
										$rate_select = $mysql -> query( "SELECT * FROM topics_votes WHERE `topic_id` = '$topic_to_show' AND `topic_vote_user` = '".$session -> user['user_id']."'");
										
										if ( $rate_result = mysql_fetch_array( $rate_select, MYSQL_ASSOC)){
										
											//do nothing
										
										}else{
											
											$rate_result = false;
											
										}
										
										if ( $session -> checkForm() && $_POST['do_rate']){
											
											/**
											 * get vote
											 */
											
											$vote_scale = $_POST['topic_rating'];
											
											settype( $vote_scale, 'integer');
											
											if ( $vote_scale >= -1 && $vote_scale <= 10){
												
												/**
												 * check if we already voted
												 */
												
												if ( $rate_result == false && $vote_scale >= 0){
													
													/**
													 * do new vote
													 */
													
													$new_vote_sql['topic_id'] = $topic_to_show;
													$new_vote_sql['topic_vote'] = $vote_scale;
													$new_vote_sql['topic_vote_user'] = $session -> user['user_id'];
													
													$mysql -> insert( $new_vote_sql, 'topics_votes');
													
													$topic_update_sql['topic_score'] = $topic_result['topic_score'] + $vote_scale;
													$topic_update_sql['topic_votes'] = $topic_result['topic_votes'] + 1;
													
													$topic_result['topic_score'] = $topic_result['topic_score'] + $vote_scale;
													$topic_result['topic_votes'] = $topic_result['topic_votes'] + 1;
													
												}else if ( $vote_scale == -1){
													
													/**
													 * delete vote
													 */
													
													$mysql -> delete( 'topics_votes', "`topic_id` = '$topic_to_show' AND `topic_vote_user` = '".$session -> user['user_id']."'");
													
													$topic_update_sql['topic_score'] = $topic_result['topic_score'] - $rate_result['topic_vote'];
													$topic_update_sql['topic_votes'] = $topic_result['topic_votes'] - 1;
													
													$topic_result['topic_score'] = $topic_result['topic_score'] - $rate_result['topic_vote'];
													$topic_result['topic_votes'] = $topic_result['topic_votes'] - 1;
													
												}else{
													
													/**
													 * update existing one
													 */
													
													$up_vote_sql['topic_vote'] = $vote_scale;
													
													$mysql -> update( $up_vote_sql, 'topics_votes', "`topic_id` = '$topic_to_show' AND `topic_vote_user` = '".$session -> user['user_id']."'");
													
													$topic_update_sql['topic_score'] = $topic_result['topic_score'] - $rate_result['topic_vote'] + $vote_scale;
													$topic_result['topic_score'] = $topic_result['topic_score'] - $rate_result['topic_vote'] + $vote_scale;
													
												}
												
												$rate_result['topic_vote'] = $vote_scale;
												
											}
										}
										
										if ( strlen( $rate_result['topic_vote']) == 0)
											$rate_result['topic_vote'] = -1;
										
										$drawed_ops = str_ireplace( 'value="'.$rate_result['topic_vote'].'"', 'value="'.$rate_result['topic_vote'].'" selected', $drawed_ops);
										
										/**
										 * draw rest
										 */
										
										$topic_rate_form -> addToContent( '<select id="topic_rating" name="topic_rating">'.$drawed_ops.'</select> <a href="javascript:doTopicRate()">'.$style -> drawImage( 'button_go', $language -> getString( 'post_moderation_do')).'</a>');
										
										$topic_rate_form -> closeForm();
										$topic_rate_form -> addToContent( '<script type="text/JavaScript">
					
											function doTopicRate(){
												
												rate_select_form = document.forms[\'topic_rate_form\']
												rate_select_form.submit()	
												
											}
												
										
										</script>');
										
										$topic_rating_html .= $topic_rate_form -> display();
									
										$topic_ops -> drawButton( '<b>'.$language -> getString( 'topic_ratings').':</b> '.$forums -> drawTopicRating( $topic_result['topic_score'], $topic_result['topic_votes']));
									
										$topic_ops -> drawButton( $topic_rating_html);
									
									}
									
									/**
									 * mod ops
									 */
										
									if ( count( $topic_mod_ops) > 0){
										
										$topic_ops -> drawSpacer( $language -> getString( 'topic_moderation'));
										
										foreach ( $topic_mod_ops as $mod_do => $mod_label)
										$topic_ops -> drawButton( '<a href="'.parent::systemLink( 'mod', array('topic' => $topic_to_show, 'do' => $mod_do)).'">'.$mod_label.'</a>');
										
									}
									
									/**
									 * draw head witch ops
									 */
											
									$topic_head = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
										  <tr>
										    <td style="width: 100%;">'.$title_prefix.$topic_result['topic_name'].'</td>
										    <td nowrap="nowrap">'.$topic_ops -> display( $language -> getString( 'topic_options'), 'topic').'</td>
										  </tr>
										</table>';
								
								}else{
									
									$topic_head = $title_prefix.$topic_result['topic_name'];
									
								}
									
								/**
								 * display generated list
								 */
								
								parent::draw( $style -> drawFormBlock( $topic_head, $topic_posts -> display()));
							
							}
								
							/**
							 * draw options
							 */
												
							parent::draw('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 6px;">
							  <tr>
							    <td style="text-align: left">'.$paginator_html.'</td>
							    <td style="text-align: right">'.join( " ", $topic_actions).'</td>
							  </tr>
							</table>');
							
							/**
							 * draw quick reply
							 */
							
							if ( !defined( 'SIMPLE_MODE')){
							
								if ( $forum_result['forum_allow_quick_reply'] && ($settings['quick_reply_avaibility'] == 1 || $settings['quick_reply_avaibility'] == 2) && $session -> canReplyTopics( $forum_result['forum_id']) && ((!$forum_result['forum_locked'] && !$topic_result['topic_closed']) || $session -> user['user_avoid_closed_topics'] || $session -> isMod( $forum_result['forum_id']))){
									
									$quick_reply = new form();
									$quick_reply -> openForm( parent::systemLink( 'new_post', array( 'topic' => $topic_to_show)));
									
									if ( $session -> user['user_id'] == -1){
										
										$captcha_key = $captcha -> generate();
										
										$quick_reply -> hiddenValue( 'captcha', $captcha_key);
									}
									
									$quick_reply -> openOpTable();
									
									/**
									 * user login
									 */
									
									if ( $session -> user['user_id'] == -1)
										$quick_reply -> drawTextInput( $language -> getString( 'new_topic_user'), 'post_author');
									
									/**
									 * editor
									 */
									
									$quick_reply -> drawEditor( $language -> getString( 'fast_reply_text'), 'post_text', null, null, $forum_result['forum_allow_bbcode'], true);
									
									if ( $session -> user['user_id'] == -1)
										$quick_reply -> addToContent( $captcha -> drawForm($captcha_key));
									
									$quick_reply -> closeTable();
									$quick_reply -> drawButton( $language -> getString( 'fast_reply_send'));
									$quick_reply -> closeForm();
										
									if ( $settings['quick_reply_visibility']){					
									
										parent::draw('<div id="fast_reply" style="display: none"><a name="quick_reply">&nbsp;</a>');
									
									}else{
										
										parent::draw('<div id="fast_reply"><a name="quick_reply">&nbsp;</a>');
									
									}
									
									
									parent::draw( $style ->drawFormBlock( $language -> getString('fast_reply_block'), $quick_reply -> display()));
									parent::draw('</div>');
									
									if ( $settings['quick_reply_visibility']){					
									
										parent::draw('<script type="text/JavaScript">
										
											function openQuickReply(){
											
												quick_reply_form = document.getElementById( \'fast_reply\')
												
												if( quick_reply_form.style.display == ""){
													
													quick_reply_form.style.display = "none"
												
												}else{
													
													quick_reply_form.style.display = ""
													window.location.hash = "quick_reply"
											
												}
												
											}
										
										</script>');
									
									}else{
										
										parent::draw('<script type="text/JavaScript">
										
											function openQuickReply(){
											
												quick_reply_form = document.getElementById( \'fast_reply\')
												
												window.location.hash = "quick_reply"
											
											}
										
										</script>');
									
									}
									
								}
													
								/**
								 * update reading
								 */
								
								if ( $session -> user['user_id'] > 0 && $topic_read < $topic_result['topic_last_time']){
								
									$mysql -> delete( 'topics_reads', "`topic_read_forum` = '".$forum_result['forum_id']."' AND `topic_read_user` = '".$session -> user['user_id']."' AND `topic_read_topic` = '$topic_to_show'");
								
									$mysql -> insert( array( 'topic_read_forum' => $forum_result['forum_id'], 'topic_read_user' => $session -> user['user_id'], 'topic_read_topic' => $topic_to_show, 'topic_read_time' => time()), 'topics_reads');
									
									$forums -> checkForumRead( $forum_result['forum_id'], $forum_result['forum_threads']);
																
								}
								
								/**
								 * tags
								 */
								
								if ( $settings['forum_allow_tags'] && !defined( 'SIMPLE_MODE')){
									
									/**
									 * tags tools
									 */
									
									if ( $session -> isMod( $forum_result['forum_id']) || ($session -> user['user_id'] != -1 && $session -> user['user_id'] == $topic_result['topic_start_user'])){
										
										$tags_tools = '<a href="javascript:loadTagsEditor()">'.$style -> drawImage( 'small_edit').'</a>';
										$tags_java = '<script language="JavaScript" type="text/javascript">
		
												function loadTagsEditor(){
												
													//get tools id
													tools_div = document.getElementById( \'tags_tools\');
												
													//get tags id
													tags_div = document.getElementById( \'tags_screen\');
																		
													//orginal tools content
													tools_org = tools_div.innerHTML;
													
													//new ajax object
													uniAJAX = GetXmlHttpObject();
												
													uniAJAX.onreadystatechange = function(){
													
														if(uniAJAX.readyState == 4){
															
															//clear loader
															tools_div.innerHTML = "<a href=\"javascript:saveTags()\">'.addslashes( $style -> drawImage( 'small_save')).'</a><a href=\"javascript:reloadTags()\">'.addslashes( $style -> drawImage( 'small_delete')).'</a>";
																								
															//write new content
															tags_div.innerHTML = uniAJAX.responseText;
																												
															
														}else{
																											
															//set loader
															tools_div.innerHTML = tools_org + " '.str_replace( '"', '\"', $style -> drawImage( 'small_loader')).'";
														
														}
													}
																						
													uniAJAX.open( "GET", "'.parent::systemLink( parent::getId(), array( 'smode' => 2, 'topic' => $topic_to_show, 'do' => 0)).'" , true);
													uniAJAX.send( null);
										
												}
												
												function saveTags(){
												
													//get tools id
													tools_div = document.getElementById( \'tags_tools\');
												
													//get tags id
													tags_div = document.getElementById( \'tags_screen\');
																		
													//get tags editor
													tags_editor = document.getElementById( \'topic_tags_ed\');
													
													//orginal tools content
													tools_org = tools_div.innerHTML;
													
													//new ajax object
													uniAJAX = GetXmlHttpObject();
												
													uniAJAX.onreadystatechange = function(){
													
														if(uniAJAX.readyState == 4){
															
															//clear loader
															tools_div.innerHTML = "<a href=\"javascript:loadTagsEditor()\">'.addslashes( $style -> drawImage( 'small_edit')).'</a>";
																								
															//write new content
															tags_div.innerHTML = uniAJAX.responseText;
																												
															
														}else{
																											
															//set loader
															tools_div.innerHTML = tools_org + " '.str_replace( '"', '\"', $style -> drawImage( 'small_loader')).'";
														
														}
													}
																						
													uniAJAX.open( "POST", "'.parent::systemLink( parent::getId(), array( 'smode' => 2, 'topic' => $topic_to_show, 'do' => 1)).'" , true);
													uniAJAX.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
													uniAJAX.send( "tags=" + encodeURIComponent( tags_editor.value));
												
												}	
												
												function reloadTags(){
												
													//get tools id
													tools_div = document.getElementById( \'tags_tools\');
												
													//get tags id
													tags_div = document.getElementById( \'tags_screen\');
																		
													//orginal tools content
													tools_org = tools_div.innerHTML;
													
													//new ajax object
													uniAJAX = GetXmlHttpObject();
												
													uniAJAX.onreadystatechange = function(){
													
														if(uniAJAX.readyState == 4){
															
															//clear loader
															tools_div.innerHTML = "<a href=\"javascript:loadTagsEditor()\">'.addslashes( $style -> drawImage( 'small_edit')).'</a>";
																								
															//write new content
															tags_div.innerHTML = uniAJAX.responseText;
																												
															
														}else{
																											
															//set loader
															tools_div.innerHTML = tools_org + " '.str_replace( '"', '\"', $style -> drawImage( 'small_loader')).'";
														
														}
													}
																						
													uniAJAX.open( "GET", "'.parent::systemLink( parent::getId(), array( 'smode' => 2, 'topic' => $topic_to_show, 'do' => 2)).'" , true);
													uniAJAX.send( null);
										
												}
												
											</script>';
									
									}else{
										
										$tags_tools = '';
										$tags_java = '';
										
									}
									
									/**
									 * draw tab
									 */
									
									$tags_list = split( "\n", $topic_result['topic_tags']);
									
									if ( strlen( $topic_result['topic_tags']) > 0){
										
										/**
										 * parse tags
										 */
										
										foreach ( $tags_list as $tag_name){
											
											$parsed_tags[] = '<a href="'.parent::systemLink( 'search', array( 'do' => 'tagged', 'tag' => urlencode( trim($tag_name)))).'">'.trim($tag_name).'</a>';
											
										}
										
										parent::draw( $style -> drawBlankBlock( '<div id="tags_screen">'.join( ', ', $parsed_tags).'</div><div id="tags_tools" style="text-align: right">'.$tags_tools.'</div>'.$tags_java));
									
									}else{
									
										parent::draw( $style -> drawBlankBlock( '<div id="tags_screen">'.$language -> getString( 'topic_notags').'</div><div id="tags_tools" style="text-align: right">'.$tags_tools.'</div>'.$tags_java));
									
									}
								}
								
								/**
								 * online
								 */
								
								if ( $settings['draw_online_topic'] && $settings['users_count_online']){
									
									$users_browsing = array();
									
									$bots = 0;
									$guests = 0;
									$users = 0;
									$hidden = 0;
									
									$users_browsing_sql = $mysql -> query( "SELECT s.*, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM users_sessions s
									LEFT JOIN users u ON s.users_session_user_id = u.user_id
									LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
									WHERE s.users_session_location_topic = '".$topic_to_show."'
									ORDER BY s.users_session_bot, u.user_login");
									
									while ( $users_browsing_result = mysql_fetch_array( $users_browsing_sql, MYSQL_ASSOC)){
										
										//clear result
										$users_browsing_result = $mysql -> clear( $users_browsing_result);
										
										/**
										 * check if user is guest
										 */
										
										if ( $users_browsing_result['users_session_user_id'] == -1){
											
											if ( $users_browsing_result['users_session_bot']){
												
												/**
												 * bot session
												 */
												
												$bots ++;
												
												if ( $settings['spiders_draw_online']){
													
													$drawed_bots = array();
		
													if ( !in_array( $users_browsing_result['users_session_bot_name'], $drawed_bots)){
																	
														$drawed_bots[$users_browsing_result['users_session_bot_name']] ++;
																
													}
													
													foreach ( $drawed_bots as $bot_name => $bot_nums){
														
														if ( $bot_nums > 1){
															$users_browsing[] = '<span class="online_bot">'.$bot_name.' ('.$bot_nums.')</span>';
														}else{
															$users_browsing[] = '<span class="online_bot">'.$bot_name.'</span>';
														}
													}
												}
																				
											}else{
												
												$guests ++;
												
											}
											
										}else{
											
											/**
											 * is user hidden
											 */
											
											if ( $users_browsing_result['users_session_hidden'] && ($session -> user['user_see_hidden'] || $users_browsing_result['users_session_user_id'] == $session -> user['user_id'])){
												
												/**
												 * user is hidden
												 */
												
												$users_browsing[] = '<a href="'.parent::systemLink( 'user', array( 'user' => $users_browsing_result['users_session_user_id'])).'">'.$users_browsing_result['users_group_prefix'].$users_browsing_result['user_login'].$users_browsing_result['users_group_suffix'].'</a>*';
												
												$hidden ++;
												
											}else{
												
												/**
												 * user is visible
												 */
												
												$users_browsing[] = '<a href="'.parent::systemLink( 'user', array( 'user' => $users_browsing_result['users_session_user_id'])).'">'.$users_browsing_result['users_group_prefix'].$users_browsing_result['user_login'].$users_browsing_result['users_group_suffix'].'</a>';
												
												$users++;
												
											}
											
										}
										
									}
									
									if ( $users == 0 && $hidden == 0)
										$users_browsing[] = '<i>'.$language -> getString( 'topics_online_list_none').'</i>';
									
									$language -> setKey( 'forum_views_total', $bots + $guests + $users + $hidden);
									$language -> setKey( 'forum_views_bot', $bots);
									$language -> setKey( 'forum_views_guests', $guests);
									$language -> setKey( 'forum_views_logged_in', $users);
									$language -> setKey( 'forum_views_hidden', $hidden);
										
									parent::draw( $style -> drawBlock( $language -> getString( 'topics_online_list_summary'), join( ", ", $users_browsing)));
									
								}
								
								/**
								 * topic side tools
								 */
								
								$side_tools = array();
																
								/**
								 * and jump list
								 */
								
								if ( $settings['forum_draw_forums_jumplist']){
									
									$side_tools[] = '<b>'.$language -> getString( 'forums_jump').':</b><br />'.$forums -> drawForumsJumpList( $topic_result['topic_forum_id']);
									
								}
								
								/**
								 * now mod tools
								 */
								
								$topic_mod_tools = array();
								
								/**
								 * posts tools
								 */
								
								if ( $session -> isMod( $forum_result['forum_id'])){
									
									$posts_mod_ops = array();
									
									$posts_mod_ops[] = '<option value="0">'.$language -> getString( 'post_moderation_merge').'</option>';
									$posts_mod_ops[] = '<option value="1">'.$language -> getString( 'post_moderation_move').'</option>';
									$posts_mod_ops[] = '<option value="2">'.$language -> getString( 'post_moderation_split').'</option>';
									$posts_mod_ops[] = '<option value="3">'.$language -> getString( 'post_moderation_delete').'</option>';
								
									$topic_mod_tools[] = '<b>'.$language -> getString( 'post_moderation').'</b><br /><select id="topic_posts_moderation" name="topic_posts_moderation">'.join( '', $posts_mod_ops).'</select> <a href="javascript:doModPostsAction()">'.$style -> drawImage( 'button_go', $language -> getString( 'post_moderation_do')).'</a><script type="text/JavaScript">
					
										function doModPostsAction(){
										
											action_select = document.getElementById(\'topic_posts_moderation\')
											posts_list = document.forms[\'posts_list\']
											
											if( action_select.value == 0){
			
												//open topic	
												
												posts_list.action = "'.parent::systemLink( 'mod', array( 'do' => 'merge')).'"
												posts_list.submit()	
																			
											}else if( action_select.value == 1){
			
												//close topic	
												
												posts_list.action = "'.parent::systemLink( 'mod', array( 'do' => 'move')).'"
												posts_list.submit()	
																			
											}else if( action_select.value == 2){
			
												//move topic	
												
												posts_list.action = "'.parent::systemLink( 'mod', array( 'do' => 'split')).'"
												posts_list.submit()	
																		
											}else if( action_select.value == 3){
			
												//delete topic	
												
												posts_list.action = "'.parent::systemLink( 'mod', array( 'do' => 'delete')).'"
												posts_list.submit()	
																			
											}		
										
										}
											
									
									</script>';
									
								}
																
								/**
								 * draw ops block
								 */
								
								$ops_block_content = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
								  <tr>
								    <td nowrap="nowrap" style="vertical-align: top">'.join( '<br />', $side_tools).'</td>
								    <td style="width:100%">&nbsp</td>
								    <td nowrap="nowrap" style="vertical-align: top">'.join( '<br />', $topic_mod_tools).'</td>
								  </tr>
								</table>';
								
								if ( count( $side_tools) > 0 || count($topic_mod_tools) > 0)
									parent::draw( $style -> drawBlankBlock( $ops_block_content));
								
							}
								
							/**
							 * update reads num
							 */
							
							$topic_update_sql['topic_views_num'] = ($topic_result['topic_views_num'] + 1);
							
							$mysql -> update( $topic_update_sql, 'topics', "`topic_id` = '$topic_to_show'");

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
						 * topic ajax code
						 */
						
						if ( $session -> isMod( $forum_result['forum_id']) || ($session -> user['user_id'] != -1 && $session -> user['user_id'] == $topic_result['topic_start_user'])){
								
							switch ( $_GET['do']){
								
								case 0:
									
									parent::draw( '<textarea id="topic_tags_ed" name="topic_tags_ed" cols="60" rows="8">'.$topic_result['topic_tags'].'</textarea>');
									
								break;
								
								case 1:
									
									$tags = $strings -> inputClear( $_POST['tags'], false);
										
									$tags_array = split( "\n", $tags);
									$tags = array();
																		
									foreach ( $tags_array as $tag){
										
										$tag = strtolower( trim( $tag));
										
										if ( strlen( $tag) > 0 && !in_array( $tag, $tags))
											$tags[] = $tag;
										
									}
																	
									$mysql -> update( array( 'topic_tags' => $strings -> inputClear( join( "\n", $tags), false)), 'topics', "`topic_id` = '$topic_to_show'");
																		
									if ( strlen( stripslashes( $tags)) > 0){
										
										/**
										 * parse tags
										 */
										
										foreach ( $tags as $tag_name){
											
											$parsed_tags[] = '<a href="'.parent::systemLink( 'search', array( 'do' => 'tagged', 'tag' => urlencode( $tag_name))).'">'.$tag_name.'</a>';
											
										}
										
										parent::draw( join( ', ', $parsed_tags));
									
									}else{
									
										parent::draw( $language -> getString( 'topic_notags'));
									
									}
									
								break;
								
								case 2:
									
									$tags_list = split( "\n", $topic_result['topic_tags']);
									
									if ( strlen( $topic_result['topic_tags']) > 0){
										
										/**
										 * parse tags
										 */
										
										foreach ( $tags_list as $tag_name){
											
											$parsed_tags[] = '<a href="'.parent::systemLink( 'search', array( 'tag' => urlencode( $tag_name))).'">'.$tag_name.'</a>';
											
										}
										
										parent::draw( join( ', ', $parsed_tags));
									
									}else{
									
										parent::draw( $language -> getString( 'topic_notags'));
									
									}
									
								break;
								
							}
						}
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