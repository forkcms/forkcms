/**
 * Interaction for the blog module
 */
jsBackend.Blog =
{
    // init, something like a constructor
    init: function () {
        // variables
        $title = $('#title');

        jsBackend.Blog.controls.init();

        // do meta
        if ($title.length > 0) $title.doMeta();
    }
};

jsBackend.Blog.controls =
{
    currentCategory: null,

    // init, something like a constructor
    init: function () {
        // variables
        $saveAsDraft = $('#saveAsDraft');
        $addCategorySubmit = $('#addCategorySubmit');
        $addCategoryDialog = $('#addCategoryDialog');
        $categoryTitle = $('#categoryTitle');
        $categoryTitleError = $('#categoryTitleError');
        $categoryId = $('#categoryId');

        $saveAsDraft.on('click', function (e) {
            $('form').append('<input type="hidden" name="status" value="draft" />').submit();
        });

        if ($addCategorySubmit.length > 0 && $addCategoryDialog.length > 0) {
            $addCategorySubmit.click(function () {
                // hide errors
                $categoryTitleError.hide();

                $.ajax({
                    data: {
                        fork: {action: 'AddCategory'},
                        value: $('#categoryTitle').val()
                    },
                    success: function (json, textStatus) {
                        if (json.code != 200) {
                            // show error if needed
                            if (jsBackend.debug) alert(textStatus);

                            // show message
                            $categoryTitleError.show();
                        }
                        else {
                            // add and set selected
                            $categoryId.append('<option value="' + json.data.id + '">' + json.data.title + '</option>');

                            // reset value
                            jsBackend.Blog.controls.currentCategory = json.data.id;

                            // close dialog
                            $addCategoryDialog.modal('hide');
                        }
                    }
                });
            });

            $addCategoryDialog.on('hide.bs.modal', function () {
                $categoryId.val(jsBackend.Blog.controls.currentCategory);
            });

            // bind change
            $categoryId.on('change', function (e) {
                // new category?
                if ($(this).val() == 'new_category') {
                    // prevent default
                    e.preventDefault();

                    // open dialog
                    $addCategoryDialog.modal('show');
                }

                // reset current category
                else jsBackend.Blog.controls.currentCategory = $categoryId.val();
            });
        }

        jsBackend.Blog.controls.currentCategory = $categoryId.val();
    }
};

$(jsBackend.Blog.init);
