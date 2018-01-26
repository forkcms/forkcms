CKEDITOR.plugins.add(
    'medialibrary',
    {
        lang: 'en',
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
                    label: editor.lang.medialibrary.insertLink,
                    toolbar: 'insert,0'
                }
            );

            editor.ui.addButton(
                'ForkImage',
                {
                    command: 'addImage',
                    label: editor.lang.medialibrary.insertImage,
                    toolbar: 'insert,0'
                }
            );

            editor.addMenuItem("image", {
              command: "addImage",
              group: "image",
              label: editor.lang.image.menu
            })
            editor.addMenuItem("link", {
              command: "addLink",
              group: "link",
              label: editor.lang.link.menu
            })
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
