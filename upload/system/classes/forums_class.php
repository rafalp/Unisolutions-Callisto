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
|	Forums Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class forums{
	
	/**
	 * error message
	 *
	 */
	
	var $error = 0;
	
	var $forums_mods = array();
	
	/**
	 * CONSTRUCTOR DOES FEW THINGS:
	 * 1. build up an list of forums
	 */
	
	function __construct(){
		
		global $mysql;
		global $cache;
		
		$forums_list = $cache -> loadCache( 'forums');
		
		if ( gettype( $forums_list) != 'array'){
			
			$forums_query = $mysql -> query( "SELECT forum_id, forum_parent, forum_type, forum_name, forum_image, forum_info, forum_url, forum_locked, forum_increase_counter FROM forums");
			
			while ( $forums_result = mysql_fetch_array( $forums_query, MYSQL_ASSOC)){
				
				$forums_result = $mysql -> clear( $forums_result);
				
				$this -> forums_list[$forums_result['forum_id']] = array(
					'forum_parent' => $forums_result['forum_parent'],
					'forum_type' => $forums_result['forum_type'],
					'forum_name' => $forums_result['forum_name'],
					'forum_image' => $forums_result['forum_image'],
					'forum_info' => $forums_result['forum_info'],
					'forum_url' => $forums_result['forum_url'],
					'forum_increase_counter' => $forums_result['forum_increase_counter'],
					'forum_locked' => $forums_result['forum_locked']
				);
				
			}
			
			$cache -> saveCache( 'forums', $this -> forums_list);
			
		}else{
			
			$this -> forums_list = $forums_list;
			
		}
				
		/**
		 * mods list
		 */
		
		$mods_list = $cache -> loadCache( 'moderators');
		
		if ( gettype( $mods_list) != 'array'){
			
			$mods_query = $mysql -> query( "SELECT m.*, u.user_login, g.users_group_name FROM moderators m LEFT JOIN users u ON m.moderator_user_id = u.user_id LEFT JOIN users_groups g ON m.moderator_group_id = g.users_group_id");
			
			while ( $mods_result = mysql_fetch_array( $mods_query, MYSQL_ASSOC)){
				
				$mods_result = $mysql -> clear($mods_result);
				
				if ( $mods_result['moderator_user_id'] > 0)
					$this -> forums_mods[$mods_result['moderator_forum_id']]['users'][$mods_result['moderator_user_id']] = '<a href="'.ROOT_PATH.'index.php?act=user&user='.$mods_result['moderator_user_id'].'">'.$mods_result['user_login'].'</a>';
				
				if ( $mods_result['moderator_group_id'] > 0)
					$this -> forums_mods[$mods_result['moderator_forum_id']]['groups'][$mods_result['moderator_group_id']] = $mods_result['users_group_name'];
					
			}
			
			$cache -> saveCache( 'moderators', $this -> forums_mods);
			
		}else{
			
			$this -> forums_mods = $mods_list;
			
		}
		
		/**
		 * perms
		 */
		
		$perms_list = $cache -> loadCache( 'permissions');
		
		if ( gettype( $perms_list) != 'array'){
			
			$perms_query = $mysql -> query( "SELECT * FROM forums_access");
			
			while ( $perms_result = mysql_fetch_array( $perms_query, MYSQL_ASSOC)){
			
				$this -> forums_perms[] = $perms_result;
						
			}
			
			$cache -> saveCache( 'permissions', $this -> forums_perms);
			
		}else{
			
			$this -> forums_perms = $perms_list;
			
		}
		
		/**
		 * perms masks
		 */
		
		foreach ( $this -> forums_perms as $perm_ops)
			$this -> forums_perms_masks[$perm_ops['forums_acess_perms_id']] = true;

		/**
		 * prefixes
		 */
		
		$prefixes_list = $cache -> loadCache( 'prefixes');
		
		if ( gettype( $prefixes_list) != 'array'){
			
			$prefixes_query = $mysql -> query( "SELECT * FROM topics_prefixes ORDER BY topic_prefix_pos DESC, topic_prefix_name");
			
			while ( $prefixes_result = mysql_fetch_array( $prefixes_query, MYSQL_ASSOC)){
				
				$prefixes_result = $mysql -> clear( $prefixes_result);
				
				$this -> prefixes_list[$prefixes_result['topic_prefix_id']] = array(
					'topic_prefix_name' => $prefixes_result['topic_prefix_name'],
					'topic_prefix_html' => $prefixes_result['topic_prefix_html'],
					'topic_prefix_forums' => $prefixes_result['topic_prefix_forums'],
				);
				
			}
			
			$cache -> saveCache( 'prefixes', $this -> prefixes_list);
			
		}else{
			
			$this -> prefixes_list = $prefixes_list;
			
		}
			
	}
	
	/**
	 * fixes paths
	 */
	
	function fixPaths(){
		
		global $style;
		
		foreach ($this -> forums_list as $forum_id => $forum_ops) {
			
			$this -> forums_list[ $forum_id]['forum_image'] = str_ireplace( "{S:P}", $style -> style['path'], $this -> forums_list[ $forum_id]['forum_image']);
			$this -> forums_list[ $forum_id]['forum_image'] = str_ireplace( "{S:#}", $style -> style_id, $this -> forums_list[ $forum_id]['forum_image']);
			
		}
		
	}
	
	/**
	 * creates new forum
	 *
	 * @param string $forum_title
	 * @param string $forum_info
	 * @param int $forum_parent
	 * @param string $forum_url
	 * @param bool $forum_count_redirects
	 * @param bool $forum_increase_counter
	 * @param bool $forum_allow_bbcode
	 * @param bool $forum_allow_surveys
	 * @param bool $forum_allow_quick_reply
	 * @param int $forum_force_drawdate
	 * @param int $forum_force_ordering
	 * @param bool $forum_ordering_way
	 * @param bool $forum_locked
	 * @return bool
	 */
	
	function newForum( $forum_title, $forum_image, $forum_info, $forum_parent, $forum_category, $forum_guidelines, $forum_guidelines_url, $forum_url, $forum_count_redirects, $forum_increase_counter, $forum_allow_bbcode, $forum_allow_surveys, $forum_allow_quick_reply, $forum_force_ordering, $forum_ordering_way, $forum_prune, $forum_prune_days, $forum_locked, $overrite = 0, $actual_parent = 0){
		
		//include globals
		include(FUNCTIONS_GLOBALS);
		
		//begin
		$this -> error = 0;
		
		/**
		 * check title
		 */
		
		if ( empty( $forum_title))
			$this -> error = 1;
		
		/**
		 * check if forum parent exists
		 */
			
		if ( $forum_parent != 0 && $this -> error == 0){
						
			if ( !$this -> checkIfForumExists($forum_parent))
				$this -> error = 2;
		}
		
		/**
		 * check if we have errors
		 */
		
		if ( $this -> error == 0){
			
			/**
			 * no errors found, lets prepare to insert new forum into mysql
			 * start from parent and position
			 */
			
			$new_forum_sql['forum_parent'] = $forum_parent;
			
			if ( $overrite == 0 || $forum_parent != $actual_parent)
				$new_forum_sql['forum_pos'] = $this -> getNextFreePos( $forum_parent);
			
			/**
			 * now specify forum type forum type
			 * TYPES:
			 * 0 - CATEGORY
			 * 1 - FORUM
			 * 2 - REDIRECT
			 */
			
			if ( $forum_parent == 0 || $forum_category){
				
				/**
				 * forum is category
				 */
				
				$forum_type = 0;
				
			}else if ( !empty($forum_url)){
				
				/**
				 * forum is link
				 */
				
				$forum_type = 2;
				
			}else{
				
				/**
				 * forum is forum ;)
				 */

				$forum_type = 1;
							
			}
			
			$new_forum_sql['forum_type'] = $forum_type;
			
			/**
			 * now forum name
			 */
			
			$new_forum_sql['forum_name'] = $strings -> inputClear( $forum_title, false);
			$new_forum_sql['forum_image'] = $strings -> inputClear( $forum_image, false);
			
			/**
			 * now forum info
			 */
			
			$new_forum_sql['forum_info'] = $strings -> inputClear( $forum_info, false);
			
			/**
			 * guidelines
			 */
			
			$new_forum_sql['forum_guidelines'] = $strings -> inputClear( $forum_guidelines, false);
			$new_forum_sql['forum_guidelines_url'] = $strings -> inputClear( $forum_guidelines_url, false);
			
			/**
			 * forum url
			 */
			
			$new_forum_sql['forum_url'] = $strings -> inputClear( $forum_url, false);
			
			/**
			 * count forum redirects
			 */
			
			settype( $forum_count_redirects, 'bool');
			
			$new_forum_sql['forum_count_redirects'] = $forum_count_redirects;
			
			/**
			 * posting on forum increase counter
			 */
			
			settype( $forum_increase_counter, 'bool');
			
			$new_forum_sql['forum_increase_counter'] = $forum_increase_counter;
			
			/**
			 * posting on forum increase counter
			 */
			
			settype( $forum_allow_bbcode, 'bool');
			
			$new_forum_sql['forum_allow_bbcode'] = $forum_allow_bbcode;
			
			/**
			 * posting on forum increase counter
			 */
			
			settype( $forum_allow_surveys, 'bool');
			
			$new_forum_sql['forum_allow_surveys'] = $forum_allow_surveys;
			
			/**
			 * posting on forum increase counter
			 */
			
			settype( $forum_allow_quick_reply, 'bool');
			
			$new_forum_sql['forum_allow_quick_reply'] = $forum_allow_quick_reply;
						
			/**
			 * force ordering method...
			 */
			
			$proper_ordering_methods = array( 0, 1, 2, 3, 4, 5, 6);
			
			settype( $forum_force_ordering, 'integer');
			
			if ( !in_array( $forum_force_ordering, $proper_ordering_methods))
				$forum_force_ordering = 0;
			
			$new_forum_sql['forum_force_ordering'] = $forum_force_ordering;
			
			/**
			 * ...and direction
			 */
			
			settype( $forum_ordering_way, "bool");
			
			$new_forum_sql['forum_ordering_way'] = $forum_ordering_way;
			
			/**
			 * forum pruning
			 */
			
			settype( $forum_prune, "bool");
			
			$new_forum_sql['forum_pruning'] = $forum_prune;
			
			/**
			 * forum prune time
			 */			
			
			settype( $forum_prune_days, "integer");
			
			if ( $forum_prune_days < 0)
				$forum_prune_days = 0;
			
			$new_forum_sql['forum_prune_days'] = $forum_prune_days;
			
			/**
			 * is forum locked?
			 */
			
			settype( $forum_locked, "bool");
			
			$new_forum_sql['forum_locked'] = $forum_locked;
			
			/**
			 * insert it into mysql
			 */
			
			if ( $overrite == 0){
			
				$mysql -> insert( $new_forum_sql, 'forums');
			
			}else{
				
				$mysql -> update( $new_forum_sql, 'forums', "`forum_id`	= '$overrite'");
			
			}
		}
		
		/**
		 * send result
		 */
			
		if ( $this -> error == 0){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * set permissions for forum
	 *
	 * @param int $forum_id
	 * @return true
	 */
	
	function setForumPerms( $forum_id){
		
		//just one class
		global $mysql;
		global $cache;
		
		/**
		 * firstly, delete existing perms of forum
		 */
		
		$mysql -> delete( 'forums_access', "`forums_acess_forum_id` = '$forum_id'");
		$cache -> flushCache( 'permissions');
		
		/**
		 * select existing masks
		 */
		
		$masks_query = $mysql -> query("SELECT p.* FROM users_perms p");
	
		/**
		 * get perms
		 */
		
		$forum_access_show_forum = $_POST['forum_access_show_forum'];
		$forum_access_show_topics = $_POST['forum_access_show_topics'];
		$forum_access_reply_topics = $_POST['forum_access_reply_topics'];
		$forum_access_start_topics = $_POST['forum_access_start_topics'];
		$forum_access_attachments_upload = $_POST['forum_access_attachments_upload'];
		$forum_access_attachments_download = $_POST['forum_access_attachments_download'];
		
		while ( $masks_result = mysql_fetch_array( $masks_query, MYSQL_ASSOC)){
			
			//clear slachses
			$masks_result = $mysql -> clear( $masks_result);
			
			/**
			 * get current perms
			 */
			
			$this_forum_access_show_forum = $forum_access_show_forum[$masks_result['users_perm_id']];
			$this_forum_access_show_topics = $forum_access_show_topics[$masks_result['users_perm_id']];
			$this_forum_access_reply_topics = $forum_access_reply_topics[$masks_result['users_perm_id']];
			$this_forum_access_start_topics = $forum_access_start_topics[$masks_result['users_perm_id']];
			$this_forum_access_attachments_upload = $forum_access_attachments_upload[$masks_result['users_perm_id']];
			$this_forum_access_attachments_download = $forum_access_attachments_download[$masks_result['users_perm_id']];
			
			/**
			 * force types
			 */
			
			settype( $this_forum_access_show_forum, 'bool');
			settype( $this_forum_access_show_topics, 'bool');
			settype( $this_forum_access_reply_topics, 'bool');
			settype( $this_forum_access_start_topics, 'bool');
			settype( $this_forum_access_attachments_upload, 'bool');
			settype( $this_forum_access_attachments_download, 'bool');
			
			/**
			 * insert
			 */
			
			$new_perms_forum_access_sql['forums_acess_perms_id'] = $masks_result['users_perm_id'];
			$new_perms_forum_access_sql['forums_acess_forum_id'] = $forum_id;
			$new_perms_forum_access_sql['forums_access_show_forum'] = $this_forum_access_show_forum;
			$new_perms_forum_access_sql['forums_access_show_topics'] = $this_forum_access_show_topics;
			$new_perms_forum_access_sql['forums_access_reply_topics'] = $this_forum_access_reply_topics;
			$new_perms_forum_access_sql['forums_access_start_topics'] = $this_forum_access_start_topics;
			$new_perms_forum_access_sql['forums_access_attachments_upload'] = $this_forum_access_attachments_upload;
			$new_perms_forum_access_sql['forums_access_attachments_download'] = $this_forum_access_attachments_download;
			
			$mysql -> insert( $new_perms_forum_access_sql, 'forums_access');
			
		}
		
		/**
		 * always return true
		 */
		
		return true;	
		
	}
	
	/**
	 * returns list of forums, for use in forms
	 *
	 * @param int $parent_forum
	 * @return array
	 */
	
	function getForumsList( $except = 0){
		
		//include mysql
		global $mysql;
		
		/**
		 * create empty list
		 */
		
		$forums_list = array();
		$forums_found = array();
		
		/**
		 * do query
		 */
		
		$forums_query = $mysql -> query( "SELECT forum_id, forum_parent, forum_pos, forum_name FROM forums WHERE `forum_id` <> '$except' ORDER BY `forum_parent` DESC, `forum_pos`");
		
		while ( $forums_result = mysql_fetch_array( $forums_query, MYSQL_ASSOC)){
			
			/**
			 * for now, we will just simply build list of forums
			 */
			
			$forums_result = $mysql -> clear( $forums_result);
			
			$forums_found[$forums_result['forum_id']] = array(
				'forum_parent' => $forums_result['forum_parent'],
				'forum_pos' => $forums_result['forum_pos'],
				'forum_name' => $forums_result['forum_name']
			);
			
		}
		
		foreach ( $forums_found as $forum_id => $forum_props){
			
			
			$base_code = $forums_list[$forum_id];
			
			$forum_lvl = $forum_props['forum_parent'];
			
			$forum_lvl_prefix = '';
			
			while ( $forum_lvl != 0){
				
				$forum_lvl_prefix .= '--';
				
				$forum_lvl = $forums_found[$forum_lvl]['forum_parent'];
				
			}
			
			$base_code = "\n".$forum_id.':'.$forum_lvl_prefix.$forum_props['forum_name'].$base_code;
			
			
			$forums_list[$forum_props['forum_parent']] .= $base_code;
			
		}
		
		/**
		 * secound cycle
		 */
		
		$pre_ready_forums = split( "\n", $forums_list[0]);
		
		foreach ( $pre_ready_forums as $forum_txt){
			
			$forum_id = substr( $forum_txt, 0, strpos( $forum_txt, ":"));
			$forum_name = substr( $forum_txt, strpos( $forum_txt, ":")+1 );
			
			$forums_list_final[$forum_id] = $forum_name;
			
		}
		
		/**
		 * return list
		 */
		
		return $forums_list_final;
		
	}
	
	function checkIfForumExists( $forum_id){
		
		global $mysql;
		
		$forums_query = $mysql -> query( "SELECT forum_id FROM forums WHERE `forum_id` = '$forum_id'");

		if ( $result = mysql_fetch_array( $forums_query, MYSQL_ASSOC)){
			
			return true;
			
		}else{
			
			return false;
			
		}
		
	}
	
	function forumUp( $id){
				
		global $mysql;
			
		/**
		 * firstly, we will chceck element position
		 */
			
		$query = $mysql -> query( "SELECT `forum_pos`, `forum_parent` FROM forums WHERE `forum_id` = '$id'");			
			
		if($result = mysql_fetch_array( $query, MYSQL_ASSOC))
			$actual_pos = $result['forum_pos'];
			$parent = $result['forum_parent'];
				
		/**
		 * now position highest than actual
		 */
			
		$query = $mysql -> query( "SELECT `forum_id`, `forum_pos` FROM forums WHERE `forum_pos` < '$actual_pos' AND `forum_parent` = '$parent' ORDER BY `forum_pos` DESC LIMIT 1");			
			
		if($result = mysql_fetch_array( $query, MYSQL_ASSOC)){
			
			$next_id = $result['forum_id'];
			$next_pos = $result['forum_pos'];
			
		}
			
		/**
		 * check, if module is found 
		 */
			
		if( isset($next_id)){
				
			$mysql -> query( "UPDATE forums SET `forum_pos` = '$next_pos' WHERE `forum_id` = '$id'");
			$mysql -> query( "UPDATE forums SET `forum_pos` = '$actual_pos' WHERE `forum_id` = '$next_id'");
				
		}					
	}
		
	function forumDown( $id){
			
		global $mysql;
		
		/**
		 * firstly, we will chceck element position
		 */
			
		$query = $mysql -> query( "SELECT `forum_pos`, `forum_parent` FROM forums WHERE `forum_id` = '$id'");			
			
		if($result = mysql_fetch_array( $query, MYSQL_ASSOC)){
			
			$actual_pos = $result['forum_pos'];
			$parent = $result['forum_parent'];
			
		}	
				
		/**
		 * now position highest than actual
		 */
			
		$query = $mysql -> query( "SELECT `forum_id`, `forum_pos` FROM forums WHERE `forum_pos` > '$actual_pos'  AND `forum_parent` = '$parent' ORDER BY `forum_pos` LIMIT 1");			
			
		if($result = mysql_fetch_array( $query, MYSQL_ASSOC)){
			
			$next_id = $result['forum_id'];
			$next_pos = $result['forum_pos'];
			
		}
			
		/**
		 * check, if module is found 
		 */
			
		if( isset($next_id)){
				
			$mysql -> query( "UPDATE forums SET `forum_pos` = '$next_pos' WHERE `forum_id` = '$id'");
			$mysql -> query( "UPDATE forums SET `forum_pos` = '$actual_pos' WHERE `forum_id` = '$next_id'");
				
		}					
	}
	
	function getNextFreePos( $forum_id){
		
		global $mysql;
			
		$forums_query = $mysql -> query( "SELECT `forum_pos` FROM forums WHERE `forum_parent` = '$forum_id' ORDER BY `forum_pos` DESC LIMIT 1");		
		
		if ( $result = mysql_fetch_array( $forums_query, MYSQL_ASSOC)){

			$free_pos = $result['forum_pos'] + 1;
			
		}else{

			$free_pos = 0;
		
		}
			
		return $free_pos;
		
	}
	
	/**
	 * resynchronize marked forum
	 *
	 */
	
	function forumResynchronise( $forum_id){
		
		global $mysql;
		
		$forum_topics_num = 0;
		$forum_posts_num = 0;
		$forum_last_topic_id = 0;
		$forum_last_topic_time = 0;
		$forum_last_topic_author = 0;
		$forum_last_topic_author_id = 0;
		
		settype( $forum_id, 'integer');
		
		/**
		 * select topics
		 */
		
		$forum_query = $mysql -> query( "SELECT topic_id, topic_name, topic_last_time, topic_last_user, topic_last_user_name, topic_posts_num FROM `topics` WHERE `topic_forum_id` = '$forum_id' ORDER BY `topic_last_time`");
		
		while ( $forum_result = mysql_fetch_array( $forum_query, MYSQL_ASSOC)){
			
			$forum_topics_num ++;
			$forum_posts_num = $forum_posts_num + $forum_result['topic_posts_num'] + 1;
			
			$forum_last_topic_id = $forum_result['topic_id'];
			$forum_last_topic_time = $forum_result['topic_last_time'];
			$forum_last_topic_author = $forum_result['topic_last_user'];
			$forum_last_topic_author_id = $forum_result['topic_last_user_name'];
			
		}
		
		/**
		 * update
		 */
		
		$forum_update_sql['forum_threads'] = $forum_topics_num;
		$forum_update_sql['forum_posts'] = $forum_posts_num;
		$forum_update_sql['forum_last_topic'] = $forum_last_topic_id;
		$forum_update_sql['forum_last_topic_time'] = $forum_last_topic_time;
		$forum_update_sql['forum_last_poster_id'] = $forum_last_topic_author;
		$forum_update_sql['forum_last_poster_name'] = $forum_last_topic_author_id;
		
		$mysql -> update( $forum_update_sql, 'forums', "`forum_id` = '$forum_id'");
		
	}
	
	/**
	 * resynchronize marked topic
	 *
	 */
	
	function topicResynchronise( $topic_id){
		
		global $mysql;
		
		$topic_posts_num = 0;
		$topic_last_post_id = 0;
		$topic_last_post_time = 0;
		$topic_last_post_author = 0;
		$topic_last_post_author_id = 0;
		
		settype( $topic_id, 'integer');
		
		$topic_attachments = false;
		
		/**
		 * select topics
		 */
		
		$forum_query = $mysql -> query( "SELECT post_id, post_time, post_author, post_author_name, post_has_attachments FROM `posts` WHERE `post_topic` = '$topic_id' ORDER BY `post_time`");
		
		while ( $forum_result = mysql_fetch_array( $forum_query, MYSQL_ASSOC)){
			
			$topic_posts_num ++;
			
			$topic_last_post_id = $forum_result['post_id'];
			$topic_last_post_time = $forum_result['post_time'];
			$topic_last_post_author_id = $forum_result['post_author'];
			$topic_last_post_author = $forum_result['post_author_name'];
			
			if ( $forum_result['post_has_attachments'])
				$topic_attachments = true;
				
		}
		
		/**
		 * update
		 */
		
		$topic_update_sql['topic_posts_num'] = $topic_posts_num - 1;
		$topic_update_sql['topic_last_time'] = $topic_last_post_time;
		$topic_update_sql['topic_last_user'] = $topic_last_post_author_id;
		$topic_update_sql['topic_last_user_name'] = $topic_last_post_author;
		$topic_update_sql['topic_attachments'] = $topic_attachments;
				
		$mysql -> update( $topic_update_sql, 'topics', "`topic_id` = '$topic_id'");
		
	}
	
	function getErrorMessage(){
		
		global $language;
		
		return $language -> getString( 'forums_interface_error_'.$this -> error);
		
	}
	
	/**
	 * draws forums list
	 *
	 * @param int $forum_id
	 */
	
	function drawForumsList( $show_forum_id = 0){
		
		/**
		 * include globals
		 */
		
		include( FUNCTIONS_GLOBALS);
				
		/**
		 * create generated code var
		 */
		
		$generated_code = '';
				
		/**
		 * build up an list of boards
		 */
		
		if ( defined( 'SIMPLE_MODE')){
			
			$forums_list = array();
			$forums_found = array();
			
			$forums_query = $mysql -> query( "SELECT forum_id, forum_parent, forum_pos, forum_name, forum_type FROM forums WHERE `forum_id` <> '$except' ORDER BY `forum_parent` DESC, `forum_pos`");
		
			while ( $forums_result = mysql_fetch_array( $forums_query, MYSQL_ASSOC)){
				
				/**
				 * for now, we will just simply build list of forums
				 */
				
				$forums_result = $mysql -> clear( $forums_result);
				
				$forums_found[$forums_result['forum_id']] = array(
					'forum_parent' => $forums_result['forum_parent'],
					'forum_pos' => $forums_result['forum_pos'],
					'forum_name' => $forums_result['forum_name'],
					'forum_type' => $forums_result['forum_type']
				);
				
			}
					
		}else{
			
			$found_boards = array();
			$found_topics = array();
			$posts_counters = array();
			/**
			 * get forums reads
			 */
			
			$reads_list = array();
			
			if ( $session -> user['user_id'] != -1){
				
				$reads_query = $mysql -> query( "SELECT * FROM forums_reads WHERE `forums_read_user` = '".$session -> user['user_id']."'");
				while ( $reads_result = mysql_fetch_array( $reads_query, MYSQL_ASSOC)) {
				
					$reads_list[$reads_result['forums_read_forum']] = $reads_result['forums_read_time'];
					
				}
			}
			
			$boards_query = $mysql -> query( "SELECT f.*, t.*, u.user_id, u.user_login, g.users_group_prefix, g.users_group_suffix, lu.user_main_group AS topic_starter_main_group, lu.user_other_groups AS topic_starter_other_groups
			FROM forums f
			LEFT JOIN topics t ON f.forum_last_topic = t.topic_id
			LEFT JOIN users u ON f.forum_last_poster_id = u.user_id
			LEFT JOIN users_groups g ON g.users_group_id = u.user_main_group
			LEFT JOIN users lu ON t.topic_start_user = lu.user_id
			ORDER BY f.forum_parent, f.forum_pos");		
		
			while ( $board_result = mysql_fetch_array( $boards_query, MYSQL_ASSOC)) {
				
				//clear result
				$board_result = $mysql -> clear( $board_result);
				
				$board_result['forum_image'] = str_replace( "{S:P}", $style -> style['path'], $board_result['forum_image']);
				$board_result['forum_image'] = str_replace( "{S:#}", $style -> style_id, $board_result['forum_image']);
							
				/**
				 * before add, check perms
				 */
				
				if ( $session -> canSeeForum($board_result['forum_id'])){
					
					/**
					 * build up main table row
					 */
					
					$generated_parents[$board_result['forum_parent']][] = $board_result['forum_id'];
					
					/**
					 * build up subforum row
					 */
					
					if ( $session -> user['user_id'] != -1){
						
						if ( !key_exists( $board_result['forum_id'], $reads_list))
							$reads_list[$board_result['forum_id']] = 0;
						
						if ( $reads_list[$board_result['forum_id']] < $board_result['forum_last_topic_time']){
						
							/**
							 * forum unread
							 */
							
							$force_read[$board_result['forum_parent']] = true;
							
							if ( $board_result['forum_locked']){
							
								$forum_image = $style -> drawImage( 'small_forum_new', $language -> getString( 'forum_new_posts_closed'));
							
							}else{
								
								$forum_image = $style -> drawImage( 'small_forum_new', $language -> getString( 'forum_new_posts'));
							
							}
							
						}else{
							
							/**
							 * forum is read
							 */
							
							if ( $board_result['forum_locked']){
						
								$forum_image = $style -> drawImage( 'small_forum', $language -> getString( 'forum_no_new_posts_closed'));
							
							}else{
								
								$forum_image = $style -> drawImage( 'small_forum', $language -> getString( 'forum_no_new_posts'));
							
							}
						
						}
						
					}else{
						
						/**
						 * user is guest, so forums are always unread
						 */
						
						if ( $board_result['forum_locked']){
						
							$forum_image = $style -> drawImage( 'small_forum', $language -> getString( 'forum_no_new_posts_closed'));
						
						}else{
							
							$forum_image = $style -> drawImage( 'small_forum', $language -> getString( 'forum_no_new_posts'));
						
						}
					}
						
					$generated_subforums[$board_result['forum_parent']][] = $forum_image.' <a href="'.ROOT_PATH.'index.php?act=forum&forum='.$board_result['forum_id'].'">'.$board_result['forum_name'].'</a>';
				
					/**
					 * clear topic
					 */
					
					$last_topic_author_groups = array();
					$last_topic_author_groups = split( ",", $board_result['topic_starter_mother_groups']);
					$last_topic_author_groups[] = $board_result['topic_starter_main_group'];
					
					if ( !$users -> cantCensore( $last_topic_author_groups)){
						
						$board_result['topic_name'] = $strings -> censore( $board_result['topic_name']);
						
					}
					
					/**
					 * ad forums now
					 */
					
					$found_boards[$board_result['forum_id']] = $board_result;
									
				}
						
			}
		}
				
		/**
		 * begin script
		 */
		
		if ( defined( "SIMPLE_MODE")){
			
			/**
			 * simple mode list
			 */
			
			$forums_drawed = false;
			
			foreach ( $forums_found as $forum_id => $forum_props){
			
				$base_code = $forums_list[$forum_id];
				
				$forum_lvl = $forum_props['forum_parent'];
				
				$forum_lvl_prefix = '';
				
				while ( $forum_lvl != 0){
					
					$forum_lvl_prefix .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					
					$forum_lvl = $forums_found[$forum_lvl]['forum_parent'];
					
				}
				
				if ( $forum_props['forum_type'] == 0){
					$base_code = "\n".$forum_id.':'.$forum_lvl_prefix.'<b>'.$forum_props['forum_name'].'</b><br />'.$base_code;
				}else{
					$base_code = "\n".$forum_id.':'.$forum_lvl_prefix.'<a href="'.ROOT_PATH.SIMPLE_PATH.'index.php?act=forum&forum='.$forum_id.'">'.$forum_props['forum_name'].'</a><br />'.$base_code;
				}
				
				$forums_list[$forum_props['forum_parent']] .= $base_code;
				
			}
			
			/**
			 * secound cycle
			 */
			
			$pre_ready_forums = split( "\n", $forums_list[0]);
			
			foreach ( $pre_ready_forums as $forum_txt){
				
				$forum_id = substr( $forum_txt, 0, strpos( $forum_txt, ":"));
				$forum_name = substr( $forum_txt, strpos( $forum_txt, ":")+1 );
				
				$forums_list_final[$forum_id] = $forum_name;
				
			}

			/**
			 * perms clear
			 */
							
			foreach ( $forums_list_final as $forum_id => $forum_name){
				
				if ( $session -> canSeeTopics( $forum_id)){
					
					$generated_code .= $forum_name;
					
				}
				
			}
			
		}else{
			
			/**
			 * normal list
			 * first cycle, for forums last topics
			 */	
			
			foreach ( $found_boards as $forum_id => $forum_ops){
				
				/**
				 * we have to have access to child
				 */

				if ( $session -> canSeeTopics( $forum_id)){
					
					/**
					 * check if we have never topic than our parent has
					 */
					
					if ( $forum_ops['forum_last_topic_time'] > $found_topics[$forum_ops['forum_parent']]['time']){
						
						/**
						 * set parent read to new
						 */
						
						$last_post_author = "";
											
						if ( $forum_ops['forum_last_poster_id'] == -1){
							
							$last_post_author = $forum_ops['users_group_prefix'].$forum_ops['forum_last_poster_name'].$forum_ops['users_group_suffix'];
							
						}else{
							
							$last_post_author = '<a href="'.ROOT_PATH.'index.php?act=user&user='.$forum_ops['forum_last_poster_id'].'">'.$forum_ops['users_group_prefix'].$forum_ops['forum_last_poster_name'].$forum_ops['users_group_suffix'].'</a>';
																		
						}
						
						if ( $settings['forums_draw_last_topics']){
						
							$topic_prefix = $this -> getPrefixHTML( $forum_ops['topic_prefix'], $forum_id);
							
							if ( strlen( $topic_prefix) == 0){
														
								if ( $forum_ops['topic_type'] == 1){
									
									$topic_prefix = '<b>'.$settings['forum_stick_prefix'].':</b> ';
								
								}else if ($forum_ops['topic_survey']){
									
									$topic_prefix = '<b>'.$settings['forum_survey_prefix'].':</b> ';
									
								}else{
									
									$topic_prefix = '';
									
								}
								
							}else{
								
								$topic_prefix .= ' ';
									
							}
							
							/**
							 * clear topic
							 */
							
							$topic_name_c = $this -> cutTopicName( $forum_ops['topic_name']);
																			
							/**
							 * insert
							 */
							
							$last_post_info = '<a href="'.ROOT_PATH.'index.php?act=topic&topic='.$forum_ops['topic_id'].'&p='.ceil(($forum_ops['topic_posts_num'] + 1) / $settings[ 'forum_posts_per_page'] ).'#post'.$forum_ops['topic_last_post_id'].'" title="'.$language -> getString( 'topic_goto_last').': '.$forum_ops['topic_name'].'">'.$style -> drawImage( 'goto', $language -> getString( 'topic_goto_last')).' '.$topic_prefix.$topic_name_c.'</a><br />
							<b>'.$language -> getString( 'forums_last_post_autor').': </b>'.$last_post_author.'<br />
							<b>'.$language -> getString( 'forums_last_post_time').': </b>'.$time -> drawDate( $forum_ops['forum_last_topic_time']);
						
						}else{
							
							$last_post_info = '<b>'.$language -> getString( 'forums_last_post_autor').': </b>'.$last_post_author.'<br />
							<b>'.$language -> getString( 'forums_last_post_time').': </b>'.$time -> drawDate( $forum_ops['forum_last_topic_time']);
							
						}
						
						$found_topics[$forum_ops['forum_parent']] = array(
							'html' => $last_post_info,
							'time' => $forum_ops['forum_last_topic_time']);
						
					}
					
					/**
					 * and stats
					 */
											
					$posts_counters[$forum_id]['p'] = $forum_ops['forum_posts'];
					$posts_counters[$forum_id]['t'] = $forum_ops['forum_threads'];
					
					$posts_counters[$forum_ops['forum_parent']]['p'] += $posts_counters[ $forum_id]['p'];
					$posts_counters[$forum_ops['forum_parent']]['t'] += $posts_counters[ $forum_id]['t'];
											
				}
				
			}
			
			/**
			 * add java
			 */
			
			$generated_code = '';
			
			if ( $settings['forums_list_look'] == 1 && $show_forum_id == 0){
				
				$boards_one_list = new form();
				$boards_one_list -> openOpTable();
				$boards_one_list -> addToContent( '<tr>
					<th colspan="2">'.$language -> getString( 'forums_name').'</th>');
				
				if ( $settings['forums_stats_draw'] == 1 || $settings['forums_stats_draw'] == 3 )
					$boards_one_list -> addToContent( '<th style="width: 100px; text-align: center">'.$language -> getString( 'forums_topics').'</th>');
					
				if ( $settings['forums_stats_draw'] == 2 || $settings['forums_stats_draw'] == 3 )
					$boards_one_list -> addToContent( '<th style="width: 100px; text-align: center">'.$language -> getString( 'forums_posts').'</th>');
			
				if ( $settings['forums_stats_draw'] == 4 )
					$boards_one_list -> addToContent( '<th style="width: 100px; text-align: center">'.$language -> getString( 'forums_topics').'/'.$language -> getString( 'forums_posts').'</th>');
				
				$boards_one_list -> addToContent( '<th style="width: 300px; text-align: center">'.$language -> getString( 'forums_last_post').'</th>
				</tr>');
			}
			
			/**
			 * go trought generated parents
			 */
			
			$forums_drawed = false;
			
			foreach ( $found_boards as $forum_id => $forum_ops){
				
				/**
				 * check if forum is in our cat
				 */
				
				if ( ($forum_ops['forum_parent'] == $show_forum_id && $show_forum_id == 0) || ($forum_ops['forum_id'] == $show_forum_id)){
					
					/**
					 * check if cat is empty
					 */
					
					if ( key_exists( $forum_id, $generated_parents)){
						
						$forums_drawed = true;
						
						/**
						 * open cat block
						 */
						
						if ( $settings['forums_list_look'] == 0 || $show_forum_id > 0){
	
							$category_block = new form();	
							$category_block -> addToContent( '<div id="cat_block_'.$forum_id.'">');	
							$category_block -> openOpTable();
							$category_block -> addToContent( '<tr>
								<th colspan="2">'.$language -> getString( 'forums_name').'</th>');
						
							if ( $settings['forums_stats_draw'] == 1 || $settings['forums_stats_draw'] == 3 )
								$category_block -> addToContent( '<th style="width: 100px; text-align: center">'.$language -> getString( 'forums_topics').'</th>');
								
							if ( $settings['forums_stats_draw'] == 2 || $settings['forums_stats_draw'] == 3 )
								$category_block -> addToContent( '<th style="width: 100px; text-align: center">'.$language -> getString( 'forums_posts').'</th>');
						
							if ( $settings['forums_stats_draw'] == 4 )
								$category_block -> addToContent( '<th style="width: 100px; text-align: center">'.$language -> getString( 'forums_topics').'/'.$language -> getString( 'forums_posts').'</th>');
							
							$category_block -> addToContent( '<th style="width: 300px; text-align: center">'.$language -> getString( 'forums_last_post').'</th>
								</tr>');
							
						}else{
							
							$boards_sub_lists = '';
							
						}
						
						/**
						 * now cycle trought childs
						 */
						
						$forum_childs = array();
						$forum_childs = $generated_parents[$forum_id];
											
						foreach ( $forum_childs as $child_id){
							
							/**
							 * few things will be the same for all boards
							 */
							
							$forum_info = '';
							
							if ( strlen( $found_boards[$child_id]['forum_info']) > 0)
									$forum_info = '<br />'.$strings -> parseBB( nl2br( $found_boards[$child_id]['forum_info']), true, true);
							
							/**
							 * subcats
							 */
									
							$forum_subcats = '';
							
							if ( key_exists( $child_id, $generated_subforums) && ($settings['forums_draw_subs'] == 1 || ( $settings['forums_draw_subs'] == 2 && $show_forum_id == 0) || ( $settings['forums_draw_subs'] == 3 && $show_forum_id != 0))){
								
								$forum_subcats = '<br /><b>'.$language -> getString( 'forums_subforums').':</b> '.join( ", ", $generated_subforums[$child_id]);
								
							}
							
							/**
							 * check forum type
							 */
							
							switch ( $found_boards[$child_id]['forum_type']){
							
								case 0:
									
									/**
									 * category
									 */
									
									if ( $session -> canSeeTopics($child_id)){
									
										/**
										 * we will cycle trught subforums, trying to draw last topic in this forum.
										 */
										
										$topic_found = false;
										
										
										if ($found_boards[$child_id]['forum_threads'] == 0 && strlen( $found_topics[$child_id]['time']) == 0){
											
											/**
											 * forum is empty
											 */
										
											$last_post_info = '<i>'.$language -> getString( 'forums_last_post_none').'</i>';
										
										}else{
											
											/**
											 * forum contains some data
											 */
											
											if ( $found_topics[$child_id]['time'] > $found_boards[$child_id]['forum_last_topic_time']){
												
												$last_post_info = $found_topics[$child_id]['html'];
												$topic_found = true;
												
											}
											
										}
										
										if ( !$topic_found){
											
											/**
											 * no last topic in subforums
											 */
											
											if ($found_boards[$child_id]['forum_threads'] == 0){
											
												/**
												 * forum is empty
												 */
											
												$last_post_info = '<i>'.$language -> getString( 'forums_last_post_none').'</i>';
											
											}else{
												
												/**
												 * forum contains some data
												 */
												
												$last_post_author = "";
												
												if ( $found_boards[$child_id]['forum_last_poster_id'] == -1){
													
													$last_post_author = $found_boards[$child_id]['users_group_prefix'].$found_boards[$child_id]['forum_last_poster_name'].$found_boards[$child_id]['users_group_suffix'];
													
												}else{
													
													$last_post_author = '<a href="'.ROOT_PATH.'index.php?act=user&user='.$found_boards[$child_id]['forum_last_poster_id'].'">'.$found_boards[$child_id]['users_group_prefix'].$found_boards[$child_id]['forum_last_poster_name'].$found_boards[$child_id]['users_group_suffix'].'</a>';
																								
												}
												
												if ( $settings['forums_draw_last_topics']){
						
													$topic_prefix = $this -> getPrefixHTML( $found_boards[$child_id]['topic_prefix'], $child_id);
	
													if ( strlen( $topic_prefix) == 0){
														
														if ( $found_boards[$child_id]['topic_type'] == 1){
															
															$topic_prefix = '<b>'.$settings['forum_stick_prefix'].':</b> ';
														
														}else if ($found_boards[$child_id]['topic_survey']){
															
															$topic_prefix = '<b>'.$settings['forum_survey_prefix'].':</b> ';
															
														}else{
															
															$topic_prefix = '';
															
														}
														
													}else{
													
														$topic_prefix .= ' ';
															
													}
													
													/**
													 * clear topic
													 */
													
													$topic_name_c = $this -> cutTopicName( $found_boards[$child_id]['topic_name']);
													
													/**
													 * insert
													 */
													
													$last_post_info = '<a href="'.ROOT_PATH.'index.php?act=topic&topic='.$found_boards[$child_id]['topic_id'].'&p='.ceil(($found_boards[$child_id]['topic_posts_num'] + 1) / $settings[ 'forum_posts_per_page'] ).'#post'.$found_boards[$child_id]['topic_last_post_id'].'" title="'.$language -> getString( 'topic_goto_last').': '.$found_boards[$child_id]['topic_name'].'">'.$style -> drawImage( 'goto', $language -> getString( 'topic_goto_last')).' '.$topic_prefix.$topic_name_c.'</a><br />
													<b>'.$language -> getString( 'forums_last_post_autor').': </b>'.$last_post_author.'<br />
													<b>'.$language -> getString( 'forums_last_post_time').': </b>'.$time -> drawDate( $found_boards[$child_id]['forum_last_topic_time']);
												
												}else{
													
													$last_post_info = '<b>'.$language -> getString( 'forums_last_post_autor').': </b>'.$last_post_author.'<br />
													<b>'.$language -> getString( 'forums_last_post_time').': </b>'.$time -> drawDate( $found_boards[$child_id]['forum_last_topic_time']);
													
												}
												
											}
											
										}
										
									}else{
										
										$last_post_info = '<i>'.$language -> getString( 'forums_last_post_secret').'</i>';
									
									}
									
									/**
									 * forum mods
									 */
									
									$forum_moderators = '';
									
									if ( key_exists( $child_id, $this -> forums_mods)){
										
										settype( $this -> forums_mods[$child_id]['users'], 'array');
										settype( $this -> forums_mods[$child_id]['groups'], 'array');
										
										$forum_mods = array_merge( $this -> forums_mods[$child_id]['users'], $this -> forums_mods[$child_id]['groups']);
										
										settype( $forum_mods, 'array');
										
										$forum_moderators = '<br /><i>'.$language -> getString( 'forums_mods').': '.join( ", ", $forum_mods).'</i>';
										
									}
									
									settype( $posts_counters[$child_id]['t'], 'integer');
									settype( $posts_counters[$child_id]['p'], 'integer');
									
									if ( strlen( $found_boards[$child_id]['forum_image']) > 0){
										
										$forum_image = '<img src="'.$found_boards[$child_id]['forum_image'].'" title="'.$language -> getString( 'forum_category').'" />';
										
									}else{
										
										$forum_image = $style -> drawImage( 'forum_category', $language -> getString( 'forum_category'));
										
									}
									
									/**
									 * draw
									 */
									
									$row_html = '<tr>
											<td class="opt_row1" style="width: 40px; text-align: center">'.$forum_image.'</td>
											<td class="opt_row2"><a href="'.ROOT_PATH.'index.php?act=forum&forum='.$child_id.'">'.$found_boards[$child_id]['forum_name'].'</a><span class="element_info">'.$forum_info.$forum_moderators.$forum_subcats.'</span></td>';
											
									if ( $settings['forums_stats_draw'] == 1 || $settings['forums_stats_draw'] == 3 )
										$row_html .= '<td class="opt_row1" style="width: 100px; text-align: center">'.$posts_counters[$child_id]['t'].'</td>';
										
									if ( $settings['forums_stats_draw'] == 2 || $settings['forums_stats_draw'] == 3 )
										$row_html .= '<td class="opt_row2" style="width: 100px; text-align: center">'.$posts_counters[$child_id]['p'].'</td>';
								
									if ( $settings['forums_stats_draw'] == 4 )
										$row_html .= '<td class="opt_row2" style="width: 200px; text-align: center">'.$posts_counters[$child_id]['t'].'/'.$posts_counters[$child_id]['p'].'</td>';
									
									$row_html .= '
											<td class="opt_row3" style="width: 300px">'.$last_post_info.'</td>
										</tr>';
									
									if ( $settings['forums_list_look'] == 0 || $show_forum_id > 0){
	
										$category_block -> addToContent( $row_html);
									
									}else{
										
										$boards_sub_lists .= $row_html;
										
									}
									
								break;
								
								case 1:
									
									/**
									 * standard forum
									 * define images
									 */
									
									if ( !key_exists( $child_id, $reads_list)){
										$reads_list[$child_id] = 0;
									}
									
									
									//set tiny image to empty
									$forum_tiny_image = '';
									
									if ( $found_boards[$child_id]['forum_locked']){
										
										/**
										 * forum is closed
										 */
										
										if ( $session -> user['user_id'] != -1){
											
											/**
											 * check read
											 */
											
											if ( $reads_list[$child_id] < $found_boards[$child_id]['forum_last_topic_time'] || $force_read[$child_id]){
												
												/**
												 * new posts
												 */
												
												if ( strlen( $found_boards[$child_id]['forum_image']) > 0){
													
													$forum_image = '<img src="'.$found_boards[$child_id]['forum_image'].'" title="'.$language -> getString( 'forum_new_posts_closed').'" />';
													$forum_tiny_image = $style -> drawImage( 'small_forum_new', $language -> getString( 'forum_new_posts_closed'));
													
												}else{
													
													$forum_image = $style -> drawImage( 'forum_closed_new', $language -> getString( 'forum_new_posts_closed'));
													
												}
												
											}else{
												
												/**
												 * no new posts
												 */
												
												if ( strlen( $found_boards[$child_id]['forum_image']) > 0){
													
													$forum_image = '<img src="'.$found_boards[$child_id]['forum_image'].'" title="'.$language -> getString( 'forum_no_new_posts_closed').'" />';
													$forum_tiny_image = $style -> drawImage( 'small_forum', $language -> getString( 'forum_no_new_posts_closed'));
													
												}else{
													
													$forum_image = $style -> drawImage( 'forum_closed', $language -> getString( 'forum_no_new_posts_closed'));
													
												}
																						
											}
											
										}else{
											
											/**
											 * user is guest, so forum is always unread
											 */
											
											if ( strlen( $found_boards[$child_id]['forum_image']) > 0){
												
												$forum_image = '<img src="'.$found_boards[$child_id]['forum_image'].'" title="'.$language -> getString( 'forum_no_new_posts_closed').'" />';
												$forum_tiny_image = $style -> drawImage( 'small_forum', $language -> getString( 'forum_no_new_posts_closed'));
													
											}else{
												
												$forum_image = $style -> drawImage( 'forum_closed', $language -> getString( 'forum_no_new_posts_closed'));
												
											}
											
										}
										
									}else{
										
										/**
										 * forum opened
										 */
										
										if ( $session -> user['user_id'] != -1){
											
											/**
											 * check read
											 */
											
											if ( $reads_list[$child_id] < $found_boards[$child_id]['forum_last_topic_time'] || $force_read[$child_id]){
												
												/**
												 * new posts
												 */
												
												if ( strlen( $found_boards[$child_id]['forum_image']) > 0){
													
													$forum_image = '<img src="'.$found_boards[$child_id]['forum_image'].'" title="'.$language -> getString( 'forum_new_posts').'" />';
													$forum_tiny_image = $style -> drawImage( 'small_forum_new', $language -> getString( 'forum_new_posts'));
													
												}else{
													
													$forum_image = $style -> drawImage( 'forum_new', $language -> getString( 'forum_new_posts'));
													
												}
												
											}else{
												
												/**
												 * no new posts
												 */
												
												if ( strlen( $found_boards[$child_id]['forum_image']) > 0){
													
													$forum_image = '<img src="'.$found_boards[$child_id]['forum_image'].'" title="'.$language -> getString( 'forum_no_new').'" />';
													$forum_tiny_image = $style -> drawImage( 'small_forum', $language -> getString( 'forum_no_new'));
													
												}else{
													
													$forum_image = $style -> drawImage( 'forum', $language -> getString( 'forum_no_new'));
													
												}
																						
											}
											
										}else{
											
											/**
											 * user is guest, so forum is always unread
											 */
											
											if ( strlen( $found_boards[$child_id]['forum_image']) > 0){
												
												$forum_image = '<img src="'.$found_boards[$child_id]['forum_image'].'" title="'.$language -> getString( 'forum_no_new').'" />';
													$forum_tiny_image = $style -> drawImage( 'small_forum', $language -> getString( 'forum_no_new'));
												
											}else{
												
												$forum_image = $style -> drawImage( 'forum', $language -> getString( 'forum_no_new'));
												
											}
										}
										
									}
									
									/**
									 * and last post info
									 * check if we have access
									 */
									
									if ( $session -> canSeeTopics($child_id)){
									
										/**
										 * we will cycle trught subforums, trying to draw last topic in this forum.
										 */
										
										$topic_found = false;
										
										
										if ($found_boards[$child_id]['forum_threads'] == 0 && strlen( $found_topics[$child_id]['time']) == 0){
											
											/**
											 * forum is empty
											 */
										
											$last_post_info = '<i>'.$language -> getString( 'forums_last_post_none').'</i>';
										
										}else{
											
											/**
											 * forum contains some data
											 */
											
											if ( $found_topics[$child_id]['time'] > $found_boards[$child_id]['forum_last_topic_time']){
												
												$last_post_info = $found_topics[$child_id]['html'];
												$topic_found = true;
												
											}
											
										}
										
										if ( !$topic_found){
											
											/**
											 * no last topic in subforums
											 */
											
											if ($found_boards[$child_id]['forum_threads'] == 0){
											
												/**
												 * forum is empty
												 */
											
												$last_post_info = '<i>'.$language -> getString( 'forums_last_post_none').'</i>';
											
											}else{
												
												/**
												 * forum contains some data
												 */
												
												$last_post_author = "";
												
												if ( $found_boards[$child_id]['forum_last_poster_id'] == -1){
													
													$last_post_author = $found_boards[$child_id]['users_group_prefix'].$found_boards[$child_id]['forum_last_poster_name'].$found_boards[$child_id]['users_group_suffix'];
													
												}else{
													
													$last_post_author = '<a href="'.ROOT_PATH.'index.php?act=user&user='.$found_boards[$child_id]['forum_last_poster_id'].'">'.$found_boards[$child_id]['users_group_prefix'].$found_boards[$child_id]['forum_last_poster_name'].$found_boards[$child_id]['users_group_suffix'].'</a>';
																								
												}
												
												if ( $settings['forums_draw_last_topics']){
													
													$topic_prefix = $this -> getPrefixHTML( $found_boards[$child_id]['topic_prefix'], $child_id);
	
													if ( strlen( $topic_prefix) == 0){
														
														if ( $found_boards[$child_id]['topic_type'] == 1){
															
															$topic_prefix = '<b>'.$settings['forum_stick_prefix'].':</b> ';
														
														}else if ($found_boards[$child_id]['topic_survey']){
															
															$topic_prefix = '<b>'.$settings['forum_survey_prefix'].':</b> ';
															
														}else{
															
															$topic_prefix = '';
															
														}
														
													}else{
													
														$topic_prefix .= ' ';
															
													}
													
													/**
													 * cut topic name
													 */
													
													$topic_name_c = $this -> cutTopicName( $found_boards[$child_id]['topic_name']);
													
													/**
													 * insert
													 */
													
													$last_post_info = '<a href="'.ROOT_PATH.'index.php?act=topic&topic='.$found_boards[$child_id]['topic_id'].'&p='.ceil(($found_boards[$child_id]['topic_posts_num'] + 1) / $settings[ 'forum_posts_per_page'] ).'#post'.$found_boards[$child_id]['topic_last_post_id'].'" title="'.$language -> getString( 'topic_goto_last').': '.$found_boards[$child_id]['topic_name'].'">'.$style -> drawImage( 'goto', $language -> getString( 'topic_goto_last')).' '.$topic_prefix.$topic_name_c.'</a><br />
													<b>'.$language -> getString( 'forums_last_post_autor').': </b>'.$last_post_author.'<br />
													<b>'.$language -> getString( 'forums_last_post_time').': </b>'.$time -> drawDate( $found_boards[$child_id]['forum_last_topic_time']);
												
												}else{
													
													$last_post_info = '<b>'.$language -> getString( 'forums_last_post_autor').': </b>'.$last_post_author.'<br />
													<b>'.$language -> getString( 'forums_last_post_time').': </b>'.$time -> drawDate( $found_boards[$child_id]['forum_last_topic_time']);
												
												}
													
											}
											
										}
										
									}else{
										
										$last_post_info = '<i>'.$language -> getString( 'forums_last_post_secret').'</i>';
									
									}
									
									/**
									 * forum mods
									 */
									
									$forum_moderators = '';
									
									if ( key_exists( $child_id, $this -> forums_mods)){
										
										settype( $this -> forums_mods[$child_id]['users'], 'array');
										settype( $this -> forums_mods[$child_id]['groups'], 'array');
										
										$forum_mods = array_merge( $this -> forums_mods[$child_id]['users'], $this -> forums_mods[$child_id]['groups']);
										
										settype( $forum_mods, 'array');
										
										$forum_moderators = '<br /><i>'.$language -> getString( 'forums_mods').': '.join( ", ", $forum_mods).'</i>';
										
									}
									
									settype( $posts_counters[$child_id]['t'], 'integer');
									settype( $posts_counters[$child_id]['p'], 'integer');
									
									//fix tiny image
									if ( strlen( $forum_tiny_image) > 0)
										$forum_tiny_image .= ' ';									
									
									/**
									 * draw
									 */
									
									$row_html = '<tr>
										<td class="opt_row1" style="width: 40px; text-align: center">'.$forum_image.'</td>
										<td class="opt_row2">'.$forum_tiny_image.'<a href="'.ROOT_PATH.'index.php?act=forum&forum='.$child_id.'">'.$found_boards[$child_id]['forum_name'].'</a><span class="element_info">'.$forum_info.$forum_moderators.$forum_subcats.'</span></td>';
									
									if ( $settings['forums_stats_draw'] == 1 || $settings['forums_stats_draw'] == 3 )
										$row_html .= '<td class="opt_row1" style="width: 100px; text-align: center">'.$posts_counters[$child_id]['t'].'</td>';
										
									if ( $settings['forums_stats_draw'] == 2 || $settings['forums_stats_draw'] == 3 )
										$row_html .= '<td class="opt_row2" style="width: 100px; text-align: center">'.$posts_counters[$child_id]['p'].'</td>';
								
									if ( $settings['forums_stats_draw'] == 4 )
										$row_html .= '<td class="opt_row1" style="width: 200px; text-align: center">'.$posts_counters[$child_id]['t'].'/'.$posts_counters[$child_id]['p'].'</td>';
									
									$row_html .= '<td class="opt_row3" style="width: 300px">'.$last_post_info.'</td>
										</tr>';
									
									if ( $settings['forums_list_look'] == 0 || $show_forum_id > 0){
	
										$category_block -> addToContent( $row_html);
									
									}else{
										
										$boards_sub_lists .= $row_html;
										
									}
									
								break;
								
								case 2:
											
									/**
									 * forum is redirect
									 */
									
									if ( $found_boards[$child_id]['forum_count_redirects']){
										$redirects = '<b>'.$language -> getString( 'forum_redirects_num').':</b> '.$found_boards[$child_id]['forum_redirects'];
									}else{
										$redirects = '<i>'.$language -> getString( 'forum_redirects_num_notcounting').'</i>';
									}
										
									if ( strlen( $found_boards[$child_id]['forum_image']) > 0){
										
										$forum_image = '<img src="'.$found_boards[$child_id]['forum_image'].'" title="'.$language -> getString( 'forum_url').'" />';
										
									}else{
										
										$forum_image = $style -> drawImage( 'forum_redirect', $language -> getString( 'forum_url'));
										
									}
									
									$colspan = 1;
									
									if ( $settings['forums_stats_draw'] != 0)
										$colspan ++;
									
									if ( $settings['forums_list_look'] == 0 || $show_forum_id > 0){
								
										$category_block -> addToContent( '<tr>
											<td class="opt_row1" style="width: 40px; text-align: center">'.$forum_image.'</td>
											<td class="opt_row2" ><a href="'.ROOT_PATH.'index.php?act=forum&forum='.$child_id.'">'.$found_boards[$child_id]['forum_name'].'</a><span class="element_info">'.$forum_info.$forum_subcats.'</span></td>
											<td class="opt_row1" colspan="'.$colspan.'">&nbsp;</td>
											<td class="opt_row3" style="width: 300px">'.$redirects.'</td>
										</tr>');
										
									}else{
										
										$boards_sub_lists .= '<tr>
											<td class="opt_row1" style="width: 40px; text-align: center">'.$forum_image.'</td>
											<td class="opt_row2"><a href="'.ROOT_PATH.'index.php?act=forum&forum='.$child_id.'">'.$found_boards[$child_id]['forum_name'].'</a><span class="element_info">'.$forum_info.$forum_subcats.'</span></td>
											<td class="opt_row1" colspan="'.$colspan.'">&nbsp;</td>
											<td class="opt_row3" style="width: 300px">'.$redirects.'</td>
										</tr>';
										
									}
									
								break;
							}
						}
						
						/**
						 * close tab
						 */
						
						if ( $settings['forums_list_look'] == 0 || $show_forum_id > 0){
						
							$category_block -> closeTable();
							$category_block -> addToContent( '</div>');
						
						}
						
						/**
						 * put code to generated one list
						 */
						
						if ( $show_forum_id == 0){
							$forum_name = $forum_ops['forum_name'];
						}else{
							
							$language -> setKey( 'category_of_subforums', $forum_ops['forum_name']);
							
							$forum_name = $language -> getString( 'forums_subforums_list');
						}
						
						
						if ( $settings['forums_list_look'] == 0 || $show_forum_id > 0){
	
							$forum_title = '<table border="0" cellpadding="0" cellspacing="0" style="width: 100%"><tr>
								<td>'.$forum_name.'<td>
								<td style="text-align: right"><a href="javascript:switchBlockDisplay( \'cat_block_'.$forum_id.'\', \'cat_img_'.$forum_id.'\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')"><div id="cat_img_'.$forum_id.'">'.$style -> drawImage( 'forum_collapse').'</div></a></td>
								</tr></table>';
							
							$generated_code .= $style -> drawFormBlock( $forum_title, $category_block -> display());
						
						}else{
							
							$forum_title = '<table border="0" cellpadding="0" cellspacing="0" style="width: 100%"><tr>
								<td>'.$forum_name.'<td>
								<td style="text-align: right"><a href="javascript:switchBlockDisplay(\'cat_block_'.$forum_id.'\', \'cat_img_'.$forum_id.'\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')"><div id="cat_img_'.$forum_id.'">'.$style -> drawImage( 'forum_collapse').'</div></a></td>
								</tr></table>';
							
							$boards_one_list -> addToContent( '<tr>
								<td class="opt_row5" colspan="5">'.$forum_title.'</td>
							</tr>');
							
							$boards_one_list -> addToContent( '<tbody id="cat_block_'.$forum_id.'">');
							$boards_one_list -> addToContent( $boards_sub_lists);
							$boards_one_list -> addToContent( '</tbody>');
						}
						
						$generated_java .= '							
							getBlockDisplayR( \'cat_block_'.$forum_id.'\', \'cat_img_'.$forum_id.'\', \''.ROOT_PATH.'styles/'.$style -> style['path'].'\')
							
						';
						
					}
					
				}
				
			}
			
			/**
			 * use alternative method
			 */
			
			if ( $settings['forums_list_look'] == 1 && $show_forum_id == 0 && $forums_drawed){
				
				
				$boards_one_list -> closeTable();
				
				$generated_code .= $style -> drawFormBlock( $language -> getString( 'forums_list'), $boards_one_list -> display());
			}
			
			/**
			 * if list is empty, draw message
			 */
			
			if ( $show_forum_id == 0 && !$forums_drawed)
				$generated_code .= $style -> drawBlock( $language -> getString( 'forums_list'), $language -> getString( 'forums_list_empty'));

			$generated_code .= '<script type="text/javascript">'.$generated_java.'</script>';
					
		}
				
		/**
		 * return our generation
		 */
				
		return $generated_code;
		
	}
	
	/**
	 * draws forums jumplist
	 */
	
	function drawForumsJumpList( $forum = 0){
		
		//include globals	
		include( FUNCTIONS_GLOBALS);
	
		/**
		 * get forums
		 */
		
		$forums_list = $this -> getForumsList();
		
		/**
		 * start drawing
		 */
		
		$jumplist = new form();
		$jumplist -> addToContent( '<form name="jumplist" action="'.ROOT_PATH.'index.php" TYPE="GET">');
		$jumplist -> hiddenValue( 'act', 'forum');
		$jumplist -> addToContent( '<select name="forum" onChange="jumpToForum()">');
		
		/**
		 * clear unaccessed
		 */
		
		
		
		foreach ( $forums_list as $forum_id => $forum_name){
			
			if ( $session -> canSeeTopics( $forum_id)){
				
				if ( $forum_id == $forum){
					
					$jumplist -> addToContent( '<option value="'.$forum_id.'" selected>');
					
				}else{
					
					$jumplist -> addToContent( '<option value="'.$forum_id.'">');
					
				}
				
				$jumplist -> addToContent( $forum_name.'</option>');
					
			}
			
		}
		
		$jumplist -> addToContent( '</select>');
		$jumplist -> addToContent( ' <a href="javascript:jumpToForum()">'.$style -> drawImage( 'button_go', $language -> getString( 'forums_jump_button')).'</a>');
		$jumplist -> addToContent( '<script type="text/JavaScript">
		
			function jumpToForum(){
			
				forums_list = document.forms[\'jumplist\'];
				forums_list.submit();
						
			
			}
				
		
		</script>');
		$jumplist -> closeForm();
		
		return $jumplist -> display();
		
	}
	
	function canReadTopics( $forum, $masks = array()){
				
		/**
		 * other forums
		 */
		
		$other_forums = array();
		
		/**
		 * go trought masks
		 */

		foreach ( $this -> forums_perms as $forum_perms){
						
			/**
			 * check if in mask
			 */
			
			if ( in_array( $forum_perms['forums_acess_perms_id'], $masks)){
			
				/**
				 * full array;
				 */
				
				if ( $other_forums[ $forum_perms['forums_acess_forum_id']]['forums_access_show_forum'] != true && $forum_perms['forums_access_show_forum'] == true){
					
					$other_forums[ $forum_perms['forums_acess_forum_id']]['forums_access_show_forum'] = $forum_perms['forums_access_show_forum'];
				
				}
			
				if ( $other_forums[ $forum_perms['forums_acess_forum_id']]['forums_access_show_topics'] != true && $forum_perms['forums_access_show_topics'] == true){
					
					$other_forums[ $forum_perms['forums_acess_forum_id']]['forums_access_show_topics'] = $forum_perms['forums_access_show_topics'];
				
				}
									
			}
			
		}
		
		/**
		 * now our forum
		 */
		
		$readable_forum = $other_forums[ $forum]['forums_access_show_topics'];
		
		if ( $readable_forum){
			
			/**
			 * do pathfinding
			 */
			
			$current_location = $forums -> forums_list[ $forum_id]['forum_parent'];
	
			while ( $current_location != 0){
				
				/**
				 * check, if user can see element on a path
				 */
				
				if ( $other_forums[$current_location]['forums_access_show_topics']){
					
					/**
					 * we dont see simple element on a path, so we cant reach actual one
					 */
					
					$readable_forum = false;	
					$current_location = 0;
					
				}else{
					
					$current_location = $forums -> forums_list[ $current_location]['forum_parent'];
				
				}	
			}
			
		}
		
		/**
		 * send result
		 */
			
		return $readable_forum;		
	}
	
	function checkForumRead( $forum_to_show, $forum_topics){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		if( $session -> user['user_id'] != -1){
		
			$rebuild_reads = false;
			
			/**
			 * check if forum has read
			 */
			
				
			$unreaded_topics = $mysql -> query( "SELECT count(t.topic_id) AS unreads_results FROM topics t LEFT JOIN topics_reads r ON t.topic_id = r.topic_read_topic WHERE t.topic_forum_id = '".$forum_to_show."' AND r.topic_read_time >= t.topic_last_time AND r.topic_read_user = '".$session -> user['user_id']."'");
								
			if ( $unreads_result = mysql_fetch_array( $unreaded_topics, MYSQL_ASSOC)){
								
				$unreaded_topics = $unreads_result['unreads_results'];
				
			}
						
			if ( $forum_topics <= $unreaded_topics){
				
				/**
				 * update forum reads
				 */

				$mysql -> delete( 'forums_reads', "`forums_read_forum` = '".$forum_to_show."' AND `forums_read_user` = '".$session -> user['user_id']."'");

				$mysql -> insert( array( 'forums_read_forum' => $forum_to_show, 'forums_read_user' => $session -> user['user_id'], 'forums_read_time' => time()), 'forums_reads');
	
			}
			
		}
		
	}
	
	/**
	 * TOPICS
	 */
	
	function getPrefixHTML( $prefix_id, $forum_id){
		
		if ( key_exists( $prefix_id, $this -> prefixes_list)){
			
			/**
			 * split prefix forums
			 */
			
			$forums = split( ",", $this -> prefixes_list[$prefix_id]['topic_prefix_forums']);
			
			if ( in_array( $forum_id, $forums)){
			
				return htmlspecialchars_decode( $this -> prefixes_list[$prefix_id]['topic_prefix_html']);
					
			}else{
				
				return '';
			
			}
			
		}else{
			
			return '';
			
		}
		
	}
	
	function getPrefixName( $prefix_id, $forum_id){
		
		if ( key_exists( $prefix_id, $this -> prefixes_list)){
			
			/**
			 * split prefix forums
			 */
			
			$forums = split( ",", $this -> prefixes_list[$prefix_id]['topic_prefix_forums']);
			
			if ( in_array( $forum_id, $forums)){
			
				return $this -> prefixes_list[$prefix_id]['topic_prefix_name'];
					
			}else{
				
				return '';
			
			}
			
		}else{
			
			return '';
			
		}
		
	}
	
	function  getPrefixSelect( $forum_id, $select_variable, $select_value, $name_variable, $name_value){
		
		global $language;
		
		$prefixes_list = '';
		
		foreach ( $this -> prefixes_list as $prefix_id => $prefix_value){
			
			$prefix_forums = split( ",", $prefix_value['topic_prefix_forums']);
			
			if ( in_array( $forum_id, $prefix_forums)){
				
				if ( $prefix_id == $select_value){
				
					$prefixes_list .= '<option value="'.$prefix_id.'" selected>'.$prefix_value['topic_prefix_name'].'</option>';
				
				}else{
					
					$prefixes_list .= '<option value="'.$prefix_id.'">'.$prefix_value['topic_prefix_name'].'</option>';
					
				}
			}
		}
		
		if ( strlen($prefixes_list) > 0)
			$prefixes_list = '<select name="'.$select_variable.'"><option value="0">'.$language -> getString( 'new_topic_noprefix').'</option>'.$prefixes_list.'</select> ';
		
		return $prefixes_list.'<input id="'.$name_variable.'" name="'.$name_variable.'" type="text" size="50" value="'.$name_value.'" />';
		
	}
	
	function cutTopicName( $toptic_name){
		
		global $utf8;
		
		/**
		 * clear topic
		 */
		
		$topic_name_c = $utf8 -> turnOffChars( $toptic_name);
		$topic_name_c = str_replace( '&quot;', '"', $topic_name_c);	
		$topic_name_c = htmlspecialchars_decode( $topic_name_c);
		
		if ( strlen( $topic_name_c) > 35){
			
			$topic_name_c = substr( $topic_name_c, 0, 35).'...';
			
		}
		
		$topic_name_c = htmlspecialchars( $topic_name_c);
		$topic_name_c = $utf8 -> turnChars( $topic_name_c);
		
		return $topic_name_c;
		
	}
	
	function drawTopicRating( $score, $votes){
		
		global $settings;
		global $style;
		
		$rank = round( $score * 10 / $votes, 0);
		
		switch ( $settings['topics_rantings_scale']){
		
			case 0:
				
				$stars_to_draw = floor( $rank / 20);
				
				$drawed_stars = array();
				
				while ( $stars_to_draw > 0){
					
					$drawed_stars[] = $style -> drawImage( 'pip', $rank.'%');
					$stars_to_draw --;
					
				}
				
				return join( " ", $drawed_stars);
				
			break;
			
			case 1:
				
				return $rank.'%';
				
			break;
			
		}
		
	}
	
}

?>