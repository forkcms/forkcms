if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.tags = {
	init: function() {
		if($('.datagrid td.tag').length > 0) {
			// buil ajax-url
			var url = '/backend/ajax.php?module=' + jsBackend.current.module + '&action=edit&language=' + jsBackend.current.language;

			// bind 
			$('.datagrid td.tag').inlineTextEdit({ saveUrl: url, tooltip: '{$msgClickToEdit}' });
		}
	},

	// end
	eoo: true
}

$(document).ready(function() { jsBackend.tags.init(); });