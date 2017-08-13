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
|	New topic
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

class action_new_topic extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
				
		/**
		 * get forum, in which we are writing
		 */
		
		$topic_forum = $_GET['forum'];
		settype( $topic_forum, 'integer');
			
		if ( key_exists( $topic_forum, $forums -> forums_list) && $session -> canSeeForum( $topic_forum)){
			
			/**
			 * get forum data
			 */
			
			$forum_query = $mysql -> query( "SELECT * FROM forums WHERE `forum_id` = '$topic_forum'");
			
			if ( $forum_result = mysql_fetch_array( $forum_query, MYSQL_ASSOC)){
				
				//clear result
				$forum_result = $mysql -> clear( $forum_result);
				$this -> forum_result = $forum_result;
				
				/**
				 * check if forum is closed
				 */
				
				if ( !$forum_result['forum_locked'] || $session -> user['user_avoid_closed_topics'] || $session -> isMod( $forum_result['forum_id'])){
				
					/**
					 * draw all
					 */
					
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
					
					$path -> addBreadcrumb( $forum_result['forum_name'], parent::systemLink( 'forum', array( 'forum' => $topic_forum)));
					$path -> addBreadcrumb( $language -> getString( 'new_topic'), parent::systemLink( parent::getId(), array( 'forum' => $topic_forum)));
					
					/**
					 * set page title
					 */
					
					$output -> setTitle( $forum_result['forum_name'].": ".$language -> getString( 'new_topic'));
					
					/**
					 * check, if forum is forum
					 */
					
					if ( $forum_result['forum_type'] == 1){
					
						/**
						 * check, if we can start topic
						 */
						
						if ( $session -> canStartTopics( $topic_forum)){
								
							/**
							 * check if we have new form
							 */
							
							if ( $session -> checkForm()){
								
								if ( $_GET['preview'] == true){
									
									/**
									 * preview topic
									 */
									
									$topic_text = stripslashes($strings -> inputClear( $_POST['new_topic_text'], false));
									
									if( strlen( $topic_text) == 0 ){
										
										parent::draw( $style -> drawBlock( $language -> getString( 'new_topic_preview'), $language -> getString( 'new_topic_new_empty_text')));
										
									}else{
										
										parent::draw( $style -> drawBlock( $language -> getString( 'new_topic_preview'), $strings -> parseBB( nl2br( $topic_text), $this -> forum_result['forum_allow_bbcode'], true)));
										
									}
									
									/**
									 * and form
									 */
									
									$this -> drawForm( $topic_forum, true);
								
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
									
									$this -> drawForm( $topic_forum, true);
									
								}else if( isset( $_GET['delete'])){
									
									/**
									 * delete attachment
									 */
									
									$attachment_to_delete = $_GET['delete'];
									settype( $attachment_to_delete, 'integer');
									
									if ( $session -> canUpload( $topic_forum)){
										
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
									
									$this -> drawForm( $topic_forum, true);
									
								}else{
									
									/**
									 * post topic
									 * start from getting values
									 */
									
									$post_author = uniSlashes(htmlspecialchars(trim( $_POST['post_author'])));
									$topic_name = $strings -> inputClear( $_POST['new_topic_name'], false);
									
									$topic_name_clear = trim( $_POST['new_topic_name']);				
									$topic_name_clear = str_replace( '&quot;', '"', $topic_name_clear);
											
									if ( get_magic_quotes_gpc())
										$topic_name_clear = stripslashes( $topic_name_clear);
									
									if ( $settings['topic_info_length'] > 0){
										$topic_info = $strings -> inputClear( $_POST['new_topic_info'], false);
										
										$topic_info_clear = trim( $_POST['new_topic_info']);
										$topic_info_clear = str_replace( '&quot;', '"', $topic_info_clear);
												
										if ( get_magic_quotes_gpc())
											$topic_info_clear = stripslashes( $topic_info_clear);
										
									
									}
									
									$topic_text = $strings -> inputClear( $_POST['new_topic_text'], false);
									$topic_prefix = $_POST['new_topic_prefix'];
			
									settype( $topic_prefix, 'integer');
			
									$topic_type = $_POST['new_topic_type'];
									settype( $topic_type, 'integer');
									
									$topic_survey_question = $strings -> inputClear( $_POST['new_survey_question'], false);
									$topic_survey_options = $strings -> inputClear( $_POST['new_survey_options'], false);
									$topic_survey_timeout = $strings -> inputClear( $_POST['new_survey_timeout'], false);
									
									settype( $topic_survey_timeout, 'integer');
									
									if ($topic_survey_timeout < 0)
										$topic_survey_timeout = 0;
									
									/**
									 * basic error check
									 */
									
									if ( $session -> user['user_id'] != -1 && $session -> user['user_last_post_time'] > (time() - $settings['board_flood_limit']) && !$session -> user['user_avoid_flood'] && $settings['board_flood_limit'] != 0){
							
										/**
										 * Too fast
										 */
										
										parent::draw( $style -> drawBlock( $language -> getString( 'new_topic'), $language -> getString( 'new_topic_new_too_fast')));
										
										$this -> drawForm( $topic_forum, true);
									
									}else if ( $session -> user['user_id'] == -1 && strlen( $post_author) == 0){

										/**
										 * guest not submited a nick
										 */
										
										parent::draw( $style -> drawBlock( $language -> getString( 'new_topic'), $language -> getString( 'new_topic_new_empty_user_name')));
										
										$this -> drawForm( $topic_forum, true);
									
									}else if ( $session -> user['user_id'] == -1 && !$captcha -> check( $_POST['captcha'])){
										
										parent::draw( $style -> drawBlock( $language -> getString( 'new_topic'), $captcha -> getError()));
										
										$this -> drawForm( $topic_forum, true);
									
									}else if ( strlen( $topic_name) == 0){
										
										parent::draw( $style -> drawBlock( $language -> getString( 'new_topic'), $language -> getString( 'new_topic_new_empty_name')));
										
										$this -> drawForm( $topic_forum, true);
									
									}else if ( strlen( $topic_name_clear) > $settings['msg_title_max_length'] || strlen( $topic_name_clear) > 90){
										
										$language -> setKey( 'name_length_limit', $settings['msg_title_max_length']);
										parent::draw( $style -> drawBlock( $language -> getString( 'new_topic'), $language -> getString( 'new_topic_new_long_name')));
										
										$this -> drawForm( $topic_forum, true);
								
									}else if ( $settings['topic_info_length'] > 0 && (strlen( $topic_info_clear) > $settings['topic_info_length'] || strlen( $topic_info_clear) > 90)){
										
										$language -> setKey( 'name_length_limit', $settings['topic_info_length']);
										parent::draw( $style -> drawBlock( $language -> getString( 'new_topic'), $language -> getString( 'new_topic_new_long_info')));
										
										$this -> drawForm( $topic_forum, true);
								
									}else if ( strlen( $topic_text) == 0){
										
										parent::draw( $style -> drawBlock( $language -> getString( 'new_topic'), $language -> getString( 'new_topic_new_empty_text')));

										$this -> drawForm( $topic_forum, true);
									
									}else{
										
										/**
										 * basic errors check done
										 * go forward
										 */
										
										if ( $session -> isMod( $forum_result['forum_id'])){
			
											if ( $topic_type > 2)
												$topic_type = 2;
												
										}else{
											
											$topic_type = 0;
											
										}
										
										if ( $topic_type < 0)
											$topic_type = 0;
										
										/**
										 * chceck if we have minimally 2 options in survey
										 */
											
										$topic_survey_ops = split( "\n", $topic_survey_options);
										
										$many_survey_ops = false;
										
										if ( count( $topic_survey_ops) >= 2)
											$many_survey_ops = true;
										
										if ( $many_survey_ops){		
											foreach ( $topic_survey_ops as $actual_id => $actual_op){
												
												$actual_op = trim( $actual_op);
												
												if ( strlen( $actual_op) == 0)
													unset( $topic_survey_ops[$actual_id]);
												
											}
										}
										
										if ( count( $topic_survey_ops) >= 2){
											$many_survey_ops = true;
										}else{
											
											$many_survey_ops = false;
										}
										
										/**
										 * topic types done
										 * surveys now
										 */
										
										if ( strlen( $topic_survey_question) > 0 && strlen( $topic_survey_options) == 0){
											
											parent::draw( $style -> drawBlock( $language -> getString( 'new_topic'), $language -> getString( 'new_topic_new_empty_survey_options')));
	
											$this -> drawForm( $topic_forum, true);
										
										}else if ( strlen( $topic_survey_question) == 0 && strlen( $topic_survey_options) > 0){
											
											parent::draw( $style -> drawBlock( $language -> getString( 'new_topic'), $language -> getString( 'new_topic_new_empty_survey_question')));
	
											$this -> drawForm( $topic_forum, true);
											
										}else if ( strlen( $topic_survey_question) > 0 && !$many_survey_ops){
											
											parent::draw( $style -> drawBlock( $language -> getString( 'new_topic'), $language -> getString( 'new_topic_new_empty_survey_options_minimal2')));
	
											$this -> drawForm( $topic_forum, true);
											
										}else if ( strlen( $topic_survey_question) > 0 && count( $topic_survey_ops) > 10){
											
											parent::draw( $style -> drawBlock( $language -> getString( 'new_topic'), $language -> getString( 'new_topic_new_empty_survey_options_maximal')));
	
											$this -> drawForm( $topic_forum, true);
											
										}else{
											
											/**
											 * all ok
											 * firstly, we will make post query
											 */
											
											$this_time = time();
											
											$new_post_query['post_author'] = $session -> user['user_id'];
											
											if ( $session -> user['user_id'] == -1){
												$new_post_query['post_author_name'] = $post_author;
											}else{
												$new_post_query['post_author_name'] = addslashes( $session -> user['user_login']);
											}
											
											$new_post_query['post_time'] = $this_time;
											$new_post_query['post_text'] = $topic_text;
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
											 * insert topic now
											 */
											
											$new_topic_sql['topic_forum_id'] = $topic_forum;
											$new_topic_sql['topic_type'] = $topic_type;
											$new_topic_sql['topic_name'] = $topic_name;
											$new_topic_sql['topic_prefix'] = $topic_prefix;
											
											if ( $settings['topic_info_length'] > 0)
												$new_topic_sql['topic_info'] = $topic_info;
											
											$new_topic_sql['topic_start_time'] = $this_time;
											$new_topic_sql['topic_start_user'] = $session -> user['user_id'];
										
											if ( $settings['forum_allow_tags']){
												
												$tags = $strings -> inputClear( $_POST['topic_tags'], false);
										
												$tags_array = split( "\n", $tags);
												$tags = array();
																					
												foreach ( $tags_array as $tag){
													
													$tag = strtolower( trim( $tag));
													
													if ( strlen( $tag) > 0 && !in_array( $tag, $tags))
														$tags[] = $tag;
													
												}
												
												$new_topic_sql['topic_tags'] = join( "\n", $tags);
										
											}
				
											if ( $session -> user['user_id'] == -1){
												$new_topic_sql['topic_start_user_name'] = $post_author;
											}else{
												$new_topic_sql['topic_start_user_name'] = addslashes( $session -> user['user_login']);
											}
											
											$new_topic_sql['topic_last_time'] = $this_time;
											$new_topic_sql['topic_last_user'] = $session -> user['user_id'];
											
											if ( $session -> user['user_id'] == -1){
												$new_topic_sql['topic_last_user_name'] = $post_author;
											}else{
												$new_topic_sql['topic_last_user_name'] = addslashes( $session -> user['user_login']);
											}
											
											$new_topic_sql['topic_first_post_id'] = $topic_post_id;
											$new_topic_sql['topic_last_post_id'] = $topic_post_id;
											
											if ( strlen( $topic_survey_question) > 0 && strlen( $topic_survey_options) > 0 && $session -> user['user_start_surveys'] && $this -> forum_result['forum_allow_surveys'] && $session -> user['user_id'] != -1){
											
												/**
												 * we have survey
												 */
												
												$new_topic_sql['topic_survey'] = true;
												$new_topic_sql['topic_survey_text'] = $topic_survey_question;
																									
											}else{
												
												/**
												 * no survey
												 */
												
												$new_topic_sql['topic_survey'] = false;
											
											}
											
											if ( $session -> isMod( $forum_result['forum_id']) || ($session -> user['user_close_own_topics'] && $session -> user['user_id'] != -1 && !$forum_result['forum_locked'])){
		
												$topic_closed = $_POST['topic_close'];
											
												settype( $topic_closed, 'bool');
				
												$new_topic_sql['topic_closed'] = $topic_closed;
																							
											}
											
											/**
											 * insert
											 */
											
											$mysql -> insert( $new_topic_sql, 'topics');
											
											$new_topic_id = mysql_insert_id();
											
											$mysql -> update( array( 'post_topic' => $new_topic_id), 'posts', "`post_id` = '$topic_post_id'");
											
											/**
											 * survey ops
											 */
											
											if ( strlen( $topic_survey_question) > 0 && strlen( $topic_survey_options) > 0 && $session -> user['user_start_surveys'] && $this -> forum_result['forum_allow_surveys']){
											
												/**
												 * we have survey
												 */
												
												$topic_survey_ops = split( "\n", $topic_survey_options);
												
												foreach ( $topic_survey_ops as $survey_op_content){
													
													$survey_op_content = $strings -> inputClear( $survey_op_content, false);
													
													if ( strlen( $survey_op_content) > 0){
													
														$new_survey_sql['survey_op_topic'] = $new_topic_id;
														$new_survey_sql['survey_op_name'] = $survey_op_content;
														
														$mysql -> insert( $new_survey_sql, 'surveys_ops');
													
													}
												}
												
											}	
											
											/**
											 * update forum stats
											 */

											if ( $session -> user['user_id'] == -1){
												$last_poster = $post_author;
											}else{
												$last_poster = addslashes( $session -> user['user_login']);
											}
											
											$forum_update['forum_last_topic'] = $new_topic_id;
											$forum_update['forum_last_topic_time'] = $this_time;
											$forum_update['forum_last_poster_id'] = $session -> user['user_id'];
											$forum_update['forum_last_poster_name'] = $last_poster;
											$forum_update['forum_threads'] = $forum_result['forum_threads'] + 1;
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
											
											//	$mysql -> insert( array( 'topic_read_time' => time(), 'topic_read_topic' => $new_topic_id, 'topic_read_user' => $session -> user['user_id'], 'topic_read_forum' => $forum_result['forum_id']), 'topics_reads');
												
											//	$forums -> checkForumRead( $forum_result['forum_id']);
																															
											}
											
											/**
											 * and overall board stats
											 * lol, there are lots of queries!
											 */
												
											$mysql -> update( array( 'setting_value' => $settings['board_posts_total'] + 1), 'settings', "`setting_setting` = 'board_posts_total'");
											$mysql -> update( array( 'setting_value' => $settings['board_threads_total'] + 1), 'settings', "`setting_setting` = 'board_threads_total'");
											
											$cache -> flushCache( 'system_settings');
											
											if ( $settings['news_turn'] && $settings['news_forum'] == $forum_result['forum_id'])
												$cache -> flushCache( 'forum_news');
												
											/**
											 * subscribe user, if he wish to
											 */
											
											if ( $settings['subscriptions_turn']){
													
												if ( $session -> user['user_auto_subscribe'] && $session -> user['user_id'] != -1)
													$mysql -> insert( array( 'subscription_topic' => $new_topic_id, 'subscription_topic_user' => $session -> user['user_id'], 'subscription_topic_time' => time()), 'subscriptions_topics');
													
												
												/**
												 * send subscriptions
												 * (always)
												 */
																	
												$subscriptions_forums_sql = $mysql -> query( "SELECT s.*, u.user_mail, u.user_permissions, u.user_main_group, u.user_other_groups, u.user_lang, f.forum_id, f.forum_name, f.forum_threads, f.forum_posts, f.forum_last_topic, f.forum_last_topic_time FROM subscriptions_forums s
												LEFT JOIN users u ON s.subscription_forum_user = u.user_id
												LEFT JOIN forums f ON s.subscription_forum = f.forum_id
												WHERE s.subscription_forum = '$topic_forum' AND s.subscription_forum_user <> '".$session -> user['user_id']."'");
												
												while ($subscriptions_forums_result = mysql_fetch_array( $subscriptions_forums_sql, MYSQL_ASSOC)){
													
													/**
													 * build up an list of masks
													 */
													
													$user_masks = array();
												
													if ( key_exists( $subscriptions_forums_result['user_permissions'], $forums -> forums_perms_masks))
														$user_masks[] = $subscriptions_forums_result['user_permissions'];
														
													/**
													 * main group perms
													 */
													
													if ( key_exists( $users -> users_groups[$subscriptions_forums_result['user_main_group']]['users_group_permissions'], $forums -> forums_perms_masks))
														$user_masks[] = $users -> users_groups[$subscriptions_forums_result['user_main_group']]['users_group_permissions'];
														
													/**
													 * sec groups
													 */
														
													$sec_groups = array();
													$sec_groups = split( ",", $subscriptions_forums_result['user_other_groups']);
													
													foreach ( $sec_groups as $group_id){
														
														if ( key_exists( $users -> users_groups[$group_id]['users_group_permissions'], $forums -> forums_perms_masks))
														$user_masks[] = $users -> users_groups[$group_id]['users_group_permissions'];
													
													}
													
													/**
													 * we should have complete list of masks
													 * lets go trought it
													 */
													
													if ( $forums -> canReadTopics( $topic_forum, $user_masks)){
														
														/**
														 * lets send an info
														 * start from swicthing language
														 */
														
														$language -> switchLanguage( $subscriptions_forums_result['user_lang']);
														
														/**
														 * rebuild stats
														 */
														
														$new_forum_posts = $subscriptions_forums_result['forum_posts'] - $subscriptions_forums_result['subscription_forum_posts'];
														$new_forum_topics = $subscriptions_forums_result['forum_threads'] - $subscriptions_forums_result['subscription_forum_topics'];
															
														/**
														 * set text
														 */
															
														$mail_title = $language -> getString( 'forum_subscription_mail_title');
														$mail_title = str_replace( "{TOPIC_NAME}", $utf8 -> turnOffChars( stripslashes( $topic_name)), $mail_title);
														$mail_title = str_replace( "{FORUM_NAME}", $utf8 -> turnOffChars( $subscriptions_forums_result['forum_name']), $mail_title);
														
														$mail_text = $language -> getString( 'forum_subscription_mail_text');
														
														$mail_text = str_replace( "{FORUM_NAME}", $utf8 -> turnOffChars( $subscriptions_forums_result['forum_name']), $mail_text);
														$mail_text = str_replace( "{TOPIC_NAME}", $utf8 -> turnOffChars( stripslashes( $topic_name)), $mail_text);
														$mail_text = str_replace( "{TOPIC_URL}", $settings['board_address'].'index.php?act=topic&topic='.$new_topic_id, $mail_text);
														$mail_text = str_replace( "{UNSUBSCRIBE_URL}", $settings['board_address'].'index.php?act=forum&forum='.$subscriptions_forums_result['forum_id'].'&do=unsubscribe', $mail_text);
														
														if ( $session -> user['user_id'] == -1){
															
															$mail_text = str_replace( "{TOPIC_AUTHOR}", stripslashes( $post_author), $mail_text);
														
														}else{
														
															$mail_text = str_replace( "{TOPIC_AUTHOR}", $session -> user['user_login'], $mail_text);
														
														}
														
														/**
														 * send mail
														 */
														
														$mail -> send( $subscriptions_forums_result['user_mail'], $mail_title, $mail_text);
														
														/**
														 * reset lang
														 */
														
														$language -> resetLanguage();
																											
													}
													
												}
											
											}
																	
											/**
											 * now move user to topic
											 */
												
											header( "Location:".parent::systemLink( 'topic', array( 'topic' => $new_topic_id)));
											
											//parent::draw( $style -> drawBlock( $language -> getString( 'new_topic'), $language -> getString( 'new_topic_new_done').':<br /><br /><a href="'..'">'.$language -> getString( 'new_topic_new_go_topic').'</a><br /><a href="'.parent::systemLink( 'forum', array( 'forum' => $topic_forum)).'">'.$language -> getString( 'new_topic_new_go_forum').'</a>'));
											
										}
											
									}
									
								}
								
							}else{
								
								/**
								 * write topic
								 */
								
								$this -> drawForm( $topic_forum);
								
							}
															
						}else{
	
							$main_error = new main_error();
							$main_error -> type = 'information';
							$main_error -> message = $language -> getString( 'new_topic_cant_start_new');
							parent::draw( $main_error -> display());
							
						}
						
					}else{
						
						$main_error = new main_error();
						$main_error -> type = 'error';
						parent::draw( $main_error -> display());
						
					}
					
				}else{
											
					$main_error = new main_error();
					$main_error -> type = 'error';
					$main_error -> message = $language -> getString( 'new_topic_cant_write_in_closed');
					parent::draw( $main_error -> display());
					
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

	function drawForm( $forum, $retake = false){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * set blank values
		 */
		
		$post_author = '';
		$topic_name = '';
		
		if ( $settings['topic_info_length'] > 0)
			$topic_info = '';
		
		$topic_text = '';
		$topic_prefix = 0;
		$topic_tags = '';
		
		$topic_type = 0;
		
		$topic_survey_question = '';
		$topic_survey_options = '';
		$topic_survey_timeout = 0;
		
		$writing_session = md5( md5( $session -> user_ip).md5(time()));
		
		if ( $session -> isMod( $this -> forum_result['forum_id']) || ($session -> user['user_close_own_topics'] && $session -> user['user_id'] != -1 && !$this -> forum_result['forum_locked'])){
				
			$topic_closed = false;
							
		}
		
		if ( $retake){
			
			$post_author = stripslashes( $strings -> inputClear( $_POST['post_author'], false));
			
			$topic_name = stripslashes( $strings -> inputClear( $_POST['new_topic_name'], false));
			
			if ( $settings['topic_info_length'] > 0)
				$topic_info = stripslashes( $strings -> inputClear( $_POST['new_topic_info'], false));
			
			$topic_text = stripslashes( $strings -> inputClear( $_POST['new_topic_text'], false));
			$topic_prefix = $_POST['new_topic_prefix'];
			
			if ( $settings['forum_allow_tags'])
				$topic_tags = stripslashes( $strings -> inputClear( $_POST['topic_tags'], false));
		
			settype( $topic_prefix, 'integer');
			
			$topic_type = $_POST['new_topic_type'];
			settype( $topic_type, 'integer');
			
			$topic_survey_question = stripslashes( $strings -> inputClear( $_POST['new_survey_question'], false));
			$topic_survey_options = stripslashes( $strings -> inputClear( $_POST['new_survey_options'], false));
			
			$writing_session = uniSlashes( $_POST['writing_session']);
			
			if ( strlen( $writing_session) != 32){
				
				$writing_session = md5( md5( $session -> user_ip).md5(time()));
				
			}
			
			if ( $session -> isMod( $this -> forum_result['forum_id']) || ($session -> user['user_close_own_topics'] && $session -> user['user_id'] == -1 && !$this -> forum_result['forum_locked'])){
				
				$topic_closed = $_POST['topic_close'];
			
				settype( $topic_closed, 'bool');
				
			}
			
		}
		
		/**
		 * begin drawing form
		 */
		
		$new_topic_form = new form();
		$new_topic_form -> openForm( parent::systemLink( parent::getId(), array( 'forum' => $forum)), 'POST', true, 'new_message');
		$new_topic_form -> hiddenValue( 'writing_session', $writing_session);
		
		if ( $session -> user['user_id'] == -1){
							
			$captcha_key = $captcha -> generate();
			
			$new_topic_form -> hiddenValue( 'captcha', $captcha_key);
		}
		
		$new_topic_form -> openOpTable();
		
		/**
		 * user login
		 */
		
		if ( $session -> user['user_id'] == -1)
			$new_topic_form -> drawTextInput( $language -> getString( 'new_topic_user'), 'post_author', $post_author);
				
		$new_topic_form -> drawInfoRow( $language -> getString( 'new_topic_name'), $forums -> getPrefixSelect( $this -> forum_result['forum_id'], 'new_topic_prefix', $topic_prefix, 'new_topic_name', $topic_name));
		
		if ( $settings['topic_info_length'] > 0)
			$new_topic_form -> drawTextInput( $language -> getString( 'new_topic_info'), 'new_topic_info', $topic_info);
		
		$new_topic_form -> drawEditor( $language -> getString( 'new_topic_text'), 'new_topic_text', $topic_text, null, $this -> forum_result['forum_allow_bbcode'], true);
		
		if ( $settings['forum_allow_tags'])
			$new_topic_form -> drawTextBox( $language -> getString( 'new_topic_tags'), 'topic_tags', $topic_tags, $language -> getString( 'new_topic_tags_help'));
		
		if ( $session -> isMod( $this -> forum_result['forum_id'])){
			
			$topic_types[0] = $language -> getString( 'new_topic_type_0');
			$topic_types[1] = $language -> getString( 'new_topic_type_1');
			$topic_types[2] = $language -> getString( 'new_topic_type_2');
			
			$new_topic_form -> drawList( $language -> getString( 'new_topic_type'), 'new_topic_type', $topic_types, $topic_type);
						
		}
		
		if ( $session -> isMod( $this -> forum_result['forum_id']) || ($session -> user['user_close_own_topics'] && $session -> user['user_id'] != -1 && !$this -> forum_result['forum_locked'])){
				
			$post_close_ops[0] = $language -> getString( 'topic_moderation_open');
			$post_close_ops[1] = $language -> getString( 'topic_moderation_close');
				
			$new_topic_form -> drawList( $language -> getString( 'new_topic_after_write'), 'topic_close', $post_close_ops, $topic_closed);
				
		}
		
		$new_topic_form -> closeTable();
		
		/**
		 * survey part
		 */
		
		if ( $this -> forum_result['forum_allow_surveys'] && $session -> user['user_start_surveys'] && $session -> user['user_id'] != -1){
			
			$new_topic_form -> drawSpacer( $language -> getString( 'new_survey'));
			$new_topic_form -> openOpTable();
			
			$new_topic_form -> drawTextInput( $language -> getString( 'new_topic_survey_question'), 'new_survey_question', $topic_survey_question);
			$new_topic_form -> drawTextBox( $language -> getString( 'new_topic_survey_options'), 'new_survey_options', $topic_survey_options, $language -> getString( 'new_topic_survey_options_help'));
			
			$new_topic_form -> closeTable();
			
		}
		
		/**
		 * attachments part
		 */
		
		if ( $session -> canUpload( $forum)){
			
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
		
		$new_topic_form -> drawButton( $language -> getString( 'new_topic_post_button'), false, '<input name="show_preview" type="button" value="'.$language -> getString( 'new_topic_preview_button').'" onClick="previewPost()" />');
		$new_topic_form -> closeForm();
		$new_topic_form -> addToContent( '<script type="text/JavaScript">
		
			function previewPost(){
				
				post_form = document.forms["new_message"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'forum' => $forum, 'preview' => true)).'";
			
				post_form.submit();
				
			}
		
			function addAttachment(){
				
				post_form = document.forms["new_message"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'forum' => $forum, 'upload' => true)).'";
			
				post_form.submit();
				
			}
			
			function deleteAttachment( att){
				
				post_form = document.forms["new_message"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'forum' => $forum)).'&delete=" + att;
			
				post_form.submit();
				
			}
			
		</script>');
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'new_topic'), $new_topic_form -> display()));
		
		
	}
	
}

?>