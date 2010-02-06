/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($) {
	function getFlashVersion() {
		var v;

		try {
			v = navigator.plugins['Shockwave Flash'];
			v = v.description;
		} catch (ex) {
			try {
				v = new ActiveXObject('ShockwaveFlash.ShockwaveFlash').GetVariable('$version');
			} catch (ex) {
				v = '0.0';
			}
		}

		v = v.match(/\d+/g);

		return parseFloat(v[0] + '.' + v[1]);
	};

	if (!$.multiUpload.initialized && getFlashVersion() >= 10) {
		$.multiUpload.initialized = 1; 
		$.multiUpload.runtime = 'flash';
		$.multiUpload.instances = [];

		$.multiUpload._fireEvent = function(na, p1, p2, p3, p4) {
			// Detach event from flash
			window.setTimeout(function() {
				$($.multiUpload.instances).each(function(i, v) {
					$(v).trigger('multiUpload:' + na, [p1, p2, p3, p4]);
					v.repaint();
				});
			}, 0);
		};

		// Override init function
		$.extend($.multiUpload.prototype, {
			getFlash : function() {
				return document.getElementById('flashuploader');
			},

			repaint : function() {
				var up = this, fb = $(up.settings.flash_browse_button), off = fb.offset();

				up.flashContainer.css({position : 'absolute', top : off.top, left : off.left, width : fb.width(), height : fb.height()});
			},

			init : function() {
				var up = this, s = up.settings, so, fb, filter, flashUrl = 'js/jquery/jquery.multiupload.flash.swf?r=' + new Date().getTime();

				$.multiUpload.instances.push(up);

				$(up).bind('multiUpload:flashInit', function(e) {
					$(up).trigger('multiUpload:init');
				});

				filter = $.map(up.settings.filter, function(v) {
					return '*.' + v
				}).join(';');

				$(document.body).append(
					'<div id="flashUploaderContainer">' +
					'<object id="flashuploader" width="100%" height="100%" type="application/x-shockwave-flash" data="' + flashUrl + '">' +
					'<param name="movie" value="' + flashUrl + '" />' +
					'<param name="wmode" value="transparent" />' +
					'<param name="allowscriptaccess" value="always" />' +
					'<param name="flashvars" value="file_filter=' + escape(filter) + '" />' +
					'<embed src="' + flashUrl + '" type="application/x-shockwave-flash" allowscriptaccess="always" width="100%" height="100%"></embed>' + 
					'</object></div>'
				);

				up.flashContainer = $('#flashUploaderContainer');

				// Reposition flash
				$(function(e) {
					up.repaint();
				});

				up.repaint();

				// Register event handlers

				$(up).bind('multiUpload:flashSelectFiles', function(e, sel) {
					// Add selected files to file queue
					$(sel).each(function(i, fo) {
						up.files.push(fo);
					});

					// Trigger events
					$(up).trigger('multiUpload:filesSelected', [{files : sel}]);
					$(up).trigger('multiUpload:filesChanged');
				});

				$(up).bind('multiUpload:flashUploadComplete', function(e, o) {
					var fo;

					if (fo = up.getFile(o.id)) {
						if (!fo.status) {
							fo.status = 'completed';
							$(up).trigger('multiUpload:fileUploaded', [{file : fo, response : o.text}]);
						}
					}

					$(up).trigger('multiUpload:filesChanged');
					up.uploadNext();
				});

				$(up).bind('multiUpload:flashUploadProcess', function(e, pr) {
					var fo = up.getFile(pr.id);

					if (!fo.status) {
						fo.loaded = pr.loaded;
						$(up).trigger('multiUpload:fileUploadProgress', [{file : fo, loaded : fo.loaded, total : fo.size}]);
					}
				});

				$(up).bind('multiUpload:flashIOError', function(e, o) {
					var fo;

					if (fo = up.getFile(o.id)) {
						if (!fo.status) {
							fo.status = 'failed';
							$(up).trigger('multiUpload:uploadChunkError', [{file : fo, error : o.message}]);
						}
					}
				});

				$(up).bind('multiUpload:stopUpload', function(e) {
					up.getFlash().cancelUpload();
				});

				$(up).bind('multiUpload:removeFile', function(e, fo) {
					up.getFlash().removeFile(fo.id);
				});

				$(up).bind('multiUpload:clearFiles', function(e) {
					up.getFlash().clearFiles();
				});

				$(up).bind('multiUpload:uploadFile', function(e, fo) {
					var pageURL = document.location.href.replace(/\/[^\/]+$/g, '/');
var flash = up.getFlash();
					flash.uploadFile(fo.id, {
						upload_url : pageURL + up.settings.upload_url + '&path=' + escape(up.settings.path) + '&name=' + escape(fo.name),
						chunk_size : up.settings.chunk_size,
						file_field : 'file0',
						post_args : {
							name0 : fo.name
						}
					});
				});

				$(up).bind('multiUpload:flashUploadChunkComplete', function(e, o) {
					var fo = up.getFile(o.id), arg;

					arg = {
						file : fo,
						chunk : o.chunk,
						chunks : o.chunks,
						response : o.text
					};

					if (!fo.status)
						$(up).trigger('multiUpload:chunkUploaded', [arg]);

					if (arg.cancel) {
						fo.status = 'failed';
						up.getFlash().cancelUpload();
					}
				});
			}
		});
	}
})(jQuery);