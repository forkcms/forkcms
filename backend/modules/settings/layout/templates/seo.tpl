{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblSEOSettings|ucfirst}</h2>
</div>

{form:settingsSeo}
	<div class="box">
		<div class="heading">
			<h3>{$lblSEO|ucfirst}</h3>
		</div>
		<div class="options">
			<ul class="inputList">
				<li>
					<label for="seoNoodp">{$chkSeoNoodp} NOODP</label>
					<span class="helpTxt">{$msgHelpSEONoodp}</span>
				</li>
				<li>
					<label for="seoNoydir">{$chkSeoNoydir} NOYDIR</label>
					<span class="helpTxt">{$msgHelpSEONoydir}</span>
				</li>
				<li>
					<label for="seoNofollowInComments">{$chkSeoNofollowInComments} {$msgSEONoFollowInComments}</label>
				</li>
			</ul>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settingsSeo}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}