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
|	This software is released under GNU General Public Licence v3
|	http://www.gnu.org/licenses/gpl.txt
|
#===========================================================================
|
|	Frontend file
|	by Rafał Pitoń
|
#===========================================================================
*/

//============================
//Define that we are in script
//============================

define( 'IN_UNI', 1);

define( 'ROOT_PATH', './');

//============================
//Include file containing globals and classes definitions
//============================

require_once( ROOT_PATH.'system/init.php');

//============================
//Initialise core
//============================

require_once( ROOT_PATH.'system/kernel.php');

$uni = new unisolutions();

?>