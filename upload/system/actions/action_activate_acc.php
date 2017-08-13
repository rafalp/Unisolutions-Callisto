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
|	Activate user account
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

class action_activate_acc extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
				
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'activate_account'));
		
		/**
		 * and add breadcrumb
		 */
		
		$path -> addBreadcrumb( $language -> getString( 'activate_account'), parent::systemLink( parent::getId()));
		
		/**
		 * check if user is logged
		 */
		
		if ( $session -> user['user_id'] == -1){
			
			/**
			 * get data
			 */
				
			$user_to_activate = $_GET['user'];
			$activation_code = $_GET['code'];
			
			settype( $user_to_activate, 'integer');
			
			/**
			 * get user data
			 */
			
			$user_found = false;
			
			$user_query = $mysql -> query( "SELECT `user_id`, `user_active`, `user_activation_code` FROM users WHERE `user_id` = '$user_to_activate'");
			
			if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
				
				$user_found = true;
				
				$user_result = $mysql -> clear( $user_result);
				
			}
			
			/**
			 * check all data
			 */
			
			if ( !$user_found){
				
				/**
				 * wrong user
				 */
								
				$main_error = new main_error();
				$main_error -> type = 'error';
				$main_error -> message = $language -> getString( 'activate_account_user_not_found');
				parent::draw( $main_error -> display());
				
			}else if( $user_result['user_active']){
				
				/**
				 * user is active
				 */
				
				$main_error = new main_error();
				$main_error -> type = 'error';
				$main_error -> message = $language -> getString( 'activate_account_user_alreadyactive');
				parent::draw( $main_error -> display());
				
			}else if( $user_result['user_activation_code'] == "0"){
				
				/**
				 * user can be activated by admin only
				 */
				
				$main_error = new main_error();
				$main_error -> type = 'error';
				$main_error -> message = $language -> getString( 'activate_account_user_by_dmin_only');
				parent::draw( $main_error -> display());
				
			}else if( $user_result['user_activation_code'] != $activation_code){
				
				/**
				 * code is wrong
				 */
								
				$main_error = new main_error();
				$main_error -> type = 'error';
				$main_error -> message = $language -> getString( 'activate_account_user_wrong_code');
				parent::draw( $main_error -> display());
				
			}else{
				
				/**
				 * activate account
				 */
				
				$update_acc_sql['user_active'] = true;
				$update_acc_sql['user_main_group'] = 3;
				
				$mysql -> update( $update_acc_sql, 'users', "`user_id` = '$user_to_activate'");
				
				/**
				 * draw message
				 */
				
				parent::draw( $style -> drawBlock( $language -> getString( 'activate_account'), $language -> getString( 'activate_account_done')));
				
			}
			
		}else{
			
			/**
			 * user is logged in
			 */
			
			$main_error = new main_error();
			$main_error -> type = 'information';
			parent::draw( $main_error -> display());
			
		}
		
	}
	
}

?>