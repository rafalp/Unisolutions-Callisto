function GetXmlHttpObject(){
	var uniAJAX=null;
	
	try{
		
		// Firefox, Opera 8.0+, Safari
		uniAJAX = new XMLHttpRequest();
	
	}catch (e){
		
		// Internet Explorer
		
		try{

			uniAJAX = new ActiveXObject("Msxml2.XMLHTTP");
		
		}catch (e){
			
			uniAJAX = new ActiveXObject("Microsoft.XMLHTTP");
		
		}
	
	}
	
	return uniAJAX;
}