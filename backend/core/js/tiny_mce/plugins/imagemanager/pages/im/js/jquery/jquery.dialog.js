/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($) {
	$.Window = function(f, a) {
		var t = this;

		t.features = f;
		t.args = a;
		t.isIE6 = /MSIE [56]/.test(navigator.userAgent);

		// Register actions
		t.clickActions = {
			min : t.minimize,
			max : t.maximize,
			med : t.medimize,
			close : t.close
		};

		t.mouseDownActions = {
			move : t.startDrag,
			'resize-n' : t.startDrag,
			'resize-nw' : t.startDrag,
			'resize-w' : t.startDrag,
			'resize-e' : t.startDrag,
			'resize-s' : t.startDrag,
			'resize-sw' : t.startDrag,
			'resize-se' : t.startDrag
		};
	};

	$.extend($.Window.prototype, {
		getArgs : function() {
			return this.args;
		},

		focus : function() {
			var t = this, id = t.id;

			if (!t.focused) {
				if (t.shim)
					t.shim.css('z-index', $.WindowManager.zIndex);

				$('#' + id).css('z-index', t.zIndex = $.WindowManager.zIndex++);
				$('#' + id + '_container').addClass('focus');
				$('#' + id + '_top').addClass('focustop');
				$('#' + id + '_middle').addClass('focusmiddle');
				$('#' + id + '_bottom').addClass('focusbottom');

				$($.WindowManager.windows).each(function() {
					if (this != t)
						this.blur();
				});

				t.focused = 1;
			}

			return this;
		},

		blur : function() {
			var t = this, id = t.id;

			if (t.focused) {
				$('#' + id + '_container').removeClass('focus');
				$('#' + id + '_top').removeClass('focustop');
				$('#' + id + '_middle').removeClass('focusmiddle');
				$('#' + id + '_bottom').removeClass('focusbottom');

				t.focused = 0;
			}

			return t;
		},

		setTitle : function(ti) {
			this.features.title = ti;
			$('#' + this.id + '_title').html(ti);

			return this;
		},

		setStatus : function(st) {
			$('#' + this.id + '_statustext').html(st);

			return this;
		},

		minimize : function() {
			return this;
		},

		medimize : function() {
			var t = this, sp = $.scrollPos(), r;

			if (r = t.lastRect) {
				$('#' + t.id + '_max').removeClass('med').addClass('max');
				t.moveTo(r.x, r.y);
				t.resizeTo(r.w, r.h);
				t.lastRect = 0;
			}

			return t;
		},

		maximize : function() {
			var t = this, sp = $.scrollPos();

			if (!t.lastRect) {
				t.lastRect = {x : t.x, y : t.y, w : t.width, h : t.height};

				$('#' + t.id + '_max').removeClass('max').addClass('med');
				t.moveTo(sp.x, sp.y);
				t.resizeTo($.winWidth(), $.winHeight(), 1);
			}

			return t;
		},

		close : function(ac) {
			var t = this, f;

			if (ac && (f = t.features.onbeforeclose)) {
				if (f.call(t) === false)
					return;
			}

			$('#' + t.id).remove();

			if ($.WindowManager.windows.length == 1)
				$('#windowManEventBlocker').remove();

			$.WindowManager.remove(t);

			if (t.shim) {
				t.shim.remove();
				t.shim = 0;
			}

			if (t.features.onclose)
				t.features.onclose(t);
		},

		moveTo : function(x, y) {
			var t = this, el = $('#' + t.id);

			if (t.x != x) {
				el.css({left : x});
				t.x = x;
			}

			if (t.y != y) {
				el.css({top : y});
				t.y = y;
			}

			if (t.shim)
				t.shim.css({left : x, top : y});

			return t;
		},

		moveBy : function(dx, dy) {
			return this.moveTo(this.x + parseInt(dx), this.y + parseInt(dy));
		},

		resizeTo : function(w, h, nr) {
			var t = this, b = t.borders, el = $('#' + t.id), ifr = $('#' + t.id + '_content, #' + t.id + '_ifr');

			w = parseInt(w);
			h = parseInt(h);

			if (!nr && t.lastRect) {
				$('#' + t.id + '_max').removeClass('med').addClass('max');
				t.lastRect = 0;
			}

			if (t.width != w) {
				ifr.css({width : w - b.w});
				el.css({width : w});
				t.width = w;
			}

			if (t.height != h) {
				ifr.css({height : h - b.h});
				el.css({height : h});
				$('#' + t.id + '_middle').css('height', h - b.h);
				t.height = h;
			}

			if (t.shim)
				t.shim.css({width : w, height : h});

			return t;
		},

		resizeBy : function(dw, dh) {
			return this.resizeTo(this.width + parseInt(dw), this.height + parseInt(dh));
		},

		show : function() {
			var t = this, id = t.id, bw, bh;

			$('#' + id).show();

			// Calculate borders
			bw = $('#' + id + '_middle div.left')[0].clientWidth;
			bw += $('#' + id + '_middle div.right')[0].clientWidth;
			bh = $('#' + id + '_top')[0].clientHeight;
			bh += $('#' + id + '_bottom')[0].clientHeight;

			t.borders = {w : bw, h : bh - 1};

			if (t.shim)
				t.shim.show();

			return t;
		},

		hide : function() {
			var t = this;

			$('#' + t.id).hide();
			
			if (t.shim)
				t.shim.hide();

			return t;
		},

		setContent : function(h) {
			$('#' + this.id + '_content').html(h);

			return this;
		},

		setURL : function(u) {
			var t = this, id = t.id, ifr = $('#' + id + '_ifr'), b = t.borders;

			if (!ifr[0])
				$('#' + id + '_content').html('').append($.createElm('iframe', {id : id + '_ifr', src : u, frameBorder : '0', scrolling : t.features.scrolling}).css({width : t.width - b.w, height : t.height - b.h}));
			else
				ifr.attr('src', u);

			return t;
		},

		startMove : function(a, e) {
			var t = this, bid = t.features.theme + '_move', mb, sx = e.screenX, sy = e.screenY, dx, dy, wx, wy;

			$(document.body).append($.createElm('div', {id : bid, 'class' : t.features.theme + '_event_blocker'}).css('z-index', $.WindowManager.zIndex));
			mb = $('#' + bid).css({width : $.winWidth(), height : $.winHeight(), position : t.isIE6 ? 'absolute' : 'fixed'});
			wx = t.x;
			wy = t.y;

			mb.mousemove(function(e) {
				dx = e.screenX - sx;
				dy = e.screenY - sy;
				t.moveTo(wx + dx, wy + dy);
			});

			mb.mouseup(function(e) {
				mb.remove();
			});
		},

		startDrag : function(a, e) {
			var t = this, bid = t.features.theme + '_move', mb, sx = e.screenX, sy = e.screenY, dx, dy, wx, wy, w, h;

			$(document.body).append($.createElm('div', {id : bid, 'class' : t.features.theme + '_event_blocker'}).css('z-index', $.WindowManager.zIndex));
			mb = $('#' + bid).css({width : $.winWidth(), height : $.winHeight(), position : t.isIE6 ? 'absolute' : 'fixed'});
			wx = t.x;
			wy = t.y;
			w = t.width;
			h = t.height;

			mb.mousemove(function(e) {
				dx = e.screenX - sx;
				dy = e.screenY - sy;
				var x = wx + dx, y = wy + dy;

				switch (a) {
					case 'resize-n':
						t.moveTo(wx, y);
						t.resizeTo(w, h - dy);
					break;

					case 'resize-nw':
						t.moveTo(x, y);
						t.resizeTo(w - dx, h - dy);
					break;

					case 'resize-ne':
						t.moveTo(wx, y);
						t.resizeTo(w + dx, h - dy);
					break;

					case 'resize-w':
						t.moveTo(x, wy);
						t.resizeTo(w - dx, h);
					break;

					case 'resize-e':
						t.resizeTo(w + dx, h);
					break;

					case 'resize-s':
						t.resizeTo(w, h + dy);
					break;

					case 'resize-sw':
						t.moveTo(wx + dx, wy);
						t.resizeTo(w - dx, h + dy);
					break;

					case 'resize-se':
						t.resizeTo(w + dx, h + dy);
					break;

					case 'move':
						t.moveTo(wx + dx, wy + dy);
					break;
				}

				e.preventDefault();
				return false;
			});

			mb.mouseup(function(e) {
				mb.remove();
			});

			e.preventDefault();
			return false;
		},

		render : function() {
			var t = this, id = 'win_' + $.WindowManager.count++, f = t.features, mc = '', bl, v;

			t.features = f = $.extend({
				theme : 'clearlooks2',
				modal : 1,
				type : 'window',
				x : -1,
				y : -1,
				width : 320,
				height : 240,
				title : ' '
			}, f);

			// Center window
			if (f.x == -1)
				f.x = Math.round(($.winWidth() / 2) - (f.width / 2));

			if (f.y == -1)
				f.y = Math.round(($.winHeight() / 2) - (f.height / 2));

			t.id = id;

			if (f.modal) {
				bl = $('#windowManEventBlocker');
				if (!bl[0]) {
					$(document.body).append($.createElm('div', {id : 'windowManEventBlocker', 'class' : f.theme + '_event_blocker'}).addClass(f.theme + '_visible_event_blocker'));
					bl = $('#windowManEventBlocker');
				}

				bl.show().css('z-index', $.WindowManager.zIndex++).css({width : $.winWidth(), height : $.winHeight(), position : t.isIE6 ? 'absolute' : 'fixed'});
			}

			if (t.isIE6 && !t.shim) {
				$(document.body).append('<iframe id="' + id + '_shim" src="javascript:\'\'" frameborder="0" scrolling="no" style="position:absolute;left:0;top:0;filter:Alpha(style=0,opacity=0);"></iframe>');
				t.shim = $('#' + id + '_shim');
			}

			$(document.body).appendAll(
				['div', {id : id, 'class' : f.theme + ' window'},
					['div', {id : id + '_container', 'class' : 'statusbar ' + f.type},
						['div', {id : id + '_top', 'class' : 'windowtop'},
							['div', {'class' : 'left'}],
							['div', {'class' : 'middle'}],
							['div', {'class' : 'right'}],
							['div', {id : id + '_title', 'class' : 'title'}, f.title],

							['a', {id : id + '_min', href : '', 'class' : 'action min'}],
							['a', {id : id + '_max', href : '', 'class' : 'action max'}],
							['a', {id : id + '_close', href : '', 'class' : 'action close'}],
							['a', {id : id + '_move', 'class' : 'action move', tabindex : '-1'}],
							['a', {id : id + '_resize_n', 'class' : 'action resize resize-n', tabindex : '-1'}],
							['a', {id : id + '_resize_nw', 'class' : 'action resize resize-nw', tabindex : '-1'}],
							['a', {id : id + '_resize_ne', 'class' : 'action resize resize-ne', tabindex : '-1'}]
						],

						['div', {id : id + '_middle', 'class' : 'windowmiddle'},
							['div', {'class' : 'left'}],
							['div', {id : id + '_content', 'class' : 'middle'}, ' '],
							['div', {'class' : 'right'}],
							['div', {id : id + '_bigicon', 'class' : 'bigicon'}],

							['a', {id : id + '_resize_w', 'class' : 'action resize resize-w', tabindex : '-1'}],
							['a', {id : id + '_resize_e', 'class' : 'action resize resize-e', tabindex : '-1'}]
						],

						['div', {id : id + '_bottom', 'class' : 'windowbottom' + (f.statusbar ? ' statusbarbottom' : '')},
							['div', {'class' : 'left'}],
							['div', {'class' : 'middle'}],
							['div', {'class' : 'right'}],
							['div', {id : id + '_statustext', 'class' : 'statustext'}, ' '],

							['a', {id : id + '_resize_s', 'class' : 'action resize resize-s', tabindex : '-1'}],
							['a', {id : id + '_resize_sw', 'class' : 'action resize resize-sw', tabindex : '-1'}],
							['a', {id : id + '_resize_se', 'class' : 'action resize resize-se', tabindex : '-1'}]
						]
					]
				]
			);

			if (f.bigicon)
				$('#' + id + '_bigicon').addClass(f.bigicon);

			if (f.chromeless)
				$('#' + id + '_container').addClass('chromeless');

			if (f.resizable)
				$('#' + id + '_container').addClass('resizable');

			if (f.statusbar) {
				$('#' + id + '_container').addClass('statusbar');
				$('#' + id + '_bottom').addClass('statusbarbottom');
			}

			t.show().hide().resizeTo(f.width, f.height).moveTo(f.x, f.y).show();

			if (v = f.title)
				t.setTitle(v);

			if (v = f.content)
				t.setContent(v);

			if (v = f.url)
				t.setURL(v);

			function handleAction(e) {
				var el = $(e.target), action, f;

				t.focus();

				if (el[0].nodeName == 'A' && el.hasClass('action')) {
					action = $.grep(el[0].className.split(/\s+/), function(v) {return !/^action|resize$/.test(v);}).join(' ');

					if (e.type == 'mousedown')
						f = t.mouseDownActions[action];
					else {
						if (t.features.onaction) {
							t.features.onaction.call(t, action);
							return false;
						}

						f = t.clickActions[action];
					}

					if (f) {
						f.call(t, action, e);
						return false;
					}
				}
			};

			$('#' + id).click(handleAction).mousedown(handleAction);

			$($.WindowManager).trigger('WindowManager:open', [t]);

			return t;
		}
	});

	$.WindowManager = {
		zIndex : 100010,
		count : 0,
		windows : [],
		i18n : {
			yes : 'Yes',
			no : 'No',
			ok : 'Ok'
		},

		setup : function() {
			var w = window, lw, nwm, op, b;

			// Find root window manager
			while ((w = w.parent || w.opener) && w != lw) {
				if (w.$ && w.$.WindowManager)
					nwm = w.$.WindowManager;

				lw = w;
			}

			// Found parent window manager use that one and set the title for the dialog
			if (nwm && nwm != this) {
				$.CurrentWindowManager = $.WindowManager;
				b = document.location.pathname.replace(/[^\/]+$/, '');
				op = nwm.open;

				$.WindowManager = $.extend({}, nwm, {
					open : function(f, a) {
						// Is relative URL force it absolute
						if (!/^https?\:|\//.test(f.url))
							f.url = b + f.url;

						return op.call(nwm, f, a);
					}
				});

				nwm.find(window).setTitle(document.title);
			} else {
				$(window).bind('resize', function() {
					$($.WindowManager.windows).each(function(i, w) {
						if (w.lastRect)
							w.resizeTo($.winWidth(), $.winHeight(), 1);
					});
				});
			}

			// Find current window
			$.WindowManager.currentWindow = $.WindowManager.find(window);
		},

		find : function(tw) {
			var i, ifr, w;

			for (i = 0; i < this.windows.length; i++) {
				w = this.windows[i];
				ifr = $('#' + w.id + '_ifr')[0];

				if (ifr && ifr.contentWindow == tw)
					return w;
			}

			return this.defaultWin;
		},

		open : function(f, a) {
			var w = new $.Window(f, a).render();

			this.windows.push(w);

			return w.focus();
		},

		warn : function(msg, cb) {
			return this.modalBox(msg, {
				bigicon : 'warning'
			}, cb);
		},

		error : function(msg, cb) {
			return this.modalBox(msg, {
				bigicon : 'error'
			}, cb);
		},

		info : function(msg, cb) {
			return this.modalBox(msg, {
				bigicon : 'info'
			}, cb);
		},

		confirm : function(msg, cb) {
			return this.modalBox(msg, {
				type : 'confirm',
				bigicon : 'ask',
				onaction : function(a) {
					var t = this;

					if (a == 'ok') {
						t.close();

						if (cb)
							cb.call(t, 1);
					} else if (a == 'cancel' || a == 'close') {
						t.close();

						if (cb)
							cb.call(t, 0);
					}
				}
			}, cb).setContent(
				'<div class="message">' + msg +'</div>' +
				'<a href="" class="action ok">' + this.i18n.yes + '</a>' +
				'<a href="#" class="action cancel">' + this.i18n.no + '</a>'
			);
		},

		modalBox : function(msg, f, cb) {
			f = $.extend({
				type : 'alert',
				bigicon : 'error',
				width : 400,
				height : 130,
				onaction : function(a) {
					if (a == 'ok') {
						this.close();

						if (cb)
							cb.call(this, 1);
					} else if (a == 'close')
						this.close();
				}
			}, f);

			return this.open(f).setContent(
				'<div class="message">' + (msg || '') +'</div>' +
				'<a href="" class="action ok">' + this.i18n.ok + '</a>'
			);
		},

		remove : function(w) {
			var t = this, fr, z = 0, nl = [];

			// Find front most window
			$(t.windows).each(function(i, cw) {
				if (w == cw)
					return;

				if (cw.zIndex > z) {
					z = cw.zIndex;
					fr = cw;
				}

				nl.push(cw);
			});

			t.windows = nl;

			if (fr)
				fr.focus();
		}
	};

	$.WindowManager.setup();
})(jQuery);