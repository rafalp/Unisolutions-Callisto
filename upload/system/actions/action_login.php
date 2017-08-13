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
|	User Log-in
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class action_login extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
			/**
			 * what user can do
			 */
			
			$correct_do = array( 'login', 'login_check', 'logout', 'acp_logout');
						
			if ( !isset( $_GET['do']) || !in_array( $_GET['do'], $correct_do)){
			
				/**
				 * default action then
				 */
					
				$do = 'login';
				
			}else{
				
				/**
				 * do specified action
				 */
				
				$do = $_GET['do'];
								
			}
			
			$action_to_do = 'action_'.$do;
			
			$this -> $action_to_do();
		
	}
	
	function action_login( $error_message = ""){
		
		global $page_module;
		global $style;
		global $language;
		global $settings;
		global $smode;
		global $strings;
		
		//$form = new form();
			
		$gets_array['do'] = 'login_check';	
		
		$login_form = new form();
		
		if(defined( 'ACP' )){
		
			$login_form -> openForm(parent::adminLink(parent::getId(), $gets_array));
			
		}else{
						
			$login_form -> openForm(parent::systemLink(parent::getId(), $gets_array));
			//$form -> drawSpacer( $language -> getString( 'login_form_info'));
		}
		
		/**
		 * and login form
		 */
		
		if ( !defined( 'ACP' ) && !empty( $_POST['login']))
			$typed_login = $_POST['login'];
		
		if(defined( 'ACP' )){
		
			$message = $language -> getString( 'login_message_acp');
		
		}else{
			
			if ( $settings['board_offline']){
			
				if ( strlen( $settings['board_closed_message']) == 0){
				
					$message = $language -> getString( 'page_is_offline_info');
				
				}else{
				
					$message = $strings -> parseBB( nl2br( $settings['board_closed_message']), true, true);
										
				}
				
			}else{
			
				if ( $settings['user_require_login_to_browse']){
				
					$message = $language -> getString( 'page_is_closed_info');
				
				}else{
					
					$message = $language -> getString( 'login_message_frontend');
				
				}
			}
			
			/**
			 * forgot password
			 */
						
			$message .= '<br /><br /><a href="'.parent::systemLink( 'reset_pass').'">'.$language -> getString( 'login_forget_pass').'</a>';
						
			/**
			 * registering add_on
			 */
			
		}
					
		$login_form -> addToContent( $style -> drawLoginForm( $message, $error_message, true, $_POST['user_remember'], $_POST['user_hidden']));
		
		//$form -> addToContent( $style -> drawLoginForm( $style -> drawFormBlock( $language -> getString( 'login_form'), $sub_form -> display()), $message_block, $ops_block));
			
		$login_form -> drawButton( $language -> getString( 'login_button'));
		
		$login_form -> closeForm();
		
		if ( defined( 'ACP'))
			parent::draw('<div style="width: 500px; margin-left: auto; margin-right: auto">');
		
		if ( $settings['board_offline'] && !defined( 'ACP')){
		
			parent::draw( $style -> drawFormBlock( $language -> getString( 'board_is_offline_title'), $login_form -> display()));
		
		}else{
		
			parent::draw( $style -> drawFormBlock( $language -> getString( 'login_form'), $login_form -> display()));
			
		}
		if ( defined( 'ACP'))
			parent::draw('</div>');
		
	}
	
	function action_login_check(){

		global $system_settings;
		global $settings;
		global $mysql;
		global $style;
		global $session;
		global $smode;
		global $users;
		global $page;
		global $language;
		global $strings;
		global $logs;
		global $output;
		global $cache;
		global $ban_filters;
		
		$login = uniSlashes(htmlspecialchars(trim($_POST['user_login'])));
		$pass = trim($_POST['user_pass']);
			
		$error = 0;
		
		if( $session -> banned)
			$error = 7;
				
		if($login == null)
			$error = 1;
	
				
		if($pass == null && $error == 0)
			$error = 2;
				
		$pass = md5(md5($pass).md5($pass));
						
		If($error == 0){
					
			/**
			 * chceck in data base, if user exist
			 */
					
			$login_found = false;
					
			$query = $mysql -> query("SELECT `user_id`, `user_password`, `user_active`, `user_locked`, `user_logins_tries`, `user_logins_tries_num`, `user_warns`, `user_mail`, `user_main_group`, `user_other_groups` FROM users WHERE `user_login` LIKE '".$login."' AND `user_id` <> '-1'");
			
			if ($result = mysql_fetch_array($query, MYSQL_NUM)) {
						
				$login_found = true;
				$user_id = $result[0];
				$new_pass = $result[1];
				
				$user_active = $result[2];
				$user_locked = $result[3];
										
				$login_tries_not_parsed = $result[4];
				$login_tries_num = $result[5];
				
				$user_warns = $result[6];
				
				$user_mail = $result[7];
				$user_main_group = $result[8];
				$user_other_groups = $result[9];
				
				$user_groups = array();
				$user_groups = split( ",", $user_other_groups);
				$user_groups[] = $user_main_group;
				
				$access = $users -> checkAcp( $user_groups);
				
				$login_tries;
				
				if( !empty($login_tries_not_parsed)){
					
					/**
					 * build up array containing list of last login tries
					 */
					
					$login_tries_not_parsed_step_2 = split( ';', $login_tries_not_parsed);
					
					foreach ( $login_tries_not_parsed_step_2 as $try_data){
						
						$try_details = split( '-', $try_data);
						
						$login_tries[$try_details[0]] = $try_details[1];
						
					}
					
				}
				
			}
					
			if($login_found == false){
				$error = 3;
				$login = '';
			}else{
					
				if($new_pass != $pass){
					
					$error = 4;
					
					/**
					 * pass is wrong. We can assume that somebody tries to brute-force it, so lets start the proceedure.
					 */
					
					if ( $settings['user_login_max_tries'] > 0){
						
						/**
						 * brute force protection is on, lets add new trace to log
						 */
						
						$login_tries_not_parsed_step_2[] = time().'-'.$_SERVER['REMOTE_ADDR'];
						$login_tries_num ++;
						
						$mysql -> query("UPDATE users SET `user_logins_tries_num` = '$login_tries_num', `user_logins_tries` = '".join( ';', $login_tries_not_parsed_step_2)."' WHERE `user_id` = '$user_id'");
											
					}
					
				}
					
				if($access == false && defined( 'ACP' ))
					$error = 5;
						
			}
						
		}
		
		if ( $error == 0 && $user_active == false)
			$error = 6;			
				
		if ( $error == 0 && $user_locked)
			$error = 7;	
		
		/**
		 * additional ban filtering
		 */
			
		if ( $error == 0 && $user_main_group != 1){
			
			/**
			 * check ip
			 */
			
			foreach ( $ban_filters as $banfilter_ops){
				
				if ( $banfilter_ops['type'] == 0){
					
					/**
					 * check filter
					 */
					
					if ( strstr( $banfilter_ops['filter'], "*") != false){
						
						/**
						 * inteligent match
						 */
						
						$match_pattern = str_ireplace( ".", "\\.", $banfilter_ops['filter']);
						$match_pattern = str_ireplace( "^", "\\^", $match_pattern);
						$match_pattern = str_ireplace( "$", "\\$", $match_pattern);
						$match_pattern = str_ireplace( "?", "\\?", $match_pattern);
						$match_pattern = str_ireplace( "+", "\\+", $match_pattern);
						$match_pattern = str_ireplace( "[", "\\[", $match_pattern);
						$match_pattern = str_ireplace( "]", "\\]", $match_pattern);
						$match_pattern = str_ireplace( "(", "\\(", $match_pattern);
						$match_pattern = str_ireplace( ")", "\\)", $match_pattern);
						$match_pattern = str_ireplace( "{", "\\{", $match_pattern);
						$match_pattern = str_ireplace( "}", "\\}", $match_pattern);
						$match_pattern = str_ireplace( "\\", "\\\\", $match_pattern);
												
						$match_pattern = str_replace( "*", "(.+)", $match_pattern);
						
						$match_pattern = '^'.$match_pattern.'$^';
						$match_string = $_SERVER['REMOTE_ADDR'];
						$maths = preg_match( $match_pattern, $match_string);
						
						if ( $maths > 0){
						
							$user_locked = true;
							$error = 7;
							
						}
							
					}else{
						
						/**
						 * simple match
						 */
						
						if ( $banfilter_ops['filter'] == $login){
							$user_locked = true;
							$error = 7;
						}
					}
					
				}
				
			}
			
			/**
			 * check user login
			 */
			
			foreach ( $ban_filters as $banfilter_ops){
				
				if ( $banfilter_ops['type'] == 1){
					
					/**
					 * check filter
					 */
					
					if ( strstr( $banfilter_ops['filter'], "*") != false){
						
						/**
						 * inteligent match
						 */
						
						$match_pattern = str_ireplace( "\\", "\\\\", $banfilter_ops['filter']);
						$match_pattern = str_ireplace( ".", "\\.", $match_pattern);
						$match_pattern = str_ireplace( "^", "\\^", $match_pattern);
						$match_pattern = str_ireplace( "$", "\\$", $match_pattern);
						$match_pattern = str_ireplace( "?", "\\?", $match_pattern);
						$match_pattern = str_ireplace( "+", "\\+", $match_pattern);
						$match_pattern = str_ireplace( "[", "\\[", $match_pattern);
						$match_pattern = str_ireplace( "]", "\\]", $match_pattern);
						$match_pattern = str_ireplace( "(", "\\(", $match_pattern);
						$match_pattern = str_ireplace( ")", "\\)", $match_pattern);
						$match_pattern = str_ireplace( "{", "\\{", $match_pattern);
						$match_pattern = str_ireplace( "}", "\\}", $match_pattern);
												
						$match_pattern = str_replace( "*", "(.+)", $match_pattern);
						
						$match_pattern = '^'.$match_pattern.'$^';
						$match_string = $login;
						$maths = preg_match( $match_pattern, $match_string);
						
						if ( $maths > 0){
						
							$user_locked = true;
							$error = 7;
							
						}
						
					}else{
						
						/**
						 * simple match
						 */
						
						if ( $banfilter_ops['filter'] == $login){
							$user_locked = true;
							$error = 7;
						}
					}
					
				}
				
			}
			
			/**
			 * check user mail
			 */
			
			foreach ( $ban_filters as $banfilter_ops){
				
				if ( $banfilter_ops['type'] == 2){
					
					/**
					 * check filter
					 */
					
					if ( strstr( $banfilter_ops['filter'], "*") != false){
						
						/**
						 * inteligent match
						 */
						
						$match_pattern = str_ireplace( "\\", "\\\\", $banfilter_ops['filter']);
						$match_pattern = str_ireplace( ".", "\\.", $match_pattern);
						$match_pattern = str_ireplace( "^", "\\^", $match_pattern);
						$match_pattern = str_ireplace( "$", "\\$", $match_pattern);
						$match_pattern = str_ireplace( "?", "\\?", $match_pattern);
						$match_pattern = str_ireplace( "+", "\\+", $match_pattern);
						$match_pattern = str_ireplace( "[", "\\[", $match_pattern);
						$match_pattern = str_ireplace( "]", "\\]", $match_pattern);
						$match_pattern = str_ireplace( "(", "\\(", $match_pattern);
						$match_pattern = str_ireplace( ")", "\\)", $match_pattern);
						$match_pattern = str_ireplace( "{", "\\{", $match_pattern);
						$match_pattern = str_ireplace( "}", "\\}", $match_pattern);
												
						$match_pattern = str_replace( "*", "(.+)", $match_pattern);
						
						$match_pattern = '^'.$match_pattern.'$^';
						
						$match_string = $user_mail;
						$maths = preg_match( $match_pattern, $match_string);
						
						if ( $maths > 0){
						
							$user_locked = true;
							$error = 7;
							
						}
							
					}else{
						
						/**
						 * simple match
						 */
						
						if ( $banfilter_ops['filter'] == $login){
							$user_locked = true;
							$error = 7;
						}
					}
					
				}
				
			}
			
		}
		
		/**
		 * reached/passed limit of max login tries
		 */
		
		if ( $error == 0 && !defined('ACP') && $login_tries_num >= $settings['user_login_max_tries'] && $settings['user_login_max_tries'] > 0){
			
			/**
			 * lets go trought array, and check, if actual user tried to login 
			 */
			
			$last_try_from_client = 0;
			$clients_ip_matches = 0;
			
			/**
			 * go trought list o tries
			 */
			
			$client_ip = $_SERVER['REMOTE_ADDR'];
			
			foreach ( $login_tries as $try_time => $try_ip){
						
				if ( $try_ip == $client_ip){
					
					$last_try_from_client = $try_time;
					$clients_ip_matches ++;
				}
				
			}
			
			/**
			 * final check, if we can login from this ip
			 */
			
			$time_to_unlock = round(($last_try_from_client + ($settings['user_login_reset_after'] * 60) - time())/60);
			
			if( $clients_ip_matches >= $settings['user_login_max_tries']){
				
				if ( $settings['user_login_automaticaly_unlock']){
				
					if ( $time_to_unlock > 0){
						
						/**
						 * its still too soon
						 */
						
						$language -> setKey( 'lm', $time_to_unlock);
						$error = 8;
						
					}else{
						
						/**
						 * time is right, remove clients ip from list, and let him log in
						 */
													
						foreach ( $login_tries as $try_time => $try_ip){
						
							if ( $try_ip != $client_ip){
								
								$login_tries_clean[$try_time] = $try_ip;
							}
							
						}
																				
						/**
						 * update it
						 */
						
						if ( isset($login_tries_clean) && $settings['user_login_reset_after'] != 0){
						
							/**
							 * there are other failed tries
							 */
													
							$mysql -> query("UPDATE users SET `user_logins_tries_num` = '".count( $login_tries)."', `user_logins_tries` = '".join( ';', $login_tries_clean)."' WHERE `user_id` = '$user_id'");
						
						}else{
							
							/**
							 * there are no other failed tries
							 */
							
							$mysql -> query("UPDATE users SET `user_logins_tries_num` = '0', `user_logins_tries` = '' WHERE `user_id` = '$user_id'");
														
						}
					}
					
				}else{
					
					$error = 9;
					
				}
			}
		}
		
		/**
		 * warns limit
		 */
		
		if ( $error == 0 && $settings['warns_turn'] && $user_warns >= $settings['warns_max'] && $settings['warns_max'] > 0 && $settings['warns_lock_account'] && $user_main_group != 1)
			$error = 11;	
		
		/**
		 * check, if user is already logged in
		 */
		
		$user_ip = ip2long($_SERVER['REMOTE_ADDR']);
			
		if ( defined( 'ACP')){
		
			$session_query = $mysql -> query( "SELECT * FROM admins_sessions WHERE `admin_session_id` = '$user_id' AND `admin_session_ip` = '$user_ip'");
		
			$last_action_time = 'admin_session_last_time';
			
		}else{
					
			$session_query = $mysql -> query( "SELECT * FROM users_sessions WHERE `users_session_id` = '$user_id' AND `users_session_ip` = '$user_ip'");
		
			$last_action_time = 'users_session_last_time';	
			
		}
		
		if ( $session_result = mysql_fetch_array( $session_query, MYSQL_ASSOC) && $error == 0 ){
			
			if( $session_result[$last_action_time] > (time()- 60))
				$error = 10;
		}
		
		if ( defined( 'ACP') && $error == 0 && !$access)
			$error = 12;
		
		if($error == 0){
			
			/**
			 * no errors during login procedure
			 */
						
			if( defined( 'ACP' )){
				$session -> newAdminSession( $user_id, $session -> session_id);
			}else{
				
				$session_hidden = $_POST['user_hidden'];
				
				settype( $session_hidden, 'bool');
				
				$session -> promoteGuestSession( $user_id, $session -> session_id, $session_hidden);
				
				/**
				 * set cookies
				 */
				
				$user_remember = $_POST['user_remember'];
				
				settype( $user_remember, 'bool');
				
				if( $user_remember){
				
					$auto_login_key = md5($pass.time().ip2long( $_SERVER['REMOTE_ADDR']));
					
					setUniCookie( 'login_user', $user_id, true);
					setUniCookie( 'login_key', $auto_login_key, true);
					
					$new_key_sql['users_autologin_key'] = $auto_login_key;
					$new_key_sql['users_autologin_user'] = $user_id;
					$new_key_sql['users_autologin_last_use'] = time();
					$new_key_sql['users_autologin_hidden'] = $session_hidden;
					
					$mysql -> insert( $new_key_sql, 'users_autologin');
					
				}
			}
				
			/**
			 * everythings done, draw information box
			 */
			
			if( defined( 'ACP' )){
				
				$message = $language -> getString( 'login_acp_success');
				$title = $language -> getString( 'login_acp_form');
				
				$acp_link = ACP_PATH;
				
			}else{
				
				$message = $language -> getString( 'login_success');
				$title = $language -> getString( 'login_form');
				
				$acp_link = '';
			}
				
			$content = new form();
			$content -> openForm( defined( 'ACP' ) ? ROOT_PATH.$acp_link : parent::systemLink(''));
			$content -> openOpTable();
			
			$content -> drawRow( $message);
			
			$content -> closeTable();
			$content -> drawButton( $language -> getString('login_proceed'));
			$content -> closeForm();
			
			parent::draw( $style -> drawFormBlock( $title, $content -> display()));
			
			$output -> setRedirect();
			
			/**
			 * set special mode
			 */
			
			$smode = 1;
			
			/**
			 * clear online cache
			 */

			$cache -> flushCache( 'users_online');
			$cache -> flushCache( 'users_settings');
			
			/**
			 * add log
			 */
			
			$logs -> addLoginLog( $user_id, 1);
				
		}else{
			
			/**
			 * we got an error in the login
			 */
					
			$error_msg[1] = $language -> getString( 'login_error_1');
			$error_msg[2] = $language -> getString( 'login_error_2');
			$error_msg[3] = $language -> getString( 'login_error_3');
			$error_msg[4] = $language -> getString( 'login_error_4');
			$error_msg[5] = $language -> getString( 'login_error_5');
			$error_msg[6] = $language -> getString( 'login_error_6');
			$error_msg[7] = $language -> getString( 'login_error_7');
			$error_msg[8] = $language -> getString( 'login_error_8');
			$error_msg[9] = $language -> getString( 'login_error_9');
			$error_msg[10] = $language -> getString( 'login_error_10');
			$error_msg[12] = $language -> getString( 'login_error_12');
				
			if ( strlen( $settings['warns_lock_message']) == 0){
				
				$error_msg[11] = $language -> getString( 'login_error_11');
			
			}else{
				$error_msg[11] = $strings -> parseBB( nl2br( $settings['warns_lock_message']), false, false);
			}
			
			parent::draw( $this -> action_login( $error_msg[$error]));
			
			/**
			 * add log
			 */
			
			$logs -> addLoginLog( $user_id, 0);	
		}
		
		
	}
	
	/**
	 * logout action
	 *
	 */
	
	function action_logout(){
		
		include( FUNCTIONS_GLOBALS);

		/**
		 * logout user only, if he is prevoiusly logged in
		 */
			
		if ( $session -> user['user_id'] != -1){
			
			/**
			 * depending on our current location, do diffren action
			 */
			
			if ( defined('ACP')){
			
				$acp_link = ACP_PATH;
				$message = $language -> getString( 'login_acp_logout');
				$title = $language -> getString( 'login_acp_form');
								
			}else{
				
				$session -> degradeSession( $session -> user['user_id'], $session -> session_id);
				
				killUniCookie( 'login_user');
				killUniCookie( 'login_key');		
				
				$cache -> flushCache( 'users_online');
				
				$message = $language -> getString( 'login_logout');
				$title = $language -> getString( 'login_form');
			
			}
				
			
			$smode = 1;
				
			$content = new form();
			$content -> openForm( ROOT_PATH.$acp_link);		
			$content -> openOpTable();
			
			$content -> drawRow( $message);
			
			$content -> closeTable();
			$content -> drawButton( $language -> getString('login_proceed'));
			$content -> closeForm();
			
			parent::draw( $style -> drawFormBlock( $title, $content -> display()));
			
			$output -> setRedirect();
			
		}
		
	}
	
	/**
	 * acp logout action
	 *
	 */
	
	function action_acp_logout(){
		
		global $session;
		global $style;
		global $page;
		global $language;
		global $output;
		
		$session -> degradeAdminSession( $session -> user['user_id'], $session -> session_id);
		
		$content = new form();
		$content -> openForm( ROOT_PATH.ACP_PATH);
		$content -> openOpTable();
		$content -> drawRow( $language -> getString( 'login_acp_logout'));
		$content -> closeTable();
		$content -> drawButton( $language -> getString('login_proceed'));
		$content -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'login_form'), $content -> display()));
		
		$output -> setRedirect();
		
	}
	
}

?>