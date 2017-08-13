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
|	Acp Users Admin Page
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class acp_section_users extends acp_section{
	
	function __construct(){
				
		/**
		 * include global classes pointers
		 */
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * begin
		 */
		
		$correct_acts = array( 'users', 'find_user', 'user_edit_profile', 'user_update_profile', 'user_edit_sig', 'user_change_sig', 'user_edit_login', 'user_change_login', 'user_edit_pass', 'user_change_pass', 'user_delete', 'user_kill', 'new_member', 'add_member', 'ranks', 'reps', 'reps_new', 'reps_add', 'reps_edit', 'reps_change', 'reps_delete', 'edit_rank', 'change_rank', 'pfields', 'pfields_new', 'pfields_add', 'pfields_edit', 'pfields_change', 'pfields_delete', 'users_groups', 'new_users_group', 'add_new_users_group', 'edit_users_group', 'change_users_group', 'kill_users_group', 'delete_users_group' , 'kill_users_group', 'users_notactive', 'users_banned', 'bad_words', 'autobanning');
		
		if ( !isset( $_GET['act']) || !in_array( $_GET['act'], $correct_acts)){
			$current_act = 	$correct_acts[0];
		}else{
			$current_act = $_GET['act'];
		}
		
		/**
		 * now array containing subsections
		 */
		
		$subsections_list['members'] = 'members';
		$subsections_list['unwanted_content'] = 'unwanted_content';
		
		/**
		 * and subsections list
		 */
		
		$subsections_elements_list['users'] = 'members';
		$subsections_elements_list['new_member'] = 'members';
		$subsections_elements_list['ranks'] = 'members';
		$subsections_elements_list['reps'] = 'members';
		$subsections_elements_list['pfields'] = 'members';
		$subsections_elements_list['users_groups'] = 'members';
		$subsections_elements_list['users_notactive'] = 'members';
		$subsections_elements_list['users_banned'] = 'members';
				
		$subsections_elements_list['bad_words'] = 'unwanted_content';
		$subsections_elements_list['autobanning'] = 'unwanted_content';
		
		/**
		 * draw left-side menu
		 */
		
		parent::drawSubSections( $subsections_list, $subsections_elements_list);
		
		/**
		 * do act
		 */
		
		global $actual_action;
		$actual_action = $current_act;
		
		switch ( $current_act){
			
			case 'users':
				
				/**
				 * user search page
				 */
				
				$this -> act_users();
				
			break;
			
			case 'find_user':
				
				/**
				 * do searching
				 */
			
				$this -> act_find_user();
				
			break;	
			
			case 'user_edit_profile':
				
				/**
				 * begin edition of user profile
				 */
				
				$this -> act_edit_user_profile();
				
			break;
			
			case 'user_update_profile':

				/**
				 * check form
				 */
				
				if ( $session -> checkForm()){
					
					/**
					 * check if user is specified
					 */
					
					if ( isset( $_GET['user']) && !empty( $_GET['user'])){
						
						/**
						 * select user
						 */
						
						$user_to_edit = $_GET['user'];
						settype( $user_to_edit, 'integer');
						
						$user_query = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '$user_to_edit' AND `user_id` > 0");
						
						if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
							
							if ( !$users -> isRoot( $user_to_edit) || $session -> user['user_is_root']){
					
								/**
								 * get data
								 */
							
								$user_custom_title = $strings -> inputClear( $_POST['user_custom_title'], false);
								$user_posts_num = $_POST['user_posts_num'];
								$user_mail = $strings -> inputClear( $_POST['user_mail'], false);
								$user_showmail = $_POST['user_showmail'];
								$user_want_mail = $_POST['user_want_mail'];
								$user_main_group = $_POST['user_main_group'];
								$user_other_groups = $_POST['user_other_groups'];
								$user_permissions = $_POST['user_permissions'];
								$user_notify_pm = $_POST['user_notify_pm'];
								$user_time_zone = $_POST['user_time_zone'];
								$user_dst = $_POST['user_dst'];
								$user_show_avatars = $_POST['user_show_avatars'];
								$user_show_sigs = $_POST['user_show_sigs'];
								$user_style = $_POST['user_style'];
								$user_lang = $strings -> inputClear( $_POST['user_lang'], false);
								$user_name = $strings -> inputClear( $_POST['user_name'], false);
								$user_gender = $_POST['user_gender'];
								$user_jabber_id = $strings -> inputClear( $_POST['user_jabber_id'], false);
								$user_web = $strings -> inputClear( $_POST['user_web'], false);
								$user_localisation = $strings -> inputClear( $_POST['user_localisation'], false);
								$user_interests = $strings -> inputClear( $_POST['user_interests'], false);
								
								$user_birth_day = $_POST['user_birth_day'];
								$user_birth_month = $_POST['user_birth_month'];
								$user_birth_year = $_POST['user_birth_year'];
								
								settype( $user_birth_day, 'integer');
								settype( $user_birth_month, 'integer');
								settype( $user_birth_year, 'integer');
								
								if ( $user_birth_day < 1)
									$user_birth_day = 1;
								
								if ( $user_birth_day > 31)
									$user_birth_day = 31;
									
								if ( $user_birth_month < 1)
									$user_birth_month = 1;
												
								if ( $user_birth_month > 12)
									$user_birth_month = 12;
									
								if ( $user_birth_year < 1890)
									$user_birth_year = 1890;
									
								if ( $user_birth_year > date( "Y"))
									$user_birth_year = date( "Y");
										
								if ( !empty( $_POST['user_birth_day']) && !empty( $_POST['user_birth_month']) && !empty( $_POST['user_birth_year'])){
									$user_birth_date = $user_birth_day.'-'.$user_birth_month.'-'.$user_birth_year;
								}else{
									$user_birth_date = '';
								}
								
								/**
								 * force types
								 */
								
								settype( $user_posts_num, 'integer');
								settype( $user_showmail, 'bool');
								settype( $user_want_mail, 'bool');
								
								settype( $user_main_group, 'integer');
								settype( $user_permissions, 'integer');
								
								settype( $user_other_groups, 'array');
								
								settype( $user_notify_pm, 'bool');
								
								settype( $user_time_zone, 'float');
								
								settype( $user_dst, 'bool');
								settype( $user_show_avatars, 'bool');
								settype( $user_show_sigs, 'bool');
								
								settype( $user_style, 'integer');
								settype( $user_gender, 'integer');
								
								/**
								 * do checking
								 */
								
								if ( $user_posts_num < 0)
									$user_posts_num = 0;
								
								if ( $user_time_zone < -12)
									$user_time_zone = -12;
								
								if ( $user_time_zone > 14)
									$user_time_zone = 14;
								
								/**
								 * groups checking
								 */
									
								if ( $user_main_group == 1 && !$session -> user['user_is_root']){
									$user_main_group = 3;
								}
								
								if ( in_array( 1, $user_other_groups) && !$session -> user['user_is_root']){
									
									foreach ( $user_other_groups as $groups_element => $groups_id){
										
										if ( $groups_id == 1){
											
											unset( $user_other_groups[$groups_element]);
											
										}
										
									}
									
								}
								
								/**
								 * queries checking
								 */
									
								$mail_free = true;
								
								$mail_check_query = $mysql -> query( "SELECT * FROM users WHERE `user_mail` = '$user_mail' AND `user_id` <> '$user_to_edit'");
								
								if ( $mail_result = mysql_fetch_array( $mail_check_query))
									$mail_free = false;
									
								$style_exists = false;
								
								$style_check_query = $mysql -> query( "SELECT * FROM styles WHERE `style_id` = '$user_style'");
								
								if ( $style_result = mysql_fetch_array( $style_check_query))
									$style_exists = true;
									
								$lang_exists = false;
								
								$lang_check_query = $mysql -> query( "SELECT * FROM languages WHERE `lang_id` = '$user_lang'");
								
								if ( $lang_result = mysql_fetch_array( $lang_check_query))
									$lang_exists = true;
									
								$main_group_exists = false;
								
								$group_check_query = $mysql -> query( "SELECT * FROM users_groups WHERE `users_group_id` = '$user_main_group'");
								
								if ( $group_result = mysql_fetch_array( $group_check_query))
									$main_group_exists = true;
									
								/**
								 * begin it
								 */
											
								if ( empty( $user_mail)){
									
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'acp_users_section_members_edit_member_profile_emptymail')));
									
									$this -> act_edit_user_profile();
								
								}else if( !$mail_free && !$settings['users_allow_mail_reuse']){	
								
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'acp_users_section_members_edit_member_profile_mail_taken')));
									
									$this -> act_edit_user_profile();
								
								}else if( !$main_group_exists){	
								
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'acp_users_section_members_edit_member_profile_wrong_group')));
									
									$this -> act_edit_user_profile();
								
								}else if( !$style_exists){	
								
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'acp_users_section_members_edit_member_profile_wrong_style')));
									
									$this -> act_edit_user_profile();
								
								}else if( !$lang_exists){	
								
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'acp_users_section_members_edit_member_profile_wrong_lang')));
									
									$this -> act_edit_user_profile();
								
								}else{
									
									/**
									 * can we proceed?
									 */
												
									$procced = true;
										
									/**
									 * additional fields?
									 */
												
									$fields_update_mysql = array();
									
									if ( count( $users -> custom_fields) > 0){
										
										foreach ( $users -> custom_fields as $field_id => $field_ops){
											
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
												
											/**
											 * replace field
											 */
											
											$new_field_value = $_POST[ 'field_'.$field_id];
											
											/**
											 * check if it is empty
											 */
											
											if ( strlen( $new_field_value) == 0 && $field_ops['profile_field_require']){
												
												$procced = false;
												
												$language -> setKey( 'field_name', $field_ops['profile_field_name']);
												
												parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'user_cp_need_error')));
									
											}else{
												
												if ( strlen( $new_field_value) > $field_ops['profile_field_length'] && $field_ops['profile_field_length'] > 0){
													
													$language -> setKey( 'field_name', $field_ops['profile_field_name']);
													$language -> setKey( '$field_length', $field_ops['profile_field_length']);
													
													parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'user_cp_need_error')));
													
													if ( $field_ops['profile_field_require'])
														$procced = false;
																	
												}else{
													
													$new_field_value = $strings -> inputClear( $new_field_value, false);
													
													$fields_update_mysql[ 'field_'.$field_id] = $new_field_value;
													
												}
												
											}
											
										}
										
									}
									
									if ( $procced){
									
										/**
										 * everything is okey, update
										 */
										
										$update_user_profile_sql['user_name'] = $user_name;
										$update_user_profile_sql['user_birth_date'] = $user_birth_date;
										$update_user_profile_sql['user_mail'] = $user_mail;
										$update_user_profile_sql['user_show_mail'] = $user_showmail;
										$update_user_profile_sql['user_want_mail'] = $user_want_mail;
										$update_user_profile_sql['user_jabber_id'] = $user_jabber_id;
										$update_user_profile_sql['user_web'] = $user_web;
										$update_user_profile_sql['user_localisation'] = $user_localisation;
										$update_user_profile_sql['user_interests'] = $user_interests;
										$update_user_profile_sql['user_show_sigs'] = $user_show_sigs;
										$update_user_profile_sql['user_custom_title'] = $user_custom_title;
										$update_user_profile_sql['user_posts_num'] = $user_posts_num;
										$update_user_profile_sql['user_notify_pm'] = $user_notify_pm;
										$update_user_profile_sql['user_show_avatars'] = $user_show_avatars;
										$update_user_profile_sql['user_time_zone'] = $user_time_zone;
										$update_user_profile_sql['user_dst'] = $user_dst;
										$update_user_profile_sql['user_permissions'] = $user_permissions;
										$update_user_profile_sql['user_main_group'] = $user_main_group;
										$update_user_profile_sql['user_other_groups'] = $strings -> inputClear( join( ",", $user_other_groups), false);
										$update_user_profile_sql['user_lang'] = $user_lang;
										$update_user_profile_sql['user_style'] = $user_style;
										$update_user_profile_sql['user_gender'] = $user_gender;
										
										$mysql -> update( $update_user_profile_sql, 'users', "`user_id` = '$user_to_edit'");
										
										if ( count( $fields_update_mysql) > 0)
											$mysql -> update( $fields_update_mysql, 'profile_fields_data', "`profile_fields_user` = '".$user_to_edit."'");
				
					
										/**
										 * add log
										 */
										
										$log_keys = array( 'user_profile_edited' => $user_to_edit);
										$logs -> addAdminLog( $language -> getString( 'acp_users_section_members_edit_member_profile_log'), $log_keys);
										
										/**
										 * draw message
										 */
										
										parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'acp_users_section_members_edit_member_profile_done')));
										
										/**
										 * get back to users search results
										 */
										
										$this -> act_find_user();
										
									}else{
										
										$this -> act_edit_user_profile();
									
									}
									
								}
							
							}else{
							
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'acp_users_section_members_find_member_cant_root')));
								
								$this -> act_find_user();
							}
								
						}else{
						
							/**
							 * user not found
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'acp_users_section_members_find_member_notfound')));
						
							$this -> act_users();
							
						}
						
					}else{
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_error')));
						
						$this -> act_users();
						
					}
				
				}else{
								
					$this -> act_users();
					
				}
						
			break;
			
			case 'user_change_sig':
			
				/**
				 * check form
				 */
				
				if ( $session -> checkForm()){
					
					/**
					 * check if user is specified
					 */
					
					if ( isset( $_GET['user']) && !empty( $_GET['user'])){
						
						/**
						 * select user
						 */
						
						$user_to_edit = $_GET['user'];
						
						$user_query = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '$user_to_edit' AND `user_id` > 0");
						
						if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
						
							if ( !$users -> isRoot( $user_to_edit) || $session -> user['user_is_root']){
								
								$new_user_signature = $strings -> inputClear( $_POST['user_signature'], false);
			
								/**
								 * do error checking
								 */
								
								if ( $settings['user_sig_max_length'] != 0 && strlen( $new_user_signature) > $settings['user_sig_max_length']){
									
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member_act_edit_sig'), $language -> getString( 'acp_users_section_members_find_member_act_edit_sig_too_long')));
									
									$this -> act_user_edit_sig();
									
								}else{
									
									$profile_update_mysql['user_signature'] = $new_user_signature;
									
									/**
									 * update mysql
									 */
									
									$mysql -> update( $profile_update_mysql, 'users', "`user_id` = '$user_to_edit'");
									
									/**
									 * add log
									 */
									
									$log_keys = array( 'user_sig_edit' => $user_to_edit);
									
									$logs -> addModLog( $language -> getString( 'acp_users_section_members_find_member_act_edit_sig_log'), $log_keys, 0, 0, 0, $user_to_edit);
									
									/**
									 * do rest
									 */
									
									parent::draw( $style -> drawBlock( $language -> getString( 'acp_users_section_members_find_member_act_edit_sig'), $language -> getString( 'acp_users_section_members_find_member_act_edit_sig_done')));
									
									$this -> act_find_user();
									
								}
							}else{
						
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member_act_edit_sig'), $language -> getString( 'acp_users_section_members_find_member_cant_root')));
								
								$this -> act_find_user();
							}	
						
						}else{
							
							/**
							 * user not found
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member_act_edit_sig'), $language -> getString( 'acp_users_section_members_find_member_notfound')));
						
							$this -> act_users();
							
						}
						
					}else{
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member_act_edit_sig'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_error')));
						
						$this -> act_users();
						
					}
				
				}else{
								
					$this -> act_users();
					
				}
				
			break;
			
			case 'user_edit_sig':
				
				/**
				 * run user sig editor
				 */
				
				$this -> act_user_edit_sig();
				
			break;
			
			case 'user_edit_login':
			
				/**
				 * draw login hange form
				 */
				
				$this -> act_user_edit_login();
				
			break;
				
			case 'user_change_login':
			
				if( $session -> checkForm()){
					
					/**
					 * proceed with error checking
					 */
					
					if ( isset( $_GET['user']) && !empty( $_GET['user'])){
			
						/**
						 * select user
						 */
						
						$user_to_edit = $_GET['user'];
						
						$user_query = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '$user_to_edit' AND `user_id` > 0");
						
						if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
	
							if ( !$users -> isRoot( $user_to_edit) || $session -> user['user_is_root']){
				
								/**
								 * check if new user login is submitted
								 */
								
								$user_login = htmlspecialchars(uniSlashes(trim($_POST['user_login'])));
							
								$login_free = true;
								
								/**
								 * check login avaibility
								 */
								
								$login_check_query = $mysql -> query( "SELECT `user_login` FROM users WHERE `user_login` = '$login_free'");
								
								if ( $login_check = mysql_fetch_array( $login_check_query, MYSQL_ASSOC))
									$login_free = true;
									
								if ( strlen( $user_login) == 0){
									
									/**
									 * empty
									 */
									
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_login_change'), $language -> getString( 'acp_users_section_members_login_change_user_login_empty')));
									
									$this -> act_user_edit_login();
									
								}else if( !$login_free){
																	
									/**
									 * empty
									 */
									
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_login_change'), $language -> getString( 'acp_users_section_members_login_change_user_login_used')));
									
									$this -> act_user_edit_login();
									
								}else{
									
									/**
									 * all okey login
									 */
									
									$user_update_sql['user_login'] = $user_login;
									
									$mysql -> update( $user_update_sql, 'users', "`user_id` = '$user_to_edit'");
									
									$mysql -> update( array( 'post_author_name' => $user_login), 'posts', "`post_author` = '$user_to_edit'");
									$mysql -> update( array( 'topic_start_user_name' => $user_login), 'topics', "`topic_start_user` = '$user_to_edit'");
									$mysql -> update( array( 'topic_last_user_name' => $user_login), 'topics', "`topic_last_user` = '$user_to_edit'");
									$mysql -> update( array( 'forum_last_poster_name' => $user_login), 'forums', "`forum_last_poster_id` = '$user_to_edit'");
									$mysql -> update( array( 'users_message_author_name' => $user_login), 'users_messages', "`users_message_author` = '$user_to_edit'");
									$mysql -> update( array( 'user_warning_mod_name' => $user_login), 'users_warnings', "`user_warning_mod` = '$user_to_edit'");
									$mysql -> update( array( 'shout_author_name' => $user_login), 'shouts', "`shout_author` = '$user_to_edit'");
									
									/**
									 * clear cache
									 */
									
									$cache -> flushCache( 'moderators');
									$cache -> flushCache( 'forum_news');
									
									/**
									 * add log
									 */
									
									$log_keys = array( 'user_login_changed_id' => $user_to_edit);
									$logs -> addAdminLog( $language -> getString( 'acp_users_section_members_login_change_user_login_log'), $log_keys);
									
									/**
									 * draw message
									 */
									
									parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_section_members_login_change'), $language -> getString( 'acp_users_section_members_login_change_user_login_done')));
									
									/**
									 * search results
									 */
									
									$this -> act_find_user();
									
								}
							
							}else{
							
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_login_change'), $language -> getString( 'acp_users_section_members_find_member_cant_root')));
								
								$this -> act_find_user();
							}
								
						}else{
							
							/**
							 * user not found
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_login_change'), $language -> getString( 'acp_users_section_members_find_member_notfound')));
						
							$this -> act_users();
							
						}
						
					}else{
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_login_change'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_error')));
						
						$this -> act_users();
						
					}
					
				}else{
					
					/**
					 * no form submited
					 */
					
					$this -> act_users();
					
				}
				
			break;
			
			case 'user_edit_pass':
			
				/**
				 * draw pass change form
				 */
				
				$this -> act_user_edit_pass();
				
			break;
				
			case 'user_change_pass':
			
				if( $session -> checkForm()){
					
					/**
					 * proceed with error checking
					 */
					
					if ( isset( $_GET['user']) && !empty( $_GET['user'])){
			
						/**
						 * select user
						 */
						
						$user_to_edit = $_GET['user'];
						
						$user_query = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '$user_to_edit' AND `user_id` > 0");
						
						if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
	
							if ( !$users -> isRoot( $user_to_edit) || $session -> user['user_is_root']){
				
								/**
								 * check if new user login is submitted
								 */
								
								$user_pass_new = trim( $_POST['user_pass']);
								
								$user_pass = md5( md5( $user_pass_new).md5( $user_pass_new));
									
								if ( strlen( $user_pass_new) == 0){
									
									/**
									 * empty
									 */
									
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_pass_change'), $language -> getString( 'acp_users_section_members_pass_change_user_pass_empty')));
									
									$this -> act_user_edit_pass();
									
								}else{
									
									/**
									 * all okey login
									 */
									
									$user_update_sql['user_password'] = $user_pass;
									
									$mysql -> update( $user_update_sql, 'users', "`user_id` = '$user_to_edit'");
									
									/**
									 * add log
									 */
									
									$log_keys = array( 'user_pass_changed_id' => $user_to_edit);
									$logs -> addAdminLog( $language -> getString( 'acp_users_section_members_pass_change_user_pass_log'), $log_keys);
									
									/**
									 * draw message
									 */
									
									parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_section_members_pass_change'), $language -> getString( 'acp_users_section_members_pass_change_user_pass_done')));
									
									/**
									 * search results
									 */
									
									$this -> act_find_user();
									
								}
							
							}else{
							
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_pass_change'), $language -> getString( 'acp_users_section_members_find_member_cant_root')));
								
								$this -> act_find_user();
							}
									
						}else{
							
							/**
							 * user not found
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_pass_change'), $language -> getString( 'acp_users_section_members_find_member_notfound')));
						
							$this -> act_users();
							
						}
						
					}else{
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_pass_change'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_error')));
						
						$this -> act_users();
						
					}
					
				}else{
					
					/**
					 * no form submited
					 */
					
					$this -> act_users();
					
				}
				
			break;
			
			case 'user_delete':
				
				/**
				 * delete user
				 */
				
				$this -> act_delete_user();
				
			break;
			
			case 'user_kill':
				
				if ( $session -> checkForm()){
					
					/**
					 * check if user is specified
					 */
					
					if ( isset( $_GET['user']) && !empty( $_GET['user'])){
						
						/**
						 * select user
						 */
						
						$user_to_edit = $_GET['user'];
						
						$user_query = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '$user_to_edit' AND `user_id` > 0");
						
						if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
						
							if ( !$users -> isRoot( $user_to_edit) || $session -> user['user_is_root']){
					
								/**
								 * check if we have to delete
								 */
								
								if( $_POST['delete_user_accept']){
									
									/**
									 * delete user
									 */
									
									$mysql -> delete( 'users', "`user_id` = '$user_to_edit'");
									$mysql -> delete( 'users_sessions', "`users_session_user_id` = '$user_to_edit'");
									$mysql -> delete( 'users_autologin', "`users_autologin_user` = '$user_to_edit'");
									$mysql -> delete( 'topics_reads', "`topic_read_user` = '$user_to_edit'");
									$mysql -> delete( 'forums_reads', "`forums_read_user` = '$user_to_edit'");
									$mysql -> delete( 'moderators', "`moderator_user_id` = '$user_to_edit'");
									$mysql -> delete( 'users_warnings', "`user_warning_user` = '$user_to_edit'");
									$mysql -> delete( 'subscriptions_forums', "`subscription_forum_user` = '$user_to_edit'");
									$mysql -> delete( 'subscriptions_topics', "`subscription_topic_user` = '$user_to_edit'");
									$mysql -> delete( 'profile_fields_data', "`profile_fields_user` = '$user_to_edit'");
									$mysql -> delete( 'topics_votes', "`topic_vote_user` = '$user_to_edit'");
									$mysql -> delete( 'surveys_votes', "`surveys_vote_user` = '$user_to_edit'");
									
									/**
									 * flush cache
									 */
									
									$cache -> flushCache( 'users_online');
									$cache -> flushCache( 'moderators');
									
									/**
									 * update tables
									 */
									
									$mysql -> update( array( 'topic_start_user' => -1), 'topics', "`topic_start_user` = '$user_to_edit'");
									$mysql -> update( array( 'topic_last_user' => -1), 'topics', "`topic_last_user` = '$user_to_edit'");
									$mysql -> update( array( 'forum_last_poster_id' => -1), 'forums', "`forum_last_poster_id` = '$user_to_edit'");
									$mysql -> update( array( 'users_message_author' => -1), 'users_messages', "`users_message_author` = '$user_to_edit'");
									$mysql -> update( array( 'shout_author' => -1), 'shout_id', "`shout_author` = '$user_to_edit'");
									$mysql -> update( array( 'user_warning_mod' => -1), 'users_warnings', "`user_warning_mod` = '$user_to_edit'");
									
									/**
									 * log
									 */
									
									$log_keys = array( 'user_delete_id' => $user_to_edit);
									$logs -> addAdminLog( $language -> getString( 'acp_users_section_members_delete_log'), $log_keys);
									
									/**
									 * message
									 */
									
									parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_section_members_delete'), $language -> getString( 'acp_users_section_members_delete_done')));
									
									/**
									 * search
									 */
									
									$this -> act_users();
									
								}else{
									
									/**
									 * doing nothing
									 */
									
									$this -> act_find_user();
								
								}
								
							}else{
							
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_delete'), $language -> getString( 'acp_users_section_members_find_member_cant_root')));
								
								$this -> act_find_user();
							}
														
						}else{
				
							/**
							 * user not found
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_delete'), $language -> getString( 'acp_users_section_members_find_member_notfound')));
						
							$this -> act_users();
							
						}
						
					}else{
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_delete'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_error')));
						
						$this -> act_users();
						
					}
					
				}else{
					
					$this -> act_users();
					
				}
				
			break;
			
			case 'new_member':
				
				/**
				 * new member creation form
				 */
				
				$this -> act_new_member();
				
			break;
			
			case 'add_member':
				
				/**
				 * add new member
				 */
				
				if ( $session -> checkForm()){
					
					/**
					 * form submited
					 */
					
					$user_group = $_POST['user_group'];
					
					if ( $user_group == 1 && !$session -> user['user_is_root'])
						$user_group = 3;
					
					$new_user_added = $users -> newUser( $_POST['user_login'], $_POST['user_pass'], $_POST['user_pass_rep'], $_POST['user_mail'], $_POST['user_group']);
					
					if ( $new_user_added){
						
						/**
						 * new user added
						 */
						
						parent::draw($style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_new_member'), $language -> getString( 'acp_users_section_members_new_member_create_done')));
						
						/**
						 * create log
						 */
						
						$log_keys = array( 'new_member_name' => uniSlashes( htmlspecialchars( trim( $_POST['user_login']))));
						
						$logs -> addAdminLog( $language -> getString( 'acp_users_section_members_new_member_create_log'), $log_keys);
						
						/**
						 * jup to search
						 */
						
						$this -> act_users();
						
					}else{
						
						/**
						 * error happend
						 */
						
						parent::draw($style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_new_member'), $users -> getError()));
						
						$this -> act_new_member();
						
					}
					
				}else{
					
					$this -> act_users();
					
				}
				
			break;
			
			case 'ranks':
				
				/**
				 * ranks manager
				 */
				
				$this -> act_ranks();
				
			break;
			
			case 'edit_rank':
				
				/**
				 * edit rank
				 */
				
				$this -> act_edit_rank();
				
			break;
			
			case 'change_rank':
				
				/**
				 * update rank
				 */
				
				if( $session -> checkForm()){
					
					if( isset( $_GET['rank'])){
					
						/**
						 * check if rank exists
						 */
						
						$rank_to_edit = $_GET['rank'];
						settype( $rank_to_edit, 'integer');
						
						$rank_edit_query = $mysql -> query( "SELECT * FROM ranks WHERE `rank_id` = '$rank_to_edit'");
						
						if ( $rank_result = mysql_fetch_array( $rank_edit_query, MYSQL_ASSOC)) {
							
							$rank_name = $strings -> inputClear( $_POST['rank_name'], false);
							$rank_posts = $_POST['rank_posts'];
							$rank_images = $_POST['rank_images'];
							$rank_image = $strings -> inputClear( $_POST['rank_image'], false);
							
							settype( $rank_posts, 'integer');
							settype( $rank_images, 'integer');
							
							if ( $rank_posts < 0)
								$rank_posts = 0;
							
							if ( $rank_images < 0)
								$rank_images = 0;
								
							if ( $rank_images > 10)
								$rank_images = 10;
								
							if ( strlen( $rank_name) == 0){
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_ranks_edit_rank'), $language -> getString( 'acp_users_subsection_ranks_new_rank_name_empty')));
								
								$this -> act_edit_rank();
								
							}else{
								
								/**
								 * mysql
								 */
								
								$new_rang_sql['rank_name'] = $rank_name;
								$new_rang_sql['rank_posts_required'] = $rank_posts;
								$new_rang_sql['rank_image'] = $rank_image;
								$new_rang_sql['rank_stars'] = $rank_images;
								
								$mysql -> update( $new_rang_sql, 'ranks', "`rank_id` = '$rank_to_edit'");
								
								$cache -> flushCache( 'users_ranks');
								
								/**
								 * add new log
								 */
								
								$log_keys = array( 'edit_rank_name' => $rank_name);
								$logs -> addAdminLog( $language -> getString( 'acp_users_subsection_ranks_edit_rank_log'), $log_keys);
								
								/**
								 * message
								 */
								
								parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_ranks_edit_rank'), $language -> getString( 'acp_users_subsection_ranks_edit_rank_done')));
								
								$this -> act_ranks();
								
							}
						}else{
										
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_ranks_edit_rank'), $language -> getString( 'acp_users_subsection_ranks_edit_rank_nofound')));
						
							$this -> act_ranks();
						
						}
						
					}else{
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_ranks_edit_rank'), $language -> getString( 'acp_users_subsection_ranks_edit_rank_notarget')));
						
						$this -> act_ranks();
						
					}
									
				}else{
					
					$this -> act_ranks();
					
				}
				
			break;
			
			case 'reps':
				
				/**
				 * reputations leveling
				 */
				
				$this -> act_reps();
				
			break;
			
			case 'reps_new':
				
				/**
				 * reputations leveling
				 */
				
				$this -> act_reps_new();
				
			break;
			
			case 'reps_add':
				
				/**
				 * add reputation
				 */
				
				if ( $session -> checkForm()){
					
					//get values
					
					$rep_name = $strings -> inputClear( $_POST['rep_name'], false);
					$rep_pos = $_POST['rep_pos'];
					
					settype( $rep_pos, 'integer');
					
					if ( strlen( $rep_name) == 0){
						
						//name must not be empty
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_reps_new_rep'), $language -> getString( 'acp_users_subsection_reps_name_empty')));
						$this -> act_reps_new();
						
					}else{
						
						$new_rep_sql['reputation_scale_name'] = $rep_name;
						$new_rep_sql['reputation_scale_points'] = $rep_pos;
						
						$mysql -> insert( $new_rep_sql, 'reputation_scale');
						
						$cache -> flushCache( 'users_reps');
						
						//add log
						
						$logs -> addAdminLog( $language -> getString( 'acp_users_subsection_reps_new_log'), array( 'rep_name' => $rep_name));
						
						//draw message
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_reps_new_rep'), $language -> getString( 'acp_users_subsection_reps_new_done')));
						
						//jump back to manager
						$this -> act_reps();
						
					}
					
				}else{
					
					//jump back to manager
					$this -> act_reps();
					
				}
				
			break;
			
			case 'reps_edit':
				
				/**
				 * reputations leveling
				 */
				
				$this -> act_reps_edit();
				
			break;
			
			case 'reps_change':
				
				/**
				 * add reputation
				 */
				
				if ( $session -> checkForm()){
					
					$rep_to_edit = $_GET['rep'];
					settype( $rep_to_edit, 'integer');
					
					$rep_query = $mysql -> query( "SELECT * FROM reputation_scale WHERE `reputation_scale_id` = '$rep_to_edit'");
					
					if ( $rep_result = mysql_fetch_array( $rep_query, MYSQL_ASSOC)){
					
						//get values
					
						$rep_name = $strings -> inputClear( $_POST['rep_name'], false);
						$rep_pos = $_POST['rep_pos'];
						
						settype( $rep_pos, 'integer');
						
						if ( strlen( $rep_name) == 0){
							
							//name must not be empty
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_reps_edit_rep'), $language -> getString( 'acp_users_subsection_reps_name_empty')));
							$this -> act_reps_new();
							
						}else{
							
							$new_rep_sql['reputation_scale_name'] = $rep_name;
							$new_rep_sql['reputation_scale_points'] = $rep_pos;
							
							$mysql -> update( $new_rep_sql, 'reputation_scale', "`reputation_scale_id` = '$rep_to_edit'");
							
							$cache -> flushCache( 'users_reps');
							
							//add log
							
							$logs -> addAdminLog( $language -> getString( 'acp_users_subsection_reps_edit_log'), array( 'rep_name' => $rep_name));
							
							//draw message
							
							parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_reps_edit_rep'), $language -> getString( 'acp_users_subsection_reps_edit_done')));
							
							//jump back to manager
							$this -> act_reps();
							
						}
					
					}else{
					
						//rep not found
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_reps_edit_rep'), $language -> getString( 'acp_users_subsection_reps_name_notfound')));	
						
						$this -> act_reps();
						
					}
						
				}else{
					
					//jump back to manager
					$this -> act_reps();
					
				}
				
			break;
			
			case 'reps_delete':
				
				/**
				 * delete reputation
				 */
					
				$rep_to_edit = $_GET['rep'];
				settype( $rep_to_edit, 'integer');
				
				$rep_query = $mysql -> query( "SELECT * FROM reputation_scale WHERE `reputation_scale_id` = '$rep_to_edit'");
				
				if ( $rep_result = mysql_fetch_array( $rep_query, MYSQL_ASSOC)){
					
					$mysql -> delete( 'reputation_scale', "`reputation_scale_id` = '$rep_to_edit'");
					
					$cache -> flushCache( 'users_reps');
					
					//add log
					
					$logs -> addAdminLog( $language -> getString( 'acp_users_subsection_reps_delete_log'), array( 'rep_id' => $rep_to_edit));
					
					//draw message
					
					parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_reps_del_rep'), $language -> getString( 'acp_users_subsection_reps_delete_done')));
					
					//jump back to manager
					$this -> act_reps();
											
				}else{
				
					//rep not found
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_reps_del_rep'), $language -> getString( 'acp_users_subsection_reps_name_notfound')));	
					
					$this -> act_reps();
					
				}
						
			break;
			
			case 'pfields':
				
				/**
				 * users fields manager
				 */

				$this -> act_pfields();
				
			break;
			
			case 'pfields_new':
				
				/**
				 * new pfield
				 */
				
				$this -> act_pfields_new();
				
			break;
			
			case 'pfields_add':
				
				/**
				 * add pfield
				 */
				
				if ( $session -> checkForm()){
					
					$pfield_name = ( $strings -> inputClear( $_POST['pfield_name'], false));
					$pfield_info = ( $strings -> inputClear( $_POST['pfield_info'], false));
					$pfield_type = $_POST['pfield_type'];
					$pfield_length = $_POST['pfield_length'];
					$pfield_options = ( $strings -> inputClear( $_POST['pfield_options'], false));
					$pfield_onregister = $_POST['pfield_onregister'];
					$pfield_onlist = $_POST['pfield_onlist'];
					$pfield_inposts = $_POST['pfield_inposts'];
					$pfield_require = $_POST['pfield_require'];
					$pfield_private = $_POST['pfield_private'];
					$pfield_byteam = $_POST['pfield_byteam'];
					$pfield_display = ( $strings -> inputClear( $_POST['pfield_display'], false));			
					
					settype( $pfield_type, 'integer');
					settype( $pfield_length, 'integer');
					
					if ( $pfield_type < 0)
						$pfield_type = 0;
					
					if ( $pfield_type > 2)
						$pfield_type = 2;
						
					if ( $pfield_length < 0)
						$pfield_length = 0;
						
					settype( $pfield_onregister, 'bool');
					settype( $pfield_onlist, 'bool');
					settype( $pfield_inposts, 'bool');
					settype( $pfield_require, 'bool');
					settype( $pfield_private, 'bool');
					settype( $pfield_byteam, 'bool');
					
					/**
					 * run error checks
					 */
					
					if ( strlen( $pfield_name) == 0){
						
						/**
						 * name is empty
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_pfield_new'), $language -> getString( 'acp_users_subsection_pfield_form_name_empty')));
						
						$this -> act_pfields_new();
						
					}else if( $pfield_type == 2 && strlen( $pfield_options) == 0){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_pfield_new'), $language -> getString( 'acp_users_subsection_pfield_form_options_empty')));
												
					}else if( strlen( $pfield_display) == 0){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_pfield_new'), $language -> getString( 'acp_users_subsection_pfield_form_display_empty')));
												
					}else{
						
						/**
						 * all ok, lets add field to mysql
						 */
						
						$pfield_pos = $mysql -> countRows( 'profile_fields') + 1;
						
						$new_field_sql['profile_field_pos'] = $pfield_pos;
						$new_field_sql['profile_field_name'] = $pfield_name;
						$new_field_sql['profile_field_info'] = $pfield_info;
						$new_field_sql['profile_field_type'] = $pfield_type;
						$new_field_sql['profile_field_length'] = $pfield_length;
						$new_field_sql['profile_field_options'] = $pfield_options;
						$new_field_sql['profile_field_onregister'] = $pfield_onregister;
						$new_field_sql['profile_field_onlist'] = $pfield_onlist;
						$new_field_sql['profile_field_inposts'] = $pfield_inposts;
						$new_field_sql['profile_field_require'] = $pfield_require;
						$new_field_sql['profile_field_private'] = $pfield_private;
						$new_field_sql['profile_field_byteam'] = $pfield_byteam;
						$new_field_sql['profile_field_display'] = $pfield_display;
						
						$mysql -> insert( $new_field_sql, 'profile_fields');
						
						$new_field_id = mysql_insert_id();
						
						$cache -> flushCache( 'profile_fields');
						
						/**
						 * add new field in fields data structure
						 */
						
						$mysql -> query( 'ALTER TABLE `profile_fields_data` ADD `field_'.$new_field_id.'` TEXT NULL ;');
						
						/**
						 * add log
						 */
						
						$logs -> addAdminLog( $language -> getString( 'acp_users_subsection_pfield_new_log'), array( 'field_name' => $pfield_name));
						
						/**
						 * draw message
						 */
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_pfield_new'), $language -> getString( 'acp_users_subsection_pfield_new_done')));
						
						/**
						 * jump to manager
						 */
						
						$this -> act_pfields();
						
					}
					
				}else{
					
					$this -> act_pfields();
				
				}
				
			break;
			
			case 'pfields_edit':
				
				/**
				 * edit pfield
				 */
				
				$this -> act_pfields_edit();
				
			break;
			
			case 'pfields_change':
				
				/**
				 * add pfield
				 */
				
				if ( $session -> checkForm()){
					
					/**
					 * select field
					 */
					
					$field_to_edit = $_GET['field'];
					
					settype( $field_to_edit, 'integer');
					
					$field_query = $mysql -> query( "SELECT * FROM `profile_fields` WHERE `profile_field_id` = '$field_to_edit'");
					
					if ( $field_result = mysql_fetch_array( $field_query, MYSQL_ASSOC)){
					
						$pfield_name = ( $strings -> inputClear( $_POST['pfield_name'], false));
						$pfield_info = ( $strings -> inputClear( $_POST['pfield_info'], false));
						$pfield_type = $_POST['pfield_type'];
						$pfield_length = $_POST['pfield_length'];
						$pfield_options = ( $strings -> inputClear( $_POST['pfield_options'], false));
						$pfield_onregister = $_POST['pfield_onregister'];
						$pfield_onlist = $_POST['pfield_onlist'];
						$pfield_inposts = $_POST['pfield_inposts'];
						$pfield_require = $_POST['pfield_require'];
						$pfield_private = $_POST['pfield_private'];
						$pfield_byteam = $_POST['pfield_byteam'];
						$pfield_display = ( $strings -> inputClear( $_POST['pfield_display'], false));			
						
						settype( $pfield_type, 'integer');
						settype( $pfield_length, 'integer');
						
						if ( $pfield_type < 0)
							$pfield_type = 0;
						
						if ( $pfield_type > 2)
							$pfield_type = 2;
							
						if ( $pfield_length < 0)
							$pfield_length = 0;
							
						settype( $pfield_onregister, 'bool');
						settype( $pfield_onlist, 'bool');
						settype( $pfield_inposts, 'bool');
						settype( $pfield_require, 'bool');
						settype( $pfield_private, 'bool');
						settype( $pfield_byteam, 'bool');
						
						/**
						 * run error checks
						 */
						
						if ( strlen( $pfield_name) == 0){
							
							/**
							 * name is empty
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_pfield_list_edit'), $language -> getString( 'acp_users_subsection_pfield_form_name_empty')));
							
							$this -> act_pfields_new();
							
						}else if( $pfield_type == 2 && strlen( $pfield_options) == 0){
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_pfield_list_edit'), $language -> getString( 'acp_users_subsection_pfield_form_options_empty')));
													
						}else if( strlen( $pfield_display) == 0){
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_pfield_list_edit'), $language -> getString( 'acp_users_subsection_pfield_form_display_empty')));
													
						}else{
							
							/**
							 * all ok, lets add field to mysql
							 */
							
							$pfield_pos = $mysql -> countRows( 'profile_fields') + 1;
							
							$new_field_sql['profile_field_pos'] = $pfield_pos;
							$new_field_sql['profile_field_name'] = $pfield_name;
							$new_field_sql['profile_field_info'] = $pfield_info;
							$new_field_sql['profile_field_type'] = $pfield_type;
							$new_field_sql['profile_field_length'] = $pfield_length;
							$new_field_sql['profile_field_options'] = $pfield_options;
							$new_field_sql['profile_field_onregister'] = $pfield_onregister;
							$new_field_sql['profile_field_onlist'] = $pfield_onlist;
							$new_field_sql['profile_field_inposts'] = $pfield_inposts;
							$new_field_sql['profile_field_require'] = $pfield_require;
							$new_field_sql['profile_field_private'] = $pfield_private;
							$new_field_sql['profile_field_byteam'] = $pfield_byteam;
							$new_field_sql['profile_field_display'] = $pfield_display;
							
							$mysql -> update( $new_field_sql, 'profile_fields', "`profile_field_id` = '$field_to_edit'");
							
							$cache -> flushCache( 'profile_fields');
												
							/**
							 * add log
							 */
							
							$logs -> addAdminLog( $language -> getString( 'acp_users_subsection_pfield_edit_log'), array( 'field_name' => $pfield_name));
							
							/**
							 * draw message
							 */
							
							parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_pfield_list_edit'), $language -> getString( 'acp_users_subsection_pfield_edit_done')));
							
							/**
							 * jump to manager
							 */
							
							$this -> act_pfields();
							
						}

					}else{
		
						/**
						 * not found
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_pfield_edit'), $language -> getString( 'acp_users_subsection_pfield_edit_notfound')));
						
						$this -> act_pfields();
					
					}

				}else{
					
					$this -> act_pfields();
				
				}
				
			break;
			
			case 'pfields_delete':
				
				/**
				 * delete pfield
				 */
				
				$this -> act_pfields_delete();
				
			break;
			
			case 'users_groups':

				/**
				 * users groups
				 */
				
				$this -> act_users_groups();
				
			break;
				
			case 'new_users_group':

				/**
				 * new users group
				 */
				
				$this -> act_new_users_group();
				
			break;
			
			case 'add_new_users_group':
				
				/**
				 * add new users group
				 */
				
				if ( $session -> checkForm()){
					
					/**
					 * get values
					 */
					
					$group_name = $strings -> inputClear( $_POST['group_name'], false);
					$group_perms = $_POST['group_perms'];
					$group_image = $strings -> inputClear( $_POST['group_image'], false);
					$group_message = $strings -> inputClear( $_POST['group_message'], false);
					$group_message_title = $strings -> inputClear( $_POST['group_message_title'], false);
					
					$group_title = $strings -> inputClear( $_POST['group_title'], false);
					$group_prefix = $strings -> inputClear( $_POST['group_prefix']);
					$group_suffix = $strings -> inputClear( $_POST['group_suffix']);
					$group_hidden = $_POST['group_hidden'];
					
					$group_can_see_closed = $_POST['group_can_see_closed'];
					$group_can_users_profiles = $_POST['group_can_users_profiles'];
					$group_can_use_pm = $_POST['group_can_use_pm'];
					$group_pm_limit = $_POST['group_pm_limit'];
					$group_can_mail = $_POST['group_can_mail'];
					$group_avoid_badwords = $_POST['group_avoid_badwords'];
					$group_can_see_hidden = $_POST['group_can_see_hidden'];
					$group_can_search = $_POST['group_can_search'];
					$group_search_limit = $_POST['group_search_limit'];
					
					$group_delete_own_topics = $_POST['group_delete_own_topics'];
					$group_change_own_topics = $_POST['group_change_own_topics'];
					$group_close_own_topics = $_POST['group_close_own_topics'];
					$group_delete_own_posts = $_POST['group_delete_own_posts'];
					$group_edit_own_posts = $_POST['group_edit_own_posts'];
					$group_edit_limit = $_POST['group_edit_limit'];
					$group_draw_edit_legend = $_POST['group_draw_edit_legend'];
					$group_avoid_flood = $_POST['group_avoid_flood'];
					$group_avoid_closed_topics = $_POST['group_avoid_closed_topics'];
					$group_start_surveys = $_POST['group_start_surveys'];
					$group_vote_surveys = $_POST['group_vote_surveys'];
					
					$group_uploads_quota = $_POST['group_uploads_quota'];
					$group_uploads_limit = $_POST['group_uploads_limit'];
					
					$group_can_use_acp = $_POST['group_can_use_acp'];
					$group_can_moderate = $_POST['group_can_moderate'];
					$group_can_edit_calendar = $_POST['group_can_edit_calendar'];
					$group_shoutbox_access = $_POST['group_shoutbox_access'];
					
					$group_promote_to = $_POST['group_promote_to'];
					$group_promote_at = $_POST['group_promote_at'];
					
					/**
					 * force types
					 */
					
					settype( $group_perms, 'integer');
					settype( $group_hidden, 'bool');
					
					settype( $group_can_see_closed, 'bool');
					settype( $group_can_users_profiles, 'bool');
					settype( $group_can_use_pm, 'bool');
					settype( $group_pm_limit, 'integer');
					settype( $group_can_mail, 'bool');
					settype( $group_avoid_badwords, 'bool');
					settype( $group_can_see_hidden, 'bool');
					settype( $group_can_search, 'bool');
					settype( $group_search_limit, 'integer');
					
					if ( $group_pm_limit < 1)
						$group_pm_limit = 1;
						
					if ( $group_search_limit < 0)
						$group_search_limit = 0;
					
					settype( $group_delete_own_topics, 'bool');
					settype( $group_change_own_topics, 'bool');
					settype( $group_close_own_topics, 'bool');
					settype( $group_delete_own_posts, 'bool');
					settype( $group_edit_own_posts, 'bool');
					settype( $group_edit_limit, 'integer');
					settype( $group_draw_edit_legend, 'bool');
					settype( $group_avoid_flood, 'bool');
					settype( $group_avoid_closed_topics, 'bool');
					settype( $group_start_surveys, 'bool');
					settype( $group_vote_surveys, 'bool');
					
					if ( $group_edit_limit < 0)
						$group_edit_limit = 0;
					
					settype( $group_uploads_quota, 'integer');
					settype( $group_uploads_limit, 'integer');
					
					if ( $group_uploads_quota < 0)
						$group_uploads_quota = 0;
						
					if ( $group_uploads_limit < 0)
						$group_uploads_limit = 0;
						
					settype( $group_can_use_acp, 'bool');
					settype( $group_can_moderate, 'bool');
					settype( $group_can_edit_calendar, 'bool');
					settype( $group_shoutbox_access, 'integer');
					
					if ( $group_shoutbox_access < 0)
						$group_shoutbox_access = 0;
						
					if ( $group_shoutbox_access > 3)
						$group_shoutbox_access = 3;
							
					settype( $group_promote_to, 'integer');
					settype( $group_promote_at, 'integer');
					
					if ( $group_promote_at < 0)
						$group_promote_at = 0;
						
					/**
					 * check if name is empty
					 */
						
					if ( strlen( $group_name) == 0){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_new_group'), $language -> getString( 'acp_users_subsection_users_groups_new_group_name_empty')));
						
						$this -> act_new_users_group();
						
					}else{
						
						/**
						 * do sql
						 */
						
						$new_group_sql['users_group_name'] = $group_name;
						$new_group_sql['users_group_prefix'] = $group_prefix;
						$new_group_sql['users_group_suffix'] = $group_suffix;
						$new_group_sql['users_group_title'] = $group_title;
						$new_group_sql['users_group_image'] = $group_image;
						$new_group_sql['users_group_message'] = $group_message;
						$new_group_sql['users_group_msg_title'] = $group_message_title;
						$new_group_sql['users_group_hidden'] = $group_hidden;
						$new_group_sql['users_group_permissions'] = $group_perms;
						$new_group_sql['users_group_system'] = false;
						$new_group_sql['users_group_can_use_acp'] = $group_can_use_acp;
						$new_group_sql['users_group_can_see_closed_page'] = $group_can_see_closed;
						$new_group_sql['users_group_can_see_users_profiles'] = $group_can_users_profiles;
						$new_group_sql['users_group_can_use_pm'] = $group_can_use_pm;
						$new_group_sql['users_group_pm_limit'] = $group_pm_limit;
						$new_group_sql['users_group_can_email_members'] = $group_can_mail;
						$new_group_sql['users_group_can_moderate'] = $group_can_moderate;
						$new_group_sql['users_group_can_edit_calendar'] = $group_can_edit_calendar;
						$new_group_sql['users_group_shoutbox_access'] = $group_shoutbox_access;
						$new_group_sql['users_group_edit_time_limit'] = $group_edit_limit;
						$new_group_sql['users_group_draw_edit_legend'] = $group_draw_edit_legend;
						$new_group_sql['users_group_delete_own_topics'] = $group_delete_own_topics;
						$new_group_sql['users_group_change_own_topics'] = $group_change_own_topics;
						$new_group_sql['users_group_close_own_topics'] = $group_close_own_topics;
						$new_group_sql['users_group_delete_own_posts'] = $group_delete_own_posts;
						$new_group_sql['users_group_edit_own_posts'] = $group_edit_own_posts;
						$new_group_sql['users_group_start_surveys'] = $group_start_surveys;
						$new_group_sql['users_group_vote_surveys'] = $group_vote_surveys;
						$new_group_sql['users_group_avoid_flood'] = $group_avoid_flood;
						$new_group_sql['users_group_avoid_badwords'] = $group_avoid_badwords;
						$new_group_sql['users_group_avoid_closed_topics'] = $group_avoid_closed_topics;
						$new_group_sql['users_group_promote_to'] = $group_promote_to;
						$new_group_sql['users_group_promote_at'] = $group_promote_at;
						$new_group_sql['users_group_see_hidden'] = $group_can_see_hidden;
						$new_group_sql['users_group_search'] = $group_can_search;
						$new_group_sql['users_group_search_limit'] = $group_search_limit;
						$new_group_sql['users_group_uploads_quota'] = $group_uploads_quota;
						$new_group_sql['users_group_uploads_limit'] = $group_uploads_limit;
						
						$mysql -> insert( $new_group_sql, 'users_groups');
						
						/**
						 * draw message
						 */
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_users_groups_new_group'), $language -> getString( 'acp_users_subsection_users_groups_new_group_done')));
						
						/**
						 * clear cache
						 */
						
						$cache -> flushCache( 'users_groups');
						
						/**
						 * add log
						 */
						
						$log_keys = array( 'new_users_group_name' => $group_name);
						$logs -> addAdminLog( $language -> getString( 'acp_users_subsection_users_groups_new_group_log'), $log_keys);
						
						/**
						 * jump to manager
						 */
						
						$this -> act_users_groups();
						
					}
					
				}else{
					
					$this -> act_new_users_group();
					
				}
								
			break;
			
			case 'edit_users_group':
				
				/**
				 * draw editor form
				 */
				
				$this -> act_edit_users_group();
				
			break;
			
			case 'change_users_group':
				
				/**
				 * update users group
				 */
				
				if ( $session -> checkForm()){
				
					if ( isset( $_GET['group']) && !empty( $_GET['group'])){
			
						/**
						 * get group to delete
						 */
						
						$group_to_edit = $_GET['group'];
						
						settype( $group_to_edit, 'integer');
						
						/**
						 * select group
						 */
						
						$group_query = $mysql -> query( "SELECT * FROM users_groups WHERE `users_group_id` = '$group_to_edit'");
						
						if ( $group_result = mysql_fetch_array( $group_query, MYSQL_ASSOC)){
						
							/**
							 * get values
							 */
							
							$group_name = $strings -> inputClear( $_POST['group_name'], false);
							$group_perms = $_POST['group_perms'];
							$group_image = $strings -> inputClear( $_POST['group_image'], false);
							$group_message = $strings -> inputClear( $_POST['group_message'], false);
							$group_message_title = $strings -> inputClear( $_POST['group_message_title'], false);
							
							$group_title = $strings -> inputClear( $_POST['group_title'], false);
							$group_prefix = $strings -> inputClear( $_POST['group_prefix']);
							$group_suffix = $strings -> inputClear( $_POST['group_suffix']);
							$group_hidden = $_POST['group_hidden'];
							
							$group_can_see_closed = $_POST['group_can_see_closed'];
							$group_can_users_profiles = $_POST['group_can_users_profiles'];
							$group_can_use_pm = $_POST['group_can_use_pm'];
							$group_pm_limit = $_POST['group_pm_limit'];
							$group_can_mail = $_POST['group_can_mail'];
							$group_avoid_badwords = $_POST['group_avoid_badwords'];
							$group_can_see_hidden = $_POST['group_can_see_hidden'];
							$group_can_search = $_POST['group_can_search'];
							$group_search_limit = $_POST['group_search_limit'];
							
							$group_delete_own_topics = $_POST['group_delete_own_topics'];
							$group_change_own_topics = $_POST['group_change_own_topics'];
							$group_close_own_topics = $_POST['group_close_own_topics'];
							$group_delete_own_posts = $_POST['group_delete_own_posts'];
							$group_edit_own_posts = $_POST['group_edit_own_posts'];
							$group_edit_limit = $_POST['group_edit_limit'];
							$group_draw_edit_legend = $_POST['group_draw_edit_legend'];
							$group_avoid_flood = $_POST['group_avoid_flood'];
							$group_avoid_closed_topics = $_POST['group_avoid_closed_topics'];
							$group_start_surveys = $_POST['group_start_surveys'];
							$group_vote_surveys = $_POST['group_vote_surveys'];
							
							$group_uploads_quota = $_POST['group_uploads_quota'];
							$group_uploads_limit = $_POST['group_uploads_limit'];
							
							$group_can_use_acp = $_POST['group_can_use_acp'];
							$group_can_moderate = $_POST['group_can_moderate'];
							$group_can_edit_calendar = $_POST['group_can_edit_calendar'];
							$group_shoutbox_access = $_POST['group_shoutbox_access'];
							
							$group_promote_to = $_POST['group_promote_to'];
							$group_promote_at = $_POST['group_promote_at'];
							
							/**
							 * force types
							 */
							
							settype( $group_perms, 'integer');
							settype( $group_hidden, 'bool');
							
							settype( $group_can_see_closed, 'bool');
							settype( $group_can_users_profiles, 'bool');
							settype( $group_can_use_pm, 'bool');
							settype( $group_pm_limit, 'integer');
							settype( $group_can_mail, 'bool');
							settype( $group_avoid_badwords, 'bool');
							settype( $group_can_see_hidden, 'bool');
							settype( $group_can_search, 'bool');
							settype( $group_search_limit, 'integer');
							
							if ( $group_pm_limit < 1)
								$group_pm_limit = 1;
								
							if ( $group_search_limit < 0)
								$group_search_limit = 0;
							
							settype( $group_delete_own_topics, 'bool');
							settype( $group_change_own_topics, 'bool');
							settype( $group_close_own_topics, 'bool');
							settype( $group_delete_own_posts, 'bool');
							settype( $group_edit_own_posts, 'bool');
							settype( $group_edit_limit, 'integer');
							settype( $group_draw_edit_legend, 'bool');
							settype( $group_avoid_flood, 'bool');
							settype( $group_avoid_closed_topics, 'bool');
							settype( $group_start_surveys, 'bool');
							settype( $group_vote_surveys, 'bool');
							
							if ( $group_edit_limit < 0)
								$group_edit_limit = 0;
							
							settype( $group_uploads_quota, 'integer');
							settype( $group_uploads_limit, 'integer');
							
							if ( $group_uploads_quota < 0)
								$group_uploads_quota = 0;
								
							if ( $group_uploads_limit < 0)
								$group_uploads_limit = 0;
								
							settype( $group_can_use_acp, 'bool');
							settype( $group_can_moderate, 'bool');
							settype( $group_can_edit_calendar, 'bool');
							settype( $group_shoutbox_access, 'integer');
			
							if ( $group_shoutbox_access < 0)
								$group_shoutbox_access = 0;
							
							if ( $group_shoutbox_access > 3)
								$group_shoutbox_access = 3;
								
							if ( $group_shoutbox_access > 1 && $group_to_edit == 2)
								$group_shoutbox_access = 1;
																
							settype( $group_promote_to, 'integer');
							settype( $group_promote_at, 'integer');
							
							if ( $group_promote_at < 0)
								$group_promote_at = 0;
								
							/**
							 * check if name is empty
							 */
								
							if ( strlen( $group_name) == 0){
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_edit_group'), $language -> getString( 'acp_users_subsection_users_groups_new_group_name_empty')));
								
								$this -> act_edit_users_group();
								
							}else{
								
								/**
								 * do sql
								 */
								
								$new_group_sql['users_group_name'] = $group_name;
								$new_group_sql['users_group_prefix'] = $group_prefix;
								$new_group_sql['users_group_suffix'] = $group_suffix;
								$new_group_sql['users_group_title'] = $group_title;
								$new_group_sql['users_group_image'] = $group_image;
								$new_group_sql['users_group_message'] = $group_message;
								$new_group_sql['users_group_msg_title'] = $group_message_title;
								$new_group_sql['users_group_hidden'] = $group_hidden;
								$new_group_sql['users_group_permissions'] = $group_perms;
								
								if ( $group_to_edit != 1)
									$new_group_sql['users_group_can_use_acp'] = $group_can_use_acp;
								
								$new_group_sql['users_group_can_see_closed_page'] = $group_can_see_closed;
								$new_group_sql['users_group_can_see_users_profiles'] = $group_can_users_profiles;
								$new_group_sql['users_group_can_use_pm'] = $group_can_use_pm;
								$new_group_sql['users_group_pm_limit'] = $group_pm_limit;
								$new_group_sql['users_group_can_email_members'] = $group_can_mail;
								
								if ( $group_to_edit != 1){
									$new_group_sql['users_group_can_moderate'] = $group_can_moderate;
								}
									
								$new_group_sql['users_group_can_edit_calendar'] = $group_can_edit_calendar;
								$new_group_sql['users_group_shoutbox_access'] = $group_shoutbox_access;
								$new_group_sql['users_group_edit_time_limit'] = $group_edit_limit;
								$new_group_sql['users_group_draw_edit_legend'] = $group_draw_edit_legend;
								$new_group_sql['users_group_delete_own_topics'] = $group_delete_own_topics;
								$new_group_sql['users_group_change_own_topics'] = $group_change_own_topics;
								$new_group_sql['users_group_close_own_topics'] = $group_close_own_topics;
								$new_group_sql['users_group_delete_own_posts'] = $group_delete_own_posts;
								$new_group_sql['users_group_edit_own_posts'] = $group_edit_own_posts;
								$new_group_sql['users_group_start_surveys'] = $group_start_surveys;
								$new_group_sql['users_group_vote_surveys'] = $group_vote_surveys;
								$new_group_sql['users_group_avoid_flood'] = $group_avoid_flood;
								$new_group_sql['users_group_avoid_badwords'] = $group_avoid_badwords;
								$new_group_sql['users_group_avoid_closed_topics'] = $group_avoid_closed_topics;
								
								if ( $group_to_edit != 1){
									$new_group_sql['users_group_promote_to'] = $group_promote_to;
									$new_group_sql['users_group_promote_at'] = $group_promote_at;
								}
								
								if ( $group_promote_to == 1 && $group_to_edit != 1)
									$new_group_sql['users_group_promote_to'] = 3;
								
								$new_group_sql['users_group_see_hidden'] = $group_can_see_hidden;
								$new_group_sql['users_group_search'] = $group_can_search;
								$new_group_sql['users_group_search_limit'] = $group_search_limit;
								$new_group_sql['users_group_uploads_quota'] = $group_uploads_quota;
								$new_group_sql['users_group_uploads_limit'] = $group_uploads_limit;
								
								$mysql -> update( $new_group_sql, 'users_groups', "`users_group_id` = '$group_to_edit'");
								
								/**
								 * draw message
								 */
								
								parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_users_groups_edit_group'), $language -> getString( 'acp_users_subsection_users_groups_edit_group_done')));
								
								/**
								 * clear cache
								 */
								
								$cache -> flushCache( 'moderators');
								$cache -> flushCache( 'forum_news');
								$cache -> flushCache( 'users_groups');
								
								/**
								 * add log
								 */
								
								$log_keys = array( 'users_group_edited' => $group_to_edit);
								$logs -> addAdminLog( $language -> getString( 'acp_users_subsection_users_groups_edit_group_log'), $log_keys);
								
								/**
								 * jump to manager
								 */
								
								$this -> act_users_groups();
								
							}
						
						}else{
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_edit_group'), $language -> getString( 'acp_users_subsection_users_groups_edit_group_nofound')));
							
							$this -> act_users_groups();
							
						}
						
					}else{
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_edit_group'), $language -> getString( 'acp_users_subsection_users_groups_edit_group_notarget')));
						
						$this -> act_users_groups();
						
					}	
										
				}else{
					
					$this -> act_users_groups();
					
				}
					
			break;
			
			case 'delete_users_group':
				
				/**
				 * delete group form
				 */
				
				$this -> act_delete_users_group();
				
			break;
			
			case 'kill_users_group':
				
				if ( $session -> checkForm()){
					
					/**
					 * error checking
					 */
					
					if ( isset( $_GET['group']) && !empty( $_GET['group'])){
						
						/**
						 * get group to delete
						 */
						
						$group_to_delete = $_GET['group'];
						
						settype( $group_to_delete, 'integer');
											
						/**
						 * select group
						 */
						
						$group_query = $mysql -> query( "SELECT * FROM users_groups WHERE `users_group_id` = '$group_to_delete'");
						
						if ( $group_result = mysql_fetch_array( $group_query, MYSQL_ASSOC)){
							
							//clear
							
							$group_result = $mysql -> clear( $group_result);
							
							if ( $group_result['users_group_system']){
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_delete_group'), $language -> getString( 'acp_users_subsection_users_groups_delete_group_system')));
								
								$this -> act_users_groups();
							
							}else{
								
								/**
								 * we can delete group
								 * get replacer
								 */
								
								$group_to_replace = $_POST['group_replace'];
								
								settype( $group_to_replace, 'integer');
								
								$group_query = $mysql -> query( "SELECT * FROM users_groups WHERE `users_group_id` = '$group_to_replace'");
								if ( $group_result = mysql_fetch_array( $group_query, MYSQL_ASSOC)){
									
									if ( $group_to_replace != $group_to_delete && $group_to_replace != 1){
										
										/**
										 * update users groups
										 */
										
										$update_users_sql['user_main_group'] = $group_to_replace;
										
										$mysql -> update( $update_users_sql, 'users', "`user_main_group` = '$group_to_delete'");
										
										/**
										 * delete existing one
										 */
										
										$mysql -> delete( 'users_groups', "`users_group_id` = '$group_to_delete'");
										$mysql -> delete( 'moderators', "`moderator_group_id` = '$group_to_delete'");
										
										/**
										 * clear cache
										 */
										
										$cache -> flushCache( 'users_groups');
										$cache -> flushCache( 'forum_news');
										$cache -> flushCache( 'moderators');
										
										/**
										 * set log
										 */
										
										$logs_keys = array( 'users_group_deleted', $group_to_delete);
										
										$logs -> addAdminLog( $language -> getString( 'acp_users_subsection_users_groups_delete_group_log'), $logs_keys);
										
										/**
										 * draw message
										 */
										
										parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_users_groups_delete_group'), $language -> getString( 'acp_users_subsection_users_groups_delete_group_done')));
										
										/**
										 * return to manager
										 */
										
										$this -> act_users_groups();
										
									}else{
										
										/**
										 * group is wrong
										 */
										
										parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_delete_group'), $language -> getString( 'acp_users_subsection_users_groups_delete_group_replace_notfound')));
								
										$this -> act_delete_users_group();
										
									}
									
								}else{
									
									/**
									 * group is wrong
									 */
									
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_delete_group'), $language -> getString( 'acp_users_subsection_users_groups_delete_group_replace_notfound')));
							
									$this -> act_delete_users_group();
																
								}
								
								
							}
							
						}else{
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_delete_group'), $language -> getString( 'acp_users_subsection_users_groups_delete_group_nofound')));
							
							$this -> act_users_groups();
							
						}
						
					}else{
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_delete_group'), $language -> getString( 'acp_users_subsection_users_groups_delete_group_notarget')));
						
						$this -> act_users_groups();
						
					}
				
				}else{
					
					$this -> act_users_groups();
					
				}
					
			break;
			
			case 'users_notactive':
			
				/**
				 * inactive users manager
				 */
				
				$this -> act_users_notactive();
				
			break;
			
			case 'users_banned':
			
				/**
				 * banned users manager
				 */
				
				$this -> act_users_banned();
				
			break;
			
			case 'bad_words':
				
				/**
				 * filter badwords
				 */
				
				$this -> act_bad_words();
				
			break;
			
			case 'autobanning':
				
				/**
				 * filter badwords
				 */
				
				$this -> act_autobanning();
				
			break;
		
		}
		
	}	
	
	function act_users(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'users');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_users'), parent::adminLink( parent::getId(), $path_link));		
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_users_subsection_users'));
		
		/**
		 * draw form
		 */
		
		$search_user_link = array( 'act' => 'find_user');
		
		$user_search_form = new form();
		$user_search_form -> openForm( parent::adminLink( parent::getId(), $search_user_link));
		$user_search_form -> drawSpacer( $language -> getString( 'acp_users_section_members_find_member_help'));
		$user_search_form -> openOpTable();
		
		$user_search_form -> drawTextInput( $language -> getString( 'acp_users_section_members_find_member_login'), 'user_login');
		$user_search_form -> drawTextInput( $language -> getString( 'acp_users_section_members_find_member_email'), 'user_mail');
		
		$user_search_form -> closeTable();
		$user_search_form -> drawButton( $language -> getString( 'acp_users_section_members_find_member_button'));
		$user_search_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_section_members_find_member'), $user_search_form -> display()));
				
	}
	
	function act_edit_user_profile(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * check if user is specified
		 */
		
		if ( isset( $_GET['user']) && !empty( $_GET['user'])){
			
			/**
			 * select user
			 */
			
			$user_to_edit = $_GET['user'];
			
			$user_query = $mysql -> query( "SELECT u.*, f.* 
			FROM users u
			LEFT JOIN profile_fields_data f
			ON u.user_id = f.profile_fields_user
			WHERE u.user_id = '$user_to_edit' AND u.user_id > 0");
			
			if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
				
				if ( !$users -> isRoot( $user_to_edit) || $session -> user['user_is_root']){
				
					/**
					 * add breadcrumbs
					 */
					
					$path_link = array( 'act' => 'users');
					
					$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
					$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_users'), parent::adminLink( parent::getId(), $path_link));		
					
					$path_link = array( 'act' => 'user_edit_profile', 'user' => $user_to_edit);
					
					$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members_edit_member_profile'), parent::adminLink( parent::getId(), $path_link));		
					
					/**
					 * set page title
					 */
					
					$output -> setTitle( $language -> getString( 'acp_users_section_members_edit_member_profile'));
					
					/**
					 * clear result
					 */
					
					$user_result = $mysql -> clear( $user_result);
					
					/**
					 * do we have additional fields?
					 */
							
					if ( $user_result['profile_fields_user'] != $user_to_edit)
						$mysql -> insert( array( "profile_fields_user" => $user_to_edit), 'profile_fields_data');
					
						
					/**
					 * set default vars
					 */
					
					$user_custom_title = $user_result['user_custom_title'];
					$user_posts_num = $user_result['user_posts_num'];
					$user_mail = $user_result['user_mail'];
					$user_showmail = $user_result['user_showmail'];
					$user_want_mail = $user_result['user_want_mail'];
					$user_main_group = $user_result['user_main_group'];
					$user_other_groups = $user_result['user_other_groups'];
					$user_permissions = $user_result['user_permissions'];
					$user_notify_pm = $user_result['user_notify_pm'];
					$user_time_zone = $user_result['user_time_zone'];
					$user_dst = $user_result['user_dst'];
					$user_show_avatars = $user_result['user_show_avatars'];
					$user_show_sigs = $user_result['user_show_sigs'];
					$user_style = $user_result['user_style'];
					$user_lang = $user_result['user_lang'];
					$user_name = $user_result['user_name'];
					$user_birth_date = $user_result['user_birth_date'];
					$user_gender = $user_result['user_gender'];
					$user_jabber_id = $user_result['user_jabber_id'];
					$user_web = $user_result['user_web'];
					$user_localisation = $user_result['user_localisation'];
					$user_interests = $user_result['user_interests'];
					
					/**
					 * check if repeating
					 */
					
					if ( $_GET['act'] == 'user_update_profile'){
						
						/**
						 * get data
						 */
					
						$user_custom_title = stripslashes( $strings -> inputClear( $_POST['user_custom_title'], false));
						$user_posts_num = $_POST['user_posts_num'];
						$user_mail = stripslashes( $strings -> inputClear( $_POST['user_mail'], false));
						$user_showmail = $_POST['user_showmail'];
						$user_want_mail = $_POST['user_want_mail'];
						$user_main_group = $_POST['user_main_group'];
						$user_other_groups = $_POST['user_other_groups'];
						$user_permissions = $_POST['user_permissions'];
						$user_notify_pm = $_POST['user_notify_pm'];
						$user_time_zone = $_POST['user_time_zone'];
						$user_dst = $_POST['user_dst'];
						$user_show_avatars = $_POST['user_show_avatars'];
						$user_show_sigs = $_POST['user_show_sigs'];
						$user_style = $_POST['user_style'];
						$user_lang = $strings -> inputClear( $_POST['user_lang'], false);
						$user_name = $strings -> inputClear( $_POST['user_name'], false);
						$user_gender = $_POST['user_gender'];
						$user_jabber_id = stripslashes( $strings -> inputClear( $_POST['user_jabber_id'], false));
						$user_web = stripslashes( $strings -> inputClear( $_POST['user_web'], false));
						$user_localisation = stripslashes( $strings -> inputClear( $_POST['user_localisation'], false));
						$user_interests = stripslashes( $strings -> inputClear( $_POST['user_interests'], false));
						
						$user_birth_day = $_POST['user_birth_day'];
						$user_birth_month = $_POST['user_birth_month'];
						$user_birth_year = $_POST['user_birth_year'];
						
						settype( $user_birth_day, 'integer');
						settype( $user_birth_month, 'integer');
						settype( $user_birth_year, 'integer');
						
						if ( $user_birth_day < 1)
							$user_birth_day = 1;
						
						if ( $user_birth_month < 1)
							$user_birth_month = 1;
										
						if ( $user_birth_year < 1890)
							$user_birth_year = 1890;
							
						if ( !empty( $_POST['user_birth_day']) && !empty( $_POST['user_birth_month']) && !empty( $_POST['user_birth_year'])){
							$user_birth_date = $user_birth_day.'-'.$user_birth_month.'-'.$user_birth_year;
						}else{
							$user_birth_date = '';
						}
						
						/**
						 * force types
						 */
						
						settype( $user_posts_num, 'integer');
						settype( $user_showmail, 'bool');
						settype( $user_want_mail, 'bool');
						
						settype( $user_main_group, 'integer');
						settype( $user_permissions, 'integer');
						
						settype( $user_notify_pm, 'bool');
						
						settype( $user_time_zone, 'float');
						
						settype( $user_dst, 'bool');
						settype( $user_show_avatars, 'bool');
						settype( $user_show_sigs, 'bool');
						
						settype( $user_style, 'integer');
						settype( $user_gender, 'integer');
						
						/**
						 * do checking
						 */
						
						if ( $user_posts_num < 0)
							$user_posts_num = 0;
						
						if ( $user_time_zone < -12)
							$user_time_zone = -12;
						
						if ( $user_time_zone > 14)
							$user_time_zone = 14;					
						
					}
					
					/**
					 * begin drawing form
					 */
				
					$edit_user_link = array( 'act' => 'user_update_profile', 'user' => $user_to_edit);
					
					$user_edit_form = new form();
					$user_edit_form -> openForm( parent::adminLink( parent::getId(), $edit_user_link));
					$user_edit_form -> openOpTable();
					
					/**
					 * main section
					 */
					
					$user_edit_form -> drawTextInput( $language -> getString( 'acp_users_section_members_edit_member_profile_custom_title'), 'user_custom_title', $user_custom_title);
					$user_edit_form -> drawTextInput( $language -> getString( 'acp_users_section_members_edit_member_profile_posts_num'), 'user_posts_num', $user_posts_num);
					
					$user_edit_form -> drawTextInput( $language -> getString( 'acp_users_section_members_edit_member_profile_email'), 'user_mail', $user_mail);
					$user_edit_form -> drawYesNo( $language -> getString( 'acp_users_section_members_edit_member_profile_show_mail'), 'user_showmail', $user_showmail);
					$user_edit_form -> drawYesNo( $language -> getString( 'acp_users_section_members_edit_member_profile_receive_mail'), 'user_want_mail', $user_want_mail);
					
					$user_edit_form -> closeTable();
					$user_edit_form -> drawSpacer( $language -> getString( 'acp_users_section_members_edit_member_profile_groups'));
					$user_edit_form -> openOpTable();
					
					/**
					 * select groups
					 */
					
					$groups_query = $mysql -> query( "SELECT * FROM users_groups users_group_can_use_acp ORDER BY users_group_can_use_acp DESC, users_group_can_moderate DESC, users_group_name");
					
					while ( $users_groups_result = mysql_fetch_array( $groups_query, MYSQL_ASSOC)){
						
						$users_groups_result = $mysql -> clear( $users_groups_result);
						
						/**
						 * check group
						 */
						
						if ( $users_groups_result['users_group_id'] != 1 || $session -> user['user_is_root'])
							$users_groups_list[$users_groups_result['users_group_id']] = $users_groups_result['users_group_name'];
						
					}				
					
					$user_edit_form -> drawList( $language -> getString( 'acp_users_section_members_edit_member_profile_main_group'), 'user_main_group', $users_groups_list, $user_main_group);
					$user_edit_form -> drawMultiList( $language -> getString( 'acp_users_section_members_edit_member_profile_other_groups'), 'user_other_groups[]', $users_groups_list, split( ",", $user_other_groups));
					
					/**
					 * and masks
					 */
					
					$masks_query = $mysql -> query( "SELECT * FROM users_perms ORDER BY users_perm_name");
					
					$masks_list[0] = $language -> getString( 'acp_users_section_members_edit_member_profile_permissions_none');
					
					while ( $masks_result = mysql_fetch_array( $masks_query, MYSQL_ASSOC)){
						
						$masks_result = $mysql -> clear( $masks_result);
												
						$masks_list[$masks_result['users_perm_id']] = $masks_result['users_perm_name'];
						
					}
					
					$user_edit_form -> drawList( $language -> getString( 'acp_users_section_members_edit_member_profile_permissions'), 'user_permissions', $masks_list, $user_permissions);
					
					$user_edit_form -> closeTable();
					$user_edit_form -> drawSpacer( $language -> getString( 'acp_users_section_members_edit_member_profile_settings'));
					$user_edit_form -> openOpTable();
					
					$user_edit_form -> drawYesNo( $language -> getString( 'acp_users_section_members_edit_member_profile_notify_pm'), 'user_notify_pm', $user_notify_pm);
					
					$user_edit_form -> drawList( $language -> getString( 'acp_users_section_members_edit_member_profile_time_zone'), 'user_time_zone', $time -> getTimeZones(), $user_time_zone);
					$user_edit_form -> drawYesNo( $language -> getString( 'acp_users_section_members_edit_member_profile_dst'), 'user_dst', $user_dst);
					
					$user_edit_form -> drawYesNo( $language -> getString( 'acp_users_section_members_edit_member_profile_show_avatars'), 'user_show_avatars', $user_show_avatars);
					$user_edit_form -> drawYesNo( $language -> getString( 'acp_users_section_members_edit_member_profile_show_sigs'), 'user_show_sigs', $user_show_sigs);
					
					/**
					 * styles select list
					 */
					
					$styles_query = $mysql -> query( "SELECT * FROM styles");
					
					while ( $styles_result = mysql_fetch_array( $styles_query, MYSQL_ASSOC)){
						
						$styles_result = $mysql -> clear( $styles_result);
						
						$styles_list[$styles_result['style_id']] = $styles_result['style_name'];
						
					}
					
					$user_edit_form -> drawList( $language -> getString( 'acp_users_section_members_edit_member_profile_style'), 'user_style', $styles_list, $user_style);
					
					/**
					 * language select list
					 */
					
					$langs_query = $mysql -> query( "SELECT * FROM languages");
					
					while ( $langs_result = mysql_fetch_array( $langs_query, MYSQL_ASSOC)){
						
						$langs_result = $mysql -> clear( $langs_result);
						
						$langs_list[$langs_result['lang_id']] = $langs_result['lang_name'];
						
					}
					
					$user_edit_form -> drawList( $language -> getString( 'acp_users_section_members_edit_member_profile_lang'), 'user_lang', $langs_list, $user_lang);
									
					$user_edit_form -> closeTable();
					$user_edit_form -> drawSpacer( $language -> getString( 'acp_users_section_members_edit_member_profile_information'));
					$user_edit_form -> openOpTable();
					
					$user_edit_form -> drawTextInput( $language -> getString( 'acp_users_section_members_edit_member_profile_name'), 'user_name', $user_name);
					
					$birth_date = split("-", $user_birth_date);
		
					$user_edit_form -> drawInfoRow( $language -> getString( 'acp_users_section_members_edit_member_profile_birth_date'), '<input name="user_birth_day" type="text" size="2" maxlength="2" value="'.$birth_date[0].'"/> - <input name="user_birth_month" type="text" size="2" maxlength="2" value="'.$birth_date[1].'"/> - <input name="user_birth_year" type="text" size="4" maxlength="4" value="'.$birth_date[2].'"/>');
					
					$genders[0] = $language -> getString( 'gender_0');
					$genders[1] = $language -> getString( 'gender_1');
					$genders[2] = $language -> getString( 'gender_2');
					
					$user_edit_form -> drawList( $language -> getString( 'acp_users_section_members_edit_member_profile_gender'), 'user_gender', $genders, $user_gender);
					
					$user_edit_form -> drawTextInput( $language -> getString( 'acp_users_section_members_edit_member_profile_jabber'), 'user_jabber_id', $user_jabber_id);
					$user_edit_form -> drawTextInput( $language -> getString( 'acp_users_section_members_edit_member_profile_web'), 'user_web', $user_web);
					$user_edit_form -> drawTextInput( $language -> getString( 'acp_users_section_members_edit_member_profile_localisation'), 'user_localisation', $user_localisation);
					$user_edit_form -> drawTextBox( $language -> getString( 'acp_users_section_members_edit_member_profile_interests'), 'user_interests', $user_interests);
					
					/**
					 * custom fields
					 */
					
					if ( count( $users -> custom_fields) > 0){
			
						$user_edit_form -> closeTable();
						$user_edit_form -> drawSpacer( $language -> getString( 'user_cp_section_personal_change_profile_other'));
						$user_edit_form -> openOpTable();
						
						foreach ( $users -> custom_fields as $field_id => $field_ops){
							
							/**
							 * profile options list
							 */
							
							if ( $field_ops['profile_field_type'] == 2){
								
								$preparsed_options = split( "\n", $field_ops['profile_field_options']);
								
								$made_options = array();
								
								foreach ( $preparsed_options as $preparsed_option) {
									
									$option_id = substr( $preparsed_option, 0, strpos( $preparsed_option, "="));
									$option_value = substr( $preparsed_option, strpos( $preparsed_option, "=") + 1);
									
									$made_options[$option_id] = $option_value;
									
								}
								
							}
														
							switch ( $field_ops['profile_field_type']){
							
								case 0:
							
									$limit = '';
									$limit_msg = '';
									
									if ( $field_ops['profile_field_length'] > 0){
										
										$limit = ' maxlength="'.$field_ops['profile_field_length'].'" ';
										
										$language -> setKey( 'max_lenght', $field_ops['profile_field_length']);
										
										$limit_msg = $language -> getString( 'user_cp_length_info');
										
										if ( strlen( $field_ops['profile_field_info']) > 0)
											$limit_msg = '<br />'.$limit_msg;
										
									}
									
									$user_edit_form -> drawInfoRow( $field_ops['profile_field_name'], '<input name="field_'.$field_id.'" type="text" size="50" value="'.$user_result['field_'.$field_id].'" '.$limit.'/>', $field_ops['profile_field_info'].$limit_msg);
							
								break;
							
								case 1:
							
									$limit_msg = '';
									
									if ( $field_ops['profile_field_length'] > 0){
										
										$language -> setKey( 'max_lenght', $field_ops['profile_field_length']);
										
										$limit_msg = $language -> getString( 'user_cp_length_info');
										
										if ( strlen( $field_ops['profile_field_info']) > 0)
											$limit_msg = '<br />'.$limit_msg;								
											
									}
									
									$user_edit_form -> drawTextBox( $field_ops['profile_field_name'], 'field_'.$field_id, $user_result['field_'.$field_id], $field_ops['profile_field_info'].$limit_msg);
									
								break;
														
								case 2:
														
									$user_edit_form -> drawList( $field_ops['profile_field_name'], 'field_'.$field_id, $made_options, $user_result['field_'.$field_id], $field_ops['profile_field_info']);
							
								break;
							}
							
						}
											
					}
					
					$user_edit_form -> closeTable();
					$user_edit_form -> drawButton( $language -> getString( 'acp_users_section_members_edit_member_profile_button'));
					$user_edit_form -> closeForm();
					
					/**
					 * display it
					 */
					
					parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $user_edit_form -> display()));
					
				}else{
							
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'acp_users_section_members_find_member_cant_root')));
					
					$this -> act_find_user();
				}
				
			}else{
				
				/**
				 * user not found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'acp_users_section_members_find_member_notfound')));
			
				$this -> act_users();
				
			}
			
		}else{
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_edit_member_profile'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_error')));
			
			$this -> act_users();
			
		}
	}
	
	function act_user_edit_sig(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * check if user is specified
		 */
		
		if ( isset( $_GET['user']) && !empty( $_GET['user'])){
			
			/**
			 * select user
			 */
			
			$user_to_edit = $_GET['user'];
			
			$user_query = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '$user_to_edit' AND `user_id` > 0");
			
			if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
				
				if ( !$users -> isRoot( $user_to_edit) || $session -> user['user_is_root']){
				
					/**
					 * add breadcrumbs
					 */
					
					$path_link = array( 'act' => 'users');
					
					$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
					$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_users'), parent::adminLink( parent::getId(), $path_link));		
					
					$path_link = array( 'act' => 'user_edit_sig', 'user' => $user_to_edit);
					
					$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members_find_member_act_edit_sig'), parent::adminLink( parent::getId(), $path_link));		
					
					/**
					 * set page title
					 */
					
					$output -> setTitle( $language -> getString( 'acp_users_section_members_find_member_act_edit_sig'));
					
					/**
					 * clear result
					 */
					
					$user_result = $mysql -> clear( $user_result);
	
					$change_signature_link = array( 'act' => 'user_change_sig', 'user' => $user_to_edit);
			
					$signature_edit_form = new form();
					$signature_edit_form -> openForm( parent::adminLink( parent::getId(), $change_signature_link));
					$signature_edit_form -> hiddenValue( 'update_signature', true);
					$signature_edit_form -> openOpTable();
					
					$lenght_info = '';
					
					if ( $settings['user_sig_max_length'] != 0){
						
						$language -> setKey( 'max_lenght', $settings['user_sig_max_length']);
						
						$lenght_info = $language -> getString( 'user_cp_length_info');
						
					}
						
					$signature = $user_result['user_signature'];
					
					$language -> setKey( 'user_edited_sig', $user_result['user_login']);
					
					$signature_edit_form -> drawEditor( $language -> getString( 'acp_users_section_members_find_member_act_edit_sig_member'), 'user_signature', $signature, $lenght_info, $settings['users_allow_bbcodes_in_sigs'], $settings['users_allow_emoticones_in_sigs']);
					
					$signature_edit_form -> closeTable();
					$signature_edit_form -> drawButton( $language -> getString( 'user_cp_change_button'));
					$signature_edit_form -> closeForm();
					
					parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_section_members_find_member_act_edit_sig'), $signature_edit_form -> display()));

				}else{
							
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member_act_edit_sig'), $language -> getString( 'acp_users_section_members_find_member_cant_root')));
					
					$this -> act_find_user();
				
				}
								
			}else{
				
				/**
				 * user not found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member_act_edit_sig'), $language -> getString( 'acp_users_section_members_find_member_notfound')));
			
				$this -> act_users();
				
			}
			
		}else{
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member_act_edit_sig'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_error')));
			
			$this -> act_users();
			
		}
		
	}
	
	function act_user_edit_login(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * check if user is specified
		 */
		
		if ( isset( $_GET['user']) && !empty( $_GET['user'])){
			
			/**
			 * select user
			 */
			
			$user_to_edit = $_GET['user'];
			
			$user_query = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '$user_to_edit' AND `user_id` > 0");
			
			if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
				
				if ( !$users -> isRoot( $user_to_edit) || $session -> user['user_is_root']){
			
					/**
					 * add breadcrumbs
					 */
					
					$path_link = array( 'act' => 'users');
					
					$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
					$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_users'), parent::adminLink( parent::getId(), $path_link));		
					
					$path_link = array( 'act' => 'user_edit_login', 'user' => $user_to_edit);
					
					$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members_login_change'), parent::adminLink( parent::getId(), $path_link));		
					
					/**
					 * set page title
					 */
					
					$output -> setTitle( $language -> getString( 'acp_users_section_members_login_change'));
					
					/**
					 * clear result
					 */
					
					$user_result = $mysql -> clear( $user_result);
					
					/**
					 * draw form
					 */
	
					$user_login = $user_result['user_login'];
					
					if ( $_GET['act'] == 'user_change_login')
						$user_login = stripslashes(htmlspecialchars(trim($_POST['user_login'])));
					
					$update_user_link = array( 'act' => 'user_change_login', 'user' => $user_to_edit);
					
					$user_login_change_form = new form();
					$user_login_change_form -> openForm( parent::adminLink( parent::getId(), $update_user_link));
					$user_login_change_form -> openOpTable();
					
					$user_login_change_form -> drawTextInput( $language -> getString( 'acp_users_section_members_login_change_user_login'), 'user_login', $user_login);
					
					$user_login_change_form -> closeTable();
					$user_login_change_form -> drawButton( $language -> getString( 'acp_users_section_members_login_change_but'));
					$user_login_change_form -> closeForm();
					
					parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_section_members_login_change'), $user_login_change_form -> display()));
					
				}else{
							
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_login_change'), $language -> getString( 'acp_users_section_members_find_member_cant_root')));
					
					$this -> act_find_user();
				
				}
		}else{
				
				/**
				 * user not found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_login_change'), $language -> getString( 'acp_users_section_members_find_member_notfound')));
			
				$this -> act_users();
				
			}
			
		}else{
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_login_change'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_error')));
			
			$this -> act_users();
			
		}
	}
	
	function act_user_edit_pass(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * check if user is specified
		 */
		
		if ( isset( $_GET['user']) && !empty( $_GET['user'])){
			
			/**
			 * select user
			 */
			
			$user_to_edit = $_GET['user'];
			
			$user_query = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '$user_to_edit' AND `user_id` > 0");
			
			if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
				
				if ( !$users -> isRoot( $user_to_edit) || $session -> user['user_is_root']){
					
					/**
					 * add breadcrumbs
					 */
					
					$path_link = array( 'act' => 'users');
					
					$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
					$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_users'), parent::adminLink( parent::getId(), $path_link));		
					
					$path_link = array( 'act' => 'user_edit_pass', 'user' => $user_to_edit);
					
					$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members_pass_change'), parent::adminLink( parent::getId(), $path_link));		
					
					/**
					 * set page title
					 */
					
					$output -> setTitle( $language -> getString( 'acp_users_section_members_pass_change'));
					
					/**
					 * draw form
					 */
					
					$update_user_link = array( 'act' => 'user_change_pass', 'user' => $user_to_edit);
					
					$user_login_change_form = new form();
					$user_login_change_form -> openForm( parent::adminLink( parent::getId(), $update_user_link));
					$user_login_change_form -> openOpTable();
					
					$user_login_change_form -> drawPassInput( $language -> getString( 'acp_users_section_members_pass_change_user_pass'), 'user_pass');
					
					$user_login_change_form -> closeTable();
					$user_login_change_form -> drawButton( $language -> getString( 'acp_users_section_members_pass_change_but'));
					$user_login_change_form -> closeForm();
					
					parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_section_members_pass_change'), $user_login_change_form -> display()));
				
				}else{
							
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_pass_change'), $language -> getString( 'acp_users_section_members_find_member_cant_root')));
					
					$this -> act_find_user();
				
				}
					
		}else{
				
				/**
				 * user not found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_pass_change'), $language -> getString( 'acp_users_section_members_find_member_notfound')));
			
				$this -> act_users();
				
			}
			
		}else{
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_pass_change'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_error')));
			
			$this -> act_users();
			
		}
	}
	
	function act_delete_user(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * check if user is specified
		 */
		
		if ( isset( $_GET['user']) && !empty( $_GET['user'])){
			
			/**
			 * select user
			 */
			
			$user_to_edit = $_GET['user'];
			
			$user_query = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '$user_to_edit' AND `user_id` > 0");
			
			if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
				
				if ( !$users -> isRoot( $user_to_edit) || $session -> user['user_is_root']){
							
					/**
					 * user delete form
					 */
					
					$delete_user_link = array( 'act' => 'user_kill', 'user' => $user_to_edit);
					
					$user_delete_form = new form();
					$user_delete_form -> openForm( parent::adminLink( parent::getId(), $delete_user_link));
					$user_delete_form -> openOpTable();
					
					$user_delete_form -> drawRow( $language -> getString( 'acp_users_section_members_delete_ask').'<br /><br />'.$user_delete_form -> drawSelect( 'delete_user_accept').' <b>'.$language -> getString( 'acp_users_section_members_delete_select').'</b>');
					
					$user_delete_form -> closeTable();
					$user_delete_form -> drawButton( $language -> getString( 'acp_users_section_members_delete_but'));
					$user_delete_form -> closeForm();
					
					parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_section_members_delete'), $user_delete_form -> display()));
				
				}else{
							
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_delete'), $language -> getString( 'acp_users_section_members_find_member_cant_root')));
					
					$this -> act_find_user();
				
				}
				
			}else{
				
				/**
				 * user not found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_delete'), $language -> getString( 'acp_users_section_members_find_member_notfound')));
			
				$this -> act_users();
				
			}
			
		}else{
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_delete'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_error')));
			
			$this -> act_users();
			
		}
			
	}
	
	function act_find_user(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * check if any conditions was send
		 */
		
		$user_login = $strings -> inputClear( $_POST['user_login']);
		
		if ( isset( $_GET['user']) && !empty( $_GET['user'])){
		
			$user_to_show = $_GET['user'];
			settype( $user_to_show, 'integer');
			
		}
			
		$user_mail = $strings -> inputClear( $_POST['user_mail']);
		
		settype( $prev_search_id, 'integer');
		
		if ( !empty( $user_login) || !empty( $user_mail) || !empty( $prev_search_id) || !empty( $user_to_show)){
			
			if ( !empty( $user_login))
				$search_conditions[] = " u.user_login LIKE '%$user_login%'";
			
			if ( !empty( $user_mail))
				$search_conditions[] = "u.user_mail LIKE '%$user_mail%'";
				
			/**
			 * do user acts
			 */
				
			$proper_does = array( 'kill_avatar', 'unban_user', 'ban_user');
			
			if ( isset( $_GET['do']) && in_array( $_GET['do'], $proper_does)){
				
				$act_to_do = $_GET['do'];
				
				switch ( $act_to_do){
				
					case 'kill_avatar':

						$user_to_kill_avatar = $_GET['user'];
						
						settype( $user_to_kill_avatar, 'integer');
						
						if ( $user_to_kill_avatar > 0){
							
							if ( !$users -> isRoot( $user_to_kill_avatar) || $session -> user['user_is_root']){
							
								$users -> killAvatar( $user_to_kill_avatar);
								
								$log_keys = array( 'user_remove_avatar' => $user_to_kill_avatar);
								
								$logs -> addModLog( $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_log'), $log_keys, 0, 0, 0, $user_to_kill_avatar);
								
								parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_section_members_find_member_act_delete_av'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_done')));
							
							}else{
							
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member_act_delete_av'), $language -> getString( 'acp_users_section_members_find_member_cant_root')));
								
							}
							
						}else{
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member_act_delete_av'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_error')));
														
						}
						
					break;
					
					case 'unban_user':
					
						$user_to_ban = $_GET['user'];
						
						settype( $user_to_ban, 'integer');
						
						if ( $user_to_ban > 0){
							
							if ( !$users -> isRoot( $user_to_ban) || $session -> user['user_is_root']){
								
								$ban_user_mysql['user_locked'] = false;
								
								$mysql -> update( $ban_user_mysql, 'users', "`user_id` = '$user_to_ban'");
														
								$log_keys = array( 'user_ban_id' => $user_to_ban);
								
								$logs -> addModLog( $language -> getString( 'acp_users_section_members_find_member_do_unban_log'), $log_keys, 0, 0, 0, $user_to_ban);
								
								parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_section_members_find_member_act_unban_acc'), $language -> getString( 'acp_users_section_members_find_member_do_unban_done')));
								
							}else{
							
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member_act_unban_acc'), $language -> getString( 'acp_users_section_members_find_member_cant_root')));
								
							}
								
						}else{
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member_act_unban_acc'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_error')));
														
						}
						
					break;
					
					case 'ban_user':
					
						$user_to_ban = $_GET['user'];
						
						settype( $user_to_ban, 'integer');
						
						if ( $user_to_ban > 0){
							
							if ( !$users -> isRoot( $user_to_ban) || $session -> user['user_is_root']){
								
								$ban_user_mysql['user_locked'] = true;
								
								$mysql -> update( $ban_user_mysql, 'users', "`user_id` = '$user_to_ban'");
	
								/**
								 * kill session
								 */
											
								$mysql -> delete( 'users_sessions', "`users_session_user_id` = '$user_to_ban'");
											
								/**
								 * do rest
								 */
												
								$log_keys = array( 'user_ban_id' => $user_to_ban);
								
								$logs -> addModLog( $language -> getString( 'acp_users_section_members_find_member_act_ban_acc'), $log_keys, 0, 0, 0, $user_to_ban);
								
								parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_section_members_find_member_act_ban_acc'), $language -> getString( 'acp_users_section_members_find_member_do_ban_done')));
								
							}else{
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member_act_ban_acc'), $language -> getString( 'acp_users_section_members_find_member_cant_root')));
															
							}
							
						}else{
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member_act_ban_acc'), $language -> getString( 'acp_users_section_members_find_member_do_delete_avatar_error')));
														
						}
						
					break;
					
				}				
				
			}
				
			/**
			 * select results
			 */
			
			if ( isset( $user_to_show)){
				$user_search_query = $mysql -> query( "SELECT u.*, g.* FROM users u LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE u.user_id = '$user_to_show'");
			}else{
				$user_search_query = $mysql -> query( "SELECT u.*, g.* FROM users u LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE ".join( ' AND ', $search_conditions)." AND u.user_id <> -1");
			}
			
			$column = 0;
			$search_results_num = 0;
					
			while( $user_result = mysql_fetch_array( $user_search_query, MYSQL_ASSOC)){
								
				if ( $column == 2){
					
					$column = 0;
					$found_users_tab .= '</tr><tr>';
				}
				
				$user_result = $mysql -> clear( $user_result);
				
				$results_ids[] = $user_result['user_id'];
				
				$found_users_tab .= '<td class="opt_row1" style="vertical-align: top">';
				
				/**
				 * begin drawing subform with result
				 */
				
				$user_result_block = new form();
				$user_result_block -> openOpTable();
				
				$avatar_div = '';
				
				if ( $user_result['user_avatar_type'] != 0){
					
					$avatar_div = '<div style="float: left; margin-right: 3px;">'.$users -> drawAvatar( $user_result['user_avatar_type'], $user_result['user_avatar_image'], $user_result['user_avatar_width'], $user_result['user_avatar_height']).'</div>';
					
				}
				
				$user_profile_fields = array();
				
				$user_profile_fields[] = '<b>'.$language -> getString( 'user_group').':</b> '.$user_result['users_group_prefix'].$user_result['users_group_name'].$user_result['users_group_suffix'];
				$user_profile_fields[] = '<b>'.$language -> getString( 'user_mail').':</b> <a href="mailto:'.$user_result['user_mail'].'">'.$user_result['user_mail'].'</a>';
				$user_profile_fields[] = '<b>'.$language -> getString( 'user_registration').':</b> '.$time -> drawDate( $user_result['user_regdate']);
				
				$user_profile_info = join( '<br />', $user_profile_fields);
				
				$user_result_block -> drawRow( $avatar_div.$user_profile_info);
				
				if ( $user_result['user_main_group'] != 1 || $session -> user['user_is_root']){
					
					/**
					 * actions row
					 */
					
					$user_actions = array();
					
					$user_edit_profile_link = array( 'act' => 'user_edit_profile', 'user' => $user_result['user_id']);
					$user_actions[] = '<a href="'.parent::adminLink( parent::getId(), $user_edit_profile_link).'">'.$style -> drawImage( 'edit').' '.$language -> getString( 'acp_users_section_members_find_member_act_edit_profile').'</a>';
					
					/**
					 * edit user signature
					 */
					
					$change_user_sig_link = array( 'act' => 'user_edit_sig', 'user' => $user_result['user_id']);
					
					$user_actions[] = '<a href="'.parent::adminLink( parent::getId(), $change_user_sig_link).'">'.$style -> drawImage( 'edit').' '.$language -> getString( 'acp_users_section_members_find_member_act_edit_sig').'</a>';
					
					$delete_user_avatar_link = array( 'act' => 'find_user', 'do' => 'kill_avatar', 'user' => $user_result['user_id']);
					
					if ( $user_result['user_avatar_type'] != 0)
						$user_actions[] = '<a href="'.parent::adminLink( parent::getId(), $delete_user_avatar_link).'">'.$style -> drawImage( 'delete').' '.$language -> getString( 'acp_users_section_members_find_member_act_delete_av').'</a>';
					
					$change_user_login_link = array( 'act' => 'user_edit_login', 'user' => $user_result['user_id']);
					$user_actions[] = '<a href="'.parent::adminLink( parent::getId(), $change_user_login_link).'">'.$style -> drawImage( 'edit').' '.$language -> getString( 'acp_users_section_members_find_member_act_change_login').'</a>';
					
					/**
					 * change user password
					 */
					
					$change_user_pass_link = array( 'act' => 'user_edit_pass', 'user' => $user_result['user_id']);
					
					$user_actions[] = '<a href="'.parent::adminLink( parent::getId(), $change_user_pass_link).'">'.$style -> drawImage( 'edit').' '.$language -> getString( 'acp_users_section_members_find_member_act_change_pass').'</a>';
					
					/**
					 * ban user option
					 */
					
					if ( $user_result['user_locked']){
						
						$unban_user_link = array( 'act' => 'find_user', 'do' => 'unban_user', 'user' => $user_result['user_id']);
						
						$user_actions[] = '<a href="'.parent::adminLink( parent::getId(), $unban_user_link).'">'.$style -> drawImage( 'lock').' '.$language -> getString( 'acp_users_section_members_find_member_act_unban_acc').'</a>';
					}else{
						
						$ban_user_link = array( 'act' => 'find_user', 'do' => 'ban_user', 'user' => $user_result['user_id']);
						
						$user_actions[] = '<a href="'.parent::adminLink( parent::getId(), $ban_user_link).'">'.$style -> drawImage( 'lock').' '.$language -> getString( 'acp_users_section_members_find_member_act_ban_acc').'</a>';
					}
					
					$delete_user_link = array( 'act' => 'user_delete', 'user' => $user_result['user_id']);
					$user_actions[] = '<a href="'.parent::adminLink( parent::getId(), $delete_user_link).'">'.$style -> drawImage( 'delete').' '.$language -> getString( 'acp_users_section_members_find_member_act_delete_acc').'</a>';
					
					$user_result_block -> drawRow( '<b>'.$language -> getString( 'actions').':</b><br />'.join( '<br />', $user_actions));
					
				}else{
					
					$user_result_block -> drawRow( '<b>'.$language -> getString( 'acp_users_section_members_find_member_cant_root').'</b>');
										
				}
					
				$user_result_block -> closeTable();

				$found_users_tab .=  $style -> drawFormBlock( $user_result['user_login'], $user_result_block -> display());
				
				/**
				 * close cell
				 */
				
				$found_users_tab .= '</td>';
				
				$column ++;
				$search_results_num ++;
				
			}
			
			if ( isset( $found_users_tab)){
							
				/**
				 * draw form
				 */
					
				$results_form = new form();
				$results_form -> openOpTable( true);
				$results_form -> addToContent( $found_users_tab);
				$results_form -> closeTable();
				
				$language -> setKey( 'member_searchs_results_num', $search_results_num);
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_section_members_find_member_results'), $results_form -> display()));
				
			}else{
				
				/**
				 * nothing found
				 */
				
				parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_section_members_find_member'),  $language -> getString( 'acp_users_section_members_find_member_nothing_found')));
				
				$this -> act_users();
				
			}
				
		}else{
			
			/**
			 * both are empty
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_section_members_find_member'), $language -> getString( 'acp_users_section_members_find_member_help')));
			
			$this -> act_users();
		}
		
	}
		
	function act_new_member(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'new_member');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_new_member'), parent::adminLink( parent::getId(), $path_link));		
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_users_subsection_new_member'));
		
		/**
		 * add new
		 */
		
		if ( $_GET['act'] == 'add_member'){
			
			$user_login = $_POST['user_login'];
			$user_mail = $_POST['user_mail'];
			$user_group = $_POST['user_group'];
			
			settype( $user_group, 'integer');
			
		}
		
		/**
		 * new user form
		 */
		
		$new_member_link = array( 'act' => 'add_member');
		
		$new_user_form = new form();
		$new_user_form -> openForm( parent::adminLink( parent::getId(), $new_member_link));
		$new_user_form -> openOpTable();
		
		$new_user_form -> drawTextInput( $language -> getString( 'acp_users_section_members_new_member_login'), 'user_login', $strings -> outputClear( $user_login));
		$new_user_form -> drawPassInput( $language -> getString( 'acp_users_section_members_new_member_pass'), 'user_pass');
		$new_user_form -> drawPassInput( $language -> getString( 'acp_users_section_members_new_member_pass_rep'), 'user_pass_rep');
		$new_user_form -> drawTextInput( $language -> getString( 'acp_users_section_members_new_member_mail'), 'user_mail', $strings -> outputClear( $user_mail));
		
		/**
		 * build up an list of proper groups
		 */
		
		$groups_query = $mysql -> query( "SELECT * FROM users_groups ORDER BY users_group_can_use_acp DESC, users_group_can_moderate DESC, users_group_name");
		
		while ( $groups_result = mysql_fetch_array( $groups_query, MYSQL_ASSOC)){
			
			$groups_result = $mysql -> clear( $groups_result);
			
			if ( $groups_result['users_group_id'] != 1 || $session -> user['user_is_root'])
				$found_users_groups[$groups_result['users_group_id']] = $groups_result['users_group_name'];
			
		}
		
		$new_user_form -> drawList( $language -> getString( 'acp_users_section_members_new_member_group'), 'user_group', $found_users_groups, $user_group);
		
		$new_user_form -> closeTable();
		$new_user_form -> drawButton( $language -> getString( 'acp_users_section_members_new_member_create_button'));
		$new_user_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_new_member'), $new_user_form -> display()));
		
	}
	
	function act_edit_rank(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * get rank
		 */
		
		if( isset( $_GET['rank'])){
					
			/**
			 * check if rank exists
			 */
			
			$rank_to_edit = $_GET['rank'];
			settype( $rank_to_edit, 'integer');
			
			$rank_edit_query = $mysql -> query( "SELECT * FROM ranks WHERE `rank_id` = '$rank_to_edit'");
			
			if ( $rank_result = mysql_fetch_array( $rank_edit_query, MYSQL_ASSOC)) {
				
				/**
				 * add breadcrumbs
				 */
				
				$path_link = array( 'act' => 'ranks');
				
				$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
				
				$path_link = array( 'act' => 'edit_rank', 'rank' => $rank_to_edit);
				
				$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_ranks_edit_rank'), parent::adminLink( parent::getId(), $path_link));
				
				/**
				 * set page title
				 */
				
				$output -> setTitle( $language -> getString( 'acp_users_subsection_ranks_edit_rank'));
				
				/**
				 * clear result
				 */
			
				$rank_result = $mysql -> clear( $rank_result);

				$rank_name = $rank_result['rank_name'];
				$rank_posts = $rank_result['rank_posts_required'];
				$rank_image = $rank_result['rank_image'];
				$rank_images = $rank_result['rank_stars'];
				
				if ( $_GET['act'] == 'change_rank'){
					
					$rank_name = stripslashes( $strings -> inputClear( $_POST['rank_name'], false));
					$rank_posts = $_POST['rank_posts'];
					$rank_images = $_POST['rank_images'];
					$rank_image = stripslashes( $strings -> inputClear( $_POST['rank_image'], false));
				
				}
				
				/**
				 * draw form
				 */
				
				$edit_form_link = array( 'act' => 'change_rank', 'rank' => $rank_to_edit);
		
				$edit_rank_form = new form();
				$edit_rank_form -> openForm( parent::adminLink( parent::getId(), $edit_form_link));
				$edit_rank_form -> openOpTable();
				
				$edit_rank_form -> drawTextInput( $language -> getString( 'acp_users_subsection_ranks_new_rank_name'), 'rank_name', $rank_name);
				$edit_rank_form -> drawTextInput( $language -> getString( 'acp_users_subsection_ranks_new_rank_posts'), 'rank_posts', $rank_posts);
				$edit_rank_form -> drawTextInput( $language -> getString( 'acp_users_subsection_ranks_new_rank_images'), 'rank_images', $rank_images);
				$edit_rank_form -> drawTextInput( $language -> getString( 'acp_users_subsection_ranks_new_rank_image'), 'rank_image', $rank_image, $language -> getString( 'acp_users_subsection_ranks_new_rank_image_help'));
				
				$edit_rank_form -> closeTable();
				$edit_rank_form -> drawButton( $language -> getString( 'acp_users_subsection_ranks_edit_rank_but'));
				$edit_rank_form -> closeForm();
				
				/**
				 * draw it
				 */
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_ranks_edit_rank'), $edit_rank_form -> display()));

			}else{
							
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_ranks_edit_rank'), $language -> getString( 'acp_users_subsection_ranks_edit_rank_nofound')));
			
				$this -> act_ranks();
			
			}
			
		}else{
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_ranks_edit_rank'), $language -> getString( 'acp_users_subsection_ranks_edit_rank_notarget')));
			
			$this -> act_ranks();
			
		}
	}
	
	function act_ranks(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'ranks');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_ranks'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_users_subsection_ranks'));
		
		/**
		 * do something
		 */
		
		$proper_does = array( 'new_rank', 'delete_rank');
		
		if ( isset( $_GET['do']) && in_array( $_GET['do'], $proper_does)){
			
			switch ( $_GET['do']){
				
				case 'new_rank':
				
					if ( $session -> checkForm()){
						
						/**
						 * build adds list
						 */
						
						$rank_name = $strings -> inputClear( $_POST['rank_name'], false);
						$rank_posts = $_POST['rank_posts'];
						$rank_images = $_POST['rank_images'];
						$rank_image = $strings -> inputClear( $_POST['rank_image'], false);
						
						settype( $rank_posts, 'integer');
						settype( $rank_images, 'integer');
						
						if ( $rank_posts < 0)
							$rank_posts = 0;
						
						if ( $rank_images < 0)
							$rank_images = 0;
							
						if ( $rank_images > 10)
							$rank_images = 10;
							
						if ( strlen( $rank_name) == 0){
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_ranks_new_rank'), $language -> getString( 'acp_users_subsection_ranks_new_rank_name_empty')));
							
							$new_rank_name = $rank_name;
							$new_rank_posts = $rank_posts;
							$new_rank_images = $rank_images;
							$new_rank_image = $rank_image;
							
						}else{
							
							/**
							 * mysql
							 */
							
							$new_rang_sql['rank_name'] = $rank_name;
							$new_rang_sql['rank_posts_required'] = $rank_posts;
							$new_rang_sql['rank_image'] = $rank_image;
							$new_rang_sql['rank_stars'] = $rank_images;
							
							$mysql -> insert( $new_rang_sql, 'ranks');
							
							$cache -> flushCache( 'users_ranks');
							
							/**
							 * add new log
							 */
							
							$log_keys = array( 'new_rank_name' => $rank_name);
							$logs -> addAdminLog( $language -> getString( 'acp_users_subsection_ranks_new_rank_log'), $log_keys);
							
							/**
							 * message
							 */
							
							parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_ranks_new_rank'), $language -> getString( 'acp_users_subsection_ranks_new_rank_done')));
							
						}
							
					}
					
				break;
				
				case 'delete_rank':
					
					if( isset( $_GET['rank'])){
						
						/**
						 * check if rank exists
						 */
						
						$rank_to_kill = $_GET['rank'];
						settype( $rank_to_kill, 'integer');
						
						$rank_del_query = $mysql -> query( "SELECT * FROM ranks WHERE `rank_id` = '$rank_to_kill'");
						
						if ( $rank_result = mysql_fetch_array( $rank_del_query, MYSQL_ASSOC)) {
							
							/**
							 * delete it
							 */
							
							$mysql -> delete( 'ranks', "`rank_id` = '$rank_to_kill'");
							$cache -> flushCache( 'users_ranks');
							
							/**
							 * add log
							 */
							
							$log_keys = array( 'delete_rank_id' => $rank_to_kill);
							$logs -> addAdminLog( $language -> getString( 'acp_users_subsection_ranks_delete_rank_log'), $log_keys);
							
							/**
							 * message
							 */
							
							parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_ranks_delete_rank'), $language -> getString( 'acp_users_subsection_ranks_delete_rank_done')));
						
						}else{
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_ranks_delete_rank'), $language -> getString( 'acp_users_subsection_ranks_delete_rank_nofound')));
						
						}
						
					}else{
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_ranks_delete_rank'), $language -> getString( 'acp_users_subsection_ranks_delete_rank_notarget')));
						
					}
					
				break;
				
			}
			
		}
		
		/**
		 * get ranks list
		 */
		
		$ranks_list = new form();
		$ranks_list -> openOpTable();
		$ranks_list -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_users_subsection_ranks_list_name').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_ranks_list_image').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_ranks_list_posts').'</th>
			<th>'.$language -> getString( 'actions').'</th>
		</tr>');
		
		/**
		 * draw ranks
		 */
		
		$ranks_query = $mysql -> query( "SELECT * FROM ranks ORDER BY `rank_posts_required`");
		
		while ( $ranks_result = mysql_fetch_array( $ranks_query, MYSQL_ASSOC)){
			
			//clear result
			$ranks_result = $mysql -> clear( $ranks_result);

			/**
			 * rank image
			 */
			
			$rank_image = '';
					
			while ( $ranks_result['rank_stars'] > 0) {
				
				$rank_image .= $style -> drawImage( 'pip');
				$ranks_result['rank_stars']--;
			}
			
			/**
			 * add row
			 */
			
			$edit_rank_link = array( 'act' => 'edit_rank', 'rank' => $ranks_result['rank_id']);
			$delete_rank_link = array( 'act' => 'ranks', 'do' => 'delete_rank', 'rank' => $ranks_result['rank_id']);
			
			$ranks_list -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%">'.$ranks_result['rank_name'].'</th>
				<td class="opt_row2" NOWRAP>'.$rank_image.'</td>
				<td class="opt_row1" style="text-align: center" NOWRAP>'.$ranks_result['rank_posts_required'].'</td>
				<td class="opt_row3" style="text-align: center" NOWRAP>
				<a href="'.parent::adminLink( parent::getId(), $edit_rank_link).'">'.$style -> drawImage( 'edit', $language -> getString( 'edit')).'</a>
				<a href="'.parent::adminLink( parent::getId(), $delete_rank_link).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a>
				</td>
			</tr>');
				
		}
		
		/**
		 * close and draw table
		 */
		
		$ranks_list -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_ranks'), $ranks_list -> display()));
		
		/**
		 * now new rank form
		 */
		
		$new_form_link = array( 'act' => 'ranks', 'do' => 'new_rank');

		$new_rank_form = new form();
		$new_rank_form -> openForm( parent::adminLink( parent::getId(), $new_form_link));
		$new_rank_form -> openOpTable();
		
		$new_rank_form -> drawTextInput( $language -> getString( 'acp_users_subsection_ranks_new_rank_name'), 'rank_name', $new_rank_name);
		$new_rank_form -> drawTextInput( $language -> getString( 'acp_users_subsection_ranks_new_rank_posts'), 'rank_posts', $new_rank_posts);
		$new_rank_form -> drawTextInput( $language -> getString( 'acp_users_subsection_ranks_new_rank_images'), 'rank_images', $new_rank_images);
		$new_rank_form -> drawTextInput( $language -> getString( 'acp_users_subsection_ranks_new_rank_image'), 'rank_image', $new_rank_image, $language -> getString( 'acp_users_subsection_ranks_new_rank_image_help'));
		
		$new_rank_form -> closeTable();
		$new_rank_form -> drawButton( $language -> getString( 'acp_users_subsection_ranks_new_rank_but'));
		$new_rank_form -> closeForm();
		
		/**
		 * draw it
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_ranks_new_rank'), $new_rank_form -> display()));

	}
	
	function act_reps(){
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'reps');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_reps'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_users_subsection_reps'));
		
		if ( $_GET['reorder'] && $session -> checkForm()){
			
			$reps_to_reorder = $_POST['reps'];
			
			settype( $reps_to_reorder, 'array');
			
			foreach ( $reps_to_reorder as $rep_id => $rep_pos){
				
				settype( $rep_id, 'integer');
				settype( $rep_pos, 'integer');
				
				$mysql -> update( array( 'reputation_scale_points' => $rep_pos), "reputation_scale", "`reputation_scale_id` = '$rep_id'");
				
			}
			
			$cache -> flushCache( 'users_reps');
			
		}
		
		/**
		 * begin drawing table
		 */
		
		$reps_tab = new form();
		$reps_tab -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'reps', 'reorder' => true)));
		$reps_tab -> openOpTable();
		$reps_tab -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_users_subsection_topics_prefixes_name').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_topics_prefixes_pos').'</th>
			<th>'.$language -> getString( 'actions').'</th>
		</tr>');
		
		/**
		 * select prefixes from mysql
		 */
		
		$reps_query = $mysql -> query( "SELECT * FROM reputation_scale ORDER BY reputation_scale_points, reputation_scale_name");
		
		while ( $reps_result = mysql_fetch_array( $reps_query, MYSQL_ASSOC)) {
			
			//clear
			$reps_result = $mysql -> clear( $reps_result);
			
			/**
			 * add row
			 */
			$reps_tab -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%">'.$reps_result['reputation_scale_name'].'</td>
				<td class="opt_row2" style="text-align: center" nowrap="nowrap"><input name="reps['.$reps_result['reputation_scale_id'].']" size="5" type="text" value="'.$reps_result['reputation_scale_points'].'"/></td>
				<td class="opt_row3" style="text-align: center" nowrap="nowrap">
				<a href="'.parent::adminLink( parent::getId(), array( 'act' => 'reps_edit', 'rep' => $reps_result['reputation_scale_id'])).'">'.$style -> drawImage( 'edit', $language -> getString( 'edit')).'</a>
				<a href="'.parent::adminLink( parent::getId(), array( 'act' => 'reps_delete', 'rep' => $reps_result['reputation_scale_id'])).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a>
				</td>
			</tr>');
				
		}
		
		/**
		 * finish and draw table
		 */
		
		$reps_tab -> closeTable();
		$reps_tab -> drawButton( $language -> getString( 'acp_users_subsection_topics_prefixes_reorder'), false, '<input type="button" name="Submit" value="'.$language -> getString( 'acp_users_subsection_reps_new_rep_but').'" onclick="document.location=\''.parent::adminLink( parent::getId(), array('act' => 'reps_new')).'\'"/>');
		$reps_tab -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_reps'), $reps_tab -> display()));
		
	}
	
	function act_reps_new(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'reps_new');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_reps'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_reps_new_rep'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_users_subsection_reps_new_rep'));
		
		/**
		 * set blank values
		 */
		
		$rep_name = '';
		$rep_pos = 0;
		
		/**
		 * retake vars
		 */
		
		if ( $_GET['act'] == 'reps_add'){
			
			$rep_name = stripslashes( $strings -> inputClear( $_POST['rep_name'], false));
			$rep_pos = $_POST['rep_pos'];
			
			settype( $rep_pos, 'integer');
			
		}
		
		/**
		 * retake values
		 */
		
		/**
		 * draw form
		 */
		
		$new_prefix = new form();
		$new_prefix -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'reps_add')));
		$new_prefix -> openOpTable();
		$new_prefix -> drawTextInput( $language -> getString( 'acp_users_subsection_reps_name'), 'rep_name', $rep_name);
		$new_prefix -> drawTextInput( $language -> getString( 'acp_users_subsection_reps_pos'), 'rep_pos', $rep_pos);
		$new_prefix -> closeTable();
		$new_prefix -> drawButton( $language -> getString( 'acp_users_subsection_reps_new_rep_but'));
		$new_prefix -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_reps_new_rep'), $new_prefix -> display()));
	}
	
	function act_reps_edit(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * get rep to edit
		 */
		
		$rep_to_edit = $_GET['rep'];
		settype( $rep_to_edit, 'integer');
		
		$rep_query = $mysql -> query( "SELECT * FROM reputation_scale WHERE `reputation_scale_id` = '$rep_to_edit'");
		
		if ( $rep_result = mysql_fetch_array( $rep_query, MYSQL_ASSOC)){
		
			//clear
			$rep_result = $mysql -> clear( $rep_result);
			
			/**
			 * add breadcrumbs
			 */
			
			$path_link = array( 'act' => 'reps_edit', 'rep' => $rep_to_edit);
			
			$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
			$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_reps'), parent::adminLink( parent::getId(), $path_link));
			$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_reps_edit_rep'), parent::adminLink( parent::getId(), $path_link));
			
			/**
			 * set page title
			 */
			
			$output -> setTitle( $language -> getString( 'acp_users_subsection_reps_edit_rep'));
			
			/**
			 * set blank values
			 */
			
			$rep_name = $rep_result['reputation_scale_name'];
			$rep_pos = $rep_result['reputation_scale_points'];
			
			/**
			 * retake vars
			 */
			
			if ( $_GET['act'] == 'reps_add'){
				
				$rep_name = stripslashes( $strings -> inputClear( $_POST['rep_name'], false));
				$rep_pos = $_POST['rep_pos'];
				
				settype( $rep_pos, 'integer');
				
			}
			
			/**
			 * draw form
			 */
			
			$new_prefix = new form();
			$new_prefix -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'reps_change', 'rep' => $rep_to_edit)));
			$new_prefix -> openOpTable();
			$new_prefix -> drawTextInput( $language -> getString( 'acp_users_subsection_reps_name'), 'rep_name', $rep_name);
			$new_prefix -> drawTextInput( $language -> getString( 'acp_users_subsection_reps_pos'), 'rep_pos', $rep_pos);
			$new_prefix -> closeTable();
			$new_prefix -> drawButton( $language -> getString( 'acp_users_subsection_reps_edit_rep_but'));
			$new_prefix -> closeForm();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_reps_edit_rep'), $new_prefix -> display()));
	
		}else{
		
			//rep not found
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_reps_edit_rep'), $language -> getString( 'acp_users_subsection_reps_name_notfound')));	
			
			$this -> act_reps();
			
		}
			
	}
	
	function act_new_users_group(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'users_groups');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
		
		$path_link = array( 'act' => 'new_users_group');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_users_groups_new_group'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_users_subsection_users_groups_new_group'));
		
		/**
		 * basic fields
		 */
		
		$group_name = '';
		$group_perms = 0;
		$group_image = '';
		$group_name = '';
		$group_title = '';
		$group_prefix = '';
		$group_suffix = '';
		$group_hidden = '';
		
		$group_can_see_closed = 0;
		$group_can_users_profiles = 0;
		$group_can_use_pm = 0;
		$group_pm_limit = 5;
		$group_can_mail = 0;
		$group_avoid_badwords = 0;
		$group_can_see_hidden = 0;
		$group_can_search = 0;
		$group_search_limit = 15;
		
		$group_delete_own_topics = 0;
		$group_change_own_topics = 0;
		$group_close_own_topics = 0;
		$group_delete_own_posts = 0;
		$group_edit_own_posts = 0;
		$group_edit_limit = 900;
		$group_draw_edit_legend = 1;
		$group_avoid_flood = 0;
		$group_avoid_closed_topics = 0;
		$group_start_surveys = 0;
		$group_vote_surveys = 0;
		
		$group_uploads_quota = 10485760;
		$group_uploads_limit = 1048576;
		
		$group_can_use_acp = 0;
		$group_can_moderate = 0;
		$group_can_edit_calendar = 0;
		$group_shoutbox_access = 0;
		
		$group_promote_to = 0;
		$group_promote_at = 0;
		
		if ( $_GET['act'] == 'add_new_users_group'){
			
			$group_name = stripslashes( $strings -> inputClear( $_POST['group_name'], false));
			$group_perms = $_POST['group_perms'];
			$group_image = stripslashes( $strings -> inputClear( $_POST['group_image'], false));
			$group_message_title = stripslashes( $strings -> inputClear( $_POST['group_message_title'], false));
			$group_message = stripslashes( $strings -> inputClear( $_POST['group_message'], false));
			$group_title = stripslashes( $strings -> inputClear( $_POST['group_title'], false));
			$group_prefix = stripslashes( $strings -> inputClear( $_POST['group_prefix']));
			$group_suffix = stripslashes( $strings -> inputClear( $_POST['group_suffix']));
			$group_hidden = $_POST['group_hidden'];
			
			$group_can_see_closed = $_POST['group_can_see_closed'];
			$group_can_users_profiles = $_POST['group_can_users_profiles'];
			$group_can_use_pm = $_POST['group_can_use_pm'];
			$group_pm_limit = $_POST['group_pm_limit'];
			$group_can_mail = $_POST['group_can_mail'];
			$group_avoid_badwords = $_POST['group_avoid_badwords'];
			$group_can_see_hidden = $_POST['group_can_see_hidden'];
			$group_can_search = $_POST['group_can_search'];
			$group_search_limit = $_POST['group_search_limit'];
			
			$group_delete_own_topics = $_POST['group_delete_own_topics'];
			$group_change_own_topics = $_POST['group_change_own_topics'];
			$group_close_own_topics = $_POST['group_close_own_topics'];
			$group_delete_own_posts = $_POST['group_delete_own_posts'];
			$group_edit_own_posts = $_POST['group_edit_own_posts'];
			$group_edit_limit = $_POST['group_edit_limit'];
			$group_draw_edit_legend = $_POST['group_draw_edit_legend'];
			$group_avoid_flood = $_POST['group_avoid_flood'];
			$group_avoid_closed_topics = $_POST['group_avoid_closed_topics'];
			$group_start_surveys = $_POST['group_start_surveys'];
			$group_vote_surveys = $_POST['group_vote_surveys'];
			
			$group_uploads_quota = $_POST['group_uploads_quota'];
			$group_uploads_limit = $_POST['group_uploads_limit'];
			
			$group_can_use_acp = $_POST['group_can_use_acp'];
			$group_can_moderate = $_POST['group_can_moderate'];
			$group_can_edit_calendar = $_POST['group_can_edit_calendar'];
			$group_shoutbox_access = $_POST['group_shoutbox_access'];
			
			$group_promote_to = $_POST['group_promote_to'];
			$group_promote_at = $_POST['group_promote_at'];
			
			/**
			 * force types
			 */
			
			settype( $group_perms, 'integer');
			settype( $group_hidden, 'bool');
			
			settype( $group_can_see_closed, 'bool');
			settype( $group_can_users_profiles, 'bool');
			settype( $group_can_use_pm, 'bool');
			settype( $group_pm_limit, 'integer');
			settype( $group_can_mail, 'bool');
			settype( $group_avoid_badwords, 'bool');
			settype( $group_can_see_hidden, 'bool');
			settype( $group_can_search, 'bool');
			settype( $group_search_limit, 'integer');
			
			if ( $group_pm_limit < 1)
				$group_pm_limit = 1;
				
			if ( $group_search_limit < 0)
				$group_search_limit = 0;
			
			settype( $group_delete_own_topics, 'bool');
			settype( $group_change_own_topics, 'bool');
			settype( $group_close_own_topics, 'bool');
			settype( $group_delete_own_posts, 'bool');
			settype( $group_edit_own_posts, 'bool');
			settype( $group_edit_limit, 'integer');
			settype( $group_draw_edit_legend, 'bool');
			settype( $group_avoid_flood, 'bool');
			settype( $group_avoid_closed_topics, 'bool');
			settype( $group_start_surveys, 'bool');
			settype( $group_vote_surveys, 'bool');
			
			if ( $group_edit_limit < 0)
				$group_edit_limit = 0;
			
			settype( $group_uploads_quota, 'integer');
			settype( $group_uploads_limit, 'integer');
			
			if ( $group_uploads_quota < 0)
				$group_uploads_quota = 0;
				
			if ( $group_uploads_limit < 0)
				$group_uploads_limit = 0;
				
			settype( $group_can_use_acp, 'bool');
			settype( $group_can_moderate, 'bool');
			settype( $group_can_edit_calendar, 'bool');
			settype( $group_shoutbox_access, 'integer');
			
			if ( $group_shoutbox_access < 0)
				$group_shoutbox_access = 0;
			
			if ( $group_shoutbox_access > 3)
				$group_shoutbox_access = 3;
				
			settype( $group_promote_to, 'integer');
			settype( $group_promote_at, 'integer');
			
			if ( $group_promote_to == 1)
				$group_promote_to = 3;
			
			if ( $group_promote_at < 0)
				$group_promote_at = 0;
				
		}
		
		/**
		 * begind drawing form
		 */
		
		$save_new_group_link = array( 'act' => 'add_new_users_group');
		
		$new_group_form = new form();
		$new_group_form -> openForm( parent::adminLink( parent::getId(), $save_new_group_link));
		$new_group_form -> openOpTable();
		
		/**
		 * general section
		 */
		
		$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_name'), 'group_name', $group_name);
		
		/**
		 * perms list
		 */
		
		$perms_query = $mysql -> query( "SELECT * FROM users_perms ORDER BY `users_perm_name`");
		while ( $perms_result = mysql_fetch_array( $perms_query, MYSQL_ASSOC)){
			
			$perms_result = $mysql -> clear( $perms_result);
			
			$perms_list[ $perms_result['users_perm_id']] = $perms_result['users_perm_name'];
			
		}
		
		$new_group_form -> drawList( $language -> getString( 'acp_users_subsection_users_groups_new_group_perms'), 'group_perms', $perms_list, $group_perms);
		
		$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_image'), 'group_image', $group_image);
		$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_message_title'), 'group_message_title', $group_message_title, $language -> getString( 'acp_users_subsection_users_groups_new_group_message_title_help'));
		$new_group_form -> drawEditor( $language -> getString( 'acp_users_subsection_users_groups_new_group_message'), 'group_message', $group_message, null, true, true);	
		$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_title'), 'group_title', $group_title);
		$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_prefix'), 'group_prefix', $strings -> outputClear( $group_prefix));
		$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_suffix'), 'group_suffix', $strings -> outputClear( $group_suffix));
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_hidden'), 'group_hidden', $group_hidden);
		
		$new_group_form -> closeTable();
		$new_group_form -> drawSpacer( $language -> getString( 'acp_users_subsection_users_groups_new_group_global_perms'));
		$new_group_form -> openOpTable();
		
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_see_closed'), 'group_can_see_closed', $group_can_see_closed);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_users_profiles'), 'group_can_users_profiles', $group_can_users_profiles);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_use_pm'), 'group_can_use_pm', $group_can_use_pm);
		$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_pm_limit'), 'group_pm_limit', $group_pm_limit, $language -> getString( 'acp_users_subsection_users_groups_new_group_pm_limit_help'));
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_mail'), 'group_can_mail', $group_can_mail);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_avoid_badwords'), 'group_avoid_badwords', $group_avoid_badwords);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_see_hidden'), 'group_can_see_hidden', $group_can_see_hidden);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_search'), 'group_can_search', $group_can_search);
		$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_search_limit'), 'group_search_limit', $group_search_limit, $language -> getString( 'acp_users_subsection_users_groups_new_group_search_limit'));
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_edit_calendar'), 'group_can_edit_calendar', $group_can_edit_calendar);

		$shoutbox_access[0] = $language -> getString( 'acp_users_subsection_users_groups_new_group_can_shoutbox_0');
		$shoutbox_access[1] = $language -> getString( 'acp_users_subsection_users_groups_new_group_can_shoutbox_1');
		$shoutbox_access[2] = $language -> getString( 'acp_users_subsection_users_groups_new_group_can_shoutbox_2');
		$shoutbox_access[3] = $language -> getString( 'acp_users_subsection_users_groups_new_group_can_shoutbox_3');
		
		$new_group_form -> drawList( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_shoutbox'), 'group_shoutbox_access', $shoutbox_access, $group_shoutbox_access);
	
		$new_group_form -> closeTable();
		$new_group_form -> drawSpacer( $language -> getString( 'acp_users_subsection_users_groups_new_group_writing_perms'));
		$new_group_form -> openOpTable();
		
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_delete_own_topics'), 'group_delete_own_topics', $group_delete_own_topics);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_change_own_topics'), 'group_change_own_topics', $group_change_own_topics);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_close_own_topics'), 'group_close_own_topics', $group_close_own_topics);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_delete_own_posts'), 'group_delete_own_posts', $group_delete_own_posts);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_edit_own_posts'), 'group_edit_own_posts', $group_edit_own_posts);
		$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_edit_limit'), 'group_edit_limit', $group_edit_limit, $language -> getString( 'acp_users_subsection_users_groups_new_group_edit_limit_help'));
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_draw_edit_legend'), 'group_draw_edit_legend', $group_draw_edit_legend);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_avoid_flood'), 'group_avoid_flood', $group_avoid_flood);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_avoid_closed_topics'), 'group_avoid_closed_topics', $group_avoid_closed_topics);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_start_surveys'), 'group_start_surveys', $group_start_surveys);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_vote_surveys'), 'group_vote_surveys', $group_vote_surveys);
		
		$new_group_form -> closeTable();
		$new_group_form -> drawSpacer( $language -> getString( 'acp_users_subsection_users_groups_new_group_uploading'));
		$new_group_form -> openOpTable();
			
		$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_uploads_quota'), 'group_uploads_quota', $group_uploads_quota, $language -> getString( 'acp_users_subsection_users_groups_new_group_in_bytes'));
		$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_uploads_limit'), 'group_uploads_limit', $group_uploads_limit, $language -> getString( 'acp_users_subsection_users_groups_new_group_in_bytes'));
		
		$new_group_form -> closeTable();
		$new_group_form -> drawSpacer( $language -> getString( 'acp_users_subsection_users_groups_new_group_special_access'));
		$new_group_form -> openOpTable();
		
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_use_acp'), 'group_can_use_acp', $group_can_use_acp);
		$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_moderate'), 'group_can_moderate', $group_can_moderate);
		
		$new_group_form -> closeTable();
		$new_group_form -> drawSpacer( $language -> getString( 'acp_users_subsection_users_groups_new_group_autopromote'));
		$new_group_form -> openOpTable();
			
		
		$groups_query = $mysql -> query( "SELECT * FROM users_groups WHERE `users_group_id` <> '1' ORDER BY `users_group_name`");
		while ( $groups_result = mysql_fetch_array( $groups_query, MYSQL_ASSOC)){
			
			$groups_result = $mysql -> clear( $groups_result);
			
			$groups_list[ $groups_result['users_group_id']] = $groups_result['users_group_name'];
			
		}
		
		$new_group_form -> drawList( $language -> getString( 'acp_users_subsection_users_groups_new_group_promote_to'), 'group_promote_to', $groups_list, $group_promote_to);
		
		$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_promote_at'), 'group_promote_at', $group_promote_at, $language -> getString( 'acp_users_subsection_users_groups_new_group_promote_at_help'));
			
		$new_group_form -> closeTable();
		$new_group_form -> drawButton( $language -> getString( 'acp_users_subsection_users_groups_new_group_button'));
		$new_group_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_users_groups_new_group'), $new_group_form -> display()));
		
	}
	
	function act_edit_users_group(){
	
	//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * error checking
		 */
		
		if ( isset( $_GET['group']) && !empty( $_GET['group'])){
			
			/**
			 * get group to delete
			 */
			
			$group_to_edit = $_GET['group'];
			
			settype( $group_to_edit, 'integer');
			
			/**
			 * select group
			 */
			
			$group_query = $mysql -> query( "SELECT * FROM users_groups WHERE `users_group_id` = '$group_to_edit'");
			
			if ( $group_result = mysql_fetch_array( $group_query, MYSQL_ASSOC)){
				
				//clear
				
				$group_result = $mysql -> clear( $group_result);
				
				/**
				 * add breadcrumbs
				 */
	
				$path_link = array( 'act' => 'users_groups');
				
				$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
				
				$path_link = array( 'act' => 'edit_users_group', 'group' => $group_to_delete);
				
				$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_users_groups_edit_group'), parent::adminLink( parent::getId(), $path_link));
				
				/**
				 * set page title
				 */
				
				$output -> setTitle( $language -> getString( 'acp_users_subsection_users_groups_edit_group'));
				
				/**
				 * basic fields
				 */
				
				$group_name = $group_result['users_group_name'];
				$group_perms = $group_result['users_group_permissions'];
				$group_image = $group_result['users_group_image'];
				$group_message_title = $group_result['users_group_msg_title'];
				$group_message = $group_result['users_group_message'];
				$group_title = $group_result['users_group_title'];
				$group_prefix = $group_result['users_group_prefix'];
				$group_suffix = $group_result['users_group_suffix'];
				$group_hidden = $group_result['users_group_hidden'];
				
				$group_can_see_closed = $group_result['users_group_can_see_closed_page'];
				$group_can_users_profiles = $group_result['users_group_can_see_users_profiles'];
				$group_can_use_pm = $group_result['users_group_can_use_pm'];
				$group_pm_limit = $group_result['users_group_pm_limit'];
				$group_can_mail = $group_result['users_group_can_email_members'];
				$group_avoid_badwords = $group_result['users_group_avoid_badwords'];
				$group_can_see_hidden = $group_result['users_group_see_hidden'];
				$group_can_search = $group_result['users_group_search'];
				$group_search_limit = $group_result['users_group_search_limit'];
				
				$group_delete_own_topics = $group_result['users_group_delete_own_topics'];
				$group_change_own_topics = $group_result['users_group_change_own_topics'];
				$group_close_own_topics = $group_result['users_group_close_own_topics'];
				$group_delete_own_posts = $group_result['users_group_delete_own_posts'];
				$group_edit_own_posts = $group_result['users_group_edit_own_posts'];
				$group_edit_limit = $group_result['users_group_edit_time_limit'];
				$group_draw_edit_legend = $group_result['users_group_draw_edit_legend'];
				$group_avoid_flood = $group_result['users_group_avoid_flood'];
				$group_avoid_closed_topics = $group_result['users_group_avoid_closed_topics'];
				$group_start_surveys = $group_result['users_group_start_surveys'];
				$group_vote_surveys = $group_result['users_group_vote_surveys'];
				
				$group_uploads_quota = $group_result['users_group_uploads_quota'];
				$group_uploads_used = $group_result['users_group_uploads_used'];
				$group_uploads_limit = $group_result['users_group_uploads_limit'];
				
				$group_can_use_acp = $group_result['users_group_can_use_acp'];
				$group_can_moderate = $group_result['users_group_can_moderate'];
				$group_can_edit_calendar = $group_result['users_group_can_edit_calendar'];
				$group_shoutbox_access = $group_result['users_group_shoutbox_access'];
				
				$group_promote_to = $group_result['users_group_promote_to'];
				$group_promote_at = $group_result['users_group_promote_at'];
				
				if ( $_GET['act'] == 'change_users_group'){
					
					$group_name = stripslashes( $strings -> inputClear( $_POST['group_name'], false));
					$group_perms = $_POST['group_perms'];
					$group_image = stripslashes( $strings -> inputClear( $_POST['group_image'], false));
					$group_message_title = stripslashes( $strings -> inputClear( $_POST['group_message_title'], false));
					$group_message = stripslashes( $strings -> inputClear( $_POST['group_message'], false));
					$group_title = stripslashes( $strings -> inputClear( $_POST['group_title'], false));
					$group_prefix = stripslashes( $strings -> inputClear( $_POST['group_prefix']));
					$group_suffix = stripslashes( $strings -> inputClear( $_POST['group_suffix']));
					$group_hidden = $_POST['group_hidden'];
					
					$group_can_see_closed = $_POST['group_can_see_closed'];
					$group_can_users_profiles = $_POST['group_can_users_profiles'];
					$group_can_use_pm = $_POST['group_can_use_pm'];
					$group_pm_limit = $_POST['group_pm_limit'];
					$group_can_mail = $_POST['group_can_mail'];
					$group_avoid_badwords = $_POST['group_avoid_badwords'];
					$group_can_see_hidden = $_POST['group_can_see_hidden'];
					$group_can_search = $_POST['group_can_search'];
					$group_search_limit = $_POST['group_search_limit'];
					
					$group_delete_own_topics = $_POST['group_delete_own_topics'];
					$group_change_own_topics = $_POST['group_change_own_topics'];
					$group_close_own_topics = $_POST['group_close_own_topics'];
					$group_delete_own_posts = $_POST['group_delete_own_posts'];
					$group_edit_own_posts = $_POST['group_edit_own_posts'];
					$group_edit_limit = $_POST['group_edit_limit'];
					$group_draw_edit_legend = $_POST['group_draw_edit_legend'];
					$group_avoid_flood = $_POST['group_avoid_flood'];
					$group_avoid_closed_topics = $_POST['group_avoid_closed_topics'];
					$group_start_surveys = $_POST['group_start_surveys'];
					$group_vote_surveys = $_POST['group_vote_surveys'];
					
					$group_uploads_quota = $_POST['group_uploads_quota'];
					$group_uploads_limit = $_POST['group_uploads_limit'];
					
					$group_can_use_acp = $_POST['group_can_use_acp'];
					$group_can_moderate = $_POST['group_can_moderate'];
					$group_can_edit_calendar = $_POST['group_can_edit_calendar'];
					$group_shoutbox_access = $_POST['group_shoutbox_access'];
			
					$group_promote_to = $_POST['group_promote_to'];
					$group_promote_at = $_POST['group_promote_at'];
					
					/**
					 * force types
					 */
					
					settype( $group_perms, 'integer');
					settype( $group_hidden, 'bool');
					
					settype( $group_can_see_closed, 'bool');
					settype( $group_can_users_profiles, 'bool');
					settype( $group_can_use_pm, 'bool');
					settype( $group_pm_limit, 'integer');
					settype( $group_can_mail, 'bool');
					settype( $group_avoid_badwords, 'bool');
					settype( $group_can_see_hidden, 'bool');
					settype( $group_can_search, 'bool');
					settype( $group_search_limit, 'integer');
					
					if ( $group_pm_limit < 1)
						$group_pm_limit = 1;
						
					if ( $group_search_limit < 0)
						$group_search_limit = 0;
					
					settype( $group_delete_own_topics, 'bool');
					settype( $group_change_own_topics, 'bool');
					settype( $group_close_own_topics, 'bool');
					settype( $group_delete_own_posts, 'bool');
					settype( $group_edit_own_posts, 'bool');
					settype( $group_edit_limit, 'integer');
					settype( $group_draw_edit_legend, 'bool');
					settype( $group_avoid_flood, 'bool');
					settype( $group_avoid_closed_topics, 'bool');
					settype( $group_start_surveys, 'bool');
					settype( $group_vote_surveys, 'bool');
					
					if ( $group_edit_limit < 0)
						$group_edit_limit = 0;
					
					settype( $group_uploads_quota, 'integer');
					settype( $group_uploads_limit, 'integer');
					
					if ( $group_uploads_quota < 0)
						$group_uploads_quota = 0;
						
					if ( $group_uploads_limit < 0)
						$group_uploads_limit = 0;
						
					settype( $group_can_use_acp, 'bool');
					settype( $group_can_moderate, 'bool');
					settype( $group_can_edit_calendar, 'bool');
					settype( $group_shoutbox_access, 'integer');
			
					if ( $group_shoutbox_access < 0)
						$group_shoutbox_access = 0;
					
					if ( $group_shoutbox_access > 3)
						$group_shoutbox_access = 3;
			
					if ( $group_shoutbox_access > 1 && $group_to_edit == 2)
						$group_shoutbox_access = 1;
						
					settype( $group_promote_to, 'integer');
					settype( $group_promote_at, 'integer');
					
					if ( $group_promote_to == 1)
						$group_promote_to = 3;
					
					if ( $group_promote_at < 0)
						$group_promote_at = 0;
						
				}
				
				/**
				 * begind drawing form
				 */
				
				$save_new_group_link = array( 'act' => 'change_users_group', 'group' => $group_to_edit);
				
				$new_group_form = new form();
				$new_group_form -> openForm( parent::adminLink( parent::getId(), $save_new_group_link));
				$new_group_form -> openOpTable();
				
				/**
				 * general section
				 */
				
				$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_name'), 'group_name', $group_name);
				
				/**
				 * perms list
				 */
				
				$perms_query = $mysql -> query( "SELECT * FROM users_perms ORDER BY `users_perm_name`");
				while ( $perms_result = mysql_fetch_array( $perms_query, MYSQL_ASSOC)){
					
					$perms_result = $mysql -> clear( $perms_result);
					
					$perms_list[ $perms_result['users_perm_id']] = $perms_result['users_perm_name'];
					
				}
				
				$new_group_form -> drawList( $language -> getString( 'acp_users_subsection_users_groups_new_group_perms'), 'group_perms', $perms_list, $group_perms);
				
				$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_image'), 'group_image', $group_image);
				$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_message_title'), 'group_message_title', $group_message_title, $language -> getString( 'acp_users_subsection_users_groups_new_group_message_title_help'));
				$new_group_form -> drawEditor( $language -> getString( 'acp_users_subsection_users_groups_new_group_message'), 'group_message', $group_message, null, true, true);
				$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_title'), 'group_title', $group_title);
				$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_prefix'), 'group_prefix', $strings -> outputClear( $group_prefix));
				$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_suffix'), 'group_suffix', $strings -> outputClear( $group_suffix));
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_hidden'), 'group_hidden', $group_hidden);
				
				$new_group_form -> closeTable();
				$new_group_form -> drawSpacer( $language -> getString( 'acp_users_subsection_users_groups_new_group_global_perms'));
				$new_group_form -> openOpTable();
				
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_see_closed'), 'group_can_see_closed', $group_can_see_closed);
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_users_profiles'), 'group_can_users_profiles', $group_can_users_profiles);
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_use_pm'), 'group_can_use_pm', $group_can_use_pm);
				$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_pm_limit'), 'group_pm_limit', $group_pm_limit, $language -> getString( 'acp_users_subsection_users_groups_new_group_pm_limit_help'));
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_mail'), 'group_can_mail', $group_can_mail);
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_avoid_badwords'), 'group_avoid_badwords', $group_avoid_badwords);
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_see_hidden'), 'group_can_see_hidden', $group_can_see_hidden);
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_search'), 'group_can_search', $group_can_search);
				$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_search_limit'), 'group_search_limit', $group_search_limit, $language -> getString( 'acp_users_subsection_users_groups_new_group_search_limit'));
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_edit_calendar'), 'group_can_edit_calendar', $group_can_edit_calendar);
				
				$shoutbox_access[0] = $language -> getString( 'acp_users_subsection_users_groups_new_group_can_shoutbox_0');
				$shoutbox_access[1] = $language -> getString( 'acp_users_subsection_users_groups_new_group_can_shoutbox_1');
				
				if ( $group_to_edit != 2){
					
					$shoutbox_access[2] = $language -> getString( 'acp_users_subsection_users_groups_new_group_can_shoutbox_2');
					$shoutbox_access[3] = $language -> getString( 'acp_users_subsection_users_groups_new_group_can_shoutbox_3');
				
				}
				
				$new_group_form -> drawList( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_shoutbox'), 'group_shoutbox_access', $shoutbox_access, $group_shoutbox_access);
					
				$new_group_form -> closeTable();
				$new_group_form -> drawSpacer( $language -> getString( 'acp_users_subsection_users_groups_new_group_writing_perms'));
				$new_group_form -> openOpTable();
				
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_delete_own_topics'), 'group_delete_own_topics', $group_delete_own_topics);
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_change_own_topics'), 'group_change_own_topics', $group_change_own_topics);
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_close_own_topics'), 'group_close_own_topics', $group_close_own_topics);
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_delete_own_posts'), 'group_delete_own_posts', $group_delete_own_posts);
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_edit_own_posts'), 'group_edit_own_posts', $group_edit_own_posts);
				$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_edit_limit'), 'group_edit_limit', $group_edit_limit, $language -> getString( 'acp_users_subsection_users_groups_new_group_edit_limit_help'));
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_draw_edit_legend'), 'group_draw_edit_legend', $group_draw_edit_legend);
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_avoid_flood'), 'group_avoid_flood', $group_avoid_flood);
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_avoid_closed_topics'), 'group_avoid_closed_topics', $group_avoid_closed_topics);
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_start_surveys'), 'group_start_surveys', $group_start_surveys);
				$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_vote_surveys'), 'group_vote_surveys', $group_vote_surveys);
				
				$new_group_form -> closeTable();
				$new_group_form -> drawSpacer( $language -> getString( 'acp_users_subsection_users_groups_new_group_uploading'));
				$new_group_form -> openOpTable();
					
				$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_uploads_quota'), 'group_uploads_quota', $group_uploads_quota, $language -> getString( 'acp_users_subsection_users_groups_new_group_in_bytes'));
				$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_uploads_limit'), 'group_uploads_limit', $group_uploads_limit, $language -> getString( 'acp_users_subsection_users_groups_new_group_in_bytes'));
				
				$new_group_form -> closeTable();
				
				if ( $group_to_edit != 1){
				
					$new_group_form -> drawSpacer( $language -> getString( 'acp_users_subsection_users_groups_new_group_special_access'));
					$new_group_form -> openOpTable();
					
					$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_use_acp'), 'group_can_use_acp', $group_can_use_acp);
					$new_group_form -> drawYesNo( $language -> getString( 'acp_users_subsection_users_groups_new_group_can_moderate'), 'group_can_moderate', $group_can_moderate);
					
					$new_group_form -> closeTable();
					$new_group_form -> drawSpacer( $language -> getString( 'acp_users_subsection_users_groups_new_group_autopromote'));
					$new_group_form -> openOpTable();
										
					$groups_query = $mysql -> query( "SELECT * FROM users_groups WHERE `users_group_id` <> '1' AND `users_group_id` <> '$group_to_edit' ORDER BY `users_group_name`");
					while ( $groups_result = mysql_fetch_array( $groups_query, MYSQL_ASSOC)){
						
						$groups_result = $mysql -> clear( $groups_result);
						
						$groups_list[ $groups_result['users_group_id']] = $groups_result['users_group_name'];
						
					}
					
					$new_group_form -> drawList( $language -> getString( 'acp_users_subsection_users_groups_new_group_promote_to'), 'group_promote_to', $groups_list, $group_promote_to);
					
					$new_group_form -> drawTextInput( $language -> getString( 'acp_users_subsection_users_groups_new_group_promote_at'), 'group_promote_at', $group_promote_at, $language -> getString( 'acp_users_subsection_users_groups_new_group_promote_at_help'));
						
					$new_group_form -> closeTable();
					
				}
				
				
				$new_group_form -> drawButton( $language -> getString( 'acp_users_subsection_users_groups_edit_group_button'));
				$new_group_form -> closeForm();
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_users_groups_edit_group'), $new_group_form -> display()));
				
			}else{
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_edit_group'), $language -> getString( 'acp_users_subsection_users_groups_edit_group_nofound')));
				
				$this -> act_users_groups();
				
			}
			
		}else{
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_edit_group'), $language -> getString( 'acp_users_subsection_users_groups_edit_group_notarget')));
			
			$this -> act_users_groups();
			
		}
	}
	
	function act_delete_users_group(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * error checking
		 */
		
		if ( isset( $_GET['group']) && !empty( $_GET['group'])){
			
			/**
			 * get group to delete
			 */
			
			$group_to_delete = $_GET['group'];
			
			settype( $group_to_delete, 'integer');
			
			/**
			 * select group
			 */
			
			$group_query = $mysql -> query( "SELECT * FROM users_groups WHERE `users_group_id` = '$group_to_delete'");
			
			if ( $group_result = mysql_fetch_array( $group_query, MYSQL_ASSOC)){
				
				//clear
				
				$group_result = $mysql -> clear( $group_result);
				
				if ( $group_result['users_group_system']){
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_delete_group'), $language -> getString( 'acp_users_subsection_users_groups_delete_group_system')));
					
					$this -> act_users_groups();
				
				}else{
					
					/**
					 * we can delete group.
					 */
					
					/**
					 * add breadcrumbs
					 */
		
					$path_link = array( 'act' => 'users_groups');
					
					$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
					
					$path_link = array( 'act' => 'delete_users_group', 'group' => $group_to_delete);
					
					$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_users_groups_delete_group'), parent::adminLink( parent::getId(), $path_link));
					
					/**
					 * set page title
					 */
					
					$output -> setTitle( $language -> getString( 'acp_users_subsection_users_groups_delete_group'));
					
					/**
					 * draw form
					 */
					
					$delete_group_link = array( 'act' => 'kill_users_group', 'group' => $group_to_delete);
					
					$delete_group_form = new form();
					$delete_group_form -> openForm( parent::adminLink( parent::getId(), $delete_group_link));
					$delete_group_form -> openOpTable();
					
					$groups_query = $mysql -> query( "SELECT * FROM users_groups WHERE `users_group_id` <> '1' AND `users_group_id` <> '$group_to_delete'");
					
					while ( $groups_result = mysql_fetch_array( $groups_query, MYSQL_ASSOC)){
						
						$groups_result = $mysql -> clear( $groups_result);
					
						$users_groups[$groups_result['users_group_id']] = $groups_result['users_group_name'];
							
					}
										
					$delete_group_form -> drawList( $language -> getString( 'acp_users_subsection_users_groups_delete_group_replace'), 'group_replace', $users_groups);
					
					$delete_group_form -> closeTable();
					$delete_group_form -> drawButton( $language -> getString( 'acp_users_subsection_users_groups_delete_group_button'));
					$delete_group_form -> closeForm();
					
					parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_users_groups_delete_group'), $delete_group_form -> display()));
					
				}
				
			}else{
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_delete_group'), $language -> getString( 'acp_users_subsection_users_groups_delete_group_nofound')));
				
				$this -> act_users_groups();
				
			}
			
		}else{
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_users_groups_delete_group'), $language -> getString( 'acp_users_subsection_users_groups_delete_group_notarget')));
			
			$this -> act_users_groups();
			
		}
		
	}
	
	function act_pfields(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'pfields');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_pfields'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_users_subsection_pfields'));
		
		/**
		 * do actions
		 */
		
		if ( $_GET['d'] == 0){
			
			/**
			 * move it up
			 */
			
			$el_to_move = $_GET['field'];
			
			settype( $el_to_move, 'integer');
			
			/**
			 * select our position
			 */
			
			$query = $mysql -> query( "SELECT `profile_field_pos` FROM profile_fields WHERE `profile_field_id` = '$el_to_move'");			
			
			if($result = mysql_fetch_array( $query, MYSQL_ASSOC)){
				
				$actual_pos = $result['profile_field_pos'];
				
			}	
					
			/**
			 * now position highest than actual
			 */
				
			$query = $mysql -> query( "SELECT `profile_field_id`, `profile_field_pos` FROM profile_fields WHERE `profile_field_pos` < '$actual_pos' ORDER BY `profile_field_pos` LIMIT 1");			
				
			if($result = mysql_fetch_array( $query, MYSQL_ASSOC)){
				
				$next_id = $result['profile_field_id'];
				$next_pos = $result['profile_field_pos'];
				
			}
				
			/**
			 * check, if module is found 
			 */
				
			if( isset($next_id)){
					
				$mysql -> query( "UPDATE profile_fields SET `profile_field_pos` = '$next_pos' WHERE `profile_field_id` = '$el_to_move'");
				$mysql -> query( "UPDATE profile_fields SET `profile_field_pos` = '$actual_pos' WHERE `profile_field_id` = '$next_id'");
					
				$cache -> flushCache( 'profile_fields');
			
			}
			
		}
		
		if ( $_GET['d'] == 1){
			
			/**
			 * move it up
			 */
			
			$el_to_move = $_GET['field'];
			
			settype( $el_to_move, 'integer');
			
			/**
			 * select our position
			 */
			
			$query = $mysql -> query( "SELECT `profile_field_pos` FROM profile_fields WHERE `profile_field_id` = '$el_to_move'");			
			
			if($result = mysql_fetch_array( $query, MYSQL_ASSOC)){
				
				$actual_pos = $result['profile_field_pos'];
				
			}	
					
			/**
			 * now position highest than actual
			 */
				
			$query = $mysql -> query( "SELECT `profile_field_id`, `profile_field_pos` FROM profile_fields WHERE `profile_field_pos` > '$actual_pos' ORDER BY `profile_field_pos` LIMIT 1");			
				
			if($result = mysql_fetch_array( $query, MYSQL_ASSOC)){
				
				$next_id = $result['profile_field_id'];
				$next_pos = $result['profile_field_pos'];
				
			}
				
			/**
			 * check, if module is found 
			 */
				
			if( isset($next_id)){
					
				$mysql -> query( "UPDATE profile_fields SET `profile_field_pos` = '$next_pos' WHERE `profile_field_id` = '$el_to_move'");
				$mysql -> query( "UPDATE profile_fields SET `profile_field_pos` = '$actual_pos' WHERE `profile_field_id` = '$next_id'");
					
				$cache -> flushCache( 'profile_fields');
						
			}
			
		}
		
		/**
		 * open fields table
		 */
		
		$pfields = new form();
		$pfields -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'pfields_new')));
		$pfields -> openOpTable();
		$pfields -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_users_subsection_pfield_list_name').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_pfield_list_type').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_pfield_list_req').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_pfield_list_private').'</th>
			<th>'.$language -> getString( 'actions').'</th>
		</th>');
		
		/**
		 * draw fields
		 */	
		
		$fields_query = $mysql -> query( "SELECT * FROM profile_fields ORDER BY profile_field_pos");
		
		while( $fields_result = mysql_fetch_array( $fields_query, MYSQL_ASSOC)){
			
			//clear
			$fields_result = $mysql -> clear( $fields_result);
			
			/**
			 * add row
			 */
			
			$pfields -> addToContent( '<tr>
				<td class="opt_row1">'.$fields_result['profile_field_name'].'</td>
				<td class="opt_row2">'.$language -> getString( 'acp_users_subsection_pfield_type_'.$fields_result['profile_field_type']).'</td>
				<td class="opt_row1" style="text-align: center">'.$style -> drawThick( $fields_result['profile_field_require'], true).'</td>
				<td class="opt_row2" style="text-align: center">'.$style -> drawThick( $fields_result['profile_field_private'], true).'</td>
				<td class="opt_row3" style="text-align: center" nowrap="nowrap">
				<a href="'.parent::adminLink( parent::getId(), array( 'act' => 'pfields', 'field' => $fields_result['profile_field_id'], 'd' => 0)).'">'.$style -> drawImage( 'go_up', $language -> getString( 'go_up')).'</a>
				<a href="'.parent::adminLink( parent::getId(), array( 'act' => 'pfields', 'field' => $fields_result['profile_field_id'], 'd' => 1)).'">'.$style -> drawImage( 'go_down', $language -> getString( 'go_down')).'</a>
				<a href="'.parent::adminLink( parent::getId(), array( 'act' => 'pfields_edit', 'field' => $fields_result['profile_field_id'])).'">'.$style -> drawImage( 'edit', $language -> getString( 'edit')).'</a>
				<a href="'.parent::adminLink( parent::getId(), array( 'act' => 'pfields_delete', 'field' => $fields_result['profile_field_id'])).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a>
				</td>
			</th>');
			
		}
		
		/**
		 * close and draw table
		 */
		
		$pfields -> closeTable();
		$pfields -> drawButton( $language -> getString( 'acp_users_subsection_pfield_list_add_new'));
		$pfields -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_pfields'), $pfields -> display()));
		
	}
	
	function act_pfields_new(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'pfields');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_pfields'), parent::adminLink( parent::getId(), $path_link));
		
		$path_link = array( 'act' => 'pfields_new');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_pfield_new'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_users_subsection_pfield_new'));
		
		/**
		 * set values
		 */
		
		$pfield_name = '';
		$pfield_info = '';
		$pfield_type = 0;
		$pfield_length = 0;
		$pfield_options = '';
		$pfield_onregister = false;
		$pfield_onlist = false;
		$pfield_inposts = false;
		$pfield_require = false;
		$pfield_private = false;
		$pfield_byteam = false;
		$pfield_display = '<b>{NAME}</b>: {VALUE}';
		
		/**
		 * retake
		 */
		
		if ( $_GET['act'] == 'pfields_add'){
			
			$pfield_name = stripslashes( $strings -> inputClear( $_POST['pfield_name'], false));
			$pfield_info = stripslashes( $strings -> inputClear( $_POST['pfield_info'], false));
			$pfield_type = $_POST['pfield_type'];
			$pfield_length = $_POST['pfield_length'];
			$pfield_options = stripslashes( $strings -> inputClear( $_POST['pfield_options'], false));
			$pfield_onregister = $_POST['pfield_info'];
			$pfield_onlist = $_POST['pfield_onlist'];
			$pfield_inposts = $_POST['pfield_inposts'];
			$pfield_require = $_POST['pfield_require'];
			$pfield_private = $_POST['pfield_private'];
			$pfield_byteam = $_POST['pfield_byteam'];
			$pfield_display = stripslashes( $strings -> inputClear( $_POST['pfield_display'], false));			
			
			settype( $pfield_type, 'integer');
			settype( $pfield_length, 'integer');
			
			if ( $pfield_type < 0)
				$pfield_type = 0;
			
			if ( $pfield_type > 2)
				$pfield_type = 2;
				
			if ( $pfield_length < 0)
				$pfield_length = 0;
				
			settype( $pfield_onregister, 'bool');
			settype( $pfield_onlist, 'bool');
			settype( $pfield_inposts, 'bool');
			settype( $pfield_require, 'bool');
			settype( $pfield_private, 'bool');
			settype( $pfield_byteam, 'bool');
			
		}
		
		/**
		 * draw form
		 */
		
		$new_pfield = new form();
		$new_pfield -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'pfields_add')));
		$new_pfield -> openOpTable();
		
		$new_pfield -> drawTextInput( $language -> getString( 'acp_users_subsection_pfield_form_name'), 'pfield_name', $pfield_name);
		$new_pfield -> drawTextBox( $language -> getString( 'acp_users_subsection_pfield_form_info'), 'pfield_info', $pfield_info);
		
		$field_types[0] = $language -> getString( 'acp_users_subsection_pfield_type_0');
		$field_types[1] = $language -> getString( 'acp_users_subsection_pfield_type_1');
		$field_types[2] = $language -> getString( 'acp_users_subsection_pfield_type_2');
		
		$new_pfield -> drawList( $language -> getString( 'acp_users_subsection_pfield_form_type'), 'pfield_type', $field_types, $pfield_type);
		$new_pfield -> drawTextInput( $language -> getString( 'acp_users_subsection_pfield_form_length'), 'pfield_length', $pfield_length, $language -> getString( 'acp_users_subsection_pfield_form_length_help'));
		$new_pfield -> drawTextBox( $language -> getString( 'acp_users_subsection_pfield_form_options'), 'pfield_options', $pfield_options, $language -> getString( 'acp_users_subsection_pfield_form_options_help'));

		$new_pfield -> drawYesNo( $language -> getString( 'acp_users_subsection_pfield_form_onregister'), 'pfield_onregister', $pfield_onregister);
		$new_pfield -> drawYesNo( $language -> getString( 'acp_users_subsection_pfield_form_onlist'), 'pfield_onlist', $pfield_onlist);
		$new_pfield -> drawYesNo( $language -> getString( 'acp_users_subsection_pfield_form_inposts'), 'pfield_inposts', $pfield_inposts);
		$new_pfield -> drawYesNo( $language -> getString( 'acp_users_subsection_pfield_form_req'), 'pfield_require', $pfield_require);
		$new_pfield -> drawYesNo( $language -> getString( 'acp_users_subsection_pfield_form_private'), 'pfield_private', $pfield_private);
		$new_pfield -> drawYesNo( $language -> getString( 'acp_users_subsection_pfield_form_byteam'), 'pfield_byteam', $pfield_byteam);
		
		$new_pfield -> drawTextBox( $language -> getString( 'acp_users_subsection_pfield_form_display'), 'pfield_display', $pfield_display, $language -> getString('acp_users_subsection_pfield_form_display_help'));
		
		$new_pfield -> closeTable();
		$new_pfield -> drawButton( $language -> getString( 'acp_users_subsection_pfield_list_add_new'));
		$new_pfield -> closeForm();
		
		parent::draw( $style  -> drawFormBlock( $language -> getString( 'acp_users_subsection_pfield_new'), $new_pfield -> display()));
		
	}	
	
	function act_pfields_edit(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * select field
		 */
		
		$field_to_edit = $_GET['field'];
		
		settype( $field_to_edit, 'integer');
		
		$field_query = $mysql -> query( "SELECT * FROM `profile_fields` WHERE `profile_field_id` = '$field_to_edit'");
		
		if ( $field_result = mysql_fetch_array( $field_query, MYSQL_ASSOC)){
		
			/**
			 * add breadcrumbs
			 */
			
			$path_link = array( 'act' => 'pfields');
			
			$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
			$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_pfields'), parent::adminLink( parent::getId(), $path_link));
			
			$path_link = array( 'act' => 'pfields_new', 'field' => $field_to_edit);
			
			$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_pfield_edit'), parent::adminLink( parent::getId(), $path_link));
			
			/**
			 * set page title
			 */
			
			$output -> setTitle( $language -> getString( 'acp_users_subsection_pfield_edit'));
			
			/**
			 * set values
			 */
			
			$pfield_name = $field_result['profile_field_name'];
			$pfield_info = $field_result['profile_field_info'];
			$pfield_type = $field_result['profile_field_type'];
			$pfield_length = $field_result['profile_field_length'];
			$pfield_options = $field_result['profile_field_options'];
			$pfield_onregister = $field_result['profile_field_onregister'];
			$pfield_onlist = $field_result['profile_field_onlist'];
			$pfield_inposts = $field_result['profile_field_inposts'];
			$pfield_require = $field_result['profile_field_require'];
			$pfield_private = $field_result['profile_field_private'];
			$pfield_byteam = $field_result['profile_field_byteam'];
			$pfield_display = $field_result['profile_field_display'];
			
			/**
			 * retake
			 */
			
			if ( $_GET['act'] == 'pfields_add'){
				
				$pfield_name = stripslashes( $strings -> inputClear( $_POST['pfield_name'], false));
				$pfield_info = stripslashes( $strings -> inputClear( $_POST['pfield_info'], false));
				$pfield_type = $_POST['pfield_type'];
				$pfield_length = $_POST['pfield_length'];
				$pfield_options = stripslashes( $strings -> inputClear( $_POST['pfield_options'], false));
				$pfield_onregister = $_POST['pfield_info'];
				$pfield_onlist = $_POST['pfield_onlist'];
				$pfield_inposts = $_POST['pfield_inposts'];
				$pfield_require = $_POST['pfield_require'];
				$pfield_private = $_POST['pfield_private'];
				$pfield_byteam = $_POST['pfield_byteam'];
				$pfield_display = stripslashes( $strings -> inputClear( $_POST['pfield_display'], false));			
				
				settype( $pfield_type, 'integer');
				settype( $pfield_length, 'integer');
				
				if ( $pfield_type < 0)
					$pfield_type = 0;
				
				if ( $pfield_type > 2)
					$pfield_type = 2;
					
				if ( $pfield_length < 0)
					$pfield_length = 0;
					
				settype( $pfield_onregister, 'bool');
				settype( $pfield_onlist, 'bool');
				settype( $pfield_inposts, 'bool');
				settype( $pfield_require, 'bool');
				settype( $pfield_private, 'bool');
				settype( $pfield_byteam, 'bool');
				
			}
			
			/**
			 * draw form
			 */
			
			$new_pfield = new form();
			$new_pfield -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'pfields_change', 'field' => $field_to_edit)));
			$new_pfield -> openOpTable();
			
			$new_pfield -> drawTextInput( $language -> getString( 'acp_users_subsection_pfield_form_name'), 'pfield_name', $pfield_name);
			$new_pfield -> drawTextBox( $language -> getString( 'acp_users_subsection_pfield_form_info'), 'pfield_info', $pfield_info);
			
			$field_types[0] = $language -> getString( 'acp_users_subsection_pfield_type_0');
			$field_types[1] = $language -> getString( 'acp_users_subsection_pfield_type_1');
			$field_types[2] = $language -> getString( 'acp_users_subsection_pfield_type_2');
			
			$new_pfield -> drawList( $language -> getString( 'acp_users_subsection_pfield_form_type'), 'pfield_type', $field_types, $pfield_type);
			$new_pfield -> drawTextInput( $language -> getString( 'acp_users_subsection_pfield_form_length'), 'pfield_length', $pfield_length, $language -> getString( 'acp_users_subsection_pfield_form_length_help'));
			$new_pfield -> drawTextBox( $language -> getString( 'acp_users_subsection_pfield_form_options'), 'pfield_options', $pfield_options, $language -> getString( 'acp_users_subsection_pfield_form_options_help'));
	
			$new_pfield -> drawYesNo( $language -> getString( 'acp_users_subsection_pfield_form_onregister'), 'pfield_onregister', $pfield_onregister);
			$new_pfield -> drawYesNo( $language -> getString( 'acp_users_subsection_pfield_form_onlist'), 'pfield_onlist', $pfield_onlist);
			$new_pfield -> drawYesNo( $language -> getString( 'acp_users_subsection_pfield_form_inposts'), 'pfield_inposts', $pfield_inposts);
			$new_pfield -> drawYesNo( $language -> getString( 'acp_users_subsection_pfield_form_req'), 'pfield_require', $pfield_require);
			$new_pfield -> drawYesNo( $language -> getString( 'acp_users_subsection_pfield_form_private'), 'pfield_private', $pfield_private);
			$new_pfield -> drawYesNo( $language -> getString( 'acp_users_subsection_pfield_form_byteam'), 'pfield_byteam', $pfield_byteam);
			
			$new_pfield -> drawTextBox( $language -> getString( 'acp_users_subsection_pfield_form_display'), 'pfield_display', $pfield_display, $language -> getString('acp_users_subsection_pfield_form_display_help'));
			
			$new_pfield -> closeTable();
			$new_pfield -> drawButton( $language -> getString( 'acp_users_subsection_pfield_list_edit'));
			$new_pfield -> closeForm();
			
			parent::draw( $style  -> drawFormBlock( $language -> getString( 'acp_users_subsection_pfield_edit'), $new_pfield -> display()));
			
		}else{
			
			/**
			 * not found
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_pfield_edit'), $language -> getString( 'acp_users_subsection_pfield_edit_notfound')));
			
			$this -> act_pfields();
		
		}
			
	}	
	
	function act_pfields_delete(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * get field id
		 */
		
		$field_id = $_GET['field'];
		settype( $field_id, 'integer');
		
		$field_query = $mysql -> query( "SELECT * FROM profile_fields WHERE `profile_field_id` = '$field_id'");
		
		if ( $field_result = mysql_fetch_array( $field_query, MYSQL_ASSOC)){
			
			$mysql -> delete( "profile_fields", "`profile_field_id` = '$field_id'");
			$mysql -> query( "ALTER TABLE `profile_fields_data` DROP `field_$field_id`");
			
			$cache -> flushCache( 'profile_fields');
						
			/**
			 * add log
			 */
			
			$logs -> addAdminLog( $language -> getString( 'acp_users_subsection_pfield_delete_log', array( 'field_id' => $field_id)));
			
			/**
			 * draw message
			 */
			
			parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_pfield_delete'), $language -> getString( 'acp_users_subsection_pfield_delete_done')));
			
		}
		
		/**
		 * jump to manager
		 */
		
		$this -> act_pfields();
		
	}
	
	function act_users_groups(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'users_groups');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_users_groups'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_users_subsection_users_groups'));
		
		/**
		 * get list of groups
		 */
		
		$new_group_link = array( 'act' => 'new_users_group');
		
		$existing_groups_list = new form();
		$existing_groups_list -> openForm( parent::adminLink( parent::getId(), $new_group_link));
		$existing_groups_list -> openOpTable();
		$existing_groups_list -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_users_subsection_users_groups_list_name').'</th>
			<th NOWRAP>'.$language -> getString( 'acp_users_subsection_users_groups_list_mod').'</th>
			<th NOWRAP>'.$language -> getString( 'acp_users_subsection_users_groups_list_admin').'</th>
			<th NOWRAP>'.$language -> getString( 'actions').'</th>
		</tr>');
		
		/**
		 * add items
		 */
		
		$groups_query = $mysql -> query( "SELECT * FROM users_groups ORDER BY users_group_can_use_acp DESC, users_group_can_moderate DESC, users_group_name");
		
		while ( $groups_result = mysql_fetch_array( $groups_query, MYSQL_ASSOC)){
			
			//clear result
			$groups_result = $mysql -> clear( $groups_result);

			/**
			 * actions
			 */
			
			$edit_group_id = array( 'act' => 'edit_users_group', 'group' => $groups_result['users_group_id']);
			
			$actions = '<a href="'.parent::adminLink( parent::getId(), $edit_group_id).'">'.$style -> drawImage( 'edit', $language -> getString( 'edit')).'</a>';
			
			$delete_group_id = array( 'act' => 'delete_users_group', 'group' => $groups_result['users_group_id']);
			
			if ( !$groups_result['users_group_system']){
				$actions .= '<a href="'.parent::adminLink( parent::getId(), $delete_group_id).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a>';
			}
			
			/**
			 * add row
			 */
			
			$existing_groups_list -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%">'.$groups_result['users_group_prefix'].$groups_result['users_group_name'].$groups_result['users_group_suffix'].'</td>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$style  -> drawThick( $groups_result['users_group_can_moderate'], true).'</td>
				<td class="opt_row1" style="text-align: center" NOWRAP>'.$style  -> drawThick( $groups_result['users_group_can_use_acp'], true).'</td>
				<td class="opt_row3" style="text-align: center" NOWRAP>'.$actions.'</td>
			</tr>');
			
		}
		
		/**
		 * close table
		 */
		
		$existing_groups_list -> closeTable();
		$existing_groups_list -> drawButton( $language -> getString( 'acp_users_subsection_users_groups_new_group'));
		$existing_groups_list -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_users_groups'), $existing_groups_list -> display()));
		
	}
	
	function act_users_notactive(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'users_notactive');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_users_notactive'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_users_subsection_users_notactive'));
		
		/**
		 * activate user
		 */
		
		if ( $_GET['do'] == 'activate_user'){
			
			$user_to_activate = $_GET['user'];
			
			settype( $user_to_activate, 'integer');
			
			if ( $user_to_activate > 0){
				
				/**
				 * update
				 */
				
				$user_activate_sql['user_active'] = true;
				
				$mysql -> update( $user_activate_sql, 'users', "`user_id` = '$user_to_activate'");
				
				/**
				 * draw message
				 */
				
				parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_users_notactive_user_activation'), $language -> getString( 'acp_users_subsection_users_notactive_user_activation_done')));
				
			}
			
		}
		
		/**
		 * draw form
		 */
		
		$inactive_users_form = new form();
		$inactive_users_form -> openOpTable();
		
		$inactive_users_form -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_users_subsection_users_notactive_user').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_users_notactive_mail').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_users_notactive_regdate').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_users_notactive_activate').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_users_notactive_delete').'</th>
		</tr>');
		
		$inactive_users_query = $mysql -> query( "SELECT u.*, g.* FROM users u LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE u.user_active = 0 AND u.user_id <> -1");
		
		while ( $users_result = mysql_fetch_array( $inactive_users_query, MYSQL_ASSOC)){
			
			$users_result = $mysql -> clear( $users_result);
			
			/**
			 * links
			 */
			
			$activate_user_link = array( 'act' => 'users_notactive', 'do' => 'activate_user', 'user' => $users_result['user_id']);
			$delete_user_link = array( 'act' => 'user_delete', 'user' => $users_result['user_id']);
			
			/**
			 * add row
			 */
			
			$inactive_users_form -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%">'.$users_result['users_group_prefix'].$users_result['user_login'].$users_result['users_group_suffix'].'</td>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$users_result['user_mail'].'</td>
				<td class="opt_row1" style="text-align: center" NOWRAP>'.$time -> drawDate( $users_result['user_regdate']).'</td>
				<td class="opt_row3" style="text-align: center" NOWRAP><a href="'.parent::adminLink( parent::getId(), $activate_user_link).'">'.$language -> getString( 'acp_users_subsection_users_notactive_activate').'</a></td>
				<td class="opt_row3" style="text-align: center" NOWRAP><a href="'.parent::adminLink( parent::getId(), $delete_user_link).'">'.$language -> getString( 'acp_users_subsection_users_notactive_delete').'</a></td>
			</tr>');
		
			
		}
		
		$inactive_users_form -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_users_notactive'), $inactive_users_form -> display()));
		
	}
	
	function act_users_banned(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'users_banned');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_section_members'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_users_banned'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_users_subsection_users_banned'));
			
		/**
		 * draw form
		 */
		
		$inactive_users_form = new form();
		$inactive_users_form -> openOpTable();
		
		$inactive_users_form -> addToContent( '<tr>
			<th NOWRAP>'.$language -> getString( 'acp_users_subsection_users_banned_user').'</th>
			<th NOWRAP>'.$language -> getString( 'acp_users_subsection_users_banned_user_mail').'</th>
			<th NOWRAP>'.$language -> getString( 'acp_users_subsection_users_banned_unban').'</th>
			<th NOWRAP>'.$language -> getString( 'acp_users_subsection_users_banned_delete').'</th>
		</tr>');
		
		$inactive_users_query = $mysql -> query( "SELECT u.*, g.* FROM users u LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE u.user_locked = 1 AND u.user_id <> -1");
		
		while ( $users_result = mysql_fetch_array( $inactive_users_query, MYSQL_ASSOC)){
			
			$users_result = $mysql -> clear( $users_result);
			
			/**
			 * links
			 */
			
			$unban_user_link = array( 'act' => 'find_user', 'do' => 'unban_user', 'user' => $users_result['user_id']);
			$delete_user_link = array( 'act' => 'user_delete', 'user' => $users_result['user_id']);
			
			/**
			 * add row
			 */
			
			$inactive_users_form -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%">'.$users_result['users_group_prefix'].$users_result['user_login'].$users_result['users_group_suffix'].'</td>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$users_result['user_mail'].'</td>
				<td class="opt_row3" style="text-align: center" NOWRAP><a href="'.parent::adminLink( parent::getId(), $unban_user_link).'">'.$language -> getString( 'acp_users_subsection_users_banned_unban').'</a></td>
				<td class="opt_row3" style="text-align: center" NOWRAP><a href="'.parent::adminLink( parent::getId(), $delete_user_link).'">'.$language -> getString( 'acp_users_subsection_users_banned_delete').'</a></td>
			</tr>');
		
			
		}
		
		$inactive_users_form -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_users_banned'), $inactive_users_form -> display()));
	
	}
		
	function act_bad_words(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'bad_words');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_section_unwanted_content'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_bad_words'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_users_subsection_bad_words'));
		
		/**
		 * action
		 */
		
		if ( $_GET['do'] == 'new_word' && $session -> checkForm()){
			
			/**
			 * new word
			 */
			
			$new_bad_word_word = $strings -> inputClear( $_POST['new_bad_word_word'], false);
			$new_bad_word_replace = $strings -> inputClear( $_POST['new_bad_word_replace'], false);
			
			if ( strlen( $new_bad_word_word) == 0){
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_bad_words_new_word'), $language -> getString( 'acp_users_subsection_bad_words_new_word_empty_word')));
				
			}else if ( strlen( $new_bad_word_replace) == 0){
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_users_subsection_bad_words_new_word'), $language -> getString( 'acp_users_subsection_bad_words_new_word_empty_replace')));
				
			}else{
				
				/**
				 * mysql
				 */
				
				$new_badword_sql['badword_find'] = $new_bad_word_word;
				$new_badword_sql['badword_replace'] = $new_bad_word_replace;
				
				$mysql -> insert( $new_badword_sql, 'badwords');
				
				/**
				 * log
				 */
				
				$log_keys = array( 'new_bad_word' => $new_bad_word_word);
				$logs -> addAdminLog( $language -> getString( 'acp_users_subsection_bad_words_new_word_log'), $log_keys);
				
				/**
				 * message
				 */
				
				parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_bad_words_new_word'), $language -> getString( 'acp_users_subsection_bad_words_new_word_done')));
				
				/**
				 * clear word
				 */
				
				$new_bad_word_word = '';
				$new_bad_word_replace = '';
				
			}
			
			$cache -> flushCache( 'badwords');
			
		}
		
		/**
		 * update
		 */
		
		if ( $_GET['do'] == 'update' && $session -> checkForm()){
		
			/**
			 * build up an list of emoticons to add
			 */
			
			$badword_find = $_POST['badword_find'];
			settype( $badword_find, 'array');
			
			$badword_replace = $_POST['badword_replace'];
			settype( $badword_replace, 'array');
			
			$badword_delete = $_POST['delete_word'];
			settype( $badword_delete, 'array');
			
			/**
			 * start from deleteing
			 */
			
			$deleted_badwords = array();
			
			foreach ( $badword_delete as $badword_id => $badword_delete){
				
				settype( $badword_id, 'integer');
				
				$mysql -> delete( 'badwords', "`badword_id` = '$badword_id'");
				
				$deleted_badwords[] = $badword_id;
				
			}
			
			/**
			 * update words
			 */
			
			foreach ( $badword_find as $badword_id => $badword_find_text){
				
				settype( $badword_id, 'integer');
				
				if ( !in_array( $badword_id, $deleted_badwords)){
			
					$new_badword_word = $strings -> inputClear( $badword_find_text, false);
					$new_badword_replace = $strings -> inputClear( $badword_replace[$badword_id], false);
					
					if ( strlen( $new_badword_word) != 0 && strlen( $new_badword_replace) != 0){
					
						$badword_change_sql['badword_find'] = $new_badword_word;
						$badword_change_sql['badword_replace'] = $new_badword_replace;
						
						$mysql -> update( $badword_change_sql, 'badwords', "`badword_id` = '$badword_id'");
					
					}
				}
				
			}
			
			$cache -> flushCache( 'badwords');
			
		}
		
		/**
		 * list of existing badwords
		 */
		
		$update_badwords_link = array( 'act' => 'bad_words', 'do' => 'update');
		
		$badwords_list = new form();
		$badwords_list -> openForm( parent::adminLink( parent::getId(), $update_badwords_link));
		$badwords_list -> openOpTable();
		$badwords_list -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_users_subsection_bad_words_list_word').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_bad_words_list_replace').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_bad_words_list_delete').'</th>
		</tr>');
		
		$words_query = $mysql -> query( "SELECT * FROM badwords ORDER BY badword_find");
		
		while ( $words_result = mysql_fetch_array( $words_query, MYSQL_ASSOC)){
			
			//clear
			$words_result = $mysql -> clear( $words_result);
			
			/**
			 * insert row
			 */
			
			$badwords_list -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%"><input type="text" name="badword_find['.$words_result['badword_id'].']" value="'.$words_result['badword_find'].'" /></td>
				<td class="opt_row2" style="text-align: center" NOWRAP><input type="text" name="badword_replace['.$words_result['badword_id'].']" value="'.$words_result['badword_replace'].'" /></td>
				<td class="opt_row3" style="text-align: center" NOWRAP>'.$badwords_list -> drawSelect( 'delete_word['.$words_result['badword_id'].']').'</td>
			</tr>');
			
		}
		
		$badwords_list -> closeTable();
		$badwords_list -> drawButton( $language -> getString( 'acp_users_subsection_bad_words_list_update_button'));
		$badwords_list -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_bad_words_list'), $badwords_list -> display()));
		
		/**
		 * new word filter
		 */
		
		$new_badword_link = array( 'act' => 'bad_words', 'do' => 'new_word');
		
		$new_badword_form = new form();
		$new_badword_form -> openForm( parent::adminLink( parent::getId(), $new_badword_link));
		$new_badword_form -> openOpTable();
		
		$new_badword_form -> drawTextInput( $language -> getString( 'acp_users_subsection_bad_words_new_word_word'), 'new_bad_word_word', $new_bad_word_word);
		$new_badword_form -> drawTextInput( $language -> getString( 'acp_users_subsection_bad_words_new_word_replace'), 'new_bad_word_replace', $new_bad_word_replace);
		
		$new_badword_form -> closeTable();
		$new_badword_form -> drawButton( $language -> getString( 'acp_users_subsection_bad_words_new_word_button'));
		$new_badword_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_bad_words_new_word'), $new_badword_form -> display()));
		
	}
	
	function act_autobanning(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'bad_words');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_section_unwanted_content'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_users_subsection_autobanning'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_users_subsection_autobanning'));

		/**
		 * add new filter
		 */
		
		if ( $_GET['do'] == 'new_filter' && $session -> checkForm()){
			
			$new_filter_text = $strings -> inputClear( $_POST['new_filter_content'], false);
			
			$new_filter_type = $_POST['new_filter_type'];
			
			settype( $new_filter_type, 'integer');
			
			if ( $new_filter_type >= 0 && $new_filter_type <= 2 && strlen( $new_filter_text) > 0){
				
				$new_filter_sql[ 'banfilter_type'] = $new_filter_type;
				$new_filter_sql[ 'banfilter_filter'] = $new_filter_text;
				
				$mysql -> insert( $new_filter_sql, 'banfilters');
				
				/**
				 * clear cache
				 */
				
				$cache -> flushCache( 'banfilters');
			
				/**
				 * add log
				 */
				
				$logs -> addAdminLog( 'acp_users_subsection_autobanning_new_filter_log');
				
				/**
				 * draw message
				 */
					
				parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_autobanning_new_filter'), $language -> getString( 'acp_users_subsection_autobanning_new_filter_done')));
				
			}
			
		}
		
		if ( $_GET['do'] == 'update_filters' && $session -> checkForm()){
			
			$filters_to_delete = $_POST['delete_filter'];
			
			settype( $filters_to_delete, 'array');
			
			foreach ( $filters_to_delete as $delete_filter_id => $delete_filter){
				
				settype( $delete_filter_id, 'integer');

				$mysql -> delete( 'banfilters', "`banfilter_id` = '$delete_filter_id'");			
				
				
				$filters_changed = true;
			}
			
			if ( isset( $filters_changed)){
				
				/**
				 * clear cache
				 */
				
				$cache -> flushCache( 'banfilters');
			
				/**
				 * add log
				 */
				
				$logs -> addAdminLog( 'acp_users_subsection_autobanning_delete_filter_log');
				
				/**
				 * draw message
				 */
					
				parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_users_subsection_autobanning_delete_filters'), $language -> getString( 'acp_users_subsection_autobanning_delete_filter_done')));
				
			}
		}
		
		/**
		 * and filters list
		 */
		
		$filters_update_link = array( 'act' => 'autobanning', 'do' => 'update_filters');
		
		$filters_list = new form();		
		$filters_list -> openForm( parent::adminLink( parent::getId(), $filters_update_link));
		$filters_list -> drawSpacer( $language -> getString( 'acp_users_subsection_autobanning_ip'));
		$filters_list -> openOpTable();
		$filters_list -> addToContent('<tr>
			<th>'.$language -> getString( 'acp_users_subsection_autobanning_list_filter').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_autobanning_list_delete').'</th>
		</tr>');
		
		/**
		 * seletect filters
		 */
		
		$filters_query = $mysql -> query( "SELECT * FROM banfilters ORDER BY `banfilter_filter`");
		
		while ( $filters_result = mysql_fetch_array( $filters_query, MYSQL_ASSOC)) {
			
			//clear result
			$filters_result = $mysql -> clear( $filters_result);
			
			$filters_list_part[$filters_result['banfilter_type']] .= '<tr>
				<td class="opt_row1" style="width: 100%">'.$filters_result['banfilter_filter'].'</td>
				<td class="opt_row3" style="text-align: center">'.$filters_list -> drawSelect( 'delete_filter['.$filters_result['banfilter_id'].']').'</td>
			</tr>';
			
		}
		
		$filters_list -> addToContent( $filters_list_part[0]);
		
		$filters_list -> closeTable();
		$filters_list -> drawSpacer( $language -> getString( 'acp_users_subsection_autobanning_logins'));
		$filters_list -> openOpTable();
		$filters_list -> addToContent('<tr>
			<th>'.$language -> getString( 'acp_users_subsection_autobanning_list_filter').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_autobanning_list_delete').'</th>
		</tr>');
		
		$filters_list -> addToContent( $filters_list_part[1]);
		
		$filters_list -> closeTable();
		$filters_list -> drawSpacer( $language -> getString( 'acp_users_subsection_autobanning_mails'));
		$filters_list -> openOpTable();
		$filters_list -> addToContent('<tr>
			<th>'.$language -> getString( 'acp_users_subsection_autobanning_list_filter').'</th>
			<th>'.$language -> getString( 'acp_users_subsection_autobanning_list_delete').'</th>
		</tr>');
		
		$filters_list -> addToContent( $filters_list_part[2]);
		
		$filters_list -> closeTable();
		$filters_list -> drawButton( $language -> getString( 'acp_users_subsection_autobanning_delete_selected'));
		$filters_list -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_autobanning'), $filters_list -> display()));
	
		/**
		 * new filter form
		 */
		
		$new_filter_link = array( 'act' => 'autobanning', 'do' => 'new_filter');
		
		$new_filter_form = new form();
		$new_filter_form -> openForm( parent::adminLink( parent::getId(), $new_filter_link));
		$new_filter_form -> openOpTable();
		
		$new_filter_form -> drawTextInput( $language -> getString( 'acp_users_subsection_autobanning_new_filter_content'), 'new_filter_content', '', $language -> getString( 'acp_users_subsection_autobanning_new_filter_content_help'));
		
		$filter_types[0] = $language -> getString( 'acp_users_subsection_autobanning_new_filter_type_0');
		$filter_types[1] = $language -> getString( 'acp_users_subsection_autobanning_new_filter_type_1');
		$filter_types[2] = $language -> getString( 'acp_users_subsection_autobanning_new_filter_type_2');
		
		$new_filter_form -> drawList( $language -> getString( 'acp_users_subsection_autobanning_new_filter_type'), 'new_filter_type', $filter_types);
		
		$new_filter_form -> closeTable();
		$new_filter_form -> drawButton( $language -> getString( 'acp_users_subsection_autobanning_new_filter_button'));
		$new_filter_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_users_subsection_autobanning_new_filter'), $new_filter_form -> display()));
	}
	
}
	
?>