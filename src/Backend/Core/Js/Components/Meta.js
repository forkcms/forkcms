import { EventUtil } from './EventUtil'
import { StringUtil } from './StringUtil'

export class Meta {
  static doMeta (options, element) {
    // define defaults
    const defaults = {
      pageId: null,
      metaIdSelector: '#metaId',
      pageTitleSelector: '#pageTitle',
      pageTitleOverwriteSelector: '#pageTitleOverwrite',
      navigationTitleSelector: '#navigationTitle',
      navigationTitleOverwriteSelector: '#navigationTitleOverwrite',
      metaDescriptionSelector: '#metaDescription',
      metaDescriptionOverwriteSelector: '#metaDescriptionOverwrite',
      metaKeywordsSelector: '#metaKeywords',
      metaKeywordsOverwriteSelector: '#metaKeywordsOverwrite',
      urlSelector: '#url',
      urlOverwriteSelector: '#urlOverwrite',
      generatedUrlSelector: '#generatedUrl',
      baseFieldSelector: '#baseFieldName',
      customSelector: '#custom',
      classNameSelector: '#className',
      methodNameSelector: '#methodName',
      parametersSelector: '#parameters'
    }

    // extend options
    options = $.extend(defaults, options)

    // loop all elements
    return $(element).each((index, el) => {
      // variables
      const $element = $(el)
      const $pageTitle = $(options.pageTitleSelector)
      const $pageTitleOverwrite = $(options.pageTitleOverwriteSelector)
      const $navigationTitle = $(options.navigationTitleSelector)
      const $navigationTitleOverwrite = $(options.navigationTitleOverwriteSelector)
      const $metaDescription = $(options.metaDescriptionSelector)
      const $metaDescriptionOverwrite = $(options.metaDescriptionOverwriteSelector)
      const $metaKeywords = $(options.metaKeywordsSelector)
      const $metaKeywordsOverwrite = $(options.metaKeywordsOverwriteSelector)
      const $urlOverwrite = $(options.urlOverwriteSelector)

      // bind keypress
      $element.bind('keyup input', EventUtil.debounce(calculateMeta, 400))

      // bind change on the checkboxes
      if ($pageTitle.length > 0 && $pageTitleOverwrite.length > 0) {
        $pageTitleOverwrite.change((e) => {
          if (!$element.is(':checked')) $pageTitle.val($element.val())
        })
      }

      if ($navigationTitle.length > 0 && $navigationTitleOverwrite.length > 0) {
        $navigationTitleOverwrite.change((e) => {
          if (!$element.is(':checked')) $navigationTitle.val($element.val())
        })
      }

      $metaDescriptionOverwrite.change((e) => {
        if (!$element.is(':checked')) $metaDescription.val($element.val())
      })

      $metaKeywordsOverwrite.change((e) => {
        if (!$element.is(':checked')) $metaKeywords.val($element.val())
      })

      $urlOverwrite.change((e) => {
        if (!$element.is(':checked')) generateUrl($element.val())
      })

      // generate url
      function generateUrl (url) {
        if (options.pageId === '1') {
          $(options.urlSelector).val('')
          $(options.generatedUrlSelector).html('')

          return
        }

        // make the call
        $.ajax(
          {
            data: {
              fork: {module: 'Core', action: 'GenerateUrl'},
              url: url,
              metaId: $(options.metaIdSelector).val(),
              baseFieldName: $(options.baseFieldSelector).val(),
              custom: $(options.customSelector).val(),
              className: $(options.classNameSelector).val(),
              methodName: $(options.methodNameSelector).val(),
              parameters: $(options.parametersSelector).val()
            },
            success: function (data, textStatus) {
              url = data.data

              $(options.urlSelector).val(url)
              $(options.generatedUrlSelector).html(url)
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
              url = decodeURI(StringUtil.urlise(url))
              $(options.urlSelector).val(url)
              $(options.generatedUrlSelector).html(url)
            }
          })
      }

      // calculate meta
      function calculateMeta (e, element) {
        const title = (typeof element !== 'undefined') ? element.val() : $(this).val()

        if ($pageTitle.length > 0 && $pageTitleOverwrite.length > 0) {
          if (!$pageTitleOverwrite.is(':checked')) $pageTitle.val(title)
        }

        if ($navigationTitle.length > 0 && $navigationTitleOverwrite.length > 0) {
          if (!$navigationTitleOverwrite.is(':checked')) $navigationTitle.val(title)
        }

        if (!$metaDescriptionOverwrite.is(':checked')) $metaDescription.val(title)

        if (!$metaKeywordsOverwrite.is(':checked')) $metaKeywords.val(title)

        if (!$urlOverwrite.is(':checked')) {
          generateUrl(title)
        }
      }
    })
  }
}
