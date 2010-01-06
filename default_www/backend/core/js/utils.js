if(!utils) { var utils = new Object(); }

utils = {
	// datamembers
	debug: true,
	eof: true
}	

utils.string = {
	trim: function(value) {
		return value.replace(/^\s+/,'').replace(/\s+$/,'');
	},
	// end
	eof: true
}