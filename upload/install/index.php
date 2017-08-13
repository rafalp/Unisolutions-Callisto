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
|	Installer script
|	by Rafał Pitoń
|
#===========================================================================
*/

//run session
	
session_start();

//we are in script
define( 'IN_UNI', 1);
	
//software name
define( 'UNI_PRODUCT', 'Callisto');

//version numbers
define( 'UNI_VER', '1.1.6');
define( 'UNI_VER_LONG', 18);

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

require_once( ROOT_PATH.'system/classes/exceptions_class.php');
require_once( ROOT_PATH.'install/includes/output_class.php');
require_once( ROOT_PATH.'install/includes/style_class.php');
require_once( ROOT_PATH.'system/classes/mysql_class.php');
require_once( ROOT_PATH.'system/classes/menu_class.php');
require_once( ROOT_PATH.'system/classes/form_class.php');
require_once( ROOT_PATH.'system/classes/utf8_class.php');
require_once( ROOT_PATH.'install/includes/strings_class.php');

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
$output -> setTitle( 'Callisto Installation System');

/**
 * do code
 */

if ( file_exists( ROOT_PATH.'install/lock')){
	
	$page = '<h1>'.$lang_string['install_welcome'].'</h1>';
	$page .= '<p>'.$lang_string['install_locked'].'</p>';
				
	
}else{

	if ( $_GET['ajax']){
		
		/**
		 * ajax actions
		 */
		
		/**
		 * connect to db
		 */
		
		global $system_settings;
		include( ROOT_PATH.'config.php');
		
		global $mysql;
		$mysql = new mysql();
		
		switch ( $_GET['step']){
		
			case 4:
	
				/**
				 * create structure
				 */
				
				/**
				 * load install tabs
				 */
				
				include( ROOT_PATH.'install/db_data/db_sheme.php');
																	
				/**
				 * do tab?
				 */
				
				$next_query = $_GET['q'];
				
				if ( key_exists( $next_query, $install_query)){
					
					$query_text = $install_query[$next_query];
					
					$mysql -> query( $query_text);
											
					/**
					 * error?
					 */
					
					$table_name = substr( $query_text, strpos( $query_text, "`") + 1);
					$table_name = substr( $table_name, 0, strpos( $table_name, "`"));
									
					if ( strlen( mysql_error()) != 0){
						$page = $style -> drawImage( 'errror').' '.$lang_string['install_query_create'].' <b>"'.$table_name.'"</b>';
					}else{
						$page = $style -> drawImage( 'success').' '.$lang_string['install_query_create'].' <b>"'.$table_name.'"</b>';
					}
									
				}else{
				
					$page = 'done';
				
				}
									
			break;
			
			case 5:
					
				/**
				 * fill with data
				 */
								
				include( ROOT_PATH.'install/db_data/db_data_both.php');
				include( ROOT_PATH.'install/db_data/db_data_'.LANG.'.php');
				
				/**
				 * do tab?
				 */
				
				$next_query = $_GET['q'];
				
				if ( key_exists( $next_query, $install_query)){
					
					$query_text = $install_query[$next_query];
					
					$mysql -> query( $query_text);
											
					$table_name = substr( $query_text, strpos( $query_text, "`") + 1);
					$table_name = substr( $table_name, 0, strpos( $table_name, "`"));
									
					if ( strlen( mysql_error()) != 0){
						$page = $style -> drawImage( 'error').' '.$lang_string['install_query_insert'].' <b>"'.$table_name.'"</b>';
					}else{
						$page = $style -> drawImage( 'success').' '.$lang_string['install_query_insert'].' <b>"'.$table_name.'"</b>';
					}
					
				}else{
				
					$page = 'done';
				
				}
				
			break;
			
		}
		
		/**
		 * draw ajax_result
		 */
		
		$style -> drawAjaxPage( $page);
		
	}else{
		
		switch ( $_GET['step']){
		
			default:
				
				/**
				 * install entry
				 */
				
				$page = '<h1>'.$lang_string['install_welcome'].'</h1>
				<p>'.$lang_string['install_entry'].'</p>';
				
				/**
				 * do error check
				 */
				
				$can_install = true;
				
				if ( version_compare(PHP_VERSION, '5.1', '<')){
						
					$can_install = false;
					$page .= '<li>'.$lang_string['install_reqs_php'].' ('.$style -> drawImage( 'error').'; '.$lang_string['install_reqs_actual'].' '.PHP_VERSION.')</li>';
					
				}else{
					
					$page .= '<li>'.$lang_string['install_reqs_php'].' ('.$style -> drawImage( 'success').')</li>';
					
				}
				
				if (extension_loaded('mysql')){
						
					$mysql_ver = mysql_get_client_info();
							
					if( version_compare( $mysql_ver, '3.4', '>')){
						
						$page .= '<li>'.$lang_string['install_reqs_mysql'].' ('.$style -> drawImage( 'success').')</li>';
				
					}else{
						
						$can_install = false;
						$page .= '<li>'.$lang_string['install_reqs_mysql'].' ('.$style -> drawImage( 'error').'; '.$lang_string['install_reqs_actual'].' '.$mysql_ver.')</li>';
				
					}
					
				}else{
					
					$can_install = false;
					$page .= '<li>'.$lang_string['install_reqs_mysql'].' ('.$style -> drawImage( 'error').'; '.$lang_string['install_reqs_actual_nonie'].')</li>';
				
				}
				
				if ( is_writable( ROOT_PATH.'config.php')){
					
					$page .= '<li>'.$lang_string['install_reqs_saveable_config'].' ('.$style -> drawImage( 'success').')</li>';
				
				}else{
					
					$can_install = false;
					$page .= '<li>'.$lang_string['install_reqs_saveable_config'].' ('.$style -> drawImage( 'error').')</li>';
				
				}
				
				if ( is_writable( ROOT_PATH.'cache/')){
					
					$page .= '<li>'.$lang_string['install_reqs_saveable_cache'].' ('.$style -> drawImage( 'success').')</li>';
				
				}else{
					
					$can_install = false;
					$page .= '<li>'.$lang_string['install_reqs_saveable_cache'].' ('.$style -> drawImage( 'error').')</li>';
				
				}
				
				if ( is_writable( ROOT_PATH.'uploads/')){
					
					$page .= '<li>'.$lang_string['install_reqs_saveable_uploads'].' ('.$style -> drawImage( 'success').')</li>';
				
				}else{
					
					$can_install = false;
					$page .= '<li>'.$lang_string['install_reqs_saveable_uploads'].' ('.$style -> drawImage( 'error').')</li>';
				
				}
				
				/**
				 * close list
				 */
				
				$page .= '</ul></p>';
				
				if ( $can_install){
					
					$page .= '<p><input type="button" name="go" value="'.$lang_string['install_continue'].'" onClick="document.location=\''.ROOT_PATH.'install/index.php?step=2'.'\'"></p>';
					
				}else{
					
					$page .= '<p><b>'.$lang_string['install_cant_continue'].'</b></p>';
					
				}
				
			break;
			
			case 2:
				
				/**
				 * database connection
				 */
				
				$page = '<h1>'.$lang_string['install_db_configuration'].'</h1>';
				
				drawDBForm();
				
			break;
			
			case 3:
				
				/**
				 * database configuration check
				 */
				
				$page = '<h1>'.$lang_string['install_db_configuration'].'</h1>';
				
				/**
				 * get values
				 */
				
				$db_host = trim( $_POST['db_host']);
				$db_name = trim( $_POST['db_name']);
				$db_user = trim( $_POST['db_user']);
				$db_pass = trim( $_POST['db_pass']);
				$db_prefix = trim( $_POST['db_prefix']);
							
				if ( strlen($db_host) == 0){
				
					$page .= $style -> drawErrorBlock( $lang_string['install_db_configuration'], $lang_string['db_host_wrong']);
					
					drawDBForm( true);
				
				}else if ( strlen($db_name) == 0){
				
					$page .= $style -> drawErrorBlock( $lang_string['install_db_configuration'], $lang_string['db_name_wrong']);
					
					drawDBForm( true);
				
				}else if ( strlen($db_user) == 0){
				
					$page .= $style -> drawErrorBlock( $lang_string['install_db_configuration'], $lang_string['db_user_wrong']);
					
					drawDBForm( true);
				
				}else{
					
					/**
					 * make sure we has preffix
					 */
					
					if ( strlen($db_prefix) == 0){
						
						$db_prefix = 'call'.substr( md5( time()), 0, 4).'_';
						
					}
					
					/**
					 * try to connect db serv
					 */
					
					@$db_connection = mysql_connect( $db_host, $db_user, $db_pass);
					
					if ( $db_connection == false){
						
						/**
						 * error connecting db host
						 */
						
						$page .= $style -> drawErrorBlock( $lang_string['install_db_configuration'], $lang_string['db_host_no_connect']);
						
						drawDBForm( true);
									
					}else{
						
						/**
						 * no error, try to select db
						 */
						
						@$db_select = mysql_select_db( $db_name);
						
						if ( $db_select == false){
							
							/**
							 * error connecting to database
							 */
							
							$page .= $style -> drawErrorBlock( $lang_string['install_db_configuration'], $lang_string['db_no_connect']);
						
							drawDBForm( true);
											
						}else{
							
							/**
							 * check if talbes exists
							 */
							
							$tables_query = mysql_query( "SHOW TABLES LIKE '$db_prefix%'");
							
							if ( $tabs_result = mysql_fetch_array( $tables_query, MYSQL_ASSOC)){
								
								/**
								 * tables exist
								 */
								
								$page .= $style -> drawErrorBlock( $lang_string['install_db_configuration'], $lang_string['db_prefix_used']);
						
								drawDBForm( true);
							
							}else{
								
								/**
								 * all okey
								 * save config
								 */
								
								$config_sample = file_get_contents( ROOT_PATH.'install/db_data/config.php');
								$config_sample = str_replace( "['installed'] = false", "['installed'] = true", $config_sample);
								$config_sample = str_replace( "['db_name'] = ''", "['db_name'] = '$db_name'", $config_sample);
								$config_sample = str_replace( "['db_server'] = ''", "['db_server'] = '$db_host'", $config_sample);
								$config_sample = str_replace( "['db_user'] = ''", "['db_user'] = '$db_user'", $config_sample);
								$config_sample = str_replace( "['db_pass'] = ''", "['db_pass'] = '$db_pass'", $config_sample);
								$config_sample = str_replace( "['db_prefix'] = ''", "['db_prefix'] = '$db_prefix'", $config_sample);
								
								file_put_contents( ROOT_PATH.'config.php', $config_sample);
								
								/**
								 * draw ok
								 */
								
								$page .= '<p>'.$lang_string['db_connect_done'].'</p>';
								$page .= '<p><input type="button" name="go" value="'.$lang_string['install_continue'].'" onClick="document.location=\''.ROOT_PATH.'install/index.php?step=4'.'\'"></p>';
											
							}
							
						}
					
					}
					
				}
				
			break;
			
			case 4:
				
				/**
				 * big part of this page is controlled by ajax
				 */
				
				$page = '<h1>'.$lang_string['install_structure'].'</h1>';
				
				/**
				 * move ajax
				 */
				
				$page .= '<p id="install_ajax" style="height: 300px; overflow: auto;"></p>';
				$page .= '<div id="install_message" style="display: none">
					<p>'.$lang_string['install_structure_done'].'</p>
					<p><input type="button" name="go" value="'.$lang_string['install_continue'].'" onClick="document.location=\''.ROOT_PATH.'install/index.php?step=5'.'\'"></p>
				</div>';
				
				$page .= '<script type="text/javascript">
				
					query = 0
				
					install_done = false;
					install_screen = document.getElementById(\'install_ajax\')
					install_done_screen = document.getElementById(\'install_message\')
						
					uniAJAX = GetXmlHttpObject()
							
					last_anwswer = ""
					isBusy = false;
					
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
										
											query ++;
										
										}
										
										last_anwswer = uniAJAX.responseText
										
									}
									
									isBusy = false;
									
								}else{
								
									isBusy = true;
								
								}
									
							}
							
							if( isBusy == false){	

								uniAJAX.open("GET", "'.ROOT_PATH.'install/index.php?ajax=1&step=4&q=" + query, true)
								uniAJAX.send( null)
							
							}
						}			
					}
					
					window.setInterval( "installAjax( uniAJAX)", 500)
										
				</script>';
				
			break;
			
			case 5:
				
				/**
				 * big part of this page is controlled by ajax
				 */
				
				$page = '<h1>'.$lang_string['install_data'].'</h1>';
				
				/**
				 * move ajax
				 */
				
				$page .= '<p id="install_ajax" style="height: 300px; overflow: auto;"></p>';
				$page .= '<div id="install_message" style="display: none">
					<p>'.$lang_string['install_data_done'].'</p>
					<p><input type="button" name="go" value="'.$lang_string['install_continue'].'" onClick="document.location=\''.ROOT_PATH.'install/index.php?step=6'.'\'"></p>
				</div>';
				
				$page .= '<script type="text/javascript">
				
					query = 0
				
					install_done = false;
					install_screen = document.getElementById(\'install_ajax\')
					install_done_screen = document.getElementById(\'install_message\')
											
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
										
											query ++;
										
										}
										
										last_anwswer = uniAJAX.responseText
										
									}

									isBusy = false;
																	
								}else{
								
									isBusy = true;
								
								}
								
							}
								
							if( isBusy == false){				
								
								uniAJAX.open("GET", "'.ROOT_PATH.'install/index.php?ajax=1&step=5&q=" + query, true)
								uniAJAX.send( null)
							
							}
						}			
					}
					
					window.setInterval("installAjax( uniAJAX)", 500)
										
				</script>';
				
			break;
			
			case 6:
				
				/**
				 * basic configuration form
				 */
				
				$page = '<h1>'.$lang_string['forum_conf'].'</h1>';
				
				drawConfForm();
				
			break;
			
			case 7:
				
				/**
				 * check data, and put it into sql
				 */
				
				$page = '<h1>'.$lang_string['forum_conf'].'</h1>';
				
				$forum_path = $strings -> inputClear($_POST['forum_path'], false);
			
				if ( strlen( $forum_path) == 0){
					
					/**
					 * user has sended us empty file
					 */
					 
					$page .= $style -> drawErrorBlock( $lang_string['forum_conf'], $lang_string['forum_path_empty']);
					
					drawConfForm(true);
					
				}else{
					
					/**
					 * connect to db
					 */
					
					global $system_settings;
					include( ROOT_PATH.'config.php');
					
					global $mysql;
					$mysql = new mysql();
					
					/**
					 * udate path
					 */
					
					$mysql -> update( array('setting_value' => $forum_path), 'settings', "`setting_setting` = 'board_address'");
					
					/**
					 * message
					 */
					
					$page .= '<p>'.$lang_string['forum_conf_saved'].'</p>';
					
					$page .= '<p><input type="button" name="go" value="'.$lang_string['install_continue'].'" onClick="document.location=\''.ROOT_PATH.'install/index.php?step=8'.'\'"></p>';
					
				}
				
			break;
				
			case 8:
				
				/**
				 * admin account
				 */
				
				$page = '<h1>'.$lang_string['admin_acc'].'</h1>';
				
				drawAdminForm();
				
			break;
			
			case 9:
				
				/**
				 * create admin account
				 */
				
				$page = '<h1>'.$lang_string['admin_acc'].'</h1>';
				
				$admin_login = addslashes( htmlspecialchars(trim( $_POST['admin_login'])));
				$admin_mail = $strings -> inputClear( trim( $_POST['admin_mail']), false);
			
				$pass = trim( $_POST['admin_pass']);
				$pass = md5( md5( $pass).md5( $pass));	
				
				$pass_rep = trim( $_POST['admin_pass_rep']);
				$pass_rep = md5( md5( $pass_rep).md5( $pass_rep));	
				
				/**
				 * erros check
				 */
				
				if ( strlen( $admin_login) == 0){
					
					/**
					 * no login
					 */
					 
					$page .= $style -> drawErrorBlock( $lang_string['admin_acc'], $lang_string['admin_field_empty']);
					
					drawAdminForm(true);
					
				}else if ( strlen( $admin_mail) == 0){
					
					$page .= $style -> drawErrorBlock( $lang_string['admin_acc'], $lang_string['admin_field_empty']);
					
					drawAdminForm(true);
					
				}else if ( strlen( trim( $_POST['admin_pass'])) == 0){
					
					$page .= $style -> drawErrorBlock( $lang_string['admin_acc'], $lang_string['admin_field_empty']);
					
					drawAdminForm(true);
					
				}else if ( strlen( trim( $_POST['admin_pass_rep'])) == 0){
					
					$page .= $style -> drawErrorBlock( $lang_string['admin_acc'], $lang_string['admin_field_empty']);
					
					drawAdminForm(true);
					
				}else if ( $pass != $pass_rep){
					
					$page .= $style -> drawErrorBlock( $lang_string['admin_acc'], $lang_string['admin_pass_nomath']);
					
					drawAdminForm(true);
					
				}else if ( !checkMail( $admin_mail)){
					
					$page .= $style -> drawErrorBlock( $lang_string['admin_acc'], $lang_string['admin_wrong_email']);
					
					drawAdminForm(true);
					
				}else{
					
					/**
					 * connect to db
					 */
					
					global $system_settings;
					include( ROOT_PATH.'config.php');
					
					global $mysql;
					$mysql = new mysql();
					
					/**
					 * everything is ok, set users accounts
					 */
					
					$guest_acc['user_id'] = -1;
					$guest_acc['user_login'] = 'Guest';
					$guest_acc['user_regdate'] = time();
					$guest_acc['user_last_login'] = time();
					$guest_acc['user_main_group'] = 2;
					
					$mysql -> insert( $guest_acc, 'users');
					
					/**
					 * admin acc
					 */
					
					$admin_acc['user_id'] = 1;
					$admin_acc['user_login'] = $admin_login;
					$admin_acc['user_password'] = $pass;
					$admin_acc['user_regdate'] = time();
					$admin_acc['user_mail'] = $admin_mail;
					$admin_acc['user_active'] = true;
					$admin_acc['user_main_group'] = 1;
					$admin_acc['user_lang'] = LANG;
					$admin_acc['user_style'] = 1;
					
					$mysql -> insert( $admin_acc, 'users');
					
					/**
					 * now update settings
					 */
					
					$settings_to_update['board_start_date'] = time();
					$settings_to_update['default_language'] = LANG;
					$settings_to_update['default_style'] = 1;
					$settings_to_update['email_address'] = $admin_mail;
					$settings_to_update['record_time'] = time();
					
					foreach ( $settings_to_update as $setting => $value){
						
						$mysql -> update( array( 'setting_value' => $value), 'settings', "`setting_setting` = '$setting'");
						
					}
					
					/**
					 * instalaltions history entry
					 */
					
					$version['version_id'] = UNI_VER_LONG;
					$version['version_short'] = UNI_VER;
					$version['version_time'] = time();
					
					$mysql -> insert( $version, 'version_history');
					
					/**
					 * switch config
					 */
					
					$config_sample = file_get_contents( ROOT_PATH.'config.php');
					$config_sample = str_replace( "['installed'] = false", "['installed'] = true", $config_sample);
					
					file_put_contents( ROOT_PATH.'config.php', $config_sample);
					
					
					/**
					 * message
					 */
					
					$page .= '<p>'.$lang_string['admin_acc_done'].'</p>';
					
					$page .= '<p><input type="button" name="go" value="'.$lang_string['install_finish'].'" onClick="document.location=\''.ROOT_PATH.'install/index.php?step=10'.'\'"></p>';
									
				}
							
			break;
			
			case 10:
				
				$page = '<h1>'.$lang_string['install_finished'].'</h1>';
				$page .= '<p>'.$lang_string['install_finished_text'].'</p>';
				$page .= '<p><input type="button" name="go" value="'.$lang_string['show_forum'].'" onClick="document.location=\''.ROOT_PATH.'\'"></p>';
						
				/**
				 * close install
				 */
					
				file_put_contents( ROOT_PATH.'install/lock', 'If you want to unlock installator, simply delete this file.');
				
			break;
			
		}

	}
	
}

