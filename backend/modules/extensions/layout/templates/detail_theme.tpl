{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblExtensions|ucfirst}: {$msgTheme|sprintf:{$name}}</h2>
</div>

{option:warnings}
	<div class="generalMessage infoMessage content">
		<ul class="pb0">
			{iteration:warnings}
				<li>{$warnings.message}</li>
			{/iteration:warnings}
		</ul>
	</div>
{/option:warnings}

{option:information}
	<table width="100%">
		<tr>
			<td id="leftColumn">
				{option:information.description}
					<div class="box">
						<div class="heading">
							<h3>{$lblDescription|ucfirst}</h3>
						</div>
						<div class="options">
							<p>{$information.description}</p>
						</div>
					</div>
				{/option:information.description}
				{option:dataGridTemplates}
					<div class="box">
						<div class="heading">
							<h3>{$lblTemplates|ucfirst}</h3>
						</div>
						<div class="dataGridHolder">
							{$dataGridTemplates}
						</div>
					</div>
				{/option:dataGridTemplates}
			</td>
			<td id="sidebar">
				{option:information.thumbnail}
					<div class="box">
						<div class="heading">
							<h3>{$lblImage|ucfirst}</h3>
						</div>
						<div class="options">
							<img src="/frontend/themes/{$name}/{$information.thumbnail}" alt="{$name}" />
						</div>
					</div>
				{/option:information.thumbnail}

				{option:information.version}
					<div class="box">
						<div class="heading">
							<h3>{$lblVersion|ucfirst}</h3>
						</div>
						<div class="options">
							<p>{$information.version}</p>
						</div>
					</div>
				{/option:information.version}

				{option:information.authors}
					<div class="box">
						<div class="heading">
							<h3>{$lblAuthors|ucfirst}</h3>
						</div>
						<div class="options">
							<ul>
								{iteration:information.authors}
									<li>
										{option:information.authors.url}
											<a href="{$information.authors.url}" title="{$information.authors.name}">
										{/option:information.authors.url}
										{$information.authors.name}
										{option:information.authors.url}
											</a>
										{/option:information.authors.url}
									</li>
								{/iteration:information.authors}
							</ul>
						</div>
					</div>
				{/option:information.authors}
			</td>
		</tr>
	</table>
{/option:information}

{option:showExtensionsInstallTheme}
<div class="fullwidthOptions">
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'install_theme'}&amp;theme={$name}" data-message-id="confirmInstall" class="askConfirmation button mainButton">
			<span>{$lblInstall|ucfirst}</span>
		</a>
	</div>
</div>
{/option:showExtensionsInstallTheme}

<div id="confirmInstall" title="{$lblInstall|ucfirst}?" style="display: none;">
	<p>
		{$msgConfirmThemeInstall|sprintf:{$name}}
	</p>
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}