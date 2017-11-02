CKEDITOR.plugins.add(
    'medialibrary',
    {
        icons: 'medialibrary',
        init: function (editor) {
            editor.addCommand(
                'addLink',
                new CKEDITOR.dialogCommand('linkDialog')
            );

            editor.addCommand(
                'addImage',
                new CKEDITOR.dialogCommand('imageDialog')
            );

            editor.ui.addButton(
                'ForkLink',
                {
                    command: 'addLink',
                    label: 'Insert Link',
                    toolbar: 'insert,0'
                }
            );

            editor.ui.addButton(
                'ForkImage',
                {
                    command: 'addImage',
                    label: 'Insert Image',
                    toolbar: 'insert,0'
                }
            );

            CKEDITOR.dialog.add('linkDialog', this.path + 'dialogs/linkDialog.js');
            CKEDITOR.dialog.add('imageDialog', this.path + 'dialogs/imageDialog.js');

            // Disable default link dialog behaviour
            editor.on('doubleclick', function (event) {
                var element = event.data.element;
                if (element.is('a')) {
                    event.data.dialog = 'linkDialog'
                }
                if (element.is('img')) {
                    event.data.dialog = 'imageDialog'
                }
            }, null, null, 100);
        }
    }
);
