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
|	User CP
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

/**
 * draw user profile
 *
 */

class action_user_cp extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
					
		/**
		 * check if we are logged in
		 */
		
		if ( $session -> user['user_id'] != -1){
			
			/**
			 * user is logged in
			 * add first breadcrumb
			 */

			$path -> addBreadcrumb( $language -> getString('user_cp_title'), parent::systemLink( 'profile'));
			
			/**
			 * set page title
			 */
			
			$output -> setTitle($language -> getString( 'user_cp_title'));
			
			/**
			 * split page in two
			 */
				
			parent::draw( '<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
			    <td style="width: 250px; vertical-align: top; padding-right: 6px;">');
			
			$user_cp_menu = new form();
						
			/**
			 * if we can, draw messenger
			 */
			
			if ( $session -> user['user_can_use_pm']){
				
				/**
				 * user can use messenger
				 */
				
				$new_message_link = array( 'do' => 'new_pm');
				$have_messages_link = array( 'do' => 'pms_inbox');
				$send_messages_link = array( 'do' => 'pms_outbox');
				
				$messenger_navigation = '<ul>
					<li><a href="'.parent::systemLink( parent::getId(), $new_message_link).'">'.$language -> getString( 'user_cp_section_messenger_new').'</a></li>
					<li><a href="'.parent::systemLink( parent::getId(), $have_messages_link).'">'.$language -> getString( 'user_cp_section_messenger_msgs_received').'</a></li>
					<li><a href="'.parent::systemLink( parent::getId(), $send_messages_link).'">'.$language -> getString( 'user_cp_section_messenger_msgs_send').'</a></li>
				</ul>';
				
				$user_cp_menu -> drawSpacer( $language -> getString( 'user_cp_section_messenger'));
				$user_cp_menu -> openOpTable();
				$user_cp_menu -> drawRow( $messenger_navigation);
				$user_cp_menu -> closeTable();
			}
			
			/**
			 * subscriptions
			 */
			
			$user_cp_menu -> drawSpacer( $language -> getString( 'user_cp_section_subscriptions'));
			$user_cp_menu -> openOpTable();
			
			$subscriptions_navigation = '<ul>
				<li><a href="'.parent::systemLink( parent::getId(), array( 'do' => 'subs_forums')).'">'.$language -> getString( 'user_cp_section_subscriptions_forums').'</a></li>
				<li><a href="'.parent::systemLink( parent::getId(), array( 'do' => 'subs_topics')).'">'.$language -> getString( 'user_cp_section_subscriptions_topics').'</a></li>
			</ul>';
			
			$user_cp_menu -> drawRow( $subscriptions_navigation);
			$user_cp_menu -> closeTable();
			
			/**
			 * personal data
			 */
			
			$change_profile_link = array( 'do' => 'edit_profile');
			$change_signature_link = array( 'do' => 'edit_signature');
			$change_avatar_link = array( 'do' => 'edit_avatar');
			$change_mail_link = array( 'do' => 'edit_mail');
			$change_pass_link = array( 'do' => 'edit_pass');
			
			$profile_navigation = '<ul>
				<li><a href="'.parent::systemLink( parent::getId(), $change_profile_link).'">'.$language -> getString( 'user_cp_section_personal_change_profile').'</a></li>
				<li><a href="'.parent::systemLink( parent::getId(), $change_signature_link).'">'.$language -> getString( 'user_cp_section_personal_change_signature').'</a></li>
				<li><a href="'.parent::systemLink( parent::getId(), $change_avatar_link).'">'.$language -> getString( 'user_cp_section_personal_change_avatar').'</a></li>
				<li><a href="'.parent::systemLink( parent::getId(), $change_mail_link).'">'.$language -> getString( 'user_cp_section_personal_change_mail').'</a></li>
				<li><a href="'.parent::systemLink( parent::getId(), $change_pass_link).'">'.$language -> getString( 'user_cp_section_personal_change_pass').'</a></li>
			</ul>';
			
			$user_cp_menu -> drawSpacer( $language -> getString( 'user_cp_section_personal'));
			$user_cp_menu -> openOpTable();
			$user_cp_menu -> drawRow( $profile_navigation);
			$user_cp_menu -> closeTable();
			
			/**
			 * options
			 */
			
			$change_emails_settings_link = array( 'do' => 'emails_settings');
			$change_board_settings_link = array( 'do' => 'board_settings');
			
			$options_navigation = '<ul>
				<li><a href="'.parent::systemLink( parent::getId(), $change_emails_settings_link).'">'.$language -> getString( 'user_cp_section_options_emails_settings').'</a></li>
				<li><a href="'.parent::systemLink( parent::getId(), $change_board_settings_link).'">'.$language -> getString( 'user_cp_section_options_board_settings').'</a></li>
			</ul>';
			
			$user_cp_menu -> drawSpacer( $language -> getString( 'user_cp_section_options'));
			$user_cp_menu -> openOpTable();
			$user_cp_menu -> drawRow( $options_navigation);
			$user_cp_menu -> closeTable();
			
			/**
			 * moderation
			 */
			
			if ( $session -> user['user_can_be_mod']){
				
				$mod_navigation = '<ul>
					<li><a href="'.parent::systemLink( parent::getId(), array( 'do' => 'mod_summary')).'">'.$language -> getString( 'mod_cp_summary').'</a></li>
					<li><a href="'.parent::systemLink( parent::getId(), array( 'do' => 'reports')).'">'.$language -> getString( 'mod_cp_reports').'</a></li>
				</ul>';
					
				$user_cp_menu -> drawSpacer( $language -> getString( 'mod_action'));
				$user_cp_menu -> openOpTable();
				$user_cp_menu -> drawRow( $mod_navigation);
				$user_cp_menu -> closeTable();
				
			}
			
			/**
			 * draw menu
			 */
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'menu'), $user_cp_menu -> display()));
			
			/**
			 * close one column, and open another
			 */
			
			parent::draw( '</td><td style="vertical-align: top">');
				
			/**
			 * create list of proper does
			 */
			
			$proper_does = array( 'summary', 'new_pm', 'pms_inbox', 'show_pm', 'pms_outbox', 'subs_forums', 'subs_topics', 'edit_profile', 'edit_signature', 'edit_avatar', 'show_avatars', 'change_avatar', 'edit_mail', 'edit_pass', 'emails_settings', 'board_settings');
			
			if ( $session -> user['user_can_be_mod']){
			
				$proper_does[] = 'mod_summary';
				$proper_does[] = 'reports';
				$proper_does[] = 'post_reports';
					
			}
			
			if ( isset( $_GET['do']) && in_array( $_GET['do'], $proper_does)){
				
				$action_to_do = $_GET['do'];
				
			}else{
				
				$action_to_do = 'summary';
				
			}
			
			/**
			 * do action
			 */
			
			switch ( $action_to_do){
				
				case 'summary':
					
					$this -> action_draw_summary();
					
				break;
				
				case 'mod_summary':
					
					$this -> action_draw_mod_summary();
					
				break;
				
				case 'new_pm':
					
					$this -> action_new_pm();
					
				break;
				
				case 'pms_inbox':
					
					$this -> action_pms_inbox();
					
				break;
				
				case 'show_pm':
					
					$this -> action_show_pm();
					
				break;
				
				case 'pms_outbox':
					
					$this -> action_pms_outbox();
					
				break;
				
				case 'subs_forums':
					
					$this -> action_subs_forums();
					
				break;
				
				case 'subs_topics':
					
					$this -> action_subs_topics();
					
				break;
				
				case 'edit_profile':
					
					$this -> action_edit_profile();
					
				break;
				
				case 'edit_signature':
					
					$this -> action_edit_signature();
					
				break;
				
				case 'edit_avatar':
					
					$this -> action_edit_avatar();
					
				break;
				
				case 'show_avatars':
					
					$this -> action_show_avatars_gallery();
					
				break;
				
				case 'change_avatar':
					
					$this -> action_change_avatar();
					
				break;
				
				case 'edit_mail':
					
					$this -> action_edit_mail();
					
				break;
				
				case 'edit_pass':
					
					$this -> action_edit_pass();
					
				break;
				
				case  'emails_settings':
					
					$this -> action_emails_settings();
					
				break;
				
				case  'board_settings':
					
					$this -> action_board_settings();
					
				break;
				
				case  'reports':
					
					$this -> action_reports();
					
				break;
				
				case  'post_reports':
					
					$this -> action_post_reports();
					
				break;
				
			}
			
			/**
			 * close maintable
			 */
			
			parent::draw( '</td>
				</tr>
			</table>');
			
		}else{
			
			/**
			 * user is an guest
			 * draw error, and login form
			 */
			
			parent::draw($style -> drawErrorBlock( $language -> getString( 'error_no_access'), $language -> getString( 'users_guest_noaccess')));
			
			global $actual_action;
			$actual_action = 'login';
			$login_form = new action_login();
			
			/**
			 * set page title
			 */
			
			$output -> setTitle( $language -> getString( 'error_no_access'));
			
		}
		
	}
	
	/**
	 * draws summamry page
	 *
	 */
	
	function action_draw_summary(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * update notepad
		 */
		
		if ( $_POST['sub_act'] == 'update_notepad' && $session -> checkForm()){
			
			$notepad_update_sql['user_notepad'] = $strings -> inputClear( $_POST['user_notepad'], false);
			
			$mysql -> update($notepad_update_sql, 'users', "`user_id` = '".$session -> user['user_id']."'");
			
		}
		
		/**
		 * select user data
		 */
		
		$user_query = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '".$session -> user['user_id']."'");
		
		if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
			
			$user_result = $mysql -> clear( $user_result);
			
		}
		
		/**
		 * begin drawing form
		 */
		
		$summary_form = new form();
		$summary_form -> drawSpacer( $language -> getString( 'user_cp_act_summary_subsection_summary'));
		$summary_form -> openOpTable();
		
		/**
		 * draw 4 rows
		 */
		
		$summary_form -> drawInfoRow( $language -> getString( 'user_mail'), '<a href="mailto:'.$user_result['user_mail'].'">'.$user_result['user_mail'].'</a>');
		$summary_form -> drawInfoRow( $language -> getString( 'user_posts'), $user_result['user_posts_num']);
		$summary_form -> drawInfoRow( $language -> getString( 'user_registration'), $time -> drawDate( $user_result['user_regdate']));
		$summary_form -> drawInfoRow( $language -> getString( 'user_posts_average_per_day'), round( $user_result['user_posts_num']/((time()-$user_result['user_regdate'])/(24*60*60)), 2));
		
		$summary_form -> closeTable();
		$summary_form -> drawSpacer( $language -> getString( 'user_cp_act_summary_subsection_notepad'));
		
		$update_notepad_link['do'] = 'summary';
		
		$summary_form -> openForm(parent::systemLink( parent::getId(), $update_notepad_link));
		$summary_form -> hiddenValue( 'sub_act', 'update_notepad');
		$summary_form -> openOpTable();
		$summary_form -> drawSingleTextBox( 'user_notepad', $user_result['user_notepad']);
		$summary_form -> closeTable();
		$summary_form -> drawButton( $language -> getString( 'user_cp_act_summary_subsection_notepad_save'));
		$summary_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_title'), $summary_form -> display()));
		
	}
	
	/**
	 * pm's prewiev form
	 */
	
	function pmsSummary(){
		
		//include globals
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * begin drawing
		 */
		
		$pms_summary_form = new form();
		$pms_summary_form -> openOpTable();
		$pms_summary_form -> addToContent( '<tr>
			<td class="opt_row1" style="width: 100px; font-weight: bold">'.$language -> getString( 'user_cp_section_messenger_summary_total').'</td>
			<td class="opt_row2" style="width: 100px; font-weight: bold">'.$session -> user['user_pm_num'].'/'.$session -> user['user_pm_limit'].'</td>
			<td class="opt_row3">'.$style -> drawBar( floor( ($session -> user['user_pm_num']/$session -> user['user_pm_limit'])*100 )).'</td>
		</tr>');
		
		$pms_summary_form -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_messenger_summary'), $pms_summary_form -> display()));
		
	}
	
	/**
	 * new pm form
	 */
	
	function action_new_pm(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check perms
		 */
		
		if ( $session -> user['user_can_use_pm']){
			
			/**
			 * check is we have specified user
			 */
			
			$user_predefinied = false;
			
			$user_groups = array();
				
			if ( isset( $_GET['user'])){
				
				$user_to_show = $_GET['user'];
				
				settype( $user_to_show, 'integer');
				
				/**
				 * select him
				 */
								
				$user_query = $mysql -> query( "SELECT u.user_id, u.user_login, u.user_pm_num, u.user_main_group, u.user_other_groups, g.users_group_prefix, g.users_group_suffix FROM users u LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE u.user_id = '$user_to_show' AND u.user_id > '0'");
				
				if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
					
					$user_result = $mysql -> clear( $user_result);
					$receiver_result = $user_result;
					$user_predefinied = true;
					
					$user_groups = split( ",", $user_result['user_other_groups']);
					$user_groups[] = $user_result['user_main_group'];
					
					$receiver_groups = $user_groups;
					
				}
				
				/**
				 * we need user, to quote message
				 */
				
				if ( isset( $_GET['reply'])){
					
					$msg_to_show = $_GET['reply'];
					
					settype( $msg_to_show, 'integer');
					
					/**
					 * select it
					 */
					
					$reply_query = $mysql -> query( "SELECT * FROM users_messages WHERE `users_message_author` = '$user_to_show' AND `users_message_id` = '$msg_to_show' AND `users_message_receiver` = '".$session -> user['user_id']."'");
				
					if ( $reply_result = mysql_fetch_array( $reply_query, MYSQL_ASSOC)){
						
						$reply_result = $mysql -> clear( $reply_result);
						
					}
					
				}
				
			}
			
			if ( !$user_predefinied || ($user_predefinied && $users -> checkPm( $user_groups))){
				
				/**
				 * check step
				 */
				
				if ( $_GET['send'] == true && $session -> checkForm()){
					
					/**
					 * user decided to save message
					 * we must have existing receiver, and message content
					 */
					
					$new_pm_receiver_found = false;
					
					if ( $user_predefinied){
						
						$new_pm_receiver = $user_to_show;
						$new_pm_receiver_found = true;
					
					}else{
						
						/**
						 * check receiver
						 */
						
						$new_pm_receiver = uniSlashes(htmlspecialchars(trim( $_POST[ 'pm_receiver'])));
				
						$receiver_groups = array();
						
						$receiver_check_query = $mysql -> query( "SELECT user_id, user_mail, user_pm_num, user_notify_pm, user_main_group, user_other_groups, user_lang FROM users WHERE `user_login` = '$new_pm_receiver'");
						if ( $receiver_result = mysql_fetch_array( $receiver_check_query, MYSQL_ASSOC)){
							
							$receiver_result = $mysql -> clear( $receiver_result);
							$new_pm_receiver_found = true;
							
							$new_pm_receiver = $receiver_result['user_id'];
							
							$receiver_groups = split( ",", $receiver_result['user_other_groups']);
							$receiver_groups[] = $receiver_result['user_main_group'];
						}
						
					}
					
					$new_pm_title = $strings -> inputClear( $_POST['pm_title'], false);
					
					$new_pm_title_clear = trim( $_POST['pm_title']);
					$new_pm_title_clear = str_replace( '&quot;', '"', $new_pm_title_clear);
						
					if ( get_magic_quotes_gpc())
						$new_pm_title_clear = stripslashes( $new_pm_title_clear);
				
					$new_pm_text = $strings -> inputClear( $_POST['pm_text'], false);
					
					if ( strlen( $new_pm_receiver) == 0 || strlen( $new_pm_title) == 0 || strlen( $new_pm_text) == 0){
						
						/**
						 * draw error
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_messenger_new'), $language -> getString( 'user_cp_section_messenger_empty_fields')));
					
						/**
						 * draw form
						 */
						
						$this -> drawNewPmForm( $user_predefinied, $user_result, true);
					
					}else if ( strlen( $new_pm_title_clear) > $settings['msg_title_max_length'] || strlen( $new_pm_title_clear) > 90){
										
						$language -> setKey( 'msg_title_limit', $settings['msg_title_max_length']);
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_messenger_new'), $language -> getString( 'user_cp_section_messenger_name_too_long_fields')));
						
						$this -> drawNewPmForm( $user_predefinied, $user_result, true);
				
					}else if( !$new_pm_receiver_found){
						
						/**
						 * draw error
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_messenger_new'), $language -> getString( 'user_cp_section_messenger_receiver_wrong')));
					
						/**
						 * draw form
						 */
						
						$this -> drawNewPmForm( $user_predefinied, $user_result, true);
						
					}else if( $receiver_result['user_pm_num'] >= $users -> checkPmSpace( $receiver_groups)){
						
						/**
						 * draw error
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_messenger_new'), $language -> getString( 'user_cp_section_messenger_receiver_full')));
					
						/**
						 * draw form
						 */
						
						$this -> drawNewPmForm( $user_predefinied, $user_result, true);
						
					}else{
						
						/**
						 * all ok, send it
						 */
						
						$new_pm_sql['users_message_author'] = $session -> user['user_id'];
						$new_pm_sql['users_message_author_name'] = $session -> user['user_login'];
						$new_pm_sql['users_message_receiver'] = $new_pm_receiver;
						$new_pm_sql['users_message_send_time'] = time();
						$new_pm_sql['users_message_subject'] = $new_pm_title;
						$new_pm_sql['users_message_text'] = $new_pm_text;
						
						$mysql -> insert( $new_pm_sql, 'users_messages');
						
						/**
						 * update receiver pm's stats
						 */
						
						$update_user_stats_sql['user_pm_num'] = $receiver_result['user_pm_num'] + 1;
						$update_user_stats_sql['user_pm_new_num'] = $receiver_result['user_pm_new_num'] + 1;
						$mysql -> update( $update_user_stats_sql, 'users', "`user_id` = '$new_pm_receiver'");
						
						/**
						 * send mail
						 */
						
						if ( $receiver_result['user_notify_pm']){
							
							/**
							 * set lang keys
							 */
							
							$language -> setKey( 'new_pm_title', trim( $_POST['pm_title']));
							$language -> setKey( 'new_pm_autor', $session -> user['user_login']);
							$language -> setKey( 'new_pm_link', $settings['board_address'].'/index.php?act=profile&do=pms_inbox');
							
							/**
							 * switch lang
							 */
							
							$language -> switchLanguage( $receiver_result['user_lang']);
							
							/**
							 * send mail
							 */
							
							$mail -> send( $receiver_result['user_mail'], $language -> getString( 'user_cp_section_messenger_new_pm_mail_title'), $language -> getString( 'user_cp_section_messenger_new_pm_mail_text'));
							
							/**
							 * reset lang
							 */
							
							$language -> resetLanguage();
							
						}
						
						/**
						 * and draw message
						 */
						
						parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_messenger_new'), $language -> getString( 'user_cp_section_messenger_send_done')));
						
					}
					
				}else{
					
					/**
					 * previev?
					 */
					
					if ( $_GET['preview']){
						
						parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_messenger_new_previev'), $strings -> parseBB(nl2br( stripslashes( $strings -> inputClear( $_POST['pm_text'], false))), true, true)));
						
						/**
						 * draw form
						 */
						
						$this -> drawNewPmForm( $user_predefinied, $user_result, true);
					
					}else{
					
						/**
						 * draw standard empty form
						 */
							
						$this -> drawNewPmForm( $user_predefinied, $user_result, false, $reply_result);
					
					}
					
				}
				
			}else{
				
				/**
				 * user cant read pm's
				 */
				
				parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_messenger_new'), $language -> getString( 'user_cp_section_messenger_receiver_nopms')));
							
			}
			
		}else{
			
			/**
			 * no perms, draw error
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_messenger_new'), $language -> getString( 'user_cp_section_messenger_noaccess')));
			
		}
		
	}
	
	function drawNewPmForm( $user_predefinied, $user_result = array(), $reget = false, $reply = array()){
		
		global $language;
		global $style;
		global $strings;
		
		$pm_receiver = '';
		
		settype( $reply, 'array');
		
		if ( key_exists( 'users_message_subject', $reply)){
			$pm_title = 'Re: '.$reply['users_message_subject'];
		}else{
			$pm_title = '';
		}
		
		if ( key_exists( 'users_message_text', $reply)){
			$pm_text = '[quote="'.$user_result['user_login'].'"]'.$reply['users_message_text'].'[/quote]';
		}else{
			$pm_text = '';
		}
		
		if ( $reget){
				
			$pm_title = stripslashes( $strings -> inputClear( $_POST['pm_title'], false));
			$pm_text = stripslashes( $strings -> inputClear( $_POST['pm_text'], false));
			$pm_receiver = htmlspecialchars(trim( $_POST[ 'pm_receiver']));
		}
		
		$new_pm_link = array( 'do' => 'new_pm', 'send' => true);
				
		if ( $user_predefinied)
			$new_pm_link['user'] = $user_result['user_id'];
	
		$new_pm_form = new form();
		$new_pm_form -> openForm(parent::systemLink( parent::getId(), $new_pm_link), 'POST', false, 'new_msg_form');
		$new_pm_form -> openOpTable();
		
		if ( $user_predefinied){
			
			/**
			 * receiver is predefinied
			 */
			
			$new_pm_form -> drawInfoRow( $language -> getString( 'user_cp_section_messenger_new_receiver'), '<a href="'.parent::systemLink( 'user', array( 'user' => $user_to_show)).'">'.$user_result['users_group_prefix'].$user_result['user_login'].$user_result['users_group_suffix'].'</a>' );
			
		}else{
			
			/**
			 * receiver not definied, draw input field
			 */
			
			$new_pm_form -> drawTextInput( $language -> getString( 'user_cp_section_messenger_new_receiver'), 'pm_receiver', $pm_receiver);
			
		}
		
		$new_pm_form -> drawTextInput( $language -> getString( 'user_cp_section_messenger_new_title'), 'pm_title', $pm_title);
		$new_pm_form -> drawEditor( $language -> getString( 'user_cp_section_messenger_new_text'), 'pm_text', $pm_text, '', true, true);
		$new_pm_form -> closeTable();
		$new_pm_form -> drawButton( $language -> getString( 'user_cp_section_messenger_new_send_button'), false, '<input type="button" name="preview_message" value="'.$language -> getString( 'user_cp_section_messenger_new_previev_button').'" onclick="showMessage()">');
		$new_pm_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_messenger_new'), $new_pm_form -> display()));
		
		/**
		 * and javascript
		 */
		
		$preview_message_link = array( 'do' => 'new_pm', 'preview' => true);
		
		if ( $user_predefinied){
			$preview_message_link['user'] = $user_result['user_id'];
		}
		
		parent::draw( '<script type="text/JavaScript">
		
			function showMessage(){
			
				pm_form = document.forms["new_msg_form"];
				
				pm_form.action="'.parent::systemLink( parent::getId(), $preview_message_link).'";
			
				pm_form.submit();
			}
			
		</script>');
		
	}
	
	function action_pms_inbox(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check perms
		 */
		
		if ( $session -> user['user_can_use_pm']){
			
			/**
			 * check actions
			 */
			
			if ( isset( $_GET['delete']) && !empty( $_GET['delete'])){
				
				/**
				 * delete exact message
				 */
				
				$msg_to_delete = $_GET['delete'];
				
				settype( $msg_to_delete, 'integer');
				
				$mysql -> delete( 'users_messages', "`users_message_id` = '$msg_to_delete' AND `users_message_receiver` = '".$session -> user['user_id']."'");
				
				/**
				 * resynchornize stats
				 */
				
				$pms_num = $mysql -> countRows( 'users_messages', " `users_message_receiver` = '".$session -> user['user_id']."'");
			
				$unread_pms_num = $mysql -> countRows( 'users_messages', " `users_message_receiver` = '".$session -> user['user_id']."' AND `users_message_readed` = '0'");
			
				$update_users_stats_sql['user_pm_num'] = $pms_num;
				$update_users_stats_sql['user_pm_new_num'] = $unread_pms_num;
				
				$mysql -> update( $update_users_stats_sql, 'users', "`user_id` = '".$session -> user['user_id']."'");
				
				/**
				 * and in session
				 */
				
				$session -> user[ 'user_pm_num'] = $pms_num;
				$session -> user[ 'user_pm_new_num'] = $unread_pms_num;
				
			}
			
			if ( $session -> checkForm()){
				
				/**
				 * do actions
				 */
				
				$pms_to_maniulate = $_POST[ 'select_msg'];
				
				settype( $pms_to_maniulate, 'array');
				
				$pms_to_maniulate_ids = array();
				
				foreach ( $pms_to_maniulate as $pm_id => $pm_manipulate)
					$pms_to_maniulate_ids[] = $pm_id;
				
				$pms_to_maniulate = join( ',', $pms_to_maniulate_ids);
				
				if ( !empty( $pms_to_maniulate)){
				
					switch ( $_POST['pms_actions']){
					
						case 0:
							
							$pms_update_sql['users_message_readed'] = true;
							$pms_update_sql['users_message_receive_time'] = time();
							
							$mysql -> update( $pms_update_sql, 'users_messages ', "`users_message_id` IN ($pms_to_maniulate) AND `users_message_receiver` = '".$session -> user['user_id']."'");
							
						break;
							
						case 1:
							
							$pms_update_sql['users_message_readed'] = false;
							
							$mysql -> update( $pms_update_sql, 'users_messages ', "`users_message_id` IN ($pms_to_maniulate) AND `users_message_receiver` = '".$session -> user['user_id']."'");
							
						break;
						
						case 2:
							
							$mysql -> delete( 'users_messages ', "`users_message_id` IN ($pms_to_maniulate) AND `users_message_receiver` = '".$session -> user['user_id']."'");
							
						break;
					
					}
					
					/**
					 * resynchornize stats
					 */
					
					$pms_num = $mysql -> countRows( 'users_messages', " `users_message_receiver` = '".$session -> user['user_id']."'");
				
					$unread_pms_num = $mysql -> countRows( 'users_messages', " `users_message_receiver` = '".$session -> user['user_id']."' AND `users_message_readed` = '0'");
				
					$update_users_stats_sql['user_pm_num'] = $pms_num;
					$update_users_stats_sql['user_pm_new_num'] = $unread_pms_num;
					
					$mysql -> update( $update_users_stats_sql, 'users', "`user_id` = '".$session -> user['user_id']."'");
					
					/**
					 * and in session
					 */
					
					$session -> user[ 'user_pm_num'] = $pms_num;
					$session -> user[ 'user_pm_new_num'] = $unread_pms_num;
					
				}
			}
			
			/**
			 * count pms
			 */
			
			if ( !isset( $pms_num))
				$pms_num = $mysql -> countRows( 'users_messages', " `users_message_receiver` = '".$session -> user['user_id']."'");
			
			/**
			 * draw summary at the top
			 */
			
			$this -> pmsSummary( $pms_num);
			
			/**
			 * build paginator
			 */
			
			$pms_per_page = $settings['users_pm_draw'];
			
			settype( $pms_per_page, 'integer');
			
			if ( $pms_per_page < 1)
				$pms_per_page = 1;
			
			$pages_num = floor( $pms_num / $pms_per_page);
					
			/**
			 * get current page
			 */
			
			$current_page = $_GET['p'];
			
			$current_page --;
			
			settype( $current_page, 'integer');
			
			if ( $current_page < 0)
				$current_page = 1;
				
			if ( $current_page > $pages_num)
				$current_page = $pages_num;
				
			/**
			 * begin drawing form
			 */
			
			$inbox_content = new form();
			$inbox_content -> openForm( parent::systemLink( parent::getId(), array( 'do' => 'pms_inbox')));
			$inbox_content -> openOpTable();
			$inbox_content -> addToContent( '<tr>
					<th colspan="2">'.$language -> getString( 'user_cp_section_messenger_msgs_list_title').'</th>
					<th>'.$language -> getString( 'user_cp_section_messenger_msgs_list_time').'</th>
					<th>'.$language -> getString( 'user_cp_section_messenger_msgs_list_sender').'</th>
					<th>&nbsp;</th>
				</tr>');
			
			/**
			 * select
			 */
			
			$messages_query = $mysql -> query( "SELECT m.users_message_id, m.users_message_author, m.users_message_author_name, m.users_message_receive_time, m.users_message_send_time, m.users_message_readed, m.users_message_subject, u.user_id, u.user_login, u.user_main_group, u.user_other_groups, g.users_group_prefix, g.users_group_suffix FROM users_messages m LEFT JOIN users u ON m.users_message_author = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE m.users_message_receiver = '".$session -> user['user_id']."' ORDER BY m.users_message_send_time DESC LIMIT ".($current_page * $pms_per_page).", $pms_per_page");
			
			while( $message_result = mysql_fetch_array( $messages_query, MYSQL_ASSOC)){
				
				//clear result
				$message_result = $mysql -> clear( $message_result);
				
				//sender groups
				$sender_groups = array();
				$sender_groups = split( "'", $message_result['user_other_groups']);
				$sender_groups[] = $message_result['user_main_group'];
				
				/**
				 * message title
				 */
				
				$message_title = $message_result[ 'users_message_subject'];
				
				if ( !$users -> cantCensore( $sender_groups)){
					
					$message_title = $strings -> censore( $message_title);
					
				}
				
				/**
				 * readed?
				 */
				
				if ( $message_result[ 'users_message_readed']){
				
					$message_icon = $style -> drawImage( 'topic', $language -> getString( 'user_cp_section_messenger_msgs_list_read'));
			
				}else{
				
					$message_icon = $style -> drawImage( 'topic_new', $language -> getString( 'user_cp_section_messenger_msgs_list_unread'));
					
				}
				
				if (  $message_result[ 'users_message_receive_time'] == 0){
					
					$msg_read_info = '';
			
				}else{
					
					$msg_read_info = '<br />'.$language -> getString( 'user_cp_section_messenger_msgs_list_read_info').' '.$time -> drawDate( $message_result[ 'users_message_receive_time']);
					
				}
				
				/**
				 * author
				 */
				
				if ( $message_result['users_message_author'] == -1){
					$message_author = $message_result[ 'users_group_prefix'].$message_result[ 'users_message_author_name'].$message_result[ 'users_group_suffix'];
				}else{
					$message_author = '<a href="'.parent::systemLink( 'user', array( 'user' => $message_result[ 'users_message_author'])).'">'.$message_result[ 'users_group_prefix'].$message_result[ 'user_login'].$message_result[ 'users_group_suffix'].'</a>';
				}
				
				/**
				 * add row
				 */
				
				$inbox_content -> addToContent( '<tr>
					<td class="opt_row2" style="text-align: center">'.$message_icon.'</td>
					<td class="opt_row1" style="width: 100%"><a href="'.parent::systemLink( parent::getId(), array('do' => 'show_pm', 'msg' => $message_result[ 'users_message_id'])).'">'.$forums -> cutTopicName( $message_title).'</a>'.$msg_read_info.'</td>
					<td class="opt_row2" style="text-align: center" NOWRAP="nowrap">'.$time -> drawDate( $message_result[ 'users_message_send_time']).'</td>
					<td class="opt_row1" style="text-align: center" NOWRAP="nowrap">'.$message_author.'</td>
					<td class="opt_row3" style="text-align: center" NOWRAP="nowrap">'.$inbox_content -> drawSelect( 'select_msg['.$message_result[ 'users_message_id'].']').'</td>
				</tr>');
				
			}
			
			$inbox_content -> closeTable();
			
			/**
			 * actions
			 */
			
			$pms_actions[0] = '<option value="0">'.$language -> getString( 'user_cp_section_messenger_msgs_list_actions_0').'</option>';
			$pms_actions[1] = '<option value="1">'.$language -> getString( 'user_cp_section_messenger_msgs_list_actions_1').'</option>';
			$pms_actions[2] = '<option value="2">'.$language -> getString( 'user_cp_section_messenger_msgs_list_actions_2').'</option>';
			
			$inbox_content -> drawButton( $language -> getString( 'user_cp_section_messenger_msgs_list_do_action'), false, '<select name="pms_actions">'.join( '', $pms_actions).'</select>');
			$inbox_content  -> closeForm();
			
			/**
			 * display list
			 */
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_messenger_msgs_received'), $inbox_content -> display()));
			
			/**
			 * and paginator
			 */
			
			parent::draw( $style -> drawPaginator( parent::systemLink( parent::getId(), array('do' => 'pms_inbox')), 'p', $pages_num, ($current_page+1)));
			
		}else{
			
			/**
			 * no perms, draw error
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_messenger_msgs_saved'), $language -> getString( 'user_cp_section_messenger_noaccess')));
			
		}
	}
	
	/**
	 * draws pm
	 */
	
	function action_show_pm(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check perms
		 */
		
		if ( $session -> user['user_can_use_pm']){
			
			/**
			 * get message to draw
			 */
			
			$message_to_draw = $_GET['msg'];
			
			settype( $message_to_draw, 'integer');
			
			/**
			 * select message from sql
			 */
			
			if ( $_GET['in'] == true){
				$message_query = $mysql -> query( "SELECT m.*, u.*, f.*, g.users_group_name, g.users_group_prefix, g.users_group_suffix, g.users_group_title FROM users_messages m
				LEFT JOIN users u ON m.users_message_author = u.user_id
				LEFT JOIN profile_fields_data f ON u.user_id = f.profile_fields_user
				LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
				WHERE m.users_message_id = '$message_to_draw' AND m.users_message_author = '".$session -> user['user_id']."'");
			}else{
				$message_query = $mysql -> query( "SELECT m.*, u.*, f.*, g.users_group_name, g.users_group_prefix, g.users_group_suffix, g.users_group_title FROM users_messages m
				LEFT JOIN users u ON m.users_message_author = u.user_id
				LEFT JOIN profile_fields_data f ON u.user_id = f.profile_fields_user
				LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
				WHERE m.users_message_id = '$message_to_draw' AND m.users_message_receiver = '".$session -> user['user_id']."'");	
			}
			
			if ( $message_result = mysql_fetch_array( $message_query, MYSQL_ASSOC)){
				
				//clear result
				$message_result = $mysql -> clear( $message_result);
				
				//sender groups
				$sender_groups = array();
				$sender_groups = split( "'", $message_result['user_other_groups']);
				$sender_groups[] = $message_result['user_main_group'];
				
				/**
				 * message found
				 * draw it
				 */
				
				$message_title = $message_result['users_message_subject'];
				
				if ( !$users -> cantCensore( $sender_groups)){
					$message_title = $strings -> censore( $message_title);
				}
				
				$post_show_form = new form();
				$post_show_form -> drawSpacer( $message_title);
				$post_show_form -> openOpTable();
				
				/**
				 * begin getting data for post parser
				 */

				$pm_display_parts = array();
				$pm_display_strings = array();
				
				/**
				 * user is guest?
				 */
				
				if ( $message_result['user_id'] == -1){
					
					$pm_display_parts['member'] = false;
					
				}else{
					
					$pm_display_parts['member'] = true;
					
				}
				
				/**
				 * begin from author
				 */
				
				if ( $message_result['user_id'] == -1){
				
					/**
					 * author is deleted
					 */
					
					$pm_display_parts['author_status'] = false;
					
					$pm_display_strings['AUTHOR_NAME'] = $message_result['users_group_prefix'].$message_result['users_message_author_name'].$message_result['users_group_suffix'];
					
				}else{
					
					/**
					 * author exists
					 */
					
					if ( $settings['users_count_online']){
					
						$pm_display_parts['author_status'] = true;
					
						$pm_display_strings['AUTHOR_STATUS_IMG'] = $style -> drawStatus( $users -> checkOnLine( $message_result['users_message_author']));
						
					}else{
						
						$pm_display_parts['author_status'] = false;
						
					}
					
					/**
					 * and user login
					 */
					
					$pm_display_strings['AUTHOR_NAME'] = '<a href="'.parent::systemLink( 'user', array( 'user' => $message_result['users_message_author'])).'">'.$message_result['users_group_prefix'].$message_result['user_login'].$message_result['users_group_suffix'].'</a>';				
					
				}
				
				/**
				 * message info
				 */
				
				if ( !$message_result['users_message_readed']){
					$msg_image = $style -> drawImage( 'topic_new', $language -> getString( 'user_cp_section_messenger_msgs_list_unread'));
				}else{
					$msg_image = $style -> drawImage( 'topic', $language -> getString( 'user_cp_section_messenger_msgs_list_read'));
				}
				
				$message_info_title = $language -> getString('user_cp_section_messenger_msgs_received_date').': '.$time -> drawDate( $message_result['users_message_send_time']);
				
				if ( $message_result['users_message_receive_time'] != 0)
					$message_info_title .= ', '.$language -> getString('user_cp_section_messenger_msgs_received_date_read').": ".$time -> drawDate( $message_result['users_message_receive_time']);
				
				/**
				 * receiver now
				 */
				
				if ( $_GET['in'] == true ){
					
					$receiver_query = $mysql -> query( "SELECT u.user_id, u.user_login, u.user_warns, u.user_rep, u.user_posts_num, g.users_group_prefix, g.users_group_suffix FROM users u LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE u.user_id = '".$message_result['users_message_receiver']."'");
					if ( $receiver_result = mysql_fetch_array( $receiver_query, MYSQL_ASSOC)){
						
						$receiver_result = $mysql -> clear( $receiver_result);
						
						$message_info_title .= ', '.$language -> getString('user_cp_section_messenger_msgs_received_receiver').': <a href="'.parent::systemLink( 'user', array( 'user' => $receiver_result['user_id'])).'">'.$receiver_result['users_group_prefix'].$receiver_result['user_login'].$receiver_result['users_group_suffix'].'</a>';
										
					}
				
				}
				
				
				$pm_display_strings['INFO'] = $msg_image.' '.$message_info_title;
				
				/**
				 * reputation
				 */
				
				if ( $settings['reputation_turn']){
					
					$pm_display_parts['reps'] = true;
					
					$pm_display_strings['PROFILE_REPUTATION'] = $language -> getString( 'user_reputation');
					$pm_display_strings['USER_REPUTATION'] = $users -> drawReputation( $users -> countReputation( $message_result['user_rep'], $message_result['user_posts_num'], $message_result['user_regdate']));
					
				}else{
					
					$pm_display_parts['reps'] = false;
					
				}
				
				/**
				 * warns
				 */
						
				if ( $settings['warns_turn'] && $receiver_result['post_author'] != -1){
										
					/**
					 * check if we can see warns
					 */
					
					if ( $settings['warns_show'] == 0 || ( $settings['warns_show'] == 1 && $session -> user['user_id'] != -1) || ( $settings['warns_show'] == 2 && ($session -> user['user_can_be_mod'] || ($session -> user['user_id'] != -1 && $session -> user['user_id'] == $message_result['post_author'])))){
					
						/**
						 * draw warns
						 */
						
						$pm_display_parts['warns'] = true;
						
						$pm_display_strings['PROFILE_WARNS'] = $language -> getString( 'user_warns');							
						
						if ( $message_result['user_warns'] > 0){
							
							$warns_link_open = '<a href="'.parent::systemLink( 'user_warns', array( 'user' => $message_result['users_message_author'])).'">';
							$warns_link_close = '</a>';
							
						}else{
							
							$warns_link_open = '';
							$warns_link_close = '';
							
						}
												
						/**
						 * draw warns, or wanrs + mod?
						 */
						
						if ( $session -> user['user_can_be_mod']){
							
							$pm_display_strings['USER_WARNS'] = '<a href="'.parent::systemLink( 'mod', array( 'user' => $message_result['users_message_author'], 'd' => '1')).'" title="'.$language -> getString( 'user_warn_decrease').'">'.$style -> drawImage( 'minus').'</a> '.$warns_link_open.$users -> drawWarnLevel( $message_result['user_warns']).$warns_link_close.' <a href="'.parent::systemLink( 'mod', array( 'user' => $message_result['users_message_author'], 'd' => '0')).'" title="'.$language -> getString( 'user_warn_add').'">'.$style -> drawImage( 'plus').'</a>';
							
						}else{
							
							$pm_display_strings['USER_WARNS'] = $warns_link_open.$users -> drawWarnLevel( $message_result['user_warns']).$warns_link_close;
														
						}
						
					}else{
						
						/**
						 * cant see warns
						 */
						
						$pm_display_parts['warns'] = false;
					
					}
					
				}else{
					
					$pm_display_parts['warns'] = false;
					
				}
				
				/**
				 * author profile
				 */
				
				if ( $message_result['user_avatar_type'] != 0 && $settings['users_can_avatars'] && $session -> user['user_show_avatars']){
					
					$pm_display_parts['avatar'] = true;
					
					/**
					 * draw avatar
					 */
					
					$pm_display_strings['AUTHOR_AVATAR'] = $users -> drawAvatar( $message_result['user_avatar_type'], $message_result['user_avatar_image'], $message_result['user_avatar_width'], $message_result['user_avatar_height']);				
					
				}else{
					
					$pm_display_parts['avatar'] = false;
					
				}
				
				/**
				 * title
				 */
				
				if ( strlen( $message_result['user_custom_title']) > 0 && $settings['users_posts_to_title'] > 0 && $message_result['user_posts_num'] >= $settings['users_posts_to_title']){
						
					$pm_display_strings['AUTHOR_TITLE'] = $message_result['user_custom_title'];
					
				}else if ( strlen( $message_result['users_group_title']) > 0){
					
					$pm_display_strings['AUTHOR_TITLE'] = $message_result['users_group_title'];
					
				}else{
					
					$pm_display_strings['AUTHOR_TITLE'] = $users -> drawRankName( $message_result['user_posts_num']);
										
				}
			
				if ( strlen( $users -> users_groups[$message_result['user_main_group']]['users_group_image']) > 0){
					$pm_display_strings['AUTHOR_RANK'] = '<img src="'.$users -> users_groups[$message_result['user_main_group']]['users_group_image'].'" alt="" title""/>';
				}else{
					$pm_display_strings['AUTHOR_RANK'] = $users -> drawRankImage( $message_result['user_posts_num']);
				}
								
				/**
				 * post text
				 */
				
				$message_text = $message_result['users_message_text'];
				
				/**
				 * check badwords
				 */
								
				if ( !$users -> cantCensore( $sender_groups)){
					$message_text = $strings -> censore( $message_text);
				}
				
				/**
				 * add it
				 */
									
				$pm_display_strings['POST_TEXT'] = $strings -> parseBB( nl2br( $message_text), true, true);

				//limit it
				if( $settings['message_big_cut'] > 0 && !defined( 'SIMPLE_MODE')){
					
					$pm_display_strings['POST_TEXT'] = '<div style="max-height: '.$settings['message_big_cut'].'px;overflow: auto">'.$pm_display_strings['POST_TEXT'].'<div>';
					
				}
				
				/**
				 * signature
				 */
				
				if ( strlen( $message_result['user_signature']) > 0 && $session -> user['user_show_sigs'] && $settings['users_can_sigs']){
					
					$pm_display_parts['signature'] = true;
					$pm_display_strings['SIGNATURE'] = $strings -> parseBB( nl2br( $message_result['user_signature']), $settings['users_allow_bbcodes_in_sigs'], $settings['users_allow_emoticones_in_sigs']);
				
				}else{
					
					$pm_display_parts['signature'] = false;
						
				}
				
				
				$pm_display_strings['PROFILE_GROUP'] = $language -> getString( 'user_group');
				$pm_display_strings['USER_GROUP'] = $message_result['users_group_prefix'].$message_result['users_group_name'].$message_result['users_group_suffix'];
				
				$pm_display_strings['PROFILE_POSTS'] = $language -> getString( 'user_posts');
				$pm_display_strings['USER_POSTS'] = $message_result['user_posts_num'];
				
				$pm_display_parts['posts'] = true;
				$pm_display_parts['reported'] = false;
					
				if ( strlen( $message_result['user_localisation']) != 0){
					
					$pm_display_parts['author_location'] = true;
					$pm_display_strings['PROFILE_LOCATION'] = $language -> getString( 'user_localisation');
					$pm_display_strings['USER_LOCATION'] = $message_result['user_localisation'];
				
				}else{
					
					$pm_display_parts['author_location'] = false;
					
				}
				
				
				$pm_display_strings['PROFILE_JOIN_DATE'] = $language -> getString( 'user_registration');
				$pm_display_strings['USER_JOIN_DATE'] = $time -> drawDate( $message_result['user_regdate']);

				/**
				 * custom fields
				 */
				
				$drawed_custom_fields = '';
				
				if ( $message_result['user_id'] != -1){
					
					if ( count( $users -> custom_fields) > 0){
						
						foreach ( $users -> custom_fields as $field_id => $field_ops) {
							
							if ( strlen( $message_result['field_'.$field_id]) > 0 && $field_ops['profile_field_inposts'] && (!$field_ops['profile_field_private'] || ( $field_ops['profile_field_private'] && ( $message_result['user_id'] == $session -> user['user_id'] || $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate'])))){
								
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
									
									$field_template = str_ireplace( '{KEY}', $message_result['field_'.$field_id], $field_template);
									$field_template = str_ireplace( '{VALUE}', $made_options[$message_result['field_'.$field_id]], $field_template);
									
								}else{
									
									$field_template = str_ireplace( '{VALUE}', $message_result['field_'.$field_id], $field_template);
															
								}
														
								$drawed_custom_fields .= '<br />'.$field_template;
								
							}
							
						}
						
					}
					
				}
				
				$pm_display_strings['FIELDS'] = $drawed_custom_fields;
								
				/**
				 * shortcuts now
				 */
				
				$shortcuts_list = array();
				
				/**
				 * mail
				 */
				
				if ( $message_result['user_want_mail'] && $session -> user['user_can_send_mails']){
					
					if ( $message_result['user_show_mail']){
					
						/**
						 * user show his mail, draw it
						 */
						
						$send_mail_link = 'mailto:'.$message_result['user_mail'];
						
					}else{
						
						/**
						 * user not shows his mail, we have to send it "round the way"
						 */
						
						$mail_user_target = array( 'user' => $message_result['user_id']);
						
						$send_mail_link = parent::systemLink( 'mail_user', $mail_user_target);
						
					}
					
					$shortcuts_list[] = '<a href="'.$send_mail_link.'">'.$style -> drawImage( 'button_email', $language -> getString( 'user_mail_send')).'</a>';
					
				}
				
				/**
				 * pm
				 */
				
				$send_pw_link = array( 'do' => 'new_pm', 'user' => $message_result['user_id']);
					
				$shortcuts_list[] = '<a href="'.parent::systemLink( 'profile', $send_pw_link).'">'.$style -> drawImage( 'button_pm', $language -> getString( 'user_pm_send')).'</a>';
				
				/**
				 * www
				 */
					
				if ( !empty( $message_result['user_web'])){
					
					if ( substr( $message_result['user_web'], 0, 7) != "http://")
						$message_result['user_web'] = "http://".$message_result['user_web'];
						
					$shortcuts_list[] = '<a href="'.$message_result['user_web'].'">'.$style -> drawImage( 'button_www', $language -> getString( 'user_www')).'</a>';
				
				}
				
				/**
				 * display
				 */
				
				$pm_display_strings['SHORTCUTS'] = join( " ", $shortcuts_list);
				
				/**
				 * and actions now
				 */
				
				$pm_actions = array();
				
				/**
				 * check if user exists, if yes, add reply buttton
				 */
				
				if ( $message_result['user_id'] != -1 && $_GET['in'] != true){
					
					$reply_pw_link = array( 'do' => 'new_pm', 'user' => $message_result['user_id'], 'reply' => $message_to_draw);
					
					$pm_actions[] = '<a href="'.parent::systemLink( 'profile', $reply_pw_link).'">'.$style -> drawImage( 'button_reply', $language -> getString( 'user_cp_section_messenger_msgs_received_reply')).'</a>';
				
					
				}
				
				/**
				 * delete button
				 */
				
				if ( $_GET['in'] == true){
					
					if ( $message_result['users_message_receive_time'] == 0){
						
						$delete_pw_link = array( 'do' => 'pms_outbox', 'delete' => $message_to_draw);
						$pm_actions[] = '<a href="'.parent::systemLink( 'profile', $delete_pw_link).'">'.$style -> drawImage( 'button_delete', $language -> getString( 'user_cp_section_messenger_msgs_received_delete')).'</a>';
					
					}
					
				}else{
					
					$delete_pw_link = array( 'do' => 'pms_inbox', 'delete' => $message_to_draw);	
					$pm_actions[] = '<a href="'.parent::systemLink( 'profile', $delete_pw_link).'">'.$style -> drawImage( 'button_delete', $language -> getString( 'user_cp_section_messenger_msgs_received_delete')).'</a>';
				
				}
				
				/**
				 * display actions
				 */
				
				$pm_display_strings['ACTIONS'] = join( " ", $pm_actions);
				
				$pm_display_parts['edit'] = false;
				$pm_display_parts['edit_info'] = false;
				$pm_display_parts['edit_reason'] = false;
				$pm_display_parts['attachment'] = false;	
				$pm_display_parts['thanks'] = false;						
				
				/**
				 * run parser
				 */
				
				$post_show_form -> addToContent( $style -> drawPost( 0, $pm_display_parts, $pm_display_strings));
				
				/**
				 * close
				 */
				
				$post_show_form -> closeTable();
	
				if ( $_GET['in'] == true){
					parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_messenger_msgs_send'), $post_show_form -> display()));
				}else{
					parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_messenger_msgs_received'), $post_show_form -> display()));
				}
				/**
				 * update stats
				 */
				
				if ( !$message_result['users_message_readed'] && $_GET['in'] != true){
					
					$update_msg_sql['users_message_receive_time'] = time();
					$update_msg_sql['users_message_readed'] = true;
					
					$mysql -> update( $update_msg_sql, 'users_messages', "`users_message_id` = '$message_to_draw' AND `users_message_receiver` = '".$session -> user['user_id']."'");
					
					$unread_pms_num = $mysql -> countRows( 'users_messages', " `users_message_receiver` = '".$session -> user['user_id']."' AND `users_message_readed` = '0'");
					$update_users_stats_sql['user_pm_new_num'] = $unread_pms_num;
					
					$mysql -> update( $update_users_stats_sql, 'users', "`user_id` = '".$session -> user['user_id']."'");
					
					
				}
				
			}else{
				
				/**
				 * message not found
				 */
							
				if ( $_GET['in'] == true){
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_messenger_msgs_send'), $language -> getString( 'user_cp_section_messenger_noaccess')));

					$this -> action_pms_outbox();
				
				}else{

					parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_messenger_msgs_received'), $language -> getString( 'user_cp_section_messenger_msgs_received_notfound')));
				
					$this -> action_pms_inbox();
				
				}
						
			}
			
			
		}else{
			
			/**
			 * no perms, draw error
			 */
			
			if ( $_GET['in'] == true){
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_messenger_msgs_send'), $language -> getString( 'user_cp_section_messenger_noaccess')));
			}else{
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_messenger_msgs_received'), $language -> getString( 'user_cp_section_messenger_noaccess')));	
			}
		}
		
	}
	
	/**
	 * draws outbox
	 */
	
	function action_pms_outbox(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check perms
		 */
		
		if ( $session -> user['user_can_use_pm']){
			
			/**
			 * check actions
			 */
			
			if ( isset( $_GET['delete']) && !empty( $_GET['delete'])){
				
				/**
				 * delete exact message
				 * select it from mysql
				 */
				
				$msg_to_delete = $_GET['delete'];
				
				settype( $msg_to_delete, 'integer');
				
				$msg_query = $mysql -> query( "SELECT users_message_receiver FROM `users_messages` WHERE `users_message_id` = '$msg_to_delete' AND `users_message_author` = '".$session -> user['user_id']."' AND `users_message_receive_time` = '0'");
				
				if ( $msg_deletion_result = mysql_fetch_array( $msg_query, MYSQL_ASSOC)){
				
					$mysql -> delete( 'users_messages', "`users_message_id` = '$msg_to_delete' AND `users_message_author` = '".$session -> user['user_id']."'");
					
					/**
					 * resynchornize stats
					 */
					
					$pms_num = $mysql -> countRows( 'users_messages', " `users_message_receiver` = '".$msg_deletion_result['users_message_receiver']."'");
				
					$unread_pms_num = $mysql -> countRows( 'users_messages', " `users_message_receiver` = '".$msg_deletion_result['users_message_receiver']."' AND `users_message_readed` = '0'");
				
					$update_users_stats_sql['user_pm_num'] = $pms_num;
					$update_users_stats_sql['user_pm_new_num'] = $unread_pms_num;
					
					$mysql -> update( $update_users_stats_sql, 'users', "`user_id` = '".$msg_deletion_result['users_message_receiver']."'");
										
				}
			}
			
			if ( $session -> checkForm()){
				
				/**
				 * do actions
				 */
				
				$pms_to_maniulate = $_POST[ 'select_msg'];
				
				settype( $pms_to_maniulate, 'array');
				
				$pms_to_maniulate_ids = array();
				
				foreach ( $pms_to_maniulate as $pm_id => $pm_manipulate)
					$pms_to_maniulate_ids[] = $pm_id;
								
				if ( !empty( $pms_to_maniulate_ids)){
					
					foreach ( $pms_to_maniulate_ids as $pm_to_del_id){
						
						settype( $pm_to_del_id, 'integer');
				
						$msg_query = $mysql -> query( "SELECT users_message_receiver FROM `users_messages` WHERE `users_message_id` = '$pm_to_del_id' AND `users_message_author` = '".$session -> user['user_id']."' AND `users_message_receive_time` = '0'");
					
						if ( $msg_deletion_result = mysql_fetch_array( $msg_query, MYSQL_ASSOC)){

								$mysql -> delete( 'users_messages', "`users_message_id` = '$pm_to_del_id' AND `users_message_author` = '".$session -> user['user_id']."'");
					
								/**
								 * resynchornize stats
								 */
								
								$pms_num = $mysql -> countRows( 'users_messages', " `users_message_receiver` = '".$msg_deletion_result['user_id']."'");
							
								$unread_pms_num = $mysql -> countRows( 'users_messages', " `users_message_receiver` = '".$msg_deletion_result['user_id']."' AND `users_message_readed` = '0'");
							
								$update_users_stats_sql['user_pm_num'] = $pms_num;
								$update_users_stats_sql['user_pm_new_num'] = $unread_pms_num;
								
								$mysql -> update( $update_users_stats_sql, 'users', "`user_id` = '".$msg_deletion_result['user_id']."'");
								
						}
					}
										
				}
			}
			
			/**
			 * count pms
			 */
			
			if ( !isset( $pms_num))
			$pms_num = $mysql -> countRows( 'users_messages', " `users_message_author` = '".$session -> user['user_id']."'");
			
			/**
			 * draw summary at the top
			 */
			
			$this -> pmsSummary( $pms_num);
			
			/**
			 * build paginator
			 */
			
			$pms_per_page = $settings['users_pm_draw'];
			
			settype( $pms_per_page, 'integer');
			
			if ( $pms_per_page < 1)
				$pms_per_page = 1;
			
			$pages_num = floor( $pms_num / $pms_per_page);
					
			/**
			 * get current page
			 */
			
			$current_page = $_GET['p'];
			
			$current_page --;
			
			settype( $current_page, 'integer');
			
			if ( $current_page < 0)
				$current_page = 1;
				
			if ( $current_page > $pages_num)
				$current_page = $pages_num;
				
			/**
			 * begin drawing form
			 */
			
			$inbox_content = new form();
			$inbox_content -> openForm( parent::systemLink( parent::getId(), array( 'do' => 'pms_outbox')));
			$inbox_content -> openOpTable();
			$inbox_content -> addToContent( '<tr>
					<th colspan="2">'.$language -> getString( 'user_cp_section_messenger_msgs_list_title').'</th>
					<th>'.$language -> getString( 'user_cp_section_messenger_msgs_list_time').'</th>
					<th>'.$language -> getString( 'user_cp_section_messenger_msgs_list_receiver').'</th>
					<th>&nbsp;</th>
				</tr>');
			
			/**
			 * select
			 */
			
			$messages_query = $mysql -> query( "SELECT m.users_message_id, m.users_message_receiver, m.users_message_receive_time, m.users_message_send_time, m.users_message_subject, u.user_id, u.user_login, u.user_main_group, u.user_other_groups, g.users_group_prefix, g.users_group_suffix FROM users_messages m LEFT JOIN users u ON m.users_message_receiver = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE m.users_message_author = '".$session -> user['user_id']."' ORDER BY m.users_message_send_time DESC LIMIT ".($current_page * $pms_per_page).", $pms_per_page");
			
			while( $message_result = mysql_fetch_array( $messages_query, MYSQL_ASSOC)){
				
				//clear result
				$message_result = $mysql -> clear( $message_result);
				
				//sender groups
				$sender_groups = array();
				$sender_groups = split( "'", $message_result['user_other_groups']);
				$sender_groups[] = $message_result['user_main_group'];
				
				/**
				 * message title
				 */
				
				$message_title = $message_result[ 'users_message_subject'];
				
				if ( !$users -> cantCensore( $sender_groups)){
					
					$message_title = $strings -> censore( $message_title);
					
				}
				
				/**
				 * readed?
				 */
				
				if (  $message_result[ 'users_message_receive_time'] == 0){
					
					$msg_read_info = '<br /><i>'.$language -> getString( 'user_cp_section_messenger_msgs_list_unread_info').'</i>';
					$message_icon = $style -> drawImage( 'topic_new', $language -> getString( 'user_cp_section_messenger_msgs_list_unread'));
					
					$message_check = $inbox_content -> drawSelect( 'select_msg['.$message_result[ 'users_message_id'].']');
					
				}else{
					
					$msg_read_info = '<br />'.$language -> getString( 'user_cp_section_messenger_msgs_list_read_info').' '.$time -> drawDate( $message_result[ 'users_message_receive_time']);
					$message_icon = $style -> drawImage( 'topic', $language -> getString( 'user_cp_section_messenger_msgs_list_read'));
					
					$message_check = '&nbsp;';
				}
				
				/**
				 * author
				 */
				
				$message_author = '<a href="'.parent::systemLink( 'user', array( 'user' => $message_result[ 'users_message_receiver'])).'">'.$message_result[ 'users_group_prefix'].$message_result[ 'user_login'].$message_result[ 'users_group_suffix'].'</a>';
								
				/**
				 * add row
				 */
				
				$inbox_content -> addToContent( '<tr>
					<td class="opt_row2" style="text-align: center">'.$message_icon.'</td>
					<td class="opt_row1" style="width: 100%"><a href="'.parent::systemLink( parent::getId(), array('do' => 'show_pm', 'msg' => $message_result[ 'users_message_id'], 'in' => '1')).'">'.$forums -> cutTopicName( $message_title).'</a>'.$msg_read_info.'</td>
					<td class="opt_row2" style="text-align: center" NOWRAP="nowrap">'.$time -> drawDate( $message_result[ 'users_message_send_time']).'</td>
					<td class="opt_row1" style="text-align: center" NOWRAP="nowrap">'.$message_author.'</td>
					<td class="opt_row3" style="text-align: center" NOWRAP="nowrap">'.$message_check.'</td>
				</tr>');
				
			}
			
			$inbox_content -> closeTable();
			
			/**
			 * actions
			 */

			$inbox_content -> drawButton( $language -> getString( 'user_cp_section_messenger_msgs_list_do_delete_selected'));
			$inbox_content  -> closeForm();
			
			/**
			 * display list
			 */
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_messenger_msgs_send'), $inbox_content -> display()));
			
			/**
			 * and paginator
			 */
			
			parent::draw( $style -> drawPaginator( parent::systemLink( parent::getId(), array('do' => 'pms_inbox')), 'p', $pages_num, ($current_page+1)));
			
		}else{
			
			/**
			 * no perms, draw error
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_messenger_msgs_send'), $language -> getString( 'user_cp_section_messenger_noaccess')));
			
		}
	}
	
	/**
	 * draws forums subscriptions
	 */
	
	function action_subs_forums(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		if ( $settings['subscriptions_turn']){
		
			/**
			 * delete chosen forums
			 */
			
			if ( $_GET['delete'] && $session -> checkForm()){
				
				$subs_to_delete = $_POST['delete_sub'];
				
				if ( gettype( $subs_to_delete) == 'array'){
					
					/**
					 * create proper list
					 */
					
					$forums_ids = array();
					
					foreach ( $subs_to_delete as $sub_forum => $sub_id){
						
						$forums_ids[] = $sub_forum;
						
					}
					
					if ( count( $forums_ids) > 0){
						
						$mysql -> delete( 'subscriptions_forums', "`subscription_forum` IN (".join( ",", $forums_ids).") AND `subscription_forum_user` = '".$session -> user['user_id']."'");
						
						parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_subscriptions_forums'), $language -> getString( 'user_cp_section_subscriptions_forums_deleted')));
						
					}
					
				}
				
			}
			
			/**
			 * begin drawign list of observed forums
			 */
			
			$observed_forums_list = new form();
			$observed_forums_list -> openForm( parent::systemLink( parent::getId(), array( 'do' => 'subs_forums', 'delete' => true)));
			$observed_forums_list -> openOpTable();
			
			/**
			 * got any subs?
			 */
			
			$got_subs = false;
			
			/**
			 * select subs
			 */
			
			$subs_query = $mysql -> query( "SELECT s.*, f.forum_id, f.forum_name, f.forum_threads, f.forum_posts FROM subscriptions_forums s
			LEFT JOIN forums f ON s.subscription_forum = f.forum_id
			WHERE s.subscription_forum_user = '".$session -> user['user_id']."'
			ORDER BY s.subscription_forum_time DESC");
				
			while ( $sub_result = mysql_fetch_array( $subs_query, MYSQL_ASSOC)) {
				
				//clear result
				$sub_result = $mysql -> clear( $sub_result);
				
				if ( $session -> canSeeTopics( $sub_result['forum_id'])){
					
					/**
					 * got subs
					 */
					
					$got_subs = true;
					
					/**
					 * set all keys
					 */
					
					if ( $sub_result['subscription_forum_time'] > 0){
						
						$language -> setKey( 'last_update_time', $time -> drawDate( $sub_result['subscription_forum_time']));
						
					}else{
						
						$language -> setKey( 'last_update_time', $language -> getString( 'time_never'));
						
					}
					
					/**
					 * new posts and topics
					 */
					
					$new_topics_num = $sub_result['forum_threads'] - $sub_result['subscription_forum_topics'];
					$new_posts_num = $sub_result['forum_posts'] - $sub_result['subscription_forum_posts'];
					
					$language -> setKey( 'new_topics_num', $new_topics_num);
					$language -> setKey( 'new_posts_num', $new_posts_num);
										
					/**
					 * define info string
					 */
					
					if ( $new_posts_num > 0 && $new_topics_num > 0){
						$sub_info = $language -> getString( 'user_cp_section_subscriptions_forums_list_forum_info');
					}else if ( $new_posts_num > 0){
						$sub_info = $language -> getString( 'user_cp_section_subscriptions_forums_list_forum_info_no_new_topics');
					}else if ( $new_topics_num > 0){
						$sub_info = $language -> getString( 'user_cp_section_subscriptions_forums_list_forum_info_no_new_posts');
					}else{
						$sub_info = $language -> getString( 'user_cp_section_subscriptions_forums_list_forum_info_no_new');
					}
					
					/**
					 * add row
					 */
					
					$observed_forums_list -> addToContent( '<tr>
						<td class="opt_row1" style="width: 100%"><a href="'.parent::systemLink( 'forum', array( 'forum' => $sub_result['forum_id'])).'">'.$sub_result['forum_name'].'</a><br />'.$sub_info.'</td>
						<td class="opt_row3">'.$observed_forums_list -> drawSelect( 'delete_sub['.$sub_result['subscription_forum'].']').'</td>
					</tr>');
					
				}
				
			}
			
			if ( !$got_subs){
				
				$observed_forums_list -> addToContent( '<tr>
						<td class="opt_row1" style="text-align: center">'.$language -> getString( 'user_cp_section_subscriptions_forums_none').'</td>
					</tr>');
			}
			
			/**
			 * close table
			 */
			
			$observed_forums_list -> closeTable();
			
			if ( $got_subs)
				$observed_forums_list -> drawButton( $language -> getString( 'user_cp_section_subscriptions_delete_selected'));
			
			$observed_forums_list -> closeForm();
			
			/**
			 * display
			 */
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_subscriptions_forums'), $observed_forums_list -> display()));
			
		}else{
			
			parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_subscriptions_forums'), $language -> getString( 'user_cp_section_subscriptions_forums_off')));
		
		}
			
	}
	
	/**
	 * draws topics subscriptions
	 */
	
	function action_subs_topics(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		if ( $settings['subscriptions_turn']){
		
			/**
			 * delete chosen forums
			 */
			
			if ( $_GET['delete'] && $session -> checkForm()){
				
				$subs_to_delete = $_POST['delete_sub'];
				
				if ( gettype( $subs_to_delete) == 'array'){
					
					/**
					 * create proper list
					 */
					
					$forums_ids = array();
					
					foreach ( $subs_to_delete as $sub_forum => $sub_id){
						
						$forums_ids[] = $sub_forum;
						
					}
					
					if ( count( $forums_ids) > 0){
						
						$mysql -> delete( 'subscriptions_topics', "`subscription_topic` IN (".join( ",", $forums_ids).") AND `subscription_topic_user` = '".$session -> user['user_id']."'");
						
						parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_subscriptions_topics'), $language -> getString( 'user_cp_section_subscriptions_topics_deleted')));
						
					}
					
				}
				
			}
			
			
			/**
			 * begin drawign list of observed forums
			 */
			
			$observed_topics_list = new form();
			$observed_topics_list -> openForm( parent::systemLink( parent::getId(), array( 'do' => 'subs_topics', 'delete' => true)));
			$observed_topics_list -> openOpTable();
			
			/**
			 * got any subs?
			 */
			
			$got_subs = false;
			
			/**
			 * select subs
			 */
			
			$subs_query = $mysql -> query( "SELECT s.*, t.topic_id, t.topic_forum_id, t.topic_name, t.topic_info, t.topic_posts_num FROM subscriptions_topics s
			LEFT JOIN topics t ON s.subscription_topic = t.topic_id
			WHERE s.subscription_topic_user = '".$session -> user['user_id']."'
			ORDER BY s.subscription_topic_time DESC");
				
			while ( $sub_result = mysql_fetch_array( $subs_query, MYSQL_ASSOC)) {
				
				//clear result
				$sub_result = $mysql -> clear( $sub_result);
				
				if ( $session -> canSeeTopics( $sub_result['topic_forum_id'])){
					
					/**
					 * got subs
					 */
					
					$got_subs = true;
					
					/**
					 * set all keys
					 */
					
					if ( $sub_result['subscription_topic_time'] > 0){
						
						$language -> setKey( 'last_update_time', $time -> drawDate( $sub_result['subscription_topic_time']));
						
					}else{
						
						$language -> setKey( 'last_update_time', $language -> getString( 'time_never'));
						
					}
					
					/**
					 * new posts and topics
					 */
					
					$new_posts_num = $sub_result['topic_posts_num'] - $sub_result['subscription_topic_posts'];
					
					$language -> setKey( 'new_posts_num', $new_posts_num);
										
					/**
					 * define info string
					 */
					
					if ( $new_posts_num > 0){
						$sub_info = $language -> getString( 'user_cp_section_subscriptions_topics_list_topic_info');
					}else{
						$sub_info = $language -> getString( 'user_cp_section_subscriptions_topics_list_topic_info_no_new');
					}
					
					/**
					 * topic info
					 */
					
					if ( strlen( $sub_result['topic_info']) > 0){
						
						$topic_info = ' - '.$sub_result['topic_info'];
					
					}else{
						
						$topic_info = '';
						
					}
					
					/**
					 * add row
					 */
					
					$observed_topics_list -> addToContent( '<tr>
						<td class="opt_row1" style="width: 100%"><a href="'.parent::systemLink( 'topic', array( 'topic' => $sub_result['topic_id'])).'">'.$sub_result['topic_name'].$topic_info.'</a><br />'.$sub_info.'</td>
						<td class="opt_row3">'.$observed_topics_list -> drawSelect( 'delete_sub['.$sub_result['subscription_topic'].']').'</td>
					</tr>');
					
				}
				
			}
			
			if ( !$got_subs){
				
				$observed_topics_list -> addToContent( '<tr>
						<td class="opt_row1" style="text-align: center">'.$language -> getString( 'user_cp_section_subscriptions_topics_none').'</td>
					</tr>');
			}
			
			/**
			 * close table
			 */
			
			$observed_topics_list -> closeTable();
			
			if ( $got_subs)
				$observed_topics_list -> drawButton( $language -> getString( 'user_cp_section_subscriptions_delete_selected'));
			
			$observed_topics_list -> closeForm();
			
			/**
			 * display
			 */
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_subscriptions_topics'), $observed_topics_list -> display()));
		
		}else{
			
			parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_subscriptions_topics'), $language -> getString( 'user_cp_section_subscriptions_topics_off')));
			
		}
				
	}
	
	/**
	 * draws profile edition
	 */
	
	function action_edit_profile(){

		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * update profile data
		 */
		
		if ( $_POST['update_profile'] && $session -> checkForm()){
			
			$user_birth_day = $_POST['birth_day'];
			$user_birth_month = $_POST['birth_month'];
			$user_birth_year = $_POST['birth_year'];
			
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
				
			if ( !empty( $_POST['birth_day']) && !empty( $_POST['birth_month']) && !empty( $_POST['birth_year'])){
				$user_birth_date = $user_birth_day.'-'.$user_birth_month.'-'.$user_birth_year;
			}else{
				$user_birth_date = '';
			}
			
			$profile_update_mysql['user_name'] = $strings -> inputClear( $_POST['user_name'], false);
			$profile_update_mysql['user_birth_date'] = $user_birth_date;	
			$profile_update_mysql['user_gender'] = $strings -> inputClear( $_POST['user_gender'], false);
			$profile_update_mysql['user_web'] = $strings -> inputClear( $_POST['user_web'], false);
			$profile_update_mysql['user_jabber_id'] = $strings -> inputClear( $_POST['user_jabber_id'], false);
			$profile_update_mysql['user_localisation'] = $strings -> inputClear( $_POST['user_localisation'], false);
			
			if ( $settings['users_posts_to_title'] > 0 && $session -> user['user_posts_num'] >= $settings['users_posts_to_title']){
				
				$profile_update_mysql['user_custom_title'] = $strings -> inputClear( $_POST['user_custom_title'], false);
				$session -> user['user_custom_title'] = stripslashes( $strings -> inputClear( $_POST['user_custom_title'], false));
				$style -> drawString( 'USER_TITLE', $session -> user['user_custom_title']);
			
			}
			
			if ( $settings['user_interests_max_lenght'] != 0 && strlen( trim( $_POST['user_interests'])) > $settings['user_interests_max_lenght']){
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_profile'), $language -> getString( 'user_cp_interests_too_long')));
				
			}else{
				
				$profile_update_mysql['user_interests'] = $strings -> inputClear( $_POST['user_interests'], false);
				
			}
			
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
					
					if ( !$field_ops['profile_field_byteam']){
						
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
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_profile'), $language -> getString( 'user_cp_need_error')));
				
						}else{
							
							if ( strlen( $new_field_value) > $field_ops['profile_field_length'] && $field_ops['profile_field_length'] > 0){
								
								$language -> setKey( 'field_name', $field_ops['profile_field_name']);
								$language -> setKey( '$field_length', $field_ops['profile_field_length']);
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_profile'), $language -> getString( 'user_cp_need_error')));
								
								if ( $field_ops['profile_field_require'])
									$procced = false;
												
							}else{
								
								$new_field_value = $strings -> inputClear( $new_field_value, false);
								
								$fields_update_mysql[ 'field_'.$field_id] = $new_field_value;
								
							}
							
						}
						
					}
					
				}
				
			}
			
			/**
			 * update mysql
			 */
			
			if ( $procced){
				
				$mysql -> update( $profile_update_mysql, 'users', "`user_id` = '".$session -> user['user_id']."'");
				
				if ( count( $fields_update_mysql) > 0)
					$mysql -> update( $fields_update_mysql, 'profile_fields_data', "`profile_fields_user` = '".$session -> user['user_id']."'");
				
				parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_personal_change_profile'), $language -> getString( 'user_cp_profile_changed')));
					
			}
			
		}
		
		/**
		 * select profile data from mysql
		 */
		
		$profile_select_sql = $mysql -> query( "SELECT u.*, f.* FROM users u 
		LEFT JOIN profile_fields_data f ON u.user_id = f.profile_fields_user
		WHERE u.user_id = '".$session -> user['user_id']."'");
		
		if ( $profile_result = mysql_fetch_array( $profile_select_sql, MYSQL_ASSOC))
			$profile_result = $mysql -> clear($profile_result);
		
		/**
		 * do we have additional fields?
		 */
				
		if ( $profile_result['profile_fields_user'] != $session -> user['user_id'])
			$mysql -> insert( array( "profile_fields_user" => $session -> user['user_id']), 'profile_fields_data');
		
		/**
		 * begin drawing
		 */
		
		$update_profile_link = array( 'do' => 'edit_profile');
		
		$profile_edit_form = new form();
		$profile_edit_form -> openForm( parent::systemLink( parent::getId(), $update_profile_link));
		$profile_edit_form -> hiddenValue( 'update_profile', true);
		$profile_edit_form -> openOpTable();
		
		/**
		 * check, if we can set our own title
		 */
		
		if ( $settings['users_posts_to_title'] > 0 && $session -> user['user_posts_num'] >= $settings['users_posts_to_title']){
		
			$profile_edit_form -> drawTextInput( $language -> getString( 'user_cp_section_personal_change_profile_title'), 'user_custom_title', $profile_result['user_custom_title']);
		
		}
		
		$profile_edit_form -> drawTextInput( $language -> getString( 'user_cp_section_personal_change_profile_name'), 'user_name', $profile_result['user_name']);
		
		$birth_date = split("-", $profile_result['user_birth_date']);
		
		$profile_edit_form -> drawInfoRow( $language -> getString( 'user_cp_section_personal_change_profile_birth'), '<input name="birth_day" type="text" size="2" maxlength="2" value="'.$birth_date[0].'"/> - <input name="birth_month" type="text" size="2" maxlength="2" value="'.$birth_date[1].'"/> - <input name="birth_year" type="text" size="4" maxlength="4" value="'.$birth_date[2].'"/>', $language -> getString( 'user_cp_section_personal_change_profile_birth_help'));
		
		$genders_list[0] = $language -> getString( 'gender_0');
		$genders_list[1] = $language -> getString( 'gender_1');
		$genders_list[2] = $language -> getString( 'gender_2');
		
		$profile_edit_form -> drawList( $language -> getString( 'user_cp_section_personal_change_profile_gender'), 'user_gender', $genders_list, $profile_result['user_gender']);
		
		$profile_edit_form -> closeTable();
		$profile_edit_form -> drawSpacer( $language -> getString( 'user_cp_section_personal_change_profile_contacts'));
		$profile_edit_form -> openOpTable();
		
		$profile_edit_form -> drawTextInput( $language -> getString( 'user_cp_section_personal_change_profile_www'), 'user_web', $profile_result['user_web']);
		$profile_edit_form -> drawTextInput( $language -> getString( 'user_cp_section_personal_change_profile_jid'), 'user_jabber_id', $profile_result['user_jabber_id']);
				
		$profile_edit_form -> closeTable();
		$profile_edit_form -> drawSpacer( $language -> getString( 'user_cp_section_personal_change_profile_info'));
		$profile_edit_form -> openOpTable();
		
		$profile_edit_form -> drawTextInput( $language -> getString( 'user_cp_section_personal_change_profile_localisation'), 'user_localisation', $profile_result['user_localisation']);
		
		$lenght_limit_info = '';
		
		if ( $settings['user_interests_max_lenght'] != 0){
			
			$language -> setKey( 'max_lenght', $settings['user_interests_max_lenght']);
			
			$lenght_limit_info = $language -> getString( 'user_cp_length_info');
			
		}
		
		$profile_edit_form -> drawTextBox( $language -> getString( 'user_cp_section_personal_change_profile_interests'), 'user_interests', $profile_result['user_interests'], $lenght_limit_info);
				
		$profile_edit_form -> closeTable();
				
		/**
		 * additional fields?
		 */
		
		if ( count( $users -> custom_fields) > 0){
			
			$profile_edit_form -> drawSpacer( $language -> getString( 'user_cp_section_personal_change_profile_other'));
			$profile_edit_form -> openOpTable();
			
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
				
				if ( $field_ops['profile_field_byteam']){
				
					$profile_edit_form -> drawInfoRow( $field_ops['profile_field_name'], $field_value_select, $field_ops['profile_field_info']);

					switch ( $field_ops['profile_field_type']){
					
						case 0:
					
							$profile_edit_form -> drawInfoRow( $field_ops['profile_field_name'], $profile_result['field_'.$field_id], $field_ops['profile_field_info']);
					
						break;
					
						case 1:
					
							$profile_edit_form -> drawInfoRow( $field_ops['profile_field_name'], $profile_result['field_'.$field_id], $field_ops['profile_field_info']);
					
						break;
						
						case 2:
					
							$profile_edit_form -> drawInfoRow( $field_ops['profile_field_name'], $made_options[$profile_result['field_'.$field_id]], $field_ops['profile_field_info']);
					
						break;
						
					}
									
				}else{
				
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
							
							$profile_edit_form -> drawInfoRow( $field_ops['profile_field_name'], '<input name="field_'.$field_id.'" type="text" size="50" value="'.$profile_result['field_'.$field_id].'" '.$limit.'/>', $field_ops['profile_field_info'].$limit_msg);
					
						break;
					
						case 1:
					
							$limit_msg = '';
							
							if ( $field_ops['profile_field_length'] > 0){
								
								$language -> setKey( 'max_lenght', $field_ops['profile_field_length']);
								
								$limit_msg = $language -> getString( 'user_cp_length_info');
								
								if ( strlen( $field_ops['profile_field_info']) > 0)
									$limit_msg = '<br />'.$limit_msg;								
									
							}
							
							$profile_edit_form -> drawTextBox( $field_ops['profile_field_name'], 'field_'.$field_id, $profile_result['field_'.$field_id], $field_ops['profile_field_info'].$limit_msg);
							
						break;
												
						case 2:
												
							$profile_edit_form -> drawList( $field_ops['profile_field_name'], 'field_'.$field_id, $made_options, $profile_result['field_'.$field_id], $field_ops['profile_field_info']);
					
						break;
					}
				
				}
			}
			
			$profile_edit_form -> closeTable();
		
		}
		
		$profile_edit_form -> drawButton( $language -> getString( 'user_cp_change_button'));
		$profile_edit_form -> closeForm();
		
		/**
		 * display
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_personal_change_profile'), $profile_edit_form -> display()));
		
	}
	
	/**
	 * draws signature edition
	 */
	
	function action_edit_signature(){

		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check if users can chave signatures
		 */
		
		if ( $settings['users_can_sigs']){
		
			/**
			 * update signature
			 */
					
			if ( $_POST['update_signature'] && $session -> checkForm()){
				
				$new_user_signature = $strings -> inputClear( $_POST['user_signature'], false);
				
				/**
				 * do error checking
				 */
				
				if ( $settings['user_sig_max_length'] != 0 && strlen( $new_user_signature) > $settings['user_sig_max_length']){
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_signature'), $language -> getString( 'user_cp_sig_too_long')));
					
				}else{
					
					$profile_update_mysql['user_signature'] = $new_user_signature;
					
					/**
					 * update mysql
					 */
					
					$mysql -> update( $profile_update_mysql, 'users', "`user_id` = '".$session -> user['user_id']."'");
					
					parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_personal_change_signature'), $language -> getString( 'user_cp_sig_changed')));
					
				}
			}
			
			/**
			 * select profile data from mysql
			 */
			
			$profile_select_sql = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '".$session -> user['user_id']."'");
			
			if ( $profile_result = mysql_fetch_array( $profile_select_sql, MYSQL_ASSOC))
				$profile_result = $mysql -> clear($profile_result);
			
			/**
			 * if not empty, draw preview
			 */
			
			if ( strlen( $profile_result['user_signature']) > 0){
							
				parent::draw( $style -> drawBlock( $language -> getString( 'user_profile_signature'), '<div class="signature">'.$strings -> parseBB( nl2br($profile_result['user_signature']), $settings['users_allow_bbcodes_in_sigs'], $settings['users_allow_emoticones_in_sigs']).'</div>'));
							
			}
			
			/**
			 * now draw editor
			 */
			
			$change_signature_link = array( 'do' => 'edit_signature');
			
			$signature_edit_form = new form();
			$signature_edit_form -> openForm( parent::systemLink( parent::getId(), $change_signature_link));
			$signature_edit_form -> hiddenValue( 'update_signature', true);
			$signature_edit_form -> openOpTable();
			
			$lenght_info = '';
			
			if ( $settings['user_sig_max_length'] != 0){
				
				$language -> setKey( 'max_lenght', $settings['user_sig_max_length']);
				
				$lenght_info = $language -> getString( 'user_cp_length_info');
				
			}
				
			$signature = $profile_result['user_signature'];
			
			$signature_edit_form -> drawEditor( $language -> getString( 'user_cp_section_personal_change_signature_text'), 'user_signature', $signature, $lenght_info, $settings['users_allow_bbcodes_in_sigs'], $settings['users_allow_emoticones_in_sigs']);
			
			$signature_edit_form -> closeTable();
			$signature_edit_form -> drawButton( $language -> getString( 'user_cp_change_button'));
			$signature_edit_form -> closeForm();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_personal_change_signature'), $signature_edit_form -> display()));
			
		}else{
			
			parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_personal_change_signature'), $language -> getString( 'user_cp_sigs_notallowed')));
			
		}
			
	}
		
	
	/**
	 * draws avatar edition
	 */
	
	function action_edit_avatar(){

		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check, i we can have avatars
		 */
		
		if ( $settings['users_can_avatars']){
			
			/**
			 * check if user has avatar, and decidet to delete it
			 */
			
			if ( $_GET['delete_avatar'] == 'delete'){
				
				/**
				 * kill avatar
				 */
				
				$users -> killAvatar( $session -> user['user_id']);
				
				$style -> drawPart( 'user_avatar', false);
					
				/**
				 * draw message
				 */
				
				parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_personal_change_avatar_delte'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_new_avatar_deleted')));
				
			}
				
			$profile_select_sql = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '".$session -> user['user_id']."'");
			
			if ( $profile_result = mysql_fetch_array( $profile_select_sql, MYSQL_ASSOC))
				$profile_result = $mysql -> clear($profile_result);
									
			/**
			 * draw sumamry tab
			 */
			
			$avatar_form = array( 'do' => 'change_avatar');
			
			$avatar_summary_tab = new form();
			$avatar_summary_tab -> openForm( parent::systemLink( parent::getId(), $avatar_form), 'POST', true);
			$avatar_summary_tab -> drawSpacer( $language -> getString( 'user_cp_section_personal_change_avatar_information'));
			$avatar_summary_tab -> openOpTable();
			
			if ( $settings['users_avatar_max_size'] != 0){
				
				$language -> setKey( 'avatar_limit_size', $settings['users_avatar_max_size']);
				$avatars_limits[] = $language -> getString( 'user_cp_section_personal_change_avatar_information_size');
			}
			
			if ( $settings['users_avatar_max_width'] != 0){
				
				$language -> setKey( 'avatar_limit_width', $settings['users_avatar_max_width']);
				$avatars_limits[] = $language -> getString( 'user_cp_section_personal_change_avatar_information_width');
			}
			
			if ( $settings['users_avatar_max_height'] != 0){
				
				$language -> setKey( 'avatar_limit_height', $settings['users_avatar_max_height']);
				$avatars_limits[] = $language -> getString( 'user_cp_section_personal_change_avatar_information_height');
			}
			
			$avatars_limits[] = $language -> getString( 'user_cp_section_personal_change_avatar_information_type').': <b>'.$settings['users_avatars_extensions'].'</b>';
			
			if ( $settings['users_can_remote_avatars']){
				
				$avatars_limits[] = $language -> getString( 'user_cp_section_personal_change_avatar_information_remote_true');
			
			}else{
				
				$avatars_limits[] = $language -> getString( 'user_cp_section_personal_change_avatar_information_remote_false');
			
			}
			
			if ( $settings['users_can_upload_avatars']){
				
				$avatars_limits[] = $language -> getString( 'user_cp_section_personal_change_avatar_information_upload_true');
			
			}else{
				
				$avatars_limits[] = $language -> getString( 'user_cp_section_personal_change_avatar_information_upload_false');
			
			}
			
			$avatar_summary_tab -> drawRow( join( '<br />', $avatars_limits));
			
			$avatar_summary_tab -> closeTable();
			
			/**
			 * current avatar
			 */
			
			if ( $profile_result['user_avatar_type'] != 0){
				
				$avatar_summary_tab -> drawSpacer( $language -> getString( 'user_cp_section_personal_change_avatar_actual'));
				$avatar_summary_tab -> openOpTable();
			
				$delete_avatar_link = array( 'do' => 'edit_avatar', 'delete_avatar' => 'delete');
				
				$avatar_summary_tab -> drawRow( '<div style="text-align: center">'.$users -> drawAvatar( $profile_result['user_avatar_type'], $profile_result['user_avatar_image'], $profile_result['user_avatar_width'], $profile_result['user_avatar_height']).'<br />'.$profile_result['user_avatar_width'].'x'.$profile_result['user_avatar_height'].'<br />( <a href="'.parent::systemLink( parent::getId(), $delete_avatar_link).'">'.$language -> getString( 'user_cp_section_personal_change_avatar_delte').'</a> )</div>');
				
			
				/**
				 * avatar scalling
				 */
				
				$avatar_summary_tab -> drawInfoRow( $language -> getString('user_cp_section_personal_change_avatar_new_avatar_size'), '<input name="avatar_width" id="avatar_width" type="text" size="5" value="" /> x <input name="avatar_height" id="avatar_height" type="text" size="5" value="" />');
				
				$avatar_summary_tab -> closeTable();
			}
						
			$avatar_summary_tab -> drawSpacer( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar'));
			$avatar_summary_tab -> openOpTable();
			
			$avatars_galleries_link = array( 'do' => 'show_avatars');
			
			$avatar_summary_tab -> drawInfoRow( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar_from_gallery'), '<a href="'.parent::systemLink( parent::getId(), $avatars_galleries_link).'">'.$language -> getString( 'user_cp_section_personal_change_avatar_new_avatar_from_gallery_show').'</a>');
			
			/**
			 * remote avatar
			 */
			
			if ( $settings['users_can_remote_avatars'])
				$avatar_summary_tab -> drawTextInput( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar_remote'), 'remote_avatar');
			
			/**
			 * upload avatar
			 */
			
			if ( $settings['users_can_upload_avatars'])
				$avatar_summary_tab -> drawFile( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar_upload'), 'upload_avatar');
			
			$avatar_summary_tab -> closeTable();
			$avatar_summary_tab -> drawButton( $language -> getString( 'user_cp_section_personal_change_avatar_button_update'));
			$avatar_summary_tab -> closeForm();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_personal_change_avatar'), $avatar_summary_tab -> display()));
			
		}else{
			
			/**
			 * draw error
			 */
			
			parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_personal_change_avatar'), $language -> getString( 'user_cp_avatars_notallowed')));
			
		}
		
	}
	
	function action_show_avatars_gallery(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check, i we can have avatars
		 */
		
		if ( $settings['users_can_avatars']){
		
			/**
			 * draw tab with avatars in gallery
			 */
				
			$avatars_in_galleries = new form();
			
			/**
			 * build up list of avatars
			 */
			
			$avatars_galleries = glob( ROOT_PATH.'images/avatars_galleries/*', GLOB_ONLYDIR);
			
			if ( !empty( $avatars_galleries)){
			
				foreach ( $avatars_galleries as $gallery_path){
					
					/**
					 * add gallery
					 */
					
					$gallery_name = str_ireplace( ROOT_PATH.'images/avatars_galleries/', '', $gallery_path);
					
					$avatars_in_galleries -> drawSpacer( $gallery_name);
					$avatars_in_galleries -> openOpTable( true);
					
					$images_in_gallery = glob( $gallery_path."/*.{gif,jpg,png}", GLOB_BRACE);
					
					if ( !empty( $images_in_gallery)){
					
						$columnm = 0;
						
						$generated_html = '<tr>';
						
						foreach ( $images_in_gallery as $image_path){
							
							/**
							 * before we will draw avatar, check if it is under limit
							 */
														
							if ( $settings['users_avatar_max_size'] == 0 || round( (filesize( $image_path) / 1024), 0) < $settings['users_avatar_max_size']){
							
								if ( $columnm == 4){
									$generated_html .= '</tr><tr>';
									$columnm = 0;
								}
															
								if ( $columnm % 2 == 0){
									
									$cell_style = 'opt_row1';
									
								}else{
									
									$cell_style = 'opt_row2';
									
								}
								
								$image_name = str_ireplace( $gallery_path."/", '', $image_path);
							
								$avatar_link = array( 'do' => 'change_avatar', 'gallery_image' => $image_name, 'gallery_name' => $gallery_name);
								
								$generated_html .= '<td class="'.$cell_style.'"><div style="text-align: center"><a href="'.parent::systemLink( parent::getId(), $avatar_link).'"><img src="'.$image_path.'"></a><br /><b>'.$image_name.'</b></div></td>';
									
								$columnm ++;
							}
							
						}
						
						if ( $columnm != 0){
							
							while ( $columnm < 4){
								
								if ( $columnm % 2 == 0){
								
									$cell_style = 'opt_row1';
									
								}else{
									
									$cell_style = 'opt_row2';
									
								}
								
								$generated_html .= '<td class="'.$cell_style.'">&nbsp;</td>';
							
								$columnm++;
							
							}	
						
						}
						
						$generated_html .= '</tr>';
						
						if ( !empty( $generated_html)){
							
							$avatars_in_galleries -> addToContent( $generated_html);
							
						}else{
							
							$avatars_in_galleries -> drawRow( $language -> getString( 'user_cp_section_personal_change_avatar_avatars_gallery_empty'));
						
						}
						
					}else{
					
						$avatars_in_galleries -> drawRow( $language -> getString( 'user_cp_section_personal_change_avatar_avatars_gallery_empty'));
							
					}
						
					$avatars_in_galleries -> closeTable();
					
				}
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar_from_gallery_show'), $avatars_in_galleries -> display()));
				
			}else{
				
				/**
				 * no galleries found
				 */
				
				parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar_from_gallery_show'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_gallery_nofound')));
			
			}
			
		}else{
			
			/**
			 * draw error
			 */
			
			parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_personal_change_avatar'), $language -> getString( 'user_cp_avatars_notallowed')));
			
		}
	}
	
	function action_change_avatar(){
		
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check, i we can have avatars
		 */
		
		if ( $settings['users_can_avatars']){
			
			/**
			 * check if we sended any avatar
			 */
			
			$new_avatar_type = 0;
			
			if ( isset( $_GET['gallery_image']) && !empty( $_GET['gallery_image']) && isset( $_GET['gallery_name']) && !empty( $_GET['gallery_name']))
				$new_avatar_type = 3;
			
			$remote_avatar = trim( $_POST['remote_avatar']);
				
			if ( $session -> checkForm()){
				
				if ( isset( $_POST['remote_avatar']) && !empty( $remote_avatar) && $settings['users_can_remote_avatars'])
					$new_avatar_type = 1;
									
				if ( !empty( $_FILES['upload_avatar']['name']) && $settings['users_can_upload_avatars'])
					$new_avatar_type = 2;
									
				if ( (isset( $_POST['avatar_width']) && !empty( $_POST['avatar_width'])) || (isset( $_POST['avatar_height']) && !empty( $_POST['avatar_height'])))
					$new_avatar_type = 4;	
								
			}	
				
			if ( $new_avatar_type != 0){
				
				/**
				 * define, what avatar is send
				 */
				
				switch ( $new_avatar_type){
				
					case 3:
					
						/**
						 * avatar from gallery
						 * check if it exists
						 */
	
						if ( file_exists(ROOT_PATH.'images/avatars_galleries/'.$_GET['gallery_name'].'/'.$_GET['gallery_image'])){
							
							/**
							 * file exists make sure, it is image
							 */
							
							$propper_types = array( 'jpg', 'gif', 'png');
							
							if ( in_array( substr( $_GET['gallery_image'], strlen( $_GET['gallery_image']) - 3), $propper_types)){
								
								/**
								 * check size
								 */
								
								if ( $settings['users_avatar_max_size'] == 0 || round( (filesize( ROOT_PATH.'images/avatars_galleries/'.$_GET['gallery_name'].'/'.$_GET['gallery_image']) / 1024), 0) < $settings['users_avatar_max_size']){
								
									/**
									 * everything okey, kill actual avatar
									 */
									
									$users -> killAvatar( $session -> user['user_id']);
									
									/**
									 * set new avatar
									 */
									
									$gallery_image = stripslashes( trim( $_GET['gallery_name'].'/'.$_GET['gallery_image']));
									
									$update_av_sql['user_avatar_image'] = $gallery_image;
									$update_av_sql['user_avatar_type'] = 3;
									
									/**
									 * define size using diffrent method
									 */
									
									if ( $settings['users_avatar_autosize']){
		
										$image_size = getimagesize( ROOT_PATH.'images/avatars_galleries/'.$_GET['gallery_name'].'/'.$_GET['gallery_image']);
																	
										$image_w = $image_size[0];
										$image_h = $image_size[1];
										
										/**
										 * check limits
										 */
										
										if ( $settings['users_avatar_max_width'] != 0 && $image_w > $settings['users_avatar_max_width'])
											$image_w = $settings['users_avatar_max_width'];
										
										if ( $settings['users_avatar_max_height'] != 0 && $image_h > $settings['users_avatar_max_height'])
											$image_h = $settings['users_avatar_max_height'];
											
										/**
										 * update
										 */
											
										$update_av_sql['user_avatar_width'] = $image_w;
										$update_av_sql['user_avatar_height'] = $image_h;
										
									}else{
										
										$avatar_width = $settings['users_avatar_max_width'];
										$avatar_height = $settings['users_avatar_max_height'];
																		
										if ( $avatar_width < 1)
											$avatar_width = 1;
																							
										if ( $avatar_height < 1)
											$avatar_height = 1;
										
										$update_av_sql['user_avatar_width'] = $avatar_width;
										$update_av_sql['user_avatar_height'] = $avatar_height;
										
									}
									
									$style -> drawString( 'USER_AVATAR_IMAGE', $users -> drawAvatar( 3, $gallery_image, $avatar_width, $avatar_height));
									$style -> drawPart( 'user_avatar', true);
							
									$mysql -> update( $update_av_sql, 'users', "`user_id` = '".$session -> user['user_id']."'");
									
									/**
									 * draw message
									 */
									
									parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_new_avatar_changed')));
									
									$this -> action_edit_avatar();
									
								}else{
																		
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_new_avatar_too_big')));
												
									$this -> action_show_avatars_gallery();
									
								}
								
							}else{
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_new_avatar_wrong_format')));
											
								$this -> action_show_avatars_gallery();
								
							}
							
						}else{
							
							/**
							 * nothing found
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_new_avatar_notfound')));
										
							$this -> action_show_avatars_gallery();
							
						}
						
					break;
					
					case 1:
						
						/**
						 * remote avatar
						 * check extension
						 */
				
						$avatar_extension = substr( $remote_avatar, strrpos( $remote_avatar, '.') + 1);
						$avatar_extensions = split( ',', $settings['users_avatars_extensions']);
						
						if ( in_array( $avatar_extension, $avatar_extensions)){
							
							/**
							 * everything is okey
							 */
							
							$users -> killAvatar( $session -> user['user_id']);
							
							$avatar_width = $settings['users_avatar_max_width'];
							$avatar_height = $settings['users_avatar_max_height'];
															
							if ( $avatar_width < 1)
								$avatar_width = 1;
																				
							if ( $avatar_height < 1)
								$avatar_height = 1;
														
							$update_av_sql['user_avatar_image'] = uniSlashes( $remote_avatar);
							$update_av_sql['user_avatar_type'] = 1;
							$update_av_sql['user_avatar_width'] = $avatar_width;
							$update_av_sql['user_avatar_height'] = $avatar_height;
							
							$mysql -> update( $update_av_sql, 'users', "`user_id` = '".$session -> user['user_id']."'");
							
							$style -> drawString( 'USER_AVATAR_IMAGE', $users -> drawAvatar( 1, $remote_avatar, $avatar_width, $avatar_height));
							$style -> drawPart( 'user_avatar', true);
																
							/**
							 * draw message
							 */
							
							parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_new_avatar_changed')));
									
						}else{
							
							/**
							 * wrong extension
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_new_avatar_wrong_format')));
										
						}
								
						$this -> action_edit_avatar();
										
					break;
						
					case 2:
						
						/**
						 * upload avatar
						 * check if sended
						 */
				
						if ( is_uploaded_file($_FILES['upload_avatar']['tmp_name'])) {
							
							/**
							 * check file mime
							 */
							
							if ( substr( $_FILES['upload_avatar']['type'], 0, 5) == 'image'){
								
								/**
								 * check file real extension
								 */
																
								$avatar_extension = substr( $_FILES['upload_avatar']['name'], strrpos( $_FILES['upload_avatar']['name'], '.') + 1);
								$avatar_extensions = split( ',', $settings['users_avatars_extensions']);
								
								if ( in_array( $avatar_extension, $avatar_extensions)){
									
									/**
									 * check avatar size
									 */
									
									if ( $settings['users_avatar_max_size'] == 0 || round( (filesize( $_FILES['upload_avatar']['tmp_name']) / 1024), 0) < $settings['users_avatar_max_size']){

										$users -> killAvatar( $session -> user['user_id']);
							
										/**
										 * everything seems ok. To avoid conflicts, generate new name for image
										 */
										
										$image_new_name = md5( time().ip2long( $_SERVER['REMOTE_ADDR']));
										
										$image_new_name = substr_replace( $_FILES['upload_avatar']['name'], $image_new_name, 0, stripos( $_FILES['upload_avatar']['name'], '.'));
										
										/**
										 * move it
										 */
										
										 move_uploaded_file( $_FILES['upload_avatar']['tmp_name'], ROOT_PATH.'uploads/'.$image_new_name);
										 
										 /**
										  * begin query
										  */
										 
										$update_av_sql['user_avatar_image'] = uniSlashes( $image_new_name);
										$update_av_sql['user_avatar_type'] = 2;
										
										if ( $settings['users_avatar_autosize']){
					
											$image_size = getimagesize( ROOT_PATH.'uploads/'.$image_new_name);
																		
											$image_w = $image_size[0];
											$image_h = $image_size[1];
											
											/**
											 * check limits
											 */
											
											if ( $settings['users_avatar_max_width'] != 0 && $image_w > $settings['users_avatar_max_width'])
												$image_w = $settings['users_avatar_max_width'];
											
											if ( $settings['users_avatar_max_height'] != 0 && $image_h > $settings['users_avatar_max_height'])
												$image_h = $settings['users_avatar_max_height'];
												
											/**
											 * update
											 */
												
											$update_av_sql['user_avatar_width'] = $image_w;
											$update_av_sql['user_avatar_height'] = $image_h;
											
										}else{
														
											$avatar_width = $settings['users_avatar_max_width'];
											$avatar_height = $settings['users_avatar_max_height'];
																			
											if ( $avatar_width < 1)
												$avatar_width = 1;
																								
											if ( $avatar_height < 1)
												$avatar_height = 1;
											
											$update_av_sql['user_avatar_width'] = $avatar_width;
											$update_av_sql['user_avatar_height'] = $avatar_height;
											
											$style -> drawString( 'USER_AVATAR_IMAGE', $users -> drawAvatar( 2, $image_new_name, $avatar_width, $avatar_height));
											$style -> drawPart( 'user_avatar', true);
					
										}
										
										$mysql -> update( $update_av_sql, 'users', "`user_id` = '".$session -> user['user_id']."'");
							
										parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_new_avatar_changed')));
										
										
									}else{
										
										parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_new_avatar_too_big')));
																			
									}
									
								}else{
								
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_new_avatar_wrong_format')));
									
								}
								
							}else{
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_new_avatar_wrong_format')));
								
							}
						
						}else{
							
							/**
							 * nothing send
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_new_avatar_notsend')));
							
						}
						
						/**
						 * return to editing
						 */
									
						$this -> action_edit_avatar();
						
					break;
					
					case 4:
						
						/**
						 * resize avatar
						 */
						
						$avatar_width = $_POST['avatar_width'];
						$avatar_height = $_POST['avatar_height'];
						
						if ( !empty( $_POST['avatar_width'])){
							
							settype( $avatar_width, 'integer');
							
							if ( $avatar_width < 1)
								$avatar_width = 1;
								
							if ( $settings['users_avatar_max_width'] != 0 && $avatar_width > $settings['users_avatar_max_width'])
								$avatar_width = $settings['users_avatar_max_width'];
								
							$update_av_sql['user_avatar_width'] = $avatar_width;						
							
						}
						
						if ( !empty( $_POST['avatar_height'])){
							
							settype( $avatar_height, 'integer');
							
							if ( $avatar_height < 1)
								$avatar_height = 1;
								
							if ( $settings['users_avatar_max_height'] != 0 && $avatar_height > $settings['users_avatar_max_height'])
								$avatar_height = $settings['users_avatar_max_height'];
								
							$update_av_sql['user_avatar_height'] = $avatar_height;						
							
						}
						
						if ( isset( $update_av_sql))
							$mysql -> update( $update_av_sql, 'users', "`user_id` = '".$session -> user['user_id']."'");
						
						$this -> action_edit_avatar();
							
					break;
					
				}
					
			}else{
				
				/**
				 * nothing send
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_avatar_new_avatar'), $language -> getString( 'user_cp_section_personal_change_avatar_avatars_new_avatar_notsend')));
							
				$this -> action_edit_avatar();
				
			}
			
		}else{
			
			/**
			 * draw error
			 */
			
			parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_personal_change_avatar'), $language -> getString( 'user_cp_avatars_notallowed')));
			
		}
	}
	
	function action_edit_mail(){

		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check if we have new form
		 */
		
		if ( $session -> checkForm()){
			
			$user_new_mail = $strings -> inputClear( $_POST['new_user_mail'], false);
			$new_user_mail_rep = $strings -> inputClear( $_POST['new_user_mail_rep'], false);
			
			$new_user_pass_bit = trim( $_POST['new_user_pass']);
			
			/**
			 * check mail avaibility
			 */
							
			$mail_free = true;
								
			if($mysql -> countRows( "users", "`user_mail` = '".$user_mail."' AND `user_id` > '-1' AND `user_id` <> '".$session -> user['user_id']."'") != 0 && $settings['users_allow_mail_reuse'] == false)
				$mail_free = false;
			
			/**
			 * pass
			 */
					
			$profile_select_sql = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '".$session -> user['user_id']."'");
		
			if ( $profile_result = mysql_fetch_array( $profile_select_sql, MYSQL_ASSOC))
				$profile_result = $mysql -> clear($profile_result);
			
				
			if ( strlen( $user_new_mail) == 0 || strlen( $new_user_mail_rep) == 0 || strlen( $new_user_pass_bit) == 0){
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_mail'), $language -> getString( 'user_cp_section_personal_change_mail_all_fields_required')));
				
			}else if( $user_new_mail != $new_user_mail_rep){
			
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_mail'), $language -> getString( 'user_cp_section_personal_change_mail_no_matchs')));
				
			}else if( !$users -> checkMail($user_new_mail)){
			
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_mail'), $language -> getString( 'user_cp_section_personal_change_mail_wrong')));
				
			}else if( md5( md5($new_user_pass_bit).md5($new_user_pass_bit)) != $profile_result['user_password']){
			
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_mail'), $language -> getString( 'user_cp_section_personal_change_mail_pass_wrong')));
				
			}else{
				
				/**
				 * all okey, change mail
				 */
				
				$new_mail_sql['user_mail'] = $user_new_mail;
				
				$mysql -> update( $new_mail_sql, 'users', "`user_id` = '".$session -> user['user_id']."'");
				
				/**
				 * draw message
				 */
				
				parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_personal_change_mail'), $language -> getString( 'user_cp_section_personal_change_mail_done')));
				
			}
			
		}
		
		/**
		 * select profile data from mysql
		 */
		
		$profile_select_sql = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '".$session -> user['user_id']."'");
		
		if ( $profile_result = mysql_fetch_array( $profile_select_sql, MYSQL_ASSOC))
			$profile_result = $mysql -> clear($profile_result);
		
		
		/**
		 * change mail form
		 */
		
		$change_mail_link = array( 'do' => 'edit_mail');
		
		$change_mail_form = new form();
		$change_mail_form -> openForm( parent::systemLink( parent::getId(), $change_mail_link));
		$change_mail_form -> openOpTable();
		
		$change_mail_form -> drawInfoRow( $language -> getString( 'user_cp_section_personal_change_mail_actual'), $profile_result['user_mail']);
		
		$change_mail_form -> drawTextInput( $language -> getString( 'user_cp_section_personal_change_mail_new'), 'new_user_mail');
		$change_mail_form -> drawTextInput( $language -> getString( 'user_cp_section_personal_change_mail_new_rep'), 'new_user_mail_rep');
		$change_mail_form -> drawPassInput( $language -> getString( 'user_cp_section_personal_change_mail_pass'), 'new_user_pass');
		
		$change_mail_form -> closeTable();
		$change_mail_form -> drawButton( $language ->getString( 'user_cp_section_personal_change_mail_but'));
		$change_mail_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_personal_change_mail'), $change_mail_form -> display()));		
		
	}
	
	function action_edit_pass(){

		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check if we have new form
		 */
		
		if ( $session -> checkForm()){
					
			$new_user_pass_bit = trim( $_POST['new_user_pass']);
			$new_user_pass_rep_bit = trim( $_POST['new_user_pass_rep']);
			$act_user_pass_bit = trim( $_POST['act_user_pass']);
			
			/**
			 * pass
			 */
					
			$profile_select_sql = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '".$session -> user['user_id']."'");
		
			if ( $profile_result = mysql_fetch_array( $profile_select_sql, MYSQL_ASSOC))
				$profile_result = $mysql -> clear($profile_result);
			
				
			if ( strlen( $new_user_pass_bit) == 0 || strlen( $new_user_pass_rep_bit) == 0 || strlen( $act_user_pass_bit) == 0){
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_pass'), $language -> getString( 'user_cp_section_personal_change_pass_must_all')));
				
			}else if( $new_user_pass_bit != $new_user_pass_rep_bit){
			
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_pass'), $language -> getString( 'user_cp_section_personal_change_pass_no_matchs')));
				
			}else if( md5( md5($act_user_pass_bit).md5($act_user_pass_bit)) != $profile_result['user_password']){
			
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'user_cp_section_personal_change_pass'), $language -> getString( 'user_cp_section_personal_change_pass_wrong')));
				
			}else{
				
				/**
				 * all okey, change pass
				 */
				
				$new_pass_sql['user_password'] = md5( md5($new_user_pass_bit).md5($new_user_pass_bit));
				
				$mysql -> update( $new_pass_sql, 'users', "`user_id` = '".$session -> user['user_id']."'");
				
				/**
				 * kill auto login keys
				 */
				
				$mysql -> delete( 'users_autologin', "`users_autologin_user` = '".$session -> user['user_id']."'");
				
				/**
				 * draw message
				 */
				
				parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_personal_change_pass'), $language -> getString( 'user_cp_section_personal_change_pass_done')));
				
			}
			
		}
		
		/**
		 * select profile data from mysql
		 */
		
		$profile_select_sql = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '".$session -> user['user_id']."'");
		
		if ( $profile_result = mysql_fetch_array( $profile_select_sql, MYSQL_ASSOC))
			$profile_result = $mysql -> clear($profile_result);
		
		
		/**
		 * change mail form
		 */
		
		$change_pass_link = array( 'do' => 'edit_pass');
		
		$change_pass_form = new form();
		$change_pass_form -> openForm( parent::systemLink( parent::getId(), $change_pass_link));
		$change_pass_form -> openOpTable();
		
		$change_pass_form -> drawPassInput( $language -> getString( 'user_cp_section_personal_change_pass_new'), 'new_user_pass');
		$change_pass_form -> drawPassInput( $language -> getString( 'user_cp_section_personal_change_pass_new_rep'), 'new_user_pass_rep');
		$change_pass_form -> drawPassInput( $language -> getString( 'user_cp_section_personal_change_pass_actual'), 'act_user_pass');
		
		$change_pass_form -> closeTable();
		$change_pass_form -> drawButton( $language ->getString( 'user_cp_section_personal_change_pass_butt'));
		$change_pass_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_personal_change_pass'), $change_pass_form -> display()));		
		
	}
	
	function action_emails_settings(){
		
		include( FUNCTIONS_GLOBALS);
		
		if ( $_POST['update_profile'] && $session -> checkForm()){
			
			$user_show_mail = $_POST['user_show_email'];
			$user_want_mail = $_POST['user_want_email'];
			$user_notify_pm = $_POST['user_pm_notify'];
			$user_auto_subscribe = $_POST['user_auto_subscribe'];
			
			settype( $user_show_mail, 'bool');
			settype( $user_want_mail, 'bool');
			settype( $user_notify_pm, 'bool');
			settype( $user_auto_subscribe, 'bool');
			
			$profile_update_mysql['user_show_mail'] = $user_show_mail;
			$profile_update_mysql['user_want_mail'] = $user_want_mail;
			$profile_update_mysql['user_notify_pm'] = $user_notify_pm;			
			$profile_update_mysql['user_auto_subscribe'] = $user_auto_subscribe;			
			
			/**
			 * update mysql
			 */
			
			$mysql -> update( $profile_update_mysql, 'users', "`user_id` = '".$session -> user['user_id']."'");
			
			parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_options_emails_settings'), $language -> getString( 'user_cp_section_options_emails_settings_changed')));
				
		}
		
		/**
		 * select profile data from mysql
		 */
		
		$profile_select_sql = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '".$session -> user['user_id']."'");
		
		if ( $profile_result = mysql_fetch_array( $profile_select_sql, MYSQL_ASSOC))
			$profile_result = $mysql -> clear($profile_result);
			
		/**
		 * begin drawing
		 */
		
		$update_profile_link = array( 'do' => 'emails_settings');
		
		$profile_edit_form = new form();
		$profile_edit_form -> openForm( parent::systemLink( parent::getId(), $update_profile_link));
		$profile_edit_form -> hiddenValue( 'update_profile', true);
		$profile_edit_form -> openOpTable();
		
		$profile_edit_form -> drawYesNo( $language -> getString( 'user_cp_section_options_emails_settings_show_email'), 'user_show_email', $profile_result['user_show_mail']);
		$profile_edit_form -> drawYesNo( $language -> getString( 'user_cp_section_options_emails_settings_want_email'), 'user_want_email', $profile_result['user_want_mail']);
		$profile_edit_form -> drawYesNo( $language -> getString( 'user_cp_section_options_emails_settings_pm_notify'), 'user_pm_notify', $profile_result['user_notify_pm']);
		$profile_edit_form -> drawYesNo( $language -> getString( 'user_cp_section_options_emails_settings_auto_subscribe'), 'user_auto_subscribe', $profile_result['user_auto_subscribe']);
		
		$profile_edit_form -> closeTable();
		$profile_edit_form -> drawButton( $language -> getString( 'user_cp_change_button'));
		$profile_edit_form -> closeForm();
		
		/**
		 * display
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_options_emails_settings'), $profile_edit_form -> display()));
		
	}
	
	function action_board_settings(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * update
		 */
		
		if ( $_POST['update_profile'] && $session -> checkForm()){
			
			$user_timezone = $_POST['user_timezone'];
			$user_dst = $_POST['user_dst'];
			
			$user_show_avs = $_POST['user_display_avatars'];
			$user_show_sigs = $_POST['user_display_sigs'];
			
			settype( $user_timezone, 'float');
			settype( $user_dst, 'bool');
			settype( $user_show_avs, 'bool');
			settype( $user_show_sigs, 'bool');
			
			$profile_update_mysql['user_time_zone'] = $user_timezone;
			$profile_update_mysql['user_dst'] = $user_dst;
			$profile_update_mysql['user_show_avatars'] = $user_show_avs;
			$profile_update_mysql['user_show_sigs'] = $user_show_sigs;			
			
			/**
			 * update mysql
			 */
			
			$mysql -> update( $profile_update_mysql, 'users', "`user_id` = '".$session -> user['user_id']."'");
			
			parent::draw( $style -> drawBlock( $language -> getString( 'user_cp_section_options_board_settings'), $language -> getString( 'user_cp_section_options_board_settings_changed')));
				
		}
		
		/**
		 * select profile data from mysql
		 */
		
		$profile_select_sql = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '".$session -> user['user_id']."'");
		
		if ( $profile_result = mysql_fetch_array( $profile_select_sql, MYSQL_ASSOC))
			$profile_result = $mysql -> clear($profile_result);
		
			/**
		 * begin drawing
		 */
		
		$update_profile_link = array( 'do' => 'board_settings');
		
		$profile_edit_form = new form();
		$profile_edit_form -> openForm( parent::systemLink( parent::getId(), $update_profile_link));
		$profile_edit_form -> hiddenValue( 'update_profile', true);
		$profile_edit_form -> drawSpacer( $language -> getString( 'user_cp_section_options_board_settings_time'));
		$profile_edit_form -> openOpTable();
		
		$profile_edit_form -> drawList( $language -> getString( 'user_cp_section_options_board_settings_time_timezone'), 'user_timezone', $time -> getTimeZones(), $profile_result['user_time_zone']);
		$profile_edit_form -> drawYesNo( $language -> getString( 'user_cp_section_options_board_settings_time_dste'), 'user_dst', $profile_result['user_dst']);
		$profile_edit_form -> closeTable();
		$profile_edit_form -> drawSpacer( $language -> getString( 'user_cp_section_options_board_settings_display'));
		$profile_edit_form -> openOpTable();
		$profile_edit_form -> drawYesNo( $language -> getString( 'user_cp_section_options_board_settings_draw_avatars'), 'user_display_avatars', $profile_result['user_show_avatars']);
		$profile_edit_form -> drawYesNo( $language -> getString( 'user_cp_section_options_board_settings_draw_signatures'), 'user_display_sigs', $profile_result['user_show_sigs']);
		$profile_edit_form -> closeTable();
		$profile_edit_form -> drawButton( $language -> getString( 'user_cp_change_button'));
		$profile_edit_form -> closeForm();
		
		/**
		 * display
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'user_cp_section_options_board_settings'), $profile_edit_form -> display()));
				
	}
	
	/**
	 * draws mod summary
	 */
	
	function action_draw_mod_summary(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * update notepad
		 */
		
		if ( $_POST['sub_act'] == 'update_notepad' && $session -> checkForm()){
			
			$notepad_update_sql['mods_notepad'] = $strings -> inputClear( $_POST['mods_notepad'], false);
			
			$mysql -> update($notepad_update_sql, 'settings', "`setting_setting` = 'mods_notepad'");
			
			$settings['mods_notepad'] = stripslashes( $strings -> inputClear( $_POST['mods_notepad'], false));
			
		}
		
		/**
		 * begin drawing form
		 */
				
		$summary_form = new form();
		$summary_form -> drawSpacer( $language -> getString( 'mod_cp_summary'));
		$summary_form -> openOpTable();
		
		/**
		 * reports num
		 */
		$forums_list = $forums -> getForumsList();
					
		$clear_forum_list = array();
		
		foreach ( $forums_list as $forum_id => $forum_name){
			
			if ( $session -> isMod($forum_id))
				$clear_forum_list[] = $forum_id;
		}
		
		/**
		 * we have forums list
		 * count posts reports
		 */
		
		$count_reports = $mysql -> query( "SELECT COUNT(*) FROM posts_reports r
		LEFT JOIN posts p ON p.post_id = r.post_report_post
		LEFT JOIN topics t ON p.post_topic = t.topic_id
		WHERE t.topic_forum_id IN (".join( ",", $clear_forum_list).")");
		
		if ( $reports_num = mysql_fetch_array( $count_reports, MYSQL_NUM))
			$reports_num = $reports_num[0];

		settype( $reports_num, 'integer');
		
		$summary_form -> drawInfoRow( $language -> getString( 'mod_cp_summary_reports'), $reports_num);
		
		/**
		 * last report time
		 */
		
		if ( $reports_num > 0){
		
			$last_report = $mysql -> query( "SELECT post_report_time FROM posts_reports r
			LEFT JOIN posts p ON p.post_id = r.post_report_post
			LEFT JOIN topics t ON p.post_topic = t.topic_id
			WHERE t.topic_forum_id IN (".join( ",", $clear_forum_list).") ORDER BY post_report_time DESC LIMIT 1");
			
			if ( $reports_result = mysql_fetch_array( $last_report, MYSQL_NUM)){
						
				$summary_form -> drawInfoRow( $language -> getString( 'mod_cp_summary_reports_last_time'), $time -> drawDate( $reports_result[0]));
			
			}
			
		}
		
		/**
		 * warned users
		 */
		
		$warned_users_num = $mysql -> countRows( 'users', "`user_warns` > 0");
		
		$summary_form -> drawInfoRow( $language -> getString( 'mod_cp_summary_users_warned'), $warned_users_num);
		
		/**
		 * close summary tab
		 */
		
		$summary_form -> closeTable();
		
		/**
		 * notepad
		 */
		
		$summary_form -> drawSpacer( $language -> getString( 'mod_cp_notepad'));
		
		$update_notepad_link['do'] = 'summary';
		
		$summary_form -> openForm(parent::systemLink( parent::getId(), $update_notepad_link));
		$summary_form -> hiddenValue( 'sub_act', 'update_notepad');
		$summary_form -> openOpTable();
		$summary_form -> drawSingleTextBox( 'mods_notepad', $settings['mods_notepad']);
		$summary_form -> closeTable();
		$summary_form -> drawButton( $language -> getString( 'mod_cp_notepad_update'));
		$summary_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'mod_action'), $summary_form -> display()));
		
	}
	
	/**
	 * draws reports list
	 */
	
	function action_reports(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * what reports can we draw
		 */
		
		$forums_list = $forums -> getForumsList();
					
		$clear_forum_list = array();
		
		foreach ( $forums_list as $forum_id => $forum_name){
			
			if ( $session -> isMod($forum_id))
				$clear_forum_list[] = $forum_id;
		}
		
		/**
		 * we have forums list
		 * count posts reports
		 */
		
		//$logs_query = $mysql -> query( "SELECT l.*, COUNT(*) as logs_num, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM moderators_logs l LEFT JOIN users u ON l.moderators_log_user_id = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id GROUP BY l.moderators_log_user_id  ORDER BY l.moderators_log_time DESC LIMIT 5");
		
		$count_reports = $mysql -> query( "SELECT COUNT(*) FROM posts p
		LEFT JOIN topics t ON p.post_topic = t.topic_id
		WHERE t.topic_forum_id IN (".join( ",", $clear_forum_list).") AND p.post_reported = '1'");
		
		if ( $reports_num = mysql_fetch_array( $count_reports, MYSQL_NUM))
			$reports_num = $reports_num[0];

		settype( $reports_num, 'integer');
		
		/**
		 * build up paginating
		 */
		
		$pages_number = ceil( $reports_num / 20);
					
		/**
		 * select reported posts
		 */
		
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
		 * paginator
		 */
		
		$paginator_html = $style -> drawPaginator( parent::systemLink( parent::getId(), array( 'do' => 'reports')), 'p', $pages_number, ( $page_to_draw + 1));
			
		/**
		 * open table
		 */
		
		$reports_list = new form();
		$reports_list -> openOpTable();
		
		/**
		 * do query
		 */
		
		$select_reports = $mysql -> query( "SELECT p.*, t.*, f.*, u.user_id, u.user_main_group, u.user_other_groups, u.user_login, g.users_group_prefix, g.users_group_suffix, COUNT(*) as reports_number FROM posts p
		LEFT JOIN topics t ON p.post_topic = t.topic_id
		LEFT JOIN users u ON p.post_author = u.user_id
		LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
		LEFT JOIN forums f ON t.topic_forum_id = f.forum_id
		LEFT JOIN posts_reports r ON r.post_report_post = p.post_id
		WHERE t.topic_forum_id IN (".join( ",", $clear_forum_list).") AND p.post_reported = '1' GROUP BY p.post_id ORDER BY p.post_time DESC LIMIT ".($page_to_draw* 20).",20");
		
		while ( $report_result = mysql_fetch_array( $select_reports, MYSQL_ASSOC)) {
			
			//clear result
			$report_result = $mysql -> clear( $report_result);
			
			if ( $report_result['post_author'] == -1){
	
				/**
				 * author is deleted
				 */
													
				$post_author = '<a name="post'.$report_result['post_id'].'" id="post'.$report_result['post_id'].'">'.$report_result['users_group_prefix'].$report_result['post_author_name'].$report_result['users_group_suffix'].'</a>';
				
			}else{
													
				/**
				 * and user login
				 */
				
				$post_author = '<a name="post'.$report_result['post_id'].'" id="post'.$report_result['post_id'].'">'.'<a href="'.parent::systemLink( 'user', array( 'user' => $report_result['post_author'])).'">'.$report_result['users_group_prefix'].$report_result['user_login'].$report_result['users_group_suffix'].'</a></a>';				
				
			}
			
			/**
			 * and message
			 */
			
			$post_message = $strings -> parseBB( nl2br( $report_result['post_text']), $report_result['forum_allow_bbcode'], true);
			
			$user_groups = array();
			$user_groups = split( ",", $post_result['user_other_groups']);
			$user_groups[] = $post_result['user_main_group'];
			
			if ( !$users -> cantCensore( $user_groups))
				$post_message = $strings -> censore( $post_message);
									
			/**
			 * insert row
			 */
			
			$reports_list -> addToContent( '<tr>
				<td class="opt_row1" style="width: 170px; vertical-align: top">'.$post_author.'<br />
				'.$time -> drawDate( $report_result['post_time']).'<br /><br />
				<b>'.$language -> getString( 'search_results_topic').':</b> <a href="'.parent::systemLink( 'topic', array( 'topic' => $report_result['topic_id'])).'">'.$report_result['topic_name'].'</a><br />
				<b>'.$language -> getString( 'search_results_topic_replies').':</b> '.$report_result['topic_posts_num'].'<br />
				<b>'.$language -> getString( 'mod_cp_reports_number').':</b> '.$report_result['reports_number'].'<br /><br />
				<a href="'.parent::systemLink( parent::getId(), array( 'do' => 'post_reports', 'post' => $report_result['post_id'])).'">'.$language -> getString( 'mod_cp_reports_show').'</a>
				</td>
				<td class="opt_row2" style="vertical-align: top">'.$post_message.'</td>
			</tr>
			<tr>
				<td colspan="2" class="post_end"></td>
			</tr>');
			
			//found reports
			$found_reports = true;
			
		}
		
		if ( !isset( $found_reports))
			$reports_list -> drawRow( $language -> getString( 'mod_cp_reports_list_empty'));
		
		/**
		 * close table
		 */
		
		$reports_list -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'mod_cp_reports'), $reports_list -> display()));
		
		parent::draw($paginator_html);
		
	}
	
	/**
	 * draw post reports list
	 */
	
	function action_post_reports(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * what reports can we draw
		 */
		
		$forums_list = $forums -> getForumsList();
					
		$clear_forum_list = array();
		
		foreach ( $forums_list as $forum_id => $forum_name){
			
			if ( $session -> isMod($forum_id))
				$clear_forum_list[] = $forum_id;
		}
		
		/**
		 * get post to draw
		 */
		
		$post_to_draw = $_GET['post'];
		
		settype( $post_to_draw, 'integer');
		
		/**
		 * select post
		 */
		
		$post_query = $mysql -> query( "SELECT p.*, t.*, f.*, u.user_id, u.user_login, u.user_main_group, u.user_other_groups, g.users_group_prefix, g.users_group_suffix FROM posts p
		LEFT JOIN topics t ON p.post_topic = t.topic_id
		LEFT JOIN forums f ON t.topic_forum_id = f.forum_id
		LEFT JOIN users u ON p.post_author = u.user_id
		LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
		WHERE p.post_id = '$post_to_draw' AND p.post_reported = '1' AND t.topic_forum_id IN (".join( ",", $clear_forum_list).")"); 
		
		if ( $post_result = mysql_fetch_array( $post_query, MYSQL_ASSOC)){
			
			/**
			 * we got post
			 */
			
			$post_result = $mysql -> clear( $post_result);
			
			/**
			 * open form
			 */
			
			$post_report = new form();
			$post_report -> openOpTable();
			
			/**
			 * post groups
			 */
			
			if ( $post_result['post_author'] == -1){
	
				/**
				 * author is deleted
				 */
													
				$post_author = '<a name="post'.$post_result['post_id'].'" id="post'.$post_result['post_id'].'">'.$post_result['users_group_prefix'].$post_result['post_author_name'].$post_result['users_group_suffix'].'</a>';
				
			}else{
													
				/**
				 * and user login
				 */
				
				$post_author = '<a name="post'.$post_result['post_id'].'" id="post'.$post_result['post_id'].'">'.'<a href="'.parent::systemLink( 'user', array( 'user' => $post_result['post_author'])).'">'.$post_result['users_group_prefix'].$post_result['user_login'].$post_result['users_group_suffix'].'</a></a>';				
				
			}
			
			/**
			 * and message
			 */
			
			$post_message = $strings -> parseBB( nl2br( $post_result['post_text']), $post_result['forum_allow_bbcode'], true);
			
			$user_groups = array();
			$user_groups = split( ",", $post_result['user_other_groups']);
			$user_groups[] = $post_result['user_main_group'];
			
			if ( !$users -> cantCensore( $user_groups))
				$post_message = $strings -> censore( $post_message);
									
			/**
			 * insert row
			 */
			
			$post_report -> addToContent( '<tr>
				<td class="opt_row1" style="width: 170px; vertical-align: top">'.$post_author.'<br />
				'.$time -> drawDate( $post_result['post_time']).'<br /><br />
				<b>'.$language -> getString( 'search_results_topic').':</b> <a href="'.parent::systemLink( 'topic', array( 'topic' => $post_result['topic_id'])).'">'.$post_result['topic_name'].'</a><br />
				<b>'.$language -> getString( 'search_results_topic_replies').':</b> '.$post_result['topic_posts_num'].'
				</td>
				<td class="opt_row2" style="vertical-align: top">'.$post_message.'</td>
			</tr>
			<tr>
				<td colspan="2" class="post_end"></td>
			</tr>');
			
			$post_report -> closeTable();
			$post_report -> drawSpacer( $language -> getString( 'mod_cp_post_reports_this_list'));
			$post_report -> openOpTable();
			
			/**
			 * delete report?
			 */
			
			if ( isset( $_GET['del_rep'])){
				
				$report_to_delete = $_GET['del_rep'];
				
				settype( $report_to_delete, 'integer');
				
				/**
				 * delete report
				 */
				
				$mysql -> delete( 'posts_reports', "`post_report_id` = '$report_to_delete' AND `post_report_post` = '$post_to_draw'");
				
			}
			
			$nothing_found = true;
			
			/**
			 * get reports
			 */
			
			$reports_query = $mysql -> query( "SELECT r.*, u.user_id, u.user_login, u.user_main_group, u.user_other_groups, g.users_group_prefix, g.users_group_suffix FROM posts_reports r
			LEFT JOIN users u ON u.user_id = r.post_report_user
			LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
			WHERE r.post_report_post = '$post_to_draw' ORDER BY r.post_report_time");
			
			while( $reports_result = mysql_fetch_array( $reports_query, MYSQL_ASSOC)){
				
				//clear result
				$reports_result = $mysql -> clear( $reports_result);

				/**
				 * post groups
				 */
				
				$user_groups = array();
				$user_groups = split( ",", $post_result['user_other_groups']);
				$user_groups[] = $post_result['user_main_group'];
			
				if ( $reports_result['post_author'] == -1){
		
					/**
					 * author is deleted
					 */
														
					$post_author = '<a name="post'.$reports_result['post_id'].'" id="post'.$reports_result['post_id'].'">'.$reports_result['users_group_prefix'].$reports_result['post_author_name'].$reports_result['users_group_suffix'].'</a>';
					
				}else{
														
					/**
					 * and user login
					 */
					
					$post_author = '<a name="post'.$reports_result['post_id'].'" id="post'.$reports_result['post_id'].'">'.'<a href="'.parent::systemLink( 'user', array( 'user' => $reports_result['post_author'])).'">'.$reports_result['users_group_prefix'].$reports_result['user_login'].$reports_result['users_group_suffix'].'</a></a>';				
					
				}
				
				/**
				 * report message
				 */
				
				$post_message = $strings -> parseBB( nl2br( $reports_result['post_report_text']), false, false);
			
				$user_groups = array();
				$user_groups = split( ",", $reports_result['user_other_groups']);
				$user_groups[] = $reports_result['user_main_group'];
				
				if ( !$users -> cantCensore( $user_groups))
					$post_message = $strings -> censore( $post_message);
				
				/**
				 * and draw report
			 	*/
			
				$post_report -> addToContent( '<tr>
					<td class="opt_row1" style="width: 170px; vertical-align: top">'.$post_author.'<br />
					'.$time -> drawDate( $reports_result['post_report_time']).'<br /><br />
					<a href="'.parent::systemLink( parent::getId(), array( 'do' => 'post_reports', 'post' => $post_to_draw, 'del_rep' => $reports_result['post_report_id'])).'">'.$language -> getString( 'mod_cp_post_reports_delete').'</a><br />
					</td>
					<td class="opt_row2" style="vertical-align: top">'.$post_message.'</td>
				</tr>
				<tr>
					<td colspan="2" class="post_end"></td>
				</tr>');
				
				
				//tell further code that we found reports
				$nothing_found = false;
				
			}
			
			if ( $nothing_found){
				
				$mysql -> update( array( 'post_reported' => false), 'posts', "`post_id` = '$post_to_draw'");
				
				$post_report -> drawRow( $language -> getString( 'mod_cp_post_reports_list_unmarked'));
				
			}
			
			/**
			 * close tab
			 */
			
			$post_report -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'mod_cp_post_reports_list'), $post_report -> display()));
			
		}else{
			
			/**
			 * not logged in
			 */
			
			$main_error = new main_error();
			$main_error -> type = 'information';
			parent::draw( $main_error -> display());
			
		}
		
	}
	
}

?>