<?

/*
#===========================================================================
|
|	Unisolutions Pegasus
|
|	by Rafał Pitoń
|	Copyright 2007 by Unisolutions
|	http://www.unisolutions.pl
|
#===========================================================================
|
|	This software is released under Creative Commons 2.5 BY-ND PL licence
|	http://creativecommons.org/licenses/by-nd/2.5/pl/
|
#===========================================================================
|
|	Tools list class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class tools{

	public $content;
	private $templates;
		
	function drawButton( $content = ''){
		
		$this -> content .= '<div class="tools_button">'.$content.'</div>';
		
	 }
	 
	function drawSpacer( $title = ''){
			 	
		$this -> content .= '<div class="tools_spacer">'.$title.'</div>';
		
	 }
	 	 
	 function addToContent( $html){
	 	
	 	$this -> content .= $html;
	 	
	 }
	 	 
	 function display( $title, $id){
	 	
	 	return '<div id="tools_container_'.$id.'">
 					<script type="text/JavaScript">
		 				
 						document.onclick = function(e){  
	 					 	
		 					event = e;
		 					
		 					if( navigator.appName == "Microsoft Internet Explorer"){
		 					
		 						actual_element = event.srcElement;
		 					
		 					}else{
		 					
		 						actual_element = event.target;
		 					
		 					}
		 					
		 					var action = 0;
		 					
		 					while( actual_element != document){
		 					
			 					if( actual_element.id == "tools_container_'.$id.'"){
			 						action = 1;
			 					}
			 					
			 					actual_element = actual_element.parentNode;
			 					
			 				}
			 				
			 				if( action == 0){
		 						
		 						hideDiv( \'tools_'.$id.'\');
		 					
		 					}	
			 				
		 				}
		 				
 					</script>
					<div id="tools_switch_'.$id.'" class="tools_switch"><a href="javascript:switchDivDisplay( \'tools_'.$id.'\', \'tools_switch_'.$id.'\', false)">'.$title.'</a></div>
					<div id="tools_'.$id.'" class="tools_border" style="display: none;">
						'.$this -> content.'
					</div>
				</div>';
	 		 	
	 }
	 
}

?>