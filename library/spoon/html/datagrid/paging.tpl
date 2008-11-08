{* previous page *}
{option:oPreviousURL}<a href="{$previous.url}" title="previous page">{/option:oPreviousURL}
	&laquo; previous 
{option:oPreviousURL}</a>{/option:oPreviousURL}

{* list of pages *}
{iteration:iPages}
	{option:oPage}
		{option:oCurrentPage}<strong>{$iPage}</strong>{/option:oCurrentPage}
		{option:oOtherPage}<a href="{$url}">{$iPage}</a>{/option:oOtherPage}
	{/option:oPage}
	
	{option:oHellip}&hellip;{/option:oHellip}
{/iteration:iPages}

{* next page *}
{option:oNextURL}<a href="{$next.url}" title="next page">{/option:oNextURL}
	 next &raquo; 
{option:oNextURL}</a>{/option:oNextURL}