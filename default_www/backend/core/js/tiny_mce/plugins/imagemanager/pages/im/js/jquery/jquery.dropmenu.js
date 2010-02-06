/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($) {
	var idCount = 0;

	function DropMenu(s) {
		var t = this, k;

		t.items = [];

		t.settings = s = $.extend({
			menu_class : 'menu',
			menu_item_class : 'menuitem',
			item_class : 'item',
			separator_class : 'separator',
			submenu_item_class : 'submenu',
			active_class : 'active'
		}, s);

		t.id = s.id;

		if (s.setup)
			s.setup.call(t, t);

		// Private methods
		function hideHandler(e) {
			if (e.target.nodeName !== 'A' || !$(e.target).parents('div.' + s.menu_class).length)
				t.hide();
		};

		function clickHandler(e) {
			var m;

			if (e.target.nodeName === 'A') {
				m = t.find(e.target.rel);

				if (!m.show && !m.disabled) {
					if (m.onclick)
						m.onclick.call(m.scope || this, e, m);

					if (s.onClick)
						s.onClick(e, m);

					m = t;
					do {
						m.hide();
					} while (m = m.parentMenu);
				}

				e.preventDefault();
				return false;
			}
		};

		function mouseOverHandler(e) {
			var x, y, o, m, el;

			if (e.target.nodeName === 'A') {
				if (t.lastMenu)
					t.lastMenu.hide();

				if (m = t.find(e.target.rel)) {
					if (m.show) {
						el = $(e.target.parentNode);
						o = el.offset();
						x = o.left + el.width();
						y = o.top;
						el.addClass(m.settings.active_class);

						t.lastMenu = m;
						m.show(x, y);
					}
				}
			}
		};

		function createElm(n, a, h) {
			n = document.createElement(n);

			if (a)
				$(n).attr(a);

			if (h)
				$(n).html(h);

			return n;
		};

		function uniqueId() {
			return 'jquery_mc_' + idCount++;
		};

		// Public methods
		$.extend(this, {
			createMenu : function(s) {
				return new DropMenu(s);
			},

			find : function(n) {
				var i;

				for (i = 0; i < t.items.length; i++) {
					if (t.items[i].id === n)
						return t.items[i];
				}

				return null;
			},

			clear : function() {
				t.hide();
				t.items = [];
				$('#' + t.id).remove();
				t.rendered = 0;
			},

			render : function(n) {
				if (!n.id)
					n.id = t.id = uniqueId();
				else
					t.id = 'jquery_mc_' + n.id;

				if (s.onInit)
					s.onInit(t);
			},

			show : function(x, y) {
				var pe, it, m, s = t.settings;

				if (t.visible)
					t.hide();

				$(t).trigger('DropMenu:beforeshow', [t]);
				$().trigger('DropMenu:beforeshow', [t]);

				if (!t.rendered) {
					pe = createElm('div', {id : t.id, 'class' : s.menu_class});

					$.each(t.items, function(i, v) {
						var ti, id, cl = '', an;

						if (v.constructor == DropMenu) {
							ti = v.settings.title;
							cl = ' ' + s.submenu_item_class;

							if (v.settings['class'])
								cl += ' ' + v.settings['class'];
						} else {
							ti = v.title;

							if (this['class'])
								cl = ' ' + v['class'];
						}

						if (v.disabled || v.settings && v.settings.disabled)
							cl += ' disabled';

						// Add menu item
						it = createElm('div', {id : t.id + '_' + v.id, 'class' : s.menu_item_class + cl});
						an = createElm('a', {rel : v.id, href : '#'}, ti);
						//$(an).append(createElm('span', null, ti));
						$(it).append(an);
						$(pe).append(it);
					});

					$(document.body).append(pe);
					t.rendered = 1;
				}

				$().mouseup(hideHandler);

				m = $('#' + t.id);
				m.mouseover(mouseOverHandler).show().css({left : -5000, top : -5000});

				// Measure and align
				if (s.halign == 'right')
					x -= m.width();

				if (s.valign == 'bottom')
					y -= m.height();

				// Constrain
				if (s.constrain) {
					x = x < 0 ? 0 : x;
					y = y < 0 ? 0 : y;
					x = x + m.width() > $.winWidth() ? $.winWidth() - m.width() : x;
					y = y + m.height() > $.winHeight() ? $.winHeight() - m.height() : y;
				}

				m.css({left : x, top : y}).click(clickHandler);

				t.visible = 1;

				$(t).trigger('DropMenu:show');
				$().trigger('DropMenu:show', [t]);

				return false;
			},

			hide : function() {
				if (!t.visible)
					return false;

				$('a[rel=' + t.id + ']').parent().removeClass('active');
				$().unbind('mouseup', hideHandler);
				$('#' + t.id).unbind('mouseover', mouseOverHandler).hide();
				$('#' + t.id).unbind('click', clickHandler);

				t.visible = 0;

				$.each(t.items, function() {
					if (this.hide)
						this.hide();
				});

				$(t).trigger('DropMenu:hide');
				$().trigger('DropMenu:hide', [t]);

				return false;
			},

			add : function(o) {
				o.id = o.id || uniqueId();

				t.items.push(o);

				return o;
			},

			addSeparator : function() {
				return t.add({'class' : s.separator_class, title : 'separator'});
			},

			addMenu : function(o) {
				if (!o.onClick)
					o.onClick = s.onClick;

				o = new DropMenu(o);
				o.parentMenu = t;

				return t.add(o);
			}
		});
	};

	jQuery.mcDropMenu = DropMenu;

	jQuery.fn.mcContextMenu = function(s) {
		this.each(function() {
			var m = new $.mcDropMenu(s);

			m.render(this);

			$(this).bind('contextmenu', function(e) {
				return m.show(e.clientX, e.clientY);
			});
		});
	};
})(jQuery);
