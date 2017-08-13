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
|	Acp Forums and Posts Page
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class acp_section_forums extends acp_section{
	
	function __construct(){
				
		/**
		 * include global classes pointers
		 */
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * begin
		 */
		
		$correct_acts = array( 'boards', 'new_board', 'add_board', 'edit_board', 'change_board', 'delete_board', 'kill_board', 'boards_perms', 'edit_perms', 'change_perms', 'topics_prefixes', 'topics_prefixes_new', 'topics_prefixes_add', 'topics_prefixes_edit', 'topics_prefixes_change', 'topics_prefixes_delete', 'bbtags_manage', 'bbtags_new', 'bbtags_add', 'bbtags_edit', 'bbtags_change', 'bbtags_delete', 'atts_types', 'new_att_type', 'add_att_type', 'edit_att_type', 'change_att_type', 'del_att_type', 'orpchans');
		
		if ( !isset( $_GET['act']) || !in_array( $_GET['act'], $correct_acts)){
			$current_act = 	$correct_acts[0];
		}else{
			$current_act = $_GET['act'];
		}
		
		/**
		 * now array containing subsections
		 */
		
		$subsections_list['boards'] = 'boards';
		$subsections_list['bbtags'] = 'bbtags';
		$subsections_list['attachments'] = 'attachments';
		
		/**
		 * and subsections list
		 */
	
		$subsections_elements_list['boards'] = 'boards';
		$subsections_elements_list['new_board'] = 'boards';
		$subsections_elements_list['boards_perms'] = 'boards';
		$subsections_elements_list['topics_prefixes'] = 'boards';
		
		$subsections_elements_list['bbtags_manage'] = 'bbtags';
		$subsections_elements_list['bbtags_new'] = 'bbtags';
		
		$subsections_elements_list['atts_types'] = 'attachments';
		$subsections_elements_list['orpchans'] = 'attachments';
		
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
			
			case 'boards':
				
				/**
				 * forums manager
				 */
				
				$this -> act_boards_manage();
				
			break;
			
			case 'new_board':
				
				$this -> act_new_forum();
				
			break;
			
			case 'add_board':
				
				if ( $session -> checkForm()){
					
					$new_forum = $forums -> newForum( $_POST['forum_name'], $_POST['forum_image'], $_POST['forum_info'], $_POST['forum_parent'], $_POST['forum_category'], $_POST['forum_gidelines_text'], $_POST['forum_gidelines_url'], $_POST['forum_url'], $_POST['forum_redirect_count'], $_POST['forum_increase_counter'], $_POST['forum_allow_bbcode'], $_POST['forum_allow_surveys'], $_POST['forum_allow_quickreply'], $_POST['forum_force_order'], $_POST['forum_force_direction'], $_POST['forum_pruning'], $_POST['forum_prune_days'], $_POST['forum_closed']);
					
					if ( $new_forum){
						
						/**
						 * set perms
						 */
						
						$forums -> setForumPerms( mysql_insert_id());
						
						$cache -> flushCache( 'forums');
						$cache -> flushCache( 'permissions');
			
						/**
						 * we successfully added new forum
						 */
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_new_board_title'), $language -> getString( 'acp_forums_subsection_new_board_saved')));
						
						/**
						 * draw boards
						 */
						
						$this -> act_boards_manage();
						
					}else{
						
						/**
						 * we have error, draw it
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_new_board_title'), $forums -> getErrorMessage()));
						
						/**
						 * and repeat new board action
						 */
						
						$this -> act_new_forum();
						
					}
				
				}else{
					
					$this -> act_new_forum();
					
				}
					
			break;
			
			case 'edit_board':
				
				/**
				 * run board editor
				 */
				
				$this -> act_edit_board();
				
			break;
			
			case 'change_board':
				
				/**
				 * get forum to admin
				 */
				
				$forum_to_admin = $_GET['board'];
				
				settype( $forum_to_admin, 'integer');
				
				if ( $forum_to_admin > 0){
					
					/**
					 * proper forum id
					 * select forum from MySQL
					 */
					
					$forum_query = $mysql -> query( "SELECT * FROM forums WHERE `forum_id` = '$forum_to_admin'");
					
					if ( $forum_result = mysql_fetch_array( $forum_query, MYSQL_ASSOC)){
						
						/**
						 * forum found, update it
						 */
						
						$new_forum = $forums -> newForum( $_POST['forum_name'], $_POST['forum_image'], $_POST['forum_info'], $_POST['forum_parent'], $_POST['forum_category'], $_POST['forum_gidelines_text'], $_POST['forum_gidelines_url'], $_POST['forum_url'], $_POST['forum_redirect_count'], $_POST['forum_increase_counter'], $_POST['forum_allow_bbcode'], $_POST['forum_allow_surveys'], $_POST['forum_allow_quickreply'], $_POST['forum_force_order'], $_POST['forum_force_direction'], $_POST['forum_pruning'], $_POST['forum_prune_days'], $_POST['forum_closed'], $forum_to_admin, $forum_result['forum_parent']);
					
						if ( $new_forum){
							
							/**
							 * set perms
							 */
							
							$forums -> setForumPerms( $forum_to_admin);
							
							$cache -> flushCache( 'forums');
							$cache -> flushCache( 'permissions');
							
							/**
							 * add log
							 */
							
							$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_boards_edit_log'), array( 'edit_forum_id' => $forum_to_admin));
							
							/**
							 * we successfully added new forum
							 */
							
							parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_boards_edit'), $language -> getString( 'acp_forums_subsection_boards_edit_done')));
							
							/**
							 * draw boards
							 */
							
							$this -> act_boards_manage();
							
						}else{
							
							/**
							 * we have error, draw it
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_edit'), $forums -> getErrorMessage()));
							
							/**
							 * and repeat new board action
							 */
							
							$this -> act_edit_board();
														
						}
						
					}else{
						
						/**
						 * board not found
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_edit'), $language -> getString( 'acp_forums_subsection_board_target_notfound')));
						
						$this -> act_boards_manage();
						
					}
					
				}else{
					
					/**
					 * wrong forum to edit
					 */
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_edit'), $language -> getString( 'acp_forums_subsection_board_target_wrong')));
					
					$this -> act_boards_manage();
					
				}
				
			break;
			
			
			case 'delete_board':
				
				/**
				 * run board editor
				 */
				
				$this -> act_delete_board();
				
			break;
			
			case 'kill_board':
				
				if ( $session -> checkForm()){
										
					/**
					 * get forum to admin
					 */
					
					$forum_to_admin = $_GET['board'];
					
					settype( $forum_to_admin, 'integer');
					
					if ( $forum_to_admin > 0){
						
						/**
						 * proper forum id
						 * select forum from MySQL
						 */
						
						$forum_query = $mysql -> query( "SELECT * FROM forums WHERE `forum_id` = '$forum_to_admin'");
						
						if ( $forum_result = mysql_fetch_array( $forum_query, MYSQL_ASSOC)){
							
							/**
							 * forum found, delete it
							 */
							
							$forum_replace = $_POST['forum_replace'];
							
							settype( $forum_replace, 'integer');
							
							/**
							 * set replacer
							 */
							
							$replacer_exists = false;
							
							if( key_exists( $forum_replace, $forums -> getForumsList( $forum_to_admin))){
								
								$replacer_exists = true;
								
							}
							
							$cache -> flushCache( 'forums');
						
							/**
							 * do action
							 */
							
							if ( $forum_replace == 0){
																
								/**
								 * build up an list of subboards
								 */
								
								$boards_to_kill = array( $forum_to_admin);
								
								foreach ( $forums -> forums_list as $forum_id => $forum_ops){
									
									$current_pos = $forum_ops['forum_parent'];
									
									while ( $current_pos != 0){
										
										if ( in_array( $current_pos, $boards_to_kill)){
											
											$boards_to_kill[] = $forum_id;
											
										}
										
										$current_pos = $forums -> forums_list[$forum_ops['forum_parent']]['forum_id'];
										
									}
									
								}
								
								/**
								 * delete forum and its content
								 */
								
								$mysql -> delete( "forums", "`forum_id` IN (".join( ",", $boards_to_kill).")");
								
								/**
								 * select topics
								 */
								
								$topics_to_kill_query = $mysql -> query( "SELECT topic_id FROM topics WHERE `topic_forum_id` IN (".join( ",", $boards_to_kill).")");
								
								$topics_found = false;
								$topics_to_kill = array();
								
								while ( $topics_to_kill_result = mysql_fetch_array( $topics_to_kill_query, MYSQL_ASSOC)){
									
									$topics_found = true;
									$topics_to_kill[] = $topics_to_kill_result['topic_id'];
									
								}
								
								$mysql -> delete( "topics", "`topic_forum_id` IN (".join( ",", $boards_to_kill).")");
								
								if ( $topics_found)
									$mysql -> delete( "posts", "`post_topic` IN (".join( ",", $topics_to_kill).")");
								
								/**
								 * end all
								 */
								
								$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_boards_delete_log'), array( 'edit_forum_id' => $forum_to_admin));
								
								parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_boards_delete'), $language -> getString( 'acp_forums_subsection_boards_delete_done')));
								
								$this -> act_boards_manage();
								
							}else if( $replacer_exists){
								
								/**
								 * delete forum, and move its content
								 */
								
								$mysql -> delete( "forums", "`forum_id` = '$forum_to_admin'");
								
								/**
								 * generate new positions
								 */
								
								$subforums_query = $mysql -> query( "SELECT `forum_id` FROM forums WHERE `forum_parent` = '$forum_to_admin' ORDER BY `forum_pos`");
								
								$next_free_pos = $forums -> getNextFreePos( $forum_replace);
								
								while ( $forum_replace_result = mysql_fetch_array( $subforums_query, MYSQL_NUM)){
									
									$mysql -> update( array( 'forum_parent' => $forum_replace, 'forum_pos' => $next_free_pos), "forums", "`forum_id` = '".$forum_replace_result[0]."'");
									
									$next_free_pos++;
									
								}
								
								/**
								 * and topics
								 */
								
								$mysql -> update( array( 'topic_forum_id' => $forum_replace), "topics", "`topic_forum_id` = '".$forum_replace_result[0]."'");
																	
								/**
								 * end all
								 */
								
								$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_boards_delete_log'), array( 'edit_forum_id' => $forum_to_admin));
								
								parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_boards_delete'), $language -> getString( 'acp_forums_subsection_boards_delete_done')));
								
								$this -> act_boards_manage();
								
							}else{
								
								/**
								 * replace notfound
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_delete'), $language -> getString( 'acp_forums_subsection_boards_delete_noreplace')));
							
								$this -> act_delete_board();
							
							}
							
						}else{
							
							/**
							 * board not found
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_delete'), $language -> getString( 'acp_forums_subsection_board_target_notfound')));
							
							$this -> act_boards_manage();
							
						}
						
					}else{
						
						/**
						 * wrong forum to edit
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_delete'), $language -> getString( 'acp_forums_subsection_board_target_wrong')));
						
						$this -> act_boards_manage();
						
					}
										
				}else{
				
					$this -> act_boards_manage();
				
				}
				
			break;
			
			case 'boards_mods':
				
				$this -> act_boards_mods();
				
			break;
			
			case 'boards_perms':
				
				$this -> act_forums_perms();
				
			break;
			
			case 'edit_perms':
				
				$this -> act_edit_perms();
				
			break;
			
			case 'change_perms':
				
				if ( $session -> checkForm()){
					
					/**
					 * get perms to admin
					 */
					
					$perms_to_admin = $_GET['perms'];
					settype( $perms_to_admin, 'integer');
					
					if ( $perms_to_admin > 0){
						
						/**
						 * select it
						 */
						
						$perms_query = $mysql -> query( "SELECT * FROM users_perms WHERE `users_perm_id` = '$perms_to_admin'");
						
						if ( $perms_result = mysql_fetch_array( $perms_query, MYSQL_ASSOC)){
				
							/**
							 * perm found
							 */
							
							$perm_name = $strings -> inputClear( $_POST['perms_name'], false);
							
							if ( strlen( $perm_name) == 0){
								
								/**
								 * name empty, error and return
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_perms_edit'), $language -> getString( 'acp_forums_subsection_boards_perms_edit_noname')));
								
								$this -> act_edit_perms();
								
							}else{
								
								/**
								 * all ok, update perms
								 */
								
								$mysql -> update( array( 'users_perm_name' => $perm_name), 'users_perms', "`users_perm_id` = '$perms_to_admin'");
								
								/**
								 * delete access data, we will overwrite it with new
								 */
								
								$mysql -> delete( 'forums_access', "`forums_acess_perms_id` = '$perms_to_admin'");
								
								$boards_query = $mysql -> query( "SELECT forum_id FROM forums");
				
								while ( $boards_result = mysql_fetch_array( $boards_query, MYSQL_ASSOC)){
									
									$boards_result = $mysql -> clear($boards_result);
									
									$board_id = $boards_result['forum_id'];
									
									$perms_entry = array(
										'forums_acess_perms_id' => $perms_to_admin,
										'forums_acess_forum_id' => $board_id,
										'forums_access_show_forum' => $_POST['forums_access_show_forum'][$board_id],
										'forums_access_show_topics' => $_POST['forums_access_show_topics'][$board_id],
										'forums_access_reply_topics' => $_POST['forums_access_reply_topics'][$board_id],
										'forums_access_start_topics' => $_POST['forums_access_start_topics'][$board_id],
										'forums_access_attachments_upload' => $_POST['forums_access_attachments_upload'][$board_id],
										'forums_access_attachments_download' => $_POST['forums_access_attachments_download'][$board_id]
									);
									
									/**
									 * make sure its safe
									 */
									
									foreach ( $perms_entry as $perms_entry_id => $perms_entry_value){
										
										$perms_entry_id = $strings -> inputClear( $perms_entry_id, false);
										$perms_entry_value = $strings -> inputClear( $perms_entry_value, false);
										
										$perms_entry[ $perms_entry_id] = $perms_entry_value;
										
									}
									
									/**
									 * insert
									 */
									
									$mysql -> insert( $perms_entry, 'forums_access');
									
								}
								
								$cache -> flushCache( 'permissions');
		
								/**
								 * log
								 */
								
								$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_boards_perms_edit_log'), array( 'changed_perms_id' => $perms_to_admin));
								
								/**
								 * message
								 */
								
								parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_boards_perms_edit'), $language -> getString( 'acp_forums_subsection_boards_perms_edit_done')));
								
								/**
								 * and manager
								 */
								
								$this -> act_forums_perms();
								
							}
							
						}else{
						
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_perms_edit'), $language -> getString( 'acp_forums_subsection_boards_perms_edit_notfound')));
						
							$this -> act_forums_perms();
							
						}
						
					}else{
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_perms_edit'), $language -> getString( 'acp_forums_subsection_boards_perms_edit_notarget')));
						
						$this -> act_forums_perms();
						
					}
				
				}else{
				
					$this -> act_forums_perms();
						
				}
				
			break;
			
			case 'topics_prefixes':
				
				$this -> act_topics_prefixes();
				
			break;
			
			case 'topics_prefixes_new':
			
				$this -> act_topics_prefixes_new();
				
			break;
			
			case 'topics_prefixes_add':
			
				/**
				 * check form
				 */
				
				if ( $session -> checkForm()){
					
					/**
					 * get values
					 */
					
					$prefix_name = ( $strings -> inputClear( $_POST['prefix_name'], false));
					$prefix_html = ( $strings -> inputClear( $_POST['prefix_html'], false));
					$prefix_pos = $_POST['prefix_pos'];
					$prefix_forums = $_POST['prefix_forums'];
					
					settype( $prefix_pos, 'integer');
					settype( $prefix_forums, 'array');
					
					/**
					 * check forums
					 */
					
					$forum_exists = true;
				
					foreach ( $prefix_forums as $forum_key => $forum_id){
						
						if ( !key_exists( $forum_id, $forums -> forums_list)){
							
							$forum_exists = false;
						
						}else{
							
							settype( $prefix_forums[$forum_key], 'integer');
							
						}
							
					}
					
					if ( count( $prefix_forums) == 0)
						$forum_exists = false;
					
					/**
					 * do error checking
					 */
					
					if ( strlen( $prefix_name) == 0){
						
						/**
						 * empty prefix name
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_new'), $language -> getString( 'acp_forums_subsection_topics_prefixes_name_empty')));
						
						$this -> act_topics_prefixes_new();
						
					}else if ( strlen( $prefix_html) == 0){
						
						/**
						 * empty prefix name
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_new'), $language -> getString( 'acp_forums_subsection_topics_prefixes_html_empty')));
						
						$this -> act_topics_prefixes_new();
						
					}else if ( !$forum_exists){
						
						/**
						 * empty prefix name
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_new'), $language -> getString( 'acp_forums_subsection_topics_prefixes_forums_empty')));
						
						$this -> act_topics_prefixes_new();
						
					}else{
						
						/**
						 * insert prefix
						 */
						
						$new_prefix_sql['topic_prefix_pos'] = $prefix_pos;
						$new_prefix_sql['topic_prefix_name'] = $prefix_name;
						$new_prefix_sql['topic_prefix_html'] = $prefix_html;
						$new_prefix_sql['topic_prefix_forums'] = join( ",", $prefix_forums);
						
						$mysql -> insert( $new_prefix_sql, 'topics_prefixes');
						
						$cache -> flushCache( 'prefixes');
						
						/**
						 * add log
						 */
						
						$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_topics_prefixes_forums_new_log'), array( 'prefix_name' => $prefix_name));
						
						/**
						 * draw message
						 */
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_new'), $language -> getString( 'acp_forums_subsection_topics_prefixes_forums_new_done')));
						
						$this -> act_topics_prefixes();
					
					}
					
				}else{
					
					$this -> act_topics_prefixes();
				
				}
				
			break;
			
			case 'topics_prefixes_edit':
				
				$this -> act_topics_prefixes_edit();
				
			break;
			
			case 'topics_prefixes_change':
			
				/**
				 * check form
				 */
				
				if ( $session -> checkForm()){
					
					/**
					 * get prefix to edit
					 */
					
					$prefix_to_edit = $_GET['prefix'];
					settype( $prefix_to_edit, 'integer');
					
					/**
					 * select event
					 */
					
					$event_query = $mysql -> query( "SELECT * FROM topics_prefixes WHERE `topic_prefix_id` = '$prefix_to_edit'");
					
					if ( $prefix_result = mysql_fetch_array( $event_query, MYSQL_ASSOC)){
				
						/**
						 * get values
						 */
						
						$prefix_name = ( $strings -> inputClear( $_POST['prefix_name'], false));
						$prefix_html = ( $strings -> inputClear( $_POST['prefix_html'], false));
						$prefix_pos = $_POST['prefix_pos'];
						$prefix_forums = $_POST['prefix_forums'];
						
						settype( $prefix_pos, 'integer');
						settype( $prefix_forums, 'array');
						
						/**
						 * check forums
						 */
						
						$forum_exists = true;
					
						foreach ( $prefix_forums as $forum_key => $forum_id){
							
							if ( !key_exists( $forum_id, $forums -> forums_list)){
								
								$forum_exists = false;
							
							}else{
								
								settype( $prefix_forums[$forum_key], 'integer');
								
							}
								
						}
						
						if ( count( $prefix_forums) == 0)
							$forum_exists = false;
						
						/**
						 * do error checking
						 */
						
						if ( strlen( $prefix_name) == 0){
							
							/**
							 * empty prefix name
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_edit'), $language -> getString( 'acp_forums_subsection_topics_prefixes_name_empty')));
							
							$this -> act_topics_prefixes_new();
							
						}else if ( strlen( $prefix_html) == 0){
							
							/**
							 * empty prefix name
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_edit'), $language -> getString( 'acp_forums_subsection_topics_prefixes_html_empty')));
							
							$this -> act_topics_prefixes_new();
							
						}else if ( !$forum_exists){
							
							/**
							 * empty prefix name
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_edit'), $language -> getString( 'acp_forums_subsection_topics_prefixes_forums_empty')));
							
							$this -> act_topics_prefixes_new();
							
						}else{
							
							/**
							 * insert prefix
							 */
							
							$new_prefix_sql['topic_prefix_pos'] = $prefix_pos;
							$new_prefix_sql['topic_prefix_name'] = $prefix_name;
							$new_prefix_sql['topic_prefix_html'] = $prefix_html;
							$new_prefix_sql['topic_prefix_forums'] = join( ",", $prefix_forums);
							
							$mysql -> update( $new_prefix_sql, 'topics_prefixes', "topic_prefix_id = '$prefix_to_edit'");
							
							$cache -> flushCache( 'prefixes');
							
							/**
							 * add log
							 */
							
							$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_topics_prefixes_forums_edit_log'), array( 'prefix_name' => $prefix_name));
							
							/**
							 * draw message
							 */
							
							parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_edit'), $language -> getString( 'acp_forums_subsection_topics_prefixes_forums_edit_done')));
							
							$this -> act_topics_prefixes();
						
						}
					
					}else{
						
						//not found. Draw message and jump to manager
						
						parent::draw( $style ->drawErrorBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_edit'), $language -> getString( 'acp_forums_subsection_topics_prefixes_notfound')));
						
						$this -> act_topics_prefixes();
						
					}
					
				}else{
					
					$this -> act_topics_prefixes();
				
				}
				
			break;
			
			case 'topics_prefixes_delete':
								
				/**
				 * get prefix to edit
				 */
				
				$prefix_to_edit = $_GET['prefix'];
				settype( $prefix_to_edit, 'integer');
				
				/**
				 * select event
				 */
				
				$event_query = $mysql -> query( "SELECT * FROM topics_prefixes WHERE `topic_prefix_id` = '$prefix_to_edit'");
				
				if ( $prefix_result = mysql_fetch_array( $event_query, MYSQL_ASSOC)){
										
					$mysql -> delete( 'topics_prefixes', "topic_prefix_id = '$prefix_to_edit'");
					
					$cache -> flushCache( 'prefixes');
					
					/**
					 * add log
					 */
					
					$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_topics_prefixes_forums_delete_log'), array( 'prefix_id' => $prefix_to_edit));
					
					/**
					 * draw message
					 */
					
					parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_delete'), $language -> getString( 'acp_forums_subsection_topics_prefixes_forums_delete_done')));
					
					$this -> act_topics_prefixes();
									
				}else{
					
					//not found. Draw message and jump to manager
					
					parent::draw( $style ->drawErrorBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_delete'), $language -> getString( 'acp_forums_subsection_topics_prefixes_notfound')));
					
					$this -> act_topics_prefixes();
					
				}
								
			break;
			
			case 'bbtags_manage':
		
				$this -> act_bbtags_manage();
			
			break;
			
			case 'bbtags_new':
				
				$this -> act_bbtags_new();
				
			break;
			
			case 'bbtags_add':
				
				/**
				 * add new bbcode
				 */
				
				if ( $session -> checkForm()){
					
					$code_name = ( $strings -> inputClear( $_POST['code_name'], false));
					$code_info = ( $strings -> inputClear( $_POST['code_info'], false));
					$code_content = ( $strings -> inputClear( $_POST['code_content'], false));
					$code_option = $_POST['code_option'];
					$code_replace = ( $strings -> inputClear( $_POST['code_replace'], false));
					$code_draw = $_POST['code_draw'];
					
					settype( $code_option, 'bool');
					settype( $code_draw, 'bool');
					
					/**
					 * do error checking
					 */
					
					if ( strlen( $code_name) == 0){
						
						/**
						 * name empty
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_bbtags_new'), $language -> getString('acp_forums_subsection_bbtags_new_name_empty')));
						
						$this -> act_bbtags_new();
						
					}else if ( strlen( $code_content) == 0){
						
						/**
						 * content empty
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_bbtags_new'), $language -> getString('acp_forums_subsection_bbtags_new_name_content')));
						
						$this -> act_bbtags_new();
						
					}else if ( strlen( $code_replace) == 0){
						
						/**
						 * html empty
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_bbtags_new'), $language -> getString('acp_forums_subsection_bbtags_new_name_html')));
						
						$this -> act_bbtags_new();
						
					}else{
						
						/**
						 * add bbcode
						 */
						
						$new_bbcode_sql['tag_name'] = $code_name;
						$new_bbcode_sql['tag_info'] = $code_info;
						$new_bbcode_sql['tag_tag'] = $code_content;
						$new_bbcode_sql['tag_option'] = $code_option;
						$new_bbcode_sql['tag_replace'] = $code_replace;
						$new_bbcode_sql['tag_draw'] = $code_draw;
						
						$mysql -> insert( $new_bbcode_sql, 'bbtags');
						
						$cache -> flushCache( 'bbcodes');
						
						/**
						 * add log
						 */
						
						$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_bbtags_new_added_log'), array( 'code_name' => $code_name));
						
						/**
						 * draw message
						 */
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_bbtags_new'), $language -> getString('acp_forums_subsection_bbtags_new_added')));
						
						$this -> act_bbtags_manage();
						
					}
				
				}else{
					
					$this -> act_bbtags_manage();
					
				}
				
			break;
			
			case 'bbtags_edit':
				
				$this -> act_bbtags_edit();
				
			break;
			
			case 'bbtags_change':
				
				/**
				 * update bbcode
				 */
				
				if ( $session -> checkForm()){
	
					/**
					 * get tag to edit
					 */
					
					$tag_to_edit = $_GET['tag'];
					
					settype( $tag_to_edit, 'integer');
					
					/**
					 * select tag from db
					 */
					
					$tag_query = $mysql -> query( "SELECT * FROM bbtags WHERE `tag_id` = '$tag_to_edit'");
					
					if ( $tag_result = mysql_fetch_array( $tag_query, MYSQL_ASSOC)){
						
						$code_name = ( $strings -> inputClear( $_POST['code_name'], false));
						$code_info = ( $strings -> inputClear( $_POST['code_info'], false));
						$code_content = ( $strings -> inputClear( $_POST['code_content'], false));
						$code_option = $_POST['code_option'];
						$code_replace = ( $strings -> inputClear( $_POST['code_replace'], false));
						$code_draw = $_POST['code_draw'];
						
						settype( $code_option, 'bool');
						settype( $code_draw, 'bool');
						
						/**
						 * do error checking
						 */
						
						if ( strlen( $code_name) == 0){
							
							/**
							 * name empty
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_bbtags_edit'), $language -> getString('acp_forums_subsection_bbtags_new_name_empty')));
							
							$this -> act_bbtags_new();
							
						}else if ( strlen( $code_content) == 0){
							
							/**
							 * content empty
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_bbtags_edit'), $language -> getString('acp_forums_subsection_bbtags_new_name_content')));
							
							$this -> act_bbtags_new();
							
						}else if ( strlen( $code_replace) == 0){
							
							/**
							 * html empty
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_bbtags_edit'), $language -> getString('acp_forums_subsection_bbtags_new_name_html')));
							
							$this -> act_bbtags_new();
							
						}else{
							
							/**
							 * add bbcode
							 */
							
							$new_bbcode_sql['tag_name'] = $code_name;
							$new_bbcode_sql['tag_info'] = $code_info;
							$new_bbcode_sql['tag_tag'] = $code_content;
							$new_bbcode_sql['tag_option'] = $code_option;
							$new_bbcode_sql['tag_replace'] = $code_replace;
							$new_bbcode_sql['tag_draw'] = $code_draw;
							
							$mysql -> update( $new_bbcode_sql, 'bbtags', "`tag_id` = '$tag_to_edit'");
							
							$cache -> flushCache( 'bbcodes');
							
							/**
							 * add log
							 */
							
							$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_bbtags_edit_log'), array( 'code_name' => $code_name));
							
							/**
							 * draw message
							 */
							
							parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_bbtags_edit'), $language -> getString('acp_forums_subsection_bbtags_edit_done')));
							
							$this -> act_bbtags_manage();
							
						}
								
					}else{
						
						/**
						 * tag not found
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_bbtags_edit'), $language -> getString( 'acp_forums_subsection_bbtags_edit_notfound')));
						
						$this -> act_bbtags_manage();
						
					}
					
				}else{
					
					$this -> act_bbtags_manage();
				}
				
			break;
			
			case 'bbtags_delete':
				
				/**
				 * get tag to delete
				 */
				
				$tag_to_delete = $_GET['tag'];
				
				settype( $tag_to_delete, 'integer');
				
				$mysql -> delete( "bbtags", "`tag_id` = '$tag_to_delete'");
				
				$cache -> flushCache( 'bbcodes');
				
				/**
				 * add log
				 */
				
				$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_bbtags_delete_log'), array( 'code_id' => $tag_to_delete));
				
				/**
				 * draw message
				 */
				
				parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_bbtags_delete'), $language -> getString('acp_forums_subsection_bbtags_delete_ok')));
						
				$this -> act_bbtags_manage();
						
			break;
			
			case 'atts_types':
				
				$this -> act_atts_types();
				
			break;
			
			case 'orpchans':
				
				$this -> act_orpchans();
				
			break;
			
			case 'add_att_type':
			
				/**
				 * check form
				 */
				
				if ( $session -> checkForm()){
					
					/**
					 * get values
					 */
							
					$attach_type_extension = $strings -> inputClear( $_POST['att_ext'], false);
					$attach_type_mime = $strings -> inputClear( $_POST['att_mime'], false);
					$attach_type_image = $strings -> inputClear( $_POST['att_image'], false);
					
					/**
					 * check mime
					 */
					
					$ext_taken = false;
					
					$ext_query = $mysql -> query( "SELECT * FROM `attachments_types` WHERE `attachments_type_extension` = '$attach_type_extension'");
					
					if ( $ext_result = mysql_fetch_array( $ext_query, MYSQL_ASSOC))
						$ext_taken = true;
					
					/**
					 * check errors
					 */
					
					if ( strlen( $attach_type_extension)  < 2 || strlen( $attach_type_extension)  > 3){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_atts_types_new_type'), $language -> getString( 'acp_forums_subsection_atts_types_new_type_empty_extension')));
						
						$this -> act_new_att_type();
						
					}else if( $ext_taken){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_atts_types_new_type'), $language -> getString( 'acp_forums_subsection_atts_types_new_type_taken_extension')));
						
						$this -> act_new_att_type();
						
					}else{
						
						/**
						 * do query
						 */
						
						$new_att_type_sql['attachments_type_extension'] = $attach_type_extension;
						$new_att_type_sql['attachments_type_mime'] = $attach_type_mime;
						$new_att_type_sql['attachments_type_image'] = $attach_type_image;
						
						$mysql -> insert( $new_att_type_sql, 'attachments_types');
						
						/**
						 * add log
						 */
						
						$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_atts_types_new_type_log', array( 'new_att_extension' => $attach_type_extension)));
						
						/**
						 * draw message
						 */
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_atts_types_new_type'), $language -> getString( 'acp_forums_subsection_atts_types_new_type_done')));
						
						/**
						 * go to manager
						 */
						
						$this -> act_atts_types();
						
					}
					
				}else{
					
					$this -> act_atts_types();
					
				}
				
			break;
				
			case 'new_att_type':
				
				$this -> act_new_att_type();
				
			break;
				
			case 'change_att_type':
				
				/**
				 * get type to edit
				 */
				
				$type_to_edit = $_GET['type'];
				settype( $type_to_edit, 'integer');
				
				/**
				 * do query
				 */
				
				$type_query = $mysql -> query( "SELECT * FROM `attachments_types` WHERE `attachments_type_id` = '$type_to_edit'");
							
				if ( $type_result = mysql_fetch_array( $type_query, MYSQL_ASSOC)){
				
					/**
					 * get values
					 */
							
					$attach_type_extension = $strings -> inputClear( $_POST['att_ext'], false);
					$attach_type_mime = $strings -> inputClear( $_POST['att_mime'], false);
					$attach_type_image = $strings -> inputClear( $_POST['att_image'], false);
					
					/**
					 * check mime
					 */
					
					$ext_taken = false;
					
					$ext_query = $mysql -> query( "SELECT * FROM `attachments_types` WHERE `attachments_type_extension` = '$attach_type_extension' AND `attachments_type_id` <> '$type_to_edit'");
					
					if ( $ext_result = mysql_fetch_array( $ext_query, MYSQL_ASSOC))
						$ext_taken = true;
					
					/**
					 * check errors
					 */
					
					if ( strlen( $attach_type_extension)  < 2 || strlen( $attach_type_extension)  > 3){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_atts_types_edit_type'), $language -> getString( 'acp_forums_subsection_atts_types_new_type_empty_extension')));
						
						$this -> act_edit_att_type();
						
					}else if( $ext_taken){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_atts_types_edit_type'), $language -> getString( 'acp_forums_subsection_atts_types_new_type_taken_extension')));
						
						$this -> act_edit_att_type();
						
					}else{
						
						/**
						 * do query
						 */
						
						$new_att_type_sql['attachments_type_extension'] = $attach_type_extension;
						$new_att_type_sql['attachments_type_mime'] = $attach_type_mime;
						$new_att_type_sql['attachments_type_image'] = $attach_type_image;
						
						$mysql -> update( $new_att_type_sql, 'attachments_types', "`attachments_type_id` = '$type_to_edit'");
						
						/**
						 * add log
						 */
						
						$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_atts_types_edit_type_log', array( 'ed_att_type' => $type_to_edit)));
						
						/**
						 * draw message
						 */
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_atts_types_edit_type'), $language -> getString( 'acp_forums_subsection_atts_types_edit_type_done')));
						
						/**
						 * go to manager
						 */
						
						$this -> act_atts_types();
						
					}
					
				}else{
					
					parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_atts_types_edit_type'), $language -> getString( 'acp_forums_subsection_atts_types_edit_type_notfound')));
								
					$this -> act_atts_types();		
					
				}
				
			break;
			
			case 'edit_att_type':
				
				$this -> act_edit_att_type();
				
			break;
				
			case 'del_att_type':
				
				/**
				 * get type to edit
				 */
				
				$type_to_edit = $_GET['type'];
				settype( $type_to_edit, 'integer');
				
				/**
				 * do query
				 */
				
				$type_query = $mysql -> query( "SELECT * FROM attachments_types WHERE `attachments_type_id` = '$type_to_edit'");
							
				if ( $type_result = mysql_fetch_array( $type_query, MYSQL_ASSOC)){
				
					/**
					 * check if it is in use
					 */
					
					$attachs_query = $mysql -> query( "SELECT * FROM attachments WHERE `attachment_type` = '$type_to_edit' LIMIT 1");
					
					if ( $attach_result = mysql_fetch_array( $attachs_query, MYSQL_ASSOC)){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_atts_types_del_type'), $language -> getString( 'acp_forums_subsection_atts_types_del_type_in_user')));
					
					}else{
						
						/**
						 * delete type
						 */
						
						$mysql -> delete( 'attachments_types', "`attachments_type_id` = '$type_to_edit'");
						
						/**
						 * add log
						 */
						
						$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_atts_types_del_type_log'), array( 'del_att_type' => $type_to_edit));
						
						/**
						 * draw message
						 */
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_atts_types_del_type'), $language -> getString( 'acp_forums_subsection_atts_types_del_type_done')));
					
					}
					
				}else{
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_atts_types_del_type'), $language -> getString( 'acp_forums_subsection_atts_types_del_type_notfound')));
					
				}
				
				$this -> act_atts_types();		
					
			break;
						
		}
		
	}	
		
	function act_new_forum(){
	
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * proper sub-actions
		 */
		
		$proper_does = array( 'new_forum');
		
		$path_link = array( 'act' => 'boards');
				
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_boards'), parent::adminLink( parent::getId(), $path_link));		
		
		$path_link = array( 'act' => 'new_board');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_new_board_title'), parent::adminLink( parent::getId(), $path_link));
		
		$output -> setTitle( $language -> getString( 'acp_forums_subsection_new_board_title'));
		
		/**
		 * predefinied settings
		 */
		
		$forum_parent = 0;
		
		$forum_redirect_count = true;
		
		$forum_allow_bbcode = true;
		$forum_allow_quickreply = true;
		$forum_allow_surveys = true;
		$forum_increase_counter = true;
		
		$forum_pruning = false;
		$forum_prune_days = 0;
			
		/**
		 * retake actions, if we are repeating act
		 */
			
		if ( $_GET['act'] == 'add_board') {
			
			$forum_name = stripslashes( $strings -> inputClear( $_POST['forum_name'], false));
			$forum_image = stripslashes( $strings -> inputClear( $_POST['forum_image'], false));
			$forum_info = stripslashes( $strings -> inputClear( $_POST['forum_info'], false));
			$forum_parent = trim($_POST['forum_parent']);
			$forum_category = trim($_POST['forum_category']);
			$forum_closed = trim($_POST['forum_closed']);
			$forum_gidelines_text = stripslashes( $strings -> inputClear( $_POST['forum_gidelines_text'], false));
			$forum_gidelines_url = stripslashes( $strings -> inputClear( $_POST['forum_gidelines_url'], false));
			$forum_url = stripslashes( $strings -> inputClear( $_POST['forum_url']));
			$forum_redirect_count = $_POST['forum_redirect_count'];
			$forum_allow_bbcode = trim($_POST['forum_allow_bbcode']);
			$forum_allow_quickreply = trim($_POST['forum_allow_quickreply']);
			$forum_allow_surveys = trim($_POST['forum_allow_surveys']);
			$forum_increase_counter = trim($_POST['forum_increase_counter']);
			$forum_force_order = trim($_POST['forum_force_order']);
			$forum_force_direction = $_POST['forum_force_direction'];
			$forum_pruning = stripslashes( $strings -> inputClear( $_POST['forum_pruning'], false));
			$forum_prune_days = stripslashes( $strings -> inputClear( $_POST['forum_prune_days'], false));
			
			settype( $forum_category, 'bool');
			settype( $forum_closed, 'bool');
			settype( $forum_redirect_count, 'bool');
			settype( $forum_allow_bbcode, 'bool');
			settype( $forum_allow_quickreply, 'bool');
			settype( $forum_allow_surveys, 'bool');
			settype( $forum_increase_counter, 'bool');
			settype( $forum_pruning, 'bool');
			
		}
		
		/**
		 * now draw form
		 */
				
		$new_forum_link['act'] = 'add_board';
		
		$new_board_form = new form();
		$new_board_form -> openForm( parent::adminLink( parent::getId(), $new_forum_link), 'POST', false, 'forum_form');
		$new_board_form -> drawSpacer( $language -> getString( 'acp_forums_subsection_new_board_subtitle_basic_settings'));
		$new_board_form -> openOpTable();
		
		$new_board_form -> drawTextInput( $language -> getString('acp_forums_subsection_new_board_basic_settings_forum_title'), 'forum_name', $forum_name);
		$new_board_form -> drawTextInput( $language -> getString('acp_forums_subsection_new_board_basic_settings_forum_image'), 'forum_image', $forum_image, $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_image_help'));
		$new_board_form -> drawEditor( $language -> getString('acp_forums_subsection_new_board_basic_settings_forum_info'), 'forum_info', $forum_info, '', true, false);
		
		$forums_list = $forums -> getForumsList();
		$forums_list[0] = $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_localisation_0');
		
		$new_board_form -> drawList( $language -> getString('acp_forums_subsection_new_board_basic_settings_forum_localisation'), 'forum_parent', $forums_list, $forum_parent, $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_localisation_help'));
		$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_category'), 'forum_category', $forum_category, $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_category_help'));
		$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_closed'), 'forum_closed', $forum_closed, $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_closed_help'));
		
		$new_board_form -> closeTable();
		$new_board_form -> drawSpacer( $language -> getString( 'acp_forums_subsection_new_board_subtitle_guidelines_settings'));
		$new_board_form -> openOpTable();
		
		$new_board_form -> drawEditor( $language -> getString('acp_forums_subsection_new_board_subtitle_guidelines_settings_text'), 'forum_gidelines_text', $forum_gidelines_text, '', true, false);
		$new_board_form -> drawTextInput( $language -> getString('acp_forums_subsection_new_board_subtitle_guidelines_settings_link'), 'forum_gidelines_url', $forum_gidelines_url);
		
		$new_board_form -> closeTable();
		$new_board_form -> drawSpacer( $language -> getString( 'acp_forums_subsection_new_board_subtitle_redirect_settings'));
		$new_board_form -> openOpTable();
		
		$new_board_form -> drawTextInput( $language -> getString( 'acp_forums_subsection_new_board_redirect_settings_forum_redirect_url'), 'forum_url', $forum_url, $language -> getString( 'acp_forums_subsection_new_board_redirect_settings_forum_redirect_url_help'));
		$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_redirect_settings_forum_redirect_count'), 'forum_redirect_count', $forum_redirect_count);
		
		$new_board_form -> closeTable();
		$new_board_form -> drawSpacer( $language -> getString( 'acp_forums_subsection_new_board_subtitle_writing_settings'));
		$new_board_form -> openOpTable();
		
		$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_allow_bbcodes'), 'forum_allow_bbcode', $forum_allow_bbcode);
		$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_quick_reply'), 'forum_allow_quickreply', $forum_allow_quickreply);
		$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_allow_surveys'), 'forum_allow_surveys', $forum_allow_surveys);
		$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_post_conter_increase'), 'forum_increase_counter', $forum_increase_counter);

		$forums_order_list[0] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_0');
		$forums_order_list[1] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_1');
		$forums_order_list[2] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_2');
		$forums_order_list[3] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_3');
		$forums_order_list[4] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_4');
		$forums_order_list[5] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_5');
		$forums_order_list[6] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_6');
		
		$new_board_form -> drawList( $language -> getString('acp_forums_subsection_new_board_writing_settings_forum_default_order'), 'forum_force_order', $forums_order_list, $forum_force_order);
		
		$forums_directions_list[0] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_direction_0');
		$forums_directions_list[1] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_direction_1');
		
		$new_board_form -> drawList( $language -> getString('acp_forums_subsection_new_board_writing_settings_forum_default_direction'), 'forum_force_direction', $forums_directions_list, $forum_force_direction);
		
		$new_board_form -> closeTable();
		$new_board_form -> drawSpacer( $language -> getString( 'acp_forums_subsection_new_board_subtitle_pruning'));
		$new_board_form -> openOpTable( true);
		
		$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_pruning'), 'forum_pruning', $forum_pruning);
		$new_board_form -> drawTextInput( $language -> getString( 'acp_forums_subsection_new_board_pruning_days'), 'forum_prune_days', $forum_prune_days, $language -> getString( 'acp_forums_subsection_new_board_pruning_days_help'));
					
		$new_board_form -> closeTable();
		$new_board_form -> drawSpacer( $language -> getString( 'acp_forums_subsection_new_board_subtitle_permissions'));
		$new_board_form -> openOpTable( true);
		$new_board_form -> addToContent( '<tr>
			<th>&nbsp;</th>
			<th>'.$language -> getString( 'forums_access_show_forum').'
			<br />(<a href="javascript:selectColumn(0)">+</a> | <a href="javascript:emptyColumn(0)">-</a>)</th>
			<th>'.$language -> getString( 'forums_access_show_topics').'
			<br />(<a href="javascript:selectColumn(1)">+</a> | <a href="javascript:emptyColumn(1)">-</a>)</th>
			<th>'.$language -> getString( 'forums_access_reply_topics').'
			<br />(<a href="javascript:selectColumn(2)">+</a> | <a href="javascript:emptyColumn(2)">-</a>)</th>
			<th>'.$language -> getString( 'forums_access_start_topics').'
			<br />(<a href="javascript:selectColumn(3)">+</a> | <a href="javascript:emptyColumn(3)">-</a>)</th>
			<th>'.$language -> getString( 'forums_access_attachments_upload').'
			<br />(<a href="javascript:selectColumn(4)">+</a> | <a href="javascript:emptyColumn(4)">-</a>)</th>
			<th>'.$language -> getString( 'forums_access_attachments_download').'
			<br />(<a href="javascript:selectColumn(5)">+</a> | <a href="javascript:emptyColumn(5)">-</a>)</th>
		</tr>');
		
		$masks_query = $mysql -> query("SELECT p.* FROM users_perms p");
		
		$board_num = 0;
		$boards_list = '';
		
		while ( $masks_result = mysql_fetch_array( $masks_query, MYSQL_ASSOC)){
			
			//clear slachses
			$masks_result = $mysql -> clear( $masks_result);
			
			$new_board_form -> addToContent( '<tr>
				<td class="opt_row1">'.$masks_result['users_perm_name'].'<br />(<a href="javascript:selectRow('.$masks_result['users_perm_id'].')">+</a> | <a href="javascript:emptyRow('.$masks_result['users_perm_id'].')">-</a>)</td>
				<td class="opt_row2" style="text-align: center">'.$new_board_form -> drawSelect( 'forum_access_show_forum['.$masks_result['users_perm_id'].']').'</td>
				<td class="opt_row1" style="text-align: center">'.$new_board_form -> drawSelect( 'forum_access_show_topics['.$masks_result['users_perm_id'].']').'</td>
				<td class="opt_row2" style="text-align: center">'.$new_board_form -> drawSelect( 'forum_access_reply_topics['.$masks_result['users_perm_id'].']').'</td>
				<td class="opt_row1" style="text-align: center">'.$new_board_form -> drawSelect( 'forum_access_start_topics['.$masks_result['users_perm_id'].']').'</td>
				<td class="opt_row2" style="text-align: center">'.$new_board_form -> drawSelect( 'forum_access_attachments_upload['.$masks_result['users_perm_id'].']').'</td>
				<td class="opt_row1" style="text-align: center">'.$new_board_form -> drawSelect( 'forum_access_attachments_download['.$masks_result['users_perm_id'].']').'</td>
			</tr>');
			
			$boards_list .= "forums_list[".$board_num."] = ".$masks_result['users_perm_id']."\n";
			
			$board_num++;
			
		}
		
		$new_board_form -> addToContent( '<script type="text/javascript">
													
					function selectColumn( column){
										
						var columns = new Array()
						
						columns[0] = "forum_access_show_forum"	
						columns[1] = "forum_access_show_topics"	
						columns[2] = "forum_access_reply_topics"	
						columns[3] = "forum_access_start_topics"	
						columns[4] = "forum_access_attachments_upload"	
						columns[5] = "forum_access_attachments_download"	
								
						var forums_list = new Array()				
						'.$boards_list.'
						
						forums_to_move = forums_list.length
						
						for( var forum_num in forums_list){
						
							if( forums_to_move > 0){
														
								cp = document.forms["forum_form"].elements[columns[column] + "[" + forums_list[forum_num] + "]"]
								
								cp.checked = "checked"
						
								forums_to_move ++
								
							}
							
						}
							
					}
					
					function emptyColumn( column){
					
					var columns = new Array()
						
						columns[0] = "forum_access_show_forum"	
						columns[1] = "forum_access_show_topics"	
						columns[2] = "forum_access_reply_topics"	
						columns[3] = "forum_access_start_topics"	
						columns[4] = "forum_access_attachments_upload"	
						columns[5] = "forum_access_attachments_download"	
								
						var forums_list = new Array()				
						'.$boards_list.'
						
						forums_to_move = forums_list.length
						
						for( var forum_num in forums_list){
						
							if( forums_to_move > 0){
														
								cp = document.forms["forum_form"].elements[columns[column] + "[" + forums_list[forum_num] + "]"]
								
								cp.checked = ""
						
								forums_to_move ++
								
							}
							
						}
						
					}
					
					function selectRow( row){
					
						rp1 = document.forms["forum_form"].elements["forum_access_show_forum["+row+"]"];
						rp2 = document.forms["forum_form"].elements["forum_access_show_topics["+row+"]"];
						rp3 = document.forms["forum_form"].elements["forum_access_reply_topics["+row+"]"];
						rp4 = document.forms["forum_form"].elements["forum_access_start_topics["+row+"]"];
						rp5 = document.forms["forum_form"].elements["forum_access_attachments_upload["+row+"]"];
						rp6 = document.forms["forum_form"].elements["forum_access_attachments_download["+row+"]"];
						
						rp1.checked = "checked";
						rp2.checked = "checked";
						rp3.checked = "checked";
						rp4.checked = "checked";
						rp5.checked = "checked";
						rp6.checked = "checked";
					
					}
				
					function emptyRow( row){
					
						rp1 = document.forms["forum_form"].elements["forum_access_show_forum["+row+"]"];
						rp2 = document.forms["forum_form"].elements["forum_access_show_topics["+row+"]"];
						rp3 = document.forms["forum_form"].elements["forum_access_reply_topics["+row+"]"];
						rp4 = document.forms["forum_form"].elements["forum_access_start_topics["+row+"]"];
						rp5 = document.forms["forum_form"].elements["forum_access_attachments_upload["+row+"]"];
						rp6 = document.forms["forum_form"].elements["forum_access_attachments_download["+row+"]"];
						
						
						rp1.checked = "";
						rp2.checked = "";
						rp3.checked = "";
						rp4.checked = "";
						rp5.checked = "";
						rp6.checked = "";
					
					}
					
				</script>');
		
		$new_board_form -> closeTable();		
		$new_board_form -> drawButton( $language -> getString( 'acp_forums_subsection_new_board_title_button'));
		$new_board_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_new_board_title'), $new_board_form -> display()));
		
	}
	
	function act_edit_board(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * get forum to admin
		 */
		
		$forum_to_admin = $_GET['board'];
		
		settype( $forum_to_admin, 'integer');
		
		if ( $forum_to_admin > 0){
			
			/**
			 * proper forum id
			 * select forum from MySQL
			 */
			
			$forum_query = $mysql -> query( "SELECT * FROM forums WHERE `forum_id` = '$forum_to_admin'");
			
			if ( $forum_result = mysql_fetch_array( $forum_query, MYSQL_ASSOC)){
				
				/**
				 * clear result
				 */
				
				$forum_result = $mysql -> clear( $forum_result);
				
				/**
				 * add breadcrumbs
				 */
				
				$path_link = array( 'act' => 'boards');
				
				$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_boards'), parent::adminLink( parent::getId(), $path_link));		
				$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_boards'), parent::adminLink( parent::getId(), $path_link));
				
				$path_link = array( 'act' => 'edit_board', 'board' => $forum_to_admin);
				
				$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_boards_edit'), parent::adminLink( parent::getId(), $path_link));
				
				/**
				 * set page title
				 */
				
				$output -> setTitle( $language -> getString( 'acp_forums_subsection_boards_edit'));

				/**
				 * set basic values
				 */
				
				$forum_name = $forum_result['forum_name'];
				$forum_image = $forum_result['forum_image'];
				$forum_info = $forum_result['forum_info'];
				$forum_parent = $forum_result['forum_parent'];
				
				if ( $forum_result['forum_type'] == 0)
					$forum_category = true;
				
				$forum_closed = $forum_result['forum_locked'];
				$forum_gidelines_text = $forum_result['forum_guidelines'];
				$forum_gidelines_url = $forum_result['forum_guidelines_url'];
				$forum_url = $forum_result['forum_url'];
				$forum_redirect_count = $forum_result['forum_count_redirects'];
				$forum_allow_bbcode = $forum_result['forum_allow_bbcode'];
				$forum_allow_quickreply = $forum_result['forum_allow_quick_reply'];
				$forum_allow_surveys = $forum_result['forum_allow_surveys'];
				$forum_increase_counter = $forum_result['forum_increase_counter'];
				$forum_force_order = $forum_result['forum_force_ordering'];
				$forum_force_direction = $forum_result['forum_ordering_way'];
				$forum_pruning = $forum_result['forum_pruning'];
				$forum_prune_days = $forum_result['forum_prune_days'];
					
				/**
				 * retake actions, if we are repeating act
				 */
					
				if ( $_GET['act'] == 'change_board') {
					
					$forum_name = stripslashes( $strings -> inputClear( $_POST['forum_name'], false));
					$forum_image = stripslashes( $strings -> inputClear( $_POST['forum_image'], false));
					$forum_info = stripslashes( $strings -> inputClear( $_POST['forum_info'], false));
					$forum_parent = trim($_POST['forum_parent']);
					$forum_category = trim($_POST['forum_category']);
					$forum_closed = trim($_POST['forum_closed']);
					$forum_gidelines_text = stripslashes( $strings -> inputClear( $_POST['forum_gidelines_text'], false));
					$forum_gidelines_url = stripslashes( $strings -> inputClear( $_POST['forum_gidelines_url'], false));
					$forum_url = stripslashes( $strings -> inputClear( $_POST['forum_url']));
					$forum_redirect_count = trim($_POST['forum_redirect_count']);
					$forum_allow_bbcode = trim($_POST['forum_allow_bbcode']);
					$forum_allow_quickreply = trim($_POST['forum_allow_quickreply']);
					$forum_allow_surveys = trim($_POST['forum_allow_surveys']);
					$forum_increase_counter = trim($_POST['forum_increase_counter']);
					$forum_force_order = trim($_POST['forum_force_order']);
					$forum_force_direction = $_POST['forum_force_direction'];
					$forum_pruning = stripslashes( $strings -> inputClear( $_POST['forum_pruning'], false));
					$forum_prune_days = stripslashes( $strings -> inputClear( $_POST['forum_prune_days'], false));
					
					settype( $forum_category, 'bool');
					settype( $forum_closed, 'bool');
					settype( $forum_redirect_count, 'bool');
					settype( $forum_allow_bbcode, 'bool');
					settype( $forum_allow_quickreply, 'bool');
					settype( $forum_allow_surveys, 'bool');
					settype( $forum_increase_counter, 'bool');
					settype( $forum_pruning, 'bool');
					
				}
				
				/**
				 * now draw form
				 */
						
				$new_forum_link['act'] = 'change_board';
				$new_forum_link['board'] = $forum_to_admin;
				
				$new_board_form = new form();
				$new_board_form -> openForm( parent::adminLink( parent::getId(), $new_forum_link), 'POST', false, 'forum_form');
				$new_board_form -> drawSpacer( $language -> getString( 'acp_forums_subsection_new_board_subtitle_basic_settings'));
				$new_board_form -> openOpTable();
				
				$new_board_form -> drawTextInput( $language -> getString('acp_forums_subsection_new_board_basic_settings_forum_title'), 'forum_name', $forum_name);
				$new_board_form -> drawTextInput( $language -> getString('acp_forums_subsection_new_board_basic_settings_forum_image'), 'forum_image', $forum_image, $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_image_help'));
				$new_board_form -> drawEditor( $language -> getString('acp_forums_subsection_new_board_basic_settings_forum_info'), 'forum_info', $forum_info, '', true, false);
				
				$forums_list = $forums -> getForumsList( $forum_to_admin);
				$forums_list[0] = $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_localisation_0');
				
				$new_board_form -> drawList( $language -> getString('acp_forums_subsection_new_board_basic_settings_forum_localisation'), 'forum_parent', $forums_list, $forum_parent, $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_localisation_help'));
				$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_category'), 'forum_category', $forum_category, $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_category_help'));
				$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_closed'), 'forum_closed', $forum_closed, $language -> getString( 'acp_forums_subsection_new_board_basic_settings_forum_closed_help'));
						
				$new_board_form -> closeTable();
				$new_board_form -> drawSpacer( $language -> getString( 'acp_forums_subsection_new_board_subtitle_guidelines_settings'));
				$new_board_form -> openOpTable();
				
				$new_board_form -> drawEditor( $language -> getString('acp_forums_subsection_new_board_subtitle_guidelines_settings_text'), 'forum_gidelines_text', $forum_gidelines_text, '', true, false);
				$new_board_form -> drawTextInput( $language -> getString('acp_forums_subsection_new_board_subtitle_guidelines_settings_link'), 'forum_gidelines_url', $forum_gidelines_url);
				
				
				$new_board_form -> closeTable();
				$new_board_form -> drawSpacer( $language -> getString( 'acp_forums_subsection_new_board_subtitle_redirect_settings'));
				$new_board_form -> openOpTable();
				
				$new_board_form -> drawTextInput( $language -> getString( 'acp_forums_subsection_new_board_redirect_settings_forum_redirect_url'), 'forum_url', $forum_url, $language -> getString( 'acp_forums_subsection_new_board_redirect_settings_forum_redirect_url_help'));
				$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_redirect_settings_forum_redirect_count'), 'forum_redirect_count', $forum_redirect_count);
				
				$new_board_form -> closeTable();
				$new_board_form -> drawSpacer( $language -> getString( 'acp_forums_subsection_new_board_subtitle_writing_settings'));
				$new_board_form -> openOpTable();
				
				$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_allow_bbcodes'), 'forum_allow_bbcode', $forum_allow_bbcode);
				$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_quick_reply'), 'forum_allow_quickreply', $forum_allow_quickreply);
				$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_allow_surveys'), 'forum_allow_surveys', $forum_allow_surveys);
				$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_post_conter_increase'), 'forum_increase_counter', $forum_increase_counter);
		
				$forums_order_list[0] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_0');
				$forums_order_list[1] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_1');
				$forums_order_list[2] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_2');
				$forums_order_list[3] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_3');
				$forums_order_list[4] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_4');
				$forums_order_list[5] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_5');
				$forums_order_list[6] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_order_6');
				
				$new_board_form -> drawList( $language -> getString('acp_forums_subsection_new_board_writing_settings_forum_default_order'), 'forum_force_order', $forums_order_list, $forum_force_order);
				
				$forums_directions_list[0] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_direction_0');
				$forums_directions_list[1] = $language -> getString( 'acp_forums_subsection_new_board_writing_settings_forum_default_direction_1');
				
				$new_board_form -> drawList( $language -> getString('acp_forums_subsection_new_board_writing_settings_forum_default_direction'), 'forum_force_direction', $forums_directions_list, $forum_force_direction);
				
				$new_board_form -> closeTable();
				$new_board_form -> drawSpacer( $language -> getString( 'acp_forums_subsection_new_board_subtitle_pruning'));
				$new_board_form -> openOpTable( true);
				
				$new_board_form -> drawYesNo( $language -> getString( 'acp_forums_subsection_new_board_pruning'), 'forum_pruning', $forum_pruning);
				$new_board_form -> drawTextInput( $language -> getString( 'acp_forums_subsection_new_board_pruning_days'), 'forum_prune_days', $forum_prune_days, $language -> getString( 'acp_forums_subsection_new_board_pruning_days_help'));
											
				$new_board_form -> closeTable();
				$new_board_form -> drawSpacer( $language -> getString( 'acp_forums_subsection_new_board_subtitle_permissions'));
				$new_board_form -> openOpTable( true);
				$new_board_form -> addToContent( '<tr>
					<th>&nbsp;</th>
					<th>'.$language -> getString( 'forums_access_show_forum').'
					<br />(<a href="javascript:selectColumn(0)">+</a> | <a href="javascript:emptyColumn(0)">-</a>)</th>
					<th>'.$language -> getString( 'forums_access_show_topics').'
					<br />(<a href="javascript:selectColumn(1)">+</a> | <a href="javascript:emptyColumn(1)">-</a>)</th>
					<th>'.$language -> getString( 'forums_access_reply_topics').'
					<br />(<a href="javascript:selectColumn(2)">+</a> | <a href="javascript:emptyColumn(2)">-</a>)</th>
					<th>'.$language -> getString( 'forums_access_start_topics').'
					<br />(<a href="javascript:selectColumn(3)">+</a> | <a href="javascript:emptyColumn(3)">-</a>)</th>
					<th>'.$language -> getString( 'forums_access_attachments_upload').'
					<br />(<a href="javascript:selectColumn(4)">+</a> | <a href="javascript:emptyColumn(4)">-</a>)</th>
					<th>'.$language -> getString( 'forums_access_attachments_download').'
					<br />(<a href="javascript:selectColumn(5)">+</a> | <a href="javascript:emptyColumn(5)">-</a>)</th>
				</tr>');
				
				$access_query = $mysql -> query( "SELECT * FROM forums_access WHERE `forums_acess_forum_id` = '$forum_to_admin'");
								
				while ( $access_result = mysql_fetch_array( $access_query, MYSQL_ASSOC)){
					
					$forum_access[ $access_result['forums_acess_perms_id']]['forums_access_show_forum'] = $access_result['forums_access_show_forum'];
					$forum_access[ $access_result['forums_acess_perms_id']]['forums_access_show_topics'] = $access_result['forums_access_show_topics'];
					$forum_access[ $access_result['forums_acess_perms_id']]['forums_access_reply_topics'] = $access_result['forums_access_reply_topics'];
					$forum_access[ $access_result['forums_acess_perms_id']]['forums_access_start_topics'] = $access_result['forums_access_start_topics'];
					$forum_access[ $access_result['forums_acess_perms_id']]['forums_access_attachments_upload'] = $access_result['forums_access_attachments_upload'];
					$forum_access[ $access_result['forums_acess_perms_id']]['forums_access_attachments_download'] = $access_result['forums_access_attachments_download'];
					
				}
				
				$masks_query = $mysql -> query("SELECT * FROM users_perms");
				
				$board_num = 0;
				$boards_list = '';
				
				while ( $masks_result = mysql_fetch_array( $masks_query, MYSQL_ASSOC)){
					
					//clear slachses
					$masks_result = $mysql -> clear( $masks_result);
					
					$new_board_form -> addToContent( '<tr>
						<td class="opt_row1">'.$masks_result['users_perm_name'].'<br />(<a href="javascript:selectRow('.$masks_result['users_perm_id'].')">+</a> | <a href="javascript:emptyRow('.$masks_result['users_perm_id'].')">-</a>)</td>
						<td class="opt_row2" style="text-align: center">'.$new_board_form -> drawSelect( 'forum_access_show_forum['.$masks_result['users_perm_id'].']', $forum_access[$masks_result['users_perm_id']]['forums_access_show_forum']).'</td>
						<td class="opt_row1" style="text-align: center">'.$new_board_form -> drawSelect( 'forum_access_show_topics['.$masks_result['users_perm_id'].']', $forum_access[$masks_result['users_perm_id']]['forums_access_show_topics']).'</td>
						<td class="opt_row2" style="text-align: center">'.$new_board_form -> drawSelect( 'forum_access_reply_topics['.$masks_result['users_perm_id'].']', $forum_access[$masks_result['users_perm_id']]['forums_access_reply_topics']).'</td>
						<td class="opt_row1" style="text-align: center">'.$new_board_form -> drawSelect( 'forum_access_start_topics['.$masks_result['users_perm_id'].']', $forum_access[$masks_result['users_perm_id']]['forums_access_start_topics']).'</td>
						<td class="opt_row2" style="text-align: center">'.$new_board_form -> drawSelect( 'forum_access_attachments_upload['.$masks_result['users_perm_id'].']', $forum_access[$masks_result['users_perm_id']]['forums_access_attachments_upload']).'</td>
						<td class="opt_row1" style="text-align: center">'.$new_board_form -> drawSelect( 'forum_access_attachments_download['.$masks_result['users_perm_id'].']', $forum_access[$masks_result['users_perm_id']]['forums_access_attachments_download']).'</td>
					</tr>');					
					
					$boards_list .= "forums_list[".$board_num."] = ".$masks_result['users_perm_id']."\n";
					
					$board_num++;
					
				}
				
				$new_board_form -> closeTable();		
				$new_board_form -> drawButton( $language -> getString( 'acp_forums_subsection_boards_edit_button'));
				$new_board_form -> closeForm();
				$new_board_form -> addToContent( '<script type="text/javascript">
													
					function selectColumn( column){
										
						var columns = new Array()
						
						columns[0] = "forum_access_show_forum"	
						columns[1] = "forum_access_show_topics"	
						columns[2] = "forum_access_reply_topics"	
						columns[3] = "forum_access_start_topics"	
						columns[4] = "forum_access_attachments_upload"	
						columns[5] = "forum_access_attachments_download"	
								
						var forums_list = new Array()				
						'.$boards_list.'
						
						forums_to_move = forums_list.length
						
						for( var forum_num in forums_list){
						
							if( forums_to_move > 0){
														
								cp = document.forms["forum_form"].elements[columns[column] + "[" + forums_list[forum_num] + "]"]
								
								cp.checked = "checked"
						
								forums_to_move ++
								
							}
							
						}
							
					}
					
					function emptyColumn( column){
					
					var columns = new Array()
						
						columns[0] = "forum_access_show_forum"	
						columns[1] = "forum_access_show_topics"	
						columns[2] = "forum_access_reply_topics"	
						columns[3] = "forum_access_start_topics"	
						columns[4] = "forum_access_attachments_upload"	
						columns[5] = "forum_access_attachments_download"	
								
						var forums_list = new Array()				
						'.$boards_list.'
						
						forums_to_move = forums_list.length
						
						for( var forum_num in forums_list){
						
							if( forums_to_move > 0){

								cp = document.forms["forum_form"].elements[columns[column] + "[" + forums_list[forum_num] + "]"]
								
								cp.checked = ""
						
								forums_to_move ++
								
							}
							
						}
						
					}
					
					function selectRow( row){
					
						rp1 = document.forms["forum_form"].elements["forum_access_show_forum["+row+"]"];
						rp2 = document.forms["forum_form"].elements["forum_access_show_topics["+row+"]"];
						rp3 = document.forms["forum_form"].elements["forum_access_reply_topics["+row+"]"];
						rp4 = document.forms["forum_form"].elements["forum_access_start_topics["+row+"]"];
						rp5 = document.forms["forum_form"].elements["forum_access_attachments_upload["+row+"]"];
						rp6 = document.forms["forum_form"].elements["forum_access_attachments_download["+row+"]"];
						
						rp1.checked = "checked";
						rp2.checked = "checked";
						rp3.checked = "checked";
						rp4.checked = "checked";
						rp5.checked = "checked";
						rp6.checked = "checked";
					
					}
				
					function emptyRow( row){
					
						rp1 = document.forms["forum_form"].elements["forum_access_show_forum["+row+"]"];
						rp2 = document.forms["forum_form"].elements["forum_access_show_topics["+row+"]"];
						rp3 = document.forms["forum_form"].elements["forum_access_reply_topics["+row+"]"];
						rp4 = document.forms["forum_form"].elements["forum_access_start_topics["+row+"]"];
						rp5 = document.forms["forum_form"].elements["forum_access_attachments_upload["+row+"]"];
						rp6 = document.forms["forum_form"].elements["forum_access_attachments_download["+row+"]"];
												
						rp1.checked = "";
						rp2.checked = "";
						rp3.checked = "";
						rp4.checked = "";
						rp5.checked = "";
						rp6.checked = "";
					
					}
					
				</script>');
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_boards_edit'), $new_board_form -> display()));
								
			}else{
				
				/**
				 * board not found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_edit'), $language -> getString( 'acp_forums_subsection_board_target_notfound')));
				
				$this -> act_boards_manage();
				
			}
			
		}else{
			
			/**
			 * wrong forum to edit
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_edit'), $language -> getString( 'acp_forums_subsection_board_target_wrong')));
			
			$this -> act_boards_manage();
			
		}
		
	}
	
	function act_delete_board(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * get forum to admin
		 */
		
		$forum_to_admin = $_GET['board'];
		
		settype( $forum_to_admin, 'integer');
		
		if ( $forum_to_admin > 0){
			
			/**
			 * proper forum id
			 * select forum from MySQL
			 */
			
			$forum_query = $mysql -> query( "SELECT * FROM forums WHERE `forum_id` = '$forum_to_admin'");
			
			if ( $forum_result = mysql_fetch_array( $forum_query, MYSQL_ASSOC)){
				
				/**
				 * clear result
				 */
				
				$forum_result = $mysql -> clear( $forum_result);
				
				/**
				 * add breadcrumbs
				 */
				
				$path_link = array( 'act' => 'boards');
				
				$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_boards'), parent::adminLink( parent::getId(), $path_link));		
				$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_boards'), parent::adminLink( parent::getId(), $path_link));
				
				$path_link = array( 'act' => 'delete_board', 'board' => $forum_to_admin);
				
				$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_boards_delete'), parent::adminLink( parent::getId(), $path_link));
				
				/**
				 * set page title
				 */
				
				$output -> setTitle( $language -> getString( 'acp_forums_subsection_boards_delete'));
				
				/**
				 * draw form
				 */

				$form_delete_form = new form();
				$form_delete_form -> openForm( parent::adminLink( parent::getId(), array('act' => 'kill_board', 'board' => $forum_to_admin)));
				$form_delete_form -> openOpTable();
				
				$forums_list = $forums -> getForumsList( $forum_to_admin);
				
				$forums_list[0] = $language -> getString( 'acp_forums_subsection_boards_delete_replace_0');
				
				$form_delete_form -> drawList( $language -> getString( 'acp_forums_subsection_boards_delete_replace'), 'forum_replace', $forums_list);
				
				$form_delete_form -> closeTable();
				$form_delete_form -> drawButton( $language -> getString( 'acp_forums_subsection_boards_delete_button'));
				$form_delete_form -> closeForm();
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_boards_delete'), $form_delete_form -> display()));
				
			}else{
				
				/**
				 * board not found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_delete'), $language -> getString( 'acp_forums_subsection_board_target_notfound')));
				
				$this -> act_boards_manage();
				
			}
			
		}else{
			
			/**
			 * wrong forum to edit
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_delete'), $language -> getString( 'acp_forums_subsection_board_target_wrong')));
			
			$this -> act_boards_manage();
			
		}
		
	}
	
	function act_boards_manage(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'boards');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_boards'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_boards'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_forums_subsection_boards'));
		
		/**
		 * actions
		 */
		
		if ( $_GET['do'] == 'board_up' && !empty( $_GET['board'])){
			
			$board_to_move = $_GET['board'];
			settype( $board_to_move, 'integer');
			
			$forums -> forumUp($board_to_move);
			
		}
		
		if ( $_GET['do'] == 'board_down' && !empty( $_GET['board'])){
			
			$board_to_move = $_GET['board'];
			settype( $board_to_move, 'integer');
			
			$forums -> forumDown($board_to_move);
			
		}
		
		if ( $_GET['do'] == 'resynchronize' && !empty( $_GET['board']))
			$forums -> forumResynchronise( $_GET['board']);
		
		/**
		 * add mod
		 */
		
		if ( $session -> checkForm()){
			
			$forums_to_add_mods = $_POST['forum_mods'];
			
			if ( gettype( $forums_to_add_mods) == 'array'){
				
				/**
				 * list submitted
				 * build up lsit of mods
				 */
				
				$mods_query = $mysql -> query( "SELECT * FROM moderators");
				
				$forum_mods_existings_users = array();
				$forum_mods_existings_groups = array();
				
				while ( $mods_result = mysql_fetch_array( $mods_query, MYSQL_ASSOC)){
					
					$mods_result = $mysql -> clear($mods_result);
					
					if ( $mods_result['moderator_user_id'] > 0)
						$forum_mods_existings_users[$mods_result['moderator_forum_id']][$mods_result['moderator_user_id']] = true;
					
					if ( $mods_result['moderator_group_id'] > 0)
						$forum_mods_existings_groups[$mods_result['moderator_forum_id']][$mods_result['moderator_group_id']] = true;
						
				}
				
				/**
				 * get moderator to add
				 */
				
				$new_mod_user = uniSlashes(htmlspecialchars(trim($_POST['moderator_user'])));
				$new_mod_group = $_POST['moderator_group'];
				
				settype( $new_mod_group, 'integer');
				
				if ( strlen( $new_mod_user) != 0){
					
					/**
					 * add user
					 * check first, if user exists
					 */
					
					$user_mod_query = $mysql -> query( "SELECT user_id FROM users WHERE `user_login` LIKE '$new_mod_user' LIMIT 1");
					
					if ( $user_mod_result = mysql_fetch_array( $user_mod_query, MYSQL_ASSOC)){
						
						/**
						 * user found
						 */
						
						$user_mod_id = $user_mod_result['user_id'];
						
						foreach ( $forums_to_add_mods as $forum_id => $forum_add_mod){
						
							//set type
							settype( $forum_id, 'integer');
							
							/**
							 * check if group is already an mod
							 */
							
							settype( $forum_mods_existings_users[$forum_id], 'array');
							
							if ( !key_exists( $user_mod_id, $forum_mods_existings_users[$forum_id])){
								
								$mysql -> insert( array( 'moderator_forum_id' => $forum_id, 'moderator_user_id' => $user_mod_id), 'moderators');
								
							}
							
						}
					}
					
				}else{
					
					/**
					 * add group
					 */
					
					foreach ( $forums_to_add_mods as $forum_id => $forum_add_mod){
						
						//set type
						settype( $forum_id, 'integer');
						
						/**
						 * check if group is already an mod
						 */
						
						settype( $forum_mods_existings_groups[$forum_id], 'array');
						
						if ( !key_exists( $new_mod_group, $forum_mods_existings_groups[$forum_id])){
							
							$mysql -> insert( array( 'moderator_forum_id' => $forum_id, 'moderator_group_id' => $new_mod_group), 'moderators');
														
						}
						
					}
										
				}
				
			}
			
			$cache -> flushCache( 'moderators');
			
		}
		
		/**
		 * delete mod
		 */
		
		if ( isset( $_GET['remove_mod'])){
			
			$mod_to_delete = $_GET['remove_mod'];
			settype( $mod_to_delete, 'integer');
			
			$mysql -> delete( 'moderators', "`moderator_id` = '$mod_to_delete'");
			
			$cache -> flushCache( 'moderators');
			
		}
		
		/**
		 * list of mods
		 */
		
		$mods_query = $mysql -> query( "SELECT m.*, u.user_login, g.users_group_name FROM moderators m LEFT JOIN users u ON m.moderator_user_id = u.user_id LEFT JOIN users_groups g ON m.moderator_group_id = g.users_group_id");
		$forum_mods_stats = array();
		while ( $mods_result = mysql_fetch_array( $mods_query, MYSQL_ASSOC)){
			
			$mods_result = $mysql -> clear($mods_result);
			
			if ( $mods_result['moderator_user_id'] > 0)
				$forum_mods_stats[$mods_result['moderator_forum_id']]['users'][] = $mods_result['user_login'].' <a href="'.parent::adminLink( parent::getId(), array( 'act' => 'boards', 'remove_mod' => $mods_result['moderator_id'])).'">'.$style -> drawImage( 'delete', 'delete').'</a>';
			
			if ( $mods_result['moderator_group_id'] > 0)
				$forum_mods_stats[$mods_result['moderator_forum_id']]['groups'][] = $mods_result['users_group_name'].' <a href="'.parent::adminLink( parent::getId(), array( 'act' => 'boards', 'remove_mod' => $mods_result['moderator_id'])).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a>';
				
		}
		
		/**
		 * now draw list of boards
		 */
		
		$found_boards = array();
		
		$boards_query = $mysql -> query( "SELECT * FROM forums ORDER BY `forum_pos`");
		
		while ( $boards_result = mysql_fetch_array( $boards_query, MYSQL_ASSOC)){
			
			$boards_result = $mysql -> clear($boards_result);
			
			if ( !key_exists( $boards_result['forum_parent'], $found_boards))
				$found_boards[$boards_result['forum_parent']] = array();
				
			$found_boards[$boards_result['forum_parent']][$boards_result['forum_id']] = array(
				'forum_parent' => $boards_result['forum_parent'],
				'forum_pos' => $boards_result['forum_pos'],
				'forum_type' => $boards_result['forum_type'],
				'forum_name' => $boards_result['forum_name'],
				'forum_info' => $boards_result['forum_info'],
			);
			
		}
		
		//reorder
		$found_boards = hierarchy_class::buildTree( $found_boards, 0, 'hierarchy_image', 'hierarchy_depth', 1);
		
		/**
		 * begin drawing forums
		 */
				
		$forums_list = new form();
		$forums_list -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'boards')));
		$forums_list -> openOpTable();
		$forums_list -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_forums_subsection_boards_list_board_name').'</th>
			<th>'.$language -> getString( 'acp_forums_subsection_boards_list_board_type').'</th>
			<th>'.$language -> getString( 'actions').'</th>
			<th>&nbsp;</th>
		</tr>');
		
		/**
		 * draw table now
		 */
		
		$board_types[0] = $language -> getString('forum_category');
		$board_types[1] = $language -> getString('forum_forum');
		$board_types[2] = $language -> getString('forum_url');
		
		foreach ( $found_boards as $board_id => $board_ops){
			
			/**
			 * moderators
			 */
			
			$board_mods = '';
			
			if ( key_exists( $board_id, $forum_mods_stats)){
								
				settype( $forum_mods_stats[$board_id]['users'], 'array');
				settype( $forum_mods_stats[$board_id]['groups'], 'array');
				
				$forum_mods = array_merge( $forum_mods_stats[$board_id]['users'], $forum_mods_stats[$board_id]['groups']);
				
				settype( $forum_mods, 'array');
				
				$board_mods = join( ", ", $forum_mods).' ';
				
			}else{
				
				$board_mods = $language -> getString( 'acp_forums_subsection_boards_list_mods_none').' ';
				
			}
			
			/**
			 * links
			 */
			
			$board_up_link = array( 'act' => 'boards', 'do' => 'board_up', 'board' => $board_id);
			$board_down_link = array( 'act' => 'boards', 'do' => 'board_down', 'board' => $board_id);
			$board_rebuild_link = array( 'act' => 'boards', 'do' => 'resynchronize', 'board' => $board_id);
			$board_edit_link = array( 'act' => 'edit_board', 'board' => $board_id);
			$board_delete_link = array( 'act' => 'delete_board', 'board' => $board_id);
			
			/**
			 * insert row
			 */
			
			$forums_list -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%">
					<table border="0" width: 100%>
						<tr>
							<td NOWRAP>
								'.$board_ops['hierarchy_image'].'
							</td>
							<td style="width: 100%">
								<b>'.$board_ops['forum_name'].'</b>
								<br />'.$language -> getString( 'acp_forums_subsection_boards_list_mods').': '.$board_mods.'
							</td>
						</tr>
					</table>
				</td>
				<td class="opt_row2" NOWRAP>'.$board_types[$board_ops['forum_type']].'</td>
				<td class="opt_row3" style="text-align: center" NOWRAP>
				<a href="'.parent::adminLink( parent::getId(), $board_up_link).'">'.$style -> drawImage( 'go_up', $language -> getString( 'go_up')).'</a>
				<a href="'.parent::adminLink( parent::getId(), $board_down_link).'">'.$style -> drawImage( 'go_down', $language -> getString( 'go_down')).'</a>
				<a href="'.parent::adminLink( parent::getId(), $board_rebuild_link).'">'.$style -> drawImage( 'resync', $language -> getString( 'acp_forums_subsection_boards_list_board_resunchronize')).'</a>
				<a href="'.parent::adminLink( parent::getId(), $board_edit_link).'">'.$style -> drawImage( 'edit', $language -> getString( 'edit')).'</a>
				<a href="'.parent::adminLink( parent::getId(), $board_delete_link).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a>
				</td>
				<td class="opt_row3" style="text-align: center" NOWRAP>'.$forums_list -> drawSelect( 'forum_mods['.$board_id.']').'</td>
			</tr>');
					
		}
		
		/**
		 * close and draw
		 */
		
		$forums_list -> closeTable();
		$forums_list -> drawSpacer( $language -> getString( 'acp_forums_subsection_boards_list_mods_new'));
		$forums_list -> openOpTable();
		
		/**
		 * add mod
		 */
		
		$users_groups ='';
		$users_groups_query = $mysql -> query( "SELECT users_group_id, users_group_name FROM users_groups ORDER BY users_group_can_use_acp DESC, users_group_can_moderate DESC, users_group_name");
		
		while ( $users_group_result = mysql_fetch_array( $users_groups_query, MYSQL_ASSOC)){
		
			$users_group_result = $mysql -> clear( $users_group_result);
		
			$users_groups .= '<option value="'.$users_group_result['users_group_id'].'">'.$users_group_result['users_group_name'].'</option>';
		
		}
		
		$forums_list -> drawRow( '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed">
		  <tr>
		    <td>'.$language -> getString( 'acp_forums_subsection_boards_list_mods_new_user').'<br /><input name="moderator_user" type="text"></td>
		    <td>'.$language -> getString( 'acp_forums_subsection_boards_list_mods_new_or_group').'<br /><select name="moderator_group">'.$users_groups.'</select></td>
		    <td><input name="'.$language -> getString( 'acp_forums_subsection_boards_list_mods_new_add').'" value="'.$language -> getString( 'acp_forums_subsection_boards_list_mods_new_add').'" type="submit"></td>
		  </tr>
		</table>');
		
		$forums_list -> closeTable();
		$forums_list -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_boards_list'), $forums_list -> display()));
		
	}
	
	function act_forums_perms(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'boards_perms');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_boards'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_boards_perms'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_forums_subsection_boards_perms'));
		
		/**
		 * add new perms
		 */
		
		if ( $session -> checkForm()){
			
			$new_perms_name = $strings -> inputClear( $_POST['new_perms_name'], false);
			
			if ( strlen( $new_perms_name) > 0){
				
				$new_pems_sql['users_perm_name'] = $new_perms_name;
				
				$mysql -> insert( $new_pems_sql, 'users_perms');
				
				$cache -> flushCache( 'permissions');
				
				/**
				 * add log
				 */
				
				$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_boards_perms_new_add_log'), array( 'forums_perms' => $new_perms_name));
				
				/**
				 * draw page
				 */
				
				parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_boards_perms_new'), $language -> getString( 'acp_forums_subsection_boards_perms_new_add_done')));
				
			}
			
		}
		
		/**
		 * delete existing perms
		 */
		
		if ( isset( $_GET['delete_perms'])){
			
			$perms_to_delete = $_GET['delete_perms'];
			
			settype( $perms_to_delete, 'integer');
			
			/**
			 * check, if they are used
			 */
			
			$users_perms_use = $mysql -> countRows( 'users', "`user_permissions` = '$perms_to_delete'");
			$groups_perms_use = $mysql -> countRows( 'users_groups', "`users_group_permissions` = '$perms_to_delete'");
		
			if ( $users_perms_use == 0 && $groups_perms_use == 0){
				
				/**
				 * perms not use
				 */
				
				$mysql -> delete( 'users_perms', "`users_perm_id` = '$perms_to_delete'");
				
				$mysql -> delete( 'forums_access', "`forums_acess_perms_id` = '$perms_to_delete'");
						
				$cache -> flushCache( 'permissions');
				
				/**
				 * add log
				 */
				
				$logs -> addAdminLog( $language -> getString( 'acp_forums_subsection_boards_perms_delete_log'), array( 'perms_delete_id' => $perms_to_delete));
				
				/**
				 * draw message
				 */
				
				parent::draw( $style -> drawInfoBlock( $language -> getString('acp_forums_subsection_boards_perms_delete'), $language -> getString('acp_forums_subsection_boards_perms_delete_done')));
				
			}else{
				
				/**
				 * perms in use
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString('acp_forums_subsection_boards_perms_delete'), $language -> getString('acp_forums_subsection_boards_perms_delete_inuse')));
				
			}
				
		}
		
		/**
		 * draw list
		 */
				
		$perms_list = new form();
		$perms_list -> openOpTable();
		$perms_list -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_forums_subsection_boards_perms_list_name').'</th>
			<th>'.$language -> getString( 'actions').'</th>
		</tr>');
		
		/**
		 * begin drawing summary table
		 */
		
		$perms_query = $mysql -> query( "SELECT * FROM users_perms ORDER BY `users_perm_name`");
		
		while ( $perms_result = mysql_fetch_array( $perms_query, MYSQL_ASSOC)) {
			
			//clear result
			$perms_result = $mysql -> clear( $perms_result);
			
			/**
			 * draw row
			 */
						
			$perms_list -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%">'.$perms_result['users_perm_name'].'</td>
				<td class="opt_row3" style="text-align: center" NOWRAP="nowrap">
				<a href="'.parent::adminLink( parent::getId(), array( 'act' => 'edit_perms', 'perms' => $perms_result['users_perm_id'])).'">'.$style -> drawImage( 'edit', $language -> getString( 'edit')).'</a>
				<a href="'.parent::adminLink( parent::getId(), array( 'act' => 'boards_perms', 'delete_perms' => $perms_result['users_perm_id'])).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a>
				</td>
			</tr>');
		
		}
		
		/**
		 * close table
		 */
		
		$perms_list -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString('acp_forums_subsection_boards_perms'), $perms_list -> display()));
		
		/**
		 * new perms
		 */
		
		$new_perms_form = new form();
		$new_perms_form -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'boards_perms')));
		$new_perms_form -> openOpTable();
		
		$new_perms_form -> drawTextInput( $language -> getString( 'acp_forums_subsection_boards_perms_new_name'), 'new_perms_name');
		
		$new_perms_form -> closeTable();
		$new_perms_form -> drawButton( $language -> getString( 'acp_forums_subsection_boards_perms_new_add_button'));
		$new_perms_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString('acp_forums_subsection_boards_perms_new'), $new_perms_form -> display()));
		
	}
	
	function act_edit_perms(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * get perms to admin
		 */
		
		$perms_to_admin = $_GET['perms'];
		settype( $perms_to_admin, 'integer');
		
		if ( $perms_to_admin > 0){
			
			/**
			 * select it
			 */
			
			$perms_query = $mysql -> query( "SELECT * FROM users_perms WHERE `users_perm_id` = '$perms_to_admin'");
			
			if ( $perms_result = mysql_fetch_array( $perms_query, MYSQL_ASSOC)){
				
				/**
				 * add breadcrumbs
				 */
				
				$path_link = array( 'act' => 'boards_perms');
				
				$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_boards'), parent::adminLink( parent::getId(), $path_link));		
				$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_boards_perms'), parent::adminLink( parent::getId(), $path_link));
				
				$path_link = array( 'act' => 'edit_perms');
				
				$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_boards_perms_edit'), parent::adminLink( parent::getId(), $path_link));
				
				/**
				 * set page title
				 */
				
				$output -> setTitle( $language -> getString( 'acp_forums_subsection_boards_perms_edit'));
			
				/**
				 * set values
				 */
				
				$perms_name = $perms_result['users_perm_name'];
					
				if ( $_GET['act'] == 'change_perms')
					$perms_name = $strings -> inputClear( $_POST['perms_name'], false);
				
				/**
				 * begin drawing form
				 */
			
				$perms_edit_form = new form();
				$perms_edit_form -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'change_perms', 'perms' => $perms_to_admin)), 'POST', false, 'forum_form');
				$perms_edit_form -> openOpTable();
				
				$perms_edit_form -> drawTextInput( $language -> getString( 'acp_forums_subsection_boards_perms_new_name'), 'perms_name', $perms_name);
				
				$perms_edit_form -> closeTable();
				$perms_edit_form -> drawSpacer( $language -> getString( 'acp_forums_subsection_boards_perms_edit_forums'));
				$perms_edit_form -> openOpTable( true);
				
				/**
				 * add masks
				 */
				
				$perms_edit_form -> addToContent( '<tr>
					<th>&nbsp;</th>
					<th>'.$language -> getString( 'forums_access_show_forum').']
					<br />(<a href="javascript:selectColumn(0)">+</a> | <a href="javascript:emptyColumn(0)">-</a>)</th>
					<th>'.$language -> getString( 'forums_access_show_topics').'
					<br />(<a href="javascript:selectColumn(1)">+</a> | <a href="javascript:emptyColumn(1)">-</a>)</th>
					<th>'.$language -> getString( 'forums_access_reply_topics').'
					<br />(<a href="javascript:selectColumn(2)">+</a> | <a href="javascript:emptyColumn(2)">-</a>)</th>
					<th>'.$language -> getString( 'forums_access_start_topics').'
					<br />(<a href="javascript:selectColumn(3)">+</a> | <a href="javascript:emptyColumn(3)">-</a>)</th>
					<th>'.$language -> getString( 'forums_access_attachments_upload').'
					<br />(<a href="javascript:selectColumn(4)">+</a> | <a href="javascript:emptyColumn(4)">-</a>)</th>
					<th>'.$language -> getString( 'forums_access_attachments_download').'
					<br />(<a href="javascript:selectColumn(5)">+</a> | <a href="javascript:emptyColumn(5)">-</a>)</th>
				</tr>');
				
				/**
				 * select perms
				 */
				
				$perms_query = $mysql -> query( "SELECT * FROM forums_access WHERE `forums_acess_perms_id` = '$perms_to_admin'");
				
				while ( $perms_result = mysql_fetch_array( $perms_query, MYSQL_ASSOC)){
					
					$perms_access[$perms_result['forums_acess_forum_id']] = array(
						'forums_access_show_forum' => $perms_result['forums_access_show_forum'],
						'forums_access_show_topics' => $perms_result['forums_access_show_topics'],
						'forums_access_reply_topics' => $perms_result['forums_access_reply_topics'],
						'forums_access_start_topics' => $perms_result['forums_access_start_topics'],
						'forums_access_attachments_upload' => $perms_result['forums_access_attachments_upload'],
						'forums_access_attachments_download' => $perms_result['forums_access_attachments_download']
					);
					
				}
				
				/**
				 * and forums
				 */
				
				$last_cat = array();
		
				$boards_query = $mysql -> query( "SELECT forum_id, forum_name, forum_parent, forum_pos FROM forums ORDER BY `forum_parent` DESC, `forum_pos`");
				
				while ( $boards_result = mysql_fetch_array( $boards_query, MYSQL_ASSOC)){
					
					$boards_result = $mysql -> clear($boards_result);
					
					$found_boards[$boards_result['forum_id']] = array(
						'forum_parent' => $boards_result['forum_parent'],
						'forum_pos' => $boards_result['forum_pos'],
						'forum_name' => $boards_result['forum_name'],
					);
					
					$last_cat[$boards_result['forum_parent']] = $boards_result['forum_id'];
				}
								
				/**
				 * draw table now
				 */

				$board_number = 0;
							
				foreach ( $found_boards as $board_id => $board_ops){
		
					$code_base = $drawed_list[$board_id];
						
					/**
					 * get depth
					 */
					
					$level = 0;
					
					$lvl_search = $board_ops['forum_parent'];
					
					$a = 0;
					
					while( $lvl_search != 0){
						
						$level ++;
						$lvl_search = $found_boards[$lvl_search]['forum_parent'];
						
					}
					
					$tree_img = '';
							
					while ( $level > 1){
						
						$level --;
						$tree_img .= '--';
						
					}
					
					
					if ( !$board_ops['forum_parent'] == 0){
					
						$tree_img .= '--';
					
					}
					
					/**
					 * insert row
					 */
					
					$code_base = ('<tr>
						<td class="opt_row1" style="width: 100%">
							'.$tree_img.'<b>'.$board_ops['forum_name'].' (<a href="javascript:selectRow('.$board_id.')">+</a> | <a href="javascript:emptyRow('.$board_id.')">-</a>)</b>
						</td>
						<td class="opt_row2" style="text-align: center" NOWRAP>'.$perms_edit_form -> drawSelect( 'forums_access_show_forum['.$board_id.']', $perms_access[$board_id]['forums_access_show_forum']).'</td>
						<td class="opt_row1" style="text-align: center" NOWRAP>'.$perms_edit_form -> drawSelect( 'forums_access_show_topics['.$board_id.']', $perms_access[$board_id]['forums_access_show_topics']).'</td>
						<td class="opt_row2" style="text-align: center" NOWRAP>'.$perms_edit_form -> drawSelect( 'forums_access_reply_topics['.$board_id.']', $perms_access[$board_id]['forums_access_reply_topics']).'</td>
						<td class="opt_row1" style="text-align: center" NOWRAP>'.$perms_edit_form -> drawSelect( 'forums_access_start_topics['.$board_id.']', $perms_access[$board_id]['forums_access_start_topics']).'</td>
						<td class="opt_row2" style="text-align: center" NOWRAP>'.$perms_edit_form -> drawSelect( 'forums_access_attachments_upload['.$board_id.']', $perms_access[$board_id]['forums_access_attachments_upload']).'</td>
						<td class="opt_row1" style="text-align: center" NOWRAP>'.$perms_edit_form -> drawSelect( 'forums_access_attachments_download['.$board_id.']', $perms_access[$board_id]['forums_access_attachments_download']).'</td>
					</tr>').$code_base;
				
					$drawed_list[$board_ops['forum_parent']] .= $code_base; 
					
					/**
					 * entry for java
					 */
					
					$boards_list .= 'forums_list['.$board_number.'] = '.$board_id."\n";
					$board_number ++;			
				}
				
				/**
				 * close and draw
				 */
				
				$perms_edit_form -> addToContent( $drawed_list[0]);
				
				/**
				 * and javascripts
				 */
				
				$perms_edit_form -> addToContent( '<script type="text/javascript">
													
					function selectColumn( column){
										
						var columns = new Array()
						
						columns[0] = "forums_access_show_forum"	
						columns[1] = "forums_access_show_topics"	
						columns[2] = "forums_access_reply_topics"	
						columns[3] = "forums_access_start_topics"	
						columns[4] = "forums_access_attachments_upload"	
						columns[5] = "forums_access_attachments_download"	
								
						var forums_list = new Array()				
						'.$boards_list.'
						
						forums_to_move = forums_list.length
						
						for( var forum_num in forums_list){
						
							if( forums_to_move > 0){
														
								cp = document.forms["forum_form"].elements[columns[column] + "[" + forums_list[forum_num] + "]"]
								
								cp.checked = "checked"
						
								forums_to_move ++
								
							}
							
						}
							
					}
					
					function emptyColumn( column){
					
					var columns = new Array()
						
						columns[0] = "forums_access_show_forum"	
						columns[1] = "forums_access_show_topics"	
						columns[2] = "forums_access_reply_topics"	
						columns[3] = "forums_access_start_topics"	
						columns[4] = "forums_access_attachments_upload"	
						columns[5] = "forums_access_attachments_download"	
								
						var forums_list = new Array()				
						'.$boards_list.'
						
						forums_to_move = forums_list.length
						
						for( var forum_num in forums_list){
						
							if( forums_to_move > 0){
														
								cp = document.forms["forum_form"].elements[columns[column] + "[" + forums_list[forum_num] + "]"]
								
								cp.checked = ""
						
								forums_to_move ++
								
							}
							
						}
						
					}
					
					function selectRow( row){
					
						rp1 = document.forms["forum_form"].elements["forums_access_show_forum["+row+"]"];
						rp2 = document.forms["forum_form"].elements["forums_access_show_topics["+row+"]"];
						rp3 = document.forms["forum_form"].elements["forums_access_reply_topics["+row+"]"];
						rp4 = document.forms["forum_form"].elements["forums_access_start_topics["+row+"]"];
						rp5 = document.forms["forum_form"].elements["forums_access_attachments_upload["+row+"]"];
						rp6 = document.forms["forum_form"].elements["forums_access_attachments_download["+row+"]"];
												
						rp1.checked = "checked";
						rp2.checked = "checked";
						rp3.checked = "checked";
						rp4.checked = "checked";
						rp5.checked = "checked";
						rp6.checked = "checked";
					
					}
				
					function emptyRow( row){
						
						rp1 = document.forms["forum_form"].elements["forums_access_show_forum["+row+"]"];
						rp2 = document.forms["forum_form"].elements["forums_access_show_topics["+row+"]"];
						rp3 = document.forms["forum_form"].elements["forums_access_reply_topics["+row+"]"];
						rp4 = document.forms["forum_form"].elements["forums_access_start_topics["+row+"]"];
						rp5 = document.forms["forum_form"].elements["forums_access_attachments_upload["+row+"]"];
						rp6 = document.forms["forum_form"].elements["forums_access_attachments_download["+row+"]"];
						
						rp1.checked = "";
						rp2.checked = "";
						rp3.checked = "";
						rp4.checked = "";
						rp5.checked = "";
						rp6.checked = "";
					
					}
					
				</script>');
						
				$perms_edit_form -> closeTable();
				$perms_edit_form -> drawButton( $language -> getString( 'acp_forums_subsection_boards_perms_edit_button'));
				$perms_edit_form -> closeForm();
				
				/**
				 * draw form
				 */
			
				parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_boards_perms_edit'), $perms_edit_form -> display()));
				
			}else{
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_perms_edit'), $language -> getString( 'acp_forums_subsection_boards_perms_edit_notfound')));
			
				$this -> act_forums_perms();
				
			}
			
		}else{
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_boards_perms_edit'), $language -> getString( 'acp_forums_subsection_boards_perms_edit_notarget')));
			
			$this -> act_forums_perms();
			
		}
		
	}
	
	function act_topics_prefixes(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'topics_prefixes');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_boards'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_topics_prefixes'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_forums_subsection_topics_prefixes'));
		
		if ( $_GET['reorder'] && $session -> checkForm()){
			
			$prefixes_to_reorder = $_POST['prefixes'];
			
			settype( $prefixes_to_reorder, 'array');
			
			foreach ( $prefixes_to_reorder as $prefix_id => $prefix_pos){
				
				settype( $prefix_id, 'integer');
				settype( $prefix_pos, 'integer');
				
				$mysql -> update( array( 'topic_prefix_pos' => $prefix_pos), "topics_prefixes", "`topic_prefix_id` = '$prefix_id'");
				
			}
			
			$cache -> flushCache( 'prefixes');
			
		}
		
		/**
		 * begin drawing table
		 */
		
		$prefixes_tab = new form();
		$prefixes_tab -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'topics_prefixes', 'reorder' => true)));
		$prefixes_tab -> openOpTable();
		$prefixes_tab -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_forums_subsection_topics_prefixes_name').'</th>
			<th>'.$language -> getString( 'acp_forums_subsection_topics_prefixes_html').'</th>
			<th>'.$language -> getString( 'acp_forums_subsection_topics_prefixes_pos').'</th>
			<th>'.$language -> getString( 'actions').'</th>
		</tr>');
		
		/**
		 * select prefixes from mysql
		 */
		
		$prefixes_query = $mysql -> query( "SELECT * FROM topics_prefixes ORDER BY topic_prefix_pos DESC, topic_prefix_name");
		
		while ( $prefix_result = mysql_fetch_array( $prefixes_query, MYSQL_ASSOC)) {
			
			//clear
			$prefix_result = $mysql -> clear( $prefix_result);
			
			/**
			 * add row
			 */
			$prefixes_tab -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%">'.$prefix_result['topic_prefix_name'].'</td>
				<td class="opt_row2" style="text-align: center" nowrap="nowrap">'.htmlspecialchars_decode($prefix_result['topic_prefix_html']).'</td>
				<td class="opt_row1" style="text-align: center" nowrap="nowrap"><input name="prefixes['.$prefix_result['topic_prefix_id'].']" size="5" type="text" value="'.$prefix_result['topic_prefix_pos'].'"/></td>
				<td class="opt_row3" style="text-align: center" nowrap="nowrap">
				<a href="'.parent::adminLink( parent::getId(), array( 'act' => 'topics_prefixes_edit', 'prefix' => $prefix_result['topic_prefix_id'])).'">'.$style -> drawImage( 'edit', $language -> getString( 'edit')).'</a>
				<a href="'.parent::adminLink( parent::getId(), array( 'act' => 'topics_prefixes_delete', 'prefix' => $prefix_result['topic_prefix_id'])).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a>
				</td>
			</tr>');
				
		}
		
		/**
		 * finish and draw table
		 */
		
		$prefixes_tab -> closeTable();
		$prefixes_tab -> drawButton( $language -> getString( 'acp_forums_subsection_topics_prefixes_reorder'), false, '<input type="button" name="Submit" value="'.$language -> getString( 'acp_forums_subsection_topics_prefixes_new').'" onclick="document.location=\''.parent::adminLink( parent::getId(), array('act' => 'topics_prefixes_new')).'\'"/>');
		$prefixes_tab -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes'), $prefixes_tab -> display()));
		
	}
	
	function act_topics_prefixes_new(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'topics_prefixes_new');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_boards'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_topics_prefixes'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_topics_prefixes_new'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_forums_subsection_topics_prefixes_new'));
		
		/**
		 * set blank values
		 */
		
		$prefix_name = '';
		$prefix_html = '';
		$prefix_pos = 0;
		$prefix_forums = '';
		
		/**
		 * build up na list of forums
		 */
		
		$forums_list = $forums -> getForumsList();
		
		unset( $forums_list[0]);
		
		/**
		 * retake vars
		 */
		
		if ( $_GET['act'] == 'topics_prefixes_add'){
			
			$prefix_name = stripslashes( $strings -> inputClear( $_POST['prefix_name'], false));
			$prefix_html = stripslashes( $strings -> inputClear( $_POST['prefix_html'], false));
			$prefix_pos = $_POST['prefix_pos'];
			$prefix_forums = $_POST['prefix_forums'];
			
			settype( $prefix_pos, 'integer');			
			settype( $prefix_forums, 'array');
			
		}
		
		/**
		 * retake values
		 */
		
		/**
		 * draw form
		 */
		
		$new_prefix = new form();
		$new_prefix -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'topics_prefixes_add')));
		$new_prefix -> openOpTable();
		$new_prefix -> drawTextInput( $language -> getString( 'acp_forums_subsection_topics_prefixes_prefix_name'), 'prefix_name', $prefix_name);
		$new_prefix -> drawTextInput( $language -> getString( 'acp_forums_subsection_topics_prefixes_prefix_html'), 'prefix_html', $prefix_html);
		$new_prefix -> drawTextInput( $language -> getString( 'acp_forums_subsection_topics_prefixes_prefix_pos'), 'prefix_pos', $prefix_pos);
		$new_prefix -> drawMultiList( $language -> getString( 'acp_forums_subsection_topics_prefixes_prefix_forums'), 'prefix_forums[]', $forums_list, $prefix_forums);
		$new_prefix -> closeTable();
		$new_prefix -> drawButton( $language -> getString( 'acp_forums_subsection_topics_prefixes_prefix_save'));
		$new_prefix -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_new'), $new_prefix -> display()));
	}
	
	function act_topics_prefixes_edit(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * get prefix to edit
		 */
		
		$prefix_to_edit = $_GET['prefix'];
		settype( $prefix_to_edit, 'integer');
		
		/**
		 * select event
		 */
		
		$event_query = $mysql -> query( "SELECT * FROM topics_prefixes WHERE `topic_prefix_id` = '$prefix_to_edit'");
		
		if ( $prefix_result = mysql_fetch_array( $event_query, MYSQL_ASSOC)){
		
			//clear result
			$prefix_result = $mysql -> clear( $prefix_result);
			
			/**
			 * add breadcrumbs
			 */
			
			$path_link = array( 'act' => 'topics_prefixes_edit', 'prefix' => $prefix_to_edit);
			
			$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_boards'), parent::adminLink( parent::getId(), $path_link));		
			$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_topics_prefixes'), parent::adminLink( parent::getId(), $path_link));
			$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_topics_prefixes_edit'), parent::adminLink( parent::getId(), $path_link));
			
			/**
			 * set page title
			 */
			
			$output -> setTitle( $language -> getString( 'acp_forums_subsection_topics_prefixes_edit'));
			
			/**
			 * set blank values
			 */
			
			$prefix_name = $prefix_result['topic_prefix_name'];
			$prefix_html = $prefix_result['topic_prefix_html'];
			$prefix_pos = $prefix_result['topic_prefix_pos'];
			$prefix_forums = split( ",", $prefix_result['topic_prefix_forums']);
			
			/**
			 * build up na list of forums
			 */
			
			$forums_list = $forums -> getForumsList();
			
			unset( $forums_list[0]);
			
			/**
			 * retake vars
			 */
			
			if ( $_GET['act'] == 'topics_prefixes_add'){
				
				$prefix_name = stripslashes( $strings -> inputClear( $_POST['prefix_name'], false));
				$prefix_html = stripslashes( $strings -> inputClear( $_POST['prefix_html'], false));
				$prefix_pos = $_POST['prefix_pos'];
				$prefix_forums = $_POST['prefix_forums'];
				
				settype( $prefix_pos, 'integer');			
				settype( $prefix_forums, 'array');
				
			}
						
			/**
			 * draw form
			 */
			
			$new_prefix = new form();
			$new_prefix -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'topics_prefixes_change', 'prefix' => $prefix_to_edit)));
			$new_prefix -> openOpTable();
			$new_prefix -> drawTextInput( $language -> getString( 'acp_forums_subsection_topics_prefixes_prefix_name'), 'prefix_name', $prefix_name);
			$new_prefix -> drawTextInput( $language -> getString( 'acp_forums_subsection_topics_prefixes_prefix_html'), 'prefix_html', $prefix_html);
			$new_prefix -> drawTextInput( $language -> getString( 'acp_forums_subsection_topics_prefixes_prefix_pos'), 'prefix_pos', $prefix_pos);
			$new_prefix -> drawMultiList( $language -> getString( 'acp_forums_subsection_topics_prefixes_prefix_forums'), 'prefix_forums[]', $forums_list, $prefix_forums);
			$new_prefix -> closeTable();
			$new_prefix -> drawButton( $language -> getString( 'acp_forums_subsection_topics_prefixes_prefix_change'));
			$new_prefix -> closeForm();
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_edit'), $new_prefix -> display()));
	
		}else{
			
			//not found. Draw message and jump to manager
			
			parent::draw( $style ->drawErrorBlock( $language -> getString( 'acp_forums_subsection_topics_prefixes_edit'), $language -> getString( 'acp_forums_subsection_topics_prefixes_notfound')));
			
			$this -> act_topics_prefixes();
			
		}
			
	}
	
	function act_bbtags_manage(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'bbtags_manage');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_bbtags'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_bbtags_manage'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_forums_subsection_bbtags_manage'));
		
		/**
		 * begin code
		 */
		
		$bb_list = new form();
		$bb_list -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'bbtags_new')));
		$bb_list -> openOpTable();
		
		$bb_list -> addToContent( '<tr>
			<th>'.$language -> getString('acp_forums_subsection_bbtags_manage_name').'</th>
			<th>'.$language -> getString('actions').'</th>
		</tr>');
		
		/**
		 * select codes
		 */
		
		$codes_query = $mysql -> query( "SELECT * FROM bbtags ORDER BY tag_name");
		
		while ( $codes_result = mysql_fetch_array( $codes_query, MYSQL_ASSOC)) {
			
			//clear result
			$codes_result =  $mysql -> clear( $codes_result);
			
			/**
			 * add row
			 */
			
			$bb_list -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%">'.$codes_result['tag_name'].'</td>
				<td class="opt_row3" style="text-align: center" nowrap="nowrap">
				<a href="'.parent::adminLink( parent::getId(), array( 'act' => 'bbtags_edit', 'tag' => $codes_result['tag_id'])).'">'.$style -> drawImage( 'edit', $language -> getString( 'edit')).'</a>
				<a href="'.parent::adminLink( parent::getId(), array( 'act' => 'bbtags_delete', 'tag' => $codes_result['tag_id'])).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a>
				</td>
			</tr>');
		
		}
		
		/**
		 * close table
		 */
		
		$bb_list -> closeTable();
		$bb_list -> drawButton( $language -> getString( 'acp_forums_subsection_bbtags_new'));
		$bb_list -> closeForm();
				
		/**
		 * display it
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_bbtags_manage'), $bb_list -> display()));
		
	}
	
	function act_bbtags_new(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'bbtags_new');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_bbtags'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_bbtags_new'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_forums_subsection_bbtags_new'));
		
		/**
		 * set values
		 */
		
		$code_name = '';
		$code_info = '';
		$code_content = '';
		$code_option = false;
		$code_replace = '';
		$code_draw = false;
		
		/**
		 * retake values
		 */
		
		if ( $_GET['act'] == 'bbtags_add') {
			
			$code_name = stripslashes( $strings -> inputClear( $_POST['code_name'], false));
			$code_info = stripslashes( $strings -> inputClear( $_POST['code_info'], false));
			$code_content = stripslashes( $strings -> inputClear( $_POST['code_content'], false));
			$code_option = $_POST['code_option'];
			$code_replace = stripslashes( $strings -> inputClear( $_POST['code_replace'], false));
			$code_draw = $_POST['code_draw'];
			
			settype( $code_option, 'bool');
			settype( $code_draw, 'bool');
			
		}
		
		/**
		 * draw form
		 */
		
		$new_tag = new form();
		$new_tag -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'bbtags_add')));
		$new_tag -> openOpTable();
		
		$new_tag -> drawTextInput( $language -> getString( 'acp_forums_subsection_bbtags_new_name'), 'code_name', $code_name);
		$new_tag -> drawTextBox( $language -> getString( 'acp_forums_subsection_bbtags_new_info'), 'code_info', $code_info);
		$new_tag -> drawTextInput( $language -> getString( 'acp_forums_subsection_bbtags_new_content'), 'code_content', $code_content, $language -> getString( 'acp_forums_subsection_bbtags_new_content_help'));
		$new_tag -> drawYesNo( $language -> getString( 'acp_forums_subsection_bbtags_new_option'), 'code_option', $code_option, $language -> getString( 'acp_forums_subsection_bbtags_new_option_help'));
		$new_tag -> drawTextBox( $language -> getString( 'acp_forums_subsection_bbtags_new_html'), 'code_replace', $code_replace, $language -> getString( 'acp_forums_subsection_bbtags_new_html_help'));
		$new_tag -> drawYesNo( $language -> getString( 'acp_forums_subsection_bbtags_new_draw'), 'code_draw', $code_draw);
		
		$new_tag -> closeTable();
		$new_tag -> drawButton( $language -> getString( 'acp_forums_subsection_bbtags_new_add'));
		$new_tag -> closeForm();
		
		/**
		 * draw block
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_bbtags_new'), $new_tag -> display()));
		
	}
	
	function act_bbtags_edit(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * get tag to edit
		 */
		
		$tag_to_edit = $_GET['tag'];
		
		settype( $tag_to_edit, 'integer');
		
		/**
		 * select tag from db
		 */
		
		$tag_query = $mysql -> query( "SELECT * FROM bbtags WHERE `tag_id` = '$tag_to_edit'");
		
		if ( $tag_result = mysql_fetch_array( $tag_query, MYSQL_ASSOC)){
			
			//clear
			$tag_result = $mysql -> clear( $tag_result);
			
			/**
			 * add breadcrumbs
			 */
			
			$path_link = array( 'act' => 'bbtags_edit', 'tag' => $tag_to_edit);
			
			$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_bbtags'), parent::adminLink( parent::getId(), $path_link));		
			$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_bbtags_edit'), parent::adminLink( parent::getId(), $path_link));
			
			/**
			 * set page title
			 */
			
			$output -> setTitle( $language -> getString( 'acp_forums_subsection_bbtags_edit'));
			
			/**
			 * set values
			 */
			
			$code_name = $tag_result['tag_name'];
			$code_info = $tag_result['tag_info'];
			$code_content = $tag_result['tag_tag'];
			$code_option = $tag_result['tag_option'];
			$code_replace = $tag_result['tag_replace'];
			$code_draw = $tag_result['tag_draw'];
			
			/**
			 * retake values
			 */
			
			if ( $_GET['act'] == 'bbtags_change') {
				
				$code_name = stripslashes( $strings -> inputClear( $_POST['code_name'], false));
				$code_info = stripslashes( $strings -> inputClear( $_POST['code_info'], false));
				$code_content = stripslashes( $strings -> inputClear( $_POST['code_content'], false));
				$code_option = $_POST['code_option'];
				$code_replace = stripslashes( $strings -> inputClear( $_POST['code_replace'], false));
				$code_draw = $_POST['code_draw'];
				
				settype( $code_option, 'bool');
				settype( $code_draw, 'bool');
				
			}
			
			/**
			 * draw form
			 */
			
			$new_tag = new form();
			$new_tag -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'bbtags_change', 'tag' => $tag_to_edit)));
			$new_tag -> openOpTable();
			
			$new_tag -> drawTextInput( $language -> getString( 'acp_forums_subsection_bbtags_new_name'), 'code_name', $code_name);
			$new_tag -> drawTextBox( $language -> getString( 'acp_forums_subsection_bbtags_new_info'), 'code_info', $code_info);
			$new_tag -> drawTextInput( $language -> getString( 'acp_forums_subsection_bbtags_new_content'), 'code_content', $code_content, $language -> getString( 'acp_forums_subsection_bbtags_new_content_help'));
			$new_tag -> drawYesNo( $language -> getString( 'acp_forums_subsection_bbtags_new_option'), 'code_option', $code_option, $language -> getString( 'acp_forums_subsection_bbtags_new_option_help'));
			$new_tag -> drawTextBox( $language -> getString( 'acp_forums_subsection_bbtags_new_html'), 'code_replace', $code_replace, $language -> getString( 'acp_forums_subsection_bbtags_new_html_help'));
			$new_tag -> drawYesNo( $language -> getString( 'acp_forums_subsection_bbtags_new_draw'), 'code_draw', $code_draw);
			
			$new_tag -> closeTable();
			$new_tag -> drawButton( $language -> getString( 'acp_forums_subsection_bbtags_edit_button'));
			$new_tag -> closeForm();
			
			/**
			 * draw block
			 */
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_bbtags_edit'), $new_tag -> display()));
			
			
		}else{
			
			/**
			 * tag not found
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_forums_subsection_bbtags_edit'), $language -> getString( 'acp_forums_subsection_bbtags_edit_notfound')));
			
			$this -> act_bbtags_manage();
			
		}
		
	}
		
	function act_atts_types(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'atts_types');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_attachments'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_atts_types'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_forums_subsection_atts_types'));
		
		/**
		 * draw table with attachments
		 */
		
		$attachments_types = new form();
		$attachments_types -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'new_att_type')));
		$attachments_types -> openOpTable();
		$attachments_types -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_forums_subsection_atts_types_list_extension').'</th>
			<th>'.$language -> getString( 'acp_forums_subsection_atts_types_list_mime').'</th>
			<th>'.$language -> getString( 'actions').'</th>
		</tr>');
		
		/**
		 * select types
		 */
		
		$types_query = $mysql -> query( "SELECT * FROM attachments_types ORDER BY `attachments_type_extension`");
		
		while ( $types_result = mysql_fetch_array( $types_query, MYSQL_ASSOC)){
			
			//clear result
			$types_result = $mysql -> clear( $types_result);
			
			/**
			 * add row
			 */
			
			$attachments_types -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%"><img src="'.ROOT_PATH.'images/attachments_types/'.$types_result['attachments_type_image'].'" /> '.$types_result['attachments_type_extension'].'</td>
				<td class="opt_row2" style="text-align: center" NOWRAP="nowrap">'.(isset($types_result['attachments_type_mime'][0]) ? $types_result['attachments_type_mime'] : '<i>' . $language -> getString('acp_forums_subsection_atts_types_list_mime_none') . '</i>').'</td>
				<td class="opt_row3" style="text-align: center" NOWRAP="nowrap">
				<a href="'.parent::adminLink( parent::getId(), array('act' => 'edit_att_type', 'type' => $types_result['attachments_type_id'])).'">'.$style -> drawImage( 'edit', $language -> getString( 'edit')).'</a>
				<a href="'.parent::adminLink( parent::getId(), array('act' => 'del_att_type', 'type' => $types_result['attachments_type_id'])).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a>
				</td>
			</tr>');
			
		}
		
		/**
		 * draw rest
		 */
		
		$attachments_types -> closeTable();
		$attachments_types -> drawButton( $language -> getString( 'acp_forums_subsection_atts_types_list_new_type'));
		$attachments_types -> closeForm();
		
		/**
		 * draw list
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_atts_types'), $attachments_types -> display()));
		
	}
	
	function act_new_att_type(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'atts_types');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_attachments'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_atts_types'), parent::adminLink( parent::getId(), $path_link));
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_atts_types_new_type'), parent::adminLink( parent::getId(), array( 'act' => 'new_att_type')));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_forums_subsection_atts_types_new_type'));
		
		/**
		 * set basic empty vars
		 */
		
		$attach_type_extension = '';
		$attach_type_mime = '';
		$attach_type_image = 'unknown.png';
		
		if ( $_GET['act'] == 'add_att_type'){
			
			$attach_type_extension = stripslashes( $strings -> inputClear( $_POST['att_ext'], false));
			$attach_type_mime = stripslashes( $strings -> inputClear( $_POST['att_mime'], false));
			$attach_type_image = stripslashes( $strings -> inputClear( $_POST['att_image'], false));
			
		}
		
		/**
		 * begin drawing form
		 */
		
		$new_att_type = new form();
		$new_att_type -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'add_att_type')));
		$new_att_type -> openOpTable();
		
		$new_att_type -> drawTextInput( $language -> getString( 'acp_forums_subsection_atts_types_new_type_extension'), 'att_ext', $attach_type_extension);
		$new_att_type -> drawTextInput( $language -> getString( 'acp_forums_subsection_atts_types_new_type_mime'), 'att_mime', $attach_type_mime);
		
		/**
		 * build up an list of images
		 */
		
		$types_images = glob( ROOT_PATH."images/attachments_types/*.{gif,jpg,png}", GLOB_BRACE);
		
		foreach ( $types_images as $image_path){
			
			$image_name = substr( $image_path, strrpos( $image_path, "/") + 1);
			
			$images_files[$image_name] = $image_name;
			
		}
		
		$new_att_type -> drawList( $language -> getString( 'acp_forums_subsection_atts_types_new_type_image'), 'att_image', $images_files, $attach_type_image);
		
		$new_att_type -> closeTable();
		$new_att_type -> drawButton( $language -> getString( 'acp_forums_subsection_atts_types_new_type_save'));
		$new_att_type -> closeForm();
		
		/**
		 * display form
		 */
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_atts_types_new_type'), $new_att_type -> display()));
		
	}
	
	function act_edit_att_type(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * get type to edit
		 */
		
		$type_to_edit = $_GET['type'];
		settype( $type_to_edit, 'integer');
		
		/**
		 * do query
		 */
		
		$type_query = $mysql -> query( "SELECT * FROM `attachments_types` WHERE `attachments_type_id` = '$type_to_edit'");
					
		if ( $type_result = mysql_fetch_array( $type_query, MYSQL_ASSOC)){
			
			//clear result
			$type_result = $mysql -> clear( $type_result);
			
			/**
			 * add breadcrumbs
			 */
			
			$path_link = array( 'act' => 'atts_types');
			
			$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_attachments'), parent::adminLink( parent::getId(), $path_link));		
			$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_atts_types'), parent::adminLink( parent::getId(), $path_link));
			$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_atts_types_edit_type'), parent::adminLink( parent::getId(), array( 'act' => 'edit_att_type')));
			
			/**
			 * set page title
			 */
			
			$output -> setTitle( $language -> getString( 'acp_forums_subsection_atts_types_edit_type'));
			
			/**
			 * set basic empty vars
			 */
			
			$attach_type_extension = $type_result['attachments_type_extension'];
			$attach_type_mime = $type_result['attachments_type_mime'];
			$attach_type_image = $type_result['attachments_type_image'];
			
			if ( $_GET['act'] == 'change_att_type'){
				
				$attach_type_extension = stripslashes( $strings -> inputClear( $_POST['att_ext'], false));
				$attach_type_mime = stripslashes( $strings -> inputClear( $_POST['att_mime'], false));
				$attach_type_image = stripslashes( $strings -> inputClear( $_POST['att_image'], false));
				
			}
			
			/**
			 * begin drawing form
			 */
			
			$new_att_type = new form();
			$new_att_type -> openForm( parent::adminLink( parent::getId(), array( 'act' => 'change_att_type', 'type' => $type_to_edit)));
			$new_att_type -> openOpTable();
			
			$new_att_type -> drawTextInput( $language -> getString( 'acp_forums_subsection_atts_types_new_type_extension'), 'att_ext', $attach_type_extension);
			$new_att_type -> drawTextInput( $language -> getString( 'acp_forums_subsection_atts_types_new_type_mime'), 'att_mime', $attach_type_mime);
			
			/**
			 * build up an list of images
			 */
			
			$types_images = glob( ROOT_PATH."images/attachments_types/*.{gif,jpg,png}", GLOB_BRACE);
			
			foreach ( $types_images as $image_path){
				
				$image_name = substr( $image_path, strrpos( $image_path, "/") + 1);
				
				$images_files[$image_name] = $image_name;
				
			}
			
			$new_att_type -> drawList( $language -> getString( 'acp_forums_subsection_atts_types_new_type_image'), 'att_image', $images_files, $attach_type_image);
			
			$new_att_type -> closeTable();
			$new_att_type -> drawButton( $language -> getString( 'acp_forums_subsection_atts_types_edit_type_button'));
			$new_att_type -> closeForm();
			
			/**
			 * display form
			 */
			
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_forums_subsection_atts_types_edit_type'), $new_att_type -> display()));
		
		}else{
			
			parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_forums_subsection_atts_types_edit_type'), $language -> getString( 'acp_forums_subsection_atts_types_edit_type_notfound')));
						
			$this -> act_atts_types();		
			
		}
	}
	
	function act_orpchans(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'orpchans');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_section_attachments'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_forums_subsection_orpchans'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_forums_subsection_orpchans'));
		
		/**
		 * in this task we will not play with any forms, but simply select orphaned attachments, and delete them
		 */
		
		$deleted_orpchans = 0;
		$orpchans_to_kill = array();
		
		$orpchans_query = $mysql -> query( "SELECT a.* FROM attachments a LEFT OUTER JOIN posts p ON a.attachment_post = p.post_id WHERE a.attachment_post > '0' AND p.post_id = ''");
		
		while ( $orpchans_result = mysql_fetch_array( $orpchans_query, MYSQL_ASSOC)){
			
			//clear result
			$orpchans_result = $mysql -> clear( $orpchans_result);
			
			/**
			 * if file exists, delete it
			 */
			
			if ( file_exists( ROOT_PATH.'uploads/'.$orpchans_result['attachment_file'])){
				
				unlink( ROOT_PATH.'uploads/'.$orpchans_result['attachment_file']);
				
			}
			
			/**
			 * add attachment to list
			 */
			
			$deleted_orpchans++;
			$orpchans_to_kill[] = $orpchans_result['attachment_id'];
			
		}
		
		/**
		 * begin drawing form
		 */
		
		if ($deleted_orpchans > 0){
			
			/**
			 * delete
			 */
			
			$mysql -> delete( 'attachments', "`attachment_id` IN (".join( ",", $orpchans_to_kill).")");
			
			/**
			 * orpchans found
			 */
			
			$language -> setKey( 'deleted_orpchans_num', $deleted_orpchans);
			
			parent::draw( $style -> drawBlock( $language -> getString( 'acp_forums_subsection_orpchans'), $language -> getString( 'acp_forums_subsection_orpchans_deleted')));
		
		}else{
			
			/**
			 * no orpchans found
			 */
			
			parent::draw( $style -> drawBlock( $language -> getString( 'acp_forums_subsection_orpchans'), $language -> getString( 'acp_forums_subsection_orpchans_none')));
		
		}
	}
		
}
	
?>