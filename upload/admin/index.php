<?

/*
#===========================================================================
|
|	Unisolutions Pegasus
|
|	by Rafał Pitoń
|	Copyright 2007 by Unisolutions
|	http://www.unisolutions.pl
|
#===========================================================================
|
|	This software is released under Creative Commons 2.5 BY-ND PL licence
|	http://creativecommons.org/licenses/by-nd/2.5/pl/
|
#===========================================================================
|
|	Admin control panel main file
|	by Rafał Pitoń
|
#===========================================================================
*/

//============================
//Define that we are in script
//============================

define( 'IN_UNI', 1);
define( 'ACP', 1);

define( 'ROOT_PATH', '../');
//============================
//Include fiile containing globals and classes definitions
//============================

require_once( ROOT_PATH.'system/init.php');

//============================
//Initialise core
//============================

require_once( ROOT_PATH.'system/kernel.php');

$uni = new unisolutions();

?>