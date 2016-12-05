/*
Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
For licensing, see license.txt or http://cksource.com/ckfinder/license
*/

CKFinder.customConfig = function( config )
{
	// Define changes to default configuration here.
	// For the list of available options, check:
	// http://docs.cksource.com/ckfinder_2.x_api/symbols/CKFinder.config.html

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
