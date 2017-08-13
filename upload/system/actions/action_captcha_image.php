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
|	Send captcha
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

class action_captcha_image extends action{
		
	function __construct(){
		
		//include globals
		include( FUNCTIONS_GLOBALS);
				
		/**
		 * we will draw captcha image
		 */
		
		if ( extension_loaded('gd')){
			
			if( isset( $_GET['code'])){
					
				/**
				 * we will generate image using code from db
				 */
				
				/**
				 * start from generating array with background files
				 */
				
				$backgrouds = glob( ROOT_PATH.'images/captcha_bg/*.jpg');
				
				$bg_to_use = rand( 0, count($backgrouds) - 1);
				
				$captcha_img = imagecreatefromjpeg( $backgrouds[$bg_to_use]);
				
				/**
				 * get code from mysql
				 */
				
				$code_query = $mysql -> query( "SELECT `captcha_code` FROM captcha_generations WHERE `captcha_id` = '".addslashes($_GET['code']) . "'");
				if( $result = mysql_fetch_array( $code_query, MYSQL_NUM))
					$code = $result[0];
				
				/**
				 * select font now
				 */
				
				$fonts = glob( ROOT_PATH.'images/captcha_fonts/*.TTF');			
				
				/**
				 * capthca will always have the same number o letters
				 */
				
				$start = 0;
				
				while( $start <6){
					
					/**
					 * get letter
					 */
					
					$letter = substr( $code, $start, 1);
	
					$font_to_use = $fonts[rand( 0, count($fonts) - 1)];
					$color = imagecolorallocate( $captcha_img, rand( 0, 100), rand( 0, 100), rand( 0, 100));
					
					imagettftext($captcha_img, rand(22, 28), rand( -15, 15), 20 + ($start*20) + rand( 0, 5), rand( 30, 50), $color, $font_to_use, $letter);
					
					/**
					 * go to next letter
					 */
					
					$start ++;
					
				}	
				
				header('Content-type: image/jpeg');
				imagejpeg( $captcha_img, '', 100);
				
				die();
			
			}else{
					
				/**
				 * we will generate random image, jus for presentation
				 */
				
				/**
				 * start from generating array with background files
				 */
				
				$backgrouds = glob( ROOT_PATH.'images/captcha_bg/*.jpg');
				
				$bg_to_use = rand( 0, count($backgrouds) - 1);
				
				$captcha_img = imagecreatefromjpeg( $backgrouds[$bg_to_use]);
				
				$code = substr( md5( time()), 3, 6);
				
				/**
				 * select font now
				 */
				
				$fonts = glob( ROOT_PATH.'images/captcha_fonts/*.TTF');			
				
				/**
				 * capthca will always have the same number o letters
				 */
				
				$start = 0;
				
				while( $start <6){
					
					/**
					 * get letter
					 */
					
					$letter = substr( $code, $start, 1);
					
					$font_to_use = $fonts[rand( 0, count($fonts) - 1)];
					$color = imagecolorallocate( $captcha_img, rand( 0, 100), rand( 0, 100), rand( 0, 100));
					
					imagettftext($captcha_img, rand(22, 28), rand( -15, 15), 20 + ($start*20) + rand( 0, 5), rand( 30, 50), $color, $font_to_use, $letter);
					
					/**
					 * go to next letter
					 */
					
					$start ++;
					
				}	
								
				header('Content-type: image/jpeg');
				imagejpeg( $captcha_img, '', 100);
				
				die();
				
			}
	
		}
			
	}
	
}

?>