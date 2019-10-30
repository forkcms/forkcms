CKEDITOR.dialog.add(
    'imageDialog',
    function (editor) {
        var urlRegex = '(https?:\\/\\/(www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{2,256}(\\.[a-z]{2,6})?)?\\/([-a-zA-Z0-9@:%_\\+.~#?&//=]*)';

        return {
            title: editor.lang.medialibrary.addImage,

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
                                    label: editor.lang.medialibrary.url,
                                    validate: CKEDITOR.dialog.validate.regex(new RegExp(urlRegex), editor.lang.medialibrary.urlNotValid),
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
                                    label: editor.lang.medialibrary.browseServer,
                                    onClick: function () {
                                        var editor = this.getDialog().getParentEditor();
                                        editor.popup(window.location.origin + jsData.MediaLibrary.browseActionImages, 800, 800);

                                        window.onmessage = function (event) {
                                            if (event.data && typeof event.data === 'object' && 'media-url' in event.data) {
                                                this.setValueOf('tab', 'url', event.data['media-url']);
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
                            label: editor.lang.medialibrary.alternativeText,
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
