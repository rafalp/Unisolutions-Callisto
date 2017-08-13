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
|	Reset password
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

class action_reset_pass extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
				
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'reset_password'));
		
		/**
		 * and add breadcrumb
		 */
		
		$path -> addBreadcrumb( $language -> getString( 'reset_password'), parent::systemLink( parent::getId()));
		
		/**
		 * check if user is logged
		 */
		
		if ( $session -> user['user_id'] == -1){
				
			/**
			 * check step
			 */
			
			if( $_GET['step'] == 2 &&  $session -> checkForm()){
				
				/**
				 * form submited
				 */
				
				$user_login = uniSlashes(htmlspecialchars(trim($_POST['user_login'])));
				$user_mail = $strings -> inputClear( $_POST['user_mail'], false);
				
				/**
				 * select user
				 */
				
				$user_query = $mysql -> query( "SELECT `user_id` FROM users WHERE `user_login` = '$user_login' AND `user_mail` = '$user_mail' AND `user_id` > 0");
				
				$user_found = false;
				
				if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
					
					$user_found = true;
					$user_result = $mysql -> clear( $user_result);
				}
				
				if ( strlen( $user_login) == 0 || strlen( $user_mail) == 0){
					
					/**
					 * one of fields are empty
					 */
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'reset_password'), $language -> getString( 'reset_password_empty_fields')));
					
					$this -> drawForm();
					
				}else if( !$user_found){
					
					/**
					 * nothing found
					 */
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'reset_password'), $language -> getString( 'reset_password_no_user')));
					
					$this -> drawForm();
					
				}else{

					/**
					 * all ok, reset pass, and send it in mail
					 */
					
					$new_pass_bit = substr( md5(time().$session -> user_ip), 0, 8);
					
					$new_pass_sql['user_password'] = md5( md5($new_pass_bit).md5($new_pass_bit));
					
					$mysql -> update( $new_pass_sql, 'users', "`user_id` = '".$user_result['user_id']."'");
					
					/**
					 * kill autologin keys
					 */
					
					$mysql -> delete( "users_autologin", "`users_autologin_user` = '".$user_result['user_id']."'");
					
					/**
					 * send mail
					 */
					
					$language -> setKey( 'new_user_pass', $new_pass_bit);
					$language -> setKey( 'board_login_link', $settings['board_address'].'index.php?act=login');
					
					$mail -> send( trim( $_POST['user_mail']), $language -> getString( 'reset_password_mail_title'), $language -> getString( 'reset_password_mail_text'));
					
					/**
					 * draw message
					 */
					
					parent::draw( $style -> drawBlock( $language -> getString( 'reset_password'), $language -> getString( 'reset_password_done')));
					
				}
				
			}else{
				
				/**
				 * draw simple form
				 */
				
				$this -> drawForm();
				
			}
			
		}else{
			
			/**
			 * user is logged in
			 */
						
			$main_error = new main_error();
			$main_error -> type = 'information';
			$main_error -> message = $language -> getString( 'reset_password_logged_in');
			parent::draw( $main_error -> display());
			
		}
		
	}
	
	function drawForm(){
		
		global $language;
		global $style;
		
		$reset_pass_form = new form();
		$reset_pass_form -> openForm( parent::systemLink( parent::getId(), array( 'step' => 2)));
		$reset_pass_form -> openOpTable();
		
		$reset_pass_form -> drawTextInput( $language -> getString( 'reset_password_login'), 'user_login');
		$reset_pass_form -> drawTextInput( $language -> getString( 'reset_password_mail'), 'user_mail');
		
		$reset_pass_form -> closeTable();
		$reset_pass_form -> drawButton( $language -> getString( 'reset_password_button'));
		$reset_pass_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'reset_password'), $reset_pass_form -> display())); 
		
	}
	
}

?>