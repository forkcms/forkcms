var PNG = {
	isOldIE : navigator.userAgent.indexOf('MSIE 5') > 0 || navigator.userAgent.indexOf('MSIE 6') > 0,
	transparentImg : 'img/transparent.gif',
	transparentBGImg : '../img/transparent.gif',

	fix : function(e) {
		var rs, cs, b, o;

		// Remove behavior to prevent memory leaks
		e.runtimeStyle.behavior = "none";

		// Check browser version
		if (!this.isOldIE)
			return;

		// Use old PNG src
		if (e.png_src)
			e.src = e.png_src;

		// Is PNG image
		if (e.src && e.src.toLowerCase().indexOf('.png') > 0) {
			e.png_src = e.src;
			e.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + e.src + "', sizingMethod='scale')";
			e.src = this.transparentImg;
		}

		cs = e.currentStyle;
		b = cs.backgroundImage;

		// Has PNG background
		if (b && b.toLowerCase().indexOf('png') > 0) {
			rs = e.runtimeStyle;
			b = b.replace(/url\(\"([^"]+)\"\)/g, "$1");
			o = rs["background-position"];

			if (!cs.hasLayout)
				rs.display = 'inline-block';

			rs.backgroundImage = 'url(\'' + this.transparentBGImg + '\')';
			rs.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + b + "', sizingMethod='crop')";
			rs["background-position"] = o;
		}

		// Remove referenced to prevent memory leaks
		rs = cs = b = o = e = null;
	}
};
