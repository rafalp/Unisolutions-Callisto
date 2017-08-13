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
|	Show forum
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

class action_show_forum extends action{
		
	/**
	 * this forum id
	 *
	 */
	
	var $forum_to_show;
	
	/**
	 * forum result
	 *
	 */
	
	var $forum_result = array();
	
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check, if forum exists
		 */
		
		$forum_to_show = $_GET['forum'];
		
		settype( $forum_to_show, 'integer');
		
		if ( $forum_to_show < 0){
			
			$forum_to_show = 0;
			
		}
		
		$this -> forum_to_show = $forum_to_show;
		
		if ( key_exists( $forum_to_show, $forums -> forums_list) && $session -> canSeeForum( $forum_to_show)){
			
			/**
			 * get forum data
			 */
			
			$forum_query = $mysql -> query( "SELECT * FROM forums WHERE `forum_id` = '$forum_to_show'");
			
			if ( $forum_result = mysql_fetch_array( $forum_query, MYSQL_ASSOC)){
				
				//clear result
				$forum_result = $mysql -> clear( $forum_result);
					
				$this -> forum_result = $forum_result;
				
				/**
				 * do switch
				 */
				
				switch ( $forum_result['forum_type']){
					
					case 0:
						
						/**
						 * forum is category
						 * check, if we can see content
						 */
						
						if ( $session -> canSeeTopics( $forum_to_show)){
							
							/**
							 * user can see forum content
							 * draw tree
							 */
														
							if ( !defined( 'SIMPLE_MODE')){
							
								$this -> setEssentials();
								
								/**
								 * and subforums
								 */
								
								parent::draw( $forums -> drawForumsList( $forum_to_show));
								
								if ( $settings['forum_draw_forums_jumplist']){
									
									parent::draw( $style -> drawBlankBlock( '<b>'.$language -> getString( 'forums_jump').':</b><br />'.$forums -> drawForumsJumpList( $forum_to_show)));
									
								}
								
							}else{
								
								parent::draw( $forums -> drawForumsList( 0));
								
							}
							
						}else{
							
							/**
							 * no access to forum content
							 */
							
							if ( defined( 'SIMPLE_MODE')){
								
								parent::draw( $language -> getString( 'forums_error_noreading'));
															
							}else{
							
								$main_error = new main_error();
								$main_error -> type = 'error';
								parent::draw( $main_error -> display());
								
							}
								
						}
												
					break;
					
					case 1:
						
						/**
						 * forum is forum
						 * check, if we can see content
						 */
						
						if ( $session -> canSeeTopics( $forum_to_show)){
							
							/**
							 * we can see forum content
							 */
							
							$this -> setEssentials();
								
							/**
							 * resynch
							 */
										
							if ( $session -> user['user_can_be_admin'] && $_GET['do'] == 'resynch')
								$forums -> forumResynchronise( $forum_to_show);
										
							/**
							 * subforums
							 */
							
							if ( !defined( 'SIMPLE_MODE')){
								parent::draw( $forums -> drawForumsList( $forum_to_show));
								
								/**
								 * forum subscription
								 */											
								
								if ( $session -> user['user_id'] != -1 && $settings['subscriptions_turn']){
									
									$subscription_sql = $mysql -> query( "SELECT * FROM subscriptions_forums WHERE `subscription_forum` = '$forum_to_show' AND `subscription_forum_user` = '".$session -> user['user_id']."'");
									
									if ( $subscription_result = mysql_fetch_array( $subscription_sql, MYSQL_ASSOC)){
									
										$subscribed = true;
										
										if ( $_GET['do'] == 'unsubscribe'){
											
											$mysql -> delete( 'subscriptions_forums', "`subscription_forum` = '$forum_to_show' AND `subscription_forum_user` = '".$session -> user['user_id']."'");
											
											$subscribed = false;
										
										}else{
											
											/**
											 * update
											 */
											
											$mysql -> update( array( 'subscription_forum_time' => time(), 'subscription_forum_topics' => $forum_result['forum_threads'], 'subscription_forum_posts' => $forum_result['forum_posts']), 'subscriptions_forums', "`subscription_forum_id` = '".$subscription_result['subscription_forum_id']."'");
																					
										}
									
									}else{
										
										$subscribed = false;
										
										if ($_GET['do'] == 'subscribe'){
									
											$mysql -> delete( 'subscriptions_forums', "`subscription_forum` = '$forum_to_show' AND `subscription_forum_user` = '".$session -> user['user_id']."'");
											$mysql -> insert( array( 'subscription_forum' => $forum_to_show, 'subscription_forum_user' => $session -> user['user_id'], 'subscription_forum_time' => 0, 'subscription_forum_topics' => $forum_result['forum_threads'], 'subscription_forum_posts' => $forum_result['forum_posts']), 'subscriptions_forums');
											
											$subscribed = true;
											
										}
										
									}
									
								}
							
							}	
							
							/**
							 * count paging
							 */
							
							if ( $settings['forum_topics_per_page'] < 1)
								$settings['forum_topics_per_page'] = 1;
							
							$pages_number = ceil( $forum_result['forum_threads'] / $settings['forum_topics_per_page']);
							
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
							 * set ordering method
							 */
							
							if ( isset( $_GET['o'])){
								
								$ordering_method = $_GET['o'];
							
							}else{
								
								$ordering_method = $forum_result['forum_force_ordering'];
								
							}
													
							settype( $ordering_method, 'integer');
							
							if ( $ordering_method < 0)
								$ordering_method = 0;
							
							if ( $ordering_method > 6)
								$ordering_method = 6;
								
							$ordering_method_sql[0] = 't.topic_last_time';
							$ordering_method_sql[1] = 't.topic_name';
							$ordering_method_sql[2] = 't.topic_start_user_name';
							$ordering_method_sql[3] = 't.topic_posts_num';
							$ordering_method_sql[4] = 't.topic_views_num';
							$ordering_method_sql[5] = 't.topic_start_time';
							$ordering_method_sql[6] = 't.topic_last_user_name';
								
							/**
							 * and direction
							 */
								
							if ( isset( $_GET['d'])){
								
								$ordering_direction = $_GET['d'];
							
							}else{
								
								$ordering_direction = $forum_result['forum_ordering_way'];
							
							}
															
							settype( $ordering_direction, 'integer');
							
							if ( $ordering_direction < 0)
								$ordering_direction = 0;
									
							if ( $ordering_direction > 1)
								$ordering_direction = 1;
													
							$ordering_direction_sql[0] = 'DESC';
							$ordering_direction_sql[1] = 'ASC';
							
							/**
							 * paginator html
							 */
										
							$paginator_html = $style -> drawPaginator( parent::systemLink( parent::getId(), array( 'forum' => $forum_to_show, 'o' => $ordering_method, 'd' => $ordering_direction)), 'p', $pages_number, ( $page_to_draw + 1));
									
							/**
							 * forum simple/normal link
							 */
							
							if ( defined( 'SIMPLE_MODE')){
								
								$style -> drawString( 'LINK_MODE_CHANGE', ROOT_PATH.'index.php?act=forum&forum='.$forum_to_show.'&p='.( $page_to_draw + 1).'&d='.$ordering_direction.'&o='.$ordering_method);
						
							}else{
								
								$style -> drawString( 'LINK_MODE_CHANGE', ROOT_PATH.SIMPLE_PATH.'index.php?act=forum&forum='.$forum_to_show.'&p='.( $page_to_draw + 1).'&d='.$ordering_direction.'&o='.$ordering_method);
								
							}
							
							/**
							 * buttons now
							 */
							
							if ( !defined( 'SIMPLE_MODE')){
								
								if ( $forum_result['forum_locked']){
								
									/**
									 * forum is locked, check if we have perms
									 */
									
									if ( $session -> user['user_avoid_closed_topics']){
										
										$buttons_html = '<a href="'.parent::systemLink( 'new_topic', array( 'forum' => $forum_to_show)).'">'.$style -> drawImage( 'button_closed_topic', $language -> getString( 'topics_new_button')).'</a>';
										
									}else{
									
										$buttons_html = $style -> drawImage( 'button_closed_topic', $language -> getString( 'topics_new_button_locked'));
									
									}
									
								}else{
	
									$buttons_html = '<a href="'.parent::systemLink( 'new_topic', array( 'forum' => $forum_to_show)).'">'.$style -> drawImage( 'button_new_topic', $language -> getString( 'topics_new_button')).'</a>';
								
								}					
								
							}else{
								
								$buttons_html = '';
							
							}
								
							/**
							 * split page in two, and draw ops
							 */

							if ( !defined( 'SIMPLE_MODE')){
																				
								parent::draw('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 6px">
									  <tr>
									    <td style="text-align: left">'.$paginator_html.'</td>
									    <td style="text-align: right">'.$buttons_html.'</td>
									  </tr>
									</table>');
										
									/**
									 * begin drawing topics list
									 */
									
									$topics_list = new form();
									
									if ( $session -> isMod( $forum_to_show))
										$topics_list -> openForm( parent::systemLink( 'mod'), 'POST', false, 'topics_list');
									
									$topics_list -> openOpTable();
									
									if ( $settings['forum_topics_table_head']){
										
										$topics_list -> addToContent( '<tr>
											<th>&nbsp;</th>
											<th NOWRAP="nowrap">'.$language -> getString( 'topics_name').'</th>');
										
										if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist'])
											$topics_list -> addToContent( '<th style="width: 100px">'.$language -> getString( 'topics_rating').'</th>');
											
										$topics_list -> addToContent( '
											<th NOWRAP="nowrap">'.$language -> getString( 'topics_posts').'</th>
											<th NOWRAP="nowrap">'.$language -> getString( 'topics_author').'</th>
											<th NOWRAP="nowrap">'.$language -> getString( 'topics_views').'</th>
											<th NOWRAP="nowrap">'.$language -> getString( 'topics_last_reply').'</th>');
										
										if ( $session -> isMod( $forum_to_show))
											$topics_list -> addToContent( '<th style="width: 24px">&nbsp;</th>');
										
										$topics_list -> addToContent( '</tr>');
							
									}
									
							}else{
								
								parent::draw( '<ol>');
								
							}
															
							if ( $forum_result['forum_threads'] > 0 ){
							
								/**
								 * select reads
								 */
								
								$topics_reads = array();
								
								if ( $session -> user['user_id'] != -1){
								
									$reads_query = $mysql -> query( "SELECT * FROM `topics_reads` WHERE `topic_read_forum` = '$forum_to_show' AND `topic_read_user` = '".$session -> user['user_id']."'");
									
									while ( $reads_result = mysql_fetch_array( $reads_query, MYSQL_ASSOC))
										$topics_reads[$reads_result['topic_read_topic']] = $reads_result['topic_read_time'];
									
								}
								
								/**
								 * are we in important topics
								 */
									
								$in_inportants = false;
																
								/**
								 * select topics
								 * even if forum is empty, there still can were any global topics
								 */
								
								$topics_query = $mysql -> query( "SELECT t.*, us.user_id, us.user_login, us.user_main_group, us.user_other_groups, gs.users_group_prefix, gs.users_group_suffix, lu.user_id as last_user_id, lu.user_login as last_user_login, lg.users_group_prefix as last_users_group_prefix, lg.users_group_suffix as last_users_group_suffix
								FROM topics t
								LEFT JOIN users us ON us.user_id = t.topic_start_user
								LEFT JOIN users_groups gs ON gs.users_group_id = us.user_main_group
								LEFT JOIN users lu ON lu.user_id = t.topic_last_user
								LEFT JOIN users_groups lg ON lg.users_group_id = lu.user_main_group
								WHERE t.topic_forum_id = '$forum_to_show'
								ORDER BY t.topic_type DESC, ".$ordering_method_sql[$ordering_method]." ".$ordering_direction_sql[$ordering_direction]."
								LIMIT ".($page_to_draw * $settings['forum_topics_per_page']).", ".$settings['forum_topics_per_page']);
								
								while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
									
									//clear result
									$topics_result = $mysql -> clear( $topics_result);
	
									if ( !defined( 'SIMPLE_MODE')){
																	
										/**
										 * topic icon
										 */
											
										$topic_goto = '';
										
										if ( $session -> user['user_id'] == -1){
											
											/**
											 * we are guests, co topic will always be readed
											 */
											
											if ( $topics_result['topic_closed']){
												
												$topic_image = $style -> drawImage( 'topic_closed', $language -> getString( 'topic_type_closed'));
											
											}else if ( $settings['forum_hot_topic'] > 0 && $topics_result['topic_posts_num'] >= $settings['forum_hot_topic']){
												
												$topic_image = $style -> drawImage( 'topic_popular', $language -> getString( 'topic_type_popular'));
												
											}else{
												
												$topic_image = $style -> drawImage( 'topic', $language -> getString( 'topic_type'));
												
											}
											
										}else{
											
											/**
											 * check if topic is read
											 */
											
											if ( $topics_result['topic_last_time'] > $topics_reads[$topics_result['topic_id']]){
												
												if ( $topics_result['topic_closed']){
													
													$topic_image = $style -> drawImage( 'topic_closed_new', $language -> getString( 'topic_type_closed_new'));
												
												}else if ( $settings['forum_hot_topic'] > 0 && $topics_result['topic_posts_num'] >= $settings['forum_hot_topic']){
													
													$topic_image = $style -> drawImage( 'topic_popular_new', $language -> getString( 'topic_type_popular_new'));
													
												}else{
													
													$topic_image = $style -> drawImage( 'topic_new', $language -> getString( 'topic_type_new'));
													
												}
												
												/**
												 * goto last unread
												 */
												
												$topic_goto = '<a href="'.parent::systemLink( 'topic', array( 'topic' => $topics_result['topic_id'], 'p' => ceil(($topics_result['topic_posts_num'] + 1) / $settings[ 'forum_posts_per_page'] ))).'#post'.$topics_result['topic_last_post_id'].'">'.$style -> drawImage( 'goto', $language -> getString( 'topic_goto_last_unread')).'</a> ';
												
											}else{
												
												if ( $topics_result['topic_closed']){
													
													$topic_image = $style -> drawImage( 'topic_closed', $language -> getString( 'topic_type_closed'));
												
												}else if ( $settings['forum_hot_topic'] > 0 && $topics_result['topic_posts_num'] >= $settings['forum_hot_topic']){
													
													$topic_image = $style -> drawImage( 'topic_popular', $language -> getString( 'topic_type_popular'));
													
												}else{
													
													$topic_image = $style -> drawImage( 'topic', $language -> getString( 'topic_type'));
													
												}
												
											}
											
										}
										
										/**
										 * topic prefix
										 */
										
										$topic_prefix = $forums -> getPrefixHTML( $topics_result['topic_prefix'], $forum_to_show);
										
										if ( strlen( $topic_prefix) == 0){
												
											if ( $topics_result['topic_type'] == 1){
												
												$topic_prefix = '<b>'.$settings['forum_stick_prefix'].':</b> ';
											
											}else if ($topics_result['topic_survey']){
												
												$topic_prefix = '<b>'.$settings['forum_survey_prefix'].':</b> ';
											
											}else{
												
												$topic_prefix = '';
												
											}
											
										}else{
											
											$topic_prefix .= ' ';
											
										}
										
										/**
										 * are we in inportant topics?
										 */
										
										$spacers_span = 6;
										
										if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist'])
											$spacers_span ++;
										
										if ( $topics_result['topic_type'] == 2 && !$in_inportants){
											
											if ( $settings['forum_draw_spacer']){
												
												if ( $session -> isMod( $forum_to_show)){
													
													$spacers_span ++;												
												
												}
											
												$topics_list -> addToContent( '<tr>
														<td class="opt_row4" colspan="'.$spacers_span.'"><b>'.$language -> getString( 'topics_important_topics').'</b></td>
													</tr>');
											}
											
											$in_inportants = true;
											
										}
										
										/**
										 * are we leaving important topics?
										 */
										
										if ( $topics_result['topic_type'] < 2 && $in_inportants){
											
											if ( $settings['forum_draw_spacer']){
												
												if ( $session -> isMod( $forum_to_show)){
													
													$spacers_span ++;												
												
												}
												
												$topics_list -> addToContent( '<tr>
													<td class="opt_row4" colspan="'.$spacers_span.'"><b>'.$language -> getString( 'topics_rest_of_topics').'</b></td>
												</tr>');
												
											}
											
											$in_inportants = false;
											
										}	
										
										/**
										 * topic info
										 */
										
										$topic_info = '';
										
										if ( strlen( $topics_result['topic_info']) > 0 && $settings['topic_info_length'] > -1)
											$topic_info = '<br /><span class="element_info">'.$topics_result['topic_info'].'</span>';
										
										/**
										 * topic tags
										 */
											
										if ( $settings['forum_topics_draw_tags'] && strlen( $topics_result['topic_tags']) > 0){
											
											
											$tags_list = split( "\n", $topics_result['topic_tags']);
											
											foreach ( $tags_list as $tag_id => $tag_name){
												
												$tags_list[$tag_id] = '<a href="'.parent::systemLink( 'search', array( 'do' => 'tagged', 'tag' => urlencode($tag_name))).'">'.$tag_name.'</a>';
												
											}
											
											$topic_tags = '<br /><span class="element_info"><b>'.$language -> getString( 'topics_tags').':</b> '.join( ", ", $tags_list).'</span>';
											
										}else{
											
											$topic_tags = '';
											
										}
										
										/**
										 * topic author
										 */
											
										if ( $topics_result['topic_start_user'] == -1){
										
											/**
											 * starter is an guest
											 */
											
											$topic_author = $topics_result['users_group_prefix'].$topics_result['topic_start_user_name'].$topics_result['users_group_suffix'];
										
										}else{
											
											/**
											 * starter is regitered user
											 */
											
											$topic_author = '<a href="'.parent::systemLink( 'user', array( 'user' => $topics_result['topic_start_user'])).'">'.$topics_result['users_group_prefix'].$topics_result['user_login'].$topics_result['users_group_suffix'].'</a>';
										}
										
										/**
										 * last anwswer
										 */
										
										$topic_last_anwswer = $time -> drawDate( $topics_result['topic_last_time']);
										
										if ( $topics_result['topic_last_user'] == -1){
											
											$topic_last_anwswer .= '<br />'.$topics_result['last_users_group_prefix'].$topics_result['topic_last_user_name'].$topics_result['last_users_group_suffix'];
											
										}else{
											
											$topic_last_anwswer .= '<br /><a href="'.parent::systemLink( 'user', array( 'user' => $topics_result['topic_last_user'])).'">'.$topics_result['last_users_group_prefix'].$topics_result['last_user_login'].$topics_result['last_users_group_suffix'].'</a>';
											
										}
										
										/**
										 * topic paging
										 */
										
										if ( $settings['forum_posts_per_page'] < 1)
											$settings['forum_posts_per_page'] = 1;
										
										$pages_number = ceil( ($topics_result['topic_posts_num']+1) / $settings['forum_posts_per_page']);
										
										$topic_paging = $this -> drawJump( $topics_result['topic_id'], $pages_number);
						
										/**
										 * attachment
										 */
										
										if ( $topics_result['topic_attachments'] && $settings['forum_topics_mark_attachments']){
											
											$topic_attachment = ' '.$style -> drawImage( 'attachment', $language -> getString( 'topics_has_attachments'));
											
										}else{
											
											$topic_attachment = '';
											
										}
										
										/**
										 * censore
										 */
										
										$topic_author_groups = array();
										$topic_author_groups = split( ",", $topics_result['user_other_groups']);
										$topic_author_groups[] = $topics_result['user_main_group'];
										
										if ( !$users -> cantCensore( $topic_author_groups)){
											
											$topics_result['topic_name'] = $strings -> censore( $topics_result['topic_name']);
											$topic_info = $strings -> censore( $topic_info);
											
										}
										
										/**
										 * rating
										 */
										
										if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist']){
											
											$rating_row = '<td class="opt_row2" style="text-align: center; width: 100px">'.$forums -> drawTopicRating( $topics_result['topic_score'], $topics_result['topic_votes']).'</td>';
											
										}else{
											
											$rating_row = '';
											
										}
										
										
										/**
										 * insert row
										 */
										
										$topics_list -> addToContent( '<tr>
											<td class="opt_row2" style="text-align: center; width: 32px">'.$topic_image.'</td>
											<td class="opt_row1" NOWRAP="nowrap">'.$topic_goto.$topic_prefix.'<a href="'.parent::systemLink( 'topic', array( 'topic' => $topics_result['topic_id'])).'" title="'.$topic_result['topic_name'].'">'.$forums -> cutTopicName( $topics_result['topic_name']).'</a>'.$topic_attachment.$topic_paging.$topic_info.$topic_tags.'</td>
											'.$rating_row.'
											<td class="opt_row2" style="text-align: center; width: 80px">'.$topics_result['topic_posts_num'].'</td>
											<td class="opt_row1" style="text-align: center; width: 110px">'.$topic_author.'</td>
											<td class="opt_row2" style="text-align: center; width: 80px">'.$topics_result['topic_views_num'].'</td>
											<td class="opt_row3" style="width: 130px" NOWRAP="nowrap">'.$topic_last_anwswer.'</td>');
											
										if ( $session -> isMod( $forum_to_show))
											$topics_list -> addToContent( '<td class="opt_row3" style="text-align: center; width: 24px">'.$topics_list -> drawSelect( 'topic_select['.$topics_result['topic_id'].']').'</td>');
											
										$topics_list -> addToContent( '</tr>');
										
									}else{
										
										/**
										 * topic prefix
										 */
										
										$topic_prefix = $forums -> getPrefixName( $topics_result['topic_prefix'], $forum_to_show);
										
										if ( strlen($topic_prefix) == 0){
											
											if ( $topics_result['topic_type'] == 1){
												
												$topic_prefix = '<b>'.$settings['forum_stick_prefix'].':</b> ';
											
											}else if ($topics_result['topic_survey']){
												
												$topic_prefix = '<b>'.$settings['forum_survey_prefix'].':</b> ';
											
											}else{
												
												$topic_prefix = '';
												
											}
											
										}else{
											
											$topic_prefix = '<b>'.$topic_prefix.':</b> ';
										
										}
										
										parent::draw( '<li>'.$topic_prefix.'<a href="'.parent::systemLink( 'topic', array( 'topic' => $topics_result['topic_id'])).'" title="'.$topic_result['topic_name'].'">'.$topics_result['topic_name'].'</a>'.$topic_paging.$topic_info.'</li>');
										
									}
									
								}
							
							}else{
								
								/**
								 * draw message
								 */
								
								if ( !defined( 'SIMPLE_MODE')){
								
									$spacers_span = 6;
										
									if ( $session -> isMod( $forum_to_show))
										$spacers_span ++;												
									
									if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist'])
										$spacers_span ++;
										
									$topics_list -> addToContent( '<tr>
										<td class="opt_row1" style="text-align: center" colspan="'.$spacers_span.'">'.$language -> getString( 'forums_last_post_none').'</td>
									</tr>');
							
								}else{
									
									parent::draw( $language -> getString( 'forums_last_post_none'));
									
								}
								
							
							}

							if ( !defined( 'SIMPLE_MODE')){
																					
								/**
								 * close table
								 */
								
								$topics_list -> closeTable();
									
								/**
								 * close mod form
								 */
								
								if ( $session -> isMod( $forum_to_show))
									$topics_list -> closeForm();
								
									
								/**
								 * forum ops
								 */
								
								$forum_ops = array();
								
								/**
								 * forum search
								 */
								
								if ( $session -> user['user_search'] && $session -> user['user_id'] != -1){
									
									$quick_search = new form();
									$quick_search -> openForm( parent::systemLink( 'search', array( 'do' => 'search')));
									$quick_search -> hiddenValue( 'search_forums[]', $forum_to_show);
									
									$quick_search -> addToContent( '<input type="text" name="search_word"> <input type="submit" name="Submit" value="'.$language -> getString( 'forums_search').'">');
									
									$quick_search -> closeForm();
									
									$forum_ops[] = '<td>'.$quick_search -> display().'</td>';
									
								}else{
									
									$forum_ops[] = '<td>&nbsp;</td>';
									
								}
																								
								$language -> setKey( 'forum_posts_num', $forum_result['forum_posts']);
								$language -> setKey( 'forum_topics_num', $forum_result['forum_threads']);
								
								$forum_ops[] = '<td style="text-align: right">'.$language -> getString( 'forums_info').'</td>';
																
								/**
								 * forum ops
								 */
								
								$topics_list -> drawSpacer( '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed"><tr>'.join( '', $forum_ops).'</tr></table>');
	
								/**
								 * forum head
								 */
								
								/**
								 * forum tools
								 */
								
								if ( $session -> user['user_id'] != -1){
									
									$forum_tools = new tools();
									
									/**
									 * forum subscription
									 */
									
									if ($settings['subscriptions_turn']){
										
										/**
										 * select subscription
										 */
										
										if ( $subscribed){
											
											$subscribe_link_title = $language -> getString( 'forums_unsubscribe');
											$subscribe_link	= parent::systemLink( parent::getId(), array( 'forum' => $forum_to_show, 'p' => ($page_to_draw + 1), 'do' => 'unsubscribe'));
																
										}else{
											
											$subscribe_link_title = $language -> getString( 'forums_subscribe');
											$subscribe_link	= parent::systemLink( parent::getId(), array( 'forum' => $forum_to_show, 'p' => ($page_to_draw + 1), 'do' => 'subscribe'));
										
										}
										
										$forum_tools -> drawButton( '<a href="'.$subscribe_link.'">'.$subscribe_link_title.'</a>');
										
									}
									
									$forum_tools -> drawButton( '<a href="'.parent::systemLink( 'mark_read', array( 'forum' => $forum_to_show)).'">'.$language -> getString( 'mark_read_forum_link').'</a>');
									$forum_tools -> drawButton( '<a href="'.parent::systemLink( 'mark_read', array( 'forum' => $forum_to_show, 'unread' => true)).'">'.$language -> getString( 'mark_unread_read_forum_link').'</a>');
									
									/**
									 * are we an admin?
									 */
										
									if ( $session -> user['user_can_be_admin']){
										
										$forum_tools -> drawSpacer( $language -> getString( 'forum_acp_options'));
										$forum_tools -> drawButton( '<a href="'.parent::systemLink( parent::getId(), array( 'forum' => $forum_to_show, 'p' => $page_to_draw + 1, 'do' => 'resynch')).'">'.$language -> getString( 'forum_acp_resynch').'</a>');
									
									}
									
									/**
									 * draw
									 */
												
									$forum_head = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
										  <tr>
										    <td style="width: 100%;">'.$forum_result['forum_name'].'</td>
										    <td nowrap="nowrap">'.$forum_tools -> display( $language -> getString( 'forum_options'), 'forum').'</td>
										  </tr>
										</table>';
									
								}else{
									
									$forum_head = $forum_result['forum_name'];
									
								}
											
								/**
								 * display list
								 */
								
								parent::draw( $style -> drawFormBlock( $forum_head, $topics_list -> display()));
								
							}else{
								
								parent::draw( '</ol>');
								
							}
							
							/**
							 * split page in two, and draw ops
							 */
						
							parent::draw('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 6px">
								  <tr>
								    <td style="text-align: left">'.$paginator_html.'</td>
								    <td style="text-align: right">'.$buttons_html.'</td>
								  </tr>
								</table>');
							
							/**
							 * online
							 */
							
							if ( !defined( 'SIMPLE_MODE')){
															
								if ( $settings['draw_online_forum'] && $settings['users_count_online']){
									
									$users_browsing = array();
									
									$bots = 0;
									$guests = 0;
									$users = 0;
									$hidden = 0;
									
									$users_browsing_sql = $mysql -> query( "SELECT s.*, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM users_sessions s
									LEFT JOIN users u ON s.users_session_user_id = u.user_id
									LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
									WHERE s.users_session_location_forum = '".$forum_to_show."'
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
										$users_browsing[] = '<i>'.$language -> getString( 'forums_online_list_none').'</i>';
									
									$language -> setKey( 'forum_views_total', $bots + $guests + $users + $hidden);
									$language -> setKey( 'forum_views_bot', $bots);
									$language -> setKey( 'forum_views_guests', $guests);
									$language -> setKey( 'forum_views_logged_in', $users);
									$language -> setKey( 'forum_views_hidden', $hidden);
										
									parent::draw( $style -> drawBlock( $language -> getString( 'forums_online_list_summary'), join( ", ", $users_browsing)));
									
								}
								
								/**
								 * mod ops
								 */
								
								if ( $session -> isMod( $forum_to_show)){
									
									$topic_mod_ops = array();
									
									$topic_mod_ops[] = '<option value="0">'.$language -> getString( 'topic_moderation_open').'</option>';
									$topic_mod_ops[] = '<option value="1">'.$language -> getString( 'topic_moderation_close').'</option>';
									$topic_mod_ops[] = '<option value="2">'.$language -> getString( 'topic_moderation_move').'</option>';
									$topic_mod_ops[] = '<option value="7">'.$language -> getString( 'topic_moderation_merge').'</option>';
									$topic_mod_ops[] = '<option value="3">'.$language -> getString( 'topic_moderation_delete').'</option>';
									$topic_mod_ops[] = '<option value="4">'.$language -> getString( 'topic_moderation_normal').'</option>';
									$topic_mod_ops[] = '<option value="5">'.$language -> getString( 'topic_moderation_pin').'</option>';
									$topic_mod_ops[] = '<option value="6">'.$language -> getString( 'topic_moderation_important').'</option>';
							
									$topics_mod = '<b>'.$language -> getString( 'topics_moderation').':</b><br /><select id="topics_moderation" name="topics_moderation">'.join( '', $topic_mod_ops).'</select> <a href="javascript:doModAction()">'.$style -> drawImage( 'button_go', $language -> getString( 'topic_moderation_do')).'</a><script type="text/JavaScript">
			
										function doModAction(){
										
											action_select = document.getElementById(\'topics_moderation\')
											topics_list = document.forms[\'topics_list\']
											
											if( action_select.value == 0){
			
												//open topic	
												
												topics_list.action = "'.parent::systemLink( 'mod', array('topic' => $topic_to_show, 'do' => 'open')).'"
													
												topics_list.submit()
																		
											}else if( action_select.value == 1){
			
												//close topic	
												
												topics_list.action = "'.parent::systemLink( 'mod', array('topic' => $topic_to_show, 'do' => 'close')).'"
												
												topics_list.submit()
																			
											}else if( action_select.value == 2){
			
												//move topic	
												
												topics_list.action = "'.parent::systemLink( 'mod', array('topic' => $topic_to_show, 'do' => 'move')).'"
												
												topics_list.submit()
																			
											}else if( action_select.value == 3){
			
												//delete topic	
												
												topics_list.action = "'.parent::systemLink( 'mod', array('topic' => $topic_to_show, 'do' => 'delete')).'"
												
												topics_list.submit()
																			
											}else if( action_select.value == 4){
			
												//normalize topic	
												
												topics_list.action = "'.parent::systemLink( 'mod', array('topic' => $topic_to_show, 'do' => 'normalize')).'"
												
												topics_list.submit()
																			
											}else if( action_select.value == 5){
			
												//pin topic	
												
												topics_list.action = "'.parent::systemLink( 'mod', array('topic' => $topic_to_show, 'do' => 'pin')).'"
												
												topics_list.submit()
																			
											}else if( action_select.value == 6){
			
												//inportant topic	
												
												topics_list.action = "'.parent::systemLink( 'mod', array('topic' => $topic_to_show, 'do' => 'important')).'"
												
												topics_list.submit()
																			
											}else if( action_select.value == 7){
			
												//inportant topic	
												
												topics_list.action = "'.parent::systemLink( 'mod', array('topic' => $topic_to_show, 'do' => 'merge')).'"
												
												topics_list.submit()
																			
											}	
										
										}
											
									
									</script>
									<br />';
									
								}else{
									
									$topics_mod = '';
									
								}
								
								/**
								 * ordering settings
								 */
									
								$ordering_form = new form();
								$ordering_form -> openForm( '', 'GET');
								
								$ordering_form -> hiddenValue('act', 'forum');
								$ordering_form -> hiddenValue('forum', $forum_to_show);
								$ordering_form -> hiddenValue('p', ($page_to_draw+1));
								
								$ordering_options = '<option value="0">'.$language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_0').'</option>
								<option value="1">'.$language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_1').'</option>
								<option value="2">'.$language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_2').'</option>
								<option value="3">'.$language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_3').'</option>
								<option value="4">'.$language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_4').'</option>
								<option value="5">'.$language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_5').'</option>
								<option value="6">'.$language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_6').'</option>';
										
								$ordering_options = str_ireplace( 'option value="'.$ordering_method.'">', 'option value="'.$ordering_method.'" selected>', $ordering_options);
									
								$ordering_form -> addToContent( '<b>'.$language -> getString( 'forum_change_ordering').':</b><br />');				
								$ordering_form -> addToContent( '<select name="o">'.$ordering_options.'</select>');
								$ordering_form -> addToContent( '<select name="d"><option value="0">'.$language -> getString('forum_default_direction_0').'</option><option value="1">'.$language -> getString('forum_default_direction_1').'</option></select>');
								
								$ordering_form -> addToContent( ' <input value="'.$language -> getString( 'forum_change_ordering_change').'" type="submit" />');
								
								$ordering_form -> closeForm();
								
								/**
								 * now draw legend
								 */
								
								if ( $settings['forum_draw_forums_jumplist']){
									$jump_list = '<b>'.$language -> getString( 'forums_jump').':</b><br />'.$forums -> drawForumsJumpList( $forum_to_show);
								}else{
									$jump_list = '';
								}
																				
								/**
								 * send it to output
								 */
								
								$legend_html = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
								  <tr>
								    <td style="width: 200px; vertical-align: top">
								    	<table width="100%" border="0" cellspacing="4" cellpadding="0">
										  <tr>
										    <td NOWRAP="nowrap">'.$style -> drawImage( 'topic', $language -> getString( 'topic_type')).' '.$language -> getString( 'topic_type').'</td>
										    <td NOWRAP="nowrap">'.$style -> drawImage( 'topic_popular', $language -> getString( 'topic_type_popular')).' '.$language -> getString( 'topic_type_popular').'</td>
										    <td NOWRAP="nowrap">'.$style -> drawImage( 'topic_closed', $language -> getString( 'topic_type_closed')).' '.$language -> getString( 'topic_type_closed').'</td>
										  </tr>
										  <tr>
										    <td NOWRAP="nowrap">'.$style -> drawImage( 'topic_new', $language -> getString( 'topic_type_new')).' '.$language -> getString( 'topic_type_new').'</td>
										    <td NOWRAP="nowrap">'.$style -> drawImage( 'topic_popular_new', $language -> getString( 'topic_type_popular_new')).' '.$language -> getString( 'topic_type_popular_new').'</td>
										    <td NOWRAP="nowrap">'.$style -> drawImage( 'topic_closed_new', $language -> getString( 'topic_type_closed_new')).' '.$language -> getString( 'topic_type_closed_new').'</td>
										  </tr>
										</table>
																	    
								    </td>
								    <td style="width: 100%">
								    	&nbsp;
								    </td>
								    <td style="vertical-align: top", NOWRAP="nowrap">'.$ordering_form -> display().$topics_mod.$jump_list.'</td>
								  </tr>
								</table>';
								
								parent::draw( $style -> drawBlankBlock( $legend_html));
															
								/**
								 * update reads
								 */
								
								$forums -> checkForumRead( $forum_to_show, $forum_result['forum_threads']);
							
							}	
							
						}else{
							
							/**
							 * no access to forum content
							 */
							
							if ( defined( 'SIMPLE_MODE')){
								
								parent::draw( $language -> getString( 'forums_error_noreading'));
															
							}else{
							
								$main_error = new main_error();
								$main_error -> type = 'error';
								parent::draw( $main_error -> display());
								
							}			
						}
						
					break;
					
					case 2:
						
						if ( $session -> canSeeTopics( $forum_to_show)){
						
							/**
							 * forum is redirect
							 * set smode to 1
							 */
							
							$smode = 1;
							
							/**
							 * begin drawing form
							 */
							
							$output -> setRemoteRedirect( $forum_result['forum_url']);
														
							if ( !defined( 'SIMPLE_MODE')){
								
								$redirect_block = new form();
								$redirect_block -> openForm( $forum_result['forum_url'], 'GET');
								$redirect_block -> openOpTable();
								$redirect_block -> drawRow( $language -> getString( 'forums_redirect_message'));
								$redirect_block -> closeTable();
								$redirect_block -> drawButton( $language -> getString( 'forums_redirect_button'));
								$redirect_block -> closeForm();
								
								parent::draw( $style -> drawFormBlock( $forum_result['forum_name'], $redirect_block -> display()));
							
							}else{
								
								parent::draw( $language -> getString( 'forums_redirect_message').'<br/><br /><a href="'.$forum_result['forum_url'].'">'.$language -> getString( 'forums_redirect_button').'</a>');
																
							}
								
							/**
							 * if we have counting, update stats
							 */
							
							if ( $forum_result['forum_count_redirects']){
								
								$new_count = $forum_result['forum_redirects'] + 1;
							
								$update_stats_sql['forum_redirects'] = $new_count;
								
								$mysql -> update( $update_stats_sql, 'forums', "`forum_id` = '$forum_to_show'");
								
							}
						
						}else{
							
							/**
							 * no access to forum content
							 */
							
							if ( defined( 'SIMPLE_MODE')){
								
								parent::draw( $language -> getString( 'forums_error_noreading'));
															
							}else{
							
								$main_error = new main_error();
								$main_error -> type = 'error';
								parent::draw( $main_error -> display());
								
							}			
						}
						
					break;
					
				}
				
			}
			
		}else{
			
			/**
			 * error
			 */
			
			$main_error = new main_error();
			$main_error -> type = 'error';
			parent::draw( $main_error -> display());
			
		}
		
	}
	
	function setEssentials(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * user can see forum content
		 * draw tree
		 */
		
		$curren_position = $this -> forum_result['forum_parent'];

		if ( $curren_position != 0){
				
			while ( $curren_position != 0){
				
				/**
				 * add tree element
				 */
				
				$path_elements[$forums -> forums_list[$curren_position]['forum_name']] = parent::systemLink( parent::getId(), array( 'forum' => $curren_position));
				
				/**
				 * jump to next element
				 */
				
				$curren_position = $forums -> forums_list[$curren_position]['forum_parent'];
				
			}
			
			$path_elements = array_reverse( $path_elements);
			
			foreach ( $path_elements as $path_element_name => $path_element_link)
				$path -> addBreadcrumb( $path_element_name, $path_element_link);
			
		}
		
		/**
		 * add this breadcrumb
		 */
		
		$path -> addBreadcrumb( $this -> forum_result['forum_name'], parent::systemLink( parent::getId(), array( 'forum' => $this -> forum_to_show)));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $this -> forum_result['forum_name']);
		
		/**
		 * draw forum info, if user desires
		 */
		
		if ( $settings['forum_draw_info'] && strlen( $this -> forum_result['forum_info']) > 0){
			
			/**
			 * draw info
			 */
			
			parent::draw( $style -> drawBlankBlock( $strings -> parseBB( nl2br( $this -> forum_result['forum_info']), true, false)));
	
		}

		/**
		 * and guidelines
		 */
		
		if ( strlen( $this -> forum_result['forum_guidelines']) > 0 ){
			
			parent::draw( $style -> drawBlock( $language -> getString( 'forums_guidelines'), $strings -> parseBB( nl2br( $this -> forum_result['forum_guidelines']), true, false)));
			
		}else if ( strlen( $this -> forum_result['forum_guidelines_url']) > 0){
			
			$language -> setKey( 'board_guidelines_url', $this -> forum_result['forum_guidelines_url']);
			
			parent::draw( $style -> drawBlock( $language -> getString( 'forums_guidelines'), $language ->getString( 'forums_guidelines_url')));
						
		}
		
	}
	
	function drawJump( $topic_id, $pages){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
			
		if ( $pages > 1 && $settings['topics_paging_draw'] > 0){
			
			/**
			 * we will display two first pages, and tree last
			 */
			
			$pages_drawed = array();
			$pages_drawed_html = array();
			
			$last_pages = false;
			
			$first_half = floor( $settings['topics_paging_draw'] / 2);
			$secound_half = ceil( $settings['topics_paging_draw'] / 2);
			
			for ( $i = 1; $i <= $first_half; $i ++){
				
				if ( $i < $pages){
					
					$pages_drawed_html[$i] = '<a href="'.parent::systemLink( 'topic', array( 'topic' => $topic_id, 'p' => ( $i + 1))).'">'.( $i + 1).'</a>';
					
				}
				
			}
			
			$spacer_drawed = false;
			
			while ( $secound_half > 0){
				
				if ( $pages - $secound_half > 0){
					
					$key = $pages - $secound_half;
					
					settype( $key, 'integer');
					
					if ( !key_exists( $key, $pages_drawed_html)){
						
						if ( !$spacer_drawed){
							
							$spacer_drawed = true;
							
							$pages_drawed_html[] = '...';
						}
						
						$pages_drawed_html[ $key] = '<a href="'.parent::systemLink( 'topic', array( 'topic' => $topic_id, 'p' => ( $key + 1))).'">'.( $key + 1).'</a>';
						
					}
					
				}
				
				$secound_half --;
				
			}
			
			/**
			 * lets go
			 */
			
			$generated_html = "[ ";
			
			$generated_html .= join( ', ', $pages_drawed_html);
						
			$generated_html .= ' ]';
			
			$generated_html = str_replace( ", ...,", " ... ", $generated_html);
			
			/**
			 * return
			 */
			
			return $generated_html;
			
		}else{
		
			return "";
			
		}
		
	}
	
}

?>