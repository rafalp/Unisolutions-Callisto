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
|	Mod action handler
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class action_mod extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * define what to do
		 */
			
		$topic_to_mod = $_GET['topic'];
		settype( $topic_to_mod, 'integer');
		
		$post_to_mod = $_GET['post'];
		settype( $post_to_mod, 'integer');
		
		$posts_to_mod = $_POST['post_select'];
		settype( $posts_to_mod, 'array');
		
		$topics_to_mod = $_POST['topic_select'];
		settype( $topics_to_mod, 'array');
		
		$user_to_mod = $_GET['user'];
		settype( $user_to_mod, 'integer');
		
		/**
		 * messages
		 */
		
		$no_message = false;
		$message = '';
		$message_type = false;
		
		$return_topic = true;
		$return_forum = true;
		
		/**
		 * action
		 */
		
		$action_to_do = $_GET['do'];
		
		$proper_topic_does = array( 'open', 'close', 'move', 'merge', 'delete', 'normalize', 'pin', 'important');
		$proper_post_does = array( 'move', 'merge', 'split', 'delete');
		
		/**
		 * go forward
		 */
		
		if ( $topic_to_mod > 0 && in_array( $_GET['do'], $proper_topic_does)){
			
			/**
			 * mod topic
			 */
			
			$topic_query = $mysql -> query( "SELECT * FROM topics WHERE `topic_id` = '$topic_to_mod'");
			
			if ( $topic_result = mysql_fetch_array( $topic_query, MYSQL_ASSOC)){
				
				//clear result
				$topic_result = $mysql -> clear($topic_result);
				
				/**
				 * check if we can read topic
				 */
				
				if ( $session -> canSeeTopics( $topic_result['topic_forum_id'])){
					
					/**
					 * now depending on action we will do diffrent thing
					 */
					
					switch ( $action_to_do){
						
						case 'open':
							
							/**
							 * open topic
							 */
							
							if ( $session -> isMod( $topic_result['topic_forum_id']) || ($session -> user['user_close_own_topics'] && $topic_result['topic_start_user'] == $session -> user['user_id'] && $session -> user['user_id'] != -1 && $forums -> forums_list[$topic_result['topic_forum_id']]['forum_locked'] == false)){
					
								if ( $topic_result['topic_closed']){
									
									/**
									 * open topic
									 */
									
									$mysql -> update( array( 'topic_closed' => false), 'topics', "`topic_id` = '$topic_to_mod'");
									
									/**
									 * success
									 */
									
									$message_type = true;
									$message = $language -> getString( 'mod_topic_opened');		
									
									/**
									 * add log
									 */
									
									$log_keys['topic_name'] = $topic_result['topic_name'];
									
									$logs -> addModLog( $language -> getString( 'mod_topic_opened_log'), $log_keys, $topic_result['topic_forum_id'],$topic_result['topic_id'], 0, $session -> user['user_id']);
									
								}else{
									
									/**
									 * cant do an action
									 */
									
									$message = $language -> getString( 'mod_noaction');
									
								}
									
							}else{
								
								/**
								 * cant do an action
								 */
								
								$message = $language -> getString( 'mod_noaction');
														
							}
							
						break;
						
						case 'close':
							
							/**
							 * close topic
							 */
							
							if ( $session -> isMod( $topic_result['topic_forum_id']) || ($session -> user['user_close_own_topics'] && $topic_result['topic_start_user'] == $session -> user['user_id'] && $session -> user['user_id'] != -1 && $forums -> forums_list[$topic_result['topic_forum_id']]['forum_locked'] == false)){
					
								if ( !$topic_result['topic_closed']){
									
									/**
									 * close topic
									 */
									
									$mysql -> update( array( 'topic_closed' => true), 'topics', "`topic_id` = '$topic_to_mod'");
									
									/**
									 * success
									 */
									
									$message_type = true;
									$message = $language -> getString( 'mod_topic_closed');
									
									/**
									 * add log
									 */
									
									$log_keys['topic_name'] = $topic_result['topic_name'];
									
									$logs -> addModLog( $language -> getString( 'mod_topic_closed_log'), $log_keys, $topic_result['topic_forum_id'],$topic_result['topic_id'], 0, $session -> user['user_id']);
									
								}else{
									
									/**
									 * cant do an action
									 */
									
									$message = $language -> getString( 'mod_noaction');
									
								}
									
							}else{
								
								/**
								 * cant do an action
								 */
								
								$message = $language -> getString( 'mod_noaction');
													
							}
							
						break;
						
						case 'move':
							
							/**
							 * move topic
							 */
							
							if ( $session -> isMod( $topic_result['topic_forum_id'])){
								
								/**
								 * have form?
								 */
								
								$new_topic_forum = $_POST['new_topic_forum'];
								settype( $new_topic_forum, 'integer');
								
								if ( $session -> checkForm() && $session -> canSeeForum( $new_topic_forum, $forums -> forums_list) && $new_topic_forum != $topic_result['topic_forum_id']){
									
									/**
									 * lets move topic
									 */
									
									$mysql -> update( array( 'topic_forum_id' => $new_topic_forum), 'topics', "`topic_id` = '$topic_to_mod'");
									$mysql -> update( array( 'topic_read_forum' => $new_topic_forum), 'topics_reads', "`topic_read_topic` = '$topic_to_mod'");
								
									$forums -> forumResynchronise( $topic_result['topic_forum_id']);
									$forums -> forumResynchronise( $new_topic_forum);
									
									if ( $settings['news_turn'] && ($settings['news_forum'] == $new_topic_forum['topic_forum_id'] || $settings['news_forum'] == $topic_result['topic_forum_id']))
										$cache -> flushCache( 'forum_news');
									
									/**
									 * draw message
									 */
									
									$message_type = true;
									$message = $language ->getString( 'mod_topic_moved');
									
									/**
									 * add log
									 */
									
									$log_keys['topic_name'] = $topic_result['topic_name'];
									$log_keys['forum_name'] = $forums -> forums_list[$new_topic_forum]['forum_name'];
									
									$logs -> addModLog( $language -> getString( 'mod_topic_moved_log'), $log_keys, $topic_result['topic_forum_id'],$topic_result['topic_id'], 0, $session -> user['user_id']);
									
								}else{
									
									/**
									 * draw move form
									 */
									
									$move_topic_form = new form();
									$move_topic_form -> openForm( parent::systemLink( parent::getId(), array( 'topic' => $topic_to_mod, 'do' => 'move')));
									$move_topic_form -> openOpTable();
									
									$forums_list = $forums -> getForumsList();
									
									unset( $forums_list[0]);
									
									foreach ( $forums_list as $forum_id => $forum_name){
										
										if ( !$session -> canSeeForum( $forum_id))
											unset( $forums_list[$forum_id]);
										
									}
									
									$move_topic_form -> drawList( $language -> getString( 'mod_topic_move_to'), 'new_topic_forum', $forums_list);
									
									$move_topic_form -> closeTable();
									$move_topic_form -> drawButton( $language -> getString( 'mod_topic_move_button'));
									$move_topic_form -> closeForm();
									
									/**
									 * display block
									 */
									
									parent::draw( $style -> drawFormBlock( $language -> getString( 'mod_action'), $move_topic_form -> display()));
									
									/**
									 * hide form
									 */
									
									$no_message = true;
									
								}
								
							}else{
								
								/**
								 * cant do an action
								 */
								
								$message = $language -> getString( 'mod_noaction');
								
							}
							
						break;
						
						case 'delete':
							
							/**
							 * delete topic
							 */
							
							if ( $session -> isMod( $topic_result['topic_forum_id']) || ($session -> user['user_delete_own_topics'] && $topic_result['topic_start_user'] == $session -> user['user_id'] && $session -> user['user_id'] != -1 && $forums -> forums_list[$topic_result['topic_forum_id']]['forum_locked'] == false)){
								
								/**
								 * check if we have trashcan
								 */
								
								if ( $settings['trashcan_turn'] && key_exists( $settings['trashcan_forum'], $forums -> forums_list) && $topic_result['topic_forum_id'] != $settings['trashcan_forum']){
									
									/**
									 * put it in trashcan
									 */
									
									$mysql -> update( array( 'topic_name' => $strings -> inputClear( $language -> getString( 'mod_topics_delete_from').' '.$forums -> forums_list[$topic_result['topic_forum_id']]['forum_name'].': '.htmlspecialchars_decode( $topic_result['topic_name']), false), 'topic_forum_id' => $settings['trashcan_forum']), 'topics', "`topic_id` = '$topic_to_mod'");
									$mysql -> update( array( 'topic_read_forum' => $settings['trashcan_forum']), 'topics_reads', "`topic_read_topic` = '$topic_to_mod'");
									
									$forums -> forumResynchronise( $topic_result['topic_forum_id']);
									$forums -> forumResynchronise( $settings['trashcan_forum']);
									
									if ( $settings['news_turn'] && ($settings['news_forum'] == $settings['trashcan_forum'] || $settings['news_forum'] == $topic_result['topic_forum_id']))
										$cache -> flushCache( 'forum_news');
																		
									/**
									 * draw message
									 */
									
									$message_type = true;
									$message = $language ->getString( 'mod_topic_trashed');
									
									/**
									 * add log
									 */
									
									$log_keys['topic_name'] = $topic_result['topic_name'];
																		
									$logs -> addModLog( $language -> getString( 'mod_topic_trashed_log'), $log_keys, $topic_result['topic_forum_id'],$topic_result['topic_id'], 0, $session -> user['user_id']);
																											
								}else{
									
									/**
									 * update users counters
									 */
									
									$users_ids = $mysql -> query( "SELECT DISTINCT post_author FROM posts WHERE `post_topic` = '".$topic_to_mod."' AND post_author > '-1'");
									
									$authors = array();
									
									while ( $users_result = mysql_fetch_array( $users_ids, MYSQL_ASSOC)){
										
										$authors[] = $users_ids;
										
									}
									
									/**
									 * delete all
									 */
									
									$mysql -> delete( 'posts', "`post_topic` = '$topic_to_mod'");
									$mysql -> delete( 'topics', "`topic_id` = '$topic_to_mod'");
									$mysql -> delete( 'topics_reads', "`topic_read_topic` = '$topic_to_mod'");
									
									$forums -> forumResynchronise( $topic_result['topic_forum_id']);
									
									/**
									 * build up an list of proper forums
									 */
											
									$proper_forums = array();
															
									foreach ( $forums -> forums_list as $forum_id => $forum_ops){
										
										if ( $forum_ops['forum_increase_counter']){
											
											$proper_forums[] = $forum_id;
										
										}
										
									}
									
									if ( count( $proper_forums) > 0){
										
										foreach ( $authors as $author_id){
											
											/**
											 * do we resync post counter?
											 */
											
											$user_posts_q = $mysql -> query( "SELECT COUNT(*) FROM p.posts LEFT JOIN topics t ON p.post_topic = t.topic_id WHERE p.post_author = '$author_id' AND t.topic_forum_id IN (".join( ",", $proper_forums).")");
											if ($result = mysql_fetch_array($user_posts_q, MYSQL_NUM))	  
												$user_posts = $result[0];
	
											$mysql -> update( array( 'user_posts_num' => $user_posts), 'users', "`user_id` = '$author_id'");
											
										}
									
									}
																			
									if ( $settings['news_turn'] &&$settings['news_forum'] == $topic_result['topic_forum_id'])
										$cache -> flushCache( 'forum_news');
									
									/**
									 * draw message
									 */
									
									$message_type = true;
									$message = $language ->getString( 'mod_topic_deleted');
									
									$return_topic = false;
									
									/**
									 * add log
									 */
									
									$log_keys['topic_name'] = $topic_result['topic_name'];
																		
									$logs -> addModLog( $language -> getString( 'mod_topic_deleted_log'), $log_keys, $topic_result['topic_forum_id'],$topic_result['topic_id'], 0, $session -> user['user_id']);
									
								}
								
							}else{
								
								/**
								 * cant do an action
								 */
								
								$message = $language -> getString( 'mod_noaction');
								
							}
					
						break;
						
						case 'normalize':
							
							/**
							 * normalize topic
							 */
							
							if ( $session -> isMod( $topic_result['topic_forum_id'])){
								
								/**
								 * check if we can normalize topic
								 */
								
								if ( $topic_result['topic_type'] != 0){
									
									$mysql -> update( array( 'topic_type' => 0), 'topics', "`topic_id` = '$topic_to_mod'");
									
									/**
									 * success
									 */
									
									$message_type = true;
									$message = $language -> getString( 'mod_topic_normalised');
									
									/**
									 * add log
									 */
									
									$log_keys['topic_name'] = $topic_result['topic_name'];
																		
									$logs -> addModLog( $language -> getString( 'mod_topic_normalised_log'), $log_keys, $topic_result['topic_forum_id'],$topic_result['topic_id'], 0, $session -> user['user_id']);
									
								}else{
									
									/**
									 * cant do an action
									 */
									
									$message = $language -> getString( 'mod_noaction');
									
								}
								
							}else{
								
								/**
								 * cant do an action
								 */
								
								$message = $language -> getString( 'mod_noaction');
								
							}
					
						break;
						
						case 'pin':
							
							/**
							 * normalize topic
							 */
							
							if ( $session -> isMod( $topic_result['topic_forum_id'])){
								
								/**
								 * check if we can normalize topic
								 */
								
								if ( $topic_result['topic_type'] != 1){
									
									$mysql -> update( array( 'topic_type' => 1), 'topics', "`topic_id` = '$topic_to_mod'");
									
									/**
									 * success
									 */
									
									$message_type = true;
									$message = $language -> getString( 'mod_topic_pinned');
									
									/**
									 * add log
									 */
									
									$log_keys['topic_name'] = $topic_result['topic_name'];
																		
									$logs -> addModLog( $language -> getString( 'mod_topic_pinned_log'), $log_keys, $topic_result['topic_forum_id'],$topic_result['topic_id'], 0, $session -> user['user_id']);
									
								}else{
									
									/**
									 * cant do an action
									 */
									
									$message = $language -> getString( 'mod_noaction');
									
								}
								
							}else{
								
								/**
								 * cant do an action
								 */
								
								$message = $language -> getString( 'mod_noaction');
								
							}
					
						break;
						
						case 'important':
							
							/**
							 * normalize topic
							 */
							
							if ( $session -> isMod( $topic_result['topic_forum_id'])){
								
								/**
								 * check if we can normalize topic
								 */
								
								if ( $topic_result['topic_type'] != 2){
									
									$mysql -> update( array( 'topic_type' => 2), 'topics', "`topic_id` = '$topic_to_mod'");
									
									/**
									 * success
									 */
									
									$message_type = true;
									$message = $language -> getString( 'mod_topic_important');
									
									/**
									 * add log
									 */
									
									$log_keys['topic_name'] = $topic_result['topic_name'];
																		
									$logs -> addModLog( $language -> getString( 'mod_topic_important_log'), $log_keys, $topic_result['topic_forum_id'],$topic_result['topic_id'], 0, $session -> user['user_id']);
									
								}else{
									
									/**
									 * cant do an action
									 */
									
									$message = $language -> getString( 'mod_noaction');
									
								}
								
							}else{
								
								/**
								 * cant do an action
								 */
								
								$message = $language -> getString( 'mod_noaction');
								
							}
					
						break;
					}
					
					if ( !$no_message){
						
						/**
						 * set return links
						 */
						
						$message .= '<br /><br />';
						
						if ( $return_topic)
							$message .= '<a href="'.parent::systemLink( 'topic', array( 'topic' => $topic_to_mod)).'">'.$language -> getString( 'mod_return_topic').'</a><br />';
						
						$message .= '<a href="'.parent::systemLink( 'forum', array( 'forum' => $topic_result['topic_forum_id'])).'">'.$language -> getString( 'mod_return_forum').'</a>';
													
						/**
						 * draw message
						 */
						
						if ( $message_type){
							
							parent::draw( $style -> drawBlock( $language -> getString( 'mod_action'), $message));
															
						}else{
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'mod_action'), $message));
															
						}
					
					}
						
				}else{
					
					/**
					 * topic not found
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
			
		}else if( $post_to_mod > 0){
		
			/**
			 * this time we will only delete it
			 */
			
			$post_query = $mysql -> query( "SELECT p.*, t.*, f.forum_id, f.forum_name FROM posts p
			LEFT JOIN topics t ON p.post_topic = t.topic_id
			LEFT JOIN forums f ON t.topic_forum_id = f.forum_id
			WHERE p.post_id = '$post_to_mod'");
			
			if ( $post_result = mysql_fetch_array( $post_query, MYSQL_ASSOC)){
				
				//clear result
				$post_result = $mysql -> clear( $post_result);
				
				/**
				 * check if we can mod this post
				 */
				
				if ( $session -> canSeeTopics( $post_result['forum_id'])){
					
					/**
					 * return part for message
					 */
					
					$return_part = '<a href="'.parent::systemLink( 'topic', array( 'topic' => $post_result['topic_id'])).'">'.$language -> getString( 'mod_return_topic').'</a><br /><a href="'.parent::systemLink( 'forum', array( 'forum' => $post_result['forum_id'])).'">'.$language -> getString( 'mod_return_forum').'</a>';
					
					/**
					 * check, if we can delete our post
					 */
					
					if ( $post_result['post_id'] != $post_result['topic_first_post_id']){
					
						if ( $session -> isMod( $post_result['forum_id']) || ($post_result['post_author'] == $session -> user['user_id'] && $session -> user['user_delete_own_posts'])){
							
							/**
							 * what to do
							 */
							
							if ( $settings['trashcan_turn'] && key_exists( $settings['trashcan_forum'], $forums -> forums_list) && $post_result['forum_id'] == $settings['trashcan_forum']){
								
								/**
								 * move to trashcan
								 */
										
								$new_topic_sql['topic_forum_id'] = $settings['trashcan_forum'];
								$new_topic_sql['topic_type'] = 0;
								$new_topic_sql['topic_name'] = $strings -> inputClear( $language -> getString( 'mod_posts_delete_from').': '.htmlspecialchars_decode( $post_result['topic_name']), false);
								$new_topic_sql['topic_info'] = $post_result['topic_info'];
								$new_topic_sql['topic_start_time'] = $post_result['post_time'];
								$new_topic_sql['topic_start_user'] = $post_result['post_author'];
								$new_topic_sql['topic_start_user_name'] = uniSlashes( $post_result['post_author_name']);
								$new_topic_sql['topic_first_post_id'] = $post_result['post_id'];
								
								$mysql -> insert( $new_topic_sql, 'topics');
								
								$new_topic_id = mysql_insert_id();
								
								/**
								 * move actual posts to topic
								 */
							
								$mysql -> update( array( 'post_topic' => $new_topic_id), 'posts', "`post_id` = '$post_to_mod'");
								
								$forums -> topicResynchronise( $post_result['post_topic']);
								$forums -> topicResynchronise( $new_topic_id);
								
								$forums -> forumResynchronise( $post_result['topic_forum_id']);
								$forums -> forumResynchronise( $settings['trashcan_turn']);
								
								/**
								 * add log
								 */
										
								$logs -> addModLog( $language -> getString( 'mod_posts_trashed_log'), array( 'topic_name' => $post_result['topic_name']));
								
								/**
								 * draw message
								 */
								
								parent::draw( $style -> drawBlock( $language -> getString( 'mod_action'), $language -> getString( 'mod_posts_trashed').$return_part));
										
							}else{
							
								/**
								 * delete post
								 */
								
								$mysql -> delete( 'posts', "`post_id` = '$post_to_mod'");
								$mysql -> delete( 'reputation_votes', "`reputation_vote_post` = '$post_to_mod'");
								
								/**
								 * resynch topic
								 */
								
								$forums -> topicResynchronise( $post_result['post_topic']);
								$forums -> forumResynchronise( $post_result['topic_forum_id']);
								
								foreach ( $forums -> forums_list as $forum_id => $forum_ops){
										
									if ( $forum_ops['forum_increase_counter']){
										
										$proper_forums[] = $forum_id;
									
									}
									
								}
								
								if ( count( $proper_forums) > 0 && $post_result['post_author'] != -1){
										
									/**
									 * do we resync post counter?
									 */
									
									$user_posts_q = $mysql -> query( "SELECT COUNT(*) FROM posts p LEFT JOIN topics t ON p.post_topic = t.topic_id WHERE p.post_author = '".$post_result['post_author']."' AND t.topic_forum_id IN (".join( ",", $proper_forums).")");
									if ($result = mysql_fetch_array($user_posts_q, MYSQL_NUM))	  
										$user_posts = $result[0];
	
									$mysql -> update( array( 'user_posts_num' => $user_posts), 'users', "`user_id` = '".$post_result['post_author']."'");
																	
								}	
									
								/**
								 * add log
								 */
										
								$logs -> addModLog( $language -> getString( 'mod_posts_delete_log'), array( 'topic_name' => $post_result['topic_name']));
								
								/**
								 * draw message
								 */
								
								parent::draw( $style -> drawBlock( $language -> getString( 'mod_action'), $language -> getString( 'mod_posts_delete').'<br/>'.$return_part));
								
							}
														
						}else{
							
							/**
							 * cant delete post
							 */
							
							$main_error = new main_error();
							$main_error -> type = 'error';
							parent::draw( $main_error -> display());
										
						}
					
					}else{
						
						/**
						 * cant delete first post
						 */
						
						$main_error = new main_error();
						$main_error -> type = 'error';
						$main_error -> message = $language -> getString( 'mod_posts_cant_be_first').$return_part;
						parent::draw( $main_error -> display());
					
					}
					
				}else{
					
					/**
					 * post cant be saw
					 */
					
					$main_error = new main_error();
					$main_error -> type = 'error';
					parent::draw( $main_error -> display());
					
				}
				
			}else{
				
				/**
				 * post not found
				 */
				
				$main_error = new main_error();
				$main_error -> type = 'error';
				parent::draw( $main_error -> display());
													
			}
			
		}else if( count( $posts_to_mod) > 0 && in_array( $_GET['do'], $proper_post_does)){
			
			/**
			 * mod posts
			 * cycle trought list
			 */
			
			switch ( $action_to_do){
				
				case 'merge':
				
					/**
					 * we will have to merge posts
					 */
						
					$posts_list = array();
					
					foreach ( $posts_to_mod as $post_id => $post_act){
						
						settype( $post_id, 'integer');
						$posts_list[] = $post_id;
						
					}
					
					if ( count( $posts_list) > 0){
						
						/**
						 * check first post
						 */
						
						$first_posts_query = $mysql -> query( "SELECT p.post_id, p.post_topic, p.post_author, p.post_text, t.topic_id, t.topic_forum_id, t.topic_name FROM posts p LEFT JOIN topics t ON p.post_topic = t.topic_id WHERE p.post_id IN (".join( ",", $posts_list).") ORDER BY p.post_time ASC LIMIT 1");
						
						if ( $first_post_result = mysql_fetch_array( $first_posts_query, MYSQL_ASSOC)){
							
							//clear result
							$first_post_result = $mysql -> clear( $first_post_result);

							$post_text = $first_post_result['post_text'];
							
							unset( $posts_list[array_search( $first_post_result['post_id'], $posts_list)]);
													
							/**
							 * check forum perms
							 */
							
							if ( $session -> isMod( $first_post_result['topic_forum_id'])){
								
								/**
								 * select other posts
								 */
								
								$merging_error = false;
																
								$posts_query = $mysql -> query( "SELECT p.post_id, p.post_topic, p.post_author, p.post_text, t.topic_id, t.topic_forum_id FROM posts p LEFT JOIN topics t ON p.post_topic = t.topic_id WHERE p.post_id IN (".join( ",", $posts_list).") AND p.post_topic = '".$first_post_result['post_topic']."'");
								
								$merged_posts = 0;
								
								while ( $posts_result = mysql_fetch_array( $posts_query, MYSQL_ASSOC)){
									
									if ( !$merging_error){
											
										//clear result
										$posts_result = $mysql -> clear( $posts_result);
										
										/**
										 * check errors
										 */
																				
										if ( $first_post_result['post_author'] != $posts_result['post_author']){
											$merging_error = true;
										}

										$merged_posts ++;
																			
										/**
										 * add to content
										 */
										
										$post_text .= "\n\n".$posts_result['post_text'];
									
									}	
										
								}
									
								if ( !$merging_error){
									
									/**
									 * all okey, delete unmerged posts
									 */
									
									$mysql -> delete( 'posts', "`post_id` IN (".join( ",", $posts_list).")");
									
									/**
									 * update first post
									 */
									
									$first_post_update_sql['post_text'] = $strings -> inputClear( htmlspecialchars_decode( $post_text), false);
									$first_post_update_sql['post_edits'] = $first_post_result['post_edits'] + 1;
									$first_post_update_sql['post_last_edit'] = time();
									$first_post_update_sql['post_last_editor'] = $session -> user['user_id'];
									$first_post_update_sql['post_last_editor_name'] = $strings -> inputClear( $session -> user['user_login'], false);
									
									$mysql -> update( $first_post_update_sql, 'posts', "`post_id` = '".$first_post_result['post_id']."'");
									
									if ( $first_post_result['post_author'] != -1)
										$mysql -> query( "UPDATE users SET user_posts_num = user_posts_num - $merged_posts WHERE `user_id` = '".$first_post_result['post_author']."'");
									
									/**
									 * update topic
									 */
									
									$forums -> topicResynchronise( $first_post_result['post_topic']);
									$forums -> forumResynchronise( $first_post_result['topic_forum_id']);
									
									/**
									 * message
									 */
									
									$message = $language -> getString( 'mod_posts_merge');
									$message_type = true;
									
									$topic_to_mod = $first_post_result['topic_id'];
									$forum_to_mod = $first_post_result['topic_forum_id'];
									
									/**
									 * add log
									 */
									
									$log_keys['topic_name'] = $first_post_result['topic_name'];
																		
									$logs -> addModLog( $language -> getString( 'mod_posts_merge_log'), $log_keys, $topic_result['topic_forum_id'], $first_post_result['topic_id'], 0, $session -> user['user_id']);
									
								}else if( $merging_error){
									
									/**
									 * post has wrong author
									 */
									
									$message = $language -> getString( 'mod_posts_misauthors');
										
									$return_topic = false;
									$return_forum = false;
									
								}
															
							}else{
								
								/**
								 * topic not found
								 */
										
								$message = $language -> getString( 'mod_noaction');
							
								$return_topic = false;
								$return_forum = false;
								
							}
							
						}
												
					}else{
						
						/**
						 * cant do an action
						 */
						
						$message = $language -> getString( 'mod_noaction');
						
						$return_topic = false;
						$return_forum = false;
						
					}
					
				break;
				
				case 'move':
					
					/**
					 * move posts
					 */
						
					$posts_list = array();
					
					foreach ( $posts_to_mod as $post_id => $post_act){
						
						settype( $post_id, 'integer');
						$posts_list[] = $post_id;
						
						
					}
					
					$this -> posts_list = $posts_list;
						
					if ( count( $posts_list) > 0){
						
						/**
						 * check first post
						 */
						
						$first_posts_query = $mysql -> query( "SELECT p.post_id, p.post_topic, p.post_author, t.topic_id, t.topic_name, t.topic_forum_id, t.topic_first_post_id FROM posts p LEFT JOIN topics t ON p.post_topic = t.topic_id WHERE p.post_id IN (".join( ",", $posts_list).") ORDER BY p.post_time ASC LIMIT 1");
						
						if ( $first_post_result = mysql_fetch_array( $first_posts_query, MYSQL_ASSOC)){
							
							//clear result
							$first_post_result = $mysql -> clear( $first_post_result);
							$this -> first_post_result = $first_post_result;
													
							/**
							 * check forum perms
							 */
							
							if ( $session -> isMod( $first_post_result['topic_forum_id'])){
								
								/**
								 * check, if one of selected posts are first post
								 */
														
								if ( $first_post_result['post_id'] != $first_post_result['topic_first_post_id']){
								
									/**
									 * we can move posts
									 */
									
									if ( $session -> checkForm() && $_GET['finalize'] == true){
										
										/**
										 * move posts
										 */
										
										$topic_url = $_POST['posts_move_topic_link'];
										
										if ( strstr( $topic_url, "topic=") != false)
											$topic_id = substr( $topic_url, strpos( $topic_url, "topic=") + 6);
										
										if ( strstr( $topic_id, "&") != false)
											$topic_id = substr( $topic_id, 0, strpos( $topic_id, "&"));
											
										settype( $topic_id, 'integer');
										
										/**
										 * got topic ID, check, if it is actual topic
										 */
										
										if ( $topic_id != $first_post_result['topic_id']){
										
											/**
											 * check topic
											 */
											
											$new_topic_query = $mysql -> query( "SELECT topic_id, topic_name, topic_forum_id FROM topics WHERE `topic_id` = '$topic_id'");
											
											if ( $new_topic_result = mysql_fetch_array( $new_topic_query, MYSQL_ASSOC)){
												
												/**
												 * check if we can see topic
												 */
												
												if ( $session -> canSeeTopics( $new_topic_result['topic_forum_id'])){
												
													/**
													 * all ok, move those posts
													 */
																				
													$mysql -> update( array( 'post_topic' => $topic_id), 'posts', "`post_id` IN (".join( ",", $posts_list).") AND `post_topic` = '".$first_post_result['topic_id']."'");
													
													/**
													 * resynchronise
													 */
													
													$forums -> topicResynchronise( $first_post_result['post_topic']);
													$forums -> topicResynchronise( $topic_id);
													
													$forums -> forumResynchronise( $first_post_result['topic_forum_id']);
													
													/**
													 * resynch another one
													 */
													
													if ( $new_topic_result['topic_forum_id'] != $first_post_result['topic_forum_id'])
														$forums -> forumResynchronise( $new_topic_result['topic_forum_id']);
													
													/**
													 * message
													 */
												
													$message_type = true;	
													$message = $language -> getString( 'mod_posts_moved');
												
													$topic_to_mod = $first_post_result['topic_id'];
													$forum_to_mod = $first_post_result['topic_forum_id'];
													
													/**
													 * add log
													 */
													
													$new_topic_result = $mysql -> clear( $new_topic_result);
													
													$log_keys['topic_name'] = $first_post_result['topic_name'];
													$log_keys['new_topic_name'] = $new_topic_result['topic_name'];
																						
													$logs -> addModLog( $language -> getString( 'mod_posts_moved_log'), $log_keys, $topic_result['topic_forum_id'], $first_post_result['topic_id'], 0, $session -> user['user_id']);
															
												}else{
													
													/**
													 * topic not found
													 */
													
													parent::draw( $style -> drawErrorBlock( $language -> getString( 'mod_action'), $language -> getString( 'mod_posts_cant_move_to_new')));
														
													$this -> drawPostsMoveForm();
													
													/**
													 * dont draw second form
													 */
													
													$no_message = true;
													
												}
												
											}else{
												
												/**
												 * topic not found
												 */
														
												parent::draw( $style -> drawErrorBlock( $language -> getString( 'mod_action'), $language -> getString( 'mod_posts_cant_move_to_new')));
												
												$this -> drawPostsMoveForm();
													
												$no_message = true;
												
											}
												
										}else{
										 	
											/**
											 * new topic is same as actual
											 */
											
											parent::draw( $style -> drawErrorBlock( $language -> getString( 'mod_action'), $language -> getString( 'mod_posts_cant_move_to_where_they_are')));
											
											$this -> drawPostsMoveForm();
											
											$no_message = true;
											
										}
										 
									}else{
										
										/**
										 * draw moving form
										 */
										
										$move_posts_form = new form();
										$move_posts_form -> openForm( parent::systemLink( parent::getId(), array( 'do' => 'move', 'finalize' => true)));
										
										foreach ( $posts_list as $post_id){
										
											$move_posts_form -> hiddenValue( 'post_select['.$post_id.']', true);
										
										}
										
										$move_posts_form -> openOpTable();
										
										$move_posts_form -> drawTextInput( $language -> getString( 'mod_posts_move_to'), 'posts_move_topic_link');
										
										$move_posts_form -> closeTable();
										$move_posts_form -> drawButton( $language -> getString( 'mod_posts_move_button'));
										$move_posts_form -> closeForm();
										
										/**
										 * draw form
										 */
										
										parent::draw( $style -> drawFormBlock( $language -> getString( 'mod_action'), $move_posts_form -> display()));
										
										/**
										 * dont draw second form
										 */
										
										$no_message = true;
										
									}
									
								}else{
									
									/**
									 * cant move first post
									 */
											
									$message = $language -> getString( 'mod_posts_cant_move_first');
								
									$topic_to_mod = $first_post_result['topic_id'];
									$forum_to_mod = $first_post_result['topic_forum_id'];
									
								}
								
							}else{
								
								/**
								 * topic not found
								 */
										
								$message = $language -> getString( 'mod_noaction');
							
								$return_topic = false;
								$return_forum = false;
								
							}
								
						}
						
					}else{
						
						/**
						 * cant do an action
						 */
						
						$message = $language -> getString( 'mod_noaction');
						
						$return_topic = false;
						$return_forum = false;
						
					}
					
				break;
				
				case 'split':
					
					/**
					 * split posts
					 */
						
					$posts_list = array();
					
					foreach ( $posts_to_mod as $post_id => $post_act){
						
						settype( $post_id, 'integer');
						$posts_list[] = $post_id;
						
					}
					
					$this -> posts_list = $posts_list;
					
					if ( count( $posts_list) > 0){
						
						/**
						 * check first post
						 */
						
						$first_posts_query = $mysql -> query( "SELECT p.post_id, p.post_topic, p.post_author, p.post_author_name, p.post_time, t.topic_id, t.topic_name, t.topic_forum_id, t.topic_first_post_id FROM posts p LEFT JOIN topics t ON p.post_topic = t.topic_id WHERE p.post_id IN (".join( ",", $posts_list).") ORDER BY p.post_time ASC LIMIT 1");
						
						if ( $first_post_result = mysql_fetch_array( $first_posts_query, MYSQL_ASSOC)){
							
							//clear result
							$first_post_result = $mysql -> clear( $first_post_result);
							
							$this -> first_post_result = $first_post_result;
																				
							/**
							 * check forum perms
							 */
							
							if ( $session -> isMod( $first_post_result['topic_forum_id'])){
								
								/**
								 * check, if one of selected posts are first post
								 */
														
								if ( $first_post_result['post_id'] != $first_post_result['topic_first_post_id']){
								
									/**
									 * we can split posts
									 */
									
									if ( $session -> checkForm() && $_GET['finalize'] == true){
										
										/**
										 * split posts
										 */
										
										$new_topic_name = $strings -> inputClear( $_POST['new_topic_name'], false);
										$new_topic_info = $strings -> inputClear( $_POST['new_topic_info'], false);
										
										$new_topic_forum = $_POST['new_topic_forum'];
										settype( $new_topic_forum, 'integer');
										
										if ( strlen( $new_topic_name) == 0){
											
											parent::draw( $style -> drawErrorBlock( $language -> getString( 'mod_action'), $language -> getString( 'new_topic_new_empty_name')));
											$this -> drawPostsSplitForm( true);
											
											$no_message = true;
										
										}else if ( !$session -> canStartTopics( $new_topic_forum)){
											
											parent::draw( $style -> drawErrorBlock( $language -> getString( 'mod_action'), $language -> getString( 'new_topic_cant_start_new')));
											$this -> drawPostsSplitForm( true);
											
											$no_message = true;
										
										}else{
											
											/**
											 * all ok!
											 * start new topic
											 */
											
											$new_topic_sql['topic_forum_id'] = $new_topic_forum;
											$new_topic_sql['topic_type'] = 0;
											$new_topic_sql['topic_name'] = $new_topic_name;
											$new_topic_sql['topic_info'] = $new_topic_info;
											$new_topic_sql['topic_start_time'] = $first_post_result['post_time'];
											$new_topic_sql['topic_start_user'] = $first_post_result['post_author'];
											$new_topic_sql['topic_start_user_name'] = uniSlashes( $first_post_result['post_author_name']);
											$new_topic_sql['topic_first_post_id'] = $first_post_result['post_id'];
											
											$mysql -> insert( $new_topic_sql, 'topics');
											
											$new_topic_id = mysql_insert_id();
											
											/**
											 * move actual posts to topic
											 */
										
											$mysql -> update( array( 'post_topic' => $new_topic_id), 'posts', "`post_id` IN (".join( ",", $posts_list).") AND `post_topic` = '".$first_post_result['topic_id']."'");
											
											/**
											 * resynch all
											 */
																						
											$forums -> topicResynchronise( $first_post_result['post_topic']);
											$forums -> topicResynchronise( $new_topic_id);
											
											$forums -> forumResynchronise( $topic_id['topic_forum_id']);
											
											/**
											 * resynch another one
											 */
											
											if ( $new_topic_forum != $first_post_result['topic_forum_id'])
												$forums -> forumResynchronise( $new_topic_forum);
											
											/**
											 * draw message
											 */
												
											$message_type = true;
											
											$message = $language -> getString( 'mod_posts_splited');
										
											$topic_to_mod = $new_topic_id;
											$forum_to_mod = $new_topic_forum;
											
											/**
											 * add log
											 */
											
											$log_keys['topic_name'] = $first_post_result['topic_name'];
											$log_keys['new_topic_name'] = $new_topic_name;
																				
											$logs -> addModLog( $language -> getString( 'mod_posts_splited_log'), $log_keys, $topic_result['topic_forum_id'], $first_post_result['topic_id'], 0, $session -> user['user_id']);
														
										}
										 
									}else{
										
										/**
										 * draw moving form
										 */
										
										$this -> drawPostsSplitForm();
										
										/**
										 * dont draw second form
										 */
										
										$no_message = true;
										
									}
									
								}else{
									
									/**
									 * cant move first post
									 */
											
									$message = $language -> getString( 'mod_posts_cant_move_first');
								
									$topic_to_mod = $first_post_result['topic_id'];
									$forum_to_mod = $first_post_result['topic_forum_id'];
									
								}
								
							}else{
								
								/**
								 * topic not found
								 */
										
								$message = $language -> getString( 'mod_noaction');
							
								$return_topic = false;
								$return_forum = false;
								
							}
								
						}
						
					}else{
						
						/**
						 * cant do an action
						 */
						
						$message = $language -> getString( 'mod_noaction');
						
						$return_topic = false;
						$return_forum = false;
						
					}
					
				break;
				
				case 'delete':
					
					/**
					 * delete posts
					 */
						
					$posts_list = array();
					
					foreach ( $posts_to_mod as $post_id => $post_act){
						
						settype( $post_id, 'integer');
						$posts_list[] = $post_id;
						
					}
					
					if ( count( $posts_list) > 0){
						
						/**
						 * check first post
						 */
						
						$first_posts_query = $mysql -> query( "SELECT p.post_id, p.post_topic, p.post_author, p.post_text, p.post_time, p.post_author, p.post_author_name, t.topic_name, t.topic_info, t.topic_id, t.topic_forum_id, t.topic_first_post_id FROM posts p LEFT JOIN topics t ON p.post_topic = t.topic_id WHERE p.post_id IN (".join( ",", $posts_list).") ORDER BY p.post_time ASC LIMIT 1");
						
						if ( $first_post_result = mysql_fetch_array( $first_posts_query, MYSQL_ASSOC)){
							
							//clear result
							$first_post_result = $mysql -> clear( $first_post_result);

							$post_text = $first_post_result['post_text'];
													
							/**
							 * check forum perms
							 */
							
							if ( $session -> isMod( $first_post_result['topic_forum_id'])){
								
								/**
								 * check, if one of selected posts are first post
								 */
														
								if ( $first_post_result['post_id'] != $first_post_result['topic_first_post_id']){
									
									if ( $settings['trashcan_turn'] && key_exists( $settings['trashcan_forum'], $forums -> forums_list) && $first_post_result['topic_forum_id'] != $settings['trashcan_forum']){
								
										/**
										 * move to trashcan
										 */
										
										$new_topic_sql['topic_forum_id'] = $settings['trashcan_forum'];
										$new_topic_sql['topic_type'] = 0;
										$new_topic_sql['topic_name'] = $strings -> inputClear( $language -> getString( 'mod_posts_delete_from').': '.htmlspecialchars_decode( $first_post_result['topic_name']), false);
										$new_topic_sql['topic_info'] = $first_post_result['topic_info'];
										$new_topic_sql['topic_start_time'] = $first_post_result['post_time'];
										$new_topic_sql['topic_start_user'] = $first_post_result['post_author'];
										$new_topic_sql['topic_start_user_name'] = uniSlashes( $first_post_result['post_author_name']);
										$new_topic_sql['topic_first_post_id'] = $first_post_result['post_id'];
										
										$mysql -> insert( $new_topic_sql, 'topics');
										
										$new_topic_id = mysql_insert_id();
										
										/**
										 * move actual posts to topic
										 */
									
										$mysql -> update( array( 'post_topic' => $new_topic_id), 'posts', "`post_id` IN (".join( ",", $posts_list).") AND `post_topic` = '".$first_post_result['topic_id']."'");
										
										/**
										 * resynch all
										 */
																					
										$forums -> topicResynchronise( $first_post_result['post_topic']);
										$forums -> topicResynchronise( $new_topic_id);
										
										$forums -> forumResynchronise( $first_post_result['topic_forum_id']);
										$forums -> forumResynchronise( $settings['trashcan_forum']);
																				
										/**
										 * message
										 */
									
										$message_type = true;	
										$message = $language -> getString( 'mod_posts_trashed');
									
										$topic_to_mod = $first_post_result['topic_id'];
										$forum_to_mod = $first_post_result['topic_forum_id'];
									
										/**
										 * add log
										 */
										
										$log_keys['topic_name'] = $first_post_result['topic_name'];
																			
										$logs -> addModLog( $language -> getString( 'mod_posts_trashed_log'), $log_keys, $topic_result['topic_forum_id'], $first_post_result['topic_id'], 0, $session -> user['user_id']);
										
									}else{
											
										/**
										 * select posts authors
										 */
										
										$users_ids = $mysql -> query( "SELECT DISTINCT post_author FROM posts WHERE `post_id` IN (".join( ",", $posts_list).") AND `post_topic` = '".$first_post_result['topic_id']."' AND post_author > '-1'");
										
										$authors = array();
										
										while ( $users_result = mysql_fetch_array( $users_ids, MYSQL_ASSOC)){
											
											$authors[] = $users_result['post_author'];
											
										}
									
										/**
										 * we can delete posts
										 */
										
										$mysql -> delete( 'posts', "`post_id` IN (".join( ",", $posts_list).") AND `post_topic` = '".$first_post_result['topic_id']."'");
										$mysql -> delete( 'reputation_votes', "`reputation_vote_post` IN (".join( ",", $posts_list).")");
										
										/**
										 * resynchronise
										 */
										
										$forums -> topicResynchronise( $first_post_result['post_topic']);
										$forums -> forumResynchronise( $first_post_result['topic_forum_id']);
										
										foreach ( $forums -> forums_list as $forum_id => $forum_ops){
										
											if ( $forum_ops['forum_increase_counter']){
												
												$proper_forums[] = $forum_id;
											
											}
											
										}
										
										if ( count( $proper_forums) > 0){
											
											foreach ( $authors as $author_id){
												
												/**
												 * do we resync post counter?
												 */
												
												$user_posts_q = $mysql -> query( "SELECT COUNT(*) FROM posts p LEFT JOIN topics t ON p.post_topic = t.topic_id WHERE p.post_author = '$author_id' AND t.topic_forum_id IN (".join( ",", $proper_forums).")");
												if ($result = mysql_fetch_array($user_posts_q, MYSQL_NUM))	  
													$user_posts = $result[0];
		
												$mysql -> update( array( 'user_posts_num' => $user_posts), 'users', "`user_id` = '$author_id'");
												
											}
										
										}
										
										/**
										 * message
										 */
									
										$message_type = true;	
										$message = $language -> getString( 'mod_posts_delete');
									
										$topic_to_mod = $first_post_result['topic_id'];
										$forum_to_mod = $first_post_result['topic_forum_id'];
										
										/**
										 * add log
										 */
										
										$log_keys['topic_name'] = $first_post_result['topic_name'];
																			
										$logs -> addModLog( $language -> getString( 'mod_posts_delete_log'), $log_keys, $topic_result['topic_forum_id'], $first_post_result['topic_id'], 0, $session -> user['user_id']);
										
									}
										
								}else{
									
									/**
									 * cant delete first post
									 */
											
									$message = $language -> getString( 'mod_posts_cant_be_first');
								
									$topic_to_mod = $first_post_result['topic_id'];
									$forum_to_mod = $first_post_result['topic_forum_id'];
									
								}
								
							}else{
								
								/**
								 * topic not found
								 */
										
								$message = $language -> getString( 'mod_noaction');
							
								$return_topic = false;
								$return_forum = false;
								
							}
								
						}
						
					}else{
						
						/**
						 * cant do an action
						 */
						
						$message = $language -> getString( 'mod_noaction');
						
						$return_topic = false;
						$return_forum = false;
						
					}
					
				break;
			}
			
			if ( !$no_message){
						
				/**
				 * set return links
				 */
				
				$message .= '<br /><br />';
				
				if ( $return_topic)
					$message .= '<a href="'.parent::systemLink( 'topic', array( 'topic' => $topic_to_mod)).'">'.$language -> getString( 'mod_return_topic').'</a><br />';
				
				if ( $return_forum)
					$message .= '<a href="'.parent::systemLink( 'forum', array( 'forum' => $forum_to_mod)).'">'.$language -> getString( 'mod_return_forum').'</a>';
											
				/**
				 * draw message
				 */
				
				if ( $message_type){
					
					parent::draw( $style -> drawBlock( $language -> getString( 'mod_action'), $message));
													
				}else{
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'mod_action'), $message));
													
				}
			
			}
			
		}else if( count( $topics_to_mod) > 0  && in_array( $_GET['do'], $proper_topic_does)){
			
			/**
			 * topics multimoderation
			 */
			
			$topics_list = array();
					
			foreach ( $topics_to_mod as $topic_id => $topic_act){
				
				settype( $topic_id, 'integer');
				$topics_list[] = $topic_id;
				
			}
			
			/**
			 * do action
			 */
			
			switch ( $action_to_do){
				
				case 'open':
					
					/**
					 * open topics
					 */
					
					$topics_query = $mysql -> query( "SELECT topic_id, topic_forum_id FROM topics WHERE `topic_id` IN (".join( ",", $topics_list).")");
					
					$topics_to_close = array();
					
					while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
						
						if ( $session -> isMod( $topics_result['topic_forum_id']))
							$topics_to_close[] = $topics_result['topic_id'];
						
						$forum_to_mod = $topics_result['topic_forum_id'];
					
					}
					
					if ( count( $topics_to_close) > 0){
						
						$mysql -> update( array( 'topic_closed' => false), 'topics', "`topic_id` IN (".join( ",", $topics_to_close).")");
						
					}
					
					$message_type = true;
					
					$message = $language -> getString( 'mod_topics_opened');
					
					/**
					 * add log
					 */
														
					$logs -> addModLog( $language -> getString( 'mod_topics_opened_log'), array(), $topic_result['topic_forum_id'], $first_post_result['topic_id'], 0, $session -> user['user_id']);
					
				break;
				
				case 'close':
					
					/**
					 * close topics
					 */
					
					$topics_query = $mysql -> query( "SELECT topic_id, topic_forum_id FROM topics WHERE `topic_id` IN (".join( ",", $topics_list).")");
					
					$topics_to_close = array();
					
					while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
						
						if ( $session -> isMod( $topics_result['topic_forum_id']))
							$topics_to_close[] = $topics_result['topic_id'];
						
						$forum_to_mod = $topics_result['topic_forum_id'];
						
					}
					
					if ( count( $topics_to_close) > 0){
						
						$mysql -> update( array( 'topic_closed' => true), 'topics', "`topic_id` IN (".join( ",", $topics_to_close).")");
						
					}
					
					$message_type = true;
					
					$message = $language -> getString( 'mod_topics_closed');
					
					/**
					 * add log
					 */
														
					$logs -> addModLog( $language -> getString( 'mod_topics_closed_log'), array(), $topic_result['topic_forum_id'], $first_post_result['topic_id'], 0, $session -> user['user_id']);
					
				break;
				
				case 'move':
					
					/**
					 * move topics
					 */
					
					$topics_query = $mysql -> query( "SELECT topic_id, topic_forum_id FROM topics WHERE `topic_id` IN (".join( ",", $topics_list).")");
					
					$topics_to_mod = array();
					
					while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
						
						if ( $session -> isMod( $topics_result['topic_forum_id'])){
							$topics_to_mod[] = $topics_result['topic_id'];
							
							$this -> topics_to_mod = $topics_to_mod;
							$forums_to_mod[] = $topics_result['topic_forum_id'];
							
							$forum_to_mod = $topics_result['topic_forum_id'];
						}
					}
					
					if ( count( $topics_to_mod) > 0){
						
						/**
						 * check form
						 */
						
						if ( $session -> checkForm() && $_GET['finalize']){
							
							/**
							 * get new topics forum
							 */
							
							$new_topics_forum = $_POST['topics_new_forum'];
							settype( $new_topics_forum, 'integer');
							
							/**
							 * check if we can see forum
							 */
							
							if ( $session -> canSeeForum( $new_topics_forum)){
								
								/**
								 * move topics
								 */
								
								$mysql -> update( array( 'topic_forum_id' => $new_topics_forum), 'topics', "`topic_id` IN (".join( ",", $topics_to_mod).")");
								$mysql -> update( array( 'topic_read_forum' => $new_topics_forum), 'topics_reads', "`topic_read_topic` IN (".join( ",", $topics_to_mod).")");
								
								/**
								 * resynch forums
								 */
								
								foreach ( $forums_to_mod as $forum_id)
									$forums -> forumResynchronise($forum_id);
									
								$forums -> forumResynchronise($new_topics_forum);
									
								/**
								 * draw message
								 */
								
								$message_type = true;
								$message = $language -> getString( 'mod_topics_moved');
								
								/**
								 * add log
								 */
																	
								$log_keys['moved_forum_name'] = $forums -> forums_list[$new_topics_forum]['forum_name'];
								
								$logs -> addModLog( $language -> getString( 'mod_topics_moved_log'), $log_keys, $topic_result['topic_forum_id'], 0, 0, $session -> user['user_id']);
								
							}else{
								
								/**
								 * draw form
								 */
								
								$this -> drawTopicsMoveForm();
								
								/**
								 * hide message
								 */
								
								$no_message = true;
								
							}
							
						}else{
							
							/**
							 * draw form
							 */
							
							$this -> drawTopicsMoveForm();
							
							/**
							 * hide message
							 */
							
							$no_message = true;
							
						}
						
					}else{
						
						/**
						 * cant mod those topics
						 */
						
						$message = $language -> getString( 'mod_noaction');
						
					}
					
				break;
					
				case 'merge':
					
					/**
					 * merge topics
					 */
					
					$topics_query = $mysql -> query( "SELECT topic_id, topic_forum_id, topic_start_time, topic_start_user, topic_start_user_name, topic_views_num, topic_first_post_id FROM topics WHERE `topic_id` IN (".join( ",", $topics_list).") ORDER BY topic_start_time");
					
					$topics_to_mod = array();
					
					$first_topic = false;
					
					$first_topic_data = array();
					
					$views = 0;
					
					while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
						
						if ( $session -> isMod( $topics_result['topic_forum_id'])){
							$topics_to_mod[] = $topics_result['topic_id'];
							
							$this -> topics_to_mod = $topics_to_mod;
							
							$views += $topics_result['topic_views_num'];
							
							$forums_to_mod[] = $topics_result['topic_forum_id'];
							
							$forum_to_mod = $topics_result['topic_forum_id'];
							
							if ( !$first_topic){
								
								/**
								 * get firs topic data
								 */
								
								$first_topic_data = array(
									'topic_id' => $topics_result['topic_id'],
									'topic_forum_id' => $topics_result['topic_forum_id'],
									'topic_start_time' => $topics_result['topic_start_time'],
									'topic_start_user' => $topics_result['topic_start_user'],
									'topic_start_user_name' => $topics_result['topic_start_user_name'],
									'topic_first_post_id' => $topics_result['topic_first_post_id']
								);
								
								$first_topic = true;
								
							}
							
						}
					}
					
					if ( count( $topics_to_mod) > 0){
												
						/**
						 * check form
						 */
						
						if ( $session -> checkForm() && $_GET['finalize']){
							
							/**
							 * get new topics forum
							 */
							
							$new_topics_forum = $_POST['topics_new_forum'];
							settype( $new_topics_forum, 'integer');
							
							$new_topic_name = $strings -> inputClear( $_POST['topic_name'], false);
							$new_topic_info = $strings -> inputClear( $_POST['topic_info'], false);
							
							/**
							 * check if we can see forum
							 */
							
							if ( $session -> canSeeForum( $new_topics_forum)){
								
								if ( strlen( $new_topic_name) == 0){
										
									/**
									 * draw error
									 */
									
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'mod_action'), $language -> getString( 'new_topic_new_empty_name')));
									
									/**
									 * draw form
									 */
									
									$this -> drawTopicsMergeForm( true);
									
									/**
									 * hide message
									 */
									
									$no_message = true;
									
								}else{
								
									/**
									 * create new topic
									 */
									
									$new_topic_sql['topic_forum_id'] = $new_topics_forum;
									$new_topic_sql['topic_type'] = 0;
									$new_topic_sql['topic_name'] = $new_topic_name;
									$new_topic_sql['topic_info'] = $new_topic_info;
									$new_topic_sql['topic_start_time'] = $first_topic_data['topic_start_time'];
									$new_topic_sql['topic_start_user'] = $first_topic_data['topic_start_user'];
									$new_topic_sql['topic_start_user_name'] = uniSlashes( $first_topic_data['topic_start_user_name']);
									$new_topic_sql['topic_first_post_id'] = $first_topic_data['topic_first_post_id'];
									$new_topic_sql['topic_views_num'] = $views;
																		
									$mysql -> insert( $new_topic_sql, 'topics');
									
									$new_topic_id = mysql_insert_id();
									
									/**
									 * move posts
									 */
									
									$mysql -> update( array( 'post_topic' => $new_topic_id), 'posts', "`post_topic` IN (".join( ",", $topics_to_mod).")");
																		
									/**
									 * delete topics
									 */
									
									$mysql -> delete( 'topics', "`topic_id` IN (".join( ",", $topics_to_mod).")");
									$mysql -> delete( 'topics_reads', "`topic_read_topic` IN (".join( ",", $topics_to_mod).")");
									
									/**
									 * resynch topic
									 */
									
									$forums -> topicResynchronise($new_topic_id);
									
									/**
									 * resynch forums
									 */
										
									foreach ( $forums_to_mod as $forum_id)
										$forums -> forumResynchronise($forum_id);
										
									/**
									 * draw message
									 */
									
									$message_type = true;
									$message = $language -> getString( 'mod_topics_merged');
									
									/**
									 * add log
									 */
																		
									$log_keys['merged_topic_name'] = $new_topic_name;
									
									$logs -> addModLog( $language -> getString( 'mod_topics_merged_log'), $log_keys, $topic_result['topic_forum_id'], 0, 0, $session -> user['user_id']);
									
								}
									
							}else{
								
								/**
								 * draw form
								 */
								
								$this -> drawTopicsMergeForm( true);
								
								/**
								 * hide message
								 */
								
								$no_message = true;
								
							}
							
						}else{
							
							/**
							 * draw form
							 */
							
							$this -> drawTopicsMergeForm();
							
							/**
							 * hide message
							 */
							
							$no_message = true;
							
						}
						
					}else{
						
						/**
						 * cant mod those topics
						 */
						
						$message = $language -> getString( 'mod_noaction');
												
					}
					
				break;
				
				case 'delete':
					
					/**
					 * delete topics
					 */
					
					$topics_query = $mysql -> query( "SELECT topic_id, topic_forum_id FROM topics WHERE `topic_id` IN (".join( ",", $topics_list).")");
					
					$topics_to_mod = array();
					$forums_to_mod = array();
					
					while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
						
						if ( $session -> isMod( $topics_result['topic_forum_id'])){

							$topics_to_mod[] = $topics_result['topic_id'];
						
							$forums_to_mod[] = $topics_result['topic_forum_id'];
							
							$forum_to_mod = $topics_result['topic_forum_id'];
						
						}
					}
					
					if ( count( $topics_to_mod) > 0){
						
						/**
						 * check trascan
						 */
						
						if ( $settings['trashcan_turn'] && key_exists( $settings['trashcan_forum'], $forums -> forums_list) && $forum_to_mod != $settings['trashcan_forum']){
							
							/**
							 * put into trascan
							 */
							
							$moving_query = $mysql -> query( "SELECT topic_id, topic_name, topic_forum_id FROM topics WHERE `topic_id` IN (".join( ",", $topics_to_mod).")");
							
							while ( $moved_topic_result = mysql_fetch_array( $moving_query, MYSQL_ASSOC)){
								
								/**
								 * clear
								 */
							
								$moved_topic_result = $mysql -> clear( $moved_topic_result);
								
								$mysql -> update( array( 'topic_name' => $strings -> inputClear( $language -> getString( 'mod_topics_delete_from').' '.$forums -> forums_list[$moved_topic_result['topic_forum_id']]['forum_name'].': '.$moved_topic_result['topic_name'], false), 'topic_forum_id' => $settings['trashcan_forum']), 'topics', "`topic_id` = '".$moved_topic_result['topic_id']."'");
								
							}

							$mysql -> update( array( 'topic_read_forum' => $settings['trashcan_forum']), 'topics_reads', "`topic_read_topic` IN (".join( ",", $topics_to_mod).")");
															
							/**
							 * resynch trash
							 */
							
							$forums -> forumResynchronise($settings['trashcan_forum']);
							
							/**
							 * messages
							 */
							
							$message_type = true;
							$message = $language -> getString( 'mod_topics_trashed');
							
							/**
							 * add log
							 */
																							
							$logs -> addModLog( $language -> getString( 'mod_topics_trashed_log'), array(), $topic_result['topic_forum_id'], 0, 0, $session -> user['user_id']);
							
						}else{

							$users_ids = $mysql -> query( "SELECT DISTINCT post_author FROM posts WHERE `post_topic` IN (".join( ",", $topics_to_mod).") AND post_author > '-1'");
										
							$authors = array();
							
							while ( $users_result = mysql_fetch_array( $users_ids, MYSQL_ASSOC)){
								
								$authors[] = $users_result['post_author'];
								
							}
													
							/**
							 * delete topics permanently
							 */
														
							$mysql -> delete( 'posts', "`post_topic` IN (".join( ",", $topics_to_mod).")");
							$mysql -> delete( 'topics', "`topic_id` IN (".join( ",", $topics_to_mod).")");
							$mysql -> delete( 'topics_reads', "`topic_read_topic` IN (".join( ",", $topics_to_mod).")");
							
							$message_type = true;
							$message = $language -> getString( 'mod_topics_deleted');
							
							/**
							 * add log
							 */
																							
							$logs -> addModLog( $language -> getString( 'mod_topics_deleted_log'), array(), $topic_result['topic_forum_id'], 0, 0, $session -> user['user_id']);
														
						}
						
						/**
						 * resynch forums
						 */
						
						foreach ( $forums_to_mod as $forum_id)
							$forums -> forumResynchronise($forum_id);
								
						foreach ( $forums -> forums_list as $forum_id => $forum_ops){
										
							if ( $forum_ops['forum_increase_counter']){
								
								$proper_forums[] = $forum_id;
							
							}
							
						}
						
						if ( count( $proper_forums) > 0){
							
							foreach ( $authors as $author_id){
								
								/**
								 * do we resync post counter?
								 */
								
								$user_posts_q = $mysql -> query( "SELECT COUNT(*) FROM posts p LEFT JOIN topics t ON p.post_topic = t.topic_id WHERE p.post_author = '$author_id' AND t.topic_forum_id IN (".join( ",", $proper_forums).")");
								if ($result = mysql_fetch_array($user_posts_q, MYSQL_NUM))	  
									$user_posts = $result[0];

								$mysql -> update( array( 'user_posts_num' => $user_posts), 'users', "`user_id` = '$author_id'");
								
							}
						
						}				
							
					}else{
						
						/**
						 * cant mod those topics
						 */
						
						$message = $language -> getString( 'mod_noaction');
						
					}
					
				break;
				
				case 'normalize':
					
					/**
					 * normalize topics
					 */
					
					$topics_query = $mysql -> query( "SELECT topic_id, topic_forum_id FROM topics WHERE `topic_id` IN (".join( ",", $topics_list).")");
					
					$topics_to_mod = array();
					
					while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
						
						if ( $session -> isMod( $topics_result['topic_forum_id']))
							$topics_to_mod[] = $topics_result['topic_id'];
						
						$forum_to_mod = $topics_result['topic_forum_id'];
						
					}
					
					if ( count( $topics_to_mod) > 0){
						
						$mysql -> update( array( 'topic_type' => 0), 'topics', "`topic_id` IN (".join( ",", $topics_to_mod).")");
						
					}
					
					$message_type = true;
					
					$message = $language -> getString( 'mod_topics_normalised');
					
					/**
					 * add log
					 */
																					
					$logs -> addModLog( $language -> getString( 'mod_topics_normalised_log'), array(), $topic_result['topic_forum_id'], 0, 0, $session -> user['user_id']);
					
				break;
				
				case 'pin':
					
					/**
					 * pin topics
					 */
					
					$topics_query = $mysql -> query( "SELECT topic_id, topic_forum_id FROM topics WHERE `topic_id` IN (".join( ",", $topics_list).")");
					
					$topics_to_mod = array();
					
					while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
						
						if ( $session -> isMod( $topics_result['topic_forum_id']))
							$topics_to_mod[] = $topics_result['topic_id'];
						
						$forum_to_mod = $topics_result['topic_forum_id'];
						
					}
					
					if ( count( $topics_to_mod) > 0){
						
						$mysql -> update( array( 'topic_type' => 1), 'topics', "`topic_id` IN (".join( ",", $topics_to_mod).")");
						
					}
					
					$message_type = true;
					
					$message = $language -> getString( 'mod_topics_pinned');
					
					/**
					 * add log
					 */
																					
					$logs -> addModLog( $language -> getString( 'mod_topics_pinned_log'), array(), $topic_result['topic_forum_id'], 0, 0, $session -> user['user_id']);
					
				break;
				
				case 'important':
					
					/**
					 * important topics
					 */
					
					$topics_query = $mysql -> query( "SELECT topic_id, topic_forum_id FROM topics WHERE `topic_id` IN (".join( ",", $topics_list).")");
					
					$topics_to_mod = array();
					
					while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
						
						if ( $session -> isMod( $topics_result['topic_forum_id']))
							$topics_to_mod[] = $topics_result['topic_id'];
						
						$forum_to_mod = $topics_result['topic_forum_id'];
						
					}
					
					if ( count( $topics_to_mod) > 0){
						
						$mysql -> update( array( 'topic_type' => 2), 'topics', "`topic_id` IN (".join( ",", $topics_to_mod).")");
						
					}
					
					$message_type = true;
					
					$message = $language -> getString( 'mod_topics_important');
					
					/**
					 * add log
					 */
																					
					$logs -> addModLog( $language -> getString( 'mod_topics_important_log'), array(), $topic_result['topic_forum_id'], 0, 0, $session -> user['user_id']);
					
				break;
			}
			
			if ( !$no_message){
						
				/**
				 * set return links
				 */
				
				$message .= '<br /><br />';
				
				if ( $return_forum)
					$message .= '<a href="'.parent::systemLink( 'forum', array( 'forum' => $forum_to_mod)).'">'.$language -> getString( 'mod_return_forum').'</a>';
											
				/**
				 * draw message
				 */
				
				if ( $message_type){
					
					parent::draw( $style -> drawBlock( $language -> getString( 'mod_action'), $message));
													
				}else{
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'mod_action'), $message));
													
				}
			
			}
			
		}else if ( $user_to_mod > 0 && $session -> user['user_can_be_mod'] && $settings['warns_turn']){
			
			/**
			 * lets mod an user
			 */
			
			$user_query = $mysql -> query( "SELECT user_id, user_login, user_warns, user_main_group, user_other_groups FROM users WHERE `user_id` = '$user_to_mod'");
			
			if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
				
				/**
				 * clear result
				 */
				
				$user_result = $mysql -> clear( $user_result);
				$this -> user_result = $user_result;
									
				/**
				 * do warn?
				 */
				
				if ( $session -> checkForm() && $_GET['finalize']){
						
					$direction = $_POST['d'];
					settype( $direction, 'bool');
						
					$reason = $strings -> inputClear( $_POST['warn_reason'], false);
					
					/**
					 * error checking
					 */
					
					if ( strlen( $reason) == 0){
						
						/**
						 * must enter a reason
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'mod_warn'), $language -> getString( 'mod_noreason')));
				
						$this -> drawWarnForm( true);
					
					}else if ( $user_result['user_warns'] >= $settings['warns_max'] && !$direction){
						
						/**
						 * limit max
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'mod_warn'), $language -> getString( 'mod_warn_limit_max')));
				
						$this -> drawWarnForm( true);
					
					}else if ( $user_result['user_warns'] <= 0 && $direction){
						
						/**
						 * limit min
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'mod_warn'), $language -> getString( 'mod_warn_limit_min')));
				
						$this -> drawWarnForm( true);
					
					}else{
						
						/**
						 * give user an warning
						 */
						
						$new_warning_sql['user_warning_user'] = $user_to_mod;
						$new_warning_sql['user_warning_mod'] = $session -> user['user_id'];
						$new_warning_sql['user_warning_mod_name'] = uniSlashes( $session -> user['user_login']);
						$new_warning_sql['user_warning_direction'] = $direction;
						$new_warning_sql['user_warning_time'] = time();
						$new_warning_sql['user_warning_text'] = $reason;
						
						$mysql -> insert( $new_warning_sql, 'users_warnings');
						
						/**
						 * update user stats
						 */
						
						if ( $direction){
							$mysql -> update( array( 'user_warns' => $user_result['user_warns'] - 1), 'users', "`user_id` = '$user_to_mod'");
						}else{
							$mysql -> update( array( 'user_warns' => $user_result['user_warns'] + 1), 'users', "`user_id` = '$user_to_mod'");
						}
						
						/**
						 * message now
						 */
						
						parent::draw( $style -> drawBlock( $language -> getString( 'mod_warn'), $language -> getString( 'mod_warned_user').'<br /><br /><a href="'.parent::systemLink('').'">'.$language -> getString( 'forum_main_page').'</a>'));
						
					}
					
				}else{
					
					/**
					 * draw form
					 */
					
					$this -> drawWarnForm();
					
				}
			
			}else{
				
				/**
				 * not found
				 */
			
				$main_error = new main_error();
				$main_error -> type = 'error';
				parent::draw( $main_error -> display());
				
			}
			
		}else{
						
			/**
			 * no mod action
			 */
			
			$main_error = new main_error();
			$main_error -> type = 'error';
			parent::draw( $main_error -> display());
			
		}
		
	}
	
	function drawPostsMoveForm(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * and form
		 */
		
		parent::draw( $style -> drawErrorBlock( $language -> getString( 'mod_action'), $language -> getString( 'mod_posts_cant_move_to_new')));
												
		$no_message = true;
		
		/**
		 * draw moving form
		 */
		
		$move_posts_form = new form();
		$move_posts_form -> openForm( parent::systemLink( parent::getId(), array( 'do' => 'move', 'finalize' => true)));
		
		foreach ( $this -> posts_list as $post_id){
		
			$move_posts_form -> hiddenValue( 'post_select['.$post_id.']', true);
		
		}
		
		$move_posts_form -> openOpTable();
		
		$move_posts_form -> drawTextInput( $language -> getString( 'mod_posts_move_to'), 'posts_move_topic_link');
		
		$move_posts_form -> closeTable();
		$move_posts_form -> drawButton( $language -> getString( 'mod_posts_move_button'));
		$move_posts_form -> closeForm();
		
		/**
		 * draw form
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'mod_action'), $move_posts_form -> display()));
		
		
	}
	
	function drawPostsSplitForm( $retake = false){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		$move_posts_form = new form();
		$move_posts_form -> openForm( parent::systemLink( parent::getId(), array( 'do' => 'split', 'finalize' => true)));
		
		foreach ( $this -> posts_list as $post_id){
		
			$move_posts_form -> hiddenValue( 'post_select['.$post_id.']', true);
		
		}
		
		$new_topic_name = '';
		$new_topic_info = '';
		
		$new_topic_forum = $this -> first_post_result['topic_forum_id'];
		
		if ( $retake){
							
			$new_topic_name = stripslashes( $strings -> inputClear( $_POST['new_topic_name'], false));
			$new_topic_info = stripslashes( $strings -> inputClear( $_POST['new_topic_info'], false));
			
			$new_topic_forum = $_POST['new_topic_forum'];
			settype( $new_topic_forum, 'integer');
						
		}
		
		$move_posts_form -> openOpTable();
		
		$move_posts_form -> drawTextInput( $language -> getString( 'mod_posts_split_to_topic'), 'new_topic_name', $new_topic_name);
		$move_posts_form -> drawTextInput( $language -> getString( 'mod_posts_split_to_info'), 'new_topic_info', $new_topic_info);
		
		$forums_list = $forums -> getForumsList();
	
		unset( $forums_list[0]);
		
		foreach ( $forums_list as $forum_id => $forum_name){
			
			if ( !$session -> canSeeForum( $forum_id))
				unset( $forums_list[$forum_id]);
			
		}
		
		$move_posts_form -> drawList( $language -> getString( 'mod_posts_split_to'), 'new_topic_forum', $forums_list, $new_topic_forum);
		
		$move_posts_form -> closeTable();
		$move_posts_form -> drawButton( $language -> getString( 'mod_posts_split_button'));
		$move_posts_form -> closeForm();
		
		/**
		 * draw form
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'mod_action'), $move_posts_form -> display()));
		
		
	}
	
	function drawTopicsMoveForm(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * begin drawing form
		 */
		
		$move_form = new form();
		$move_form -> openForm( parent::systemLink( parent::getId(), array( 'do' => 'move', 'finalize' => true)));
		
		foreach ( $this -> topics_to_mod as $topic_id)
			$move_form -> hiddenValue( 'topic_select['.$topic_id.']', true);
			
		$move_form -> openOpTable();
		
		$forums_list = $forums -> getForumsList();
		
		unset( $forums_list[0]);
		
		foreach ( $forums_list as $forum_id => $forum_name){
			
			if ( !$session -> canSeeForum($forum_id))
				unset( $forums_list[$forum_id]);
			
		}
			
		
		$move_form -> drawList( $language -> getString( 'mod_topics_move_to'), 'topics_new_forum', $forums_list);
		
		$move_form -> closeTable();
		$move_form -> drawButton( $language -> getString( 'mod_topics_move_button'));
		$move_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'mod_action'), $move_form -> display()));
			
	}
	
	function drawTopicsMergeForm( $retake = false){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * values
		 */
		
		$topic_name = '';
		$topic_info = '';
		$topic_forum = 0;
		
		if ( $retake){
			
			$topic_name = stripslashes( $strings -> inputClear( $_POST['topic_name'], false));
			$topic_info = stripslashes( $strings -> inputClear( $_POST['topic_info'], false));
			
			$topic_forum = $_POST['topics_new_forum'];
			
			settype( $topic_forum, 'integer');
			
		}
		
		/**
		 * begin drawing form
		 */
		
		$move_form = new form();
		$move_form -> openForm( parent::systemLink( parent::getId(), array( 'do' => 'merge', 'finalize' => true)));
		
		foreach ( $this -> topics_to_mod as $topic_id)
			$move_form -> hiddenValue( 'topic_select['.$topic_id.']', true);
			
		$move_form -> openOpTable();
		
		$move_form -> drawTextInput( $language -> getString( 'new_topic_name'), 'topic_name', $topic_name);
		$move_form -> drawTextInput( $language -> getString( 'new_topic_info'), 'topic_info', $topic_info);
		
		$forums_list = $forums -> getForumsList();
		
		unset( $forums_list[0]);
		
		foreach ( $forums_list as $forum_id => $forum_name){
			
			if ( !$session -> canSeeForum($forum_id))
				unset( $forums_list[$forum_id]);
			
		}
			
		
		$move_form -> drawList( $language -> getString( 'mod_topics_merge_in'), 'topics_new_forum', $forums_list, $topic_forum);
		
		$move_form -> closeTable();
		$move_form -> drawButton( $language -> getString( 'mod_topics_merge_button'));
		$move_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'mod_action'), $move_form -> display()));
		
	}
	
	function drawWarnForm( $retake = false){

		//include globals
		include( FUNCTIONS_GLOBALS);
		
		$direction = $_GET['d'];
		settype( $direction, 'bool');
		
		$reason = '';
		
		if ( $retake){
				
			$direction = $_POST['d'];
			settype( $direction, 'bool');
				
			$reason = stripslashes( $strings -> inputClear( $_POST['warn_reason'], false));
			
		}
		
		/**
		 * start drawing form
		 */
		
		$warn_form = new form();
		$warn_form -> openForm( parent::systemLink( parent::getId(), array( 'user' => $this -> user_result['user_id'], 'finalize' => true)));
		$warn_form -> openOpTable();
		
		$warn_form -> drawList( $language -> getString( 'mod_warn_power'), 'd', array( 0 => $language -> getString( 'mod_warn_power_0'), 1 => $language -> getString( 'mod_warn_power_1')), $direction);
		$warn_form -> drawTextBox( $language -> getString( 'mod_warn_reason'), 'warn_reason', $reason);
		
		$warn_form -> closeTable();
		$warn_form -> drawButton( $language -> getString( 'mod_warn_user'));
		$warn_form -> closeForm();
				
		parent::draw( $style -> drawFormBlock( $language -> getString( 'mod_warn').': '.$this -> user_result['user_login'], $warn_form -> display()));
		
	}
	
}

?>