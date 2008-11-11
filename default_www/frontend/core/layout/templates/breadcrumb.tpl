			<div id="breadcrumb">
				<span>{$lblYouAreHere|ucfirst}:</span>
				{iteration:iBreadcrumb}
					{option:oSeparator} - {/option:oSeparator}
					{option:oHasUrl}<a href="{$url}" title="{$title}">{/option:oHasUrl}
						{$title}
					{option:oHasUrl}</a>{/option:oHasUrl}
				{/iteration:iBreadcrumb}
			</div>
