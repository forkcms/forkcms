{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblExtensions|ucfirst}: {$msgModule|sprintf:{$name}}</h2>
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

				{option:dataGridCronjobs}
					<div class="box">
						<div class="heading">
							<h3>{$lblCronjobs|ucfirst}</h3>
						</div>
						<div class="options">
							{$msgCronjobsInfo|sprintf:{$PATH_WWW}:{$SITE_URL}}
						</div>
						<div class="dataGridHolder">
							{$dataGridCronjobs}
						</div>
					</div>
				{/option:dataGridCronjobs}

				{option:dataGridEvents}
					<div class="box">
						<div class="heading">
							<h3>{$lblEvents|ucfirst}</h3>
						</div>
						<div class="dataGridHolder">
							{$dataGridEvents}
						</div>
					</div>
				{/option:dataGridEvents}
			</td>
			<td id="sidebar">
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

{option:showExtensionsInstallModule}
<div class="fullwidthOptions">
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'install_module'}&amp;module={$name}" data-message-id="confirmInstall" class="askConfirmation button mainButton">
			<span>{$lblInstall|ucfirst}</span>
		</a>
	</div>
</div>
{/option:showExtensionsInstallModule}

<div id="confirmInstall" title="{$lblInstall|ucfirst}?" style="display: none;">
	<p>
		{$msgConfirmModuleInstall|sprintf:{$name}}
	</p>
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}