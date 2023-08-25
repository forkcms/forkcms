import { EventUtil } from '../../../../../../../Core/assets/js/Components/EventUtil'
import { StringUtil } from '../../../../../../../Core/assets/js/Components/StringUtil'

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
      slugSelector: '#slug',
      slugOverwriteSelector: '#slugOverwrite',
      canonicalUrlSelector: '#canonicalUrlSelector',
      canonicalUrlOverwriteSelector: '#canonicalUrlOverwriteSelector',
      generatedSlugSelector: '#generatedSlug',
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
      const $slugOverwrite = $(options.slugOverwriteSelector)

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

      $slugOverwrite.change((e) => {
        if (!$element.is(':checked')) generateSlug($element.val())
      })

      function generateSlug (slug) {
        if (options.pageId === '1') {
          $(options.slugSelector).val('')
          $(options.generatedSlugSelector).html('')

          return
        }

        // make the call
        $.ajax(
          {
            data: {
              module: 'frontend',
              action: 'generate_slug',
              slug,
              metaId: $(options.metaIdSelector).val(),
              baseFieldName: $(options.baseFieldSelector).val(),
              custom: $(options.customSelector).val(),
              className: $(options.classNameSelector).val(),
              methodName: $(options.methodNameSelector).val(),
              parameters: $(options.parametersSelector).val()
            },
            success: function (data) {
              slug = data.slug

              $(options.slugSelector).val(slug)
              $(options.generatedSlugSelector).html(slug)
            },
            error: function () {
              slug = decodeURI(StringUtil.urlise(slug))
              $(options.slugSelector).val(slug)
              $(options.generatedSlugSelector).html(slug)
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

        if (!$slugOverwrite.is(':checked')) {
          generateSlug(title)
        }
      }
    })
  }
}
