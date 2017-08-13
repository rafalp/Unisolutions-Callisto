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
|	Acp Styles and Langs Page
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class acp_section_look extends acp_section{
	
	function __construct(){
				
		/**
		 * include global classes pointers
		 */
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * begin
		 */
		
		$correct_acts = array( 'styles', 'edit_style', 'delete_style', 'change_style', 'kill_style', 'emoticons', 'langs', 'new_language', 'save_language', 'edit_language', 'change_language', 'delete_language', 'kill_language');
		
		if ( !isset( $_GET['act']) || !in_array( $_GET['act'], $correct_acts)){
			$current_act = 	$correct_acts[0];
		}else{
			$current_act = $_GET['act'];
		}
		
		/**
		 * now array containing subsections
		 */
		
		$subsections_list['look'] = 'look';
		$subsections_list['langs'] = 'langs';
		
		/**
		 * and subsections list
		 */
			
		$subsections_elements_list['styles'] = 'look';
		$subsections_elements_list['emoticons'] = 'look';
		
		$subsections_elements_list['langs'] = 'langs';
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
			
			case 'styles':
				
				$show_group = $this -> act_styles();
				
			break;
			
			case 'edit_style':
				
				$show_group = $this -> act_edit_style();
				
			break;
			
			case 'delete_style':
				
				$show_group = $this -> act_delete_style();
				
			break;
			
			case 'change_style':
				
				if ( $session -> checkForm()){
					
					if ( isset( $_GET['style']) && !empty( $_GET['style'])){
			
						/**
						 * style to edit specified
						 * select it from mysql
						 */
						
						$style_to_edit = $_GET['style'];
						
						settype( $style_to_edit, 'integer');
						
						$style_query = $mysql -> query( "SELECT * FROM styles WHERE `style_id` = '$style_to_edit'");
						
						if ( $style_result = mysql_fetch_array( $style_query, MYSQL_ASSOC)){
							
							$style_name = $strings -> inputClear( $_POST['style_name'], false);
							$style_default = $strings -> inputClear( $_POST['style_default'], false);
							$style_author = $strings -> inputClear( $_POST['style_author'], false);
							$style_www = $strings -> inputClear( $_POST['style_author_www'], false);
							
							settype( $style_default, 'bool');
							
							/**
							 * error checking
							 */
							
							if ( empty( $_POST['style_name'])){
								
								/**
								 * draw error
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_edit'), $language -> getString( 'acp_look_subsection_styles_edit_noname')));
															
								$this -> act_edit_style();
								
							}else{
								
								$update_style_query['style_name'] = $style_name;
								$update_style_query['style_author'] = $style_author;
								$update_style_query['style_www'] = $style_www;
								
								$mysql -> update( $update_style_query, 'styles', "`style_id` = '$style_to_edit'");
								
								$logs_keys = array( 'style_edit_id' => $style_to_edit);
								
								$logs -> addAdminLog( $language -> getString( 'acp_look_subsection_styles_edit_log'), $logs_keys);
								
								parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_look_subsection_styles_edit'), $language -> getString( 'acp_look_subsection_styles_edit_done')));
								
								/**
								 * check if we have to make it default
								 */
								
								if ( $style_default && $settings['default_style'] != $style_to_edit){
									
									$update_settings['setting_value'] = $style_to_edit;
									
									$mysql -> update( $update_settings, 'settings', "`setting_setting` = 'default_style'");
									
									$cache -> flushCache( 'system_settings');
									
									$settings['default_style'] = $style_to_edit;
									
								}
								
								$cache -> flushCache( 'style_'.$style_to_edit);
								$cache -> flushCache( 'styles');
									
								/**
								 * draw manager
								 */
								
								$this -> act_styles();
								
							}
							
						}else{
					
							/**
							 * style to edit no found, draw error
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_edit'), $language -> getString( 'acp_look_subsection_styles_edit_notfound')));
							
							$this -> act_styles();
							
						}
				
					
					}else{
						
						/**
						 * style to edit no found, draw error
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_edit'), $language -> getString( 'acp_look_subsection_styles_edit_notarget')));
						
						$this -> act_styles();
						
					}
				
				}else{
										
					$show_group = $this -> act_styles();
				
				}
				
			break;
			
			case 'kill_style':
				
				if ( $session -> checkForm()){
					
					if ( isset( $_GET['style']) && !empty( $_GET['style'])){
			
						/**
						 * style to edit specified
						 * select it from mysql
						 */
						
						$style_to_delete = $_GET['style'];
						
						settype( $style_to_delete, 'integer');
							
						$style_query = $mysql -> query( "SELECT * FROM styles WHERE `style_id` = '$style_to_delete'");
						
						if ( $style_result = mysql_fetch_array( $style_query, MYSQL_ASSOC)){
							
							if ( $style_to_delete != $settings['default_style']){
						
								if ( isset( $_POST['style_replacement']) && !empty($_POST['style_replacement'])){
								
									$style_to_replace = $_POST['style_replacement'];
									settype( $style_to_replace, 'integer');
										
									/**
									 * check if replacement exists
									 */
																	
									$style_query = $mysql -> query( "SELECT * FROM styles WHERE `style_id` = '$style_to_replace' && `style_id` <> '$style_to_delete'");
									if ( $style_result = mysql_fetch_array( $style_query, MYSQL_ASSOC)){
						
										/**
										 * replace existing styles with new one
										 */
										
										$update_users_usement_styles['user_style'] = $style_to_replace;
										
										$mysql -> update( $update_users_usement_styles, 'users', "`user_style` = '$style_to_delete'");
										
										/**
										 * delete style
										 */
										
										$mysql -> delete( 'styles', "`style_id` = '$style_to_delete'");
										
										/**
										 * flish cache
										 */
										
										$cache -> flushCache( 'style_'.$style_to_delete.'_data');
										$cache -> flushCache( 'styles');
										
										/**
										 * add log
										 */
										
										$logs_keys = array( 'style_delete_id' => $style_to_delete);
										$logs -> addAdminLog( $language -> getString( 'acp_look_subsection_styles_delete_log'), $logs_keys);
										
										/**
										 * draw message
										 */
										
										parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_look_subsection_styles_delete'), $language -> getString( 'acp_look_subsection_styles_delete_done')));
																			
										/**
										 * draw manager
										 */
										
										$this -> act_styles();
										
									}else{
										
										/**
										 * not found replacement
										 */
									
										parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_delete'), $language -> getString( 'acp_look_subsection_styles_delete_replace_notfound')));
									
										$this -> act_delete_style();
											
										}
										
								}else{
									
									/**
									 * not specified replacement
									 */
								
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_delete'), $language -> getString( 'acp_look_subsection_styles_delete_replace_notarget')));
								
									$this -> act_delete_style();
								
								}
								
							}else{
								
								/**
								 * cant delete default style
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_delete'), $language -> getString( 'acp_look_subsection_styles_delete_default')));
								
								$this -> act_styles();
							
							}
							
						}else{
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_delete'), $language -> getString( 'acp_look_subsection_styles_delete_notfound')));
							
							$this -> act_styles();
							
						}
												
					}else{
						
						/**
						 * style to edit no found, draw error
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_delete'), $language -> getString( 'acp_look_subsection_styles_delete_notarget')));
						
						$this -> act_styles();
						
					}
				
				}else{
										
					$show_group = $this -> act_styles();
				
				}
				
			break;
			
			case 'emoticons':
				
				/**
				 * emoticons manager
				 */
				
				$this -> act_emoticons();
				
			break;
			
			case 'langs':
				
				/**
				 * langs manager
				 */
				
				$this -> act_languages_manager();
				
			break;
			
			case 'new_language':
				
				/**
				 * new language
				 */
				
				$this -> act_new_lang();
				
			break;
			
			case 'save_language':
				
				if ( $session -> checkForm()){
					
					/**
					 * we will add new language
					 */
					
					$new_lang_id = $strings -> inputClear( $_POST[ 'lang_id'], false);
					$new_lang_name = $strings -> inputClear( $_POST[ 'lang_name'], false);
					
					if ( empty( $new_lang_id)){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_new'), $language -> getString( 'acp_look_section_langs_new_id_empty')));
						
						$this -> act_new_lang();
						
					}else if ( empty( $new_lang_name)){
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_new'), $language -> getString( 'acp_look_section_langs_new_name_empty')));
						
						$this -> act_new_lang();
						
					}else{
						
						/**
						 * first error check done. check id 
						 */
						
						$id_check_query = $mysql -> query( "SELECT * FROM languages WHERE `lang_id` = '$new_lang_id'");
						
						if ( $lang_result = mysql_fetch_array( $id_check_query, MYSQL_ASSOC)){
							
							/**
							 * id is already taken
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_new'), $language -> getString( 'acp_look_section_langs_new_id_used')));
						
							$this -> act_new_lang();
						
						}else{
							
							/**
							 * everything is okey
							 */
							
							$new_lang_sql['lang_id'] = $new_lang_id;
							$new_lang_sql['lang_name'] = $new_lang_name;
							
							$mysql -> insert( $new_lang_sql, 'languages');
							$cache -> flushCache( 'languages');
							
							/**
							 * add log
							 */
							
							$logs_keys = array( 'new_lang_name' => $new_lang_name);
							$logs -> addAdminLog( $language -> getString( 'acp_look_section_langs_new_saved_log'), $logs_keys);
							
							/**
							 * draw message
							 */
							
							parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_look_section_langs_new'), $language -> getString( 'acp_look_section_langs_new_saved')));
						
							$this -> act_languages_manager();
						
						}
						
					}
					
				}else{
					
					$this -> act_languages_manager();
				
				}
				
			break;
			
			case 'edit_language':
				
				/**
				 * edit language
				 */
				
				$this -> act_edit_lang();
				
			break;
			
			case 'change_language':
			
				/**
				 * change language
				 */
				
				if ( $session -> checkForm()){
						
					if ( isset( $_GET['lang']) && !empty( $_GET['lang'])) {
				
						$lang_to_edit = uniSlashes(trim($_GET['lang']));
						
						$lang_query = $mysql -> query( "SELECT * FROM languages WHERE `lang_id` = '$lang_to_edit'");
						
						if ( $lang_result = mysql_fetch_array( $lang_query, MYSQL_ASSOC)){
							
							$lang_name = $strings -> inputClear( $_POST['lang_name'], false);
							
							if ( !empty( $lang_name)){
								
								$update_lang_sql['lang_name'] = $lang_name;
								
								$mysql -> update( $update_lang_sql, 'languages', "`lang_id` = '$lang_to_edit'");
								
								/**
								 * clear cache
								 */
								
								$cache -> flushCache( "lang_".$lang_to_edit);
								$cache -> flushCache( 'languages');
								
								/**
								 * check if we have to make id default
								 */
								
								if ( $lang_to_edit != $settings[ 'default_language'] && $_POST['lang_default'] == true){
									
									$settings_sql['setting_value'] = $lang_to_edit;
									
									$mysql -> update( $update_lang_sql, 'settings', "`setting_setting` = 'default_language'");
									
									$cache -> flushCache( "system_settings");
									
								}
								
								/**
								 * log
								 */
								
								$log_keys = array( 'lang_edit_id' => $lang_to_edit);
								
								$logs -> addAdminLog( $language -> getString( 'acp_look_section_langs_edit_log'), $log_keys);
								
								/**
								 * message
								 */
								
								parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_look_section_langs_edit'), $language -> getString( 'acp_look_section_langs_edit_done')));
															
								/**
								 * draw manager
								 */
								
								$this -> act_languages_manager();
								
							}else{
																
								/**
								 * lang name empty
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_edit'), $language -> getString( 'acp_look_section_langs_new_name_empty')));
											
								$this -> act_edit_lang();
								
							}
							
						}else{
							
							/**
							 * lang not found
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_edit'), $language -> getString( 'acp_look_section_langs_edit_notfound')));
										
							$this -> act_languages_manager();
										
						}
						
					}else{
						
						/**
						 * empty lang to edit
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_edit'), $language -> getString( 'acp_look_section_langs_edit_empty')));
						
						$this -> act_languages_manager();
						
					}
				
				}
					
			break;
			
			case 'delete_language':
				
				/**
				 * delete language
				 */
				
				$this -> act_delete_lang();
				
			break;
			
			case 'kill_language':
				
				if ( $session -> checkForm()){
					
					if ( isset( $_GET['lang']) && !empty( $_GET['lang'])) {
			
						$lang_to_edit = uniSlashes(trim($_GET['lang']));
						
						$lang_query = $mysql -> query( "SELECT * FROM languages WHERE `lang_id` = '$lang_to_edit'");
						
						if ( $lang_result = mysql_fetch_array( $lang_query, MYSQL_ASSOC)){
							
							$lang_result = $mysql -> clear( $lang_result);
							
							/**
							 * check if default
							 */
							
							if ( $lang_to_edit == $settings[ 'default_language']){
								
								/**
								 * cant delete default
								 */
								
								parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_delete'), $language -> getString( 'acp_look_section_langs_delete_cant_default')));
											
								$this -> act_languages_manager();
								
							}else{
								
								if ( isset( $_POST['lang_replace']) && !empty( $_POST['lang_replace'])){
									
									$lang_to_replace = uniSlashes(trim($_POST['lang_replace']));
									
									$lang_query = $mysql -> query( "SELECT * FROM languages WHERE `lang_id` = '$lang_to_replace'");
						
									if ( $lang_result = mysql_fetch_array( $lang_query, MYSQL_ASSOC)){
										
										/**
										 * lang found, delete it
										 */
										
										$mysql -> delete( 'languages', "`lang_id` = '$lang_to_edit'");
										$cache -> flushCache( 'languages');
										
										/**
										 * update langs
										 */
										
										$update_lang_sql['user_lang'] = $lang_to_replace;
										$mysql -> update( $update_lang_sql, 'users', "`user_lang` = '$lang_to_edit'");
										
										/**
										 * clear cache
										 */
										
										$cache -> flushCache( 'lang_'.$lang_to_edit);
										$cache -> flushCache( 'system_settings');
										
										/**
										 * add log
										 */
										
										$logs_keys = array( 'lang_del_id' => $lang_to_edit);
										$logs -> addAdminLog( $language -> getString( 'acp_look_section_langs_delete_log'), $logs_keys);

										/**
										 * message and manager now
										 */
										
										parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_look_section_langs_delete'), $language -> getString( 'acp_look_section_langs_delete_done')));
													
										$this -> act_languages_manager();
										
									}else{
										
										parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_delete'), $language -> getString( 'acp_look_section_langs_delete_replace_notfound')));
													
										$this -> act_delete_lang();
										
									}
									
								}else{
									
									
									parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_delete'), $language -> getString( 'acp_look_section_langs_delete_none_replace')));
												
									$this -> act_delete_lang();
									
								}
											
							}
							
						}else{
							
							/**
							 * lang not found
							 */
							
							parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_delete'), $language -> getString( 'acp_look_section_langs_delete_notfound')));
										
							$this -> act_languages_manager();
										
						}
						
					}else{
						
						/**
						 * empty lang to edit
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_delete'), $language -> getString( 'acp_look_section_langs_delete_empty')));
						
						$this -> act_languages_manager();
						
					}	
					
				}
				
			break;
			
		}
		
	}	
		
	function act_styles(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'styles');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_look_section_look'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_look_subsection_styles'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_look_subsection_styles'));
		
		/**
		 * check if we are installing new style
		 */
		
		if ( $_GET['do'] == 'new_style'){
			
			if ( isset( $_GET['style']) && !empty( $_GET['style'])){
				
				/**
				 * check if style exists
				 */
				
				$style_path = trim(uniSlashes( $_GET['style']));
				
				if ( file_exists( ROOT_PATH.'styles/'.$style_path.'/info.xml')){
					
					/**
					 * check if style is installed
					 */
					
					$styles_query = $mysql -> query( "SELECT * FROM styles WHERE `style_path` = '$style_path'");
		
					if ( $style_result = mysql_fetch_array( $styles_query, MYSQL_ASSOC)){
						
						/**
						 * style already exists
						 */
						
						parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_new'), $language -> getString( 'acp_look_subsection_styles_install_exists')));
										
					}else{
						
						/**
						 * open style info, and install it
						 */
						
						$style_xml_file = file_get_contents( ROOT_PATH.'styles/'.$style_path.'/info.xml');
						$style_xml = new SimpleXMLElement( $style_xml_file);
										
						$new_style_mysql['style_name'] = $strings -> inputClear( $style_xml -> details -> name, false);
						$new_style_mysql['style_path'] = $style_path;
						$new_style_mysql['style_author'] = $strings -> inputClear( $style_xml -> details -> author, false);
						$new_style_mysql['style_www'] = $strings -> inputClear( $style_xml -> details -> www, false);
						
						/**
						 * insert it
						 */
						
						$mysql -> insert( $new_style_mysql, 'styles');
						
						$cache -> flushCache( 'styles');
						
						/**
						 * log now
						 */
						
						$log_keys = array( 'new_style_name' => $strings -> inputClear( $style_xml -> details -> name, false));
						$logs -> addAdminLog( $language -> getString( 'acp_look_subsection_styles_install_log'), $log_keys);
						
						/**
						 * and message
						 */
						
						parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_look_subsection_styles_new'), $language -> getString( 'acp_look_subsection_styles_install_done')));
						
					}
					
				}else{
					
					/**
					 * style not found
					 */
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_new'), $language -> getString( 'acp_look_subsection_styles_install_notfound')));
				
				}
				
			}else{
			
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_new'), $language -> getString( 'acp_look_subsection_styles_install_empty')));
					
			}
			
		}
		
		/**
		 * draw list of styles
		 */
		
		$actual_styles_form = new form();
		$actual_styles_form -> openOpTable();
		$actual_styles_form -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_look_subsection_styles_installed_style_name').'</th>
			<th>'.$language -> getString( 'acp_look_subsection_styles_installed_style_default').'</th>
			<th>'.$language -> getString( 'acp_look_subsection_styles_installed_style_users').'</th>
			<th>'.$language -> getString( 'actions').'</th>
		</tr>');
		
		$styles_query = $mysql -> query( "SELECT * FROM styles");
		while ( $styles_result = mysql_fetch_array( $styles_query, MYSQL_ASSOC)){
			
			//clear
			$styles_result = $mysql -> clear( $styles_result);
			
			$found_paths[] = $styles_result['style_path'];
			
			/**
			 * style authors
			 */
			
			$style_authors = '';
			
			if ( !empty( $styles_result['style_author']))
				$style_authors = $styles_result['style_author'];
				
			if ( !empty( $styles_result['style_www']))
				$style_authors = '<a href="'.$styles_result['style_www'].'">'.$style_authors.'</a>';
				
			if ( !empty( $style_authors))
				$style_authors = '<br />'.$style_authors;
			
			/**
			 * style_default
			 */
			
			$style_is_default = false;
			
			if ( $settings['default_style'] == $styles_result['style_id'])
				$style_is_default = true;
			
			/**
			 * links
			 */
					
			$edit_style_link = array( 'act' => 'edit_style', 'style' => $styles_result['style_id']);
			$delete_style_link = array( 'act' => 'delete_style', 'style' => $styles_result['style_id']);
			
			/**
			 * insert row
			 */
						
			$actual_styles_form -> addToContent( '<tr>
				<td class="opt_row1" style="width: 100%"><b>'.$styles_result['style_name'].'</b>'.$style_authors.'</td>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$style -> drawThick( $style_is_default, true).'</td>
				<td class="opt_row1" style="text-align: center" NOWRAP>'.$styles_result['style_users'].'</td>
				<td class="opt_row3" style="text-align: center" NOWRAP>
					<a href="'.parent::adminLink( parent::getId(), $edit_style_link).'">'.$style -> drawImage( 'edit', $language -> getString( 'edit')).'</a>
					<a href="'.parent::adminLink( parent::getId(), $delete_style_link).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a>
				</td>
			</tr>');
			
		}
		
		$actual_styles_form -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language ->getString( 'acp_look_subsection_styles_installed'), $actual_styles_form -> display()));
		
		/**
		 * list of un-instaled styles
		 */
		
		$styles_avaiable_list = glob( ROOT_PATH.'styles/*/info.xml');
		
		foreach ( $styles_avaiable_list as $style_install_file){
			
			/**
			 * check if path is to instaled one
			 */
			
			$current_path = str_ireplace(  ROOT_PATH.'styles/', '', $style_install_file);
			$current_path = str_ireplace(  '/info.xml', '', $current_path);
			
			if ( !in_array( $current_path, $found_paths)){
				
				$styles_found = true;

				/**
				 * load style data
				 */
				
				$style_xml_file = file_get_contents( $style_install_file);
				$style_xml = new SimpleXMLElement( $style_xml_file);
				
				$style_info = '';
				
				if ( !empty( $style_xml -> details -> author))
					$style_info = $style_xml -> details -> author;
					
				if ( !empty( $style_xml -> details -> www))
					$style_info = '<a href="'.$style_xml -> details -> www.'">'.$style_info.'</a>';
				
				if ( !empty( $style_info))	
					$style_info = '<br />'.$style_info;
				
				$install_style_link = array( 'act' => 'styles', 'do' => 'new_style', 'style' => $current_path);
					
				$styles_to_install_list_content .= '<tr>
					<td class="opt_row1" style="width: 100%"><b>'.$style_xml -> details -> name.'</b>'.$style_info.'</td>
					<td class="opt_row3" style="text-align: center" NOWRAP><a href="'.parent::adminLink( parent::getId(), $install_style_link).'">'.$language -> getString( 'acp_look_subsection_styles_installed_style_install_do').'</a></td>
				</tr>'; 
				
			}
			
		}
		
		if ( isset( $styles_found)){
			
			$install_styles_form = new form();
			$install_styles_form -> openOpTable();
			$install_styles_form -> addToContent( '<tr>
				<th>'.$language -> getString( 'acp_look_subsection_styles_installed_style_name').'</th>
				<th>'.$language -> getString( 'acp_look_subsection_styles_installed_style_install').'</th>
			</tr>');
			
			$install_styles_form -> addToContent( $styles_to_install_list_content);
			
			$install_styles_form -> closeTable();
		
			parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_look_subsection_styles_found'), $install_styles_form -> display()));
			
		}
		
	}
	
	function act_edit_style(){
		
		//include globals
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * check target
		 */
		
		if ( isset( $_GET['style']) && !empty( $_GET['style'])){
			
			/**
			 * style to edit specified
			 * select it from mysql
			 */
			
			$style_to_edit = $_GET['style'];
			
			settype( $style_to_edit, 'integer');
			
			$style_query = $mysql -> query( "SELECT * FROM styles WHERE `style_id` = '$style_to_edit'");
			if ( $style_result = mysql_fetch_array( $style_query, MYSQL_ASSOC)){
				
				/**
				 * style found, clear result first
				 */
				
				$style_result = $mysql -> clear( $style_result);
				
				/**
				 * add breadcrumbs
				 */
				
				$path_link = array( 'act' => 'styles');
				
				$path -> addBreadcrumb( $language -> getString( 'acp_look_section_look'), parent::adminLink( parent::getId(), $path_link));		
				$path -> addBreadcrumb( $language -> getString( 'acp_look_subsection_styles'), parent::adminLink( parent::getId(), $path_link));
				
				$path_link = array( 'act' => 'edit_style', 'style' => $style_result['style_id']);
				
				$path -> addBreadcrumb( $language -> getString( 'acp_look_subsection_styles_edit'), parent::adminLink( parent::getId(), $path_link));
				
				/**
				 * set page title
				 */
				
				$output -> setTitle( $language -> getString( 'acp_look_subsection_styles_edit'));
				
				/**
				 * begin form
				 */
				
				$edit_style_link = array( 'act' => 'change_style', 'style' => $style_to_edit);
				
				$style_edit_form = new form();
				$style_edit_form -> openForm( parent::adminLink( parent::getId(), $edit_style_link));
				$style_edit_form -> openOpTable();
				
				$style_edit_form -> drawTextInput( $language -> getString( 'acp_look_subsection_styles_edit_name'), 'style_name', $style_result['style_name']);
				
				if ( $settings['default_style'] != $style_to_edit)
					$style_edit_form -> drawYesNo( $language -> getString( 'acp_look_subsection_styles_edit_default'), 'style_default', false);
				
				$style_edit_form -> closeTable();
				$style_edit_form -> drawSpacer( $language -> getString( 'acp_look_subsection_styles_edit_author'));
				$style_edit_form -> openOpTable();
				
				$style_edit_form -> drawTextInput( $language -> getString( 'acp_look_subsection_styles_edit_author'), 'style_author', $style_result['style_author']);
				$style_edit_form -> drawTextInput( $language -> getString( 'acp_look_subsection_styles_edit_author_www'), 'style_author_www', $style_result['style_www']);
				
				$style_edit_form -> closeTable();
				$style_edit_form -> drawButton( $language -> getString( 'acp_look_subsection_styles_edit_button'));
				$style_edit_form -> closeForm();
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_look_subsection_styles_edit'), $style_edit_form -> display()));
				
			}else{
				
				/**
				 * style to edit no found, draw error
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_edit'), $language -> getString( 'acp_look_subsection_styles_edit_notfound')));
				
				$this -> act_styles();
				
			}
		
			
		}else{
			
			/**
			 * style to edit no found, draw error
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_edit'), $language -> getString( 'acp_look_subsection_styles_edit_notarget')));
			
			$this -> act_styles();
			
		}
		
	}
	
	function act_delete_style(){
		
		//include globals
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * check target
		 */
		
		if ( isset( $_GET['style']) && !empty( $_GET['style'])){
			
			/**
			 * style to edit specified
			 * select it from mysql
			 */
			
			$style_to_delete = $_GET['style'];
			
			settype( $style_to_delete, 'integer');
			
			$style_query = $mysql -> query( "SELECT * FROM styles WHERE `style_id` = '$style_to_delete'");
			if ( $style_result = mysql_fetch_array( $style_query, MYSQL_ASSOC)){
				
				/**
				 * style found, clear result first
				 */
				
				$style_result = $mysql -> clear( $style_result);
				
				/**
				 * check if it is default
				 */
				
				if ( $settings['default_style'] != $style_to_delete){
					
					/**
					 * add breadcrumbs
					 */
					
					$path_link = array( 'act' => 'styles');
					
					$path -> addBreadcrumb( $language -> getString( 'acp_look_section_look'), parent::adminLink( parent::getId(), $path_link));		
					$path -> addBreadcrumb( $language -> getString( 'acp_look_subsection_styles'), parent::adminLink( parent::getId(), $path_link));
					
					$path_link = array( 'act' => 'delete_style', 'style' => $style_result['style_id']);
					
					$path -> addBreadcrumb( $language -> getString( 'acp_look_subsection_styles_delete'), parent::adminLink( parent::getId(), $path_link));
					
					/**
					 * set page title
					 */
					
					$output -> setTitle( $language -> getString( 'acp_look_subsection_styles_delete'));
					
					/**
					 * begin form
					 */
					
					$edit_style_link = array( 'act' => 'kill_style', 'style' => $style_to_delete);
					
					$style_del_form = new form();
					$style_del_form -> openForm( parent::adminLink( parent::getId(), $edit_style_link));
					$style_del_form -> openOpTable();
					
					$style_del_form -> drawInfoRow( $language -> getString( 'acp_look_subsection_styles_delete_users'), $style_result['style_users']);
					
					$other_styles_query = $mysql -> query( "SELECT * FROM styles WHERE `style_id` <> '$style_to_delete'");
					
					while ( $styles_result = mysql_fetch_array( $other_styles_query, MYSQL_ASSOC)){
						$styles_result = $mysql -> clear($styles_result);
						
						$replacements[$styles_result['style_id']] = $styles_result['style_name'];
						
					}
					
					$style_del_form -> drawList( $language -> getString( 'acp_look_subsection_styles_delete_replace'), 'style_replacement', $replacements);
					
					$style_del_form -> closeTable();
					$style_del_form -> drawButton( $language -> getString( 'acp_look_subsection_styles_delete_button'));
					$style_del_form -> closeForm();
					
					parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_look_subsection_styles_delete'), $style_del_form -> display()));
					
				}else{
					
					/**
					 * style is default
					 */
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_delete'), $language -> getString( 'acp_look_subsection_styles_delete_default')));
					
					$this -> act_styles();
					
				}
					
			}else{
				
				/**
				 * style to edit no found, draw error
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_delete'), $language -> getString( 'acp_look_subsection_styles_delete_notfound')));
				
				$this -> act_styles();
				
			}
		
			
		}else{
			
			/**
			 * style to edit no found, draw error
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_subsection_styles_delete'), $language -> getString( 'acp_look_subsection_styles_delete_notarget')));
			
			$this -> act_styles();
			
		}
		
	}
	
	function act_emoticons(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'emoticons');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_look_section_look'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_look_subsection_emoticons'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_look_subsection_emoticons'));
		
		/**
		 * do action
		 */
		
		if ( $_GET['do'] == 'update' && $session -> checkForm()){
			
			/**
			 * cycle trought emoticons to delete
			 */
			
			$emoticons_to_delete = $_POST['delete_emoticon'];
			
			settype( $emoticons_to_delete, 'array');
			
			foreach ( $emoticons_to_delete as $emoticon_id => $emoticon_check)
				$emoticons_to_delete_list[] = $emoticon_id;
			
			if ( isset( $emoticons_to_delete_list))				
				$mysql -> delete( 'emoticons', "`emoticon_id` IN (".join( ',', $emoticons_to_delete_list).")");
			
			/**
			 * now codes
			 */
			
			$emoticons_to_recode = $_POST['code_emoticon'];
			
			settype( $emoticons_to_recode, 'array');
			
			foreach ( $emoticons_to_recode as $emoticon_id => $emoticon_code){
				
				unset( $update_emo_sql);
				
				$emoticon_code = trim( $emoticon_code);
				
				if ( strlen( $emoticon_code) > 0){
				
					settype( $emoticon_id, 'integer');
					
					$update_emo_sql['emoticon_type'] = $strings -> inputClear( $emoticon_code, false);
					
					$mysql -> update( $update_emo_sql, 'emoticons', "`emoticon_id` = '$emoticon_id'");
					
				}
								
			}
				
			/**
			 * now names
			 */
		
			$emoticons_to_rename = $_POST['name_emoticon'];
			
			settype( $emoticons_to_rename, 'array');
			
			foreach ( $emoticons_to_rename as $emoticon_id => $emoticon_name){
				
				unset( $update_emo_sql);
				
				settype( $emoticon_id, 'integer');
				
				$update_emo_sql['emoticon_name'] = $strings -> inputClear( $emoticon_name, false);
				
				$mysql -> update( $update_emo_sql, 'emoticons', "`emoticon_id` = '$emoticon_id'");
								
			}
			
			$logs -> addAdminLog( $language -> getString( 'acp_look_subsection_emoticons_update_log'));
				
			parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_look_subsection_emoticons_update'), $language -> getString( 'acp_look_subsection_emoticons_update_done')));
					
			$cache -> flushCache( 'emoticons');
			
		}
		
		/**
		 * install new
		 */
		
		if ( $_GET['do'] == 'install_new' && $session -> checkForm()){
		
			/**
			 * build up an list of used images
			 */
			
			$used_images = array();
			
			$emoticons_query = $mysql -> query( "SELECT * FROM emoticons");
			while ( $emoticon_result = mysql_fetch_array( $emoticons_query, MYSQL_ASSOC)){
				$emoticon_result = $mysql -> clear( $emoticon_result);
				
				$used_images[] = $emoticon_result[ 'emoticon_image'];
				
			}
			
			/**
			 * build up an list of emoticons to add
			 */
			
			$new_emoticons_files = $_POST['install_emoticon'];
			settype( $new_emoticons_files, 'array');
			
			$new_emoticons_images = $_POST['image_emoticon'];
			settype( $new_emoticons_images, 'array');
			
			$new_emoticons_codes = $_POST['code_emoticon'];
			settype( $new_emoticons_codes, 'array');
			
			$new_emoticons_names = $_POST['name_emoticon'];
			settype( $new_emoticons_names, 'array');
			
			/**
			 * now extensions
			 */
			
			$proper_extensions[] = 'gif';
			$proper_extensions[] = 'jpg';
			$proper_extensions[] = 'png';
			
			/**
			 * and go
			 */
		
			foreach ( $new_emoticons_files as $emoticon_img => $add_emoticon){
				
				/**
				 * get emoticon data
				 */
				
				$emoticon_image = trim( $new_emoticons_images[$emoticon_img]);
				$emoticon_code = trim( $new_emoticons_codes[$emoticon_img]);
				$emoticon_name = trim( $new_emoticons_names[$emoticon_img]);
				
				/**
				 * check image type, and its existence on list and in folder
				 */
				
				if ( file_exists( ROOT_PATH.'images/emoticons/'.$emoticon_image) && in_array( substr( $emoticon_image, strrpos($emoticon_image, ".")+1) ,$proper_extensions ) && !in_array( $emoticon_image, $used_images)){
					
					if ( strlen( $emoticon_image) > 0 && strlen( $emoticon_code) > 0 && strlen( $emoticon_name) > 0){
					
						unset( $new_emoticon_sql);
						
						$new_emoticon_sql['emoticon_type'] = $strings -> inputClear($emoticon_code, false);
						$new_emoticon_sql['emoticon_image'] = $strings -> inputClear($emoticon_image, false);
						$new_emoticon_sql['emoticon_name'] = $strings -> inputClear($emoticon_name, false);
						
						$mysql -> insert( $new_emoticon_sql, 'emoticons');
						
						$new_emoticones_added = true;
						
					}
					
				}
				
			}
			
			if ( isset( $new_emoticones_added)){
				
				$logs -> addAdminLog( $language -> getString( 'acp_look_subsection_emoticons_new_log'));
				
				parent::draw( $style -> drawInfoBlock( $language -> getString( 'acp_look_subsection_emoticons_new'), $language -> getString( 'acp_look_subsection_emoticons_new_done')));
				
				$cache -> flushCache( 'emoticons');
			
			}
				
		}
		
		/**
		 * draw lis of found emoticons
		 */
		
		$update_emos_link = array( 'act' => 'emoticons', 'do' => 'update');
		
		$emoticons_list = new form();
		$emoticons_list -> openForm( parent::adminLink( parent::getId(), $update_emos_link));
		$emoticons_list -> openOpTable( true);
		$emoticons_list -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_look_subsection_emoticons_list_image').'</th>
			<th>'.$language -> getString( 'acp_look_subsection_emoticons_list_code').'</th>
			<th>'.$language -> getString( 'acp_look_subsection_emoticons_list_name').'</th>
			<th>'.$language -> getString( 'acp_look_subsection_emoticons_list_delete').'</th>
		</tr>');
		
		$emoticons_query = $mysql -> query( "SELECT * FROM emoticons");
		
		$existing_images_list = array();
		
		while ( $emoticon_result = mysql_fetch_array( $emoticons_query, MYSQL_ASSOC)){
			
			$emoticon_result = $mysql -> clear( $emoticon_result);
			
			/**
			 * add to existing list
			 */
			
			$existing_images_list[] = $emoticon_result['emoticon_image'];
			
			/**
			 * add element to list
			 */
			
			$emoticons_list -> addToContent( '<tr>
				<td class="opt_row1" style="text-align: center"><img src="'.ROOT_PATH.'images/emoticons/'.$emoticon_result['emoticon_image'].'" /> '.$emoticon_result['emoticon_image'].'</td>
				<td class="opt_row2" style="text-align: center"><input type="text" name="code_emoticon['.$emoticon_result['emoticon_id'].']" value="'.$emoticon_result['emoticon_type'].'" /></td>
				<td class="opt_row1" style="text-align: center"><input type="text" name="name_emoticon['.$emoticon_result['emoticon_id'].']" value="'.$emoticon_result['emoticon_name'].'" /></td>
				<td class="opt_row3" style="text-align: center">'.$emoticons_list -> drawSelect( 'delete_emoticon['.$emoticon_result['emoticon_id'].']').'</td>
			</tr>');
			
		}
		
		$emoticons_list -> closeTable();
		$emoticons_list -> drawButton( $language -> getString( 'acp_look_subsection_emoticons_list_edit'));
		$emoticons_list -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_look_subsection_emoticons'), $emoticons_list -> display()));
		
		/**
		 * and possible to install
		 */
		
		$found_emoticons = glob( ROOT_PATH."images/emoticons/*.{gif,jpg,png}", GLOB_BRACE);
				
		$update_emos_link = array( 'act' => 'emoticons', 'do' => 'install_new');
		
		$emoticons_install_list = new form();
		$emoticons_install_list -> openForm( parent::adminLink( parent::getId(), $update_emos_link));
		$emoticons_install_list -> openOpTable( true);
		$emoticons_install_list -> addToContent( '<tr>
			<th>'.$language -> getString( 'acp_look_subsection_emoticons_list_image').'</th>
			<th>'.$language -> getString( 'acp_look_subsection_emoticons_list_code').'</th>
			<th>'.$language -> getString( 'acp_look_subsection_emoticons_list_name').'</th>
			<th>'.$language -> getString( 'acp_look_subsection_emoticons_list_install').'</th>
		</tr>');
		
		/**
		 * go trought found emoticons list
		 */
		
		foreach ( $found_emoticons as $emoticon_path){
						
			/**
			 * create names
			 */
						
			$emoticon_image = str_ireplace( ROOT_PATH."images/emoticons/", '', $emoticon_path);
			$emoticon_name = substr( $emoticon_image, 0, strrpos( $emoticon_image, '.'));
			
			if ( !in_array( $emoticon_image, $existing_images_list)){
			
				$emoticons_install_list -> addToContent( '<tr>
					<td class="opt_row1" style="text-align: center"><input name="image_emoticon['.$emoticon_name.']" type="hidden" value="'.$emoticon_image.'" /><img src="'.$emoticon_path.'" /> '.$emoticon_image.'</td>
					<td class="opt_row2" style="text-align: center"><input type="text" name="code_emoticon['.$emoticon_name.']" value=":'.$emoticon_name.':" /></td>
					<td class="opt_row1" style="text-align: center"><input type="text" name="name_emoticon['.$emoticon_name.']" value="'.$emoticon_name.'" /></td>
					<td class="opt_row3" style="text-align: center">'.$emoticons_list -> drawSelect( 'install_emoticon['.$emoticon_name.']').'</td>
				</tr>');
				
			}
		}
		
		$emoticons_install_list -> closeTable();
		$emoticons_install_list -> drawButton( $language -> getString( 'acp_look_subsection_emoticons_list_add'));
		$emoticons_install_list -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_look_subsection_emoticons_found_list'), $emoticons_install_list -> display()));
		
	}
	
	function act_languages_manager(){
				
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'langs');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_look_section_langs'), parent::adminLink( parent::getId(), $path_link));		
		$path -> addBreadcrumb( $language -> getString( 'acp_look_section_langs'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_look_section_langs'));
		
		/**
		 * draw list of languages
		 */
		
		$new_language_link = array( 'act' => 'new_language');
		
		$langs_list = new form();
		$langs_list -> openForm( parent::adminLink( parent::getId(), $new_language_link));
		$langs_list -> openOpTable();
		$langs_list -> addToContent('<tr>
			<th>'.$language -> getString( 'acp_look_section_langs_list_name').'</th>
			<th>'.$language -> getString( 'acp_look_section_langs_list_default').'</th>
			<th>'.$language -> getString( 'actions').'</th>
		</tr>');
		
		$langs_query = $mysql -> query( "SELECT * FROM languages");
		while ( $lang_result = mysql_fetch_array( $langs_query, MYSQL_ASSOC)) {
			
			$lang_result = $mysql -> clear( $lang_result);
				
			$language_default = false;
			
			if ( $settings['default_language'] == $lang_result['lang_id'])
				$language_default = true;
			
			$edit_lang_link = array( 'act' => 'edit_language', 'lang' => $lang_result['lang_id']);
			$del_lang_link = array( 'act' => 'delete_language', 'lang' => $lang_result['lang_id']);
				
			$langs_list -> addToContent('<tr>
				<td class="opt_row1" style="width: 100%">'.$lang_result['lang_name'].'</td>
				<td class="opt_row2" style="text-align: center" NOWRAP>'.$style -> drawThick( $language_default, true).'</td>
				<td class="opt_row3" style="text-align: center" NOWRAP>
				<a href="'.parent::adminLink( parent::getId(), $edit_lang_link).'">'.$style -> drawImage( 'edit', $language -> getString( 'edit')).'</a>
				<a href="'.parent::adminLink( parent::getId(), $del_lang_link).'">'.$style -> drawImage( 'delete', $language -> getString( 'delete')).'</a>
				</td>
			</tr>');
			
		}
		
		$langs_list -> closeTable();
		$langs_list -> drawButton( $language -> getString( 'acp_look_section_langs_new_lang_button'));
		$langs_list -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_look_section_langs'), $langs_list -> display()));
		
	}
	
	function act_new_lang(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumbs
		 */
		
		$path_link = array( 'act' => 'langs');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_look_section_langs'), parent::adminLink( parent::getId(), $path_link));		
		
		$path_link = array( 'act' => 'new_language');
		
		$path -> addBreadcrumb( $language -> getString( 'acp_look_section_langs_new'), parent::adminLink( parent::getId(), $path_link));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'acp_look_section_langs_new'));
		
		/**
		 * predef vars
		 */
		
		$new_lang_name = '';
		$new_lang_id = '';
		
		if ( $_GET['act'] == 'save_language'){
			
			$new_lang_id = stripslashes( $strings -> inputClear( $_POST[ 'lang_id'], false));
			$new_lang_name = stripslashes( $strings -> inputClear( $_POST[ 'lang_name'], false));
		
		}
		
		/**
		 * draw form
		 */
		
		$add_new_lang_link = array( 'act' => 'save_language');
		
		$new_lang_form = new form();
		$new_lang_form -> openForm( parent::adminLink( parent::getId(), $add_new_lang_link));
		$new_lang_form -> openOpTable();
		
		$new_lang_form -> drawTextInput( $language -> getString( 'acp_look_section_langs_new_id'), 'lang_id', $new_lang_id, $language -> getString( 'acp_look_section_langs_new_id_help'));
		$new_lang_form -> drawTextInput( $language -> getString( 'acp_look_section_langs_new_name'), 'lang_name', $new_lang_name);
				
		$new_lang_form -> closeTable();
		$new_lang_form -> drawButton( $language -> getString( 'acp_look_section_langs_new_lang_save_button'));
		$new_lang_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_look_section_langs_new'), $new_lang_form -> display()));
	}
	
	function act_edit_lang(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		if ( isset( $_GET['lang']) && !empty( $_GET['lang'])) {
			
			$lang_to_edit = uniSlashes(trim($_GET['lang']));
			
			$lang_query = $mysql -> query( "SELECT * FROM languages WHERE `lang_id` = '$lang_to_edit'");
			
			if ( $lang_result = mysql_fetch_array( $lang_query, MYSQL_ASSOC)){
				
				$lang_result = $mysql -> clear( $lang_result);
				
				$lang_name = $lang_result['lang_name'];
				
				/**
				 * add breadcrumbs
				 */
				
				$path_link = array( 'act' => 'langs');
				
				$path -> addBreadcrumb( $language -> getString( 'acp_look_section_langs'), parent::adminLink( parent::getId(), $path_link));		
				
				$path_link = array( 'act' => 'edit_language', 'lang' => $lang_to_edit);
				
				$path -> addBreadcrumb( $language -> getString( 'acp_look_section_langs_edit'), parent::adminLink( parent::getId(), $path_link));
				
				/**
				 * set page title
				 */
				
				$output -> setTitle( $language -> getString( 'acp_look_section_langs_edit'));
				
				/**
				 * draw form
				 */
				
				$edit_lang_link = array( 'act' => 'change_language', 'lang' => $lang_to_edit);
		
				$new_lang_form = new form();
				$new_lang_form -> openForm( parent::adminLink( parent::getId(), $edit_lang_link));
				$new_lang_form -> openOpTable();
				
				$new_lang_form -> drawTextInput( $language -> getString( 'acp_look_section_langs_new_name'), 'lang_name', $lang_name);
						
				/**
				 * default form
				 */
				
				if ( $lang_to_edit != $settings[ 'default_language'])
					$new_lang_form -> drawYesNo( $language -> getString( 'acp_look_section_langs_new_default'), 'lang_default', false);
				
				$new_lang_form -> closeTable();
				$new_lang_form -> drawButton( $language -> getString( 'acp_look_section_langs_edit_button'));
				$new_lang_form -> closeForm();
				
				parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_look_section_langs_edit'), $new_lang_form -> display()));
			
				
			}else{
				
				/**
				 * lang not found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_edit'), $language -> getString( 'acp_look_section_langs_edit_notfound')));
							
				$this -> act_languages_manager();
							
			}
			
		}else{
			
			/**
			 * empty lang to edit
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_edit'), $language -> getString( 'acp_look_section_langs_edit_empty')));
			
			$this -> act_languages_manager();
			
		}
		
	}
	
	function act_delete_lang(){
		
		//include
		include(FUNCTIONS_GLOBALS);
		
		if ( isset( $_GET['lang']) && !empty( $_GET['lang'])) {
			
			$lang_to_edit = uniSlashes(trim($_GET['lang']));
			
			$lang_query = $mysql -> query( "SELECT * FROM languages WHERE `lang_id` = '$lang_to_edit'");
			
			if ( $lang_result = mysql_fetch_array( $lang_query, MYSQL_ASSOC)){
				
				$lang_result = $mysql -> clear( $lang_result);
				
				/**
				 * check if default
				 */
				
				if ( $lang_to_edit == $settings[ 'default_language']){
					
					/**
					 * cant delete default
					 */
					
					parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_delete'), $language -> getString( 'acp_look_section_langs_delete_cant_default')));
								
					$this -> act_languages_manager();
					
				}else{
					
					/**
					 * add breadcrumbs
					 */
					
					$path_link = array( 'act' => 'langs');
					
					$path -> addBreadcrumb( $language -> getString( 'acp_look_section_langs_delete'), parent::adminLink( parent::getId(), $path_link));		
					
					$path_link = array( 'act' => 'delete_language', 'lang' => $lang_to_edit);
					
					$path -> addBreadcrumb( $language -> getString( 'acp_look_section_langs_delete'), parent::adminLink( parent::getId(), $path_link));
					
					/**
					 * set page title
					 */
					
					$output -> setTitle( $language -> getString( 'acp_look_section_langs_delete'));
					
					/**
					 * now delete form
					 */
					
					$delete_lang_link = array( 'act' => 'kill_language', 'lang' => $lang_to_edit);
		
					$delete_lang_form = new form();
					$delete_lang_form -> openForm( parent::adminLink( parent::getId(), $delete_lang_link));
					$delete_lang_form -> openOpTable();
					
					$delete_lang_form -> drawInfoRow( $language -> getString( 'acp_look_section_langs_del_users'), $lang_result['lang_users']);
					
					$replacements_query = $mysql -> query( "SELECT * FROM languages WHERE `lang_id` <> '$lang_to_edit'");
					
					while ( $replacements_result = mysql_fetch_array( $replacements_query, MYSQL_ASSOC)){
						
						$replacements_result = $mysql -> clear( $replacements_result);
						
						$replacements_list[$replacements_result['lang_id']] = $replacements_result['lang_name'];
						
					}
					
					$delete_lang_form -> drawList( $language -> getString( 'acp_look_section_langs_del_replace'), 'lang_replace', $replacements_list);
					
					$delete_lang_form -> closeTable();
					$delete_lang_form -> drawButton( $language -> getString( 'acp_look_section_langs_delete_lang_button'));
					$delete_lang_form -> closeForm();
					
					parent::draw( $style -> drawFormBlock( $language -> getString( 'acp_look_section_langs_delete'), $delete_lang_form -> display()));
								
				}
				
			}else{
				
				/**
				 * lang not found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_delete'), $language -> getString( 'acp_look_section_langs_delete_notfound')));
							
				$this -> act_languages_manager();
							
			}
			
		}else{
			
			/**
			 * empty lang to edit
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'acp_look_section_langs_delete'), $language -> getString( 'acp_look_section_langs_delete_empty')));
			
			$this -> act_languages_manager();
			
		}
		
	}
	
}
	
?>