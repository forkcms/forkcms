/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

function onSilverlightError(sender, args) {
	alert("onSilverlightError: " + args.errormessage);
};

(function($) {
	function isInstalled(version) {
		var isVersionSupported = false;
		var container = null;

		try {
			var control = null;

			try {
				control = new ActiveXObject('AgControl.AgControl');

				if (version == null)
					isVersionSupported = true;
				else if (control.IsVersionSupported(version))
					isVersionSupported = true;

				control = null;
			} catch (e) {
				var plugin = navigator.plugins["Silverlight Plug-In"];

				if (plugin) {
					if (version === null) {
						isVersionSupported = true;
					} else {
						var actualVer = plugin.description;

						if (actualVer === "1.0.30226.2")
							actualVer = "2.0.30226.2";

						var actualVerArray = actualVer.split(".");

						while (actualVerArray.length > 3)
							actualVerArray.pop();

						while ( actualVerArray.length < 4)
							actualVerArray.push(0);

						var reqVerArray = version.split(".");

						while (reqVerArray.length > 4)
							reqVerArray.pop();

						var requiredVersionPart, actualVersionPart, index = 0;

						do {
							requiredVersionPart = parseInt(reqVerArray[index]);
							actualVersionPart = parseInt(actualVerArray[index]);
							index++;
						} while (index < reqVerArray.length && requiredVersionPart === actualVersionPart);

						if (requiredVersionPart <= actualVersionPart && !isNaN(requiredVersionPart))
							isVersionSupported = true;
					}
				}
			}
		} catch (e) {
			isVersionSupported = false;
		}

		return isVersionSupported;
	};

	if (!$.multiUpload.initialized && isInstalled('2.0.31005.0')) {
		$.multiUpload.initialized = 1;
		$.multiUpload.runtime = 'silverlight';

		// Register global Silverlight instance
		$.multiUpload.setup = function(se) {
			$.multiUpload.plugin = $('#multiuploader')[0].content.Upload;
			$.multiUpload._fireEvent('init');
		};

		$.multiUpload.instances = [];

		$.multiUpload._fireEvent = function(na, p1, p2, p3, p4) {
			// Detach event from flash
			window.setTimeout(function() {
				$($.multiUpload.instances).each(function(i, v) {
					v.trigger('multiUpload:' + na, [p1, p2, p3, p4]);
				});
			}, 0);
		};

		// Override init function
		$.extend($.multiUpload.prototype, {
			init : function() {
				var up = this, sel = [];

				$.multiUpload.instances.push(up);

				// Add silverlight runtime
				if (!$('#multiuploader')[0]) {
					$("body").append(
						'<object id="multiuploader" data="data:application/x-silverlight," type="application/x-silverlight-2" width="100" height="100">' +
						'<param name="source" value="' + up.settings.silverlight_xap_url + '"/>' +
						'<param name="onerror" value="onSilverlightError" /></object>'
					);
				}

				// Register silverlight specific event handlers
				$(up).bind('multiUpload:slSelectFile', function(e, id, na, sz) {
					var fo = {id : id, name : na, size : sz, loaded : 0};

					sel.push(fo);
					up.files.push(fo);
				});

				$(up).bind('multiUpload:slSelectSuccessful', function() {
					up.trigger('multiUpload:filesSelected', [{files : sel}]);
					up.trigger('multiUpload:filesChanged');
					sel = [];
				});

				$(up).bind('multiUpload:slSelectCancelled', function() {
					up.trigger('multiUpload:filesSelectionCancelled', [sel]);
					sel = [];
				});

				$(up).bind('multiUpload:slUploadFileProgress', function(e, id, lod, tot) {
					var file = up.getFile(id);

					file.loaded = lod;

					up.trigger('multiUpload:fileUploadProgress', [{file : file, loaded : lod, total : tot}]);
				});

				$(up).bind('multiUpload:slUploadSuccessful', function(e, id, resp) {
					var fo;

					if (fo = up.getFile(id)) {
						if (!fo.status) {
							fo.status = "completed";
							up.trigger('multiUpload:fileUploaded', [{file : fo, response : resp}]);
						}
					}

					up.trigger('multiUpload:filesChanged');
					up.uploadNext();
				});
	
				$(up).bind('multiUpload:stopUpload', function(e) {
					$.multiUpload.plugin.CancelUpload();
				});

				$(up).bind('multiUpload:slUploadChunkSuccessful', function(e, id, chunk, chunks, resp) {
					var fo = up.getFile(id), ar = {file : fo, chunk : chunk, chunks : chunks, response : resp};

					up.trigger('multiUpload:chunkUploaded', [ar]);

					if (ar.cancel) {
						fo.status = "failed";
						$.multiUpload.plugin.CancelUpload();
					}
				});

				$(up).bind('multiUpload:slUploadChunkError', function(e, id, chunk, chunks, err) {
					up.trigger('multiUpload:uploadChunkError', [{file : up.getFile(id), chunk : chunk, chunks : chunks, error : err}]);
				});

				// Register event handlers
				$(up).bind('multiUpload:selectFiles', function(e) {
					$.multiUpload.plugin.SelectFiles(
						'Files |' + $.map(up.settings.filter, function(v) {
							return '*.' + v
						}).join(';')
					);
				});

				$(up).bind('multiUpload:removeFile', function(e, fo) {
					$.multiUpload.plugin.RemoveFile(fo.id);
				});

				$(up).bind('multiUpload:uploadFile', function(e, fo) {
					$.multiUpload.plugin.UploadFile(
						fo.id,
						up.settings.upload_url + '&name=' + escape(fo.name) + "&path=" + escape(up.settings.path),
						parseInt(up.settings.chunk_size)
					);
				});

				$(up).bind('multiUpload:clearFiles', function(e) {
					$.multiUpload.plugin.ClearFiles();
				});
			}
		});
	}
})(jQuery);