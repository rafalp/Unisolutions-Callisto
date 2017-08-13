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
|	Captcha Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class captcha{

	
	/**
	 * this function generates captcha
	 */
	
	function generate(){
		
		global $settings;
		global $mysql;
		
		if( $settings['bots_protection_captcha']){
			
			/**
			 * captcha is set to yes, generate code
			 */
			
			$code = substr( md5( time()), 3, 6);
			
			$inserts['captcha_code'] = $code;
			
		}
		
		if( $settings['bots_protection_challenge_response']){
			
			/**
			 * CR is set to yes, generate it
			 */
			
			$numberA = rand(1, 4);
			$numberB = rand(1, 4);
			
			if( $settings['bots_protection_challenge_response_type'] == 0){
				
				$cr_result = $numberA + $numberB;
				
			}else if( $settings['bots_protection_challenge_response_type'] == 1){
				
				$cr_result = $numberA - $numberB;
				
			}else{
				
				$cr_result = $numberA * $numberB;
				
			}
			
			$inserts['captcha_num1'] = $numberA;
			$inserts['captcha_num2'] = $numberB;
			$inserts['captcha_type'] = $settings['bots_protection_challenge_response_type'];
			$inserts['captcha_result'] = $cr_result;
			
		}
		
		$inserts['captcha_generated'] = time();
		
		$mysql -> insert( $inserts, 'captcha_generations');
		
		return mysql_insert_id();
		
	}
	
	function drawForm( $id){
		
		global $language;
		global $mysql;
		global $settings;
		global $system_settings;
		
		if( $settings['bots_protection_captcha'] || $settings['bots_protection_challenge_response'] || $settings['bots_protection_humanity_test']){
		
		
			$captha_form = new form();
			
			$captha_form -> closeTable();
			$captha_form -> drawSpacer( $language -> getString( 'spam_bots_protection'));
			$captha_form -> openOpTable();
			
			if( $settings['bots_protection_captcha'] && extension_loaded('gd')){
				
				$link['code'] = $id;
				
				$captha_form -> drawInfoRow( $language -> getString( 'spam_bots_protection_code'), '<img src="'.$this -> systemLink( 'captcha', $link).'" />', $language -> getString( 'spam_bots_protection_code_help'));
				$captha_form -> drawTextInput( $language -> getString( 'spam_bots_protection_code_type'), 'captcha_code', '', $language -> getString( 'spam_bots_protection_code_type_help'));
				
			}
			
			if( $settings['bots_protection_challenge_response']){
				
				$cr_query = $mysql -> query( 'SELECT captcha_num1, captcha_num2, captcha_type, captcha_result FROM captcha_generations WHERE `captcha_id` = \''.$id.'\'');
				
				$cr_types[0] = '+';
				$cr_types[1] = '-';
				$cr_types[2] = 'x';
				
				if( $result = mysql_fetch_array( $cr_query, MYSQL_NUM)){
					$language -> setKey( 'anum', $result[0]);
					$language -> setKey( 'bnum', $result[1]);					
					$language -> setKey( 'crtype', $cr_types[$result[2]]);
					
					$language -> setKey( 'crresult', $result[3]);
				}
				
				$captha_form -> drawTextInput( $language -> getString( 'spam_bots_protection_cr'), 'captcha_challenge_response', '', $language -> getString( 'spam_bots_protection_cr_help'));
				
			}
			
			if( $settings['bots_protection_humanity_test']){
				
				$captha_form -> drawYesNo( $language -> getString( 'spam_bots_protection_ht'), 'captcha_humanity_test', 0, $language -> getString( 'spam_bots_protection_ht_help'));
				
			}
		
			return $captha_form -> display();
			
		}
	}
	
	function check( $id){
		
		global $language;
		global $mysql;
		global $settings;
		global $system_settings;
		
		
		
		if( $settings['bots_protection_captcha'] || $settings['bots_protection_challenge_response'] || $settings['bots_protection_humanity_test']){
			
			/**
			 * get data from mysql
			 */
			
			settype( $id, 'integer');
			
			$protection_query = $mysql -> query( 'SELECT * FROM captcha_generations WHERE `captcha_id` = \''.$id.'\'');

			if( $result = mysql_fetch_array( $protection_query, MYSQL_ASSOC)){
				
				$captcha_code = $result['captcha_code'];
				$captcha_result	 = $result['captcha_result'];			
				
			}
			/**
			 * check every protections one by one
			 */
			
			$error = true;
			
			if( $settings['bots_protection_captcha'] && $error == true){
				
				/**
				 * user is using an captcha, lets chceck it
				 */
				
				if( $captcha_code != trim( $_POST['captcha_code']) && extension_loaded('gd')){
					$this -> error_msg = $language -> getString( 'spam_bots_protection_code_wrong');
					$error = false;
				}
					
			}
			
			if( $settings['bots_protection_challenge_response'] && $error == true){
				
				/**
				 * user is using challenge response
				 */
				
				if( $captcha_result != trim( $_POST['captcha_challenge_response'])){
					$this -> error_msg = $language -> getString( 'spam_bots_protection_cr_wrong');
					$error = false;
				}
				
			}
			
			if( $settings['bots_protection_humanity_test'] && $error == true){
				
				if( $_POST['captcha_humanity_test'] == 0){
					$this -> error_msg = $language -> getString( 'spam_bots_protection_ht_wrong');
					$error = false;
				}
					
			}
				
			$mysql -> delete( 'captcha_generations', '`id` = \''.$id.'\'');
			
			return $error;
			
		}else{
			
			return true;
			
		}
		
	}
	
	function  getError(){
		
		return $this -> error_msg;
		
	}
	
	function systemLink( $act, $gets = null){
		
		global $settings;
		
		$link = ROOT_PATH.'index.php';
		
		$link .= '?act='.$act;
		
		if($gets != null)
			foreach( $gets as $get_name => $get_value)
				$link .= '&'.$get_name.'='.$get_value;
		
		return $link;
	}
		
}

?>