/**
 * bound code and display
 */

if ( !isset( $_GET['ajax'])){

$style -> drawPage( $page);
	
//open page
$output -> openPage();

//close page
$output -> closePage();
	
}

function drawDBForm( $retake = false){
	
	global $lang_string;
	global $style;
	global $page;
	
	/**
	 * values
	 */
	
	$db_host = 'localhost';
	$db_prefix = 'call_';
	
	if ( $retake){
		
		$db_host = htmlspecialchars_decode( trim( $_POST['db_host']));
		$db_name = htmlspecialchars_decode( trim( $_POST['db_name']));
		$db_user = htmlspecialchars_decode( trim( $_POST['db_user']));
		$db_pass = htmlspecialchars_decode( trim( $_POST['db_pass']));
		$db_prefix = htmlspecialchars_decode( trim( $_POST['db_prefix']));
				
	}
	
	/**
	 * draw form
	 */
	
	$db_set_form = new form();
	$db_set_form -> openForm( ROOT_PATH.'install/index.php?step=3');
	$db_set_form -> openOpTable();
	$db_set_form -> drawTextInput( $lang_string['db_host'], 'db_host', $db_host, $lang_string['db_host_help']);
	$db_set_form -> drawTextInput( $lang_string['db_name'], 'db_name', $db_name, $lang_string['db_name_help']);
	$db_set_form -> drawTextInput( $lang_string['db_user'], 'db_user', $db_user, $lang_string['db_user_help']);
	$db_set_form -> drawTextInput( $lang_string['db_pass'], 'db_pass', $db_pass, $lang_string['db_pass_help']);
	$db_set_form -> drawTextInput( $lang_string['db_prefix'], 'db_prefix', $db_prefix, $lang_string['db_prefix_help']);
	$db_set_form -> closeTable();
	$db_set_form -> drawButton( $lang_string['install_continue']);
	$db_set_form -> closeForm();
	
	$page .=  $style -> drawFormBlock( $lang_string['install_db_configuration'], $db_set_form -> display());
	
}

