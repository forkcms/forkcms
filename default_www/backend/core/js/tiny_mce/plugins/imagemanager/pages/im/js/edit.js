(function($){
	window.EditDialog = {
		currentWin : $.WindowManager.find(window),

		init : function() {
			var t = this, args;

			args = t.args = $.extend({
				path : '{0}',
				visual_path : '/'
			}, t.currentWin.getArgs());

			if (t.currentWin.features) {
				t.currentWin.features.onbeforeclose = function() {
					// Temp file exists, ask to save
					if (t.imgPath != t.targetPath) {
						$.WindowManager.confirm($.translate('{#edit_image.confirm_no_save}'), function(s) {
							if (s)
								t.currentWin.close();
						});

						return false; // Block close
					}
				};
			}

			$(window).bind('resize', function(e) {
				t.resizeView();
			});

			t.imageSelection = $.createImageSelection($('#editImage'), {
				scroll_container : $('#imageWrapper'),
				delta_x : 26 + ($.browser.msie ? 2 : 0),
				delta_y : 90 + ($.browser.msie ? 2 : 0)
			});

			$(t.imageSelection).bind('imgselection:change', function(e, x, y, w, h) {
				var f = document.forms[0];

				if (this.mode == 'resize') {
					f.resize_w.value = w;
					f.resize_h.value = h;
				} else {
					f.crop_x.value = x;
					f.crop_y.value = y;
					f.crop_w.value = w;
					f.crop_h.value = h;
				}
			});

			t.loadImage({path : args.path, url : args.url, initial : 1});
			t.resizeView();

			$(['save', 'revert', 'crop', 'resize', 'flip', 'rotate']).each(function(i, v) {
				var a = $('#' + v);

				a.click(function() {
					if (!a.hasClass('disabled') && !a.hasClass('active')) {
						t[v]();

						$('div.panel').hide();
						$('#' + v + '_tools').show();

						a.addClass('active');
					}
				});
			});

			$('a.apply').click(function(e) {t.apply();});
			$('a.cancel').click(function(e) {t.cancel();});
		},

		apply : function() {
		},

		cancel : function() {
			$('div.panel').hide();
			$('#toolbar a').removeClass('active');
			this.imageSelection.setMode('none');

			if (this.imgUtils) {
				this.imgUtils.destroy();
				this.imgUtils = null;
			}

			$.WindowManager.hideProgress();
		},

		save : function() {
			var t = this, f = document.forms[0];

			f.save_filename.value = t.targetPath.substring(t.targetPath.lastIndexOf('/') + 1);

			t.apply = function() {
				// Get replace mode
				RPC.exec('im.getConfig', {path : t.imgPath}, function(data) {
					var config = data.result, f = document.forms[0];

					if (!RPC.handleError({message : 'Get config error', visual_path : t.args.visual_path, response : data})) {
						if (config['filesystem.clean_names'] == "true")
							$('#save_filename').val($.cleanName($('#save_filename').val()));

						$.WindowManager.showProgress({message : $.translate("{#edit_image.saving_wait}")});
						RPC.exec('im.saveImage', {path : t.imgPath, target : $('#save_filename').val()}, function(data) {
							var res;

							$.WindowManager.hideProgress();

							if (!RPC.handleError({message : 'Save error', visual_path : t.args.visual_path, response : data})) {
								res = RPC.toArray(data.result);

								if (t.args.onsave)
									return t.insertFile(res[0].file);

								if (res.length > 0)
									t.loadImage({path : res[0].file, initial : 1});

								$('#save,#revert').addClass('disabled');
								t.cancel();
							}
						});
					}
				});
			};
		},

		revert : function(e) {
			var t = this;

			$.WindowManager.confirm($.translate("{#edit_image.confirm_revert}"), function(s) {
				if (s) {
					$('#save,#revert').addClass('disabled');
					t.loadImage({path : t.targetPath});
				}
			});
		},

		resize : function() {
			var t = this, f = document.forms[0];

			t.cancel();
			t.imageSelection.setMode('resize');
			t.imageSelection.proportional = f.resize_prop.checked;

			$(f.resize_prop).click(function() {
				t.imageSelection.proportional = f.resize_prop.checked;
			});

			$('#resize_tools input[type=text]').change(function(e) {
				if (f.resize_prop.checked) {
					if (e.target.id == "resize_w")
						f.resize_h.value = Math.round(t.imageSelection.h * (parseInt(f.resize_w.value) / t.imageSelection.w));
					else
						f.resize_w.value = Math.round(t.imageSelection.w * (parseInt(f.resize_h.value) / t.imageSelection.h));
				}

				t.imageSelection.setRect(0, 0, parseInt(f.resize_w.value), parseInt(f.resize_h.value));
			});

			t.apply = function() {
				$.WindowManager.showProgress({message : $.translate('{#edit_image.please_wait}')});
				t.execRPC('im.resizeImage', {path : t.imgPath, width : f.resize_w.value, height : f.resize_h.value, temp : true}, '{#error.resize_failed}');
			};
		},

		crop : function() {
			var t = this, f = document.forms[0];

			t.cancel();
			t.imageSelection.setMode('crop');
			t.imageSelection.proportional = f.crop_prop.checked;

			$(f.crop_prop).click(function() {
				t.imageSelection.proportional = f.crop_prop.checked;
			});

			$('#crop_tools input[type=text]').change(function(e) {
				if (f.crop_prop.checked) {
					if (e.target.id == "crop_w")
						f.crop_h.value = Math.round(t.imageSelection.h * (parseInt(f.crop_w.value) / t.imageSelection.w));
					else
						f.crop_w.value = Math.round(t.imageSelection.w * (parseInt(f.crop_h.value) / t.imageSelection.h));
				}

				t.imageSelection.setRect(parseInt(f.crop_x.value), parseInt(f.crop_y.value), parseInt(f.crop_w.value), parseInt(f.crop_h.value));
			});

			t.apply = function() {
				$.WindowManager.showProgress({message : $.translate('{#edit_image.please_wait}')});
				t.execRPC('im.cropImage', {path : t.imgPath, left : f.crop_x.value, top : f.crop_y.value, width : f.crop_w.value, height : f.crop_h.value, temp : true}, '{#error.crop_failed}');
			};
		},

		flip : function() {
			var t = this, f = document.forms[0], axis;

			$('#flip_tools input').attr('checked', '');

			t.cancel();
			t.imageSelection.setMode('none');
			t.imgUtils = new $.ImageUtils($('#editImage'));
			$(t.imgUtils).bind('ImageUtils:load', function() {
				$('#flip_tools input').click(function() {
					t.imgUtils.flip(axis = $('#flip_tools input:checked').val());
				});

				t.apply = function() {
					$.WindowManager.showProgress({message : $.translate('{#edit_image.please_wait}')});

					if (axis)
						t.execRPC('im.flipImage', {path : t.imgPath, horizontal : axis == 'h', vertical : axis == 'v', temp : true}, '{#error.flip_failed}');
					else
						t.cancel();
				};
			});
			t.imgUtils.render();
		},

		rotate : function() {
			var t = this, f = document.forms[0], ang;

			$('#rotate_tools input').attr('checked', '');

			t.cancel();
			t.imageSelection.setMode('none');
			t.imgUtils = new $.ImageUtils($('#editImage'));
			$(t.imgUtils).bind('ImageUtils:load', function() {
				$('#rotate_tools input').click(function() {
					t.imgUtils.rotate(ang = parseInt($('#rotate_tools input:checked').val()));
				});

				t.apply = function() {
					$.WindowManager.showProgress({message : $.translate('{#edit_image.please_wait}')});

					if (ang)
						t.execRPC('im.rotateImage', {path : t.imgPath, angle : ang, temp : true}, '{#error.rotate_failed}');
					else
						t.cancel();
				};
			});
			t.imgUtils.render();
		},

		execRPC : function(m, a, er) {
			var t = this;

			RPC.exec(m, a, function(data) {
				var res = RPC.toArray(data.result);

				$.WindowManager.hideProgress();

				if (!RPC.handleError({message : er, response : data})) {
					$('#save,#revert').removeClass('disabled');
					t.loadImage({path : res[0].file});
				}
			});
		},

		insertFile : function(p) {
			var t = this, s = t.args;

			RPC.insertFile({
				relative_urls : s.relative_urls,
				document_base_url : s.document_base_url,
				default_base_url : s.default_base_url,
				no_host : s.remove_script_host || s.no_host,
				path : p,
				progress_message : $.translate("{#common.image_data}"),
				insert_filter : s.insert_filter,
				oninsert : function(o) {
					s.onsave(o);
					t.currentWin.close();
				}
			});
		},

		loadImage : function(o, cb) {
			var t = this;

			$('#crop,#resize,#flip,#rotate').addClass('disabled');
			$.WindowManager.showProgress({message : $.translate(o.initial ? "{#edit_image.loading}" : "{#edit_image.please_wait}")});
			RPC.exec('im.getMediaInfo', {path : o.path, url : o.url}, function (data) {
				var res = RPC.toArray(data.result);

				if (!RPC.handleError({message : 'Generic error', response : data})) {
					if (o.initial)
						t.imageURL = res[0].url;

					// Initial load
					if (o.initial)
						t.targetPath = res[0].path;

					t.imgPath = res[0].path;

					$('#editImage').load(function() {
						$.WindowManager.hideProgress();
						t.imageSelection.setImage($('#editImage'));
						$('#crop,#resize,#flip,#rotate').removeClass('disabled');
						t.cancel();
					});

					$('#editImage').error(function() {
						$.WindowManager.hideProgress();
					});

					$('#editImage').attr('src', '../../stream/index.php?cmd=im.streamFile&path=' + escape(res[0].path) + '&rnd=' + new Date().getTime());

					if (cb)
						cb(res);
				}
			});
		},

		resizeView : function() {
			$('#imageWrapper').css({'width' : (window.innerWidth || $.winWidth()) - 30, 'height' : (window.innerHeight || $.winHeight()) - 100});
		},

		isDemo : function() {
			if (this.args.is_demo) {
				$.WindowManager.info($.translate('{#error.demo}')); 
				return true;
			}
		}
	};

	$(function(e) {
		EditDialog.init();
	});
})(jQuery);
