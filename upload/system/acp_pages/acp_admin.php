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
|	Acp Admin Page
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class acp_section_admin extends acp_section{
	
	function __construct(){
				
		/**
		 * include global classes pointers
		 */
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * begin
		 */
		
		$correct_acts = array( 'version_check', 'secruity_summary', 'secruity_admins_list', 'logs_admins', 'logs_mods', 'logs_emails', 'logs_bots', 'logs_bots', 'logs_logins', 'stats_registers', 'stats_topics', 'stats_posts', 'stats_pms', 'stats_topics_views', 'mysql_query');
		
		if ( !isset( $_GET['act']) || !in_array( $_GET['act'], $correct_acts)){
			$current_act = 	$correct_acts[0];
		}else{
			$current_act = $_GET['act'];
		}
		
		/**
		 * now array containing subsections
		 */
		
		$subsections_list['version'] = 'version';
		$subsections_list['secruity'] = 'secruity';
		$subsections_list['logs'] = 'logs';
		$subsections_list['statistics'] = 'statistics';
		
		if ( $session -> user['user_is_root'])
			$subsections_list['mysql'] = 'mysql';
		
		/**
		 * and subsections list
		 */
		
		$subsections_elements_list['version_check'] = 'version';
		
		$subsections_elements_list['secruity_summary'] = 'secruity';
		$subsections_elements_list['secruity_admins_list'] = 'secruity';
		
		$subsections_elements_list['logs_admins'] = 'logs';
		$subsections_elements_list['logs_mods'] = 'logs';
		$subsections_elements_list['logs_emails'] = 'logs';
		$subsections_elements_list['logs_bots'] = 'logs';
		$subsections_elements_list['logs_logins'] = 'logs';
		
		$subsections_elements_list['stats_registers'] = 'statistics';
		$subsections_elements_list['stats_topics'] = 'statistics';
		$subsections_elements_list['stats_posts'] = 'statistics';
		
		if ( $session -> user['user_is_root'])
			$subsections_elements_list['mysql_query'] = 'mysql';
		
		/**
		 * draw left-side menu
		 */
		
		parent::drawSubSections( $subsections_list, $subsections_elements_list);
		
		/**
		 * do act
		 */
		
		switch ($current_act){
			
			case 'version_check':
				
				/**
				 * check update
				 */
				
				$this -> act_version_check();
								
			break;
			
			case 'secruity_summary':
							
				/**
				 * run function drawing secruiuty summary
				 */
				
				$this -> act_secruity_summary();
				
			break;
			
			case 'secruity_admins_list':
			
				/**
				 * run function drawing admins list
				 */
				
				$this -> act_admins_list();
				
			break;
					
			case 'logs_admins':
				
				/**
				 * run function drawing admins logs
				 */
				
				$this -> act_admins_logs_list();
				
			break;
			
			case 'logs_mods':
				
				/**
				 * run mods logs
				 */
			
				$this -> act_mods_logs_list();
				
			break;
			
			case 'logs_emails':
				
				/**
				 * run mails logs
				 */
			
				$this -> act_mails_logs_list();
				
			break;
			
			case 'logs_bots':
				
				/**
				 * run mails logs
				 */
			
				$this -> act_bots_logs_list();
				
			break;
			
			case 'logs_logins':
			
				/**
				 * run function drawing acp logins logs
				 */
				
				$this -> act_acp_logins_logs_list();
				
			break;
			
			case 'stats_registers':
				
				/**
				 * registrations stats
				 */
				
				$this -> act_stats_registers();
				
			break;
			
			case 'stats_topics':
				
				/**
				 * new topics stats
				 */
				
				$this -> act_stats_topics();
				
			break;
			
			case 'stats_posts':
				
				/**
				 * posting stats
				 */
				
				$this -> act_stats_posts();
				
			break;
			
			case 'mysql_query':
			
				/**
				 * mysql query form
				 */
				
			if ( $session -> user['user_is_root']){
				
				$this -> act_mysql_query();
			
			}else{
				
				$this -> act_secruity_summary();
			
			}
			
			break;
				
		}
		
	}	
	
	function act_version_check(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'version_check');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_section_version'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_version_check'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_admin_subsection_version_check'));

		/**
		 * check
		 */
		
		if (ini_get('allow_url_fopen') == '1') {
	
			$callisto_version_file = fopen( 'http://unisolutions.pl/uni_check.php', 'r');
			
			if ( $callisto_version_file == false){
				
				$callisto_file_open = file_get_contents( 'http://unisolutions.pl/uni_check.php');
				
				if ( $callisto_file_open == false){
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_admin_subsection_version_check_not_enabled'), $language -> getString( 'acp_admin_subsection_version_check_not_enabled_info')));
								
				}else{
					
					$callisto_version_name = substr( $callisto_file_open, strpos( $callisto_file_open, '<version>') + 9);
					$callisto_version_name = substr( $callisto_version_name, 0, strpos( $callisto_version_name, '</version>'));
					
					$callisto_version_id = substr( $callisto_file_open, strpos( $callisto_file_open, '<version_long>') + 14);
					$callisto_version_id = substr( $callisto_version_id, 0, strpos( $callisto_version_id, '</version_long>'));
					
					/**
					 * do compare
					 */
					
					if ( $callisto_version_id > UNI_VER_LONG){
						
						$language -> setKey( 'actual_call_ver', UNI_VER);
						$language -> setKey( 'actual_call_id', UNI_VER_LONG);
						$language -> setKey( 'call_new_ver', $callisto_version_name);
						$language -> setKey( 'call_new_id', $callisto_version_id);
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_admin_subsection_version_check_error'), $language -> getString( 'acp_admin_subsection_version_check_error_info')));
						
					}else{
						
						parent::draw( $style -> drawBlock( $language -> getString( 'acp_admin_subsection_version_check_ok'), $language -> getString( 'acp_admin_subsection_version_check_ok_info')));
						
					}
					
				}
				
			}else{
				
				/**
				 * we got data
				 */
				
				$callisto_version = fread( $callisto_version_file, 1024);
				
				$callisto_version_name = substr( $callisto_version, strpos( $callisto_version, '<version>') + 9);
				$callisto_version_name = substr( $callisto_version_name, 0, strpos( $callisto_version_name, '</version>'));
				
				$callisto_version_id = substr( $callisto_version, strpos( $callisto_version, '<version_long>') + 14);
				$callisto_version_id = substr( $callisto_version_id, 0, strpos( $callisto_version_id, '</version_long>'));
				
				/**
				 * do compare
				 */
				
				if ( $callisto_version_id > UNI_VER_LONG){
					
					$language -> setKey( 'actual_call_ver', UNI_VER);
					$language -> setKey( 'actual_call_id', UNI_VER_LONG);
					$language -> setKey( 'call_new_ver', $callisto_version_name);
					$language -> setKey( 'call_new_id', $callisto_version_id);
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_admin_subsection_version_check_error'), $language -> getString( 'acp_admin_subsection_version_check_error_info')));
					
				}else{
					
					parent::draw( $style -> drawBlock( $language -> getString( 'acp_admin_subsection_version_check_ok'), $language -> getString( 'acp_admin_subsection_version_check_ok_info')));
					
				}
				
			}
			
		} else {
		   
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_admin_subsection_version_check_not_enabled'), $language -> getString( 'acp_admin_subsection_version_check_not_enabled_info')));
			
		}
			
	}
	
	function act_secruity_summary(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'secruity_summary');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_section_secruity'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_secruity_summary'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_admin_subsection_secruity_summary'));
		
		/**
		 * begin drawing form
		 */
		
		$secruity_raport = new form();
		
		/**
		 * acp localisation
		 */
		
		$secruity_raport -> drawSpacer( $language -> getString( 'acp_admin_subsection_secruity_summary_acp_path'));
		$secruity_raport -> openOpTable();
				
		$acp_path_notstandard = true;
		
		if ( ACP_PATH == 'admin/')
			$acp_path_notstandard = false;
		
		$secruity_raport -> drawInfoRow( $language -> getString( 'acp_admin_subsection_secruity_summary_state'), $style -> drawThick( $acp_path_notstandard, true));
		
		if ( !$acp_path_notstandard)
			$secruity_raport -> drawRow( $language -> getString( 'acp_admin_subsection_secruity_summary_acp_path_info'));
		
		$secruity_raport -> closeTable();
		
		/**
		 * acp localisation
		 */
		
		$secruity_raport -> drawSpacer( $language -> getString( 'acp_admin_subsection_secruity_summary_acp_link_draw'));
		$secruity_raport -> openOpTable();
						
		$secruity_raport -> drawInfoRow( $language -> getString( 'acp_admin_subsection_secruity_summary_state'), $style -> drawThick( !$settings['board_show_acp_link'], true));
		
		$show_acp_setting_link = array( 'act' => 'show_group', 'group' => 9);
		
		if ( $settings['board_show_acp_link'])
			$secruity_raport -> drawRow( $language -> getString( 'acp_admin_subsection_secruity_summary_acp_link_draw_info'));
		
		$secruity_raport -> closeTable();
		
		/**
		 * display it
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_secruity_summary'), $secruity_raport -> display()));
		
	}
	
	function act_admins_list(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'secruity_admins_list');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_section_secruity'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_secruity_admins_list'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_admin_subsection_secruity_admins_list'));
		
		/**
		 * begin drawing list
		 */
		
		$admins_list = new form();
		$admins_list -> openOpTable();
		$admins_list -> addToContent( '<tr>
			<th NOWRAP>'.$language -> getString( 'user_username').'</th>
			<th NOWRAP>'.$language -> getString( 'user_group').'</th>
			<th NOWRAP>'.$language -> getString( 'user_groups').'</th>
			<th NOWRAP>'.$language -> getString( 'user_mail').'</th>
			<th NOWRAP>'.$language -> getString( 'user_registration').'</th>
			<th NOWRAP>'.$language -> getString( 'user_posts').'</th>
		</tr>');
		
		/**
		 * build an list of users groups
		 */
		
		$users_groups_query = $mysql -> query( "SELECT * FROM users_groups WHERE `users_group_can_use_acp` = '1' ORDER BY users_group_name");
		
		while ( $users_groups_result = mysql_fetch_array( $users_groups_query, MYSQL_ASSOC)){
			
			$users_groups_result = $mysql -> clear( $users_groups_result);
			
			$users_groups[$users_groups_result['users_group_id']] = $users_groups_result['users_group_prefix'].$users_groups_result['users_group_name'].$users_groups_result['users_group_suffix'];
			
			/**
			 * add to acp groups array
			 */
		
			$acp_groups[] = $users_groups_result['users_group_id'];
				
		}
		
		/**
		 * now users with those groups
		 */
		
		$users_query = $mysql -> query( "SELECT * FROM users WHERE `user_main_group` IN (".join( ",", $acp_groups).")");
		
		while ( $users_result = mysql_fetch_array( $users_query, MYSQL_ASSOC)){
			
			$users_result = $mysql -> clear( $users_result);
			
			$user_sec_groups = array();
			$user_sec_groups_id = array();
			
			$user_sec_groups_id = split( ',', $users_result['user_other_groups']);

			foreach ( $user_sec_groups_id as $sec_group_id){

				$user_sec_groups[] = $users_groups[$sec_group_id];
				
			}
			
			$user_profile_link = array( 'user' => $users_result['user_id']);
						
			$admins_list -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%"><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$users_result['user_login'].'</a></td>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$users_groups[$users_result['user_main_group']].'</td>
				<td class="opt_row1" style="text-align: center" NOWRAP>'.join( "<br />", $user_sec_groups).'</td>
				<td class="opt_row2" style="text-align: center" NOWRAP><a href="mailto:'.$users_result['user_posts_num'].'">'.$users_result['user_mail'].'</a></td>
				<td class="opt_row1" style="text-align: center" NOWRAP>'.$time -> drawDate( $users_result['user_regdate']).'</td>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$users_result['user_posts_num'].'</td>
			</tr>');
		
		}
		
		/**
		 * close table
		 */
		
		$admins_list -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_secruity_admins_list'), $admins_list -> display()));
		
	}
	
	function act_admins_logs_list(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'logs_admins');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_section_logs'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_logs_admins'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_admin_subsection_logs_admins'));
		
		if ( $_GET['do'] == 'show_user_logs' && isset( $_GET['user'])){
			
			/**
			 * draw logs of exact user
			 */
			
			$user_logs_to_show = $_GET['user'];
			
			settype( $user_logs_to_show, 'integer');
			
			/**
			 * add breadcrumb
			 */
			
			$path_link = array( 'act' => 'logs_admins', 'do' => 'show_user_logs', 'user' => $user_logs_to_show);
			
			$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_logs_admins_saved_logs'), parent::adminLink( parent::getId(), $path_link));
		
			/**
			 * set page title
			 */
			
			$output -> setTitle( $language -> getString( 'acp_admin_subsection_logs_admins_saved_logs'));
					
			/**
			 * define page
			 */
			
			$logs_nubmer = $mysql -> countRows( 'admins_logs', "`admins_log_user_id` = '$user_logs_to_show'");
			
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
			
			$user_logs_form = new form();
			$user_logs_form -> openOpTable();
			$user_logs_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'user_username').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_type').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_time').'</th>
				<th>'.$language -> getString( 'user_ip').'</th>
			</tr>');
			
			$logs_query = $mysql -> query( "SELECT l.*, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM admins_logs l LEFT JOIN users u ON l.admins_log_user_id = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE `admins_log_user_id` = '$user_logs_to_show' ORDER BY admins_log_time DESC LIMIT ".($page_to_draw * 20).", 20");
			
			while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_ASSOC)){
				
				$logs_result = $mysql -> clear( $logs_result);
				
				$user_profile_link = array( 'user' => $logs_result['user_id']);
				
				$user_logs_form -> addToContent( '<tr>
					<td class="opt_row1" NOWRAP><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result['users_group_prefix'].$logs_result['user_login'].$logs_result['users_group_suffix'].'</a></th>
					<td class="opt_row2" style="width: 100%">'.$logs_result['admins_log_details'].'</td>
					<td class="opt_row1" style="text-align: center" NOWRAP>'.$time -> drawDate( $logs_result['admins_log_time']).'</td>
					<td class="opt_row2" style="text-align: center" NOWRAP>'.long2ip( $logs_result['admins_log_user_ip']).'</td>
				</tr>');
				
			}
			
			$user_logs_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_logs_admins_saved_logs'), $user_logs_form -> display()));
			
			/**
			 * and paginator now
			 */
			
			$logs_pages_link = array( 'act' => 'logs_admins', 'do' => 'show_user_logs', 'user' => $user_logs_to_show);
			
			parent::draw( $style -> drawPaginator( parent::adminLink( parent::getId(), $logs_pages_link), 'p', ceil( $logs_nubmer / 20), ( $page_to_draw + 1)));
			
		}else if ($_GET['do'] == 'search_logs' && (isset( $_POST['search_phrase']) || isset( $_GET['search_phrase'])) && (strlen( trim($_POST['search_phrase'])) > 0 || strlen( trim($_GET['search_phrase'])) > 0)) {
			
			/**
			 * build list to search
			 * start from phrase
			 */
			
			if ( isset($_POST['search_phrase'])){
			
				$phrase_to_search = $_POST['search_phrase'];
					
			}else{
				
				$phrase_to_search = $_GET['search_phrase'];
				
			}
			
			$phrase_to_search = uniSlashes( htmlspecialchars( trim( $phrase_to_search)));
						
			/**
			 * now where we have to search
			 */
			
			$proper_search_types = array( 0, 1, 2);
			
			if ( isset( $_POST['search_type']) && in_array( $_POST['search_type'], $proper_search_types)){
				
				$search_type = $_POST['search_type'];
			
			}else if ( isset( $_GET['search_type']) && in_array( $_GET['search_type'], $proper_search_types)){
				
				$search_type = $_GET['search_type'];
				
			}else{
				
				$search_type = 0;
				
			}
			
			$count_condition[0] = "admins_log_details";
			$count_condition[2] = "admins_log_user_ip";
			
			$select_condition[0] = "l.admins_log_details";
			$select_condition[2] = "l.admins_log_user_ip";
			
			/**
			 * add breadcrumb
			 */
			
			$path_link = array( 'act' => 'logs_admins', 'do' => 'search_logs', 'search_phrase' => urlencode($phrase_to_search), 'search_type' => $search_type);
			
			$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs'), parent::adminLink( parent::getId(), $path_link));
		
			/**
			 * set page title
			 */
			
			$output -> setTitle( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs'));
				
			/**
			 * define page
			 */
			
			$logs_nubmer = $mysql -> countRows( 'admins_logs', $count_condition[$search_type]." LIKE '%$phrase_to_search%'");
			
			$pages_number = floor( $logs_nubmer / 20 );
			
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
			
			$user_logs_form = new form();
			$user_logs_form -> openOpTable();
			$user_logs_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'user_username').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_type').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_time').'</th>
				<th>'.$language -> getString( 'user_ip').'</th>
			</tr>');
			
			$logs_query = $mysql -> query( "SELECT l.*, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM admins_logs l LEFT JOIN users u ON l.admins_log_user_id = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE ".$select_condition[$search_type]." LIKE '%$phrase_to_search%' ORDER BY admins_log_time DESC LIMIT ".($page_to_draw * 20).", 20");
			
			while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_ASSOC)){
				
				$logs_result = $mysql -> clear( $logs_result);
				
				$user_profile_link = array( 'user' => $logs_result['user_id']);
				
				$user_logs_form -> addToContent( '<tr>
					<td class="opt_row1" NOWRAP><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result['users_group_prefix'].$logs_result['user_login'].$logs_result['users_group_suffix'].'</a></th>
					<td class="opt_row2" style="width: 100%">'.$logs_result['admins_log_details'].'</td>
					<td class="opt_row1" style="text-align: center" NOWRAP>'.$time -> drawDate( $logs_result['admins_log_time']).'</td>
					<td class="opt_row2" style="text-align: center" NOWRAP>'.long2ip( $logs_result['admins_log_user_ip']).'</td>
				</tr>');
				
			}
			
			$user_logs_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_logs_admins_saved_logs'), $user_logs_form -> display()));
			
			/**
			 * and paginator now
			 */
			
			$logs_pages_link = array( 'act' => 'logs_admins', 'do' => 'search_logs', 'search_phrase' => urlencode($phrase_to_search), 'search_type' => $search_type);
						
			parent::draw( $style -> drawPaginator( parent::adminLink( parent::getId(), $logs_pages_link), 'p', floor( $logs_nubmer / 20), ( $page_to_draw + 1)));
			
			
		}else{
		
			if ( $_GET['do'] == 'search_logs')
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'error'), $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_empty_phrase')));
			
			/**
			 * draw last 5 admins actions
			 */
			
			$last_logs_form = new form();
			$last_logs_form -> openOpTable();
			$last_logs_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'user_username').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_type').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_time').'</th>
				<th>'.$language -> getString( 'user_ip').'</th>
			</tr>');
			
			$logs_query = $mysql -> query( "SELECT l.*, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM admins_logs l LEFT JOIN users u ON l.admins_log_user_id = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id ORDER BY admins_log_time DESC LIMIT 5");
			
			while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_ASSOC)){
				
				$logs_result = $mysql -> clear( $logs_result);
				
				$user_profile_link = array( 'user' => $logs_result['user_id']);
				
				$last_logs_form -> addToContent( '<tr>
					<td class="opt_row1" NOWRAP><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result['users_group_prefix'].$logs_result['user_login'].$logs_result['users_group_suffix'].'</a></th>
					<td class="opt_row2" style="width: 100%">'.$logs_result['admins_log_details'].'</td>
					<td class="opt_row1" style="text-align: center" NOWRAP>'.$time -> drawDate( $logs_result['admins_log_time']).'</td>
					<td class="opt_row2" style="text-align: center" NOWRAP>'.long2ip( $logs_result['admins_log_user_ip']).'</td>
				</tr>');
				
			}
			
			$last_logs_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_logs_admins_last_5'), $last_logs_form -> display()));
			
			/**
			 * now admins and their logs browser
			 */
			
			$users_logs_form = new form();
			$users_logs_form -> openOpTable();
			$users_logs_form -> addToContent( '<tr>
				<th NOWRAP>'.$language -> getString( 'user_username').'</th>
				<th NOWRAP>'.$language -> getString( 'acp_admin_subsection_logs_admins_saved_logs_logs_num').'</th>
				<th NOWRAP>'.$language -> getString( 'actions').'</th>
			</tr>');
			
			$logs_query = $mysql -> query( "SELECT l.*, COUNT(*) as logs_num, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM admins_logs l LEFT JOIN users u ON l.admins_log_user_id = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id GROUP BY l.admins_log_user_id  ORDER BY admins_log_time DESC LIMIT 5");
			
			while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_ASSOC)){
				
				$logs_result = $mysql -> clear( $logs_result);
				
				$user_profile_link = array( 'user' => $logs_result['user_id']);
				$user_logs_link = array( 'act' => 'logs_admins', 'do' => 'show_user_logs', 'user' => $logs_result['user_id']);
				
				$users_logs_form -> addToContent( '<tr>
					<td class="opt_row1" style="width: 100%"><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result['users_group_prefix'].$logs_result['user_login'].$logs_result['users_group_suffix'].'</a></th>
					<td class="opt_row2" style="text-align: center" NOWRAP>'.$logs_result['logs_num'].'</td>
					<td class="opt_row3" style="text-align: center" NOWRAP><a href="'.parent::adminLink( parent::getId(), $user_logs_link).'">'.$language -> getString( 'acp_admin_subsection_logs_admins_saved_logs_member_logs_show').'</a></td>
				</tr>');
				
			}
			
			$users_logs_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_logs_admins_saved_logs'), $users_logs_form -> display()));
			
			/**
			 * searching function
			 */
				
			$search_url = array( 'act' => 'logs_admins', 'do' => 'search_logs');
			
			$search_form = new form();
			$search_form -> openForm( parent::adminLink( parent::getId(), $search_url));
			$search_form -> openOpTable();
			
			$search_form -> drawTextInput( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_by'), 'search_phrase');
			
			$searchs_types[0] = $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_in_0');
			//$searchs_types[1] = $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_in_1');
			$searchs_types[2] = $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_in_2');
			
			$search_form -> drawList( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_in'), 'search_type', $searchs_types);
			
			$search_form -> closeTable();
			$search_form -> drawButton( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_button'), 'search_logs');
			$search_form -> closeForm();			
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs'), $search_form -> display()));

			
					
		}
		
	}
	
	function act_mods_logs_list(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'logs_mods');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_section_logs'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_logs_mods'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_admin_subsection_logs_mods'));
		
		if ( $_GET['do'] == 'show_user_logs' && isset( $_GET['user'])){
			
			/**
			 * draw logs of exact user
			 */
			
			$user_logs_to_show = $_GET['user'];
			
			settype( $user_logs_to_show, 'integer');
			
			/**
			 * add breadcrumb
			 */
			
			$path_link = array( 'act' => 'logs_mods', 'do' => 'show_user_logs', 'user' => $user_logs_to_show);
			
			$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_logs_mods_saved_logs'), parent::adminLink( parent::getId(), $path_link));
		
			/**
			 * set page title
			 */
			
			$output -> setTitle( $language -> getString( 'acp_admin_subsection_logs_mods_saved_logs'));
					
			/**
			 * define page
			 */
			
			$logs_nubmer = $mysql -> countRows( 'moderators_logs', "`moderators_log_user_id` = '$user_logs_to_show'");
			
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
			
			$user_logs_form = new form();
			$user_logs_form -> openOpTable();
			$user_logs_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'user_username').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_type').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_time').'</th>
				<th>'.$language -> getString( 'user_ip').'</th>
			</tr>');
			
			$logs_query = $mysql -> query( "SELECT l.*, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM moderators_logs l LEFT JOIN users u ON l.moderators_log_user_id = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE `moderators_log_user_id` = '$user_logs_to_show' ORDER BY moderators_log_time DESC LIMIT ".($page_to_draw * 20).", 20");
			
			while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_ASSOC)){
				
				$logs_result = $mysql -> clear( $logs_result);
				
				$user_profile_link = array( 'user' => $logs_result['user_id']);
				
				$user_logs_form -> addToContent( '<tr>
					<td class="opt_row1" NOWRAP><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result['users_group_prefix'].$logs_result['user_login'].$logs_result['users_group_suffix'].'</a></th>
					<td class="opt_row2" style="width: 100%">'.$logs_result['moderators_log_details'].'</td>
					<td class="opt_row1" style="text-align: center" NOWRAP>'.$time -> drawDate( $logs_result['moderators_log_time']).'</td>
					<td class="opt_row2" style="text-align: center" NOWRAP>'.long2ip( $logs_result['moderators_log_user_ip']).'</td>
				</tr>');
				
			}
			
			$user_logs_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_logs_mods_saved_logs'), $user_logs_form -> display()));
			
			/**
			 * and paginator now
			 */
			
			$logs_pages_link = array( 'act' => 'logs_mods', 'do' => 'show_user_logs', 'user' => $user_logs_to_show);
			
			parent::draw( $style -> drawPaginator( parent::adminLink( parent::getId(), $logs_pages_link), 'p', ceil( $logs_nubmer / 20), ( $page_to_draw + 1)));
			
		}else if ($_GET['do'] == 'search_logs' && (isset( $_POST['search_phrase']) || isset( $_GET['search_phrase'])) && (strlen( trim($_POST['search_phrase'])) > 0 || strlen( trim($_GET['search_phrase'])) > 0)) {
			
			/**
			 * build list to search
			 * start from phrase
			 */
			
			if ( isset($_POST['search_phrase'])){
			
				$phrase_to_search = $_POST['search_phrase'];
					
			}else{
				
				$phrase_to_search = $_GET['search_phrase'];
				
			}
			
			$phrase_to_search = uniSlashes( htmlspecialchars( trim( $phrase_to_search)));
						
			/**
			 * now where we have to search
			 */
			
			$proper_search_types = array( 0, 1, 2);
			
			if ( isset( $_POST['search_type']) && in_array( $_POST['search_type'], $proper_search_types)){
				
				$search_type = $_POST['search_type'];
			
			}else if ( isset( $_GET['search_type']) && in_array( $_GET['search_type'], $proper_search_types)){
				
				$search_type = $_GET['search_type'];
				
			}else{
				
				$search_type = 0;
				
			}
			
			$count_condition[0] = "moderators_log_details";
			$count_condition[2] = "moderators_log_user_ip";
			
			$select_condition[0] = "l.moderators_log_user_ip";
			$select_condition[2] = "l.admins_log_user_ip";
			
			/**
			 * add breadcrumb
			 */
			
			$path_link = array( 'act' => 'logs_mods', 'do' => 'search_logs', 'search_phrase' => urlencode($phrase_to_search), 'search_type' => $search_type);
			
			$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs'), parent::adminLink( parent::getId(), $path_link));
		
			/**
			 * set page title
			 */
			
			$output -> setTitle( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs'));
				
			/**
			 * define page
			 */
			
			$logs_nubmer = $mysql -> countRows( 'moderators_logs', $count_condition[$search_type]." LIKE '%$phrase_to_search%'");
			
			$pages_number = floor( $logs_nubmer / 20 );
			
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
			
			$user_logs_form = new form();
			$user_logs_form -> openOpTable();
			$user_logs_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'user_username').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_type').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_time').'</th>
				<th>'.$language -> getString( 'user_ip').'</th>
			</tr>');
			
			$logs_query = $mysql -> query( "SELECT l.*, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM  moderators_logs l LEFT JOIN users u ON l.moderators_log_user_id = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id WHERE ".$select_condition[$search_type]." LIKE '%$phrase_to_search%' ORDER BY moderators_log_time DESC LIMIT ".($page_to_draw * 20).", 20");
			
			while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_ASSOC)){
				
				$logs_result = $mysql -> clear( $logs_result);
				
				$user_profile_link = array( 'user' => $logs_result['user_id']);
				
				$user_logs_form -> addToContent( '<tr>
					<td class="opt_row1" NOWRAP><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result['users_group_prefix'].$logs_result['user_login'].$logs_result['users_group_suffix'].'</a></th>
					<td class="opt_row2" style="width: 100%">'.$logs_result['admins_log_details'].'</td>
					<td class="opt_row1" style="text-align: center" NOWRAP>'.$time -> drawDate( $logs_result['admins_log_user_ip']).'</td>
					<td class="opt_row2" style="text-align: center" NOWRAP>'.long2ip( $logs_result['admins_log_user_ip']).'</td>
				</tr>');
				
			}
			
			$user_logs_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_logs_mods_saved_logs'), $user_logs_form -> display()));
			
			/**
			 * and paginator now
			 */
			
			$logs_pages_link = array( 'act' => 'logs_mods', 'do' => 'search_logs', 'search_phrase' => urlencode($phrase_to_search), 'search_type' => $search_type);
						
			parent::draw( $style -> drawPaginator( parent::adminLink( parent::getId(), $logs_pages_link), 'p', floor( $logs_nubmer / 20), ( $page_to_draw + 1)));
			
			
		}else{
		
			if ( $_GET['do'] == 'search_logs')
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'error'), $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_empty_phrase')));
			
			/**
			 * draw last 5 admins actions
			 */
			
			$last_logs_form = new form();
			$last_logs_form -> openOpTable();
			$last_logs_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'user_username').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_type').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_time').'</th>
				<th>'.$language -> getString( 'user_ip').'</th>
			</tr>');
			
			$logs_query = $mysql -> query( "SELECT l.*, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM moderators_logs l LEFT JOIN users u ON l.moderators_log_user_id = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id ORDER BY l.moderators_log_time DESC LIMIT 5");
			
			while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_ASSOC)){
				
				$logs_result = $mysql -> clear( $logs_result);
				
				$user_profile_link = array( 'user' => $logs_result['user_id']);
				
				$last_logs_form -> addToContent( '<tr>
					<td class="opt_row1" NOWRAP><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result['users_group_prefix'].$logs_result['user_login'].$logs_result['users_group_suffix'].'</a></th>
					<td class="opt_row2" style="width: 100%">'.$logs_result['moderators_log_details'].'</td>
					<td class="opt_row1" style="text-align: center" NOWRAP>'.$time -> drawDate( $logs_result['moderators_log_time']).'</td>
					<td class="opt_row2" style="text-align: center" NOWRAP>'.long2ip( $logs_result['moderators_log_user_ip']).'</td>
				</tr>');
				
			}
			
			$last_logs_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_logs_mods_last_5'), $last_logs_form -> display()));
			
			/**
			 * now admins and their logs browser
			 */
			
			$users_logs_form = new form();
			$users_logs_form -> openOpTable();
			$users_logs_form -> addToContent( '<tr>
				<th NOWRAP>'.$language -> getString( 'user_username').'</th>
				<th NOWRAP>'.$language -> getString( 'acp_admin_subsection_logs_admins_saved_logs_logs_num').'</th>
				<th NOWRAP>'.$language -> getString( 'actions').'</th>
			</tr>');
			
			$logs_query = $mysql -> query( "SELECT l.*, COUNT(*) as logs_num, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM moderators_logs l LEFT JOIN users u ON l.moderators_log_user_id = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id GROUP BY l.moderators_log_user_id  ORDER BY l.moderators_log_time DESC LIMIT 5");
			
			while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_ASSOC)){
				
				$logs_result = $mysql -> clear( $logs_result);
				
				$user_profile_link = array( 'user' => $logs_result['user_id']);
				$user_logs_link = array( 'act' => 'logs_mods', 'do' => 'show_user_logs', 'user' => $logs_result['user_id']);
				
				$users_logs_form -> addToContent( '<tr>
					<td class="opt_row1" style="width: 100%"><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result['users_group_prefix'].$logs_result['user_login'].$logs_result['users_group_suffix'].'</a></th>
					<td class="opt_row2" style="text-align: center" NOWRAP>'.$logs_result['logs_num'].'</td>
					<td class="opt_row3" style="text-align: center" NOWRAP><a href="'.parent::adminLink( parent::getId(), $user_logs_link).'">'.$language -> getString( 'acp_admin_subsection_logs_admins_saved_logs_member_logs_show').'</a></td>
				</tr>');
				
			}
			
			$users_logs_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_logs_mods_saved_logs'), $users_logs_form -> display()));
			
			/**
			 * searching function
			 */
				
			$search_url = array( 'act' => 'logs_mods', 'do' => 'search_logs');
			
			$search_form = new form();
			$search_form -> openForm( parent::adminLink( parent::getId(), $search_url));
			$search_form -> openOpTable();
			
			$search_form -> drawTextInput( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_by'), 'search_phrase');
			
			$searchs_types[0] = $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_in_0');
			//$searchs_types[1] = $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_in_1');
			$searchs_types[2] = $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_in_2');
			
			$search_form -> drawList( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_in'), 'search_type', $searchs_types);
			
			$search_form -> closeTable();
			$search_form -> drawButton( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_button'), 'search_logs');
			$search_form -> closeForm();			
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs'), $search_form -> display()));

			
					
		}
		
	}
	
	function act_mails_logs_list(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'logs_emails');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_section_logs'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_logs_emails'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_admin_subsection_logs_emails'));
		
		if ( $_GET['do'] == 'show_user_logs' && isset( $_GET['user'])){
			
			/**
			 * draw logs of exact user
			 */
			
			$user_logs_to_show = $_GET['user'];
			
			settype( $user_logs_to_show, 'integer');
			
			/**
			 * add breadcrumb
			 */
			
			$path_link = array( 'act' => 'logs_emails', 'do' => 'show_user_logs', 'user' => $user_logs_to_show);
			
			$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_mails_logs_saved_logs'), parent::adminLink( parent::getId(), $path_link));
		
			/**
			 * set page title
			 */
			
			$output -> setTitle( $language -> getString( 'acp_admin_subsection_mails_logs_saved_logs'));
					
			/**
			 * define page
			 */
			
			$logs_nubmer = $mysql -> countRows( 'mails_logs', "`mails_log_sender` = '$user_logs_to_show'");
			
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
			
			$user_logs_form = new form();
			$user_logs_form -> openOpTable();
			$user_logs_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'acp_admin_subsection_mails_logs_sender').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_mails_logs_subject').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_mails_logs_receiver').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_time').'</th>
				<th>'.$language -> getString( 'user_ip').'</th>
			</tr>');
			
			$logs_query = $mysql -> query( "SELECT l.*, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix, ur.user_id, ur.user_login, gr.users_group_prefix, gr.users_group_suffix FROM mails_logs l LEFT JOIN users u ON l.mails_log_sender = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id LEFT JOIN users ur ON l.mails_log_receiver = ur.user_id LEFT JOIN users_groups gr ON ur.user_main_group = gr.users_group_id WHERE mails_log_sender = '$user_logs_to_show' ORDER BY l.mails_log_time DESC LIMIT ".($page_to_draw * 20).", 20");
			
			while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_NUM)){
				
				$logs_result = $mysql -> clear( $logs_result);
				
				$user_profile_link = array( 'user' => $logs_result[6]);
				
				$user_logs_form -> addToContent( '<tr>
					<td class="opt_row1" NOWRAP><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result[8].$logs_result[7].$logs_result[9].'</a></th>
					<td class="opt_row2" NOWRAP>'.$logs_result[5].'</td>
					<td class="opt_row1" NOWRAP><a href="'.parent::systemLink( 'user', array( 'user' => $logs_result[10])).'" target="_blank">'.$logs_result[12].$logs_result[11].$logs_result[13].'</a></th>
					<td class="opt_row2" style="text-align: center" NOWRAP>'.$time -> drawDate( $logs_result[1]).'</td>
					<td class="opt_row1" style="text-align: center" NOWRAP>'.long2ip( $logs_result[4]).'</td>
				</tr>');
				
			}
			
			$user_logs_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_mails_logs_saved_logs'), $user_logs_form -> display()));
			
			/**
			 * and paginator now
			 */
			
			$logs_pages_link = array( 'act' => 'logs_emails', 'do' => 'show_user_logs', 'user' => $user_logs_to_show);
			
			parent::draw( $style -> drawPaginator( parent::adminLink( parent::getId(), $logs_pages_link), 'p', ceil( $logs_nubmer / 20), ( $page_to_draw + 1)));
			
		}else if ($_GET['do'] == 'search_logs' && (isset( $_POST['search_phrase']) || isset( $_GET['search_phrase'])) && (strlen( trim($_POST['search_phrase'])) > 0 || strlen( trim($_GET['search_phrase'])) > 0)) {
			
			/**
			 * build list to search
			 * start from phrase
			 */
			
			if ( isset($_POST['search_phrase'])){
			
				$phrase_to_search = $_POST['search_phrase'];
					
			}else{
				
				$phrase_to_search = $_GET['search_phrase'];
				
			}
			
			$phrase_to_search = uniSlashes( htmlspecialchars( trim( $phrase_to_search)));
							
			/**
			 * add breadcrumb
			 */
			
			$path_link = array( 'act' => 'logs_emails', 'do' => 'search_logs', 'search_phrase' => urlencode($phrase_to_search), 'search_type' => $search_type);
			
			$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs'), parent::adminLink( parent::getId(), $path_link));
		
			/**
			 * set page title
			 */
			
			$output -> setTitle( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs'));
				
			/**
			 * define page
			 */
			
			$logs_nubmer = $mysql -> countRows( 'mails_logs', "`mails_log_subject` LIKE '%$phrase_to_search%'");
			
			$pages_number = floor( $logs_nubmer / 20 );
			
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
			
			$user_logs_form = new form();
			$user_logs_form -> openOpTable();
			$user_logs_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'acp_admin_subsection_mails_logs_sender').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_mails_logs_subject').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_mails_logs_receiver').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_time').'</th>
				<th>'.$language -> getString( 'user_ip').'</th>
			</tr>');
			
			$logs_query = $mysql -> query( "SELECT l.*, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix, ur.user_id, ur.user_login, gr.users_group_prefix, gr.users_group_suffix FROM mails_logs l LEFT JOIN users u ON l.mails_log_sender = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id LEFT JOIN users ur ON l.mails_log_receiver = ur.user_id LEFT JOIN users_groups gr ON ur.user_main_group = gr.users_group_id WHERE l.mails_log_subject LIKE '%$phrase_to_search%' ORDER BY l.mails_log_time DESC LIMIT ".($page_to_draw * 20).", 20");
			
			while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_NUM)){
				
				$logs_result = $mysql -> clear( $logs_result);
				
				$user_profile_link = array( 'user' => $logs_result['user_id']);
				
				$user_logs_form -> addToContent( '<tr>
					<td class="opt_row1" NOWRAP><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result[8].$logs_result[7].$logs_result[9].'</a></th>
					<td class="opt_row2" NOWRAP>'.$logs_result[5].'</td>
					<td class="opt_row1" NOWRAP><a href="'.parent::systemLink( 'user', array( 'user' => $logs_result[10])).'" target="_blank">'.$logs_result[12].$logs_result[11].$logs_result[13].'</a></th>
					<td class="opt_row2" style="text-align: center" NOWRAP>'.$time -> drawDate( $logs_result[1]).'</td>
					<td class="opt_row1" style="text-align: center" NOWRAP>'.long2ip( $logs_result[4]).'</td>
				</tr>');
				
			}
			
			$user_logs_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_mails_logs_saved_logs'), $user_logs_form -> display()));
			
			/**
			 * and paginator now
			 */
			
			$logs_pages_link = array( 'act' => 'logs_emails', 'do' => 'search_logs', 'search_phrase' => urlencode($phrase_to_search), 'search_type' => $search_type);
						
			parent::draw( $style -> drawPaginator( parent::adminLink( parent::getId(), $logs_pages_link), 'p', floor( $logs_nubmer / 20), ( $page_to_draw + 1)));
			
			
		}else{
		
			if ( $_GET['do'] == 'search_logs')
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'error'), $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_empty_phrase')));
			
			/**
			 * draw last 5 admins actions
			 */
			
			$last_logs_form = new form();
			$last_logs_form -> openOpTable();
			$last_logs_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'acp_admin_subsection_mails_logs_sender').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_mails_logs_subject').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_mails_logs_receiver').'</th>
				<th>'.$language -> getString( 'acp_admin_subsection_logs_action_time').'</th>
				<th>'.$language -> getString( 'user_ip').'</th>
			</tr>');
			
			$logs_query = $mysql -> query( "SELECT l.*, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix, ur.user_id, ur.user_login, gr.users_group_prefix, gr.users_group_suffix FROM mails_logs l LEFT JOIN users u ON l.mails_log_sender = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id LEFT JOIN users ur ON l.mails_log_receiver = ur.user_id LEFT JOIN users_groups gr ON ur.user_main_group = gr.users_group_id ORDER BY l.mails_log_time DESC LIMIT 5");
			
			while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_NUM)){
				
				$logs_result = $mysql -> clear( $logs_result);
				
				$user_profile_link = array( 'user' => $logs_result[6]);
				
				$last_logs_form -> addToContent( '<tr>
					<td class="opt_row1" NOWRAP><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result[8].$logs_result[7].$logs_result[9].'</a></th>
					<td class="opt_row2" NOWRAP>'.$logs_result[5].'</td>
					<td class="opt_row1" NOWRAP><a href="'.parent::systemLink( 'user', array( 'user' => $logs_result[10])).'" target="_blank">'.$logs_result[12].$logs_result[11].$logs_result[13].'</a></th>
					<td class="opt_row2" style="text-align: center" NOWRAP>'.$time -> drawDate( $logs_result[1]).'</td>
					<td class="opt_row1" style="text-align: center" NOWRAP>'.long2ip( $logs_result[4]).'</td>
				</tr>');
				
			}
			
			$last_logs_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_mails_logs_last_5'), $last_logs_form -> display()));
			
			/**
			 * now admins and their logs browser
			 */
			
			$users_logs_form = new form();
			$users_logs_form -> openOpTable();
			$users_logs_form -> addToContent( '<tr>
				<th NOWRAP>'.$language -> getString( 'user_username').'</th>
				<th NOWRAP>'.$language -> getString( 'acp_admin_subsection_logs_admins_saved_logs_logs_num').'</th>
				<th NOWRAP>'.$language -> getString( 'actions').'</th>
			</tr>');
			
			$logs_query = $mysql -> query( "SELECT l.*, COUNT(*) as logs_num, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM mails_logs l LEFT JOIN users u ON l.mails_log_sender = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id GROUP BY l.mails_log_sender  ORDER BY l.mails_log_time DESC LIMIT 5");
			
			while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_ASSOC)){
				
				$logs_result = $mysql -> clear( $logs_result);
				
				$user_profile_link = array( 'user' => $logs_result['user_id']);
				$user_logs_link = array( 'act' => 'logs_emails', 'do' => 'show_user_logs', 'user' => $logs_result['user_id']);
				
				$users_logs_form -> addToContent( '<tr>
					<td class="opt_row1" style="width: 100%"><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result['users_group_prefix'].$logs_result['user_login'].$logs_result['users_group_suffix'].'</a></th>
					<td class="opt_row2" style="text-align: center" NOWRAP>'.$logs_result['logs_num'].'</td>
					<td class="opt_row3" style="text-align: center" NOWRAP><a href="'.parent::adminLink( parent::getId(), $user_logs_link).'">'.$language -> getString( 'acp_admin_subsection_logs_admins_saved_logs_member_logs_show').'</a></td>
				</tr>');
				
			}
			
			$users_logs_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_mails_logs_saved_logs'), $users_logs_form -> display()));
			
			/**
			 * searching function
			 */
				
			$search_url = array( 'act' => 'logs_emails', 'do' => 'search_logs');
			
			$search_form = new form();
			$search_form -> openForm( parent::adminLink( parent::getId(), $search_url));
			$search_form -> openOpTable();
			
			$search_form -> drawTextInput( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_by'), 'search_phrase');
			
			$search_form -> closeTable();
			$search_form -> drawButton( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs_search_button'), 'search_logs');
			$search_form -> closeForm();			
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_logs_admins_search_logs'), $search_form -> display()));
							
		}
		
	}
	
	function act_bots_logs_list(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'logs_bpts');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_section_logs'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_logs_bots'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_admin_subsection_logs_bots'));
			
		$logs_nubmer = $mysql -> countRows( 'mails_logs', "`mails_log_sender` = '$user_logs_to_show'");
		
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
		
		$user_logs_form = new form();
		$user_logs_form -> openOpTable();
		$user_logs_form -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_admin_subsection_logs_bots_bot').'</th>
			<th>'.$language -> getString( 'acp_admin_subsection_logs_bots_time').'</th>
			<th>'.$language -> getString( 'acp_admin_subsection_logs_bots_ip').'</th>
		</tr>');
		
		$logs_query = $mysql -> query( "SELECT * FROM spiders_logs ORDER BY spider_log_time DESC LIMIT ".($page_to_draw * 20).", 20");
		
		while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_ASSOC)){
			
			$logs_result = $mysql -> clear( $logs_result);
						
			$user_logs_form -> addToContent( '<tr>
				<td class="opt_row1" NOWRAP>'.$logs_result['spider_log_name'].'</th>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$time -> drawDate( $logs_result['spider_log_time']).'</td>
				<td class="opt_row1" style="text-align: center" NOWRAP>'.long2ip( $logs_result['spider_log_ip']).'</td>
			</tr>');
			
		}
		
		$user_logs_form -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_logs_bots'), $user_logs_form -> display()));
		
		/**
		 * and paginator now
		 */
		
		$logs_pages_link = array( 'act' => 'logs_bots');
		
		parent::draw( $style -> drawPaginator( parent::adminLink( parent::getId(), $logs_pages_link), 'p', ceil( $logs_nubmer / 20), ( $page_to_draw + 1)));
	
	}
	
	function act_acp_logins_logs_list(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'logs_logins');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_section_logs'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_logs_logins'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_admin_subsection_logs_logins'));
		
		/**
		 * draw form
		 */
		
		$logins_logs_form = new form();
		$logins_logs_form -> openOpTable();
		$logins_logs_form -> addToContent( '<tr>
			<th>'.$language -> getString( 'user_username').'</th>
			<th>'.$language -> getString( 'time').'</th>
			<th>'.$language -> getString( 'user_ip').'</th>
			<th>'.$language -> getString( 'acp_login_logs_status').'</th>
		</tr>');
		
		$logs_query = $mysql -> query( "SELECT l.admins_login_log_time, l.admins_login_log_user_ip, l.admins_login_log_success, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM admins_loging_log l LEFT JOIN users u ON u.user_id = l.admins_login_log_user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id ORDER BY l.admins_login_log_time DESC LIMIT 30");
		
		while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_ASSOC)){
			
			$logs_result = $mysql -> clear($logs_result);
			
			$user_profile_link = array( 'user' => $logs_result['user_id']);
			
			$logins_logs_form -> addToContent( '<tr>
			<td class="opt_row1" style="width: 100%"><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result['users_group_prefix'].$logs_result['user_login'].$logs_result['users_group_suffix'].'</a></td>
			<td class="opt_row2" style="text-align: center" NOWRAP>'.$time -> drawDate( $logs_result['admins_login_log_time']).'</td>
			<td class="opt_row1" style="text-align: center" NOWRAP>'.long2ip( $logs_result['admins_login_log_user_ip']).'</td>
			<td class="opt_row2" style="text-align: center" NOWRAP>'.$style -> drawThick( $logs_result['admins_login_log_success']).'</td>
		</tr>');
			
		}
		
		$logins_logs_form -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_login_logs'), $logins_logs_form -> display()));
		
		
	}

	function act_stats_registers(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'stats_registers');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_section_statistics'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_stats_registers'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_admin_subsection_stats_registers'));
		
		/**
		 * generate statistics
		 */
		
		$gen_time_start = (time() - 500 * 24 * 60 * 60);
		$gen_time_end = time();
		$gen_prec = 0;
		
		$precisions_list[0] = $language -> getString( 'acp_admin_subsection_stats_registers_generate_precision_0');
		$precisions_list[1] = $language -> getString( 'acp_admin_subsection_stats_registers_generate_precision_1');
		$precisions_list[2] = $language -> getString( 'acp_admin_subsection_stats_registers_generate_precision_2');
		
		if ( $session -> checkForm()){
			
			/**
			 * form submited
			 */
			
			$gen_time_start = $time  -> getDate( 'generate_start');
			$gen_time_end = $time  -> getDate( 'generate_end');
			$gen_prec = $_POST['generate_prec'];
			
			settype( $gen_time_start, 'integer');
			settype( $gen_time_end, 'integer');
			settype( $gen_prec, 'integer');
			
			if ( $gen_prec < 0 )
				$gen_prec = 0;
			
			if ( $gen_prec > 2 )
				$gen_prec = 2;
				
			/**
			 * begin drawing
			 */
				
			$stats_result_form = new form();
			$stats_result_form -> drawSpacer( $language -> getString( 'acp_admin_subsection_stats_registers_generate_start').' '.$time -> drawDate( $gen_time_start).'; '.$language -> getString( 'acp_admin_subsection_stats_registers_generate_end').': '.$time -> drawDate( $gen_time_end).'; '.$language -> getString( 'acp_admin_subsection_stats_registers_generate_precision').': '.$precisions_list[$gen_prec]);
			$stats_result_form -> openOpTable();
			$stats_result_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'acp_admin_subsection_stats_registers_generate_result_date').'</th>
				<th>&nbsp;</th>
				<th>'.$language -> getString( 'acp_admin_subsection_stats_registers_generate_result_results').'</th>
			</tr>');
			
			/**
			 * do query
			 */
			
			$stats_query = $mysql -> query( "SELECT user_regdate FROM users WHERE `user_id` > '0' AND `user_regdate` >= '$gen_time_start' AND `user_regdate` <= '$gen_time_end' ORDER BY `user_regdate`");
			
			$actual_time = 0;
			$total_results = 0;
			
			$generated_stats = array();
			
			while ( $stats_result = mysql_fetch_array( $stats_query, MYSQL_NUM)){
							
				/**
				 * increase results
				 */
				
				$total_results ++;
				
				/**
				 * check date
				 */
				
				switch ( $gen_prec){
					
					case 0:
						
						$generated_stats[ date( 'Y.m.d', $stats_result[0])] ++;
						
					break;
					
					case 1:
						
						$generated_stats[ date( 'Y.m', $stats_result[0])] ++;
						
					break;
					
					case 2:
						
						$generated_stats[ date( 'Y', $stats_result[0])] ++;
						
					break;
				}
				
			}
			
			/**
			 * now draw generated result
			 */
			
			foreach ( $generated_stats as $generation_time => $generation_value){
				
				$stats_result_form -> addToContent( '<tr>
					<td class="opt_row1" NOWRAP="nowrap">'.$generation_time.'</td>
					<td class="opt_row2" style="width: 100%">'.$style -> drawBar( ceil( $generation_value * 100 / $total_results)).'</td>
					<td class="opt_row3" NOWRAP="nowrap">'.$generation_value.'</td>
				</tr>');
				
			}
			
			/**
			 * close table
			 */
			
			$stats_result_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_stats_registers'), $stats_result_form -> display()));
			
		}
				
		/**
		 * generate form
		 */
		
		$generate_new = new form();
		$generate_new -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'stats_registers')));
		$generate_new -> openOpTable();
		
		$generate_new -> drawDateSelect( $language -> getString( 'acp_admin_subsection_stats_registers_generate_start'), 'generate_start', $gen_time_start);
		$generate_new -> drawDateSelect( $language -> getString( 'acp_admin_subsection_stats_registers_generate_end'), 'generate_end', $gen_time_end);
		
		$generate_new -> drawList( $language -> getString( 'acp_admin_subsection_stats_registers_generate_precision'), 'generate_prec', $precisions_list, $gen_prec); 
		
		$generate_new -> closeTable();
		$generate_new -> drawButton();
		$generate_new -> closeForm();
		
		/**
		 * display
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_stats_registers_generate'), $generate_new -> display()));
		
	}
	
	function act_stats_topics(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'stats_topics');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_section_statistics'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_stats_topics'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_admin_subsection_stats_topics'));
		
		/**
		 * generate statistics
		 */
		
		$gen_time_start = (time() - 500 * 24 * 60 * 60);
		$gen_time_end = time();
		$gen_prec = 0;
		
		$precisions_list[0] = $language -> getString( 'acp_admin_subsection_stats_registers_generate_precision_0');
		$precisions_list[1] = $language -> getString( 'acp_admin_subsection_stats_registers_generate_precision_1');
		$precisions_list[2] = $language -> getString( 'acp_admin_subsection_stats_registers_generate_precision_2');
		
		if ( $session -> checkForm()){
			
			/**
			 * form submited
			 */
			
			$gen_time_start = $time  -> getDate( 'generate_start');
			$gen_time_end = $time  -> getDate( 'generate_end');
			$gen_prec = $_POST['generate_prec'];
			
			settype( $gen_time_start, 'integer');
			settype( $gen_time_end, 'integer');
			settype( $gen_prec, 'integer');
			
			if ( $gen_prec < 0 )
				$gen_prec = 0;
			
			if ( $gen_prec > 2 )
				$gen_prec = 2;
				
			/**
			 * begin drawing
			 */
				
			$stats_result_form = new form();
			$stats_result_form -> drawSpacer( $language -> getString( 'acp_admin_subsection_stats_registers_generate_start').' '.$time -> drawDate( $gen_time_start).'; '.$language -> getString( 'acp_admin_subsection_stats_registers_generate_end').': '.$time -> drawDate( $gen_time_end).'; '.$language -> getString( 'acp_admin_subsection_stats_registers_generate_precision').': '.$precisions_list[$gen_prec]);
			$stats_result_form -> openOpTable();
			$stats_result_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'acp_admin_subsection_stats_registers_generate_result_date').'</th>
				<th>&nbsp;</th>
				<th>'.$language -> getString( 'acp_admin_subsection_stats_registers_generate_result_results').'</th>
			</tr>');
			
			/**
			 * do query
			 */
			
			$stats_query = $mysql -> query( "SELECT topic_start_time FROM topics WHERE `topic_start_time` >= '$gen_time_start' AND `topic_start_time` <= '$gen_time_end' ORDER BY `topic_start_time`");
			
			$actual_time = 0;
			$total_results = 0;
			
			$generated_stats = array();
			
			while ( $stats_result = mysql_fetch_array( $stats_query, MYSQL_NUM)){
							
				/**
				 * increase results
				 */
				
				$total_results ++;
				
				/**
				 * check date
				 */
				
				switch ( $gen_prec){
					
					case 0:
						
						$generated_stats[ date( 'Y.m.d', $stats_result[0])] ++;
						
					break;
					
					case 1:
						
						$generated_stats[ date( 'Y.m', $stats_result[0])] ++;
						
					break;
					
					case 2:
						
						$generated_stats[ date( 'Y', $stats_result[0])] ++;
						
					break;
				}
				
			}
			
			/**
			 * now draw generated result
			 */
			
			foreach ( $generated_stats as $generation_time => $generation_value){
				
				$stats_result_form -> addToContent( '<tr>
					<td class="opt_row1" NOWRAP="nowrap">'.$generation_time.'</td>
					<td class="opt_row2" style="width: 100%">'.$style -> drawBar( ceil( $generation_value * 100 / $total_results)).'</td>
					<td class="opt_row3" NOWRAP="nowrap">'.$generation_value.'</td>
				</tr>');
				
			}
			
			/**
			 * close table
			 */
			
			$stats_result_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_stats_topics'), $stats_result_form -> display()));
			
		}
				
		/**
		 * generate form
		 */
		
		$generate_new = new form();
		$generate_new -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'stats_topics')));
		$generate_new -> openOpTable();
		
		$generate_new -> drawDateSelect( $language -> getString( 'acp_admin_subsection_stats_registers_generate_start'), 'generate_start', $gen_time_start);
		$generate_new -> drawDateSelect( $language -> getString( 'acp_admin_subsection_stats_registers_generate_end'), 'generate_end', $gen_time_end);
		
		$generate_new -> drawList( $language -> getString( 'acp_admin_subsection_stats_registers_generate_precision'), 'generate_prec', $precisions_list, $gen_prec); 
		
		$generate_new -> closeTable();
		$generate_new -> drawButton();
		$generate_new -> closeForm();
		
		/**
		 * display
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_stats_registers_generate'), $generate_new -> display()));
		
	}
	
	function act_stats_posts(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'stats_posts');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_section_statistics'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_stats_posts'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_admin_subsection_stats_posts'));
		
		/**
		 * generate statistics
		 */
		
		$gen_time_start = (time() - 500 * 24 * 60 * 60);
		$gen_time_end = time();
		$gen_prec = 0;
		
		$precisions_list[0] = $language -> getString( 'acp_admin_subsection_stats_registers_generate_precision_0');
		$precisions_list[1] = $language -> getString( 'acp_admin_subsection_stats_registers_generate_precision_1');
		$precisions_list[2] = $language -> getString( 'acp_admin_subsection_stats_registers_generate_precision_2');
		
		if ( $session -> checkForm()){
			
			/**
			 * form submited
			 */
			
			$gen_time_start = $time  -> getDate( 'generate_start');
			$gen_time_end = $time  -> getDate( 'generate_end');
			$gen_prec = $_POST['generate_prec'];
			
			settype( $gen_time_start, 'integer');
			settype( $gen_time_end, 'integer');
			settype( $gen_prec, 'integer');
			
			if ( $gen_prec < 0 )
				$gen_prec = 0;
			
			if ( $gen_prec > 2 )
				$gen_prec = 2;
				
			/**
			 * begin drawing
			 */
				
			$stats_result_form = new form();
			$stats_result_form -> drawSpacer( $language -> getString( 'acp_admin_subsection_stats_registers_generate_start').' '.$time -> drawDate( $gen_time_start).'; '.$language -> getString( 'acp_admin_subsection_stats_registers_generate_end').': '.$time -> drawDate( $gen_time_end).'; '.$language -> getString( 'acp_admin_subsection_stats_registers_generate_precision').': '.$precisions_list[$gen_prec]);
			$stats_result_form -> openOpTable();
			$stats_result_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'acp_admin_subsection_stats_registers_generate_result_date').'</th>
				<th>&nbsp;</th>
				<th>'.$language -> getString( 'acp_admin_subsection_stats_registers_generate_result_results').'</th>
			</tr>');
			
			/**
			 * do query
			 */
			
			$stats_query = $mysql -> query( "SELECT post_time FROM posts WHERE `post_time` >= '$gen_time_start' AND `post_time` <= '$gen_time_end' ORDER BY `post_time`");
			
			$actual_time = 0;
			$total_results = 0;
			
			$generated_stats = array();
			
			while ( $stats_result = mysql_fetch_array( $stats_query, MYSQL_NUM)){
							
				/**
				 * increase results
				 */
				
				$total_results ++;
				
				/**
				 * check date
				 */
				
				switch ( $gen_prec){
					
					case 0:
						
						$generated_stats[ date( 'Y.m.d', $stats_result[0])] ++;
						
					break;
					
					case 1:
						
						$generated_stats[ date( 'Y.m', $stats_result[0])] ++;
						
					break;
					
					case 2:
						
						$generated_stats[ date( 'Y', $stats_result[0])] ++;
						
					break;
				}
				
			}
			
			/**
			 * now draw generated result
			 */
			
			foreach ( $generated_stats as $generation_time => $generation_value){
				
				$stats_result_form -> addToContent( '<tr>
					<td class="opt_row1" NOWRAP="nowrap">'.$generation_time.'</td>
					<td class="opt_row2" style="width: 100%">'.$style -> drawBar( ceil( $generation_value * 100 / $total_results)).'</td>
					<td class="opt_row3" NOWRAP="nowrap">'.$generation_value.'</td>
				</tr>');
				
			}
			
			/**
			 * close table
			 */
			
			$stats_result_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_stats_posts'), $stats_result_form -> display()));
			
		}
				
		/**
		 * generate form
		 */
		
		$generate_new = new form();
		$generate_new -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'stats_posts')));
		$generate_new -> openOpTable();
		
		$generate_new -> drawDateSelect( $language -> getString( 'acp_admin_subsection_stats_registers_generate_start'), 'generate_start', $gen_time_start);
		$generate_new -> drawDateSelect( $language -> getString( 'acp_admin_subsection_stats_registers_generate_end'), 'generate_end', $gen_time_end);
		
		$generate_new -> drawList( $language -> getString( 'acp_admin_subsection_stats_registers_generate_precision'), 'generate_prec', $precisions_list, $gen_prec); 
		
		$generate_new -> closeTable();
		$generate_new -> drawButton();
		$generate_new -> closeForm();
		
		/**
		 * display
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_stats_registers_generate'), $generate_new -> display()));
		
	}
	
	function act_mysql_query(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'logs_logins');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_section_mysql'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_admin_subsection_mysql_query'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_admin_subsection_mysql_query'));
		

		/**
		 * action is set, lets make a query
		 */
		
		if( isset( $_POST['custom_query']) && $session -> checkForm()){
			
			$custom_query = trim($_POST['custom_query']);
			
			if ( get_magic_quotes_gpc())
				$custom_query = stripslashes( $custom_query);
			
			$error = 0;
						
			if($error == 0){
				
				$query_time = microtime();
				
				$query = $mysql -> query($custom_query);
				
				$query_time = abs($query_time-microtime());
							
				if(mysql_error() == null){			
					
					$language -> setKey( 'mysql_query_time', round( $query_time, 5));
					$language -> setKey( 'mysql_query_results', mysql_affected_rows());
					
					parent::draw( $style -> drawBlock( $language -> getString( 'acp_admin_subsection_mysql_query_success'), $language -> getString( 'acp_admin_subsection_mysql_query_result_info')));
					
					/**
					 * add new admin log
					 */
					
					$log_keys = array( 'mysql_log_content' => $custom_query);
					
					$logs -> addAdminLog( $language -> getString( 'acp_admin_subsection_mysql_query_log'), $log_keys);
					
					/**
					 * now draw table with results
					 */
					
					if( substr( $custom_query, 0, 6) == "SELECT"){
						
						$result_table = new form();
						$result_table -> openOpTable();
						
						$head_drawed = false;
						
						while( $result = mysql_fetch_array( $query, MYSQL_ASSOC)){
							
							$n = 0;
							
							$result_table -> addToContent( '<tr>');
							
							if( !$head_drawed){
								
								foreach ( $result as $element_id => $value)
									$result_table -> addToContent( '<th>'.$element_id.'</th>');
									
								$result_table -> addToContent( '</tr><tr>');
								
								$head_drawed = true;
							}
							
							foreach ( $result as $element_id => $value){
									
								if( $n % 2 == 0){
									$class = 'opt_row1';
								}else{
									$class = 'opt_row2';
								}
								
								$result_table -> addToContent( '<td class="'.$class.'">'.$value.'</td>');
								$n ++;
							}
							
							$result_table -> addToContent( '</tr>');
							
						}
						
						$result_table -> closeTable();
						
						parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_mysql_query_result'), $result_table -> display()));							
						
					}
										
				}else{
					
					$language -> setKey( 'mysql_error_number', mysql_errno());
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_admin_subsection_mysql_query_error'), mysql_error()));
										
				}
				
				
				
			}
			
			
		}
			
		/**
		 * display form now
		 */
		
		$new_query_link = array( 'act' => 'mysql_query');
		
		$quering_form = new form();
		$quering_form -> openForm( parent::adminLink( parent::getId(), $new_query_link));
		$quering_form -> openOpTable();
		
		$quering_form -> drawTextBox( $language -> getString('acp_admin_subsection_mysql_query_text'), 'custom_query');
		
		$quering_form -> closeTable();
		$quering_form -> drawButton( $language -> getString( 'acp_admin_subsection_mysql_query_button'));
		$quering_form -> closeForm();
		
		parent::draw($style -> drawFormBlock( $language -> getString( 'acp_admin_subsection_mysql_query'), $quering_form -> display()));
		
	}
	
}
	
?>