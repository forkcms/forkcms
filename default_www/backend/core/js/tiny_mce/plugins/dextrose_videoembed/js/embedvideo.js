/**
 * Saves content if submit button is pushed
 */
function saveContent() {
	var embedcode = document.getElementById("embedcode").value;
	
	if (embedcode == ''){
		tinyMCEPopup.close();
		return false;
	}

	tinyMCEPopup.execCommand('pasteVideo', false, embedcode);
	tinyMCEPopup.close();
}

function onLoadInit() {
	tinyMCEPopup.resizeToInnerSize();
}

tinyMCEPopup.onInit.add(onLoadInit);
