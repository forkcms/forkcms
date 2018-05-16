CKEDITOR.dialog.add(
    'linkDialog',
    function (editor) {
        var urlRegex = '(https?:\\/\\/(www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{2,256}(\\.[a-z]{2,6})?)?\\/([-a-zA-Z0-9@:%_\\+.~#?&//=]*)';

        return {
            title: editor.lang.medialibrary.addLink,

            minWidth: 400,
            minHeight: 200,

            contents: [
                {
                    id: 'tab',
                    label: '',
                    elements: [
                        {
                            type: 'select',
                            id: 'type',
                            label: editor.lang.medialibrary.type,
                            items: [
                                [
                                    editor.lang.medialibrary.url,
                                    'url'
                                ],
                                [
                                    editor.lang.medialibrary.email,
                                    'email'
                                ]
                            ],
                            default: 'url',
                            onChange: function (element) {
                                var $urlFields = this.getDialog().getContentElement('tab', 'url').getElement().getParent().getParent().getParent().getParent().getParent().getParent();
                                var $localPageField = this.getDialog().getContentElement('tab', 'localPage').getElement().getParent().getParent();
                                var $openInNewWindowField = this.getDialog().getContentElement('tab', 'openInNewWindow').getElement().getParent().getParent();
                                var $emailAddressField = this.getDialog().getContentElement('tab', 'email').getElement().getParent().getParent();
                                var $subjectField = this.getDialog().getContentElement('tab', 'subject').getElement().getParent().getParent();
                                var $contentField = this.getDialog().getContentElement('tab', 'content').getElement().getParent().getParent();

                                var value = this.getValue();

                                // email url
                                if (value === 'email') {
                                    $urlFields.hide();
                                    $localPageField.hide();
                                    $openInNewWindowField.hide();
                                    $emailAddressField.show();
                                    $subjectField.show();
                                    $contentField.show();

                                    return;
                                }

                                // normal url
                                $urlFields.show();
                                $localPageField.show();
                                $openInNewWindowField.show();
                                $emailAddressField.hide();
                                $subjectField.hide();
                                $contentField.hide();
                            },
                            setup: function (element) {
                                var type = 'url';
                                if (element.getAttribute('href').startsWith('mailto:')) {
                                    type = 'email';
                                }

                                this.getDialog().setValueOf('tab', 'type', type);
                            },
                            onLoad: function (element) {
                                var $urlFields = this.getDialog().getContentElement('tab', 'url').getElement().getParent().getParent().getParent().getParent().getParent().getParent();
                                var $localPageField = this.getDialog().getContentElement('tab', 'localPage').getElement().getParent().getParent();
                                var $openInNewWindowField = this.getDialog().getContentElement('tab', 'openInNewWindow').getElement().getParent().getParent();
                                var $emailAddressField = this.getDialog().getContentElement('tab', 'email').getElement().getParent().getParent();
                                var $subjectField = this.getDialog().getContentElement('tab', 'subject').getElement().getParent().getParent();
                                var $contentField = this.getDialog().getContentElement('tab', 'content').getElement().getParent().getParent();

                                $urlFields.show();
                                $localPageField.show();
                                $openInNewWindowField.show();
                                $emailAddressField.hide();
                                $subjectField.hide();
                                $contentField.hide();
                            },
                            onHide: function (element) {
                                var $urlFields = this.getDialog().getContentElement('tab', 'url').getElement().getParent().getParent().getParent().getParent().getParent().getParent();
                                var $localPageField = this.getDialog().getContentElement('tab', 'localPage').getElement().getParent().getParent();
                                var $openInNewWindowField = this.getDialog().getContentElement('tab', 'openInNewWindow').getElement().getParent().getParent();
                                var $emailAddressField = this.getDialog().getContentElement('tab', 'email').getElement().getParent().getParent();
                                var $subjectField = this.getDialog().getContentElement('tab', 'subject').getElement().getParent().getParent();
                                var $contentField = this.getDialog().getContentElement('tab', 'content').getElement().getParent().getParent();

                                $urlFields.show();
                                $localPageField.show();
                                $openInNewWindowField.show();
                                $emailAddressField.hide();
                                $subjectField.hide();
                                $contentField.hide();
                            }
                        },
                        {
                            type: 'text',
                            id: 'displayText',
                            label: editor.lang.medialibrary.displayText,
                            validate: CKEDITOR.dialog.validate.notEmpty(editor.lang.medialibrary.displayCannotBeEmpty),
                            setup: function (element) {
                                this.setValue(element.getText());
                            },
                            commit: function (element) {
                                element.setText(this.getValue());
                            }
                        },
                        {
                            type: 'hbox',
                            widths: ['65%', '5%', '30%'],
                            children: [
                                {
                                    type: 'text',
                                    id: 'url',
                                    label: 'URL',
                                    setup: function (element) {
                                        if (element.getAttribute('href').startsWith('mailto:')) {
                                            return;
                                        }

                                        this.setValue(element.getAttribute('href'));
                                        this.getDialog().setValueOf('tab', 'openInNewWindow', element.getAttribute('target') === '_blank');
                                    },
                                    commit: function (element) {
                                        if (this.getDialog().getValueOf('tab', 'type') === 'email') {
                                            return;
                                        }

                                        element.setAttribute('href', this.getValue());
                                        element.setAttribute('data-cke-saved-href', this.getValue());
                                        element.removeAttribute('target');
                                        if (this.getDialog().getValueOf('tab', 'openInNewWindow')) {
                                            element.setAttribute('target', '_blank');
                                        }
                                    }
                                },
                                {
                                    type: 'html',
                                    html: '<span style="display: block;padding: 27px 5px 0 5px;">or</span>'
                                },
                                {
                                    type: 'button',
                                    id: 'browseServer',
                                    label: editor.lang.medialibrary.browseServer,
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
                            type: 'select',
                            label: jsBackend.locale.msg('EditorSelectInternalPage'),
                            id: 'localPage',
                            title: jsBackend.locale.msg('EditorSelectInternalPage'),
                            items: linkList,
                            onChange: function (event) {
                                this.getDialog().getContentElement('tab', 'url').setValue(event.data.value);
                            }
                        },
                        {
                            type: 'checkbox',
                            id: 'openInNewWindow',
                            label: editor.lang.medialibrary.openInNewWindow
                        },
                        {
                            type: 'text',
                            id: 'email',
                            label: editor.lang.medialibrary.email,
                            setup: function (element) {
                                if (this.getDialog().getValueOf('tab', 'type') === 'url') {
                                    return;
                                }

                                var url = new URL(element.getAttribute('href'));

                                this.setValue(url.pathname);
                                if (url.searchParams.has('subject')) {
                                    this.getDialog().setValueOf('tab', 'subject', url.searchParams.get('subject'));
                                }
                                if (url.searchParams.has('body')) {
                                    this.getDialog().setValueOf('tab', 'content', url.searchParams.get('body'));
                                }
                            },
                            commit: function (element) {
                                if (this.getDialog().getValueOf('tab', 'type') === 'url') {
                                    return;
                                }

                                var url = new URL('mailto:' + this.getValue());
                                if (this.getDialog().getValueOf('tab', 'subject') !== '') {
                                    url.searchParams.append('subject', this.getDialog().getValueOf('tab', 'subject'));
                                }
                                if (this.getDialog().getValueOf('tab', 'content') !== '') {
                                    url.searchParams.append('body', this.getDialog().getValueOf('tab', 'content'));
                                }

                                element.setAttribute('href', url.toString());
                                element.setAttribute('data-cke-saved-href', url.toString());
                            }
                        },
                        {
                            type: 'text',
                            id: 'subject',
                            label: editor.lang.medialibrary.subject
                        },
                        {
                            type: 'textarea',
                            id: 'content',
                            label: editor.lang.medialibrary.content
                        }
                    ]
                }
            ],

            onOk: function () {
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

                dialog.insertMode = !element || element.getName() !== 'a';
                if (dialog.insertMode) {
                    element = editor.document.createElement('a');

                    dialog.setValueOf('tab', initialText.match(urlRegex) ? 'url' : 'displayText', initialText);
                }

                this.element = element;

                if (!this.insertMode) {
                    this.setupContent(element);
                }
            }
        };
    }
);
