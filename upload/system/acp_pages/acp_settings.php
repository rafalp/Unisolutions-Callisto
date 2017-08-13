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
|	Acp Settings Page
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class acp_section_settings extends acp_section{
	
	function __construct(){
				
		/**
		 * include global classes pointers
		 */
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * begin
		 */
	
		$correct_acts = array( 'ops_list', 'edit_setting', 'show_group', 'manage_helps', 'edit_help', 'show_cache', 'manage_cache', 'new_task', 'add_task', 'edit_task', 'change_task', 'manage_tasks', 'tasks_logs', 'mails_list', 'add_mail', 'new_mail');
		
		#if( ALLOW_PLUGINS){
		#	$correct_acts[] = 'plugins';
		#	$correct_acts[] = 'plugins_install';
		#	$correct_acts[] = 'install_plugin';
		#}
		
		/**
		 * functions allowed for root
		 */
		
		if ( $session -> user['user_is_root']){
		
			$correct_acts[] = 'edit_setting';
			$correct_acts[] = 'new_group';
		
		}
		
		if ( !isset( $_GET['act']) || !in_array( $_GET['act'], $correct_acts)){
			$current_act = 	$correct_acts[0];
		}else{
			$current_act = $_GET['act'];
		}
		
		/**
		 * now array containing subsections
		 */
		
		$subsections_list['system_settings'] = 'system_settings';
		$subsections_list['maintenace'] = 'maintenace';
		
		#if( ALLOW_PLUGINS)
		#	$subsections_list['plugins'] = 'plugins';
		
		$subsections_list['mailing'] = 'mailing';
		
		/**
		 * and subsections list
		 */
		
		$subsections_elements_list['ops_list'] = 'system_settings';
		
		if ( $session -> user['user_is_root']){
		
			$subsections_elements_list['edit_setting'] = 'system_settings';
			$subsections_elements_list['ops_list&do=new_group'] = 'system_settings';
		
		}
		
		$subsections_elements_list['manage_helps'] = 'maintenace';
		$subsections_elements_list['manage_cache'] = 'maintenace';
		$subsections_elements_list['manage_tasks'] = 'maintenace';
		$subsections_elements_list['tasks_logs'] = 'maintenace';
		
		#if( ALLOW_PLUGINS){
		#	$subsections_elements_list['plugins'] = 'plugins';
		#	$subsections_elements_list['plugins_install'] = 'plugins';
		#}
		
		$subsections_elements_list['mails_list'] = 'mailing';
		$subsections_elements_list['new_mail'] = 'mailing';
		
		/**
		 * draw left-side menu
		 */
		
		parent::drawSubSections( $subsections_list, $subsections_elements_list);
		
		/**
		 * do act
		 */
		
		global $actual_action;
		$actual_action = $current_act;
		
		switch ( $current_act){
			
			case 'ops_list':
				
				$new_ops_list = new action_ops_list();
				
			break;
			
			case 'edit_setting':
				
				$edit_setting = new action_edit_setting();
				
			break;
			
			case 'show_group':
				
				$show_group = new action_show_group();
				
			break;
			
			case 'new_group':
				
				$new_group = new action_ops_list();
				
			break;
			
			case 'manage_helps':
				
				/**
				 * run help files manager
				 */
				
				$this -> act_manage_helps();
				
			break;
			
			case 'edit_help':
				
				$this -> act_edit_help_file();
				
			break;
			
			case 'show_cache':
				
				if ( isset( $_GET['file']) && !empty( $_GET['file'])){
				
					/**
					 * check if file exists
					 */
					
					if ( file_exists( ROOT_PATH.'cache/'.$_GET['file'].'.php')){
						
						/**
						 * add breadcrumbs
						 */
						
						$path_link = array( 'act' => 'manage_cache');
						
						$path -> addBreadcrumb( $language -> getString( 'acp_settings_section_maintenace'), parent::adminLink( parent::getId(), $path_link));		
						$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_manage_cache'), parent::adminLink( parent::getId(), $path_link));
						
						$path_link = array( 'act' => 'show_cache', 'file' => $_GET['file']);
						
						$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_manage_cache_show_file'), parent::adminLink( parent::getId(), $path_link));
						
						/**
						 * set page title
						 */
						
						$output -> setTitle( $language -> getString( 'acp_settings_subsection_manage_cache_show_file'));
												
						/**
						 * include that file
						 */
						
						include( ROOT_PATH.'cache/'.$_GET['file'].'.php');
						
						/**
						 * begin drawing form
						 */
						
						$cache_preview = new form();
						$cache_preview -> drawSpacer( $_GET['file']);
						$cache_preview -> openOpTable();
						
						$cache_preview -> drawRow( nl2br(htmlspecialchars( var_export( $cache, true))));
						
						$cache_preview -> closeTable();
						
						parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_manage_cache_show_file'), $cache_preview -> display()));
						
					}else{
						
						/**
						 * dont found what to display
						 * draw error
						 */
							
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_cache_show_file'), $language -> getString( 'acp_settings_subsection_manage_cache_show_file_notfound')));
						
						$this -> act_manage_cache();
						
					}
					
				}else{
				
					/**
					 * dont know what to display
					 * draw error
					 */
						
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_cache_show_file'), $language -> getString( 'acp_settings_subsection_manage_cache_show_file_notset')));
					
					$this -> act_manage_cache();
				
				}
				
			break;
			
			case 'manage_cache':
				
				$this -> act_manage_cache();
				
			break;
			
			case 'new_task':
				
				$this -> act_new_task();
				
			break;
			
			case 'add_task':
				
				/**
				 * check form
				 */
				
				if ( $session -> checkForm()){
					
					/**
					 * take elements from form
					 */
					
					$task_name = $strings -> inputClear( $_POST['task_name'], false);
					$task_info = $strings -> inputClear( $_POST['task_info'], false);
					$task_file = $strings -> inputClear( $_POST['task_file'], false);
					
					$task_minutes = $_POST['task_minutes'];
					$task_hours = $_POST['task_hours'];
					$task_days = $_POST['task_days'];
					
					$task_logs = $_POST['task_logs'];
					$task_active = $_POST['task_active'];
					
					settype( $task_minutes, 'integer');
					settype( $task_hours, 'integer');
					settype( $task_days, 'integer');
					
					settype( $task_logs, 'bool');
					settype( $task_active, 'bool');
					
					if ( $task_minutes < 0)
						$task_minutes = 0;
						
					if ( $task_minutes > 59)
						$task_minutes = 59;
					
					if ( $task_hours < 0)
						$task_hours = 0;
						
					if ( $task_hours > 23)
						$task_hours = 23;
						
					if ( $task_days < 0)
						$task_days = 0;
						
					/**
					 * do error check
					 */
					
					if ( empty( $task_name)){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_new'), $language -> getString( 'acp_settings_subsection_manage_tasks_new_notitle')));
						
						$this -> act_new_task();
						
					}else if( !file_exists( ROOT_PATH.'system/tasks/'.$task_file)){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_new'), $language -> getString( 'acp_settings_subsection_manage_tasks_new_nofile')));
						
						$this -> act_new_task();
						
					}else{
						
						/**
						 * add new
						 */
						
						$new_task_mysql['task_title'] = $task_name;
						$new_task_mysql['task_info'] = $task_info;
						$new_task_mysql['task_file'] = $task_file;
						$new_task_mysql['task_active'] = $task_active;
						$new_task_mysql['task_collect_logs'] = $task_logs;
						$new_task_mysql['task_minute'] = $task_minutes;
						$new_task_mysql['task_hour'] = $task_hours;
						$new_task_mysql['task_day'] = $task_days;
						
						$mysql -> insert( $new_task_mysql, 'tasks');
						
						/**
						 * add log
						 */
						
						$log_keys = array( 'new_task_name' => $task_name);
						
						$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_manage_tasks_new_log'), $log_keys);
						
						/**
						 * draw message
						 */
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_new'), $language -> getString( 'acp_settings_subsection_manage_tasks_new_done')));
						
						/**
						 * clear tasks
						 */
						
						$cache -> flushCache( 'system_tasks');
						
						$this -> act_manage_tasks();
						
					}
					
				}else{
					
					$this -> act_manage_tasks();
					
				}
				
			break;
			
			case 'edit_task':
				
				$this -> act_edit_task();
				
			break;
			
			case 'change_task':
			
				/**
				 * select task from mysql
				 */
				
				if ( $session -> checkForm()){
				
					if ( isset($_GET['task']) && !empty( $_GET['task'])){
							
						$task_to_edit = $_GET['task'];
						
						settype( $task_to_edit, 'integer');
						
						$task_to_run_query = $mysql -> query( "SELECT * FROM tasks WHERE `task_id` = '$task_to_edit'");
						
						if ( $task_result = mysql_fetch_array( $task_to_run_query, MYSQL_ASSOC)){
						
							/**
							 * take elements from form
							 */
							
							$task_name = $strings -> inputClear( $_POST['task_name'], false);
							$task_info = $strings -> inputClear( $_POST['task_info'], false);
							$task_file = $strings -> inputClear( $_POST['task_file'], false);
							
							$task_minutes = $_POST['task_minutes'];
							$task_hours = $_POST['task_hours'];
							$task_days = $_POST['task_days'];
							
							$task_logs = $_POST['task_logs'];
							$task_active = $_POST['task_active'];
							
							settype( $task_minutes, 'integer');
							settype( $task_hours, 'integer');
							settype( $task_days, 'integer');
							
							settype( $task_logs, 'bool');
							settype( $task_active, 'bool');
							
							if ( $task_minutes < 0)
								$task_minutes = 0;
								
							if ( $task_minutes > 59)
								$task_minutes = 59;
							
							if ( $task_hours < 0)
								$task_hours = 0;
								
							if ( $task_hours > 23)
								$task_hours = 23;
								
							if ( $task_days < 0)
								$task_days = 0;
								
							/**
							 * do error check
							 */
							
							if ( empty( $task_name)){
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_edit'), $language -> getString( 'acp_settings_subsection_manage_tasks_new_notitle')));
								
								$this -> act_edit_task();
								
							}else if( !file_exists( ROOT_PATH.'system/tasks/'.$task_file)){
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_edit'), $language -> getString( 'acp_settings_subsection_manage_tasks_new_nofile')));
								
								$this -> act_edit_task();
								
							}else{
								
								/**
								 * update task
								 */
								
								$new_task_mysql['task_title'] = $task_name;
								$new_task_mysql['task_info'] = $task_info;
								$new_task_mysql['task_file'] = $task_file;
								$new_task_mysql['task_active'] = $task_active;
								$new_task_mysql['task_collect_logs'] = $task_logs;
								$new_task_mysql['task_minute'] = $task_minutes;
								$new_task_mysql['task_hour'] = $task_hours;
								$new_task_mysql['task_day'] = $task_days;
								
								$mysql -> update( $new_task_mysql, 'tasks', "`task_id` = '$task_to_edit'");
								
								/**
								 * add log
								 */
								
								$log_keys = array( 'task_edit_id' => $task_to_edit);
								
								$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_manage_tasks_edited_log'), $log_keys);
								
								/**
								 * draw message
								 */
								
								parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_edit'), $language -> getString( 'acp_settings_subsection_manage_tasks_edit_done')));
								
								/**
								 * clear tasks
								 */
								
								$cache -> flushCache( 'system_tasks');
								
								$this -> act_manage_tasks();
							
							}
								
						}else{
						
							/**
							 * task to run not found
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_edit'), $language -> getString( 'acp_settings_subsection_manage_tasks_edit_notfound')));
						
							$this -> act_manage_tasks();
						
						}
						
					}else{
						
						/**
						 * task to run not defined
						 */
					
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_edit'), $language -> getString( 'acp_settings_subsection_manage_tasks_edit_notspecified')));
						
						$this -> act_manage_tasks();
					
					}
			
				}
				
			break;
				
			case 'manage_tasks':
				
				$this -> act_manage_tasks();
				
			break;
			
			case 'tasks_logs':
				
				$this -> act_draw_tasks_logs();
				
			break;
			
			case 'mails_list':
				
				$this -> act_mails_list();
				
			break;
			
			case 'add_mail':
				
				if ( $session -> checkForm()){
					
					/**
					 * user submitted proper form
					 */
					
					$mail_groups = $_POST['mail_groups'];
					$mail_subject = $strings -> inputClear( $_POST['mail_subject'], false);
					$mail_text = $strings -> inputClear( $_POST['mail_text'], false);
					$mail_ignore = $_POST['mail_to_all'];
					
					settype( $mail_groups, 'array');
					settype( $mail_ignore, 'bool');
					
					/**
					 * set last user
					 */
					
					$last_user = 0;
					
					if ( count( $mail_groups) > 0){
					
						$clear_groups = array();
						
						foreach ( $mail_groups as $mail_group_id){
							
							settype( $mail_group_id, 'integer');
							
							$clear_groups[] = (int) $mail_group_id;
							
						}
						
						if ( count( $clear_groups) > 0){
							
							$users_query = $mysql -> query( "SELECT `user_id` FROM users WHERE `user_main_group` IN (".join( ",", $clear_groups).") AND `user_id` > '0' ORDER BY `user_regdate` DESC LIMIT 1");
					
							if ( $user_result = mysql_fetch_assoc( $users_query))
								$last_user = $user_result['user_id'];					
							
						}
					}
					
					/**
					 * errors checking
					 */
					
					if ( $last_user == 0 ){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_new_mail'), $language -> getString( 'acp_settings_subsection_new_mail_empty_groups')));
						
						$this -> act_new_mail();
						
					}else if( strlen( $mail_subject) == 0){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_new_mail'), $language -> getString( 'acp_settings_subsection_new_mail_empty_title')));
						
						$this -> act_new_mail();
						
					}else if( strlen( $mail_text) == 0){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_new_mail'), $language -> getString( 'acp_settings_subsection_new_mail_empty_text')));
						
						$this -> act_new_mail();
						
					}else{
						
						/**
						 * all okey, add mail, and send it
						 */
					
						$new_mail_sql['mail_end_at_user'] = $last_user;
						$new_mail_sql['mail_toall'] = $mail_ignore;
						$new_mail_sql['mail_subject'] = $mail_subject;
						$new_mail_sql['mail_text'] = $mail_text;
						
						$mysql -> insert( $new_mail_sql, 'mails');
						
						$mysql -> update( array( 'task_active' => 1), 'tasks', "task_file = 'mailing.php'");	
						$cache -> flushCache( 'system_tasks');
						
						/**
						 * add log
						 */
						
						$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_new_mail_log'), array( 'new_mailing_title' => $mail_subject));
						
						/**
						 * draw message
						 */
							
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_new_mail'), $language -> getString( 'acp_settings_subsection_new_mail_done')));
						
						/**
						 * jump back to manager
						 */
						
						$this -> act_mails_list();
						
					}
					
				}else{
					
					$this -> act_mails_list();
					
				}
				
			break;
			
			case 'new_mail':
				
				/**
				 * new mail to mailing
				 */
				
				$this -> act_new_mail();
				
			break;
			
			/**
			 * PLUGINS CASES
			 */
			
			#case 'plugins':
			#
			#	$this -> act_plugins_mgr();
			#	
			#break;
			
			#case 'plugins_install':
			#
			#	$this -> act_plugins_install();
			#	
			#break;
			
			#case 'install_plugin':
			#
			#	$this -> act_install_plugin();
			#	
			#break;
			
		}
		
	}

	function act_manage_helps(){
		
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'manage_helps');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_section_maintenace'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_manage_helps'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * what to do?
		 */
		
		$proper_does = array( 'manage', 'new', 'change_file', 'move_down', 'move_up', 'delete');
		
		if ( isset( $_GET['do']) && in_array( $_GET['do'], $proper_does)){
			
			$act_to_do = $_GET['do'];
			
		}else{
			
			$act_to_do = $proper_does[0];
			
		}
		
		/**
		 * sub acts
		 */
		
		switch ( $_GET['sub_act']){
			
			case 'add_new':
				
				if ( $session -> checkForm()){
					
					$help_file_name = $strings -> inputClear( $_POST['help_file_name'], false);
					$help_file_info = $strings -> inputClear( $_POST['help_file_info'], false);
					$help_file_text = $strings -> inputClear( $_POST['help_file_text'], false);
					
					if( empty( $help_file_name)){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'error'), $language -> getString( 'acp_settings_subsection_help_files_new_file_name_empty')));
						
						$this -> act_edit_help_file();
						
					}else{
						
						$new_help_file_sql['help_file_pos'] = $this -> getNextHelpFreePos();
						$new_help_file_sql['help_file_name'] = $help_file_name;
						$new_help_file_sql['help_file_info'] = $help_file_info;
						$new_help_file_sql['help_file_text'] = $help_file_text;
						
						$mysql -> insert( $new_help_file_sql, 'help_files');
						
						/**
						 * draw message
						 */
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_help_files_new_file'), $language -> getString( 'acp_settings_subsection_help_files_new_file_saved')));
							
						/**
						 * set action
						 */
													
						$act_to_do = 'manage';
						
						/**
						 * add log
						 */
						
						$log_keys = array( 'new_help_file_name' => $help_file_name);
						$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_help_files_new_file_saved_log'), $log_keys);
											
					}
					
				}else{
					
					$act_to_do = 'manage';
					
				}
				
			break;
			
			case 'change_file':
				
				/**
				 * edit file
				 */
				
				if ( isset( $_GET['file']) && !empty( $_GET['file'])){
					
					$file_to_edit = $_GET['file'];
					settype( $file_to_edit, 'integer');
					
					/**
					 * select file from mysql
					 */
					
					$help_file_query = $mysql -> query( "SELECT * FROM help_files WHERE `help_file_id` = '$file_to_edit'");
					
					if ( $help_file_result = mysql_fetch_array( $help_file_query, MYSQL_ASSOC)){
						
						if ( $session -> checkForm()){
					
							$help_file_name = $strings -> inputClear( $_POST['help_file_name'], false);
							$help_file_info = $strings -> inputClear( $_POST['help_file_info'], false);
							$help_file_text = $strings -> inputClear( $_POST['help_file_text'], false);
							
							if( empty( $help_file_name)){
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'error'), $language -> getString( 'acp_settings_subsection_help_files_new_file_name_empty')));
								
								$this -> act_edit_help_file();
								
							}else{
								
								$new_help_file_sql['help_file_pos'] = $this -> getNextHelpFreePos();
								$new_help_file_sql['help_file_name'] = $help_file_name;
								$new_help_file_sql['help_file_info'] = $help_file_info;
								$new_help_file_sql['help_file_text'] = $help_file_text;
								
								$mysql -> update( $new_help_file_sql, 'help_files', "`help_file_id` = '$file_to_edit'");
								
								/**
								 * draw message
								 */
								
								parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_help_files_edit_file'), $language -> getString( 'acp_settings_subsection_help_files_file_hanged')));
									
								/**
								 * set action
								 */
															
								$act_to_do = 'manage';
								
								/**
								 * add log
								 */
								
								$log_keys = array( 'help_file_edited_id' => $help_file_name);
								$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_help_files_file_hanged_log'), $log_keys);
													
							}
							
						}else{
							
							$act_to_do = 'manage';
							
						}
									
					}else{
						
						/**
						 * file not found
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString('acp_settings_subsection_help_files_edit_file'), $language -> getString( 'acp_settings_subsection_help_files_edit_file_notfound')));
						
						$this -> act_manage_helps();
						
					}
					
				}else{
					
					parent::draw( $style -> drawErrorBlock( $language -> getString('acp_settings_subsection_help_files_edit_file'), $language -> getString( 'acp_settings_subsection_help_files_edit_file_empty')));
					
					$this -> act_manage_helps();
					
				}
				
			break;
									
		}
		
		/**
		 * now acts
		 */
		
		switch ( $act_to_do){
			
			case 'move_down':
				
				if ( isset( $_GET['file'])){
					
					$file_to_move = $_GET['file'];
					settype( $file_to_move, 'integer');
					
					$this -> helpFileDown( $file_to_move);
					
					parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_help_files_reorder_file'),  $language -> getString( 'acp_settings_subsection_help_files_files_reordered')));
					
					$log_key = array( 'help_moved_id' => $file_to_move);
					$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_help_files_moved_down_log'), $log_key);
					
				}
				
				$this -> act_draw_help_files_list();
				
			break;
			
			case 'move_up':
				
				if ( isset( $_GET['file'])){
					
					$file_to_move = $_GET['file'];
					settype( $file_to_move, 'integer');
					
					$this -> helpFileUp( $file_to_move);
					
					parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_help_files_reorder_file'),  $language -> getString( 'acp_settings_subsection_help_files_files_reordered')));
					
					$log_key = array( 'help_moved_id' => $file_to_move);
					$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_help_files_moved_up_log'), $log_key);
					
				}
				
				$this -> act_draw_help_files_list();
					
			break;
			
			case 'delete':
				
				if ( isset( $_GET['file'])){
					
					$file_to_delete = $_GET['file'];
					settype( $file_to_delete, 'integer');
					
					$mysql -> delete( 'help_files', "`help_file_id` = '$file_to_delete'");
		
					parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_help_files_delete_file'),  $language -> getString( 'acp_settings_subsection_help_files_file_deleted')));
					
					$log_key = array( 'help_deleted_id' => $file_to_delete);
					$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_help_files_deleted_log'), $log_key);
					
				}
				
				$this -> act_draw_help_files_list();
					
			break;
			
			case  'manage':
			
				$this -> act_draw_help_files_list();
				
			break;
			
			case 'new':
				
				/**
				 * add breadcrumb
				 */
				
				$path_link = array( 'act' => 'manage_helps', 'do' => 'new');
				
				$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_help_files_new_file'), parent::adminLink( parent::getId(), $path_link));
						
				/**
				 * set title
				 */
				
				$output -> setTitle( $language -> getString( 'acp_settings_subsection_help_files_new_file'));
					
				/**
				 * start from drawing form
				 */
				
				$new_help_file_link = array( 'act' => 'manage_helps', 'do' => 'new', 'sub_act' => 'add_new');
				
				$new_help_file = new form();
				$new_help_file -> openForm( parent::adminLink( parent::getId(), $new_help_file_link));
				$new_help_file -> openOpTable();
				$new_help_file -> drawTextInput( $language -> getString( 'acp_settings_subsection_help_files_new_file_name'), 'help_file_name', $help_file_name);
				$new_help_file -> drawEditor( $language -> getString( 'acp_settings_subsection_help_files_new_file_info'), 'help_file_info', $help_file_info, '', true, true);
				$new_help_file -> drawEditor( $language -> getString( 'acp_settings_subsection_help_files_new_file_text'), 'help_file_text', $help_file_text, '', true, true);
				$new_help_file -> closeTable();
				$new_help_file -> drawButton( $language -> getString( 'acp_settings_subsection_help_files_new_file_button'));
				$new_help_file -> closeForm();
				
				/**
				 * siplay form
				 */
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_help_files_new_file'), $new_help_file -> display()));
				
			break;
								
		}
		
	}
	
	function act_edit_help_file(){
		
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * edit file
		 */
		
		if ( isset( $_GET['file']) && !empty( $_GET['file'])){
			
			$file_to_edit = $_GET['file'];
			settype( $file_to_edit, 'integer');
			
			/**
			 * select file from mysql
			 */
			
			$help_file_query = $mysql -> query( "SELECT * FROM help_files WHERE `help_file_id` = '$file_to_edit'");
			
			if ( $help_file_result = mysql_fetch_array( $help_file_query, MYSQL_ASSOC)){
				
				$help_file_result = $mysql -> clear( $help_file_result);
				
				/**
				 * help file exists
				 */
				
				$path_link = array( 'act' => 'manage_helps');
				
				$path -> addBreadcrumb( $language -> getString( 'acp_settings_section_maintenace'), parent::adminLink( parent::getId(), $path_link));
				$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_manage_helps'), parent::adminLink( parent::getId(), $path_link));
				
				$path_link = array( 'act' => 'edit_help', 'file' => $file_to_edit);
				
				$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_help_files_edit_file'), parent::adminLink( parent::getId(), $path_link));
				
				/**
				 * set title
				 */
				
				$output -> setTitle( $language -> getString('acp_settings_subsection_help_files_edit_file'));
				
				/**
				 * get vars
				 */
				
				$help_file_name = $help_file_result['help_file_name'];
				$help_file_info = $help_file_result['help_file_info'];
				$help_file_text = $help_file_result['help_file_text'];
				
				/**
				 * if we are repeating edition
				 */
				
				if ( $_GET['sub_act'] == 'change_file'){
					
					$help_file_name = stripslashes( $strings -> inputClear($_POST['help_file_name'], false));
					$help_file_info = stripslashes( $strings -> inputClear($_POST['help_file_info'], false));
					$help_file_text = stripslashes( $strings -> inputClear($_POST['help_file_text'], false));
					
				}
				
				/**
				 * draw Form
				 */
				
				$edit_help_file_link = array( 'act' => 'manage_helps', 'sub_act' => 'change_file', 'file' => $file_to_edit);
		
				$edit_help_file = new form();
				$edit_help_file -> openForm( parent::adminLink( parent::getId(), $edit_help_file_link));
				$edit_help_file -> openOpTable();
				$edit_help_file -> drawTextInput( $language -> getString( 'acp_settings_subsection_help_files_new_file_name'), 'help_file_name', $help_file_name);
				$edit_help_file -> drawEditor( $language -> getString( 'acp_settings_subsection_help_files_new_file_info'), 'help_file_info', $help_file_info, '', true, true);
				$edit_help_file -> drawEditor( $language -> getString( 'acp_settings_subsection_help_files_new_file_text'), 'help_file_text', $help_file_text, '', true, true);
				$edit_help_file -> closeTable();
				$edit_help_file -> drawButton( $language -> getString( 'acp_settings_subsection_help_files_change_file_button'));
				$edit_help_file -> closeForm();
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_help_files_edit_file'), $edit_help_file -> display()));
				
			}else{
				
				/**
				 * file not found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString('acp_settings_subsection_help_files_edit_file'), $language -> getString( 'acp_settings_subsection_help_files_edit_file_notfound')));
				
				$this -> act_manage_helps();
				
			}
			
		}else{
			
			parent::draw( $style -> drawErrorBlock( $language -> getString('acp_settings_subsection_help_files_edit_file'), $language -> getString( 'acp_settings_subsection_help_files_edit_file_empty')));
			
			$this -> act_manage_helps();
			
		}
		
	}
	
	function act_draw_help_files_list(){
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * set title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_settings_subsection_manage_helps'));
		
		/**
		 * begin from drawing form
		 */
		
		$new_help_file_link = array( 'act' => 'manage_helps', 'do' => 'new');
		
		$help_files_form = new form();
		$help_files_form -> openForm( parent::adminLink( parent::getId(), $new_help_file_link));
		$help_files_form -> openOpTable();
		$help_files_form -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_settings_subsection_help_files_file').'</th>
			<th>'.$language -> getString( 'actions').'</th>
		</tr>');
		
		$help_files_query = $mysql -> query( "SELECT * FROM help_files ORDER BY help_file_pos");
		
		while ( $help_file_result = mysql_fetch_array( $help_files_query, MYSQL_ASSOC)) {
			
			$help_file_result = $mysql -> clear( $help_file_result);
			
			$action_move_down_link = array( 'act' => 'manage_helps', 'file' => $help_file_result['help_file_id'], 'do' => 'move_down');
			$action_move_up_link = array( 'act' => 'manage_helps', 'file' => $help_file_result['help_file_id'], 'do' => 'move_up');
			$action_edit_link = array( 'act' => 'edit_help', 'file' => $help_file_result['help_file_id']);
			$action_delete_link = array( 'act' => 'manage_helps', 'file' => $help_file_result['help_file_id'], 'do' => 'delete');
			
			$help_files_form -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%">'.$help_file_result['help_file_name'].'</td>
				<td class="opt_row2" style="text-align: center" NOWRAP>
				<a href="'.parent::adminLink( parent::getId(), $action_move_down_link).'">'.$style -> drawImage( 'go_down', $language ->getString( 'go_down')).'</a>
				<a href="'.parent::adminLink( parent::getId(), $action_move_up_link).'">'.$style -> drawImage( 'go_up', $language ->getString( 'go_up')).'</a>
				<a href="'.parent::adminLink( parent::getId(), $action_edit_link).'">'.$style -> drawImage( 'edit', $language ->getString( 'edit')).'</a>
				<a href="'.parent::adminLink( parent::getId(), $action_delete_link).'">'.$style -> drawImage( 'delete', $language ->getString( 'delete')).'</a></td>
			</tr>');
			
		}
		
		$help_files_form -> closeTable();
		$help_files_form -> drawButton( $language -> getString( 'acp_settings_subsection_help_files_new_file_button'));
		$help_files_form -> closeForm();
		
		/**
		 * display it
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_manage_helps'), $help_files_form -> display()));
		
		
	}
	
	function getNextHelpFreePos(){
		
		global $mysql;
			
		$forums_query = $mysql -> query( "SELECT help_file_pos FROM help_files ORDER BY `help_file_pos` DESC LIMIT 1");
		
		if ( $result = mysql_fetch_array( $forums_query, MYSQL_ASSOC))
			$free_pos = $result['help_file_pos'] + 1;
			
		if( !isset( $free_pos))
			$free_pos = 0;
			
		return $free_pos;
		
	}
	
	function helpFileUp( $id){
				
		global $mysql;
			
		/**
		 * firstly, we will chceck element position
		 */
			
		$query = $mysql -> query( 'SELECT `help_file_pos` FROM help_files WHERE `help_file_id` = \''.$id.'\'');			
			
		if($result = mysql_fetch_array( $query, MYSQL_ASSOC))
			$actual_pos = $result['help_file_pos'];
				
		/**
		 * now position highest than actual
		 */
			
		$query = $mysql -> query( 'SELECT `help_file_id`, `help_file_pos` FROM help_files WHERE `help_file_pos` < \''.$actual_pos.'\' ORDER BY `help_file_pos` DESC LIMIT 1');			
			
		if($result = mysql_fetch_array( $query, MYSQL_ASSOC)){
			$next_id = $result['help_file_id'];
			$next_pos = $result['help_file_pos'];
		}
			
		/**
		 * check, if module is found 
		 */
			
		if( isset($next_id)){
				
			$mysql -> query( "UPDATE help_files SET `help_file_pos` = '$next_pos' WHERE `help_file_id` = '$id'");
			$mysql -> query( "UPDATE help_files SET `help_file_pos` = '$actual_pos' WHERE `help_file_id` = '$next_id'");
				
		}					
	}
		
	function helpFileDown( $id){
			
		global $mysql;
			
		/**
		 * firstly, we will chceck element position
		 */
			
		$query = $mysql -> query( 'SELECT `help_file_pos` FROM help_files WHERE `help_file_id` = \''.$id.'\'');			
			
		if($result = mysql_fetch_array( $query, MYSQL_ASSOC))
			$actual_pos = $result['help_file_pos'];
				
		/**
		 * now position highest than actual
		 */
			
		$query = $mysql -> query( 'SELECT `help_file_id`, `help_file_pos` FROM help_files WHERE `help_file_pos` > \''.$actual_pos.'\' ORDER BY `help_file_pos` DESC LIMIT 1');			
			
		if($result = mysql_fetch_array( $query, MYSQL_ASSOC)){
			$next_id = $result['help_file_id'];
			$next_pos = $result['help_file_pos'];
		}
			
		/**
		 * check, if module is found 
		 */
			
		if( isset($next_id)){
				
			$mysql -> query( "UPDATE help_files SET `help_file_pos` = '$next_pos' WHERE `help_file_id` = '$id'");
			$mysql -> query( "UPDATE help_files SET `help_file_pos` = '$actual_pos' WHERE `help_file_id` = '$next_id'");
				
		}
					
	}

	function act_manage_cache(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'manage_cache');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_section_maintenace'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_manage_cache'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_settings_subsection_manage_cache'));

		/**
		 * delete cache
		 */
		
		if ( isset( $_GET['file']) && !empty( $_GET['file']) && $_GET['do'] == 'delete'){
				
			/**
			 * check if file exists
			 */
			
			if ( file_exists( ROOT_PATH.'cache/'.$_GET['file'].'.php')){
				
				/**
				 * delete file
				 */
				
				unlink( ROOT_PATH.'cache/'.$_GET['file'].'.php');
				
				/**
				 * draw message
				 */
				
				parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_manage_cache_delete_file'), $language -> getString( 'acp_settings_subsection_manage_cache_delete_file_done')));
				
				/**
				 * add log
				 */
				
				$log_keys = array( 'cache_deleted_file' => $_GET['file']);
				
				$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_manage_cache_delete_file_log'), $log_keys);
				
			}else{
				
				/**
				 * dont found what to display
				 * draw error
				 */
					
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_cache_delete_file'), $language -> getString( 'acp_settings_subsection_manage_cache_delete_file_notfound')));
								
			}
			
		}
		
		/**
		 * build list of files
		 */
		
		$cache_files = glob( ROOT_PATH.'cache/*.php');
		
		/**
		 * we will cut names only for list 
		 */
		
		$cache_files_list = new form();
		$cache_files_list -> openOpTable();
		$cache_files_list -> addToContent('<tr>
			<th>'.$language -> getString( 'acp_settings_subsection_manage_cache_file').'</th>
			<th>'.$language -> getString( 'acp_settings_subsection_manage_cache_file_size').'</th>
			<th>'.$language -> getString( 'actions').'</th>
		</tr>');
		
		foreach ( $cache_files as $cache_file_name){
				
			$short_file_name = str_ireplace( ROOT_PATH.'cache/', '', $cache_file_name);
			$short_file_name = str_ireplace( '.php', '', $short_file_name);
			
			$show_cache_file_link = array( 'act' => 'show_cache', 'file' => $short_file_name);
			
			$delete_cache_file_link = array( 'act' => 'manage_cache', 'do' => 'delete', 'file' => $short_file_name);
			
			$cache_files_list -> addToContent('<tr>
				<td class="opt_row1" style="width: 100%"><a href="'.parent::adminLink( parent::getId(), $show_cache_file_link).'">'.$short_file_name.'</a></td>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$this -> cache_file_size( filesize( $cache_file_name)).'</td>
				<td class="opt_row3" style="text-align: center" NOWRAP><a href="'.parent::adminLink( parent::getId(), $delete_cache_file_link).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a></td>
			</tr>');
			
		}
			
		$cache_files_list -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_manage_cache'), $cache_files_list -> display()));
			
	}
	
	function cache_file_size( $size){
		
		if ( $size > 1024){
			
			return round( ($size/1024), 2).' kb';
			
		}else if ( $size > 1048576){
			
			return round( ($size/1048576), 2).' mb';
			
		}else{
			
			return round( ($size), 2).' b';
			
		}
		
	}
	
	function cache_get_type( $type){
		
	}
	
	function act_new_task(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'manage_tasks');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_section_maintenace'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_manage_tasks'), parent::adminLink( parent::getId(), $path_link));
		
		$path_link = array( 'act' => 'new_task');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_manage_tasks_new'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_settings_subsection_manage_tasks_new'));
		
		/**
		 * define what to do
		 */
		
		$task_days = 0;
		$task_logs = true;
		$task_active = true;
		
		if ( $_GET['act'] == 'add_task'){
		
			/**
			 * we are repeating
			 */
				
			$task_name = stripslashes( $strings -> inputClear( $_POST['task_name'], false));
			$task_info = stripslashes( $strings -> inputClear( $_POST['task_info'], false));
			$task_file = stripslashes( $strings -> inputClear( $_POST['task_file'], false));
			
			$task_minutes = $_POST['task_minutes'];
			$task_hours = $_POST['task_hours'];
			$task_days = $_POST['task_days'];
			
			$task_logs = $_POST['task_logs'];
			$task_active = $_POST['task_active'];
			
			settype( $task_minutes, 'integer');
			settype( $task_hours, 'integer');
			settype( $task_days, 'integer');
			
			settype( $task_logs, 'bool');
			settype( $task_active, 'bool');
			
			if ( $task_minutes < 0)
				$task_minutes = 0;
				
			if ( $task_minutes > 59)
				$task_minutes = 59;
			
			if ( $task_hours < 0)
				$task_hours = 0;
				
			if ( $task_hours > 23)
				$task_hours = 23;
				
			if ( $task_days < 0)
				$task_days = 0;
				
		}
		
		/**
		 * draw form
		 */
		
		$new_task_link = array( 'act' => 'add_task');
		
		$new_task_form = new form();
		$new_task_form -> openForm( parent::adminLink( parent::getId(), $new_task_link));
		$new_task_form -> openOpTable();
		
		$new_task_form -> drawTextInput( $language -> getString( 'acp_settings_subsection_manage_tasks_new_name'), 'task_name', $task_name);
		$new_task_form -> drawTextBox( $language -> getString( 'acp_settings_subsection_manage_tasks_new_info'), 'task_info', $task_info);
		
		/**
		 * prepare list of files
		 */
		
		$files_list = glob( ROOT_PATH.'system/tasks/*.php');
		
		foreach ( $files_list as $file_id => $file_path){
			
			$file_path = str_ireplace( ROOT_PATH.'system/tasks/', '', $file_path);
			$clear_files_list[$file_path] = $file_path;
		}
		
		$new_task_form -> drawList( $language -> getString( 'acp_settings_subsection_manage_tasks_new_file'), 'task_file', $clear_files_list, $task_file, $language -> getString( 'acp_settings_subsection_manage_tasks_new_file_help'));
				
		$new_task_form -> closeTable();
		$new_task_form -> drawSpacer( $language -> getString( 'acp_settings_subsection_manage_tasks_new_file_sub_time'));
		$new_task_form -> openOpTable();
				
		$minutes_list = array();
		
		$minute_to_add = 0;
		
		while( $minute_to_add < 60){
			
			$minutes_list[] = $minute_to_add;
			$minute_to_add ++;
			
		}
		
		$new_task_form -> drawList( $language -> getString( 'acp_settings_subsection_manage_tasks_new_minutes'), 'task_minutes', $minutes_list, $task_minutes);
				
		$hours_list = array();
		
		$hours_to_add = 0;
		
		while( $hours_to_add < 24){
			
			$hours_list[] = $hours_to_add;
			$hours_to_add ++;
			
		}
		
		$new_task_form -> drawList( $language -> getString( 'acp_settings_subsection_manage_tasks_new_hours'), 'task_hours', $hours_list, $task_hours);

		$new_task_form -> drawTextInput( $language -> getString( 'acp_settings_subsection_manage_tasks_new_days'), 'task_days', $task_days);
			
		$new_task_form -> closeTable();
		$new_task_form -> drawSpacer( $language -> getString( 'acp_settings_subsection_manage_tasks_new_file_sub_addidtional'));
		$new_task_form -> openOpTable();
				
		$new_task_form -> drawYesNo( $language -> getString( 'acp_settings_subsection_manage_tasks_new_logs'), 'task_logs', $task_logs);
		$new_task_form -> drawYesNo( $language -> getString( 'acp_settings_subsection_manage_tasks_new_active'), 'task_active', $task_active);
		
		$new_task_form -> closeTable();
		$new_task_form -> drawButton( $language -> getString( 'acp_settings_subsection_manage_tasks_add_new_button'));
		$new_task_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_new'), $new_task_form -> display()));
		
	}
	
	function act_edit_task(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * select task from mysql
		 */
		
		if ( isset($_GET['task']) && !empty( $_GET['task'])){
				
			$task_to_edit = $_GET['task'];
			
			settype( $task_to_edit, 'integer');
			
			$task_to_run_query = $mysql -> query( "SELECT * FROM tasks WHERE `task_id` = '$task_to_edit'");
			
			if ( $task_result = mysql_fetch_array( $task_to_run_query, MYSQL_ASSOC)){
				
				$task_result = $mysql -> clear( $task_result);
				
				/**
				 * add breadcrumbs
				 */
				
				$path_link = array( 'act' => 'manage_tasks');
				
				$path -> addBreadcrumb( $language -> getString( 'acp_settings_section_maintenace'), parent::adminLink( parent::getId(), $path_link));		
				$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_manage_tasks'), parent::adminLink( parent::getId(), $path_link));
				
				$path_link = array( 'act' => 'edit_task', 'task' => $task_to_edit);
				
				$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_manage_tasks_edit'), parent::adminLink( parent::getId(), $path_link));
				
				/**
				 * set page title
				 */
				
				$output -> setTitle( $language -> getString( 'acp_settings_subsection_manage_tasks_edit'));
				
				/**
				 * set vars
				 */
				
				$task_name = $task_result['task_title'];
				$task_info = $task_result['task_info'];
				$task_file = $task_result['task_file'];
				
				$task_minutes = $task_result['task_minute'];
				$task_hours = $task_result['task_hour'];
				$task_days = $task_result['task_day'];
				
				$task_logs = $task_result['task_collect_logs'];
				$task_active = $task_result['task_active'];
				
				/**
				 * draw form
				 */
				
				if ( $_GET['act'] == 'change_task'){
				
					/**
					 * we are repeating
					 */
						
					$task_name = stripslashes( $strings -> inputClear( $_POST['task_name'], false));
					$task_info = stripslashes( $strings -> inputClear( $_POST['task_info'], false));
					$task_file = stripslashes( $strings -> inputClear( $_POST['task_file'], false));
					
					$task_minutes = $_POST['task_minutes'];
					$task_hours = $_POST['task_hours'];
					$task_days = $_POST['task_days'];
					
					$task_logs = $_POST['task_logs'];
					$task_active = $_POST['task_active'];
					
					settype( $task_minutes, 'integer');
					settype( $task_hours, 'integer');
					settype( $task_days, 'integer');
					
					settype( $task_logs, 'bool');
					settype( $task_active, 'bool');
					
					if ( $task_minutes < 0)
						$task_minutes = 0;
						
					if ( $task_minutes > 59)
						$task_minutes = 59;
					
					if ( $task_hours < 0)
						$task_hours = 0;
						
					if ( $task_hours > 23)
						$task_hours = 23;
						
					if ( $task_days < 0)
						$task_days = 0;
						
				}
				
				/**
				 * draw form
				 */
				
				$edit_task_link = array( 'act' => 'change_task', 'task' => $task_to_edit);
				
				$edit_task_form = new form();
				$edit_task_form -> openForm( parent::adminLink( parent::getId(), $edit_task_link));
				$edit_task_form -> openOpTable();
				
				$edit_task_form -> drawTextInput( $language -> getString( 'acp_settings_subsection_manage_tasks_new_name'), 'task_name', $task_name);
				$edit_task_form -> drawTextBox( $language -> getString( 'acp_settings_subsection_manage_tasks_new_info'), 'task_info', $task_info);
				
				/**
				 * prepare list of files
				 */
				
				$files_list = glob( ROOT_PATH.'system/tasks/*.php');
				
				foreach ( $files_list as $file_id => $file_path){
					
					$file_path = str_ireplace( ROOT_PATH.'system/tasks/', '', $file_path);
					$clear_files_list[$file_path] = $file_path;
				}
				
				$edit_task_form -> drawList( $language -> getString( 'acp_settings_subsection_manage_tasks_new_file'), 'task_file', $clear_files_list, $task_file, $language -> getString( 'acp_settings_subsection_manage_tasks_new_file_help'));
						
				$edit_task_form -> closeTable();
				$edit_task_form -> drawSpacer( $language -> getString( 'acp_settings_subsection_manage_tasks_new_file_sub_time'));
				$edit_task_form -> openOpTable();
						
				$minutes_list = array();
				
				$minute_to_add = 0;
				
				while( $minute_to_add < 60){
					
					$minutes_list[] = $minute_to_add;
					$minute_to_add ++;
					
				}
				
				$edit_task_form -> drawList( $language -> getString( 'acp_settings_subsection_manage_tasks_new_minutes'), 'task_minutes', $minutes_list, $task_minutes);
						
				$hours_list = array();
				
				$hours_to_add = 0;
				
				while( $hours_to_add < 24){
					
					$hours_list[] = $hours_to_add;
					$hours_to_add ++;
					
				}
				
				$edit_task_form -> drawList( $language -> getString( 'acp_settings_subsection_manage_tasks_new_hours'), 'task_hours', $hours_list, $task_hours);
		
				$edit_task_form -> drawTextInput( $language -> getString( 'acp_settings_subsection_manage_tasks_new_days'), 'task_days', $task_days);
					
				$edit_task_form -> closeTable();
				$edit_task_form -> drawSpacer( $language -> getString( 'acp_settings_subsection_manage_tasks_new_file_sub_addidtional'));
				$edit_task_form -> openOpTable();
						
				$edit_task_form -> drawYesNo( $language -> getString( 'acp_settings_subsection_manage_tasks_new_logs'), 'task_logs', $task_logs);
				$edit_task_form -> drawYesNo( $language -> getString( 'acp_settings_subsection_manage_tasks_new_active'), 'task_active', $task_active);
				
				$edit_task_form -> closeTable();
				$edit_task_form -> drawButton( $language -> getString( 'acp_settings_subsection_manage_tasks_add_new_button'));
				$edit_task_form -> closeForm();
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_edit'), $edit_task_form -> display()));
				
			}else{
				
				/**
				 * task to run not found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_edit'), $language -> getString( 'acp_settings_subsection_manage_tasks_edit_notfound')));
			
				$this -> act_manage_tasks();
				
			}
			
		}else{
			
			/**
			 * task to run not defined
			 */
		
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_edit'), $language -> getString( 'acp_settings_subsection_manage_tasks_edit_notspecified')));
			
			$this -> act_manage_tasks();
		
		}
		
	}
	
	function act_manage_tasks(){

		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'manage_tasks');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_section_maintenace'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_manage_tasks'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_settings_subsection_manage_tasks'));

		/**
		 * if user decided to, run task
		 */
		
		if ( $_GET['do'] == 'run_task'){
			
			if ( isset($_GET['task']) && !empty( $_GET['task'])){
				
				$task_to_run = $_GET['task'];
				
				settype( $task_to_run, 'integer');
				
				$task_to_run_query = $mysql -> query( "SELECT * FROM tasks WHERE `task_id` = '$task_to_run'");
				
				if ( $task_result = mysql_fetch_array( $task_to_run_query, MYSQL_ASSOC)){
					
					$task_result = $mysql -> clear( $task_result);
					
					/**
					 * run task
					 */
					
					include( ROOT_PATH.'system/tasks/'.$task_result['task_file']);	
		
					/**
					 * update
					 */
					
					$correction = ( $task_result['task_minute'] * 60) + ( $task_result['task_hour'] * 3600)  + ( $task_result['task_day'] * 3600 * 24);
						
					$next_run = time() + $correction;
							
					/**
					 * if time delays between next runs are equal to 0, dont change next run
					 */
						
					if( $correction > 0)
						$mysql -> query( 'UPDATE tasks SET `task_next_run` = \''.$next_run.'\' WHERE `task_id` = \''.$task_to_run.'\'');
				
					/**
					 * add log
					 */
					
					if ( $task_result['task_collect_logs'])
						$logs -> addTaskLog( $task_to_run);	
					
					$log_keys = array( 'task_run_id' => $task_to_run);
					
					$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_manage_tasks_run_log'), $log_keys);
			
					/**
					 * clear cache
					 */
					
					$cache -> flushCache( 'system_tasks');
					
					/**
					 * draw message
					 */
					
					parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_run'), $language -> getString( 'acp_settings_subsection_manage_tasks_run_done')));
					
				}else{
					
					/**
					 * task to run not found
					 */
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_run'), $language -> getString( 'acp_settings_subsection_manage_tasks_run_notfound')));
				
				}
				
			}else{
				
				/**
				 * task to run not defined
				 */
			
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_run'), $language -> getString( 'acp_settings_subsection_manage_tasks_run_notspecified')));
					
			}
			
		}
		
		/**
		 * delete task
		 */
		
		if ( $_GET['do'] == 'delete_task'){
			
			if ( isset($_GET['task']) && !empty( $_GET['task'])){
				
				$task_to_run = $_GET['task'];
				
				settype( $task_to_run, 'integer');
				
				$task_to_run_query = $mysql -> query( "SELECT * FROM tasks WHERE `task_id` = '$task_to_run'");
				
				if ( $task_result = mysql_fetch_array( $task_to_run_query, MYSQL_ASSOC)){
					
					/**
					 * kill task
					 */
					
					$mysql -> delete( 'tasks', "`task_id` = '$task_to_run'");
					
					/**
					 * add log
					 */
					
					$log_keys = array( 'task_delete_id' => $task_to_run);
					
					$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_manage_tasks_delete_log'), $log_keys);
			
					/**
					 * clear cache
					 */
					
					$cache -> flushCache( 'system_tasks');
					
					/**
					 * draw message
					 */
					
					parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_delete'), $language -> getString( 'acp_settings_subsection_manage_tasks_delete_done')));
					
				}else{
					
					/**
					 * task to run not found
					 */
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_delete'), $language -> getString( 'acp_settings_subsection_manage_tasks_delete_notfound')));
				
				}
				
			}else{
				
				/**
				 * task to run not defined
				 */
			
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_manage_tasks_delete'), $language -> getString( 'acp_settings_subsection_manage_tasks_delete_notspecified')));
					
			}
			
		}
		
		/**
		 * begin of drawing tasks list
		 */
		
		$new_task_link = array( 'act' => 'new_task');
		
		$tasks_list = new form();
		$tasks_list -> openForm( parent::adminLink( parent::getId(), $new_task_link));
		$tasks_list -> openOpTable();
		$tasks_list -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_settings_subsection_tasks_logs_task').'</th>
			<th>'.$language -> getString( 'acp_settings_subsection_tasks_logs_active').'</th>
			<th>'.$language -> getString( 'acp_settings_subsection_tasks_logs_task_next_time').'</th>
			<th>'.$language -> getString( 'acp_settings_subsection_tasks_logs_miutes').'</th>
			<th>'.$language -> getString( 'acp_settings_subsection_tasks_logs_hours').'</th>
			<th>'.$language -> getString( 'acp_settings_subsection_tasks_logs_days').'</th>
			<th>'.$language -> getString( 'actions').'</th>
		</tr>');
		
		$tasks_query = $mysql -> query( "SELECT * FROM tasks ORDER BY task_next_run");
		
		while ( $tasks_result = mysql_fetch_array( $tasks_query, MYSQL_ASSOC)){
			
			//clear result
			$tasks_result = $mysql -> clear($tasks_result);
			
			/**
			 * draw
			 */
				
			$task_info = '';
			
			if ( !empty( $tasks_result['task_info']))
				$task_info = '<br />'.$tasks_result['task_info'];
			
			$run_task_link = array( 'act' => 'manage_tasks', 'do' => 'run_task', 'task' => $tasks_result['task_id']);
			$edit_task_link = array( 'act' => 'edit_task', 'task' => $tasks_result['task_id']);
			$delete_task_link = array( 'act' => 'manage_tasks', 'do' => 'delete_task', 'task' => $tasks_result['task_id']);
				
			$tasks_list -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%"><a href="'.parent::adminLink( parent::getId(), $run_task_link).'">'.$tasks_result['task_title'].'</a>'.$task_info.'</th>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$style -> drawThick( $tasks_result['task_active'], true).'</td>
				<td class="opt_row1" style="text-align: center" NOWRAP>'.( $tasks_result['task_next_run'] > 0 ? $time -> drawDate( $tasks_result['task_next_run']) : '- -').'</td>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$tasks_result['task_minute'].'</td>
				<td class="opt_row1" style="text-align: center" NOWRAP>'.$tasks_result['task_hour'].'</td>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$tasks_result['task_day'].'</td>
				<td class="opt_row3" style="text-align: center" NOWRAP>
				<a href="'.parent::adminLink( parent::getId(), $edit_task_link).'">'.$style -> drawImage( 'edit', $language -> getString( 'edit')).'</a>
				<a href="'.parent::adminLink( parent::getId(), $delete_task_link).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a></td>
			</tr>');
		}
		
		$tasks_list -> closeTable();
		$tasks_list -> drawButton( $language -> getString( 'acp_settings_subsection_manage_tasks_new_button'));
		$tasks_list -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_manage_tasks'), $tasks_list -> display()));
		
	}
	
	function act_draw_tasks_logs(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'tasks_logs');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_section_maintenace'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_tasks_logs'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_settings_subsection_tasks_logs'));
		
		/**
		 * define page
		 */
		
		$logs_nubmer = $mysql -> countRows( 'tasks_logs');
		
		$pages_number = ceil( $logs_nubmer / 20 );

		if( isset( $_GET['p'])){
			
			$page_to_draw = $_GET['p'];
			settype( $page_to_draw, 'integer');
		
		}else{
			
			$page_to_draw = 1;
			
		}
		
		if ( $page_to_draw > $pages_number){
			
			$page_to_draw = 1;
			
		}
		
		if ( $page_to_draw < 1){
			
			$page_to_draw = 1;
			
		}
		
		$page_to_draw -= 1;
		
		/**
		 * begin drawing
		 */
		
		$tasks_logs_form = new form();
		$tasks_logs_form -> openOpTable();
		$tasks_logs_form -> addToContent( '<tr>
			<th NOWRAP>'.$language -> getString( 'acp_settings_subsection_tasks_logs_task').'</th>
			<th NOWRAP>'.$language -> getString( 'acp_settings_subsection_tasks_logs_task_time').'</th>
			<th NOWRAP>'.$language -> getString( 'acp_settings_subsection_tasks_logs_task_ip').'</th>
		</tr>');
		
		$logs_query = $mysql -> query( "SELECT l.*, t.* FROM tasks_logs l LEFT JOIN tasks t ON l.tasks_log_task = t.task_id ORDER BY tasks_log_time DESC LIMIT ".($page_to_draw * 20).", 20");
		
		while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_ASSOC)){
			
			$logs_result = $mysql -> clear( $logs_result);
						
			$tasks_logs_form -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%">'.$logs_result['task_title'].'</th>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$time -> drawDate( $logs_result['tasks_log_time']).'</td>
				<td class="opt_row1" style="text-align: center" NOWRAP>'.long2ip( $logs_result['tasks_log_ip']).'</td>
			</tr>');
			
		}
		
		$tasks_logs_form -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_tasks_logs'), $tasks_logs_form -> display()));
		
		/**
		 * and paginator now
		 */
		
		$logs_pages_link = array( 'act' => 'tasks_logs');
		
		parent::draw( $style -> drawPaginator( parent::adminLink( parent::getId(), $logs_pages_link), 'p', ceil( $logs_nubmer / 20), ( $page_to_draw + 1)));
		
		
	}
	
	function act_mails_list(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'mails_list');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_section_mailing'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_mails_list'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_settings_subsection_mails_list'));
		
		/**
		 * kill at users desire
		 */
		
		if ( $session -> checkForm()){
			
			$messages_to_delete = $_POST['delete_mail'];
			
			if ( gettype( $messages_to_delete) == 'array'){
				
				foreach ( $messages_to_delete as $message_id => $delete_message){
					
					settype( $message_id, 'integer');
					
					$messages_to_delete_id[] = $message_id;
				}
				
				$mysql -> delete( 'mails', "`mail_id` IN (".join( ",", $messages_to_delete_id).")");
				
			}
			
		}
		
		/**
		 * begin paginating
		 */
		
		$mails_nubmer = $mysql -> countRows( 'mails');
		
		$pages_number = ceil( $mails_nubmer / 20 );
			
		$page_to_draw = $_GET['p'];
		settype( $page_to_draw, 'integer');
				
		if ( $page_to_draw > $pages_number){
			
			$page_to_draw = $pages_number;
			
		}
		
		if ( $page_to_draw < 0){
			
			$page_to_draw = 0;
			
		}
		
		/**
		 * begin drawing form
		 */
		
		$mails_form = new form();
		$mails_form -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'mails_list')));
		$mails_form -> openOpTable();
		$mails_form -> addToContent( '<tr>
			<th NOWRAP="nowrap">'.$language -> getString('acp_settings_subsection_mails_list_mail_subject').'</th>
			<th NOWRAP="nowrap">'.$language -> getString('acp_settings_subsection_mails_list_mail_to_all').'</th>
			<th NOWRAP="nowrap">'.$language -> getString('acp_settings_subsection_mails_list_mail_last_message').'</th>
			<th NOWRAP="nowrap">'.$language -> getString('acp_settings_subsection_mails_list_mail_done').'</th>
			<th NOWRAP="nowrap">&nbsp;</th>
		</tr>');
		
		/**
		 * select mails
		 */
		
		$mails_query = $mysql -> query( "SELECT * FROM mails LIMIT ".($page_to_draw * 20).", 20");
		
		while ( $mails_result = mysql_fetch_array( $mails_query, MYSQL_ASSOC)){
			
			//clear result
			$mails_result = $mysql -> clear( $mails_result);
			
			/**
			 * mail time
			 */
			
			if ( $mails_result['mail_last_time'] == 0){
				$mail_time = $language -> getString( 'acp_settings_subsection_new_mail_last_none');
			}else{
				$mail_time = $time -> drawDate( $mails_result['mail_last_time']);
			}
			
			/**
			 * add row
			 */
		
			$mails_form -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%">'.$mails_result['mail_subject'].'</td>
				<td class="opt_row2" style="text-align: center;" NOWRAP="nowrap">'.$style -> drawThick( $mails_result['mail_toall'], true).'</td>
				<td class="opt_row1" style="text-align: center;" NOWRAP="nowrap">'.$mail_time.'</td>
				<td class="opt_row2" style="text-align: center;" NOWRAP="nowrap">'.$style -> drawThick( $mails_result['mail_done'], true).'</td>
				<td class="opt_row3" style="text-align: center;" NOWRAP="nowrap">'.$mails_form -> drawSelect( 'delete_mail['.$mails_result['mail_id'].']').'</td>
			</tr>');
				
		}
		
		/**
		 * close table
		 */
		
		$mails_form -> closeTable();
		$mails_form -> drawButton( $language -> getString( 'acp_settings_subsection_mails_list_mail_delete'));
		$mails_form -> closeForm();
		
		/**
		 * display it
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_mails_list'), $mails_form -> display()));
		
		/**
		 * and paginator
		 */
		
		$pages_link = array( 'act' => 'mails_list');
		
		parent::draw( $style -> drawPaginator( parent::adminLink( parent::getId(), $pages_link), 'p', ceil( $mails_nubmer / 20), ( $page_to_draw + 1)));
		
	}
	
	function act_new_mail(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'add_mail');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_section_mailing'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_new_mail'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_settings_subsection_new_mail'));
		
		/**
		 * set blank values
		 */
		
		$mail_groups = array();
		$mail_subject = '';
		$mail_text = '';
		$mail_ignore = false;
		
		/**
		 * retake values
		 */
		
		if ( $_GET['act'] == 'add_mail'){
			
			$mail_groups = $_POST['mail_groups'];
			$mail_subject = stripslashes( $strings -> inputClear( $_POST['mail_subject'], false));
			$mail_text = stripslashes( $strings -> inputClear( $_POST['mail_text'], false));
			$mail_ignore = $_POST['mail_to_all'];
			
			settype( $mail_groups, 'array');
			settype( $mail_ignore, 'bool');
			
		}
		
		/**
		 * draw form
		 */
		
		$new_mail_form = new form();
		$new_mail_form -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'add_mail')));
		$new_mail_form -> openOpTable();
		
		/**
		 * select groups
		 */
		
		$groups_query = $mysql -> query( "SELECT * FROM users_groups ORDER BY users_group_name");
		
		while ( $users_groups_result = mysql_fetch_array( $groups_query, MYSQL_ASSOC)){
			
			$users_groups_result = $mysql -> clear( $users_groups_result);
			
			/**
			 * check group
			 */
			
			$users_groups_list[$users_groups_result['users_group_id']] = $users_groups_result['users_group_name'];
			
		}

		$new_mail_form -> drawMultiList( $language -> getString( 'acp_settings_subsection_new_mail_groups'), 'mail_groups[]', $users_groups_list, $mail_groups, $language -> getString( 'acp_settings_subsection_new_mail_groups_help'));
		
		$new_mail_form -> drawTextInput( $language -> getString( 'acp_settings_subsection_new_mail_subject'), 'mail_subject', $mail_subject);
		$new_mail_form -> drawTextBox( $language -> getString( 'acp_settings_subsection_new_mail_text'), 'mail_text', $mail_text);
		$new_mail_form -> drawYesNo( $language -> getString( 'acp_settings_subsection_new_mail_everyone'), 'mail_to_all', $mail_ignore, $language -> getString( 'acp_settings_subsection_new_mail_everyone_help'));
		
		$new_mail_form -> closeTable();
		$new_mail_form -> drawSpacer( $language -> getString( 'acp_settings_subsection_new_mail_keys'));
		$new_mail_form -> openOpTable();
		
		$new_mail_form -> drawInfoRow( '{SITE_NAME}', $language -> getString( 'acp_settings_subsection_new_mail_keys_site_name'));
		$new_mail_form -> drawInfoRow( '{SITE_URL}', $language -> getString( 'acp_settings_subsection_new_mail_keys_site_url'));
		$new_mail_form -> drawInfoRow( '{SITE_POSTS}', $language -> getString( 'acp_settings_subsection_new_mail_keys_site_posts'));
		$new_mail_form -> drawInfoRow( '{SITE_TOPICS}', $language -> getString( 'acp_settings_subsection_new_mail_keys_site_topics'));
		$new_mail_form -> drawInfoRow( '{SITE_USERS}', $language -> getString( 'acp_settings_subsection_new_mail_keys_site_users'));
		$new_mail_form -> drawInfoRow( '{USER_NAME}', $language -> getString( 'acp_settings_subsection_new_mail_keys_user_name'));
		$new_mail_form -> drawInfoRow( '{USER_POSTS}', $language -> getString( 'acp_settings_subsection_new_mail_keys_user_posts'));
		
		$new_mail_form -> closeTable();
		$new_mail_form -> drawButton( $language -> getString( 'acp_settings_subsection_new_mail_button'));
		$new_mail_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_new_mail'), $new_mail_form -> display()));
		
	}
	
	/**
	 * PLUGINS
	 */
	
	function act_plugins_mgr(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'plugins');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_section_plugins'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_plugins'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_settings_subsection_plugins'));
		
		/**
		 * beign manager
		 */
		
		parent::draw('TODO: manager code there!');
		
	}
	
	function act_plugins_install(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'plugins_install');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_section_plugins'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_plugins_install'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_settings_subsection_plugins_install'));
		
		/**
		 * select installed plugins
		 */
		
		$installed_plugins = new form();
		$installed_plugins -> openOpTable();
		$installed_plugins -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_settings_subsection_plugins_install_found_name').'</th>
			<th>'.$language -> getString( 'actions').'</th>
		</tr>');
		
		/**
		 * select plugins
		 */
		
		$plugins_query = $mysql -> query( "SELECT * FROM plugins ORDER BY plugin_prior");
	
		$installed_plugins_paths = array();
		
		while ( $plugin_result = mysql_fetch_array( $plugins_query, MYSQL_ASSOC)) {
			
			//clear result
			$plugin_result = $mysql -> clear( $plugin_result);
			
		}
		
		/**
		 * close table
		 */
		
		$installed_plugins -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_plugins_install_found'), $installed_plugins -> display()));
		
		/**
		 * draw avaiable plugins
		 */
		
		$plugins_installs = glob( ROOT_PATH."plugins/*/install.xml");
		
		settype( $plugins_installs, 'array');
		
		$found_list = new form();
		$found_list -> openOpTable();
		$found_list -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_settings_subsection_plugins_install_found_name').'</th>
			<th>'.$language -> getString( 'actions').'</th>
		</tr>');
		
		/**
		 * select plugins
		 */
		
		foreach ( $plugins_installs as $plugin_full_path){
			
			/**
			 * create clear path
			 */
			
			$clear_path = str_ireplace( ROOT_PATH."plugins/", '', $plugin_full_path);
			$clear_path = str_ireplace( "/install.xml", '', $clear_path);
			
			/**
			 * load plugin install xml
			 */
			
			$plugin_xml = file_get_contents( $plugin_full_path);
			
			$plugin_xml = new SimpleXMLElement( $plugin_xml);
			
			/**
			 * plugin info
			 */
			
			if ( strlen( $plugin_xml -> description) > 0){
			
				$plugin_info = '<br />'.$plugin_xml -> description;
			
			}else{
				
				$plugin_info = '';
				
			}
			
			/**
			 * add row
			 */
			
			$found_list -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%"><b>'.$plugin_xml -> name.'</b>'.$plugin_info.'</td>
				<td class="opt_row3" style="text-align: center;" nowrap="nowrap"><a href="'.parent::adminLink( parent::getId(), array('act' => 'install_plugin', 'plugin' => $clear_path)).'">'.$language -> getString( 'acp_settings_subsection_plugins_install_found_install').'</a></td>
			</tr>');
			
		}
		
		/**
		 * close table
		 */
		
		$found_list -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_plugins_install_avieable'), $found_list -> display()));
				
	}
	
	function act_install_plugin(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * get path to install
		 */
		
		$path_to_load = trim( $_GET['plugin']);
		
		/**
		 * check if path is okey
		 */
		
		if ( file_exists( ROOT_PATH.'plugins/'.$path_to_load.'/install.xml')){
			
			/**
			 * check if path is installed
			 */
			
			$path_to_sql = $strings -> inputClear( $_GET['plugin'], false);
			
			$plugin_check = $mysql -> query( "SELECT * FROM plugins WHERE `plugin_path` = '$path_to_sql'");
			
			if ( $plugin_found = mysql_fetch_array( $plugin_check, MYSQL_ASSOC)){
				
				/**
				 * plugin found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_plugins_installator'), $language -> getString( 'acp_settings_subsection_plugins_installator_already_done')));
				
				$this -> act_plugins_install();
			
			}else{
				
				/**
				 * add breadcrumbs
				 */
				
				$path_link = array( 'act' => 'plugins_install');
				
				$path -> addBreadcrumb( $language -> getString( 'acp_settings_section_plugins'), parent::adminLink( parent::getId(), $path_link));		
				$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_plugins_install'), parent::adminLink( parent::getId(), $path_link));
				
				$path_link = array( 'act' => 'plugins_install');
				
				$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_plugins_installator'), parent::adminLink( parent::getId(), $path_link));
				
				/**
				 * set page title
				 */
				
				$output -> setTitle( $language -> getString( 'acp_settings_subsection_plugins_installator'));
				
				/**
				 * load plugin data
				 */
				
				$plugin_xml = file_get_contents( ROOT_PATH.'plugins/'.$path_to_load.'/install.xml');
			
				$plugin_xml = new SimpleXMLElement( $plugin_xml);
			
				$plugin_name = stripslashes( $strings -> inputClear( $plugin_xml -> name, false));
				$plugin_info = stripslashes( $strings -> inputClear( $plugin_xml -> description, false));
				
				if ( isset( $plugin_xml -> settings_group)){
				
					$plugin_group_name = stripslashes( $strings -> inputClear( $plugin_xml -> settings_group -> title, false));
					$plugin_group_info = stripslashes( $strings -> inputClear( $plugin_xml -> settings_group -> description, false));
					
				}
				
				/**
				 * begin drawing form
				 */
				
				$inst_form = new form();
				$inst_form -> openForm( parent::adminLink( parent::getId(), array('act' => 'add_plugin')));
				$inst_form -> openOpTable();
				
				$inst_form -> drawTextInput( $language -> getString( 'acp_settings_subsection_plugins_installator_plugin_name'), 'plugin_name', $plugin_name);
				$inst_form -> drawTextBox( $language -> getString( 'acp_settings_subsection_plugins_installator_plugin_info'), 'plugin_info', $plugin_info);
				$inst_form -> drawInfoRow( $language -> getString( 'acp_settings_subsection_plugins_installator_plugin_class'), $plugin_xml -> class_name);
				
				$inst_form -> closeTable();
				
				if ( isset( $plugin_xml -> settings_group)){
					
					$inst_form -> drawSpacer( $language -> getString( 'acp_settings_subsection_plugins_installator_sub_setts_group'));
					$inst_form -> openOpTable();
					
					$inst_form -> drawTextInput( $language -> getString( 'acp_settings_subsection_plugins_installator_sub_setts_group_name'), 'plugin_group_name', $plugin_group_name);
					$inst_form -> drawTextBox( $language -> getString( 'acp_settings_subsection_plugins_installator_sub_setts_group_info'), 'plugin_group_info', $plugin_group_info);
					
					$inst_form -> closeTable();
					
				}
				
				$inst_form -> drawButton( $language -> getString( 'acp_settings_subsection_plugins_installator_install_plugin'));
				$inst_form -> closeForm();
				
				/**
				 * draw form
				 */
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_plugins_installator'), $inst_form -> display()));
				
			}
				
		}else{
			
			/**
			 * path not found
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_plugins_installator'), $language -> getString( 'acp_settings_subsection_plugins_installator_notfound')));
			
			$this -> act_plugins_install();
			
		}
		
	}
	
}
	
?>