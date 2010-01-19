			<div id="breadcrumb">
				<span>{$lblYouAreHere|ucfirst}:</span>
				{iteration:breadcrumb}
					{option:!last} • {/option:!last}
					{option:url}<a href="{$breadcrumb.url}" title="{$breadcrumb.title}">{/option:url}
						{$breadcrumb.title}
					{option:url}</a>{/option:url}
				{/iteration:breadcrumb}
			</div>
