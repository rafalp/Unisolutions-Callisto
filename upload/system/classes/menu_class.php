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
|	Menu Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class menu{
	
	/**
	 * generated menu html
	 *
	 * @var string
	 */
	
	public $html;

	/**
	 * creates menu object
	 *
	 * @param array $elements
	 * @param type '.$type.'
	 */
	
	function __construct( $elements, $type){
		
		global $style;
		global $settings;
		
		/**
		 * take templates from style, and parse them
		 */
		
		if ( $type != 'inblock'){
			
			/**
			 * we are operating on main template file
			 */
			
			if ( !defined( 'SIMPLE_MODE')){
			
				/**
				 * preparse template
				 */
				
				if ( $settings['board_path_type'] == 0){
					
					/**
					 * do clearing
					 */
					
					$style -> templates['main'] = preg_replace("#\<style:path_step\>(.*?)\</style:path_step\>#si",'', $style -> templates['main']);
					$style -> templates['main'] = str_replace( "<style:path_standard>", "", $style -> templates['main']);
					$style -> templates['main'] = str_replace( "</style:path_standard>", "", $style -> templates['main']);
					
				}else{
					
					/**
					 * do clearing
					 */
					
					$style -> templates['main'] = preg_replace("#\<style:path_standard\>(.*?)\</style:path_standard\>#si",'', $style -> templates['main']);
					$style -> templates['main'] = str_replace( "<style:path_step>", "", $style -> templates['main']);
					$style -> templates['main'] = str_replace( "</style:path_step>", "", $style -> templates['main']);
					
				}
				
			}
			
			$template = $style -> templates['main'];
			
		}else{
			
			$template = $style -> templates['inblock_menu'];
		
		}
				
		$template = substr( $template, stripos( $template, '<'.$type.'>') + strlen($type)+2, (stripos( $template, '</'.$type.'>') - stripos( $template, '<'.$type.'>') - strlen( '</'.$type.'>') + 1));
		
		$this -> element_template = substr( $template, stripos( $template, '<element>') + 9, (stripos( $template, '</element>') - stripos( $template, '<element>') - strlen( '</element>') + 1));
		
		if ( $type != 'inblock' && !defined( 'SIMPLE_MODE')){
			
			$this -> element_last_template = substr( $template, stripos( $template, '<element_last>') + 14, (stripos( $template, '</element_last>') - stripos( $template, '<element_last>') - strlen( '</element_last>') + 1));
			
		}
		
		if( stristr($template, '<spacer>') != false)
			$this -> spacer_template = substr( $template, stripos( $template, '<spacer>') + 8, (stripos( $template, '</spacer>') - stripos( $template, '<spacer>') - strlen( '</spacer>') + 1));
		
		/**
		 * no go trought elements list, and build up html code
		 */
		
		$elemets_num = count( $elements);
		$actual_element = 1;
		
		foreach ( $elements as $element){
			
			
			if ( isset($element['action'])){
					
				/**
				 * element is action link
				 */
				
				$links[] = $this -> linkAction( $element['title'], $element['action'], $element['gets']);
				
					
			}else{
						
				if ( $type != 'inblock' && $actual_element > 1 && $actual_element == $elemets_num && !defined( 'SIMPLE_MODE')){
					
					$last_link = $this -> linkLast( $element['title']);
				
				}else{
					
					$links[] = $this -> link( $element['title'], $element['www'], $element['target']);
				
				}
			}
			
			$actual_element++;
			
		}
		
		/**
		 * finish up all stuff
		 */
			
		$template = substr_replace( $template, "{CONTENT}",stripos( $template, "<element>"), stripos( $template, "</element>") + strlen("</element>") - stripos( $template, "<element>"));
		
		/**
		 * if spacers are present, remove them
		 */
		
		if( stristr( $template, "<spacer>")){
			
			$template = substr_replace( $template, '',stripos( $template, "<spacer>"), stripos( $template, "</spacer>") + strlen("</spacer>") - stripos( $template, "<spacer>"));
		
		}
		
		/**
		 * if last element are present, remove them
		 */
				
		if ( !defined( 'SIMPLE_MODE') && $type != 'inblock'){
		
			$template = substr_replace( $template, '', stripos( $template, '<element_last>'), (stripos( $template, '</element_last>') - stripos( $template, '<element_last>') + strlen( '</element_last>') + 1));

		}
		
		/**
		 * send generated html to output
		 */
		
		$this -> html = str_ireplace( '{CONTENT}', join( $this -> spacer_template, $links), $template);
		
		/**
		 * and last element
		 */
		
		if ( strlen( $last_link) > 0)
			$this -> html .= $last_link;
		
	}
	
	function display(){
		
		return $this -> html;
		
	}
	
	/**
	 * draws standard link to blace beyond pegasus
	 *
	 * @param label $name
	 * @param link $http
	 * @param link target $target
	 * @return string
	 */
	
	function link( $name, $http, $target = 2){
		
		$targets[0] = '_blank';
		$targets[1] = '_parent';
		$targets[2] = '_self';
		$targets[3] = '_top';
		
		$template = $this -> element_template;
		
		$template = str_ireplace( "{URL}", $http, $template);
		$template = str_ireplace( "{TARGET}", $targets[$target], $template);
		$template = str_ireplace( "{TITLE}", $name, $template);
		
		return $template;
		
	}
	
	/**
	 * draws last link to blace beyond pegasus
	 *
	 * @param label $name
	 * @param link $http
	 * @param link target $target
	 * @return string
	 */
	
	function linkLast( $name){
				
		$template = $this -> element_last_template;
		
		$template = str_ireplace( "{TITLE}", $name, $template);
		
		return $template;
		
	}
		
	/**
	 * draws action link
	 *
	 * @param label $name
	 * @param module $module
	 * @return html
	 */
	
	function linkAction( $title, $name, $gets = null){
		
		global $settings;
		
		$index_path = '';
		$link = '';
		
		if ( defined( 'ACP')){
			$index_path = ACP_PATH;
		}else{
			if( defined( 'SIMPLE_MODE' ))
				$index_path = SIMPLE_PATH;
		}	
		
		settype( $gets, 'array');
		
		foreach( $gets as $get_name => $get_value)
			$link .= '&'.$get_name.'='.$get_value;
		
		$template = $this -> element_template;
		
		$template = str_ireplace( "{URL}", ROOT_PATH.$index_path.'index.php?act='.$name.$link, $template);
		$template = str_ireplace( "{TARGET}", '_self', $template);
		$template = str_ireplace( "{TITLE}", $title, $template);
		
		return $template;
		
	}
	
}

?>