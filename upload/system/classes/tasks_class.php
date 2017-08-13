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
|	System Task Class
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

	
class tasks{
	
	/**
	 * list of tasks
	 *
	 */
	
	var $tasks_list;
	
	/**
	 * constructor will build list of tasks, and run them
	 *
	 */
	
	function __construct(){
		
		global $mysql;
		global $cache;
		global $logs;
		
		$this -> tasks_list = $cache -> loadCache( 'system_tasks');
		
		if ($this -> tasks_list == false){
		
			/**
			 * clear value, prepare it to become an array
			 */
			
			$this -> tasks_list = null;
			
			/**
			 * build up autorun cache
			 */
			
			$tasks_query = $mysql -> query( 'SELECT * FROM tasks WHERE `task_active` = \'1\' ORDER BY `task_next_run`');		
			while( $result = mysql_fetch_array($tasks_query, MYSQL_ASSOC)) {
				
				$result = $mysql -> clear($result);
				
				$this -> tasks_list[$result['task_id']] = array(
					'task_file' => $result['task_file'],
					'task_active' => $result['task_active'],
					'task_collect_logs' => $result['task_collect_logs'],
					'task_next_run' => $result['task_next_run'],
					'task_minute' => $result['task_minute'],
					'task_hour' => $result['task_hour'],
					'task_day' => $result['task_day'],
				); 
			}
			
			$cache -> saveCache( 'system_tasks', $this -> tasks_list);
			
		}

		/**
		 * now go trought system tasks
		 */
		
		foreach( $this -> tasks_list as $task_id => $task_params){
		
			/**
			 * check first, it its time to activate module;
			 */
				
			if( $task_params['task_next_run'] <= time()){
						
				/**
				 * run task
				 */
				
				$this -> runTask( $task_id);
																	
			}				
								
		}
		
	}
	
	/**
	 * this function runs task
	 */
	
	function runTask( $task_id){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		$task_params = $this -> tasks_list[$task_id];
		
		/**
		 * run task
		 */
		
		if ( file_exists( ROOT_PATH.'system/tasks/'.$task_params['task_file'])){
			
			include( ROOT_PATH.'system/tasks/'.$task_params['task_file']);	
			
			/**
			 * count time of next run
			 */
			
			$correction = ( $task_params['task_minute'] * 60) + ( $task_params['task_hour'] * 3600)  + ( $task_params['task_day'] * 3600 * 24);
				
			$next_run = time() + $correction;
					
			/**
			 * if time delays between next runs are equal to 0, dont change next run
			 */
				
			if( $correction > 0)
				$mysql -> query( 'UPDATE tasks SET `task_next_run` = \''.$next_run.'\' WHERE `task_id` = \''.$task_id.'\'');
		
			/**
			 * add log
			 */
			if ( $task_params['task_collect_logs'])
				$logs -> addTaskLog( $task_id);		
	
			/**
			 * clear cache
			 */
			
			$cache -> flushCache( 'system_tasks');
			
		}
		
	}
	
}

?>