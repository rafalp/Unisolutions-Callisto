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
|	Show settings group
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class action_show_group extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * proper sub-actions
		 */
		
		$proper_does = array( 'change_settings');
		$proper_repeats = array( 'show_group');
		
		if ( $session -> user['user_is_root']){
		
			$proper_does = array( 'change_settings', 'add_setting', 'update_setting', 'delete_group');
			$proper_repeats = array( 'show_group', 'new_setting', 'edit_setting');
			
		}
		
		/**
		 * now check if group to change is specified
		 */
		
		if ( isset( $_POST['groups_to_change']) && !empty( $_POST['groups_to_change'])){
				
			/**
			 * add positon to path
			 */
			
			$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_ops_list_title'), parent::adminSectionLink( 'ops_list'));
						
			/**
			 * proceed
			 */
			
			$group_to_change = $_POST['groups_to_change'];
			settype( $group_to_change, 'array');
			
			$groups_to_select = array();
			$drawed_groups = 0;
							
			foreach ( $group_to_change as $group_id){
				
				if ( $drawed_groups < 5){
					
					settype( $group_id, 'integer');
					$groups_to_select[] = $group_id;
					$drawed_groups ++;
				
				}
			}
			
			$this -> groups = $groups_to_select;
			
			/**
			 * now select it from mysql
			 */
		
			$this -> group_number = 0;
			
			
			if ( count( $group_to_change) == 0){
								
				$groups_to_select[] = 0;
				$this -> group_number = 1;
			
			}
			
			/**
			 * open form
			 */
			
			$groups_form = new form();
			$groups_form -> openForm( parent::adminSectionLink( parent::getId(), array( 'act' => 'show_group', 'do' => 'change_settings')));

			foreach ( $groups_to_select as $group_id){
				
				$groups_form -> hiddenValue( 'groups_to_change[]', $group_id);
				
			}
			
			parent::draw( $groups_form -> display());
			
			/**
			 * draw groups
			 */
					
			$group_query = $mysql -> query( "SELECT `settings_group_id`, `settings_group_title`, `settings_group_info` FROM settings_groups WHERE `settings_group_id` IN (".join( ',', $groups_to_select).") ORDER BY settings_group_title ASC");
			
			$form_checked = $session -> checkForm();
			$message_drawed = false;
			
			while ( $group_result = mysql_fetch_array( $group_query, MYSQL_ASSOC)){
				
				/**
				 * group found
				 */
				
				$group_result = $mysql -> clear( $group_result);
				
				$this -> group_number ++;
				
				$this -> group = $group_result['settings_group_id'];
				$this -> group_title = $group_result['settings_group_title'];
				$this -> group_info = $group_result['settings_group_info'];
				
				/**
				 * does
				 */
				
				if ( isset( $_GET['do']) && in_array( $_GET['do'], $proper_does)){
					
					switch ( $_GET['do']){
					
						case 'change_settings':
							
							/**
							 * change settings action, we will cycle trought settings in group
							 */
							
							if ( $form_checked){
								
								$settings_query = $mysql -> query( "SELECT * FROM settings WHERE `setting_group` = '".$group_result['settings_group_id']."'");
								
								while ( $settings_result = mysql_fetch_array( $settings_query, MYSQL_ASSOC)){
									
									/**
									 * clear result
									 */
									
									$settings_result = $mysql -> clear( $settings_result);
									
									/**
									 * define if setting type can be changeable
									 */
								
									$not_interceptables = array( 'info', 'thick');
									
									if( !in_array( $settings_result['setting_type'], $not_interceptables)){
										
										/**
										 * we can change this setting. Check, if user sended this setting
										 */
										
										if ( isset( $_POST['setting_'.$settings_result['setting_setting']])){
									
											/**
											 * user sended it. Depending on field type, we will do diffrent thing
											 */
											
											$new_setting_value = $_POST['setting_'.$settings_result['setting_setting']];
													
											/**
											 * check if field is empty (use default value, if so)
											 */
											
											if ( $new_setting_value == null && $settings_result['setting_value_default'] != null){
												
												$new_setting_value = $settings_result['setting_value_default'];
												
											}else{
												
												switch ( $settings_result['setting_type']){
													
													case 'text-box':
														
														$params = split( ";", $settings_result['setting_extra']);
														
														$new_setting_value = trim( $_POST['setting_'.$settings_result['setting_setting']]);
														
														if ( !in_array( "html", $params))
															$new_setting_value = htmlspecialchars($new_setting_value);
														
													break;
													
													case 'text-input':
														
														$params = split( ";", $settings_result['setting_extra']);
														
														$new_setting_value = trim( $_POST['setting_'.$settings_result['setting_setting']]);
														
														if ( !in_array( "html", $params))
															$new_setting_value = htmlspecialchars($new_setting_value);
														
													break;
													
													case 'text-editor':
														
														$params = split( ";", $settings_result['setting_extra']);
														
														$new_setting_value = $strings -> inputClear( $_POST['setting_'.$settings_result['setting_setting']], false);
														
													break;
													
													case 'list':
														
														$setting_list = array();
														
														$setting_list_unparsed = $settings_result['setting_extra'];
														
														if ( $setting_list_unparsed == "#forums#"){
														
															$setting_list = $forums -> getForumsList();
															
															unset( $setting_list[0]);
															
														}else{
															
															$setting_list_half_parsed = split( "\n", $setting_list_unparsed);
															
															foreach ( $setting_list_half_parsed as $setting_list_half_parsed_element){
																
																$half_parsed_id = substr( $setting_list_half_parsed_element, 0, strpos( $setting_list_half_parsed_element, "="));
																$half_parsed_value = substr( $setting_list_half_parsed_element, strpos( $setting_list_half_parsed_element, "=")+1, strlen( $setting_list_half_parsed_element));
															
																$setting_list[$half_parsed_id] = $half_parsed_value;
															
															}
															
														}
														
														if ( !key_exists( $new_setting_value, $setting_list)){
															$new_setting_value = $settings_result['setting_value_default'];
														}
														
													break;
													
													case 'yes-no':
																											
														$new_setting_value = $_POST['setting_'.$settings_result['setting_setting']];
														
														settype( $new_setting_value, "bool");
														
														if ( !$new_setting_value)
															$new_setting_value = 0;
														
													break;
													
												}
												
											}
											
											/**
											 * define if we have to force value type
											 */
											
											if ( !empty( $settings_result['setting_value_type']))
												settype( $new_setting_value, $settings_result['setting_value_type']);
												
											/**
											 * and update it in mysql
											 */
											
											$setting_update_sql['setting_value'] = uniSlashes($new_setting_value);
											$mysql -> update( $setting_update_sql, 'settings', "`setting_setting` = '".$settings_result['setting_setting']."'");
	
											/**
											 * and in actual page
											 */
											
											$settings[$settings_result['setting_setting']] = $new_setting_value;
											
										}
																		
									}
									
								}
								
								
								/**
								 * clear cache
								 */
								
								$cache -> flushCache( 'system_settings');
											
								/**
								 * draw message
								 */
								
								if ( !$message_drawed){
								
									parent::draw( $style -> drawInfoBlock( $language -> getString( 'information', true), $language -> getString( 'acp_settings_subsection_show_group_changed')));
									$message_drawed = true;
									
								}
								
								/**
								 * add log
								 */
									
								$log_keys = array( 'settings_group_name' => $this -> group_title);
								
								$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_show_group_changed_log'), $log_keys);
								
							}
							
						break;
						
					}
					
				}
				
				/**
				 * check what to do
				 */
				
				if ( isset( $repeat_act) && in_array( $repeat_act, $proper_repeats)){
				
					/**
					 * we have to repeat sub-act
					 */
					
					global $actual_action;
					$action_to_run = 'act_'.$repeat_act;
							
					$actual_action = $repeat_act;
					
					$run_action = $this -> $action_to_run();
					
				}else if ( isset( $_GET['do']) && in_array( $_GET['do'], $proper_repeats)){
					
					/**
					 * we have to repeat sub-act
					 */
					
					global $actual_action;
					$action_to_run = 'act_'.$_GET['do'];
							
					$actual_action = $_GET['do'];
					
					$run_action = $this -> $action_to_run();
					
				}else{
										
					/**
					 * draw list of groups
					 */
										
					$this -> show_group();
			
				}
		
			}
			
			/**
			 * close form
			 */

			$groups_form = new form();
			$groups_form -> closeForm();
				
			parent::draw( $groups_form -> display());
				
		}else{
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_show_group'), $language -> getString( 'acp_settings_subsection_show_group_nogroup')));
			
		}
		
	}
	
	function show_group(){
		
		/**
		 * include classes
		 */
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * add positon to path
		 */
		
		$show_group_link = '';
		
		//$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_show_group').': '.$this -> group_title, parent::adminSectionLink( 'show_group', $show_group_link));	
	
		/**
		 * set title
		 */
		
		//$output -> setTitle( $language -> getString( 'acp_settings_subsection_show_group').': '.$this -> group_title);
			
		/**
		 * draw switcher
		 */
		
		$select_setting_list = new form();
		
		/**
		 * open form for new groups
		 */

		$select_setting_list -> openOpTable();
				
		/**
		 * settings groups
		 */
		
		$settings_in_group = new form();
		
		/**
		 * open form for new groups
		 */
				
		$change_settins_link['do'] = 'change_settings';
		$change_settins_link['group'] = $this -> group;
		
		$settings_in_group -> openForm( parent::adminSectionLink( parent::getId(), $change_settins_link));
		
		$settings_in_group -> openOpTable();
		
		/**
		 * description first
		 */
		
		if ( strlen( $this -> group_info) > 0){
			
			$settings_in_group -> addToContent( '<td colspan="2" class="opt_row2">'.$this -> group_info.'</td>');
			
		}
		
		/**
		 * select from sql settings groups
		 */
		
		$settings_query = $mysql -> query( "SELECT * FROM settings WHERE `setting_group` = '".$this -> group."' ORDER BY setting_position");
		
		while ( $settings_result = mysql_fetch_array( $settings_query, MYSQL_ASSOC)){
					
			/**
			 * clear result
			 */
			
			$settings_result = $mysql -> clear( $settings_result);
			
			if ( !empty( $settings_result['setting_subgroup_open'])){
				
				/**
				 * we have to open subgroup
				 */
				
				$settings_in_group -> closeTable();
				$settings_in_group -> drawSpacer( $settings_result['setting_subgroup_open']);
				$settings_in_group -> openOpTable();
				
			}
			
			/**
			 * define what to do with actual setting
			 */
			
			switch ( $settings_result['setting_type']){
				
				case 'info':
					
					/**
					 * standard info row
					 */
					
					$settings_in_group -> drawInfoRow( $settings_result['setting_title'], $settings_result['setting_value'], $settings_result['setting_info']);
					
				break;
				
				case 'thick':
					
					/**
					 * standard info row width thick
					 */
					
					$settings_in_group -> drawInfoRow( $settings_result['setting_title'], $style -> drawThick( $settings_result['setting_value'], true), $settings_result['setting_info']);
					
				break;
				
				case 'text-box':
				
					/**
					 * standard text-input field
					 */
					
					$params = split( ";", $settings_result['setting_extra']);
					
					if ( in_array( "html", $params))
						$settings_result['setting_value'] = $strings -> outputClear( $settings_result['setting_value']);
					
					$settings_in_group -> drawTextBox( $settings_result['setting_title'], 'setting_'.$settings_result['setting_setting'], $settings_result['setting_value'], $settings_result['setting_info']);
					
				break;
				
				case 'text-editor':
				
					/**
					 * standard text-input field with editor for bbtags
					 */
					
					$settings_in_group -> drawEditor( $settings_result['setting_title'], 'setting_'.$settings_result['setting_setting'], $strings -> outputClear( $settings_result['setting_value'], false), $settings_result['setting_info'], true, true);
					
				break;
				
				case 'text-input':
				
					/**
					 * standard text-input field
					 */
					
					$params = split( ";", $settings_result['setting_extra']);
					
					if ( in_array( "html", $params))
						$settings_result['setting_value'] = $strings -> outputClear( $settings_result['setting_value']);
						
					$settings_in_group -> drawTextInput( $settings_result['setting_title'], 'setting_'.$settings_result['setting_setting'], $settings_result['setting_value'], $settings_result['setting_info']);
					
				break;
				
				case 'list':
				
					/**
					 * standard list
					 */
					
					$setting_list = array();
					
					$setting_list_unparsed = $settings_result['setting_extra'];
					
					if ( $setting_list_unparsed == "#forums#"){
					
						$setting_list = $forums -> getForumsList();
						
						unset( $setting_list[0]);
						
					}else{
																						
						$setting_list_half_parsed = split( "\n", $setting_list_unparsed);
						
						foreach ( $setting_list_half_parsed as $setting_list_half_parsed_element){
							
							$half_parsed_id = substr( $setting_list_half_parsed_element, 0, strpos( $setting_list_half_parsed_element, "="));
							$half_parsed_value = substr( $setting_list_half_parsed_element, strpos( $setting_list_half_parsed_element, "=")+1, strlen( $setting_list_half_parsed_element));
						
							$setting_list[$half_parsed_id] = $half_parsed_value;
						
						}
						
					}
					
					$settings_in_group -> drawList( $settings_result['setting_title'], 'setting_'.$settings_result['setting_setting'], $setting_list, $settings_result['setting_value'], $settings_result['setting_info']);
					
				break;
				
				case 'yes-no':
					
					/**
					 * yes-no type field
					 */
					
					$settings_in_group -> drawYesNo( $settings_result['setting_title'], 'setting_'.$settings_result['setting_setting'], $settings_result['setting_value'], $settings_result['setting_info']);
					
				break;
				
			}
			
		}
			
		/**
		 * close and draw list
		 */
			
		$settings_in_group -> closeTable();
		
		/**
		 * new group button
		 */
			
		if ( $this -> group_number == count( $this -> groups))
			$settings_in_group -> drawButton( $language -> getString( 'acp_settings_subsection_show_group_change'));
				
		/**
		 * group title
		 */
		
		if ( $session -> user['user_is_root']){
			
			$group_edit_link['act'] = 'ops_list';
			$group_edit_link['do'] = 'edit_group';
			$group_edit_link['group'] = $this -> group;
			
			$group_delete_link['act'] = 'ops_list';
			$group_delete_link['do'] = 'delete_group';
			$group_delete_link['group'] = $this -> group;
						
			$group_resync_link['act'] = 'ops_list';
			$group_resync_link['do'] = 'recount_group';
			$group_resync_link['group'] = $this -> group;
					
			$group_title = '<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td>'.$this -> group_title.'</td>
			<td nowrap="nowrap" style="text-align: right">
			<a href="'.parent::adminSectionLink( parent::getId(), $group_edit_link).'">'.$style -> drawImage( 'edit', $language -> getString( 'edit')).'</a>
			<a href="'.parent::adminSectionLink( parent::getId(), $group_delete_link).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a>
			<a href="'.parent::adminSectionLink( parent::getId(), $group_resync_link).'">'.$style -> drawImage( 'resync', $language -> getString( 'refresh')).'</a>
			</td>
			</tr>
			</table>';
		
		}else{
			
			$group_title = $this -> group_title;
		
		}
			
		parent::draw( $style -> drawFormBlock( $group_title, $settings_in_group -> display()));
		
	}
	
	function act_edit_group(){
		
		/**
		 * include classes
		 */
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check if group is specified, and we can edit it
		 */
		
		if ( isset( $_GET['group']) && !empty( $_GET['group'])){
			
			$group_to_edit = $_GET['group'];
			settype( $group_to_edit, 'integer');
			
			/**
			 * select group from mysql
			 */
			
			$settings_group_query = $mysql -> query( "SELECT * FROM settings_groups WHERE `settings_group_id` = '$group_to_edit'"); 
			if ( $group_result = mysql_fetch_array( $settings_group_query, MYSQL_ASSOC)){
				
				$group_result = $mysql -> clear( $group_result);
				
				/**
				 * action link
				 */
				
				$action_link['act'] = 'ops_list';
				$action_link['do'] = 'update_group';
				$action_link['group'] = $group_to_edit;
				
				/**
				 * begin module
				 */
				
				$group_title = $group_result['settings_group_title'];
				$group_info = $group_result['settings_group_info'];
				$group_key = $group_result['settings_group_key'];
				$group_hidden = $group_result['settings_group_hidden'];
				
				/**
				 * actions and forms
				 */
				
				if ( isset( $_GET['do']) && $_GET['do'] == 'update_group'){
					
					/**
					 * if we are in this loop, that means, we have to repeat this action. So retake values from posts
					 */
					
					$group_title = $strings -> inputClear( $_POST[ 'group_title'], false);
					$group_info = $strings -> inputClear( $_POST[ 'group_info']);
					$group_key = $this -> groupTitleNormalize( $strings -> inputClear( $_POST[ 'group_key'], false));
					$group_hidden = $strings -> inputClear( $_POST[ 'group_hidden'], false);
					
				}
				
				/**
				 * draw form
				 */
				
				$new_group_form = new form();
				$new_group_form -> openForm( parent::adminSectionLink( parent::getId(), $action_link));
				$new_group_form -> openOpTable();
				
				$new_group_form -> drawTextInput( $language -> getString( 'acp_settings_subsection_new_group_title'), 'group_title', $group_title, $language -> getString( 'acp_settings_subsection_new_group_title_help'));
				$new_group_form -> drawTextBox( $language -> getString( 'acp_settings_subsection_new_group_info'), 'group_info', $strings -> outputClear($group_info, true), $language -> getString( 'acp_settings_subsection_new_group_info_help'));
				$new_group_form -> drawTextInput( $language -> getString( 'acp_settings_subsection_new_group_key'), 'group_key', $group_key, $language -> getString( 'acp_settings_subsection_new_group_key_help'));
				$new_group_form -> drawYesNo( $language -> getString( 'acp_settings_subsection_new_group_hide'), 'group_hidden', $group_hidden, $language -> getString( 'acp_settings_subsection_new_group_hide_help'));
				
				$new_group_form -> closeTable();
				$new_group_form -> drawButton( $language -> getString( 'acp_settings_subsection_edit_group_save'));
				$new_group_form -> closeForm();
				
				/**
				 * display it
				 */
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_edit_group_form_title'), $new_group_form -> display()));
				
				
			}else{
				
				/**
				 * group not found, draw error
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_edit_group_form_title'), $language -> getString( 'acp_settings_subsection_edit_group_notfound')));
				$this -> drawGroupsList();
			
			}
			
		}else{
			
			/**
			 * group not set, draw error
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_edit_group_form_title'), $language -> getString( 'acp_settings_subsection_edit_group_notarget')));
			$this -> drawGroupsList();
			
		}
		
	}
	
	function groupTitleNormalize( $title){
		
		/**
		 * trim
		 */
		
		$title = trim($title);
		
		/**
		 * remowe whitespaces
		 */
		
		while ( strstr( $title, "  ") != false)
			$title = str_replace( "  ", " ", $title);
			
		$title = str_replace( " ", "_", $title);
		
		$title = strtolower( $title);
		
		$title = str_replace( "#", "", $title);
		
		return $title;
		
	}
	
}

?>