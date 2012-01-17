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
