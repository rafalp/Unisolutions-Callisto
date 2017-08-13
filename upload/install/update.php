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
|	Updater script
|	by Rafał Pitoń
|
#===========================================================================
*/

/**
 * few presets
 */

if( version_compare(PHP_VERSION, '5.1', '<'))
	exit('<h1>CRITICAL ERROR</h1>Your\'s host PHP version is too old, to enable Unisolutions Updater working. Required version is 5.1.');

//run session
session_start();

//we are in script
define( 'IN_UNI', 1);
	
//software name
define( 'UNI_PRODUCT', 'Callisto');

//version numbers
define( 'UNI_VER', '1.0');
define( 'UNI_VER_LONG', 1);

//script location
define( 'ROOT_PATH', '../');

//install language
define( 'LANG', 'pl');

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
	
	die($template);
	
};

set_error_handler( 'uni_error', E_ERROR);

/**
 * load data there
 */

require_once( ROOT_PATH.'config.php');
require_once( ROOT_PATH.'system/classes/exceptions_class.php');
require_once( ROOT_PATH.'install/includes/output_class.php');
require_once( ROOT_PATH.'install/includes/style_class.php');
require_once( ROOT_PATH.'system/functions.php');
require_once( ROOT_PATH.'system/classes/mysql_class.php');
require_once( ROOT_PATH.'system/classes/menu_class.php');
require_once( ROOT_PATH.'system/classes/form_class.php');
require_once( ROOT_PATH.'system/classes/utf8_class.php');
require_once( ROOT_PATH.'install/includes/strings_class.php');
require_once( ROOT_PATH.'install/includes/session_class.php');

/**
 * create classes
 */

//output sender
global  $output;
$output = new output();

//style drawer
global  $style;
$style = new style();

//strings hanlder
global  $strings;
$strings = new strings();

//utf8
global  $utf8;
$utf8 = new utf8();

//set language
global $lang_string;

include( ROOT_PATH.'install/includes/lang_'.LANG.'.php');

/**
 * set start vars
 */

global $licence;
$licence = "
		".UNI_PRODUCT." - ".date( "Y")." by Unisolutions
		http://www.unisolutions.pl
		
		This software is released under GNU GPL v3 licence
		http://www.gnu.org/licenses/gpl-3.0.txt
		
		";

//credits
global $credits;
$credits = 'powered by '.UNI_PRODUCT.' &copy; '.date( 'Y').' <a href="http://www.unisolutions.pl">Unisolutions</a>';
	
//page content
global $page;
$page = '';

//time
date_default_timezone_set( 'UTC');

//set title
$output -> setTitle( 'Callisto Upgrading System');

/**
 * connect with mysql
 */

global $mysql;
$mysql = new mysql();

/**
 * start session
 */

global $session;
$session = new session();

/**
 * do code
 */

