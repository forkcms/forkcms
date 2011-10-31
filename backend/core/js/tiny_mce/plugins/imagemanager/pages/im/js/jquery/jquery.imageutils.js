/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($) {
	$.ImageUtils = function(ta) {
		var t = this, d = document, ss;

		ta = $(ta);
		t.target = ta;

		if ($.browser.msie) {
			// Add VML namespace and stylesheets on IE
			d.namespaces.add("v", "urn:schemas-microsoft-com:vml");

			ss = d.createStyleSheet();
			ss.cssText = "v\\:*{behavior:url(#default#VML);display:inline-block;margin:0;padding:0}";
		} else {
			ta.after('<canvas id="editImageCanvas" style="display:none"></canvas>');
			t.canvas = document.getElementById('editImageCanvas');
			t.context = t.canvas.getContext('2d'); 
		}
	};

	$.extend($.ImageUtils.prototype, {
		render : function() {
			var t = this;

			t.img = new Image();

			$(t.img).load(function() {
				if (t.canvas) {
					t.canvas.width = t.img.width;
					t.canvas.height = t.img.height;
					$(t.canvas).css({width : t.img.width, height : t.img.height}).show();
					t.context.drawImage(t.img, 0, 0);
				} else
					t.target.after('<v:image id="editImageVML" src="' + t.img.src + '" style="width:' + t.img.width + 'px;height:' + t.img.height + 'px"></v:image>');

				t.target.hide();
				$(t).trigger('ImageUtils:load');
			});

			t.img.src = t.target.attr('src');
		},

		flip : function(d) {
			var t = this, ctx = t.context;

			if (!t.canvas) {
				$('#editImageVML').css('flip', d == 'h' ? 'x' : 'y');
				return;
			}

			if (d == 'h') {
				ctx.save();
				ctx.clearRect(0, 0, t.img.width, t.img.height);
				ctx.scale(-1, 1);
				ctx.drawImage(t.img, -t.img.width, 0);
				ctx.restore();
			} else {
				ctx.save();
				ctx.clearRect(0, 0, t.img.width, t.img.height);
				ctx.scale(1, -1);
				ctx.drawImage(t.img, 0, -t.img.height);
				ctx.restore();
			}
		},

		rotate : function(a) {
			var t = this, img = t.img, can = t.canvas, ctx = t.context, rad = a * Math.PI / 180;

			if (!t.canvas) {
				$('#editImageVML').attr('rotation', a);
				return;
			}

			ctx.save();
			ctx.clearRect(0, 0, t.img.width, t.img.height);

			switch(a) {
				case 90:
					can.width = img.height;
					can.height = img.width;
					ctx.rotate(rad);
					ctx.drawImage(img, 0, -img.height);
					$(t.canvas).css({width : img.height, height : img.width});
					break;

				case 180:
					can.width = img.width;
					can.height = img.height;
					ctx.rotate(rad);
					ctx.drawImage(img, -img.width, -img.height);
					break;

				case 270:
					can.width = img.height;
					can.height = img.width;
					ctx.rotate(rad);
					ctx.drawImage(img, -img.width, 0);
					break;
			}

			ctx.restore();
			$(t.canvas).css({width : can.width, height : can.height});
		},

		destroy : function() {
			var t = this;

			if (t.canvas)
				$(t.canvas).remove();
			else
				$('#editImageVML').remove();

			t.target.show();
		}
	});
})(jQuery);