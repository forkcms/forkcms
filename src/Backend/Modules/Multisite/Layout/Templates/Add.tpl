{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblSite|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblSite|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<table width="100%">
				<tr>
					<td id="leftColumn">
						<div class="box">
							<div class="heading">
								<label for="domain"><h3>{$lblDomain|ucfirst}<abbr domain="{$lblRequiredField}">*</abbr></h3></label>
							</div>
							<div class="options">
								{$txtDomain} {$txtDomainError}
								<label for="isActive">{$chkIsActive} {$lblActive|ucfirst}</label>
								<label for="isViewable">{$chkIsViewable} {$lblViewable|ucfirst}</label>
							</div>
						</div>

						<div class="box">
							<div class="heading">
								<h3>{$lblLanguages|ucfirst}<abbr title="{$lblRequiredField|ucfirst">*</abbr></h3>
							</div>
							<div class="options">
							<table>
							{iteration:languages}
								<tr>
									<td><label for="{$languages.languageId}">{$languages.language} {$languages.languageLabel}</label></td>
									<td><label for="{$languages.activeId}">{$languages.active} {$languages.activeLabel}</label></td>
									<td><label for="{$languages.viewableId}">{$languages.viewable} {$languages.viewableLabel}</label></td>
								</tr>
							{/iteration:languages}
							</table>
							{$chkLanguagesError}
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
