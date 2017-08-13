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
|	Edit setting
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class action_edit_setting extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * add breadcrumbs
		 */
				
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_edit_setting'), ROOT_PATH.ACP_PATH.'index.php?section=settings&act=edit_setting');
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_settings_subsection_edit_setting'));
		
		/**
		 * proper sub-actions
		 */
		
		$proper_does = array( 'change_setting');
		$proper_repeats = array( 'select_setting', 'edit_setting');
		
		/**
		 * now check if group to change is specified
		 */
		
		if ( (isset( $_GET['setting']) && !empty( $_GET['setting'])) || (isset( $_POST['setting']) && !empty( $_POST['setting']))){
			
			if ( isset( $_GET['setting']) && !empty( $_GET['setting'])){
				
				$setting_to_change = $_GET['setting'];
				
			}else{
				
				$setting_to_change = $_POST['setting'];
								
			}
						
			$this -> setting = uniSlashes( $setting_to_change);
			
			/**
			 * now select it from mysql
			 */
		
			$setting_query = $mysql -> query( "SELECT `setting_setting` FROM settings WHERE `setting_setting` = '$setting_to_change'");
			if ( $setting_result = mysql_fetch_array( $setting_query, MYSQL_ASSOC)){
				
				/**
				 * group found
				 */
				
				$setting_result = $mysql -> clear( $setting_result);
				
				$this -> setting_title = $setting_result['setting_setting_setting'];
				
				/**
				 * does
				 */
				
				if ( isset( $_GET['do']) && in_array( $_GET['do'], $proper_does)){
					
					switch ( $_GET['do']){
					
						case 'change_setting':
							
							if ( $session -> checkForm()){
								
								/**
								 * change setting action
								 */
							
								$setting_types['info'] = 'info';
								$setting_types['thick'] = 'thick';
								$setting_types['text-input'] = 'text-input';
								$setting_types['text-box'] = 'text-box';
								$setting_types['text-editor'] = 'text-editor';
								$setting_types['list'] = 'list';
								$setting_types['yes-no'] = 'yes-no';
								
								if ( empty( $_POST[ 'setting_title'])){
									
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_edit_setting'), $language -> getString( 'acp_settings_subsection_edit_setting_title_empty')));
								
								}else if ( empty( $_POST[ 'setting_type']) || !key_exists( $_POST[ 'setting_type'], $setting_types)){
									
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_edit_setting'), $language -> getString( 'acp_settings_subsection_edit_setting_type_wrong')));
									
								}else{
												
									/**
									 * and update it in mysql
									 */
									
									$setting_group = $_POST[ 'setting_group'];
									$setting_position = $_POST[ 'setting_position'];
									
									settype( $setting_group, 'integer');
									settype( $setting_position, 'integer');
									
									$setting_update_sql['setting_title'] = $strings -> inputClear( $_POST[ 'setting_title'], false);
									$setting_update_sql['setting_info'] = $strings -> inputClear( $_POST[ 'setting_info'], false);
									$setting_update_sql['setting_group'] = $setting_group;
									$setting_update_sql['setting_position'] = $setting_position;
									$setting_update_sql['setting_type'] = $strings -> inputClear( $_POST[ 'setting_type']);
									$setting_update_sql['setting_value'] = $strings -> inputClear( $_POST[ 'setting_value']);
									$setting_update_sql['setting_value_default'] = $strings -> inputClear( $_POST[ 'setting_value_default']);
									$setting_update_sql['setting_value_type'] = $strings -> inputClear( $_POST[ 'setting_value_type']);
									$setting_update_sql['setting_extra'] = $strings -> inputClear( $_POST[ 'setting_extra']);
									$setting_update_sql['setting_subgroup_open'] = $strings -> inputClear( $_POST[ 'setting_subgroup'], false);
									
									$mysql -> update( $setting_update_sql, 'settings', "`setting_setting` = '".$setting_to_change."'");
		
									/**
									 * and in actual page
									 */
									
									$settings[$settings_result['setting_setting']] = $new_setting_value;
														
									/**
									 * clear cache
									 */
									
									$cache -> flushCache( 'system_settings');
												
									/**
									 * draw message
									 */
									
									parent::draw( $style -> drawInfoBlock( $language -> getString( 'information', true), $language -> getString( 'acp_settings_subsection_edit_setting_changed')));
						
									/**
									 * add log
									 */
										
									$log_keys = array( 'setting_name' => $setting_to_change);
									
									$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_edit_setting_changed_log'), $log_keys);
							
								}
							
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
					
					$this -> act_edit_setting();
				
				}
		
			}else{
				
				/**
				 * group not found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_edit_setting'), $language -> getString( 'acp_settings_subsection_edit_setting_not_found')));
							
			}
				
		}else{
			
			$this -> select_setting();
						
		}
		
	}
	
	function select_setting(){
		
		/**
		 * include classes
		 */
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * start drawing form
		 */
		
		$select_setting_list = new form();
		
		/**
		 * open form for new groups
		 */

		$edit_setting_link['do'] = 'edit_setting';	
		
		$select_setting_list -> openForm( parent::adminSectionLink( parent::getId(), $edit_setting_link));
		
		$select_setting_list -> openOpTable();
		
		/**
		 * select from sql settings groups
		 */
		
		$settings_list = array();
		
		$settings_query = $mysql -> query( "SELECT * FROM settings");
		
		while ( $settings_result = mysql_fetch_array( $settings_query, MYSQL_ASSOC)){
			
			/**
			 * clear result
			 */
			
			$settings_result = $mysql -> clear( $settings_result);
			
			$settings_list[$settings_result['setting_setting']] = $settings_result['setting_setting'];
			
		}
		
		/**
		 * draw list
		 */
			
		$select_setting_list -> drawList( $language -> getString('acp_settings_subsection_edit_setting_setting'), 'setting', $settings_list);
		
		/**
		 * close and draw list
		 */
			
		$select_setting_list -> closeTable();
		
		/**
		 * select setting button
		 */
			
		$select_setting_list -> drawButton( $language -> getString( 'acp_settings_subsection_edit_setting_button'));
		
		/**
		 * close form
		 */
		
		$select_setting_list -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_edit_setting'), $select_setting_list -> display()));
		
	}
	
	function act_edit_setting(){
		
		/**
		 * include classes
		 */
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
				
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_edit_setting').': '.$this -> setting, ROOT_PATH.ACP_PATH.'index.php?section=settings&act=edit_setting&do=edit_setting&setting='.$this -> setting);
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_settings_subsection_edit_setting').': '.$this -> setting);
		
		/**
		 * settings groups
		 */
		
		$edit_setting = new form();
		
		/**
		 * open form for new groups
		 */
				
		$change_setting_link['do'] = 'change_setting';
		$change_setting_link['setting'] = $this -> setting;
		
		$edit_setting -> openForm( parent::adminSectionLink( parent::getId(), $change_setting_link));
		
		$edit_setting -> openOpTable();
		
		/**
		 * select from sql settings groups
		 */
		
		$setting_query = $mysql -> query( "SELECT * FROM settings WHERE `setting_setting` = '".$this -> setting."'");
		
		if ( $setting_result = mysql_fetch_array( $setting_query, MYSQL_ASSOC)){
			
			/**
			 * clear result
			 */
			
			$setting_result = $mysql -> clear( $setting_result);
					
		}
		
		if ( isset( $_GET['do']) && $_GET['do'] == 'change_setting'){
			
			$setting_result['setting_title'] = stripslashes( $strings -> inputClear( $_POST['setting_title'], false));
			$setting_result['setting_info'] = stripslashes(  $strings -> inputClear( $_POST['setting_info'], false));
			$setting_result['setting_group'] = stripslashes(  $strings -> inputClear( $_POST['setting_group'], false));
			$setting_result['setting_position'] = stripslashes(  $strings -> inputClear( $_POST['setting_position'], false));
			$setting_result['setting_type'] = stripslashes(  $strings -> inputClear( $_POST['setting_type'], false));
			$setting_result['setting_value'] = stripslashes(  $strings -> inputClear( $_POST['setting_value'], false));
			$setting_result['setting_value_default'] = stripslashes(  $strings -> inputClear( $_POST['setting_value_default'], false));
			$setting_result['setting_value_type'] = stripslashes(  $strings -> inputClear( $_POST['setting_value_type'], false));
			$setting_result['setting_extra'] = stripslashes(  $strings -> inputClear( $_POST['setting_extra'], false));
			$setting_result['setting_subgroup_open'] = stripslashes(  $strings -> inputClear( $_POST['setting_subgroup'], false));
			
		}
		
		/**
		 * setting title
		 */
		
		$edit_setting -> drawTextInput( $language -> getString( 'acp_settings_subsection_edit_setting_title'), 'setting_title', $strings -> outputClear( $setting_result['setting_title']));
		
		/**
		 * setting info
		 */
		
		$edit_setting -> drawTextBox( $language -> getString( 'acp_settings_subsection_edit_setting_info'), 'setting_info', $strings -> outputClear( $setting_result['setting_info']));
		
		/**
		 * setting group
		 */
		
		$groups_query = $mysql -> query( "SELECT settings_group_id, settings_group_title FROM settings_groups ORDER BY `settings_group_title`");

		$settings_groups = array();
		
		while ( $group_result = mysql_fetch_array( $groups_query, MYSQL_ASSOC)){
			
			$group_result = $mysql -> clear( $group_result);
			
			$settings_groups[$group_result['settings_group_id']] = $group_result['settings_group_title'];
			
		}
		
		$edit_setting -> drawList( $language -> getString( 'acp_settings_subsection_edit_setting_group'), 'setting_group', $settings_groups, $setting_result['setting_group']);
		
		/**
		 * setting position
		 */
		
		$edit_setting -> drawTextInput( $language -> getString( 'acp_settings_subsection_edit_setting_position'), 'setting_position',  $strings -> outputClear( $setting_result['setting_position']));
				
		/**
		 * setting type
		 */
		
		$setting_types['info'] = 'info';
		$setting_types['thick'] = 'thick';
		$setting_types['text-input'] = 'text-input';
		$setting_types['text-box'] = 'text-box';
		$setting_types['text-editor'] = 'text-editor';
		$setting_types['list'] = 'list';
		$setting_types['yes-no'] = 'yes-no';
		
		$edit_setting -> drawList( $language -> getString( 'acp_settings_subsection_edit_setting_type'), 'setting_type', $setting_types, $setting_result['setting_type']);
		
		/**
		 * setting value
		 */
		
		$edit_setting -> drawTextBox( $language -> getString( 'acp_settings_subsection_edit_setting_value'), 'setting_value', $strings -> outputClear( $setting_result['setting_value']));
		
		/**
		 * setting value default
		 */
		
		$edit_setting -> drawTextBox( $language -> getString( 'acp_settings_subsection_edit_setting_value_default'), 'setting_value_default', $strings -> outputClear( $setting_result['setting_value_default']));
				
		/**
		 * setting value_type
		 */
		
		$edit_setting -> drawTextInput( $language -> getString( 'acp_settings_subsection_edit_setting_value_type'), 'setting_value_type',  $strings -> outputClear( $setting_result['setting_value_type']));
		
		/**
		 * setting extra
		 */
		
		$edit_setting -> drawTextBox( $language -> getString( 'acp_settings_subsection_edit_setting_extra'), 'setting_extra', $strings -> outputClear( $setting_result['setting_extra']));
		
		/**
		 * setting subgroup open
		 */
		
		$edit_setting -> drawTextInput( $language -> getString( 'acp_settings_subsection_edit_setting_subgroup'), 'setting_subgroup',  $strings -> outputClear( $setting_result['setting_subgroup_open']));
		
		/**
		 * close and draw list
		 */
			
		$edit_setting -> closeTable();
		
		/**
		 * new group button
		 */
			
		$edit_setting -> drawButton( $language -> getString( 'acp_settings_subsection_edit_setting_button'));
		
		/**
		 * close form
		 */
		
		$edit_setting -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_edit_setting').': '.$this -> setting, $edit_setting -> display()));
		
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