CKEDITOR.dialog.add(
    'linkDialog',
    function (editor) {
        return {
            title: 'Add link',

            minWidth: 400,
            minHeight: 200,

            contents: [
                {
                    id: 'tab',
                    label: '',
                    elements: [
                        {
                            type: 'text',
                            id: 'displayText',
                            label: 'Display Text',
                            validate: CKEDITOR.dialog.validate.notEmpty('Display cannot be empty.')
                        },
                        {
                            type: 'text',
                            id: 'url',
                            label: 'URL',
                            validate: function () {
                                return CKEDITOR.dialog.validate.notEmpty('URL cannot be empty.') &&
                                    CKEDITOR.dialog.validate.regex('https?:\\/\\/(www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{2,256}\\.[a-z]{2,6}\\b([-a-zA-Z0-9@:%_\\+.~#?&//=]*)', 'URL is not valid.')
                            }
                        },
                        {
                            type: 'button',
                            id: 'browseServer',
                            label: 'Browse server'
                        }
                    ]
                }
            ],

            onOk: function () {
                var dialog = this;

                var anchor = editor.document.createElement('a');
                anchor.setAttribute('href', dialog.getValueOf('tab', 'url'));
                anchor.setText(dialog.getValueOf('tab', 'displayText'));

                editor.insertElement(anchor);
            }
        }
    }
)
