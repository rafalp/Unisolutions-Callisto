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
|	Session Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class session{
	
	/**
	 * session id
	 */
	
	var $session_id;
	
	/**
	 * set user id for guest
	 */
	
	var $user_id = -1;
	
	/**
	 * set ip
	 */
	
	var $user_ip = 0;
	
	/**
	 * set acp access key
	 */
	
	var $admin_key = '';
	
	/**
	 * set user array
	 */
	
	var $user = array();
	
	var $user_groups = array();
	
	/**
	 * user localization
	 */
	
	var $user_localisation = array( 'section', 'action', 'target');
	
	/**
	 * user masks
	 */
	
	var $user_masks = array();
	
	/**
	 * user forums access
	 */
	
	var $user_forums = array();
	
	/**
	 * user mod access
	 */
	
	var $user_mod_forums = array();
	
	/**
	 * run constructor
	 */
	
	function __construct(){
	
		/**
		 * pre-include few globals
		 */
		
		global $system_settings;
		global $mysql;
		global $strings;
		global $settings;
		global $statistics;
		global $forums;
		global $ban_filters;
		global $cache;
		
		/**
		 * some basic stuff
		 * start from defining ip
		 */
		
		// Determine user IP
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
	    {
	    	$this -> user_ip = ip2long($_SERVER['HTTP_CLIENT_IP']);
	    }
	    else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	    {
	    	$this -> user_ip = ip2long($_SERVER['HTTP_X_FORWARDED_FOR']);
	    }
	    else
	    {
	    	$this -> user_ip = ip2long($_SERVER['REMOTE_ADDR']);
	    }
	    	
		/**
		 * define if we are in acp, or not
		 */
		
		if ( defined( 'ACP')){
			
			// Dont look for SID's in query
			define('USE_SID_IN_QUERY', false);
			
			/**
			 * we are in admin
			 * for secruity reasons, auto-liging feature does'nt work in ACP, so we can leave autologin session opening
			 * start from capturing key
			 */
			
			$this -> user_id = $_SESSION['admin_session_user_id'];
			$this -> admin_key = $_SESSION['admin_session_key'];
			$this -> session_id = $_SESSION['admin_session_id'];
			
			/**
			 * lets check, if key doesnt contains whitespaces, quotes, and other things, making it proper
			 */
			
			if ( strstr( $this -> admin_key, " ") != false || strstr( $this -> admin_key, "\"") != false || strstr( $this -> admin_key, "'") != false)
				$this -> admin_key = -1;

			/**
			 * now the same proceddure for session id
			 */
						
			if ( strstr( $this -> session_id, " ") != false || strstr( $this -> session_id, "\"") != false || strstr( $this -> session_id, "'") != false)
				$this -> session_id = -1;
			
			/**
			 * for session id we simply force value type to be number
			 */
			
			settype( $this -> user_id, 'integer');
			
			/**
			 * proceed further
			 */
				
			$this -> getUser( $this -> user_id);
			
		}else{
			
			/**
			 * we are in front-end
			 * get session id from cookie
			 */
			
			$this -> session_id = uniSlashes(getUniCookie( 'sid'));
			
			// Session empty?
			if (!isset($this -> session_id[31]) && ALLOW_SID_IN_QUERY)
			{
				// Lookie for SID in query
				define('USE_SID_IN_QUERY', true);
				
				// Take it from query?
				$this -> session_id = uniSlashes(isset($_GET['sid']) ? $_GET['sid'] : '');
			}
			
			$this -> getUser();
			
		}
		
		/**
		 * update session life-time
		 */
		
		if ( (defined( 'ACP') && $this -> user['user_id'] != -1) || (!defined( 'ACP'))){
			
			$this -> updateSession();
			
		}
		
		/**
		 * styles and langs switching
		 */
		
		if ( !defined( 'ACP')){
			
			if ( isset( $_GET['nstyle']) && $settings[ 'style_allow_change']){
				
				$this -> switchStyle();
				
			}
						
			if ( isset( $_GET['nlang'])){
				
				$this -> switchLang();
				
			}
			
		}
		
	}
	
	/**
	 * make session life longer
	 *
	 */
	
	function updateSession(){
		
		/**
		 * we will update session, but only if we are not goues in acp
		 */
					
		include( FUNCTIONS_GLOBALS);
		
		if ( defined( 'ACP')){
			
			$table_name = 'admins_sessions';
			$time_field = 'admin_session_last_time';
			$condition_field = 'admin_session_id';
		
		}else{
			
			$table_name = 'users_sessions';
			$time_field = 'users_session_last_time';
			$condition_field = 'users_session_id';
			
			/**
			 * build up our atual localistation
			 */
			
			if ( !in_array( $_GET['act'], array( 'captcha', 'rss', 'shoutbox')) || ($_GET['act'] != 'download' && $_GET['thumb'] != true)){
			
				$update_session_query['users_session_location_act'] = $strings -> inputClear($_GET['act'], false);
				
				$forum = $_GET['forum'];
				$topic = $_GET['topic'];
				$post = $_GET['post'];
				$user = $_GET['user'];
				
				settype( $forum, 'integer');
				settype( $topic, 'integer');
				settype( $post, 'integer');
				settype( $user, 'integer');
				
				$update_session_query['users_session_location_forum'] = $forum;
				$update_session_query['users_session_location_topic'] = $topic;
				$update_session_query['users_session_location_post'] = $post;
				$update_session_query['users_session_location_user'] = $user;
				$update_session_query['users_session_agent'] = uniSlashes( $_SERVER['HTTP_USER_AGENT']);
				
				if ( $this -> isBot() != false && $this -> user['user_id'] == -1 && $this -> user['user_is_bot']){
										
					$update_session_query['users_session_bot'] = true;
					$update_session_query['users_session_bot_name'] = $this -> isBot();
					
					/**
					 * cache
					 */
					
					$cache -> flushCache( 'users_online');
					$cache -> flushCache( 'spiders_online');
					
					/**
					 * check if we are forcing lite style
					 */
					
					if ( $settings['spiders_force_simple'])
						define( 'SIMPLE_MODE', true);
					
					if ( $settings['spiders_log_visits'])
						$logs -> addSpiderLog( $this -> isBot());
				
				}
				
			}
		
			setUniCookie( 'sid', $this -> session_id, false);
			setUniCookie( 'uid', $this -> session_user['user_id'], false);
			
		}
		
		$update_session_query[$time_field] = time();
		
		$mysql -> update( $update_session_query, $table_name, "`$condition_field` = '".$this -> session_id."'");
	
	}
	/**
	 * logouts user
	 *
	 * @param int $user_id
	 * @param string $session_id
	 * @return true
	 */
	
	function degradeAdminSession( $user_id, $session_id){
		
		global $mysql;
		
		/**
		 * delete session from mysql
		 */
				
		$mysql -> delete( 'admins_sessions', "`admin_session_id` = '$session_id' AND `admin_session_user_id` = '$user_id' AND `admin_session_ip` = '".$this -> user_ip."'");
			
		/**
		 * kill cookies
		 */
			
		unset( $_SESSION['admin_session_user_id']);
		unset( $_SESSION['admin_session_key']);
		unset( $_SESSION['admin_session_id']);
		
		/**
		 * return true
		 */
		
		return true;
		
	}
	
	/**
	 * logouts user
	 *
	 * @param int $user_id
	 * @param string $session_id
	 * @return true
	 */
	
	function degradeSession( $user_id, $session_id){
		
		global $mysql;
		
		/**
		 * turn user session into guests
		 */
		
		$update_session_sql['users_session_user_id'] = -1;
		
		$mysql -> update( $update_session_sql, 'users_sessions', "`users_session_user_id` = '$user_id' AND `users_session_id` = '$session_id' AND `users_session_ip` = '".$this -> user_ip."'");
		
		/**
		 * and update lastvisit
		 */
		
		$update_user_sql['user_last_login'] = time();
		
		$mysql -> update( $update_user_sql, 'users', "`user_id` = '$user_id'");
				
		/**
		 * return true
		 */
		
		return true;
		
	}
	
	/**
	 * gets user data from mysql result
	 *
	 * @param array $user_result
	 */
	
	function getUser( $user_id = -1){
		
		global $mysql;
		global $settings;
		global $logs;
		global $users;
				
		/**
		 * now lets make query
		 */
	
		if ( defined('ACP')){
			$session_query = $mysql -> query( "SELECT s.*, u.*, g.* FROM admins_sessions s LEFT JOIN users u ON u.user_id = s.admin_session_user_id LEFT JOIN users_groups g ON g.users_group_id = u.user_id WHERE s.admin_session_id = '".$this -> session_id."' AND s.admin_session_user_id = '".$this -> user_id."' AND s.admin_session_ip = '".$this -> user_ip."' AND s.admin_session_key = '".$this -> admin_key."' AND s.admin_session_last_time >= '".(time() - 3600)."' LIMIT 1");
		}else{
			
			/**
			 * session taking conditions
			 */
			
			$taking_conditions[] = "s.users_session_last_time >= '".(time() - $settings['session_time'])."'";
				
			$taking_conditions[] = "s.users_session_id = '".$this -> session_id."'";
			
			if ( $settings['sessions_ip'] || ALLOW_SID_IN_QUERY)
				$taking_conditions[] = "s.users_session_ip = '".$this -> user_ip."'";
			
			if ( $settings['sessions_agents'])
				$taking_conditions[] = "s.users_session_agent = '".uniSlashes( $_SERVER['HTTP_USER_AGENT'])."'";
			
			
			
			$session_query = $mysql -> query( "SELECT s.*, u.*, f.*, g.* FROM users_sessions s LEFT JOIN users u ON u.user_id = s.users_session_user_id LEFT JOIN profile_fields_data f ON u.user_id = f.profile_fields_user LEFT JOIN users_groups g ON g.users_group_id = u.user_id WHERE ".join( " AND ", $taking_conditions)." LIMIT 1");
		}
		
		/**
		 * check if we have result
		 */
		
		$session_found = false;
		
		if ( $session_result = mysql_fetch_array( $session_query, MYSQL_ASSOC)){
			
			$session_result = $mysql -> clear( $session_result);
			$session_found = true;
		
			if ( !defined('ACP'))
				$this -> session_id = $session_result['users_session_id'];
			
			/**
			 * check banned list
			 */
			
		}
			
		/**
		 * some vars for guest
		 */
		
		$lang_from_cookie = getUniCookie( 'language');
		$style_from_cookie = getUniCookie( 'style');
		
		$lang_from_cookie = uniSlashes( $lang_from_cookie);
		settype( $style_from_cookie, 'integer');
		
		if ( !empty( $lang_from_cookie)){
			
			$guest_lang = $lang_from_cookie;
			
		}else{
		
			/**
			 * set deault lang for fuests
			 */
			
			$guest_lang = $settings['default_language'];
		
		}
		
		if ( !empty( $style_from_cookie)){
			
			$guest_style = $style_from_cookie;
			
		}else{
		
			/**
			 * set deault lang for fuests
			 */
			
			$guest_style = $settings['default_style'];
		
		}
		
		/**
		 * define what to do next
		 */
		
		if ( $session_found){
			
			/**
			 * we managed to catch session
			 * start from setting basic info about user.
			 */
			
			if ( $session_result['user_id'] == -1){
				
				/**
				 * guest user
				 */
				
				$this -> user = array(
					'user_id' => -1,
					'user_can_use_pm' => false,
					'user_time_zone' => $settings['time_timezone'],
					'user_dst' => $settings['time_dst'],
					'user_active' => true,
					'user_main_group' => 2,
					'user_lang' => $guest_lang,
					'user_style' => $guest_style,
					'user_show_sigs' => $settings['guest_draw_signatures'],
					'user_show_avatas' => $settings['guest_draw_avatars'],
					'user_avatar_image' => 0,
					'user_can_use_pm' => false,
					'user_pm_limit' => 0,
					'user_can_be_admin' => false,
					'user_can_be_mod' => false,
					'user_is_root' => false,
					'user_is_global_mod' => false,
					'user_can_moderate' => false,
					'user_can_edit_calendar' => false,
					'user_shoutbox' => 0,
					'user_can_see_closed_page' => false,
					'user_can_send_mails' => false,
					'user_can_see_users_profiles' => false,
					'user_edit_time_limit' => 0,
					'user_draw_edit_legend' => false,
					'user_delete_own_topics' => false,
					'user_change_own_topics' => false,
					'user_close_own_topics' => false,
					'user_delete_own_posts' => false,
					'user_edit_own_posts' => false,
					'user_start_surveys' => false,
					'user_vote_surveys' => false,
					'user_avoid_flood' => false,
					'user_avoid_badwords' => false,
					'user_avoid_closed_topics' => false,
					'user_see_hidden' => false,
					'user_search' => false,
					'user_search_limit' => 0,
					'user_uploads_quota' => 0,
					'user_uploads_used' => 0,
					'user_uploads_limit' => 0);
				
				/**
				 * set group to one used by guests
				 */
					
				$this -> user_groups[] = 2;
				
				/**
				 * list o masks
				 */
					
				$this -> user_masks[] = $session_result['user_permissions'];
				
				/**
				 * check if we have an bot
				 */
				
				if ( $session_result['users_session_bot']){		
				
					if ( $settings['spiders_force_simple'])
						define( 'SIMPLE_MODE', true);
				
				}
				
			}else{
				
				/**
				 * logged in user
				 */
				
				$this -> user = array(
					'user_id' => $session_result['user_id'],
					'user_login' => $session_result['user_login'],
					'user_mail' => $session_result['user_mail'],
					'user_show_mail' => $session_result['user_show_mail'],
					'user_posts_num' => $session_result['user_posts_num'],
					'user_notify_pm' => $session_result['user_notify_pm'],
					'user_pm_num' => $session_result['user_pm_num'],
					'user_pm_new_num' => $session_result['user_pm_new_num'],
					'user_posts_num' => $session_result['user_posts_num'],
					'user_last_post_time' => $session_result['user_last_post_time'],
					'user_last_search_time' => $session_result['user_last_search_time'],
					'user_time_zone' => $session_result['user_time_zone'],
					'user_dst' => $session_result['user_dst'],
					'user_active' => $session_result['user_active'],
					'user_warns' => $session_result['user_warns'],
					'user_rep' => $session_result['user_rep'],
					'user_last_login' => $session_result['user_last_login'],
					'user_permissions' => $session_result['user_permissions'],
					'user_main_group' => $session_result['user_main_group'],
					'user_lang' => $session_result['user_lang'],
					'user_style' => $session_result['user_style'],
					'user_show_sigs' => $session_result['user_show_sigs'],
					'user_show_avatars' => $session_result['user_show_avatars'],
					'user_regdate' => $session_result['user_regdate'],
					'user_custom_title' => $session_result['user_custom_title'],
					'user_avatar_image' => $session_result['user_avatar_image'],
					'user_avatar_type' => $session_result['user_avatar_type'],
					'user_avatar_width' => $session_result['user_avatar_width'],
					'user_avatar_height' => $session_result['user_avatar_height'],
					'user_auto_subscribe' => $session_result['user_auto_subscribe'],
					'user_is_bot' => $session_result['users_session_bot'],
					'user_can_use_pm' => false,
					'user_pm_limit' => 0,
					'user_can_be_admin' => false,
					'user_can_be_mod' => false,
					'user_is_root' => false,
					'user_is_global_mod' => false,
					'user_can_moderate' => false,
					'user_can_edit_calendar' => false,
					'user_shoutbox' => 0,
					'user_can_see_closed_page' => false,
					'user_can_send_mails' => false,
					'user_can_see_users_profiles' => true,
					'user_edit_time_limit' => 0,
					'user_draw_edit_legend' => false,
					'user_delete_own_topics' => false,
					'user_change_own_topics' => false,
					'user_close_own_topics' => false,
					'user_delete_own_posts' => false,
					'user_edit_own_posts' => false,
					'user_start_surveys' => false,
					'user_vote_surveys' => false,
					'user_avoid_flood' => false,
					'user_avoid_badwords' => false,
					'user_avoid_closed_topics' => false,
					'user_see_hidden' => false,
					'user_search' => false,
					'user_search_limit' => 0,
					'user_uploads_quota' => 0,
					'user_uploads_used' => 0,
					'user_uploads_limit' => 0);
					
				/**
				 * additional fields
				 */
				
				foreach ( $users -> custom_fields as $field_id => $field_ops)
					$this -> user['user_custom_field_'.$field_id] = $session_result['field_'.$field_id];
									
				/**
				 * if we are in frontend, set session id
				 */
					
				if ( !defined('ACP'))
					$this -> session_id = $session_result['users_session_id'];
								
				/**
				 * list o masks
				 */
					
				$this -> user_masks[] = $session_result['user_permissions'];
				
				/**
				 * list of user groups
				 */
				
				if ( !empty( $session_result['user_other_groups']))
					$this -> user_groups = split( ",", $session_result['user_other_groups']);
				
				$this -> user_groups[] = $session_result['user_main_group'];
								
			}
				
		}else{
			
			/**
			 * we will start new guest session, but only if we are not in ACP
			 */
			
			$autologin = $this -> autoLog();
			
			if ( !$autologin){
				
				$this -> user = array(
					'user_id' => -1,
					'user_can_use_pm' => false,
					'user_time_zone' => $settings['time_timezone'],
					'user_dst' => $settings['time_dst'],
					'user_active' => true,
					'user_main_group' => 2,
					'user_lang' => $guest_lang,
					'user_style' => $guest_style,
					'user_show_sigs' => $settings['guest_draw_signatures'],
					'user_show_avatas' => $settings['guest_draw_avatars'],
					'user_avatar_image' => 0,
					'user_can_use_pm' => false,
					'user_pm_limit' => 0,
					'user_can_be_admin' => false,
					'user_can_be_mod' => false,
					'user_is_root' => false,
					'user_is_global_mod' => false,
					'user_can_moderate' => false,
					'user_can_edit_calendar' => false,
					'user_shoutbox' => 0,
					'user_can_see_closed_page' => false,
					'user_can_send_mails' => false,
					'user_can_see_users_profiles' => false,
					'user_edit_time_limit' => 0,
					'user_draw_edit_legend' => false,
					'user_delete_own_topics' => false,
					'user_change_own_topics' => false,
					'user_close_own_topics' => false,
					'user_delete_own_posts' => false,
					'user_edit_own_posts' => false,
					'user_start_surveys' => false,
					'user_vote_surveys' => false,
					'user_avoid_flood' => false,
					'user_avoid_badwords' => false,
					'user_avoid_closed_topics' => false,
					'user_see_hidden' => false,
					'user_search' => false,
					'user_search_limit' => 0,
					'user_uploads_quota' => 0,
					'user_uploads_used' => 0,
					'user_uploads_limit' => 0);
				
				$this -> user_groups[] = 2;
				
				/**
				 * check, if we have bot
				 */
				
				if ( $this -> isBot() != false){
					
					$new_session_sql['users_session_bot'] = true;
					$new_session_sql['users_session_bot_name'] = $this -> isBot();
					
					/**
					 * check if we are forcing lite style
					 */
					
					if ( $settings['spiders_force_simple'])
						define( 'SIMPLE_MODE', true);
					
					if ( $settings['spiders_log_visits'])
						$logs -> addSpiderLog( $this -> isBot());
					
				}
				
				/**
				 * only if we arent in acp, we will put user info into mysql
				 */
			
				if ( !defined( 'ACP')){
					
					$new_session_sql['users_session_id'] = md5(mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . $this -> user_ip . mt_rand(0, 9));
					$new_session_sql['users_session_ip'] = $this -> user_ip;
					$new_session_sql['users_session_user_id'] = -1;
					$new_session_sql['users_session_open_time'] = time();
					$new_session_sql['users_session_last_time'] = time();
					$new_session_sql['users_session_agent'] = uniSlashes( $_SERVER['HTTP_USER_AGENT']);
										
					/**
					 * put new session into database
					 */
					
					$mysql -> insert( $new_session_sql, 'users_sessions');
					
					$this -> session_id = $new_session_sql['users_session_id'];
					
					/**
					 * set cookie
					 */
								
					setUniCookie( 'sid', $this -> session_id, false);
						
				}			
			}
				
		}
		
		// Not in admin?
		if (!defined('ACP') && !defined('USE_SID_IN_QUERY'))
		{
			define('USE_SID_IN_QUERY', false);
		}
		
		/**
		 * check if root
		 */
		
		if ( $this -> user['user_id'] != -1 && $session_result['user_main_group'] == 1)
			$this -> user['user_is_root'] = true;
		
		/**
		 * take perms from groups
		 */
		
		$this -> getGroups();
						
		/**
		 * get masks
		 */
		
		$this -> getMasks();
			
		/**
		 * check, if we can use mod cp
		 */
			
		global $forums;
		
		foreach ( $forums -> forums_list as $forum_id => $forum_ops){
			
			if ( $this -> isMod($forum_id))
				$this -> user['user_can_be_mod'] = true;
			
		}
		
	}
	
	/**
	 * makes autolog
	 */
	
	function autoLog(){
		
		global $mysql;
		global $settings;
		global $ban_filters;
		global $cache;
		global $users;
		
		if ( defined( 'ACP')){
		
			/**
			 * cant autolog in acp!
			 */
			
			return false;
		
		}else{
			
			/**
			 * check cookies
			 */
			
			$login_user = getUniCookie( 'login_user');
			$login_key = getUniCookie( 'login_key');	
			
			settype( $login_user, 'integer');
			$login_key = uniSlashes( $login_key);
				
			/**
			 * cookies exists
			 */
			
			if ( $login_user > 0 && strlen( $login_key) == 32){
				
				/**
				 * cookies exists, lets select autologin key
				 */
								
				$alogin_query = $mysql -> query( "SELECT users_autologin_hidden, users_autologin_last_use FROM users_autologin WHERE `users_autologin_key` = '$login_key' AND `users_autologin_user` = '$login_user'");
				
				if ( $alogin_result = mysql_fetch_array( $alogin_query, MYSQL_ASSOC)){
					
					/**
					 * found key!
					 * get user
					 */
					
					$session_query = $mysql -> query( "SELECT u.*, f.*, g.* FROM users u JOIN profile_fields_data f ON u.user_id = f.profile_fields_user LEFT JOIN users_groups g ON g.users_group_id = u.user_id WHERE u.user_id = '$login_user'");
				
					if ( $session_result = mysql_fetch_array( $session_query, MYSQL_ASSOC)){

						//clear result		
						$session_result = $mysql -> clear( $session_result);
			
						/**
						 * are we locked?
						 */
						
						$locked = false;
						
						if ( $session_result['user_active'] == false)
							$locked = true;
						
						if ( $session_result['user_locked'] == true)
							$locked = true;
							
						if( $settings['warns_turn'] && $session_result['user_warns'] >= $settings['warns_max'] && $settings['warns_max'] > 0 && $settings['warns_lock_account'] && $session_result['user_main_group'] != 0)
							$locked = true;
						
						if ( $session_result['user_main_group'] != 1){
								
							foreach ( $ban_filters as $banfilter_ops){
					
								if ( $banfilter_ops['type'] == 0){
									
									/**
									 * check filter
									 */
									
									if ( strstr( $banfilter_ops['filter'], "*") != false){
										
										/**
										 * inteligent match
										 */
										
										$match_pattern = str_ireplace( ".", "\\.", $banfilter_ops['filter']);
										$match_pattern = str_ireplace( "^", "\\^", $match_pattern);
										$match_pattern = str_ireplace( "$", "\\$", $match_pattern);
										$match_pattern = str_ireplace( "?", "\\?", $match_pattern);
										$match_pattern = str_ireplace( "+", "\\+", $match_pattern);
										$match_pattern = str_ireplace( "[", "\\[", $match_pattern);
										$match_pattern = str_ireplace( "]", "\\]", $match_pattern);
										$match_pattern = str_ireplace( "(", "\\(", $match_pattern);
										$match_pattern = str_ireplace( ")", "\\)", $match_pattern);
										$match_pattern = str_ireplace( "{", "\\{", $match_pattern);
										$match_pattern = str_ireplace( "}", "\\}", $match_pattern);
										$match_pattern = str_ireplace( "\\", "\\\\", $match_pattern);
																
										$match_pattern = str_replace( "*", "(.+)", $match_pattern);
										
										$match_pattern = '^'.$match_pattern.'$^';
										$match_string = $_SERVER['REMOTE_ADDR'];
										$maths = preg_match( $match_pattern, $match_string);
										
										if ( $maths > 0){
										
											$locked = true;
											
										}
											
									}else{
										
										/**
										 * simple match
										 */
										
										if ( $banfilter_ops['filter'] == $login){
											$locked = true;
										}
									}
									
								}
								
							}
								
							foreach ( $ban_filters as $banfilter_ops){
					
								if ( $banfilter_ops['type'] == 1){
									
									/**
									 * check filter
									 */
									
									if ( strstr( $banfilter_ops['filter'], "*") != false){
										
										/**
										 * inteligent match
										 */
										
										$match_pattern = str_ireplace( "\\", "\\\\", $banfilter_ops['filter']);
										$match_pattern = str_ireplace( ".", "\\.", $match_pattern);
										$match_pattern = str_ireplace( "^", "\\^", $match_pattern);
										$match_pattern = str_ireplace( "$", "\\$", $match_pattern);
										$match_pattern = str_ireplace( "?", "\\?", $match_pattern);
										$match_pattern = str_ireplace( "+", "\\+", $match_pattern);
										$match_pattern = str_ireplace( "[", "\\[", $match_pattern);
										$match_pattern = str_ireplace( "]", "\\]", $match_pattern);
										$match_pattern = str_ireplace( "(", "\\(", $match_pattern);
										$match_pattern = str_ireplace( ")", "\\)", $match_pattern);
										$match_pattern = str_ireplace( "{", "\\{", $match_pattern);
										$match_pattern = str_ireplace( "}", "\\}", $match_pattern);
																
										$match_pattern = str_replace( "*", "(.+)", $match_pattern);
										
										$match_pattern = '^'.$match_pattern.'$^';
										$match_string = $session_result['user_login'];
										$maths = preg_match( $match_pattern, $match_string);
										
										if ( $maths > 0){
										
											$user_locked = true;
											
										}
										
									}else{
										
										/**
										 * simple match
										 */
										
										if ( $banfilter_ops['filter'] == $login){
											$user_locked = true;
										}
									}
									
								}
								
							}
							
							foreach ( $ban_filters as $banfilter_ops){
					
								if ( $banfilter_ops['type'] == 2){
									
									/**
									 * check filter
									 */
									
									if ( strstr( $banfilter_ops['filter'], "*") != false){
										
										/**
										 * inteligent match
										 */
										
										$match_pattern = str_ireplace( "\\", "\\\\", $banfilter_ops['filter']);
										$match_pattern = str_ireplace( ".", "\\.", $match_pattern);
										$match_pattern = str_ireplace( "^", "\\^", $match_pattern);
										$match_pattern = str_ireplace( "$", "\\$", $match_pattern);
										$match_pattern = str_ireplace( "?", "\\?", $match_pattern);
										$match_pattern = str_ireplace( "+", "\\+", $match_pattern);
										$match_pattern = str_ireplace( "[", "\\[", $match_pattern);
										$match_pattern = str_ireplace( "]", "\\]", $match_pattern);
										$match_pattern = str_ireplace( "(", "\\(", $match_pattern);
										$match_pattern = str_ireplace( ")", "\\)", $match_pattern);
										$match_pattern = str_ireplace( "{", "\\{", $match_pattern);
										$match_pattern = str_ireplace( "}", "\\}", $match_pattern);
																
										$match_pattern = str_replace( "*", "(.+)", $match_pattern);
										
										$match_pattern = '^'.$match_pattern.'$^';
										
										$match_string = $session_result['user_mail'];
										$maths = preg_match( $match_pattern, $match_string);
										
										if ( $maths > 0){
										
											$user_locked = true;
											$error = 7;
											
										}
											
									}else{
										
										/**
										 * simple match
										 */
										
										if ( $banfilter_ops['filter'] == $login){
											$user_locked = true;
											$error = 7;
										}
									}
									
								}
								
							}
						
						}
							
						/**
						 * set all
						 */
						
						if ( !$locked){
							
							$this -> user = array(
								'user_id' => $session_result['user_id'],
								'user_login' => $session_result['user_login'],
								'user_mail' => $session_result['user_mail'],
								'user_show_mail' => $session_result['user_show_mail'],
								'user_posts_num' => $session_result['user_posts_num'],
								'user_notify_pm' => $session_result['user_notify_pm'],
								'user_pm_num' => $session_result['user_pm_num'],
								'user_pm_new_num' => $session_result['user_pm_new_num'],
								'user_posts_num' => $session_result['user_posts_num'],
								'user_last_post_time' => $session_result['user_last_post_time'],
								'user_last_search_time' => $session_result['user_last_search_time'],
								'user_time_zone' => $session_result['user_time_zone'],
								'user_dst' => $session_result['user_dst'],
								'user_active' => $session_result['user_active'],
								'user_warns' => $session_result['user_warns'],
								'user_rep' => $session_result['user_rep'],
								'user_last_login' => $session_result['user_last_login'],
								'user_permissions' => $session_result['user_permissions'],
								'user_main_group' => $session_result['user_main_group'],
								'user_lang' => $session_result['user_lang'],
								'user_style' => $session_result['user_style'],
								'user_show_sigs' => $session_result['user_show_sigs'],
								'user_show_avatars' => $session_result['user_show_avatars'],
								'user_regdate' => $session_result['user_regdate'],
								'user_custom_title' => $session_result['user_custom_title'],
								'user_avatar_image' => $session_result['user_avatar_image'],
								'user_avatar_type' => $session_result['user_avatar_type'],
								'user_avatar_width' => $session_result['user_avatar_width'],
								'user_avatar_height' => $session_result['user_avatar_height'],
								'user_auto_subscribe' => $session_result['user_auto_subscribe'],
								'user_can_use_pm' => false,
								'user_pm_limit' => 0,
								'user_can_be_admin' => false,
								'user_can_be_mod' => false,
								'user_is_root' => false,
								'user_is_global_mod' => false,
								'user_can_moderate' => false,
								'user_can_edit_calendar' => false,
								'user_shoutbox' => 0,
								'user_can_see_closed_page' => false,
								'user_can_send_mails' => false,
								'user_can_see_users_profiles' => true,
								'user_edit_time_limit' => 0,
								'user_draw_edit_legend' => false,
								'user_delete_own_topics' => false,
								'user_change_own_topics' => false,
								'user_close_own_topics' => false,
								'user_delete_own_posts' => false,
								'user_edit_own_posts' => false,
								'user_start_surveys' => false,
								'user_vote_surveys' => false,
								'user_avoid_flood' => false,
								'user_avoid_badwords' => false,
								'user_avoid_closed_topics' => false,
								'user_see_hidden' => false,
								'user_search' => false,
								'user_search_limit' => 0,
								'user_uploads_quota' => 0,
								'user_uploads_used' => 0,
								'user_uploads_limit' => 0);
					
							/**
							 * additional fields
							 */
							
							foreach ( $users -> custom_fields as $field_id => $field_ops)
								$this -> user['user_custom_field_'.$field_id] = $session_result['field_'.$field_id];
															
							$this -> session_id = md5( md5(time()).md5($this -> user_ip));
										
							/**
							 * list o masks
							 */
								
							$this -> user_masks[] = $session_result['user_permissions'];
							
							/**
							 * list of user groups
							 */
							
							if ( !empty( $session_result['user_other_groups']))
								$this -> user_groups = split( ",", $session_result['user_other_groups']);
							
							$this -> user_groups[] = $session_result['user_main_group'];
					
							/**
							 * delete existing sessions
							 */
							
							$mysql -> delete( 'users_sessions', "`users_session_ip` = '".$this -> user_ip."' OR `users_session_user_id` = '$login_user'");
							
							$cache -> flushCache( 'users_online');
							$cache -> flushCache( 'spiders_online');
										
							/**
							 * open session
							 */
							
							$new_session_sql['users_session_id'] = $this -> session_id;
							$new_session_sql['users_session_ip'] = $this -> user_ip;
							$new_session_sql['users_session_user_id'] = $login_user;
							$new_session_sql['users_session_hidden'] = $alogin_result['users_autologin_hidden'];
							$new_session_sql['users_session_open_time'] = time();
							$new_session_sql['users_session_last_time'] = time();
							
							/**
							 * put new session into database
							 */
							
							$mysql -> insert( $new_session_sql, 'users_sessions');
								
							setUniCookie( 'sid', $new_session_sql['users_session_id'], false);
							setUniCookie( 'uid', $new_session_sql['users_session_user_id'], false);
					
							/**
							 * make autologin live longer
							 */
							
							setUniCookie( 'login_user', $login_user, true);
							setUniCookie( 'login_key', $login_key, true);
							
							$mysql -> update( array( 'users_autologin_last_use' => time()), 'users_autologin', "`users_autologin_key` = '$login_key' AND `users_autologin_user` = '$login_user'");
						
							/**
							 * all ok
							 */
							
							return true;
							
						}else{
							
							return false;
							
						}
						
					}else{
						
						/**
						 * user not found
						 */
						
						return false;
					
					}
					
				}else{
				
					/**
					 * auto login doesnt exists in mysql
					 */
					
					return false;
				
				}
				
			}else{
			
				return false;
		
			}
			
		}
	}
	
	/**
	 * takes data from user groups
	 *
	 */
	
	function getGroups(){
		
		global $users;
		
		foreach ( $users -> users_groups as $group_id => $groups_result){
			
			if ( in_array( $group_id, $this -> user_groups)){
			
				/**
				 * add mask
				 */
									
				$this -> user_masks[] = $groups_result['users_group_permissions'];
				
				/**
				 * user can administrate
				 */
				
				if( $groups_result['users_group_can_use_acp'] == true){
					$this -> user['user_can_be_admin'] = true;
				}
				
				/**
				 * can browse closed page
				 */
				
				if( $groups_result['users_group_can_see_closed_page'] == true){
					$this -> user['user_can_see_closed_page'] = true;
				}
				
				/**
				 * can see users profiles
				 */
				
				if( $groups_result['users_group_can_see_users_profiles'] == true){
					$this -> user['user_can_see_users_profiles'] = true;
				}
				
				/**
				 * can use pm
				 */
				
				if( $groups_result['users_group_can_use_pm'] == true && !$this -> user['user_can_use_pm']){
					$this -> user['user_can_use_pm'] = true;
				}
				
				/**
				 * pm limit
				 */
				
				if( $groups_result['users_group_pm_limit']  > $this -> user['user_pm_limit']){
					$this -> user['user_pm_limit'] = $groups_result['users_group_pm_limit'];
					
				}
				
				/**
				 * can send mails to others
				 */
				
				if( $groups_result['users_group_can_email_members'] == true){
					$this -> user['user_can_send_mails'] = true;
				}
				
				/**
				 * can use topics
				 */
				
				if( $groups_result['users_group_can_have_topics'] == true && !$this -> user['user_deny_topics']){
					$this -> user['user_deny_topics'] = false;
				}
							
				/**
				 * can moderate
				 */
				
				if( $groups_result['users_group_can_moderate'] == true){
					$this -> user['user_is_global_mod'] = true;
					$this -> user['user_can_moderate'] = true;
				}
						
				/**
				 * can edit calendar
				 */
				
				if( $groups_result['users_group_can_edit_calendar'] == true){
					$this -> user['user_can_edit_calendar'] = true;
				}
						
				/**
				 * can use shoutbox
				 */
				
				if( $groups_result['users_group_shoutbox_access'] > $this -> user['user_shoutbox']){
					$this -> user['user_shoutbox'] = $groups_result['users_group_shoutbox_access'];
				}
				
				
				/**
				 * edit time limit
				 */
				
				if( $groups_result['users_group_edit_time_limit'] > $this -> user['user_edit_time_limit']){
					$this -> user['user_edit_time_limit'] = $groups_result['users_group_edit_time_limit'];
				}
				
				/**
				 * draw legend after edit
				 */
				
				if( $groups_result['users_group_draw_edit_legend'] == true){
					$this -> user['user_draw_edit_legend'] = true;
				}
				
				/**
				 * delete own topics
				 */
				
				if( $groups_result['users_group_delete_own_topics'] == true){
					$this -> user['user_delete_own_topics'] = true;
				}
				
				/**
				 * edit own topics
				 */
				
				if( $groups_result['users_group_change_own_topics'] == true){
					$this -> user['user_change_own_topics'] = true;
				}
				
				/**
				 * close own topics
				 */
				
				if( $groups_result['users_group_close_own_topics'] == true){
					$this -> user['user_close_own_topics'] = true;
				}			
				
				/**
				 * delete own posts
				 */
				
				if( $groups_result['users_group_delete_own_posts'] == true){
					$this -> user['user_delete_own_posts'] = true;
				}
				
				/**
				 * edit own posts
				 */
				
				if( $groups_result['users_group_edit_own_posts'] == true){
					$this -> user['user_edit_own_posts'] = true;
				}
				
				/**
				 * start surveys
				 */
				
				if( $groups_result['users_group_start_surveys'] == true){
					$this -> user['user_start_surveys'] = true;
				}
				
				/**
				 * vote in surveys
				 */
				
				if( $groups_result['users_group_vote_surveys'] == true){
					$this -> user['user_vote_surveys'] = true;
				}
				
				/**
				 * avoid flood
				 */
				
				if( $groups_result['users_group_avoid_flood'] == true){
					$this -> user['user_avoid_flood'] = true;
				}
								
				/**
				 * avoid badwords
				 */
				
				if( $groups_result['users_group_avoid_badwords'] == true){
					$this -> user['user_avoid_badwords'] = true;
				}
				
				/**
				 * avoid closed
				 */
				
				if( $groups_result['users_group_avoid_closed_topics'] == true){
					$this -> user['user_avoid_closed_topics'] = true;
				}
				
				/**
				 * see hidden users
				 */
				
				if( $groups_result['users_group_see_hidden'] == true){
					$this -> user['user_see_hidden'] = true;
				}
				
				/**
				 * can search
				 */
				
				if( $groups_result['users_group_search'] == true){
					$this -> user['user_search'] = true;
				}
				
				/**
				 * search limit
				 */
				
				if( $groups_result['users_group_search_limit'] > $this -> user['user_search_limit']){
					$this -> user['user_search_limit'] = $groups_result['users_group_search_limit'];
				}
				
				/**
				 * uploads limit
				 */

				if ( $group_id == $this -> user['user_main_group']){
				
					if( $groups_result['users_group_uploads_quota'] > $this -> user['user_uploads_quota']){
						$this -> user['user_uploads_quota'] = $groups_result['users_group_uploads_quota'];
					}
					
					/**
					 * uploads summary
					 */
					
					if( $groups_result['users_group_uploads_used'] > $this -> user['user_uploads_used']){
						$this -> user['user_uploads_used'] = $groups_result['users_group_uploads_used'];
					}
					
					/**
					 * uploads unit limit
					 */
					
					if( $groups_result['users_group_uploads_limit'] > $this -> user['user_uploads_limit']){
						$this -> user['user_uploads_limit'] = $groups_result['users_group_uploads_limit'];
					}
					
				}
			}
			
		}
		
	}
	
	/**
	 * builds up an list of masks for user
	 *
	 */
	
	function getMasks(){
		
		global $forums;
				
		foreach ( $forums -> forums_perms as $perm_ops){
		
			if ( in_array( $perm_ops['forums_acess_perms_id'], $this -> user_masks)){
			
				/**
				 * forums_access_show_forum
				 */
				
				if ( $this -> user_forums[ $perm_ops['forums_acess_forum_id']]['forums_access_show_forum'] != true && $perm_ops['forums_access_show_forum'] == true){
					
					$this -> user_forums[ $perm_ops['forums_acess_forum_id']]['forums_access_show_forum'] = $perm_ops['forums_access_show_forum'];
				
				}
			
				/**
				 * forums_access_show_topics
				 */
				
				if ( $this -> user_forums[ $perm_ops['forums_acess_forum_id']]['forums_access_show_topics'] != true && $perm_ops['forums_access_show_topics'] == true){
					
					$this -> user_forums[ $perm_ops['forums_acess_forum_id']]['forums_access_show_topics'] = $perm_ops['forums_access_show_topics'];
				
				}
				
				/**
				 * forums_access_reply_topics
				 */
				
				if ( $this -> user_forums[ $perm_ops['forums_acess_forum_id']]['forums_access_reply_topics'] != true && $perm_ops['forums_access_reply_topics'] == true){
					
					$this -> user_forums[ $perm_ops['forums_acess_forum_id']]['forums_access_reply_topics'] = $perm_ops['forums_access_reply_topics'];
				
				}
				
				/**
				 * forums_access_start_topics
				 */
				
				if ( $this -> user_forums[ $perm_ops['forums_acess_forum_id']]['forums_access_start_topics'] != true && $perm_ops['forums_access_start_topics'] == true){
					
					$this -> user_forums[ $perm_ops['forums_acess_forum_id']]['forums_access_start_topics'] = $perm_ops['forums_access_start_topics'];
				
				}
				
				/**
				 * forums_access_attachments_upload
				 */
				
				if ( $this -> user_forums[ $perm_ops['forums_acess_forum_id']]['forums_access_attachments_upload'] != true && $perm_ops['forums_access_attachments_upload'] == true){
					
					$this -> user_forums[ $perm_ops['forums_acess_forum_id']]['forums_access_attachments_upload'] = $perm_ops['forums_access_attachments_upload'];
				
				}
				
				/**
				 * forums_access_attachments_download
				 */
				
				if ( $this -> user_forums[ $perm_ops['forums_acess_forum_id']]['forums_access_attachments_download'] != true && $perm_ops['forums_access_attachments_download'] == true){
					
					$this -> user_forums[ $perm_ops['forums_acess_forum_id']]['forums_access_attachments_download'] = $perm_ops['forums_access_attachments_download'];
				
				}
			}
		}
			
	}
	
	/**
	 * promotes guest session to logged in user
	 */
	
	function promoteGuestSession( $user_id, $session_id, $as_hidden = false){
		
		global $mysql;
		
		$promotion_sql['users_session_user_id'] = $user_id;
		$promotion_sql['users_session_open_time'] = time();
		$promotion_sql['users_session_last_time'] = time();
		
		if ( $as_hidden)
			$promotion_sql['users_session_hidden'] = true;
		
		$mysql -> update( $promotion_sql, 'users_sessions', "`users_session_id` = '$session_id' AND `users_session_user_id` = '-1' AND `users_session_ip` = '".$this -> user_ip."'");
		
		$this -> user['user_id'] = $user_id;
				
		setUniCookie( 'uid', $user_id, false);
		
		/**
		 * delete other sessions from this ip
		 */
		
		$mysql -> delete( 'users_sessions', "`users_session_id` <> '$session_id' AND (`users_session_ip` = '".$this -> user_ip."' OR `users_session_user_id` = '$user_id')");
		
	}
	
	function newAdminSession( $user_id, $user_ip){
		
		global $mysql;
		
		/**
		 * prepare session query
		 */
		
		$session_id = md5( md5(time()).md5($this -> user_ip));
		
		$new_session_sql['admin_session_id'] = $session_id;
		$new_session_sql['admin_session_ip'] = $this -> user_ip;
		$new_session_sql['admin_session_key'] = md5( $user_ip.time());
		$new_session_sql['admin_session_user_id'] = $user_id;
		$new_session_sql['admin_session_open_time'] = time();
		$new_session_sql['admin_session_last_time'] = time();
		
		/**
		 * put new session into database
		 */
		
		$mysql -> insert( $new_session_sql, 'admins_sessions');
		
		/**
		 * kill rest sessions
		 */
		
		$mysql -> delete( 'admins_sessions', "`admin_session_id` <> '$session_id' AND `admin_session_user_id` = '$user_id' AND `admin_session_ip` = '".$this -> user_ip."'");
		
		/**
		 * set session values
		 */
		
		$_SESSION['admin_session_user_id'] = $user_id;
		$_SESSION['admin_session_id'] = $new_session_sql['admin_session_id'];
		$_SESSION['admin_session_key'] = $new_session_sql['admin_session_key'];
		
		/**
		 * now capture it
		 */
		
		$this -> getUser($user_id);
		
	}
	
	/**
	 * checks if form was already submitted
	 *
	 * @return unknown
	 */
	
	function checkForm(){
		
		$form_id = $_POST['form_id'];
		
		$sended_forms = array();
		$sended_forms = $_SESSION['sended_forms'];
		
		settype( $sended_forms, 'array');
		
		if ( in_array( $form_id, $sended_forms, true) || empty($form_id)){
			
			/**
			 * we already sended that form
			 */
			
			return false;
			
		}else{
			
			/**
			 * we havent sended that form
			 */
			
			$sended_forms[] = $form_id;
			$_SESSION['sended_forms'] = $sended_forms;
			
			return true;
			
		}
		
	}
	
	/**
	 * function checking, if user can see forum
	 */
	
	function canSeeForum( $forum_id){
		
		global $forums;
		
		$can_see_forum = $this -> user_forums[$forum_id]['forums_access_show_forum'];
				
		/**
		 * do pathfinding
		 */
				
		$current_location = $forums -> forums_list[ $forum_id]['forum_parent'];
	
		while ( $current_location != 0){
			
			/**
			 * check, if user can see element on a path
			 */
			
			if ( !$this -> user_forums[$current_location]['forums_access_show_forum'] || !$this -> user_forums[$current_location]['forums_access_show_topics']){
				
				/**
				 * we dont see simple element on a path, so we cant reach actual one
				 */
				
				$can_see_forum = false;	
				$current_location = 0;
			
			}else{
				
				$current_location = $forums -> forums_list[ $current_location]['forum_parent'];
					
			}	
		}
		
		return $can_see_forum;
	}
	
	/**
	 * function checking, if user can browse forum
	 */
	
	function canSeeTopics( $forum_id){
		
		global $forums;
		
		$can_see_topics = $this -> user_forums[$forum_id]['forums_access_show_topics'];
		
		/**
		 * do pathfinding
		 */
		
		if ( !$this -> canSeeForum( $forum_id))
			$can_see_topics = false;
		
		if ( $can_see_topics){
			
			/**
			 * do pathfinding
			 */
			
			$current_location = $forums -> forums_list[ $forum_id]['forum_parent'];
	
			while ( $current_location != 0){
				
				/**
				 * check, if user can see element on a path
				 */
				
				if ( !$this -> user_forums[$current_location]['forums_access_show_topics']){
					
					/**
					 * we dont see simple element on a path, so we cant reach actual one
					 */
					
					$can_see_topics = false;	
					$current_location = 0;
					
				}else{
					
					$current_location = $forums -> forums_list[ $current_location]['forum_parent'];
				
				}	
			}
			
		}
			
		return $can_see_topics;
	}
	
	/**
	 * function checking, if user can start new topics
	 */
	
	function canStartTopics( $forum_id){
		
		global $forums;
		
		$can_start_topics = $this -> user_forums[$forum_id]['forums_access_start_topics'];
		
		/**
		 * do pathfinding
		 */
		
		if ( !$this -> canSeeTopics( $forum_id) || !$this -> canReplyTopics( $forum_id))
			$can_start_topics = false;
		
		return $can_start_topics;
	}
	
	/**
	 * function checking, if user can reply existing topics
	 */
	
	function canReplyTopics( $forum_id){
		
		global $forums;
		
		$can_start_topics = $this -> user_forums[$forum_id]['forums_access_reply_topics'];
		
		/**
		 * do pathfinding
		 */
		
		if ( !$this -> canSeeTopics( $forum_id))
			$can_start_topics = false;
			
		return $can_start_topics;
	}
	
	/**
	 * function checking, if user can upload attachments
	 */
	
	function canUpload( $forum_id){
		
		global $forums;
		
		$can_start_topics = false;
		
		if ( $this -> user_forums[$forum_id]['forums_access_attachments_upload'] && $this -> user_forums[$forum_id]['forums_access_attachments_download'])
			$can_start_topics = true;
		
		return $can_start_topics;
	}
	
	/**
	 * function checking, if user can download attachments
	 */
	
	function canDownload( $forum_id){
		
		global $forums;
		
		$can_start_topics = $this -> user_forums[$forum_id]['forums_access_attachments_download'];
		
		return $can_start_topics;
	}
	
	/**
	 * function checking, if user is an mod
	 */
	
	function isMod( $forum_id){
		
		global $forums;
		
		$is_mod = false;
		
		if ( $this -> user['user_is_global_mod'])
			$is_mod = true;
		
		if ( !$is_mod){
			
			$moderating_users_list = $forums -> forums_mods[$forum_id]['users'];
			settype( $moderating_users_list, 'array');
			
			if ( key_exists( $this -> user['user_id'], $moderating_users_list))
				$is_mod = true;
			
		}
			
		/**
		 * cycle trought groups
		 */
		
		if ( !$is_mod){
			
			$moderating_groups_list = $forums -> forums_mods[$forum_id]['groups'];
			
			settype( $moderating_groups_list, 'array');
			
			foreach ( $moderating_groups_list as $group_id => $group_name){
				
				if ( in_array( $group_id, $this -> user_groups))
					$is_mod = true;
					
			}
		
		}
		
		/**
		 * we need to see forum
		 */
			
		if ( $is_mod && !$this -> canSeeTopics( $forum_id))
			$is_mod = false;
			
		/**
		 * is root
		 */
		
		return $is_mod;
		
	}
	
	function isBot(){
		
		global $settings;
		
		/**
		 * build up an list of bots
		 */
		
		$bots_list = array();
		
		if ( !defined( 'ACP')){
			
			$unparsed_bots_list = split( "\n", $settings['spiders_list']);
		
			foreach ( $unparsed_bots_list as $unparsed_bot){
				
				$bot_agent = trim( substr( $unparsed_bot, 0, strrpos( $unparsed_bot, ":")));
				$bot_name = trim( substr( $unparsed_bot, strrpos( $unparsed_bot, ":")+1));
				
				if ( strlen( $bot_agent) > 0 && strlen( $bot_name) > 0){
					if ( strstr( $_SERVER['HTTP_USER_AGENT'], $bot_agent) != false){
						
						return $bot_name;
						
					}
				}
			}
					
		}
		
		return false;
		
	}
	
	/**
	 * switch user style
	 */
	
	function switchStyle(){
		
		include( FUNCTIONS_GLOBALS);
		
		$new_style_id = $_GET['nstyle'];
		settype( $new_style_id, 'integer');
		
		$styles_query = $mysql -> query( "SELECT * FROM styles WHERE `style_id` = '$new_style_id'");

		if ( $style_result = mysql_fetch_array( $styles_query, MYSQL_ASSOC)){
			
			/**
			 * style exists, update it
			 */
								
			if ( $this -> user['user_id'] != -1){
					
				/**
				 * update style for member
				 */
				
				$update_user_sql['user_style'] = $new_style_id;
				$mysql -> update( $update_user_sql, 'users', "`user_id` = '".$this -> user['user_id']."'");
			
			}else{
				
				/**
				 * update style for guest
				 */
				
				setUniCookie( 'style', $new_style_id, true);
				
			}
			
			/**
			 * update in session
			 */
		
			$this -> user['user_style'] = $new_style_id;
			
		}
			
	}
	
	/**
	 * lang switch
	 */
	
	function switchLang(){
		
		include( FUNCTIONS_GLOBALS);
		
		$new_lang_id = $strings -> inputClear( $_GET['nlang'], false);
			
		$lang_query = $mysql -> query( "SELECT * FROM languages WHERE `lang_id` = '$new_lang_id'");

		if ( $lang_result = mysql_fetch_array( $lang_query, MYSQL_ASSOC)){
			
			/**
			 * lang exists, update it
			 */
					
			if ( $this -> user['user_id'] != -1){
				
				/**
				 * update suer language
				 */
					
				$update_user_sql['user_lang'] = $new_lang_id;
				$mysql -> update( $update_user_sql, 'users', "`user_id` = '".$this -> user['user_id']."'");
			
			}else{
			
				/**
				 * update guest language
				 */
					
				setUniCookie( 'language', $new_lang_id, true);
					
			}
			
		}
		
		/**
		 * and in session
		 */
		
		$this -> user['user_lang'] = $new_lang_id;
			
	}
	
}

?>