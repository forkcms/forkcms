CKEDITOR.dialog.add(
    'imageDialog',
    function (editor) {
        var urlRegex = '(https?:\\/\\/(www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{2,256}(\\.[a-z]{2,6})?)?\\/([-a-zA-Z0-9@:%_\\+.~#?&//=]*)';

        return {
            title: 'Add image',

            minWidth: 400,
            minHeight: 200,

            contents: [
                {
                    id: 'tab',
                    label: '',
                    elements: [
                        {
                            type: 'hbox',
                            widths: ['65%', '35%'],
                            children: [
                                {
                                    type: 'text',
                                    id: 'url',
                                    label: 'URL',
                                    validate: CKEDITOR.dialog.validate.regex(new RegExp(urlRegex), 'URL is not valid.'),
                                    setup: function (element) {
                                        this.setValue(element.getAttribute('src'));
                                    },
                                    commit: function (element) {
                                        element.setAttribute('src', this.getValue());
                                        element.setAttribute('data-cke-saved-src', this.getValue());
                                    }
                                },
                                {
                                    type: 'button',
                                    id: 'browseServer',
                                    label: 'Browse server',
                                    onClick: function () {
                                        var editor = this.getDialog().getParentEditor();
                                        editor.popup(window.location.origin + jsData.MediaLibrary.browseAction, 800, 800);

                                        window.onmessage = function (event) {
                                            if (event.data) {
                                                this.setValueOf('tab', 'url', event.data);
                                            }
                                        }.bind(this.getDialog());
                                    },
                                    style: 'margin-top: 20px;'
                                }
                            ]
                        },
                        {
                            type: 'text',
                            id: 'alternativeText',
                            label: 'Alternative Text',
                            setup: function (element) {
                                this.setValue(element.getAttribute('alt'));
                            },
                            commit: function (element) {
                                element.setAttribute('alt', this.getValue());
                                element.setAttribute('data-cke-saved-alt', this.getValue());
                            }
                        }
                    ]
                }
            ],

            onOk: function () {
                var dialog = this,
                    image = dialog.element;

                dialog.commitContent(image);

                if (dialog.insertMode) {
                    editor.insertElement(image);
                }
            },

            onShow: function () {
                var dialog = this;

                var selection = editor.getSelection();
                var initialText = selection.getSelectedText();
                var element = selection.getStartElement();

                if (element) {
                    // First element should the img element
                    element = element.getChild(0);
                }

                dialog.insertMode = !element || element.getName() !== 'img';
                if (dialog.insertMode) {
                    element = editor.document.createElement('img');

                    dialog.setValueOf('tab', initialText.match(urlRegex) ? 'url' : 'alternativeText', initialText);
                }

                this.element = element;

                if (!this.insertMode) {
                    this.setupContent(element);
                }
            }
        };
    }
);
