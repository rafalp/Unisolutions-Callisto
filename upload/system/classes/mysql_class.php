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
|	MySQL Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class mysql{
	
	//total queries number
	var $queries_num = 0;
	//defauld query id
	var $qid = 0;
	//queries history array
	var $queries_array;
	
	/**
	 * tables used by script
	 */
	
	var $proper_tables = array( 
	'admins_loging_log',
	'admins_logs',
	'admins_sessions',
	'attachments',
	'attachments_types',
	'badwords',
	'banfilters',
	'bbtags',
	'calendar_events',
	'captcha_generations',
	'emoticons',
	'forums',
	'forums_access',
	'forums_reads',
	'help_files',
	'languages',
	'mails',
	'mails_logs',
	'moderators',
	'moderators_logs',
	#'plugins',
	'posts',
	'posts_reports',
	'profile_fields',
	'profile_fields_data',
	'ranks',
	'reputation_scale',
	'reputation_votes',
	'searchs_results',
	'settings',
	'settings_groups',
	'shouts',
	'spiders_logs',
	'styles',
	'subscriptions_forums',
	'subscriptions_topics',
	'surveys_ops',
	'surveys_votes',
	'tasks',
	'tasks_logs',
	'topics',
	'topics_prefixes',
	'topics_reads',
	'topics_votes',
	'users',
	'users_autologin',
	'users_groups',
	'users_messages',
	'users_perms',
	'users_sessions',
	'users_warnings',
	'version_history');
		
	function __construct(){
		
		global $system_settings;
		
		/**
		 * Now we will try to connect to MySQL server
		 */
		
		try{
			
			@$db_connection = mysql_connect($system_settings['db_server'], $system_settings['db_user'], $system_settings['db_pass']);
			@$db_select = mysql_select_db($system_settings['db_name']);
			
			/**
			 * set coding
			 */
			
			//@mysql_query( "SET NAMES latin1");   
			//@mysql_query( "SET CHARSET latin1");
			
			if(!$db_connection)
				throw new uniException( 'MySQL Server is not responding');
				
			if(!$db_select)
				throw new uniException( 'Cannot connect to specified database');
			
		}catch(uniException $error){
			
			 $error -> criticalError( 1);
			
		}
	}
	
	function __destruct(){
		
		/**
		 * script is over, lets close mysql Connection
		 */
		
		@mysql_close();
		
	}
	
	function query($query){
		
		global $system_settings;
		global $utf8;
		
		$time_before = microtime();
		
		$query = $utf8 -> turnChars($query);
		
		/**
		 * add table prefix, if table is found on list of script tables
		 */
		
		foreach ( $this -> proper_tables as $table_name) {
			
			/**
			 * check is table name is in string
			 */
			
			if ( strstr( $query, $table_name) != false){
				
				/**
				 * FROM
				 */
				
				$query = str_replace( "FROM `".$table_name."`", "FROM `".$system_settings['db_prefix'].$table_name."`", $query);
				$query = str_replace( "FROM ".$table_name, "FROM ".$system_settings['db_prefix'].$table_name, $query);
				
				/**
				 * INTO
				 */
				
				$query = str_replace( "INTO `".$table_name."`", "INTO `".$system_settings['db_prefix'].$table_name."`", $query);
				$query = str_replace( "INTO ".$table_name." ", "INTO ".$system_settings['db_prefix'].$table_name, $query);
				
				/**
				 * UPDATE
				 */
				
				$query = str_replace( "UPDATE `".$table_name."`", "UPDATE `".$system_settings['db_prefix'].$table_name."`", $query);
				$query = str_replace( "UPDATE ".$table_name." ", "UPDATE ".$system_settings['db_prefix'].$table_name." ", $query);
				
				/**
				 * JOIN
				 */
				
				$query = str_replace( "JOIN `".$table_name."`", "JOIN `".$system_settings['db_prefix'].$table_name."`", $query);
				$query = str_replace( "JOIN ".$table_name." ", "JOIN ".$system_settings['db_prefix'].$table_name." ", $query);
				
				/**
				 * CREATE TABLE
				 */
				
				$query = str_replace( "CREATE TABLE `".$table_name."`", "CREATE TABLE `".$system_settings['db_prefix'].$table_name."`", $query);
				
				/**
				 * ALTER TABLE
				 */
				
				$query = str_replace( "ALTER TABLE `".$table_name."`", "ALTER TABLE `".$system_settings['db_prefix'].$table_name."`", $query);
				
			}
			
		}
		
		$result = mysql_query( $query);
		
		$time_after = microtime()-$time_before;
   		
		$this -> queries_array[$this -> qid]['time'] = round( abs($time_after), 5);
		$this -> queries_array[$this -> qid]['content'] = $query;
		$this -> queries_array[$this -> qid]['err_no'] = mysql_errno();
		$this -> queries_array[$this -> qid]['err'] = mysql_error();
		
		$qid = $this -> qid;
				
		$this -> qid++;
		
		$this -> queries_num++;
		return $result;

	}
	
	function insert( $values, $table){
		
		global $system_settings;
				
		foreach ($values as $field_name => $field_value){
	 		$fields[] = $field_name;
	 		$conent[] = $field_value;
	 	}
		
		$parsed_fields =  join($fields, "`, `");
		$parsed_values =  join($conent, "', '");
	 	
		$query = "INSERT INTO ".$table." ( `".$parsed_fields."`) VALUES ( '".$parsed_values."')";
				
		$this -> query($query);
		
		if(mysql_error() != null)
			return false;

	}
	
	function update( $values, $table, $conditions = null){
		
		global $system_settings;
				
		foreach ($values as $field_name => $field_value){
	 		$fields[] = "`".$field_name."` = '".$field_value."'";
	 	}
		
	 	$parsed_fields = join( ", ", $fields);
	 	
		$query = "UPDATE ".$table." SET $parsed_fields";
		
		if($conditions != null)
			$query .= ' WHERE '.$conditions;
		
		$this -> query($query);
		
		if(mysql_error() != null)
			return false;

	}
	
	function delete( $table, $conditions = null){
		
		global $system_settings;
		
		$query = "DELETE FROM ".$table;
		
		if($conditions != null)
			$query .= ' WHERE '.$conditions;
	 	
		$this -> query($query);
		
		if(mysql_error() != null)
			return false;

	}
	
	function countRows( $table, $condition = null){
		
		global $system_settings;
		
		$query = "SELECT COUNT(*) FROM $table";
		
		if($condition != null)
			$query .= ' WHERE '.$condition;
		
		$quering = $this -> query($query);
		
		if ($result = mysql_fetch_array($quering, MYSQL_NUM))	  
			return  $result[0];
		
	}
	
	/**
	 * removes slashes from whole result
	 *
	 * @param unknown_type $result
	 * @return unknown
	 */
	
	function clear( $result){
		
		foreach ( $result as $result_id => $result_value)
			$result[$result_id] = stripslashes($result_value);
									
		return $result;
		
	}
		
}

?>