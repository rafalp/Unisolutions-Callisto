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
|	Acp Main Page
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class acp_section_main extends acp_section{
	
	function __construct(){
				
		/**
		 * include global classes pointers
		 */
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * begin the rest
		 */
		
		if ( $smode != 2){		
			
			parent::draw( $language -> getString( 'acp_blocks_hello_text'));
			
			/**
			 * and rest of summary page
			 */
							
			parent::draw( '<table style="width: 100%; table-layout: fixed">
				<tr>
					<td style="vertical-align: top; width: 70%; padding-right: 3px;">');
			
			/**
			 * shorts
			 */
			
			$shotscuts = new form();
			$shotscuts -> openOpTable( true);
			$shotscuts -> addToContent('<tr>');
			
			$shortcut[] = '<a href="'.parent::adminLink( 'settings').'">'.$language ->getString( 'acp_settings_subsection_ops_list').'</a>';
			$shortcut[] = '<a href="'.parent::adminLink( 'users').'">'.$language ->getString( 'acp_users_subsection_users').'</a>';
			
			$short_link = array( 'act' => 'users_groups');
			$shortcut[] = '<a href="'.parent::adminLink( 'users', $short_link).'">'.$language ->getString( 'acp_users_subsection_users_groups').'</a>';
			
			$short_link = array( 'act' => 'new_member');
			$shortcut[] = '<a href="'.parent::adminLink( 'users', $short_link).'">'.$language ->getString( 'acp_users_subsection_new_member').'</a>';
			
			$short_link = array( 'act' => 'users_notactive');
			$shortcut[] = '<a href="'.parent::adminLink( 'users', $short_link).'">'.$language ->getString( 'acp_users_subsection_users_notactive').'</a>';
			
			$short_link = array( 'act' => 'users_banned');
			$shortcut[] = '<a href="'.parent::adminLink( 'users', $short_link).'">'.$language ->getString( 'acp_users_subsection_users_banned').'</a>';
			
			$short_link = array( 'act' => 'boards');
			$shortcut[] = '<a href="'.parent::adminLink( 'forums', $short_link).'">'.$language ->getString( 'acp_forums_subsection_boards').'</a>';
			
			$short_link = array( 'act' => 'new_board');
			$shortcut[] = '<a href="'.parent::adminLink( 'forums', $short_link).'">'.$language ->getString( 'acp_forums_subsection_new_board').'</a>';
			
			$short_link = array( 'act' => 'boards_perms');
			$shortcut[] = '<a href="'.parent::adminLink( 'forums', $short_link).'">'.$language ->getString( 'acp_forums_subsection_boards_perms').'</a>';
			
			$cell = 0;
			
			foreach($shortcut as $shortcut){
				
				if ( $cell == 3){
					
					$shotscuts -> addToContent('</tr><tr>');
			
					$cell = 0;
				}
				
				/**
				 * insert fields
				 */
				
				$shotscuts -> addToContent( '<td class="opt_row'.($cell+1).'">'.$shortcut.'</td>');
			
				/**
				 * increase cell
				 */
				
				$cell ++;
				
			}
			
			while ( $cell < 3){
				$shotscuts -> addToContent( '<td class="opt_row'.($cell+1).'">&nbsp;</td>');
				$cell++;
			}
			
			$shotscuts -> addToContent('</tr>');
			$shotscuts -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_blocks_shorstcuts'), $shotscuts -> display()));
			
			/**
			 * system previev block
			 */
			
			$summary_block = new form();
			$summary_block -> openOpTable( true);
			
			$summary_fields['acp_blocks_summary_script_name'] = UNI_PRODUCT;
			$summary_fields['acp_blocks_summary_script_version'] = UNI_VER;
			$summary_fields['acp_blocks_summary_script_members'] = $settings['users_num'];
			$summary_fields['acp_blocks_summary_script_members_unactive'] = $mysql -> countRows( 'users', "`user_active` = '0' AND `user_id` > '0'");
			$summary_fields['acp_blocks_summary_script_threads'] = $settings['board_threads_total'];
			$summary_fields['acp_blocks_summary_script_posts'] = $settings['board_posts_total'];
			
			$summary_fields['acp_blocks_server_php_os'] = $_SERVER['SERVER_SOFTWARE'];
			$summary_fields['acp_blocks_server_php_ver'] = phpversion();
			$summary_fields['acp_blocks_server_mysql_ver'] = mysql_get_client_info();
			$summary_fields['acp_blocks_server_max_post_size'] = ini_get('post_max_size');
			$summary_fields['acp_blocks_server_max_upload_size'] = ini_get('upload_max_filesize');
			
			if ( extension_loaded('gd')){
			
				$gd_info = gd_info();
				$gd_info = $gd_info['GD Version'];
				$summary_fields['acp_blocks_server_gd_ver'] = $gd_info;
			
			}else{
				
				$summary_fields['acp_blocks_server_gd_ver'] = $language -> getString( 'acp_blocks_server_gd_notfound');
				
			}
			
			$summary_block -> addToContent('<tr>');
			
			$cell = 0;
			
			foreach($summary_fields as $field_name => $field_value){
				
				if ( $cell == 2){
					
					$summary_block -> addToContent('</tr><tr>');
			
					$cell = 0;
				}
				
				/**
				 * insert fields
				 */
				
				$summary_block -> addToContent( '<td class="opt_row1"><b>'.$language -> getString( $field_name).':</b></td><td class="opt_row2">'.$field_value.'</td>');
			
				/**
				 * increase cell
				 */
				
				$cell ++;
				
			}
			
			if ( $cell == 1){
				$summary_block -> addToContent( '<td class="opt_row1">&nbsp;</td><td class="opt_row2">&nbsp;</td>');
			
			}
			
			$summary_block -> addToContent('</tr>');
			$summary_block -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString('acp_blocks_summary'), $summary_block -> display()));
			
			/**
			 * admins notepad
			 */
			
			$admins_notepad = new form();
			//$admins_notepad -> openForm( $acp_path.'index.php', 'POST', false, 'adm_notepad');
			$admins_notepad -> hiddenValue( 'action', 'update_notepad');
			$admins_notepad -> openOpTable();
			$admins_notepad -> drawSingleTextBox( 'admins_notepad', $settings['admins_notepad']);
			$admins_notepad -> closeTable();
			$admins_notepad -> drawButton( false, "ntp_update");
			//$admins_notepad -> closeForm();
			
			$ajax_update_admin_notepad['smode'] = '2';
			$ajax_update_admin_notepad['action'] = 'upd_notepad';
			
			$admins_notepad -> addToContent('<script type="text/javascript">
			
				loader = document.getElementById(\'notepad_status\')
				ntpfield = document.getElementById(\'admins_notepad\')
				updnotepad = document.getElementById(\'ntp_update\')
				
				orginal_notepad_title = loader.innerHTML
			
				function updateNotepad(){
					
					uniAJAX = GetXmlHttpObject()
				
					uniAJAX.onreadystatechange = function(){
					
						if(uniAJAX.readyState == 4){
							loader.innerHTML = orginal_notepad_title
						}else{
							loader.innerHTML = "'.str_replace( '"', '\"', $style -> drawImage( 'loader_small')).'" + orginal_notepad_title
						}
					}
					
					newtext = ntpfield.value
					
					
					uniAJAX.open("POST","'.parent::adminLink( parent::getId(), $ajax_update_admin_notepad).'", true)
					uniAJAX.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
					uniAJAX.send( "text=" + encodeURIComponent(newtext))
					
				}
				
				updnotepad.onclick="updateNotepad()"
										
			</script>');
			
			$notepad_title = '<table style="width: 100%" border="0" colpadding="0" cellpadding="0"><tr><td>'.$language -> getString('acp_blocks_notepad').'</td><td style="text-align: right"><div id="notepad_status"></div></td></tr></table>';
			
			parent::draw( $style -> drawFormBlock( $notepad_title, $admins_notepad -> display()));			
			
			parent::draw( '</td><td style="vertical-align: top; padding-left: 3px;">');
			
			/**
			 * errors block
			 */
			
			if( file_exists( ROOT_PATH.'install/index.php')){
				
				parent::draw( $style -> drawErrorBlock( $language -> getString('acp_blocks_installer_present'), $language -> getString( 'acp_blocks_installer_present_info')));
				
			}
			
			if( !is_writeable( ROOT_PATH.'cache/')){
				
				parent::draw( $style -> drawErrorBlock( $language -> getString('acp_blocks_cache_unsaveable'), $language -> getString( 'acp_blocks_cache_unsaveable_info')));
				
			}
			
			if( is_writeable( ROOT_PATH.'config.php')){
				
				parent::draw( $style -> drawErrorBlock( $language -> getString('acp_blocks_config_saveable'), $language -> getString( 'acp_blocks_config_saveable_info')));
				
			}
			
			/**
			 * draw on-line admins
			 */
			
			$online_admins_title = '<table style=" width: 100%" colpadding="0" cellpadding="0"><tr><td style="width: 100%;">'.$language -> getString('acp_blocks_admins_online').'</td><td><div id="online_loader"></div></td></tr></table>';
						
			parent::draw( $style -> drawFormBlock( $online_admins_title, '<div id="on_line_users"></div>'));
			
			$ajax_update_online['smode'] = '2';
			$ajax_update_online['action'] = 'upd_online';
			
			parent::draw( '<script type="text/javascript">
			
			users_loader = document.getElementById(\'online_loader\')
			users_list = document.getElementById(\'on_line_users\')
			
			orginal_title = users_loader.innerHTML
			
			function loadonline(){
					
				uniAJAX = GetXmlHttpObject()
			
				uniAJAX.onreadystatechange = function(){
				
					if(uniAJAX.readyState == 4){
						users_list.innerHTML = uniAJAX.responseText
					}else{
						users_list.innerHTML = "<div style=\'text-align: center\'>'.str_replace( '"', '\"', $style -> drawImage( 'loader')).'</div>"
					}
				}
											
				uniAJAX.open("GET","'.parent::adminLink( parent::getId(), $ajax_update_online).'", true)
				uniAJAX.send( null)
				
			}
			
			function updateonline(){
					
				uniAJAX = GetXmlHttpObject()
			
				uniAJAX.onreadystatechange = function(){
				
					if(uniAJAX.readyState == 4){
						users_loader.innerHTML = orginal_title
						users_list.innerHTML = uniAJAX.responseText
					}else{
						users_loader.innerHTML = "'.str_replace( '"', '\"', $style -> drawImage( 'loader_small')).'" + orginal_title
					}
				}
											
				uniAJAX.open("GET","'.parent::adminLink( parent::getId(), $ajax_update_online).'", true)
				uniAJAX.send( null)
				
			}
			
			loadonline()
			window.setInterval("updateonline()", 10000)
			
			</script>');
			
			/**
			 * logins logs preview
			 */
			
			$logins_logs_form = new form();
			$logins_logs_form -> openOpTable();
			$logins_logs_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'user_username').'</th>
				<th>'.$language -> getString( 'acp_login_logs_status').'</th>
			</tr>');
			
			$logs_query = $mysql -> query( "SELECT l.admins_login_log_time, l.admins_login_log_user_ip, l.admins_login_log_success, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix FROM admins_loging_log l LEFT JOIN users u ON u.user_id = l.admins_login_log_user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id ORDER BY l.admins_login_log_time DESC LIMIT 5");
			
			while ( $logs_result = mysql_fetch_array( $logs_query, MYSQL_ASSOC)){
				
				$logs_result = $mysql -> clear($logs_result);
				
				$user_profile_link = array( 'user' => $logs_result['user_id']);
				
				$logins_logs_form -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%"><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$logs_result['users_group_prefix'].$logs_result['user_login'].$logs_result['users_group_suffix'].'</a><br/ >'.$time -> drawDate( $logs_result['admins_login_log_time']).' ('.long2ip( $logs_result['admins_login_log_user_ip']).')</td>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$style -> drawThick( $logs_result['admins_login_log_success']).'</td>
			</tr>');
				
			}
			
			$logins_logs_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_login_logs'), $logins_logs_form -> display()));
			
			/**
			 * draw installations history
			 */
			
			$install_table = new form();

			$install_table -> openOpTable();	
			
			$query = $mysql -> query("SELECT * FROM version_history ORDER BY `version_time` DESC");
			
			$install_table -> addToContent('<tr>
							<th>'.$language -> getString( 'acp_blocks_installment_history_version').'</th>
							<th NOWRAP>'.$language -> getString( 'acp_blocks_installment_history_date').'</th>
						</tr>');
			
			
			/**
			 * draw ver history
			 */
			
			while ($result = mysql_fetch_array($query, MYSQL_NUM)) {
				
				$install_table -> addToContent('<tr>
								<td class="opt_row1" style="width: 100%;">'.$result[1].' ('.$result[0].')</td>
								<td class="opt_row2" style="text-align: center;" NOWRAP>'.$time -> drawDate($result[2]).'</td>
							</tr>');
				
			}
			
			$install_table -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_blocks_installment_history'), $install_table -> display()));
			
			/**
			 * close table
			 */
			
			parent::draw( '</td>
				</tr>
			</table>');
			
			/**
			 * copyrights
			 */
			
			$credits_form = new form();
			$credits_form -> drawSpacer( $language -> getString( 'acp_blocks_credits_authors'));
			$credits_form -> openOpTable();
			$credits_form -> drawRow( $language -> getString( 'acp_blocks_credits_authors_text'));
			$credits_form -> closeTable();
			$credits_form -> drawSpacer( $language -> getString( 'acp_blocks_credits_special_thanks'));
			$credits_form -> openOpTable();
			$credits_form -> drawRow( $language -> getString( 'acp_blocks_credits_special_thanks_text'));
			$credits_form -> closeTable();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_blocks_credits'), $credits_form -> display()));
		
		}else{
			
			/**
			 * we are in ajax mode
			 */
			
			if ( $_GET['action'] == 'upd_notepad'){
				
				/**
				 * we are updating notepad
				 */
				
				$sql_ar['setting_value'] = $strings -> inputClear( $_POST['text'], false);
				
				$mysql -> update( $sql_ar, 'settings', "setting_setting = 'admins_notepad'");
				
				$cache -> flushCache( 'system_settings');
				
			}
			
			if ( $_GET['action'] == 'upd_online'){
				
				/**
				 * we are drawing on-line list
				 */
				
				$session_table = new form();
				$session_table -> openOpTable();	
			
				$query = $mysql -> query("SELECT u.user_id, u.user_login, s.admin_session_ip, s.admin_session_open_time, s.admin_session_last_time, g.users_group_prefix, g.users_group_suffix FROM admins_sessions s LEFT JOIN users u ON s.admin_session_user_id = u.user_id LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id");
				
				$session_table -> addToContent('<tr>
								<th>'.$language -> getString( 'acp_blocks_admins_online_user').'</th>
								<th NOWRAP>'.$language -> getString( 'acp_blocks_admins_online_login').'</th>
								<th NOWRAP>'.$language -> getString( 'acp_blocks_admins_online_last_action').'</th>
							</tr>');
				
				while ($result = mysql_fetch_array($query, MYSQL_ASSOC)) {
					
					$result = $mysql -> clear( $result);
					
					$user_profile_link = array( 'user' => $result['user_id']);
					
					$session_table -> addToContent('<tr>
									<td class="opt_row1" style="width: 100%;"><a href="'.parent::systemLink( 'user', $user_profile_link).'" target="_blank">'.$result['users_group_prefix'].$result['user_login'].$result['users_group_suffix'].'</a><br />'.long2ip( $result['admin_session_ip']).'</td>
									<td class="opt_row2" style="text-align: center;" NOWRAP>'.$time -> drawHour($result['admin_session_open_time']).'</td>
									<td class="opt_row1" style="text-align: center;" NOWRAP>'.$time -> timeAgo($result['admin_session_last_time']).'</td>
								</tr>');
					
				}
				
				$session_table -> closeTable();
				
				parent::draw( $session_table -> display());
					
			}
		}
	}
}
	
?>