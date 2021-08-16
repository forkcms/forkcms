{option:pagination}
  {* there is more than 1 page *}
  {option:pagination.multiple_pages}
  <nav>
    <ul class="pagination">
      <li class="page-item{option:!pagination.show_previous} disabled{/option:!pagination.show_previous}">
        <a href="{$pagination.previous_url}" class="page-link" aria-label="{$previousLabel|ucfirst}">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>

      {option:pagination.first}
        {iteration:pagination.first}
        <li class="page-item">
          <a href="{$pagination.first.url}" class="page-link" rel="nofollow" title="{$goToLabel|ucfirst} {$pagination.first.label}">
            {$pagination.first.label}
          </a>
        </li>
        {/iteration:pagination.first}
        <li class="page-item ellipsis">
          <span>&hellip;</span>
        </li>
      {/option:pagination.first}

      {iteration:pagination.pages}
        {option:pagination.pages.current}
        <li class="page-item active">
          <a href="{$pagination.pages.url}" class="page-link">{$pagination.pages.label}<span class="visually-hidden">({$lblPaginationCurrent})</span></a>
        </li>
        {/option:pagination.pages.current}
        {option:!pagination.pages.current}
        <li class="page-item">
          <a href="{$pagination.pages.url}" class="page-link" rel="nofollow" title="{$goToLabel|ucfirst} {$pagination.pages.label}">
            {$pagination.pages.label}
          </a>
        </li>
        {/option:!pagination.pages.current}
      {/iteration:pagination.pages}

      {option:pagination.last}
        <li class="page-item ellipsis"><span>&hellip;</span></li>
        {iteration:pagination.last}
        <li class="page-item">
          <a href="{$pagination.last.url}" class="page-link" rel="nofollow" title="{$goToLabel|ucfirst} {$pagination.last.label}">
            {$pagination.last.label}
          </a>
        </li>
        {/iteration:pagination.last}
      {/option:pagination.last}

      <li class="page-item{option:!pagination.show_next} disabled{/option:!pagination.show_next}">
        <a href="{$pagination.next_url}" class="page-link" aria-label="{$nextLabel|ucfirst}">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    </ul>
  </nav>
  {/option:pagination.multiple_pages}
{/option:pagination}
