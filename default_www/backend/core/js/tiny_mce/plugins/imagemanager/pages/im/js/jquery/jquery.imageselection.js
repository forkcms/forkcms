/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($) {
	var count = 0;

	function ImageSelection(ta, s) {
		var t = this, id;

		s = t.settings = $.extend({
		}, s);

		t.id = id = 'imageSelection' + (count++);
		t.mode = s.mode;

		$(ta).after(
			'<div id="' + id + '_container" style="position:relative">' +
				'<div id="' + id + '_view">' +
					'<div id="' + id + '_sel" class="selection"></div>' + 
				'</div>' + 

				'<span id="' + id + '_tl" class="selection-corner selection-corner-tl"></span>' + 
				'<span id="' + id + '_tc" class="selection-corner selection-corner-tc"></span>' + 
				'<span id="' + id + '_tr" class="selection-corner selection-corner-tr"></span>' + 
				'<span id="' + id + '_cl" class="selection-corner selection-corner-cl"></span>' + 
				'<span id="' + id + '_cr" class="selection-corner selection-corner-cr"></span>' + 
				'<span id="' + id + '_bl" class="selection-corner selection-corner-bl"></span>' + 
				'<span id="' + id + '_bc" class="selection-corner selection-corner-bc"></span>' + 
				'<span id="' + id + '_br" class="selection-corner selection-corner-br"></span>' + 
			'</div>'
		);

		t.scrollContainer = $(s.scroll_container);
		t.container = $('#' + id + '_container');
		t.selection = $('#' + id + '_sel');
		t.cornerTL = $('#' + id + '_tl');
		t.cornerTC = $('#' + id + '_tc');
		t.cornerTR = $('#' + id + '_tr');
		t.cornerCL = $('#' + id + '_cl');
		t.cornerCR = $('#' + id + '_cr');
		t.cornerBL = $('#' + id + '_bl');
		t.cornerBC = $('#' + id + '_bc');
		t.cornerBR = $('#' + id + '_br');

		// Add images
		t.offset = t.container.offset();
		t.setImage(ta);

		t.container.mousedown(function(e) {
			var el = e.target;

			if (t.mode == 'none')
				return;

			if (el.id == id + '_mainImg')
				return t.drag(e, 'sel');

			if (el.id == id + '_selectionImg')
				return t.drag(e, 'move');

			if (el.nodeName == 'SPAN')
				return t.drag(e, el.className.replace(/selection\-corner(-|\s+)/g, ''));
		});
	};

	$.extend(ImageSelection.prototype, {
		getX : function(e) {
			return (e.clientX - this.settings.delta_x) + this.scrollContainer[0].scrollLeft;
		},

		getY : function(e) {
			return (e.clientY - this.settings.delta_y) + this.scrollContainer[0].scrollTop;
		},

		setMode : function(m) {
			var t = this;

			if (t.mode != m) {
				t.container.removeClass(t.mode);
				t.mode = m;
				t.container.addClass(m);

				if (m == 'none') {
					t.reset();
					t.targetImg.show();
					t.container.hide();
				} else {
					t.targetImg.hide();
					t.container.show();
				}

				if (m == 'resize') {
					t.cornersVisible = 1;
					t.setRect(0, 0, t.maxW, t.maxH).show();
				} else {
					t.cornersVisible = 0;
					t.setRect(0, 0, 0, 0);
					t.hide();
				}
			}

			return t;
		},

		setBounderyRect : function(x, y, w, h) {
			var t = this;

			t.minX = x;
			t.minY = y;
			t.maxW = w;
			t.maxH = h;
		},

		setImage : function(ta) {
			var t = this;

			ta = $(ta);

			if (t.mode != 'none')
				ta.hide();

			t.container.find('img').remove();
			t.container.append($(ta[0].cloneNode(true)).attr('id', t.id + '_mainImg').addClass('mainimage'));
			t.mainImage = $('#' + t.id + '_mainImg').show();
			t.selection.append($(ta[0].cloneNode(true)).attr('id', t.id + '_selectionImg').addClass('selimage'));
			t.selectionImg = t.selection.find('img').show();
			t.targetImg = ta;

			t.setBounderyRect(0, 0, ta.width(), ta.height());

			return t;
		},

		setRect : function(x, y, w, h, ns) {
			var t = this, s = t.settings;

			// Flip rect horizontal
			if (w < 0) {
				w = w * -1;
				x -= w;

				if (x < 0)
					w += x;
			}

			// Flip rect vertical
			if (h < 0) {
				h = h * -1;
				y -= h;

				if (y < 0)
					h += y;
			}

			// Boundery check
			x = x < 0 ? 0 : x;
			y = y < 0 ? 0 : y;

			if (t.mode == 'crop') {
				w = w > t.maxW - x ? t.maxW - x : w;
				h = h > t.maxH - y ? t.maxH - y : h;
			}

			if (t.x != x) {
				t.selection.css('left', t.x = x);

				if (t.selectionImg)
					t.selectionImg.css('left', 0 - x - 1);
			}

			if (t.y != y) {
				t.selection.css('top', t.y = y);

				if (t.selectionImg)
					t.selectionImg.css('top', 0 - y - 1);
			}

			if (t.w != w)
				t.selection.css('width', 0).css('width', (t.w = w) - 2);

			if (t.h != h)
				t.selection.css('height', 0).css('height', (t.h = h) - 2);

			if (t.mode == 'resize') {
				t.selectionImg.css({left : 0, top : 0, width : t.w, height : t.h});

				if (!ns)
					t.mainImage.css({width : t.w, height : t.h});
			}

			$(t).trigger('imgselection:change', [x, y, w, h]);

			if (!ns)
				t.cornersVisible = 1;

			t.drawCorners().show();

			return this;
		},

		show : function() {
			var t = this;

			if (!t.visible) {
				t.selection.show();

				if (t.cornersVisible)
					t.container.find('span').show();
		
				t.visible = 1;
			}

			return t;
		},

		hide : function() {
			var t = this;

			if (t.visible) {
				t.selection.hide();
				t.container.find('span').hide();
				t.visible = 0;
			}

			return t;
		},

		reset : function() {
			var t = this, w = t.targetImg.width(), h = t.targetImg.height();

			t.mainImage.css({width : w, height : h});
			t.selectionImg.css({width : w, height : h});
			t.setRect(0, 0, w, h);
			t.setBounderyRect(0, 0, w, h);

			return t;
		},

		drawCorners : function() {
			var t = this;

			if (t.cornersVisible) {
				t.cornerTL.css({left : t.x - 4, top : t.y - 4}).show();
				t.cornerTC.css({left : t.x + Math.round((t.w - 8) / 2), top : t.y - 4}).show();
				t.cornerTR.css({left : t.x + t.w - 3, top : t.y - 4}).show();

				t.cornerCL.css({left : t.x - 4, top : t.y + Math.round((t.h - 8) / 2)}).show();
				t.cornerCR.css({left : t.x + t.w - 3, top : t.y + Math.round((t.h - 8) / 2)}).show();

				t.cornerBL.css({left : t.x - 4, top : t.y + t.h - 3}).show();
				t.cornerBC.css({left : t.x + Math.round((t.w - 8) / 2), top : t.y + t.h - 3}).show();
				t.cornerBR.css({left : t.x + t.w - 3, top : t.y + t.h - 3}).show();
			}

			return t;
		},

		startDrag : function(e, o) {
			var t = this, sx = t.getX(e), sy = t.getY(e);

			if (o.start)
				o.start.call(t, sx, sy);

			function drag(e) {
				if (o.drag)
					o.drag.call(t, t.getX(e), t.getY(e));

				e.preventDefault();
				return false;
			};

			function up() {
				if (o.end)
					o.end.call(t, t.getX(e), t.getY(e));

				$().unbind('mouseup', up);
				$().unbind('mousemove', drag);

				e.preventDefault();
				return false;
			};

			$().mousemove(drag);
			$().mouseup(up);

			e.preventDefault();
			return false;
		},

		drag : function(e, ty) {
			var t = this, sx, sy, rx, ry, rw, rh, mw, mh, a;

			t.startDrag(e, {
				start : function(x, y) {
					sx = x;
					sy = y;
					rx = t.x;
					ry = t.y;
					rw = t.w;
					rh = t.h;
					mw = t.maxW;
					mh = t.maxH;
					a = (rw / rh) || 0;

					if (rw == 0 && rh == 0)
						t.hide();

					t.cornersVisible = 0;
					t.container.find('span').hide();
					t.selection.addClass('selection-corner-' + ty);
				},

				drag : function(cx, cy) {
					var x = rx, y = ry, w = rw, h = rh, dx, dy, p = t.proportional || e.shiftKey;

					dx = cx - sx;
					dy = cy - sy;

					// Calc rect
					switch (ty) {
						case 'sel':
							dx = p ? Math.round(dy * a) : dx;
							w = dx;
							h = dy;
							x = sx;
							y = sy - 1; // Fix quirk
							break;

						case 'tl':
							dx = p ? Math.round(dy * a) : dx;
							x = rx + dx;
							y = ry + dy;
							w = rw - dx;
							h = rh - dy;
							break;

						case 'tc':
							y = ry + dy;
							h = rh - dy;
							break;

						case 'tr':
							dx = p ? Math.round(-dy * a) : dx;
							y = ry + dy;
							w = rw + dx;
							h = rh - dy;
							break;

						case 'cl':
							x = rx + dx;
							w = rw - dx;
							break;

						case 'cr':
							w = rw + dx;
							break;

						case 'bl':
							dx = p ? Math.round(-dy * a) : dx;
							x = rx + dx;
							w = rw - dx;
							h = rh + dy;
							break;

						case 'bc':
							h = rh + dy;
							break;

						case 'br':
							dx = p ? Math.round(dy * a) : dx;
							w = rw + dx;
							h = rh + dy;

							break;

						case 'move':
							x = rx + dx;
							y = ry + dy;
							x = x + rw > mw ? mw - rw : x;
							y = y + rh > mh ? mh - rh : y;

							break;
					}

					if (ty != 'move') {
						w = x < 0 ? w + x : w;
						h = y < 0 ? h + y : h;
					}

					t.setRect(x, y, w, h, 1);
				},

				end : function() {
					t.cornersVisible = 1;

					if (t.mode == 'resize') {
						t.setRect(0, 0, t.w, t.h);
						t.setBounderyRect(0, 0, t.w, t.h);
					}

					t.drawCorners();
					t.selection.removeClass('selection-corner-' + ty);
				}
			});
		}
	});

	$.createImageSelection = function(ta, s) {
		return new ImageSelection(ta, s);
	};
})(jQuery);