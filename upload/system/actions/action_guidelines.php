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
|	Board guidelines
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

class action_guidelines extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
					
		/**
		 * define guidelines title
		 */
		
		if ( !empty( $settings['guidelines_link_title'])){
					
			$guidelines_title = $settings['guidelines_link_title'];
			
		}else{
			
			$guidelines_title = $language -> getString( 'main_menu_guidelines');
			
		}
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $guidelines_title);
		
		/**
		 * and add breadcrumb
		 */
		
		$path -> addBreadcrumb( $guidelines_title, parent::systemLink( parent::getId()));
		
		/**
		 * draw guidelines
		 */
		
		parent::draw($style -> drawBlock( $guidelines_title, $strings -> parseBB(nl2br($settings['guidelines_board']), true, true)));
		
	}
	
}

?>