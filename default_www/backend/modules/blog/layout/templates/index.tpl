{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}

		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblBlog|ucfirst} &gt; {$lblOverview|ucfirst}</p>
			</div>

			<div class="inner">
				{option:dgRecent}
					<div class="datagridHolder">
						<div class="tableHeading">
							<h3>{$lblRecentlyEdited|ucfirst}</h3>
							<div class="buttonHolderRight">
								<a href="{$var|geturl:'add'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">
									<span><span><span>{$lblAdd|ucfirst}</span></span></span>
								</a>
							</div>
						</div>
						{$dgRecent}
					</div>
				{/option:dgRecent}

				{option:dgDrafts}
					<div class="datagridHolder">
						<div class="tableHeading">
							<h3>{$lblDrafts|ucfirst}</h3>
						</div>
						{$dgDrafts}
					</div>
				{/option:dgDrafts}

				{option:dgPosts}
				<form action="{$var|geturl:'mass_post_action'}" method="get" class="forkForms submitWithLink" id="posts">
					<input type="hidden" name="from" value="posts" />
					<div class="datagridHolder">
						<div class="tableHeading">
							<h3>{$lblAllPosts|ucfirst}</h3>
						</div>
						{$dgPosts}
					</div>
				</form>
				{/option:dgPosts}

				{option:!dgPosts}
				<div class="datagridHolder">
					<div class="tableHeading">
						<h3>{$lblAllPosts|ucfirst}</h3>
						<div class="buttonHolderRight">
							<a class="button icon iconAdd" href="{$var|geturl:'add'}" title="{$lblAdd}"><span><span><span>{$lblAdd|ucfirst}</span></span></span></a>
						</div>
					</div>
					<table cellspacing="0" class="datagrid">
						<tbody>
							<tr>
								<td>
									{$msgNoItems}
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				{/option:!dgPosts}
			</div>

		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}