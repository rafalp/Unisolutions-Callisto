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
|	ACP Section Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
abstract class acp_section{
	
	/**
	 * this is abstract class for modules/ blocks etc.
	 */
	
	var $id;
	var $path;
	var $name;
	var $info;
	
	function adminLink( $section, $gets = null, $anch = null){
		
		$link = ROOT_PATH.ACP_PATH.'index.php?section='.$section;
					
		if($gets != null){
			foreach( $gets as $get_name => $get_value)
				$link .= '&'.$get_name.'='.$get_value;
		}
				
		if ( $anch != null)
			$link .= '#'.$anch;
		
		return $link;
	}
	
	function systemLink( $act, $gets = null, $anch = null){
		
		$link = ROOT_PATH.'index.php?act='.$act;
							
		if($gets != null){
			foreach( $gets as $get_name => $get_value)
				$link .= '&'.$get_name.'='.$get_value;
		}
				
		if ( $anch != null)
			$link .= '#'.$anch;
		
		return $link;
	}
	
	function getId(){
		
		global $actual_section;
		
		return $actual_section;
		
	}
	
	function getName(){
		
		global $actual_module;
		global $modules;
		
		return $modules[$actual_module]['name'];
		
	}
	
	function getInfo(){
		
		global $actual_module;
		global $modules;
		
		return $modules[$actual_module]['info'];
		
	}
	
	function getType(){
		
		global $generating_phase;
		
		return $generating_phase;
		
	}
	
	function getVar( $id){
		
		global $actual_block;
		
		return $actual_block['var'.$id];
		
	}
	
	function drawImg( $img){
		
		global $modules;
		global $actual_block;
		
		$link = ROOT_PATH.'modules/'.$modules[$actual_block]['path'].'/images/'.$img;

		return '<img src="'.$link.'" alt="" />';
	}
	
	/**
	 * sends section html to proper place in page
	 *
	 * @param string $html
	 */
	
	function draw( $html){
		
		global $page;
			
		$page .= $html;
			
	}
	
	/**
	 * sends section block
	 *
	 * @param string $html
	 */
	
	function drawBlock( $html){
		
		global $blocks;
			
		$blocks .= $html;
			
	}
	
	function drawSubSections( $sections, $subsections){
		
		include( FUNCTIONS_GLOBALS);
		
		$generated_html = '';
		
		/**
		 * end draw it
		 */
		
		foreach ( $sections as $section_name){
			
			/**
			 * begin drawing menu
			 */
			
			unset( $section_menu);
			$subsection_links = array();
			
			foreach ( $subsections as $subsection_name => $subsection_parent){
			
				if ( $subsection_parent == $section_name){
					
					$subsection_links[] = array(
						'title' => $language -> getString( 'acp_'.$this -> getId().'_subsection_'.$subsection_name),
						'type' => 2,
						'www' => $acp_path.'index.php?section='.$this -> getId().'&act='.$subsection_name,
					);
					
				}
				
			}
			
			$section_menu = new menu( $subsection_links, 'inblock');
			$generated_html .= $style -> drawFormBlock( $language -> getString( 'acp_'.$this -> getId().'_section_'.$section_name), $section_menu -> display());
						
		}
		
		/**
		 * and draw it
		 */
		
		$this -> drawBlock( $generated_html);
		
	}

}

?>