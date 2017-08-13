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
|	Help browser
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

/**
 * draw board guidelines
 *
 */

class action_help extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * add breadcrumb
		 */
		
		$path -> addBreadcrumb( $language -> getString( 'help_system'), parent::systemLink( parent::getId()));
		
		/**
		 * set page title
		 */
		
		$output -> setTitle( $language -> getString( 'help_system'));
		
		/**
		 * define what to do
		 */
		
		$proper_does = array( 'start', 'show_topic', 'search');
		
		if ( isset( $_GET['do']) && in_array( $_GET['do'], $proper_does)){
			
			$act_to_do = $_GET['do'];
			
		}else{
			
			$act_to_do = 'start';
			
		}
		
		switch ( $act_to_do){
			
			case 'start':
				
				$this -> act_draw_help_files_list();
				
			break;
			
			case 'search':
				
				$this -> act_do_search();
				
			break;
			
			case 'show_topic':
			
				$this -> act_show_topic();
				
			break;
				
		}
		
	}
	
	function act_draw_help_files_list(){
	
		//include globals
		include( FUNCTIONS_GLOBALS);

		/**
		 * search form
		 */
		
		$search_link = array( 'do' => 'search');
		
		$search_form = new form();
		$search_form -> openForm( parent::systemLink( parent::getId(), $search_link));
		$search_form -> openOpTable();
		
		$search_form -> drawTextInput( $language -> getString( 'help_system_help_phrase'), 'search_phrase');
		
		$search_form -> closeTable();
		$search_form -> drawButton( $language -> getString( 'help_system_search_button'));	
		$search_form -> closeForm();
		
		parent::draw( $style -> drawFormBlock( $language -> getString('help_system_search'), $search_form -> display()));
		
		/**
		 * begin drawing form
		 */
		
		$help_topics_list = new form();
		$help_topics_list -> openOpTable();
		
		$help_topics_query = $mysql -> query( "SELECT * FROM help_files ORDER BY `help_file_pos`");
		
		while ( $help_topics_result = mysql_fetch_array( $help_topics_query, MYSQL_ASSOC)){
			
			$help_topics_result = $mysql -> clear( $help_topics_result);
			
			/**
			 * parse help
			 */
			
			$help_file_info = '';
			
			if ( !empty( $help_topics_result['help_file_info']))
				$help_file_info = '<br />'.$strings -> parseBB( nl2br( $help_topics_result['help_file_info']), true, true);
			
			/**
			 * add position to list
			 */
			
			$topic_link = array( 'do' => 'show_topic', 'topic' => $help_topics_result['help_file_id']);
			
			$help_topics_list -> drawRow( '<a href="'.parent::systemLink( parent::getId(), $topic_link).'">'.$help_topics_result['help_file_name'].'</a>'.$help_file_info);
		}
		
		$help_topics_list -> closeTable();
		
		parent::draw( $style -> drawFormBlock( $language -> getString('help_system_help_topics'), $help_topics_list -> display()));
		
	}
	
	function act_show_topic(){
	
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * get topic to draw
		 */
		
		if ( isset( $_GET['topic']) && !empty( $_GET['topic'])){
		
			$topic_to_draw = $_GET['topic'];
			settype( $topic_to_draw, 'integer');
		
			$show_topic_query = $mysql -> query( "SELECT * FROM help_files WHERE `help_file_id` = '$topic_to_draw'");
			
			if ( $help_topic_result = mysql_fetch_array( $show_topic_query, MYSQL_ASSOC)){
				
				$help_topic_result = $mysql -> clear( $help_topic_result);
				
				/**
				 * add breadcrumb
				 */
				
				$show_topic_link = array( 'do' => 'show_topic', 'topic' => $topic_to_draw);
				
				$path -> addBreadcrumb( $help_topic_result['help_file_name'], parent::systemLink( parent::getId(), $show_topic_link));
				
				/**
				 * draw help
				 */
				
				parent::draw( $style -> drawBlock( $help_topic_result['help_file_name'], $strings -> parseBB( nl2br( $help_topic_result['help_file_text']), true, true)));
				
			}else{
				
				/**
				 * topic not found
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'help_system_help_topics'), $language -> getString( 'help_system_help_topic_notfound')));
				
				$this -> act_draw_help_files_list();
				
			}
			
		}else{
			
			/**
			 * we got nothing
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'help_system_help_topics'), $language -> getString( 'help_system_help_topic_empty')));
			
			$this -> act_draw_help_files_list();
			
		}
	}
	
	function act_do_search(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
		
		/**
		 * check, if user sended us an phrase to search
		 */
		
		$phrase_to_search = trim( $_POST['search_phrase']);
		
		if ( $phrase_to_search != ""){
			
			/**
			 * create query
			 */
			
			$search_help_query = $mysql -> query( "SELECT * FROM help_files WHERE `help_file_name` LIKE '%$phrase_to_search%' OR `help_file_info` LIKE '%$phrase_to_search%' OR `help_file_text` LIKE '%$phrase_to_search%'");
			
			if ( mysql_num_rows( $search_help_query) > 0){
				
				$search_results = new form();
				$search_results -> openOpTable();
				
				while ( $help_topics_result = mysql_fetch_array( $search_help_query, MYSQL_ASSOC)){
			
					$help_topics_result = $mysql -> clear( $help_topics_result);
					
					/**
					 * parse help
					 */
					
					$help_file_info = '';
					
					if ( !empty( $help_topics_result['help_file_info']))
						$help_file_info = '<br />'.$strings -> parseBB( nl2br( $help_topics_result['help_file_info']), true, true);
					
					/**
					 * add position to list
					 */
					
					$topic_link = array( 'do' => 'show_topic', 'topic' => $help_topics_result['help_file_id']);
			
					$search_results -> drawRow( '<a href="'.parent::systemLink( parent::getId(), $topic_link).'">'.$help_topics_result['help_file_name'].'</a>'.$help_file_info);
				}
				
				$search_results -> closeTable();
				parent::draw( $style -> drawFormBlock( $language -> getString('help_system_search_results'), $search_results -> display()));
		
			}else{
				
				/**
				 * we found nothing
				 */
				
				parent::draw( $style -> drawErrorBlock( $language -> getString( 'help_system_search_results'), $language -> getString( 'help_system_help_phrase_nothing_found')));
				
				$this -> act_draw_help_files_list();
			}
			
		}else{
			
			/**
			 * phrase is empty, draw error, and start page
			 */
			
			parent::draw( $style -> drawErrorBlock( $language -> getString( 'error'), $language -> getString( 'help_system_help_phrase_empty')));
			
			$this -> act_draw_help_files_list();
			
		}
		
	}
	
}

?>