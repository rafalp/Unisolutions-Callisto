function storeCaret(ftext){
	
	if (ftext.createTextRange){
		ftext.caretPos = document.selection.createRange().duplicate();
	}
	
}

function UniRange(start, end) {
	this.start = start;
	this.end = end;
}

function getSelection(textId) {
	ctrl = document.getElementById(textId);
	if (document.selection) {
		ctrl.focus ();
		var range = document.selection.createRange ();
		var length = range.text.length;
		range.moveStart('character', -ctrl.value.length);
		return new UniRange(range.text.length - length, range.text.length);
	} else if (ctrl.selectionStart || ctrl.selectionStart == '0') {
		return new UniRange(ctrl.selectionStart, ctrl.selectionEnd);
	}
}

function setSelection(textId, UniRange) {
	ctrl = document.getElementById(textId);
	if (ctrl.setSelectionRange) {
		ctrl.focus();
		ctrl.setSelectionRange(UniRange.start, UniRange.end);
	} else if (ctrl.createTextRange) {
		var range = ctrl.createTextRange();
		range.collapse(true);
		range.moveStart('character', UniRange.start);
		range.moveEnd('character', UniRange.end);
		range.select();
	}
}

function makeTag(textId, myRange, tag_a, tag_b) {
	var ctrl = document.getElementById(textId);
	var text = ctrl.value;
	var startText = text.substring(0, myRange.start) + tag_a;
	var middleText = text.substring(myRange.start, myRange.end);
	var endText = tag_b + text.substring(myRange.end);
	ctrl.value = startText + middleText + endText;
	setSelection(textId, new UniRange(startText.length, startText.length + middleText.length));
}

function makeSoloTag(textId, myRange, tag) {
	var ctrl = document.getElementById(textId);
	var text = ctrl.value;
	var startText = text.substring(0, myRange.start);
	var middleText = tag;
	var endText = text.substring(myRange.end);
	ctrl.value = startText + middleText + endText;
	setSelection(textId, new UniRange(startText.length + middleText.length, startText.length + middleText.length));
}

function addSimpleTag(textId, tag_a, tag_b) {
	makeTag(textId, getSelection(textId), tag_a, tag_b);
}

function addEmo( textId, emo){
	
	makeSoloTag(textId, getSelection(textId), emo);
	
}

function addSizeTag( textId, size){
	
	size_select = document.getElementById( size);
	
	if ( size_select.value != "chose"){
	
		addSimpleTag ( textId, "[size=" + size_select.value +"]", "[/size]")
		size_select.value = "chose"
	
	}
}

function addColorTag( textId, size){
	
	size_select = document.getElementById( size);
	
	if ( size_select.value != "chose"){
	
		addSimpleTag ( textId, "[color=" + size_select.value +"]", "[/color]")
		size_select.value = "chose"
			
		size_select.value = "chose"
		
	}
}