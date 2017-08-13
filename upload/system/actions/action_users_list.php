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
|	Show users list
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class action_users_list extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check, if list is on
		 */
		
		if( $settings['users_list_turn']){
	
			/**
			 * check, if user can se a list
			 */
			
			if ( $session -> user['user_can_see_users_profiles']){
				
				/**
				 * add entry to path
				 */
					
				$path -> addBreadcrumb( $language -> getString('users'), parent::systemLink( parent::getId()));
						
				/**
				 * set page title
				 */
				
				$output -> setTitle( $language -> getString( 'users'));
				
				/**
				 * begin drawing
				 */
				
				$users_list = new form();
				
				
				if( $settings['users_list_type'] == 0){
					
					$users_list -> openOpTable( true);
				
				}else{
					
					$users_list -> addToContent('<table width="100%" border="0" cellspacing="4" cellpadding="0" style="table-layout: fixed">');
				
				}
				
				$users_list -> addToContent('<tr>');
				
				/**
				 * if we are in table, draw header
				 */
				
				if( $settings['users_list_type'] == 0){
					
					/**
					 * avatar column
					 */
					
					if ( $settings['users_list_draw_avatar'] && $settings['users_can_avatars'] && $session -> user['user_show_avatars'])
						$users_list -> addToContent('<th>'.$language -> getString( 'user_avatar').'</th>');
					
					
					$users_list -> addToContent('<th>'.$language -> getString( 'user_username').'</th>');
					
					if ( $settings['users_list_draw_rank'])
						$users_list -> addToContent('<th>'.$language -> getString( 'user_rank').'</th>');
					
					$users_list -> addToContent('<th>'.$language -> getString( 'user_group').'</th>');
					
					if ( $settings['users_list_draw_register'])
						$users_list -> addToContent('<th>'.$language -> getString( 'user_registration').'</th>');
					
					if ( $settings['users_list_draw_posts'])
						$users_list -> addToContent('<th>'.$language -> getString( 'user_posts').'</th>');
					
					/**
					 * custom fields
					 */
						
					foreach ( $users -> custom_fields as $field_ops){
						
						if ( $field_ops['profile_field_onlist'] && !$field_ops['profile_field_private'])
							$users_list -> addToContent('<th>'.$field_ops['profile_field_name'].'</th>');
					
					}
					
					/**
					 * in the end we will draw contact
					 */
					
					if ( $settings['users_list_draw_contact'])
						$users_list -> addToContent('<th>'.$language -> getString( 'user_contact').'</th>');
									
					$users_list -> addToContent('</tr>');
					
				}else{
					
					/**
					 * 4 users in row
					 */
					
					$users_in_row = 0;
					
					$users_list -> addToContent('<tr>');
					
				}
				
				/**
				 * begin paginating
				 */
				
				$users_num = $settings['users_num'];
				
				$users_per_page = $settings['users_list_users_per_page'];
				
				if ( $users_per_page < 1)
					$users_per_page = 1;
				
				/**
				 * we will always draw 20 users per page
				 */
				
				$pages_num = ceil( $users_num / $users_per_page);
				
				/**
				 * get current page
				 */
				
				$current_page = $_GET['p'];
				
				$current_page --;
				
				settype( $current_page, 'integer');
				
				if ( $current_page < 0)
					$current_page = 1;
					
				if ( $current_page > $pages_num)
					$current_page = $pages_num;
					
				/**
				 * ordering
				 */
				
				$ordering_methods[0] = $language -> getString( 'users_list_ordering_0');
				$ordering_methods[1] = $language -> getString( 'users_list_ordering_1');
				$ordering_methods[2] = $language -> getString( 'users_list_ordering_2');
				$ordering_methods[3] = $language -> getString( 'users_list_ordering_3');
				
				$ordering_method = $_GET['o'];
				
				if (!key_exists( $ordering_method, $ordering_methods))
					$ordering_method = 0;
				
				$ordering_directions[0] = $language -> getString( 'users_list_direction_0');
				$ordering_directions[1] = $language -> getString( 'users_list_direction_1');
					
				$ordering_direction = $_GET['d'];
				
				if (!key_exists( $ordering_direction, $ordering_directions))
					$ordering_direction = 0;
				
				
				/**
				 * and paginator
				 */
				
				parent::draw( $style -> drawPaginator( parent::systemLink( parent::getId(), array( 'o' => $ordering_method, 'd' => $ordering_direction)), 'p', $pages_num, ($current_page+1)));
										
				/**
				 * in sql
				 */
				
				$ordering_methods_sql[0] = "user_login";
				$ordering_methods_sql[1] = "user_regdate";
				$ordering_methods_sql[2] = "user_posts_num";
				$ordering_methods_sql[3] = "user_last_login";
				
				$ordering_directions_sql[0] = "ASC";
				$ordering_directions_sql[1] = "DESC";
				
				/**
				 * make query
				 */
				
				$users_query = $mysql -> query( "SELECT u.*, f.*, g.* FROM users u
				LEFT JOIN profile_fields_data f ON u.user_id = f.profile_fields_user
				LEFT JOIN users_groups g ON u.user_main_group = g.users_group_id
				WHERE u.user_id > '-1' ORDER BY u.".$ordering_methods_sql[$ordering_method]." ".$ordering_directions_sql[$ordering_direction]." LIMIT ".($current_page * $users_per_page).", ".$users_per_page);
				while ( $user_result = mysql_fetch_array( $users_query, MYSQL_ASSOC)) {
	
					$user_result = $mysql -> clear( $user_result);
					
					$user_show_link['user'] = $user_result['user_id'];
					
					if ( $settings['users_count_online']){
						$user_status = $style -> drawStatus( $users -> checkOnline( $user_result['user_id'])).' ';
					}else{
						$user_status = '';
					}
					
					/**
					 * draw diffrent things
					 */
					
					if ( $settings['users_list_type'] == 0){
						
						$users_list -> addToContent('<tr>');
						
						/**
						 * avatar
						 */
						
						if ( $settings['users_list_draw_avatar'] && $settings['users_can_avatars'] && $session -> user['user_show_avatars']){
							
							if ( $user_result['user_avatar_type'] != 0){
								
								$users_list -> addToContent( '<td class="opt_row2" style="text-align: center">'.$users -> drawAvatar( $user_result['user_avatar_type'], $user_result['user_avatar_image'], $user_result['user_avatar_width'], $user_result['user_avatar_height']).'</td>');
								
							}else{
							
								$users_list -> addToContent( '<td class="opt_row2" style="text-align: center">&nbsp;</td>');
							
							}
							
						}
						
						$field_num = 0;
						
						/**
						 * user name
						 */
						
						$users_list -> addToContent('<td class="opt_row'.(1 + ($field_num % 2)).'">'.$user_status.'<a href="'.parent::systemLink( 'user', $user_show_link).'">'.$user_result['user_login'].'</a></td>');
												
						/**
						 * user rank
						 */
						
						if ( $settings['users_list_draw_rank']){
							
							$field_num++;
						
							if ( strlen( $users -> users_groups[$user_result['user_main_group']]['users_group_image']) > 0){
								
								$users_list -> addToContent('<td class="opt_row'.(1 + ($field_num % 2)).'"><img src="'.$users -> users_groups[$user_result['user_main_group']]['users_group_image'].'" alt="" title""/></td>');
						
							}else{
								
								$users_list -> addToContent('<td class="opt_row'.(1 + ($field_num % 2)).'">'.$users -> drawRankImage( $user_result['user_posts_num']).'</td>');
						
							}
						}
						
						/**
						 * user group
						 */
						
						$field_num++;
							
						$users_list -> addToContent('<td class="opt_row'.(1 + ($field_num % 2)).'" style="text-align: center">'.$user_result['users_group_prefix'].$user_result['users_group_name'].$user_result['users_group_suffix'].'</td>');
						
						/**
						 * user register date
						 */
						
						if ( $settings['users_list_draw_register']){
							
							$field_num++;
						
							$users_list -> addToContent('<td class="opt_row'.(1 + ($field_num % 2)).'" style="text-align: center">'.$time -> drawDate( $user_result['user_regdate']).'</td>');
						
						}
						
						/**
						 * user posts num
						 */
						
						if ( $settings['users_list_draw_posts']){
							
							$field_num ++;
							
							$users_list -> addToContent('<td class="opt_row'.(1 + ($field_num % 2)).'" style="text-align: center">'.$user_result['user_posts_num'].'</td>');						
						
						}
						
						/**
						 * custom fields
						 */
							
						foreach ( $users -> custom_fields as $field_id => $field_ops){
							
							if ( $field_ops['profile_field_onlist'] && !$field_ops['profile_field_private']){
								
								$field_num ++;
								
								$users_list -> addToContent('<td class="opt_row'.(1 + ($field_num % 2)).'" style="text-align: center">'.$user_result['field_'.$field_id].'</td>');
							
							}
						}
						
						/**
						 * contact
						 */
						
						if ( $settings['users_list_draw_contact']){
						
							$field_num++;
							
							$contact_ops = array();
							
							if ( $session -> user['user_id'] != -1){
								
								$send_pw_link = array( 'do' => 'new_pm', 'user' => $user_result['user_id']);
									
								$contact_ops[] = '<a href="'.parent::systemLink( 'profile', $send_pw_link).'">'.$style -> drawImage( 'button_pm', $language -> getString( 'user_pm_send')).'</a>';
								
							}
							
							if ( $user_result['user_show_mail'] && $user_result['user_want_mail']){
								$contact_ops[] = '<a href="mailto:'.$user_result['user_mail'].'">'.$style -> drawImage( 'button_email').'</a>';
							}else if ( !$user_result['user_show_mail'] && $user_result['user_want_mail']){
								$send_user_mail_link = array( 'user' => $user_result['user_id']);
								$contact_ops[] = '<a href="'.parent::systemLink( 'mail_user', $send_user_mail_link).'">'.$style -> drawImage( 'button_email').'</a>';
							}
													
							if ( count( $contact_ops) > 0){
								
								$users_list -> addToContent( '<td class="opt_row'.(1 + ($field_num % 2)).'" style="text-align: center">'.join( " ", $contact_ops).'</td>');
							
							}else{
								
								$users_list -> addToContent( '<td class="opt_row'.(1 + ($field_num % 2)).'" style="text-align: center">&nbsp;</td>');
							
							}
								
						}
						
						$users_list -> addToContent('</tr>');
						
					}else{
						
						/**
						 * draw blocks
						 * break, if we have to
						 */
							
						if ( $users_in_row == 4){
							
							$users_list -> addToContent('</tr><tr>');
							$users_in_row = 0;
												
						}
						
						/**
						 * begin drawing
						 */
						
						$drawed_block = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
    							<td style="height: 100px">';
						
						/**
						 * draw avatar
						 */
						
						if ( $settings['users_list_draw_avatar'] && $settings['users_can_avatars'] && $session -> user['user_show_avatars']){
							
							if ( $user_result['user_avatar_type'] != 0){
								
								/**
								 * acpect scale avatar
								 */
								
								if( $user_result['user_avatar_width'] > 50){
									
									$user_result['user_avatar_height'] = round( $user_result['user_avatar_height'] / ($user_result['user_avatar_width'] / 50));
									$user_result['user_avatar_width'] = 50;
									
								}
								
								if( $user_result['user_avatar_height'] > 50){
									
									$user_result['user_avatar_width'] = round( $user_result['user_avatar_width'] / ($user_result['user_avatar_height'] / 50));
									$user_result['user_avatar_height'] = 50;
									
								}
								
								$drawed_block .= '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td style="width: 100%; vertical-align: top">';
								$close_block = '</td><td style="vertical-align: top">'.$users -> drawAvatar( $user_result['user_avatar_type'], $user_result['user_avatar_image'], $user_result['user_avatar_width'], $user_result['user_avatar_height']).'</td></tr></table>';
																
							}else{
							
								$drawed_block .= '';							
								$close_block = '';
								
							}
							
						}
						
						//user nick
						$drawed_block .= $user_status.'<a href="'.parent::systemLink( 'user', $user_show_link).'">'.$user_result['user_login'].'</a><br />';
																		
						/**
						 * user rank
						 */
						
						if ( $settings['users_list_draw_rank']){
							
							if ( strlen( $users -> users_groups[$user_result['user_main_group']]['users_group_image']) > 0){
								
								$drawed_block .= '<img src="'.$users -> users_groups[$user_result['user_main_group']]['users_group_image'].'" alt="" title""/><br />';
							
							}else{
								
								$drawed_block .= $users -> drawRankImage( $user_result['user_posts_num']).'<br />';
							
							}						
													
						}
						
						/**
						 * user group
						 */
						
						$drawed_block .= '<br />'.$user_result['users_group_prefix'].$user_result['users_group_name'].$user_result['users_group_suffix'].'<br />';
												
						//close block
						$drawed_block .= $close_block;
							
						/**
						 * user register date
						 */
						
						if ( $settings['users_list_draw_register'])
							$drawed_block .= '<b>'.$language -> getString( 'user_registration').':</b> '.$time -> drawDate( $user_result['user_regdate']).'<br />';
						
						/**
						 * user posts num
						 */
						
						if ( $settings['users_list_draw_posts'])
							$drawed_block .= '<b>'.$language -> getString( 'user_posts').':</b> '.$user_result['user_posts_num'].'<br />';						

						
						/**
						 * custom fields
						 */
							
						foreach ( $users -> custom_fields as $field_id => $field_ops){
							
							if ( $field_ops['profile_field_onlist'] && !$field_ops['profile_field_private']){
																
								if ( strlen( $user_result['field_'.$field_id]) > 0){
								
									$drawed_block .= '<b>'.$field_ops['profile_field_name'].':</b> '.$user_result['field_'.$field_id].'<br />';
								
								}else{
									
									$drawed_block .= '<b>'.$field_ops['profile_field_name'].':</b> <i>'.$language -> getString( 'user_no_info').'</i>'.'<br />';
									
								}
							}
						}
						
						$drawed_block .= '</td></tr>';
						
						/**
						 * contact
						 */
						
						if ( $settings['users_list_draw_contact']){
						
							$field_num++;
							
							$contact_ops = array();
							
							if ( $session -> user['user_id'] != -1){
								
								$send_pw_link = array( 'do' => 'new_pm', 'user' => $user_result['user_id']);
									
								$contact_ops[] = '<a href="'.parent::systemLink( 'profile', $send_pw_link).'">'.$style -> drawImage( 'button_pm', $language -> getString( 'user_pm_send')).'</a>';
								
							}
							
							if ( $user_result['user_show_mail'] && $user_result['user_want_mail']){
								$contact_ops[] = '<a href="mailto:'.$user_result['user_mail'].'">'.$style -> drawImage( 'button_email').'</a>';
							}else if ( !$user_result['user_show_mail'] && $user_result['user_want_mail']){
								$send_user_mail_link = array( 'user' => $user_result['user_id']);
								$contact_ops[] = '<a href="'.parent::systemLink( 'mail_user', $send_user_mail_link).'">'.$style -> drawImage( 'button_email').'</a>';
							}
													
							if ( count( $contact_ops) > 0){
								
								$drawed_block .= '<tr><td>'.join( " ", $contact_ops).'</td></tr>';
							
							}
								
						}
						
						$drawed_block .= '</table>';
						
						/**
						 * add block
						 */
									
						$users_list -> addToContent('<td class="blankblock">'.$drawed_block.'</td>');

						$users_in_row++;
											
					}
						
				}
								
				if( $settings['users_list_type'] == 0){
				
					$users_list -> closeTable();
				
					parent::draw( $style -> drawFormBlock( $language -> getString('users'), $users_list -> display()));
				
				}else{
				
					while ( $users_in_row < 4){
					
						$users_list -> addToContent('<td>&nbsp;</td>');
						$users_in_row ++;
						
					}
					
					$users_list -> addToContent('</tr>');
					
					$users_list -> closeTable();
								
					parent::draw( $style -> drawBlock( $language -> getString('users'), $users_list -> display()));
					
				}
				
				
				/**
				 * and paginator
				 */
				
				parent::draw( $style -> drawPaginator( parent::systemLink( parent::getId(), array( 'o' => $ordering_method, 'd' => $ordering_direction)), 'p', $pages_num, ($current_page+1)));
				
				/**
				 * build html
				 */
				
				foreach ( $ordering_methods as $dir_id => $dir_value)
					$ordering_methods_list .= '<option value="'.$dir_id.'">'.$dir_value.'</value>';
					
				foreach ( $ordering_directions as $dir_id => $dir_value)
					$ordering_directions_list .= '<option value="'.$dir_id.'">'.$dir_value.'</value>';
					
				$ordering_methods_list = str_ireplace( 'value="'.$ordering_method.'">', 'value="'.$ordering_method.'" selected>', $ordering_methods_list);
				$ordering_directions_list = str_ireplace( 'value="'.$ordering_direction.'">', 'value="'.$ordering_direction.'" selected>', $ordering_directions_list);
					
				/**
				 * and other tabs
				 */
				
				$ordering_select_tab = new form();
				$ordering_select_tab -> openForm( parent::getId());
				$ordering_select_tab -> addToContent( '<table border="0" cellspacing="0" cellpadding="4" style="margin-left:auto">
					<tr>
						<td><b>'.$language -> getString( 'users_list_ordering').':</b><br />
						      <select id="order_method" name="order_method">
						      '.$ordering_methods_list.'
						      </select>
						</td>
						<td><b>'.$language -> getString( 'users_list_direction').':</b>
						<br />
						      <select id="order_direction" name="order_direction">
						      '.$ordering_directions_list.'
						      </select>
						</td>
						<td><a href="javascript:newOrder()">'.$style -> drawImage( 'button_go').'</a></td>
					</tr>
				</table>
				
				<script type="text/JavaScript">
			
					function newOrder(){
					
						order_method_sel = document.getElementById( "order_method")
						order_direction_sel = document.getElementById( "order_direction")
						document.location = "./index.php?act=users&p=1&o=" + order_method_sel.value + "&d=" + order_direction_sel.value
						
					}
	
				</script>');
				
				$ordering_select_tab -> closeForm();
				
				parent::draw( $style -> drawBlankBlock( $ordering_select_tab -> display()));
				
			}else{
				
				/**
				 * draw error, and force login
				 */
					
				$main_error = new main_error();
				$main_error -> type = 'information';
				parent::draw( $main_error -> display());
					
			}
			
		}else{
			
			/**
			 * draw message
			 */
			
			$main_error = new main_error();
			$main_error -> type = 'information';
			$main_error -> message = $language -> getString( 'users_list_off');
			parent::draw( $main_error -> display());
						
		}
	}
	
}

?>