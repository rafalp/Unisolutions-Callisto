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
|	UTF8 Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
/**
 * converts special chars to html codes
 *
 */
	
class utf8{
	
	var $chars = array(
		'ó' => "oacute",
		'Ó' => "Oacute",
		'ę' => "#281",
		'Ę' => "#280",
		'ą' => "#261",
		'Ą' => "#260",
		'ś' => "#347",
		'Ś' => "#346",
		'ł' => "#322",
		'Ł' => "#321",
		'ż' => "#380",
		'Ż' => "#379",
		'ź' => "#378",
		'Ź' => "#377",
		'ć' => "#263",
		'Ć' => "#262",
		'ń' => "#324",
		'Ń' => "#323");
	
	function turnChars( $string){	
		
		foreach ( $this -> chars as $char => $replacement)
			$string = str_replace( $char, "&".$replacement.";", $string);
		
		return $string;
		
	}
	
	function turnOffChars( $string){	
		
		foreach ( $this -> chars as $char => $replacement)
			$string = str_replace( "&".$replacement.";", $char, $string);
		
		return $string;
		
	}
	
	function charsClear( $string){
		
		foreach ( $this -> chars as $char => $replacement)
			$string = str_replace( "&amp;".$replacement.";", "&".$replacement.";", $string);
		
		return $string;
		
	}
	
}

?>