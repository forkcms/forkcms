/*
 * Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see license.txt or http://cksource.com/ckfinder/license
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

CKFinder.addPlugin( 'fileeditor', function( api )
{
	var regexTextExt = /^(ascx|asp|aspx|c|cfc|cfm|cpp|cs|css|htm|html|inc|java|js|less|md|mysql|php|pl|py|rb|rst|sass|scss|sql|txt|xml|xsl|xslt)$/i,
		regexCodeMirrorExt = /^(ascx|asp|aspx|c|cfc|cfm|cpp|cs|css|htm|html|java|js|less|md|mysql|php|pl|py|rb|rst|sass|scss|sql|xml|xsl)$/i,
		codemirror,
		file,
		fileLoaded = false,
		doc,

		codeMirrorPath = CKFinder.getPluginPath( 'fileeditor' ) + 'codemirror/',
		codeMirrorModePath = codeMirrorPath + 'mode/',

		codeMirrorParsers = {
		c: codeMirrorModePath + 'clike/clike.js',
		css: codeMirrorModePath + 'css/css.js',
		html: [ codeMirrorModePath + 'xml/xml.js', codeMirrorModePath + 'javascript/javascript.js', codeMirrorModePath + 'css/css.js', codeMirrorModePath + 'htmlmixed/htmlmixed.js' ],
		js: codeMirrorModePath + 'javascript/javascript.js',
		md: codeMirrorModePath + 'markdown/markdown.js',
		php: [ codeMirrorModePath + 'xml/xml.js', codeMirrorModePath + 'javascript/javascript.js', codeMirrorModePath + 'css/css.js', codeMirrorModePath + 'clike/clike.js', codeMirrorModePath + 'php/php.js' ],
		pl: codeMirrorModePath + 'perl/perl.js',
		py: codeMirrorModePath + 'python/python.js',
		rb: codeMirrorModePath + 'ruby/ruby.js',
		rst: [ codeMirrorModePath + 'rst/rst.js', codeMirrorModePath + 'python/python.js', codeMirrorModePath + 'stex/stex.js', codeMirrorPath + 'addon/mode/overlay.js' ],
		sql: codeMirrorModePath + 'sql/sql.js',
		sass: codeMirrorModePath + 'sass/sass.js',
		xml: codeMirrorModePath + 'xml/xml.js'
	};
	codeMirrorParsers.ascx = codeMirrorParsers.html;
	codeMirrorParsers.asp = codeMirrorParsers.html;
	codeMirrorParsers.aspx = codeMirrorParsers.html;
	codeMirrorParsers.cfm = codeMirrorParsers.html;
	codeMirrorParsers.cfc = codeMirrorParsers.html;
	codeMirrorParsers.less = codeMirrorParsers.css;
	codeMirrorParsers.cpp = codeMirrorParsers.c;
	codeMirrorParsers.cs = codeMirrorParsers.c;
	codeMirrorParsers.htm = codeMirrorParsers.html;
	codeMirrorParsers.java = codeMirrorParsers.c;
	codeMirrorParsers.mysql = codeMirrorParsers.sql;
	codeMirrorParsers.scss = codeMirrorParsers.css;
	codeMirrorParsers.xsl = codeMirrorParsers.xml;

	var codeMirrorModes = {
		ascx : 'htmlmixed',
		asp : 'htmlmixed',
		aspx : 'htmlmixed',
		c : 'clike',
		cpp : 'clike',
		cs : 'clike',
		cfc : 'htmlmixed',
		cfm : 'htmlmixed',
		htm : 'htmlmixed',
		html : 'htmlmixed',
		java : 'clike',
		js : 'javascript',
		less : 'css',
		md : 'markdown',
		mysql : 'sql',
		php : 'php',
		pl : 'perl',
		py : 'python',
		rb : 'ruby',
		rst : 'rst',
		sass : 'sass',
		scss : 'css',
		sql : 'sql',
		xsl : 'xml'
	};

	CKFinder.dialog.add( 'fileEditor', function( api )
	{
		var height, width,
			saveButton = (function()
				{
					return {
						id : 'save',
						label : api.lang.Fileeditor.save,
						type : 'button',
						onClick : function ( evt )
						{
							if ( !fileLoaded )
								return true;

							var dialog = evt.data.dialog,
								content = codemirror ? codemirror.getValue() : doc.getById( 'fileContent' ).getValue();
							api.connector.sendCommandPost( 'SaveFile', null, {
									content : content,
									fileName : file.name
								},
								function( xml )
								{
									if ( xml.checkError() )
										return false;

									api.openMsgDialog( '', api.lang.Fileeditor.fileSaveSuccess );
									dialog.hide();
									return undefined;
								},
								file.folder.type,
								file.folder
							);
							return false;
						}
					};
				})();

		if ( api.inPopup )
		{
			width = api.document.documentElement.offsetWidth;
			height = api.document.documentElement.offsetHeight;
		}
		else
		{
			var parentWindow = ( api.document.parentWindow || api.document.defaultView ).parent;
			width = parentWindow.innerWidth ? parentWindow.innerWidth : parentWindow.document.documentElement.clientWidth;
			height = parentWindow.innerHeight ? parentWindow.innerHeight : parentWindow.document.documentElement.clientHeight;
		}

		var cssWidth = parseInt( parseInt( width, 10 ) * 0.6 ),
			cssHeight = parseInt( parseInt( height, 10 ) * 0.7 - 20 );

		return {
			title : api.getSelectedFile().name,
			minWidth : parseInt( parseInt( width, 10 ) * 0.6 ),
			minHeight : parseInt( parseInt( height, 10 ) * 0.7 ),
			onHide : function()
			{
				if ( fileLoaded )
				{
					var fileContent = doc.getById( 'fileContent' );
					if ( fileContent )
						fileContent.remove();
				}
			},
			onShow : function()
			{
				var dialog = this;

				doc = dialog.getElement().getDocument();
				var win = doc.getWindow();
				doc.getById( 'fileArea' ).setHtml( '<div class="ckfinder_loader_32" style="margin: 100px auto 0 auto;text-align:center;"><p style="height:' + cssHeight + 'px;width:' + cssWidth + 'px;">' + api.lang.Fileeditor.loadingFile + '</p></div>' );

				file = api.getSelectedFile();
				var enableCodeMirror = regexCodeMirrorExt.test( file.ext );
				this.setTitle( file.name );

				if ( enableCodeMirror && win.$.CodeMirror === undefined ) {
					doc.appendStyleSheet( codeMirrorPath + 'lib/codemirror.css' );
				}

				// If CKFinder is running under a different domain than baseUrl, then the following call will fail:
				// CKFinder.ajax.load( file.getUrl() + '?t=' + (new Date().getTime()), function( data )...

				var url = api.connector.composeUrl( 'DownloadFile', { FileName : file.name, format : 'text', t : new Date().getTime() },
						file.folder.type, file.folder );

				CKFinder.ajax.load( url, function( data )
				{
					if ( data === null || ( file.size > 0 && data === '' ) )
					{
						api.openMsgDialog( '', api.lang.Fileeditor.fileOpenError );
						dialog.hide();
						return;
					}
					else
						fileLoaded = true;

					var fileArea = doc.getById( 'fileArea' );

					fileArea.setStyle( 'height', '100%' );
					fileArea.setHtml( '<textarea id="fileContent" style="height:' + cssHeight + 'px; width:' + cssWidth + 'px"></textarea>' );

					var fileContent = doc.getById( 'fileContent' );
					if ( CKFinder.env.chrome || CKFinder.env.opera ) {
						fileContent.setHtml( CKFinder.tools.htmlEncode( data ) );
					} else {
						fileContent.setText( data );
					}

					codemirror = null;
					if ( enableCodeMirror )
					{
						CKFinder.scriptLoader.load( codeMirrorPath + 'lib/codemirror.js', function()
						{
							CKFinder.scriptLoader.load( codeMirrorParsers[ file.ext ], function()
							{
								codemirror = win.$.CodeMirror.fromTextArea( doc.getById( 'fileContent' ).$, { mode : codeMirrorModes[ file.ext ] || file.ext } );
								var fileArea = doc.getById( 'fileArea' );

								// TODO get rid of ugly buttons and provide something better
								var undoB = doc.createElement( 'button', { attributes: { 'label' : api.lang.common.undo, 'class' : 'fileeditor-button' } } );
								undoB.on( 'click', function()
								{
									codemirror.undo();
								});
								undoB.setHtml( api.lang.common.undo );
								undoB.appendTo( fileArea );

								var redoB = doc.createElement( 'button', { attributes: { 'label' : api.lang.common.redo, 'class' : 'fileeditor-button' } } );
								redoB.on( 'click', function()
								{
									codemirror.redo();
								});
								redoB.setHtml( api.lang.common.redo );
								redoB.appendTo( fileArea );
							}, this, false, doc.getHead(), doc );
						}, this, false, doc.getHead(), doc );
					}
				});
			},
			contents : [
				{
					id : 'tab1',
					label : '',
					title : '',
					expand : true,
					padding : 0,
					elements :
					[
						{
							type : 'html',
							id : 'htmlLoader',
							html : '' +
							'<style type="text/css">' +
							'#fileArea .CodeMirror {background:white;height: '+ cssHeight +'px;}' +
							'#fileArea .CodeMirror-scroll {height:' + cssHeight + 'px; width:' + cssWidth + 'px;margin-bottom:0;}' +
							'#fileArea .CodeMirror .cm-tab {white-space:pre;}' +
							'button.fileeditor-button {border: 1px solid #999;margin: 7px 7px 0 0;text-align: center;width: 60px;color: #222;padding: 3px 10px;}' +
							// override .cke-compatibility issues which resolves to cursor below edited content bug
							'#fileArea .CodeMirror * {font-family:monospace !important;white-space:pre !important;line-height: 1.2em;}' +
							// FF >= 12 has some scrolling issue
							( CKFinder.env.gecko && CKFinder.env.version >= 120000 ? '#fileArea .CodeMirror-scroll > div > div {position:absolute !important}' : '' ) +
							'</style>' +
							'<div id="fileArea"></div>'
						}
					]
				}
			],
			// TODO http://dev.fckeditor.net/ticket/4750
			buttons : [ saveButton, CKFinder.dialog.cancelButton ]
		};
	} );

	api.addFileContextMenuOption( { label : api.lang.Fileeditor.contextMenuName, command : 'fileEditor' } , function( api, file )
			{
				api.openDialog( 'fileEditor' );
			},
			function ( file )
			{
				var maxSize = 1024;

				if ( typeof ( CKFinder.config.fileeditorMaxSize ) != 'undefined' )
					maxSize = CKFinder.config.fileeditorMaxSize;

				// Disable for images, binary files, large files etc.
				if ( regexTextExt.test( file.ext ) && file.size <= maxSize )
					return file.folder.acl.fileDelete ? true : -1;

				return false;
			});
} );
