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
|	Updater Session Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class session{
	
	var $user_is_logged = false;
	
	/**
	 * run constructor
	 */
	
	function __construct(){
	
		//include one global
		global $mysql;
		
		//get session
		
		$user_id = $_SESSION['admin_id'];
		$user_pass = addslashes( $_SESSION['admin_pass']);
		
		settype( $user_id, 'integer');
		
		/**
		 * get user from DB
		 */
		
		$user_query = $mysql -> query( "SELECT user_main_group, user_other_groups FROM users WHERE user_id = '$user_id' AND user_password = '$user_pass'");
		
		if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
			
			$user_groups = array();
			
			if ( strlen($user_result['user_other_groups']) > 0)
				$user_groups = split( ",", $user_result['user_other_groups']);
			
			$user_groups[] = $user_result['user_main_group'];
			
			/**
			 * slect groups
			 */
			
			if ( count( $user_groups) > 0){
			
				$groups_query = $mysql -> query( "SELECT users_group_id FROM users_groups WHERE users_group_id IN (".join(",", $user_groups).") AND users_group_can_use_acp = '1'");
				
				if ( $groups_result = mysql_fetch_array( $groups_query, MYSQL_ASSOC)){
					
					/**
					 * user is admin :]
					 */
					
					$this -> user_is_logged = true;
					
				}
		
			
			
			}
			
		}
		
		/**
		 * try for posts
		 */
		
		if ( !$this -> user_is_logged){
			
			$user_name = uniSlashes(htmlspecialchars(trim( $_POST['admin_login'])));
			$user_pass = trim( $_POST['admin_pass']);
			
			settype( $user_id, 'integer');
			
			$user_pass = md5(md5($user_pass).md5($user_pass));
			
			/**
			 * get user from DB
			 */
			
			$user_query = $mysql -> query( "SELECT user_id, user_main_group, user_other_groups FROM users WHERE user_login = '$user_name' AND user_password = '$user_pass'");
			
			if ( $user_result = mysql_fetch_array( $user_query, MYSQL_ASSOC)){
				
				$user_groups = array();
				
				if ( strlen($user_result['user_other_groups']) > 0)
					$user_groups = split( ",", $user_result['user_other_groups']);
				
				$user_groups[] = $user_result['user_main_group'];
				
				/**
				 * slect groups
				 */
				
				if ( count( $user_groups) > 0){
				
					$groups_query = $mysql -> query( "SELECT users_group_id FROM users_groups WHERE users_group_id IN (".join(",", $user_groups).") AND users_group_can_use_acp = '1'");
					
					if ( $groups_result = mysql_fetch_array( $groups_query, MYSQL_ASSOC)){
						
						/**
						 * user is admin :]
						 */
						
						$this -> user_is_logged = true;
						
						$_SESSION['admin_id'] = $user_result['user_id'];
						$_SESSION['admin_pass'] = $user_pass;
						
					}
			
				}
				
			}
		
		}
		
	}
	
}

?>