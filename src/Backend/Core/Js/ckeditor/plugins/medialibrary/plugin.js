CKEDITOR.plugins.add(
    'medialibrary',
    {
        icons: 'medialibrary',
        init: function (editor) {
            editor.addCommand(
                'addLink',
                new CKEDITOR.dialogCommand('linkDialog')
            )

            editor.ui.addButton(
                'ForkLink',
                {
                    command: 'addLink',
                    icon: this.path + 'icons/link.png',
                    label: 'Insert Link',
                    toolbar: 'insert,0'
                }
            )

            CKEDITOR.dialog.add('linkDialog', this.path + 'dialogs/linkDialog.js');

            // Disable default link dialog behaviour
            editor.on('doubleclick', function (event) {
                var element = event.data.element
                if (element.is('a')) {
                    event.data.dialog = 'linkDialog'
                }
            }, null, null, 100);
        }
    }
);
