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
|	Clear cache
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');


$cache_files = glob( ROOT_PATH.'cache/*.php');

if (count($cache_files) > 0){

	foreach ( $cache_files as $cache_file){
		
		$cache_file_name = substr( $cache_file, strrpos( $cache_file, '/')+1);
		$cache_file_name = substr( $cache_file_name, 0, strrpos( $cache_file_name, "."));
		
		$cache -> flushCache($cache_file_name);
	
	}
}
	
?>