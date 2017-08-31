CKEDITOR.dialog.add(
    'linkDialog',
    function (editor) {
        var urlRegex = 'https?:\\/\\/(www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{2,256}\\.[a-z]{2,6}\\b([-a-zA-Z0-9@:%_\\+.~#?&//=]*)';

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
                            validate: CKEDITOR.dialog.validate.notEmpty('Display cannot be empty.'),
                            setup: function(element) {
                                this.setValue(element.getText());
                            },
                            commit: function (element) {
                                element.setText(this.getValue());
                            }
                        },
                        {
                            type: 'text',
                            id: 'url',
                            label: 'URL',
                            validate: CKEDITOR.dialog.validate.regex(new RegExp(urlRegex), 'URL is not valid.'),
                            setup: function(element) {
                                this.setValue(element.getAttribute('href'));
                            },
                            commit: function (element) {
                                element.setAttribute('href', this.getValue());
                            }
                        },
                        {
                            type: 'button',
                            id: 'browseServer',
                            label: 'Browse server',
                            onClick: function () {
                                var editor = this.getDialog().getParentEditor();
                                editor.popup(window.location.origin + jsData.MediaLibrary.browseAction, 500, 500);
                            }
                        }
                    ]
                }
            ],

            onOk: function () {
                // var dialog = this;

                // var anchor = editor.document.createElement('a');
                // anchor.setAttribute('href', dialog.getValueOf('tab', 'url'));
                // anchor.setText(dialog.getValueOf('tab', 'displayText'));
                //
                // editor.insertElement(anchor);

                var dialog = this,
                    anchor = dialog.element;

                dialog.commitContent(anchor);

                if (dialog.insertMode) {
                    editor.insertElement(anchor);
                }
            },

            onShow: function () {
                var dialog = this;

                var selection = editor.getSelection();
                var initialText = selection.getSelectedText();
                var element = selection.getStartElement();

                if (element) {
                    element = element.getAscendant('a', true);
                }

                if (!element || element.getName() !== 'a') {
                    element = editor.document.createElement('a');

                    if (initialText.match(urlRegex)) {
                        dialog.setValueOf('tab', 'url', initialText);
                    } else {
                        dialog.setValueOf('tab', 'displayText', initialText);
                    }

                    this.insertMode = true;
                } else {
                    this.insertMode = false;
                }

                this.element = element;

                if (!this.insertMode) {
                    this.setupContent(element)
                }
            }
        }
    }
)
