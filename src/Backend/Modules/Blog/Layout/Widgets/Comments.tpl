<div id="widgetBlogComments" class="panel panel-primary">
  <div class="panel-heading">
    <h2 class="panel-title">
      <a href="{$var|geturl:'comments':'blog'}">{$lblBlog|ucfirst}: {$lblLatestComments|ucfirst}</a>
    </h2>
  </div>
  {option:blogNumCommentsToModerate}
  <div class="panel-body">
    <div class="pull-left">
      {$msgCommentsToModerate|sprintf:{$blogNumCommentsToModerate}}
    </div>
    <div class="pull-right">
      <a href="{$var|geturl:'comments':'blog'}#tabModeration"><span>{$lblModerate|ucfirst}</span></a>
    </div>
  </div>
  {/option:blogNumCommentsToModerate}
  {option:blogComments}
  <table class="table table-striped dataGrid">
    <tbody>
    {iteration:blogComments}
      <tr class="{cycle:'odd':'even'}">
        <td><a href="{$blogComments.full_url}">{$blogComments.title}</a></td>
        <td class="name">{$blogComments.author}</td>
      </tr>
    {/iteration:blogComments}
    </tbody>
  </table>
  {/option:blogComments}
  {option:!blogComments}
  <div class="panel-body">
    <p>{$msgNoPublishedComments}</p>
  </div>
  {/option:!blogComments}
  <div class="panel-footer">
    <div class="btn-toolbar">
      <div class="btn-group">
        <a href="{$var|geturl:'comments':'blog'}" class="btn">{$lblAllComments|ucfirst}</a>
      </div>
    </div>
  </div>
</div>
