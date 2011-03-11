(function($){
	window.FileManager = $.extend(BaseManager, {
		focusedIndex : -1,
		clipboard : null,
		access : '',
		tools : [
			'createdir', 'edit', 'createdoc', 'refresh', 'zip',
			'upload', 'rename', 'cut', 'copy',
			'paste', 'delete', 'view', 'download',
			'insert', 'imagemanager', 'help'
		],

		init : function() {
			var t = this, args = t.currentWin.getArgs(), uri, chunks;

			$('#selectall,#unselectall').removeClass('disabled');

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

			// Compile templates
			t.fileListTpl = $.templateFromScript('#filelist_item_template');
			t.dirInfoTpl = $.templateFromScript('#dirinfo_template');
			t.caregoryListTpl = $.templateFromScript('#folders_template');
			t.previewFileTpl = $.templateFromScript('#preview_file_template');
			t.pathTpl = $.templateFromScript('#path_template');
			t.customDirsTpl = $.templateFromScript('#custom_dir_template');

			$('#filemanagerlist').css('height', $.winHeight() - 140);
			$(window).bind('resize', function() {
				$('#filemanagerlist').css('height', $.winHeight() - 140);
			});

			// Register default actions
			$().bind('action:selectall', function() {t.setFileSelection(0, t.files.length, true);});
			$().bind('action:unselectall', function() {t.focusFile(0);});
			$().bind('action:cut', function() {t.setClipBoard({files : t.selectedFiles, action : 'cut'});});
			$().bind('action:copy', function() {t.setClipBoard({files : t.selectedFiles, action : 'copy'});});
			$().bind('action:paste', function() {t.pasteFiles();});
			$().bind('action:rename', function() {t.renameFile(t.focusedFile);});
			$().bind('action:delete', function() {t.deleteFiles(t.selectedFiles);});
			$().bind('action:zip', function() {t.zipFiles();});

			// Register sort actions
			$().bind('action:sortbyname', function(e) {t.updateFileList('name', $(e.target).hasClass('sortasc'));});
			$().bind('action:sortbysize', function(e) {t.updateFileList('size', $(e.target).hasClass('sortasc'));});
			$().bind('action:sortbytype', function(e) {t.updateFileList('type', $(e.target).hasClass('sortasc'));});
			$().bind('action:sortbymodified', function(e) {t.updateFileList('modified', $(e.target).hasClass('sortasc'));});

			// Register view actions
			$().bind('action:insert', function() {t.insertFiles();});
			$().bind('action:download', function() {t.downloadFile(t.focusedFile);});
			$().bind('action:view', function() {t.viewFile(t.focusedFile);});

			// Register toolbar actions
			$().bind('action:createdir', function() {t.createDir();});
			$().bind('action:edit', function() {t.editFile(t.focusedFile);});
			$().bind('action:upload', function() {t.uploadFiles();});
			$().bind('action:createdoc', function() {t.createDoc();});
			$().bind('action:refresh', function() {t.listFiles();});
			$().bind('action:help', function() {});
			$().bind('action:imagemanager', function() {t.imageManager();});

			$().bind('filemanager:clipboardchanged', function() {
				if (t.clipboard && !$('#paste').hasClass('deactivated'))
					$('#paste').removeClass('disabled');
				else
					$('#paste').addClass('disabled');
			});

			$().bind('selection:changed', function() {
				$(['cut', 'copy', 'delete', 'zip']).each(function(i, v) {
					if (t.selectedFiles.length > 0 && !$('#' + v).hasClass('deactivated'))
						$('#' + v).removeClass('disabled');
					else
						$('#' + v).addClass('disabled');
				});
			});

			$().bind('filelist:changed', function() {
				if (t.clipboard && !$('#paste').hasClass('deactivated'))
					$('#paste').removeClass('disabled');
				else
					$('#paste').addClass('disabled');
			});

			$().bind('selection:changed', function() {
				if (t.focusedFile)
					$('#previewinfo').html(t.previewFileTpl, t.focusedFile);
				else
					$('#previewinfo').html('&nbsp;');

				$(['rename', 'insert', 'download', 'view', 'edit']).each(function(i, v) {
					if (!t.focusedFile) {
						$('#' + v).addClass('disabled');
						t.showPreview(0);
					} else if (!$('#' + v).hasClass('deactivated'))
						$('#' + v).removeClass('disabled');
				});

				if (t.focusedFile && (!t.focusedFile.custom || !t.focusedFile.custom.editable))
						$('#edit').addClass('disabled');
			});

			$('#selection_actions li, #view_actions li, #tools li, #filemanagerlist a').each(function(i, v) {
				$(v).click(function(e) {
					if (!$(v).hasClass('disabled'))
						$().trigger('action:' + v.id, e);

					e.preventDefault();
					return false;
				});
			});

			$('#filemanagerlist').mousedown(function(e) {
				if (e.shiftKey)
					e.preventDefault();
			});

			$('#filemanagerlist').bind('selectstart', function(e) {
				if (e.target.nodeName != 'INPUT')
					e.preventDefault();
			});

			$('#category_list').click(function(e) {
				var el = e.target;

				if (el.title)
					t.listFiles(el.title);

				e.preventDefault();
				return false;
			});

			$('#special_list').click(function(e) {
				var el = e.target;

				if (el.title)
					t.listFiles(el.title);

				e.preventDefault();
				return false;
			});

			$('#filemanagerlist').click(t.handleSelection);
			$('#filemanagerlist').dblclick(t.handleSelection);
			$('#filemanagerlist').bind('contextmenu', t.handleSelection);

			$('#filemanagerlist').mcContextMenu({
				constrain : 1,
				setup : function(m) {
					$(m).bind('DropMenu:beforeshow', function(e, m) {
						// Build new menu
						m.clear();
						m.add({title : $.translate('{#actions.cut}'), disabled : t.isDisabled('cut') || !t.selectedFiles.length, onclick : function() {t.setClipBoard({files : t.selectedFiles, action : 'cut'});}});
						m.add({title : $.translate('{#actions.copy}'), disabled : t.isDisabled('copy') || !t.selectedFiles.length, onclick : function() {t.setClipBoard({files : t.selectedFiles, action : 'copy'});}});
						m.add({title : $.translate('{#actions.paste}'), disabled : t.isDisabled('paste') || !t.clipboard, onclick : function() {t.pasteFiles();}});
						m.addSeparator();
						m.add({title : $.translate('{#actions.edit}'), disabled : !t.focusedFile || (!t.focusedFile.custom || !t.focusedFile.custom.editable) || t.isDisabled('edit') || !t.selectedFiles.length, onclick : function() {t.editFile(t.focusedFile);}});
						m.add({title : $.translate('{#actions.rename}'), disabled : t.isDisabled('rename') || !t.selectedFiles.length, onclick : function() {t.renameFile(t.focusedFile);}});
						m.add({title : $.translate('{#actions.deleteit}'), disabled : t.isDisabled('delete') || !t.selectedFiles.length, onclick : function() {t.deleteFiles(t.selectedFiles);}});
						m.add({title : $.translate('{#actions.zip}'), disabled : t.isDisabled('zip') || !t.selectedFiles.length, onclick : function() {t.zipFiles();}});
					});
				}
			});

			$().bind('DropMenu:show', function(e, m) {
				$('#' + m.id).css('opacity', 0).animate({
					opacity: 0.9
				}, 100);
			});

			if (t.specialFolders.length) {
				$(t.specialFolders).each(function(i, v) {
					v.title = $.translate(v.title);
					$('#special_list').append(t.customDirsTpl, v);
				});

				$('#special_list').show();
			}

			setInterval(function() {
				RPC.exec('fm.keepAlive', {});
			}, 60 * 1000 * 5); // 5 min
		},

		handleSelection : function(e) {
			var t = FileManager, el = e.target, tr = $(el).parents('tr.listrow')[0], idx, type;

			if (tr && el.nodeName != 'INPUT') {
				idx = parseInt(tr.id.replace(/[^0-9]+/g, ''));
				t.focusedFile = t.files[idx];
				type = t.focusedFile.type;

				$('#file_' + t.focusedIndex).removeClass('focused');

				if (e.shiftKey)
					t.setFileSelection(t.focusedIndex, idx, true, 1);

				if ($(el).hasClass('checkbox')) {
					if ($(tr).hasClass('selected')) {
						$(tr).removeClass('focused');
						idx = -1;
						t.focusedFile = null;
					} else
						$(tr).addClass('focused');

					$(tr).toggleClass('selected');
					t.showPreview(0);
				} else {
					if (!e.shiftKey && (type == 'folder' || type == 'parent' || type == 'zip') && e.type == 'click') {
						if (type == 'zip')
							t.listFiles('zip://' + t.focusedFile.path);
						else
							t.listFiles(t.focusedFile.path);

						t.showPreview(0);

						e.preventDefault();
						return false;
					} else if (e.type != 'dblclick')
						t.showPreview(t.focusedFile);

					if (!e.shiftKey && (e.type != 'contextmenu' || !$(tr).hasClass('selected'))) {
						$('#filemanagerlist tbody tr').removeClass('selected');
						$('#file_' + t.focusedIndex).removeClass('selected');
					}

					$(tr).addClass('focused').addClass('selected');
				}

				// Build list of selected files
				t.selectedFiles = [];
				$('#filemanagerlist tbody tr.selected').each(function(i, tr) {
					t.selectedFiles.push(t.files[parseInt(tr.id.replace(/[^0-9]+/g, ''))]);
				});

				t.focusedIndex = idx;

				$().trigger('selection:changed');

				if (e.type == 'dblclick' && !t.insertDisabled)
					t.insertFiles(t.selectedFiles);

				e.preventDefault();
				return false;
			}
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
					FileManager.listFiles();
				}
			});
		},

		createDoc : function() {
			$.WindowManager.open({
				url : 'createdoc.html' + this.urlSuffix,
				width : 450,
				height : 280
			}, {
				is_demo : this.demoMode,
				path : this.path,
				visual_path : this.visualPath,
				oncreate : function() {
					FileManager.listFiles();
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
					FileManager.listFiles();
				}
			});
		},

		zipFiles : function() {
			var files = [];

			$(this.selectedFiles).each(function(i, f) {
				files.push(f.path);
			});

			$.WindowManager.open({
				url : 'createzip.html' + this.urlSuffix,
				width : 450,
				height : 280
			}, {
				is_demo : this.demoMode,
				files : files,
				path : this.path,
				visual_path : this.visualPath,
				oncreate : function() {
					FileManager.listFiles();
				}
			});
		},

		editFile : function(fi) {
			$.WindowManager.open({
				url : 'edit.html' + this.urlSuffix,
				width : 750,
				height : 450
			}, {
				is_demo : this.demoMode,
				path : fi.path,
				visual_path : (this.visualPath + '/' + /[^\/]+$/.exec(fi.path)).replace(/^\/\//, '/'),
				onsave : function() {
					FileManager.listFiles();
				}
			});
		},

		showPreview : function(fi) {
			if (fi && (!fi.custom || fi.custom.previewable) && fi.type != 'zip')
				$('#preview').attr('src', '../../stream/index.php?cmd=fm.streamFile&path=' + encodeURIComponent(fi.path)).css('visibility', 'visible');
			else
				$('#preview').attr('src', "javascript:''").css('visibility', 'hidden');
		},

		deleteFiles : function(fl) {
			var t = this, args = {};

			if (fl) {
				$(fl).each(function(i, v) {
					args['path' + i] = v.path; 
				});

				$.WindowManager.confirm($.translate('{#message.confirm_delete}'), function(s) {
					if (s) {
						if (!t.isDemo()) {
							RPC.exec('fm.deleteFiles', args, function (data) {
								if (!RPC.handleError({message : '{#error.delete_failed}', visual_path : t.visualPath, response : data}))
									t.listFiles();
							});
						}
					}
				});
			}
		},

		pasteFiles : function() {
			var t = this, args = {};

			if (t.clipboard && !t.isDemo()) {
				args.topath = t.path;

				$(t.clipboard.files).each(function(i, v) {
					args['frompath' + i] = v.path; 
				});

				$.WindowManager.showProgress({message : $.translate("{#message.paste_in_progress}")});
				if (t.clipboard.action == 'cut') {
					RPC.exec('fm.moveFiles', args, function(data) {
						$.WindowManager.hideProgress();

						if (!RPC.handleError({message : '{#error.move_failed}', visual_path : t.visualPath, response : data}))
							t.listFiles();
					});
				} else {
					RPC.exec('fm.copyFiles', args, function(data) {
						$.WindowManager.hideProgress();

						if (!RPC.handleError({message : '{#error.copy_failed}', visual_path : t.visualPath, response : data}))
							t.listFiles();
					});
				}

				t.clipboard = null;
				$().trigger('filemanager:clipboardchanged');
			}
		},

		setClipBoard : function(clip) {
			this.clipboard = clip;
			$().trigger('filemanager:clipboardchanged', clip);
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
				insert_filter : s.insert_filter,
				progress_message : $.translate("{#message.insert}"),
				oninsert : function(o) {
					if (s.oninsert) {
						$(o.files).each(function(i, v) {
							if (v.path == t.focusedFile.path)
								o.focusedFile = v;
						});

						s.oninsert(o);
					}

					t.currentWin.close();
				}
			});
		},

		renameFile : function(f) {
			var t = this, inp, ext = '', ma;

			t.curRenameFile = f;
			inp = $.createElm('input', {id : f.id + '_rename', type : "text", "class" : "text", "value" : f.name.replace(/\.[^\.]+$/, '')});
			$('#' + f.id + ' td.file').append(inp).find('a').hide();

			if (ma = /\.[^\.]+$/.exec(f.name))
				ext = ma[0];

			inp.focus().blur(function() {
				t.endRename();
			}).keyup(function(e) {
				var c = e.keyCode, na;

				if (c == 13 || c == 27) {
					if (c == 13) {
						if (!t.isDemo()) {
							na = inp.val();

							if (t.shouldCleanNames)
								na = $.cleanName(na);

							RPC.exec('fm.moveFiles', {
								frompath0 : f.path,
								toname0 : na + ext
							}, function (data) {
								if (!RPC.handleError({message : '{#error.rename_failed}', visual_path : t.visualPath, response : data}))
									t.listFiles();
							});
						}
					}

					t.endRename();
				}
			})
		},

		endRename : function() {
			var td;
			
			if (this.curRenameFile) {
				td = $('#' + this.curRenameFile.id + ' td.file');
				td.find('input').remove();
				td.find('a').show();
			}
		},

		listFiles : function(p, col, des) {
			var t = this, args = t.currentWin.getArgs();

			t.path = p || t.path;

			$('#dirinfo').hide();
			$('#progress').show();
			$('#filelist').html('');
			t.showPreview(0);

			RPC.exec('fm.listFiles', {
				path : t.path,
				root_path : t.rootPath,
				url : t.inputURL,
				extensions : t.extensions,
				include_file_pattern : t.include_file_pattern,
				exclude_file_pattern : t.exclude_file_pattern,
				include_directory_pattern : t.include_directory_pattern,
				exclude_directory_pattern : t.exclude_directory_pattern,
				config : 'general,filesystem,imagemanager',
				remember_last_path : t.remember_last_path
			}, function(data) {
				var header, cfg, disabled, visible, argTools, argDisabledTools;

				if (!RPC.handleError({message : 'List files error', visual_path : t.visualPath, response : data})) {
					header = data.result.header;
					cfg = data.result.config;

					t.access = header.attribs;
					t.visualPath = header.visual_path;
					t.imageManagerURL = cfg['imagemanager.urlprefix'];
					t.demoMode = cfg['general.demo'] == "true";
					t.path = header.path;

					function explode(s) {
						return s ? s.replace(/\s+/g, '').split(',') : s;
					};

					// Enable/disable tools
					t.shouldCleanNames = cfg['filesystem.clean_names'] == "true";
					disabled = explode(cfg['general.disabled_tools']);
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

						if ($.inArray(v, visible) != -1) {
							li.show();

							if (v == 'insert')
								t.insertDisabled = false;
						} else {
							li.hide();
						
							if (v == 'insert')
								t.insertDisabled = true;
						}
					});

					$('#tools').show();
					$('#progress').hide();
					$('#curpath').html(t.pathTpl, header).attr("title", t.visualPath);

					// Convert result table into object list
					t.files = RPC.toArray(data.result);

					// Update file list
					t.updateFileList(col, des);

					$().trigger('filelist:changed');
				}
			});
		},

		updateFileList : function(col, des) {
			var t = this, info = {dirs : 0, files : 0, filesize : 0, access : t.access}, co = 0, fileLst;

			col = col || 'name';

			$('thead a.sortdesc').removeClass('sortdesc');
			$('thead a.sortasc').removeClass('sortasc');
			$('#sortby' + col).removeClass('sort' + (des ? 'asc' : 'desc'));
			$('#sortby' + col).addClass('sort' + (des ? 'desc' : 'asc'));

			t.endRename();
			t.focusedIndex = -1;
			t.focusedFile = null;
			t.selectedFiles = [];
			$().trigger('selection:changed');

			// Sort the files
			this.files = $(this.files).sortArray(col, des);

			// Make sure directories are before files
			this.files = $.grep(this.files, function(v) {
				return v.type == 'folder' || v.type == 'parent';
			}).concat($.grep(this.files, function(v) {
				return v.type != 'folder' && v.type != 'parent';
			}));

			$('#filelist').html('');

			// Collect directory info
			$(this.files).each(function(i, f) {
				if (f.type != 'parent') {
					if (f.type == 'folder')
						info.dirs++;
					else
						info.files++;

					if (f.size != -1)
						info.filesize += f.size;
				}

				f.id = 'file_' + i;
			});

			$('#dirinfo').html(t.dirInfoTpl, info);
			$('#dirinfo').show();
			fileLst = $('#filelist');

			$(this.files).each(function() {
				fileLst.append(t.fileListTpl, this);
			});
		},

		downloadFile : function(f) {
			if (f.type != 'folder' && f.type != 'parent')
				$('#preview').attr('src', '../../stream/index.php?cmd=fm.download&path=' + encodeURIComponent(f.path));
		},

		viewFile : function(f) {
			if (f.type != 'folder' && f.type != 'parent' && f.type != 'zip')
				window.open('../../stream/index.php?cmd=fm.streamFile&path=' + encodeURIComponent(f.path), 'View'); 
		},

		imageManager : function() {
			var suf;

			if (this.imageManagerURL.indexOf('?') != -1)
				suf = this.urlSuffix.replace(/\?/, '&');

			document.location = this.imageManagerURL + suf;
		},

		focusFile : function(f) {
			var t = this;

			if (f && f.type == 'parent')
				return;

			$('#filemanagerlist tbody tr').removeClass('selected').removeClass('focused');

			if (!f) {
				t.selectedFiles = [];
				t.focusedFile = null;
				t.focusedIndex = -1;
				$().trigger('selection:changed');
				t.showPreview(0);
				return;
			}

			t.focusedIndex = f.index;
			t.focusedFile = f;
			t.selectedFiles = [f];
			$().trigger('selection:changed');
			$('#' + f.id).addClass('selected focused');
			t.showPreview(f);
		},

		setFileSelection : function(st, en, ck, nup) {
			var t = this, i;

			function sel(v, i) {
				if (v && v.type != 'parent') {
					v = $('#file_' + i);

					if (ck)
						v.addClass('selected');
					else if (!v.hasClass('focused'))
						v.removeClass('selected');
				}
			};

			for (i = st; i < en; i++)
				sel(t.files[i], i);

			for (i = en; i < st; i++)
				sel(t.files[i], i);

			if (!nup) {
				// Build list of selected files
				t.selectedFiles = [];
				$('#filemanagerlist tbody tr.selected').each(function(i, tr) {
					t.selectedFiles.push(t.files[parseInt(tr.id.replace(/[^0-9]+/g, ''))]);
				});
			}

			$().trigger('selection:changed');
		},

		listRoots : function() {
			var t = this;

			if (t.rootPathName) {
				$('#category_list').html(t.caregoryListTpl, {name : t.rootPathName, path : t.rootPath});
				return;
			}

			$('#progress').show();

			RPC.exec('fm.listFiles', {
				"path" : "root:///"
			}, function(data) {
				if (!RPC.handleError({message : 'List files error', visual_path : t.visualPath, response : data})) {
					$(RPC.toArray(data.result)).each(function() {
						$('#category_list').append(t.caregoryListTpl, this);
					});
				}
			});
		}
	});

	$(function() {
		FileManager.init();
		FileManager.listFiles();
		FileManager.listRoots();
	});
})(jQuery);
