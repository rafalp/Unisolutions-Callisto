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
|	Mail user form
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

class action_mail_user extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
				
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'mail_user'));
		
		/**
		 * check if user is logged
		 */
		
		if ( $session -> user['user_id'] != -1){
			
			/**
			 * check perms
			 */
			
			if ( $session -> user['user_can_send_mails']){
				
				/**
				 * get data
				 */
					
				$user_to_mail = $_GET['user'];
				
				settype( $user_to_mail, 'integer');
				
				/**
				 * and add breadcrumb
				 */
				
				$path -> addBreadcrumb( $language -> getString( 'mail_user'), parent::systemLink( parent::getId(), array( 'user' => $user_to_mail)));
				
				/**
				 * select user
				 */
				
				$user_query = $mysql -> query( "SELECT u.user_login, u.user_mail, u.user_want_mail, g.users_group_prefix, g.users_group_suffix FROM users u LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE u.user_id = '$user_to_mail' AND u.user_id > '0'");
				
				if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
					
					/**
					 * check, if user want to get mails
					 */
					
					$user_result = $mysql -> clear( $user_result);
										
					if ( $user_result['user_want_mail']){
						
						/**
						 * draw mail send form
						 */
						
						if ( $_GET['do'] == 'send' && $session -> checkForm()){
							
							/**
							 * send mail
							 */
							
							$mail_topic = htmlspecialchars_decode( trim( $_POST[ 'mail_topic']));
							$mail_text = htmlspecialchars_decode( trim( $_POST[ 'mail_text']));
								
							if ( strlen( $mail_topic) == 0 || strlen($mail_text) == 0){
							
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'mail_user'), $language -> getString( 'mail_user_error')));						
								
								$this -> drawForm( $user_to_mail, $user_result);
							
							}else{
								
								/**
								 * send mail
								 */
								
								$language -> setKey( 'sender_login', $session -> user['user_login']);
								$language -> setKey( 'sender_profile_link', $settings['board_address'].'/index.php?act=user&user='.$session -> user['user_id']);
								
								$mail -> send( $user_result['user_mail'], $language -> getString( 'mail_user_title').': '.$mail_topic, $language -> getString( 'mail_user_text').$mail_text);
								
								/**
								 * add log
								 */
								
								$logs -> addMailLog( $session -> user['user_id'], $user_to_mail, $_POST[ 'mail_topic']);
								
								/**
								 * draw message
								 */
								
								parent::draw( $style -> drawBlock( $language -> getString( 'mail_user'), $language -> getString( 'mail_user_done')));						
								
							}
							
						}else{
							
							/**
							 * draw form
							 */
							
							$this -> drawForm( $user_to_mail, $user_result);
						}
						
					}else{
						
						/**
						 * user dont want to get mails
						 */
												
						$main_error = new main_error();
						$main_error -> type = 'information';
						$main_error -> message = $language -> getString( 'mail_user_notwant');
						parent::draw( $main_error -> display());
						
					}
					
				}else{
					
					/**
					 * user not found send mails
					 */
					
					$main_error = new main_error();
					$main_error -> type = 'error';
					$main_error -> message = $language -> getString( 'mail_user_nofound');
					parent::draw( $main_error -> display());
									
				}
			
			}else{
				
				/**
				 * user cant send mails
				 */
				
				$main_error = new main_error();
				$main_error -> type = 'error';
				parent::draw( $main_error -> display());
								
			}
				
		}else{
			
			/**
			 * user isn't logged in
			 */
			
			$main_error = new main_error();
			$main_error -> type = 'information';
			parent::draw( $main_error -> display());
			
		}
		
	}
	
	function drawForm( $user_to_mail, $user_result){
		
		include( FUNCTIONS_GLOBALS);

		/**
		 * remind
		 */
		
		parent::draw( $style -> drawBlock( $language -> getString( 'information'), $language -> getString( 'mail_user_remind')));
		
		/**
		 * draw mail form
		 */
		
		$send_mail_link = array( 'user' => $user_to_mail, 'do' => 'send');
		
		$new_mail_form = new form();
		$new_mail_form -> openForm( parent::systemLink( parent::getId(), $send_mail_link));
		$new_mail_form -> openOpTable();
		
		$mail_topic = trim( $_POST[ 'mail_topic']);
		$mail_text = trim( $_POST[ 'mail_text']);
		
		$new_mail_form -> drawInfoRow( $language -> getString( 'mail_user_form_receiver'), '<a href="'.parent::systemLink( 'user', array( 'user' => $user_to_mail)).'">'.$user_result['users_group_prefix'].$user_result['user_login'].$user_result['users_group_suffix'].'</a>');
		$new_mail_form -> drawTextInput( $language -> getString( 'mail_user_form_topic'), 'mail_topic', htmlspecialchars( $mail_topic));
		$new_mail_form -> drawTextBox( $language -> getString( 'mail_user_form_text'), 'mail_text', htmlspecialchars( $mail_text));
		
		$new_mail_form -> closeTable();
		$new_mail_form -> drawButton( $language -> getString( 'mail_user_button'));
		$new_mail_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'mail_user'), $new_mail_form -> display()));
		
		
	}
	
}

?>