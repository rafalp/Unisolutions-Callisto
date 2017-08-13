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
|	Output Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class output{
	
	//templates to parse
	public $templates = array();	
	
	//output
	public $to_display;
		
	/**
	 * value containing site title
	 *
	 * @var string
	 */
	var $title;
	
	/**
	 * value containing page title
	 *
	 * @var string
	 */
	var $simpletitle;
	
	/**
	 * value containing redirect target
	 *
	 * @var string
	 */
	var $redirect;
	
	/**
	 * value containing file target
	 *
	 * @var bool
	 */
	
	var $header_file = false;
	
	/**
	 * value containing file path
	 *
	 * @var string
	 */
	
	var $header_file_path;
	
	/**
	 * value containing file name
	 *
	 * @var string
	 */
	
	var $header_file_name;
	
	/**
	 * value containing file size
	 *
	 * @var string
	 */
	
	var $header_file_size;
	
	/**
	 * value containing file type
	 *
	 * @var string
	 */
	
	var $header_file_type;
		
	function __construct(){
		
		/**
		 * flush clients cache
		 */
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		
	}
	
	function addToOutput( $html){
		
		$this -> to_display .= $html;
		
	}
	
	function addToParse( $tag, $value){
		
		/**
		 * This function enables us to insert into templates array new keys to replace
		 */
		
		$tag = strtoupper( $tag);
		
		$tag = str_ireplace( '{', '', $tag);
		$tag = str_ireplace( '}', '', $tag);
		
		$this -> templates[$tag] = $value;
		
	}
	
	function parse(){
		
		/**
		 * this function parses output before we send it back to client
		 */
		
		$parsed = $this -> to_display;
		
		foreach ( $this -> templates as $key_to_change => $replacement)		
			$parsed = str_ireplace( '{'.$key_to_change.'}', $replacement, $parsed);		
		
		/**
		 * return parsed html
		 */
		
		$this -> to_display = $parsed;
	}
	
	function display(){
		
		$this -> parse();
		
		echo($this -> to_display);

	}
	
	function openPage( $style){
		
		global $settings;
		
		$template = file_get_contents( ROOT_PATH.'system/templates/header.htm');
		
		/**
		 * page title and style firs
		 */
		
		$templates['PAGE_NAME'] = $this -> title;
		$templates['STYLE'] = $style;
		
		$templates['META_KEYS'] = $settings['meta_keys'];
		$templates['META_DESC'] = $settings['meta_desc'];
		
		/**
		 * now java scripts
		 */
		
		$scripts = glob( ROOT_PATH.'system/java/*.js');
		
		foreach ($scripts as $id => $value){
	 		$parsed_scripts .=  "<script type=\"text/javascript\" src=\"$value\"></script>\n";
	 	}
		
	 	$templates['SCRIPTS'] = $parsed_scripts;
		
	 	/**
	 	 * favicon
	 	 */
	 	
	 	if( empty( $settings['board_favicon'])){
	 		
	 		/**
	 		 * use default
	 		 */
	 		
			$templates['FAVICON'] = ROOT_PATH.'favicon.ico';
			
	 	}else{
	 		
	 		/**
	 		 * use custom
	 		 */
	 		
			$templates['FAVICON'] = ROOT_PATH.$settings['site_favicon'];	 		
	 		
	 	}
	 			
		/**
		 * licence ifnromation now
		 */
			
		global $licence;
		
		$templates['LICENCE'] = $licence;
		
		/**
		 * parse template
		 */
		
		foreach ( $templates as $key_to_change => $replacement)		
			$template = str_ireplace( '{'.$key_to_change.'}', $replacement, $template);
		
		/**
		 * page header is done, send it to output
		 */
			
		$this -> to_display = $template.$this -> to_display;
		
	}
	
	function closePage(){
		
		$template = file_get_contents( ROOT_PATH.'system/templates/footer.htm');
			
		$this -> to_display .= $template;
		
	}
	
	function getTitle(){
		
		if( $this -> title == null){
			return false;
		}else{
			return true;
		}		
		
	}
	
	function setTitle( $title){
		
		global $settings;
		global $language;
		
		$this -> simpletitle = $title;
				
		if (defined( 'ACP') || defined( 'INSTALL')){
			
			/**
			 * we are in ACP, so titles looks like this:
			 * SCIRPT NAME: title
			 */
			
			$title = UNI_PRODUCT.': '.$title;
		
		}else{
			
			/**
			 * we are in frontend, so titles looks like this:
			 * title - sitename
			 */
			
			$title = $title.' - '.$settings['board_name'];
			
		}
		
		$this -> title = $title;
		
	}
	
	function setRedirect( $target = null, $gets = null){
		
		global $settings;
		
		$redirect_path = ROOT_PATH;
			
		if( defined( 'ACP'))
			$redirect_path .= ACP_PATH;
			
		$redirect_path .= 'index.php';
		
		if ( $target != null)
			$redirect_path .= '?act='.$target;
		
		if($gets != null){
			foreach( $gets as $get_name => $get_value)
				$redirect_path .= '&'.$get_name.'='.$get_value;
		}
			
		$this -> redirect = $redirect_path;
		
	}

	function setRemoteRedirect( $target = null){
		
		global $settings;
						
		$this -> redirect = $target;
		
	}
	
	function outputFile( $path, $name, $size = null, $type = null){
		
		if( file_exists($path)){
			
			if ( $size != null)
				header('Content-Length: '.$size);
			
			if ( $type != null)
				header('Content-type: '.$type);
			
			/**
			 * flush clients cache
			 */
									
			readfile( $path);
		
		}
				
	}
	
	function __destruct(){
		
		global $settings;
				
		/**
		 * content
		 */
		
		header( 'Content-Type: text/html; charset=UTF-8');	
		
		/**
		 * refresh
		 */
		
		if ( strlen( $this -> redirect) > 0){
			
			if ( $settings['redirect_time'] == 0){
			
				header( 'Location: '.$this -> redirect );
			
			}else{
			
				header( 'refresh: '.$settings['redirect_time'].'; url='.$this -> redirect );
			
			}
		}
		
		/**
		 * Page generation is over, lets send parsed result to client
		 */
		
		$this -> display();
			
	}
	
}

?>