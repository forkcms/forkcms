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
        }
    }
);
