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
|	Edit topic
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

class action_edit_topic extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
				
		/**
		 * select post to edit
		 */
		
		$topic_to_edit = $_GET['topic'];
		settype( $topic_to_edit, 'integer');
		
		$this -> topic_to_edit = $topic_to_edit;
		
		/**
		 * make query
		 */
		
		$topic_query = $mysql -> query( "SELECT * FROM topics WHERE `topic_id` = '$topic_to_edit'");
		
		if ( $topic_result = mysql_fetch_array( $topic_query, MYSQL_ASSOC)){
			
			/**
			 * clear and save result
			 */
			
			$topic_result = $mysql -> clear( $topic_result);
			$this -> topic_result = $topic_result;
			
			/**
			 * select topic
			 */
			
			$post_query = $mysql -> query( "SELECT * FROM posts WHERE `post_id` = '".$topic_result['topic_first_post_id']."'");
			
			if ( $post_result = mysql_fetch_array( $post_query, MYSQL_ASSOC)){
				
				/**
				 * clear topic result
				 */
				
				$post_result = $mysql -> clear( $post_result);
				$this -> post_result = $post_result;
				$post_to_edit = $topic_result['topic_first_post_id'];
				
				/**
				 * select forum
				 */
				
				$forum_query = $mysql -> query( "SELECT * FROM forums WHERE `forum_id` = '".$topic_result['topic_forum_id']."'");
				
				if ( $forum_result = mysql_fetch_array( $forum_query, MYSQL_ASSOC)){
					
					/**
					 * clear forum result
					 */
					
					$forum_result = $mysql -> clear( $forum_result);
					$this -> forum_result = $forum_result;
					
					/**
					 * begin perms check
					 */
					
					if ( $session -> canSeeTopics( $forum_result['forum_id'])){
					
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
							$path -> addBreadcrumb( $language -> getString( 'edit_topic'), parent::systemLink( parent::getId(), array( 'topic' => $topic_to_edit)));
							
							/**
							 * set page title
							 */
							
							$output -> setTitle( $topic_result['topic_name'].": ".$language -> getString( 'edit_topic'));

							/**
							 * check forum type
							 */
							
							if ( $forum_result['forum_type'] == 1){
									
								/**
								 * check, if we can edit post
								 */
								
								if ( (($session -> user['user_edit_time_limit'] == 0 || ( $session -> user['user_edit_time_limit'] > 0 && ($post_result['post_time'] + ($session -> user['user_edit_time_limit'] * 60)) > time())) && ($session -> user['user_change_own_topics'] && $post_result['post_author'] == $session -> user['user_id'] && $session -> user['user_id'] > 0)) || $session -> isMod( $forum_result['forum_id'])){
										
									/**
									 * we can edit post, lets proceed
									 */
									
									if ( $session -> checkForm()){
										
										if ( $_GET['preview'] == true){
										
											/**
											 * preview topic
											 */
											
											$topic_text = stripslashes( $strings -> inputClear( $_POST['topic_text'], false));
																							
											if( strlen( $topic_text) == 0 ){
												
												parent::draw( $style -> drawBlock( $language -> getString( 'edit_post_preview'), $language -> getString( 'new_post_empty_text')));
												
											}else{
												
												parent::draw( $style -> drawBlock( $language -> getString( 'edit_post_preview'), $strings -> parseBB( nl2br( $topic_text), $this -> forum_result['forum_allow_bbcode'], true)));
												
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
																	$new_attachment_sql['attachment_post'] = $topic_result['topic_first_post_id'];
																	$new_attachment_sql['attachment_time'] = time();
																	$new_attachment_sql['attachment_name'] = uniSlashes( trim( $_FILES['new_attachment']['name']));
																	$new_attachment_sql['attachment_file'] = $new_file_name;
																	$new_attachment_sql['attachment_type'] = $attach_type;
																	$new_attachment_sql['attachment_size'] = filesize( ROOT_PATH.'uploads/'.$new_file_name);
																	
																	$mysql -> insert( $new_attachment_sql, 'attachments');	
																	
																	/**
																	 * check post attachments
																	 */
																	
																	$writing_session = uniSlashes( $_POST['writing_session']);
								
																	$attachments_query = $mysql -> query( "SELECT attachment_id FROM attachments WHERE attachment_post = '$post_to_edit'");
										
																	if ( $attachments_result = mysql_fetch_array( $attachments_query, MYSQL_ASSOC)){
																		
																		$new_post_query['post_has_attachments'] = true;
																														
																	}else{
																		
																		$new_post_query['post_has_attachments'] = false;
																		
																	}
																													
																	/**
																	 * insert post
																	 */
																	
																	$mysql -> update( $new_post_query, 'posts', "`post_id` = '$post_to_edit'");
																	
																	$cache -> flushCache( 'attachments_'.$post_to_edit);
																	
																	/**
																	 * increase upload usement
																	 */
																
																	if ( $session -> user['user_uploads_quota'] > 0){
																		
																		$quota_upade_sql['user_uploads_used'] = $session -> user['user_uploads_used'] + filesize( ROOT_PATH.'uploads/'.$new_file_name);
																		
																		$mysql -> update( $quota_upade_sql, 'users_groups', "`users_group_id` = '".$session -> user['user_main_group']."'");
																		
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
												
												$attachment_query = $mysql -> query( "SELECT attachment_file FROM attachments WHERE `attachment_id` = '$attachment_to_delete' AND `attachment_post` = '".$topic_result['topic_first_post_id']."'");
												
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
														
														/**
														 * check post attachments
														 */
														
														$writing_session = uniSlashes( $_POST['writing_session']);
					
														$attachments_query = $mysql -> query( "SELECT attachment_id FROM attachments WHERE attachment_post = '".$topic_result['topic_first_post_id']."'");
							
														if ( $attachments_result = mysql_fetch_array( $attachments_query, MYSQL_ASSOC)){
															
															$new_post_query['post_has_attachments'] = true;
																											
														}else{
															
															$new_post_query['post_has_attachments'] = false;
															
														}
																										
														/**
														 * insert post
														 */
														
														$mysql -> update( $new_post_query, 'posts', "`post_id` = '".$topic_result['topic_first_post_id']."'");
														
														$cache -> flushCache( 'attachments_'.$post_to_edit);
																
														/**
														 * and topic now
														 */
														
														$attachments_query = $mysql -> query( "SELECT post_id FROM posts WHERE post_has_attachments = '1' AND `post_topic_id` = '$topic_to_edit'");
							
														if ( $attachments_result = mysql_fetch_array( $attachments_query, MYSQL_ASSOC)){
															
															$new_topic_query['topic_attachments'] = true;
																											
														}else{
															
															$new_topic_query['topic_attachments'] = false;
															
														}
														
														$mysql -> update( $new_topic_query, 'topics', "`topic_id` = '$topic_to_edit'");
														
													}
												}
												
											}
											
											$this -> drawForm( true);
										
										}else if( $_GET['deleteop'] == true){
											
											if ( $forum_result['forum_allow_surveys'] && $session -> user['user_start_surveys']){
												
												$delete_opt = $_GET['opt'];
												settype( $delete_opt, 'integer');
												
												$survey_existing_ops = $mysql -> countRows( "surveys_ops", "`survey_op_topic` = '$topic_to_edit'");
												$survey_actual_opt = $mysql -> countRows( "surveys_ops", "`survey_op_topic` = '$topic_to_edit' AND `survey_op_id` = '$delete_opt'");
												
												if ( $survey_existing_ops > 2 && $survey_actual_opt == 1){
													
													$mysql -> delete( 'surveys_ops', "`survey_op_id` = '$delete_opt'");
													$mysql -> delete( 'surveys_votes', "`surveys_vote_option` = '$delete_opt'");
													
													/**
													 * refresh stats
													 */
													
													$survey_votes = $mysql -> countRows( "surveys_votes", "`surveys_vote_topic` = '$topic_to_edit'");
													$mysql -> update( array( 'topic_survey_votes' => $survey_votes), 'topics', "`topic_id` = '$topic_to_edit'");
													
												}
												
											}
											
											$this -> drawForm( true);
											
										}else if( $_GET['addop'] == true){
											
											if ( $forum_result['forum_allow_surveys'] && $session -> user['user_start_surveys']){
												
												$new_survey_opt = $strings -> inputClear( $_POST['new_option'], false);
												
												$survey_existing_ops = $mysql -> countRows( "surveys_ops", "`survey_op_topic` = '$topic_to_edit'");
												
												if ( $survey_existing_ops < 10 && strlen( $new_survey_opt) > 0){
													
													$mysql -> insert( array( 'survey_op_topic' => $topic_to_edit, 'survey_op_name' => $new_survey_opt), 'surveys_ops');
													
												}
												
											}
											
											$this -> drawForm( true);
											
										}else if( $_GET['update_survey'] == true){
											
											if ( $forum_result['forum_allow_surveys'] && $session -> user['user_start_surveys']){
												
												/**
												 * we are updating survey, but only options
												 */
												
												$options_to_update = $_POST['survey_opt'];
												settype( $options_to_update, 'array');
												
												foreach ( $options_to_update as $option_id => $option_name){
													
													settype( $option_id, 'integer');
													$option_name = $strings -> inputClear( $option_name, false);
													
													if ( strlen( $option_name) > 0){
														
														$mysql -> update( array( 'survey_op_name' => $option_name), 'surveys_ops', "`survey_op_id` = '$option_id' AND `survey_op_topic` = '$topic_to_edit'");
														
													}
													
												}
												
											}
											
											$this -> drawForm( true);
											
										}else if( $_GET['delete_survey'] == true){
											
											if ( $forum_result['forum_allow_surveys'] && $session -> user['user_start_surveys']){
												
												/**
												 * we are deleting survey
												 */
												
												$mysql -> delete( "surveys_ops", "`survey_op_topic` = '$topic_to_edit'");
												$mysql -> delete( "surveys_votes", "`surveys_vote_topic` = '$topic_to_edit'");
												
												$topic_update_sql['topic_survey'] = false;
												$topic_update_sql['topic_survey_text'] = '';
												$topic_update_sql['topic_survey_votes'] = 0;
												
												$mysql -> update( $topic_update_sql, 'topics', "`topic_id` = '$topic_to_edit'");
												
												$topic_result['topic_survey'] = false;
												$topic_result['topic_survey_text'] = '';
												$topic_result['topic_survey_votes'] = 0;
												
												$this -> topic_result['topic_survey'] = false;
												$this -> topic_result['topic_survey_text'] = '';
												$this -> topic_result['topic_survey_votes'] = 0;
												
											}
											
											$this -> drawForm( true);
												
										}else{
											
											/**
											 * post topic
											 * start from getting values
											 */
											
											$topic_name = $strings -> inputClear( $_POST['topic_name'], false);
											
											$topic_name_clear = trim( $_POST['topic_name']);
											$topic_name_clear = str_replace( '&quot;', '"', $topic_name_clear);
											
											if ( get_magic_quotes_gpc())
												$topic_name_clear = stripslashes( $topic_name_clear);
											
											if ( $settings['topic_info_length'] > 0){
												$topic_info = $strings -> inputClear( $_POST['topic_info'], false);
												
												$topic_info_clear = trim( $_POST['topic_info']);
												$topic_info_clear = str_replace( '&quot;', '"', $topic_info_clear);
												
												if ( get_magic_quotes_gpc())
													$topic_info_clear = stripslashes( $topic_info_clear);
											
											}
												
											$topic_type = $strings -> inputClear( $_POST['topic_type'], false);
											$topic_prefix = $_POST['new_topic_prefix'];
											
											settype( $topic_prefix, 'integer');
									
											$topic_text = $strings -> inputClear( $_POST['topic_text'], false);
											
											$topic_survey_question = $strings -> inputClear( $_POST['survey_question'], false);
											$topic_survey_options = $strings -> inputClear( $_POST['survey_options'], false);
											
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
											
											if ( !$this -> topic_result['topic_survey'] && strlen( $topic_survey_question) == 0 && strlen( $topic_survey_options) > 0){
												
												parent::draw( $style -> drawBlock( $language -> getString( 'edit_topic'), $language -> getString( 'new_topic_new_empty_survey_question')));
		
												$this -> drawForm( true);
												
											}else if ( !$this -> topic_result['topic_survey'] && strlen( $topic_survey_question) > 0 && !$many_survey_ops){
												
												parent::draw( $style -> drawBlock( $language -> getString( 'edit_topic'), $language -> getString( 'new_topic_new_empty_survey_options_minimal2')));
		
												$this -> drawForm(true);
												
											}else if ( !$this -> topic_result['topic_survey'] && strlen( $topic_survey_question) > 0 && count( $topic_survey_ops) > 10){
												
												parent::draw( $style -> drawBlock( $language -> getString( 'edit_topic'), $language -> getString( 'new_topic_new_empty_survey_options_maximal')));
		
												$this -> drawForm(true);
												
											}else if( strlen( $topic_name) == 0){
										
												parent::draw( $style -> drawBlock( $language -> getString( 'edit_topic'), $language -> getString( 'new_topic_new_empty_name')));
												
												$this -> drawForm( true);
											
											}else if ( strlen( $topic_name_clear) > $settings['msg_title_max_length'] || strlen( $topic_name_clear) > 90){
										
												$language -> setKey( 'name_length_limit', $settings['msg_title_max_length']);
												parent::draw( $style -> drawBlock( $language -> getString( 'edit_topic'), $language -> getString( 'new_topic_new_long_name')));
												
												$this -> drawForm( true);
										
											}else if ( $settings['topic_info_length'] > 0 && (strlen( $topic_info_clear) > $settings['topic_info_length'] || strlen( $topic_info_clear) > 90)){
										
												$language -> setKey( 'name_length_limit', $settings['topic_info_length']);
												parent::draw( $style -> drawBlock( $language -> getString( 'edit_topic'), $language -> getString( 'new_topic_new_long_info')));
												
												$this -> drawForm( true);
										
											}else if ( strlen( $topic_text) == 0){
														
												parent::draw( $style -> drawBlock( $language -> getString( 'edit_topic'), $language -> getString( 'new_post_empty_text')));
												
												$this -> drawForm( true);
											
											}else{
												
												/**
												 * basic errors check done
												 * go forward
												 */
													
												$this_time = time();
												
												$new_post_query['post_text'] = $topic_text;
												$new_post_query['post_edits'] = $this -> post_result['post_edits'] + 1;
												
												$new_post_query['post_last_edit'] = $this_time;
												$new_post_query['post_last_editor'] = $session -> user['user_id'];
												$new_post_query['post_last_editor_name'] = $session -> user['user_login'];
																								
												if ( $session -> isMod( $forum_result['forum_id']) && $strings -> inputClear( $_POST['topic_edit_message'], false) != uniSlashes( $post_result['topic_edit_message'])){
													
													$new_post_query['post_edit_message'] = $strings -> inputClear( $_POST['topic_edit_message'], false);
												
												}
																						
												/**
												 * check post attachments
												 */
												
												$writing_session = uniSlashes( $_POST['writing_session']);
			
												$attachments_query = $mysql -> query( "SELECT attachment_id FROM attachments WHERE attachment_post = '".$topic_result['topic_first_post_id']."'");
					
												if ( $attachments_result = mysql_fetch_array( $attachments_query, MYSQL_ASSOC)){
													
													$new_post_query['post_has_attachments'] = true;
																									
												}else{
													
													$new_post_query['post_has_attachments'] = false;
													
												}
												
												if ( strlen( $topic_survey_question) > 0){
											
													/**
													 * we have survey
													 */
													
													$edit_topic_query['topic_survey'] = true;
													$edit_topic_query['topic_survey_text'] = $topic_survey_question;
													
													/**
													 * insert survey ops
													 */
													
													if ( !$topic_result['topic_survey']){
														
														foreach ( $topic_survey_ops as $topic_survey_option){
															
															$new_survey_opt_sql['survey_op_topic'] = $topic_to_edit;
															$new_survey_opt_sql['survey_op_name'] = $strings -> inputClear( $topic_survey_option, false);
															
															$mysql -> insert( $new_survey_opt_sql, 'surveys_ops');
															
														}
														
													}
													
												}else{
													
													/**
													 * no survey
													 */
													
													$edit_topic_query['topic_survey'] = false;
												
												}
																								
												/**
												 * insert post
												 */
												
												$mysql -> update( $new_post_query, 'posts', "`post_id` = '".$topic_result['topic_first_post_id']."'");
												$topic_post_id = mysql_insert_id();
															
												/**
												 * now time for topic
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
												 * check if we have any attachments in this topic
												 */
													
												$attachments_query = $mysql -> query( "SELECT post_id FROM posts WHERE post_has_attachments = '1' AND post_topic = '$topic_to_edit'");
												
												if ( $result = mysql_fetch_array( $attachments_query, MYSQL_ASSOC)){
													
													$edit_topic_query['topic_attachments'] = true;
												
												}else{
													
													$edit_topic_query['topic_attachments'] = false;
													
												}
												
												$edit_topic_query['topic_type'] = $topic_type;
												$edit_topic_query['topic_name'] = $topic_name;
												$edit_topic_query['topic_prefix'] = $topic_prefix;
												
												if ( $settings['topic_info_length'] > 0)
													$edit_topic_query['topic_info'] = $topic_info;
												
												if ( $settings['forum_allow_tags']){
													
													$tags = $strings -> inputClear( $_POST['topic_tags'], false);
											
													$tags_array = split( "\n", $tags);
													$tags = array();
																						
													foreach ( $tags_array as $tag){
														
														$tag = strtolower( trim( $tag));
														
														if ( strlen( $tag) > 0 && !in_array( $tag, $tags))
															$tags[] = $tag;
														
													}
													
													$edit_topic_query['topic_tags'] = join( "\n", $tags);
											
												}
																									
												$mysql -> update( $edit_topic_query, 'topics', "`topic_id` = '$topic_to_edit'");
												
												/**
												 * now move user to topic
												 */
													
												header( "Location:".parent::systemLink( 'topic', array( 'topic' => $topic_to_edit)));
												
												//parent::draw( $style -> drawBlock( $language -> getString( 'edit_topic'), $language -> getString( 'edit_post_done').':<br /><br /><a href="'.parent::systemLink( 'topic', array( 'topic' => $topic_to_edit, 'p' => $topic_last_page)).'">'.$language -> getString( 'new_topic_new_go_topic').'</a><br /><a href="'.parent::systemLink( 'forum', array( 'forum' => $forum_result['forum_id'])).'">'.$language -> getString( 'new_topic_new_go_forum').'</a>'));
												
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
									 * we cant edit post
									 */
									
									$main_error = new main_error();
									$main_error -> type = 'error';
									parent::draw( $main_error -> display());
								
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
						 * no forum
						 */
						
						$main_error = new main_error();
						$main_error -> type = 'error';
						parent::draw( $main_error -> display());
						
					}
					
				}else{
					
					/**
					 * no forum
					 */
					
					$main_error = new main_error();
					$main_error -> type = 'error';
					parent::draw( $main_error -> display());
					
				}
				
			}else{
			
				/**
				 * no post
				 */
				
				$main_error = new main_error();
				$main_error -> type = 'error';
				parent::draw( $main_error -> display());
				
			}
				
		}else{
			
			/**
			 * no topic
			 */
			
			$main_error = new main_error();
			$main_error -> type = 'error';
			parent::draw( $main_error -> display());
			
		}
		
	}
	
	function drawForm( $retake = false){
			
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * set blank values
		 */
		
		$topic_name = $this -> topic_result['topic_name'];
		
		if ( $settings['topic_info_length'] > 0)
			$topic_info = $this -> topic_result['topic_info'];
		
		$topic_prefix = $this -> topic_result['topic_prefix'];
		
		$topic_type = $this -> topic_result['topic_type'];
		
		$topic_text = $this -> post_result['post_text'];
		$topic_tags = $this -> topic_result['topic_tags'];
		$topic_edit_reason = $this -> post_result['post_edit_message'];
		
		$topic_survey_question = $this -> topic_result['topic_survey_text'];
													
		/**
		 * post to quote
		 */
		
		$writing_session = uniSlashes( $_POST['writing_session']);
		
		if ( strlen( $writing_session) != 32){
			$writing_session = md5( md5( $session -> user_ip).md5(time()));
			
		}
		
		if ( $retake){
		
			$topic_name = stripslashes($strings -> inputClear( $_POST['topic_name'], false));
			
			if ( $settings['topic_info_length'] > 0)
				$topic_info = stripslashes($strings -> inputClear( $_POST['topic_info'], false));
			
			$topic_text = stripslashes($strings -> inputClear( $_POST['topic_text'], false));
			
			if ( $settings['forum_allow_tags'])
				$topic_tags = stripslashes( $strings -> inputClear( $_POST['topic_tags'], false));
			
			$topic_edit_reason = stripslashes($strings -> inputClear( $_POST['topic_edit_message'], false));
			
			$topic_survey_question = stripslashes( $strings -> inputClear( $_POST['survey_question'], false));
			$topic_survey_options = stripslashes( $strings -> inputClear( $_POST['survey_options'], false));
			
		
			$topic_prefix = $_POST['new_topic_prefix'];
			
			settype( $topic_prefix, 'integer');
			
		}
		
		/**
		 * begin drawing form
		 */
		
		$new_topic_form = new form();
		$new_topic_form -> openForm( parent::systemLink( parent::getId(), array( 'topic' => $this -> topic_to_edit)), 'POST', true, 'new_message');
		$new_topic_form -> hiddenValue( 'writing_session', $writing_session);
		$new_topic_form -> openOpTable();
		
		$new_topic_form -> drawInfoRow( $language -> getString( 'new_topic_name'), $forums -> getPrefixSelect( $this -> forum_result['forum_id'], 'new_topic_prefix', $topic_prefix, 'topic_name', $topic_name));
		
		if ( $settings['topic_info_length'] > 0)
			$new_topic_form -> drawTextInput( $language -> getString( 'new_topic_info'), 'topic_info', $topic_info);
		
		$new_topic_form -> drawEditor( $language -> getString( 'edit_post_text'), 'topic_text', $topic_text, null, $this -> forum_result['forum_allow_bbcode'], true);
	
		if ( $settings['forum_allow_tags'])
			$new_topic_form -> drawTextBox( $language -> getString( 'new_topic_tags'), 'topic_tags', $topic_tags, $language -> getString( 'new_topic_tags_help'));
				
		if ( $session -> isMod( $this -> forum_result['forum_id']))
			$new_topic_form -> drawTextInput( $language -> getString( 'edit_post_reason'), 'topic_edit_message', $topic_edit_reason);
		
		if ( $session -> isMod( $this -> forum_result['forum_id'])){
			
			$topic_types[0] = $language -> getString( 'new_topic_type_0');
			$topic_types[1] = $language -> getString( 'new_topic_type_1');
			$topic_types[2] = $language -> getString( 'new_topic_type_2');
			
			$new_topic_form -> drawList( $language -> getString( 'new_topic_type'), 'topic_type', $topic_types, $topic_type);
					
		}
		
		$new_topic_form -> closeTable();
		
		/**
		 * survey part
		 */
		
		if ( $this -> forum_result['forum_allow_surveys'] && $session -> user['user_start_surveys']){
			
			/**
			 * we will act diffrently, depending if we have survey, or not
			 */
			
			$new_topic_form -> drawSpacer( $language -> getString( 'new_survey'));
			$new_topic_form -> openOpTable();
		
			if ( $this -> topic_result['topic_survey']){
				
				/**
				 * topic already has an survey
				 */
			
				$new_topic_form -> drawTextInput( $language -> getString( 'new_topic_survey_question'), 'survey_question', $topic_survey_question);
			
				/**
				 * tab with actual survey ops
				 */
				
				$survey_ops = '<table width="100%" border="0" cellspacing="4" cellpadding="0">';
				
				$survey_ops_query = $mysql -> query( "SELECT * FROM `surveys_ops` WHERE `survey_op_topic` = '".$this -> topic_to_edit."'");
				
				$survey_ops_num = 0;
				
				while ( $survey_ops_result = mysql_fetch_array( $survey_ops_query, MYSQL_ASSOC)){
					
					//clear result
					$survey_ops_result = $mysql -> clear( $survey_ops_result);
					
					/**
					 * add to content
					 */
					
					$survey_ops .= '<tr>
								    <td NOWRAP="nowrap"><input name="survey_opt['.$survey_ops_result['survey_op_id'].']" type="text" value="'.$survey_ops_result['survey_op_name'].'" size="40"/></td>
								    <td style="width: 100%"><input name="delete_option" type="button" value="'.$language -> getString( 'edit_topic_survey_delete_opt').'" onClick="deleteOption( '.$survey_ops_result['survey_op_id'].')" /></td>
								  </tr>';
				
					$survey_ops_num ++;
					
				}
				
				$survey_ops .= '</table>';
				
				$new_topic_form -> drawInfoRow( $language -> getString( 'new_topic_survey_options'), $survey_ops);
				$new_topic_form -> addToContent( '<tr>
					<td class="opt_row1" colspan="2" style="text-align: center">
						<input name="delete_survey" type="button" value="'.$language -> getString( 'edit_topic_survey_delete').'" onClick="deleteSurvey()" />
						<input name="update_survey" type="button" value="'.$language -> getString( 'edit_topic_survey_update').'" onClick="updateSurvey()" />
					</td>
				</tr>');
				
				if ( $survey_ops_num < 10)
					$new_topic_form -> drawInfoRow( $language -> getString( 'edit_topic_survey_new_opt'), '<input name="new_option" type="text" size="40"/> <input name="add_to_survey" type="button" value="'.$language -> getString( 'edit_topic_survey_new_opt_add').'" onClick="addToSurvey()" />');			
				
			}else{
				
				/**
				 * add new survey to topic
				 */
			
				$new_topic_form -> drawTextInput( $language -> getString( 'new_topic_survey_question'), 'survey_question', $topic_survey_question);
				$new_topic_form -> drawTextBox( $language -> getString( 'new_topic_survey_options'), 'survey_options', $topic_survey_options, $language -> getString( 'new_topic_survey_options_help'));
								
			}
			
			
			$new_topic_form -> closeTable();
			
		}
		
		/**
		 * attachments part
		 */
		
		if ( $session -> canUpload( $this -> forum_result['forum_id'])){
			
			$new_topic_form -> drawSpacer( $language -> getString( 'new_attachments'));
							
				$new_topic_form -> openOpTable( true);
				
				/**
				 * draw list of existing attachments
				 */
				
				$attachments_query = $mysql -> query( "SELECT a.attachment_id, a.attachment_name, a.attachment_size, t.attachments_type_image, t.attachments_type_extension FROM attachments a LEFT JOIN attachments_types t ON a.attachment_type = t.attachments_type_id WHERE a.attachment_post = '".$this -> topic_result['topic_first_post_id']."' ORDER BY a.attachment_name");
				
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
			
		/**
		 * draw button, close form, and display it
		 */
		
		$new_topic_form -> drawButton( $language -> getString( 'edit_topic'), false, '<input name="show_preview" type="button" value="'.$language -> getString( 'new_post_preview').'" onClick="previewPost()" />');
		$new_topic_form -> closeForm();
		$new_topic_form -> addToContent( '<script type="text/JavaScript">
		
			function previewPost(){
				
				post_form = document.forms["new_message"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'topic' => $this -> topic_to_edit, 'preview' => true)).'";
			
				post_form.submit();
				
			}
		
			function addAttachment(){
				
				post_form = document.forms["new_message"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'topic' => $this -> topic_to_edit, 'upload' => true)).'";
			
				post_form.submit();
				
			}
			
			function deleteAttachment( att){
				
				post_form = document.forms["new_message"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'topic' => $this -> topic_to_edit)).'&delete=" + att;
			
				post_form.submit();
				
			}
			
			function addToSurvey(){
				
				post_form = document.forms["new_message"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'topic' => $this -> topic_to_edit, 'addop' => true)).'"
			
				post_form.submit();
				
			}
			
			function deleteOption( option){
				
				post_form = document.forms["new_message"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'topic' => $this -> topic_to_edit, 'deleteop' => true)).'&opt=" + option;
			
				post_form.submit();
				
			}
			
			function updateSurvey(){
				
				post_form = document.forms["new_message"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'topic' => $this -> topic_to_edit, 'update_survey' => true)).'";
			
				post_form.submit();
				
			}
		
			function deleteSurvey(){
				
				post_form = document.forms["new_message"];
				post_form.action = "'.parent::systemLink( parent::getId(), array( 'topic' => $this -> topic_to_edit, 'delete_survey' => true)).'";
			
				post_form.submit();
				
			}
			
		</script>');
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'edit_post'), $new_topic_form -> display()));
				
	}
	
}

?>