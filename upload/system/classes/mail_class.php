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
|	Mails Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class mail{
	
	/**
	 * address used to send mails
	 *
	 * @var string
	 */
	
	public $mail_address;
	
	/**
	 * sending method. 0 if mail(), 1 if smtp
	 *
	 * @var bool
	 */
	
	public $use_smtp;
	
	/**
	 * smtp host
	 *
	 * @var string
	 */
	
	public $smtp_host;
	
	/**
	 * smtp port
	 *
	 * @var int
	 */
	
	public $smtp_port;
	
	/**
	 * smtp user name
	 *
	 * @var string
	 */
	
	public $smtp_username;
	
	/**
	 * smtp password
	 *
	 * @var string
	 */
	
	public $smtp_pass;
	
	/**
	 * smtp server timeout
	 *
	 * @var int
	 */
	
	public $timeout;
	
	/**
	 * smtp connection pointer
	 *
	 * @var object
	 */
	
	public $smtp;
	
	public $smtp_connected = false;
	
	function __construct(){
		
		global $settings;
		global $logs;
		
		/**
		 * set up everything
		 */
		
		$this -> mail_address = $settings['email_address'];
		$this -> use_smtp = $settings['email_send_method'];
		$this -> smtp_host = $settings['email_smtp_host'];
		$this -> smtp_port = $settings['email_smtp_port'];
		$this -> smtp_username = $settings['email_smtp_username'];
		$this -> smtp_pass = $settings['email_smtp_pass'];
		$this -> timeout = $settings['email_smtp_timeout'];
		
		/**
		 * set keys
		 */
		
		$this -> mail_keys['SITE_NAME'] = $settings['board_name'];
		$this -> mail_keys['SITE_URL'] = $settings['board_address'];
				
	}
		
	function send( $to, $subject, $content){
		
		global $logs;
		global $settings;
		
		if( $this -> use_smtp){
			
			/**
			 * send mail using SMTP
			 */

			if ( $this -> smtp_connected == false){
				
				/**
				 * it is first mail, try to connect
				 */
				
				$this -> smtp = fsockopen( $this -> smtp_host, $this -> smtp_port, $errnom, $errstr, $this -> timeout);
		
				if( empty( $this -> smtp)){
		
					/**
					 * not connected
					 * return to mail
					 */
								
					$this -> use_smtp = false;
									
				}else{
					
					/**
					 * we connected to SMTP serv
					 * 
					 */
    				
    				if( substr( fgets( $this -> smtp, 512), 0, 3) == "220"){
    					
    					/**
    					 * we are welcome, auth now
    					 */
    					
    					fputs( $this -> smtp, "HELO ".$this -> smtp_host."\r\n");	
						
    					if( substr( fgets( $this -> smtp, 512), 0, 3) == "250"){
    						
    						/**
    						 * serven id send
    						 */
    					
    						fputs( $this -> smtp, "AUTH LOGIN\r\n");
    					
    						if( substr( fgets( $this -> smtp, 512), 0, 3) == "334"){
    							
    							/**
    							 * send user name
    							 */
    							
    							fputs( $this -> smtp, base64_encode($this -> smtp_username)."\r\n");
    							
    							if( substr( fgets( $this -> smtp, 512), 0, 3) == "334"){
    						
    								/**
    								 * send password
    								 */
    								
    								fputs( $this -> smtp, base64_encode($this -> smtp_pass)."\r\n");
    								
    								if( substr( fgets( $this -> smtp, 512), 0, 3) == "235"){
    								
    									/**
    									 * we are in, give him from
    									 */
    									
    									$this -> smtp_connected = true;
    								}
    							}
    						}
    					}
    				}
				}
			}
			
			/**
			 * send mail
			 */
			
			fputs( $this -> smtp, "MAIL FROM:<".$this -> mail_address.">\r\n");
    								
			if( substr( fgets( $this -> smtp, 512), 0, 3) == "250"){
				
				/**
				 * accepted, give to
				 */
				
				fputs( $this -> smtp, "RCPT TO:<".$to.">\r\n");
				
				if( substr( fgets( $this -> smtp, 512), 0, 3) == "250"){
					
					/**
					 * send data
					 */
					
					fputs( $this -> smtp, "DATA\r\n");
					
					if( substr( fgets( $this -> smtp, 512), 0, 3) == "354"){
						
						/**
						 * we can send mail
						 */
						
						foreach ( $this -> mail_keys as $key_id => $key_content){
				
							$subject = str_replace( "{".$key_id."}", $key_content, $subject);
							$content = str_replace( "{".$key_id."}", $key_content, $content);
							
						}
						
						/**
						 * send to server
						 */
						
						fputs( $this -> smtp, "Content-type: text/plain; charset=UTF-8\r\n");
						fputs( $this -> smtp, "From: ".$settings['board_name']."<".$this -> mail_address.">\r\n");
						fputs( $this -> smtp, "Subject: ".$subject."\r\n");
						fputs( $this -> smtp, "\r\n".$content."\r\n.\r\n");
						
					}
				}
			}
			
		}else{
			
			/**
			 * parse mails
			 */
			
			foreach ( $this -> mail_keys as $key_id => $key_content){
				
				$subject = str_replace( "{".$key_id."}", $key_content, $subject);
				$content = str_replace( "{".$key_id."}", $key_content, $content);
				
			}
			
			/**
			 * send mail using mail()
			 */
			
			$mail_headers = array(
				"From: ".$settings['board_name']." <".$this -> mail_address.">",
				"MIME-Version: 1.0",
				"Content-Type: text/plain; charset=UTF-8"
			);
			
			mail( $to, $subject, $content, join( "\n", $mail_headers));
			     
			$logs -> addMailLog( 1, 1, $to);
			    	
		}
		
	}
	
}

?>