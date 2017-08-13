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
|	Forums Pruner
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

/**
 * select forums to prune
 */

$forums_to_prune = array();

$topics_to_kill = array();

$prune_forums_sql = $mysql -> query( "SELECT forum_id, forum_prune_days FROM forums WHERE `forum_pruning` = '1'");

while ( $prune_forums_result = mysql_fetch_array( $prune_forums_sql, MYSQL_NUM)){
	
	/**
	 * select topics
	 */
	
	$topics_prune = $mysql -> query( "SELECT topic_id FROM topics WHERE `topic_forum_id` = '".$prune_forums_result[0]."' AND `topic_last_time` < '".(time() - (24 * 60 *60 * $prune_forums_result[1]))."'");
	
	while ( $prune_topics_result = mysql_fetch_array( $topics_prune, MYSQL_ASSOC)){
		
		$topics_to_kill[] = $prune_topics_result['topic_id'];
		
	}
	
	$mysql -> delete( 'topics', "`topic_forum_id` = '".$prune_forums_result[0]."'");
	
	/**
	 * resynch our board
	 */
	
	$forums -> forumResynchronise( $prune_forums_result[0]);
	
}

if ( count( $topics_to_kill) > 0){
	
	$users_ids = $mysql -> query( "SELECT DISTINCT post_author FROM posts WHERE `post_topic` IN (".join( ",", $topics_to_kill).")");
										
	$authors = array();
	
	while ( $users_result = mysql_fetch_array( $users_ids, MYSQL_ASSOC)){
		
		$authors[] = $users_result['post_author'];
		
	}
	
	$mysql -> delete( 'posts', "`post_topic` IN (".join( ",", $topics_to_kill).")");
			
	foreach ( $forums -> forums_list as $forum_id => $forum_ops){
					
		if ( $forum_ops['forum_increase_counter']){
			
			$proper_forums[] = $forum_id;
		
		}
		
	}
	
	if ( count( $proper_forums) > 0){
		
		foreach ( $authors as $author_id){
			
			/**
			 * do we resync post counter?
			 */
			
			$user_posts_q = $mysql -> query( "SELECT COUNT(*) FROM posts p LEFT JOIN topics t ON p.post_topic = t.topic_id WHERE p.post_author = '$author_id' AND t.topic_forum_id IN (".join( ",", $proper_forums).")");
			if ($result = mysql_fetch_array($user_posts_q, MYSQL_NUM))	  
				$user_posts = $result[0];

			$mysql -> update( array( 'user_posts_num' => $user_posts), 'users', "`user_id` = '$author_id'");
			
		}
	
	}

}

/**
 * clear outtimed votes
 */

$votes_to_delete = array();
$votes_query = $mysql -> query( "SELECT v.* FROM reputation_votes v LEFT OUTER JOIN posts p ON v.reputation_vote_post = p.post_id WHERE p.post_id = ''");
		
while ( $votes_result = mysql_fetch_array( $votes_query, MYSQL_ASSOC)){
	
	//clear result
	$votes_result = $mysql -> clear( $votes_result);
		
	/**
	 * add attachment to list
	 */
	
	$deleted_votes++;
	$votes_to_delete[] = $votes_result['reputation_vote_id'];
	
}

if ( count( $votes_to_delete) > 0){
	
	$mysql -> delete( "reputation_votes", "reputation_vote_post IN ( ".join( ",", $votes_to_delete).")");
	
}

?>