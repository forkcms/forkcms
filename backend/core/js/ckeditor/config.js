if(!jsBackend) { var jsBackend = new Object(); }


/**
 * CK Editor related objects
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.ckeditor =
{
	// initialize the editor
	init: function() {
		// bind on inputEditor and inputEditorError
		$('textarea.inputEditor, textarea.inputEditorError').ckeditor(
			jsBackend.ckeditor.callback,
			{
				customConfig: '',

				// layout configuration
				bodyClass: 'content',
				content_css: [
					'/frontend/core/layout/css/screen.css'{option:THEME_HAS_CSS},
					'/frontend/themes/{$THEME}/core/layout/css/screen.css', {/option:THEME_HAS_CSS}
					'/backend/core/layout/css/editor_content.css'{option:THEME_HAS_EDITOR_CSS},
					'/frontend/themes/{$THEME}/core/layout/css/editor_content.css'{/option:THEME_HAS_EDITOR_CSS}
				],

				// language options
				contentsLanguage: '{$LANGUAGE}',
				language: '{$INTERFACE_LANGUAGE}',

				// paste options
				forcePasteAsPlainText: true,

				// buttons
				toolbar_Full: [
					{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Strike'] },
					{ name: 'clipboard',   items: [ 'Undo', 'Redo' ] },
					{ name: 'paragraph',   items: [ 'NumberedList', 'BulletedList', 'Blockquote' ] },
					{ name: 'links',       items: [ 'Link', 'Unlink', 'Anchor' ] },
					{ name: 'document',    items: [ 'Source', 'ShowBlocks', 'Maximize', 'Templates' ] },
					'/',
					{ name: 'insert',      items : [ 'Table', '-', 'Image', 'Flash', 'SpecialChar' ] },
					{ name: 'styles',      items : [ 'Format' ] }
				],

				// skin
				skin: 'kama',
				uiColor: '#E7F0F8',
//				toolbarStartupExpanded: false

				// remove useless plugins
				removePlugins: 'about,a11yhelp,bidi,colorbutton,colordialog,font,format,find,forms,horizontalrule,indent,newpage,pagebreak,preview,print,smiley'

			}
		);
	},

	//
	callback: function() {
		console.log(CKEDITOR.config);
	}
}

$(document).ready(jsBackend.ckeditor.init);