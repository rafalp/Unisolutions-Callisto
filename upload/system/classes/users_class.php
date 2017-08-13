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
|	Users Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class users{

	/**
	 * error number
	 *
	 * @var int
	 */
	var $error = 0;
	
	/**
	 * users online
	 * 
	 */
	
	var $users_on_line = array();
	
	/**
	 * spiders online
	 * 
	 */
	
	var $bots_on_line = array();
	
	/**
	 * users ranks
	 * 
	 */
	
	var $users_ranks = array();
	
	/**
	 * users groups
	 * 
	 */
	
	var $users_groups = array();
	
	/**
	 * custom profile fields
	 * 
	 */
	
	var $custom_fields = array();
	
	/**
	 * members online
	 */
	
	var $members_online_num = 0;
	
	/**
	 * hidden online
	 */
	
	var $hidden_online_num = 0;
	
	/**
	 * guests_online
	 */
	
	var $guests_online_num = 0;
	
	/**
	 * class construction
	 *
	 */
	
	function __construct(){
		
		global $settings;
		global $mysql;
		global $cache;
				
		/**
		 * build list of ranks
		 */
		
		$users_ranks_from_cache = $cache -> loadCache( 'users_ranks');
		
		if( gettype( $users_ranks_from_cache) != 'array'){
			
			$ranks_query = $mysql -> query( "SELECT * FROM ranks ORDER BY `rank_posts_required`");
			while ( $ranks_result = mysql_fetch_array( $ranks_query, MYSQL_ASSOC)) {
				
				$ranks_result = $mysql -> clear( $ranks_result);
				
				$this -> users_ranks[$ranks_result['rank_id']] = array(
					'rank_name' => $ranks_result['rank_name'],
					'rank_posts_required' => $ranks_result['rank_posts_required'],
					'rank_image' => $ranks_result['rank_image'],
					'rank_stars' => $ranks_result['rank_stars'],
				);
				
			}
			
			$cache -> saveCache( 'users_ranks', $this -> users_ranks);
			
		}else{
			
			$this -> users_ranks = $users_ranks_from_cache;
			
		}
		
		/**
		 * rebuild list of groups
		 */
				
		$users_groups_from_cache = $cache -> loadCache( 'users_groups');
		
		if( gettype( $users_groups_from_cache) != 'array'){
			
			$groups_query = $mysql -> query( "SELECT * FROM users_groups ORDER BY users_group_can_use_acp DESC, users_group_can_moderate DESC, users_group_name");
			while ( $groups_result = mysql_fetch_array( $groups_query, MYSQL_ASSOC)) {
				
				$groups_result = $mysql -> clear($groups_result);
				
				$this -> users_groups[$groups_result['users_group_id']] = array(
					'users_group_name' => $groups_result['users_group_name'],
					'users_group_prefix' => $groups_result['users_group_prefix'],
					'users_group_suffix' => $groups_result['users_group_suffix'],
					'users_group_title' => $groups_result['users_group_title'],
					'users_group_image' => $groups_result['users_group_image'],
					'users_group_message_title' => $groups_result['users_group_msg_title'],
					'users_group_message' => $groups_result['users_group_message'],
					'users_group_hidden' => $groups_result['users_group_hidden'],
					'users_group_permissions' => $groups_result['users_group_permissions'],
					'users_group_system' => $groups_result['users_group_system'],
					'users_group_can_use_acp' => $groups_result['users_group_can_use_acp'],
					'users_group_can_see_closed_page' => $groups_result['users_group_can_see_closed_page'],
					'users_group_can_see_users_profiles' => $groups_result['users_group_can_see_users_profiles'],
					'users_group_can_use_pm' => $groups_result['users_group_can_use_pm'],
					'users_group_pm_limit' => $groups_result['users_group_pm_limit'],
					'users_group_can_email_members' => $groups_result['users_group_can_email_members'],
					'users_group_can_moderate' => $groups_result['users_group_can_moderate'],
					'users_group_can_edit_calendar' => $groups_result['users_group_can_edit_calendar'],
					'users_group_shoutbox_access' => $groups_result['users_group_shoutbox_access'],
					'users_group_edit_time_limit' => $groups_result['users_group_edit_time_limit'],
					'users_group_draw_edit_legend' => $groups_result['users_group_draw_edit_legend'],
					'users_group_delete_own_topics' => $groups_result['users_group_delete_own_topics'],
					'users_group_change_own_topics' => $groups_result['users_group_change_own_topics'],
					'users_group_close_own_topics' => $groups_result['users_group_close_own_topics'],
					'users_group_delete_own_posts' => $groups_result['users_group_delete_own_posts'],
					'users_group_edit_own_posts' => $groups_result['users_group_edit_own_posts'],
					'users_group_start_surveys' => $groups_result['users_group_name'],
					'users_group_vote_surveys' => $groups_result['users_group_vote_surveys'],
					'users_group_avoid_flood' => $groups_result['users_group_avoid_flood'],
					'users_group_avoid_badwords' => $groups_result['users_group_avoid_badwords'],
					'users_group_avoid_closed_topics' => $groups_result['users_group_avoid_closed_topics'],
					'users_group_promote_to' => $groups_result['users_group_promote_to'],
					'users_group_promote_at' => $groups_result['users_group_promote_at'],
					'users_group_see_hidden' => $groups_result['users_group_see_hidden'],
					'users_group_search' => $groups_result['users_group_search'],
					'users_group_search_limit' => $groups_result['users_group_search_limit'],
					'users_group_uploads_quota' => $groups_result['users_group_uploads_quota'],
					'users_group_uploads_used' => $groups_result['users_group_uploads_used'],
					'users_group_uploads_limit' => $groups_result['users_group_uploads_limit']
				);
				
			}
			
			$cache -> saveCache( 'users_groups', $this -> users_groups);
			
		}else{
			
			$this -> users_groups = $users_groups_from_cache;
			
		}
		
		/**
		 * rebuild list of fields
		 */
				
		$profile_fields_from_cache = $cache -> loadCache( 'profile_fields');
		
		if( gettype( $profile_fields_from_cache) != 'array'){
			
			$fields_query = $mysql -> query( "SELECT * FROM profile_fields ORDER BY profile_field_pos");
			while ( $fields_result = mysql_fetch_array( $fields_query, MYSQL_ASSOC)) {
				
				$fields_result = $mysql -> clear($fields_result);
				
				$this -> custom_fields[$fields_result['profile_field_id']] = array(
					'profile_field_name' => $fields_result['profile_field_name'],
					'profile_field_info' => $fields_result['profile_field_info'],
					'profile_field_type' => $fields_result['profile_field_type'],
					'profile_field_length' => $fields_result['profile_field_length'],
					'profile_field_options' => $fields_result['profile_field_options'],
					'profile_field_onregister' => $fields_result['profile_field_onregister'],
					'profile_field_onlist' => $fields_result['profile_field_onlist'],
					'profile_field_inposts' => $fields_result['profile_field_inposts'],
					'profile_field_require' => $fields_result['profile_field_require'],
					'profile_field_private' => $fields_result['profile_field_private'],
					'profile_field_byteam' => $fields_result['profile_field_byteam'],
					'profile_field_display' => htmlspecialchars_decode($fields_result['profile_field_display'])
				);
				
			}
			
			$cache -> saveCache( 'profile_fields', $this -> custom_fields);
			
		}else{
			
			$this -> custom_fields = $profile_fields_from_cache;
			
		}
		
		/**
		 * build list of reps
		 */
		
		$users_reps_from_cache = $cache -> loadCache( 'users_reps');
		
		if( gettype( $users_reps_from_cache) != 'array'){
			
			$reps_query = $mysql -> query( "SELECT * FROM reputation_scale ORDER BY `reputation_scale_points`, `reputation_scale_name`");
			while ( $reps_result = mysql_fetch_array( $reps_query, MYSQL_ASSOC)) {
				
				$reps_result = $mysql -> clear( $reps_result);
				
				$this -> users_reps[$reps_result['reputation_scale_id']] = array(
					'reputation_scale_name' => $reps_result['reputation_scale_name'],
					'reputation_scale_points' => $reps_result['reputation_scale_points']
				);
				
			}
			
			$cache -> saveCache( 'users_reps', $this -> users_reps);
			
		}else{
			
			$this -> users_reps = $users_reps_from_cache;
			
		}
		
	}
	
	function buildOnlines(){
				
		global $settings;
		global $mysql;
		global $cache;
		
		/**
		 * select vars from settings
		 */
		
		$this -> members_online_num = $settings['users_online'];
		$this -> hidden_online_num = $settings['users_online_hidden'];
		$this -> guests_online_num = $settings['users_guests_online'];
				
		/**
		 * rebuild list of online
		 */
		
		if ( $settings['users_count_online']){
			
			$members_online = 0;
			$hidden_online = 0;
			$guests_online = 0;
			
			$users_online_from_cache = $cache -> loadCache( 'users_online');
			$bots_online_from_cache = $cache -> loadCache( 'spiders_online');
			
			if( gettype( $users_online_from_cache) != 'array' || gettype( $bots_online_from_cache) != 'array'){
				
				$sessions_query = $mysql -> query( "SELECT * FROM users_sessions");
				while ( $sessions_result = mysql_fetch_array( $sessions_query, MYSQL_ASSOC)) {
					
					if ( $sessions_result['users_session_user_id'] != -1){

						$this -> users_on_line[$sessions_result['users_session_user_id']] = $sessions_result['users_session_hidden'];
						
						if ( $sessions_result['users_session_hidden'] ){
							
							$hidden_online ++;
						
						}else{
							
							$members_online ++;
							
						}
					
					}else if ( $sessions_result['users_session_bot']){
							
						$this -> bots_on_line[] = $sessions_result['users_session_bot_name'];
						
						$guests_online ++;
						
					}else{
						
						$guests_online ++;
						
					}
				}
				
				$cache -> saveCache( 'users_online', $this -> users_on_line, $settings['session_recount_time'] * 60);
				$cache -> saveCache( 'spiders_online', $this -> bots_on_line, $settings['session_recount_time'] * 60);
				$cache -> flushCache( 'system_settings');
				
				$this -> members_online_num = $members_online;
				$this -> hidden_online_num = $hidden_online;
				$this -> guests_online_num = $guests_online;
				
				$update_members_query = array( 'setting_value' => $members_online);
				$mysql -> update( $update_members_query, 'settings', "`setting_setting` = 'users_online'");
				
				$update_members_query = array( 'setting_value' => $hidden_online);
				$mysql -> update( $update_members_query, 'settings', "`setting_setting` = 'users_online_hidden'");
				
				$update_members_query = array( 'setting_value' => $guests_online);
				$mysql -> update( $update_members_query, 'settings', "`setting_setting` = 'users_guests_online'");
				
				/**
				 * check record
				 */
				
				if ( ($members_online + $hidden_online) > $settings['record_number']){
					
					/**
					 * update record too
					 */
					
					$update_record_query = array( 'setting_value' => ($members_online + $hidden_online));
					$mysql -> update( $update_record_query, 'settings', "`setting_setting` = 'record_number'");
					
					$update_record_query = array( 'setting_value' => time());
					$mysql -> update( $update_record_query, 'settings', "`setting_setting` = 'record_time'");
					
				}
				
			}else{
				
				$this -> users_on_line = $users_online_from_cache;
				$this -> bots_on_line = $bots_online_from_cache;
				
			}
			
		}
		
	}
	
	/**
	 * fixes paths
	 */
	
	function fixPaths(){
		
		global $style;
		
		foreach ($this -> users_groups as $group_id => $group_ops) {
			
			$this -> users_groups[ $group_id]['users_group_image'] = str_replace( '{S:P}', $style -> style['path'], $this -> users_groups[ $group_id]['users_group_image']);
			$this -> users_groups[ $group_id]['users_group_image'] = str_replace( '{S:#}', $style -> style_id, $this -> users_groups[ $group_id]['users_group_image']);
			
		}
		
	}
	
	/**
	 * check if user is online
	 *
	 * @param unknown_type $user_id
	 * @return unknown
	 */
	
	function checkOnline( $user_id){
		
		global $session;
		
		if ( key_exists( $user_id, $this -> users_on_line)){
			
			/**
			 * key exists, check if it is hidden
			 */
			
			if ( ($this -> users_on_line[$user_id] && $session -> user['user_see_hidden']) || $user_id == $session -> user['user_id'] || !$this -> users_on_line[$user_id] ){
			
				return true;
			
			}else{
				
				return false;
			}
			
		}else{
			
			return false;
			
		}
		
	}
	
	function drawRankName( $target){
		
		global $style;
		
		/**
		 * cycle trought ranks
		 */
		
		$user_rank = 0;
		
		foreach ( $this -> users_ranks as $rank_id => $rank_type){
			
			if ( $rank_type['rank_posts_required'] <= $target){
				
				$user_rank = $rank_id;
				
			}
			
		}
			
		return $this -> users_ranks[$user_rank]['rank_name'];
		
	}
	
	function drawRankImage( $target){
		
		global $style;
		
		/**
		 * cycle trought ranks
		 */
		
		$user_rank = 0;
		
		foreach ( $this -> users_ranks as $rank_id => $rank_type){
			
			if ( $rank_type['rank_posts_required'] <= $target){
				
				$user_rank = $rank_id;
				
			}
			
		}
		
		/**
		 * lets define rank image
		 */
		
		$rank_image = $style -> drawImage( 'pip');
		
		/**
		 * check if custom image is selected
		 */
		
		if ( !empty($this -> users_ranks[$user_rank]['rank_image'])){
			
			$rank_image = '<img src="'.ROOT_PATH.'images/'.$this -> users_ranks[$user_rank]['rank_image'].'" />';
			
		}
			
		$generated_html = '';
				
		/**
		 * add images
		 */
		
		$stars_to_draw = $this -> users_ranks[$user_rank]['rank_stars'];
		
		/**
		 * we have to use optimalised algoritm
		 */

		if ( $stars_to_draw == 10){
			
			$generated_html = $rank_image.$rank_image.$rank_image.$rank_image.$rank_image.$rank_image.$rank_image.$rank_image.$rank_image.$rank_image;
		
			$stars_to_draw = 0;
			
		}else  if ( $stars_to_draw >= 5){
			
			$generated_html = $rank_image.$rank_image.$rank_image.$rank_image.$rank_image;
		
			$stars_to_draw = $stars_to_draw - 5;
			
			
			
		}
		
		for ( $i = $stars_to_draw; $i > 0; $i --){
				
			$generated_html .= $rank_image;
		
		}
		
		return $generated_html;
		
	}
	
	function newUser( $login, $pass, $pass_repeat, $mail, $group, $active = 1){
		
		global $settings;
		global $strings;
		global $mysql;
		global $cache;
		
		$this -> error = 0;
		
		$login = uniSlashes(htmlspecialchars(trim($login)));
		$mail = $strings -> inputClear( $mail, false);
		$pass = trim( $pass);	
		
		
		if($login == null)
			$this -> error = 1;
			
		if($pass == null && $this -> error == 0)
			$this -> error = 2;
			
		if($pass != $pass_repeat && $this -> error == 0)
			$this -> error = 3;
			
		if($mail == null && $this -> error == 0)
			$this -> error = 4;
			
		if($mysql -> countRows( "users", "`user_login` LIKE '".$login."' AND `user_id` > '-1'") != 0)
			$this -> error = 6;
			
		if($mysql -> countRows( "users", "`user_mail` LIKE '".$mail."' AND `user_id` > '-1'") != 0 && $settings['users_allow_mail_reuse'] == false)
			$this -> error = 7;			
		
		
		if($this -> error == 0){	
			
			$mail = uniSlashes(trim($mail));
			$pass = md5( md5( $pass).md5( $pass));	
			
			$new_user_sql['user_login'] = $login;
			$new_user_sql['user_password'] = $pass;
			$new_user_sql['user_mail'] = $mail;
			$new_user_sql['user_regdate'] = time();
			$new_user_sql['user_active'] = $active;
			$new_user_sql['user_main_group'] = $group;
			$new_user_sql['user_lang'] = $settings['default_language'];
			$new_user_sql['user_style'] = $settings['default_style'];
			
			$mysql -> insert( $new_user_sql, 'users');
			
			$mysql -> query("UPDATE settings SET `setting_value` = (setting_value+1) WHERE `setting_setting` = 'users_num'");
			$mysql -> query("UPDATE settings SET `setting_value` = '".mysql_insert_id()."' WHERE `setting_setting` = 'last_member_id'");
			$mysql -> query("UPDATE settings SET `setting_value` = '".$login."' WHERE `setting_setting` = 'last_member_name'");
			
			$cache -> flushCache( 'system_settings');
			
			return true;
			
		}else{
			
			return false;
			
		}
		
	}
	
	/**
	 * Checks user perms
	 *
	 * @param user_id $user_id
	 * @return bool
	 */
	
	function checkAcp( $groups){
		
		$can_admin = false;
		
		foreach ( $groups as $group_id){
			
			if ( !empty( $group_id) && $this -> users_groups[$group_id]['users_group_can_use_acp']){
				
				$can_admin = true;
				
			}
		}
		
		return $can_admin;
		
	}
	
	/**
	 * bans user via ID
	 *
	 * @param int $id
	 */
	
	function banUser( $id){
		
		global $mysql;
		
		if ( $id > -1){
			
			/**
			 * we are sure, that user to ban isnt guest user, lets ban him
			 */
			
			$sql_ar['locked'] = true;
			
			$mysql -> update( $sql_ar, 'users', "`id` = '$id'");
			
		}
		
	}
	
	/**
	 * returns array with users gorups
	 *
	 * @return array
	 */
	
	function getGroups(){
		
		global $system_settings;
		global $mysql;
		
		$groups = $mysql -> query("SELECT id, name FROM users_groups ORDER BY name DESC");
		while( $list = mysql_fetch_array( $groups, MYSQL_ASSOC)){
			
			$result[$list['id']] = $list['name'];
			
		}
		
		return $result;
		
	}
	
	/**
	 * checks if user is root
	 */

	function isRoot( $user_id){
		
		global $mysql;
		
		settype( $user_id, 'integer');
		
		$query = $mysql -> query("SELECT user_main_group FROM users WHERE `user_id` = '$user_id' AND `user_main_group` = '1'");
		if( $list = mysql_fetch_array( $query, MYSQL_ASSOC)){
			
			return true;
			
		}else{
			
			return false;
			
		}
		
	}
	
	/**
	 * returns error number
	 *
	 * @return int
	 */
	
	function getErrorNumber(){
		
		if($this -> error == 0){
			return false;
		}else{
			return $this -> error;
		}
		
	}
	
	/**
	 * returns error message
	 *
	 * @return string
	 */
	
	function getError(){
		
		global $language;
		
		if($this -> error == 0){
			return false;
		}else{
			return $language -> getString( 'users_error_'.$this -> error);
		}
		
	}
	
	/**
	 * draw warns level
	 */
	
	function drawWarnLevel( $level){
		
		global $style;
		global $settings;
		
		/**
		 * get warns limit
		 */
		
		$warns_limit = $settings['warns_max'];
		
		if ( $warns_limit == 0){
			
			/**
			 * wrong limit
			 */
			
			return $style -> drawImage( 'warns_0', "0%");
			
		}else{
			
			/**
			 * count percent
			 */
			
			$warns_percent = floor( $level * 100 / $settings['warns_max']);
			
			if ( $warns_percent == 0){
				
				return $style -> drawImage( 'warns_0', $warns_percent."%");
				
			}else if ( $warns_percent <= 20){
				
				return $style -> drawImage( 'warns_1', $warns_percent."%");
				
			}else if ( $warns_percent <= 40){
				
				return $style -> drawImage( 'warns_2', $warns_percent."%");
				
			}else if ( $warns_percent <= 60){
				
				return $style -> drawImage( 'warns_3', $warns_percent."%");
				
			}else if ( $warns_percent <= 80){
				
				return $style -> drawImage( 'warns_4', $warns_percent."%");
				
			}else if ( $warns_percent <= 100){
				
				return $style -> drawImage( 'warns_5', $warns_percent."%");
				
			}
			
		}
		
		
	}
	
	/**
	 * draw user avatar
	 *
	 */
	
	function drawAvatar( $type, $image, $width, $height){
		
		if ( $type != 0){
			
			switch ( $type){
			
				case 1:
									
				$avatar_path = $image;
				
				break;
			
				case 2:
				
				$avatar_path = ROOT_PATH.'uploads/'.$image;
			
				break;
				
				case 3:
				
				$avatar_path = ROOT_PATH.'images/avatars_galleries/'.$image;
			
				break;
			
			}
			
			return '<img src="'.$avatar_path.'" width="'.$width.'" height="'.$height.'" />';
		
		}else{
			
			return false;
			
		}
	}
	
	/**
	 * remove actual user avatar
	 */
	
	function killAvatar( $user_id){
	
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		settype( $user_id, 'integer');
		
		/**
		 * select user id
		 */
		
		$profile_select_sql = $mysql -> query( "SELECT * FROM users WHERE `user_id` = '".$user_id."'");
		
		if ( $profile_result = mysql_fetch_array( $profile_select_sql, MYSQL_ASSOC))
			$profile_result = $mysql -> clear($profile_result);
			
		/**
		 * check, if user already has avatar
		 */
			
		if ( $profile_result['user_avatar_type'] != 0){
			
			/**
			 * do diffrent things
			 */
				
			if ( $profile_result['user_avatar_type'] == 2){
				
				/**
				 * avatar is uploaded
				 */
				
				if( file_exists( ROOT_PATH.'uploads/'.$profile_result['user_avatar_image']))
					unlink( ROOT_PATH.'uploads/'.$profile_result['user_avatar_image']);
				
			}
			
			/**
			 * update tables
			 */
			
			$update_av_sql['user_avatar_image'] = '';
			$update_av_sql['user_avatar_type'] = 0;
			$update_av_sql['user_avatar_width'] = 0;
			$update_av_sql['user_avatar_height'] = 0;
			
			$mysql -> update( $update_av_sql, 'users', "`user_id` = '$user_id'");
			
		}		
	}
	
	/**
	 * check, if user can be censored
	 */
	
	function cantCensore( $groups = array()){
		
		$can_censore = false;
		
		foreach ( $groups as $group_id){
			
			if ( !empty( $group_id) && $this -> users_groups[$group_id]['users_group_avoid_badwords']){
				$can_censore = true;
			}
		}
		
		return $can_censore;
		
	}
		
	/**
	 * check, if user can have legend
	 */
	
	function checkEditLegend( $groups = array()){
		
		$can_legend = false;
		
		foreach ( $groups as $group_id){
			
			if ( !empty( $group_id) && $this -> users_groups[$group_id]['users_group_draw_edit_legend']){
				$can_legend = true;
			}
		}
		
		return $can_legend;
		
	}
	
	/**
	 * check, if user can get PM's
	 */
	
	function checkPm( $groups = array()){
		
		$can_get_pm = false;
		
		foreach ( $groups as $group_id){
			
			if ( !empty( $group_id) && $this -> users_groups[$group_id]['users_group_can_use_pm']){
				$can_get_pm = true;
			}
		}
		
		return $can_get_pm;
		
	}
	
	/**
	 * check, if user have space for new PM
	 */
	
	function checkPmSpace( $groups = array()){
		
		$pm_space = 0;
		
		foreach ( $groups as $group_id){
			
			if ( !empty( $group_id) && $this -> users_groups[$group_id]['users_group_pm_limit'] > $pm_space){
				$pm_space = $this -> users_groups[$group_id]['users_group_pm_limit'];
			}
		}
		
		return $pm_space;
	}
	
	/**
	 * check if mail is proper
	 *
	 * @param string $mail
	 */
	
	function checkMail( $mail){
		
		$proper_mail = false;
		
		/**
		 * check at
		 */
		
		if ( strstr( $mail, "@") != false)
			$proper_mail = true;
		
		/**
		 * check domain
		 */
			
		if ( strrpos($mail, ".") == (strlen( $mail) - 3) || strrpos($mail, ".") == (strlen( $mail) - 4))
			$proper_mail = true;
			
		return $proper_mail;
			
	}
	
	/**
	 * draw user reputation level
	 */
	
	function drawReputation( $rep){
		
		$started = false;
		
		foreach ( $this -> users_reps as $rep_ops){
			
			if ( !$started){
				
				$started = true;
				$reputation = $rep_ops['reputation_scale_name'];
				
			}
			
			if ( $rep >= $rep_ops['reputation_scale_points']){
				
				$reputation = $rep_ops['reputation_scale_name'];
				
			}
			
		}
		
		return $reputation;
		
	}
	
	/**
	 * count user reputation level
	 */
	
	function countReputation( $rep, $posts, $regdate){

		global $settings;
		
		if ( $settings['reputation_posting_bonus'] !=0){
			
			$rep += $posts * $settings['reputation_posting_bonus'];
			
		}
		
		if ( $settings['reputation_bonus_days'] > 0 && $settings['reputation_bonus_size'] != 0){
			
			$rep += floor( $regdate / ( 24 * 3600) / $settings['reputation_bonus_days']) * $settings['reputation_bonus_size'];
			
		}
		
		return $rep;
		
	}
	
}

?>