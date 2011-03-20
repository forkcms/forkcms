/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($) {
	$.StatusWindow = function(f, a) {
		var t = this;

		t.isIE6 = /MSIE [56]/.test(navigator.userAgent);

		f = $.extend({
			type : 'status',
			width : 400,
			height : 240,
			onaction : function(a) {
				if (a == 'ok')
					t.close();
			}
		}, f);

		$.Window.call(t, f, a);
	};

	$.extend($.StatusWindow.prototype, $.Window.prototype, {
		setContent : function(co) {
			var v, k, h = '';

			if (typeof(co) != 'string') {
				// Build string
				$(co).each(function(i, v) {
					h += '<div class="statusrow">' +
							'<div class="statuscol1">' + v.title + '</div>' + 
							'<div class="statuscol2">' + v.content + '</div>' +
						'</div>';
				});
			}

			$('#' + this.id + '_statuscont').html(h || co);

			return this;
		},

		render : function() {
			var t = this, f, v;

			$.Window.prototype.render.call(t);
			$.Window.prototype.setContent.call(t,
				'<div id="' + t.id + '_statuscont" class="statustext"></div>' +
				'<a href="#" class="action ok">' + $.WindowManager.i18n.ok + '</a>'
			);

			f = t.features;

			if (v = f.content)
				t.setContent(v);

			return t;
		}
	});

	if (!$.CurrentWindowManager) {
		$.extend($.WindowManager, {
			status : function(f, cb) {
				var t = this, w;

				f = $.extend({
					onaction : function(a) {
						if (a == 'ok') {
							w.close();

							if (cb)
								cb.call(t, 1);
						} else if (a == 'close')
							w.close();
					}
				}, f);

				w = new $.StatusWindow(f).render();

				t.windows.push(w);

				return w.focus();
			},

			showProgress : function(f) {
				var t = this, bl, id = 'progressWindow';

				t.hideProgress();
				t.progress = 1;

				f = $.extend({
					theme : 'clearlooks2',
					x : -1,
					y : -1
				}, f);

				bl = $('#windowManProgressEventBlocker');
				if (!bl[0]) {
					$(document.body).append($.createElm('div', {id : 'windowManProgressEventBlocker', 'class' : f.theme + '_event_blocker'}).addClass(f.theme + '_visible_event_blocker'));
					bl = $('#windowManProgressEventBlocker');
				}

				bl.show().css('z-index', t.zIndex++).css({width : $.winWidth(), height : $.winHeight(), position : t.isIE6 ? 'absolute' : 'fixed'});

				$(document.body).appendAll(
					['div', {id : id, 'class' : f.theme + '_progress'},
						['div', {id : id + '_msg', 'class' : 'message'}]
					]
				);

				$('#' + id + '_msg').html(f.message);
				$('#' + id).css({left : -1000, top : -1000}).show();

				// Center window
				if (f.x == -1)
					f.x = Math.round(($.winWidth() / 2) - ($('#' + id).width() / 2));

				if (f.y == -1)
					f.y = Math.round(($.winHeight() / 2) - ($('#' + id).height() / 2));

				$('#' + id).css({left : f.x, top : f.y});

				$(t).bind('WindowManager:open', function() {t.hideProgress();});
			},

			hideProgress : function() {
				var t = this;

				if (t.progress) {
					$('#progressWindow').remove();
					$('#windowManProgressEventBlocker').hide();
					$(t).unbind('WindowManager:open', t.hideProgress);
					t.progress = 0;
				}
			}
		});
	}
})(jQuery);