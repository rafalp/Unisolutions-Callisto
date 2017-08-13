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
|	delete dead logs
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
			
if( $settings['logs_clear_each'] > 0 && $settings['logs_clear_turn'] && defined( 'ACP')){
	
	$timeout = time() - ( $settings['logs_clear_each'] * 24 * 60 * 60);
	
	$mysql -> query("DELETE FROM admins_loging_log WHERE `admins_login_log_time` <= '$timeout'");
	$mysql -> query("DELETE FROM admins_logs WHERE `admins_log_time` <= '$timeout'");
	$mysql -> query("DELETE FROM mails_logs WHERE `mails_log_time` <= '$timeout'");
	$mysql -> query("DELETE FROM moderators_logs WHERE `moderators_log_time` <= '$timeout'");
	$mysql -> query("DELETE FROM spiders_logs WHERE `spider_log_time` <= '$timeout'");
	$mysql -> query("DELETE FROM tasks_logs WHERE `tasks_log_time` <= '$timeout'");
	
}
			
?>