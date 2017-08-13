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
|	Strings Class
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class strings{

	/**
	 * actual tags
	 *
	 * @var int
	 */
	
	public $bbtags = array();
	
	/**
	 * custom tags
	 *
	 * @var int
	 */
	
	public $custom_bbtags = array();
	
	/**
	 * more complicated tags templates
	 *
	 * @var array
	 */
	
	public $tags_templates = array();
	
	/**
	 * emotes
	 *
	 * @var array
	 */
	
	public $emoticons = array();
	
	/**
	 * badwords
	 *
	 */
	
	public $badwords = array();
	
	function __construct(){
		
		global $system_settings;
		global $cache;
		global $mysql;
		
		/**
		 * load templates
		 */
		
		$this -> tags_templates['quote'] = file_get_contents( ROOT_PATH.'system/templates/bb_tags/quote.html');
		$this -> tags_templates['code'] = file_get_contents( ROOT_PATH.'system/templates/bb_tags/code.html');
		$this -> tags_templates['spoiler'] = file_get_contents( ROOT_PATH.'system/templates/bb_tags/spoiler.html');
		
		$emotes_load = $cache -> loadCache( 'emoticons');
		
		if( gettype( $emotes_load) == 'array'){
			
			/**
			 * we successfully loaded emoticons cache from db
			 */
			
			$this -> emoticons = $emotes_load;
			
		}else{
			
			/**
			 * loading failed, lets build up new cache
			 */
			
			$emo_query = $mysql -> query("SELECT * FROM emoticons ORDER BY `emoticon_type` DESC");
			
			while( $result = mysql_fetch_array( $emo_query, MYSQL_ASSOC)){
				
				$result = $mysql -> clear( $result);
			
				$this -> emoticons[$result['emoticon_type']] = array( 'image' => $result['emoticon_image'], 'name' => $result['emoticon_name']);
				
			}	
			
			$cache -> saveCache( 'emoticons', $this -> emoticons);
			
		}
		
		/**
		 * load custom bbtags
		 */
		
		$bbcodes_load = $cache -> loadCache( 'bbcodes');
		
		if( gettype( $bbcodes_load) == 'array'){
			
			/**
			 * we successfully loaded emoticons cache from db
			 */
			
			$this -> custom_bbtags = $bbcodes_load;
			
		}else{
			
			/**
			 * loading failed, lets build up new cache
			 */
			
			$codes_query = $mysql -> query("SELECT * FROM bbtags ORDER BY `tag_name`");
			
			while( $code_result = mysql_fetch_array( $codes_query, MYSQL_ASSOC)){
			
				$code_result = $mysql -> clear( $code_result);
				
				$this -> custom_bbtags[] = array(
					'tag_name' => $code_result['tag_name'],
					'tag_info' => $code_result['tag_info'],
					'tag_tag' => $code_result['tag_tag'],
					'tag_option' => $code_result['tag_option'],
					'tag_replace' => $code_result['tag_replace'],
					'tag_draw' => $code_result['tag_draw']
				);
				
			}	
			
			$cache -> saveCache( 'bbcodes', $this -> custom_bbtags);
				
		}
		
		/**
		 * load badwords
		 */
		
		$badwords_load = $cache -> loadCache( 'badwords');
		
		if( gettype( $badwords_load) == 'array'){
			
			/**
			 * we successfully loaded emoticons cache from db
			 */
			
			$this -> badwords = $badwords_load;
			
		}else{
			
			/**
			 * loading failed, lets build up new cache
			 */
			
			$bw_query = $mysql -> query("SELECT * FROM badwords ORDER BY `badword_find` DESC");
			
			while( $bw_result = mysql_fetch_array( $bw_query, MYSQL_ASSOC)){
			
				$bw_result = $mysql -> clear( $bw_result);
				
				$this -> badwords[$bw_result['badword_find']] = $bw_result['badword_replace'];
				
			}	
			
			$cache -> saveCache( 'badwords', $this -> badwords);
				
		}
	}
	
	function parseBB( $text, $tags = true, $emotes = true){

		global $style;
		global $settings;
		global $utf8;
		
		$text = str_replace( "&lt;", '[<]', $text);
		$text = str_replace( "&gt;", '[>]', $text);
		
		$text = $utf8 -> turnOffChars( $text);
		
		if( $emotes){
			
			foreach( $this -> emoticons as $code => $ops){
				
				$code = str_replace( "&lt;", '[<]', $code);
				$code = str_replace( "&gt;", '[>]', $code);
		
				$text = str_ireplace( $code, '<img src="'.ROOT_PATH.'images/emoticons/'.$ops['image'].'" alt="'.$ops['name'].'" title="'.$ops['name'].'">', $text);
				
			}
			
		}

		$text = $utf8 -> turnChars( $text);
		
		$text = str_replace( "[<]", '&lt;', $text);
		$text = str_replace( "[>]", '&gt;', $text);
			
		if( $tags){
						
			/**
			 * code tags preparse
			 */
					
			preg_match_all( "#\[code\](.*?)\[/code\]#si", $text, $code_tags_content);
			
			foreach ( $code_tags_content[1] as $code_tag_content){
								
				$code_tag_content_rpl = str_ireplace( "[", "{[}", $code_tag_content);
				$code_tag_content_rpl = str_ireplace( "]", "{]}", $code_tag_content_rpl);
						
				if( $emotes){
					
					foreach( $this -> emoticons as $code => $ops){
						
						$code = str_replace( "&lt;", '[<]', $code);
						$code = str_replace( "&gt;", '[>]', $code);
						
						$code_tag_content_rpl = str_replace( '<img src="'.ROOT_PATH.'images/emoticons/'.$ops['image'].'" alt="'.$ops['name'].'" title="'.$ops['name'].'">', $code, $code_tag_content_rpl);
						
					}
					
				}
				
				$text = str_replace( '[code]'.$code_tag_content.'[/code]', '[code]'.$code_tag_content_rpl.'[/code]', $text);
				
			}
			
			/**
			 * urls tags preparse
			 */
			
			preg_match_all( "#\[url\](.*?)\[/url\]#si", $text, $url_tags_content);
			
			foreach ( $url_tags_content[1] as $url_tag_content){
				
				$url_tag_content_rpl = $url_tag_content;
				
				if( $emotes){
					
					foreach( $this -> emoticons as $code => $ops){
						
						$code = str_replace( "&lt;", '[<]', $code);
						$code = str_replace( "&gt;", '[>]', $code);
						
						$url_tag_content_rpl = str_replace( '<img src="'.ROOT_PATH.'images/emoticons/'.$ops['image'].'" alt="'.$ops['name'].'" title="'.$ops['name'].'">', $code, $url_tag_content_rpl);
						
					}
						
				}
				
				$url_tag_content_rpl = str_replace( "<br />", "", $url_tag_content_rpl);
				
				$url_tag_content_rpl = $utf8 -> turnOffChars($url_tag_content_rpl);
				
				$text = str_replace( '[url]'.$url_tag_content.'[/url]', '[url]'.$url_tag_content_rpl.'[/url]', $text);
				
			}
			
			preg_match_all( "#\[url=(.*?)\[/url\]#si", $text, $url_tags_content);
			
			foreach ( $url_tags_content[1] as $url_tag_content){
				
				$url_tag_link_rpl = substr( $url_tag_content, 0, strpos( $url_tag_content, ']'));
				$url_tag_link_org = $url_tag_link_rpl;
				
				$url_tag_rest = substr( $url_tag_content, strpos( $url_tag_content, ']') + 1);
				
				if( $emotes){
					
					foreach( $this -> emoticons as $code => $ops){
						
						$code = str_replace( "&lt;", '[<]', $code);
						$code = str_replace( "&gt;", '[>]', $code);
						
						$url_tag_link_rpl = str_replace( '<img src="'.ROOT_PATH.'images/emoticons/'.$ops['image'].'" alt="'.$ops['name'].'" title="'.$ops['name'].'">', $code, $url_tag_link_rpl);
						
					}
						
				}
				
				$url_tag_link_rpl = str_replace( "<br />", "", $url_tag_link_rpl);
										
				$url_tag_link_rpl = $utf8 -> turnOffChars($url_tag_link_rpl);
				
				$text = str_replace( '[url='.$url_tag_link_org.']'.$url_tag_rest.'[/url]', '[url='.$url_tag_link_rpl.']'.$url_tag_rest.'[/url]', $text);
				
			}
			
			/**
			 * begin form simple tags
			 */
			
			$tags_list['b'] = 'b';
			$tags_list['i'] = 'i';
			$tags_list['u'] = 'u';
			$tags_list['s'] = 's';
			$tags_list['sup'] = 'sup';
			$tags_list['sub'] = 'sub';
			
			foreach ( $tags_list as $bb_tag => $bb_replacement){
						
				/**
				 * firstly, we will check, if there is open tag, and close tag
				 */
				
				$text = preg_replace("#\[$bb_tag\](.*?)\[/$bb_tag\]#si",'<'.$bb_tag.'>\\1</'.$bb_tag.'>', $text);
						
			}
			
			/**
			 * tags with divs
			 */
			
			$tags_divs_list['left'] = 'text-align:left';
			$tags_divs_list['center'] = 'text-align:center';
			$tags_divs_list['right'] = 'text-align:right';
			
			foreach ( $tags_divs_list as $bb_tag => $bb_replacement){
						
				/**
				 * firstly, we will check, if there is open tag, and close tag
				 */
				
				$text = preg_replace("#\[$bb_tag\](.*?)\[/$bb_tag\]#si",'<div style="'.$bb_replacement.'">\\1</div>', $text);
						
			}
			
			/**
			 * tags with spans
			 */
			
			$tags_spans_list = array();
			
			foreach ( $tags_spans_list as $bb_tag => $bb_replacement){
						
				/**
				 * firstly, we will check, if there is open tag, and close tag
				 */
				
				$text = preg_replace("#\[$bb_tag\](.*?)\[/$bb_tag\]#si",'<span style="'.$bb_replacement.'">\\1</span>', $text);
						
			}
			
			/**
			 * custom bbcodes
			 */
			
			foreach ( $this -> custom_bbtags as $custom_bbtag){
				
				$bb_replacement = htmlspecialchars_decode( $custom_bbtag['tag_replace']);
								
				if ( $custom_bbtag['tag_option']){
					
					$bb_replacement = str_ireplace( '{OPTION}', '\\1', $bb_replacement);
					$bb_replacement = str_ireplace( '{CONTENT}', '\\2', $bb_replacement);
					
					$text = preg_replace("#\[".$custom_bbtag['tag_tag']."=&quot;(.*?)&quot;\](.*?)\[/".$custom_bbtag['tag_tag']."\]#si", $bb_replacement, $text);
					
				}else{
				
					$bb_replacement = str_ireplace( '{CONTENT}', '\\1', $bb_replacement);
					
					$text = preg_replace("#\[".$custom_bbtag['tag_tag']."\](.*?)\[/".$custom_bbtag['tag_tag']."\]#si", $bb_replacement, $text);
									
				}
				
			}
			
			/**
			 * hr
			 */
			
			$text = str_ireplace( '[hr]', '<hr />', $text);
			
			/**
			 * clear url tags
			 */
			
			if ( $emotes){
				
				$text = preg_replace_callback( '#\[url\](.*?)\[/url\]#si', array($this, 'clearPath'), $text);
				$text = preg_replace_callback( '#\[ftp\](.*?)\[/ftp\]#si', array($this, 'clearPath'), $text);
				$text = preg_replace_callback( '#\[mail\](.*?)\[/mail\]#si', array($this, 'clearPath'), $text);
				$text = preg_replace_callback( '#\[img\](.*?)\[/img\]#si', array($this, 'clearPath'), $text);
						
				$text = preg_replace_callback( '#\[url=(.*?)\](.*?)\[/url\]#si', array($this, 'clearPathExt'), $text);
				$text = preg_replace_callback( '#\[ftp=(.*?)\](.*?)\[/ftp\]#si', array($this, 'clearPathExt'), $text);
				$text = preg_replace_callback( '#\[mail=(.*?)\](.*?)\[/mail\]#si', array($this, 'clearPathExt'), $text);
				$text = preg_replace_callback( '#\[img=(.*?)\](.*?)\[/img\]#si', array($this, 'clearPathExt'), $text);
				
			}
			
			/**
			 * url tags
			 */
			
			$text = preg_replace("#\[url\]http://(.*?)\[/url\]#si", '<a href="http://\\1">\\1</a>', $text);
			$text = preg_replace("#\[url=http://(.*?)\](.*?)\[/url\]#si", '<a href="http://\\1">\\2</a>', $text);			
			
			$text = preg_replace("#\[url\](.*?)\[/url\]#si", '<a href="http://\\1">\\1</a>', $text);
			$text = preg_replace("#\[url=(.*?)\](.*?)\[/url\]#si", '<a href="http://\\1">\\2</a>', $text);
			
			$text = preg_replace("#\[ftp\]ftp://(.*?)\[/url\]#si", '<a ftp="ftp://\\1">\\1</a>', $text);
			$text = preg_replace("#\[ftp=ftp://(.*?)\](.*?)\[/url\]#si", '<a ftp="ftp://\\1">\\2</a>', $text);
			
			$text = preg_replace("#\[ftp\](.*?)\[/ftp\]#si", '<a href="ftp://\\1">\\1</a>', $text);
			$text = preg_replace("#\[ftp=(.*?)\](.*?)\[/ftp\]#si", '<a href="ftp://\\1">\\2</a>', $text);
			
			$text = preg_replace("#\[mail\](.*?)\[/mail\]#si", '<a href="mailto:\\1">\\1</a>', $text);
			$text = preg_replace("#\[mail=(.*?)\](.*?)\[/mail\]#si", '<a href="mailto:\\1">\\2</a>', $text);
			
			/**
			 * img tags
			 */
			
			$text = preg_replace_callback( "#\[((img)|(img=(.*?)))\](.*?)\[/img\]#si", array($this, 'imgParse'), $text);
			
			/**
			 * size
			 */
			
			$text = preg_replace("#\[size=(.*?)\](.*?)\[/size\]#si", '<span style="font-size: \\1">\\2</span>', $text);
			
			/**
			 * color
			 */
			
			$text = preg_replace("#\[color=(.*?)\](.*?)\[/color\]#si", '<span style="color: \\1">\\2</span>', $text);
						
			/**
			 * now quote tag
			 */
								
			$text = preg_replace("#\[quote\](.*?)\[/quote\]#si", $this -> quoteTemplate( 0), $text);
				
			$text = preg_replace('#\[quote=&quot;(.*?)&quot;\](.*?)\[/quote\]#si', $this -> quoteTemplate( 1), $text);
			
			/**
			 * spoiler tag
			 */
				
			$text = preg_replace("#\[spoiler\](.*?)\[/spoiler\]#si", $this -> spoilerTemplate( 0), $text);
				
			$text = preg_replace('#\[spoiler=&quot;(.*?)&quot;\](.*?)\[/spoiler\]#si', $this -> spoilerTemplate( 1), $text);
						
			/**
			 * code tags
			 */
					
			preg_match_all( "#\[code\](.*?)\[/code\]#si", $text, $code_tags_content);
						
			foreach ( $code_tags_content[1] as $code_tag_content){
								
				$code_tag_content_rpl = str_ireplace( "{[}", "[", $code_tag_content);
				$code_tag_content_rpl = str_ireplace( "{]}", "]", $code_tag_content_rpl);
				
				$text = str_replace( $code_tag_content, $code_tag_content_rpl, $text);
				
			}
				
			$text = preg_replace("#\[code\](.*?)\[/code\]#si", $this -> codeTemplate(), $text);
			
		}
		
		/**
		 * return output
		 */
		
		return $text;
		
	}
	
	function clearPath( $mathes){
		
		$orginal_path = $mathes[1];
		$fixed_path = $mathes[1];
		
		foreach( $this -> emoticons as $code => $ops){
			
			$code = str_replace( "&lt;", '[<]', $code);
			$code = str_replace( "&gt;", '[>]', $code);
			
			$fixed_path = str_ireplace( '<img src="'.ROOT_PATH.'images/emoticons/'.$ops['image'].'" alt="'.$ops['name'].'" title="'.$ops['name'].'">', $code, $fixed_path);
			
		}
		
		return str_replace( $orginal_path, $fixed_path, $mathes[0]);
		
	}
	
	function clearPathExt( $mathes){
		
		//get tag type
		$tag = substr( $mathes[0], strrpos( $mathes[0], '[/') + 2);
		$tag = substr( $tag, 0, strrpos( $tag, ']'));

		$fixed_path = $mathes[1];
		
		foreach( $this -> emoticons as $code => $ops){
			
			$code = str_replace( "&lt;", '[<]', $code);
			$code = str_replace( "&gt;", '[>]', $code);
			
			$fixed_path = str_replace( '<img src="'.ROOT_PATH.'images/emoticons/'.$ops['image'].'" alt="'.$ops['name'].'" title="'.$ops['name'].'">', $code, $fixed_path);
			
		}
		
		return '['.$tag.'='.$fixed_path.']'.$mathes[2].'[/'.$tag.']';
		
	}
	
	function imgParse( $matches){
				
		//grab raw url
		$raw_url = str_replace( '&amp;', '&', ( strlen( $matches[4]) > 0 ? $matches[4] : $matches[5]));
		$raw_url_prs = parse_url( $raw_url);		
		
		//explode it to items
		$raw_url_prs = explode( '&', $raw_url_prs['query']);
		$url_prs = array();
		
		//parse it
		foreach( $raw_url_prs as $url_item){
			
			//item key
			$item_key = trim( substr( $url_item, 0, strpos( $url_item, '=')));
			
			//got it?
			if ( strlen( $item_key) > 0)
				$url_prs[$item_key] = trim( substr( $url_item, 1 + strpos( $url_item, '=')));
				
		}
		
		//url to anything else than attachment?
		if ( stripos( $raw_url, 'index.php?') === false || ( stripos( $raw_url, 'index.php?') !== false && ( !key_exists( 'act', $url_prs) || ( key_exists( 'act', $url_prs) && $url_prs['act'] == 'download' )))){
			
			//img with param?
			if ( strlen( $matches[4]) > 0){
				
				//return image
				return '<img src="'.addslashes( $matches[4]).'" alt="'.addslashes( $matches[5]).'" title="'.addslashes( $matches[5]).'"/>';
				
			}else{
				
				return '<img src="'.addslashes( $matches[5]).'" alt="" />';
				
			}
			
		}else{
		
			//return result
			return $matches[0];
			
		}
		
	}
	
	function censore( $text){
		
		foreach ( $this -> badwords as $badword => $censure)
			$text = str_ireplace( $badword, $censure, $text);
			
		return $text;
	}
	
	function quoteTemplate( $type = 1){
		
		global $language;
		
		$template = $this -> tags_templates['quote']; 
		
		if($type == 1){
			
			/**
			 * there are blocks in head, so we will put them into template
			 */
			
			$template = str_ireplace( '{TITLE}', $language -> getString('bb_quote').' (\\1)', $template);
			$template = str_ireplace( '{TEXT}', '\\2', $template);
					
		}else{
			
			/**
			 * there are no blocks in head, so delete everything between tags
			 */
			
			$template = str_ireplace( '{TITLE}', $language -> getString('bb_quote'), $template);
			$template = str_ireplace( '{TEXT}', '\\1', $template);
		}
		
		return $template;
		
	}
	
	function codeTemplate(){
		
		global $language;
		
		$template = $this -> tags_templates['code'];
			
		$template = str_ireplace( '{TITLE}', $language -> getString('bb_code'), $template);
		$template = str_ireplace( '{TEXT}', '\\1', $template);
			
		
		return $template;
		
	}
	
	function spoilerTemplate( $type = 1){
		
		global $language;
		
		$template = $this -> tags_templates['spoiler']; 
		
		if($type == 1){
			
			/**
			 * there are blocks in head, so we will put them into template
			 */
			
			$template = str_ireplace( '{TITLE}', $language -> getString('bb_spoiler').' (\\1)', $template);
			$template = str_ireplace( '{SHOW}', $language -> getString('bb_spoiler_show'), $template);
			$template = str_ireplace( '{HIDE}', $language -> getString('bb_spoiler_hide'), $template);
			
			$template = str_ireplace( '{TEXT}', '\\2', $template);
					
		}else{
			
			/**
			 * there are no blocks in head, so delete everything between tags
			 */
			
			$template = str_ireplace( '{TITLE}', $language -> getString('bb_spoiler'), $template);
			$template = str_ireplace( '{SHOW}', $language -> getString('bb_spoiler_show'), $template);
			$template = str_ireplace( '{HIDE}', $language -> getString('bb_spoiler_hide'), $template);
			$template = str_ireplace( '{TEXT}', '\\1', $template);
		}
		
		return $template;
		
	}
		
	function fileSize( $bytes = 0){
		
		/**
		 * turn file size
		 */
		
		if( $bytes >= 1048576){
			
			/**
			 * draw in megs
			 */
			
			$bytes = round( $bytes / 1048576, 2);
			
			return $bytes.' Mb';
			
		}else if ( $bytes >= 1024){
			
			/**
			 * draw in kilobytes
			 */
			
			$bytes = round( $bytes / 1024, 2);
			
			return $bytes.' Kb';
			
		}else{
			
			/**
			 * draw in bytes
			 */
			
			return $bytes.' b';
			
		}
		
	}
	
	function standardClear( $text){
			
		$text = trim($text);
		$text = htmlspecialchars($text);
		$text = addslashes($text);
		$text = nl2br($text);
			
		return $text;
			
	}
		
	function standardUnclear( $text){
			
		$text = str_ireplace( '<br />', '', $text);
		$text = stripslashes($text);
		$text = htmlspecialchars_decode($text);
						
		return $text;
			
	}
	
	function inputClear( $text, $code_html = true){
		
		global $utf8;
		
		$text = trim($text);
					
		if( $code_html){
			$text = htmlspecialchars_decode($text);
					
		}else{
			$text = htmlspecialchars($text);
		}
			
		if ( !get_magic_quotes_gpc())
			$text = addslashes($text);
				
		$text = $utf8 -> charsClear( $text);
		
		return $text;
		
	}
	
	function outputClear( $text, $code_html = true , $clear_br = false){
		
		global $utf8;
		
		$text = trim($text);
		$text = stripslashes($text);	
		
		if( $code_html){
			$text = htmlspecialchars($text);
			
			if( $clear_br)
				$text = str_ireplace( "&lt;br /&gt;", "", $text);
			
		}else{
			$text = htmlspecialchars_decode($text);
			
			if( $clear_br)
				$text = str_ireplace( "<br />", "", $text);
			
		}
		
		$text = $utf8 -> charsClear( $text);
			
		return $text;		
	}
	
	function mysqlClearOutput( $text){
		
		$text = stripslashes( $text);
		
		return $text;
		
	}
	
}

?>