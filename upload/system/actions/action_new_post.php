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
|	New post
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

class action_new_post extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
				
		/**
		 * get topic, in which we are writing
		 */		
		
		$topic_to_write = $_GET['topic'];
		settype( $topic_to_write, 'integer');
		
		$this -> topic_to_write = $topic_to_write;
		
		/**
		 * select topic to write
		 */
		
		$topic_query = $mysql -> query( "SELECT * FROM topics WHERE `topic_id` = '$topic_to_write'");
		
		if ( $topic_result = mysql_fetch_array( $topic_query, MYSQL_ASSOC)){
			
			/**
			 * clear topic result
			 */
			
			$topic_result = $mysql -> clear( $topic_result);
			
			$this -> topic_result = $topic_result;
			
			/**
			 * select forum
			 */
			
			$forum_query = $mysql -> query( "SELECT * FROM forums WHERE `forum_id` = '".$topic_result['topic_forum_id']."'");
			
			if ( $forum_result = mysql_fetch_array( $forum_query, MYSQL_ASSOC)){
				
				/**
				 * clear topic result
				 */
				
				$forum_result = $mysql -> clear( $forum_result);
				
				$this -> forum_result = $forum_result;
			
				if ( $session -> canSeeTopics( $forum_result['forum_id'])){
					
					/**
					 * check, if we can write
					 */
					
					if ( $session -> canReplyTopics( $forum_result['forum_id'])){
					
						/**
						 * check, if topic is closed
						 */
						
						if ( (!$forum_result['forum_locked'] && !$topic_result['topic_closed']) || $session -> isMod( $forum_result['forum_id']) || $session -> user['user_avoid_closed_topics']){
							$curren_position = $forum_result['forum_parent'];
							
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
							
							/**
							 * add this breadcrumb
							 */
							
							$path -> addBreadcrumb( $forum_result['forum_name'], parent::systemLink( 'forum', array( 'forum' => $forum_result['forum_id'])));
							$path -> addBreadcrumb( $topic_result['topic_name'], parent::systemLink( 'topic', array( 'topic' => $topic_result['topic_id'])));
							$path -> addBreadcrumb( $language -> getString( 'new_post'), parent::systemLink( parent::getId(), array( 'topic' => $topic_to_write)));
							
							/**
							 * set page title
							 */
							
							$output -> setTitle( $topic_result['topic_name'].": ".$language -> getString( 'new_post'));
						
							/**
							 * check forum type
							 */
							
							if ( $forum_result['forum_type'] == 1){
								
								/**
								 * all ok, lets go forward
								 */
								
								if ( $session -> checkForm()){
									
									if ( $_GET['preview'] == true){
									
										/**
										 * preview topic
										 */
										
										$topic_text = stripslashes( $strings -> inputClear( $_POST['post_text'], false));
										
										if( strlen( $topic_text) == 0 ){
											
											parent::draw( $style -> drawBlock( $language -> getString( 'new_post_preview_block'), $language -> getString( 'new_post_empty_text')));
											
										}else{
											
											parent::draw( $style -> drawBlock( $language -> getString( 'new_post_preview_block'), $strings -> parseBB( nl2br( $topic_text), $this -> forum_result['forum_allow_bbcode'], true)));
											
										}
										
										/**
										 * and form
										 */
										
										$this -> drawForm( true);
									
									}else if( $_GET['upload'] == true){
										
										if ( $session -> canUpload( $forum_result['forum_id'])){
										
											/**
											 * add attachemnt to form
											 */
	
											if ( is_uploaded_file($_FILES['new_attachment']['tmp_name'])) {
												
												/**
												 * file uploaded
												 * get it extension, and mime
												 */
												
												$new_attachment_extension = substr( $_FILES['new_attachment']['name'], strrpos( $_FILES['new_attachment']['name'], '.') + 1);
												$new_attachment_mime = $_FILES['new_attachment']['type'];
												
												/**
												 * check file type
												 */
												
												$attach_type_query = $mysql -> query( "SELECT attachments_type_id, attachments_type_mime FROM attachments_types WHERE `attachments_type_extension` = '".addslashes( $new_attachment_extension)."'");
												
												if ( $attachment_type_result = mysql_fetch_array( $attach_type_query, MYSQL_NUM)){
													
													// Check mime?
													if (strlen($attachment_type_result[1]) == 0 || strtolower($new_attachment_mime) == strtolower($attachment_type_result[1]))
													{
														/**
														 * type found
														 */
														
														$attach_type = $attachment_type_result[0];
														
														/**
														 * check sizes
														 */
														
														if ( $session -> user['user_uploads_limit'] > 0 && filesile( $_FILES['new_attachment']['tmp_name']) > $session -> user['user_uploads_limit']){
															
															/**
															 * file is too big
															 */
															
															parent::draw( $style -> drawBlock( $language -> getString( 'new_attachments'), $language -> getString( 'new_topic_new_bad_file_size')));
							
															/**
															 * delete attachment
															 */
															
															unlink( $_FILES['new_attachment']['tmp_name']);
															
														}else{
															
															/**
															 * check group quota
															 */
															
															if ( $session -> user['user_uploads_quota'] > 0 && ( filesile( $_FILES['new_attachment']['tmp_name']) + $session -> user['user_uploads_used']) > $session -> user['user_uploads_quota']){
																
																/**
																 * no place for file
																 */
																
																parent::draw( $style -> drawBlock( $language -> getString( 'new_attachments'), $language -> getString( 'new_topic_new_out_file_size')));
								
																/**
																 * delete attachment
																 */
																
																unlink( $_FILES['new_attachment']['tmp_name']);
																
															}else{
																
																$writing_session = uniSlashes( $_POST['writing_session']);
				
																if ( strlen( $writing_session) == 32){
																
																	/**
																	 * all good, lets go forward
																	 * generate unique file name
																	 */
																	
																	$new_file_name = str_replace( substr( $_FILES['new_attachment']['name'], 0, strrpos( $_FILES['new_attachment']['name'], '.')), md5( time().$_SERVER['REMOTE_ADDR']), $_FILES['new_attachment']['name']);
																	
																	/**
																	 * move it
																	 */
																	
																	move_uploaded_file( $_FILES['new_attachment']['tmp_name'], ROOT_PATH.'uploads/'.$new_file_name);
																	
																	/**
																	 * generate thimbnail
																	 */
																	
																	if ( extension_loaded('gd')){
																		
																		$proper_mimes = array( 'image/jpeg', 'image/png', 'image/gif');
																		
																		if ( in_array( $new_attachment_mime, $proper_mimes)){
																																					
																			/**
																			 * image must be wider than 100px, in order to be scaled down
																			 */
																		
																			$image_size = getimagesize( ROOT_PATH.'uploads/'.$new_file_name);
																			
																			if ( $image_size[0] > 100){
																				
																				/**
																				 * we chave to scale it down
																				 */
																				
																				switch ( $new_attachment_mime){
																					
																					case 'image/jpeg':
																						
																						$image_source = imagecreatefromjpeg(  ROOT_PATH.'uploads/'.$new_file_name);
																						
																					break;
																					
																					case 'image/png':
																						
																						$image_source = imagecreatefrompng(  ROOT_PATH.'uploads/'.$new_file_name);
																						
																					break;
																					
																					case 'image/gif':
																						
																						$image_source = imagecreatefromgif(  ROOT_PATH.'uploads/'.$new_file_name);
																						
																					break;
																					
																				}
																			
																				/**
																				 * width will always equal 100
																				 */
																				
																				$new_height = $image_size[1] / ($image_size[0] / 100); 
																				
																				if ( $new_height < 1)
																					$new_height = 1;
																				
																				$image_thumb = imagecreatetruecolor( 100, $new_height);
																				
																				imagecopyresized($image_thumb, $image_source, 0, 0, 0, 0, 100, $new_height, $image_size[0], $image_size[1]);
																				
																				$thumb_name = str_ireplace( '.', '_thumb.', $new_file_name);
																																				
																				/**
																				 * save all
																				 */
																				
																				switch ( $new_attachment_mime){
																					
																					case 'image/jpeg':
																						
																						imagejpeg( $image_thumb, ROOT_PATH.'uploads/'.$thumb_name, 90);
																						
																					break;
																					
																					case 'image/png':
																						
																						imagepng( $image_thumb, ROOT_PATH.'uploads/'.$thumb_name, 90);
																						
																					break;
																					
																					case 'image/gif':
																						
																						imagegif( $image_thumb, ROOT_PATH.'uploads/'.$thumb_name, 90);
																						
																					break;
																					
																				}
																				
																				/**
																				 * destroy them
																				 */
																				
																				imagedestroy( $image_source);
																				imagedestroy( $thumb_name);
																				
																			}
																			
																		}
																		
																	}
																	
																	/**
																	 * begin sql
																	 */
													 
																	$new_attachment_sql['attachment_writing_session'] = uniSlashes( $_POST['writing_session']);
																	$new_attachment_sql['attachment_time'] = time();
																	$new_attachment_sql['attachment_name'] = uniSlashes( trim( $_FILES['new_attachment']['name']));
																	$new_attachment_sql['attachment_file'] = $new_file_name;
																	$new_attachment_sql['attachment_type'] = $attach_type;
																	$new_attachment_sql['attachment_size'] = filesize( ROOT_PATH.'uploads/'.$new_file_name);
																	
																	$mysql -> insert( $new_attachment_sql, 'attachments');	
																	
																	/**
																	 * increase upload usement
																	 */
																
																	if ( $session -> user['user_uploads_quota'] > 0){
																		
																		$quota_upade_sql['user_uploads_used'] = $session -> user['user_uploads_used'] + filesize( ROOT_PATH.'uploads/'.$new_file_name);
																		
																		$mysql -> update( $quota_upade_sql, 'users_groups', "`users_group_id` = '".$session -> user['user_main_group']."'");
																		
																	}
																	
																}else{
																	
																	unlink( $_FILES['new_attachment']['tmp_name']);
														
																}
															}
															
														}
													
													}else{
		
														parent::draw( $style -> drawBlock( $language -> getString( 'new_attachments'), $language -> getString( 'new_topic_new_bad_file')));
						
														/**
														 * delete attachment
														 */
														
														unlink( $_FILES['new_attachment']['tmp_name']);
																										
													}
												
												}else{
	
													parent::draw( $style -> drawBlock( $language -> getString( 'new_attachments'), $language -> getString( 'new_topic_new_bad_file')));
					
													/**
													 * delete attachment
													 */
													
													unlink( $_FILES['new_attachment']['tmp_name']);
																									
												}
												
											}
								
											
										}
										
										$this -> drawForm( true);
																					
									}else if( isset( $_GET['delete'])){
										
										/**
										 * delete attachment
										 */
										
										$attachment_to_delete = $_GET['delete'];
										settype( $attachment_to_delete, 'integer');
										
										if ( $session -> canUpload( $forum_result['forum_id'])){
											
											$attachment_query = $mysql -> query( "SELECT attachment_file FROM attachments WHERE `attachment_id` = '$attachment_to_delete' AND `attachment_post` = '0' AND `attachment_writing_session` = '".uniSlashes( $_POST['writing_session'])."'");
											
											if ( $attachment_result = mysql_fetch_array( $attachment_query, MYSQL_ASSOC)){
												
												//delete sql
												$mysql -> delete( 'attachments', "`attachment_id` = '$attachment_to_delete'");
												
												//clear result
												$attachment_result = $mysql -> clear( $attachment_result);
												
												if ( file_exists( ROOT_PATH.'uploads/'.$attachment_result['attachment_file'])){
												
													$attahmnent_size = filesize( ROOT_PATH.'uploads/'.$attachment_result['attachment_file']);
													
													/**
													 * delete file
													 */
													
													unlink( ROOT_PATH.'uploads/'.$attachment_result['attachment_file']);
													
													$attachment_result['attachment_file'] = str_ireplace( '.', '_thumb.', $attachment_result['attachment_file']);
													
													if ( file_exists(ROOT_PATH.'uploads/'.$attachment_result['attachment_file']))
														unlink( ROOT_PATH.'uploads/'.$attachment_result['attachment_file']);
													
													if ( $session -> user['user_uploads_quota'] > 0){
																
														$quota_upade_sql['user_uploads_used'] = $session -> user['user_uploads_used'] - filesize( ROOT_PATH.'uploads/'.$new_file_name);
														
														$mysql -> update( $quota_upade_sql, 'users_groups', "`users_group_id` = '".$session -> user['user_main_group']."'");
														
													}
												}
											}
											
										}
										
										$this -> drawForm( true);
										
									}else{
										
										/**
										 * post topic
										 * start from getting values
										 */
										
										$author_name = uniSlashes(htmlspecialchars(trim( $_POST['post_author'])));
										$post_text = $strings -> inputClear( $_POST['post_text'], false);
										
										/**
										 * basic error check
										 */
								
										if ( $session -> user['user_id'] != -1 && $session -> user['user_last_post_time'] > (time() - $settings['board_flood_limit']) && !$session -> user['user_avoid_flood'] && $settings['board_flood_limit'] != 0){
							
											/**
											 * too fast
											 */
											
											parent::draw( $style -> drawBlock( $language -> getString( 'new_post'), $language -> getString( 'new_post_cant_write_too_fast')));
											
											$this -> drawForm( $topic_forum, true);
																					
										}else if ( $session -> user['user_id'] == -1 && strlen( $author_name) == 0){

											/**
											 * guest not submited a nick
											 */
											
											parent::draw( $style -> drawBlock( $language -> getString( 'new_post'), $language -> getString( 'new_topic_new_empty_user_name')));
											
											$this -> drawForm( $topic_forum, true);
										
										}else if ( $session -> user['user_id'] == -1 && !$captcha -> check( $_POST['captcha'])){
											
											parent::draw( $style -> drawBlock( $language -> getString( 'new_post'), $captcha -> getError()));
											
											$this -> drawForm( $topic_forum, true);
										
										}else if ( strlen( $post_text) == 0){
											
											parent::draw( $style -> drawBlock( $language -> getString( 'new_post'), $language -> getString( 'new_post_empty_text')));
											
											$this -> drawForm( true);
										
										}else{
											
											/**
											 * basic errors check done
											 * go forward
											 */
												
											$this_time = time();
											
											$new_post_query['post_topic'] = $topic_to_write;
											$new_post_query['post_author'] = $session -> user['user_id'];
											
											if ( $session -> user['user_id'] == -1){
												$new_post_query['post_author_name'] = $author_name;
											}else{
												$new_post_query['post_author_name'] = addslashes( $session -> user['user_login']);
											}
											
											$new_post_query['post_time'] = $this_time;
											$new_post_query['post_text'] = $post_text;
											$new_post_query['post_ip'] = $session -> user_ip;
											$new_post_query['post_user_agent'] = $strings -> inputClear( $_SERVER['HTTP_USER_AGENT'], false);
																							
											/**
											 * check post attachments
											 */
											
											$writing_session = uniSlashes( $_POST['writing_session']);
											$update_attachments = false;
		
											$attachments_query = $mysql -> query( "SELECT attachment_id FROM attachments WHERE attachment_post = '0' AND attachment_writing_session = '$writing_session'");
				
											if ( $attachments_result = mysql_fetch_array( $attachments_query, MYSQL_ASSOC)){
												
												$update_attachments = true;
												
												$new_topic_sql['topic_attachments'] = true;
												
												$new_post_query['post_has_attachments'] = true;
																								
											}
																							
											/**
											 * insert post
											 */
											
											$mysql -> insert( $new_post_query, 'posts');
											$topic_post_id = mysql_insert_id();
											
											if ( $update_attachments){
												
												$update_attachments_sql['attachment_post'] = $topic_post_id;
												$update_attachments_sql['attachment_writing_session'] = '';
												
												$mysql -> update( $update_attachments_sql, 'attachments', "attachment_post = '0' AND attachment_writing_session = '$writing_session'");
												
											}
											
											/**
											 * update topic now
											 */
											
											$new_topic_sql['topic_posts_num'] = $topic_result['topic_posts_num'] + 1;
											$new_topic_sql['topic_last_time'] = $this_time;
											$new_topic_sql['topic_last_user'] = $session -> user['user_id'];
											
											if ( $session -> user['user_id'] == -1){
												$new_topic_sql['topic_last_user_name'] = $author_name;
											}else{
												$new_topic_sql['topic_last_user_name'] = addslashes( $session -> user['user_login']);
											}
																						
											$new_topic_sql['topic_last_post_id'] = $topic_post_id;

											if ( $session -> isMod( $forum_result['forum_id']) || ($session -> user['user_close_own_topics'] && $topic_result['topic_start_user'] == $session -> user['user_id'] && $session -> user['user_id'] != -1 && !$forum_result['forum_locked'])){
		
												$post_closed = $_POST['post_close'];
											
												settype( $post_closed, 'bool');
				
												$new_topic_sql['topic_closed'] = $post_closed;
																							
											}
																					
											$mysql -> update( $new_topic_sql, 'topics', "`topic_id` = '$topic_to_write'");
												
											/**
											 * update forum stats
											 */
																						
											if ( $session -> user['user_id'] == -1){
												$last_poster = $post_author;
											}else{
												$last_poster = addslashes( $session -> user['user_login']);
											}
											
											$forum_update['forum_last_topic'] = $topic_to_write;
											$forum_update['forum_last_topic_time'] = $this_time;
											$forum_update['forum_last_poster_id'] = $session -> user['user_id'];
											$forum_update['forum_last_poster_name'] = $last_poster;
											$forum_update['forum_posts'] = $forum_result['forum_posts'] + 1;
											
											$mysql -> update( $forum_update, 'forums', "`forum_id` = '".$forum_result['forum_id']."'");
											
											/**
											 * and user (if forum increases counter)
											 */
											
											if ( $this -> forum_result['forum_increase_counter'] && $session -> user['user_id'] != -1){
												
												$user_update_sql['user_posts_num'] = $session -> user['user_posts_num'] + 1;	
												
												/**
												 * and promote, if we have to
												 */
												
												if ( $session -> user['user_main_group'] != 1 && $users -> users_groups[$session -> user['user_main_group']]['users_group_promote_at'] != 0 && $users -> users_groups[$session -> user['user_main_group']]['users_group_promote_at'] >= ($session -> user['user_posts_num'] + 1) && $users -> users_groups[$session -> user['user_main_group']]['users_group_promote_to'] != 1){
												
													$user_update_sql['user_main_group'] = $users -> users_groups[$session -> user['user_main_group']]['users_group_promote_to'];
													
												}
												
											}
											
											if ( $session -> user['user_id'] != -1){
											
												$user_update_sql['user_last_post_time'] = $this_time;
												
												$mysql -> update($user_update_sql, 'users', "`user_id` = '".$session -> user['user_id']."'");
											
											//	$mysql -> update( array( 'topic_read_time' => time()), 'topics_reads', "`topic_read_topic` = '$topic_to_write' AND `topic_read_user` = '".$session -> user['user_id']."'");
												
											//	$forums -> checkForumRead( $forum_result['forum_id']);
												
											}
											
											/**
											 * and overall board stats
											 * lol, there are lots of queries!
											 */
												
											$mysql -> update( array( 'setting_value' => $settings['board_posts_total'] + 1), 'settings', "`setting_setting` = 'board_posts_total'");
											
											$cache -> flushCache( 'system_settings');
											
											if ( $settings['news_turn'] && $settings['news_forum'] == $forum_result['forum_id'])
												$cache -> flushCache( 'forum_news');
												
											/**
											 * subscribe user, if he wish to
											 */
												
											if ( $session -> user['user_auto_subscribe'] && $session -> user['user_id'] != -1){
												
												$subscription_sql = $mysql -> query( "SELECT * FROM subscriptions_topics WHERE `subscription_topic` = '$topic_to_write' AND `subscription_topic_user` = '".$session -> user['user_id']."'");
					
												if ( $subscription_result = mysql_fetch_array( $subscription_sql, MYSQL_ASSOC)){
												
													//do nothing!
														
												}else{
													
													$mysql -> insert( array( 'subscription_topic' => $topic_to_write, 'subscription_topic_user' => $session -> user['user_id'], 'subscription_topic_time' => time()), 'subscriptions_topics');
												
												}						
											}
																					
											/**
											 * topic last page
											 */
											
											$topic_last_page = ceil( ($topic_result['topic_posts_num'] + 2) / $settings['forum_posts_per_page']);
											
											/**
											 * send news
											 */
											
											if ( $settings['subscriptions_turn']){
											
												$subscriptions_topics_sql = $mysql -> query( "SELECT s.*, u.user_mail, u.user_permissions, u.user_main_group, u.user_other_groups, u.user_lang, t.topic_id, t.topic_forum_id, t.topic_name, t.topic_last_time, t.topic_posts_num FROM subscriptions_topics s
												LEFT JOIN users u ON s.subscription_topic_user = u.user_id
												LEFT JOIN topics t ON s.subscription_topic = t.topic_id
												WHERE s.subscription_topic = '".$topic_result['topic_id']."' AND s.subscription_topic_time > '".$topic_result['topic_last_time']."' AND s.subscription_topic_user <> '".$session -> user['user_id']."'");
												
												while ($subscriptions_topics_result = mysql_fetch_array( $subscriptions_topics_sql, MYSQL_ASSOC)){
													
													/**
													 * build up an list of masks
													 */
													
													$user_masks = array();
												
													if ( key_exists( $subscriptions_topics_result['user_permissions'], $forums -> forums_perms_masks))
														$user_masks[] = $subscriptions_topics_result['user_permissions'];
														
													/**
													 * main group perms
													 */
													
													if ( key_exists( $users -> users_groups[$subscriptions_topics_result['user_main_group']]['users_group_permissions'], $forums -> forums_perms_masks))
														$user_masks[] = $users -> users_groups[$subscriptions_topics_result['user_main_group']]['users_group_permissions'];
														
													/**
													 * sec groups
													 */
														
													$sec_groups = array();
													$sec_groups = split( ",", $subscriptions_topics_result['user_other_groups']);
													
													foreach ( $sec_groups as $group_id){
														
														if ( key_exists( $users -> users_groups[$group_id]['users_group_permissions'], $forums -> forums_perms_masks))
														$user_masks[] = $users -> users_groups[$group_id]['users_group_permissions'];
													
													}
													
													/**
													 * we should have complete list of masks
													 * lets go trought it
													 */
													
													if ( $forums -> canReadTopics( $subscriptions_topics_result['topic_forum_id'], $user_masks)){
														
														/**
														 * lets send an info
														 * start from swicthing language
														 */
														
														$language -> switchLanguage( $subscriptions_topics_result['user_lang']);
														
														/**
														 * rebuild stats
														 */
														
														$new_topic_posts = $subscriptions_topics_result['topic_posts_num'] - $subscriptions_topics_result['subscription_topic_posts'];
															
														/**
														 * set text
														 */
															
														$mail_title = $language -> getString( 'forum_subscription_topic_title');
														$mail_title = str_replace( "{TOPIC_NAME}", $utf8 -> turnOffChars($subscriptions_topics_result['topic_name']), $mail_title);
														
														$mail_text = $language -> getString( 'forum_subscription_topic_text');
														
														$mail_text = str_replace( "{TOPIC_NAME}", $utf8 -> turnOffChars($subscriptions_topics_result['topic_name']), $mail_text);
														$mail_text = str_replace( "{TOPIC_URL}", $settings['board_address'].'index.php?act=topic&topic='.$topic_to_write.'&p='.$topic_last_page.'#post'.$topic_post_id, $mail_text);
														$mail_text = str_replace( "{UNSUBSCRIBE_URL}", $settings['board_address'].'index.php?act=topic&topic='.$subscriptions_topics_result['topic_id'].'&do=unsubscribe', $mail_text);
														
														if ( $session -> user['user_id'] == -1){
															
															$mail_text = str_replace( "{POST_AUTHOR}", stripslashes( $post_author), $mail_text);
														
														}else{
														
															$mail_text = str_replace( "{POST_AUTHOR}", $session -> user['user_login'], $mail_text);
														
														}
														
														/**
														 * send mail
														 */
														
														$mail -> send( $subscriptions_topics_result['user_mail'], $mail_title, $mail_text);
														
														/**
														 * reset lang
														 */
														
														$language -> resetLanguage();
														
													}
														
												}
												
											}
											
											/**
											 * move user to post
											 */
												
											header( "Location:".parent::systemLink( 'topic', array( 'topic' => $topic_to_write, 'p' => $topic_last_page)).'#post'.$topic_post_id);
											
											//parent::draw( $style -> drawBlock( $language -> getString( 'new_post'), $language -> getString( 'new_post_done').':<br /><br /><a href="'.parent::systemLink( 'topic', array( 'topic' => $topic_to_write, 'p' => $topic_last_page)).'">'.$language -> getString( 'new_topic_new_go_topic').'</a><br /><a href="'.parent::systemLink( 'forum', array( 'forum' => $forum_result['forum_id'])).'">'.$language -> getString( 'new_topic_new_go_forum').'</a>'));
											
										}
										
									}
									
								}else{
									
									/**
									 * draw form
									 */
									
									$this -> drawForm();
									
								}
															
									
							}else{
								
								/**
								 * forum is not forum
								 */
	
								$main_error = new main_error();
								$main_error -> type = 'information';
								$main_error -> message = $language -> getString( 'new_post_cant_write_notforum');
								parent::draw( $main_error -> display());
								
							}
							
						}else{
							
							/**
							 * topic is locked
							 */
							
							$main_error = new main_error();
							$main_error -> type = 'information';
							$main_error -> message = $language -> getString( 'new_post_cant_write_closed');
							parent::draw( $main_error -> display());
							
						}
						
					}else{
						
						/**
						 * no topic
						 */
									
						$main_error = new main_error();
						$main_error -> type = 'information';
						parent::draw( $main_error -> display());
						
					}
					
				}else{
					
					/**
					 * no topic
					 */
					
					$main_error = new main_error();
					$main_error -> type = 'information';
					parent::draw( $main_error -> display());
					
				}
				
			}else{
				
				/**
				 * no topic
				 */
				
				$main_error = new main_error();
				$main_error -> type = 'information';
				parent::draw( $main_error -> display());
				
			}
				
		}else{
			
			/**
			 * no topic
			 */
			
			$main_error = new main_error();
			$main_error -> type = 'information';
			parent::draw( $main_error -> display());
			
		}
		
	}
	
	function drawForm( $retake = false){
			
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * set blank values
		 */
		
		$post_text = '';
		$post_author = '';
		
		if ( $session -> isMod( $this -> forum_result['forum_id']) || ($session -> user['user_close_own_topics'] && $this -> topic_result['topic_start_user'] == $session -> user['user_id'] && $session -> user['user_id'] != -1 && !$this -> forum_result['forum_locked'])){
				
			$post_closed = $this -> topic_result['topic_closed'];
				
		}
		
		
		/**
		 * post to quote
		 */
		
		if ( !$retake){
			
			/**
			 * proper forums
			 */
			
			$proper_forums = array(0);
			$quote_posts = array( 0);
						
			foreach ( $forums -> forums_list as $forum_id => $forum_ops){
				
				if ( $session -> canSeeTopics( $forum_id)){
					
					$proper_forums[] = $forum_id;
					
				}
			}
			
			if ( isset( $_GET['post'])){
			
				$post_to_quote = $_GET['post'];
				settype( $post_to_quote, 'integer');
				
				$quote_posts[] = $post_to_quote;
				
			}else{
				
				$post_to_quote = split( ",", $_COOKIE['topic_quote']);
				
				foreach ( $post_to_quote as $post_to_quote){
					
					settype( $post_to_quote, 'integer');
				
					if ( $post_to_quote > 0)
						$quote_posts[] = $post_to_quote;
						
				}
			}
			
			$quote_post_query = $mysql -> query( "SELECT p.post_text, p.post_author_name, t.topic_forum_id, u.user_id, u.user_login
			FROM posts p
			LEFT JOIN topics t ON p.post_topic = t.topic_id
			LEFT JOIN users u ON p.post_author = u.user_id
			WHERE p.post_id IN (".join( ",", $quote_posts).") AND t.topic_forum_id IN (".join( ",", $proper_forums).")
			ORDER BY p.post_time");
			
			while ( $post_quote_result = mysql_fetch_array( $quote_post_query, MYSQL_ASSOC)){
				
				/**
				 * clear result
				 */
			
				$post_quote_result = $mysql -> clear( $post_quote_result);
									
					if ( $post_quote_result['user_id'] == -1){
						
						$quote_author = $post_quote_result['post_author_name'];
					
					}else{
					
						
						$quote_author = $post_quote_result['user_login'];
							
					}
					
					$post_text .= '[quote="'.$quote_author.'"]'.$post_quote_result['post_text'].'[/quote]'."\n\n";
					
				}
						
		}
		
		$writing_session = md5( md5( $session -> user_ip).md5(time()));
								
		if ( $retake){
		
			$post_author = htmlspecialchars(trim( $_POST['post_author']));
			$post_text = stripslashes( $strings -> inputClear( $_POST['post_text'], false));
			
			$writing_session = uniSlashes( $_POST['writing_session']);
			
			if ( strlen( $writing_session) != 32){
				
				$writing_session = md5( md5( $session -> user_ip).md5(time()));
				
			}
			
			if ( $session -> isMod( $this -> forum_result['forum_id']) || ($session -> user['user_close_own_topics'] && $this -> topic_result['topic_start_user'] == $session -> user['user_id'] && $session -> user['user_id'] != -1 && !$this -> forum_result['forum_locked'])){
				
				$post_closed = $_POST['post_close'];
			
				settype( $post_closed, 'bool');
				
			}
		}
		
		/**
		 * begin drawing form
		 */
		
		$new_topic_form = new form();
		$new_topic_form -> openForm( parent::systemLink( parent::getId(), array( 'topic' => $this -> topic_to_write)), 'POST', true, 'new_message');
		
		if ( $session -> user['user_id'] == -1){
							
			$captcha_key = $captcha -> generate();
			
			$new_topic_form -> hiddenValue( 'captcha', $captcha_key);
		}
		
		$new_topic_form -> hiddenValue( 'writing_session', $writing_session);
		$new_topic_form -> openOpTable();
		
		/**
		 * user login
		 */
		
		if ( $session -> user['user_id'] == -1)
			$new_topic_form -> drawTextInput( $language -> getString( 'new_topic_user'), 'post_author', $post_author);
		
							
		$new_topic_form -> drawEditor( $language -> getString( 'new_post_text'), 'post_text', $post_text, null, $this -> forum_result['forum_allow_bbcode'], true);
		
		if ( $session -> isMod( $this -> forum_result['forum_id']) || ($session -> user['user_close_own_topics'] && $this -> topic_result['topic_start_user'] == $session -> user['user_id'] && $session -> user['user_id'] != -1 && !$this -> forum_result['forum_locked'])){
				
			$post_close_ops[0] = $language -> getString( 'topic_moderation_open');
			$post_close_ops[1] = $language -> getString( 'topic_moderation_close');
							
			$new_topic_form -> drawList( $language -> getString( 'new_topic_after_write'), 'post_close', $post_close_ops, $post_closed);
							
		}
		
		$new_topic_form -> closeTable();
		
		/**
		 * attachments part
		 */
		
		if ( $session -> canUpload( $this -> forum_result['forum_id'])){
			
			$new_topic_form -> drawSpacer( $language -> getString( 'new_attachments'));
			
			if ( $retake){
				
				$new_topic_form -> openOpTable( true);
				
				/**
				 * draw list of existing attachments
				 */
				
				$attachments_query = $mysql -> query( "SELECT a.attachment_id, a.attachment_name, a.attachment_size, t.attachments_type_image, t.attachments_type_extension FROM attachments a LEFT JOIN attachments_types t ON a.attachment_type = t.attachments_type_id WHERE a.attachment_post = '0' AND a.attachment_writing_session = '$writing_session' ORDER BY a.attachment_name");
				
				while ( $attachments_result = mysql_fetch_array( $attachments_query, MYSQL_ASSOC)){
					
					//clear result
					$attachments_result = $mysql -> clear( $attachments_result);
					
					/**
					 * add row
					 */
					
					$new_topic_form -> addToContent( '<tr>
						<td class="opt_row1"><img src="'.ROOT_PATH.'images/attachments_types/'.$attachments_result['attachments_type_image'].'" /> '.$attachments_result['attachment_name'].' <input name="show_preview" type="button" value="'.$language -> getString( 'new_topic_delete_button').'" onClick="deleteAttachment('.$attachments_result['attachment_id'].')" /></td>
					</tr>');
					
				}
				
				$new_topic_form -> closeTable();
				
			}
						
			$new_topic_form -> openOpTable();
						
			$new_topic_form -> drawFile( $language -> getString( 'new_attachments_add'), 'new_attachment');
			$new_topic_form -> addToContent( '<tr><td class="opt_row3" colspan="2" style="text-align: center"><input name="show_preview" type="button" value="'.$language -> getString( 'new_topic_upload_button').'" onClick="addAttachment()" /></td></tr>');
			
			$new_topic_form -> closeTable();
			
		}
		
		/**
		 * captcha
		 */
		
		if ( $session -> user['user_id'] == -1){
			$new_topic_form -> addToContent( $captcha -> drawForm($captcha_key));
			$new_topic_form -> closeTable();
		}
		
		/**
		 * draw button, close form, and display it
		 */
		
		$new_topic_form -> drawButton( $language -> getString( 'new_post_post'), false, '<input name="show_preview" type="button" value="'.$language -> getString( 'new_post_preview').'" onClick="previewPost()" />');
		$new_topic_form -> closeForm();
		$new_topic_form -> addToContent( '<script type="text/JavaScript">
		
			function previewPost(){
				
				post_form = document.forms["new_message"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'topic' => $this -> topic_to_write, 'preview' => true)).'";
			
				post_form.submit();
				
			}
		
			function addAttachment(){
				
				post_form = document.forms["new_message"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'topic' => $this -> topic_to_write, 'upload' => true)).'";
			
				post_form.submit();
				
			}
			
			function deleteAttachment( att){
				
				post_form = document.forms["new_message"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'topic' => $this -> topic_to_write)).'&delete=" + att;
			
				post_form.submit();
				
			}
			
		</script>');
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'new_post'), $new_topic_form -> display()));
		
		/**
		 * and preview
		 */

		if ( $settings['preview_posts_num'] > 0){
			
			$preview_topic_block = new form();
			$preview_topic_block -> openOpTable();
			
			/**
			 * select last 5 posts
			 */
			
			$posts_query = $mysql -> query( "SELECT p.post_author, p.post_author_name, p.post_time, p.post_text, u.user_login, u.user_main_group, u.user_other_groups, g.users_group_prefix, g.users_group_suffix FROM posts p
			LEFT JOIN users u ON p.post_author = u.user_id
			LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
			WHERE p.post_topic = '".$this -> topic_to_write."'
			ORDER BY p.post_time DESC
			LIMIT ".$settings['preview_posts_num']);
			
			while ( $post_result = mysql_fetch_array( $posts_query, MYSQL_ASSOC)){
				
				//clear result
				$post_result = $mysql -> clear( $post_result);
				
				/**
				 * post author
				 */
				
				if ( $post_result['post_author'] == -1){
				
					$post_author = $post_result['users_group_prefix'].$post_result['post_author_name'].$post_result['users_group_suffix'];
				
				}else{
					
					$post_author = '<a href="'.parent::systemLink( 'user', array( 'user' => $post_result['post_author'])).'">'.$post_result['users_group_prefix'].$post_result['user_login'].$post_result['users_group_suffix'].'</a>';
								
				}
				
				/**
				 * and message
				 */
				
				$post_message = $strings -> parseBB( nl2br( $post_result['post_text']), $this -> forum_result['forum_allow_bbcode'], true);
				
				$user_groups = array();
				$user_groups = split( ",", $post_result['user_other_groups']);
				$user_groups[] = $post_result['user_main_group'];
				
				if ( !$users -> cantCensore( $user_groups))
					$post_message = $strings -> censore( $post_message);
				
				if ( $settings['preview_posts_height'] > 0)
					$post_message = '<div style="max-height: '.$settings['preview_posts_height'].'px; overflow: auto;">'.$post_message.'</div>';	
				
				/**
				 * add row
				 */
				
				$preview_topic_block -> addToContent( '<tr>
					<td class="opt_row1" style="width: 170px; vertical-align: top">'.$post_author.'<br />'.$time -> drawDate( $post_result['post_time']).'</td>
					<td class="opt_row2">'.$post_message.'</td>
				</tr>');
				
			}
			
			$preview_topic_block -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $this -> topic_result['topic_name'], $preview_topic_block -> display()));
			
		}
		
	}
	
}

?>