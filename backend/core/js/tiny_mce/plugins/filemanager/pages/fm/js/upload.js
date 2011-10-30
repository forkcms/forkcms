(function($){
	window.UploadDialog = {
		currentWin : $.WindowManager.find(window),

		init : function() {
			var t = this, args;

			t.args = args = $.extend({
				path : '{default}',
				visual_path : '/'
			}, t.currentWin.getArgs());

			t.fileListTpl = $.templateFromScript('#filelist_item_template');

			$('.uploadtype').html($.translate('{#upload.basic_upload}', 0, {a : '<a id="singleupload" href="#basic">', '/a' : '</a>'}));
			$('#createin').html(args.visual_path);
			$('form input[name=path]').val(args.path);
			$('form input[name=file0]').change(function(e) {
				$('form input[name=name0]').val(t.cleanName(/([^\/\\]+)$/.exec(e.target.value)[0].replace(/\.[^\.]+$/, '')));
			});

			$('form').submit(function() {
				$.WindowManager.showProgress({message : $.translate('{#upload.progress}')}); 
			});

			if (document.location.hostname != document.domain)
				$('form input[name=domain]').val(document.domain);

			t.path = args.path;

			$('#singleupload').click(function(e) {
				$('#multiupload_view').hide();
				$('#singleupload_view').show();
			});

			RPC.exec('fm.getConfig', {path : args.path}, function(data) {
				var config = data.result, maxSize, upExt, fsExt, outExt = [], i, x, found;

				maxSize = config['upload.maxsize'];
				fsExt = config['filesystem.extensions'].split(',');
				upExt = config['upload.extensions'].split(',');
				t.debug = config['general.debug'] == "true";
				t.shouldCleanNames = config['filesystem.clean_names'] == "true";
				t.chunkSize = config['upload.chunk_size'] || '1mb';

				$('#content').show();

				if ($.multiUpload.initialized)
					$('#multiupload_view').show();
				else
					$('#singleupload_view').show();

				// Disabled upload
				if (config['upload.multiple_upload'] != "true") {
					$('#multiupload_view').hide();
					$('#singleupload_view').show();
				}

				maxSize = maxSize.replace(/\s+/, '');
				maxSize = maxSize.replace(/([0-9]+)/g, '$1 ');

				if (upExt[0] == '*')
					upExt = fsExt;

				if (fsExt[0] == '*')
					fsExt = upExt;

				for (i = 0; i < upExt.length; i++) {
					upExt[i] = $.trim(upExt[i].toLowerCase());
					found = false;

					for (x = 0; x < fsExt.length; x++) {
						fsExt[x] = $.trim(fsExt[x]).toLowerCase();

						if (upExt[i] == fsExt[x]) {
							found = true;
							break;
						}
					}

					if (found)
						outExt.push(upExt[i]);
				}

				t.validExtensions = outExt;
				t.maxSize = maxSize;

				$('#facts').html($.templateFromScript('#facts_template'), {extensions : outExt.join(', '), maxsize : maxSize, path : args.visual_path});

				if (config['upload.multiple_upload'] == "true")
					t.initMultiUpload();
			});

			$('#cancel').click(function() {t.currentWin.close();});
		},

		cleanName : function(s) {
			if (this.shouldCleanNames)
				s = $.cleanName(s);

			return s;
		},

		handleSingleUploadResponse : function(data) {
			var t = this, args = t.currentWin.getArgs();

			$.WindowManager.hideProgress();

			if (!RPC.handleError({message : '{#error.upload_failed}', visual_path : t.args.visual_path, response : data})) {
				var res = RPC.toArray(data.result);

				$.WindowManager.info($.translate('{#message.upload_ok}'));
				$('#file0, #name0').val('');

				t.insertFiles([res[0].file]);
			}
		},

		initMultiUpload : function() {
			var t = this, up, args = t.currentWin.getArgs(), initial = 1, startTime;

			up = $.multiUpload.create({
				silverlight_xap_url : '../../stream/index.php?theme=fm&package=static_files&file=multiupload_xap',
				upload_url : '../../stream/index.php?cmd=fm.upload',
				path : t.path,
				filter : t.validExtensions,
				chunk_size : t.chunkSize,
				max_size : t.maxSize,
				flash_browse_button : '#add',
				oninit : function() {
					$('#add').removeClass('hidden');
				}
			});

			if (t.debug)
				alert('Runtime used: ' + $.multiUpload.runtime);

			function calc(up) {
				var size = 0, uploaded = 0, loaded = 0, unloaded = 0, bps = 0, finished = true, fl = [];

				if (!up.files.length) {
					$('#selectview').css('top', 0);
					$('#selectview').show();
					$('#fileblock').css({position : 'relative', top : 400});
					initial = 1;
					return;
				}

				$(up.files).each(function(i, f) {
					size += f.size;
					loaded += f.loaded;

					if (f.status == 'completed')
						uploaded++;

					if (!f.status)
						finished = false;
				});

				bps = Math.ceil(loaded / ((new Date().getTime() - startTime || 1) / 1000.0));

				if (finished) {
					$('#abortupload').hide();

					$(up.files).each(function(i, f) {
						if (f.status == 'completed')
							fl.push(t.path + '/' + f.name);
					});

					$('#progressbar').css('width', '100%');

					t.insertFiles(fl, function() {
						// All files uploaded 100% ok
						if (up.files.length == uploaded)
							t.currentWin.close();
					});

					return;
				}

				$('#progressinfo').html($.translate('{#upload.progressinfo}', 1, {loaded : up.formatSize(loaded), total : up.formatSize(size), speed : up.formatSize(bps)}));
				$('#progressbar').css('width', Math.round(loaded / size * 100.0) + '%');

				$('#stats').html($.translate('{#upload.statusrow}', 1, {files : up.files.length, size : up.formatSize(size)}));
			};

			// Register event listeners

			$(up).bind('multiUpload:filesSelected', function(e, fs) {
				var up = this, totalSize = 0;

				if (!fs.files.length) {
					$.WindowManager.info($.translate('{#upload.no_valid_files}'));
					return;
				}

				if (initial) {
					$('#selectview').animate({
						top: '-150px'
					}, 1000);

					$('#fileblock').animate({
						top:'-60px'
					}, 1000, 'linear', function() {
						$('#fileblock').css('position', 'static');
						$('#selectview').hide();
						up.repaint();
					});

					initial = 0;
				}

				$(fs.files).each(function(i, fo) {
					fo.name = t.cleanName(fo.name);

					$('#files').show();
					$('#files tbody').append(t.fileListTpl, {id : fo.id, name : fo.name, size : fo.size});

					$('#' + fo.id + ' a.remove').click(function(e) {
						$('#' + fo.id).remove();
						$.multiUpload.get(up.id).removeFile(fo.id);

						e.preventDefault();
						return false;
					});

					$('#' + fo.id + ' a.rename').click(function(e) {
						var a = $(e.target), inp, parts;

						if (!a.hasClass('disabled')) {
							parts = /^(.+)(\.[^\.]+)$/.exec(fo.name);
							a.hide();
							$(e.target).parent().append('<input id="rename" type="text" class="text" />');
							inp = $('#rename').val(parts[1]);
							t.renameEnabled = 1;

							inp.focus().blur(function() {
								t.endRename();
							}).keydown(function(e) {
								var c = e.keyCode;

								if (c == 13 || c == 27) {
									if (c == 13) {
										fo.name = t.cleanName(inp.val()) + parts[2];
										a.html(fo.name);
									}

									t.endRename();
								}
							});
						}

						e.preventDefault();
						return false;
					});
				});

				up.settings.flash_browse_button = '#addmore';
				up.repaint();
				$('#filelist')[0].scrollTop = 0;
			});

			$(up).bind('multiUpload:fileUploaded', function(e, o) {
				$('#' + o.file.id).removeClass('failed').addClass('done');
			});

			$(up).bind('multiUpload:filesChanged', function() {
				calc(up);
				up.repaint();
				t.endRename();
			});

			$(up).bind('multiUpload:fileUploadProgress', function(e, pr) {
				if (up.status) {
					if (!pr.file.scroll) {
						$('#filelist').scrollTo($('#' + pr.file.id), 50);
						pr.file.scroll = 1;
					}

					$('#' + pr.file.id + ' td.status').html(Math.round(pr.loaded / pr.total * 100.0) + '%');
					calc(up);
				}
			});

			$(up).bind('multiUpload:chunkUploaded', function(e, o) {
				var res = $.parseJSON(o.response), data = RPC.toArray(res.result);

				if (data[0]["status"] != 'OK') {
					o.file.loaded = o.file.size;
					calc(up);
					$('#' + o.file.id).addClass('failed');
					$('#' + o.file.id + ' td.status').html($.translate(data[0]["message"]));
					o.cancel = 1;
				}
			});

			$(up).bind('multiUpload:uploadChunkError', function(e, o) {
				$('#' + o.file.id).addClass('failed');
				$('#' + o.file.id + ' td.status').html('Failed').attr('title', o.error);
				//top.console.log(o.file, o.chunk, o.chunks, o.error);
			});

			// Add UI events
			$('#add, #addmore').click(function(e) {
				up.selectFiles();

				e.preventDefault();
				return false;
			});

			$('#abortupload').click(function(e) {
				up.stopUpload();

				$.WindowManager.info($.translate('{#upload.cancelled}'), function() {
					t.currentWin.close();
				});
			});

			$('#uploadstart').click(function(e) {
				$('#uploadstart').parent().hide();
				$('#status').show();
				$('#statsrow').hide();
				$('#files .status').html('-');
				$('#files .fname a').addClass('disabled');

				startTime = new Date().getTime();
				up.startUpload();

				e.preventDefault();
				return false;
			});

			$('#uploadstop').click(function(e) {
				up.stopUpload();

				e.preventDefault();
				return false;
			});

			$('#clear').click(function(e) {
				up.clearFiles();
				$('#files').hide();
				$('#files tbody').html('');

				e.preventDefault();
				return false;
			});
		},

		insertFiles : function(pa, cb) {
			var s = this.currentWin.getArgs();

			// Insert file
			if (s.onupload) {
				RPC.insertFiles({
					relative_urls : s.relative_urls,
					document_base_url : s.document_base_url,
					default_base_url : s.default_base_url,
					no_host : s.remove_script_host || s.no_host,
					paths : pa,
					insert_filter : s.insert_filter,
					oninsert : function(o) {
						s.onupload(o);

						if (cb)
							cb();
					}
				});
			}
		},

		isDemo : function() {
			if (this.currentWin.getArgs().is_demo) {
				$.WindowManager.info($.translate('{#error.demo}')); 
				return true;
			}
		},

		endRename : function() {
			if (this.renameEnabled) {
				$('#files input').remove();
				$('#files a').show();
				this.renameEnabled = 0;
			}
		}
	};

	// JSON handler
	window.handleJSON = function(data) {
		window.focus();
		UploadDialog.handleSingleUploadResponse(data);
	};

	$(function(e) {
		UploadDialog.init();
	});
})(jQuery);