function drawConfForm( $retake = false){
	
	global $lang_string;
	global $style;
	global $strings;
	global $page;
	
	/**
	 * values
	 */
	
	$forum_path = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$forum_path = substr( $forum_path, 0, strpos( $forum_path, 'install'));
		
	if ( $retake){
		
		$db_host = stripslashes( $strings -> inputClear($_POST['forum_path'], false));
				
	}
	
	/**
	 * draw form
	 */
	
	$set_form = new form();
	$set_form -> openForm( ROOT_PATH.'install/index.php?step=7');
	$set_form -> openOpTable();
	$set_form -> drawTextInput( $lang_string['forum_path'], 'forum_path', $forum_path, $lang_string['forum_path_help']);
	$set_form -> closeTable();
	$set_form -> drawButton( $lang_string['install_continue']);
	$set_form -> closeForm();
	
	$page .=  $style -> drawFormBlock( $lang_string['forum_conf'], $set_form -> display());
	
}

function drawAdminForm( $retake = false){
	
	global $lang_string;
	global $style;
	global $strings;
	global $page;
	
	/**
	 * values
	 */
	
	$admin_login = '';
	$admin_pass = '';
	$admin_pass_rep = '';
	$admin_mail = '';
		
	if ( $retake){
		
		$admin_login = htmlspecialchars(trim( $_POST['admin_login']));
		$admin_mail = stripslashes( $strings -> inputClear( trim( $_POST['admin_mail']), false));
				
	}
	
	/**
	 * draw form
	 */
	
	$set_form = new form();
	$set_form -> openForm( ROOT_PATH.'install/index.php?step=9');
	$set_form -> openOpTable();
	$set_form -> drawTextInput( $lang_string['admin_login'], 'admin_login', $admin_login);
	$set_form -> drawTextInput( $lang_string['admin_pass'], 'admin_pass', $admin_pass);
	$set_form -> drawTextInput( $lang_string['admin_pass_rep'], 'admin_pass_rep', $admin_pass_rep);
	$set_form -> drawTextInput( $lang_string['admin_mail'], 'admin_mail', $admin_mail);
	$set_form -> closeTable();
	$set_form -> drawButton( $lang_string['install_continue']);
	$set_form -> closeForm();
	
	$page .=  $style -> drawFormBlock( $lang_string['admin_acc'], $set_form -> display());
	
}
	
function checkMail( $mail){
		
	$proper_mail = false;
	
	/**
	 * check at
	 */
	
	if ( strstr( $mail, "@") != false)
		$proper_mail = true;
	
	/**
	 * check domain
	 */
		
	if ( strrpos($mail, ".") == (strlen( $mail) - 3) || strrpos($mail, ".") == (strlen( $mail) - 4))
		$proper_mail = true;
		
	return $proper_mail;
		
}

?>