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
|	Logs Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class logs{
	
	/**
	 * array containig system generating logs
	 *
	 * @var array
	 */
	
	public $system_log;
	
	/**
	 * array containig mails logs
	 *
	 * @var array
	 */
	
	public $mails_log;
	
	/**
	 * class constructor
	 *
	 */
	
	function addAdminLog( $text, $keys = array()){
		
		global $session;
		global $mysql;
		global $strings;
		global $utf8;
		
		/**
		 * parse keys in array
		 */
		
		foreach ( $keys as $key => $replacement){
			
			$text = str_ireplace( "%".$key, htmlspecialchars_decode($replacement), $text);
			
		}
		
		/**
		 * create new query
		 */
		
		$new_admin_log_sql_ar['admins_log_user_id'] = $session -> user['user_id'];
		$new_admin_log_sql_ar['admins_log_user_ip'] = $session -> user_ip;
		$new_admin_log_sql_ar['admins_log_time'] = time();
		$new_admin_log_sql_ar['admins_log_act'] = $_GET['act'];
		$new_admin_log_sql_ar['admins_log_do'] = $_GET['do'];
		$new_admin_log_sql_ar['admins_log_details'] = $utf8 -> charsClear( $strings -> inputClear( $text, false));
		
		$mysql -> insert( $new_admin_log_sql_ar, 'admins_logs');
				
	}
	
	function addModLog( $text, $keys = array(), $forum = 0, $topic = 0, $post = 0, $user = -1){
		
		global $session;
		global $mysql;
		global $strings;
		global $utf8;
		
		/**
		 * parse keys in array
		 */
		
		foreach ( $keys as $key => $replacement){
			
			$text = str_ireplace( "%".$key, htmlspecialchars_decode($replacement), $text);
			
		}
		
		/**
		 * set types
		 */
		
		settype( $forum, 'integer');
		settype( $topic, 'integer');
		settype( $post, 'integer');
		settype( $user, 'integer');
		
		/**
		 * create new query
		 */
		
		$new_admin_log_sql_ar['moderators_log_user_id'] = $session -> user['user_id'];
		$new_admin_log_sql_ar['moderators_log_user_ip'] = $session -> user_ip;
		$new_admin_log_sql_ar['moderators_log_time'] = time();
		$new_admin_log_sql_ar['moderators_log_forum'] = $forum;
		$new_admin_log_sql_ar['moderators_log_topic'] = $topic;
		$new_admin_log_sql_ar['moderators_log_post'] = $post;
		$new_admin_log_sql_ar['moderators_log_target_user'] = $user;
		$new_admin_log_sql_ar['moderators_log_details'] = $utf8 -> charsClear( $strings -> inputClear( $text, false));
		
		$mysql -> insert( $new_admin_log_sql_ar, 'moderators_logs');
				
	}
	
	function addMailLog( $sender, $receiver, $subject){
		
		global $mysql;
		global $strings;
		global $utf8;
		
		settype( $sender, 'integer');
		settype( $receiver, 'integer');
		
		$new_mail_log_sql['mails_log_sender'] = $sender;
		$new_mail_log_sql['mails_log_receiver'] = $receiver;
		$new_mail_log_sql['mails_log_subject'] = $strings -> inputClear( $subject, false);
		$new_mail_log_sql['mails_log_time'] = time();
		$new_mail_log_sql['mails_log_ip'] = ip2long($_SERVER['REMOTE_ADDR']);
		
		$mysql -> insert( $new_mail_log_sql, 'mails_logs');
	}
	
	function addLoginLog( $user, $status){
		
		global $mysql;
		
		$sql_ar['admins_login_log_time'] = time();
		$sql_ar['admins_login_log_user_id'] = $user;
		$sql_ar['admins_login_log_user_ip'] = ip2long($_SERVER['REMOTE_ADDR']);
		$sql_ar['admins_login_log_success'] = $status;
				
		if(defined( 'ACP'))		
			$mysql -> insert( $sql_ar, 'admins_loging_log');
				
	}
	
	function addTaskLog( $task_id){

		global $mysql;
		global $utf8;
		
		settype( $task_id, 'integer');
		
		$sql_ar['tasks_log_task'] = $task_id;
		$sql_ar['tasks_log_time'] = time();
		$sql_ar['tasks_log_ip'] = ip2long($_SERVER['REMOTE_ADDR']);
		
		if(defined( 'ACP'))		
			$mysql -> insert( $sql_ar, 'tasks_logs');
		
	}
	
	function addSpiderLog( $spider){
		
		global $mysql;
		global $utf8;
		
		$sql_ar['spider_log_name'] = uniSlashes( $spider);
		$sql_ar['spider_log_ip'] = ip2long($_SERVER['REMOTE_ADDR']);
		$sql_ar['spider_log_time'] = time();
				
		if(!defined( 'ACP'))		
			$mysql -> insert( $sql_ar, 'spiders_logs');
				
	}
		
	function drawMailLogsTable(){
		
		global $style;
		global $page;
		global $language;
		
		if( count( $this -> mails_log) != 0){
				
			$logs_table = new form();
			
			$logs_table -> openOpTable();
			$logs_table -> addToContent( '<tr>
												<th style="width: 100%">'.$language -> getString( 'system_logs_mail_type').'</th>
												<th>'.$language -> getString( 'system_logs_mail_address').'</th>
												<th>'.$language -> getString( 'system_logs_mail_method').'</th>
												<th>'.$language -> getString( 'system_logs_mail_status').'</th>
			</tr>');
			
			$methods[0] = $language -> getString( 'system_logs_mail_method_0');
			$methods[1] = $language -> getString( 'system_logs_mail_method_1');
			
			$types[0] = $language -> getString( 'system_logs_mail_types_0');
			$types[1] = $language -> getString( 'system_logs_mail_types_1');
			$types[2] = $language -> getString( 'system_logs_mail_types_2');
			
			foreach ( $this -> mails_log as $mail){
				
				$logs_table -> addToContent( '<tr>
												<td class="opt_row1">'.$types[$mail['type']].'</td>
												<td class="opt_row2" style="text-align: center">'.$mail['address'].'</td>
												<td class="opt_row1" style="text-align: center">'.$methods[$mail['method']].'</td>
												<td class="opt_row2" style="text-align: center">'.$style -> drawThick($mail['status']).'</td>
											</tr>');
			}
			
			$logs_table -> closeTable();
			
			$page['foot'] .= $style -> drawBlock( $language -> getString( 'system_logs_mails'), $logs_table -> display());
			
		}
	}
	
}

?>