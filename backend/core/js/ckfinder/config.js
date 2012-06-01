﻿/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckfinder.com/license
*/

CKFinder.customConfig = function( config )
{
	// configuration
	config.basePath = '/backend/core/js/ckfinder';

	// layout
	config.disableHelpButton = true;
	config.width = 800;
	config.skin = 'kama';
	config.uiColor = '#E7F0F8';

	// remove useless plugins
	config.removePlugins = 'basket,help';
};
