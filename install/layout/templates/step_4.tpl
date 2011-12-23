{include:{$PATH_WWW}/install/layout/templates/head.tpl}

<h2>Settings</h2>
{form:step4}
	{option:formError}<div class="formMessage errorMessage"><p>{$formError}</p></div>{/option:formError}
		<div>
			<h3 class="noPadding">Modules</h3>
			<p>Which modules would you like to install?</p>
			<ul id="moduleList" class="inputList noPadding">
				{iteration:modules}
					<li>{$modules.chkModules} <label for="{$modules.id}">{$modules.label}</label></li>
				{/iteration:modules}
			</ul>

			<h3>Example data</h3>
			<p>If you are new to Fork CMS, you might prefer to have an example website with a default theme set up.</p>
			<ul class="inputList noPadding">
				<li>
					{$chkExampleData} <label for="exampleData">Install example data </label>
					<span class="helpTxt">(The blog-module is required and will be installed)</span>
				</li>
			</ul>

			<h3>Debug mode</h3>
			<p>Warning: debug mode is only useful when developing on Fork CMS.</p>
			<ul class="inputList noPadding">
				<li>
					{$chkDebugMode} <label for="debugMode">Enable debug mode </label>
					<span class="helpTxt">(Leave this checkbox unticked for better security and performance)</span>
				</li>
			</ul>
		</div>
		<div class="fullwidthOptions">
			<div class="buttonHolder">
				<a href="index.php?step=3" class="button">Previous</a>
				<input id="installerButton" class="inputButton button mainButton" type="submit" name="installer" value="Next" />
			</div>
		</div>
{/form:step4}

{include:{$PATH_WWW}/install/layout/templates/foot.tpl}