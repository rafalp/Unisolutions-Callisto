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
|	Form Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class form{

	public $content;
	private $templates;
	
	/**
	 * constructor loads template files
	 *
	 */
	
	function __construct(){
		
		$this -> templates['row'] = file_get_contents( ROOT_PATH.'system/templates/forms/row.html');
		$this -> templates['row_info'] = file_get_contents( ROOT_PATH.'system/templates/forms/row_info.html');
		$this -> templates['row_list'] = file_get_contents( ROOT_PATH.'system/templates/forms/row_list.html');
		$this -> templates['row_fulldate'] = file_get_contents( ROOT_PATH.'system/templates/forms/row_fulldate.html');
		$this -> templates['row_date'] = file_get_contents( ROOT_PATH.'system/templates/forms/row_date.html');
		$this -> templates['row_hour'] = file_get_contents( ROOT_PATH.'system/templates/forms/row_hour.html');
		$this -> templates['row_yesno'] = file_get_contents( ROOT_PATH.'system/templates/forms/row_yesno.html');
		$this -> templates['row_textimput'] = file_get_contents( ROOT_PATH.'system/templates/forms/row_textimput.html');
		$this -> templates['row_file'] = file_get_contents( ROOT_PATH.'system/templates/forms/row_file.html');
		$this -> templates['row_password'] = file_get_contents( ROOT_PATH.'system/templates/forms/row_password.html');
		$this -> templates['row_textbox'] = file_get_contents( ROOT_PATH.'system/templates/forms/row_textbox.html');
		$this -> templates['row_editor'] = file_get_contents( ROOT_PATH.'system/templates/forms/row_editor.html');
		$this -> templates['row_editor_simple'] = file_get_contents( ROOT_PATH.'system/templates/forms/row_editor_simple.html');
		$this -> templates['spacer'] = file_get_contents( ROOT_PATH.'system/templates/forms/spacer.html');
		$this -> templates['submit_button'] = file_get_contents( ROOT_PATH.'system/templates/forms/submit_button.html');
		
	}
	
	function openForm($action, $method = 'POST', $file = false, $name = 'form'){
		
		if($file == true){
			
			$filetag = 'ENCTYPE="multipart/form-data"';
			
		}
				
		$result = "<form name=\"$name\" action=\"$action\" method=\"$method\" $filetag>";	
		
   		$this -> content .= $result;
   		
   		/**
   		 * also register form id
   		 */
   		
   		$form_id = time();
   		
   		$this -> content .= '<input name="form_id" type="hidden" value="'.$form_id.'" />';  		
   		
	}
	
	function closeForm(){
		
   		$this -> content .=  "</form>";
	}
	
	function hiddenValue( $name, $value = null){
		
   		$this -> content .=  '<input name="'.$name.'" type="hidden" value="'.$value.'" />';
	}
	
	function drawButton( $name = false, $id = false, $additionals = ''){
		
		global $language;
		
		if(!$name)
			$name = $language -> getString( 'send');
		
		$template = $this -> templates['submit_button'];
		
		if ( $id != false){
			$template = str_ireplace( '{ID}', "id=\"".$id.'"', $template);	
		}else{
			$template = str_ireplace( '{ID}', '', $template);	
		}
		
		/**
		 * additionals
		 */
		
		$template = str_ireplace( '{NAME}', $name, $template);
		$this -> content .= str_ireplace( '{OTHER_BUTTONS}', $additionals, $template);
	 }
	 
	function drawSpacer( $title = false){
		
		$template = $this -> templates['spacer'];
	 	
		$this -> content .= str_ireplace( '{TEXT}', $title, $template);
	 }
	 
	 function openOpTable( $fix_table = false){
	 	
	 	if ( $fix_table){
	 		$fix_table_html = 'style="table-layout: fixed"';
	 	}else{
	 		$fix_table_html = '';
	 	}
	 	
	 	$this -> content .= '<table class="opt" '.$fix_table_html.' align="center">';
	 	
	 }
	 
	 function closeTable(){
	 	
	 	$this -> content .= '</table>';
	 	
	 }
	 
	 function drawInfoRow( $name, $value, $help = null){
	 	
	 	$template = $this -> templates['row_info'];
	 	
		$template = str_ireplace( '{NAME}', $name, $template);
		
	 	$template = $this -> parseHelp( $help, $template);
		$this -> content .= str_ireplace( '{VALUE}', $value, $template);
	 	
	 }
	 
	  function drawRow( $content){
	 	
	 	$template = $this -> templates['row'];
		$this -> content .= str_ireplace( '{TEXT}', $content, $template);
	 	
	 }
	 
	 function drawTextInput( $name, $variable,  $value = null, $help = null){
	 	
	 	$template = $this -> templates['row_textimput'];
	 	
	 	$template = str_ireplace( '{NAME}', $name, $template);
	 	$template = str_ireplace( '{VARIABLE}', $variable, $template);
	 	$template = str_ireplace( '{TEXT}', $value, $template);
	 	
	 	$template = $this -> parseHelp( $help, $template);
	 	
	 	$this -> content .= $template;
	 	
	 }
	 
	  function drawFile( $name, $variable,  $value = null, $help = null){
	 	
	 	$template = $this -> templates['row_file'];
	 	
	 	$template = str_ireplace( '{NAME}', $name, $template);
	 	$template = str_ireplace( '{VARIABLE}', $variable, $template);
	 	$template = str_ireplace( '{TEXT}', $value, $template);
	 	
	 	$template = $this -> parseHelp( $help, $template);
	 	
	 	$this -> content .= $template;
	 	
	 }
	 
	 function drawPassInput( $name, $variable,  $value = null, $help = null){
	 	
	 	$template = $this -> templates['row_password'];
	 	
	 	$template = str_ireplace( '{NAME}', $name, $template);
	 	$template = str_ireplace( '{VARIABLE}', $variable, $template);
	 	$template = str_ireplace( '{TEXT}', $value, $template);
	 	
	 	$template = $this -> parseHelp( $help, $template);
	 	
	 	$this -> content .= $template;
	 	
	 }
	 
	 function drawTextBox ( $name, $variable, $value = null, $help = null){
	 	
	 	global $style;
	 	global $language;
	 	
	 	$template = $this -> templates['row_textbox'];
	 	
	 	$template = str_ireplace( '{PLUS}', $style -> drawImage( 'plus', $language -> getString( 'edition_filed_increase')), $template);
	 	$template = str_ireplace( '{MINUS}', $style -> drawImage( 'minus', $language -> getString( 'edition_filed_decrease')), $template);
	 	$template = str_ireplace( '{RESET}', $style -> drawImage( 'refresh', $language -> getString( 'edition_filed_reset')), $template);
	 	
	 	
	 	$template = str_ireplace( '{NAME}', $name, $template);
	 	$template = str_ireplace( '{VARIABLE}', $variable, $template);
	 	$template = str_ireplace( '{TEXT}', $value, $template);
	 	
	 	$template = $this -> parseHelp( $help, $template);
	 	
	 	$this -> content .= $template;
	 	
	 }
	 
	 function drawSimpleEditor ( $variable, $value = null, $bb_Tags = false, $emotes = false){
	 	
	 	global $style;
	 	global $language;
	 	global $strings;
	 	
	 	$template = $this -> templates['row_editor_simple'];
	 	
	 	$template = str_ireplace( '{PLUS}', $style -> drawImage( 'plus', $language -> getString( 'edition_filed_increase')), $template);
	 	$template = str_ireplace( '{MINUS}', $style -> drawImage( 'minus', $language -> getString( 'edition_filed_decrease')), $template);
	 	$template = str_ireplace( '{RESET}', $style -> drawImage( 'refresh', $language -> getString( 'edition_filed_reset')), $template);
	 	
	 	if ( $bb_Tags){
	 			 			 
	 		$bb_editor[] = '<input type="button" name="B" value="B" onclick="addSimpleTag(\''.$variable.'\', \'[b]\', \'[/b]\')" />';
	 		$bb_editor[] = '<input type="button" name="I" value="I" onclick="addSimpleTag(\''.$variable.'\', \'[i]\', \'[/i]\')" />';
	 		$bb_editor[] = '<input type="button" name="U" value="U" onclick="addSimpleTag(\''.$variable.'\', \'[u]\', \'[/u]\')" />';
	 		$bb_editor[] = '<input type="button" name="URL" value="Url" onclick="addSimpleTag(\''.$variable.'\', \'[url]\', \'[/url]\')" />';
	 		$bb_editor[] = '<input type="button" name="IMG" value="Img" onclick="addSimpleTag(\''.$variable.'\', \'[img]\', \'[/img]\')" />';
	 		$bb_editor[] = '<input type="button" name="QOTE" value="'.$language -> getString( 'bb_quote').'" onclick="addSimpleTag(\''.$variable.'\', \'[quote]\', \'[/quote]\')" />';
	 		$bb_editor[] = '<input type="button" name="CODE" value="'.$language -> getString( 'bb_code').'" onclick="addSimpleTag(\''.$variable.'\', \'[code]\', \'[/code]\')" />';
	 		
	 		$bb_editor[] = $language -> getString( 'edition_size').': <select name="'.$variable.'_size" onchange="addSizeTag( \''.$variable.'\', this.name )">
	 		<option value="chose">'.$language -> getString( 'edition_chose').'</option>
	 		<option value="smaller">'.$language -> getString( 'edition_size_smaller').'</option>
	 		<option value="small">'.$language -> getString( 'edition_size_small').'</option>
	 		<option value="medium">'.$language -> getString( 'edition_size_medium').'</option>
	 		<option value="large">'.$language -> getString( 'edition_size_large').'</option>
	 		<option value="x-large">'.$language -> getString( 'edition_size_larger').'</option>
	 		</select>';
	 		
	 		$bb_editor[] = $language -> getString( 'edition_color').': <select name="'.$variable.'_color" onchange="addColorTag( \''.$variable.'\', this.name )">
	 		<option value="chose">'.$language -> getString( 'edition_chose').'</option>
	 		<option value="black">'.$language -> getString( 'edition_color_black').'</option>
	 		<option value="red">'.$language -> getString( 'edition_color_red').'</option>
	 		<option value="green">'.$language -> getString( 'edition_color_green').'</option>
	 		<option value="blue">'.$language -> getString( 'edition_color_blue').'</option>
	 		</select>';
	 				
	 		$template = str_ireplace( '{BBTAGS}', join( " ", $bb_editor), $template);
	 		
	 	}else{
	 		
	 		$template = str_ireplace( '{BBTAGS}', '', $template);
	 		
	 	}
	 	
	 	if ( $emotes){
	 		
	 		foreach ( $strings -> emoticons as $emo_id => $emo_props)
	 			$emo_editor[] = '<a href="javascript:addEmo(\''.$variable.'\', \''.$emo_id.'\')"><img src="'.ROOT_PATH.'images/emoticons/'.$emo_props['image'].'" alt="'.$emo_props['name'].'" title="'.$emo_props['name'].'"></a>';
	 		
	 		$template = str_ireplace( '{EMOS}', join( " ", $emo_editor), $template);
	 		
	 	}else{
	 		
	 		$template = str_ireplace( '{EMOS}', '', $template);
	 		
	 	}
	 	
	 	$template = str_ireplace( '{VARIABLE}', $variable, $template);
	 	$template = str_ireplace( '{TEXT}', $value, $template);
	 		 	
	 	return $template;
	 	
	 }
	 
	 function drawEditor ( $name, $variable, $value = null, $help = null, $bb_Tags = false, $emotes = false){
	 	
	 	global $style;
	 	global $language;
	 	global $strings;
	 	
	 	$template = $this -> templates['row_editor'];
	 	
	 	$template = str_ireplace( '{PLUS}', $style -> drawImage( 'plus', $language -> getString( 'edition_filed_increase')), $template);
	 	$template = str_ireplace( '{MINUS}', $style -> drawImage( 'minus', $language -> getString( 'edition_filed_decrease')), $template);
	 	$template = str_ireplace( '{RESET}', $style -> drawImage( 'refresh', $language -> getString( 'edition_filed_reset')), $template);
	 	
	 	if ( $bb_Tags){
	 			 
			$bb_tags['BOLD'] = array(
				'name' => $language -> getString( 'edition_bbtag_bold'),
				'image' => 'format_text_bold.png'
			);
			
			$bb_tags['ITALIC'] = array(
				'name' => $language -> getString( 'edition_bbtag_italic'),
				'image' => 'format_text_italic.png'
			);
			
			$bb_tags['UNDERLINE'] = array(
				'name' => $language -> getString( 'edition_bbtag_underline'),
				'image' => 'format_text_underline.png'
			);
			
			$bb_tags['STROKE'] = array(
				'name' => $language -> getString( 'edition_bbtag_stroke'),
				'image' => 'format_text_strikethrough.png'
			);
			
			$bb_tags['JUSTIFY_LEFT'] = array(
				'name' => $language -> getString( 'edition_bbtag_justify_left'),
				'image' => 'format_justify_left.png'
			);
			
			$bb_tags['JUSTIFY_CENTER'] = array(
				'name' => $language -> getString( 'edition_bbtag_justify_center'),
				'image' => 'format_justify_center.png'
			);
			
			$bb_tags['JUSTIFY_RIGHT'] = array(
				'name' => $language -> getString( 'edition_bbtag_justify_right'),
				'image' => 'format_justify_right.png'
			);
			
			$bb_tags['SUPERSCRIPT'] = array(
				'name' => $language -> getString( 'edition_bbtag_supscript'),
				'image' => 'format_text_superscript.png'
			);
			
			$bb_tags['SUBSCRIPT'] = array(
				'name' => $language -> getString( 'edition_bbtag_subscript'),
				'image' => 'format_text_subscript.png'
			);
			
			/**
			 * secound row
			 */
			
			$bb_tags['URL'] = array(
				'name' => $language -> getString( 'edition_bbtag_url'),
				'image' => 'link.png'
			);
						
			$bb_tags['MAIL'] = array(
				'name' => $language -> getString( 'edition_bbtag_mail'),
				'image' => 'mail.png'
			);
						
			$bb_tags['FTP'] = array(
				'name' => $language -> getString( 'edition_bbtag_ftp'),
				'image' => 'link_ftp.png'
			);
			
			$bb_tags['IMG'] = array(
				'name' => $language -> getString( 'edition_bbtag_img'),
				'image' => 'image.png'
			);
				
			$bb_tags['QUOTE'] = array(
				'name' => $language -> getString( 'edition_bbtag_quote'),
				'image' => 'quote.png'
			);
			
			$bb_tags['CODE'] = array(
				'name' => $language -> getString( 'edition_bbtag_code'),
				'image' => 'code.png'
			);
			
			$bb_tags['SPOILER'] = array(
				'name' => $language -> getString( 'edition_bbtag_spoiler'),
				'image' => 'spoiler.png'
			);	
			
			$bb_tags['LINE'] = array(
				'name' => $language -> getString( 'edition_bbtag_line'),
				'image' => 'line.png'
			);
			
			/**
			 * draw them
			 */
				
			foreach ( $bb_tags as $tag_id => $tag_props){
				
				$template = str_ireplace( '{'.$tag_id.'_IMAGE}', ROOT_PATH.'images/editor_images/'.$tag_props['image'], $template);
				$template = str_ireplace( '{'.$tag_id.'_NAME}', $tag_props['name'], $template);
				
			}
			
			/**
			 * sizes selector
			 */
	 				 
			$sizes_list['chose'] = $language -> getString( 'edition_chose_size');
			$sizes_list['smaller'] = $language -> getString( 'edition_size_smaller');
			$sizes_list['small'] = $language -> getString( 'edition_size_small');
			$sizes_list['medium'] = $language -> getString( 'edition_size_medium');
			$sizes_list['large'] = $language -> getString( 'edition_size_large');
			$sizes_list['x-large'] = $language -> getString( 'edition_size_larger');
			
			$generated_list = '';
							
			foreach ( $sizes_list as $size_id => $size_name)
				$generated_list .= '<option value="'.$size_id.'">'.$size_name.'</option>';
			
			$template = str_ireplace( '{SIZES_LIST}', $generated_list, $template);
			
			/**
			 * colors selector
			 */
			
			$colors_list['chose'] = $language -> getString( 'edition_chose_color');
			$colors_list['black'] = $language -> getString( 'edition_color_black');
			$colors_list['white'] = $language -> getString( 'edition_color_white');
			$colors_list['red'] = $language -> getString( 'edition_color_red');
			$colors_list['green'] = $language -> getString( 'edition_color_green');
			$colors_list['blue'] = $language -> getString( 'edition_color_blue');
			$colors_list['yellow'] = $language -> getString( 'edition_color_yellow');
			$colors_list['pink'] = $language -> getString( 'edition_color_pink');
			$colors_list['orange'] = $language -> getString( 'edition_color_orange');
			$colors_list['purple'] = $language -> getString( 'edition_color_purple');
			$colors_list['beige'] = $language -> getString( 'edition_color_beige');
			$colors_list['teal'] = $language -> getString( 'edition_color_teal');
			$colors_list['navy'] = $language -> getString( 'edition_color_navy');
			$colors_list['maroon'] = $language -> getString( 'edition_color_maroon');
			$colors_list['limeGreen'] = $language -> getString( 'edition_color_limegreen');
			
			$generated_list = '';
							
			foreach ( $colors_list as $color_id => $color_name)
				$generated_list .= '<option value="'.$color_id.'">'.$color_name.'</option>';
			
			$template = str_ireplace( '{COLORS_LIST}', $generated_list, $template);
			
			
	 		$bb_editor[] = '<input type="button" name="B" value="B" onclick="addSimpleTag(\''.$variable.'\', \'[b]\', \'[/b]\')" />';
	 		$bb_editor[] = '<input type="button" name="I" value="I" onclick="addSimpleTag(\''.$variable.'\', \'[i]\', \'[/i]\')" />';
	 		$bb_editor[] = '<input type="button" name="U" value="U" onclick="addSimpleTag(\''.$variable.'\', \'[u]\', \'[/u]\')" />';
	 		$bb_editor[] = '<input type="button" name="URL" value="Url" onclick="addSimpleTag(\''.$variable.'\', \'[url]\', \'[/url]\')" />';
	 		$bb_editor[] = '<input type="button" name="IMG" value="Img" onclick="addSimpleTag(\''.$variable.'\', \'[img]\', \'[/img]\')" />';
	 		$bb_editor[] = '<input type="button" name="QOTE" value="'.$language -> getString( 'bb_quote').'" onclick="addSimpleTag(\''.$variable.'\', \'[quote]\', \'[/quote]\')" />';
	 		$bb_editor[] = '<input type="button" name="CODE" value="'.$language -> getString( 'bb_code').'" onclick="addSimpleTag(\''.$variable.'\', \'[code]\', \'[/code]\')" />';
	 		
	 		$template = str_ireplace( '{SPACER}', '<img src="'.ROOT_PATH.'images/editor_images/spacer.gif" />', $template);
					 		
	 		$template = str_replace( "<style:bbtags>", "", $template);
			$template = str_replace( "</style:bbtags>", "", $template);	
	 		
			/**
			 * custom bbtags now
			 */
			
			$draw_tags = array();
			
			foreach ( $strings -> custom_bbtags as $tag_ops){
				
				if ( $tag_ops['tag_draw']){
					
					$tag_info = '';
					
					if ( strlen( $tag_ops['tag_info']) > 0)
						$tag_info = ' - '.$tag_ops['tag_info'];
					
					if ( $tag_ops['tag_option']){
						
						$draw_tags[] = '<a href="javascript:addSimpleTag( \''.$variable.'\', \'['.$tag_ops['tag_tag'].'=]\', \'[/'.$tag_ops['tag_tag'].']\')">'.$tag_ops['tag_name'].'</a>'.$tag_info;
					
					}else{
					
						$draw_tags[] = '<a href="javascript:addSimpleTag( \''.$variable.'\', \'['.$tag_ops['tag_tag'].']\', \'[/'.$tag_ops['tag_tag'].']\')">'.$tag_ops['tag_name'].'</a>'.$tag_info;
					
					}
				}
				
			}
			
			if ( count( $draw_tags) > 0){
				
				$template = str_ireplace( '{CUSTOM_TAGS}', $style -> drawBlock( $language -> getString( 'edition_bbtags_custom'), join( "<br />", $draw_tags)), $template);
			
			}else{
			
				$template = str_ireplace( '{CUSTOM_TAGS}', '', $template);
			
			}
			
	 	}else{
	 		
	 		$template = preg_replace("#\<style:bbtags\>(.*?)\</style:bbtags\>#si",'', $template);				
			
	 	}
	 	
	 	if ( $emotes){
	 		
	 		$col = 0;
	 		
	 		$emo_editor = '<table width="100%" border="0" cellspacing="0" cellpadding="4" style="table-layout:fixed">
  				<tr>';
	 		
	 		foreach ( $strings -> emoticons as $emo_id => $emo_props){
	 			
	 			if ( $col >= 4){
	 				
	 				$col = 0;
	 				$emo_editor .= '</tr><tr>';
	 				
	 			}
	 			
	 			$emo_editor .= '<td style="vertical-align: middle; text-align: center"><a href="javascript:addEmo(\''.$variable.'\', \''.$emo_id.'\')"><img src="'.ROOT_PATH.'images/emoticons/'.$emo_props['image'].'" alt="'.$emo_props['name'].'" title="'.$emo_props['name'].'"></a></td>';
	 		
	 			$col ++;
	 		
	 		}
	 		
	 		$emo_editor .='</tr></table>';
	 		
	 		$template = str_ireplace( '{EMOS}', $style -> drawBlock( $language -> getString( 'edition_emo'), $emo_editor), $template);
	 		
	 		$template = str_replace( "<style:emoticons>", "", $template);
			$template = str_replace( "</style:emoticons>", "", $template);	
			
	 	}else{
	 		
	 		$template = preg_replace("#\<style:emoticons\>(.*?)\</style:emoticons\>#si",'', $template);
	 		
	 	}
	 	
	 	$template = str_ireplace( '{NAME}', $name, $template);
	 	$template = str_ireplace( '{VARIABLE}', $variable, $template);
	 	$template = str_ireplace( '{TEXT}', $value, $template);
	 	
	 	$template = $this -> parseHelp( $help, $template);
	 	
	 	$this -> content .= $template;
	 	
	 }
	 
	 function drawYesNo( $name, $variable, $value = true, $help = null){

	 	global $style;
	 	global $language;
	 	
	 	$template = $this -> templates['row_yesno'];
	 	
	 	$template = str_ireplace( '{YES}', $language -> getString( 'yes'), $template);
	 	$template = str_ireplace( '{NO}', $language -> getString( 'no'), $template); 	
	 	
	 	$template = str_ireplace( '{NAME}', $name, $template);
	 	$template = str_ireplace( '{VARIABLE}', $variable, $template);
	 	 	
	 	$template = $this -> parseHelp( $help, $template);
		
		if($value){
	
			$template = substr_replace( $template, '', stripos($template, '<no>'), (stripos($template, '</no>') - stripos($template, '<no>') + strlen( '</no>')));
			$template = str_ireplace( '<yes>', '', $template);
			$template = str_ireplace( '</yes>', '', $template);
			
		}else{
			
			$template = substr_replace( $template, '', stripos($template, '<yes>'), (stripos($template, '</yes>') - stripos($template, '<yes>') + strlen( '</yes>')));
			$template = str_ireplace( '<no>', '', $template);
			$template = str_ireplace( '</no>', '', $template);
			
		}
		
		
		$this -> content .= $template;
	 }
	 
	 function drawSingleTextBox ( $variable, $value = null){
	 	
	 	$this -> content .= '<tr>
					<td class="opt_row2" style="text-align: center;"><textarea id="'.$variable.'" name="'.$variable.'" cols="50" rows="9">'.$value.'</textarea></td>
				</tr>';
	 	
	 }
	 
	 function drawList ( $name, $variable, $elements, $select_pos = 0, $help = null){
	 	
	 	$template = $this -> templates['row_list'];
	 	
	 	$template = str_ireplace( '{NAME}', $name, $template);
	 	$template = str_ireplace( '{VARIABLE}', $variable, $template);
	 	$template = str_ireplace( '{SIZE}', 1, $template);
	 	
	 	$template = str_ireplace( '{MULTIPLE}', '', $template);
	 		 	
	 	$template = $this -> parseHelp( $help, $template);
	 	
	 	$parsed_list = '';
	 	
	 	foreach ($elements as $id => $value){
	 		$parsed_list .= '<option value="'.$id.'">'.$value.'</option>';
	 	}
	 	
	 	$parsed_list = str_ireplace( 'value="'.$select_pos.'"', 'value="'.$select_pos.'" selected', $parsed_list);
	 	
	 	$template = str_ireplace( '{LIST}', $parsed_list, $template);
	 	
	 	$this -> content .= $template;
	 	
	 }
	 
	 function drawMultiList ( $name, $variable, $list, $select = 0, $help = null, $size = 5){
	 	
	 	$template = $this -> templates['row_list'];
	 	
	 	$template = str_ireplace( '{NAME}', $name, $template);
	 	$template = str_ireplace( '{VARIABLE}', $variable, $template);
	 	$template = str_ireplace( '{SIZE}', $size, $template);
	 		
	 	$template = str_ireplace( '{MULTIPLE}', 'multiple="multiple"', $template);
	 	
	 	$template = $this -> parseHelp( $help, $template);
	 	
	 	$parsed_list = '';
	 	
	 	foreach ($list as $id => $value){
	 		$parsed_list .= '<option value="'.$id.'">'.$value.'</option>';
	 	}
	 	
	 	if( $select != 0){
	 		
	 		foreach ($select as $select_id => $select_value){
	 			
	 			$parsed_list = str_ireplace( 'value="'.$select_value.'"', 'value="'.$select_value.'" selected', $parsed_list);
	 	
	 		}
	 		
	 	}
	 	
	 	$template = str_ireplace( '{LIST}', $parsed_list, $template);
	 	
	 	$this -> content .= $template;
	 	
	 }	 
	 
	 function drawFullDateSelect ( $name, $variable, $time = 0, $help = null){
	 	
	 	$template = $this -> templates['row_fulldate'];
	 	
	 	$template = str_ireplace( '{NAME}', $name, $template);
	 	$template = str_ireplace( '{VARIABLE}', $variable, $template);
	 		 	
	 	$template = $this -> parseHelp( $help, $template);
	 	
	 	/**
	 	 * times now
	 	 */
	 	
	 	$time_hour = date( "G", $time);
	 	$time_minute = date( "i", $time);
	 	$time_secound = date( "s", $time);
	 	
	 	$time_year = date( "Y", $time);
	 	$time_month = date( "m", $time);
	 	$time_day = date( "d", $time);
	 	
	 	settype( $time_hour, 'integer');
	 	settype( $time_minute, 'integer');
	 	settype( $time_secound, 'integer');
	 	
	 	settype( $time_year, 'integer');
	 	settype( $time_month, 'integer');
	 	settype( $time_day, 'integer');
	 	
	 	/**
	 	 * lets draw
	 	 */
	 	
	 	$hour_list = '';
	 	
	 	for( $i = 0; $i < 24; $i++){
	 		$hour_list .= '<option value="'.$i.'">'.$i.'</value>';
	 	}
	 	
	 	$hour_list = str_ireplace( 'value="'.$time_hour.'"', 'value="'.$time_hour.'" selected', $hour_list);
	 	
	 	$template = str_ireplace( '{HOURS}', $hour_list, $template);
	 	
	 	$minutes_list = '';
	 	
	 	for( $i = 0; $i <= 59; $i++){
	 		$minutes_list .= '<option value="'.$i.'">'.$i.'</value>';
	 	}
	 	
	 	$minutes_list = str_ireplace( 'value="'.$time_minute.'"', 'value="'.$time_minute.'" selected', $minutes_list);
	 	
	 	$template = str_ireplace( '{MINUTES}', $minutes_list, $template);
	 	
	 	$secounds_list = '';
	 	
	 	for( $i = 0; $i <= 59; $i++){
	 		$secounds_list .= '<option value="'.$i.'">'.$i.'</value>';
	 	}
	 	
	 	$secounds_list = str_ireplace( 'value="'.$time_secound.'"', 'value="'.$time_secound.'" selected', $secounds_list);
	 	
	 	$template = str_ireplace( '{SECOUNDS}', $secounds_list, $template);
	 	
	 	/**
	 	 * days
	 	 */	 	
	 	
	 	$year_list = '';
	 	
	 	for( $i = 1930; $i <= date("Y"); $i++){
	 		$year_list .= '<option value="'.$i.'">'.$i.'</value>';
	 	}
	 	
	 	$year_list = str_ireplace( 'value="'.$time_year.'"', 'value="'.$time_year.'" selected', $year_list);
	 	
	 	$template = str_ireplace( '{YEARS}', $year_list, $template);
	 	
	 	$months_list = '';
	 	
	 	for( $i = 1; $i <= 12; $i++){
	 		$months_list .= '<option value="'.$i.'">'.$i.'</value>';
	 	}
	 	
	 	$months_list = str_ireplace( 'value="'.$time_month.'"', 'value="'.$time_month.'" selected', $months_list);
	 	
	 	$template = str_ireplace( '{MONTHS}', $months_list, $template);
	 	
	 	$days_list = '';
	 	
	 	for( $i = 1; $i <= 31; $i++){
	 		$days_list .= '<option value="'.$i.'">'.$i.'</value>';
	 	}
	 	
	 	$days_list = str_ireplace( 'value="'.$time_day.'"', 'value="'.$time_day.'" selected', $days_list);
	 	
	 	$template = str_ireplace( '{DAYS}', $days_list, $template);
	 	
	 	/**
	 	 * return
	 	 */
	 	
	 	$this -> content .= $template;
	 	
	 }	 	 
	 
	 function drawDateSelect ( $name, $variable, $time = 0, $help = null){
	 	
	 	$template = $this -> templates['row_date'];
	 	
	 	$template = str_ireplace( '{NAME}', $name, $template);
	 	$template = str_ireplace( '{VARIABLE}', $variable, $template);
	 		 	
	 	$template = $this -> parseHelp( $help, $template);
	 	
	 	/**
	 	 * times now
	 	 */
	 	
	 	$time_year = date( "Y", $time);
	 	$time_month = date( "m", $time);
	 	$time_day = date( "d", $time);
	 	
	 	settype( $time_year, 'integer');
	 	settype( $time_month, 'integer');
	 	settype( $time_day, 'integer');
	 	
	 	/**
	 	 * lets draw
	 	 */
	 	
	 	$year_list = '';
	 	
	 	for( $i = 1930; $i <= date("Y"); $i++){
	 		$year_list .= '<option value="'.$i.'">'.$i.'</value>';
	 	}
	 	
	 	$year_list = str_ireplace( 'value="'.$time_year.'"', 'value="'.$time_year.'" selected', $year_list);
	 	
	 	$template = str_ireplace( '{YEARS}', $year_list, $template);
	 	
	 	$months_list = '';
	 	
	 	for( $i = 1; $i <= 12; $i++){
	 		$months_list .= '<option value="'.$i.'">'.$i.'</value>';
	 	}
	 	
	 	$months_list = str_ireplace( 'value="'.$time_month.'"', 'value="'.$time_month.'" selected', $months_list);
	 	
	 	$template = str_ireplace( '{MONTHS}', $months_list, $template);
	 	
	 	$days_list = '';
	 	
	 	for( $i = 1; $i <= 31; $i++){
	 		$days_list .= '<option value="'.$i.'">'.$i.'</value>';
	 	}
	 	
	 	$days_list = str_ireplace( 'value="'.$time_day.'"', 'value="'.$time_day.'" selected', $days_list);
	 	
	 	$template = str_ireplace( '{DAYS}', $days_list, $template);
	 	
	 	/**
	 	 * return
	 	 */
	 	
	 	$this -> content .= $template;
	 	
	 }
	 
	 function drawHourSelect ( $name, $variable, $time = 0, $help = null){
	 	
	 	$template = $this -> templates['row_hour'];
	 	
	 	$template = str_ireplace( '{NAME}', $name, $template);
	 	$template = str_ireplace( '{VARIABLE}', $variable, $template);
	 		 	
	 	$template = $this -> parseHelp( $help, $template);
	 	
	 	/**
	 	 * times now
	 	 */
	 	
	 	$time_hour = date( "G", $time);
	 	$time_minute = date( "i", $time);
	 	$time_secound = date( "s", $time);
	 	
	 	settype( $time_hour, 'integer');
	 	settype( $time_minute, 'integer');
	 	settype( $time_secound, 'integer');
	 	
	 	/**
	 	 * lets draw
	 	 */
	 	
	 	$hour_list = '';
	 	
	 	for( $i = 0; $i < 24; $i++){
	 		$hour_list .= '<option value="'.$i.'">'.$i.'</value>';
	 	}
	 	
	 	$hour_list = str_ireplace( 'value="'.$time_hour.'"', 'value="'.$time_hour.'" selected', $hour_list);
	 	
	 	$template = str_ireplace( '{HOURS}', $hour_list, $template);
	 	
	 	$minutes_list = '';
	 	
	 	for( $i = 0; $i <= 59; $i++){
	 		$minutes_list .= '<option value="'.$i.'">'.$i.'</value>';
	 	}
	 	
	 	$minutes_list = str_ireplace( 'value="'.$time_minute.'"', 'value="'.$time_minute.'" selected', $minutes_list);
	 	
	 	$template = str_ireplace( '{MINUTES}', $minutes_list, $template);
	 	
	 	$secounds_list = '';
	 	
	 	for( $i = 0; $i <= 59; $i++){
	 		$secounds_list .= '<option value="'.$i.'">'.$i.'</value>';
	 	}
	 	
	 	$secounds_list = str_ireplace( 'value="'.$time_secound.'"', 'value="'.$time_secound.'" selected', $secounds_list);
	 	
	 	$template = str_ireplace( '{SECOUNDS}', $secounds_list, $template);
	 		 	
	 	/**
	 	 * return
	 	 */
	 	
	 	$this -> content .= $template;
	 	
	 }
	 
	 function drawSelect( $name, $select = false){
				
		if( $select){
			
			$selected = 'checked';
			
		}else{
			
			$selected = '';
			
		}
			
		$to_return = '<input name="'.$name.'" type="checkbox" value="1" '.$selected.' />';
			
		return $to_return;
		
	}
	 
	 function addToContent( $html){
	 	
	 	$this -> content .= $html;
	 	
	 }
	 
	 function display(){
	 	
	 	return $this -> content;
	 	
	 }
	 
	 function parseHelp( $help, $html){
	 	
	 	/**
		 * parse help
		 */
		
		if($help != null){
			
			/**
			 * there are blocks in head, so we will put them into template
			 */

			$html = str_ireplace( '{HELP}', $help, $html);
			$html = str_ireplace( '<help>', '', $html);
			$html = str_ireplace( '</help>', '', $html);
			
		}else{
			
			/**
			 * there are no blocks in head, so delete everything between tags
			 */
			
			$html = substr_replace( $html, '', stripos($html, '<help>'), (stripos($html, '</help>') - stripos($html, '<help>') + strlen( '</help>')));
		}
		
		return $html;
	 	
	 }
	
}

?>