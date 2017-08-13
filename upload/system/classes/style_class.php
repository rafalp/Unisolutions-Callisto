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
|	Style Class
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
		
		global $settings;
		global $session;		
		
		if( defined( 'ACP' )){
			
			/**
			 * we are in acp, so lets load acp template
			 */
			
			$this -> loadAcpStyle();
		
		}elseif( defined( 'SIMPLE_MODE' )){
			
			/**
			 * we are in simple, so lets load simple template
			 */
			
			$this -> loadSimpleStyle();
			
		
		}else{
		
			/**
			 * we have to check, which one style have to be loaded: users, or system default
			 */
			
			if( $settings['style_allow_change']){
				
				/**
				 * style overrunning not active, we have to load custom one
				 */
				
				$this -> loadStyle( $session -> user['user_style']);
				
				$this -> style_id = $session -> user['user_style'];
				
			}else{
				
				/**
				 * style overrunning activated, we have to load default one
				 */
				
				$this -> loadStyle( $settings['default_style']);
				
				$this -> style_id = $settings['default_style'];
				
			}
			
		}
		
		/**
		 * load style images now
		 */
		
		$this -> loadImages();
		
	}
	
	/**
	 * loads acp style files
	 *
	 */
	
	function loadAcpStyle(){
		
		global $page;
		
		/**
		 * this function loads acp style
		 */
		
		$this -> style['path'] = ACP_STYLE;
				
		try{		
			
			/**
			 * now load template files
			 */
			
			
			$template_xml_file = file_get_contents( ROOT_PATH.'styles_acp/'.$this -> style['path'].'/info.xml');
			$template_xml = new SimpleXMLElement( $template_xml_file);
					
			foreach( $template_xml -> templates[0] as $template_id => $template_file){
				
				$this -> templates[$template_id] = file_get_contents( ROOT_PATH.'styles_acp/'.$this -> style['path'].'/templates/'.$template_file);
				$this -> templates[$template_id] = str_ireplace('{STYLE}', ROOT_PATH.'styles_acp/'.$this -> style['path'], $this -> templates[$template_id]);
			}
							
			/**
			 * set css file
			 */
	
			$this -> style_css = ROOT_PATH.'styles_acp/'.ACP_STYLE.'/style.css';
				
		}catch(uniException $error){
			
			$error -> criticalError( 2);
			
		}
		
		
		
	}
	/**
	 * loads simple style files
	 *
	 * @param string $style
	 */
	
	function loadSimpleStyle(){
				
		global $page;
			
		/**
		 * we know the path, so lets use it
		 */
		
		$this -> style['path'] = SIMPLE_PATH;
		
		/**
		 * set css file
		 */

		$this -> style_css = ROOT_PATH.SIMPLE_PATH.'style.css';
		
		/**
		 * now load rest form info xml file
		 */
		
		$style_xml_file = file_get_contents( ROOT_PATH.$this -> style['path'].'info.xml');
		$style_xml = new SimpleXMLElement( $style_xml_file);
		
		$this -> style['name'] = $style_xml -> details -> name;	
		$this -> style['author'] = $style_xml -> details -> author;
		$this -> style['www'] = $style_xml -> details -> www;
		$this -> style['creation'] = time();
		$this -> style['users'] = 1;
		
		/**
		 * now load template files
		 */
				
		foreach( $style_xml -> templates[0] as $template_id => $template_file){
			
			$this -> templates[$template_id] = file_get_contents( ROOT_PATH.$this -> style['path'].'/templates/'.$template_file);
			$this -> templates[$template_id] = str_ireplace('{STYLE}', ROOT_PATH.$this -> style['path'], $this -> templates[$template_id]);
		}
		
	}
	
	/**
	 * loads frontend style files
	 *
	 * @param string $style
	 */
	
	function loadStyle( $style){
		
		global $mysql;
		global $cache;
		global $page;
		global $system_settings;
		
		/**
		 * this function loads selected style
		 */
		
		try{
			
			$style_found = false;
			
			/**
			 * gather style data from mysql
			 */
			
			$this -> style = $cache -> loadCache( 'style_'.$style.'_data');
			
			if( $this -> style == false){
			
				$this -> style = null;
				$style_query = $mysql -> query( 'SELECT * FROM styles WHERE `style_id` = '.$style);
				
				while( $result = mysql_fetch_array($style_query, MYSQL_ASSOC)) {
					
					$this -> style['name'] = $result['style_name'];
					$this -> style['path'] = $result['style_path'];
					$this -> style['author'] = $result['style_author'];
					$this -> style['www'] = $result['style_www'];
					$this -> style['creation'] = $result['style_creation'];
					$this -> style['users'] = $result['style_users'];
					
					$style_found = true;
					
				}
				
				$cache -> saveCache( 'style_'.$style.'_data', $this -> style);
				
			}else{
			
				$style_found = true;
				
			}
			
			if(!$style_found)
				throw new uniException( 'Specified style not found');
				
			/**
			 * set css
			 */
				
			$this -> style_css = ROOT_PATH.'styles/'.($this -> style['path']).'/style.css';
			
			/**
			 * now load template files
			 */
			
			$template_xml_file = file_get_contents( ROOT_PATH.'styles/'.$this -> style['path'].'/info.xml');
			$template_xml = new SimpleXMLElement( $template_xml_file);
					
			foreach( $template_xml -> templates[0] as $template_id => $template_file){
				
				$this -> templates[$template_id] = file_get_contents( ROOT_PATH.'styles/'.$this -> style['path'].'/templates/'.$template_file);
				$this -> templates[$template_id] = str_ireplace('{STYLE}', ROOT_PATH.'styles/'.$this -> style['path'], $this -> templates[$template_id]);
			}
					
		}catch(uniException $error){
			
			$error -> criticalError( 2);
			
		}
		
		
		
	}
	
	/**
	 * loads images from style
	 *
	 */
	
	function loadImages(){
		
		global $session;
		
		/**
		 * image template
		 */
		
		$this -> templates['image'] = file_get_contents( ROOT_PATH.'system/templates/image.htm');
		
		/**
		 * files list now
		 */
		
		if( defined( 'ACP' )){
			
			$imageset_xml_file = file_get_contents( ROOT_PATH.'styles_acp/'.$this -> style['path'].'/info.xml');
		
		}else if( defined( 'SIMPLE_MODE' )){
			
			$imageset_xml_file = file_get_contents( ROOT_PATH.$this -> style['path'].'/info.xml');
			
		}else{
			
			$imageset_xml_file = file_get_contents( ROOT_PATH.'styles/'.$this -> style['path'].'/info.xml');
			
		}
		
		$imageset_xml = new SimpleXMLElement( $imageset_xml_file);
		
		foreach ( $imageset_xml -> imageset[0] as $image_id => $image)
			$this -> images[$image_id] = str_ireplace( 'LANG', $session -> user['user_lang'],$image);
		
	}
	
	/**
	 * draws page
	 *
	 * @param string $content
	 * @param string $head
	 * @param array $blocks
	 * @param string $foot
	 */
	
	function drawPage( $content, $breadcrumbs){
		
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
		 * breadcrumbs
		 */
		
		if ( !empty($breadcrumbs)){
			
			$template = substr_replace( $template, $breadcrumbs, stripos($template, "<breadcrumbs>"), (stripos($template, '</breadcrumbs>') - stripos($template, '<breadcrumbs>') + strlen( '</breadcrumbs>')));
			
			if ( $settings['board_path_atend']){
				
				$template = str_replace( "{BREADCRUMBS}", $breadcrumbs, $template);
				$template = str_replace( "</style:repeat_breadcrumbs>", "", $template);
				$template = str_replace( "<style:repeat_breadcrumbs>", "", $template);
								
			}else{
				
				$template = preg_replace("#\<style:repeat_breadcrumbs\>(.*?)\</style:repeat_breadcrumbs\>#si",'', $template);				
			
			}
			
		}else{
			
			$template = substr_replace( $template, '', stripos($template, "<path>"), (stripos($template, '</path>') - stripos($template, '<path>') + strlen( '</path>')));
			$template = preg_replace("#\<style:repeat_breadcrumbs\>(.*?)\</style:repeat_breadcrumbs\>#si",'', $template);				
				
		}
		
		$template = str_replace( "</path>", "", $template);
		$template = str_replace( "<path>", "", $template);
			
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
	 * draws acp page
	 *
	 * @param string $content
	 * @param string $head
	 * @param array $blocks
	 * @param string $foot
	 */
	
	function drawAcpPage( $content, $blocks, $breadcrumbs){
		
		global $output;
		global $language;
		
		/**
		 * this function generates page html, and sends it to output;
		 */
		
		$template = $this -> templates['main'];

		$template = str_ireplace( '{CREDITS}', $this -> drawFoot(), $template);
		$template = str_ireplace( '{ACP_HELLO}', $language -> getString( 'acp_hello_text'), $template);
		
		/**
		 * breadcrumbs
		 */
		
		if ( !empty($breadcrumbs)){
			
			$template = substr_replace( $template, $breadcrumbs, stripos($template, "<breadcrumbs>"), (stripos($template, '</breadcrumbs>') - stripos($template, '<breadcrumbs>') + strlen( '</breadcrumbs>')));
			
		}else{
			
			$template = substr_replace( $template, '', stripos($template, "<path>"), (stripos($template, '</path>') - stripos($template, '<path>') + strlen( '</path>')));
				
		}
		
		/**
		 * parse side blocks
		 */
		
		if($blocks != null){
			
			/**
			 * there are blocks at the left so we will put them into template
			 */
			
			$blocks = $this -> removeBrackets($blocks);
			
			$template = str_ireplace( '{BLOCKS}', $blocks, $template);
			$template = str_ireplace( '<blocks>', '', $template);
			$template = str_ireplace( '</blocks>', '', $template);
			
		}else{
			
			/**
			 * there are no blocks at the left, so delete everything between tags
			 */
			
			$template = substr_replace( $template, '', stripos($template, '<blocks>'), (stripos($template, '</blocks>') - stripos($template, '<blocks>') + strlen( '</blocks>')));
		}
		
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
	
	function drawSimplePage( $content){
		
		global $output;
		
		$template = $this -> templates['simple_main'];
		
		$output -> addToOutput( str_ireplace( '{CONTENT}', $content, $template));
		
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
	 * draws post
	 *
	 * @param bool $type
	 * @param array $post_content
	 * @return string
	 */
	
	function drawPost( $type, $post_parts, $post_strings){

		global $settings;
		global $language;
		
		/**
		 * add basic strings
		 */
		
		$post_strings['GO_TO_TOP'] = $language -> getString( 'topics_go_to_top');
		
		/**
		 * check, if we have template
		 */
		
		if ( key_exists( 'post_body_'.$type, $this -> templates)){
			
			$template = $this -> templates['post_body_'.$type];
			
		}else{
			
			/**
			 * template not yet made
			 */
			
			if ( $settings['forum_post_look']){
				$template = $this -> templates['post_body'];
			}else{
				$template = $this -> templates['post_body_alternative'];
			}
			
			$template = str_replace( "<style:post_".$type.">", "", $template);
			$template = str_replace( "</style:post_".$type.">", "", $template);
				
			if ( $type == 0){
				$counter_type = 1;
			}else{
				$counter_type = 0;
			}
			
			$template = preg_replace("#\<style:post_".$counter_type."\>(.*?)\</style:post_".$counter_type."\>#si",'', $template);	
			
			/**
			 * save template
			 */
			
			$this -> templates['post_body_'.$type] = $template;
			
		}
		
		/**
		 * do parsing
		 */
		
		foreach ( $post_parts as $part_name => $part_draw){
			
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
			
		foreach ( $post_strings as $string_id => $string_text){
			
			$template = str_replace( "{".$string_id."}", $this ->removeBrackets($string_text), $template);
			
		}
		
		/**
		 * return html
		 */
		$template = $this -> fixBrackets( $template);
		
		return $template;	
		
	}
	
	/**
	 * draws login form
	 *
	 * @param string $title
	 * @param mixed $content
	 * @return string
	 */
	
	function drawLoginForm( $message = "", $error = "", $remember = false, $remember_check = false, $hidden_check = false){
		
		global $language;
		global $credits;
		global $settings;
		
		/**
		 * get template
		 */
		
		$template = $this -> templates['login_form'];
		
		$message = $this -> removeBrackets( $message);
		$user = $this -> removeBrackets( $user);
		
		/**
		 * parse languages
		 */
		
		$template = str_ireplace( '{CREDITS}', $credits, $template);
		
		$template = str_ireplace( '{SECTION_LOGIN}', $language -> getString( 'login_form'), $template);
		$template = str_ireplace( '{SECTION_OPS}', $language -> getString( 'login_form_ops'), $template);
		
		$template = str_ireplace( '{LOGIN}', $language -> getString( 'login_username'), $template);
		$template = str_ireplace( '{PASSWORD}', $language -> getString( 'login_pass'), $template);
		$template = str_ireplace( '{REMEMBER_ME}', $language -> getString( 'login_renember'), $template);
		$template = str_ireplace( '{HIDDEN}', $language -> getString( 'login_hidden'), $template);
		
		/**
		 * checks
		 */
		
		if ( $remember_check){
			
			$template = str_ireplace( '{REMEMBER_ME_CHECKED}', 'checked="checked"', $template);
		
		}else{
			
			$template = str_ireplace( '{REMEMBER_ME_CHECKED}', '', $template);
			
		}
		
		if ( $hidden_check){
			
			$template = str_ireplace( '{HIDDEN_CHECKED}', 'checked="checked"', $template);
		
		}else{
			
			$template = str_ireplace( '{HIDDEN_CHECKED}', '', $template);
			
		}
		
		/**
		 * now parse rest
		 */
		
		$template = str_ireplace( '{LOGING_TEXT}', $message, $template);
		
		$template = str_ireplace( '{RETURN_LINK}', '<a href="'.ROOT_PATH.'">'.$language -> getString( 'login_return_to_site').'</a>', $template);
		
		/**
		 * message
		 */
		
		if($message != ""){
			
			/**
			 * there are blocks at the right, so we will put them into template
			 */
			
			$message = $this -> removeBrackets($message);
			
			$template = str_ireplace( '{MESSAGE}', $message, $template);
			$template = str_ireplace( '<message>', '', $template);
			$template = str_ireplace( '</message>', '', $template);
			
		}else{
			
			/**
			 * there are no blocks at the right, so delete everything between tags
			 */
			
			$template = substr_replace( $template, '', stripos($template, '<message>'), (stripos($template, '</message>') - stripos($template, '<message>') + strlen( '</message>')));
		}
		
		/**
		 * error message
		 */
		
		if($error != ""){
			
			/**
			 * there are blocks at the right, so we will put them into template
			 */
			
			$error = $this -> removeBrackets($error);
			
			$template = str_ireplace( '{ERROR_MESSAGE}', $error, $template);
			$template = str_ireplace( '<error>', '', $template);
			$template = str_ireplace( '</error>', '', $template);
			
		}else{
			
			/**
			 * there are no blocks at the right, so delete everything between tags
			 */
			
			$template = substr_replace( $template, '', stripos($template, '<error>'), (stripos($template, '</error>') - stripos($template, '<error>') + strlen( '</error>')));
		}
				
		/**
		 * error message
		 */
		
		if( $remember){
			
			/**
			 * there are blocks at the right, so we will put them into template
			 */
			
			$template = str_ireplace( '<remember>', '', $template);
			$template = str_ireplace( '</remember>', '', $template);
			
		}else{
			
			/**
			 * there are no blocks at the right, so delete everything between tags
			 */
			
			if ( stristr( $template, '<remember>') == false)
			$template = substr_replace( $template, '', stripos($template, '<remember>'), (stripos($template, '</remember>') - stripos($template, '<remember>') + strlen( '</remember>')));
		}
		
		$template = $this -> fixBrackets( $template);
		
		return $template;
		
	}
		
	/**
	 * draws page foot
	 *
	 * @return string
	 */
	
	function drawFoot(){
		
		global $settings;
		global $language;
		global $mysql;
		global $gen_start_time;
		global $time;
		
		$foot = '';
		
		if( $settings['board_debug_level'] > 0){
			
			/**
			 * debug level is bigger than 0, so we have to put it in footer
			 * 
			 * debug levels:
			 * 0 - disabled
			 * 1 - olny generation time
			 * 2 - generation time end sql queries number
			 * 3 - generation time, sql queries, and table containing queries data (not in footer)
			 */
						
			$foot .= '[ ';
			
			$gen_time = explode( " ", microtime());

			$gen_end_time = $gen_time[0] + $gen_time[1];
			
			$secound = $gen_end_time - $gen_start_time;
			
			if ( $secound < 0)
				$secound = 0;
			
			$foot .= round( abs( ($ac_gen_time_ms - $gen_time_ms)) + $secound, 5).' s';
						
			if( $settings['board_debug_level'] >= 2)
				$foot .= ' | '.($mysql -> queries_num).' SQL`s';
			
			$foot .= ' ]';
			
			if ( !defined( 'ACP')){
				
				$foot .= '<br />';
			
			}else{
				
				$foot .= ' ';
				
			}
			
		}
		
		/**
		 * draw it
		 */
		
		if ( !defined( 'ACP') && strlen( $settings['board_copyright']) > 0)
			$foot .= $settings['board_copyright'].'<br />';
		
		if( $settings['board_draw_version'] || defined( 'ACP')){
			
			/**
			 * lets draw foot with script version number
			 */
			
			global $credits_v;
			
			$foot .= $credits_v.$simple_link;
		
		}else{
			
			/**
			 * lets draw foot without version number
			 */
			
			global $credits;
			
			$foot .= $credits.$simple_link;
		
		}
		
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
	 * this function draws select page form
	 *
	 * @param url $base_url
	 * @param variable name $page_value
	 * @param total num of pages $pages_num
	 * @param actual page number $page_actual
	 * @return string
	 */
	
	function drawPaginator( $base_url, $page_value, $pages_num, $page_actual){

		if( $pages_num > 1){
		
			global $language;
			
			$template = $this -> templates['paginator'];
			
			/**
			 * build up templates
			 */
			
			$element = substr( $template, stripos( $template, '<element>') + 9, (stripos( $template, '</element>') - stripos( $template, '<element>') - strlen( '</element>') + 1));
			$selected_element = substr( $template, stripos( $template, '<element_selected>') + 18, (stripos( $template, '</element_selected>') - stripos( $template, '<element_selected>') - strlen( '</element_selected>') + 1));
			$template = substr( $template, stripos( $template, '<container>') + 11, (stripos( $template, '</container>') - stripos( $template, '<container>') - strlen( '</container>') + 1));
			
			/**
			 * replace info
			 */
			
			$language -> setKey( 'pa', $page_actual);
			$language -> setKey( 'pb', $pages_num);
			
			$template = str_ireplace( '{INFO}', $language -> getString( 'page_from_to'), $template);
			
			/**
			 * and content
			 */
	
			$tabs = str_ireplace( '{TITLE}', $language -> getString( 'page_first'), str_ireplace( '{URL}', $base_url.'&'.$page_value.'=1', $element));
			
			for( $i=3; $i > 0; $i--){
				
				if(( $page_actual - $i) > 0)
					$tabs .= str_ireplace( '{TITLE}', $page_actual - $i, str_ireplace( '{URL}', $base_url.'&'.$page_value.'='.($page_actual - $i), $element));
						
			}
			
			$tabs .= str_ireplace( '{TITLE}', $page_actual, $selected_element);
					
			for( $i=1; $i <= 3; $i++){
				
				if(( $page_actual + $i) <= $pages_num)
					$tabs .= str_ireplace( '{TITLE}', $page_actual + $i, str_ireplace( '{URL}', $base_url.'&'.$page_value.'='.($page_actual + $i), $element));
						
			}
			
			$tabs .= str_ireplace( '{TITLE}', $language -> getString( 'page_last'), str_ireplace( '{URL}', $base_url.'&'.$page_value.'='.$pages_num, $element));
			
				
			$template = str_ireplace( '{CONTENT}', $tabs, $template);
			
			/**
			 * return html
			 */
			
			return $template;
			
		}
	}
	
	/**
	 * this function draws standard image
	 *
	 * @param string $image
	 * @param string $alt
	 */
	
	function drawImage( $image, $alt = null, $id = null){
		
		global $settings;
		
		$template = $this -> templates['image'];
		
		if( !defined( 'ACP') && !defined( 'SIMPLE_MODE')){
			$styles_container = 'styles/';
		}else if( defined( 'SIMPLE_MODE')){
			$styles_container = '';
		}else{
			$styles_container = 'styles_acp/';
		}
		
		$template = str_ireplace( '{IMAGE}', ROOT_PATH.$styles_container.$this -> style['path'].'/'.$this -> images[$image], $template);
		$template = str_ireplace( '{ALT}', $alt, $template);
		$template = str_ireplace( '{ID}', $id, $template);
		
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
	
	/**
	 * draws online or off-line icon
	 *
	 * @param bool $online
	 * @return string
	 */
	
	function drawStatus( $online = true){
		
		global $language;
		
		if( $online){
			
			return $this -> drawImage( 'on-line', $language -> getString( 'on-line'));
			
		}else{
			
			return $this -> drawImage( 'off-line', $language -> getString( 'off-line'));
			
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