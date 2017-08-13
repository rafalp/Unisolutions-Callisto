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
|	Rebuild stats
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

			
/**
 * start from user num
 */

$users_num = $mysql -> countRows( 'users', "`user_id` > '0'");
$mysql -> query( "UPDATE settings SET `setting_value` = '$users_num' WHERE `setting_setting` = 'users_num'");

/**
 * groups num now
 */

$groups_num = $mysql -> countRows( 'users_groups');
$mysql -> query( "UPDATE settings SET `setting_value` = '$groups_num' WHERE setting_setting = 'users_groups_num'");

/**
 * topics
 */

$topics_num = $mysql -> countRows( 'topics');
$mysql -> query( "UPDATE settings SET `setting_value` = '$topics_num' WHERE `setting_setting` = 'board_threads_total'");

/**
 * posts
 */

$posts_num = $mysql -> countRows( 'posts');
$mysql -> query( "UPDATE settings SET `setting_value` = '$posts_num' WHERE `setting_setting` = 'board_posts_total'");

/**
 * languages usage
 */

$languages_query = $mysql -> query( "SELECT `lang_id` FROM languages");
while( $lang_result = mysql_fetch_array( $languages_query, MYSQL_ASSOC)){
	
	$lang_users_num = $mysql -> countRows( 'users', "`user_lang` = '".$lang_result['lang_id']."'");
	$mysql -> query( "UPDATE languages SET `lang_users` = '".$lang_users_num."' WHERE `lang_id` = '".$lang_result['lang_id']."'");

}

/**
 * styles usage now
 */
	
$styles_query = $mysql -> query( "SELECT `style_id` FROM styles");
while( $style_result = mysql_fetch_array( $styles_query, MYSQL_ASSOC)){
	
	$style_users_num = $mysql -> countRows( 'users', "`user_style` = '".$style_result['style_id']."'");
	$mysql -> query( "UPDATE styles SET `style_users` = '".$style_users_num."' WHERE `style_id` = '".$style_result['style_id']."'");

}

/**
 * cache
 */

$cache -> flushCache( 'system_settings');

?>