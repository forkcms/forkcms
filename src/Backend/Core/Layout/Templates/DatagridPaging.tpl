{option:pagination}
{* there is more than 1 page *}
{option:pagination.multiple_pages}
<nav>
  <ul class="pagination">
    <li {option:!pagination.show_previous} class="disabled" {/option:!pagination.show_previous}>
    {option:!pagination.show_previous}
      <span>
      {/option:!pagination.show_previous}
      {option:pagination.show_previous}
      <a href="{$pagination.previous_url}" aria-label="{$previousLabel|ucfirst}">
      {/option:pagination.show_previous}
        <span aria-hidden="true">&laquo;</span>
      {option:pagination.show_previous}
      </a>
      {/option:pagination.show_previous}
      {option:!pagination.show_previous}
      </span>
    {/option:!pagination.show_previous}
    </li>
    {option:pagination.first}
    {iteration:pagination.first}
    <li>
      <a href="{$pagination.first.url}" rel="nofollow" title="{$goToLabel|ucfirst} {$pagination.first.label}">
        {$pagination.first.label}
      </a>
    </li>
    {/iteration:pagination.first}
    <li class="ellipsis">
      <span>&hellip;</span>
    </li>
    {/option:pagination.first}
    {iteration:pagination.pages}
    {option:pagination.pages.current}
    <li class="active">
      <span>{$pagination.pages.label}<span class="sr-only">({$lblPaginationCurrent})</span></span>
    </li>
    {/option:pagination.pages.current}
    {option:!pagination.pages.current}
    <li>
      <a href="{$pagination.pages.url}" rel="nofollow" title="{$goToLabel|ucfirst} {$pagination.pages.label}">
        {$pagination.pages.label}
      </a>
    </li>
    {/option:!pagination.pages.current}
    {/iteration:pagination.pages}
    {option:pagination.last}
    <li class="ellipsis"><span>&hellip;</span></li>
    {iteration:pagination.last}
    <li>
      <a href="{$pagination.last.url}" rel="nofollow" title="{$goToLabel|ucfirst} {$pagination.last.label}">
        {$pagination.last.label}
      </a>
    </li>
    {/iteration:pagination.last}
    {/option:pagination.last}
    <li {option:!pagination.show_next} class="disabled" {/option:!pagination.show_next}>
    {option:!pagination.show_next}
      <span>
      {/option:!pagination.show_next}
      {option:pagination.show_next}
      <a href="{$pagination.next_url}" aria-label="{$nextLabel|ucfirst}">
      {/option:pagination.show_next}
        <span aria-hidden="true">&raquo;</span>
      {option:pagination.show_next}
      </a>
      {/option:pagination.show_next}
      {option:!pagination.show_next}
      </span>
    {/option:!pagination.show_next}
    </li>
  </ul>
</nav>
{/option:pagination.multiple_pages}
{/option:pagination}
