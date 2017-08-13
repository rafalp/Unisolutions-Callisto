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
|	Flush Cookies
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class action_flush_cookies extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * kill cookies
		 */
		
		setUniCookie( 'sid', 0, -1);
		setUniCookie( 'uid', 0, -1);
		setUniCookie( 'language', 0, -1);
		setUniCookie( 'style', 0, -1);
		setUniCookie( 'login_user', 0, -1);
		setUniCookie( 'login_key', 0, -1);
		
		/**
		 * draw page
		 */
		
		$smode = 1;
		
		parent::draw( $style -> drawBlock( $language -> getString( 'board_summary_delete_cookies'), $language -> getString( 'board_summary_delete_cookies_done').'<br /><br /><a href="'.parent::systemLink('').'">'.$language -> getString( 'main_menu_website').'</a>'));			
		
		$output -> setRedirect( ROOT_PATH);
				
	}
	
}

?>