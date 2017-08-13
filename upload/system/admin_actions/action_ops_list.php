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
|	Show ops List
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class action_ops_list extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * proper sub-actions
		 */
		
		$proper_does = array( 'update_group');
		$proper_repeats = array();
		
		if ( $session -> user['user_is_root']){
			
			$proper_does = array( 'add_group', 'update_group', 'recount_group', 'edit_group', 'delete_group');
			$proper_repeats = array( 'new_group', 'edit_group');
			
			
		}
		
		/**
		 * does
		 */
		
		if ( isset( $_GET['do']) && in_array( $_GET['do'], $proper_does)){
			
			switch ( $_GET['do']){
			
				case 'add_group':
				
					if ( $session -> checkForm()){
						
						/**
						 * add new group
						 */
						
						$group_title = $strings -> inputClear( $_POST[ 'group_title'], false);
						$group_info = $strings -> inputClear( $_POST[ 'group_info']);
						$group_key = $this -> groupTitleNormalize( $strings -> inputClear( $_POST[ 'group_key'], false));
						$group_hidden = $strings -> inputClear( $_POST[ 'group_hidden'], false);
						
						if ( empty( $group_title)){
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_new_group_form_title'), $language -> getString( 'acp_settings_subsection_new_group_create_empty_title')));
							
							$repeat_act = 'edit_group';
							
						}else if ( empty( $group_key)){
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_new_group_form_title'), $language -> getString( 'acp_settings_subsection_new_group_create_empty_key')));
											
							$repeat_act = 'edit_group';
							
						}else{
							
							/**
							 * lets put new group into mysql
							 */
							
							$new_group_ar['settings_group_title'] = $group_title;
							$new_group_ar['settings_group_info'] = $group_info;
							$new_group_ar['settings_group_key'] = $group_key;
							$new_group_ar['settings_group_hidden'] = $group_hidden;
							
							/**
							 * insert it
							 */
							
							$mysql -> insert ( $new_group_ar, 'settings_groups');
							
							/**
							 * draw message
							 */
							
							parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_new_group_form_title'), $language -> getString( 'acp_settings_subsection_new_group_create_done')));
						
							/**
							 * add log
							 */
								
							$log_keys = array( 'settings_group_name' => $group_title);
							
							$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_new_group_create_log'), $log_keys);
														
						}
				
					}
						
				break;
				
				case 'update_group':
					
					/**
					 * edit existing group group
					 */
					
					if ( $session -> checkForm()){
						
						if ( isset( $_GET['group']) && !empty( $_GET['group'])){
							
							$group_to_change = $_GET['group'];
							settype( $group_to_change, 'integer');
							
							$group_title = $strings -> inputClear( $_POST[ 'group_title'], false);
							$group_info = $strings -> inputClear( $_POST[ 'group_info']);
							$group_key = $this -> groupTitleNormalize( $strings -> inputClear( $_POST[ 'group_key'], false));
							$group_hidden = $strings -> inputClear( $_POST[ 'group_hidden'], false);
							
							if ( empty( $group_title)){
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_edit_group_form_title'), $language -> getString( 'acp_settings_subsection_new_group_create_empty_title')));
								
								$repeat_act = 'new_group';
								
							}else if ( empty( $group_key)){
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_settings_subsection_edit_group_form_title'), $language -> getString( 'acp_settings_subsection_new_group_create_empty_key')));
												
								$repeat_act = 'new_group';
								
							}else{
								
								/**
								 * lets put new group into mysql
								 */
								
								$change_group_ar['settings_group_title'] = $group_title;
								$change_group_ar['settings_group_info'] = $group_info;
								$change_group_ar['settings_group_key'] = $group_key;
								$change_group_ar['settings_group_hidden'] = $group_hidden;
								
								/**
								 * insert it
								 */
								
								$mysql -> update ( $change_group_ar, 'settings_groups', "`settings_group_id` = '$group_to_change'");
								
								/**
								 * draw message
								 */
								
								parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_edit_group_form_title'), $language -> getString( 'acp_settings_subsection_edit_group_done')));
								
								/**
								 * add log
								 */
									
								$log_keys = array( 'settings_group_id' => $group_to_change);
								
								$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_edit_group_log'), $log_keys);
											
							}
						
						}
						
					}
					
				break;
				
				case 'delete_group':
					
					if ( isset( $_GET['group'])){
					
						$group_to_delete = $_GET['group'];
						
						settype( $group_to_delete, 'integer');
						
						$mysql -> delete( 'settings_groups', "`settings_group_id` = '$group_to_delete'");
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_delete_group'), $language -> getString( 'acp_settings_subsection_delete_group_done')));
						
						/**
						 * add log
						 */
							
						$log_keys = array( 'settings_group_id' => $group_to_delete);
						
						$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_delete_group_log'), $log_keys);
						
					}
					
				break;
				
				case  'recount_group':
					
					if ( isset( $_GET['group'])){
					
						$group_to_count = $_GET['group'];
						
						settype( $group_to_count, 'integer');
						
						$settings_in_group['settings_group_settings'] = $mysql -> countRows( 'settings', "`setting_group` = '$group_to_count'");
						$mysql -> update( $settings_in_group, 'settings_groups', "`settings_group_id` = '$group_to_count'");
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_settings_subsection_refresh_group'), $language -> getString( 'acp_settings_subsection_refresh_group_done')));
					
						/**
						 * add log
						 */
							
						$log_keys = array( 'settings_group_id' => $group_to_count);
						
						$logs -> addAdminLog( $language -> getString( 'acp_settings_subsection_refresh_group_log'), $log_keys);
						
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
			
			$this -> drawGroupsList();
		
		}
		
	}
	function drawGroupsList(){
		
		/**
		 * include classes
		 */
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * add positon to path
		 */
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_ops_list_title'), parent::adminSectionLink(parent::getId()));
		
		/**
		 * set title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_settings_subsection_ops_list_title'));
		
		/**
		 * settings groups
		 */
		
		$settings_groups = new form();
		
		/**
		 * open form for new groups
		 */
				
		$settings_groups -> openForm( parent::adminSectionLink( 'show_group'));
		
		$settings_groups -> openOpTable();
		
		$settings_groups_list = array();
		
		/**
		 * select from sql settings groups
		 */
		
		$settings_query = $mysql -> query( "SELECT * FROM settings_groups WHERE settings_group_hidden = '0' ORDER BY settings_group_title");
		
		while ( $settings_result = mysql_fetch_array( $settings_query, MYSQL_ASSOC)){
			
			/**
			 * clear result
			 */
			
			$settings_result = $mysql -> clear( $settings_result);
			
			/**
			 * add to results
			 */
			
			$settings_groups_list[$settings_result['settings_group_id']] = $settings_result['settings_group_title'].' ('.$settings_result['settings_group_settings'].')';
			
		}
			
		$settings_groups -> drawMultiList( $language -> getString( 'acp_settings_subsection_ops_list_select'), 'groups_to_change[]', $settings_groups_list, 0, $language -> getString( 'acp_settings_subsection_ops_list_select_help'), 18);
		
		/**
		 * close and draw list
		 */
			
		$settings_groups -> closeTable();
		
		/**
		 * new group button
		 */
			
		$settings_groups -> drawButton( $language -> getString( 'acp_settings_subsection_ops_list_change'));
		
		/**
		 * close form
		 */
		
		$settings_groups -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_ops_list_title'), $settings_groups -> display()));
		
	}
	
	function act_new_group(){
		
		/**
		 * include classes
		 */
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * add positon to path
		 */
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_ops_list_title'),  parent::adminSectionLink(parent::getId()));
		
		$path_url = array( 'act' => 'ops_list', 'do' => 'new_group');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_new_group_form_title'),  parent::adminSectionLink(parent::getId(), $path_url));
		
		/**
		 * action link
		 */
		
		$action_link['act'] = 'ops_list';
		$action_link['do'] = 'add_group';
		
		/**
		 * begin module
		 */
		
		$group_title = '';
		$group_info = '';
		$group_key = '';
		$group_hidden = false;
		
		/**
		 * actions and forms
		 */
		
		if ( isset( $_GET['do']) && $_GET['do'] == 'add_group' && $session -> checkForm()){
			
			/**
			 * if we are in this loop, that means, we have to repeat this action. So retake values from posts
			 */
			
			$group_title = stripslashes( $strings -> inputClear( $_POST[ 'group_title'], false));
			$group_info = stripslashes( $strings -> inputClear( $_POST[ 'group_info']));
			$group_key = $this -> groupTitleNormalize( $strings -> inputClear( $_POST[ 'group_key'], false));
			$group_hidden = stripslashes( $strings -> inputClear( $_POST[ 'group_hidden'], false));
			
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
		$new_group_form -> drawButton( $language -> getString( 'acp_settings_subsection_new_group_create'));
		$new_group_form -> closeForm();
		
		/**
		 * display it
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_settings_subsection_new_group_form_title'), $new_group_form -> display()));
		
	}
	
	function act_edit_group(){
		
		/**
		 * include classes
		 */
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * add positon to path
		 */
		
		$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_ops_list_title'),  parent::adminSectionLink(parent::getId()));
				
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
				 * add breadcrumb
				 */
				
				$path_url = array( 'act' => 'ops_list', 'do' => 'edit_group', 'group' => $group_to_edit);
				
				$path -> addBreadcrumb( $language -> getString( 'acp_settings_subsection_edit_group_form_title'),  parent::adminSectionLink(parent::getId(), $path_url));
				
				/**
				 * set page title
				 */
				
				$output -> setTitle( $language -> getString( 'acp_settings_subsection_edit_group_form_title'));
				
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
					
					$group_title = stripslashes( $strings -> inputClear( $_POST[ 'group_title'], false));
					$group_info = stripslashes( $strings -> inputClear( $_POST[ 'group_info']));
					$group_key = $this -> groupTitleNormalize( $strings -> inputClear( $_POST[ 'group_key'], false));
					$group_hidden = stripslashes( $strings -> inputClear( $_POST[ 'group_hidden'], false));
					
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