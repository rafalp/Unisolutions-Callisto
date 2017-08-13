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
|	User registration
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

class action_register extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
				
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'register_on_board'));
		
		/**
		 * and add breadcrumb
		 */
		
		$path -> addBreadcrumb( $language -> getString( 'register_on_board'), parent::systemLink( parent::getId()));
		
		/**
		 * check if user is logged
		 */
		
		if ( $session -> user['user_id'] == -1){
			
			/**
			 * check perms
			 */
			
			if ( $settings['users_allow_register']){
					
				/**
				 * get page to draw
				 */
				
				if ( $_GET['step'] == 3){
					
					if ( isset( $_POST['form_id'])){
					
						/**
						 * form send
						 * check refreshs
						 */
						
						if( $session -> checkForm()){
							
							/**
							 * get vaules
							 */
							
							$user_login = "";
							$user_pass = "";
							$user_pass_rep = "";
							$user_mail = "";
							
							/**
							 * login
							 */
							
							$user_login = uniSlashes(htmlspecialchars(trim($_POST['user_login'])));
							$user_clear_login = trim($_POST['user_login']);
							
							/**
							 * user_pass
							 */
							
							$pass_piece = trim( $_POST['user_pass']);
							
							$user_pass = md5(md5($pass_piece).md5($pass_piece));
		
							/**
							 * pass repeat
							 */
							
							$user_pass_rep_piece = trim( $_POST['user_pass_rep']);
							
							$user_pass_rep = md5(md5($user_pass_rep_piece).md5($user_pass_rep_piece));
							
							/**
							 * user mail
							 */
							
							$user_mail = $strings -> inputClear( $_POST['user_mail'], false);
							
							/**
							 * check login avaibility
							 */

							$login_free = true;
								
							if($mysql -> countRows( "users", "`user_login` LIKE '".$user_login."' AND `user_id` > '-1'") != 0)
								$login_free = false;
									
							/**
							 * check mail avaibility
							 */
							
							$mail_free = true;
												
							if($mysql -> countRows( "users", "`user_mail` LIKE '".$user_mail."' AND `user_id` > '-1'") != 0 && $settings['users_allow_mail_reuse'] == false)
								$mail_free = false;
								
							/**
							 * now start
							 */
								
							if ( strlen( $user_login) == 0){
								
								/**
								 * login empty
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'information'), $language -> getString( 'register_on_board_login_empty')));
								
								$this -> drawForm();
								
							}else if ( !$login_free){
								
								/**
								 * login taken
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'information'), $language -> getString( 'register_on_board_login_taken')));
								
								$this -> drawForm();
								
							}else if ( $settings['user_login_length_min'] > strlen( $user_clear_login)){
								
								/**
								 * login taken
								 */
								
								$language -> setKey( 'login_length', $settings['user_login_length_min']);
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'information'), $language -> getString( 'register_on_board_login_tooshort')));
								
								$this -> drawForm();
								
							}else if ( $settings['user_login_length_max'] < strlen( $user_clear_login) && $settings['user_login_length_max'] <= 50){
								
								/**
								 * login taken
								 */
								
								$language -> setKey( 'login_length', $settings['user_login_length_max']);
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'information'), $language -> getString( 'register_on_board_login_toolong')));
								
								$this -> drawForm();
								
							}else if ( strlen( $pass_piece) == 0){
								
								/**
								 * pass empty
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'information'), $language -> getString( 'register_on_board_pass_empty')));
								
								$this -> drawForm();
								
							}else if ( strlen( $user_pass_rep_piece) == 0){
								
								/**
								 * pass rep empty
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'information'), $language -> getString( 'register_on_board_pass_rep_empty')));
								
								$this -> drawForm();
								
							}else if ( $user_pass != $user_pass_rep){
								
								/**
								 * no match in passes
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'information'), $language -> getString( 'register_on_board_pass_nomatch')));
								
								$this -> drawForm();
								
							}else if ( empty($user_mail)){
								
								/**
								 * empty mail
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'information'), $language -> getString( 'register_on_board_mail_empty')));
								
								$this -> drawForm();
								
							}else if ( !$mail_free){
								
								/**
								 * mail taken
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'information'), $language -> getString( 'register_on_board_mail_taken')));
								
								$this -> drawForm();
								
							}else if ( !$captcha -> check( $_POST['captcha'])){
								
								/**
								 * mail taken
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'information'), $captcha -> getError()));
								
								$this -> drawForm();
								
							}else{
								
								$proceed = true;
								$additional_fields = array();
								
								/**
								 * check fields
								 */
					
								if ( count( $users -> custom_fields) > 0){
									
									foreach ( $users -> custom_fields as $field_id => $field_ops){
						
										if( $proceed){
											
											if ( $field_ops['profile_field_onregister']){
												
												$new_fiel_vaule = $_POST['field_'.$field_id];
												
												/**
												 * check if it is empty
												 */
												
												if ( strlen( $new_field_value) == 0 && $field_ops['profile_field_require']){
													
													$proceed = false;
													
													$language -> setKey( 'field_name', $field_ops['profile_field_name']);
													
													parent::draw( $style -> drawErrorBlock( $language -> getString( 'information'), $language -> getString( 'user_cp_need_error')));
										
												}else{
													
													if ( strlen( $new_field_value) > $field_ops['profile_field_length'] && $field_ops['profile_field_length'] > 0){
														
														$language -> setKey( 'field_name', $field_ops['profile_field_name']);
														$language -> setKey( 'field_length', $field_ops['profile_field_length']);
														
														parent::draw( $style -> drawErrorBlock( $language -> getString( 'information'), $language -> getString( 'user_cp_need_error')));
														
														if ( $field_ops['profile_field_require'])
															$proceed = false;
																		
													}else{
														
														$new_field_value = $strings -> inputClear( $new_field_value, false);
														
														$additional_fields[ 'field_'.$field_id] = $new_field_value;
														
													}
													
												}
																								
											}
											
										}
										
									}
									
								}
								
								if ( $proceed){
								
									/**
									 * all is okey
									 */
									
									$new_user_sql[ 'user_login'] = $user_login;
									$new_user_sql[ 'user_password'] = $user_pass;
									$new_user_sql[ 'user_mail'] = $user_mail;
									$new_user_sql[ 'user_regdate'] = time();
									$new_user_sql[ 'user_time_zone'] = $settings['time_timezone'];
									$new_user_sql[ 'user_dst'] = $settings['time_dst'];
									$new_user_sql[ 'user_lang'] = $session -> user['user_lang'];
									$new_user_sql[ 'user_style'] = $session -> user['user_style'];
									
									$language -> setKey( 'new_user_login', $user_login);
									$language -> setKey( 'new_user_pass', substr( $pass_piece, 0, 3)."********");
									
									switch ( $settings['user_account_activation']){
										
										case  0:
											
											/**
											 * activate instantly
											 */
											
											$new_user_sql[ 'user_active'] = true;
											$new_user_sql[ 'user_main_group'] = 3;
											
											$mail -> send( $user_mail, $language -> getString( 'register_on_board_mail_title'), $language -> getString( 'register_on_board_mail_1'));
											
											parent::draw( $style -> drawBlock( $language -> getString( 'register_on_board'), $language -> getString( 'register_on_board_done_1')));
											
										break;
										
										case 1:
											
											/**
											 * activate via mail
											 */
											
											$activation_code = substr( md5( time().$session -> user_ip), 0, 12);
											
											$new_user_sql[ 'user_active'] = false;
											$new_user_sql[ 'user_activation_code'] = $activation_code;
											$new_user_sql[ 'user_main_group'] = 4;
											
											$activate_acc_link['code'] = $activation_code;
											
											parent::draw( $style -> drawBlock( $language -> getString( 'register_on_board'), $language -> getString( 'register_on_board_done_2')));
											
										break;
										
										case 2:
											
											/**
											 * activate via admin
											 */
											
											$new_user_sql[ 'user_active'] = false;
											$new_user_sql[ 'user_activation_code'] = 0;
											$new_user_sql[ 'user_main_group'] = 4;
											
											$mail -> send( $user_mail, $language -> getString( 'register_on_board_mail_title'), $language -> getString( 'register_on_board_mail_3'));
											
											parent::draw( $style -> drawBlock( $language -> getString( 'register_on_board'), $language -> getString( 'register_on_board_done_3')));
											
										break;
									}
									
									/**
									 * do insert
									 */
									
									$mysql -> insert( $new_user_sql, 'users');
									
									$new_user_id = mysql_insert_id();
									
									/**
									 * custom fields?
									 */
									
									if ( count( $additional_fields) > 0){
										
										$additional_fields['profile_fields_user'] = $new_user_id;
										
										$mysql -> insert( $additional_fields, 'profile_fields_data');
										
									}
									
									$read_time = time();
				
									/**
									 * set reads
									 */
									
									$forums_reads_sql = array();
									$topics_reads_sql = array();
									
									/**
									 * selet forums
									 */
									
									$forums_query = $mysql -> query( "SELECT forum_id FROM forums");
									
									while ( $forum_result = mysql_fetch_array( $forums_query, MYSQL_ASSOC)){
										
										if ( $session -> canSeeTopics( $forum_result['forum_id']))
											$forums_reads_sql[] = "( ".$forum_result['forum_id'].", ".$read_time.", ".$new_user_id.")";
										
									}
									
									if ( count( $forums_reads_sql) > 0){
										
										$mysql -> query( "INSERT INTO `forums_reads` (`forums_read_forum`, `forums_read_time`, `forums_read_user`) VALUES ".join( ", ", $forums_reads_sql));
										
										/**
										 * select topics
										 */
										
										$topics_query = $mysql -> query( "SELECT topic_id, topic_forum_id FROM topics");
									
										while ( $topics_result = mysql_fetch_array( $topics_query, MYSQL_ASSOC)){
											
											if ( $session -> canSeeTopics( $topics_result['topic_forum_id']))
												$topics_reads_sql[] = "( ".$topics_result['topic_forum_id'].", ".$topics_result['topic_id'].", ".$read_time.", ".$new_user_id.")";
										
										}
									
										if ( count( $topics_reads_sql) > 0){
											
											$mysql -> query( "INSERT INTO `topics_reads` (`topic_read_forum`, `topic_read_topic`, `topic_read_time`, `topic_read_user`) VALUES ".join( ", ", $topics_reads_sql));
											
										}
									}
									
									/**
									 * send mail from here, is its set
									 */
									
									if ($settings['user_account_activation'] == 1){
										
										$language -> setKey( 'activate_accoun_link', $settings['board_address'].'/index.php?act=activate_acc&user='.$new_user_id.'&code='.$activation_code);
										$language -> setKey( 'activate_accoun_title', $language -> getString( 'register_on_board_activate_inmail_link'));
								
										$mail -> send( $user_mail, $language -> getString( 'register_on_board_mail_title'), $language -> getString( 'register_on_board_mail_2'));
																			
									}
									
									/**
									 * update stats
									 */
									
									$mysql -> query("UPDATE settings SET `setting_value` = (setting_value+1) WHERE `setting_setting` = 'users_num'");
								
									/**
									 * flush settings
									 */
								
									$cache -> flushCache('system_settings');
									
								}else{
									
									$this -> drawForm();
									
								}
															
							}
							
						}else{
							
							/**
							 * it is an resend
							 * refresh form simply
							 */
							
							$this -> drawForm();
							
						}
						
						
					}else{
						
						/**
						 * form not send
						 */
						
						parent::draw( $style -> drawBlock( $language -> getString( 'register_on_board_guidelines'), $language -> getString( 'register_on_board_bad_step')));
											
					}
					
				}else if ( $_GET['step'] == 2){
					
					/**
					 * draw form
					 */
					
					$this -> drawForm();
					
				}else{
					
					/**
					 * if message
					 */
					
					if ( $_GET['dec']){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'information'), $language -> getString( 'register_on_board_guidelines_decline_message')));
						
					}
					
					/**
					 * draw guidelines and yes no
					 */
					
					$next_step_link = array( 'step' => 2);
					$decline_link = array( 'dec' => 1);
					
					$guide_lines_form = new form();
					$guide_lines_form -> openForm( parent::systemLink( parent::getId(), $next_step_link));
					$guide_lines_form -> openOpTable();
					
					$guide_lines_form -> drawRow( $strings -> parseBB( nl2br( $settings['guidelines_registration']), true, true));
					
					$guide_lines_form -> closeTable();
					$guide_lines_form -> drawButton( $language -> getString( 'register_on_board_guidelines_accept'), false, '<input type="button" name="decline" value="'.$language -> getString( 'register_on_board_guidelines_decline').'" onclick="document.location = \''.parent::systemLink( parent::getId(), $decline_link).'\'">');
					$guide_lines_form -> closeForm();
					
					parent::draw( $style -> drawFormBlock( $language -> getString( 'register_on_board_guidelines'), $guide_lines_form -> display()));
					
				}
			
			}else{
				
				/**
				 * user is logged in
				 */
				
				parent::draw( $style -> drawBlock( $language -> getString( 'register_on_board'), $language -> getString( 'register_on_board_disabled')));
				
			}
				
		}else{
			
			/**
			 * user is logged in
			 */
			
			parent::draw( $style -> drawBlock( $language -> getString( 'register_on_board'), $language -> getString( 'register_on_board_for_guests_only')));
			
		}
	}
	
	function drawForm(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * default values
		 */
		
		$user_login = "";
		$user_mail = "";

		$custom_fields = array();
		
		/**
		 * retake vars, if repeat
		 */
		
		if ( $_GET['step'] == 3){

			$user_login = uniSlashes(htmlspecialchars(trim($_POST['user_login'])));
			$user_mail = $strings -> inputClear( $_POST['user_mail'], false);
			
			/**
			 * additional fields ;]
			 */
			
			if ( count( $users -> custom_fields) > 0){
				
				foreach ( $users -> custom_fields as $field_id => $field_ops){
	
					if ( $field_ops['profile_field_onregister'])
						$custom_fields[ $field_id] = stripslashes( $strings -> inputClear( $_POST['field_'.$field_id], false));
						
				}
				
			}
			
		}
		
		/**
		 * captcha
		 */
		
		$captcha_key = $captcha -> generate();
		
		/**
		 * begin drawing
		 */
		
		$register_url = array( 'step' => 3);
		
		$register_form = new form();
		$register_form -> openForm( parent::systemLink( parent::getId(), $register_url));
		$register_form -> hiddenValue( 'captcha', $captcha_key);
		$register_form -> openOpTable();
		
		$register_form -> drawTextInput( $language -> getString( 'register_on_board_login'), 'user_login', $user_login);
		$register_form -> drawPassInput( $language -> getString( 'register_on_board_pass'), 'user_pass');
		$register_form -> drawPassInput( $language -> getString( 'register_on_board_pass_rep'), 'user_pass_rep');
		$register_form -> drawTextInput( $language -> getString( 'register_on_board_email'), 'user_mail', $user_mail);
		
		/**
		 * additional fields ;]
		 */
		
		if ( count( $users -> custom_fields) > 0){
			
			$table_opened = false;
			$table_closed = false;
			
			foreach ( $users -> custom_fields as $field_id => $field_ops){

				if ( $field_ops['profile_field_onregister']){
					
					if ( !$table_opened){
						
						$table_opened = true;
						$register_form -> closeTable();
						$register_form -> drawSpacer( $language -> getString( 'user_cp_section_personal_change_profile_other'));
						$register_form -> openOpTable();
					
					}
					
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
							
							$register_form -> drawInfoRow( $field_ops['profile_field_name'], '<input name="field_'.$field_id.'" type="text" size="50" value="'.$custom_fields[$field_id].'" '.$limit.'/>', $field_ops['profile_field_info'].$limit_msg);
					
						break;
					
						case 1:
					
							$limit_msg = '';
							
							if ( $field_ops['profile_field_length'] > 0){
								
								$language -> setKey( 'max_lenght', $field_ops['profile_field_length']);
								
								$limit_msg = $language -> getString( 'user_cp_length_info');
								
								if ( strlen( $field_ops['profile_field_info']) > 0)
									$limit_msg = '<br />'.$limit_msg;								
									
							}
							
							$register_form -> drawTextBox( $field_ops['profile_field_name'], 'field_'.$field_id, $custom_fields[$field_id], $field_ops['profile_field_info'].$limit_msg);
							
						break;
												
						case 2:
												
							$register_form -> drawList( $field_ops['profile_field_name'], 'field_'.$field_id, $made_options, $custom_fields[$field_id], $field_ops['profile_field_info']);
					
						break;
					}
					
				}	
					
			}
			
			if ( $table_opened && ! $table_closed)
				$register_form -> closeTable();
			
		}
		
		/**
		 * and captcha
		 */
		
		$register_form -> addToContent( $captcha -> drawForm($captcha_key));
		
		$register_form -> closeTable();
		$register_form -> drawButton( $language -> getString( 'register_on_board_ok'));
		$register_form -> closeForm();
		
		/**
		 * display
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'register_on_board'), $register_form -> display()));
		
	}
	
}

?>