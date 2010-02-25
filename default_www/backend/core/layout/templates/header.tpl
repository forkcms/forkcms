{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
<body id="{$bodyID}" class="{$bodyClass}">
	<table border="0" cellspacing="0" cellpadding="0" id="encloser">
		<tr>
			<td>
				<div id="headerHolder">
					<h1><a href="/">{$SITE_TITLE} <span>Bezoek website &rarr;</span></a></h1>
					<table cellspacing="0" cellpadding="0" id="header">
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
									{option:workingLanguages}
									<li>
										{$lblWorkingLanguage|ucfirst}:
										<select id="workingLanguage">
											{iteration:workingLanguages}
												<option{option:workingLanguages.selected} selected="selected"{/option:workingLanguages.selected} value="{$workingLanguages.abbr}">{$workingLanguages.label|ucfirst}</option>
											{/iteration:workingLanguages}
										</select>
									</li>
									{/option:workingLanguages}
									<li class="settings">
										<a href="{$var|geturl:'index':'settings'}" class="icon iconSettings">
											{$lblSettings|ucfirst}
										</a>
									</li>
									<li>
										<table border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td>
													<div class="avatar av24">
														<div class="avOverlay">&nbsp;</div>
														<a href="{$authenticatedUserEditUrl}">
															<img src="{$FRONTEND_FILES_URL}/backend_users/avatars/32x32/{$authenticatedUserAvatar}" width="24" height="24" alt="{$authenticatedUserNickname}" />
														</a>
													</div>
												</td>
												<td><a class="user" href="{$authenticatedUserEditUrl}">{$authenticatedUserNickname}</a></td>
											</tr>
										</table>
									</li>
									<li>
										<a href="{$var|geturl:'logout':'authentication'}">{$lblSignOut|ucfirst}</a>
									</li>
								</ul>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td id="container">
				<div id="main">