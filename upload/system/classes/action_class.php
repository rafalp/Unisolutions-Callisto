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
|	Action Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
abstract class action{
	
	/**
	 * this is abstract class for modules/ blocks etc.
	 */
	
	var $id;
	
	function systemLink( $action, $gets = null, $anch = null){
		
		global $settings;
		
		if( defined( 'SIMPLE_MODE' ))
			$simple_path = SIMPLE_PATH;
		
		$link = ROOT_PATH.$simple_path.'index.php?act='.$action;
		
		if($gets != null)
		{
			foreach( $gets as $get_name => $get_value)
				$link .= '&'.$get_name.'='.$get_value;
		}
		
		if (USE_SID_IN_QUERY)
		{
			global $session;
			
			$link .= '&sid='.$session -> session_id;	
		}
		
		if ( $anch != null)
			$link .= '#'.$anch;
		
		return $link;
	}
	
	function adminLink( $action, $gets = null, $anch = null){
		
		global $settings;
		
		if($settings['site_frontend_disable']){
			$link = ROOT_PATH.'index.php?act='.$action;
		}else{
			$link = ROOT_PATH.ACP_PATH.'index.php?act='.$action;
		}
			
		if($gets != null){
			foreach( $gets as $get_name => $get_value)
				$link .= '&'.$get_name.'='.$get_value;
		}
				
		if ( $anch != null)
			$link .= '#'.$anch;
		
		return $link;
	}
	
	
	function adminSectionLink( $action, $gets = null, $anch = null){
		
		global $settings;
		
		$section = $_GET['section'];
		
		if($settings['site_frontend_disable']){
			$link = ROOT_PATH.'index.php?section='.$section.'&act='.$action;
		}else{
			$link = ROOT_PATH.ACP_PATH.'index.php?section='.$section.'&act='.$action;
		}
			
		if($gets != null){
			foreach( $gets as $get_name => $get_value)
				$link .= '&'.$get_name.'='.$get_value;
		}
				
		if ( $anch != null)
			$link .= '#'.$anch;
		
		return $link;
	}
	
	function getId(){
		
		global $actual_action;
		
		return $actual_action;
		
	}
	
	function draw( $html){
		
		global $page;
		$page .= $html;
			
	}
	
}

?>