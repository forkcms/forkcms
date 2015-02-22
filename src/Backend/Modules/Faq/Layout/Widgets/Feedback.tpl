<div id="widgetFaqFeedback" class="panel panel-primary">
  <div class="panel-heading">
    <h2 class="panel-title">
      <a href="{$var|geturl:'index':'faq'}">{$lblFaq|ucfirst}: {$lblFeedback|ucfirst}</a>
    </h2>
  </div>
  {option:faqFeedback}
  <table class="table table-striped dataGrid">
    <tbody>
      {iteration:faqFeedback}
      <tr class="{cycle:'odd':'even'}">
        <td><a href="{$faqFeedback.full_url}">{$faqFeedback.text|truncate:150}</a></td>
      </tr>
      {/iteration:faqFeedback}
    </tbody>
  </table>
  {/option:faqFeedback}
  {option:!faqFeedback}
  <div class="panel-body">
    <p>{$msgNoFeedback}</p>
  </div>
  {/option:!faqFeedback}
  <div class="panel-footer">
    <div class="btn-toolbar">
      <div class="btn-group">
        <a href="{$var|geturl:'index':'faq'}" class="btn"><span>{$lblAllQuestions|ucfirst}</span></a>
      </div>
    </div>
  </div>
</div>
