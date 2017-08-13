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
|	Some useful functions
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');


/**
 * set cookie
 */

function setUniCookie( $name, $value, $expire){
	
	global $settings;
	
	$expire_time = time() + ($expire ? 31536000 : 86400);
	
	settype( $expire_time, 'integer');
	
	setcookie( $settings['cookie_name'].$name, $value, $expire_time, $settings['cookie_path'], $settings['cookie_domain'], $settings['cookie_secure'], false);
	
}

function killUniCookie( $name){
	
	global $settings;
	
	$expire_time = time() - 31536000;
	
	settype( $expire_time, 'integer');
	
	setcookie( $settings['cookie_name'].$name, $value, $expire_time, $settings['cookie_path'], $settings['cookie_domain'], $settings['cookie_secure'], false);
	
}

function getUniCookie( $name){
	
	global $settings;
	
	$cookie_to_return = $_COOKIE[$settings['cookie_name'].$name];
	
	return $cookie_to_return;
	
}

function uniSlashes( $text){
	
	if ( !get_magic_quotes_gpc())
		$text = addslashes( $text);
		
	return $text;
}

?>