import { EventUtil } from './EventUtil'
import { StringUtil } from './StringUtil'

export class Meta {
  static doMeta (options, element) {
    // define defaults
    const defaults = {
      pageId: null,
      metaIdSelector: '#metaId',
      pageTitleSelector: 'page_title',
      pageNavigationTitleSelector: 'page_navigation_title',
      metaDescriptionSelector: 'meta_description',
      metaKeywordsSelector: 'meta_keywords',
      urlSelector: '#url',
      urlOverwriteSelector: '#urlOverwrite',
      canonicalUrlSelector: '#canonicalUrlSelector',
      canonicalUrlOverwriteSelector: '#canonicalUrlOverwriteSelector',
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
      const $pageTitle = options.pageTitleSelector
      const $navigationTitle = options.pageNavigationTitleSelector
      const $metaDescription = options.metaDescriptionSelector
      const $metaKeywords = options.metaKeywordsSelector
      const $urlOverwrite = $(options.urlOverwriteSelector)

      // bind keypress
      $element.bind('keyup input update-value', EventUtil.debounce(calculateMeta, 400))

      // pass vue emited event to jquery
      window.backend.seoVue.$on('update-value', () => {
        $element.trigger('update-value')
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
        const seoVueRefs = window.backend.seoVue.$refs

        if (seoVueRefs[$pageTitle] !== 'undefined') {
          seoVueRefs[$pageTitle].updateValue(title)
        }

        if (seoVueRefs[$navigationTitle] !== 'undefined') {
          seoVueRefs[$navigationTitle].updateValue(title)
        }

        if (seoVueRefs[$metaDescription] !== 'undefined') {
          seoVueRefs[$metaDescription].updateValue(title)
        }

        if (seoVueRefs[$metaKeywords] !== 'undefined') {
          seoVueRefs[$metaKeywords].updateValue(title)
        }

        if (!$urlOverwrite.is(':checked')) {
          generateUrl(title)
        }
      }
    })
  }
}
