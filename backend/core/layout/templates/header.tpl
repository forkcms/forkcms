<div id="headerHolder">
	<h1><a href="/{option:SITE_MULTILANGUAGE}{$LANGUAGE}{/option:SITE_MULTILANGUAGE}" title="{$lblVisitWebsite|ucfirst}">{$SITE_TITLE}</a></h1>
	<table id="header">
		<tr>
			<td id="navigation">
				{$var|getmainnavigation}
			</td>
			<td id="user">
				<ul>
					{option:debug}
						<li>
							<div id="debugnotify">{$lblDebugMode|ucfirst}</div>
						</li>
					{/option:debug}

					{option:SITE_MULTILANGUAGE}
					{option:workingLanguages}
						<li>
							<label for="workingLanguage">{$msgNowEditing}:</label>
							<select id="workingLanguage">
								{iteration:workingLanguages}
									<option{option:workingLanguages.selected} selected="selected"{/option:workingLanguages.selected} value="{$workingLanguages.abbr}">{$workingLanguages.label|ucfirst}</option>
								{/iteration:workingLanguages}
							</select>
						</li>
					{/option:workingLanguages}
					{/option:SITE_MULTILANGUAGE}

					<li id="account">
						<a href="#ddAccount" id="openAccountDropdown" class="fakeDropdown">
							<span class="avatar av24 block">
								<img src="{$FRONTEND_FILES_URL}/backend_users/avatars/32x32/{$authenticatedUserAvatar}" width="24" height="24" alt="{$authenticatedUserNickname}" />
							</span>
							<span class="nickname">{$authenticatedUserNickname}</span>
							<span class="arrow">&#x25BC;</span>
						</a>
						<ul class="hidden" id="ddAccount">
							{option:authenticatedUserEditUrl}<li><a href="{$authenticatedUserEditUrl}">{$lblEditProfile|ucfirst}</a></li>{/option:authenticatedUserEditUrl}
							<li class="lastChild"><a href="{$var|geturl:'logout':'authentication'}">{$lblSignOut|ucfirst}</a></li>
						</ul>
					</li>
				</ul>
			</td>
		</tr>
	</table>
</div>
