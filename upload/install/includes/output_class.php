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
|	Installer Output Class
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
		
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				
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
	
	function openPage(){
		
		global $settings;
		
		$template = file_get_contents( ROOT_PATH.'system/templates/header.htm');
		
		/**
		 * page title and style firs
		 */
		
		$templates['PAGE_NAME'] = $this -> title;
		$templates['STYLE'] = 'gfx/style.css';
		
		$templates['META_KEYS'] = 'Unisolutions, Callisto, Installation';
		$templates['META_DESC'] = 'Unisolutions Callisto Installation';
		
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
		 * redirecting scripts
		 */
			
		$templates['REDIRECT'] = '';
		
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
				
		$this -> simpletitle = $title;
			
		$this -> title = $title;
		
	}
	
	function setRedirect( $target = null, $gets = null){
				
		$redirect_path = ROOT_PATH.'install/index.php?act='.$target;
		
		if($gets != null){
			foreach( $gets as $get_name => $get_value)
				$redirect_path .= '&'.$get_name.'='.$get_value;
		}
				
		$this -> redirect = $redirect_path;
		
	}

	function setRemoteRedirect( $target = null){
								
		$this -> redirect = $target;
		
	}
		
	public function __destruct(){
				
		/**
		 * refresh
		 */
		
		if ( strlen( $this -> redirect) > 0)
			header( 'refresh: 3; url='.$this -> redirect );
		
		/**
		 * Page generation is over, lets send parsed result to client
		 */
		
		$this -> display();
		
	}
	
}

?>