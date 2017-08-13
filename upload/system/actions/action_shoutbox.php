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
|	Shoutbox
|	by Rafał Pitoń
|
#===========================================================================
*/
	
if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');

class action_shoutbox extends action{
		
	function __construct(){
		
		include( FUNCTIONS_GLOBALS);
			
		/**
		 * check if we can acces
		 */
		
		if ( $session -> user['user_shoutbox'] > 0 && $settings['shoutbox_turn'] && !defined('SIMPLE_MODE')){

			/**
			 * ok, we can use shoutbox
			 * define mode
			 */
				
			if ( $smode != 2){
				
				/**
				 * main shoutbox mode
				 */
				
				$shout_title = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td style="width: 100%">'.$language -> getString( 'shoutbox').'</td>
						<td style="text-align: center"><div id="shoutbox_loader"></div></td>
					</tr>
				</table>';
				
				$shoutbox_form = new form();
				
				if ( $session -> user['user_shoutbox'] > 1){
				
					$shoutbox_form -> openForm( 'javascript:doShout()');
					$shoutbox_form -> drawSpacer( '<b>'.$language -> getString( 'shoutbox_new_shout').':</b> <input id="new_shout_text" name="new_shout_text" type="text" style="width: 75%;" value="" /> <input type="submit" name="Submit"  value="'.$language -> getString( 'send').'">');
					$shoutbox_form -> closeForm();
				
				}
				
				$shoutbox_form -> openOpTable();
				$shoutbox_form -> drawRow( '<div id="shoutbox_messages" style="max-height: '.$settings['shoutbox_height'].'px; overflow: auto;"></div>');
				$shoutbox_form -> closeTable();
								
				/**
				 * draw shout
				 */
				
				parent::draw( $style -> drawFormBlock( $shout_title, $shoutbox_form -> display()));
				
				/**
				 * draw javascript
				 */

				$shout_functions = array();
				
				if ( $session -> user['user_shoutbox'] > 1){
					
					$shout_functions[] = '
					
					//send function
					function doShout(){
						
						if( messaging == false){
										
							uniAJAX = GetXmlHttpObject();
					
							uniAJAX.onreadystatechange = function(){
							
								if(uniAJAX.readyState == 4){
								
									loader_div.innerHTML = "";
									messages_div.innerHTML = uniAJAX.responseText;
									
									if( shout_text == message_text.value){
										message_text.value = "";
									}
									
									can_query = true;
									messaging = false;
									
								}else{
								
									messaging = true;
									can_query = false;
									loader_div.innerHTML = "'.addslashes( $style -> drawImage( 'small_loader')).'";
	
								}	
							}
	
							if( can_query == false){
							
								//clear buffor for next query
								uniAJAX.abort();
								
							}
									
							shout_text = message_text.value;
										
							uniAJAX.open("POST","'.ROOT_PATH.'index.php?act=shoutbox&m=1&smode=2", true);
							uniAJAX.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
							uniAJAX.send( "text=" + encodeURIComponent(shout_text));
						
						}
							
					}';
					
				}
				
				if ( $session -> user['user_shoutbox'] > 2){
					
					$shout_functions[] = '
					
					//send function
					function deleteShout( shout_to_kill){
															
						uniAJAX = GetXmlHttpObject();
				
						uniAJAX.onreadystatechange = function(){
						
							if(uniAJAX.readyState == 4){
							
								loader_div.innerHTML = "";
								messages_div.innerHTML = uniAJAX.responseText;
								can_query = true;
								
							}else{
							
								can_query = false;
								loader_div.innerHTML = "'.addslashes( $style -> drawImage( 'small_loader')).'";

							}	
						}

						if( can_query == false){
						
							//clear buffor for next query
							uniAJAX.abort();
							
						}
									
						uniAJAX.open("GET","'.ROOT_PATH.'index.php?act=shoutbox&m=2&smode=2&shout=" + shout_to_kill, true);
						uniAJAX.send( null);
													
					}';
					
				}
				
				/**
				 * display javscript
				 */
					
				$shout_frq[0] = '10000';
				$shout_frq[1] = '5000';
				$shout_frq[2] = '1000';
						
				parent::draw( '<script type="text/JavaScript">
				
					loader_div = document.getElementById( \'shoutbox_loader\');
					messages_div = document.getElementById( \'shoutbox_messages\');
					message_text = document.getElementById( \'new_shout_text\');
					
					can_query = true;
					messaging = false;
					
					//loader function
					function refreshShouts(){
							
						if( can_query){
							
							uniAJAX = GetXmlHttpObject();
					
							uniAJAX.onreadystatechange = function(){
							
								if(uniAJAX.readyState == 4){
								
									loader_div.innerHTML = "";
									messages_div.innerHTML = uniAJAX.responseText;
									can_query = true;
									
								}else{
								
									can_query = false;
									loader_div.innerHTML = "'.addslashes( $style -> drawImage( 'small_loader')).'";
	
								}	
							}
														
							uniAJAX.open("GET","'.ROOT_PATH.'index.php?act=shoutbox&smode=2", true)
							uniAJAX.send( null)
					
						}
						
					}
											
					refreshShouts();
					window.setInterval("refreshShouts()", '.$shout_frq[$settings['shoutbox_frequency']].');
				
					'.join( "\n\n", $shout_functions).'
					
				</script>');
				
			}else{
				
				/**
				 * actions mode
				 */
				
				$mode = $_GET['m'];
				
				switch ( $mode){
					
					case 1:
					
						if ( $session -> user['user_shoutbox'] > 1){
							
							$message_to_shout = $strings -> inputClear( $_POST['text'], false);
							
							if ( strlen( $message_to_shout) > 0){
								
								/**
								 * add shout
								 */
								
								$new_shout_sql['shout_author'] = $session -> user['user_id'];
								$new_shout_sql['shout_author_name'] = addslashes( $session -> user['user_login']);
								$new_shout_sql['shout_time'] = time();
								$new_shout_sql['shout_message'] = $message_to_shout;
								
								$mysql -> insert( $new_shout_sql, 'shouts');
								
							}
							
						}
						
					break;
						
					case 2:
					
						if ( $session -> user['user_shoutbox'] > 2){
							
							$message_to_delete = $_GET['shout'];
							settype( $message_to_delete, 'integer');
							
							$mysql -> delete( 'shouts', "`shout_id` = '$message_to_delete'");
							
						}
						
					break;
					
				}
				
				/**
				 * draw shouts
				 */
										
				$shouts_query = $mysql -> query( "SELECT s.*, u.user_id, u.user_login, u.user_main_group, u.user_other_groups, g.users_group_prefix, g.users_group_suffix
				FROM shouts s
				LEFT JOIN users u ON s.shout_author = u.user_id
				LEFT JOIN users_groups g ON g.users_group_id = u.user_main_group
				ORDER BY s.shout_time DESC
				LIMIT ".$settings['shoutbox_shouts_num']);
				
				$generated_shouts = array();
				
				while( $shouts_result = mysql_fetch_array( $shouts_query, MYSQL_ASSOC)){
					
					//clear result
					$shouts_result = $mysql -> clear( $shouts_result);
					
					/**
					 * shout kill_link
					 */
					
					if ( $session -> user['user_shoutbox'] > 2){
						
						$kill_link = '<a href="javascript:deleteShout( '.$shouts_result['shout_id'].')">'.$style -> drawImage( 'small_delete').'</a> ';
						
					}else{
						
						$kill_link = '';
						
					}
					
					/**
					 * shout author
					 */
					
					if ( $shouts_result['user_id'] == -1){
						
						$shout_author = $shouts_result['users_group_prefix'].$shouts_result['shout_author_name'].$shouts_result['users_group_suffix'];
												
					}else{
						
						$shout_author = '<a href="'.parent::systemLink( 'user', array('user' => $shouts_result['user_id'])).'">'.$shouts_result['users_group_prefix'].$shouts_result['user_login'].$shouts_result['users_group_suffix'].'</a>';
						
					}
					
					/**
					 * shout text
					 */
					
					$shout_text = $shouts_result['shout_message'];
					
					//parse bb
					$shout_text = $strings -> parseBB( $shout_text, $settings['shoutbox_allow_bbcodes'], $settings['shoutbox_allow_emos']);
					
					/**
					 * censore message
					 */
					
					$user_groups = split( ',', $shouts_result['user_other_groups']);
					$user_groups[] = $shouts_result['user_main_group'];
					
					if ( !$users -> cantCensore( $user_groups)){
						$shout_text = $strings -> censore( $shout_text);
					}
									
					//add shout
					$generated_shouts[] = '<b>'.$kill_link.'['.$time -> drawDate( $shouts_result['shout_time']).']</b> '.$shout_author.'<b>:</b> '.$shout_text;
										
				}
				
				if ( count( $generated_shouts) > 0){
				
					parent::draw( join( '<br />', $generated_shouts));
				
				}else{
				
					parent::draw( $language -> getString( 'shoutbox_empty'));
					
				}
			}
			
		}
		
	}

}

?>