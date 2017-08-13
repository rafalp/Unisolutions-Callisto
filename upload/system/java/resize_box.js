function increaseTextBoxSize( name){	
		
	text_box = document.getElementById( name)
	
	text_box.rows += 20
	
}

function decreaseTextBoxSize( name){
	
	text_box = document.getElementById( name)
	
	if( text_box.rows > 20){
		text_box.rows -= 20
	}	
	
}

function resetTextBoxSize( name){
	
	text_box = document.getElementById( name)
	
	text_box.rows = 13
	
}