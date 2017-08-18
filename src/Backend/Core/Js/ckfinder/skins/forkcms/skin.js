CKFinder.define(
    {
        config: function( config ) {
            // @TODO: We could probably move this to a SASS generated file
            config.themeCSS = 'skins/forkcms/skin.css';
            config.iconsCSS = 'skins/forkcms/icons.css';

            return config;
        },

        init: function( finder ) {
            CKFinder.require( [ 'jquery' ], function( jQuery ) {
                // Enforce black iconset.
                jQuery( 'body' ).addClass( 'ui-alt-icon' );
            } );
        }
    }
);
