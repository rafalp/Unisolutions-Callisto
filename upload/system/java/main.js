function switchDivDisplay( name){
	
	div_obj = document.getElementById( name);
	
	if(div_obj.style.display == "none"){
		div_obj.style.display = ""
	}else{
		div_obj.style.display = "none"
	}
	
}

function trim( text){
	return text.replace(/^\s+|\s+$/g, '')
}

function showDivs( list){
	
	for(i =0; i< list.length; i++){
	 list[i].style.display = ""
	}
}

function hideDivs( list){
	
	for(i =0; i< list.length; i++){
	 list[i].style.display = "none"
	}
}

function showDiv( name){
	
	div_obj = document.getElementById( name);
	div_obj.style.display = ""

}

function hideDiv( name){
	
	div_obj = document.getElementById( name);
	
	div_obj.style.display = "none"
	
}

function addPostToMultiquote( post){
	
	button_obj = document.getElementById( "mquote_button_" + post);
			
	if( topic_quote[post] == post){
	
		topic_quote[post] = null;
		button_obj.src = mutliqoute_button_add;
								
	}else{
		
		topic_quote[post] = post;
		button_obj.src = mutliqoute_button_remove;
		
	}
	
	document.cookie = "topic_quote=" + topic_quote.toString();
	
}

function switchBlockDisplay( block_name, switcher_name, style_path){
	
	block_div = document.getElementById( block_name);
	block_button = document.getElementById( switcher_name);

	if(block_div.style.display == "none"){
		
		block_div.style.display = ""
		block_button.innerHTML = "<img src='" + style_path + "/forum_collapse.png' ALT='' TITLE='' />"
		
		document.cookie = block_name + "_visible=1"
		
	}else{
		
		block_div.style.display = "none"
		block_button.innerHTML = "<img src='" + style_path + "/forum_open.png' ALT='' TITLE='' />"
		
		document.cookie = block_name + "_visible=0"
		
	}
	
}

function getBlockDisplay( block_name, switcher_name, style_path){

	block_div = document.getElementById( block_name);
	block_button = document.getElementById( switcher_name);
	
	if( document.cookie.length == 0 ){
		
		show_cat = false
		
	}else{
		
		if( document.cookie.indexOf( block_name + "_visible=1" ) != -1){
			
			show_cat = true
			
		}else{
			
			show_cat = false
			
		}
		
	}
	
	if( show_cat){
		
		block_div.style.display = ""
		block_button.innerHTML = "<img src='" + style_path + "/forum_collapse.png' ALT='' TITLE='' />"
		
	}else{
		
		block_div.style.display = "none"
		block_button.innerHTML = "<img src='" + style_path + "/forum_open.png' ALT='' TITLE='' />"
		
	}
	
}

function getBlockDisplayR( block_name, switcher_name, style_path){
	
	block_div = document.getElementById( block_name);
	block_button = document.getElementById( switcher_name);
		
	if( document.cookie.length == 0 ){
		
		show_cat = true
	
	}else{
		
		if( document.cookie.indexOf( block_name + "_visible=0" ) != -1){
			
			show_cat = false
		}else{
			
			show_cat = true
			
		}
		
	}
	
	
	if( show_cat){
		
		block_div.style.display = ""
		block_button.innerHTML = "<img src='" + style_path + "/forum_collapse.png' ALT='' TITLE='' />"
		
	}else{
		
		block_div.style.display = "none"
		block_button.innerHTML = "<img src='" + style_path + "/forum_open.png' ALT='' TITLE='' />"
		
	}
	
}