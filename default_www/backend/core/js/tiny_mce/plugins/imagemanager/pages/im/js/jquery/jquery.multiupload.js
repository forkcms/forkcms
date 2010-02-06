/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($) {
	var id = 0;

	$.multiUpload = function(s) {
		var up = this, mul;

		up.id = up.generateID();

		// Default settings
		up.settings = {
		};

		up.settings = s = $.extend(up.settings, s);

		// Parse max size
		if (s.max_size)
			s.max_size = up.parseSize(s.max_size);

		// Parse chunk size
		if (s.chunk_size)
			s.chunk_size = up.parseSize(s.chunk_size);

		if (s.oninit) {
			$(up).bind('multiUpload:init', function() {
				s.oninit.call(up, up);
			});
		}

		up.init();

		$(['setup', 'filesSelected', 'fileProgress', 'filesProgress', 'filesUploaded', 'fileUploaded', 'fileUploadProgress']).each(function() {
			if (s[(this)])
				$(up).bind('multiUpload:' + this, s[(this)]);
		});

		up.trigger('multiUpload:setup');
		$(up).bind('multiUpload:selectFiles', function() {this.cache = {};});

		$(up).bind('multiUpload:filesSelected', function(e, fs) {
			var mx = up.settings.max_size;

			function filter(f) {
				var m = /\.([^.]+)$/.exec(f.name.toLowerCase()), ext = m ? m[1] : null;

				return ext && $.inArray(ext, s.filter) != -1 && (!mx || f.size < mx);
			};

			// Remove non valid files
			if (s.filter[0] != '*') {
				this.files = $.grep(this.files, filter);
				fs.files = $.grep(fs.files, filter);
			}

			this.cache = {};
		});
	};

	// Add public methods
	$.extend($.multiUpload.prototype, {
		files : [],
		cache : {},
		listeners : {},
		status : 0,

		init : function() {
		},

		repaint : function() {
		},

		destroy : function() {
			this.destroyed = true;
		},

		trigger : function(name, args) {
			if (!this.destroyed)
				$(this).trigger(name, args);
		},

		selectFiles : function() {
			this.trigger('multiUpload:selectFiles');
		},

		startUpload : function() {
			this.status = 1;
			this.trigger('multiUpload:startUpload');
			this.uploadNext();
		},

		stopUpload : function() {
			this.status = 0;
			this.trigger('multiUpload:stopUpload');
		},

		uploadNext : function() {
			var i, fl = this.files;

			if (!this.status)
				return;

			for (i = 0; i < fl.length; i++) {
				if (!fl[i].status) {
					this.trigger('multiUpload:uploadFile', [fl[i]]);
					return;
				}
			}

			this.stopUpload();
		},

		getFile : function(id) {
			var t = this, f, i, fl = t.files;

			if (f = t.cache[id])
				return f;

			for (i = 0; i < fl.length; i++) {
				if (fl[i].id == id)
					return t.cache[id] = fl[i];
			}
		},

		removeFile : function(id) {
			var up = this, f;

			up.files = $.grep(up.files, function(v) {
				if (v.id == id)
					f = v;

				return v.id != id;
			});

			this.trigger('multiUpload:removeFile', f);
			this.trigger('multiUpload:filesChanged');
		},

		clearFiles : function() {
			this.stopUpload();
			this.files = [];
			this.cache = {};
			this.trigger('multiUpload:clearFiles');
			this.trigger('multiUpload:filesChanged');
		},

		formatSize : function(v) {
			// MB
			if (v > 1048576)
				return Math.round(v / 1048576, 1) + " MB";

			// KB
			if (v > 1024)
				return Math.round(v / 1024, 1) + " KB";

			return v + " b";
		},

		generateID : function() {
			return 'u' + (id++);
		},

		parseSize : function(sz) {
			var mul;

			if (typeof(sz) == 'string') {
				sz = /^([0-9]+)([mk]+)$/.exec(sz.toLowerCase().replace(/[^0-9mk]/g, ''));
				mul = sz[2];
				sz = parseInt(sz[1]);

				if (mul == 'm')
					sz *= 1048576;

				if (mul == 'k')
					sz *= 1024;
			}

			return sz;
		}
	});

	// Static methods
	$.extend($.multiUpload, {
		instances : {},

		create : function(s) {
			return this.add(new $.multiUpload(s));
		},

		remove : function(id) {
			if (this.get(id))
				delete this.instances[id];
		},

		add : function(up) {
			return this.instances[up.id] = up;
		},

		get : function(id) {
			return this.instances[id];
		}
	});
})(jQuery);
