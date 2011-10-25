(function($) {
	window.ImageManager = $.extend(BaseManager, {
		page : 0,
		pages : 0,
		pageSize : 25,
		viewMode : 'thumbs',
		tools : [
			'createdir', 'preview', 'refresh', 'edit',
			'upload', 'rename', 'delete', 'download',
			'insert', 'filemanager', 'help'
		],

		init : function() {
			var t = this, args = t.currentWin.getArgs(), uri;

			// Compile templates
			t.type = 'im';
			t.caregoryListTpl = $.templateFromScript('#folders_template');
			t.foldersTpl = $.templateFromScript('#folders_template');
			t.thumbTpl = $.templateFromScript('#thumb_template');
			t.textTpl = $.templateFromScript('#text_template');
			t.customDirsTpl = $.templateFromScript('#custom_dir_template');

			t.path = args.path || '{default}';
			t.rootPath = args.rootpath;
			t.extensions = args.extensions;
			t.include_file_pattern = args.include_file_pattern;
			t.exclude_file_pattern = args.exclude_file_pattern;
			t.include_directory_pattern = args.include_directory_pattern;
			t.exclude_directory_pattern = args.exclude_directory_pattern;
			t.remember_last_path = args.remember_last_path;
			t.urlSuffix = '';

			if (document.domain != document.location.hostname)
				t.urlSuffix = '?domain=' + document.domain;

			if (args.url) {
				uri = $.parseURI(args.url, {base_url : args.document_base_url || args.default_base_url});

				if (uri)
					t.inputURL = uri.path.replace(/\/[^\/]+$/, '');
			}

			if (t.rootPath) {
				chunks = t.rootPath.split(/=/);
				t.rootPathName = chunks.length > 1 ? chunks[0] : /[^\/]+$/.exec(t.rootPath);
				t.rootPath = chunks[1] || t.rootPath;
			}

			// Add template helpers
			$.extend($.template.helpers, {
				add : function(v, a) {
					if (v != 'auto')
						v = parseInt(v) + parseInt(a) + 'px';

					return v;
				}
			});

			t.menu = new $.mcDropMenu({
				halign : 'right',
				valign : 'bottom',
				setup : function(m) {
					$(m).bind('DropMenu:beforeshow', function(e, m) {
						var file = t.focusedFile;

						// Build new menu
						m.clear();

						if (file.type != 'folder')
							m.add({title : $.translate('{#common.insert}'), disabled : t.isDisabled('insert'), onclick : function() {t.insertFiles();}});

						m.add({title : $.translate('{#common.deleteit}'), disabled : t.isDisabled('delete'), onclick : function() {t.deleteFiles([file]);}});

						if (file.type != 'folder') {
							if (file.custom.editable)
								m.add({title : $.translate('{#common.edit}'), disabled : t.isDisabled('edit'), onclick : function() {t.editFile(file);}});
							
							m.add({title : $.translate('{#common.view}'), disabled : t.isDisabled('view'), onclick : function() {t.viewFile(file);}});
							m.add({title : $.translate('{#common.download}'), disabled : t.isDisabled('download'), onclick : function() {t.downloadFile(file);}});
						}
					});
				}
			});

			$().bind('DropMenu:show', function(e, m) {
				$('#' + m.id).css('opacity', 0).animate({
					opacity: 0.9
				}, 300);
			});

			t.menu.render('#filelist');

			// Register toolbar actions
			$().bind('action:createdir', function() {t.createDir();});
			$().bind('action:upload', function() {t.uploadFiles();});
			$().bind('action:refresh', function() {t.listFiles();t.listDirs();});
			$().bind('action:help', function() {});
			$().bind('action:filemanager', function() {t.fileManager()});

			$('#tools li').each(function(i, v) {
				$(v).click(function(e) {
					if (!$(v).hasClass('disabled'))
						$().trigger('action:' + v.id, e);

					e.preventDefault();
					return false;
				});
			});

			$('#filelist').click(function(e) {
				var a = $(e.target).parents('a')[0], fo, o, div, img;

				if (a && a.rel) {
					div = $(a).parents('div.file')[0];
					fo = t.focusedFile = t.files[div.id.replace(/[^0-9]/g, '')];
					t.selectedFiles = [fo];

					if (a.rel == 'file') {
						if (fo.type == 'folder' || fo.type == 'parent') {
							t.page = 0;
							t.listFiles(fo.path);
							t.listDirs();
						} else if (t.config['thumbnail.insert'] == "true")
							t.insertFiles();
					} else if (a.rel == 'menu') {
						img = $(a).find('img');
						o = img.offset();
						t.menu.show(o.left + img.width(), o.top);
					}

					e.preventDefault();
					return false;
				}
			});

			$('#folder_list, #special_list, #category_list').click(function(e) {
				var a = e.target.nodeName == 'A' ? e.target : $(e.target).parents('a')[0];

				if (a) {
					// IE 8 SUX! We need this to keep it from crashing
					window.setTimeout(function() {
						t.page = 0;
						t.listFiles(a.title);
						t.listDirs();
					}, 1);
				}
			});

			$('#selectView').change(function(e) {
				t.viewMode = $(e.target).val();
				t.updateFileList();
			});

			$('#setPages').change(function(e) {
				t.page = 0;
				t.pageSize = $(e.target).val();
				t.listFiles();
			});

			$('#curpage').change(function(e) {
				t.page = parseInt(e.target.value) - 1;
				t.listFiles();
			});

			$('#pages').click(function(e) {
				var el = e.target;

				if (el.nodeName == 'A') {
					if ($(el).hasClass('disabled'))
						return;

					if ($(el).hasClass('next'))
						t.page++;
					else if ($(el).hasClass('prev'))
						t.page--;

					t.listFiles();

					e.preventDefault();
					return false;
				}
			});

			function filter(e) {
				var f = $(e.target).val();

				if (f == t.filterVal)
					return;

				t.filterVal = f;

				if (f == '')
					f = null;
				else if (f.indexOf('*') == -1)
					f = '*' + f + '*';

				t.page = 0;
				t.filter = f;
				t.listFiles();
				t.listDirs();
			};

			$('#filter').change(filter).keyup(function(e) {
				if (e.keyCode == 13)
					filter(e);
			});

			if (t.specialFolders.length) {
				$(t.specialFolders).each(function(i, v) {
					v.title = $.translate(v.title);
					$('#special_list').append(t.customDirsTpl, v);
				});

				$('#special_list').show();
			}

			setInterval(function() {
				RPC.exec('im.keepAlive', {});
			}, 60 * 1000 * 5); // 5 min
		},

		listFiles : function(p) {
			var t = this, args = t.currentWin.getArgs();

			t.path = p || t.path;

			$('#pages').hide();
			$('#progress').show();
			$('#filelist').html('');

			t.page = isNaN(t.page) || t.page < 0 ? 0 : t.page;

			if (t.pages)
				t.page = t.page > t.pages - 1 ? t.pages - 1 : t.page;

			RPC.exec('im.listFiles', {
				path : t.path,
				root_path : t.rootPath,
				url : t.inputURL,
				config : 'general,thumbnail,filesystem,filemanager',
				extensions : t.extensions,
				include_file_pattern : t.include_file_pattern,
				exclude_file_pattern : t.exclude_file_pattern,
				include_directory_pattern : t.include_directory_pattern,
				exclude_directory_pattern : t.exclude_directory_pattern,
				remember_last_path : !!t.remember_last_path,
				page : t.page,
				page_size : t.pageSize,
				filter : t.filter
			}, function(data) {
				var header, cfg, disabled, visible, argTools, argDisabledTools;

				if (!RPC.handleError({message : 'List files error', visual_path : t.visualPath, response : data})) {
					header = data.result.header;
					cfg = data.result.config;
					t.access = header.attribs;
					t.visualPath = header.visual_path;
					t.pages = parseInt(header.pages);
					t.config = cfg;
					t.fileManagerURL = cfg['filemanager.urlprefix'];
					t.demoMode = cfg['general.demo'] == "true";
					t.path = header.path;

					function explode(s) {
						return s ? s.replace(/\s+/g, '').split(',') : s;
					};

					// Enable/disable tools
					disabled = t.disabledTools = explode(cfg['general.disabled_tools']);
					visible = explode(cfg['general.tools']);

					if (argDisabledTools = explode(args.disabled_tools))
						disabled = jQuery.merge(argDisabledTools, disabled);

					if (argTools = explode(args.tools)) {
						$(argTools).each(function(i, v) {
							if (!$.inArray(v, visible))
								visible.push(v);
						});

						visible = $.grep(visible, function(v) {
							return $.inArray(v, argTools);
						});
					}

					$(t.tools).each(function(i, v) {
						var li = $('#' + v);

						t.setDisabled(v, $.inArray(v, disabled) != -1);

						if ($.inArray(v, visible) != -1)
							li.show();
						else
							li.hide();
					});

					$('#tools').show();
					$('#progress').hide();
					$('#curpath').html(t.visualPath).attr('title', t.visualPath);

					// Convert result table into object list
					t.files = RPC.toArray(data.result);

					// Update file list
					t.updateFileList();

					$().trigger('filelist:changed');
				}
			});
		},

		updateFileList : function() {
			var t = this;

			fileLst = $('#filelist');
			fileLst.html('');
			$('#numpages').html(t.pages);
			$('#curpage').val(t.page + 1);
			$('#pages').show();

			if (!t.page)
				$('#pages .prev').addClass('disabled');
			else
				$('#pages .prev').removeClass('disabled');

			if (t.page == t.pages - 1)
				$('#pages .next').addClass('disabled');
			else
				$('#pages .next').removeClass('disabled');

			$(t.files).each(function(i) {
				var r = this, cfg = t.config;

				r = $.extend({
					index : i,
					thumburl : '../../stream/index.php?cmd=im.thumb&path=' + escape(r.path) + '&u=' + r.size,
					thumb_width : (parseInt(cfg["thumbnail.width"]) + 10) + 'px',
					thumb_height : (parseInt(cfg["thumbnail.height"]) + 10) + 'px',
					text_width : (parseInt(cfg["thumbnail.width"]) - 16) + 'px'
				}, this);

				if (r.custom.twidth) {
					r.width = r.custom.twidth + 'px';
					r.height = r.custom.theight + 'px'; 
				} else {
					r.width = r.height = 'auto';
					r.thumburl = 'img/img_generic.png';
				}

				switch (r.type) {
					case 'parent':
						r.thumburl = 'img/parent_big.gif';
						break;

					case 'folder':
						r.thumburl = 'img/folder_big.gif';
						break;

					case 'swf':
					case 'flv':
						r.thumburl = 'img/flash.gif';
						break;

					case 'dcr':
						r.thumburl = 'img/dcr.gif';
						break;

					case 'mov':
					case 'qt':
						r.thumburl = 'img/qt.gif';
						break;

					case 'ram':
					case 'rm':
						r.thumburl = 'img/rm.gif';
						break;

					case 'wmv':
					case 'avi':
					case 'mpg':
					case 'mpeg':
					case 'asf':
						r.thumburl = 'img/avi.gif';
						break;
				}

				fileLst.append(t.viewMode == 'thumbs' ? t.thumbTpl : t.textTpl, r);

				$('#file_' + i + ' img.thumbnailimage').attr('src', r.thumburl);
			});
		},

		deleteFiles : function(fl) {
			var t = this, args = {};

			if (fl) {
				$(fl).each(function(i, v) {
					args['path' + i] = v.path; 
				});

				$.WindowManager.confirm($.translate('{#view.confirm_delete}'), function(s) {
					if (s) {
						if (!t.isDemo()) {
							RPC.exec('im.deleteFiles', args, function (data) {
								if (!RPC.handleError({message : '{#error.delete_failed}', visual_path : t.visualPath, response : data})) {
									t.listFiles();
									t.listDirs();
								}
							});
						}
					}
				});
			}
		},

		insertFiles : function() {
			var t = this, s = t.currentWin.getArgs(), selectedPaths = [];

			$(t.selectedFiles).each(function(i, v) {
				selectedPaths.push(v.path);
			});

			RPC.insertFiles({
				relative_urls : s.relative_urls,
				document_base_url : s.document_base_url,
				default_base_url : s.default_base_url,
				no_host : s.remove_script_host || s.no_host,
				paths : selectedPaths,
				progress_message : $.translate("{#common.image_data}"),
				insert_filter : s.insert_filter,
				oninsert : function(o) {
					t.currentWin.close();

					if (s.oninsert) {
						$(o.files).each(function(i, v) {
							if (v.path == t.focusedFile.path)
								o.focusedFile = v;
						});

						s.oninsert(o);
					}
				}
			});
		},

		editFile : function(f) {
			$('#center').hide();
			window.scrollTo(0, 0);

			$.WindowManager.open({
				url : 'edit.html' + this.urlSuffix,
				onclose : function() {
					$('#center').show();
				}
			}, {
				is_demo : this.demoMode,
				path : this.focusedFile.path,
				visual_path : this.visualPath,
				onsave : function() {
					ImageManager.listFiles();
					ImageManager.listDirs();
				}
			}).maximize();
		},

		downloadFile : function(f) {
			if (f.type != 'folder' && f.type != 'parent')
				document.location = '../../stream/index.php?cmd=im.download&path=' + encodeURIComponent(f.path);
		}, 

		viewFile : function(f) {
			$('#center').hide();
			window.scrollTo(0, 0);

			$.WindowManager.open({
				url : 'view.html' + this.urlSuffix,
				chromeless : 1,
				onclose : function() {
					$('#center').show();
				}
			}, {
				is_demo : this.demoMode,
				path : f.path,
				visual_path : this.visualPath,
				ondelete : function() {
					ImageManager.listFiles();
				}
			}).maximize();
		},

		createDir : function() {
			$.WindowManager.open({
				url : 'createdir.html' + this.urlSuffix,
				width : 450,
				height : 280
			}, {
				is_demo : this.demoMode,
				path : this.path,
				visual_path : this.visualPath,
				oncreate : function() {
					ImageManager.listFiles();
					ImageManager.listDirs();
				}
			});
		},

		uploadFiles : function() {
			$.WindowManager.open({
				url : 'upload.html' + this.urlSuffix,
				width : 550,
				height : 350,
				scrolling : 'no'
			}, {
				is_demo : this.demoMode,
				path : this.path,
				visual_path : this.visualPath,
				onupload : function() {
					ImageManager.listFiles();
				}
			});
		},

		fileManager : function() {
			var suf;

			if (this.fileManagerURL.indexOf('?') != -1)
				suf = this.urlSuffix.replace(/\?/, '&');

			document.location = this.fileManagerURL + suf;
		},

		listRoots : function() {
			var t = this;

			if (t.rootPathName) {
				$('#category_list').html(t.caregoryListTpl, {name : t.rootPathName, path : t.rootPath});
				return;
			}

			RPC.exec('im.listFiles', {
				"path" : "root:///"
			}, function(data) {
				$(RPC.toArray(data.result)).each(function() {
					$('#category_list').append(t.caregoryListTpl, this);
				});
			});
		}, 

		listDirs : function() {
			var t = this;

			$('#folder_list').html($.translate('<li class="progress">{#common.loading}</li>'));

			RPC.exec('im.listFiles', {
				url : t.inputURL,
				path : t.path,
				root_path : t.rootPath,
				only_dirs : true,
				remember_last_path : !!t.remember_last_path,
				include_directory_pattern : t.include_directory_pattern,
				exclude_directory_pattern : t.exclude_directory_pattern,
				filter : t.filter
			}, function(data) {
				$('#folder_list').html('');

				$(RPC.toArray(data.result)).each(function() {
					$('#folder_list').append(t.caregoryListTpl, this);
				});
			});
		}
	});

	$(function() {
		ImageManager.init();
		ImageManager.listFiles();
		ImageManager.listDirs();
		ImageManager.listRoots();
	});
})(jQuery);
