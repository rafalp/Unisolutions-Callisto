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
|	Finder
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class action_search extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * check if we can access
		 */
		
		if ( $session -> user['user_search']){
			
			/**
			 * first breadcrumb
			 */

			$path -> addBreadcrumb( $language -> getString('search_forum'), parent::systemLink( 'search'));
			
			/**
			 * set page title
			 */
			
			$output -> setTitle($language -> getString( 'search_forum'));
						
			/**
			 * check if we can search
			 */
			
			if ( ($session -> user['user_id'] != -1 && ($session -> user['user_search_limit'] == 0 || ( $session -> user['user_search_limit'] > 0 && ($session -> user['user_last_search_time'] - ( $session -> user['user_search_limit'] * 60) <= time())))) || $session -> user['user_id'] == -1){
				
				/**
				 * define what to do
				 */
				
				if ( $_GET['do'] == 'new_posts' && $session -> user['user_id'] != -1){
							
					/**
					 * build up an list of reachable forums
					 */
					
					$forums_list = $forums -> getForumsList();
					
					$clear_forum_list = array();
					
					foreach ( $forums_list as $forum_id => $forum_name){
						
						if ( $session -> canSeeTopics($forum_id))
							$clear_forum_list[] = $forum_id;
					}
					
					/**
					 * unreaded topics
					 */
					
					$topics_reads = array();
					$topics_with_new_posts = array();
						
					$reads_query = $mysql -> query( "SELECT topic_read_topic, topic_read_time FROM `topics_reads` WHERE `topic_read_user` = '".$session -> user['user_id']."' ORDER BY `topic_read__time` DESC LIMIT ".$settings['forum_topics_per_page']);
										
					while ( $reads_result = mysql_fetch_array( $reads_query, MYSQL_ASSOC)){
						
						$topics_reads[] = $reads_result['topic_read_topic'];
					
						$topics_with_new_posts[] = "(t.topic_id = '".$reads_result['topic_read_topic']."' AND t.topic_last_time > '".$reads_result['topic_read_time']."')";
						
					}
						
					if ( count( $topics_reads) > 0){
										
						$reads_condition = "AND t.topic_id NOT IN (".join( ",", $topics_reads).") OR (".join( " OR ", $topics_with_new_posts).")";
						
					}else{
						
						$reads_condition = '';
						
					}
					
					/**
					 * topics number
					 */
					
					$topics_num = $mysql -> countRows( 'topics', "topic_forum_id IN (".join( ",", $clear_forum_list).") AND topic_last_time >= '".$session -> user['user_last_login']."'");
					
					/**
					 * start drawing form
					 */
						
					$search_results = new form();
					$search_results -> drawSpacer( $topics_num.' '.$language -> getString( 'search_show_new_posts'));
					$search_results -> openOpTable();
					
					if ( $settings['forum_topics_table_head']){
						
						if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist']){
							$rank_head_row = '<th style="width: 100px">'.$language -> getString( 'topics_rating').'</th>';
						}else{
							$rank_head_row = '';
						}
						
						$search_results -> addToContent( '<tr>
							<th>&nbsp;</th>
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_name').'</th>
							'.$rank_head_row.'
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_posts').'</th>
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_author').'</th>
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_views').'</th>
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_last_reply').'</th>
						</tr>');
						
					}
					
					if ( count( $clear_forum_list) > 0 ){
						
						//found anything?
						if ( $topics_num < 1){
							
							/**
							 * no results
							 */
							
							parent::draw( $style -> drawBlock( $language -> getString( 'search_forum'), $language -> getString( 'search_forum_no_results')));
							$this -> drawSearchForm();
														
						}else{
							
							if ( $settings['forum_topics_per_page'] < 1)
								$settings['forum_topics_per_page'] = 1;
								
							$pages_number = ceil( count( $topics_num) / $settings['forum_topics_per_page']);
								
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
										
							$paginator_html = $style -> drawPaginator( parent::systemLink( parent::getId(), array( 'do' => 'new_posts')), 'p', $pages_number, ( $page_to_draw + 1));
							
							/**
							 * array with topics cache
							 */
							
							$topics_cache = array();
							
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
							WHERE t.topic_forum_id IN (".join( ",", $clear_forum_list).") AND t.topic_last_time >= '".$session -> user['user_last_login']."'
							ORDER BY t.topic_type DESC, t.topic_last_time DESC
							LIMIT ".($page_to_draw * $settings['forum_topics_per_page']).", ".$settings['forum_topics_per_page']);
							
							while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
															
								//add to list
								$topics_cache[$topics_result['topic_id']] = $topics_result;
								
							}

							//select topics reads
							$reads_sql = $mysql -> query( "SELECT * FROM `topics_reads` WHERE `topic_read_topic` IN (".join( ",", array_keys( $topics_cache)).") AND `topic_read_user` = '".$session -> user['user_id']."'");
								
							while ( $reads_result = mysql_fetch_array( $reads_sql, MYSQL_ASSOC))
								$topics_cache[$reads_result['topic_read_topic']]['topic_read_time'] = $reads_result['topic_read_time'];
							
							//reiterate
							foreach ( $topics_cache as $topic_id => $topics_result){
													
								/**
								 * topic icon
								 */
									
								$topic_goto = '';
								
								//new?
								if ( $topics_result['topic_last_time'] < $topics_result['topic_read_time']){
										
									if ( $topics_result['topic_closed']){
										
										$topic_image = $style -> drawImage( 'topic_closed', $language -> getString( 'topic_type_closed'));
									
									}else if ( $settings['forum_hot_topic'] > 0 && $topics_result['topic_posts_num'] >= $settings['forum_hot_topic']){
										
										$topic_image = $style -> drawImage( 'topic_popular', $language -> getString( 'topic_type_popular'));
										
									}else{
										
										$topic_image = $style -> drawImage( 'topic', $language -> getString( 'topic_type'));
										
									}
									
									$topic_goto = '';
									
								}else{
										
									if ( $topics_result['topic_closed']){
										
										$topic_image = $style -> drawImage( 'topic_closed_new', $language -> getString( 'topic_type_closed_new'));
									
									}else if ( $settings['forum_hot_topic'] > 0 && $topics_result['topic_posts_num'] >= $settings['forum_hot_topic']){
										
										$topic_image = $style -> drawImage( 'topic_popular_new', $language -> getString( 'topic_type_popular_new'));
										
									}else{
										
										$topic_image = $style -> drawImage( 'topic_new', $language -> getString( 'topic_type_new'));
										
									}
									
									/**
									 * goto last unread?
									 */
									
									$topic_goto = '<a href="'.parent::systemLink( 'topic', array( 'topic' => $topics_result['topic_id'])).'#post'.$topics_result['topic_last_post_id'].'">'.$style -> drawImage( 'goto', $language -> getString( 'topic_goto_last_unread')).'</a> ';
									
								}
								
								/**
								 * topic prefix
								 */
								
								$topic_prefix = $forums -> getPrefixHTML( $topics_result['topic_prefix'], $topics_result['topic_forum_id']);
								
								if ( strlen( $topic_prefix) == 0){
									
									if ( $topics_result['topic_type'] == 1){
										
										$topic_prefix = '<b>'.$settings['forum_stick_prefix'].':</b> ';
									
									}else if ($topics_result['topic_survey']){
										
										$topic_prefix = '<b>'.$settings['forum_survey_prefix'].':</b> ';
									
									}else{
										
										$topic_prefix = '';
										
									}
								
								}
									
								/**
								 * are we in inportant topics?
								 */
								
								if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist']){
									$spacer_colspan = 7;
								}else{
									$spacer_colspan = 6;
								}
								
								if ( $topics_result['topic_type'] == 2 && !$in_inportants){
									
									if ( $settings['forum_draw_spacer']){
									
										$search_results -> addToContent( '<tr>
											<td class="opt_row4" colspan="'.$spacer_colspan.'"><b>'.$language -> getString( 'topics_important_topics').'</b></td>
										</tr>');
									
									}
									
									$in_inportants = true;
									
								}
								
								/**
								 * are we leaving important topics?
								 */
								
								if ( $topics_result['topic_type'] < 2 && $in_inportants){
									
									if ( $settings['forum_draw_spacer']){
		
										$search_results -> addToContent( '<tr>
											<td class="opt_row4" colspan="'.$spacer_colspan.'"><b>'.$language -> getString( 'topics_rest_of_topics').'</b></td>
										</tr>');
											
									}
									
									$in_inportants = false;
									
								}
									
								/**
								 * topic info
								 */
								
								$topic_info = '';
								
								if ( strlen( $topics_result['topic_info']) > 0 && $settings['topic_info_length'] > -1)
									$topic_info = '<br />'.$topics_result['topic_info'];
								
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
								 * attachment
								 */
								
								if ( $topics_result['topic_attachments'] && $settings['forum_topics_mark_attachments']){
									
									$topic_attachment = ' '.$style -> drawImage( 'attachment', $language -> getString( 'topics_has_attachments'));
									
								}else{
									
									$topic_attachment = '';
									
								}
								
								/**
								 * topic paging
								 */
								
								if ( $settings['forum_posts_per_page'] < 1)
									$settings['forum_posts_per_page'] = 1;
								
								$pages_number = ceil( ($topics_result['topic_posts_num']+1) / $settings['forum_posts_per_page']);
								
								$topic_paging = $this -> drawJump( $topics_result['topic_id'], $pages_number);
				
								$topic_author_groups = array();
								$topic_author_groups = split( ",", $topics_result['user_other_groups']);
								$topic_author_groups[] = $topics_result['user_main_group'];
								
								if ( !$users -> cantCensore( $topic_author_groups)){
									
									$topics_result['topic_name'] = $strings -> censore( $topics_result['topic_name']);
									$topic_info = $strings -> censore( $topic_info);
									
								}
								
								/**
								 * topic rank
								 */
								
								if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist']){
												
									$rating_row = '<td class="opt_row2" style="text-align: center; width: 100px">'.$forums -> drawTopicRating( $topics_result['topic_score'], $topics_result['topic_votes']).'</td>';
									
								}else{
									
									$rating_row = '';
									
								}
								
								/**
								 * insert row
								 */
								
								$search_results -> addToContent( '<tr>
									<td class="opt_row2" style="text-align: center; width: 32px">'.$topic_image.'</td>
									<td class="opt_row1" NOWRAP="nowrap">'.$topic_goto.$topic_prefix.'<a href="'.parent::systemLink( 'topic', array( 'topic' => $topics_result['topic_id'])).'" title="'.$topic_result['topic_name'].'">'.$forums -> cutTopicName( $topics_result['topic_name']).'</a>'.$topic_attachment.$topic_paging.$topic_info.$topic_tags.'</td>
									'.$rating_row.'
									<td class="opt_row2" style="text-align: center; width: 80px">'.$topics_result['topic_posts_num'].'</td>
									<td class="opt_row1" style="text-align: center; width: 110px">'.$topic_author.'</td>
									<td class="opt_row2" style="text-align: center; width: 80px">'.$topics_result['topic_views_num'].'</td>
									<td class="opt_row3" style="width: 130px" NOWRAP="nowrap">'.$topic_last_anwswer.'</td>
								</tr>');
																
							}
						
						}
							
					}else{
						
						$search_results -> addToContent( '<tr>
								<td class="opt_row1" style="text-align: center" colspan="6">'.$language -> getString( 'forums_list_empty').'</td>
							</tr>');
						
					}
										
					/**
					 * close table
					 */
					
					$search_results -> closeTable();
									
					parent::draw( $style -> drawFormBlock( $language -> getString( 'user_menu_show_new_posts'), $search_results -> display()));
						
					/**
					 * and paginating
					 */
					
					parent::draw( $paginator_html);
					
				}else if ( $_GET['do'] == 'today_posts' && $session -> user['user_id'] == -1){
							
					/**
					 * build up an list of reachable forums
					 */
					
					$forums_list = $forums -> getForumsList();
					
					$clear_forum_list = array();
					
					foreach ( $forums_list as $forum_id => $forum_name){
						
						if ( $session -> canSeeTopics($forum_id))
							$clear_forum_list[] = $forum_id;
					}
											
					/**
					 * start drawing form
					 */
						
					$search_results = new form();
					$search_results -> drawSpacer( $settings['forum_topics_per_page'].' '.$language -> getString( 'search_show_today_posts'));
					$search_results -> openOpTable();
					
					if ( $settings['forum_topics_table_head']){
						
						if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist']){
							$rank_head_row = '<th style="width: 100px">'.$language -> getString( 'topics_rating').'</th>';
						}else{
							$rank_head_row = '';
						}
						
						$search_results -> addToContent( '<tr>
							<th>&nbsp;</th>
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_name').'</th>
							'.$rank_head_row.'
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_posts').'</th>
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_author').'</th>
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_views').'</th>
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_last_reply').'</th>
						</tr>');
						
					}
					
					if ( count( $clear_forum_list) > 0 ){
						
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
						WHERE t.topic_forum_id IN (".join( ",", $clear_forum_list).") AND t.topic_last_time >= '".(time() - ( 24 * 60 * 60))."'
						ORDER BY t.topic_type DESC, t.topic_last_time DESC
						LIMIT ".$settings['forum_topics_per_page']);
						
						while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
							
							//clear result
							$topics_result = $mysql -> clear( $topics_result);
																	
							/**
							 * topic icon
							 */
								
							$topic_goto = '';
									
							if ( $topics_result['topic_closed']){
											
								$topic_image = $style -> drawImage( 'topic_closed', $language -> getString( 'topic_type_closed'));
							
							}else if ( $settings['forum_hot_topic'] > 0 && $topics_result['topic_posts_num'] >= $settings['forum_hot_topic']){
								
								$topic_image = $style -> drawImage( 'topic_popular', $language -> getString( 'topic_type_popular'));
								
							}else{
								
								$topic_image = $style -> drawImage( 'topic', $language -> getString( 'topic_type'));
								
							}
							
							/**
							 * topic prefix
							 */
							
							$topic_prefix = $forums -> getPrefixHTML( $topics_result['topic_prefix'], $topics_result['topic_forum_id']);
							
							if ( strlen( $topic_prefix) == 0){
								
								if ( $topics_result['topic_type'] == 1){
									
									$topic_prefix = '<b>'.$settings['forum_stick_prefix'].':</b> ';
								
								}else if ($topics_result['topic_survey']){
									
									$topic_prefix = '<b>'.$settings['forum_survey_prefix'].':</b> ';
								
								}else{
									
									$topic_prefix = '';
									
								}
								
							}
							
							/**
							 * are we in inportant topics?
							 */
							
							if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist']){
								$spacer_colspan = 7;
							}else{
								$spacer_colspan = 6;
							}
							
							if ( $topics_result['topic_type'] == 2 && !$in_inportants){
								
								if ( $settings['forum_draw_spacer']){
								
									$search_results -> addToContent( '<tr>
										<td class="opt_row4" colspan="'.$spacer_colspan.'"><b>'.$language -> getString( 'topics_important_topics').'</b></td>
									</tr>');
								
								}
								
								$in_inportants = true;
								
							}
							
							/**
							 * are we leaving important topics?
							 */
							
							if ( $topics_result['topic_type'] < 2 && $in_inportants){
								
								if ( $settings['forum_draw_spacer']){
	
									$search_results -> addToContent( '<tr>
										<td class="opt_row4" colspan="'.$spacer_colspan.'"><b>'.$language -> getString( 'topics_rest_of_topics').'</b></td>
									</tr>');
										
								}
								
								$in_inportants = false;
								
							}
								
							/**
							 * topic info
							 */
							
							$topic_info = '';
							
							if ( strlen( $topics_result['topic_info']) > 0 && $settings['topic_info_length'] > -1)
								$topic_info = '<br />'.$topics_result['topic_info'];
							
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
								
								$topic_author = $topics_result['users_group_prefix'].$topics_result['topic_start_user_name'].$topics_result['users_group_sufix'];
							
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
							
							$topic_author_groups = array();
							$topic_author_groups = split( ",", $topics_result['user_other_groups']);
							$topic_author_groups[] = $topics_result['user_main_group'];
							
							if ( !$users -> cantCensore( $topic_author_groups)){
								
								$topics_result['topic_name'] = $strings -> censore( $topics_result['topic_name']);
								$topic_info = $strings -> censore( $topic_info);
								
							}
							
							/**
							 * attachment
							 */
							
							if ( $topics_result['topic_attachments'] && $settings['forum_topics_mark_attachments']){
								
								$topic_attachment = ' '.$style -> drawImage( 'attachment', $language -> getString( 'topics_has_attachments'));
								
							}else{
								
								$topic_attachment = '';
								
							}
							
							/**
							 * topic paging
							 */
							
							if ( $settings['forum_posts_per_page'] < 1)
								$settings['forum_posts_per_page'] = 1;
							
							$pages_number = ceil( ($topics_result['topic_posts_num']+1) / $settings['forum_posts_per_page']);
							
							$topic_paging = $this -> drawJump( $topics_result['topic_id'], $pages_number);
			
							/**
							 * topic rank
							 */
							
							if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist']){
											
								$rating_row = '<td class="opt_row2" style="text-align: center; width: 100px">'.$forums -> drawTopicRating( $topics_result['topic_score'], $topics_result['topic_votes']).'</td>';
								
							}else{
								
								$rating_row = '';
								
							}
							
							/**
							 * insert row
							 */
							
							$search_results -> addToContent( '<tr>
								<td class="opt_row2" style="text-align: center; width: 32px">'.$topic_image.'</td>
								<td class="opt_row1" NOWRAP="nowrap">'.$topic_goto.$topic_prefix.'<a href="'.parent::systemLink( 'topic', array( 'topic' => $topics_result['topic_id'])).'" title="'.$topic_result['topic_name'].'">'.$forums -> cutTopicName( $topics_result['topic_name']).'</a>'.$topic_attachment.$topic_paging.$topic_info.$topic_tags.'</td>
								'.$rating_row.'
								<td class="opt_row2" style="text-align: center; width: 80px">'.$topics_result['topic_posts_num'].'</td>
								<td class="opt_row1" style="text-align: center; width: 110px">'.$topic_author.'</td>
								<td class="opt_row2" style="text-align: center; width: 80px">'.$topics_result['topic_views_num'].'</td>
								<td class="opt_row3" style="width: 130px" NOWRAP="nowrap">'.$topic_last_anwswer.'</td>
							</tr>');
							
						}
						
					}else{
						
						$search_results -> addToContent( '<tr>
								<td class="opt_row1" style="text-align: center" colspan="6">'.$language -> getString( 'forums_list_empty').'</td>
							</tr>');
						
					}
										
					/**
					 * close table
					 */
					
					$search_results -> closeTable();
									
					parent::draw( $style -> drawFormBlock( $language -> getString( 'user_menu_show_today_posts'), $search_results -> display()));
					
				}else if ( $_GET['do'] == 'users_posts' && $_GET['user'] > 0){
					
					/**
					 * show user posts
					 */
					
					$user_to_show = $_GET['user'];
					settype( $user_to_show, 'integer');
					
					/**
					 * select user
					 */
					
					$user_query = $mysql -> query( "SELECT user_login FROM users WHERE `user_id` = '$user_to_show'");
					
					if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
						
						//clear user result
						$user_result = $mysql -> clear( $user_result);
						
						/**
						 * select proper forums
						 */
						
						$forums_list = $forums -> getForumsList();
			
						unset( $forums_list[0]);
	
						$checked_forums = array();
							
						foreach ( $forums_list as $forum_id => $forum_name){
							
							if( $session -> canSeeTopics($forum_id)){
								
								$checked_forums[] = $forum_id;
								
							}
							
						}
			
						/**
						 * open form
						 */
						
						$search_results = new form();
						$search_results -> openOpTable();
						
						/**
						 * check elements
						 */
						
						if ( count( $checked_forums) > 0){
							
							/**
							 * count user posts num
							 */
							
							$user_posts_query = $mysql -> query( "SELECT COUNT(*) FROM posts p LEFT JOIN topics t ON p.post_topic = t.topic_id WHERE p.post_author = '".$user_to_show."' AND t.topic_forum_id IN (".join( ",", $checked_forums).")");
							
							$user_posts = 0;
							
							if ( $user_posts_result = mysql_fetch_array( $user_posts_query, MYSQL_NUM))
								$user_posts = $user_posts_result[0];
								
							/**
							 * proceed with paginating
							 */
								
							if ( $settings['forum_posts_per_page'] < 1)
									$settings['forum_posts_per_page'] = 1;
							
							$pages_number = ceil( $user_posts / $settings['forum_posts_per_page']);
							
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
										
							$paginator_html = $style -> drawPaginator( parent::systemLink( parent::getId(), array( 'do' => 'users_posts', 'user' => $user_to_show)), 'p', $pages_number, ( $page_to_draw + 1));
							
							/**
							 * make an query
							 */
							
							$posts_query = $mysql -> query( "SELECT p.*, t.*, f.forum_allow_bbcode, u.*, g.users_group_name, g.users_group_prefix, g.users_group_suffix
							FROM posts p
							LEFT JOIN topics t ON p.post_topic = t.topic_id
							LEFT JOIN forums f ON t.topic_forum_id = f.forum_id
							LEFT JOIN users u ON p.post_author = u.user_id
							LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
							WHERE p.post_author = '".$user_to_show."' AND t.topic_forum_id IN (".join( ",", $checked_forums).")
							ORDER BY post_time DESC
							LIMIT ".( $page_to_draw * $settings['forum_posts_per_page']).", ".$settings['forum_posts_per_page']);
							
							$posts_found = false;
							
							while ( $posts_result = mysql_fetch_array( $posts_query, MYSQL_ASSOC)){
								
								$posts_found = true;
								
								//clear result
								$posts_result = $mysql -> clear( $posts_result);
								
								/**
								 * post groups
								 */
								
								$sender_groups = array();
								$sender_groups = split( ",", $posts_result['user_other_groups']);
								
								$sender_groups[] = $posts_result['user_main_group'];
								
								if ( $posts_result['post_author'] == -1){
						
									/**
									 * author is deleted
									 */
																		
									$post_author = '<a name="post'.$posts_result['post_id'].'" id="post'.$posts_result['post_id'].'">'.$posts_result['users_group_prefix'].$posts_result['post_author_name'].$posts_result['users_group_suffix'].'</a>';
									
								}else{
																		
									/**
									 * and user login
									 */
									
									$post_author = '<a name="post'.$posts_result['post_id'].'" id="post'.$posts_result['post_id'].'">'.'<a href="'.parent::systemLink( 'user', array( 'user' => $posts_result['post_author'])).'">'.$posts_result['users_group_prefix'].$posts_result['user_login'].$posts_result['users_group_suffix'].'</a></a>';				
									
								}
								
								/**
								 * and message
								 */
								
								$post_message = $strings -> parseBB( nl2br( $posts_result['post_text']), $posts_result['forum_allow_bbcode'], true);
								
								$user_groups = array();
								$user_groups = split( ",", $post_result['user_other_groups']);
								$user_groups[] = $post_result['user_main_group'];
								
								if ( !$users -> cantCensore( $user_groups))
									$post_message = $strings -> censore( $post_message);
								
								//cut it	
								if( $settings['message_small_cut'] > 0 && !defined( 'SIMPLE_MODE')){
							
									$post_message = '<div style="max-height: '.$settings['message_small_cut'].'px;overflow: auto">'.$post_message.'<div>';
									
								}
									
								/**
								 * add row
								 */
								
								$search_results -> addToContent( '<tr>
									<td class="opt_row1" style="width: 170px; vertical-align: top">'.$post_author.'<br />
									'.$time -> drawDate( $posts_result['post_time']).'<br /><br />
									<b>'.$language -> getString( 'search_results_topic').':</b> <a href="'.parent::systemLink( 'topic', array( 'topic' => $posts_result['topic_id'])).'" title="'.$topic_result['topic_name'].'">'.$forums -> cutTopicName( $posts_result['topic_name']).'</a><br />
									<b>'.$language -> getString( 'search_results_topic_replies').':</b> '.$posts_result['topic_posts_num'].'
									</td>
									<td class="opt_row2" style="vertical-align: top">'.$post_message.'</td>
								</tr>
								<tr>
									<td colspan="2" class="post_end"></td>
								</tr>');
								
							}
							
						}else{
														
							/**
							 * no forums
							 */
							
							$search_results -> addToContent( '<tr>
									<td class="opt_row1" style="text-align: center">'.$language -> getString( 'forums_list_empty').'</td>
								</tr>');
							
						}
							
						if ( !$posts_found){
							
							$search_results -> addToContent( '<tr>
									<td class="opt_row1" style="text-align: center" colspan="6">'.$language -> getString( 'search_forum_no_results').'</td>
								</tr>');
							
						}
										
						/**
						 * close table
						 */
						
						$search_results -> closeTable();
										
						parent::draw( $style -> drawFormBlock( $language -> getString( 'search_show_user_posts').': '.$user_result['user_login'], $search_results -> display()));
						
						/**
						 * and paginating
						 */
						
						parent::draw( $paginator_html);
						
					}else{
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'search_show_user_posts'), $language -> getString( 'error_user_notfound')));
						
					}
					
				}else if ( $_GET['do'] == 'tagged' && strlen( $_GET['tag']) > 0 && $settings['forum_allow_tags']){
							
					/**
					 * build up an list of reachable forums
					 */
					
					$forums_list = $forums -> getForumsList();
					
					$clear_forum_list = array();
					
					foreach ( $forums_list as $forum_id => $forum_name){
						
						if ( $session -> canSeeTopics($forum_id))
							$clear_forum_list[] = $forum_id;
					}

					/**
					 * get tag
					 */
					
					$tag_to_draw = $strings -> inputClear( $_GET['tag'], false);
					
					/**
					 * open table
					 */
					
					$search_results = new form();
					$search_results -> openOpTable();					
					
					if ( $settings['forum_topics_table_head']){
						
						if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist']){
							$rank_head_row = '<th style="width: 100px">'.$language -> getString( 'topics_rating').'</th>';
						}else{
							$rank_head_row = '';
						}
						
						$search_results -> addToContent( '<tr>
							<th>&nbsp;</th>
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_name').'</th>
							'.$rank_head_row.'
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_posts').'</th>
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_author').'</th>
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_views').'</th>
							<th NOWRAP="nowrap">'.$language -> getString( 'topics_last_reply').'</th>
						</tr>');
						
					}

					if ( count( $clear_forum_list) > 0){
						
						/**
						 * get results num
						 */
						
						$topics_num = $mysql -> countRows( 'topics', '`topic_tags` LIKE "%'.$tag_to_draw.'%" AND `topic_forum_id` IN ('.join( ",", $clear_forum_list).')');
					
						if ( $settings['forum_topics_per_page'] < 1)
							$settings['forum_topics_per_page'] = 1;
							
						$pages_number = ceil( count( $topics_num) / $settings['forum_topics_per_page']);
							
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
									
						$paginator_html = $style -> drawPaginator( parent::systemLink( parent::getId(), array( 'do' => 'tagged', 'tag' => urlencode( $tag_to_draw))), 'p', $pages_number, ( $page_to_draw + 1));
						
						/**
						 * select topics
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
						WHERE t.topic_forum_id IN (".join( ",", $clear_forum_list).") AND t.topic_tags LIKE \"%$tag_to_draw%\"
						ORDER BY t.topic_type DESC, t.topic_last_time DESC
						LIMIT ".($page_to_draw * $settings['forum_topics_per_page']).", ".$settings['forum_topics_per_page']);
						
						while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
							
							//clear result
							$topics_result = $mysql -> clear( $topics_result);
																	
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
									
									$topic_goto = '<a href="'.parent::systemLink( 'topic', array( 'topic' => $topics_result['topic_id'])).'#post'.$topics_result['topic_last_post_id'].'">'.$style -> drawImage( 'goto', $language -> getString( 'topic_goto_last_unread')).'</a> ';
									
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
							
							$topic_prefix = $forums -> getPrefixHTML( $topics_result['topic_prefix'], $topics_result['topic_forum_id']);
							
							if ( strlen( $topic_prefix) == 0){
								
								if ( $topics_result['topic_type'] == 1){
									
									$topic_prefix = '<b>'.$settings['forum_stick_prefix'].':</b> ';
								
								}else if ($topics_result['topic_survey']){
									
									$topic_prefix = '<b>'.$settings['forum_survey_prefix'].':</b> ';
								
								}else{
									
									$topic_prefix = '';
									
								}
								
							}
							
							/**
							 * are we in inportant topics?
							 */
							
							if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist']){
								$spacer_colspan = 7;
							}else{
								$spacer_colspan = 6;
							}
							
							if ( $topics_result['topic_type'] == 2 && !$in_inportants){
								
								if ( $settings['forum_draw_spacer']){
								
									$search_results -> addToContent( '<tr>
										<td class="opt_row4" colspan="'.$spacer_colspan.'"><b>'.$language -> getString( 'topics_important_topics').'</b></td>
									</tr>');
								
								}
								
								$in_inportants = true;
								
							}
							
							/**
							 * are we leaving important topics?
							 */
							
							if ( $topics_result['topic_type'] < 2 && $in_inportants){
								
								if ( $settings['forum_draw_spacer']){

									$search_results -> addToContent( '<tr>
										<td class="opt_row4" colspan="'.$spacer_colspan.'"><b>'.$language -> getString( 'topics_rest_of_topics').'</b></td>
									</tr>');
										
								}
								
								$in_inportants = false;
								
							}	
							/**
							 * topic info
							 */
							
							$topic_info = '';
							
							if ( strlen( $topics_result['topic_info']) > 0 && $settings['topic_info_length'] > -1)
								$topic_info = '<br />'.$topics_result['topic_info'];
							
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
							 * attachment
							 */
							
							if ( $topics_result['topic_attachments'] && $settings['forum_topics_mark_attachments']){
								
								$topic_attachment = ' '.$style -> drawImage( 'attachment', $language -> getString( 'topics_has_attachments'));
								
							}else{
								
								$topic_attachment = '';
								
							}
							
							/**
							 * topic paging
							 */
							
							if ( $settings['forum_posts_per_page'] < 1)
								$settings['forum_posts_per_page'] = 1;
							
							$pages_number = ceil( ($topics_result['topic_posts_num']+1) / $settings['forum_posts_per_page']);
							
							$topic_paging = $this -> drawJump( $topics_result['topic_id'], $pages_number);
			
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
							 * topic rank
							 */
							
							if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist']){
											
								$rating_row = '<td class="opt_row2" style="text-align: center; width: 100px">'.$forums -> drawTopicRating( $topics_result['topic_score'], $topics_result['topic_votes']).'</td>';
								
							}else{
								
								$rating_row = '';
								
							}
							
							/**
							 * insert row
							 */
							
							$search_results -> addToContent( '<tr>
								<td class="opt_row2" style="text-align: center; width: 32px">'.$topic_image.'</td>
								<td class="opt_row1" NOWRAP="nowrap">'.$topic_goto.$topic_prefix.'<a href="'.parent::systemLink( 'topic', array( 'topic' => $topics_result['topic_id'])).'" title="'.$topic_result['topic_name'].'">'.$forums -> cutTopicName( $topics_result['topic_name']).'</a>'.$topic_attachment.$topic_paging.$topic_info.$topic_tags.'</td>
								'.$rating_row.'
								<td class="opt_row2" style="text-align: center; width: 80px">'.$topics_result['topic_posts_num'].'</td>
								<td class="opt_row1" style="text-align: center; width: 110px">'.$topic_author.'</td>
								<td class="opt_row2" style="text-align: center; width: 80px">'.$topics_result['topic_views_num'].'</td>
								<td class="opt_row3" style="width: 130px" NOWRAP="nowrap">'.$topic_last_anwswer.'</td>
							</tr>');
							
						}
						
					}else{
														
						/**
						 * no forums
						 */
						
						$search_results -> addToContent( '<tr>
								<td class="opt_row1" style="text-align: center" colspan="6">'.$language -> getString( 'forums_list_empty').'</td>
							</tr>');
						
					}
					
					/**
					 * close table
					 */
					
					$search_results -> closeTable();
									
					parent::draw( $style -> drawFormBlock( $language -> getString( 'search_show_tagged_topics').': '.$tag_to_draw, $search_results -> display()));
					
					/**
					 * and paginating
					 */
					
					parent::draw( $paginator_html);
					
				}else if ( $_GET['do'] == 'show_results'){
					
					/**
					 * show results
					 */
					
					$results_to_show = $_GET['results'];
					settype( $results_to_show, 'integer');
					
					/**
					 * select results
					 */
					
					$results_query = $mysql -> query( "SELECT * FROM searchs_results WHERE `search_id` = '$results_to_show' AND `search_session` = '".$session -> session_id."'");
					
					if ( $results_result = mysql_fetch_array( $results_query, MYSQL_ASSOC)){
						
						$results_result = $mysql -> clear( $results_result);
						
						$search_type = $results_result['search_result_type'];
						
						$results_to_draw = split( ",", $results_result['search_result']);
						
						/**
						 * do paginating
						 */
						
						if ($search_type){
														
							if ( $settings['forum_posts_per_page'] < 1)
									$settings['forum_posts_per_page'] = 1;
									
							$pages_number = ceil( count( $results_to_draw) / $settings['forum_posts_per_page']);
							
						}else{
							
							if ( $settings['forum_topics_per_page'] < 1)
								$settings['forum_topics_per_page'] = 1;
								
							$pages_number = ceil( count( $results_to_draw) / $settings['forum_topics_per_page']);
						
						}
							
						
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
									
						$paginator_html = $style -> drawPaginator( parent::systemLink( parent::getId(), array( 'do' => 'show_results', 'results' => $results_to_show)), 'p', $pages_number, ( $page_to_draw + 1));
						
						/**
						 * set keys
						 */
						
						$language -> setKey( 'search_phrase', $results_result['search_phrase']);
						$language -> setKey( 'search_results', count( $results_to_draw));
						
						/**
						 * start drawing table
						 */
						
						$search_results = new form();
						$search_results -> drawSpacer( $language -> getString( 'search_results_phrase'));
						$search_results -> openOpTable();
						
						/**
						 * open tab
						 */
						
						if ( $search_type){
							
							$posts_query = $mysql -> query( "SELECT p.*, t.*, f.forum_allow_bbcode, u.*, g.users_group_name, g.users_group_prefix, g.users_group_suffix
							FROM posts p
							LEFT JOIN topics t ON p.post_topic = t.topic_id
							LEFT JOIN forums f ON t.topic_forum_id = f.forum_id
							LEFT JOIN users u ON p.post_author = u.user_id
							LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
							WHERE p.post_id IN (".join( ",", $results_to_draw).")
							ORDER BY post_time
							LIMIT ".( $page_to_draw * $settings['forum_posts_per_page']).", ".$settings['forum_posts_per_page']);
							
							while ( $posts_result = mysql_fetch_array( $posts_query, MYSQL_ASSOC)){
								
								//clear result
								$posts_result = $mysql -> clear( $posts_result);
								
								/**
								 * post groups
								 */
								
								$sender_groups = array();
								$sender_groups = split( ",", $posts_result['user_other_groups']);
								
								$sender_groups[] = $posts_result['user_main_group'];
								
								if ( $posts_result['post_author'] == -1){
						
									/**
									 * author is deleted
									 */
																		
									$post_author = '<a name="post'.$posts_result['post_id'].'" id="post'.$posts_result['post_id'].'">'.$posts_result['users_group_prefix'].$posts_result['post_author_name'].$posts_result['users_group_suffix'].'</a>';
									
								}else{
																		
									/**
									 * and user login
									 */
									
									$post_author = '<a name="post'.$posts_result['post_id'].'" id="post'.$posts_result['post_id'].'">'.'<a href="'.parent::systemLink( 'user', array( 'user' => $posts_result['post_author'])).'">'.$posts_result['users_group_prefix'].$posts_result['user_login'].$posts_result['users_group_suffix'].'</a></a>';				
									
								}
								/**
								 * and message
								 */
								
								$post_message = $strings -> parseBB( nl2br( $posts_result['post_text']), $posts_result['forum_allow_bbcode'], true);
								
								$user_groups = array();
								$user_groups = split( ",", $post_result['user_other_groups']);
								$user_groups[] = $post_result['user_main_group'];
								
								if ( !$users -> cantCensore( $user_groups))
									$post_message = $strings -> censore( $post_message);
								
								//cut it	
								if( $settings['message_small_cut'] > 0 && !defined( 'SIMPLE_MODE')){
							
									$post_message = '<div style="max-height: '.$settings['message_small_cut'].'px;overflow: auto">'.$post_message.'<div>';
									
								}
									
								/**
								 * add row
								 */
								
								$search_results -> addToContent( '<tr>
									<td class="opt_row1" style="width: 170px; vertical-align: top">'.$post_author.'<br />
									'.$time -> drawDate( $posts_result['post_time']).'<br /><br />
									<b>'.$language -> getString( 'search_results_topic').':</b> <a href="'.parent::systemLink( 'topic', array( 'topic' => $posts_result['topic_id'])).'" title="'.$topic_result['topic_name'].'">'.$forums -> cutTopicName( $posts_result['topic_name']).'</a><br />
									<b>'.$language -> getString( 'search_results_topic_replies').':</b> '.$posts_result['topic_posts_num'].'
									</td>
									<td class="opt_row2" style="vertical-align: top">'.$post_message.'</td>
								</tr>
								<tr>
									<td colspan="2" class="post_end"></td>
								</tr>');
								
							}
							
						}else{
							
							if ( $settings['forum_topics_table_head']){
								
								if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist']){
									$rank_head_row = '<th style="width: 100px">'.$language -> getString( 'topics_rating').'</th>';
								}else{
									$rank_head_row = '';
								}
								
								$search_results -> addToContent( '<tr>
									<th>&nbsp;</th>
									<th NOWRAP="nowrap">'.$language -> getString( 'topics_name').'</th>
									'.$rank_head_row.'
									<th NOWRAP="nowrap">'.$language -> getString( 'topics_posts').'</th>
									<th NOWRAP="nowrap">'.$language -> getString( 'topics_author').'</th>
									<th NOWRAP="nowrap">'.$language -> getString( 'topics_views').'</th>
									<th NOWRAP="nowrap">'.$language -> getString( 'topics_last_reply').'</th>
								</tr>');
								
							}

							/**
							 * if we are registered member, get reads
							 */
							
							if ( $session -> user['user_id'] != -1){
								
								$topics_reads = array();
									
								$reads_query = $mysql -> query( "SELECT * FROM `topics_reads` WHERE `topic_read_topic` IN (".join( ",", $results_to_draw).") AND `topic_read_user` = '".$session -> user['user_id']."'");
								
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
							
							$topics_query = $mysql -> query( "SELECT t.*, us.user_id, us.user_login, us.user_main_group, us.user_other_groups, gs.users_group_prefix, gs.users_group_suffix, lu.user_id AS last_user_id, lu.user_login AS last_user_login, lg.users_group_prefix AS last_users_group_prefix, lg.users_group_suffix AS last_users_group_suffix
							FROM topics t
							LEFT JOIN users us ON t.topic_start_user = us.user_id 
							LEFT JOIN users_groups gs ON gs.users_group_id = us.user_main_group
							LEFT JOIN users lu ON t.topic_last_user = lu.user_id
							LEFT JOIN users_groups lg ON lg.users_group_id = lu.user_main_group
							WHERE t.topic_id IN (".join( ",", $results_to_draw).")
							ORDER BY t.topic_type DESC, t.topic_last_time DESC
							LIMIT ".($page_to_draw * $settings['forum_topics_per_page']).", ".$settings['forum_topics_per_page']);
							
							while ( $topics_result = mysql_fetch_assoc( $topics_query)){
								
								//clear result
								$topics_result = $mysql -> clear( $topics_result);
																		
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
										
										$topic_goto = '<a href="'.parent::systemLink( 'topic', array( 'topic' => $topics_result['topic_id'])).'#post'.$topics_result['topic_last_post_id'].'">'.$style -> drawImage( 'goto', $language -> getString( 'topic_goto_last_unread')).'</a> ';
										
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
								
								$topic_prefix = $forums -> getPrefixHTML( $topics_result['topic_prefix'], $topics_result['topic_forum_id']);
								
								if ( strlen( $topic_prefix) == 0){
									
									if ( $topics_result['topic_type'] == 1){
										
										$topic_prefix = '<b>'.$settings['forum_stick_prefix'].':</b> ';
									
									}else if ($topics_result['topic_survey']){
										
										$topic_prefix = '<b>'.$settings['forum_survey_prefix'].':</b> ';
									
									}else{
										
										$topic_prefix = '';
										
									}
									
								}
								
								/**
								 * are we in inportant topics?
								 */
								
								if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist']){
									$spacer_colspan = 7;
								}else{
									$spacer_colspan = 6;
								}
								
								if ( $topics_result['topic_type'] == 2 && !$in_inportants){
									
									if ( $settings['forum_draw_spacer']){
									
										$search_results -> addToContent( '<tr>
											<td class="opt_row4" colspan="'.$spacer_colspan.'"><b>'.$language -> getString( 'topics_important_topics').'</b></td>
										</tr>');
									
									}
									
									$in_inportants = true;
									
								}
								
								/**
								 * are we leaving important topics?
								 */
								
								if ( $topics_result['topic_type'] < 2 && $in_inportants){
									
									if ( $settings['forum_draw_spacer']){

										$search_results -> addToContent( '<tr>
											<td class="opt_row4" colspan="'.$spacer_colspan.'"><b>'.$language -> getString( 'topics_rest_of_topics').'</b></td>
										</tr>');
											
									}
									
									$in_inportants = false;
									
								}	
								/**
								 * topic info
								 */
								
								$topic_info = '';
								
								if ( strlen( $topics_result['topic_info']) > 0 && $settings['topic_info_length'] > -1)
									$topic_info = '<br />'.$topics_result['topic_info'];
								
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
									
									$topic_author = $topics_result['users_group_prefix'].$topics_result['topic_start_user_name'].$topics_result['users_group_presufix'];
								
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
								 * pagination
								 */
								
								$topic_author_groups = array();
								$topic_author_groups = split( ",", $topics_result['user_other_groups']);
								$topic_author_groups[] = $topics_result['user_main_group'];
								
								if ( !$users -> cantCensore( $topic_author_groups)){
									
									$topics_result['topic_name'] = $strings -> censore( $topics_result['topic_name']);
									$topic_info = $strings -> censore( $topic_info);
									
								}
																
								/**
								 * topic rank
								 */
								
								if ( $settings['topics_rantings_turn'] && $settings['topics_rantings_onlist']){
												
									$rating_row = '<td class="opt_row2" style="text-align: center; width: 100px">'.$forums -> drawTopicRating( $topics_result['topic_score'], $topics_result['topic_votes']).'</td>';
									
								}else{
									
									$rating_row = '';
									
								}
								/**
								 * insert row
								 */
								
								$search_results -> addToContent( '<tr>
									<td class="opt_row2" style="text-align: center; width: 32px">'.$topic_image.'</td>
									<td class="opt_row1" NOWRAP="nowrap">'.$topic_goto.$topic_prefix.'<a href="'.parent::systemLink( 'topic', array( 'topic' => $topics_result['topic_id'])).'" title="'.$topic_result['topic_name'].'">'.$forums -> cutTopicName( $topics_result['topic_name']).'</a>'.$topic_attachment.$topic_paging.$topic_info.$topic_tags.'</td>
									'.$rating_row.'
									<td class="opt_row2" style="text-align: center; width: 80px">'.$topics_result['topic_posts_num'].'</td>
									<td class="opt_row1" style="text-align: center; width: 110px">'.$topic_author.'</td>
									<td class="opt_row2" style="text-align: center; width: 80px">'.$topics_result['topic_views_num'].'</td>
									<td class="opt_row3" style="width: 130px" NOWRAP="nowrap">'.$topic_last_anwswer.'</td>
								</tr>');
								
							}
							
						}
							
						/**
						 * close table
						 */
						
						$search_results -> closeTable();
						
						/**
						 * display table
						 */
						
						parent::draw( $style -> drawFormBlock( $language -> getString( 'search_results'), $search_results -> display()));
						
						/**
						 * and paginating
						 */
						
						parent::draw( $paginator_html);
						
						/**
						 * make results live longer
						 */
						
						$mysql -> update( array( 'search_time' => time()), 'searchs_results', "`search_id` = '$results_to_show' AND `search_session` = '".$session -> session_id."'");
						
					}else{
						
						/**
						 * notfound
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'search_forum'), $language -> getString( 'search_forum_notfound_results')));
						
					}
										
				}else if ( $_GET['do'] == 'search' && $session -> checkForm()){
					
					/**
					 * do search
					 */
					
					$word_to_search = $strings -> inputClear( $_POST['search_word'], false);
					$user_to_search = $strings -> inputClear( $_POST['search_user'], false);
					
					$search_forums = $_POST[ 'search_forums'];
					$search_results = $_POST['search_results'];
					$search_time = $_POST['search_time'];
					
					
					/**
					 * force types
					 */
					
					settype( $search_forums, 'array');
					settype( $search_results, 'bool');
					settype( $search_time, 'integer');
					
					if ( $search_time < 0)
						$search_time = 0;
					
					if ( $search_time > 4)
						$search_time = 4;
						
					/**
					 * check forums
					 */
										
					foreach ( $search_forums as $forum_id){
												
						if( !$session -> canSeeTopics($forum_id))
							unset( $search_forums[$forum_id]);
				
					}
				
					/**
					 * do checking
					 */
					
					if ( strlen( $word_to_search) == 0){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'search_forum'), $language -> getString( 'search_forum_no_phrase')));
						$this -> drawSearchForm();
						
					}else if ( strlen( $word_to_search) < $settings['search_phrase_min_length'] && $settings['search_phrase_min_length'] > 0){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'search_forum'), $language -> getString( 'search_forum_too_short_phrase')));
						$this -> drawSearchForm();
						
					}else if ( count( $search_forums) == 0){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'search_forum'), $language -> getString( 'search_forum_no_forum')));
						$this -> drawSearchForm();
						
					}else if ( $session -> user['user_id'] == -1 && $captcha -> check( $_POST['captcha'])){
						
						/**
						 * captcha
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'search_forum'), $captcha -> getError()));
						$this -> drawSearchForm();
														
					}else{
						
						/**
						 * proceed with searching
						 */
												
						$search_time_sql[0] = '';
						$search_time_sql[1] = " AND p.post_time > '".(time() - (24 * 60 * 60))."'";
						$search_time_sql[2] = " AND p.post_time > '".(time() - (7 * 24 * 60 * 60))."'";
						$search_time_sql[3] = " AND p.post_time > '".(time() - (30 * 24 * 60 * 60))."'";
						$search_time_sql[4] = " AND p.post_time > '".(time() - (364 * 60 * 60))."'";
						
						/**
						 * user
						 */
						
						if ( strlen( $user_to_search) > 0){
							
							$user_condition = " AND p.post_author_name LIKE '%$user_to_search%'";
							
						}else{
							
							$user_condition = '';
							
						}
						
						/**
						 * make query
						 */
						
						$search_query = $mysql -> query( "SELECT p.post_id, t.topic_id FROM posts p
						LEFT JOIN topics t ON p.post_topic = t.topic_id
						WHERE (p.post_text LIKE '%$word_to_search%' OR t.topic_name LIKE '%$word_to_search%' OR t.topic_info LIKE '%$word_to_search%')".$search_time_sql[$search_time].$user_condition);
						
						$search_returns = array();
						
						$new_search_results_sql['search_session'] = $session -> session_id;
						$new_search_results_sql['search_time'] = time();
						$new_search_results_sql['search_phrase'] = $word_to_search;
						
						if ( $search_results){
						
							/**
							 * show results as posts
							 */
							
							$new_search_results_sql['search_result_type'] = 1;
							
							while ( $search_result = mysql_fetch_array( $search_query, MYSQL_ASSOC)){
								
								if ( $search_result['post_id'] > 0 && !in_array( $search_result['post_id'], $search_returns))
									$search_returns[] = $search_result['post_id'];
								
							}
							
						}else{
							
							/**
							 * show results as topics
							 */
							
							$new_search_results_sql['search_result_type'] = 0;
						
							while ( $search_result = mysql_fetch_array( $search_query, MYSQL_ASSOC)){
								
								if ( $search_result['topic_id'] > 0 && !in_array( $search_result['topic_id'], $search_returns))
									$search_returns[] = $search_result['topic_id'];
								
							}
							
						}
						
						/**
						 * check what to do further
						 */
						
						if ( count( $search_returns) > 0){
							
							/**
							 * set results
							 */

							$new_search_results_sql['search_result'] = join( ",", $search_returns);
							
							$mysql -> insert( $new_search_results_sql, 'searchs_results');
							
							$search_results_id = mysql_insert_id();
							
							/**
							 * all ok, set redirect
							 */
													
							$output -> setRedirect( parent::getId(), array( 'do' => 'show_results', 'results' => $search_results_id));
							
							/**
							 * draw message
							 */
							
							$redirect_form = new form();
							$redirect_form -> openForm( parent::systemLink( parent::getId(), array( 'do' => 'show_results', 'results' => $search_results_id)));
							$redirect_form -> openOpTable();
							$redirect_form -> drawRow( $language -> getString( 'search_forum_redirect'));
							$redirect_form -> closeTable();
							$redirect_form -> drawButton( $language -> getString( 'search_forum_redirect_button'));
							$redirect_form -> closeForm();
							
							/**
							 * display form
							 */
							
							parent::draw( $style -> drawFormBlock( $language -> getString( 'search_forum'), $redirect_form -> display()));
							
						}else{
							
							/**
							 * no results
							 */
							
							parent::draw( $style -> drawBlock( $language -> getString( 'search_forum'), $language -> getString( 'search_forum_no_results')));
							$this -> drawSearchForm();
							
						}
					}
					
				}else{
				
					/**
					 * draw searh form
					 */
					
					$this -> drawSearchForm();
				
				}
				
			}else{
				
				$main_error = new main_error();
				$main_error -> type = 'information';
				$main_error -> message = $language -> getString( 'search_forum_no_time');
				parent::draw( $main_error -> display());
				
			}
			
		}else{
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'search_forum'), $language -> getString( 'search_forum_no_access')));
			
			$main_error = new main_error();
			$main_error -> type = 'error';
			parent::draw( $main_error -> display());
			
		}
		
	}
	
	/**
	 * draws search form
	 */
	
	function drawSearchForm(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * forums list
		 */
		
		$forums_list = $forums -> getForumsList();
		
		$forums_list[0] = $language -> getString( 'search_forum_do_search_everywere');
		
		foreach ( $forums_list as $forum_id => $forum_name){
			
			if( $session -> canSeeTopics($forum_id)){
				
				$forums_list[$forum_id] = '<option value="'.$forum_id.'" selected>'.$forum_name.'</option>';
				
			}else{
				
				/**
				 * remove forum from list
				 */
				
				unset( $forums_list[$forum_id]);
				
			}
			
		}
		
		/**
		 * search times
		 */
		
		$search_times[0] = $language -> getString( 'search_forum_results_time_0');
		$search_times[1] = $language -> getString( 'search_forum_results_time_1');
		$search_times[2] = $language -> getString( 'search_forum_results_time_2');
		$search_times[3] = $language -> getString( 'search_forum_results_time_3');
		$search_times[4] = $language -> getString( 'search_forum_results_time_4');
		
		foreach ( $search_times as  $search_time_id => $search_time_name)
			$search_times[$search_time_id] = '<option value="'.$search_time_id.'">'.$search_time_name.'</option>';
		
		/**
		 * captcha
		 */
			
		if ( $session -> user['user_id'] == -1)
			$captcha_key = $captcha -> generate();
		
		/**
		 * start drawing form
		 */
		
		$search_form = new form();
		$search_form -> openForm( parent::systemLink( parent::getId(), array( 'do' => 'search')));
		
		if ( $session -> user['user_id'] == -1)
			$register_form -> hiddenValue( 'captcha', $captcha_key);
		
		$search_form -> openOpTable( true);
		
		$search_form -> addToContent( '<tr><td class="opt_row1">
										<div class="sub_title">'.$language -> getString( 'search_forum_phrase').'</div>
										<div class="sub_border"><input type="text" name="search_word" style="width:100%"></div>');
		$search_form -> addToContent( '</td><td class="opt_row2">
										<div class="sub_title">'.$language -> getString( 'search_forum_author').'</div>
										<div class="sub_border">'.$language -> getString( 'search_forum_author_help').'<br /><br /><input type="text" name="search_user" style="width:100%"></div>');
		$search_form -> addToContent( '</td></tr>');
		
		$search_form -> closeTable();
		$search_form -> drawSpacer( $language -> getString( 'search_forum_options'));
		$search_form -> openOpTable( true);
		
		$search_form -> addToContent( '<tr><td class="opt_row1">
										<div class="sub_title">'.$language -> getString( 'search_forum_forums').'</div>
										<div class="sub_border"><select name="search_forums[]" size="8" style="width: 100%" multiple>'.join( "", $forums_list).'</select></div>');
		$search_form -> addToContent( '</td><td class="opt_row2">
										<div class="sub_title">'.$language -> getString( 'search_forum_results_type').'</div>
										<div class="sub_border">
										<input name="search_results" type="radio" value="0" checked> '.$language -> getString( 'search_forum_results_type_0').'<br />
										<input name="search_results" type="radio" value="1"> '.$language -> getString( 'search_forum_results_type_1').'
										</div><br />
										<div class="sub_title">'.$language -> getString( 'search_forum_results_time').'</div>
										<div class="sub_border"><select name="search_time" style="width: 100%">'.join( $search_times).'</select></div>');
		$search_form -> addToContent( '</td></tr>');
		
		$search_form -> closeTable();
		$search_form -> drawButton( $language -> getString( 'search_forum_do_search'));
		$search_form -> closeForm();
		
		/**
		 * draw form
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'search_forum'), $search_form -> display()));
		
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