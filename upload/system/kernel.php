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
|	Core
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class unisolutions{
	
	function __construct(){
		
		/**
		 * at the beginning we will create pointers to two global arrays, containing mysql configuration data, and system configuration, taken from db
		 */
		
		global $system_settings;
		global $settings;
		global $modules;
		global $system_tasks;
		global $generating_phase;
		
		/**
		 * start session
		 */
		
		session_start();
		
		/**
		 * set generating phase to pregeneratio
		 */
		
		$generating_phase = 'pregeneration';
				
		/**
		 * copyrights first
		 */
		
		global $credits_v;
		$credits_v = 'powered by <a href="http://www.uniboard-project.org">'.UNI_PRODUCT.'</a> v'.UNI_VER.' &copy; '.date( 'Y').' by Rafał Pitoń';
		
		global $credits;
		$credits = 'powered by <a href="http://www.uniboard-project.org">'.UNI_PRODUCT.'</a> &copy; '.date( 'Y').' by Rafał Pitoń';
		
		/**
		 * licence text
		 */
		
		global $licence;
		$licence = "
		".UNI_PRODUCT." - ".date( "Y")." by Rafał Pitoń
		http://www.uniboard-project.org
		
		This software is released under GNU GPL v3 licence
		http://www.gnu.org/licenses/gpl-3.0.txt
		
		";
		
		/**
		 * begin creating classes instances
		 */
				
		// output generating class
		global $output;
		$output = new output();	
		
		// utf8 class
		global $utf8;
		$utf8 = new utf8();
		
		// mysql class
		global $mysql;
		$mysql = new mysql();
		
		// cache class
		global $cache;
		$cache = new cache();
				
		//forums class
		global $forums;
		$forums = new forums();	
		
		// captcha class
		global $captcha;
		$captcha = new captcha();
		
		// strings class
		global $strings;
		$strings = new strings();
			
		// actual module: 0 for normal, 1 for box, 2 for ajax
		global $smode;
		$smode = $_GET['smode'];
		
		if( !isset($_GET['smode']))
			$smode = 0;
			
		if ($smode > 2)
			$smode = 2;		
		
		if ($smode < 0)
			$smode = 0;	
					
		/**
		 * further classes will need system configuration data, which is stored in global array called settings. We dont need full settings data, so everything we get from db is setting name, and its value
		 * firstly we will try to load it from cache
		 */
		
		$settings = $cache -> loadCache( 'system_settings');
		
		/**
		 * check if we loaded cache
		 */
		
		if( empty( $settings)){
		
			/**
			 * we havent got cached settings list, lets build it, and save again
			 */
			
			$settings = null;
			
			$settings_query = $mysql -> query( 'SELECT `setting_setting`, `setting_value` FROM settings');
			
			while( $result = mysql_fetch_array($settings_query, MYSQL_ASSOC)) {
				$settings[$result['setting_setting']] = stripslashes($result['setting_value']);
			}
			
			$cache -> saveCache( 'system_settings', $settings, 3600);
			
		}
		
		// users class
		global $users;
		$users = new users();
		
		/**
		 * build up array with ban filters
		 */
		
		global $ban_filters;
		
		$ban_filters = $cache -> loadCache( 'banfilters');
		if( gettype( $ban_filters) != 'array'){
			
			$ban_filters_query = $mysql -> query( "SELECT * FROM banfilters");
			
			while( $result = mysql_fetch_array( $ban_filters_query, MYSQL_NUM)){
				
				$ban_filters[$result[0]]['type'] = $result[1];
				$ban_filters[$result[0]]['filter'] = stripslashes($result[2]);
				
			}
			
			/**
			 * save new cache
			 */
			
			$cache -> saveCache( 'banfilters', $ban_filters);
			
		}
				
		// logs class
		global $logs;
		$logs = new logs();
		
		/**
		 * now we will create new session object
		 */
		
		global $session;
		$session = new session();
			
		/**
		 * refresh onlines list
		 */
		
		$users -> buildOnlines();
		
		/**
		 * now load language data
		 */
		
		//language class
		global $language;
		$language = new language();
					
		// mail class
		global $mail;
		$mail = new mail();
		
		/**
		 * time function
		 */
		
		global $time;
		$time = new site_time();
		
		/**
		 * now values containing layout elements
		 * 
		 * array containing blocks content
		 * @var $blocks array 
		 * 
		 * array containing other elements
		 * @var $page array
		 */
		
		global $page;
		$page = null;
		
		global $blocks;
		$blocks = null;
		
		/**
		 * depending on situation we will use diffren mechanics
		 */
		
		global $style;
		$style = new style();
		
		/**
		 * fix urls in forums and groups
		 */
		
		$forums -> fixPaths();
		$users -> fixPaths();
		
		/**
		 * do system tasks
		 */
		
		$system_tasks = new tasks();
		
		/**
		 * set generating phase to pre_content
		 */
		
		$generating_phase = 'pre_content';
		
		/**
		 * create new path object for breadcrums
		 */
		
		global $path;
		$path = new path();
		
		/**
		 * run plugins system?
		 */
		
		#if ( ALLOW_PLUGINS){
		#	
		#	global $plugins_manager;
		#	$plugins_manager = new plugins_manager();
		#	
		#	//run plugins
		#	$plugins_manager -> runPlugins( 'kernel_before_generation');
		#}
		
		/**
		 * set generation phase to content
		 */
		
		$generating_phase = 'content';
		
		if( defined( 'ACP' )){
			
			/**
			 * we are in acp
			 * check, if user is logged and/or has access to acp
			 */
			
			if( $session -> user['user_id'] != -1){
				
				/**
				 * user is logged in, we will check if it is an admin session
				 */
				
				if( $session -> user['user_can_be_admin']){
										
					/**
					 * pre reserve tabs
					 */
						
					global $page_tabs;
					
					$acp_path = ROOT_PATH.ACP_PATH;
					
					/**
					 * define proper cats list
					 */
					
					$acp_pages = array( 'main', 'forums', 'users', 'settings', 'look', 'admin');
										
					/**
					 * draw tabs
					 */
					
					if ( $smode == 0){
						
						/**
						 * draw tabs
						 */
						
						foreach ($acp_pages as $acp_page) {
							
							$style -> drawString( 'SECTION_'.strtoupper( $acp_page).'_URL', $acp_path.'index.php?section='.$acp_page);
							$style -> drawString( 'SECTION_'.strtoupper( $acp_page).'_IMG', '<img src="'.ROOT_PATH.'images/acp_icons/'.$acp_page.'.png">');
							$style -> drawString( 'SECTION_'.strtoupper( $acp_page).'_TITLE', $language -> getString( 'acp_'.$acp_page.'_title'));
							
						}
						
						/**
						 * user menu
						 */
							
						$style -> drawString( 'SHOW_PAGE_URL', ROOT_PATH);
						$style -> drawString( 'SHOW_PAGE_TITLE', $language -> getString( 'acp_view_board'));
							
						$style -> drawString( 'LOGOUT_URL', $acp_path.'index.php?act=login&do=acp_logout&smode=1');
						$style -> drawString( 'LOGOUT_TITLE', $language -> getString( 'acp_logout'));
														
					}
					
					/**
					 * define what to do
					 * sections comes before actions
					 */

									
					if ( !isset( $_GET['section']) && isset( $_GET['act']) && !empty( $_GET['act'])){
					
						/**
						 * we have to do an standard action
						 */
						
						global $actual_action;
						
						$proper_actions = array( 'login');
						
						if ( isset( $_GET['act']) && in_array( $_GET['act'], $proper_actions)){
							
							$actual_action = $_GET['act'];
							
						}
						
						switch ( $actual_action){
							
							case 'login':
								
								$new_login_action = new action_login();
								
							break;
							
						}
						
					}else{
						
						/**
						 * define section
						 */
						
						global $actual_section;
										
						if ( !isset( $_GET['section']) || !in_array( $_GET['section'], $acp_pages)){
						
							$actual_section = 'main';
						
						}else{
							
							$actual_section = $_GET['section'];
							
						}
						
						/**
						 * RUN SECTION
						 * 1. set page title
						 * 2. add breadcrumb
						 * 3. include section file
						 * 4. run section
						 */
						
						$output -> setTitle( $language -> getString( 'acp_'.$actual_section.'_title'));
					
						$path -> addBreadcrumb( $language -> getString( 'acp_'.$actual_section.'_title'), ROOT_PATH.ACP_PATH.'index.php?section='.$actual_section);
						
						$section_to_run = 'acp_section_'.$actual_section;
						
						require_once( ROOT_PATH.'system/acp_pages/acp_'.$actual_section.'.php');
						$run_section = new $section_to_run();
						
					
					}
					
				}else if( $session -> acp){
					
					/**
					 * it isnt an admin session, but user still has perms allowing to use acp, so we will load loging module
					 */
					
					if ( $smode != 2){
						
						$new_action = new action_login();
						$smode = 1;
						
					}
					
				}else{
					
					/**
					 * user havent got any perms allowing him to use acp, so draw error
					 */
					
					if ( $smode != 2){
					
						$smode = 1;
						
						$page .= $style -> drawErrorBlock( $language -> getString( 'error_no_access'), $language -> getString( 'user_acp_noaccess'));
					
					}
						
				}
				
			}else{
				
				/**
				 * it isnt an admin session, but user still has pers allowing to use acp, so we will load loging module
				 */
					
				$actual_action = 'login';
				$new_action = new action_login();
				$smode = 1;
				
			}	
			
		}else if ( SERVICE_MODE) {
		
			/**
			 * service mode
			 */
				
			switch ( $_GET['act']){
				
				case 'reset_style':

					$mysql -> update( array( 'user_style' => 1), 'users');
					$mysql -> update( array( 'style_path' => 'Callisto'), 'styles', "`style_id` = '1'");
					
					$page .= $style -> drawBlock( 'Reset style', 'All users style has been changed to Callisto.');
								
				break;
				
				case 'flush_cache':
				
					$cache_files = glob( ROOT_PATH.'cache/*.php');
					$deleted_files = 0;
					$deleted_files_list = array();
					
					foreach ( $cache_files as $cache_file){
						
						if ( is_writable( $cache_file)){
							
							unlink( $cache_file);
							$deleted_files_list[] = str_ireplace( ROOT_PATH.'cache/', '', $cache_file);
							$deleted_files ++;
						}
						
					}
					
					if ( $deleted_files > 0){
						
						$page .= $style -> drawBlock( 'Flush cache', '<b>'.$deleted_files.'</b> files has been deleted from cache:<br />'.join( "<br />", $deleted_files_list));
					
					}else{
					
						$page .= $style -> drawBlock( 'Flush cache', '<b>'.$deleted_files.'</b> files has been deleted from cache.');
					
					}
					
				break;
				
				case 'versions_history':
				
					/**
					 * select versions
					 */
					
					$versions_query = $mysql -> query( "SELECT * FROM version_history ORDER BY version_time DESC");
					
					while ( $versions_result = mysql_fetch_array( $versions_query, MYSQL_ASSOC)){
						
						$versions_result = $mysql -> clear( $versions_result);
						
						if ( UNI_VER == $versions_result['version_short'] && UNI_VER_LONG == $versions_result['version_id']){
						
							$found_versions[] = $versions_result['version_id'].':'.$versions_result['version_short'].' <span style="color: #0000FF">-same as files</span> -'.$time -> drawDate( $versions_result['version_time']);
						
						}else{
							
							$found_versions[] = $versions_result['version_id'].':'.$versions_result['version_short'].' -'.$time -> drawDate( $versions_result['version_time']);
						
						}
					}
					
					$page .= $style -> drawBlock( 'History of installations and upgrades', join( '<br />', $found_versions));
					
				break;
				
				case 'conf':
				
					$confs_tab = new form();
					$confs_tab -> openOpTable();
					
					$sets_query = $mysql -> query( "SELECT * FROM settings");
					
					while ( $sets_result = mysql_fetch_array( $sets_query, MYSQL_ASSOC)){
					
						$sets_result = $mysql -> clear( $sets_result);
						
						$confs_tab -> drawInfoRow( $sets_result['setting_setting'], $sets_result['setting_value']);
					
					}
					
					$confs_tab -> closeTable();
					
					$page .= $style -> drawFormBlock( 'Script configuration settings', $confs_tab -> display());
										
				break;
				
				case 'init':
				
					$inits_tab = new form();
					$inits_tab -> openOpTable();
					
					$inits_tab -> drawInfoRow( 'UNI_PRODUCT', UNI_PRODUCT);
					$inits_tab -> drawInfoRow( 'UNI_VER', UNI_VER);
					$inits_tab -> drawInfoRow( 'UNI_VER_LONG', UNI_VER_LONG);
					$inits_tab -> drawInfoRow( 'UNI_INTEGRA_USE', UNI_INTEGRA_USE);
					$inits_tab -> drawInfoRow( 'UNI_INTEGRA_PATH', UNI_INTEGRA_PATH);
					$inits_tab -> drawInfoRow( 'ACP_PATH', ACP_PATH);
					$inits_tab -> drawInfoRow( 'ACP_STYLE', ACP_STYLE);
					$inits_tab -> drawInfoRow( 'SIMPLE_PATH', SIMPLE_PATH);
					$inits_tab -> drawInfoRow( 'SERVICE_MODE', SERVICE_MODE);
					
					$inits_tab -> closeTable();
					
					$page .= $style -> drawFormBlock( 'Script initialisation settings', $inits_tab -> display());
										
				break;
				
				case 'phpinfo':
				
					phpinfo();
					die();
					
				break;
				
				default:
					
					
				break;	
			}
			
			/**
			 * avaiable comands
			 */
			
			$service_commands[] = '<a href="'.ROOT_PATH.'index.php?act=reset_style">reset_style</a> - reset style';
			$service_commands[] = '<a href="'.ROOT_PATH.'index.php?act=flush_cache">flush_cache</a> - flush cache';
			$service_commands[] = '<a href="'.ROOT_PATH.'index.php?act=versions_history">versions_history</a> - history of upgrading';
			$service_commands[] = '<a href="'.ROOT_PATH.'index.php?act=conf">conf</a> - shows configuration settings';
			$service_commands[] = '<a href="'.ROOT_PATH.'index.php?act=init">init</a> - shows initialisation settings';
			$service_commands[] = '<a href="'.ROOT_PATH.'index.php?act=phpinfo">phpinfo</a> - shows phpinfo()';
			
			$page .= $style -> drawBlock( 'Avaiable commands in service mode', join( "<br />", $service_commands));
			
		}else{						
					
			/**
			 * we are in frontend
			 */
				
			#if ( ALLOW_PLUGINS){				
			#	//run plugins
			#	$plugins_manager -> runPlugins( 'frontend_after_breadcrumbs');
			#}
			
			switch ( $settings['board_logo']){
				
				case '0':
					
					$style -> drawPart( 'logo_style_default', true);
					$style -> drawPart( 'logo_text_title', false);
					$style -> drawPart( 'logo_text_title_info', false);
					
				break;
				
				case '1':
					
					$style -> drawPart( 'logo_style_default', false);
					$style -> drawPart( 'logo_text_title', true);
					$style -> drawPart( 'logo_text_title_info', false);
					
				break;
				
				case '2':
					
					$style -> drawPart( 'logo_style_default', false);
					$style -> drawPart( 'logo_text_title', false);
					$style -> drawPart( 'logo_text_title_info', true);
					
				break;
				
			}
			
			$style -> drawString( 'BOARD_TITLE', $settings['board_name']);
			$style -> drawString( 'BOARDT_INFO', $settings['board_description']);
			
			/**
			 * create main menu
			 * (always the same)
			 */
			
			$style -> drawString( 'TITLE_HELP', $language -> getString( 'main_menu_help'));
			$style -> drawString( 'LINK_HELP', $this -> link( 'help'));
			
			/**
			 * if user has perms, draw search link
			 */
			
			if ( $session -> user['user_search']){
				
				$main_menu_links[] = array(
				'title' => $language -> getString( 'main_menu_search'),
				'action' => 'search'
				);
					
				$style -> drawString( 'TITLE_SEARCH', $language -> getString( 'main_menu_search'));
				$style -> drawString( 'LINK_SEARCH', $this -> link( 'search'));
				
				$style -> drawPart( 'search', true);
				
				if( $settings['forum_allow_tags'] && $settings['tags_cloud_enable']){
					
					$style -> drawPart( 'tags', true);
					
					$style -> drawString( 'TITLE_TAGS', $language -> getString( 'main_menu_tags'));
					$style -> drawString( 'LINK_TAGS', $this -> link( 'tags_cloud'));
										
				}else{
					
					$style -> drawPart( 'tags', false);
				
				}
				
			}else{
				
				$style -> drawPart( 'search', false);
				$style -> drawPart( 'tags', false);
				
			}
			
			$style -> drawString( 'TITLE_USERS', $language -> getString( 'main_menu_members'));
			$style -> drawString( 'LINK_USERS', $this -> link( 'users'));
			
			if ( $settings['users_list_turn'] && $session -> user['user_can_see_users_profiles']){
				
				$style -> drawPart( 'users_list', true);
				
			}else{
				
				$style -> drawPart( 'users_list', false);
				
			}
			
			/**
			 * calendar
			 */
			
			$style -> drawString( 'TITLE_CALENDAR', $language -> getString( 'main_menu_calendar'));
			$style -> drawString( 'LINK_CALENDAR', $this -> link( 'calendar'));
			
			if ( $settings['calendar_turn']){
				
				if ( !$settings['calendar_to_guests'] || ($settings['calendar_to_guests'] && $session -> user['user_id'] != -1)){
				
					$style -> drawPart( 'calendar', true);
				
				}else{
				
					$style -> drawPart( 'calendar', false);
					
				}
				
			}else{
				
				$style -> drawPart( 'calendar', false);
				
			}
			
			$style -> drawString( 'TITLE_CONTACT', $language -> getString( 'main_menu_contact'));
			$style -> drawString( 'LINK_CONTACT', $settings['email_address']);

			/**
			 * and additionals now
			 * website url
			 */
			
			if ( !empty($settings['board_website_url'])){
				
				/**
				 * link content
				 * defautly "return to site"
				 */
					
				$webside_link_title = $language -> getString( 'main_menu_website');
							
				/**
				 * if custom, overrite it
				 */
				
				if ( !empty($settings['board_website_title']))
					$webside_link_title = $settings['board_website_title'];
				
				
				$style -> drawString( 'TITLE_SITEURL', $webside_link_title);
				$style -> drawString( 'LINK_SITEURL', $settings['board_website_url']);
				
				$style -> drawPart( 'siteurl', true);
				
			}else{	
				
				$style -> drawPart( 'siteurl', false);
				
			}
						
			/**
			 * site link
			 */
						
			if ( $settings['guidelines_show_link'] && (!empty( $settings['guidelines_url']) || !empty( $settings['guidelines_board']))){
			
				/**
				 * define link title
				 */
					
				if ( !empty( $settings['guidelines_link_title'])){
					
					$guidelines_link_title = $settings['guidelines_link_title'];
					
				}else{
					
					$guidelines_link_title = $language -> getString( 'main_menu_guidelines');
					
				}
				
				/**
				 * and target
				 */
				
				if ( !empty( $settings['guidelines_url'])){
					
					/**
					 * url to guidelines
					 */
							
					$guidelines_url = $settings['guidelines_url'];
										
				}else{
					
					/**
					 * use our own guidelines system
					 */
							
					$guidelines_url = $this -> link( 'guidelines');
					
				}
				
				$style -> drawString( 'TITLE_GUIDELINES', $guidelines_link_title);
				$style -> drawString( 'LINK_GUIDELINES', $guidelines_url);				
				
				$style -> drawPart( 'guidelines', true);
				
			}else{
				
				$style -> drawPart( 'guidelines', false);
				
			}
						
			/**
			 * if user is an guest, draw red bar content
			 */
					
			$style -> drawPart( 'page_closed', false);
			
			if ( $session -> user['user_id'] == -1){
				
				if ( !$settings['board_offline']){
					
					$style -> drawPart( 'guest', true);
					
					$style -> drawString( 'GUEST_HELLO_TITLE', $language -> getString( 'board_hello_guest_title'));
						
					$style -> drawString( 'GUEST_HELLO_TEXT', $language -> getString( 'board_hello_guest_text'));
					
					$style -> drawPart( 'register', true);
					
					$style -> drawString( 'TITLE_REGISTER', $language -> getString( 'register'));
					$style -> drawString( 'LINK_REGISTER', $this -> link( 'register'));
				
					
					$style -> drawString( 'TITLE_LOGIN', $language -> getString( 'login'));
					$style -> drawString( 'LINK_LOGIN', $this -> link( 'login'));
						
				}else{			
				
					
					$style -> drawString( 'PAGE_IS_CLOSED', $language -> getString( 'board_is_offline_browsing_info'));
				
					$style -> drawPart( 'guest', false);
					$style -> drawPart( 'page_closed', true);
					
				}
					
				/**
				 * login form
				 */
				
				if ( $_GET['act'] != 'login' && !$settings['board_offline'] && !$settings['user_require_login_to_browse']){
					
					$style -> drawString( 'USER_LOGIN_TEXT', $language -> getString( 'login_username'));
					$style -> drawString( 'USER_PASS_TEXT', $language -> getString( 'login_pass'));
					$style -> drawString( 'USER_LOGIN_REMEBER', $language -> getString( 'login_renember'));
					$style -> drawString( 'QUICK_LOGIN_BUTTON', $language -> getString( 'login_button'));
									
					$style -> drawString( 'QUICK_LOGIN_URL', $this -> link( 'login&do=login_check'));
					
					$style -> drawPart( 'quick_login', true);
					
				}else{
					
					$style -> drawPart( 'quick_login', false);
					
				}
				
			}else{
												
				$style -> drawPart( 'guest', false);
				$style -> drawPart( 'quick_login', false);
				
			}
						
			/**
			 * if user is logged in, draw additional links
			 */
			
			if ( $session -> user['user_id'] != -1){
				
				$style -> drawPart( 'logged', true);
				
				/**
				 * if user is admin, draw acp link
				 */
				
				if ( $session -> user['user_can_be_admin'] && $settings['board_show_acp_link']){
					
					$style -> drawPart( 'admin', true);
											
					$style -> drawString( 'TITLE_ADMIN', $language -> getString( 'user_menu_acp'));
					$style -> drawString( 'LINK_ADMIN', ROOT_PATH.ACP_PATH);
					
				}else{
					
					$style -> drawPart( 'admin', false);
					
				}
													
				/**
				 * red message
				 */
							
				
				if ( $settings['board_offline']){
					
					$style -> drawString( 'PAGE_IS_CLOSED', $language -> getString( 'board_is_offline_browsing_info'));
					$style -> drawPart( 'page_closed', true);
					
				}
				
				$style -> drawString( 'TITLE_SETTINGS', $language -> getString( 'user_menu_settings'));
				$style -> drawString( 'LINK_SETTINGS', $this -> link( 'profile'));
				
				$style -> drawString( 'TITLE_LOGOUT', $language -> getString( 'logout'));
				$style -> drawString( 'LINK_LOGOUT', $this -> link( 'login&do=logout'));
				
				$style -> drawString( 'BOARD_USER_HELLO', $language -> getString( 'board_logged_in_user'));
				
				/**
				 * if user can use messenger, draw it
				 */
				
				if ( $session -> user['user_can_use_pm']){
					
					$style -> drawPart( 'messenger', true);
										
					$style -> drawString( 'TITLE_MESSENGER', $language -> getString( 'user_menu_messages'));
					$style -> drawString( 'LINK_MESSENGER', $this -> link( 'profile&do=pms_inbox'));
				
				}else{
					
					$style -> drawPart( 'messenger', false);
					
				}
										
				/**
				 * and welcome + last visit
				 */
								
				$style -> drawString( 'USER_HELLO_TEXT', $language -> getString( 'hello_logged_in'));
				$style -> drawString( 'LAST_VISIT_TEXT', $language -> getString( 'logged_in_lastvisit'));
				
				if ( $session -> user['user_last_login'] != 0){
					$style -> drawString( 'LAST_VISIT_TIME', $time -> drawDate( $session -> user['user_last_login']));
				}else{
					$style -> drawString( 'LAST_VISIT_TIME', '<i>'.$language -> getString( 'time_never').'</i>');
				}
				
				$style -> drawString( 'USER_HEADER_USER_POSTS_TITLE', $language -> getString( 'logged_in_posts'));
				$style -> drawString( 'USER_HEADER_USER_POSTS_NUM', $session -> user['user_posts_num']);
				
				/**
				 * avatar
				 */
				
				if ( $session -> user['user_avatar_type'] != 0 && $settings['users_can_avatars']){
					
					$style -> drawString( 'USER_AVATAR_IMAGE', $users -> drawAvatar( $session -> user['user_avatar_type'], $session -> user['user_avatar_image'], $session -> user['user_avatar_width'], $session -> user['user_avatar_height']));
				
					$style -> drawPart( 'user_avatar', true);
					
				}else{
					
					$style -> drawPart( 'user_avatar', false);
									
				}
				
			}else{
				
				$style -> drawPart( 'admin', false);
				$style -> drawPart( 'logged', false);
				
			}
				
			if ( $session -> user['user_search']){
							
				if ( $session -> user['user_id'] == -1){
				
					$style -> drawPart( 'today_posts', true);
					$style -> drawPart( 'new_posts', false);
					
					$style -> drawString( 'TITLE_TODAY_POSTS', $language -> getString( 'user_menu_show_today_posts'));
					$style -> drawString( 'LINK_TODAY_POSTS', $this -> link( 'search&do=today_posts'));
					
				}else{
					
					$style -> drawPart( 'today_posts', false);
					$style -> drawPart( 'new_posts', true);
					
					$style -> drawString( 'TITLE_NEW_POSTS', $language -> getString( 'user_menu_show_new_posts'));
					$style -> drawString( 'LINK_NEW_POSTS', $this -> link( 'search&do=new_posts'));
					
				}
				
			}else{
				
				$style -> drawPart( 'today_posts', false);
				$style -> drawPart( 'new_posts', false);
			
			}
				
			/**
			 * RSS
			 */
					
			if ( $settings['rss_turn']){
				
				$style -> drawPart( 'rss', true);
				
				$style -> drawString( 'RSS_TITLE', $language -> getString( 'main_menu_rss').': '.$settings['rss_channel_title']);
				$style -> drawString( 'RSS_LINK', ROOT_PATH.'index.php?act=rss');
				
			}else{
				
				$style -> drawPart( 'rss', false);
				
			}
			
			/**
			 * style select
			 */
			
			if ( $settings['style_allow_change'] && !defined( 'SIMPLE_MODE')){
		
				/**
				 * we will draw selector
				 */
				
				$selector_html = '<select id="skin_select" onchange="change_style()">';
				
				$styles_list = $cache -> loadCache( 'styles');
				
				if ( gettype( $styles_list) != 'array'){
					
					$styles_query = $mysql -> query( "SELECT * FROM `styles`");
					
					while ( $styles_result = mysql_fetch_array( $styles_query, MYSQL_ASSOC)){
						
						$styles_result = $mysql -> clear( $styles_result);
						
						$styles_list[$styles_result['style_id']] = $styles_result['style_name'];
						
					}
				
					$cache -> saveCache( 'styles', $styles_list);
					
				}
				
				foreach ( $styles_list as $style_id => $style_name){
				
					$selector_html .= '<option value="'.$style_id.'">'.$style_name.'</option>';
						
				}	
				
				$selector_html = str_ireplace( 'value="'.$session -> user['user_style'].'"', 'value="'.$session -> user['user_style'].'" selected="selected"', $selector_html);
				
				$selector_html .= '</select>';
				
				/**
				 * javascript now
				 */
				
				$selector_html .= '<script type="text/JavaScript">
	
					function change_style(){
					
						skin_selector = document.getElementById( \'skin_select\')
						
						if( location.href.indexOf( "index.php" ) == -1){
							
							document.location = location.href + "index.php?nstyle=" + skin_selector.value' . (USE_SID_IN_QUERY ? ' + "&sid=' . $session -> session_id . '"' : '') . '
						
						}else{
						
							if( location.href.indexOf( "nstyle=" ) != -1){
													
								document.location = location.href.replace( "nstyle='.$session -> user['user_style'].'", "nstyle=" + skin_selector.value)
							
							}else{
							
								if( location.href.indexOf( "?" ) != -1){
							
									if( location.href.indexOf( "#" ) != -1){
									
										document.location = location.href.replace( "#", "&nstyle=" + skin_selector.value + "#")
																
									}else{
									
										document.location = location.href + "&nstyle=" + skin_selector.value
									
									}
									
								}else{
								
									document.location = location.href + "?nstyle=" + skin_selector.value
							
								}
						
							}
								
						}
												
					}
	
				</script>';
				
				/**
				 * and draw it
				 */
				
				$style -> drawPart( 'skinselect', true);
				$style -> drawString( 'SKIN_SELECT', $selector_html);
			
			}else{
				
				$style -> drawPart( 'skinselect', false);			
			
			}
			
			/**
			 * and language
			 */
				
			$selector_html = '<select id="lang_select" onchange="change_language()">';
			
			$langs_list = $cache -> loadCache( 'languages');
			
			if ( gettype( $langs_list) != 'array'){
					
				$langs_query = $mysql -> query( "SELECT * FROM `languages` ORDER BY `lang_id`");
				
				while ( $langs_result = mysql_fetch_array( $langs_query, MYSQL_ASSOC)){
					
					$langs_result = $mysql -> clear( $langs_result);
					
					$langs_list[$langs_result['lang_id']] = $langs_result['lang_name'];
					
				}
			
				$cache -> saveCache( 'languages', $langs_list);
				
			}
				
			foreach ( $langs_list as $lang_id => $lang_name){
				
				$selector_html .= '<option value="'.$lang_id.'">'.$lang_name.'</option>';
					
			}
			
			$selector_html = str_ireplace( 'value="'.$session -> user['user_lang'].'"', 'value="'.$session -> user['user_lang'].'" selected="selected"', $selector_html);
			
			$selector_html .= '</select>';
			
			/**
			 * javascript now
			 */
			
			$selector_html .= '<script type="text/JavaScript">

				function change_language(){
				
					lang_selector = document.getElementById( \'lang_select\')
					
					if( location.href.indexOf( "index.php" ) == -1){
							
						document.location = location.href + "index.php?nlang=" + lang_selector.value' . (USE_SID_IN_QUERY ? ' + "&sid=' . $session -> session_id . '"' : '') . '
					
					}else{
					
						if( location.href.indexOf( "nlang=" ) != -1){
												
							document.location = location.href.replace( "nlang='.$session -> user['user_lang'].'", "nlang=" + lang_selector.value)
						
						}else{
						
							if( location.href.indexOf( "?" ) != -1){
						
								if( location.href.indexOf( "#" ) != -1){
									
									document.location = location.href.replace( "#", "&nlang=" + lang_selector.value + "#")
						
								}else{
									
									document.location = location.href + "&nlang=" + lang_selector.value
						
								}
								
							}else{
							
								document.location = location.href + "?nlang=" + lang_selector.value
						
							}
					
						}
							
					}
					
				}
						
			</script>';
			
			/**
			 * and draw it
			 */
			
			$style -> drawPart( 'langselect', true);
			$style -> drawString( 'LANGUAGE_SELECT', $selector_html);
				
			/**
			 * pm notification
			 */
			
			if ( $session -> user[ 'user_pm_new_num'] > 0 && $_GET['act'] != 'profile' && $_GET['do'] != 'pms_inbox'){
				
				$language -> setKey( 'unread_pm_num', $session -> user[ 'user_pm_new_num']);
				
				$style -> drawString( 'NEW_PRIVATE_MESSAGES', $language -> getString( 'new_messages_in_inbox'));
				$style -> drawString( 'READ_NEW_MESSAGES', $language -> getString( 'go_to_inbox_link'));
				$style -> drawPart( 'unread_messages');
				
			}else{
				
				$style -> drawPart( 'unread_messages', false);
				
			}
			
			/**
			 * time message
			 */
			
			$style -> drawString( 'SITE_TIME', $time -> drawDate(time(), false));
			
			/**
			 * light mode link
			 */			
			
			if( !defined( 'SIMPLE_MODE')){
			
				$style -> drawString( 'TITLE_MODE_CHANGE', $language -> getString( 'main_menu_light_mode'));
				$style -> drawString( 'LINK_MODE_CHANGE', ROOT_PATH.SIMPLE_PATH.(USE_SID_IN_QUERY ? '?sid='.$session -> session_id : ''));
				
			}else{
								
				$style -> drawString( 'TITLE_MODE_CHANGE', $language -> getString( 'main_menu_full_mode'));
				$style -> drawString( 'LINK_MODE_CHANGE', ROOT_PATH.(USE_SID_IN_QUERY ? '?sid='.$session -> session_id : ''));
				
			}
					
			/**
			 * few profile strings
			 */
			
			$style -> drawString( 'USER_LOGIN', $session -> user['user_login']);
			$style -> drawString( 'USER_NAME', $users -> users_groups[$session -> user['user_main_group']]['users_group_prefix'].$session -> user['user_login'].$users -> users_groups[$session -> user['user_main_group']]['users_group_suffix']);
			
			if ( strlen( $session -> user['user_custom_title']) > 0 && $settings['users_posts_to_title'] > 0 && $session -> user['user_posts_num'] >= $settings['users_posts_to_title']){
			
				$style -> drawString( 'USER_TITLE', $session -> user['user_custom_title']);
			
			}else if ( strlen( $users -> users_groups[$session -> user['user_main_group']]['users_group_title']) > 0){
				
				$style -> drawString( 'USER_TITLE', $users -> users_groups[$session -> user['user_main_group']]['users_group_title']);
			
			}else{
			
				$style -> drawString( 'USER_TITLE', $users -> drawRankName( $session -> user['user_posts_num']));
				
			}
			
			
			if ( strlen( $users -> users_groups[$session -> user['user_main_group']]['users_group_image']) > 0){
				$style -> drawString( 'USER_RANK', '<img src="'.$users -> users_groups[$session -> user['user_main_group']]['users_group_image'].'" alt="" title""/>');
			}else{
				$style -> drawString( 'USER_RANK', $users -> drawRankImage( $session -> user['user_posts_num']));
			}
			
			$style -> drawString( 'USER_PROFILE_LINK', $this -> link( 'user&user='.$session -> user['user_id']));
			$style -> drawString( 'USER_GROUP', $users -> users_groups[$session -> user['user_main_group']]['users_group_prefix'].$users -> users_groups[$session -> user['user_main_group']]['users_group_name'].$users -> users_groups[$session -> user['user_main_group']]['users_group_suffix']);
			$style -> drawString( 'PROFILE_GROUP', $language -> getString( 'user_group'));
			$style -> drawString( 'USER_POSTS', $session -> user['user_posts_num']);
			$style -> drawString( 'PROFILE_POSTS', $language -> getString( 'user_posts'));
			
			if ( $session -> user['user_last_post_time'] == 0){
				
				$style -> drawString( 'USER_LAST_POST', '<i>'.$language -> getString( 'never').'</i>');
				
			}else{
				
				$style -> drawString( 'USER_LAST_POST', $time -> drawDate( $session -> user['user_last_post_time']));
			
			}
			
			$style -> drawString( 'PROFILE_LAST_POSTS', $language -> getString( 'user_last_post'));
			
			$style -> drawString( 'USER_LAST_LOGIN', $time -> drawDate( $session -> user['user_last_login']));
			
			/**
			 * can we draw infotab?
			 */
				
			if ( $settings['board_allow_infotab'] && $session -> user['user_id'] != -1){
				
				$style -> drawPart( 'infotab', true);
				
				/**
				 * draw fields?
				 */
					
				$drawed_fields = '';
							
				foreach ( $users -> custom_fields as $field_id => $field_ops){
					
					if ( strlen( $session -> user['user_custom_field_'.$field_id]) > 0 && $field_ops['profile_field_inposts']){
						
						$field_template = $field_ops['profile_field_display'];
						
						$field_template = str_ireplace( '{NAME}', $field_ops['profile_field_name'], $field_template);
						
						if ( $field_ops['profile_field_type'] == 2){
							
							/**
							 * profile options list
							 */
							
							$preparsed_options = split( "\n", $field_ops['profile_field_options']);
							
							$made_options = array();
							
							foreach ( $preparsed_options as $preparsed_option) {
								
								$option_id = substr( $preparsed_option, 0, strpos( $preparsed_option, "="));
								$option_value = substr( $preparsed_option, strpos( $preparsed_option, "=") + 1);
								
								$made_options[$option_id] = $option_value;
								
							}
							
							$field_template = str_ireplace( '{KEY}', $session -> user['user_custom_field_'.$field_id], $field_template);
							$field_template = str_ireplace( '{VALUE}', $made_options[$session -> user['user_custom_field_'.$field_id]], $field_template);
							
						}else{
							
							$field_template = str_ireplace( '{VALUE}', $session -> user['user_custom_field_'.$field_id], $field_template);
													
						}
												
						$drawed_fields .= '<br />'.$field_template;
						
					}
					
				}
				
				$style -> drawString( 'FIELDS', $drawed_fields);
				
				/**
				 * reputation draw?
				 */
				
				if ( $settings['reputation_turn']){
					
					$style -> drawPart( 'reps', true);
					
					$style -> drawString( 'PROFILE_REPUTATION', $language -> getString( 'user_reputation'));
					$style -> drawString( 'USER_REPUTATION', $users -> drawReputation( $users -> countReputation( $session -> user['user_rep'], $session -> user['user_posts_num'], $session -> user['user_regdate'])));
					
				}else{
					
					$style -> drawPart( 'reps', false);
					
				}
				
				/**
				 * warns draw?
				 */
			
				if ( $settings['warns_turn']){
					
					$style -> drawPart( 'warns', true);
				
					$style -> drawString( 'PROFILE_WARNS', $language -> getString( 'user_warns'));
			
					if ( $session -> user['user_warns'] > 0){
									
						$warns_link_open = '<a href="'.$this -> link( 'user_warns?user='.$session -> user['user_id']).'">';
						$warns_link_close = '</a>';
						
					}else{
						
						$warns_link_open = '';
						$warns_link_close = '';
						
					}
					
					$style -> drawString( 'USER_WARNS', $warns_link_open.$users -> drawWarnLevel( $session -> user['user_warns']).$warns_link_close);
					
				}else{
					
					$style -> drawPart( 'warns', false);
				
				}
				
			}else{
				
				$style -> drawPart( 'infotab', false);
				
			}
			
			/**
			 * add breadcrumb width board title
			 */
			
			if ( defined( 'SIMPLE_MODE')){
			
				$path -> addBreadcrumb( $settings['board_name'], ROOT_PATH.SIMPLE_PATH.(USE_SID_IN_QUERY ? '?sid='.$session -> session_id : ''));
			
			}else{
			
				$path -> addBreadcrumb( $settings['board_name'], ROOT_PATH.(USE_SID_IN_QUERY ? '?sid='.$session -> session_id : ''));
			
			}
			
			/**
			 * hide news
			 */
			
			$style -> drawPart( 'news', false);
			
			/**
			 * create full list of proper actions
			 */
			
			$proper_actions = array( 'main', 'login', 'captcha', 'register', 'activate_acc', 'reset_pass', 'users', 'user', 'profile', 'mod', 'user_warns', 'mail_user', 'forums', 'forum', 'topic', 'new_topic', 'edit_topic', 'post', 'new_post', 'edit_post', 'report_post', 'rate_post', 'show_reps', 'download', 'search', 'tags_cloud', 'calendar', 'cal_event', 'cal_event_new', 'cal_event_edit', 'cal_event_del', 'guidelines', 'help', 'team', 'online', 'mark_read', 'flush_cookies', 'rss', 'shoutbox');
			
			/**
			 * check, if board requiries user to login
			 */
			
			if ( $settings['user_require_login_to_browse'] && $session -> user['user_id'] == -1){
				
				/**
				 * reduce proper actions to login, captcha and register
				 */
				
				if ( !defined( 'SIMPLE_MODE')){
				
					$proper_actions = array( 'login', 'reset_pass', 'captcha', 'register', 'activate_acc');
				
				}else{
					
					$proper_actions = array( 'simple_offline');
					
				}
			}
			
			/**
			 * check if board is closed
			 */
			
			if ( $settings['board_offline'] && !$session -> user['user_can_see_closed_page']){
				
				/**
				 * reduce proper actions to login and captcha
				 */
				
				$proper_actions = array( 'login', 'reset_pass', 'captcha');
				
			}
			
			if ( defined( 'SIMPLE_MODE')){
				
				if ( $settings['board_offline'] && !$session -> user['user_can_see_closed_page']){
					$proper_actions = array( 'simple_offline');
				}else{
					$proper_actions = array( 'main', 'forum', 'topic');
				}
			}
				
			/**
			 * now define action
			 */
			
			global $actual_action;
			
			if ( isset( $_GET['act']) && in_array( $_GET['act'], $proper_actions)){
				
				/**
				 * do intercepted act
				 */
				
				$actual_action = $_GET['act'];
				
			}else{
				
				/**
				 * do first act from list
				 */
				
				$actual_action = $proper_actions[0];
				
			}
			
			/**
			 * top adblock
			 */
			
			if ( !$settings['board_offline'] || $session -> user['user_can_see_closed_page']){
				
				if ( !defined( 'SIMPLE_MODE') && $smode == 0){
					
					if ( !$settings['ads_in_top_mainpage_only'] || ($settings['ads_in_top_mainpage_only'] && $actual_action == $proper_actions[0])){
						
						if ( !$settings['ads_in_top_guests_only'] || ($settings['ads_in_top_guests_only'] && $session -> user['user_id'] == -1)){
							
							if ( strlen( $settings['ads_in_top_content']) > 0){
								
								$page .= $style -> drawBlankBlock( $settings['ads_in_top_content']);
								
							}
							
						}
						
					}
					
				}
				
			}
			
			/**
			 * do specified action
			 */
			
			switch ( $actual_action){
				
				case 'main':
					
					/**
					 * run action drawing main page
					 */
					
					$board_main_page = new action_main_page();
					
				break;
				
				case 'simple_offline':
					
					/**
					 * run action drawing main page
					 */
					
					if ( $settings['board_offline']){
			
						if ( strlen( $settings['board_closed_message']) == 0){
						
							$message = $language -> getString( 'page_is_offline_info');
						
						}else{
						
							$message = $strings -> parseBB( nl2br( $settings['board_closed_message']), true, true);
												
						}
						
					}else{
					
						if ( $settings['user_require_login_to_browse']){
						
							$message = $language -> getString( 'page_is_closed_info');
						
						}else{
							
							$message = $language -> getString( 'login_message_frontend');
						
						}
					}
					
					$page .= $message;
					
				break;
				
				case 'login':
				
					$login_form = new action_login();
					
				break;
				
				case 'register':
				
					$register_form = new action_register();
					
				break;
					
				case 'activate_acc':
					
					/**
					 * activate new user account
					 */
				
					$activate_acc = new action_activate_acc();
					
				break;	
					
				case 'reset_pass':
					
					/**
					 * reset user pass
					 */
				
					$reset_pass = new action_reset_pass();
					
				break;	
				
				case 'users':
					
					/**
					 * run action drawing users list
					 */
					
					$users_list = new action_users_list();
					
				break;
				
				case 'user':
					
					/**
					 * run action drawing user card
					 */
					
					$users_list = new action_user_profile();
					
				break;
				
				case 'profile':
					
					/**
					 * run action drawing user cp
					 */
					
					$users_list = new action_user_cp();
					
				break;
								
				case 'user_warns':
				
					/**
					 * run action showing user warns
					 */
					
					$show_user_warns_action = new action_user_warns();
					
				break;
								
				case 'mod':
				
					/**
					 * run action handling mod actions
					 */
					
					$mod_action = new action_mod();
					
				break;
					
				case 'mail_user':
					
					/**
					 * mail user
					 */
					
					$mail_user = new action_mail_user();
					
				break;
				
				case 'guidelines':
					
					/**
					 * draw guidelines for user
					 */
				
					$guidelines = new action_guidelines();
					
				break;
				
				case 'help':
					
					/**
					 * draw help browser
					 */
				
					$help_browser = new action_help();
					
				break;
				
				case 'team':
					
					/**
					 * draw help browser
					 */
				
					$help_browser = new action_team();
					
				break;
				
				case 'forum':
					
					/**
					 * show forum
					 */
					
					$forum_show = new action_show_forum();
					
					/**
					 * simple mode addings
					 */
					
					if ( defined('SIMPLE_MODE')){
						
						$forum_to_show = $_GET['forum'];
						$page_to_show = $_GET['p'];
						
						settype( $forum_to_show, 'integer');
						settype( $page_to_show, 'integer');
						
						$style -> drawString( 'LINK_MODE_CHANGE', ROOT_PATH.'index.php?act=forum&forum='.$forum_to_show.'&p='.$page_to_show.(USE_SID_IN_QUERY ? '&sid='.$session -> session_id : ''));
						
					}
					
				break;
				
				case 'topic':
					
					/**
					 * show topic
					 */
					
					$show_topic = new action_show_topic;
					
					/**
					 * simple mode addings
					 */
					
					if ( defined('SIMPLE_MODE')){
						
						$topic_to_show = $_GET['topic'];
						$page_to_show = $_GET['p'];
						
						settype( $topic_to_show, 'integer');
						settype( $page_to_show, 'integer');
						
						$style -> drawString( 'LINK_MODE_CHANGE', ROOT_PATH.'index.php?act=topic&topic='.$topic_to_show.'&p='.$page_to_show.(USE_SID_IN_QUERY ? '&sid='.$session -> session_id : ''));
						
					}
					
				break;
				
				case 'new_topic':
					
					/**
					 * new topic
					 */
				
					$new_topic = new action_new_topic();
					
				break;
					
				case 'edit_topic':
					
					/**
					 * edit topic
					 */
				
					$edit_topic = new action_edit_topic();
					
				break;
				
				case 'post':
					
					/**
					 * show single post
					 */
					
					$show_post = new action_show_post();
					
				break;
				
				case 'new_post':
					
					/**
					 * new post
					 */
				
					$new_post = new action_new_post();
					
				break;
					
				case 'edit_post':
					
					/**
					 * edit post
					 */
				
					$edit_post = new action_edit_post();
					
				break;
				
				case 'report_post':
					
					/**
					 * report post
					 */
				
					$report_post = new action_report_post();
					
				break;
				
				case 'rate_post':
				
					/**
					 * rate marked post
					 */
					
					$rate_post = new action_rate_post();
										
				break;
				
				case 'show_reps':
					
					/**
					 * show post ranks post
					 */
					
					$rate_post = new action_show_reps();
					
				break;	
				
				case 'download':
					
					/**
					 * send attachment
					 */
					
					$download = new action_download();
					
				break;
				
				case 'calendar':
					
					/**
					 * run calendar
					 */
					
					$calendar = new action_calendar();
					
				break;
				
				case 'cal_event':
					
					/**
					 * show calendar event
					 */
					
					$calendar_event = new action_calendar_event();
					
				break;
				
				case 'cal_event_new':
					
					/**
					 * add new event to calendar
					 */
					
					$new_calendar_event = new action_new_calendar_event();
					
				break;
				
				case 'cal_event_edit':
					
					/**
					 * edit event in calendar
					 */
					
					$edit_calendar_event = new action_edit_calendar_event();
					
				break;
				
				case 'cal_event_del':
					
					/**
					 * delete event from calendar
					 */
					
					$delete_calendar_event = new action_delete_calendar_event();
					
				break;
				
				case 'search':
					
					/**
					 * do search
					 */
					
					$search = new action_search();
					
				break;
				
				case 'tags_cloud':
					
					/**
					 * draw tags cloud
					 */
					
					$tags_cloud = new action_tags_cloud();					
					
				break;
				
				case 'online':
					
					/**
					 * online
					 */
					
					$action_online = new action_online();
					
				break;
				
				case  'captcha':
					
					/**
					 * make new captcha generation
					 */
					
					$new_captha_image = new action_captcha_image();
					
				break;
				
				case 'mark_read':
					
					/**
					 * read forum's
					 */
					
					$mark_read = new action_mark_read();
					
				break;
				
				case 'flush_cookies':
					
					/**
					 * delete cookies
					 */
					
					$flush_cookies = new action_flush_cookies();
					
				break;
				
				case 'rss':
					
					/**
					 * send rss
					 */
					
					$rss_content = new action_rss_content();
					
				break;
				
				case 'shoutbox':
					
					/**
					 * show shoutbox
					 */
					
					$shoutbox_act = new action_shoutbox();
					
				break;
				
				case 'blank':
					
					/**
					 * this action does nothing, so its empty
					 */
					
				break;
				
			}
			
		}
		
		/**
		 * foot adblock
		 */
		
		if ( !$settings['board_offline'] || $session -> user['user_can_see_closed_page']){
			
			if ( !defined( 'SIMPLE_MODE') && $smode == 0 ){
				
				if ( !$settings['ads_in_foot_mainpage_only'] || ($settings['ads_in_foot_mainpage_only'] && $actual_action == $proper_actions[0])){
					
					if ( !$settings['ads_in_foot_guests_only'] || ($settings['ads_in_foot_guests_only'] && $session -> user['user_id'] == -1)){
						
						if ( strlen( $settings['ads_in_foot_content']) > 0){
							
							$page .= $style -> drawBlankBlock( $settings['ads_in_foot_content']);
							
						}
						
					}
					
				}
				
			}
			
		}
			
		/**
		 * if debug lvl is set to 3, draw debug tables
		 */
		
		if($settings['board_debug_level'] >= 3 && $smode != 2 && !defined( 'SIMPLE_MODE')){
			
			$this -> drawClientDeclaration();
			$this -> drawSessionData();
			$logs -> drawMailLogsTable();
			$this -> drawQueriesTable();
			
		}
				
		#if ( ALLOW_PLUGINS){				
		#	//run plugins
		#	$plugins_manager -> runPlugins( 'kernel_after_generation');
		#}
		
		/**
		 * we done everything, lets send it to output
		 */
		
		if ( $smode == 0){
		
			if ( defined( 'ACP')){
				
				$style -> drawAcpPage( $page, $blocks, $path -> display());
			
			}else if( SERVICE_MODE){
					
				$style -> drawSimplePage( $page);
			
			}else{
								
				$style -> drawPage( $page, $path -> display());
				
			}
			
		}else if ( $smode == 1){
						
			$style -> drawSimplePage( $page);
						
		}else{
						
			$style -> drawAjaxPage( $page);
			
		}
		
		/**
		 * set default title
		 */
		
		if ( $output -> getTitle() == ''){
		
			if (defined( 'ACP')){
			
				/**
				 * we are in ACP
				 */
				
				$output -> setTitle( $language -> getString( 'acp_title'));
			
			}else{
				
				/**
				 * we are in frontend
				 */
				
				$output -> title = $settings['board_name'];
				
				if ( strlen( $settings['board_description']) > 0)
					$output -> title .= ' - '.$settings['board_description'];
				
			}
		
		}
		/**
		 * open page
		 */
		
		if ( $smode != 2)
			$output -> openPage( $style -> style_css);
		
		/**
		 * end of page is the same in all situations
		 */
		if ( $smode != 2)
			$output -> closePage();
		
	}
	
	function link( $action){
		
		global $session;
		
		if ( defined( 'SIMPLE_MODE')){
			return ROOT_PATH.SIMPLE_PATH.'index.php?act='.$action.(USE_SID_IN_QUERY ? '&sid='.$session -> session_id : '');
		}else{
			return ROOT_PATH.'index.php?act='.$action.(USE_SID_IN_QUERY ? '&sid='.$session -> session_id : '');
		}
		
	}
	
	function drawClientDeclaration(){
		
		/**
		 * this function draws block containing HTTP_USER_AGENT
		 */
		
		global $style;
		global $language;
		global $page;
				
		$msg = $_SERVER['HTTP_USER_AGENT']; 
		$page .= $style -> drawBlock( $language -> getString( 'system_client_declaration'), $msg);
		
	}
	
	function drawSessionData(){
		
		/**
		 * this function draws session user data
		 */
		
		global $page;
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * start drawind
		 */
		
		$user_session_tab = new form();
		$user_session_tab -> drawSpacer( $language -> getString( 'system_session_info_tab_user'));
		$user_session_tab -> openOpTable();
		
		foreach ( $session -> user as $user_param => $user_value)
			$user_session_tab -> drawInfoRow( $user_param, $user_value);
		
		$user_session_tab -> closeTable();
		
		$page .= $style -> drawFormBlock( $language -> getString( 'system_session_info'), $user_session_tab -> display());
		
	}
	
	function drawQueriesTable(){
		
		/**
		 * this function draws table containing queries data
		 */
		
		global $mysql;
		global $style;
		global $page;
		global $language;
		
		$queries_table = new form();
		
		$queries_table -> openOpTable();
		$queries_table -> addToContent( '<tr>
											<th>'.$language -> getString( 'system_logs_sql_id').'</th>
											<th style="width: 100%">'.$language -> getString( 'system_logs_sql_sql').'</th>
											<th>'.$language -> getString( 'system_logs_sql_time').'</th>
										</tr>');
		
		$sql_num = 1;
		
		foreach ( $mysql -> queries_array as $query){
			
			if( !empty($query['err'])){
				
				$language -> setKey( 'mr', $query['err_no']);
				$error = $style -> drawErrorBlock( $language -> getString( 'system_logs_sql_error'), $query['err']);
				
			}
			
			$queries_table -> addToContent( '<tr>
											<td class="opt_row1">'.$sql_num.'</td>
											<td class="opt_row2">'.htmlspecialchars($query['content']).$error.'</td>
											<td class="opt_row1">'.$query['time'].'</td>
										</tr>');
			$sql_num++;
		}
		
		$queries_table -> closeTable();
		
		$page.= $style -> drawFormBlock( $language -> getString( 'system_logs_sql'), $queries_table -> display());
	}
	
}

?>