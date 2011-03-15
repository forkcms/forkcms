{include:{$PATH_WWW}/install/layout/templates/head.tpl}

<h2>Settings</h2>
{form:step4}
	{option:formError}<div class="formMessage errorMessage"><p>{$formError}</p></div>{/option:formError}
		<div>
			<h3>Modules</h3>
			<p>Which modules would you like to install?</p>
			<ul id="moduleList" class="inputList">
				{iteration:modules}
					<li>{$modules.chkModules} <label for="{$modules.id}">{$modules.label}</label></li>
				{/iteration:modules}
			</ul>

			<h3>Languages</h3>
			<p>Will your site be available in multiple languages or just one? Changing this setting later on will change your URL structure.</p>

			<ul class="inputList">
				{iteration:languageType}
					<li>{$languageType.rbtLanguageType} <label for="{$languageType.id}">{$languageType.label}</label>
					{option:languageType.multiple}
						<ul id="languages" class="hidden inputList">
							{iteration:languages}
								<li>{$languages.chkLanguages} <label for="{$languages.id}">{$languages.label}</label></li>
							{/iteration:languages}
						</ul>
					{/option:languageType.multiple}
					</li>
					{option:languageType.single}
						<li id="languageSingle" class="hidden">
							{$ddmLanguage} {$ddmLanguageError}
						</li>
					{/option:languageType.single}
				{/iteration:languageType}
			</ul>
			<div id="defaultLanguageContainer">
				<p>What is the default language we should use for the website?</p>
				<p>{$ddmDefaultLanguage} {$ddmDefaultLanguageError}</p>
			</div>
			<h3>Example data</h3>
			<p>If you are new to Fork CMS, you might prefer to have an example website with a default theme set up.</p>
			<ul class="inputList">
				<li>
					{$chkExampleData} <label for="exampleData">Install example data </label>
					<span class="helpTxt">(The blog-module is required and will be installed)</span>
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