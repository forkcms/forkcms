(function($){
	window.ViewDialog = {
		currentWin : $.WindowManager.find(window),

		init : function() {
			var t = this, args;

			t.args = args = $.extend({
				path : '{default}',
				visual_path : '/'
			}, t.currentWin.getArgs()); 

			// Compile templates
			t.singeViewTpl = $.templateFromScript('#singleview_template');
			t.mpgTpl = $.templateFromScript('#mpg_template');
			t.rmTpl = $.templateFromScript('#rm_template');
			t.movTpl = $.templateFromScript('#mov_template');
			t.dcrTpl = $.templateFromScript('#dcr_template');
			t.footerFullTpl = $.templateFromScript('#single_footer_full');
			t.footerNoEditTpl = $.templateFromScript('#single_footer_no_edit');
			t.footerSimpleTpl = $.templateFromScript('#single_footer_simple');

			$('#prev').click(function(e) {
				if (!$(e.target).hasClass('disabled'))
					t.getMediaInfo(t.prevMedia);
			});

			$('#next').click(function(e) {
				if (!$(e.target).hasClass('disabled'))
					t.getMediaInfo(t.nextMedia);
			});

			$('#gallery').click(function(e) {
				if (!$(e.target).hasClass('disabled'))
					t.currentWin.close();
			});

			$('#singlefooter').click(function(e) {
				var el = e.target, a = el.nodeName == 'A' ? el : $(el).parents('a')[0];

				if (a) {
					a = $(a);

					if (a.attr('id') == 'deleteit' && !a.hasClass('disabled'))
						t.deleteFile(t.path);

					if (a.attr('id') == 'edit' && !a.hasClass('disabled'))
						t.editFile(t.path);
				}
			});

			$().keydown(function(e) {
				switch (e.keyCode) {
					case 32: // space
					case 110: // n
					case 34: // page down
					case 39: // right arrow
					case 40: // down arrow
						t.getMediaInfo(t.nextMedia);
					break;

					case 102: // p
					case 33: // page up
					case 37: // left arrow
					case 38: // up arrow
						t.getMediaInfo(t.prevMedia);
					break;

					case 27: // Esc
						t.currentWin.close();
					break;
				}
			});

			$(window).bind('resize', function() {
				t.resizeView();
			});

			$('#singlecontent').click(function(e) {
				if (e.target.nodeName == 'IMG' && $(e.target).hasClass('viewimage')) {
					if (args.onselect) {
						RPC.insertFile({
							relative_urls : args.relative_urls,
							document_base_url : s.document_base_url,
							default_base_url : s.default_base_url,
							no_host : args.remove_script_host || args.no_host,
							path : t.path,
							progress_message : $.translate("{#message.insert}"),
							insert_filter : args.insert_filter,
							oninsert : function(o) {
								args.onselect(o);
								t.currentWin.close();
							}
						});
					} else
						t.currentWin.close();
				}
			});

			t.getMediaInfo(args.path);

			focus();
		},

		getMediaInfo : function(p) {
			var t = this;

			t.path = p;

			RPC.exec('im.getMediaInfo', {path : p}, function (data) {
				var res, row, tpl, footTpl;

				res = RPC.toArray(data.result);
				row = res[0];
				t.nextMedia = row.next;
				t.prevMedia = row.prev; 

				if (!row.next)
					$('#next').addClass('disabled');
				else
					$('#next').removeClass('disabled');

				if (!row.prev)
					$('#prev').addClass('disabled');
				else
					$('#prev').removeClass('disabled');

				footTpl = t.footerSimpleTpl;
 
				switch (row.type) {
					case "jpg":
					case "jpeg":
					case "gif":
					case "png":
					case "bmp":
						tpl = t.singeViewTpl;
						footTpl = t.footerFullTpl;
						break;

					case "mpg":
					case "mpeg":
					case "wma":
					case "wmv":
					case "asf":
					case "avi":
						tpl = t.mpgTpl;
						break;

					case "qt":
					case "mov":
						tpl = t.movTpl;
						break;

					case "rm":
					case "ram":
						tpl = t.rmTpl;
						break;

					case "dcr":
						tpl = t.dcrTpl;
						break;

					case "swf":
						$('#singlecontent').html('<div id="flash"></div>');

						swfobject.embedSWF("../../stream/index.php?cmd=im.streamFile&path=" + escape(row.path), "flash", row.width, row.height, "7", null, {
						}, {
							quality : "high",
							wmode : "transparent",
							scale : "showall"
						});

						$('#singlefooter').html(t.footerNoEditTpl, row);

						return;

					case "flv":
						$('#singlecontent').html('<div id="flash"></div>');

						swfobject.embedSWF("flvplayer/flvPlayer.swf", "flash", row.width, row.height, "8", null, {
							flvToPlay : escape("../../../stream/index.php?cmd=im.streamFile&path=" + escape(row.path)),
							hiddenGui : "false",
							showScaleModes : "true",
							autoStart : "false",
							allowFullScreen : "true"
						}, {
							quality : "high",
							wmode : "transparent",
							scale : "showall"
						});

						$('#singlefooter').html(t.footerNoEditTpl, row);

						return;
				}

				$('#singlecontent').html(tpl, row);
				$('#singlefooter').html(footTpl, row);
	
				if (row.custom) {
					if (!row.custom.editable)
						$('#singleview .editsingle a.edt').addClass('disabled');
					else
						$('#singleview .editsingle a.edt').removeClass('disabled');
				}

				t.resizeView();
			});
		},

		resizeView : function() {
			$('#singleimg').css({'width' : $.winWidth() - 60, 'height' : $.winHeight() - 100});
		},

		deleteFile : function(p) {
			var t = this;

			$.WindowManager.confirm($.translate('{#view.confirm_delete}'), function(s) {
				if (s) {
					if (!t.isDemo()) {
						RPC.exec('im.deleteFiles', {path0 : p}, function (data) {
							var args;

							if (!RPC.handleError({message : '{#error.delete_failed}', visual_path : t.args.visual_path, response : data})) {
								if (t.args.ondelete)
									t.args.ondelete(p);

								if (t.nextMedia)
									t.getMediaInfo(t.nextMedia);
								else if (t.prevMedia)
									t.getMediaInfo(t.prevMedia);
								else
									t.currentWin.close();
							}
						});
					}
				}
			});
		},

		editFile : function(p) {
			var t = this;

			$.WindowManager.open({
				url : 'edit.html'
			}, {
				is_demo : t.args.is_demo,
				path : p,
				visual_path : this.visualPath,
				onsave : function() {
					t.getMediaInfo(t.path);
				}
			}).maximize();
		},

		isDemo : function() {
			if (this.args.is_demo) {
				$.WindowManager.info($.translate('{#error.demo}')); 
				return true;
			}
		}
	};

	$(function(e) {
		ViewDialog.init();
	});
})(jQuery);
