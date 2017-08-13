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
|	Exceptions Class
|	by Rafał Pitoń
|
#===========================================================================
*/

if( ! defined( 'IN_UNI' ))
	exit('<h1>CRITICAL ERROR</h1>This file cannot be accessed directly.');
	
class uniException extends Exception{
	
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0) {
    
        // make sure everything is assigned properly
        parent::__construct($message, $code);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
	
    public function criticalError( $code) {
    	
    	global $output;
    	global $error_types;
    	
    	/**
    	 * check if error page sheme exists
    	 */
    	
    	if( file_exists( ROOT_PATH.'system/templates/critical_error_page.htm')){
    		
    		/**
    		 * error page sheme found, lets use it to display page.
    		 */
    		
    		$output -> addToOutput( file_get_contents( ROOT_PATH.'system/templates/critical_error_page.htm'));
    		
    		$output -> addToParse( 'ERROR_NAME', $error_types[$code].' ERROR');
    		$output -> addToParse( 'ERROR_TYPE', $error_types[$code]);
    		$output -> addToParse( 'ERROR_MSG', $this -> message);
    		
    	}else{
    		/**
    		 * no error sheme file found, so we have to simply write error messahge
    		 */
    			
    		$output -> addToOutput( '<h1>Critical Error Occured</h1>'.$this -> message);
       		
    	}
    	
    	exit;
    
    }
		
}

?>