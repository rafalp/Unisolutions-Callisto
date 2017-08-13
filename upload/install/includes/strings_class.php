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
|	Installer Strings Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class strings{
	
	function inputClear( $text, $code_html = true){
		
		global $utf8;
		
		$text = trim($text);
		$text = addslashes($text);
		
		if( $add_br)
			$text = nl2br( $text);
			
		if( $code_html){
			$text = htmlspecialchars_decode($text);
		}else{
			$text = htmlspecialchars($text);
		}
		
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
	
}

?>