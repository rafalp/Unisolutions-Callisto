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
|	Installer Path Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class path{
	
	var $breadcrumbs = array();
	
	/**
	 * adds breadcrumb to path
	 *
	 * @param string $title
	 * @param string $link
	 */
	
	function addBreadcrumb( $title){
		
		$this -> breadcrumbs[] = array(
			'title' => $title
		);
		
	}
		
	/**
	 * displays drawed breadcrumbs
	 *
	 */
	
	function display(){
		
		$breadsrumbs_menu = new menu( $this -> breadcrumbs, 'breadcrumbs');
		return $breadsrumbs_menu -> display();
	}
	
}

?>