if ( $_GET['ajax']){
	
	/**
	 * get login
	 */
		
	if ( $session -> user_is_logged){
	
		/**
		 * get package
		 */
		
		$package = $_GET['package'];
		settype( $package, 'integer');
		
		/**
		 * check version
		 */
		
		$version_query = $mysql -> query( "SELECT * FROM version_history ORDER BY version_time DESC LIMIT 1");
		if ( $version_result = mysql_fetch_array( $version_query, MYSQL_ASSOC)){
			
			$actual_version = $version_result['version_id'];
			
		}
				
		if ( ($actual_version + 1) == $package){
			
			/**
			 * we can begin with update
			 */
			
			$step = $_GET['s'];
			settype( $step, 'integer');
			
			include( ROOT_PATH.'install/updates/update_'.$package.'.php');
			
		}else{
			
			/**
			 * end actualisation for this package
			 */
			
			$page = 'done';
			
		}
		
		/**
		 * draw ajax_result
		 */
		
		$style -> drawAjaxPage( $page);
		
	}
	
}else{
	
	switch ( $_GET['step']){
	
		default:
			
			/**
			 * install entry
			 */
			
			$page = '<h1>'.$lang_string['update_welcome'].'</h1>
			<p>'.$lang_string['update_entry'].'</p>';
			
			/**
			 * draw login form
			 */
			
			$user_login_form = new form();
			$user_login_form -> openForm( ROOT_PATH.'install/update.php?step=2');
			$user_login_form -> openOpTable();
			
			$user_login_form -> drawTextInput( $lang_string['update_login_name'], 'admin_login');
			$user_login_form -> drawPassInput( $lang_string['update_login_pass'], 'admin_pass');
			
			$user_login_form -> closeTable();
			$user_login_form -> drawButton( $lang_string['update_login_login']);
			$user_login_form -> closeForm();
			
			$page .= $style -> drawFormBlock( $lang_string['update_login'], $user_login_form -> display());
			
		break;
		
		case 2:
			
			/**
			 * check session
			 */
			
			$page = '<h1>'.$lang_string['update_login'].'</h1>';
			
			if ( $session -> user_is_logged){
				
				$page .= '<p>'.$lang_string['update_login_done'].'</p>';
				$page .= '<p><a href="'.ROOT_PATH.'install/update.php?step=3">'.$lang_string['update_begin'].'</a></p>';
				
			}else{
				
				$page .= $style -> drawErrorBlock( $lang_string['update_login'], $lang_string['update_login_wrong']);
				
				/**
				 * draw login form
				 */
				
				$user_login_form = new form();
				$user_login_form -> openForm( ROOT_PATH.'install/update.php?step=2');
				$user_login_form -> openOpTable();
				
				$user_login_form -> drawTextInput( $lang_string['update_login_name'], 'admin_login');
				$user_login_form -> drawPassInput( $lang_string['update_login_pass'], 'admin_pass');
				
				$user_login_form -> closeTable();
				$user_login_form -> drawButton( $lang_string['update_login_login']);
				$user_login_form -> closeForm();
				
				$page .= $style -> drawFormBlock( $lang_string['update_login'], $user_login_form -> display());
			
			}
			
		break;
		
		case 3:
			
			/**
			 * run updater, if we have to
			 */
			
			$page = '<h1>'.$lang_string['update_process'].'</h1>';
			
			if ( $session -> user_is_logged){
				
				/**
				 * include versions list
				 */
				
				include( ROOT_PATH.'install/updates/versions_list.php');
				
				/**
				 * select actual database version
				 */
				
				$version_query = $mysql -> query( "SELECT * FROM version_history ORDER BY version_time DESC LIMIT 1");
				
				if ( $version_result = mysql_fetch_array( $version_query, MYSQL_ASSOC)){
					
					if ( key_exists( ($version_result['version_id'] + 1 ), $versions)){
												
						/**
						 * close forum
						 */
						
						$mysql -> update( array( 'setting_value' => 1), 'settings', "`setting_setting` = 'board_offline'");

						/**
						 * flush cache
						 */
										
						$cache_files = glob( ROOT_PATH.'cache/*.php');

						if (count($cache_files) > 0){
						
							foreach ( $cache_files as $cache_file){
								
								unlink( $cache_file);								
							
							}
						}
							
						/**
						 * draw page
						 */
						
						$page .= '<p><b>'.$lang_string['update_process_begin'].' '.$versions[$version_result['version_id'] + 1].':</b></p>';
						
						$page .= '<div id="update_screen" style="height: 300px; overflow: auto;"></div>';
						$page .= '<div id="update_message" style="display: none">
							<p>'.$lang_string['update_next_done'].'</p>
							<p><input type="button" name="go" value="'.$lang_string['update_next_continue'].'" onClick="document.location=\''.ROOT_PATH.'install/update.php?step=3'.'\'"></p>
						</div>';
						
						$page .= '<script type="text/javascript">
						
							step = 0;
							
							install_done = false;
							install_screen = document.getElementById("update_screen");
							install_done_screen = document.getElementById("update_message");
																					
							last_anwswer = ""
							isBusy = false;
							
							uniAJAX = GetXmlHttpObject()
									
							function installAjax( uniAJAX){
			
								if( install_done == false){
											
									uniAJAX.onreadystatechange = function(){
											
											if(uniAJAX.readyState == 4){
												
												if( uniAJAX.responseText == "done"){
												
													install_done_screen.style.display = ""
													install_done = true
												
												}else{
												
													if( uniAJAX.responseText != last_anwswer){
													
														install_screen.innerHTML += uniAJAX.responseText + "<br />"
														
														step ++;
													
													}
													
													last_anwswer = uniAJAX.responseText
													
												}
												
												isBusy = false;
												
											}else{
											
												isBusy = true;
											
											}
									}
									
									if( isBusy == false){
									
										uniAJAX.open("GET", "'.ROOT_PATH.'install/update.php?ajax=1&package='.($version_result['version_id'] + 1).'&s=" + step, true)
										uniAJAX.send( null)
									
									}
									
								}			
							}
							
							window.setInterval("installAjax( uniAJAX)", 200);
												
						</script>';
						
					}else{
						
						/**
						 * we are up to date!
						 */
						
						$page .= '<p>'.$lang_string['update_process_no_need'].'</p>';
			
					}
					
				}
				
			}else{
				
				drawLoginPage();
				
			}
			
		break;
	}

}

/**
 * bound code and display
 */

if ( $_GET['ajax'] != true){
		
	$style -> drawPage( $page);
	
	//open page
	$output -> openPage();
	
	//close page
	$output -> closePage();
		
}

/**
 * loging page
 */

function drawLoginPage(){
	
	global $page;
	global $lang_string;
	
	$page .= $style -> drawErrorBlock( $lang_string['update_login'], $lang_string['update_login_wrong']);
	
	/**
	 * draw login form
	 */
	
	$user_login_form = new form();
	$user_login_form -> openForm( ROOT_PATH.'install/update.php?step=2');
	$user_login_form -> openOpTable();
	
	$user_login_form -> drawTextInput( $lang_string['update_login_name'], 'admin_login');
	$user_login_form -> drawPassInput( $lang_string['update_login_pass'], 'admin_pass');
	
	$user_login_form -> closeTable();
	$user_login_form -> drawButton( $lang_string['update_login_login']);
	$user_login_form -> closeForm();
	
	$page .= $style -> drawFormBlock( $lang_string['update_login'], $user_login_form -> display());
	
}

?>