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
|	User profile list
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
/**
 * draw user profile
 *
 */

class action_user_profile extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * add first breadcrumb
		 */
		
		$path -> addBreadcrumb( $language -> getString('users'), parent::systemLink( 'users'));
						
		/**
		 * get user id
		 */
		
		if ( isset( $_GET['user']) && !empty( $_GET['user']) && $_GET['user'] >= 1){
			
			/**
			 * we will display foreign user
			 */
			
			$user_to_draw = $_GET['user'];
			settype( $user_to_draw, 'integer');
			
		}else{
			
			$user_to_draw = $session -> user['user_id'];
			
		}
		
		/**
		 * check if user can watch profile
		 */
		
		if ( $session -> user['user_can_see_users_profiles'] || ($session -> user['user_id'] == $user_to_draw)){
			
			if ( $user_to_draw != -1){
				
				$user_query = $mysql -> query( "SELECT u.*, f.* FROM users u
				LEFT JOIN profile_fields_data f ON u.user_id = f.profile_fields_user
				WHERE u.user_id = '$user_to_draw'");
				
				if( $user_result = mysql_fetch_array($user_query, MYSQL_ASSOC)){
					
					$user_found = true;
					
					/**
					 * clear results
					 */
					
					$user_result = $mysql -> clear( $user_result);
					$this -> user_to_draw = $user_to_draw;
					
					/**
					 * check additional fields
					 */
					
					if ( strlen( $user_result['profile_fields_user']) == 0)
						$mysql -> insert( array( 'profile_fields_user' => $user_to_draw), 'profile_fields_data');
					
					/**
					 * now split page into two parts using table
					 */
					
					parent::draw( '<table style="width: 100%; table-layout: fixed" cellpadding="0" cellspacing="0"><tr><td style="padding-right: 3px; vertical-align: top" width="60%">');
					
						
					/**
					 * what mode
					 */
					
					if ( $smode != 2){
					
						/**
						 * mod action
						 */
						
						if ( $session -> user['user_can_be_mod']){
							
							if ( $_GET['mod_do'] == 'kill_avatar' && $user_result['user_avatar_type'] != 0){
								
								$users -> killAvatar( $user_to_draw);
								$user_result['user_avatar_type'] = 0;
								
								/**
								 * log
								 */
								
								$logs -> addModLog( $language -> getString( 'user_delete_avatar_log'), array( 'mod_user_name' => $user_result['user_login']));
								
							}
							
							if ( $_GET['mod_do'] == 'kill_title' && strlen($user_result['user_custom_title']) > 0 && $settings['users_posts_to_title'] > 0 && $user_result['user_posts_num'] >= $settings['users_posts_to_title'] ){
								
								$user_result['user_custom_title'] = '';
								
								$mysql -> update( array( 'user_custom_title' => ''), 'users', "`user_id` = '$user_to_draw'");
								
								/**
								 * log
								 */
								
								$logs -> addModLog( $language -> getString( 'user_delete_title_log'), array( 'mod_user_name' => $user_result['user_login']));
								
							}
							
							if ( $_GET['mod_do'] == 'kill_sig' && strlen($user_result['user_signature']) > 0 && $settings['users_can_sigs']){
								
								$user_result['user_signature'] = '';
								
								$mysql -> update( array( 'user_signature' => ''), 'users', "`user_id` = '$user_to_draw'");
								
								/**
								 * log
								 */
								
								$logs -> addModLog( $language -> getString( 'user_delete_sig_log'), array( 'mod_user_name' => $user_result['user_login']));
								
							}
							
						}
						
						/**
						 * user_group
						 */
							
						$user_group_query = $mysql -> query( "SELECT * FROM users_groups WHERE users_group_id = ".$user_result['user_main_group']);							
						
						if($user_group_result = mysql_fetch_array( $user_group_query, MYSQL_ASSOC)){
						
							$user_group_result = $mysql -> clear($user_group_result);
						
						}
						
						/**
						 * add breadcrumb
						 */
										
						$breadcrumb_link['user'] = $user_to_draw;
						
						$language -> setKey( 'member_name', $user_result['user_login']);
						
						$path -> addBreadcrumb( $language -> getString('user_profile'), parent::systemLink( 'users', $breadcrumb_link));
						
						/**
						 * set page title
						 */
						
						$output -> setTitle( $language -> getString( 'user_profile'));
	
						/**
						 * user profile page will be build from 5 blocks, one main, and 4 additional
						 */
						
						$main_block = new form();
						$main_block -> openOpTable( );
						$main_block -> addToContent( '<tr>');
						
						/**
						 * check, if user has got an avatar, and perms allow that
						 */
						
						if( $user_result['user_avatar_type'] != 0 && $settings['users_can_avatars'] && $session -> user['user_show_avatars']){
							
							/**
							 * user has got an avatar, so we will draw profile in two-columns format
							 */
							
							$main_block -> addToContent( '<td class="opt_row1" rowspan="2" style="vertical-align:top">');
							
							/**
							 * draw avatar
							 */
												
							$main_block -> addToContent( $users -> drawAvatar( $user_result['user_avatar_type'], $user_result['user_avatar_image'], $user_result['user_avatar_width'], $user_result['user_avatar_height']));
							
							$main_block -> addToContent('</td>');
							
						}
						
						/**
						 * right side of table will contain user basic data
						 */
						
						$main_block -> addToContent('<td class="opt_row2" width="100%" style="vertical-align:top">');
						
						/**
						 * draw user name
						 */
						
						$main_block -> addToContent( '<h1>'.$user_result['user_login'].'</h1>');
						
						/**
						 * user rank now
						 */
						
						if ( strlen( $user_result['user_custom_title']) > 0 && $settings['users_posts_to_title'] > 0 && $user_result['user_posts_num'] >= $settings['users_posts_to_title']){
							
							$main_block -> addToContent( $user_result['user_custom_title']);
							
						}else if ( strlen( $user_group_result['users_group_title']) > 0){
							
							$main_block -> addToContent( $user_group_result['users_group_title']);
							
						}else{
							
							$main_block -> addToContent( $users -> drawRankName( $user_result['user_posts_num']));
												
						}
						
						if ( strlen( $user_group_result[ 'users_group_image']) > 0){
							
							$user_group_result[ 'users_group_image'] = str_replace( '{S:P}', $style -> style['path'], $user_group_result[ 'users_group_image']);
							$user_group_result[ 'users_group_image'] = str_replace( '{S:#}', $style -> style_id, $user_group_result[ 'users_group_image']);
			
							$main_block -> addToContent( '<br /><img src="'.$user_group_result[ 'users_group_image'].'" alt="" title="" /><br /><br />');
						}else{
							$main_block -> addToContent( '<br />'.$users -> drawRankImage( $user_result['user_posts_num']).'<br /><br />');
						}
							
						/**
						 * and rows containing user group and reg date
						 */
						
						$info_fiels[ $language -> getString( 'user_group')] = $user_group_result['users_group_prefix'].$user_group_result['users_group_name'].$user_group_result['users_group_suffix'];
						$info_fiels[ $language -> getString( 'user_registration')] = $time -> drawDate($user_result['user_regdate']);
						
						foreach ( $info_fiels as $field_title => $field_value){
							
							$main_block -> addToContent( '<b>'.$field_title.':</b> '.$field_value.'<br />');
							
						}
						
						if ( $settings['reputation_turn']){
							
							$main_block -> addToContent( '<b>'.$language -> getString( 'user_reputation').':</b> '.$users -> drawReputation( $users -> countReputation( $user_result['user_rep'], $user_result['user_posts_num'], $user_result['user_regdate'])).'<br />');
						
						}
						
						/**
						 * warns
						 */
								
						if ( $settings['warns_turn'] && $posts_result['post_author'] != -1){
							
							/**
							 * check if we can see warns
							 */
							
							if ( $settings['warns_show'] == 0 || ( $settings['warns_show'] == 1 && $session -> user['user_id'] != -1) || ( $settings['warns_show'] == 2 && ($session -> user['user_can_be_mod'] || ($session -> user['user_id'] != -1 && $session -> user['user_id'] == $user_to_draw)))){
							
								/**
								 * draw warns
								 */
								
								$post_draw_parts['warns'] = true;
								
								$post_draw_strings['PROFILE_WARNS'] = $language -> getString( 'user_warns');							
								
								if ( $user_result['user_warns'] > 0){
									
									$warns_link_open = '<a href="'.parent::systemLink( 'user_warns', array( 'user' => $user_to_draw)).'">';
									$warns_link_close = '</a>';
									
								}else{
									
									$warns_link_open = '';
									$warns_link_close = '';
									
								}
								
								/**
								 * draw warns, or wanrs + mod?
								 */
																
								if ( $session -> user['user_can_be_mod']){
									
									$main_block -> addToContent( '<b>'.$language -> getString( 'user_warns').':</b> <a href="'.parent::systemLink( 'mod', array( 'user' => $user_to_draw, 'd' => '1')).'" title="'.$language -> getString( 'user_warn_decrease').'">'.$style -> drawImage( 'minus').'</a> '.$warns_link_open.$users -> drawWarnLevel( $user_result['user_warns']).$warns_link_close.' <a href="'.parent::systemLink( 'mod', array( 'user' => $user_to_draw, 'd' => '0')).'" title="'.$language -> getString( 'user_warn_add').'">'.$style -> drawImage( 'plus').'</a>');
									
								}else{
									
									$main_block -> addToContent( '<b>'.$language -> getString( 'user_warns').':</b> '.$warns_link_open.$users -> drawWarnLevel( $user_result['user_warns']).$warns_link_close);
																
								}
								
							}
							
						}
						
						/**
						 * admin user
						 */
						
						if ( $session -> user['user_can_be_mod'] || $session -> user['user_can_be_admin']){
							$main_block -> addToContent( '<hr />');
						
							$user_tools = array();
							
							/**
							 * show warns
							 */
							
							if ( $session -> user['user_can_be_mod'])
								$user_tools[] = '<a href="'.parent::systemLink( 'user_warns', array( 'user' => $user_to_draw)).'">'.$language -> getString( 'user_show_warns').'</a>';
							
							/**
							 * delete title
							 */
							
							if ( $session -> user['user_can_be_mod'] && strlen( $user_result['user_custom_title']) > 0 && $settings['users_posts_to_title'] > 0 && $user_result['user_posts_num'] >= $settings['users_posts_to_title'])
								$user_tools[] = '<a href="'.parent::systemLink( parent::getId(), array( 'user' => $user_to_draw, 'mod_do' => 'kill_title')).'">'.$language -> getString( 'user_delete_title').'</a>';
							
							/**
							 * delete signature
							 */
							
							if ( $session -> user['user_can_be_mod'] && strlen( $user_result['user_signature']) > 0 && $settings['users_can_sigs'])
								$user_tools[] = '<a href="'.parent::systemLink( parent::getId(), array( 'user' => $user_to_draw, 'mod_do' => 'kill_sig')).'">'.$language -> getString( 'user_delete_sig').'</a>';
							
							/**
							 * delete avatar
							 */
							
							if ( $session -> user['user_can_be_mod'] && $user_result['user_avatar_type'] != 0)
								$user_tools[] = '<a href="'.parent::systemLink( parent::getId(), array( 'user' => $user_to_draw, 'mod_do' => 'kill_avatar')).'">'.$language -> getString( 'user_delete_avatar').'</a>';
							
							/**
							 * add admin user
							 */
							
							if ( $session -> user['user_can_be_admin'])
								$user_tools[] = '<a href="'.parent::adminLink( 'find_user', array( 'section' => 'management', 'user' => $user_to_draw)).'">'.$language -> getString( 'user_administrate').'</a>';
							
							/**
							 * draw tools
							 */
									
							$main_block -> addToContent( join( "<br />", $user_tools));
								
						}
						
						$main_block -> addToContent( '</td></tr>');
						$main_block -> closeTable();
						
						/**
						 * contact row
						 */
						
						$main_block -> drawSpacer( $language -> getString( 'user_profile_contact'));
						$main_block -> openOpTable( true);
						
						$main_block -> addToContent( '<tr>
							<td class="opt_row1">');
						
						/**
						 * pm
						 */
						
						$send_pw_link = array( 'do' => 'new_pm', 'user' => $user_result['user_id']);
						
						$main_block -> addToContent( $style -> drawImage( 'icon_mail').' <b>'.$language -> getString( 'user_pm').':</b> <a href="'.parent::systemLink( 'profile', $send_pw_link).'">'.$language -> getString( 'user_pm_send').'</a>');
						
						
						$main_block -> addToContent( '</td><td class="opt_row1">');
						
						if ( $user_result['user_want_mail'] && $session -> user['user_can_send_mails']){
							
							if ( $user_result['user_show_mail']){
							
								/**
								 * user show his mail, draw it
								 */
								
								$send_mail_link = 'mailto:'.$user_result['user_mail'];
								
							}else{
								
								/**
								 * user not shows his mail, we have to send it "round the way"
								 */
								
								$mail_user_target = array( 'user' => $user_result['user_id']);
								
								$send_mail_link = parent::systemLink( 'mail_user', $mail_user_target);
								
							}
							
							$main_block -> addToContent( $style -> drawImage( 'icon_mail').' <b>'.$language -> getString( 'user_mail').': </b>'.'<a href="'.$send_mail_link.'">'.$language -> getString( 'user_mail_send').'</a>');
						
						}else{
							
							$main_block -> addToContent( $style -> drawImage( 'icon_mail').' <b>'.$language -> getString( 'user_mail').': </b><i>'.$language -> getString( 'user_no_info').'</i>');
						
						}
						
						$main_block -> addToContent( '</td><td class="opt_row1">');
						
						/**
						 * jabber cell
						 */
						
						if ( !empty( $user_result['user_jabber_id'])){
							$main_block -> addToContent( $style -> drawImage( 'icon_jabber').' <b>'.$language -> getString( 'user_jabber_id').': </b>'.$user_result['user_jabber_id']);
						}else{
							$main_block -> addToContent( $style -> drawImage( 'icon_jabber').' <b>'.$language -> getString( 'user_jabber_id').': </b><i>'.$language -> getString( 'user_no_info').'</i>');
						}
						
						$main_block -> addToContent( '</td></tr>');
						
						
						/**
						 * close table
						 */
						
						$main_block -> closeTable();
						
						/**
						 * and draw signature
						 */
						
						if ( strlen( $user_result['user_signature']) > 0 && $session -> user['user_show_sigs'] && $settings['users_can_sigs']){
							
							$main_block -> drawSpacer( $language -> getString( 'user_profile_signature'));
							$main_block -> openOpTable();
							$main_block -> drawRow( $strings -> parseBB( nl2br($user_result['user_signature']), $settings['users_allow_bbcodes_in_sigs'], $settings['users_allow_emoticones_in_sigs']));
							$main_block -> closeTable();
						}
						
						parent::draw( $style -> drawFormBlock( $language -> getString( 'user_profile'), $main_block -> display() ));										
						
						/**
						 * now break table, and open new colummn
						 */
											
						parent::draw( '</td><td style="padding-left: 3px; vertical-align: top">');
												
						$user_activity_info = new form();
						$user_activity_info -> openOpTable();
											
						/**
						 * posts counter
						 */
						
						if ( $user_result['user_posts_num'] != 0 && (time() - $user_result['user_regdate']) > (24*3600)){
							
							$posts_pd = round( ((time() - $user_result['user_regdate'])/(24*3600)) / $user_result['user_posts_num'], 2);
							
							$language -> setKey( 'post_per_day', $posts_pd);
						
						}else{
						
							$language -> setKey( 'post_per_day', 0);
						
						}
						
						if ( $settings['board_posts_total'] != 0){
							$language -> setKey( 'post_total', round( ($user_result['user_posts_num']*100/$settings['board_posts_total']), 0));
						}else{
							$language -> setKey( 'post_total', 0);
						}
						
						$user_activity_info -> drawInfoRow( $language -> getString( 'user_posts'), $user_result['user_posts_num'].'<br />'.$language -> getString( 'user_posts_info').'<br /><a href="'.parent::systemLink( 'search', array( 'do' => 'users_posts', 'user' => $user_to_draw)).'">'.$language -> getString( 'user_show_posts').'</a>');					
						
						/**
						 * last login and visits number
						 */
										
						if ( $user_result['user_last_login'] > 0){
							$user_activity_info -> drawInfoRow( $language -> getString( 'user_lastlog'), $time -> drawDate($user_result['user_last_login']));	
						}else{
							$user_activity_info -> drawInfoRow( $language -> getString( 'user_lastlog'), '<i>'.$language -> getString( 'time_never').'</i>');	
						}
						/**
						 * user status
						 */
						
						if ( $settings['users_count_online']){
							
							$user_status = 0;
							
							if( $users -> checkOnLine( $user_to_draw)){
								
								$user_status = 1;
								
							}
							
							$user_activity_info -> drawInfoRow( $language -> getString( 'user_status'), $style -> drawStatus( $user_status).' '.$language -> getString( 'user_status_'.$user_status));	
							
						}
						
						$user_activity_info -> closeTable();
						parent::draw( $style -> drawFormBlock( $language -> getString( 'user_profile_activity'), $user_activity_info -> display() ));										
						
						/**
						 * now informations
						 */
							
						$informations_tab = new form();
						$informations_tab -> openOpTable();
												
						if ( !empty( $user_result['user_name'])){
							$informations_tab -> drawInfoRow( $this -> drawRowTitle( 'user_name', $language -> getString( 'user_name')), $this -> drawRowContent( 'user_name', $user_result['user_name']));
						}else{
							$informations_tab -> drawInfoRow( $this -> drawRowTitle( 'user_name', $language -> getString( 'user_name')), $this -> drawRowContent( 'user_name', '<i>'.$language -> getString( 'user_no_info').'</i>'));
						}
						
						if ( !empty( $user_result['user_web'])){
							
							if ( substr( $user_result['user_web'], 0, 7) != "http://")
								$user_result['user_web'] = "http://".$user_result['user_web'];
							
							$informations_tab -> drawInfoRow( $this -> drawRowTitle( 'user_web', $language -> getString( 'user_www')), $this -> drawRowContent( 'user_web', '<a href="'.$user_result['user_web'].'" target="_blank">'.$user_result['user_web'].'</a>'));
						
						}else{
							$informations_tab -> drawInfoRow( $this -> drawRowTitle( 'user_web', $language -> getString( 'user_www')), $this -> drawRowContent( 'user_web', '<i>'.$language -> getString( 'user_no_info').'</i>'));
						}
						
						if ( strlen($user_result['user_birth_date']) > 0){
							
							$birth_date = split("-", $user_result['user_birth_date']);
							
							$informations_tab -> drawInfoRow( $this -> drawRowTitle( 'user_birth_date', $language -> getString( 'user_birth')), $this -> drawRowContent( 'user_birth_date', $time -> translateDate( date( "l, j F Y", mktime( 1, 1, 1, $birth_date[1], $birth_date[0], $birth_date[2])))));
						}else{
							$informations_tab -> drawInfoRow( $this -> drawRowTitle( 'user_birth_date', $language -> getString( 'user_birth')), $this -> drawRowContent( 'user_birth_date', '<i>'.$language -> getString( 'user_no_info').'</i>'));
						}
						
						if ( !empty( $user_result['user_localisation'])){
							$informations_tab -> drawInfoRow( $this -> drawRowTitle( 'user_localisation', $language -> getString( 'user_localisation')), $this -> drawRowContent( 'user_localisation', $user_result['user_localisation']));
						}else{
							$informations_tab -> drawInfoRow( $this -> drawRowTitle( 'user_localisation', $language -> getString( 'user_localisation')), $this -> drawRowContent( 'user_localisation', '<i>'.$language -> getString( 'user_no_info').'</i>'));
						}
						
						$informations_tab -> drawInfoRow(  $this -> drawRowTitle( 'user_gender', $language -> getString( 'user_gender')), $this -> drawRowContent( 'user_gender', $language -> getString( 'gender_'.$user_result['user_gender'])));
						
						if ( !empty( $user_result['user_interests'])){
							$informations_tab -> drawInfoRow( $language -> getString( 'user_interests'), $strings -> parseBB( nl2br( $user_result['user_interests'])));
						}else{
							$informations_tab -> drawInfoRow( $language -> getString( 'user_interests'), '<i>'.$language -> getString( 'user_no_info').'</i>');
						}
						
						/**
						 * custom profile fields
						 */
						
						foreach ( $users -> custom_fields as $field_id => $field_ops){
							
							/**
							 * check if we can see field there
							 */
							
							if ( !$field_ops['profile_field_private'] || ( $field_ops['profile_field_private'] && ( $session -> user['user_id'] == $user_to_draw || $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate']))){
								
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
								
								if ( strlen( $user_result['field_'.$field_id]) == 0){
									
									if ( !$field_ops['profile_field_byteam'] || $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate']){
											
										$informations_tab -> drawInfoRow( $this -> drawRowTitle( 'field_'.$field_id, $field_ops['profile_field_name']), $this -> drawRowContent( 'field_'.$field_id, '<i>'.$language -> getString( 'user_no_info').'</i>'));
									
									}else{
									
										$informations_tab -> drawInfoRow( $field_ops['profile_field_name'], '<i>'.$language -> getString( 'user_no_info').'</i>');
										
									}
									
								}else{
								
									switch ( $field_ops['profile_field_type']){
									
										case 0:
									
											if ( !$field_ops['profile_field_require'] && (!$field_ops['profile_field_byteam'] && $session -> user['user_id'] == $user_to_draw) || $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate']){
											
												$informations_tab -> drawInfoRow( $this -> drawRowTitle( 'field_'.$field_id, $field_ops['profile_field_name']), $this -> drawRowContent( 'field_'.$field_id, $user_result['field_'.$field_id]));
											
											}else{
												
												$informations_tab -> drawInfoRow( $field_ops['profile_field_name'], $user_result['field_'.$field_id]);
											
											}
											
										break;
									
										case 1:
											
											if ( !$field_ops['profile_field_require'] && (!$field_ops['profile_field_byteam'] && $session -> user['user_id'] == $user_to_draw) || $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate']){
											
												$informations_tab -> drawInfoRow( $this -> drawRowTitle( 'field_'.$field_id, $field_ops['profile_field_name']), $this -> drawRowContent( 'field_'.$field_id, nl2br( $user_result['field_'.$field_id])));
											
											}else{
												
												$informations_tab -> drawInfoRow( $field_ops['profile_field_name'], nl2br( $user_result['field_'.$field_id]));
											
											}
											
										break;
										
										case 2:
											
											if ( !$field_ops['profile_field_require'] && (!$field_ops['profile_field_byteam'] && $session -> user['user_id'] == $user_to_draw) || $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate']){
											
												$informations_tab -> drawInfoRow( $this -> drawRowTitle( 'field_'.$field_id, $field_ops['profile_field_name']), $this -> drawRowContent( 'field_'.$field_id, $made_options[$user_result['field_'.$field_id]]));
											
											}else{
												
												$informations_tab -> drawInfoRow( $field_ops['profile_field_name'], $made_options[$user_result['field_'.$field_id]]);
											
											}
																					
										break;
									}
									
								}
												
							}
							
						}
						
						/**
						 * close table
						 */
						
						$informations_tab -> closeTable();
						
						parent::draw( $style -> drawFormBlock( $language -> getString( 'user_profile_info'), $informations_tab -> display() ));										
						
						/**
						 * and close table
						 */
						
						parent::draw("</td></tr></table>");
						
						/**
						 * editing fields java
						 */
									
						if ( $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate'] || $session -> user['user_id'] == $user_to_draw){
							
							parent::draw( '<script language="JavaScript" type="text/javascript">
							
								function loadSimpleEdit( field){
								
									//get loader id
									field_loader = document.getElementById( field + \'_loader\');
								
									//get field id
									field_content = document.getElementById( field + \'_content\');
														
									//new ajax object
									uniAJAX = GetXmlHttpObject();
								
									uniAJAX.onreadystatechange = function(){
									
										if(uniAJAX.readyState == 4){
											
											//clear loader
											field_loader.innerHTML = "";
																				
											//write new content
											field_content.innerHTML = uniAJAX.responseText;
											
										}else{
										
											//set loader
											field_loader.innerHTML = "'.str_replace( '"', '\"', $style -> drawImage( 'small_loader')).' ";
										
										}
									}
																		
									uniAJAX.open( "GET", "'.parent::systemLink( parent::getId(), array( 'smode' => 2, 'user' => $user_to_draw, 'do' => 0)).'&field=" + field , true);
									uniAJAX.send( null);
									
								}
							
								function saveSimpleField( field){
																
									//get loader id
									field_loader = document.getElementById( field + \'_loader\');
								
									//get field id
									field_content = document.getElementById( field + \'_content\');
														
									//get editor id
									field_editor = document.getElementById( field + \'_editor\');
									
									//new ajax object
									uniAJAX = GetXmlHttpObject();
								
									uniAJAX.onreadystatechange = function(){
									
										if(uniAJAX.readyState == 4){
											
											//clear loader
											field_loader.innerHTML = "";
																				
											//write new content
											field_content.innerHTML = uniAJAX.responseText;
											
										}else{
										
											//set loader
											field_loader.innerHTML = "'.str_replace( '"', '\"', $style -> drawImage( 'small_loader')).' ";
										
										}
									}
																		
									uniAJAX.open( "POST", "'.parent::systemLink( parent::getId(), array( 'smode' => 2, 'user' => $user_to_draw, 'do' => 1)).'&field=" + field , true);
									uniAJAX.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
										
									if( field == "user_birth_date"){
																		
										//get editor id
										field_editor_day = document.getElementById( field + \'_editor_day\');
										field_editor_month = document.getElementById( field + \'_editor_month\');
										field_editor_year = document.getElementById( field + \'_editor_year\');
										
										uniAJAX.send( "birth_day=" + encodeURIComponent( field_editor_day.value) + "&birth_month=" + encodeURIComponent( field_editor_month.value) + "&birth_year=" + encodeURIComponent( field_editor_year.value));
										
									}else{
									
										uniAJAX.send( "text=" + encodeURIComponent( field_editor.value));
									
									}
								}
								
								function cancelSimpleField( field){
								
									//get loader id
									field_loader = document.getElementById( field + \'_loader\');
								
									//get field id
									field_content = document.getElementById( field + \'_content\');
														
									//new ajax object
									uniAJAX = GetXmlHttpObject();
								
									uniAJAX.onreadystatechange = function(){
									
										if(uniAJAX.readyState == 4){
											
											//clear loader
											field_loader.innerHTML = "";
																				
											//write new content
											field_content.innerHTML = uniAJAX.responseText;
											
										}else{
										
											//set loader
											field_loader.innerHTML = "'.str_replace( '"', '\"', $style -> drawImage( 'small_loader')).' ";
										
										}
									}
																		
									uniAJAX.open( "GET", "'.parent::systemLink( parent::getId(), array( 'smode' => 2, 'user' => $user_to_draw, 'do' => 2)).'&field=" + field , true);
									uniAJAX.send( null);
									
								}
								
							</script>');
							
						}
								
					}else{
					
						/**
						 * profile ajax
						 */
						
						if ( $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate'] || $session -> user['user_id'] == $user_to_draw){
							
							switch ( $_GET['do']){
								
								/**
								 * what to do and send for ajax?
								 */
								
								case 0:
									
									/**
									 * draw and send simple editor
									 */
									
									$field = $_GET['field'];
									
									if ( $field == 'user_gender'){
										
										$gender_list = '<select id="'.$field.'_editor" name="'.$field.'_field">
											<option value="0">'.$language -> getString( 'gender_0').'</value>
											<option value="1">'.$language -> getString( 'gender_1').'</value>
											<option value="2">'.$language -> getString( 'gender_2').'</value>
										</select>';
										
										$gender_list = str_replace( 'value="'.$user_result['user_gender'].'"', 'value="'.$user_result['user_gender'].'" selected', $gender_list );
										
										$editor_html = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
										  <tr>
										    <td style="width: 100%">'.$gender_list.'</td>
										    <td nowrap="nowrap"><a href="javascript:saveSimpleField( \''.$field.'\')">'.$style -> drawImage( 'small_save').'</a> <a href="javascript:cancelSimpleField( \''.$field.'\')">'.$style -> drawImage( 'small_delete').'</a></td>
										  </tr>
										</table>';
									
									}else if ( $field == 'user_birth_date') {
										
										$user_acutal_birth = split( "-", $user_result['user_birth_date']);
										
										$editor_html = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
										  <tr>
										    <td style="width: 100%"><input id="user_birth_date_editor_day" name="user_birth_date_editor_day" type="text" size="2" maxlength="2" value="'.$user_acutal_birth[0].'"/> - <input id="user_birth_date_editor_month" name="user_birth_date_editor_month" type="text" size="2" maxlength="2" value="'.$user_acutal_birth[1].'"/> - <input id="user_birth_date_editor_year" name="user_birth_date_editor_year" type="text" size="4" maxlength="4" value="'.$user_acutal_birth[2].'"/></td>
										    <td nowrap="nowrap"><a href="javascript:saveSimpleField( \''.$field.'\')">'.$style -> drawImage( 'small_save').'</a> <a href="javascript:cancelSimpleField( \''.$field.'\')">'.$style -> drawImage( 'small_delete').'</a></td>
										  </tr>
										</table>';
										
									}else{
										
										/**
										 * check if we have custom field
										 */
										
										if ( substr( $field, 0, 6) == 'field_'){
											
											/**
											 * detect field id
											 */
											
											$field_id = substr( $field, 6);
											
											/**
											 * check if we can edit this field
											 */
											
											if ( !$users -> custom_fields[$field_id]['profile_field_require'] && (!$users -> custom_fields[$field_id]['profile_field_byteam'] && $session -> user['user_id'] == $user_to_draw) || $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate']){
												
												/**
												 * we can edit field, so draw editor
												 */
												
												switch ( $users -> custom_fields[$field_id]['profile_field_type']){
													
													case 0:
													
														$limit = '';
														
														if ( $users -> custom_fields[$field_id]['profile_field_length'] > 0)
															$limit = 'maxlength="'.$users -> custom_fields[$field_id]['profile_field_length'].'"';
														
														$editor_html = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
																		  <tr>
																		    <td style="width: 100%"><input id="field_'.$field_id.'_editor" name="field_'.$field_id.'_editor" type="text" value="'.$user_result[$field].'" size="25" '.$limit.'/></td>
																		    <td nowrap="nowrap"><a href="javascript:saveSimpleField( \''.$field.'\')">'.$style -> drawImage( 'small_save').'</a> <a href="javascript:cancelSimpleField( \''.$field.'\')">'.$style -> drawImage( 'small_delete').'</a></td>
																		  </tr>
																		</table>';
														
													break;
														
													case 1:
														
														$editor_html = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
																		  <tr>
																		    <td style="width: 100%"><textarea id="field_'.$field_id.'_editor" name="field_'.$field_id.'_editor" style="width: 100%" rows="9">'.$user_result[$field].'</textarea></td>
																		    <td nowrap="nowrap"><a href="javascript:saveSimpleField( \''.$field.'\')">'.$style -> drawImage( 'small_save').'</a> <a href="javascript:cancelSimpleField( \''.$field.'\')">'.$style -> drawImage( 'small_delete').'</a></td>
																		  </tr>
																		</table>';
														
													break;
													
													case 2:
													
														/**
														 * profile options list
														 */
														
														$preparsed_options = split( "\n", $users -> custom_fields[$field_id]['profile_field_options']);
														
														$made_options = array();
														
														$parsed_list = '';
														
														foreach ( $preparsed_options as $preparsed_option) {
															
															$option_id = substr( $preparsed_option, 0, strpos( $preparsed_option, "="));
															$option_value = substr( $preparsed_option, strpos( $preparsed_option, "=") + 1);
															
															if ( $user_result[$field] == $option_id){
																
																$parsed_list .= '<option value="'.$option_id.'" selected>'.$option_value.'</value>';
																
															}else{
																
																$parsed_list .= '<option value="'.$option_id.'">'.$option_value.'</value>';
																
															}
																														
														}
																												
														$editor_html = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
																		  <tr>
																		    <td style="width: 100%"><select id="field_'.$field_id.'_editor" name="field_'.$field_id.'_editor">'.$parsed_list.'</select></td>
																		    <td nowrap="nowrap"><a href="javascript:saveSimpleField( \''.$field.'\')">'.$style -> drawImage( 'small_save').'</a> <a href="javascript:cancelSimpleField( \''.$field.'\')">'.$style -> drawImage( 'small_delete').'</a></td>
																		  </tr>
																		</table>';
														
														
													break;
														
												}
												
											}else{
												
												/**
												 * send back default value and monit
												 */
												
												if ( strlen( $user_result['field_'.$field_id]) == 0){
												
													$editor_html = '<i>'.$language -> getString( 'user_no_info').'</i>';
												
												}else{
													
													switch ( $users -> custom_fields[$field_id]['profile_field_type']){
														
														case 0:
														
															
															$editor_html = $user_result['field_'.$field_id];
															
														break;
															
														case 1:
														
															
															$editor_html = nl2br( $user_result['field_'.$field_id]);
															
														break;
														
														case 2:
														
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
															
															$editor_html = $made_options[$user_result['field_'.$field_id]];
															
														break;
													}
													
												}
											}
											
										}else{
											
											$editor_html = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
											  <tr>
											    <td style="width: 100%"><input id="'.$field.'_editor" name="'.$field.'_field" type="text" value="'.$user_result[$field].'" size="25" /></td>
											    <td nowrap="nowrap"><a href="javascript:saveSimpleField( \''.$field.'\')">'.$style -> drawImage( 'small_save').'</a> <a href="javascript:cancelSimpleField( \''.$field.'\')">'.$style -> drawImage( 'small_delete').'</a></td>
											  </tr>
											</table>';
											
										}
										
									}
									
									parent::draw( $editor_html);
									
								break;
								
								case 1:
									
									/**
									 * update value
									 */
									
									$field = $strings -> inputClear( $_GET['field'], false);
									
									$new_value = $_POST['text'];
										
									$act_tab = 'users';
									$act_field = 'user_id';
															
									if ( $field == 'user_web'){
										
										if ( substr( $new_value, 0, 7) != "http://")
											$draw_value = "http://".$new_value;
											
										$draw_value = '<a href="'.$draw_value.'" target="_blank">'.$draw_value.'</a>';
											
									}else if ( $field == 'user_gender'){
										
										settype( $new_value, 'integer');
										
										if ( $new_value < 0)
											$new_value = 0;
										
										if ( $new_value > 2)
											$new_value = 2;
											
										$draw_value = $language -> getString( 'gender_'.$new_value);	
									
									}else if ( $field == 'user_birth_date'){
											
										$user_birth_day = $_POST['birth_day'];
										$user_birth_month = $_POST['birth_month'];
										$user_birth_year = $_POST['birth_year'];
										
										settype( $user_birth_day, 'integer');
										settype( $user_birth_month, 'integer');
										settype( $user_birth_year, 'integer');
										
										if ( $user_birth_day < 1)
											$user_birth_day = 1;
										
										if ( $user_birth_day > 31)
											$user_birth_day = 31;
											
										if ( $user_birth_month < 1)
											$user_birth_month = 1;
														
										if ( $user_birth_month > 12)
											$user_birth_month = 12;
											
										if ( $user_birth_year < 1890)
											$user_birth_year = 1890;
											
										if ( $user_birth_year > date( "Y"))
											$user_birth_year = date( "Y");
																					
										if ( !empty( $_POST['birth_day']) && !empty( $_POST['birth_month']) && !empty( $_POST['birth_year'])){
											
											$new_value = $user_birth_day.'-'.$user_birth_month.'-'.$user_birth_year;
											$draw_value = $time -> translateDate( date( "l, d F Y", mktime( 1, 1, 1, $user_birth_month, $user_birth_day, $user_birth_year)));
											
										}else{
											
											$new_value = '';
											$draw_value = '<i>'.$language -> getString( 'user_no_info').'</i>';
										}
										
									}else if ( substr( $field, 0, 6) == 'field_') {
										
										/**
										 * we are in custom field
										 * define its id
										 */
												
										$field_id = substr( $field, 6);
										
										if ( !$users -> custom_fields[$field_id]['profile_field_require'] && (!$users -> custom_fields[$field_id]['profile_field_byteam'] && $session -> user['user_id'] == $user_to_draw) || $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate']){
										
											/**
											 * jup, we can edit field
											 */
												
											switch ( $users -> custom_fields[$field_id]['profile_field_type']){
												
												case 0:
													
													/**
													 * thats simple
													 */
													
													$new_value = $strings -> inputClear( $_POST['text'], false);
													
													/**
													 * check limit
													 */
																	
													if ( $users -> custom_fields[$field_id]['profile_field_length'] == 0 || ( $users -> custom_fields[$field_id]['profile_field_length'] > 0 && strlen( $new_value) <= $users -> custom_fields[$field_id]['profile_field_length'])){
														
														$new_value = $strings -> inputClear( $_POST['text'], false);
														$draw_value = $new_value;
														
													}else{
														
														/**
														 * draw old value
														 */
														
														$language -> setKey( 'field_name', $users -> custom_fields[$field_id]['profile_field_name']);
														$language -> setKey( 'field_length', $users -> custom_fields[$field_id]['profile_field_length']);
														
														$new_value = $strings -> inputClear( $user_result['field_'.$field_id], false);
														$draw_value = $new_value.'<br /><b>'.$strings -> inputClear( $language -> getString( 'user_cp_length_error')).'</b>';
																													
													}
																				
												break;
												
												case 1:
													
													/**
													 * thats simple
													 */
													
													$new_value = $strings -> inputClear( $_POST['text'], false);
													
													/**
													 * check limit
													 */
																	
													if ( $users -> custom_fields[$field_id]['profile_field_length'] == 0 || ( $users -> custom_fields[$field_id]['profile_field_length'] > 0 && strlen( $new_value) <= $users -> custom_fields[$field_id]['profile_field_length'])){
														
														$new_value = $strings -> inputClear( $_POST['text'], false);
														$draw_value = $new_value;
														
													}else{
														
														/**
														 * draw old value
														 */
														
														$language -> setKey( 'field_name', $users -> custom_fields[$field_id]['profile_field_name']);
														$language -> setKey( 'field_length', $users -> custom_fields[$field_id]['profile_field_length']);
														
														$new_value = $strings -> inputClear( $user_result['field_'.$field_id], false);
														$draw_value = $new_value.'<br /><b>'.$strings -> inputClear( $language -> getString( 'user_cp_length_error')).'</b>';
																													
													}
																				
												break;
												
												case 2:
												
													/**
													 * profile options list
													 */
													
													$preparsed_options = split( "\n", $users -> custom_fields[$field_id]['profile_field_options']);
													
													$made_options = array();
													
													foreach ( $preparsed_options as $preparsed_option) {
														
														$option_id = substr( $preparsed_option, 0, strpos( $preparsed_option, "="));
														$option_value = substr( $preparsed_option, strpos( $preparsed_option, "=") + 1);
														
														$made_options[$option_id] = $option_value;
																													
													}
															
													$new_value = $strings -> inputClear( $_POST['text'], false);
														
													/**
													 * check if new field is in array
													 */
														
													if ( key_exists( $new_value, $made_options)){
														
														$draw_value = $made_options[$new_value];
														
													}else{
														
														$new_value = addcslashes( $user_result[$field]);
														$draw_value = $made_options[$new_value];
														
													}
																									
												break;
												
											}
																			
											$act_tab = 'profile_fields_data';
											$act_field = 'profile_fields_user';
											
										}
										
										
									}else{
										
										$new_value = $strings -> inputClear( $new_value, false);
										$draw_value = $new_value;
										
									}
									
									$mysql -> update( array( $field => $new_value), $act_tab, "`$act_field` = '$user_to_draw'");
													
									if ( strlen( $new_value) == 0)
										$draw_value = '<i>'.$language -> getString( 'user_no_info').'</i>';
														
									parent::draw( $this -> drawRowContent( $field, stripslashes( $draw_value)));
																		
								break;
								
								case 2:
									
									/**
									 * set orginal
									 */
									
									$field = $_GET['field'];
									
									if ( strlen( $user_result[$field]) > 0){
										
										if ( $field == 'user_web'){
											
											if ( substr( $user_result[$field], 0, 7) != "http://")
												$draw_value = "http://".$user_result[$field];
												
											$draw_value = '<a href="'.$draw_value.'" target="_blank">'.$draw_value.'</a>';
												
										}else if( $field == 'user_gender'){	
										
											$draw_value = $language -> getString( 'gender_'.$user_result[$field]);
										
										}else if ( $field == 'user_birth_date'){
											
												$birth_date = split("-", $user_result['user_birth_date']);
							
												$draw_value = $time -> translateDate( date( "l, d F Y", mktime( 1, 1, 1, $birth_date[1], $birth_date[0], $birth_date[2])));
						
										}else if ( substr( $field, 0, 6) == 'field_') {
											
											/**
											 * we are in custom field
											 * define its id
											 */
												
											$field_id = substr($field, 6);
											
											if ( strlen( $user_result['field_'.$field_id]) == 0){
												
												$draw_value = '<i>'.$language -> getString( 'user_no_info').'</i>';
											
											}else{
												
												switch ( $users -> custom_fields[$field_id]['profile_field_type']){
													
													case 0:
													
														
														$draw_value = $user_result['field_'.$field_id];
														
													break;
														
													case 1:
													
														
														$draw_value = nl2br( $user_result['field_'.$field_id]);
														
													break;
													
													case 2:
													
														/**
														 * profile options list
														 */
														
														$preparsed_options = split( "\n", $users -> custom_fields[$field_id]['profile_field_options']);
														
														$made_options = array();
														
														foreach ( $preparsed_options as $preparsed_option) {
															
															$option_id = substr( $preparsed_option, 0, strpos( $preparsed_option, "="));
															$option_value = substr( $preparsed_option, strpos( $preparsed_option, "=") + 1);
															
															$made_options[$option_id] = $option_value;
															
														}
														
														$draw_value = $made_options[$user_result['field_'.$field_id]];
														
													break;
												}
												
											}
											
										}else{
											
											$draw_value = $user_result[$field];
											
										}
										
										parent::draw( $this -> drawRowContent( $field, $draw_value));
									
									}else{
									
										parent::draw( $this -> drawRowContent( $field, '<i>'.$language -> getString( 'user_no_info').'</i>'));
											
									}
									
									
								break;
							}
							
						}
						
					}
					
				}
			}
			
			/**
			 * user not found
			 */
			
			if( !isset($user_found)){
										
				/**
				 * set page title
				 */
				
				$main_error = new main_error();
				$main_error -> type = 'error';
				$main_error -> message = $language -> getString( 'error_user_notfound');
				parent::draw( $main_error -> display());
							
			}
			
		}else{
			
			/**
			 * draw error
			 */
			
			$main_error = new main_error();
			$main_error -> type = 'information';
			parent::draw( $main_error -> display());
			
		}
		
	}
	
	function drawRowTitle( $id, $title){
		
		global $session;
		
		if ( $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate'] || $session -> user['user_id'] == $this -> user_to_draw){
			
			return '<span id="'.$id.'_loader"></span>'.$title.'';

		}else{
			
			return $title;
			
		}
		
	}
	
	function drawRowContent( $id, $content){
		
		global $language;
		global $session;
		global $style;
		
		if ( $session -> user['user_can_be_admin'] || $session -> user['user_can_moderate'] || $session -> user['user_id'] == $this -> user_to_draw){
			
			return '<div id="'.$id.'_content"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
					    <td style="width: 100%">'.$content.'</td>
					    <td nowrap="nowrap"><a href="javascript:loadSimpleEdit( \''.$id.'\')">'.$style -> drawImage( 'small_edit', $language -> getString( 'edit')).'</a></td>
					  </tr>
					</table></div>';

		}else{
			
			return $content;
			
		}
	}
	
}

?>