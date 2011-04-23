{include:{$PATH_WWW}/install/layout/templates/head.tpl}

<h2>Settings</h2>
{form:step3}
	{option:formError}<div class="formMessage errorMessage"><p>{$formError}</p></div>{/option:formError}
		<div>
			<h3>Languages</h3>
			<p>Will your site be available in multiple languages or just one? Changing this setting later on will change your URL structure.</p>

			<ul class="inputList">
				{iteration:language_type}
					<li>{$language_type.rbtLanguageType} <label for="{$language_type.id}">{$language_type.label}</label>
					{option:language_type.multiple}
						<ul id="languages" class="hidden inputList noPadding">
							{iteration:languages}
								<li>{$languages.chkLanguages} <label for="{$languages.id}">{$languages.label}</label></li>
							{/iteration:languages}
						</ul>
					{/option:language_type.multiple}
					</li>
					{option:language_type.single}
						<li id="languageSingle" class="hidden">
							{$ddmLanguage} {$ddmLanguageError}
						</li>
					{/option:language_type.single}
				{/iteration:language_type}
			</ul>
			<div id="defaultLanguageContainer">
				<p>What is the default language we should use for the website?</p>
				<p>{$ddmDefaultLanguage} {$ddmDefaultLanguageError}</p>
			</div>

			<h3>CMS interface languages</h3>
			<p>What languages do you plan to use in the CMS interface?</p>

			<ul class="inputList">
				<li>
					{$chkSameInterfaceLanguage} <label for="sameInterfaceLanguage">Use the same language(s) for the CMS interface.</label>
					<p id="interfaceLanguagesExplanation" class="hidden noPadding">Select the language(s) you would like to use use in the CMS interface.</p>
					<ul id="interfaceLanguages" class="hidden inputList noPadding">
						{iteration:interfaceLanguages}
							<li>{$interfaceLanguages.chkInterfaceLanguages} <label for="{$interfaceLanguages.id}">{$interfaceLanguages.label}</label></li>
						{/iteration:interfaceLanguages}
					</ul>
				</li>
			</ul>
			<div id="defaultInterfaceLanguageContainer">
				<p>What is the default language we should use for the CMS interface?</p>
				<p>{$ddmDefaultInterfaceLanguage} {$ddmDefaultInterfaceLanguageError}</p>
			</div>
		</div>
		<div class="fullwidthOptions">
			<div class="buttonHolder">
				<input id="installerButton" class="inputButton button mainButton" type="submit" name="installer" value="Next" />
			</div>
		</div>
{/form:step3}

{include:{$PATH_WWW}/install/layout/templates/foot.tpl}