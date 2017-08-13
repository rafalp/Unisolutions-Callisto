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
|	Script's core initializator
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

//===========================
//PHP VERSION CHECK
//===========================

if( version_compare(PHP_VERSION, '5.1', '<'))
	exit('<h1>CRITICAL ERROR</h1>Your\'s host PHP version is too old to enable Unisolutions Callisto working. Required version is 5.1.');

//============================
//DEFINE GLOBALS
//============================

// generation time
$gen_time = explode( " ", microtime());
$gen_start_time = $gen_time[0] + $gen_time[1];

//software name
define( 'UNI_PRODUCT', 'Callisto');

//version numbers
define( 'UNI_VER', '1.1.6');
define( 'UNI_VER_LONG', 18);

//integrate with pegasus
define( 'UNI_INTEGRA_USE', false);
define( 'UNI_INTEGRA_PATH', false);
                                                                                                                           
//acp path
define( 'ACP_PATH', 'admin/');

//acp style
define( 'ACP_STYLE', 'blues');

//simple path
define( 'SIMPLE_PATH', 'simple/');

//allow service mode?
define( 'SERVICE_MODE', false);

//allow plugins system?
//WARNING! THIS FUNCTION WILL CAUSE AN SYSTEM CRASH IF TURNED ON
define( 'ALLOW_PLUGINS', false);

// Allow SID over URL?
define( 'ALLOW_SID_IN_QUERY', true);

//stop default php error reporting
error_reporting( false);

//system error types
$error_types = array(
	0 => 'SYSTEM',
	1 => 'MYSQL',
	2 => 'STYLE',
	3 => 'MODULE',
);

//============================
//define and error handeler
//============================

function uni_error ( $errno, $errstr, $errfile, $errline){
	
	$template = file_get_contents( ROOT_PATH.'system/templates/error_page.htm');
		
	$error_params['ERROR_NUM'] = $errno;
	$error_params['ERROR_STR'] = $errstr;
	$error_params['ERROR_FILE'] = $errfile;
	$error_params['ERROR_LINE'] = $errline;
	
	foreach ( $error_params as $error_template => $error_replacement){
		
		$template = str_ireplace( '{'.$error_template.'}', $error_replacement, $template);
		
	}
	
	die ( $template);
	
};

set_error_handler( 'uni_error', E_ERROR & E_WARNING & E_CORE_ERROR & E_COMPILE_ERROR);

//============================
//Include configuration file
//============================

require_once( ROOT_PATH.'config.php');

//============================
//Include classes files
//============================

require_once( ROOT_PATH.'system/classes/exceptions_class.php');
require_once( ROOT_PATH.'system/classes/mysql_class.php');
require_once( ROOT_PATH.'system/classes/output_class.php');
require_once( ROOT_PATH.'system/classes/users_class.php');
require_once( ROOT_PATH.'system/classes/session_class.php');
require_once( ROOT_PATH.'system/classes/style_class.php');
require_once( ROOT_PATH.'system/classes/form_class.php');
require_once( ROOT_PATH.'system/classes/language_class.php');
require_once( ROOT_PATH.'system/classes/mail_class.php');
require_once( ROOT_PATH.'system/classes/logs_class.php');
require_once( ROOT_PATH.'system/classes/time_class.php');
require_once( ROOT_PATH.'system/classes/cache_class.php');
require_once( ROOT_PATH.'system/classes/hierarchy_class.php');
require_once( ROOT_PATH.'system/classes/captcha_class.php');
require_once( ROOT_PATH.'system/classes/menu_class.php');
require_once( ROOT_PATH.'system/classes/strings_class.php');
require_once( ROOT_PATH.'system/classes/utf8_class.php');
require_once( ROOT_PATH.'system/classes/acp_section_class.php');
require_once( ROOT_PATH.'system/classes/action_class.php');
require_once( ROOT_PATH.'system/classes/tasks_class.php');
require_once( ROOT_PATH.'system/classes/path_class.php');
require_once( ROOT_PATH.'system/classes/forums_class.php');
require_once( ROOT_PATH.'system/classes/main_error_class.php');
#require_once( ROOT_PATH.'system/classes/plugins_manager.php');
require_once( ROOT_PATH.'system/classes/tools_class.php');
require_once( ROOT_PATH.'system/functions.php');

//============================
//Include common action files
//============================

require_once( ROOT_PATH.'system/actions/action_main_page.php');
require_once( ROOT_PATH.'system/actions/action_login.php');
require_once( ROOT_PATH.'system/actions/action_users_list.php');
require_once( ROOT_PATH.'system/actions/action_user_profile.php');
require_once( ROOT_PATH.'system/actions/action_user_cp.php');
require_once( ROOT_PATH.'system/actions/action_calendar.php');
require_once( ROOT_PATH.'system/actions/action_calendar_event.php');
require_once( ROOT_PATH.'system/actions/action_new_calendar_event.php');
require_once( ROOT_PATH.'system/actions/action_edit_calendar_event.php');
require_once( ROOT_PATH.'system/actions/action_delete_calendar_event.php');
require_once( ROOT_PATH.'system/actions/action_guidelines.php');
require_once( ROOT_PATH.'system/actions/action_help.php');
require_once( ROOT_PATH.'system/actions/action_board_summary.php');
require_once( ROOT_PATH.'system/actions/action_team.php');
require_once( ROOT_PATH.'system/actions/action_register.php');
require_once( ROOT_PATH.'system/actions/action_activate_acc.php');
require_once( ROOT_PATH.'system/actions/action_captcha_image.php');
require_once( ROOT_PATH.'system/actions/action_show_forum.php');
require_once( ROOT_PATH.'system/actions/action_mail_user.php');
require_once( ROOT_PATH.'system/actions/action_reset_pass.php');
require_once( ROOT_PATH.'system/actions/action_show_topic.php');
require_once( ROOT_PATH.'system/actions/action_new_topic.php');
require_once( ROOT_PATH.'system/actions/action_edit_topic.php');
require_once( ROOT_PATH.'system/actions/action_show_post.php');
require_once( ROOT_PATH.'system/actions/action_new_post.php');
require_once( ROOT_PATH.'system/actions/action_edit_post.php');
require_once( ROOT_PATH.'system/actions/action_report_post.php');
require_once( ROOT_PATH.'system/actions/action_rate_post.php');
require_once( ROOT_PATH.'system/actions/action_show_reps.php');
require_once( ROOT_PATH.'system/actions/action_shoutbox.php');
require_once( ROOT_PATH.'system/actions/action_mod.php');
require_once( ROOT_PATH.'system/actions/action_user_warns.php');
require_once( ROOT_PATH.'system/actions/action_download.php');
require_once( ROOT_PATH.'system/actions/action_search.php');
require_once( ROOT_PATH.'system/actions/action_tags_cloud.php');
require_once( ROOT_PATH.'system/actions/action_online.php');
require_once( ROOT_PATH.'system/actions/action_mark_read.php');
require_once( ROOT_PATH.'system/actions/action_flush_cookies.php');
require_once( ROOT_PATH.'system/actions/action_rss_content.php');
require_once( ROOT_PATH.'system/admin_actions/action_edit_setting.php');
require_once( ROOT_PATH.'system/admin_actions/action_ops_list.php');
require_once( ROOT_PATH.'system/admin_actions/action_show_group.php');

//============================
//define including path for funcions
//============================

define( 'FUNCTIONS_GLOBALS', ROOT_PATH.'system/globals.php');

//============================
//If script isnt installed yet, redirect to installator
//============================

if ( !$system_settings['installed']){
	header( "Location:".ROOT_PATH.'install/');	
	die();
}

?>