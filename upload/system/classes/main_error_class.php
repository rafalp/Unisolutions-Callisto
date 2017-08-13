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
|	Users Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class main_error{
	
	/**
	 * target user
	 */
	
	var $user = '';
	
	/**
	 * draw loging form
	 */
	
	var $loging = false;
	
	/**
	 * problem message
	 */
	
	var $message = '';
	
	/**
	 * type
	 */
	
	var $type = 'information';
	
	/**
	 * drawed form
	 */
	
	var $form;
	
	/**
	 * construct
	 */
	
	function __construct(){	
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * open form
		 */
		
		$this -> form = new form();
		
	}
		
	/**
	 * display
	 *
	 */
	
	function display(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumb
		 */
		
		$path -> addBreadCrumb( $language -> getString( 'callisto_message'), ROOT_PATH);
		
		/**
		 * set title
		 */
		
		$output -> setTitle( $language -> getString( 'callisto_message'));
		
		/**
		 * add first element to form
		 */
		
		$this -> form -> drawSpacer( $language -> getString( $this -> type));
		$this -> form -> openOpTable();
		
		/**
		 * we will draw diffrent reasons depending on what we have
		 */
		
		if ( strlen( $this -> message) > 0){
		
			$this -> form -> drawRow( $this -> message);
		
		}else{
			
			if ( $session -> user['user_id'] != -1){
				
				/**
				 * logged user
				 */
				
				$language -> setKey( 'user_name', $users -> users_groups[$session -> user['user_main_group']]['users_group_prefix'].$session -> user['user_login'].$users -> users_groups[$session -> user['user_main_group']]['users_group_suffix']);
				
				$reasons = array();
				
				$reasons[] = $language -> getString( 'callisto_message_reason_1');
				$reasons[] = $language -> getString( 'callisto_message_reason_2');
				$reasons[] = $language -> getString( 'callisto_message_reason_3');
				
				$this -> form -> drawRow( $language -> getString( 'callisto_message_member').'<br /><br /><ul><li>'.join( "</li><li>", $reasons).'</li></ul>');
				
			}else{
				
				/**
				 * guest
				 */
				
				$reasons = array();
				
				$reasons[] = $language -> getString( 'callisto_message_reason_0');
				$reasons[] = $language -> getString( 'callisto_message_reason_1');
				$reasons[] = $language -> getString( 'callisto_message_reason_2');
				$reasons[] = $language -> getString( 'callisto_message_reason_3');
				
				$this -> form -> drawRow( $language -> getString( 'callisto_message_guest').'<br /><br /><ul><li>'.join( "</li><li>", $reasons).'</li></ul>');
				
			}
			
		}
		
		$this -> form -> closeTable();
		
		/**
		 * add loging section to form
		 */
		
		/**
		 * add useful links
		 */
		$this -> form -> drawSpacer( $language -> getString( 'callisto_message_links'));
		$this -> form -> openOpTable();
		
		/**
		 * we will draw diffrent reasons depending on what we have
		 */
			
		$useful_links = array();
		
		/**
		 * links for user
		 */
		
		if ( $session -> user['user_id'] == -1){
			
			$useful_links[] = '<a href="'.ROOT_PATH.'index.php?act=login">'.$language -> getString( 'login').'</a>';
			$useful_links[] = '<a href="'.ROOT_PATH.'index.php?act=register">'.$language -> getString( 'register').'</a>';
			
		}else{
			
			$useful_links[] = '<a href="'.ROOT_PATH.'index.php?act=profile">'.$language -> getString( 'user_menu_settings').'</a>';
			
		}
		
		$useful_links[] = '<a href="'.ROOT_PATH.'index.php?act=help">'.$language -> getString( 'main_menu_help').'</a>';
		$useful_links[] = '<a href="'.ROOT_PATH.'">'.$language -> getString( 'forum_main_page').'</a>';
			
		$this -> form -> drawRow( join( "<br />", $useful_links));
				
		$this -> form -> closeTable();
		
		/**
		 * lets draw our message
		 */
		
		return ($style -> drawFormBlock( $language -> getString( 'callisto_message'), $this -> form -> display()));
		
	}
	
}

?>