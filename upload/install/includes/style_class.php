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
|	Installer Style Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class style{
	
	/**
	 * array containing templates
	 * 
	 * @var array
	 */
	var $templates;
	
	/**
	 * array containing templates to load
	 * 
	 * @var array
	 */
	var $templates_to_load;
	
	/**
	 * array containing style data
	 *
	 * @var array
	 */
	var $style = array();
	
	/**
	 * array containig images code
	 *
	 * @var array
	 */
	var $images;
	
	/**
	 * stylesheet file
	 */
	
	var $style_css;
	
	/**
	 * parts to draw
	 */
	
	var $parts_to_draw = array();
	
	/**
	 * strings to replace
	 */
	
	var $strings_to_replace = array();
	
	/**
	 * class constructor
	 *
	 */
	
	function __construct(){

		/**
		 * load installator style
		 */

		try{		
			
			/**
			 * now load template files
			 */
			
			
			$template_xml_file = file_get_contents( ROOT_PATH.'install/gfx/info.xml');
			$template_xml = new SimpleXMLElement( $template_xml_file);
					
			foreach( $template_xml -> templates[0] as $template_id => $template_file){
				
				$this -> templates[$template_id] = file_get_contents( ROOT_PATH.'install/gfx/templates/'.$template_file);
				$this -> templates[$template_id] = str_ireplace('{STYLE}', ROOT_PATH.'install/gfx/', $this -> templates[$template_id]);
			}
			
			/**
			 * load style images now
			 */
			
			$this -> templates['image'] = file_get_contents( ROOT_PATH.'system/templates/image.htm');
					
			foreach ( $template_xml -> imageset[0] as $image_id => $image)
				$this -> images[$image_id] = $image;				
			}catch(uniException $error){
				
				$error -> criticalError( 2);
			
		}
				
	}
		
	/**
	 * draws page
	 *
	 * @param string $content
	 * @param string $head
	 * @param array $blocks
	 * @param string $foot
	 */
	
	function drawPage( $content){
		
		global $output;
		global $settings;
		global $language;
		global $session;
		global $mysql;
		
		/**
		 * this function generates page html, and sends it to output;
		 */
		
		$template = $this -> templates['main'];
		
		$template = str_ireplace( '{CREDITS}', $this -> drawFoot(), $template);
		
		/**
		 * run parts parsing
		 */
		
		foreach ( $this -> parts_to_draw as $part_name => $part_draw){
			
			if ( $part_draw){
				
				/**
				 * draw part
				 */
				
				$template = str_replace( "<style:".$part_name.">", "", $template);
				$template = str_replace( "</style:".$part_name.">", "", $template);
				
			}else{
				
				/**
				 * delete part
				 */
				
				$template = preg_replace("#\<style:".$part_name."\>(.*?)\</style:".$part_name."\>#si",'', $template);				
			}
			
		}
		
		/**
		 * go trought strings
		 */
		
		foreach ( $this -> strings_to_replace as $string_id => $string_text){
			
			$template = str_replace( "{".$string_id."}", $string_text, $template);
			
		}
		
		/**
		 * now page content
		 */
		
		$content = $this -> removeBrackets($content);
		$template = str_ireplace( '{CONTENT}', $content, $template);
		
		/**
		 * we finished filling template with data, send it to output then;
		 */
		
		$output -> addToOutput( $this -> fixBrackets($template));
		
	}
	
	/**
	 * function adding parts
	 */
	
	function drawPart( $part_name, $part_draw = true){
		
		$this -> parts_to_draw[$part_name] = $part_draw;
	
	}
	
	/**
	 * function adding strings
	 */
	
	function drawString( $string_key, $string_replace = ""){
		
		$this -> strings_to_replace[$string_key] = $string_replace;
	
	}
		
	/**
	 * draws page for ajax
	 *
	 * @param string $content
	 */
	
	function drawAjaxPage( $content){
		
		global $output;
		
		/**
		 * we finished filling template with data, send it to output then;
		 */
		
		$output -> addToOutput( $content);
		
	}
	
	/**
	 * draws standard block
	 *
	 * @param string $title
	 * @param mixed $content
	 * @return string
	 */
	
	function drawBlock( $title, $content){
		
		$template = $this -> templates['block'];
		
		$title = $this -> removeBrackets( $title);
		$content = $this -> removeBrackets( $content);
		
		$template = str_ireplace( '{NAME}', $title, $template);
		$template = str_ireplace( '{CONTENT}', $content, $template);
		
		$template = $this -> fixBrackets( $template);
		
		return $template;
		
	}
	
	/**
	 * draws block containing form/table
	 *
	 * @param string $title
	 * @param mixed $content
	 * @return string
	 */
	
	function drawFormBlock( $title, $content){
		
		$template = $this -> templates['form_block'];
		
		$title = $this -> removeBrackets( $title);
		$content = $this -> removeBrackets( $content);
		
		$template = str_ireplace( '{NAME}', $title, $template);
		$template = str_ireplace( '{CONTENT}', $content, $template);
		
		$template = $this -> fixBrackets( $template);
		
		return $template;
		
	}	
	
	/**
	 * draws blank block
	 *
	 * @param string $title
	 * @param mixed $content
	 * @return string
	 */
	
	function drawBlankBlock( $content){
		
		$template = $this -> templates['blank_block'];
		
		$content = $this -> removeBrackets( $content);
		
		$template = str_ireplace( '{CONTENT}', $content, $template);
		
		$template = $this -> fixBrackets( $template);
		
		return $template;
		
	}
	
	/**
	 * draws error block
	 *
	 * @param string $title
	 * @param mixed $content
	 * @return string
	 */
	
	function drawErrorBlock( $title, $content){
		
		$template = $this -> templates['error_block'];
		
		$title = $this -> removeBrackets( $title);
		$content = $this -> removeBrackets( $content);
		
		$template = str_ireplace( '{NAME}', $title, $template);
		$template = str_ireplace( '{CONTENT}', $content, $template);
		
		$template = $this -> fixBrackets( $template);
		
		return $template;
		
	}
	
	/**
	 * draws information block block
	 *
	 * @param string $title
	 * @param mixed $content
	 * @return string
	 */
	
	function drawInfoBlock( $title, $content){
		
		$template = $this -> templates['info_block'];
		
		$title = $this -> removeBrackets( $title);
		$content = $this -> removeBrackets( $content);
		
		$template = str_ireplace( '{NAME}', $title, $template);
		$template = str_ireplace( '{CONTENT}', $content, $template);
		
		$template = $this -> fixBrackets( $template);
		
		return $template;
		
	}
	
	/**
	 * draws page foot
	 *
	 * @return string
	 */
	
	function drawFoot(){

		/**
		 * lets draw foot without version number
		 */
			
		global $credits;
			
		$foot .= $credits.$simple_link;
		
		return $foot;
		
	}
	
	/**
	 * this function draws bar
	 *
	 * @param string length
	 */
	
	function drawBar( $length){
		
		global $settings;
		
		$template = $this -> templates['bar'];
		
		$length_small = ceil( $length * 0.9);
		
		$template = str_ireplace( '{VALUE}', $length_small, $template);
		$template = str_ireplace( '{TRUE_VALUE}', $length, $template);
		
		return $template;
		
	}
		
	/**
	 * this function draws standard image
	 *
	 * @param string $image
	 * @param string $alt
	 */
	
	function drawImage( $image, $alt = null){
		
		global $settings;
		
		$template = $this -> templates['image'];
		$template = str_ireplace( '{IMAGE}', ROOT_PATH.'install/gfx/'.$this -> images[$image], $template);
		$template = str_ireplace( '{ALT}', $alt, $template);
		
		return $template;
		
	}
	
	/**
	 * draws thick or thoe, depending on value
	 *
	 * @param bool $thick
	 * @return string
	 */
	
	function drawThick( $thick = true, $yes_no = false){
		
		global $language;
		
		if( $yes_no){
			$yes = 'yes';
			$no = 'no';
		}else{
			$yes = 'success';
			$no = 'error';	
		}
		
		if( $thick){
			
			return $this -> drawImage( 'success', $language -> getString( $yes));
			
		}else{
			
			return $this -> drawImage( 'error', $language -> getString( $no));
			
		}
		
	}
		
	function removeBrackets( $string){
		
		$string = str_replace( '{', '[{]', $string);
		$string = str_replace( '}', '[}]', $string);
		
		return $string;
			
	}
	
	
	function fixBrackets( $string){
		
		$string = str_replace( '[{]', '{', $string);
		$string = str_replace( '[}]', '}', $string);
		
		return $string;
			
	}
	
}

?>