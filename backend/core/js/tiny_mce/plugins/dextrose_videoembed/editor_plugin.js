(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('dextrose_videoembed');

	tinymce.create('tinymce.plugins.DextroseVideoEmbed', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			var t = this;
			t.editor = ed;
			
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('pasteVideo', function(ui, embedcode) {
				if(ui){				
					ed.windowManager.open({
						file : url + '/dialog.htm',
						width : 320 + ed.getLang('dextrose_videoembed.delta_width', 0),
						height : 240 + ed.getLang('dextrose_videoembed.delta_height', 0),
						inline : 1
					});
				} else {
					t.insertVideoCode(embedcode);
				}
			});
			
			ed.addButton('dextrose_video', {
				title : 'dextrose_videoembed.video',
				image : url + '/img/movie.png',
				cmd : 'pasteVideo',
				ui: true
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : "Dextrose Video Embed",
				author : 'Bert Pattyn',
				authorurl : 'http://www.dextrose.be/',
				infourl : 'http://www.dextrose.be/tinymce-video-embed/',
				version : "0.0.1"
			};
		},
		
		insertVideoCode : function(embedcode) {
			this.editor.execCommand("mceInsertRawHTML", false, embedcode); 
		}
	});

	// Register plugin
	tinymce.PluginManager.add('dextrose_videoembed', tinymce.plugins.DextroseVideoEmbed);
})();